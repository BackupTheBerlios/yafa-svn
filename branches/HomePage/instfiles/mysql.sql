-- SQL Dump

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
-- Table structure for table `Crud`
--

CREATE TABLE IF NOT EXISTS `Crud` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255),
  `body` varchar(255),
  PRIMARY KEY  (`ID`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;


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

