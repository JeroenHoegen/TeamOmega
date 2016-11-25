-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 25 nov 2016 om 10:47
-- Serverversie: 5.6.13
-- PHP-versie: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `omega`
--
CREATE DATABASE IF NOT EXISTS `omega` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `omega`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `functierol`
--

CREATE TABLE IF NOT EXISTS `functierol` (
  `naam` varchar(45) NOT NULL,
  `minimalerol` int(11) NOT NULL,
  PRIMARY KEY (`naam`),
  KEY `minimalerol` (`minimalerol`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `functierol`
--

INSERT INTO `functierol` (`naam`, `minimalerol`) VALUES
('accountsbeheren', 1),
('klantenexporteren', 1),
('klantverwijderen', 2),
('reparatieverwijderen', 2),
('klantbewerken', 3),
('klanttoevoegen', 3),
('overzichtbekijken', 3),
('reparatiebewerken', 3),
('reparatietoevoegen', 3),
('wachtwoordwijzigen', 3);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `gebruiker`
--

CREATE TABLE IF NOT EXISTS `gebruiker` (
  `gebruikersnaam` varchar(30) NOT NULL,
  `wachtwoord` varchar(40) NOT NULL,
  `rol` int(11) NOT NULL,
  `voornaam` varchar(50) NOT NULL,
  `achternaam` varchar(50) NOT NULL,
  PRIMARY KEY (`gebruikersnaam`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `gebruiker`
--

INSERT INTO `gebruiker` (`gebruikersnaam`, `wachtwoord`, `rol`, `voornaam`, `achternaam`) VALUES
('admin', '21232f297a57a5a743894a0e4a801fc3', 1, 'Gerrit-Jan', 'Jansen'),
('medewerker', 'ef44844a10169131c2aaeb85f3de7731', 2, 'Getje', 'Fraai'),
('stagair', 'd211806882c08af324080b6e2f2fbdaa', 3, 'Timo', 'de Boer');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `klant`
--

CREATE TABLE IF NOT EXISTS `klant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(45) NOT NULL,
  `achternaam` varchar(45) NOT NULL,
  `adres` varchar(45) NOT NULL,
  `woonplaats` varchar(45) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `telefoonnummer` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Gegevens worden uitgevoerd voor tabel `klant`
--

INSERT INTO `klant` (`id`, `voornaam`, `achternaam`, `adres`, `woonplaats`, `postcode`, `email`, `telefoonnummer`) VALUES
(5, 'Jan', 'Veenhuis', 'Langejanstraat 90b', 'Nijverdal', '8491LD', 'janveenhuis@gmail.com', '06-29412291'),
(6, 'Gerrit', 'van Tafel', 'Tafelstraat 93a', 'Almelo', '9401LD', 'gerrittafel@gmail.com', '06-50192201'),
(7, 'Geert', 'de Haas', 'hazenweg 3', 'Arnhem', '2918LD', 'geert@gmail.com', '06-20491229');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `reparatie`
--

CREATE TABLE IF NOT EXISTS `reparatie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `klantid` int(11) NOT NULL,
  `medewerker` varchar(50) NOT NULL,
  `serienummer` varchar(50) NOT NULL,
  `startdatum` varchar(50) NOT NULL,
  `omschrijving` varchar(500) NOT NULL,
  `kosten` int(11) DEFAULT NULL,
  `garantie` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `emailverstuurd` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `klantid` (`klantid`),
  KEY `klantid_2` (`klantid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

--
-- Gegevens worden uitgevoerd voor tabel `reparatie`
--

INSERT INTO `reparatie` (`id`, `klantid`, `medewerker`, `serienummer`, `startdatum`, `omschrijving`, `kosten`, `garantie`, `status`, `emailverstuurd`) VALUES
(31, 5, 'admin', '5012931', '21-11-2016', 'laptop warm', 0, 0, 2, 1),
(38, 6, 'admin', '5012931', '22-11-2016', 'lievv', 0, 0, 2, 1),
(40, 5, 'admin', '5012931', '22-11-2016', 'kaasje', 0, 0, 2, 0),
(41, 5, 'admin', '5012931', '22-11-2016', 'Laptop van vrouw', 0, 0, 2, 1),
(42, 7, 'admin', '102380128312', '25-11-2016', 'laptop kapot', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `rol`
--

CREATE TABLE IF NOT EXISTS `rol` (
  `naam` varchar(20) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`naam`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `rol`
--

INSERT INTO `rol` (`naam`, `id`) VALUES
('beheerder', 1),
('medewerker', 2),
('stagair', 3);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `updates`
--

CREATE TABLE IF NOT EXISTS `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reparatieid` int(11) NOT NULL,
  `medewerker` varchar(45) NOT NULL,
  `datum` varchar(45) NOT NULL,
  `tijd` varchar(10) NOT NULL,
  `omschrijving` varchar(500) NOT NULL,
  `verwijderbaar` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reparatieid` (`reparatieid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=109 ;

--
-- Gegevens worden uitgevoerd voor tabel `updates`
--

INSERT INTO `updates` (`id`, `reparatieid`, `medewerker`, `datum`, `tijd`, `omschrijving`, `verwijderbaar`) VALUES
(32, 31, 'admin', '21-11-2016', '09:11', 'Reparatie toegevoegd', 0),
(34, 31, 'admin', '21-11-2016', '09:11', 'Status: Open', 0),
(35, 31, 'admin', '21-11-2016', '09:11', 'Status: Wordt aan gewerkt', 0),
(36, 31, 'admin', '21-11-2016', '09:11', 'Veel werk', 0),
(37, 31, 'admin', '21-11-2016', '09:11', 'Hardeschijf vervangen', 0),
(38, 31, 'admin', '21-11-2016', '09:11', 'CPU probleem', 0),
(39, 31, 'admin', '21-11-2016', '09:11', 'moooi', 0),
(40, 31, 'admin', '21-11-2016', '09:11', 'Status: Afgerond', 0),
(93, 38, 'admin', '22-11-2016', '01:11', 'Reparatie toegevoegd', 0),
(94, 38, 'admin', '22-11-2016', '01:11', 'Status: Afgerond', 0),
(95, 38, 'admin', '22-11-2016', '01:11', 'Status: Afgerond', 0),
(96, 38, 'admin', '22-11-2016', '01:11', 'Status: Afgerond', 0),
(97, 38, 'admin', '22-11-2016', '01:11', 'Status: Afgerond', 0),
(101, 40, 'admin', '22-11-2016', '01:11', 'Reparatie toegevoegd', 0),
(102, 40, 'admin', '22-11-2016', '01:11', 'Status: Afgerond', 0),
(103, 41, 'admin', '22-11-2016', '01:11', 'Reparatie toegevoegd', 0),
(104, 41, 'admin', '22-11-2016', '01:11', 'Scherm kapot', 1),
(105, 41, 'admin', '22-11-2016', '01:11', 'Status: Wordt aan gewerkt', 0),
(106, 41, 'admin', '22-11-2016', '01:11', 'Status: Afgerond', 0),
(107, 42, 'admin', '25-11-2016', '10:11', 'Reparatie toegevoegd', 0);

--
-- Beperkingen voor gedumpte tabellen
--

--
-- Beperkingen voor tabel `reparatie`
--
ALTER TABLE `reparatie`
  ADD CONSTRAINT `reparatie_ibfk_1` FOREIGN KEY (`klantid`) REFERENCES `klant` (`id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `updates`
--
ALTER TABLE `updates`
  ADD CONSTRAINT `updates_ibfk_1` FOREIGN KEY (`reparatieid`) REFERENCES `reparatie` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
