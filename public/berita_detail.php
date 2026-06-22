<?php
/**
 * HMI IT Telkom - BERITA DETAIL
 */
require_once __DIR__ . '/../config/functions.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    redirect(url('/berita'));
}

$pdo = getDB();
// Ambil data berita + nama author
$stmt = $pdo->prepare("SELECT be.*, u.username as author_name FROM berita_events be LEFT JOIN users u ON be.author_id = u.id WHERE be.slug = ? AND be.tipe_post='berita'");
$stmt->execute([$slug]);
$berita = $stmt->fetch();

if (!$berita) {
    flash('error', 'Berita tidak ditemukan.', 'error');
    redirect(url('/berita'));
}

// SEO Meta Data
$pageTitle = $berita['judul'];
$kontenPolos = strip_tags($berita['konten_teks']); // Bersihkan tag HTML untuk deskripsi
$pageDescription = mb_substr($kontenPolos, 0, 160) . '...';
$pageKeywords = 'berita HMI, ' . $berita['judul'] . ', HMI IT Telkom';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="h-20 md:h-24"></div>

<main class="container mx-auto px-4 pb-20 max-w-4xl">
    
    <div class="mb-6">
        <a href="<?= url('/berita') ?>" class="inline-flex items-center text-gray-500 hover:text-hmi-green transition font-medium text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Berita
        </a>
    </div>

    <article class="bg-white md:p-0">
        
        <header class="mb-8 border-b pb-6">
            <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide mb-3 inline-block">
                Berita
            </span>
            
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-4 font-outfit">
                <?= sanitize($berita['judul']) ?>
            </h1>

            <div class="flex items-center text-gray-500 text-sm gap-4">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span><?= formatTanggal($berita['created_at']) ?></span>
                </div>
                <?php if ($berita['author_name']): ?>
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="capitalize"><?= sanitize($berita['author_name']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <?php if ($berita['path_gambar']): ?>
            <div class="mb-8 rounded-xl overflow-hidden shadow-md">
                <img src="<?= asset('/') ?><?= $berita['path_gambar'] ?>" 
                     alt="<?= sanitize($berita['judul']) ?>"
                     class="w-full h-auto object-cover max-h-[500px]">
            </div>
        <?php endif; ?>

        <div class="content-body text-gray-800 text-lg leading-relaxed">
            <?= $berita['konten_teks'] ?>
        </div>

    </article>

    <div class="mt-12 pt-8 border-t text-center">
        <a href="<?= url('/berita') ?>" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg transition">
            Lihat Berita Lainnya
        </a>
    </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>