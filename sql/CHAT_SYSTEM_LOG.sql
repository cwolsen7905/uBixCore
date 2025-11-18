/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: CHAT_SYSTEM_LOG
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
-- Table structure for table `Applet_Stats`
--

DROP TABLE IF EXISTS `Applet_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Applet_Stats` (
  `applet_type` enum('chat','video') NOT NULL DEFAULT 'chat',
  `applet_filename` varchar(100) NOT NULL DEFAULT '',
  `applet_class` varchar(100) NOT NULL DEFAULT '',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `browser` varchar(100) NOT NULL DEFAULT '',
  `operating_system` varchar(100) NOT NULL DEFAULT '',
  `java_vendor` varchar(50) NOT NULL DEFAULT '',
  `java_version` varchar(50) NOT NULL DEFAULT '',
  `embed_type` varchar(10) NOT NULL DEFAULT 'FF',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `model_id` int(11) NOT NULL DEFAULT 0,
  `result` varchar(100) NOT NULL DEFAULT '',
  KEY `ip_search` (`datetime`,`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Automated_Chat_Messages`
--

DROP TABLE IF EXISTS `Automated_Chat_Messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Automated_Chat_Messages` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `ts_sent` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `category` varchar(50) NOT NULL DEFAULT '',
  `message` varchar(255) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `model_id` (`model_id`),
  KEY `send_search` (`ts_sent`)
) ENGINE=InnoDB AUTO_INCREMENT=52575062 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Backup_Files`
--

DROP TABLE IF EXISTS `Backup_Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Backup_Files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` date DEFAULT NULL,
  `file_modified_date` varchar(50) DEFAULT NULL,
  `file_name` varchar(150) DEFAULT NULL,
  `file_size` int(10) unsigned DEFAULT NULL,
  `host_video_server` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `date_verified` date DEFAULT NULL,
  `recording_server_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_name` (`file_name`),
  KEY `status` (`status`),
  CONSTRAINT `Backup_Files_ibfk_1` FOREIGN KEY (`status`) REFERENCES `Backup_Files_Status` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=608872 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Backup_Files_Status`
--

DROP TABLE IF EXISTS `Backup_Files_Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Backup_Files_Status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `C2C_Broadcasts`
--

DROP TABLE IF EXISTS `C2C_Broadcasts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `C2C_Broadcasts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `video_host` varchar(64) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81959 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `C2C_Views`
--

DROP TABLE IF EXISTS `C2C_Views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `C2C_Views` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `datetime_started` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `broadcast_id` int(10) unsigned NOT NULL,
  `model_id` int(10) unsigned NOT NULL,
  `show_activity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=19448 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CDN_Providers`
--

DROP TABLE IF EXISTS `CDN_Providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CDN_Providers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CDN_Usage`
--

DROP TABLE IF EXISTS `CDN_Usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CDN_Usage` (
  `datetime` datetime NOT NULL,
  `provider_id` int(11) unsigned NOT NULL,
  `usage_type_id` int(11) unsigned NOT NULL,
  `value` bigint(11) unsigned NOT NULL,
  PRIMARY KEY (`datetime`,`provider_id`,`usage_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CDN_Usage_Types`
--

DROP TABLE IF EXISTS `CDN_Usage_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CDN_Usage_Types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `description` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chat_Logins_By_Affiliate_bak`
--

DROP TABLE IF EXISTS `Chat_Logins_By_Affiliate_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Chat_Logins_By_Affiliate_bak` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '0000',
  `num_guest` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_guest_success` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_regular` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`affiliate_id`,`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chat_Logins_By_Model_bak`
--

DROP TABLE IF EXISTS `Chat_Logins_By_Model_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Chat_Logins_By_Model_bak` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_guest` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_guest_success` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_regular` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_relocate_regular` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_relocate_guest` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Cam_Activity`
--

DROP TABLE IF EXISTS `Customer_Cam_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Cam_Activity` (
  `show_activity_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_ended` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `broadcast_type` varchar(16) NOT NULL DEFAULT 'flash',
  KEY `show_activity_id` (`show_activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Chat_Stats`
--

DROP TABLE IF EXISTS `Customer_Chat_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Chat_Stats` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `hour` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `customer_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `num_messages` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_users` smallint(5) unsigned NOT NULL DEFAULT 0,
  `seconds_in_room` int(10) unsigned NOT NULL DEFAULT 0,
  `chat_is_blocked` enum('Y','N') NOT NULL DEFAULT 'N',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compid` (`date`,`hour`,`model_id`,`customer_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39441591 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Encoder_Usage`
--

DROP TABLE IF EXISTS `Encoder_Usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Encoder_Usage` (
  `model_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Error_Activity`
--

DROP TABLE IF EXISTS `Error_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Error_Activity` (
  `code` smallint(4) unsigned NOT NULL DEFAULT 0,
  `nickname` varchar(32) NOT NULL DEFAULT '',
  `token` int(11) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `server_public_name` varchar(150) NOT NULL DEFAULT '',
  `version` varchar(20) NOT NULL DEFAULT '',
  `details` varchar(255) NOT NULL DEFAULT '',
  KEY `datetime` (`datetime`),
  KEY `model_id` (`model_id`),
  KEY `server_public_name` (`server_public_name`),
  KEY `version` (`version`),
  KEY `code_datetime` (`code`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Error_Activity_Summarized`
--

DROP TABLE IF EXISTS `Error_Activity_Summarized`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Error_Activity_Summarized` (
  `code` smallint(4) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `num_errors` mediumint(8) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`code`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Event_Log`
--

DROP TABLE IF EXISTS `Event_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Event_Log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(64) NOT NULL DEFAULT '',
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `details` text DEFAULT NULL,
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `event_type_id` (`event_type`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Feel_App_Device_Log`
--

DROP TABLE IF EXISTS `Feel_App_Device_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feel_App_Device_Log` (
  `date_used` date NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `feel_app_id` varchar(32) NOT NULL,
  `device_name` varchar(128) NOT NULL,
  PRIMARY KEY (`date_used`,`user_id`,`device_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Feel_App_Usage_Log`
--

DROP TABLE IF EXISTS `Feel_App_Usage_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feel_App_Usage_Log` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `feel_app_id` varchar(32) NOT NULL,
  `date_started` datetime NOT NULL,
  `date_ended` datetime NOT NULL,
  `activity_type` varchar(32) NOT NULL DEFAULT 'online',
  `activity_id` int(11) unsigned NOT NULL DEFAULT 0,
  KEY `user_id` (`user_id`,`date_started`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Group_Chats`
--

DROP TABLE IF EXISTS `Group_Chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Group_Chats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `performer_activity_id` int(11) unsigned NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `credit_goal` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `min_pledge` mediumint(5) NOT NULL DEFAULT 0,
  `countdown_duration` smallint(5) unsigned NOT NULL DEFAULT 0,
  `show_duration` smallint(5) unsigned NOT NULL DEFAULT 0,
  `credits_earned` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_customers_before` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_customers_after` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_credits_before` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_credits_after` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `reached_goal` enum('Y','N') NOT NULL DEFAULT 'N',
  `accepted` tinyint(1) NOT NULL DEFAULT 0,
  `accepted_credits` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `date_started` datetime DEFAULT '0000-00-00 00:00:00',
  `date_ended` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `performer_activity_id` (`performer_activity_id`),
  KEY `model_id` (`model_id`),
  KEY `status` (`status`),
  KEY `date_started` (`date_started`),
  KEY `date_ended` (`date_ended`)
) ENGINE=InnoDB AUTO_INCREMENT=8344198 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Interactive_Activity`
--

DROP TABLE IF EXISTS `Interactive_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Interactive_Activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_group_id` tinyint(3) unsigned NOT NULL,
  `show_activity_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity` varchar(25) NOT NULL DEFAULT '',
  `date_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_ended` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `date_started` (`date_started`)
) ENGINE=InnoDB AUTO_INCREMENT=3249 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Interactive_Daily`
--

DROP TABLE IF EXISTS `Interactive_Daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Interactive_Daily` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`date`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Location_Scoring`
--

DROP TABLE IF EXISTS `Location_Scoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Location_Scoring` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `location_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `pa_milliseconds` int(11) unsigned NOT NULL DEFAULT 0,
  `score_returned` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compid` (`model_id`,`datetime`,`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Monitor_Activity`
--

DROP TABLE IF EXISTS `Monitor_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Monitor_Activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `studio_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `activity` varchar(24) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_activity` (`studio_admin_id`,`activity`,`datetime`),
  KEY `model_id` (`model_id`),
  KEY `user_name` (`user_id`,`user_nickname`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=6611851 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Monitor_Chatlog`
--

DROP TABLE IF EXISTS `Monitor_Chatlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Monitor_Chatlog` (
  `studio_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `server_public_name` varchar(150) NOT NULL DEFAULT '',
  KEY `studio_admin_id` (`studio_admin_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Party_Chat_Goals`
--

DROP TABLE IF EXISTS `Party_Chat_Goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Party_Chat_Goals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL,
  `date_started` datetime NOT NULL,
  `goal_1_description` varchar(20) NOT NULL,
  `goal_1_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `goal_1_credits_received` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `goal_2_description` varchar(20) NOT NULL,
  `goal_2_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `goal_2_credits_received` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `goal_3_description` varchar(20) NOT NULL,
  `goal_3_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `goal_3_credits_received` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `date_started` (`date_started`)
) ENGINE=InnoDB AUTO_INCREMENT=9391127 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Activity`
--

DROP TABLE IF EXISTS `Performer_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `performer_login_type_id` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `studio_code` char(12) DEFAULT NULL,
  `location_id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `room_name` varchar(100) NOT NULL DEFAULT '',
  `activity` varchar(25) NOT NULL DEFAULT 'login',
  `date_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_ended` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `server_public_name` varchar(255) NOT NULL DEFAULT '',
  `date_exported` date NOT NULL DEFAULT '0000-00-00',
  `is_new` tinyint(1) NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `needs_review` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `date_started` (`date_started`),
  KEY `location` (`location_id`,`room_name`),
  KEY `studio` (`studio_code`),
  KEY `login_type_id` (`performer_login_type_id`),
  KEY `model_id` (`model_id`,`date_ended`,`server_public_name`(48)),
  KEY `activity` (`activity`),
  KEY `platform` (`platform`),
  KEY `update_model_id_started_end_activity` (`model_id`,`date_started`,`date_ended`,`activity`)
) ENGINE=InnoDB AUTO_INCREMENT=217296882 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Activity_Ex`
--

DROP TABLE IF EXISTS `Performer_Activity_Ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Activity_Ex` (
  `performer_activity_id` int(11) unsigned NOT NULL,
  `performer_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `performer_app_version` varchar(20) NOT NULL DEFAULT '',
  `video_width` smallint(4) NOT NULL DEFAULT 0,
  `video_height` smallint(4) NOT NULL DEFAULT 0,
  `video_codec` tinyint(1) unsigned NOT NULL DEFAULT 3,
  `extra` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`performer_activity_id`),
  KEY `video_codec` (`video_codec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Alerts`
--

DROP TABLE IF EXISTS `Performer_Alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Alerts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `monitor_id` mediumint(8) unsigned DEFAULT NULL,
  `date_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_resolved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `alert` varchar(255) NOT NULL DEFAULT '',
  `resolution` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`,`date_started`),
  KEY `monitor_id` (`monitor_id`,`date_resolved`),
  KEY `date_resolved` (`date_resolved`)
) ENGINE=InnoDB AUTO_INCREMENT=360469 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Conversion`
--

DROP TABLE IF EXISTS `Performer_Conversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Conversion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `show_date` date NOT NULL DEFAULT '0000-00-00',
  `show_month_start` date NOT NULL DEFAULT '0000-00-00',
  `login_group_id` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `cpm` smallint(5) unsigned NOT NULL DEFAULT 0,
  `seconds_online` mediumint(9) NOT NULL DEFAULT 0,
  `seconds_show` mediumint(9) NOT NULL DEFAULT 0,
  `credits_show` mediumint(9) NOT NULL DEFAULT 0,
  `credits_tips_open` mediumint(9) NOT NULL DEFAULT 0,
  `credits_tips_show` mediumint(9) NOT NULL DEFAULT 0,
  `num_show` mediumint(9) NOT NULL DEFAULT 0,
  `num_tips_open` mediumint(9) NOT NULL DEFAULT 0,
  `num_tips_show` mediumint(9) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `summarized_record` (`show_date`,`login_group_id`,`service`,`cpm`),
  KEY `show_date` (`show_date`),
  KEY `show_month_start` (`show_month_start`)
) ENGINE=InnoDB AUTO_INCREMENT=2569 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Logins_Blocked`
--

DROP TABLE IF EXISTS `Performer_Logins_Blocked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Logins_Blocked` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `num_models_online` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_users_chatting` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `lifetime_cph` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `percentile_rank` smallint(3) unsigned NOT NULL DEFAULT 0,
  `blocked_reason` varchar(255) NOT NULL DEFAULT '',
  KEY `date` (`date`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Penalty_Log`
--

DROP TABLE IF EXISTS `Performer_Penalty_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Penalty_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity` varchar(24) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `user_name` (`user_id`,`user_nickname`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=20081367 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Relocate_Log`
--

DROP TABLE IF EXISTS `Relocate_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Relocate_Log` (
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `token` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `model_id_from` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id_to` int(11) unsigned NOT NULL DEFAULT 0,
  `relocate_type` enum('v1_to_v1','v1_to_v3','v3_to_v1','v3_to_v3') NOT NULL DEFAULT 'v1_to_v1',
  KEY `datetime` (`datetime`,`relocate_type`),
  KEY `service` (`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Server_Assignment`
--

DROP TABLE IF EXISTS `Server_Assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Server_Assignment` (
  `datetime` datetime NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `bitrate` mediumint(8) unsigned NOT NULL,
  `previous_ingest` varchar(64) NOT NULL DEFAULT '',
  `previous_egress` varchar(64) NOT NULL DEFAULT '',
  `assigned_ingest` varchar(64) NOT NULL DEFAULT '',
  `assigned_egress` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Server_Log_Summary`
--

DROP TABLE IF EXISTS `Server_Log_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Server_Log_Summary` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `server_public_name` varchar(255) NOT NULL DEFAULT '',
  `server_type` enum('chat','video') NOT NULL DEFAULT 'video',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `file` varchar(50) NOT NULL DEFAULT '',
  `line` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `message` varchar(255) NOT NULL DEFAULT '',
  `total` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`server_public_name`,`server_type`,`model_id`,`file`,`line`),
  KEY `standard_search` (`date`,`server_type`,`file`,`line`),
  KEY `message` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Server_Stats`
--

DROP TABLE IF EXISTS `Server_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Server_Stats` (
  `server` varchar(150) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(12) NOT NULL DEFAULT '',
  `running` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `load_avg` float(5,2) NOT NULL DEFAULT 0.00,
  `num_performers` smallint(4) unsigned NOT NULL DEFAULT 0,
  `num_customers` smallint(5) unsigned NOT NULL DEFAULT 0,
  `data` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`),
  KEY `server_scoring` (`datetime`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=35722790 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Show_Activity_Chatlog`
--

DROP TABLE IF EXISTS `Show_Activity_Chatlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_Activity_Chatlog` (
  `show_activity_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `direction` enum('from_model','from_user') NOT NULL DEFAULT 'from_user',
  `message` varchar(255) NOT NULL DEFAULT '',
  `show_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `user_ip` varchar(15) NOT NULL DEFAULT '',
  KEY `show_activity_id` (`show_activity_id`),
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`),
  KEY `datetime` (`datetime`),
  FULLTEXT KEY `message` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Show_Offers`
--

DROP TABLE IF EXISTS `Show_Offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_Offers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `performer_activity_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `default_cpm` float(6,2) NOT NULL DEFAULT 0.00,
  `initial_cpm` float(6,2) NOT NULL,
  `initial_duration` smallint(5) unsigned NOT NULL,
  `expiration_duration` smallint(5) unsigned NOT NULL,
  `counters` smallint(5) unsigned NOT NULL DEFAULT 0,
  `final_cpm` float(6,2) NOT NULL,
  `final_duration` smallint(5) unsigned NOT NULL,
  `status` smallint(5) unsigned NOT NULL DEFAULT 0,
  `show_activity_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`),
  KEY `show_activity_id` (`show_activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4977729 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 17:35:32
