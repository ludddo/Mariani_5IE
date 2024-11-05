-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Creato il: Nov 05, 2024 alle 10:16
-- Versione del server: 10.1.10-MariaDB
-- Versione PHP: 7.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `musica`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `MUSICA_artisti`
--

CREATE TABLE `MUSICA_artisti` (
  `ID` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Cognome` varchar(50) NOT NULL,
  `Data_nascita` date DEFAULT NULL,
  `Immagine` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `MUSICA_artisti`
--

INSERT INTO `MUSICA_artisti` (`ID`, `Nome`, `Cognome`, `Data_nascita`, `Immagine`) VALUES
(1, 'Vasco', 'Rossi', NULL, 'blasco.jpg'),
(2, 'Elettra', 'Lamborghini', NULL, 'lamborghini.jpg');

-- --------------------------------------------------------

--
-- Struttura della tabella `MUSICA_brani`
--

CREATE TABLE `MUSICA_brani` (
  `ID` int(11) NOT NULL,
  `Titolo` varchar(100) NOT NULL,
  `Album` varchar(50) NOT NULL,
  `Durata` time NOT NULL,
  `Mp3` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `MUSICA_brani`
--

INSERT INTO `MUSICA_brani` (`ID`, `Titolo`, `Album`, `Durata`, `Mp3`) VALUES
(1, 'Buoni o cattivi', 'Buoni o cattivi', '00:03:35', NULL),
(2, 'Lambo', 'Velocità', '00:03:21', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `MUSICA_brani_MUSICA_artisti`
--

CREATE TABLE `MUSICA_brani_artisti` (
  `ID_ARTISTA` int(11) NOT NULL,
  `ID_BRANO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `MUSICA_brani_MUSICA_artisti`
--

INSERT INTO `MUSICA_brani_artisti` (`ID_ARTISTA`, `ID_BRANO`) VALUES
(1, 1),
(2, 2);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `MUSICA_artisti`
--
ALTER TABLE `MUSICA_artisti`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `MUSICA_brani`
--
ALTER TABLE `MUSICA_brani`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `MUSICA_brani_MUSICA_artisti`
--
ALTER TABLE `MUSICA_brani_artisti`
  ADD PRIMARY KEY (`ID_ARTISTA`,`ID_BRANO`),
  ADD KEY `ID_ARTISTA` (`ID_ARTISTA`),
  ADD KEY `ID_BRANO` (`ID_BRANO`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `MUSICA_artisti`
--
ALTER TABLE `MUSICA_artisti`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT per la tabella `MUSICA_brani`
--
ALTER TABLE `MUSICA_brani`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `MUSICA_brani_MUSICA_artisti`
--
ALTER TABLE `MUSICA_brani_artisti`
  ADD CONSTRAINT `MUSICA_brani_artisti_ibfk_1` FOREIGN KEY (`ID_ARTISTA`) REFERENCES `MUSICA_artisti` (`ID`),
  ADD CONSTRAINT `MUSICA_brani_artisti_ibfk_2` FOREIGN KEY (`ID_BRANO`) REFERENCES `MUSICA_brani` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
