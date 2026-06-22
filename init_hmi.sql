-- ============================================================
-- HMI Komisariat IT Telkom - Database Initialization Script
-- Target: MySQL 5.7+ / MariaDB 10.3+ (XAMPP)
-- Admin Login: admin / password
-- ============================================================

DROP DATABASE IF EXISTS hmi_ittelkom;

CREATE DATABASE hmi_ittelkom 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE hmi_ittelkom;

-- ============================================================
-- 1. USERS TABLE
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'kader') NOT NULL DEFAULT 'kader',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. KADER_PROFILES
-- ============================================================
CREATE TABLE kader_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    nama_lengkap VARCHAR(150) NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    program_studi VARCHAR(100) NOT NULL,
    angkatan YEAR NOT NULL,
    status_kaderisasi ENUM('pending', 'lk1_registered', 'lk1_lulus', 'aktif') NOT NULL DEFAULT 'pending',
    foto_path VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 3. BERITA_EVENTS
-- ============================================================
CREATE TABLE berita_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe_post ENUM('berita', 'event') NOT NULL,
    judul VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    konten_teks TEXT NOT NULL,
    tanggal_pelaksanaan DATE DEFAULT NULL,
    path_gambar VARCHAR(255) DEFAULT NULL,
    author_id INT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- 4. DOKUMEN
-- ============================================================
CREATE TABLE dokumen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    kategori VARCHAR(100) DEFAULT 'Umum',
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL DEFAULT 0,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 5. HOTLINE_MESSAGES
-- ============================================================
CREATE TABLE hotline_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengirim VARCHAR(150) NOT NULL,
    email_pengirim VARCHAR(100) NOT NULL,
    subjek VARCHAR(255) NOT NULL,
    pesan TEXT NOT NULL,
    lampiran_path VARCHAR(255) DEFAULT NULL,
    status ENUM('baru', 'dibaca', 'selesai') NOT NULL DEFAULT 'baru',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 6. LMS_MODULES
-- ============================================================
CREATE TABLE lms_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_modul VARCHAR(150) NOT NULL,
    deskripsi TEXT DEFAULT NULL,
    urutan INT NOT NULL DEFAULT 0,
    passing_score INT NOT NULL DEFAULT 70
) ENGINE=InnoDB;

-- ============================================================
-- 7. LMS_MATERIALS
-- ============================================================
CREATE TABLE lms_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    judul_materi VARCHAR(255) NOT NULL,
    tipe_konten ENUM('teks', 'pdf', 'video', 'slide') NOT NULL DEFAULT 'teks',
    konten_teks TEXT DEFAULT NULL,
    url_konten VARCHAR(500) DEFAULT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (module_id) REFERENCES lms_modules(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 8. LMS_QUESTIONS
-- ============================================================
CREATE TABLE lms_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    pertanyaan TEXT NOT NULL,
    opsi_a VARCHAR(500) NOT NULL,
    opsi_b VARCHAR(500) NOT NULL,
    opsi_c VARCHAR(500) NOT NULL,
    opsi_d VARCHAR(500) NOT NULL,
    jawaban_benar ENUM('A', 'B', 'C', 'D') NOT NULL,
    FOREIGN KEY (module_id) REFERENCES lms_modules(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 9. LMS_SCORES
-- ============================================================
CREATE TABLE lms_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    module_id INT NOT NULL,
    skor INT NOT NULL DEFAULT 0,
    lulus TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES lms_modules(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 10. PENGURUS - Organigram Kepengurusan (CMS)
-- ============================================================
CREATE TABLE pengurus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    jabatan VARCHAR(150) NOT NULL,
    bidang VARCHAR(100) DEFAULT NULL,
    level ENUM('top', 'pao', 'middle', 'staff') NOT NULL DEFAULT 'staff',
    urutan INT NOT NULL DEFAULT 0,
    foto_path VARCHAR(255) DEFAULT NULL,
    periode VARCHAR(20) NOT NULL DEFAULT '2024-2025',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 11. SITE_SETTINGS - Key-Value untuk CMS
-- ============================================================
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    setting_type ENUM('text', 'textarea', 'image', 'color', 'url') NOT NULL DEFAULT 'text',
    label VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) NOT NULL DEFAULT 'umum',
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin default (username: admin, password: password)
INSERT INTO users (username, password_hash, email, role) VALUES 
('admin', '$2y$10$Y8i8km0v5/6pTOSvGMiD/OcYhEWk.JzRjDr9ggd/Sb51sUb0.2y2e', 'admin@hmiittelkom.com', 'admin');

-- 5 Modul LMS
INSERT INTO lms_modules (nama_modul, deskripsi, urutan, passing_score) VALUES
('Sejarah Perjuangan HMI', 'Tinjauan komprehensif peradaban Arab Pra-Islam, dakwah Nabi Muhammad SAW, hingga kronologi berdirinya HMI oleh Lafran Pane pada 5 Februari 1947.', 1, 70),
('Konstitusi HMI', 'Membedah Anggaran Dasar (AD) dan Anggaran Rumah Tangga (ART) HMI secara mendalam.', 2, 70),
('Nilai Dasar Perjuangan (NDP)', 'Jantung filosofis dan teologis HMI yang dirumuskan oleh Nurcholish Madjid.', 3, 70),
('Kepemimpinan Manajemen Organisasi (KMO)', 'Tipologi kepemimpinan transformasional vs transaksional, dinamika organisasi.', 4, 70),
('Mission HMI', 'Konsepsi Insan Cita: Insan Akademis, Insan Pencipta, dan Insan Pengabdi.', 5, 70);

-- Sample Questions (column names match PHP code)
INSERT INTO lms_questions (module_id, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar) VALUES
(1, 'Kapan HMI secara resmi didirikan?', '5 Februari 1945', '5 Februari 1947', '17 Agustus 1947', '14 Rabiul Awwal 1367 H', 'B'),
(1, 'Siapa pemrakarsa utama berdirinya HMI?', 'Soekarno', 'Nurcholish Madjid', 'Lafran Pane', 'Kartono Zarkasy', 'C'),
(1, 'Di mana HMI pertama kali didirikan?', 'Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta (STI)', 'D'),
(1, 'Julukan apa yang diberikan Jenderal Sudirman kepada HMI?', 'Harapan Masyarakat Indonesia', 'Pejuang Kemerdekaan', 'Garda Terdepan Bangsa', 'Benteng Terakhir Islam', 'A'),
(1, 'Corps Mahasiswa (CM) HMI dibentuk untuk menumpas pemberontakan di mana?', 'Bandung', 'Surabaya', 'Madiun', 'Semarang', 'C'),
(2, 'Apa singkatan AD dalam konteks konstitusi HMI?', 'Aturan Dasar', 'Anggaran Dasar', 'Acuan Dasar', 'Asas Dasar', 'B'),
(2, 'Entitas tertinggi dalam struktur organisasi HMI adalah?', 'Pengurus Cabang', 'Pengurus Komisariat', 'Pengurus Besar (PB HMI)', 'Badan Koordinasi (Badko)', 'C'),
(2, 'Tingkat Komisariat HMI berada di level apa?', 'Nasional', 'Wilayah', 'Kabupaten/Kota', 'Perguruan Tinggi/Fakultas', 'D'),
(2, 'ART merupakan singkatan dari?', 'Aturan Rumah Tangga', 'Anggaran Rumah Tangga', 'Acuan Rumah Tangga', 'Asas Rumah Tangga', 'B'),
(3, 'Siapa perumus utama NDP HMI?', 'Lafran Pane', 'Nurcholish Madjid', 'Bagas Kurniawan', 'Dahlan Husein', 'B'),
(3, 'Bab pertama NDP membahas tentang?', 'Kepemimpinan', 'Kemanusiaan', 'Dasar-Dasar Kepercayaan', 'Kemasyarakatan', 'C'),
(3, 'NDP HMI memakan waktu diskusi hingga berapa jam dalam pelatihan luring?', '4 jam', '8 jam', '14 jam', '24 jam', 'C'),
(4, 'KMO mendefinisikan kepemimpinan sebagai seni untuk?', 'Memerintah orang lain', 'Mempengaruhi orang agar bekerja kolektif', 'Mengatur keuangan', 'Membuat kebijakan', 'B'),
(4, 'Model kepemimpinan transformasional bertujuan?', 'Transaksional', 'Otoriter', 'Mengubah visi menjadi realitas', 'Laissez-faire', 'C'),
(4, 'Sindrom atasan terlalu melindungi bawahan disebut?', 'Micro-managing', 'Over-protective', 'Authoritarian', 'Bureaucratic', 'B'),
(5, 'Konsepsi ideal seorang kader HMI disebut?', 'Insan Kamil', 'Insan Cita', 'Insan Utama', 'Insan Muda', 'B'),
(5, 'Profil Insan Akademis menekankan pada?', 'Kewirausahaan', 'Pengabdian masyarakat', 'Pendidikan tinggi dan pengetahuan luas', 'Kepemimpinan politik', 'C'),
(5, 'Insan Pengabdi mewakafkan ilmunya untuk?', 'Kepentingan pribadi', 'Kesejahteraan rakyat banyak', 'Organisasi saja', 'Akademik saja', 'B');

-- Sample Berita (with placeholder images)
INSERT INTO berita_events (tipe_post, judul, slug, konten_teks, path_gambar, author_id) VALUES
('berita', 'Malam Puncak Dies Natalis HMI ke-79', 'malam-puncak-dies-natalis-hmi-ke-79', 
'<p>Himpunan Mahasiswa Islam (HMI) Komisariat IT Telkom menggelar perayaan Dies Natalis ke-79 yang berlangsung meriah di Aula Kampus. Acara ini dihadiri oleh seluruh pengurus, kader aktif, serta alumni yang turut memeriahkan rangkaian kegiatan.</p><p>Ketua Komisariat menyampaikan bahwa perayaan ini bukan sekadar seremonial, melainkan momentum refleksi atas perjalanan panjang HMI dalam membina intelektual muslim di lingkungan kampus teknologi.</p>', 
NULL, 1),
('berita', 'Lokakarya Literasi Digital untuk Kader Baru', 'lokakarya-literasi-digital-kader-baru',
'<p>Dalam rangka mempersiapkan kader yang melek teknologi, HMI Komisariat IT Telkom menyelenggarakan lokakarya literasi digital bertema "Navigasi Era Digital dengan Nilai-Nilai Islam". Workshop ini menghadirkan pemateri dari alumni yang kini berkarir di industri teknologi.</p>',
NULL, 1),
('event', 'Latihan Kader I (LK-1) Semester Genap 2026', 'lk1-semester-genap-2026',
'<p>Pendaftaran Latihan Kader I (LK-1) untuk semester genap 2026 telah dibuka. LK-1 merupakan prosesi kaderisasi wajib yang membahas 5 materi fundamental HMI.</p>',
'2026-03-15', 1),
('event', 'Diskusi Panel: Peran Mahasiswa Muslim di Era AI', 'diskusi-panel-peran-mahasiswa-muslim-era-ai',
'<p>HMI Komisariat IT Telkom mengundang seluruh mahasiswa untuk hadir dalam diskusi panel bertema "Peran Mahasiswa Muslim di Era Kecerdasan Buatan".</p>',
'2026-04-10', 1);

-- Pengurus Seed Data (Organigram)
INSERT INTO pengurus (nama, jabatan, bidang, level, urutan, periode) VALUES
-- Top Management
('Ahmad Rizki Fauzan', 'Ketua Umum', NULL, 'top', 1, '2024-2025'),
('Siti Nurhaliza', 'Sekretaris Umum', NULL, 'top', 2, '2024-2025'),
('Muhammad Farhan', 'Bendahara Umum', NULL, 'top', 3, '2024-2025'),
-- PAO
('Dina Rahmawati', 'Pembantu Ahli Organisasi (PAO)', NULL, 'pao', 1, '2024-2025'),
-- Middle Management (Kepala Bidang + Wasekum)
('Budi Santoso', 'Kepala Bidang', 'Pembinaan Anggota', 'middle', 1, '2024-2025'),
('Rina Kartika', 'Wasekum Bidang', 'Pembinaan Anggota', 'middle', 2, '2024-2025'),
('Andi Prasetyo', 'Kepala Bidang', 'Kewirausahaan', 'middle', 3, '2024-2025'),
('Lestari Dewi', 'Wasekum Bidang', 'Kewirausahaan', 'middle', 4, '2024-2025'),
('Hendra Wijaya', 'Kepala Bidang', 'Hubungan Masyarakat', 'middle', 5, '2024-2025'),
('Putri Amelia', 'Wasekum Bidang', 'Hubungan Masyarakat', 'middle', 6, '2024-2025'),
('Reza Fahlevi', 'Kepala Bidang', 'Litbang', 'middle', 7, '2024-2025'),
('Nadia Safitri', 'Wasekum Bidang', 'Litbang', 'middle', 8, '2024-2025'),
('Irfan Maulana', 'Kepala Bidang', 'Advokasi', 'middle', 9, '2024-2025'),
('Zahra Kamilah', 'Wasekum Bidang', 'Advokasi', 'middle', 10, '2024-2025'),
('Galih Permana', 'Kepala Bidang', 'Media & IT', 'middle', 11, '2024-2025'),
('Anisa Rahmah', 'Wasekum Bidang', 'Media & IT', 'middle', 12, '2024-2025'),
-- Staff
('Faisal Rahman', 'Staff', 'Pembinaan Anggota', 'staff', 1, '2024-2025'),
('Mega Puspita', 'Staff', 'Pembinaan Anggota', 'staff', 2, '2024-2025'),
('Yoga Pratama', 'Staff', 'Kewirausahaan', 'staff', 3, '2024-2025'),
('Dewi Anggraini', 'Staff', 'Hubungan Masyarakat', 'staff', 4, '2024-2025'),
('Arif Hidayat', 'Staff', 'Litbang', 'staff', 5, '2024-2025'),
('Sari Mulyani', 'Staff', 'Advokasi', 'staff', 6, '2024-2025'),
('Rizal Aditya', 'Staff', 'Media & IT', 'staff', 7, '2024-2025');

-- Site Settings Seed
INSERT INTO site_settings (setting_key, setting_value, setting_type, label, kategori) VALUES
('hero_title', 'Rumah Bagi Intelektual Muslim', 'text', 'Hero Title', 'beranda'),
('hero_subtitle', 'HMI Komisariat IT Telkom berkomitmen mempertahankan semangat keindonesiaan dan keislaman, membina generasi akademis, pencipta, dan pengabdi di lingkungan kampus teknologi.', 'textarea', 'Hero Subtitle', 'beranda'),
('hero_image', NULL, 'image', 'Hero Background Image', 'beranda'),
('about_image', NULL, 'image', 'About Section Image', 'profil'),
('logo_image', NULL, 'image', 'Logo Organisasi', 'umum'),
('contact_email', 'hmi.ittelkom@gmail.com', 'text', 'Email Kontak', 'umum'),
('contact_phone', '+62 812-3456-7890', 'text', 'No. Telepon', 'umum'),
('contact_address', 'Kampus IT Telkom, Jl. Telekomunikasi No.1, Bandung 40257', 'textarea', 'Alamat', 'umum'),
('instagram_url', 'https://instagram.com/hmi_ittelkom', 'url', 'Instagram', 'sosmed'),
('youtube_url', '', 'url', 'YouTube', 'sosmed'),
('periode_aktif', '2024-2025', 'text', 'Periode Kepengurusan Aktif', 'umum');
