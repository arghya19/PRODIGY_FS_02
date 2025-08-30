-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 27, 2025 at 02:18 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ems`
--

-- --------------------------------------------------------

--
-- Table structure for table `emp`
--

DROP TABLE IF EXISTS `emp`;
CREATE TABLE IF NOT EXISTS `emp` (
  `e_id` varchar(100) NOT NULL,
  `e_name` varchar(100) NOT NULL,
  `e_email` varchar(100) NOT NULL,
  `e_phno` varchar(100) NOT NULL,
  `e_desig` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`e_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `emp`
--

INSERT INTO `emp` (`e_id`, `e_name`, `e_email`, `e_phno`, `e_desig`, `created_at`) VALUES
('E25001', 'Arghya Nath', 'arghyanath2021tmsl@gmail.com', '8240542798', 'Finance', '2025-08-26 21:42:48'),
('E25002', 'Rai Chakraborty', 'pegasis1910@gmail.com', '9873216545', 'Engineering', '2025-08-26 21:43:51'),
('E25003', 'Dipen Nath', 'arghyanath2000@gmail.com', '8564231547', 'Marketing', '2025-08-26 21:49:51'),
('E25004', 'Utsav Dey', 'utsav@gmail.com', '9873216542', 'Engineering', '2025-08-26 21:59:43'),
('E25005', 'Debrup Bagchi', 'debrup@gmail.com', '9871593574', 'Sales', '2025-08-26 22:10:05'),
('E25006', 'JoyDeep', 'joy@gmail.com', '8798456512', 'Sales', '2025-08-26 22:33:00'),
('E25007', 'Aparna Nath', 'anath@gmail.com', '6290564122', 'Marketing', '2025-08-26 22:33:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `u_id` varchar(100) NOT NULL,
  `u_name` varchar(100) NOT NULL,
  `u_pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `u_role` varchar(100) NOT NULL,
  `created_at` varchar(100) NOT NULL,
  PRIMARY KEY (`u_id`),
  UNIQUE KEY `unique_username` (`u_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `u_name`, `u_pass`, `u_role`, `created_at`) VALUES
('E2025004', 'raj', '$2y$10$zU/CD/0ekmGRQ1ddK/p3leLy1RFfvA3gLCKU./xCH75KfFwyZEsLW', 'admin', '2025-08-24 23:53:50'),
('E2025003', 'arghya', '$2y$10$2HPIWQBB4VZVkogTzewbI.AhikGMArtL4TDvO/dCTS.5DP9plNT..', 'admin', '2025-08-24 17:48:13'),
('E2025002', 'ab', '$2y$10$43YEGxkObYKtG2IWGApItuTCKq8evfWQYA/s90I0JesdlWS7zs.om', 'admin', '2025-08-24 17:41:03'),
('E2025001', 'pega', '$2y$10$Fxr2.ZrMK3q/PiGnEs2Q7O2BlE2X0rq2Hai0thns5a22eM/zcGBKy', 'admin', '2025-08-24 05:22:46'),
('E2025005', 'rai', '$2y$10$C3.23fYW1nxJ7rn8TsFU2OlVU1nXNZaeroVTVAIAULhZhRvUy4x6S', 'admin', '2025-08-25 03:49:14');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
