<?php
/**
 * HMI IT Telkom - ADMIN: Manajemen Pengurus (Organigram)
 */
require_once __DIR__ . '/../config/functions.php';
$pdo = getDB();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nama = trim($_POST['nama'] ?? '');
        $jabatan = trim($_POST['jabatan'] ?? '');
        $bidang = trim($_POST['bidang'] ?? '') ?: null;
        $level = $_POST['level'] ?? 'staff';
        $urutan = (int) ($_POST['urutan'] ?? 0);
        $periode = trim($_POST['periode'] ?? '2024-2025');

        $fotoPath = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($_FILES['foto'], 'assets/uploads/pengurus', ['jpg', 'jpeg', 'png', 'webp'], 5242880);
            if ($upload['success'])
                $fotoPath = $upload['path'];
        }

        if ($nama && $jabatan) {
            $stmt = $pdo->prepare("INSERT INTO pengurus (nama, jabatan, bidang, level, urutan, foto_path, periode) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$nama, $jabatan, $bidang, $level, $urutan, $fotoPath, $periode]);
            flash('success', 'Pengurus berhasil ditambahkan.', 'success');
        }
    } elseif ($action === 'edit') {
        $id = (int) $_POST['id'];
        $nama = trim($_POST['nama'] ?? '');
        $jabatan = trim($_POST['jabatan'] ?? '');
        $bidang = trim($_POST['bidang'] ?? '') ?: null;
        $level = $_POST['level'] ?? 'staff';
        $urutan = (int) ($_POST['urutan'] ?? 0);
        $periode = trim($_POST['periode'] ?? '2024-2025');

        $fotoSql = '';
        $params = [$nama, $jabatan, $bidang, $level, $urutan, $periode];

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($_FILES['foto'], 'assets/uploads/pengurus', ['jpg', 'jpeg', 'png', 'webp'], 5242880);
            if ($upload['success']) {
                $fotoSql = ', foto_path=?';
                $params[] = $upload['path'];
            }
        }
        $params[] = $id;
        $pdo->prepare("UPDATE pengurus SET nama=?, jabatan=?, bidang=?, level=?, urutan=?, periode=?$fotoSql WHERE id=?")->execute($params);
        flash('success', 'Data pengurus diperbarui.', 'success');

    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM pengurus WHERE id=?")->execute([(int) $_POST['id']]);
        flash('success', 'Pengurus dihapus.', 'success');

    } elseif ($action === 'toggle_active') {
        $pdo->prepare("UPDATE pengurus SET is_active = NOT is_active WHERE id=?")->execute([(int) $_POST['id']]);
        flash('success', 'Status diperbarui.', 'success');
    }
    redirect(adminUrl('/pengurus.php'));
}

// Now safe to output HTML
$pageTitle = 'Manajemen Pengurus';
require_once __DIR__ . '/../includes/admin_header.php';

// Get data
$periodeAktif = getSetting('periode_aktif', '2024-2025');
$filterPeriode = $_GET['periode'] ?? $periodeAktif;

$stmt = $pdo->prepare("SELECT * FROM pengurus WHERE periode=? ORDER BY FIELD(level,'top','pao','middle','staff'), urutan");
$stmt->execute([$filterPeriode]);
$pengurus = $stmt->fetchAll();

$periodeList = $pdo->query("SELECT DISTINCT periode FROM pengurus ORDER BY periode DESC")->fetchAll(PDO::FETCH_COLUMN);

// Check for edit mode
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM pengurus WHERE id=?");
    $stmt->execute([(int) $_GET['edit']]);
    $editItem = $stmt->fetch();
}

$levelLabels = ['top' => 'Top Management', 'pao' => 'PAO', 'middle' => 'Middle Management (Bidang)', 'staff' => 'Staff'];
$levelColors = ['top' => '#1B5E20', 'pao' => '#E65100', 'middle' => '#1565C0', 'staff' => '#757575'];
?>

<!-- Form Tambah/Edit -->
<div style="background:white;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <h3 style="margin-bottom:16px;">
        <?= $editItem ? '✏️ Edit Pengurus' : '➕ Tambah Pengurus' ?>
    </h3>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="<?= $editItem ? 'edit' : 'create' ?>">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= $editItem['id'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="nama" class="form-input" value="<?= sanitize($editItem['nama'] ?? '') ?>"
                    required>
            </div>
            <div class="form-group">
                <label class="form-label">Jabatan *</label>
                <input type="text" name="jabatan" class="form-input" value="<?= sanitize($editItem['jabatan'] ?? '') ?>"
                    placeholder="Ketua Umum / Kepala Bidang / Staff" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Level Organisasi *</label>
                <select name="level" class="form-input" required>
                    <?php foreach ($levelLabels as $k => $v): ?>
                        <option value="<?= $k ?>" <?= ($editItem['level'] ?? '') === $k ? 'selected' : '' ?>>
                            <?= $v ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Bidang</label>
                <input type="text" name="bidang" class="form-input" value="<?= sanitize($editItem['bidang'] ?? '') ?>"
                    placeholder="Contoh: Pembinaan Anggota (kosongkan jika Top/PAO)">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Periode</label>
                <input type="text" name="periode" class="form-input"
                    value="<?= sanitize($editItem['periode'] ?? $periodeAktif) ?>" placeholder="2024-2025">
            </div>
            <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="urutan" class="form-input" value="<?= $editItem['urutan'] ?? 0 ?>" min="0">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Foto (JPG/PNG/WebP, max 5MB)</label>
            <input type="file" name="foto" class="form-input" accept=".jpg,.jpeg,.png,.webp" style="padding:10px;">
            <?php if ($editItem && $editItem['foto_path']): ?>
                <div style="margin-top:8px;"><img src="<?= asset('/' . $editItem['foto_path']) ?>"
                        style="width:60px;height:60px;border-radius:50%;object-fit:cover;"> <small
                        style="color:#757575;">Foto saat ini</small></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">
            <?= $editItem ? '💾 Simpan Perubahan' : '➕ Tambah Pengurus' ?>
        </button>
        <?php if ($editItem): ?>
            <a href="<?= adminUrl('/pengurus.php') ?>" class="btn" style="border:1px solid #E0E0E0;margin-left:8px;">Batal</a>
        <?php endif; ?>
    </form>
</div>

<!-- Filter Periode -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;align-items:center;">
    <span style="font-weight:600;font-size:0.9rem;">Periode:</span>
    <?php foreach ($periodeList as $p): ?>
        <a href="?periode=<?= urlencode($p) ?>" class="btn btn-sm <?= $filterPeriode === $p ? 'btn-primary' : '' ?>"
            style="<?= $filterPeriode === $p ? '' : 'border:1px solid #E0E0E0;background:white;' ?>">
            <?= sanitize($p) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Organigram Preview -->
<?php
$grouped = ['top' => [], 'pao' => [], 'middle' => [], 'staff' => []];
foreach ($pengurus as $p) {
    $grouped[$p['level']][] = $p;
}
?>

<?php foreach (['top', 'pao', 'middle', 'staff'] as $lvl): ?>
    <?php if (!empty($grouped[$lvl])): ?>
        <div style="margin-bottom:24px;">
            <div
                style="padding:10px 16px;background:<?= $levelColors[$lvl] ?>;color:white;border-radius:8px 8px 0 0;font-weight:600;font-size:0.85rem;">
                <?= $levelLabels[$lvl] ?> (
                <?= count($grouped[$lvl]) ?>)
            </div>
            <div style="background:white;border-radius:0 0 8px 8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <?php foreach ($grouped[$lvl] as $p): ?>
                    <div
                        style="padding:12px 16px;border-bottom:1px solid #F5F5F5;display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <?php if ($p['foto_path']): ?>
                                <img src="<?= asset('/' . $p['foto_path']) ?>"
                                    style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                                <div
                                    style="width:40px;height:40px;border-radius:50%;background:<?= $levelColors[$lvl] ?>20;display:flex;align-items:center;justify-content:center;font-weight:700;color:<?= $levelColors[$lvl] ?>;font-size:0.85rem;">
                                    <?= mb_substr($p['nama'], 0, 1) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <strong style="font-size:0.9rem;">
                                    <?= sanitize($p['nama']) ?>
                                </strong>
                                <div style="font-size:0.8rem;color:#757575;">
                                    <?= sanitize($p['jabatan']) ?>
                                    <?php if ($p['bidang']): ?><span style="color:<?= $levelColors[$lvl] ?>"> ·
                                            <?= sanitize($p['bidang']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <a href="?edit=<?= $p['id'] ?>&periode=<?= urlencode($filterPeriode) ?>" class="btn btn-sm"
                                style="border:1px solid #E0E0E0;padding:4px 10px;font-size:0.8rem;">✏️</a>
                            <form method="POST" style="display:inline;"><input type="hidden" name="action" value="delete"><input
                                    type="hidden" name="id" value="<?= $p['id'] ?>">
                                <?= csrfField() ?><button class="btn btn-danger btn-sm" data-confirm="Hapus pengurus?"
                                    style="padding:4px 10px;font-size:0.8rem;">🗑️</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
