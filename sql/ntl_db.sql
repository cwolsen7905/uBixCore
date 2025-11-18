/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: ntl_db
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
-- Table structure for table `2257_info`
--

DROP TABLE IF EXISTS `2257_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2257_info` (
  `contact_info` tinytext NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Activity_Velocity_Log`
--

DROP TABLE IF EXISTS `Activity_Velocity_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Activity_Velocity_Log` (
  `ip_address_long` int(11) unsigned NOT NULL DEFAULT 0,
  `activity` varchar(30) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `ip_search` (`ip_address_long`,`activity`),
  KEY `purge_key` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AdminGroupAccess`
--

DROP TABLE IF EXISTS `AdminGroupAccess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AdminGroupAccess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) DEFAULT NULL,
  `usergroup` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AdminLinks`
--

DROP TABLE IF EXISTS `AdminLinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AdminLinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linkname` varchar(160) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AdminSections`
--

DROP TABLE IF EXISTS `AdminSections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AdminSections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sectionname` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AdminUsers`
--

DROP TABLE IF EXISTS `AdminUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AdminUsers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(160) DEFAULT NULL,
  `usergroup` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Age_Verification`
--

DROP TABLE IF EXISTS `Age_Verification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Age_Verification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `country` char(2) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `date_verified` datetime DEFAULT NULL,
  `method` varchar(32) NOT NULL,
  `avs_regulation` varchar(32) NOT NULL,
  `verification_token` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`method`,`avs_regulation`),
  KEY `verification_token` (`verification_token`)
) ENGINE=InnoDB AUTO_INCREMENT=332205 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Age_Verification_Allowed_Methods`
--

DROP TABLE IF EXISTS `Age_Verification_Allowed_Methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Age_Verification_Allowed_Methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mandate_id` int(11) NOT NULL,
  `method_id` int(11) DEFAULT NULL,
  `white_label_id` int(11) DEFAULT 0,
  `action` enum('allow','disallow') NOT NULL DEFAULT 'allow',
  `is_active` enum('Y','N') NOT NULL DEFAULT 'N',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_allowed_method` (`mandate_id`,`method_id`,`white_label_id`)
) ENGINE=InnoDB AUTO_INCREMENT=364 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Age_Verification_Blocked_White_Labels`
--

DROP TABLE IF EXISTS `Age_Verification_Blocked_White_Labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Age_Verification_Blocked_White_Labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mandate_id` int(11) NOT NULL,
  `white_label_id` int(11) DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'N',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_blocked_white_label` (`mandate_id`,`white_label_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Age_Verification_Mandates`
--

DROP TABLE IF EXISTS `Age_Verification_Mandates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Age_Verification_Mandates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` char(2) NOT NULL,
  `state` char(3) NOT NULL,
  `avs_regulation` varchar(32) NOT NULL COMMENT 'This field matches Age_Verification field. This field is mainly for notes. Example: UK 2019',
  `activation_datetime` datetime DEFAULT NULL,
  `card_datetime` datetime DEFAULT NULL,
  `restriction_response` enum('blur','block') NOT NULL DEFAULT 'blur',
  `is_active` enum('Y','N') NOT NULL DEFAULT 'N',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_mandate` (`country`,`state`,`activation_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Age_Verification_Methods`
--

DROP TABLE IF EXISTS `Age_Verification_Methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Age_Verification_Methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Anniv_Party_2007`
--

DROP TABLE IF EXISTS `Anniv_Party_2007`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Anniv_Party_2007` (
  `guest_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `company` varchar(255) NOT NULL DEFAULT '',
  `address_1` varchar(255) NOT NULL DEFAULT '',
  `address_2` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `zip` varchar(50) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `num_guests` tinyint(1) NOT NULL DEFAULT 1,
  `confirmed` enum('Y','N','P') NOT NULL DEFAULT 'P',
  `type` varchar(50) NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`guest_id`),
  UNIQUE KEY `name` (`name`),
  KEY `name_2` (`name`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Anniv_Party_2007_Join`
--

DROP TABLE IF EXISTS `Anniv_Party_2007_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Anniv_Party_2007_Join` (
  `guest_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  KEY `guest_id` (`guest_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CCPA_Requests`
--

DROP TABLE IF EXISTS `CCPA_Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CCPA_Requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `sitekey` varchar(255) NOT NULL DEFAULT '',
  `request_type` enum('remove','do not sell') DEFAULT NULL,
  `request_details` text DEFAULT NULL,
  `pending` tinyint(4) NOT NULL DEFAULT 1,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `date_added` (`date_added`),
  KEY `pending` (`pending`),
  KEY `request_type` (`request_type`)
) ENGINE=InnoDB AUTO_INCREMENT=55109 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GDPR`
--

DROP TABLE IF EXISTS `GDPR`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GDPR` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `sitekey` varchar(255) NOT NULL DEFAULT '',
  `pending` tinyint(4) NOT NULL DEFAULT 1,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `date_added` (`date_added`),
  KEY `pending` (`pending`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GDPR_Purge_Users`
--

DROP TABLE IF EXISTS `GDPR_Purge_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `GDPR_Purge_Users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `optiusers_id` int(10) unsigned NOT NULL,
  `datetime_requested` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `purge_version` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime_purged` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `optiusers_id` (`optiusers_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1730 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Password_Reset`
--

DROP TABLE IF EXISTS `Password_Reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Password_Reset` (
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `reset_key` varchar(64) NOT NULL DEFAULT '',
  `status` enum('pending','complete','failed') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`,`reset_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Reengagement_Revenue`
--

DROP TABLE IF EXISTS `Reengagement_Revenue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Reengagement_Revenue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mp_code` varchar(6) NOT NULL DEFAULT '',
  `date` date NOT NULL,
  `revenue_same_mp` float(8,2) NOT NULL DEFAULT 0.00,
  `revenue_same_aff` float(8,2) NOT NULL DEFAULT 0.00,
  `revenue_diff_aff` float(8,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_code` (`mp_code`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=1919940 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Sitekey_Products`
--

DROP TABLE IF EXISTS `Sitekey_Products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Sitekey_Products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dhd_prod_id` int(11) NOT NULL DEFAULT 0,
  `sitekey` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
  `site_type` enum('All','F4F','WL','XVC','XVT') NOT NULL,
  `display_location` varchar(255) NOT NULL DEFAULT 'myAccount',
  `datetime_start` date NOT NULL DEFAULT '0000-00-00',
  `datetime_end` date NOT NULL DEFAULT '0000-00-00',
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `display_delay` int(11) NOT NULL DEFAULT 300,
  `device_type` enum('any','mobile','desktop') NOT NULL DEFAULT 'any',
  `survey_browser` enum('Y','N') NOT NULL DEFAULT 'N',
  `survey_os` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surveys_Browser`
--

DROP TABLE IF EXISTS `Surveys_Browser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surveys_Browser` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) unsigned NOT NULL DEFAULT 0,
  `browser_name` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Surveys_OS`
--

DROP TABLE IF EXISTS `Surveys_OS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Surveys_OS` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) unsigned NOT NULL DEFAULT 0,
  `OS_name` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=407 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `guest_id` varchar(32) NOT NULL DEFAULT '',
  `submission` text DEFAULT NULL,
  `datetime_submitted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `username` varchar(32) NOT NULL DEFAULT '',
  `total_spent` float(10,2) NOT NULL DEFAULT 0.00,
  `credits_left` mediumint(7) NOT NULL DEFAULT 0,
  `member_since` date NOT NULL DEFAULT '0000-00-00',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `http_host` varchar(255) NOT NULL DEFAULT '',
  `user_agent` text NOT NULL DEFAULT '',
  `device_info` varchar(255) NOT NULL DEFAULT '',
  `spending_group` varchar(24) NOT NULL DEFAULT '',
  `service` varchar(5) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=109582 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Two_Factor_Auth`
--

DROP TABLE IF EXISTS `Two_Factor_Auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Two_Factor_Auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `auth_method` enum('Google Authenticator','SMS') NOT NULL DEFAULT 'Google Authenticator',
  `label` varchar(32) NOT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `auth_value` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3178 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VWL_email`
--

DROP TABLE IF EXISTS `VWL_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VWL_email` (
  `email` varchar(255) NOT NULL DEFAULT '',
  `enc_cc_num` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `access_codes`
--

DROP TABLE IF EXISTS `access_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `access_codes` (
  `code` varchar(26) NOT NULL DEFAULT '',
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `access_codes_ver2`
--

DROP TABLE IF EXISTS `access_codes_ver2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `access_codes_ver2` (
  `code` varchar(100) NOT NULL DEFAULT '',
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expire` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ach_directory`
--

DROP TABLE IF EXISTS `ach_directory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ach_directory` (
  `TRN` int(9) unsigned NOT NULL DEFAULT 0,
  `office_code` char(1) DEFAULT NULL,
  `servicing_FRB` int(9) unsigned DEFAULT NULL,
  `record_type` char(1) DEFAULT NULL,
  `changed_date` int(6) unsigned DEFAULT NULL,
  `new_TRN` int(9) unsigned DEFAULT NULL,
  `customer_name` varchar(36) DEFAULT NULL,
  `address` varchar(36) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zipcode` int(5) DEFAULT NULL,
  `zipcode_ext` int(4) DEFAULT NULL,
  `area_code` int(3) DEFAULT NULL,
  `phone_prefix` int(3) DEFAULT NULL,
  `phone_suffix` int(4) DEFAULT NULL,
  `inst_status` char(1) DEFAULT NULL,
  `data_view` char(1) DEFAULT NULL,
  `filler` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`TRN`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `active_content`
--

DROP TABLE IF EXISTS `active_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `active_content` (
  `site_id` smallint(6) NOT NULL DEFAULT 0,
  `category_id` varchar(4) NOT NULL DEFAULT '0',
  `content_id` tinyint(4) NOT NULL DEFAULT 0,
  `order_by` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `link` varchar(255) DEFAULT NULL,
  `linkname` varchar(160) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_pass`
--

DROP TABLE IF EXISTS `admin_pass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_pass` (
  `name` varchar(255) DEFAULT NULL,
  `pass` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_sections`
--

DROP TABLE IF EXISTS `admin_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_sections` (
  `name` varchar(150) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `groupname` varchar(150) DEFAULT NULL,
  `sectionsid` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adult_auction_mailing`
--

DROP TABLE IF EXISTS `adult_auction_mailing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `adult_auction_mailing` (
  `email` varchar(60) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `affiliate_traffic`
--

DROP TABLE IF EXISTS `affiliate_traffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `affiliate_traffic` (
  `active` char(1) NOT NULL DEFAULT '',
  `site_link` varchar(255) NOT NULL DEFAULT '',
  `image_name` varchar(255) NOT NULL DEFAULT '',
  `theme` varchar(255) DEFAULT NULL,
  `bad_country` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `anonymous_email`
--

DROP TABLE IF EXISTS `anonymous_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `anonymous_email` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authenticate`
--

DROP TABLE IF EXISTS `authenticate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `authenticate` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `product` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `available_model_name`
--

DROP TABLE IF EXISTS `available_model_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `available_model_name` (
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avn_promo_traffic`
--

DROP TABLE IF EXISTS `avn_promo_traffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `avn_promo_traffic` (
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_count` int(11) DEFAULT NULL,
  `unique_count` int(11) DEFAULT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`date`,`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avoid_reason`
--

DROP TABLE IF EXISTS `avoid_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `avoid_reason` (
  `id` tinyint(2) unsigned NOT NULL,
  `description` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banner_name`
--

DROP TABLE IF EXISTS `banner_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banner_src`
--

DROP TABLE IF EXISTS `banner_src`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner_src` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `bannertype` enum('image','text') NOT NULL DEFAULT 'image',
  `width` int(11) NOT NULL DEFAULT 0,
  `height` int(11) NOT NULL DEFAULT 0,
  `source` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `site` varchar(100) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT 0,
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`site`,`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batch`
--

DROP TABLE IF EXISTS `batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `batch` (
  `batch_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `zip` varchar(15) DEFAULT NULL,
  `prod_id` varchar(9) DEFAULT NULL,
  `minutes` int(2) DEFAULT NULL,
  `buylink` varchar(255) NOT NULL DEFAULT '',
  `transact_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`batch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bill_session`
--

DROP TABLE IF EXISTS `bill_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bill_session` (
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_key` varchar(50) NOT NULL DEFAULT '',
  `product_id` varchar(10) DEFAULT NULL,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `username_taken` char(1) DEFAULT NULL,
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(32) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_bl` char(1) DEFAULT NULL,
  `cc_num` varchar(38) DEFAULT NULL,
  `cc_num_bl` char(1) DEFAULT NULL,
  `cc_exp_yr` varchar(4) DEFAULT NULL,
  `cc_exp_mo` char(2) DEFAULT NULL,
  `TRN` varchar(9) DEFAULT NULL,
  `DDA` varchar(17) DEFAULT NULL,
  `TRN_DDA_bl` char(1) DEFAULT NULL,
  `account_type` char(1) DEFAULT NULL,
  `product_code` varchar(10) DEFAULT NULL,
  `version` tinyint(4) DEFAULT NULL,
  `model_name` varchar(100) DEFAULT NULL,
  `service_number` tinyint(4) DEFAULT NULL,
  `freeporn` varchar(5) DEFAULT NULL,
  `live` varchar(5) DEFAULT NULL,
  `terms` varchar(5) DEFAULT NULL,
  `language` tinyint(1) DEFAULT NULL,
  `submit` varchar(6) DEFAULT NULL,
  `http_referer` varchar(255) DEFAULT NULL,
  `http_user_agent` varchar(255) DEFAULT NULL,
  `remote_addr` varchar(20) DEFAULT NULL,
  `velocity` smallint(5) DEFAULT NULL,
  `status_code` tinyint(1) DEFAULT NULL,
  `spending_limit` float(8,2) DEFAULT NULL,
  `fix_code` smallint(4) DEFAULT NULL,
  `preferred_cc` char(1) DEFAULT NULL,
  `preferred_email` char(1) DEFAULT NULL,
  `preferred_TRN_DDA` char(1) DEFAULT NULL,
  `virtual_white_listed` char(1) DEFAULT NULL,
  `tried` varchar(255) DEFAULT NULL,
  `score` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`session_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bin_blacklist`
--

DROP TABLE IF EXISTS `bin_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bin_blacklist` (
  `bin` varchar(38) NOT NULL DEFAULT '',
  PRIMARY KEY (`bin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `block_code`
--

DROP TABLE IF EXISTS `block_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `block_code` (
  `block_code` tinyint(4) NOT NULL DEFAULT 0,
  `reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`block_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bonus`
--

DROP TABLE IF EXISTS `bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bonus` (
  `email` varchar(255) NOT NULL DEFAULT '',
  `exp_date` date NOT NULL DEFAULT '0000-00-00',
  `bonus` float(24,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bounty_paid`
--

DROP TABLE IF EXISTS `bounty_paid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bounty_paid` (
  `code` char(1) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` tinytext DEFAULT NULL,
  `keyword` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chargeback`
--

DROP TABLE IF EXISTS `chargeback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chargeback` (
  `chargeback_id` int(11) NOT NULL AUTO_INCREMENT,
  `chargeback_date_in` int(10) NOT NULL,
  `chargeback_date_out` int(10) NOT NULL,
  `chargeback_name` varchar(100) DEFAULT NULL,
  `chargeback_phone` varchar(32) DEFAULT NULL,
  `chargeback_email` varchar(255) DEFAULT NULL,
  `chargeback_manager` smallint(4) unsigned NOT NULL DEFAULT 0,
  `chargeback_comments` text DEFAULT NULL,
  `chargeback_status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `chargeback_cc_num` varchar(255) NOT NULL DEFAULT '',
  `enc_chargeback_cc_num` tinyblob NOT NULL,
  `enc_DDA` tinyblob NOT NULL,
  `TRN` varchar(255) NOT NULL DEFAULT '',
  `account_type` varchar(20) NOT NULL DEFAULT '',
  `amount` float(5,2) NOT NULL DEFAULT 0.00,
  `avoid_user_id` smallint(3) unsigned DEFAULT NULL,
  `avoid_reason_id` tinyint(2) DEFAULT NULL,
  `date_chargeback` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `amount_returned` float(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`chargeback_id`),
  KEY `chargeback_manager` (`chargeback_manager`),
  KEY `chargeback_cc_num` (`chargeback_cc_num`),
  KEY `chargeback_status` (`chargeback_status`),
  KEY `enc_chargeback_cc_num` (`enc_chargeback_cc_num`(255)),
  KEY `enc_DDA` (`enc_DDA`(255),`TRN`(9)),
  KEY `chargeback_email` (`chargeback_email`),
  KEY `chargeback_date_in` (`chargeback_date_in`),
  KEY `date_chargeback` (`date_chargeback`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=1470278 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chargeback_change_log`
--

DROP TABLE IF EXISTS `chargeback_change_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chargeback_change_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chargeback_id` int(11) NOT NULL DEFAULT 0,
  `prev_credit_type` tinyint(4) NOT NULL DEFAULT 0,
  `new_credit_type` tinyint(4) NOT NULL DEFAULT 0,
  `datetime` datetime DEFAULT NULL,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `prev_date_chargeback` date DEFAULT NULL,
  `new_date_chargeback` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62435 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chargeback_manager`
--

DROP TABLE IF EXISTS `chargeback_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chargeback_manager` (
  `manager_id` int(11) NOT NULL AUTO_INCREMENT,
  `manager_name` varchar(30) DEFAULT NULL,
  `manager_email` varchar(35) DEFAULT NULL,
  `manager_icq` varchar(20) DEFAULT NULL,
  `manager_status` int(11) DEFAULT NULL,
  `manager_username` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`manager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classic_pages`
--

DROP TABLE IF EXISTS `classic_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `classic_pages` (
  `room` varchar(30) NOT NULL DEFAULT '',
  `multiview` enum('N','Y') NOT NULL DEFAULT 'N',
  `reload` enum('N','Y') NOT NULL DEFAULT 'N',
  `callmodel` enum('N','Y') NOT NULL DEFAULT 'N',
  `tip` enum('N','Y') NOT NULL DEFAULT 'N',
  `help` enum('N','Y') NOT NULL DEFAULT 'N',
  `videosize` enum('N','Y') NOT NULL DEFAULT 'N',
  `html_template` varchar(30) NOT NULL DEFAULT '',
  `credit` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`room`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collection`
--

DROP TABLE IF EXISTS `collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collection` (
  `chargeback_id` int(11) NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` char(2) NOT NULL DEFAULT '',
  `user` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `collection_agent_id` tinyint(2) NOT NULL DEFAULT 0,
  `collected` float(24,2) DEFAULT NULL,
  `paid` float(24,2) DEFAULT NULL,
  PRIMARY KEY (`chargeback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collection_agent`
--

DROP TABLE IF EXISTS `collection_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collection_agent` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(70) DEFAULT NULL,
  `contact_name` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consumer_product`
--

DROP TABLE IF EXISTS `consumer_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumer_product` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`,`product_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consumer_product_type`
--

DROP TABLE IF EXISTS `consumer_product_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumer_product_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL DEFAULT '',
  `consumer_product_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`product_name`,`consumer_product_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `content` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `contentname` varchar(50) DEFAULT NULL,
  `description` tinytext DEFAULT NULL,
  `category` tinyint(4) NOT NULL DEFAULT 0,
  `url` tinytext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_indexing`
--

DROP TABLE IF EXISTS `content_indexing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_indexing` (
  `site_id` varchar(100) DEFAULT NULL,
  `sections_id` varchar(50) DEFAULT NULL,
  `groups` varchar(100) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_tracking`
--

DROP TABLE IF EXISTS `content_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product` varchar(100) DEFAULT NULL,
  `type` enum('video','text','gallery') DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `format` varchar(100) DEFAULT NULL,
  `quantity` varchar(20) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `copyright`
--

DROP TABLE IF EXISTS `copyright`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `copyright` (
  `mp_code` varchar(4) NOT NULL DEFAULT '',
  `copyright` enum('N','Y') NOT NULL DEFAULT 'N',
  `email_harvest` enum('checked','unchecked','off') NOT NULL DEFAULT 'checked',
  KEY `mp_code` (`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credit_log`
--

DROP TABLE IF EXISTS `credit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_log` (
  `credit_id` int(11) NOT NULL AUTO_INCREMENT,
  `credit_date` varchar(25) DEFAULT NULL,
  `credit_cc_num` varchar(16) NOT NULL DEFAULT '',
  `credit_amount` float(5,2) NOT NULL DEFAULT 0.00,
  `credit_action` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`credit_id`),
  KEY `credit_action` (`credit_action`),
  KEY `credit_cc_num` (`credit_cc_num`),
  KEY `credit_amount` (`credit_amount`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credit_type`
--

DROP TABLE IF EXISTS `credit_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_type` (
  `id` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `short_description` varchar(12) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crum_counter`
--

DROP TABLE IF EXISTS `crum_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `crum_counter` (
  `site_name` varchar(255) NOT NULL DEFAULT '',
  `hits` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cs_transact`
--

DROP TABLE IF EXISTS `cs_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cs_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `ics_rcode` char(2) DEFAULT NULL,
  `ics_rflag` varchar(14) DEFAULT NULL,
  `auth_cv_result` char(2) DEFAULT NULL,
  `bill_trans_ref_no` int(11) DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `csza_description`
--

DROP TABLE IF EXISTS `csza_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `csza_description` (
  `match_code` varchar(12) NOT NULL DEFAULT '',
  `description` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`match_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `current_charges`
--

DROP TABLE IF EXISTS `current_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `current_charges` (
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `product_id` int(11) NOT NULL DEFAULT 0,
  `amount` float NOT NULL DEFAULT 0,
  UNIQUE KEY `mp_code` (`mp_code`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `current_f4f_bonus`
--

DROP TABLE IF EXISTS `current_f4f_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `current_f4f_bonus` (
  `service` char(11) NOT NULL DEFAULT '',
  `bonus` float(24,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_support`
--

DROP TABLE IF EXISTS `customer_support`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_support` (
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `cc_num` varchar(255) DEFAULT NULL,
  `trn` varchar(255) DEFAULT NULL,
  `dda` varchar(255) DEFAULT NULL,
  `trans_num` varchar(255) DEFAULT NULL,
  `resolution_comments` text DEFAULT NULL,
  `resolution_date` varchar(255) DEFAULT NULL,
  `remote_ip` varchar(255) DEFAULT NULL,
  `cb_manager_id` varchar(255) DEFAULT NULL,
  `charge_date` varchar(20) DEFAULT NULL,
  `charge_amount` varchar(20) DEFAULT NULL,
  `help_options` varchar(20) DEFAULT NULL,
  `request_date` varchar(20) DEFAULT NULL,
  `customer_comments` text DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `cvv2` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `old_ticket_number` varchar(255) DEFAULT NULL,
  `ticket_number` varchar(10) NOT NULL DEFAULT '',
  `zipcode` varchar(255) DEFAULT NULL,
  `manager_name` varchar(255) DEFAULT NULL,
  `enc_cc_num` tinyblob NOT NULL,
  `enc_dda` tinyblob NOT NULL,
  `enc_cvv2` tinyblob NOT NULL,
  PRIMARY KEY (`ticket_number`),
  KEY `request_date` (`request_date`),
  KEY `resolution_date` (`resolution_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_gallery_config_options`
--

DROP TABLE IF EXISTS `daily_gallery_config_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_gallery_config_options` (
  `site_id` int(10) unsigned zerofill NOT NULL DEFAULT 0000000000,
  `upsell` enum('1','0') NOT NULL DEFAULT '1',
  `email_harvest` enum('1','0') NOT NULL DEFAULT '1',
  `image_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `db_conn_info`
--

DROP TABLE IF EXISTS `db_conn_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `db_conn_info` (
  `digest_str` varchar(16) NOT NULL DEFAULT '',
  `server_str` varchar(255) DEFAULT NULL,
  `system_script_str` varchar(255) DEFAULT NULL,
  `cwd_str` varchar(255) DEFAULT NULL,
  `script_str` varchar(255) DEFAULT NULL,
  `host_str` varchar(255) DEFAULT NULL,
  `database_str` varchar(255) DEFAULT NULL,
  `username_str` varchar(16) DEFAULT NULL,
  `password_str` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`digest_str`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `db_data_dictionary`
--

DROP TABLE IF EXISTS `db_data_dictionary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `db_data_dictionary` (
  `database_name` varchar(100) NOT NULL DEFAULT '',
  `table_name` varchar(100) NOT NULL DEFAULT '',
  `column_name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`database_name`,`table_name`,`column_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dbs`
--

DROP TABLE IF EXISTS `dbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dbs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dhd_product_code`
--

DROP TABLE IF EXISTS `dhd_product_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dhd_product_code` (
  `product_code` varchar(6) NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `minutes_days` int(11) NOT NULL DEFAULT 0,
  `amount` float(5,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`product_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `display`
--

DROP TABLE IF EXISTS `display`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `display` (
  `keyword` varchar(15) NOT NULL DEFAULT '',
  `imagenumber` tinyint(4) NOT NULL DEFAULT 1,
  `display` int(7) NOT NULL DEFAULT 0,
  `displayhour` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `repeatdisplay` int(7) NOT NULL DEFAULT 0,
  PRIMARY KEY (`keyword`,`imagenumber`,`displayhour`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `divine_content`
--

DROP TABLE IF EXISTS `divine_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `divine_content` (
  `id` int(11) NOT NULL DEFAULT 0,
  `contentname` varchar(60) DEFAULT NULL,
  `description` tinytext DEFAULT NULL,
  `category` tinyint(4) DEFAULT NULL,
  `url` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dms_domain`
--

DROP TABLE IF EXISTS `dms_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dms_domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `registrar_account_id` int(10) unsigned NOT NULL DEFAULT 0,
  `create_date` date NOT NULL DEFAULT '0000-00-00',
  `expire_date` date NOT NULL DEFAULT '0000-00-00',
  `registrant_info` text DEFAULT NULL,
  `admin_contact_info` text DEFAULT NULL,
  `billing_contact_info` text DEFAULT NULL,
  `technical_contact_info` text DEFAULT NULL,
  `name_server_info` varchar(255) DEFAULT NULL,
  `parse_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `registrar` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dms_registrar`
--

DROP TABLE IF EXISTS `dms_registrar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dms_registrar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dms_registrar_account`
--

DROP TABLE IF EXISTS `dms_registrar_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dms_registrar_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `registrar_id` int(10) unsigned NOT NULL DEFAULT 0,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`registrar_id`,`username`,`password`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dti_transact`
--

DROP TABLE IF EXISTS `dti_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dti_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `transaction_id` int(6) DEFAULT NULL,
  `error_code` int(3) DEFAULT NULL,
  `customer_id` int(6) DEFAULT NULL,
  `result` char(25) DEFAULT NULL,
  `subs_id` int(6) DEFAULT NULL,
  `aff_id` int(8) DEFAULT NULL,
  `avs_code` char(2) DEFAULT NULL,
  `return_code` char(6) DEFAULT NULL,
  `site_id` int(5) NOT NULL DEFAULT 592,
  `price_id` int(5) NOT NULL DEFAULT 0,
  `status` char(10) DEFAULT NULL,
  `type` char(20) DEFAULT NULL,
  `date` char(20) DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `e_transact`
--

DROP TABLE IF EXISTS `e_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `e_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `CNAME` varchar(20) DEFAULT NULL,
  `Amount` float(20,2) DEFAULT NULL,
  `clTransDate` varchar(8) DEFAULT NULL,
  `AVSCode` char(3) DEFAULT NULL,
  `RText` varchar(30) DEFAULT NULL,
  `SeqNum` varchar(10) DEFAULT NULL,
  `HubAcct` varchar(10) DEFAULT NULL,
  `ResultCode` char(3) DEFAULT NULL,
  `Issuer` varchar(10) DEFAULT NULL,
  `clRefNum` varchar(10) NOT NULL DEFAULT '',
  `CardType` char(2) DEFAULT NULL,
  `HubID` varchar(10) DEFAULT NULL,
  `AuthCode` varchar(6) DEFAULT NULL,
  `AVSText` varchar(30) DEFAULT NULL,
  `BatchID` varchar(10) DEFAULT NULL,
  `ItemNum` varchar(10) DEFAULT NULL,
  `CVV2Resp` char(1) DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ec_transact`
--

DROP TABLE IF EXISTS `ec_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ec_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `transaction_id` int(11) NOT NULL DEFAULT 0,
  `source_ip` varchar(16) NOT NULL DEFAULT '',
  `product` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `command` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_addresses_company`
--

DROP TABLE IF EXISTS `email_addresses_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_addresses_company` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `type` enum('account','alias') NOT NULL DEFAULT 'account',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_aliases_company`
--

DROP TABLE IF EXISTS `email_aliases_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_aliases_company` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `entry` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`,`entry`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_blacklist`
--

DROP TABLE IF EXISTS `email_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_blacklist` (
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_harvest`
--

DROP TABLE IF EXISTS `email_harvest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_harvest` (
  `email_address` varchar(255) NOT NULL DEFAULT '',
  `harvest_code` int(10) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`email_address`,`harvest_code`),
  KEY `email_address` (`email_address`),
  KEY `harvest_code` (`harvest_code`),
  KEY `mp_code` (`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_promotion`
--

DROP TABLE IF EXISTS `email_promotion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_promotion` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `segment_query` text DEFAULT NULL,
  `segment_description` varchar(255) NOT NULL DEFAULT '',
  `benefit_code` varchar(255) NOT NULL DEFAULT '',
  `benefit_description` varchar(255) NOT NULL DEFAULT '',
  `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `finish_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `re_uses_int` smallint(4) unsigned NOT NULL DEFAULT 0,
  `message_id` int(10) unsigned DEFAULT NULL,
  `sent_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `emails_sent_int` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_promotion_code`
--

DROP TABLE IF EXISTS `email_promotion_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_promotion_code` (
  `service_number` tinyint(2) unsigned NOT NULL DEFAULT 20,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `promotion_code` varchar(6) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `theme` varchar(30) NOT NULL DEFAULT '',
  `mp_code` varchar(4) NOT NULL DEFAULT '',
  `email_promotion_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `email_read_int` smallint(4) unsigned NOT NULL DEFAULT 0,
  `page_viewed_int` smallint(4) unsigned NOT NULL DEFAULT 0,
  `used_int` smallint(4) unsigned NOT NULL DEFAULT 0,
  `benefited_int` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`service_number`,`username`,`promotion_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_service`
--

DROP TABLE IF EXISTS `email_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_service` (
  `email` varchar(50) NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 1,
  `contact` enum('Y','N') NOT NULL DEFAULT 'Y',
  UNIQUE KEY `email` (`email`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_commissions`
--

DROP TABLE IF EXISTS `employee_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_commissions` (
  `employee_id` smallint(6) NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`employee_id`,`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entity`
--

DROP TABLE IF EXISTS `entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `entity` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `mp_code` varchar(4) NOT NULL DEFAULT '',
  `given_name` varchar(100) NOT NULL DEFAULT '',
  `surname` varchar(100) NOT NULL DEFAULT '',
  `address` varchar(80) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `zip` varchar(15) NOT NULL DEFAULT '',
  `dob` date NOT NULL DEFAULT '0000-00-00',
  `ssn_last_4` smallint(4) unsigned DEFAULT NULL,
  `gender` enum('','M','F') NOT NULL DEFAULT '',
  `confirmed_email` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f4f_5_params`
--

DROP TABLE IF EXISTS `f4f_5_params`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `f4f_5_params` (
  `click_check` varchar(20) NOT NULL DEFAULT '',
  `customerid` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `payment_type` char(2) NOT NULL DEFAULT '',
  `paymentid` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `purchase_type` varchar(20) NOT NULL DEFAULT '',
  `purchase_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `session` varchar(50) NOT NULL DEFAULT '',
  `modelid` smallint(5) unsigned NOT NULL DEFAULT 0,
  `groupid` smallint(5) unsigned NOT NULL DEFAULT 0,
  `small` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`click_check`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f4f_ip_block`
--

DROP TABLE IF EXISTS `f4f_ip_block`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `f4f_ip_block` (
  `ip` varchar(30) NOT NULL DEFAULT '',
  UNIQUE KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f4f_private_log_ex`
--

DROP TABLE IF EXISTS `f4f_private_log_ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `f4f_private_log_ex` (
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `username` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_type` char(3) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  KEY `time` (`time`),
  KEY `username` (`username`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f4f_rank`
--

DROP TABLE IF EXISTS `f4f_rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `f4f_rank` (
  `model_name` varchar(50) NOT NULL DEFAULT '',
  `rank` int(11) DEFAULT NULL,
  `total_private` int(11) DEFAULT NULL,
  `service` varchar(50) DEFAULT NULL,
  UNIQUE KEY `model_name` (`model_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fapp_code`
--

DROP TABLE IF EXISTS `fapp_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fapp_code` (
  `code` char(3) NOT NULL DEFAULT '',
  `reason` varchar(51) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `favorites_id_translation`
--

DROP TABLE IF EXISTS `favorites_id_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites_id_translation` (
  `old_cookie_id` varchar(25) NOT NULL DEFAULT '',
  `new_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`old_cookie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `features`
--

DROP TABLE IF EXISTS `features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `features` (
  `feature1` varchar(50) DEFAULT NULL,
  `feature` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flirt_override`
--

DROP TABLE IF EXISTS `flirt_override`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `flirt_override` (
  `domain` varchar(100) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  `show_trans` enum('Y','N') NOT NULL DEFAULT 'N',
  `show_asians` enum('Y','N') NOT NULL DEFAULT 'Y',
  `show_dungeon` enum('Y','N') NOT NULL DEFAULT 'Y',
  `show_girls` enum('Y','N') NOT NULL DEFAULT 'Y',
  `show_guys` enum('Y','N') NOT NULL DEFAULT 'Y',
  `show_amateur` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`domain`,`path`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flirtusers`
--

DROP TABLE IF EXISTS `flirtusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `flirtusers` (
  `room` varchar(25) NOT NULL DEFAULT '',
  `username` varchar(25) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `free_hits`
--

DROP TABLE IF EXISTS `free_hits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `free_hits` (
  `day` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `page` varchar(150) NOT NULL DEFAULT '',
  `unique_count` int(11) DEFAULT NULL,
  `total_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`day`,`mp_code`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `free_time_log`
--

DROP TABLE IF EXISTS `free_time_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `free_time_log` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `seconds` int(11) NOT NULL DEFAULT 0,
  `entry_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `employee_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `reason_code` tinyint(4) NOT NULL DEFAULT 0,
  KEY `EntryDate_Service_Username` (`entry_date`,`service`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `free_time_reason_codes`
--

DROP TABLE IF EXISTS `free_time_reason_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `free_time_reason_codes` (
  `code` tinyint(4) NOT NULL DEFAULT 0,
  `descrip` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gettrx_support`
--

DROP TABLE IF EXISTS `gettrx_support`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gettrx_support` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `GETTRX_key` varchar(19) NOT NULL DEFAULT '',
  `credit_type` tinyint(1) DEFAULT NULL,
  `action` varchar(17) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gettrx_transact`
--

DROP TABLE IF EXISTS `gettrx_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gettrx_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `approved` char(2) NOT NULL DEFAULT '',
  `GETTRX_key` varchar(19) NOT NULL DEFAULT '',
  `AVS` char(2) DEFAULT NULL,
  `CVV2Match` char(2) DEFAULT NULL,
  `ZipMatch` int(1) DEFAULT NULL,
  `GETScore` float(24,2) DEFAULT NULL,
  `BatchID` smallint(5) unsigned DEFAULT NULL,
  `BatchAmount` float(24,2) DEFAULT NULL,
  `TransType` char(3) DEFAULT NULL,
  KEY `transact_id` (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `girls_switchover_data`
--

DROP TABLE IF EXISTS `girls_switchover_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `girls_switchover_data` (
  `email` varchar(100) NOT NULL DEFAULT '',
  `username_original` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `username_new` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `time_left` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `first_name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`email`,`username_original`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `glossary`
--

DROP TABLE IF EXISTS `glossary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `glossary` (
  `term` varchar(255) NOT NULL DEFAULT '',
  `def` tinytext NOT NULL,
  `cat` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`term`),
  KEY `cat` (`cat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_option`
--

DROP TABLE IF EXISTS `help_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_option` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_options`
--

DROP TABLE IF EXISTS `help_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `help_options` (
  `option_id` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hit_counter`
--

DROP TABLE IF EXISTS `hit_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hit_counter` (
  `url` varchar(255) NOT NULL DEFAULT '',
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `count` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `conversion` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `url_status` (`url`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hits`
--

DROP TABLE IF EXISTS `hits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hits` (
  `keyword` varchar(15) NOT NULL DEFAULT '',
  `imagenumber` tinyint(4) NOT NULL DEFAULT 1,
  `clickedhour` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clicked` int(7) NOT NULL DEFAULT 0,
  PRIMARY KEY (`keyword`,`imagenumber`,`clickedhour`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibill`
--

DROP TABLE IF EXISTS `ibill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibill` (
  `account` int(9) unsigned NOT NULL DEFAULT 0,
  `amount` float(24,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibill_pincode`
--

DROP TABLE IF EXISTS `ibill_pincode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ibill_pincode` (
  `pin` varchar(7) NOT NULL DEFAULT '',
  `account` int(9) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`pin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ig_site_name`
--

DROP TABLE IF EXISTS `ig_site_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ig_site_name` (
  `customer_number` varchar(20) NOT NULL DEFAULT '',
  `site_name` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`customer_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `igallery_hits`
--

DROP TABLE IF EXISTS `igallery_hits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `igallery_hits` (
  `day` datetime DEFAULT NULL,
  `total_count` int(11) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `include_files`
--

DROP TABLE IF EXISTS `include_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `include_files` (
  `include_file` varchar(200) NOT NULL DEFAULT '',
  `calling_file` varchar(200) NOT NULL DEFAULT '',
  `project` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`include_file`,`calling_file`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `index_pages`
--

DROP TABLE IF EXISTS `index_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `index_pages` (
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `page_num` tinyint(4) NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`service`,`page_num`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ip_1on1_hits`
--

DROP TABLE IF EXISTS `ip_1on1_hits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ip_1on1_hits` (
  `visitor_id` varchar(50) NOT NULL DEFAULT '',
  `day` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `script` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`visitor_id`,`day`),
  KEY `mp_code_day` (`mp_code`,`day`),
  KEY `mp_code_day_script` (`mp_code`,`day`,`script`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ip_blocklist`
--

DROP TABLE IF EXISTS `ip_blocklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ip_blocklist` (
  `ip_address` varbinary(39) NOT NULL,
  `expires` float(13,3) NOT NULL DEFAULT 0.000,
  `inserted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ip_address`),
  KEY `inserted_at` (`inserted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ip_employee`
--

DROP TABLE IF EXISTS `ip_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ip_employee` (
  `employee` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ip_history`
--

DROP TABLE IF EXISTS `ip_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ip_history` (
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `time` float(13,3) NOT NULL DEFAULT 0.000
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ip_studio`
--

DROP TABLE IF EXISTS `ip_studio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ip_studio` (
  `ip` varchar(15) NOT NULL DEFAULT '',
  `studio` char(12) NOT NULL DEFAULT '',
  UNIQUE KEY `class_c` (`ip`,`studio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ip_vsmedia`
--

DROP TABLE IF EXISTS `ip_vsmedia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ip_vsmedia` (
  `ip` varchar(15) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ipchains_deny`
--

DROP TABLE IF EXISTS `ipchains_deny`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ipchains_deny` (
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `launch`
--

DROP TABLE IF EXISTS `launch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `launch` (
  `product` varchar(20) NOT NULL DEFAULT '',
  `product_id` mediumint(8) unsigned zerofill NOT NULL DEFAULT 00000000,
  `console_type` varchar(50) NOT NULL DEFAULT '',
  `forward` varchar(100) NOT NULL DEFAULT '',
  `product_new_win` enum('Y','N') NOT NULL DEFAULT 'Y',
  `title` varchar(100) NOT NULL DEFAULT '',
  `right_click` enum('Y','N') NOT NULL DEFAULT 'Y',
  `click_message` varchar(100) NOT NULL DEFAULT '',
  `window_name` varchar(30) NOT NULL DEFAULT '',
  `ie_index_page` varchar(100) NOT NULL DEFAULT '',
  `net_index_page` varchar(100) NOT NULL DEFAULT '',
  `window_height` int(11) NOT NULL DEFAULT 0,
  `window_width` int(11) NOT NULL DEFAULT 0,
  `window_menubar` enum('1','0') NOT NULL DEFAULT '1',
  `window_toolbar` enum('1','0') NOT NULL DEFAULT '1',
  `window_scrollbars` enum('1','0') NOT NULL DEFAULT '1',
  `window_resizable` enum('1','0') NOT NULL DEFAULT '1',
  `window_copyhistory` enum('1','0') NOT NULL DEFAULT '1',
  `window_location` enum('1','0') NOT NULL DEFAULT '1',
  `window_status` enum('1','0') NOT NULL DEFAULT '1',
  `window_titlebar` enum('1','0') NOT NULL DEFAULT '1',
  `window_top` enum('1','0') NOT NULL DEFAULT '1',
  `window_left` enum('1','0') NOT NULL DEFAULT '1',
  `server_name1` varchar(100) NOT NULL DEFAULT '',
  `server_name2` varchar(100) NOT NULL DEFAULT '',
  `server_name3` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`product`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `room` varchar(50) NOT NULL DEFAULT '',
  `number` varchar(5) NOT NULL DEFAULT '',
  `model_name` varchar(50) NOT NULL DEFAULT '',
  `model_image_path` varchar(255) NOT NULL DEFAULT '',
  `statement` varchar(255) NOT NULL DEFAULT '',
  `status` char(3) NOT NULL DEFAULT 'Y',
  `studio` char(12) NOT NULL DEFAULT 'A',
  `user_timeleft` int(11) NOT NULL DEFAULT 0,
  `user_name` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `bad_chat_session` enum('N','Y') NOT NULL DEFAULT 'N',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`room`,`number`),
  KEY `room_status` (`room`,`status`),
  KEY `model_name` (`model_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mbot_search_results`
--

DROP TABLE IF EXISTS `mbot_search_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mbot_search_results` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_term` varchar(255) NOT NULL DEFAULT '',
  `rank_num` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `url` varchar(255) NOT NULL DEFAULT '',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `date` datetime DEFAULT NULL,
  `source` enum('google') NOT NULL DEFAULT 'google',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `merchant_bank`
--

DROP TABLE IF EXISTS `merchant_bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchant_bank` (
  `merchant_bank` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(20) NOT NULL DEFAULT '',
  `long_name` varchar(50) DEFAULT NULL,
  `dhd_name` varchar(5) DEFAULT NULL,
  `processor_name` varchar(20) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `processor_id` tinyint(3) unsigned NOT NULL,
  `descriptor_prefix` varchar(13) NOT NULL,
  `descriptor` varchar(28) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `running` char(1) NOT NULL,
  `ideal_percent` float(6,5) unsigned NOT NULL,
  `payment_type` varchar(64) NOT NULL DEFAULT '',
  `1099k_name` varchar(80) DEFAULT NULL,
  `max_amount` float(7,2) DEFAULT NULL,
  `tx_count` mediumint(6) unsigned DEFAULT NULL,
  `rg_index` tinyint(2) unsigned DEFAULT NULL,
  `bank` varchar(32) NOT NULL DEFAULT '',
  `bank_id` tinyint(3) NOT NULL DEFAULT 0,
  `high_risk_only` enum('Y','N') NOT NULL DEFAULT 'N',
  `network` varchar(50) NOT NULL DEFAULT '',
  `goods` enum('Y','N') DEFAULT 'N',
  `account_closed` enum('Y','N') NOT NULL DEFAULT 'N',
  `region` varchar(8) NOT NULL DEFAULT 'all',
  `verifi` tinyint(1) NOT NULL DEFAULT 0,
  `ethoca` tinyint(1) NOT NULL DEFAULT 0,
  `3ds` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`merchant_bank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mod_auth_group`
--

DROP TABLE IF EXISTS `mod_auth_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mod_auth_group` (
  `username` char(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `group_field` char(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`username`,`group_field`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mod_auth_password`
--

DROP TABLE IF EXISTS `mod_auth_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mod_auth_password` (
  `username` varchar(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `auth_group` char(2) DEFAULT NULL,
  `service` smallint(6) DEFAULT NULL,
  `vendor_id` varchar(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_login`
--

DROP TABLE IF EXISTS `model_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL DEFAULT 0,
  `is_compliant` tinyint(4) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1183584 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_name_suggestion`
--

DROP TABLE IF EXISTS `model_name_suggestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_name_suggestion` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `sex` char(1) NOT NULL DEFAULT '',
  `ts_user` varchar(12) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `date_in` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_real_name`
--

DROP TABLE IF EXISTS `model_real_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_real_name` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `stage_name` varchar(255) NOT NULL DEFAULT '',
  `real_name` varchar(255) NOT NULL DEFAULT '',
  `misc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stage_name` (`stage_name`),
  KEY `real_name` (`real_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2592 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_review_type`
--

DROP TABLE IF EXISTS `model_review_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_review_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_timer`
--

DROP TABLE IF EXISTS `model_timer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_timer` (
  `year` enum('2000','2001','2002','2003','2004','2005') NOT NULL DEFAULT '2000',
  `month` enum('1','2','3','4','5','6','7','8','9','10','11','12') NOT NULL DEFAULT '1',
  `day` enum('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31') NOT NULL DEFAULT '1',
  `seconds` int(11) NOT NULL DEFAULT 0,
  `model` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`year`,`month`,`day`,`model`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models`
--

DROP TABLE IF EXISTS `models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  `image` varchar(255) NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `studio` char(12) DEFAULT NULL,
  `active` char(1) NOT NULL DEFAULT 'Y',
  `begin_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `harvest_code` int(10) unsigned NOT NULL DEFAULT 0,
  `is_test_account` enum('Y','N') DEFAULT 'N',
  `studio_location_id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `middlename` varchar(50) NOT NULL DEFAULT '',
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `birthdate` date NOT NULL DEFAULT '0000-00-00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `enc_password` varchar(64) NOT NULL DEFAULT '',
  `salt` varchar(64) NOT NULL DEFAULT '',
  `2fa_secret` varchar(255) DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phone_number` varchar(50) NOT NULL DEFAULT '',
  `mail_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `server_name` varchar(255) NOT NULL DEFAULT '',
  `category_id` int(11) NOT NULL DEFAULT 0,
  `category_id_2` int(10) unsigned NOT NULL DEFAULT 0,
  `category_id_3` int(10) unsigned NOT NULL DEFAULT 0,
  `prev_category_id` int(11) unsigned DEFAULT 0,
  `num_perf` tinyint(4) NOT NULL DEFAULT 1,
  `review_type` char(2) NOT NULL DEFAULT '',
  `custodian_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `rate_id` smallint(4) unsigned NOT NULL DEFAULT 1,
  `room_name` varchar(55) NOT NULL DEFAULT '',
  `can_fake_chat` enum('Y','N') NOT NULL DEFAULT 'Y',
  `can_ban_users` enum('Y','N') NOT NULL DEFAULT 'Y',
  `can_schedule_shows` enum('Y','N') NOT NULL DEFAULT 'Y',
  `vod_allowed` enum('Y','N') NOT NULL DEFAULT 'Y',
  `keep_vod_private` enum('Y','N') NOT NULL DEFAULT 'N',
  `apply_vod_rules` enum('Y','N') NOT NULL DEFAULT 'N',
  `vod_default_cpm` tinyint(2) unsigned NOT NULL DEFAULT 10,
  `vod_import_status` enum('active','active_private') NOT NULL DEFAULT 'active',
  `rate_per_credit` float(4,2) NOT NULL DEFAULT 0.00,
  `use_net_rate` enum('Y','N') NOT NULL DEFAULT 'N',
  `wishlist_id` int(11) NOT NULL DEFAULT 0,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `pre_2010_credits` int(8) unsigned NOT NULL DEFAULT 0,
  `total_credits` int(8) unsigned NOT NULL DEFAULT 0,
  `total_hours` int(8) unsigned NOT NULL DEFAULT 0,
  `min_power_score` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `last_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_first_broadcast` date DEFAULT NULL,
  `interactive` tinyint(1) NOT NULL DEFAULT 1,
  `is_new` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_fetish` enum('Y','N','S') NOT NULL DEFAULT 'N',
  `unlimited_party_chat` enum('Y','N') NOT NULL DEFAULT 'N',
  `unrestricted_party_chat` enum('Y','N') NOT NULL DEFAULT 'N',
  `unlimited_group_chat` enum('Y','N') NOT NULL DEFAULT 'N',
  `unlimited_fake_chat` enum('Y','N') NOT NULL DEFAULT 'N',
  `fetish_image_id` int(10) unsigned NOT NULL DEFAULT 0,
  `country_code` char(3) NOT NULL DEFAULT '',
  `national_id_type` varchar(2) NOT NULL DEFAULT '',
  `national_id_value` varchar(24) NOT NULL DEFAULT '',
  `cookie_policy_acceptance_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cookie_policy_acceptance_ip` varbinary(16) NOT NULL DEFAULT '',
  `cookie_policy_acceptance_version` varchar(32) NOT NULL DEFAULT '',
  `activation_date` datetime DEFAULT NULL,
  `on_f4f` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvc` enum('Y','N') NOT NULL DEFAULT 'Y',
  `on_xvt` enum('Y','N') NOT NULL DEFAULT 'Y',
  `block_guest_chat` enum('Y','N') NOT NULL DEFAULT 'N',
  `block_basic_chat` enum('Y','N') NOT NULL DEFAULT 'N',
  `powerscore_boost_expiry` date NOT NULL DEFAULT '0000-00-00',
  `widescreen_image_id` int(10) unsigned NOT NULL DEFAULT 0,
  `on_sgg` enum('Y','N') NOT NULL DEFAULT 'N',
  `display_fanclub_rate_per_credit` float(4,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_rate_per_credit` float(4,2) NOT NULL DEFAULT 0.00,
  `display_vod_rate_per_credit` float(4,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_rate_per_credit` float(4,2) NOT NULL DEFAULT 0.00,
  `on_club` enum('Y','N') NOT NULL DEFAULT 'N',
  `on_asia` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_new_ended` datetime DEFAULT NULL,
  `is_trans` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `total_credits_scale` int(10) unsigned NOT NULL DEFAULT 0,
  `display_model_access_rate_per_credit` float(4,2) NOT NULL DEFAULT 0.00,
  `sfw_profile_image_id` int(10) unsigned DEFAULT NULL COMMENT 'Stores auto inc ID of default SFW profile(sample) image',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`site_type`),
  KEY `category_id` (`category_id`),
  KEY `review_type` (`review_type`),
  KEY `vod_allowed` (`vod_allowed`),
  KEY `username` (`username`),
  KEY `active` (`active`),
  KEY `category_id_2` (`category_id_2`),
  KEY `category_id_3` (`category_id_3`),
  KEY `studio` (`studio`),
  KEY `interactive` (`interactive`),
  KEY `is_fetish` (`is_fetish`),
  KEY `date_first_broadcast` (`date_first_broadcast`),
  KEY `is_new_ended` (`is_new_ended`)
) ENGINE=InnoDB AUTO_INCREMENT=1442673 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`10.%.%.%`*/ /*!50003 TRIGGER ntl_db.model_status AFTER UPDATE ON ntl_db.models 
FOR EACH ROW BEGIN 
  IF (OLD.active != NEW.active OR OLD.harvest_code != NEW.harvest_code OR OLD.num_perf != NEW.num_perf) THEN 
    UPDATE STUDIOS.Models_Bio 
    SET model_status = NEW.active, sample_image_id = NEW.harvest_code, num_perf = NEW.num_perf, last_updated = NOW() 
    WHERE model_id = NEW.id;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `models_2257`
--

DROP TABLE IF EXISTS `models_2257`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_2257` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `compliant` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `final_check_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `final_check_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`),
  KEY `admin_id` (`admin_id`),
  KEY `final_check_admin_id` (`final_check_admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_2257_log`
--

DROP TABLE IF EXISTS `models_2257_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_2257_log` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `compliant` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`datetime`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_extra_info`
--

DROP TABLE IF EXISTS `models_extra_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_extra_info` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `info_key` varchar(30) NOT NULL DEFAULT '',
  `info_value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`info_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_join_names`
--

DROP TABLE IF EXISTS `models_join_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_join_names` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id_other` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`model_id_other`),
  KEY `model_id_other` (`model_id_other`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_other_names`
--

DROP TABLE IF EXISTS `models_other_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_other_names` (
  `model_id` int(11) NOT NULL DEFAULT 0,
  `other_name` varchar(255) NOT NULL DEFAULT '',
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_password_log`
--

DROP TABLE IF EXISTS `models_password_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_password_log` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `model_id` (`model_id`,`password`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_xvc`
--

DROP TABLE IF EXISTS `models_xvc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_xvc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `xvc_model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `xvc_profile_name` varchar(100) NOT NULL DEFAULT '',
  `vsm_model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `tier_rating` tinyint(1) unsigned NOT NULL DEFAULT 4,
  PRIMARY KEY (`id`),
  UNIQUE KEY `xvc_model_id` (`xvc_model_id`),
  UNIQUE KEY `xvc_profile_name` (`xvc_profile_name`),
  KEY `vsm_model_id` (`vsm_model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7065 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `msb_transact`
--

DROP TABLE IF EXISTS `msb_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msb_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `cvv2_code` char(2) DEFAULT NULL,
  `response_message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mysql_auth`
--

DROP TABLE IF EXISTS `mysql_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mysql_auth` (
  `username` varchar(25) NOT NULL DEFAULT '',
  `passwd` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mysql_auth_groups`
--

DROP TABLE IF EXISTS `mysql_auth_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mysql_auth_groups` (
  `username` varchar(25) DEFAULT NULL,
  `groups` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `new_flirt`
--

DROP TABLE IF EXISTS `new_flirt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `new_flirt` (
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `domain` varchar(100) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  `signup_date` float(13,3) NOT NULL DEFAULT 0.000,
  `status` enum('N','Y','TO') NOT NULL DEFAULT 'N',
  `comments` text DEFAULT NULL,
  `username` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`domain`,`path`),
  KEY `mp_code_status` (`mp_code`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `not_voided_by_johnbot`
--

DROP TABLE IF EXISTS `not_voided_by_johnbot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `not_voided_by_johnbot` (
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `numdayusers`
--

DROP TABLE IF EXISTS `numdayusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `numdayusers` (
  `id` int(11) NOT NULL DEFAULT 0,
  `siteId` int(11) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `users` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `numhourusers`
--

DROP TABLE IF EXISTS `numhourusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `numhourusers` (
  `id` int(11) NOT NULL DEFAULT 0,
  `siteId` int(11) DEFAULT NULL,
  `hour` int(11) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `users` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `numusers`
--

DROP TABLE IF EXISTS `numusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `numusers` (
  `id` int(11) NOT NULL DEFAULT 0,
  `siteId` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `users` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_accounts`
--

DROP TABLE IF EXISTS `oauth_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oauth_identifier` varchar(255) NOT NULL DEFAULT '',
  `oauth_type` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_identifier` (`oauth_identifier`,`oauth_type`)
) ENGINE=InnoDB AUTO_INCREMENT=18014 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optichat_keys`
--

DROP TABLE IF EXISTS `optichat_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optichat_keys` (
  `username` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `optikey` varchar(100) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`username`,`service`,`optikey`),
  KEY `lookup` (`username`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers`
--

DROP TABLE IF EXISTS `optiusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(40) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `salt` varchar(40) NOT NULL,
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(80) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT 'US',
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
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `play_and_pay_user_limit` float(6,2) NOT NULL DEFAULT 0.00,
  `play_and_pay_pending_limit` float(6,2) NOT NULL DEFAULT 0.00,
  `rewards_status` smallint(2) unsigned NOT NULL DEFAULT 1,
  `rewards_level` smallint(3) unsigned NOT NULL DEFAULT 1,
  `rewards_min_level` smallint(3) unsigned NOT NULL DEFAULT 1,
  `rewards_last_calc` date NOT NULL DEFAULT '0000-00-00',
  `fetish_percent` float(4,3) NOT NULL,
  `fetish_counter` int(8) NOT NULL,
  `last_engaged_mp_code` varchar(12) DEFAULT NULL,
  `last_engaged_date` datetime DEFAULT NULL,
  `rewards_points` int(11) NOT NULL DEFAULT 0,
  `rewards_status_points` int(11) NOT NULL DEFAULT 0,
  `rewards_points_rebuilt` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  KEY `password` (`password`),
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `city` (`city`),
  KEY `country` (`country`),
  KEY `zip` (`zip`),
  KEY `phone` (`phone`),
  KEY `email` (`email`),
  KEY `mp_code` (`mp_code`),
  KEY `subscription_product_code` (`subscription_product_code`),
  KEY `dhd_cust_id` (`dhd_cust_id`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`),
  KEY `date_last_login` (`date_last_login`),
  KEY `sitekey` (`sitekey`),
  KEY `domain` (`domain`),
  KEY `username` (`username`,`service`),
  KEY `spending_group` (`spending_group`,`spending_group_date`)
) ENGINE=InnoDB AUTO_INCREMENT=69910089 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`localhost`*/ /*!50003 TRIGGER `ntl_db`.`optiusers_update` AFTER UPDATE ON `optiusers` FOR EACH ROW
BEGIN
			SET @note_str = 'UPDATED: ';
			IF OLD.username <> NEW.username THEN
				SET @note_str = concat(@note_str, "Username: '", OLD.username,"' changed to '",NEW.username,"', ");
			END IF;
			IF OLD.firstname <> NEW.firstname THEN
				SET @note_str = concat(@note_str, "First Name: '", OLD.firstname,"' changed to '",NEW.firstname,"', ");
			END IF;
			IF OLD.lastname <> NEW.lastname THEN
				SET @note_str = concat(@note_str, "Last Name: '", OLD.lastname,"' changed to '",NEW.lastname,"', ");
			END IF;
			IF OLD.address <> NEW.address THEN
				SET @note_str = concat(@note_str, "Address: '", OLD.address,"' changed to '",NEW.address,"', ");
			END IF;
			IF OLD.city <> NEW.city THEN
				SET @note_str = concat(@note_str, "City: '", OLD.city,"' changed to '",NEW.city,"', ");
			END IF;
			IF OLD.state <> NEW.state THEN
				SET @note_str = concat(@note_str, "State: '", OLD.state,"' changed to '",NEW.state,"', ");
			END IF;
			IF OLD.country <> NEW.country THEN
				SET @note_str = concat(@note_str, "Country: '", OLD.country,"' changed to '",NEW.country,"', ");
			END IF;
			IF OLD.zip <> NEW.zip THEN
				SET @note_str = concat(@note_str, "Zip: '", OLD.zip,"' changed to '",NEW.zip,"', ");
			END IF;
			IF OLD.phone <> NEW.phone THEN
				SET @note_str = concat(@note_str, "Phone: '", OLD.phone,"' changed to '",NEW.phone,"', ");
			END IF;
			IF OLD.email <> NEW.email THEN
				SET @note_str = concat(@note_str, "Email: '", OLD.email,"' changed to '",NEW.email,"', ");
				UPDATE MAILINGS.User_List set address = NEW.email where address =  OLD.email;
			END IF;
			IF OLD.total_spent <> NEW.total_spent THEN
				UPDATE ntl_db.optiusers_ex 
				SET total_spent_since_2010 = ( total_spent_since_2010 + ( NEW.total_spent - OLD.total_spent ) ) 
				WHERE user_id = NEW.user_id AND ( total_spent_since_2010 + ( NEW.total_spent - OLD.total_spent ) ) >= 0;
			END IF;
			IF ( OLD.total_spent <> NEW.total_spent AND OLD.timeleft <> NEW.timeleft ) THEN
				UPDATE ntl_db.optiusers_ex 
				SET total_credits_since_2010 = GREATEST( CAST( total_credits_since_2010 AS SIGNED ) + ( NEW.timeleft - OLD.timeleft ), 0 ) 
				WHERE user_id = NEW.user_id;
			
			END IF;
			IF ( OLD.total_spent = NEW.total_spent AND NEW.total_spent = 0 AND NEW.timeleft > OLD.timeleft ) THEN
				SELECT total_credits_since_2010  
				INTO @ox_total_credits_since_2010 
				FROM ntl_db.optiusers_ex 
				WHERE user_id = NEW.user_id;
				IF ( @ox_total_credits_since_2010 = 0 ) THEN
					UPDATE ntl_db.optiusers_ex 
					SET total_credits_since_2010 = GREATEST( CAST( total_credits_since_2010 AS SIGNED ) + ( NEW.timeleft - OLD.timeleft ), 0 ) 
					WHERE user_id = NEW.user_id;
				END IF;
			END IF;
			IF OLD.mp_code <> NEW.mp_code THEN
				SET @note_str = concat(@note_str, "mp_code: '", OLD.mp_code,"' changed to '",NEW.mp_code,"', ");
				INSERT INTO BILLING.mp_code_log set new_mp_code = NEW.mp_code, old_mp_code = OLD.mp_code, user_id = NEW.user_id;
			END IF;
			IF @note_str <> 'UPDATED: ' THEN
				INSERT INTO BILLING.User_Notes values(NULL,NEW.user_id,NEW.email,183,now(),@note_str,0);
			END IF;
        END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `optiusers_audit`
--

DROP TABLE IF EXISTS `optiusers_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_audit` (
  `user_id` int(10) unsigned NOT NULL,
  `old_timeleft` mediumint(7) DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers_ex`
--

DROP TABLE IF EXISTS `optiusers_ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_ex` (
  `user_id` int(10) unsigned NOT NULL,
  `first_purchase_date` date NOT NULL DEFAULT '0000-00-00',
  `game_screen_name` varchar(32) NOT NULL,
  `social_screen_name` varchar(32) NOT NULL,
  `birthdate` date NOT NULL,
  `total_game_points` int(9) unsigned NOT NULL,
  `forum_status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `1click_processor_id` tinyint(3) unsigned NOT NULL,
  `total_spent_since_2010` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
  `total_credits_since_2010` int(11) unsigned NOT NULL DEFAULT 0,
  `privacy_policy_acceptance_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `privacy_policy_acceptance_ip` varbinary(16) NOT NULL DEFAULT '',
  `privacy_policy_acceptance_version` varchar(32) NOT NULL DEFAULT '',
  `popunder_optout` enum('yes','no') NOT NULL DEFAULT 'no',
  `cookie_policy_acceptance_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cookie_policy_acceptance_ip` varbinary(16) NOT NULL DEFAULT '',
  `cookie_policy_acceptance_version` varchar(32) NOT NULL DEFAULT '',
  `vwl` tinyint(1) NOT NULL DEFAULT 0,
  `timezone` varchar(36) NOT NULL DEFAULT '',
  `bad_lead_datetime` datetime DEFAULT NULL,
  `bad_lead_reason` varchar(255) DEFAULT NULL,
  `email_status` varchar(64) NOT NULL DEFAULT '',
  `valid_email` tinyint(1) NOT NULL DEFAULT 1,
  `age_verified` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`user_id`),
  KEY `first_purchase_date` (`first_purchase_date`),
  KEY `bad_lead_datetime` (`bad_lead_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers_oauth`
--

DROP TABLE IF EXISTS `optiusers_oauth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_oauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oauth_identifier` varchar(255) NOT NULL DEFAULT '',
  `oauth_type` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`),
  KEY `oauth_identifier` (`oauth_identifier`,`oauth_type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=517738 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers_oauth_keys`
--

DROP TABLE IF EXISTS `optiusers_oauth_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_oauth_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oauth_identifier` varchar(255) NOT NULL DEFAULT '',
  `oauth_type` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL,
  `oauth_token` varchar(255) NOT NULL,
  `oauth_token_secret` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_identifier` (`oauth_identifier`,`oauth_type`) USING BTREE,
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=501943 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers_pending_conf`
--

DROP TABLE IF EXISTS `optiusers_pending_conf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_pending_conf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `salt` varchar(40) NOT NULL DEFAULT '',
  `auth_key` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `firstname` varchar(50) DEFAULT '',
  `lastname` varchar(50) DEFAULT '',
  `country` varchar(50) DEFAULT '',
  `state` varchar(32) DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `product_code` int(11) NOT NULL,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(64) NOT NULL DEFAULT '',
  `source_site_id` smallint(4) unsigned NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reminder_sent` tinyint(1) NOT NULL DEFAULT 0,
  `update_cnt` tinyint(1) NOT NULL DEFAULT 0,
  `iovation_bb` text NOT NULL,
  `language` varchar(2) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_sitekey` (`username`,`sitekey`),
  KEY `email_sitekey_auth` (`email`,`sitekey`,`auth_key`),
  KEY `email_username_sitekey_auth` (`email`,`username`,`sitekey`,`auth_key`),
  KEY `email_password_sitekey_date` (`email`,`password`,`sitekey`,`date_created`),
  KEY `username_sitekey_email_date` (`username`,`sitekey`,`email`,`date_created`),
  KEY `username_password_email_sitekey` (`username`,`password`,`email`,`sitekey`),
  KEY `username_password_sitekey_update` (`username`,`password`,`sitekey`,`update_cnt`),
  KEY `username_password_auth_sitekey` (`username`,`password`,`auth_key`,`sitekey`),
  KEY `date_sitekey_reminder` (`date_created`,`sitekey`,`reminder_sent`)
) ENGINE=InnoDB AUTO_INCREMENT=3633770 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers_settings`
--

DROP TABLE IF EXISTS `optiusers_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_settings` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `key` varchar(32) NOT NULL DEFAULT '',
  `value` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers_site_logins`
--

DROP TABLE IF EXISTS `optiusers_site_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_site_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `ip_address_long` varbinary(16) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fail_code` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `login_type` varchar(20) NOT NULL DEFAULT '',
  `request_id` varchar(20) DEFAULT NULL COMMENT 'Key to BILLING.Fingerprints',
  `no_adult` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ip_address_long` (`ip_address_long`),
  KEY `datetime` (`datetime`),
  KEY `fail_code` (`fail_code`),
  KEY `username` (`username`),
  KEY `domain_login_type` (`domain`,`login_type`),
  KEY `sitekey` (`sitekey`),
  KEY `login_type` (`login_type`),
  KEY `request_id` (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=597989853 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers_updates`
--

DROP TABLE IF EXISTS `optiusers_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `type` varchar(64) NOT NULL,
  `old_value` varchar(255) NOT NULL,
  `new_value` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `optiusers_xvc`
--

DROP TABLE IF EXISTS `optiusers_xvc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optiusers_xvc` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(40) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `salt` varchar(40) NOT NULL,
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(80) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT 'US',
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
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `play_and_pay_user_limit` float(6,2) NOT NULL DEFAULT 0.00,
  `play_and_pay_pending_limit` float(6,2) NOT NULL DEFAULT 0.00,
  `rewards_status` smallint(2) unsigned NOT NULL DEFAULT 1,
  `rewards_level` smallint(3) unsigned NOT NULL DEFAULT 1,
  `rewards_min_level` smallint(3) unsigned NOT NULL DEFAULT 1,
  `rewards_last_calc` date NOT NULL DEFAULT '0000-00-00',
  `fetish_percent` float(4,3) NOT NULL,
  `fetish_counter` int(8) NOT NULL,
  `imported` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`username`,`service`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `password` (`password`),
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `city` (`city`),
  KEY `country` (`country`),
  KEY `zip` (`zip`),
  KEY `phone` (`phone`),
  KEY `email` (`email`),
  KEY `mp_code` (`mp_code`),
  KEY `subscription_product_code` (`subscription_product_code`),
  KEY `dhd_cust_id` (`dhd_cust_id`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`),
  KEY `date_last_login` (`date_last_login`),
  KEY `spending_group` (`spending_group`),
  KEY `sitekey` (`sitekey`),
  KEY `domain` (`domain`),
  KEY `email_verified` (`email_verified`),
  KEY `imported` (`imported`)
) ENGINE=InnoDB AUTO_INCREMENT=37591383 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_tracking`
--

DROP TABLE IF EXISTS `page_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_tracking` (
  `page_type` enum('reg','rereg') DEFAULT NULL,
  `style` varchar(5) DEFAULT NULL,
  `source_code` varchar(50) DEFAULT NULL,
  `script` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paycom_product_id`
--

DROP TABLE IF EXISTS `paycom_product_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paycom_product_id` (
  `product_id_str` varchar(255) NOT NULL DEFAULT '',
  `amount` float(24,2) NOT NULL DEFAULT 0.00,
  `file_str` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`product_id_str`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paycom_rx`
--

DROP TABLE IF EXISTS `paycom_rx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paycom_rx` (
  `co_code_str` char(3) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `filename_str` varchar(12) NOT NULL DEFAULT '',
  `order_id` int(11) NOT NULL DEFAULT 0,
  `initial_transaction_date` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(63) NOT NULL DEFAULT '',
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `country` char(3) NOT NULL DEFAULT '',
  `transaction_amount` float(24,2) NOT NULL DEFAULT 0.00,
  `product_id` varchar(255) NOT NULL DEFAULT '',
  `transaction_type` char(1) NOT NULL DEFAULT '',
  `payment_method` char(1) NOT NULL DEFAULT '',
  `reseller_code` varchar(10) NOT NULL DEFAULT '',
  `password_removal_date` varchar(20) DEFAULT NULL,
  `transaction_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paycom_transact`
--

DROP TABLE IF EXISTS `paycom_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paycom_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `ipaddress` varchar(16) DEFAULT NULL,
  `product_id` varchar(255) DEFAULT NULL,
  `submit_count` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `session_id` varchar(255) DEFAULT NULL,
  `ans` varchar(255) DEFAULT NULL,
  `cvv2_return_code` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paypal_transact`
--

DROP TABLE IF EXISTS `paypal_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paypal_transact` (
  `receiver_email` varchar(20) NOT NULL DEFAULT '',
  `item_name` varchar(255) DEFAULT NULL,
  `item_number` char(1) DEFAULT NULL,
  `quantity` tinyint(1) DEFAULT NULL,
  `invoice` int(11) unsigned NOT NULL DEFAULT 0,
  `custom` varchar(255) DEFAULT NULL,
  `payment_status` varchar(10) DEFAULT NULL,
  `pending_reason` varchar(11) DEFAULT NULL,
  `payment_date` varchar(35) DEFAULT NULL,
  `payment_gross` float(24,2) DEFAULT NULL,
  `payment_fee` float(24,2) DEFAULT NULL,
  `txn_id` varchar(255) DEFAULT NULL,
  `txn_type` varchar(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address_street` varchar(80) DEFAULT NULL,
  `address_city` varchar(100) DEFAULT NULL,
  `address_state` varchar(32) DEFAULT NULL,
  `address_zip` varchar(32) DEFAULT NULL,
  `address_country` varchar(50) DEFAULT NULL,
  `address_status` varchar(11) DEFAULT NULL,
  `payer_email` varchar(255) DEFAULT NULL,
  `payer_status` varchar(16) DEFAULT NULL,
  `payment_method` varchar(7) DEFAULT NULL,
  `notify_version` varchar(6) DEFAULT NULL,
  `verify_sign` varchar(255) DEFAULT NULL,
  `subscr_id` varchar(255) DEFAULT NULL,
  `subscr_date` varchar(35) DEFAULT NULL,
  `period1` varchar(6) DEFAULT NULL,
  `period2` varchar(6) DEFAULT NULL,
  `period3` varchar(6) DEFAULT NULL,
  `amount1` float(24,2) DEFAULT NULL,
  `amount2` float(24,2) DEFAULT NULL,
  `amount3` float(24,2) DEFAULT NULL,
  `recurring` char(1) DEFAULT NULL,
  `reattempt` char(1) DEFAULT NULL,
  `retry_at` varchar(35) DEFAULT NULL,
  `verify_response` varchar(9) DEFAULT NULL,
  `payer_id` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payperview_ticket`
--

DROP TABLE IF EXISTS `payperview_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payperview_ticket` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `ticket_id` varchar(7) DEFAULT NULL,
  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pnp_transact`
--

DROP TABLE IF EXISTS `pnp_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pnp_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `app_level` tinyint(1) DEFAULT NULL,
  `FinalStatus` varchar(255) DEFAULT NULL,
  `MErrMsg` varchar(255) DEFAULT NULL,
  `MStatus` varchar(255) DEFAULT NULL,
  `success` varchar(255) DEFAULT NULL,
  `auth_msg` varchar(255) DEFAULT NULL,
  `auth_code` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `cvvresp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `premium_feeds`
--

DROP TABLE IF EXISTS `premium_feeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `premium_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `feed_id` varchar(25) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preview_content`
--

DROP TABLE IF EXISTS `preview_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `preview_content` (
  `site_id` smallint(6) NOT NULL DEFAULT 0,
  `category_id` varchar(4) NOT NULL DEFAULT '0',
  `content_id` tinyint(4) NOT NULL DEFAULT 0,
  `viewmember` enum('N','Y') NOT NULL DEFAULT 'N',
  `order_by` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `processor_down`
--

DROP TABLE IF EXISTS `processor_down`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `processor_down` (
  `processor` varchar(12) NOT NULL DEFAULT '',
  `date_str` datetime DEFAULT NULL,
  PRIMARY KEY (`processor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `product_id` mediumint(8) unsigned zerofill NOT NULL DEFAULT 00000000,
  `name` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `type` smallint(3) unsigned NOT NULL DEFAULT 0,
  `mp_link` varchar(100) NOT NULL DEFAULT 'NA',
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_attributes` (
  `site_id` int(10) unsigned zerofill NOT NULL DEFAULT 0000000000,
  `product_id` mediumint(8) unsigned zerofill NOT NULL DEFAULT 00000000,
  `attribute_name` varchar(50) NOT NULL DEFAULT '',
  `attribute_value` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`site_id`,`product_id`,`attribute_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_billing`
--

DROP TABLE IF EXISTS `product_billing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `hit_cookie_value` varchar(50) DEFAULT NULL,
  `monthly_charge` float(10,2) DEFAULT NULL,
  `addl_url` float(10,2) DEFAULT NULL,
  `tier1_hits` int(11) DEFAULT NULL,
  `tier1_amount` int(11) DEFAULT NULL,
  `tier1_overage` int(11) DEFAULT NULL,
  `tier2_hits` int(11) DEFAULT NULL,
  `tier2_amount` int(11) DEFAULT NULL,
  `tier2_overage` int(11) DEFAULT NULL,
  `tier3_hits` int(11) DEFAULT NULL,
  `tier3_amount` int(11) DEFAULT NULL,
  `tier3_overage` int(11) DEFAULT NULL,
  `tier4_hits` int(11) DEFAULT NULL,
  `tier4_amount` int(11) DEFAULT NULL,
  `tier4_overage` int(11) DEFAULT NULL,
  `tier5_hits` int(11) DEFAULT NULL,
  `tier5_amount` int(11) DEFAULT NULL,
  `tier5_overage` int(11) DEFAULT NULL,
  `tier6_hits` int(11) DEFAULT NULL,
  `tier6_amount` int(11) DEFAULT NULL,
  `tier6_overage` int(11) DEFAULT NULL,
  `tier7_hits` int(11) DEFAULT NULL,
  `tier7_amount` int(11) DEFAULT NULL,
  `tier7_overage` int(11) DEFAULT NULL,
  `tier8_hits` int(11) DEFAULT NULL,
  `tier8_amount` int(11) DEFAULT NULL,
  `tier8_overage` int(11) DEFAULT NULL,
  `tier9_hits` int(11) DEFAULT NULL,
  `tier9_amount` int(11) DEFAULT NULL,
  `tier9_overage` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_code`
--

DROP TABLE IF EXISTS `product_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_code` (
  `product_code` varchar(6) NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `minutes_days` int(11) NOT NULL DEFAULT 0,
  `amount` float(5,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) NOT NULL DEFAULT '',
  `dhd_prod_id` int(9) unsigned NOT NULL DEFAULT 0,
  `VIP` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_code`),
  KEY `dhd_prod_id` (`dhd_prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_detail`
--

DROP TABLE IF EXISTS `product_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_detail` (
  `product_id` smallint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `link` varchar(150) DEFAULT NULL,
  `type` smallint(3) unsigned NOT NULL DEFAULT 0,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `sexual_orientation` enum('straight','gay','both') NOT NULL DEFAULT 'straight',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_id`
--

DROP TABLE IF EXISTS `product_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_id` (
  `product_code` varchar(6) NOT NULL DEFAULT '',
  `product_id` mediumint(8) unsigned zerofill NOT NULL DEFAULT 00000000,
  PRIMARY KEY (`product_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psob_transact`
--

DROP TABLE IF EXISTS `psob_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `psob_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `cvv2_code` char(2) DEFAULT NULL,
  `response_message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psw_product_id`
--

DROP TABLE IF EXISTS `psw_product_id`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `psw_product_id` (
  `id` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `psw_transact`
--

DROP TABLE IF EXISTS `psw_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `psw_transact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `subscriber_id` int(11) DEFAULT NULL,
  `error_code` mediumint(5) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `country_number` smallint(3) DEFAULT NULL,
  `amount_digits` mediumint(7) unsigned DEFAULT NULL,
  `ip_address` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publishd_last_run`
--

DROP TABLE IF EXISTS `publishd_last_run`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `publishd_last_run` (
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_amount`
--

DROP TABLE IF EXISTS `ra_amount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_amount` (
  `amount` float(6,2) NOT NULL DEFAULT 0.00,
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`amount`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_avs`
--

DROP TABLE IF EXISTS `ra_avs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_avs` (
  `avs` char(1) NOT NULL DEFAULT '',
  `ratio` float(7,5) DEFAULT NULL,
  `sales` float(10,2) DEFAULT NULL,
  PRIMARY KEY (`avs`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_bin`
--

DROP TABLE IF EXISTS `ra_bin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_bin` (
  `bin` varchar(9) NOT NULL,
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`bin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_class_b`
--

DROP TABLE IF EXISTS `ra_class_b`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_class_b` (
  `class_b` varchar(7) NOT NULL DEFAULT '',
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`class_b`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_class_c`
--

DROP TABLE IF EXISTS `ra_class_c`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_class_c` (
  `class_c` varchar(11) NOT NULL DEFAULT '',
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`class_c`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_class_c_temp`
--

DROP TABLE IF EXISTS `ra_class_c_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_class_c_temp` (
  `remote_ip` varchar(15) NOT NULL DEFAULT '',
  `credit_type` tinyint(4) NOT NULL DEFAULT 0,
  `amount` float(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('accepted','rejected','pending') NOT NULL DEFAULT 'accepted',
  `date_out` float(13,3) NOT NULL DEFAULT 0.000
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_country`
--

DROP TABLE IF EXISTS `ra_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_country` (
  `country` varchar(50) NOT NULL DEFAULT '',
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_csza`
--

DROP TABLE IF EXISTS `ra_csza`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_csza` (
  `csza` varchar(12) NOT NULL DEFAULT '',
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) DEFAULT NULL,
  PRIMARY KEY (`csza`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_domain`
--

DROP TABLE IF EXISTS `ra_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_domain` (
  `domain` varchar(255) NOT NULL DEFAULT '',
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_mp_code`
--

DROP TABLE IF EXISTS `ra_mp_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_mp_code` (
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_recent_transact`
--

DROP TABLE IF EXISTS `ra_recent_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_recent_transact` (
  `id` int(11) unsigned NOT NULL,
  `chargeback_id` int(11) NOT NULL,
  `credit_type` tinyint(4) unsigned NOT NULL,
  `amount` float(5,2) unsigned NOT NULL,
  `date_out` float(13,3) unsigned NOT NULL,
  `remote_ip` varchar(15) NOT NULL,
  `zip` varchar(15) NOT NULL,
  `BIN` varchar(9) NOT NULL,
  `mp_code` varchar(5) NOT NULL,
  `country` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date_out` (`date_out`),
  KEY `remote_ip` (`remote_ip`),
  KEY `chargeback_id` (`chargeback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_user_score`
--

DROP TABLE IF EXISTS `ra_user_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_user_score` (
  `cc_num` varchar(255) NOT NULL DEFAULT '',
  `weighted_score` float(7,5) DEFAULT NULL,
  `block_code` mediumint(5) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `member_since` datetime DEFAULT NULL,
  `ts_score` varchar(5) DEFAULT NULL,
  `ts_user` varchar(25) DEFAULT NULL,
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `class_c_ratio` float(7,5) DEFAULT NULL,
  `zip_ratio` float(7,5) DEFAULT NULL,
  `bin_ratio` float(7,5) DEFAULT NULL,
  `class_b_ratio` float(7,5) DEFAULT NULL,
  `domain_ratio` float(7,5) DEFAULT NULL,
  `mp_code_ratio` float(7,5) DEFAULT NULL,
  `last_chargeback_id` int(11) NOT NULL DEFAULT 0,
  `country_ratio` float(7,5) DEFAULT NULL,
  `cs_max_score` tinyint(4) DEFAULT NULL,
  `zip` varchar(15) NOT NULL DEFAULT '',
  `email` varchar(230) NOT NULL DEFAULT '',
  `email_bounced` char(1) DEFAULT NULL,
  `enc_cc_num` tinyblob NOT NULL,
  `TRN` varchar(9) NOT NULL DEFAULT '',
  `enc_DDA` tinyblob NOT NULL,
  `account_type` varchar(20) NOT NULL DEFAULT '',
  `review_datetime` datetime NOT NULL,
  KEY `transact_id` (`transact_id`),
  KEY `enc_cc_num` (`enc_cc_num`(255)),
  KEY `enc_DDA` (`enc_DDA`(255),`TRN`),
  KEY `zip` (`zip`),
  KEY `email` (`email`),
  KEY `ts_score` (`ts_score`),
  KEY `member_since` (`member_since`),
  KEY `scoring` (`block_code`,`account_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_user_score_ext`
--

DROP TABLE IF EXISTS `ra_user_score_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_user_score_ext` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transact_id` int(11) DEFAULT NULL,
  `date_scored` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `date_scored` (`date_scored`)
) ENGINE=InnoDB AUTO_INCREMENT=102238 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_user_score_history`
--

DROP TABLE IF EXISTS `ra_user_score_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_user_score_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(230) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `score_prev` tinyint(4) DEFAULT NULL,
  `score_new` tinyint(4) NOT NULL,
  `source` enum('queue','manual') DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`,`modified_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=662336 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_wb`
--

DROP TABLE IF EXISTS `ra_wb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_wb` (
  `name` varchar(30) NOT NULL DEFAULT '',
  `weight` float(7,5) DEFAULT NULL,
  `block` float(7,5) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ra_zip`
--

DROP TABLE IF EXISTS `ra_zip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ra_zip` (
  `zip` varchar(20) NOT NULL DEFAULT '',
  `ratio` float(7,5) NOT NULL DEFAULT 0.00000,
  `sales` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`zip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registration_product_request`
--

DROP TABLE IF EXISTS `registration_product_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `registration_product_request` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `rv_id` int(12) NOT NULL DEFAULT 0,
  `rv1_id` int(12) NOT NULL DEFAULT 0,
  `product_id` varchar(45) NOT NULL DEFAULT '0',
  `user_id` int(12) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `rv_id` (`rv_id`),
  KEY `rv1_id` (`rv1_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=346212 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `retrieval_log`
--

DROP TABLE IF EXISTS `retrieval_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `retrieval_log` (
  `sale_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review_transact`
--

DROP TABLE IF EXISTS `review_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_transact` (
  `username` varchar(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `inserted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`username`,`transact_id`),
  KEY `transact_id` (`transact_id`),
  KEY `inserted_at` (`inserted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review_transact_rules`
--

DROP TABLE IF EXISTS `review_transact_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_transact_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `txn_score` varchar(45) DEFAULT NULL,
  `cs_score` varchar(45) DEFAULT NULL,
  `days` smallint(5) unsigned DEFAULT 60,
  PRIMARY KEY (`id`),
  UNIQUE KEY `score_unique` (`txn_score`,`cs_score`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review_transact_rules_override`
--

DROP TABLE IF EXISTS `review_transact_rules_override`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_transact_rules_override` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `days` smallint(5) unsigned NOT NULL DEFAULT 0,
  `expires` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_unique` (`user_id`),
  KEY `user_id` (`user_id`),
  KEY `expires_index` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `room_ports`
--

DROP TABLE IF EXISTS `room_ports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `room_ports` (
  `room_name` varchar(30) NOT NULL DEFAULT '',
  `port` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`room_name`,`port`),
  KEY `room_name` (`room_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schedule_check`
--

DROP TABLE IF EXISTS `schedule_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room` varchar(100) DEFAULT NULL,
  `weekday` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `shift_start` time NOT NULL DEFAULT '00:00:00',
  `shift_end` time NOT NULL DEFAULT '00:00:00',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13484 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `screen_name_bios`
--

DROP TABLE IF EXISTS `screen_name_bios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `screen_name_bios` (
  `user_id` int(11) unsigned NOT NULL,
  `screen_name_lower` varchar(32) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `tagline` varchar(140) NOT NULL DEFAULT '',
  `birthdate` date NOT NULL DEFAULT '0000-00-00',
  `gender` enum('not_specified','female','male','trans') NOT NULL DEFAULT 'not_specified',
  `orientation` enum('not_specified','straight','gay','bi') NOT NULL DEFAULT 'not_specified',
  `text_physical` text NOT NULL,
  `text_likes` text NOT NULL,
  `looking_for` text NOT NULL,
  `location` varchar(50) NOT NULL DEFAULT '',
  `relationship` varchar(50) NOT NULL DEFAULT '',
  `star_sign` varchar(50) NOT NULL DEFAULT '',
  `ethnicity` enum('not_specified','mixed','caucasian','pacific','asian','african','native','black','hispanic','other') NOT NULL DEFAULT 'not_specified',
  `languages` set('not_specified','en','de','ru','es','pl','cs','fr','it','jp','nl','tl') NOT NULL DEFAULT 'not_specified',
  `webcam` enum('Y','N') NOT NULL DEFAULT 'N',
  `view_count` int(11) unsigned NOT NULL DEFAULT 0,
  `avatar_binary` longblob DEFAULT NULL,
  `avatar_review` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `avatar_type` enum('gif','jpg') NOT NULL DEFAULT 'jpg',
  `avatar_build_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `num_posts` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `default_avatar_id` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`screen_name_lower`),
  KEY `name` (`name`),
  KEY `birthdate` (`birthdate`),
  KEY `gender` (`gender`),
  KEY `orientation` (`orientation`),
  KEY `location` (`location`),
  KEY `relationship` (`relationship`),
  KEY `ethnicity` (`ethnicity`),
  KEY `languages` (`languages`),
  KEY `webcam` (`webcam`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `screen_names`
--

DROP TABLE IF EXISTS `screen_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `screen_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `screen_name` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `screen_name_lower` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(40) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `reconciled` enum('N','Y') DEFAULT 'Y',
  `chat_default` enum('Y','N') NOT NULL DEFAULT 'N',
  `set_rewards_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `set_rewards_level` smallint(3) unsigned NOT NULL DEFAULT 0,
  `show_performers_rewards` enum('Y','N') NOT NULL DEFAULT 'Y',
  `show_customers_rewards` enum('Y','N') NOT NULL DEFAULT 'Y',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `anonymous_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `dm_online` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `optiusers_id` (`optiusers_id`),
  KEY `screen_name_lower` (`screen_name_lower`(12)),
  KEY `screen_name` (`screen_name`)
) ENGINE=InnoDB AUTO_INCREMENT=68332403 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_tracking`
--

DROP TABLE IF EXISTS `search_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `search_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `search_reason` varchar(255) DEFAULT NULL,
  `search_field` varchar(50) DEFAULT NULL,
  `search_string` varchar(255) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `search_field` (`search_field`),
  KEY `search_string` (`search_string`),
  KEY `manager_id` (`manager_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=253243 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `service` (
  `service_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(20) NOT NULL DEFAULT '',
  `service_parent_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `service_display_title` varchar(40) NOT NULL DEFAULT '',
  `rank` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`service_name`),
  UNIQUE KEY `service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `signups`
--

DROP TABLE IF EXISTS `signups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `signups` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `signups` varchar(255) DEFAULT NULL,
  `average_dollars` float(10,2) DEFAULT NULL,
  `exclude` char(1) DEFAULT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `retention` float(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `signups_start`
--

DROP TABLE IF EXISTS `signups_start`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `signups_start` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `average_dollars` float(10,2) DEFAULT NULL,
  `average_return_dollars` float(10,2) DEFAULT NULL,
  `returns_ratio` float(4,1) DEFAULT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `start_with_trial` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `signups_trial_renewals`
--

DROP TABLE IF EXISTS `signups_trial_renewals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `signups_trial_renewals` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `renewal_ratio` float(4,1) DEFAULT NULL,
  `average_dollars` float(10,2) DEFAULT NULL,
  `average_return_dollars` float(10,2) DEFAULT NULL,
  `returns_ratio` float(4,1) DEFAULT NULL,
  `product_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site`
--

DROP TABLE IF EXISTS `site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `keyword` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `framerow` smallint(6) DEFAULT NULL,
  `frameposition` enum('T','S') NOT NULL DEFAULT 'T',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_master_subscription`
--

DROP TABLE IF EXISTS `site_master_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_master_subscription` (
  `master_sub_id` int(11) NOT NULL AUTO_INCREMENT,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `product_id` int(11) DEFAULT NULL,
  `tier` int(11) NOT NULL DEFAULT 1,
  `monthly_charge` float(10,2) DEFAULT 0.00,
  `overage` float(10,2) DEFAULT 0.00,
  `num_urls` int(11) DEFAULT 0,
  `url_amt` float(10,2) DEFAULT 0.00,
  `discount` enum('N','Y','PR') NOT NULL DEFAULT 'N',
  `discounted_charge` float(10,2) DEFAULT 0.00,
  `comments` text DEFAULT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`master_sub_id`),
  KEY `mp_code` (`mp_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2570 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_subscription`
--

DROP TABLE IF EXISTS `site_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_subscription` (
  `subscription_id` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(3) DEFAULT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `comments` text DEFAULT NULL,
  `quantity` mediumint(8) unsigned DEFAULT 0,
  `monthly_charge` float(10,2) DEFAULT 0.00,
  `bill_by` varchar(5) DEFAULT NULL,
  `bandwidth_source` enum('VS','BP') DEFAULT 'VS',
  `mp_new_win` enum('Y','N') DEFAULT 'Y',
  `exit_traffic_status` enum('N','Y') DEFAULT 'N',
  `domain` varchar(100) DEFAULT NULL,
  `path` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `site_username` varchar(20) DEFAULT NULL,
  `site_password` varchar(20) DEFAULT NULL,
  `activation_date` float(13,3) DEFAULT 0.000,
  `publish_date` date DEFAULT NULL,
  `status` enum('Y','N') DEFAULT 'Y',
  `upsell` enum('N','Y') NOT NULL DEFAULT 'N',
  `hits_threshold` int(11) NOT NULL DEFAULT 250,
  `pack_id` int(11) DEFAULT NULL,
  `discounted_charge` float(10,2) DEFAULT 0.00,
  PRIMARY KEY (`subscription_id`),
  KEY `mp_code` (`mp_code`),
  KEY `domain` (`domain`),
  KEY `path` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=54925 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skins`
--

DROP TABLE IF EXISTS `skins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skins` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `image` varchar(100) NOT NULL DEFAULT '',
  `k_size` int(11) NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` enum('Y','N','P') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `source_code`
--

DROP TABLE IF EXISTS `source_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `source_code` (
  `source_code` int(5) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  `group_id` tinyint(2) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `filename` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `second_id` int(11) unsigned DEFAULT NULL,
  `page_type` varchar(20) DEFAULT NULL,
  `parent_source_code` int(5) unsigned NOT NULL DEFAULT 0,
  `feature_show_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`source_code`),
  KEY `feature_show_id` (`feature_show_id`),
  KEY `description` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=14665 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `source_code_first`
--

DROP TABLE IF EXISTS `source_code_first`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `source_code_first` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `source_code` mediumint(5) NOT NULL DEFAULT 0,
  `hits` mediumint(7) NOT NULL DEFAULT 1,
  PRIMARY KEY (`date`,`source_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `source_code_group`
--

DROP TABLE IF EXISTS `source_code_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `source_code_group` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `source_code_last`
--

DROP TABLE IF EXISTS `source_code_last`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `source_code_last` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `source_code` mediumint(5) NOT NULL DEFAULT 0,
  `hits` mediumint(7) NOT NULL DEFAULT 1,
  PRIMARY KEY (`date`,`source_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `special_offer_email`
--

DROP TABLE IF EXISTS `special_offer_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `special_offer_email` (
  `email` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `static_sale_seq`
--

DROP TABLE IF EXISTS `static_sale_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `static_sale_seq` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `mp_code` varchar(4) NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `seq_no` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`username`,`mp_code`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `studios`
--

DROP TABLE IF EXISTS `studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `studios` (
  `name` varchar(25) NOT NULL,
  `studio` char(12) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  `status` char(1) DEFAULT 'Y',
  `feature` enum('Y','N') NOT NULL DEFAULT 'Y',
  `girls` enum('Y','N') NOT NULL DEFAULT 'Y',
  `guys` enum('N','Y') NOT NULL DEFAULT 'N',
  `amateur` enum('N','Y') NOT NULL DEFAULT 'N',
  `asians` enum('N','Y') NOT NULL DEFAULT 'N',
  `trans` enum('N','Y') NOT NULL DEFAULT 'N',
  `dungeon` enum('N','Y') NOT NULL DEFAULT 'N',
  `room_block_start` smallint(5) unsigned DEFAULT NULL,
  `room_block_end` smallint(5) unsigned DEFAULT NULL,
  `user_monitor` varchar(12) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `pass_monitor` varchar(12) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `featureguys` enum('Y','N') NOT NULL DEFAULT 'N',
  `videomanager_host` varchar(100) NOT NULL DEFAULT '',
  `video_test_url` varchar(255) DEFAULT NULL,
  `agreement_signed` enum('N','Y') NOT NULL DEFAULT 'N',
  `banking_info_received` enum('N','Y') NOT NULL DEFAULT 'N',
  `2257_forms_received` enum('N','Y') NOT NULL DEFAULT 'N',
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `model_login_by` enum('model','admin') NOT NULL DEFAULT 'model',
  `activation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deactivation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `performer_app_installs` smallint(4) unsigned DEFAULT 0,
  `custodian_id` mediumint(8) unsigned DEFAULT NULL,
  `salesperson_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `salesperson_quota` mediumint(8) unsigned NOT NULL DEFAULT 100,
  `contact_details` text NOT NULL,
  `pay_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `power_score_adj` decimal(3,2) NOT NULL DEFAULT 0.00,
  `newbie_hours` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `display_fanclub_pay_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_pay_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `display_vod_pay_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_pay_rate` float(4,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`studio`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subs_stats_daily`
--

DROP TABLE IF EXISTS `subs_stats_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subs_stats_daily` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `product` varchar(25) NOT NULL DEFAULT '',
  `subs` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `sales_avg` float(7,2) unsigned NOT NULL DEFAULT 0.00,
  `returned_avg` float(7,2) unsigned NOT NULL DEFAULT 0.00,
  `days_avg` smallint(4) unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscription_attributes`
--

DROP TABLE IF EXISTS `subscription_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_id` smallint(3) NOT NULL DEFAULT 0,
  `attribute_name` varchar(50) NOT NULL DEFAULT '',
  `attribute_value` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`subscription_id`,`attribute_name`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscription_tracking`
--

DROP TABLE IF EXISTS `subscription_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_tracking` (
  `day` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `site_id` int(11) NOT NULL DEFAULT 33,
  `raw_hits` int(13) NOT NULL DEFAULT 0,
  `unique_hits` int(13) NOT NULL DEFAULT 0,
  PRIMARY KEY (`day`,`mp_code`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp`
--

DROP TABLE IF EXISTS `temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `temp` (
  `mike` varchar(12) NOT NULL DEFAULT '',
  `joe` varchar(12) NOT NULL DEFAULT '',
  `whatever` varchar(12) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_date`
--

DROP TABLE IF EXISTS `temp_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `temp_date` (
  `day` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_pay_period`
--

DROP TABLE IF EXISTS `temp_pay_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `temp_pay_period` (
  `model_id` mediumint(8) DEFAULT NULL,
  `period` smallint(4) DEFAULT NULL,
  `minutes` mediumint(8) DEFAULT NULL,
  `credits` mediumint(8) DEFAULT NULL,
  `users` smallint(4) DEFAULT NULL,
  KEY `period` (`period`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_cc_num_to_ip`
--

DROP TABLE IF EXISTS `test_cc_num_to_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `test_cc_num_to_ip` (
  `cc_num` varchar(255) NOT NULL DEFAULT '',
  `remote_ip` varchar(16) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`cc_num`,`remote_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timeup`
--

DROP TABLE IF EXISTS `timeup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `timeup` (
  `roomname` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`roomname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tip_fixs`
--

DROP TABLE IF EXISTS `tip_fixs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tip_fixs` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trans_switchover_data`
--

DROP TABLE IF EXISTS `trans_switchover_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trans_switchover_data` (
  `email` varchar(100) NOT NULL DEFAULT '',
  `username_original` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `username_new` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `time_left` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `first_name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`email`,`username_original`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact`
--

DROP TABLE IF EXISTS `transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `firstname` varchar(100) NOT NULL DEFAULT '',
  `lastname` varchar(100) NOT NULL DEFAULT '',
  `address` varchar(80) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT 'US',
  `zip` varchar(15) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `cc_num` varchar(255) NOT NULL DEFAULT '',
  `cc_exp_mo` varchar(255) NOT NULL DEFAULT '',
  `cc_exp_yr` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(64) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT '',
  `product_code` varchar(6) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `status` enum('accepted','rejected','pending') NOT NULL DEFAULT 'accepted',
  `cascade_status` enum('initial','cascade') NOT NULL DEFAULT 'initial',
  `remote_ip` varbinary(16) NOT NULL DEFAULT '',
  `ip_country` char(2) NOT NULL DEFAULT '',
  `score` tinyint(4) NOT NULL DEFAULT 0,
  `auth_code` varchar(8) NOT NULL DEFAULT '',
  `avs_flag` varchar(15) NOT NULL DEFAULT '',
  `avs_code` char(2) NOT NULL DEFAULT 'A',
  `date_in` int(11) unsigned NOT NULL DEFAULT 0,
  `date_out` int(11) unsigned NOT NULL DEFAULT 0,
  `score_factors` varchar(50) NOT NULL DEFAULT '',
  `credit_type` tinyint(4) NOT NULL DEFAULT 0,
  `chargeback_id` int(11) NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL DEFAULT '',
  `amount` float(8,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `settled_amount` float(8,2) NOT NULL DEFAULT 0.00,
  `settled_currency` varchar(3) NOT NULL DEFAULT 'USD',
  `processor` varchar(12) NOT NULL DEFAULT '',
  `merchant_bank` varchar(20) NOT NULL DEFAULT '',
  `comments` text NOT NULL,
  `renewal` enum('Y','N') NOT NULL DEFAULT 'Y',
  `processor_request_id` varchar(50) NOT NULL DEFAULT '',
  `card_suffix` varchar(255) NOT NULL DEFAULT '',
  `click_check` varchar(20) NOT NULL DEFAULT '',
  `enc_cc_num` tinyblob NOT NULL,
  `enc_DDA` tinyblob NOT NULL,
  `TRN` varchar(9) NOT NULL DEFAULT '',
  `enc_cc_exp_mo` tinyblob NOT NULL,
  `enc_cc_exp_yr` tinyblob NOT NULL,
  `enc_card_suffix` tinyblob NOT NULL,
  `account_type` varchar(20) NOT NULL DEFAULT '',
  `BIN` varchar(9) NOT NULL DEFAULT '',
  `user_vip_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `paid_tokens` smallint(5) unsigned NOT NULL DEFAULT 0,
  `free_tokens` smallint(5) unsigned NOT NULL DEFAULT 0,
  `dhd_inv_id` int(9) unsigned NOT NULL DEFAULT 0,
  `dhd_cust_id` int(9) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_payment_id` int(11) DEFAULT 0,
  `distribution_group_id` tinyint(3) unsigned DEFAULT 0,
  `sequence_num` smallint(5) unsigned NOT NULL DEFAULT 0,
  `user_age` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) DEFAULT NULL,
  `3ds` tinyint(1) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `cc_num` (`cc_num`),
  KEY `product_code` (`product_code`),
  KEY `status` (`status`),
  KEY `avs_code` (`avs_code`),
  KEY `date_out` (`date_out`),
  KEY `credit_type` (`credit_type`),
  KEY `chargeback_id` (`chargeback_id`),
  KEY `email` (`email`),
  KEY `remote_ip` (`remote_ip`,`date_in`),
  KEY `click_check` (`click_check`),
  KEY `processor_status` (`processor`,`status`),
  KEY `enc_cc_num` (`enc_cc_num`(255)),
  KEY `enc_DDA` (`enc_DDA`(255),`TRN`),
  KEY `cc_exp_mo` (`cc_exp_mo`(2)),
  KEY `cc_exp_yr` (`cc_exp_yr`(4)),
  KEY `enc_cc_exp_mo` (`enc_cc_exp_mo`(64)),
  KEY `enc_cc_exp_yr` (`enc_cc_exp_yr`(64)),
  KEY `BIN` (`BIN`),
  KEY `card_suffix` (`card_suffix`),
  KEY `enc_card_suffix` (`enc_card_suffix`(128)),
  KEY `dhd_inv_id` (`dhd_inv_id`),
  KEY `dhd_cust_id` (`dhd_cust_id`),
  KEY `date_in` (`date_in`),
  KEY `user_id` (`user_id`),
  KEY `zip` (`zip`),
  KEY `type` (`type`),
  KEY `cascade_status` (`cascade_status`),
  KEY `user_payment_id` (`user_payment_id`),
  KEY `distribution_group_id` (`distribution_group_id`),
  KEY `ip_country` (`ip_country`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=73007573 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_3ds`
--

DROP TABLE IF EXISTS `transact_3ds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_3ds` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `3d_version` varchar(10) DEFAULT NULL,
  `cardinal_session_id` varchar(255) DEFAULT NULL,
  `step_up_entry` varchar(255) NOT NULL DEFAULT '',
  `pares` varchar(255) NOT NULL DEFAULT '',
  `ECI` varchar(3) DEFAULT NULL,
  `processor_id` varchar(12) DEFAULT NULL,
  `lookup_processor_inv_id` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `zip` varchar(15) NOT NULL DEFAULT '',
  `cc_num` varchar(20) DEFAULT NULL,
  `product_code` varchar(6) NOT NULL DEFAULT '',
  `remote_ip` varbinary(16) NOT NULL DEFAULT '',
  `ip_country` char(2) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(64) NOT NULL DEFAULT '',
  `amount` float(5,2) NOT NULL DEFAULT 0.00,
  `settled_amount` float(8,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT NULL,
  `distribution_group_id` tinyint(3) unsigned DEFAULT 0,
  `bin` varchar(9) NOT NULL DEFAULT '',
  `merchant_bank` varchar(20) DEFAULT NULL,
  `initial_response_code` varchar(32) DEFAULT '',
  `lookup_response_code` varchar(32) NOT NULL DEFAULT '',
  `error_code` varchar(5) DEFAULT NULL,
  `processed_as_3ds` tinyint(1) NOT NULL DEFAULT 0,
  `sitekey` varchar(20) DEFAULT NULL,
  `billing_ref_id` varchar(32) NOT NULL DEFAULT '',
  `billing_zone_id` smallint(6) NOT NULL DEFAULT 0,
  `experience_ref_id` varchar(32) NOT NULL DEFAULT '',
  `transact_log_id` int(10) unsigned DEFAULT NULL,
  `transact_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `datetime` (`datetime`),
  KEY `distribution_group_id` (`distribution_group_id`),
  KEY `ip_country` (`ip_country`),
  KEY `billing_ref_id` (`billing_ref_id`),
  KEY `billing_zone_id` (`billing_zone_id`),
  KEY `transact_id` (`transact_id`),
  KEY `transact_log_id` (`transact_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1769694 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_bonus`
--

DROP TABLE IF EXISTS `transact_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_bonus` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `seconds` smallint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_ex`
--

DROP TABLE IF EXISTS `transact_ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_ex` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `source_code` varchar(255) DEFAULT NULL,
  `salutation` varchar(50) DEFAULT NULL,
  `driverlicense` varchar(255) DEFAULT NULL,
  `driverlicense_state` varchar(255) DEFAULT NULL,
  `checknumber` varchar(255) DEFAULT NULL,
  `bday_month` tinyint(2) unsigned zerofill NOT NULL DEFAULT 00,
  `bday_day` tinyint(2) unsigned zerofill NOT NULL DEFAULT 00,
  `bday_year` smallint(4) unsigned zerofill NOT NULL DEFAULT 0000,
  `source_mp_code` varchar(5) DEFAULT NULL,
  `user_type` char(1) NOT NULL DEFAULT '1',
  `feature_show_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `batch_id` int(9) NOT NULL DEFAULT 0,
  `merch_code` varchar(5) NOT NULL DEFAULT '',
  `inv_reportdate` date NOT NULL DEFAULT '0000-00-00',
  `inv_amount` float(5,2) NOT NULL DEFAULT 0.00,
  `model_id` int(11) NOT NULL DEFAULT 0,
  `studio` char(12) DEFAULT NULL,
  `PA_ID` int(11) NOT NULL DEFAULT 0,
  `exported` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `domain` varchar(255) DEFAULT NULL,
  `user_mp_code` varchar(5) NOT NULL DEFAULT '',
  `show_activity_id` int(11) unsigned DEFAULT NULL,
  `descriptor` varchar(24) NOT NULL DEFAULT '',
  `processor_scrub_results` varchar(255) NOT NULL DEFAULT '',
  `billing_ref_id` varchar(32) NOT NULL DEFAULT '',
  `billing_zone_id` smallint(6) NOT NULL DEFAULT 0,
  `experience_ref_id` varchar(32) NOT NULL DEFAULT '',
  `experience_flow` varchar(64) NOT NULL DEFAULT '',
  `experience_trigger` varchar(64) NOT NULL DEFAULT '',
  `iovation_result` char(1) NOT NULL DEFAULT '',
  `iovation_reason` varchar(160) NOT NULL DEFAULT '',
  `iovation_rule_type` varchar(35) NOT NULL DEFAULT '',
  `iovation_rules_matched` smallint(4) unsigned NOT NULL DEFAULT 0,
  `cvv_flag` varchar(15) NOT NULL DEFAULT '',
  `cvv_response` char(1) NOT NULL DEFAULT '',
  `scrub_flag` varchar(15) NOT NULL DEFAULT '',
  `scrub_response` varchar(64) NOT NULL DEFAULT '',
  `last_engaged_mp_code` varchar(12) DEFAULT NULL,
  `last_engaged_days` smallint(5) unsigned DEFAULT NULL,
  `days_since_last_purchase` smallint(5) unsigned DEFAULT NULL,
  `first_engagement_purchase` enum('Y','N') NOT NULL DEFAULT 'N',
  `days_since_engagement_purchase` smallint(5) unsigned DEFAULT NULL,
  `bank_retrieval_id` varchar(20) DEFAULT NULL,
  `bank_reference_id` varchar(20) DEFAULT NULL,
  `service` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`transact_id`),
  KEY `feature_show_id` (`feature_show_id`),
  KEY `exported` (`exported`),
  KEY `show_activity_id` (`show_activity_id`),
  KEY `billing_ref_id` (`billing_ref_id`),
  KEY `billing_zone_id` (`billing_zone_id`),
  KEY `experience_flow` (`experience_flow`),
  KEY `experience_trigger` (`experience_trigger`),
  KEY `bank_retrieval_id` (`bank_retrieval_id`),
  KEY `bank_reference_id` (`bank_reference_id`),
  KEY `service` (`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_log`
--

DROP TABLE IF EXISTS `transact_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `user_id` int(10) unsigned DEFAULT NULL,
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `firstname` varchar(100) NOT NULL DEFAULT '',
  `lastname` varchar(100) NOT NULL DEFAULT '',
  `address` varchar(80) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `zip` varchar(15) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `short_cc_num` varchar(20) NOT NULL,
  `service` varchar(20) NOT NULL DEFAULT '',
  `product_code` varchar(6) NOT NULL DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `source_code` varchar(255) DEFAULT NULL,
  `status` enum('accepted','rejected','pending') NOT NULL DEFAULT 'rejected',
  `cascade_status` enum('initial','cascade') NOT NULL DEFAULT 'initial',
  `remote_ip` varbinary(16) NOT NULL DEFAULT '',
  `ip_country` char(2) NOT NULL DEFAULT '',
  `auth_code` varchar(8) NOT NULL DEFAULT '',
  `avs_flag` varchar(15) NOT NULL DEFAULT '',
  `avs_code` char(2) NOT NULL DEFAULT 'A',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `amount` float(5,2) NOT NULL DEFAULT 0.00,
  `settled_amount` float(8,2) DEFAULT 0.00,
  `settled_currency` varchar(3) DEFAULT 'USD',
  `user_payment_id` int(11) DEFAULT NULL,
  `distribution_group_id` tinyint(3) unsigned DEFAULT 0,
  `comments` text DEFAULT NULL,
  `account_type` varchar(20) NOT NULL DEFAULT '',
  `bin` varchar(9) NOT NULL DEFAULT '',
  `paid_tokens` smallint(5) DEFAULT 0,
  `free_tokens` smallint(5) DEFAULT 0,
  `processor` varchar(12) NOT NULL DEFAULT '',
  `merchant_bank` varchar(20) DEFAULT NULL,
  `processor_response_code` varchar(32) NOT NULL DEFAULT '',
  `processor_decline_msg` varchar(255) NOT NULL DEFAULT '',
  `network_response_code` varchar(32) NOT NULL DEFAULT '',
  `network_decline_msg` varchar(255) NOT NULL DEFAULT '',
  `bank_response_code` varchar(32) NOT NULL DEFAULT '',
  `bank_decline_msg` varchar(255) NOT NULL DEFAULT '',
  `cvv_flag` varchar(15) NOT NULL DEFAULT '',
  `cvv_response` char(1) NOT NULL DEFAULT '',
  `scrub_flag` varchar(15) NOT NULL DEFAULT '',
  `scrub_response` varchar(64) NOT NULL DEFAULT '',
  `processor_inv_id` varchar(255) DEFAULT NULL,
  `processor_cust_id` varchar(255) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `sitekey` varchar(20) DEFAULT NULL,
  `error_code` char(3) DEFAULT NULL,
  `form_type` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `source_site_id` smallint(4) unsigned DEFAULT 1,
  `billing_ref_id` varchar(32) NOT NULL DEFAULT '',
  `billing_zone_id` smallint(6) NOT NULL DEFAULT 0,
  `experience_ref_id` varchar(32) NOT NULL DEFAULT '',
  `3ds` tinyint(1) NOT NULL DEFAULT 0,
  `processor_request_id` varchar(50) NOT NULL DEFAULT '',
  `card_category` varchar(24) DEFAULT NULL,
  `type_of_card` varchar(24) DEFAULT NULL,
  `bin_country` char(2) DEFAULT NULL,
  `card_brand` varchar(24) DEFAULT NULL,
  `issuing_bank` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `datetime` (`datetime`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `billing_ref_id` (`billing_ref_id`),
  KEY `billing_zone_id` (`billing_zone_id`),
  KEY `cascade_status` (`cascade_status`),
  KEY `type` (`type`),
  KEY `user_payment_id` (`user_payment_id`),
  KEY `distribution_group_id` (`distribution_group_id`),
  KEY `ip_country` (`ip_country`)
) ENGINE=InnoDB AUTO_INCREMENT=30847399 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_log_user_env`
--

DROP TABLE IF EXISTS `transact_log_user_env`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_log_user_env` (
  `transact_log_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`transact_log_id`),
  KEY `user_platform_type` (`user_platform_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_play_and_pay`
--

DROP TABLE IF EXISTS `transact_play_and_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_play_and_pay` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `transact_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_type` varchar(20) NOT NULL DEFAULT '',
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `amount` float(6,2) NOT NULL DEFAULT 0.00,
  `amount_tax` float(6,2) NOT NULL DEFAULT 0.00,
  `num_credits` smallint(5) unsigned NOT NULL DEFAULT 0,
  `credits_per_dollar` float(5,3) DEFAULT 10.000,
  `num_process_attempts` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  KEY `transact_id` (`transact_id`),
  KEY `transact_date` (`transact_date`),
  KEY `user_id` (`user_id`),
  KEY `activity_type` (`activity_type`),
  KEY `activity_ref_id` (`activity_ref_id`),
  KEY `model_id` (`model_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_review_set`
--

DROP TABLE IF EXISTS `transact_review_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_review_set` (
  `transact_id` int(11) NOT NULL,
  `userset` set('anthonyl','bojana','walterg','miguel','laylaf','johnr','alexm','donald','osula','joeg','daniell') DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_risk_scores`
--

DROP TABLE IF EXISTS `transact_risk_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_risk_scores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transact_id` int(10) unsigned NOT NULL DEFAULT 0,
  `transact_date` date NOT NULL DEFAULT '0000-00-00',
  `data_type` varchar(20) NOT NULL DEFAULT '',
  `score_weight` tinyint(4) NOT NULL DEFAULT 1,
  `score_value` tinyint(4) NOT NULL DEFAULT 9,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transact_value` (`transact_id`,`data_type`),
  KEY `transact_id` (`transact_id`),
  KEY `transact_date` (`transact_date`)
) ENGINE=InnoDB AUTO_INCREMENT=117561482 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_tax`
--

DROP TABLE IF EXISTS `transact_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_tax` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `transact_date` float(13,3) NOT NULL DEFAULT 0.000,
  `transact_amount` float(6,2) NOT NULL DEFAULT 0.00,
  `tax_percent` float(4,2) NOT NULL DEFAULT 0.00,
  `tax_amount` float(6,2) NOT NULL DEFAULT 0.00,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `country` char(2) DEFAULT NULL,
  PRIMARY KEY (`transact_id`),
  KEY `transact_date` (`transact_date`),
  KEY `user_id` (`user_id`),
  KEY `country` (`country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_user_env`
--

DROP TABLE IF EXISTS `transact_user_env`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_user_env` (
  `transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transact_user_type`
--

DROP TABLE IF EXISTS `transact_user_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transact_user_type` (
  `user_type` char(1) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  UNIQUE KEY `user_type` (`user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ucs_mod_auth_group`
--

DROP TABLE IF EXISTS `ucs_mod_auth_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ucs_mod_auth_group` (
  `username` char(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `group_field` char(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`username`,`group_field`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ucs_mod_auth_password`
--

DROP TABLE IF EXISTS `ucs_mod_auth_password`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ucs_mod_auth_password` (
  `username` varchar(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(24) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `vendor_id` varchar(20) DEFAULT NULL,
  `auth_group` char(2) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unit_type`
--

DROP TABLE IF EXISTS `unit_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_account_adjustment_type`
--

DROP TABLE IF EXISTS `user_account_adjustment_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_account_adjustment_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  `activate_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deactivate_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `complimentary` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_comp_time`
--

DROP TABLE IF EXISTS `user_comp_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_comp_time` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `service` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `qty_in_seconds` int(10) unsigned NOT NULL DEFAULT 0,
  `comments` varchar(255) NOT NULL DEFAULT '',
  `cs_agent_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `approval_employee_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`username`,`service`),
  KEY `cs_agent_id` (`cs_agent_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_site`
--

DROP TABLE IF EXISTS `user_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `site_id` int(4) unsigned NOT NULL DEFAULT 0,
  `entered_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`username`,`site_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71575 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_status`
--

DROP TABLE IF EXISTS `user_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_status` (
  `status` char(1) NOT NULL,
  `short_description` varchar(15) NOT NULL,
  `description` varchar(255) NOT NULL,
  `auto_blacklist` enum('Y','N') NOT NULL DEFAULT 'N',
  `sort_order` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`status`),
  KEY `auto_blacklist` (`auto_blacklist`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_tests`
--

DROP TABLE IF EXISTS `user_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_tests` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `test_name` varchar(256) NOT NULL DEFAULT '',
  `test_value` varchar(256) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`,`test_name`),
  KEY `user_id` (`user_id`),
  KEY `test_name` (`test_name`),
  KEY `status` (`status`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_vip`
--

DROP TABLE IF EXISTS `user_vip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_vip` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `consumer_product_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `bonus_percentage` float(3,2) NOT NULL DEFAULT 0.00,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vc_last_run`
--

DROP TABLE IF EXISTS `vc_last_run`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vc_last_run` (
  `last_run` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`last_run`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vc_last_run_dev`
--

DROP TABLE IF EXISTS `vc_last_run_dev`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vc_last_run_dev` (
  `last_run` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`last_run`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vc_room_users`
--

DROP TABLE IF EXISTS `vc_room_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vc_room_users` (
  `room` varchar(40) NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`room`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vc_user_time`
--

DROP TABLE IF EXISTS `vc_user_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vc_user_time` (
  `username` varchar(12) NOT NULL DEFAULT '',
  `room` varchar(12) NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`username`,`room`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vc_users`
--

DROP TABLE IF EXISTS `vc_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vc_users` (
  `username` varchar(12) NOT NULL DEFAULT '',
  `password` varchar(12) NOT NULL DEFAULT '',
  `timeleft` int(11) NOT NULL DEFAULT 0,
  `service` enum('20','1','2','3','4','5','6') NOT NULL DEFAULT '20',
  PRIMARY KEY (`username`,`password`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vs2pc`
--

DROP TABLE IF EXISTS `vs2pc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vs2pc` (
  `co_code_str` char(3) NOT NULL DEFAULT '',
  `product_code` varchar(6) NOT NULL DEFAULT '',
  `theme_str` varchar(11) NOT NULL DEFAULT '',
  `product_id_str` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vs_msg`
--

DROP TABLE IF EXISTS `vs_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vs_msg` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `message` varchar(60) DEFAULT NULL,
  `html_message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=408 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vs_msg_bk`
--

DROP TABLE IF EXISTS `vs_msg_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vs_msg_bk` (
  `id` char(3) DEFAULT NULL,
  `message` varchar(60) DEFAULT NULL,
  `html_message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vs_msg_translations`
--

DROP TABLE IF EXISTS `vs_msg_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vs_msg_translations` (
  `error_message_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `language` char(2) NOT NULL DEFAULT '',
  `message` varchar(60) NOT NULL,
  `html_message` text NOT NULL,
  PRIMARY KEY (`error_message_id`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webtalk`
--

DROP TABLE IF EXISTS `webtalk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `webtalk` (
  `ticket_number` varchar(10) NOT NULL DEFAULT '',
  `username` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `date_in` datetime DEFAULT NULL,
  `date_out` datetime DEFAULT NULL,
  `the_url_address` varchar(255) DEFAULT NULL,
  `studio_name` varchar(255) DEFAULT NULL,
  `model_name` varchar(255) DEFAULT NULL,
  `room_number` varchar(255) DEFAULT NULL,
  `date_time_of_incident` varchar(255) DEFAULT NULL,
  `remote_ip` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `it_manager` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `response_comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ticket_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webtalk_group`
--

DROP TABLE IF EXISTS `webtalk_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `webtalk_group` (
  `username` varchar(50) NOT NULL DEFAULT '',
  `webgroup` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workload`
--

DROP TABLE IF EXISTS `workload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `workload` (
  `item` varchar(50) NOT NULL DEFAULT '',
  `num_pending` mediumint(6) NOT NULL DEFAULT 0,
  `oldest_item` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `next_item_key` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`item`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wsb_transact`
--

DROP TABLE IF EXISTS `wsb_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wsb_transact` (
  `transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cvv2_resp` char(2) DEFAULT NULL,
  `EndUserID` int(11) DEFAULT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `xvt_users`
--

DROP TABLE IF EXISTS `xvt_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `xvt_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rv_id` int(11) unsigned NOT NULL DEFAULT 0,
  `rv1_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `xv_user_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `rv1_id` (`rv1_id`),
  KEY `user_id` (`user_id`),
  KEY `xv_user_id` (`xv_user_id`),
  KEY `rv_id` (`rv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13741945 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ziplist5`
--

DROP TABLE IF EXISTS `ziplist5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ziplist5` (
  `city` varchar(28) NOT NULL DEFAULT '',
  `state` char(2) NOT NULL DEFAULT '',
  `zip` mediumint(5) unsigned zerofill NOT NULL DEFAULT 00000,
  `area_code` smallint(3) unsigned zerofill NOT NULL DEFAULT 000,
  `fips` mediumint(5) unsigned zerofill NOT NULL DEFAULT 00000,
  `county` varchar(25) NOT NULL DEFAULT '',
  `preferred` char(1) NOT NULL DEFAULT '',
  `time_zone` varchar(5) NOT NULL DEFAULT '',
  `dst` char(1) NOT NULL DEFAULT '',
  `zip_type` char(1) NOT NULL DEFAULT '',
  KEY `zip` (`zip`),
  KEY `area_code` (`area_code`),
  KEY `city` (`city`),
  KEY `state` (`state`)
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

-- Dump completed on 2025-10-24 17:51:51
