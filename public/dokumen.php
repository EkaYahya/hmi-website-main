<?php
/**
 * HMI IT Telkom - DOKUMEN
 */
$pageTitle = 'Arsip Dokumen';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDB();
$dokumen = $pdo->query("SELECT * FROM dokumen ORDER BY uploaded_at DESC")->fetchAll();
?>
<div style="height:72px;"></div>
<section style="background:linear-gradient(135deg,#0D3B13,#121212);padding:60px 24px;text-align:center;">
    <h1 style="color:white;font-size:2.5rem;margin-bottom:12px;">📁 Arsip <span style="color:#FFD700;">Dokumen</span>
    </h1>
    <p style="color:rgba(255,255,255,0.7);max-width:600px;margin:0 auto;">Unduh tata tertib, hasil ketetapan rapat,
        pedoman organisasi, hymne HMI, dan dokumen resmi lainnya.</p>
</section>
<section class="section">
    <div class="container" style="max-width:800px;">
        <?php if (empty($dokumen)): ?>
            <div class="text-center" style="padding:60px 0;">
                <div style="font-size:3rem;margin-bottom:16px;">📁</div>
                <h3 style="color:#757575;">Belum ada dokumen yang diunggah.</h3>
            </div>
        <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <?php foreach ($dokumen as $d): ?>
                    <div class="card" style="padding:20px;display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:16px;">
                            <span style="font-size:1.5rem;">
                                <?= strtolower($d['file_type']) === 'pdf' ? '📄' : '📎' ?>
                            </span>
                            <div>
                                <strong>
                                    <?= sanitize($d['judul']) ?>
                                </strong>
                                <div style="font-size:0.8rem;color:#757575;">
                                    <?= sanitize($d['kategori']) ?> ·
                                    <?= strtoupper($d['file_type']) ?> ·
                                    <?= round($d['file_size'] / 1024) ?> KB
                                </div>
                            </div>
                        </div>
                        <a href="<?= asset('/') ?><?= $d['file_path'] ?>" download class="btn btn-primary btn-sm">⬇ Unduh</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
