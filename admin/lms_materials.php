<?php
/**
 * HMI IT Telkom - LMS ADMIN: Materials  
 */
$pageTitle = 'Materi LMS';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $judul = trim($_POST['judul'] ?? '');
        $moduleId = (int) $_POST['module_id'];
        $tipe = $_POST['tipe_konten'] ?? 'teks';
        $konten = $_POST['konten'] ?? '';
        $url = trim($_POST['url_konten'] ?? '');

        $filePath = null;
        if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($_FILES['file_materi'], 'assets/uploads/lms', ['pdf', 'ppt', 'pptx', 'doc', 'docx', 'mp4', 'webm'], 52428800);
            if ($upload['success'])
                $filePath = $upload['path'];
        }

        if ($judul && $moduleId) {
            $stmt = $pdo->prepare("INSERT INTO lms_materials (module_id,judul_materi,tipe_konten,konten_teks,url_konten,file_path) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$moduleId, $judul, $tipe, $konten ?: null, $url ?: null, $filePath]);
            flash('success', 'Materi berhasil ditambahkan.', 'success');
        }
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM lms_materials WHERE id=?")->execute([(int) $_POST['id']]);
        flash('success', 'Materi dihapus.', 'success');
    }
    redirect(adminUrl('/lms_materials.php'));
}

$modules = $pdo->query("SELECT * FROM lms_modules ORDER BY urutan")->fetchAll();
$materials = $pdo->query("SELECT m.*, lm.nama_modul FROM lms_materials m JOIN lms_modules lm ON m.module_id=lm.id ORDER BY lm.urutan, m.id")->fetchAll();
?>

<div style="background:white;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <h3 style="margin-bottom:16px;">➕ Tambah Materi Baru</h3>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="create">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Modul *</label>
                <select name="module_id" class="form-input" required>
                    <option value="">— Pilih Modul —</option>
                    <?php foreach ($modules as $m): ?>
                        <option value="<?= $m['id'] ?>">
                            <?= sanitize($m['nama_modul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Konten</label>
                <select name="tipe_konten" class="form-input">
                    <option value="teks">Teks/Artikel</option>
                    <option value="pdf">PDF</option>
                    <option value="video">Video</option>
                    <option value="slide">Slide</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Judul Materi *</label>
            <input type="text" name="judul" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label">Konten Teks</label>
            <textarea name="konten" class="form-input" rows="4"
                placeholder="Isi materi teks (opsional, bisa menggunakan HTML)"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">URL Konten</label>
                <input type="url" name="url_konten" class="form-input" placeholder="https://youtube.com/...">
            </div>
            <div class="form-group">
                <label class="form-label">Upload File</label>
                <input type="file" name="file_materi" class="form-input" style="padding:10px;"
                    accept=".pdf,.ppt,.pptx,.doc,.docx,.mp4,.webm">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">📤 Tambah Materi</button>
    </form>
</div>

<div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="padding:16px 20px;border-bottom:1px solid #E0E0E0;"><strong>📎 Daftar Materi (
            <?= count($materials) ?>)
        </strong></div>
    <?php $lastMod = '';
    foreach ($materials as $m): ?>
        <?php if ($m['nama_modul'] !== $lastMod):
            $lastMod = $m['nama_modul']; ?>
            <div style="padding:10px 20px;background:#F5F5F5;font-weight:600;font-size:0.85rem;color:#1B5E20;">📚
                <?= sanitize($m['nama_modul']) ?>
            </div>
        <?php endif; ?>
        <div
            style="padding:12px 20px 12px 36px;border-bottom:1px solid #FAFAFA;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <strong style="font-size:0.9rem;">
                    <?= sanitize($m['judul_materi']) ?>
                </strong>
                <span
                    style="font-size:0.75rem;background:#E3F2FD;color:#1565C0;padding:2px 8px;border-radius:50px;margin-left:8px;">
                    <?= $m['tipe_konten'] ?>
                </span>
            </div>
            <form method="POST"><input type="hidden" name="action" value="delete"><input type="hidden" name="id"
                    value="<?= $m['id'] ?>">
                <?= csrfField() ?><button class="btn btn-danger btn-sm" data-confirm="Hapus materi?"
                    style="padding:4px 10px;font-size:0.8rem;">🗑️</button>
            </form>
        </div>
    <?php endforeach; ?>
    <?php if (empty($materials)): ?>
        <div style="padding:40px;text-align:center;color:#757575;">Belum ada materi.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
