<?php
/**
 * HMI IT Telkom - Public Header (Navbar)
 * Included by all public-facing pages
 * SEO-optimized with Open Graph, Twitter Card, JSON-LD
 */
require_once __DIR__ . '/../config/functions.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// ============================================================
// SEO CONFIGURATION
// ============================================================
$siteUrl = 'https://hmiittelkom.com';
$siteName = 'HMI Komisariat IT Telkom';
$defaultDesc = 'Portal resmi Himpunan Mahasiswa Islam (HMI) Komisariat IT Telkom. Organisasi kemahasiswaan Islam tertua di Indonesia â€” wadah kaderisasi intelektual muslim di lingkungan kampus IT Telkom Surabaya.';
$defaultKeys = 'HMI, himpunan mahasiswa islam, HMI IT Telkom, hmi komisariat it telkom, himpunan mahasiswa islam it telkom, organisasi mahasiswa islam, kaderisasi HMI, NDP HMI, LK-1, LK-2, organisasi kampus IT Telkom, HMI Surabaya, mahasiswa islam indonesia, kader HMI, lafran pane';
$seoTitle = isset($pageTitle) ? sanitize($pageTitle) . ' 🟢 ' . $siteName : $siteName . '🟢 Rumah Bagi Intelektual Muslim';
$seoDesc = isset($pageDescription) ? $pageDescription : $defaultDesc;
$canonicalUrl = $siteUrl . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$logoImg = getSetting('logo_image');
$seoImage = $logoImg ? $siteUrl . '/' . $logoImg : $siteUrl . '/assets/images/og-cover.png';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Primary SEO Meta -->
    <title><?= $seoTitle ?></title>
    <meta name="description" content="<?= htmlspecialchars($seoDesc) ?>">
    <meta name="keywords" content="<?= htmlspecialchars(isset($pageKeywords) ? $pageKeywords : $defaultKeys) ?>">
    <meta name="author" content="HMI Komisariat IT Telkom">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($seoTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seoDesc) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($seoImage) ?>">
    <meta property="og:site_name" content="<?= $siteName ?>">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seoTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seoDesc) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($seoImage) ?>">

    <!-- Theme Color -->
    <meta name="theme-color" content="#1B5E20">
    <meta name="msapplication-TileColor" content="#1B5E20">

    <!-- Favicon -->
    <?php if ($logoImg): ?>
        <link rel="icon" type="image/png" href="<?= asset('/' . $logoImg) ?>">
        <link rel="apple-touch-icon" href="<?= asset('/' . $logoImg) ?>">
    <?php endif; ?>

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "HMI Komisariat IT Telkom",
        "alternateName": ["Himpunan Mahasiswa Islam IT Telkom", "HMI IT Telkom", "HMI Komisariat IT Telkom Surabaya"],
        "url": "<?= $siteUrl ?>",
        <?php if ($logoImg): ?>"logo": "<?= $siteUrl ?>/<?= $logoImg ?>",<?php endif; ?>

        "description": "<?= htmlspecialchars($defaultDesc) ?>",
        "foundingDate": "1947-02-05",
        "parentOrganization": {
            "@type": "Organization",
            "name": "Himpunan Mahasiswa Islam (HMI)",
            "url": "https://hmi.or.id"
        },
        "sameAs": []
    }
    </script>

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        hmi: { green: '#1B5E20', 'green-light': '#4CAF50', gold: '#FFD700', black: '#121212' }
                    },
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.querySelector('.navbar-toggle');
            const navbarMenu = document.querySelector('.navbar-menu');
            
            if(toggleButton && navbarMenu) {
                toggleButton.addEventListener('click', function() {
                    // Toggle class 'active' pada menu dan tombol
                    navbarMenu.classList.toggle('active');
                    toggleButton.classList.toggle('active');
                    
                    // Ubah icon burger menjadi X (opsional, jika ingin simpel via teks)
                    if (toggleButton.classList.contains('active')) {
                        toggleButton.innerHTML = '✕'; // Icon Close
                    } else {
                        toggleButton.innerHTML = '☰'; // Icon Burger
                    }
                });
            }
        });
    </script>
</head>

<body>

    <!-- NAVBAR -->
  <nav class="bg-hmi-green text-white fixed w-full z-50 top-0 shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            
            <a href="<?= url('/') ?>" class="flex items-center gap-2 font-outfit font-bold text-lg">
                <?php if ($logoImg): ?>
                    <img src="<?= asset('/' . $logoImg) ?>" class="h-10 w-auto object-contain">
                <?php else: ?>
                    <span>🟢</span>
                <?php endif; ?>
                <div class="leading-tight">
                    <span class="block">HMI</span>
                    <span class="text-[10px] font-normal opacity-80">Komisariat IT Telkom</span>
                </div>
            </a>

            <button id="mobile-menu-btn" class="md:hidden text-2xl focus:outline-none">
                ☰
            </button>

            <ul class="hidden md:flex gap-6 text-sm font-medium items-center">
                <li><a href="<?= url('/') ?>" class="hover:text-hmi-gold transition <?= $currentPage === 'index' ? 'text-hmi-gold' : '' ?>">Beranda</a></li>
                <li><a href="<?= url('/profil') ?>" class="hover:text-hmi-gold transition <?= $currentPage === 'profil' ? 'text-hmi-gold' : '' ?>">Profil</a></li>
                <li><a href="<?= url('/sejarah') ?>" class="hover:text-hmi-gold transition <?= $currentPage === 'sejarah' ? 'text-hmi-gold' : '' ?>">Sejarah</a></li>
                <li><a href="<?= url('/berita') ?>" class="hover:text-hmi-gold transition <?= $currentPage === 'berita' ? 'text-hmi-gold' : '' ?>">Berita</a></li>
                <li><a href="<?= url('/event') ?>" class="hover:text-hmi-gold transition <?= $currentPage === 'event' ? 'text-hmi-gold' : '' ?>">Event</a></li>
                <li><a href="<?= url('/dokumen') ?>" class="hover:text-hmi-gold transition <?= $currentPage === 'dokumen' ? 'text-hmi-gold' : '' ?>">Dokumen</a></li>
                <li><a href="<?= url('/hotline') ?>" class="hover:text-hmi-gold transition <?= $currentPage === 'hotline' ? 'text-hmi-gold' : '' ?>">Hotline</a></li>

                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?= adminUrl('/index.php') ?>" class="bg-hmi-gold text-hmi-black px-4 py-2 rounded-full font-bold text-xs hover:bg-yellow-400 transition">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="<?= lmsUrl('/index.php') ?>" class="bg-hmi-gold text-hmi-black px-4 py-2 rounded-full font-bold text-xs hover:bg-yellow-400 transition">LMS</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>
                        <a href="<?= url('/login') ?>" class="bg-hmi-gold text-hmi-black px-4 py-2 rounded-full font-bold text-xs hover:bg-yellow-400 transition">
                            Masuk
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-hmi-green border-t border-white/10 p-4 absolute w-full left-0 shadow-xl">
            <ul class="flex flex-col gap-4 text-center">
                <li><a href="<?= url('/') ?>" class="block py-2 hover:bg-white/5 rounded">Beranda</a></li>
                <li><a href="<?= url('/profil') ?>" class="block py-2 hover:bg-white/5 rounded">Profil</a></li>
                <li><a href="<?= url('/sejarah') ?>" class="block py-2 hover:bg-white/5 rounded">Sejarah</a></li>
                <li><a href="<?= url('/berita') ?>" class="block py-2 hover:bg-white/5 rounded">Berita</a></li>
                <li><a href="<?= url('/event') ?>" class="block py-2 hover:bg-white/5 rounded">Event</a></li>
                <li><a href="<?= url('/dokumen') ?>" class="block py-2 hover:bg-white/5 rounded">Dokumen</a></li>
                <li><a href="<?= url('/hotline') ?>" class="block py-2 hover:bg-white/5 rounded">Hotline</a></li>
                
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?= adminUrl('/index.php') ?>" class="block py-2 bg-hmi-gold text-black rounded font-bold">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="<?= lmsUrl('/index.php') ?>" class="block py-2 bg-hmi-gold text-black rounded font-bold">LMS</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?= url('/login') ?>" class="block py-2 bg-hmi-gold text-black rounded font-bold">Masuk</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        if(btn && menu){
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
                btn.innerHTML = menu.classList.contains('hidden') ? '☰' : '✕';
            });
        }
    </script>

    <!-- Flash Messages -->
    <div style="position:fixed;top:80px;right:24px;z-index:9999;width:360px;">
        <?= renderFlash('auth') ?>
        <?= renderFlash('success') ?>
        <?= renderFlash('error') ?>
        <?= renderFlash('info') ?>
    </div>