-- --------------------------------------------------------
-- Host:                         localhost
-- Server-Version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server-Betriebssystem:        Win64
-- HeidiSQL Version:             12.7.0.6850
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
DROP DATABASE IF EXISTS `airlimited`;
CREATE DATABASE IF NOT EXISTS `airlimited` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;
USE `airlimited`;

-- Exportiere Struktur von Tabelle airlimited.auftrag
DROP TABLE IF EXISTS `auftrag`;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.auftrag: ~3 rows (ungefähr)
DELETE FROM `auftrag`;
INSERT INTO `auftrag` (`AuftragsNr`, `Auftragsdatum`, `Status`, `Enddatum`, `FertigungsNr`, `SKUNr`, `Reihenfolge`) VALUES
	(1, '2024-05-05 17:41:26', 'In Auftrag', NULL, 1, 13, 1),
	(2, '2024-05-06 17:41:57', 'In Bearbeitung', NULL, 2, 10, 2),
	(3, '2024-05-04 17:44:20', 'Fertig', '2024-05-05 17:44:28', 1, 43, 3);

-- Exportiere Struktur von Tabelle airlimited.bestellposten
DROP TABLE IF EXISTS `bestellposten`;
CREATE TABLE IF NOT EXISTS `bestellposten` (
  `BestellNr` int(11) NOT NULL,
  `BestellpostenNr` int(11) NOT NULL AUTO_INCREMENT,
  `Quantität` int(11) NOT NULL,
  `SKUNr` int(11) NOT NULL,
  PRIMARY KEY (`BestellpostenNr`,`BestellNr`) USING BTREE,
  KEY `SKUNr` (`SKUNr`),
  KEY `bestellposten_BestellNr` (`BestellNr`),
  CONSTRAINT `SKUNr` FOREIGN KEY (`SKUNr`) REFERENCES `sku` (`SKUNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `bestellposten_BestellNr` FOREIGN KEY (`BestellNr`) REFERENCES `bestellung` (`BestellNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.bestellposten: ~18 rows (ungefähr)
DELETE FROM `bestellposten`;
INSERT INTO `bestellposten` (`BestellNr`, `BestellpostenNr`, `Quantität`, `SKUNr`) VALUES
	(1, 17, 8, 31),
	(1, 18, 4, 41),
	(44, 19, 2, 1),
	(44, 20, 2, 6),
	(45, 21, 2, 1),
	(45, 22, 2, 6),
	(46, 23, 2, 1),
	(46, 24, 2, 6),
	(47, 25, 2, 1),
	(47, 26, 2, 6),
	(48, 27, 2, 1),
	(48, 28, 2, 6),
	(49, 29, 1, 6),
	(50, 30, 4, 4),
	(50, 31, 1, 5),
	(50, 32, 1, 7),
	(50, 33, 2, 10),
	(50, 34, 2, 18);

-- Exportiere Struktur von Tabelle airlimited.bestellung
DROP TABLE IF EXISTS `bestellung`;
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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.bestellung: ~9 rows (ungefähr)
DELETE FROM `bestellung`;
INSERT INTO `bestellung` (`BestellNr`, `Bestelldatum`, `ServicepartnerNr`, `LagerNr`) VALUES
	(1, '2024-05-05 17:33:07', 1, NULL),
	(2, '2024-05-05 20:03:31', NULL, 1),
	(3, '2024-05-09 16:29:36', 1, NULL),
	(4, '2024-05-26 10:28:38', 10, NULL),
	(43, '2024-05-26 10:30:06', NULL, 3),
	(44, '2024-05-26 10:31:11', 7, NULL),
	(45, '2024-05-26 10:36:15', 7, NULL),
	(46, '2024-05-26 10:36:34', 7, NULL),
	(47, '2024-05-26 10:51:03', 7, NULL),
	(48, '2024-05-26 10:51:17', 7, NULL),
	(49, '2024-05-26 11:01:00', 7, NULL),
	(50, '2024-05-26 12:40:00', 1, NULL);

-- Exportiere Struktur von Tabelle airlimited.fertigung
DROP TABLE IF EXISTS `fertigung`;
CREATE TABLE IF NOT EXISTS `fertigung` (
  `FertigungsNr` int(11) NOT NULL AUTO_INCREMENT,
  `Straße` varchar(50) DEFAULT NULL,
  `HausNr` int(11) DEFAULT NULL,
  `PLZ` varchar(50) DEFAULT NULL,
  `Stadt` varchar(50) DEFAULT NULL,
  `Land` varchar(50) DEFAULT NULL,
  `TelefonNr` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`FertigungsNr`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.fertigung: ~10 rows (ungefähr)
DELETE FROM `fertigung`;
INSERT INTO `fertigung` (`FertigungsNr`, `Straße`, `HausNr`, `PLZ`, `Stadt`, `Land`, `TelefonNr`) VALUES
	(1, 'Industriestraße', 20, '12345', 'Berlin', 'Deutschland', '+49 30 7654321'),
	(2, 'Fabrikweg', 15, '54321', 'München', 'Deutschland', '+49 89 9876543'),
	(3, 'Produktionsstraße', 30, '67890', 'Rom', 'Italien', '+39 06 23456789'),
	(4, 'Maschinenweg', 25, '13579', 'Barcelona', 'Spanien', '+34 93 357911'),
	(5, 'Herstellungsweg', 10, '98765', 'Mailand', 'Italien', '+39 02 246810'),
	(6, 'Fertigungsallee', 5, '24680', 'Lissabon', 'Portugal', '+351 21 8765432'),
	(7, 'Fabrikstraße', 8, '56789', 'Prag', 'Tschechien', '+420 221 9876543'),
	(8, 'Produktionsweg', 12, '78901', 'Warschau', 'Polen', '+48 22 6543210'),
	(9, 'Montageweg', 18, '23456', 'Budapest', 'Ungarn', '+36 1 1357924'),
	(10, 'Herstellungsstraße', 6, '34567', 'Kopenhagen', 'Dänemark', '+45 1234 5678');

-- Exportiere Struktur von Tabelle airlimited.gehoert_zu
DROP TABLE IF EXISTS `gehoert_zu`;
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

-- Exportiere Daten aus Tabelle airlimited.gehoert_zu: ~4 rows (ungefähr)
DELETE FROM `gehoert_zu`;
INSERT INTO `gehoert_zu` (`AuftragsNr`, `BestellNr`, `Quantitaet`, `Versandt`) VALUES
	(1, 1, 20, 'Nein'),
	(1, 2, 5, 'Nein'),
	(2, 1, 10, 'Nein'),
	(2, 3, 5, 'Nein');

-- Exportiere Struktur von Tabelle airlimited.lager
DROP TABLE IF EXISTS `lager`;
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
  PRIMARY KEY (`LagerNr`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.lager: ~10 rows (ungefähr)
DELETE FROM `lager`;
INSERT INTO `lager` (`LagerNr`, `Lagerstandort`, `Straße`, `HausNr`, `PLZ`, `Land`, `Verantwortlicher Vormane`, `Verantwortlicher Nachname`, `TelefonNr`) VALUES
	(1, 'Berlin', 'Industriestraße', 10, '12345', 'Deutschland', 'Max', 'Schulze', '+49 30 1234567'),
	(2, 'Hamburg', 'Hauptstraße', 25, '54321', 'Deutschland', 'Julia', 'Becker', '+49 40 9876543'),
	(3, 'Paris', 'Luftweg', 5, '67890', 'Frankreich', 'Sophie', 'Leclerc', '+33 1 23456789'),
	(4, 'Frankfurt', 'Kühlstraße', 15, '13579', 'Deutschland', 'Felix', 'Hoffmann', '+49 69 357911'),
	(5, 'Stuttgart', 'Windweg', 8, '98765', 'Deutschland', 'Nina', 'Wagner', '+49 711 135790'),
	(6, 'Madrid', 'Luftallee', 20, '24680', 'Spanien', 'Carlos', 'García', '+34 91 2345678'),
	(7, 'Köln', 'Briseallee', 12, '56789', 'Deutschland', 'Lena', 'Schneider', '+49 221 901234'),
	(8, 'Dresden', 'Frischweg', 30, '78901', 'Deutschland', 'Tom', 'Mayer', '+49 351 246801'),
	(9, 'London', 'Klimaweg', 18, '23456', 'England', 'Olivia', 'Taylor', '+44 20 3456789'),
	(10, 'Leipzig', 'Luftmeisterstraße', 6, '34567', 'Deutschland', 'Finn', 'Schulz', '+49 341 135792');

-- Exportiere Struktur von Tabelle airlimited.servicepartner
DROP TABLE IF EXISTS `servicepartner`;
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
  PRIMARY KEY (`ServicepartnerNr`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.servicepartner: ~10 rows (ungefähr)
DELETE FROM `servicepartner`;
INSERT INTO `servicepartner` (`ServicepartnerNr`, `Firmenname`, `Nachname Kontaktperson`, `Vorname Kontaktperson`, `Straße`, `HausNr`, `Stadt`, `PLZ`, `Land`, `TelefonNr`, `E-Mail`, `VIPKunde`) VALUES
	(1, 'Ventilator-Expert GmbH', 'Müller', 'Klaus', 'Industriestraße', 10, 'Berlin', '12345', 'Deutschland', '+49 30 1234567', 'klaus.mueller@ventilator-expert.de', 'Ja'),
	(2, 'Luftstrom AG', 'Schneider', 'Sabine', 'Hauptstraße', 25, 'Hamburg', '54321', 'Deutschland', '+49 40 9876543', 'sabine.schneider@luftstrom-ag.de', 'Nein'),
	(3, 'AirTech Solutions GmbH', 'Meier', 'Michael', 'Luftweg', 5, 'München', '67890', 'Deutschland', '+49 89 246813', 'michael.meier@airtech-solutions.de', 'Nein'),
	(4, 'Cooling Systems GmbH', 'Schulz', 'Andreas', 'Kühlstraße', 15, 'Frankfurt', '13579', 'Deutschland', '+49 69 357911', 'andreas.schulz@coolingsystems.de', 'Nein'),
	(5, 'Ventilation Services Ltd.', 'Hoffmann', 'Anna', 'Windweg', 8, 'Stuttgart', '98765', 'Deutschland', '+49 711 135790', 'anna.hoffmann@ventilationservices.com', 'Ja'),
	(6, 'AirFlow Experts GmbH', 'Wagner', 'Peter', 'Luftallee', 20, 'Düsseldorf', '24680', 'Deutschland', '+49 211 468013', 'peter.wagner@airflow-experts.de', 'Nein'),
	(7, 'Breeze Solutions GmbH', 'Becker', 'Sandra', 'Briseallee', 12, 'Köln', '56789', 'Deutschland', '+49 221 901234', 'sandra.becker@breeze-solutions.de', 'Nein'),
	(8, 'FreshAir GmbH', 'Zimmermann', 'Thomas', 'Frischweg', 30, 'Dresden', '78901', 'Deutschland', '+49 351 246801', 'thomas.zimmermann@freshair.de', 'Ja'),
	(9, 'Climate Control AG', 'Hahn', 'Markus', 'Klimaweg', 18, 'Hannover', '23456', 'Deutschland', '+49 511 679013', 'markus.hahn@climatecontrol-ag.de', 'Nein'),
	(10, 'AirMaster Solutions GmbH', 'Fischer', 'Laura', 'Luftmeisterstraße', 6, 'Leipzig', '34567', 'Deutschland', '+49 341 135792', 'laura.fischer@airmastersolutions.de', 'Nein');

-- Exportiere Struktur von Tabelle airlimited.sind_in
DROP TABLE IF EXISTS `sind_in`;
CREATE TABLE IF NOT EXISTS `sind_in` (
  `LagerNr` int(11) NOT NULL,
  `SKUNr` int(11) NOT NULL,
  `Bestand` int(11) DEFAULT NULL,
  PRIMARY KEY (`LagerNr`,`SKUNr`),
  KEY `sind_in SKUNr` (`SKUNr`),
  CONSTRAINT `sind_in LagerNr` FOREIGN KEY (`LagerNr`) REFERENCES `lager` (`LagerNr`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `sind_in SKUNr` FOREIGN KEY (`SKUNr`) REFERENCES `sku` (`SKUNr`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.sind_in: ~3 rows (ungefähr)
DELETE FROM `sind_in`;
INSERT INTO `sind_in` (`LagerNr`, `SKUNr`, `Bestand`) VALUES
	(1, 13, 50),
	(1, 43, 100),
	(2, 10, 10);

-- Exportiere Struktur von Tabelle airlimited.sku
DROP TABLE IF EXISTS `sku`;
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
  `Verfuegbarkeit` varchar(50) DEFAULT NULL,
  `Fertigungsanweisungen` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`SKUNr`)
) ENGINE=InnoDB AUTO_INCREMENT=2051 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.sku: ~60 rows (ungefähr)
DELETE FROM `sku`;
INSERT INTO `sku` (`SKUNr`, `Name`, `Standardlosgroeße`, `Laenge`, `Breite`, `Hoehe`, `Gewicht`, `Beschreibung`, `Preis`, `Foto`, `Art`, `Verfuegbarkeit`, `Fertigungsanweisungen`) VALUES
	(1, 'Lüfterblatt', 20, 30, 30, 5, 0.5, 'Hochwertiges Lüfterblatt für Industrieanlagen', 25.99, 'lüfterblatt.jpg', 'Komplex', 'ja', 'lüfterblatt_anweisungen.pdf'),
	(2, 'Lager', 50, 10, 10, 5, 0.1, 'Hochleistungslager für Ventilatoren', 8.75, 'lager.jpg', 'Einfach', 'Auf Anfrage', 'lager_anweisungen.pdf'),
	(3, 'Flügelrad', 30, 25, 25, 7, 0.7, 'Leichtes und effizientes Flügelrad', 39.99, 'fluegelrad.jpg', 'Komplex', 'In 2 Wochen lieferbar', 'fluegelrad_anweisungen.pdf'),
	(4, 'Motor', 100, 20, 15, 10, 2.5, 'Hochleistungsmotor für industrielle Ventilatoren', 149.99, 'motor.jpg', 'Komplex', 'ja', 'motor_anweisungen.pdf'),
	(5, 'Stator', 80, 30, 30, 20, 5, 'Robuster Stator für große Ventilatoren', 199.99, 'stator.jpg', 'Komplex', 'In 4 Wochen lieferbar', 'stator_anweisungen.pdf'),
	(6, 'Gehäuse', 150, 40, 40, 30, 8, 'Stabiles Gehäuse für Ventilatorbaugruppen', 299.99, 'gehaeuse.jpg', 'Komplex', 'ja', 'gehaeuse_anweisungen.pdf'),
	(7, 'Rotor', 40, 25, 25, 10, 1.5, 'Präzisionsrotor für effiziente Luftströmung', 69.99, 'rotor.jpg', 'Komplex', 'ja', 'rotor_anweisungen.pdf'),
	(8, 'Schraube', 10, 5, 5, 2, 0.05, 'Hochfeste Befestigungsschraube', 0.99, 'schraube.jpg', 'Einfach', 'ja', 'schraube_anweisungen.pdf'),
	(9, 'Schalldämpfer', 70, 30, 20, 15, 3, 'Effektiver Schalldämpfer für geräuscharmen Betrieb', 49.99, 'schalldaempfer.jpg', 'Komplex', 'In 3 Wochen lieferbar', 'schalldaempfer_anweisungen.pdf'),
	(10, 'Blendenkappe', 20, 10, 10, 5, 0.2, 'Kappen für Lüftungsöffnungen', 4.99, 'blendenkappe.jpg', 'Einfach', 'ja', 'blendenkappe_anweisungen.pdf'),
	(11, 'Steuerungseinheit', 120, 15, 10, 5, 1, 'Intelligente Steuerung für Ventilatoren', 129.99, 'steuerung.jpg', 'Komplex', 'In 6 Wochen lieferbar', 'steuerung_anweisungen.pdf'),
	(12, 'Ausgleichsgewicht', 20, 5, 5, 5, 0.3, 'Gewichte für Balancierung von Ventilatorflügeln', 9.99, 'ausgleichsgewicht.jpg', 'Einfach', 'ja', 'ausgleichsgewicht_anweisungen.pdf'),
	(13, 'Antriebsriemen', 60, 50, 5, 5, 0.5, 'Robuste Riemen für Motorantriebe', 19.99, 'antriebsriemen.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'antriebsriemen_anweisungen.pdf'),
	(14, 'Luftleitschaufel', 25, 20, 15, 5, 0.3, 'Präzise gefertigte Luftleitschaufel für optimale Luftströmung', 12.99, 'luftleitschaufel.jpg', 'Komplex', 'In 3 Wochen lieferbar', 'luftleitschaufel_anweisungen.pdf'),
	(15, 'Dichtungssatz', 40, 10, 10, 2, 0.1, 'Hochwertiger Dichtungssatz für Ventilatorbaugruppen', 6.99, 'dichtungssatz.jpg', 'Einfach', 'ja', 'dichtungssatz_anweisungen.pdf'),
	(16, 'Ventilatorflügel', 35, 30, 30, 8, 0.6, 'Leistungsstarke Ventilatorflügel für industrielle Anwendungen', 29.99, 'ventilatorfluegel.jpg', 'Komplex', 'In 4 Wochen lieferbar', 'ventilatorfluegel_anweisungen.pdf'),
	(17, 'Achse', 60, 20, 20, 20, 1, 'Stabile Achse für Ventilatoren', 19.99, 'achse.jpg', 'Einfach', 'ja', 'achse_anweisungen.pdf'),
	(18, 'Drehzahlregler', 80, 10, 5, 2, 0.3, 'Präziser Drehzahlregler für Ventilatoren', 49.99, 'drehzahlregler.jpg', 'Komplex', 'In 2 Wochen lieferbar', 'drehzahlregler_anweisungen.pdf'),
	(19, 'Sicherungsschalter', 30, 5, 5, 2, 0.1, 'Zuverlässiger Sicherungsschalter für Ventilatoren', 7.99, 'sicherungsschalter.jpg', 'Einfach', 'ja', 'sicherungsschalter_anweisungen.pdf'),
	(20, 'Filterelement', 45, 15, 15, 10, 0.5, 'Effektives Filterelement für Luftreinigung', 14.99, 'filterelement.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'filterelement_anweisungen.pdf'),
	(21, 'Schaltkreisplatine', 90, 10, 10, 2, 0.2, 'Hochwertige Schaltkreisplatine für Ventilatorsteuerungen', 79.99, 'schaltkreisplatine.jpg', 'Komplex', 'ja', 'schaltkreisplatine_anweisungen.pdf'),
	(22, 'Befestigungsklammer', 15, 5, 5, 3, 0.05, 'Robuste Befestigungsklammer für Ventilatorkomponenten', 1.99, 'befestigungsklammer.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'befestigungsklammer_anweisungen.pdf'),
	(23, 'Gummifüße', 10, 5, 5, 2, 0.05, 'Hochwertige Gummifüße für Vibrationsdämpfung', 3.99, 'gummifuesse.jpg', 'Einfach', 'ja', 'gummifuesse_anweisungen.pdf'),
	(24, 'Ventilatorblende', 25, 10, 10, 3, 0.3, 'Stabile Blenden für Ventilatorgehäuse', 9.99, 'ventilatorblende.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'ventilatorblende_anweisungen.pdf'),
	(25, 'Spannungsregler', 70, 10, 5, 2, 0.2, 'Präziser Spannungsregler für Ventilatorantriebe', 39.99, 'spannungsregler.jpg', 'Komplex', 'ja', 'spannungsregler_anweisungen.pdf'),
	(26, 'Austrittsgitter', 20, 20, 20, 3, 0.2, 'Hochwertiges Austrittsgitter für Ventilatoren', 8.99, 'austrittsgitter.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'austrittsgitter_anweisungen.pdf'),
	(27, 'Luftführungskanal', 60, 40, 20, 10, 1.5, 'Effizienter Luftführungskanal für Ventilatoranlagen', 24.99, 'luftfuehrungskanal.jpg', 'Komplex', 'ja', 'luftfuehrungskanal_anweisungen.pdf'),
	(28, 'Schnellverschlusskup', 15, 5, 5, 2, 0.1, 'Zuverlässige Schnellverschlusskupplungen für Ventilatorschläuche', 5.99, 'schnellverschlusskupplung.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'schnellverschlusskupplung_anweisungen.pdf'),
	(29, 'Schmiermittel', 10, 5, 5, 5, 0.2, 'Hochwertiges Schmiermittel für Ventilatorlager', 4.99, 'schmiermittel.jpg', 'Einfach', 'ja', 'schmiermittel_anweisungen.pdf'),
	(30, 'Montagehalterung', 25, 10, 10, 5, 0.3, 'Robuste Montagehalterungen für Ventilatorinstallationen', 7.99, 'montagehalterung.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'montagehalterung_anweisungen.pdf'),
	(31, 'Ventilatorsteuerpult', 100, 30, 20, 10, 2, 'Modernes Steuerpult für industrielle Ventilatoren', 99.99, 'steuerpult.jpg', 'Komplex', 'In 4 Wochen lieferbar', 'steuerpult_anweisungen.pdf'),
	(32, 'Lagerbock', 30, 15, 15, 10, 0.5, 'Stabiler Lagerbock für Ventilatormontage', 12.99, 'lagerbock.jpg', 'Einfach', 'ja', 'lagerbock_anweisungen.pdf'),
	(33, 'Ablaufleitung', 40, 20, 20, 5, 0.3, 'Effiziente Ablaufleitungen für Kondenswasserabfluss', 9.99, 'ablaufleitung.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'ablaufleitung_anweisungen.pdf'),
	(34, 'Motorschutzschalter', 50, 5, 5, 3, 0.2, 'Zuverlässiger Motorschutzschalter für Ventilatoren', 14.99, 'motorschutzschalter.jpg', 'Einfach', 'ja', 'motorschutzschalter_anweisungen.pdf'),
	(35, 'Kühlgebläse', 70, 20, 20, 10, 1, 'Effektives Kühlgebläse für Ventilatorantriebe', 29.99, 'kuehlgeblaese.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'kuehlgeblaese_anweisungen.pdf'),
	(36, 'Wandhalterung', 20, 10, 10, 5, 0.5, 'Stabile Wandhalterungen für Ventilatoren', 6.99, 'wandhalterung.jpg', 'Einfach', 'ja', 'wandhalterung_anweisungen.pdf'),
	(37, 'Schwingungsdämpfer', 15, 5, 5, 3, 0.1, 'Effektive Schwingungsdämpfer für ruhigen Betrieb', 3.99, 'schwingungsdaempfer.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'schwingungsdaempfer_anweisungen.pdf'),
	(38, 'Drucksensor', 80, 5, 5, 5, 0.3, 'Präziser Drucksensor für Ventilatorüberwachung', 49.99, 'drucksensor.jpg', 'Komplex', 'ja', 'drucksensor_anweisungen.pdf'),
	(39, 'Isoliermatte', 30, 30, 30, 5, 0.5, 'Effektive Isoliermatten für Schalldämpfung', 9.99, 'isoliermatte.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'isoliermatte_anweisungen.pdf'),
	(40, 'Hitzeschild', 40, 20, 20, 5, 0.3, 'Robustes Hitzeschild für Ventilatoranlagen', 14.99, 'hitzeschild.jpg', 'Einfach', 'ja', 'hitzeschild_anweisungen.pdf'),
	(41, 'Drehflügel', 25, 20, 20, 5, 0.4, 'Präzise gefertigte Drehflügel für effiziente Luftzirkulation', 18.99, 'drehfluegel.jpg', 'Komplex', 'In 3 Wochen lieferbar', 'drehfluegel_anweisungen.pdf'),
	(42, 'Stellmotor', 50, 10, 10, 5, 0.5, 'Zuverlässiger Stellmotor für Ventilatorsteuerung', 29.99, 'stellmotor.jpg', 'Komplex', 'ja', 'stellmotor_anweisungen.pdf'),
	(43, 'Dichtungsband', 15, 5, 5, 2, 0.1, 'Hochwertiges Dichtungsband für Luftabdichtung', 3.99, 'dichtungsband.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'dichtungsband_anweisungen.pdf'),
	(44, 'Kondensatablauf', 30, 20, 20, 10, 0.6, 'Effizienter Kondensatablauf für Ventilatoranlagen', 11.99, 'kondensatablauf.jpg', 'Einfach', 'ja', 'kondensatablauf_anweisungen.pdf'),
	(45, 'Saugrohr', 35, 40, 20, 15, 1.2, 'Stabiles Saugrohr für Luftansaugung', 17.99, 'saugrohr.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'saugrohr_anweisungen.pdf'),
	(46, 'Kugellager', 20, 5, 5, 5, 0.2, 'Hochwertige Kugellager für Ventilatoren', 5.99, 'kugellager.jpg', 'Einfach', 'ja', 'kugellager_anweisungen.pdf'),
	(47, 'Schnellkupplung', 15, 5, 5, 3, 0.1, 'Effektive Schnellkupplungen für Ventilatorleitungen', 4.99, 'schnellkupplung.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'schnellkupplung_anweisungen.pdf'),
	(48, 'Vibrationsdämpfer', 20, 5, 5, 2, 0.2, 'Robuste Vibrationsdämpfer für geräuscharmen Betrieb', 6.99, 'vibrationsdaempfer.jpg', 'Einfach', 'ja', 'vibrationsdaempfer_anweisungen.pdf'),
	(49, 'Hochleistungsschalte', 40, 5, 5, 3, 0.2, 'Zuverlässiger Hochleistungsschalter für Ventilatoren', 12.99, 'hochleistungsschalter.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'hochleistungsschalter_anweisungen.pdf'),
	(50, 'Klemmverbinder', 10, 5, 5, 2, 0.1, 'Stabile Klemmverbinder für Ventilatorleitungen', 3.99, 'klemmverbinder.jpg', 'Einfach', 'ja', 'klemmverbinder_anweisungen.pdf'),
	(51, 'Ventilatorgehäuse', 60, 30, 30, 20, 3.5, 'Robustes Gehäuse für industrielle Ventilatoren', 39.99, 'ventilatorgehaeuse.jpg', 'Komplex', 'In 4 Wochen lieferbar', 'ventilatorgehaeuse_anweisungen.pdf'),
	(52, 'Steuerkabel', 25, 5, 5, 2, 0.1, 'Hochwertige Steuerkabel für Ventilatorsteuerungen', 4.99, 'steuerkabel.jpg', 'Einfach', 'ja', 'steuerkabel_anweisungen.pdf'),
	(53, 'Druckluftschlauch', 30, 10, 10, 5, 0.3, 'Robuster Druckluftschlauch für Ventilatoranwendungen', 8.99, 'druckluftschlauch.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'druckluftschlauch_anweisungen.pdf'),
	(54, 'Gleitlager', 15, 5, 5, 5, 0.1, 'Hochwertige Gleitlager für Ventilatoren', 3.99, 'gleitlager.jpg', 'Einfach', 'ja', 'gleitlager_anweisungen.pdf'),
	(55, 'Ölabscheider', 20, 10, 10, 5, 0.4, 'Effizienter Ölabscheider für Ventilatoren', 9.99, 'oelabscheider.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'oelabscheider_anweisungen.pdf'),
	(56, 'Axiallüfter', 45, 20, 20, 10, 1, 'Leistungsstarker Axiallüfter für industrielle Anwendungen', 34.99, 'axialluefter.jpg', 'Einfach', 'ja', 'axialluefter_anweisungen.pdf'),
	(57, 'Ventilatormesser', 25, 15, 5, 2, 0.2, 'Präzise Ventilatormesser für Leistungsüberwachung', 7.99, 'ventilatormesser.jpg', 'Einfach', 'In 2 Wochen lieferbar', 'ventilatormesser_anweisungen.pdf'),
	(58, 'Heizwiderstand', 40, 5, 5, 2, 0.3, 'Effektiver Heizwiderstand für Ventilatorheizungen', 11.99, 'heizwiderstand.jpg', 'Einfach', 'ja', 'heizwiderstand_anweisungen.pdf'),
	(59, 'Stecker', 10, 5, 5, 2, 0.1, 'Zuverlässige Stecker für Ventilatoranschlüsse', 2.99, 'stecker.jpg', 'Einfach', 'In 3 Wochen lieferbar', 'stecker_anweisungen.pdf'),
	(60, 'Luftdrucksensor', 30, 5, 5, 2, 0.2, 'Präziser Luftdrucksensor für Ventilatorüberwachung', 9.99, 'luftdrucksensor.jpg', 'Einfach', 'ja', 'luftdrucksensor_anweisungen.pdf');

-- Exportiere Struktur von Tabelle airlimited.warenkorb
DROP TABLE IF EXISTS `warenkorb`;
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
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Exportiere Daten aus Tabelle airlimited.warenkorb: ~14 rows (ungefähr)
DELETE FROM `warenkorb`;
INSERT INTO `warenkorb` (`WarenkorbNr`, `ServicepartnerNr`, `LagerNr`, `SKUNr`, `Menge`) VALUES
	(45, 1, NULL, 4, 4),
	(46, 1, NULL, 7, 1),
	(47, 1, NULL, 10, 2),
	(48, 2, NULL, 5, 1),
	(49, 1, NULL, 18, 2),
	(51, NULL, 3, 31, 8),
	(52, 1, NULL, 5, 1),
	(53, 5, NULL, 6, 2),
	(54, 3, NULL, 6, 15),
	(55, 3, NULL, 4, 1),
	(57, NULL, 3, 41, 4),
	(62, 1, NULL, 1, 1),
	(63, 1, NULL, 11, 1),
	(64, 1, NULL, 1, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
