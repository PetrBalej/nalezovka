-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Čtv 12. pro 2019, 06:21
-- Verze serveru: 10.4.10-MariaDB
-- Verze PHP: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Databáze: `geodb`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `gbifID` int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datasetKey` varchar(50) NOT NULL,
  `countryCode` varchar(3) NOT NULL,
  `locality` varchar(250) NOT NULL,
  `stateProvince` varchar(50) NOT NULL,
  `decimalLatitude` decimal(10,8) NOT NULL,
  `decimalLongitude` decimal(11,8) NOT NULL,
  `elevation` smallint(4) DEFAULT NULL,
  `eventDate` varchar(50) NOT NULL,
  `day` smallint(2) DEFAULT NULL,
  `month` smallint(2) DEFAULT NULL,
  `year` smallint(4) DEFAULT NULL,
  `institutionCode` varchar(50) NOT NULL,
  `souradnice` point NOT NULL,
  PRIMARY KEY (`gbifID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `occurrence`
--

DROP TABLE IF EXISTS `occurrence`;
CREATE TABLE IF NOT EXISTS `occurrence` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `event_gbifID` int(12) UNSIGNED NOT NULL,
  `occurrenceID` varchar(250) NOT NULL,
  `basisOfRecord` varchar(20) NOT NULL,
  `license` varchar(20) NOT NULL,
  `rightsHolder` varchar(50) NOT NULL,
  `recordedBy` varchar(50) NOT NULL,
  `issue` varchar(250) NOT NULL,
  `taxon_taxonKey` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_gbifID` (`event_gbifID`),
  KEY `taxon_taxonKey` (`taxon_taxonKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `taxon`
--

DROP TABLE IF EXISTS `taxon`;
CREATE TABLE IF NOT EXISTS `taxon` (
  `taxonKey` int(12) NOT NULL AUTO_INCREMENT,
  `kingdom` varchar(50) NOT NULL,
  `phylum` varchar(50) NOT NULL,
  `class` varchar(50) NOT NULL,
  `order` varchar(50) NOT NULL,
  `family` varchar(50) NOT NULL,
  `genus` varchar(50) NOT NULL,
  `species` varchar(50) NOT NULL,
  `taxonRank` varchar(20) NOT NULL,
  `scientificName` varchar(150) NOT NULL,
  `speciesKey` int(12) DEFAULT NULL,
  PRIMARY KEY (`taxonKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Minimální povinná struktura tabulky `reky`
--


DROP TABLE IF EXISTS `reky`;
CREATE TABLE IF NOT EXISTS `reky` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) DEFAULT NULL,
  `geo_line` multilinestring NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Minimální povinná struktura tabulky `jezera`
--

DROP TABLE IF EXISTS `jezera`;
CREATE TABLE IF NOT EXISTS `jezera` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(14) DEFAULT NULL,
  `geo_poly` multipolygon NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;