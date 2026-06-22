<?php
/**
 * HMI IT Telkom - MANAJEMEN DOKUMEN
 */
$pageTitle = 'Manajemen Dokumen';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        flash('error', 'Sesi tidak valid.', 'error');
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'upload') {
            $judul = trim($_POST['judul'] ?? '');
            $kategori = trim($_POST['kategori'] ?? 'Umum');
            if (empty($judul) || !isset($_FILES['dokumen']) || $_FILES['dokumen']['error'] !== UPLOAD_ERR_OK) {
                flash('error', 'Judul dan file wajib diisi.', 'error');
            } else {
                $upload = uploadFile($_FILES['dokumen'], 'assets/uploads/dokumen', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'], 10485760);
                if ($upload['success']) {
                    $stmt = $pdo->prepare("INSERT INTO dokumen (judul,kategori,file_path,file_type,file_size) VALUES (?,?,?,?,?)");
                    $ext = pathinfo($_FILES['dokumen']['name'], PATHINFO_EXTENSION);
                    $stmt->execute([$judul, $kategori, $upload['path'], $ext, $_FILES['dokumen']['size']]);
                    flash('success', 'Dokumen berhasil diunggah.', 'success');
                } else {
                    flash('error', $upload['error'], 'error');
                }
            }
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM dokumen WHERE id=?")->execute([(int) $_POST['id']]);
            flash('success', 'Dokumen berhasil dihapus.', 'success');
        }
    }
    redirect(adminUrl('/manajemen_dokumen.php'));
}

$dokList = $pdo->query("SELECT * FROM dokumen ORDER BY uploaded_at DESC")->fetchAll();
?>

<div style="background:white;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <h3 style="margin-bottom:16px;">📤 Upload Dokumen Baru</h3>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="upload">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Judul Dokumen *</label>
                <input type="text" name="judul" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-input">
                    <option value="Umum">Umum</option>
                    <option value="Tata Tertib">Tata Tertib</option>
                    <option value="Hasil Rapat">Hasil Rapat</option>
                    <option value="Pedoman">Pedoman</option>
                    <option value="Multimedia">Multimedia</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">File *</label>
            <input type="file" name="dokumen" class="form-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required
                style="padding:10px;">
            <div style="font-size:0.8rem;color:#757575;margin-top:4px;">Format: PDF, DOC, XLS, PPT — Maks. 10 MB</div>
        </div>
        <button type="submit" class="btn btn-primary">📤 Upload</button>
    </form>
</div>

<div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="padding:16px 20px;border-bottom:1px solid #E0E0E0;"><strong>📁 Arsip Dokumen (
            <?= count($dokList) ?>)
        </strong></div>
    <?php foreach ($dokList as $d): ?>
        <div
            style="padding:14px 20px;border-bottom:1px solid #F5F5F5;display:flex;justify-content:space-between;align-items:center;">
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:1.3rem;">📄</span>
                <div>
                    <strong style="font-size:0.9rem;">
                        <?= sanitize($d['judul']) ?>
                    </strong>
                    <div style="font-size:0.8rem;color:#757575;">
                        <?= sanitize($d['kategori']) ?> ·
                        <?= strtoupper($d['file_type']) ?> ·
                        <?= round($d['file_size'] / 1024) ?> KB
                    </div>
                </div>
            </div>
            <form method="POST" style="display:inline;">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus dokumen ini?">🗑️</button>
            </form>
        </div>
    <?php endforeach; ?>
    <?php if (empty($dokList)): ?>
        <div style="padding:40px;text-align:center;color:#757575;">Belum ada dokumen.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
