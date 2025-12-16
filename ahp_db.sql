-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2025 at 05:25 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ahp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ahp_input_matrices`
--

CREATE TABLE `ahp_input_matrices` (
  `id` int NOT NULL,
  `level_name` varchar(50) NOT NULL,
  `input_matrix` json NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ahp_input_matrices`
--

INSERT INTO `ahp_input_matrices` (`id`, `level_name`, `input_matrix`, `last_updated`) VALUES
(1, 'kriteria', '{\"pekerjaan\": {\"pekerjaan\": 1, \"minat_bakat\": 0.2554364774645177, \"tema_skripsi\": 0.43679023236814946}, \"minat_bakat\": {\"pekerjaan\": 3.914867641168863, \"minat_bakat\": 1, \"tema_skripsi\": 2.2894284851066637}, \"tema_skripsi\": {\"pekerjaan\": 2.2894284851066637, \"minat_bakat\": 0.43679023236814946, \"tema_skripsi\": 1}}', '2025-12-16 05:23:30'),
(2, 'minat_bakat', '{\"membaca\": {\"membaca\": 1, \"menulis\": 0.5, \"menggambar\": 0.3333333333333333, \"menghitung\": 0.2}, \"menulis\": {\"membaca\": 2, \"menulis\": 1, \"menggambar\": 0.3333333333333333, \"menghitung\": 0.25}, \"menggambar\": {\"membaca\": 3, \"menulis\": 3, \"menggambar\": 1, \"menghitung\": 0.25}, \"menghitung\": {\"membaca\": 5, \"menulis\": 4, \"menggambar\": 4, \"menghitung\": 1}}', '2025-12-10 18:12:52'),
(3, 'tema_skripsi', '{\"si\": {\"si\": 1, \"game\": 0.3333333333333333, \"algoritma\": 0.25, \"media_pembelajaran\": 3}, \"game\": {\"si\": 3, \"game\": 1, \"algoritma\": 0.3333333333333333, \"media_pembelajaran\": 3.5}, \"algoritma\": {\"si\": 4, \"game\": 3, \"algoritma\": 1, \"media_pembelajaran\": 4}, \"media_pembelajaran\": {\"si\": 0.3333333333333333, \"game\": 0.2857142857142857, \"algoritma\": 0.25, \"media_pembelajaran\": 1}}', '2025-12-10 18:12:03'),
(4, 'pekerjaan', '{\"admin\": {\"admin\": 1, \"animator\": 0.2, \"wirausaha\": 0.3333333333333333, \"programmer\": 0.2}, \"animator\": {\"admin\": 5, \"animator\": 1, \"wirausaha\": 3, \"programmer\": 0.2857142857142857}, \"wirausaha\": {\"admin\": 3, \"animator\": 0.3333333333333333, \"wirausaha\": 1, \"programmer\": 0.2}, \"programmer\": {\"admin\": 5, \"animator\": 3.5, \"wirausaha\": 5, \"programmer\": 1}}', '2025-12-10 18:11:09'),
(5, 'kriteria_dm_4', '{\"pekerjaan\": {\"pekerjaan\": 1, \"minat_bakat\": 0.3333333333333333, \"tema_skripsi\": 0.5}, \"minat_bakat\": {\"pekerjaan\": 3, \"minat_bakat\": 1, \"tema_skripsi\": 2}, \"tema_skripsi\": {\"pekerjaan\": 2, \"minat_bakat\": 0.5, \"tema_skripsi\": 1}}', '2025-12-16 05:10:22'),
(6, 'kriteria_dm_5', '{\"pekerjaan\": {\"pekerjaan\": 1, \"minat_bakat\": 0.2, \"tema_skripsi\": 0.3333333333333333}, \"minat_bakat\": {\"pekerjaan\": 5, \"minat_bakat\": 1, \"tema_skripsi\": 2}, \"tema_skripsi\": {\"pekerjaan\": 3, \"minat_bakat\": 0.5, \"tema_skripsi\": 1}}', '2025-12-16 05:10:43'),
(7, 'kriteria_dm_6', '{\"pekerjaan\": {\"pekerjaan\": 1, \"minat_bakat\": 0.25, \"tema_skripsi\": 0.5}, \"minat_bakat\": {\"pekerjaan\": 4, \"minat_bakat\": 1, \"tema_skripsi\": 3}, \"tema_skripsi\": {\"pekerjaan\": 2, \"minat_bakat\": 0.3333333333333333, \"tema_skripsi\": 1}}', '2025-12-16 05:08:47'),
(8, 'kriteria_dm_1', '{\"pekerjaan\": {\"pekerjaan\": 1, \"minat_bakat\": 0.2554364774645177, \"tema_skripsi\": 0.43679023236814946}, \"minat_bakat\": {\"pekerjaan\": 3.914867641168863, \"minat_bakat\": 1, \"tema_skripsi\": 2.2894284851066637}, \"tema_skripsi\": {\"pekerjaan\": 2.2894284851066637, \"minat_bakat\": 0.43679023236814946, \"tema_skripsi\": 1}}', '2025-12-16 05:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `ahp_weights_kriteria`
--

CREATE TABLE `ahp_weights_kriteria` (
  `id` int NOT NULL,
  `input_matrix_id` int DEFAULT NULL,
  `minat_bakat` decimal(10,8) NOT NULL,
  `tema_skripsi` decimal(10,8) NOT NULL,
  `pekerjaan` decimal(10,8) NOT NULL,
  `cr_kriteria` decimal(10,8) NOT NULL,
  `is_consistent` tinyint(1) NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ahp_weights_kriteria`
--

INSERT INTO `ahp_weights_kriteria` (`id`, `input_matrix_id`, `minat_bakat`, `tema_skripsi`, `pekerjaan`, `cr_kriteria`, `is_consistent`, `last_updated`) VALUES
(1, 1, '0.58291847', '0.28142360', '0.13565793', '0.01068621', 1, '2025-12-16 05:23:27'),
(2, 5, '0.53896104', '0.29725830', '0.16378066', '0.00964074', 1, '2025-12-16 05:10:22'),
(3, 6, '0.58126362', '0.30915033', '0.10958606', '0.00424461', 1, '2025-12-16 05:10:43'),
(4, 7, '0.62322473', '0.23948761', '0.13728766', '0.02196583', 1, '2025-12-16 05:08:47');

-- --------------------------------------------------------

--
-- Table structure for table `ahp_weights_minat_bakat`
--

CREATE TABLE `ahp_weights_minat_bakat` (
  `id` int NOT NULL,
  `input_matrix_id` int DEFAULT NULL,
  `menghitung` decimal(10,8) NOT NULL,
  `menggambar` decimal(10,8) NOT NULL,
  `menulis` decimal(10,8) NOT NULL,
  `membaca` decimal(10,8) NOT NULL,
  `cr_minat_bakat` decimal(10,8) NOT NULL,
  `is_consistent` tinyint(1) NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ahp_weights_minat_bakat`
--

INSERT INTO `ahp_weights_minat_bakat` (`id`, `input_matrix_id`, `menghitung`, `menggambar`, `menulis`, `membaca`, `cr_minat_bakat`, `is_consistent`, `last_updated`) VALUES
(1, 2, '0.55481283', '0.23729947', '0.12633690', '0.08155080', '0.06384120', 1, '2025-12-10 18:12:52');

-- --------------------------------------------------------

--
-- Table structure for table `ahp_weights_pekerjaan`
--

CREATE TABLE `ahp_weights_pekerjaan` (
  `id` int NOT NULL,
  `input_matrix_id` int DEFAULT NULL,
  `programmer` decimal(10,8) NOT NULL,
  `animator` decimal(10,8) NOT NULL,
  `wirausaha` decimal(10,8) NOT NULL,
  `admin` decimal(10,8) NOT NULL,
  `cr_pekerjaan` decimal(10,8) NOT NULL,
  `is_consistent` tinyint(1) NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ahp_weights_pekerjaan`
--

INSERT INTO `ahp_weights_pekerjaan` (`id`, `input_matrix_id`, `programmer`, `animator`, `wirausaha`, `admin`, `cr_pekerjaan`, `is_consistent`, `last_updated`) VALUES
(1, 4, '0.54536043', '0.26168461', '0.12657445', '0.06638051', '0.08839724', 1, '2025-12-10 18:11:09');

-- --------------------------------------------------------

--
-- Table structure for table `ahp_weights_tema_skripsi`
--

CREATE TABLE `ahp_weights_tema_skripsi` (
  `id` int NOT NULL,
  `input_matrix_id` int DEFAULT NULL,
  `algoritma` decimal(10,8) NOT NULL,
  `game` decimal(10,8) NOT NULL,
  `si` decimal(10,8) NOT NULL,
  `media_pembelajaran` decimal(10,8) NOT NULL,
  `cr_tema_skripsi` decimal(10,8) NOT NULL,
  `is_consistent` tinyint(1) NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ahp_weights_tema_skripsi`
--

INSERT INTO `ahp_weights_tema_skripsi` (`id`, `input_matrix_id`, `algoritma`, `game`, `si`, `media_pembelajaran`, `cr_tema_skripsi`, `is_consistent`, `last_updated`) VALUES
(1, 3, '0.50569129', '0.26566521', '0.14734954', '0.08129396', '0.08899587', 1, '2025-12-10 18:12:03');

-- --------------------------------------------------------

--
-- Table structure for table `decision_makers`
--

CREATE TABLE `decision_makers` (
  `id` int NOT NULL,
  `email` varchar(100) NOT NULL COMMENT 'Email DM (unik)',
  `nama` varchar(150) NOT NULL COMMENT 'Nama Decision Maker',
  `password` varchar(255) NOT NULL COMMENT 'Password (hashed)',
  `dm_number` int DEFAULT NULL COMMENT 'Nomor DM (1,2,3...)',
  `role_label` varchar(100) DEFAULT NULL COMMENT 'Label peran (contoh: Kepala Sekolah, Dosen, Admin)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `input_matrix_id` int DEFAULT NULL COMMENT 'Relasi ke ahp_input_matrices'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `decision_makers`
--

INSERT INTO `decision_makers` (`id`, `email`, `nama`, `password`, `dm_number`, `role_label`, `created_at`, `input_matrix_id`) VALUES
(1, 'kajur@campus.ac.id', 'Bapak Kajur', '123456', 1, 'Kepala Jurusan', '2025-12-16 04:44:12', 8),
(2, 'kabag_aa@campus.ac.id', 'Ibu Kabag AA', '123456', 2, 'Kepala Bagian Administrasi Akademik', '2025-12-16 04:44:12', NULL),
(3, 'kabag_mhs@campus.ac.id', 'Bapak Kabag Kemahasiswaan', '123456', 3, 'Kepala Bagian Kemahasiswaan', '2025-12-16 04:44:12', NULL),
(4, 'kajur@gmail.com', 'ifa', '$2y$10$W49wfPbAqT0DYTIYqt2DAuXxnF2oVODrOzI/c2ys6fbs1FnH0dJcO', 4, 'kajur', '2025-12-16 04:48:37', 5),
(5, 'sekprod@gmail.com', 'kevin', '$2y$10$eR0y/xVEzz4lRBRoWPA8NenFI14UAC.xjNCZwDY3dLblkM33koVuu', 5, 'sekprod', '2025-12-16 05:01:34', 6),
(6, 'dosen@gmail.com', 'rahma', '$2y$10$B6bYhljWNaZWL3ZW5GcXr.KMuSmRlRSzy2TTNLdFrjRksEpfXfQYK', 6, 'dosen', '2025-12-16 05:08:19', 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `email` varchar(100) NOT NULL COMMENT 'Email pengguna, dijadikan Primary Key',
  `nama` varchar(150) NOT NULL COMMENT 'Nama lengkap pengguna',
  `password` varchar(255) NOT NULL COMMENT 'Password yang di-hash (disarankan menggunakan minimal 60 karakter)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`email`, `nama`, `password`, `created_at`) VALUES
('ifa@gmail.com', 'hanifah', 'ifalocal123', '2025-12-11 12:08:45'),
('kevin@gmail.com', 'Kevin', '$2y$10$WdW/d2VpdHRt78cmOMobk.A/uSvJfHvF93ORXh2j1s61GuskOVFa.', '2025-12-10 18:31:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ahp_input_matrices`
--
ALTER TABLE `ahp_input_matrices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `level_name` (`level_name`);

--
-- Indexes for table `ahp_weights_kriteria`
--
ALTER TABLE `ahp_weights_kriteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `input_matrix_id` (`input_matrix_id`);

--
-- Indexes for table `ahp_weights_minat_bakat`
--
ALTER TABLE `ahp_weights_minat_bakat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `input_matrix_id` (`input_matrix_id`);

--
-- Indexes for table `ahp_weights_pekerjaan`
--
ALTER TABLE `ahp_weights_pekerjaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `input_matrix_id` (`input_matrix_id`);

--
-- Indexes for table `ahp_weights_tema_skripsi`
--
ALTER TABLE `ahp_weights_tema_skripsi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `input_matrix_id` (`input_matrix_id`);

--
-- Indexes for table `decision_makers`
--
ALTER TABLE `decision_makers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_dm_input_matrix` (`input_matrix_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ahp_input_matrices`
--
ALTER TABLE `ahp_input_matrices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ahp_weights_kriteria`
--
ALTER TABLE `ahp_weights_kriteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ahp_weights_minat_bakat`
--
ALTER TABLE `ahp_weights_minat_bakat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ahp_weights_pekerjaan`
--
ALTER TABLE `ahp_weights_pekerjaan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ahp_weights_tema_skripsi`
--
ALTER TABLE `ahp_weights_tema_skripsi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `decision_makers`
--
ALTER TABLE `decision_makers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ahp_weights_kriteria`
--
ALTER TABLE `ahp_weights_kriteria`
  ADD CONSTRAINT `ahp_weights_kriteria_ibfk_1` FOREIGN KEY (`input_matrix_id`) REFERENCES `ahp_input_matrices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ahp_weights_minat_bakat`
--
ALTER TABLE `ahp_weights_minat_bakat`
  ADD CONSTRAINT `ahp_weights_minat_bakat_ibfk_1` FOREIGN KEY (`input_matrix_id`) REFERENCES `ahp_input_matrices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ahp_weights_pekerjaan`
--
ALTER TABLE `ahp_weights_pekerjaan`
  ADD CONSTRAINT `ahp_weights_pekerjaan_ibfk_1` FOREIGN KEY (`input_matrix_id`) REFERENCES `ahp_input_matrices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ahp_weights_tema_skripsi`
--
ALTER TABLE `ahp_weights_tema_skripsi`
  ADD CONSTRAINT `ahp_weights_tema_skripsi_ibfk_1` FOREIGN KEY (`input_matrix_id`) REFERENCES `ahp_input_matrices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `decision_makers`
--
ALTER TABLE `decision_makers`
  ADD CONSTRAINT `fk_dm_input_matrix` FOREIGN KEY (`input_matrix_id`) REFERENCES `ahp_input_matrices` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
