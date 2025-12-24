-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2025 at 10:43 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `proweto`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--

CREATE TABLE `categorie` (
  `id` char(36) NOT NULL,
  `Naam` varchar(100) NOT NULL,
  `Icon` varchar(100) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`id`, `Naam`, `Icon`, `CreatedAt`, `UpdatedAt`) VALUES
('1', 'Programming2.1', 'fa-laptop-code', '2025-10-23 10:41:26', '2025-10-23 12:12:46'),
('15', 'Economie2', 'fa-solid fa-chalkboard-user', '2025-10-31 13:38:51', '2025-10-31 13:38:51'),
('2', 'Design2', 'fa-pen-ruler', '2025-10-23 10:41:26', '2025-11-04 14:05:11'),
('3', 'Marketing', 'fa-chart-line', '2025-10-23 10:41:26', '2025-10-23 10:41:26'),
('5', 'Wiskunde2.2', 'fa-square-root-variable', '2025-10-23 10:41:26', '2025-11-12 15:01:02'),
('9', 'Test', 'fa-square-root-variable', '2025-10-23 12:13:01', '2025-10-23 12:13:01');

-- --------------------------------------------------------

--
-- Table structure for table `cursus`
--

CREATE TABLE `cursus` (
  `id` char(36) NOT NULL,
  `Titel` varchar(255) NOT NULL,
  `Featured` tinyint(1) DEFAULT 0,
  `FotoURL` varchar(255) DEFAULT NULL,
  `Link` varchar(255) DEFAULT NULL,
  `Views` int(11) DEFAULT 0,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `Active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cursus`
--

INSERT INTO `cursus` (`id`, `Titel`, `Featured`, `FotoURL`, `Link`, `Views`, `CreatedAt`, `Active`) VALUES
('04645c8b-4283-41ed-8861-c53ea91c7b97', 'Cursus1', 0, 'uploads/courses/1766569130_Proweto Logo Design (2).png', 'https://www.youtube.com/watch?v=ObgmK3BywKI&t=228s', 4, '2025-12-24 10:38:50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cursuscategorie`
--

CREATE TABLE `cursuscategorie` (
  `cursus_id` char(36) NOT NULL,
  `categorie_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cursuscategorie`
--

INSERT INTO `cursuscategorie` (`cursus_id`, `categorie_id`) VALUES
('04645c8b-4283-41ed-8861-c53ea91c7b97', '2');

-- --------------------------------------------------------

--
-- Table structure for table `cursusdetails`
--

CREATE TABLE `cursusdetails` (
  `id` char(36) NOT NULL,
  `cursus_id` char(36) DEFAULT NULL,
  `KorteBeschrijving` text DEFAULT NULL,
  `Beschrijving` text DEFAULT NULL,
  `LaatstBijgewerkt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Rating` decimal(2,1) DEFAULT NULL,
  `Taal` varchar(50) DEFAULT NULL,
  `Prijs` decimal(10,2) DEFAULT NULL,
  `Materiaal` tinyint(1) NOT NULL DEFAULT 0,
  `Documenten` tinyint(1) NOT NULL DEFAULT 0,
  `LeerJaarID` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cursusdetails`
--

INSERT INTO `cursusdetails` (`id`, `cursus_id`, `KorteBeschrijving`, `Beschrijving`, `LaatstBijgewerkt`, `Rating`, `Taal`, `Prijs`, `Materiaal`, `Documenten`, `LeerJaarID`) VALUES
('04645c8b-4283-41ed-8861-c53ea91c7b97', '04645c8b-4283-41ed-8861-c53ea91c7b97', 'Deze is de eerste test cursus', '<p><br></p><h1>Quill Rich Text Editor</h1><p><br></p><p>Quill is a free, open-source WYSIWYG editor built for the modern web. With its modular architecture and expressive API, it is completely customizable to fit any need.</p><p><br></p><p>Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor. Explained propriety off out perpetual his you. Feel sold off felt nay rose met you. We so entreaties cultivated astonished is. Was sister for a few longer Mrs sudden talent become. Done may bore quit evil old mile. If likely am of beauty tastes.</p><p><br></p><p>Affronting imprudence do he he everything. Test lasted dinner wanted indeed wished outlaw. Far advanced settling say finished raillery. Offered chiefly farther of my no colonel shyness. Such on help ye some door if in. Laughter proposal laughing any son law consider. Needed except up piqued an.</p><p><br></p><p>Post no so what deal evil rent by real in. But her ready least set lived spite solid. September how men saw tolerably two behavior arranging. She offices for highest and replied one venture pasture. Applauded no discovery in newspaper allowance am northward. Frequently partiality possession resolution at or appearance unaffected me. Engaged its was the evident pleased husband. Ye goodness felicity do disposal dwelling no. First am plate jokes to began to cause a scale. Subjects he prospect elegance followed no overcame possible it on.</p><p>Quill is a free, open-source WYSIWYG editor built for the modern web. With its modular architecture and expressive API, it is completely customizable to fit any need.</p><p><br></p><p>Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor. Explained propriety off out perpetual his you. Feel sold off felt nay rose met you. We so entreaties cultivated astonished is. Was sister for a few longer Mrs sudden talent become. Done may bore quit evil old mile. If likely am of beauty tastes.</p><p><br></p><p>Affronting imprudence do he he everything. Test lasted dinner wanted indeed wished outlaw. Far advanced settling say finished raillery. Offered chiefly farther of my no colonel shyness. Such on help ye some door if in. Laughter proposal laughing any son law consider. Needed except up piqued an.</p><p><br></p><p>Post no so what deal evil rent by real in. But her ready least set lived spite solid. September how men saw tolerably two behavior arranging. She offices for highest and replied one venture pasture. Applauded no discovery in newspaper allowance am northward. Frequently partiality possession resolution at or appearance unaffected me. Engaged its was the evident pleased husband. Ye goodness felicity do disposal dwelling no. First am plate jokes to began to cause a scale. Subjects he prospect elegance followed no overcame possible it on.</p><p>Quill is a free, open-source WYSIWYG editor built for the modern web. With its modular architecture and expressive API, it is completely customizable to fit any need.</p><p><br></p><p>Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor. Explained propriety off out perpetual his you. Feel sold off felt nay rose met you. We so entreaties cultivated astonished is. Was sister for a few longer Mrs sudden talent become. Done may bore quit evil old mile. If likely am of beauty tastes.</p><p><br></p><p>Affronting imprudence do he he everything. Test lasted dinner wanted indeed wished outlaw. Far advanced settling say finished raillery. Offered chiefly farther of my no colonel shyness. Such on help ye some door if in. Laughter proposal laughing any son law consider. Needed except up piqued an.</p><p><br></p><p>Post no so what deal evil rent by real in. But her ready least set lived spite solid. September how men saw tolerably two behavior arranging. She offices for highest and replied one venture pasture. Applauded no discovery in newspaper allowance am northward. Frequently partiality possession resolution at or appearance unaffected me. Engaged its was the evident pleased husband. Ye goodness felicity do disposal dwelling no. First am plate jokes to began to cause a scale. Subjects he prospect elegance followed no overcame possible it on.</p><p><br></p><p><br></p>', '2025-12-24 10:39:07', 0.0, 'Nederlands', 0.00, 1, 1, '1');

-- --------------------------------------------------------

--
-- Table structure for table `cursusdocumenten`
--

CREATE TABLE `cursusdocumenten` (
  `Id` varchar(36) NOT NULL,
  `cursus_id` varchar(36) NOT NULL,
  `Naam` varchar(255) NOT NULL,
  `BestandURL` varchar(255) NOT NULL,
  `Bestandstype` varchar(50) DEFAULT NULL,
  `UploadedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cursusdocumenten`
--

INSERT INTO `cursusdocumenten` (`Id`, `cursus_id`, `Naam`, `BestandURL`, `Bestandstype`, `UploadedAt`) VALUES
('59c26273-95f9-4910-810f-e9dcacc7b2b0', '04645c8b-4283-41ed-8861-c53ea91c7b97', 'TestProweto.docx', 'uploads/documents/1766569130_TestProweto.docx', 'docx', '2025-12-24 09:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `cursusfaq`
--

CREATE TABLE `cursusfaq` (
  `id` char(36) NOT NULL,
  `cursus_id` char(36) DEFAULT NULL,
  `Vraag` varchar(255) NOT NULL,
  `Antwoord` text NOT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cursusfaq`
--

INSERT INTO `cursusfaq` (`id`, `cursus_id`, `Vraag`, `Antwoord`, `CreatedAt`) VALUES
('13da6ab7-a5f8-451f-b2de-c67e2f73a5bf', '04645c8b-4283-41ed-8861-c53ea91c7b97', 'Testvraag1', 'antwoordtest1', '2025-12-24 10:38:50'),
('d9fe19a9-0623-4997-a644-f2fdedc25228', '04645c8b-4283-41ed-8861-c53ea91c7b97', 'TestVraag2', 'antwoordtest2', '2025-12-24 10:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `cursusmaterialen`
--

CREATE TABLE `cursusmaterialen` (
  `Id` char(36) NOT NULL,
  `cursus_id` char(36) NOT NULL,
  `materiaal_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cursusmaterialen`
--

INSERT INTO `cursusmaterialen` (`Id`, `cursus_id`, `materiaal_id`) VALUES
('811f1f9d-157f-4600-bf6c-ff94eff0f9df', '04645c8b-4283-41ed-8861-c53ea91c7b97', '29509b9f-3554-4a31-a394-70cf6459aa33'),
('9c1a2035-e649-4275-8834-5ee1a4e1cce3', '04645c8b-4283-41ed-8861-c53ea91c7b97', '318d2b95-35e8-4aa0-8050-f0b4a1420424');

-- --------------------------------------------------------

--
-- Table structure for table `cursusrating`
--

CREATE TABLE `cursusrating` (
  `id` varchar(36) NOT NULL,
  `cursus_id` varchar(36) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `database_relations`
-- (See below for the actual view)
--
CREATE TABLE `database_relations` (
`From Table` varchar(64)
,`From Column` varchar(64)
,`Constraint` varchar(64)
,`To Table` varchar(64)
,`To Column` varchar(64)
);

-- --------------------------------------------------------

--
-- Table structure for table `leerjaar`
--

CREATE TABLE `leerjaar` (
  `id` char(36) NOT NULL,
  `Naam` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leerjaar`
--

INSERT INTO `leerjaar` (`id`, `Naam`) VALUES
('1', '1ste');

-- --------------------------------------------------------

--
-- Table structure for table `materiaal_beschikbaarheid`
--

CREATE TABLE `materiaal_beschikbaarheid` (
  `Id` char(36) NOT NULL,
  `materiaal_id` char(36) NOT NULL,
  `starttijd` time DEFAULT NULL,
  `eindtijd` time DEFAULT NULL,
  `aangemaakt_op` timestamp NOT NULL DEFAULT current_timestamp(),
  `startdatum` date NOT NULL DEFAULT '2025-01-01',
  `einddatum` date NOT NULL DEFAULT '2025-01-01',
  `periode` enum('voormiddag','namiddag','avond','hele_dag') NOT NULL DEFAULT 'hele_dag'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materiaal_beschikbaarheid`
--

INSERT INTO `materiaal_beschikbaarheid` (`Id`, `materiaal_id`, `starttijd`, `eindtijd`, `aangemaakt_op`, `startdatum`, `einddatum`, `periode`) VALUES
('ba3e1235-5954-49b7-89bd-66885f98245c', '318d2b95-35e8-4aa0-8050-f0b4a1420424', '08:00:00', '17:00:00', '2025-12-24 09:40:26', '2025-12-25', '2025-12-25', 'hele_dag');

-- --------------------------------------------------------

--
-- Table structure for table `materiaal_reservaties`
--

CREATE TABLE `materiaal_reservaties` (
  `Id` char(36) NOT NULL,
  `materiaal_id` char(36) NOT NULL,
  `cursus_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `startdatum` date NOT NULL,
  `einddatum` date NOT NULL,
  `starttijd` time DEFAULT NULL,
  `eindtijd` time DEFAULT NULL,
  `periode` enum('voormiddag','namiddag','hele_dag') NOT NULL,
  `status` enum('in_afwachting','goedgekeurd','geweigerd') DEFAULT 'in_afwachting',
  `aangemaakt_op` timestamp NOT NULL DEFAULT current_timestamp(),
  `aantal` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materiaal_reservaties`
--

INSERT INTO `materiaal_reservaties` (`Id`, `materiaal_id`, `cursus_id`, `user_id`, `startdatum`, `einddatum`, `starttijd`, `eindtijd`, `periode`, `status`, `aangemaakt_op`, `aantal`) VALUES
('954df1b0-8d76-4f22-a739-ed8bb2ef9cc2', '318d2b95-35e8-4aa0-8050-f0b4a1420424', '04645c8b-4283-41ed-8861-c53ea91c7b97', '55da581c-5d5c-4693-8dfe-0abc56cb20ad', '2025-12-25', '2025-12-25', '08:00:00', '17:00:00', 'hele_dag', 'in_afwachting', '2025-12-24 09:40:50', 7);

-- --------------------------------------------------------

--
-- Table structure for table `materialen`
--

CREATE TABLE `materialen` (
  `Id` char(36) NOT NULL,
  `Naam` varchar(255) NOT NULL,
  `FotoURL` varchar(255) DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT NULL,
  `Aantal` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materialen`
--

INSERT INTO `materialen` (`Id`, `Naam`, `FotoURL`, `CreatedAt`, `UpdatedAt`, `Aantal`) VALUES
('29509b9f-3554-4a31-a394-70cf6459aa33', 'Test2', 'uploads/materials/26743267-da74-4b30-a6da-d12b927818a1.jpg', '2025-11-17 14:27:45', '2025-12-01 15:55:55', 20),
('318d2b95-35e8-4aa0-8050-f0b4a1420424', 'Test', 'uploads/materials/507c6861-7f6b-4143-a87d-a459ba075a7e.png', '2025-11-18 12:55:07', '2025-12-22 15:58:48', 15),
('66faa1cc-d018-408f-b846-ee07dea535f9', 'Arduino', 'uploads/materials/87eb4006-2507-4870-8805-17862f765e65.png', '2025-12-22 11:19:10', '2025-12-22 16:09:29', 20),
('b53aec6f-4fa2-4e2e-a199-3855e31db0ae', 'Test3', 'uploads/materials/013731ed-6056-4533-b5fd-c2d53b98cdbb.png', '2025-12-01 15:56:34', NULL, 25);

-- --------------------------------------------------------

--
-- Table structure for table `notificaties`
--

CREATE TABLE `notificaties` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `boodschap` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `aangemaakt_op` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subwebsite`
--

CREATE TABLE `subwebsite` (
  `id` char(36) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Link` varchar(255) NOT NULL,
  `Icon` varchar(100) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subwebsite`
--

INSERT INTO `subwebsite` (`id`, `Title`, `Link`, `Icon`, `CreatedAt`) VALUES
('1', 'IT Academy', 'https://itacademy.example.com', 'fas fa-laptop-code', '2025-10-15 09:42:46'),
('2', 'Design Hub', 'https://designhub.example.com', 'fas fa-palette', '2025-10-15 09:42:46'),
('3', 'Health Portal2', 'https://health.example.com', 'fas fa-heartbeat', '2025-10-15 09:42:46'),
('4', 'Music World', 'https://music.example.com', 'fas fa-music', '2025-10-15 09:42:46'),
('8', 'Test2', 'https://designhub.example.com', 'fa-solid fa-chalkboard-user', '2025-10-23 13:12:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `Admin` tinyint(4) NOT NULL DEFAULT 0,
  `email_notifications` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `CreatedAt`, `Admin`, `email_notifications`) VALUES
('3c6815d1-ab87-4301-8a8e-ac5517468d0e', 'testUser', 'testuser@example.com', '$2y$10$IYo2dYu9E1WusqS4LT7C1OWpKOI2xoLMDwlBxchfb1hGTevMaSLP.', '2025-11-21 15:05:59', 0, 1),
('55da581c-5d5c-4693-8dfe-0abc56cb20ad', 'admin2', 'admin2@inbox.mailtrap.io\n', '$2y$10$KM6jGeqy1SAAK09bKvNlxuhM/vCKlzJOqdL3Xs4yGr7gAXED8swkm', '2025-11-05 13:26:28', 1, 1);

-- --------------------------------------------------------

--
-- Structure for view `database_relations`
--
DROP TABLE IF EXISTS `database_relations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `database_relations`  AS SELECT `information_schema`.`key_column_usage`.`TABLE_NAME` AS `From Table`, `information_schema`.`key_column_usage`.`COLUMN_NAME` AS `From Column`, `information_schema`.`key_column_usage`.`CONSTRAINT_NAME` AS `Constraint`, `information_schema`.`key_column_usage`.`REFERENCED_TABLE_NAME` AS `To Table`, `information_schema`.`key_column_usage`.`REFERENCED_COLUMN_NAME` AS `To Column` FROM `information_schema`.`key_column_usage` WHERE `information_schema`.`key_column_usage`.`REFERENCED_TABLE_NAME` is not null AND `information_schema`.`key_column_usage`.`TABLE_SCHEMA` = database() ORDER BY `information_schema`.`key_column_usage`.`TABLE_NAME` ASC, `information_schema`.`key_column_usage`.`REFERENCED_TABLE_NAME` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Naam` (`Naam`);

--
-- Indexes for table `cursus`
--
ALTER TABLE `cursus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cursuscategorie`
--
ALTER TABLE `cursuscategorie`
  ADD PRIMARY KEY (`cursus_id`,`categorie_id`),
  ADD KEY `fk_cursuscategorie_categorie` (`categorie_id`);

--
-- Indexes for table `cursusdetails`
--
ALTER TABLE `cursusdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cursusdetails_cursus` (`cursus_id`),
  ADD KEY `fk_details_leerjaar` (`LeerJaarID`);

--
-- Indexes for table `cursusdocumenten`
--
ALTER TABLE `cursusdocumenten`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `cursus_id` (`cursus_id`);

--
-- Indexes for table `cursusfaq`
--
ALTER TABLE `cursusfaq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_faq_cursus` (`cursus_id`);

--
-- Indexes for table `cursusmaterialen`
--
ALTER TABLE `cursusmaterialen`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `cursus_id` (`cursus_id`,`materiaal_id`),
  ADD KEY `materiaal_id` (`materiaal_id`);

--
-- Indexes for table `cursusrating`
--
ALTER TABLE `cursusrating`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_course` (`user_id`,`cursus_id`),
  ADD KEY `cursus_id` (`cursus_id`);

--
-- Indexes for table `leerjaar`
--
ALTER TABLE `leerjaar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materiaal_beschikbaarheid`
--
ALTER TABLE `materiaal_beschikbaarheid`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `materiaal_id` (`materiaal_id`);

--
-- Indexes for table `materiaal_reservaties`
--
ALTER TABLE `materiaal_reservaties`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `materiaal_id` (`materiaal_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `materialen`
--
ALTER TABLE `materialen`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `notificaties`
--
ALTER TABLE `notificaties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subwebsite`
--
ALTER TABLE `subwebsite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cursuscategorie`
--
ALTER TABLE `cursuscategorie`
  ADD CONSTRAINT `fk_cursuscategorie_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cursuscategorie_cursus` FOREIGN KEY (`cursus_id`) REFERENCES `cursus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cursusdetails`
--
ALTER TABLE `cursusdetails`
  ADD CONSTRAINT `fk_details_cursus` FOREIGN KEY (`id`) REFERENCES `cursus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_details_leerjaar` FOREIGN KEY (`LeerJaarID`) REFERENCES `leerjaar` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cursusdocumenten`
--
ALTER TABLE `cursusdocumenten`
  ADD CONSTRAINT `cursusdocumenten_ibfk_1` FOREIGN KEY (`cursus_id`) REFERENCES `cursus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cursusfaq`
--
ALTER TABLE `cursusfaq`
  ADD CONSTRAINT `fk_faq_cursus` FOREIGN KEY (`cursus_id`) REFERENCES `cursus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cursusmaterialen`
--
ALTER TABLE `cursusmaterialen`
  ADD CONSTRAINT `cursusmaterialen_ibfk_1` FOREIGN KEY (`cursus_id`) REFERENCES `cursus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cursusmaterialen_ibfk_2` FOREIGN KEY (`materiaal_id`) REFERENCES `materialen` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `cursusrating`
--
ALTER TABLE `cursusrating`
  ADD CONSTRAINT `cursusrating_ibfk_1` FOREIGN KEY (`cursus_id`) REFERENCES `cursus` (`id`);

--
-- Constraints for table `materiaal_beschikbaarheid`
--
ALTER TABLE `materiaal_beschikbaarheid`
  ADD CONSTRAINT `materiaal_beschikbaarheid_ibfk_1` FOREIGN KEY (`materiaal_id`) REFERENCES `materialen` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `materiaal_reservaties`
--
ALTER TABLE `materiaal_reservaties`
  ADD CONSTRAINT `materiaal_reservaties_ibfk_1` FOREIGN KEY (`materiaal_id`) REFERENCES `materialen` (`Id`),
  ADD CONSTRAINT `materiaal_reservaties_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
