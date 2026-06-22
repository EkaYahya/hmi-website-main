<?php
/**
 * HMI IT Telkom - EVENT
 */
$pageTitle = 'Event & Kegiatan';
$pageDescription = 'Agenda dan event HMI Komisariat IT Telkom — jadwal kegiatan, seminar, diskusi, pelatihan, dan acara organisasi mahasiswa Islam.';
$pageKeywords = 'event HMI, kegiatan HMI, agenda HMI IT Telkom, seminar HMI, pelatihan HMI, diskusi HMI, acara mahasiswa islam';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDB();
$events = $pdo->query("SELECT * FROM berita_events WHERE tipe_post='event' ORDER BY tanggal_pelaksanaan DESC")->fetchAll();
?>

<div style="height:72px;"></div>

<section style="background:linear-gradient(135deg,#0D3B13,#121212);padding:60px 24px;text-align:center;">
    <h1 style="color:white;font-size:2.5rem;margin-bottom:12px;">📅 Event & <span style="color:#FFD700;">Kegiatan</span>
    </h1>
    <p style="color:rgba(255,255,255,0.7);max-width:600px;margin:0 auto;">Jadwal kegiatan, seminar, lokakarya, dan
        pelatihan kader HMI Komisariat IT Telkom.</p>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($events)): ?>
            <div class="text-center" style="padding:60px 0;">
                <div style="font-size:3rem;margin-bottom:16px;">📅</div>
                <h3 style="color:#757575;">Belum ada event terjadwal.</h3>
            </div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($events as $ev): ?>
                    <div class="card">
                        <div class="card-body">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                <span class="card-badge badge-event">Event</span>
                                <?php if ($ev['tanggal_pelaksanaan'] && strtotime($ev['tanggal_pelaksanaan']) >= strtotime('today')): ?>
                                    <span
                                        style="font-size:0.75rem;background:#E8F5E9;color:#1B5E20;padding:4px 10px;border-radius:50px;font-weight:600;">Mendatang</span>
                                <?php else: ?>
                                    <span
                                        style="font-size:0.75rem;background:#F5F5F5;color:#757575;padding:4px 10px;border-radius:50px;font-weight:600;">Selesai</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="card-title">
                                <?= sanitize($ev['judul']) ?>
                            </h3>
                            <?php if ($ev['tanggal_pelaksanaan']): ?>
                                <div
                                    style="display:flex;align-items:center;gap:8px;color:#1B5E20;font-weight:600;font-size:0.9rem;margin:8px 0;">
                                    📅
                                    <?= formatTanggal($ev['tanggal_pelaksanaan']) ?>
                                </div>
                            <?php endif; ?>
                            <p class="card-text">
                                <?= truncateText($ev['konten_teks'], 150) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>