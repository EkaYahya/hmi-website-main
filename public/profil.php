<?php
/**
 * HMI IT Telkom - PROFIL ORGANISASI
 * Dynamic organigram from database
 */
$pageTitle = 'Profil Organisasi';
$pageDescription = 'Profil HMI Komisariat IT Telkom — struktur kepengurusan, hierarki organisasi, visi misi, dan bidang kerja Himpunan Mahasiswa Islam di kampus IT Telkom Surabaya.';
$pageKeywords = 'profil HMI, kepengurusan HMI IT Telkom, struktur organisasi HMI, hierarki HMI, bidang kerja HMI, visi misi HMI, himpunan mahasiswa islam profil, organigram HMI';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDB();
$defaultPeriode = getSetting('periode_aktif', '2024-2025');

// Get all available periodes for the dropdown
$allPeriodes = $pdo->query("SELECT DISTINCT periode FROM pengurus WHERE is_active=1 ORDER BY periode DESC")->fetchAll(PDO::FETCH_COLUMN);

// Check if a specific periode was requested via URL
$periodeAktif = isset($_GET['periode']) && in_array($_GET['periode'], $allPeriodes) ? $_GET['periode'] : $defaultPeriode;

$stmt = $pdo->prepare("SELECT * FROM pengurus WHERE periode=? AND is_active=1 ORDER BY FIELD(level,'top','pao','middle','staff'), urutan");
$stmt->execute([$periodeAktif]);
$allPengurus = $stmt->fetchAll();

$top = array_values(array_filter($allPengurus, fn($p) => $p['level'] === 'top'));
$pao = array_values(array_filter($allPengurus, fn($p) => $p['level'] === 'pao'));
$middle = array_values(array_filter($allPengurus, fn($p) => $p['level'] === 'middle'));
$staff = array_values(array_filter($allPengurus, fn($p) => $p['level'] === 'staff'));

// Separate Ketua Umum from Sekjend & Bendahara
$ketuaUmum = null;
$wakil = [];
foreach ($top as $t) {
    if (stripos($t['jabatan'], 'Ketua Umum') !== false) {
        $ketuaUmum = $t;
    } else {
        $wakil[] = $t;
    }
}

// Group middle by bidang
$bidangGroups = [];
foreach ($middle as $m) {
    $bidangGroups[$m['bidang']][] = $m;
}
$staffGroups = [];
foreach ($staff as $s) {
    $staffGroups[$s['bidang']][] = $s;
}

// Get unique bidang list
$bidangList = array_keys($bidangGroups);
$bidangColors = ['#1565C0', '#2E7D32', '#6A1B9A', '#C62828', '#E65100', '#00838F', '#AD1457', '#283593'];
?>

<style>
    .profil-hero {
        min-height: 50vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(160deg, #0a2e10 0%, #121212 50%, #0D3B13 100%);
        padding: 120px 24px 80px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .profil-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 600px 400px at 20% 30%, rgba(76, 175, 80, 0.12), transparent),
            radial-gradient(ellipse 500px 350px at 80% 70%, rgba(255, 215, 0, 0.06), transparent);
        pointer-events: none;
    }

    .profil-hero .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 8px 22px;
        border-radius: 50px;
        color: #FFD700;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 28px;
        backdrop-filter: blur(4px);
    }

    .profil-hero h1 {
        color: white;
        font-size: clamp(2.2rem, 5vw, 3.2rem);
        margin-bottom: 18px;
        letter-spacing: -0.03em;
        line-height: 1.15;
    }

    .profil-hero h1 .accent {
        background: linear-gradient(135deg, #FFD700 0%, #FFA000 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .profil-hero p {
        color: rgba(255, 255, 255, 0.55);
        font-size: 1.05rem;
        line-height: 1.8;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Organigram */
    .org-section {
        padding: 100px 24px;
        position: relative;
    }

    .org-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 1px;
        height: 100%;
        background: linear-gradient(to bottom, #E0E0E0, transparent);
        opacity: 0.5;
        z-index: 0;
    }

    .org-header {
        text-align: center;
        margin-bottom: 60px;
        position: relative;
        z-index: 1;
    }

    .org-header h2 {
        font-size: 2rem;
        letter-spacing: -0.02em;
        margin-bottom: 8px;
    }

    .org-header p {
        color: #757575;
        font-size: 1rem;
    }

    .org-header .line {
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #1B5E20, #FFD700);
        margin: 16px auto 0;
        border-radius: 2px;
    }

    /* Ketua Umum - Apex */
    .org-apex {
        text-align: center;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }

    .org-apex .avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        font-weight: 800;
        background: linear-gradient(135deg, #1B5E20, #2E7D32);
        border: 5px solid #E8F5E9;
        box-shadow: 0 8px 32px rgba(27, 94, 32, 0.35), 0 0 0 8px rgba(27, 94, 32, 0.08);
    }

    .org-apex .avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .org-apex h3 {
        font-size: 1.25rem;
        margin-bottom: 6px;
    }

    .org-apex .role {
        display: inline-block;
        background: linear-gradient(135deg, #FFD700, #FFA000);
        color: #121212;
        padding: 5px 20px;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* Connector lines */
    .org-connector {
        display: flex;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .org-vline {
        width: 2px;
        height: 40px;
        background: linear-gradient(to bottom, #1B5E20, #4CAF50);
        margin: 0 auto;
    }

    .org-hline-group {
        position: relative;
        display: flex;
        justify-content: center;
        gap: 0;
    }

    .org-hline-group::before {
        content: '';
        position: absolute;
        top: 0;
        left: 25%;
        right: 25%;
        height: 2px;
        background: #4CAF50;
    }

    /* Sekjend + Bendahara */
    .org-duo {
        display: flex;
        justify-content: center;
        gap: 60px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
        margin-bottom: 16px;
    }

    .org-person {
        text-align: center;
        width: 160px;
    }

    .org-person .avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: white;
        font-weight: 700;
        border: 3px solid #E8F5E9;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .org-person .avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .org-person h4 {
        font-size: 0.95rem;
        margin-bottom: 4px;
    }

    .org-person .role {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 14px;
        border-radius: 50px;
        display: inline-block;
    }

    /* PAO */
    .org-pao-card {
        max-width: 360px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        padding: 20px 28px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 2px solid #FFF3E0;
        position: relative;
        z-index: 1;
    }

    /* Level badge */
    .level-badge {
        display: inline-block;
        padding: 6px 24px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: white;
        margin-bottom: 28px;
    }

    /* Bidang Cards */
    .bidang-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        position: relative;
        z-index: 1;
    }

    .bidang-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #F0F0F0;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .bidang-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .bidang-card-header {
        padding: 16px 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bidang-card-header .icon {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .bidang-card-body {
        padding: 16px 20px;
    }

    .bidang-member {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #F5F5F5;
    }

    .bidang-member:last-child {
        border-bottom: none;
    }

    .bidang-member .mini-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .bidang-member .mini-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .bidang-member .info strong {
        font-size: 0.9rem;
        display: block;
    }

    .bidang-member .info span {
        font-size: 0.78rem;
        font-weight: 600;
    }

    .staff-section {
        margin-top: 8px;
        padding-top: 10px;
        border-top: 2px dashed #EEEEEE;
    }

    .staff-section .label {
        font-size: 0.65rem;
        text-transform: uppercase;
        color: #BDBDBD;
        letter-spacing: 1.5px;
        margin-bottom: 8px;
        font-weight: 700;
    }

    .staff-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F5F5F5;
        padding: 5px 12px 5px 5px;
        border-radius: 50px;
        font-size: 0.8rem;
        margin: 3px;
    }

    .staff-chip .dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        color: white;
        font-weight: 700;
    }

    .staff-chip img {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Sections */
    .visi-misi-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 48px;
        align-items: start;
    }

    .visi-card {
        border-radius: 20px;
        padding: 36px;
        position: relative;
        overflow: hidden;
    }

    .visi-card::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        opacity: 0.08;
    }

    .visi-card h3 {
        font-size: 1.1rem;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .visi-card p {
        font-size: 0.95rem;
        line-height: 1.8;
    }

    .bidang-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
    }

    .bidang-info-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 28px;
        transition: all 0.3s ease;
    }

    .bidang-info-card:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-3px);
    }

    .bidang-info-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 14px;
    }

    @media (max-width: 768px) {
        .profil-hero {
            padding: 80px 16px 60px;
            min-height: auto;
        }

        .profil-hero h1 {
            font-size: 1.8rem !important;
        }

        .profil-hero p {
            font-size: 0.95rem;
        }

        .visi-misi-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .visi-card {
            padding: 24px;
        }

        .org-section {
            padding: 60px 16px;
        }

        .org-header {
            margin-bottom: 40px;
        }

        .org-apex .avatar {
            width: 90px;
            height: 90px;
            font-size: 2rem;
        }

        .org-duo {
            flex-direction: column;
            gap: 16px;
            align-items: center;
        }

        .org-person {
            width: 100%;
            max-width: 280px;
        }

        .org-person .avatar {
            width: 64px;
            height: 64px;
            font-size: 1.3rem;
        }

        .org-pao-card {
            max-width: 100%;
        }

        .bidang-grid {
            grid-template-columns: 1fr;
        }

        .bidang-info-grid {
            grid-template-columns: 1fr;
        }

        .org-hline-group::before {
            left: 10%;
            right: 10%;
        }

        .level-badge {
            font-size: 0.7rem;
            padding: 5px 18px;
        }
    }

    @media (max-width: 480px) {
        .profil-hero {
            padding: 70px 12px 48px;
        }

        .profil-hero h1 {
            font-size: 1.5rem !important;
        }

        .org-section {
            padding: 48px 12px;
        }

        .org-apex .avatar {
            width: 80px;
            height: 80px;
            font-size: 1.8rem;
        }

        .org-apex h3 {
            font-size: 1.1rem;
        }

        .org-person h4 {
            font-size: 0.88rem;
        }

        .bidang-card-header {
            padding: 12px 16px;
        }

        .bidang-card-body {
            padding: 12px 16px;
        }

        .bidang-member .mini-avatar {
            width: 32px;
            height: 32px;
        }

        .staff-chip {
            font-size: 0.75rem;
        }

        .visi-card h3 {
            font-size: 1rem;
        }

        .visi-card p {
            font-size: 0.88rem;
        }
    }
</style>

<div style="height:72px;"></div>

<!-- HERO -->
<section class="profil-hero">
    <div style="position:relative;z-index:1;">
        <div class="badge">✦ Komisariat IT Telkom</div>
        <h1>Profil <span class="accent">Organisasi</span></h1>
        <p>Mengenal struktur kepengurusan HMI Komisariat IT Telkom — organisasi pengkaderan intelektual muslim tertua di
            Indonesia.</p>
    </div>
</section>

<!-- TENTANG & VISI MISI -->
<section class="section" style="padding:100px 24px;">
    <div class="container" style="max-width:1000px;">
        <!-- About -->
        <div class="reveal" style="margin-bottom:64px;">
            <span
                style="color:#1B5E20;font-weight:700;font-size:0.78rem;text-transform:uppercase;letter-spacing:2px;">Tentang
                Kami</span>
            <h2 style="font-size:2.2rem;margin:12px 0 24px;letter-spacing:-0.03em;line-height:1.2;">Wadah
                Pengkaderan<br>Intelektual Muslim</h2>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="visi-misi-grid">
                <p style="color:#616161;line-height:2;font-size:0.95rem;">
                    Himpunan Mahasiswa Islam (HMI) adalah organisasi mahasiswa tertua di Indonesia yang berlandaskan
                    pada semangat <strong>Keindonesiaan</strong> dan <strong>Keislaman</strong>. Didirikan pada
                    <strong>5 Februari 1947</strong> di Yogyakarta oleh <strong>Lafran Pane</strong>.
                </p>
                <p style="color:#616161;line-height:2;font-size:0.95rem;">
                    HMI Komisariat IT Telkom memposisikan diri sebagai wadah pengkaderan intelektual muslim di
                    lingkungan kampus teknologi, mencetak generasi yang memiliki keseimbangan antara kompetensi
                    akademis, jiwa kepemimpinan, dan nilai-nilai keislaman.
                </p>
            </div>
        </div>

        <!-- Visi Misi -->
        <div class="visi-misi-grid reveal">
            <div class="visi-card" style="background:linear-gradient(135deg,#E8F5E9,#C8E6C9);">
                <div
                    style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:#1B5E20;opacity:0.06;">
                </div>
                <h3 style="color:#1B5E20;">🎯 Visi</h3>
                <p style="color:#2E7D32;">Terbinanya intelektual muslim sebagai insan cita yang bertanggung jawab atas
                    terwujudnya masyarakat adil makmur yang diridhai Allah SWT.</p>
            </div>
            <div class="visi-card" style="background:linear-gradient(135deg,#FFF8E1,#FFECB3);">
                <div
                    style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:#E65100;opacity:0.06;">
                </div>
                <h3 style="color:#E65100;">🚀 Misi</h3>
                <p style="color:#BF360C;">Membina dan mengembangkan potensi kader melalui jalur kaderisasi formal (LK-1,
                    LK-2, LK-3) dan kaderisasi informal, serta mengoptimalkan peran organisasi di tengah masyarakat.</p>
            </div>
        </div>
    </div>
</section>

<!-- HIERARKI HMI (Flowchart) -->
<section style="padding:80px 24px;background:#FAFAFA;">
    <div class="container" style="max-width:680px;">
        <div class="org-header reveal">
            <h2>Struktur Hierarki HMI</h2>
            <p>Organisasi berjenjang dari nasional hingga komisariat</p>
            <div class="line"></div>
        </div>
        <div class="hierarki-flow reveal">
            <?php
            $hierarki = [
                ['🏛️', 'Pengurus Besar (PB HMI)', 'Tingkat Nasional — Pusat kepemimpinan dan kebijakan strategis organisasi secara keseluruhan.', '#0D3B13'],
                ['🗺️', 'Badan Koordinasi (Badko)', 'Tingkat Wilayah — Mengoordinasikan cabang-cabang HMI dalam satu regional.', '#1B5E20'],
                ['🏙️', 'Pengurus Cabang', 'Tingkat Kab/Kota — Mengelola komisariat-komisariat di tingkat kabupaten/kota.', '#388E3C'],
                ['🎓', 'Komisariat IT Telkom', 'Tingkat Perguruan Tinggi — Unit operasional kaderisasi di lingkungan kampus.', '#FFD700'],
            ];
            foreach ($hierarki as $i => $h):
                ?>
                <div class="hk-step">
                    <div class="hk-node" style="background:<?= $h[3] ?>;<?= $h[3] === '#FFD700' ? 'color:#121212;' : '' ?>">
                        <span><?= $i + 1 ?></span>
                    </div>
                    <?php if ($i < count($hierarki) - 1): ?>
                        <div class="hk-connector"
                            style="background:linear-gradient(to bottom, <?= $h[3] ?>, <?= $hierarki[$i + 1][3] ?>);"></div>
                    <?php endif; ?>
                    <div class="hk-card<?= $i === 3 ? ' hk-active' : '' ?>">
                        <div class="hk-icon"><?= $h[0] ?></div>
                        <div class="hk-content">
                            <h4 class="hk-title"><?= $h[1] ?></h4>
                            <p class="hk-desc"><?= $h[2] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    /* ====== HIERARKI FLOWCHART ====== */
    .hierarki-flow {
        display: flex;
        flex-direction: column;
    }

    .hk-step {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }

    .hk-node {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        z-index: 2;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.18);
    }

    .hk-node span {
        color: white;
        font-weight: 800;
        font-size: 0.9rem;
    }

    .hk-connector {
        position: absolute;
        left: 20px;
        top: 42px;
        width: 2px;
        height: calc(100% - 42px);
        z-index: 1;
        border-radius: 1px;
    }

    .hk-card {
        flex: 1;
        background: white;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #F0F0F0;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        transition: all 0.3s ease;
    }

    .hk-card:hover {
        transform: translateX(4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
    }

    .hk-active {
        background: linear-gradient(135deg, #0D3B13, #1B5E20) !important;
        border-color: #1B5E20 !important;
    }

    .hk-active .hk-title {
        color: white !important;
    }

    .hk-active .hk-desc {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    .hk-icon {
        font-size: 1.4rem;
        flex-shrink: 0;
        padding-top: 2px;
    }

    .hk-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #212121;
        margin-bottom: 4px;
    }

    .hk-desc {
        font-size: 0.82rem;
        color: #757575;
        line-height: 1.6;
    }

    @media (max-width:768px) {
        .hk-node {
            width: 36px;
            height: 36px;
        }

        .hk-node span {
            font-size: 0.8rem;
        }

        .hk-connector {
            left: 17px;
            top: 36px;
            height: calc(100% - 36px);
        }

        .hk-card {
            padding: 16px;
            gap: 12px;
        }

        .hk-step {
            gap: 14px;
        }
    }

    @media (max-width:480px) {
        .hk-node {
            width: 32px;
            height: 32px;
        }

        .hk-node span {
            font-size: 0.72rem;
        }

        .hk-connector {
            left: 15px;
            top: 32px;
            height: calc(100% - 32px);
        }

        .hk-card {
            padding: 14px;
            gap: 10px;
            margin-bottom: 12px;
        }

        .hk-step {
            gap: 10px;
        }
    }
</style>

<!-- ORGANIGRAM -->
<section class="org-section" style="background:white;">
    <div class="container" style="max-width:1100px;">
        <div class="org-header reveal">
            <h2>Kepengurusan <?= sanitize($periodeAktif) ?></h2>
            <p>Struktur organisasi HMI Komisariat IT Telkom</p>
            <div class="line"></div>

            <?php if (count($allPeriodes) > 1): ?>
                <div style="margin-top:24px;">
                    <form method="get" action=""
                        style="display:inline-flex;align-items:center;gap:10px;background:white;padding:6px 8px 6px 16px;border-radius:50px;box-shadow:0 2px 8px rgba(0,0,0,0.06);border:1px solid #E0E0E0;">
                        <label for="periodeSelect"
                            style="font-size:0.8rem;font-weight:600;color:#757575;white-space:nowrap;">📅 Pilih
                            Periode:</label>
                        <select name="periode" id="periodeSelect" onchange="this.form.submit()"
                            style="border:none;background:#F5F5F5;padding:8px 16px;border-radius:50px;font-size:0.85rem;font-weight:600;color:#1B5E20;cursor:pointer;outline:none;-webkit-appearance:none;appearance:none;padding-right:28px;background-image:url('data:image/svg+xml;utf8,<svg fill=\'%231B5E20\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/></svg>');background-repeat:no-repeat;background-position:right 8px center;background-size:16px;">
                            <?php foreach ($allPeriodes as $p): ?>
                                <option value="<?= sanitize($p) ?>" <?= $p === $periodeAktif ? 'selected' : '' ?>>
                                    <?= sanitize($p) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($ketuaUmum): ?>
            <!-- KETUA UMUM — paling atas, sendirian -->
            <div class="org-apex reveal">
                <div class="avatar">
                    <?php if ($ketuaUmum['foto_path']): ?>
                        <img src="<?= asset('/') ?><?= $ketuaUmum['foto_path'] ?>" alt="<?= sanitize($ketuaUmum['nama']) ?>">
                    <?php else: ?>
                        <?= mb_substr($ketuaUmum['nama'], 0, 1) ?>
                    <?php endif; ?>
                </div>
                <h3><?= sanitize($ketuaUmum['nama']) ?></h3>
                <div class="role">Ketua Umum</div>
            </div>

            <!-- Connector: Ketua → Sekjend/Bendahara -->
            <div class="org-vline"></div>
        <?php endif; ?>

        <?php if (!empty($wakil)): ?>
            <!-- SEKJEND & BENDAHARA — dibawah Ketua Umum -->
            <div class="org-hline-group" style="margin-bottom:8px;">
                <div style="height:2px;"></div>
            </div>
            <div class="org-duo reveal">
                <?php foreach ($wakil as $w): ?>
                    <div class="org-person">
                        <div class="avatar" style="background:linear-gradient(135deg,#2E7D32,#66BB6A);">
                            <?php if ($w['foto_path']): ?>
                                <img src="<?= asset('/') ?><?= $w['foto_path'] ?>" alt="<?= sanitize($w['nama']) ?>">
                            <?php else: ?>
                                <?= mb_substr($w['nama'], 0, 1) ?>
                            <?php endif; ?>
                        </div>
                        <h4><?= sanitize($w['nama']) ?></h4>
                        <span class="role" style="background:#E8F5E9;color:#1B5E20;"><?= sanitize($w['jabatan']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="org-vline" style="background:linear-gradient(to bottom,#4CAF50,#E65100);"></div>
        <?php endif; ?>

        <?php if (!empty($pao)): ?>
            <!-- PAO -->
            <div class="reveal" style="text-align:center;margin-bottom:16px;">
                <div class="level-badge" style="background:#E65100;">PAO</div>
            </div>
            <div style="display:flex;justify-content:center;gap:20px;flex-wrap:wrap;margin-bottom:16px;" class="reveal">
                <?php foreach ($pao as $p): ?>
                    <div class="org-pao-card">
                        <div style="display:flex;align-items:center;gap:14px;">
                            <?php if ($p['foto_path']): ?>
                                <img src="<?= asset('/') ?><?= $p['foto_path'] ?>"
                                    style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:3px solid #E65100;">
                            <?php else: ?>
                                <div
                                    style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#E65100,#FF9800);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:white;font-weight:700;flex-shrink:0;">
                                    <?= mb_substr($p['nama'], 0, 1) ?>
                                </div>
                            <?php endif; ?>
                            <div style="text-align:left;">
                                <strong style="font-size:0.95rem;"><?= sanitize($p['nama']) ?></strong>
                                <div style="color:#E65100;font-size:0.8rem;font-weight:600;"><?= sanitize($p['jabatan']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="org-vline" style="background:linear-gradient(to bottom,#E65100,#1565C0);"></div>
        <?php endif; ?>

        <?php if (!empty($bidangGroups)): ?>
            <!-- MIDDLE MANAGEMENT — BIDANG -->
            <div class="reveal" style="text-align:center;margin-bottom:28px;">
                <div class="level-badge" style="background:#1565C0;">Bidang-Bidang</div>
            </div>
            <div class="bidang-grid reveal">
                <?php $colorIdx = 0;
                foreach ($bidangGroups as $bidang => $members):
                    $color = $bidangColors[$colorIdx % count($bidangColors)]; ?>
                    <div class="bidang-card">
                        <div class="bidang-card-header"
                            style="background:linear-gradient(135deg,<?= $color ?>,<?= $color ?>CC);">
                            <div class="icon"><?= ['📋', '💼', '📣', '📚', '🤝', '🌐', '🎨', '📊'][$colorIdx % 8] ?></div>
                            <strong style="font-size:0.9rem;"><?= sanitize($bidang) ?></strong>
                        </div>
                        <div class="bidang-card-body">
                            <?php foreach ($members as $m): ?>
                                <div class="bidang-member">
                                    <div class="mini-avatar" style="background:<?= $color ?>15;color:<?= $color ?>;">
                                        <?php if ($m['foto_path']): ?>
                                            <img src="<?= asset('/') ?><?= $m['foto_path'] ?>" alt="">
                                        <?php else: ?>
                                            <?= mb_substr($m['nama'], 0, 1) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info">
                                        <strong><?= sanitize($m['nama']) ?></strong>
                                        <span
                                            style="color:<?= strpos($m['jabatan'], 'Wasekum') !== false ? '#E65100' : $color ?>;"><?= sanitize($m['jabatan']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (isset($staffGroups[$bidang])): ?>
                                <div class="staff-section">
                                    <div class="label">Staff</div>
                                    <?php foreach ($staffGroups[$bidang] as $s): ?>
                                        <span class="staff-chip">
                                            <?php if ($s['foto_path']): ?>
                                                <img src="<?= asset('/') ?><?= $s['foto_path'] ?>" alt="">
                                            <?php else: ?>
                                                <span class="dot"
                                                    style="background:<?= $color ?>;"><?= mb_substr($s['nama'], 0, 1) ?></span>
                                            <?php endif; ?>
                                            <?= sanitize($s['nama']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $colorIdx++; endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- BIDANG KERJA INFO -->
<section style="padding:100px 24px;background:#121212;">
    <div class="container" style="max-width:1000px;">
        <div class="org-header reveal" style="position:relative;z-index:1;">
            <h2 style="color:white;">Bidang Kerja</h2>
            <p style="color:rgba(255,255,255,0.45);">Enam pilar kerja komisariat</p>
            <div class="line"></div>
        </div>
        <div class="bidang-info-grid reveal">
            <?php
            $bidangInfo = [
                ['📋', 'Pembinaan Anggota', 'Mengelola proses kaderisasi formal dan non-formal, pengembangan kapasitas kader.', '#4CAF50'],
                ['📣', 'PTKP', 'Perguruan Tinggi, Kemahasiswaan, dan Kepemudaan — mengawal isu kampus dan gerakan kepemudaan.', '#2196F3'],
                ['📚', 'KAIL', 'Kajian dan Aksi Intelektual — diskusi ilmiah, bedah buku, dan penguatan wawasan kritis.', '#9C27B0'],
                ['💼', 'KPP', 'Kewirausahaan dan Pengembangan Profesi — inkubasi bisnis, workshop skill, dan karier kader.', '#FF9800'],
                ['👩', 'PP', 'Pemberdayaan Perempuan — memperjuangkan kesetaraan gender dan leadership perempuan.', '#E91E63'],
                ['🌐', 'Media & IT', 'Platform digital, konten kreatif, dokumentasi, dan teknologi informasi.', '#00BCD4'],
            ];
            foreach ($bidangInfo as $b):
                ?>
                <div class="bidang-info-card">
                    <div class="bidang-info-icon" style="background:<?= $b[3] ?>18;"><?= $b[0] ?></div>
                    <h3 style="font-size:1rem;color:white;margin-bottom:8px;"><?= $b[1] ?></h3>
                    <p style="font-size:0.88rem;color:rgba(255,255,255,0.4);line-height:1.7;"><?= $b[2] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
