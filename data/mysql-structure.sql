-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 18. Jul 2014 um 21:43
-- Server Version: 5.5.38-0ubuntu0.14.04.1
-- PHP-Version: 5.5.9-1ubuntu4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `zf2-static-page`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `pg_key` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `navtxt` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `pg_key` (`pg_key`),
  KEY `isactive` (`isactive`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `pg_key`, `title`, `navtxt`, `content`, `isactive`) VALUES
(1, 'about', 'About us', 'About us', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages_meta`
--

CREATE TABLE IF NOT EXISTS `pages_meta` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `pgm_pg_id` int(5) NOT NULL,
  `pgm_attr` enum('name','property') NOT NULL,
  `pgm_attr_txt` varchar(255) NOT NULL,
  `pgm_content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pgm_pg_id` (`pgm_pg_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `pages_meta`
--

INSERT INTO `pages_meta` (`id`, `pgm_pg_id`, `pgm_attr`, `pgm_attr_txt`, `pgm_content`) VALUES
(1, 1, 'name', 'keywords', 'foo, bar, bat'),
(2, 1, 'property', 'og:title', 'Title!');

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `pages_meta`
--
ALTER TABLE `pages_meta`
  ADD CONSTRAINT `pages_meta_ibfk_1` FOREIGN KEY (`pgm_pg_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
