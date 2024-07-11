-- --------------------------------------------------------
-- Host:                         localhost
-- Server-Version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server-Betriebssystem:        Win64
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Exportiere Datenbank-Struktur für airlimited
CREATE DATABASE IF NOT EXISTS `airlimited` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;
USE `airlimited`;

-- Exportiere Struktur von Tabelle airlimited.auftrag
CREATE TABLE IF NOT EXISTS `auftrag` (
  `AuftragsNr` int(11) NOT NULL AUTO_INCREMENT,
  `Auftragsdatum` datetime DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'In Auftrag',
  `Enddatum` datetime DEFAULT NULL,
  `FertigungsNr` int(11) DEFAULT NULL,
  `SKUNr` int(11) NOT NULL,
  `Reihenfolge` int(11) DEFAULT NULL,
  PRIMARY KEY (`AuftragsNr`),
  KEY `auftrag SKUNr1` (`SKUNr`),
  KEY `auftrag FertigungsNr` (`FertigungsNr`),
  KEY `Reihenfolge` (`Reihenfolge`),
  CONSTRAINT `auftrag FertigungsNr` FOREIGN KEY (`FertigungsNr`) REFERENCES `fertigung` (`FertigungsNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `auftrag SKUNr1` FOREIGN KEY (`SKUNr`) REFERENCES `sku` (`SKUNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.auftrag: ~4 rows (ungefähr)
DELETE FROM `auftrag`;
INSERT INTO `auftrag` (`AuftragsNr`, `Auftragsdatum`, `Status`, `Enddatum`, `FertigungsNr`, `SKUNr`, `Reihenfolge`) VALUES
	(68, '2024-07-09 01:33:22', 'In Bearbeitung', NULL, 7, 1, 1),
	(69, '2024-07-09 01:34:47', 'In Bearbeitung', '2024-07-09 01:35:08', 1, 13, NULL),
	(70, '2024-07-09 15:09:59', 'In Bearbeitung', NULL, 9, 5, NULL),
	(71, '2024-07-09 23:16:51', 'In Bearbeitung', NULL, 8, 4, NULL),
	(72, '2024-07-11 21:07:00', 'In Bearbeitung', NULL, 7, 2, 2);

-- Exportiere Struktur von Tabelle airlimited.bestellposten
CREATE TABLE IF NOT EXISTS `bestellposten` (
  `BestellNr` int(11) NOT NULL,
  `BestellpostenNr` int(11) NOT NULL AUTO_INCREMENT,
  `Quantität` int(11) NOT NULL,
  `SKUNr` int(11) NOT NULL,
  `versandbereit` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`BestellpostenNr`,`BestellNr`) USING BTREE,
  KEY `SKUNr` (`SKUNr`),
  KEY `bestellposten_BestellNr` (`BestellNr`),
  CONSTRAINT `SKUNr` FOREIGN KEY (`SKUNr`) REFERENCES `sku` (`SKUNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `bestellposten_BestellNr` FOREIGN KEY (`BestellNr`) REFERENCES `bestellung` (`BestellNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.bestellposten: ~12 rows (ungefähr)
DELETE FROM `bestellposten`;
INSERT INTO `bestellposten` (`BestellNr`, `BestellpostenNr`, `Quantität`, `SKUNr`, `versandbereit`) VALUES
	(121, 130, 3, 1, 1),
	(122, 131, 200, 1, 0),
	(123, 132, 20, 1, 0),
	(124, 133, 200, 13, 1),
	(125, 134, 60, 13, 1),
	(126, 135, 3, 1, 1),
	(127, 136, 3, 5, 1),
	(128, 137, 80, 5, 0),
	(127, 138, 7, 24, 1),
	(127, 139, 2, 39, 1),
	(129, 140, 10, 1, 1),
	(129, 141, 4, 4, 1),
	(130, 142, 100, 4, 0),
	(131, 143, 5, 1, 1),
	(132, 144, 50, 2, 0),
	(133, 145, 50, 2, 0);

-- Exportiere Struktur von Tabelle airlimited.bestellung
CREATE TABLE IF NOT EXISTS `bestellung` (
  `BestellNr` int(11) NOT NULL AUTO_INCREMENT,
  `Bestelldatum` datetime DEFAULT NULL,
  `ServicepartnerNr` int(11) DEFAULT NULL,
  `LagerNr` int(11) DEFAULT NULL,
  PRIMARY KEY (`BestellNr`),
  KEY `bestellung LagerNr` (`LagerNr`),
  KEY `bestellung ServicepartnerNr` (`ServicepartnerNr`),
  CONSTRAINT `bestellung LagerNr` FOREIGN KEY (`LagerNr`) REFERENCES `lager` (`LagerNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `bestellung ServicepartnerNr` FOREIGN KEY (`ServicepartnerNr`) REFERENCES `servicepartner` (`ServicepartnerNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.bestellung: ~8 rows (ungefähr)
DELETE FROM `bestellung`;
INSERT INTO `bestellung` (`BestellNr`, `Bestelldatum`, `ServicepartnerNr`, `LagerNr`) VALUES
	(121, '2024-07-09 01:31:44', 1, NULL),
	(122, '2024-07-09 01:33:22', 1, NULL),
	(123, '2024-07-09 01:33:22', NULL, 7),
	(124, '2024-07-09 01:34:47', 1, NULL),
	(125, '2024-07-09 01:34:47', NULL, 1),
	(126, '2024-07-09 01:37:44', 1, NULL),
	(127, '2024-07-09 15:09:59', NULL, 5),
	(128, '2024-07-09 15:09:59', NULL, 9),
	(129, '2024-07-09 23:16:51', 1, NULL),
	(130, '2024-07-09 23:16:51', NULL, 8),
	(131, '2024-07-11 21:06:29', 1, NULL),
	(132, '2024-07-11 21:07:00', 1, NULL),
	(133, '2024-07-11 21:07:00', NULL, 7);

-- Exportiere Struktur von Tabelle airlimited.fertigung
CREATE TABLE IF NOT EXISTS `fertigung` (
  `FertigungsNr` int(11) NOT NULL AUTO_INCREMENT,
  `Straße` varchar(50) DEFAULT NULL,
  `HausNr` int(11) DEFAULT NULL,
  `PLZ` varchar(50) DEFAULT NULL,
  `Stadt` varchar(50) DEFAULT NULL,
  `Land` varchar(50) DEFAULT NULL,
  `TelefonNr` varchar(50) DEFAULT NULL,
  `Passwort` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`FertigungsNr`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.fertigung: ~10 rows (ungefähr)
DELETE FROM `fertigung`;
INSERT INTO `fertigung` (`FertigungsNr`, `Straße`, `HausNr`, `PLZ`, `Stadt`, `Land`, `TelefonNr`, `Passwort`) VALUES
	(1, 'Industriestraße', 20, '12345', 'Berlin', 'Deutschland', '+49 30 7654321', '$2y$10$NOPLXvLCIMJ.1LlHT2YdhOJDtzPuWTU0EkcMYoQx45QTvCxpQqS/6'),
	(2, 'Fabrikweg', 15, '54321', 'München', 'Deutschland', '+49 89 9876543', '$2y$10$nFbDtM.G15gDiB3kIwW3zOJfB968A.YV5m3BOdt./nkS0CqBe7Xyu'),
	(3, 'Produktionsstraße', 30, '67890', 'Rom', 'Italien', '+39 06 23456789', '$2y$10$gWhykGmJ2g4wgodpzfi.8.FTapuMrX90X//HHpZmUdQMPKUzKApfu'),
	(4, 'Maschinenweg', 25, '13579', 'Barcelona', 'Spanien', '+34 93 357911', '$2y$10$vvXYAo.MFHQBZ3pnTV0g3uiYBeDybPSWvr4MRFr2LFjRGQqc0Q0W6'),
	(5, 'Herstellungsweg', 10, '98765', 'Mailand', 'Italien', '+39 02 246810', '$2y$10$GOSEeFrixhILnfs2nKA1j.uKwmc.itQDIJtlEOLnm4qIC/P.lQMxG'),
	(6, 'Fertigungsallee', 5, '24680', 'Lissabon', 'Portugal', '+351 21 8765432', '$2y$10$XlD3Kar1YQSJ9nf1lmYe0.nCld9eGt2WSubZSmt7Or7VLn9m1rUGu'),
	(7, 'Fabrikstraße', 8, '56789', 'Prag', 'Tschechien', '+420 221 9876543', '$2y$10$tEiN3co6F1e0WP3BMhYnvuk5K0c./dVUYNg3/rxlg.QcFR4JUdADq'),
	(8, 'Produktionsweg', 12, '78901', 'Warschau', 'Polen', '+48 22 6543210', '$2y$10$MqEXdONfVrDVlw9GIpZrje8aJWx/Zx.95qJAoCXksjGAtovDPGSb2'),
	(9, 'Montageweg', 18, '23456', 'Budapest', 'Ungarn', '+36 1 1357924', '$2y$10$mh9Zr6IDoGFdGwn4pWGO1.XuuRkzpqjw223VYQW9MfJ7ug8CiQiUe'),
	(10, 'Herstellungsstraße', 6, '34567', 'Kopenhagen', 'Dänemark', '+45 1234 5678', '$2y$10$4FsW/KuZRzXiYqcxx0/XiOj2KfghC/EuQic0Fpx9c2B0E62BkY5Li');

-- Exportiere Struktur von Tabelle airlimited.gehoert_zu
CREATE TABLE IF NOT EXISTS `gehoert_zu` (
  `AuftragsNr` int(11) NOT NULL,
  `BestellNr` int(11) NOT NULL,
  `Quantitaet` int(11) DEFAULT NULL,
  `Versandt` varchar(5) DEFAULT 'Nein',
  PRIMARY KEY (`AuftragsNr`,`BestellNr`),
  KEY `gehört_zu_BestellNr` (`BestellNr`),
  CONSTRAINT `gehört_zu_AuftragsNr` FOREIGN KEY (`AuftragsNr`) REFERENCES `auftrag` (`AuftragsNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `gehört_zu_BestellNr` FOREIGN KEY (`BestellNr`) REFERENCES `bestellung` (`BestellNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.gehoert_zu: ~5 rows (ungefähr)
DELETE FROM `gehoert_zu`;
INSERT INTO `gehoert_zu` (`AuftragsNr`, `BestellNr`, `Quantitaet`, `Versandt`) VALUES
	(68, 122, 115, 'Nein'),
	(68, 123, 20, 'Nein'),
	(69, 124, 132, 'Nein'),
	(69, 125, 60, 'Nein'),
	(70, 128, 80, 'Nein'),
	(71, 130, 100, 'Nein'),
	(72, 132, 4, 'Nein'),
	(72, 133, 50, 'Nein');

-- Exportiere Struktur von Tabelle airlimited.lager
CREATE TABLE IF NOT EXISTS `lager` (
  `LagerNr` int(11) NOT NULL AUTO_INCREMENT,
  `Lagerstandort` varchar(50) DEFAULT NULL,
  `Straße` varchar(50) DEFAULT NULL,
  `HausNr` int(11) DEFAULT NULL,
  `PLZ` varchar(50) DEFAULT NULL,
  `Land` varchar(50) DEFAULT NULL,
  `Verantwortlicher Vormane` varchar(50) DEFAULT NULL,
  `Verantwortlicher Nachname` varchar(50) DEFAULT NULL,
  `TelefonNr` varchar(50) DEFAULT NULL,
  `Passwort` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`LagerNr`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.lager: ~10 rows (ungefähr)
DELETE FROM `lager`;
INSERT INTO `lager` (`LagerNr`, `Lagerstandort`, `Straße`, `HausNr`, `PLZ`, `Land`, `Verantwortlicher Vormane`, `Verantwortlicher Nachname`, `TelefonNr`, `Passwort`) VALUES
	(1, 'Berlin', 'Industriestraße', 10, '12345', 'Deutschland', 'Max', 'Schulze', '+49 30 1234567', '$2y$10$0NRqFxxIGPCJFmvEmps60uTKuGMIFgEOlKn54gMsBwyF4xb6fZEWK'),
	(2, 'München', 'Hauptstraße', 25, '54321', 'Deutschland', 'Julia', 'Becker', '+49 40 9876543', '$2y$10$vZtYeFKqKdBQJcUfh/9opuRimBsZ2jrwstdxIj9Ix1rhuN7gpILgq'),
	(3, 'Rom', 'Luftweg', 5, '67890', 'Italien', 'Sophie', 'Leclerc', '+33 1 23456789', '$2y$10$w.bRtN/ej2x0oEeOCaEbGewIo.qZVjZLpYeAPpzm/r.3AitgtUQ2a'),
	(4, 'Barcelona', 'Kühlstraße', 15, '13579', 'Spanien', 'Felix', 'Hoffmann', '+49 69 357911', '$2y$10$fhtzNboBj9EgDzhVVlUmKeU4mrtAntY63oK2Bp1FNWLJgTQeOtrEa'),
	(5, 'Venedig', 'Windweg', 8, '98765', 'Italien', 'Nina', 'Wagner', '+49 711 135790', '$2y$10$RgcfQfjRHbdvVyVNwtHCW.EDfXzTRuuv.ElgqgfEjkF63HPj4C45W'),
	(6, 'Lissabon', 'Luftallee', 20, '24680', 'Portugal', 'Carlos', 'García', '+34 91 2345678', '$2y$10$.4bJYWRhwPSoTFqaSSJTD.FWqOtbdmRA/LGfJ9RhT5k9yyPMCxm2m'),
	(7, 'Prag', 'Briseallee', 12, '56789', 'Tschechien', 'Lena', 'Schneider', '+49 221 901234', '$2y$10$TimJM44ll9ol1LsFh3rl3ugojDUQeeqp9XaZNBIGA6HcclPEPvRii'),
	(8, 'Warschau', 'Frischweg', 30, '78901', 'Polen', 'Tom', 'Mayer', '+49 351 246801', '$2y$10$NE5j6f2vzg.Jm2b6JCUXKuZqKRsO.T4lCvvXr1AQEgffRe9Umyzba'),
	(9, 'Budapest', 'Klimaweg', 18, '23456', 'Ungarn', 'Olivia', 'Taylor', '+44 20 3456789', '$2y$10$PbvzG8xUW3Q4QA32kkmuhuoyzeW0It2BJgnFKNSCPaMsniNjfoT2O'),
	(10, 'Kopenhagen', 'Luftmeisterstraße', 6, '34567', 'Dänemark', 'Finn', 'Schulz', '+49 341 135792', '$2y$10$4eyJ5R8BAVv2vpRL//srb.JT5AnKIUenVENK.Y1D2f9A0frGhazmC');

-- Exportiere Struktur von Tabelle airlimited.management
CREATE TABLE IF NOT EXISTS `management` (
  `ManagementNr` int(11) NOT NULL,
  `Passwort` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ManagementNr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.management: ~5 rows (ungefähr)
DELETE FROM `management`;
INSERT INTO `management` (`ManagementNr`, `Passwort`) VALUES
	(1, '$2y$10$.F76M42dFt3x48NnS5bzd.rRRITLnbhzAphJSgzZgCS6PDK6g5xq2'),
	(2, '$2y$10$JwDy87eHS1vBDWBeH9W/mOeXLRSHYjPR44aZy/CpfTNZNiJm36mEm'),
	(3, '$2y$10$Y7zAFxNjujsdHUoKhShMg.kuHbocLSchWDGYgeGoUIAOK.54p1SJS'),
	(4, '$2y$10$xQ6mBm1DCHlo..wRK5QgIO/HhBC0xOWofTwF7YacagPUjbDlDMg3G'),
	(5, '$2y$10$1f/otgAc3cXHvfU/2sEHKehlKKeLPIeNtXoQgwUPA68X7IHbeRpdG');

-- Exportiere Struktur von Tabelle airlimited.servicepartner
CREATE TABLE IF NOT EXISTS `servicepartner` (
  `ServicepartnerNr` int(11) NOT NULL AUTO_INCREMENT,
  `Firmenname` varchar(50) DEFAULT NULL,
  `Nachname Kontaktperson` varchar(50) DEFAULT NULL,
  `Vorname Kontaktperson` varchar(50) DEFAULT NULL,
  `Straße` varchar(50) DEFAULT NULL,
  `HausNr` int(11) DEFAULT NULL,
  `Stadt` varchar(50) DEFAULT NULL,
  `PLZ` varchar(50) DEFAULT NULL,
  `Land` varchar(50) DEFAULT NULL,
  `TelefonNr` varchar(50) DEFAULT NULL,
  `E-Mail` varchar(50) DEFAULT NULL,
  `VIPKunde` varchar(5) DEFAULT 'Nein',
  `Passwort` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ServicepartnerNr`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.servicepartner: ~9 rows (ungefähr)
DELETE FROM `servicepartner`;
INSERT INTO `servicepartner` (`ServicepartnerNr`, `Firmenname`, `Nachname Kontaktperson`, `Vorname Kontaktperson`, `Straße`, `HausNr`, `Stadt`, `PLZ`, `Land`, `TelefonNr`, `E-Mail`, `VIPKunde`, `Passwort`) VALUES
	(1, 'Ventilator-Expert GmbH', 'Müller', 'Klaus', 'Industriestraße', 11, 'Berlin', '12345', 'Deutschland', '+49 30 1234567', 'klaus.mueller@ventilator-expert.de', 'Ja', '$2y$10$L15a72qsdNI2N1p.NNkjEOayh42TY49Qdb.6ZJnAiIgfa6r2/Fm2m'),
	(2, 'Luftstrom AG', 'Schneider', 'Sabine', 'Hauptstraße', 25, 'Hamburg', '54321', 'Deutschland', '+49 40 9876543', 'sabine.schneider@luftstrom-ag.de', 'Nein', '$2y$10$/QRSRjkSfgbwR1IfaP.2XeyLLK3vojt0GjN4alB5go3p/41vCef0m'),
	(3, 'AirTech Solutions GmbH', 'Meier', 'Michael', 'Luftweg', 5, 'München', '67890', 'Deutschland', '+49 89 246813', 'michael.meier@airtech-solutions.de', 'Nein', '$2y$10$ZaIR2sXaKi.BwUWSOFNzKO44k1WEE3doK/Eb/ZNUdT6gpIq0Zjrgm'),
	(4, 'Cooling Systems GmbH', 'Schulz', 'Andreas', 'Kühlstraße', 15, 'Frankfurt', '13579', 'Deutschland', '+49 69 357911', 'andreas.schulz@coolingsystems.de', 'Nein', '$2y$10$nbQ9at9A2Vpd1GbMn3YAHuCoUB38w2imfzOIzz5rC0s3t2J5Vftku'),
	(5, 'Ventilation Services Ltd.', 'Hoffmann', 'Anna', 'Windweg', 8, 'Stuttgart', '98765', 'Deutschland', '+49 711 135790', 'anna.hoffmann@ventilationservices.com', 'Ja', '$2y$10$uDMUv/1qoSQJZBO8QhK0uu5GxNkM/YUPR/D37AJMUkmG6INxcSUpa'),
	(6, 'AirFlow Experts GmbH', 'Wagner', 'Peter', 'Luftallee', 20, 'Düsseldorf', '24680', 'Deutschland', '+49 211 468013', 'peter.wagner@airflow-experts.de', 'Nein', '$2y$10$yWEbgbDI7yK28ovT56UOwu1Bphu2s2KAs1DVzAC9QoHPQ2YOqHvz2'),
	(7, 'Breeze Solutions GmbH', 'Becker', 'Sandra', 'Briseallee', 12, 'Köln', '56789', 'Deutschland', '+49 221 901234', 'sandra.becker@breeze-solutions.de', 'Nein', '$2y$10$FGmehw.PJi3wsVV5qU6vhe9w3.wL.NKHtNBQy6iQPKMOylPMWXWJ6'),
	(8, 'FreshAir GmbH', 'Zimmermann', 'Thomas', 'Frischweg', 30, 'Dresden', '78901', 'Deutschland', '+49 351 246801', 'thomas.zimmermann@freshair.de', 'Ja', '$2y$10$FIVbfAWOnFZCjLdFPXu9gOG.S6GroNfpU5ZS8DJgcwVbMUMu7kj7u'),
	(9, 'Climate Control AG', 'Hahn', 'Markus', 'Klimaweg', 18, 'Hannover', '23456', 'Deutschland', '+49 511 679013', 'markus.hahn@climatecontrol-ag.de', 'Nein', '$2y$10$UpB0kqNeJHlQ9UdueXj.0.745hEFpDmvc6beCdzoF4RAQjHm2dc6i');

-- Exportiere Struktur von Tabelle airlimited.sind_in
CREATE TABLE IF NOT EXISTS `sind_in` (
  `LagerNr` int(11) NOT NULL,
  `SKUNr` int(11) NOT NULL,
  `Bestand` int(11) DEFAULT NULL,
  PRIMARY KEY (`LagerNr`,`SKUNr`),
  KEY `sind_in SKUNr` (`SKUNr`),
  CONSTRAINT `sind_in LagerNr` FOREIGN KEY (`LagerNr`) REFERENCES `lager` (`LagerNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sind_in SKUNr` FOREIGN KEY (`SKUNr`) REFERENCES `sku` (`SKUNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.sind_in: ~60 rows (ungefähr)
DELETE FROM `sind_in`;
INSERT INTO `sind_in` (`LagerNr`, `SKUNr`, `Bestand`) VALUES
	(1, 13, 60),
	(1, 17, 110),
	(1, 33, 40),
	(1, 57, 20),
	(2, 12, 70),
	(2, 22, 89),
	(2, 26, 88),
	(2, 48, 53),
	(2, 56, 12),
	(3, 10, 13),
	(3, 15, 15),
	(3, 36, 14),
	(3, 41, 16),
	(3, 43, 67),
	(4, 18, 37),
	(4, 20, 35),
	(4, 38, 14),
	(4, 53, 568),
	(5, 3, 24),
	(5, 6, 15),
	(5, 23, 36),
	(5, 54, 47),
	(6, 39, 55),
	(6, 40, 46),
	(6, 49, 86),
	(6, 50, 57),
	(6, 58, 46),
	(7, 1, 67),
	(7, 2, 46),
	(7, 27, 33),
	(7, 32, 14),
	(7, 35, 345),
	(7, 44, 35),
	(7, 46, 36),
	(7, 60, 36),
	(8, 4, 11),
	(8, 7, 77),
	(8, 8, 36),
	(8, 9, 74),
	(8, 14, 63),
	(8, 21, 75),
	(8, 28, 57),
	(8, 29, 63),
	(8, 30, 36),
	(8, 34, 36),
	(8, 45, 25),
	(8, 47, 13),
	(8, 55, 15),
	(9, 5, 71),
	(9, 19, 754),
	(9, 25, 74),
	(9, 37, 14),
	(9, 42, 15),
	(9, 52, 64),
	(9, 59, 64),
	(10, 11, 75),
	(10, 16, 634),
	(10, 24, 626),
	(10, 31, 22),
	(10, 51, 11);

-- Exportiere Struktur von Tabelle airlimited.sku
CREATE TABLE IF NOT EXISTS `sku` (
  `SKUNr` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(20) NOT NULL,
  `Standardlosgroeße` int(11) NOT NULL DEFAULT 0,
  `Laenge` float DEFAULT NULL,
  `Breite` float DEFAULT NULL,
  `Hoehe` float DEFAULT NULL,
  `Gewicht` float DEFAULT NULL,
  `Beschreibung` varchar(200) DEFAULT NULL,
  `Preis` float DEFAULT NULL,
  `Foto` varchar(50) DEFAULT NULL,
  `Art` varchar(50) DEFAULT NULL,
  `Fertigungsanweisungen` varchar(50) DEFAULT NULL,
  `FertigungsNr` int(11) DEFAULT NULL,
  `LagerNr` int(11) DEFAULT NULL,
  PRIMARY KEY (`SKUNr`),
  KEY `FertigungsNr` (`FertigungsNr`),
  KEY `Lagerstandort` (`LagerNr`),
  CONSTRAINT `sku FertigungsNr` FOREIGN KEY (`FertigungsNr`) REFERENCES `fertigung` (`FertigungsNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sku LagerNr` FOREIGN KEY (`LagerNr`) REFERENCES `lager` (`LagerNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2051 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.sku: ~60 rows (ungefähr)
DELETE FROM `sku`;
INSERT INTO `sku` (`SKUNr`, `Name`, `Standardlosgroeße`, `Laenge`, `Breite`, `Hoehe`, `Gewicht`, `Beschreibung`, `Preis`, `Foto`, `Art`, `Fertigungsanweisungen`, `FertigungsNr`, `LagerNr`) VALUES
	(1, 'Lüfterblatt', 20, 30, 30, 5, 0.5, 'Hochwertiges Lüfterblatt für Industrieanlagen', 25.99, 'lüfterblatt.jpg', 'Komplex', 'lüfterblatt_anweisungen.pdf', 7, 7),
	(2, 'Lager', 50, 10, 10, 5, 0.1, 'Hochleistungslager für Ventilatoren', 8.75, 'lager.jpg', 'Einfach', 'lager_anweisungen.pdf', 7, 7),
	(3, 'Flügelrad', 30, 25, 25, 7, 0.7, 'Leichtes und effizientes Flügelrad', 39.99, 'fluegelrad.jpg', 'Komplex', 'fluegelrad_anweisungen.pdf', 5, 5),
	(4, 'Motor', 100, 20, 15, 10, 2.5, 'Hochleistungsmotor für industrielle Ventilatoren', 149.99, 'motor.jpg', 'Komplex', 'motor_anweisungen.pdf', 8, 8),
	(5, 'Stator', 80, 30, 30, 20, 5, 'Robuster Stator für große Ventilatoren', 199.99, 'stator.jpg', 'Komplex', 'stator_anweisungen.pdf', 9, 9),
	(6, 'Gehäuse', 150, 40, 40, 30, 8, 'Stabiles Gehäuse für Ventilatorbaugruppen', 299.99, 'gehaeuse.jpg', 'Komplex', 'gehaeuse_anweisungen.pdf', 5, 5),
	(7, 'Rotor', 40, 25, 25, 10, 1.5, 'Präzisionsrotor für effiziente Luftströmung', 69.99, 'rotor.jpg', 'Komplex', 'rotor_anweisungen.pdf', 8, 8),
	(8, 'Schraube', 10, 5, 5, 2, 0.05, 'Hochfeste Befestigungsschraube', 0.99, 'schraube.jpg', 'Einfach', 'schraube_anweisungen.pdf', 8, 8),
	(9, 'Schalldämpfer', 70, 30, 20, 15, 3, 'Effektiver Schalldämpfer für geräuscharmen Betrieb', 49.99, 'schalldaempfer.jpg', 'Komplex', 'schalldaempfer_anweisungen.pdf', 8, 8),
	(10, 'Blendenkappe', 20, 10, 10, 5, 0.2, 'Kappen für Lüftungsöffnungen', 4.99, 'blendenkappe.jpg', 'Einfach', 'blendenkappe_anweisungen.pdf', 3, 3),
	(11, 'Steuerungseinheit', 120, 15, 10, 5, 1, 'Intelligente Steuerung für Ventilatoren', 129.99, 'steuerung.jpg', 'Komplex', 'steuerung_anweisungen.pdf', 10, 10),
	(12, 'Ausgleichsgewicht', 20, 5, 5, 5, 0.3, 'Gewichte für Balancierung von Ventilatorflügeln', 9.99, 'ausgleichsgewicht.jpg', 'Einfach', 'ausgleichsgewicht_anweisungen.pdf', 2, 2),
	(13, 'Antriebsriemen', 60, 50, 5, 5, 0.5, 'Robuste Riemen für Motorantriebe', 19.99, 'antriebsriemen.jpg', 'Einfach', 'antriebsriemen_anweisungen.pdf', 1, 1),
	(14, 'Luftleitschaufel', 25, 20, 15, 5, 0.3, 'Präzise gefertigte Luftleitschaufel für optimale Luftströmung', 12.99, 'luftleitschaufel.jpg', 'Komplex', 'luftleitschaufel_anweisungen.pdf', 8, 8),
	(15, 'Dichtungssatz', 40, 10, 10, 2, 0.1, 'Hochwertiger Dichtungssatz für Ventilatorbaugruppen', 6.99, 'dichtungssatz.jpg', 'Einfach', 'dichtungssatz_anweisungen.pdf', 3, 3),
	(16, 'Ventilatorflügel', 35, 30, 30, 8, 0.6, 'Leistungsstarke Ventilatorflügel für industrielle Anwendungen', 29.99, 'ventilatorfluegel.jpg', 'Komplex', 'ventilatorfluegel_anweisungen.pdf', 10, 10),
	(17, 'Achse', 60, 20, 20, 20, 1, 'Stabile Achse für Ventilatoren', 19.99, 'achse.jpg', 'Einfach', 'achse_anweisungen.pdf', 1, 1),
	(18, 'Drehzahlregler', 80, 10, 5, 2, 0.3, 'Präziser Drehzahlregler für Ventilatoren', 49.99, 'drehzahlregler.jpg', 'Komplex', 'drehzahlregler_anweisungen.pdf', 4, 4),
	(19, 'Sicherungsschalter', 30, 5, 5, 2, 0.1, 'Zuverlässiger Sicherungsschalter für Ventilatoren', 7.99, 'sicherungsschalter.jpg', 'Einfach', 'sicherungsschalter_anweisungen.pdf', 9, 9),
	(20, 'Filterelement', 45, 15, 15, 10, 0.5, 'Effektives Filterelement für Luftreinigung', 14.99, 'filterelement.jpg', 'Einfach', 'filterelement_anweisungen.pdf', 4, 4),
	(21, 'Schaltkreisplatine', 90, 10, 10, 2, 0.2, 'Hochwertige Schaltkreisplatine für Ventilatorsteuerungen', 79.99, 'schaltkreisplatine.jpg', 'Komplex', 'schaltkreisplatine_anweisungen.pdf', 8, 8),
	(22, 'Befestigungsklammer', 15, 5, 5, 3, 0.05, 'Robuste Befestigungsklammer für Ventilatorkomponenten', 1.99, 'befestigungsklammer.jpg', 'Einfach', 'befestigungsklammer_anweisungen.pdf', 2, 2),
	(23, 'Gummifüße', 10, 5, 5, 2, 0.05, 'Hochwertige Gummifüße für Vibrationsdämpfung', 3.99, 'gummifuesse.jpg', 'Einfach', 'gummifuesse_anweisungen.pdf', 5, 5),
	(24, 'Ventilatorblende', 25, 10, 10, 3, 0.3, 'Stabile Blenden für Ventilatorgehäuse', 9.99, 'ventilatorblende.jpg', 'Einfach', 'ventilatorblende_anweisungen.pdf', 10, 10),
	(25, 'Spannungsregler', 70, 10, 5, 2, 0.2, 'Präziser Spannungsregler für Ventilatorantriebe', 39.99, 'spannungsregler.jpg', 'Komplex', 'spannungsregler_anweisungen.pdf', 9, 9),
	(26, 'Austrittsgitter', 20, 20, 20, 3, 0.2, 'Hochwertiges Austrittsgitter für Ventilatoren', 8.99, 'austrittsgitter.jpg', 'Einfach', 'austrittsgitter_anweisungen.pdf', 2, 2),
	(27, 'Luftführungskanal', 60, 40, 20, 10, 1.5, 'Effizienter Luftführungskanal für Ventilatoranlagen', 24.99, 'luftfuehrungskanal.jpg', 'Komplex', 'luftfuehrungskanal_anweisungen.pdf', 7, 7),
	(28, 'Schnellverschlusskup', 15, 5, 5, 2, 0.1, 'Zuverlässige Schnellverschlusskupplungen für Ventilatorschläuche', 5.99, 'schnellverschlusskupplung.jpg', 'Einfach', 'schnellverschlusskupplung_anweisungen.pdf', 8, 8),
	(29, 'Schmiermittel', 10, 5, 5, 5, 0.2, 'Hochwertiges Schmiermittel für Ventilatorlager', 4.99, 'schmiermittel.jpg', 'Einfach', 'schmiermittel_anweisungen.pdf', 8, 8),
	(30, 'Montagehalterung', 25, 10, 10, 5, 0.3, 'Robuste Montagehalterungen für Ventilatorinstallationen', 7.99, 'montagehalterung.jpg', 'Einfach', 'montagehalterung_anweisungen.pdf', 8, 8),
	(31, 'Ventilatorsteuerpult', 100, 30, 20, 10, 2, 'Modernes Steuerpult für industrielle Ventilatoren', 99.99, 'steuerpult.jpg', 'Komplex', 'steuerpult_anweisungen.pdf', 10, 10),
	(32, 'Lagerbock', 30, 15, 15, 10, 0.5, 'Stabiler Lagerbock für Ventilatormontage', 12.99, 'lagerbock.jpg', 'Einfach', 'lagerbock_anweisungen.pdf', 7, 7),
	(33, 'Ablaufleitung', 40, 20, 20, 5, 0.3, 'Effiziente Ablaufleitungen für Kondenswasserabfluss', 9.99, 'ablaufleitung.jpg', 'Einfach', 'ablaufleitung_anweisungen.pdf', 1, 1),
	(34, 'Motorschutzschalter', 50, 5, 5, 3, 0.2, 'Zuverlässiger Motorschutzschalter für Ventilatoren', 14.99, 'motorschutzschalter.jpg', 'Einfach', 'motorschutzschalter_anweisungen.pdf', 8, 8),
	(35, 'Kühlgebläse', 70, 20, 20, 10, 1, 'Effektives Kühlgebläse für Ventilatorantriebe', 29.99, 'kuehlgeblaese.jpg', 'Einfach', 'kuehlgeblaese_anweisungen.pdf', 7, 7),
	(36, 'Wandhalterung', 20, 10, 10, 5, 0.5, 'Stabile Wandhalterungen für Ventilatoren', 6.99, 'wandhalterung.jpg', 'Einfach', 'wandhalterung_anweisungen.pdf', 3, 3),
	(37, 'Schwingungsdämpfer', 15, 5, 5, 3, 0.1, 'Effektive Schwingungsdämpfer für ruhigen Betrieb', 3.99, 'schwingungsdaempfer.jpg', 'Einfach', 'schwingungsdaempfer_anweisungen.pdf', 9, 9),
	(38, 'Drucksensor', 80, 5, 5, 5, 0.3, 'Präziser Drucksensor für Ventilatorüberwachung', 49.99, 'drucksensor.jpg', 'Komplex', 'drucksensor_anweisungen.pdf', 4, 4),
	(39, 'Isoliermatte', 30, 30, 30, 5, 0.5, 'Effektive Isoliermatten für Schalldämpfung', 9.99, 'isoliermatte.jpg', 'Einfach', 'isoliermatte_anweisungen.pdf', 6, 6),
	(40, 'Hitzeschild', 40, 20, 20, 5, 0.3, 'Robustes Hitzeschild für Ventilatoranlagen', 14.99, 'hitzeschild.jpg', 'Einfach', 'hitzeschild_anweisungen.pdf', 6, 6),
	(41, 'Drehflügel', 25, 20, 20, 5, 0.4, 'Präzise gefertigte Drehflügel für effiziente Luftzirkulation', 18.99, 'drehfluegel.jpg', 'Komplex', 'drehfluegel_anweisungen.pdf', 3, 3),
	(42, 'Stellmotor', 50, 10, 10, 5, 0.5, 'Zuverlässiger Stellmotor für Ventilatorsteuerung', 29.99, 'stellmotor.jpg', 'Komplex', 'stellmotor_anweisungen.pdf', 9, 9),
	(43, 'Dichtungsband', 15, 5, 5, 2, 0.1, 'Hochwertiges Dichtungsband für Luftabdichtung', 3.99, 'dichtungsband.jpg', 'Einfach', 'dichtungsband_anweisungen.pdf', 3, 3),
	(44, 'Kondensatablauf', 30, 20, 20, 10, 0.6, 'Effizienter Kondensatablauf für Ventilatoranlagen', 11.99, 'kondensatablauf.jpg', 'Einfach', 'kondensatablauf_anweisungen.pdf', 7, 7),
	(45, 'Saugrohr', 35, 40, 20, 15, 1.2, 'Stabiles Saugrohr für Luftansaugung', 17.99, 'saugrohr.jpg', 'Einfach', 'saugrohr_anweisungen.pdf', 8, 8),
	(46, 'Kugellager', 20, 5, 5, 5, 0.2, 'Hochwertige Kugellager für Ventilatoren', 5.99, 'kugellager.jpg', 'Einfach', 'kugellager_anweisungen.pdf', 7, 7),
	(47, 'Schnellkupplung', 15, 5, 5, 3, 0.1, 'Effektive Schnellkupplungen für Ventilatorleitungen', 4.99, 'schnellkupplung.jpg', 'Einfach', 'schnellkupplung_anweisungen.pdf', 8, 8),
	(48, 'Vibrationsdämpfer', 20, 5, 5, 2, 0.2, 'Robuste Vibrationsdämpfer für geräuscharmen Betrieb', 6.99, 'vibrationsdaempfer.jpg', 'Einfach', 'vibrationsdaempfer_anweisungen.pdf', 2, 2),
	(49, 'Hochleistungsschalte', 40, 5, 5, 3, 0.2, 'Zuverlässiger Hochleistungsschalter für Ventilatoren', 12.99, 'hochleistungsschalter.jpg', 'Einfach', 'hochleistungsschalter_anweisungen.pdf', 6, 6),
	(50, 'Klemmverbinder', 10, 5, 5, 2, 0.1, 'Stabile Klemmverbinder für Ventilatorleitungen', 3.99, 'klemmverbinder.jpg', 'Einfach', 'klemmverbinder_anweisungen.pdf', 6, 6),
	(51, 'Ventilatorgehäuse', 60, 30, 30, 20, 3.5, 'Robustes Gehäuse für industrielle Ventilatoren', 39.99, 'ventilatorgehaeuse.jpg', 'Komplex', 'ventilatorgehaeuse_anweisungen.pdf', 10, 10),
	(52, 'Steuerkabel', 25, 5, 5, 2, 0.1, 'Hochwertige Steuerkabel für Ventilatorsteuerungen', 4.99, 'steuerkabel.jpg', 'Einfach', 'steuerkabel_anweisungen.pdf', 9, 9),
	(53, 'Druckluftschlauch', 30, 10, 10, 5, 0.3, 'Robuster Druckluftschlauch für Ventilatoranwendungen', 8.99, 'druckluftschlauch.jpg', 'Einfach', 'druckluftschlauch_anweisungen.pdf', 4, 4),
	(54, 'Gleitlager', 15, 5, 5, 5, 0.1, 'Hochwertige Gleitlager für Ventilatoren', 3.99, 'gleitlager.jpg', 'Einfach', 'gleitlager_anweisungen.pdf', 5, 5),
	(55, 'Ölabscheider', 20, 10, 10, 5, 0.4, 'Effizienter Ölabscheider für Ventilatoren', 9.99, 'oelabscheider.jpg', 'Einfach', 'oelabscheider_anweisungen.pdf', 8, 8),
	(56, 'Axiallüfter', 45, 20, 20, 10, 1, 'Leistungsstarker Axiallüfter für industrielle Anwendungen', 34.99, 'axialluefter.jpg', 'Einfach', 'axialluefter_anweisungen.pdf', 2, 2),
	(57, 'Ventilatormesser', 25, 15, 5, 2, 0.2, 'Präzise Ventilatormesser für Leistungsüberwachung', 7.99, 'ventilatormesser.jpg', 'Einfach', 'ventilatormesser_anweisungen.pdf', 1, 1),
	(58, 'Heizwiderstand', 40, 5, 5, 2, 0.3, 'Effektiver Heizwiderstand für Ventilatorheizungen', 11.99, 'heizwiderstand.jpg', 'Einfach', 'heizwiderstand_anweisungen.pdf', 6, 6),
	(59, 'Stecker', 10, 5, 5, 2, 0.1, 'Zuverlässige Stecker für Ventilatoranschlüsse', 2.99, 'stecker.jpg', 'Einfach', 'stecker_anweisungen.pdf', 9, 9),
	(60, 'Luftdrucksensor', 30, 5, 5, 2, 0.2, 'Präziser Luftdrucksensor für Ventilatorüberwachung', 9.99, 'luftdrucksensor.jpg', 'Einfach', 'luftdrucksensor_anweisungen.pdf', 7, 7);

-- Exportiere Struktur von Tabelle airlimited.warenkorb
CREATE TABLE IF NOT EXISTS `warenkorb` (
  `WarenkorbNr` int(11) NOT NULL AUTO_INCREMENT,
  `ServicepartnerNr` int(11) DEFAULT NULL,
  `LagerNr` int(11) DEFAULT NULL,
  `SKUNr` int(11) NOT NULL,
  `Menge` int(11) NOT NULL,
  PRIMARY KEY (`WarenkorbNr`),
  KEY `warenkorb_ServicepartnerNr` (`ServicepartnerNr`),
  KEY `warenkorb_LagerNr` (`LagerNr`),
  KEY `warenkorb_SKUNr` (`SKUNr`),
  CONSTRAINT `warenkorb_LagerNr` FOREIGN KEY (`LagerNr`) REFERENCES `lager` (`LagerNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `warenkorb_SKUNr` FOREIGN KEY (`SKUNr`) REFERENCES `sku` (`SKUNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `warenkorb_ServicepartnerNr` FOREIGN KEY (`ServicepartnerNr`) REFERENCES `servicepartner` (`ServicepartnerNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.warenkorb: ~0 rows (ungefähr)
DELETE FROM `warenkorb`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
