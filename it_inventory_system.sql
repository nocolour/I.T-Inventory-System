-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 03, 2025 at 07:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `it_inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `accessories`
--

CREATE TABLE `accessories` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `processor` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `existing_user` varchar(100) DEFAULT NULL,
  `status` enum('Available','Assigned','Under Repair','Decommissioned') DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty` date DEFAULT NULL,
  `other_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accessories`
--

INSERT INTO `accessories` (`id`, `device_name`, `brand`, `serial_number`, `model`, `processor`, `ram`, `storage`, `ip_address`, `mac_address`, `location`, `existing_user`, `status`, `purchase_date`, `warranty`, `other_details`) VALUES
(1, 'HDMI-VGA CONVERTER-01', 'OEM', 'NOSERIALNUMBER01', 'HDMI-VGA CONVERTER', '', '', '', '', '', '', 'MARKETING', 'Assigned', '2025-01-31', '2025-02-28', '');

-- --------------------------------------------------------

--
-- Table structure for table `computers`
--

CREATE TABLE `computers` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `processor` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `existing_user` varchar(100) DEFAULT NULL,
  `status` enum('Available','Assigned','Under Repair','Decommissioned') DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty` date DEFAULT NULL,
  `other_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `computers`
--

INSERT INTO `computers` (`id`, `device_name`, `brand`, `serial_number`, `model`, `processor`, `ram`, `storage`, `ip_address`, `mac_address`, `location`, `existing_user`, `status`, `purchase_date`, `warranty`, `other_details`) VALUES
(1, 'DESMOND-PC', 'CLONE PC', 'FT656DFG12', 'ASUS MB856', 'INTEL I3 GEN 8', '8 GB', '512 GB ', '10.10.10.67', 'AA-BB-CC-DD-EE-FF', 'JOHOR JAYA', 'DESMOND', 'Assigned', '2022-02-01', '2023-01-31', 'WIN 10, SSD'),
(2, 'FARHAN-LAPTOP', 'HP', 'GH6765RT2', 'PROLITE G312', 'INTEL I5 GEN 10', '12 GB', '512 GB', '10.10.10.68', '1A-2B-3C-4D-5E-6F', 'JOHOR JAYA', 'FARHAN', 'Assigned', '2024-04-03', '2025-04-02', 'WIN 11, SSD');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `action` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `username`, `action`, `timestamp`) VALUES
(1, 1, 'admin', 'User logged out', '2025-02-01 11:45:49'),
(2, 1, 'admin', 'Created Admin Account', '2025-02-01 11:46:15'),
(3, 1, 'admin', 'User logged in successfully', '2025-02-01 11:47:00'),
(4, 1, 'admin', 'Added a new computer: DESMOND-PC', '2025-02-01 11:52:19'),
(5, 1, 'admin', 'Added a new computer: FARHAN-LAPTOP', '2025-02-01 11:55:13'),
(6, 1, 'admin', 'Edited computer: FARHAN-LAPTOP', '2025-02-01 11:55:33'),
(7, 1, 'admin', 'Added a new computer: TEST-PC', '2025-02-01 11:59:59'),
(8, 1, 'admin', 'Deleted computer with ID: 3', '2025-02-01 12:00:03'),
(9, 1, 'admin', 'Added a new printer: SWP02-PRINTER', '2025-02-01 12:03:21'),
(10, 1, 'admin', 'Edited printer: SWP02-PTR', '2025-02-01 12:04:15'),
(11, 1, 'admin', 'Edited printer: SWP02-PTR', '2025-02-01 12:08:33'),
(12, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:26:14'),
(13, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:27:28'),
(14, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:31:04'),
(15, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:32:41'),
(16, 1, 'admin', 'Added a new tablet: DESMOND-TABLET', '2025-02-01 12:35:08'),
(17, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:35:11'),
(18, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:44:20'),
(19, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:44:30'),
(20, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:47:14'),
(21, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:50:39'),
(22, 1, 'admin', 'User logged in successfully', '2025-02-01 12:52:09'),
(23, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:52:09'),
(24, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:53:49'),
(25, 1, 'admin', 'User logged out', '2025-02-01 12:53:51'),
(26, 0, 'desmond', 'Failed login attempt', '2025-02-01 12:54:38'),
(27, 0, 'desmond', 'Failed login attempt', '2025-02-01 12:54:47'),
(28, 1, 'admin', 'User logged in successfully', '2025-02-01 12:54:57'),
(29, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:54:57'),
(30, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:55:14'),
(31, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:55:33'),
(32, 1, 'admin', 'Added a new phone: DESMOND-HP', '2025-02-01 12:57:37'),
(33, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:58:04'),
(34, 1, 'admin', 'User logged out', '2025-02-01 12:58:23'),
(35, 0, 'desmond', 'Failed login attempt', '2025-02-01 12:59:00'),
(36, 0, 'desmond', 'Failed login attempt', '2025-02-01 12:59:07'),
(37, 1, 'admin', 'User logged in successfully', '2025-02-01 12:59:15'),
(38, 1, 'admin', 'Accessed Dashboard', '2025-02-01 12:59:15'),
(39, 1, 'admin', 'Created new user: desmond', '2025-02-01 12:59:58'),
(40, 1, 'admin', 'User logged out', '2025-02-01 13:00:03'),
(41, 2, 'desmond', 'User logged in successfully', '2025-02-01 13:00:14'),
(42, 2, 'desmond', 'Accessed Dashboard', '2025-02-01 13:00:14'),
(43, 2, 'desmond', 'Accessed Dashboard', '2025-02-01 13:00:24'),
(44, 2, 'desmond', 'Added a new server: SW-SERVER', '2025-02-01 13:02:24'),
(45, 2, 'desmond', 'Accessed Dashboard', '2025-02-01 13:02:28'),
(46, 2, 'desmond', 'Updated their profile.', '2025-02-01 13:03:04'),
(47, 2, 'desmond', 'Accessed Dashboard', '2025-02-01 13:03:07'),
(48, 2, 'desmond', 'Accessed Dashboard', '2025-02-01 13:03:29'),
(49, 2, 'desmond', 'Reset password user ID: 2', '2025-02-01 13:03:40'),
(50, 2, 'desmond', 'Accessed Dashboard', '2025-02-01 13:03:44'),
(51, 2, 'desmond', 'Accessed Dashboard', '2025-02-01 13:04:13'),
(52, 2, 'desmond', 'User logged out', '2025-02-01 13:05:41'),
(53, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:05:46'),
(54, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:05:55'),
(55, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:06:07'),
(56, 0, 'admin', 'Failed login attempt', '2025-02-01 13:06:14'),
(57, 0, 'admin', 'Failed login attempt', '2025-02-01 13:06:24'),
(58, 0, 'admin', 'Failed login attempt', '2025-02-01 13:06:33'),
(59, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:06:43'),
(60, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:06:55'),
(61, 2, 'desmond', 'User logged in successfully', '2025-02-01 13:07:47'),
(62, 2, 'desmond', 'User logged out', '2025-02-01 13:08:10'),
(63, 1, 'admin', 'User logged in successfully', '2025-02-01 13:08:30'),
(64, 1, 'admin', 'Edited user: desmond', '2025-02-01 13:08:41'),
(65, 1, 'admin', 'User logged in successfully', '2025-02-01 13:11:20'),
(66, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:13:22'),
(67, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:13:30'),
(68, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:13:43'),
(69, 1, 'admin', 'User logged in successfully', '2025-02-01 13:13:51'),
(70, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:15:22'),
(71, 0, 'admin', 'Failed login attempt', '2025-02-01 13:15:31'),
(72, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:15:49'),
(73, 0, 'desmond', 'Failed login attempt', '2025-02-01 13:15:56'),
(74, 2, 'desmond', 'User logged in successfully', '2025-02-01 13:16:03'),
(75, 2, 'desmond', 'User logged in successfully', '2025-02-01 13:20:19'),
(76, 2, 'desmond', 'User logged in successfully', '2025-02-01 13:21:57'),
(77, 1, 'admin', 'User logged in successfully', '2025-02-03 05:44:27'),
(78, 1, 'admin', 'Added a new printer: SWP03-PTR', '2025-02-03 05:46:35'),
(79, 1, 'admin', 'Added a new printer: TPP01-PTR', '2025-02-03 05:48:05'),
(80, 1, 'admin', 'Edited printer: SWP03-PTR', '2025-02-03 05:48:15'),
(81, 1, 'admin', 'Added a new network equipment: HQ-CORE01', '2025-02-03 05:52:16'),
(82, 1, 'admin', 'User logged out', '2025-02-03 05:53:16'),
(83, 2, 'desmond', 'User logged in successfully', '2025-02-03 05:53:34'),
(84, 2, 'desmond', 'Changed their password.', '2025-02-03 05:53:54'),
(85, 2, 'desmond', 'User logged out', '2025-02-03 05:54:19'),
(86, 2, 'desmond', 'User logged in successfully', '2025-02-03 05:54:26'),
(87, 2, 'desmond', 'Added a new server: JJ-SERVER', '2025-02-03 06:01:25'),
(88, 2, 'desmond', 'Edited server: JJ-SERVER', '2025-02-03 06:01:48'),
(89, 2, 'desmond', 'Added a new accessory: HDMI-VGA CONVERTER-01', '2025-02-03 06:03:23'),
(90, 2, 'desmond', 'User logged out', '2025-02-03 06:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `network_equipment`
--

CREATE TABLE `network_equipment` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `processor` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `existing_user` varchar(100) DEFAULT NULL,
  `status` enum('Available','Assigned','Under Repair','Decommissioned') DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty` date DEFAULT NULL,
  `other_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `network_equipment`
--

INSERT INTO `network_equipment` (`id`, `device_name`, `brand`, `serial_number`, `model`, `processor`, `ram`, `storage`, `ip_address`, `mac_address`, `location`, `existing_user`, `status`, `purchase_date`, `warranty`, `other_details`) VALUES
(1, 'HQ-CORE01', 'CISCO', 'XFDH34GF2', 'CATALYST C3500S', '', '', '', '10.10.1.1', 'F0-D9-C8-B7-A6-E5', '', 'HQ', 'Assigned', '2025-02-03', '2026-02-01', '');

-- --------------------------------------------------------

--
-- Table structure for table `phones`
--

CREATE TABLE `phones` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `processor` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `existing_user` varchar(100) DEFAULT NULL,
  `status` enum('Available','Assigned','Under Repair','Decommissioned') DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty` date DEFAULT NULL,
  `other_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phones`
--

INSERT INTO `phones` (`id`, `device_name`, `brand`, `serial_number`, `model`, `processor`, `ram`, `storage`, `ip_address`, `mac_address`, `location`, `existing_user`, `status`, `purchase_date`, `warranty`, `other_details`) VALUES
(1, 'DESMOND-HP', 'XIOAMI', 'SDF54SDD2', 'MI 11 LITE 5G', 'SNAPDRAGON', '8 GB', '256 GB', '', '', 'JOHOR JAYA', 'DESMOND', 'Assigned', '2022-02-02', '2023-02-01', '');

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE `printers` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `processor` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `existing_user` varchar(100) DEFAULT NULL,
  `status` enum('Available','Assigned','Under Repair','Decommissioned') DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty` date DEFAULT NULL,
  `other_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `printers`
--

INSERT INTO `printers` (`id`, `device_name`, `brand`, `serial_number`, `model`, `processor`, `ram`, `storage`, `ip_address`, `mac_address`, `location`, `existing_user`, `status`, `purchase_date`, `warranty`, `other_details`) VALUES
(1, 'SWP02-PTR', 'ZYWELL', 'TEBGC67651', 'C-80', '', '', '', '', '', 'JOHOR JAYA', 'SWP02', 'Assigned', '2023-02-02', '2024-02-01', 'NEED REPLACE PRINT HEAD'),
(2, 'SWP03-PTR', 'ZYWELL', 'TEBGC67652', 'C-80', '', '', '', '', '', 'JOHOR JAYA', 'SWP03', 'Assigned', '2023-02-02', '2024-02-01', ''),
(3, 'TPP01-PTR', 'ZYWELL', 'TEBGC67653', 'C-80', '', '', '', '', '', 'TAMPOI', 'TPP01', 'Assigned', '2023-02-02', '2024-02-01', '');

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `processor` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `existing_user` varchar(100) DEFAULT NULL,
  `status` enum('Available','Assigned','Under Repair','Decommissioned') DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty` date DEFAULT NULL,
  `other_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `servers`
--

INSERT INTO `servers` (`id`, `device_name`, `brand`, `serial_number`, `model`, `processor`, `ram`, `storage`, `ip_address`, `mac_address`, `location`, `existing_user`, `status`, `purchase_date`, `warranty`, `other_details`) VALUES
(1, 'SW-SERVER', 'DELL', 'FDF76EFSW', 'POWEREDGE T30', 'INTEL XEON N2566', '32 GB', '2 TB', '10.10.4.1', 'DD-D4-34-E3-FF-9C', 'JOHOR JAYA', '', 'Assigned', '2023-02-02', '2024-02-01', ''),
(2, 'JJ-SERVER', 'HP', 'WEA34F5GS', 'PROLIANT 380 G9', 'NTEL XEON N3568', '32 GB', '2 TB', '10.10.4.6', '3D-54-34-E3-FF-9F', 'JOHOR JAYA', '', 'Assigned', '2020-02-03', '2022-02-02', '');

-- --------------------------------------------------------

--
-- Table structure for table `tablets`
--

CREATE TABLE `tablets` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `processor` varchar(50) DEFAULT NULL,
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `mac_address` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `existing_user` varchar(100) DEFAULT NULL,
  `status` enum('Available','Assigned','Under Repair','Decommissioned') DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty` date DEFAULT NULL,
  `other_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tablets`
--

INSERT INTO `tablets` (`id`, `device_name`, `brand`, `serial_number`, `model`, `processor`, `ram`, `storage`, `ip_address`, `mac_address`, `location`, `existing_user`, `status`, `purchase_date`, `warranty`, `other_details`) VALUES
(1, 'DESMOND-TABLET', 'SAMSUNG', 'SDW234SF1Q13', 'TAB 8 PRO', 'SNAPDRAGON', '8 GB', '256 GB', '', '', '', 'DESMOND', 'Assigned', '2023-02-02', '2024-02-01', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `access_permission` enum('view','add','edit','admin') DEFAULT 'view',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `staff_id`, `contact_number`, `access_permission`, `created_at`) VALUES
(1, 'admin', '$2y$10$FcBj/CAKXAmTvEyOUpHOM.yPeDMO/spK3ZfVjGJFpNCwKDrC/D4QW', 'admin@email.my', 'admin01', '00000000', 'admin', '2025-02-01 11:46:15'),
(2, 'desmond', '$2y$10$YqjZxOm4E/j2BgB7V0NcRO3liMccaNQSrIHtMuqPR7/vrfrYgITy.', 'desmond@email.com', 'desmond002', '016-7100709', 'admin', '2025-02-01 12:59:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accessories`
--
ALTER TABLE `accessories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `computers`
--
ALTER TABLE `computers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `network_equipment`
--
ALTER TABLE `network_equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `phones`
--
ALTER TABLE `phones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `printers`
--
ALTER TABLE `printers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tablets`
--
ALTER TABLE `tablets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `staff_id` (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accessories`
--
ALTER TABLE `accessories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `computers`
--
ALTER TABLE `computers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `network_equipment`
--
ALTER TABLE `network_equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `phones`
--
ALTER TABLE `phones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `printers`
--
ALTER TABLE `printers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tablets`
--
ALTER TABLE `tablets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
