/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: MESSAGING
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
-- Table structure for table `Blocked_Models`
--

DROP TABLE IF EXISTS `Blocked_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blocked_Models` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_blocked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CM_Sub_Topics`
--

DROP TABLE IF EXISTS `CM_Sub_Topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CM_Sub_Topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `title` varchar(32) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `subtopic` varchar(32) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `ttl` int(11) NOT NULL DEFAULT 0,
  `flood_control_ttl` int(11) NOT NULL DEFAULT 0,
  `model_recently_logged_in` tinyint(4) NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `sitekey_index` (`sitekey`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CM_Topics`
--

DROP TABLE IF EXISTS `CM_Topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CM_Topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `topic` varchar(32) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CM_Unsubscribe`
--

DROP TABLE IF EXISTS `CM_Unsubscribe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CM_Unsubscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `subtopic_id` int(11) NOT NULL DEFAULT 0,
  `model_id` int(11) NOT NULL DEFAULT 0,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`topic_id`,`subtopic_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2910018 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Filter`
--

DROP TABLE IF EXISTS `Filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Filter` (
  `phrase` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`phrase`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Messages_Attachments`
--

DROP TABLE IF EXISTS `Messages_Attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Messages_Attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_message_id` int(11) unsigned NOT NULL,
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `file_type` enum('jpg','gif','png') DEFAULT 'jpg',
  `file_name` varchar(50) NOT NULL,
  `file_size` float(5,2) NOT NULL,
  `pending_delete` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4444236 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Contacts`
--

DROP TABLE IF EXISTS `Model_Contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Contacts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `color` varchar(6) NOT NULL DEFAULT '',
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `screen_name` varchar(32) NOT NULL DEFAULT '',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `model_and_user` (`model_id`,`optiusers_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1125017 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Folders`
--

DROP TABLE IF EXISTS `Model_Folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Folders` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  `old_folder_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17201 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_0`
--

DROP TABLE IF EXISTS `Model_Messages_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_0` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=11179750 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_1`
--

DROP TABLE IF EXISTS `Model_Messages_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_1` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3198502 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_2`
--

DROP TABLE IF EXISTS `Model_Messages_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3865735 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_3`
--

DROP TABLE IF EXISTS `Model_Messages_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_3` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3098668 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_4`
--

DROP TABLE IF EXISTS `Model_Messages_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_4` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3571798 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_5`
--

DROP TABLE IF EXISTS `Model_Messages_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_5` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3673298 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_6`
--

DROP TABLE IF EXISTS `Model_Messages_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_6` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3810254 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_7`
--

DROP TABLE IF EXISTS `Model_Messages_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_7` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3332649 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_8`
--

DROP TABLE IF EXISTS `Model_Messages_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_8` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3322736 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_9`
--

DROP TABLE IF EXISTS `Model_Messages_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_9` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`),
  KEY `sender_id_sender_type` (`sender_id`,`sender_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3286690 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Messages_Queue`
--

DROP TABLE IF EXISTS `Model_Messages_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Messages_Queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `max_message_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `queued_action` enum('deleted','unread','read') NOT NULL,
  `action_status` enum('pending','complete') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `origin_folder_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `max_message_id` (`max_message_id`,`model_id`,`date_created`),
  KEY `queued_action` (`queued_action`),
  KEY `date_created` (`date_created`),
  KEY `model_id` (`date_created`,`max_message_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11102 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Quarantined_Messages`
--

DROP TABLE IF EXISTS `Quarantined_Messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Quarantined_Messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_status` enum('rejected','approved','pending') NOT NULL DEFAULT 'pending',
  `review_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=173004 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Folders`
--

DROP TABLE IF EXISTS `User_Folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Folders` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  `old_folder_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38064 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_0`
--

DROP TABLE IF EXISTS `User_Messages_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_0` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4378013 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_1`
--

DROP TABLE IF EXISTS `User_Messages_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_1` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4068700 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_2`
--

DROP TABLE IF EXISTS `User_Messages_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_2` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4143335 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_3`
--

DROP TABLE IF EXISTS `User_Messages_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_3` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4306602 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_4`
--

DROP TABLE IF EXISTS `User_Messages_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_4` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4214918 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_5`
--

DROP TABLE IF EXISTS `User_Messages_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_5` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4222348 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_6`
--

DROP TABLE IF EXISTS `User_Messages_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_6` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4543919 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_7`
--

DROP TABLE IF EXISTS `User_Messages_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_7` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4077973 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_8`
--

DROP TABLE IF EXISTS `User_Messages_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_8` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4243040 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Messages_9`
--

DROP TABLE IF EXISTS `User_Messages_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Messages_9` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender_name` varchar(32) NOT NULL,
  `sender_message_id` int(11) unsigned NOT NULL,
  `sender_ip` varbinary(16) NOT NULL DEFAULT '',
  `original_sender_type` varchar(14) NOT NULL DEFAULT 'user',
  `original_sender_id` int(10) unsigned NOT NULL DEFAULT 0,
  `original_message_id` int(11) unsigned NOT NULL,
  `recipient_id` int(10) unsigned NOT NULL DEFAULT 0,
  `recipient_user_list` text NOT NULL,
  `recipient_model_list` text NOT NULL,
  `folder_id` int(11) unsigned NOT NULL,
  `has_attachments` tinyint(1) unsigned NOT NULL,
  `is_read` tinyint(1) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `status` varchar(14) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `date_created` (`date_created`),
  KEY `original_sender_info` (`original_sender_type`,`original_sender_id`,`original_message_id`),
  KEY `folder_id` (`folder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4236559 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_0`
--

DROP TABLE IF EXISTS `User_Notes_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_0` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21146 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_1`
--

DROP TABLE IF EXISTS `User_Notes_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28117 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_2`
--

DROP TABLE IF EXISTS `User_Notes_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24626 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_3`
--

DROP TABLE IF EXISTS `User_Notes_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18871 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_4`
--

DROP TABLE IF EXISTS `User_Notes_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_4` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33594 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_5`
--

DROP TABLE IF EXISTS `User_Notes_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_5` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26586 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_6`
--

DROP TABLE IF EXISTS `User_Notes_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_6` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23469 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_7`
--

DROP TABLE IF EXISTS `User_Notes_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_7` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19540 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_8`
--

DROP TABLE IF EXISTS `User_Notes_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_8` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28519 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes_9`
--

DROP TABLE IF EXISTS `User_Notes_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes_9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `num_edits` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33272 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 17:37:38
