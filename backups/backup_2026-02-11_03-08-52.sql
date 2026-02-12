-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: coba_kost
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aturan_kost`
--

DROP TABLE IF EXISTS `aturan_kost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aturan_kost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kost_id` int(11) NOT NULL,
  `aturan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kost_id` (`kost_id`),
  CONSTRAINT `aturan_kost_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aturan_kost`
--

LOCK TABLES `aturan_kost` WRITE;
/*!40000 ALTER TABLE `aturan_kost` DISABLE KEYS */;
INSERT INTO `aturan_kost` VALUES (1,1,'Dilarang membawa tamu menginap','2026-01-28 23:18:31'),(2,1,'Jam malam pukul 22.00','2026-01-28 23:18:31'),(3,1,'Dilarang membawa hewan peliharaan','2026-01-28 23:18:31'),(4,2,'Tamu wajib lapor ke pengelola','2026-01-28 23:18:31'),(5,2,'Kebersihan kamar tanggung jawab penyewa','2026-01-28 23:18:31'),(6,3,'Check-in minimal 3 bulan','2026-01-28 23:18:31'),(7,3,'Deposit 1 bulan','2026-01-28 23:18:31'),(8,4,'asdaw','2026-01-29 05:07:17');
/*!40000 ALTER TABLE `aturan_kost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fasilitas_kost`
--

DROP TABLE IF EXISTS `fasilitas_kost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fasilitas_kost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kost_id` int(11) NOT NULL,
  `nama_fasilitas` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kost_id` (`kost_id`),
  CONSTRAINT `fasilitas_kost_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fasilitas_kost`
--

LOCK TABLES `fasilitas_kost` WRITE;
/*!40000 ALTER TABLE `fasilitas_kost` DISABLE KEYS */;
INSERT INTO `fasilitas_kost` VALUES (1,1,'WiFi Gratis','2026-01-28 23:18:31'),(2,1,'Parkir Motor','2026-01-28 23:18:31'),(3,1,'Dapur Bersama','2026-01-28 23:18:31'),(4,2,'WiFi Gratis','2026-01-28 23:18:31'),(5,2,'Parkir Motor & Mobil','2026-01-28 23:18:31'),(6,2,'Laundry','2026-01-28 23:18:31'),(7,3,'WiFi Gratis','2026-01-28 23:18:31'),(8,3,'Parkir Motor & Mobil','2026-01-28 23:18:31'),(9,3,'Gym','2026-01-28 23:18:31'),(10,3,'Laundry','2026-01-28 23:18:31'),(11,4,'asdaw','2026-01-29 05:07:12');
/*!40000 ALTER TABLE `fasilitas_kost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kamar`
--

DROP TABLE IF EXISTS `kamar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kamar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kost_id` int(11) NOT NULL,
  `nomor_kamar` varchar(20) NOT NULL,
  `harga_sewa` decimal(12,2) NOT NULL,
  `status` enum('Kosong','Terisi','Maintenance') DEFAULT 'Kosong',
  `foto` varchar(255) DEFAULT NULL,
  `fasilitas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kost_id` (`kost_id`),
  CONSTRAINT `kamar_ibfk_1` FOREIGN KEY (`kost_id`) REFERENCES `kost` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kamar`
--

LOCK TABLES `kamar` WRITE;
/*!40000 ALTER TABLE `kamar` DISABLE KEYS */;
INSERT INTO `kamar` VALUES (1,1,'A1',1500000.00,'Terisi',NULL,'AC, Kasur, Lemari, WiFi','2026-01-28 23:18:31'),(2,1,'A2',1500000.00,'Terisi',NULL,'AC, Kasur, Lemari, WiFi','2026-01-28 23:18:31'),(3,1,'A3',1200000.00,'Terisi',NULL,'Kipas, Kasur, Lemari, WiFi','2026-01-28 23:18:31'),(4,2,'B1',1800000.00,'Terisi',NULL,'AC, Kasur, Lemari, WiFi, Kamar Mandi Dalam','2026-01-28 23:18:31'),(5,2,'B2',1600000.00,'Terisi',NULL,'AC, Kasur, Lemari, WiFi','2026-01-28 23:18:31'),(6,3,'C1',2000000.00,'Kosong',NULL,'AC, Kasur, Lemari, WiFi, Kamar Mandi Dalam, TV','2026-01-28 23:18:31'),(7,1,'A3',1500000.00,'Kosong','kamar_1769684010.jpeg','AC, Kamar Mandi Dalam','2026-01-29 10:53:30'),(8,4,'asdasd',21123.00,'Terisi','kamar_1769663216.png','asda','2026-01-29 05:06:56'),(9,4,'1231223',120000.00,'Kosong','kamar_1769663537.png','AC','2026-01-29 05:12:17'),(10,4,'123897',150000.00,'Terisi','kamar_1769663553.png','AC','2026-01-29 05:12:33');
/*!40000 ALTER TABLE `kamar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kontrak`
--

DROP TABLE IF EXISTS `kontrak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kontrak` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penyewa_id` int(11) NOT NULL,
  `kamar_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `durasi_bulan` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status` enum('Aktif','Akan Berakhir','Selesai') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `penyewa_id` (`penyewa_id`),
  KEY `kamar_id` (`kamar_id`),
  CONSTRAINT `kontrak_ibfk_1` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kontrak_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kontrak`
--

LOCK TABLES `kontrak` WRITE;
/*!40000 ALTER TABLE `kontrak` DISABLE KEYS */;
INSERT INTO `kontrak` VALUES (1,1,1,'2026-01-29','2026-03-01',1,1500000.00,'Aktif','2026-01-29 03:30:14'),(2,2,5,'2026-01-29','2026-03-01',1,1600000.00,'Aktif','2026-01-29 04:00:18'),(4,4,2,'2026-01-29','2026-05-01',3,4500000.00,'Aktif','2026-01-29 09:22:45'),(5,5,4,'2026-01-29','2026-04-01',2,3600000.00,'Aktif','2026-01-29 10:43:40'),(6,6,8,'2026-01-29','2026-03-01',1,21123.00,'Aktif','2026-01-29 05:10:36'),(7,7,10,'2026-01-29','2026-06-01',4,600000.00,'Aktif','2026-01-29 05:20:04');
/*!40000 ALTER TABLE `kontrak` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kost`
--

DROP TABLE IF EXISTS `kost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pemilik_id` int(11) NOT NULL,
  `nama_kost` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pemilik_id` (`pemilik_id`),
  CONSTRAINT `kost_ibfk_1` FOREIGN KEY (`pemilik_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kost`
--

LOCK TABLES `kost` WRITE;
/*!40000 ALTER TABLE `kost` DISABLE KEYS */;
INSERT INTO `kost` VALUES (1,2,'Kost Mawar Indah','Jl. Mawar No. 123, Kelapa Gading','Jakarta','Kost nyaman dengan fasilitas lengkap',NULL,'2026-01-28 23:18:31'),(2,2,'Kost Melati Asri','Jl. Melati No. 45, Kebayoran','Jakarta','Kost strategis dekat kampus',NULL,'2026-01-28 23:18:31'),(3,3,'Kost Anggrek Residence','Jl. Anggrek No. 78, Sudirman','Jakarta','Kost eksklusif dengan AC dan WiFi',NULL,'2026-01-28 23:18:31'),(4,11,'asdaw','awdads','awd','awdasd','kost_1769665470_169.png','2026-01-29 05:06:29'),(5,11,'Kost Indah','Cirebon','Cirebon','Murah meriah','kost_1770679660_975.jpg','2026-02-09 23:27:40');
/*!40000 ALTER TABLE `kost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kontrak_id` int(11) NOT NULL,
  `bulan_ke` int(11) NOT NULL,
  `periode_bulan` varchar(20) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `metode` enum('QRIS') DEFAULT 'QRIS',
  `status` enum('Lunas','Belum Bayar','Terlambat','Menunggu') DEFAULT 'Belum Bayar',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kontrak_id` (`kontrak_id`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
INSERT INTO `pembayaran` VALUES (1,1,1,'January 2026',1500000.00,'QRIS','Lunas','bukti_1769657450_4.png',NULL,'2026-01-29','2026-01-29 03:30:14'),(2,2,1,'January 2026',1600000.00,'QRIS','Belum Bayar',NULL,NULL,NULL,'2026-01-29 04:00:18'),(5,4,1,'January 2026',1500000.00,'QRIS','Lunas','bukti_1769678545_7.png',NULL,'2026-01-29','2026-01-29 09:22:45'),(6,4,2,'April 2026',1500000.00,'QRIS','Lunas','bukti_1769678633_7.png',NULL,'2026-01-29','2026-01-29 09:23:37'),(7,4,3,'May 2026',1500000.00,'QRIS','Belum Bayar',NULL,'2026-01-29 16:35:14',NULL,'2026-01-29 09:28:23'),(8,5,1,'January 2026',1800000.00,'QRIS','Lunas','bukti_1769683403_8.png',NULL,'2026-01-29','2026-01-29 10:43:40'),(9,5,2,'April 2026',1800000.00,'QRIS','Lunas','bukti_1769683507_8.png',NULL,'2026-01-29','2026-01-29 10:44:31'),(10,6,1,'January 2026',21123.00,'QRIS','Lunas','bukti_1769663412_12.png',NULL,'2026-01-29','2026-01-29 05:10:36'),(11,7,1,'January 2026',150000.00,'QRIS','Lunas','bukti_1769663986_13.png',NULL,'2026-01-29','2026-01-29 05:20:04'),(12,7,2,'April 2026',150000.00,'QRIS','Belum Bayar',NULL,NULL,NULL,'2026-01-29 05:20:24'),(13,7,3,'May 2026',150000.00,'QRIS','Belum Bayar',NULL,NULL,NULL,'2026-01-29 05:20:24'),(14,7,4,'June 2026',150000.00,'QRIS','Belum Bayar',NULL,NULL,NULL,'2026-01-29 05:20:24');
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penyewa`
--

DROP TABLE IF EXISTS `penyewa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyewa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kamar_id` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status` enum('Aktif','Selesai') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `kamar_id` (`kamar_id`),
  CONSTRAINT `penyewa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penyewa_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penyewa`
--

LOCK TABLES `penyewa` WRITE;
/*!40000 ALTER TABLE `penyewa` DISABLE KEYS */;
INSERT INTO `penyewa` VALUES (1,4,1,'2026-01-29',NULL,'Aktif','2026-01-29 03:30:14'),(2,5,5,'2026-01-29',NULL,'Aktif','2026-01-29 04:00:18'),(4,7,2,'2026-01-29',NULL,'Aktif','2026-01-29 09:22:45'),(5,8,4,'2026-01-29',NULL,'Aktif','2026-01-29 10:43:40'),(6,12,8,'2026-01-27','2026-01-28','Selesai','2026-01-29 05:10:36'),(7,13,10,'2026-01-27','2026-01-28','Aktif','2026-01-29 05:20:04');
/*!40000 ALTER TABLE `penyewa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_sewa`
--

DROP TABLE IF EXISTS `request_sewa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `request_sewa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kamar_id` int(11) NOT NULL,
  `tanggal_request` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diterima','Ditolak','Menunggu Konfirmasi','Selesai') DEFAULT 'Menunggu',
  `qr_code` varchar(255) DEFAULT NULL,
  `qr_expired` datetime DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `kamar_id` (`kamar_id`),
  CONSTRAINT `request_sewa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `request_sewa_ibfk_2` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_sewa`
--

LOCK TABLES `request_sewa` WRITE;
/*!40000 ALTER TABLE `request_sewa` DISABLE KEYS */;
INSERT INTO `request_sewa` VALUES (1,7,2,'2026-01-29 16:20:50','Selesai','REQ_1_1769678516.png','2026-01-30 10:21:56','bukti_1769678545_7.png','2026-01-29 16:20:50','2026-01-29 16:22:45'),(2,8,4,'2026-01-29 17:42:40','Selesai','REQ_2_1769683376.png','2026-01-30 11:42:56','bukti_1769683403_8.png','2026-01-29 17:42:40','2026-01-29 17:43:40'),(3,9,7,'2026-01-29 17:55:09','Diterima','REQ_3_1769684121.png','2026-01-30 11:55:21',NULL,'2026-01-29 17:55:09','2026-01-29 17:55:21'),(4,12,8,'2026-01-29 12:09:04','Selesai','REQ_4_1769663366.png','2026-01-30 06:09:26','bukti_1769663412_12.png','2026-01-29 12:09:04','2026-01-29 12:10:36'),(5,13,10,'2026-01-29 12:13:03','Selesai','REQ_5_1769663711.png','2026-01-30 06:15:11','bukti_1769663986_13.png','2026-01-29 12:13:03','2026-01-29 12:20:04'),(6,12,7,'2026-02-10 06:32:22','Menunggu',NULL,NULL,NULL,'2026-02-10 06:32:22','2026-02-10 06:32:22'),(7,12,9,'2026-02-10 06:33:13','Diterima','REQ_7_1770680040.png','2026-02-11 06:33:13',NULL,'2026-02-10 06:33:13','2026-02-10 06:34:00');
/*!40000 ALTER TABLE `request_sewa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('admin','pemilik','pembeli') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2a$12$1a7qklcbSIajsQIJaTHEVuzqORvmynLlnBQ.xAV52myh.UMGvs.vq','Administrator','admin@sewakost.com',NULL,'admin','2026-01-28 23:18:31'),(2,'pemilik1','$2a$12$uHjj/nWWLHoKY9A0/BpH7.Dg0Gz0iOPuUl3A5AnwyAvOLur1gsaJe','Budi Santoso','budi@gmail.com','08123456789','pemilik','2026-01-28 23:18:31'),(3,'pemilik2','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Siti Aminah','siti@gmail.com','08198765432','pemilik','2026-01-28 23:18:31'),(4,'penyewa','$2a$12$sE4B/i/vhWI9VkTmJhtxROaHJ2AEemNCz1UfLJxf2HS7BbZugkJqq','Ahmad Rizki','ahmad@gmail.com','08111222333','pembeli','2026-01-28 23:18:31'),(5,'penyewa2','$2a$12$auPspCXy2t9JWfO90AsFUOBvLfXeSYMN4SQZwWhmH.0pNxljqKI8.','Dewi Lestari','dewi@gmail.com','08555666777','pembeli','2026-01-28 23:18:31'),(7,'falan','$2y$10$YPAJcgIOQfo9yUAacbf/juGE94Yj.cVW4W8laGeU63p7SWQovQ6L6','Falan','falan@gmail.com','081936182648','pembeli','2026-01-29 08:38:41'),(8,'fulan','$2y$10$skerogAB.uaZAuF22IyNyeSr957nEQi1Ljfaj6XNOMxdbQLzZDG/y','Fulan','fulan@gmail.com','083718294628','pembeli','2026-01-29 10:41:50'),(9,'fiil','$2y$10$edeKDgkcaK.Vfn8ZLW3CGuorMJZ8nNbX1AD/iHsoQTYSSonqN38rS','Fiil','fiil@gmail.com','085724719385','pembeli','2026-01-29 10:54:46'),(10,'adminFaqih','$2a$12$ojqg4iII0R82nJABdsdyHO9bDTCz7EKU74X5qW7YuhtYPWa9PndkW','Faqih As','faqih@gmail.com','08218763','admin','2026-01-29 05:03:57'),(11,'pemilikAzrul','$2a$12$ojqg4iII0R82nJABdsdyHO9bDTCz7EKU74X5qW7YuhtYPWa9PndkW','Azrul','azrul@gmail.com','902108390','pemilik','2026-01-29 05:04:42'),(12,'dummy','$2y$10$IVvQvBU8xFOFvvrIjTfuRe6r1qUqgRXgLKf7nsYvvnQtiERBJmRKm','Dummy','admin2@gmail.com','085798319409','pembeli','2026-01-29 05:08:49'),(13,'dummy2','$2y$10$/lDtRLXOggJ821phuF8yYOndIuvC7lEzTsp.jU3NL64dVKcU1PpTq','dummyJuga','awdiawui2ads@asd','085798319409','pembeli','2026-01-29 05:11:31');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-11  9:08:52
