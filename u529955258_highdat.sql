-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 18, 2025 at 07:13 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u529955258_highdat`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'The user who performed the action',
  `action` varchar(100) NOT NULL COMMENT 'e.g., service_assigned, password_changed',
  `details` text DEFAULT NULL COMMENT 'A description of the action',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(3, 43, 'service_assigned', 'Service (Order ID: #21) assigned to user \'Goldvps\' by Seller (ID: 43).', '2025-09-11 04:50:48'),
(4, 113, 'service_assigned', 'Service (Order ID: #59) assigned to user \'shahjahan786\' (ID: 116).', '2025-10-06 17:29:26'),
(5, 119, 'service_assigned', 'Service (Order ID: #62) assigned to user \'PRAMODVPS\' by Seller (ID: 119).', '2025-10-07 13:01:50'),
(6, 123, 'service_assigned', 'Service (Order ID: #73) assigned to user \'Guddu1040\' by Seller (ID: 123).', '2025-10-12 04:14:35');

-- --------------------------------------------------------

--
-- Table structure for table `deposit_requests`
--

CREATE TABLE `deposit_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `utr_number` varchar(50) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposit_requests`
--

INSERT INTO `deposit_requests` (`id`, `user_id`, `amount`, `utr_number`, `status`, `created_at`, `updated_at`) VALUES
(5, 43, 2500.00, '525349553616', 'approved', '2025-09-10 03:14:23', '2025-09-10 04:24:52'),
(6, 77, 500.00, '123456789555', 'rejected', '2025-09-12 19:12:03', '2025-09-13 02:12:14'),
(7, 78, 1600.00, '143632934223', 'approved', '2025-09-13 02:10:01', '2025-09-13 02:12:23'),
(8, 62, 1400.00, '525676440102', 'approved', '2025-09-13 16:16:47', '2025-09-13 16:17:57'),
(9, 85, 1200.00, '253585553588525925', 'rejected', '2025-09-14 23:17:51', '2025-09-15 03:55:41'),
(10, 90, 1400.00, '861902926112', 'approved', '2025-09-21 16:16:38', '2025-09-21 16:30:43'),
(11, 88, 900.00, '137725941453', 'approved', '2025-09-22 16:04:42', '2025-09-23 00:38:47'),
(12, 70, 1000.00, '986832733177', 'approved', '2025-09-23 01:55:18', '2025-09-23 01:57:01'),
(13, 98, 900.00, '673814649919', 'approved', '2025-09-24 06:16:23', '2025-09-24 06:22:33'),
(14, 112, 1300.00, '564183981860', 'approved', '2025-10-02 07:28:41', '2025-10-02 07:28:51'),
(15, 113, 1200.00, '704355936553', 'approved', '2025-10-03 06:36:07', '2025-10-03 06:36:29'),
(16, 115, 1300.00, '972797930150', 'approved', '2025-10-05 18:34:50', '2025-10-05 23:13:32'),
(17, 90, 650.00, '475262549295', 'approved', '2025-10-06 01:49:40', '2025-10-06 01:49:48'),
(18, 113, 1200.00, '876810297776', 'approved', '2025-10-06 16:55:08', '2025-10-06 16:56:59'),
(19, 44, 1300.00, '390362727265', 'approved', '2025-10-07 03:36:13', '2025-10-07 03:36:32'),
(20, 114, 1000.00, '015835351730', 'approved', '2025-10-07 07:45:01', '2025-10-07 07:45:08'),
(21, 119, 1300.00, '857416049474', 'approved', '2025-10-07 10:52:37', '2025-10-07 10:53:57'),
(22, 46, 651.00, '528093596284', 'approved', '2025-10-07 17:58:03', '2025-10-07 17:58:15'),
(23, 90, 1200.00, '074697356685', 'approved', '2025-10-09 03:26:34', '2025-10-09 03:28:40'),
(24, 126, 650.00, '643851512275', 'approved', '2025-10-10 07:00:15', '2025-10-10 07:01:06'),
(25, 62, 1900.00, '528421197150', 'approved', '2025-10-11 13:15:28', '2025-10-11 13:17:35'),
(26, 131, 750.00, '610748121905', 'approved', '2025-10-11 15:15:18', '2025-10-11 15:15:28'),
(27, 123, 1300.00, '044007894735', 'approved', '2025-10-12 03:36:38', '2025-10-12 03:38:41'),
(28, 134, 2050.00, '991957434297', 'approved', '2025-10-12 07:02:48', '2025-10-12 07:04:29'),
(29, 129, 1300.00, '684633404539', 'approved', '2025-10-12 12:19:44', '2025-10-12 12:19:58'),
(30, 62, 1900.00, '528645582951', 'approved', '2025-10-12 19:03:10', '2025-10-12 23:06:28'),
(31, 62, 250.00, '528652963230', 'approved', '2025-10-13 04:06:45', '2025-10-13 04:08:03'),
(32, 90, 2350.00, '558222511416', 'approved', '2025-10-14 14:09:21', '2025-10-14 14:09:57'),
(33, 138, 1400.00, '528801392455', 'approved', '2025-10-14 20:01:30', '2025-10-14 22:44:08'),
(34, 83, 2600.00, '826736907745', 'approved', '2025-10-15 00:39:49', '2025-10-15 00:54:30'),
(35, 119, 1300.00, '281017289841', 'approved', '2025-10-15 04:02:54', '2025-10-15 04:04:44'),
(36, 72, 1300.00, '089873091296', 'approved', '2025-10-15 04:58:45', '2025-10-15 05:03:29'),
(37, 126, 550.00, '790419561817', 'approved', '2025-10-15 08:15:40', '2025-10-15 08:16:34'),
(38, 141, 750.00, '528977518863', 'approved', '2025-10-16 02:13:55', '2025-10-16 02:14:21'),
(39, 119, 1300.00, '568628560975', 'approved', '2025-10-16 10:50:02', '2025-10-16 10:50:44');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `custom_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `vps_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` enum('pending','active','canceled') NOT NULL DEFAULT 'pending',
  `expires_at` datetime DEFAULT NULL,
  `assigned_to_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `amount`, `custom_options`, `vps_details`, `status`, `expires_at`, `assigned_to_id`, `order_date`) VALUES
(21, 61, 18, 2500.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.157 - MUM DC [OUT OF STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"103.157.50.169\",\"password\":\"Lhjnj4578r##r58gbht@!#Bh49kffgr\",\"vps_id\":\"\"}', 'active', '2025-10-10 03:20:03', 61, '2025-09-10 03:20:03'),
(22, 78, 14, 1600.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.215 - MUM DC [IN STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"103.215.96.162\",\"password\":\"@#$HHGTAGTjhg96bnghfgvgtrfg\",\"vps_id\":\"\"}', 'active', '2025-10-13 02:12:56', NULL, '2025-09-13 02:12:56'),
(23, 62, 14, 1400.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.215 - MUM DC [IN STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"103.215.96.162\",\"password\":\"Lhjny85674##r896gbht@!#Bh4hhj22g\",\"vps_id\":\"\"}', 'active', '2025-10-13 16:20:30', NULL, '2025-09-13 16:20:30'),
(24, 77, 11, 1900.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"103.151 [IN STOCK] [NEW LAUNCH]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.72.102.105\",\"password\":\"2z5mv1da1dwkva\",\"vps_id\":\"4443\"}', 'active', '2025-10-13 19:36:01', NULL, '2025-09-13 19:36:01'),
(25, 90, 14, 1400.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.157 - MUM DC [IN  STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"103.215.96.95\",\"password\":\"Lhjny6756r##r66gbht@!#Bh49rafgt\",\"vps_id\":\"\"}', 'active', '2025-10-22 00:34:12', NULL, '2025-09-22 00:34:12'),
(26, 88, 12, 900.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.181 - LKO DC [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.177.227.159\",\"password\":\"Lhjny4125r##r43gbht@!#Bh49rafgt\",\"vps_id\":\"\"}', 'active', '2025-10-23 00:54:44', NULL, '2025-09-23 00:54:44'),
(27, 70, 12, 1000.00, '{\"OS\":\"Ubuntu 18 64\",\"IP Series\":\"103.187 - NOIDA DC [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.187.93.208\",\"password\":\"Liirf6789r##r55gbht@!#Bh49gfgy\",\"vps_id\":\"\"}', 'active', '2025-10-23 02:02:12', NULL, '2025-09-23 02:02:12'),
(28, 98, 3, 900.00, '{\"OS\":\"Windows 10 Pro\",\"IP Series\":\"163.61 [LIMITED STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"163.61.30.239\",\"password\":\"4^dx-j*8vNN@P29N\",\"vps_id\":\"\"}', 'active', '2025-10-24 06:23:06', NULL, '2025-09-24 06:23:06'),
(51, 112, 10, 1300.00, '{\"OS\":\"Windows 2025 64\",\"IP Series\":\"103.151 [IN STOCK] [NEW LAUNCH]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.49.131.111\",\"password\":\"efuwni4POxijw\",\"vps_id\":\"\"}', 'active', '2025-11-02 07:29:11', NULL, '2025-10-02 07:29:11'),
(52, 43, 9, 750.00, '{\"OS\":\"Ubuntu 18 64\",\"IP Series\":\"103.151 [IN STOCK] [NEW LAUNCH]\"}', NULL, 'pending', '2025-11-02 07:29:21', NULL, '2025-10-02 07:29:21'),
(53, 43, 14, 1500.00, '[]', NULL, 'pending', '2025-11-02 09:57:25', NULL, '2025-10-02 09:57:25'),
(54, 109, 1, 500.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.182 [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.182.103.47\",\"password\":\"3K3J*y8tm&amp;FkU6(_\",\"vps_id\":\"\"}', 'active', '2025-11-02 15:47:49', NULL, '2025-10-02 15:47:49'),
(55, 113, 10, 1200.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.49 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.49.131.124\",\"password\":\"Llhmk5845r##r43gbht@!#Bh49kjmft\",\"vps_id\":\"\"}', 'active', '2025-11-03 06:37:07', NULL, '2025-10-03 06:37:07'),
(57, 90, 9, 650.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.49 [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.49.131.86\",\"password\":\"8b6r67y0aotrhi\",\"vps_id\":\"\"}', 'active', '2025-11-06 01:50:16', NULL, '2025-10-06 01:50:16'),
(58, 115, 10, 1300.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.49 [IN STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"103.49.131.82\",\"password\":\"Lhjny4125r##r43gbht@!#Bh49fdrt\",\"vps_id\":\"\"}', 'active', '2025-11-06 02:12:11', NULL, '2025-10-06 02:12:11'),
(59, 116, 10, 1200.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.49 [IN STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"103.49.131.141\",\"password\":\"Lhjny4125r##r43gbht@!#Bh49fdrt\",\"vps_id\":\"\"}', 'active', '2025-11-06 16:57:04', 116, '2025-10-06 16:57:04'),
(60, 44, 10, 1300.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.49 [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.49.131.143\",\"password\":\"Pw!mfrwkm8e8l\",\"vps_id\":\"\"}', 'active', '2025-11-07 03:37:04', NULL, '2025-10-07 03:37:04'),
(61, 114, 12, 1000.00, '{\"OS\":\"Ubuntu 24 64\",\"IP Series\":\"103.187 - NOIDA DC [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.187.93.110\",\"password\":\"Pw!9z1yxg6qnb\",\"vps_id\":\"\"}', 'active', '2025-11-07 07:47:45', NULL, '2025-10-07 07:47:45'),
(62, 120, 10, 1300.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"163.5 [IN STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"163.5.191.229\",\"password\":\"Pw!l0udtotwi4\",\"vps_id\":\"\"}', 'active', '2025-11-07 10:54:36', 120, '2025-10-07 10:54:36'),
(63, 109, 1, 500.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"163.223 [LIMITED STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"163.223.52.253\",\"password\":\"L98un2(yD5T+B_y(\",\"vps_id\":\"\"}', 'active', '2025-11-07 13:47:57', NULL, '2025-10-07 13:47:57'),
(64, 46, 9, 651.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.49 [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.49.131.158\",\"password\":\"lbrgo7l754v7sz\",\"vps_id\":\"\"}', 'active', '2025-11-07 18:00:39', NULL, '2025-10-07 18:00:39'),
(65, 109, 1, 500.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.182 [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.182.102.91\",\"password\":\"a9LxL3)W^X@5*j0z\",\"vps_id\":\"\"}', 'active', '2025-11-08 17:17:24', NULL, '2025-10-08 17:17:24'),
(66, 109, 1, 500.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"160.191 [LIMITED STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"160.191.28.41\",\"password\":\"3c6T1*@Y3D&amp;Jcei(\",\"vps_id\":\"\"}', 'active', '2025-11-08 17:18:17', NULL, '2025-10-08 17:18:17'),
(67, 109, 1, 500.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.182 [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.182.102.120\",\"password\":\"+1LN(_3&amp;tznYk9P0\",\"vps_id\":\"\"}', 'active', '2025-11-08 18:26:32', NULL, '2025-10-08 18:26:32'),
(68, 90, 10, 1200.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.215\",\"password\":\"Lhjny4125r##r43gbht@!#Bh49fdrt\",\"vps_id\":\"\"}', 'active', '2025-11-09 03:29:55', NULL, '2025-10-09 03:29:55'),
(69, 109, 12, 800.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"165.99 - NOIDA DC [IN STOCK]\"}', NULL, 'pending', '2025-11-10 12:47:49', NULL, '2025-10-10 12:47:49'),
(70, 43, 9, 750.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', NULL, 'pending', '2025-11-11 08:26:32', NULL, '2025-10-11 08:26:32'),
(71, 62, 11, 1900.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.49 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.160\",\"password\":\"Lhjny4125r##r43gbht@!#Bh49rafgt\",\"vps_id\":\"\"}', 'active', '2025-11-11 13:19:44', NULL, '2025-10-11 13:19:44'),
(72, 131, 9, 750.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.72.102.74\",\"password\":\"3d641ztnpbpnhu\",\"vps_id\":\"\"}', 'active', '2025-11-11 15:16:46', NULL, '2025-10-11 15:16:46'),
(73, 133, 10, 1300.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"103.72.102.115\",\"password\":\"Pw!7sryrwb11m\",\"vps_id\":\"\"}', 'active', '2025-11-12 03:39:45', 133, '2025-10-12 03:39:45'),
(74, 134, 11, 2050.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.103\",\"password\":\"Liirf7886r##r55gbht@!#Bh49gfgy\",\"vps_id\":\"\"}', 'active', '2025-11-12 07:04:48', NULL, '2025-10-12 07:04:48'),
(75, 129, 10, 1300.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"administrator\",\"ip_address\":\"103.72.102.107\",\"password\":\"Pw!r3v7b2yf08\",\"vps_id\":\"\"}', 'active', '2025-11-12 12:22:36', NULL, '2025-10-12 12:22:36'),
(76, 62, 4, 1350.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.182 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.182.102.100\",\"password\":\"7uX)J6_bb5J)-Q3l\",\"vps_id\":\"\"}', 'active', '2025-11-13 03:07:52', NULL, '2025-10-13 03:07:52'),
(77, 62, 12, 800.00, '{\"OS\":\"Ubuntu 22 64\",\"IP Series\":\"103.183 - NOIDA DC [IN STOCK]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.183.217.122\",\"password\":\"@#$HNJMAGTjhg69bnghfgvgtrfg\",\"vps_id\":\"\"}', 'active', '2025-11-13 04:14:30', NULL, '2025-10-13 04:14:30'),
(78, 109, 10, 1200.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.211\",\"password\":\"Lhjny587368##r423ghgt@!#Bh4ttre22g\",\"vps_id\":\"\"}', 'active', '2025-11-14 07:13:10', NULL, '2025-10-14 07:13:10'),
(79, 90, 18, 2350.00, '[]', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.214.231.148\",\"password\":\"Lhjny4125r##r43gbht@!#Bh49fdrt\",\"vps_id\":\"\"}', 'active', '2025-11-14 14:11:37', NULL, '2025-10-14 14:11:37'),
(80, 83, 10, 1300.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.100\",\"password\":\"ytjyzrzit24Lo@\",\"vps_id\":\"\"}', 'active', '2025-11-15 01:17:39', NULL, '2025-10-15 01:17:39'),
(81, 83, 10, 1300.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.234\",\"password\":\"ytjyzrzi4tMi@\",\"vps_id\":\"\"}', 'active', '2025-11-15 01:18:42', NULL, '2025-10-15 01:18:42'),
(82, 138, 10, 1400.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"64.50 [IN STOCK] [NEW LAUNCH]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.220\",\"password\":\"Lhjny5785##r558gbht@!#Bh4hhj22g\",\"vps_id\":\"\"}', 'active', '2025-11-15 04:02:50', NULL, '2025-10-15 04:02:50'),
(83, 119, 10, 1300.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"64.50 [IN STOCK] [NEW LAUNCH]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.216\",\"password\":\"@#$HNJMAGTjhg67bnghfgvgtrfg\",\"vps_id\":\"\"}', 'active', '2025-11-15 04:05:59', NULL, '2025-10-15 04:05:59'),
(84, 72, 10, 1300.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.213\",\"password\":\"@#$HNJMAGTjhg67bnghfgvgtrfg\",\"vps_id\":\"\"}', 'active', '2025-11-15 05:04:13', NULL, '2025-10-15 05:04:13'),
(85, 109, 10, 1200.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.225\",\"password\":\"@#$HNJMAGTjhg99bnghfgvgtrfg\",\"vps_id\":\"\"}', 'active', '2025-11-15 06:07:54', NULL, '2025-10-15 06:07:54'),
(86, 126, 10, 1200.00, '{\"OS\":\"Windows 2022 64\",\"IP Series\":\"103.72 [IN STOCK]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.72.102.224\",\"password\":\"Pw!gxiekaoqra\",\"vps_id\":\"\"}', 'active', '2025-11-15 08:17:17', NULL, '2025-10-15 08:17:17'),
(87, 141, 9, 750.00, '{\"OS\":\"Ubuntu 18 64\",\"IP Series\":\"64.50 [IN STOCK] [NEW LAUNCH]\"}', '{\"hostname\":\"root\",\"ip_address\":\"103.115.19.126\",\"password\":\"Pw!ir3pbfnr9r\",\"vps_id\":\"\"}', 'active', '2025-11-16 02:14:42', NULL, '2025-10-16 02:14:42'),
(88, 119, 10, 1300.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"103.15 [IN STOCK] [NEW LAUNCH]\"}', '{\"hostname\":\"Administrator\",\"ip_address\":\"103.115.19.113\",\"password\":\"vnuxqAPd@wf6\",\"vps_id\":\"\"}', 'active', '2025-11-16 10:58:20', NULL, '2025-10-16 10:58:20'),
(89, 77, 10, 1200.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"103.15 [IN STOCK] [NEW LAUNCH]\"}', '{\"vps_id\":4309}', 'active', '2025-11-16 20:20:53', NULL, '2025-10-16 20:20:16'),
(90, 77, 10, 1200.00, '{\"OS\":\"Windows 2019 64\",\"IP Series\":\"103.15 [IN STOCK] [NEW LAUNCH]\"}', NULL, 'pending', '2025-11-18 19:12:21', NULL, '2025-10-18 19:12:21');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `server_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `price_super_seller` decimal(10,2) NOT NULL,
  `price_seller` decimal(10,2) NOT NULL,
  `price_user` decimal(10,2) NOT NULL,
  `custom_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `server_id`, `name`, `description`, `stock`, `price_super_seller`, `price_seller`, `price_user`, `custom_fields`, `status`, `created_at`) VALUES
(1, 1, NULL, 'Super Sonic 2 Core 4 GB Linux', '✅ INTEL XEON 2.4GHz\r\n✅ 2 CORE\r\n✅ 4 GB RAM\r\n✅ 40 GB NVMe SSD\r\n✅ 2000 GB Bandwidth\r\n✅ 1GBPS PORT\r\n✅ 0-1 Ping\r\n✅ Full Root Access\r\n✅ 1 IPv4 Dedicated IP\r\n✅ Custom IP Series &amp; Pool Choice\r\n✅ Location Mumbai\r\n✅ No Refunds/Replace', 90, 500.00, 600.00, 700.00, '{\r\n  \"OS\": [\r\n    \"Ubuntu 18 64\", \r\n     \"Ubuntu 20 64\",\r\n     \"Ubuntu 22 64\",\r\n     \"Ubuntu 24 64\",\r\n     \"CentOS 7 64\",\r\n      \"CentOS 8 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.181 [IN STOCK]\",\r\n\"103.182 [IN STOCK]\",\r\n\"103.146 [IN STOCK]\",\r\n\"103.159 [IN STOCK]\",\r\n\"103.243 [IN STOCK]\",\r\n\"160.191 [LIMITED STOCK]\",\r\n\"163.227 [OUT OF STOCK]\",\r\n\"163.61 [OUT OF STOCK]\",\r\n\"163.223 [LIMITED STOCK]\"\r\n  ]\r\n}', 'active', '2025-09-02 08:44:32'),
(3, 1, NULL, 'Super Sonic 4Core 8GB WINDOWS VPS', '✅ AMD EPYC 2.6GHz Processor\r\n✅ 4 CORE\r\n✅ 8 GB RAM\r\n✅ 80 GB NVMe SSD\r\n✅ 2000 GB Bandwidth\r\n✅ 10 GBPS PORT\r\n✅ 0 Ping\r\n✅ Full Administrator Access\r\n✅ 1 IPv4 Dedicated IP\r\n✅ Custom IP Series &amp; Pool Choice\r\n✅ Location Mumbai\r\n✅ Android Emulator Working\r\n✅ No Refunds/Replace', 23, 900.00, 1000.00, 1100.00, '{\r\n  \"OS\": [\r\n    \"Ubuntu 18 64\", \r\n     \"Ubuntu 20 64\",\r\n     \"Ubuntu 22 64\",\r\n     \"Ubuntu 24 64\",\r\n     \"CentOS 7 64\",\r\n      \"CentOS 8 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.181 [IN STOCK]\",\r\n\"103.182 [IN STOCK]\",\r\n\"103.146 [IN STOCK]\",\r\n\"103.159 [IN STOCK]\",\r\n\"103.243 [IN STOCK]\",\r\n\"160.191 [LIMITED STOCK]\",\r\n\"163.227 [OUT OF STOCK]\",\r\n\"163.61 [OUT OF STOCK]\",\r\n\"163.223 [LIMITED STOCK]\"\r\n  ]\r\n}', 'active', '2025-09-02 19:24:42'),
(4, 1, NULL, 'Super Sonic 8 Core 16 GB WINDOWS VPS', '✅ AMD EPYC 2.60 GHz\r\n✅ 8 vCPUs Frequency\r\n✅ 16 GB RAM DDR4\r\n✅ 120 GB NVMe\r\n✅ 4000 GB Bandwidth \r\n✅ 10 GBPS PORT\r\n✅ 0 Ping\r\n✅ Full Administrator Access\r\n✅ 1 IPv4 Dedicated IP\r\n✅ Custom IP Series &amp; Pool Choice\r\n✅ Location Mumbai\r\n✅ Android Emulator Working\r\n✅ No Refunds/Replace', 99, 1350.00, 1500.00, 1700.00, '{\r\n  \"OS\": [\r\n    \"Ubuntu 18 64\", \r\n     \"Ubuntu 20 64\",\r\n     \"Ubuntu 22 64\",\r\n     \"Ubuntu 24 64\",\r\n     \"CentOS 7 64\",\r\n      \"CentOS 8 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.181 [IN STOCK]\",\r\n\"103.182 [IN STOCK]\",\r\n\"103.146 [IN STOCK]\",\r\n\"103.159 [IN STOCK]\",\r\n\"103.243 [IN STOCK]\",\r\n\"160.191 [LIMITED STOCK]\",\r\n\"163.227 [OUT OF STOCK]\",\r\n\"163.61 [OUT OF STOCK]\",\r\n\"163.223 [LIMITED STOCK]\"\r\n  ]\r\n}', 'active', '2025-09-08 02:08:10'),
(5, 1, NULL, 'Super Sonic 12 Core 24 GB WINDOWS VPS', '✅ AMD EPYC 2.60 GHz\r\n✅ 12 vCPUs Frequency\r\n✅ 24 GB RAM DDR4\r\n✅ 180 GB NVMe\r\n✅ 4000GB Bandwidth\r\n✅ 10 GBPS PORT\r\n✅ 0 Ping\r\n✅ Full Administrator Access\r\n✅ 1 IPv4 Dedicated IP\r\n✅ Custom IP Series &amp; Pool Choice\r\n✅ Location Mumbai\r\n✅ Android Emulator Working\r\n✅ No Refunds/Replace', 5, 1850.00, 2000.00, 2200.00, '{\r\n  \"OS\": [\r\n    \"Ubuntu 18 64\", \r\n     \"Ubuntu 20 64\",\r\n     \"Ubuntu 22 64\",\r\n     \"Ubuntu 24 64\",\r\n     \"CentOS 7 64\",\r\n      \"CentOS 8 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.181 [IN STOCK]\",\r\n\"103.182 [IN STOCK]\",\r\n\"103.146 [IN STOCK]\",\r\n\"103.159 [IN STOCK]\",\r\n\"103.243 [IN STOCK]\",\r\n\"160.191 [LIMITED STOCK]\",\r\n\"163.227 [OUT OF STOCK]\",\r\n\"163.61 [OUT OF STOCK]\",\r\n\"163.223 [LIMITED STOCK]\"\r\n  ]\r\n}', 'active', '2025-08-30 17:34:07'),
(6, 1, NULL, 'Super Sonic 16 Core 32 GB WINDOWS VPS', '✅ AMD EPYC 2.60 GHz\r\n✅ 16 vCPUs Frequency\r\n✅ 32 GB RAM DDR4\r\n✅ 240 GB NVMe\r\n✅ 8000 GB Bandwidth\r\n✅ 10 GBPS PORT\r\n✅ 0 Ping\r\n✅ Full Administrator Access\r\n✅ 1 IPv4 Dedicated IP\r\n✅ Custom IP Series &amp; Pool Choice\r\n✅ Location Mumbai\r\n✅ Android Emulator Working\r\n✅ No Refunds/Replace', 100, 2350.00, 2500.00, 2700.00, '{\r\n  \"OS\": [\r\n    \"Ubuntu 18 64\", \r\n     \"Ubuntu 20 64\",\r\n     \"Ubuntu 22 64\",\r\n     \"Ubuntu 24 64\",\r\n     \"CentOS 7 64\",\r\n      \"CentOS 8 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.181 [IN STOCK]\",\r\n\"103.182 [IN STOCK]\",\r\n\"103.146 [IN STOCK]\",\r\n\"103.159 [IN STOCK]\",\r\n\"103.243 [IN STOCK]\",\r\n\"160.191 [LIMITED STOCK]\",\r\n\"163.227 [OUT OF STOCK]\",\r\n\"163.61 [OUT OF STOCK]\",\r\n\"163.223 [LIMITED STOCK]\"\r\n  ]\r\n}', 'active', '2025-09-08 14:38:51'),
(9, 2, NULL, 'Hyper Sonic 2 Core  4 GB Linux', '✅ Intel Xeon\r\n      2Core\r\n✅ 4 GB DDR4\r\n✅ 40 GB SSD\r\n✅ Bandwidth Unlimited\r\n✅ 1 Dedicated IPv4\r\n✅ IPv6 ROTATING\r\n✅ Location NOIDA\r\n✅ Uptime 99.95%', 91, 650.00, 750.00, 850.00, '{\r\n  \"OS\": [\r\n    \"Ubuntu 18 64\", \r\n     \"Ubuntu 20 64\",\r\n     \"Ubuntu 22 64\",\r\n     \"Ubuntu 24 64\",\r\n     \"CentOS 7 64\",\r\n      \"CentOS 8 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.15 [IN STOCK] [NEW LAUNCH]\", \"103.49 [IN STOCK]\", \"103.72 [IN STOCK]\", \"163.5 [IN STOCK]\"\r\n  ]\r\n}', 'active', '2025-09-08 14:42:47'),
(10, 2, 8, 'Hyper Sonic 4 Core 8 GB Windows VPS', '✅ Intel Xeon/Gold, 4 vCore\r\n✅ 8 GB DDR4\r\n✅ 80 GB SSD\r\n✅ Bandwidth Unlimited\r\n✅ 1 IPv4 Dedicated IP\r\n✅ IPv6 ROTATING\r\n✅ Location NOIDA\r\n✅ Uptime 99.95%', 30, 1200.00, 1300.00, 1400.00, '{\r\n  \"OS\": [\r\n    \"Windows 2019 64\", \"Windows 2022 64\", \"Windows 2025 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.15 [IN STOCK] [NEW LAUNCH]\", \"103.49 [IN STOCK]\", \"103.72 [IN STOCK]\", \"163.5 [IN STOCK]\"\r\n  ]\r\n}', 'active', '2025-09-08 14:49:32'),
(11, 2, NULL, 'Hyper Sonic 8 Core 16 GB Windows VPS', '✅ Intel Xeon/Gold, 8 vCore\r\n✅ 16 GB DDR4 RAM\r\n✅ 160 GB SSD\r\n✅ Bandwidth Unlimited\r\n✅ 1 IPv4 Dedicated IP\r\n✅ IPv6 ROTATING\r\n✅ Location NOIDA\r\n✅ Uptime 99.95%', 97, 1900.00, 2050.00, 2250.00, '{\r\n  \"OS\": [\r\n    \"Windows 2019 64\", \"Windows 2022 64\", \"Windows 2025 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.15 [IN STOCK] [NEW LAUNCH]\", \"103.49 [IN STOCK]\", \"103.72 [IN STOCK]\", \"163.5 [IN STOCK]\"\r\n  ]\r\n}', 'active', '2025-09-08 14:51:44'),
(12, 3, NULL, 'Ultra Sonic 2 Core 4 GB Linux', '✅ Intel CPU\r\n✅ 2 CORE\r\n✅ 4 GB RAM\r\n✅ 40 GB NVMe SSD\r\n✅ Unlimited Bandwidth\r\n✅ 1GBPS PORT\r\n✅ 0-1 Ping\r\n✅ Full ROOT Access\r\n✅ OS Ubuntu\r\n✅ 1 Dedicated IPv4 Free\r\n✅ Rotation IPv6\r\n✅ Custom IP Series &amp; Pool Choice\r\n✅ Location India', 95, 800.00, 900.00, 1000.00, '{\r\n  \"OS\": [\r\n    \"Ubuntu 18 64\", \r\n     \"Ubuntu 20 64\",\r\n     \"Ubuntu 22 64\",\r\n     \"Ubuntu 24 64\",\r\n     \"CentOS 7 64\",\r\n      \"CentOS 8 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.177 - LKO DC [IN STOCK]\", \"103.181 - LKO DC [IN STOCK]\", \"103.189 - LKO DC [IN STOCK]\", \"103.179 - LKO DC [IN STOCK]\", \"103.179 - NOIDA DC [IN STOCK]\", \"103.183 - NOIDA DC [IN STOCK]\", \"103.187 - NOIDA DC [IN STOCK]\", \"165.99 - NOIDA DC [IN STOCK]\"\r\n  ]\r\n}', 'active', '2025-09-08 14:54:21'),
(14, 3, NULL, 'Ultra Sonic 4 Core 8 GB Windows VPS', '✅ Intel Gold 3.1GHz Turbo 4.0 GHz CPU\r\n✅ 4 Core\r\n✅ 8 GB RAM RAM DDR5 ECC 4800MHz\r\n✅ 100 GB NVMe SSD\r\n✅ Unlimited Bandwidth\r\n✅ 1-2 Gbps+ PORT\r\n✅ OS Windows 2022/2019\r\n✅ Full Administrator Access\r\n✅ 1 Dedicated IPv4 Free\r\n✅ Rotation IPv6\r\n✅ Custom IP Series &amp; Pool Choice\r\n✅ Location NOIDA/MUMBAI/LUCKNOW-India', 94, 1400.00, 1500.00, 1600.00, '{\r\n  \"OS\": [\r\n    \"Windows 2022 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.214 - MUM DC [IN STOCK]\", \"103.215 - MUM DC [IN STOCK]\", \"103.161 - MUM DC [IN  STOCK]\", \"103.168 - MUM DC [IN STOCK]\",\"103.179 - MUM DC [IN STOCK]\"\r\n  ]\r\n}\r\n', 'active', '2025-09-08 14:58:24'),
(18, 3, NULL, 'Ultra Sonic 8 Core 16 GB Windows VPS', '✅ Intel Gold 3.1GHz Turbo 4.0 GHz CPU\r\n✅ 8 Core\r\n✅ 16 GB RAM RAM DDR5 ECC 4800MHz\r\n✅ 200 GB NVMe SSD\r\n✅ Unlimited Bandwidth\r\n✅ 1-2 Gbps+ PORT\r\n✅ OS Windows 2022/2019\r\n✅ Full Administrator Access\r\n✅ 1 Dedicated IPv4 Free\r\n✅ Rotation IPv6\r\n✅ Custom IP Series &amp;amp; Pool Choice\r\n✅ Location NOIDA/MUMBAI/LUCKNOW-India', 68, 2350.00, 2500.00, 2700.00, '{\r\n  \"OS\": [\r\n    \"Windows 2022 64\"\r\n  ],\r\n  \"IP Series\": [\r\n    \"103.214 - MUM DC [IN STOCK]\", \"103.215 - MUM DC [IN STOCK]\", \"103.161 - MUM DC [IN  STOCK]\", \"103.168 - MUM DC [IN STOCK]\",\"103.179 - MUM DC [IN STOCK]\"\r\n  ]\r\n}\r\n', 'active', '2025-09-08 14:58:24');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `status`, `created_at`) VALUES
(1, 'Super Sonic Server [Non Rotating IP]', 'active', '2025-08-26 18:19:47'),
(2, 'Hyper Sonic Server [Rotating IP]', 'active', '2025-08-26 18:20:02'),
(3, 'Ultra Sonic  Server [Rotating IP]', 'active', '2025-09-08 14:22:20');

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'A friendly name, e.g., "HostDzire - India"',
  `panel_type` enum('virtualizor','hostdzire','autovm') NOT NULL,
  `hostname` varchar(255) NOT NULL COMMENT 'IP or FQDN of the master server',
  `api_key` text NOT NULL,
  `api_password` text DEFAULT NULL COMMENT 'Encrypted, optional for some providers',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `servers`
--

INSERT INTO `servers` (`id`, `name`, `panel_type`, `hostname`, `api_key`, `api_password`, `status`, `created_at`) VALUES
(8, 'SKY-URLSTART', 'virtualizor', 'cp.securednscloud.com', 'RTRBX8U28NNVFAO5', 'Y2l4eFVnYkVWaFVTVDVtV1JQdGlReXVEOXJYd3JhVkNyVzU0WlhEek4vV2srZVoxRGFXS3RrZ1ZiOEoyQStIUA==', 'active', '2025-10-16 20:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_name`, `setting_value`, `updated_at`) VALUES
(1, 'upi_id', 'Adminpanel@ybl', '2025-09-23 00:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount`, `description`, `created_at`) VALUES
(20, 5, 'credit', 2000.00, 'credit admin', '2025-09-06 16:22:55'),
(51, 5, 'credit', 500000.00, 'credit admin', '2025-09-08 15:23:42'),
(67, 43, 'credit', 2500.00, 'paid', '2025-09-10 03:18:44'),
(68, 43, 'debit', 2500.00, 'Purchased: Ultra Sonic 8 Core 16 GB Windows VPS', '2025-09-10 03:20:03'),
(69, 43, 'credit', 2500.00, 'Deposit approved (Request ID: 5)', '2025-09-10 04:24:52'),
(70, 5, 'credit', 10000.00, 'Paid', '2025-09-11 07:43:23'),
(71, 43, 'credit', 1200.00, '22', '2025-09-11 07:47:41'),
(72, 54, 'credit', 1.00, '1', '2025-09-11 07:49:57'),
(73, 54, 'debit', 1.00, '1', '2025-09-11 07:50:05'),
(74, 45, 'credit', 5.00, '5', '2025-09-11 08:12:05'),
(75, 45, 'debit', 5.00, '5', '2025-09-11 08:12:22'),
(76, 5, 'credit', 84300.00, 'credit', '2025-09-11 10:04:12'),
(77, 64, 'credit', 100.00, 'Paid', '2025-09-11 15:14:54'),
(78, 64, 'credit', 100.00, 'Paid', '2025-09-11 15:24:07'),
(80, 48, 'credit', 1.00, '1', '2025-09-11 18:17:42'),
(81, 78, 'credit', 1600.00, 'Deposit approved (Request ID: 7)', '2025-09-13 02:12:23'),
(82, 78, 'debit', 1600.00, 'Purchased: Ultra Sonic 4 Core 8 GB Windows VPS', '2025-09-13 02:12:56'),
(83, 62, 'credit', 1400.00, 'Deposit approved (Request ID: 8)', '2025-09-13 16:17:57'),
(84, 62, 'debit', 1400.00, 'Purchased: Ultra Sonic 4 Core 8 GB Windows VPS', '2025-09-13 16:20:30'),
(85, 77, 'credit', 2000.00, 'ok', '2025-09-13 19:35:38'),
(86, 77, 'debit', 1900.00, 'Purchased: Hyper Sonic 8 Core 16 GB Windows VPS', '2025-09-13 19:36:01'),
(87, 90, 'credit', 1400.00, 'Deposit approved (Request ID: 10)', '2025-09-21 16:30:43'),
(88, 90, 'debit', 1400.00, 'Purchased: Ultra Sonic 4 Core 8 GB Windows VPS', '2025-09-22 00:34:12'),
(89, 88, 'credit', 900.00, 'Deposit approved (Request ID: 11)', '2025-09-23 00:38:47'),
(90, 88, 'debit', 900.00, 'Purchased: Ultra Sonic 2 Core 4 GB Linux', '2025-09-23 00:54:44'),
(91, 70, 'credit', 1000.00, 'Deposit approved (Request ID: 12)', '2025-09-23 01:57:01'),
(92, 70, 'debit', 1000.00, 'Purchased: Ultra Sonic 2 Core 4 GB Linux', '2025-09-23 02:02:12'),
(93, 64, 'debit', 100.00, 'Transferred to Seller ID 98. By fuji', '2025-09-24 06:04:37'),
(94, 98, 'credit', 100.00, 'Received from Super Seller ID 64. By fuji', '2025-09-24 06:04:37'),
(95, 98, 'credit', 900.00, 'Deposit approved (Request ID: 13)', '2025-09-24 06:22:33'),
(96, 98, 'debit', 900.00, 'Purchased: Super Sonic 4Core 8GB WINDOWS VPS', '2025-09-24 06:23:06'),
(98, 109, 'credit', 400.00, 'Paid', '2025-09-30 03:33:03'),
(99, 109, 'credit', 100.00, 'Paid', '2025-09-30 03:35:21'),
(114, 109, 'credit', 1700.00, 'Service Credit', '2025-09-30 13:39:47'),
(123, 112, 'credit', 1300.00, 'Deposit approved (Request ID: 14)', '2025-10-02 07:28:51'),
(124, 112, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-02 07:29:11'),
(125, 43, 'debit', 750.00, 'Purchased: Hyper Sonic 2 Core  4 GB Linux', '2025-10-02 07:29:21'),
(126, 43, 'debit', 1500.00, 'Purchased: Ultra Sonic 4 Core 8 GB Windows VPS', '2025-10-02 09:57:25'),
(127, 109, 'debit', 500.00, 'Purchased: Super Sonic 2 Core 4 GB Linux', '2025-10-02 15:47:49'),
(128, 113, 'credit', 1200.00, 'Deposit approved (Request ID: 15)', '2025-10-03 06:36:29'),
(129, 113, 'debit', 1200.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-03 06:37:07'),
(131, 115, 'credit', 1300.00, 'Deposit approved (Request ID: 16)', '2025-10-05 23:13:32'),
(132, 90, 'credit', 650.00, 'Deposit approved (Request ID: 17)', '2025-10-06 01:49:48'),
(133, 90, 'debit', 650.00, 'Purchased: Hyper Sonic 2 Core  4 GB Linux', '2025-10-06 01:50:16'),
(134, 115, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-06 02:12:11'),
(135, 113, 'credit', 1200.00, 'Deposit approved (Request ID: 18)', '2025-10-06 16:56:59'),
(136, 113, 'debit', 1200.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-06 16:57:04'),
(137, 44, 'credit', 1300.00, 'Deposit approved (Request ID: 19)', '2025-10-07 03:36:32'),
(138, 44, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-07 03:37:04'),
(139, 114, 'credit', 1000.00, 'Deposit approved (Request ID: 20)', '2025-10-07 07:45:08'),
(140, 114, 'debit', 1000.00, 'Purchased: Ultra Sonic 2 Core 4 GB Linux', '2025-10-07 07:47:45'),
(141, 119, 'credit', 1300.00, 'Deposit approved (Request ID: 21)', '2025-10-07 10:53:57'),
(142, 119, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-07 10:54:36'),
(143, 109, 'debit', 500.00, 'Purchased: Super Sonic 2 Core 4 GB Linux', '2025-10-07 13:47:57'),
(144, 46, 'credit', 651.00, 'Deposit approved (Request ID: 22)', '2025-10-07 17:58:15'),
(145, 46, 'debit', 651.00, 'Purchased: Hyper Sonic 2 Core  4 GB Linux', '2025-10-07 18:00:39'),
(146, 109, 'debit', 500.00, 'Purchased: Super Sonic 2 Core 4 GB Linux', '2025-10-08 17:17:24'),
(147, 109, 'debit', 500.00, 'Purchased: Super Sonic 2 Core 4 GB Linux', '2025-10-08 17:18:17'),
(148, 109, 'credit', 200.00, 'Service Credit', '2025-10-08 17:42:07'),
(149, 109, 'credit', 4600.00, 'Service Credit', '2025-10-08 18:25:28'),
(150, 109, 'credit', 500.00, 'Service Credit', '2025-10-08 18:25:46'),
(151, 109, 'debit', 500.00, 'Purchased: Super Sonic 2 Core 4 GB Linux', '2025-10-08 18:26:32'),
(152, 90, 'credit', 1200.00, 'Deposit approved (Request ID: 23)', '2025-10-09 03:28:40'),
(153, 90, 'debit', 1200.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-09 03:29:55'),
(154, 126, 'credit', 650.00, 'Deposit approved (Request ID: 24)', '2025-10-10 07:01:06'),
(155, 109, 'debit', 800.00, 'Purchased: Ultra Sonic 2 Core 4 GB Linux', '2025-10-10 12:47:49'),
(156, 43, 'debit', 750.00, 'Purchased: Hyper Sonic 2 Core  4 GB Linux', '2025-10-11 08:26:32'),
(157, 62, 'credit', 1900.00, 'Deposit approved (Request ID: 25)', '2025-10-11 13:17:35'),
(158, 62, 'debit', 1900.00, 'Purchased: Hyper Sonic 8 Core 16 GB Windows VPS', '2025-10-11 13:19:44'),
(159, 131, 'credit', 750.00, 'Deposit approved (Request ID: 26)', '2025-10-11 15:15:28'),
(160, 131, 'debit', 750.00, 'Purchased: Hyper Sonic 2 Core  4 GB Linux', '2025-10-11 15:16:46'),
(161, 123, 'credit', 1300.00, 'Deposit approved (Request ID: 27)', '2025-10-12 03:38:41'),
(162, 123, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-12 03:39:45'),
(163, 134, 'credit', 2050.00, 'Deposit approved (Request ID: 28)', '2025-10-12 07:04:29'),
(164, 134, 'debit', 2050.00, 'Purchased: Hyper Sonic 8 Core 16 GB Windows VPS', '2025-10-12 07:04:48'),
(165, 129, 'credit', 1300.00, 'Deposit approved (Request ID: 29)', '2025-10-12 12:19:58'),
(166, 129, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-12 12:22:36'),
(167, 62, 'credit', 1900.00, 'Deposit approved (Request ID: 30)', '2025-10-12 23:06:28'),
(168, 62, 'debit', 1350.00, 'Purchased: Super Sonic 8 Core 16 GB WINDOWS VPS', '2025-10-13 03:07:52'),
(169, 62, 'credit', 250.00, 'Deposit approved (Request ID: 31)', '2025-10-13 04:08:03'),
(170, 62, 'debit', 800.00, 'Purchased: Ultra Sonic 2 Core 4 GB Linux', '2025-10-13 04:14:30'),
(171, 109, 'debit', 1200.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-14 07:13:10'),
(172, 90, 'credit', 2350.00, 'Deposit approved (Request ID: 32)', '2025-10-14 14:09:57'),
(173, 90, 'debit', 2350.00, 'Purchased: Ultra Sonic 8 Core 16 GB Windows VPS', '2025-10-14 14:11:37'),
(174, 138, 'credit', 1400.00, 'Deposit approved (Request ID: 33)', '2025-10-14 22:44:08'),
(175, 83, 'credit', 2600.00, 'Deposit approved (Request ID: 34)', '2025-10-15 00:54:30'),
(176, 83, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-15 01:17:39'),
(177, 83, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-15 01:18:42'),
(178, 138, 'debit', 1400.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-15 04:02:50'),
(179, 136, 'credit', 100.00, 'Commission from sale to User ID #138 (Order #82)', '2025-10-15 04:02:50'),
(180, 119, 'credit', 1300.00, 'Deposit approved (Request ID: 35)', '2025-10-15 04:04:44'),
(181, 119, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-15 04:05:59'),
(182, 72, 'credit', 1300.00, 'Deposit approved (Request ID: 36)', '2025-10-15 05:03:29'),
(183, 72, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-15 05:04:13'),
(184, 109, 'debit', 1200.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-15 06:07:54'),
(185, 126, 'credit', 550.00, 'Deposit approved (Request ID: 37)', '2025-10-15 08:16:34'),
(186, 126, 'debit', 1200.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-15 08:17:17'),
(187, 141, 'credit', 750.00, 'Deposit approved (Request ID: 38)', '2025-10-16 02:14:21'),
(188, 141, 'debit', 750.00, 'Purchased: Hyper Sonic 2 Core  4 GB Linux', '2025-10-16 02:14:42'),
(189, 119, 'credit', 1300.00, 'Deposit approved (Request ID: 39)', '2025-10-16 10:50:44'),
(190, 119, 'debit', 1300.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-16 10:58:20'),
(191, 77, 'credit', 5000.00, 'ok', '2025-10-16 20:19:14'),
(192, 77, 'debit', 1200.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-16 20:20:16'),
(193, 77, 'debit', 1200.00, 'Purchased: Hyper Sonic 4 Core 8 GB Windows VPS', '2025-10-18 19:12:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','super_seller','seller','user') NOT NULL,
  `status` enum('active','suspended','banned') NOT NULL DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(5, 'admin', 'amitrajput@highdatacenter.com', '$2a$12$zkBoUg/biWKPb9ORPMV6PuUJlGve5xW13NX6JDoPQDxQh2FowN//.', 'Amit Rajput', 'admin', 'active', NULL, '2025-08-26 07:19:59', '2025-09-06 07:06:50'),
(42, 'SUPERMAN', 'superman@highdatacenter.com', '$2y$10$7vMP/2xX1YfKjOErHUrMEOXAGgmz0xSLD9ts5es/epuV/p87QpSam', 'SUPERMAN', 'super_seller', 'active', 5, '2025-09-09 04:24:47', '2025-10-18 16:48:06'),
(43, 'GEYASHJI', 'geyashji@highdatacenter.com', '$2y$10$jKWOr2tpLd2ISuRs9r.oxO9os/Adz2YHMvech1mKsgHpjKfyj1VeW', 'GEYASHJI', 'seller', 'active', 5, '2025-09-09 04:34:21', '2025-09-09 04:34:21'),
(44, 'RAMESHJI', 'rameshji@highdatacenter.com', '$2y$10$hza3tq2Pg2yXFWYhP5iRc.md5T945M.C7iOA0n.uQU1nhulRWEUxO', 'RAMESHJI', 'seller', 'active', 5, '2025-09-09 04:41:36', '2025-10-07 03:25:29'),
(45, 'MUKESHJI', 'mukeshji@highdatacenter.com', '$2y$10$qEbSo7OWyiVvIA8z8RZa8.IvfFzndwEPpzKunB/rcseRsn.O9jV7i', 'MUKESHJI', 'user', 'active', 5, '2025-09-09 06:39:52', '2025-09-09 06:39:52'),
(46, 'BULLETBABA', 'bulletbaba@highdatacenter.com', '$2y$10$IjJ.lrn254A7LKc11omX6O.8deIbBd4BfchIvxVZJ5vGgzq4bhp4G', 'BULLETBABA', 'super_seller', 'active', 5, '2025-09-09 08:19:57', '2025-09-09 08:19:57'),
(47, 'RAJDON', 'RAJDON@gmail.com', '$2y$10$MLxRis.QQlccfDcG6mmJj.a4k3kqgzKFTx.oqBzNGnKtmFSpgE0Aq', 'RAJ', 'seller', 'active', 46, '2025-09-09 08:32:09', '2025-09-09 08:32:09'),
(48, 'RAMJI2021', 'RAMJI2021@highdatacenter.com', '$2y$10$Jhx0CrT5R2jmraQkxTH0qeE.dzVeYgQ3anUio.qXz4L99d2vCC.1y', 'RAMJI2021', 'seller', 'active', 5, '2025-09-09 09:32:02', '2025-09-09 09:32:02'),
(49, 'RAJSINGH', 'rajsingh@highdatacenter.com', '$2y$10$725uxUR6Td2XqHLc43tWDOtbGb0JYo9YTWnDAhABWPFW2AwShGzkq', 'RAJSINGH', 'super_seller', 'active', 5, '2025-09-09 10:50:40', '2025-09-09 10:50:40'),
(53, 'RAHUL2025', 'shahinsharma40@gmail.com', '$2y$10$gWbxHlkmhg/YafuVkPPZf.LQanDIbJ4AheqfSEWvY2GA0DoW1tvla', 'RAHUL', 'user', 'active', 48, '2025-09-10 02:27:11', '2025-09-10 02:27:11'),
(54, 'AMANROHTAS', 'amanrohtas@highdatacenter.com', '$2y$10$wngOpET9mlrmOMwCeoAchuP6xIx4tco8qxjvRi4UcUZbdk9fMynjK', 'AMANROHTAS', 'user', 'active', 5, '2025-09-10 07:16:20', '2025-09-11 02:29:09'),
(55, 'JAGUARHDC', 'jaguarhdc@highdatacenter.com', '$2y$10$WiFYg.pRnTWCfLdxlaQZOOEDRu//7QG8/ff0wHqjo9bh5A3hHB9G6', 'JAGUARHDC', 'super_seller', 'active', 5, '2025-09-10 15:29:34', '2025-09-10 15:29:34'),
(56, 'SALMAN123', 'salman123@gmail.com', '$2y$10$Dr5gUmvVMFGI9FEpQJOkeeF1ZV3XxMiecRBJo/GBR1n7neqzwn8ki', 'Salman', 'seller', 'active', 55, '2025-09-10 15:38:35', '2025-09-10 15:38:35'),
(59, 'PIKACHUHDC', 'pikachuhdc@highdatacenter.com', '$2y$10$FZc6ebSNwEQA8ZXx8/tSyOoRUAHIsONTR7SerK0O5ZHQGBm2/xKOu', 'PIKACHUHDC', 'seller', 'active', 5, '2025-09-10 22:49:50', '2025-09-10 22:49:50'),
(60, 'SHIVA', 'gshivratan1@gmail.com', '$2y$10$S3.WrOaU7QaLujDEOlEKCeIZlJRJxoWmXXGXnMAF7wkj4FUUcr3pO', 'Shiva', 'user', 'active', 59, '2025-09-11 02:12:32', '2025-09-11 02:12:32'),
(61, 'Goldvps', 'bja12800@gmail.com', '$2y$10$XEv0b74UXcqvCYaRgPpQ6.L3lwSW1s3S6qM92RAC4RebkGz77N3Vm', 'Geyas', 'user', 'active', 43, '2025-09-11 04:47:37', '2025-09-11 04:47:37'),
(62, 'RAJBHAIHDC', 'rajbhaihdc@highdatacenter.com', '$2y$10$eOwyDyneRdEEv7313.Saoulq2IECTL7tbrKT4yfYEHRMVjy0fKDHS', 'RAJBHAIHDC', 'super_seller', 'active', 5, '2025-09-11 05:31:57', '2025-09-11 05:31:57'),
(63, 'JAKIJIHDC', 'jakijihdc@highdatacenter.com', '$2y$10$oLT6eXh2/mm4MFuJ4kw9O.bwzcMGEaNK5VgRfJd4jg25WkDVTbKoi', 'JAKIJIHDC', 'super_seller', 'active', 5, '2025-09-11 05:54:49', '2025-09-11 05:54:49'),
(64, 'FUJISUPERHDC', 'fujisuperhdc@highdatacenter.com', '$2y$10$bza7kNrYZZRDn870.Ndh1Oxx.TgfBg.koQpcKa95QamahLTvkSt7S', 'FUJISUPERHDC', 'super_seller', 'active', 5, '2025-09-11 15:14:19', '2025-09-11 15:14:19'),
(65, 'AGNI', 'AGNI@gmail.com', '$2y$10$8.kx2azTm.kFwT72q9wHgOeomEjJXh476HxNndYc7g2h3ddIW27mu', 'AGNI', 'seller', 'active', 64, '2025-09-11 15:25:28', '2025-09-11 15:25:28'),
(66, 'SONAADMIN', 'SONAADMIN@gmail.com', '$2y$10$eNhbSnGNjYMyA8BiZvdipeyNtPpVbF1iw.4YiqdzX7A4UPLjKZUyK', 'SONAADMIN', 'seller', 'active', 64, '2025-09-11 15:36:27', '2025-09-11 15:36:27'),
(67, 'AGNI4G', 'lawkushonlinecenter@gmail.com', '$2y$10$6uKa3ziV0dZXJP1pa9KDROvRRr2AP3UXTviijo.ChtS7CaKPJu4V2', 'Agni', 'user', 'active', 65, '2025-09-11 15:43:00', '2025-09-11 15:43:00'),
(68, 'SAMEER', 'SAMEER@gmail.com', '$2y$10$NqAJBJ2GvCu4IjZAyvf0Je9NH3AvEQJid7g3pPU4i6vdyqb0GzZDu', 'SAMEER', 'seller', 'active', 64, '2025-09-11 16:07:48', '2025-09-11 16:07:48'),
(69, 'AYUSH', 'AYUSH@gmail.com', '$2y$10$NERXb2Fi2Qop2niVfXOR9O1BYr.HdZxN7qpU6kj./Ss4cJ.3RSJl6', 'AYUSH', 'seller', 'active', 64, '2025-09-11 16:42:37', '2025-09-11 16:42:37'),
(70, 'CLIENT53', 'client53@highdatacenter.com', '$2y$10$XLMIFjDOa69ey/DRI3rPxOooOeOgPHTHQDnFD6r5rHipI79wK.1Vq', 'CLIENT53', 'user', 'active', 5, '2025-09-12 02:03:55', '2025-09-12 02:03:55'),
(71, 'AKHILHDC', 'akhilhdc@highdatacenter.com', '$2y$10$IEiGaUn/oG8LKPFnkR0slui3sCgodRtvH5TLmvz2GEsxcz2HehAUS', 'AKHILHDC', 'seller', 'active', 5, '2025-09-12 06:59:09', '2025-09-12 06:59:09'),
(72, 'BIRUMANISHHDC', 'birumanish@gmail.com', '$2y$10$GZ5VOlBaHXAvEO7o6/VTmu0eeuSDFxidF6EVAVnl1WgVYdTVXZMoa', 'BIRUMANISHHDC', 'seller', 'active', 5, '2025-09-12 08:31:11', '2025-09-12 08:31:11'),
(73, 'ROCKYHDC', 'rockyhdc@highdatacenter.com', '$2y$10$AlhCNkYCaPVDSqeadb31nuiDtx/5/iP31ZiqwFcuT8/deMcuavXgy', 'ROCKYHDC', 'super_seller', 'active', 5, '2025-09-12 09:21:30', '2025-09-12 09:21:30'),
(74, 'ANOM', 'ANOM@gmail.com', '$2y$10$loNGEyuj/mV3XZxITlT2hucx/P/gYKFgFp0I6KWodvbRsS6KlKEDm', 'ANOM', 'seller', 'active', 64, '2025-09-12 14:13:20', '2025-09-12 14:13:20'),
(75, 'Guru2025', 'guru2025@highdatacenter.com', '$2y$10$unGXEdokctAmDcYZgqDUiui5WxWjMDKsJKbMmf8AXYHHa94Vhhg5W', 'Guru2025', 'seller', 'active', 5, '2025-09-12 14:22:24', '2025-09-12 14:22:24'),
(76, 'MANDALHDC', 'mandalhdc@highdatacenter.com', '$2y$10$D1VjESIiPdkmAOUsgpiu0OWvFeIaaphZyUSTeGFFthK19yV8O0qc6', 'MANDALHDC', 'seller', 'active', 5, '2025-09-12 17:17:21', '2025-09-12 17:17:21'),
(77, 'supertest', 'amitsingh@highdatacenter.com', '$2y$10$x799buGpD6q3H7NKVJtkG.2i.CteAjXA1MmoN6SgATmG8CZkk5ssO', 'AMITSINGH', 'super_seller', 'active', 5, '2025-09-12 19:09:09', '2025-10-18 16:49:37'),
(78, 'RAVIKANT', 'ravikant@highdatacenter.com', '$2y$10$3CGvrq.zC542TXxfLa2AruLwMU880RuoGpa5aTAt/HB8bdas8z6Pe', 'RAVIKANT', 'user', 'active', 5, '2025-09-13 01:53:34', '2025-09-13 01:53:34'),
(79, 'BENGALTIGER', 'rukon45@gmail.com', '$2y$10$JfDAHBM910x/7/VnzaAg4OxUBf8HoevokerFLTlSuXgkPD7gCmnYi', 'BENGALTIGER', 'seller', 'active', 73, '2025-09-13 14:35:13', '2025-09-13 14:35:13'),
(80, 'ARIOBHAI', 'ariobhai@highdatacenter.com', '$2y$10$h3PHNbPTLF9wftj2QN7pBu704cNHjxX/z3t4CI9C3SZ2Ay5YDRLgK', 'ARIOBHAI', 'super_seller', 'active', 5, '2025-09-13 15:26:17', '2025-09-13 15:26:17'),
(81, 'MUNNA', 'MUNNA@gmail.com', '$2y$10$fXasguJXDQjh9NeAWQLBau9yY0dUVoDNso7ojo6av6ZvK3X0Fso06', 'MUNNA', 'seller', 'active', 64, '2025-09-14 02:20:03', '2025-09-14 02:20:03'),
(82, 'CAPTANHDC', 'captanhdc@highdatacenter.com', '$2y$10$sF9o4fYdeHxESHnc6hfRiuv4at1Xs2bUCNa3xq3f8qIHkV3mqn0ES', 'CAPTANHDC', 'seller', 'active', 5, '2025-09-14 03:28:09', '2025-09-14 03:28:09'),
(83, 'SANTU2025', 'santu2025@highdatacenter.com', '$2y$10$4O0uyw/jkW3VC50ei4OZruwUGiyJDRXtSKImn9d5JO3Sy3nJdYZaS', 'SANTU2025', 'seller', 'active', 5, '2025-09-14 04:24:05', '2025-09-14 04:24:05'),
(85, 'DEMO', 'salman@bharatvpvks.com', '$2y$10$DL5nPOH0t6t09U2LDrEhL.X/FkRC6QDa.tcr5gPBORgzNVzGXw5Xy', 'Ygjh', 'user', 'active', 56, '2025-09-14 23:16:39', '2025-09-19 10:57:51'),
(86, 'XADMIN', 'XADMIN@gmail.com', '$2y$10$bdvN8f18b9Vb4S80eqzgCu7SH4BHAXUYzbmQ1YZfhW93AGOQrbi1.', 'XADMIN', 'seller', 'active', 64, '2025-09-17 18:06:58', '2025-09-17 18:06:58'),
(87, 'P K TRAVELS', 'pktravel@email.com', '$2y$10$42qCSPLMrveC/WLfdswaUekn9KRxp8glwzSDX8uZcLW2ssRKMGS4S', 'P K TRAVELS', 'user', 'active', 86, '2025-09-17 18:10:13', '2025-09-17 18:10:13'),
(88, 'VIJAY2025', 'vijay2025@highdatacenter.com', '$2y$10$.SzC3R9Ny9Z8x/Y39mwP3uJTr5YBNgTyWl8yU5QhEV5BElf2PDCB6', 'VIJAY2025', 'seller', 'active', 5, '2025-09-19 16:27:42', '2025-09-19 16:27:42'),
(89, 'RANJANSELLER', 'Ranjanseller@highdatacenter.com', '$2y$10$2wu9S5skolQpCDSqIQ/bbery6jYVLiZIXwQ5OPrLN9Zc/TJyRokIG', 'RANJANSELLER', 'seller', 'active', 5, '2025-09-20 02:20:47', '2025-09-20 02:20:47'),
(90, 'AMANSELLER', 'amanseller@highdatacenter.com', '$2y$10$3qOVmPCS3FsE3P8dppgDl.5EGFgWZ.3g3rjpr0sjYq2IJMbhaF78G', 'AMANSELLER', 'super_seller', 'active', 5, '2025-09-21 00:50:09', '2025-09-21 00:50:09'),
(91, 'Badsha786', 'badsha786@highdatacenter.com', '$2y$10$4UC1rwY4aQdoqf/aGf3DEewjiEBbrNwjWBCuJd22LW8H7jPh5ZYQG', 'Badsha786', 'seller', 'active', 5, '2025-09-21 06:27:34', '2025-09-21 06:27:34'),
(92, 'Client34', 'client34@highdatacenter.com', '$2y$10$42NJ9aoTHbk/mxh70v.b9.Za6SLMzDqNKLiifRJk0QGVDxEYqlb.m', 'Client34', 'user', 'active', 5, '2025-09-21 16:43:12', '2025-09-21 16:43:12'),
(93, 'LUCKY', 'lucky@gmail.com', '$2y$10$hJMLiWqS.pZl8tiKiuUAo.aQpjadEYMF9WN4rkBz9jfE654279btO', 'LUCKY', 'user', 'active', 86, '2025-09-22 02:40:59', '2025-09-22 02:40:59'),
(94, 'DEMOSELLER', 'DEMOSELLER@DEMOSELLER.DEMOSELLER', '$2y$10$iacjFJrbWt5sCDDx70OG2.Wf4zVBC2iX2RzSYx2Dlemj4Nuqeluk6', 'DEMOSELLER', 'seller', 'active', 5, '2025-09-22 20:33:57', '2025-09-22 20:33:57'),
(95, 'Aahilbhai', 'aahilbhai@highdatacenter.com', '$2y$10$zfqmXcNFSnkG3Csy.959eOcwRV/rzXocIH2BlsUfFE9K9teyeAYpC', 'Aahilbhai', 'super_seller', 'active', 5, '2025-09-23 15:20:59', '2025-09-23 15:20:59'),
(96, 'Netajihdc', 'netajihdc@highdatacenter.com', '$2y$10$C6UbV7pz9iLP8szq2D/b6Os0uiVmzoe7Ld9MAn97cBvvTj4GEPpv.', 'Netajihdc', 'super_seller', 'active', 5, '2025-09-23 15:52:19', '2025-09-23 15:58:08'),
(97, 'Vinaybhaiji', 'vinaybhaiji@highdatacenter.com', '$2y$10$MOSJglw9DMYDOELX4fA0OO4xTgaMAC1UHz/GLZBgZvp3Cr9HcWL6e', 'Vinaybhaiji', 'super_seller', 'active', 5, '2025-09-23 16:04:28', '2025-10-03 15:40:47'),
(98, 'NINJA', 'NINJA@gmail.com', '$2y$10$9e269smdW0wDmP4kbw1EQu8PxrL7yjl0cC2CgpBWryKlA.RdZ1oXi', 'NINJA', 'seller', 'active', 64, '2025-09-24 05:56:01', '2025-09-24 05:56:01'),
(99, 'Royal', 'royal@gmail.com', '$2y$10$bzlpod9O6YgQHLl36/OWxulsyQDZptl/hLNQS9AfjNGpxoEynQFFS', 'ROYAL', 'seller', 'active', 46, '2025-09-26 01:51:00', '2025-09-26 01:51:00'),
(100, 'Mahadevseller', 'Mahadevseller@highdatacenter.com', '$2y$10$0NDW84qIxAJf9HpFopECL.d8cze2aPXsJrSGZDt0/VPBQDIg3KbMe', 'Mahadevseller', 'super_seller', 'active', 5, '2025-09-26 15:42:32', '2025-09-26 15:42:32'),
(101, 'seller1S', 'seller1S@GMAIL.COM', '$2y$10$U00KKqpYfUvF739PbmrnyOQhhwIhs22uXoqUnWt9PbJQhuD1aTABW', 'seller1S', 'user', 'active', 100, '2025-09-26 16:23:10', '2025-09-26 16:23:10'),
(102, 'RAJAN12', 'RAJAN12@GMAIL.COM', '$2y$10$6/AxHHzbWHjGTeGF6.pQ9OnKdj.lpCyeSUJqJVFX/573XXnDZxTLO', 'RAJAN12', 'seller', 'active', 100, '2025-09-26 16:25:27', '2025-09-26 16:25:27'),
(103, 'Rinkuhdc', 'Rinkuhdc@highdatacenter.com', '$2y$10$PKwcmNiQpEfTJcD48Iouku.KTiRc4hQBZZDuweddWygwqVDWT/M9W', 'Rinkuhdc', 'seller', 'active', 5, '2025-09-27 14:02:54', '2025-09-27 14:02:54'),
(104, 'Winzip', 'tatkalsoftwarewinzip@gmail.com', '$2y$10$xXW9TmugQCwP4gxGFeGJD.VaWxSye8pwg.S3ZECsrJYuHCevbZoV6', 'Winzip', 'user', 'active', 99, '2025-09-27 17:35:21', '2025-09-27 17:35:21'),
(105, 'Skpjihdc', 'Skpjihdc@highdatacenter.com', '$2y$10$bOIF/C2OjWi5NrLiA5A7.uemvq5q4DH.Bt4zYfJSExLQYutFRUbB2', 'Skpjihdc', 'seller', 'active', 5, '2025-09-28 12:14:01', '2025-09-28 12:14:01'),
(106, 'Amit503hdc', 'Amit503hdc@highdatacenter.com', '$2y$10$USubLOX.bBa/nuwRl0z4LuJUmhUSrjmgfR/kBpHH3IEdx5teN4US6', 'Amit503hdc', 'seller', 'active', 5, '2025-09-28 17:03:14', '2025-09-28 17:03:14'),
(107, 'Aahilbhai1', 'mumbaiwest143@gmail.com', '$2y$10$mfOM1nqZ4x.VjF0A70zn5O/u9kokWQl9dahe3u9.FmGHgL5FE2p1S', 'Aahil Bhai', 'seller', 'active', 95, '2025-09-29 15:11:39', '2025-09-29 15:11:39'),
(108, 'Aahilbhai2', 'newkhanybl@gmail.com', '$2y$10$vWYcBSKjiOGqw29hnlZEpu.hcEKCY2Sns.W/tcjKRpexqmiyg8pbK', 'Aahil Bhai', 'user', 'active', 107, '2025-09-29 15:14:58', '2025-09-29 15:14:58'),
(109, 'Amitsuper', 'Amitsuper@highdatacenter.com', '$2y$10$9sF4ypLCCgCbB7ar1GKiKe30xfJ6eaYPe2n0QNU9hGeTdfASS3S12', 'Amitsuper', 'super_seller', 'active', 5, '2025-09-30 03:31:24', '2025-09-30 03:31:24'),
(110, 'Tatkalkinghdc', 'Tatkalkinghdc@highdatacenter.com', '$2y$10$ICDE7mdjpIiaBVOfvP0i.OCFDi3zvSdqrqN9RgCFZ3oEUA/QOjQvK', 'Tatkalkinghdc', 'seller', 'active', 5, '2025-09-30 23:53:36', '2025-09-30 23:53:36'),
(111, 'SAMAD8210', 'mdsamad09155@gmail.com', '$2y$10$AbY/9F3zGqUIp2gqhOV8euPaRM3PSRjlT4k.ILTTXIdB.oBgwUFSi', 'Md sanad', 'user', 'active', 110, '2025-10-01 00:21:38', '2025-10-01 00:21:38'),
(112, 'Jayshreeraam', 'Jayshreeraam@highdatacenter.com', '$2y$10$zKO.jpPmgq.F4QyyrPwhQeegm.EOO/0DsYYBIBCKdzQPCDViwzHCO', 'Jayshreeraam', 'seller', 'active', 5, '2025-10-02 06:47:13', '2025-10-02 06:47:13'),
(113, 'Enterprisehdc', 'Enterprisehdc@highdatacenter.com', '$2y$10$8Rm2660HXTf4On4wepmPZueYvge4jdaL8kHL/TpDDlVJuI/BNGjJm', 'Enterprisehdc', 'super_seller', 'active', 5, '2025-10-03 06:22:03', '2025-10-03 06:22:03'),
(114, 'ADITYAHDC', 'adityahdc@highdatacenter.com', '$2y$10$0Xe9PYTalmO9UQIeivtB5.j8QnRvAiJUYtRBQgCi0PTHqz1baQm.q', 'ADITYAHDC', 'user', 'active', 5, '2025-10-03 17:11:07', '2025-10-03 17:11:07'),
(115, 'Emraanhdc', 'emraanhdc@highdatacenter.com', '$2y$10$4ZgDHthBPFse9SPiC/L98ODoZvc.0JXo.pJM0JI.ElNXDSlg9SM5G', 'Emraanhdc', 'seller', 'active', 5, '2025-10-05 14:56:58', '2025-10-05 14:56:58'),
(116, 'shahjahan786', 'gfddhnn123@gmail.com', '$2y$10$y2FmHstrvRAsBOw0416/ke89bzUbO0oBhnrqDFWaSty8M7oaXTk06', 'shahjahan', 'user', 'active', 113, '2025-10-06 17:26:58', '2025-10-06 17:26:58'),
(117, 'Ajsingh2025', 'Ajsingh2025@highdatacenter.com', '$2y$10$DsRbcb5T49ZQEN9wS0CODuxnRGYHBA.4LK8tXijlZuoZ.l8nNRfdq', 'Ajsingh2025', 'seller', 'active', 5, '2025-10-07 03:47:53', '2025-10-07 03:47:53'),
(118, 'Ajsingh123', 'ajsingh123@highdatacenter.com', '$2y$10$px7ly5tbWpKqpPiYFshCEO9a9gCebEMAgxvqCcYrNHwUY33gbdQra', 'Ajsingh123', 'user', 'active', 75, '2025-10-07 04:05:01', '2025-10-07 04:05:01'),
(119, 'THORADMIN', 'THORADMIN@highdatacenter.com', '$2y$10$WD98TFfajab8gQGjV8FYsOxj5lYwG8CG6DEM3zozKqMkOLpUxiIgi', 'THORADMIN', 'seller', 'active', 5, '2025-10-07 10:42:35', '2025-10-07 10:42:35'),
(120, 'PRAMODVPS', 'thora1791@gmail.com', '$2y$10$ePd0iup0M88axaxX/HmMZOt7mPCMnlghupwBdUDIHQC.kJe.yvtee', 'PRAMOD', 'user', 'active', 119, '2025-10-07 10:57:58', '2025-10-07 10:57:58'),
(121, 'DHARAN', 'DHARAN@gmail.com', '$2y$10$Od/TtA1yQAxBqFXL36AFAOJfbQbBx42lfz1bKzkP8yaMcyz5HSuGu', 'DHARAN', 'seller', 'active', 64, '2025-10-07 15:48:45', '2025-10-07 15:48:45'),
(122, 'GEMAR', 'GEMAR@gmail.com', '$2y$10$4ejSRwp9CDSepQM/FYkNq.GeBxJf8tbhto0rpiyFcDuUAAygLvwaq', 'GEMAR', 'seller', 'active', 64, '2025-10-07 19:50:14', '2025-10-07 19:50:14'),
(123, 'Saurabhji', 'Saurabhji@highdatacenter.com', '$2y$10$6lMqPd1Zs./KjYIraucbhOJmyI2PpemV1wG9DXlAE3WgLoijrAX6K', 'Saurabhji', 'seller', 'active', 5, '2025-10-08 06:05:59', '2025-10-08 06:05:59'),
(124, 'PROXADMIN', 'PROXADMIN@highdatacenter.com', '$2y$10$wSRvyStyQstaeJZBm9qv7e.L/u00XiPMS3q19TOqlVhcJE/JjKEcK', 'PROXADMIN', 'super_seller', 'active', 5, '2025-10-08 06:08:43', '2025-10-08 06:08:43'),
(125, 'Sahil007', 'Sahil007@gmail.com', '$2y$10$O8pz4gORlvQfCv7VgfKPj.U0Bo0v9LyjB43Ie24wLA5v3b/GbyGP.', 'Sahil', 'seller', 'active', 46, '2025-10-09 03:17:46', '2025-10-09 03:17:46'),
(126, 'Monugaon', 'Monugaon@highdatacenter.com', '$2y$10$UFj197dLi1GsLpqde83Eh.NLWSKh/soD3bWtl0nh1nB8BmqLbeiAq', 'Monugaon', 'super_seller', 'active', 5, '2025-10-10 06:52:59', '2025-10-10 06:52:59'),
(127, 'Bhushawalhdc', 'Bhushawalhdc@highdatacenter.com', '$2y$10$ufH4ij3V8/koKNHg0FpsxuBdXWIhif3WgGvijeVjdFCQx.KjDfyQa', 'Bhushawalhdc', 'seller', 'active', 5, '2025-10-11 02:54:26', '2025-10-11 02:54:26'),
(128, 'Mimbai123', 'rajiji@gmail.com', '$2y$10$oQc0FoUMJ1rr82tk5zrUh.YSX91SRy4CUjcAuIO69vQtAFIOMYVqe', 'Rajiji', 'user', 'active', 127, '2025-10-11 03:13:10', '2025-10-11 03:13:10'),
(129, 'Manirhdc', 'Manirhdc@highdatacenter.com', '$2y$10$5mNazcVB0u6hNQy/4Lc2DuXV1fLAvIVsxYRMHThuJNYD.2RkRqz0G', 'Manirhdc', 'seller', 'active', 5, '2025-10-11 08:21:13', '2025-10-11 08:21:13'),
(130, 'Parvez2025', 'Baba123@gmail.com', '$2y$10$zq8NtojRnkKr.ZZbRdsTBufJ6EbBzf04q9T9RhLwVYAeW9/RHzpdS', 'Parvez2025', 'user', 'active', 129, '2025-10-11 08:31:26', '2025-10-11 08:31:26'),
(131, 'Jakirhdc', 'Jakirhdc@highdatacenter.com', '$2y$10$tR.eu3nPgqfyHnA0i5xXH.pa1/vSIJ1K6W2upHRxhuJstlDE1VEMC', 'Jakirhdc', 'seller', 'active', 5, '2025-10-11 15:00:39', '2025-10-11 15:00:39'),
(132, 'Shaikh', 'Shaikh@gmail.com', '$2y$10$boZd7.JOMnV5z5n.N2/0wuvseqyb7dU/ilOLHeZok/2ZAXkpnTX7O', 'Shaikh', 'user', 'active', 127, '2025-10-11 15:47:49', '2025-10-11 15:47:49'),
(133, 'Guddu1040', 'gudduxxxx1983@gmail.com', '$2y$10$EdQ0EK45mGBhtDSiFIqTSORzon6aCJicyRkaOslNciuvkXv2qHjSu', 'Guddu', 'user', 'active', 123, '2025-10-12 04:12:50', '2025-10-12 04:12:50'),
(134, 'REZANHDC', 'rezan@hdc.com', '$2y$10$nLpkUdYLn.jIbpy/AcC5LeDO9lM.AZUsXgBzxBBBIbJ8Q/hxpw51W', 'REZANHDC', 'seller', 'active', 5, '2025-10-12 06:54:54', '2025-10-12 06:54:54'),
(135, 'AMARJEET', 'AMARJEET@hdc.com', '$2y$10$Z/ZBo5ImF6EOyH89BVzS3.1FS.p5wIJLJaqUExnk7UnJFs43uPeYu', 'AMARJEET', 'seller', 'active', 5, '2025-10-12 07:05:50', '2025-10-12 07:05:50'),
(136, 'Aditya2025', 'Aditya2025@hdc.com', '$2y$10$j4hc/J3n92PXjSdMgiSJxeHuRqcA1pqlkSm2thWCZIkP18p.s8QCm', 'Aditya2025', 'seller', 'active', 5, '2025-10-14 07:25:26', '2025-10-14 07:25:26'),
(137, 'Aditya', 'panup857@gmail.com', '$2y$10$XQBGztRMqHJE0VXmSVtrweS8AJTXaMMzEIwmz7rnGIv3ahFE/W8me', 'aditya', 'user', 'active', 136, '2025-10-14 07:33:58', '2025-10-14 07:33:58'),
(138, 'VAIRAS', 'jojoda286@gmail.com', '$2y$10$knG5hguhRsI3bEC5ZyaXyOpb9P37oVAIp2df9dDX/eibCO93o5tAW', 'VAIRAS', 'user', 'active', 136, '2025-10-14 16:45:35', '2025-10-14 16:45:35'),
(139, 'Vishal786', 'Vishal786@hdc.com', '$2y$10$5TWf9r/c015i3qYuGUAtA.FKM881Dh.lEvaCZjmTWt829HKLt..J2', 'Vishal786', 'seller', 'active', 5, '2025-10-15 10:13:50', '2025-10-15 10:13:50'),
(140, 'NITESH', 'monasingh99141@gmail.com', '$2y$10$k3rR.HE11WXUyMLNHC4/JuztSVek.9cNcP0QyAZcw4AtfYVKaLcyi', 'Nitesh', 'user', 'active', 136, '2025-10-15 17:44:23', '2025-10-15 17:44:23'),
(141, 'Dineshpatel', 'Dineshpatel@hdc.com', '$2y$10$7B//dBnqUkN6XsMfWYdbpOXXfa2DMU9c13J48TimDZOUcq3QlD3VS', 'Dineshpatel', 'seller', 'active', 5, '2025-10-16 01:13:46', '2025-10-16 01:13:46'),
(142, 'infobazar', 'infobazar@hdc.com', '$2y$10$EyAExxdimD6pmIiE/QSVC.F2TJhJOc62h9yDkRsjLbmY1owWzx0H2', 'infobazar', 'seller', 'active', 5, '2025-10-16 12:36:57', '2025-10-16 12:36:57'),
(143, 'ANSARI', 'ANSARI@gmail.com', '$2y$10$aDhyZ61iBtLlfV7i2dzdtOhx7Qhs3.eHswqJ2MKpAl/ZaQRJucVIm', 'ANSARI', 'seller', 'active', 46, '2025-10-16 14:29:57', '2025-10-16 14:29:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_product_prices`
--

CREATE TABLE `user_product_prices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'The user for whom this custom price applies',
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL COMMENT 'The custom selling price',
  `set_by_user_id` int(11) NOT NULL COMMENT 'The ID of the admin or reseller who set this price',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_product_prices`
--

INSERT INTO `user_product_prices` (`id`, `user_id`, `product_id`, `price`, `set_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 46, 9, 651.00, 5, '2025-09-09 20:57:40', '2025-09-09 20:57:40'),
(5, 69, 9, 700.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(6, 69, 10, 1270.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(7, 69, 11, 1965.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(8, 69, 5, 2100.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(9, 69, 6, 2450.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(10, 69, 1, 560.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(11, 69, 3, 980.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(12, 69, 4, 1450.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(13, 69, 12, 850.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(14, 69, 14, 1450.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(15, 69, 18, 2450.00, 64, '2025-09-11 16:45:11', '2025-09-11 16:45:11'),
(17, 42, 9, 651.00, 5, '2025-09-12 07:53:54', '2025-09-12 07:53:54'),
(19, 98, 3, 900.00, 64, '2025-09-24 06:00:30', '2025-09-24 06:00:30'),
(20, 107, 11, 2699.00, 95, '2025-09-29 15:12:52', '2025-09-29 15:12:52'),
(21, 107, 9, 1300.00, 95, '2025-09-29 15:13:24', '2025-09-29 15:13:24'),
(22, 107, 10, 1899.00, 95, '2025-09-29 15:13:24', '2025-09-29 15:13:24'),
(23, 120, 9, 1500.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(24, 120, 10, 1800.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(25, 120, 11, 2500.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(26, 120, 5, 2500.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(27, 120, 6, 3000.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(28, 120, 1, 1200.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(29, 120, 3, 1600.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(30, 120, 4, 2000.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(31, 120, 12, 1400.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(32, 120, 14, 2000.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(33, 120, 18, 3000.00, 119, '2025-10-07 11:14:16', '2025-10-07 11:14:16'),
(45, 132, 9, 850.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(46, 132, 10, 1400.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(47, 132, 11, 2100.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(48, 132, 5, 2100.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(49, 132, 6, 2600.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(50, 132, 1, 750.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(51, 132, 3, 1100.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(52, 132, 4, 1600.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(53, 132, 12, 1111.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(54, 132, 14, 1600.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(55, 132, 18, 2600.00, 127, '2025-10-11 15:49:56', '2025-10-11 15:49:56'),
(56, 133, 9, 1000.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(57, 133, 10, 1900.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(58, 133, 11, 2500.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(59, 133, 5, 2500.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(60, 133, 6, 3000.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(61, 133, 1, 1000.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(62, 133, 3, 1500.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(63, 133, 4, 2000.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(64, 133, 12, 1400.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(65, 133, 14, 1900.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(66, 133, 18, 3000.00, 123, '2025-10-12 04:14:12', '2025-10-12 04:14:12'),
(67, 137, 9, 750.00, 136, '2025-10-14 07:35:35', '2025-10-14 07:35:35'),
(68, 138, 9, 800.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(69, 138, 10, 1400.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(70, 138, 11, 2150.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(71, 138, 5, 2100.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(72, 138, 6, 2600.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(73, 138, 1, 650.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(74, 138, 3, 1100.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(75, 138, 4, 1600.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(76, 138, 12, 1000.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(77, 138, 14, 1600.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(78, 138, 18, 2600.00, 136, '2025-10-14 16:47:01', '2025-10-14 16:47:01'),
(79, 140, 9, 800.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(80, 140, 10, 1400.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(81, 140, 11, 2150.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(82, 140, 5, 2100.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(83, 140, 6, 2600.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(84, 140, 1, 700.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(85, 140, 3, 1100.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(86, 140, 4, 1600.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(87, 140, 12, 950.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(88, 140, 14, 1600.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43'),
(89, 140, 18, 2600.00, 136, '2025-10-15 17:48:43', '2025-10-15 17:48:43');

-- --------------------------------------------------------

--
-- Table structure for table `vps_action_logs`
--

CREATE TABLE `vps_action_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `role` enum('user','seller','super_admin') NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vps_commissions`
--

CREATE TABLE `vps_commissions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `status` enum('unpaid','paid') DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vps_orders`
--

CREATE TABLE `vps_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `vps_id` int(11) DEFAULT NULL,
  `main_ip` varchar(50) DEFAULT NULL,
  `vps_username` varchar(100) DEFAULT NULL,
  `vps_password` varchar(100) DEFAULT NULL,
  `status` enum('pending','active','suspended','terminated') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vps_packages`
--

CREATE TABLE `vps_packages` (
  `id` int(11) NOT NULL,
  `provider_key` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `cpu` varchar(50) NOT NULL,
  `ram` varchar(50) NOT NULL,
  `disk` varchar(50) NOT NULL,
  `bandwidth` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `commission_rate` decimal(5,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vps_packages`
--

INSERT INTO `vps_packages` (`id`, `provider_key`, `name`, `description`, `cpu`, `ram`, `disk`, `bandwidth`, `price`, `commission_rate`, `status`, `created_at`) VALUES
(1, 'hyper_sonic', 'TEST VPS API PKG 01', 'TEST', '2 Core', '4 GB', '500 GB', '1 TB', 199.00, 0.00, 'active', '2025-09-26 19:31:25');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `updated_at`) VALUES
(8, 5, 596300.00, '2025-09-11 10:04:12'),
(35, 43, 700.00, '2025-10-11 08:26:32'),
(39, 54, 0.00, '2025-09-11 07:50:05'),
(40, 45, 0.00, '2025-09-11 08:12:22'),
(42, 64, 100.00, '2025-09-24 06:04:37'),
(45, 48, 1.00, '2025-09-11 18:17:42'),
(46, 78, 0.00, '2025-09-13 02:12:56'),
(47, 62, 0.00, '2025-10-13 04:14:30'),
(48, 77, 2700.00, '2025-10-18 19:12:21'),
(49, 90, 0.00, '2025-10-14 14:11:37'),
(50, 88, 0.00, '2025-09-23 00:54:44'),
(51, 70, 0.00, '2025-09-23 02:02:12'),
(52, 98, 100.00, '2025-09-24 06:23:06'),
(55, 109, 1800.00, '2025-10-15 06:07:54'),
(58, 112, 0.00, '2025-10-02 07:29:11'),
(59, 113, 0.00, '2025-10-06 16:57:04'),
(60, 115, 0.00, '2025-10-06 02:12:11'),
(63, 44, 0.00, '2025-10-07 03:37:04'),
(64, 114, 0.00, '2025-10-07 07:47:45'),
(65, 119, 0.00, '2025-10-16 10:58:20'),
(66, 46, 0.00, '2025-10-07 18:00:39'),
(71, 126, 0.00, '2025-10-15 08:17:17'),
(73, 131, 0.00, '2025-10-11 15:16:46'),
(74, 123, 0.00, '2025-10-12 03:39:45'),
(75, 134, 0.00, '2025-10-12 07:04:48'),
(76, 129, 0.00, '2025-10-12 12:22:36'),
(80, 138, 0.00, '2025-10-15 04:02:50'),
(81, 83, 0.00, '2025-10-15 01:18:42'),
(82, 136, 100.00, '2025-10-15 04:02:50'),
(84, 72, 0.00, '2025-10-15 05:04:13'),
(86, 141, 0.00, '2025-10-16 02:14:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `deposit_requests`
--
ALTER TABLE `deposit_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utr_number` (`utr_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `assigned_to_id` (`assigned_to_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `server_id` (`server_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `user_product_prices`
--
ALTER TABLE `user_product_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product_unique` (`user_id`,`product_id`) COMMENT 'Ensures a user can only have one custom price per product',
  ADD KEY `product_id` (`product_id`),
  ADD KEY `set_by_user_id` (`set_by_user_id`);

--
-- Indexes for table `vps_action_logs`
--
ALTER TABLE `vps_action_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indexes for table `vps_commissions`
--
ALTER TABLE `vps_commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `vps_orders`
--
ALTER TABLE `vps_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `vps_packages`
--
ALTER TABLE `vps_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `deposit_requests`
--
ALTER TABLE `deposit_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `user_product_prices`
--
ALTER TABLE `user_product_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `vps_action_logs`
--
ALTER TABLE `vps_action_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vps_commissions`
--
ALTER TABLE `vps_commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vps_orders`
--
ALTER TABLE `vps_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vps_packages`
--
ALTER TABLE `vps_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deposit_requests`
--
ALTER TABLE `deposit_requests`
  ADD CONSTRAINT `deposit_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`assigned_to_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_product_prices`
--
ALTER TABLE `user_product_prices`
  ADD CONSTRAINT `user_product_prices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_product_prices_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_product_prices_ibfk_3` FOREIGN KEY (`set_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vps_action_logs`
--
ALTER TABLE `vps_action_logs`
  ADD CONSTRAINT `vps_action_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `vps_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vps_action_logs_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vps_commissions`
--
ALTER TABLE `vps_commissions`
  ADD CONSTRAINT `vps_commissions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `vps_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vps_commissions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vps_orders`
--
ALTER TABLE `vps_orders`
  ADD CONSTRAINT `vps_orders_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `vps_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vps_orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
