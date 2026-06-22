<?php
/**
 * HMI IT Telkom - ADMIN: Site Settings (CMS)
 */
require_once __DIR__ . '/../config/functions.php';
$pdo = getDB();

// Handle save (BEFORE any HTML output)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF($_POST['csrf_token'] ?? '')) {
    $settings = $_POST['settings'] ?? [];

    foreach ($settings as $key => $value) {
        setSetting($key, trim($value));
    }

    // Handle image uploads
    if (!empty($_FILES['images'])) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['images']['name'][$key],
                    'type' => $_FILES['images']['type'][$key],
                    'tmp_name' => $_FILES['images']['tmp_name'][$key],
                    'error' => $_FILES['images']['error'][$key],
                    'size' => $_FILES['images']['size'][$key],
                ];
                $upload = uploadFile($file, 'assets/uploads/settings', ['jpg', 'jpeg', 'png', 'webp', 'svg'], 5242880);
                if ($upload['success']) {
                    setSetting($key, $upload['path']);
                }
            }
        }
    }

    flash('success', 'Pengaturan berhasil disimpan.', 'success');
    redirect(adminUrl('/site_settings.php'));
}

// Now safe to output HTML
$pageTitle = 'Pengaturan Situs';
require_once __DIR__ . '/../includes/admin_header.php';

// Group settings by kategori
$allSettings = $pdo->query("SELECT * FROM site_settings ORDER BY kategori, id")->fetchAll();
$grouped = [];
foreach ($allSettings as $s) {
    $grouped[$s['kategori']][] = $s;
}

$kategoriLabels = [
    'umum' => '⚙️ Umum',
    'beranda' => '🏠 Beranda',
    'profil' => '📋 Profil',
    'sosmed' => '🌐 Sosial Media',
];
?>

<form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>

    <?php foreach ($grouped as $kat => $items): ?>
        <div
            style="background:white;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid #E0E0E0;">
                <?= $kategoriLabels[$kat] ?? ucfirst($kat) ?>
            </h3>

            <?php foreach ($items as $s): ?>
                <div class="form-group">
                    <label class="form-label">
                        <?= sanitize($s['label']) ?> <code
                            style="font-size:0.7rem;background:#F5F5F5;padding:2px 6px;border-radius:4px;"><?= $s['setting_key'] ?></code>
                    </label>

                    <?php if ($s['setting_type'] === 'text'): ?>
                        <input type="text" name="settings[<?= $s['setting_key'] ?>]" class="form-input"
                            value="<?= sanitize($s['setting_value'] ?? '') ?>">

                    <?php elseif ($s['setting_type'] === 'textarea'): ?>
                        <textarea name="settings[<?= $s['setting_key'] ?>]" class="form-input"
                            rows="3"><?= sanitize($s['setting_value'] ?? '') ?></textarea>

                    <?php elseif ($s['setting_type'] === 'url'): ?>
                        <input type="url" name="settings[<?= $s['setting_key'] ?>]" class="form-input"
                            value="<?= sanitize($s['setting_value'] ?? '') ?>" placeholder="https://...">

                    <?php elseif ($s['setting_type'] === 'image'): ?>
                        <div style="display:flex;align-items:center;gap:16px;">
                            <?php if ($s['setting_value']): ?>
                                 <img src="<?= asset($s['setting_value']) ?>"
                                    style="width:80px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #E0E0E0;">
                            <?php else: ?>
                                <div
                                    style="width:80px;height:60px;background:#F5F5F5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#BDBDBD;font-size:1.5rem;">
                                    📷</div>
                            <?php endif; ?>
                            <input type="file" name="images[<?= $s['setting_key'] ?>]" class="form-input"
                                accept=".jpg,.jpeg,.png,.webp,.svg" style="padding:10px;flex:1;">
                        </div>
                        <small style="color:#757575;">Format: JPG, PNG, WebP, SVG · Max: 5MB.
                            <?php if ($s['setting_value']): ?>Saat ini:
                                <?= $s['setting_value'] ?>
                            <?php endif; ?>
                        </small>

                    <?php elseif ($s['setting_type'] === 'color'): ?>
                        <input type="color" name="settings[<?= $s['setting_key'] ?>]"
                            value="<?= sanitize($s['setting_value'] ?? '#1B5E20') ?>"
                            style="height:40px;width:80px;cursor:pointer;border:none;">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary btn-lg">💾 Simpan Pengaturan</button>
</form>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
