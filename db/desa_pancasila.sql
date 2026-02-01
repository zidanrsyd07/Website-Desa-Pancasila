-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 01, 2026 at 05:41 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `desa_pancasila`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` longtext NOT NULL,
  `konten` longtext,
  `gambar` varchar(255) DEFAULT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'published',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `deskripsi`, `konten`, `gambar`, `penulis`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Gotong Royong Membersihkan Lapangan Desa', 'Warga Desa Pancasila kompak membersihkan lapangan desa', 'Warga Desa Pancasila kompak membersihkan lapangan desa dengan sangat kompak dan senang', 'img_6976e62b862bb0.85948592.jpg', 'Administrator', 'published', 11, '2026-01-26 03:57:31', '2026-02-01 12:56:08');

-- --------------------------------------------------------

--
-- Table structure for table `desa_info`
--

CREATE TABLE `desa_info` (
  `id` int NOT NULL,
  `judul_desa` varchar(100) DEFAULT 'Desa Pancasila',
  `sejarah_desa` longtext,
  `jumlah_penduduk` int DEFAULT '0',
  `jumlah_dusun` int DEFAULT '0',
  `jumlah_rumah_tangga` int DEFAULT '0',
  `alamat` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `desa_info`
--

INSERT INTO `desa_info` (`id`, `judul_desa`, `sejarah_desa`, `jumlah_penduduk`, `jumlah_dusun`, `jumlah_rumah_tangga`, `alamat`, `no_telepon`, `email`, `updated_at`) VALUES
(1, 'Desa Pancasila', 'Desa Pancasila adalah desa yang indah dengan keraifan lokal yang kaya dan potensi alam yang melimpah. Desa ini telah berkembang menjadi pusat ekonomi lokal dengan berbagai kegiatan pertanian dan wisata yang menarik.', 3080, 6, 862, 'Desa Pancasila, Kec. Natar, Kab. Lampung Selatan, Prov. Lampung', '0274-123456', 'admin@desapancasila.id', '2026-01-26 04:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text,
  `gambar` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `tanggal_upload` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `judul`, `deskripsi`, `gambar`, `kategori`, `tanggal_upload`, `updated_at`) VALUES
(1, 'Sosialialisasi SDN Pancasila PHBS Cuci Tangan dengan Benar', 'Kegiatan sosialisasi Perilaku Hidup Bersih dan Sehat (PHBS) di SDN Pancasila dilaksanakan untuk menanamkan kebiasaan cuci tangan yang benar pada siswa sejak usia dini. Kegiatan ini bertujuan meningkatkan kesadaran siswa tentang pentingnya menjaga kebersihan tangan untuk mencegah penyakit, melalui penyampaian materi dan praktik langsung langkah-langkah cuci tangan yang benar.', 'img_697f898534ca47.01387490.jpg', 'Lainnya', '2026-01-26 03:29:05', '2026-02-01 17:12:37'),
(2, 'Sosialisasi Judi Online SMAN 2 Natar', 'Kegiatan sosialisasi judi online di SMAN 2 Natar bertujuan untuk memberikan pemahaman kepada siswa tentang bahaya dan dampak negatif judi online terhadap prestasi belajar, kesehatan mental, kondisi ekonomi, serta risiko hukum. Melalui kegiatan ini, siswa diharapkan mampu bersikap kritis dalam menggunakan internet dan menjauhi praktik judi online.', 'img_697f88ead766b7.53672077.jpg', 'Acara', '2026-02-01 17:10:02', '2026-02-01 17:14:19'),
(3, 'Berbagi Sayuran Gratis Jumat Berkah Bersama PHBI', 'Kegiatan Berbagi Sayuran Gratis dalam program Jumat Berkah bersama PHBI (Persatuan Hari Besar Islam) merupakan bentuk kepedulian sosial kepada masyarakat. Kegiatan ini bertujuan membantu memenuhi kebutuhan pangan sekaligus menumbuhkan semangat berbagi, kepedulian, dan kebersamaan di tengah masyarakat.', 'img_697f89db4d0279.11628289.jpg', 'Masyarakat', '2026-02-01 17:14:03', '2026-02-01 17:14:03'),
(4, 'Senam Bersama Lansia dan Pemeriksaan Kesehatan Gratis', 'Kegiatan Senam Bersama Lansia dan Pemeriksaan Kesehatan Gratis dilaksanakan sebagai upaya meningkatkan kebugaran fisik serta memantau kondisi kesehatan lansia. Kegiatan ini bertujuan mendorong pola hidup sehat, deteksi dini masalah kesehatan, dan meningkatkan kualitas hidup lansia melalui aktivitas fisik dan layanan pemeriksaan kesehatan dasar.', 'img_697f8a8a0e2376.05118710.jpg', 'Masyarakat', '2026-02-01 17:16:58', '2026-02-01 17:17:11'),
(5, 'Sosialisasi Stop Bullying SDN Pancasila', 'Kegiatan Sosialisasi Stop Bullying di SDN Pancasila dilaksanakan untuk meningkatkan pemahaman siswa tentang bentuk-bentuk bullying dan dampak negatifnya terhadap korban. Melalui kegiatan ini, siswa diharapkan mampu menumbuhkan sikap saling menghargai, berani menolak tindakan bullying, serta menciptakan lingkungan sekolah yang aman dan nyaman.', 'img_697f8b0725bbe8.98245825.jpg', 'Acara', '2026-02-01 17:19:03', '2026-02-01 17:19:03'),
(6, 'Sosialisasi Penggunaan Qris', 'Kegiatan Sosialisasi Penggunaan QRIS di Desa Pancasila bertujuan untuk meningkatkan pemahaman masyarakat dan pelaku usaha desa dalam melakukan transaksi non-tunai secara mudah, aman, dan efisien. Melalui kegiatan ini, diharapkan masyarakat mampu memanfaatkan QRIS sebagai sarana pembayaran digital untuk mendukung perkembangan ekonomi desa dan literasi keuangan digital.', 'img_697f8e5d34d434.35878599.jpg', 'Masyarakat', '2026-02-01 17:33:17', '2026-02-01 17:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `map_locations`
--

CREATE TABLE `map_locations` (
  `id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `deskripsi` text,
  `alamat` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `map_locations`
--

INSERT INTO `map_locations` (`id`, `nama`, `kategori`, `latitude`, `longitude`, `deskripsi`, `alamat`, `status`, `created_at`) VALUES
(1, 'Balai Desa Pancasila', 'Kantor', -5.2542, 105.266517, 'Kantor pusat pemerintahan desa.', 'Jalan Raya Desa Pancasila No.1', 'aktif', '2026-01-26 05:33:56'),
(2, 'SMAN 2 Natar', 'Sekolah', -5.248589, 105.266816, 'SMAN 2 Natar', 'Pancasila, Kec. Natar, Kabupaten Lampung Selatan, Lampung 35362', 'aktif', '2026-01-26 05:40:02'),
(3, 'Pasar Desa Pancasila', 'Pertanian', -5.254705, 105.26734, 'Pasar Desa Pancasila Menjual Sayur sayuran dan bahan bahan lengkap', 'P7W8+5W6, Dusun 2, Pancasila, Kec. Natar, Kabupaten Lampung Selatan, Lampung 35362', 'aktif', '2026-01-26 05:43:56'),
(4, 'SMP Muhammadiyah 3 Natar', 'Sekolah', -5.255752, 105.275914, 'Sekolah Menengah Pertama Desa Pancasila', 'blok 1, Pancasila, Kec. Natar, Kabupaten Lampung Selatan, Lampung 35362', 'aktif', '2026-01-26 05:48:41'),
(5, 'Masjid Nurul Hidayah', 'Ibadah', -5.255784, 105.266496, 'Masjid Dusun 2', 'P7V8+MJV, Dusun Dua, Pancasila, Kec. Natar, Kabupaten Lampung Selatan, Lampung 35362', 'aktif', '2026-02-01 14:59:51'),
(6, 'Gereja Protestan Kristen Muria Indonesia (GKMI Sukototo)', 'Ibadah', -5.249816, 105.259143, 'Gereja Protestan', 'Q725+5PC, Pancasila, Kec. Natar, Kabupaten Lampung Selatan, Lampung 35362', 'aktif', '2026-02-01 15:03:12'),
(7, 'Masjid nurul iman', 'Ibadah', -5.251885, 105.265141, 'Masjid Dusun 5', 'blok 5, Pancasila, Kec. Natar, Kabupaten Lampung Selatan, Lampung 35362', 'aktif', '2026-02-01 15:05:10'),
(8, 'Gereja Katolik Stasi Santo Aloysius', 'Ibadah', -5.254666, 105.271144, 'Gereja Katolik', 'P7WC+5C4, Pancasila, Kec. Natar, Kabupaten Lampung Selatan, Lampung 35362', 'aktif', '2026-02-01 15:07:09'),
(9, 'Tugu Selamat Datang Desa Pancasila', 'Pariwisata', -5.259491, 105.250092, 'Tugu Selamat Datang Desa Pancasila merupakan ikon desa yang berfungsi sebagai penanda wilayah sekaligus simbol identitas dan kebanggaan masyarakat. Tugu ini mencerminkan nilai-nilai Pancasila sebagai dasar kehidupan bermasyarakat, serta menjadi wujud komitmen Desa Pancasila dalam menjunjung persatuan, gotong royong, dan kebersamaan.', 'P6RX+6WH, Pancasila, Kec. Natar, Kabupaten Lampung Selatan, Lampung 35362', 'aktif', '2026-02-01 17:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `umkm`
--

CREATE TABLE `umkm` (
  `id` int NOT NULL,
  `nama_usaha` varchar(150) DEFAULT NULL,
  `pemilik` varchar(150) DEFAULT NULL,
  `kontak` varchar(20) DEFAULT NULL,
  `deskripsi` text,
  `harga` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `umkm`
--

INSERT INTO `umkm` (`id`, `nama_usaha`, `pemilik`, `kontak`, `deskripsi`, `harga`, `foto`, `created_at`) VALUES
(1, 'Pabrik Tahu Rumahan', 'zidan', '08111047304', 'Pabrik Tahu Rumahan Desa Pancasila merupakan usaha skala rumah tangga yang bergerak di bidang pengolahan kedelai menjadi tahu sebagai produk pangan sehari-hari. Usaha ini berperan dalam memenuhi kebutuhan masyarakat akan pangan bergizi dengan harga terjangkau, sekaligus membuka lapangan kerja dan meningkatkan perekonomian warga Desa Pancasila.', '5000', 'img_697f8c60eeb3a9.70090454.jpg', '2026-01-27 13:12:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `full_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `full_name`, `created_at`, `updated_at`) VALUES
(2, 'zidan', 'zidanrosyid22@gmail.com', '$2y$10$8qbIBPj0ljH2FutCTE20suN8Ug3OLmd9hevXiuBWhI7eL0scqoXsy', 'user', 'zidan', '2026-01-25 14:39:57', '2026-01-25 14:39:57'),
(3, 'salha', 'salha@gmail.com', '$2y$10$ITvCrtSOlm0ShCmu5YzE8e3pRWnXgMFgRFi9pgDs4sRVsKML5lfB2', 'user', 'salha', '2026-01-25 14:57:26', '2026-01-25 14:57:26'),
(6, 'admin', 'admin@desa.id', '$2y$10$jEkqZtUEBN1d6usMmu8cU.459ztbmoeONhqdj3dyKqOji7zu.c.t2', 'admin', 'Administrator', '2026-01-26 03:01:16', '2026-01-26 03:01:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `desa_info`
--
ALTER TABLE `desa_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `map_locations`
--
ALTER TABLE `map_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `umkm`
--
ALTER TABLE `umkm`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `desa_info`
--
ALTER TABLE `desa_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `map_locations`
--
ALTER TABLE `map_locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `umkm`
--
ALTER TABLE `umkm`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
