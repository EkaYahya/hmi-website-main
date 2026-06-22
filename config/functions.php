<?php
/**
 * HMI IT Telkom - Utility Functions
 * Sanitasi, CSRF, auth helpers, file upload, dll.
 */

session_start();

require_once __DIR__ . '/database.php';

// ============================================================
// BASE PATH & URL HELPERS
// ============================================================

/**
 * Auto-detect base path:
 * - localhost/XAMPP → '/hmi'  
 * - Production hosting (hmiittelkom.com) → ''
 */
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']);
define('BASE_PATH', $isLocal ? '/hmi' : '');

/**
 * Generate a URL for public pages with clean paths.
 * Usage: url('/profil') → '/profil' (production) or '/hmi/public/profil.php' (local)
 *        url('/berita/detail?slug=xxx') 
 */
function url(string $path = '/'): string
{
    // Map clean routes to actual file paths for local dev
    $routeMap = [
        '/' => '/public/index.php',
        '/profil' => '/public/profil.php',
        '/sejarah' => '/public/sejarah.php',
        '/berita' => '/public/berita.php',
        '/event' => '/public/event.php',
        '/dokumen' => '/public/dokumen.php',
        '/hotline' => '/public/hotline.php',
        '/login' => '/public/login.php',
        '/daftar-kader' => '/public/daftar_kader.php',
        '/gagasan' => '/public/gagasan.php',
    ];

    // Parse query string if present
    $parts = explode('?', $path, 2);
    $cleanPath = $parts[0];
    $query = isset($parts[1]) ? '?' . $parts[1] : '';

    if (BASE_PATH === '') {
        // Production: use clean URLs
        return $cleanPath . $query;
    } else {
        // Local: map to actual file
        $filePath = $routeMap[$cleanPath] ?? $cleanPath;
        return BASE_PATH . $filePath . $query;
    }
}

/**
 * Generate asset URL 
 * Usage: asset('/assets/css/style.css')
 */
function asset(string $path): string
{
    return BASE_PATH . $path;
}

/**
 * Generate admin URL
 * Usage: adminUrl('/index.php')
 */
function adminUrl(string $path = '/'): string
{
    return BASE_PATH . '/admin' . $path;
}

/**
 * Generate LMS URL
 */
function lmsUrl(string $path = '/'): string
{
    return BASE_PATH . '/lms' . $path;
}

// ============================================================
// SANITASI & KEAMANAN
// ============================================================

/**
 * Sanitasi input untuk mencegah XSS
 */
function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token dan simpan di session
 */
function generateCSRF(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi CSRF token dari form submission
 */
function validateCSRF(string $token): bool
{
    if (!empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        unset($_SESSION['csrf_token']); // Single-use token
        return true;
    }
    return false;
}

/**
 * Render CSRF hidden input field
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . generateCSRF() . '">';
}

// ============================================================
// NAVIGASI & FLASH MESSAGES
// ============================================================

/**
 * Redirect ke URL tertentu
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Set flash message ke session
 */
function flash(string $key, string $message, string $type = 'info'): void
{
    $_SESSION['flash'][$key] = [
        'message' => $message,
        'type' => $type // info, success, error, warning
    ];
}

/**
 * Ambil dan hapus flash message
 */
function getFlash(string $key): ?array
{
    if (isset($_SESSION['flash'][$key])) {
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
    return null;
}

/**
 * Render flash message sebagai HTML alert
 */
function renderFlash(string $key): string
{
    $flash = getFlash($key);
    if (!$flash)
        return '';

    $colors = [
        'success' => 'bg-green-100 border-green-400 text-green-800',
        'error' => 'bg-red-100 border-red-400 text-red-800',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
        'info' => 'bg-blue-100 border-blue-400 text-blue-800',
    ];
    $cls = $colors[$flash['type']] ?? $colors['info'];

    return '<div class="' . $cls . ' border px-4 py-3 rounded-lg mb-4 flex items-center justify-between" role="alert">'
        . '<span>' . sanitize($flash['message']) . '</span>'
        . '<button onclick="this.parentElement.remove()" class="ml-4 font-bold">&times;</button>'
        . '</div>';
}

// ============================================================
// AUTENTIKASI & ROLE
// ============================================================

/**
 * Cek apakah user sudah login
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Cek apakah user adalah admin
 */
function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

/**
 * Cek apakah user adalah kader
 */
function isKader(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'kader';
}

/**
 * Guard: hanya admin yang boleh akses
 */
function requireAdmin(): void
{
    if (!isAdmin()) {
        flash('auth', 'Akses ditolak. Silakan login sebagai admin.', 'error');
        redirect(url('/login'));
    }
}

/**
 * Guard: hanya kader yang boleh akses
 */
function requireKader(): void
{
    if (!isKader()) {
        flash('auth', 'Silakan login sebagai kader untuk mengakses LMS.', 'error');
        redirect(url('/login'));
    }
}

/**
 * Guard: harus login (role apapun)
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        flash('auth', 'Silakan login terlebih dahulu.', 'error');
        redirect(url('/login'));
    }
}

/**
 * Ambil data user yang sedang login
 */
function currentUser(): ?array
{
    if (!isLoggedIn())
        return null;

    static $user = null;
    if ($user === null) {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT u.*, kp.nama_lengkap, kp.nim, kp.program_studi, kp.angkatan, kp.status_kaderisasi 
                                   FROM users u 
                                   LEFT JOIN kader_profiles kp ON u.id = kp.user_id 
                                   WHERE u.id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            $user = null;
        }
    }
    return $user;
}

// ============================================================
// FILE UPLOAD
// ============================================================

/**
 * Upload file dengan validasi keamanan
 * 
 * @param array  $file       $_FILES['field']
 * @param string $targetDir  Direktori tujuan (relatif dari root project)
 * @param array  $allowedExt Ekstensi yang diizinkan
 * @param int    $maxSize    Ukuran maksimal dalam bytes (default 10MB)
 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
 */
function uploadFile(array $file, string $targetDir, array $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'], int $maxSize = 10485760): array
{
    // Validasi upload error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'path' => null, 'error' => 'Upload gagal. Kode error: ' . $file['error']];
    }

    // Validasi ukuran
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1048576, 1);
        return ['success' => false, 'path' => null, 'error' => "Ukuran file melebihi batas maksimal ({$maxMB} MB)."];
    }

    // Validasi ekstensi
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return ['success' => false, 'path' => null, 'error' => 'Format file tidak diizinkan. Gunakan: ' . implode(', ', $allowedExt)];
    }

    // Buat direktori jika belum ada
    $fullDir = __DIR__ . '/../' . $targetDir;
    if (!is_dir($fullDir)) {
        mkdir($fullDir, 0755, true);
    }

    // Generate nama unik
    $newName = uniqid('hmi_') . '_' . time() . '.' . $ext;
    $fullPath = $fullDir . '/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return ['success' => true, 'path' => $targetDir . '/' . $newName, 'error' => null];
    }

    return ['success' => false, 'path' => null, 'error' => 'Gagal menyimpan file.'];
}

// ============================================================
// UTILITAS STRING & FORMAT
// ============================================================

/**
 * Generate slug dari string (URL-friendly)
 */
function generateSlug(string $string): string
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    // Pastikan unik di database
    $pdo = getDB();
    $baseSlug = $slug;
    $counter = 1;
    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM berita_events WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() == 0)
            break;
        $slug = $baseSlug . '-' . $counter++;
    }
    return $slug;
}

/**
 * Format waktu relatif (e.g., "2 jam lalu")
 */
function timeAgo(string $datetime): string
{
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0)
        return $diff->y . ' tahun lalu';
    if ($diff->m > 0)
        return $diff->m . ' bulan lalu';
    if ($diff->d > 0)
        return $diff->d . ' hari lalu';
    if ($diff->h > 0)
        return $diff->h . ' jam lalu';
    if ($diff->i > 0)
        return $diff->i . ' menit lalu';
    return 'Baru saja';
}

/**
 * Format tanggal ke bahasa Indonesia
 */
function formatTanggal(string $date): string
{
    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

/**
 * Truncate teks dengan batas karakter
 */
function truncateText(string $text, int $limit = 150): string
{
    $clean = strip_tags($text);
    if (strlen($clean) <= $limit)
        return $clean;
    return substr($clean, 0, $limit) . '...';
}

/**
 * Base URL helper
 */
function baseUrl(string $path = ''): string
{
    return BASE_PATH . '/' . ltrim($path, '/');
}

// ============================================================
// SITE SETTINGS (CMS Key-Value Store)
// ============================================================

/**
 * Get a site setting value by key
 */
function getSetting(string $key, ?string $default = null): ?string
{
    static $cache = [];
    if (isset($cache[$key]))
        return $cache[$key];

    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        $cache[$key] = $val !== false ? $val : $default;
        return $cache[$key];
    } catch (PDOException $e) {
        return $default;
    }
}

/**
 * Set / update a site setting value
 */
function setSetting(string $key, ?string $value): void
{
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->execute([$value, $key]);
}
