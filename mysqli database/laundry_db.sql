-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2026 at 08:28 AM
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
-- Database: `laundry_db`
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
(4, 'zulkif', '$2y$10$ifiOMI.LRmTHjE7cI9JPeu0ksyY3kZtEPYbi8XMXqj5eyPix4xspe', '2025-11-27 12:53:06', 'zulkif', 'kzulfi968@gmail.com', '03348242929'),
(6, 'haris', '$2y$10$zo2NMipEFC8dH2Xl7LUF1.uFUPgjeqG/ySiAS9rTNbY7GydW/j0jC', '2025-12-11 13:46:15', 'Haris', 'haris@gmail.com', '033333');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
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

INSERT INTO `bookings` (`id`, `user_id`, `user_name`, `provider_id`, `service_type`, `booking_date`, `booking_time`, `location`, `description`, `status`, `created_at`) VALUES
(1, 'mobile_user_1', 'Jane Doe', NULL, NULL, '2026-01-12', '09:00:00', '456 Mobile St', 'Test Booking 1', 'confirmed', '2026-01-10 06:48:25'),
(2, 'mobile_user_2', 'John Smith', NULL, NULL, '2026-01-13', '10:30:00', '789 App Ave', 'Test Booking 2', 'pending', '2026-01-10 06:48:25');

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
(16, 21),
(16, 22),
(16, 23),
(16, 25),
(17, 21),
(17, 22),
(17, 23),
(17, 24),
(17, 25),
(17, 26),
(18, 21),
(18, 22),
(18, 23),
(18, 25),
(18, 26),
(19, 21),
(19, 22),
(19, 23),
(19, 25),
(20, 21),
(20, 22),
(20, 23),
(20, 25),
(20, 26),
(21, 21),
(21, 22),
(21, 23),
(21, 25),
(22, 21),
(22, 22),
(22, 23),
(22, 25);

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
(7, 'from_name', 'DR.SPIN', '2025-11-28 12:45:40');

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
(21, 'Shafqat Ali', 'shafqall2i@gmail.com', '', '', 1599.97, NULL, 'staff_6957d375d7ebb3.01038520.jpg', 'available', '2026-01-02 14:15:56', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun'),
(22, 'Junaid Wazir', 'junaid@gmail.com', '', '', 1399.97, NULL, 'staff_6957d3667eb910.72796973.jpg', 'available', '2026-01-02 14:17:10', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun'),
(23, 'Ahmad Khan', 'ahmas@gmail.com', '', '', 1500.00, NULL, 'staff_6957d39ed4b763.25179239.jpg', 'available', '2026-01-02 14:18:06', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun'),
(24, 'Arif Ahmed', 'aruis@gmail.com', '', '', 12000.00, NULL, 'staff_6957d3b8c90591.03277666.jpeg', 'available', '2026-01-02 14:18:32', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun'),
(25, 'Junaid Wazir', 'junai2@gmail.com', '', '', 1399.98, NULL, 'default.png', 'available', '2026-01-02 14:19:26', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun'),
(26, 'ALi Khan', 'kalis@gmail.com', '', '', 1499.97, NULL, 'default.png', 'available', '2026-01-02 14:20:00', '09:00:00', '17:00:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun');

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
(21, 18),
(22, 20),
(23, 17),
(24, 19),
(25, 20),
(26, 19);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

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
