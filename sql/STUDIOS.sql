/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: STUDIOS
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
-- Table structure for table `Account_Hold_Log`
--

DROP TABLE IF EXISTS `Account_Hold_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Account_Hold_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `account_type` enum('affiliate','broadcaster') DEFAULT 'affiliate',
  `admin_id` int(10) unsigned NOT NULL DEFAULT 0,
  `hold_change` enum('placed','removed') DEFAULT 'placed',
  `period_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `account_type` (`account_id`,`account_type`),
  KEY `account_date` (`account_id`,`created_at`),
  KEY `account_period` (`account_id`,`period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13012 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_Stream`
--

DROP TABLE IF EXISTS `Activity_Stream`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_Stream` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_type` varchar(20) NOT NULL DEFAULT 'manual',
  `user_type` varchar(15) NOT NULL DEFAULT '',
  `user_id` varchar(11) NOT NULL DEFAULT '',
  `display_name` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `external_link_url` varchar(255) DEFAULT NULL,
  `external_link_title` varchar(100) DEFAULT NULL,
  `date_posted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`message_id`),
  KEY `date_posted` (`date_posted`),
  KEY `message_type` (`message_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1721409 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Access_Log`
--

DROP TABLE IF EXISTS `Admin_Access_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Access_Log` (
  `item_id` int(11) unsigned NOT NULL DEFAULT 0,
  `item_type` enum('broadcaster','model','studio_user','prospect') NOT NULL DEFAULT 'broadcaster',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  KEY `date` (`date`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Config_Settings`
--

DROP TABLE IF EXISTS `Admin_Config_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Config_Settings` (
  `studio` char(12) NOT NULL DEFAULT '',
  `config_type_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `config_value` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`studio`,`config_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Config_Types`
--

DROP TABLE IF EXISTS `Admin_Config_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Config_Types` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `default_value` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Function_Access`
--

DROP TABLE IF EXISTS `Admin_Function_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Function_Access` (
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `option_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`admin_id`,`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Function_Categories`
--

DROP TABLE IF EXISTS `Admin_Function_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Function_Categories` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `script` varchar(250) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Function_Options`
--

DROP TABLE IF EXISTS `Admin_Function_Options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Function_Options` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `name` varchar(30) NOT NULL DEFAULT '',
  `script` varchar(250) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `linkable` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `tip` text DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=264 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Group_Users`
--

DROP TABLE IF EXISTS `Admin_Group_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Group_Users` (
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `group_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`admin_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Groups`
--

DROP TABLE IF EXISTS `Admin_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Groups` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(150) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Notification_Types`
--

DROP TABLE IF EXISTS `Admin_Notification_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Notification_Types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  `tpl` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Notification_Users`
--

DROP TABLE IF EXISTS `Admin_Notification_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Notification_Users` (
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `notification_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`admin_id`,`notification_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_User_Notes`
--

DROP TABLE IF EXISTS `Admin_User_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_User_Notes` (
  `studio_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  KEY `studio_admin_id` (`studio_admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users`
--

DROP TABLE IF EXISTS `Admin_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `username` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `forum_username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `enc_password` varchar(64) NOT NULL DEFAULT '',
  `salt` varchar(64) NOT NULL DEFAULT '',
  `2fa_secret` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `telephone` varchar(25) DEFAULT NULL,
  `mail_address` text DEFAULT NULL,
  `twitter_handle` varchar(25) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `can_monitor` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `vs_monitor` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `monitor_username` varchar(32) NOT NULL DEFAULT '',
  `editable` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `solo` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `adult` enum('Y','N') NOT NULL DEFAULT 'N',
  `vs_acct_mgr` enum('Y','N') DEFAULT 'N',
  `psychic` enum('Y','N') NOT NULL DEFAULT 'N',
  `date_last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_opened` date NOT NULL DEFAULT '0000-00-00',
  `last_clicked` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `cookie_policy_acceptance_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cookie_policy_acceptance_ip` varbinary(16) NOT NULL DEFAULT '',
  `cookie_policy_acceptance_version` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `editable` (`editable`)
) ENGINE=InnoDB AUTO_INCREMENT=854956 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users_Api_Tokens`
--

DROP TABLE IF EXISTS `Admin_Users_Api_Tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users_Api_Tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `api_token` varchar(64) NOT NULL DEFAULT '',
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `api_token` (`api_token`,`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users_Configs`
--

DROP TABLE IF EXISTS `Admin_Users_Configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users_Configs` (
  `admin_id` mediumint(5) unsigned NOT NULL,
  `json` text DEFAULT NULL,
  `associated_secure_data` text DEFAULT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users_Customers`
--

DROP TABLE IF EXISTS `Admin_Users_Customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users_Customers` (
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`admin_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users_IPs`
--

DROP TABLE IF EXISTS `Admin_Users_IPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users_IPs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varbinary(16) NOT NULL DEFAULT '',
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `date_added` datetime DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'Y',
  `added_admin_id` mediumint(8) DEFAULT NULL,
  `added_admin_type` enum('studio','admin') NOT NULL DEFAULT 'studio',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_bc` (`ip`,`broadcaster_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=14682 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users_IPs_Join`
--

DROP TABLE IF EXISTS `Admin_Users_IPs_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users_IPs_Join` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `ip_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_ip` (`admin_id`,`ip_id`),
  KEY `ip_id` (`ip_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129686 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users_Logins`
--

DROP TABLE IF EXISTS `Admin_Users_Logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users_Logins` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `ip_address_long` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fail_code` tinyint(2) unsigned NOT NULL DEFAULT 0,
  KEY `ip_address_long` (`ip_address_long`),
  KEY `datetime` (`datetime`),
  KEY `fail_code` (`fail_code`),
  KEY `velocity_search` (`ip_address_long`,`datetime`,`fail_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log`
--

DROP TABLE IF EXISTS `Agreement_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28063152 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_0`
--

DROP TABLE IF EXISTS `Agreement_Log_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_0` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31161881 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_1`
--

DROP TABLE IF EXISTS `Agreement_Log_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_1` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31086912 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_2`
--

DROP TABLE IF EXISTS `Agreement_Log_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31073321 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_3`
--

DROP TABLE IF EXISTS `Agreement_Log_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_3` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31034339 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_4`
--

DROP TABLE IF EXISTS `Agreement_Log_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_4` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31089071 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_5`
--

DROP TABLE IF EXISTS `Agreement_Log_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_5` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31110031 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_6`
--

DROP TABLE IF EXISTS `Agreement_Log_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_6` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31107550 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_7`
--

DROP TABLE IF EXISTS `Agreement_Log_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_7` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31077245 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_8`
--

DROP TABLE IF EXISTS `Agreement_Log_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_8` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31101638 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreement_Log_9`
--

DROP TABLE IF EXISTS `Agreement_Log_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreement_Log_9` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` enum('studio','model') NOT NULL DEFAULT 'studio',
  `user_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_signature` varchar(255) NOT NULL DEFAULT '',
  `additional_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_lookup` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31107771 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Agreements`
--

DROP TABLE IF EXISTS `Agreements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Agreements` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(50) NOT NULL DEFAULT '',
  `document_version` date NOT NULL DEFAULT '0000-00-00',
  `is_current` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_version` (`document_type`,`document_version`),
  KEY `current_doc` (`document_type`,`is_current`)
) ENGINE=InnoDB AUTO_INCREMENT=10062 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Announcements`
--

DROP TABLE IF EXISTS `Announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ts` datetime DEFAULT NULL,
  `template_path` varchar(255) NOT NULL DEFAULT '',
  `from_name` varchar(25) NOT NULL DEFAULT 'VS Broadcasting',
  `from_email` varchar(100) NOT NULL DEFAULT 'broadcastsupport@vs.com',
  `subject` text DEFAULT NULL,
  `detail` mediumtext DEFAULT NULL,
  `model_display` enum('N','Y') NOT NULL DEFAULT 'N',
  `solo_display` enum('N','Y') NOT NULL DEFAULT 'Y',
  `sent` enum('N','Y','P','D') DEFAULT NULL,
  `num_sent` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_opened` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `ok_to_send` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `send_to_adult` enum('N','Y') NOT NULL DEFAULT 'Y',
  `send_to_psychic` enum('N','Y') NOT NULL DEFAULT 'N',
  `solo_segment` varchar(25) NOT NULL DEFAULT '',
  `service_segment` enum('all','girls','guys') NOT NULL DEFAULT 'all',
  `salesperson_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `studio_active` tinyint(1) NOT NULL DEFAULT 0,
  `studio_inactive` tinyint(1) NOT NULL DEFAULT 0,
  `studio_prospects` tinyint(1) NOT NULL DEFAULT 0,
  `exec_only` enum('N','Y') DEFAULT 'N',
  `platform_segment` enum('all','asia','club','flirt4free') NOT NULL DEFAULT 'all',
  `is_payscale_related` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `send_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4178 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AnnouncementsSent`
--

DROP TABLE IF EXISTS `AnnouncementsSent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AnnouncementsSent` (
  `news_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  KEY `news_id` (`news_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Announcements_XVC`
--

DROP TABLE IF EXISTS `Announcements_XVC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Announcements_XVC` (
  `xvc_table` enum('studio','model') DEFAULT NULL,
  `xvc_id` int(10) unsigned DEFAULT NULL,
  `unsubscribe` enum('Y','N') NOT NULL DEFAULT 'Y',
  UNIQUE KEY `table_id` (`xvc_table`,`xvc_id`),
  KEY `xvc_table` (`xvc_table`),
  KEY `xvc_id` (`xvc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Automated_Screen_Capture`
--

DROP TABLE IF EXISTS `Automated_Screen_Capture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Automated_Screen_Capture` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `capture_date` date NOT NULL DEFAULT '0000-00-00',
  `broadcaster_id` mediumint(5) unsigned NOT NULL,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL,
  `room_status` char(3) NOT NULL DEFAULT '',
  `server_public_name` varchar(150) NOT NULL DEFAULT '',
  `time_online` smallint(2) unsigned NOT NULL DEFAULT 0,
  `insert_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processed_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processed_server` varchar(255) NOT NULL DEFAULT '',
  `status` enum('pending','failed','created') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `capture_date` (`capture_date`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1320437 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcaster_Admin_Join`
--

DROP TABLE IF EXISTS `Broadcaster_Admin_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcaster_Admin_Join` (
  `broadcaster_id` mediumint(5) unsigned NOT NULL,
  `studio_admin_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`broadcaster_id`,`studio_admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcaster_Tax_Compliance`
--

DROP TABLE IF EXISTS `Broadcaster_Tax_Compliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcaster_Tax_Compliance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` int(11) NOT NULL,
  `notified_on` date NOT NULL,
  `notification_type` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `atc_composite_index` (`broadcaster_id`,`notified_on`,`notification_type`)
) ENGINE=InnoDB AUTO_INCREMENT=38245 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters`
--

DROP TABLE IF EXISTS `Broadcasters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `prospect_id` mediumint(8) unsigned NOT NULL,
  `ref_affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `ref_model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ref_broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `ref_user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ref_bounty_paid` enum('N','Y') NOT NULL DEFAULT 'N',
  `details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `salesperson_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `salesperson_id_initial` smallint(3) unsigned NOT NULL DEFAULT 0,
  `type` enum('large','medium','small','direct','solo') NOT NULL DEFAULT 'small',
  `daily_report_recipients` varchar(255) NOT NULL DEFAULT '',
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `date_first_broadcast` date DEFAULT NULL,
  `date_last_activity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `can_manage` enum('Y','N') NOT NULL DEFAULT 'Y',
  `can_recruit` enum('Y','N') NOT NULL DEFAULT 'N',
  `interactive` tinyint(1) NOT NULL DEFAULT 1,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `on_hold_until` date DEFAULT NULL,
  `payout_interval` enum('bi-weekly','weekly') NOT NULL DEFAULT 'bi-weekly',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `model_request_email` varchar(255) NOT NULL DEFAULT '',
  `on_hold_since` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `salesperson_id` (`salesperson_id`),
  KEY `prospect_id` (`prospect_id`),
  KEY `studio_admin_id` (`studio_admin_id`),
  KEY `ref_affiliate_id` (`ref_affiliate_id`),
  KEY `ref_model_id` (`ref_model_id`),
  KEY `ref_user_id` (`ref_user_id`),
  KEY `ref_broadcaster_id` (`ref_broadcaster_id`),
  KEY `date_last_activity` (`date_last_activity`),
  KEY `hold_index` (`on_hold`,`on_hold_since`)
) ENGINE=InnoDB AUTO_INCREMENT=871981 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_ACH_Verification_Log`
--

DROP TABLE IF EXISTS `Broadcasters_ACH_Verification_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_ACH_Verification_Log` (
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `ach_user_id` varchar(30) NOT NULL DEFAULT '',
  `paymethod_id` varchar(30) NOT NULL DEFAULT '',
  `deposit_amount_1` float(5,2) NOT NULL DEFAULT 0.00,
  `transact_id_1` char(40) NOT NULL DEFAULT '',
  `deposit_amount_2` float(5,2) NOT NULL DEFAULT 0.00,
  `transact_id_2` char(40) NOT NULL DEFAULT '',
  `deposit_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reclaimed` tinyint(1) NOT NULL DEFAULT 0,
  `reclaimed_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','verified','failed','deleted') NOT NULL DEFAULT 'pending',
  `comment` text NOT NULL,
  `attempts_made` tinyint(1) NOT NULL DEFAULT 0,
  `attempts_left` tinyint(1) NOT NULL DEFAULT 3,
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`broadcaster_id`,`ach_user_id`,`paymethod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_Comments`
--

DROP TABLE IF EXISTS `Broadcasters_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_Comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('studio','admin','model') DEFAULT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=16155725 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_Contact_Details`
--

DROP TABLE IF EXISTS `Broadcasters_Contact_Details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_Contact_Details` (
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `contact_details` text NOT NULL,
  PRIMARY KEY (`broadcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_Details`
--

DROP TABLE IF EXISTS `Broadcasters_Details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_Details` (
  `details_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `check_name` varchar(255) NOT NULL DEFAULT '',
  `mail_name` varchar(255) NOT NULL DEFAULT '',
  `address_1` varchar(255) NOT NULL DEFAULT '',
  `address_2` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(30) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL DEFAULT '',
  `tax_id` varchar(40) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `fax` varchar(50) NOT NULL DEFAULT '',
  `payout_method` varchar(20) NOT NULL DEFAULT 'mail',
  `backup_payment_method` varchar(20) NOT NULL DEFAULT '',
  `payout_minimum` float(6,2) NOT NULL DEFAULT 150.00,
  `epassporte_id` varchar(32) NOT NULL DEFAULT '',
  `payoneer_id` varchar(32) NOT NULL DEFAULT '',
  `paxum_id` varchar(255) NOT NULL DEFAULT '',
  `cosmopay_id` varchar(32) NOT NULL DEFAULT '0',
  `redpass_id` varchar(255) NOT NULL DEFAULT '',
  `forte_id` varchar(30) NOT NULL DEFAULT '',
  `forte_paymethod_id` varchar(30) NOT NULL DEFAULT '',
  `epayments_id` varchar(10) NOT NULL DEFAULT '',
  `wire_recip_address` text NOT NULL,
  `wire_bank_name` varchar(40) NOT NULL DEFAULT '',
  `wire_bank_address` text NOT NULL,
  `wire_routing_num` varchar(40) NOT NULL DEFAULT '',
  `wire_account_num` varchar(40) NOT NULL DEFAULT '',
  `wire_correspondent` varchar(100) NOT NULL DEFAULT '',
  `created_by_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `created_by_admin_type` enum('studio','admin','model') DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `secondary_backup_payment_method` varchar(20) NOT NULL DEFAULT '',
  `accounting_vendor_id` varchar(8) NOT NULL DEFAULT '',
  `wire_intl` char(9) DEFAULT NULL,
  `accounting_status` varchar(15) DEFAULT NULL,
  `crypto_wallet` varchar(64) DEFAULT NULL,
  `crypto_hash` char(64) DEFAULT NULL,
  `crypto_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`details_id`),
  KEY `details_id` (`details_id`),
  KEY `broadcaster_id` (`broadcaster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1336919 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_Documents`
--

DROP TABLE IF EXISTS `Broadcasters_Documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_Documents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `upload_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_size` int(10) unsigned NOT NULL DEFAULT 0,
  `file_type` enum('W9','Identification Card','Proof of Residency','Misc','W8-BEN','W8-BEN-E','Sourcing Statement') DEFAULT NULL,
  `file_mime_type` varchar(50) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `file_type` (`file_type`)
) ENGINE=InnoDB AUTO_INCREMENT=111968 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_Payment_Method`
--

DROP TABLE IF EXISTS `Broadcasters_Payment_Method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_Payment_Method` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `payment_method_details` text DEFAULT NULL,
  `associated_secure_data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `broadcaster_id` (`broadcaster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12412 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_Setup`
--

DROP TABLE IF EXISTS `Broadcasters_Setup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_Setup` (
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `payment_details` varchar(20) DEFAULT NULL,
  `prospect_images` varchar(20) DEFAULT NULL,
  `stage_name` varchar(20) DEFAULT NULL,
  `model_record` varchar(20) DEFAULT NULL,
  `custodian_record` varchar(20) DEFAULT NULL,
  `bio` varchar(20) DEFAULT NULL,
  `upload_id` varchar(20) DEFAULT NULL,
  `upload_2257` varchar(20) DEFAULT NULL,
  `upload_headshot` varchar(20) DEFAULT NULL,
  `upload_sample_image` varchar(20) DEFAULT NULL,
  `download_software` varchar(20) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_last_accessed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_completed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','complete','set_inactive') NOT NULL DEFAULT 'pending',
  `misc_setup_data` text DEFAULT NULL,
  `upload_carry_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`broadcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_Sourcing_Statement`
--

DROP TABLE IF EXISTS `Broadcasters_Sourcing_Statement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_Sourcing_Statement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `form_data` text DEFAULT NULL COMMENT 'Form data is stored as a JSON',
  PRIMARY KEY (`id`),
  KEY `broadcaster_date_index` (`broadcaster_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4607 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasters_Watchlist`
--

DROP TABLE IF EXISTS `Broadcasters_Watchlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasters_Watchlist` (
  `item_type` enum('broadcaster','model') NOT NULL DEFAULT 'broadcaster',
  `item_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`item_type`,`item_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Brokers_Comments`
--

DROP TABLE IF EXISTS `Brokers_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Brokers_Comments` (
  `broker_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  KEY `broker_id` (`broker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Change_Log`
--

DROP TABLE IF EXISTS `Change_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Change_Log` (
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `change_type` enum('model','studio') NOT NULL DEFAULT 'model',
  `change_id` int(11) unsigned NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`datetime`,`admin_id`,`change_type`,`change_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Confirmo_Payouts`
--

DROP TABLE IF EXISTS `Confirmo_Payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Confirmo_Payouts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `json_response` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=748 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Country_Codes`
--

DROP TABLE IF EXISTS `Country_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Country_Codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(255) NOT NULL,
  `alpha2` char(2) NOT NULL,
  `alpha3` char(3) NOT NULL,
  `num3` char(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Credit_Adjustments`
--

DROP TABLE IF EXISTS `Credit_Adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Credit_Adjustments` (
  `credit_transact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_credit_transact_id` int(11) unsigned DEFAULT NULL,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `num_credits` mediumint(9) NOT NULL DEFAULT 0,
  `type` enum('adjustment','reversal','mb_adjustment') DEFAULT NULL,
  `identifier` varchar(20) DEFAULT NULL,
  `ref_id` int(11) unsigned DEFAULT NULL,
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `reason_code` mediumint(8) unsigned DEFAULT NULL,
  `reason_text` text DEFAULT NULL,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`credit_transact_id`),
  KEY `model_id` (`model_id`,`date_transact`),
  KEY `platform` (`platform`),
  KEY `identifier` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=34537 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Credit_Tiers`
--

DROP TABLE IF EXISTS `Credit_Tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Credit_Tiers` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `minimum_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `maximum_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `bar_width` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `badge_image` varchar(75) NOT NULL DEFAULT '',
  `tier_order` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `tier_order` (`tier_order`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Credits_Tally_Temp`
--

DROP TABLE IF EXISTS `Credits_Tally_Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Credits_Tally_Temp` (
  `date` date NOT NULL,
  `model_id` int(11) DEFAULT NULL,
  `credits` mediumint(7) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Custodians`
--

DROP TABLE IF EXISTS `Custodians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Custodians` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `custodian_name` varchar(100) NOT NULL DEFAULT '',
  `custodian_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `address` text NOT NULL,
  `binary_cor_form` longblob DEFAULT NULL,
  `created_admin_type` varchar(25) NOT NULL DEFAULT 'internal',
  `created_admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `custodian_admin_id` (`custodian_admin_id`),
  KEY `custodian_name` (`custodian_name`)
) ENGINE=InnoDB AUTO_INCREMENT=96453 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Custom_Referral_Slugs`
--

DROP TABLE IF EXISTS `Custom_Referral_Slugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Custom_Referral_Slugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT 0,
  `account_type` enum('model','broadcaster') NOT NULL DEFAULT 'model',
  `url_slug` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `recruiting_domain` varchar(255) NOT NULL DEFAULT 'webcam4money.com',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account_id`,`account_type`),
  UNIQUE KEY `url_slug` (`url_slug`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Daily_Overview_Report_Sub_Studios`
--

DROP TABLE IF EXISTS `Daily_Overview_Report_Sub_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Daily_Overview_Report_Sub_Studios` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `studio_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `email_recipient` varchar(255) NOT NULL DEFAULT '',
  `sub_studio` char(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `studio_admin_id` (`studio_admin_id`,`email_recipient`,`sub_studio`)
) ENGINE=InnoDB AUTO_INCREMENT=236 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Digital_Signature_Requests`
--

DROP TABLE IF EXISTS `Digital_Signature_Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Digital_Signature_Requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rand_id` varchar(25) NOT NULL,
  `admin_id` mediumint(8) unsigned NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `bc_email` varchar(255) NOT NULL,
  `id_type` varchar(255) NOT NULL,
  `id_authority` varchar(255) NOT NULL,
  `id_number` varchar(255) NOT NULL,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `rand_id` (`rand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=229937 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Documents`
--

DROP TABLE IF EXISTS `Documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Documents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned DEFAULT NULL,
  `phone` varchar(50) DEFAULT '',
  `file_name` varchar(50) NOT NULL DEFAULT '',
  `page_num` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `document_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upload_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upload_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `upload_admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `review_status` enum('pending','approved','rejected','hold') NOT NULL DEFAULT 'pending',
  `review_comments` varchar(255) DEFAULT NULL,
  `redacted_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `redacted_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `redacted_status` enum('pending','complete') NOT NULL DEFAULT 'pending',
  `original_review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `original_review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `studio` (`studio`),
  KEY `model_id` (`model_id`),
  KEY `upload_date` (`upload_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3075133 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Documents_Temp`
--

DROP TABLE IF EXISTS `Documents_Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Documents_Temp` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(155) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Documents_Types`
--

DROP TABLE IF EXISTS `Documents_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Documents_Types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(30) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Epassporte_Log`
--

DROP TABLE IF EXISTS `Epassporte_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Epassporte_Log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `amount` float(8,2) NOT NULL DEFAULT 0.00,
  `message` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `broadcaster_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `response_code` varchar(32) NOT NULL DEFAULT '',
  `response_string` varchar(255) NOT NULL DEFAULT '',
  `ep_user_id` varchar(32) NOT NULL DEFAULT '',
  `ep_debit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_fee_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_credit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_action_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL DEFAULT 'unknown',
  `pay_period_id` smallint(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`),
  KEY `ep_user_id` (`ep_user_id`),
  KEY `status` (`status`),
  KEY `broadcaster_id` (`broadcaster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9018 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Payouts`
--

DROP TABLE IF EXISTS `External_Payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Payouts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payout_method` varchar(20) NOT NULL DEFAULT '',
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `amount` float(8,2) NOT NULL DEFAULT 0.00,
  `message` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `broadcaster_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `response_code` varchar(32) NOT NULL DEFAULT '',
  `response_string` varchar(255) NOT NULL DEFAULT '',
  `ep_user_id` varchar(255) NOT NULL DEFAULT '',
  `ep_debit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_fee_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_credit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_action_id` int(11) unsigned NOT NULL DEFAULT 0,
  `processor_transact_id` varchar(120) NOT NULL DEFAULT '',
  `processor_fee` float(8,3) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'unknown',
  `pay_period_id` smallint(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`),
  KEY `ep_user_id` (`ep_user_id`),
  KEY `status` (`status`),
  KEY `broadcaster_id` (`broadcaster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57391 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ`
--

DROP TABLE IF EXISTS `FAQ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FAQ` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `models_can_view` enum('Y','N') NOT NULL DEFAULT 'N',
  `adult` enum('Y','N') NOT NULL DEFAULT 'N',
  `club` enum('Y','N') NOT NULL DEFAULT 'N',
  `xvc` enum('Y','N') NOT NULL DEFAULT 'N',
  `question` varchar(255) NOT NULL DEFAULT '',
  `answer` text NOT NULL,
  `votes_good` smallint(4) unsigned NOT NULL DEFAULT 0,
  `votes_total` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=783 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ_Categories`
--

DROP TABLE IF EXISTS `FAQ_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FAQ_Categories` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ_Search_Log`
--

DROP TABLE IF EXISTS `FAQ_Search_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FAQ_Search_Log` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `user_type` enum('model','studio') NOT NULL DEFAULT 'studio',
  `query` varchar(255) NOT NULL DEFAULT '',
  `num_searches` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`user_type`,`query`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FMOM_Winners`
--

DROP TABLE IF EXISTS `FMOM_Winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FMOM_Winners` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `year_month` char(7) NOT NULL DEFAULT '0000-00',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  PRIMARY KEY (`id`),
  KEY `winner` (`model_id`,`year_month`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fame_Type`
--

DROP TABLE IF EXISTS `Fame_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fame_Type` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fan_Club_Activity`
--

DROP TABLE IF EXISTS `Fan_Club_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fan_Club_Activity` (
  `activity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity_type` varchar(20) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `display_name` varchar(50) NOT NULL DEFAULT '',
  `domain` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `external_link_url` varchar(255) DEFAULT NULL,
  `external_link_title` varchar(100) DEFAULT NULL,
  `date_posted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`activity_id`),
  KEY `date_posted` (`date_posted`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1156600 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Filter_ByCountry`
--

DROP TABLE IF EXISTS `Filter_ByCountry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filter_ByCountry` (
  `studio` char(12) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  UNIQUE KEY `studio` (`studio`,`country`),
  KEY `studio_2` (`studio`,`country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Filter_ByCountry_Models`
--

DROP TABLE IF EXISTS `Filter_ByCountry_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filter_ByCountry_Models` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `country` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Filter_ByIP`
--

DROP TABLE IF EXISTS `Filter_ByIP`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filter_ByIP` (
  `studio` char(12) NOT NULL DEFAULT '',
  `model_id` int(11) NOT NULL DEFAULT 0,
  `start_ip` varchar(15) NOT NULL DEFAULT '',
  `end_ip` varchar(15) NOT NULL DEFAULT '',
  `created_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`studio`,`model_id`,`start_ip`,`end_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Filter_ByState`
--

DROP TABLE IF EXISTS `Filter_ByState`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filter_ByState` (
  `studio` char(12) NOT NULL DEFAULT '',
  `state` char(2) NOT NULL DEFAULT '',
  UNIQUE KEY `studio` (`studio`,`state`),
  KEY `studio_2` (`studio`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Filter_ByState_Models`
--

DROP TABLE IF EXISTS `Filter_ByState_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filter_ByState_Models` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `state` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Filtered_Words_Pending`
--

DROP TABLE IF EXISTS `Filtered_Words_Pending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filtered_Words_Pending` (
  `word` varchar(255) NOT NULL,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_requested` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `review_comments` varchar(255) DEFAULT NULL,
  `status` enum('pending','complete','rejected') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`word`,`model_id`),
  KEY `date_requested` (`date_requested`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Perks`
--

DROP TABLE IF EXISTS `Flirt_Perks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Perks` (
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Perks_Adjustments`
--

DROP TABLE IF EXISTS `Flirt_Perks_Adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Perks_Adjustments` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `broadcaster_id` mediumint(5) unsigned NOT NULL,
  `num_credits` smallint(5) unsigned NOT NULL,
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `schedule_id` int(11) unsigned NOT NULL,
  `ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `identifier` (`identifier`),
  KEY `schedule_id` (`schedule_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `ref_id` (`ref_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49340 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Perks_Schedule`
--

DROP TABLE IF EXISTS `Flirt_Perks_Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Perks_Schedule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Summit_Random_Optin`
--

DROP TABLE IF EXISTS `Flirt_Summit_Random_Optin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Summit_Random_Optin` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_University_Answers`
--

DROP TABLE IF EXISTS `Flirt_University_Answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_University_Answers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) unsigned NOT NULL DEFAULT 0,
  `quiz_id` int(11) unsigned NOT NULL DEFAULT 0,
  `text` varchar(255) NOT NULL DEFAULT '',
  `order_by` char(1) NOT NULL DEFAULT '',
  `correct` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB AUTO_INCREMENT=821 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_University_Helpful_Ratings`
--

DROP TABLE IF EXISTS `Flirt_University_Helpful_Ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_University_Helpful_Ratings` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_type` enum('model','studio') NOT NULL DEFAULT 'model',
  `category_item_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `category_type` enum('Training_Videos','FAQ','Handbook') NOT NULL DEFAULT 'FAQ',
  `voted` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`,`user_type`,`category_item_id`,`category_type`),
  KEY `voted` (`voted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_University_Models_Answers`
--

DROP TABLE IF EXISTS `Flirt_University_Models_Answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_University_Models_Answers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `question_id` int(11) unsigned NOT NULL DEFAULT 0,
  `quiz_id` int(11) unsigned NOT NULL DEFAULT 0,
  `answer_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model` (`model_id`,`quiz_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10180500 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_University_Models_Completed_Quizzes`
--

DROP TABLE IF EXISTS `Flirt_University_Models_Completed_Quizzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_University_Models_Completed_Quizzes` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `quiz_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_completed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`model_id`,`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_University_Questions`
--

DROP TABLE IF EXISTS `Flirt_University_Questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_University_Questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `num` int(11) unsigned NOT NULL DEFAULT 0,
  `quiz_id` int(11) unsigned NOT NULL DEFAULT 0,
  `text` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_University_Questions_Videos`
--

DROP TABLE IF EXISTS `Flirt_University_Questions_Videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_University_Questions_Videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int(10) unsigned NOT NULL DEFAULT 0,
  `question_id` int(10) unsigned NOT NULL DEFAULT 0,
  `video_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quiz_id_question_id` (`quiz_id`,`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_University_Quizzes`
--

DROP TABLE IF EXISTS `Flirt_University_Quizzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_University_Quizzes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_University_Search_Data`
--

DROP TABLE IF EXISTS `Flirt_University_Search_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_University_Search_Data` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` enum('model','studio') NOT NULL DEFAULT 'model',
  `title` varchar(30) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `keywords` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_type` (`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Form_1099_Broadcasters`
--

DROP TABLE IF EXISTS `Form_1099_Broadcasters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Form_1099_Broadcasters` (
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `year` year(4) NOT NULL DEFAULT 0000,
  `recipient_id` varchar(25) NOT NULL DEFAULT '',
  `compensation` float(10,2) NOT NULL DEFAULT 0.00,
  `name` varchar(75) NOT NULL DEFAULT '',
  `address` varchar(250) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `province` varchar(25) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  `postal_code` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`year`,`broadcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Activities`
--

DROP TABLE IF EXISTS `GeoPowerScore_Activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `identifier` varchar(48) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `applies` enum('by_region','across_all') NOT NULL DEFAULT 'by_region',
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Current`
--

DROP TABLE IF EXISTS `GeoPowerScore_Current`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Current` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `score` int(11) NOT NULL DEFAULT 0,
  `rank` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mrps` (`model_id`,`region`,`platform`,`sitekey`),
  KEY `srps` (`region`,`platform`,`sitekey`),
  KEY `platreg` (`platform`,`region`),
  KEY `region` (`region`),
  KEY `rank` (`rank`)
) ENGINE=InnoDB AUTO_INCREMENT=4543656575 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_0`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_0` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=9491215 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_1`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=8586087 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_2`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=8347743 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_3`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_3` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=8468043 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_4`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_4` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=8489060 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_5`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_5` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=9048961 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_6`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_6` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=8608000 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_7`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_7` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=8300669 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_8`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_8` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=8037645 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Daily_Summary_9`
--

DROP TABLE IF EXISTS `GeoPowerScore_Daily_Summary_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Daily_Summary_9` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `region` varchar(16) NOT NULL DEFAULT '',
  `platform` varchar(16) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `credits` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dmrpsa` (`date`,`model_id`,`region`,`platform`,`sitekey`,`activity_id`),
  KEY `actdate` (`activity_id`,`date`),
  KEY `date` (`date`),
  KEY `activity_id` (`activity_id`),
  KEY `model_id` (`model_id`),
  KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=8801806 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GeoPowerScore_Performer_Details`
--

DROP TABLE IF EXISTS `GeoPowerScore_Performer_Details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GeoPowerScore_Performer_Details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bio_video` int(11) unsigned NOT NULL DEFAULT 0,
  `hours_online` int(11) unsigned NOT NULL DEFAULT 0,
  `cph` int(11) unsigned NOT NULL DEFAULT 0,
  `photos` int(11) unsigned NOT NULL DEFAULT 0,
  `spenders` int(11) unsigned NOT NULL DEFAULT 0,
  `flirtu` int(11) unsigned NOT NULL DEFAULT 0,
  `flirt_boosts` int(11) unsigned NOT NULL DEFAULT 0,
  `custom_adjustments` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`),
  KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB AUTO_INCREMENT=1442391318 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hall_of_Fame`
--

DROP TABLE IF EXISTS `Hall_of_Fame`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hall_of_Fame` (
  `model_id` int(11) unsigned NOT NULL,
  `service` enum('girls','guys','trans') DEFAULT NULL,
  `fame_type` tinyint(4) unsigned NOT NULL,
  `date` date NOT NULL,
  `quantity` mediumint(7) unsigned NOT NULL,
  `ref_id` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hall_of_Fame_Tiers`
--

DROP TABLE IF EXISTS `Hall_of_Fame_Tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hall_of_Fame_Tiers` (
  `model_id` int(11) unsigned NOT NULL,
  `service` enum('girls','guys','trans') DEFAULT NULL,
  `fame_type` tinyint(4) unsigned NOT NULL,
  `date` date NOT NULL,
  `quantity` mediumint(7) unsigned DEFAULT NULL,
  `ref_id` int(11) unsigned NOT NULL,
  `tier` enum('','platinum','gold','silver','bronze') DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Historical_Tier_Changes`
--

DROP TABLE IF EXISTS `Historical_Tier_Changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Historical_Tier_Changes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL,
  `tier_id` tinyint(3) unsigned NOT NULL,
  `effective_since_period_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hold_Reasons`
--

DROP TABLE IF EXISTS `Hold_Reasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hold_Reasons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description_internal` varchar(255) NOT NULL DEFAULT '',
  `description_external` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Kiiroo_Results`
--

DROP TABLE IF EXISTS `Kiiroo_Results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Kiiroo_Results` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(17) DEFAULT NULL,
  `device_type` enum('Onyx','Pearl') NOT NULL DEFAULT 'Onyx',
  `address` text NOT NULL,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Locations`
--

DROP TABLE IF EXISTS `Locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Locations` (
  `id` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `studio_code` (`studio_code`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=31002 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Login_Override_Log`
--

DROP TABLE IF EXISTS `Login_Override_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Login_Override_Log` (
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL DEFAULT 0,
  `account_type` enum('model','studio') NOT NULL DEFAULT 'studio',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bypassed_2fa` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`admin_id`,`account_id`,`datetime`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Login_Overrides`
--

DROP TABLE IF EXISTS `Login_Overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Login_Overrides` (
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL DEFAULT 0,
  `account_type` enum('model','studio') NOT NULL DEFAULT 'studio',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hashed_token` varchar(64) NOT NULL DEFAULT '',
  `bypass_2fa` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`hashed_token`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Low_CPH_Log`
--

DROP TABLE IF EXISTS `Low_CPH_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Low_CPH_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `warning_bucket` varchar(25) NOT NULL DEFAULT '25hr_50cph',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `lifetime_cph` decimal(11,2) NOT NULL DEFAULT 0.00,
  `lifetime_hours` decimal(11,2) NOT NULL DEFAULT 0.00,
  `percentile` smallint(3) unsigned DEFAULT 0,
  `action` enum('warning','ban') NOT NULL DEFAULT 'warning',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=93641 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Low_CPM_Log`
--

DROP TABLE IF EXISTS `Low_CPM_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Low_CPM_Log` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `test_group` varchar(20) NOT NULL DEFAULT 'percentile 70 to 99',
  `lifetime_cph` decimal(11,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`model_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MCL_review`
--

DROP TABLE IF EXISTS `MCL_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MCL_review` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_seconds` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MOW_Answers`
--

DROP TABLE IF EXISTS `MOW_Answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MOW_Answers` (
  `winner_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `question_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `answer` text NOT NULL,
  PRIMARY KEY (`winner_id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MOW_Questions`
--

DROP TABLE IF EXISTS `MOW_Questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MOW_Questions` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MOW_Winners`
--

DROP TABLE IF EXISTS `MOW_Winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MOW_Winners` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `week_ending` date NOT NULL DEFAULT '0000-00-00',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  PRIMARY KEY (`id`),
  KEY `winner` (`model_id`,`week_ending`)
) ENGINE=InnoDB AUTO_INCREMENT=2885 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Masspay_Api_Log`
--

DROP TABLE IF EXISTS `Masspay_Api_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Masspay_Api_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `env` enum('api','staging-api') NOT NULL DEFAULT 'api',
  `version` varchar(15) NOT NULL DEFAULT '',
  `resource` varchar(24) NOT NULL DEFAULT '',
  `endpoint` varchar(100) NOT NULL DEFAULT '',
  `request` text NOT NULL,
  `response` text NOT NULL,
  `result_user_token` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Activity`
--

DROP TABLE IF EXISTS `Model_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Activity` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Activity_Stream`
--

DROP TABLE IF EXISTS `Model_Activity_Stream`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Activity_Stream` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `activity_id` smallint(3) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `activity_id` (`activity_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=3496321 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Activity_Stream_Log`
--

DROP TABLE IF EXISTS `Model_Activity_Stream_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Activity_Stream_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_activity_stream_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` mediumint(11) unsigned NOT NULL DEFAULT 0,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `model_activity_stream_id` (`model_activity_stream_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3400658 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Attrition`
--

DROP TABLE IF EXISTS `Model_Attrition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Attrition` (
  `model_id` int(8) unsigned NOT NULL,
  `begin_date` date DEFAULT NULL,
  `last_show` date DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `attrition_type` char(1) DEFAULT NULL,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Authorization_Tokens`
--

DROP TABLE IF EXISTS `Model_Authorization_Tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Authorization_Tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` int(11) unsigned NOT NULL DEFAULT 0,
  `token` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime DEFAULT NULL,
  `date_expires` datetime DEFAULT NULL,
  `bc_hash` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76113 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Blocked_Users`
--

DROP TABLE IF EXISTS `Model_Blocked_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Blocked_Users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `screen_name_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_added` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_user` (`model_id`,`screen_name_id`),
  KEY `model_id` (`model_id`),
  KEY `screen_name_id` (`screen_name_id`),
  KEY `user_id` (`user_id`),
  KEY `date_added` (`date_added`)
) ENGINE=InnoDB AUTO_INCREMENT=3588 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Evaluations`
--

DROP TABLE IF EXISTS `Model_Evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Evaluations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `score` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `send_to_studio` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `reevaluation_datetime` datetime DEFAULT '0000-00-00 00:00:00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `score` (`score`),
  KEY `reevaluation_datetime` (`reevaluation_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Favorites`
--

DROP TABLE IF EXISTS `Model_Favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Favorites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `screen_name_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_added` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_user` (`model_id`,`screen_name_id`),
  KEY `model_id` (`model_id`),
  KEY `screen_name_id` (`screen_name_id`),
  KEY `user_id` (`user_id`),
  KEY `date_added` (`date_added`)
) ENGINE=InnoDB AUTO_INCREMENT=2120725 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Move_Queue`
--

DROP TABLE IF EXISTS `Model_Move_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Move_Queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `from_studio` char(12) NOT NULL DEFAULT '',
  `status` enum('Queue','Progress','Done','Error') NOT NULL DEFAULT 'Queue',
  `message` varchar(100) DEFAULT NULL,
  `created` timestamp NULL DEFAULT current_timestamp(),
  `updated` datetime DEFAULT NULL,
  `admin_id` mediumint(5) unsigned NOT NULL,
  `options` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6766 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Moves`
--

DROP TABLE IF EXISTS `Model_Moves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Moves` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Names_Approval`
--

DROP TABLE IF EXISTS `Model_Names_Approval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Names_Approval` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_name` varchar(50) NOT NULL DEFAULT '',
  `model_legal_name` varchar(50) NOT NULL DEFAULT '',
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  `studio` char(12) NOT NULL DEFAULT '',
  `service` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `submitted_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `submitted_datetime` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_datetime` datetime DEFAULT NULL,
  `review_comments` varchar(255) DEFAULT NULL,
  `review_status` enum('pending','approved','expired','rejected','escalated') DEFAULT 'pending',
  `taken` enum('N','Y') NOT NULL DEFAULT 'N',
  `num_perf` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_name` (`model_name`,`studio`),
  KEY `review_status` (`review_status`),
  KEY `submitted_datetime` (`submitted_datetime`),
  KEY `studio` (`studio`)
) ENGINE=InnoDB AUTO_INCREMENT=2242206 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Names_Approval_Prefix`
--

DROP TABLE IF EXISTS `Model_Names_Approval_Prefix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Names_Approval_Prefix` (
  `prefix` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Newbie_Hours`
--

DROP TABLE IF EXISTS `Model_Newbie_Hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Newbie_Hours` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned DEFAULT 0,
  `date_created` date NOT NULL,
  `newbie_hours` smallint(1) unsigned NOT NULL DEFAULT 0,
  `reason` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_date` (`model_id`,`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=8427 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Party_RSVP`
--

DROP TABLE IF EXISTS `Model_Party_RSVP`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Party_RSVP` (
  `party_name` varchar(50) NOT NULL DEFAULT '',
  `user_type` enum('studio','model') NOT NULL DEFAULT 'model',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_guests` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`party_name`,`user_type`,`user_id`),
  KEY `party_name` (`party_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Plugin`
--

DROP TABLE IF EXISTS `Model_Plugin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `label` varchar(255) NOT NULL DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '0000',
  `access_key` varchar(40) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `access_salt` varchar(40) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `access_key` (`access_key`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1242 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Plugin_Settings`
--

DROP TABLE IF EXISTS `Model_Plugin_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Plugin_Settings` (
  `plugin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `setting_key` varchar(40) NOT NULL DEFAULT '',
  `setting_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`plugin_id`,`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Plugin_Show_Log`
--

DROP TABLE IF EXISTS `Model_Plugin_Show_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Plugin_Show_Log` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `performer_activity_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`user_id`,`performer_activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Requests`
--

DROP TABLE IF EXISTS `Model_Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Requests` (
  `id` int(11) unsigned NOT NULL,
  `stage_name` varchar(50) NOT NULL DEFAULT '',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `birthdate` date NOT NULL DEFAULT '0000-00-00',
  `email` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `service` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `category` int(10) unsigned NOT NULL DEFAULT 0,
  `num_perf` int(10) unsigned NOT NULL DEFAULT 1,
  `studio_location` varchar(50) NOT NULL DEFAULT '0',
  `national_type_id` varchar(50) NOT NULL DEFAULT '0',
  `national_type_value` varchar(50) NOT NULL DEFAULT '',
  `interactive` int(10) unsigned NOT NULL DEFAULT 0,
  `credit_rates` varchar(10) NOT NULL DEFAULT '',
  `vod_dynamic` varchar(10) NOT NULL DEFAULT '',
  `apply_vod_rules` varchar(10) NOT NULL DEFAULT '',
  `vod_bulk_credit` varchar(10) NOT NULL DEFAULT '',
  `rate_type` varchar(10) NOT NULL DEFAULT 'flat',
  `rate_per_credit` int(10) unsigned NOT NULL DEFAULT 0,
  `timeframe` varchar(50) NOT NULL DEFAULT 'today',
  `matches` text DEFAULT NULL,
  `studio_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `status` enum('pending','cancel','approved') DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `hold_date` datetime DEFAULT NULL,
  `hold_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Schedule`
--

DROP TABLE IF EXISTS `Model_Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL DEFAULT 0,
  `dow` enum('sun','mon','tue','wed','thu','fri','sat') NOT NULL DEFAULT 'sun',
  `start_time` time NOT NULL DEFAULT '00:00:00',
  `end_time` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1735220 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Webservice_Keys`
--

DROP TABLE IF EXISTS `Model_Webservice_Keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Webservice_Keys` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `security_key` varchar(45) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`model_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models2Tag`
--

DROP TABLE IF EXISTS `Models2Tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models2Tag` (
  `model_id` int(11) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tag_id`,`model_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Badges`
--

DROP TABLE IF EXISTS `Models_Badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Badges` (
  `badge` varchar(50) NOT NULL DEFAULT '',
  `priority` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `category` varchar(35) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `image` varchar(100) NOT NULL DEFAULT '',
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  `service` enum('girls','guys','all') NOT NULL DEFAULT 'all',
  `description` varchar(255) NOT NULL DEFAULT '',
  `email_salesperson` enum('Y','N') NOT NULL DEFAULT 'Y',
  `email_subscribers` enum('Y','N') NOT NULL DEFAULT 'Y',
  `email_model` enum('Y','N') NOT NULL DEFAULT 'Y',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `badge_type` varchar(255) NOT NULL DEFAULT '',
  `threshold` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`badge`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Badges_Categories`
--

DROP TABLE IF EXISTS `Models_Badges_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Badges_Categories` (
  `category` varchar(35) NOT NULL DEFAULT '',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `last_earn_date` date DEFAULT NULL,
  `subcat_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `sort_order` smallint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Badges_Join`
--

DROP TABLE IF EXISTS `Models_Badges_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Badges_Join` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `badge` varchar(30) NOT NULL DEFAULT '',
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `created_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `favorite_flag` tinyint(1) NOT NULL DEFAULT 0,
  `display_status` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`model_id`,`badge`),
  KEY `date_earned` (`date_earned`),
  KEY `display_status` (`display_status`),
  KEY `badge` (`badge`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Badges_Subcategories`
--

DROP TABLE IF EXISTS `Models_Badges_Subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Badges_Subcategories` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `sort_order` smallint(8) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT '',
  `counter` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `css` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Badges_Subcategories_Join`
--

DROP TABLE IF EXISTS `Models_Badges_Subcategories_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Badges_Subcategories_Join` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `subcat_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Bio`
--

DROP TABLE IF EXISTS `Models_Bio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Bio` (
  `model_id` int(11) unsigned NOT NULL,
  `model_status` char(1) NOT NULL DEFAULT 'N',
  `name` varchar(50) NOT NULL DEFAULT '',
  `num_perf` tinyint(4) unsigned NOT NULL DEFAULT 1,
  `sample_image_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `bio_birthdate` date NOT NULL DEFAULT '0000-00-00',
  `average_rating` float(2,1) NOT NULL DEFAULT 0.0,
  `tagline` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(50) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  `height_cm` smallint(3) unsigned NOT NULL DEFAULT 145,
  `weight_kg` smallint(3) unsigned NOT NULL DEFAULT 35,
  `eye_color` enum('blue','brown','grey','green','black','hazel') NOT NULL DEFAULT 'brown',
  `hair_color` enum('brown','blonde','grey','black','red','white','bald','multi-color') NOT NULL DEFAULT 'brown',
  `hair_length` enum('bald','short','average','shoulder','long','very long') NOT NULL DEFAULT 'average',
  `languages` set('en','de','ru','es','pl','cs','fr','it','jp','nl','tl') NOT NULL DEFAULT 'en',
  `ethnicity` enum('mixed','caucasian','pacific','asian','african','native','black','hispanic','other') NOT NULL DEFAULT 'other',
  `cock_type` enum('cut','uncut') NOT NULL DEFAULT 'uncut',
  `cock_cm` tinyint(2) unsigned NOT NULL DEFAULT 10,
  `cup_size` enum('A','B','C','D','DD','DDD','E','EE','EEE','F','FF','FFF','G','GG','GGG') NOT NULL DEFAULT 'C',
  `orientation` enum('straight','bi','gay','lesbian','bi-curious') NOT NULL DEFAULT 'straight',
  `gay_role` enum('top','bottom','vers') NOT NULL DEFAULT 'vers',
  `body_type` enum('athletic','average','muscular','slim','curvy') NOT NULL DEFAULT 'average',
  `body_mods` set('piercing','tattoo','fake_tits') NOT NULL DEFAULT '',
  `pubic_hair` enum('shaved','natural','trimmed','hairy') NOT NULL DEFAULT 'natural',
  `text_likes` text NOT NULL,
  `text_fetishes` text NOT NULL,
  `text_fantasies` text NOT NULL,
  `text_misc` text NOT NULL,
  `text_special` text NOT NULL,
  `search_data` text NOT NULL,
  `power_score` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `percentile` smallint(3) unsigned NOT NULL DEFAULT 100,
  `cph` decimal(11,2) NOT NULL DEFAULT 0.00,
  `conversion_ratio` decimal(11,2) NOT NULL DEFAULT 0.00,
  `review_status` enum('pending','active','rejected','escalate') NOT NULL DEFAULT 'pending',
  `review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `review_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` date NOT NULL DEFAULT '0000-00-00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fan_points` int(11) NOT NULL,
  `text_likes_hash` char(32) NOT NULL,
  `text_fetishes_hash` char(32) NOT NULL,
  `text_fantasies_hash` char(32) NOT NULL,
  `text_misc_hash` char(32) NOT NULL,
  `text_special_hash` char(32) NOT NULL,
  `power_score_xvc` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `percentile_xvc` smallint(3) unsigned NOT NULL DEFAULT 100,
  `power_score_xvt` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `percentile_xvt` smallint(3) unsigned NOT NULL DEFAULT 100,
  PRIMARY KEY (`model_id`),
  KEY `model_status` (`model_status`),
  KEY `name` (`name`),
  KEY `service` (`service`),
  KEY `bio_birthdate` (`bio_birthdate`),
  KEY `average_rating` (`average_rating`),
  KEY `location` (`location`),
  KEY `height_cm` (`height_cm`),
  KEY `weight_kg` (`weight_kg`),
  KEY `eye_color` (`eye_color`),
  KEY `hair_color` (`hair_color`),
  KEY `languages` (`languages`),
  KEY `ethnicity` (`ethnicity`),
  KEY `cock_type` (`cock_type`),
  KEY `cock_cm` (`cock_cm`),
  KEY `cup_size` (`cup_size`),
  KEY `pubic_hair` (`pubic_hair`),
  KEY `orientation` (`orientation`),
  KEY `gay_role` (`gay_role`),
  KEY `body_type` (`body_type`),
  KEY `review_status` (`review_status`),
  KEY `body_mods` (`body_mods`),
  KEY `num_perf` (`num_perf`),
  KEY `hair_length` (`hair_length`),
  KEY `last_login` (`last_login`),
  KEY `text_likes_hash` (`text_likes_hash`),
  KEY `text_fetishes_hash` (`text_fetishes_hash`),
  KEY `text_fantasies_hash` (`text_fantasies_hash`),
  KEY `text_misc_hash` (`text_misc_hash`),
  KEY `text_special_hash` (`text_special_hash`),
  KEY `percentile` (`percentile`),
  FULLTEXT KEY `search_data` (`search_data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Bio_Backup`
--

DROP TABLE IF EXISTS `Models_Bio_Backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Bio_Backup` (
  `model_id` int(11) unsigned NOT NULL,
  `model_status` char(1) NOT NULL DEFAULT 'N',
  `name` varchar(50) NOT NULL DEFAULT '',
  `num_perf` tinyint(4) unsigned NOT NULL DEFAULT 1,
  `sample_image_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `bio_birthdate` date NOT NULL DEFAULT '0000-00-00',
  `average_rating` float(2,1) NOT NULL DEFAULT 0.0,
  `tagline` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(50) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  `height_cm` smallint(3) unsigned NOT NULL DEFAULT 145,
  `weight_kg` smallint(3) unsigned NOT NULL DEFAULT 35,
  `eye_color` enum('blue','brown','grey','green','black','hazel') NOT NULL DEFAULT 'brown',
  `hair_color` enum('brown','blonde','grey','black','red','white','bald','multi-color') NOT NULL DEFAULT 'brown',
  `hair_length` enum('bald','short','average','shoulder','long','very long') NOT NULL DEFAULT 'average',
  `languages` set('en','de','ru','es','pl','cs','fr','it','jp','nl','tl') NOT NULL DEFAULT 'en',
  `ethnicity` enum('mixed','caucasian','pacific','asian','african','native','black','hispanic','other') NOT NULL DEFAULT 'other',
  `cock_type` enum('cut','uncut') NOT NULL DEFAULT 'uncut',
  `cock_cm` tinyint(2) unsigned NOT NULL DEFAULT 10,
  `cup_size` enum('A','B','C','D','DD','DDD','E','EE','EEE','F','FF','FFF','G','GG','GGG') NOT NULL DEFAULT 'C',
  `orientation` enum('straight','bi','gay','lesbian','bi-curious') NOT NULL DEFAULT 'straight',
  `gay_role` enum('top','bottom','vers') NOT NULL DEFAULT 'vers',
  `body_type` enum('athletic','average','muscular','slim','curvy') NOT NULL DEFAULT 'average',
  `body_mods` set('piercing','tattoo','fake_tits') NOT NULL DEFAULT '',
  `pubic_hair` enum('shaved','natural','trimmed','hairy') NOT NULL DEFAULT 'natural',
  `text_likes` text NOT NULL,
  `text_fetishes` text NOT NULL,
  `text_fantasies` text NOT NULL,
  `text_misc` text NOT NULL,
  `text_special` text NOT NULL,
  `search_data` text NOT NULL,
  `power_score` mediumint(8) NOT NULL DEFAULT 0,
  `percentile` smallint(3) unsigned NOT NULL DEFAULT 100,
  `cph` decimal(11,2) NOT NULL DEFAULT 0.00,
  `conversion_ratio` decimal(11,2) NOT NULL DEFAULT 0.00,
  `review_status` enum('pending','active','rejected','escalate') NOT NULL DEFAULT 'pending',
  `review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `review_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` date NOT NULL DEFAULT '0000-00-00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fan_points` int(11) NOT NULL,
  `text_likes_hash` char(32) NOT NULL,
  `text_fetishes_hash` char(32) NOT NULL,
  `text_fantasies_hash` char(32) NOT NULL,
  `text_misc_hash` char(32) NOT NULL,
  `text_special_hash` char(32) NOT NULL,
  `power_score_xvc` mediumint(8) NOT NULL DEFAULT 0,
  `percentile_xvc` smallint(3) unsigned NOT NULL DEFAULT 100,
  `power_score_xvt` mediumint(8) NOT NULL DEFAULT 0,
  `percentile_xvt` smallint(3) unsigned NOT NULL DEFAULT 100,
  PRIMARY KEY (`model_id`),
  KEY `model_status` (`model_status`),
  KEY `name` (`name`),
  KEY `service` (`service`),
  KEY `bio_birthdate` (`bio_birthdate`),
  KEY `average_rating` (`average_rating`),
  KEY `location` (`location`),
  KEY `height_cm` (`height_cm`),
  KEY `weight_kg` (`weight_kg`),
  KEY `eye_color` (`eye_color`),
  KEY `hair_color` (`hair_color`),
  KEY `languages` (`languages`),
  KEY `ethnicity` (`ethnicity`),
  KEY `cock_type` (`cock_type`),
  KEY `cock_cm` (`cock_cm`),
  KEY `cup_size` (`cup_size`),
  KEY `pubic_hair` (`pubic_hair`),
  KEY `orientation` (`orientation`),
  KEY `gay_role` (`gay_role`),
  KEY `body_type` (`body_type`),
  KEY `review_status` (`review_status`),
  KEY `body_mods` (`body_mods`),
  KEY `num_perf` (`num_perf`),
  KEY `hair_length` (`hair_length`),
  KEY `last_login` (`last_login`),
  KEY `text_likes_hash` (`text_likes_hash`),
  KEY `text_fetishes_hash` (`text_fetishes_hash`),
  KEY `text_fantasies_hash` (`text_fantasies_hash`),
  KEY `text_misc_hash` (`text_misc_hash`),
  KEY `text_special_hash` (`text_special_hash`),
  KEY `percentile` (`percentile`),
  FULLTEXT KEY `search_data` (`search_data`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Bio_Likes_WL`
--

DROP TABLE IF EXISTS `Models_Bio_Likes_WL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Bio_Likes_WL` (
  `model_id` int(11) unsigned NOT NULL,
  `text_likes` text NOT NULL,
  `text_fetishes` text NOT NULL,
  `text_fantasies` text NOT NULL,
  `text_misc` text NOT NULL,
  `text_special` text NOT NULL,
  `text_likes_hash` char(32) NOT NULL DEFAULT '',
  `text_fetishes_hash` char(32) NOT NULL DEFAULT '',
  `text_fantasies_hash` char(32) NOT NULL DEFAULT '',
  `text_misc_hash` char(32) NOT NULL DEFAULT '',
  `text_special_hash` char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`),
  KEY `text_likes_hash` (`text_likes_hash`),
  KEY `text_fetishes_hash` (`text_fetishes_hash`),
  KEY `text_fantasies_hash` (`text_fantasies_hash`),
  KEY `text_misc_hash` (`text_misc_hash`),
  KEY `text_special_hash` (`text_special_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Bio_Settings`
--

DROP TABLE IF EXISTS `Models_Bio_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Bio_Settings` (
  `model_id` int(11) NOT NULL DEFAULT 0,
  `setting_key` varchar(40) NOT NULL DEFAULT '',
  `setting_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Change_Log`
--

DROP TABLE IF EXISTS `Models_Change_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Change_Log` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action` varchar(255) NOT NULL DEFAULT '',
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_0`
--

DROP TABLE IF EXISTS `Models_Comments_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_0` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1939851 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_1`
--

DROP TABLE IF EXISTS `Models_Comments_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1929643 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_2`
--

DROP TABLE IF EXISTS `Models_Comments_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1896944 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_3`
--

DROP TABLE IF EXISTS `Models_Comments_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1881634 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_4`
--

DROP TABLE IF EXISTS `Models_Comments_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_4` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1831226 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_5`
--

DROP TABLE IF EXISTS `Models_Comments_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_5` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1892739 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_6`
--

DROP TABLE IF EXISTS `Models_Comments_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_6` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1950211 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_7`
--

DROP TABLE IF EXISTS `Models_Comments_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_7` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1884540 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_8`
--

DROP TABLE IF EXISTS `Models_Comments_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_8` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1927774 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Comments_9`
--

DROP TABLE IF EXISTS `Models_Comments_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Comments_9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_important` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `comment_type` enum('general','open_room_review','bio_notes') DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`),
  KEY `comment_type` (`comment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1928391 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Configs`
--

DROP TABLE IF EXISTS `Models_Configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Configs` (
  `model_id` int(11) unsigned NOT NULL,
  `json` text DEFAULT NULL,
  `associated_secure_data` text DEFAULT NULL,
  UNIQUE KEY `model_id_UNIQUE` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Conversion_Log`
--

DROP TABLE IF EXISTS `Models_Conversion_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Conversion_Log` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) DEFAULT NULL,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `minutes_online` int(5) unsigned NOT NULL DEFAULT 0,
  `minutes_private` int(5) unsigned NOT NULL DEFAULT 0,
  `week_end` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`model_id`,`week_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Dismiss_Log`
--

DROP TABLE IF EXISTS `Models_Dismiss_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Dismiss_Log` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Friends`
--

DROP TABLE IF EXISTS `Models_Friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Friends` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `friend_id` int(11) unsigned NOT NULL DEFAULT 0,
  `friend_type` enum('model','user') NOT NULL DEFAULT 'model',
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`model_id`,`friend_id`,`friend_type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_In_Training`
--

DROP TABLE IF EXISTS `Models_In_Training`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_In_Training` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `trainer_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `date_training_started` date DEFAULT NULL,
  `average_monthly_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `program_time` enum('6','12') NOT NULL DEFAULT '12',
  `has_training` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `trainer_id` (`trainer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_In_Training_Credits`
--

DROP TABLE IF EXISTS `Models_In_Training_Credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_In_Training_Credits` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `month` date NOT NULL DEFAULT '0000-00-00',
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `trainer_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `date_training_started` date DEFAULT NULL,
  `average_monthly_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `program_time` enum('6','12') NOT NULL DEFAULT '12',
  `show_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `gift_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `vod_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `fanclub_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `total_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `cph` smallint(5) unsigned NOT NULL DEFAULT 0,
  `increase` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `month_model` (`month`,`model_id`),
  KEY `trainer` (`trainer_id`,`month`)
) ENGINE=InnoDB AUTO_INCREMENT=307 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Individual_Tipping`
--

DROP TABLE IF EXISTS `Models_Individual_Tipping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Individual_Tipping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` mediumint(8) NOT NULL DEFAULT 0,
  `admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  `model_id` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','not_set') NOT NULL DEFAULT 'not_set',
  `date_created` datetime DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `model_info` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id_UNIQUE` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35938 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Lists`
--

DROP TABLE IF EXISTS `Models_Lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Lists` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `theme` varchar(20) NOT NULL DEFAULT 'basic',
  `style` enum('ordered','bullet') NOT NULL DEFAULT 'bullet',
  `display_bio` enum('Y','N') NOT NULL DEFAULT 'Y',
  `display_panel` enum('Y','N') NOT NULL DEFAULT 'N',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9146 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Lists_Elements`
--

DROP TABLE IF EXISTS `Models_Lists_Elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Lists_Elements` (
  `element_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(11) unsigned NOT NULL DEFAULT 0,
  `item` varchar(40) NOT NULL DEFAULT '',
  `position` tinyint(2) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`element_id`)
) ENGINE=InnoDB AUTO_INCREMENT=87410 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Metadata`
--

DROP TABLE IF EXISTS `Models_Metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Metadata` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `identifier` varchar(20) NOT NULL DEFAULT '',
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_desc` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Party_Goals`
--

DROP TABLE IF EXISTS `Models_Party_Goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Party_Goals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `goal1_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `goal1_description` varchar(20) NOT NULL DEFAULT '',
  `goal2_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `goal2_description` varchar(20) NOT NULL DEFAULT '',
  `goal3_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `goal3_description` varchar(20) NOT NULL DEFAULT '',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=147598 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Schedule`
--

DROP TABLE IF EXISTS `Models_Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Schedule` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `performer_login_type_id` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `room_name` varchar(255) NOT NULL DEFAULT '',
  KEY `performer_login_type_id` (`performer_login_type_id`),
  KEY `model_id` (`model_id`),
  KEY `studio` (`studio`),
  KEY `date_start` (`date_start`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Social_Join`
--

DROP TABLE IF EXISTS `Models_Social_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Social_Join` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `social_type` varchar(20) NOT NULL DEFAULT '',
  `social_handle` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`social_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Social_Types`
--

DROP TABLE IF EXISTS `Models_Social_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Social_Types` (
  `social_type` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(25) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`social_type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Spin_Wheel`
--

DROP TABLE IF EXISTS `Models_Spin_Wheel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Spin_Wheel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(15) DEFAULT NULL,
  `price` mediumint(8) unsigned DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `platform` (`platform`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=229801 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Spin_Wheel_Options`
--

DROP TABLE IF EXISTS `Models_Spin_Wheel_Options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Spin_Wheel_Options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `spin_wheel_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `spin_wheel_id` (`spin_wheel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=391911 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Temp_Boosts`
--

DROP TABLE IF EXISTS `Models_Temp_Boosts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Temp_Boosts` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `expire_ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Tip_Action`
--

DROP TABLE IF EXISTS `Models_Tip_Action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Tip_Action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(20) NOT NULL DEFAULT '',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `platform` (`platform`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=176842 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Tip_Action_Options`
--

DROP TABLE IF EXISTS `Models_Tip_Action_Options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Tip_Action_Options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tip_action_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(20) NOT NULL DEFAULT '',
  `price` mediumint(8) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tip_action_id` (`tip_action_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1768411 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_User_Notes`
--

DROP TABLE IF EXISTS `Models_User_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_User_Notes` (
  `user_id` int(11) unsigned NOT NULL,
  `screen_name_lower` varchar(32) NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `note` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_edits` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`screen_name_lower`,`model_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_User_Stats`
--

DROP TABLE IF EXISTS `Models_User_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_User_Stats` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `low` smallint(4) unsigned NOT NULL DEFAULT 0,
  `high` smallint(4) unsigned NOT NULL DEFAULT 0,
  `average` float(6,2) unsigned NOT NULL DEFAULT 0.00,
  `num_minutes` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_VOD_Metadata`
--

DROP TABLE IF EXISTS `Models_VOD_Metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_VOD_Metadata` (
  `model_id` int(11) unsigned NOT NULL,
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_description` text NOT NULL,
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Versus`
--

DROP TABLE IF EXISTS `Models_Versus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Versus` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `versus_type_id` tinyint(3) unsigned NOT NULL,
  `from_model_id` int(10) unsigned NOT NULL,
  `to_model_id` int(10) unsigned NOT NULL,
  `datetime_start` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `datetime_last_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('pending','accepted','rejected','cancelled') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Versus_Types`
--

DROP TABLE IF EXISTS `Models_Versus_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Versus_Types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Video_Stats`
--

DROP TABLE IF EXISTS `Models_Video_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Video_Stats` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `studio_code` char(12) DEFAULT NULL,
  `stream_name` varchar(55) NOT NULL DEFAULT '',
  `frame_rate` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `gop_size` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `data_rate` float(5,2) unsigned NOT NULL DEFAULT 0.00,
  `audio` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `audio_rate` float(5,2) unsigned DEFAULT NULL,
  `dimensions_w` smallint(4) unsigned NOT NULL DEFAULT 0,
  `dimensions_h` smallint(4) unsigned NOT NULL DEFAULT 0,
  `bitrate` smallint(4) unsigned NOT NULL DEFAULT 0,
  `image_quality` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `image_constant` enum('quality','bitrate') NOT NULL DEFAULT 'bitrate',
  PRIMARY KEY (`model_id`,`datetime`),
  KEY `data_rate` (`data_rate`),
  KEY `studio_code` (`studio_code`),
  KEY `frame_rate` (`frame_rate`),
  KEY `gop_size` (`gop_size`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Videos`
--

DROP TABLE IF EXISTS `Models_Videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Videos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `video_type` varchar(25) NOT NULL DEFAULT 'generic',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `video_codec` tinyint(1) unsigned NOT NULL DEFAULT 3,
  `width` smallint(5) unsigned NOT NULL DEFAULT 320,
  `height` smallint(5) unsigned NOT NULL DEFAULT 240,
  `length` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `screencap_ts` time NOT NULL DEFAULT '00:00:00',
  `screencap_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_status` enum('escalate','pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_comments` text DEFAULT NULL,
  `status` enum('active','inactive','pending_deleted','deleted') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `review_status` (`review_status`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=333034 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Videos_Cues`
--

DROP TABLE IF EXISTS `Models_Videos_Cues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Videos_Cues` (
  `model_video_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT '',
  `timestamp` time NOT NULL DEFAULT '00:00:00',
  `end_timestamp` time NOT NULL DEFAULT '00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text DEFAULT NULL,
  PRIMARY KEY (`model_video_id`,`type`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Videos_Types`
--

DROP TABLE IF EXISTS `Models_Videos_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Videos_Types` (
  `video_type` varchar(25) NOT NULL DEFAULT '',
  `video_type_char` char(1) NOT NULL,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `max_length` smallint(4) unsigned NOT NULL DEFAULT 30,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  UNIQUE KEY `video_type` (`video_type`),
  UNIQUE KEY `video_type_char` (`video_type_char`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Videos_Upload`
--

DROP TABLE IF EXISTS `Models_Videos_Upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Videos_Upload` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `video_type` varchar(25) NOT NULL DEFAULT '',
  `url` text NOT NULL,
  `server` varchar(64) NOT NULL DEFAULT '',
  `status` varchar(12) NOT NULL DEFAULT '',
  `process_count` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `server` (`server`)
) ENGINE=InnoDB AUTO_INCREMENT=346 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Wishlist_Join`
--

DROP TABLE IF EXISTS `Models_Wishlist_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Wishlist_Join` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `wishlist_id` varchar(30) NOT NULL DEFAULT '',
  `wishlist_type` varchar(30) NOT NULL DEFAULT '',
  `login_hash` varchar(255) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`wishlist_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Money_Network_Balance`
--

DROP TABLE IF EXISTS `Money_Network_Balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Money_Network_Balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `amount` float(10,2) DEFAULT 0.00,
  `balance` float(10,2) DEFAULT 0.00,
  `type` enum('in','out') DEFAULT 'in',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4242 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Monitor_Webservice_Keys`
--

DROP TABLE IF EXISTS `Monitor_Webservice_Keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Monitor_Webservice_Keys` (
  `admin_id` mediumint(8) unsigned NOT NULL,
  `security_key` varchar(45) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`admin_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `New_Performer_Low_CPM_AB`
--

DROP TABLE IF EXISTS `New_Performer_Low_CPM_AB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `New_Performer_Low_CPM_AB` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `test_group` enum('control','test') DEFAULT NULL,
  `datetime_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_test_optout` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3690 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Password_Reset_Request`
--

DROP TABLE IF EXISTS `Password_Reset_Request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Password_Reset_Request` (
  `account_id` int(11) NOT NULL DEFAULT 0,
  `account_type` enum('model','studio','prospect') NOT NULL DEFAULT 'studio',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `status` enum('pending','complete','failed') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hashed_token` varchar(64) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hashed_token`),
  KEY `datetime` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Adjustments`
--

DROP TABLE IF EXISTS `Payment_Adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Adjustments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned DEFAULT NULL,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `amount` float(10,2) NOT NULL DEFAULT 0.00,
  `gl_code` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `description` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_applicable` date NOT NULL DEFAULT '0000-00-00',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `model_id` (`model_id`),
  KEY `studio_code` (`studio_code`),
  KEY `adjustment_search` (`period_id`,`broadcaster_id`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=292849 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Adjustments_GL_Codes`
--

DROP TABLE IF EXISTS `Payment_Adjustments_GL_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Adjustments_GL_Codes` (
  `id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Advances`
--

DROP TABLE IF EXISTS `Payment_Advances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Advances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `payment_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_dollars` float(10,2) NOT NULL DEFAULT 0.00,
  `comm_pct` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  `net_dollars` float(10,2) NOT NULL DEFAULT 0.00,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `period_id` (`period_id`,`broadcaster_id`),
  KEY `broadcaster_id` (`broadcaster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4478 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Details`
--

DROP TABLE IF EXISTS `Payment_Details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `pay_period_id` smallint(4) NOT NULL DEFAULT 0,
  `commission_total` float(10,2) NOT NULL DEFAULT 0.00,
  `manual_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `violations` float(10,2) NOT NULL DEFAULT 0.00,
  `violation_refunds` float(10,2) NOT NULL DEFAULT 0.00,
  `plan_id` smallint(5) NOT NULL DEFAULT 0,
  `plan_level` enum('broadcaster','studio','model') NOT NULL DEFAULT 'broadcaster',
  `plan_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `bonus_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `vod_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `plan_credits` mediumint(7) NOT NULL DEFAULT 0,
  `bonus_credits` mediumint(7) NOT NULL DEFAULT 0,
  `plan_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `vod_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_plan_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_bonus_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_vod_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_commission_net_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `display_commission_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `display_plan_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_vod_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_total_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `is_current_bc` enum('Y','N') NOT NULL DEFAULT 'N',
  `studio_location_id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `percentile` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `broadcast_country` varchar(2) NOT NULL DEFAULT '',
  `seconds_online` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_break` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_show` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_fake` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `num_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unique_customers` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `vod_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `gift_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `gift_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `fanclub_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_perk_credits` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_phone_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_phone_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_sms_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_sms_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `photo_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `photo_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `credit_adjustments` mediumint(7) NOT NULL DEFAULT 0,
  `total_credits` mediumint(7) NOT NULL DEFAULT 0,
  `gross_sales` float(10,2) NOT NULL DEFAULT 0.00,
  `net_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `on_f4f` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvc` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvt` enum('Y','N') NOT NULL DEFAULT 'Y',
  `fanclub_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `fanclub_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_credits` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_referred_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `vod_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `vod_referred_credits` mediumint(7) NOT NULL DEFAULT 0,
  `vod_referred_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `on_club` enum('Y','N') NOT NULL DEFAULT 'N',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `on_asia` enum('Y','N') NOT NULL DEFAULT 'N',
  `payout_method_fee` float(10,2) NOT NULL DEFAULT 0.00,
  `model_access_image_message_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `model_access_image_message_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `model_access_text_message_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `model_access_text_message_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `model_access_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `model_access_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_model_access_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_model_access_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `premium_conversion_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `premium_conversion_credits` mediumint(7) NOT NULL DEFAULT 0,
  `premium_conversion_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_premium_conversion_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_premium_conversion_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `model_access_video_message_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `model_access_video_message_refunds` mediumint(7) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`pay_period_id`,`studio`,`broadcaster_id`,`platform`),
  KEY `broadcaster` (`broadcaster_id`,`pay_period_id`),
  KEY `studio` (`studio`,`pay_period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1059762 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Details_Progressive`
--

DROP TABLE IF EXISTS `Payment_Details_Progressive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Details_Progressive` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `pay_period_id` smallint(4) NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `plan_id` smallint(5) NOT NULL DEFAULT 0,
  `seconds_online` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_break` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_show` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_fake` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `dm_plus_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `dm_plus_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `fanclub_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_phone_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_phone_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `gift_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `gift_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `show_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `vod_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `violations` float(10,2) NOT NULL DEFAULT 0.00,
  `violation_refunds` float(10,2) NOT NULL DEFAULT 0.00,
  `manual_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `net_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `member_bonus_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `member_bonus_commission_rate` float(5,2) DEFAULT NULL,
  `member_bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `premium_conversion_credits` mediumint(7) NOT NULL DEFAULT 0,
  `premium_conversion_commission_rate` float(5,2) DEFAULT NULL,
  `premium_conversion_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `progressive_net_credits` mediumint(7) unsigned NOT NULL DEFAULT 0 COMMENT 'Credits earned after applying the rate from the scale',
  `progressive_total_credits` int(10) NOT NULL DEFAULT 0 COMMENT 'Total number of credits the model earned for the period',
  `progressive_total_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `progressive_level` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Tier the model reached at the time of payment',
  `progressive_rate` float(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Rate associated with the tier the model reached at the time of payment',
  `progressive_effective_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `progressive_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `lifetime_total_credits` int(8) unsigned NOT NULL DEFAULT 0,
  `total_commission` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`pay_period_id`,`studio`,`broadcaster_id`,`platform`),
  KEY `broadcaster` (`broadcaster_id`,`pay_period_id`),
  KEY `studio` (`studio`,`pay_period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=372065 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Details_Progressive_Sim`
--

DROP TABLE IF EXISTS `Payment_Details_Progressive_Sim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Details_Progressive_Sim` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `pay_period_id` smallint(4) NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `plan_id` smallint(5) NOT NULL DEFAULT 0,
  `seconds_online` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_break` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_show` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_fake` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `dm_plus_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `dm_plus_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `fanclub_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_phone_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_phone_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `gift_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `gift_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `show_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `vod_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `violations` float(10,2) NOT NULL DEFAULT 0.00,
  `violation_refunds` float(10,2) NOT NULL DEFAULT 0.00,
  `manual_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `net_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `member_bonus_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `member_bonus_commission_rate` float(5,2) DEFAULT NULL,
  `member_bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `premium_conversion_credits` mediumint(7) NOT NULL DEFAULT 0,
  `premium_conversion_commission_rate` float(5,2) DEFAULT NULL,
  `premium_conversion_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `progressive_net_credits` mediumint(7) unsigned NOT NULL DEFAULT 0 COMMENT 'Credits earned after applying the rate from the scale',
  `progressive_total_credits` int(10) NOT NULL DEFAULT 0 COMMENT 'Total number of credits the model earned for the period',
  `progressive_total_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `progressive_level` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Tier the model reached at the time of payment',
  `progressive_rate` float(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Rate associated with the tier the model reached at the time of payment',
  `progressive_effective_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `progressive_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `lifetime_total_credits` int(8) unsigned NOT NULL DEFAULT 0,
  `total_commission` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`pay_period_id`,`studio`,`broadcaster_id`,`platform`),
  KEY `broadcaster` (`broadcaster_id`,`pay_period_id`),
  KEY `studio` (`studio`,`pay_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Details_Sim`
--

DROP TABLE IF EXISTS `Payment_Details_Sim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Details_Sim` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `pay_period_id` smallint(4) NOT NULL DEFAULT 0,
  `commission_total` float(10,2) NOT NULL DEFAULT 0.00,
  `manual_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `violations` float(10,2) NOT NULL DEFAULT 0.00,
  `violation_refunds` float(10,2) NOT NULL DEFAULT 0.00,
  `plan_id` smallint(5) NOT NULL DEFAULT 0,
  `plan_level` enum('broadcaster','studio','model') NOT NULL DEFAULT 'broadcaster',
  `plan_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `bonus_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `vod_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `plan_credits` mediumint(7) NOT NULL DEFAULT 0,
  `bonus_credits` mediumint(7) NOT NULL DEFAULT 0,
  `plan_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `vod_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_plan_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_bonus_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_vod_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_commission_net_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `display_commission_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `display_plan_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_vod_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_total_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `is_current_bc` enum('Y','N') NOT NULL DEFAULT 'N',
  `studio_location_id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `percentile` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `broadcast_country` varchar(2) NOT NULL DEFAULT '',
  `seconds_online` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_break` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_show` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_fake` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `num_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unique_customers` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `vod_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `gift_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `gift_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `fanclub_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_perk_credits` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_phone_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_phone_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_sms_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_sms_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `photo_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `photo_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `credit_adjustments` mediumint(7) NOT NULL DEFAULT 0,
  `total_credits` mediumint(7) NOT NULL DEFAULT 0,
  `gross_sales` float(10,2) NOT NULL DEFAULT 0.00,
  `net_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `payout_method_fee` float(10,2) NOT NULL DEFAULT 0.00,
  `on_f4f` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvc` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvt` enum('Y','N') NOT NULL DEFAULT 'Y',
  `fanclub_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `fanclub_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_credits` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_referred_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `vod_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `vod_referred_credits` mediumint(7) NOT NULL DEFAULT 0,
  `vod_referred_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `on_club` enum('Y','N') NOT NULL DEFAULT 'N',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `on_asia` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`pay_period_id`,`studio`,`broadcaster_id`,`platform`),
  KEY `broadcaster` (`broadcaster_id`,`pay_period_id`),
  KEY `studio` (`studio`,`pay_period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=181049 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Details_Sim_Archive`
--

DROP TABLE IF EXISTS `Payment_Details_Sim_Archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Details_Sim_Archive` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `pay_period_id` smallint(4) NOT NULL DEFAULT 0,
  `commission_total` float(10,2) NOT NULL DEFAULT 0.00,
  `manual_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `violations` float(10,2) NOT NULL DEFAULT 0.00,
  `violation_refunds` float(10,2) NOT NULL DEFAULT 0.00,
  `plan_id` smallint(5) NOT NULL DEFAULT 0,
  `plan_level` enum('broadcaster','studio','model') NOT NULL DEFAULT 'broadcaster',
  `plan_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `bonus_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `vod_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `plan_credits` mediumint(7) NOT NULL DEFAULT 0,
  `bonus_credits` mediumint(7) NOT NULL DEFAULT 0,
  `plan_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `vod_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_plan_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_bonus_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_vod_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_commission_net_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `display_commission_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `display_plan_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_vod_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_total_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `is_current_bc` enum('Y','N') NOT NULL DEFAULT 'N',
  `studio_location_id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `percentile` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `broadcast_country` varchar(2) NOT NULL DEFAULT '',
  `seconds_online` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_break` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_show` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_fake` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `num_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unique_customers` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `vod_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `gift_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `gift_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `fanclub_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_perk_credits` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_phone_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_phone_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_sms_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_sms_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `photo_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `photo_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `credit_adjustments` mediumint(7) NOT NULL DEFAULT 0,
  `total_credits` mediumint(7) NOT NULL DEFAULT 0,
  `gross_sales` float(10,2) NOT NULL DEFAULT 0.00,
  `net_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `on_f4f` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvc` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvt` enum('Y','N') NOT NULL DEFAULT 'Y',
  `fanclub_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `fanclub_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `fanclub_referred_credits` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_referred_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `vod_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `vod_referred_credits` mediumint(7) NOT NULL DEFAULT 0,
  `vod_referred_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `on_club` enum('Y','N') NOT NULL DEFAULT 'N',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `on_asia` enum('Y','N') NOT NULL DEFAULT 'N',
  `sim_type` varchar(64) NOT NULL DEFAULT '',
  `period_type` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`pay_period_id`,`studio`,`broadcaster_id`,`platform`,`sim_type`),
  KEY `broadcaster` (`broadcaster_id`,`pay_period_id`),
  KEY `studio` (`studio`,`pay_period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11733053 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Details_backup_historical`
--

DROP TABLE IF EXISTS `Payment_Details_backup_historical`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Details_backup_historical` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(4) NOT NULL DEFAULT '',
  `pay_period_id` smallint(4) NOT NULL DEFAULT 0,
  `plan_id` smallint(5) NOT NULL DEFAULT 0,
  `plan_level` enum('broadcaster','studio','model') NOT NULL DEFAULT 'broadcaster',
  `commission_percent` float(5,2) NOT NULL DEFAULT 0.00,
  `is_current_bc` enum('Y','N') NOT NULL DEFAULT 'N',
  `studio_location_id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `percentile` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `broadcast_country` varchar(2) NOT NULL DEFAULT '',
  `seconds_online` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_break` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_show` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `seconds_fake` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `num_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unique_customers` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `show_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `vod_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `gift_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `gift_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `fanclub_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_perk_credits` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_phone_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_phone_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `flirt_sms_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `flirt_sms_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `photo_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `photo_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `bonus_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `credit_adjustments` mediumint(7) NOT NULL DEFAULT 0,
  `total_credits` mediumint(7) NOT NULL DEFAULT 0,
  `gross_sales` float(10,2) NOT NULL DEFAULT 0.00,
  `commission_net_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `commission_bonus_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `commission_rate` float(5,2) NOT NULL DEFAULT 0.00,
  `commission` float(10,2) NOT NULL DEFAULT 0.00,
  `manual_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `tips` float(10,2) NOT NULL DEFAULT 0.00,
  `violations` float(10,2) NOT NULL DEFAULT 0.00,
  `violation_refunds` float(10,2) NOT NULL DEFAULT 0.00,
  `net_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `on_f4f` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvc` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvt` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`pay_period_id`,`studio`),
  KEY `broadcaster` (`broadcaster_id`,`pay_period_id`),
  KEY `studio` (`studio`,`pay_period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21497 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Direct_Deposit_Log`
--

DROP TABLE IF EXISTS `Payment_Direct_Deposit_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Direct_Deposit_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `pay_period_id` int(11) unsigned NOT NULL DEFAULT 0,
  `requested_amount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fee` decimal(4,2) NOT NULL DEFAULT 0.00,
  `date_submitted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','failed','complete') NOT NULL DEFAULT 'pending',
  `comment` text NOT NULL,
  `date_processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `broadcaster_id` (`broadcaster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Instant_Pay_Log`
--

DROP TABLE IF EXISTS `Payment_Instant_Pay_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Instant_Pay_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL,
  `pay_period_id` int(11) unsigned NOT NULL DEFAULT 0,
  `requested_amount` decimal(7,2) NOT NULL DEFAULT 0.00,
  `response_code` varchar(32) NOT NULL,
  `response_string` varchar(255) NOT NULL,
  `payout_method` varchar(20) NOT NULL,
  `date_submitted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `comment` text NOT NULL,
  `date_processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fee` decimal(4,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `model_id` (`model_id`),
  KEY `studio_code` (`studio_code`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `date_processed` (`date_processed`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2946 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Instant_Pay_Overrides`
--

DROP TABLE IF EXISTS `Payment_Instant_Pay_Overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Instant_Pay_Overrides` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` int(11) unsigned NOT NULL DEFAULT 0,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_by_admin_id` mediumint(8) NOT NULL DEFAULT 0,
  `status` enum('pending','failed','complete') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `period_id` (`period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Overrides`
--

DROP TABLE IF EXISTS `Payment_Overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Overrides` (
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `override_type` enum('temp_override','final_payout') NOT NULL DEFAULT 'temp_override',
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_by_admin_id` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`broadcaster_id`,`pay_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Periods`
--

DROP TABLE IF EXISTS `Payment_Periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Periods` (
  `period_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `period_type` enum('bi-weekly','weekly') NOT NULL DEFAULT 'bi-weekly',
  PRIMARY KEY (`period_id`),
  UNIQUE KEY `pay_period_idx` (`start_date`,`end_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2326 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Plans`
--

DROP TABLE IF EXISTS `Payment_Plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Plans` (
  `plan_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `period_type` enum('bi-weekly','weekly') NOT NULL DEFAULT 'bi-weekly',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_progressive` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`plan_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1002 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Plans_Breaks`
--

DROP TABLE IF EXISTS `Payment_Plans_Breaks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Plans_Breaks` (
  `break_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_hours_broadcast` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_sales` int(10) unsigned NOT NULL DEFAULT 0,
  `member_bonus_comm_pct` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  `comm_pct` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`break_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1372 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Plans_Join`
--

DROP TABLE IF EXISTS `Payment_Plans_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Plans_Join` (
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `period_id_start` smallint(5) unsigned NOT NULL DEFAULT 0,
  `period_id_end` smallint(5) unsigned NOT NULL DEFAULT 0,
  `changed_by_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `changed_by_admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`broadcaster_id`,`plan_id`,`period_id_start`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Plans_Join_Migration_Backup`
--

DROP TABLE IF EXISTS `Payment_Plans_Join_Migration_Backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Plans_Join_Migration_Backup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `action` enum('cap','insert') NOT NULL,
  `broadcaster_id` bigint(20) unsigned NOT NULL,
  `plan_id` smallint(5) unsigned NOT NULL,
  `period_id_start` int(10) unsigned NOT NULL,
  `original_period_id_end` int(10) unsigned NOT NULL,
  `new_period_id_end` int(10) unsigned NOT NULL,
  `changed_by_admin_id` bigint(20) unsigned NOT NULL,
  `changed_by_admin_type` enum('admin','studio') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `action` (`action`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `plan_id` (`plan_id`),
  KEY `period_id_start` (`period_id_start`)
) ENGINE=InnoDB AUTO_INCREMENT=2432694 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Split_Requests`
--

DROP TABLE IF EXISTS `Payment_Split_Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Split_Requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `request_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `studio_user_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `request_json` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `broadcaster_id` (`broadcaster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments`
--

DROP TABLE IF EXISTS `Payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `payment_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `comm_pct` decimal(4,2) unsigned NOT NULL DEFAULT 0.00,
  `net_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `paid_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `capture_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `reverse_period_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT 'Indicates that payment was previously marked as paid, but has been reversed.',
  `reverse_date` date DEFAULT NULL COMMENT 'The date when a previously paid payment had been reversed',
  `reverse_admin_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Who initiated a Payout reversal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `period_broadcaster_platform` (`period_id`,`broadcaster_id`,`platform`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `platform` (`platform`),
  KEY `broadcaster_pay_period` (`broadcaster_id`,`pay_period_id`,`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=655335 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments_20240730`
--

DROP TABLE IF EXISTS `Payments_20240730`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments_20240730` (
  `id` int(10) unsigned NOT NULL DEFAULT 0,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `payment_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `comm_pct` decimal(4,2) unsigned NOT NULL DEFAULT 0.00,
  `net_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `paid_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `capture_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `reverse_period_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT 'Indicates that payment was previously marked as paid, but has been reversed.',
  `reverse_date` date DEFAULT NULL COMMENT 'The date when a previously paid payment had been reversed',
  `reverse_admin_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Who initiated a Payout reversal'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments_Backup`
--

DROP TABLE IF EXISTS `Payments_Backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments_Backup` (
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `payment_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_dollars` float(10,2) NOT NULL DEFAULT 0.00,
  `comm_pct` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  `net_dollars` float(10,2) NOT NULL DEFAULT 0.00,
  `final_payment` float(10,2) NOT NULL DEFAULT 0.00,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `paid_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `capture_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`period_id`,`broadcaster_id`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `broadcaster_id` (`broadcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments_Breakdowns_SMOKETEST`
--

DROP TABLE IF EXISTS `Payments_Breakdowns_SMOKETEST`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments_Breakdowns_SMOKETEST` (
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT '',
  `service` enum('girls','guys','combined') NOT NULL DEFAULT 'combined',
  `quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `total_dollars` float(10,2) NOT NULL DEFAULT 0.00,
  `payment_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `description` text NOT NULL,
  `date_imported` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`period_id`,`broadcaster_id`,`type`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments_Breakdowns_TEMP_SMOKETEST`
--

DROP TABLE IF EXISTS `Payments_Breakdowns_TEMP_SMOKETEST`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments_Breakdowns_TEMP_SMOKETEST` (
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT '',
  `service` enum('girls','guys','combined') NOT NULL DEFAULT 'combined',
  `quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `total_dollars` float(10,2) NOT NULL DEFAULT 0.00,
  `payment_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `description` text NOT NULL,
  `date_imported` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`period_id`,`broadcaster_id`,`type`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments_OLD`
--

DROP TABLE IF EXISTS `Payments_OLD`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments_OLD` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `payment_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_dollars` float(10,2) NOT NULL DEFAULT 0.00,
  `comm_pct` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  `net_dollars` float(10,2) NOT NULL DEFAULT 0.00,
  `final_payment` float(10,2) NOT NULL DEFAULT 0.00,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `paid_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `capture_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  UNIQUE KEY `period_broadcaster_platform` (`period_id`,`broadcaster_id`,`platform`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=513678 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments_Progressive`
--

DROP TABLE IF EXISTS `Payments_Progressive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments_Progressive` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `payment_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `comm_pct` decimal(4,2) unsigned NOT NULL DEFAULT 0.00,
  `net_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `paid_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `capture_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `reverse_period_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT 'Indicates that payment was previously marked as paid, but has been reversed.',
  `reverse_date` date DEFAULT NULL COMMENT 'The date when a previously paid payment had been reversed',
  `reverse_admin_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Who initiated a Payout reversal',
  `update_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `period_broadcaster_platform` (`period_id`,`broadcaster_id`,`platform`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=41063 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments_Sim`
--

DROP TABLE IF EXISTS `Payments_Sim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments_Sim` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `payment_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `comm_pct` decimal(4,2) unsigned NOT NULL DEFAULT 0.00,
  `net_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `paid_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `capture_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `reverse_period_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT 'Indicates that payment was previously marked as paid, but has been reversed.',
  `reverse_date` date DEFAULT NULL COMMENT 'The date when a previously paid payment had been reversed',
  `reverse_admin_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Who initiated a Payout reversal',
  `update_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `period_broadcaster_platform` (`period_id`,`broadcaster_id`,`platform`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=29415 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payments_Sim_Archive`
--

DROP TABLE IF EXISTS `Payments_Sim_Archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payments_Sim_Archive` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `broadcaster_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `payment_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `comm_pct` decimal(4,2) unsigned NOT NULL DEFAULT 0.00,
  `net_dollars` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `paid_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `capture_rollover_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `sim_type` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `period_broadcaster_platform` (`period_id`,`broadcaster_id`,`platform`,`sim_type`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `platform` (`platform`),
  KEY `broadcaster_pay_period` (`broadcaster_id`,`sim_type`,`pay_period_id`,`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=2389345 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payout_Method_New_Requests`
--

DROP TABLE IF EXISTS `Payout_Method_New_Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payout_Method_New_Requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `request_type` varchar(30) NOT NULL DEFAULT '',
  `request_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('new','processing','completed','failed') DEFAULT NULL,
  `failed_reason` text DEFAULT NULL,
  `date_completed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `broadcaster_id` (`broadcaster_id`,`request_type`,`status`),
  KEY `request_date` (`request_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1758 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Affiliate_Activity`
--

DROP TABLE IF EXISTS `Performer_Affiliate_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Affiliate_Activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `activity_type` varchar(50) NOT NULL DEFAULT '',
  `ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_bonus_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`),
  KEY `activity_type` (`activity_type`,`ref_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=1974361 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Affiliate_Messages_Queue`
--

DROP TABLE IF EXISTS `Performer_Affiliate_Messages_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Affiliate_Messages_Queue` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `plan_type` enum('regular','guest','basic') NOT NULL DEFAULT 'regular',
  `created_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`),
  KEY `created_datetime` (`created_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=74484 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Affiliate_Plans`
--

DROP TABLE IF EXISTS `Performer_Affiliate_Plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Affiliate_Plans` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `num_bonus_pct` float(3,2) NOT NULL DEFAULT 0.00,
  `plan_type` enum('regular','guest','basic') NOT NULL DEFAULT 'regular',
  `is_default` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Affiliate_Plans_Broadcasters`
--

DROP TABLE IF EXISTS `Performer_Affiliate_Plans_Broadcasters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Affiliate_Plans_Broadcasters` (
  `broadcaster_id` int(11) unsigned NOT NULL DEFAULT 0,
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 1,
  `plan_type` enum('regular','guest','basic') NOT NULL DEFAULT 'regular',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changed_by` enum('affiliate','system','admin') NOT NULL DEFAULT 'system',
  `canged_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`broadcaster_id`,`plan_id`,`plan_type`,`date_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Affiliate_Plans_Models`
--

DROP TABLE IF EXISTS `Performer_Affiliate_Plans_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Affiliate_Plans_Models` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 1,
  `plan_type` enum('regular','guest','basic') NOT NULL DEFAULT 'regular',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changed_by` enum('affiliate','system','admin') NOT NULL DEFAULT 'system',
  `canged_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`plan_id`,`plan_type`,`date_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Affiliate_Plans_Studios`
--

DROP TABLE IF EXISTS `Performer_Affiliate_Plans_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Affiliate_Plans_Studios` (
  `studio` char(12) NOT NULL DEFAULT '',
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 1,
  `plan_type` enum('regular','guest','basic') NOT NULL DEFAULT 'regular',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changed_by` enum('affiliate','system','admin') NOT NULL DEFAULT 'system',
  `canged_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`studio`,`plan_id`,`plan_type`,`date_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Affiliate_Referrals`
--

DROP TABLE IF EXISTS `Performer_Affiliate_Referrals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Affiliate_Referrals` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `plan_type` varchar(25) NOT NULL DEFAULT 'regular',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`,`model_id`,`plan_type`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Affiliate_Referrals_Queue`
--

DROP TABLE IF EXISTS `Performer_Affiliate_Referrals_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Affiliate_Referrals_Queue` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `registration_view_id` int(11) unsigned DEFAULT NULL,
  `datetime_created` datetime DEFAULT NULL,
  `needs_review` tinyint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_view_id_datetime` (`user_id`,`registration_view_id`,`datetime_created`),
  KEY `user_id` (`user_id`),
  KEY `registration_view_id` (`registration_view_id`),
  KEY `datetime_created` (`datetime_created`)
) ENGINE=InnoDB AUTO_INCREMENT=995961 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Bonus_Adjustments`
--

DROP TABLE IF EXISTS `Performer_Bonus_Adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Bonus_Adjustments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `activity_type` varchar(50) NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `ref_id` int(11) unsigned NOT NULL,
  `bonus_adjustment` mediumint(8) DEFAULT NULL,
  `transaction_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item` (`activity_type`,`ref_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Bonus_Winners`
--

DROP TABLE IF EXISTS `Performer_Bonus_Winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Bonus_Winners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `bonus_type` enum('top_models','consistency_bonus') NOT NULL DEFAULT 'top_models',
  `rank` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `credits` int(11) unsigned NOT NULL DEFAULT 0,
  `time` int(11) unsigned NOT NULL DEFAULT 0,
  `payout` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7776 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_PowerScore_Details`
--

DROP TABLE IF EXISTS `Performer_PowerScore_Details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_PowerScore_Details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cph` decimal(11,2) NOT NULL DEFAULT 0.00,
  `num_spenders` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_hours` decimal(6,2) NOT NULL DEFAULT 0.00,
  `num_guest_conv` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_guest_prem_conv` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_favorites` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_notifications` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_photos` smallint(5) unsigned NOT NULL DEFAULT 0,
  `video_intro` enum('Y','N') NOT NULL DEFAULT 'N',
  `flirtu` enum('Y','N') NOT NULL DEFAULT 'N',
  `boosts_admin` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `boosts_users` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `custom_adjustments` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `power_score` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `percentile` smallint(5) unsigned NOT NULL DEFAULT 100,
  `md5_hash` varchar(32) NOT NULL DEFAULT '',
  `percentile_details` text NOT NULL,
  `custom_adjustments_details` text NOT NULL,
  `additional_details` text NOT NULL,
  `power_score_f4f` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `power_score_xvc` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `power_score_xvt` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `percentile_f4f` smallint(5) unsigned NOT NULL DEFAULT 100,
  `percentile_xvc` smallint(5) unsigned NOT NULL DEFAULT 100,
  `percentile_xvt` smallint(5) unsigned NOT NULL DEFAULT 100,
  `custom_adjustments_f4f` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `custom_adjustments_xvc` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `custom_adjustments_xvt` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `on_club` enum('Y','N') NOT NULL DEFAULT 'N',
  `on_asia` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`),
  KEY `service` (`service`),
  KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB AUTO_INCREMENT=153580 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Records`
--

DROP TABLE IF EXISTS `Performer_Records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `record_type` varchar(20) NOT NULL DEFAULT '',
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `record_value` int(11) NOT NULL DEFAULT 0,
  `ref_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_search` (`model_id`,`record_type`),
  KEY `date_earned` (`date_earned`),
  KEY `ref_search` (`record_type`,`ref_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1056499 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performers_Bonus`
--

DROP TABLE IF EXISTS `Performers_Bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performers_Bonus` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `month_end` date NOT NULL DEFAULT '0000-00-00',
  `bonus_type` varchar(50) NOT NULL DEFAULT '',
  `bonus_value` int(11) NOT NULL DEFAULT 0,
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_exported` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`model_id`,`studio`,`month_end`,`bonus_type`),
  KEY `model_month` (`model_id`,`month_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performers_PowerScore_Weights`
--

DROP TABLE IF EXISTS `Performers_PowerScore_Weights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performers_PowerScore_Weights` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item` varchar(30) NOT NULL DEFAULT '',
  `weight` float(3,2) NOT NULL DEFAULT 0.00,
  `site_key` varchar(3) NOT NULL DEFAULT 'f4f',
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_site_key` (`item`,`site_key`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PhotoDNA_Results`
--

DROP TABLE IF EXISTS `PhotoDNA_Results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `PhotoDNA_Results` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `image_type` varchar(50) NOT NULL DEFAULT '',
  `image_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `model_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `api_response_code` int(11) unsigned NOT NULL DEFAULT 0,
  `api_response_message` text NOT NULL,
  `submitted_url` text NOT NULL,
  `submission_tries` int(1) NOT NULL DEFAULT 1,
  `submission_result` enum('success','match','error') NOT NULL DEFAULT 'success',
  `processed_time` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `image_type` (`image_type`),
  KEY `image_id` (`image_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4285075 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Poll_Answers`
--

DROP TABLE IF EXISTS `Poll_Answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Poll_Answers` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `answer` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=586 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Poll_Questions`
--

DROP TABLE IF EXISTS `Poll_Questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Poll_Questions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `user_type` enum('all','model','studio') NOT NULL DEFAULT 'model',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Poll_Results`
--

DROP TABLE IF EXISTS `Poll_Results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Poll_Results` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_type` enum('model','studio') NOT NULL DEFAULT 'model',
  `poll_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `answer_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`,`user_type`,`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Power_Score_ByRegion`
--

DROP TABLE IF EXISTS `Power_Score_ByRegion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Power_Score_ByRegion` (
  `model_id` int(11) NOT NULL,
  `service` enum('girls','guys','trans') DEFAULT 'girls',
  `region` varchar(50) NOT NULL DEFAULT '',
  `power_score` mediumint(8) NOT NULL DEFAULT 0,
  `percentile` smallint(3) unsigned NOT NULL DEFAULT 100,
  PRIMARY KEY (`model_id`,`region`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Power_Score_Country2Region`
--

DROP TABLE IF EXISTS `Power_Score_Country2Region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Power_Score_Country2Region` (
  `region` varchar(50) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  KEY `country_code` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Power_Score_Log`
--

DROP TABLE IF EXISTS `Power_Score_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Power_Score_Log` (
  `datetime` datetime DEFAULT NULL,
  `service` varchar(5) DEFAULT NULL,
  `power_score` mediumint(8) unsigned DEFAULT NULL,
  `models` smallint(5) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Powerscore_Starting_Lineup`
--

DROP TABLE IF EXISTS `Powerscore_Starting_Lineup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Powerscore_Starting_Lineup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` mediumint(8) NOT NULL DEFAULT 0,
  `studio_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`model_id`,`studio_admin_id`),
  KEY `model_id` (`model_id`),
  KEY `studio_admin_id` (`studio_admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=612482 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Private_Message_Log`
--

DROP TABLE IF EXISTS `Private_Message_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Private_Message_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_type` varchar(14) NOT NULL DEFAULT '',
  `sender_name` varchar(32) NOT NULL DEFAULT '',
  `recipient_name` varchar(32) NOT NULL DEFAULT '',
  `message` text NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4624515 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prospect_Views_BAK`
--

DROP TABLE IF EXISTS `Prospect_Views_BAK`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prospect_Views_BAK` (
  `tracker` varchar(255) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `signup_tpl` varchar(50) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  KEY `date` (`date`),
  KEY `tracker` (`tracker`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prospects`
--

DROP TABLE IF EXISTS `Prospects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prospects` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  `password` varchar(32) NOT NULL DEFAULT '',
  `enc_password` varchar(64) NOT NULL DEFAULT '',
  `salt` varchar(64) NOT NULL DEFAULT '',
  `company_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(25) DEFAULT NULL,
  `fax` varchar(25) DEFAULT NULL,
  `im_address` varchar(255) NOT NULL DEFAULT '',
  `country_code` char(3) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `geography` text DEFAULT NULL,
  `birthdate` date NOT NULL DEFAULT '0000-00-00',
  `service` enum('girls','guys','trans') DEFAULT 'girls',
  `allow_text_messages` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `preferred_contact_method` enum('phone','text','email','skype','twitter','instagram','whatsapp') NOT NULL DEFAULT 'email',
  `contact_method_info` varchar(64) NOT NULL DEFAULT '0',
  `auth_key` varchar(32) NOT NULL DEFAULT '',
  `experience` text DEFAULT NULL,
  `on_f4f` enum('yes','no') DEFAULT 'no',
  `stage_name` varchar(50) DEFAULT NULL,
  `cameras` text DEFAULT NULL,
  `performers` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `signup_tpl` varchar(50) NOT NULL DEFAULT '',
  `tracker` varchar(255) NOT NULL DEFAULT 'type-in',
  `campaign_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `ref_affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `ref_model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ref_broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `ref_user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `potential` enum('hot','warm','cool','cold','not_assigned') NOT NULL DEFAULT 'not_assigned',
  `status_contract` enum('pending','sent','review','complete') DEFAULT 'pending',
  `status_ipcheck` enum('pending','pass','fail') DEFAULT 'pending',
  `status_final` varchar(30) NOT NULL DEFAULT 'in_progress',
  `signup_origin` enum('legacy','2019-a') DEFAULT 'legacy',
  `email_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `date_email_confirmed` datetime DEFAULT NULL,
  `date_last_updated` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_reminder` date DEFAULT '0000-00-00',
  `last_opened` date NOT NULL DEFAULT '0000-00-00',
  `salesperson_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `studio_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `studio_website` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `tracker` (`tracker`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1145015 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`localhost`*/ /*!50003 TRIGGER STUDIOS.prospects_update BEFORE UPDATE ON STUDIOS.Prospects  
 FOR EACH ROW BEGIN
 
 
  
  IF OLD.status_final != NEW.status_final 
  	AND NEW.status_final != 'in_progress' 
	AND OLD.status = 1 
  THEN
   
     SET NEW.status = 0;
 
   END IF;
  
  
  
  IF OLD.status_final != NEW.status_final 
  	AND NEW.status_final = 'in_progress' 
	AND OLD.status = 0 
  THEN
   
     SET NEW.status = 1;
 
   END IF;

 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Prospects_Applications`
--

DROP TABLE IF EXISTS `Prospects_Applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prospects_Applications` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` varchar(255) NOT NULL,
  `application_status` enum('MISSING_PERSONAL_DATA','PERSONAL_DATA_PENDING_REVIEW','MISSING_DOCUMENTS','DOCUMENTS_PENDING_REVIEW','DOCUMENTS_APPROVED','APPROVED','REJECTED') NOT NULL DEFAULT 'MISSING_PERSONAL_DATA',
  `application_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `is_valid_json` CHECK (json_valid(`application_data`))
) ENGINE=InnoDB AUTO_INCREMENT=427 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prospects_Duplicates`
--

DROP TABLE IF EXISTS `Prospects_Duplicates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prospects_Duplicates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `review_status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `review_comments` text DEFAULT NULL,
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `submission_json` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=253124 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prospects_Ex`
--

DROP TABLE IF EXISTS `Prospects_Ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prospects_Ex` (
  `prospect_id` int(8) unsigned NOT NULL DEFAULT 0,
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`prospect_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prospects_Images`
--

DROP TABLE IF EXISTS `Prospects_Images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prospects_Images` (
  `md5_hash` varchar(32) NOT NULL DEFAULT '',
  `prospect_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `upload_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_size` float(8,2) unsigned NOT NULL DEFAULT 0.00,
  `file_type` varchar(20) NOT NULL DEFAULT 'images/jpeg',
  `purge_requested_datetime` datetime DEFAULT NULL,
  `image_type` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`md5_hash`),
  KEY `prospect_id` (`prospect_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prospects_Notes`
--

DROP TABLE IF EXISTS `Prospects_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prospects_Notes` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `prospect_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`,`prospect_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=3974409 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Psychics_Bio`
--

DROP TABLE IF EXISTS `Psychics_Bio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Psychics_Bio` (
  `model_id` int(11) unsigned NOT NULL,
  `model_status` char(1) NOT NULL DEFAULT 'N',
  `name` varchar(50) NOT NULL DEFAULT '',
  `num_perf` tinyint(4) unsigned NOT NULL DEFAULT 1,
  `sample_image_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `bio_birthdate` date NOT NULL DEFAULT '0000-00-00',
  `average_rating` float(2,1) NOT NULL DEFAULT 1.0,
  `tagline` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(50) NOT NULL DEFAULT '',
  `languages` set('en','de','ru','es','pl','cs','fr','it','jp','nl','tl') NOT NULL DEFAULT 'en',
  `ethnicity` enum('mixed','caucasian','pacific','asian','african','native','black','hispanic','other') NOT NULL DEFAULT 'other',
  `experience` int(11) NOT NULL DEFAULT 1,
  `search_data` text NOT NULL,
  `text_services` text NOT NULL,
  `text_services_hash` char(32) NOT NULL,
  `power_score` mediumint(8) NOT NULL DEFAULT 0,
  `percentile` smallint(3) unsigned NOT NULL DEFAULT 100,
  `review_status` enum('pending','active','rejected','escalate') NOT NULL DEFAULT 'pending',
  `review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `review_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` date NOT NULL DEFAULT '0000-00-00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fan_points` int(11) NOT NULL,
  PRIMARY KEY (`model_id`),
  KEY `model_status` (`model_status`),
  KEY `name` (`name`),
  KEY `service` (`service`),
  KEY `bio_birthdate` (`bio_birthdate`),
  KEY `average_rating` (`average_rating`),
  KEY `location` (`location`),
  KEY `languages` (`languages`),
  KEY `ethnicity` (`ethnicity`),
  KEY `review_status` (`review_status`),
  KEY `num_perf` (`num_perf`),
  KEY `last_login` (`last_login`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Purge_Requests`
--

DROP TABLE IF EXISTS `Purge_Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Purge_Requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) unsigned NOT NULL DEFAULT 0,
  `record_type` enum('model','prospect','broadcasters','studio_admin') DEFAULT NULL,
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `purged_version` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime_purged` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `purge_request` (`record_id`,`record_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Quick_Message`
--

DROP TABLE IF EXISTS `Quick_Message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Quick_Message` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `message_number` tinyint(2) unsigned NOT NULL DEFAULT 1,
  `short_desc` varchar(50) NOT NULL DEFAULT '',
  `message` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`message_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Radiators`
--

DROP TABLE IF EXISTS `Radiators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Radiators` (
  `radiator_key` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `function_option_id` smallint(4) unsigned NOT NULL DEFAULT 25,
  `seconds_refresh` smallint(2) unsigned NOT NULL DEFAULT 30,
  `status` enum('active','inactive','testing') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`radiator_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Radiators_Join`
--

DROP TABLE IF EXISTS `Radiators_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Radiators_Join` (
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `radiator_key` varchar(30) NOT NULL DEFAULT '',
  `seconds_refresh` smallint(2) unsigned NOT NULL DEFAULT 30,
  `num_impressions` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`admin_id`,`radiator_key`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Radiators_Options`
--

DROP TABLE IF EXISTS `Radiators_Options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Radiators_Options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `radiator_key` varchar(30) NOT NULL DEFAULT '',
  `username` varchar(50) NOT NULL DEFAULT '',
  `radiator_type` varchar(32) NOT NULL DEFAULT '',
  `radiator_value` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `radiator_key` (`radiator_key`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4662 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Raffle_Tickets`
--

DROP TABLE IF EXISTS `Raffle_Tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Raffle_Tickets` (
  `ticket_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  UNIQUE KEY `ticket_id` (`ticket_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Rebuild_Total_Credits`
--

DROP TABLE IF EXISTS `Rebuild_Total_Credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Rebuild_Total_Credits` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `total_credits` int(8) unsigned NOT NULL DEFAULT 0,
  `date_transferred` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recorders`
--

DROP TABLE IF EXISTS `Recorders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recorders` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` varchar(5) DEFAULT NULL,
  `make` enum('lite-on','phillips') DEFAULT NULL,
  `location` enum('vs','studio') NOT NULL DEFAULT 'vs',
  `studio` char(12) NOT NULL DEFAULT '',
  `date_shipped` date DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `status` enum('active','broken','repair','dead') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10024 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recorders_Notes`
--

DROP TABLE IF EXISTS `Recorders_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recorders_Notes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `recorder_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `comments` text DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recorder_id` (`recorder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recruiter_Referral_Milestones`
--

DROP TABLE IF EXISTS `Recruiter_Referral_Milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recruiter_Referral_Milestones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recruiter_broadcaster_id` mediumint(8) unsigned NOT NULL,
  `referred_broadcaster_id` mediumint(8) unsigned NOT NULL,
  `referred_model_id` mediumint(8) unsigned NOT NULL,
  `milestone_number` int(10) unsigned NOT NULL,
  `step_credits` int(10) unsigned NOT NULL,
  `threshold_credits` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_adjustment_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_referral_milestone` (`recruiter_broadcaster_id`,`referred_broadcaster_id`,`referred_model_id`,`milestone_number`),
  KEY `recruiter_broadcaster_id` (`recruiter_broadcaster_id`),
  KEY `referred_broadcaster_id` (`referred_broadcaster_id`),
  KEY `referred_model_id` (`referred_model_id`),
  KEY `payment_adjustment_id` (`payment_adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recruitment_Associations`
--

DROP TABLE IF EXISTS `Recruitment_Associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recruitment_Associations` (
  `broadcaster_id` int(11) unsigned NOT NULL DEFAULT 0,
  `period_id_start` smallint(5) unsigned NOT NULL DEFAULT 0,
  `period_id_end` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`broadcaster_id`,`period_id_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recruitment_Plans`
--

DROP TABLE IF EXISTS `Recruitment_Plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recruitment_Plans` (
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `comm_pct` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  `months` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `comm_type` enum('net','gross') NOT NULL DEFAULT 'gross',
  `plan_type` enum('flat','difference') NOT NULL DEFAULT 'flat',
  `period_id_start` smallint(5) unsigned NOT NULL DEFAULT 0,
  `period_id_end` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`broadcaster_id`,`period_id_start`),
  KEY `period_id_end` (`period_id_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Rejection_Reasons`
--

DROP TABLE IF EXISTS `Rejection_Reasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Rejection_Reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `rejection_reason` varchar(255) DEFAULT NULL,
  `review_page` smallint(6) DEFAULT NULL,
  `create_time` datetime DEFAULT current_timestamp() COMMENT 'Create Time',
  `status` tinyint(4) DEFAULT 1,
  `admin_id` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `rejection_reason` (`rejection_reason`),
  KEY `review_page` (`review_page`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Room_Review_Actions`
--

DROP TABLE IF EXISTS `Room_Review_Actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Room_Review_Actions` (
  `action` varchar(32) NOT NULL DEFAULT '',
  `category` enum('shutdown','warning','dismiss','suggestion') NOT NULL DEFAULT 'dismiss',
  `title` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Room_Review_Log`
--

DROP TABLE IF EXISTS `Room_Review_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Room_Review_Log` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('admin','studio') NOT NULL DEFAULT 'admin',
  `action` varchar(32) NOT NULL DEFAULT 'reviewed',
  `comments` text NOT NULL,
  KEY `date` (`date`),
  KEY `datetime` (`datetime`),
  KEY `model_search` (`date`,`model_id`),
  KEY `admin_type` (`admin_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Room_Review_Pending`
--

DROP TABLE IF EXISTS `Room_Review_Pending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Room_Review_Pending` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `minute` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_pending` mediumint(6) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`minute`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Scale_Tiers`
--

DROP TABLE IF EXISTS `Scale_Tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Scale_Tiers` (
  `scale_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_hours_broadcast` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_credits` int(11) unsigned NOT NULL DEFAULT 0,
  `comm_pct` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  KEY `scale_id` (`scale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Scales`
--

DROP TABLE IF EXISTS `Scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Scales` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `studio` char(12) NOT NULL DEFAULT '',
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `date_last_updated` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `studio` (`studio`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3083 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Scales_Models_Join`
--

DROP TABLE IF EXISTS `Scales_Models_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Scales_Models_Join` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `scale_id` int(11) unsigned NOT NULL DEFAULT 0,
  `start_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `end_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `changed_by_admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `changed_by_admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`model_id`,`scale_id`,`start_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Scheduled_Shows_Pending`
--

DROP TABLE IF EXISTS `Scheduled_Shows_Pending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Scheduled_Shows_Pending` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `sample_image_id` int(10) unsigned NOT NULL DEFAULT 0,
  `login_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `recurring_show_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tz_offset` decimal(3,1) NOT NULL DEFAULT 0.0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_model_plugin` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `review_status` enum('pending','cancel','approved') DEFAULT 'pending',
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `booking_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `domain` varchar(255) DEFAULT 'all',
  `sitekey` varchar(20) DEFAULT 'all',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `service` (`service`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=89011 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Search_Likes`
--

DROP TABLE IF EXISTS `Search_Likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Search_Likes` (
  `url_article` varchar(255) NOT NULL DEFAULT '',
  `likes` int(10) unsigned NOT NULL DEFAULT 0,
  `dislikes` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`url_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Solo_Acquisition_Activity`
--

DROP TABLE IF EXISTS `Solo_Acquisition_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Solo_Acquisition_Activity` (
  `date_acquired` date NOT NULL DEFAULT '0000-00-00',
  `date_activity` date NOT NULL DEFAULT '0000-00-00',
  `campaign_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_applied` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_confirmed` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_completed` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `hours_broadcast` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `total_credits` mediumint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`campaign_id`,`date_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Solo_Acquisition_Campaigns`
--

DROP TABLE IF EXISTS `Solo_Acquisition_Campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Solo_Acquisition_Campaigns` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `contact_info` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10079 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Solo_Acquisition_Spending`
--

DROP TABLE IF EXISTS `Solo_Acquisition_Spending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Solo_Acquisition_Spending` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `date_paid` date NOT NULL DEFAULT '0000-00-00',
  `amount` float(7,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) NOT NULL DEFAULT '',
  `created_admin_id` smallint(3) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=25775 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studio_Activity`
--

DROP TABLE IF EXISTS `Studio_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studio_Activity` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studio_Activity_Stream`
--

DROP TABLE IF EXISTS `Studio_Activity_Stream`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studio_Activity_Stream` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `activity_id` smallint(3) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `studio_code` (`studio_code`),
  KEY `activity_id` (`activity_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=8330492 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studio_Activity_Stream_Log`
--

DROP TABLE IF EXISTS `Studio_Activity_Stream_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studio_Activity_Stream_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `studio_activity_stream_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `studio_admin_id` (`studio_admin_id`),
  KEY `studio_activity_stream_id` (`studio_activity_stream_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22099486 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studio_Code_Groups`
--

DROP TABLE IF EXISTS `Studio_Code_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studio_Code_Groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `studio_user_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `studios` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `studio_user_id` (`studio_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=361 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studio_Scale_Tiers`
--

DROP TABLE IF EXISTS `Studio_Scale_Tiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studio_Scale_Tiers` (
  `scale_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_hours_broadcast` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_credits` int(11) unsigned NOT NULL DEFAULT 0,
  `comm_pct` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  KEY `scale_id` (`scale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studio_Scales`
--

DROP TABLE IF EXISTS `Studio_Scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studio_Scales` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `studio` char(12) NOT NULL DEFAULT '',
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `date_last_updated` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `studio` (`studio`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studio_Scales_Join`
--

DROP TABLE IF EXISTS `Studio_Scales_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studio_Scales_Join` (
  `studio` char(12) NOT NULL DEFAULT '',
  `scale_id` int(11) unsigned NOT NULL DEFAULT 0,
  `start_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `end_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `changed_by_admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `changed_by_admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`studio`,`scale_id`,`start_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studios_Comments`
--

DROP TABLE IF EXISTS `Studios_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studios_Comments` (
  `studio` char(12) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('studio','admin') DEFAULT NULL,
  `comment` text NOT NULL,
  KEY `studio_code` (`studio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studios_IPs`
--

DROP TABLE IF EXISTS `Studios_IPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studios_IPs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `studio` char(12) NOT NULL DEFAULT '',
  `ip` varbinary(16) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` varchar(255) DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'Y',
  `added_admin_id` smallint(6) DEFAULT NULL,
  `added_admin_type` enum('studio','admin') NOT NULL DEFAULT 'studio',
  `model_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `studio` (`studio`,`ip`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=283083 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studios_IPs_Access`
--

DROP TABLE IF EXISTS `Studios_IPs_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studios_IPs_Access` (
  `ip_id` int(11) unsigned NOT NULL DEFAULT 0,
  `access_type_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ip_id`,`access_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studios_IPs_AccessTypes`
--

DROP TABLE IF EXISTS `Studios_IPs_AccessTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studios_IPs_AccessTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studios_Reminders`
--

DROP TABLE IF EXISTS `Studios_Reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studios_Reminders` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `date_contact` date NOT NULL DEFAULT '0000-00-00',
  `date_closed` date NOT NULL DEFAULT '0000-00-00',
  `priority` enum('urgent','normal','low') NOT NULL DEFAULT 'normal',
  `comments` text NOT NULL,
  `comments_closed` text NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`),
  KEY `studio` (`studio`,`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23363 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studios_Users`
--

DROP TABLE IF EXISTS `Studios_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studios_Users` (
  `studio` char(12) NOT NULL DEFAULT '',
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`studio`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Suggested_Names`
--

DROP TABLE IF EXISTS `Suggested_Names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Suggested_Names` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `sex` char(1) NOT NULL DEFAULT '',
  `origin` varchar(50) DEFAULT NULL,
  `meaning` varchar(255) DEFAULT NULL,
  `admin_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `date_in` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=454791 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Suggested_Names_Temp`
--

DROP TABLE IF EXISTS `Suggested_Names_Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Suggested_Names_Temp` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `sex` char(1) NOT NULL DEFAULT '',
  `origin` varchar(50) DEFAULT NULL,
  `meaning` varchar(255) DEFAULT NULL,
  `status` enum('pending','valid','delete') DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Survey_Responses`
--

DROP TABLE IF EXISTS `Survey_Responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Survey_Responses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `traffic_to_rooms` int(5) DEFAULT NULL,
  `quality_of_customers` int(5) DEFAULT NULL,
  `customer_service` int(5) DEFAULT NULL,
  `broadcasting_support` int(5) DEFAULT NULL,
  `broadcasting_software` int(5) DEFAULT NULL,
  `bonuses_and_promotions` int(5) DEFAULT NULL,
  `model_registration` int(5) DEFAULT NULL,
  `stats_and_reports` int(5) DEFAULT NULL,
  `keep_whisper` text DEFAULT NULL,
  `complaint_from_performers` text DEFAULT NULL,
  `complaint_from_customers` text DEFAULT NULL,
  `performers_login_more` text DEFAULT NULL,
  `more_performers` text DEFAULT NULL,
  `favorite_contest` text DEFAULT NULL,
  `new_features` text DEFAULT NULL,
  `like_on_our_network` text DEFAULT NULL,
  `dislike_on_our_network` text DEFAULT NULL,
  `change_about_site` text DEFAULT NULL,
  `performer_broadcaster` varchar(20) DEFAULT NULL,
  `current_status` varchar(20) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surveys`
--

DROP TABLE IF EXISTS `Surveys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surveys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `survey_type` varchar(30) NOT NULL DEFAULT 'all',
  `survey_region` enum('Y','N') NOT NULL DEFAULT 'N',
  `notification_type_name` varchar(255) NOT NULL DEFAULT 'BROADCASTING: General PA Survey Recipients',
  `datetime_start` date NOT NULL DEFAULT '0000-00-00',
  `datetime_end` date NOT NULL DEFAULT '0000-00-00',
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surveys_Questions`
--

DROP TABLE IF EXISTS `Surveys_Questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surveys_Questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) unsigned NOT NULL DEFAULT 0,
  `question_type` enum('rating','comment','yes_no') NOT NULL DEFAULT 'rating',
  `question_text` text DEFAULT NULL,
  `display_order` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB AUTO_INCREMENT=897 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surveys_Regions`
--

DROP TABLE IF EXISTS `Surveys_Regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surveys_Regions` (
  `survey_id` int(11) unsigned NOT NULL DEFAULT 0,
  `country_code` varchar(2) NOT NULL DEFAULT '',
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surveys_Submissions`
--

DROP TABLE IF EXISTS `Surveys_Submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surveys_Submissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `submission` text DEFAULT NULL,
  `datetime_submitted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4755 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tax_Service_Fields`
--

DROP TABLE IF EXISTS `Tax_Service_Fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tax_Service_Fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `broadcaster_id` int(11) unsigned NOT NULL DEFAULT 0,
  `form_type` enum('W9','W8-BEN','W8-BEN-E') NOT NULL DEFAULT 'W9',
  `field_name` varchar(255) NOT NULL DEFAULT '',
  `field_value` varchar(255) NOT NULL DEFAULT '',
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=422172 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Temp_Model_Videos_Reencode`
--

DROP TABLE IF EXISTS `Temp_Model_Videos_Reencode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Temp_Model_Videos_Reencode` (
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `video_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` enum('visited','pending') NOT NULL DEFAULT 'pending',
  `encoded` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`admin_id`,`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tip_Target_Contributions`
--

DROP TABLE IF EXISTS `Tip_Target_Contributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tip_Target_Contributions` (
  `tip_target_id` int(11) unsigned NOT NULL DEFAULT 0,
  `gift_transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  KEY `tip_target_id` (`tip_target_id`),
  KEY `gift_transact_id` (`gift_transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tip_Targets`
--

DROP TABLE IF EXISTS `Tip_Targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tip_Targets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `image_id` int(11) unsigned NOT NULL DEFAULT 0,
  `credit_goal` int(11) unsigned NOT NULL DEFAULT 0,
  `credits_earned` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=103643 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Trainers`
--

DROP TABLE IF EXISTS `Trainers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Trainers` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Training_Videos`
--

DROP TABLE IF EXISTS `Training_Videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Training_Videos` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `site_type` enum('adult','psychic','all') NOT NULL DEFAULT 'all',
  `file` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `category_id` smallint(5) NOT NULL DEFAULT 1,
  `num_views` int(10) unsigned NOT NULL DEFAULT 0,
  `video_codec` tinyint(1) unsigned NOT NULL DEFAULT 3,
  `width` smallint(5) unsigned NOT NULL DEFAULT 320,
  `height` smallint(5) unsigned NOT NULL DEFAULT 240,
  `length` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `allow_studio_users` enum('Y','N') NOT NULL DEFAULT 'Y',
  `allow_models` enum('Y','N') NOT NULL DEFAULT 'Y',
  `allow_solo` enum('Y','N') NOT NULL DEFAULT 'Y',
  `allow_adult` enum('Y','N') NOT NULL DEFAULT 'Y',
  `allow_psychic` enum('Y','N') NOT NULL DEFAULT 'Y',
  `xvc_only` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `encode_status` enum('encoded','pending','failed') NOT NULL DEFAULT 'pending',
  `featured` enum('all','limited-esp','limited-rus','none') NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`),
  UNIQUE KEY `file` (`file`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10490 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Training_Videos_Categories`
--

DROP TABLE IF EXISTS `Training_Videos_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Training_Videos_Categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Training_Videos_Views`
--

DROP TABLE IF EXISTS `Training_Videos_Views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Training_Videos_Views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(12) DEFAULT 0,
  `model_id` int(12) DEFAULT 0,
  `play_count` int(4) DEFAULT 0,
  `impression_count` int(4) DEFAULT 0,
  `duration` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13559 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Twitter_Auth_Accounts`
--

DROP TABLE IF EXISTS `Twitter_Auth_Accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Twitter_Auth_Accounts` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `oauth_token` varchar(64) NOT NULL DEFAULT '',
  `oauth_token_secret` varchar(64) NOT NULL DEFAULT '',
  `screen_name` varchar(25) NOT NULL DEFAULT '',
  `is_blocked` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Twitter_Event_Default_Messages`
--

DROP TABLE IF EXISTS `Twitter_Event_Default_Messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Twitter_Event_Default_Messages` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `tweet_text` text NOT NULL,
  `event_type_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `site` enum('XVC','F4F') DEFAULT 'F4F',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=831 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Twitter_Event_Messages`
--

DROP TABLE IF EXISTS `Twitter_Event_Messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Twitter_Event_Messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `tweet_text` text NOT NULL,
  `event_type_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `event_default_messages_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9364253 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Twitter_Event_Permissions`
--

DROP TABLE IF EXISTS `Twitter_Event_Permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Twitter_Event_Permissions` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `event_type_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`model_id`,`event_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Twitter_Event_Types`
--

DROP TABLE IF EXISTS `Twitter_Event_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Twitter_Event_Types` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `frequency_limit` int(6) unsigned NOT NULL DEFAULT 28800,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Twitter_Queued_Tweets`
--

DROP TABLE IF EXISTS `Twitter_Queued_Tweets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Twitter_Queued_Tweets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `tweet_text` text NOT NULL,
  `event_type_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime DEFAULT current_timestamp(),
  `params` text DEFAULT NULL,
  `sitekey` varchar(255) DEFAULT 'F4F',
  `media_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=600971 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Twitter_Tweet_Log`
--

DROP TABLE IF EXISTS `Twitter_Tweet_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Twitter_Tweet_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `tweet_text` text NOT NULL,
  `event_type_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `params` text DEFAULT NULL,
  `status` varchar(255) DEFAULT 'Unknown' COMMENT 'Default is Unknown. All earlier Tweets (< September 2023) have Unknown status because the column was created later. A successfully sent Tweet has the status Success. Other statuses are returned by the Twitter API response. A Tweet can also have Unknown status because it is written to the log before the request takes place.',
  PRIMARY KEY (`id`),
  KEY `event_type_id` (`event_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4735878 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Download_Queue`
--

DROP TABLE IF EXISTS `VOD_Download_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Download_Queue` (
  `recording_id` int(11) NOT NULL DEFAULT 0,
  `temp_vod_download_hash` varchar(80) NOT NULL,
  `studio_user_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(64) NOT NULL DEFAULT '',
  `datetime_requested` datetime NOT NULL DEFAULT current_timestamp(),
  `download_status` varchar(10) NOT NULL DEFAULT 'queued',
  `file_name` varchar(20) NOT NULL DEFAULT '',
  UNIQUE KEY `download_request` (`recording_id`,`studio_user_id`),
  KEY `download_status` (`download_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Packages`
--

DROP TABLE IF EXISTS `VOD_Packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Packages` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `model_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `datetime` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5886 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Packages_Pricing`
--

DROP TABLE IF EXISTS `VOD_Packages_Pricing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Packages_Pricing` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `duration` varchar(255) NOT NULL DEFAULT '',
  `num_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `datetime` (`package_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11814 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Packages_Videos`
--

DROP TABLE IF EXISTS `VOD_Packages_Videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Packages_Videos` (
  `package_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `recording_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`package_id`,`recording_id`),
  KEY `package_id` (`package_id`),
  KEY `recording_id` (`recording_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations`
--

DROP TABLE IF EXISTS `Violations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `studio` char(12) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `penalty` float(6,2) DEFAULT NULL,
  `description` text NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `entered_by` smallint(5) unsigned NOT NULL DEFAULT 0,
  `entered_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reviewed_by` smallint(3) unsigned NOT NULL DEFAULT 0,
  `reviewed_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `exported` tinyint(1) unsigned DEFAULT 0,
  `status` enum('pending','approved','cancelled') NOT NULL DEFAULT 'pending',
  `date_exported` date NOT NULL DEFAULT '0000-00-00',
  `action` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `studio` (`studio`,`model_id`),
  KEY `reviewed_datetime` (`reviewed_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=60677 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Comments`
--

DROP TABLE IF EXISTS `Violations_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Comments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `violation_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  `comments` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `violation_id` (`violation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65699 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Comments_Studios`
--

DROP TABLE IF EXISTS `Violations_Comments_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Comments_Studios` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `violation_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comments` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`),
  KEY `violation_id` (`violation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Credits`
--

DROP TABLE IF EXISTS `Violations_Credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Credits` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `violation_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `exported` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_exported` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `violation_id` (`violation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Credits_Studios`
--

DROP TABLE IF EXISTS `Violations_Credits_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Credits_Studios` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `violation_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `exported` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_exported` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `violation_id` (`violation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Documents`
--

DROP TABLE IF EXISTS `Violations_Documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Documents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `violation_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65183 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Documents_Studios`
--

DROP TABLE IF EXISTS `Violations_Documents_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Documents_Studios` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `violation_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `file_extension` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Id_Type`
--

DROP TABLE IF EXISTS `Violations_Id_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Id_Type` (
  `violation_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `violation_type_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`violation_id`,`violation_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Id_Type_Studios`
--

DROP TABLE IF EXISTS `Violations_Id_Type_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Id_Type_Studios` (
  `violation_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `violation_type_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`violation_id`,`violation_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Studios`
--

DROP TABLE IF EXISTS `Violations_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Studios` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `studio` char(12) NOT NULL DEFAULT '0',
  `penalty` float(6,2) DEFAULT NULL,
  `description` text NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `entered_by` smallint(3) unsigned NOT NULL DEFAULT 0,
  `entered_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reviewed_by` smallint(3) unsigned NOT NULL DEFAULT 0,
  `reviewed_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `exported` tinyint(1) unsigned DEFAULT 0,
  `status` enum('pending','approved','cancelled') NOT NULL DEFAULT 'pending',
  `date_exported` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `studio` (`studio`),
  KEY `reviewed_datetime` (`reviewed_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Types`
--

DROP TABLE IF EXISTS `Violations_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Types` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `severity` varchar(100) NOT NULL DEFAULT '',
  `report_type` varchar(100) NOT NULL DEFAULT '',
  `report_description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1037 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Violations_Types_Studios`
--

DROP TABLE IF EXISTS `Violations_Types_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Violations_Types_Studios` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1024 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Welcome_Day_Schedule`
--

DROP TABLE IF EXISTS `Welcome_Day_Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Welcome_Day_Schedule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `scheduled_date` date NOT NULL DEFAULT '0000-00-00',
  `paid_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `free_credits_qualified_shows` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `qualified_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `seconds_online` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `free_credits_non_qualified_shows` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `non_qualified_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `shift_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `date` (`scheduled_date`)
) ENGINE=InnoDB AUTO_INCREMENT=6359 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `XVC_Models_Money_Network`
--

DROP TABLE IF EXISTS `XVC_Models_Money_Network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `XVC_Models_Money_Network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `bc_payment_method_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mn` (`broadcaster_id`,`bc_payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 17:38:07
