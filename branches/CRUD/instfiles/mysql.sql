-- phpMyAdmin SQL Dump
-- version 2.11.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 16, 2007 at 10:06 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yafa`
--
DROP DATABASE `yafa`;
CREATE DATABASE `yafa` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `yafa`;

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE IF NOT EXISTS `Category` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `Image`
--

CREATE TABLE IF NOT EXISTS `Image` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `filename` varchar(255) NOT NULL,
  `origfilename` varchar(255) NOT NULL,
  `checksum` varchar(33) NOT NULL,
  `url` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `urlthumb` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `checksum` (`checksum`),
  FULLTEXT KEY `origfilename` (`origfilename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=82701 ;

-- --------------------------------------------------------

--
-- Table structure for table `Image_Tags`
--

CREATE TABLE IF NOT EXISTS `Image_Tags` (
  `Image_ID` int(11) unsigned NOT NULL,
  `Tag_ID` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`Image_ID`,`Tag_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Msg`
--

CREATE TABLE IF NOT EXISTS `Msg` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `subject` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `unread` tinyint(1) unsigned NOT NULL default '1',
  `tagged` tinyint(1) unsigned NOT NULL default '0',
  `sender` varchar(255) NOT NULL,
  `messageid` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `unread` (`unread`),
  KEY `tagged` (`tagged`),
  KEY `sender` (`sender`),
  FULLTEXT KEY `subject` (`subject`,`sender`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21134 ;

-- --------------------------------------------------------

--
-- Table structure for table `Msg_Images`
--

CREATE TABLE IF NOT EXISTS `Msg_Images` (
  `Msg_ID` int(11) unsigned NOT NULL,
  `Image_ID` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`Msg_ID`,`Image_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Tag`
--

CREATE TABLE IF NOT EXISTS `Tag` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `Category_ID` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `Category` (`Category_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=173 ;

-- --------------------------------------------------------

--
-- Table structure for table `Tag_Connects`
--

CREATE TABLE IF NOT EXISTS `Tag_Connects` (
  `Tag_ID_is` int(10) unsigned NOT NULL,
  `Tag_ID_set` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`Tag_ID_is`,`Tag_ID_set`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Text`
--

CREATE TABLE IF NOT EXISTS `Text` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Msg_ID` int(11) unsigned NOT NULL,
  `content_type` varchar(25) NOT NULL,
  `body` longtext NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `content_type` (`content_type`),
  KEY `Msg_ID` (`Msg_ID`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41130 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `level` tinyint(4) NOT NULL default '0',
  `mail` varchar(255) NOT NULL,
  PRIMARY KEY  (`username`),
  FULLTEXT KEY `mail` (`mail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO `users` (`username`, `realname`, `level`, `mail`) VALUES ('admin', 'Admin', 9, 'root@localhost');

