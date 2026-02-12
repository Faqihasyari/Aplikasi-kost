-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2026 at 12:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coba_kost`
--

-- --------------------------------------------------------

--
-- Table structure for table `aturan_kost`
--

CREATE TABLE `aturan_kost` (
  `id` int(11) NOT NULL,
  `kost_id` int(11) NOT NULL,
  `aturan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aturan_kost`
--

INSERT INTO `aturan_kost` (`id`, `kost_id`, `aturan`, `created_at`) VALUES
(1, 1, 'Dilarang membawa tamu menginap', '2026-01-28 23:18:31'),
(2, 1, 'Jam malam pukul 22.00', '2026-01-28 23:18:31'),
(3, 1, 'Dilarang membawa hewan peliharaan', '2026-01-28 23:18:31'),
(4, 2, 'Tamu wajib lapor ke pengelola', '2026-01-28 23:18:31'),
(5, 2, 'Kebersihan kamar tanggung jawab penyewa', '2026-01-28 23:18:31'),
(6, 3, 'Check-in minimal 3 bulan', '2026-01-28 23:18:31'),
(7, 3, 'Deposit 1 bulan', '2026-01-28 23:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas_kost`
--

CREATE TABLE `fasilitas_kost` (
  `id` int(11) NOT NULL,
  `kost_id` int(11) NOT NULL,
  `nama_fasilitas` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fasilitas_kost`
--

INSERT INTO `fasilitas_kost` (`id`, `kost_id`, `nama_fasilitas`, `created_at`) VALUES
(1, 1, 'WiFi Gratis', '2026-01-28 23:18:31'),
(2, 1, 'Parkir Motor', '2026-01-28 23:18:31'),
(3, 1, 'Dapur Bersama', '2026-01-28 23:18:31'),
(4, 2, 'WiFi Gratis', '2026-01-28 23:18:31'),
(5, 2, 'Parkir Motor & Mobil', '2026-01-28 23:18:31'),
(6, 2, 'Laundry', '2026-01-28 23:18:31'),
(7, 3, 'WiFi Gratis', '2026-01-28 23:18:31'),
(8, 3, 'Parkir Motor & Mobil', '2026-01-28 23:18:31'),
(9, 3, 'Gym', '2026-01-28 23:18:31'),
(10, 3, 'Laundry', '2026-01-28 23:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `id` int(11) NOT NULL,
  `kost_id` int(11) NOT NULL,
  `nomor_kamar` varchar(20) NOT NULL,
  `harga_sewa` decimal(12,2) NOT NULL,
  `status` enum('Kosong','Terisi','Maintenance') DEFAULT 'Kosong',
  `foto` varchar(255) DEFAULT NULL,
  `fasilitas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kamar`
--

INSERT INTO `kamar` (`id`, `kost_id`, `nomor_kamar`, `harga_sewa`, `status`, `foto`, `fasilitas`, `created_at`) VALUES
(1, 1, 'A1', 1500000.00, 'Terisi', NULL, 'AC, Kasur, Lemari, WiFi', '2026-01-28 23:18:31'),
(2, 1, 'A2', 1500000.00, 'Terisi', NULL, 'AC, Kasur, Lemari, WiFi', '2026-01-28 23:18:31'),
(3, 1, 'A3', 1200000.00, 'Terisi', NULL, 'Kipas, Kasur, Lemari, WiFi', '2026-01-28 23:18:31'),
(4, 2, 'B1', 1800000.00, 'Terisi', NULL, 'AC, Kasur, Lemari, WiFi, Kamar Mandi Dalam', '2026-01-28 23:18:31'),
(5, 2, 'B2', 1600000.00, 'Terisi', NULL, 'AC, Kasur, Lemari, WiFi', '2026-01-28 23:18:31'),
(6, 3, 'C1', 2000000.00, 'Kosong', NULL, 'AC, Kasur, Lemari, WiFi, Kamar Mandi Dalam, TV', '2026-01-28 23:18:31'),
(7, 1, 'A3', 1500000.00, 'Kosong', 'kamar_1769684010.jpeg', 'AC, Kamar Mandi Dalam', '2026-01-29 10:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `kontrak`
--

CREATE TABLE `kontrak` (
  `id` int(11) NOT NULL,
  `penyewa_id` int(11) NOT NULL,
  `kamar_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `durasi_bulan` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status` enum('Aktif','Akan Berakhir','Selesai') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontrak`
--

INSERT INTO `kontrak` (`id`, `penyewa_id`, `kamar_id`, `tanggal_mulai`, `tanggal_berakhir`, `durasi_bulan`, `total_harga`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-01-29', '2026-03-01', 1, 1500000.00, 'Aktif', '2026-01-29 03:30:14'),
(2, 2, 5, '2026-01-29', '2026-03-01', 1, 1600000.00, 'Aktif', '2026-01-29 04:00:18'),
(4, 4, 2, '2026-01-29', '2026-05-01', 3, 4500000.00, 'Aktif', '2026-01-29 09:22:45'),
(5, 5, 4, '2026-01-29', '2026-04-01', 2, 3600000.00, 'Aktif', '2026-01-29 10:43:40');

-- --------------------------------------------------------

--
-- Table structure for table `kost`
--

CREATE TABLE `kost` (
  `id` int(11) NOT NULL,
  `pemilik_id` int(11) NOT NULL,
  `nama_kost` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kost`
--

INSERT INTO `kost` (`id`, `pemilik_id`, `nama_kost`, `alamat`, `kota`, `deskripsi`, `foto`, `created_at`) VALUES
(1, 2, 'Kost Mawar Indah', 'Jl. Mawar No. 123, Kelapa Gading', 'Jakarta', 'Kost nyaman dengan fasilitas lengkap', NULL, '2026-01-28 23:18:31'),
(2, 2, 'Kost Melati Asri', 'Jl. Melati No. 45, Kebayoran', 'Jakarta', 'Kost strategis dekat kampus', NULL, '2026-01-28 23:18:31'),
(3, 3, 'Kost Anggrek Residence', 'Jl. Anggrek No. 78, Sudirman', 'Jakarta', 'Kost eksklusif dengan AC dan WiFi', NULL, '2026-01-28 23:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `kontrak_id` int(11) NOT NULL,
  `bulan_ke` int(11) NOT NULL,
  `periode_bulan` varchar(20) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `metode` enum('QRIS') DEFAULT 'QRIS',
  `status` enum('Lunas','Belum Bayar','Terlambat','Menunggu') DEFAULT 'Belum Bayar',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `kontrak_id`, `bulan_ke`, `periode_bulan`, `jumlah`, `metode`, `status`, `bukti_pembayaran`, `tanggal_upload`, `tanggal_bayar`, `created_at`) VALUES
(1, 1, 1, 'January 2026', 1500000.00, 'QRIS', 'Lunas', 'bukti_1769657450_4.png', NULL, '2026-01-29', '2026-01-29 03:30:14'),
(2, 2, 1, 'January 2026', 1600000.00, 'QRIS', 'Belum Bayar', NULL, NULL, NULL, '2026-01-29 04:00:18'),
(5, 4, 1, 'January 2026', 1500000.00, 'QRIS', 'Lunas', 'bukti_1769678545_7.png', NULL, '2026-01-29', '2026-01-29 09:22:45'),
(6, 4, 2, 'April 2026', 1500000.00, 'QRIS', 'Lunas', 'bukti_1769678633_7.png', NULL, '2026-01-29', '2026-01-29 09:23:37'),
(7, 4, 3, 'May 2026', 1500000.00, 'QRIS', 'Belum Bayar', NULL, '2026-01-29 16:35:14', NULL, '2026-01-29 09:28:23'),
(8, 5, 1, 'January 2026', 1800000.00, 'QRIS', 'Lunas', 'bukti_1769683403_8.png', NULL, '2026-01-29', '2026-01-29 10:43:40'),
(9, 5, 2, 'April 2026', 1800000.00, 'QRIS', 'Lunas', 'bukti_1769683507_8.png', NULL, '2026-01-29', '2026-01-29 10:44:31');

-- --------------------------------------------------------

--
-- Table structure for table `penyewa`
--

CREATE TABLE `penyewa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kamar_id` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status` enum('Aktif','Selesai') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyewa`
--

INSERT INTO `penyewa` (`id`, `user_id`, `kamar_id`, `tanggal_masuk`, `tanggal_keluar`, `status`, `created_at`) VALUES
(1, 4, 1, '2026-01-29', NULL, 'Aktif', '2026-01-29 03:30:14'),
(2, 5, 5, '2026-01-29', NULL, 'Aktif', '2026-01-29 04:00:18'),
(4, 7, 2, '2026-01-29', NULL, 'Aktif', '2026-01-29 09:22:45'),
(5, 8, 4, '2026-01-29', NULL, 'Aktif', '2026-01-29 10:43:40');

-- --------------------------------------------------------

--
-- Table structure for table `request_sewa`
--

CREATE TABLE `request_sewa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kamar_id` int(11) NOT NULL,
  `tanggal_request` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diterima','Ditolak','Menunggu Konfirmasi','Selesai') DEFAULT 'Menunggu',
  `qr_code` varchar(255) DEFAULT NULL,
  `qr_expired` datetime DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_sewa`
--

INSERT INTO `request_sewa` (`id`, `user_id`, `kamar_id`, `tanggal_request`, `status`, `qr_code`, `qr_expired`, `bukti_pembayaran`, `created_at`, `updated_at`) VALUES
(1, 7, 2, '2026-01-29 16:20:50', 'Selesai', 'REQ_1_1769678516.png', '2026-01-30 10:21:56', 'bukti_1769678545_7.png', '2026-01-29 16:20:50', '2026-01-29 16:22:45'),
(2, 8, 4, '2026-01-29 17:42:40', 'Selesai', 'REQ_2_1769683376.png', '2026-01-30 11:42:56', 'bukti_1769683403_8.png', '2026-01-29 17:42:40', '2026-01-29 17:43:40'),
(3, 9, 7, '2026-01-29 17:55:09', 'Diterima', 'REQ_3_1769684121.png', '2026-01-30 11:55:21', NULL, '2026-01-29 17:55:09', '2026-01-29 17:55:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('admin','pemilik','pembeli') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `role`, `created_at`) VALUES
(1, 'admin', '$2a$12$1a7qklcbSIajsQIJaTHEVuzqORvmynLlnBQ.xAV52myh.UMGvs.vq', 'Administrator', 'admin@sewakost.com', NULL, 'admin', '2026-01-28 23:18:31'),
(2, 'pemilik1', '$2a$12$uHjj/nWWLHoKY9A0/BpH7.Dg0Gz0iOPuUl3A5AnwyAvOLur1gsaJe', 'Budi Santoso', 'budi@gmail.com', '08123456789', 'pemilik', '2026-01-28 23:18:31'),
(3, 'pemilik2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Aminah', 'siti@gmail.com', '08198765432', 'pemilik', '2026-01-28 23:18:31'),
(4, 'penyewa', '$2a$12$sE4B/i/vhWI9VkTmJhtxROaHJ2AEemNCz1UfLJxf2HS7BbZugkJqq', 'Ahmad Rizki', 'ahmad@gmail.com', '08111222333', 'pembeli', '2026-01-28 23:18:31'),
(5, 'penyewa2', '$2a$12$auPspCXy2t9JWfO90AsFUOBvLfXeSYMN4SQZwWhmH.0pNxljqKI8.', 'Dewi Lestari', 'dewi@gmail.com', '08555666777', 'pembeli', '2026-01-28 23:18:31'),
(7, 'falan', '$2y$10$YPAJcgIOQfo9yUAacbf/juGE94Yj.cVW4W8laGeU63p7SWQovQ6L6', 'Falan', 'falan@gmail.com', '081936182648', 'pembeli', '2026-01-29 08:38:41'),
(8, 'fulan', '$2y$10$skerogAB.uaZAuF22IyNyeSr957nEQi1Ljfaj6XNOMxdbQLzZDG/y', 'Fulan', 'fulan@gmail.com', '083718294628', 'pembeli', '2026-01-29 10:41:50'),
(9, 'fiil', '$2y$10$edeKDgkcaK.Vfn8ZLW3CGuorMJZ8nNbX1AD/iHsoQTYSSonqN38rS', 'Fiil', 'fiil@gmail.com', '085724719385', 'pembeli', '2026-01-29 10:54:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aturan_kost`
--
ALTER TABLE `aturan_kost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kost_id` (`kost_id`);

--
-- Indexes for table `fasilitas_kost`
--
ALTER TABLE `fasilitas_kost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kost_id` (`kost_id`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kost_id` (`kost_id`);

--
-- Indexes for table `kontrak`
--
ALTER TABLE `kontrak`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penyewa_id` (`penyewa_id`),
  ADD KEY `kamar_id` (`kamar_id`);

--
-- Indexes for table `kost`
--
ALTER TABLE `kost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pemilik_id` (`pemilik_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kontrak_id` (`kontrak_id`);

--
-- Indexes for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kamar_id` (`kamar_id`);

--
-- Indexes for table `request_sewa`
--
ALTER TABLE `request_sewa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kamar_id` (`kamar_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aturan_kost`
--
ALTER TABLE `aturan_kost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fasilitas_kost`
--
ALTER TABLE `fasilitas_kost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kontrak`
--
ALTER TABLE `kontrak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kost`
--
ALTER TABLE `kost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `request_sewa`
--
ALTER TABLE `request_sewa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aturan_kost`
--
ALTER TABLE `aturan_kost`
  ADD CONSTRAINT `aturan_kost_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fasilitas_kost`
--
ALTER TABLE `fasilitas_kost`
  ADD CONSTRAINT `fasilitas_kost_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kamar`
--
ALTER TABLE `kamar`
  ADD CONSTRAINT `kamar_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kontrak`
--
ALTER TABLE `kontrak`
  ADD CONSTRAINT `kontrak_ibfk_1` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kontrak_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kost`
--
ALTER TABLE `kost`
  ADD CONSTRAINT `kost_ibfk_1` FOREIGN KEY (`pemilik_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD CONSTRAINT `penyewa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penyewa_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_sewa`
--
ALTER TABLE `request_sewa`
  ADD CONSTRAINT `request_sewa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_sewa_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
