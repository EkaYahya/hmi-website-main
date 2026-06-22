<?php
/**
 * HMI IT Telkom - LMS MODULE VIEWER
 * Multi-modal content viewer + quiz access
 */
require_once __DIR__ . '/../config/functions.php';
requireKader();

$moduleId = (int) ($_GET['id'] ?? 0);
if (!$moduleId)
    redirect(lmsUrl('/index.php'));

$pdo = getDB();
$userId = $_SESSION['user_id'];

// Fetch module
$stmt = $pdo->prepare("SELECT * FROM lms_modules WHERE id=?");
$stmt->execute([$moduleId]);
$module = $stmt->fetch();
if (!$module) {
    flash('error', 'Modul tidak ditemukan.', 'error');
    redirect(lmsUrl('/index.php'));
}

// Check gating
$modules = $pdo->query("SELECT * FROM lms_modules ORDER BY urutan")->fetchAll();
foreach ($modules as $i => $m) {
    if ($m['id'] == $moduleId && $i > 0) {
        $prevId = $modules[$i - 1]['id'];
        $stmtCheck = $pdo->prepare("SELECT MAX(lulus) as passed FROM lms_scores WHERE user_id=? AND module_id=?");
        $stmtCheck->execute([$userId, $prevId]);
        $prev = $stmtCheck->fetch();
        if (!$prev || !$prev['passed']) {
            flash('error', 'Anda harus menyelesaikan modul sebelumnya terlebih dahulu.', 'error');
            redirect(lmsUrl('/index.php'));
        }
    }
}

// Fetch materials
$stmt = $pdo->prepare("SELECT * FROM lms_materials WHERE module_id=? ORDER BY id");
$stmt->execute([$moduleId]);
$materials = $stmt->fetchAll();

// Fetch quiz info
$qCount = $pdo->prepare("SELECT COUNT(*) FROM lms_questions WHERE module_id=?");
$qCount->execute([$moduleId]);
$questionCount = $qCount->fetchColumn();

// User's scores for this module
$stmt = $pdo->prepare("SELECT * FROM lms_scores WHERE user_id=? AND module_id=? ORDER BY created_at DESC");
$stmt->execute([$userId, $moduleId]);
$attempts = $stmt->fetchAll();
$bestScore = 0;
$isPassed = false;
foreach ($attempts as $a) {
    if ($a['skor'] > $bestScore)
        $bestScore = $a['skor'];
    if ($a['lulus'])
        $isPassed = true;
}

$pageTitle = $module['nama_modul'];
require_once __DIR__ . '/../includes/lms_header.php';
?>

<!-- Module Header -->
<div
    style="background:linear-gradient(135deg,#0D3B13,#1B5E20);border-radius:16px;padding:32px;color:white;margin-bottom:32px;">
    <h2 style="margin-bottom:8px;">
        <?= sanitize($module['nama_modul']) ?>
    </h2>
    <p style="opacity:0.7;line-height:1.6;">
        <?= sanitize($module['deskripsi']) ?>
    </p>
    <div style="display:flex;gap:16px;margin-top:16px;flex-wrap:wrap;">
        <span style="background:rgba(255,255,255,0.15);padding:6px 14px;border-radius:50px;font-size:0.85rem;">📎
            <?= count($materials) ?> Materi
        </span>
        <span style="background:rgba(255,255,255,0.15);padding:6px 14px;border-radius:50px;font-size:0.85rem;">📃
            <?= $questionCount ?> Soal Quiz
        </span>
        <span style="background:rgba(255,255,255,0.15);padding:6px 14px;border-radius:50px;font-size:0.85rem;">🎯
            Passing:
            <?= $module['passing_score'] ?>%
        </span>
        <?php if ($isPassed): ?>
            <span
                style="background:#FFD700;color:#121212;padding:6px 14px;border-radius:50px;font-size:0.85rem;font-weight:700;">✅
                LULUS</span>
        <?php endif; ?>
    </div>
</div>

<!-- Materials -->
<h3 style="margin-bottom:16px;">📎 Materi Pembelajaran</h3>

<?php if (empty($materials)): ?>
    <div style="background:white;border-radius:12px;padding:40px;text-align:center;color:#757575;margin-bottom:24px;">Belum
        ada materi untuk modul ini.</div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:16px;margin-bottom:32px;">
        <?php foreach ($materials as $mat): ?>
            <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <h4 style="font-size:1rem;">
                        <?php
                        $typeIcon = ['teks' => '📝', 'pdf' => '📄', 'video' => '🎬', 'slide' => '📊'];
                        echo ($typeIcon[$mat['tipe_konten']] ?? '📎') . ' ' . sanitize($mat['judul_materi']);
                        ?>
                    </h4>
                    <span style="font-size:0.75rem;background:#E3F2FD;color:#1565C0;padding:3px 10px;border-radius:50px;">
                        <?= $mat['tipe_konten'] ?>
                    </span>
                </div>

                <?php if ($mat['tipe_konten'] === 'teks' && $mat['konten_teks']): ?>
                    <div style="color:#424242;line-height:1.8;font-size:0.95rem;">
                        <?= $mat['konten_teks'] ?>
                    </div>
                <?php endif; ?>

                <?php if ($mat['tipe_konten'] === 'video' && $mat['url_konten']): ?>
                    <?php
                    $yt = $mat['url_konten'];
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]+)/', $yt, $m)) {
                        echo '<div style="position:relative;padding-top:56.25%;border-radius:12px;overflow:hidden;"><iframe src="https://www.youtube.com/embed/' . $m[1] . '" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;" allowfullscreen></iframe></div>';
                    } else {
                        echo '<a href="' . sanitize($yt) . '" target="_blank" class="btn btn-primary btn-sm">▶ Tonton Video</a>';
                    }
                    ?>
                <?php endif; ?>

                <?php if ($mat['file_path']): ?>
                    <a href="<?= asset('/' . $mat['file_path']) ?>" target="_blank" class="btn btn-primary btn-sm"
                        style="margin-top:8px;"> <?= strtoupper($mat['tipe_konten']) ?>
                    </a>
                <?php endif; ?>

                <?php if ($mat['tipe_konten'] === 'pdf' && $mat['file_path']): ?>
                    <div style="margin-top:12px;border:1px solid #E0E0E0;border-radius:8px;overflow:hidden;">
                        <a href="<?= lmsUrl('/quiz.php?module_id=' . $moduleId) ?>" class="btn btn-primary">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Quiz Section -->
<h3 style="margin-bottom:16px;">📄 Quiz Evaluasi</h3>

<?php if ($questionCount > 0): ?>
    <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
            <div>
                <p style="margin-bottom:4px;">Kerjakan quiz untuk menguji pemahaman Anda.</p>
                <div style="font-size:0.85rem;color:#757575;">
                    <?= $questionCount ?> soal · Passing score:
                    <?= $module['passing_score'] ?>% · Waktu:
                    <?= $questionCount * 2 ?> menit
                </div>
            </div>
            <a href="<?= lmsUrl('/quiz.php?module_id=<?= $moduleId ?>') ?>" class="btn btn-primary">
                <?= count($attempts) > 0 ? '🔄 Kerjakan Lagi' : '📝 Mulai Quiz' ?>
            </a>
        </div>
    </div>
<?php else: ?>
    <div style="background:white;border-radius:12px;padding:24px;text-align:center;color:#757575;margin-bottom:24px;">
        Belum ada soal quiz untuk modul ini.
    </div>
<?php endif; ?>

<!-- Riwayat Percobaan -->
<?php if (!empty($attempts)): ?>
    <h3 style="margin-bottom:16px;">📋 Riwayat Percobaan</h3>
    <div style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Percobaan</th>
                    <th>Skor</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attempts as $idx => $att): ?>
                    <tr>
                        <td>#
                            <?= count($attempts) - $idx ?>
                        </td>
                        <td><strong style="color:<?= $att['lulus'] ? '#1B5E20' : '#E53935' ?>">
                                <?= $att['skor'] ?>%
                            </strong></td>
                        <td>
                            <?php if ($att['lulus']): ?>
                                <span
                                    style="background:#E8F5E9;color:#1B5E20;padding:3px 10px;border-radius:50px;font-size:0.8rem;font-weight:600;">✅
                                    Lulus</span>
                            <?php else: ?>
                                <span
                                    style="background:#FFEBEE;color:#E53935;padding:3px 10px;border-radius:50px;font-size:0.8rem;font-weight:600;">❌
                                    Belum Lulus</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:0.85rem;color:#757575;">
                            <?= formatTanggal($att['created_at']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/lms_footer.php'; ?>
