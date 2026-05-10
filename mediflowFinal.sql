-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 07 mai 2026 à 01:54
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mediflow`
--

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `date_commandes` datetime NOT NULL DEFAULT current_timestamp(),
  `date_livraison` date DEFAULT NULL,
  `statut` enum('en attente','valid?e','livr?e','annul?e','retourn?e') NOT NULL DEFAULT 'en attente',
  `pharmacien_matricule` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
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
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `statut` enum('en_attente','approuve','rejete') NOT NULL DEFAULT 'en_attente',
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `id_post`, `id_utilisateur`, `contenu`, `statut`, `date_creation`, `date_modification`) VALUES
(1, 1, 4, 'I tried the remote monitoring mentioned in this article and saw a 15% increase in my HRV. The future of telemedicine is truly exciting!', 'approuve', '2026-04-02 10:30:00', '2026-04-15 16:21:57'),
(2, 1, 5, 'Is there a specific wearable device you would recommend for this type of health tracking?', 'approuve', '2026-04-02 14:15:00', '2026-04-15 16:21:57'),
(3, 1, 6, 'Great article! Very informative about the latest developments in remote healthcare.', 'approuve', '2026-04-03 09:00:00', '2026-04-15 16:21:57'),
(4, 2, 4, 'I have been incorporating blueberries into my daily diet for 3 months and noticed improved focus. Science backs this up!', 'approuve', '2026-04-04 11:20:00', '2026-04-15 16:21:57'),
(5, 2, 6, 'What about dark chocolate? I have read that it also has significant brain health benefits.', 'approuve', '2026-04-04 16:45:00', '2026-04-15 16:21:57'),
(6, 4, 5, 'This data seems contradictory to the 2022 study. Can we get clarification on the sample size used?', 'en_attente', '2026-04-11 08:30:00', '2026-04-15 16:21:57'),
(7, 4, 4, 'Great article! Very helpful for my thesis on neuro-rehabilitation.', 'approuve', '2026-04-11 12:00:00', '2026-04-20 20:03:29'),
(8, 1, 6, 'Check out this link for cheap medical supplies at discount prices!!!', 'en_attente', '2026-04-14 15:00:00', '2026-04-15 16:21:57'),
(9, 3, 4, 'The explanation of sleep cycles here is the clearest I have ever read. Thank you!', 'approuve', '2026-04-06 20:00:00', '2026-04-15 16:21:57'),
(10, 5, 5, 'Our hospital recently started a green initiative. This article validates our approach perfectly.', 'approuve', '2026-04-13 11:30:00', '2026-04-15 16:21:57'),
(11, 6, 4, 'AAAA', 'en_attente', '2026-04-15 18:56:52', '2026-04-15 18:56:52'),
(12, 6, 4, 'AAA', 'en_attente', '2026-04-15 18:57:15', '2026-04-15 18:57:15'),
(13, 7, 4, 'This is a test comment from the dynamic test', 'en_attente', '2026-04-15 19:05:11', '2026-04-15 19:05:11'),
(14, 5, 4, 'hey', 'en_attente', '2026-04-15 19:06:38', '2026-04-15 19:06:38'),
(15, 4, 4, 'Alo ya ma', 'en_attente', '2026-04-15 20:03:06', '2026-04-15 20:03:06'),
(16, 4, 4, 'Alo ya pa', 'en_attente', '2026-04-15 20:03:19', '2026-04-15 20:03:19'),
(17, 1, 4, 'This is a test comment from the diagnostic script.', 'en_attente', '2026-04-15 20:14:51', '2026-04-15 20:14:51'),
(18, 1, 4, 'This is a test comment from the diagnostic script.', 'en_attente', '2026-04-15 20:22:45', '2026-04-15 20:22:45'),
(19, 1, 4, 'aaa', 'en_attente', '2026-04-15 20:23:34', '2026-04-15 20:23:34'),
(20, 1, 4, 'a', 'en_attente', '2026-04-15 20:24:57', '2026-04-15 20:24:57'),
(21, 1, 4, 'aaa', 'approuve', '2026-04-15 20:37:45', '2026-04-15 20:37:45'),
(22, 1, 4, 'a', 'approuve', '2026-04-15 20:37:55', '2026-04-15 20:37:55'),
(23, 1, 4, 'a', 'approuve', '2026-04-15 20:51:21', '2026-04-15 20:51:21'),
(24, 9, 4, 'aaaaaaaa', 'approuve', '2026-04-16 13:44:02', '2026-04-16 13:44:02'),
(25, 9, 4, 'aaaa', 'approuve', '2026-04-16 13:44:15', '2026-04-16 13:44:15'),
(26, 7, 4, 'aaaa', 'approuve', '2026-04-16 14:33:31', '2026-04-16 14:33:31'),
(28, 5, 19, 'aaa', 'approuve', '2026-04-23 15:47:03', '2026-04-23 15:47:03'),
(29, 7, 19, 'AHLA', 'approuve', '2026-04-24 15:17:41', '2026-04-24 15:17:41'),
(30, 7, 19, 'ahla', 'approuve', '2026-04-24 15:33:05', '2026-04-24 15:33:05'),
(31, 7, 20, 'aha', 'approuve', '2026-04-24 15:34:46', '2026-04-24 15:34:46'),
(32, 6, 19, 'Alo', 'approuve', '2026-04-24 16:00:25', '2026-04-24 16:00:25'),
(33, 11, 19, 'Abro', 'approuve', '2026-04-24 16:01:32', '2026-04-24 16:01:32'),
(34, 11, 20, 'aaaAhla, bro', 'approuve', '2026-04-24 16:10:50', '2026-04-27 14:25:10'),
(35, 11, 20, 'aaa', 'approuve', '2026-04-27 14:25:00', '2026-04-27 14:25:00');

-- --------------------------------------------------------

--
-- Structure de la table `consultation`
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

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
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
-- Structure de la table `equipement`
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
-- Déchargement des données de la table `equipement`
--

INSERT INTO `equipement` (`id`, `reference`, `nom`, `categorie`, `prix_jour`, `statut`, `image`, `created_at`) VALUES
(1, 'EQ-9402', 'Moniteur Patient V60', 'Cardiologie', 9.00, 'disponible', NULL, '2026-04-13 12:15:01'),
(3, 'EQ-7721', 'Fauteuil M?dicalis? X3', 'G?riatrie', 12.00, 'loue', 'EQ-7721.jpg', '2026-04-13 12:15:01'),
(4, 'EQ-2256', '?chographe Portable S9', 'Radiologie', 18.00, 'loue', 'EQ-2256.jpg', '2026-04-13 12:15:01'),
(5, 'EQ-3310', 'Concentrateur Oxyg?ne', 'Respiratoire', 15.00, 'disponible', 'EQ-3310.jpg', '2026-04-13 12:15:01'),
(6, 'EQ-4401', 'D?ambulateur Rollator', 'Mobilit?', 5.00, 'disponible', 'EQ-4401.jpg', '2026-04-13 12:15:01'),
(15, 'EG-2222', 'matelas axtair automorphooo', 'Respiratoire', 16.00, 'disponible', NULL, '2026-04-19 20:48:36');

-- --------------------------------------------------------

--
-- Structure de la table `lignescommandes`
--

CREATE TABLE `lignescommandes` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite_demande` int(11) NOT NULL CHECK (`quantite_demande` > 0),
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lignescommandes`
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
-- Structure de la table `notifications`
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
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `title`, `message`, `icon`, `color`, `user_id`, `is_read`, `created_at`) VALUES
(1, 'google_signup', 'Inscription via Google', 'Le patient fathi khelifi11 s\'est inscrit avec son compte Google.', 'account_circle', 'primary', 85, 1, '2026-05-07 00:39:51'),
(2, 'new_user', 'Nouvel utilisateur (Admin)', 'L\'administrateur a créé le compte de fathii fathi (admin@gmail.com).', 'person_add', 'primary', 19, 1, '2026-05-07 00:42:38'),
(3, 'user_activated', 'Compte réactivé', 'Le compte de Khelifi Fathi (Matricule: PT100) a été réactivé.', 'check_circle', 'tertiary', 86, 1, '2026-05-07 00:46:26'),
(4, 'user_suspended', 'Compte suspendu', 'Le compte de Khelifi Fathi (Matricule: PT100) a été suspendu.', 'block', 'error', 86, 1, '2026-05-07 00:46:38'),
(5, 'new_user', 'Nouvel utilisateur (Admin)', 'L\'administrateur a créé le compte de tesstt testF (Matricule: PT103).', 'person_add', 'primary', 86, 1, '2026-05-07 00:47:15'),
(6, 'password_changed', 'Mot de passe mis à jour', 'L\'utilisateur tesstt testF (Matricule: PT103) a mis à jour son mot de passe.', 'lock_reset', 'secondary', 87, 1, '2026-05-07 00:48:23');

-- --------------------------------------------------------

--
-- Structure de la table `ordonnance`
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

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `planning`
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
-- Déchargement des données de la table `planning`
--

INSERT INTO `planning` (`id`, `medecin_id`, `rdv_id`, `titre`, `date_debut`, `date_fin`, `type`, `note`, `created_at`) VALUES
(19, 19, NULL, 'aloooo', '2026-04-30 03:17:00', '2026-04-30 03:17:00', 'urgence', 'salem', '2026-04-29 23:17:25'),
(20, 19, NULL, 'alooooooo', '2026-04-30 19:18:00', '2026-04-30 12:18:00', 'autre', '', '2026-04-29 23:18:31');

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `categorie` varchar(100) NOT NULL DEFAULT 'General Health',
  `image_url` varchar(500) DEFAULT NULL,
  `auteur_id` int(11) NOT NULL,
  `statut` enum('brouillon','publie','archive') NOT NULL DEFAULT 'brouillon',
  `likes_count` int(11) NOT NULL DEFAULT 0,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_publication` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `titre`, `contenu`, `categorie`, `image_url`, `auteur_id`, `statut`, `likes_count`, `views_count`, `date_creation`, `date_modification`, `date_publication`) VALUES
(1, 'The Future of Personalized Telemedicine: Beyond Video Calls', 'Exploring how integrated biosensors and real-time data streaming are transforming the remote consultation experience from simple conversations into clinical-grade assessments.\r\n\r\nThe landscape of telemedicine has evolved dramatically over the past few years. What once started as simple video conferencing between doctors and patients has now transformed into a sophisticated ecosystem of interconnected health technologies.\r\n\r\nModern telemedicine platforms are integrating wearable biosensors that can transmit vital signs in real-time during consultations. Heart rate, blood oxygen levels, blood pressure, and even ECG readings can now be captured by consumer-grade devices and streamed directly to the physician\'s dashboard.\r\n\r\nThis shift represents a fundamental change in how we think about remote healthcare. Rather than relying solely on patient-reported symptoms, doctors can now make data-driven decisions during virtual visits, bringing the consultation closer to the accuracy of in-person examinations.\r\n\r\nKey developments include:\r\n- AI-powered symptom analysis that pre-screens patients before consultations\r\n- Integration of home diagnostic kits with telemedicine platforms\r\n- Real-time vital sign monitoring during video consultations\r\n- Automated follow-up scheduling based on consultation outcomes', 'General Health', 'https://ars.els-cdn.com/content/image/X18075932.jpg', 1, 'publie', 1204, 8522, '2026-04-01 09:00:00', '2026-04-16 13:31:01', '2026-04-01 09:00:00'),
(2, '5 Superfoods for Brain Health', 'Research indicates that a diet rich in these specific nutrients can significantly reduce cognitive decline and improve mental clarity.\r\n\r\nThe connection between diet and brain health has been a growing area of scientific research. Studies consistently show that certain foods contain compounds that directly support neural function, protect against oxidative stress, and promote the growth of new brain cells.\r\n\r\n1. **Blueberries** - Rich in anthocyanins, these powerful antioxidants cross the blood-brain barrier and accumulate in areas responsible for learning and memory.\r\n\r\n2. **Fatty Fish** - Salmon, trout, and sardines are excellent sources of omega-3 fatty acids, which are essential building blocks of the brain.\r\n\r\n3. **Turmeric** - Curcumin, the active ingredient in turmeric, has been shown to cross the blood-brain barrier and has anti-inflammatory and antioxidant benefits.\r\n\r\n4. **Broccoli** - High in compounds called glucosinolates, which produce isothiocyanates that may reduce oxidative stress and lower the risk of neurodegenerative diseases.\r\n\r\n5. **Pumpkin Seeds** - Contain zinc, magnesium, copper, and iron ? all crucial for nerve signaling and brain function.', 'Diet & Nutrition', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjJ4UHdtpL6BV1hMMPQ3dqzM-ogrnY6xS_Og&s', 1, 'publie', 850, 4200, '2026-04-03 14:30:00', '2026-04-16 13:27:54', '2026-04-03 14:30:00'),
(3, 'Understanding Sleep Cycles and Recovery', 'How REM and deep sleep stages impact your body\'s daily recovery mechanisms and what you can do to optimize your rest.\r\n\r\nSleep is not a uniform state ? it\'s a complex, dynamic process consisting of multiple stages that cycle throughout the night. Understanding these cycles is key to improving both the quality and effectiveness of your rest.\r\n\r\nThe sleep cycle consists of four stages:\r\n- **Stage 1 (N1)**: Light sleep lasting 1-7 minutes. Your heartbeat, breathing, and eye movements slow down.\r\n- **Stage 2 (N2)**: Deeper sleep where body temperature drops and brain waves show specific patterns called sleep spindles.\r\n- **Stage 3 (N3)**: Deep sleep or slow-wave sleep. This is the most restorative stage where tissue growth and repair occurs.\r\n- **REM Sleep**: Rapid Eye Movement sleep where most dreaming occurs. Critical for memory consolidation and emotional processing.\r\n\r\nEach complete cycle lasts approximately 90 minutes, and a healthy adult goes through 4-6 cycles per night. The proportion of each stage changes throughout the night, with more deep sleep in the first half and more REM sleep in the second half.', 'Research', 'https://www.clmsleep.com/wp-content/uploads/2025/07/stages-of-sleep-2.jpg', 2, 'publie', 432, 3100, '2026-04-05 11:00:00', '2026-04-16 13:28:25', '2026-04-05 11:00:00'),
(4, 'Advancements in Neural Plasticity Research', 'New studies reveal groundbreaking findings about the brain\'s ability to rewire itself, offering hope for stroke recovery and neurodegenerative disease treatments.\r\n\r\nNeuroplasticity ? the brain\'s remarkable ability to reorganize itself by forming new neural connections ? has been one of the most exciting areas of neuroscience research in recent decades.\r\n\r\nRecent clinical trials have demonstrated that targeted rehabilitation programs, combined with non-invasive brain stimulation techniques, can significantly enhance neural plasticity in stroke patients. These findings suggest that the window for recovery may be much wider than previously believed.\r\n\r\nKey findings include:\r\n- Transcranial magnetic stimulation (TMS) combined with physical therapy shows 40% improvement in motor recovery\r\n- Music therapy activates multiple brain regions simultaneously, promoting cross-hemispheric connections\r\n- Virtual reality rehabilitation programs create immersive environments that challenge the brain to adapt\r\n- Pharmacological interventions using BDNF (Brain-Derived Neurotrophic Factor) show promise in animal models', 'Research', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSdq236h91l4XE_Z3tLGLK94uOmlQVbdzn1Q&s', 2, 'publie', 679, 5606, '2026-04-10 08:15:00', '2026-04-16 13:28:40', '2026-04-10 08:15:00'),
(5, 'The Future of Sustainable Healthcare Infrastructure', 'How modern hospitals are embracing green architecture, renewable energy, and sustainable materials to reduce their environmental footprint while improving patient outcomes.\r\n\r\nHealthcare facilities are among the most energy-intensive buildings in any community. They operate 24/7, require sophisticated HVAC systems, and consume vast amounts of water and electricity. But a growing movement in healthcare architecture is proving that sustainability and excellent patient care can go hand in hand.\r\n\r\nLeading medical centers around the world are now:\r\n- Installing solar panels and wind turbines to offset energy consumption\r\n- Using biophilic design principles that incorporate natural elements into healing environments\r\n- Implementing smart building management systems that optimize energy usage in real-time\r\n- Adopting circular economy principles for medical waste management\r\n- Creating green spaces and therapeutic gardens that improve patient recovery times\r\n\r\nStudies show that patients in sustainably designed hospitals recover up to 15% faster and report higher satisfaction scores. The natural lighting, improved air quality, and connection to nature all contribute to better health outcomes.', 'General Health', 'https://www.creativehatti.com/wp-content/uploads/edd/2023/07/Health-medical-clinic-poster-banner-template-78-large.jpg', 1, 'publie', 546, 3807, '2026-04-12 16:45:00', '2026-04-28 19:15:45', '2026-04-12 16:45:00'),
(6, 'Cardiovascular Robotics: A New Frontier', 'Robotic-assisted cardiovascular procedures are revolutionizing how surgeons approach complex heart operations, offering unprecedented precision and faster patient recovery.\r\n\r\nThe integration of robotics into cardiovascular surgery represents one of the most significant technological advances in modern medicine. These sophisticated systems allow surgeons to perform intricate procedures through small incisions with enhanced visualization and greater dexterity than the human hand alone can provide.\r\n\r\nCurrent applications include:\r\n- Robotic-assisted coronary artery bypass grafting (CABG)\r\n- Minimally invasive mitral valve repair\r\n- Catheter-based interventions with robotic navigation\r\n- Hybrid procedures combining traditional and robotic techniques\r\n\r\nThe da Vinci surgical system and newer platforms offer 3D high-definition visualization, motion scaling (converting large hand movements into precise micro-movements), and tremor filtration. These capabilities are particularly valuable in cardiac surgery where precision is literally a matter of life and death.', 'Journals', 'https://www.acc.org//-/media/Non-Clinical/Images/2024/01/CARDIOLOGY/02/Robotics-3-1200x800.jpg', 3, 'publie', 326, 2908, '2026-04-13 10:00:00', '2026-04-27 14:31:04', '2026-04-13 10:00:00'),
(7, 'Epidemiology Trends: 2026 Seasonal Preview', 'A data-driven look at respiratory health and preventive measures for the coming months based on global surveillance data.\r\n\r\nAs we enter the second quarter of 2026, epidemiological data from global health surveillance networks provides crucial insights into what we can expect in terms of seasonal health challenges.\r\n\r\nThis preview is based on data collected from the WHO Global Influenza Surveillance and Response System, CDC FluView, and European Centre for Disease Prevention and Control.\r\n\r\nKey trends for the upcoming season include shifts in influenza strain dominance, emerging respiratory syncytial virus (RSV) patterns, and the continued evolution of COVID-19 variants.\r\n\r\nPreventive recommendations:\r\n- Updated vaccination schedules for high-risk populations\r\n- Enhanced indoor air quality measures in clinical settings\r\n- Community-based health literacy programs\r\n- Early warning systems integration with primary care networks', 'General Health', 'https://img.freepik.com/premium-psd/medical-clinic-poster-design_452208-1049.jpg', 1, 'publie', 293, 2114, '2026-04-14 13:20:00', '2026-04-30 13:52:47', '2026-04-14 13:20:00'),
(8, 'Mental Health in the Digital Age: Navigating Screen Time', 'Understanding the complex relationship between technology use and psychological well-being, with evidence-based strategies for healthier digital habits.\r\n\r\nThe ubiquity of smartphones, social media, and digital entertainment has created an unprecedented challenge for mental health. While technology offers incredible benefits for connectivity and information access, excessive or mindless use can contribute to anxiety, depression, and sleep disorders.\r\n\r\nThis article is currently under review by our editorial board and will be published upon completion of peer review.', 'Mental Wellness', 'https://static.vecteezy.com/system/resources/thumbnails/004/341/503/small/prenatal-clinic-social-media-post-mockup-childbirth-at-hospital-advertising-web-banner-design-template-social-media-booster-content-layout-promotion-poster-print-ads-with-flat-illustrations-vector.jpg', 3, 'brouillon', 0, 0, '2026-04-15 09:00:00', '2026-04-16 13:26:39', NULL),
(11, 'Test', 'aaaaaaavbbb', 'General Health', '/integration/assets/uploads/img_69f3542fe07e15.81578862.jpg', 19, 'publie', 2, 15, '2026-04-24 15:36:32', '2026-04-30 14:08:54', '2026-04-24 17:00:50');

-- --------------------------------------------------------

--
-- Structure de la table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(8, 7, 19, '2026-04-30 13:52:46');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
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
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `image`, `categorie`, `quantite_disponible`, `prix_unitaire`, `seuil_alerte`, `prix_achat`, `fournisseur_matricule`) VALUES
(4, 'mehdi', '/Mediflow1/assets/images/produit/produit_1776022575_69dbf42f00497.png', 'comprim?s', 6, 11.00, 5, 6.00, NULL),
(5, 'bibo', 'assets/images/produit/produit_1777503177_69f28bc982e57.png', 'injectables', 22, 15.00, 5, 5.00, 'AD103'),
(6, 'tito', 'assets/images/produit/produit_1776286564_69dffb6404d5b.jpg', 'comprim?s', 10, 25.00, 5, 2.00, NULL),
(7, 'doliprane 100', 'assets/images/produit/produit_1777503368_69f28c8859009.jpg', '', 3, 500.00, 5, 300.00, 'AD103'),
(9, 'testtttt', 'assets/images/produit/produit_1777499119_69f27bef55dfb.png', 'sirops', 25, 15.00, 5, 65.00, 'FR101');

-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int(11) NOT NULL,
  `medecin_id` int(11) NOT NULL,
  `patient_nom` varchar(100) NOT NULL,
  `patient_prenom` varchar(100) NOT NULL,
  `cin` char(8) NOT NULL,
  `genre` enum('homme','femme') NOT NULL,
  `date_rdv` date NOT NULL,
  `heure_rdv` time NOT NULL,
  `motif` varchar(100) DEFAULT 'Consultation g?n?rale',
  `statut` enum('en_attente','confirme','annule') DEFAULT 'en_attente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id`, `medecin_id`, `patient_nom`, `patient_prenom`, `cin`, `genre`, `date_rdv`, `heure_rdv`, `motif`, `statut`, `created_at`) VALUES
(20, 79, 'khelifi', 'fathi', '14529894', 'homme', '2026-07-22', '14:55:00', 'Consultation g?n?rale', 'en_attente', '2026-05-06 23:37:18');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `equipement_id`, `locataire_nom`, `matricule`, `locataire_ville`, `date_debut`, `date_fin`, `statut`, `telephone`, `created_at`) VALUES
(31, 15, 'abdo samad', 'PT102', '', '2027-07-22', '2027-07-30', 'en_cours', '+216 92 518 333', '2026-04-21 01:15:46'),
(34, 15, 'cherif khalil', 'PT103', '', '2026-07-07', '2026-07-08', 'en_cours', '', '2026-04-23 00:20:22'),
(35, 1, 'fathi khelifi', 'PT102', '', '2026-07-20', '2026-07-26', 'en_cours', '+216 92 518 333', '2026-04-23 01:01:27'),
(36, 15, 'fathi khelifi', NULL, '', '2026-06-22', '2026-06-25', 'en_cours', '', '2026-05-05 14:39:42');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `permission` text DEFAULT NULL,
  `date_creation` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
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
-- Structure de la table `utilisateurs`
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL,
  `onboarding_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_PK`, `matricule`, `nom`, `prenom`, `mail`, `motdp`, `tel`, `adresse`, `id_role`, `status`, `cin`, `created_at`, `updated_at`, `profile_pic`, `onboarding_completed`) VALUES
(19, 'AD103', 'fathi', 'khelifi', 'admin11@mediflow.com', '$2y$10$9TUE.90W5k6IkVyMWzHND.WB4b4brVFoagkE0H8p11z80ObcPju0G', '+216 92 518 333', 'Ariena,Ghazela', 1, 'active', NULL, '2026-04-16 12:40:30', '2026-05-06 23:00:56', '/integration/assets/uploads/profiles/profile_19_1778108456.jpg', 0),
(74, 'TC100', 'nada', 'karoui', 'nada.karoui@mediflow.tn', '$2y$10$1vbqXQTeg6w6NqZ04/V03uBJUEfwyG4uF31atfSdCt.DNMQymxiZi', NULL, NULL, 5, 'active', NULL, '2026-05-06 22:18:54', '2026-05-06 22:18:54', NULL, 0),
(75, 'RD100', 'khalil', 'cherif', 'khalil.cherif@mediflow.tn', '$2y$10$5wD4trN33uwMKHu4PKIn5OYV11Ebev.AHe2345FRlsLO9D92hVnte', NULL, NULL, 6, 'active', NULL, '2026-05-06 22:19:30', '2026-05-06 22:19:30', NULL, 0),
(76, 'PH100', 'mehdi', 'berririP', 'mehdi.berriri.PH@mediflow.tn', '$2y$10$B0jHKuizF1ENr07DbhxVkOwcUD4ryBFJ/X6R8Q4Xw2pfczFw9Q1JC', NULL, NULL, 4, 'active', NULL, '2026-05-06 22:20:16', '2026-05-06 22:20:16', NULL, 0),
(77, 'FR100', 'mehdi', 'berririF', 'mehdi.berriri.F@mediflow.tn', '$2y$10$2W8L./POY1C3.tyKFq6oGeVSE4GFHRTZzs9pJrQWFMGtFzCt9zDFy', NULL, NULL, 10, 'active', NULL, '2026-05-06 22:21:10', '2026-05-06 22:21:10', NULL, 0),
(78, 'RC100', 'eya', 'melouche', 'eya.melouche@mediflow.tn', '$2y$10$AXZ8UOMyrdXZ0iIfEtrdfOFOq17j3IHnyMFog7rQ/enCsgnqr0WqW', NULL, NULL, 3, 'active', NULL, '2026-05-06 22:21:48', '2026-05-06 22:21:48', NULL, 0),
(79, 'MD100', 'yasmine', 'hidri', 'yass@mediflow.tn', '$2y$10$6jx7NQgrmCI6ovu0hoQNeeJ/U.owRnrFcAnKO0PlFK4YV2LZYCS46', '', '', 2, 'active', NULL, '2026-05-06 22:22:31', '2026-05-06 23:26:47', '/integration/assets/uploads/profiles/profile_79_1778110004.jpg', 0),
(82, 'PT100', 'Fathi', 'Khelifi', 'fathikhelifi10@gmail.com', '$2y$10$9ovR2tVCEXxgbqvfjRhMGeP/TjcwsbJDk/Bt52KE5b1rA5ssHCvOe', '', '', 9, 'suspended', NULL, '2026-05-06 23:12:39', '2026-05-06 23:46:38', '/integration/assets/uploads/profiles/profile_82_1778109464.jpg', 1),
(83, 'PT101', 'TN', 'android', 'lastgames70@gmail.com', '$2y$10$.06lMp9tZmoULtDk05g8hOTW4xz7z7DrqJ5xyA0aNZEm0TD6qTwce', '', '', 9, 'active', NULL, '2026-05-06 23:19:43', '2026-05-06 23:38:20', '/integration/assets/uploads/profiles/profile_83_1778109953.png', 1),
(85, 'PT102', 'khelifi11', 'fathi', 'fathikhelifi0769@gmail.com', '$2y$10$k8j4cDgs0nUjqePNx3Qtgem8phj4IznL/GtsfMrJ4Qk4dX1lbFN5K', '', '', 9, 'active', NULL, '2026-05-06 23:39:51', '2026-05-06 23:41:27', '/integration/assets/uploads/profiles/profile_85_1778110867.jpg', 1),
(86, 'AD104', 'fathi', 'fathii', 'admin@gmail.com', '$2y$10$7c8xUCgq5ocS6Lru3EySY.WXkstKzHZ.jDyj31iXYXrshpkW9Z8B.', NULL, NULL, 1, 'active', NULL, '2026-05-06 23:42:38', '2026-05-06 23:42:38', NULL, 0),
(87, 'PT103', 'testF', 'tesstt', 'test123@gmail.com', '$2y$10$OQCbGX7JLJQNVtMtaY5GQ.WbkbRlZLmYM3/x.1FytJD/fNI3Uh3vO', '', '', 9, 'active', NULL, '2026-05-06 23:47:15', '2026-05-06 23:48:23', '/integration/assets/uploads/profiles/profile_87_1778111291.jpg', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_post` (`id_post`),
  ADD KEY `fk_comments_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_comments_statut` (`statut`);

--
-- Index pour la table `consultation`
--
ALTER TABLE `consultation`
  ADD PRIMARY KEY (`id_consultation`),
  ADD KEY `idx_medecin` (`id_medecin`),
  ADD KEY `idx_patient` (`id_patient`),
  ADD KEY `idx_date` (`date_consultation`);

--
-- Index pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contact_patient` (`id_patient`);

--
-- Index pour la table `equipement`
--
ALTER TABLE `equipement`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`);

--
-- Index pour la table `lignescommandes`
--
ALTER TABLE `lignescommandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commande_id` (`commande_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Index pour la table `ordonnance`
--
ALTER TABLE `ordonnance`
  ADD PRIMARY KEY (`id_ordonnance`),
  ADD UNIQUE KEY `uk_numero` (`numero_ordonnance`),
  ADD KEY `idx_consultation` (`id_consultation`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD KEY `key_token` (`token`),
  ADD KEY `key_expires` (`expires_at`);

--
-- Index pour la table `planning`
--
ALTER TABLE `planning`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_planning_medecin` (`medecin_id`),
  ADD KEY `fk_planning_rdv` (`rdv_id`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_posts_auteur` (`auteur_id`),
  ADD KEY `idx_posts_statut` (`statut`),
  ADD KEY `idx_posts_categorie` (`categorie`),
  ADD KEY `idx_posts_date_publication` (`date_publication`);

--
-- Index pour la table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rdv_medecin` (`medecin_id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipement_id` (`equipement_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_PK`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `consultation`
--
ALTER TABLE `consultation`
  MODIFY `id_consultation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `equipement`
--
ALTER TABLE `equipement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `lignescommandes`
--
ALTER TABLE `lignescommandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `ordonnance`
--
ALTER TABLE `ordonnance`
  MODIFY `id_ordonnance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `planning`
--
ALTER TABLE `planning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_PK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `consultation`
--
ALTER TABLE `consultation`
  ADD CONSTRAINT `fk_consult_medecin` FOREIGN KEY (`id_medecin`) REFERENCES `utilisateurs` (`id_PK`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consult_patient` FOREIGN KEY (`id_patient`) REFERENCES `utilisateurs` (`id_PK`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `fk_contact_patient` FOREIGN KEY (`id_patient`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `lignescommandes`
--
ALTER TABLE `lignescommandes`
  ADD CONSTRAINT `lignescommandes_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lignescommandes_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `ordonnance`
--
ALTER TABLE `ordonnance`
  ADD CONSTRAINT `fk_ordo_consultation` FOREIGN KEY (`id_consultation`) REFERENCES `consultation` (`id_consultation`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`email`) REFERENCES `utilisateurs` (`mail`) ON DELETE CASCADE;

--
-- Contraintes pour la table `planning`
--
ALTER TABLE `planning`
  ADD CONSTRAINT `fk_planning_medecin` FOREIGN KEY (`medecin_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_planning_rdv` FOREIGN KEY (`rdv_id`) REFERENCES `rendez_vous` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `fk_rdv_medecin` FOREIGN KEY (`medecin_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`equipement_id`) REFERENCES `equipement` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
