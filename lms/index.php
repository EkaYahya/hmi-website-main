<?php
/**
 * HMI IT Telkom - LMS DASHBOARD KADER
 * Shows 5 mandatory modules with gated progression
 */
$pageTitle = 'Dashboard Modul';
require_once __DIR__ . '/../includes/lms_header.php';

$pdo = getDB();
$userId = $_SESSION['user_id'];

// Fetch modules
$modules = $pdo->query("SELECT * FROM lms_modules ORDER BY urutan")->fetchAll();

// Fetch user's scores
$stmt = $pdo->prepare("SELECT module_id, MAX(skor) as best_score, MAX(lulus) as passed FROM lms_scores WHERE user_id=? GROUP BY module_id");
$stmt->execute([$userId]);
$scoresRaw = $stmt->fetchAll();
$scores = [];
foreach ($scoresRaw as $s) {
    $scores[$s['module_id']] = $s;
}

// Module order for gating: must pass previous module first
$icons = ['📜', '⚖️', '💡', '👔', '🎯'];
$colors = ['#1B5E20', '#2E7D32', '#388E3C', '#43A047', '#4CAF50'];

// Count total materials per module
$matCounts = [];
$matStmt = $pdo->query("SELECT module_id, COUNT(*) as cnt FROM lms_materials GROUP BY module_id");
foreach ($matStmt->fetchAll() as $mc) {
    $matCounts[$mc['module_id']] = $mc['cnt'];
}

// Count total questions per module
$qCounts = [];
$qStmt = $pdo->query("SELECT module_id, COUNT(*) as cnt FROM lms_questions GROUP BY module_id");
foreach ($qStmt->fetchAll() as $qc) {
    $qCounts[$qc['module_id']] = $qc['cnt'];
}

// Overall progress
$totalModules = count($modules);
$completedModules = 0;
foreach ($modules as $m) {
    if (isset($scores[$m['id']]) && $scores[$m['id']]['passed'])
        $completedModules++;
}
$progressPct = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;
?>

<!-- Progress Overview -->
<div
    style="background:linear-gradient(135deg,#0D3B13,#1B5E20);border-radius:16px;padding:32px;color:white;margin-bottom:32px;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
        <div>
            <h2 style="font-size:1.6rem;margin-bottom:8px;">Progres Kaderisasi</h2>
            <p style="opacity:0.7;font-size:0.95rem;">Selesaikan 5 modul wajib untuk menyelesaikan kaderisasi digital.
            </p>
        </div>
        <div style="text-align:center;">
            <div style="font-size:2.5rem;font-weight:800;color:#FFD700;">
                <?= $progressPct ?>%
            </div>
            <div style="font-size:0.85rem;opacity:0.7;">
                <?= $completedModules ?>/
                <?= $totalModules ?> Modul
            </div>
        </div>
    </div>
    <div style="background:rgba(255,255,255,0.15);border-radius:50px;height:12px;margin-top:20px;overflow:hidden;">
        <div
            style="background:linear-gradient(90deg,#FFD700,#FFA000);height:100%;border-radius:50px;width:<?= $progressPct ?>%;transition:width 1s ease;">
        </div>
    </div>
</div>

<!-- Module Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
    <?php foreach ($modules as $i => $mod):
        $isLocked = false;
        $isPassed = isset($scores[$mod['id']]) && $scores[$mod['id']]['passed'];
        $bestScore = isset($scores[$mod['id']]) ? (int) $scores[$mod['id']]['best_score'] : null;

        // Gating: check if previous module is passed (first module always unlocked)
        if ($i > 0) {
            $prevModId = $modules[$i - 1]['id'];
            if (!isset($scores[$prevModId]) || !$scores[$prevModId]['passed']) {
                $isLocked = true;
            }
        }
        ?>
        <div class="lms-module-card <?= $isLocked ? 'locked' : '' ?> <?= $isPassed ? 'completed' : '' ?>">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:12px;">
                <span style="font-size:2rem;">
                    <?= $isLocked ? '🔒' : ($icons[$i] ?? '📖') ?>
                </span>
                <?php if ($isPassed): ?>
                    <span
                        style="background:#E8F5E9;color:#1B5E20;font-size:0.75rem;padding:4px 12px;border-radius:50px;font-weight:700;">✅
                        LULUS</span>
                <?php elseif ($isLocked): ?>
                    <span
                        style="background:#F5F5F5;color:#9E9E9E;font-size:0.75rem;padding:4px 12px;border-radius:50px;font-weight:700;">🔒
                        TERKUNCI</span>
                <?php else: ?>
                    <span
                        style="background:#FFF8E1;color:#F57F17;font-size:0.75rem;padding:4px 12px;border-radius:50px;font-weight:700;">🔓
                        AKTIF</span>
                <?php endif; ?>
            </div>

            <h3 style="font-size:1.1rem;margin-bottom:8px;">
                <?= sanitize($mod['nama_modul']) ?>
            </h3>
            <p style="color:#757575;font-size:0.9rem;line-height:1.5;margin-bottom:12px;">
                <?= sanitize($mod['deskripsi']) ?>
            </p>

            <div style="display:flex;gap:12px;font-size:0.8rem;color:#9E9E9E;margin-bottom:16px;">
                <span>📎
                    <?= $matCounts[$mod['id']] ?? 0 ?> Materi
                </span>
                <span>📜
                    <?= $qCounts[$mod['id']] ?? 0 ?> Soal
                </span>
                <span>🎯 Lulus:
                    <?= $mod['passing_score'] ?>%
                </span>
            </div>

            <?php if ($bestScore !== null): ?>
                <div style="margin-bottom:12px;">
                    <div style="font-size:0.8rem;color:#757575;margin-bottom:4px;">Skor Terbaik: <strong
                            style="color:<?= $isPassed ? '#1B5E20' : '#E53935' ?>">
                            <?= $bestScore ?>%
                        </strong></div>
                    <div style="background:#F0F0F0;border-radius:50px;height:6px;overflow:hidden;">
                        <div
                            style="background:<?= $isPassed ? '#4CAF50' : '#FF9800' ?>;height:100%;width:<?= $bestScore ?>%;border-radius:50px;">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isLocked): ?>
                <button class="btn btn-sm"
                    style="width:100%;justify-content:center;border:1px solid #E0E0E0;color:#9E9E9E;cursor:not-allowed;"
                    disabled>
                    Selesaikan modul sebelumnya
                </button>
            <?php else: ?>
<a href="<?= lmsUrl('/module.php?id=' . $mod['id']) ?>" class="btn btn-sm btn-primary"                    style="width:100%;justify-content:center;">
                    <?= $isPassed ? '🔄 Pelajari Lagi' : '📖 Mulai Belajar' ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/lms_footer.php'; ?>
