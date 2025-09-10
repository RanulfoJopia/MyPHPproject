-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 09:38 PM
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
-- Database: `smilesync`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'Auto-increment primary key',
  `firstName` varchar(50) NOT NULL COMMENT 'User first name',
  `lastName` varchar(50) NOT NULL COMMENT 'User last name',
  `middleName` varchar(50) DEFAULT NULL COMMENT 'User middle name (optional)',
  `suffix` varchar(10) DEFAULT NULL COMMENT 'Suffix (optional, e.g., Jr, III)',
  `email` varchar(100) NOT NULL COMMENT 'User email',
  `password` varchar(255) NOT NULL COMMENT 'Hashed password',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Account creation time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Users table for SmileSync registration';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `middleName`, `suffix`, `email`, `password`, `created_at`) VALUES
(1, 'cathriz', 'sinay', 'Longakit', '', 'cathriz23@gmail.com', '$2y$10$wPpn8OPW.9bcB0YsuI2JD.gFPEl82W6G2EZscHAlwaRAUFH3CrG2e', '2025-09-10 19:15:30'),
(2, 'ada', 'sinay', 'ambot', '', 'ada@gmail.com', '$2y$10$QfR68CXeeMILknQmYLvr7.m4EajFF5E/rTFAz.mPFfZV8pj.J/Rdm', '2025-09-10 19:22:03'),
(3, 'ada', 'sinay', 'ambot', '', 'ada123@gmail.com', '$2y$10$oVtyo7nzaIuhbM4XIypyJ.pBQh0sTkvtDvgn2U27fiL5LzVTzOZqy', '2025-09-10 19:25:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Auto-increment primary key', AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
