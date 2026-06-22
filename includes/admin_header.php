<?php
/**
 * HMI IT Telkom - Admin Header (Sidebar + Topbar)
 */
ob_start(); // Buffer output so redirect() works even after HTML output
require_once __DIR__ . '/../config/functions.php';
requireAdmin();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?>Admin HMI IT Telkom
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">
</head>

<body>
    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="sidebar-header">
                <?php $adminLogo = getSetting('logo_image'); ?>
                <?php if ($adminLogo): ?>
                    <img src="<?= asset('/' . $adminLogo) ?>" alt="Logo"
                        style="height:32px;width:32px;object-fit:contain;border-radius:6px;">
                <?php else: ?>
                    <span style="font-size:1.5rem;">🟢</span>
                <?php endif; ?>
                <div>
                    <h2>HMI Admin</h2>
                    <small>Komisariat IT Telkom</small>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Menu Utama</div>
                    <a href="<?= adminUrl('/index.php') ?>"
                        class="sidebar-link <?= $currentPage === 'index' ? 'active' : '' ?>">
                        <span class="icon">📊</span> Dashboard
                    </a>
                    <a href="<?= adminUrl('/panel_kader.php') ?>"
                        class="sidebar-link <?= $currentPage === 'panel_kader' ? 'active' : '' ?>">
                        <span class="icon">👥</span> Manajemen Kader
                    </a>
                    <a href="<?= adminUrl('/pengurus.php') ?>"
                        class="sidebar-link <?= $currentPage === 'pengurus' ? 'active' : '' ?>">
                        <span class="icon">🏛️</span> Pengurus
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Konten</div>
                    <a href="<?= adminUrl('/manajemen_berita.php') ?>"
                        class="sidebar-link <?= $currentPage === 'manajemen_berita' ? 'active' : '' ?>">
                        <span class="icon">📰</span> Berita
                    </a>
                    <a href="<?= adminUrl('/manajemen_event.php') ?>"
                        class="sidebar-link <?= $currentPage === 'manajemen_event' ? 'active' : '' ?>">
                        <span class="icon">📅</span> Event
                    </a>
                    <a href="<?= adminUrl('/manajemen_dokumen.php') ?>"
                        class="sidebar-link <?= $currentPage === 'manajemen_dokumen' ? 'active' : '' ?>">
                        <span class="icon">📁</span> Dokumen
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">LMS</div>
                    <a href="<?= adminUrl('/lms_modules.php') ?>"
                        class="sidebar-link <?= $currentPage === 'lms_modules' ? 'active' : '' ?>">
                        <span class="icon">📚</span> Modul
                    </a>
                    <a href="<?= adminUrl('/lms_materials.php') ?>"
                        class="sidebar-link <?= $currentPage === 'lms_materials' ? 'active' : '' ?>">
                        <span class="icon">📎</span> Materi
                    </a>
                    <a href="<?= adminUrl('/lms_questions.php') ?>"
                        class="sidebar-link <?= $currentPage === 'lms_questions' ? 'active' : '' ?>">
                        <span class="icon">❓</span> Bank Soal
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Lainnya</div>
                    <a href="<?= adminUrl('/hotline_inbox.php') ?>"
                        class="sidebar-link <?= $currentPage === 'hotline_inbox' ? 'active' : '' ?>">
                        <span class="icon">📬</span> Hotline Inbox
                    </a>
                    <a href="<?= adminUrl('/site_settings.php') ?>"
                        class="sidebar-link <?= $currentPage === 'site_settings' ? 'active' : '' ?>">
                        <span class="icon">⚙️</span> Pengaturan Situs
                    </a>
                    <a href="<?= url('/') ?>" class="sidebar-link">
                        <span class="icon">🌐</span> Lihat Website
                    </a>
                    <a href="<?= adminUrl('/logout.php') ?>" class="sidebar-link" style="color:#ef4444;">
                        <span class="icon">🚪</span> Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="admin-main">
            <div class="admin-topbar">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="sidebar-toggle"
                        style="display:none;background:none;border:none;font-size:1.3rem;cursor:pointer;"
                        onclick="document.getElementById('admin-sidebar').classList.toggle('show')">☰</button>
                    <h1>
                        <?= isset($pageTitle) ? sanitize($pageTitle) : 'Dashboard' ?>
                    </h1>
                </div>
                <div style="display:flex;align-items:center;gap:12px;font-size:0.9rem;color:#757575;">
                    <span>👤
                        <?= sanitize($user['username'] ?? 'Admin') ?>
                    </span>
                </div>
            </div>

            <div class="admin-content">
                <?= renderFlash('success') ?>
                <?= renderFlash('error') ?>
                <?= renderFlash('info') ?>