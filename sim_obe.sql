-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jun 27, 2025 at 09:32 AM
-- Server version: 8.0.35
-- PHP Version: 8.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sim_obe`
--

-- --------------------------------------------------------

--
-- Table structure for table `bahan_kajian`
--

CREATE TABLE `bahan_kajian` (
  `id` bigint UNSIGNED NOT NULL,
  `kurikulum_id` bigint UNSIGNED NOT NULL,
  `kode_bk` varchar(255) NOT NULL,
  `nama_bk` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `acuan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bahan_kajian`
--

INSERT INTO `bahan_kajian` (`id`, `kurikulum_id`, `kode_bk`, `nama_bk`, `acuan`) VALUES
(4, 11, 'BK01', 'Virtual System and Services', 'IT-2917'),
(5, 11, 'BK02', 'Internet of Things', 'IT-2017');

-- --------------------------------------------------------

--
-- Table structure for table `bk_mk`
--

CREATE TABLE `bk_mk` (
  `id` bigint UNSIGNED NOT NULL,
  `bk_id` bigint UNSIGNED NOT NULL,
  `mk_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bk_mk`
--

INSERT INTO `bk_mk` (`id`, `bk_id`, `mk_id`) VALUES
(4, 4, 10),
(3, 5, 10),
(5, 5, 20);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1751016705),
('a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1751016705;', 1751016705),
('fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'i:3;', 1742491680),
('fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f:timer', 'i:1742491680;', 1742491680);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cpl`
--

CREATE TABLE `cpl` (
  `id` bigint UNSIGNED NOT NULL,
  `kurikulum_id` bigint UNSIGNED NOT NULL,
  `nama_cpl` varchar(255) NOT NULL,
  `cpl_ke` int NOT NULL,
  `deskripsi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cpl`
--

INSERT INTO `cpl` (`id`, `kurikulum_id`, `nama_cpl`, `cpl_ke`, `deskripsi`) VALUES
(19, 10, 'Tes CPL Kurikulum TS', 0, '0'),
(24, 11, 'CPL 1', 1, 'Mampu menguasai, memformulasikan, dan menerapkan konsep teoritis matematika, sains, keteknikan, dan teknologi komputasi secara mendalam, dalam penyelesaian pemasalahan nyata dan kompleks secara prosedural.'),
(25, 11, 'CPL 2', 2, 'Tes CPL 2 Deskripsi'),
(26, 11, 'CPL 3', 3, 'Tes CPL 3 Deskripsi'),
(27, 10, 'TS', 1, 'TSTS'),
(28, 11, 'CPL 4', 4, 'Tes CPL 4 Deskripsi'),
(29, 11, 'CPL 5', 5, 'Tes CPL 5 Deskripsi'),
(30, 11, 'CPL 6', 6, 'Tes Deskripsi Cpl 6'),
(31, 11, 'CPL 7', 7, 'CPL 7 Deskripsi'),
(32, 9, 'CPL 1', 1, 'Tes CPL 1 Teknik Mesin');

-- --------------------------------------------------------

--
-- Table structure for table `cpl_bk`
--

CREATE TABLE `cpl_bk` (
  `id` bigint UNSIGNED NOT NULL,
  `cpl_id` bigint UNSIGNED NOT NULL,
  `bk_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cpl_mk`
--

CREATE TABLE `cpl_mk` (
  `id` bigint UNSIGNED NOT NULL,
  `cpl_id` bigint UNSIGNED NOT NULL,
  `mk_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cpl_mk`
--

INSERT INTO `cpl_mk` (`id`, `cpl_id`, `mk_id`) VALUES
(9, 24, 10),
(12, 24, 11),
(15, 24, 13),
(47, 24, 20),
(10, 25, 10),
(17, 26, 13),
(32, 28, 13),
(35, 28, 14),
(48, 28, 20),
(30, 29, 11),
(28, 30, 10),
(31, 30, 11),
(24, 30, 13),
(36, 30, 14),
(49, 30, 20),
(37, 31, 10),
(38, 32, 17),
(46, 32, 19);

-- --------------------------------------------------------

--
-- Table structure for table `cpl_pl`
--

CREATE TABLE `cpl_pl` (
  `id` bigint UNSIGNED NOT NULL,
  `cpl_id` bigint UNSIGNED NOT NULL,
  `pl_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cpl_pl`
--

INSERT INTO `cpl_pl` (`id`, `cpl_id`, `pl_id`) VALUES
(1, 24, 1),
(2, 24, 2);

-- --------------------------------------------------------

--
-- Table structure for table `cpmk`
--

CREATE TABLE `cpmk` (
  `id` bigint UNSIGNED NOT NULL,
  `cpl_mk_id` bigint UNSIGNED NOT NULL,
  `kode_cpmk` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `bobot` int DEFAULT NULL,
  `batas_nilai_lulus` int DEFAULT NULL,
  `batas_nilai_memuaskan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cpmk`
--

INSERT INTO `cpmk` (`id`, `cpl_mk_id`, `kode_cpmk`, `deskripsi`, `bobot`, `batas_nilai_lulus`, `batas_nilai_memuaskan`) VALUES
(12, 9, 'CPMK - 1', 'Mampu melakukan pendeteksian, pencegahan, dan recovery sistem terhadap serangan siber', 10, 60, 80),
(13, 9, 'CPMK - 2', 'Tes Deskripsi CPMK', 15, 60, 80),
(14, 10, 'CPMK - 3', 'Tes Deskripsi CPMK', 20, 60, 80),
(15, 10, 'CPMK - 4', 'Tes Deskripsi CPMK', 15, 60, 80),
(16, 10, 'CPMK - 5', 'Tes Deskripsi CPMK', 20, 60, 80),
(18, 28, 'CPMK - 6', 'Tes tes', 10, 60, 80),
(19, 37, 'CPMK - 7', 'Tes tes', 10, 60, 80),
(20, 12, 'CPMK - 1', 'Tes tes', 40, 90, 100),
(21, 30, 'CPMK - 2', 'Tes tes', 30, 90, 100),
(22, 31, 'CPMK - 3', 'Tes tes', 30, 90, 100),
(23, 35, 'CPMK - 1', 'Tes tes', 50, 95, 100),
(24, 36, 'CPMK - 2', 'Tes tes', 50, 95, 100),
(25, 17, 'CPMK - 1', 'Mampu menjelaskan peran teknologi informasi dalam mendukung operasional organisasi.', 50, 70, 80),
(26, 32, 'CPMK - 2', 'Tes Deskripsi CPMK', 50, 50, 70),
(27, 48, 'CPMK - 1', 'Tes tes', 50, 60, 80);

-- --------------------------------------------------------

--
-- Table structure for table `cpmk_mahasiswa`
--

CREATE TABLE `cpmk_mahasiswa` (
  `id` bigint UNSIGNED NOT NULL,
  `cpmk_id` bigint UNSIGNED NOT NULL,
  `krs_mahasiswa_id` bigint UNSIGNED NOT NULL,
  `nilai` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cpmk_mahasiswa`
--

INSERT INTO `cpmk_mahasiswa` (`id`, `cpmk_id`, `krs_mahasiswa_id`, `nilai`) VALUES
(88, 20, 19, 70),
(89, 22, 19, 90),
(90, 20, 23, 70),
(91, 22, 23, 50),
(92, 20, 27, 50),
(93, 22, 27, 90),
(94, 20, 31, 70),
(95, 22, 31, 60),
(96, 20, 34, 40),
(97, 22, 34, 90),
(98, 21, 19, 100),
(99, 21, 23, 80),
(100, 21, 27, 90),
(101, 21, 31, 80),
(102, 21, 34, 100),
(103, 12, 18, 90),
(104, 13, 18, 80),
(105, 14, 18, 70),
(106, 15, 18, 80),
(107, 16, 18, 90),
(108, 18, 18, 80),
(109, 19, 18, 80),
(110, 12, 22, 90),
(111, 13, 22, 80),
(112, 14, 22, 70),
(113, 15, 22, 80),
(114, 16, 22, 90),
(115, 18, 22, 80),
(116, 19, 22, 80),
(117, 12, 26, 90),
(118, 13, 26, 80),
(119, 14, 26, 70),
(120, 15, 26, 80),
(121, 16, 26, 90),
(122, 18, 26, 80),
(123, 19, 26, 80),
(124, 12, 30, 90),
(125, 13, 30, 80),
(126, 14, 30, 70),
(127, 15, 30, 80),
(128, 16, 30, 90),
(129, 18, 30, 80),
(130, 19, 30, 80),
(131, 23, 21, 80),
(132, 24, 21, 70),
(133, 23, 25, 90),
(134, 24, 25, 70),
(135, 23, 29, 90),
(136, 24, 29, 60),
(139, 23, 37, 90),
(140, 24, 37, 60),
(141, 12, 40, 80),
(142, 13, 40, 90),
(143, 14, 40, 70),
(144, 15, 40, 60),
(145, 16, 40, 80),
(146, 18, 40, 100),
(147, 19, 40, 80),
(148, 12, 41, 70),
(149, 13, 41, 80),
(150, 14, 41, 90),
(151, 15, 41, 70),
(152, 16, 41, 60),
(153, 18, 41, 95),
(154, 19, 41, 90),
(158, 12, 42, 80);

-- --------------------------------------------------------

--
-- Table structure for table `krs_mahasiswa`
--

CREATE TABLE `krs_mahasiswa` (
  `id` bigint UNSIGNED NOT NULL,
  `mk_ditawarkan_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `krs_mahasiswa`
--

INSERT INTO `krs_mahasiswa` (`id`, `mk_ditawarkan_id`, `user_id`) VALUES
(18, 2, 10),
(22, 2, 11),
(26, 2, 12),
(30, 2, 13),
(42, 2, 14),
(19, 3, 10),
(23, 3, 11),
(27, 3, 12),
(31, 3, 13),
(34, 3, 14),
(20, 4, 10),
(24, 4, 11),
(28, 4, 12),
(32, 4, 13),
(35, 4, 14),
(36, 4, 14),
(21, 7, 10),
(25, 7, 11),
(29, 7, 12),
(37, 7, 14),
(40, 11, 16),
(41, 11, 17);

-- --------------------------------------------------------

--
-- Table structure for table `kurikulums`
--

CREATE TABLE `kurikulums` (
  `id` bigint UNSIGNED NOT NULL,
  `prodi_id` bigint UNSIGNED NOT NULL,
  `nama_kurikulum` varchar(255) NOT NULL,
  `status` enum('aktif','tidak aktif') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'tidak aktif'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kurikulums`
--

INSERT INTO `kurikulums` (`id`, `prodi_id`, `nama_kurikulum`, `status`) VALUES
(9, 3, 'Tes Kurikulum TM 1', 'tidak aktif'),
(10, 2, 'Tes Kurikulum TS', 'aktif'),
(11, 1, 'Kurikulum 2022', 'aktif'),
(12, 1, 'Kurikulum 2020', 'tidak aktif'),
(13, 3, 'Tes Kurikulum TM 2', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` bigint UNSIGNED NOT NULL,
  `cpmk_id` bigint UNSIGNED NOT NULL,
  `mk_ditawarkan_id` bigint UNSIGNED NOT NULL,
  `faktor_pendukung_kendala` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `rtl` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `cpmk_id`, `mk_ditawarkan_id`, `faktor_pendukung_kendala`, `rtl`) VALUES
(4, 12, 2, 'testes', 'testes'),
(5, 13, 2, 'testess', 'testestss'),
(6, 14, 2, 'tes 1', 'tes rtl');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa_pengajar`
--

CREATE TABLE `mahasiswa_pengajar` (
  `id` bigint UNSIGNED NOT NULL,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `pengajar_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mahasiswa_pengajar`
--

INSERT INTO `mahasiswa_pengajar` (`id`, `mahasiswa_id`, `pengajar_id`) VALUES
(4, 10, 8),
(5, 11, 8);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mk`
--

CREATE TABLE `mk` (
  `id` bigint UNSIGNED NOT NULL,
  `kurikulum_id` bigint UNSIGNED NOT NULL,
  `kode` varchar(255) NOT NULL,
  `nama_mk` varchar(255) NOT NULL,
  `sks` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mk`
--

INSERT INTO `mk` (`id`, `kurikulum_id`, `kode`, `nama_mk`, `sks`) VALUES
(10, 11, 'TI100', 'Baca Tulis Al Qur\'an', 3),
(11, 11, 'TI101', 'Fisika dan Elektronika Teknologi', 3),
(13, 11, 'TI102', 'Matematika Teknologi Informasi 1', 0),
(14, 11, 'TI103', 'Dasar Teknologi Informasi', 0),
(17, 9, 'TM100', 'Mata Kuliah Teknik Mesin', 0),
(19, 9, 'TM100', 'tes', 3),
(20, 11, 'Tes12', 'Tes MataKuliah', 3);

-- --------------------------------------------------------

--
-- Table structure for table `mk_ditawarkan`
--

CREATE TABLE `mk_ditawarkan` (
  `id` bigint UNSIGNED NOT NULL,
  `semester_id` bigint UNSIGNED NOT NULL,
  `mk_id` bigint UNSIGNED NOT NULL,
  `rps` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `kelas` enum('A','B','C','D','E','F') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mk_ditawarkan`
--

INSERT INTO `mk_ditawarkan` (`id`, `semester_id`, `mk_id`, `rps`, `kelas`) VALUES
(2, 1, 10, NULL, 'A'),
(3, 1, 11, NULL, 'B'),
(4, 1, 13, NULL, 'C'),
(7, 1, 14, 'rps-files/Bukti Sumbangan.pdf', 'D'),
(8, 1, 17, NULL, 'A'),
(11, 1, 10, NULL, 'B'),
(12, 1, 20, NULL, 'C');

-- --------------------------------------------------------

--
-- Table structure for table `mk_ditawarkan_pengajar`
--

CREATE TABLE `mk_ditawarkan_pengajar` (
  `id` bigint UNSIGNED NOT NULL,
  `mk_ditawarkan_id` bigint UNSIGNED NOT NULL,
  `pengajar_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mk_ditawarkan_pengajar`
--

INSERT INTO `mk_ditawarkan_pengajar` (`id`, `mk_ditawarkan_id`, `pengajar_id`) VALUES
(3, 2, 1),
(4, 2, 3),
(5, 3, 1),
(6, 3, 3),
(1, 7, 1),
(2, 7, 3),
(7, 8, 5),
(10, 11, 1),
(11, 12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pengajar`
--

CREATE TABLE `pengajar` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pengajar`
--

INSERT INTO `pengajar` (`id`, `user_id`) VALUES
(1, 8),
(3, 9),
(4, 2),
(5, 15);

-- --------------------------------------------------------

--
-- Table structure for table `pl`
--

CREATE TABLE `pl` (
  `id` bigint UNSIGNED NOT NULL,
  `kurikulum_id` bigint UNSIGNED NOT NULL,
  `kode` varchar(255) NOT NULL,
  `nama_pl` varchar(255) NOT NULL,
  `unsur` varchar(255) NOT NULL,
  `sumber` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pl`
--

INSERT INTO `pl` (`id`, `kurikulum_id`, `kode`, `nama_pl`, `unsur`, `sumber`) VALUES
(1, 11, 'PL-01', 'Profesional teknologi informasi yang mampu menggunakan pengetahuan computing untuk menganalisis permasalahan computing yang kompleks dan memberikan solusi dengan pendekatan teknologi informasi.', 'Pengetahuan', 'IT2017, Seoul Accord'),
(2, 11, 'PL-2', 'Tes tess', 'tes tess', 'tes tes');

-- --------------------------------------------------------

--
-- Table structure for table `prodis`
--

CREATE TABLE `prodis` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_prodi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prodis`
--

INSERT INTO `prodis` (`id`, `nama_prodi`) VALUES
(1, 'Teknologi Informasi'),
(2, 'Teknik Sipil'),
(3, 'Teknik Mesin'),
(4, 'Teknik Electro');

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint UNSIGNED NOT NULL,
  `angka_semester` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`id`, `tahun_ajaran_id`, `angka_semester`) VALUES
(1, 1, 2),
(2, 1, 4),
(3, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('hdSwUoGADFbhyOO1d7sXdeke1IUbQKdzmraGBz3h', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiYVVEbzdsOEd4NGpxWEFiQVRqMTVvWXJad3RiT0czcE9zb2JDdjE4MiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi91c2Vycy9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkY3FWZXpiLzN4NkZQY2kyUk5yRG5LdUJ1Ry9Nck80STNCRFV5ZzdtQkhiRi9Qb3E4cDNVOWUiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=', 1751016316),
('sdwCRq5BjvhOJbhNYWeUrJ7EduQkOUKnlOAFXovp', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRkN3elQ1RU9qRWY1dEhVT1ZvTHY3bENkQzJKaVAxbEV0VGZoOEZyZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9rdXJpa3VsdW1zIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJGNxVmV6Yi8zeDZGUGNpMlJOckRuS3VCdUcvTXJPNEkzQkRVeWc3bUJIYkYvUG9xOHAzVTllIjtzOjg6ImZpbGFtZW50IjthOjA6e319', 1751016718);

-- --------------------------------------------------------

--
-- Table structure for table `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_tahun_ajaran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`id`, `nama_tahun_ajaran`) VALUES
(1, 'Genap 2023/2024'),
(2, 'Genap 2022/2023');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('Prodi','Dosen','Mahasiswa','Staf') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Dosen',
  `angkatan` varchar(5) DEFAULT NULL,
  `nim` varchar(15) DEFAULT NULL,
  `nip` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `angkatan`, `nim`, `nip`) VALUES
(1, 'Admin TI', 'admin_ti@umy.ac.id', NULL, '$2y$12$cqVezb/3x6FPci2RNrDnKuBuG/MrO4I3BDUyg7mBHbF/Poq8p3U9e', 'x8OTJ3bfc0aki5zeeAEWPlLEiW8k0JEqaOJbfMlS9ujdpEctZFWvGWh2Bph0', '2024-09-29 23:24:45', '2024-09-29 23:24:45', 'Prodi', NULL, NULL, NULL),
(2, 'Admin TS', 'admin_ts@umy.ac.id', NULL, '$2y$12$RDE9tHuGEsxAmL8UsYCF/uiJ3NhmqVJWkXjhtTFHlFPvm9rQfH9fa', NULL, '2024-09-30 08:30:45', '2024-09-30 08:30:45', 'Dosen', NULL, NULL, NULL),
(5, 'Tes TM', 'testm@umy.ac.id', NULL, '$2y$12$w59IhmZaopMx4tPPO0ORxe6bWPZxHYTTN7ljanXp0u8pwvihiRVrq', NULL, '2024-10-24 08:06:17', '2024-12-18 17:39:03', 'Mahasiswa', '2020', '2020202020', NULL),
(6, 'tes', 'tes@gmail.com', NULL, '$2y$12$wqjLl9Av3FXRKBGh3rtvbu75/sHnoZjyo/N9XrFHrXLl2/ENo3iOS', NULL, '2024-10-24 08:07:32', '2024-10-24 08:07:32', 'Mahasiswa', NULL, '21212121212', NULL),
(8, 'Dosen TI', 'dosen_ti@umy.ac.id', NULL, '$2y$12$Mhrz9O3S9jvl5h0cfdcl4.852CMzBrIiW1Ob5WAExBOe8ahtp6EhC', NULL, '2024-10-29 09:48:48', '2024-10-29 09:48:48', 'Dosen', NULL, NULL, '202001493837'),
(9, 'Staff TI', 'staff_ti@umy.ac.id', NULL, '$2y$12$SPRIyKimiWTaOsVQCwvBZ.InFLYEXHgKkrJD1ZOTBNvnVZixsR55a', NULL, '2024-10-29 09:59:33', '2024-10-29 09:59:33', 'Staf', NULL, NULL, '202920219'),
(10, 'Naufal Rozan', 'naufal.rozan.ft20@mail.umy.ac.id', NULL, '$2y$12$FdZGcvi/wpBZKrpoT6YZ8.MEL3vv0XkWW2zEOK.1TCg7GoOFHMWlG', NULL, '2024-11-04 22:10:55', '2024-12-18 17:39:12', 'Mahasiswa', '2020', '20200140036', NULL),
(11, 'Indra Mukti', 'indra@umy.ac.id', NULL, '$2y$12$NrAa1qqdGZGqbhU1Wl.CVOsedwORcNy2NkQjryXzDwXxjfqbuuX6S', NULL, '2024-11-04 22:11:51', '2024-12-18 17:39:21', 'Mahasiswa', '2020', '20200140100', NULL),
(12, 'Dzaki', 'dzaki@umy.ac.id', NULL, '$2y$12$94qRaNnMOGFGIaiLImkOAe6L74Hqm.qBiyRGNnCvP1o55PTGCnaG2', NULL, '2024-11-04 22:12:22', '2024-12-18 17:40:13', 'Mahasiswa', '2019', '20200140067', NULL),
(13, 'Muhammad Barik', 'barik@gmail.com', NULL, '$2y$12$.IIGswX5tEoNou.B.7/XMOdGVS6ffpZwc9dy4W4Ie/UFkglixnvXS', NULL, '2024-11-04 22:13:03', '2025-03-14 16:51:37', 'Mahasiswa', '2018', '20200140123', NULL),
(14, 'Rahmatullah', 'rahmat@umy.ac.id', NULL, '$2y$12$edKqqFh.OPFlUV1j7KTzvuMKmUSaCTPnYXkLafwmjNZW8BKOqEceG', NULL, '2024-11-04 22:13:32', '2024-11-04 22:13:32', 'Mahasiswa', NULL, '20200140088', NULL),
(15, 'Dosen TM', 'dosen_tm@umy.ac.id', NULL, '$2y$12$63XGczHMEm3q0j.0qd.tceDqHRG5aTQlqCa7ZFZ2EvdeWcHvRAApm', NULL, '2024-11-05 21:06:55', '2024-11-05 21:06:55', 'Dosen', NULL, NULL, '2002020202'),
(16, 'Adelia', 'adel@umy.ac.id', NULL, '$2y$12$.SXoJCjLLcmaRyySWNhpwuqwAX80g.EL.AlXdepKmY5zAj42vTgtq', NULL, '2024-11-07 00:20:24', '2024-11-07 00:20:24', 'Mahasiswa', NULL, '202000202', NULL),
(17, 'Andre', 'andre@umy.ac.id', NULL, '$2y$12$/dSfA4wFIEuQ9unE/A20zOQpxKdy3woc9YrT2UloqigWouvjWFcWG', NULL, '2024-11-07 00:20:58', '2024-11-07 00:20:58', 'Mahasiswa', NULL, '202928302', NULL),
(18, 'Admin TM', 'admin_tm@umy.ac.id', NULL, '$2y$12$5Flaea6igKKRfHp6EBqnEuU6UCIU9bQyQWCVbg.QD/7rljdoA/HB6', NULL, '2024-12-11 16:20:02', '2024-12-11 16:20:02', 'Prodi', NULL, NULL, NULL),
(19, 'Mahasiswa Demo 1 - Teknologi Informasi', 'mahasiswa@demo.com', NULL, '$2y$12$yEaStOSOF0eIAqxhinVKbebjHRnk55exZWMJWx0fHledgWLlqlChe', NULL, '2025-06-27 02:24:47', '2025-06-27 02:24:47', 'Mahasiswa', '2020', '20200202020', NULL),
(21, 'Prodi Demo', 'prodi@demo.com', NULL, '$2y$12$45GIfXBqRYl0WKKbTQEZGOhw0d4NJhXoVwkfHZYMxS4Qu1qJYwJe6', NULL, '2025-06-27 02:31:19', '2025-06-27 02:31:19', 'Prodi', NULL, NULL, NULL),
(22, 'Pengajar Demo - Teknologi Informasi', 'pengajar@demo.com', NULL, '$2y$12$ki.gi7pGNMRPAhqgEMiH/.1Xh5MGACs.C1COslL779ENbGaaQcasm', NULL, '2025-06-27 02:31:50', '2025-06-27 02:31:50', 'Dosen', NULL, NULL, '202032392');

-- --------------------------------------------------------

--
-- Table structure for table `user_prodi`
--

CREATE TABLE `user_prodi` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `prodi_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_prodi`
--

INSERT INTO `user_prodi` (`id`, `user_id`, `prodi_id`) VALUES
(1, 1, 1),
(3, 2, 2),
(7, 5, 3),
(8, 6, 4),
(10, 8, 1),
(11, 9, 1),
(12, 10, 1),
(13, 11, 1),
(14, 12, 1),
(15, 13, 1),
(16, 14, 1),
(17, 15, 3),
(18, 16, 1),
(19, 17, 1),
(20, 18, 3),
(21, 19, 1),
(23, 21, 1),
(24, 22, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bahan_kajian`
--
ALTER TABLE `bahan_kajian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kurikulum_id` (`kurikulum_id`);

--
-- Indexes for table `bk_mk`
--
ALTER TABLE `bk_mk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bk_id` (`bk_id`,`mk_id`),
  ADD KEY `mk_id` (`mk_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cpl`
--
ALTER TABLE `cpl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kurikulum_id` (`kurikulum_id`);

--
-- Indexes for table `cpl_bk`
--
ALTER TABLE `cpl_bk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cpl_id` (`cpl_id`,`bk_id`),
  ADD KEY `bk_id` (`bk_id`);

--
-- Indexes for table `cpl_mk`
--
ALTER TABLE `cpl_mk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cpl_id` (`cpl_id`,`mk_id`),
  ADD KEY `mk_id` (`mk_id`);

--
-- Indexes for table `cpl_pl`
--
ALTER TABLE `cpl_pl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cpl_id` (`cpl_id`,`pl_id`),
  ADD KEY `pl_id` (`pl_id`);

--
-- Indexes for table `cpmk`
--
ALTER TABLE `cpmk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mk_id` (`cpl_mk_id`);

--
-- Indexes for table `cpmk_mahasiswa`
--
ALTER TABLE `cpmk_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cpmk_id` (`cpmk_id`,`krs_mahasiswa_id`),
  ADD KEY `krs_mahasiswa_id` (`krs_mahasiswa_id`);

--
-- Indexes for table `krs_mahasiswa`
--
ALTER TABLE `krs_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mk_ditawarkan_id` (`mk_ditawarkan_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kurikulums`
--
ALTER TABLE `kurikulums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prodi_id` (`prodi_id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cpmk_id` (`cpmk_id`,`mk_ditawarkan_id`),
  ADD KEY `mk_ditawarkan_id` (`mk_ditawarkan_id`);

--
-- Indexes for table `mahasiswa_pengajar`
--
ALTER TABLE `mahasiswa_pengajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mahasiswa_id` (`mahasiswa_id`,`pengajar_id`),
  ADD KEY `pengajar_id` (`pengajar_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mk`
--
ALTER TABLE `mk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kurikulum_id` (`kurikulum_id`);

--
-- Indexes for table `mk_ditawarkan`
--
ALTER TABLE `mk_ditawarkan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semester_id` (`semester_id`,`mk_id`),
  ADD KEY `mk_id` (`mk_id`);

--
-- Indexes for table `mk_ditawarkan_pengajar`
--
ALTER TABLE `mk_ditawarkan_pengajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mk_ditawarkan_id` (`mk_ditawarkan_id`,`pengajar_id`),
  ADD KEY `pengajar_id` (`pengajar_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pengajar`
--
ALTER TABLE `pengajar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pl`
--
ALTER TABLE `pl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kurikulum_id` (`kurikulum_id`);

--
-- Indexes for table `prodis`
--
ALTER TABLE `prodis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun_ajaran_id` (`tahun_ajaran_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_prodi`
--
ALTER TABLE `user_prodi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`prodi_id`),
  ADD KEY `prodi_id` (`prodi_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bahan_kajian`
--
ALTER TABLE `bahan_kajian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bk_mk`
--
ALTER TABLE `bk_mk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cpl`
--
ALTER TABLE `cpl`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `cpl_bk`
--
ALTER TABLE `cpl_bk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cpl_mk`
--
ALTER TABLE `cpl_mk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `cpl_pl`
--
ALTER TABLE `cpl_pl`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cpmk`
--
ALTER TABLE `cpmk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `cpmk_mahasiswa`
--
ALTER TABLE `cpmk_mahasiswa`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `krs_mahasiswa`
--
ALTER TABLE `krs_mahasiswa`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `kurikulums`
--
ALTER TABLE `kurikulums`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mahasiswa_pengajar`
--
ALTER TABLE `mahasiswa_pengajar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mk`
--
ALTER TABLE `mk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `mk_ditawarkan`
--
ALTER TABLE `mk_ditawarkan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `mk_ditawarkan_pengajar`
--
ALTER TABLE `mk_ditawarkan_pengajar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengajar`
--
ALTER TABLE `pengajar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pl`
--
ALTER TABLE `pl`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prodis`
--
ALTER TABLE `prodis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user_prodi`
--
ALTER TABLE `user_prodi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bahan_kajian`
--
ALTER TABLE `bahan_kajian`
  ADD CONSTRAINT `bahan_kajian_ibfk_1` FOREIGN KEY (`kurikulum_id`) REFERENCES `kurikulums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bk_mk`
--
ALTER TABLE `bk_mk`
  ADD CONSTRAINT `bk_mk_ibfk_1` FOREIGN KEY (`bk_id`) REFERENCES `bahan_kajian` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bk_mk_ibfk_2` FOREIGN KEY (`mk_id`) REFERENCES `mk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cpl`
--
ALTER TABLE `cpl`
  ADD CONSTRAINT `cpl_ibfk_1` FOREIGN KEY (`kurikulum_id`) REFERENCES `kurikulums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cpl_bk`
--
ALTER TABLE `cpl_bk`
  ADD CONSTRAINT `cpl_bk_ibfk_1` FOREIGN KEY (`bk_id`) REFERENCES `bahan_kajian` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cpl_bk_ibfk_2` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cpl_mk`
--
ALTER TABLE `cpl_mk`
  ADD CONSTRAINT `cpl_mk_ibfk_1` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cpl_mk_ibfk_2` FOREIGN KEY (`mk_id`) REFERENCES `mk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cpl_pl`
--
ALTER TABLE `cpl_pl`
  ADD CONSTRAINT `cpl_pl_ibfk_1` FOREIGN KEY (`pl_id`) REFERENCES `pl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cpl_pl_ibfk_2` FOREIGN KEY (`cpl_id`) REFERENCES `cpl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cpmk`
--
ALTER TABLE `cpmk`
  ADD CONSTRAINT `cpmk_ibfk_1` FOREIGN KEY (`cpl_mk_id`) REFERENCES `cpl_mk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cpmk_mahasiswa`
--
ALTER TABLE `cpmk_mahasiswa`
  ADD CONSTRAINT `cpmk_mahasiswa_ibfk_1` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cpmk_mahasiswa_ibfk_2` FOREIGN KEY (`krs_mahasiswa_id`) REFERENCES `krs_mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `krs_mahasiswa`
--
ALTER TABLE `krs_mahasiswa`
  ADD CONSTRAINT `krs_mahasiswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `krs_mahasiswa_ibfk_2` FOREIGN KEY (`mk_ditawarkan_id`) REFERENCES `mk_ditawarkan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kurikulums`
--
ALTER TABLE `kurikulums`
  ADD CONSTRAINT `kurikulums_ibfk_1` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`cpmk_id`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`mk_ditawarkan_id`) REFERENCES `mk_ditawarkan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mahasiswa_pengajar`
--
ALTER TABLE `mahasiswa_pengajar`
  ADD CONSTRAINT `mahasiswa_pengajar_ibfk_1` FOREIGN KEY (`mahasiswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mahasiswa_pengajar_ibfk_2` FOREIGN KEY (`pengajar_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mk`
--
ALTER TABLE `mk`
  ADD CONSTRAINT `mk_ibfk_1` FOREIGN KEY (`kurikulum_id`) REFERENCES `kurikulums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mk_ditawarkan`
--
ALTER TABLE `mk_ditawarkan`
  ADD CONSTRAINT `mk_ditawarkan_ibfk_1` FOREIGN KEY (`mk_id`) REFERENCES `mk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mk_ditawarkan_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mk_ditawarkan_pengajar`
--
ALTER TABLE `mk_ditawarkan_pengajar`
  ADD CONSTRAINT `mk_ditawarkan_pengajar_ibfk_1` FOREIGN KEY (`mk_ditawarkan_id`) REFERENCES `mk_ditawarkan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mk_ditawarkan_pengajar_ibfk_2` FOREIGN KEY (`pengajar_id`) REFERENCES `pengajar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pl`
--
ALTER TABLE `pl`
  ADD CONSTRAINT `pl_ibfk_1` FOREIGN KEY (`kurikulum_id`) REFERENCES `kurikulums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `semester`
--
ALTER TABLE `semester`
  ADD CONSTRAINT `semester_ibfk_1` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_prodi`
--
ALTER TABLE `user_prodi`
  ADD CONSTRAINT `user_prodi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_prodi_ibfk_2` FOREIGN KEY (`prodi_id`) REFERENCES `prodis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
