<?php
/**
 * HMI IT Telkom - CMS EVENT
 */
$pageTitle = 'Manajemen Event';
require_once __DIR__ . '/../includes/admin_header.php';

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        flash('error', 'Sesi tidak valid.', 'error');
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'create' || $action === 'edit') {
            $judul = trim($_POST['judul'] ?? '');
            $konten = $_POST['konten'] ?? '';
            $tanggal = $_POST['tanggal'] ?? null;
            $id = (int) ($_POST['id'] ?? 0);

            if (empty($judul) || empty($konten)) {
                flash('error', 'Judul dan konten wajib diisi.', 'error');
            } else {
                $imagePath = null;
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadFile($_FILES['gambar'], 'assets/uploads/events', ['jpg', 'jpeg', 'png', 'webp'], 2097152);
                    if ($upload['success'])
                        $imagePath = $upload['path'];
                }

                if ($action === 'create') {
                    $slug = generateSlug($judul);
                    $stmt = $pdo->prepare("INSERT INTO berita_events (tipe_post, judul, slug, konten_teks, tanggal_pelaksanaan, path_gambar, author_id) VALUES ('event', ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$judul, $slug, $konten, $tanggal ?: null, $imagePath, $_SESSION['user_id']]);
                    flash('success', 'Event berhasil ditambahkan.', 'success');
                } else {
                    $sql = "UPDATE berita_events SET judul=?, konten_teks=?, tanggal_pelaksanaan=?";
                    $params = [$judul, $konten, $tanggal ?: null];
                    if ($imagePath) {
                        $sql .= ", path_gambar=?";
                        $params[] = $imagePath;
                    }
                    $sql .= " WHERE id=?";
                    $params[] = $id;
                    $pdo->prepare($sql)->execute($params);
                    flash('success', 'Event berhasil diperbarui.', 'success');
                }
            }
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM berita_events WHERE id=? AND tipe_post='event'")->execute([(int) $_POST['id']]);
            flash('success', 'Event berhasil dihapus.', 'success');
        }
    }
    redirect(adminUrl('/manajemen_event.php'));
}

$eventList = $pdo->query("SELECT * FROM berita_events WHERE tipe_post='event' ORDER BY tanggal_pelaksanaan DESC")->fetchAll();
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM berita_events WHERE id=? AND tipe_post='event'");
    $stmt->execute([(int) $_GET['edit']]);
    $editData = $stmt->fetch();
}
?>

<div style="background:white;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <h3 style="margin-bottom:16px;">
        <?= $editData ? '�️ Edit Event' : '➕ Tambah Event Baru' ?>
    </h3>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'create' ?>">
        <?php if ($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Judul Event *</label>
                <input type="text" name="judul" class="form-input" value="<?= sanitize($editData['judul'] ?? '') ?>"
                    required>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" class="form-input"
                    value="<?= $editData['tanggal_pelaksanaan'] ?? '' ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi *</label>
            <textarea name="konten" class="form-input" rows="5"
                required><?= $editData['konten_teks'] ?? '' ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Gambar Poster</label>
            <input type="file" name="gambar" class="form-input" accept=".jpg,.jpeg,.png,.webp" style="padding:10px;">
        </div>
        <button type="submit" class="btn btn-primary">
            <?= $editData ? '💾 Simpan' : '📤 Tambahkan' ?>
        </button>
        <?php if ($editData): ?><a href="<?= adminUrl('/manajemen_event.php') ?>" class="btn"
                style="border:1px solid #E0E0E0;margin-left:8px;">Batal</a>
        <?php endif; ?>
    </form>
</div>

<div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="padding:16px 20px;border-bottom:1px solid #E0E0E0;"><strong>📅 Daftar Event (
            <?= count($eventList) ?>)
        </strong></div>
    <?php if (empty($eventList)): ?>
        <div style="padding:40px;text-align:center;color:#757575;">Belum ada event.</div>
    <?php else: ?>
        <?php foreach ($eventList as $ev): ?>
            <div
                style="padding:16px 20px;border-bottom:1px solid #F5F5F5;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <strong>
                        <?= sanitize($ev['judul']) ?>
                    </strong>
                    <div style="font-size:0.8rem;color:#757575;">
                        <?= $ev['tanggal_pelaksanaan'] ? formatTanggal($ev['tanggal_pelaksanaan']) : 'TBA' ?>
                    </div>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="?edit=<?= $ev['id'] ?>" class="btn btn-sm"
                        style="border:1px solid #E0E0E0;background:white;">�️</a>
                    <form method="POST" style="display:inline;">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus event ini?">🗑️</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
