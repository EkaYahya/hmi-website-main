<?php
/**
 * HMI IT Telkom - LOGIN PAGE
 */
$pageTitle = 'Masuk';
require_once __DIR__ . '/../config/functions.php';

// Redirect if already logged in
if (isAdmin())
    redirect(adminUrl('/index.php'));
if (isKader())
    redirect(lmsUrl('/index.php'));

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!validateCSRF($_POST['csrf_token'] ?? '')) {
            $error = 'Sesi tidak valid. Silakan coba lagi.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Username dan password wajib diisi.';
            } else {
                $pdo = getDB();
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['username'] = $user['username'];

                    flash('success', 'Selamat datang, ' . sanitize($user['username']) . '!', 'success');

                    if ($user['role'] === 'admin') {
                        redirect(adminUrl('/index.php'));
                    } else {
                        redirect(lmsUrl('/index.php'));
                    }
                } else {
                    $error = 'Username atau password salah.';
                }
            }
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — HMI IT Telkom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">
</head>

<body>

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <div style="font-size:2.5rem;margin-bottom:12px;">🟢</div>
                <h1>Masuk ke Portal</h1>
                <p>HMI Komisariat IT Telkom</p>
            </div>

            <!-- Flash messages -->
            <?= renderFlash('auth') ?>
            <?= renderFlash('success') ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border-red-400 text-red-800 border px-4 py-3 rounded-lg mb-4">
                    <?= sanitize($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <?= csrfField() ?>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Masukkan username"
                        value="<?= sanitize($_POST['username'] ?? '') ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary"
                    style="width:100%;justify-content:center;border-radius:var(--radius-sm);padding:14px;">
                    Masuk →
                </button>
            </form>

            <div class="text-center mt-6" style="font-size:0.9rem;color:#757575;">
                Belum punya akun?
                <a href="<?= url('/daftar-kader') ?>" style="color:#1B5E20;font-weight:600;">Daftar sebagai Kader</a>
            </div>

            <div class="text-center mt-2">
                <a href="<?= url('/') ?>" style="font-size:0.85rem;color:#757575;">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

</body>

</html>
