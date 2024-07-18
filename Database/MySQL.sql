-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 18, 2024 at 09:11 PM
-- Server version: 10.3.37-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `misymqsf_misy`
--

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Application`
--

CREATE TABLE `Ms_Application` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(64) NOT NULL,
  `grade` enum('0','9','10','11','12','99') NOT NULL DEFAULT '0',
  `city` varchar(64) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `program` varchar(255) NOT NULL,
  `status` enum('0','1','2') DEFAULT '0',
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Department`
--

CREATE TABLE `Ms_Department` (
  `id` int(11) NOT NULL,
  `unit_id` smallint(6) NOT NULL,
  `department_name` mediumtext NOT NULL,
  `academician_id` mediumint(9) DEFAULT NULL,
  `language` enum('tr','en','tr / en') NOT NULL DEFAULT 'tr'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Logs`
--

CREATE TABLE `Ms_Logs` (
  `id` int(11) NOT NULL,
  `log_level` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `details` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Pages`
--

CREATE TABLE `Ms_Pages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_file` varchar(255) CHARACTER SET ucs2 COLLATE ucs2_general_ci DEFAULT NULL,
  `page_type` enum('default','horizontal') DEFAULT 'default',
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` enum('tr','en') DEFAULT 'tr',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Payments`
--

CREATE TABLE `Ms_Payments` (
  `id` int(11) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `conversion_id` mediumtext NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `return_message` text DEFAULT NULL,
  `amount` double NOT NULL,
  `paid_amount` double DEFAULT NULL,
  `currency` varchar(24) DEFAULT NULL,
  `platform` varchar(24) NOT NULL,
  `payment_url` mediumtext DEFAULT NULL,
  `payment_type` varchar(64) NOT NULL,
  `card_details` text DEFAULT NULL,
  `status` enum('0','1','2') NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Posts`
--

CREATE TABLE `Ms_Posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `post_title` text NOT NULL,
  `post_text` longtext DEFAULT NULL,
  `post_file` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '{"picture": [], "file": []}',
  `post_privacy` enum('0','1') DEFAULT '1',
  `post_type` enum('news','events','announcements','research','achievements','custom','aday') NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `language` enum('tr','en') NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Unit`
--

CREATE TABLE `Ms_Unit` (
  `id` int(11) NOT NULL,
  `unit_name` mediumtext NOT NULL,
  `year` smallint(6) DEFAULT NULL,
  `is_department` tinyint(4) NOT NULL DEFAULT 0,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Users`
--

CREATE TABLE `Ms_Users` (
  `id` int(11) NOT NULL,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `first_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `birthday` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `gender` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `address` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `phone_number` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'assets/static/img/avatar.svg',
  `academic_info` varchar(200) NOT NULL DEFAULT '{"unit":null, "department":null, "tag":null}',
  `twofa_authentication` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '{"secret_key": null, "status": 0}',
  `ip_address` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `registered` int(10) UNSIGNED NOT NULL,
  `verification` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{"idenity":0,"info":0,"privacy":0,"contracts":0}',
  `roles_mask` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resettable` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `last_login` int(10) UNSIGNED DEFAULT NULL,
  `force_logout` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `idenity_number` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Users_Confirmations`
--

CREATE TABLE `Ms_Users_Confirmations` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(249) NOT NULL,
  `selector` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Users_Remembered`
--

CREATE TABLE `Ms_Users_Remembered` (
  `id` int(11) NOT NULL,
  `user` int(10) UNSIGNED NOT NULL,
  `selector` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Users_Resets`
--

CREATE TABLE `Ms_Users_Resets` (
  `id` int(11) NOT NULL,
  `user` int(10) UNSIGNED NOT NULL,
  `selector` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Ms_Users_Throttling`
--

CREATE TABLE `Ms_Users_Throttling` (
  `id` int(11) NOT NULL,
  `bucket` varchar(44) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `tokens` float UNSIGNED NOT NULL,
  `replenished_at` int(10) UNSIGNED NOT NULL,
  `expires_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Ms_Application`
--
ALTER TABLE `Ms_Application`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Department`
--
ALTER TABLE `Ms_Department`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Logs`
--
ALTER TABLE `Ms_Logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Ms_Pages`
--
ALTER TABLE `Ms_Pages`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Payments`
--
ALTER TABLE `Ms_Payments`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Posts`
--
ALTER TABLE `Ms_Posts`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Unit`
--
ALTER TABLE `Ms_Unit`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Users`
--
ALTER TABLE `Ms_Users`
  ADD PRIMARY KEY (`id`,`username`,`email`,`idenity_number`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`,`idenity_number`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`) USING BTREE;

--
-- Indexes for table `Ms_Users_Confirmations`
--
ALTER TABLE `Ms_Users_Confirmations`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Users_Remembered`
--
ALTER TABLE `Ms_Users_Remembered`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Users_Resets`
--
ALTER TABLE `Ms_Users_Resets`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `Ms_Users_Throttling`
--
ALTER TABLE `Ms_Users_Throttling`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Ms_Application`
--
ALTER TABLE `Ms_Application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Department`
--
ALTER TABLE `Ms_Department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Logs`
--
ALTER TABLE `Ms_Logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Pages`
--
ALTER TABLE `Ms_Pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Payments`
--
ALTER TABLE `Ms_Payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Posts`
--
ALTER TABLE `Ms_Posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Unit`
--
ALTER TABLE `Ms_Unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Users`
--
ALTER TABLE `Ms_Users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Users_Confirmations`
--
ALTER TABLE `Ms_Users_Confirmations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Users_Remembered`
--
ALTER TABLE `Ms_Users_Remembered`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Users_Resets`
--
ALTER TABLE `Ms_Users_Resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ms_Users_Throttling`
--
ALTER TABLE `Ms_Users_Throttling`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
