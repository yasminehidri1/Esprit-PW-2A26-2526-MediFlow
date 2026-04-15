-- ============================================================
-- MediFlow Magazine Module — Database Schema
-- Tables: posts, comments
-- Database: mediflow
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table: posts (Magazine Articles)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `date_publication` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_posts_auteur` (`auteur_id`),
  KEY `idx_posts_statut` (`statut`),
  KEY `idx_posts_categorie` (`categorie`),
  KEY `idx_posts_date_publication` (`date_publication`),
  CONSTRAINT `fk_posts_auteur` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: comments (Community Discussion)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_post` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `statut` enum('en_attente','approuve','rejete') NOT NULL DEFAULT 'en_attente',
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_comments_post` (`id_post`),
  KEY `fk_comments_utilisateur` (`id_utilisateur`),
  KEY `idx_comments_statut` (`statut`),
  CONSTRAINT `fk_comments_post` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_PK`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed Data: Sample Articles
-- --------------------------------------------------------

INSERT INTO `posts` (`titre`, `contenu`, `categorie`, `image_url`, `auteur_id`, `statut`, `likes_count`, `views_count`, `date_creation`, `date_publication`) VALUES
(
  'The Future of Personalized Telemedicine: Beyond Video Calls',
  'Exploring how integrated biosensors and real-time data streaming are transforming the remote consultation experience from simple conversations into clinical-grade assessments.\n\nThe landscape of telemedicine has evolved dramatically over the past few years. What once started as simple video conferencing between doctors and patients has now transformed into a sophisticated ecosystem of interconnected health technologies.\n\nModern telemedicine platforms are integrating wearable biosensors that can transmit vital signs in real-time during consultations. Heart rate, blood oxygen levels, blood pressure, and even ECG readings can now be captured by consumer-grade devices and streamed directly to the physician''s dashboard.\n\nThis shift represents a fundamental change in how we think about remote healthcare. Rather than relying solely on patient-reported symptoms, doctors can now make data-driven decisions during virtual visits, bringing the consultation closer to the accuracy of in-person examinations.\n\nKey developments include:\n- AI-powered symptom analysis that pre-screens patients before consultations\n- Integration of home diagnostic kits with telemedicine platforms\n- Real-time vital sign monitoring during video consultations\n- Automated follow-up scheduling based on consultation outcomes',
  'General Health',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuAv9eS0xUUSXM5NLqZzjnyfe0lt47s72alqY9qrU2mL0jh79ufYVCR3mC4XLpC69GM0zx7d7yVt78K9PT1jee12_kSlPcY6CbMx8WmpevukwZ6byxANb8-xez3oIurTRI19Ul2npzwdPa75DqD1s06X4D_zU5rPVG1OzbNw7MieaLhJGqAvD1R8ZyKO7UXlV88kQX1zeBTsS1vJdfOj5B8swseYU0nLpZkgWEM_8yMAkFLjCP1YAHgoRXqx1yjTdlod2l777ukd4dE',
  1, 'publie', 1200, 8500,
  '2026-04-01 09:00:00', '2026-04-01 09:00:00'
),
(
  '5 Superfoods for Brain Health',
  'Research indicates that a diet rich in these specific nutrients can significantly reduce cognitive decline and improve mental clarity.\n\nThe connection between diet and brain health has been a growing area of scientific research. Studies consistently show that certain foods contain compounds that directly support neural function, protect against oxidative stress, and promote the growth of new brain cells.\n\n1. **Blueberries** - Rich in anthocyanins, these powerful antioxidants cross the blood-brain barrier and accumulate in areas responsible for learning and memory.\n\n2. **Fatty Fish** - Salmon, trout, and sardines are excellent sources of omega-3 fatty acids, which are essential building blocks of the brain.\n\n3. **Turmeric** - Curcumin, the active ingredient in turmeric, has been shown to cross the blood-brain barrier and has anti-inflammatory and antioxidant benefits.\n\n4. **Broccoli** - High in compounds called glucosinolates, which produce isothiocyanates that may reduce oxidative stress and lower the risk of neurodegenerative diseases.\n\n5. **Pumpkin Seeds** - Contain zinc, magnesium, copper, and iron — all crucial for nerve signaling and brain function.',
  'Diet & Nutrition',
  NULL,
  1, 'publie', 850, 4200,
  '2026-04-03 14:30:00', '2026-04-03 14:30:00'
),
(
  'Understanding Sleep Cycles and Recovery',
  'How REM and deep sleep stages impact your body''s daily recovery mechanisms and what you can do to optimize your rest.\n\nSleep is not a uniform state — it''s a complex, dynamic process consisting of multiple stages that cycle throughout the night. Understanding these cycles is key to improving both the quality and effectiveness of your rest.\n\nThe sleep cycle consists of four stages:\n- **Stage 1 (N1)**: Light sleep lasting 1-7 minutes. Your heartbeat, breathing, and eye movements slow down.\n- **Stage 2 (N2)**: Deeper sleep where body temperature drops and brain waves show specific patterns called sleep spindles.\n- **Stage 3 (N3)**: Deep sleep or slow-wave sleep. This is the most restorative stage where tissue growth and repair occurs.\n- **REM Sleep**: Rapid Eye Movement sleep where most dreaming occurs. Critical for memory consolidation and emotional processing.\n\nEach complete cycle lasts approximately 90 minutes, and a healthy adult goes through 4-6 cycles per night. The proportion of each stage changes throughout the night, with more deep sleep in the first half and more REM sleep in the second half.',
  'Research',
  NULL,
  2, 'publie', 432, 3100,
  '2026-04-05 11:00:00', '2026-04-05 11:00:00'
),
(
  'Advancements in Neural Plasticity Research',
  'New studies reveal groundbreaking findings about the brain''s ability to rewire itself, offering hope for stroke recovery and neurodegenerative disease treatments.\n\nNeuroplasticity — the brain''s remarkable ability to reorganize itself by forming new neural connections — has been one of the most exciting areas of neuroscience research in recent decades.\n\nRecent clinical trials have demonstrated that targeted rehabilitation programs, combined with non-invasive brain stimulation techniques, can significantly enhance neural plasticity in stroke patients. These findings suggest that the window for recovery may be much wider than previously believed.\n\nKey findings include:\n- Transcranial magnetic stimulation (TMS) combined with physical therapy shows 40% improvement in motor recovery\n- Music therapy activates multiple brain regions simultaneously, promoting cross-hemispheric connections\n- Virtual reality rehabilitation programs create immersive environments that challenge the brain to adapt\n- Pharmacological interventions using BDNF (Brain-Derived Neurotrophic Factor) show promise in animal models',
  'Research',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuD8mqmyBVnquEc1jiPd25M8mm0eTLn3Fcr9ungNMisV4ydtpdFZv9XDCrcG1RIq85sGfiIMoWmU4Wt6OB2nHowY3Ze18q1hAq4QvJochy-JQd4Qa0ZIQrPuWBA6NE-ZtHT2WfBLo4nT2TYfUp_KUs3Sm9t15RUlZnvWwOD3_5_aTk_yKwrzs5dZ7iTfxNMpXrv4pZx6iy27txQnT1AaLgXb88bTvstaQK4IBTkmlnXpbibjxQAgE14v7IA4HirOcixJhjd1fvJDEik',
  2, 'publie', 678, 5600,
  '2026-04-10 08:15:00', '2026-04-10 08:15:00'
),
(
  'The Future of Sustainable Healthcare Infrastructure',
  'How modern hospitals are embracing green architecture, renewable energy, and sustainable materials to reduce their environmental footprint while improving patient outcomes.\n\nHealthcare facilities are among the most energy-intensive buildings in any community. They operate 24/7, require sophisticated HVAC systems, and consume vast amounts of water and electricity. But a growing movement in healthcare architecture is proving that sustainability and excellent patient care can go hand in hand.\n\nLeading medical centers around the world are now:\n- Installing solar panels and wind turbines to offset energy consumption\n- Using biophilic design principles that incorporate natural elements into healing environments\n- Implementing smart building management systems that optimize energy usage in real-time\n- Adopting circular economy principles for medical waste management\n- Creating green spaces and therapeutic gardens that improve patient recovery times\n\nStudies show that patients in sustainably designed hospitals recover up to 15% faster and report higher satisfaction scores. The natural lighting, improved air quality, and connection to nature all contribute to better health outcomes.',
  'General Health',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuAa-C_4qS9_amZ7jsRQBah-ZecNT3P1XQ1Db9BHW3GyBrFuv_utNRbc8o2i5pCPRUVQt2Obj9IUea0jQqgL-QiecrgGMcjursz09IuCiJiVukL_1vJJu2xkMf88hYoCQR8DzPHx1ZREeWrxtRsqsLgudkx81blEpJGmSf9jJksRhVbXz62s4VtQboPW5FQzPExP1Y8NNPtMjNa1iQeyXBSW9qTjTJceU33934QUhauvdYkjQ01j1syvNpljoxS7iB0GM_G7ozlxh6Y',
  1, 'publie', 543, 3800,
  '2026-04-12 16:45:00', '2026-04-12 16:45:00'
),
(
  'Cardiovascular Robotics: A New Frontier',
  'Robotic-assisted cardiovascular procedures are revolutionizing how surgeons approach complex heart operations, offering unprecedented precision and faster patient recovery.\n\nThe integration of robotics into cardiovascular surgery represents one of the most significant technological advances in modern medicine. These sophisticated systems allow surgeons to perform intricate procedures through small incisions with enhanced visualization and greater dexterity than the human hand alone can provide.\n\nCurrent applications include:\n- Robotic-assisted coronary artery bypass grafting (CABG)\n- Minimally invasive mitral valve repair\n- Catheter-based interventions with robotic navigation\n- Hybrid procedures combining traditional and robotic techniques\n\nThe da Vinci surgical system and newer platforms offer 3D high-definition visualization, motion scaling (converting large hand movements into precise micro-movements), and tremor filtration. These capabilities are particularly valuable in cardiac surgery where precision is literally a matter of life and death.',
  'Journals',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuD57cqXHwUhJMPhDsKsskiLu1McvwFyfCiTSSOyN0VvZhd8g1V49Zx6P82c9ebCNLYDNR-lX69fTyeQlOOV8u1hshp60EK2JTJhMzsY6wMoW9aP5sccIAo_ldwDPWyR9cEvuptGM4YNJ_LDZIlklkD97eCGSsPsGp61N_7VAdIKs2wKFUnt05HkjSH_gZ9fennCAvDtT6pd0aCCTZ4cutNkCWMD8wzNm4ftOMMOTKEkiB5g10m7Jw94-LYRTxEMCtlojHpO9m6KxyE',
  3, 'publie', 321, 2900,
  '2026-04-13 10:00:00', '2026-04-13 10:00:00'
),
(
  'Epidemiology Trends: 2026 Seasonal Preview',
  'A data-driven look at respiratory health and preventive measures for the coming months based on global surveillance data.\n\nAs we enter the second quarter of 2026, epidemiological data from global health surveillance networks provides crucial insights into what we can expect in terms of seasonal health challenges.\n\nThis preview is based on data collected from the WHO Global Influenza Surveillance and Response System, CDC FluView, and European Centre for Disease Prevention and Control.\n\nKey trends for the upcoming season include shifts in influenza strain dominance, emerging respiratory syncytial virus (RSV) patterns, and the continued evolution of COVID-19 variants.\n\nPreventive recommendations:\n- Updated vaccination schedules for high-risk populations\n- Enhanced indoor air quality measures in clinical settings\n- Community-based health literacy programs\n- Early warning systems integration with primary care networks',
  'General Health',
  NULL,
  1, 'publie', 290, 2100,
  '2026-04-14 13:20:00', '2026-04-14 13:20:00'
),
(
  'Mental Health in the Digital Age: Navigating Screen Time',
  'Understanding the complex relationship between technology use and psychological well-being, with evidence-based strategies for healthier digital habits.\n\nThe ubiquity of smartphones, social media, and digital entertainment has created an unprecedented challenge for mental health. While technology offers incredible benefits for connectivity and information access, excessive or mindless use can contribute to anxiety, depression, and sleep disorders.\n\nThis article is currently under review by our editorial board and will be published upon completion of peer review.',
  'Mental Wellness',
  NULL,
  3, 'brouillon', 0, 0,
  '2026-04-15 09:00:00', NULL
);

-- --------------------------------------------------------
-- Seed Data: Sample Comments
-- --------------------------------------------------------

INSERT INTO `comments` (`id_post`, `id_utilisateur`, `contenu`, `statut`, `date_creation`) VALUES
-- Comments on "The Future of Personalized Telemedicine"
(1, 4, 'I tried the remote monitoring mentioned in this article and saw a 15% increase in my HRV. The future of telemedicine is truly exciting!', 'approuve', '2026-04-02 10:30:00'),
(1, 5, 'Is there a specific wearable device you would recommend for this type of health tracking?', 'approuve', '2026-04-02 14:15:00'),
(1, 6, 'Great article! Very informative about the latest developments in remote healthcare.', 'approuve', '2026-04-03 09:00:00'),

-- Comments on "5 Superfoods for Brain Health"
(2, 4, 'I have been incorporating blueberries into my daily diet for 3 months and noticed improved focus. Science backs this up!', 'approuve', '2026-04-04 11:20:00'),
(2, 6, 'What about dark chocolate? I have read that it also has significant brain health benefits.', 'approuve', '2026-04-04 16:45:00'),

-- Comments on "Advancements in Neural Plasticity"
(4, 5, 'This data seems contradictory to the 2022 study. Can we get clarification on the sample size used?', 'en_attente', '2026-04-11 08:30:00'),
(4, 4, 'Great article! Very helpful for my thesis on neuro-rehabilitation.', 'en_attente', '2026-04-11 12:00:00'),

-- Flagged comment (spam)
(1, 6, 'Check out this link for cheap medical supplies at discount prices!!!', 'en_attente', '2026-04-14 15:00:00'),

-- Comments on other articles
(3, 4, 'The explanation of sleep cycles here is the clearest I have ever read. Thank you!', 'approuve', '2026-04-06 20:00:00'),
(5, 5, 'Our hospital recently started a green initiative. This article validates our approach perfectly.', 'approuve', '2026-04-13 11:30:00');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
