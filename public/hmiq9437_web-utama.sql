-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 22, 2026 at 01:59 PM
-- Server version: 10.11.17-MariaDB-cll-lve
-- PHP Version: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hmiq9437_web-utama`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita_events`
--

CREATE TABLE `berita_events` (
  `id` int(11) NOT NULL,
  `tipe_post` enum('berita','event') NOT NULL,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `konten_teks` text NOT NULL,
  `tanggal_pelaksanaan` date DEFAULT NULL,
  `path_gambar` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `berita_events`
--

INSERT INTO `berita_events` (`id`, `tipe_post`, `judul`, `slug`, `konten_teks`, `tanggal_pelaksanaan`, `path_gambar`, `author_id`, `created_at`) VALUES
(1, 'berita', 'Malam Puncak Dies Natalis HMI ke-79', 'malam-puncak-dies-natalis-hmi-ke-79', '<p>Himpunan Mahasiswa Islam (HMI) Komisariat IT Telkom menggelar perayaan Dies Natalis ke-79 yang berlangsung meriah di Aula Kampus. Acara ini dihadiri oleh seluruh pengurus, kader aktif, serta alumni yang turut memeriahkan rangkaian kegiatan.</p><p>Ketua Komisariat menyampaikan bahwa perayaan ini bukan sekadar seremonial, melainkan momentum refleksi atas perjalanan panjang HMI dalam membina intelektual muslim di lingkungan kampus teknologi.</p>', NULL, NULL, 1, '2026-02-14 11:51:24'),
(2, 'berita', 'Lokakarya Literasi Digital untuk Kader Baru', 'lokakarya-literasi-digital-kader-baru', '<p>Dalam rangka mempersiapkan kader yang melek teknologi, HMI Komisariat IT Telkom menyelenggarakan lokakarya literasi digital bertema \"Navigasi Era Digital dengan Nilai-Nilai Islam\". Workshop ini menghadirkan pemateri dari alumni yang kini berkarir di industri teknologi.</p>', NULL, NULL, 1, '2026-02-14 11:51:24'),
(3, 'event', 'Latihan Kader I (LK-1) Semester Genap 2026', 'lk1-semester-genap-2026', '<p>Pendaftaran Latihan Kader I (LK-1) untuk semester genap 2026 telah dibuka. LK-1 merupakan prosesi kaderisasi wajib yang membahas 5 materi fundamental HMI.</p>', NULL, '2026-03-15', 1, '2026-02-14 11:51:24'),
(4, 'event', 'Diskusi Panel: Peran Mahasiswa Muslim di Era AI', 'diskusi-panel-peran-mahasiswa-muslim-era-ai', '<p>HMI Komisariat IT Telkom mengundang seluruh mahasiswa untuk hadir dalam diskusi panel bertema \"Peran Mahasiswa Muslim di Era Kecerdasan Buatan\".</p>', NULL, '2026-04-10', 1, '2026-02-14 11:51:24'),
(6, 'berita', 'Minimnya Kader Kreatif Bikin HMI Jalan Ditempat -Ardhan 2019', 'minimnya-kader-kreatif-bikin-hmi-jalan-ditempat', '<div class=\"text-gray-800 leading-relaxed space-y-6\">\r\n    <p class=\"text-lg italic text-gray-600 mb-8 border-l-4 border-hmi-green pl-4\">\r\n        \"Eh, atau Malah Jalan Mundur ya?\"\r\n    </p>\r\n\r\n    <p>\r\n        Kadang gue mikir, HMI itu lagi jalan... tapi kok rasanya nggak ke mana-mana. \r\n        Bukan karena nggak ada kader. Bukan juga karena nggak ada struktur. \r\n        <span class=\"font-bold bg-yellow-100 px-1\">Tapi karena terlalu nyaman dengan pola lama.</span>\r\n    </p>\r\n\r\n    <p>\r\n        HMI itu organisasi pergerakan, katanya. <br>\r\n        Organisasi intelektual, katanya juga. <br>\r\n        Tapi kenapa geraknya gitu-gitu aja?\r\n    </p>\r\n\r\n    <div class=\"bg-gray-50 p-6 rounded-lg border border-gray-200 shadow-sm my-6\">\r\n        <ul class=\"space-y-3 list-none\">\r\n            <li class=\"flex items-start gap-2\">\r\n                <span class=\"text-green-700 font-bold\">➤</span> \r\n                <span>Diskusi? <span class=\"text-gray-500\">Ya diskusi lagi.</span></span>\r\n            </li>\r\n            <li class=\"flex items-start gap-2\">\r\n                <span class=\"text-green-700 font-bold\">➤</span>\r\n                <span>Aksi? <span class=\"text-gray-500\">Ya aksi lagi, dengan pola yang sama.</span></span>\r\n            </li>\r\n            <li class=\"flex items-start gap-2\">\r\n                <span class=\"text-green-700 font-bold\">➤</span>\r\n                <span>Program? <span class=\"text-gray-500\">Ya gitu-gitu aja.</span></span>\r\n            </li>\r\n        </ul>\r\n        <p class=\"mt-4 text-sm text-gray-500 italic border-t pt-3\">\r\n            Gitu-gitu aja pun belum tentu jalan semua.\r\n        </p>\r\n    </div>\r\n\r\n    <p class=\"font-medium\">\r\n        Bukan salah diskusinya. <br>\r\n        Bukan salah aksinya.\r\n    </p>\r\n\r\n    <div class=\"p-4 bg-red-50 border-l-4 border-red-600 rounded-r-lg\">\r\n        <p class=\"text-xl font-bold text-red-800 uppercase tracking', NULL, NULL, 1, '2026-02-14 23:55:25'),
(7, 'event', 'LK 1 HMI IT TELKOM', 'lk-1-hmi-it-telkom', '-', '2026-06-05', NULL, 1, '2026-06-09 13:54:15');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT 'Umum',
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL DEFAULT 0,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dokumen`
--

INSERT INTO `dokumen` (`id`, `judul`, `kategori`, `file_path`, `file_type`, `file_size`, `uploaded_at`) VALUES
(1, 'SEJARAH PERJUANGAN HMI', 'Pedoman', 'assets/uploads/dokumen/hmi_699174eaa62ba_1771140330.pdf', 'pdf', 83081, '2026-02-15 14:25:30'),
(2, 'SEJARAH PERADABAN ISLAM', 'Umum', 'assets/uploads/dokumen/hmi_6991750b498db_1771140363.pdf', 'pdf', 7307155, '2026-02-15 14:26:03'),
(3, 'ILMU TAJWID', 'Umum', 'assets/uploads/dokumen/hmi_6991752e05897_1771140398.pdf', 'pdf', 5052127, '2026-02-15 14:26:38'),
(4, 'ISLAM MAZHAB HMI', 'Umum', 'assets/uploads/dokumen/hmi_69917ac36f05b_1771141827.pdf', 'pdf', 9242919, '2026-02-15 14:50:27'),
(5, 'LAFRAN PANE', 'Umum', 'assets/uploads/dokumen/hmi_69917b06441d5_1771141894.pdf', 'pdf', 5420373, '2026-02-15 14:51:34');

-- --------------------------------------------------------

--
-- Table structure for table `hotline_messages`
--

CREATE TABLE `hotline_messages` (
  `id` int(11) NOT NULL,
  `nama_pengirim` varchar(150) NOT NULL,
  `email_pengirim` varchar(100) NOT NULL,
  `subjek` varchar(255) NOT NULL,
  `pesan` text NOT NULL,
  `lampiran_path` varchar(255) DEFAULT NULL,
  `status` enum('baru','dibaca','selesai') NOT NULL DEFAULT 'baru',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotline_messages`
--

INSERT INTO `hotline_messages` (`id`, `nama_pengirim`, `email_pengirim`, `subjek`, `pesan`, `lampiran_path`, `status`, `created_at`) VALUES
(2, 'htuhdxuzsv', 'teqomdtw@immenseignite.info', 'erklnuudus', 'qgohtirohskiiwooootjtfmypinxnu', NULL, 'baru', '2026-06-14 02:25:39'),
(3, 'Joanna Riggs', 'joriggsvideo3@gmail.com', 'Explainer Video for your website?', 'Hi,\r\n\r\nI just visited hmiittelkom.com and wondered if you\'d ever thought about having an engaging video to explain what you do?\r\n\r\nOur videos cost just $195 (USD) for a 30 second video ($239 for 60 seconds) and include a full script, voice-over and video.\r\n\r\nI can show you some previous videos we\'ve done if you want me to send some over. Let me know if you\'re interested in seeing samples of our previous work.\r\n\r\nRegards,\r\nJoanna', NULL, 'baru', '2026-06-18 11:50:42');

-- --------------------------------------------------------

--
-- Table structure for table `kader_profiles`
--

CREATE TABLE `kader_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `program_studi` varchar(100) NOT NULL,
  `angkatan` year(4) NOT NULL,
  `status_kaderisasi` enum('pending','lk1_registered','lk1_lulus','aktif') NOT NULL DEFAULT 'pending',
  `foto_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kader_profiles`
--

INSERT INTO `kader_profiles` (`id`, `user_id`, `nama_lengkap`, `nim`, `program_studi`, `angkatan`, `status_kaderisasi`, `foto_path`, `created_at`) VALUES
(1, 2, 'Eka Yahya Iskandar Syah', '1301190343', 'Informatika', '2019', 'aktif', NULL, '2026-02-14 19:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `lms_materials`
--

CREATE TABLE `lms_materials` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `judul_materi` varchar(255) NOT NULL,
  `tipe_konten` enum('teks','pdf','video','slide') NOT NULL DEFAULT 'teks',
  `konten_teks` text DEFAULT NULL,
  `url_konten` varchar(500) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lms_materials`
--

INSERT INTO `lms_materials` (`id`, `module_id`, `judul_materi`, `tipe_konten`, `konten_teks`, `url_konten`, `file_path`) VALUES
(1, 1, 'Sejarah Peradaban Islam', 'pdf', NULL, NULL, 'assets/uploads/lms/hmi_6990a5d5b209e_1771087317.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `lms_modules`
--

CREATE TABLE `lms_modules` (
  `id` int(11) NOT NULL,
  `nama_modul` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `passing_score` int(11) NOT NULL DEFAULT 70
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lms_modules`
--

INSERT INTO `lms_modules` (`id`, `nama_modul`, `deskripsi`, `urutan`, `passing_score`) VALUES
(1, 'Sejarah Perjuangan HMI', 'Tinjauan komprehensif peradaban Arab Pra-Islam, dakwah Nabi Muhammad SAW, hingga kronologi berdirinya HMI oleh Lafran Pane pada 5 Februari 1947.', 1, 70),
(2, 'Konstitusi HMI', 'Membedah Anggaran Dasar (AD) dan Anggaran Rumah Tangga (ART) HMI secara mendalam.', 2, 70),
(3, 'Nilai Dasar Perjuangan (NDP)', 'Jantung filosofis dan teologis HMI yang dirumuskan oleh Nurcholish Madjid.', 3, 70),
(4, 'Kepemimpinan Manajemen Organisasi (KMO)', 'Tipologi kepemimpinan transformasional vs transaksional, dinamika organisasi.', 4, 70),
(5, 'Mission HMI', 'Konsepsi Insan Cita: Insan Akademis, Insan Pencipta, dan Insan Pengabdi.', 5, 70);

-- --------------------------------------------------------

--
-- Table structure for table `lms_questions`
--

CREATE TABLE `lms_questions` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `opsi_a` varchar(500) NOT NULL,
  `opsi_b` varchar(500) NOT NULL,
  `opsi_c` varchar(500) NOT NULL,
  `opsi_d` varchar(500) NOT NULL,
  `jawaban_benar` enum('A','B','C','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lms_questions`
--

INSERT INTO `lms_questions` (`id`, `module_id`, `pertanyaan`, `opsi_a`, `opsi_b`, `opsi_c`, `opsi_d`, `jawaban_benar`) VALUES
(1, 1, 'Kapan HMI secara resmi didirikan?', '5 Februari 1945', '5 Februari 1947', '17 Agustus 1947', '14 Rabiul Awwal 1367 H', 'B'),
(2, 1, 'Siapa pemrakarsa utama berdirinya HMI?', 'Soekarno', 'Nurcholish Madjid', 'Lafran Pane', 'Kartono Zarkasy', 'C'),
(3, 1, 'Di mana HMI pertama kali didirikan?', 'Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta (STI)', 'D'),
(4, 1, 'Julukan apa yang diberikan Jenderal Sudirman kepada HMI?', 'Harapan Masyarakat Indonesia', 'Pejuang Kemerdekaan', 'Garda Terdepan Bangsa', 'Benteng Terakhir Islam', 'A'),
(5, 1, 'Corps Mahasiswa (CM) HMI dibentuk untuk menumpas pemberontakan di mana?', 'Bandung', 'Surabaya', 'Madiun', 'Semarang', 'C'),
(6, 2, 'Apa singkatan AD dalam konteks konstitusi HMI?', 'Aturan Dasar', 'Anggaran Dasar', 'Acuan Dasar', 'Asas Dasar', 'B'),
(7, 2, 'Entitas tertinggi dalam struktur organisasi HMI adalah?', 'Pengurus Cabang', 'Pengurus Komisariat', 'Pengurus Besar (PB HMI)', 'Badan Koordinasi (Badko)', 'C'),
(8, 2, 'Tingkat Komisariat HMI berada di level apa?', 'Nasional', 'Wilayah', 'Kabupaten/Kota', 'Perguruan Tinggi/Fakultas', 'D'),
(9, 2, 'ART merupakan singkatan dari?', 'Aturan Rumah Tangga', 'Anggaran Rumah Tangga', 'Acuan Rumah Tangga', 'Asas Rumah Tangga', 'B'),
(10, 3, 'Siapa perumus utama NDP HMI?', 'Lafran Pane', 'Nurcholish Madjid', 'Bagas Kurniawan', 'Dahlan Husein', 'B'),
(11, 3, 'Bab pertama NDP membahas tentang?', 'Kepemimpinan', 'Kemanusiaan', 'Dasar-Dasar Kepercayaan', 'Kemasyarakatan', 'C'),
(12, 3, 'NDP HMI memakan waktu diskusi hingga berapa jam dalam pelatihan luring?', '4 jam', '8 jam', '14 jam', '24 jam', 'C'),
(13, 4, 'KMO mendefinisikan kepemimpinan sebagai seni untuk?', 'Memerintah orang lain', 'Mempengaruhi orang agar bekerja kolektif', 'Mengatur keuangan', 'Membuat kebijakan', 'B'),
(14, 4, 'Model kepemimpinan transformasional bertujuan?', 'Transaksional', 'Otoriter', 'Mengubah visi menjadi realitas', 'Laissez-faire', 'C'),
(15, 4, 'Sindrom atasan terlalu melindungi bawahan disebut?', 'Micro-managing', 'Over-protective', 'Authoritarian', 'Bureaucratic', 'B'),
(16, 5, 'Konsepsi ideal seorang kader HMI disebut?', 'Insan Kamil', 'Insan Cita', 'Insan Utama', 'Insan Muda', 'B'),
(17, 5, 'Profil Insan Akademis menekankan pada?', 'Kewirausahaan', 'Pengabdian masyarakat', 'Pendidikan tinggi dan pengetahuan luas', 'Kepemimpinan politik', 'C'),
(18, 5, 'Insan Pengabdi mewakafkan ilmunya untuk?', 'Kepentingan pribadi', 'Kesejahteraan rakyat banyak', 'Organisasi saja', 'Akademik saja', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `lms_scores`
--

CREATE TABLE `lms_scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `skor` int(11) NOT NULL DEFAULT 0,
  `lulus` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lms_scores`
--

INSERT INTO `lms_scores` (`id`, `user_id`, `module_id`, `skor`, `lulus`, `created_at`) VALUES
(1, 2, 1, 60, 0, '2026-02-14 23:42:42'),
(2, 2, 1, 100, 1, '2026-02-15 23:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `pengurus`
--

CREATE TABLE `pengurus` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jabatan` varchar(150) NOT NULL,
  `bidang` varchar(100) DEFAULT NULL,
  `level` enum('top','pao','middle','staff') NOT NULL DEFAULT 'staff',
  `urutan` int(11) NOT NULL DEFAULT 0,
  `foto_path` varchar(255) DEFAULT NULL,
  `periode` varchar(20) NOT NULL DEFAULT '2024-2025',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengurus`
--

INSERT INTO `pengurus` (`id`, `nama`, `jabatan`, `bidang`, `level`, `urutan`, `foto_path`, `periode`, `is_active`, `created_at`) VALUES
(24, 'Eka Yahya Iskandar Syah', 'Ketua Umum', NULL, 'top', 1, 'assets/uploads/pengurus/hmi_69901d2e88faf_1771052334.png', '2023-2024', 1, '2026-02-14 13:44:41'),
(26, 'Asyhidiki Malik', 'Bendahara Umum', NULL, 'top', 2, NULL, '2023-2024', 1, '2026-02-14 13:55:47'),
(27, 'Dean Haikkal Susetyo', 'Sekretaris Umum', NULL, 'top', 0, NULL, '2023-2024', 1, '2026-02-14 13:57:00'),
(28, 'Qowiyyun Tigin Syahidan', 'PAO', NULL, 'pao', 4, NULL, '2023-2024', 1, '2026-02-14 14:00:52');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','image','color','url') NOT NULL DEFAULT 'text',
  `label` varchar(150) NOT NULL,
  `kategori` varchar(50) NOT NULL DEFAULT 'umum',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `label`, `kategori`, `updated_at`) VALUES
(1, 'hero_title', 'Rumah Bagi Intelektual Muslim', 'text', 'Hero Title', 'beranda', '2026-02-14 11:51:24'),
(2, 'hero_subtitle', 'HMI Komisariat IT Telkom berkomitmen mempertahankan semangat keindonesiaan dan keislaman, membina generasi akademis, pencipta, dan pengabdi di lingkungan kampus teknologi.', 'textarea', 'Hero Subtitle', 'beranda', '2026-02-14 11:51:24'),
(3, 'hero_image', 'assets/uploads/settings/hmi_6990a466aab81_1771086950.jpg', 'image', 'Hero Background Image', 'beranda', '2026-02-14 23:35:50'),
(4, 'about_image', 'assets/uploads/settings/hmi_6990aa0f86152_1771088399.png', 'image', 'About Section Image', 'profil', '2026-02-14 23:59:59'),
(5, 'logo_image', 'assets/uploads/settings/hmi_6990aa0f875f2_1771088399.png', 'image', 'Logo Organisasi', 'umum', '2026-02-14 23:59:59'),
(6, 'contact_email', 'hmi.ittelkom@gmail.com', 'text', 'Email Kontak', 'umum', '2026-02-14 11:51:24'),
(7, 'contact_phone', '+62 812-3456-7890', 'text', 'No. Telepon', 'umum', '2026-02-14 11:51:24'),
(8, 'contact_address', 'Kampus IT Telkom, Jl. Telekomunikasi No.1, Bandung 40257', 'textarea', 'Alamat', 'umum', '2026-02-14 11:51:24'),
(9, 'instagram_url', 'https://instagram.com/hmi_ittelkom', 'url', 'Instagram', 'sosmed', '2026-02-14 11:51:24'),
(10, 'youtube_url', '', 'url', 'YouTube', 'sosmed', '2026-02-14 11:51:24'),
(11, 'periode_aktif', '2023-2024', 'text', 'Periode Kepengurusan Aktif', 'umum', '2026-02-14 14:00:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','kader') NOT NULL DEFAULT 'kader',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$Y8i8km0v5/6pTOSvGMiD/OcYhEWk.JzRjDr9ggd/Sb51sUb0.2y2e', 'admin@hmiittelkom.com', 'admin', '2026-02-14 11:51:24'),
(2, 'ekayahya', '$2y$10$tWdx3xAuzpI5n6XKZaHZVuPHEFapUxZa7rM8yUCA7DHircY8aYZx2', 'ekayahya@student.telkomunviersity.ac.id', 'kader', '2026-02-14 19:28:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita_events`
--
ALTER TABLE `berita_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotline_messages`
--
ALTER TABLE `hotline_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kader_profiles`
--
ALTER TABLE `kader_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indexes for table `lms_materials`
--
ALTER TABLE `lms_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `lms_modules`
--
ALTER TABLE `lms_modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lms_questions`
--
ALTER TABLE `lms_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `lms_scores`
--
ALTER TABLE `lms_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita_events`
--
ALTER TABLE `berita_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hotline_messages`
--
ALTER TABLE `hotline_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kader_profiles`
--
ALTER TABLE `kader_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lms_materials`
--
ALTER TABLE `lms_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lms_modules`
--
ALTER TABLE `lms_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lms_questions`
--
ALTER TABLE `lms_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lms_scores`
--
ALTER TABLE `lms_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengurus`
--
ALTER TABLE `pengurus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `berita_events`
--
ALTER TABLE `berita_events`
  ADD CONSTRAINT `berita_events_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kader_profiles`
--
ALTER TABLE `kader_profiles`
  ADD CONSTRAINT `kader_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lms_materials`
--
ALTER TABLE `lms_materials`
  ADD CONSTRAINT `lms_materials_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `lms_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lms_questions`
--
ALTER TABLE `lms_questions`
  ADD CONSTRAINT `lms_questions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `lms_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lms_scores`
--
ALTER TABLE `lms_scores`
  ADD CONSTRAINT `lms_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lms_scores_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `lms_modules` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
