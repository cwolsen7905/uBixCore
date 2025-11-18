/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: VSCASH
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
-- Table structure for table `Activity_Stream`
--

DROP TABLE IF EXISTS `Activity_Stream`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_Stream` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_type` varchar(20) NOT NULL DEFAULT 'manual',
  `reply_message_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_type` varchar(15) NOT NULL DEFAULT 'admin',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `display_name` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `external_link_url` varchar(255) DEFAULT NULL,
  `external_link_title` varchar(100) DEFAULT NULL,
  `date_posted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`message_id`),
  KEY `date_posted` (`date_posted`),
  KEY `message_type` (`message_type`)
) ENGINE=InnoDB AUTO_INCREMENT=19864030 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_Stream_BC`
--

DROP TABLE IF EXISTS `Activity_Stream_BC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_Stream_BC` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_name` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_Stream_CSR`
--

DROP TABLE IF EXISTS `Activity_Stream_CSR`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_Stream_CSR` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_name` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=742 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_Stream_Likes`
--

DROP TABLE IF EXISTS `Activity_Stream_Likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_Stream_Likes` (
  `message_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `display_name` varchar(50) NOT NULL DEFAULT '',
  `date_posted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `message_id` (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Ad_Scrapper_Domains`
--

DROP TABLE IF EXISTS `Ad_Scrapper_Domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Ad_Scrapper_Domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime DEFAULT current_timestamp(),
  `wl_fk` varchar(255) DEFAULT '',
  `on_list` varchar(255) DEFAULT '',
  `last_checked` datetime DEFAULT '0000-00-00 00:00:00',
  `last_notified` datetime DEFAULT '0000-00-00 00:00:00',
  `list_status` tinyint(4) DEFAULT 0,
  `notes` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`wl_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Blog`
--

DROP TABLE IF EXISTS `Admin_Blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Blog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=1126 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Field_ClickUp_Map`
--

DROP TABLE IF EXISTS `Admin_Field_ClickUp_Map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Field_ClickUp_Map` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `field_name` varchar(255) NOT NULL DEFAULT '',
  `field_guid` varchar(36) NOT NULL DEFAULT '',
  `display_name` varchar(255) NOT NULL DEFAULT '',
  `required` tinyint(1) DEFAULT 0,
  `sync` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Function_Access`
--

DROP TABLE IF EXISTS `Admin_Function_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Function_Access` (
  `group_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `option_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`group_id`,`option_id`)
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
  `icon_name` varchar(25) NOT NULL DEFAULT 'folder',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
  `linkable` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `tip` text DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1399 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Group_Users`
--

DROP TABLE IF EXISTS `Admin_Group_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Group_Users` (
  `user_id` smallint(5) unsigned NOT NULL,
  `group_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Groups`
--

DROP TABLE IF EXISTS `Admin_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Groups` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(150) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=413 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_ID_ClickUp_Map`
--

DROP TABLE IF EXISTS `Admin_ID_ClickUp_Map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_ID_ClickUp_Map` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_on` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_id` smallint(5) unsigned NOT NULL,
  `field_name` varchar(255) NOT NULL DEFAULT '',
  `value_guid` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1016 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Notification_Types`
--

DROP TABLE IF EXISTS `Admin_Notification_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Notification_Types` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `tpl` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=996 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Notification_Users`
--

DROP TABLE IF EXISTS `Admin_Notification_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Notification_Users` (
  `admin_id` smallint(5) unsigned NOT NULL,
  `notification_type_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`admin_id`,`notification_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Policy_Areas`
--

DROP TABLE IF EXISTS `Admin_Policy_Areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Policy_Areas` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '',
  `admin_user_group_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Policy_Areas_Items`
--

DROP TABLE IF EXISTS `Admin_Policy_Areas_Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Policy_Areas_Items` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `area_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `item_type` enum('policy','procedure') NOT NULL DEFAULT 'policy',
  `title` varchar(100) NOT NULL DEFAULT '',
  `contents_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`area_id`,`title`),
  KEY `area_id` (`area_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Policy_Areas_Items_Contents`
--

DROP TABLE IF EXISTS `Admin_Policy_Areas_Items_Contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Policy_Areas_Items_Contents` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `contents` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Policy_Position_Responsibilities`
--

DROP TABLE IF EXISTS `Admin_Policy_Position_Responsibilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Policy_Position_Responsibilities` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` smallint(5) unsigned NOT NULL,
  `responsibility_id` tinyint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `position_id` (`position_id`,`responsibility_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Policy_Responsibilities`
--

DROP TABLE IF EXISTS `Admin_Policy_Responsibilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Policy_Responsibilities` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_User_Departments`
--

DROP TABLE IF EXISTS `Admin_User_Departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_User_Departments` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10074 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_User_Positions`
--

DROP TABLE IF EXISTS `Admin_User_Positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_User_Positions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `position_type` enum('consultant','employee','manager','director','executive') NOT NULL DEFAULT 'employee',
  `description` text NOT NULL,
  `classification` enum('hourly','salary_exempt') NOT NULL DEFAULT 'hourly',
  `max_people` tinyint(10) unsigned NOT NULL DEFAULT 0,
  `supervisor_position_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `department_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=480 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_User_Reports`
--

DROP TABLE IF EXISTS `Admin_User_Reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_User_Reports` (
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id_recipient` smallint(5) unsigned NOT NULL DEFAULT 0,
  `frequency` enum('daily','weekly','monthly') NOT NULL DEFAULT 'weekly',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`admin_id`,`admin_id_recipient`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_User_Slack_Details`
--

DROP TABLE IF EXISTS `Admin_User_Slack_Details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_User_Slack_Details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `email` varchar(200) NOT NULL DEFAULT '',
  `slack_id` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users`
--

DROP TABLE IF EXISTS `Admin_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `bamboo_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  `short_name` varchar(10) NOT NULL,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(41) NOT NULL DEFAULT '',
  `2fa_secret` varchar(255) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `phone_extension` smallint(3) unsigned DEFAULT NULL,
  `phone_cell` varchar(20) NOT NULL DEFAULT '',
  `unix_username` varchar(32) DEFAULT NULL,
  `studio_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `position_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `biography` text NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `timezone` varchar(32) DEFAULT 'America/Los_Angeles',
  `sandbox_username` varchar(32) DEFAULT NULL,
  `cu_api_token` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1337 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Admin_Users_Manager_Join`
--

DROP TABLE IF EXISTS `Admin_Users_Manager_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_Users_Manager_Join` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `direct_manager_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='Table used to track the direct supervisor of an admin user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Adwords_Campaigns`
--

DROP TABLE IF EXISTS `Adwords_Campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Adwords_Campaigns` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(11) unsigned NOT NULL DEFAULT 0,
  `campaign_name` varchar(255) NOT NULL DEFAULT '',
  `impressions` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `cost` float(8,2) unsigned NOT NULL DEFAULT 0.00,
  `average_cpc` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date` date DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2166 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_1Click_Change_Log`
--

DROP TABLE IF EXISTS `Affiliate_1Click_Change_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_1Click_Change_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action_type` enum('global_override','ip_whitelist') NOT NULL DEFAULT 'global_override',
  `action_details` text NOT NULL,
  `action_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_user_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_action` (`admin_user_id`,`action_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1925 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_1click_Summary`
--

DROP TABLE IF EXISTS `Affiliate_1click_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_1click_Summary` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `version` varchar(8) NOT NULL DEFAULT '1.0',
  `count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `method` varchar(30) NOT NULL DEFAULT '',
  `response_code` smallint(4) unsigned NOT NULL DEFAULT 0,
  `response_desc` varchar(255) NOT NULL DEFAULT '',
  `api_mode` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_method` (`date`,`version`,`affiliate_id`,`domain`,`mp_code`,`sitekey`,`method`,`response_code`,`response_desc`,`api_mode`),
  KEY `date` (`date`),
  KEY `version` (`version`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `domain` (`domain`),
  KEY `mp_code` (`mp_code`),
  KEY `method` (`method`),
  KEY `response_code` (`response_code`),
  KEY `api_mode` (`api_mode`)
) ENGINE=InnoDB AUTO_INCREMENT=504659 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Ad_Domains`
--

DROP TABLE IF EXISTS `Affiliate_Ad_Domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Ad_Domains` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ssl_domain_id` smallint(5) unsigned NOT NULL,
  `date_enabled` date NOT NULL DEFAULT '2000-01-01',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ssl_domain_id` (`ssl_domain_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Announcements`
--

DROP TABLE IF EXISTS `Affiliate_Announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Announcements` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `template_path` varchar(255) NOT NULL DEFAULT '',
  `ok_to_send` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `send_status` enum('N','Y','P') NOT NULL DEFAULT 'N',
  `salesperson_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_to_affiliates` tinyint(1) NOT NULL DEFAULT 0,
  `send_to_leads` tinyint(1) NOT NULL DEFAULT 0,
  `send_to_adult` tinyint(1) NOT NULL DEFAULT 0,
  `send_to_psychic` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=411 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Announcements_Log`
--

DROP TABLE IF EXISTS `Affiliate_Announcements_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Announcements_Log` (
  `announcement_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `contact_email` varchar(255) NOT NULL DEFAULT '',
  `date_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `opened` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`announcement_id`,`contact_email`),
  KEY `opened` (`opened`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Attrition`
--

DROP TABLE IF EXISTS `Affiliate_Attrition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Attrition` (
  `id` mediumint(8) unsigned NOT NULL,
  `date_created` date DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `attrition_type` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Attrition_Uniques`
--

DROP TABLE IF EXISTS `Affiliate_Attrition_Uniques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Attrition_Uniques` (
  `id` mediumint(8) unsigned NOT NULL,
  `recent_uniques` mediumint(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Blogs`
--

DROP TABLE IF EXISTS `Affiliate_Blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL DEFAULT 0,
  `whitelabel_domain` varchar(255) NOT NULL DEFAULT '',
  `post_title` varchar(255) NOT NULL DEFAULT '',
  `post_url` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `whitelabel_domain` (`whitelabel_domain`)
) ENGINE=InnoDB AUTO_INCREMENT=676 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Display_Name`
--

DROP TABLE IF EXISTS `Affiliate_Display_Name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Display_Name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned DEFAULT NULL,
  `mp_code` char(5) DEFAULT NULL,
  `name_code` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_name_code` (`affiliate_id`,`name_code`,`mp_code`)
) ENGINE=InnoDB AUTO_INCREMENT=1424144 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Last_First_Money`
--

DROP TABLE IF EXISTS `Affiliate_Last_First_Money`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Last_First_Money` (
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Last_Join`
--

DROP TABLE IF EXISTS `Affiliate_Last_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Last_Join` (
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Lead_Log`
--

DROP TABLE IF EXISTS `Affiliate_Lead_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Lead_Log` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `comment` text DEFAULT NULL,
  `admin_id` smallint(5) unsigned NOT NULL,
  `date` datetime DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4336 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Leads`
--

DROP TABLE IF EXISTS `Affiliate_Leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Leads` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_by_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `company` varchar(30) NOT NULL DEFAULT '',
  `contact` varchar(50) DEFAULT NULL,
  `telephone` varchar(25) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `site_name` varchar(30) DEFAULT NULL,
  `site_url` varchar(150) DEFAULT NULL,
  `date_followup` date DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `admin_id_created` (`created_by_admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3918 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Login_Log`
--

DROP TABLE IF EXISTS `Affiliate_Login_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Login_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_login` date NOT NULL DEFAULT '0000-00-00',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `date_login` (`date_login`)
) ENGINE=InnoDB AUTO_INCREMENT=306146 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Notes`
--

DROP TABLE IF EXISTS `Affiliate_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `comments` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `mp_code` (`mp_code`,`admin_id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `affiliate_id_2` (`affiliate_id`,`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=1038871 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Offers_Optout`
--

DROP TABLE IF EXISTS `Affiliate_Offers_Optout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Offers_Optout` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_One_Click_Type_Join`
--

DROP TABLE IF EXISTS `Affiliate_One_Click_Type_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_One_Click_Type_Join` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `one_click_type_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`affiliate_id`,`one_click_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Optin`
--

DROP TABLE IF EXISTS `Affiliate_Optin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Optin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) NOT NULL DEFAULT 0,
  `affiliate_optin` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4651 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Payment_Overrides`
--

DROP TABLE IF EXISTS `Affiliate_Payment_Overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Payment_Overrides` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `override_type` enum('temp_override','final_payout') NOT NULL DEFAULT 'temp_override',
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_by_admin_id` mediumint(8) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `pay_after` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `affiliate_payperiod` (`affiliate_id`,`pay_period_id`),
  KEY `pay_period_date` (`pay_period_id`,`pay_after`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Tax_Compliance`
--

DROP TABLE IF EXISTS `Affiliate_Tax_Compliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Tax_Compliance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `notified_on` date NOT NULL,
  `notification_type` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `atc_composite_index` (`affiliate_id`,`notified_on`,`notification_type`)
) ENGINE=InnoDB AUTO_INCREMENT=22139 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Translation`
--

DROP TABLE IF EXISTS `Affiliate_Translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Translation` (
  `code_to_translate` varchar(50) NOT NULL DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(12) DEFAULT NULL,
  UNIQUE KEY `code_to_translate` (`code_to_translate`,`mp_code`),
  KEY `code_to_translate_2` (`code_to_translate`,`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_VS500_RSVP`
--

DROP TABLE IF EXISTS `Affiliate_VS500_RSVP`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_VS500_RSVP` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `primary_full_name` varchar(40) NOT NULL DEFAULT '',
  `is_primary_racing` tinyint(1) NOT NULL DEFAULT 0,
  `guest_full_name` varchar(40) NOT NULL DEFAULT '',
  `is_guest_racing` tinyint(1) NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1372 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates`
--

DROP TABLE IF EXISTS `Affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `site_type` enum('adult','rtb','affiliate','psychic','ppc','xvc','xvt','xvtraffic','volume','mb_retargeting') NOT NULL DEFAULT 'adult',
  `merged_to` mediumint(8) unsigned DEFAULT NULL,
  `account_nickname` varchar(80) DEFAULT NULL,
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `salt` varchar(64) NOT NULL DEFAULT '',
  `2fa_secret` varchar(255) NOT NULL DEFAULT '',
  `default_code` varchar(12) NOT NULL DEFAULT '',
  `referrer_status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `referrer_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `salesperson_id` smallint(5) unsigned NOT NULL DEFAULT 999,
  `alert_msg` text NOT NULL,
  `details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `activation_code` varchar(20) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `b2b_tracker` smallint(5) unsigned NOT NULL DEFAULT 0,
  `watch_list` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `api_status` tinyint(1) unsigned NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `last_first_money_over_150` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `how_did_you_find_us` enum('wordofmouth','industryforum','tradeshow','onlinesearch','other') NOT NULL DEFAULT 'wordofmouth',
  `is_house` enum('Y','N') NOT NULL DEFAULT 'N',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `on_hold_since` date DEFAULT NULL,
  `alert_msg_dismissed_at` datetime DEFAULT NULL,
  `live_video_ads` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `account_nickname` (`account_nickname`),
  KEY `password` (`password`),
  KEY `username_2` (`username`),
  KEY `referrer_id` (`referrer_id`),
  KEY `date_created` (`date_created`),
  KEY `last_updated` (`last_updated`),
  KEY `date_last_login` (`date_last_login`),
  KEY `salesperson_id` (`salesperson_id`),
  KEY `parent_id` (`parent_id`),
  KEY `hold_index` (`on_hold`,`on_hold_since`),
  KEY `status` (`status`),
  KEY `alert_msg_dismissed_at` (`alert_msg_dismissed_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10076112 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_API_Counts`
--

DROP TABLE IF EXISTS `Affiliates_API_Counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_API_Counts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_hourly_api_calls` int(11) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` int(11) unsigned NOT NULL DEFAULT 0,
  `username_available` smallint(5) unsigned NOT NULL DEFAULT 0,
  `username_available_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `username_available_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `username_available_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `username_available_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `username_available_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `email_available` smallint(5) unsigned NOT NULL DEFAULT 0,
  `email_available_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `email_available_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `email_available_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `email_available_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `email_available_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `create_account` smallint(5) unsigned NOT NULL DEFAULT 0,
  `create_account_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `create_account_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `create_account_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `create_account_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `create_account_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `validate_account` smallint(5) unsigned NOT NULL DEFAULT 0,
  `validate_account_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `validate_account_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `validate_account_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `validate_account_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `validate_account_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `check_credits` smallint(5) unsigned NOT NULL DEFAULT 0,
  `check_credits_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `check_credits_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `check_credits_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `check_credits_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `check_credits_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_email` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_email_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_email_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_email_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_email_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_email_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_password` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_password_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_password_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_password_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_password_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_password_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_confirmation_email` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_confirmation_email_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_confirmation_email_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_confirmation_email_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_confirmation_email_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_confirmation_email_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `block_user` smallint(5) unsigned NOT NULL DEFAULT 0,
  `block_user_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `block_user_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `block_user_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `block_user_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `block_user_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unblock_user` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unblock_user_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unblock_user_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unblock_user_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unblock_user_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unblock_user_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `add_payment_account` smallint(5) unsigned NOT NULL DEFAULT 0,
  `add_payment_account_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `add_payment_account_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `add_payment_account_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `add_payment_account_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `add_payment_account_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `fund_account` smallint(5) unsigned NOT NULL DEFAULT 0,
  `fund_account_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `fund_account_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `fund_account_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `fund_account_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `fund_account_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assign_tracking_code` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assign_tracking_code_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assign_tracking_code_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assign_tracking_code_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assign_tracking_code_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assign_tracking_code_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `workflow_lookup` smallint(5) unsigned NOT NULL DEFAULT 0,
  `workflow_lookup_200` smallint(5) unsigned NOT NULL DEFAULT 0,
  `workflow_lookup_400` smallint(5) unsigned NOT NULL DEFAULT 0,
  `workflow_lookup_401` smallint(5) unsigned NOT NULL DEFAULT 0,
  `workflow_lookup_426` smallint(5) unsigned NOT NULL DEFAULT 0,
  `workflow_lookup_500` smallint(5) unsigned NOT NULL DEFAULT 0,
  `username_available_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `username_available_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `email_available_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `email_available_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `create_account_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pending_create_account_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `create_account_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `validate_account_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `validate_account_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `check_credits_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `check_credits_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_email_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_email_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_password_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `update_password_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_confirmation_email_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `send_confirmation_email_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `block_user_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `block_user_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unblock_user_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `unblock_user_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `add_payment_account_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `add_payment_account_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `fund_account_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `fund_account_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assign_tracking_code_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assign_tracking_code_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `workflow_lookup_success` smallint(5) unsigned NOT NULL DEFAULT 0,
  `workflow_lookup_failures` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_hourly_api_call_success` int(11) unsigned NOT NULL DEFAULT 0,
  `total_hourly_api_call_failures` int(11) unsigned NOT NULL DEFAULT 0,
  `debug_mode` char(4) NOT NULL DEFAULT 'TEST',
  PRIMARY KEY (`id`),
  KEY `AFFID_DATE_MODE` (`affiliate_id`,`date_created`,`debug_mode`),
  KEY `DATE_MODE` (`date_created`,`debug_mode`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=1025622 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Action_Log`
--

DROP TABLE IF EXISTS `Affiliates_Action_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Action_Log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `action` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mp_code` (`mp_code`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Admin_Access`
--

DROP TABLE IF EXISTS `Affiliates_Admin_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Admin_Access` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `page` varchar(20) NOT NULL DEFAULT 'general',
  KEY `stats_search` (`datetime`,`admin_id`,`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Attribution_Immune`
--

DROP TABLE IF EXISTS `Affiliates_Attribution_Immune`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Attribution_Immune` (
  `affiliate_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10075067 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Co_Reg_Codes`
--

DROP TABLE IF EXISTS `Affiliates_Co_Reg_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Co_Reg_Codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `campaign_name` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT current_timestamp() COMMENT 'Create Time',
  `domain` varchar(255) DEFAULT NULL,
  `affiliate_id` int(11) DEFAULT NULL,
  `mp_code` varchar(255) DEFAULT NULL,
  `wl_domain` varchar(255) DEFAULT NULL,
  `site_key` varchar(255) DEFAULT NULL,
  `product_code` int(11) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `wlbp` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `admin_id` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `api_key` (`api_key`),
  KEY `site_key` (`site_key`),
  KEY `product_code` (`product_code`),
  KEY `domain` (`domain`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `mp_code` (`mp_code`),
  KEY `wl_domain` (`wl_domain`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Codes`
--

DROP TABLE IF EXISTS `Affiliates_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Codes` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `tracking_code` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `salt` varchar(64) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` varchar(255) NOT NULL DEFAULT '',
  `channel_type` varchar(32) DEFAULT NULL,
  UNIQUE KEY `affiliate_id` (`affiliate_id`,`mp_code`),
  UNIQUE KEY `mp_code` (`mp_code`),
  KEY `affiliate_id_2` (`affiliate_id`,`mp_code`,`tracking_code`),
  KEY `affiliate_id_3` (`affiliate_id`),
  KEY `tracking_code` (`tracking_code`),
  KEY `mps` (`mp_code`(4)),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Codes_Fraud`
--

DROP TABLE IF EXISTS `Affiliates_Codes_Fraud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Codes_Fraud` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_code` varchar(6) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reason` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mp_code` (`mp_code`,`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=368 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Codes_Fraud_Old`
--

DROP TABLE IF EXISTS `Affiliates_Codes_Fraud_Old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Codes_Fraud_Old` (
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reason` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`mp_code`,`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Contacts`
--

DROP TABLE IF EXISTS `Affiliates_Contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Contacts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `icq_number` varchar(25) NOT NULL DEFAULT '',
  `skype_handle` varchar(32) NOT NULL DEFAULT '',
  `twitter_handle` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allow_news` enum('Y','N') NOT NULL DEFAULT 'Y',
  `allow_stats` enum('Y','N') NOT NULL DEFAULT 'Y',
  `allow_notices` enum('Y','N') NOT NULL DEFAULT 'Y',
  `account_authority` enum('Y','N') NOT NULL DEFAULT 'Y',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=81094 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Daily_Summary`
--

DROP TABLE IF EXISTS `Affiliates_Daily_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Daily_Summary` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `salesperson_id` smallint(5) unsigned NOT NULL DEFAULT 999,
  `date_acquired` date NOT NULL DEFAULT '0000-00-00',
  `num_impressions` int(11) unsigned NOT NULL DEFAULT 0,
  `num_uniques` int(11) unsigned NOT NULL DEFAULT 0,
  `num_reg` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_conf` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_add_cc` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`affiliate_id`,`date_acquired`),
  KEY `salesperson_search` (`salesperson_id`,`date_acquired`),
  KEY `date_acquired` (`date_acquired`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Details`
--

DROP TABLE IF EXISTS `Affiliates_Details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
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
  `payout_method_2` varchar(20) NOT NULL DEFAULT '',
  `payout_minimum` float(6,2) NOT NULL DEFAULT 100.00,
  `epassporte_id` varchar(32) NOT NULL DEFAULT '',
  `payoneer_id` varchar(32) NOT NULL DEFAULT '',
  `paxum_id` varchar(255) NOT NULL DEFAULT '',
  `paxum_entity_type` enum('individual','business') DEFAULT 'individual',
  `paxum_entity_name` varchar(255) NOT NULL DEFAULT '',
  `paxum_payee_birthdate` date DEFAULT NULL,
  `paxum_business_registration_number` varchar(255) DEFAULT NULL,
  `wire_recip_address` text NOT NULL,
  `wire_bank_address` text NOT NULL,
  `wire_routing_num` varchar(30) NOT NULL DEFAULT '',
  `wire_account_num` varchar(30) NOT NULL DEFAULT '',
  `wire_correspondent` varchar(100) NOT NULL DEFAULT '',
  `created_by` enum('affiliate','system','admin') NOT NULL DEFAULT 'system',
  `created_id` smallint(5) unsigned NOT NULL DEFAULT 999,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `accounting_vendor_id` varchar(8) NOT NULL DEFAULT '',
  `wire_intl` char(9) DEFAULT NULL,
  `accounting_status` varchar(15) DEFAULT NULL,
  `ach_routing_number` varchar(30) DEFAULT NULL,
  `ach_account_number` varchar(30) DEFAULT NULL,
  `ach_bank_name` varchar(255) DEFAULT NULL,
  `ach_bank_address` text DEFAULT NULL,
  `ach_account_type` varchar(45) DEFAULT NULL,
  `ach_payee_type` varchar(45) DEFAULT NULL,
  `payment_data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=288510 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Documents`
--

DROP TABLE IF EXISTS `Affiliates_Documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Documents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `upload_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_size` int(10) unsigned NOT NULL DEFAULT 0,
  `file_mime_type` varchar(50) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `file_type` enum('W9','Identification Card','Proof of Residency','Misc','W8-BEN','W8-BEN-E','Sourcing Statement') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16105 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Dormancy_Immune`
--

DROP TABLE IF EXISTS `Affiliates_Dormancy_Immune`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Dormancy_Immune` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Extra_Info`
--

DROP TABLE IF EXISTS `Affiliates_Extra_Info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Extra_Info` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `info_key` varchar(50) NOT NULL DEFAULT '',
  `info_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`affiliate_id`,`info_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Hold_Immune`
--

DROP TABLE IF EXISTS `Affiliates_Hold_Immune`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Hold_Immune` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Logins`
--

DROP TABLE IF EXISTS `Affiliates_Logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Logins` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `ip_address_long` varbinary(16) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fail_code` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `ip_address_long` (`ip_address_long`),
  KEY `datetime` (`datetime`),
  KEY `fail_code` (`fail_code`),
  KEY `velocity_search` (`ip_address_long`,`datetime`,`fail_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3133612 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Logos`
--

DROP TABLE IF EXISTS `Affiliates_Logos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Logos` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `binary_data` longblob DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_id` (`affiliate_id`,`mp_code`,`service`)
) ENGINE=InnoDB AUTO_INCREMENT=49446 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Overrides`
--

DROP TABLE IF EXISTS `Affiliates_Overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Overrides` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datecreated` datetime NOT NULL,
  `lastupdated` datetime NOT NULL,
  `affiliate_id` int(10) unsigned NOT NULL,
  `mp_code` varchar(10) NOT NULL,
  `whitelabel_domain` varchar(255) NOT NULL,
  `overrides` text NOT NULL,
  `main_default` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_mpcode_domain` (`affiliate_id`,`mp_code`,`whitelabel_domain`),
  KEY `main_default` (`main_default`)
) ENGINE=InnoDB AUTO_INCREMENT=387 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Quotas`
--

DROP TABLE IF EXISTS `Affiliates_Quotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Quotas` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `period_id` int(11) unsigned NOT NULL DEFAULT 0,
  `gross_sales_quota` float(8,2) unsigned NOT NULL DEFAULT 300.00,
  `new_user_quota` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`affiliate_id`,`period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Reminders`
--

DROP TABLE IF EXISTS `Affiliates_Reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Reminders` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `date_contact` date NOT NULL DEFAULT '0000-00-00',
  `date_closed` date NOT NULL DEFAULT '0000-00-00',
  `priority` enum('urgent','normal','low') NOT NULL DEFAULT 'normal',
  `comments` text NOT NULL,
  `comments_closed` text NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`,`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1108 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Security_White_Listing_IPS`
--

DROP TABLE IF EXISTS `Affiliates_Security_White_Listing_IPS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Security_White_Listing_IPS` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_used` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `affiliate_id` int(10) unsigned NOT NULL DEFAULT 0,
  `ip_range_start` varbinary(16) NOT NULL DEFAULT '0',
  `ip_range_end` varbinary(16) NOT NULL DEFAULT '0',
  `domain` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_domain` (`affiliate_id`,`domain`),
  KEY `ip_range` (`ip_range_start`,`ip_range_end`)
) ENGINE=InnoDB AUTO_INCREMENT=1006 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Security_White_Listing_IPS_bak`
--

DROP TABLE IF EXISTS `Affiliates_Security_White_Listing_IPS_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Security_White_Listing_IPS_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_used` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `affiliate_id` int(10) unsigned NOT NULL DEFAULT 0,
  `ip_range_start` varbinary(16) NOT NULL DEFAULT '0',
  `ip_range_end` varbinary(16) NOT NULL DEFAULT '0',
  `domain` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_domain` (`affiliate_id`,`domain`),
  KEY `ip_range` (`ip_range_start`,`ip_range_end`)
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Sites`
--

DROP TABLE IF EXISTS `Affiliates_Sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Sites` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `site_url` varchar(255) NOT NULL DEFAULT '',
  `site_title` varchar(100) NOT NULL DEFAULT '',
  `service_type` varchar(150) NOT NULL DEFAULT '',
  `traffic_type` varchar(150) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=112932 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Terms`
--

DROP TABLE IF EXISTS `Affiliates_Terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Terms` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(255) NOT NULL DEFAULT '',
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`affiliate_id`,`version`,`details_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Unsubscribe`
--

DROP TABLE IF EXISTS `Affiliates_Unsubscribe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Unsubscribe` (
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_Video_API_Keys`
--

DROP TABLE IF EXISTS `Affiliates_Video_API_Keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_Video_API_Keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(20) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  `video_key` varchar(36) NOT NULL,
  `video_key_type` tinyint(4) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `video_key_type` (`video_key_type`)
) ENGINE=InnoDB AUTO_INCREMENT=8850 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliates_White_Label_Terms`
--

DROP TABLE IF EXISTS `Affiliates_White_Label_Terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliates_White_Label_Terms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(255) NOT NULL DEFAULT '',
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `domains` text NOT NULL,
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `unique_wl_rec` (`affiliate_id`,`details_id`,`version`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8220 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Auto_Tasks`
--

DROP TABLE IF EXISTS `Auto_Tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Auto_Tasks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id_owner` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id_assignee` smallint(5) unsigned NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `checklist` text NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `days_until_due` smallint(3) NOT NULL DEFAULT 0,
  `priority_id` smallint(3) unsigned NOT NULL DEFAULT 1,
  `is_private` enum('Y','N') NOT NULL DEFAULT 'N',
  `num_hours` float(5,2) NOT NULL DEFAULT 1.00,
  `is_liquid` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `admin_id_creator` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `admin_id_assignee` (`admin_id_assignee`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=342 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Auto_Tasks_Notifyees`
--

DROP TABLE IF EXISTS `Auto_Tasks_Notifyees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Auto_Tasks_Notifyees` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `auto_task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `auto_task_id` (`auto_task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3453 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Auto_Tasks_Recurrence`
--

DROP TABLE IF EXISTS `Auto_Tasks_Recurrence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Auto_Tasks_Recurrence` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `auto_task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `run_day` smallint(3) unsigned NOT NULL DEFAULT 0,
  `repetition` enum('weekly','monthly') NOT NULL DEFAULT 'weekly',
  PRIMARY KEY (`id`),
  KEY `auto_task_id` (`auto_task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=906 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `B2B_Tracker`
--

DROP TABLE IF EXISTS `B2B_Tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `B2B_Tracker` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10056 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `B2B_Tracker_Log`
--

DROP TABLE IF EXISTS `B2B_Tracker_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `B2B_Tracker_Log` (
  `tracker_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  KEY `tracker_id` (`tracker_id`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Banners`
--

DROP TABLE IF EXISTS `Banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Banners` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `relative_path` varchar(255) NOT NULL DEFAULT '',
  `type` enum('jpg','gif','png','swf') NOT NULL DEFAULT 'jpg',
  `service` enum('all','girls','guys','trans') NOT NULL DEFAULT 'girls',
  `rating` enum('softcore','hardcore') NOT NULL DEFAULT 'softcore',
  `sitekey` varchar(20) NOT NULL DEFAULT 'generic',
  `width` smallint(4) unsigned NOT NULL DEFAULT 0,
  `height` smallint(4) unsigned NOT NULL DEFAULT 0,
  `alt_text` varchar(255) NOT NULL DEFAULT '',
  `link_text` varchar(255) NOT NULL DEFAULT '',
  `swf_video_id_str` varchar(100) NOT NULL DEFAULT '',
  `date_added` date NOT NULL DEFAULT '0000-00-00',
  `models` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `relative_path` (`relative_path`),
  KEY `status` (`status`),
  KEY `wm_banners` (`sitekey`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=18209 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Banners_Models_Join`
--

DROP TABLE IF EXISTS `Banners_Models_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Banners_Models_Join` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `banner_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Blog_Categories`
--

DROP TABLE IF EXISTS `Blog_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blog_Categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10005 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Blog_Comments`
--

DROP TABLE IF EXISTS `Blog_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blog_Comments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `nickname` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varchar(255) NOT NULL DEFAULT '',
  `comments` longtext NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Blog_Posts`
--

DROP TABLE IF EXISTS `Blog_Posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blog_Posts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  `author_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `salesperson_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `contents` longtext NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_views` int(10) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=50324 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Blog_Posts_Join_Categories`
--

DROP TABLE IF EXISTS `Blog_Posts_Join_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blog_Posts_Join_Categories` (
  `post_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `category_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`post_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BriteVerify_API_Log`
--

DROP TABLE IF EXISTS `BriteVerify_API_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `BriteVerify_API_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL DEFAULT '',
  `account` varchar(100) NOT NULL DEFAULT '',
  `domain` varchar(100) NOT NULL DEFAULT '',
  `status` varchar(10) NOT NULL DEFAULT 'valid',
  `disposable` tinyint(1) NOT NULL DEFAULT 0,
  `role_address` tinyint(1) NOT NULL DEFAULT 0,
  `error_code` varchar(31) NOT NULL DEFAULT '',
  `date_added` date NOT NULL DEFAULT '0000-00-00',
  `risk` varchar(10) DEFAULT '',
  `reason` varchar(80) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `domain` (`domain`),
  KEY `status` (`status`),
  KEY `date_added` (`date_added`)
) ENGINE=InnoDB AUTO_INCREMENT=57537681 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasting_Workload_Queue_Settings`
--

DROP TABLE IF EXISTS `Broadcasting_Workload_Queue_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasting_Workload_Queue_Settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(50) NOT NULL DEFAULT '',
  `item` varchar(100) NOT NULL DEFAULT '',
  `item_name` varchar(100) NOT NULL DEFAULT '',
  `sort_order` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `queue_item` (`queue`,`item`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CPL_Allowed_Domains`
--

DROP TABLE IF EXISTS `CPL_Allowed_Domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CPL_Allowed_Domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CPR_Grid`
--

DROP TABLE IF EXISTS `CPR_Grid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CPR_Grid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(32) NOT NULL DEFAULT 'registration',
  `country_code` char(2) NOT NULL DEFAULT '',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `platform` enum('desktop','mobile') NOT NULL DEFAULT 'desktop',
  `amount` decimal(6,2) NOT NULL DEFAULT 0.00,
  `monthly_interval` int(10) unsigned NOT NULL DEFAULT 60,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ended_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_country_service_platform` (`entity_type`,`country_code`,`service`,`platform`),
  KEY `ended_at` (`ended_at`)
) ENGINE=InnoDB AUTO_INCREMENT=26221 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CPR_Grid_Interval`
--

DROP TABLE IF EXISTS `CPR_Grid_Interval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CPR_Grid_Interval` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(32) NOT NULL DEFAULT 'registration',
  `country_code` char(2) NOT NULL DEFAULT '',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `platform` enum('desktop','mobile') NOT NULL DEFAULT 'desktop',
  `amount` decimal(6,2) NOT NULL DEFAULT 0.00,
  `monthly_interval` int(10) unsigned NOT NULL DEFAULT 60,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ended_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_country_service_platform` (`entity_type`,`country_code`,`service`,`platform`),
  KEY `ended_at` (`ended_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1474 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CPR_Registrations`
--

DROP TABLE IF EXISTS `CPR_Registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CPR_Registrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reg_view_id` int(10) unsigned NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reg_view_id` (`reg_view_id`),
  KEY `email` (`email`),
  KEY `affiliate_mp_code` (`affiliate_id`,`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Channel_Types`
--

DROP TABLE IF EXISTS `Channel_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Channel_Types` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `channel_type` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `channel_type` (`channel_type`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Click_ID_Log`
--

DROP TABLE IF EXISTS `Click_ID_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Click_ID_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `click_id` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_mpcode` (`user_id`,`mp_code`),
  KEY `click_id` (`click_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3742617 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Plans`
--

DROP TABLE IF EXISTS `Commission_Plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Plans` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `commission_types_id` int(2) NOT NULL DEFAULT 1,
  `rate_type` enum('sliding','percent','pps','pps_sliding','cpl','cpa','cpr') DEFAULT NULL,
  `product_type` enum('live','vip','external','vod','videochat','performer','dating','psychic') DEFAULT NULL,
  `processing_fees` float(3,2) NOT NULL DEFAULT 0.00,
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `commission_types_id` (`commission_types_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10401 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Plans_CPA_Limits`
--

DROP TABLE IF EXISTS `Commission_Plans_CPA_Limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Plans_CPA_Limits` (
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_days` smallint(3) unsigned NOT NULL DEFAULT 30,
  `max_accounts` mediumint(6) unsigned NOT NULL DEFAULT 1000,
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Plans_CPL_Limits`
--

DROP TABLE IF EXISTS `Commission_Plans_CPL_Limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Plans_CPL_Limits` (
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_days` smallint(3) unsigned NOT NULL DEFAULT 30,
  `max_leads` mediumint(6) unsigned NOT NULL DEFAULT 1000,
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Plans_Details`
--

DROP TABLE IF EXISTS `Commission_Plans_Details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Plans_Details` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `break_point` float(10,2) unsigned NOT NULL DEFAULT 0.00,
  `comm_rate` float(6,3) unsigned NOT NULL DEFAULT 0.000,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `plan_id` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20572 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Plans_Details_Countries`
--

DROP TABLE IF EXISTS `Commission_Plans_Details_Countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Plans_Details_Countries` (
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `plan_details_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `country` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`plan_id`,`plan_details_id`,`country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Plans_Join`
--

DROP TABLE IF EXISTS `Commission_Plans_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Plans_Join` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `product_type` enum('live','vip','store','vod','psychic') NOT NULL DEFAULT 'live',
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changed_by` enum('affiliate','system','admin') NOT NULL DEFAULT 'system',
  `changed_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`affiliate_id`,`mp_code`,`product_type`,`date_end`),
  KEY `affiliate_id_2` (`affiliate_id`,`mp_code`,`product_type`,`plan_id`,`date_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Static`
--

DROP TABLE IF EXISTS `Commission_Static`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Static` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `product_type` varchar(20) NOT NULL DEFAULT 'live',
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `plan_details_id` smallint(5) unsigned DEFAULT NULL,
  `amount` float(8,2) NOT NULL DEFAULT 0.00,
  `transact_type` enum('sale','chargeback','refund') NOT NULL DEFAULT 'sale',
  `transact_id` int(11) unsigned DEFAULT NULL,
  `record_id` int(11) unsigned NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) DEFAULT 'girls',
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `date_imported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_exported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sequence_no` smallint(5) unsigned NOT NULL DEFAULT 0,
  `external_ref` varchar(32) NOT NULL DEFAULT '',
  `product_code` varchar(6) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `price_deductible` float(8,2) NOT NULL DEFAULT 0.00,
  `is_house` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `record_id` (`record_id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `external_ref` (`external_ref`),
  KEY `transact_id` (`transact_id`),
  KEY `username` (`username`),
  KEY `date_transact` (`date_transact`),
  KEY `user_id` (`user_id`),
  KEY `date` (`date`),
  KEY `commission_plan` (`plan_id`,`plan_details_id`),
  KEY `sequence_no` (`sequence_no`),
  KEY `transact_type` (`transact_type`),
  KEY `update_key` (`mp_code`,`affiliate_id`,`plan_details_id`,`plan_id`,`date_transact`),
  KEY `is_house` (`is_house`)
) ENGINE=InnoDB AUTO_INCREMENT=23403200 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Static_Seq`
--

DROP TABLE IF EXISTS `Commission_Static_Seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Static_Seq` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `last_seq_no` smallint(5) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`affiliate_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Static_from_import`
--

DROP TABLE IF EXISTS `Commission_Static_from_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Static_from_import` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `product_type` enum('live','vip','store','vod') NOT NULL DEFAULT 'live',
  `plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `plan_details_id` smallint(5) unsigned DEFAULT NULL,
  `amount` float(6,2) NOT NULL DEFAULT 0.00,
  `transact_type` enum('sale','chargeback','refund') NOT NULL DEFAULT 'sale',
  `record_id` int(11) unsigned NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `date_imported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_exported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sequence_no` smallint(5) unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Commission_Types`
--

DROP TABLE IF EXISTS `Commission_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Commission_Types` (
  `commission_types_id` int(2) NOT NULL AUTO_INCREMENT,
  `description` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`commission_types_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Contract_Admin_Users`
--

DROP TABLE IF EXISTS `Contract_Admin_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contract_Admin_Users` (
  `contract_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`contract_id`,`admin_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Contract_Attachments`
--

DROP TABLE IF EXISTS `Contract_Attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contract_Attachments` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `attachment_title` varchar(100) NOT NULL DEFAULT '',
  `attachment_url` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `contract_id` (`contract_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Contract_Categories`
--

DROP TABLE IF EXISTS `Contract_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contract_Categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Contract_Key_Dates`
--

DROP TABLE IF EXISTS `Contract_Key_Dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contract_Key_Dates` (
  `date_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(50) NOT NULL DEFAULT '',
  `message` varchar(500) NOT NULL DEFAULT '',
  `days_before` tinyint(3) NOT NULL DEFAULT 0,
  `last_notified` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`date_id`),
  UNIQUE KEY `contract_id` (`contract_id`,`date`,`days_before`,`type`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Contracts`
--

DROP TABLE IF EXISTS `Contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contracts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(250) NOT NULL DEFAULT '',
  `ext_contact` varchar(100) NOT NULL DEFAULT '',
  `effective_date` date NOT NULL DEFAULT '0000-00-00',
  `category_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `terms_details` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deal_Brokers`
--

DROP TABLE IF EXISTS `Deal_Brokers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deal_Brokers` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deal_MP_Codes`
--

DROP TABLE IF EXISTS `Deal_MP_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deal_MP_Codes` (
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `ad_type` varchar(20) NOT NULL DEFAULT '',
  `is_bulk` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`deal_id`,`mp_code`),
  KEY `ad_type` (`ad_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deal_Notes`
--

DROP TABLE IF EXISTS `Deal_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deal_Notes` (
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  KEY `deal_id` (`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deal_Settings`
--

DROP TABLE IF EXISTS `Deal_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deal_Settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` mediumint(8) unsigned DEFAULT NULL,
  `affiliate_id` mediumint(8) unsigned DEFAULT NULL,
  `setting_key` varchar(32) NOT NULL DEFAULT '',
  `setting_value` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `key_value` (`setting_key`,`setting_value`),
  KEY `deal_id` (`deal_id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deal_Spending`
--

DROP TABLE IF EXISTS `Deal_Spending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deal_Spending` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `date_paid` date NOT NULL DEFAULT '0000-00-00',
  `amount` float(11,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) NOT NULL DEFAULT '',
  `created_admin_id` smallint(5) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `deal_id` (`deal_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=590770 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deal_Spending_Changes`
--

DROP TABLE IF EXISTS `Deal_Spending_Changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deal_Spending_Changes` (
  `invoice_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text NOT NULL,
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deal_Stats`
--

DROP TABLE IF EXISTS `Deal_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deal_Stats` (
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `iframe_loads` int(11) unsigned NOT NULL DEFAULT 0,
  `new_customers` smallint(5) unsigned NOT NULL DEFAULT 0,
  `guest_logins` int(11) unsigned NOT NULL DEFAULT 0,
  `unique_hits` int(11) unsigned NOT NULL DEFAULT 0,
  `chat_logins` int(11) unsigned NOT NULL DEFAULT 0,
  `gross_sales` float(7,2) unsigned NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`deal_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deals`
--

DROP TABLE IF EXISTS `Deals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deals` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `contact_info` text NOT NULL,
  `campaign_type` varchar(32) NOT NULL DEFAULT '',
  `site_type` enum('adult','psychic','rtb','bct','mobile','affiliate','volume','PPC','xvc','xvt','xvtraffic','mb_retargeting') NOT NULL DEFAULT 'adult',
  `service` enum('girls','guys','trans','all') NOT NULL DEFAULT 'all',
  `affiliate_id` int(11) unsigned NOT NULL DEFAULT 0,
  `broker_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `default_ad_type` varchar(20) NOT NULL DEFAULT '',
  `current_dealmaker_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `import_costs` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `channel_type` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `site_type` (`site_type`),
  KEY `campaign_type` (`campaign_type`)
) ENGINE=InnoDB AUTO_INCREMENT=16095 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Do_Not_Publish`
--

DROP TABLE IF EXISTS `Do_Not_Publish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Do_Not_Publish` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_path` (`file_path`,`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11537 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Dynamic_Ads_Profiles`
--

DROP TABLE IF EXISTS `Dynamic_Ads_Profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Dynamic_Ads_Profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Dynamic_Ads_Targets`
--

DROP TABLE IF EXISTS `Dynamic_Ads_Targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Dynamic_Ads_Targets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) unsigned NOT NULL,
  `url` varchar(250) NOT NULL,
  `weight` mediumint(5) NOT NULL DEFAULT 100,
  `utm_term` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Dynamic_Sub_Accounts`
--

DROP TABLE IF EXISTS `Dynamic_Sub_Accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Dynamic_Sub_Accounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `affiliate_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_google` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `mp_code` (`mp_code`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `is_google` (`is_google`)
) ENGINE=InnoDB AUTO_INCREMENT=4359 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Email_Blast_Log`
--

DROP TABLE IF EXISTS `Email_Blast_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Email_Blast_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `identifier` varchar(100) NOT NULL DEFAULT '',
  `admin_user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_users` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `identifier` (`identifier`),
  KEY `date_sent` (`date_sent`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='A table meant to hold records of certain email blasts that have gone out from VSCash, for affiliates / White Labels.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Email_Revalidation_Queue`
--

DROP TABLE IF EXISTS `Email_Revalidation_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Email_Revalidation_Queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL DEFAULT '',
  `reason` varchar(80) DEFAULT '',
  `datetime_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_last_verified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=261843 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `amount` float(8,2) NOT NULL DEFAULT 0.00,
  `message` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `response_code` varchar(32) NOT NULL DEFAULT '',
  `response_string` varchar(255) NOT NULL DEFAULT '',
  `ep_user_id` varchar(32) NOT NULL DEFAULT '',
  `ep_debit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_fee_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_credit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_action_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL DEFAULT 'unknown',
  `pay_period_id` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`),
  KEY `ep_user_id` (`ep_user_id`),
  KEY `status` (`status`),
  KEY `mp_code` (`mp_code`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3904 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Exoclick_Campaign_Management`
--

DROP TABLE IF EXISTS `Exoclick_Campaign_Management`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Exoclick_Campaign_Management` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id_pause` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id_unpause` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime_paused` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_unpaused` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `campaigns` text NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Admin_Access`
--

DROP TABLE IF EXISTS `External_Admin_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Admin_Access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_type` enum('vscash','xvp','tier1','asia') DEFAULT 'vscash',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `amount` float(8,2) NOT NULL DEFAULT 0.00,
  `message` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `response_code` varchar(32) NOT NULL DEFAULT '',
  `response_string` varchar(255) NOT NULL DEFAULT '',
  `ep_user_id` varchar(32) NOT NULL DEFAULT '',
  `ep_debit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_fee_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_credit_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ep_action_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` varchar(32) NOT NULL DEFAULT 'unknown',
  `pay_period_id` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`),
  KEY `ep_user_id` (`ep_user_id`),
  KEY `status` (`status`),
  KEY `mp_code` (`mp_code`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16377 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ`
--

DROP TABLE IF EXISTS `FAQ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FAQ` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `question` varchar(150) NOT NULL DEFAULT '',
  `answer` text DEFAULT NULL,
  `secure` enum('yes','no','all') NOT NULL DEFAULT 'all',
  `votes_good` smallint(4) unsigned NOT NULL DEFAULT 0,
  `votes_total` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Form_1099_Affiliates`
--

DROP TABLE IF EXISTS `Form_1099_Affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Form_1099_Affiliates` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `year` year(4) NOT NULL DEFAULT 0000,
  `recipient_id` varchar(25) NOT NULL DEFAULT '',
  `compensation` float(10,2) NOT NULL DEFAULT 0.00,
  `name` varchar(75) NOT NULL DEFAULT '',
  `address` varchar(250) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `province` varchar(25) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  `postal_code` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`year`,`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fraudsters`
--

DROP TABLE IF EXISTS `Fraudsters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fraudsters` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fraudsters_Users`
--

DROP TABLE IF EXISTS `Fraudsters_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fraudsters_Users` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `fraudster_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `fraudster_id` (`fraudster_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4096 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Freshdesk_Tickets`
--

DROP TABLE IF EXISTS `Freshdesk_Tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Freshdesk_Tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `freshdesk_id` int(11) unsigned NOT NULL,
  `task_id` int(11) unsigned NOT NULL,
  `last_reply_id` bigint(11) unsigned DEFAULT 0,
  `freshdesk_status` tinyint(1) DEFAULT 2,
  `created_datetime` datetime DEFAULT current_timestamp(),
  `updated_datetime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `freshdesk_id` (`freshdesk_id`),
  KEY `created` (`created_datetime`),
  KEY `freshdesk_status` (`freshdesk_status`)
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Google_Adgroups`
--

DROP TABLE IF EXISTS `Google_Adgroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Google_Adgroups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `adgroups_id` varchar(256) NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `thridparty_deal_id` mediumint(11) unsigned NOT NULL DEFAULT 0,
  `status` varchar(256) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `adgroups_id_UNIQUE` (`adgroups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Has_Offers_Bounty_Log`
--

DROP TABLE IF EXISTS `Has_Offers_Bounty_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Has_Offers_Bounty_Log` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `click_id` varchar(250) NOT NULL DEFAULT '',
  `bounty_amount` float(6,2) NOT NULL DEFAULT 0.00,
  `bounty_prior` float(6,2) NOT NULL DEFAULT 0.00,
  `bounty_delta` float(6,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `click_id` (`click_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1377481 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `House_Code`
--

DROP TABLE IF EXISTS `House_Code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `House_Code` (
  `mp_code` varchar(12) NOT NULL,
  PRIMARY KEY (`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `IT_Orders`
--

DROP TABLE IF EXISTS `IT_Orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `IT_Orders` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `date_requested` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requesting_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `approving_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `description` varchar(512) NOT NULL DEFAULT '',
  `amount` float(7,2) unsigned NOT NULL DEFAULT 0.00,
  `approved` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=687 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `IT_Orders_Comments`
--

DROP TABLE IF EXISTS `IT_Orders_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `IT_Orders_Comments` (
  `order_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_commented` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `commentor_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  PRIMARY KEY (`order_id`,`date_commented`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Live_Notifications_Log`
--

DROP TABLE IF EXISTS `Live_Notifications_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Live_Notifications_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(256) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT 'all',
  `url` varchar(200) DEFAULT NULL,
  `user_type` varchar(20) DEFAULT NULL,
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date` (`date_sent`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MP_Change_Log`
--

DROP TABLE IF EXISTS `MP_Change_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MP_Change_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user` tinyint(3) unsigned DEFAULT NULL,
  `field` varchar(40) NOT NULL DEFAULT '',
  `old` varchar(255) NOT NULL DEFAULT '',
  `new` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3723 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MP_Followup`
--

DROP TABLE IF EXISTS `MP_Followup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MP_Followup` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `contact_name` varchar(50) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `urgency` enum('urgent','important','regular') NOT NULL DEFAULT 'regular',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `notes` text DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_id` (`admin_id`,`mp_code`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=2515 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MP_Logs`
--

DROP TABLE IF EXISTS `MP_Logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MP_Logs` (
  `admin_id` smallint(5) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `count` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`admin_id`,`mp_code`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MP_Views`
--

DROP TABLE IF EXISTS `MP_Views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MP_Views` (
  `admin_id` smallint(5) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `count` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`admin_id`,`mp_code`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Ad_Profiles`
--

DROP TABLE IF EXISTS `Media_Buying_Ad_Profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Ad_Profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `service` varchar(50) NOT NULL DEFAULT '',
  `width` smallint(5) unsigned NOT NULL DEFAULT 0,
  `height` smallint(5) unsigned NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Ad_Stats`
--

DROP TABLE IF EXISTS `Media_Buying_Ad_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Ad_Stats` (
  `minute` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `profile_id` int(11) unsigned NOT NULL,
  `target_id` int(11) unsigned NOT NULL,
  `url` varchar(250) NOT NULL,
  `mp_code` varchar(50) NOT NULL DEFAULT '',
  `impressions` int(11) unsigned NOT NULL DEFAULT 0,
  `clicks` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`minute`,`profile_id`,`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Ad_Stats_Daily`
--

DROP TABLE IF EXISTS `Media_Buying_Ad_Stats_Daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Ad_Stats_Daily` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `profile_id` int(11) unsigned NOT NULL DEFAULT 0,
  `target_id` int(11) unsigned NOT NULL,
  `impressions` int(11) unsigned NOT NULL DEFAULT 0,
  `clicks` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`profile_id`,`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Ad_Targets`
--

DROP TABLE IF EXISTS `Media_Buying_Ad_Targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Ad_Targets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) unsigned NOT NULL,
  `url` varchar(250) NOT NULL DEFAULT '',
  `type` enum('image','iframe') NOT NULL DEFAULT 'image',
  `source` varchar(500) NOT NULL DEFAULT '',
  `weight` mediumint(5) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `profile_id` (`profile_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=782 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_ByDeal`
--

DROP TABLE IF EXISTS `Media_Buying_ByDeal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_ByDeal` (
  `month_start` date NOT NULL DEFAULT '0000-00-00',
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `total_cost` float(10,2) NOT NULL DEFAULT 0.00,
  `net_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `net_profit` float(10,2) NOT NULL DEFAULT 0.00,
  `months_to_profit` smallint(3) unsigned NOT NULL DEFAULT 0,
  `dealmaker_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `status` enum('estimated','actual') NOT NULL DEFAULT 'estimated',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`month_start`,`deal_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_ByGroup`
--

DROP TABLE IF EXISTS `Media_Buying_ByGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_ByGroup` (
  `month_start` date NOT NULL DEFAULT '0000-00-00',
  `group_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `total_cost` float(10,2) NOT NULL DEFAULT 0.00,
  `net_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `net_profit` float(10,2) NOT NULL DEFAULT 0.00,
  `months_to_profit` smallint(3) unsigned NOT NULL DEFAULT 0,
  `status` enum('estimated','actual') NOT NULL DEFAULT 'estimated',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`month_start`,`group_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_ByMonth`
--

DROP TABLE IF EXISTS `Media_Buying_ByMonth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_ByMonth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month_start` date NOT NULL DEFAULT '0000-00-00',
  `site_type` varchar(255) NOT NULL DEFAULT 'adult',
  `total_cost` float(10,2) NOT NULL DEFAULT 0.00,
  `net_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `net_profit` float(10,2) NOT NULL DEFAULT 0.00,
  `months_to_profit` smallint(3) unsigned NOT NULL DEFAULT 0,
  `mtp_projected` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` enum('estimated','actual') NOT NULL DEFAULT 'estimated',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `channel_type` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `channel_type` (`channel_type`),
  KEY `ms` (`month_start`,`site_type`),
  KEY `mc` (`month_start`,`channel_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3422 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_ByMonth_ByService`
--

DROP TABLE IF EXISTS `Media_Buying_ByMonth_ByService`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_ByMonth_ByService` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month_start` date NOT NULL DEFAULT '0000-00-00',
  `site_type` enum('adult','psychic','bct','mobile','affiliate','volume','PPC','xvc','xvt','xvtraffic','rtb','unknown') DEFAULT NULL,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `total_cost` float(10,2) NOT NULL DEFAULT 0.00,
  `net_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `net_profit` float(10,2) NOT NULL DEFAULT 0.00,
  `months_to_profit` smallint(3) unsigned NOT NULL DEFAULT 0,
  `status` enum('estimated','actual') NOT NULL DEFAULT 'estimated',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `channel_type` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `channel_type` (`channel_type`),
  KEY `mss` (`month_start`,`site_type`,`service`),
  KEY `mcs` (`month_start`,`channel_type`,`service`)
) ENGINE=InnoDB AUTO_INCREMENT=4009 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Deal_Groups`
--

DROP TABLE IF EXISTS `Media_Buying_Deal_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Deal_Groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(256) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Deal_Groups_Join`
--

DROP TABLE IF EXISTS `Media_Buying_Deal_Groups_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Deal_Groups_Join` (
  `group_id` mediumint(8) unsigned NOT NULL,
  `deal_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`deal_id`),
  KEY `group_id` (`group_id`),
  KEY `deal_id` (`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Dist_Configuration`
--

DROP TABLE IF EXISTS `Media_Buying_Dist_Configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Dist_Configuration` (
  `config_name` varchar(100) NOT NULL DEFAULT '',
  `config_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Dist_FG_Carrier_IP`
--

DROP TABLE IF EXISTS `Media_Buying_Dist_FG_Carrier_IP`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Dist_FG_Carrier_IP` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_addr_start` varbinary(16) NOT NULL DEFAULT '',
  `ip_addr_end` varbinary(16) NOT NULL DEFAULT '',
  `carrier` varchar(30) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  `redirect_url` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx1` (`ip_addr_start`,`ip_addr_end`,`redirect_url`)
) ENGINE=InnoDB AUTO_INCREMENT=778 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Dist_FG_Redirect_Stats`
--

DROP TABLE IF EXISTS `Media_Buying_Dist_FG_Redirect_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Dist_FG_Redirect_Stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `redirect_minute` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `url` varchar(250) NOT NULL DEFAULT '',
  `mp_code` varchar(10) NOT NULL DEFAULT '',
  `service` varchar(10) NOT NULL DEFAULT '',
  `carrier` varchar(50) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  `profile_id` int(11) unsigned NOT NULL DEFAULT 0,
  `count` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx` (`redirect_minute`,`url`,`mp_code`,`service`,`carrier`,`country_code`,`profile_id`),
  KEY `idx1` (`redirect_minute`,`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Dist_Profiles`
--

DROP TABLE IF EXISTS `Media_Buying_Dist_Profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Dist_Profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `utm_source` varchar(25) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `allow_ip_redirect` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=3895 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Dist_Targets`
--

DROP TABLE IF EXISTS `Media_Buying_Dist_Targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Dist_Targets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) unsigned NOT NULL,
  `url` varchar(350) NOT NULL DEFAULT '',
  `weight` mediumint(5) NOT NULL DEFAULT 0,
  `utm_medium` varchar(50) NOT NULL DEFAULT '',
  `utm_content` varchar(50) NOT NULL DEFAULT '',
  `filter_country_code` char(2) DEFAULT NULL,
  `filter_device_type` varchar(24) DEFAULT NULL,
  `filter_network_type` varchar(16) DEFAULT NULL,
  `filter_carrier` varchar(32) NOT NULL DEFAULT '',
  `filter_service` varchar(16) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `profile_id` (`profile_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=12490 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Group_Notes`
--

DROP TABLE IF EXISTS `Media_Buying_Group_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Group_Notes` (
  `group_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Projections`
--

DROP TABLE IF EXISTS `Media_Buying_Projections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Projections` (
  `month_start` date NOT NULL DEFAULT '0000-00-00',
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_uniques` int(11) unsigned NOT NULL DEFAULT 0,
  `num_reg` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_add_cc` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`month_start`,`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Traffic`
--

DROP TABLE IF EXISTS `Media_Buying_Traffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Traffic` (
  `month_start` date NOT NULL DEFAULT '0000-00-00',
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_impressions` int(11) unsigned NOT NULL DEFAULT 0,
  `num_uniques` int(11) unsigned NOT NULL DEFAULT 0,
  `num_reg` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_conf` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`month_start`,`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Transactions`
--

DROP TABLE IF EXISTS `Media_Buying_Transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Transactions` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_acquired` date NOT NULL DEFAULT '0000-00-00',
  `date_transact` date NOT NULL DEFAULT '0000-00-00',
  `transact_month_num` mediumint(4) unsigned NOT NULL DEFAULT 1,
  `country` char(2) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT '',
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `gross_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `net_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `num_txn` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_chargeback` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_refund` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `existing_user_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`date_acquired`,`date_transact`),
  KEY `country` (`country`),
  KEY `service` (`service`),
  KEY `date_transact` (`date_transact`),
  KEY `user_id` (`user_id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `mp_code` (`mp_code`),
  KEY `existing_user_id` (`existing_user_id`),
  KEY `date_acquired` (`date_acquired`),
  KEY `deal_country` (`deal_id`,`country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Transactions_OLD`
--

DROP TABLE IF EXISTS `Media_Buying_Transactions_OLD`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Transactions_OLD` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_acquired` date NOT NULL DEFAULT '0000-00-00',
  `date_transact` date NOT NULL DEFAULT '0000-00-00',
  `transact_month_num` mediumint(4) unsigned NOT NULL DEFAULT 1,
  `country` char(2) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT '',
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `gross_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `net_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `num_txn` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_chargeback` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_refund` mediumint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`date_acquired`,`date_transact`),
  KEY `deal_id` (`deal_id`),
  KEY `country` (`country`),
  KEY `service` (`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Media_Buying_Transactions_TEST`
--

DROP TABLE IF EXISTS `Media_Buying_Transactions_TEST`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Media_Buying_Transactions_TEST` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_acquired` date NOT NULL DEFAULT '0000-00-00',
  `date_transact` date NOT NULL DEFAULT '0000-00-00',
  `transact_month_num` mediumint(4) unsigned NOT NULL DEFAULT 1,
  `country` char(2) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT '',
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `gross_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `net_revenue` float(10,2) NOT NULL DEFAULT 0.00,
  `num_txn` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_chargeback` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `num_refund` mediumint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`date_acquired`,`date_transact`),
  KEY `deal_id` (`deal_id`),
  KEY `country` (`country`),
  KEY `service` (`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `News`
--

DROP TABLE IF EXISTS `News`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `News` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `datetime` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `One_Click_Types`
--

DROP TABLE IF EXISTS `One_Click_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `One_Click_Types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PPS_qualified`
--

DROP TABLE IF EXISTS `PPS_qualified`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `PPS_qualified` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `date_qualified` datetime DEFAULT NULL,
  `total_spent` float(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Paylocity_Employees_Out`
--

DROP TABLE IF EXISTS `Paylocity_Employees_Out`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Paylocity_Employees_Out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` varchar(255) NOT NULL DEFAULT '',
  `employee_id` mediumint(8) NOT NULL DEFAULT 0,
  `employee_name` varchar(255) NOT NULL DEFAULT '',
  `employee_email` varchar(255) NOT NULL DEFAULT '',
  `supervisor_email` varchar(255) NOT NULL DEFAULT '',
  `time_off_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_off_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `all_day_event` enum('Y','N') NOT NULL DEFAULT 'N',
  `hours_per_day` decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`),
  KEY `time_off_start` (`time_off_start`),
  KEY `time_off_end` (`time_off_end`)
) ENGINE=InnoDB AUTO_INCREMENT=840 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payout`
--

DROP TABLE IF EXISTS `Payout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payout` (
  `payout_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `affiliate_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `adj_gross_sales` decimal(8,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `earn_period_id` int(4) NOT NULL DEFAULT 0,
  `pay_period_id` int(4) NOT NULL DEFAULT 0,
  `new_users` mediumint(8) unsigned NOT NULL,
  `on_hold` char(1) NOT NULL DEFAULT 'N',
  `hold_reason_id` int(11) unsigned NOT NULL DEFAULT 0,
  `reverse_period_id` int(11) NOT NULL DEFAULT 0 COMMENT 'Indicates that payout was previously marked as paid, but has been reversed.',
  `reverse_date` date DEFAULT NULL COMMENT 'The date when a previously paid payout had been reversed',
  `reverse_admin_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Who initiated a Payout reversal',
  PRIMARY KEY (`payout_id`),
  KEY `payout_idx` (`affiliate_id`,`earn_period_id`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `amount` (`amount`),
  KEY `affiliate_details_id` (`affiliate_details_id`)
) ENGINE=InnoDB AUTO_INCREMENT=359216 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payout_Adjustment`
--

DROP TABLE IF EXISTS `Payout_Adjustment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payout_Adjustment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `description` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `status` (`status`),
  KEY `amount` (`amount`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=3486 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payout_Periods`
--

DROP TABLE IF EXISTS `Payout_Periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payout_Periods` (
  `period_id` int(40) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`period_id`),
  UNIQUE KEY `pay_period_idx` (`start_date`,`end_date`)
) ENGINE=InnoDB AUTO_INCREMENT=463 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payout_Rollover`
--

DROP TABLE IF EXISTS `Payout_Rollover`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payout_Rollover` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `date_end` date NOT NULL DEFAULT '0000-00-00',
  `net_payout` float(8,2) NOT NULL DEFAULT 0.00,
  `is_paid` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_paid` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_code` (`mp_code`,`date_end`)
) ENGINE=InnoDB AUTO_INCREMENT=16161 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payout_bk`
--

DROP TABLE IF EXISTS `Payout_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payout_bk` (
  `payout_id` int(11) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `affiliate_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `adj_gross_sales` decimal(8,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `earn_period_id` int(4) NOT NULL DEFAULT 0,
  `pay_period_id` int(4) NOT NULL DEFAULT 0,
  `new_users` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payout_bk2`
--

DROP TABLE IF EXISTS `Payout_bk2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payout_bk2` (
  `payout_id` int(11) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `affiliate_details_id` int(11) unsigned NOT NULL DEFAULT 0,
  `adj_gross_sales` decimal(8,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `earn_period_id` int(4) NOT NULL DEFAULT 0,
  `pay_period_id` int(4) NOT NULL DEFAULT 0,
  `new_users` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Priority_Task_Boards`
--

DROP TABLE IF EXISTS `Priority_Task_Boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Priority_Task_Boards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `sort` int(10) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Priority_Task_Groups`
--

DROP TABLE IF EXISTS `Priority_Task_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Priority_Task_Groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Priority_Task_Groups_Members`
--

DROP TABLE IF EXISTS `Priority_Task_Groups_Members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Priority_Task_Groups_Members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priority_task_group_id` int(10) unsigned NOT NULL DEFAULT 0,
  `member_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_department` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `priority_task_group_id` (`priority_task_group_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1686 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Priority_Tasks`
--

DROP TABLE IF EXISTS `Priority_Tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Priority_Tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priority_task_group_id` int(10) unsigned NOT NULL DEFAULT 0,
  `projects_task_id` int(10) unsigned NOT NULL DEFAULT 0,
  `added_by_id` int(10) unsigned NOT NULL DEFAULT 0,
  `board_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sort` int(10) unsigned NOT NULL DEFAULT 999,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `priority_task_group_id` (`priority_task_group_id`),
  KEY `projects_task_id` (`projects_task_id`),
  KEY `added_by_id` (`added_by_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3363 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Bugs_Comments`
--

DROP TABLE IF EXISTS `Projects_Bugs_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Bugs_Comments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Bugs_Groups`
--

DROP TABLE IF EXISTS `Projects_Bugs_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Bugs_Groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10007 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Bugs_Items`
--

DROP TABLE IF EXISTS `Projects_Bugs_Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Bugs_Items` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assignee_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `status` (`status`),
  KEY `created_datetime` (`created_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Change_Log`
--

DROP TABLE IF EXISTS `Projects_Change_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Change_Log` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `change_type_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1229569 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Change_Types`
--

DROP TABLE IF EXISTS `Projects_Change_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Change_Types` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(30) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Checklist`
--

DROP TABLE IF EXISTS `Projects_Checklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Checklist` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` mediumint(8) unsigned NOT NULL,
  `item` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `cu_checklist_item_id` varchar(48) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=221186 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Comments`
--

DROP TABLE IF EXISTS `Projects_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Comments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assisted_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `message` text DEFAULT NULL,
  `num_minutes` mediumint(3) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `clickup_comment_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `task_admin_date` (`task_id`,`admin_id`,`date_created`),
  KEY `admin_date` (`admin_id`,`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=2842076 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Committees`
--

DROP TABLE IF EXISTS `Projects_Committees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Committees` (
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL DEFAULT '',
  `purpose` text DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `admin_id_chair` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Committees_Members`
--

DROP TABLE IF EXISTS `Projects_Committees_Members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Committees_Members` (
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`task_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Deliverables`
--

DROP TABLE IF EXISTS `Projects_Deliverables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Deliverables` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` mediumint(8) unsigned NOT NULL,
  `deliverables` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_id` (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Department_Activity`
--

DROP TABLE IF EXISTS `Projects_Department_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Department_Activity` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `admin_user_department_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `open_hours` float(6,2) NOT NULL DEFAULT 0.00,
  `open_tasks` smallint(5) unsigned NOT NULL DEFAULT 0,
  `new_hours` float(6,2) NOT NULL DEFAULT 0.00,
  `new_tasks` smallint(5) unsigned NOT NULL DEFAULT 0,
  `pastdue_hours` float(6,2) NOT NULL DEFAULT 0.00,
  `pastdue_tasks` smallint(5) unsigned NOT NULL DEFAULT 0,
  `logged_hours` float(6,2) NOT NULL DEFAULT 0.00,
  `logged_tasks` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`admin_user_department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Departments`
--

DROP TABLE IF EXISTS `Projects_Departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Departments` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `abbreviation` varchar(5) NOT NULL DEFAULT '',
  `task_ratio` smallint(5) NOT NULL DEFAULT 1,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10130 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Departments2Users`
--

DROP TABLE IF EXISTS `Projects_Departments2Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Departments2Users` (
  `department_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`department_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Files`
--

DROP TABLE IF EXISTS `Projects_Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Files` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `upload_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `file_name` varchar(50) NOT NULL DEFAULT '',
  `file_size` int(10) unsigned NOT NULL DEFAULT 0,
  `file_mime_type` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `clickup_task_id` varchar(16) NOT NULL DEFAULT '' COMMENT 'Clickup Side Task ID',
  `clickup_file_url` varchar(255) NOT NULL DEFAULT '' COMMENT 'Clickup File URL',
  `clickup_file_id` varchar(64) NOT NULL DEFAULT '' COMMENT 'Clickup File UID',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=149689 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Leads`
--

DROP TABLE IF EXISTS `Projects_Leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Leads` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `task_id` int(12) NOT NULL DEFAULT 0,
  `admin_lead_id` int(12) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `task_lead` (`admin_lead_id`,`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Logjam`
--

DROP TABLE IF EXISTS `Projects_Logjam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Logjam` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `ref_comment_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id_owner` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id_assignee` smallint(5) unsigned NOT NULL DEFAULT 0,
  `cleared_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('cleared','jammed') NOT NULL DEFAULT 'jammed',
  PRIMARY KEY (`id`),
  KEY `admin_id_owner` (`admin_id_owner`),
  KEY `task_id` (`task_id`),
  KEY `admin_id_assignee` (`admin_id_assignee`)
) ENGINE=InnoDB AUTO_INCREMENT=1080 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Notification`
--

DROP TABLE IF EXISTS `Projects_Notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Notification` (
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`task_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Notification_Overrides`
--

DROP TABLE IF EXISTS `Projects_Notification_Overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Notification_Overrides` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `notify_field` enum('admin_id_owner','admin_id_originator','admin_id_assignee') NOT NULL DEFAULT 'admin_id_assignee' COMMENT 'admin_id_owner, admin_id_originator, admin_id_assignee',
  `notify_id` smallint(3) unsigned NOT NULL,
  `notify_by_type` enum('team','admin','position') NOT NULL DEFAULT 'admin' COMMENT 'team, admin, position',
  `notify_admin_id` smallint(3) unsigned NOT NULL,
  `notify_status` smallint(1) NOT NULL DEFAULT 0,
  `status` smallint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Objectives`
--

DROP TABLE IF EXISTS `Projects_Objectives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Objectives` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `prefix` varchar(25) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `admin_id_owner` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id_manager` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_due` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `prefix` (`prefix`)
) ENGINE=InnoDB AUTO_INCREMENT=10175 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Priority_Types`
--

DROP TABLE IF EXISTS `Projects_Priority_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Priority_Types` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(30) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_SVN`
--

DROP TABLE IF EXISTS `Projects_SVN`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_SVN` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `related_task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `file_path_md5` varchar(32) NOT NULL DEFAULT '',
  `revision_num` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `file_path_md5` (`file_path_md5`),
  KEY `related_task_id` (`related_task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=420348 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Snapshots`
--

DROP TABLE IF EXISTS `Projects_Snapshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Snapshots` (
  `grouping` varchar(20) NOT NULL DEFAULT '',
  `week_ending` date NOT NULL DEFAULT '0000-00-00',
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `week_start_status` enum('open','past_due','not_exist') NOT NULL DEFAULT 'open',
  `week_end_status` enum('open','past_due','completed','pushed') NOT NULL DEFAULT 'open',
  `minutes_logged` mediumint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`grouping`,`week_ending`,`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Tasks`
--

DROP TABLE IF EXISTS `Projects_Tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Tasks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned DEFAULT NULL,
  `department_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `deal_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `documentation_id` varchar(14) NOT NULL DEFAULT '',
  `quarterly_objective` varchar(7) NOT NULL DEFAULT '',
  `admin_id_originator` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id_owner` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id_assignee` smallint(5) unsigned NOT NULL DEFAULT 0,
  `team_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `task_type` enum('task','bugfix','proposal','goal','ongoing_issue','recurring','pd') DEFAULT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `date_created` datetime DEFAULT NULL,
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_due` date DEFAULT NULL,
  `date_last_updated` datetime DEFAULT NULL,
  `priority_id` smallint(3) unsigned NOT NULL DEFAULT 1,
  `is_private` enum('Y','N') NOT NULL DEFAULT 'N',
  `num_hours` float(5,2) NOT NULL DEFAULT 1.00,
  `percent_completed` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `order_key` smallint(3) unsigned NOT NULL DEFAULT 0,
  `is_liquid` enum('Y','N') NOT NULL DEFAULT 'N',
  `taken_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `liquid_overflow` int(11) NOT NULL DEFAULT 0,
  `overflow_php` int(11) NOT NULL DEFAULT 0,
  `overflow_design` int(11) NOT NULL DEFAULT 0,
  `overflow_billing` int(11) NOT NULL DEFAULT 0,
  `overflow_systems` int(11) NOT NULL DEFAULT 0,
  `needs_review` mediumint(5) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `clickup_id` varchar(16) NOT NULL DEFAULT '',
  `checklist_id` varchar(48) NOT NULL DEFAULT '',
  `cu_api_token` varchar(48) NOT NULL DEFAULT '',
  `cu_priority_order` varchar(10) DEFAULT NULL,
  `cu_company_objective` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`id`,`parent_id`,`admin_id_originator`),
  KEY `admin_id_asignee` (`admin_id_assignee`),
  KEY `status` (`status`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `title` (`title`),
  KEY `parent_id_only` (`parent_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=595954 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Teams`
--

DROP TABLE IF EXISTS `Projects_Teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Teams` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Teams_Members`
--

DROP TABLE IF EXISTS `Projects_Teams_Members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Teams_Members` (
  `team_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `manager` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`team_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Timelog`
--

DROP TABLE IF EXISTS `Projects_Timelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Timelog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_minutes` mediumint(3) unsigned NOT NULL DEFAULT 0,
  `clickup_interval_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=1839643 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_View_Log`
--

DROP TABLE IF EXISTS `Projects_View_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_View_Log` (
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_first_viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_viewed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_times_viewed` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`task_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Projects_Watch`
--

DROP TABLE IF EXISTS `Projects_Watch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Projects_Watch` (
  `task_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`task_id`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Query_Totals`
--

DROP TABLE IF EXISTS `Query_Totals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Query_Totals` (
  `hostname` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `is_mirror` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `reads` int(11) unsigned NOT NULL DEFAULT 0,
  `writes` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`hostname`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Retargeting_Codes`
--

DROP TABLE IF EXISTS `Retargeting_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Retargeting_Codes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `platform_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `event_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `tracking_code` varchar(512) NOT NULL,
  `goal_name` varchar(64) NOT NULL,
  `advertiser_id` varchar(32) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform_event_domain` (`platform_id`,`event_id`,`domain`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Retargeting_Events`
--

DROP TABLE IF EXISTS `Retargeting_Events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Retargeting_Events` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `description` varchar(512) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Retargeting_Platforms`
--

DROP TABLE IF EXISTS `Retargeting_Platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Retargeting_Platforms` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `js_url` varchar(512) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Revenue_Per_Registration`
--

DROP TABLE IF EXISTS `Revenue_Per_Registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Revenue_Per_Registration` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reg_start_date` date NOT NULL DEFAULT '0000-00-00',
  `reg_end_date` date NOT NULL DEFAULT '0000-00-00',
  `service` varchar(5) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT '',
  `device` varchar(32) NOT NULL DEFAULT '',
  `reg_total` int(11) unsigned NOT NULL DEFAULT 0,
  `revenue_total` float(11,2) NOT NULL DEFAULT 0.00,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `month_service_country_device` (`reg_start_date`,`service`,`country`,`device`,`reg_end_date`),
  KEY `service` (`service`),
  KEY `country` (`country`),
  KEY `device` (`device`),
  KEY `month` (`reg_start_date`),
  KEY `reg_month_end` (`reg_end_date`)
) ENGINE=InnoDB AUTO_INCREMENT=52577 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `S2S_Pixels_Log`
--

DROP TABLE IF EXISTS `S2S_Pixels_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `S2S_Pixels_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pixel_type` varchar(32) NOT NULL DEFAULT 'registration',
  `registration_view_id` int(11) unsigned NOT NULL DEFAULT 0,
  `is_oneclick` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `registration_view_id` (`registration_view_id`),
  KEY `user_id` (`user_id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `pixel_type` (`pixel_type`),
  KEY `date` (`date`),
  KEY `mp_code` (`mp_code`),
  KEY `is_oneclick` (`is_oneclick`)
) ENGINE=InnoDB AUTO_INCREMENT=8693098 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `S2S_Postback_Queue`
--

DROP TABLE IF EXISTS `S2S_Postback_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `S2S_Postback_Queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pixel_type` varchar(32) NOT NULL DEFAULT 'registration',
  `entity_type` varchar(32) NOT NULL DEFAULT 'affiliate',
  `entity_id` varchar(255) NOT NULL DEFAULT '',
  `registration_view_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_oneclick` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `is_voluum` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned DEFAULT NULL,
  `transaction_id` int(10) unsigned DEFAULT NULL,
  `url` text NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT 3,
  `is_test` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `server` varchar(255) NOT NULL DEFAULT '',
  `tracker_id` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pixel_entity_regview_oneclick` (`pixel_type`,`entity_type`,`registration_view_id`,`is_oneclick`),
  KEY `user_txn` (`user_id`,`transaction_id`),
  KEY `tracker_id` (`tracker_id`),
  KEY `priority_server` (`priority`,`server`),
  KEY `processed_at` (`processed_at`),
  KEY `created_id` (`created_at`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15185620 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `S2S_Postback_Summary`
--

DROP TABLE IF EXISTS `S2S_Postback_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `S2S_Postback_Summary` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pixel_type` varchar(32) NOT NULL DEFAULT 'registration',
  `registration_type` enum('standard','1click') NOT NULL DEFAULT 'standard',
  `traffic_type` enum('standard','voluum') NOT NULL DEFAULT 'standard',
  `date_fired` date NOT NULL DEFAULT '0000-00-00',
  `num_success` int(10) unsigned DEFAULT NULL,
  `num_failed` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pixel_reg_traffic` (`pixel_type`,`registration_type`,`traffic_type`),
  KEY `date_fired` (`date_fired`)
) ENGINE=InnoDB AUTO_INCREMENT=28593 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `S2S_Tracker_Configs`
--

DROP TABLE IF EXISTS `S2S_Tracker_Configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `S2S_Tracker_Configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pixel_type` varchar(32) NOT NULL DEFAULT 'registration',
  `entity_type` varchar(32) NOT NULL DEFAULT 'affiliate',
  `entity_id` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `is_ppl` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `is_voluum` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `api_url` varchar(512) NOT NULL DEFAULT '',
  `response_type` enum('http_code','string','image','xml') NOT NULL DEFAULT 'http_code',
  `response_value` varchar(50) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `pixel_entity_ppl_voluum` (`pixel_type`,`entity_type`,`is_ppl`,`is_voluum`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=18071 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `S2S_Trackers`
--

DROP TABLE IF EXISTS `S2S_Trackers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `S2S_Trackers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pixel_type` varchar(32) NOT NULL DEFAULT 'registration',
  `tracker_type` enum('affiliate_id','deal_id','mp_code','catch_all') NOT NULL DEFAULT 'affiliate_id',
  `tracker_type_id` varchar(9) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `is_ppl` tinyint(1) NOT NULL DEFAULT 1,
  `api_url` varchar(255) NOT NULL DEFAULT '',
  `response_type` enum('http_code','string','image','xml') NOT NULL DEFAULT 'http_code',
  `response_value` varchar(30) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `tracker_type` (`tracker_type`),
  KEY `pixel_tracker` (`pixel_type`,`tracker_type`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2546 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SEO_Organic_Traffic`
--

DROP TABLE IF EXISTS `SEO_Organic_Traffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SEO_Organic_Traffic` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `engine` varchar(20) NOT NULL DEFAULT '',
  `domain` varchar(100) NOT NULL DEFAULT '',
  `request` varchar(150) NOT NULL DEFAULT '',
  `phrase` varchar(100) NOT NULL DEFAULT '',
  `total` int(11) unsigned NOT NULL DEFAULT 0,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `phrase` (`phrase`)
) ENGINE=InnoDB AUTO_INCREMENT=1596624 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SEO_Top_Pages`
--

DROP TABLE IF EXISTS `SEO_Top_Pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SEO_Top_Pages` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `page` varchar(255) NOT NULL DEFAULT '',
  `total_requests` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SVN_Watch`
--

DROP TABLE IF EXISTS `SVN_Watch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SVN_Watch` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `file_index` (`file_path`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Simulated_Traffic_Rate`
--

DROP TABLE IF EXISTS `Simulated_Traffic_Rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Simulated_Traffic_Rate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hour` tinyint(4) NOT NULL,
  `service` enum('girls','guys','trans') DEFAULT NULL,
  `value` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3168 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Support_Users`
--

DROP TABLE IF EXISTS `Support_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Support_Users` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) NOT NULL DEFAULT '',
  `last_name` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(80) DEFAULT NULL,
  `password` varchar(41) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TaskStream_Users_Queues`
--

DROP TABLE IF EXISTS `TaskStream_Users_Queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TaskStream_Users_Queues` (
  `admin_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `queue_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT 0,
  UNIQUE KEY `Users_Queues` (`admin_id`,`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Task_Topics`
--

DROP TABLE IF EXISTS `Task_Topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Task_Topics` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `topic` varchar(100) NOT NULL,
  `department` int(5) NOT NULL,
  `channels` varchar(250) NOT NULL,
  `tags` varchar(250) NOT NULL,
  `group_tags` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tax_Service_Fields`
--

DROP TABLE IF EXISTS `Tax_Service_Fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tax_Service_Fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `form_type` enum('W9','W8-BEN','W8-BEN-E') NOT NULL,
  `serialized_form_data` text NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `composite_index` (`affiliate_id`,`form_type`,`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=1817 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Model_Profile`
--

DROP TABLE IF EXISTS `User_Model_Profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Model_Profile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `date_insert` date NOT NULL DEFAULT '0000-00-00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reg_type` varchar(12) NOT NULL DEFAULT '',
  `service` varchar(5) NOT NULL DEFAULT '',
  `first_money_amount` float(6,2) DEFAULT NULL,
  `country_code` char(2) NOT NULL DEFAULT '',
  `model_flag` varchar(14) NOT NULL DEFAULT '',
  `txn_days` int(11) unsigned NOT NULL DEFAULT 0,
  `channel_type` varchar(20) NOT NULL DEFAULT '',
  `device_type` float(4,2) NOT NULL DEFAULT 0.00,
  `fourteen_day_rev` float(8,2) NOT NULL DEFAULT 0.00,
  `fourteen_day_txns` int(11) unsigned NOT NULL DEFAULT 0,
  `fourteen_day_shows` int(8) unsigned NOT NULL DEFAULT 0,
  `fourteen_day_credits_spent` mediumint(7) unsigned NOT NULL,
  `fourteen_day_tips` int(11) unsigned NOT NULL DEFAULT 0,
  `fourteen_day_tip_cred` mediumint(7) NOT NULL DEFAULT 0,
  `fourteen_day_gift_messages` int(11) unsigned NOT NULL DEFAULT 0,
  `one_year_rev` float(8,2) NOT NULL DEFAULT 0.00,
  `one_year_txns` int(11) unsigned NOT NULL DEFAULT 0,
  `training_flag` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_date_created` (`user_id`,`date_created`),
  KEY `date_insert` (`date_insert`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Postbacks`
--

DROP TABLE IF EXISTS `User_Postbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Postbacks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pixel_type` enum('registration','confirmation','add_cc','first_purchase','sale','whale','bad_lead','chargeback','refund') NOT NULL DEFAULT 'registration',
  `ad_network` enum('voluum','exoclick') NOT NULL DEFAULT 'voluum',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `reg_view_id` int(10) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `click_id` varchar(255) NOT NULL DEFAULT '',
  `date_fired` date NOT NULL DEFAULT '0000-00-00',
  `is_oneclick` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `type_network_date` (`pixel_type`,`ad_network`,`date_fired`),
  KEY `user_mpcode_reg` (`user_id`,`mp_code`,`reg_view_id`),
  KEY `reg_type` (`reg_view_id`,`pixel_type`),
  KEY `user_type` (`user_id`,`pixel_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1970086 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Voluum_Offers`
--

DROP TABLE IF EXISTS `Voluum_Offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Voluum_Offers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offer_id` varchar(36) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(1024) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `offer_id` (`offer_id`),
  KEY `sitekey` (`sitekey`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=852255 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Webgroup_Traffic_Accounts`
--

DROP TABLE IF EXISTS `Webgroup_Traffic_Accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Webgroup_Traffic_Accounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `api_password` varchar(255) DEFAULT NULL,
  `property_group` varchar(255) DEFAULT NULL,
  `property_name` varchar(255) DEFAULT NULL,
  `api_origin` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `spending_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label`
--

DROP TABLE IF EXISTS `White_Label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '0000',
  `template_set` varchar(20) NOT NULL DEFAULT 'basic_02',
  `title` varchar(255) NOT NULL DEFAULT '',
  `short_title` varchar(100) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `custom_text` varchar(255) NOT NULL DEFAULT '',
  `service_default` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `service_girls` enum('Y','N') NOT NULL DEFAULT 'Y',
  `service_guys` enum('Y','N') NOT NULL DEFAULT 'N',
  `service_trans` enum('Y','N') NOT NULL DEFAULT 'N',
  `logo_mp_code` varchar(12) NOT NULL DEFAULT '',
  `background_url` varchar(255) NOT NULL DEFAULT '',
  `external_css_url` varchar(255) NOT NULL DEFAULT '',
  `external_favicon_url` varchar(255) NOT NULL DEFAULT '',
  `color_1_bg` varchar(7) NOT NULL DEFAULT '#ffffff',
  `color_1_text` varchar(7) NOT NULL DEFAULT '#000000',
  `color_1_link` varchar(7) NOT NULL DEFAULT '#000099',
  `color_2_bg` varchar(7) NOT NULL DEFAULT '#ffffff',
  `color_2_text` varchar(7) NOT NULL DEFAULT '#000000',
  `color_2_link` varchar(7) NOT NULL DEFAULT '#000099',
  `color_3_bg` varchar(7) NOT NULL DEFAULT '#ffffff',
  `color_3_text` varchar(7) NOT NULL DEFAULT '#000000',
  `color_3_link` varchar(7) NOT NULL DEFAULT '#000099',
  `color_4_bg` varchar(7) NOT NULL DEFAULT '#000000',
  `color_4_text` varchar(7) NOT NULL DEFAULT '#000000',
  `color_4_link` varchar(7) NOT NULL DEFAULT '#000099',
  `color_5_bg` varchar(7) NOT NULL DEFAULT '#ffffff',
  `color_5_text` varchar(7) NOT NULL DEFAULT '#000000',
  `color_5_link` varchar(7) NOT NULL DEFAULT '#000099',
  `color_6_bg` varchar(7) NOT NULL DEFAULT '#ffffff',
  `color_6_text` varchar(7) NOT NULL DEFAULT '#000000',
  `color_6_link` varchar(7) NOT NULL DEFAULT '#000099',
  `google_analytics` varchar(20) NOT NULL DEFAULT '',
  `meta_google_verify` varchar(255) NOT NULL DEFAULT '',
  `meta_bing_verify` varchar(255) NOT NULL DEFAULT '',
  `canonical_allowed` enum('Y','N') NOT NULL DEFAULT 'Y',
  `external_url` varchar(255) NOT NULL DEFAULT '',
  `external_title` varchar(255) NOT NULL DEFAULT '',
  `external_url_2` varchar(255) NOT NULL DEFAULT '',
  `external_title_2` varchar(255) NOT NULL DEFAULT '',
  `external_url_3` varchar(255) NOT NULL DEFAULT '',
  `external_title_3` varchar(255) NOT NULL DEFAULT '',
  `feature_models_studio_code` char(12) NOT NULL DEFAULT '',
  `studio_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `recruiter_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `referral_id_type` enum('studio','recruiter') NOT NULL DEFAULT 'studio',
  `authorized_domain_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `whois_last_verified` date NOT NULL DEFAULT '0000-00-00',
  `whois_type` varchar(32) NOT NULL DEFAULT 'pending',
  `recent_traffic` enum('Y','N') NOT NULL DEFAULT 'N',
  `spam_blacklist` enum('Y','N') NOT NULL DEFAULT 'N',
  `show_dating` enum('Y','N') NOT NULL DEFAULT 'Y',
  `status` enum('pending','pending_close','active','inactive','closed') NOT NULL DEFAULT 'active',
  `ssl_status` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_house` enum('Y','N') NOT NULL DEFAULT 'N',
  `cert_processed` tinyint(1) NOT NULL DEFAULT 0,
  `merchant` varchar(16) NOT NULL DEFAULT 'vsm',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `domain_review` varchar(32) NOT NULL DEFAULT 'reviewed',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_domain` (`domain`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `whois_type` (`whois_type`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=15719 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Bank_Verification`
--

DROP TABLE IF EXISTS `White_Label_Bank_Verification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Bank_Verification` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  `bank_id` char(20) NOT NULL DEFAULT '',
  `status` varchar(32) NOT NULL DEFAULT '',
  `date_reported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_approved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`domain`,`bank_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Broadcasters`
--

DROP TABLE IF EXISTS `White_Label_Broadcasters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Broadcasters` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`domain`,`broadcaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Categories`
--

DROP TABLE IF EXISTS `White_Label_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Categories` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  `category_id` int(10) NOT NULL DEFAULT 0,
  `rule` enum('feature','exclude') NOT NULL DEFAULT 'feature',
  PRIMARY KEY (`domain`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Excluded_Models`
--

DROP TABLE IF EXISTS `White_Label_Excluded_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Excluded_Models` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `model_id` int(11) NOT NULL DEFAULT 0,
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Files`
--

DROP TABLE IF EXISTS `White_Label_Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Files` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `upload_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `file_name` varchar(50) NOT NULL DEFAULT '',
  `file_size` int(10) unsigned NOT NULL DEFAULT 0,
  `file_mime_type` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=839 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Metadata`
--

DROP TABLE IF EXISTS `White_Label_Metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Metadata` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(20) NOT NULL DEFAULT '',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_desc` varchar(255) NOT NULL DEFAULT '',
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_desc` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`domain`,`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Models`
--

DROP TABLE IF EXISTS `White_Label_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Models` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  `model_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`domain`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Settings`
--

DROP TABLE IF EXISTS `White_Label_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Settings` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  `setting_key` varchar(40) NOT NULL DEFAULT '',
  `setting_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`domain`,`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `White_Label_Studios`
--

DROP TABLE IF EXISTS `White_Label_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `White_Label_Studios` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  `studio` char(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`domain`,`studio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Work`
--

DROP TABLE IF EXISTS `Work`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Work` (
  `id` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime DEFAULT NULL,
  `work_type_id` smallint(4) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `ref_id` int(9) unsigned DEFAULT NULL,
  `open_comments` varchar(140) DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `close_comments` varchar(140) DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `exclude_admin_ids` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ref_id` (`ref_id`),
  KEY `work_search` (`work_type_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3749838 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Work_Log`
--

DROP TABLE IF EXISTS `Work_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Work_Log` (
  `id` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `work_id` mediumint(7) unsigned DEFAULT NULL,
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `new_status` char(1) DEFAULT NULL,
  `comments` varchar(140) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8555309 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Work_Status`
--

DROP TABLE IF EXISTS `Work_Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Work_Status` (
  `code` char(1) DEFAULT NULL,
  `base_code` char(1) DEFAULT NULL,
  `status` varchar(35) DEFAULT NULL,
  `description` varchar(40) DEFAULT NULL,
  `completed` char(1) DEFAULT 'N',
  `in_progress` char(1) DEFAULT NULL,
  `user_selectable` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Work_Types`
--

DROP TABLE IF EXISTS `Work_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Work_Types` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `status` char(1) DEFAULT NULL,
  `priority` char(1) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `load_limit` tinyint(2) unsigned DEFAULT NULL,
  `alarm_min` smallint(4) unsigned DEFAULT NULL,
  `alarm_max` smallint(4) unsigned DEFAULT NULL,
  `notification_type_id` smallint(5) unsigned DEFAULT NULL,
  `expiration` smallint(4) unsigned DEFAULT NULL,
  `reopen_block` smallint(4) unsigned DEFAULT NULL,
  `admin_group_id` tinyint(3) unsigned DEFAULT NULL,
  `escalated_work_types_id` smallint(4) unsigned DEFAULT NULL,
  `admin_url` varchar(150) DEFAULT NULL,
  `directions` varchar(140) DEFAULT NULL,
  `work_query` text DEFAULT NULL,
  `completed_query` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Work_Types_Titles`
--

DROP TABLE IF EXISTS `Work_Types_Titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Work_Types_Titles` (
  `field` varchar(25) NOT NULL,
  `title` varchar(140) DEFAULT NULL,
  PRIMARY KEY (`field`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `playboy`
--

DROP TABLE IF EXISTS `playboy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `playboy` (
  `Transaction Date` datetime DEFAULT NULL,
  `Product` varchar(6) DEFAULT NULL,
  `Amount` float(9,2) DEFAULT NULL,
  `Username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `First Name` varchar(50) DEFAULT NULL,
  `Last Name` varchar(50) DEFAULT NULL,
  `Address` varchar(80) DEFAULT NULL,
  `City` varchar(50) DEFAULT NULL,
  `State` varchar(32) DEFAULT NULL,
  `Zip` varchar(15) DEFAULT NULL,
  `Country` varchar(50) DEFAULT NULL,
  `VS Code` varchar(5) DEFAULT NULL,
  `Tracking Code` varchar(50) DEFAULT NULL,
  `Date Created` datetime DEFAULT NULL,
  `Original Site` varchar(20) DEFAULT NULL,
  `Purchase Site` varchar(20) DEFAULT NULL,
  `Lifetime Spent` float(10,2) DEFAULT NULL,
  KEY `Username` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ttt`
--

DROP TABLE IF EXISTS `ttt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ttt` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(40) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `salt` varchar(40) NOT NULL,
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(80) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `zip` varchar(15) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `cc_num` varchar(255) NOT NULL DEFAULT '',
  `cc_exp_mo` varchar(255) NOT NULL DEFAULT '',
  `cc_exp_yr` varchar(255) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `timeleft` mediumint(7) NOT NULL DEFAULT 0,
  `timeleft_banked` mediumint(7) NOT NULL,
  `history` text DEFAULT NULL,
  `changes` varchar(10) NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `index_page` tinyint(4) NOT NULL DEFAULT 0,
  `subscription_product_code` varchar(6) NOT NULL DEFAULT '',
  `cancel` enum('Y','N') NOT NULL DEFAULT 'Y',
  `total_spent` float(10,2) NOT NULL DEFAULT 0.00,
  `generated` enum('N','Y') NOT NULL DEFAULT 'N',
  `subpw` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `bill_next` float(13,3) NOT NULL DEFAULT 0.000,
  `enc_cc_num` tinyblob NOT NULL,
  `enc_cc_exp_mo` tinyblob NOT NULL,
  `enc_cc_exp_yr` tinyblob NOT NULL,
  `account_type` varchar(20) NOT NULL DEFAULT '',
  `enc_card_suffix` tinyblob NOT NULL,
  `BIN` varchar(9) NOT NULL DEFAULT '',
  `paypal_id` varchar(255) NOT NULL DEFAULT '',
  `paycom_id` varchar(255) NOT NULL DEFAULT '',
  `paid_tokens_balance` smallint(5) NOT NULL DEFAULT 0,
  `free_tokens_balance` smallint(5) NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `enc_DDA` tinyblob NOT NULL,
  `TRN` varchar(9) NOT NULL DEFAULT '',
  `entity_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `dhd_cust_id` int(9) unsigned NOT NULL DEFAULT 0,
  `vs_vip` char(1) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `source_site_id` smallint(4) unsigned DEFAULT 1,
  `bounty_paid` char(1) NOT NULL DEFAULT 'N',
  `bounty_amount` float(6,2) NOT NULL,
  `date_last_login` date NOT NULL,
  `spending_group` varchar(25) NOT NULL,
  `spending_group_date` date NOT NULL,
  `service_counter` int(8) NOT NULL,
  `girls_percent` float(4,3) NOT NULL,
  `guys_percent` float(4,3) NOT NULL,
  `trans_percent` float(4,3) NOT NULL,
  `credits_per_dollar` float(5,3) DEFAULT 10.000,
  `play_and_pay_limit` float(6,2) NOT NULL DEFAULT 0.00,
  `play_and_pay_user_limit` float(6,2) NOT NULL DEFAULT 0.00
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

-- Dump completed on 2025-10-24 17:49:49
