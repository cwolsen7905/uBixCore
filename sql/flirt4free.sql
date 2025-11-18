/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: flirt4free
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
-- Table structure for table `2011_Annual_Survey`
--

DROP TABLE IF EXISTS `2011_Annual_Survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2011_Annual_Survey` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `rate_cs` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_video_quality` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_selection` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_chat` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_vod` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_vip` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `text_likes` text DEFAULT NULL,
  `text_dislikes` text DEFAULT NULL,
  `text_new_features` text DEFAULT NULL,
  `text_comments` text DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `2014_Annual_Survey`
--

DROP TABLE IF EXISTS `2014_Annual_Survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2014_Annual_Survey` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `rate_cs` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_video_quality` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_audio_quality` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_selection` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_attentiveness` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_chat` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_ease_of_use` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_vod` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_vip` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_pricing` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `rate_overall` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `text_likes` text DEFAULT NULL,
  `text_dislikes` text DEFAULT NULL,
  `text_new_features` text DEFAULT NULL,
  `text_comments` text DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `2015_Dopamine_Survey`
--

DROP TABLE IF EXISTS `2015_Dopamine_Survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2015_Dopamine_Survey` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `cohort` varchar(20) NOT NULL DEFAULT 'no_spending',
  `total_spent` float(10,2) NOT NULL DEFAULT 0.00,
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `http_host` varchar(255) NOT NULL DEFAULT '',
  `answer_more_questions` enum('Y','N') NOT NULL DEFAULT 'N',
  `primary_service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `text_best_things` text DEFAULT NULL,
  `text_favorite_free` text DEFAULT NULL,
  `text_competitive` text DEFAULT NULL,
  `text_other_sites` text DEFAULT NULL,
  `text_powerboost` text DEFAULT NULL,
  `text_refill` text DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `2017_Annual_Survey`
--

DROP TABLE IF EXISTS `2017_Annual_Survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2017_Annual_Survey` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `country_code` varchar(3) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `interest_access` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_party_chat` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_recorded` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_group_chat` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_reward_prgm` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_knowing_perf` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_smart_device` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_social_media` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_vip` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_hq_video` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_fanclubs` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_cam2cam` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_cs` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_diversity` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_playback` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_payment` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_live_video` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_cam2cam` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `yesno_show` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `yesno_purchase` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `yesno_playin_vod` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `yesno_send_tip` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `yesno_accessing_website` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `text_problem` text DEFAULT NULL,
  `text_comments` text DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `2019_Annual_Survey`
--

DROP TABLE IF EXISTS `2019_Annual_Survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2019_Annual_Survey` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `country_code` varchar(3) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `interest_knowing_perf` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_vip` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_fanclubs` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_recorded` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `interest_smart_device` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_cs` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_diversity` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_playback` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_live_video` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfcation_access` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_shows` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_payment` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_emails` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_usability` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `satisfaction_connectivity` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `text_comments` text DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `2257_Auth`
--

DROP TABLE IF EXISTS `2257_Auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2257_Auth` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(12) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00',
  `auth_level` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `created_by` smallint(3) unsigned NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `reset_flag` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `username_2` (`username`,`password`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `2257_Log`
--

DROP TABLE IF EXISTS `2257_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2257_Log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `auth_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `auth_level` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `ip` varbinary(16) NOT NULL DEFAULT '',
  `log_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `document_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103192 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `2257_Requests`
--

DROP TABLE IF EXISTS `2257_Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `2257_Requests` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `comments` text DEFAULT NULL,
  `model_id` int(11) unsigned DEFAULT NULL,
  `request_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `API_Accounts`
--

DROP TABLE IF EXISTS `API_Accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `API_Accounts` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) NOT NULL DEFAULT '',
  `optiuser_id` int(10) unsigned NOT NULL DEFAULT 0,
  `affiliate_id` mediumint(8) unsigned DEFAULT NULL,
  `api_key` varchar(512) NOT NULL DEFAULT '',
  `min_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `min_credits_threshold` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `suffix` varchar(8) NOT NULL DEFAULT '',
  `active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `api_domain` varchar(64) NOT NULL DEFAULT '',
  `message_postback_url` varchar(256) DEFAULT NULL,
  `show_logout_postback_url` varchar(256) DEFAULT NULL,
  `customer_boot_postback_url` varchar(256) DEFAULT NULL,
  `performer_status_postback_url` varchar(256) DEFAULT NULL,
  `test_model_id_1` int(11) unsigned NOT NULL DEFAULT 0,
  `test_model_id_2` int(11) unsigned NOT NULL DEFAULT 0,
  `path_limit_performers` int(11) NOT NULL DEFAULT 250000,
  `path_limit_performer_info` int(11) NOT NULL DEFAULT 250000,
  `path_limit_message` int(11) NOT NULL DEFAULT 250000,
  `path_limit_customer` int(11) NOT NULL DEFAULT 1000000,
  `path_limit_tip` int(11) NOT NULL DEFAULT -1,
  `path_limit_show` int(11) NOT NULL DEFAULT -1,
  `allow_party` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `API_Deposits`
--

DROP TABLE IF EXISTS `API_Deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `API_Deposits` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payment_type` enum('deposit','line_of_credit','paid_invoice') NOT NULL DEFAULT 'deposit',
  `deposit_amount` decimal(9,2) NOT NULL DEFAULT 0.00,
  `retail_rate` float(3,2) unsigned NOT NULL DEFAULT 0.00,
  `wholesale_discount` float(3,2) unsigned NOT NULL DEFAULT 0.00,
  `credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `invoice` varchar(15) NOT NULL DEFAULT '',
  `last_updated` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=460 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `API_Model_Opt_Out`
--

DROP TABLE IF EXISTS `API_Model_Opt_Out`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `API_Model_Opt_Out` (
  `account_id` tinyint(3) unsigned NOT NULL,
  `studio_code` char(12) NOT NULL,
  `model_id` int(11) NOT NULL,
  PRIMARY KEY (`account_id`,`studio_code`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `API_Request_Counts`
--

DROP TABLE IF EXISTS `API_Request_Counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `API_Request_Counts` (
  `account_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `date_requested` date NOT NULL DEFAULT '0000-00-00',
  `uri` varchar(64) NOT NULL DEFAULT '',
  `num_requests` mediumint(9) NOT NULL DEFAULT 0,
  PRIMARY KEY (`account_id`,`date_requested`,`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `API_Request_Log`
--

DROP TABLE IF EXISTS `API_Request_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `API_Request_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) NOT NULL DEFAULT '',
  `partner_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `method` varchar(80) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `response_code` varchar(255) NOT NULL DEFAULT '',
  `date_logged` date NOT NULL DEFAULT '0000-00-00',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_datetime` (`user_id`,`date_time`),
  KEY `partner_datetime_method` (`partner_id`,`date_time`,`method`),
  KEY `date_method` (`date_logged`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `API_Transactions`
--

DROP TABLE IF EXISTS `API_Transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `API_Transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `username` varchar(32) NOT NULL DEFAULT '',
  `customer_id` varchar(32) NOT NULL DEFAULT '',
  `activity` varchar(18) NOT NULL DEFAULT '',
  `activity_id` int(10) unsigned NOT NULL DEFAULT 0,
  `credits` mediumint(9) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `account_id` (`account_id`),
  KEY `customer_id` (`customer_id`),
  KEY `activity_search` (`activity`(1),`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=582273 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `API_Transactions_Negative`
--

DROP TABLE IF EXISTS `API_Transactions_Negative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `API_Transactions_Negative` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pay_period_id` int(10) unsigned NOT NULL DEFAULT 0,
  `transaction_id` int(10) unsigned NOT NULL,
  `refund_reason` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id_UNIQUE` (`transaction_id`)
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
  `service` enum('girls','guys','trans','all') NOT NULL DEFAULT 'girls',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_description` text DEFAULT NULL,
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`service`)
) ENGINE=InnoDB AUTO_INCREMENT=10024 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Blog_Posts`
--

DROP TABLE IF EXISTS `Blog_Posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blog_Posts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) DEFAULT 0,
  `author` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `contents` longtext NOT NULL,
  `post_slug` varchar(100) NOT NULL DEFAULT '',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_status` enum('draft','publish','review','inactive') NOT NULL DEFAULT 'draft',
  `comment_status` enum('open','closed','registered_only') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`),
  KEY `status` (`post_status`),
  KEY `post_slug` (`post_slug`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=11468 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
-- Table structure for table `Blog_Slugs`
--

DROP TABLE IF EXISTS `Blog_Slugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blog_Slugs` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `date_published` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','rejected','sent') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `date_published` (`date_published`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=688 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Booted_Users`
--

DROP TABLE IF EXISTS `Booted_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Booted_Users` (
  `screen_name_lower` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `to_delete` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`screen_name_lower`),
  UNIQUE KEY `screen_name_lower` (`screen_name_lower`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Booted_Users_Log`
--

DROP TABLE IF EXISTS `Booted_Users_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Booted_Users_Log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT '',
  `total` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130266 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Brand_Studio_Categories`
--

DROP TABLE IF EXISTS `Brand_Studio_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Brand_Studio_Categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_category_id_studio` (`category_id`,`studio`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Butter_Cms_Blog_Comments`
--

DROP TABLE IF EXISTS `Butter_Cms_Blog_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Butter_Cms_Blog_Comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` int(10) unsigned NOT NULL,
  `reply_to_comment_id` int(10) unsigned NOT NULL,
  `message` varchar(300) NOT NULL,
  `author_name` varchar(20) NOT NULL,
  `author_user_id` int(10) unsigned NOT NULL,
  `author_ip` int(15) unsigned NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(191) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=56042 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Butter_Cms_Blogs`
--

DROP TABLE IF EXISTS `Butter_Cms_Blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Butter_Cms_Blogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `status` varchar(191) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2716 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CSAPI_Blocked_Users`
--

DROP TABLE IF EXISTS `CSAPI_Blocked_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CSAPI_Blocked_Users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `customer_id` int(10) unsigned NOT NULL DEFAULT 0,
  `partner_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','blocked') NOT NULL DEFAULT 'blocked',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `nickname_customer_status` (`nickname`,`customer_id`,`status`),
  KEY `customer_partner` (`customer_id`,`partner_id`),
  KEY `customer_datetime` (`customer_id`,`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cam_Model_Contest`
--

DROP TABLE IF EXISTS `Cam_Model_Contest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cam_Model_Contest` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `date_end` date NOT NULL DEFAULT '0000-00-00',
  `total_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `group_name` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`model_id`,`service`,`date_end`),
  KEY `total_credits` (`total_credits`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Click_ID_Log`
--

DROP TABLE IF EXISTS `Click_ID_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Click_ID_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `registration_view_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_oneclick` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `click_id` text NOT NULL,
  `external_click_id` varchar(512) DEFAULT NULL,
  `external_click_id_type` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `pb_cc` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `reg_view_id` (`registration_view_id`),
  KEY `is_oneclick` (`is_oneclick`),
  KEY `click_id` (`click_id`(767)),
  KEY `external_click` (`external_click_id`,`external_click_id_type`)
) ENGINE=InnoDB AUTO_INCREMENT=21908424 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Contest_2010`
--

DROP TABLE IF EXISTS `Contest_2010`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contest_2010` (
  `model_id` int(11) NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `final_points` smallint(2) unsigned NOT NULL DEFAULT 0,
  `final_position` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `current_points` smallint(2) unsigned NOT NULL DEFAULT 0,
  `current_position` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Contest_2010_Points`
--

DROP TABLE IF EXISTS `Contest_2010_Points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contest_2010_Points` (
  `model_id` int(11) NOT NULL DEFAULT 0,
  `points` smallint(2) NOT NULL DEFAULT 0,
  `type` enum('total_credits','consistancy','hall_of_fame') NOT NULL DEFAULT 'total_credits',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`model_id`,`type`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Challenge_Goals`
--

DROP TABLE IF EXISTS `Customer_Challenge_Goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Challenge_Goals` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `alpha_1` decimal(24,22) NOT NULL DEFAULT 0.0000000000000000000000,
  `alpha_2` decimal(24,22) NOT NULL DEFAULT 0.0000000000000000000000,
  `alpha_3` decimal(24,22) NOT NULL DEFAULT 0.0000000000000000000000,
  `y_intercept_1` decimal(18,15) NOT NULL DEFAULT 0.000000000000000,
  `y_intercept_2` decimal(18,15) NOT NULL DEFAULT 0.000000000000000,
  `round_to` int(10) unsigned NOT NULL DEFAULT 0,
  `adjustment` decimal(18,16) NOT NULL DEFAULT 0.0000000000000000,
  `reward_coefficient` float(5,4) NOT NULL DEFAULT 0.0000,
  `beta_1` decimal(18,16) NOT NULL DEFAULT 0.0000000000000000,
  `regression_percentile` int(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `sitekey` (`sitekey`),
  KEY `start_datetime` (`start_datetime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Challenge_Users`
--

DROP TABLE IF EXISTS `Customer_Challenge_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Challenge_Users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `challenge_id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `group` varchar(32) NOT NULL DEFAULT '',
  `inserted_datetime` datetime DEFAULT NULL,
  `last_presented_datetime` datetime DEFAULT NULL,
  `accepted_datetime` datetime DEFAULT NULL,
  `completed_datetime` datetime DEFAULT NULL,
  `was_notified_datetime` datetime DEFAULT NULL,
  `challenge_target` int(10) unsigned NOT NULL DEFAULT 0,
  `reward_amount` int(10) unsigned NOT NULL DEFAULT 0,
  `reward_given` int(10) unsigned NOT NULL DEFAULT 0,
  `reward_given_datetime` datetime DEFAULT NULL,
  `processed` tinyint(3) unsigned DEFAULT 0,
  `credits_spent_in_challenge` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_challenge_id` (`user_id`,`challenge_id`),
  KEY `group_challenge_id` (`group`,`challenge_id`),
  KEY `inserted_datetime` (`inserted_datetime`),
  KEY `accepted_datetime` (`accepted_datetime`),
  KEY `completed_datetime` (`completed_datetime`),
  KEY `was_notified_datetime` (`was_notified_datetime`),
  KEY `reward_given_datetime` (`reward_given_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=39294 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Environment_Vars`
--

DROP TABLE IF EXISTS `Customer_Environment_Vars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Environment_Vars` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `variable_name` varchar(128) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `req_type` enum('boolean','integer','double','string','array','object') DEFAULT NULL,
  `default_value` varchar(128) DEFAULT NULL,
  `is_cookie` enum('true','false') NOT NULL,
  `is_preference` enum('true','false') NOT NULL,
  `is_session` enum('true','false') NOT NULL,
  `ttl` int(10) unsigned NOT NULL DEFAULT 0,
  `required` enum('true','false') NOT NULL DEFAULT 'false',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `site_key` varchar(16) NOT NULL DEFAULT 'flirt4free',
  `core` tinyint(1) NOT NULL DEFAULT 0,
  `is_functionality` enum('true','false') NOT NULL,
  `is_performance` enum('true','false') NOT NULL,
  `is_stats` enum('true','false') NOT NULL,
  `cookie_policy_category` enum('necessary','functional','analytics','performance','advertisement','other') DEFAULT 'other',
  PRIMARY KEY (`id`),
  UNIQUE KEY `variable_name` (`variable_name`,`site_key`),
  KEY `active` (`active`),
  KEY `site_key` (`site_key`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Poll_Answers`
--

DROP TABLE IF EXISTS `Customer_Poll_Answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Poll_Answers` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `answer` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `poll` (`poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Poll_Questions`
--

DROP TABLE IF EXISTS `Customer_Poll_Questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Poll_Questions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `service` varchar(5) NOT NULL DEFAULT 'girls',
  `zone` varchar(32) NOT NULL DEFAULT 'any',
  `datetime_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `service_zone` (`service`,`zone`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Poll_Results`
--

DROP TABLE IF EXISTS `Customer_Poll_Results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Poll_Results` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `poll_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `answer_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`,`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Social_Media`
--

DROP TABLE IF EXISTS `Customer_Social_Media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Social_Media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `social_media_id` smallint(2) unsigned NOT NULL DEFAULT 0,
  `username` varchar(64) NOT NULL DEFAULT '',
  `screen_name` varchar(32) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `username` (`username`),
  KEY `social_media_id` (`social_media_id`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=39623 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Email_Collection`
--

DROP TABLE IF EXISTS `Email_Collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Email_Collection` (
  `email` varchar(255) NOT NULL DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `campaign_id` smallint(5) unsigned NOT NULL DEFAULT 2,
  `stamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`email`,`stamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Email_User_Live_Models`
--

DROP TABLE IF EXISTS `Email_User_Live_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Email_User_Live_Models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `models` varchar(64) NOT NULL DEFAULT '',
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=5215 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Engagement_Log`
--

DROP TABLE IF EXISTS `Engagement_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Engagement_Log` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_type` enum('guest','basic','premium','vip') NOT NULL DEFAULT 'guest',
  `action_trigger` varchar(30) NOT NULL DEFAULT '',
  `offer_identifier` varchar(30) NOT NULL DEFAULT '',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `sequence_num` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `success_id` int(11) unsigned NOT NULL DEFAULT 0,
  KEY `user_id` (`user_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ`
--

DROP TABLE IF EXISTS `FAQ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FAQ` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` smallint(3) unsigned NOT NULL DEFAULT 101,
  `question` varchar(150) NOT NULL DEFAULT '',
  `answer` text NOT NULL,
  `votes_good` smallint(5) unsigned NOT NULL DEFAULT 0,
  `votes_total` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `translation_required` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `question` (`question`)
) ENGINE=InnoDB AUTO_INCREMENT=469 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ_Categories`
--

DROP TABLE IF EXISTS `FAQ_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FAQ_Categories` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `is_vip_only` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `translation_required` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ_Categories_Translations`
--

DROP TABLE IF EXISTS `FAQ_Categories_Translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FAQ_Categories_Translations` (
  `category_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `language` char(2) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`category_id`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ_Translations`
--

DROP TABLE IF EXISTS `FAQ_Translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FAQ_Translations` (
  `faq_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `language` char(2) NOT NULL DEFAULT '',
  `question` varchar(150) NOT NULL DEFAULT '',
  `answer` text NOT NULL,
  PRIMARY KEY (`faq_id`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Failed_Login_Attempts`
--

DROP TABLE IF EXISTS `Failed_Login_Attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Failed_Login_Attempts` (
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ipaddress` varchar(15) NOT NULL DEFAULT '',
  `program` enum('live','vip') NOT NULL DEFAULT 'live',
  PRIMARY KEY (`optiusers_id`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fantasies_Explored_Daily`
--

DROP TABLE IF EXISTS `Fantasies_Explored_Daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fantasies_Explored_Daily` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `created_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `fantasy_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(250) NOT NULL,
  `description` mediumtext NOT NULL DEFAULT '',
  `vod_category` varchar(50) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL,
  `admin_id` smallint(3) unsigned DEFAULT NULL,
  `encoding_status` varchar(50) NOT NULL DEFAULT '',
  `stream_url` varchar(120) NOT NULL DEFAULT '',
  `thumbnail_url` varchar(120) NOT NULL DEFAULT '',
  `service` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fantasy_datetime` (`fantasy_datetime`),
  KEY `created_datetime` (`created_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=744 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Favorites`
--

DROP TABLE IF EXISTS `Favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Favorites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `item_id` int(6) unsigned NOT NULL DEFAULT 0,
  `name` varchar(50) DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `username` (`username`),
  KEY `type` (`type`,`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49635311 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Favorites_Models`
--

DROP TABLE IF EXISTS `Favorites_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Favorites_Models` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_added` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_model` (`user_id`,`model_id`),
  KEY `model_id` (`model_id`),
  KEY `date_added` (`date_added`)
) ENGINE=InnoDB AUTO_INCREMENT=87763932 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Feature_Test_Event_Log`
--

DROP TABLE IF EXISTS `Feature_Test_Event_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feature_Test_Event_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `feature_test_group_id` smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'external key for flirt4free.Feature_Test_Groups',
  `event_type` varchar(10) NOT NULL DEFAULT '' COMMENT 'specific to test and logged event',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'null if guest user',
  `guest_id` varchar(20) DEFAULT NULL COMMENT 'guest user identification marker',
  `feature_state` varchar(3) NOT NULL DEFAULT '' COMMENT 'version or active flag',
  `event_value` mediumint(7) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `feature_state` (`feature_test_group_id`,`feature_state`),
  KEY `feature_event_state` (`feature_test_group_id`,`event_type`,`feature_state`)
) ENGINE=InnoDB AUTO_INCREMENT=330748 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Feature_Test_Groups`
--

DROP TABLE IF EXISTS `Feature_Test_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feature_Test_Groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `feature` varchar(20) NOT NULL COMMENT 'feature label',
  `sitekey` varchar(20) NOT NULL DEFAULT '' COMMENT 'Empty for all, or sitekey flirt4free|whitelabel|xvc|xvt',
  `domain` varchar(255) NOT NULL DEFAULT '' COMMENT 'Empty for all, given if test restricted to being active on this domain (or multiple rows)',
  `user_type` varchar(10) NOT NULL DEFAULT '' COMMENT 'Empty for all, VIP|Premium|Basic|Guest if we want to limit it',
  `feature_state` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'The state or default of the feature for this group',
  `group_column` varchar(20) NOT NULL DEFAULT '' COMMENT 'the RV field to record group value in',
  `group_marker` varchar(20) NOT NULL COMMENT 'test group label - and marker for UDF fields',
  `split` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '0 to 100, integer percent of users to bucket in this group',
  `internal_only` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'Set to N when ready to be live for All users',
  `start_datetime` datetime DEFAULT NULL,
  `bucket_end_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feature_sitekey_domain_group` (`feature`,`sitekey`,`domain`,`user_type`,`group_marker`),
  KEY `feature_group` (`feature`,`group_marker`),
  KEY `sitekey_domain` (`sitekey`,`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Challenge_Levels_Completed`
--

DROP TABLE IF EXISTS `Flirt_Challenge_Levels_Completed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Challenge_Levels_Completed` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT 0,
  `level_id` int(10) NOT NULL DEFAULT 0,
  `challenge_id` int(10) NOT NULL DEFAULT 0,
  `level_reward_claimed` tinyint(4) NOT NULL DEFAULT 0,
  `claimed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `level_completed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `user_level_challenge` (`user_id`,`level_id`,`challenge_id`),
  KEY `challenge_id` (`challenge_id`),
  KEY `level_id` (`level_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57369 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Challenge_Missions`
--

DROP TABLE IF EXISTS `Flirt_Challenge_Missions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Challenge_Missions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `challenge_id` int(10) NOT NULL DEFAULT 0,
  `title` varchar(90) NOT NULL DEFAULT '',
  `description` varchar(240) NOT NULL DEFAULT '',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `badge` varchar(45) NOT NULL DEFAULT '',
  `level_id` int(10) NOT NULL DEFAULT 0,
  `mission_check_fn` varchar(45) NOT NULL DEFAULT '',
  `required_amount` tinyint(2) NOT NULL DEFAULT 0,
  `last_checked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `challenge_id` (`challenge_id`),
  KEY `level_id` (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Challenge_Missions_Completed`
--

DROP TABLE IF EXISTS `Flirt_Challenge_Missions_Completed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Challenge_Missions_Completed` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT 0,
  `mission_id` int(10) NOT NULL DEFAULT 0,
  `level_id` int(10) NOT NULL DEFAULT 0,
  `challenge_id` int(10) NOT NULL DEFAULT 0,
  `completed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `user_mission_challenge` (`user_id`,`mission_id`,`challenge_id`),
  KEY `user_id` (`user_id`),
  KEY `challenge_id` (`challenge_id`),
  KEY `level_id` (`level_id`),
  KEY `mission_id` (`mission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1920062 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Challenges`
--

DROP TABLE IF EXISTS `Flirt_Challenges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Challenges` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `badge` varchar(45) NOT NULL DEFAULT '',
  `grace_period` tinyint(3) NOT NULL DEFAULT 0,
  `required_amount` tinyint(3) NOT NULL DEFAULT 0,
  `num_days` int(3) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Gamma_Users`
--

DROP TABLE IF EXISTS `Gamma_Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Gamma_Users` (
  `reg_view_id` int(11) unsigned NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(16) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reg_view_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hashtag_Categories`
--

DROP TABLE IF EXISTS `Hashtag_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hashtag_Categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hashtags`
--

DROP TABLE IF EXISTS `Hashtags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hashtags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `type` enum('vod','model') NOT NULL DEFAULT 'model',
  `service` enum('guys','girls','trans') DEFAULT NULL,
  `primary_tag_id` int(10) unsigned DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`type`),
  KEY `primary_tag_id` (`primary_tag_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `Hashtags_ibfk_1` FOREIGN KEY (`primary_tag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Hashtags_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `Hashtag_Categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=656 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hot_Summer_AllStars_Leaderboard`
--

DROP TABLE IF EXISTS `Hot_Summer_AllStars_Leaderboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hot_Summer_AllStars_Leaderboard` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `year` year(4) DEFAULT NULL,
  `quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `quantity_type` enum('votes','credits') NOT NULL DEFAULT 'credits',
  `rank` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `phase` enum('voting','top100','top50','top25','top10') DEFAULT NULL,
  `seconds_online` mediumint(7) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `KEY` (`model_id`,`year`,`phase`,`service`)
) ENGINE=InnoDB AUTO_INCREMENT=35448805 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Listmania`
--

DROP TABLE IF EXISTS `Listmania`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Listmania` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `slug` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `screen_name` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','pending','inactive') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `service` (`service`,`status`),
  KEY `user_id` (`user_id`),
  KEY `screen_name` (`screen_name`)
) ENGINE=InnoDB AUTO_INCREMENT=19982 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Listmania_Items`
--

DROP TABLE IF EXISTS `Listmania_Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Listmania_Items` (
  `list_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `item_id` int(11) unsigned NOT NULL DEFAULT 0,
  `item_type` varchar(20) NOT NULL DEFAULT 'model',
  `ranking` smallint(3) unsigned NOT NULL DEFAULT 0,
  `comments` varchar(255) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `list_id_2` (`list_id`,`item_id`,`item_type`),
  KEY `list_id` (`list_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Local_Pages`
--

DROP TABLE IF EXISTS `Local_Pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Local_Pages` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `script_name` varchar(50) NOT NULL DEFAULT '',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_description` text NOT NULL,
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `search_strings` varchar(100) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  UNIQUE KEY `script_name` (`script_name`)
) ENGINE=InnoDB AUTO_INCREMENT=100014 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Login_Lookups`
--

DROP TABLE IF EXISTS `Login_Lookups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Login_Lookups` (
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `search_type` varchar(30) NOT NULL DEFAULT '',
  `search_query` varchar(255) NOT NULL DEFAULT '',
  `result_set` text DEFAULT NULL,
  `result_email` varchar(255) DEFAULT NULL,
  KEY `sitekey` (`sitekey`,`datetime`,`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_0`
--

DROP TABLE IF EXISTS `Model_Hashtags_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_0` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_0_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_0_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=547312 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_1`
--

DROP TABLE IF EXISTS `Model_Hashtags_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_1_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_1_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=555286 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_2`
--

DROP TABLE IF EXISTS `Model_Hashtags_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_2_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_2_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=539681 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_3`
--

DROP TABLE IF EXISTS `Model_Hashtags_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_3` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_3_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_3_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=545671 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_4`
--

DROP TABLE IF EXISTS `Model_Hashtags_4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_4` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_4_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_4_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=552012 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_5`
--

DROP TABLE IF EXISTS `Model_Hashtags_5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_5` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_5_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_5_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=522398 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_6`
--

DROP TABLE IF EXISTS `Model_Hashtags_6`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_6` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_6_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_6_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=545612 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_7`
--

DROP TABLE IF EXISTS `Model_Hashtags_7`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_7` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_7_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_7_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=548750 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_8`
--

DROP TABLE IF EXISTS `Model_Hashtags_8`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_8` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_8_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_8_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=550094 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Hashtags_9`
--

DROP TABLE IF EXISTS `Model_Hashtags_9`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Hashtags_9` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `hashtag_id` int(10) unsigned NOT NULL,
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_date` datetime DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned DEFAULT NULL,
  `review_comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model_id` (`model_id`,`hashtag_id`),
  KEY `hashtag_id` (`hashtag_id`),
  KEY `review_admin_id` (`review_admin_id`),
  CONSTRAINT `Model_Hashtags_9_ibfk_1` FOREIGN KEY (`hashtag_id`) REFERENCES `Hashtags` (`id`),
  CONSTRAINT `Model_Hashtags_9_ibfk_2` FOREIGN KEY (`review_admin_id`) REFERENCES `VSCASH`.`Admin_Users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=548927 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Motorbunny_Performers`
--

DROP TABLE IF EXISTS `Motorbunny_Performers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Motorbunny_Performers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `model_id` int(12) NOT NULL DEFAULT 0,
  `link_token` varchar(4098) NOT NULL DEFAULT '',
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `service` varchar(10) NOT NULL DEFAULT '',
  `share_token` varchar(10) DEFAULT NULL,
  `datetime_authorized` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `link_token` (`link_token`(3072))
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Notification_Join`
--

DROP TABLE IF EXISTS `Notification_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Notification_Join` (
  `profile_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `alert_type` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`profile_id`,`model_id`,`alert_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Notification_Log`
--

DROP TABLE IF EXISTS `Notification_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Notification_Log` (
  `profile_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `alert_type` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `notification_id` (`profile_id`,`model_id`,`alert_type`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Peekshows_Spender_Data`
--

DROP TABLE IF EXISTS `Peekshows_Spender_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Peekshows_Spender_Data` (
  `hash` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `spender` enum('Y','N') NOT NULL DEFAULT 'N',
  `total_spent` float(10,2) unsigned NOT NULL DEFAULT 0.00,
  `found` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Pending_Shows_Tipping`
--

DROP TABLE IF EXISTS `Pending_Shows_Tipping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Pending_Shows_Tipping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pending_show_id` mediumint(5) unsigned NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `use_custom_image` tinyint(4) NOT NULL DEFAULT 0,
  `date_last_updated` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `show_model` (`pending_show_id`,`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Asset_Attributes`
--

DROP TABLE IF EXISTS `Performer_Asset_Attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Asset_Attributes` (
  `attribute_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_type` enum('pose','visual') NOT NULL DEFAULT 'visual',
  `service` enum('girls','guys','all') NOT NULL DEFAULT 'all',
  `name` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`attribute_id`),
  UNIQUE KEY `name` (`service`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10317 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Asset_Attributes_Join`
--

DROP TABLE IF EXISTS `Performer_Asset_Attributes_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Asset_Attributes_Join` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) unsigned NOT NULL DEFAULT 0,
  `photo_type` enum('sample_image','photo') NOT NULL DEFAULT 'sample_image',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `attribute_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_added` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `distinct_image` (`photo_id`,`photo_type`,`attribute_id`),
  KEY `model_id` (`model_id`),
  KEY `date_added` (`date_added`)
) ENGINE=InnoDB AUTO_INCREMENT=3881526 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Blogs`
--

DROP TABLE IF EXISTS `Performer_Blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Blogs` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `high_quality` enum('Y','N') NOT NULL DEFAULT 'N',
  `site` enum('all','live','fanclub') NOT NULL DEFAULT 'all',
  `display_status` tinyint(1) NOT NULL DEFAULT 0,
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_status` enum('pending','approved','rejected','escalate','spam') NOT NULL DEFAULT 'pending',
  `allow_syndication` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `review_date` (`review_date`),
  KEY `high_quality_search` (`high_quality`,`display_status`)
) ENGINE=InnoDB AUTO_INCREMENT=854184 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Default_Photos`
--

DROP TABLE IF EXISTS `Performer_Default_Photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Default_Photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL DEFAULT 0,
  `photo_id` int(11) NOT NULL DEFAULT 0,
  `image_id` int(11) NOT NULL DEFAULT 0,
  `coords` varchar(45) NOT NULL DEFAULT '',
  `height` int(5) NOT NULL DEFAULT 0,
  `width` int(5) NOT NULL DEFAULT 0,
  `original_height` int(5) NOT NULL DEFAULT 0,
  `original_width` int(5) NOT NULL DEFAULT 0,
  `rotate` int(2) NOT NULL DEFAULT 0,
  `scale_x` int(2) NOT NULL DEFAULT 0,
  `scale_y` int(2) NOT NULL DEFAULT 0,
  `zoom` int(2) NOT NULL DEFAULT 0,
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `model_photo` (`model_id`,`photo_id`,`image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=298282 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Headshots`
--

DROP TABLE IF EXISTS `Performer_Headshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Headshots` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `upload_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upload_admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `upload_admin_type` enum('studio','admin') NOT NULL DEFAULT 'studio',
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_comments` text DEFAULT NULL,
  `review_status` enum('pending','approved','rejected','hold') NOT NULL DEFAULT 'pending',
  `display_status` tinyint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `review_status` (`review_status`),
  KEY `upload_date` (`upload_date`)
) ENGINE=InnoDB AUTO_INCREMENT=1407134 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Images`
--

DROP TABLE IF EXISTS `Performer_Images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Images` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `upload_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upload_admin_id` int(11) NOT NULL DEFAULT 0,
  `upload_admin_type` enum('studio','admin','model') NOT NULL DEFAULT 'studio',
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_comments` text DEFAULT NULL,
  `review_status` enum('pending','approved','rejected','deleted','hold') NOT NULL DEFAULT 'pending',
  `width` smallint(5) unsigned NOT NULL DEFAULT 0,
  `height` smallint(5) unsigned NOT NULL DEFAULT 0,
  `processed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `metadata_exists` smallint(1) unsigned NOT NULL DEFAULT 0,
  `display_status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_sfw` tinyint(1) unsigned DEFAULT NULL COMMENT 'Null means photo has not been checked, 1 means sfw, 0 means nsfw',
  `is_sfw_flagged_by_admin_id` smallint(3) unsigned NOT NULL DEFAULT 0 COMMENT '0 means cron script, >0 means internal admin ID',
  `is_sfw_flagged_datetime` datetime DEFAULT NULL COMMENT 'datetime existence means last time sfw flag updated, either automated or manually',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `review_status` (`review_status`),
  KEY `upload_date` (`upload_date`),
  KEY `processed` (`processed`),
  KEY `metadata_tool` (`review_status`,`display_status`,`metadata_exists`)
) ENGINE=InnoDB AUTO_INCREMENT=4688053 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Photo_Galleries`
--

DROP TABLE IF EXISTS `Performer_Photo_Galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Photo_Galleries` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `script_name` varchar(50) NOT NULL DEFAULT '',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_description` text NOT NULL,
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `search_strings` varchar(100) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  UNIQUE KEY `script_name` (`script_name`)
) ENGINE=InnoDB AUTO_INCREMENT=100044 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Photos`
--

DROP TABLE IF EXISTS `Performer_Photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Photos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `site` enum('all','fanclub','tip_target','paid') NOT NULL DEFAULT 'all',
  `upload_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upload_admin_id` int(11) NOT NULL DEFAULT 0,
  `upload_admin_type` enum('studio','admin','model') NOT NULL DEFAULT 'model',
  `upload_ip` varchar(15) NOT NULL DEFAULT '',
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_comments` text DEFAULT NULL,
  `review_status` enum('uploading','pending','approved','rejected','deleted') NOT NULL DEFAULT 'pending',
  `cksum` int(11) unsigned NOT NULL DEFAULT 0,
  `size_kb` smallint(5) unsigned NOT NULL DEFAULT 0,
  `width` smallint(5) unsigned NOT NULL DEFAULT 0,
  `height` smallint(5) unsigned NOT NULL DEFAULT 0,
  `watermark_width` smallint(5) unsigned NOT NULL DEFAULT 0,
  `watermark_height` smallint(5) unsigned NOT NULL DEFAULT 0,
  `thumb_width` smallint(3) unsigned NOT NULL DEFAULT 0,
  `thumb_height` smallint(3) unsigned NOT NULL DEFAULT 0,
  `rating` enum('hardcore','softcore') NOT NULL DEFAULT 'softcore',
  `folder` varchar(50) NOT NULL DEFAULT '',
  `caption` varchar(255) NOT NULL DEFAULT '',
  `num_views` int(11) unsigned NOT NULL DEFAULT 0,
  `num_votes` int(11) unsigned NOT NULL DEFAULT 0,
  `processed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `metadata_exists` smallint(1) unsigned NOT NULL DEFAULT 0,
  `display_status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `credit_cost` smallint(5) unsigned NOT NULL DEFAULT 0,
  `is_sfw` tinyint(1) unsigned DEFAULT NULL COMMENT 'Null means photo has not been checked, 1 means sfw, 0 means nsfw',
  `is_sfw_flagged_by_admin_id` smallint(3) unsigned NOT NULL DEFAULT 0 COMMENT '0 means cron script, >0 means internal admin ID',
  `is_sfw_flagged_datetime` datetime DEFAULT NULL COMMENT 'datetime existence means last time sfw flag updated, either automated or manually',
  PRIMARY KEY (`id`),
  KEY `review_status` (`review_status`),
  KEY `display_key` (`service`,`review_status`,`display_status`),
  KEY `processed` (`processed`),
  KEY `next_by_votes` (`review_status`,`display_status`,`num_votes`,`service`),
  KEY `getphotos` (`model_id`,`review_status`,`display_status`),
  KEY `metadata_tool` (`review_status`,`display_status`,`metadata_exists`)
) ENGINE=InnoDB AUTO_INCREMENT=7452462 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Photos_Comments`
--

DROP TABLE IF EXISTS `Performer_Photos_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Photos_Comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) unsigned NOT NULL DEFAULT 0,
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `nickname` varchar(30) DEFAULT NULL,
  `comments` text NOT NULL,
  `rating` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `photo_search` (`photo_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=371596 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Photos_Votes`
--

DROP TABLE IF EXISTS `Performer_Photos_Votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Photos_Votes` (
  `photo_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  KEY `photo_id` (`photo_id`),
  KEY `user_search` (`user_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Reviews`
--

DROP TABLE IF EXISTS `Performer_Reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Reviews` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `nickname` varchar(32) NOT NULL DEFAULT '',
  `rating` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `comments` text NOT NULL,
  `had_private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `high_quality` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` enum('pending','active','inactive','escalate','model_rejected') NOT NULL DEFAULT 'pending',
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `status` (`status`),
  KEY `datetime` (`datetime`),
  KEY `high_quality_search` (`high_quality`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1561895 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Podcasts_Channels`
--

DROP TABLE IF EXISTS `Podcasts_Channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Podcasts_Channels` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `filename` varchar(20) NOT NULL DEFAULT '',
  `service` enum('guys','girls') NOT NULL DEFAULT 'girls',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Podcasts_Items`
--

DROP TABLE IF EXISTS `Podcasts_Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Podcasts_Items` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `length` mediumint(6) NOT NULL DEFAULT 0,
  `filetype` enum('audio/mpeg') NOT NULL DEFAULT 'audio/mpeg',
  `date_published` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Press_Releases`
--

DROP TABLE IF EXISTS `Press_Releases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Press_Releases` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `subject` varchar(150) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `page_name` varchar(40) NOT NULL DEFAULT '',
  `date` date DEFAULT NULL,
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_name` (`page_name`)
) ENGINE=InnoDB AUTO_INCREMENT=20089 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Clips`
--

DROP TABLE IF EXISTS `Promo_Clips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Clips` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `sitekey` varchar(20) NOT NULL DEFAULT 'all',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `directory` varchar(50) NOT NULL DEFAULT '',
  `filename` varchar(50) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `vip_video_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `length` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `sitekey` (`sitekey`),
  KEY `service` (`service`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6015 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Clips_Usage`
--

DROP TABLE IF EXISTS `Promo_Clips_Usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Clips_Usage` (
  `promo_clip_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `total` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`promo_clip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Codes`
--

DROP TABLE IF EXISTS `Promo_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Codes` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL DEFAULT '',
  `sitekey` varchar(12) NOT NULL DEFAULT 'all',
  `mp_code` varchar(12) DEFAULT NULL,
  `expiration_type` enum('date','uses','first','last') NOT NULL DEFAULT 'date',
  `expiration_date` datetime DEFAULT NULL,
  `expiration_uses` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`,`sitekey`)
) ENGINE=InnoDB AUTO_INCREMENT=10000182 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Codes_Usage`
--

DROP TABLE IF EXISTS `Promo_Codes_Usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Codes_Usage` (
  `promo_code_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `service` varchar(12) NOT NULL DEFAULT 'girls',
  KEY `promo_code_id` (`promo_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Raffle_Winners`
--

DROP TABLE IF EXISTS `Raffle_Winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Raffle_Winners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `optiusers_id` int(10) unsigned NOT NULL,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `approved` enum('Y','N') NOT NULL DEFAULT 'N',
  `anonymous` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `date_service` (`date`,`service`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recurring_Shows`
--

DROP TABLE IF EXISTS `Recurring_Shows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recurring_Shows` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `sample_image_id` int(10) unsigned NOT NULL DEFAULT 0,
  `login_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `recurring_day` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `time_start` time NOT NULL DEFAULT '00:00:00',
  `time_end` time NOT NULL DEFAULT '00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_model_plugin` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `service` (`service`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_MX_Mistypes`
--

DROP TABLE IF EXISTS `Registration_MX_Mistypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_MX_Mistypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` datetime NOT NULL DEFAULT current_timestamp(),
  `mis_mx_domain` varchar(255) NOT NULL,
  `mx_domain` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mis_mx_domain_index` (`mis_mx_domain`),
  KEY `mx_domain_index` (`mx_domain`)
) ENGINE=InnoDB AUTO_INCREMENT=2571 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views`
--

DROP TABLE IF EXISTS `Registration_Views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `source_site_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `service` varchar(5) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `registration_tpl` varchar(50) NOT NULL DEFAULT '',
  `form_type` varchar(20) NOT NULL DEFAULT '',
  `form_ref` varchar(50) NOT NULL DEFAULT '',
  `udf01` text NOT NULL,
  `udf02` text NOT NULL,
  `udf03` text NOT NULL,
  `udf04` text NOT NULL,
  `udf05` text DEFAULT NULL,
  `udf06` text DEFAULT NULL,
  `udf07` text DEFAULT NULL,
  `udf08` text DEFAULT NULL,
  `udf09` text DEFAULT NULL,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  `auth_key` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `email_domain` varchar(100) NOT NULL DEFAULT '',
  `pixel_fired` char(1) NOT NULL DEFAULT '',
  `pixel_comments` varchar(100) NOT NULL DEFAULT '',
  `click_id` varchar(255) NOT NULL DEFAULT '',
  `acct_created_tpl` varchar(30) NOT NULL DEFAULT 'initial_register',
  `acct_created_subject` varchar(100) NOT NULL DEFAULT '',
  `opened_acct_created` enum('Y','N') NOT NULL DEFAULT 'N',
  `opened_reminder` enum('Y','N') NOT NULL DEFAULT 'N',
  `opened_acct_confirmed` enum('Y','N') NOT NULL DEFAULT 'N',
  `pending_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pending_username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `expired_date` date NOT NULL DEFAULT '0000-00-00',
  `confirm_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `confirm_user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `confirm_ip_address` varbinary(16) NOT NULL DEFAULT '',
  `cpl_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `cpl_imported` varchar(20) NOT NULL DEFAULT '',
  `bounced` enum('Y','N') NOT NULL DEFAULT 'N',
  `email_allowed` enum('Y','N') NOT NULL DEFAULT 'Y',
  `seconds_to_register` smallint(5) unsigned NOT NULL DEFAULT 0,
  `confirm_hash` varchar(100) DEFAULT NULL,
  `confirm_email_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `confirm_payment_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `email_status` varchar(64) NOT NULL DEFAULT '',
  `valid_email` tinyint(1) NOT NULL DEFAULT 1,
  `no_adult` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `delete_key` (`date`,`pending_username`,`confirm_user_id`),
  KEY `stats_key` (`date`,`registration_tpl`,`pending_username`,`confirm_user_id`),
  KEY `confirm_user_id` (`confirm_user_id`),
  KEY `expired_date` (`expired_date`),
  KEY `pending_username` (`pending_username`),
  KEY `date` (`date`),
  KEY `email_domain` (`email_domain`),
  KEY `email` (`email`),
  KEY `pending_datetime` (`pending_datetime`),
  KEY `mp_code` (`mp_code`),
  KEY `confirm_email_datetime` (`confirm_email_datetime`),
  KEY `confirm_payment_datetime` (`confirm_payment_datetime`),
  KEY `click_id` (`click_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50387980 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views_1click`
--

DROP TABLE IF EXISTS `Registration_Views_1click`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views_1click` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `confirm_user_id` int(10) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `udf01` text NOT NULL,
  `udf02` text NOT NULL,
  `udf03` text NOT NULL,
  `udf04` text NOT NULL,
  `udf05` text NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `email_domain` varchar(100) NOT NULL DEFAULT '',
  `acct_created_tpl` varchar(30) NOT NULL DEFAULT 'initial_register',
  `acct_created_subject` varchar(100) NOT NULL DEFAULT '',
  `opened_acct_created` enum('Y','N') NOT NULL DEFAULT 'N',
  `opened_reminder` enum('Y','N') NOT NULL DEFAULT 'N',
  `opened_acct_confirmed` enum('Y','N') NOT NULL DEFAULT 'N',
  `bounced` enum('Y','N') NOT NULL DEFAULT 'N',
  `email_allowed` enum('Y','N') NOT NULL DEFAULT 'Y',
  `expired_date` date NOT NULL DEFAULT '0000-00-00',
  `1click_version` varchar(8) NOT NULL DEFAULT '1.0',
  `auth_key` varchar(32) NOT NULL DEFAULT '',
  `pending_username` varchar(32) NOT NULL DEFAULT '',
  `pending_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `free_credits` smallint(5) unsigned NOT NULL DEFAULT 0,
  `click_id` varchar(255) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `service` varchar(5) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(64) NOT NULL DEFAULT '',
  `cpl_plan_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `cpl_imported` varchar(20) NOT NULL DEFAULT '',
  `pixel_fired` char(1) NOT NULL DEFAULT '',
  `pixel_comments` varchar(100) NOT NULL DEFAULT '',
  `confirm_hash` varchar(100) DEFAULT NULL,
  `confirm_email_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `confirm_payment_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `email_status` varchar(64) NOT NULL DEFAULT '',
  `valid_email` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `confirm_user_id` (`confirm_user_id`),
  KEY `expired_date` (`expired_date`),
  KEY `date` (`date`),
  KEY `email_domain` (`email_domain`),
  KEY `email` (`email`),
  KEY `1click_version` (`1click_version`),
  KEY `pending_username` (`pending_username`),
  KEY `click_id` (`click_id`),
  KEY `delete_key` (`date`,`pending_username`,`confirm_user_id`),
  KEY `pending_datetime` (`pending_datetime`),
  KEY `mp_code` (`mp_code`),
  KEY `confirm_email_datetime` (`confirm_email_datetime`),
  KEY `confirm_payment_datetime` (`confirm_payment_datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=14847329 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views_1click_Ex`
--

DROP TABLE IF EXISTS `Registration_Views_1click_Ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views_1click_Ex` (
  `registration_views_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `smtp_server` varchar(128) NOT NULL DEFAULT '',
  `ga_id` varchar(15) NOT NULL,
  `utm_source` varchar(50) NOT NULL DEFAULT 'affiliate',
  `utm_medium` varchar(50) DEFAULT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `utm_content` varchar(50) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `registration_url` varchar(256) NOT NULL DEFAULT '',
  `reason` varchar(160) DEFAULT NULL,
  `rule_type` varchar(35) DEFAULT NULL,
  `rules_matched` smallint(4) unsigned DEFAULT NULL,
  `is_internal` tinyint(1) unsigned DEFAULT 0,
  `confirm_method` varchar(10) NOT NULL DEFAULT '',
  `identify_as` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`registration_views_id`),
  KEY `is_internal` (`is_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views_Abandon`
--

DROP TABLE IF EXISTS `Registration_Views_Abandon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views_Abandon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(40) NOT NULL DEFAULT '',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `country_code` char(2) NOT NULL DEFAULT '',
  `date_logged` date NOT NULL DEFAULT '0000-00-00',
  `datetime_logged` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `email` varchar(255) NOT NULL DEFAULT '',
  `nickname` varchar(32) NOT NULL DEFAULT '',
  `had_password` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `date_logged` (`date_logged`)
) ENGINE=InnoDB AUTO_INCREMENT=2691172 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views_Changes`
--

DROP TABLE IF EXISTS `Registration_Views_Changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views_Changes` (
  `registration_view_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `email_before` varchar(255) NOT NULL DEFAULT '',
  `email_after` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `registration_view_id` (`registration_view_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views_Click_IDs`
--

DROP TABLE IF EXISTS `Registration_Views_Click_IDs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views_Click_IDs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_type` enum('standard','1click') NOT NULL DEFAULT 'standard',
  `ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `click_id_long` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_value` (`ref_type`,`ref_id`)
) ENGINE=InnoDB AUTO_INCREMENT=266886 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views_Dupes`
--

DROP TABLE IF EXISTS `Registration_Views_Dupes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views_Dupes` (
  `registration_views_id` int(11) unsigned NOT NULL,
  `dupe_user_id` int(10) unsigned NOT NULL,
  `confirm_user_id` int(10) unsigned NOT NULL,
  `reason` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`registration_views_id`),
  KEY `dupe_user_key` (`dupe_user_id`),
  KEY `user_key` (`confirm_user_id`),
  KEY `reason_key` (`reason`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views_Dupes_1click`
--

DROP TABLE IF EXISTS `Registration_Views_Dupes_1click`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views_Dupes_1click` (
  `registration_views_id` int(11) unsigned NOT NULL,
  `dupe_user_id` int(10) unsigned NOT NULL,
  `confirm_user_id` int(10) unsigned NOT NULL,
  `reason` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`registration_views_id`),
  KEY `dupe_user_key` (`dupe_user_id`),
  KEY `user_key` (`confirm_user_id`),
  KEY `reason_key` (`reason`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Registration_Views_Ex`
--

DROP TABLE IF EXISTS `Registration_Views_Ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Registration_Views_Ex` (
  `registration_views_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `smtp_server` varchar(128) NOT NULL DEFAULT '',
  `ga_id` varchar(15) NOT NULL,
  `utm_source` varchar(50) NOT NULL DEFAULT 'affiliate',
  `utm_medium` varchar(50) DEFAULT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `utm_content` varchar(50) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `registration_url` varchar(256) NOT NULL DEFAULT '',
  `reason` varchar(160) DEFAULT NULL,
  `rule_type` varchar(35) DEFAULT NULL,
  `rules_matched` smallint(4) unsigned DEFAULT NULL,
  `is_internal` tinyint(1) unsigned DEFAULT 0,
  `confirm_method` varchar(10) NOT NULL DEFAULT '',
  `identify_as` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`registration_views_id`),
  KEY `is_internal` (`is_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Scheduled_Shows`
--

DROP TABLE IF EXISTS `Scheduled_Shows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Scheduled_Shows` (
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
  `platform_id` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `sitekey` varchar(20) NOT NULL DEFAULT 'all',
  `domain` varchar(255) NOT NULL DEFAULT 'all',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `service` (`service`),
  KEY `status` (`status`),
  KEY `domain` (`domain`),
  KEY `platform_sitekey_domain` (`platform_id`,`sitekey`,`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=167495 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Scheduled_Shows_Tipping`
--

DROP TABLE IF EXISTS `Scheduled_Shows_Tipping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Scheduled_Shows_Tipping` (
  `scheduled_shows_id` mediumint(5) unsigned NOT NULL,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `use_default_image` tinyint(1) NOT NULL DEFAULT 1,
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_last_updated` datetime DEFAULT NULL,
  `use_custom_image` tinyint(1) DEFAULT 0,
  UNIQUE KEY `show_and_model_ids` (`scheduled_shows_id`,`model_id`),
  KEY `scheduled_shows_id` (`scheduled_shows_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Short_Urls`
--

DROP TABLE IF EXISTS `Short_Urls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Short_Urls` (
  `id` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `short_code` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `long_url` varchar(255) NOT NULL DEFAULT '',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `url_clickthrough_count` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short_code` (`short_code`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=646570 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Social_Media`
--

DROP TABLE IF EXISTS `Social_Media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Social_Media` (
  `id` smallint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Suggestions_By_Model`
--

DROP TABLE IF EXISTS `Suggestions_By_Model`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Suggestions_By_Model` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id_other` int(11) unsigned NOT NULL DEFAULT 0,
  `score` float(6,5) unsigned NOT NULL DEFAULT 0.00000,
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Syndicated_Blogs`
--

DROP TABLE IF EXISTS `Syndicated_Blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Syndicated_Blogs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(200) NOT NULL,
  `post` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(30) NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `fetish` enum('Y','N','S') DEFAULT 'N',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=145469 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tags`
--

DROP TABLE IF EXISTS `Tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tags` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `tag_type` enum('fetish','toy','roleplay') NOT NULL DEFAULT 'fetish',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `weight` mediumint(3) unsigned NOT NULL DEFAULT 0,
  `path` varchar(50) NOT NULL DEFAULT '',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_description` text NOT NULL,
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `is_fetish` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`tag_type`,`service`),
  UNIQUE KEY `path` (`path`,`service`),
  KEY `status` (`status`),
  KEY `is_fetish` (`is_fetish`)
) ENGINE=InnoDB AUTO_INCREMENT=10884 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tip_Fraud_Whitelist`
--

DROP TABLE IF EXISTS `Tip_Fraud_Whitelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tip_Fraud_Whitelist` (
  `user_id` int(11) unsigned NOT NULL,
  `admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Translation_Chat_Phrases`
--

DROP TABLE IF EXISTS `Translation_Chat_Phrases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Translation_Chat_Phrases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `root_id` int(10) unsigned NOT NULL,
  `phrase_text` varchar(1028) DEFAULT NULL,
  `language_code` varchar(13) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `root_id` (`root_id`,`language_code`),
  KEY `phrase_text` (`phrase_text`,`root_id`),
  KEY `language_code` (`language_code`)
) ENGINE=InnoDB AUTO_INCREMENT=289107548 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Translation_File_Stats`
--

DROP TABLE IF EXISTS `Translation_File_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Translation_File_Stats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_path` varchar(767) NOT NULL DEFAULT '',
  `cksum` int(10) unsigned NOT NULL,
  `date_consumed` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_path` (`file_path`)
) ENGINE=InnoDB AUTO_INCREMENT=20465 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Translation_File_To_Text`
--

DROP TABLE IF EXISTS `Translation_File_To_Text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Translation_File_To_Text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `text_id` int(10) unsigned NOT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `text_id` (`text_id`,`file_id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Translation_Site_Text`
--

DROP TABLE IF EXISTS `Translation_Site_Text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Translation_Site_Text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `text_hash` char(32) NOT NULL,
  `text_content` varchar(32768) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `text_hash` (`text_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Translation_Site_Text_Translations`
--

DROP TABLE IF EXISTS `Translation_Site_Text_Translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Translation_Site_Text_Translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `root_id` int(10) unsigned NOT NULL,
  `language_code` varchar(13) NOT NULL,
  `text_content` varchar(32768) NOT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `root_id` (`root_id`,`language_code`),
  KEY `complete` (`complete`)
) ENGINE=InnoDB AUTO_INCREMENT=928 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Blocked_Models`
--

DROP TABLE IF EXISTS `User_Blocked_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Blocked_Models` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `screen_name_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_added` date DEFAULT NULL,
  `block_type` varchar(10) NOT NULL DEFAULT 'dm',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_model` (`screen_name_id`,`model_id`),
  KEY `screen_name_id` (`screen_name_id`),
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`),
  KEY `date_added` (`date_added`),
  KEY `block_type` (`block_type`)
) ENGINE=InnoDB AUTO_INCREMENT=34989 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Feature_Activity`
--

DROP TABLE IF EXISTS `User_Feature_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Feature_Activity` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `feature_name` varchar(25) NOT NULL,
  `datetime_created` datetime NOT NULL,
  `cookie_state` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=248520 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Groups`
--

DROP TABLE IF EXISTS `User_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Groups` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Paypig_Transact`
--

DROP TABLE IF EXISTS `User_Paypig_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Paypig_Transact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paypig_wallet_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `gift_transact_id` int(10) unsigned NOT NULL DEFAULT 0,
  `datetime_transact` datetime DEFAULT NULL,
  `num_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `wallet_model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `gifted_model_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `paypig_wallet_id` (`paypig_wallet_id`),
  KEY `gift_transact_id` (`gift_transact_id`),
  KEY `user_id` (`user_id`),
  KEY `wallet_model_id` (`wallet_model_id`),
  KEY `gifted_model_id` (`gifted_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Paypig_Transfer_Log`
--

DROP TABLE IF EXISTS `User_Paypig_Transfer_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Paypig_Transfer_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `paypig_wallet_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `datetime_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`model_id`),
  KEY `paypig_wallet_id` (`paypig_wallet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Paypig_Wallets`
--

DROP TABLE IF EXISTS `User_Paypig_Wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Paypig_Wallets` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `screen_name` varchar(32) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `tip_anonymously` tinyint(1) unsigned NOT NULL,
  `num_credits_balance` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `total_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `datetime_created` datetime DEFAULT NULL,
  `datetime_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`model_id`,`screen_name`),
  KEY `datetime_created` (`datetime_created`),
  KEY `datetime_updated` (`datetime_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Privacy_Settings`
--

DROP TABLE IF EXISTS `User_Privacy_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Privacy_Settings` (
  `user_id` int(10) unsigned NOT NULL,
  `screen_name_lower` varchar(32) NOT NULL,
  `identifier` varchar(32) NOT NULL,
  `user_group_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`screen_name_lower`,`identifier`,`user_group_id`),
  KEY `user_id` (`user_id`),
  KEY `screen_name_lower` (`screen_name_lower`),
  KEY `user_and_screen` (`user_id`,`screen_name_lower`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_2257`
--

DROP TABLE IF EXISTS `VIP_2257`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_2257` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(40) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `custodian_name` varchar(40) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Content_Category`
--

DROP TABLE IF EXISTS `VIP_Content_Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Content_Category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `service` varchar(12) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `special` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Content_Category_Join`
--

DROP TABLE IF EXISTS `VIP_Content_Category_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Content_Category_Join` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `type_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `type` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`,`type_id`,`type`),
  UNIQUE KEY `content_category` (`category_id`,`type_id`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Desired_Models`
--

DROP TABLE IF EXISTS `VIP_Desired_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Desired_Models` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `model_name` (`model_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Free_Accounts`
--

DROP TABLE IF EXISTS `VIP_Free_Accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Free_Accounts` (
  `username` varchar(12) NOT NULL DEFAULT '',
  `password` varchar(12) NOT NULL DEFAULT '',
  `type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Gallery`
--

DROP TABLE IF EXISTS `VIP_Gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Gallery` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) DEFAULT NULL,
  `title` varchar(30) NOT NULL DEFAULT '',
  `gallery` varchar(30) NOT NULL DEFAULT '',
  `feature_id` smallint(4) unsigned DEFAULT NULL,
  `service` varchar(8) DEFAULT 'girls',
  `thumb` varchar(255) DEFAULT NULL,
  `total` smallint(3) DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_selected` datetime DEFAULT NULL,
  `special` tinyint(1) DEFAULT 0,
  `site` varchar(20) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3136 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Gallery_Photos`
--

DROP TABLE IF EXISTS `VIP_Gallery_Photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Gallery_Photos` (
  `gallery_id` smallint(5) unsigned NOT NULL,
  `image_name` varchar(100) NOT NULL,
  PRIMARY KEY (`gallery_id`,`image_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Gallery_WL`
--

DROP TABLE IF EXISTS `VIP_Gallery_WL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Gallery_WL` (
  `gallery_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `title` varchar(30) NOT NULL DEFAULT '',
  `directory` varchar(30) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_name` varchar(50) NOT NULL DEFAULT '',
  `studio_code` char(12) NOT NULL DEFAULT '',
  `num_photos` smallint(3) unsigned NOT NULL DEFAULT 0,
  `thumb` varchar(255) NOT NULL DEFAULT '',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `feature_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `date_added` date NOT NULL DEFAULT '0000-00-00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`gallery_id`),
  KEY `model_id` (`model_id`),
  KEY `model_name` (`model_name`),
  KEY `service` (`service`),
  KEY `date_added` (`date_added`),
  KEY `date_updated` (`date_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Login_Log`
--

DROP TABLE IF EXISTS `VIP_Login_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Login_Log` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `total` smallint(5) unsigned NOT NULL DEFAULT 0,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Member_Photos_Access`
--

DROP TABLE IF EXISTS `VIP_Member_Photos_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Member_Photos_Access` (
  `photo_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_viewed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `num_views` smallint(5) unsigned NOT NULL DEFAULT 1,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`photo_id`,`model_id`),
  KEY `photo_id` (`photo_id`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Members_Photos_Access`
--

DROP TABLE IF EXISTS `VIP_Members_Photos_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Members_Photos_Access` (
  `item_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `item_type` enum('folder','photo') NOT NULL DEFAULT 'photo',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`item_id`,`item_type`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Members_Photos_Views`
--

DROP TABLE IF EXISTS `VIP_Members_Photos_Views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Members_Photos_Views` (
  `photo_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `last_viewed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `num_views` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`photo_id`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_News`
--

DROP TABLE IF EXISTS `VIP_News`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_News` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `service` enum('girls','guys','all') DEFAULT 'all',
  `description` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_update` tinyint(1) DEFAULT 0,
  `site` varchar(20) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6018 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Notify_Join`
--

DROP TABLE IF EXISTS `VIP_Notify_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Notify_Join` (
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT 'online',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`optiusers_id`,`model_id`,`type`),
  KEY `optiusers_id` (`optiusers_id`,`model_id`,`type`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Notify_Log`
--

DROP TABLE IF EXISTS `VIP_Notify_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Notify_Log` (
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`optiusers_id`,`model_id`,`type`),
  KEY `optiusers_id` (`optiusers_id`,`model_id`,`type`),
  KEY `datetime_index` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Notify_Profile`
--

DROP TABLE IF EXISTS `VIP_Notify_Profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Notify_Profile` (
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `address` varchar(255) NOT NULL DEFAULT '',
  `activation_code` varchar(20) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_verified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`optiusers_id`,`type`),
  KEY `optiusers_id` (`optiusers_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Poll_Answers`
--

DROP TABLE IF EXISTS `VIP_Poll_Answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Poll_Answers` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `answer` varchar(255) NOT NULL DEFAULT '',
  `poll_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Poll_Questions`
--

DROP TABLE IF EXISTS `VIP_Poll_Questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Poll_Questions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `service` varchar(5) NOT NULL DEFAULT 'girls',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Poll_Results`
--

DROP TABLE IF EXISTS `VIP_Poll_Results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Poll_Results` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `poll_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `answer_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`username`,`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Ratings`
--

DROP TABLE IF EXISTS `VIP_Ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Ratings` (
  `type` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `item_id` int(6) unsigned NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL,
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(12) NOT NULL DEFAULT 'girls',
  `model_id` int(11) unsigned DEFAULT NULL,
  `count` mediumint(8) unsigned NOT NULL DEFAULT 0,
  KEY `top_daily` (`type`,`item_id`,`date`,`sitekey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Sales_Commission`
--

DROP TABLE IF EXISTS `VIP_Sales_Commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Sales_Commission` (
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `seller` varchar(24) NOT NULL DEFAULT '',
  `mp_salesperson` varchar(50) NOT NULL DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `commission` double NOT NULL DEFAULT 0,
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chargeback_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Sites`
--

DROP TABLE IF EXISTS `VIP_Sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Sites` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) DEFAULT NULL,
  `domain` varchar(100) NOT NULL DEFAULT '',
  `domain_dev` varchar(100) NOT NULL DEFAULT '',
  `domain_vip` varchar(100) DEFAULT NULL,
  `domain_vip_dev` varchar(100) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  `default_mp` varchar(12) NOT NULL DEFAULT '',
  `vip_site_id` int(4) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sitekey` (`sitekey`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video`
--

DROP TABLE IF EXISTS `VIP_Video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) DEFAULT NULL,
  `model_name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `file` varchar(30) NOT NULL DEFAULT '',
  `directory` varchar(30) DEFAULT NULL,
  `feature_id` smallint(4) unsigned DEFAULT NULL,
  `service` varchar(8) DEFAULT 'girls',
  `length` mediumint(8) DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_selected` datetime DEFAULT NULL,
  `audio` tinyint(1) DEFAULT 0,
  `widescreen` varchar(20) DEFAULT '',
  `windows_media` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `quicktime` tinyint(1) DEFAULT 0,
  `flash_flv` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `special` tinyint(1) DEFAULT 0,
  `site` varchar(20) DEFAULT NULL,
  `media_id` mediumint(6) unsigned DEFAULT NULL,
  `show_id` int(11) unsigned NOT NULL DEFAULT 0,
  `show_studio` char(12) NOT NULL DEFAULT '',
  `date_paid` date NOT NULL DEFAULT '0000-00-00',
  `ftp_check` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15117 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Categories`
--

DROP TABLE IF EXISTS `VIP_Video_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Categories` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `service` enum('guys','girls') NOT NULL DEFAULT 'girls',
  `name` varchar(25) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service` (`service`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Categories_Join`
--

DROP TABLE IF EXISTS `VIP_Video_Categories_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Categories_Join` (
  `clip_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `category_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  UNIQUE KEY `clip_id` (`clip_id`,`category_id`,`user_id`),
  KEY `clip_id_2` (`clip_id`,`category_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Comments`
--

DROP TABLE IF EXISTS `VIP_Video_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Comments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `nickname` varchar(30) DEFAULT NULL,
  `comments` text NOT NULL,
  `rating` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','active','inactive','escalate') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `video_id` (`video_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=20472 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_DLD`
--

DROP TABLE IF EXISTS `VIP_Video_DLD`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_DLD` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `clip_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `dld_filename` varchar(50) NOT NULL DEFAULT '',
  `size` float(6,2) unsigned NOT NULL DEFAULT 0.00,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clip_id` (`clip_id`,`dld_filename`,`status`),
  KEY `clip_id_2` (`clip_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8861 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Media`
--

DROP TABLE IF EXISTS `VIP_Video_Media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Media` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `source_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `tape_id` varchar(20) NOT NULL DEFAULT '',
  `tape_prefix` varchar(12) DEFAULT NULL,
  `tape_num` smallint(5) unsigned NOT NULL DEFAULT 0,
  `medium` enum('dv','dvd','hdd') DEFAULT 'dvd',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `date_received` date NOT NULL DEFAULT '0000-00-00',
  `date_due` date NOT NULL DEFAULT '0000-00-00',
  `status` enum('new','canceled','corrupted','encoding','finished') DEFAULT NULL,
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tape_id` (`tape_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1948 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Media_Models`
--

DROP TABLE IF EXISTS `VIP_Video_Media_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Media_Models` (
  `media_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`media_id`,`model_id`),
  KEY `model_name` (`model_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Ratings`
--

DROP TABLE IF EXISTS `VIP_Video_Ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Ratings` (
  `video_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `optiusers_id` int(10) unsigned NOT NULL DEFAULT 0,
  `rating` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL,
  UNIQUE KEY `video_id` (`video_id`,`optiusers_id`,`rating`),
  KEY `video_id_2` (`video_id`,`optiusers_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Source_Studios`
--

DROP TABLE IF EXISTS `VIP_Video_Source_Studios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Source_Studios` (
  `source_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `studio_name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`source_id`,`studio_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Sources`
--

DROP TABLE IF EXISTS `VIP_Video_Sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Sources` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `prefix` varchar(12) DEFAULT NULL,
  `default_medium` enum('dv','dvd') NOT NULL DEFAULT 'dvd',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Usage`
--

DROP TABLE IF EXISTS `VIP_Video_Usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Usage` (
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `studio` char(12) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_name` varchar(50) NOT NULL DEFAULT '',
  `total_clips` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_private_shows` smallint(5) unsigned DEFAULT NULL,
  `percentage` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  `bonus_amount` float(5,2) unsigned NOT NULL DEFAULT 0.00,
  UNIQUE KEY `start_date` (`start_date`,`studio`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_Verify`
--

DROP TABLE IF EXISTS `VIP_Video_Verify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_Verify` (
  `clip_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) unsigned DEFAULT 0,
  `skip` tinyint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`clip_id`),
  UNIQUE KEY `clip_id` (`clip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VIP_Video_WL`
--

DROP TABLE IF EXISTS `VIP_Video_WL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VIP_Video_WL` (
  `video_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `title` varchar(50) NOT NULL DEFAULT '',
  `directory` varchar(30) NOT NULL DEFAULT '',
  `file` varchar(30) NOT NULL DEFAULT '',
  `length` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_name` varchar(50) NOT NULL DEFAULT '',
  `studio_code` char(12) NOT NULL DEFAULT '',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `feature_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `date_added` date NOT NULL DEFAULT '0000-00-00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`video_id`),
  KEY `model_id` (`model_id`),
  KEY `model_name` (`model_name`),
  KEY `service` (`service`),
  KEY `date_added` (`date_added`),
  KEY `date_updated` (`date_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Products`
--

DROP TABLE IF EXISTS `VOD_Products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Products` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `dhd_product_id` int(9) unsigned NOT NULL DEFAULT 0,
  `dhd_ptm_id` int(11) unsigned NOT NULL DEFAULT 0,
  `price` float(5,2) NOT NULL DEFAULT 0.00,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `length` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `dhd_product_id` (`dhd_product_id`),
  KEY `service` (`service`)
) ENGINE=InnoDB AUTO_INCREMENT=15000012 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Products_Files`
--

DROP TABLE IF EXISTS `VOD_Products_Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Products_Files` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `vod_product_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `directory` varchar(50) NOT NULL DEFAULT '',
  `filename` varchar(50) NOT NULL DEFAULT '',
  `size` float(6,2) NOT NULL DEFAULT 0.00,
  `length` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `vod_product_id` (`vod_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10027 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Products_Samples`
--

DROP TABLE IF EXISTS `VOD_Products_Samples`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Products_Samples` (
  `vod_product_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `filename` varchar(50) NOT NULL DEFAULT '',
  `caption` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`vod_product_id`,`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Valentines_2012`
--

DROP TABLE IF EXISTS `Valentines_2012`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Valentines_2012` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `total_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`user_nickname`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Video_Recorder_Backup`
--

DROP TABLE IF EXISTS `Video_Recorder_Backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Video_Recorder_Backup` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_size` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `show_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `video_server_ip` varchar(15) NOT NULL DEFAULT '',
  `recorder_server_ip` varchar(15) NOT NULL DEFAULT '',
  `video_location` varchar(30) NOT NULL DEFAULT '',
  `recorder_location` varchar(30) NOT NULL DEFAULT '',
  `uploaded` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `time_to_upload` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`file_name`),
  KEY `model_show_time` (`model_id`,`show_time`),
  KEY `date_uploaded` (`date`,`uploaded`),
  KEY `recorder_server` (`recorder_server_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Voting_Contests`
--

DROP TABLE IF EXISTS `Voting_Contests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Voting_Contests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `credit_cost` smallint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `message` varchar(255) NOT NULL DEFAULT '[CUSTOMER] Has voted in the contest',
  `make_it_rain` tinyint(4) NOT NULL DEFAULT 0,
  `service` varchar(5) NOT NULL DEFAULT '',
  `contest_description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Voting_Results`
--

DROP TABLE IF EXISTS `Voting_Results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Voting_Results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `date_voted` date NOT NULL DEFAULT '0000-00-00',
  `vote_weight` float(6,2) NOT NULL DEFAULT 0.00,
  `vote_source` enum('contest','chatroom','bio','other') DEFAULT 'other',
  PRIMARY KEY (`id`),
  KEY `contest_user` (`contest_id`,`date_voted`,`user_id`),
  KEY `contest_model` (`contest_id`,`date_voted`,`model_id`),
  KEY `user_model_date_contest` (`user_id`,`model_id`,`date_voted`,`contest_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22418353 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Voting_Weight_Log`
--

DROP TABLE IF EXISTS `Voting_Weight_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Voting_Weight_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `contest_id` int(11) unsigned NOT NULL DEFAULT 185,
  `user_type` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'open',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_type_status` (`user_type`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1480 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Voting_Weights`
--

DROP TABLE IF EXISTS `Voting_Weights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Voting_Weights` (
  `contest_id` int(11) unsigned NOT NULL DEFAULT 0,
  `weight` float(6,2) NOT NULL DEFAULT 0.00,
  `user_type` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`contest_id`,`user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Whitelabel_Announcements`
--

DROP TABLE IF EXISTS `Whitelabel_Announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Whitelabel_Announcements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL,
  `state` enum('unpublished','published','deleted') NOT NULL DEFAULT 'unpublished',
  `published_to` varchar(255) NOT NULL DEFAULT '' COMMENT 'comma-separated list of site-keys: WL,XVT,XVC',
  `date_published` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state_date` (`state`,`date_published`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_logs`
--

DROP TABLE IF EXISTS `chat_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_logs` (
  `model` varchar(50) NOT NULL DEFAULT '',
  `room_type` enum('private','open') DEFAULT 'private',
  `speaker` varchar(50) DEFAULT NULL,
  `chat_text` text DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  KEY `time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fe_questions`
--

DROP TABLE IF EXISTS `fe_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fe_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `answer` text DEFAULT NULL,
  `user_agent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=380 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feature_show`
--

DROP TABLE IF EXISTS `feature_show`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `feature_show` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `cancelled` char(1) NOT NULL DEFAULT 'N',
  `scheduled_start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `scheduled_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_login_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_logout_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` varchar(255) NOT NULL DEFAULT '',
  `long_description` text DEFAULT NULL,
  `vip_description` text DEFAULT NULL,
  `landing_page_url` varchar(255) DEFAULT NULL,
  `is_ok_to_display` enum('Y','N') NOT NULL DEFAULT 'Y',
  `site` varchar(20) DEFAULT NULL,
  `sex` char(1) DEFAULT 'F',
  `duration` mediumint(7) unsigned NOT NULL,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  KEY `scheduled_start_time` (`scheduled_start_time`)
) ENGINE=InnoDB AUTO_INCREMENT=5688 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feature_show_access`
--

DROP TABLE IF EXISTS `feature_show_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `feature_show_access` (
  `feature_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`feature_id`,`sitekey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feature_show_stats`
--

DROP TABLE IF EXISTS `feature_show_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `feature_show_stats` (
  `feature_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `datetime_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `users` smallint(4) unsigned NOT NULL DEFAULT 0,
  `minutes` float(6,2) unsigned NOT NULL DEFAULT 0.00,
  `sales` float(6,2) unsigned NOT NULL DEFAULT 0.00,
  `average_users` float(6,2) unsigned NOT NULL DEFAULT 0.00,
  `total_users` smallint(4) unsigned NOT NULL DEFAULT 0,
  `total_minutes` float(6,2) unsigned NOT NULL DEFAULT 0.00,
  `total_sales` float(6,2) unsigned NOT NULL DEFAULT 0.00,
  `total_avg_show` float(4,2) unsigned NOT NULL DEFAULT 0.00,
  KEY `feature_id` (`feature_id`,`datetime_start`,`datetime_end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_categories`
--

DROP TABLE IF EXISTS `model_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_categories` (
  `model_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `category_id` int(10) unsigned NOT NULL DEFAULT 0,
  `rank` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`category_id`),
  UNIQUE KEY `model_category_id` (`model_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11818 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_characteristics`
--

DROP TABLE IF EXISTS `model_characteristics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_characteristics` (
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `dob_month` enum('01','02','03','04','05','06','07','08','09','10','11','12') DEFAULT NULL,
  `dob_day` enum('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31') DEFAULT NULL,
  `dob_year` mediumint(8) unsigned DEFAULT NULL,
  `height_feet` enum('3','4','5','6') DEFAULT NULL,
  `height_inches` enum('0','1','2','3','4','5','6','7','8','9','10','11') DEFAULT NULL,
  `weight` enum('85','90','95','100','105','110','115','120','125','130','135','140','145','150','155','160','165','170','175','180','185','190','195','200','205','210','215','220','225','230','235','240','245','250') DEFAULT NULL,
  `hair_color` enum('black','blonde','brown','red','bald') DEFAULT NULL,
  `eye_color` enum('blue','brown','gray','green','hazel') DEFAULT NULL,
  `bra_size` enum('30','32','34','36','38','40','42','44','46','48','50') DEFAULT NULL,
  `cup_size` enum('AA','A','B','C','D','DD','DDD','EE','FF','GG') DEFAULT NULL,
  `waist` enum('18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34') DEFAULT NULL,
  `hips` enum('22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46') DEFAULT NULL,
  `orientation` enum('straight','gay','bi') DEFAULT NULL,
  `ethnicity` enum('White','Black','Asian','Hispanic') DEFAULT NULL,
  `cock_type` enum('cut','uncut') DEFAULT NULL,
  `cock_size` enum('5','5.5','6','6.5','7','7.5','8','8.5','9','9.5','10') DEFAULT NULL,
  `language_english` enum('Y','N') DEFAULT NULL,
  `language_czech` enum('Y','N') DEFAULT NULL,
  `language_spanish` enum('Y','N') DEFAULT NULL,
  `language_french` enum('Y','N') DEFAULT NULL,
  `language_german` enum('Y','N') DEFAULT NULL,
  `language_italian` enum('Y','N') DEFAULT NULL,
  `language_portuguese` enum('Y','N') DEFAULT NULL,
  `language_greek` enum('Y','N') DEFAULT NULL,
  `language_russian` enum('Y','N') DEFAULT NULL,
  `language_polish` enum('Y','N') DEFAULT NULL,
  `language_japanese` enum('Y','N') DEFAULT NULL,
  `tattoo` enum('Y','N') DEFAULT NULL,
  `pierced` enum('Y','N') DEFAULT NULL,
  `shaved` enum('Y','N') DEFAULT NULL,
  `hairy` enum('Y','N') DEFAULT NULL,
  `fantasies` varchar(255) DEFAULT NULL,
  `likes` varchar(255) DEFAULT NULL,
  `fetishes` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_message`
--

DROP TABLE IF EXISTS `model_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_message` (
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `message` text DEFAULT NULL,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_review`
--

DROP TABLE IF EXISTS `model_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_review` (
  `model_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `reviewer_name` varchar(20) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_schedule`
--

DROP TABLE IF EXISTS `model_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_schedule` (
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `room` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_votes_raw`
--

DROP TABLE IF EXISTS `model_votes_raw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_votes_raw` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `ip` varchar(15) DEFAULT NULL,
  `model_id` smallint(5) unsigned DEFAULT NULL,
  `service` varchar(15) NOT NULL DEFAULT 'girls'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schedule_time_zone`
--

DROP TABLE IF EXISTS `schedule_time_zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_time_zone` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment` int(11) DEFAULT NULL,
  `time_zone` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `service_categories`
--

DROP TABLE IF EXISTS `service_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_categories` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(10) unsigned NOT NULL DEFAULT 0,
  `category_name` varchar(20) NOT NULL DEFAULT '',
  `rank` int(10) unsigned NOT NULL DEFAULT 0,
  `display_category_name` enum('Y','N') NOT NULL DEFAULT 'Y',
  `broadcaster_can_use` enum('Y','N') NOT NULL DEFAULT 'Y',
  `restrict_to_studio` char(12) DEFAULT NULL,
  PRIMARY KEY (`service_id`,`category_name`),
  UNIQUE KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=426 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skin_info`
--

DROP TABLE IF EXISTS `skin_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skin_info` (
  `skin_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `default_mp` varchar(4) DEFAULT NULL,
  `default_service` varchar(20) DEFAULT 'girls',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  UNIQUE KEY `skin_id` (`skin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skin_statistics`
--

DROP TABLE IF EXISTS `skin_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skin_statistics` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `server` varchar(15) NOT NULL DEFAULT '',
  `skin_id` tinyint(2) unsigned NOT NULL DEFAULT 1,
  `hits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_secure` mediumint(8) unsigned DEFAULT NULL,
  `transactions` smallint(4) unsigned NOT NULL DEFAULT 0,
  `dollars` float(7,2) unsigned NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skin_summary`
--

DROP TABLE IF EXISTS `skin_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skin_summary` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `skin_id` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `hits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_secure` mediumint(8) unsigned DEFAULT NULL,
  `transactions` smallint(4) unsigned NOT NULL DEFAULT 0,
  `dollars` float(7,2) unsigned NOT NULL DEFAULT 0.00,
  UNIQUE KEY `skin_id` (`date`,`skin_id`)
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

-- Dump completed on 2025-10-25 17:29:02
