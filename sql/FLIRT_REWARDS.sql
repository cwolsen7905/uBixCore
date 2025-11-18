/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: FLIRT_REWARDS
-- ------------------------------------------------------
-- Server version	10.6.20-16-MariaDB-enterprise-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Activity_0`
--

DROP TABLE IF EXISTS `Activity_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_0` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11465974 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_1`
--

DROP TABLE IF EXISTS `Activity_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_1` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28855565 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_10`
--

DROP TABLE IF EXISTS `Activity_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_10` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11284183 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_11`
--

DROP TABLE IF EXISTS `Activity_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_11` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27218459 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_12`
--

DROP TABLE IF EXISTS `Activity_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_12` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11179450 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_13`
--

DROP TABLE IF EXISTS `Activity_13`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_13` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26398209 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_14`
--

DROP TABLE IF EXISTS `Activity_14`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_14` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11303567 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_15`
--

DROP TABLE IF EXISTS `Activity_15`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_15` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26257090 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_16`
--

DROP TABLE IF EXISTS `Activity_16`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_16` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11393765 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_17`
--

DROP TABLE IF EXISTS `Activity_17`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_17` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26672579 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_18`
--

DROP TABLE IF EXISTS `Activity_18`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_18` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11799299 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_19`
--

DROP TABLE IF EXISTS `Activity_19`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_19` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28047869 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_2`
--

DROP TABLE IF EXISTS `Activity_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10791841 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_3`
--

DROP TABLE IF EXISTS `Activity_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_3` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27419071 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_4`
--

DROP TABLE IF EXISTS `Activity_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_4` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11647256 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_5`
--

DROP TABLE IF EXISTS `Activity_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_5` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28134393 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_6`
--

DROP TABLE IF EXISTS `Activity_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_6` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12172141 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_7`
--

DROP TABLE IF EXISTS `Activity_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_7` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26955710 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_8`
--

DROP TABLE IF EXISTS `Activity_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_8` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11235983 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_9`
--

DROP TABLE IF EXISTS `Activity_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_9` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(11) NOT NULL DEFAULT 0,
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `datetime_earned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_activity_date` (`user_id`,`activity_type_id`,`date_earned`),
  KEY `user_activity_datetime` (`user_id`,`activity_type_id`,`datetime_earned`),
  KEY `date_earned` (`date_earned`),
  KEY `datetime_earned` (`datetime_earned`),
  KEY `model_activity` (`model_id`,`activity_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28002632 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_Types`
--

DROP TABLE IF EXISTS `Activity_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_Types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `method` enum('earn','redeem','purchase','mission') NOT NULL DEFAULT 'earn',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `description` text NOT NULL,
  `num_points` mediumint(8) NOT NULL DEFAULT 0,
  `model_specific` enum('Y','N') NOT NULL DEFAULT 'N',
  `display` tinyint(1) NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Event_Log`
--

DROP TABLE IF EXISTS `Event_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Event_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `action` enum('level_up','level_down','status_up') NOT NULL DEFAULT 'level_up',
  `prev_ref_id` smallint(3) unsigned NOT NULL DEFAULT 1,
  `new_ref_id` smallint(3) unsigned NOT NULL DEFAULT 1,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_action` (`date`,`action`),
  KEY `date_action_user_prev_ref` (`date`,`action`,`user_id`,`prev_ref_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Import_Log`
--

DROP TABLE IF EXISTS `Import_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Import_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3117 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 17:37:10
