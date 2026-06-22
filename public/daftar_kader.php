<?php
/**
 * HMI IT Telkom - PENDAFTARAN KADER BARU
 */
$pageTitle = 'Daftar Kader Baru';
require_once __DIR__ . '/../config/functions.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? adminUrl('/index.php') : lmsUrl('/index.php'));
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!validateCSRF($_POST['csrf_token'] ?? '')) {
            $errors[] = 'Sesi tidak valid. Silakan coba lagi.';
        } else {
            $old = $_POST;
            $namaLengkap = trim($_POST['nama_lengkap'] ?? '');
            $nim = trim($_POST['nim'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $programStudi = trim($_POST['program_studi'] ?? '');
            $angkatan = (int) ($_POST['angkatan'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validasi
            if (empty($namaLengkap))
                $errors[] = 'Nama lengkap wajib diisi.';
            if (empty($nim))
                $errors[] = 'NIM wajib diisi.';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = 'Email tidak valid.';
            if (empty($programStudi))
                $errors[] = 'Program studi wajib dipilih.';
            if ($angkatan < 2000 || $angkatan > (int) date('Y'))
                $errors[] = 'Angkatan tidak valid.';
            if (strlen($username) < 4)
                $errors[] = 'Username minimal 4 karakter.';
            if (strlen($password) < 6)
                $errors[] = 'Password minimal 6 karakter.';
            if ($password !== $confirmPassword)
                $errors[] = 'Konfirmasi password tidak cocok.';

            if (empty($errors)) {
                $pdo = getDB();

                // Check duplicates
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                if ($stmt->fetchColumn() > 0) {
                    $errors[] = 'Username atau email sudah digunakan.';
                }

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM kader_profiles WHERE nim = ?");
                $stmt->execute([$nim]);
                if ($stmt->fetchColumn() > 0) {
                    $errors[] = 'NIM sudah terdaftar.';
                }

                if (empty($errors)) {
                    $pdo->beginTransaction();

                    // Insert user
                    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, role) VALUES (?, ?, ?, 'kader')");
                    $stmt->execute([$username, password_hash($password, PASSWORD_BCRYPT), $email]);
                    $userId = $pdo->lastInsertId();

                    // Insert kader profile
                    $stmt = $pdo->prepare("INSERT INTO kader_profiles (user_id, nama_lengkap, nim, program_studi, angkatan) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$userId, $namaLengkap, $nim, $programStudi, $angkatan]);

                    $pdo->commit();

                    flash('success', 'Pendaftaran berhasil! Silakan login dengan akun Anda.', 'success');
                    redirect(url('/login'));
                }
            }
        }
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction())
            $pdo->rollBack();
        $errors[] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kader — HMI IT Telkom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">
</head>

<body>

    <div class="auth-page">
        <div class="auth-card wide">
            <div class="auth-header">
                <div style="font-size:2.5rem;margin-bottom:12px;">🎓</div>
                <h1>Daftar Kader Baru</h1>
                <p>Bergabunglah dengan HMI Komisariat IT Telkom</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border-red-400 text-red-800 border px-4 py-3 rounded-lg mb-4">
                    <ul style="list-style:disc;padding-left:20px;margin:0;">
                        <?php foreach ($errors as $err): ?>
                            <li>
                                <?= sanitize($err) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <?= csrfField() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" class="form-input" placeholder="Nama lengkap Anda"
                            value="<?= sanitize($old['nama_lengkap'] ?? '') ?>" required>
                        <div class="form-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIM *</label>
                        <input type="text" name="nim" class="form-input" placeholder="Contoh: 1301190001"
                            value="<?= sanitize($old['nim'] ?? '') ?>" required>
                        <div class="form-error"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-input" placeholder="email@telkomuniversity.ac.id"
                            value="<?= sanitize($old['email'] ?? '') ?>" required>
                        <div class="form-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Program Studi *</label>
                        <select name="program_studi" class="form-input" required>
                            <option value="">— Pilih Program Studi —</option>
                            <?php
                            $prodiList = ['Informatika', 'Teknik Telekomunikasi', 'Sistem Informasi', 'Teknik Elektro', 'Teknik Industri', 'Desain Komunikasi Visual', 'Teknik Komputer', 'Data Science', 'Rekayasa Perangkat Lunak', 'Teknik Fisika', 'Manajemen Bisnis Telekomunikasi'];
                            foreach ($prodiList as $p):
                                ?>
                                <option value="<?= $p ?>" <?= ($old['program_studi'] ?? '') === $p ? 'selected' : '' ?>>
                                    <?= $p ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-error"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Angkatan *</label>
                        <select name="angkatan" class="form-input" required>
                            <option value="">— Pilih Angkatan —</option>
                            <?php for ($y = (int) date('Y'); $y >= 2018; $y--): ?>
                                <option value="<?= $y ?>" <?= ($old['angkatan'] ?? '') == $y ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <div class="form-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-input" placeholder="Minimal 4 karakter"
                            value="<?= sanitize($old['username'] ?? '') ?>" required minlength="4">
                        <div class="form-error"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-input" placeholder="Minimal 6 karakter"
                            required minlength="6">
                        <div class="form-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password *</label>
                        <input type="password" name="confirm_password" class="form-input" placeholder="Ulangi password"
                            required>
                        <div class="form-error"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"
                    style="width:100%;justify-content:center;border-radius:var(--radius-sm);padding:14px;margin-top:8px;"
                    onclick="return HMI.validateForm('registerForm')">
                    Daftar Sekarang 🚀
                </button>
            </form>

            <div class="text-center mt-6" style="font-size:0.9rem;color:#757575;">
                Sudah punya akun? <a href="<?= url('/login') ?>" style="color:#1B5E20;font-weight:600;">Masuk di
                    sini</a>
            </div>

            <div class="text-center mt-2">
                <a href="<?= url('/') ?>" style="font-size:0.85rem;color:#757575;">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script src="<?= asset('/assets/js/main.js') ?>"></script>
</body>

</html>
