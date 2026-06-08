-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql100.infinityfree.com
-- Generation Time: Jun 07, 2026 at 09:38 PM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40693730_silatas`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `nip_nik` varchar(50) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `role_title` varchar(50) DEFAULT NULL,
  `position` varchar(150) DEFAULT NULL,
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
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `nip_nik`, `full_name`, `department`, `role_title`, `position`, `education`, `tenure`, `certificates`, `employee_type`, `is_active`, `created_at`, `type`, `updated_at`) VALUES
(63, '123456', 'Indira Destriana Anjani', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-01-25 19:08:34', 'outsourcing', '2026-04-14 02:33:07'),
(64, '111111', 'Sheni Olvianda', 'FMD', 'Internship', NULL, NULL, NULL, NULL, 'PNS', 1, '2026-02-02 22:34:19', 'outsourcing', '2026-04-14 02:33:07'),
(66, '197707072025211067', 'Slamet Widodo Sugiarto', 'FMD', 'Manager', '', NULL, NULL, NULL, 'PNS', 1, '2026-02-18 19:20:21', 'outsourcing', '2026-04-14 02:33:07'),
(111, '16267400035', 'AGUS', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:07'),
(112, '16269400052', 'AHMAD SUDRAJAT', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:07'),
(114, '16268800019', 'ANDI SUHANDI', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(115, '16260400054', 'ANGGI PURNAMA', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(116, '16260400053', 'AULIA RIZKY ANANDA', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(117, '16267900036', 'DADANG SETIAWAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(118, '16268000018', 'DEDE MAULANA', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(119, '16267800056', 'DIDIT TRISNADI', 'FMD', 'Staff', 'DRIVER', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(120, '16267900038', 'ENDANG SETIAWAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(121, '16266700037', 'ENDIH', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(122, '16269200058', 'ERWAN PRAYOGI', 'FMD', 'Staff', 'DRIVER', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(123, '16267600032', 'HASAN HUSEN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(124, '16268900039', 'HERDI FERDIANSYAH MAULANA', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(125, '16268600040', 'HERMAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(126, '16268000041', 'HERU', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(127, '16267600022', 'ICHWAN PIDIN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(128, '16268000042', 'IDA HERMAWAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(129, '16267000050', 'IDIM DIMYATI', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(130, '16268200024', 'INDRA ARDIANSYAH', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(131, '16268700030', 'ISNANDAR', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(132, '16268600017', 'KHAMELANDI ASTRIAN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(133, '16268500020', 'KOMARUDIN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(134, '16268200025', 'KURNIAWAN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(135, '16267600023', 'KUSNADI', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(136, '16269000031', 'LINGGAR', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(137, '16267800043', 'M. ABUDIN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(138, '16268300026', 'MAULANA HASANUDIN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(139, '16268300055', 'MOH.WAHYU NOORRAMDHANY Y.', 'FMD', 'Staff', 'SUPERVISOR PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(140, '16267100051', 'MUHAMAD ZAENUDDIN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(141, '16260500034', 'MUHAMMAD NAZRIL ILHAM', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(142, '16267800044', 'MULYADI', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(143, '16266800045', 'MULYANA', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(144, '16268500046', 'RIDWAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(145, '16267200047', 'ROHMAN', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(146, '16268300028', 'RUDI HARTONO', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(147, '16268000027', 'SARIPUDIN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(148, '16260400016', 'SUGIH MAULANA', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(149, '16267200057', 'TAOFIK ROKAYAT', 'FMD', 'Staff', 'DRIVER', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(150, '16267500021', 'TASORI HERIYANTO', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(151, '16267700048', 'WAHYUDI', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(152, '16269000049', 'WALDI HIDAYAT', 'FMD', 'Staff', 'PRAMUBAKTI', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(153, '16267800033', 'YAYA TARYANA', 'FMD', 'Staff', 'CHIEFSECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(154, '16269000029', 'ZULPA HILMAWAN', 'FMD', 'Staff', 'SECURITY', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 18:44:42', 'outsourcing', '2026-04-14 02:33:08'),
(155, 'admin', 'Administrator', 'IT', 'Super Admin', 'System Administrator', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:11:16', 'outsourcing', '2026-04-14 02:33:08'),
(156, '197212162014091003', 'Agus Sujadi', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(157, '198605082025211053', 'Alfi Dwi Nugroho, A.Md', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(158, '197009192025211011', 'Bahrudin', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(159, '198902222025211044', 'Indra Septian, A.Md', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(160, '199008092025212052', 'Lastiah, SE', 'FMD', 'Staff', 'Staff', NULL, NULL, NULL, 'PNS', 1, '2026-02-19 19:34:39', 'outsourcing', '2026-04-14 02:33:08'),
(163, '197005201996011000', 'Prof. Dr. Edi Santosa, S.P., M.Si', 'BoD', 'Direktur', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(164, '196905151992032009', 'Dr. Elis Rosdiawati, M.Pd', 'BOD', 'Deputi Direktur bidang Administrasi', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(165, 'TEMP_NIP_165', 'Dr.rer.nat. Doni Yusri, SP., MM', 'BoD', 'Deputi Direktur bidang Program', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(166, '198212062008101002', 'Peri Siantuni, SE', 'FAD', 'BPP', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(167, '198108072009101001', 'Mulyadiana Prayoga', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(168, '196902262014091001', 'Supriyatno, A.Md', 'FAD', 'Staf FAD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(169, '197007202007011002', 'Sunardi Ikay', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(170, '197708072014092001', 'Herni Widhiastuti, S.Si', 'Pengadaan', 'Pejabat Pengadaan', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(171, '197810122014092003', 'Dewi Rahmawati, M.Si', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(172, '198011052014092003', 'Nopi Ramli', 'HRAD', 'Staf HRAD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(173, '198912142015041002', 'Haritz Cahya Nugraha, M.T', 'KMD', 'Manajer KMD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(174, '198407032025212025', 'Aan Darwati, S.Ak', 'FAD', 'Staf FAD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(175, '198212142025211037', 'Aris Purnajaya', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(176, '198212182025211036', 'Asep Saepudin', 'KMD', 'Staf KMD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(177, '197212232025211010', 'Asep Syaefudin, SE', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(178, '198110072025211038', 'Dani Yudi Trisna, A.Md', 'KMD', 'Staf KMD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(179, '198210282025212041', 'Dewi Suryani Oktavia Basuki, SP., MM', 'HCID', 'Manajer HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(180, '198305182025211032', 'Didi Junaedi, A.Md', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(181, '198506302025211042', 'Fitri Junaedy, SEI', 'HRAD', 'Staf HRAD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(182, '198312212025211029', 'Hery Yanto', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(183, '197007272025211013', 'Iman', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(184, '198206272025211034', 'Irawan', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(185, '197712122025212024', 'Lidia Defita, S.Kom', 'HRAD', 'Staf HRAD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(186, '198007062025212028', 'Lillys Betty Yuliawati, S.Si', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(187, '198507172025212047', 'Risa Rosita, M.Si', 'SITD', 'Manajer  SITD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(188, '198703282025212040', 'Risya Ayu Astari, A.Md', 'SITD', 'Staf SITD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(189, '198912112025212050', 'Rizkia Tirtani', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(190, '197910152025211032', 'Rosadi, S.Pd.I', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(191, '198811072025211049', 'Saiful Bachri, M.Si', 'HCID', 'Supervisor HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(193, '198003182025212017', 'Tenni Wahyuni, S.I.Kom', 'HRAD', 'Manajer HRAD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(194, '199201142025212043', 'Trijanti A. Widinni Asnan, M.Si.', 'FAD', 'Manajer FAD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(195, '198109012025212021', 'Woro Kanti Dharmastuti, M.Si', 'KMD', 'Staf KMD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(196, '198605132025211031', 'Yana', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(197, '197201042025211008', 'Zaenal Abidin', 'HCID', 'Staf HCID', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08'),
(198, '198212042025211036', 'Zulkarnaen Noor Syarif, M.Kom', 'SITD', 'Supervisor SITD', '', NULL, NULL, NULL, 'PNS', 1, '2026-04-08 23:49:25', 'outsourcing', '2026-04-14 02:33:08');

-- --------------------------------------------------------

--
-- Table structure for table `item_loan_requests`
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
  `status` enum('pending','verified','approved','rejected','completed','in-progress','returned','waiting_manager_fmd','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','pic_checking','ready_for_user') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_loan_requests`
--

INSERT INTO `item_loan_requests` (`id`, `user_id`, `applicant_name`, `applicant_unit`, `item_name`, `item_quantity`, `loan_date`, `loan_time`, `return_date`, `return_time`, `purpose`, `status`, `created_at`, `updated_at`, `note`) VALUES
(7, 136, 'Indra Septian, A.Md', 'FMD', 'Drone', 1, '2026-02-09', NULL, '2026-02-09', NULL, 'Mapping Cluster', 'approved', '2026-02-09 06:18:04', '2026-03-02 04:11:40', '[02 Mar 2026 11:11] [Indra Septian, A.Md] - APPROVED: ok'),
(8, 134, 'Hery Yanto', 'HCID', 'DJI', 1, '2026-02-10', NULL, '2026-02-10', NULL, 'Dokumentasi', 'pending', '2026-02-10 07:42:25', '2026-02-10 07:42:25', NULL),
(10, 158, 'Putri Dina Rahayu, SE', 'HRAD', 'Drone', 1, '2026-02-11', NULL, '2026-02-11', NULL, 'Mapping Cluster', 'pending', '2026-02-11 04:34:40', '2026-02-11 04:34:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_rooms`
--

CREATE TABLE `master_rooms` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `master_rooms`
--

INSERT INTO `master_rooms` (`id`, `name`) VALUES
('RUANG_STUDIO', 'Ruang Studio (10-12 org)'),
('RUANG_MATOA', 'Ruang Matoa (45-75 org)'),
('RUANG_JATI', 'Ruang Jati (30-45 org)'),
('RUANG_KENARI', 'Ruang Kenari (10-12 org)'),
('RUANG_EBONY', 'Ruang Ebony (10-12 org)'),
('GEDUNG_BUNDAR', 'Gedung Bundar (75-100 org)'),
('MAHONI', 'Mahoni (10-12 org)'),
('RG_DEWAN', 'Rg. Dewan (5-7 org)'),
('RG_PDID', 'Rg. PDID (20-30 org)'),
('RUANG_KMD', 'Ruang Rapat KMD (8-10 orang)'),
('RUANG_HERBARIUM', 'Ruang Rapat Herbarium (30-35 org)');


-- --------------------------------------------------------

--
-- Table structure for table `master_vehicles`
--

CREATE TABLE `master_vehicles` (
  `id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `master_vehicles`
--

INSERT INTO `master_vehicles` (`id`, `name`) VALUES
('AVZ001', 'Panther - F 1895 A'),
('INV002', 'Panther - F 1898 A'),
('XPD001', 'Xpander Hitam B 1316 HOC'),
('XPD002', 'Xpander Silver B 1480 HKB'),
('ZNX001', 'Zennix B 1981 HKC');


-- --------------------------------------------------------

--
-- Table structure for table `notifications`
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
-- Dumping data for table `notifications`
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
-- Table structure for table `repair_budgets`
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
-- Dumping data for table `repair_budgets`
--

INSERT INTO `repair_budgets` (`id`, `repair_request_id`, `item_name`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(16, 8, 'freon', 1, '75000.00', '75000.00', '2025-12-31 03:57:29'),
(22, 2, 'Kabel HDMI', 1, '51000000.00', '51000000.00', '2026-01-09 06:10:28'),
(26, 1, 'AC Baru', 5, '5000000.00', '25000000.00', '2026-01-09 06:58:58'),
(37, 10, 'AC', 5, '5000000.00', '25000000.00', '2026-01-09 07:50:15'),
(38, 11, 'MCB Baru', 1, '20000.00', '20000.00', '2026-01-12 08:12:17'),
(39, 12, 'Mcb Baru', 1, '50000.00', '50000.00', '2026-01-21 08:16:17'),
(40, 9, 'lampu baru', 5, '10010000.00', '50050000.00', '2026-01-21 08:17:24'),
(47, 13, 'remote ac baru', 1, '750000.00', '750000.00', '2026-02-11 02:53:08');


-- --------------------------------------------------------

--
-- Table structure for table `repair_requests`
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
  `status` enum('pending','verified','approved','rejected','completed','in-progress','returned','waiting_manager_fmd','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','pic_checking','ready_for_user') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_requests`
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
(13, 4, 'Indira Destriana Anjani', 'FMD', 'Dormitory B kamar 1', '2026-02-03', '14:38:00', 'AC tidak dapat terhubung ke remote', 'medium', 'verified', '2026-02-03 07:40:32', '2026-02-11 02:53:08', '\n[11/2/2026, 09.53.08] [System] - STATUS UPDATE: RAB Diajukan (Rp 750.000) - Verified (Menunggu Approval Manager FMD)'),
(14, 4, 'Indira Destriana Anjani', 'FMD', 'Dormitory B kamar 3', '2026-03-13', '09:22:00', 'drainase wastafel macet', 'low', 'in-progress', '2026-03-13 02:22:49', '2026-04-14 04:00:41', '[14 Apr 2026 11:00] [Indra Septian, A.Md] - IN-PROGRESS: bjkbjkbhg'),
(15, 52, 'Lastiah, SE', 'FMD', 'Selasar depan perpustakaan ', '2026-04-14', '09:30:00', 'Kondisi keramik sudah mengangkat, dikhawatirkan terinjak oleh peserta kunjungan ', 'high', 'pending', '2026-04-14 08:50:50', '2026-04-14 08:50:50', NULL),
(16, 52, 'Lastiah, SE', 'FMD', 'Laboratorium Kultur Jaringan ', '2026-05-13', '09:59:00', 'Lampu laboratorium kultur jaringan diruang kultur mengalami kerusakan/mati sehingga pencahayaan di area kultur menjadi kurang optimal', 'high', 'pending', '2026-05-13 03:02:37', '2026-05-13 03:02:37', NULL),
(17, 52, 'Lastiah, SE', 'FMD', 'Garasi', '2026-05-11', '10:00:00', 'AC tidak berfugsi dengan baik', 'medium', 'pending', '2026-05-13 03:10:14', '2026-05-13 03:10:14', NULL);


-- --------------------------------------------------------

--
-- Table structure for table `room_requests`
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
  `status` enum('pending','verified','approved','rejected','completed','in-progress','returned','waiting_manager_fmd','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','pic_checking','ready_for_user') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_requests`
--

INSERT INTO `room_requests` (`id`, `user_id`, `room_id`, `applicant_name`, `applicant_unit`, `date_start`, `time_start`, `date_end`, `time_end`, `purpose`, `participants`, `special_needs`, `status`, `created_at`, `updated_at`, `note`) VALUES
(1, NULL, 'RUANG_JATI', 'Indira', 'FMD', '2025-12-08', '07:00:00', '2025-12-08', '12:00:00', 'Kunjungan Industri', 54, 'Microphone', 'completed', '2025-12-08 04:09:36', '2026-03-04 07:00:48', 'silahkan konfirmasi ke FMD atas ruangan Matoa\n[08 Jan 2026, 15.34] [Alfi Dwi Nugroho] - VERIFIED: dicarikan ruangannya\n[8/1/2026, 15.41.07] [Indira] - COMPLETED: Acara sudah selesai, confirm to security lobby\n'),
(2, NULL, 'RUANG_MATOA', 'Indira', 'FAD', '2025-12-11', '08:52:00', '2025-12-25', '08:52:00', 'Anak Magang', 8, 'Rice Cooker', 'approved', '2025-12-11 01:52:54', '2026-03-04 07:00:48', ' [Pengajuan Disetujui]'),
(3, NULL, 'GEDUNG_BUNDAR', 'Sheni', 'FMD', '2025-12-11', '09:25:00', '2025-12-11', '15:25:00', 'Gladi', 85, 'Microphone', 'completed', '2025-12-11 02:25:38', '2026-03-04 07:00:48', '[12 Jan 2026, 10.38] [Alfi Dwi Nugroho] - VERIFIED: Saya info ke manager\n[12 Jan 2026, 10.39] [Slamet Widodo Sugiarto] - APPROVED: ok, sudah saya infokan ke security untuk menyiapkan ruangannya\n[12/1/2026, 10.39.44] [Sheni] - COMPLETED: acara sudah selesai, kunci ruangan sudah dikembalikan ke security lobby'),
(4, 4, 'RUANG_JATI', 'Indira Destriana', 'FMD', '2026-01-29', '11:17:00', '2026-01-29', '12:17:00', 'awwdawd', 59, '', 'approved', '2026-01-29 04:17:32', '2026-03-13 02:17:14', '[13 Mar 2026 09:17] [Indra Septian, A.Md] - APPROVED: Ruangan: Ruang Jati (30-45 org). ok saya pindahkan ke ruangan lain karena tidak avail'),
(5, 4, 'GEDUNG_BUNDAR', 'Indira Destriana Anjani', 'FMD', '2026-03-13', '09:20:00', '2026-03-13', '12:20:00', 'Kuliah Umum Magang - Seameo Biotrop', 50, 'setting operator ', 'completed', '2026-03-13 02:21:06', '2026-04-14 03:37:33', '[13 Mar 2026 09:21] [Indra Septian, A.Md] - APPROVED: Ruangan: Gedung Bundar (75-100 org). saya pindahkan ke ruangan lain karena tidak avail\n[14 Apr 2026 10:37] [Indra Septian, A.Md] - COMPLETED: baik'),
(6, 1, 'RUANG_JATI', 'Indira Destriana Anjani', 'FMD', '2026-04-14', '09:45:00', '2026-04-14', '12:45:00', 'untuk kegiatan IHT', 32, '', 'rejected', '2026-04-14 02:39:43', '2026-04-14 07:11:12', '[14 Apr 2026 14:11] [Lastiah, SE] - REJECTED: Mengubah status menjadi rejected'),
(7, 14, 'RUANG_KENARI', 'ERWAN PRAYOGI', 'FMD', '2026-04-14', '08:00:00', '2026-04-14', '16:00:00', 'Penilaian dan Evaluasi IHT LSO', 1, 'TV atau Proyektor', 'completed', '2026-04-14 04:07:25', '2026-04-14 04:08:27', '[14 Apr 2026 11:07] [Indra Septian, A.Md] - APPROVED: Ruangan: Ruang Kenari (10-12 org). ok\n[14 Apr 2026 11:08] [ERWAN PRAYOGI] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(8, 14, 'RUANG_STUDIO', 'ERWAN PRAYOGI', 'FMD', '2026-04-14', '11:10:00', '2026-04-14', '15:12:00', '', 1, '', 'completed', '2026-04-14 04:11:14', '2026-04-14 04:12:52', '[14 Apr 2026 11:11] [Indra Septian, A.Md] - APPROVED: Ruangan: Ruang Studio (10-12 org). ok\n[14 Apr 2026 11:12] [Indra Septian, A.Md] - COMPLETED: Mengubah status menjadi completed'),
(9, 49, 'RUANG_JATI', 'Alfi Dwi Nugroho, A.Md', 'FMD', '2026-04-15', '08:30:00', '2026-04-15', '12:30:00', 'IHT LSO', 20, 'Komplit', 'completed', '2026-04-14 06:25:22', '2026-04-14 07:11:33', '[14 Apr 2026 13:26] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[14 Apr 2026 14:11] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(10, 49, 'RUANG_JATI', 'Alfi Dwi Nugroho, A.Md', 'FMD', '2026-04-14', '14:30:00', '2026-04-14', '16:00:00', 'IHT CE', 20, 'Komplit', 'completed', '2026-04-14 06:28:15', '2026-04-14 06:29:25', '[14 Apr 2026 13:29] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[14 Apr 2026 13:29] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(11, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-14', '09:00:00', '2026-04-14', '12:00:00', 'Kegiatan Pengelolaan BMN Satuan Kerja bersama BMN Setditjen Dikti', 10, 'Televisi', 'completed', '2026-04-14 06:41:37', '2026-04-14 14:14:09', '[14 Apr 2026 13:45] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[14 Apr 2026 21:14] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(12, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-04-14', '09:00:00', '2026-04-14', '12:00:00', 'Kegiatan Evaluasi dan Penilaian Peserta In House Training Lahan Sub Optimal (LSO)', 10, 'Televisi', 'completed', '2026-04-14 06:43:04', '2026-04-14 14:14:19', '[14 Apr 2026 13:46] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[14 Apr 2026 21:14] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(13, 52, 'GEDUNG_BUNDAR', 'Lastiah, SE', 'FMD', '2026-04-14', '09:00:00', '2026-04-14', '12:00:00', 'Kegiatan Kunjungan SDIT Husen Abdurahman Bogor ', 120, 'Laptop, Proyektor dan Soundsystem', 'completed', '2026-04-14 06:44:18', '2026-04-14 14:14:29', '[14 Apr 2026 13:46] [Lastiah, SE] - APPROVED: Ruangan: Gedung Bundar (75-100 org). \n[14 Apr 2026 21:14] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(14, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-04-14', '13:00:00', '2026-04-14', '16:00:00', 'Kegiatan Kuliah Umum In House Training Circular Economy', 20, 'Proyektor, Laptop, Soundsytem dan Link Zoom', 'completed', '2026-04-14 06:45:33', '2026-04-14 14:13:58', '[14 Apr 2026 13:46] [Lastiah, SE] - APPROVED: Ruangan: Ruang Matoa (45-75 org). \n[14 Apr 2026 21:13] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(15, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-04-15', '08:00:00', '2026-04-15', '15:00:00', 'Kegiatan Rapat Kerja SEAMEO BIOTROP TA 2026', 50, 'Proyektor, Soundsystem dan Laptop', 'completed', '2026-04-14 07:09:15', '2026-04-16 00:38:57', '[14 Apr 2026 14:12] [Lastiah, SE] - APPROVED: Ruangan: Ruang Matoa (45-75 org). \n[16 Apr 2026 07:38] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(16, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-04-15', '09:00:00', '2026-04-15', '15:00:00', 'Kegiatan Penyusunan Bahan Evaluasi In House Training Lahan Sub Optimal ', 12, 'Televisi', 'completed', '2026-04-14 07:10:29', '2026-04-16 00:38:42', '[14 Apr 2026 14:13] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[16 Apr 2026 07:38] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(17, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-15', '08:00:00', '2026-04-15', '09:00:00', 'Kegiatan Penjelasan Teknis Praktikum IHT GEM', 20, 'Proyektor, Screen dan Laptop', 'completed', '2026-04-14 09:32:33', '2026-04-16 00:39:04', '[14 Apr 2026 16:32] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[16 Apr 2026 07:39] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(18, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-16', '08:30:00', '2026-04-16', '11:00:00', 'Kegiatan IHT CE - Pemanfaatan Limbah Tanaman Aromatik sebagai Produk Bernilai dalam Sistem Ekonomi Sirkular', 10, 'Televisi dan Kabel HDMI', 'completed', '2026-04-15 02:39:24', '2026-04-19 09:51:09', '[15 Apr 2026 13:06] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[19 Apr 2026 16:51] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(19, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-04-16', '09:30:00', '2026-04-16', '11:30:00', 'Kegiatan Kunjungan dari SMP DAAR EL-SALAM', 50, 'Proyektor, Screen, Soundsystem dan Laptop', 'completed', '2026-04-15 02:42:03', '2026-04-19 09:51:05', '[15 Apr 2026 13:06] [Lastiah, SE] - APPROVED: Ruangan: Ruang Matoa (45-75 org). \n[19 Apr 2026 16:51] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(20, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-04-16', '08:30:00', '2026-04-16', '12:00:00', 'Kegiatan Interview Staf', 10, 'Televisi', 'completed', '2026-04-16 01:48:38', '2026-04-19 09:51:02', '[16 Apr 2026 08:49] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[19 Apr 2026 16:51] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(21, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-17', '09:00:00', '2026-04-17', '15:00:00', 'Kegiatan penutupan In House Training Smart Urban Farming (SUF) dan Lahan Sub Optimal (LSO) (waktunya tentative)', 35, 'Proyektor, Screen dan Soundsystem', 'completed', '2026-04-16 08:45:48', '2026-04-19 09:50:58', '[16 Apr 2026 15:47] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[19 Apr 2026 16:50] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(22, 52, 'RUANG_STUDIO', 'Lastiah, SE', 'FMD', '2026-04-20', '09:00:00', '2026-04-20', '12:00:00', 'Kegiatan : Rapat Persiapan ICTB bersama BoD', 12, 'Televisi, Soundsystem, Link Zoom', 'completed', '2026-04-19 22:43:29', '2026-04-22 14:53:34', '[20 Apr 2026 05:43] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[22 Apr 2026 21:53] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(23, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-21', '08:00:00', '2026-04-21', '12:00:00', 'Kegiatan : Kuliah In House Training GEM Identifikasi Gulma & IAS, Analisis Vegetasi', 30, 'Audio lengkap dan teknisi standby diruangan.', 'completed', '2026-04-19 22:49:18', '2026-04-22 14:53:30', '[20 Apr 2026 06:07] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[22 Apr 2026 21:53] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(24, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-22', '08:00:00', '2026-04-22', '12:00:00', 'Kegiatan :  In House Training GEM Pemetaan lahan edukatif berbasis GIS dasar', 30, 'Audio lengkap dan teknisi standby diruangan', 'completed', '2026-04-19 22:50:56', '2026-04-22 14:53:26', '[21 Apr 2026 15:39] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[22 Apr 2026 21:53] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(25, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-20', '13:00:00', '2026-04-20', '17:00:00', 'Kegiatan : Menyusun Dokumen oleh Mahasiswa Magang Casptone bersama Mentor dan Asisten Mentor', 8, 'Televisi dan Laptop', 'completed', '2026-04-20 05:07:32', '2026-04-22 14:53:21', '[20 Apr 2026 12:07] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[22 Apr 2026 21:53] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(26, 52, 'RG_DEWAN', 'Lastiah, SE', 'FMD', '2026-04-21', '13:30:00', '2026-04-21', '16:00:00', 'Kegiatan : Rapat BoD dan Tim Keuangan ', 7, 'Televisi', 'completed', '2026-04-21 08:36:29', '2026-04-22 14:53:15', '[21 Apr 2026 15:36] [Lastiah, SE] - APPROVED: Ruangan: Rg. Dewan (5-7 org). \n[22 Apr 2026 21:53] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(27, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-04-21', '11:00:00', '2026-04-21', '13:00:00', 'Kegiatan : Pertemuan Direktur dan Mahasiswa S3', 15, 'Televisi', 'completed', '2026-04-21 08:38:21', '2026-04-22 14:53:11', '[21 Apr 2026 15:38] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[22 Apr 2026 21:53] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(28, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-22', '08:00:00', '2026-04-22', '16:00:00', 'Kegiatan : Rapat Eksternal Setditjen Dikti ', 15, 'Televisi', 'completed', '2026-04-21 23:05:25', '2026-04-22 14:53:06', '[22 Apr 2026 06:05] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[22 Apr 2026 21:53] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(29, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-04-22', '10:30:00', '2026-04-22', '12:00:00', 'Kegiatan : Rapat Persiapan ICTB bersama HCID', 10, 'Televisi', 'completed', '2026-04-22 04:25:00', '2026-04-22 14:53:02', '[22 Apr 2026 11:25] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[22 Apr 2026 21:53] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(30, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2025-04-23', '08:00:00', '2026-04-23', '16:00:00', 'Kegiatan : Rapat Kerja Setditjen Dikti', 15, 'Televisi', 'rejected', '2026-04-22 14:55:53', '2026-04-22 22:59:53', '[23 Apr 2026 05:59] [Lastiah, SE] - REJECTED: Mengubah status menjadi rejected'),
(31, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-04-23', '08:00:00', '2026-04-23', '15:00:00', 'Kegiatan : Sosialisasi Kesesuaian Kode Belanja Dengan Kode Akun Persediaan BMN Pada Satker Dilingkungan Ditjen Dikti', 35, 'Screen, Soundsystem', 'completed', '2026-04-22 23:03:48', '2026-04-27 01:09:49', '[23 Apr 2026 06:06] [Lastiah, SE] - APPROVED: Ruangan: Ruang Matoa (45-75 org). \n[27 Apr 2026 08:09] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(32, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-23', '08:00:00', '2026-04-23', '16:00:00', 'Kegiatan : Pengembangan System Informasi Hibah BMN PP PTS 2025', 15, 'Screen', 'completed', '2026-04-22 23:05:44', '2026-04-27 01:09:52', '[23 Apr 2026 06:06] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[27 Apr 2026 08:09] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(33, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-27', '08:00:00', '2026-04-27', '12:00:00', 'Kegiatan : In House GEM Arahan Mentor Sebelum Praktikum', 20, 'Proyektor, Soundsystem', 'completed', '2026-04-27 01:07:53', '2026-04-28 01:11:13', '[27 Apr 2026 08:09] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[28 Apr 2026 08:11] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(34, 52, 'RG_PDID', 'Lastiah, SE', 'FMD', '2026-04-27', '09:00:00', '2026-04-27', '12:00:00', 'Kegiatan : In House Circular Economy arahan dari Mentor sebelum Praktikum', 10, 'Soundsystem', 'completed', '2026-04-27 01:09:36', '2026-04-28 01:11:18', '[27 Apr 2026 08:10] [Lastiah, SE] - APPROVED: Ruangan: Rg. PDID (20-30 org). \n[28 Apr 2026 08:11] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(35, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-30', '08:00:00', '2026-04-30', '12:00:00', 'Kegiatan : Lokakarya dan Diskusi Terpumpun Penguatan Ketahanan Pangan Berbasis Teknologi melalui Smart Urban Farming untuk Komunitas Pendidikan', 30, 'Proyektor dan soundsystem', 'completed', '2026-04-27 03:58:34', '2026-05-04 01:23:43', '[27 Apr 2026 10:59] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[04 May 2026 08:23] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(36, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-27', '15:00:00', '2026-04-27', '16:30:00', 'Kegiatan : Evaluasi Mahasiswa Magang Capstone', 10, '', 'completed', '2026-04-27 08:00:31', '2026-04-28 01:11:23', '[27 Apr 2026 15:00] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[28 Apr 2026 08:11] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(37, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-04-28', '08:00:00', '2026-04-28', '12:00:00', 'Kegiatan : Rapat Internal SITD', 16, 'Televisi', 'completed', '2026-04-27 08:22:39', '2026-04-29 08:18:08', '[27 Apr 2026 15:22] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[29 Apr 2026 15:18] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(38, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-28', '08:00:00', '2026-04-28', '12:00:00', 'Kegiatan : Pembelajaran Partisipatif dan Perencanaan Replikasi Program', 30, 'Proyektor', 'completed', '2026-04-28 01:10:52', '2026-04-29 08:18:21', '[28 Apr 2026 08:11] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[29 Apr 2026 15:18] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(39, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-28', '13:00:00', '2026-04-28', '16:00:00', 'Kegiatan : Presentasi Hasil Peserta In House Training Circular Economy', 10, 'Proyektor', 'completed', '2026-04-28 01:13:49', '2026-04-29 08:18:14', '[28 Apr 2026 08:14] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[29 Apr 2026 15:18] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(40, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-29', '08:30:00', '2026-04-29', '12:30:00', 'Kegiatan : Presentasi hasil peserta In House Training Circular Economi', 12, 'Proyektor', 'completed', '2026-04-28 07:59:54', '2026-04-29 01:23:39', '[28 Apr 2026 15:39] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[29 Apr 2026 08:23] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(41, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-04-29', '08:30:00', '2026-04-29', '12:30:00', 'Kegiatan : Presentasi hasil peserta In House Training Circular Economi Kelompok 2', 12, 'Televisi', 'completed', '2026-04-28 08:01:13', '2026-04-29 01:21:39', '[28 Apr 2026 15:39] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[29 Apr 2026 08:21] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(42, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-28', '08:00:00', '2026-04-28', '12:00:00', 'Kegiatan : Peer Training Acara In House Training Program Geopark Educational Model (GEM) ', 20, 'Proyektor dan screen', 'rejected', '2026-04-28 08:36:17', '2026-04-28 08:56:31', '[28 Apr 2026 15:56] [Lastiah, SE] - REJECTED: Mengubah status menjadi rejected'),
(43, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-29', '13:00:00', '2026-04-29', '16:00:00', 'Kegiatan : Penutupan Rangkaian Kegiatan In House Training Program Geopark Educational Model', 45, 'Proyektor dan Screen', 'completed', '2026-04-28 08:37:33', '2026-05-04 01:19:32', '[28 Apr 2026 15:57] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[04 May 2026 08:19] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(44, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-04-29', '08:00:00', '2026-04-29', '12:00:00', 'Kegiatan : Peer Training Acara In House Training Program Geopark Educational Model (GEM)', 20, 'Proyektor dan Screen', 'completed', '2026-04-28 08:57:32', '2026-05-04 01:23:48', '[28 Apr 2026 15:57] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[04 May 2026 08:23] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(45, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-04-29', '08:00:00', '2026-04-29', '16:00:00', 'Kegiatan : Presentasi hasil peserta In House Training Circular Economy', 12, 'Proyektor', 'completed', '2026-04-29 01:23:21', '2026-05-04 01:23:52', '[29 Apr 2026 08:23] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[04 May 2026 08:23] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(46, 52, 'RUANG_KMD', 'Lastiah, SE', 'FMD', '2026-04-29', '11:00:00', '2026-04-29', '13:00:00', 'Kegiatan : Pertemuan DDP dengan Mahasiswa IPB University', 10, 'Proyektor', 'completed', '2026-04-29 03:43:48', '2026-05-04 01:23:55', '[29 Apr 2026 14:57] [Indra Septian, A.Md] - APPROVED: Ruangan: Ruang Rapat KMD (8-10 orang). \n[04 May 2026 08:23] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(47, 52, 'RUANG_STUDIO', 'Lastiah, SE', 'FMD', '2026-04-29', '15:00:00', '2026-04-29', '16:30:00', 'Kegiatan :  Kunjungan dan Diskusi dengan VanHal Larenstein University/Wageningen University, Belanda bersama DDP', 10, 'Televisi', 'completed', '2026-04-29 03:45:51', '2026-05-04 01:23:59', '[29 Apr 2026 14:58] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[04 May 2026 08:23] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(48, 52, 'RUANG_STUDIO', 'Lastiah, SE', 'FMD', '2026-04-30', '08:00:00', '2026-04-30', '12:00:00', 'Kegiatan : Rapat Pertemuan Pra GBM bersama BoD', 10, 'Televisi, Soundsystem', 'completed', '2026-04-29 23:37:47', '2026-05-04 01:24:07', '[30 Apr 2026 06:38] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[04 May 2026 08:24] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(49, 52, 'RUANG_STUDIO', 'Lastiah, SE', 'FMD', '2026-05-04', '09:00:00', '2026-05-04', '12:00:00', 'Kegiatan : Rapat penyusunan timeline dan pengajuan anggaran ICTB-SEAMEO BIOTROP', 17, 'Televisi', 'completed', '2026-05-04 01:21:36', '2026-05-04 02:00:05', '[04 May 2026 08:21] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[04 May 2026 09:00] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(50, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-05-05', '08:00:00', '2026-05-05', '16:00:00', 'Kegiatan : Lokakarya Penyusunan Dokumen GBM ke 64 ', 20, 'Mohon salah satu teknisi Fmd hadir full di ruang jati, spt biasa dibantu untuk menayangkan lagu Indonesia raya SEAMEO color', 'rejected', '2026-05-04 01:27:51', '2026-05-04 01:28:16', '[04 May 2026 08:28] [Lastiah, SE] - REJECTED: Mengubah status menjadi rejected'),
(51, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-05-05', '08:00:00', '2026-05-06', '16:00:00', 'Kegiatan : Lokakarya Penyusunan Dokumen GBM ke 64 ', 20, 'Mohon salah satu teknisi Fmd hadir full di ruang jati, spt biasa dibantu untuk menayangkan lagu Indonesia raya SEAMEO color', 'completed', '2026-05-04 01:29:09', '2026-05-04 04:01:02', '[04 May 2026 08:57] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[04 May 2026 11:01] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(52, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-05-04', '09:00:00', '2026-05-04', '12:00:00', 'Kegiatan : Rapat penyusunan timeline dan pengajuan anggaran masing-masing PIC ICTB', 17, 'Televisi', 'completed', '2026-05-04 01:59:38', '2026-05-07 01:36:56', '[04 May 2026 08:59] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[07 May 2026 08:36] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(53, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-05-04', '13:00:00', '2026-05-04', '16:00:00', 'Kegiatan : Rapat Program 5th ICTB\r\n\r\n', 10, 'Televisi', 'completed', '2026-05-04 03:59:17', '2026-05-07 01:37:00', '[04 May 2026 11:06] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[07 May 2026 08:36] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(54, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-05-05', '08:00:00', '2026-05-06', '16:00:00', 'Kegiatan :  Lokakarya Penyusunan Dokumen GBM ke 64 ', 20, 'Mohon salah satu teknisi Fmd hadir full di ruang jati, spt biasa dibantu untuk menayangkan lagu Indonesia raya SEAMEO color\r\n', 'rejected', '2026-05-04 04:04:49', '2026-05-05 01:40:04', '[05 May 2026 08:40] [Lastiah, SE] - REJECTED: Mengubah status menjadi rejected'),
(55, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-05-05', '08:00:00', '2026-05-06', '16:00:00', 'Kegiatan : Lokakarya Penyusunan Dokumen GBM ke 64 ', 20, 'Mohon salah satu teknisi Fmd hadir full di ruang jati, spt biasa dibantu untuk menayangkan lagu Indonesia raya SEAMEO color', 'completed', '2026-05-05 01:41:14', '2026-05-07 01:37:03', '[05 May 2026 09:16] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[07 May 2026 08:37] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(56, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-05-07', '08:00:00', '2026-05-07', '14:00:00', 'Kegiatan : Launching Program dan Penutupan In House Training Circular Economy (CE)', 30, 'Proyektor', 'completed', '2026-05-07 01:31:19', '2026-05-11 07:20:21', '[07 May 2026 08:32] [Lastiah, SE] - APPROVED: Ruangan: Ruang Matoa (45-75 org). \n[11 May 2026 14:20] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(57, 52, 'RUANG_STUDIO', 'Lastiah, SE', 'FMD', '2026-05-08', '13:30:00', '2026-05-08', '16:00:00', 'Kegiatan : Rapat Persiapan In-Country Training on Urban Farming in Brunei Darussalam', 12, 'Televisi', 'completed', '2026-05-07 08:14:37', '2026-05-11 07:20:35', '[08 May 2026 07:01] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[11 May 2026 14:20] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(58, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-05-12', '09:30:00', '2026-05-12', '11:39:00', 'Kegiatan : Kunjungan SMPIT Insantama Leuwiliang-Kab. Bogor', 60, 'Screen, Audio Visual', 'completed', '2026-05-11 08:06:50', '2026-05-18 01:06:28', '[12 May 2026 07:31] [Lastiah, SE] - APPROVED: Ruangan: Ruang Matoa (45-75 org). \n[18 May 2026 08:06] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(59, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-05-13', '08:30:00', '2026-05-13', '16:00:00', 'Kegiatan : Lokakarya dan Diskusi Terpumpun Geopark Educational Model for School (GEMS) ke 2 “From Concept to Implementation: Pilot, Module, and Partnership Strengthening”', 35, 'Screen, Audio Visual', 'completed', '2026-05-11 08:11:51', '2026-05-18 01:06:18', '[12 May 2026 16:29] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[18 May 2026 08:06] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(60, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-05-13', '16:00:00', '2026-05-13', '18:00:00', 'Kegiatan : Pembekalan Trainer Pelatihan: SUF, LSO, CE, dan GEMS', 60, 'Screen, Audio Visual', 'completed', '2026-05-12 09:26:10', '2026-05-18 01:06:00', '[12 May 2026 16:29] [Lastiah, SE] - APPROVED: Ruangan: Ruang Matoa (45-75 org). \n[18 May 2026 08:06] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(61, 52, 'RUANG_STUDIO', 'Lastiah, SE', 'FMD', '2026-05-18', '08:30:00', '2026-05-18', '12:00:00', 'Kegiatan : Roadshow Program Pemagangan SEAMEO BIOTROP Tahun 2026', 10, 'Televisi dan Audiovisual', 'completed', '2026-05-18 01:32:54', '2026-05-20 01:30:41', '[18 May 2026 08:33] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[20 May 2026 08:30] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(62, 52, 'RUANG_STUDIO', 'Lastiah, SE', 'FMD', '2026-05-20', '08:00:00', '2026-05-20', '12:00:00', 'Kegiatan : Seminar dan Pelatihan Regional LSO', 5, 'Televisi', 'completed', '2026-05-20 01:31:20', '2026-05-25 01:13:56', '[20 May 2026 08:41] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[25 May 2026 08:13] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(63, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-05-20', '13:00:00', '2026-05-20', '15:00:00', 'Kegiatan : Briefing Peserta Magang terkait Presentasi Akhir dan Pelepasan Program Magang', 35, 'Screen Proyektor', 'completed', '2026-05-20 01:32:25', '2026-05-25 01:14:11', '[20 May 2026 08:41] [Lastiah, SE] - APPROVED: Ruangan: Ruang Matoa (45-75 org). \n[25 May 2026 08:14] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(64, 52, 'RUANG_STUDIO', 'Lastiah, SE', 'FMD', '2026-05-25', '10:00:00', '2026-05-25', '12:00:00', 'Kegiatan : Ekspose audi KAP bersama DDA', 10, 'Televisi', 'completed', '2026-05-25 01:15:08', '2026-05-25 09:13:40', '[25 May 2026 08:26] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[25 May 2026 16:13] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(65, 52, 'RUANG_EBONY', 'Lastiah, SE', 'FMD', '2026-05-25', '09:00:00', '2026-05-25', '12:00:00', 'Kegiatan : Rapat Tim Secretariat ICTB', 10, 'Proyektor', 'completed', '2026-05-25 02:02:24', '2026-05-25 09:13:54', '[25 May 2026 11:19] [Lastiah, SE] - APPROVED: Ruangan: Ruang Ebony (10-12 org). \n[25 May 2026 16:13] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(66, 1, 'RUANG_STUDIO', 'Indira Destriana Anjani', 'FMD', '2026-05-25', '12:00:00', '2026-05-25', '12:20:00', 'Rapat DDP dengan pengadaan', 10, '', 'completed', '2026-05-25 06:50:04', '2026-05-25 09:13:57', '[25 May 2026 13:51] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[25 May 2026 16:13] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(67, 1, 'RUANG_STUDIO', 'Indira Destriana Anjani', 'FMD', '2026-05-25', '12:20:00', '2026-05-25', '14:00:00', 'Rapat DDP dengan tim program', 10, '', 'completed', '2026-05-25 06:51:20', '2026-05-25 09:14:01', '[25 May 2026 13:51] [Lastiah, SE] - APPROVED: Ruangan: Ruang Studio (10-12 org). \n[25 May 2026 16:14] [Lastiah, SE] - COMPLETED: Mengubah status menjadi completed'),
(68, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-05-26', '08:00:00', '2026-05-26', '15:00:00', 'Kegiatan : Penutpan Magang Kemnaker', 45, 'Screen, Audio Visual', 'completed', '2026-05-25 08:51:49', '2026-06-02 00:40:29', '[26 May 2026 07:22] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[02 Jun 2026 07:40] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(69, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-06-02', '08:00:00', '2026-06-05', '16:00:00', 'Kegiatan : Pelatihan Urban Farming Guru SLB bekerja sama dengan Direktorat PMPK', 40, 'Screen, Audio Visual', 'completed', '2026-06-02 00:41:10', '2026-06-04 01:15:11', '[03 Jun 2026 07:46] [Lastiah, SE] - APPROVED: Ruangan: Ruang Jati (30-45 org). \n[04 Jun 2026 08:14] [Lastiah, SE] - READY_FOR_USER: [LAPORAN CHECK & RECHECK RUANGAN]<br>Waktu Pengecekan: -<br>Status: Siap digunakan<br><br>Kebersihan: -<br>Fasilitas: -<br>Listrik: -<br>Perlengkapan: -<br>Pengaturan: -<br>Video: -\n[04 Jun 2026 08:15] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(70, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-06-02', '09:00:00', '2026-06-02', '12:00:00', 'Kegiatan : Survey lokasi kunjungan bersama staf Diklat Kemenkeu oleh HCID', 10, 'Televisi', 'completed', '2026-06-02 01:23:09', '2026-06-04 01:15:32', '[03 Jun 2026 07:46] [Lastiah, SE] - APPROVED: Ruangan: Ruang Kenari (10-12 org). \n[04 Jun 2026 08:14] [Lastiah, SE] - READY_FOR_USER: [LAPORAN CHECK & RECHECK RUANGAN]<br>Waktu Pengecekan: -<br>Status: Siap digunakan<br><br>Kebersihan: -<br>Fasilitas: -<br>Listrik: -<br>Perlengkapan: -<br>Pengaturan: -<br>Video: -\n[04 Jun 2026 08:15] [Lastiah, SE] - COMPLETED: Pengajuan diselesaikan oleh Pemohon.'),
(71, 1, 'RUANG_STUDIO', 'Indira Destriana Anjani', 'FMD', '2026-06-03', '09:30:00', '2026-06-03', '12:30:00', 'take podcast video', 10, '', 'rejected', '2026-06-02 08:24:25', '2026-06-04 01:22:06', '[04 Jun 2026 08:22] [Lastiah, SE] - REJECTED: Mengubah status menjadi rejected'),
(72, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-06-04', '08:00:00', '2026-06-04', '16:00:00', 'Kegiatan : Rapat Koordinasi 7 SCI bersama Biro Kemdikdasmen tentang Rancangan Penyusunan Permendikdasmen bagi 7 SCI', 45, 'Screen dan Audio Visual', 'approved', '2026-06-03 01:05:39', '2026-06-04 01:58:03', '[04 Jun 2026 08:57] [Lastiah, SE] - WAITING_MANAGER_FMD: Ruang Jati (30-45 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[04 Jun 2026 08:58] [Slamet Widodo Sugiarto] - APPROVED: Silahkan dipersiapan sesuai SOP '),
(73, 52, 'RUANG_MATOA', 'Lastiah, SE', 'FMD', '2026-06-04', '08:00:00', '2026-06-04', '10:00:00', 'Kegiatan : Bimtek Peningkatan Kompetensi Guru SLB Bidang Keterampilan Moda Luring', 35, 'Proyektor dan screen', 'approved', '2026-06-04 01:05:51', '2026-06-04 01:24:51', '[04 Jun 2026 08:09] [Lastiah, SE] - WAITING_MANAGER_FMD: Ruang Matoa (45-75 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[04 Jun 2026 08:24] [Slamet Widodo Sugiarto] - APPROVED: Pengajuan di setujui'),
(74, 52, 'RUANG_KENARI', 'Lastiah, SE', 'FMD', '2026-06-04', '09:00:00', '2026-06-04', '12:00:00', 'Kegiatan : Diskusi Persiapan dan Timeline Pelaksanaan ICTB', 15, 'Televisi', 'approved', '2026-06-04 01:07:20', '2026-06-04 01:24:40', '[04 Jun 2026 08:10] [Lastiah, SE] - WAITING_MANAGER_FMD: Ruang Kenari (10-12 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[04 Jun 2026 08:10] [Lastiah, SE] - WAITING_MANAGER_FMD: Ruangan: Ruang Kenari (10-12 org). Ruang Kenari (10-12 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan\n[04 Jun 2026 08:24] [Slamet Widodo Sugiarto] - APPROVED: Pengajuan di setujui'),
(75, 52, 'RUANG_JATI', 'Lastiah, SE', 'FMD', '2026-06-08', '08:30:00', '2026-06-08', '15:00:00', 'Kegiatan : Lokakarya GBM SEAMEO BIOTROP', 30, 'proyektor..', 'waiting_manager_fmd', '2026-06-08 01:20:50', '2026-06-08 01:21:15', '[08 Jun 2026 08:21] [Lastiah, SE] - WAITING_MANAGER_FMD: Ruang Jati (30-45 org) tersedia, diteruskan kepada Manager FMD untuk approval permohonan');


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','user','supervisor') NOT NULL DEFAULT 'user',
  `telegram_chat_id` varchar(50) DEFAULT NULL,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `callmebot_apikey` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_id`, `password`, `full_name`, `role`, `telegram_chat_id`, `whatsapp_number`, `callmebot_apikey`, `created_at`) VALUES
(1, 63, 'seabiotrop68', 'Indira Destriana Anjani', 'user', '6144952473', '', NULL, '2026-04-14 02:36:34'),
(2, 64, 'seabiotrop68', 'Sheni Olvianda', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(3, 66, 'seabiotrop68', 'Slamet Widodo Sugiarto', 'admin', '', '', NULL, '2026-04-14 02:36:34'),
(4, 111, 'seabiotrop68', 'AGUS', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(5, 112, 'seabiotrop68', 'AHMAD SUDRAJAT', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(6, 114, 'seabiotrop68', 'ANDI SUHANDI', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(7, 115, 'seabiotrop68', 'ANGGI PURNAMA', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(8, 116, 'seabiotrop68', 'AULIA RIZKY ANANDA', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(9, 117, 'seabiotrop68', 'DADANG SETIAWAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(10, 118, 'seabiotrop68', 'DEDE MAULANA', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(11, 119, 'seabiotrop68', 'DIDIT TRISNADI', 'user', '', '081324708264', NULL, '2026-04-14 02:36:34'),
(12, 120, 'seabiotrop68', 'ENDANG SETIAWAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(13, 121, 'seabiotrop68', 'ENDIH', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(14, 122, 'seabiotrop68', 'ERWAN PRAYOGI', 'user', '', '081282833935', NULL, '2026-04-14 02:36:34'),
(15, 123, 'seabiotrop68', 'HASAN HUSEN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(16, 124, 'seabiotrop68', 'HERDI FERDIANSYAH MAULANA', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(17, 125, 'seabiotrop68', 'HERMAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(18, 126, 'seabiotrop68', 'HERU', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(19, 127, 'seabiotrop68', 'ICHWAN PIDIN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(20, 128, 'seabiotrop68', 'IDA HERMAWAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(21, 129, 'seabiotrop68', 'IDIM DIMYATI', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(22, 130, 'seabiotrop68', 'INDRA ARDIANSYAH', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(23, 131, 'seabiotrop68', 'ISNANDAR', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(24, 132, 'seabiotrop68', 'KHAMELANDI ASTRIAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(25, 133, 'seabiotrop68', 'KOMARUDIN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(26, 134, 'seabiotrop68', 'KURNIAWAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(27, 135, 'seabiotrop68', 'KUSNADI', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(28, 136, 'seabiotrop68', 'LINGGAR', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(29, 137, 'seabiotrop68', 'M. ABUDIN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(30, 138, 'seabiotrop68', 'MAULANA HASANUDIN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(31, 139, 'seabiotrop68', 'MOH.WAHYU NOORRAMDHANY Y.', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(32, 140, 'seabiotrop68', 'MUHAMAD ZAENUDDIN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(33, 141, 'seabiotrop68', 'MUHAMMAD NAZRIL ILHAM', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(34, 142, 'seabiotrop68', 'MULYADI', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(35, 143, 'seabiotrop68', 'MULYANA', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(36, 144, 'seabiotrop68', 'RIDWAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(37, 145, 'seabiotrop68', 'ROHMAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(38, 146, 'seabiotrop68', 'RUDI HARTONO', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(39, 147, 'seabiotrop68', 'SARIPUDIN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(40, 148, 'seabiotrop68', 'SUGIH MAULANA', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(41, 149, 'seabiotrop68', 'TAOFIK ROKAYAT', 'user', '', '081389986648', NULL, '2026-04-14 02:36:34'),
(42, 150, 'seabiotrop68', 'TASORI HERIYANTO', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(43, 151, 'seabiotrop68', 'WAHYUDI', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(44, 152, 'seabiotrop68', 'WALDI HIDAYAT', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(45, 153, 'seabiotrop68', 'YAYA TARYANA', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(46, 154, 'seabiotrop68', 'ZULPA HILMAWAN', 'user', '', NULL, NULL, '2026-04-14 02:36:34'),
(47, 155, 'seabiotrop68', 'Administrator', 'admin', '', NULL, NULL, '2026-04-14 02:36:34'),
(48, 156, 'seabiotrop68', 'Agus Sujadi', 'admin', NULL, NULL, NULL, '2026-04-14 02:36:34'),
(49, 157, 'seabiotrop68', 'Alfi Dwi Nugroho, A.Md', 'admin', '1597712640', '081219974021', NULL, '2026-04-14 02:36:34'),
(50, 158, 'seabiotrop68', 'Bahrudin', 'admin', NULL, NULL, NULL, '2026-04-14 02:36:34'),
(51, 159, 'seabiotrop68', 'Indra Septian, A.Md', 'admin', NULL, NULL, NULL, '2026-04-14 02:36:34'),
(52, 160, 'seabiotrop68', 'Lastiah, SE', 'admin', NULL, NULL, NULL, '2026-04-14 02:36:34'),
(53, 163, 'seabiotrop68', 'Prof. Dr. Edi Santosa, S.P., M.Si', 'admin', '', NULL, NULL, '2026-04-14 02:36:34'),
(54, 164, 'seabiotrop68', 'Dr. Elis Rosdiawati, M.Pd', 'admin', NULL, NULL, NULL, '2026-04-14 02:36:34'),
(55, 165, 'seabiotrop68', 'Dr.rer.nat. Doni Yusri, SP., MM', 'admin', NULL, NULL, NULL, '2026-04-14 02:36:34'),
(56, 166, 'seabiotrop68', 'Peri Siantuni, SE', 'user', NULL, NULL, NULL, '2026-04-14 02:36:34'),
(57, 167, 'seabiotrop68', 'Mulyadiana Prayoga', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(58, 168, 'seabiotrop68', 'Supriyatno, A.Md', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(59, 169, 'seabiotrop68', 'Sunardi Ikay', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(60, 170, 'seabiotrop68', 'Herni Widhiastuti, S.Si', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(61, 171, 'seabiotrop68', 'Dewi Rahmawati, M.Si', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(62, 172, 'seabiotrop68', 'Nopi Ramli', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(63, 173, 'seabiotrop68', 'Haritz Cahya Nugraha, M.T', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(64, 174, 'seabiotrop68', 'Aan Darwati, S.Ak', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(65, 175, 'seabiotrop68', 'Aris Purnajaya', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(66, 176, 'seabiotrop68', 'Asep Saepudin', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(67, 177, 'seabiotrop68', 'Asep Syaefudin, SE', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(68, 178, 'seabiotrop68', 'Dani Yudi Trisna, A.Md', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(69, 179, 'seabiotrop68', 'Dewi Suryani Oktavia Basuki, SP., MM', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(70, 180, 'seabiotrop68', 'Didi Junaedi, A.Md', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(71, 181, 'seabiotrop68', 'Fitri Junaedy, SEI', 'user', '1597712640', NULL, NULL, '2026-04-14 02:36:35'),
(72, 182, 'seabiotrop68', 'Hery Yanto', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(73, 183, 'seabiotrop68', 'Iman', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(74, 184, 'seabiotrop68', 'Irawan', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(75, 185, 'seabiotrop68', 'Lidia Defita, S.Kom', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(76, 186, 'seabiotrop68', 'Lillys Betty Yuliawati, S.Si', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(77, 187, 'seabiotrop68', 'Risa Rosita, M.Si', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(78, 188, 'seabiotrop68', 'Risya Ayu Astari, A.Md', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(79, 189, 'seabiotrop68', 'Rizkia Tirtani', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(80, 190, 'seabiotrop68', 'Rosadi, S.Pd.I', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(81, 191, 'seabiotrop68', 'Saiful Bachri, M.Si', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(82, 193, 'seabiotrop68', 'Tenni Wahyuni, S.I.Kom', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(83, 194, 'seabiotrop68', 'Trijanti A. Widinni Asnan, M.Si.', 'admin', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(84, 195, 'seabiotrop68', 'Woro Kanti Dharmastuti, M.Si', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(85, 196, 'seabiotrop68', 'Yana', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(86, 197, 'seabiotrop68', 'Zaenal Abidin', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(87, 198, 'seabiotrop68', 'Zulkarnaen Noor Syarif, M.Kom', 'user', NULL, NULL, NULL, '2026-04-14 02:36:35'),
(88, 155, 'seabiotrop68', 'Administrator Utama', 'admin', '', NULL, NULL, '2026-04-14 02:36:35'),
(89, NULL, 'seabiotrop68', 'Supervisor Lapangan', 'supervisor', '', NULL, NULL, '2026-04-14 02:36:35'),
(90, NULL, 'seabiotrop68', 'Staff Umum', 'user', '', NULL, NULL, '2026-04-14 02:36:35'),
(91, NULL, 'seabiotrop68', 'Achmad Fayad', 'user', '', NULL, NULL, '2026-04-14 02:36:35');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_requests`
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
  `status` enum('pending','verified','approved','rejected','completed','in-progress','returned','waiting_manager_fmd','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','pic_checking','ready_for_user') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_requests`
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
(18, 133, 'AVZ001', 'Fitri Junaedy, SEI', 'HRAD', '2026-03-12', '2026-03-12', 'Membeli keperluan halal bihalal', 'approved', '2026-03-12 02:27:49', '2026-03-12 02:29:06', '[12 Mar 2026 09:29] [Alfi Dwi Nugroho, A.Md] - APPROVED: Vehicle: Panther - F 1895 A. Driver: Didit Trisnadi (Pengemudi). Ok', 'Didit Trisnadi (Pengemudi)', '09:30:00', '12:30:00'),
(19, 84, 'INV002', 'Woro Kanti Dharmastuti, M.Si', 'KMD', '2026-06-03', '2026-06-03', 'Survey furniture di informa', 'approved', '2026-06-03 01:55:19', '2026-06-04 01:25:06', '[03 Jun 2026 09:07] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Panther - F 1898 A tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: ERWAN PRAYOGI (DRIVER)\n[03 Jun 2026 09:15] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Panther - F 1898 A tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: ERWAN PRAYOGI (DRIVER)\n[03 Jun 2026 09:22] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Panther - F 1898 A tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: ERWAN PRAYOGI (DRIVER)\n[03 Jun 2026 10:51] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Panther - F 1898 A tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: ERWAN PRAYOGI (DRIVER)\n[04 Jun 2026 08:25] [Slamet Widodo Sugiarto] - APPROVED: Disetujui oleh Manager FMD. Silakan PIC Kendaraan (Unit & Driver) menyiapkan permintaan dan memberikan laporan Check & Recheck.', 'ERWAN PRAYOGI (DRIVER)', '10:00:00', '00:00:00'),
(23, 62, 'INV002', 'Nopi Ramli', 'HRAD', '2026-06-04', '2026-06-04', 'Ambil makan siang di soto rahayu pajajaran sebanyak 40 paket untuk kegiatan rapat 7 SCI ', 'approved', '2026-06-04 01:56:35', '2026-06-04 04:04:21', '[04 Jun 2026 08:57] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Panther - F 1898 A tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: ERWAN PRAYOGI (DRIVER)\n[04 Jun 2026 11:04] [Slamet Widodo Sugiarto] - APPROVED: Silahkan dilaksanakan', 'ERWAN PRAYOGI (DRIVER)', '11:15:00', '12:00:00'),
(30, 69, 'INV002', 'Dewi Suryani Oktavia Basuki, SP., MM', 'HCID', '2026-06-05', '2026-06-05', '', 'waiting_manager_fmd', '2026-06-05 01:55:14', '2026-06-05 03:10:59', '[05 Jun 2026 10:10] [Alfi Dwi Nugroho, A.Md] - WAITING_MANAGER_FMD: Panther - F 1898 A tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: ERWAN PRAYOGI', 'ERWAN PRAYOGI', '09:00:00', '17:59:00');


-- --------------------------------------------------------

--
-- Table structure for table `zoom_requests`
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
  `status` enum('pending','verified','approved','rejected','completed','in-progress','returned','waiting_manager_fmd','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','pic_checking','ready_for_user') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zoom_requests`
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
(19, 133, 'zoom_01', 'Fitri Junaedy, SEI', 'HRAD', '2026-03-12', '09:45:00', '2026-03-12', '11:45:00', '0', 90, 'Rapat Triwulan', '', 'pending', '2026-03-12 02:45:17', '2026-03-12 02:45:17', NULL),
(20, 133, 'zoom_01', 'Fitri Junaedy, SEI', 'HRAD', '2026-03-12', '13:31:00', '2026-03-12', '16:31:00', '0', 52, 'Rapat Triwulan', '', 'approved', '2026-03-12 06:31:40', '2026-03-12 06:32:59', '[12 Mar 2026 13:32] [Indra Septian, A.Md] - APPROVED: Zoom Info: gudiagiwchafoieafa. ok');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip_nik` (`nip_nik`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_position` (`position`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `item_loan_requests`
--
ALTER TABLE `item_loan_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_item_loan_requests_user_id` (`user_id`);

--
-- Indexes for table `master_rooms`
--
ALTER TABLE `master_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_vehicles`
--
ALTER TABLE `master_vehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_req` (`request_id`,`request_type`),
  ADD KEY `idx_resource` (`resource_id`,`resource_type`);

--
-- Indexes for table `repair_budgets`
--
ALTER TABLE `repair_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_request_id` (`repair_request_id`);

--
-- Indexes for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_repair_requests_user_id` (`user_id`);

--
-- Indexes for table `room_requests`
--
ALTER TABLE `room_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_room_requests_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `vehicle_requests`
--
ALTER TABLE `vehicle_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_vehicle_requests_user_id` (`user_id`);

--
-- Indexes for table `zoom_requests`
--
ALTER TABLE `zoom_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `fk_zoom_requests_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `item_loan_requests`
--
ALTER TABLE `item_loan_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `repair_budgets`
--
ALTER TABLE `repair_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `repair_requests`
--
ALTER TABLE `repair_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `room_requests`
--
ALTER TABLE `room_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `vehicle_requests`
--
ALTER TABLE `vehicle_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `zoom_requests`
--
ALTER TABLE `zoom_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `repair_budgets`
--
ALTER TABLE `repair_budgets`
  ADD CONSTRAINT `repair_budgets_ibfk_1` FOREIGN KEY (`repair_request_id`) REFERENCES `repair_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD CONSTRAINT `fk_repair_requests_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `room_requests`
--
ALTER TABLE `room_requests`
  ADD CONSTRAINT `fk_room_requests_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
