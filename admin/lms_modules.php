<?php
/**
 * HMI IT Telkom - LMS ADMIN: Modules
 */
$pageTitle = 'Modul LMS';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    if ($action === 'edit') {
        $stmt = $pdo->prepare("UPDATE lms_modules SET nama_modul=?, deskripsi=?, passing_score=? WHERE id=?");
        $stmt->execute([
            trim($_POST['nama_modul']),
            trim($_POST['deskripsi']),
            (int) $_POST['passing_score'],
            (int) $_POST['id']
        ]);
        flash('success', 'Modul berhasil diperbarui.', 'success');
    }
    redirect(adminUrl('/lms_modules.php'));
}

$modules = $pdo->query("SELECT m.*, (SELECT COUNT(*) FROM lms_questions WHERE module_id=m.id) as total_soal, (SELECT COUNT(*) FROM lms_materials WHERE module_id=m.id) as total_materi FROM lms_modules m ORDER BY m.urutan")->fetchAll();
$editMod = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM lms_modules WHERE id=?");
    $stmt->execute([(int) $_GET['edit']]);
    $editMod = $stmt->fetch();
}
?>

<?php if ($editMod): ?>
    <div style="background:white;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom:16px;">📄️ Edit Modul:
            <?= sanitize($editMod['nama_modul']) ?>
        </h3>
        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $editMod['id'] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Modul</label>
                    <input type="text" name="nama_modul" class="form-input" value="<?= sanitize($editMod['nama_modul']) ?>"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label">Passing Score (%)</label>
                    <input type="number" name="passing_score" class="form-input" value="<?= $editMod['passing_score'] ?>"
                        min="0" max="100" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-input" rows="3"><?= sanitize($editMod['deskripsi']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="<?= adminUrl('/lms_modules.php') ?>" class="btn" style="border:1px solid #E0E0E0;margin-left:8px;">Batal</a>
        </form>
    </div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
    <?php $icons = ['📜', '⚖️', '💡', '👔', '🎯'];
    foreach ($modules as $i => $mod): ?>
        <div class="card" style="padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:start;">
                <span style="font-size:2rem;">
                    <?= $icons[$i] ?? '📖' ?>
                </span>
                <span style="font-size:0.8rem;color:#757575;">Urutan:
                    <?= $mod['urutan'] ?>
                </span>
            </div>
            <h3 style="font-size:1.1rem;margin:12px 0 8px;">
                <?= sanitize($mod['nama_modul']) ?>
            </h3>
            <p style="color:#757575;font-size:0.9rem;line-height:1.5;margin-bottom:16px;">
                <?= sanitize($mod['deskripsi']) ?>
            </p>
            <div style="display:flex;gap:12px;font-size:0.85rem;color:#616161;margin-bottom:12px;">
                <span>📎
                    <?= $mod['total_materi'] ?> Materi
                </span>
                <span>📃
                    <?= $mod['total_soal'] ?> Soal
                </span>
                <span>🎯 Lulus:
                    <?= $mod['passing_score'] ?>%
                </span>
            </div>
            <a href="?edit=<?= $mod['id'] ?>" class="btn btn-sm btn-primary">📄️ Edit</a>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
