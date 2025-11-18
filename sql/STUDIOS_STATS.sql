/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: STUDIOS_STATS
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
-- Table structure for table `Bonuses_Summary`
--

DROP TABLE IF EXISTS `Bonuses_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Bonuses_Summary` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `bonus_type` varchar(30) NOT NULL DEFAULT '',
  `rank` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `payout` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `total` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `random_winner` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`model_id`,`bonus_type`,`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Bonuses_Summary_Leaderboard`
--

DROP TABLE IF EXISTS `Bonuses_Summary_Leaderboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Bonuses_Summary_Leaderboard` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `bonus_type` varchar(30) NOT NULL DEFAULT '',
  `rank` smallint(3) unsigned NOT NULL DEFAULT 0,
  `total` mediumint(7) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`model_id`,`bonus_type`,`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Colombian_Thing_Rankings`
--

DROP TABLE IF EXISTS `Colombian_Thing_Rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Colombian_Thing_Rankings` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `rank` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `total_credits` mediumint(7) NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FFOTM_Approvals`
--

DROP TABLE IF EXISTS `FFOTM_Approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FFOTM_Approvals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year_month` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `year_month` (`year_month`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FFOTM_Rankings`
--

DROP TABLE IF EXISTS `FFOTM_Rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FFOTM_Rankings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `year_month` char(7) NOT NULL DEFAULT '0000-00',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `rank` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `direction` enum('up','down','no_change') NOT NULL DEFAULT 'no_change',
  PRIMARY KEY (`id`),
  KEY `category` (`year_month`),
  KEY `model_id` (`model_id`,`year_month`)
) ENGINE=InnoDB AUTO_INCREMENT=1160290 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTM_Approvals`
--

DROP TABLE IF EXISTS `FOTM_Approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTM_Approvals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year_month` char(7) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `year_month` (`year_month`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTM_Rankings`
--

DROP TABLE IF EXISTS `FOTM_Rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTM_Rankings` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `year_month` char(7) NOT NULL DEFAULT '0000-00',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `rank` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `direction` enum('up','down','no_change') NOT NULL DEFAULT 'no_change',
  PRIMARY KEY (`model_id`,`year_month`),
  KEY `category` (`year_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Camstar`
--

DROP TABLE IF EXISTS `FOTY_Camstar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Camstar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Change_Log`
--

DROP TABLE IF EXISTS `FOTY_Change_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Change_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `description` text NOT NULL,
  `service` enum('girls','guys') DEFAULT NULL,
  `type` enum('random','dynamic','data') DEFAULT NULL,
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Country`
--

DROP TABLE IF EXISTS `FOTY_Country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `region` varchar(15) NOT NULL DEFAULT '',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB AUTO_INCREMENT=19132 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Country_OptOut`
--

DROP TABLE IF EXISTS `FOTY_Country_OptOut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Country_OptOut` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `optout_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Models`
--

DROP TABLE IF EXISTS `FOTY_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Models` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `credit_level` varchar(20) NOT NULL DEFAULT '',
  `seconds_online` int(11) unsigned NOT NULL DEFAULT 0,
  `total_credits` int(8) unsigned NOT NULL DEFAULT 0,
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service` (`service`),
  KEY `credit_level` (`credit_level`),
  KEY `model_id` (`model_id`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB AUTO_INCREMENT=15264 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Rankings`
--

DROP TABLE IF EXISTS `FOTY_Rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Rankings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `category` varchar(50) NOT NULL DEFAULT '',
  `rank` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `direction` enum('up','down','no_change') NOT NULL DEFAULT 'no_change',
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `model_id` (`model_id`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB AUTO_INCREMENT=23380103 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Rankings_Archive`
--

DROP TABLE IF EXISTS `FOTY_Rankings_Archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Rankings_Archive` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `category` varchar(50) NOT NULL DEFAULT '',
  `rank` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `direction` enum('up','down','no_change') NOT NULL DEFAULT 'no_change',
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `model_id` (`model_id`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Room_Notifications`
--

DROP TABLE IF EXISTS `FOTY_Room_Notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Room_Notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notification_data` text NOT NULL,
  `status` enum('sent','pending','queued') NOT NULL DEFAULT 'pending',
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `last_updated` (`last_updated`),
  KEY `status` (`status`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB AUTO_INCREMENT=335296 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FOTY_Viewers_Choice`
--

DROP TABLE IF EXISTS `FOTY_Viewers_Choice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FOTY_Viewers_Choice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `contest_year` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `contest_year` (`contest_year`)
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Summary`
--

DROP TABLE IF EXISTS `Models_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Summary` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `pay_period_id` smallint(4) NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL,
  `broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
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
  `total_credits` int(11) NOT NULL DEFAULT 0,
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
  `seconds_fake_overcount` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `deal_code_deductions` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `on_sgg` enum('Y','N') NOT NULL DEFAULT 'N',
  `fanclub_referred_credits` mediumint(7) NOT NULL DEFAULT 0,
  `fanclub_referred_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `vod_referred_credits` mediumint(7) NOT NULL DEFAULT 0,
  `vod_referred_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `display_fanclub_commission_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_commission_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `display_vod_commission_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_commission_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `display_premium_conversion_commission_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `on_club` enum('Y','N') NOT NULL DEFAULT 'N',
  `display_fanclub_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_fanclub_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_vod_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_vod_referred_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_member_bonus_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `display_premium_conversion_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `mainstage_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `on_asia` enum('Y','N') NOT NULL DEFAULT 'N',
  `premium_conversion_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `model_access_image_message_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `model_access_image_message_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `model_access_text_message_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `model_access_text_message_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `display_model_access_commission_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `display_model_access_commission` float(10,2) NOT NULL DEFAULT 0.00,
  `lifetime_total_credits` int(8) unsigned NOT NULL DEFAULT 0,
  `model_access_video_message_refunds` mediumint(7) NOT NULL DEFAULT 0,
  `model_access_video_message_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `progressive_level` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Currently reached TIER within the associated bi-weekly period (applies to weekly models as well).',
  `progressive_net_credits` int(10) NOT NULL DEFAULT 0 COMMENT 'Currently earned CREDITS within the associated bi-weekly period (applies to weekly models as well) AFTER progressive scale rates are applied.',
  `progressive_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'The INDIVIDUAL RATE of the currently reached tier within the associated bi-weekly period (applies to weekly models as well).',
  `progressive_total_credits` int(10) NOT NULL DEFAULT 0 COMMENT 'Currently earned CREDITS within the associated bi-weekly period (applies to weekly models as well).  ',
  `progressive_commission` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'How much was earned in dollars via progressive scale',
  `progressive_member_bonus_commission` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'How much was earned in dollars from member bonus',
  `progressive_premium_conversion_commission` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'How much was earned in dollars from premium conversion',
  `progressive_total_payout` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'How much was earned in dollars from the progressive scale + member bonus + premium conversion +- refunds',
  PRIMARY KEY (`studio`,`model_id`,`date`,`platform`),
  KEY `pay_period_id` (`pay_period_id`),
  KEY `broadcaster_id` (`broadcaster_id`),
  KEY `total_credits` (`total_credits`),
  KEY `date` (`date`),
  KEY `model_id` (`model_id`),
  KEY `studio` (`studio`),
  KEY `service` (`service`),
  KEY `broadcast_country` (`broadcast_country`),
  KEY `cmodel_id` (`model_id`,`broadcaster_id`,`pay_period_id`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Models_Summary_by_Week`
--

DROP TABLE IF EXISTS `Models_Summary_by_Week`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Models_Summary_by_Week` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pay_period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `week` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `broadcaster_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) NOT NULL DEFAULT '',
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_name` varchar(100) NOT NULL DEFAULT '',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `percentile` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `lifetime_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `days_worked` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `seconds_online` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `seconds_break` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `seconds_show` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `seconds_fake` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `show_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `show_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `gift_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `gift_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `flirt_perk_credits` mediumint(9) NOT NULL DEFAULT 0,
  `flirt_phone_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `flirt_phone_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `flirt_sms_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `flirt_sms_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `photo_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `photo_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `bonus_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `bonus_credits_guest` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `bonus_credits_regular` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `fanclub_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `fanclub_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `vod_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `vod_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `fanclub_referred_credits` mediumint(9) NOT NULL DEFAULT 0,
  `fanclub_referred_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `vod_referred_credits` mediumint(9) NOT NULL DEFAULT 0,
  `vod_referred_refunds` mediumint(9) NOT NULL DEFAULT 0,
  `violations` float(10,2) NOT NULL DEFAULT 0.00,
  `violation_refunds` float(10,2) NOT NULL DEFAULT 0.00,
  `credit_adjustments` mediumint(9) NOT NULL DEFAULT 0,
  `total_credits` int(11) NOT NULL DEFAULT 0,
  `total_standard_credits` int(11) NOT NULL DEFAULT 0,
  `total_50_credits` int(11) NOT NULL DEFAULT 0,
  `gross_sales` float(10,2) NOT NULL DEFAULT 0.00,
  `manual_adjustments` float(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `pay_period` (`pay_period_id`,`week`),
  KEY `broadcaster_id` (`broadcaster_id`,`pay_period_id`,`week`),
  KEY `studio` (`studio`,`pay_period_id`,`week`),
  KEY `model_id` (`model_id`,`pay_period_id`,`week`),
  KEY `service` (`service`,`pay_period_id`,`week`),
  KEY `percentile` (`percentile`,`pay_period_id`,`week`)
) ENGINE=InnoDB AUTO_INCREMENT=23027 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `New_Performers`
--

DROP TABLE IF EXISTS `New_Performers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `New_Performers` (
  `model_id` int(11) unsigned NOT NULL,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `location` enum('studio','home') NOT NULL DEFAULT 'studio',
  `date_acquired` date NOT NULL DEFAULT '0000-00-00',
  `country` char(2) NOT NULL DEFAULT '',
  `is_solo` enum('Y','N') NOT NULL DEFAULT 'N',
  `original_studio` char(12) NOT NULL DEFAULT '',
  `original_broadcaster_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `first_broadcast_date` date NOT NULL DEFAULT '0000-00-00',
  `first_broadcast_days` smallint(3) unsigned NOT NULL DEFAULT 0,
  `first_broadcast_login_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `first_shift_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_shift_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_shift_cpm` smallint(3) unsigned NOT NULL DEFAULT 0,
  `first_shift_seconds` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `first_shift_credits` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `first_shift_cph` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `first_20hr_seconds` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `first_20hr_days` smallint(3) unsigned NOT NULL DEFAULT 0,
  `first_20hr_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_20hr_credits` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `first_20hr_cph` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `first_show_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_show_seconds` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `first_tip_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_tip_seconds` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `datetime_new_ended` datetime DEFAULT NULL,
  PRIMARY KEY (`model_id`),
  KEY `date_acquired` (`date_acquired`),
  KEY `service` (`service`),
  KEY `date_acquired_datetime_new_ended` (`date_acquired`,`datetime_new_ended`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recruiter_Commission`
--

DROP TABLE IF EXISTS `Recruiter_Commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recruiter_Commission` (
  `period_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `salesperson_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `event` varchar(50) NOT NULL DEFAULT '',
  `date_qualified` date NOT NULL DEFAULT '0000-00-00',
  `commission` float(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`model_id`,`event`),
  KEY `period_id` (`period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recruiter_Commission_Events`
--

DROP TABLE IF EXISTS `Recruiter_Commission_Events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recruiter_Commission_Events` (
  `event` varchar(50) NOT NULL DEFAULT '',
  `display_name` varchar(50) NOT NULL DEFAULT '',
  `commission` float(5,2) NOT NULL DEFAULT 0.00,
  `sort_priority` tinyint(2) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`event`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Romanian_Contest_Rankings`
--

DROP TABLE IF EXISTS `Romanian_Contest_Rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Romanian_Contest_Rankings` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `rank` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `total_credits` mediumint(7) NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `View_Share_Daily`
--

DROP TABLE IF EXISTS `View_Share_Daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `View_Share_Daily` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `pct_of_credits` float(6,5) unsigned NOT NULL DEFAULT 0.00000,
  `earned_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `bonus_credits` mediumint(7) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `model_id` (`model_id`),
  KEY `service_date` (`date`,`service`)
) ENGINE=InnoDB AUTO_INCREMENT=1233363 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 17:38:16
