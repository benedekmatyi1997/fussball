-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 23. Dez 2019 um 16:34
-- Server-Version: 10.3.16-MariaDB
-- PHP-Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `fussball`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `spiel`
--

CREATE TABLE `spiel` (
  `id` int(11) NOT NULL,
  `team1id` int(11) NOT NULL,
  `team2id` int(11) NOT NULL,
  `zeitpunkt` datetime NOT NULL,
  `halbzeit1` int(11) NOT NULL,
  `halbzeit2` int(11) NOT NULL,
  `stadionid` int(11) NOT NULL,
  `zuschauzahl` int(11) NOT NULL,
  `endstand1` int(11) NOT NULL,
  `endstand2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `spieler`
--

CREATE TABLE `spieler` (
  `id` int(11) NOT NULL,
  `vorname` varchar(50) NOT NULL,
  `nachname` varchar(50) NOT NULL,
  `geburtsdatum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `spieler2team`
--

CREATE TABLE `spieler2team` (
  `id` int(11) NOT NULL,
  `spielerid` int(11) NOT NULL,
  `teamid` int(11) NOT NULL,
  `von` date NOT NULL,
  `bis` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `spielereignis`
--

CREATE TABLE `spielereignis` (
  `id` int(11) NOT NULL,
  `spielerid` int(11) NOT NULL,
  `minute` int(11) NOT NULL,
  `nachspielzeit` int(11) NOT NULL,
  `typ` enum('spielt','einwechslung','auswechslung','tor','gelbe_karte','rote_karte') NOT NULL,
  `matchid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stadion`
--

CREATE TABLE `stadion` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ort` varchar(255) NOT NULL,
  `kapazitaet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `team`
--

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `team2stadion`
--

CREATE TABLE `team2stadion` (
  `id` int(11) NOT NULL,
  `teamid` int(11) NOT NULL,
  `stadionid` int(11) NOT NULL,
  `von` date NOT NULL,
  `bis` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `spiel`
--
ALTER TABLE `spiel`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `spieler`
--
ALTER TABLE `spieler`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `spieler2team`
--
ALTER TABLE `spieler2team`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `spielereignis`
--
ALTER TABLE `spielereignis`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `stadion`
--
ALTER TABLE `stadion`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `team2stadion`
--
ALTER TABLE `team2stadion`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `spiel`
--
ALTER TABLE `spiel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `spieler`
--
ALTER TABLE `spieler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `spieler2team`
--
ALTER TABLE `spieler2team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `spielereignis`
--
ALTER TABLE `spielereignis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `stadion`
--
ALTER TABLE `stadion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `team2stadion`
--
ALTER TABLE `team2stadion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE `spiel` ADD `saison` INT NOT NULL AFTER `team2id`;
  
RENAME TABLE `fussball`.`land` TO `fussball`.`region`;

ALTER TABLE `region` ADD `uebergeordnet` INT NOT NULL AFTER `code`, ADD `typ` ENUM('kontinent','land','region','') NOT NULL AFTER `uebergeordnet`;
ALTER TABLE `liga` CHANGE `land` `region` INT(11) NOT NULL;
ALTER TABLE `saison` ADD `aufstieg` INT NOT NULL AFTER `liga`;
ALTER TABLE `team` ADD `region` INT NOT NULL AFTER `name`;

COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


