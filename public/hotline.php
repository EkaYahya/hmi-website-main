<?php
/**
 * HMI IT Telkom - HOTLINE (Formulir Pengaduan/Kontak)
 */
$pageTitle = 'Hotline';
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!validateCSRF($_POST['csrf_token'] ?? '')) {
            flash('error', 'Sesi tidak valid.', 'error');
        } else {
            $nama = trim($_POST['nama'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subjek = trim($_POST['subjek'] ?? '');
            $pesan = trim($_POST['pesan'] ?? '');

            if (empty($nama) || empty($email) || empty($subjek) || empty($pesan)) {
                flash('error', 'Semua field wajib diisi.', 'error');
            } else {
                $lampiranPath = null;
                if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
                    $upload = uploadFile($_FILES['lampiran'], 'assets/uploads/hotline', ['doc', 'docx', 'pdf', 'jpg', 'jpeg', 'png'], 10485760);
                    if ($upload['success']) {
                        $lampiranPath = $upload['path'];
                    } else {
                        flash('error', $upload['error'], 'error');
                        redirect(url('/hotline'));
                    }
                }

                $pdo = getDB();
                $stmt = $pdo->prepare("INSERT INTO hotline_messages (nama_pengirim, email_pengirim, subjek, pesan, lampiran_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nama, $email, $subjek, $pesan, $lampiranPath]);

                flash('success', 'Pesan Anda telah terkirim! Tim kami akan menghubungi Anda segera.', 'success');
                redirect(url('/hotline'));
            }
        }
    } catch (PDOException $e) {
        flash('error', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
    }
    redirect(url('/hotline'));
}
?>
<div style="height:72px;"></div>
<section style="background:linear-gradient(135deg,#0D3B13,#121212);padding:60px 24px;text-align:center;">
    <h1 style="color:white;font-size:2.5rem;margin-bottom:12px;">💬 <span style="color:#FFD700;">Hotline</span></h1>
    <p style="color:rgba(255,255,255,0.7);max-width:600px;margin:0 auto;">Pertanyaan seputar rekrutmen, laporan kendala
        teknis, atau masukan untuk organisasi.</p>
</section>
<section class="section">
    <div class="container" style="max-width:640px;">
        <?= renderFlash('success') ?>
        <?= renderFlash('error') ?>

        <div style="background:white;border-radius:16px;padding:32px;box-shadow:0 4px 15px rgba(0,0,0,0.06);">
            <form method="POST" action="" enctype="multipart/form-data">
                <?= csrfField() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" class="form-input" placeholder="Nama Anda" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-input" placeholder="email@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Subjek *</label>
                    <input type="text" name="subjek" class="form-input" placeholder="Topik pesan Anda" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Pesan *</label>
                    <textarea name="pesan" class="form-input" rows="5" placeholder="Tuliskan pesan Anda di sini..."
                        required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Lampiran (opsional)</label>
                    <input type="file" name="lampiran" class="form-input" accept=".doc,.docx,.pdf,.jpg,.jpeg,.png"
                        style="padding:10px;">
                    <div style="font-size:0.8rem;color:#757575;margin-top:4px;">Format: .doc, .pdf, .jpg — Maks. 10 MB
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"
                    style="width:100%;justify-content:center;border-radius:var(--radius-sm);">
                    Kirim Pesan 📨
                </button>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
