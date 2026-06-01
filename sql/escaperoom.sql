-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260511.41911fadd5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 27, 2026 at 08:10 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `escaperoom`
--

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int NOT NULL COMMENT 'Primary Key',
  `question` varchar(255) DEFAULT NULL,
  `hint` varchar(255) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `roomId` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question`, `hint`, `answer`, `roomId`) VALUES
(1, 'Er staan 3 stapels dozen en achter welke ligt de hint?', 'De hint ligt achter de stapel met de minste dozen.', 'Achter de stapel met de minste dozen.', 1),
(2, 'Met wat was ik de kleding?', 'Hoe krijgt de wasmachine stroom?', 'De stroom kabel.', 1),
(3, 'Hoeveel trap tredens zijn er en deel het door 2 en dan heb je het laatste cijfer voor de kluis.', 'klik op de trap', '6', 1),
(4, 'Wat is de code van de trapkast?', 'Kijk naar het pinbord?', '1683', 2),
(5, 'Wat is de code van de kluis?', 'Kijk naar de lamp', '⬆️⬆️⬇️⬅️', 2),
(6, 'Welke sleutel hoort bij de deur', 'Kijk naar de kleur van de deur.', 'Rood', 2);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `team` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_teams_name` (`team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `username` varchar(255) NOT NULL COMMENT 'Gebruikersnaam',
  `password` varchar(255) NOT NULL COMMENT 'Beveiligd wachtwoord',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `team_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_team_member` (`team_id`, `user_id`),
  KEY `idx_team_members_team` (`team_id`),
  KEY `idx_team_members_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- AUTO_INCREMENT for dumped tables
--

-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `team_members`
--
ALTER TABLE `team_members`
  ADD CONSTRAINT `team_members_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
