-- ============================================================
-- MediFlow — Module: Gestion du Dossier Médical
-- Tables: consultation, ordonnance
-- Author: Gestion du Dossier Médical team member
-- NOTE: We only add INSERT data to existing tables (utilisateurs, roles).
--       We do NOT modify their structure.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

USE `mediflow`;

-- ============================================================
-- 1. Add Patient role to existing `roles` table
-- ============================================================
INSERT IGNORE INTO `roles` (`id_role`, `libelle`, `permission`, `date_creation`) VALUES
(7, 'Patient', 'view_own_records', CURDATE());

-- ============================================================
-- 2. Add demo patients to existing `utilisateurs` table
--    Password for all: "password123" (bcrypt hash)
-- ============================================================
INSERT IGNORE INTO `utilisateurs`
  (`id_PK`, `nom`, `prenom`, `mail`, `motdp`, `tel`, `adresse`, `id_role`, `created_at`, `updated_at`)
VALUES
(10, 'Dupont',    'Jean',      'jean.dupont@email.com',     '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33612345678', '42 Rue de la Paix, 75002 Paris', 7, NOW(), NOW()),
(11, 'Martin',    'Sophie',    'sophie.martin@email.com',   '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33698765432', '15 Avenue Victor Hugo, 69001 Lyon', 7, NOW(), NOW()),
(12, 'Bernard',   'Thomas',    'thomas.bernard@email.com',  '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33611223344', '8 Rue des Fleurs, 13001 Marseille', 7, NOW(), NOW()),
(13, 'Leclerc',   'Marie',     'marie.leclerc@email.com',   '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33622334455', '3 Boulevard Haussmann, 75009 Paris', 7, NOW(), NOW()),
(14, 'Morel',     'Pierre',    'pierre.morel@email.com',    '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33633445566', '27 Rue Nationale, 59800 Lille', 7, NOW(), NOW()),
(15, 'Laurent',   'Isabelle',  'isabelle.laurent@email.com','$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33644556677', '12 Cours Mirabeau, 13100 Aix-en-Provence', 7, NOW(), NOW()),
(16, 'Simon',     'François',  'francois.simon@email.com',  '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33655667788', '5 Rue du Commerce, 31000 Toulouse', 7, NOW(), NOW()),
(17, 'Michel',    'Nathalie',  'nathalie.michel@email.com', '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33666778899', '18 Rue Sainte-Catherine, 33000 Bordeaux', 7, NOW(), NOW()),
(18, 'Leroy',     'Marc',      'marc.leroy@email.com',      '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33677889900', '9 Place Bellecour, 69002 Lyon', 7, NOW(), NOW()),
(19, 'Roux',      'Catherine', 'catherine.roux@email.com',  '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33688990011', '34 Rue Pasteur, 67000 Strasbourg', 7, NOW(), NOW()),
(20, 'David',     'Nicolas',   'nicolas.david@email.com',   '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33699001122', '7 Rue de la République, 06000 Nice', 7, NOW(), NOW()),
(21, 'Petit',     'Elena',     'elena.petit@email.com',     '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33600112233', '21 Chemin des Roses, 44000 Nantes', 7, NOW(), NOW()),
(22, 'Moreau',    'Louis',     'louis.moreau@email.com',    '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33611223345', '6 Rue Montmartre, 75001 Paris', 7, NOW(), NOW()),
(23, 'Girard',    'Claire',    'claire.girard@email.com',   '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33622334456', '19 Avenue du Général de Gaulle, 92100 Boulogne', 7, NOW(), NOW()),
(24, 'Dubois',    'Thomas',    'thomas.dubois2@email.com',  '$2y$10$DWh64If5QpqCT.LEvRwP0O2XWg3AS6C32H5qOpEkig8EOsEnxK5Wy', '+33633445567', '11 Rue Félix Faure, 06000 Nice', 7, NOW(), NOW());

-- ============================================================
-- 3. Create `consultation` table
-- ============================================================
CREATE TABLE IF NOT EXISTS `consultation` (
  `id_consultation`    INT(11) NOT NULL AUTO_INCREMENT,
  `id_medecin`         INT(11) NOT NULL,
  `id_patient`         INT(11) NOT NULL,
  `date_consultation`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_consultation`  VARCHAR(100) DEFAULT NULL,
  `diagnostic`         TEXT DEFAULT NULL,
  `compte_rendu`       TEXT DEFAULT NULL,
  `tension_arterielle` VARCHAR(20) DEFAULT NULL,
  `rythme_cardiaque`   INT(11) DEFAULT NULL,
  `poids`              DECIMAL(5,2) DEFAULT NULL,
  `saturation_o2`      INT(11) DEFAULT NULL,
  `antecedents`        LONGTEXT DEFAULT NULL COMMENT 'JSON array',
  `allergies`          LONGTEXT DEFAULT NULL COMMENT 'JSON array',
  `created_at`         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_consultation`),
  KEY `idx_medecin` (`id_medecin`),
  KEY `idx_patient` (`id_patient`),
  KEY `idx_date`    (`date_consultation`),
  CONSTRAINT `fk_consult_medecin` FOREIGN KEY (`id_medecin`) REFERENCES `utilisateurs` (`id_PK`) ON UPDATE CASCADE,
  CONSTRAINT `fk_consult_patient` FOREIGN KEY (`id_patient`) REFERENCES `utilisateurs` (`id_PK`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. Create `ordonnance` table
-- ============================================================
CREATE TABLE IF NOT EXISTS `ordonnance` (
  `id_ordonnance`     INT(11) NOT NULL AUTO_INCREMENT,
  `id_consultation`   INT(11) NOT NULL,
  `numero_ordonnance` VARCHAR(50) DEFAULT NULL,
  `date_emission`     DATE NOT NULL DEFAULT (CURDATE()),
  `medicaments`       LONGTEXT NOT NULL COMMENT 'JSON array of medication objects',
  `note_pharmacien`   TEXT DEFAULT NULL,
  `statut`            ENUM('active','archivee','annulee') NOT NULL DEFAULT 'active',
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ordonnance`),
  UNIQUE KEY `uk_numero` (`numero_ordonnance`),
  KEY `idx_consultation` (`id_consultation`),
  CONSTRAINT `fk_ordo_consultation` FOREIGN KEY (`id_consultation`) REFERENCES `consultation` (`id_consultation`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. Demo consultation data (doctor id=2: Dupont Jean / Medecin)
-- ============================================================
INSERT INTO `consultation`
  (`id_medecin`, `id_patient`, `date_consultation`, `type_consultation`, `diagnostic`, `compte_rendu`, `tension_arterielle`, `rythme_cardiaque`, `poids`, `saturation_o2`, `antecedents`, `allergies`)
VALUES
-- Patients of Dr. Dupont (id=2)
(2, 10, '2023-10-15 09:30:00', 'Contrôle annuel',    'Hypertension Artérielle',  'État stable, tension 12/8. Renouvellement ordonnance.', '12/8', 72, 78.50, 98,
  '[{"annee":"2018","titre":"Hypertension Artérielle","description":"Diagnostiquée lors d\\u0027un contrôle de routine. Traitement par Amlodipine 5mg initié."},{"annee":"2015","titre":"Chirurgie : Appendicectomie","description":"Hôpital Américain de Paris. Suites opératoires simples, sans complications."},{"annee":"2002","titre":"Asthme à l\\u0027effort","description":"Traitement ponctuel par Ventoline si nécessaire."}]',
  '[{"nom":"Pénicilline","niveau":"Élevé"},{"nom":"Arachides","niveau":"Modéré"}]'),

(2, 10, '2023-06-02 11:00:00', 'Suivi Spécialisé',   'Suivi cardiologique',      'Examen cardiologique. ECG normal. Repos conseillé.',    '13/9', 80, 79.00, 97,
  '[{"annee":"2018","titre":"Hypertension Artérielle","description":"Diagnostiquée lors d\\u0027un contrôle de routine."},{"annee":"2015","titre":"Chirurgie : Appendicectomie","description":"Suites simples."}]',
  '[{"nom":"Pénicilline","niveau":"Élevé"}]'),

(2, 10, '2023-02-22 14:15:00', 'Téléconsultation',   'Syndrome grippal',         'Symptômes grippaux légers. Prescription Doliprane.',    '12/7', 88, 78.00, 96,
  '[{"annee":"2018","titre":"Hypertension Artérielle","description":"Traitement en cours."}]',
  '[{"nom":"Pénicilline","niveau":"Élevé"}]'),

(2, 11, '2023-10-12 10:00:00', 'Contrôle Post-Op',   'Rhinite Chronique',        'Légère congestion nasale. Traitement antihistaminique renouvelé.', '11/7', 68, 62.00, 99,
  '[{"annee":"2020","titre":"Rhinite Allergique","description":"Traitement Cetirizine 10mg au printemps."}]',
  '[{"nom":"Pollens","niveau":"Élevé"},{"nom":"Acariens","niveau":"Modéré"}]'),

(2, 12, '2023-10-08 08:45:00', 'Bilan Annuel',        'Diabète Type 2',           'Glycémie à 1.8g/L. Ajustement de la metformine.', '14/9', 76, 95.00, 97,
  '[{"annee":"2019","titre":"Diabète Type 2","description":"Traitement Metformine 500mg initié."},{"annee":"2021","titre":"Neuropathie diabétique légère","description":"Suivi mensuel recommandé."}]',
  '[{"nom":"Sulfamides","niveau":"Modéré"}]'),

(2, 13, '2023-10-05 15:00:00', 'Symptômes Grippaux', 'Bronchite Aiguë',          'Toux productive, fièvre 38.5°C. Prescription antibiotiques.', '12/8', 90, 58.00, 95,
  '[{"annee":"2022","titre":"Bronchite récidivante","description":"3 épisodes en 2 ans."}]',
  '[{"nom":"Aspirine","niveau":"Faible"}]'),

(2, 14, '2023-10-01 09:15:00', 'Suivi Traitement',   'Hypercholestérolémie',     'LDL à 1.6g/L. Bonne tolérance statines. Régime alimentaire maintenu.', '13/8', 74, 82.00, 98,
  '[{"annee":"2017","titre":"Hypercholestérolémie","description":"Traitement Rosuvastatine 10mg."}]',
  '[]'),

(2, 15, '2023-09-28 11:30:00', 'Contrôle annuel',    'Fibromyalgie',             'Douleurs musculaires diffuses maintenues. Physiothérapie recommandée.', '11/7', 66, 65.00, 99,
  '[{"annee":"2016","titre":"Fibromyalgie","description":"Traitement par antidépresseurs en faible dose."}]',
  '[{"nom":"AINS","niveau":"Modéré"}]'),

(2, 16, '2023-09-20 14:00:00', 'Bilan Annuel',        'Hypothyroïdie',            'TSH à 6.2 mUI/L. Augmentation lévothyroxine à 75µg.', '12/8', 58, 71.00, 98,
  '[{"annee":"2020","titre":"Hypothyroïdie","description":"Lévothyroxine 50µg. Contrôle TSH tous les 6 mois."}]',
  '[]'),

(2, 17, '2023-09-15 10:45:00', 'Suivi Spécialisé',   'Arthrose cervicale',       'Douleurs cervicales modérées. Kinésithérapie recommandée 10 séances.', '12/8', 70, 68.00, 98,
  '[{"annee":"2019","titre":"Arthrose cervicale","description":"Diagnostiquée par IRM cervicale."}]',
  '[{"nom":"Ibuprofène","niveau":"Faible"}]'),

(2, 18, '2023-09-10 09:00:00', 'Bilan Annuel',        'Diabète Type 2',           'Bilan satisfaisant. HbA1c à 7.2%.', '13/8', 78, 88.00, 97,
  '[{"annee":"2018","titre":"Diabète Type 2","description":"Traitement Metformine + Gliclazide."}]',
  '[]'),

-- Patients of Dr. Martin (id=3)
(3, 19, '2023-10-14 10:00:00', 'Contrôle annuel',    'Asthme Bronchique',        'Peak-flow 420L/min. Bonne maîtrise de l\'asthme.', '11/7', 72, 60.00, 99,
  '[{"annee":"2010","titre":"Asthme Bronchique","description":"Traitement par Salbutamol + Fluticasone."}]',
  '[{"nom":"Latex","niveau":"Élevé"},{"nom":"Poussière","niveau":"Modéré"}]'),

(3, 20, '2023-10-11 11:30:00', 'Suivi Traitement',   'Dépression',               'Amélioration notable. Anxiété réduite. Maintien paroxétine 20mg.', '12/7', 76, 74.00, 98,
  '[{"annee":"2021","titre":"Épisode dépressif majeur","description":"Traitement ISRS initié."}]',
  '[]'),

(3, 21, '2023-10-09 09:30:00', 'Téléconsultation',   'Otite Moyenne',            'Douleur auriculaire droite. Amoxicilline prescrite 10 jours.', '12/8', 84, 55.00, 97,
  '[{"annee":"2023","titre":"Otite récidivante","description":"3ème épisode en 1 an."}]',
  '[]'),

(3, 22, '2023-10-07 14:15:00', 'Consultation urgente','Lombalgie Aiguë',         'Contracture paravertébrale L4-L5. AINS + myorelaxant prescrit.', '13/8', 80, 80.00, 98,
  '[{"annee":"2022","titre":"Hernie discale L4-L5","description":"Traitement conservateur. Pas de chirurgie."}]',
  '[{"nom":"Morphine","niveau":"Modéré"}]'),

(3, 23, '2023-10-03 10:00:00', 'Bilan Annuel',        'Migraines Chroniques',    'Fréquence 3 crises/mois. Augmentation Topiramate à 75mg.', '11/7', 68, 58.00, 99,
  '[{"annee":"2015","titre":"Migraines sans aura","description":"Traitement de fond par bêtabloquants."}]',
  '[{"nom":"Codéine","niveau":"Élevé"}]'),

(3, 24, '2023-09-30 16:00:00', 'Contrôle annuel',    'Hypercholestérolémie',    'LDL stable à 1.4g/L. Bon suivi régime.', '12/8', 72, 78.00, 98,
  '[{"annee":"2020","titre":"Hypercholestérolémie familiale","description":"Rosuvastatine 20mg."}]',
  '[]');

-- ============================================================
-- 6. Demo ordonnance data
-- ============================================================
INSERT INTO `ordonnance`
  (`id_consultation`, `numero_ordonnance`, `date_emission`, `medicaments`, `note_pharmacien`, `statut`)
VALUES
(1, 'ORD-2023-001', '2023-10-15',
  '[{"nom":"Amlodipine","dosage":"5mg","frequence":"1 comprimé le matin","duree":"30 jours","instructions":"À prendre à jeun, toujours à la même heure.","categorie":"Antihypertenseur"},{"nom":"Ramipril","dosage":"5mg","frequence":"1 comprimé le soir","duree":"30 jours","instructions":"Surveiller la tension régulièrement.","categorie":"Antihypertenseur"}]',
  'Substitution générique autorisée pour le Ramipril.', 'active'),

(3, 'ORD-2023-002', '2023-02-22',
  '[{"nom":"Paracétamol","dosage":"1000mg","frequence":"1 comprimé toutes les 6h","duree":"5 jours","instructions":"Ne pas dépasser 4g par 24h.","categorie":"Analgésique"},{"nom":"Oseltamivir","dosage":"75mg","frequence":"2 fois par jour","duree":"5 jours","instructions":"À prendre pendant les repas.","categorie":"Antiviral"}]',
  'Génériques autorisés.', 'archivee'),

(6, 'ORD-2023-003', '2023-10-05',
  '[{"nom":"Amoxicilline","dosage":"500mg","frequence":"3 fois par jour","duree":"7 jours","instructions":"À prendre pendant les repas avec un grand verre d\'eau. Terminer impérativement la boîte.","categorie":"Antibiotique"},{"nom":"Paracétamol","dosage":"1000mg","frequence":"Toutes les 6 heures","duree":"Si fièvre uniquement","instructions":"Ne pas dépasser 4g par 24h.","categorie":"Analgésique"},{"nom":"Sirop Hélicidine","dosage":"1 cuillère à soupe","frequence":"Avant le coucher","duree":"5 jours","instructions":"Agiter avant emploi.","categorie":"Antitussif"}]',
  'Substitution générique autorisée sauf pour l\'Amoxicilline (marque spécifique pour tolérance).', 'active'),

(7, 'ORD-2023-004', '2023-10-01',
  '[{"nom":"Rosuvastatine","dosage":"10mg","frequence":"1 comprimé le soir","duree":"30 jours","instructions":"Éviter le jus de pamplemousse.","categorie":"Hypolipémiant"}]',
  'Générique autorisé.', 'active'),

(5, 'ORD-2023-005', '2023-10-08',
  '[{"nom":"Metformine","dosage":"1000mg","frequence":"2 fois par jour","duree":"30 jours","instructions":"À prendre pendant les repas.","categorie":"Antidiabétique"},{"nom":"Gliclazide","dosage":"30mg","frequence":"1 comprimé le matin","duree":"30 jours","instructions":"Surveiller glycémie régulièrement.","categorie":"Antidiabétique"}]',
  'Génériques autorisés pour la Metformine.', 'active'),

(13, 'ORD-2023-006', '2023-10-05',
  '[{"nom":"Amoxicilline","dosage":"500mg","frequence":"3 fois par jour","duree":"10 jours","instructions":"À prendre pendant les repas.","categorie":"Antibiotique"},{"nom":"Paracétamol","dosage":"500mg","frequence":"3 fois par jour","duree":"5 jours","instructions":"En cas de douleur.","categorie":"Analgésique"}]',
  'Pas de substitution pour Amoxicilline.', 'active');

COMMIT;
