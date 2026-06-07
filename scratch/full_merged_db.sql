-- BISMILLAH MERGED DB

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;
SET time_zone = '+00:00';


-- --------------------------------------------------------
-- Struktur dari tabel `employees` (Merged)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip_nik` varchar(50) DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `role_title` varchar(50) DEFAULT NULL,
  `position` varchar(150) DEFAULT NULL,
  `education` varchar(50) DEFAULT NULL,
  `tenure` varchar(50) DEFAULT NULL,
  `certificates` text DEFAULT NULL,
  `employee_type` enum('PNS','PPPK','Kontrak','Outsource') DEFAULT 'PNS',
  `is_active` tinyint(1) DEFAULT 1,
  `type` varchar(50) DEFAULT 'outsourcing',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nip_nik_unique` (`nip_nik`),
  KEY `idx_department` (`department`),
  KEY `idx_position` (`position`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------
-- Struktur dari tabel `users` (Merged)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('user','admin','super admin','supervisor') NOT NULL DEFAULT 'user',
  `telegram_chat_id` varchar(50) DEFAULT NULL,
  `daily_workload` int(11) DEFAULT 8,
  `work_radius_meters` int(11) DEFAULT 100,
  `mac_address` varchar(255) DEFAULT NULL,
  `wfh_lat` varchar(50) DEFAULT NULL,
  `wfh_lng` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `assessments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `assessments`;
CREATE TABLE `assessments` (
  `id` int NOT NULL,
  `employee_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assessment_date` date NOT NULL,
  `period` enum('1','2') COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1',
  `extra_score` int DEFAULT '0',
  `total_score` decimal(5,2) DEFAULT '0.00',
  `data_json` longtext COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `attendance`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `work_type` enum('WFO','WFH') COLLATE utf8mb4_general_ci DEFAULT 'WFO',
  `date` date NOT NULL,
  `clock_in_time` datetime DEFAULT NULL,
  `clock_out_time` datetime DEFAULT NULL,
  `clock_in_lat` decimal(11,8) DEFAULT NULL,
  `clock_in_lng` decimal(11,8) DEFAULT NULL,
  `clock_out_lat` decimal(11,8) DEFAULT NULL,
  `clock_out_lng` decimal(11,8) DEFAULT NULL,
  `status` enum('ontime','late') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ontime',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `employee_targets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `employee_targets`;
CREATE TABLE `employee_targets` (
  `id` int NOT NULL,
  `employee_id` int NOT NULL,
  `year` year NOT NULL,
  `target_3_months` text COLLATE utf8mb4_general_ci,
  `target_6_months` text COLLATE utf8mb4_general_ci,
  `target_1_year` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `evidence`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `evidence`;
CREATE TABLE `evidence` (
  `id` int NOT NULL,
  `session_id` int NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `note` text COLLATE utf8mb4_general_ci,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `gps_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `gps_logs`;
CREATE TABLE `gps_logs` (
  `id` int NOT NULL,
  `session_id` int NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trigger_type` enum('start','stop','tracking') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `item_loan_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `item_loan_requests`;
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

-- --------------------------------------------------------
-- Struktur dari tabel `login_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `nik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('success','failed_password','blocked_device','blocked_duplicate','not_found') COLLATE utf8mb4_general_ci NOT NULL,
  `device_id` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `notifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
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

-- --------------------------------------------------------
-- Struktur dari tabel `qr_tokens`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `qr_tokens`;
CREATE TABLE `qr_tokens` (
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `repair_budgets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `repair_budgets`;
CREATE TABLE `repair_budgets` (
  `id` int(11) NOT NULL,
  `repair_request_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `repair_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `repair_requests`;
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

-- --------------------------------------------------------
-- Struktur dari tabel `room_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `room_requests`;
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

-- --------------------------------------------------------
-- Struktur dari tabel `task_assignments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `task_assignments`;
CREATE TABLE `task_assignments` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `type` enum('Harian','Mingguan','Bulanan') COLLATE utf8mb4_general_ci DEFAULT 'Harian',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `vehicle_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `vehicle_requests`;
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

-- --------------------------------------------------------
-- Struktur dari tabel `work_sessions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `work_sessions`;
CREATE TABLE `work_sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('active','completed','pending_approval','revision') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `task_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `manager_note` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Struktur dari tabel `zoom_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `zoom_requests`;
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


-- --------------------------------------------------------
-- Foreign Keys
-- --------------------------------------------------------

-- Ketidakleluasaan untuk tabel `repair_budgets`
ALTER TABLE `repair_budgets`
  ADD CONSTRAINT `fk_repair_budgets_req` FOREIGN KEY (`repair_request_id`) REFERENCES `repair_requests` (`id`) ON DELETE CASCADE;

-- Ketidakleluasaan untuk tabel `repair_requests`
ALTER TABLE `repair_requests`
  ADD CONSTRAINT `fk_repair_requests_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Ketidakleluasaan untuk tabel `room_requests`
ALTER TABLE `room_requests`
  ADD CONSTRAINT `fk_room_requests_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

COMMIT;
