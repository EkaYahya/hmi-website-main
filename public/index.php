<?php
/**
 * HMI IT Telkom - BERANDA (Homepage)
 * Rich, informative homepage with HMI identity sections
 */
$pageTitle = 'Beranda';
$pageDescription = 'Selamat datang di HMI Komisariat IT Telkom — portal resmi Himpunan Mahasiswa Islam di kampus Telkom University Bandung. Informasi kegiatan, berita, kaderisasi, dan organisasi mahasiswa Islam terbesar di Indonesia.';
$pageKeywords = 'HMI, himpunan mahasiswa islam, HMI IT Telkom, hmi komisariat, hmi bandung, organisasi mahasiswa islam, beranda HMI, kegiatan HMI, kaderisasi mahasiswa islam, NDP HMI, LK-1 HMI, himpunan mahasiswa islam it telkom, hmi cabang bandung, hmi';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDB();

// Fetch counters
$totalKader = $pdo->query("SELECT COUNT(*) FROM kader_profiles")->fetchColumn();
$totalBerita = $pdo->query("SELECT COUNT(*) FROM berita_events WHERE tipe_post='berita'")->fetchColumn();
$totalEvent = $pdo->query("SELECT COUNT(*) FROM berita_events WHERE tipe_post='event'")->fetchColumn();

// Fetch latest berita
$latestBerita = $pdo->query("SELECT * FROM berita_events WHERE tipe_post='berita' ORDER BY created_at DESC LIMIT 3")->fetchAll();

// Fetch upcoming events
$upcomingEvents = $pdo->query("SELECT * FROM berita_events WHERE tipe_post='event' ORDER BY tanggal_pelaksanaan DESC LIMIT 4")->fetchAll();
?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="hero-orb hero-orb-3"></div>

    <div class="hero-content">
        <div class="hero-badge">
            Himpunan Mahasiswa Islam — Sejak 1947
        </div>
        <h1>Rumah Bagi<br><span>Intelektual Muslim</span></h1>
        <p>HMI Komisariat IT Telkom berkomitmen mempertahankan semangat keindonesiaan dan keislaman, membina generasi
            akademis, pencipta, dan pengabdi di lingkungan kampus teknologi.</p>
        <div class="hero-buttons">
            <a href="<?= url('/daftar-kader') ?>" class="btn btn-gold btn-lg">🎓 Daftar Kader Baru</a>
            <a href="<?= url('/profil') ?>" class="btn btn-secondary btn-lg">📋 Profil Organisasi</a>
        </div>
    </div>
</section>

<!-- COUNTER SECTION -->
<section class="counter-section">
    <div class="counter-grid">
        <div class="counter-card">
            <div class="counter-icon" style="background:#E8F5E9;color:#1B5E20;">👥</div>
            <div class="counter-number" data-count="<?= $totalKader ?>">0</div>
            <div class="counter-label">Kader Terdaftar</div>
        </div>
        <div class="counter-card">
            <div class="counter-icon" style="background:#E3F2FD;color:#1565C0;">📰</div>
            <div class="counter-number" data-count="<?= $totalBerita ?>">0</div>
            <div class="counter-label">Artikel & Berita</div>
        </div>
        <div class="counter-card">
            <div class="counter-icon" style="background:#FFF8E1;color:#F57F17;">📅</div>
            <div class="counter-number" data-count="<?= $totalEvent ?>">0</div>
            <div class="counter-label">Kegiatan Terselenggara</div>
        </div>
        <div class="counter-card">
            <div class="counter-icon" style="background:#FCE4EC;color:#C62828;">📚</div>
            <div class="counter-number" data-count="5">0</div>
            <div class="counter-label">Modul LMS Wajib</div>
        </div>
    </div>
</section>

<!-- TENTANG HMI -->
<section class="section" style="padding:80px 24px;">
    <div class="container" style="max-width:1000px;">
        <div class="reveal" style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center;">
            <div>
                <span
                    style="color:#1B5E20;font-weight:700;font-size:0.78rem;text-transform:uppercase;letter-spacing:2px;">Tentang
                    Kami</span>
                <h2 style="font-size:2rem;margin:12px 0 20px;letter-spacing:-0.02em;line-height:1.2;">Organisasi
                    Mahasiswa<br>Tertua di Indonesia</h2>
                <p style="color:#616161;line-height:1.9;margin-bottom:16px;">
                    <strong>Himpunan Mahasiswa Islam (HMI)</strong> didirikan pada <strong>5 Februari 1947</strong> di
                    Yogyakarta oleh <strong>Lafran Pane</strong>. Sebagai organisasi kemahasiswaan tertua di Indonesia,
                    HMI bertujuan membentuk <em>insan akademis, pencipta, dan pengabdi</em> yang bernafaskan Islam.
                </p>
                <p style="color:#616161;line-height:1.9;">
                    HMI Komisariat IT Telkom hadir sebagai ujung tombak kaderisasi intelektual di kampus teknologi,
                    mencetak pemangku kepemimpinan masa depan yang memiliki keseimbangan antara kompetensi digital,
                    keislaman, dan kebangsaan.
                </p>
                <a href="<?= url('/sejarah') ?>" class="btn btn-primary" style="margin-top:20px;">📖 Pelajari Sejarah
                    Kami</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div
                    style="background:linear-gradient(135deg,#0D3B13,#1B5E20);border-radius:20px;padding:32px;color:white;position:relative;overflow:hidden;">
                    <div
                        style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,215,0,0.1);">
                    </div>
                    <div style="margin-bottom:12px;">
                        <?php $homeLogo = getSetting('logo_image'); ?>
                        <?php if ($homeLogo): ?>
                            <img src="<?= asset('/') ?><?= $homeLogo ?>" alt="Logo HMI"
                                style="height:56px;width:56px;object-fit:contain;border-radius:10px;">
                        <?php else: ?>
                            <span style="font-size:2.5rem;">☪️</span>
                        <?php endif; ?>
                    </div>
                    <h3 style="color:#FFD700;font-size:1.1rem;margin-bottom:8px;">Asas Islam</h3>
                    <p style="color:rgba(255,255,255,0.7);font-size:0.85rem;line-height:1.7;">Menempatkan Islam sebagai
                        sumber nilai, landasan teologis, dan panduan perjuangan untuk mewujudkan masyarakat adil makmur
                        yang diridhoi Allah SWT. Diinternalisasi melalui NDP dengan pendekatan integralistik,
                        transcendental, humanis, dan inklusif.</p>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="background:#E8F5E9;border-radius:16px;padding:24px;text-align:center;">
                        <div style="font-size:1.5rem;margin-bottom:8px;">🇮🇩</div>
                        <strong style="color:#1B5E20;font-size:0.85rem;">Keindonesiaan</strong>
                    </div>
                    <div style="background:#FFF8E1;border-radius:16px;padding:24px;text-align:center;">
                        <div style="font-size:1.5rem;margin-bottom:8px;">☪️</div>
                        <strong style="color:#E65100;font-size:0.85rem;">Keislaman</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- KALENDER EVENT -->
<?php
// Calendar logic
$calMonth = isset($_GET['cm']) ? (int) $_GET['cm'] : (int) date('n');
$calYear = isset($_GET['cy']) ? (int) $_GET['cy'] : (int) date('Y');
if ($calMonth < 1) {
    $calMonth = 12;
    $calYear--;
}
if ($calMonth > 12) {
    $calMonth = 1;
    $calYear++;
}

$firstDay = mktime(0, 0, 0, $calMonth, 1, $calYear);
$daysInMonth = (int) date('t', $firstDay);
$startDay = (int) date('w', $firstDay); // 0=Sun
$monthName = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$calMonth];
$today = (int) date('j');
$todayMonth = (int) date('n');
$todayYear = (int) date('Y');

// Fetch events for this month
$stmtCal = $pdo->prepare("SELECT id, judul, tanggal_pelaksanaan FROM berita_events WHERE tipe_post='event' AND tanggal_pelaksanaan IS NOT NULL AND MONTH(tanggal_pelaksanaan)=? AND YEAR(tanggal_pelaksanaan)=? ORDER BY tanggal_pelaksanaan ASC");
$stmtCal->execute([$calMonth, $calYear]);
$calEvents = $stmtCal->fetchAll();

// Map events by day
$eventsByDay = [];
foreach ($calEvents as $ev) {
    $d = (int) date('j', strtotime($ev['tanggal_pelaksanaan']));
    $eventsByDay[$d][] = $ev;
}

$prevMonth = $calMonth - 1;
$prevYear = $calYear;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}
$nextMonth = $calMonth + 1;
$nextYear = $calYear;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}
?>

<section class="section" style="padding:80px 24px;background:#FAFAFA;">
    <div class="container" style="max-width:1000px;">
        <div class="section-header reveal">
            <h2>Kalender Kegiatan</h2>
            <p>Jadwal kegiatan, seminar, lokakarya, dan pelatihan kader</p>
            <div class="section-divider"></div>
        </div>

        <div class="cal-wrapper reveal">
            <!-- CALENDAR GRID -->
            <div class="cal-box">
                <div class="cal-nav">
                    <a href="?cm=<?= $prevMonth ?>&cy=<?= $prevYear ?>#kalender" class="cal-nav-btn"
                        aria-label="Bulan sebelumnya">‹</a>
                    <h3 class="cal-month">
                        <?= $monthName ?>
                        <?= $calYear ?>
                    </h3>
                    <a href="?cm=<?= $nextMonth ?>&cy=<?= $nextYear ?>#kalender" class="cal-nav-btn"
                        aria-label="Bulan berikutnya">›</a>
                </div>

                <div class="cal-grid">
                    <div class="cal-head">Min</div>
                    <div class="cal-head">Sen</div>
                    <div class="cal-head">Sel</div>
                    <div class="cal-head">Rab</div>
                    <div class="cal-head">Kam</div>
                    <div class="cal-head">Jum</div>
                    <div class="cal-head">Sab</div>

                    <?php
                    // Empty cells before first day
                    for ($i = 0; $i < $startDay; $i++): ?>
                        <div class="cal-day cal-empty"></div>
                    <?php endfor;

                    // Day cells
                    for ($d = 1; $d <= $daysInMonth; $d++):
                        $isToday = ($d === $today && $calMonth === $todayMonth && $calYear === $todayYear);
                        $hasEvent = isset($eventsByDay[$d]);
                        $classes = 'cal-day';
                        if ($isToday)
                            $classes .= ' cal-today';
                        if ($hasEvent)
                            $classes .= ' cal-has-event';
                        ?>
                        <div class="<?= $classes ?>">
                            <span class="cal-num">
                                <?= $d ?>
                            </span>
                            <?php if ($hasEvent): ?>
                                <span class="cal-dot"></span>
                                <div class="cal-tooltip">
                                    <?php foreach ($eventsByDay[$d] as $ev): ?>
                                        <div class="cal-tip-item">📌
                                            <?= sanitize($ev['judul']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="cal-legend">
                    <span><span class="cal-dot-legend cal-dot-legend-today"></span> Hari ini</span>
                    <span><span class="cal-dot-legend cal-dot-legend-event"></span> Ada kegiatan</span>
                </div>
            </div>

            <!-- EVENT LIST -->
            <div class="cal-events" id="kalender">
                <h4 class="cal-events-title">📅 Kegiatan
                    <?= $monthName ?>
                </h4>
                <?php if (empty($calEvents)): ?>
                    <div class="cal-no-event">
                        <div style="font-size:2rem;margin-bottom:8px;">📭</div>
                        <p>Belum ada kegiatan di bulan ini.</p>
                    </div>
                <?php else: ?>
                    <div class="cal-event-list">
                        <?php foreach ($calEvents as $ev):
                            $evDate = strtotime($ev['tanggal_pelaksanaan']);
                            $isPast = $evDate < strtotime('today');
                            ?>
                            <div class="cal-event-item <?= $isPast ? 'cal-event-past' : '' ?>">
                                <div class="cal-event-date">
                                    <span class="cal-event-day">
                                        <?= date('d', $evDate) ?>
                                    </span>
                                    <span class="cal-event-mon">
                                        <?= substr($monthName, 0, 3) ?>
                                    </span>
                                </div>
                                <div class="cal-event-info">
                                    <strong>
                                        <?= sanitize($ev['judul']) ?>
                                    </strong>
                                    <span>
                                        <?= $isPast ? '✓ Selesai' : '⏳ Akan datang' ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
    /* ====== CALENDAR WIDGET ====== */
    .cal-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .cal-box {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #F0F0F0;
    }

    .cal-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .cal-nav-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #F5F5F5;
        color: #333;
        font-size: 1.2rem;
        font-weight: 700;
        transition: all 0.2s;
        text-decoration: none;
    }

    .cal-nav-btn:hover {
        background: #1B5E20;
        color: white;
    }

    .cal-month {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1B5E20;
        letter-spacing: -0.01em;
    }

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .cal-head {
        text-align: center;
        font-size: 0.7rem;
        font-weight: 600;
        color: #9E9E9E;
        padding: 6px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cal-day {
        position: relative;
        text-align: center;
        padding: 8px 4px;
        border-radius: 10px;
        cursor: default;
        transition: all 0.2s;
        min-height: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .cal-empty {
        background: none;
    }

    .cal-num {
        font-size: 0.88rem;
        font-weight: 500;
        color: #424242;
    }

    .cal-today {
        background: #1B5E20;
        border-radius: 10px;
    }

    .cal-today .cal-num {
        color: white;
        font-weight: 700;
    }

    .cal-has-event {
        cursor: pointer;
    }

    .cal-has-event:hover {
        background: #E8F5E9;
        transform: scale(1.08);
    }

    .cal-today.cal-has-event:hover {
        background: #2E7D32;
    }

    .cal-dot {
        display: block;
        width: 5px;
        height: 5px;
        background: #FF9800;
        border-radius: 50%;
        margin-top: 2px;
    }

    .cal-today .cal-dot {
        background: #FFD700;
    }

    /* Tooltip */
    .cal-tooltip {
        display: none;
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        background: #1a1a1a;
        color: white;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 0.78rem;
        white-space: nowrap;
        z-index: 50;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        text-align: left;
        min-width: 140px;
    }

    .cal-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1a1a1a;
    }

    .cal-has-event:hover .cal-tooltip {
        display: block;
    }

    .cal-tip-item {
        padding: 3px 0;
        line-height: 1.4;
    }

    .cal-tip-item+.cal-tip-item {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 4px;
        padding-top: 6px;
    }

    /* Legend */
    .cal-legend {
        display: flex;
        gap: 20px;
        justify-content: center;
        margin-top: 16px;
        font-size: 0.75rem;
        color: #9E9E9E;
    }

    .cal-legend span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .cal-dot-legend {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .cal-dot-legend-today {
        background: #1B5E20;
    }

    .cal-dot-legend-event {
        background: #FF9800;
    }

    /* Event list */
    .cal-events {
        background: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #F0F0F0;
    }

    .cal-events-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #212121;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #F0F0F0;
    }

    .cal-no-event {
        text-align: center;
        padding: 32px 16px;
        color: #BDBDBD;
    }

    .cal-no-event p {
        font-size: 0.9rem;
    }

    .cal-event-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 320px;
        overflow-y: auto;
    }

    .cal-event-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px;
        border-radius: 12px;
        background: #FAFAFA;
        border: 1px solid #F0F0F0;
        transition: all 0.2s;
    }

    .cal-event-item:hover {
        background: #E8F5E9;
        border-color: #C8E6C9;
    }

    .cal-event-past {
        opacity: 0.55;
    }

    .cal-event-date {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #1B5E20, #4CAF50);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .cal-event-day {
        font-size: 1.1rem;
        font-weight: 800;
        color: white;
        line-height: 1;
    }

    .cal-event-mon {
        font-size: 0.6rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cal-event-info strong {
        display: block;
        font-size: 0.9rem;
        color: #212121;
        margin-bottom: 2px;
    }

    .cal-event-info span {
        font-size: 0.78rem;
        color: #9E9E9E;
    }

    /* Calendar Responsive */
    @media (max-width: 768px) {
        .cal-wrapper {
            grid-template-columns: 1fr;
        }

        .cal-box {
            padding: 20px 16px;
        }

        .cal-events {
            padding: 20px 16px;
        }

        .cal-day {
            padding: 6px 2px;
            min-height: 36px;
        }

        .cal-num {
            font-size: 0.82rem;
        }

        .cal-tooltip {
            left: 0;
            right: 0;
            transform: none;
            white-space: normal;
            min-width: auto;
        }

        .cal-tooltip::after {
            left: 30%;
        }
    }

    @media (max-width: 480px) {
        .cal-day {
            padding: 5px 1px;
            min-height: 32px;
        }

        .cal-head {
            font-size: 0.65rem;
        }

        .cal-num {
            font-size: 0.78rem;
        }

        .cal-event-item {
            padding: 10px;
            gap: 10px;
        }

        .cal-event-date {
            width: 42px;
            height: 42px;
        }

        .cal-event-day {
            font-size: 0.95rem;
        }
    }
</style>

<!-- NILAI-NILAI DASAR PERJUANGAN (NDP) -->
<section class="section" style="padding:80px 24px;background:#FAFAFA;">
    <div class="container" style="max-width:780px;">
        <div class="section-header reveal">
            <h2>Nilai-Nilai Dasar Perjuangan (NDP)</h2>
            <p>Tujuh bab yang menjadi fondasi ideologis pergerakan HMI</p>
            <div class="section-divider"></div>
        </div>

        <div class="ndp-flow reveal">
            <?php
            $ndp = [
                ['📖', 'Bab I', 'Dasar-Dasar Kepercayaan', 'Menekankan pada tauhid, fitrah manusia yang memerlukan Tuhan, dan kesadaran bahwa manusia harus bertuhan kepada Tuhan Yang Maha Esa (Allah SWT).', '#1B5E20'],
                ['👤', 'Bab II', 'Pengertian Dasar Kemanusiaan', 'Manusia dipandang sebagai khalifah di bumi yang memiliki potensi baik, serta memiliki tanggung jawab kemanusiaan.', '#1565C0'],
                ['🕊️', 'Bab III', 'Kemerdekaan Manusia & Keharusan Universal', 'Menekankan kebebasan individu dalam bertindak (ikhtiar) namun tetap terikat pada hukum alam (Taqdir Ilahi).', '#00695C'],
                ['🤝', 'Bab IV', 'Ketuhanan Yang Maha Esa & Kemanusiaan', 'Keselarasan antara keimanan kepada Tuhan dan pengabdian kepada nilai-nilai kemanusiaan.', '#6A1B9A'],
                ['⚖️', 'Bab V', 'Individu & Masyarakat', 'Menekankan keseimbangan antara kepentingan individu dan tanggung jawab sosial, serta pentingnya gotong royong.', '#E65100'],
                ['🏛️', 'Bab VI', 'Keadilan Sosial & Ekonomi', 'Perjuangan untuk menciptakan masyarakat yang adil, makmur, dan egaliter.', '#C62828'],
                ['🎓', 'Bab VII', 'Kemanusiaan & Ilmu Pengetahuan', 'Pentingnya mengembangkan ilmu pengetahuan untuk mempertinggi derajat manusia.', '#1565C0'],
            ];
            foreach ($ndp as $i => $n):
                ?>
                <div class="ndp-step">
                    <div class="ndp-node" style="background:<?= $n[4] ?>;">
                        <span><?= $i + 1 ?></span>
                    </div>
                    <?php if ($i < count($ndp) - 1): ?>
                        <div class="ndp-connector"
                            style="background:linear-gradient(to bottom, <?= $n[4] ?>, <?= $ndp[$i + 1][4] ?>);"></div>
                    <?php endif; ?>
                    <div class="ndp-card">
                        <div class="ndp-icon" style="background:<?= $n[4] ?>12;color:<?= $n[4] ?>;"><?= $n[0] ?></div>
                        <div class="ndp-content">
                            <div class="ndp-bab" style="color:<?= $n[4] ?>;"><?= $n[1] ?></div>
                            <h4 class="ndp-title"><?= $n[2] ?></h4>
                            <p class="ndp-desc"><?= $n[3] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    /* ====== NDP FLOWCHART ====== */
    .ndp-flow {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .ndp-step {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 20px;
        padding-bottom: 0;
    }

    .ndp-node {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        z-index: 2;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        position: relative;
    }

    .ndp-node span {
        color: white;
        font-weight: 800;
        font-size: 0.85rem;
        font-family: 'Outfit', sans-serif;
    }

    .ndp-connector {
        position: absolute;
        left: 19px;
        top: 40px;
        width: 2px;
        height: calc(100% - 40px);
        z-index: 1;
        border-radius: 1px;
    }

    .ndp-card {
        flex: 1;
        background: white;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #F0F0F0;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
        transition: all 0.3s ease;
    }

    .ndp-card:hover {
        transform: translateX(4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        border-color: #E0E0E0;
    }

    .ndp-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .ndp-content {
        flex: 1;
    }

    .ndp-bab {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
    }

    .ndp-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #212121;
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .ndp-desc {
        font-size: 0.85rem;
        color: #757575;
        line-height: 1.7;
    }

    @media (max-width: 768px) {
        .ndp-node {
            width: 34px;
            height: 34px;
        }

        .ndp-node span {
            font-size: 0.78rem;
        }

        .ndp-connector {
            left: 16px;
            top: 34px;
            height: calc(100% - 34px);
        }

        .ndp-card {
            padding: 16px;
            gap: 12px;
        }

        .ndp-icon {
            width: 38px;
            height: 38px;
            font-size: 1rem;
        }

        .ndp-title {
            font-size: 0.88rem;
        }

        .ndp-desc {
            font-size: 0.82rem;
        }

        .ndp-step {
            gap: 14px;
        }
    }

    @media (max-width: 480px) {
        .ndp-node {
            width: 30px;
            height: 30px;
        }

        .ndp-node span {
            font-size: 0.72rem;
        }

        .ndp-connector {
            left: 14px;
            top: 30px;
            height: calc(100% - 30px);
        }

        .ndp-card {
            flex-direction: column;
            gap: 10px;
            padding: 14px;
            margin-bottom: 12px;
        }

        .ndp-card:hover {
            transform: none;
        }

        .ndp-step {
            gap: 10px;
        }
    }
</style>

<!-- KADERISASI -->
<section class="section section-dark" style="padding:80px 24px;">
    <div class="container" style="max-width:1000px;">
        <div class="section-header reveal">
            <h2>Jenjang Kaderisasi</h2>
            <p style="color:rgba(255,255,255,0.5);">Proses pembinaan kader yang terstruktur dan berjenjang</p>
            <div class="section-divider"></div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;"
            class="reveal kader-grid">
            <?php
            $kaderisasi = [
                ['LK-1', 'Latihan Kader 1', 'Basic Training', 'Pengenalan dasar organisasi, NDP, dan nilai-nilai keislaman HMI.', '#4CAF50', '3 Hari'],
                ['LK-2', 'Latihan Kader 2', 'Intermediate Training', 'Pendalaman ideologi, analisis sosial, dan kepemimpinan organisasi.', '#1565C0', '4 Hari'],
                ['LK-3', 'Latihan Kader 3', 'Senior Course', 'Pemantapan posisi ideologis dan kesiapan memimpin pergerakan.', '#6A1B9A', '5 Hari'],
                ['SC', 'Senior Course', 'Advanced', 'Pematangan wawasan geopolitik, dakwah strategis, dan advokasi kebijakan.', '#C62828', '7+ Hari'],
            ];
            foreach ($kaderisasi as $k):
                ?>
                <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:28px 24px;transition:all 0.3s ease;position:relative;overflow:hidden;"
                    onmouseenter="this.style.background='rgba(255,255,255,0.08)';this.style.transform='translateY(-4px)'"
                    onmouseleave="this.style.background='rgba(255,255,255,0.04)';this.style.transform='translateY(0)'">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:<?= $k[4] ?>;"></div>
                    <div
                        style="display:inline-block;background:<?= $k[4] ?>;color:white;padding:4px 12px;border-radius:6px;font-size:0.75rem;font-weight:700;margin-bottom:14px;">
                        <?= $k[0] ?>
                    </div>
                    <h3 style="color:white;font-size:1rem;margin-bottom:4px;"><?= $k[1] ?></h3>
                    <div style="font-size:0.75rem;color:<?= $k[4] ?>;font-weight:600;margin-bottom:10px;"><?= $k[2] ?> ·
                        <?= $k[5] ?>
                    </div>
                    <p style="font-size:0.85rem;color:rgba(255,255,255,0.45);line-height:1.7;"><?= $k[3] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- BIDANG KERJA -->
<section class="section" style="padding:80px 24px;">
    <div class="container" style="max-width:1000px;">
        <div class="section-header reveal">
            <h2>Bidang Kerja</h2>
            <p>Enam pilar penggerak roda organisasi</p>
            <div class="section-divider"></div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;"
            class="reveal bidang-home-grid">
            <?php
            $bidangHome = [
                ['📋', 'Pembinaan Anggota', 'Mengelola proses kaderisasi formal dan non-formal, pembinaan karakter, dan pengembangan kapasitas seluruh kader.', '#1B5E20'],
                ['📣', 'PTKP', 'Perguruan Tinggi, Kemahasiswaan, dan Kepemudaan — mengawal isu kampus, pendidikan, dan gerakan kepemudaan.', '#2196F3'],
                ['📚', 'KAIL', 'Kajian dan Aksi Intelektual — forum diskusi ilmiah, bedah buku, dan penguatan wawasan kritis kader.', '#9C27B0'],
                ['💼', 'KPP', 'Kewirausahaan dan Pengembangan Profesi — inkubasi bisnis, workshop skill, dan pengembangan karier kader.', '#FF9800'],
                ['👩', 'PP', 'Pemberdayaan Perempuan — memperjuangkan kesetaraan gender, leadership perempuan, dan advokasi hak-hak perempuan.', '#E91E63'],
                ['🌐', 'Media & IT', 'Pengelolaan platform digital, konten kreatif, dokumentasi, dan teknologi informasi organisasi.', '#00BCD4'],
            ];
            foreach ($bidangHome as $b):
                ?>
                <div style="background:white;border-radius:16px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.04);border:1px solid #F0F0F0;display:flex;gap:16px;transition:all 0.3s ease;"
                    onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.08)'"
                    onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'">
                    <div
                        style="width:44px;height:44px;background:<?= $b[3] ?>10;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                        <?= $b[0] ?>
                    </div>
                    <div>
                        <h4 style="font-size:0.95rem;margin-bottom:6px;color:<?= $b[3] ?>;"><?= $b[1] ?></h4>
                        <p style="font-size:0.85rem;color:#757575;line-height:1.7;"><?= $b[2] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- BERITA TERBARU -->
<section class="section" style="padding:80px 24px;background:#FAFAFA;">
    <div class="container">
        <div class="section-header reveal">
            <h2>Berita Terbaru</h2>
            <p>Ikuti perkembangan terkini seputar kegiatan dan pemikiran kader HMI Komisariat IT Telkom.</p>
            <div class="section-divider"></div>
        </div>

        <div class="card-grid reveal">
            <?php if (empty($latestBerita)): ?>
                <div class="text-center text-muted" style="grid-column:1/-1;padding:40px;">
                    <p style="font-size:1.1rem;">📰 Belum ada berita. Konten akan segera hadir.</p>
                </div>
            <?php else: ?>
                <?php foreach ($latestBerita as $berita): ?>
                    <div class="card">
                        <div class="card-img"
                            style="<?= $berita['path_gambar'] ? "background-image:url('<?= asset('/') ?>{$berita['path_gambar']}');background-size:cover;background-position:center;" : '' ?>display:flex;align-items:center;justify-content:center;font-size:3rem;">
                            <?= $berita['path_gambar'] ? '' : '📰' ?>
                        </div>
                        <div class="card-body">
                            <span class="card-badge badge-berita">Berita</span>
                            <h3 class="card-title">
                                <a href="<?= url('/berita/detail') ?>?slug=<?= urlencode($berita['slug']) ?>">
                                    <?= sanitize($berita['judul']) ?>
                                </a>
                            </h3>
                            <p class="card-text"><?= truncateText($berita['konten_teks'], 120) ?></p>
                            <div class="card-meta">
                                <span>🕐 <?= timeAgo($berita['created_at']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="text-center mt-6">
            <a href="<?= url('/berita') ?>" class="btn btn-primary">Lihat Semua Berita →</a>
        </div>
    </div>
</section>


<!-- CTA SECTION -->
<section class="section section-dark" style="text-align:center;padding:80px 24px;">
    <div class="container" style="max-width:700px;">
        <div class="reveal">
            <div style="font-size:3rem;margin-bottom:16px;">🚀</div>
            <h2 style="font-size:2rem;margin-bottom:16px;color:white;">Siap Menjadi Bagian dari HMI?</h2>
            <p
                style="color:rgba(255,255,255,0.6);font-size:1.05rem;max-width:600px;margin:0 auto 32px;line-height:1.8;">
                Bergabunglah dengan ribuan kader intelektual muslim yang telah membentuk karakter kepemimpinan, wawasan
                keislaman, dan keahlian profesional melalui kaderisasi formal HMI.
            </p>
            <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;" class="hero-buttons">
                <a href="<?= url('/daftar-kader') ?>" class="btn btn-gold btn-lg">Daftar Sekarang 🎓</a>
                <a href="<?= url('/hotline') ?>" class="btn btn-secondary btn-lg">Hubungi Kami 💬</a>
            </div>
        </div>
    </div>
</section>

<style>
    /* Responsive overrides for homepage grids */
    @media (max-width: 768px) {
        .reveal[style*="grid-template-columns:1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }

        .ndp-grid {
            grid-template-columns: 1fr 1fr !important;
        }

        .kader-grid {
            grid-template-columns: 1fr !important;
        }

        .bidang-home-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 480px) {
        .ndp-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
