-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 14 dec 2016 om 14:00
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
-- Tabelstructuur voor tabel `bericht`
--

CREATE TABLE IF NOT EXISTS `bericht` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `van` varchar(45) NOT NULL,
  `naar` varchar(45) NOT NULL,
  `datum` varchar(45) NOT NULL,
  `tijd` varchar(10) NOT NULL,
  `bericht` varchar(500) NOT NULL,
  `gelezen` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `van` (`van`),
  KEY `naar` (`naar`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Gegevens worden uitgevoerd voor tabel `bericht`
--

INSERT INTO `bericht` (`id`, `van`, `naar`, `datum`, `tijd`, `bericht`, `gelezen`) VALUES
(2, 'admin', 'stagair', '12-12-2016', '15:01', 'Timo ga eens koffie halen!', 1),
(3, 'stagair', 'admin', '12-12-2016', '15:02', 'Oke het is mooi weer en ik ga het niet doen hoor het is veels te warm ik ga dit allemaal niet doen ga zelf lopen!!!', 1),
(4, 'admin', 'jandevries', '12-12-2016', '15:17', '"><script>alert("Hoi XSS");</script>', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `categorie`
--

CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(45) NOT NULL,
  `omschrijving` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Gegevens worden uitgevoerd voor tabel `categorie`
--

INSERT INTO `categorie` (`id`, `naam`, `omschrijving`) VALUES
(1, 'Muizen', 'Allerlei muizen, van groot tot klein.'),
(2, 'Opladers', 'Allerlei opladers');

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
('klanttoevoegen', 2),
('klantverwijderen', 2),
('reparatieverwijderen', 2),
('berichtversturen', 3),
('klantbewerken', 3),
('leveranciersbeheren', 3),
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
  `wachtwoord` varchar(256) NOT NULL,
  `rol` int(11) NOT NULL,
  `voornaam` varchar(50) NOT NULL,
  `achternaam` varchar(50) NOT NULL,
  `inactief` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gebruikersnaam`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `gebruiker`
--

INSERT INTO `gebruiker` (`gebruikersnaam`, `wachtwoord`, `rol`, `voornaam`, `achternaam`, `inactief`) VALUES
('admin', '6362c15037002ceda59ef96f14ed43079ddd1817b93f53e9d7b30f2f64241d87', 1, 'Gerrit-Jan', 'Jansen', 0),
('getjee', 'c3299c68f57cf3253389f1fb46e38cec6c130517ead989f299367bdc111c715e', 3, 'lol', 'lol', 1),
('jandejong', 'cedc9b033d0c775ce4c3211f1bb3b5c45b1ab349a4a10b3068a8dd26dec37f84', 3, 'jan', 'dejong', 1),
('jandevries', 'dc00c903852bb19eb250aeba05e534a6d211629d77d055033806b783bae09937', 3, 'Jan', 'de Vries', 1),
('medewerker', '5e3e031666656a0d83d40aaf10a59e02c3e6077480e1ca13e5017633dc5b92b5', 2, 'Getje', 'Fraai', 0),
('stagair', '60892fc096014f61af05fd69c5e9bad0503eaf63a97b3127219f2cead83dcdd5', 3, 'Timo', 'de Boer', 0);

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
  `inactief` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Gegevens worden uitgevoerd voor tabel `klant`
--

INSERT INTO `klant` (`id`, `voornaam`, `achternaam`, `adres`, `woonplaats`, `postcode`, `email`, `telefoonnummer`, `inactief`) VALUES
(5, 'Jan', 'Veenhuis', 'Langejanstraat 90b', 'Nijverdal', '8491LX', 'janveenhuis@gmail.com', '06-29412291', 0),
(6, 'Gerrit', 'van Tafel', 'Tafelstraat 93a', 'Almelo', '9401LD', 'gerritjanjansen@live.nl', '06-50192201', 0),
(7, 'Geert', 'de Haas', 'hazenweg 3', 'Arnhem', '2918LD', 'geert@gmail.com', '06-20491229', 1),
(8, 'test', 'test', 'test', 'test', 'test', 'test', 'test', 1),
(9, 'test', 'test', 'test', 'test', 'test', 'test', 'test', 1),
(10, 'Test', 'test', 'test', 'test', 'test', 'teset@test.nl', 'test', 0),
(11, 'test', 'tst', 'test', 'test', 'test', 'test@testt.nl', 'test', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `leverancier`
--

CREATE TABLE IF NOT EXISTS `leverancier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(45) NOT NULL,
  `adres` varchar(45) NOT NULL,
  `postcode` varchar(45) NOT NULL,
  `vestigingsplaats` varchar(45) NOT NULL,
  `telefoonnummer` varchar(45) NOT NULL,
  `inactief` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `leverancier`
--

INSERT INTO `leverancier` (`id`, `naam`, `adres`, `postcode`, `vestigingsplaats`, `telefoonnummer`, `inactief`) VALUES
(1, 'Groothandel Jansen', 'Druivenlaan 39AB', '2941LD', 'Almere', '0548-20149112', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leverancierid` int(11) NOT NULL,
  `categorieid` int(11) NOT NULL,
  `productnummer` varchar(45) NOT NULL,
  `naam` varchar(45) NOT NULL,
  `omschrijving` varchar(500) NOT NULL,
  `prijs` decimal(4,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `leverancierid` (`leverancierid`),
  KEY `categorieid` (`categorieid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Gegevens worden uitgevoerd voor tabel `product`
--

INSERT INTO `product` (`id`, `leverancierid`, `categorieid`, `productnummer`, `naam`, `omschrijving`, `prijs`) VALUES
(1, 1, 1, '9123123', 'Gamemuis Rood L', 'Dikke muis', '84.00');

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
  `kosten` decimal(4,2) DEFAULT NULL,
  `garantie` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `emailverstuurd` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `klantid` (`klantid`),
  KEY `klantid_2` (`klantid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

--
-- Gegevens worden uitgevoerd voor tabel `reparatie`
--

INSERT INTO `reparatie` (`id`, `klantid`, `medewerker`, `serienummer`, `startdatum`, `omschrijving`, `kosten`, `garantie`, `status`, `emailverstuurd`) VALUES
(38, 6, 'admin', '5012931', '22-11-2016', 'lievv', '0.00', 0, 2, 1),
(40, 5, 'admin', '5012931', '22-11-2016', 'kaasje', '0.00', 0, 2, 0),
(41, 5, 'admin', '5012931', '22-11-2016', 'Laptop van vrouw', '0.00', 0, 2, 1),
(42, 7, 'admin', '102380128312', '25-11-2016', 'laptop doet helemaal niets meer, waarschijnlijk kortsluiting.', '0.00', 0, 1, 0),
(44, 5, 'admin', '89789764009', '29-11-2016', 'De laptop is erg traag en er zit een barst in het scherm..', '0.00', 0, 2, 1),
(45, 5, 'admin', '23123122', '01-12-2016', 'Laptop wordt erg warm, waarschijnlijk helemaal kapot', '75.00', 0, 2, 1),
(46, 6, 'admin', '0410214412', '05-12-2016', 'Laptop is erg traag', '0.00', 0, 2, 1),
(47, 6, 'admin', '13401404130', '05-12-2016', 'De laptop is erg traag', '0.00', 0, 2, 1),
(48, 6, 'admin', '1241324413', '05-12-2016', 'Een test', '0.00', 0, 2, 1),
(49, 6, 'admin', '1412412', '07-12-2016', 'Test email', '0.00', 0, 2, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=149 ;

--
-- Gegevens worden uitgevoerd voor tabel `updates`
--

INSERT INTO `updates` (`id`, `reparatieid`, `medewerker`, `datum`, `tijd`, `omschrijving`, `verwijderbaar`) VALUES
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
(107, 42, 'admin', '25-11-2016', '10:11', 'Reparatie toegevoegd', 0),
(108, 42, 'admin', '25-11-2016', '12:11', 'Status: Wordt aan gewerkt', 0),
(110, 42, 'admin', '25-11-2016', '12:11', 'Status: Open', 0),
(112, 42, 'admin', '26-11-2016', '10:11', 'Status: Wordt aan gewerkt', 0),
(113, 42, 'stagair', '26-11-2016', '08:11', 'jahoor', 1),
(118, 44, 'admin', '29-11-2016', '12:52', 'Reparatie toegevoegd', 0),
(119, 44, 'admin', '29-11-2016', '12:53', 'Ineens doet de laptop helemaal niets meer, waarschijnlijk een probleem met windows.', 1),
(121, 44, 'admin', '29-11-2016', '12:54', 'Status: Wordt aan gewerkt', 0),
(122, 45, 'admin', '01-12-2016', '12:06', 'Reparatie toegevoegd', 0),
(123, 45, 'admin', '01-12-2016', '12:06', 'Een barst in het scherm, niet mijn schuld.', 1),
(124, 45, 'jandevries', '01-12-2016', '12:07', 'De barst in het scherm is mijn schuld', 1),
(125, 45, 'admin', '01-12-2016', '12:12', 'Oke', 1),
(126, 45, 'admin', '03-12-2016', '19:48', 'Status: Afgerond', 0),
(127, 45, 'admin', '03-12-2016', '19:50', 'Status: Afgerond', 0),
(128, 45, 'admin', '03-12-2016', '19:52', 'Status: Afgerond', 0),
(129, 45, 'admin', '03-12-2016', '20:03', 'Status: Afgerond', 0),
(130, 45, 'admin', '03-12-2016', '20:08', 'Status: Afgerond', 0),
(131, 45, 'admin', '03-12-2016', '20:22', 'Status: Afgerond', 0),
(132, 45, 'admin', '03-12-2016', '20:24', 'Status: Afgerond', 0),
(133, 45, 'admin', '03-12-2016', '20:29', 'Status: Afgerond', 0),
(134, 45, 'admin', '03-12-2016', '20:30', 'Status: Afgerond', 0),
(135, 46, 'admin', '05-12-2016', '13:03', 'Reparatie toegevoegd', 0),
(136, 44, 'admin', '05-12-2016', '13:12', 'Status: Afgerond', 0),
(137, 44, 'admin', '05-12-2016', '13:12', 'Status: Afgerond', 0),
(138, 47, 'admin', '05-12-2016', '13:13', 'Reparatie toegevoegd', 0),
(139, 47, 'admin', '05-12-2016', '13:13', 'Status: Afgerond', 0),
(140, 45, 'admin', '05-12-2016', '13:14', 'Status: Afgerond', 0),
(141, 48, 'admin', '05-12-2016', '13:17', 'Reparatie toegevoegd', 0),
(142, 48, 'admin', '05-12-2016', '13:18', 'Status: Afgerond', 0),
(143, 49, 'admin', '07-12-2016', '12:22', 'Reparatie toegevoegd', 0),
(144, 49, 'admin', '07-12-2016', '12:22', 'Status: Afgerond', 0);

--
-- Beperkingen voor gedumpte tabellen
--

--
-- Beperkingen voor tabel `bericht`
--
ALTER TABLE `bericht`
  ADD CONSTRAINT `bericht_ibfk_1` FOREIGN KEY (`van`) REFERENCES `gebruiker` (`gebruikersnaam`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bericht_ibfk_2` FOREIGN KEY (`naar`) REFERENCES `gebruiker` (`gebruikersnaam`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`leverancierid`) REFERENCES `leverancier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`categorieid`) REFERENCES `categorie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `reparatie`
--
ALTER TABLE `reparatie`
  ADD CONSTRAINT `reparatie_ibfk_1` FOREIGN KEY (`klantid`) REFERENCES `klant` (`id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `updates`
--
ALTER TABLE `updates`
  ADD CONSTRAINT `updates_ibfk_1` FOREIGN KEY (`reparatieid`) REFERENCES `reparatie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
