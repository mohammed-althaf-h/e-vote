-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2024 at 07:26 AM
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
-- Database: `vote`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `position_id` int(11) NOT NULL,
  `registerno` varchar(50) DEFAULT NULL,
  `manifesto` varchar(255) DEFAULT NULL,
  `campaign_details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `name`, `image_url`, `created_at`, `position_id`, `registerno`, `manifesto`, `campaign_details`) VALUES
(4, 'Misbah Ayub', '', '2024-05-17 13:57:18', 1, NULL, NULL, NULL),
(6, 'Mohammed Althaf H', '', '2024-05-20 16:18:50', 1, NULL, NULL, NULL),
(15, 'Test', './uploads/profile_photos/default.png', '2024-06-06 10:39:29', 1, 'C00000015', '../uploads/manifestos/IMG_3779.pdf', 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `name`) VALUES
(1, 'President'),
(2, 'Vice President'),
(3, 'Secretary');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `voting_enabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `voting_enabled`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `registerno` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `isadmin` int(11) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(100) DEFAULT NULL,
  `eligible` tinyint(1) DEFAULT 0,
  `iscand` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `registerno`, `password`, `email`, `created_at`, `isadmin`, `profile_photo`, `verified`, `verification_code`, `eligible`, `iscand`) VALUES
(3, 'Mohammed Althaf H', 'U03MS21S0141', '$2y$10$tlR7/m/URQmOAD5t0opTZOzajsWaX8xOTwhHXuEU9Mahqk03hAOUa', 'althaf78602@gmail.com', '2024-05-06 16:56:59', 1, 'uploads/profile_photos/default.png', 1, NULL, 1, 0),
(5, 'MAH', 'U03MS21S0142', '$2y$10$qNSpj6OSfldUrpby602e9.8pgnFDrDa8mzEpjf/GiP2/oEx9WB.9q', 'mah78602@gmail.com', '2024-05-07 16:47:18', 0, NULL, 1, NULL, 1, 0),
(9, 'Althaf', 'U03MS21S0143', '$2y$10$ej.lKLhta5GiGbjBBel6A.zMEHJu1HNWNO68pMvAYmEHYm4Nkr5nK', 'althaf@inilax.com', '2024-05-07 17:09:06', 0, NULL, 1, '70587be7f85b84897d64c969f0522b4d', 0, 0),
(14, 'Test', 'C00000015', '$2y$10$3tfjEQGCkDmEZf/AkOPoQe3L86LPqEdw0z5gNOz4wjuBV9Ij9ZsTW', '', '2024-06-06 10:39:29', 0, NULL, 1, NULL, 1, 1),
(15, 'Testing', 'U03MS21S0144', '$2y$10$leZePRhVxbRI3rLoiopOOumvgqzT7deWKZLnjJ3qMNz8Tb0b5cdGu', 'test@gmail.com', '2024-06-09 09:27:09', 0, '../uploads/profile_photos/default.png', 1, '1faeb3d9a45c4632ce2c509c63624291', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `registerno` varchar(255) NOT NULL,
  `position_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `registerno`, `position_id`, `candidate_id`, `voted_at`) VALUES
(3, 'U03MS21S0141', 1, 3, '2024-05-17 14:28:00'),
(4, 'U03MS21S0141', 2, 4, '2024-05-23 08:01:18'),
(5, 'U03MS21S0142', 1, 6, '2024-05-23 08:03:02'),
(6, 'U03MS21S0141', 3, 10, '2024-05-26 11:01:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_candidates_registerno` (`registerno`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`registerno`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `fk_votes_registerno` (`registerno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`);

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `fk_votes_registerno` FOREIGN KEY (`registerno`) REFERENCES `users` (`registerno`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
