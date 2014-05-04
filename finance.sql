-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2014 at 01:09 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `finance`
--

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `Stock_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Stock_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `individual`
--

CREATE TABLE IF NOT EXISTS `individual` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Cash` decimal(11,2) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `individual_act_portfolios`
--

CREATE TABLE IF NOT EXISTS `individual_act_portfolios` (
  `Transaction_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Individual_ID` int(11) NOT NULL,
  `Portfolio_ID` int(11) NOT NULL,
  `Buy_or_sell` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`Transaction_ID`,`Individual_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `individual_act_stocks`
--

CREATE TABLE IF NOT EXISTS `individual_act_stocks` (
  `Transaction_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Individual_ID` int(11) NOT NULL,
  `Stock_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Buy_or_sell` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`Transaction_ID`,`Individual_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `individual_has_portfolios`
--

CREATE TABLE IF NOT EXISTS `individual_has_portfolios` (
  `Individual_ID` int(11) NOT NULL,
  `Portfolio_ID` int(11) NOT NULL,
  `Money_invested` decimal(11,2) NOT NULL,
  PRIMARY KEY (`Individual_ID`,`Portfolio_ID`),
  KEY `Portfolio_ID` (`Portfolio_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `individual_has_stocks`
--

CREATE TABLE IF NOT EXISTS `individual_has_stocks` (
  `Individual_ID` int(11) NOT NULL,
  `Stock_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Money_invested` decimal(11,2) NOT NULL,
  `Num_stocks` int(11) NOT NULL,
  PRIMARY KEY (`Individual_ID`,`Stock_name`),
  KEY `Stock_name` (`Stock_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio`
--

CREATE TABLE IF NOT EXISTS `portfolio` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Total_cash` decimal(11,2) NOT NULL,
  `Curr_cash` decimal(11,2) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_act_stocks`
--

CREATE TABLE IF NOT EXISTS `portfolio_act_stocks` (
  `Transaction_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Portfolio_ID` int(11) NOT NULL,
  `Stock_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Buy_or_sell` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`Transaction_ID`,`Portfolio_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_has_stocks`
--

CREATE TABLE IF NOT EXISTS `portfolio_has_stocks` (
  `Portfolio_ID` int(11) NOT NULL,
  `Stock_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `percent_invested` decimal(11,11) NOT NULL,
  PRIMARY KEY (`Portfolio_ID`,`Stock_name`),
  KEY `Stock_name` (`Stock_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE IF NOT EXISTS `quotes` (
  `Date` date NOT NULL,
  `Stock_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Quote` decimal(11,2) NOT NULL,
  `Day_hi` decimal(11,2) NOT NULL,
  `Day_lo` decimal(11,2) NOT NULL,
  `Volume` int(11) NOT NULL,
  PRIMARY KEY (`Date`,`Stock_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------------------------------------
--
-- Constraints for table `individual_has_portfolios`
--
ALTER TABLE `individual_has_portfolios`
  ADD CONSTRAINT `individual_has_portfolios_ibfk_1` FOREIGN KEY (`Individual_ID`) REFERENCES `individual` (`ID`),
  ADD CONSTRAINT `individual_has_portfolios_ibfk_2` FOREIGN KEY (`Portfolio_ID`) REFERENCES `portfolio` (`ID`);

--
-- Constraints for table `individual_has_stocks`
--
ALTER TABLE `individual_has_stocks`
  ADD CONSTRAINT `individual_has_stocks_ibfk_1` FOREIGN KEY (`Individual_ID`) REFERENCES `individual` (`ID`),
  ADD CONSTRAINT `individual_has_stocks_ibfk_2` FOREIGN KEY (`Stock_name`) REFERENCES `company` (`Stock_name`);

--
-- Constraints for table `portfolio_has_stocks`
--
ALTER TABLE `portfolio_has_stocks`
  ADD CONSTRAINT `portfolio_has_stocks_ibfk_1` FOREIGN KEY (`Portfolio_ID`) REFERENCES `portfolio` (`ID`),
  ADD CONSTRAINT `portfolio_has_stocks_ibfk_2` FOREIGN KEY (`Stock_name`) REFERENCES `company` (`Stock_name`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
