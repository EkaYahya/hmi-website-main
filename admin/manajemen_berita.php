<?php
/**
 * HMI IT Telkom - CMS BERITA
 */
$pageTitle = 'Manajemen Berita';
require_once __DIR__ . '/../includes/admin_header.php';

$pdo = getDB();

// Handle create/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        flash('error', 'Sesi tidak valid.', 'error');
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'create' || $action === 'edit') {
            $judul = trim($_POST['judul'] ?? '');
            $konten = $_POST['konten'] ?? '';
            $id = (int) ($_POST['id'] ?? 0);

            if (empty($judul) || empty($konten)) {
                flash('error', 'Judul dan konten wajib diisi.', 'error');
            } else {
                $imagePath = null;
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadFile($_FILES['gambar'], 'assets/uploads/berita', ['jpg', 'jpeg', 'png', 'webp'], 2097152);
                    if ($upload['success']) {
                        $imagePath = $upload['path'];
                    } else {
                        flash('error', $upload['error'], 'error');
                        redirect(adminUrl('/manajemen_berita.php'));
                    }
                }

                if ($action === 'create') {
                    $slug = generateSlug($judul);
                    $stmt = $pdo->prepare("INSERT INTO berita_events (tipe_post, judul, slug, konten_teks, path_gambar, author_id) VALUES ('berita', ?, ?, ?, ?, ?)");
                    $stmt->execute([$judul, $slug, $konten, $imagePath, $_SESSION['user_id']]);
                    flash('success', 'Berita berhasil dipublikasikan.', 'success');
                } else {
                    if ($imagePath) {
                        $stmt = $pdo->prepare("UPDATE berita_events SET judul=?, konten_teks=?, path_gambar=? WHERE id=?");
                        $stmt->execute([$judul, $konten, $imagePath, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE berita_events SET judul=?, konten_teks=? WHERE id=?");
                        $stmt->execute([$judul, $konten, $id]);
                    }
                    flash('success', 'Berita berhasil diperbarui.', 'success');
                }
            }
        } elseif ($action === 'delete') {
            $id = (int) $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM berita_events WHERE id=? AND tipe_post='berita'");
            $stmt->execute([$id]);
            flash('success', 'Berita berhasil dihapus.', 'success');
        }
    }
    redirect(adminUrl('/manajemen_berita.php'));
}

// Load data
$beritaList = $pdo->query("SELECT * FROM berita_events WHERE tipe_post='berita' ORDER BY created_at DESC")->fetchAll();
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM berita_events WHERE id=? AND tipe_post='berita'");
    $stmt->execute([(int) $_GET['edit']]);
    $editData = $stmt->fetch();
}
?>

<!-- Form -->
<div style="background:white;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <h3 style="margin-bottom:16px;">
        <?= $editData ? '✏️️ Edit Berita' : '➕ Tambah Berita Baru' ?>
    </h3>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'create' ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label class="form-label">Judul Berita *</label>
            <input type="text" name="judul" class="form-input" placeholder="Judul artikel"
                value="<?= sanitize($editData['judul'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Konten *</label>
            <textarea name="konten" class="form-input" rows="8"
                placeholder="Tulis konten berita (mendukung HTML dasar)..."
                required><?= $editData['konten_teks'] ?? '' ?></textarea>
            <div style="font-size:0.8rem;color:#757575;margin-top:4px;">Mendukung tag HTML: &lt;p&gt;, &lt;strong&gt;,
                &lt;em&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;h3&gt;</div>
        </div>

        <div class="form-group">
            <label class="form-label">Gambar
                <?= $editData ? '(kosongkan jika tidak ingin mengubah)' : '' ?>
            </label>
            <input type="file" name="gambar" class="form-input" accept=".jpg,.jpeg,.png,.webp" style="padding:10px;">
        </div>

        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary">
                <?= $editData ? '💾 Simpan Perubahan' : '📤 Publikasikan' ?>
            </button>
            <?php if ($editData): ?>
                <a href="<?= adminUrl('/manajemen_berita.php') ?>" class="btn" style="border:1px solid #E0E0E0;">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- List -->
<div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="padding:16px 20px;border-bottom:1px solid #E0E0E0;">
        <strong>📰 Daftar Berita (
            <?= count($beritaList) ?>)
        </strong>
    </div>

    <?php if (empty($beritaList)): ?>
        <div style="padding:40px;text-align:center;color:#757575;">Belum ada berita.</div>
    <?php else: ?>
        <?php foreach ($beritaList as $b): ?>
            <div
                style="padding:16px 20px;border-bottom:1px solid #F5F5F5;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <strong>
                        <?= sanitize($b['judul']) ?>
                    </strong>
                    <div style="font-size:0.8rem;color:#757575;">
                        <?= timeAgo($b['created_at']) ?> · /
                        <?= sanitize($b['slug']) ?>
                    </div>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="?edit=<?= $b['id'] ?>" class="btn btn-sm" style="border:1px solid #E0E0E0;background:white;">✏️️</a>
                    <form method="POST" style="display:inline;">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus berita ini?"
                            style="padding:6px 12px;">🗑️</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
