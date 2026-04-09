-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 19, 2026 at 06:43 AM
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
-- Database: `u109448269_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) DEFAULT 'Admin',
  `email` varchar(255) DEFAULT '',
  `phone` varchar(50) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`, `name`, `email`, `phone`) VALUES
(4, 'Faizan.R', '$2y$10$pEMc6IAO4wFCNs5ALeeU3uGJe9hIfcJhWhaawv3x00Wf7SYvmnp1a', '2025-11-27 12:53:06', 'Faizan', 'faizan@gmail.com', '03136949487'),
(6, '12Umar12', '$2y$10$6KZC0ipsed8zjLThlmTmUe57gdLZ/gyGsVZrzuILfW8IjI6ikeil.', '2025-12-11 13:46:15', 'Umar', 'umar@gmail.com', '03199972962');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `provider_name` varchar(255) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `location` text NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `user_name`, `provider_id`, `provider_name`, `service_type`, `price`, `booking_date`, `booking_time`, `location`, `description`, `status`, `created_at`) VALUES
(2, 'mobile_user_2', 'John Smith', NULL, NULL, NULL, NULL, '2026-01-13', '10:30:00', '789 App Ave', 'Test Booking 2', 'pending', '2026-01-10 06:48:25'),
(3, 'JE3lRvnJ4df2CTV4YkNTLUgbUVi1', 'Anonymous User', '1', NULL, 'Electrician', NULL, '2026-01-10', '12:47:00', 'peshawar', 'test', 'confirmed', '2026-01-10 07:47:18'),
(4, 'JE3lRvnJ4df2CTV4YkNTLUgbUVi1', 'Anonymous User', '', NULL, 'Electrician', NULL, '2026-01-17', '13:24:00', 'lahore', 'test', 'completed', '2026-01-10 08:24:47'),
(5, 'XHljRqeSZPMB5NupbwHerZRWEWF3', 'Anonymous User', '', NULL, 'Electrician', NULL, '2026-01-21', '04:20:00', 'sjs', 'dhzhz', 'pending', '2026-01-12 05:55:09'),
(6, 'JE3lRvnJ4df2CTV4YkNTLUgbUVi1', 'Anonymous User', '', NULL, 'Electrician', NULL, '2026-01-13', '00:03:00', 'peshawar', 'test', 'pending', '2026-01-12 06:03:52'),
(7, 'XHljRqeSZPMB5NupbwHerZRWEWF3', 'Imran', '', NULL, 'Electrician', NULL, '2026-01-27', '11:19:00', 'peshawar', 'test', 'pending', '2026-01-12 06:19:34'),
(8, 'JE3lRvnJ4df2CTV4YkNTLUgbUVi1', 'umar', '23', NULL, 'Electrician', NULL, '2026-01-22', '16:47:00', 'gulbahar', 'test', 'pending', '2026-01-12 09:48:05'),
(9, 'JE3lRvnJ4df2CTV4YkNTLUgbUVi1', 'umar', '23', NULL, 'Electrician', NULL, '2026-01-16', '16:07:00', 'gulbahar', 'test', 'confirmed', '2026-01-12 10:10:05'),
(10, 'JE3lRvnJ4df2CTV4YkNTLUgbUVi1', 'umar', '23', NULL, 'Electrician', NULL, '2026-01-22', '16:11:00', 'gulbahar', 'test', 'confirmed', '2026-01-12 10:11:20'),
(11, 'JE3lRvnJ4df2CTV4YkNTLUgbUVi1', 'umar', '23', 'Ahmad Khan', 'Electrician', 0.00, '2026-01-16', '12:00:00', 'peshawar', 'test', 'pending', '2026-01-12 17:11:49'),
(12, 'XHljRqeSZPMB5NupbwHerZRWEWF3', 'Imran', '23', 'Ahmad Khan', 'Electrician', 0.00, '2026-01-23', '03:56:00', 'test', 'test', 'pending', '2026-01-13 05:56:43'),
(13, 'XHljRqeSZPMB5NupbwHerZRWEWF3', 'Imran', '23', 'Ahmad Khan', 'Electrician', 0.00, '2026-01-28', '15:45:00', 'gulbahar', 'neat and clean work', 'pending', '2026-01-13 08:46:09'),
(14, 'JE3lRvnJ4df2CTV4YkNTLUgbUVi1', 'Umar Khan', '23', 'Ahmad Khan', 'Electrician', 0.00, '2026-01-13', '15:56:00', 'peshawar', 'test', 'pending', '2026-01-13 10:57:03'),
(15, 'XHljRqeSZPMB5NupbwHerZRWEWF3', 'Imran', '23', 'Ahmad Khan', 'Electrician', 0.00, '2026-01-23', '15:15:00', 'gulbahar', '', 'pending', '2026-01-13 13:15:38');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`) VALUES
(20, 'Chamkani'),
(18, 'Circular Road'),
(21, 'Dalazak Road'),
(22, 'Hayatabad'),
(17, 'Sadder'),
(16, 'University Road'),
(19, 'Warsak Road');

-- --------------------------------------------------------

--
-- Table structure for table `city_staff`
--

CREATE TABLE `city_staff` (
  `city_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city_staff`
--

INSERT INTO `city_staff` (`city_id`, `staff_id`) VALUES
(18, 27),
(18, 47),
(21, 48),
(21, 50),
(19, 51),
(22, 52),
(19, 53),
(16, 54),
(18, 55),
(22, 56),
(17, 57),
(20, 58),
(19, 59),
(21, 60),
(18, 61),
(19, 62),
(18, 63),
(16, 64),
(22, 65),
(19, 66),
(22, 67),
(18, 68),
(21, 70),
(16, 71),
(18, 72),
(18, 73),
(21, 74),
(22, 75),
(17, 76),
(20, 77),
(16, 78),
(17, 78),
(16, 79),
(22, 79),
(18, 80),
(20, 80),
(16, 81),
(19, 81),
(21, 82),
(16, 83),
(22, 83),
(18, 84),
(20, 84),
(16, 85),
(22, 85),
(20, 86),
(16, 87),
(17, 87),
(22, 87),
(17, 88),
(21, 88),
(16, 89),
(19, 89),
(22, 89),
(22, 90),
(16, 91),
(17, 91),
(18, 91),
(19, 91),
(20, 91),
(21, 91),
(22, 91),
(16, 92),
(17, 92),
(18, 92),
(19, 92),
(20, 92),
(21, 92),
(22, 92),
(16, 93),
(17, 93),
(18, 93),
(19, 93),
(20, 93),
(21, 93),
(22, 93),
(19, 94),
(17, 95),
(18, 95),
(21, 95),
(16, 96),
(17, 96),
(18, 96),
(19, 96),
(20, 96),
(21, 96),
(22, 96),
(16, 97),
(17, 97),
(18, 97),
(19, 97),
(20, 97),
(21, 97),
(22, 97);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `pickup_address` text NOT NULL,
  `delivery_address` text NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `duration` int(11) DEFAULT 60,
  `booking_end_time` time DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `customer_name`, `customer_email`, `customer_phone`, `pickup_address`, `delivery_address`, `special_instructions`, `total_price`, `tax_amount`, `status`, `booking_date`, `booking_time`, `duration`, `booking_end_time`, `staff_id`, `city_id`, `created_at`) VALUES
(95, 'ORD-4B8EC', 'Muhammad Kamil', 'kzulfi968@gmail.com', '+923348242929', 'Shop 67, Block A, Insaf Shinwari plaza, Karkhano Peshawar', 'Shop 67, Block A, Insaf Shinwari plaza, Karkhano Peshawar', '', 879.98, 80.00, 'confirmed', '2026-01-16', '13:30:00', 240, NULL, NULL, 22, '2026-01-10 07:55:02'),
(96, 'ORD-4C550', 'Muhammad Kamil', 'kzulfi968@gmail.com', '+923348242929', 'Shop 67, Block A, Insaf Shinwari plaza, Karkhano Peshawar', 'Shop 67, Block A, Insaf Shinwari plaza, Karkhano Peshawar', '', 879.98, 80.00, 'pending', '2026-01-10', '15:30:00', 240, NULL, NULL, 17, '2026-01-10 07:57:14'),
(97, 'ORD-D1160', 'Moiz Javed', 'moiz1javed@gmail.com', '03120934577', 'kmk', 'kmk', '  jj', 3250.00, 750.00, 'pending', '2026-02-10', '15:00:00', 480, NULL, 61, 18, '2026-02-01 06:36:19'),
(98, 'ORD-33CFB', 'Haris Abdul Sattar', 'hariskh1835@gmail.com', '+923306466620', 'New Town Universty road ss club street no2 house no4 peshawar', 'New Town Universty road ss club street no2 house no4 peshawar', 'agar aj call krdien tu mn samjha deta hun kaam ke bary mn 2-3 walls ka paint krni hai.', 825.00, 75.00, 'confirmed', '2026-02-15', '11:00:00', 240, NULL, 64, 16, '2026-02-13 06:52:20');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price_at_booking` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `service_id`, `quantity`, `price_at_booking`) VALUES
(93, 95, 18, 1, 799.99),
(94, 96, 18, 1, 799.99),
(95, 97, 16, 1, 2500.00),
(96, 98, 18, 1, 750.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payer_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `payment_status` varchar(50) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'paypal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `gst_rate` decimal(5,2) DEFAULT 10.00,
  `icon` varchar(50) DEFAULT 'bi-box-seam',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price_unit` enum('item','kg') DEFAULT 'item',
  `duration` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`, `gst_rate`, `icon`, `status`, `created_at`, `price_unit`, `duration`) VALUES
(16, 'Construction', '', 0.00, 30.00, 'bi bi-house-gear', 'active', '2026-01-02 12:15:05', 'item', 30),
(17, 'Electrician', '', 0.00, 10.00, 'bi bi-lightning-charge', 'active', '2026-01-02 12:15:26', 'item', 30),
(18, 'Painter', '', 0.00, 10.00, 'bi bi-paint-bucket', 'active', '2026-01-02 13:18:32', 'item', 60),
(19, 'Plumber', '', 0.00, 10.00, 'bi bi-wrench-adjustable', 'active', '2026-01-02 13:18:46', 'item', 60),
(20, 'Solar Technician', '', 0.00, 10.00, 'bi bi-sun', 'active', '2026-01-02 13:19:18', 'item', 60);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'smtp_host', 'smtp.hostinger.com', '2025-11-28 12:45:40'),
(2, 'smtp_port', '465', '2025-11-28 12:45:40'),
(3, 'smtp_username', 'smtp@zouetech.co.uk', '2025-11-28 12:45:40'),
(4, 'smtp_password', 'Admin#$@1', '2025-11-28 12:45:40'),
(5, 'smtp_encryption', 'ssl', '2025-11-28 12:45:40'),
(6, 'from_email', 'smtp@zouetech.co.uk', '2025-11-28 12:45:40'),
(7, 'from_name', 'KamWala', '2026-01-12 05:44:32');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT 0.00,
  `city_id` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default_avatar.png',
  `status` enum('available','busy') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `working_start` time DEFAULT '09:00:00',
  `working_end` time DEFAULT '17:00:00',
  `working_days` varchar(255) DEFAULT 'Mon,Tue,Wed,Thu,Fri'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `phone`, `bio`, `daily_rate`, `city_id`, `avatar`, `status`, `created_at`, `working_start`, `working_end`, `working_days`) VALUES
(27, 'Qasim Khan', 'noreply@gmail.com', '0310-9860192', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 1499.96, NULL, 'staff_696681473d92c0.04266344.jpg', 'available', '2026-01-13 06:20:33', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(47, 'Nasir Khan', 'nasirkhan@gmail.com', '923018979399', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 2000.00, NULL, 'staff_6966811d6dd163.35134421.jpg', 'available', '2026-01-13 15:23:59', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(48, 'Ashfaq', 'ashfaq@gmail.com', '0300-9326544', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 1500.00, NULL, 'staff_6966816b31a484.34459818.jpg', 'available', '2026-01-13 15:25:49', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(50, 'Umar Farooq', 'umarfarooq@gmail.com', '0300-5968281', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 1999.97, NULL, 'staff_6966828d910e11.35662017.jpg', 'available', '2026-01-13 17:36:13', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(51, 'Musa', 'musa@gmail.com', '0306-8011225', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 2499.98, NULL, 'staff_6966831376d4d7.06299498.jpg', 'available', '2026-01-13 17:38:27', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(52, 'Ishfaq', 'ishfaq@gmail.com', '0333-9247411', 'Solar Specialist Bringing sustainable energy to your doorstep. Expert in seamless residential solar setups and battery storage solutions with a focus on safety.', 5500.00, NULL, 'staff_6966836f567cc1.99820974.jpg', 'available', '2026-01-13 17:39:59', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(53, 'Shahid', 'shahid@gmail.com', '0347-9111925', 'Solar Specialist Bringing sustainable energy to your doorstep. Expert in seamless residential solar setups and battery storage solutions with a focus on safety.', 5000.00, NULL, 'staff_696683aeb21f11.15367050.jpg', 'available', '2026-01-13 17:41:02', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(54, 'Saqib', 'saqib@gmail.com', '0306-8198449', 'Solar Specialist Bringing sustainable energy to your doorstep. Expert in seamless residential solar setups and battery storage solutions with a focus on safety.', 0.00, NULL, 'staff_696683fb507247.19247735.jpg', 'available', '2026-01-13 17:42:19', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(55, 'Uzair', 'uzair@gmail.com', '0316-9567161', 'Solar Specialist Bringing sustainable energy to your doorstep. Expert in seamless residential solar setups and battery storage solutions with a focus on safety.', 4999.99, NULL, 'staff_6966843b5c4a71.31872717.jpg', 'available', '2026-01-13 17:43:23', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(56, 'Munir', 'munir@gmail.com', '0317-4438790', 'Solar Specialist Bringing sustainable energy to your doorstep. Expert in seamless residential solar setups and battery storage solutions with a focus on safety.', 5500.00, NULL, 'staff_6966846d2b8ca2.18311451.jpg', 'available', '2026-01-13 17:44:13', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(57, 'Zahid', 'zahid@gmail.com', '0313-8303299', 'General Laborer Dedicated onsite professional with a focus on safety and efficiency. Experienced in site preparation, material handling, and supporting all phases of the build.', 2000.00, NULL, 'staff_6966850446a360.43601878.jpg', 'available', '2026-01-13 17:45:27', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(58, 'Hazrat', 'hazrat@gmail.com', '0313-6949487', 'General Laborer Dedicated onsite professional with a focus on safety and efficiency. Experienced in site preparation, material handling, and supporting all phases of the build.', 1199.99, NULL, 'staff_696684f07b1f52.16408240.jpg', 'available', '2026-01-13 17:46:24', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(59, 'Fazal', 'fazal@gmail.com', '0319-9972962', 'General Laborer Dedicated onsite professional with a focus on safety and efficiency. Experienced in site preparation, material handling, and supporting all phases of the build.', 1999.98, NULL, 'staff_696685390b09f2.40436612.jpg', 'available', '2026-01-13 17:47:37', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(60, 'Javed', 'javed@gmail.com', '0331-9313109', 'General Laborer Dedicated onsite professional with a focus on safety and efficiency. Experienced in site preparation, material handling, and supporting all phases of the build.', 1200.00, NULL, 'staff_69668560851669.55959432.jpg', 'available', '2026-01-13 17:48:16', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(61, 'Madi Gul', 'madigul@gmail.com', '0333-7223228', 'General Laborer Dedicated onsite professional with a focus on safety and efficiency. Experienced in site preparation, material handling, and supporting all phases of the build.', 2500.00, NULL, 'staff_6966859db5be09.22182049.jpg', 'available', '2026-01-13 17:49:17', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(62, 'Qazalbash', 'qazalbash@gmail.com', '0316-1227246', 'Custom Painter Bringing years of experience in color matching and surface restoration. Dedicated to reviving spaces with professional-grade craftsmanship.', 1999.99, NULL, 'staff_696685f9146354.73083130.jpg', 'available', '2026-01-13 17:50:49', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(63, 'Iqbal', 'iqbal@gmail.com', '0330-2263355', 'Custom Painter Bringing years of experience in color matching and surface restoration. Dedicated to reviving spaces with professional-grade craftsmanship.', 2200.00, NULL, 'staff_6966863099efc7.83953065.jpg', 'available', '2026-01-13 17:51:44', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(64, 'Khattak', 'khattak@gmail.com', '0315-1900185', 'Custom Painter Bringing years of experience in color matching and surface restoration. Dedicated to reviving spaces with professional-grade craftsmanship.', 1500.00, NULL, 'staff_69668663c86c87.00520409.jpg', 'available', '2026-01-13 17:52:35', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(65, 'Khan', 'khan@gmail.com', '0331-9899599', 'Custom Painter Bringing years of experience in color matching and surface restoration. Dedicated to reviving spaces with professional-grade craftsmanship.', 2000.00, NULL, 'staff_696686a527ef98.46829699.jpg', 'available', '2026-01-13 17:53:41', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(66, 'Jamal', 'jamal@gmail.com', '0313-9296947', 'Custom Painter Bringing years of experience in color matching and surface restoration. Dedicated to reviving spaces with professional-grade craftsmanship.', 2500.00, NULL, 'staff_696686d64b74f3.56343604.jpg', 'available', '2026-01-13 17:54:30', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(67, 'Mehran', 'mehran@gmail.com', '0315-9555693', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 2499.99, NULL, 'staff_6966871fbcdb50.34019267.jpg', 'available', '2026-01-13 17:55:43', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(68, 'Hammad Gul', 'hammadgul@gmail.com', '0311-9641387', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 2500.00, NULL, 'staff_696687c08fc9a1.32071325.jpg', 'available', '2026-01-13 17:56:50', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(70, 'Jahangir', 'jahangir@gmail.com', '0332-9637974', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 3000.00, NULL, 'staff_696688051be9f4.75400459.jpg', 'available', '2026-01-13 17:59:33', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(71, 'Hassan Ali', 'hassanali@gmail.com', '0313-9860461', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 2000.00, NULL, 'staff_69668925d6b4e4.53123651.jpg', 'available', '2026-01-13 18:03:57', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(72, 'Hamza', 'hamza@gmail.com', '0315-9616476', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 3500.00, NULL, 'staff_69668965272df1.38345656.jpg', 'available', '2026-01-13 18:05:25', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(73, 'Muhammad Umar', 'umar231@gmail.com', '0310-5140774', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 2500.00, NULL, 'staff_69725b140f3a18.96685256.jpg', 'available', '2026-01-22 17:15:00', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(74, 'Muhammad Zohaib', 'zohaib114@gmail.com', '0345-3966078', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 3000.00, NULL, 'staff_69725b650b9f94.20187879.jpg', 'available', '2026-01-22 17:16:21', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(75, 'Haq Nawaz', 'nawazhaq8689@gmail.com', '0348-9652325', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 3000.00, NULL, 'staff_69725bdd1d21b3.95539613.jpg', 'available', '2026-01-22 17:18:21', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(76, 'Zohaib Butt', 'zohaib756@gmail.com', '0321-5158221', 'Certified Electrician Specializing in residential and commercial wiring with years of experience. Committed to safety, precision, and high-quality electrical solutions.', 2500.00, NULL, 'staff_69725c5fa0a0d5.54204676.jpg', 'available', '2026-01-22 17:20:31', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(77, 'Ali Hassan', 'hassan099@gmail.com', '0309-8623787', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 1500.00, NULL, 'staff_69725cfc8a5cc6.90414102.jpg', 'available', '2026-01-22 17:23:08', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(78, 'Kamran Khan', 'kamran765@gmail.com', '0304-1111526', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 2499.99, NULL, 'staff_69725d3ed081f5.86742766.jpg', 'available', '2026-01-22 17:24:14', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(79, 'Irfan Ali', 'irfanali8976@gmail.com', '0310-9466707', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 3000.00, NULL, 'staff_69725dc2ac3d83.47922167.jpg', 'available', '2026-01-22 17:26:26', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(80, 'Gul Rasool', 'gulkhan0976@gmail.com', '0349-5654398', 'Plumbing Specialist Your trusted local plumber. Committed to honest pricing, quality repairs, and excellent customer service every time.', 1499.00, NULL, 'staff_69725e0cbd61f1.69322330.jpg', 'available', '2026-01-22 17:27:40', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(81, 'Waqar Khan', 'waqar908@gmail.com', '0334-9516334', 'Custom Painter Bringing years of experience in color matching and surface restoration. Dedicated to reviving spaces with professional-grade craftsmanship.', 2499.00, NULL, 'staff_69725e7c865fb0.13380697.jpg', 'available', '2026-01-22 17:29:32', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(82, 'Muhammad Ilyas', 'ilyaskhan0398@gmail.com', '0301-8989589', 'Custom Painter Bringing years of experience in color matching and surface restoration. Dedicated to reviving spaces with professional-grade craftsmanship.', 2000.00, NULL, 'staff_69725ebd5b6918.16808517.jpg', 'available', '2026-01-22 17:30:37', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(83, 'Zia ur Rehman', 'ziarehman564@gmail.com', '091-5275557', 'Custom Painter Bringing years of experience in color matching and surface restoration. Dedicated to reviving spaces with professional-grade craftsmanship.', 2499.99, NULL, 'staff_69725f147359c2.49477818.jpg', 'available', '2026-01-22 17:32:04', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(84, 'Haji Muhammad Ayoub', 'ayoub90809@gmail.com', '0300-5904567', 'General Laborer Dedicated onsite professional with a focus on safety and efficiency. Experienced in site preparation, material handling, and supporting all phases of the build.', 2500.00, NULL, 'staff_69725f7ae64932.85807666.jpg', 'available', '2026-01-22 17:33:46', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(85, 'Tavoos Khan', 'tavoos908@gmail.com', '0333-9103333', 'General Laborer Dedicated onsite professional with a focus on safety and efficiency. Experienced in site preparation, material handling, and supporting all phases of the build.', 2000.00, NULL, 'staff_69725fbc8c6b38.21884745.jpg', 'available', '2026-01-22 17:34:52', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(86, 'Fida Muhammad', 'fidaulah09807@gmail.com', '0333-9120514', 'General Laborer Dedicated onsite professional with a focus on safety and efficiency. Experienced in site preparation, material handling, and supporting all phases of the build.', 1200.00, NULL, 'staff_69726010f1a0d7.38359534.jpg', 'available', '2026-01-22 17:36:16', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(87, 'Sultan Khan', 'sultan903@gmail.com', '0333-9114565', 'Solar Specialist Bringing sustainable energy to your doorstep. Expert in seamless residential solar setups and battery storage solutions with a focus on safety.', 4499.99, NULL, 'staff_6972606a2973a2.77446910.jpg', 'available', '2026-01-22 17:37:46', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(88, 'Mushtaq Ahmad', 'ahmad02947@gmail.com', '0311-1112722', 'Solar Specialist Bringing sustainable energy to your doorstep. Expert in seamless residential solar setups and battery storage solutions with a focus on safety.', 4999.99, NULL, 'staff_6972609f8a4960.21719611.jpg', 'available', '2026-01-22 17:38:39', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(89, 'Inayat Ullah', 'inayatkhan094@gmail.com', '0311-7799002', 'Solar Specialist Bringing sustainable energy to your doorstep. Expert in seamless residential solar setups and battery storage solutions with a focus on safety.', 5500.00, NULL, 'staff_697260ddb19574.31284988.jpg', 'available', '2026-01-22 17:39:41', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(90, 'Umar Khan', 'Ukhan009912@gmail.com', '0310-5140774', 'Electrician with over 6 years of specialized experience in residential wiring and industrial pipe fitting. Based in Peshawar City, he handles complete home electrical maintenance, short-circuit repairs, and new fixture installations with a focus on safety and durability.', 2000.00, NULL, 'staff_69732600476818.45873533.webp', 'available', '2026-01-23 07:40:48', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(91, 'Future Solar Solutions (Technical Team)', 'futuresolar@gmail.com', '0333-1119003', 'Solar energy specialists located at Ring Road, Pishtakhara Chowk. They provide full management of solar panel installations, from initial site surveys to net metering and after-sales maintenance, ensuring clean and affordable energy for Peshawar residents.', 4000.00, NULL, 'default.png', 'available', '2026-01-23 07:49:02', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat'),
(92, 'PlumbingProspk', 'plumbingpro@gmail.com', '0309-8623787', 'Plumber service offering 24/7 emergency support across Peshawar (Zip 25000). Their technicians specialize in leak detection, water motor repairs, geyser installation, and sewer line cleaning, providing same-day service for urgent household plumbing issues.', 3000.00, NULL, 'staff_69732907117520.99577877.png', 'available', '2026-01-23 07:53:43', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat'),
(93, 'MyPaint', 'rehman332@gmail.com', '0301-8956501', 'Painter services specializing in both interior and exterior transformations. They offer high-quality weather-shield coatings for Peshawar’s climate, wood polishing, and metal enamel painting, utilizing digital color consultation to ensure a perfect finish.', 2500.00, NULL, 'staff_69732980711309.93535380.webp', 'available', '2026-01-23 07:55:44', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun'),
(94, 'MASHAQ Construction Company', 'MASHAQ@gmail.com', '0301-8956501', 'Construction firm based on Main University Road, Peshawar. Known for high-quality residential and commercial building projects, they manage everything from layout planning and grey structure development to final turnkey finishing.', 3000.00, NULL, 'default.png', 'available', '2026-01-23 07:58:14', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat'),
(95, 'SunSaviour Solar', 'SunSaviour333@gmail.com', '0322-4234235', 'Solar experts serving Saddar and Hayatabad for over 15 years. They focus on hybrid and on-grid solar systems, providing high-efficiency lithium batteries and inverters specifically designed to withstand the local climate of Khyber Pakhtunkhwa.', 5000.00, NULL, 'staff_69732a9335cc08.92654814.png', 'available', '2026-01-23 08:00:19', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat'),
(96, 'Saifco Builders &amp; Developers', 'saifco9756@gmail.com', '0345-9020011', 'Construction and real estate development leaders located on Ring Road. They specialize in modern architectural designs, commercial plaza development, and durable residential homes, focusing on timely delivery and sustainable building practices.', 3500.00, NULL, 'default.png', 'available', '2026-01-23 08:02:39', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat'),
(97, 'Atif Maintenance', 'muhammadatifmaintain@gmail.com', '0309-2396084', 'Plumber professionals offering verified and background-checked technicians for home repairs. Their services include PPR/PVC pipe installations, toilet and sink replacements, and water tank cleaning with a guarantee of transparent pricing.', 3000.00, NULL, 'staff_69732bad8fbc50.62706484.webp', 'available', '2026-01-23 08:05:01', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Sat,Sun');

-- --------------------------------------------------------

--
-- Table structure for table `staff_services`
--

CREATE TABLE `staff_services` (
  `staff_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_services`
--

INSERT INTO `staff_services` (`staff_id`, `service_id`) VALUES
(57, 16),
(58, 16),
(59, 16),
(60, 16),
(61, 16),
(84, 16),
(85, 16),
(86, 16),
(94, 16),
(96, 16),
(27, 17),
(47, 17),
(48, 17),
(50, 17),
(51, 17),
(73, 17),
(74, 17),
(75, 17),
(76, 17),
(90, 17),
(97, 17),
(62, 18),
(63, 18),
(64, 18),
(65, 18),
(66, 18),
(81, 18),
(82, 18),
(83, 18),
(86, 18),
(93, 18),
(97, 18),
(67, 19),
(68, 19),
(70, 19),
(71, 19),
(72, 19),
(77, 19),
(78, 19),
(79, 19),
(80, 19),
(92, 19),
(97, 19),
(52, 20),
(53, 20),
(54, 20),
(55, 20),
(56, 20),
(87, 20),
(88, 20),
(89, 20),
(91, 20),
(95, 20);

-- --------------------------------------------------------

--
-- Table structure for table `workflows`
--

CREATE TABLE `workflows` (
  `id` int(11) NOT NULL,
  `workflow_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `event_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflows`
--

INSERT INTO `workflows` (`id`, `workflow_name`, `status`, `event_type`, `created_at`, `updated_at`) VALUES
(1, 'Book Notification', 'active', 'order_booked', '2025-11-27 12:56:06', '2025-11-27 12:56:06'),
(2, 'Admin Book order notification', 'active', 'order_booked', '2025-11-27 12:57:09', '2025-11-27 12:57:09'),
(5, 'staff notification', 'inactive', 'order_booked', '2025-11-28 13:51:40', '2025-12-29 12:03:15'),
(6, 'Order Completed Notification', 'active', 'order_completed', '2025-12-29 12:14:48', '2025-12-29 12:14:48'),
(7, 'Order Cancelled Notification', 'active', 'order_cancelled', '2025-12-29 12:28:33', '2025-12-29 12:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_actions`
--

CREATE TABLE `workflow_actions` (
  `id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `action_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow_actions`
--

INSERT INTO `workflow_actions` (`id`, `workflow_id`, `action_type`, `action_data`, `created_at`) VALUES
(1, 1, 'send_email', NULL, '2025-11-27 12:56:06'),
(2, 2, 'send_email', NULL, '2025-11-27 12:57:09'),
(4, 5, 'send_email', NULL, '2025-11-28 13:51:40'),
(5, 6, 'send_email', NULL, '2025-12-29 12:14:48'),
(6, 7, 'send_email', NULL, '2025-12-29 12:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_activity_log`
--

CREATE TABLE `workflow_activity_log` (
  `id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) DEFAULT NULL,
  `status` enum('success','failed') NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow_activity_log`
--

INSERT INTO `workflow_activity_log` (`id`, `workflow_id`, `order_id`, `action_type`, `status`, `details`, `created_at`) VALUES
(196, 1, 95, 'send_email', 'failed', 'Message could not be sent. Mailer Error: SMTP Error: data not accepted. SMTP server error: DATA END command failed Detail: Disabled by user from hPanel\r\n SMTP code: 554 Additional SMTP info: 5.7.1', '2026-01-10 07:55:03'),
(197, 2, 95, 'send_email', 'failed', 'Message could not be sent. Mailer Error: SMTP Error: data not accepted. SMTP server error: DATA END command failed Detail: Disabled by user from hPanel\r\n SMTP code: 554 Additional SMTP info: 5.7.1', '2026-01-10 07:55:04'),
(198, 1, 96, 'send_email', 'failed', 'Message could not be sent. Mailer Error: SMTP Error: data not accepted. SMTP server error: DATA END command failed Detail: Disabled by user from hPanel\r\n SMTP code: 554 Additional SMTP info: 5.7.1', '2026-01-10 07:57:15'),
(199, 2, 96, 'send_email', 'failed', 'Message could not be sent. Mailer Error: SMTP Error: data not accepted. SMTP server error: DATA END command failed Detail: Disabled by user from hPanel\r\n SMTP code: 554 Additional SMTP info: 5.7.1', '2026-01-10 07:57:16'),
(200, 1, 97, 'send_email', 'failed', 'Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2026-02-01 06:36:22'),
(201, 2, 97, 'send_email', 'failed', 'Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2026-02-01 06:36:24'),
(202, 1, 98, 'send_email', 'failed', 'Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2026-02-13 06:52:22'),
(203, 2, 98, 'send_email', 'failed', 'Message could not be sent. Mailer Error: SMTP Error: Could not authenticate.', '2026-02-13 06:52:24');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_emails`
--

CREATE TABLE `workflow_emails` (
  `id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `to_email_field` varchar(255) NOT NULL,
  `email_subject` varchar(500) NOT NULL,
  `email_content` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow_emails`
--

INSERT INTO `workflow_emails` (`id`, `action_id`, `to_email_field`, `email_subject`, `email_content`, `created_at`, `updated_at`) VALUES
(1, 1, '{{customer_email}}', 'your Dr.Spin Laundry Booking is Confirmed! (Order #{{order_code}})', '<p data-path-to-node=\"4,3,1,0\">Dear <strong>{{customer_name}}</strong>,</p>\r\n<p data-path-to-node=\"4,3,1,3\">Thank you for choosing CleanPress! Your laundry booking has been successfully confirmed.</p>\r\n<p data-path-to-node=\"4,3,1,6\">Here are your booking details:</p>\r\n<p data-path-to-node=\"4,3,1,8\">---</p>\r\n<p data-path-to-node=\"4,3,1,10\"><strong>Order Confirmation Code:</strong> <strong>{{order_code}}</strong></p>\r\n<p data-path-to-node=\"4,3,1,12\"><strong>Booking Date &amp; Time:</strong> <strong>{{booking_date}}</strong></p>\r\n<p data-path-to-node=\"4,3,1,14\"><strong>Total Estimated Price:</strong> <strong>{{total_price}}</strong></p>\r\n<p data-path-to-node=\"4,3,1,16\">---</p>\r\n<p data-path-to-node=\"4,3,1,18\">Our staff will arrive promptly at the scheduled time. Please ensure your items are ready for pickup.</p>\r\n<p data-path-to-node=\"4,3,1,21\">If you have any questions, please reply to this email.</p>\r\n<p data-path-to-node=\"4,3,1,24\">Thank you,</p>\r\n<p data-path-to-node=\"4,3,1,26\">The Dr.spin Team</p>', '2025-11-27 12:56:06', '2025-12-29 12:23:46'),
(2, 2, '{{admin_email}}', '🔔 New Order Received: #{{order_code}}', '<p data-path-to-node=\"8,3,1,0\">Hello Admin,</p>\r\n<p data-path-to-node=\"8,3,1,3\">A new order has just been successfully placed through the CleanPress booking system.</p>\r\n<p data-path-to-node=\"8,3,1,6\">Please find the details below for processing and staff assignment:</p>\r\n<p data-path-to-node=\"8,3,1,8\">---</p>\r\n<p data-path-to-node=\"8,3,1,10\"><strong>Order Code:</strong> <strong>{{order_code}}</strong></p>\r\n<p data-path-to-node=\"8,3,1,12\"><strong>Customer Name:</strong> <strong>{{customer_name}}</strong></p>\r\n<p data-path-to-node=\"8,3,1,14\"><strong>Customer Email:</strong> <strong>{{customer_email}}</strong></p>\r\n<p data-path-to-node=\"8,3,1,16\"><strong>Booked Date &amp; Time:</strong> <strong>{{booking_date}}</strong></p>\r\n<p data-path-to-node=\"8,3,1,18\"><strong>Estimated Revenue:</strong> <strong>{{total_price}}</strong></p>\r\n<p data-path-to-node=\"8,3,1,20\">---</p>\r\n<p data-path-to-node=\"8,3,1,22\">Please log into the Admin Dashboard to assign staff, view service details, and manage the order fulfillment process.</p>\r\n<p data-path-to-node=\"8,3,1,25\">Thank you,</p>\r\n<p data-path-to-node=\"8,3,1,27\">Dr. Spin System Notification</p>', '2025-11-27 12:57:09', '2025-12-29 12:23:13'),
(4, 4, '{{staff_email}}', 'New Booking Assignment: Order #{{order_code}}', '<p>You have been assigned a new service booking. Please review all the details below and ensure timely preparation for the service.</p>\r\n<p><span data-path-to-node=\"10,1,0,0\"><strong>Customer Name</strong></span><span data-path-to-node=\"10,1,1,0\">{{customer_name}}</span><span data-path-to-node=\"10,2,0,0\"><strong>Phone Number</strong></span><span data-path-to-node=\"10,2,1,0\">{{customer_phone}}</span><span data-path-to-node=\"10,3,0,0\"><strong>Email</strong></span><span data-path-to-node=\"10,3,1,0\">{{customer_email}}</span></p>\r\n<p><span data-path-to-node=\"10,3,1,0\"><span data-path-to-node=\"13,1,0,0\"><strong>Pickup Location</strong></span><span data-path-to-node=\"13,1,1,0\">{{pickup_address}}</span><span data-path-to-node=\"13,2,0,0\"><strong>Delivery Location</strong></span><span data-path-to-node=\"13,2,1,0\">{{delivery_address}}</span><span data-path-to-node=\"13,3,0,0\"><strong>City</strong></span><span data-path-to-node=\"13,3,1,0\">{{city_name}}</span></span></p>', '2025-11-28 13:51:40', '2025-11-28 13:51:40'),
(5, 5, '{{customer_email}}', 'Order Completed', '<p data-start=\"198\" data-end=\"223\">Dear {{customer_name}},</p>\r\n<p data-start=\"225\" data-end=\"413\">We are pleased to inform you that your order <strong data-start=\"270\" data-end=\"288\">{{order_code}}</strong> has been completed. The total amount for this order is <strong data-start=\"344\" data-end=\"363\">{{total_price}}</strong>, and the booking date was <strong data-start=\"390\" data-end=\"410\">{{booking_date}}</strong>.</p>\r\n<p data-start=\"415\" data-end=\"513\">To proceed with the delivery, please make the payment using the following PayID: <strong data-start=\"496\" data-end=\"510\">0431500738</strong>.</p>\r\n<p data-start=\"515\" data-end=\"583\">Once the payment is received, we will promptly deliver your order.</p>\r\n<p data-start=\"585\" data-end=\"643\">Thank you for your cooperation and trust in our service.</p>\r\n<p data-start=\"645\" data-end=\"684\">Best regards,<br data-start=\"658\" data-end=\"661\"><strong data-start=\"661\" data-end=\"682\">The Dr. Spin Team</strong></p>', '2025-12-29 12:14:48', '2025-12-29 14:36:31'),
(6, 6, '{{customer_email}}', 'Order Cancelled', 'Your order has been cancelled. If you have any questions, please contact us.', '2025-12-29 12:28:33', '2025-12-29 12:28:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `city_staff`
--
ALTER TABLE `city_staff`
  ADD PRIMARY KEY (`city_id`,`staff_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_transaction` (`transaction_id`),
  ADD KEY `idx_payment_order` (`order_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `staff_services`
--
ALTER TABLE `staff_services`
  ADD PRIMARY KEY (`staff_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `workflows`
--
ALTER TABLE `workflows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `workflow_actions`
--
ALTER TABLE `workflow_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_workflow_id` (`workflow_id`);

--
-- Indexes for table `workflow_activity_log`
--
ALTER TABLE `workflow_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_workflow_id` (`workflow_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `workflow_emails`
--
ALTER TABLE `workflow_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_action_id` (`action_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `workflows`
--
ALTER TABLE `workflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `workflow_actions`
--
ALTER TABLE `workflow_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `workflow_activity_log`
--
ALTER TABLE `workflow_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `workflow_emails`
--
ALTER TABLE `workflow_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `city_staff`
--
ALTER TABLE `city_staff`
  ADD CONSTRAINT `city_staff_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `city_staff_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_services`
--
ALTER TABLE `staff_services`
  ADD CONSTRAINT `staff_services_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workflow_actions`
--
ALTER TABLE `workflow_actions`
  ADD CONSTRAINT `workflow_actions_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workflow_activity_log`
--
ALTER TABLE `workflow_activity_log`
  ADD CONSTRAINT `workflow_activity_log_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workflow_emails`
--
ALTER TABLE `workflow_emails`
  ADD CONSTRAINT `workflow_emails_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `workflow_actions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
