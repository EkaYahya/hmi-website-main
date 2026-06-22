<?php
/**
 * HMI IT Telkom - LMS Header (Sidebar + Topbar)
 */
require_once __DIR__ . '/../config/functions.php';
requireKader();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?>LMS HMI IT Telkom
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">
</head>

<body>
    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar" id="lms-sidebar"
            style="background: linear-gradient(180deg, #0D3B13 0%, #1B5E20 100%);">
            <div class="sidebar-header">
                <span style="font-size:1.5rem;">📚</span>
                <div>
                    <h2>LMS HMI</h2>
                    <small>Learning Management System</small>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Kader:
                        <?= sanitize($user['nama_lengkap'] ?? $user['username'] ?? '') ?>
                    </div>
                    <a href="<?= lmsUrl('/index.php') ?>"
                        class="sidebar-link <?= $currentPage === 'index' ? 'active' : '' ?>">
                        <span class="icon">🏠</span> Dashboard Modul
                    </a>
                    <a href="<?= lmsUrl('/rapor.php') ?>"
                        class="sidebar-link <?= $currentPage === 'rapor' ? 'active' : '' ?>">
                        <span class="icon">📋</span> Rapor Saya
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">5 Materi Wajib</div>
                    <?php
                    try {
                        $pdo = getDB();
                        $modules = $pdo->query("SELECT id, nama_modul, urutan FROM lms_modules ORDER BY urutan")->fetchAll();
                        $icons = ['📜', '⚖️', '💡', '👔', '🎯'];
                        foreach ($modules as $i => $mod):
                            ?>
                            <a href="<?= lmsUrl('/module.php?id=' . $mod['id']) ?>"
                                class="sidebar-link <?= (isset($_GET['id']) && $_GET['id'] == $mod['id']) || (isset($_GET['module_id']) && $_GET['module_id'] == $mod['id']) ? 'active' : '' ?>">
                                <span class="icon">
                                    <?= $icons[$i] ?? '📖' ?>
                                </span>
                                <span style="font-size:0.85rem;">
                                    <?= sanitize($mod['nama_modul']) ?>
                                </span>
                            </a>
                            <?php
                        endforeach;
                    } catch (PDOException $e) {
                    }
                    ?>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Lainnya</div>
                    <a href="<?= url('/') ?>" class="sidebar-link">
                        <span class="icon">🌐</span> Portal Publik
                    </a>
                    <a href="<?= adminUrl('/logout.php') ?>" class="sidebar-link" style="color:#ef4444;">
                        <span class="icon">🚪</span> Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="admin-main">
            <div class="admin-topbar" style="border-bottom: 3px solid #1B5E20;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="sidebar-toggle"
                        style="display:none;background:none;border:none;font-size:1.3rem;cursor:pointer;"
                        onclick="document.getElementById('lms-sidebar').classList.toggle('show')">☰</button>
                    <h1 style="font-size:1.3rem;">
                        <?= isset($pageTitle) ? sanitize($pageTitle) : 'Learning Management System' ?>
                    </h1>
                </div>
                <div style="display:flex;align-items:center;gap:12px;font-size:0.9rem;color:#757575;">
                    <span>🎓
                        <?= sanitize($user['nama_lengkap'] ?? $user['username'] ?? '') ?>
                    </span>
                    <span
                        style="padding:4px 10px;background:#E8F5E9;color:#1B5E20;border-radius:50px;font-size:0.75rem;font-weight:600;">Kader</span>
                </div>
            </div>

            <div class="admin-content">
                <?= renderFlash('success') ?>
                <?= renderFlash('error') ?>
                <?= renderFlash('info') ?>