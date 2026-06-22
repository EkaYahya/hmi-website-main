<?php
/**
 * HMI IT Telkom - PANEL KADER (CRUD)
 */
$pageTitle = 'Manajemen Kader';
require_once __DIR__ . '/../includes/admin_header.php';

$pdo = getDB();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        flash('error', 'Sesi tidak valid.', 'error');
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_status') {
            $kaderId = (int) $_POST['kader_id'];
            $status = $_POST['status'];
            $allowed = ['pending', 'lk1_registered', 'lk1_lulus', 'aktif'];
            if (in_array($status, $allowed)) {
                $stmt = $pdo->prepare("UPDATE kader_profiles SET status_kaderisasi = ? WHERE id = ?");
                $stmt->execute([$status, $kaderId]);
                flash('success', 'Status kader berhasil diperbarui.', 'success');
            }
        } elseif ($action === 'delete') {
            $userId = (int) $_POST['user_id'];
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'kader'");
            $stmt->execute([$userId]);
            flash('success', 'Data kader berhasil dihapus.', 'success');
        }
    }
    redirect(adminUrl('/panel_kader.php'));
}

// Filters
$filterProdi = $_GET['prodi'] ?? '';
$filterAngkatan = $_GET['angkatan'] ?? '';
$filterStatus = $_GET['status'] ?? '';

$where = "WHERE 1=1";
$params = [];
if ($filterProdi) {
    $where .= " AND kp.program_studi = ?";
    $params[] = $filterProdi;
}
if ($filterAngkatan) {
    $where .= " AND kp.angkatan = ?";
    $params[] = $filterAngkatan;
}
if ($filterStatus) {
    $where .= " AND kp.status_kaderisasi = ?";
    $params[] = $filterStatus;
}

$stmt = $pdo->prepare("SELECT kp.*, u.username, u.email, u.id as user_id FROM kader_profiles kp JOIN users u ON kp.user_id = u.id $where ORDER BY kp.created_at DESC");
$stmt->execute($params);
$kaderList = $stmt->fetchAll();

// Get unique values for filters
$prodiList = $pdo->query("SELECT DISTINCT program_studi FROM kader_profiles ORDER BY program_studi")->fetchAll(PDO::FETCH_COLUMN);
$angkatanList = $pdo->query("SELECT DISTINCT angkatan FROM kader_profiles ORDER BY angkatan DESC")->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Filters -->
<div style="background:white;border-radius:12px;padding:20px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;">
        <div>
            <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:4px;">Program Studi</label>
            <select name="prodi" class="form-input" style="width:auto;padding:8px 12px;font-size:0.85rem;">
                <option value="">Semua Prodi</option>
                <?php foreach ($prodiList as $p): ?>
                    <option value="<?= $p ?>" <?= $filterProdi === $p ? 'selected' : '' ?>>
                        <?= sanitize($p) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:4px;">Angkatan</label>
            <select name="angkatan" class="form-input" style="width:auto;padding:8px 12px;font-size:0.85rem;">
                <option value="">Semua</option>
                <?php foreach ($angkatanList as $a): ?>
                    <option value="<?= $a ?>" <?= $filterAngkatan == $a ? 'selected' : '' ?>>
                        <?= $a ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="font-size:0.8rem;font-weight:600;display:block;margin-bottom:4px;">Status</label>
            <select name="status" class="form-input" style="width:auto;padding:8px 12px;font-size:0.85rem;">
                <option value="">Semua</option>
                <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="lk1_registered" <?= $filterStatus === 'lk1_registered' ? 'selected' : '' ?>>LK-1 Terdaftar
                </option>
                <option value="lk1_lulus" <?= $filterStatus === 'lk1_lulus' ? 'selected' : '' ?>>LK-1 Lulus</option>
                <option value="aktif" <?= $filterStatus === 'aktif' ? 'selected' : '' ?>>Aktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">🔍 Filter</button>
        <a href="<?= adminUrl('/panel_kader.php') ?>" class="btn btn-sm"
            style="border:1px solid #E0E0E0;background:white;">Reset</a>
    </form>
</div>

<!-- Kader Table -->
<div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div
        style="padding:16px 20px;border-bottom:1px solid #E0E0E0;display:flex;justify-content:space-between;align-items:center;">
        <strong>📋 Daftar Kader (
            <?= count($kaderList) ?>)
        </strong>
    </div>

    <?php if (empty($kaderList)): ?>
        <div style="padding:40px;text-align:center;color:#757575;">Tidak ada data kader ditemukan.</div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIM</th>
                        <th>Program Studi</th>
                        <th>Angkatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kaderList as $k): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= sanitize($k['nama_lengkap']) ?>
                                </strong>
                                <div style="font-size:0.8rem;color:#757575;">@
                                    <?= sanitize($k['username']) ?>
                                </div>
                            </td>
                            <td>
                                <?= sanitize($k['nim']) ?>
                            </td>
                            <td>
                                <?= sanitize($k['program_studi']) ?>
                            </td>
                            <td>
                                <?= $k['angkatan'] ?>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="kader_id" value="<?= $k['id'] ?>">
                                    <select name="status" onchange="this.form.submit()"
                                        style="padding:4px 8px;border:1px solid #E0E0E0;border-radius:6px;font-size:0.8rem;cursor:pointer;">
                                        <option value="pending" <?= $k['status_kaderisasi'] === 'pending' ? 'selected' : '' ?>>⏳
                                            Pending</option>
                                        <option value="lk1_registered" <?= $k['status_kaderisasi'] === 'lk1_registered' ? 'selected' : '' ?>>📝 LK-1 Terdaftar</option>
                                        <option value="lk1_lulus" <?= $k['status_kaderisasi'] === 'lk1_lulus' ? 'selected' : '' ?>
                                            >✅ LK-1 Lulus</option>
                                        <option value="aktif" <?= $k['status_kaderisasi'] === 'aktif' ? 'selected' : '' ?>>🟢 Aktif
                                        </option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?= $k['user_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        data-confirm="Yakin menghapus kader <?= sanitize($k['nama_lengkap']) ?>?"
                                        style="padding:4px 12px;font-size:0.8rem;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
