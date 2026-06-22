<?php
/**
 * HMI IT Telkom - LMS ADMIN: Bank Soal
 */
$pageTitle = 'Bank Soal LMS';
require_once __DIR__ . '/../includes/admin_header.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRF($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $moduleId = (int) $_POST['module_id'];
        $pertanyaan = trim($_POST['pertanyaan'] ?? '');
        $opsiA = trim($_POST['opsi_a'] ?? '');
        $opsiB = trim($_POST['opsi_b'] ?? '');
        $opsiC = trim($_POST['opsi_c'] ?? '');
        $opsiD = trim($_POST['opsi_d'] ?? '');
        $jawaban = $_POST['jawaban_benar'] ?? '';

        if ($moduleId && $pertanyaan && $opsiA && $opsiB && $opsiC && $opsiD && $jawaban) {
            $stmt = $pdo->prepare("INSERT INTO lms_questions (module_id,pertanyaan,opsi_a,opsi_b,opsi_c,opsi_d,jawaban_benar) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$moduleId, $pertanyaan, $opsiA, $opsiB, $opsiC, $opsiD, $jawaban]);
            flash('success', 'Soal berhasil ditambahkan.', 'success');
        } else {
            flash('error', 'Semua field wajib diisi.', 'error');
        }
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM lms_questions WHERE id=?")->execute([(int) $_POST['id']]);
        flash('success', 'Soal dihapus.', 'success');
    }
    redirect(adminUrl('/lms_questions.php'));
}

$modules = $pdo->query("SELECT * FROM lms_modules ORDER BY urutan")->fetchAll();
$filterModule = (int) ($_GET['module'] ?? 0);
$whereClause = $filterModule ? "WHERE q.module_id=$filterModule" : "";
$questions = $pdo->query("SELECT q.*, m.nama_modul FROM lms_questions q JOIN lms_modules m ON q.module_id=m.id $whereClause ORDER BY m.urutan, q.id")->fetchAll();
?>

<div style="background:white;border-radius:12px;padding:24px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <h3 style="margin-bottom:16px;">➕ Tambah Soal Baru</h3>
    <form method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="create">
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
            <label class="form-label">Pertanyaan *</label>
            <textarea name="pertanyaan" class="form-input" rows="3" required></textarea>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Opsi A *</label><input type="text" name="opsi_a"
                    class="form-input" required></div>
            <div class="form-group"><label class="form-label">Opsi B *</label><input type="text" name="opsi_b"
                    class="form-input" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Opsi C *</label><input type="text" name="opsi_c"
                    class="form-input" required></div>
            <div class="form-group"><label class="form-label">Opsi D *</label><input type="text" name="opsi_d"
                    class="form-input" required></div>
        </div>
        <div class="form-group">
            <label class="form-label">Jawaban Benar *</label>
            <select name="jawaban_benar" class="form-input" required style="width:auto;">
                <option value="">— Pilih —</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">📤 Tambah Soal</button>
    </form>
</div>

<!-- Filter -->
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
    <a href="<?= adminUrl('/lms_questions.php') ?>" class="btn btn-sm <?= !$filterModule ? 'btn-primary' : '' ?>"
        style="<?= !$filterModule ? '' : 'border:1px solid #E0E0E0;background:white;' ?>">Semua</a>
    <?php foreach ($modules as $m): ?>
        <a href="?module=<?= $m['id'] ?>" class="btn btn-sm <?= $filterModule === $m['id'] ? 'btn-primary' : '' ?>"
            style="<?= $filterModule === $m['id'] ? '' : 'border:1px solid #E0E0E0;background:white;' ?>">
            <?= sanitize($m['nama_modul']) ?>
        </a>
    <?php endforeach; ?>
</div>

<div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="padding:16px 20px;border-bottom:1px solid #E0E0E0;"><strong> Bank Soal (
            <?= count($questions) ?>)
        </strong></div>
    <?php foreach ($questions as $q): ?>
        <div style="padding:14px 20px;border-bottom:1px solid #F5F5F5;">
            <div style="display:flex;justify-content:space-between;align-items:start;">
                <div style="flex:1;">
                    <span style="font-size:0.75rem;background:#E8F5E9;color:#1B5E20;padding:2px 8px;border-radius:50px;">
                        <?= sanitize($q['nama_modul']) ?>
                    </span>
                    <p style="margin:8px 0;font-weight:500;">
                        <?= sanitize($q['pertanyaan']) ?>
                    </p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;font-size:0.85rem;color:#616161;">
                        <span>
                            <?= $q['jawaban_benar'] === 'A' ? '✅' : '⬜' ?> A:
                            <?= sanitize($q['opsi_a']) ?>
                        </span>
                        <span>
                            <?= $q['jawaban_benar'] === 'B' ? '✅' : '⬜' ?> B:
                            <?= sanitize($q['opsi_b']) ?>
                        </span>
                        <span>
                            <?= $q['jawaban_benar'] === 'C' ? '✅' : '⬜' ?> C:
                            <?= sanitize($q['opsi_c']) ?>
                        </span>
                        <span>
                            <?= $q['jawaban_benar'] === 'D' ? '✅' : '⬜' ?> D:
                            <?= sanitize($q['opsi_d']) ?>
                        </span>
                    </div>
                </div>
                <form method="POST"><input type="hidden" name="action" value="delete"><input type="hidden" name="id"
                        value="<?= $q['id'] ?>">
                    <?= csrfField() ?><button class="btn btn-danger btn-sm" data-confirm="Hapus soal?"
                        style="padding:4px 10px;font-size:0.8rem;">🗑️</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($questions)): ?>
        <div style="padding:40px;text-align:center;color:#757575;">Belum ada soal.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
