-- ============================================================
-- MediFlow — Dossier Médical Migration
-- Run this against the main `mediflow` database (ELBASE)
-- Adds: consultation, ordonnance, contact_messages tables
-- ============================================================

-- Disable FK checks during import
SET FOREIGN_KEY_CHECKS = 0;

-- ── consultation ─────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `consultation` (
  `id_consultation` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_consultation`),
  KEY `idx_medecin` (`id_medecin`),
  KEY `idx_patient` (`id_patient`),
  KEY `idx_date` (`date_consultation`),
  CONSTRAINT `fk_consult_medecin` FOREIGN KEY (`id_medecin`) REFERENCES `utilisateurs` (`id_PK`) ON UPDATE CASCADE,
  CONSTRAINT `fk_consult_patient` FOREIGN KEY (`id_patient`) REFERENCES `utilisateurs` (`id_PK`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── ordonnance ───────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `ordonnance` (
  `id_ordonnance` int(11) NOT NULL AUTO_INCREMENT,
  `id_consultation` int(11) NOT NULL,
  `numero_ordonnance` varchar(50) DEFAULT NULL,
  `date_emission` date NOT NULL DEFAULT (curdate()),
  `medicaments` longtext NOT NULL COMMENT 'JSON array of medication objects',
  `note_pharmacien` text DEFAULT NULL,
  `statut` enum('active','archivee','annulee') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_ordonnance`),
  UNIQUE KEY `uk_numero` (`numero_ordonnance`),
  KEY `idx_consultation` (`id_consultation`),
  CONSTRAINT `fk_ordo_consultation` FOREIGN KEY (`id_consultation`)
    REFERENCES `consultation` (`id_consultation`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── contact_messages (for patient→team messaging) ────────────────

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_patient` int(11) NOT NULL,
  `sujet` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `date_message` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` enum('unread','read','archived') NOT NULL DEFAULT 'unread',
  PRIMARY KEY (`id`),
  KEY `fk_contact_patient` (`id_patient`),
  CONSTRAINT `fk_contact_patient` FOREIGN KEY (`id_patient`)
    REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Sample data (from mediflow(1).sql, adjusted for ELBASE users) ─
-- NOTE: Only insert if medecin IDs (20, 21) and patient IDs (16,17,18,23) exist in ELBASE utilisateurs.
-- Adjust id_medecin and id_patient to match IDs present in YOUR database.
-- The safe approach: run the INSERT IGNORE statements below after verifying IDs.

-- Insert consultations (using ELBASE user IDs: medecins = role libelle 'Medecin', patients = role libelle 'Patient')
-- Medecin IDs in ELBASE: 20 (PT102/fathi khelifi - role 2), 21 (MD100), 24 (MD200/Adam Smith)
-- Patient IDs in ELBASE: 16 (AD102/fathi), 17 (PT100), 18 (PT101/abdo), 23 (PT103/khalil), 25 (PT200/John Doe)

INSERT IGNORE INTO `consultation`
  (`id_consultation`, `id_medecin`, `id_patient`, `date_consultation`, `type_consultation`,
   `diagnostic`, `compte_rendu`, `tension_arterielle`, `rythme_cardiaque`, `poids`,
   `saturation_o2`, `antecedents`, `allergies`)
VALUES
(1, 20, 17, '2023-06-02 11:00:00', 'Suivi Spécialisé', 'Suivi cardiologique',
 'Examen cardiologique. ECG normal. Repos conseillé.', '13/9', 80, 79.00, 97,
 '[{"annee":"2018","titre":"Hypertension Artérielle","description":"Diagnostiquée lors d\'un contrôle de routine."}]',
 '[{"nom":"Pénicilline","niveau":"Élevé"}]'),
(2, 20, 18, '2023-10-12 10:00:00', 'Contrôle Post-Op', 'Rhinite Chronique',
 'Légère congestion nasale. Traitement antihistaminique renouvelé.', '11/7', 68, 62.00, 99,
 '[{"annee":"2020","titre":"Rhinite Allergique","description":"Traitement Cetirizine 10mg au printemps."}]',
 '[{"nom":"Pollens","niveau":"Élevé"},{"nom":"Acariens","niveau":"Modéré"}]'),
(3, 21, 23, '2023-10-03 10:00:00', 'Bilan Annuel', 'Migraines Chroniques',
 'Fréquence 3 crises/mois. Augmentation Topiramate à 75mg.', '11/7', 68, 58.00, 99,
 '[{"annee":"2015","titre":"Migraines sans aura","description":"Traitement de fond par bêtabloquants."}]',
 '[{"nom":"Codéine","niveau":"Élevé"}]'),
(4, 24, 25, '2026-04-28 19:45:00', 'Consultation urgente', 'Hypertension',
 'Tension élevée. Prescription Amlodipine 5mg.', '151/80', 75, 72.00, 97,
 '[]', '[{"nom":"Aspirine","niveau":"Faible"}]');

-- Insert ordonnances linked to the above consultations
INSERT IGNORE INTO `ordonnance`
  (`id_ordonnance`, `id_consultation`, `numero_ordonnance`, `date_emission`, `medicaments`, `note_pharmacien`, `statut`)
VALUES
(1, 2, 'ORD-2023-001', '2023-10-12',
 '[{"nom":"Cetirizine","dosage":"10mg","frequence":"1 fois par jour","duree":"30 jours","instructions":"Le soir au coucher.","categorie":"Antihistaminique"}]',
 'Générique autorisé.', 'active'),
(2, 4, 'ORD-2026-001', '2026-04-28',
 '[{"nom":"Amlodipine","dosage":"5mg","frequence":"1 fois par jour","duree":"30 jours","instructions":"Le matin avec un grand verre d\'eau.","categorie":"Antihypertenseur"}]',
 'Pas de substitution.', 'active');

SET FOREIGN_KEY_CHECKS = 1;

-- ── Done! ────────────────────────────────────────────────────────
-- Now register the new routes in Core/App.php
-- and add sidebar links in Views/Back/layout.php
