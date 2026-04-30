-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 30 avr. 2026 à 12:21
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

--
-- Déchargement des données de la table `consultation`
--

INSERT INTO `consultation` (`id_consultation`, `id_medecin`, `id_patient`, `date_consultation`, `type_consultation`, `diagnostic`, `compte_rendu`, `tension_arterielle`, `rythme_cardiaque`, `poids`, `saturation_o2`, `antecedents`, `allergies`, `created_at`, `updated_at`) VALUES
(2, 2, 10, '2023-06-02 11:00:00', 'Suivi Spécialisé', 'Suivi cardiologique', 'Examen cardiologique. ECG normal. Repos conseillé.', '13/9', 80, 79.00, 97, '[{\"annee\":\"2018\",\"titre\":\"Hypertension Artérielle\",\"description\":\"Diagnostiquée lors d\\u0027un contrôle de routine.\"},{\"annee\":\"2015\",\"titre\":\"Chirurgie : Appendicectomie\",\"description\":\"Suites simples.\"}]', '[{\"nom\":\"Pénicilline\",\"niveau\":\"Élevé\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(3, 2, 10, '2023-02-22 14:15:00', 'Téléconsultation', 'Syndrome grippal', 'Symptômes grippaux légers. Prescription Doliprane.', '12/7', 88, 78.00, 96, '[{\"annee\":\"2018\",\"titre\":\"Hypertension Artérielle\",\"description\":\"Traitement en cours.\"}]', '[{\"nom\":\"Pénicilline\",\"niveau\":\"Élevé\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(4, 2, 11, '2023-10-12 10:00:00', 'Contrôle Post-Op', 'Rhinite Chronique', 'Légère congestion nasale. Traitement antihistaminique renouvelé.', '11/7', 68, 62.00, 99, '[{\"annee\":\"2020\",\"titre\":\"Rhinite Allergique\",\"description\":\"Traitement Cetirizine 10mg au printemps.\"}]', '[{\"nom\":\"Pollens\",\"niveau\":\"Élevé\"},{\"nom\":\"Acariens\",\"niveau\":\"Modéré\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(5, 2, 12, '2023-10-08 08:45:00', 'Bilan Annuel', 'Diabète Type 2', 'Glycémie à 1.8g/L. Ajustement de la metformine.', '14/9', 76, 95.00, 97, '[{\"annee\":\"2019\",\"titre\":\"Diabète Type 2\",\"description\":\"Traitement Metformine 500mg initié.\"},{\"annee\":\"2021\",\"titre\":\"Neuropathie diabétique légère\",\"description\":\"Suivi mensuel recommandé.\"}]', '[{\"nom\":\"Sulfamides\",\"niveau\":\"Modéré\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(6, 2, 13, '2023-10-05 15:00:00', 'Symptômes Grippaux', 'Bronchite Aiguë', 'Toux productive, fièvre 38.5°C. Prescription antibiotiques.', '12/8', 90, 58.00, 95, '[{\"annee\":\"2022\",\"titre\":\"Bronchite récidivante\",\"description\":\"3 épisodes en 2 ans.\"}]', '[{\"nom\":\"Aspirine\",\"niveau\":\"Faible\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(7, 2, 14, '2023-10-01 09:15:00', 'Suivi Traitement', 'Hypercholestérolémie', 'LDL à 1.6g/L. Bonne tolérance statines. Régime alimentaire maintenu.', '13/8', 74, 82.00, 98, '[{\"annee\":\"2017\",\"titre\":\"Hypercholestérolémie\",\"description\":\"Traitement Rosuvastatine 10mg.\"}]', '[]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(8, 2, 15, '2023-09-28 11:30:00', 'Contrôle annuel', 'Fibromyalgie', 'Douleurs musculaires diffuses maintenues. Physiothérapie recommandée.', '11/7', 66, 65.00, 99, '[{\"annee\":\"2016\",\"titre\":\"Fibromyalgie\",\"description\":\"Traitement par antidépresseurs en faible dose.\"}]', '[{\"nom\":\"AINS\",\"niveau\":\"Modéré\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(9, 2, 16, '2023-09-20 14:00:00', 'Bilan Annuel', 'Hypothyroïdie', 'TSH à 6.2 mUI/L. Augmentation lévothyroxine à 75µg.', '12/8', 58, 71.00, 98, '[{\"annee\":\"2020\",\"titre\":\"Hypothyroïdie\",\"description\":\"Lévothyroxine 50µg. Contrôle TSH tous les 6 mois.\"}]', '[]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(10, 2, 17, '2023-09-15 10:45:00', 'Suivi Spécialisé', 'Arthrose cervicale', 'Douleurs cervicales modérées. Kinésithérapie recommandée 10 séances.', '12/8', 70, 68.00, 98, '[{\"annee\":\"2019\",\"titre\":\"Arthrose cervicale\",\"description\":\"Diagnostiquée par IRM cervicale.\"}]', '[{\"nom\":\"Ibuprofène\",\"niveau\":\"Faible\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(11, 2, 18, '2023-09-10 09:00:00', 'Bilan Annuel', 'Diabète Type 2', 'Bilan satisfaisant. HbA1c à 7.2%.', '13/8', 78, 88.00, 97, '[{\"annee\":\"2018\",\"titre\":\"Diabète Type 2\",\"description\":\"Traitement Metformine + Gliclazide.\"}]', '[]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(12, 3, 19, '2023-10-14 10:00:00', 'Contrôle annuel', 'Asthme Bronchique', 'Peak-flow 420L/min. Bonne maîtrise de l\'asthme.', '11/7', 72, 60.00, 99, '[{\"annee\":\"2010\",\"titre\":\"Asthme Bronchique\",\"description\":\"Traitement par Salbutamol + Fluticasone.\"}]', '[{\"nom\":\"Latex\",\"niveau\":\"Élevé\"},{\"nom\":\"Poussière\",\"niveau\":\"Modéré\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(13, 3, 20, '2023-10-11 11:30:00', 'Suivi Traitement', 'Dépression', 'Amélioration notable. Anxiété réduite. Maintien paroxétine 20mg.', '12/7', 76, 74.00, 98, '[{\"annee\":\"2021\",\"titre\":\"Épisode dépressif majeur\",\"description\":\"Traitement ISRS initié.\"}]', '[]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(14, 3, 21, '2023-10-09 09:30:00', 'Téléconsultation', 'Otite Moyenne', 'Douleur auriculaire droite. Amoxicilline prescrite 10 jours.', '12/8', 84, 55.00, 97, '[{\"annee\":\"2023\",\"titre\":\"Otite récidivante\",\"description\":\"3ème épisode en 1 an.\"}]', '[]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(15, 3, 22, '2023-10-07 14:15:00', 'Consultation urgente', 'Lombalgie Aiguë', 'Contracture paravertébrale L4-L5. AINS + myorelaxant prescrit.', '13/8', 80, 80.00, 98, '[{\"annee\":\"2022\",\"titre\":\"Hernie discale L4-L5\",\"description\":\"Traitement conservateur. Pas de chirurgie.\"}]', '[{\"nom\":\"Morphine\",\"niveau\":\"Modéré\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(16, 3, 23, '2023-10-03 10:00:00', 'Bilan Annuel', 'Migraines Chroniques', 'Fréquence 3 crises/mois. Augmentation Topiramate à 75mg.', '11/7', 68, 58.00, 99, '[{\"annee\":\"2015\",\"titre\":\"Migraines sans aura\",\"description\":\"Traitement de fond par bêtabloquants.\"}]', '[{\"nom\":\"Codéine\",\"niveau\":\"Élevé\"}]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(17, 3, 24, '2023-09-30 16:00:00', 'Contrôle annuel', 'Hypercholestérolémie', 'LDL stable à 1.4g/L. Bon suivi régime.', '12/8', 72, 78.00, 98, '[{\"annee\":\"2020\",\"titre\":\"Hypercholestérolémie familiale\",\"description\":\"Rosuvastatine 20mg.\"}]', '[]', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(18, 2, 10, '2026-04-13 22:57:00', 'Contrôle annuel', 'hhhhh', 'eee', '', NULL, NULL, NULL, '[{\"annee\":\"2018\",\"titre\":\"Hypertension Artérielle\",\"description\":\"Diagnostiquée lors d\'un contrôle de routine. Traitement par Amlodipine 5mg initié.\"},{\"annee\":\"2015\",\"titre\":\"Chirurgie : Appendicectomie\",\"description\":\"Hôpital Américain de Paris. Suites opératoires simples, sans complications.\"},{\"annee\":\"2002\",\"titre\":\"Asthme à l\'effort\",\"description\":\"Traitement ponctuel par Ventoline si nécessaire.\"}]', '[{\"nom\":\"Pénicilline\",\"niveau\":\"Élevé\"},{\"nom\":\"Arachides\",\"niveau\":\"Modéré\"}]', '2026-04-13 20:58:37', '2026-04-16 13:04:01'),
(19, 2, 12, '2026-04-13 23:11:00', 'Contrôle annuel', 'mahboul', 'habelni m3ah', '', NULL, NULL, NULL, '[{\"annee\":\"2019\",\"titre\":\"Diabète Type 2\",\"description\":\"Traitement Metformine 500mg initié.\"},{\"annee\":\"2021\",\"titre\":\"Neuropathie diabétique légère\",\"description\":\"Suivi mensuel recommandé.\"},{\"annee\":\"2025\",\"titre\":\"Hypertension Artérielle\",\"description\":\"5000000\"}]', '[{\"nom\":\"Sulfamides\",\"niveau\":\"Modéré\"}]', '2026-04-13 21:12:41', '2026-04-13 21:22:12'),
(20, 2, 14, '2026-04-13 23:57:00', 'Bilan Annuel', 'hhh', '', '', NULL, NULL, NULL, '[]', '[]', '2026-04-13 21:58:26', '2026-04-13 21:58:26'),
(21, 2, 14, '2026-04-15 23:50:00', 'Suivi Traitement', 'gxdvgh', 'gcgvu', 'ghui', NULL, NULL, NULL, '[]', '[]', '2026-04-15 21:50:29', '2026-04-15 21:50:29'),
(22, 2, 14, '2026-04-15 23:50:00', 'Contrôle annuel', '', '', '', NULL, NULL, NULL, '[]', '[{\"nom\":\"Sulfamides\",\"niveau\":\"Modéré\"}]', '2026-04-15 21:51:11', '2026-04-15 21:51:11'),
(23, 2, 14, '2026-04-15 23:51:00', 'Contrôle annuel', '', '', '1-58', 298, 512.00, NULL, '[]', '[]', '2026-04-15 21:51:45', '2026-04-15 21:51:45'),
(24, 2, 10, '2026-04-20 22:53:11', 'Contrôle annuel', 'Test diagnostic', 'Test compte rendu', '120/80', 72, 75.50, 98, '[{\"annee\":\"2020\",\"titre\":\"Test\",\"description\":\"Test desc\"}]', '[{\"nom\":\"Test allergy\",\"niveau\":\"Mod\\u00e9r\\u00e9\"}]', '2026-04-20 20:53:11', '2026-04-20 20:53:11'),
(25, 2, 10, '2026-04-20 10:00:00', 'Contrôle annuel', 'Test diagnostic', 'Test compte rendu', '120/80', 72, 75.50, 98, '[{\"annee\":\"2020\",\"titre\":\"Test antecedent\",\"description\":\"Test description\"}]', '[{\"nom\":\"Test allergy\",\"niveau\":\"Modéré\"}]', '2026-04-20 20:55:09', '2026-04-20 20:55:09'),
(26, 2, 13, '2026-04-09 22:56:00', 'Téléconsultation', 'mrayedh', '', '120/85', 55, 25.00, 99, '[{\"annee\":\"2025\",\"titre\":\"Diabète Type 2\",\"description\":\"Diagnostiquée lors d\'un contrôle de routine. Traitement par Amlodipine 5mg initié.\"}]', '[{\"nom\":\"Sulfamides\",\"niveau\":\"Modéré\"}]', '2026-04-20 20:58:25', '2026-04-20 20:58:25'),
(27, 2, 10, '2026-04-02 20:43:00', 'Contrôle Post-Op', 'hypertension', 'une maladie grave', '120/85', 55, 25.00, 99, '[]', '[]', '2026-04-28 18:44:29', '2026-04-28 18:44:29'),
(28, 2, 10, '2026-04-28 19:45:00', 'Consultation urgente', 'hypertension', 'grave', '151/80', 75, 24.80, 22, '[{\"annee\":\"2020\",\"titre\":\"Test\",\"description\":\"Test desc\"}]', '[{\"nom\":\"Test allergy\",\"niveau\":\"Modéré\"}]', '2026-04-28 18:47:00', '2026-04-28 18:47:00'),
(29, 2, 15, '2026-04-28 20:07:00', 'Téléconsultation', 'hypertension', '', '120/85', 55, 25.00, 99, '[]', '[]', '2026-04-28 19:08:32', '2026-04-28 19:08:32');

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

--
-- Déchargement des données de la table `ordonnance`
--

INSERT INTO `ordonnance` (`id_ordonnance`, `id_consultation`, `numero_ordonnance`, `date_emission`, `medicaments`, `note_pharmacien`, `statut`, `created_at`, `updated_at`) VALUES
(2, 3, 'ORD-2023-002', '2023-02-22', '[{\"nom\":\"Paracétamol\",\"dosage\":\"1000mg\",\"frequence\":\"1 comprimé toutes les 6h\",\"duree\":\"5 jours\",\"instructions\":\"Ne pas dépasser 4g par 24h.\",\"categorie\":\"Analgésique\"},{\"nom\":\"Oseltamivir\",\"dosage\":\"75mg\",\"frequence\":\"2 fois par jour\",\"duree\":\"5 jours\",\"instructions\":\"À prendre pendant les repas.\",\"categorie\":\"Antiviral\"}]', 'Génériques autorisés.', 'archivee', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(3, 6, 'ORD-2023-003', '2023-10-05', '[{\"nom\":\"Amoxicilline\",\"dosage\":\"500mg\",\"frequence\":\"3 fois par jour\",\"duree\":\"7 jours\",\"instructions\":\"À prendre pendant les repas avec un grand verre d\'eau. Terminer impérativement la boîte.\",\"categorie\":\"Antibiotique\"},{\"nom\":\"Paracétamol\",\"dosage\":\"1000mg\",\"frequence\":\"Toutes les 6 heures\",\"duree\":\"Si fièvre uniquement\",\"instructions\":\"Ne pas dépasser 4g par 24h.\",\"categorie\":\"Analgésique\"},{\"nom\":\"Sirop Hélicidine\",\"dosage\":\"1 cuillère à soupe\",\"frequence\":\"Avant le coucher\",\"duree\":\"5 jours\",\"instructions\":\"Agiter avant emploi.\",\"categorie\":\"Antitussif\"}]', 'Substitution générique autorisée sauf pour l\'Amoxicilline (marque spécifique pour tolérance).', 'active', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(4, 7, 'ORD-2023-004', '2023-10-01', '[{\"nom\":\"Rosuvastatine\",\"dosage\":\"10mg\",\"frequence\":\"1 comprimé le soir\",\"duree\":\"30 jours\",\"instructions\":\"Éviter le jus de pamplemousse.\",\"categorie\":\"Hypolipémiant\"}]', 'Générique autorisé.', 'active', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(5, 5, 'ORD-2023-005', '2023-10-08', '[]', 'Génériques autorisés pour la Metformine.', 'archivee', '2026-04-13 20:47:43', '2026-04-13 21:10:16'),
(6, 13, 'ORD-2023-006', '2023-10-05', '[{\"nom\":\"Amoxicilline\",\"dosage\":\"500mg\",\"frequence\":\"3 fois par jour\",\"duree\":\"10 jours\",\"instructions\":\"À prendre pendant les repas.\",\"categorie\":\"Antibiotique\"},{\"nom\":\"Paracétamol\",\"dosage\":\"500mg\",\"frequence\":\"3 fois par jour\",\"duree\":\"5 jours\",\"instructions\":\"En cas de douleur.\",\"categorie\":\"Analgésique\"}]', 'Pas de substitution pour Amoxicilline.', 'active', '2026-04-13 20:47:43', '2026-04-13 20:47:43'),
(8, 19, 'ORD-2026-69DD5EB563DFA', '2026-04-13', '[]', '', 'active', '2026-04-13 21:23:01', '2026-04-15 18:17:38'),
(10, 19, 'ORD-2026-69E00D70EC066', '2026-04-15', '[{\"nom\":\"hdijz\",\"dosage\":\"10000mg\",\"frequence\":\"z\",\"duree\":\"5dd\",\"instructions\":\"dz\",\"categorie\":\"dez\"}]', '', 'active', '2026-04-15 22:13:04', '2026-04-15 22:13:04'),
(11, 23, 'ORD-2026-69E0E2CC5D113', '2026-04-15', '[{\"nom\":\"Metformine\",\"dosage\":\"100\",\"frequence\":\"3\",\"duree\":\"46\",\"instructions\":\"Ne pas dépasser 4g par 24h.\",\"categorie\":\"Analgésique\"}]', '', 'active', '2026-04-16 13:23:24', '2026-04-16 13:23:24');

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
(3, 'Rendez-vous', 'appointments,billing', '2026-04-11'),
(4, 'Stock medicament', 'medication_stock', '2026-04-11'),
(5, 'Equipment', 'equipment_management', '2026-04-11'),
(6, 'Magazine', NULL, '2026-04-11'),
(7, 'Patient', 'view_own_records', '2026-04-13');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_PK` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `motdp` varchar(255) NOT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_PK`, `nom`, `prenom`, `mail`, `motdp`, `tel`, `adresse`, `id_role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Test', 'admin@mediflow.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+212612345678', 'Tunis, Tunisia', 1, '2026-04-11 15:39:37', '2026-04-11 15:56:31'),
(2, 'Dupont', 'khalil', 'medecin1@mediflow.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+212687654321', 'Tunis, Tunisia', 2, '2026-04-11 15:39:37', '2026-04-15 19:32:45'),
(3, 'Martin', 'Marie', 'medecin2@mediflow.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+212612345679', 'Tunis, Tunisia', 2, '2026-04-11 15:39:37', '2026-04-11 15:56:31'),
(5, 'Dupuis', 'Sophie', 'stock@mediflow.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+212611223344', 'Tunis, Tunisia', 4, '2026-04-11 15:39:37', '2026-04-11 15:56:31'),
(6, 'Robert', 'Pierre', 'equipment@mediflow.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+212622334455', 'Tunis, Tunisia', 2, '2026-04-11 15:39:37', '2026-04-15 19:03:52'),
(10, 'Dupont', 'Jean', 'jean.dupont@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33612345678', '42 Rue de la Paix, 75002 Paris', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(11, 'Martin', 'Sophie', 'sophie.martin@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33698765432', '15 Avenue Victor Hugo, 69001 Lyon', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(12, 'Bernard', 'Thomas', 'thomas.bernard@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33611223344', '8 Rue des Fleurs, 13001 Marseille', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(13, 'Leclerc', 'Marie', 'marie.leclerc@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33622334455', '3 Boulevard Haussmann, 75009 Paris', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(14, 'Morel', 'Pierre', 'pierre.morel@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33633445566', '27 Rue Nationale, 59800 Lille', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(15, 'Laurent', 'Isabelle', 'isabelle.laurent@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33644556677', '12 Cours Mirabeau, 13100 Aix-en-Provence', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(16, 'Simon', 'François', 'francois.simon@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33655667788', '5 Rue du Commerce, 31000 Toulouse', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(17, 'Michel', 'Nathalie', 'nathalie.michel@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33666778899', '18 Rue Sainte-Catherine, 33000 Bordeaux', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(18, 'Leroy', 'Marc', 'marc.leroy@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33677889900', '9 Place Bellecour, 69002 Lyon', 2, '2026-04-13 20:47:42', '2026-04-15 19:11:47'),
(19, 'Roux', 'Catherine', 'catherine.roux@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '5505621866', '34 Rue Pasteur, 67000 Strasbourg', 2, '2026-04-13 20:47:42', '2026-04-15 20:01:24'),
(20, 'David', 'Nicolas', 'nicolas.david@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33699001122', '7 Rue de la République, 06000 Nice', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(21, 'Petit', 'Elena', 'elena.petit@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33600112233', '21 Chemin des Roses, 44000 Nantes', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(22, 'Moreau', 'Louis', 'louis.moreau@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33611223345', '6 Rue Montmartre, 75001 Paris', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(23, 'Girard', 'Claire', 'claire.girard@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33622334456', '19 Avenue du Général de Gaulle, 92100 Boulogne', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42'),
(24, 'Dubois', 'Thomas', 'thomas.dubois2@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33633445567', '11 Rue Félix Faure, 06000 Nice', 7, '2026-04-13 20:47:42', '2026-04-13 20:47:42');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `consultation`
--
ALTER TABLE `consultation`
  ADD PRIMARY KEY (`id_consultation`),
  ADD KEY `idx_medecin` (`id_medecin`),
  ADD KEY `idx_patient` (`id_patient`),
  ADD KEY `idx_date` (`date_consultation`);

--
-- Index pour la table `ordonnance`
--
ALTER TABLE `ordonnance`
  ADD PRIMARY KEY (`id_ordonnance`),
  ADD UNIQUE KEY `uk_numero` (`numero_ordonnance`),
  ADD KEY `idx_consultation` (`id_consultation`);

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
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `consultation`
--
ALTER TABLE `consultation`
  MODIFY `id_consultation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `ordonnance`
--
ALTER TABLE `ordonnance`
  MODIFY `id_ordonnance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_PK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
-- Contraintes pour la table `ordonnance`
--
ALTER TABLE `ordonnance`
  ADD CONSTRAINT `fk_ordo_consultation` FOREIGN KEY (`id_consultation`) REFERENCES `consultation` (`id_consultation`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
