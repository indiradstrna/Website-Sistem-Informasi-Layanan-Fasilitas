-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Jun 2026 pada 10.05
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bioform_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `nip_nik` varchar(50) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `role_title` varchar(50) DEFAULT NULL,
  `position` varchar(150) DEFAULT NULL,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `education` varchar(50) DEFAULT NULL,
  `tenure` varchar(50) DEFAULT NULL,
  `certificates` text DEFAULT NULL,
  `employee_type` enum('PNS','PPPK','Kontrak','Outsource') DEFAULT 'PNS',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(50) DEFAULT 'outsourcing',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employees`
--

INSERT INTO `employees` (`id`, `nip_nik`, `full_name`, `department`, `role_title`, `position`, `whatsapp_number`, `education`, `tenure`, `certificates`, `employee_type`, `is_active`, `created_at`, `type`, `updated_at`) VALUES
(63, '123456', 'Indira Destriana Anjani', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-01-25 19:08:34', 'outsourcing', '2026-04-14 02:33:07'),
(64, '111111', 'Sheni Olvianda', 'FMD', 'Internship', NULL, NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-02 22:34:19', 'outsourcing', '2026-04-14 02:33:07'),
(66, '197707072025211067', 'Slamet Widodo Sugiarto', 'FMD', 'Manager', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-18 19:20:21', 'outsourcing', '2026-04-14 02:33:07'),
(111, '16267400035', 'AGUS', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:07'),
(112, '16269400052', 'AHMAD SUDRAJAT', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:07'),
(114, '16268800019', 'ANDI SUHANDI', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(115, '16260400054', 'ANGGI PURNAMA', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(116, '16260400053', 'AULIA RIZKY ANANDA', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(117, '16267900036', 'DADANG SETIAWAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(118, '16268000018', 'DEDE MAULANA', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(119, '16267800056', 'DIDIT TRISNADI', 'FMD', 'Staff', 'DRIVER', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(120, '16267900038', 'ENDANG SETIAWAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(121, '16266700037', 'ENDIH', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(122, '16269200058', 'ERWAN PRAYOGI', 'FMD', 'Staff', 'DRIVER', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(123, '16267600032', 'HASAN HUSEN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(124, '16268900039', 'HERDI FERDIANSYAH MAULANA', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(125, '16268600040', 'HERMAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(126, '16268000041', 'HERU', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(127, '16267600022', 'ICHWAN PIDIN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(128, '16268000042', 'IDA HERMAWAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(129, '16267000050', 'IDIM DIMYATI', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(130, '16268200024', 'INDRA ARDIANSYAH', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(131, '16268700030', 'ISNANDAR', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(132, '16268600017', 'KHAMELANDI ASTRIAN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(133, '16268500020', 'KOMARUDIN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(134, '16268200025', 'KURNIAWAN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(135, '16267600023', 'KUSNADI', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(136, '16269000031', 'LINGGAR', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(137, '16267800043', 'M. ABUDIN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(138, '16268300026', 'MAULANA HASANUDIN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(139, '16268300055', 'MOH.WAHYU NOORRAMDHANY Y.', 'FMD', 'Staff', 'SUPERVISOR PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(140, '16267100051', 'MUHAMAD ZAENUDDIN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(141, '16260500034', 'MUHAMMAD NAZRIL ILHAM', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(142, '16267800044', 'MULYADI', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(143, '16266800045', 'MULYANA', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(144, '16268500046', 'RIDWAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(145, '16267200047', 'ROHMAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(146, '16268300028', 'RUDI HARTONO', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(147, '16268000027', 'SARIPUDIN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(148, '16260400016', 'SUGIH MAULANA', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(149, '16267200057', 'TAOFIK ROKAYAT', 'FMD', 'Staff', 'DRIVER', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(150, '16267500021', 'TASORI HERIYANTO', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(151, '16267700048', 'WAHYUDI', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(152, '16269000049', 'WALDI HIDAYAT', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(153, '16267800033', 'YAYA TARYANA', 'FMD', 'Staff', 'CHIEFSECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(154, '16269000029', 'ZULPA HILMAWAN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(155, 'admin', 'Administrator', 'IT', 'Super Admin', 'System Administrator', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:11:16', 'outsourcing', '2026-04-14 02:33:08'),
(156, '197212162014091003', 'Agus Sujadi', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(157, '198605082025211053', 'Alfi Dwi Nugroho, A.Md', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(158, '197009192025211011', 'Bahrudin', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(159, '198902222025211044', 'Indra Septian, A.Md', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(160, '199008092025212052', 'Lastiah, SE', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(163, '197005201996011000', 'Prof. Dr. Edi Santosa, S.P., M.Si', 'BoD', 'Direktur', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(164, '196905151992032009', 'Dr. Elis Rosdiawati, M.Pd', 'BOD', 'Deputi Direktur bidang Administrasi', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(165, 'TEMP_NIP_165', 'Dr.rer.nat. Doni Yusri, SP., MM', 'BoD', 'Deputi Direktur bidang Program', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(166, '198212062008101002', 'Peri Siantuni, SE', 'FAD', 'BPP', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(167, '198108072009101001', 'Mulyadiana Prayoga', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(168, '196902262014091001', 'Supriyatno, A.Md', 'FAD', 'Staf FAD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(169, '197007202007011002', 'Sunardi Ikay', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(170, '197708072014092001', 'Herni Widhiastuti, S.Si', 'Pengadaan', 'Pejabat Pengadaan', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(171, '197810122014092003', 'Dewi Rahmawati, M.Si', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(172, '198011052014092003', 'Nopi Ramli', 'HRAD', 'Staf HRAD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(173, '198912142015041002', 'Haritz Cahya Nugraha, M.T', 'KMD', 'Manajer KMD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(174, '198407032025212025', 'Aan Darwati, S.Ak', 'FAD', 'Staf FAD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(175, '198212142025211037', 'Aris Purnajaya', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(176, '198212182025211036', 'Asep Saepudin', 'KMD', 'Staf KMD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(177, '197212232025211010', 'Asep Syaefudin, SE', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(178, '198110072025211038', 'Dani Yudi Trisna, A.Md', 'KMD', 'Staf KMD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(179, '198210282025212041', 'Dewi Suryani Oktavia Basuki, SP., MM', 'HCID', 'Manajer HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(180, '198305182025211032', 'Didi Junaedi, A.Md', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(181, '198506302025211042', 'Fitri Junaedy, SEI', 'HRAD', 'Staf HRAD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(182, '198312212025211029', 'Hery Yanto', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(183, '197007272025211013', 'Iman', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(184, '198206272025211034', 'Irawan', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(185, '197712122025212024', 'Lidia Defita, S.Kom', 'HRAD', 'Staf HRAD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(186, '198007062025212028', 'Lillys Betty Yuliawati, S.Si', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(187, '198507172025212047', 'Risa Rosita, M.Si', 'SITD', 'Manajer  SITD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(188, '198703282025212040', 'Risya Ayu Astari, A.Md', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(189, '198912112025212050', 'Rizkia Tirtani', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(190, '197910152025211032', 'Rosadi, S.Pd.I', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(191, '198811072025211049', 'Saiful Bachri, M.Si', 'HCID', 'Supervisor HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(193, '198003182025212017', 'Tenni Wahyuni, S.I.Kom', 'HRAD', 'Manajer HRAD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(194, '199201142025212043', 'Trijanti A. Widinni Asnan, M.Si.', 'FAD', 'Manajer FAD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(195, '198109012025212021', 'Woro Kanti Dharmastuti, M.Si', 'KMD', 'Staf KMD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(196, '198605132025211031', 'Yana', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(197, '197201042025211008', 'Zaenal Abidin', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(198, '198212042025211036', 'Zulkarnaen Noor Syarif, M.Kom', 'SITD', 'Supervisor SITD', '', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `item_loan_requests`
--

CREATE TABLE `item_loan_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_unit` varchar(100) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `loan_date` date NOT NULL,
  `loan_time` time DEFAULT NULL,
  `return_date` date NOT NULL,
  `return_time` time DEFAULT NULL,
  `purpose` longtext NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `item_loan_requests`
--

INSERT INTO `item_loan_requests` (`id`, `user_id`, `applicant_name`, `applicant_unit`, `item_name`, `item_quantity`, `loan_date`, `loan_time`, `return_date`, `return_time`, `purpose`, `status`, `created_at`, `updated_at`, `note`) VALUES
(7, 136, 'Indra Septian, A.Md', 'FMD', 'Drone', 1, '2026-02-09', NULL, '2026-02-09', NULL, 'Mapping Cluster', 'approved', '2026-02-09 06:18:04', '2026-03-02 04:11:40', '[02 Mar 2026 11:11] [Indra Septian, A.Md] - APPROVED: ok'),
(8, 134, 'Hery Yanto', 'HCID', 'DJI', 1, '2026-02-10', NULL, '2026-02-10', NULL, 'Dokumentasi', 'pending', '2026-02-10 07:42:25', '2026-02-10 07:42:25', NULL),
(10, 158, 'Putri Dina Rahayu, SE', 'HRAD', 'Drone', 1, '2026-02-11', NULL, '2026-02-11', NULL, 'Mapping Cluster', 'pending', '2026-02-11 04:34:40', '2026-02-11 04:34:40', NULL),
(11, 1, 'Indira Destriana Anjani', 'FMD', 'Camera ', 1, '2026-05-18', '09:12:00', '2026-05-18', '12:12:00', 'take content podcast', 'ready_for_user', '2026-05-18 02:12:36', '2026-05-18 08:18:16', '[18 May 2026 15:17] [Indra Septian, A.Md] - WAITING_MANAGER_FMD: Asset Info: CAM-001. Camera  tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[18 May 2026 15:18] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Peminjaman Barang menyiapkan permintaan dan memberikan laporan Check & Recheck.\n[18 May 2026 15:18] [Indra Septian, A.Md] - READY_FOR_USER: PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Barang Pinjaman telah siap untuk diserahkan/dilaksanakan.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_rooms`
--

CREATE TABLE `master_rooms` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_rooms`
--

INSERT INTO `master_rooms` (`id`, `name`) VALUES
('GEDUNG_BUNDAR', 'Gedung Bundar (75-100 org)'),
('MAHONI', 'Mahoni (10-12 org)'),
('RG_DEWAN', 'Rg. Dewan (5-7 org)'),
('RG_PDID', 'Rg. PDID (20-30 org)'),
('RUANG_EBONY', 'Ruang Ebony (10-12 org)'),
('RUANG_HERBARIUM', 'Ruang Rapat Herbarium (30-35 org)'),
('RUANG_JATI', 'Ruang Jati (30-45 org)'),
('RUANG_KENARI', 'Ruang Kenari (10-12 org)'),
('RUANG_KMD', 'Ruang Rapat KMD (8-10 orang)'),
('RUANG_MATOA', 'Ruang Matoa (45-75 org)'),
('RUANG_STUDIO', 'Ruang Studio (10-12 org)');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_vehicles`
--

CREATE TABLE `master_vehicles` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_vehicles`
--

INSERT INTO `master_vehicles` (`id`, `name`) VALUES
('AVZ001', 'Panther - F 1895 A'),
('INV002', 'Panther - F 1898 A'),
('XPD001', 'Xpander Hitam B 1316 HOC'),
('XPD002', 'Xpander Silver B 1480 HKB'),
('ZNX001', 'Zennix B 1981 HKC');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_id` int(11) DEFAULT NULL,
  `request_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `resource_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `link`, `created_at`, `request_id`, `request_type`, `reference_id`, `reference_type`, `related_id`, `related_type`, `resource_id`, `resource_type`) VALUES
(1, 1, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 5, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 114, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 115, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 125, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 129, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 136, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 1, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 138, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 146, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 148, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 153, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 156, 'Peminjaman Barang Baru', 'Hery Yanto meminjam: DJI', 'info', 0, '/admin', '2026-02-10 07:42:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 1, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 2, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 5, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 114, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 115, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 125, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 129, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 136, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 1, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 138, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 146, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 148, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 153, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 156, 'Jadwal Zoom Baru', 'Indra Septian, A.Md request zoom untuk: pemilihan danru wadanru', 'info', 0, '/admin', '2026-02-10 07:43:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `repair_budgets`
--

CREATE TABLE `repair_budgets` (
  `id` int(11) NOT NULL,
  `repair_request_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `repair_budgets`
--

INSERT INTO `repair_budgets` (`id`, `repair_request_id`, `item_name`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(16, 8, 'freon', 1, 75000.00, 75000.00, '2025-12-31 03:57:29'),
(22, 2, 'Kabel HDMI', 1, 51000000.00, 51000000.00, '2026-01-09 06:10:28'),
(26, 1, 'AC Baru', 5, 5000000.00, 25000000.00, '2026-01-09 06:58:58'),
(37, 10, 'AC', 5, 5000000.00, 25000000.00, '2026-01-09 07:50:15'),
(38, 11, 'MCB Baru', 1, 20000.00, 20000.00, '2026-01-12 08:12:17'),
(39, 12, 'Mcb Baru', 1, 50000.00, 50000.00, '2026-01-21 08:16:17'),
(40, 9, 'lampu baru', 5, 10010000.00, 50050000.00, '2026-01-21 08:17:24'),
(47, 13, 'remote ac baru', 1, 750000.00, 750000.00, '2026-02-11 02:53:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `repair_requests`
--

CREATE TABLE `repair_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_unit` varchar(100) NOT NULL,
  `location_detail` varchar(255) NOT NULL,
  `incident_date` date NOT NULL,
  `incident_time` time NOT NULL,
  `issue_description` longtext NOT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `repair_requests`
--

INSERT INTO `repair_requests` (`id`, `user_id`, `applicant_name`, `applicant_unit`, `location_detail`, `incident_date`, `incident_time`, `issue_description`, `priority`, `status`, `created_at`, `updated_at`, `note`) VALUES
(1, NULL, 'Indira', 'FMD', 'Jati Room', '2025-12-08', '11:10:00', 'AC Tidak menyala', 'high', 'completed', '2025-12-08 04:10:34', '2026-01-09 06:59:52', '\n[09 Jan 2026, 13.58] [Alfi Dwi Nugroho] - STATUS UPDATE: RAB telah diajukan (Rp 25.000.000)\n[09 Jan 2026, 13.59] [PPK] - WAITING_MANAGER_FAD: saya ok\n[09 Jan 2026, 13.59] [FAD Manager] - APPROVED_WAITING_FUND: saya lanjutkan\n[9/1/2026, 13.59.34] [Bendahara]: Menugaskan: Alfi Dwi Nugroho, A.Md. (Dana dicairkan / SPK Terbit).\n[09 Jan 2026, 13.59] [Alfi Dwi Nugroho] - COMPLETED: pekerjaan sudah selesai per hari ini'),
(2, NULL, 'Indira', 'FMD', 'Jati Room', '2025-12-08', '11:26:00', 'Proyektor mati', 'medium', 'completed', '2025-12-08 04:26:59', '2026-01-09 06:21:00', 'Menugaskan: Alfi Dwi Nugroho, A.Md. (Dana dicairkan / SPK Terbit).\n[09 Jan 2026, 13.20] [Alfi Dwi Nugroho] - COMPLETED: Pekerjaan selesai.'),
(3, NULL, 'Indira', 'FMD', 'Matoa Room', '2025-12-08', '11:34:00', 'Lampu Mati', 'low', 'completed', '2025-12-08 04:34:55', '2025-12-11 08:37:12', ''),
(4, NULL, 'Indira Destriana', 'FMD', 'Gudang', '2025-12-12', '15:00:00', 'Pintu tidak dapat dikunci', 'high', 'completed', '2025-12-12 08:01:25', '2025-12-15 08:01:31', 'Akan selesai tanggal 16/12/2025'),
(5, NULL, 'Fayas', 'FAD', 'Sensory Garden', '2025-12-22', '11:31:00', 'Lampu penerangan mati total', 'critical', 'completed', '2025-12-22 04:31:50', '2025-12-22 06:43:33', 'Pekerjaan Selesai. Diverifikasi Supervisor.'),
(8, NULL, 'Sheni', 'FMD', 'Jati Room', '2025-12-23', '10:33:00', 'AC Tidak berfungsi', 'critical', 'completed', '2025-12-23 03:33:57', '2026-01-12 02:55:09', '[RAB Disetujui - permintaan diteruskan ke FAD]\n[12 Jan 2026, 09.55] [Alfi Dwi Nugroho] - COMPLETED: sudah diselesaikan'),
(9, NULL, 'Fiar', 'FAD', 'Gudang', '2025-12-23', '10:50:00', 'Lampu penerangan mati total', 'high', 'waiting_bod', '2025-12-23 03:50:45', '2026-01-21 08:17:24', '\n[21/1/2026, 15.17.24] [System] - STATUS UPDATE: RAB Diajukan (Rp 50.050.000) - Menunggu Approval BOD (> 50 Juta)'),
(10, NULL, 'Widodo', 'FMD', 'Ruang FMD', '2026-01-07', '15:09:00', 'AC tidak berfungsi', 'high', 'approved_waiting_fund', '2026-01-07 08:09:24', '2026-01-21 08:16:36', '\n[9/1/2026, 14.50.15] [System] - STATUS UPDATE: RAB Diajukan (Rp 25.000.000) - Menunggu Approval PPK (20 - 50 Juta)\n[09 Jan 2026, 14.50] [PPK] - WAITING_MANAGER_FAD: OK\n[21 Jan 2026, 15.16] [FAD Manager] - APPROVED_WAITING_FUND: saya proseskan ke bendahara\n'),
(11, NULL, 'Alfi ', 'FMD', 'Ruang Jati', '2026-01-12', '15:10:00', 'MCB Rusak', 'high', 'in-progress', '2026-01-12 08:10:48', '2026-01-12 08:15:43', '\n[12/1/2026, 15.12.17] [System] - STATUS UPDATE: RAB Diajukan (Rp 20.000) - Menunggu Approval Manager FAD (< 20 Juta)\n[12 Jan 2026, 15.15] [FAD Manager] - APPROVED_WAITING_FUND: saya cairkan\n[12/1/2026, 15.15.43] [Bendahara]: Menugaskan: Indira Destriana Anjani. (Dana dicairkan / SPK Terbit).'),
(12, NULL, 'Alfi ', 'FMD', 'Ruang Jati', '2026-01-12', '15:10:00', 'MCB Rusak', 'high', 'waiting_manager_fad', '2026-01-12 08:10:52', '2026-01-21 08:16:17', '\n[21/1/2026, 15.16.17] [System] - STATUS UPDATE: RAB Diajukan (Rp 50.000) - Menunggu Approval Manager FAD (< 20 Juta)'),
(13, 4, 'Indira Destriana Anjani', 'FMD', 'Dormitory B kamar 1', '2026-02-03', '14:38:00', 'AC tidak dapat terhubung ke remote', 'medium', 'rejected', '2026-02-03 07:40:32', '2026-05-12 02:22:38', '\r\n[11/2/2026, 09.53.08] [System] - STATUS UPDATE: RAB Diajukan (Rp 750.000) - Verified (Menunggu Approval Manager FMD)\n[12 May 2026 09:22] [Slamet Widodo Sugiarto] - REJECTED: Mengubah status menjadi rejected'),
(14, 4, 'Indira Destriana Anjani', 'FMD', 'Dormitory B kamar 3', '2026-03-13', '09:22:00', 'drainase wastafel macet', 'low', 'pending', '2026-03-13 02:22:49', '2026-03-13 04:30:06', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `room_requests`
--

CREATE TABLE `room_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` varchar(50) NOT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_unit` varchar(100) NOT NULL,
  `date_start` date NOT NULL,
  `time_start` time NOT NULL,
  `date_end` date NOT NULL,
  `time_end` time NOT NULL,
  `purpose` longtext NOT NULL,
  `participants` int(11) NOT NULL,
  `special_needs` longtext DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `room_requests`
--

INSERT INTO `room_requests` (`id`, `user_id`, `room_id`, `applicant_name`, `applicant_unit`, `date_start`, `time_start`, `date_end`, `time_end`, `purpose`, `participants`, `special_needs`, `status`, `created_at`, `updated_at`, `note`) VALUES
(1, NULL, 'RUANG_JATI', 'Indira', 'FMD', '2025-12-08', '07:00:00', '2025-12-08', '12:00:00', 'Kunjungan Industri', 54, 'Microphone', 'completed', '2025-12-08 04:09:36', '2026-03-04 07:00:48', 'silahkan konfirmasi ke FMD atas ruangan Matoa\n[08 Jan 2026, 15.34] [Alfi Dwi Nugroho] - VERIFIED: dicarikan ruangannya\n[8/1/2026, 15.41.07] [Indira] - COMPLETED: Acara sudah selesai, confirm to security lobby\n'),
(2, NULL, 'RUANG_MATOA', 'Indira', 'FAD', '2025-12-11', '08:52:00', '2025-12-25', '08:52:00', 'Anak Magang', 8, 'Rice Cooker', 'approved', '2025-12-11 01:52:54', '2026-03-04 07:00:48', ' [Pengajuan Disetujui]'),
(3, NULL, 'GEDUNG_BUNDAR', 'Sheni', 'FMD', '2025-12-11', '09:25:00', '2025-12-11', '15:25:00', 'Gladi', 85, 'Microphone', 'completed', '2025-12-11 02:25:38', '2026-03-04 07:00:48', '[12 Jan 2026, 10.38] [Alfi Dwi Nugroho] - VERIFIED: Saya info ke manager\n[12 Jan 2026, 10.39] [Slamet Widodo Sugiarto] - APPROVED: ok, sudah saya infokan ke security untuk menyiapkan ruangannya\n[12/1/2026, 10.39.44] [Sheni] - COMPLETED: acara sudah selesai, kunci ruangan sudah dikembalikan ke security lobby'),
(4, 4, 'RUANG_JATI', 'Indira Destriana', 'FMD', '2026-01-29', '11:17:00', '2026-01-29', '12:17:00', 'awwdawd', 59, '', 'approved', '2026-01-29 04:17:32', '2026-03-13 02:17:14', '[13 Mar 2026 09:17] [Indra Septian, A.Md] - APPROVED: Ruangan: Ruang Jati (30-45 org). ok saya pindahkan ke ruangan lain karena tidak avail'),
(5, 4, 'GEDUNG_BUNDAR', 'Indira Destriana Anjani', 'FMD', '2026-03-13', '09:20:00', '2026-03-13', '12:20:00', 'Kuliah Umum Magang - Seameo Biotrop', 50, 'setting operator ', 'approved', '2026-03-13 02:21:06', '2026-03-13 02:21:44', '[13 Mar 2026 09:21] [Indra Septian, A.Md] - APPROVED: Ruangan: Gedung Bundar (75-100 org). saya pindahkan ke ruangan lain karena tidak avail'),
(6, 1, 'RUANG_JATI', 'Indira Destriana Anjani', 'FMD', '2026-04-14', '09:45:00', '2026-04-14', '12:45:00', 'untuk kegiatan IHT', 32, '', 'approved', '2026-04-14 02:39:43', '2026-05-11 09:20:53', '[11 May 2026 15:29] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Ruang Jati (30-45 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[11 May 2026 16:20] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Ruangan & Fasilitas menyiapkan permintaan dan memberikan laporan Check & Recheck.'),
(7, 1, 'RUANG_KMD', 'Indira Destriana Anjani', 'FMD', '2026-04-29', '10:35:00', '2026-04-29', '14:35:00', 'rapat FMD', 8, '', 'approved', '2026-04-29 03:35:16', '2026-04-29 03:37:31', '[29 Apr 2026 10:37] [Indra Septian, A.Md] - APPROVED: Ruangan: Ruang Rapat KMD (8-10 orang). '),
(8, 1, 'RUANG_STUDIO', 'Indira Destriana Anjani', 'FMD', '2026-05-12', '08:47:00', '2026-05-12', '14:47:00', 'syuting podcast', 5, '', 'completed', '2026-05-11 07:48:12', '2026-05-11 09:21:36', '[11 May 2026 15:21] [Lastiah, SE] - WAITING_MANAGER_FMD: Ruang Studio (10-12 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\r\n[11 May 2026 15:26] [Slamet Widodo Sugiarto] - APPROVED: ok\n[11 May 2026 16:21] [Indra Septian, A.Md] - COMPLETED: Mengubah status menjadi completed'),
(9, 1, 'RUANG_KENARI', 'Indira Destriana Anjani', 'FMD', '2026-05-12', '10:23:00', '2026-05-12', '15:24:00', 'syuting podcast ia', 5, '', 'approved', '2026-05-12 02:24:15', '2026-05-12 03:30:16', '[12 May 2026 09:54] [Indra Septian, A.Md] - WAITING_MANAGER_FMD: Ruang Kenari (10-12 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\r\n[12 May 2026 10:28] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Ruangan & Fasilitas menyiapkan permintaan dan memberikan laporan Check & Recheck.\r\n'),
(10, 1, 'RUANG_KENARI', 'Indira Destriana Anjani', 'FMD', '2026-05-18', '09:01:00', '2026-05-18', '12:01:00', 'podcast biotrop', 8, '', 'completed', '2026-05-18 02:02:07', '2026-05-21 02:51:31', '[18 May 2026 11:38] [Indra Septian, A.Md] - WAITING_MANAGER_FMD: Ruang Kenari (10-12 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[18 May 2026 11:38] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Ruangan & Fasilitas menyiapkan permintaan dan memberikan laporan Check & Recheck.\n[18 May 2026 11:39] [Indra Septian, A.Md] - READY_FOR_USER: [LAPORAN CHECK & RECHECK RUANGAN]<br>Waktu Pengecekan: 11:38<br>Status: Siap digunakan<br><br>Kebersihan: Lantai bersih, Meja bersih dan rapi, Kursi tertata rapi, Tempat sampah kosong, Tidak ada bau tidak sedap<br>Fasilitas: Proyektor berfungsi, Layar proyektor siap, TV/Monitor menyala dengan baik, Sound system berfungsi, Microphone tersedia dan berfungsi, Kabel dan konektor lengkap<br>Listrik: Stop kontak berfungsi, Lampu menyala dengan baik, AC berfungsi, WiFi tersedia dan stabil<br>Perlengkapan: Whiteboard tersedia, Spidol dan penghapus tersedia, Air minum tersedia (jika diperlukan)<br>Pengaturan: Layout sesuai permintaan, Jumlah kursi sesuai kebutuhan, Pencahayaan sesuai, Suhu ruang nyaman<br>Video: File Lagu Indonesia Raya tersedia, File Lagu SEAMEO Colours tersedia, Format file audio sesuai standar, Lokasi penyimpanan file sudah benar\n[21 May 2026 09:51] [Indra Septian, A.Md] - COMPLETED: PIC mengkonfirmasi: seluruh kebutuhan Ruangan telah terpenuhi. Permintaan selesai dilaksanakan.'),
(11, 1, 'RUANG_STUDIO', 'Indira Destriana Anjani', 'FMD', '2026-06-03', '09:30:00', '2026-06-03', '12:30:00', 'podcast video', 7, '', 'canceled', '2026-06-02 08:25:51', '2026-06-03 08:25:53', '[02 Jun 2026 15:26] [Lastiah, SE] - WAITING_MANAGER_FMD: Ruang Studio (10-12 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[02 Jun 2026 15:37] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Ruangan & Fasilitas menyiapkan permintaan dan memberikan laporan Check & Recheck.\n[02 Jun 2026 15:48] [Lastiah, SE] - READY_FOR_USER: [LAPORAN CHECK & RECHECK RUANGAN]<br>Waktu Pengecekan: 15:43<br>Status: Siap digunakan<br><br>Kebersihan: Lantai bersih, Meja bersih dan rapi, Kursi tertata rapi, Tempat sampah kosong, Tidak ada bau tidak sedap<br>Fasilitas: Proyektor berfungsi, Layar proyektor siap, TV/Monitor menyala dengan baik, Sound system berfungsi, Microphone tersedia dan berfungsi, Kabel dan konektor lengkap<br>Listrik: Stop kontak berfungsi, Lampu menyala dengan baik, AC berfungsi, WiFi tersedia dan stabil<br>Perlengkapan: Whiteboard tersedia, Spidol dan penghapus tersedia, Air minum tersedia (jika diperlukan)<br>Pengaturan: Layout sesuai permintaan, Jumlah kursi sesuai kebutuhan, Pencahayaan sesuai, Suhu ruang nyaman<br>Video: File Lagu Indonesia Raya tersedia, File Lagu SEAMEO Colours tersedia, Format file audio sesuai standar, Lokasi penyimpanan file sudah benar<br><br>Lampiran Foto:<br>- http://localhost/silatas/uploads/check_recheck/room_11_1780390110_0.png\n[03 Jun 2026 15:25] [Lastiah, SE] - CANCELED: Mengubah status menjadi canceled'),
(13, 1, 'RUANG_JATI', 'Indira Destriana Anjani', 'FMD', '2026-06-04', '09:30:00', '2026-06-04', '12:30:00', 'showcase magang', 25, '', 'canceled', '2026-06-03 08:27:19', '2026-06-03 08:32:20', '[03 Jun 2026 15:31] [Lastiah, SE] - WAITING_MANAGER_FMD: Ruangan: Ruang Jati (30-45 org). oke ada\n[03 Jun 2026 15:32] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Ruangan & Fasilitas menyiapkan permintaan dan memberikan laporan Check & Recheck.\n[03 Jun 2026 15:32] [Lastiah, SE] - CANCELED: Mengubah status menjadi canceled');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','user','supervisor') NOT NULL DEFAULT 'user',
  `telegram_chat_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `callmebot_apikey` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `employee_id`, `password`, `full_name`, `role`, `telegram_chat_id`, `created_at`, `whatsapp_number`, `callmebot_apikey`) VALUES
(1, 63, 'seabiotrop68', 'Indira Destriana Anjani', 'user', '6144952473', '2026-04-14 02:36:34', '081324708264', ''),
(2, 64, 'seabiotrop68', 'Sheni Olvianda', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(3, 66, 'seabiotrop68', 'Slamet Widodo Sugiarto', 'admin', '', '2026-04-14 02:36:34', '081388958859', NULL),
(4, 111, 'seabiotrop68', 'AGUS', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(5, 112, 'seabiotrop68', 'AHMAD SUDRAJAT', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(6, 114, 'seabiotrop68', 'ANDI SUHANDI', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(7, 115, 'seabiotrop68', 'ANGGI PURNAMA', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(8, 116, 'seabiotrop68', 'AULIA RIZKY ANANDA', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(9, 117, 'seabiotrop68', 'DADANG SETIAWAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(10, 118, 'seabiotrop68', 'DEDE MAULANA', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(11, 119, 'seabiotrop68', 'DIDIT TRISNADI', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(12, 120, 'seabiotrop68', 'ENDANG SETIAWAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(13, 121, 'seabiotrop68', 'ENDIH', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(14, 122, 'seabiotrop68', 'ERWAN PRAYOGI', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(15, 123, 'seabiotrop68', 'HASAN HUSEN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(16, 124, 'seabiotrop68', 'HERDI FERDIANSYAH MAULANA', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(17, 125, 'seabiotrop68', 'HERMAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(18, 126, 'seabiotrop68', 'HERU', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(19, 127, 'seabiotrop68', 'ICHWAN PIDIN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(20, 128, 'seabiotrop68', 'IDA HERMAWAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(21, 129, 'seabiotrop68', 'IDIM DIMYATI', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(22, 130, 'seabiotrop68', 'INDRA ARDIANSYAH', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(23, 131, 'seabiotrop68', 'ISNANDAR', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(24, 132, 'seabiotrop68', 'KHAMELANDI ASTRIAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(25, 133, 'seabiotrop68', 'KOMARUDIN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(26, 134, 'seabiotrop68', 'KURNIAWAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(27, 135, 'seabiotrop68', 'KUSNADI', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(28, 136, 'seabiotrop68', 'LINGGAR', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(29, 137, 'seabiotrop68', 'M. ABUDIN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(30, 138, 'seabiotrop68', 'MAULANA HASANUDIN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(31, 139, 'seabiotrop68', 'MOH.WAHYU NOORRAMDHANY Y.', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(32, 140, 'seabiotrop68', 'MUHAMAD ZAENUDDIN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(33, 141, 'seabiotrop68', 'MUHAMMAD NAZRIL ILHAM', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(34, 142, 'seabiotrop68', 'MULYADI', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(35, 143, 'seabiotrop68', 'MULYANA', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(36, 144, 'seabiotrop68', 'RIDWAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(37, 145, 'seabiotrop68', 'ROHMAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(38, 146, 'seabiotrop68', 'RUDI HARTONO', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(39, 147, 'seabiotrop68', 'SARIPUDIN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(40, 148, 'seabiotrop68', 'SUGIH MAULANA', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(41, 149, 'seabiotrop68', 'TAOFIK ROKAYAT', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(42, 150, 'seabiotrop68', 'TASORI HERIYANTO', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(43, 151, 'seabiotrop68', 'WAHYUDI', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(44, 152, 'seabiotrop68', 'WALDI HIDAYAT', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(45, 153, 'seabiotrop68', 'YAYA TARYANA', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(46, 154, 'seabiotrop68', 'ZULPA HILMAWAN', 'user', '', '2026-04-14 02:36:34', NULL, NULL),
(47, 155, 'seabiotrop68', 'Administrator', 'admin', '', '2026-04-14 02:36:34', NULL, NULL),
(48, 156, 'seabiotrop68', 'Agus Sujadi', 'admin', NULL, '2026-04-14 02:36:34', NULL, NULL),
(49, 157, 'seabiotrop68', 'Alfi Dwi Nugroho, A.Md', 'admin', '1597712640', '2026-04-14 02:36:34', '081219974021', NULL),
(50, 158, 'seabiotrop68', 'Bahrudin', 'admin', NULL, '2026-04-14 02:36:34', NULL, NULL),
(51, 159, 'seabiotrop68', 'Indra Septian, A.Md', 'admin', NULL, '2026-04-14 02:36:34', NULL, NULL),
(52, 160, 'seabiotrop68', 'Lastiah, SE', 'admin', '', '2026-04-14 02:36:34', '081324708264', NULL),
(53, 163, 'seabiotrop68', 'Prof. Dr. Edi Santosa, S.P., M.Si', 'admin', '', '2026-04-14 02:36:34', NULL, NULL),
(54, 164, 'seabiotrop68', 'Dr. Elis Rosdiawati, M.Pd', 'admin', NULL, '2026-04-14 02:36:34', NULL, NULL),
(55, 165, 'seabiotrop68', 'Dr.rer.nat. Doni Yusri, SP., MM', 'admin', NULL, '2026-04-14 02:36:34', NULL, NULL),
(56, 166, 'seabiotrop68', 'Peri Siantuni, SE', 'user', NULL, '2026-04-14 02:36:34', NULL, NULL),
(57, 167, 'seabiotrop68', 'Mulyadiana Prayoga', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(58, 168, 'seabiotrop68', 'Supriyatno, A.Md', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(59, 169, 'seabiotrop68', 'Sunardi Ikay', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(60, 170, 'seabiotrop68', 'Herni Widhiastuti, S.Si', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(61, 171, 'seabiotrop68', 'Dewi Rahmawati, M.Si', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(62, 172, 'seabiotrop68', 'Nopi Ramli', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(63, 173, 'seabiotrop68', 'Haritz Cahya Nugraha, M.T', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(64, 174, 'seabiotrop68', 'Aan Darwati, S.Ak', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(65, 175, 'seabiotrop68', 'Aris Purnajaya', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(66, 176, 'seabiotrop68', 'Asep Saepudin', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(67, 177, 'seabiotrop68', 'Asep Syaefudin, SE', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(68, 178, 'seabiotrop68', 'Dani Yudi Trisna, A.Md', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(69, 179, 'seabiotrop68', 'Dewi Suryani Oktavia Basuki, SP., MM', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(70, 180, 'seabiotrop68', 'Didi Junaedi, A.Md', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(71, 181, 'seabiotrop68', 'Fitri Junaedy, SEI', 'user', '1597712640', '2026-04-14 02:36:35', NULL, NULL),
(72, 182, 'seabiotrop68', 'Hery Yanto', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(73, 183, 'seabiotrop68', 'Iman', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(74, 184, 'seabiotrop68', 'Irawan', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(75, 185, 'seabiotrop68', 'Lidia Defita, S.Kom', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(76, 186, 'seabiotrop68', 'Lillys Betty Yuliawati, S.Si', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(77, 187, 'seabiotrop68', 'Risa Rosita, M.Si', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(78, 188, 'seabiotrop68', 'Risya Ayu Astari, A.Md', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(79, 189, 'seabiotrop68', 'Rizkia Tirtani', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(80, 190, 'seabiotrop68', 'Rosadi, S.Pd.I', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(81, 191, 'seabiotrop68', 'Saiful Bachri, M.Si', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(82, 193, 'seabiotrop68', 'Tenni Wahyuni, S.I.Kom', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(83, 194, 'seabiotrop68', 'Trijanti A. Widinni Asnan, M.Si.', 'admin', NULL, '2026-04-14 02:36:35', NULL, NULL),
(84, 195, 'seabiotrop68', 'Woro Kanti Dharmastuti, M.Si', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(85, 196, 'seabiotrop68', 'Yana', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(86, 197, 'seabiotrop68', 'Zaenal Abidin', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(87, 198, 'seabiotrop68', 'Zulkarnaen Noor Syarif, M.Kom', 'user', NULL, '2026-04-14 02:36:35', NULL, NULL),
(88, 155, 'seabiotrop68', 'Administrator Utama', 'admin', '', '2026-04-14 02:36:35', NULL, NULL),
(89, NULL, 'seabiotrop68', 'Supervisor Lapangan', 'supervisor', '', '2026-04-14 02:36:35', NULL, NULL),
(90, NULL, 'seabiotrop68', 'Staff Umum', 'user', '', '2026-04-14 02:36:35', NULL, NULL),
(91, NULL, 'seabiotrop68', 'Achmad Fayad', 'user', '', '2026-04-14 02:36:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `vehicle_requests`
--

CREATE TABLE `vehicle_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vehicle_id` varchar(255) DEFAULT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_unit` varchar(100) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `purpose` longtext NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vehicle_requests`
--

INSERT INTO `vehicle_requests` (`id`, `user_id`, `vehicle_id`, `applicant_name`, `applicant_unit`, `date_start`, `date_end`, `purpose`, `status`, `created_at`, `updated_at`, `note`, `driver_name`, `time_start`, `time_end`) VALUES
(3, NULL, 'INV002', 'Indira', 'FMD', '2025-12-08', '2025-12-09', 'ComVis', 'completed', '2025-12-08 03:38:06', '2025-12-11 07:31:37', NULL, NULL, '00:00:00', '00:00:00'),
(4, NULL, 'AVZ001', 'Fiar', 'FAD', '2025-12-16', '2025-12-18', 'Jogja', 'in-progress', '2025-12-11 01:57:43', '2026-01-12 03:40:54', '[Approved by Slamet Widodo Sugiarto] [Verified by Alfi Dwi Nugroho] Siap tersedia. \nOK\n[12/1/2026, 10.40.54] [Alfi Dwi Nugroho]: Menugaskan: Indira Destriana Anjani. (Dana dicairkan / SPK Terbit).', NULL, '00:00:00', '00:00:00'),
(5, NULL, 'INV002', 'Indira', 'FMD', '2025-12-16', '2025-12-18', 'Jogja', 'rejected', '2025-12-11 02:23:31', '2025-12-11 06:38:53', NULL, NULL, '00:00:00', '00:00:00'),
(6, NULL, 'HLX003', 'Sheni', 'FMD', '2025-12-11', '2025-12-12', 'Coba', 'verified', '2025-12-11 02:25:59', '2025-12-31 03:56:15', '[31 Des, 10.56] Pekerja Dipilih: Alfi Dwi Nugroho, A.Md\n[31 Des, 10.56] Diverifikasi Admin. Menunggu penunjukan pengemudi (Hub. Mas Alfi).', NULL, '00:00:00', '00:00:00'),
(7, NULL, 'HLX003', 'Achmad Fayad', 'HCID', '2026-01-08', '2026-01-10', 'Klien Bandung', 'completed', '2026-01-08 08:02:04', '2026-01-08 08:33:32', '[08 Jan 2026, 15.26] [Alfi Dwi Nugroho] - VERIFIED: Mobil avail\n[08 Jan 2026, 15.27] [Slamet Widodo Sugiarto] - APPROVED: wellnoted\n[8/1/2026, 15.33.32] [Achmad Fayad] - COMPLETED: Sudah saya kembalikan', NULL, '00:00:00', '00:00:00'),
(8, NULL, 'INV002', 'Indira Destriana', 'HCID', '2026-01-09', '2026-01-10', 'company visit', 'completed', '2026-01-08 09:05:39', '2026-01-08 09:07:02', '[08 Jan 2026, 16.06] [Alfi Dwi Nugroho] - VERIFIED: Mobil avail\n[08 Jan 2026, 16.06] [Slamet Widodo Sugiarto] - APPROVED: ok\n[8/1/2026, 16.07.02] [Indira Destriana] - COMPLETED: Sudah dikembalikan', NULL, '00:00:00', '00:00:00'),
(9, NULL, 'INV002', 'Azmi Raihan', 'FAD', '2025-12-22', '2025-12-23', 'Meeting client luar kota', 'completed', '2026-01-12 02:02:51', '2026-01-12 02:45:56', '[12 Jan 2026, 09.44] [Alfi Dwi Nugroho] - VERIFIED: saya teruskan ke manager\n[12 Jan 2026, 09.45] [Slamet Widodo Sugiarto] - APPROVED: ok, wellnoted\n[12/1/2026, 09.45.56] [Azmi Raihan] - COMPLETED: Mobil sudah dikembalikan, ac cukup panas saran untuk diperbaiki', NULL, '00:00:00', '00:00:00'),
(10, NULL, 'INV002', 'Indira Destriana Anjani', 'FMD', '2026-02-02', '2026-02-03', 'Outlook Kemdikbum', 'approved', '2026-01-29 02:07:13', '2026-02-25 02:27:06', '[25 Feb 2026, 09.27] [Alfi Dwi Nugroho, A.Md] - APPROVED: Vehicle: Innova - B 8899 EF. Driver: Didit Trisnadi (Pengemudi). ok, tunggu di lobby', 'Didit Trisnadi (Pengemudi)', '00:00:00', '00:00:00'),
(11, 4, 'AVZ001', 'Indira Destriana', 'FMD', '2026-01-29', '2026-01-29', 'dawdada', 'approved', '2026-01-29 04:28:07', '2026-02-25 02:25:57', '[24 Feb 2026 13:56] [Alfi Dwi Nugroho, A.Md] - RESCHEDULED: Mengubah status menjadi rescheduled\n[25 Feb 2026 09:25] [Alfi Dwi Nugroho, A.Md] - APPROVED: Vehicle: Avanza - B 1234 CD. Driver: Didit Trisnadi (Pengemudi). saya approve, mobil tersedia, tunggu di lobby', 'Didit Trisnadi (Pengemudi)', '00:00:00', '00:00:00'),
(12, 4, 'AVZ001', 'Indira Destriana', 'FMD', '2026-02-03', '2026-02-03', 'test', 'completed', '2026-02-03 05:07:42', '2026-03-11 03:37:22', '[09 Feb 2026, 09.45] [Alfi Dwi Nugroho, A.Md] - APPROVED: Driver Set: Didit Trisnadi (Pengemudi). Ok mobil avail, silahkan tunggu di lobby\n[11 Mar 2026 10:37] [Indira Destriana Anjani] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.', NULL, '00:00:00', '00:00:00'),
(13, 4, 'INV002', 'Sheni Olvianda', 'FMD', '2026-02-03', '2026-02-03', 'Outlook tahunan', 'completed', '2026-02-03 05:09:31', '2026-02-03 05:15:53', '[03 Feb 2026, 12.12] [Alfi Dwi Nugroho] - VERIFIED: Driver Set: Didit Trisnadi (Pengemudi). Saya lanjutkan ke Manager\n[03 Feb 2026, 12.14] [Slamet Widodo Sugiarto] - APPROVED: ok wellnoted\n[3/2/2026, 12.15.52] [Sheni Olvianda] - COMPLETED: Selama perjalanan pak didit murung', NULL, '00:00:00', '00:00:00'),
(14, 133, 'HLX003', 'Fitri Junaedy, SEI', 'HRAD', '2026-02-10', '2026-02-10', 'Coba kakak', 'approved', '2026-02-09 07:36:33', '2026-02-10 07:21:22', '[10 Feb 2026, 14.21] [Alfi Dwi Nugroho, A.Md] - APPROVED: Vehicle: Hilux - F 2222 GH. Driver: Erwan Prayogi (Pengemudi). iya', 'Erwan Prayogi (Pengemudi)', '14:00:00', '16:00:00'),
(15, 155, 'INV002', 'Alya Shafira, S.T', 'SITD', '2026-02-10', '2026-02-10', 'coba juga', 'approved', '2026-02-10 06:36:53', '2026-02-10 06:40:34', '[10 Feb 2026, 13.40] [Alfi Dwi Nugroho, A.Md] - APPROVED: Vehicle: Innova - B 8899 EF. Driver: Didit Trisnadi (Pengemudi). ', 'Didit Trisnadi (Pengemudi)', '14:00:00', '16:00:00'),
(16, 126, 'AVZ001', 'Aris Purnajaya', 'SITD', '2026-03-03', '2026-03-03', 'perjalanan mengantar DDA', 'approved', '2026-03-03 02:43:44', '2026-03-04 07:52:55', '[04 Mar 2026 14:52] [Alfi Dwi Nugroho, A.Md] - APPROVED: Vehicle: Panther - F 1895 A. Driver: Didit Trisnadi (Pengemudi). ok silahkan menunggu dilobby dijadwal yang dipilih.', 'Didit Trisnadi (Pengemudi)', '09:43:00', '15:43:00'),
(18, 133, 'AVZ001', 'Fitri Junaedy, SEI', 'HRAD', '2026-03-12', '2026-03-12', 'Membeli keperluan halal bihalal', 'ready_for_user', '2026-03-12 02:27:49', '2026-06-04 04:30:54', '[12 Mar 2026 09:29] [Alfi Dwi Nugroho, A.Md] - APPROVED: Vehicle: Panther - F 1895 A. Driver: Didit Trisnadi (Pengemudi). Ok\n[04 Jun 2026 11:30] [Alfi Dwi Nugroho, A.Md] - READY_FOR_USER: PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Kendaraan telah siap untuk diserahkan/dilaksanakan.', 'Didit Trisnadi (Pengemudi)', '09:30:00', '12:30:00'),
(19, 1, 'AVZ001', 'Indira Destriana Anjani', 'FMD', '2026-05-18', '2026-05-18', 'survei IPB ICC', 'completed', '2026-05-18 02:09:21', '2026-05-21 02:38:46', '[18 May 2026 14:55] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Panther - F 1895 A tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: DIDIT TRISNADI (DRIVER)\n[18 May 2026 14:55] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Kendaraan (Unit & Driver) menyiapkan permintaan dan memberikan laporan Check & Recheck.\n[18 May 2026 14:56] [Alfi Dwi Nugroho, A.Md] - READY_FOR_USER: PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Kendaraan telah siap untuk diserahkan/dilaksanakan.\n[21 May 2026 09:38] [Indira Destriana Anjani] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.', 'DIDIT TRISNADI (DRIVER)', '12:09:00', '15:09:00'),
(20, 1, 'PENDING_ASSIGNMENT', 'Indira Destriana Anjani', 'FMD', '2026-06-01', '2026-05-25', 'diperlukan untuk survei IPB ICC', 'pending', '2026-05-25 02:09:34', '2026-05-25 02:09:34', NULL, NULL, '09:09:00', '12:09:00'),
(21, 1, 'AVZ001', 'Indira Destriana Anjani', 'FMD', '2026-05-25', '2026-05-25', 'diperlukan untuk survei IPB ICC', 'waiting_manager_fmd', '2026-05-25 02:11:00', '2026-06-04 06:25:33', '[04 Jun 2026 13:25] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Panther - F 1895 A tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: DIDIT TRISNADI (DRIVER)', 'DIDIT TRISNADI (DRIVER)', '09:10:00', '12:10:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `zoom_requests`
--

CREATE TABLE `zoom_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `zoom_account_id` varchar(50) NOT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_unit` varchar(100) NOT NULL,
  `date_start` date NOT NULL,
  `time_start` time NOT NULL,
  `date_end` date NOT NULL,
  `time_end` time NOT NULL,
  `purpose` longtext NOT NULL,
  `participants` int(11) NOT NULL,
  `request_type` varchar(50) DEFAULT NULL,
  `special_needs` longtext DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `zoom_requests`
--

INSERT INTO `zoom_requests` (`id`, `user_id`, `zoom_account_id`, `applicant_name`, `applicant_unit`, `date_start`, `time_start`, `date_end`, `time_end`, `purpose`, `participants`, `request_type`, `special_needs`, `status`, `created_at`, `updated_at`, `note`) VALUES
(7, NULL, 'zoom_01', 'Indira Destriana', 'FMD', '2026-01-29', '10:53:00', '2026-01-29', '11:53:00', 'awwdafdada', 259, 'Tidak Rekam', '', 'approved', '2026-01-29 03:53:24', '2026-03-04 02:04:00', '[11 Feb 2026, 09.17] [Indra Septian, A.Md] - APPROVED: Zoom Info: afierinfseufpse. ok sudah bisa'),
(8, NULL, 'zoom_01', 'Indira Destriana', 'FMD', '2026-01-29', '11:12:00', '2026-01-29', '12:13:00', 'dwafewf', 99, 'Tidak Rekam', '', 'approved', '2026-01-29 04:13:05', '2026-03-04 02:04:00', '[13 Feb 2026, 16.37] [Indra Septian, A.Md] - APPROVED: Zoom Info: hehfnsuudidgdsf. ok'),
(9, 3, 'zoom_01', 'Sheni', 'FAD', '2026-01-29', '11:28:00', '2026-01-29', '15:28:00', 'dwda', 164, 'Tidak Rekam', '', 'approved', '2026-01-29 04:28:46', '2026-03-04 02:04:00', '[26 Feb 2026 11:03] [Indra Septian, A.Md] - APPROVED: Zoom Info: fesjbhfuiaeodnoa. oke mantap gesss bisa bisa '),
(13, 158, 'zoom_01', 'Putri Dina Rahayu, SE', 'HRAD', '2026-02-11', '10:58:00', '2026-02-11', '10:58:00', 'rapat', 85, 'Tidak Rekam', '', 'approved', '2026-02-11 03:58:40', '2026-03-04 02:04:00', '[26 Feb 2026 11:04] [Indra Septian, A.Md] - APPROVED: Zoom Info: dafwafasdfaw. affrg asafetgvsag'),
(14, 158, 'zoom_01', 'Putri Dina Rahayu, SE', 'HRAD', '2026-02-11', '11:32:00', '2026-02-11', '12:32:00', 'rapat', 88, 'Tidak Rekam', '', 'completed', '2026-02-11 04:33:05', '2026-03-04 02:04:00', '[25 Feb 2026, 09.28] [Indra Septian, A.Md] - APPROVED: Zoom Info: gfdhegwakudahwouhdago. sedang diproses, mohon tunggu.\n[25/2/2026, 09.29.46] [Putri Dina Rahayu, SE] - COMPLETED: ok, tidak ada kendala '),
(15, 126, 'zoom_01', 'Aris Purnajaya', 'SITD', '2026-03-03', '09:42:00', '2026-03-03', '11:42:00', '0', 1, 'Rapat BLM', 'Tidak Rekam', 'pending', '2026-03-03 02:42:55', '2026-03-04 02:04:00', NULL),
(16, 122, 'zoom_01', 'Nopi Ramli', 'FAD', '2026-03-04', '09:30:00', '2026-03-04', '12:30:00', '0', 99, 'Rapat Keuangan Tukin 3 Bulan', 'Tidak Rekam', 'approved', '2026-03-04 05:27:17', '2026-03-04 07:57:32', '[04 Mar 2026 14:57] [Indra Septian, A.Md] - APPROVED: Zoom Info: awkhdwabwbdbaifboehofa. silahkan akses link dibawah'),
(17, 133, 'zoom_01', 'Fitri Junaedy, SEI', 'HRAD', '2026-03-12', '08:00:00', '2026-03-12', '11:00:00', '0', 1, 'Rapat internal HRAD dan DDA', 'Tidak Rekam', 'pending', '2026-03-12 02:00:01', '2026-03-12 02:00:01', NULL),
(18, 133, '', 'Fitri Junaedy, SEI', 'HRAD', '2026-03-12', '09:45:00', '2026-03-12', '11:45:00', '0', 20, 'Rapat dengan DDA', '', 'pending', '2026-03-12 02:43:59', '2026-03-12 02:43:59', NULL),
(19, 133, 'zoom_01', 'Fitri Junaedy, SEI', 'HRAD', '2026-03-12', '09:45:00', '2026-03-12', '11:45:00', '0', 90, 'Rapat Triwulan', '', 'approved', '2026-03-12 02:45:17', '2026-05-11 09:20:04', '[11 May 2026 15:41] [Indra Septian, A.Md] - WAITING_MANAGER_FMD: zoom_01 tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[11 May 2026 16:20] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Akun Zoom/Link menyiapkan permintaan dan memberikan laporan Check & Recheck.'),
(20, 133, 'zoom_01', 'Fitri Junaedy, SEI', 'HRAD', '2026-03-12', '13:31:00', '2026-03-12', '16:31:00', '0', 52, 'Rapat Triwulan', '', 'approved', '2026-03-12 06:31:40', '2026-03-12 06:32:59', '[12 Mar 2026 13:32] [Indra Septian, A.Md] - APPROVED: Zoom Info: gudiagiwchafoieafa. ok'),
(21, 1, 'zoom_01', 'Indira Destriana Anjani', 'FMD', '2026-05-18', '09:03:00', '2026-05-18', '12:03:00', '0', 8, 'Wawancara narsum podcast', 'Live Youtube', 'completed', '2026-05-18 02:03:47', '2026-05-21 02:50:51', '[18 May 2026 14:52] [Indra Septian, A.Md] - WAITING_MANAGER_FMD: Zoom Info: 📌 Zoom Link : https://zoom.us/j/96533279699 📌 Meeting ID: 965 3327 9699. zoom_01 tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[18 May 2026 14:53] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Akun Zoom/Link menyiapkan permintaan dan memberikan laporan Check & Recheck.\n[18 May 2026 14:53] [Indra Septian, A.Md] - READY_FOR_USER: PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Zoom/Virtual telah siap untuk diserahkan/dilaksanakan.\n[21 May 2026 09:50] [Indira Destriana Anjani] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(22, 1, 'zoom_02', 'Indira Destriana Anjani', 'FMD', '2026-06-04', '12:36:00', '2026-06-04', '18:36:00', '0', 450, 'ICTB 5th 2026', 'Live Youtube', 'approved', '2026-06-04 05:37:06', '2026-06-04 05:38:37', '[04 Jun 2026 12:37] [Indra Septian, A.Md] - WAITING_MANAGER_FMD: zoom_02 tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[04 Jun 2026 12:38] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Akun Zoom/Link menyiapkan permintaan dan memberikan laporan Check & Recheck.');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip_nik` (`nip_nik`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_position` (`position`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indeks untuk tabel `item_loan_requests`
--
ALTER TABLE `item_loan_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_item_loan_requests_user_id` (`user_id`);

--
-- Indeks untuk tabel `master_rooms`
--
ALTER TABLE `master_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_vehicles`
--
ALTER TABLE `master_vehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_req` (`request_id`,`request_type`),
  ADD KEY `idx_resource` (`resource_id`,`resource_type`);

--
-- Indeks untuk tabel `repair_budgets`
--
ALTER TABLE `repair_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_request_id` (`repair_request_id`);

--
-- Indeks untuk tabel `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_repair_requests_user_id` (`user_id`);

--
-- Indeks untuk tabel `room_requests`
--
ALTER TABLE `room_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_room_requests_user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indeks untuk tabel `vehicle_requests`
--
ALTER TABLE `vehicle_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_vehicle_requests_user_id` (`user_id`);

--
-- Indeks untuk tabel `zoom_requests`
--
ALTER TABLE `zoom_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_zoom_requests_user_id` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT untuk tabel `item_loan_requests`
--
ALTER TABLE `item_loan_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `repair_budgets`
--
ALTER TABLE `repair_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `repair_requests`
--
ALTER TABLE `repair_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `room_requests`
--
ALTER TABLE `room_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT untuk tabel `vehicle_requests`
--
ALTER TABLE `vehicle_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `zoom_requests`
--
ALTER TABLE `zoom_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `repair_budgets`
--
ALTER TABLE `repair_budgets`
  ADD CONSTRAINT `repair_budgets_ibfk_1` FOREIGN KEY (`repair_request_id`) REFERENCES `repair_requests` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD CONSTRAINT `fk_repair_requests_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `room_requests`
--
ALTER TABLE `room_requests`
  ADD CONSTRAINT `fk_room_requests_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
