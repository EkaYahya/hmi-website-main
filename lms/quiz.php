<?php
/**
 * HMI IT Telkom - LMS QUIZ ENGINE
 * Randomized quiz with countdown timer
 */
require_once __DIR__ . '/../config/functions.php';
requireKader();

$moduleId = (int) ($_GET['module_id'] ?? 0);
if (!$moduleId)
    redirect(lmsUrl('/index.php'));

$pdo = getDB();
$userId = $_SESSION['user_id'];

// Fetch module
$stmt = $pdo->prepare("SELECT * FROM lms_modules WHERE id=?");
$stmt->execute([$moduleId]);
$module = $stmt->fetch();
if (!$module)
    redirect(lmsUrl('/index.php'));

// Fetch questions (randomized)
$stmt = $pdo->prepare("SELECT * FROM lms_questions WHERE module_id=? ORDER BY RAND()");
$stmt->execute([$moduleId]);
$questions = $stmt->fetchAll();

if (empty($questions)) {
    flash('error', 'Belum ada soal untuk modul ini.', 'error');
    redirect(lmsUrl('/module.php?id=' . $moduleId));
}

$totalQuestions = count($questions);
$timeLimitSeconds = $totalQuestions * 120; // 2 minutes per question

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        flash('error', 'Sesi tidak valid.', 'error');
        redirect(lmsUrl('/module.php?id=' . $moduleId));
    }

    $correct = 0;
    $answers = $_POST['answer'] ?? [];

    foreach ($questions as $q) {
        $qId = $q['id'];
        if (isset($answers[$qId]) && strtoupper($answers[$qId]) === strtoupper($q['jawaban_benar'])) {
            $correct++;
        }
    }

    $score = round(($correct / $totalQuestions) * 100);
    $passed = $score >= $module['passing_score'] ? 1 : 0;

    // Save score
    $stmt = $pdo->prepare("INSERT INTO lms_scores (user_id, module_id, skor, lulus) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $moduleId, $score, $passed]);

    // Show result page
    $pageTitle = 'Hasil Quiz — ' . $module['nama_modul'];
    require_once __DIR__ . '/../includes/lms_header.php';
    ?>

    <div style="max-width:600px;margin:0 auto;text-align:center;">
        <div
            style="background:<?= $passed ? 'linear-gradient(135deg,#0D3B13,#1B5E20)' : 'linear-gradient(135deg,#B71C1C,#E53935)' ?>;border-radius:16px;padding:48px;color:white;margin-bottom:24px;">
            <div style="font-size:4rem;margin-bottom:16px;">
                <?= $passed ? '🎉' : '😔' ?>
            </div>
            <h2 style="font-size:1.8rem;margin-bottom:8px;">
                <?= $passed ? 'Selamat! Anda Lulus!' : 'Belum Lulus' ?>
            </h2>
            <div style="font-size:3rem;font-weight:800;margin:16px 0;color:#FFD700;">
                <?= $score ?>%
            </div>
            <p style="opacity:0.8;">
                Benar:
                <?= $correct ?>/
                <?= $totalQuestions ?> · Passing:
                <?= $module['passing_score'] ?>%
            </p>
        </div>

        <div style="display:flex;gap:12px;justify-content:center;">
            <a href="<?= lmsUrl('/module.php?id=<?= $moduleId ?>') ?>" class="btn btn-primary">← Kembali ke Modul</a>
            <?php if (!$passed): ?>
                <a href="<?= lmsUrl('/quiz.php?module_id=<?= $moduleId ?>') ?>" class="btn btn-gold">🔄 Coba Lagi</a>
            <?php else: ?>
                <a href="<?= lmsUrl('/index.php') ?>" class="btn btn-gold">🏠 Dashboard LMS</a>
            <?php endif; ?>
        </div>
    </div>

    <?php
    require_once __DIR__ . '/../includes/lms_footer.php';
    exit;
}

$pageTitle = 'Quiz — ' . $module['nama_modul'];
require_once __DIR__ . '/../includes/lms_header.php';
?>

<!-- Quiz Header -->
<div
    style="background:white;border-radius:12px;padding:20px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
    <div>
        <h3 style="margin-bottom:4px;">📝
            <?= sanitize($module['nama_modul']) ?>
        </h3>
        <div style="font-size:0.85rem;color:#757575;">
            <?= $totalQuestions ?> soal · Passing:
            <?= $module['passing_score'] ?>%
        </div>
    </div>
    <div class="quiz-timer" id="quiz-timer" data-seconds="<?= $timeLimitSeconds ?>">
        ⏱️ <span id="timer-display">
            <?= floor($timeLimitSeconds / 60) ?>:00
        </span>
    </div>
</div>

<!-- Quiz Form -->
<form method="POST" action="" id="quizForm">
    <?= csrfField() ?>

    <?php foreach ($questions as $idx => $q): ?>
        <div class="quiz-question"
            style="background:white;border-radius:12px;padding:24px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex;gap:12px;margin-bottom:16px;">
                <span
                    style="background:#1B5E20;color:white;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;flex-shrink:0;">
                    <?= $idx + 1 ?>
                </span>
                <p style="font-weight:500;line-height:1.6;">
                    <?= sanitize($q['pertanyaan']) ?>
                </p>
            </div>

            <div class="quiz-options" style="display:flex;flex-direction:column;gap:8px;padding-left:44px;">
                <?php foreach (['A' => $q['opsi_a'], 'B' => $q['opsi_b'], 'C' => $q['opsi_c'], 'D' => $q['opsi_d']] as $letter => $option): ?>
                    <label class="quiz-option"
                        style="display:flex;align-items:center;gap:12px;padding:12px 16px;border:2px solid #E0E0E0;border-radius:10px;cursor:pointer;transition:all 0.2s;">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= $letter ?>" style="accent-color:#1B5E20;">
                        <span><strong>
                                <?= $letter ?>.
                            </strong>
                            <?= sanitize($option) ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div style="text-align:center;padding:24px 0;">
        <button type="submit" class="btn btn-primary btn-lg" id="submitQuiz">
            📤 Kumpulkan Jawaban
        </button>
    </div>
</form>

<script src="<?= asset('/assets/js/quiz.js') ?>"></script>

<?php require_once __DIR__ . '/../includes/lms_footer.php'; ?>
