<?php
/**
 * HMI IT Telkom - BERITA (List)
 */
$pageTitle = 'Berita';
$pageDescription = 'Berita terbaru dan artikel HMI Komisariat IT Telkom — update kegiatan, opini kader, dan perkembangan organisasi mahasiswa Islam.';
$pageKeywords = 'berita HMI, berita himpunan mahasiswa islam, berita HMI IT Telkom, artikel HMI, kegiatan HMI, opini kader HMI';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDB();

// Pagination
$perPage = 9;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$total = $pdo->query("SELECT COUNT(*) FROM berita_events WHERE tipe_post='berita'")->fetchColumn();
$totalPages = ceil($total / $perPage);

$stmt = $pdo->prepare("SELECT * FROM berita_events WHERE tipe_post='berita' ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$beritaList = $stmt->fetchAll();
?>

<div style="height:72px;"></div>

<section style="background:linear-gradient(135deg,#0D3B13,#121212);padding:60px 24px;text-align:center;">
    <h1 style="color:white;font-size:2.5rem;margin-bottom:12px;">📰 Berita & <span style="color:#FFD700;">Artikel</span>
    </h1>
    <p style="color:rgba(255,255,255,0.7);max-width:600px;margin:0 auto;">Liputan kegiatan, opini kritis, dan
        perkembangan terkini dari kader HMI Komisariat IT Telkom.</p>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($beritaList)): ?>
            <div class="text-center" style="padding:60px 0;">
                <div style="font-size:3rem;margin-bottom:16px;">📰</div>
                <h3 style="color:#757575;">Belum ada berita dipublikasikan.</h3>
                <p style="color:#9E9E9E;">Konten akan segera hadir. Pantau terus portal ini.</p>
            </div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($beritaList as $b): ?>
                    <div class="card">
                        <div class="card-img"
                            style="<?= $b['path_gambar'] ? "background-image:url('<?= asset('/') ?>{$b['path_gambar']}');background-size:cover;background-position:center;" : '' ?>display:flex;align-items:center;justify-content:center;font-size:3rem;">
                            <?= $b['path_gambar'] ? '' : '📰' ?>
                        </div>
                        <div class="card-body">
                            <span class="card-badge badge-berita">Berita</span>
                            <h3 class="card-title">
                                <a href="<?= url('/berita/detail') ?>?slug=<?= urlencode($b['slug']) ?>">
                                    <?= sanitize($b['judul']) ?>
                                </a>
                            </h3>
                            <p class="card-text">
                                <?= truncateText($b['konten_teks'], 120) ?>
                            </p>
                            <div class="card-meta">
                                <span>🕐
                                    <?= timeAgo($b['created_at']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">&laquo;</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active">
                                <?= $i ?>
                            </span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>">&raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
