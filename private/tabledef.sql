-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 28, 2011 at 02:08 AM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `scrabble_words`
--

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

DROP TABLE IF EXISTS `words`;
CREATE TABLE IF NOT EXISTS `words` (
  `word` tinytext NOT NULL,
  `date_added` datetime NOT NULL,
  `added_by` varchar(25) NOT NULL,
  `json` longtext NOT NULL,
  `html_version` int(11) NOT NULL,
  `html` longtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

