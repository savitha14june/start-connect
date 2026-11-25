-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Nov 2026 um 14:11
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `startupconnect_db`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bills_receipts`
--

CREATE TABLE `bills_receipts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `receipt_number` varchar(100) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `commission_rate_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_company_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `bills_receipts`
--

INSERT INTO `bills_receipts` (`id`, `user_id`, `company_id`, `receipt_number`, `amount`, `commission_rate_pct`, `commission_amount`, `net_company_amount`, `payment_method`, `payment_status`, `payment_date`, `created_at`) VALUES
(1, 1, 1, 'RE-2026-0001', 15000.00, 5.00, 750.00, 14250.00, 'Banküberweisung', 'bezahlt', '2026-11-15', '2026-11-24 19:52:05'),
(2, 1, 2, 'RE-2026-0002', 25000.00, 5.00, 1250.00, 23750.00, 'SEPA-Lastschrift', 'offen', '2026-11-24', '2026-11-24 19:52:05');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `companies`
--

CREATE TABLE `companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `owner_user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `tagline` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `company_valuation` decimal(15,2) DEFAULT NULL,
  `funding_goal` decimal(15,2) DEFAULT NULL,
  `raised_amount` decimal(15,2) DEFAULT 0.00,
  `minimum_investment` decimal(15,2) DEFAULT NULL,
  `profit_return_pct` decimal(5,2) DEFAULT NULL,
  `iban` varchar(50) DEFAULT NULL,
  `account_holder` varchar(150) DEFAULT NULL,
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `companies`
--

INSERT INTO `companies` (`id`, `owner_user_id`, `name`, `tagline`, `description`, `category`, `address`, `telephone`, `tax_number`, `company_valuation`, `funding_goal`, `raised_amount`, `minimum_investment`, `profit_return_pct`, `iban`, `account_holder`, `status`, `created_at`) VALUES
(1, 2, 'EcoRoute AI GmbH', 'KI-Optimierung für CO2-arme Lieferketten', 'Unsere Plattform analysiert Lieferdaten in Echtzeit und reduziert Leerfahrten sowie Kraftstoffverbrauch im regionalen Mittelstand.', 'GreenTech & Logistik', 'Innovationsweg 5, 30159 Hannover', '+49 511 222002', 'DE345678901', 1800000.00, 350000.00, 120000.00, 15000.00, 12.50, NULL, NULL, 'approved', '2026-11-24 19:52:05'),
(2, 3, 'MedDoc Flow', 'Strukturierte Digitale Pflegeberichte in Minuten', 'Eine Softwarelösung für Pflege teams und Kliniken, die Sprachnotizen rechtssicher und zeitsparend in strukturierte Berichte überführt.', 'HealthTech', 'Technologiepark 8, 38106 Braunschweig', '+49 531 333003', 'DE987654321', 2200000.00, 500000.00, 175000.00, 25000.00, 14.00, NULL, NULL, 'approved', '2026-11-24 19:52:05'),
(3, 2, 'FinKlar Analytics', 'Liquiditätsplanung speziell für junge Startups', 'Einfaches Cashflow- und Runway-Dashboard ohne Excel-Chaos für Gründerteams in der Seed-Phase.', 'FinTech', 'Innovationsweg 5, 30159 Hannover', '+49 511 222002', 'DE345678902', 950000.00, 200000.00, 40000.00, 10000.00, 10.00, NULL, NULL, 'pending', '2026-11-24 19:52:05'),
(4, 6, 'KargoFlow Logistics GmbH', 'KI-gestützte Tourenplanung für emissionsfreie Stadtlieferungen', '', 'Logistik & KI', NULL, NULL, NULL, NULL, 400000.00, 145000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 12:58:18'),
(5, 7, 'SolarGrid Nord GmbH', 'Modulare Batteriespeicher für Gewerbebetriebe und Kommunen', '', 'GreenTech', NULL, NULL, NULL, NULL, 600000.00, 210000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 12:58:18'),
(6, 6, 'PackCycle Solutions UG', 'Mehrweg-Verpackungssysteme mit Rückverfolgung für den E-Commerce', '', 'Sustainability', NULL, NULL, NULL, NULL, 180000.00, 65000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 12:58:18'),
(7, 6, 'KargoFlow Logistics GmbH', 'KI-gestützte Tourenplanung für emissionsfreie Stadtlieferungen', '', 'Logistik & KI', NULL, NULL, NULL, NULL, 400000.00, 145000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 13:03:13'),
(8, 7, 'SolarGrid Nord GmbH', 'Modulare Batteriespeicher für Gewerbebetriebe und Kommunen', '', 'GreenTech', NULL, NULL, NULL, NULL, 600000.00, 210000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 13:03:13'),
(9, 6, 'PackCycle Solutions UG', 'Mehrweg-Verpackungssysteme mit Rückverfolgung für den E-Commerce', '', 'Sustainability', NULL, NULL, NULL, NULL, 180000.00, 65000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 13:03:13'),
(10, 2, 'KargoFlow Logistics GmbH', 'KI-gestützte Tourenplanung für emissionsfreie Stadtlieferungen', '', 'Logistik & KI', NULL, NULL, NULL, NULL, 400000.00, 145000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 13:06:37'),
(11, 7, 'SolarGrid Nord GmbH', 'Modulare Batteriespeicher für Gewerbebetriebe und Kommunen', '', 'GreenTech', NULL, NULL, NULL, NULL, 600000.00, 210000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 13:06:37'),
(12, 2, 'PackCycle Solutions UG', 'Mehrweg-Verpackungssysteme mit Rückverfolgung für den E-Commerce', '', 'Sustainability', NULL, NULL, NULL, NULL, 180000.00, 65000.00, NULL, NULL, NULL, NULL, '', '2026-11-25 13:06:37');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `company_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','resolved') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_id`, `company_id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 1, 1, 'Lukas Weber', 'investor@test.de', 'Interesse an Seed-Runde & Pitch Deck', 'Hallo Miriam, euer Ansatz bei EcoRoute AI passt hervorragend zu meinem Fokus. Gerne würde ich mich kurz austauschen.', 'open', '2026-11-24 19:52:05'),
(2, NULL, 2, 'Clara Neumann', 'clara.neumann@beispiel.de', 'Frage zur Integration in Kliniksysteme', 'Unterstützt MedDoc Flow bereits gängige Schnittstellen im stationären Bereich?', 'resolved', '2026-11-24 19:52:05'),
(3, NULL, 4, 'Tobias Richter', 'tobias.richter.neu@nordkapital.de', 'Anfrage zum aktuellen Pitch-Deck (Seed-Runde)', 'Guten Tag Frau Hoffmann, Ihr Ansatz zur Tourenoptimierung passt gut zu unserem Portfolio. Hätten Sie nächste Woche Zeit für ein kurzes Kennenlernen?', '', '2026-11-25 12:58:19'),
(4, NULL, 5, 'Tobias Richter', 'tobias.richter.neu@nordkapital.de', 'Fragen zu Kennzahlen und Pilotprojekten', 'Hallo Herr Weber, wie viele gewerbliche Pilotkunden nutzen Ihre Speicherlösung aktuell im laufenden Betrieb?', '', '2026-11-25 12:58:19'),
(5, NULL, 6, 'Katrin Lindner', 'katrin.lindner@rhein-venture.de', 'Kooperationsanfrage für Handelsnetzwerk', 'Guten Tag, wir prüfen aktuell nachhaltige Verpackungslösungen für mittelständische Onlinehändler und würden gerne Details zu Ihren Stückkosten erfahren.', '', '2026-11-25 12:58:19'),
(6, NULL, 10, 'Tobias Richter', 'tobias.richter@nordkapital.de', 'Anfrage zum aktuellen Pitch-Deck (Seed-Runde)', 'Guten Tag, Ihr Ansatz zur Tourenoptimierung passt gut zu unserem Portfolio. Hätten Sie nächste Woche Zeit für ein kurzes Kennenlernen?', '', '2026-11-25 13:06:37'),
(7, NULL, 11, 'Tobias Richter', 'tobias.richter@nordkapital.de', 'Fragen zu Kennzahlen und Pilotprojekten', 'Hallo, wie viele gewerbliche Pilotkunden nutzen Ihre Speicherlösung aktuell im laufenden Betrieb?', '', '2026-11-25 13:06:37'),
(8, NULL, 12, 'Katrin Lindner', 'katrin.lindner@rhein-venture.de', 'Kooperationsanfrage für Handelsnetzwerk', 'Guten Tag, wir prüfen aktuell nachhaltige Verpackungslösungen für mittelständische Onlinehändler und würden gerne Details zu Ihren Stückkosten erfahren.', '', '2026-11-25 13:06:37'),
(9, NULL, 10, 'Tobias Richter', 'tobias.richter@nordkapital.de', 'Anfrage zum aktuellen Pitch-Deck (Seed-Runde)', 'Guten Tag, Ihr Ansatz zur Tourenoptimierung passt gut zu unserem Portfolio. Hätten Sie nächste Woche Zeit für ein kurzes Kennenlernen?', '', '2026-11-25 13:09:17'),
(10, NULL, 11, 'Tobias Richter', 'tobias.richter@nordkapital.de', 'Fragen zu Kennzahlen und Pilotprojekten', 'Hallo, wie viele gewerbliche Pilotkunden nutzen Ihre Speicherlösung aktuell im laufenden Betrieb?', '', '2026-11-25 13:09:17'),
(11, NULL, 12, 'Katrin Lindner', 'katrin.lindner@rhein-venture.de', 'Kooperationsanfrage für Handelsnetzwerk', 'Guten Tag, wir prüfen aktuell nachhaltige Verpackungslösungen für mittelständische Onlinehändler und würden gerne Details zu Ihren Stückkosten erfahren.', '', '2026-11-25 13:09:17'),
(12, NULL, 10, 'Tobias Richter', 'tobias.richter@nordkapital.de', 'Anfrage zum aktuellen Pitch-Deck (Seed-Runde)', 'Guten Tag, Ihr Ansatz zur Tourenoptimierung passt gut zu unserem Portfolio. Hätten Sie nächste Woche Zeit für ein kurzes Kennenlernen?', '', '2026-11-25 13:10:10'),
(13, NULL, 11, 'Tobias Richter', 'tobias.richter@nordkapital.de', 'Fragen zu Kennzahlen und Pilotprojekten', 'Hallo, wie viele gewerbliche Pilotkunden nutzen Ihre Speicherlösung aktuell im laufenden Betrieb?', '', '2026-11-25 13:10:10'),
(14, NULL, 12, 'Katrin Lindner', 'katrin.lindner@rhein-venture.de', 'Kooperationsanfrage für Handelsnetzwerk', 'Guten Tag, wir prüfen aktuell nachhaltige Verpackungslösungen für mittelständische Onlinehändler und würden gerne Details zu Ihren Stückkosten erfahren.', '', '2026-11-25 13:10:10');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favorite_companies`
--

CREATE TABLE `favorite_companies` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `favorite_companies`
--

INSERT INTO `favorite_companies` (`user_id`, `company_id`, `created_at`) VALUES
(1, 1, '2026-11-24 19:52:05'),
(1, 2, '2026-11-24 19:52:05'),
(1, 11, '2026-11-25 13:06:37'),
(1, 12, '2026-11-25 13:06:37'),
(5, 4, '2026-11-25 12:58:18'),
(5, 5, '2026-11-25 12:58:18');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('investor','founder','admin') NOT NULL DEFAULT 'investor',
  `address` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `profile_bio` text DEFAULT NULL,
  `card_last4` varchar(4) DEFAULT NULL,
  `card_expiry` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `address`, `telephone`, `profile_bio`, `card_last4`, `card_expiry`, `created_at`) VALUES
(1, 'Lukas Weber', 'investor@test.de', '$2y$10$.BhsLRlNiKof/ba3TSLlD.ZSlZKLdM6y3Z2z/A/N3Tq3SUIZxzTBi', 'investor', 'Musterstraße 12, 31134 Hildesheim', '+49 5121 111001', 'Business Angel mit Fokus auf B2B-SaaS, GreenTech und digitale Gesundheitslösungen.', NULL, NULL, '2026-11-24 19:52:03'),
(2, 'Miriam Hoffmann', 'founder1@test.de', '$2y$10$KkmAhNBMEBB0pyIcf9sif.bz4dwustsX7VGwfCw0NMtBnhdevu5zG', 'founder', 'Innovationsweg 5, 30159 Hannover', '+49 511 222002', 'Gründerin im Bereich nachhaltige Logistik und KI-gestützte Routenplanung.', NULL, NULL, '2026-11-24 19:52:03'),
(3, 'Jonas Becker', 'founder2@test.de', '$2y$10$.BhsLRlNiKof/ba3TSLlD.ZSlZKLdM6y3Z2z/A/N3Tq3SUIZxzTBi', 'founder', 'Technologiepark 8, 38106 Braunschweig', '+49 531 333003', 'Tech-Founder mit Schwerpunkt auf MedTech-Dokumentation und Workflow-Automatisierung.', NULL, NULL, '2026-11-24 19:52:03'),
(4, 'Admin Start-Connect', 'admin@test.de', '$2y$10$.BhsLRlNiKof/ba3TSLlD.ZSlZKLdM6y3Z2z/A/N3Tq3SUIZxzTBi', 'admin', 'Hauptstraße 1, 31134 Hildesheim', '+49 5121 999000', 'Plattform-Administration für Prüfung und Freigabe von Unternehmensprofilen.', NULL, NULL, '2026-11-24 19:52:03'),
(5, 'Tobias Richter', 'tobias.richter.neu@nordkapital.de', '$2y$10$.BhsLRlNiKof/ba3TSLlD.ZSlZKLdM6y3Z2z/A/N3Tq3SUIZxzTBi', 'investor', 'Georgstraße 48, 30159 Hannover', '+49 511 8901234', 'Business Angel und Partner für Frühphasen-Startups in Norddeutschland.', NULL, NULL, '2026-11-25 12:58:18'),
(6, 'Miriam Hoffmann', 'miriam.hoffmann.neu@kargo-flow.de', '$2y$10$.BhsLRlNiKof/ba3TSLlD.ZSlZKLdM6y3Z2z/A/N3Tq3SUIZxzTBi', 'founder', 'Schuhstraße 19, 31134 Hildesheim', '+49 5121 7894510', 'Gründerin und Geschäftsführerin im Bereich nachhaltige Logistik und Routenoptimierung.', NULL, NULL, '2026-11-25 12:58:18'),
(7, 'Lukas Weber', 'lukas.weber.neu@solargrid-nord.de', '$2y$10$.BhsLRlNiKof/ba3TSLlD.ZSlZKLdM6y3Z2z/A/N3Tq3SUIZxzTBi', 'founder', 'Bahnhofsallee 7, 38100 Braunschweig', '+49 531 4429180', 'Gründer mit Fokus auf dezentrale Energiespeicher und Smart-Grid-Software.', NULL, NULL, '2026-11-25 12:58:18'),
(8, 'Hannah Becker', 'hannah.becker.neu@start-connect.de', '$2y$10$.BhsLRlNiKof/ba3TSLlD.ZSlZKLdM6y3Z2z/A/N3Tq3SUIZxzTBi', 'admin', 'Almsstraße 14, 31134 Hildesheim', '+49 5121 3011920', 'Plattform-Administration, Qualitätssicherung und Rechnungsprüfung bei Start-Connect.', NULL, NULL, '2026-11-25 12:58:18');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bills_receipts`
--
ALTER TABLE `bills_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `fk_bills_user` (`user_id`),
  ADD KEY `fk_bills_company` (`company_id`);

--
-- Indizes für die Tabelle `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_companies_owner` (`owner_user_id`);

--
-- Indizes für die Tabelle `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contact_user` (`user_id`),
  ADD KEY `fk_contact_company` (`company_id`);

--
-- Indizes für die Tabelle `favorite_companies`
--
ALTER TABLE `favorite_companies`
  ADD PRIMARY KEY (`user_id`,`company_id`),
  ADD KEY `fk_fav_company` (`company_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bills_receipts`
--
ALTER TABLE `bills_receipts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `bills_receipts`
--
ALTER TABLE `bills_receipts`
  ADD CONSTRAINT `fk_bills_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bills_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_companies_owner` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `fk_contact_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_contact_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `favorite_companies`
--
ALTER TABLE `favorite_companies`
  ADD CONSTRAINT `fk_fav_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
