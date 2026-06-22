<?php
/**
 * HMI IT Telkom - ADMIN DASHBOARD
 */
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/admin_header.php';

$pdo = getDB();

// Stats
$totalKader = $pdo->query("SELECT COUNT(*) FROM kader_profiles")->fetchColumn();
$pendingKader = $pdo->query("SELECT COUNT(*) FROM kader_profiles WHERE status_kaderisasi='pending'")->fetchColumn();
$totalBerita = $pdo->query("SELECT COUNT(*) FROM berita_events WHERE tipe_post='berita'")->fetchColumn();
$totalEvent = $pdo->query("SELECT COUNT(*) FROM berita_events WHERE tipe_post='event'")->fetchColumn();
$totalHotline = $pdo->query("SELECT COUNT(*) FROM hotline_messages WHERE status='baru'")->fetchColumn();

// Recent registrations
$recentKader = $pdo->query("SELECT kp.*, u.username, u.created_at FROM kader_profiles kp JOIN users u ON kp.user_id = u.id ORDER BY u.created_at DESC LIMIT 5")->fetchAll();

// Monthly registration data for chart (last 6 months)
$chartData = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total FROM kader_profiles GROUP BY bulan ORDER BY bulan DESC LIMIT 6")->fetchAll();
$chartData = array_reverse($chartData);
$chartLabels = array_map(fn($r) => $r['bulan'], $chartData);
$chartValues = array_map(fn($r) => (int) $r['total'], $chartData);
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">👥</div>
        <div class="stat-info">
            <h3>
                <?= $totalKader ?>
            </h3>
            <p>Total Kader</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-gold">⏳</div>
        <div class="stat-info">
            <h3>
                <?= $pendingKader ?>
            </h3>
            <p>Kader Pending</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📰</div>
        <div class="stat-info">
            <h3>
                <?= $totalBerita ?>
            </h3>
            <p>Total Berita</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">📬</div>
        <div class="stat-info">
            <h3>
                <?= $totalHotline ?>
            </h3>
            <p>Pesan Baru</p>
        </div>
    </div>
</div>

<!-- Chart & Recent Activity Row -->
<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:24px;">

    <!-- Chart -->
    <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="font-size:1.1rem;margin-bottom:20px;">📈 Pertumbuhan Kader</h3>
        <canvas id="kaderChart" height="200"></canvas>
    </div>

    <!-- Recent Registrations -->
    <div style="background:white;border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="font-size:1.1rem;margin-bottom:20px;">🆕 Pendaftaran Terbaru</h3>
        <?php if (empty($recentKader)): ?>
            <p style="color:#757575;font-size:0.9rem;">Belum ada pendaftaran.</p>
        <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <?php foreach ($recentKader as $rk): ?>
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F5F5F5;">
                        <div>
                            <strong style="font-size:0.9rem;">
                                <?= sanitize($rk['nama_lengkap']) ?>
                            </strong>
                            <div style="font-size:0.8rem;color:#757575;">
                                <?= sanitize($rk['program_studi']) ?> ·
                                <?= $rk['angkatan'] ?>
                            </div>
                        </div>
                        <span class="card-badge badge-<?= $rk['status_kaderisasi'] === 'pending' ? 'pending' : 'aktif' ?>">
                            <?= $rk['status_kaderisasi'] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Chart.js - Pertumbuhan Kader
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('kaderChart');
        if (ctx && typeof Chart !== 'undefined') {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chartLabels) ?>,
                    datasets: [{
                        label: 'Kader Baru',
                        data: <?= json_encode($chartValues) ?>,
                        borderColor: '#1B5E20',
                        backgroundColor: 'rgba(27, 94, 32, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#FFD700',
                        pointBorderColor: '#1B5E20',
                        pointRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>