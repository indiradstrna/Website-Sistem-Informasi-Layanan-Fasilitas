CREATE TABLE IF NOT EXISTS `master_dormitories` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `master_dormitories` (`id`, `name`) VALUES
('A', 'Dormitory A'),
('B', 'Dormitory B'),
('C', 'Dormitory C'),
('E', 'Dormitory E'),
('F', 'Dormitory F'),
('G', 'Dormitory G'),
('H', 'Dormitory H'),
('I', 'Dormitory I'),
('J', 'Dormitory J'),
('K', 'Dormitory K'),
('L', 'Dormitory L');

CREATE TABLE IF NOT EXISTS `dormitory_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `dormitory_id` varchar(50) NOT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_unit` varchar(100) NOT NULL,
  `occupant_name` varchar(150) NOT NULL,
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
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
