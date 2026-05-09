-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2026 at 01:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mediflow`
--

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `date_commandes` datetime NOT NULL DEFAULT current_timestamp(),
  `date_livraison` date DEFAULT NULL,
  `statut` enum('en attente','valid?e','livr?e','annul?e','retourn?e') NOT NULL DEFAULT 'en attente',
  `pharmacien_matricule` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commandes`
--

INSERT INTO `commandes` (`id`, `date_commandes`, `date_livraison`, `statut`, `pharmacien_matricule`) VALUES
(18, '2026-04-15 20:56:35', NULL, '', NULL),
(19, '2026-04-16 14:50:35', NULL, '', NULL),
(20, '2026-04-20 19:28:49', NULL, '', NULL),
(21, '2026-04-23 15:03:02', NULL, '', NULL),
(22, '2026-04-23 15:16:32', NULL, '', NULL),
(23, '2026-04-29 22:51:28', NULL, 'en attente', 'SM101');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `contenu` text NOT NULL,
  `statut` enum('en_attente','approuve','rejete') NOT NULL DEFAULT 'en_attente',
  `likes_count` int(11) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `id_post`, `id_utilisateur`, `parent_id`, `contenu`, `statut`, `likes_count`, `date_creation`, `date_modification`) VALUES
(1, 1, NULL, NULL, 'I tried the remote monitoring mentioned in this article and saw a 15% increase in my HRV. The future of telemedicine is truly exciting!', 'approuve', 0, '2026-04-02 10:30:00', '2026-05-09 13:55:35'),
(2, 1, NULL, NULL, 'Is there a specific wearable device you would recommend for this type of health tracking?', 'approuve', 0, '2026-04-02 14:15:00', '2026-05-09 13:55:35'),
(3, 1, NULL, NULL, 'Great article! Very informative about the latest developments in remote healthcare.', 'approuve', 0, '2026-04-03 09:00:00', '2026-05-09 13:55:35'),
(4, 2, NULL, NULL, 'I have been incorporating blueberries into my daily diet for 3 months and noticed improved focus. Science backs this up!', 'approuve', 0, '2026-04-04 11:20:00', '2026-05-09 13:55:35'),
(5, 2, NULL, NULL, 'What about dark chocolate? I have read that it also has significant brain health benefits.', 'approuve', 0, '2026-04-04 16:45:00', '2026-05-09 13:55:35'),
(6, 4, NULL, NULL, 'This data seems contradictory to the 2022 study. Can we get clarification on the sample size used?', 'en_attente', 0, '2026-04-11 08:30:00', '2026-05-09 13:55:35'),
(7, 4, NULL, NULL, 'Great article! Very helpful for my thesis on neuro-rehabilitation.', 'approuve', 0, '2026-04-11 12:00:00', '2026-05-09 13:55:35'),
(8, 1, NULL, NULL, 'Check out this link for cheap medical supplies at discount prices!!!', 'en_attente', 0, '2026-04-14 15:00:00', '2026-05-09 13:55:35'),
(9, 3, NULL, NULL, 'The explanation of sleep cycles here is the clearest I have ever read. Thank you!', 'approuve', 0, '2026-04-06 20:00:00', '2026-05-09 13:55:35'),
(10, 5, NULL, NULL, 'Our hospital recently started a green initiative. This article validates our approach perfectly.', 'approuve', 0, '2026-04-13 11:30:00', '2026-05-09 13:55:35'),
(11, 6, NULL, NULL, 'AAAA', 'en_attente', 0, '2026-04-15 18:56:52', '2026-05-09 13:55:35'),
(12, 6, NULL, NULL, 'AAA', 'en_attente', 0, '2026-04-15 18:57:15', '2026-05-09 13:55:35'),
(13, 7, NULL, NULL, 'This is a test comment from the dynamic test', 'en_attente', 0, '2026-04-15 19:05:11', '2026-05-09 13:55:35'),
(14, 5, NULL, NULL, 'hey', 'en_attente', 0, '2026-04-15 19:06:38', '2026-05-09 13:55:35'),
(15, 4, NULL, NULL, 'Alo ya ma', 'en_attente', 0, '2026-04-15 20:03:06', '2026-05-09 13:55:35'),
(16, 4, NULL, NULL, 'Alo ya pa', 'en_attente', 0, '2026-04-15 20:03:19', '2026-05-09 13:55:35'),
(17, 1, NULL, NULL, 'This is a test comment from the diagnostic script.', 'en_attente', 0, '2026-04-15 20:14:51', '2026-05-09 13:55:35'),
(18, 1, NULL, NULL, 'This is a test comment from the diagnostic script.', 'en_attente', 0, '2026-04-15 20:22:45', '2026-05-09 13:55:35'),
(19, 1, NULL, NULL, 'aaa', 'en_attente', 0, '2026-04-15 20:23:34', '2026-05-09 13:55:35'),
(20, 1, NULL, NULL, 'a', 'en_attente', 0, '2026-04-15 20:24:57', '2026-05-09 13:55:35'),
(21, 1, NULL, NULL, 'aaa', 'approuve', 0, '2026-04-15 20:37:45', '2026-05-09 13:55:35'),
(22, 1, NULL, NULL, 'a', 'approuve', 0, '2026-04-15 20:37:55', '2026-05-09 13:55:35'),
(23, 1, NULL, NULL, 'a', 'approuve', 0, '2026-04-15 20:51:21', '2026-05-09 13:55:35'),
(26, 7, NULL, NULL, 'aaaa', 'approuve', 2, '2026-04-16 14:33:31', '2026-05-09 18:25:54'),
(28, 5, 19, NULL, 'aaa', 'approuve', 0, '2026-04-23 15:47:03', '2026-04-23 15:47:03'),
(29, 7, 19, NULL, 'AHLA', 'approuve', 2, '2026-04-24 15:17:41', '2026-05-09 18:25:53'),
(30, 7, 19, NULL, 'ahla', 'approuve', 2, '2026-04-24 15:33:05', '2026-05-09 18:25:51'),
(31, 7, 20, NULL, 'aha', 'approuve', 2, '2026-04-24 15:34:46', '2026-05-09 18:25:46'),
(32, 6, 19, NULL, 'Alo', 'approuve', 1, '2026-04-24 16:00:25', '2026-05-09 14:21:57'),
(33, 11, 19, NULL, 'Abro', 'approuve', 0, '2026-04-24 16:01:32', '2026-04-24 16:01:32'),
(34, 11, 20, NULL, 'aaaAhla, bro', 'approuve', 0, '2026-04-24 16:10:50', '2026-04-27 14:25:10'),
(35, 11, 20, NULL, 'aaa', 'approuve', 0, '2026-04-27 14:25:00', '2026-04-27 14:25:00'),
(36, 6, 25, 32, 'ahla', 'approuve', 0, '2026-05-09 14:21:40', '2026-05-09 14:23:47'),
(37, 6, 25, 36, 'ahla', 'approuve', 1, '2026-05-09 14:21:50', '2026-05-09 14:23:46'),
(38, 6, 25, 32, 'wa', 'approuve', 1, '2026-05-09 14:21:54', '2026-05-09 14:23:45'),
(39, 7, 25, 31, 'a', 'approuve', 1, '2026-05-09 14:22:53', '2026-05-09 18:25:51'),
(40, 7, 25, 30, 'ya bouti', 'approuve', 1, '2026-05-09 17:22:25', '2026-05-09 18:25:52');

-- --------------------------------------------------------

--
-- Table structure for table `comment_likes`
--

CREATE TABLE `comment_likes` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comment_likes`
--

INSERT INTO `comment_likes` (`id`, `comment_id`, `user_id`, `created_at`) VALUES
(2, 32, 25, '2026-05-09 14:21:57'),
(3, 38, 25, '2026-05-09 14:23:45'),
(4, 37, 25, '2026-05-09 14:23:46'),
(6, 30, 25, '2026-05-09 14:28:26'),
(7, 29, 25, '2026-05-09 14:28:27'),
(8, 26, 25, '2026-05-09 14:28:28'),
(15, 31, 25, '2026-05-09 17:22:01'),
(16, 31, 19, '2026-05-09 18:25:46'),
(17, 39, 19, '2026-05-09 18:25:51'),
(18, 30, 19, '2026-05-09 18:25:51'),
(19, 40, 19, '2026-05-09 18:25:52'),
(20, 29, 19, '2026-05-09 18:25:53'),
(21, 26, 19, '2026-05-09 18:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `consultation`
--

CREATE TABLE `consultation` (
  `id_consultation` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `date_consultation` datetime NOT NULL DEFAULT current_timestamp(),
  `type_consultation` varchar(100) DEFAULT NULL,
  `diagnostic` text DEFAULT NULL,
  `compte_rendu` text DEFAULT NULL,
  `tension_arterielle` varchar(20) DEFAULT NULL,
  `rythme_cardiaque` int(11) DEFAULT NULL,
  `poids` decimal(5,2) DEFAULT NULL,
  `saturation_o2` int(11) DEFAULT NULL,
  `antecedents` longtext DEFAULT NULL COMMENT 'JSON array',
  `allergies` longtext DEFAULT NULL COMMENT 'JSON array',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultation`
--

INSERT INTO `consultation` (`id_consultation`, `id_medecin`, `id_patient`, `date_consultation`, `type_consultation`, `diagnostic`, `compte_rendu`, `tension_arterielle`, `rythme_cardiaque`, `poids`, `saturation_o2`, `antecedents`, `allergies`, `created_at`, `updated_at`) VALUES
(1, 20, 17, '2023-06-02 11:00:00', 'Suivi Sp├®cialis├®', 'Suivi cardiologique', 'Examen cardiologique. ECG normal. Repos conseill├®.', '13/9', 80, 79.00, 97, '[{\"annee\":\"2018\",\"titre\":\"Hypertension Art├®rielle\",\"description\":\"Diagnostiqu├®e lors d\'un contr├┤le de routine.\"}]', '[{\"nom\":\"P├®nicilline\",\"niveau\":\"├ëlev├®\"}]', '2026-04-30 11:21:35', '2026-04-30 11:21:35'),
(2, 20, 18, '2023-10-12 10:00:00', 'Contr├┤le Post-Op', 'Rhinite Chronique', 'L├®g├¿re congestion nasale. Traitement antihistaminique renouvel├®.', '11/7', 68, 62.00, 99, '[{\"annee\":\"2020\",\"titre\":\"Rhinite Allergique\",\"description\":\"Traitement Cetirizine 10mg au printemps.\"}]', '[{\"nom\":\"Pollens\",\"niveau\":\"├ëlev├®\"},{\"nom\":\"Acariens\",\"niveau\":\"Mod├®r├®\"}]', '2026-04-30 11:21:35', '2026-04-30 11:21:35'),
(3, 21, 23, '2023-10-03 10:00:00', 'Bilan Annuel', 'Migraines Chroniques', 'Fr├®quence 3 crises/mois. Augmentation Topiramate ├á 75mg.', '11/7', 68, 58.00, 99, '[{\"annee\":\"2015\",\"titre\":\"Migraines sans aura\",\"description\":\"Traitement de fond par b├¬tabloquants.\"}]', '[{\"nom\":\"Cod├®ine\",\"niveau\":\"├ëlev├®\"}]', '2026-04-30 11:21:35', '2026-04-30 11:21:35'),
(4, 24, 25, '2026-04-28 19:45:00', 'Consultation urgente', 'Hypertension', 'Tension ├®lev├®e. Prescription Amlodipine 5mg.', '151/80', 75, 72.00, 97, '[]', '[{\"nom\":\"Aspirine\",\"niveau\":\"Faible\"}]', '2026-04-30 11:21:35', '2026-04-30 11:21:35'),
(5, 21, 16, '2026-05-02 21:25:29', 'Bilan Annuel', 'Diagnostic de routine numéro 0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(6, 21, 25, '2026-04-25 21:25:29', 'Consultation urgente', 'Diagnostic de routine numéro 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(7, 21, 25, '2026-05-03 21:25:29', 'Consultation urgente', 'Diagnostic de routine numéro 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(8, 21, 23, '2026-04-30 21:25:29', 'Consultation urgente', 'Diagnostic de routine numéro 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(9, 24, 18, '2026-04-27 21:25:29', 'Bilan Annuel', 'Diagnostic de routine numéro 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(10, 24, 18, '2026-04-30 21:25:29', 'Suivi Traitement', 'Diagnostic de routine numéro 5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(11, 21, 18, '2026-05-03 21:25:29', 'Bilan Annuel', 'Diagnostic de routine numéro 6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(12, 21, 23, '2026-05-01 21:25:29', 'Contrôle annuel', 'Diagnostic de routine numéro 7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(13, 21, 25, '2026-04-25 21:25:29', 'Consultation urgente', 'Diagnostic de routine numéro 8', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(14, 24, 18, '2026-04-25 21:25:29', 'Bilan Annuel', 'Diagnostic de routine numéro 9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(15, 21, 18, '2026-05-03 21:25:29', 'Suivi Traitement', 'Diagnostic de routine numéro 10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(16, 24, 23, '2026-04-28 21:25:29', 'Renouvellement ordonnance', 'Diagnostic de routine numéro 11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(17, 24, 16, '2026-04-28 21:25:29', 'Suivi Traitement', 'Diagnostic de routine numéro 12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(18, 21, 25, '2026-04-29 21:25:29', 'Suivi Traitement', 'Diagnostic de routine numéro 13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(19, 24, 18, '2026-05-03 21:25:29', 'Bilan Annuel', 'Diagnostic de routine numéro 14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(20, 24, 25, '2026-05-03 21:25:29', 'Bilan Annuel', 'Diagnostic de routine numéro 15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(21, 21, 18, '2026-04-24 21:25:29', 'Contrôle annuel', 'Diagnostic de routine numéro 16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(22, 24, 17, '2026-04-25 21:25:29', 'Bilan Annuel', 'Diagnostic de routine numéro 17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(23, 24, 25, '2026-04-27 21:25:29', 'Renouvellement ordonnance', 'Diagnostic de routine numéro 18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(24, 21, 18, '2026-04-28 21:25:29', 'Renouvellement ordonnance', 'Diagnostic de routine numéro 19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(25, 24, 23, '2026-04-30 21:25:29', 'Consultation urgente', 'Diagnostic de routine numéro 20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(26, 24, 18, '2026-04-25 21:25:29', 'Renouvellement ordonnance', 'Diagnostic de routine numéro 21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(27, 24, 17, '2026-05-04 21:25:29', 'Consultation urgente', 'Diagnostic de routine numéro 22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(28, 24, 16, '2026-04-25 21:25:29', 'Suivi Traitement', 'Diagnostic de routine numéro 23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(29, 24, 17, '2026-05-02 21:25:29', 'Contrôle annuel', 'Diagnostic de routine numéro 24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(30, 24, 18, '2026-05-01 21:25:29', 'Contrôle annuel', 'Diagnostic de routine numéro 25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(31, 21, 16, '2026-04-27 21:25:29', 'Suivi Traitement', 'Diagnostic de routine numéro 26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(32, 21, 16, '2026-04-29 21:25:29', 'Bilan Annuel', 'Diagnostic de routine numéro 27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(33, 21, 23, '2026-05-01 21:25:29', 'Consultation urgente', 'Diagnostic de routine numéro 28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(34, 24, 16, '2026-05-02 21:25:29', 'Renouvellement ordonnance', 'Diagnostic de routine numéro 29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:25:29', '2026-05-04 19:25:29'),
(35, 42, 51, '2026-04-23 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(36, 42, 54, '2026-04-23 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(37, 42, 54, '2026-04-16 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(38, 42, 56, '2026-04-10 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(39, 42, 58, '2026-04-30 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(40, 42, 65, '2026-04-19 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(41, 42, 65, '2026-04-17 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(42, 43, 48, '2026-04-14 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(43, 43, 48, '2026-05-02 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(44, 43, 51, '2026-04-12 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(45, 43, 51, '2026-04-29 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(46, 43, 52, '2026-05-03 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(47, 43, 57, '2026-04-19 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(48, 43, 57, '2026-04-16 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(49, 43, 57, '2026-04-18 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(50, 43, 63, '2026-04-17 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(51, 43, 63, '2026-05-04 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(52, 43, 63, '2026-04-04 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(53, 44, 48, '2026-04-13 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(54, 44, 48, '2026-04-06 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(55, 44, 48, '2026-05-03 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(56, 44, 58, '2026-05-02 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(57, 44, 59, '2026-04-13 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(58, 44, 59, '2026-05-04 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(59, 44, 61, '2026-04-10 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(60, 44, 61, '2026-04-04 21:41:35', 'Contrôle annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(61, 44, 61, '2026-04-25 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(62, 44, 63, '2026-04-23 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(63, 44, 65, '2026-04-07 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(64, 44, 65, '2026-04-06 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(65, 44, 65, '2026-04-27 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(66, 44, 66, '2026-04-17 21:41:35', 'Contrôle annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(67, 44, 66, '2026-04-23 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(68, 44, 66, '2026-04-17 21:41:35', 'Contrôle annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(69, 45, 50, '2026-04-09 21:41:35', 'Contrôle annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(70, 45, 50, '2026-04-24 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(71, 45, 50, '2026-04-05 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(72, 45, 52, '2026-04-16 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(73, 45, 52, '2026-04-08 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(74, 45, 52, '2026-04-20 21:41:35', 'Contrôle annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(75, 45, 62, '2026-04-20 21:41:35', 'Contrôle annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(76, 45, 62, '2026-04-10 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(77, 45, 62, '2026-04-05 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(78, 45, 64, '2026-04-13 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(79, 45, 64, '2026-05-04 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(80, 45, 64, '2026-04-08 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(81, 45, 66, '2026-04-30 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(82, 46, 54, '2026-04-20 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(83, 46, 54, '2026-04-30 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(84, 46, 54, '2026-05-02 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(85, 46, 55, '2026-04-20 21:41:35', 'Contrôle annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(86, 46, 57, '2026-05-02 21:41:35', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(87, 46, 57, '2026-04-07 21:41:35', 'Consultation urgente', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(88, 46, 57, '2026-04-24 21:41:35', 'Contrôle annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(89, 46, 58, '2026-04-11 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(90, 46, 58, '2026-04-23 21:41:35', 'Renouvellement ordonnance', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(91, 46, 65, '2026-04-28 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(92, 46, 65, '2026-05-02 21:41:35', 'Bilan Annuel', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(93, 46, 65, '2026-05-02 21:41:36', 'Suivi Traitement', 'Diagnostic généré automatiquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-04 19:41:36', '2026-05-04 19:41:36');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `sujet` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `date_message` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` enum('unread','read','archived') NOT NULL DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipement`
--

CREATE TABLE `equipement` (
  `id` int(11) NOT NULL,
  `reference` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `categorie` varchar(50) NOT NULL,
  `prix_jour` decimal(8,2) NOT NULL,
  `statut` enum('disponible','loue','maintenance') DEFAULT 'disponible',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipement`
--

INSERT INTO `equipement` (`id`, `reference`, `nom`, `categorie`, `prix_jour`, `statut`, `image`, `created_at`) VALUES
(1, 'EQ-9402', 'Moniteur Patient V60', 'Cardiologie', 9.00, 'disponible', NULL, '2026-04-13 12:15:01'),
(3, 'EQ-7721', 'Fauteuil M?dicalis? X3', 'G?riatrie', 12.00, 'loue', 'EQ-7721.jpg', '2026-04-13 12:15:01'),
(4, 'EQ-2256', '?chographe Portable S9', 'Radiologie', 18.00, 'loue', 'EQ-2256.jpg', '2026-04-13 12:15:01'),
(5, 'EQ-3310', 'Concentrateur Oxyg?ne', 'Respiratoire', 15.00, 'disponible', 'EQ-3310.jpg', '2026-04-13 12:15:01'),
(6, 'EQ-4401', 'D?ambulateur Rollator', 'Mobilit?', 5.00, 'disponible', 'EQ-4401.jpg', '2026-04-13 12:15:01'),
(15, 'EG-2222', 'matelas axtair automorphooo', 'Respiratoire', 11.00, 'disponible', NULL, '2026-04-19 20:48:36');

-- --------------------------------------------------------

--
-- Table structure for table `lignescommandes`
--

CREATE TABLE `lignescommandes` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite_demande` int(11) NOT NULL CHECK (`quantite_demande` > 0),
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lignescommandes`
--

INSERT INTO `lignescommandes` (`id`, `commande_id`, `produit_id`, `quantite_demande`, `prix`) VALUES
(21, 18, 5, 4, 10.00),
(22, 18, 4, 2, 11.00),
(23, 19, 7, 5, 5000.00),
(24, 19, 4, 1, 11.00),
(25, 20, 5, 1, 10.00),
(26, 20, 7, 1, 5000.00),
(27, 20, 4, 1, 11.00),
(28, 21, 5, 1, 10.00),
(30, 22, 5, 1, 10.00),
(31, 23, 5, 1, 10.00),
(32, 23, 7, 1, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` varchar(500) NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'info',
  `color` varchar(30) NOT NULL DEFAULT 'primary',
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `title`, `message`, `icon`, `color`, `user_id`, `is_read`, `created_at`) VALUES
(1, 'post_comment', 'New comment on your article', '\"ahla…\" — on \"Cardiovascular Robotics: A New Frontier\"', 'chat_bubble', 'blue', 3, 0, '2026-05-09 14:21:40'),
(2, 'comment_reply', 'Someone replied to your comment', '\"ahla…\"', 'reply', 'violet', 19, 1, '2026-05-09 14:21:40'),
(3, 'post_comment', 'New comment on your article', '\"ahla…\" — on \"Cardiovascular Robotics: A New Frontier\"', 'chat_bubble', 'blue', 3, 0, '2026-05-09 14:21:50'),
(4, 'post_comment', 'New comment on your article', '\"wa…\" — on \"Cardiovascular Robotics: A New Frontier\"', 'chat_bubble', 'blue', 3, 0, '2026-05-09 14:21:54'),
(5, 'comment_reply', 'Someone replied to your comment', '\"wa…\"', 'reply', 'violet', 19, 1, '2026-05-09 14:21:54'),
(6, 'comment_like', 'Someone liked your comment', '\"Alo…\"', 'thumb_up', 'violet', 19, 1, '2026-05-09 14:21:57'),
(7, 'post_comment', 'New comment on your article', '\"a…\" — on \"Epidemiology Trends: 2026 Seasonal Preview\"', 'chat_bubble', 'blue', 41, 0, '2026-05-09 14:22:53'),
(8, 'comment_reply', 'Someone replied to your comment', '\"a…\"', 'reply', 'violet', 20, 0, '2026-05-09 14:22:53'),
(9, 'comment_like', 'Someone liked your comment', '\"ahla…\"', 'thumb_up', 'violet', 19, 1, '2026-05-09 14:28:26'),
(10, 'comment_like', 'Someone liked your comment', '\"AHLA…\"', 'thumb_up', 'violet', 19, 1, '2026-05-09 14:28:27'),
(11, 'comment_like', 'Someone liked your comment', '\"aaaa…\"', 'thumb_up', 'violet', 0, 0, '2026-05-09 14:28:28'),
(12, 'comment_like', 'Someone liked your comment', '\"aha…\"', 'thumb_up', 'violet', 20, 0, '2026-05-09 14:28:30'),
(13, 'post_like', 'Someone liked your article', 'Your article \"Epidemiology Trends: 2026 Seasonal Preview\" received a new like.', 'favorite', 'rose', 41, 0, '2026-05-09 14:28:35'),
(14, 'comment_like', 'Someone liked your comment', '\"aha…\"', 'thumb_up', 'violet', 20, 0, '2026-05-09 15:06:44'),
(15, 'comment_like', 'Someone liked your comment', '\"aha…\"', 'thumb_up', 'violet', 20, 0, '2026-05-09 17:16:44'),
(16, 'comment_like', 'Someone liked your comment', '\"aha…\"', 'thumb_up', 'violet', 20, 0, '2026-05-09 17:22:00'),
(17, 'comment_like', 'Someone liked your comment', '\"aha…\"', 'thumb_up', 'violet', 20, 0, '2026-05-09 17:22:01'),
(18, 'post_comment', 'New comment on your article', '\"ya bouti…\" — on \"Epidemiology Trends: 2026 Seasonal Preview\"', 'chat_bubble', 'blue', 41, 0, '2026-05-09 17:22:25'),
(19, 'comment_reply', 'Someone replied to your comment', '\"ya bouti…\"', 'reply', 'violet', 19, 1, '2026-05-09 17:22:25'),
(20, 'comment_like', 'Someone liked your comment', '\"aha…\"', 'thumb_up', 'violet', 20, 0, '2026-05-09 18:25:46'),
(21, 'comment_like', 'Someone liked your comment', '\"a…\"', 'thumb_up', 'violet', 25, 1, '2026-05-09 18:25:51'),
(22, 'comment_like', 'Someone liked your comment', '\"ya bouti…\"', 'thumb_up', 'violet', 25, 1, '2026-05-09 18:25:52'),
(23, 'comment_like', 'Someone liked your comment', '\"aaaa…\"', 'thumb_up', 'violet', 0, 0, '2026-05-09 18:25:54'),
(24, 'nouveau_rdv', 'Nouveau rendez-vous', 'Nouveau rendez-vous de John Doe le 09/05/2026 à 22:39.', 'calendar', 'primary', 24, 1, '2026-05-09 18:40:01'),
(25, 'confirme', 'Rendez-vous confirmé', 'Dr. Adam Smith a confirmé votre rendez-vous du 09/05/2026 à 22:39.', 'check-circle', 'success', 25, 1, '2026-05-09 18:41:50'),
(26, 'modifie', 'Rendez-vous modifié', 'Dr. Adam Smith a modifié votre rendez-vous : nouveau créneau le 10/05/2026 à 22:39.', 'edit', 'warning', 25, 1, '2026-05-09 18:44:03'),
(27, 'modifie', 'Rendez-vous modifié', 'Dr. Adam Smith a modifié votre rendez-vous : nouveau créneau le 11/05/2026 à 22:39.', 'edit', 'warning', 25, 1, '2026-05-09 19:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `ordonnance`
--

CREATE TABLE `ordonnance` (
  `id_ordonnance` int(11) NOT NULL,
  `id_consultation` int(11) NOT NULL,
  `numero_ordonnance` varchar(50) DEFAULT NULL,
  `date_emission` date NOT NULL DEFAULT curdate(),
  `medicaments` longtext NOT NULL COMMENT 'JSON array of medication objects',
  `note_pharmacien` text DEFAULT NULL,
  `statut` enum('active','archivee','annulee') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ordonnance`
--

INSERT INTO `ordonnance` (`id_ordonnance`, `id_consultation`, `numero_ordonnance`, `date_emission`, `medicaments`, `note_pharmacien`, `statut`, `created_at`, `updated_at`) VALUES
(1, 2, 'ORD-2023-001', '2023-10-12', '[{\"nom\":\"Cetirizine\",\"dosage\":\"10mg\",\"frequence\":\"1 fois par jour\",\"duree\":\"30 jours\",\"instructions\":\"Le soir au coucher.\",\"categorie\":\"Antihistaminique\"}]', 'G├®n├®rique autoris├®.', 'active', '2026-04-30 11:21:35', '2026-04-30 11:21:35'),
(2, 4, 'ORD-2026-001', '2026-04-28', '[{\"nom\":\"Amlodipine\",\"dosage\":\"5mg\",\"frequence\":\"1 fois par jour\",\"duree\":\"30 jours\",\"instructions\":\"Le matin avec un grand verre d\'eau.\",\"categorie\":\"Antihypertenseur\"}]', 'Pas de substitution.', 'active', '2026-04-30 11:21:35', '2026-04-30 11:21:35'),
(3, 1, 'ORD-2026-X0', '2026-05-01', '[]', NULL, 'archivee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(4, 2, 'ORD-2026-X1', '2026-05-01', '[]', NULL, 'annulee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(5, 3, 'ORD-2026-X2', '2026-05-01', '[]', NULL, 'archivee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(6, 5, 'ORD-2026-X3', '2026-05-01', '[]', NULL, 'active', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(7, 6, 'ORD-2026-X4', '2026-05-01', '[]', NULL, 'archivee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(8, 7, 'ORD-2026-X5', '2026-05-01', '[]', NULL, 'active', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(9, 8, 'ORD-2026-X6', '2026-05-01', '[]', NULL, 'active', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(10, 11, 'ORD-2026-X7', '2026-05-01', '[]', NULL, 'annulee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(11, 12, 'ORD-2026-X8', '2026-05-01', '[]', NULL, 'annulee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(12, 13, 'ORD-2026-X9', '2026-05-01', '[]', NULL, 'annulee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(13, 15, 'ORD-2026-X10', '2026-05-01', '[]', NULL, 'annulee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(14, 18, 'ORD-2026-X11', '2026-05-01', '[]', NULL, 'archivee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(15, 21, 'ORD-2026-X12', '2026-05-01', '[]', NULL, 'annulee', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(16, 24, 'ORD-2026-X13', '2026-05-01', '[]', NULL, 'active', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(17, 31, 'ORD-2026-X14', '2026-05-01', '[]', NULL, 'active', '2026-05-04 19:27:29', '2026-05-04 19:27:29'),
(18, 35, 'ORD-2026-N9285', '2026-04-23', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'active', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(19, 39, 'ORD-2026-N5249', '2026-04-30', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'archivee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(20, 40, 'ORD-2026-N1402', '2026-04-19', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'archivee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(21, 42, 'ORD-2026-N5953', '2026-04-14', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'active', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(22, 44, 'ORD-2026-N2415', '2026-04-12', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'archivee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(23, 45, 'ORD-2026-N2153', '2026-04-29', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'active', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(24, 46, 'ORD-2026-N9720', '2026-05-03', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(25, 47, 'ORD-2026-N1400', '2026-04-19', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'archivee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(26, 48, 'ORD-2026-N5068', '2026-04-16', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'active', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(27, 49, 'ORD-2026-N4016', '2026-04-18', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(28, 50, 'ORD-2026-N7765', '2026-04-17', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(29, 51, 'ORD-2026-N6042', '2026-05-04', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'archivee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(30, 54, 'ORD-2026-N4800', '2026-04-06', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'active', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(31, 56, 'ORD-2026-N1751', '2026-05-02', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'active', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(32, 67, 'ORD-2026-N3173', '2026-04-23', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'archivee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(33, 68, 'ORD-2026-N2542', '2026-04-17', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'active', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(34, 70, 'ORD-2026-N8917', '2026-04-24', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'active', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(35, 73, 'ORD-2026-N3447', '2026-04-08', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(36, 76, 'ORD-2026-N9785', '2026-04-10', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(37, 78, 'ORD-2026-N6621', '2026-04-13', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'archivee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(38, 83, 'ORD-2026-N4858', '2026-04-30', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(39, 85, 'ORD-2026-N7814', '2026-04-20', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(40, 86, 'ORD-2026-N9932', '2026-05-02', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'archivee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(41, 89, 'ORD-2026-N7507', '2026-04-11', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(42, 91, 'ORD-2026-N6828', '2026-04-28', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(43, 92, 'ORD-2026-N2393', '2026-05-02', '[{\"nom\":\"M\\u00e9dicament Test\",\"dosage\":\"1 cp\",\"frequence\":\"Matin et soir\"}]', NULL, 'annulee', '2026-05-04 19:41:36', '2026-05-04 19:41:36');

-- --------------------------------------------------------

--
-- Table structure for table `planning`
--

CREATE TABLE `planning` (
  `id` int(11) NOT NULL,
  `medecin_id` int(11) NOT NULL,
  `rdv_id` int(11) DEFAULT NULL,
  `titre` varchar(150) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `type` enum('chirurgie','reunion','pause','formation','urgence','autre') DEFAULT 'autre',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `planning`
--

INSERT INTO `planning` (`id`, `medecin_id`, `rdv_id`, `titre`, `date_debut`, `date_fin`, `type`, `note`, `created_at`) VALUES
(7, 24, NULL, 'pause', '2026-04-14 12:00:00', '2026-04-16 09:00:00', 'reunion', '', '2026-04-12 22:06:41'),
(8, 24, NULL, 'reunionnnn', '2026-04-15 12:00:00', '2026-04-15 13:00:00', 'reunion', 'a ne pas venir en retard', '2026-04-12 22:08:36'),
(9, 24, NULL, 'staff', '2026-04-13 19:45:00', '2026-04-13 21:45:00', 'reunion', 'ahhhh', '2026-04-13 18:46:13'),
(10, 24, NULL, 'pause dej', '2026-04-16 13:00:00', '2026-04-16 15:00:00', 'pause', '', '2026-04-14 21:30:00'),
(11, 24, NULL, 'pause dej', '2026-04-22 07:54:00', '2026-04-22 10:23:00', 'reunion', 'top1', '2026-04-20 19:20:52'),
(12, 24, NULL, 'staff', '2026-04-21 19:00:00', '2026-04-21 21:00:00', 'formation', '', '2026-04-20 20:01:53'),
(14, 24, NULL, 'formation5', '2026-04-28 09:00:00', '2026-04-28 17:00:00', 'formation', '', '2026-04-26 15:33:02'),
(16, 24, NULL, 'R?union Staff', '2026-04-28 09:00:00', '2026-04-28 10:30:00', 'reunion', 'Important', '2026-04-28 20:17:06'),
(17, 24, NULL, 'Chirurgie', '2026-04-29 14:00:00', '2026-04-29 17:00:00', 'chirurgie', 'Bloc B', '2026-04-28 20:17:06'),
(18, 24, NULL, 'aaaaaadddd', '2026-04-29 19:16:00', '2026-04-29 23:16:00', 'urgence', 'aaaaaaaaaaa', '2026-04-29 18:16:48'),
(19, 19, NULL, 'aloooo', '2026-04-30 03:17:00', '2026-04-30 03:17:00', 'urgence', 'salem', '2026-04-29 23:17:25'),
(20, 19, NULL, 'alooooooo', '2026-04-30 19:18:00', '2026-04-30 12:18:00', 'autre', '', '2026-04-29 23:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `categorie` varchar(100) NOT NULL DEFAULT 'General Health',
  `image_url` varchar(500) DEFAULT NULL,
  `auteur_id` int(11) DEFAULT NULL,
  `statut` enum('brouillon','publie','archive') NOT NULL DEFAULT 'brouillon',
  `likes_count` int(11) NOT NULL DEFAULT 0,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_publication` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `titre`, `contenu`, `categorie`, `image_url`, `auteur_id`, `statut`, `likes_count`, `views_count`, `date_creation`, `date_modification`, `date_publication`) VALUES
(1, 'The Future of Personalized Telemedicine: Beyond Video Calls', 'Exploring how integrated biosensors and real-time data streaming are transforming the remote consultation experience from simple conversations into clinical-grade assessments.\r\n\r\nThe landscape of telemedicine has evolved dramatically over the past few years. What once started as simple video conferencing between doctors and patients has now transformed into a sophisticated ecosystem of interconnected health technologies.\r\n\r\nModern telemedicine platforms are integrating wearable biosensors that can transmit vital signs in real-time during consultations. Heart rate, blood oxygen levels, blood pressure, and even ECG readings can now be captured by consumer-grade devices and streamed directly to the physician\'s dashboard.\r\n\r\nThis shift represents a fundamental change in how we think about remote healthcare. Rather than relying solely on patient-reported symptoms, doctors can now make data-driven decisions during virtual visits, bringing the consultation closer to the accuracy of in-person examinations.\r\n\r\nKey developments include:\r\n- AI-powered symptom analysis that pre-screens patients before consultations\r\n- Integration of home diagnostic kits with telemedicine platforms\r\n- Real-time vital sign monitoring during video consultations\r\n- Automated follow-up scheduling based on consultation outcomes', 'General Health', 'https://ars.els-cdn.com/content/image/X18075932.jpg', 41, 'publie', 1204, 8524, '2026-04-01 09:00:00', '2026-05-09 15:31:12', '2026-04-01 09:00:00'),
(2, '5 Superfoods for Brain Health', 'Research indicates that a diet rich in these specific nutrients can significantly reduce cognitive decline and improve mental clarity.\r\n\r\nThe connection between diet and brain health has been a growing area of scientific research. Studies consistently show that certain foods contain compounds that directly support neural function, protect against oxidative stress, and promote the growth of new brain cells.\r\n\r\n1. **Blueberries** - Rich in anthocyanins, these powerful antioxidants cross the blood-brain barrier and accumulate in areas responsible for learning and memory.\r\n\r\n2. **Fatty Fish** - Salmon, trout, and sardines are excellent sources of omega-3 fatty acids, which are essential building blocks of the brain.\r\n\r\n3. **Turmeric** - Curcumin, the active ingredient in turmeric, has been shown to cross the blood-brain barrier and has anti-inflammatory and antioxidant benefits.\r\n\r\n4. **Broccoli** - High in compounds called glucosinolates, which produce isothiocyanates that may reduce oxidative stress and lower the risk of neurodegenerative diseases.\r\n\r\n5. **Pumpkin Seeds** - Contain zinc, magnesium, copper, and iron ? all crucial for nerve signaling and brain function.', 'Diet & Nutrition', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjJ4UHdtpL6BV1hMMPQ3dqzM-ogrnY6xS_Og&s', 41, 'publie', 850, 4200, '2026-04-03 14:30:00', '2026-05-09 13:55:04', '2026-04-03 14:30:00'),
(3, 'Understanding Sleep Cycles and Recovery', 'How REM and deep sleep stages impact your body\'s daily recovery mechanisms and what you can do to optimize your rest.\r\n\r\nSleep is not a uniform state ? it\'s a complex, dynamic process consisting of multiple stages that cycle throughout the night. Understanding these cycles is key to improving both the quality and effectiveness of your rest.\r\n\r\nThe sleep cycle consists of four stages:\r\n- **Stage 1 (N1)**: Light sleep lasting 1-7 minutes. Your heartbeat, breathing, and eye movements slow down.\r\n- **Stage 2 (N2)**: Deeper sleep where body temperature drops and brain waves show specific patterns called sleep spindles.\r\n- **Stage 3 (N3)**: Deep sleep or slow-wave sleep. This is the most restorative stage where tissue growth and repair occurs.\r\n- **REM Sleep**: Rapid Eye Movement sleep where most dreaming occurs. Critical for memory consolidation and emotional processing.\r\n\r\nEach complete cycle lasts approximately 90 minutes, and a healthy adult goes through 4-6 cycles per night. The proportion of each stage changes throughout the night, with more deep sleep in the first half and more REM sleep in the second half.', 'Research', 'https://www.clmsleep.com/wp-content/uploads/2025/07/stages-of-sleep-2.jpg', 41, 'publie', 433, 3104, '2026-04-05 11:00:00', '2026-05-09 14:33:31', '2026-04-05 11:00:00'),
(4, 'Advancements in Neural Plasticity Research', 'New studies reveal groundbreaking findings about the brain\'s ability to rewire itself, offering hope for stroke recovery and neurodegenerative disease treatments.\r\n\r\nNeuroplasticity ? the brain\'s remarkable ability to reorganize itself by forming new neural connections ? has been one of the most exciting areas of neuroscience research in recent decades.\r\n\r\nRecent clinical trials have demonstrated that targeted rehabilitation programs, combined with non-invasive brain stimulation techniques, can significantly enhance neural plasticity in stroke patients. These findings suggest that the window for recovery may be much wider than previously believed.\r\n\r\nKey findings include:\r\n- Transcranial magnetic stimulation (TMS) combined with physical therapy shows 40% improvement in motor recovery\r\n- Music therapy activates multiple brain regions simultaneously, promoting cross-hemispheric connections\r\n- Virtual reality rehabilitation programs create immersive environments that challenge the brain to adapt\r\n- Pharmacological interventions using BDNF (Brain-Derived Neurotrophic Factor) show promise in animal models', 'Research', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSdq236h91l4XE_Z3tLGLK94uOmlQVbdzn1Q&s', 41, 'publie', 680, 5608, '2026-04-10 08:15:00', '2026-05-09 13:55:04', '2026-04-10 08:15:00'),
(5, 'The Future of Sustainable Healthcare Infrastructure', 'How modern hospitals are embracing green architecture, renewable energy, and sustainable materials to reduce their environmental footprint while improving patient outcomes.\r\n\r\nHealthcare facilities are among the most energy-intensive buildings in any community. They operate 24/7, require sophisticated HVAC systems, and consume vast amounts of water and electricity. But a growing movement in healthcare architecture is proving that sustainability and excellent patient care can go hand in hand.\r\n\r\nLeading medical centers around the world are now:\r\n- Installing solar panels and wind turbines to offset energy consumption\r\n- Using biophilic design principles that incorporate natural elements into healing environments\r\n- Implementing smart building management systems that optimize energy usage in real-time\r\n- Adopting circular economy principles for medical waste management\r\n- Creating green spaces and therapeutic gardens that improve patient recovery times\r\n\r\nStudies show that patients in sustainably designed hospitals recover up to 15% faster and report higher satisfaction scores. The natural lighting, improved air quality, and connection to nature all contribute to better health outcomes.', 'General Health', 'https://www.creativehatti.com/wp-content/uploads/edd/2023/07/Health-medical-clinic-poster-banner-template-78-large.jpg', 41, 'publie', 546, 3809, '2026-04-12 16:45:00', '2026-05-09 15:41:58', '2026-04-12 16:45:00'),
(6, 'Cardiovascular Robotics: A New Frontier', 'Robotic-assisted cardiovascular procedures are revolutionizing how surgeons approach complex heart operations, offering unprecedented precision and faster patient recovery.\r\n\r\nThe integration of robotics into cardiovascular surgery represents one of the most significant technological advances in modern medicine. These sophisticated systems allow surgeons to perform intricate procedures through small incisions with enhanced visualization and greater dexterity than the human hand alone can provide.\r\n\r\nCurrent applications include:\r\n- Robotic-assisted coronary artery bypass grafting (CABG)\r\n- Minimally invasive mitral valve repair\r\n- Catheter-based interventions with robotic navigation\r\n- Hybrid procedures combining traditional and robotic techniques\r\n\r\nThe da Vinci surgical system and newer platforms offer 3D high-definition visualization, motion scaling (converting large hand movements into precise micro-movements), and tremor filtration. These capabilities are particularly valuable in cardiac surgery where precision is literally a matter of life and death.', 'Journals', 'https://www.acc.org//-/media/Non-Clinical/Images/2024/01/CARDIOLOGY/02/Robotics-3-1200x800.jpg', 3, 'publie', 326, 2912, '2026-04-13 10:00:00', '2026-05-09 14:22:57', '2026-04-13 10:00:00'),
(7, 'Epidemiology Trends: 2026 Seasonal Preview', 'A data-driven look at respiratory health and preventive measures for the coming months based on global surveillance data.\r\n\r\nAs we enter the second quarter of 2026, epidemiological data from global health surveillance networks provides crucial insights into what we can expect in terms of seasonal health challenges.\r\n\r\nThis preview is based on data collected from the WHO Global Influenza Surveillance and Response System, CDC FluView, and European Centre for Disease Prevention and Control.\r\n\r\nKey trends for the upcoming season include shifts in influenza strain dominance, emerging respiratory syncytial virus (RSV) patterns, and the continued evolution of COVID-19 variants.\r\n\r\nPreventive recommendations:\r\n- Updated vaccination schedules for high-risk populations\r\n- Enhanced indoor air quality measures in clinical settings\r\n- Community-based health literacy programs\r\n- Early warning systems integration with primary care networks', 'General Health', 'https://img.freepik.com/premium-psd/medical-clinic-poster-design_452208-1049.jpg', 41, 'publie', 294, 2162, '2026-04-14 13:20:00', '2026-05-09 19:05:58', '2026-04-14 13:20:00'),
(8, 'Mental Health in the Digital Age: Navigating Screen Time', 'Understanding the complex relationship between technology use and psychological well-being, with evidence-based strategies for healthier digital habits.\r\n\r\nThe ubiquity of smartphones, social media, and digital entertainment has created an unprecedented challenge for mental health. While technology offers incredible benefits for connectivity and information access, excessive or mindless use can contribute to anxiety, depression, and sleep disorders.\r\n\r\nThis article is currently under review by our editorial board and will be published upon completion of peer review.', 'Mental Wellness', 'https://static.vecteezy.com/system/resources/thumbnails/004/341/503/small/prenatal-clinic-social-media-post-mockup-childbirth-at-hospital-advertising-web-banner-design-template-social-media-booster-content-layout-promotion-poster-print-ads-with-flat-illustrations-vector.jpg', 3, 'brouillon', 0, 0, '2026-04-15 09:00:00', '2026-04-16 13:26:39', NULL),
(11, 'Test22', 'aaaaaaavbbbaaaaaaaaaaaaaaaaaaaa', 'General Health', '/integration/assets/uploads/img_69ff506faefa72.91032537.jpg', 19, 'publie', 3, 17, '2026-04-24 15:36:32', '2026-05-09 16:19:11', '2026-04-24 17:00:50'),
(12, 'I WANT YOU FOR IDIOTS INCORPORATED', 'Original Text:\r\nUse a lightweight Olma model for images on your local machine to avoid bottlenecks during inference.\r\n\r\nPronounced “Im-mple-ment,” this process involves implementing a custom vision model on your local machine using ONNX Runtime or TensorFlow Lite for fast inference speeds. \r\n\r\nThis is an advanced topic, but it provides significant benefits by freeing up resources and improving inference speed. It\'s the perfect way to optimize your machine learning models and achieve better results in less time. By implementing custom vision models using ONNX Runtime or TensorFlow Lite for faster inference, you can focus on building more advanced systems while ensuring optimal performance.\r\n\r\nAs a professional, we\'re committed to providing you with the best possible experience. We take our work seriously but also understand that it takes careful planning and execution to create something truly great. With ONNX Runtime or TensorFlow Lite, your machine learning models can run faster and be more efficient. This leads to increased accuracy, lower costs, and higher success rates for your projects.\r\n\r\nSo if you\'re interested in optimizing your machine learning models and achieving better results in less time, give this process a try! With ONNX Runtime or TensorFlow Lite, you can focus on building more advanced systems while improving inference speeds. We\'re confident that you\'ll enjoy the benefits of custom vision models using these techniques – we\'ve seen firsthand how they can improve your projects in exciting new ways!', 'General Health', '/integration/assets/uploads/img_69ff7c046e75e4.72067954.png', 19, 'publie', 0, 1, '2026-05-09 19:24:41', '2026-05-09 20:39:09', '2026-05-09 20:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `post_bookmarks`
--

CREATE TABLE `post_bookmarks` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_bookmarks`
--

INSERT INTO `post_bookmarks` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 6, 25, '2026-05-09 14:22:26'),
(8, 5, 25, '2026-05-09 15:29:40'),
(11, 7, 19, '2026-05-09 16:25:25');

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(5, 11, 20, '2026-04-27 14:24:54'),
(6, 6, 20, '2026-04-27 14:31:04'),
(7, 5, 20, '2026-04-28 19:15:45'),
(8, 7, 19, '2026-04-30 13:52:46'),
(9, 11, 25, '2026-04-30 14:08:52'),
(11, 4, 19, '2026-05-09 12:55:40'),
(12, 11, 19, '2026-05-09 12:55:56'),
(13, 3, 19, '2026-05-09 14:06:08'),
(14, 7, 25, '2026-05-09 14:28:35');

-- --------------------------------------------------------

--
-- Table structure for table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `categorie` enum('comprim?s','sirops','injectables') NOT NULL,
  `quantite_disponible` int(11) NOT NULL DEFAULT 0,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `seuil_alerte` int(11) NOT NULL DEFAULT 5,
  `prix_achat` decimal(10,2) NOT NULL,
  `fournisseur_matricule` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `image`, `categorie`, `quantite_disponible`, `prix_unitaire`, `seuil_alerte`, `prix_achat`, `fournisseur_matricule`) VALUES
(4, 'mehdi', '/Mediflow1/assets/images/produit/produit_1776022575_69dbf42f00497.png', 'comprim?s', 6, 11.00, 5, 6.00, NULL),
(5, 'bibo', 'assets/images/produit/produit_1777503177_69f28bc982e57.png', 'injectables', 22, 10.00, 5, 5.00, 'AD103'),
(6, 'tito', 'assets/images/produit/produit_1776286564_69dffb6404d5b.jpg', 'comprim?s', 10, 25.00, 5, 2.00, NULL),
(7, 'doliprane 100', 'assets/images/produit/produit_1777503368_69f28c8859009.jpg', 'comprim?s', 3, 500.00, 5, 3000.00, 'AD103'),
(9, 'testtttt', 'assets/images/produit/produit_1777499119_69f27bef55dfb.png', 'sirops', 25, 15.00, 5, 65.00, 'FR101');

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int(11) NOT NULL,
  `medecin_id` int(11) NOT NULL,
  `patient_nom` varchar(100) NOT NULL,
  `patient_prenom` varchar(100) NOT NULL,
  `cin` char(8) NOT NULL,
  `patient_email` varchar(150) DEFAULT NULL,
  `genre` enum('homme','femme') NOT NULL,
  `date_rdv` date NOT NULL,
  `heure_rdv` time NOT NULL,
  `motif` varchar(100) DEFAULT 'Consultation g?n?rale',
  `statut` enum('en_attente','confirme','annule') DEFAULT 'en_attente',
  `rappel_envoye` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 si le mail de rappel 24h a été envoyé',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id`, `medecin_id`, `patient_nom`, `patient_prenom`, `cin`, `patient_email`, `genre`, `date_rdv`, `heure_rdv`, `motif`, `statut`, `rappel_envoye`, `created_at`) VALUES
(17, 24, 'Doe', 'John', '12345678', NULL, 'homme', '2026-04-30', '10:00:00', 'Suivi post-op?ratoire', 'confirme', 0, '2026-04-28 20:17:06'),
(18, 24, 'Doe', 'John', '12345678', NULL, 'homme', '2026-05-01', '11:30:00', 'Vaccination', 'en_attente', 0, '2026-04-28 20:17:06'),
(19, 24, 'Doe', 'John', '12345678', 'john.doe@gmail.com', 'homme', '2026-05-11', '22:39:00', 'Consultation g?n?rale', 'en_attente', 0, '2026-05-09 17:40:01');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `equipement_id` int(11) NOT NULL,
  `locataire_nom` varchar(100) NOT NULL,
  `matricule` varchar(50) DEFAULT NULL,
  `locataire_ville` varchar(100) DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('en_cours','termine','en_retard') DEFAULT 'en_cours',
  `telephone` varchar(20) DEFAULT NULL,
  `payment_method` varchar(20) NOT NULL DEFAULT 'espece',
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `stripe_payment_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`id`, `equipement_id`, `locataire_nom`, `matricule`, `locataire_ville`, `date_debut`, `date_fin`, `statut`, `telephone`, `payment_method`, `payment_status`, `stripe_payment_id`, `created_at`) VALUES
(30, 6, 'Fathi bin AlKhalifa', NULL, '', '2026-07-22', '2026-07-30', 'en_cours', '', 'espece', 'pending', NULL, '2026-04-21 01:12:37'),
(31, 15, 'abdo samad', 'PT102', '', '2027-07-22', '2027-07-30', 'en_cours', '+216 92 518 333', 'espece', 'pending', NULL, '2026-04-21 01:15:46'),
(34, 15, 'cherif khalil', 'PT103', '', '2026-07-07', '2026-07-08', 'en_cours', '', 'espece', 'pending', NULL, '2026-04-23 00:20:22'),
(35, 1, 'fathi khelifi', 'PT102', '', '2026-07-20', '2026-07-26', 'en_cours', '+216 92 518 333', 'espece', 'pending', NULL, '2026-04-23 01:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `permission` text DEFAULT NULL,
  `date_creation` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `libelle`, `permission`, `date_creation`) VALUES
(1, 'Admin', 'all', '2026-04-11'),
(2, 'Medecin', 'medical_records,consultations', '2026-04-11'),
(3, 'receptionist', 'appointments,billing', '2026-04-11'),
(4, 'pharmacien', 'medication_stock', '2026-04-11'),
(5, 'Technicien', 'equipment_management', '2026-04-11'),
(6, 'redacteur', NULL, '2026-04-11'),
(9, 'Patient', NULL, '2026-04-13'),
(10, 'Fournisseur', 'gestion_stock', '2026-04-29');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_PK` int(11) NOT NULL,
  `matricule` varchar(50) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `motdp` varchar(255) NOT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `cin` char(8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_PK`, `matricule`, `nom`, `prenom`, `mail`, `motdp`, `tel`, `adresse`, `id_role`, `status`, `cin`, `created_at`, `updated_at`) VALUES
(3, NULL, 'yasss', 'ss', 'medecin2@mediflow.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+212612345679', 'Tunis, Tunisia', 5, 'active', NULL, '2026-04-11 15:39:37', '2026-04-16 12:48:48'),
(11, 'AD101', 'aa', 'aaaaa', 'aaaaa@mediflow.com', '$2y$10$mLnxXqMhZF09jNgd.dtOWOyyWZ2cfV.rPjsMG0q6n3O7soGg8OyNq', NULL, 'dddd', 1, 'suspended', NULL, '2026-04-13 22:36:04', '2026-04-29 23:50:01'),
(15, 'AD100', 'Admin', 'MediFlow', 'admin@mediflow.com', '$2y$10$dAUyViEr3jFurQWGeKDhKO9MMPJR8z0SiAN.nrDMGzq/xel2d.PNe', '+216 00 000 000', NULL, 1, 'active', NULL, '2026-04-13 22:44:16', '2026-04-13 22:46:34'),
(16, 'AD102', 'fathi', 'khelifi', 'fathikhelifi@mediflow.com', '$2y$10$kBpNqqIQYw3XftXxsdGL7u7D2GZRGMzqCAgj05rZ9kGVOkBGW8gMC', NULL, 'ghazel', 9, 'active', NULL, '2026-04-13 22:47:29', '2026-04-29 23:20:04'),
(17, 'PT100', 'fathi', 'fathitest', 'fathikk@gmail.com', '$2y$10$UufGs0R1Sp6XozR7fbMoWu2oeArzEksu.r0UZ.P97X4HH5imXjIby', '99999999999', NULL, 9, 'active', '12345678', '2026-04-13 23:18:06', '2026-04-29 18:33:43'),
(18, 'PT101', 'abdo', 'samad', 'abdo@mediflow.com', '$2y$10$8wybCzdyPYdGXlrB5OaLauGYqBpYAzIziw5Gx10a8/Uvna02mur26', '444444444444', 'Ariena,Ghazela', 9, 'active', '12345678', '2026-04-15 18:07:26', '2026-04-29 18:33:43'),
(19, 'AD103', 'fathi', 'khelifi', 'admin11@mediflow.com', '$2y$10$9TUE.90W5k6IkVyMWzHND.WB4b4brVFoagkE0H8p11z80ObcPju0G', '+216 92 518 333', 'Ariena,Ghazela', 1, 'active', NULL, '2026-04-16 12:40:30', '2026-04-16 12:40:30'),
(20, 'PT102', 'khelifi', 'fathi', 'fathikhelifi10@gmail.com', '$2y$10$3NgQjaBa4nrg2ubQadmZ3.FhPq8bQphdB.t6ELvhtnPJsDaO15I7q', '+216 92 518 333', NULL, 1, 'active', NULL, '2026-04-16 12:59:14', '2026-04-30 09:28:41'),
(21, 'MD100', 'fathi', 'khelifi', 'MED1@mediflow.com', '$2y$10$o8Yi2QkYNkAMYHISYJnzre6qZvZunda2yeObGuD3YCPGgaCpMqaeu', NULL, 'Ariena,Ghazela', 2, 'active', NULL, '2026-04-16 13:12:06', '2026-04-16 13:12:06'),
(22, 'EQ101', 'nada', 'karoui', 'nada@mediflow.com', '$2y$10$jji1Az.JjOARrkRAsDcysOEZDHueHhxGv7Zy0UE1ljso0F2z4hZr2', NULL, NULL, 5, 'active', NULL, '2026-04-21 00:06:42', '2026-04-21 00:06:42'),
(23, 'PT103', 'khalil', 'cherif', 'khalil@mediflow.com', '$2y$10$c7J0m95iBePywJdx36ETR.pSosQp8ebTiwfHOPVB/zkGt5eMnAG6m', NULL, NULL, 9, 'active', '12345678', '2026-04-21 01:04:22', '2026-04-29 18:33:43'),
(24, 'MD200', 'Smith', 'Adam', 'dr.smith@mediflow.com', '$2y$10$uWu93squAEw/91qe7kX0tuoY1xqsHUbkMbgAYTBRw1IDdUBmAeXpa', NULL, NULL, 2, 'active', NULL, '2026-04-28 20:17:06', '2026-04-28 20:17:06'),
(25, 'PT200', 'Doe', 'John', 'john.doe@gmail.com', '$2y$10$5aYyIrc.ZyMUxEiDV9uVKeuYgz.SDdpR.9oPYHFm6xIk7dL5/T8Ny', '55667788', NULL, 9, 'active', '12345678', '2026-04-28 20:17:06', '2026-04-29 18:33:43'),
(27, 'SM100', 'ahmed12', 'aaa', 'ahmedS@gmail.com', '$2y$10$8PwfR5l.XGjN28HTpfTTpOu/XaRMoIl1il7LdZ.qPP2md9sxavAGG', NULL, NULL, 4, 'active', NULL, '2026-04-29 21:14:53', '2026-04-29 21:14:53'),
(32, 'FR100', 'test', 'test', 'testf2@gmail.com', '$2y$10$Ml.GzXs/.K5d9hgrT3uvAuS4VIFS4aJEwR28SsPcwaKg2uUhey1oO', NULL, NULL, 10, 'active', NULL, '2026-04-29 21:40:50', '2026-04-29 21:40:50'),
(34, 'FR101', 'fathiF', 'ffff', 'fathiF1@gmail.com', '$2y$10$Tafyk01ZP0TjJd9MNCVfD.X2zG7arsTslXl7ehV1yBZHr4nEYFWt2', NULL, NULL, 10, 'active', NULL, '2026-04-29 21:44:31', '2026-04-29 21:44:31'),
(35, 'SM101', 'abdoo', 'aaa', 'abdoP@gmail.com', '$2y$10$L6r7yjPeOjNCXGHxZOoE2.RQgKSOVbdngsWG0IkFUo6ZxA4akUN.K', NULL, NULL, 4, 'active', NULL, '2026-04-29 21:46:26', '2026-04-29 21:46:26'),
(36, 'PT201', 'alloooo', 'laaaa', 'aloP@gmail.com', '$2y$10$W75DLeKAEVEv/Pk6GmEuzeleuqnxsGsvbSxObK9KCFBOVa.EVRTeO', NULL, NULL, 9, 'active', NULL, '2026-04-29 23:24:57', '2026-04-29 23:24:57'),
(37, 'UK100', 'nada11', 'aa', 'nada11@gmail.com', '$2y$10$8bhohDpipvpa52r807J6HOyEetNH76zA3TC6axOSvki7awrSc.cLi', NULL, NULL, 5, 'active', NULL, '2026-04-30 00:27:22', '2026-04-30 00:27:22'),
(38, 'FR102', 'mehdi', 'four', 'mehdiF@gmail.com', '$2y$10$I59OHhZqMPxagd3fS0WhyO3VyYlhQlkmzK54H7X5cFBjyuXoehO0C', NULL, NULL, 10, 'active', NULL, '2026-04-30 00:46:49', '2026-04-30 00:46:49'),
(39, 'PH100', 'mehdi12', 'aloo', 'mehdi12@gmail.com', '$2y$10$.P6pIB5vrc2QzJEUisCC.u0tDFKpcV4txKxQfVLesaN6RErKnFQ8G', NULL, NULL, 4, 'active', NULL, '2026-04-30 00:48:25', '2026-04-30 00:48:25'),
(41, 'RD100', 'khalil', 'aaa', 'khalil12@gmail.com', '$2y$10$u896uJ99wgxIWODl9FerLem56Kz2g1LpmgN.xMAncmgf2NGFTR33y', NULL, NULL, 6, 'active', NULL, '2026-04-30 00:52:58', '2026-04-30 00:52:58'),
(42, 'MD300', 'Dupont', 'Dr. Alice', 'alice.dupont@mediflow.com', '$2y$10$GEfC3fUpaw71d29IQSaSNe7ElWO3VN/OfacfSP.Ua7HJOif/CW7aS', '0102030405', NULL, 2, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(43, 'MD301', 'Martin', 'Dr. Bernard', 'bernard.martin@mediflow.com', '$2y$10$HOfJmArDWVqtFmb/Amqbke9sXHSKeKbuH.peOxptdZrerONPX9l2K', '0102030405', NULL, 2, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(44, 'MD302', 'Lefebvre', 'Dr. Clara', 'clara.lefebvre@mediflow.com', '$2y$10$WoVyIrFgOiyzVQe9rqd12utc5f8z/3mMftll/4IKyspOtQkIxzJCe', '0102030405', NULL, 2, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(45, 'MD303', 'Rousseau', 'Dr. David', 'david.rousseau@mediflow.com', '$2y$10$3wdB/njR.AubnSoIVOr0y.hxaRquU/7c/4vBIqfP3tI3jXjyLtQdW', '0102030405', NULL, 2, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(46, 'MD304', 'Laurent', 'Dr. Emma', 'emma.laurent@mediflow.com', '$2y$10$jmXKp0B5B9t/7jrZx.pbousWrN4ors4TTrkOKSPagpi50E9JSc9zi', '0102030405', NULL, 2, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(47, 'PT301', 'Test1', 'Patient1', 'patient1@test.com', '$2y$10$EofkrYeoQkBPBPy/LnuL2O9IXJ79ft0UQmJ1OJZhmUSQJBnV5hrn2', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(48, 'PT302', 'Test2', 'Patient2', 'patient2@test.com', '$2y$10$PrqmTu9Me6V17amq3WQTt.XiDs.N/ZD5GEVkkVivmDr0Ov5dEzcza', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(49, 'PT303', 'Test3', 'Patient3', 'patient3@test.com', '$2y$10$3VZ4b0pGHLInaibiWkteLOjeEWYHOYJyFZp4ubs0WSVlTgZzkfaBW', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(50, 'PT304', 'Test4', 'Patient4', 'patient4@test.com', '$2y$10$yEIjUkhhRzh4xRrdgTEN.uvPjKHUGnpk6D5UsDUooaT1tSYrsbAhG', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(51, 'PT305', 'Test5', 'Patient5', 'patient5@test.com', '$2y$10$612qYibHeOU9QASt88VAv.l.XJnhZPFn2U6rw74AndQYn4hTD1fg6', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(52, 'PT306', 'Test6', 'Patient6', 'patient6@test.com', '$2y$10$NjsCfXy3YoepRQCA6CVAJuLjDRE26jJt3abFU48OjPK0RJ1lFia3W', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(53, 'PT307', 'Test7', 'Patient7', 'patient7@test.com', '$2y$10$E1LZ7GvQ9YWkAgnpUD1kkeNqG5WAaPcIdbKA52oQsbkRuQlIC59cS', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:34', '2026-05-04 19:41:34'),
(54, 'PT308', 'Test8', 'Patient8', 'patient8@test.com', '$2y$10$bmvfUFOWOyhOJhQTP51gU.wJ3E6spPZJASS2lq1nu5q3MXeRDhAdm', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(55, 'PT309', 'Test9', 'Patient9', 'patient9@test.com', '$2y$10$3SArRI6lmktg1EFSmYD3leZfTLCYVeqwSPTL1.OKt9d08NQ/GPgDa', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(56, 'PT310', 'Test10', 'Patient10', 'patient10@test.com', '$2y$10$jclXognDDqPueFYxk3o.UuxwG3SR5gVjZ/tMHC6xLoe.7U0T7UzVq', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(57, 'PT311', 'Test11', 'Patient11', 'patient11@test.com', '$2y$10$HrkqIXyszKukWStcMGmdBe0.sOdOLJaw6GqcF3VlyrV1fIcg/VhNi', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(58, 'PT312', 'Test12', 'Patient12', 'patient12@test.com', '$2y$10$8Xdxoo81iddfSQNgZ413x.kreC7EkMPfxStr50ohPLsNd48XBS.Sq', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(59, 'PT313', 'Test13', 'Patient13', 'patient13@test.com', '$2y$10$wC4KcYRAcjMzOlz3KBZ1rOC5qOTj3poMoFEBhg1oJNjohrlBnmz3q', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(60, 'PT314', 'Test14', 'Patient14', 'patient14@test.com', '$2y$10$AHe5FgY/0Ut/LypRGMJY8eXQV67Qg1ClSh2Q/8kn6k4WaJCycPDHu', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(61, 'PT315', 'Test15', 'Patient15', 'patient15@test.com', '$2y$10$9/DsMXKyXFCrEh97l7n9he9K1BFnaUkjYHRgJAnpTB2oyjYOdaZ1S', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(62, 'PT316', 'Test16', 'Patient16', 'patient16@test.com', '$2y$10$ust8pQ88kZm7NIHcb3KRnu3cR6qZT8qlfMqtxFHzn2hmHyxpyBqSW', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(63, 'PT317', 'Test17', 'Patient17', 'patient17@test.com', '$2y$10$e20iUxjJpQeKBDU0gmd7leHj.6sPXWDnnwLNBQBrKQaXCPoPU1o7u', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(64, 'PT318', 'Test18', 'Patient18', 'patient18@test.com', '$2y$10$g7OyOGowLN3niU2EGjE0KObD0SIc1JgWVkBNAubDZLiMYZHlTb4vK', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(65, 'PT319', 'Test19', 'Patient19', 'patient19@test.com', '$2y$10$6WCKJ2avsfnbVfSEch5LfuVfvUWOXg8oVbP/VI4zPHKKxuzOFfZgq', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35'),
(66, 'PT320', 'Test20', 'Patient20', 'patient20@test.com', '$2y$10$Z2WNfXMg7rd1NpRk/4EdSeyhDcj7AzUctp15i576sa3uPBdwZD8bS', '0607080910', NULL, 9, 'active', NULL, '2026-05-04 19:41:35', '2026-05-04 19:41:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_post` (`id_post`),
  ADD KEY `fk_comments_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_comments_statut` (`statut`),
  ADD KEY `fk_comments_parent` (`parent_id`);

--
-- Indexes for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_comment_like` (`comment_id`,`user_id`),
  ADD KEY `fk_cl_user` (`user_id`);

--
-- Indexes for table `consultation`
--
ALTER TABLE `consultation`
  ADD PRIMARY KEY (`id_consultation`),
  ADD KEY `idx_medecin` (`id_medecin`),
  ADD KEY `idx_patient` (`id_patient`),
  ADD KEY `idx_date` (`date_consultation`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contact_patient` (`id_patient`);

--
-- Indexes for table `equipement`
--
ALTER TABLE `equipement`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`);

--
-- Indexes for table `lignescommandes`
--
ALTER TABLE `lignescommandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commande_id` (`commande_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user` (`user_id`);

--
-- Indexes for table `ordonnance`
--
ALTER TABLE `ordonnance`
  ADD PRIMARY KEY (`id_ordonnance`),
  ADD UNIQUE KEY `uk_numero` (`numero_ordonnance`),
  ADD KEY `idx_consultation` (`id_consultation`);

--
-- Indexes for table `planning`
--
ALTER TABLE `planning`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_planning_medecin` (`medecin_id`),
  ADD KEY `fk_planning_rdv` (`rdv_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_posts_auteur` (`auteur_id`),
  ADD KEY `idx_posts_statut` (`statut`),
  ADD KEY `idx_posts_categorie` (`categorie`),
  ADD KEY `idx_posts_date_publication` (`date_publication`);

--
-- Indexes for table `post_bookmarks`
--
ALTER TABLE `post_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bookmark` (`post_id`,`user_id`),
  ADD KEY `fk_bm_user` (`user_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rdv_medecin` (`medecin_id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipement_id` (`equipement_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_PK`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `consultation`
--
ALTER TABLE `consultation`
  MODIFY `id_consultation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipement`
--
ALTER TABLE `equipement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `lignescommandes`
--
ALTER TABLE `lignescommandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `ordonnance`
--
ALTER TABLE `ordonnance`
  MODIFY `id_ordonnance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `planning`
--
ALTER TABLE `planning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `post_bookmarks`
--
ALTER TABLE `post_bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_PK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD CONSTRAINT `fk_cl_comment` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cl_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE;

--
-- Constraints for table `consultation`
--
ALTER TABLE `consultation`
  ADD CONSTRAINT `fk_consult_medecin` FOREIGN KEY (`id_medecin`) REFERENCES `utilisateurs` (`id_PK`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consult_patient` FOREIGN KEY (`id_patient`) REFERENCES `utilisateurs` (`id_PK`) ON UPDATE CASCADE;

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `fk_contact_patient` FOREIGN KEY (`id_patient`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lignescommandes`
--
ALTER TABLE `lignescommandes`
  ADD CONSTRAINT `lignescommandes_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lignescommandes_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Constraints for table `ordonnance`
--
ALTER TABLE `ordonnance`
  ADD CONSTRAINT `fk_ordo_consultation` FOREIGN KEY (`id_consultation`) REFERENCES `consultation` (`id_consultation`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `planning`
--
ALTER TABLE `planning`
  ADD CONSTRAINT `fk_planning_medecin` FOREIGN KEY (`medecin_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_planning_rdv` FOREIGN KEY (`rdv_id`) REFERENCES `rendez_vous` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_auteur` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `post_bookmarks`
--
ALTER TABLE `post_bookmarks`
  ADD CONSTRAINT `fk_bm_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bm_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE;

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE;

--
-- Constraints for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `fk_rdv_medecin` FOREIGN KEY (`medecin_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`equipement_id`) REFERENCES `equipement` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
