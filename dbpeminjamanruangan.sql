-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 28, 2025 at 01:19 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbpeminjamanruangan`
--

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman_ruangan`
--

CREATE TABLE `peminjaman_ruangan` (
  `peminjaman_id` int NOT NULL,
  `user_id` int NOT NULL,
  `ruangan_id` int NOT NULL,
  `tanggal` varchar(500) NOT NULL,
  `waktu_mulai` varchar(60) NOT NULL,
  `durasi_pinjam` varchar(20) NOT NULL,
  `waktu_selesai` varchar(40) NOT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `return_status` enum('BELUM_DIKEMBALIKAN','PENGAJUAN','DIKEMBALIKAN') DEFAULT 'BELUM_DIKEMBALIKAN',
  `return_date` datetime DEFAULT NULL,
  `return_condition` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman_ruangan`
--

INSERT INTO `peminjaman_ruangan` (`peminjaman_id`, `user_id`, `ruangan_id`, `tanggal`, `waktu_mulai`, `durasi_pinjam`, `waktu_selesai`, `status`, `return_status`, `return_date`, `return_condition`) VALUES
(1, 232410019, 1, '22/05/2025', '6', '3', '3', '', 'BELUM_DIKEMBALIKAN', NULL, NULL),
(2, 232410018, 2, '23/05/2025', '6', '2', '1', '', 'BELUM_DIKEMBALIKAN', NULL, NULL),
(3, 232410017, 3, '24/05/2025', '4', '6', '8', '', 'BELUM_DIKEMBALIKAN', NULL, NULL),
(4, 232410016, 4, '25/05/2025', '5', '9', '7', '', 'BELUM_DIKEMBALIKAN', NULL, NULL),
(5, 232410015, 5, '26/05/2025', '7', '5', '9', '', 'BELUM_DIKEMBALIKAN', NULL, NULL),
(6, 1, 2, '2025-05-26', '08:37', '1', '09:37', 'DITERIMA', 'BELUM_DIKEMBALIKAN', NULL, NULL),
(7, 1, 5, '2025-05-28', '07:30', '4', '11:30', 'DITERIMA', 'DIKEMBALIKAN', '2025-05-26 14:48:31', 'ruangan jelek'),
(8, 1, 1, '2025-05-27', '12:05', '3', '15:05', 'DITOLAK', 'BELUM_DIKEMBALIKAN', NULL, NULL),
(9, 1, 3, '2025-05-27', '13:00', '1', '14:00', 'DITOLAK', 'BELUM_DIKEMBALIKAN', NULL, NULL),
(10, 1, 1, '2025-05-29', '12:30', '3', '15:30', 'DITERIMA', 'DIKEMBALIKAN', '2025-05-26 15:23:00', 'jelek ruangannya');

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `ruangan_id` int NOT NULL,
  `nama_ruangan` varchar(200) NOT NULL,
  `lokasi` varchar(400) NOT NULL,
  `kapasitas` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`ruangan_id`, `nama_ruangan`, `lokasi`, `kapasitas`) VALUES
(1, 'ruangan konekting', 'lantai 1', 30),
(2, 'ruangan aula', 'lantai 3', 200),
(3, 'ruangan bk', 'depan lapangan', 12),
(4, 'ruangan kesiswaan', 'depan kantin', 3),
(5, 'ruangan uks', 'depan taman sekolah', 5),
(6, 'uks', 'lantai 2', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `id_card` varchar(22) NOT NULL,
  `username` varchar(11) NOT NULL,
  `password` varchar(60) NOT NULL,
  `role` varchar(40) NOT NULL,
  `jenis_pengguna` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `id_card`, `username`, `password`, `role`, `jenis_pengguna`, `nama_lengkap`) VALUES
(1, '232410019', 'farel', '11111', 'user', 'siswa', 'muhamad rahmanda fahriel hermanto'),
(2, '232410018', 'fernanda', 'a7d579ba76398070eae654c30ff153a4c273272a', 'user', 'siswa', 'fernanda dermawan'),
(3, '232410017', 'dimas', '22222', 'admin', 'guru', 'dimas nugroho'),
(4, '232410016', 'nofal', 'c129b324aee662b04eccf68babba85851346dff9', 'admin', 'guru', 'nofal makhruf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `peminjaman_ruangan`
--
ALTER TABLE `peminjaman_ruangan`
  ADD PRIMARY KEY (`peminjaman_id`),
  ADD KEY `user_id` (`user_id`,`ruangan_id`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`ruangan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `peminjaman_ruangan`
--
ALTER TABLE `peminjaman_ruangan`
  MODIFY `peminjaman_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `ruangan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
