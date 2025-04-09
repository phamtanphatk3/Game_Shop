-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2025 at 04:30 PM
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
-- Database: `game_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'standard',
  `game_type` varchar(50) NOT NULL DEFAULT 'genshin',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `account_name`, `type`, `game_type`, `username`, `password`, `price`, `image`, `description`) VALUES
(13, NULL, NULL, 'STARTER', 'genshin', 'phat.2000@gmail.com', '123', 10000000.00, 'sale1.png', 'phat an cut'),
(14, NULL, NULL, 'VIP_PLAYER 3', 'game1', '123', '123', 123.00, '', '123'),
(15, NULL, NULL, 'VIP', 'game1', 'admin', '123', 123.00, '', '1234'),
(16, NULL, NULL, 'VIP', 'game1', '123', '123', 123.00, '', '4654646'),
(18, NULL, NULL, 'VIP', 'genshin', '123', '123', 123.00, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `transaction_type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
  `card_provider` varchar(50) NOT NULL DEFAULT 'unknown',
  `card_code` varchar(100) NOT NULL,
  `card_serial` varchar(100) NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `transfer_type` enum('bank','card','paypal','momo') NOT NULL DEFAULT 'bank',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `account_username` varchar(255) DEFAULT NULL,
  `account_password` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `transaction_type`, `amount`, `transaction_date`, `username`, `payment_method`, `card_provider`, `card_code`, `card_serial`, `status`, `transfer_type`, `created_at`, `account_username`, `account_password`, `password`, `updated_at`) VALUES
(8, 1, NULL, 10000.00, '2025-03-10 05:43:44', 'ha', 'card', 'viettel', '1231231', '12312312', 'completed', 'bank', '2025-03-10 05:43:44', NULL, NULL, '', '2025-03-25 15:59:43'),
(9, 1, NULL, 10000.00, '2025-03-10 05:43:56', 'ha', 'card', 'viettel', '1231231', '123123', 'completed', 'bank', '2025-03-10 05:43:56', NULL, NULL, '', '2025-03-25 15:59:43'),
(10, 1, NULL, 10000.00, '2025-03-10 05:44:23', '', 'bank_transfer', 'unknown', '', '', 'completed', 'bank', '2025-03-10 05:44:23', NULL, NULL, '', '2025-03-25 15:59:43'),
(18, 1, NULL, 10000.00, '2025-03-10 06:13:45', 'ha', 'card', 'viettel', '1231231', '12312312', '', 'bank', '2025-03-10 06:13:45', NULL, NULL, '', '2025-03-25 15:59:43'),
(19, 1, NULL, 10000.00, '2025-03-10 06:17:22', 'ha', 'card', 'viettel', '123', '12', '', 'bank', '2025-03-10 06:17:22', NULL, NULL, '', '2025-03-25 15:59:43'),
(20, 1, NULL, 10000.00, '2025-03-10 06:20:02', 'ha', 'card', 'viettel', '12', '123', '', 'bank', '2025-03-10 06:20:02', NULL, NULL, '', '2025-03-25 15:59:43'),
(21, NULL, NULL, NULL, '2025-03-10 06:35:23', 'admin', 'cash', 'unknown', '', '', 'pending', 'bank', '2025-03-10 06:35:23', NULL, NULL, '2132', '2025-03-25 15:59:43'),
(22, 1, NULL, 10000.00, '2025-03-10 06:39:12', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-10 06:39:12', 'admin', '2132', '', '2025-03-25 15:59:43'),
(23, 1, NULL, 1000000.00, '2025-03-10 06:41:49', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-10 06:41:49', 'admin123', '2132', '', '2025-03-25 15:59:43'),
(24, 1, NULL, 1000000.00, '2025-03-10 06:42:31', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-10 06:42:31', 'admin123', '2132', '', '2025-03-25 15:59:43'),
(25, 1, NULL, 500000.00, '2025-03-10 06:45:23', 'ha', 'card', 'viettel', '1231231', '123123', '', 'bank', '2025-03-10 06:45:23', NULL, NULL, '', '2025-03-25 15:59:43'),
(26, 1, NULL, 500000.00, '2025-03-10 06:45:39', 'ha', 'card', 'viettel', '1231231', '123123', '', 'bank', '2025-03-10 06:45:39', NULL, NULL, '', '2025-03-25 15:59:43'),
(27, 1, NULL, 1000000.00, '2025-03-10 06:45:56', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-10 06:45:56', 'admin123', '2132', '', '2025-03-25 15:59:43'),
(28, 1, NULL, 100000.00, '2025-03-10 06:48:26', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-10 06:48:26', 'admin', '2312312', '', '2025-03-25 15:59:43'),
(29, 1, NULL, 3123.00, '2025-03-10 07:26:58', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-10 07:26:58', '1231', '2312', '', '2025-03-25 15:59:43'),
(30, 1, NULL, 3123.00, '2025-03-10 07:27:45', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-10 07:27:45', '1231', '2312', '', '2025-03-25 15:59:43'),
(31, 1, NULL, 500000.00, '2025-03-11 06:12:14', 'ha', 'card', 'viettel', '1231231', '123123', '', 'bank', '2025-03-11 06:12:14', NULL, NULL, '', '2025-03-25 15:59:43'),
(32, 1, NULL, 3123.00, '2025-03-11 08:35:21', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-11 08:35:21', '1231', '2312', '', '2025-03-25 15:59:43'),
(33, 1, NULL, 100000.00, '2025-03-11 08:35:31', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-11 08:35:31', 'kirito123', 'asdasdad', '', '2025-03-25 15:59:43'),
(34, 1, NULL, 320000.00, '2025-03-11 08:37:16', '', 'bank_transfer', 'unknown', '', '', '', '', '2025-03-11 08:37:16', NULL, NULL, '', '2025-03-25 15:59:43'),
(35, 1, NULL, 100000.00, '2025-03-11 08:39:28', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-11 08:39:28', 'phatbeo', '123', '', '2025-03-25 15:59:43'),
(36, 1, NULL, 3123.00, '2025-03-13 06:04:39', '', 'cash', 'unknown', '', '', 'completed', 'bank', '2025-03-13 06:04:39', '1231', '2312', '', '2025-03-25 15:59:43'),
(37, 1, NULL, 10000.00, '2025-03-13 06:05:18', '', 'bank_transfer', 'unknown', '', '', '', '', '2025-03-13 06:05:18', NULL, NULL, '', '2025-03-25 15:59:43'),
(38, 1, NULL, 10000.00, '2025-03-25 15:53:40', 'ha', 'card', 'viettel', '123', '123', '', 'bank', '2025-03-25 15:53:40', NULL, NULL, '', '2025-03-25 15:59:43'),
(39, 1, NULL, 10000.00, '2025-03-25 15:56:38', 'ha', 'card', 'viettel', '1231231', '12312312', '', 'bank', '2025-03-25 15:56:38', NULL, NULL, '', '2025-03-25 15:59:43'),
(40, 1, NULL, 10000.00, '2025-03-25 15:58:52', 'ha', 'card', 'viettel', '1', '123', '', 'bank', '2025-03-25 15:58:52', NULL, NULL, '', '2025-03-25 15:59:51'),
(41, 1, NULL, 10000.00, '2025-03-25 16:00:00', 'ha', 'card', 'viettel', '123', '123', '', 'bank', '2025-03-25 16:00:00', NULL, NULL, '', '2025-03-25 16:00:08'),
(42, 1, NULL, 10000.00, '2025-03-25 16:01:17', 'ha', 'card', 'viettel', '1231231', '12312312', '', 'bank', '2025-03-25 16:01:17', NULL, NULL, '', '2025-03-25 16:01:22'),
(43, 1, NULL, 10000.00, '2025-03-25 16:03:51', '', 'bank_transfer', 'unknown', '', '', 'completed', 'bank', '2025-03-25 16:03:51', NULL, NULL, '', '2025-03-25 16:03:57'),
(44, 1, NULL, 10000.00, '2025-03-25 16:04:04', 'ha', 'card', 'viettel', '1231231', '12312312', 'completed', 'bank', '2025-03-25 16:04:04', NULL, NULL, '', '2025-03-25 16:04:08'),
(45, 1, NULL, 160000.00, '2025-03-29 15:06:31', '', 'bank_transfer', 'unknown', '', '', 'completed', '', '2025-03-29 15:06:31', NULL, NULL, '', '2025-03-29 15:06:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `type` varchar(50) NOT NULL DEFAULT 'standard',
  `avatar` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `google_id`, `facebook_id`, `name`, `email`, `balance`, `type`, `avatar`, `reset_token`, `reset_token_expires`, `token_expires`, `is_deleted`, `deleted_at`) VALUES
(1, 'ha', '$2y$10$GQR/AQuJgMhwBcGz0jked.PHiqjCUQDm5zIvgTNXt60EYw24yaINy', 'admin', '105052284277085212726', NULL, NULL, 'taiha80999@gmail.com', 877508.00, 'standard', 'uploads/1743094407_OIP.jpg', NULL, NULL, '0000-00-00 00:00:00', 0, NULL),
(2, 'kirito123', '$2y$10$5ingGqkDYAb1mHSPzAP9c.on1qoPthzaHl3MLt3Y62WTka1wC0lrq', 'user', NULL, NULL, NULL, 'vinhnp.longan@gmail.com', 0.00, 'standard', NULL, NULL, NULL, NULL, 0, NULL),
(3, 'taiha80999@gmail.com', '$2y$10$HadSItlng8y1xJFVtjBY7eeu2u9rdaaQL57/YBUqacvmrrskAB1uS', 'user', '114469137948402476112', NULL, NULL, 'hatai3300@gmail.com', 0.00, 'standard', NULL, NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_requests`
--

CREATE TABLE `user_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_name` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `map_name` varchar(255) DEFAULT NULL,
  `story_name` varchar(255) DEFAULT NULL,
  `price` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `service_type` enum('event','map','story') NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `is_bot` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_requests`
--

INSERT INTO `user_requests` (`id`, `user_id`, `game_name`, `event_name`, `map_name`, `story_name`, `price`, `status`, `created_at`, `updated_at`, `service_type`, `image_path`, `service_name`, `is_bot`) VALUES
(1, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 09:52:08', '2025-03-10 09:58:26', 'event', NULL, NULL, 0),
(2, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'approved', '2025-03-10 09:52:31', '2025-03-10 09:58:27', 'event', NULL, NULL, 0),
(3, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'approved', '2025-03-10 09:54:46', '2025-03-10 09:58:28', 'event', NULL, NULL, 0),
(4, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 09:55:26', '2025-03-10 10:12:28', 'event', NULL, NULL, 0),
(5, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:03:51', '2025-03-10 10:12:28', 'event', NULL, NULL, 0),
(6, 1, 'Không xác định', 'Hoàn thành sự kiện B', NULL, NULL, '750.000 VNĐ', 'rejected', '2025-03-10 10:05:39', '2025-03-10 10:12:29', 'event', NULL, NULL, 0),
(7, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:10:31', '2025-03-10 10:12:29', 'event', NULL, NULL, 0),
(8, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:10:50', '2025-03-10 10:12:29', 'event', NULL, NULL, 0),
(9, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:11:10', '2025-03-10 10:12:29', 'event', NULL, NULL, 0),
(10, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:12:22', '2025-03-10 10:14:53', 'event', NULL, NULL, 0),
(11, 1, 'Không xác định', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:14:43', '2025-03-10 10:14:53', 'event', NULL, NULL, 0),
(12, 1, 'Genshin Impact', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'approved', '2025-03-10 10:14:48', '2025-03-11 06:11:47', 'event', NULL, NULL, 0),
(13, 1, 'Genshin Impact', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:15:32', '2025-03-11 06:11:49', 'event', NULL, NULL, 0),
(14, 1, 'Genshin Impact', 'Hoàn thành sự kiện A', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:19:55', '2025-03-11 06:11:49', 'event', NULL, NULL, 0),
(15, 1, 'Không xác định', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-10 10:25:55', '2025-03-11 06:11:49', 'event', NULL, 'Mở khóa bản đồ khu vực 1', 0),
(16, 1, 'Genshin Impact', '', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:26:08', '2025-03-11 06:11:50', 'event', NULL, 'Mở khóa bản đồ khu vực 2', 0),
(17, 1, 'Genshin Impact', 'Mở khóa bản đồ khu vực 3', NULL, NULL, '800.000 VNĐ', 'rejected', '2025-03-10 10:26:27', '2025-03-11 06:11:50', 'event', NULL, NULL, 0),
(18, 1, 'Genshin Impact', 'Mở khóa bản đồ khu vực 2', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:26:35', '2025-03-11 06:11:50', 'event', NULL, NULL, 0),
(19, 1, 'Genshin Impact', 'Mở khóa bản đồ khu vực 3', NULL, NULL, '800.000 VNĐ', 'rejected', '2025-03-10 10:26:40', '2025-03-11 06:11:51', 'event', NULL, NULL, 0),
(20, 1, 'Genshin Impact', 'Mở khóa bản đồ khu vực 2', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-10 10:26:43', '2025-03-11 06:11:51', 'event', NULL, NULL, 0),
(21, 1, 'Genshin Impact', 'Mở khóa bản đồ khu vực 1', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-10 10:26:55', '2025-03-11 06:11:51', 'event', NULL, NULL, 0),
(22, 1, 'Genshin Impact', '', NULL, 'Hoàn thành cốt truyện chương 1', '500.000 VNĐ', 'rejected', '2025-03-10 10:30:49', '2025-03-11 06:11:52', 'story', NULL, NULL, 0),
(23, 1, 'Không xác định', '', NULL, 'Hoàn thành cốt truyện chương 1', '500.000 VNĐ', 'rejected', '2025-03-10 10:31:18', '2025-03-10 10:33:30', 'story', NULL, NULL, 0),
(24, 1, 'Genshin Impact', '', 'Mở khóa bản đồ khu vực 1', NULL, '300.000 VNĐ', 'rejected', '2025-03-10 10:33:21', '2025-03-11 06:11:52', 'map', NULL, NULL, 0),
(25, 1, 'Genshin Impact', '', NULL, NULL, '500.000 VNĐ', 'approved', '2025-03-11 06:11:27', '2025-03-11 06:11:42', 'event', NULL, 'Mở khóa bản đồ khu vực 2', 0),
(26, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-11 11:45:59', '2025-03-11 11:46:18', 'event', NULL, 'Mở khóa bản đồ khu vực 1', 0),
(27, 1, 'Genshin Impact', '', NULL, 'Hoàn thành cốt truyện chương 1', '500.000 VNĐ', 'rejected', '2025-03-11 11:46:07', '2025-03-11 11:46:18', 'story', NULL, NULL, 0),
(28, 1, 'Honkai: Star Rail', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-11 11:46:38', '2025-03-11 11:46:53', 'event', NULL, 'Mở khóa bản đồ khu vực 1', 0),
(29, 1, 'Zenless Zone Zero', '', NULL, 'Hoàn thành cốt truyện chương 2', '700.000 VNĐ', 'rejected', '2025-03-11 11:46:42', '2025-03-11 11:46:53', 'story', NULL, NULL, 0),
(30, 1, 'Zenless Zone Zero', '', 'Mở khóa bản đồ khu vực 3', NULL, '800.000 VNĐ', 'rejected', '2025-03-11 11:46:46', '2025-03-11 11:46:53', 'map', NULL, NULL, 0),
(31, 1, 'Honkai: Star Rail', '', 'Mở khóa bản đồ khu vực 3', NULL, '800.000 VNĐ', 'rejected', '2025-03-12 05:57:21', '2025-03-12 06:04:21', 'map', NULL, NULL, 0),
(32, 1, 'Genshin Impact', '', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-12 06:00:18', '2025-03-12 06:04:21', 'event', NULL, 'Hoàn thành sự kiện 2', 0),
(33, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:00:39', '2025-03-12 06:04:21', 'event', NULL, 'Hoàn thành sự kiện 1', 0),
(34, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:01:50', '2025-03-12 06:04:22', 'event', NULL, 'Hoàn thành sự kiện 1', 0),
(35, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:02:35', '2025-03-12 06:04:22', 'event', NULL, 'Mở khóa bản đồ khu vực 1', 0),
(36, 1, 'Genshin Impact', '', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-12 06:02:39', '2025-03-12 06:04:22', 'event', NULL, 'Hoàn thành sự kiện 2', 0),
(37, 1, 'Không xác định', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:03:19', '2025-03-12 06:04:22', 'event', NULL, 'Hoàn thành sự kiện 1', 0),
(38, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:04:14', '2025-03-12 06:04:23', 'event', NULL, 'Mở khóa bản đồ khu vực 1', 0),
(39, 1, 'Genshin Impact', '', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-12 06:04:30', '2025-03-12 06:13:24', 'event', NULL, 'Mở khóa bản đồ khu vực 2', 0),
(40, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:04:49', '2025-03-12 06:13:24', 'event', NULL, 'Mở khóa bản đồ khu vực 1', 0),
(41, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:05:51', '2025-03-12 06:13:23', 'event', NULL, 'Hoàn thành sự kiện 1', 0),
(42, 1, 'Genshin Impact', '', NULL, 'Hoàn thành cốt truyện chương 1', '500.000 VNĐ', 'rejected', '2025-03-12 06:06:05', '2025-03-12 06:13:23', 'story', NULL, NULL, 0),
(43, 1, 'Genshin Impact', '', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-12 06:06:17', '2025-03-12 06:13:23', 'event', NULL, 'Mở khóa bản đồ khu vực 2', 0),
(44, 1, 'Không xác định', '', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-12 06:06:47', '2025-03-12 06:13:25', 'event', NULL, 'Mở khóa bản đồ khu vực 2', 0),
(45, 1, 'Không xác định', '', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-12 06:06:57', '2025-03-12 06:13:22', 'event', NULL, 'Mở khóa bản đồ khu vực 2', 0),
(46, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:13:11', '2025-03-12 06:13:21', 'event', NULL, 'Mở khóa bản đồ khu vực 1', 0),
(47, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 06:13:35', '2025-03-12 07:07:40', 'event', NULL, 'Mở khóa bản đồ khu vực 1', 0),
(48, 1, 'Genshin Impact', '', NULL, NULL, '800.000 VNĐ', 'rejected', '2025-03-12 06:13:41', '2025-03-12 07:07:41', 'event', NULL, 'Mở khóa bản đồ khu vực 3', 0),
(49, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'rejected', '2025-03-12 07:02:38', '2025-03-12 07:07:42', '', '/images/map/norfall_barrens.png', NULL, 0),
(50, 1, 'Genshin Impact', '', NULL, NULL, '500.000 VNĐ', 'rejected', '2025-03-12 07:02:43', '2025-03-12 07:07:42', '', '/images/map/desorock_highland.png', NULL, 0),
(51, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'pending', '2025-03-12 07:07:57', '2025-03-12 07:07:57', '', '/images/map/norfall_barrens.png', NULL, 0),
(52, 1, 'Genshin Impact', '', NULL, NULL, '500.000 VNĐ', 'pending', '2025-03-12 07:08:13', '2025-03-12 07:08:13', 'event', NULL, 'Hoàn thành sự kiện 2', 0),
(53, 1, 'Genshin Impact', '', NULL, NULL, '300.000 VNĐ', 'pending', '2025-03-12 07:08:17', '2025-03-12 07:08:17', 'event', NULL, 'Hoàn thành sự kiện 1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_requests`
--
ALTER TABLE `user_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_requests`
--
ALTER TABLE `user_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
