/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: CHAT_SYSTEM
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
-- Table structure for table `Authorized_Domains`
--

DROP TABLE IF EXISTS `Authorized_Domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Authorized_Domains` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `referrer` varchar(255) NOT NULL DEFAULT '',
  `url_dev` varchar(255) NOT NULL DEFAULT '',
  `url_live` varchar(255) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`referrer`),
  KEY `domain_2` (`referrer`)
) ENGINE=InnoDB AUTO_INCREMENT=125255 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Banned_Users`
--

DROP TABLE IF EXISTS `Banned_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Banned_Users` (
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `chat_nickname` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_expire` date NOT NULL DEFAULT '0000-00-00',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `sub_user_id` varchar(64) NOT NULL DEFAULT '',
  `fingerprint` varchar(32) NOT NULL DEFAULT '',
  KEY `ip_address` (`ip_address`),
  KEY `chat_nickname` (`chat_nickname`),
  KEY `user_id` (`user_id`),
  KEY `datetime` (`datetime`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `C2C_In`
--

DROP TABLE IF EXISTS `C2C_In`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `C2C_In` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `broadcast_id` varchar(64) NOT NULL DEFAULT '',
  `stream_id` varchar(64) NOT NULL DEFAULT '',
  `token` varchar(64) NOT NULL DEFAULT '',
  `status` varchar(16) NOT NULL DEFAULT '',
  `type` enum('local','cloud') NOT NULL DEFAULT 'cloud',
  `format` varchar(32) NOT NULL DEFAULT '',
  `uri` varchar(128) NOT NULL DEFAULT '',
  `ip_addr` varchar(39) NOT NULL DEFAULT '',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_start_utc` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end_utc` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_test` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rewards_calculated` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_audio_only` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `broadcast_id` (`broadcast_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6736785 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `C2C_Out`
--

DROP TABLE IF EXISTS `C2C_Out`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `C2C_Out` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c2c_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `broadcast_id` varchar(64) NOT NULL DEFAULT '',
  `token` varchar(64) NOT NULL DEFAULT '',
  `status` varchar(16) NOT NULL DEFAULT '',
  `format` varchar(32) NOT NULL DEFAULT '',
  `uri` varchar(128) NOT NULL DEFAULT '',
  `codecs` varchar(128) NOT NULL DEFAULT '',
  `ip_addr` varchar(39) NOT NULL DEFAULT '',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_start_utc` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end_utc` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `c2c_id` (`c2c_id`),
  KEY `model_id` (`model_id`),
  KEY `broadcast_id` (`broadcast_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5249871 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CDNS_Attributes`
--

DROP TABLE IF EXISTS `CDNS_Attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CDNS_Attributes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cdn_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `attribute` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `cdn_id` (`cdn_id`),
  KEY `attribute` (`attribute`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chat_Filter_IP_Blacklist`
--

DROP TABLE IF EXISTS `Chat_Filter_IP_Blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Chat_Filter_IP_Blacklist` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(64) NOT NULL DEFAULT '',
  `subnet_cidr` tinyint(3) unsigned NOT NULL DEFAULT 32,
  `duration` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `duration_type` enum('minutes','hours','days') NOT NULL DEFAULT 'days',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `expires` (`date_expires`),
  KEY `status` (`status`),
  KEY `ip_address` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=87845 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chat_Filter_IP_Log`
--

DROP TABLE IF EXISTS `Chat_Filter_IP_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Chat_Filter_IP_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(64) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `is_banned` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `is_banned` (`is_banned`),
  KEY `date_created` (`date_created`),
  KEY `ip_address` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=178030 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chat_Logs_Temp`
--

DROP TABLE IF EXISTS `Chat_Logs_Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Chat_Logs_Temp` (
  `date` date NOT NULL,
  `server` varchar(50) NOT NULL DEFAULT '',
  `in_show` tinyint(4) NOT NULL DEFAULT 0,
  `text` varchar(200) NOT NULL DEFAULT '',
  KEY `date` (`date`),
  KEY `server` (`server`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Configuration`
--

DROP TABLE IF EXISTS `Configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Configuration` (
  `config_name` varchar(100) NOT NULL DEFAULT '',
  `config_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Types`
--

DROP TABLE IF EXISTS `Customer_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `paying` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Edge_Server_Assignment`
--

DROP TABLE IF EXISTS `Edge_Server_Assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Edge_Server_Assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin_name` varchar(128) NOT NULL,
  `edge_name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Error_Codes`
--

DROP TABLE IF EXISTS `Error_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Error_Codes` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `code` smallint(4) unsigned NOT NULL DEFAULT 0,
  `title` varchar(128) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `monitor_errors` enum('Y','N') NOT NULL DEFAULT 'N',
  `monitor_threshold` int(10) NOT NULL DEFAULT 10000,
  `monitor_address` varchar(255) NOT NULL DEFAULT '',
  `monitor_notes` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=1221 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Feel_App_Devices`
--

DROP TABLE IF EXISTS `Feel_App_Devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feel_App_Devices` (
  `feel_app_id` varchar(32) NOT NULL,
  `device_name` varchar(128) NOT NULL,
  `last_updated` datetime NOT NULL,
  KEY `feel_app_id` (`feel_app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`localhost`*/ /*!50003 TRIGGER CHAT_SYSTEM.feel_app_devices_insert AFTER INSERT ON CHAT_SYSTEM.Feel_App_Devices
    FOR EACH ROW
    BEGIN

        SET @user_id = (SELECT user_id FROM CHAT_SYSTEM.Feel_App_Status WHERE feel_app_id = NEW.feel_app_id);

        IF @user_id IS NOT NULL THEN

            INSERT IGNORE INTO CHAT_SYSTEM_LOG.Feel_App_Device_Log SET date_used = CURDATE(), user_id = @user_id, feel_app_id = NEW.feel_app_id, device_name = NEW.device_name;

        END IF;

    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Feel_App_Rooms`
--

DROP TABLE IF EXISTS `Feel_App_Rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feel_App_Rooms` (
  `feel_app_id` varchar(32) NOT NULL,
  `room_id` varchar(128) NOT NULL,
  `read_access` tinyint(1) NOT NULL,
  `write_access` tinyint(1) NOT NULL,
  `last_updated` datetime NOT NULL,
  KEY `feel_app_id` (`feel_app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`localhost`*/ /*!50003 TRIGGER CHAT_SYSTEM.feel_app_rooms_delete AFTER DELETE ON CHAT_SYSTEM.Feel_App_Rooms 
FOR EACH ROW BEGIN 

    SET @user_id = (SELECT user_id FROM CHAT_SYSTEM.Feel_App_Status WHERE feel_app_id = OLD.feel_app_id);

    IF @user_id IS NOT NULL THEN

        UPDATE CHAT_SYSTEM_LOG.Feel_App_Usage_Log SET date_ended = NOW() WHERE user_id = @user_id and activity_id != 0;

    END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Feel_App_Status`
--

DROP TABLE IF EXISTS `Feel_App_Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feel_App_Status` (
  `user_id` int(11) unsigned NOT NULL,
  `user_type` enum('customer','performer') NOT NULL DEFAULT 'customer',
  `feel_app_id` varchar(32) NOT NULL,
  `authenticated` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `online` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `last_updated` datetime NOT NULL,
  `update_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`user_type`),
  KEY `feel_app_id` (`feel_app_id`),
  KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`localhost`*/ /*!50003 TRIGGER CHAT_SYSTEM.feel_app_status_update AFTER UPDATE ON CHAT_SYSTEM.Feel_App_Status
    FOR EACH ROW
    BEGIN
        IF NEW.online = 1 AND OLD.online = 0 THEN
            INSERT INTO CHAT_SYSTEM_LOG.Feel_App_Usage_Log SET user_id = NEW.user_id, feel_app_id = NEW.feel_app_id, date_started = NOW(), date_ended = 0, activity_id = 0;

        ELSEIF OLD.online = 1 AND NEW.online = 0 THEN
            UPDATE CHAT_SYSTEM_LOG.Feel_App_Usage_Log SET date_ended = NOW() WHERE feel_app_id = NEW.feel_app_id and date_ended = 0;

        END IF;

    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Filtered_Words`
--

DROP TABLE IF EXISTS `Filtered_Words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filtered_Words` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL DEFAULT '',
  `studio_code` char(12) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `banned` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_expires` date NOT NULL DEFAULT '0000-00-00',
  `is_regex` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`,`studio_code`,`model_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10541 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Filtered_Words_Old`
--

DROP TABLE IF EXISTS `Filtered_Words_Old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filtered_Words_Old` (
  `word` varchar(255) NOT NULL DEFAULT '',
  `studio_code` char(12) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `banned` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `adult` tinyint(1) NOT NULL DEFAULT 1,
  `psychic` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`word`,`studio_code`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hourly_Top_Chatters`
--

DROP TABLE IF EXISTS `Hourly_Top_Chatters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hourly_Top_Chatters` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `hour` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_users` smallint(5) unsigned NOT NULL DEFAULT 0,
  `bonus_paid` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`date`,`hour`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Interactive_Devices`
--

DROP TABLE IF EXISTS `Interactive_Devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Interactive_Devices` (
  `identifier` varchar(50) NOT NULL,
  `allowed` tinyint(4) NOT NULL DEFAULT 1,
  `disallow_reason` varchar(250) NOT NULL DEFAULT '',
  `allow_customer_read` tinyint(4) NOT NULL DEFAULT 1,
  `allow_customer_write` tinyint(4) NOT NULL DEFAULT 1,
  `allow_performer_read` tinyint(4) NOT NULL DEFAULT 1,
  `allow_performer_write` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Mod_Settings`
--

DROP TABLE IF EXISTS `Mod_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Mod_Settings` (
  `mod_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `settings` text NOT NULL,
  PRIMARY KEY (`mod_id`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Mod_Stats`
--

DROP TABLE IF EXISTS `Mod_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Mod_Stats` (
  `mod_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`mod_id`,`model_id`,`date_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Mods`
--

DROP TABLE IF EXISTS `Mods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Mods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `min_version` varchar(8) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Monitor_Login`
--

DROP TABLE IF EXISTS `Monitor_Login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Monitor_Login` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('studio','admin') NOT NULL DEFAULT 'studio',
  `name` varchar(50) NOT NULL DEFAULT '',
  `num_servers` smallint(4) unsigned NOT NULL DEFAULT 0,
  `install_key` varchar(32) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_id_type` (`admin_id`,`admin_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2170 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PA_Settings`
--

DROP TABLE IF EXISTS `PA_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `PA_Settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned DEFAULT NULL,
  `key` varchar(32) NOT NULL,
  `value` text DEFAULT NULL,
  `platform_id` tinyint(3) NOT NULL DEFAULT 2,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_setting` (`model_id`,`key`,`platform_id`),
  KEY `platform_id` (`platform_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40322637 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PA_Version`
--

DROP TABLE IF EXISTS `PA_Version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `PA_Version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version_value` varchar(8) DEFAULT NULL,
  `version_min_value` varchar(8) DEFAULT NULL,
  `version_latest_value` varchar(8) DEFAULT NULL,
  `cut_off_date` date DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `notes` text DEFAULT '',
  `is_test` tinyint(1) DEFAULT 0,
  `active_status` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_App_Versions`
--

DROP TABLE IF EXISTS `Performer_App_Versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_App_Versions` (
  `identifier` varchar(32) NOT NULL,
  `version` varchar(16) NOT NULL,
  `date_released` date NOT NULL,
  `is_minimum` tinyint(1) NOT NULL DEFAULT 0,
  `is_latest` tinyint(1) NOT NULL DEFAULT 0,
  KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Assign_Codec`
--

DROP TABLE IF EXISTS `Performer_Assign_Codec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Assign_Codec` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `codec_id` tinyint(1) unsigned NOT NULL DEFAULT 2,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Login`
--

DROP TABLE IF EXISTS `Performer_Login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Login` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `performer_login_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `performer_app_version` varchar(20) NOT NULL DEFAULT '',
  `install_key` varchar(32) NOT NULL DEFAULT '',
  `room_name` varchar(55) NOT NULL DEFAULT '',
  `audio_enabled` enum('Y','N') NOT NULL DEFAULT 'N',
  `audio_in_open` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `num_users` smallint(5) NOT NULL DEFAULT 0,
  `total_guests` int(10) unsigned NOT NULL DEFAULT 0,
  `total_registered` int(10) unsigned NOT NULL DEFAULT 0,
  `total_guest_time` int(10) unsigned NOT NULL DEFAULT 0,
  `total_registered_time` int(10) unsigned NOT NULL DEFAULT 0,
  `total_supplemental` int(10) unsigned NOT NULL DEFAULT 0,
  `num_guest` smallint(6) NOT NULL DEFAULT 0,
  `num_basic` smallint(6) NOT NULL DEFAULT 0,
  `num_premium` smallint(6) NOT NULL DEFAULT 0,
  `num_vip` smallint(6) NOT NULL DEFAULT 0,
  `num_hov` int(10) unsigned NOT NULL DEFAULT 0,
  `num_supplemental` smallint(6) NOT NULL DEFAULT 0,
  `num_messages` smallint(5) unsigned NOT NULL DEFAULT 0,
  `chat_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `guest_chat_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `basic_chat_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `premium_whisper_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `vip_whisper_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `fc_whisper_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `video_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `allowing_offers` tinyint(1) NOT NULL DEFAULT 0,
  `interactive_device` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `interactive_device_name` varchar(128) NOT NULL DEFAULT '',
  `server_private_name` varchar(150) NOT NULL DEFAULT '',
  `server_public_name` varchar(150) NOT NULL DEFAULT '',
  `performer_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `data_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `applet_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `flash_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `xml_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `browser_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `video_host` varchar(150) DEFAULT NULL,
  `video_ingest` varchar(150) NOT NULL DEFAULT '',
  `video_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `video_audio_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `video_codec` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `video_width` smallint(4) NOT NULL DEFAULT 320,
  `video_height` smallint(4) NOT NULL DEFAULT 240,
  `fps` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `video_bitrate` smallint(5) unsigned NOT NULL DEFAULT 0,
  `video_bitrate_set` smallint(5) unsigned NOT NULL DEFAULT 0,
  `adaptive_bitrate` tinyint(4) NOT NULL DEFAULT 0,
  `image_quality` smallint(5) unsigned NOT NULL DEFAULT 0,
  `video_device` varchar(200) NOT NULL DEFAULT '',
  `audio_device` varchar(200) NOT NULL DEFAULT '',
  `video_uptime` int(10) unsigned NOT NULL DEFAULT 0,
  `video_disconnects` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `video_hd` tinyint(1) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `topic` varchar(200) NOT NULL DEFAULT '',
  `status` char(3) NOT NULL DEFAULT 'N',
  `initial_show_category` char(3) NOT NULL DEFAULT 'P',
  `external_status` tinyint(4) NOT NULL DEFAULT 0,
  `transcode_profiles` text DEFAULT NULL,
  `pa_type` varchar(16) NOT NULL DEFAULT 'pa',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `stream_settings` text DEFAULT NULL,
  PRIMARY KEY (`model_id`),
  KEY `status` (`status`),
  KEY `server_public_name` (`server_public_name`),
  KEY `performer_login_type_id` (`performer_login_type_id`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Login_Bans`
--

DROP TABLE IF EXISTS `Performer_Login_Bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Login_Bans` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ban_type` varchar(50) NOT NULL DEFAULT 'no_recent_show',
  `ban_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expire_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Login_Limits`
--

DROP TABLE IF EXISTS `Performer_Login_Limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Login_Limits` (
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `ruleset` enum('rule1','rule2') NOT NULL DEFAULT 'rule1',
  `num_models_online` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_users_chatting` mediumint(6) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`service`,`ruleset`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Login_Settings`
--

DROP TABLE IF EXISTS `Performer_Login_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Login_Settings` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `setting_key` varchar(25) NOT NULL DEFAULT '',
  `setting_value` float(6,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service` (`service`,`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Login_Types`
--

DROP TABLE IF EXISTS `Performer_Login_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Login_Types` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Login_Types_Access`
--

DROP TABLE IF EXISTS `Performer_Login_Types_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Login_Types_Access` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `performer_login_type_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `created_admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  `created_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  UNIQUE KEY `model_id` (`model_id`,`studio_code`,`performer_login_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Login_Types_Groups`
--

DROP TABLE IF EXISTS `Performer_Login_Types_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Login_Types_Groups` (
  `group_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `allow_external` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Login_Types_Join`
--

DROP TABLE IF EXISTS `Performer_Login_Types_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Login_Types_Join` (
  `performer_login_type_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `show_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`performer_login_type_id`,`show_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_System_Info`
--

DROP TABLE IF EXISTS `Performer_System_Info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_System_Info` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `operating_system` varchar(100) NOT NULL DEFAULT '',
  `processor` varchar(100) NOT NULL DEFAULT '',
  `ram` int(11) unsigned NOT NULL DEFAULT 0,
  `application_type` varchar(30) NOT NULL DEFAULT '',
  `application_version` varchar(30) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=685588 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Test_Video`
--

DROP TABLE IF EXISTS `Performer_Test_Video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Test_Video` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `video_host` varchar(150) NOT NULL DEFAULT '',
  `video_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `video_audio_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Provider_Weights`
--

DROP TABLE IF EXISTS `Provider_Weights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Provider_Weights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_name` varchar(32) NOT NULL,
  `playback_identifier` varchar(32) NOT NULL,
  `ingest_location` varchar(32) NOT NULL,
  `weight` smallint(6) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=211 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Proxy_Locations`
--

DROP TABLE IF EXISTS `Proxy_Locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Proxy_Locations` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `short_name` varchar(10) NOT NULL,
  `country` char(2) NOT NULL,
  `city` varchar(45) NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `last_status_change` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_admin_id` int(10) unsigned DEFAULT NULL,
  `notes` tinytext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `short_name_UNIQUE` (`short_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Quick_Messages`
--

DROP TABLE IF EXISTS `Quick_Messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Quick_Messages` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(128) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `message` (`message`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Room_Notification_Types`
--

DROP TABLE IF EXISTS `Room_Notification_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Room_Notification_Types` (
  `identifier` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `default_greeting` varchar(200) NOT NULL DEFAULT '',
  `default_priority` smallint(5) unsigned NOT NULL DEFAULT 0,
  `default_color` varchar(7) NOT NULL DEFAULT '#ffcc33',
  `site_type` enum('adult','psychic','both') NOT NULL DEFAULT 'both',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`identifier`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Room_Notifications`
--

DROP TABLE IF EXISTS `Room_Notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Room_Notifications` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `identifier` varchar(20) NOT NULL DEFAULT '',
  `send_notification` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `send_audio` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `send_greeting` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `notify_greeting` tinyint(4) NOT NULL DEFAULT 1,
  `notification_color` varchar(7) DEFAULT NULL,
  `custom_greeting` varchar(200) NOT NULL DEFAULT '',
  `priority` smallint(5) unsigned NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`model_id`,`identifier`),
  KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Security_Keys`
--

DROP TABLE IF EXISTS `Security_Keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Security_Keys` (
  `security_key` int(11) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`security_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Security_Keys_Join`
--

DROP TABLE IF EXISTS `Security_Keys_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Security_Keys_Join` (
  `install_key` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `security_key` int(11) unsigned NOT NULL DEFAULT 0,
  `mac_address` varchar(20) NOT NULL DEFAULT '',
  `app_type` enum('performer','monitor') DEFAULT 'performer',
  `studio_code` char(12) DEFAULT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `comments` varchar(255) NOT NULL DEFAULT '',
  `created_admin_type` enum('studio','admin') NOT NULL DEFAULT 'studio',
  `created_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_activated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changed_admin_type` enum('studio','admin','system') NOT NULL DEFAULT 'studio',
  `changed_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `changed_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changed_comments` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`install_key`),
  UNIQUE KEY `security` (`security_key`,`mac_address`),
  KEY `studio` (`studio_code`,`room_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Server_Locations`
--

DROP TABLE IF EXISTS `Server_Locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Server_Locations` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `short_name` varchar(16) NOT NULL DEFAULT '',
  `city` varchar(16) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  `region` varchar(8) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `deprecated` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `provider` varchar(64) NOT NULL DEFAULT '',
  `latitude` decimal(9,6) NOT NULL DEFAULT 0.000000,
  `longitude` decimal(9,6) NOT NULL DEFAULT 0.000000,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Servers`
--

DROP TABLE IF EXISTS `Servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Servers` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT 'chat',
  `private_name` varchar(150) NOT NULL DEFAULT '',
  `public_name` varchar(150) NOT NULL DEFAULT '',
  `manager_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `manager_data_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `manager_browser_port` smallint(5) unsigned NOT NULL DEFAULT 0,
  `manager_xml_port` smallint(5) unsigned NOT NULL DEFAULT 10020,
  `location_id` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `role` enum('standard','feature','overflow') NOT NULL DEFAULT 'standard',
  `sys_version` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `num_cores` smallint(5) NOT NULL DEFAULT 1,
  `proc_speed` float(3,2) NOT NULL DEFAULT 1.00,
  `score_value` smallint(4) unsigned NOT NULL DEFAULT 0,
  `score_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `deprecated` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `data` varchar(255) NOT NULL DEFAULT '',
  `last_status_change` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `server` (`type`,`location_id`,`public_name`)
) ENGINE=InnoDB AUTO_INCREMENT=354 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Show_End_Types`
--

DROP TABLE IF EXISTS `Show_End_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_End_Types` (
  `code` tinyint(3) unsigned NOT NULL,
  `title` varchar(32) NOT NULL,
  `description` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Show_Rates`
--

DROP TABLE IF EXISTS `Show_Rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_Rates` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_mod` float(3,2) NOT NULL DEFAULT 1.00,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Show_Rates_Join`
--

DROP TABLE IF EXISTS `Show_Rates_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_Rates_Join` (
  `show_rate_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `show_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `customer_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `rate_per_minute` float(6,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`show_rate_id`,`show_type_id`,`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Show_Types`
--

DROP TABLE IF EXISTS `Show_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_Types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `category` char(1) NOT NULL DEFAULT 'P',
  `initial` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `multiple` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `effective_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `allow_c2c` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `allow_paid_chat` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_recorded` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Show_Types_Join`
--

DROP TABLE IF EXISTS `Show_Types_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_Types_Join` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `show_type_id` mediumint(5) NOT NULL DEFAULT 0,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `num_credits` mediumint(5) NOT NULL DEFAULT 0,
  `min_duration` mediumint(5) NOT NULL DEFAULT 0,
  `image_name` varchar(50) NOT NULL DEFAULT '',
  `created_admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive','archived') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=742 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Studio_Login_Types_Access`
--

DROP TABLE IF EXISTS `Studio_Login_Types_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Studio_Login_Types_Access` (
  `studio` char(12) NOT NULL DEFAULT '',
  `performer_login_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_admin_type` enum('studio','admin') NOT NULL DEFAULT 'admin',
  `created_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  UNIQUE KEY `studio` (`studio`,`performer_login_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Video_Access`
--

DROP TABLE IF EXISTS `Video_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Video_Access` (
  `auth_string` varchar(100) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT '',
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`auth_string`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Video_Access_Codes`
--

DROP TABLE IF EXISTS `Video_Access_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Video_Access_Codes` (
  `access_code` varchar(32) NOT NULL DEFAULT '',
  `auth_string` varchar(100) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`access_code`),
  KEY `auth_string` (`auth_string`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Video_Server_to_CDN`
--

DROP TABLE IF EXISTS `Video_Server_to_CDN`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Video_Server_to_CDN` (
  `video_server_id` smallint(5) unsigned NOT NULL,
  `cdn_id` smallint(5) unsigned NOT NULL,
  `cname` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`video_server_id`,`cdn_id`),
  KEY `cdn_id` (`cdn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Video_Servers`
--

DROP TABLE IF EXISTS `Video_Servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Video_Servers` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `server_location_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `hostname` varchar(64) NOT NULL DEFAULT '',
  `legacy_hostname` varchar(64) NOT NULL DEFAULT '',
  `fqdn` varchar(64) NOT NULL DEFAULT '',
  `type` varchar(64) NOT NULL DEFAULT '',
  `role` varchar(64) NOT NULL DEFAULT '',
  `soft_cap` smallint(4) unsigned NOT NULL DEFAULT 0,
  `hard_cap` smallint(4) unsigned NOT NULL DEFAULT 0,
  `health_check` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `server_load` smallint(4) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `deprecated` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `manager_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `ip_address` varchar(40) DEFAULT '0.0.0.0',
  `uses_new_monitoring` tinyint(4) DEFAULT 0,
  `software` enum('wowza','ant') NOT NULL DEFAULT 'wowza',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=268 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Video_Servers_Platforms`
--

DROP TABLE IF EXISTS `Video_Servers_Platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Video_Servers_Platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server_id` int(10) unsigned NOT NULL,
  `platform_id` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `server_platform` (`server_id`,`platform_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12355 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Whitelisted_Words`
--

DROP TABLE IF EXISTS `Whitelisted_Words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Whitelisted_Words` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 17:34:59
