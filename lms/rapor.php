<?php
/**
 * HMI IT Telkom - LMS RAPOR (Progress Report)
 */
$pageTitle = 'Rapor Saya';
require_once __DIR__ . '/../includes/lms_header.php';

$pdo = getDB();
$userId = $_SESSION['user_id'];

// Fetch user profile
$stmt = $pdo->prepare("SELECT kp.*, u.username, u.email FROM kader_profiles kp JOIN users u ON kp.user_id = u.id WHERE kp.user_id=?");
$stmt->execute([$userId]);
$profile = $stmt->fetch();

// Fetch modules with best scores
$modules = $pdo->query("SELECT * FROM lms_modules ORDER BY urutan")->fetchAll();

$reportData = [];
$totalPassed = 0;
$avgScore = 0;
$scoreSum = 0;
$attempts = 0;

foreach ($modules as $mod) {
    $stmt = $pdo->prepare("SELECT MAX(skor) as best, MAX(lulus) as passed, COUNT(*) as tries FROM lms_scores WHERE user_id=? AND module_id=?");
    $stmt->execute([$userId, $mod['id']]);
    $sc = $stmt->fetch();

    $reportData[] = [
        'module' => $mod,
        'best_score' => $sc['best'] !== null ? (int) $sc['best'] : null,
        'passed' => (bool) $sc['passed'],
        'attempts' => (int) $sc['tries'],
    ];

    if ($sc['passed'])
        $totalPassed++;
    if ($sc['best'] !== null) {
        $scoreSum += (int) $sc['best'];
        $attempts++;
    }
}

$avgScore = $attempts > 0 ? round($scoreSum / $attempts) : 0;
$progressPct = count($modules) > 0 ? round(($totalPassed / count($modules)) * 100) : 0;
$icons = ['📜', '⚖️', '💡', '👔', '🎯'];
?>

<!-- Profile Card -->
<div
    style="background:linear-gradient(135deg,#0D3B13,#1B5E20);border-radius:16px;padding:32px;color:white;margin-bottom:32px;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:24px;">
        <div>
            <h2 style="font-size:1.5rem;margin-bottom:4px;">📋 Rapor Kaderisasi Digital</h2>
            <?php if ($profile): ?>
                <p style="opacity:0.8;margin-bottom:0;">
                    <?= sanitize($profile['nama_lengkap']) ?> ·
                    <?= sanitize($profile['nim']) ?> ·
                    <?= sanitize($profile['program_studi']) ?> ·
                    <?= $profile['angkatan'] ?>
                </p>
            <?php endif; ?>
        </div>
        <div style="display:flex;gap:24px;text-align:center;">
            <div>
                <div style="font-size:2rem;font-weight:800;color:#FFD700;">
                    <?= $progressPct ?>%
                </div>
                <div style="font-size:0.8rem;opacity:0.7;">Progres</div>
            </div>
            <div>
                <div style="font-size:2rem;font-weight:800;color:#FFD700;">
                    <?= $avgScore ?>
                </div>
                <div style="font-size:0.8rem;opacity:0.7;">Rata-rata Skor</div>
            </div>
            <div>
                <div style="font-size:2rem;font-weight:800;color:#FFD700;">
                    <?= $totalPassed ?>/
                    <?= count($modules) ?>
                </div>
                <div style="font-size:0.8rem;opacity:0.7;">Modul Lulus</div>
            </div>
        </div>
    </div>
</div>

<!-- Module Results -->
<div style="display:flex;flex-direction:column;gap:16px;">
    <?php foreach ($reportData as $i => $rd): ?>
        <div
            style="background:white;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;justify-content:space-between;align-items:center;border-left:4px solid <?= $rd['passed'] ? '#4CAF50' : ($rd['best_score'] !== null ? '#FF9800' : '#E0E0E0') ?>;">
            <div style="display:flex;align-items:center;gap:16px;">
                <span style="font-size:1.5rem;">
                    <?= $icons[$i] ?? '📖' ?>
                </span>
                <div>
                    <strong>
                        <?= sanitize($rd['module']['nama_modul']) ?>
                    </strong>
                    <div style="font-size:0.85rem;color:#757575;">
                        Passing:
                        <?= $rd['module']['passing_score'] ?>% · Percobaan:
                        <?= $rd['attempts'] ?>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:16px;">
                <?php if ($rd['best_score'] !== null): ?>
                    <div style="text-align:right;">
                        <div style="font-size:1.3rem;font-weight:700;color:<?= $rd['passed'] ? '#1B5E20' : '#E53935' ?>;">
                            <?= $rd['best_score'] ?>%
                        </div>
                        <div style="font-size:0.75rem;color:#757575;">Skor Terbaik</div>
                    </div>
                <?php endif; ?>
                <?php if ($rd['passed']): ?>
                    <span
                        style="background:#E8F5E9;color:#1B5E20;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;">✅
                        LULUS</span>
                <?php elseif ($rd['best_score'] !== null): ?>
                    <span
                        style="background:#FFF8E1;color:#F57F17;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;">🔄
                        ULANG</span>
                <?php else: ?>
                    <span
                        style="background:#F5F5F5;color:#9E9E9E;padding:6px 14px;border-radius:50px;font-size:0.8rem;font-weight:700;">—
                        BELUM</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Final Status -->
<?php if ($totalPassed === count($modules)): ?>
    <div
        style="background:linear-gradient(135deg,#FFD700,#FFA000);border-radius:16px;padding:40px;text-align:center;margin-top:32px;">
        <div style="font-size:3rem;margin-bottom:12px;">🏆</div>
        <h2 style="font-size:1.5rem;color:#121212;margin-bottom:8px;">Selamat! Kaderisasi Digital Selesai!</h2>
        <p style="color:rgba(0,0,0,0.6);">Anda telah menyelesaikan seluruh 5 modul wajib kaderisasi LMS HMI.</p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/lms_footer.php'; ?>