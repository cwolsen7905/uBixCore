/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: VSCASH_STATS
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
-- Table structure for table `CPA_MTP_Data`
--

DROP TABLE IF EXISTS `CPA_MTP_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CPA_MTP_Data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_type` varchar(255) NOT NULL DEFAULT '''CPA'',''CPL'',''PPS''',
  `site_type` varchar(255) NOT NULL DEFAULT '',
  `countries` varchar(255) NOT NULL DEFAULT 'all',
  `device` enum('all','mobile','desktop') NOT NULL DEFAULT 'all',
  `excluded_deals` varchar(255) NOT NULL DEFAULT '',
  `included_deals` varchar(255) NOT NULL DEFAULT '',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `F1` varchar(12) NOT NULL DEFAULT '',
  `F2` float(8,2) DEFAULT NULL,
  `F3` float(8,2) DEFAULT NULL,
  `F4` float(8,2) DEFAULT NULL,
  `F5` float(8,2) DEFAULT NULL,
  `F6` float(8,2) DEFAULT NULL,
  `F7` float(8,2) DEFAULT NULL,
  `F8` float(8,2) DEFAULT NULL,
  `F9` float(8,2) DEFAULT NULL,
  `F10` float(8,2) DEFAULT NULL,
  `F11` float(8,2) DEFAULT NULL,
  `F12` float(8,2) DEFAULT NULL,
  `F13` float(8,2) DEFAULT NULL,
  `F14` float(8,2) DEFAULT NULL,
  `F15` float(8,2) DEFAULT NULL,
  `F16` float(8,2) DEFAULT NULL,
  `F17` float(8,2) DEFAULT NULL,
  `F18` float(8,2) DEFAULT NULL,
  `F19` float(8,2) DEFAULT NULL,
  `F20` float(8,2) DEFAULT NULL,
  `F21` float(8,2) DEFAULT NULL,
  `F22` float(8,2) DEFAULT NULL,
  `F23` float(8,2) DEFAULT NULL,
  `F24` float(8,2) DEFAULT NULL,
  `F25` float(8,2) DEFAULT NULL,
  `F26` float(8,2) DEFAULT NULL,
  `F27` float(8,2) DEFAULT NULL,
  `F28` float(8,2) DEFAULT NULL,
  `F29` float(8,2) DEFAULT NULL,
  `F30` float(8,2) DEFAULT NULL,
  `F31` float(8,2) DEFAULT NULL,
  `F32` float(8,2) DEFAULT NULL,
  `F33` float(8,2) DEFAULT NULL,
  `F34` float(8,2) DEFAULT NULL,
  `F35` float(8,2) DEFAULT NULL,
  `F36` float(8,2) DEFAULT NULL,
  `F37` float(8,2) DEFAULT NULL,
  `F38` float(8,2) DEFAULT NULL,
  `F39` float(8,2) DEFAULT NULL,
  `F40` float(8,2) DEFAULT NULL,
  `F41` float(8,2) DEFAULT NULL,
  `F42` float(8,2) DEFAULT NULL,
  `F43` float(8,2) DEFAULT NULL,
  `F44` float(8,2) DEFAULT NULL,
  `F45` float(8,2) DEFAULT NULL,
  `F46` float(8,2) DEFAULT NULL,
  `F47` float(8,2) DEFAULT NULL,
  `F48` float(8,2) DEFAULT NULL,
  `F49` float(8,2) DEFAULT NULL,
  `F50` float(8,2) DEFAULT NULL,
  `F51` float(8,2) DEFAULT NULL,
  `F52` float(8,2) DEFAULT NULL,
  `F53` float(8,2) DEFAULT NULL,
  `F54` float(8,2) DEFAULT NULL,
  `F55` float(8,2) DEFAULT NULL,
  `F56` float(8,2) DEFAULT NULL,
  `F57` float(8,2) DEFAULT NULL,
  `F58` float(8,2) DEFAULT NULL,
  `F59` float(8,2) DEFAULT NULL,
  `F60` float(8,2) DEFAULT NULL,
  `F61` float(8,2) DEFAULT NULL,
  `F62` float(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2315267 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Dynamic_Ads_RT`
--

DROP TABLE IF EXISTS `Dynamic_Ads_RT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Dynamic_Ads_RT` (
  `md5_hash` varchar(32) NOT NULL,
  `minute` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `style` varchar(50) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `script` varchar(50) NOT NULL DEFAULT 'banners/LiveWebcams.php',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `whitelabel_domain` varchar(50) NOT NULL DEFAULT '',
  `referrer_domain` varchar(255) NOT NULL DEFAULT '',
  `referrer` varchar(255) NOT NULL DEFAULT '',
  `impressions` int(11) unsigned NOT NULL DEFAULT 0,
  `clicks` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`md5_hash`),
  KEY `minute` (`minute`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Gallery_Loads`
--

DROP TABLE IF EXISTS `Gallery_Loads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Gallery_Loads` (
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `keyword_string` varchar(150) NOT NULL DEFAULT '',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `raw_hits` int(11) unsigned NOT NULL DEFAULT 0,
  `unique_hits` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`md5_key`),
  KEY `date` (`date`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `service` (`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `IFRAME_Loads`
--

DROP TABLE IF EXISTS `IFRAME_Loads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `IFRAME_Loads` (
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `script` varchar(50) NOT NULL DEFAULT 'banners/LiveWebCams.php',
  `style` varchar(50) NOT NULL DEFAULT '',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `width` smallint(4) unsigned NOT NULL DEFAULT 0,
  `height` smallint(4) unsigned NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `impressions` int(11) unsigned NOT NULL DEFAULT 0,
  `clicks` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`md5_key`),
  KEY `date` (`date`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `service` (`service`),
  KEY `style` (`style`),
  KEY `sitekey` (`sitekey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `IFRAME_Refs`
--

DROP TABLE IF EXISTS `IFRAME_Refs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `IFRAME_Refs` (
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `referrer` varchar(255) NOT NULL DEFAULT '',
  `impressions` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`md5_key`),
  KEY `date` (`date`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `referrer` (`referrer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MTP_Regression`
--

DROP TABLE IF EXISTS `MTP_Regression`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MTP_Regression` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_type` varchar(255) NOT NULL DEFAULT '',
  `countries` varchar(255) NOT NULL DEFAULT 'all',
  `device` enum('all','mobile','desktop') NOT NULL DEFAULT 'all',
  `month_offset` int(11) NOT NULL DEFAULT 0,
  `regression` float(3,1) NOT NULL DEFAULT 0.0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=288421 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2004_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2004_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2004_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55548 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2004_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2004_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2004_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=70866 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=109686 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=120259 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=146958 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=118395 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88183 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92287 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=99256 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92940 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=177037 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=184571 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=185839 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2005_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2005_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2005_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=191243 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=160837 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129573 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=158365 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141693 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=133529 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=126387 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=153369 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=183283 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=168470 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=179428 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=172467 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2006_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2006_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2006_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=185541 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=190477 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=180340 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=206524 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=208809 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=212910 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=204199 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=221241 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=211525 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=198800 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=196510 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=188975 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2007_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2007_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2007_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=189752 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=167151 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=133404 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=114889 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=110814 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=148443 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=114820 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=115555 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=114044 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113343 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=144494 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=147830 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2008_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2008_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2008_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=189575 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=208859 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=202247 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=363996 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=390536 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=403210 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=391120 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=408408 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=413574 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=410614 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=421268 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=393016 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2009_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2009_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2009_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=385154 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=379746 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=342650 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=388500 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=373380 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=382376 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=399118 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=400598 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=393380 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=383606 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=395954 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=380892 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2010_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2010_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2010_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=379588 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=384638 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=340132 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=367842 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=361710 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=385978 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=372772 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=382508 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=394312 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=391690 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=402030 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=383818 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2011_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2011_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2011_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=388384 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=397734 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=359308 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=379456 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=364312 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=380418 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=373868 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=401010 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=375656 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=362454 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=421310 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=485024 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2012_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2012_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2012_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=401268 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=361250 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=327756 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=359862 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_03_New`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_03_New`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_03_New` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=283988 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=363828 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_04_New`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_04_New`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_04_New` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=97170 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=392334 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=360918 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=359744 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=368274 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=369008 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=397834 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=387672 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2013_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2013_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2013_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=414042 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=421412 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=378120 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=421188 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=335904 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=563300 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=643122 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=659204 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=661344 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=649568 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=617720 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=605926 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2014_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2014_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2014_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=680106 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=772866 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=712670 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=708124 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=591928 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=610696 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=617410 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=682180 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=664678 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=637044 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=593616 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=568156 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2015_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2015_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2015_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=598358 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=655064 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=591762 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=650596 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=555188 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=609980 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=691320 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=667562 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=560348 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=916050 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=871514 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=739382 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2016_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2016_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2016_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=797164 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=839126 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=889500 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=774156 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=743339 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=839098 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=833592 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1081934 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1161192 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1112838 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=900662 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1026376 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2017_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2017_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2017_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=783644 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=736970 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=701168 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=742490 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=751616 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=694354 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=632384 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=657358 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=653806 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=693640 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=819382 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1051418 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2018_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2018_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2018_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3047970 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3185588 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2905928 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3619346 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3784414 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1791937 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=723457 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=883196 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2635671 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3462369 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3059976 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1616641 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2019_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2019_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2019_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1922122 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2114545 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1973649 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1473290 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1636112 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1559512 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1501448 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1817798 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2036658 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1802473 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1683572 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1742077 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2020_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2020_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2020_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1408739 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1063841 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=794878 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=912839 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=790685 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=412524 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=379813 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=404567 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=469260 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=417177 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=452535 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=442408 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2021_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2021_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2021_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=462286 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=316492 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=372017 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=359688 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=220471 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=232644 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=231514 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=237731 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=243862 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=231869 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=262172 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=245525 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2022_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2022_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2022_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=245782 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=230195 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=204764 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=238012 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=236367 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=247529 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=247333 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=253501 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=237045 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=229085 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=252981 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=248310 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2023_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2023_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2023_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=279806 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=281999 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=271471 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=296695 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=284598 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=311628 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=297579 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=293269 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=304926 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=294788 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=308111 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=314890 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2024_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2024_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2024_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=309408 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_01`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_01` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(20) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=306029 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_02`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_02` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=278356 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_03`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_03` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=275739 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_04`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_04` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=261244 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_05`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_05` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=271515 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_06`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=249157 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_07`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_07` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=254708 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_08`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_08` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=236574 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_09`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_09` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=218057 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_10`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_10` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=186508 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_11`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_11` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_2025_12`
--

DROP TABLE IF EXISTS `Traffic_Summary_2025_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_2025_12` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2004`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2004`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2004` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=465 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2005`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2005`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2005` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=9215 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2006`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2006`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2006` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=30575 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2007`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2007`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2007` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=42941 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2008`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2008`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2008` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=42008 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2009`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2009`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2009` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=72564 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2010`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2010`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2010` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=78732 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2011`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2011`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2011` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=78102 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2012`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2012`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2012` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=71236 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2013`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2013` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=5376 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2013_New`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2013_New`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2013_New` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2014`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2014`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2014` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=9750 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2015`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2015`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2015` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4378 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2016`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2016`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2016` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4392 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2017`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2017`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2017` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4376 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2018`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2018`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2018` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4380 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2019`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2019`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2019` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=2976 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2020`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2020`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2020` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=3679 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2021`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2021`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2021` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4381 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2022`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2022`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2022` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4381 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2023`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2023`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2023` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4381 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2024`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2024`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2024` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=4393 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Traffic_Summary_By_Site_2025`
--

DROP TABLE IF EXISTS `Traffic_Summary_By_Site_2025`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Traffic_Summary_By_Site_2025` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `hits_unique` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `unique_mps` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`date`,`type`,`identifier`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=3577 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rafael_test_2020_06`
--

DROP TABLE IF EXISTS `rafael_test_2020_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rafael_test_2020_06` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2894179 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rafael_test_2020_06_a`
--

DROP TABLE IF EXISTS `rafael_test_2020_06_a`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rafael_test_2020_06_a` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6172747 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rafael_test_2020_06_b`
--

DROP TABLE IF EXISTS `rafael_test_2020_06_b`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rafael_test_2020_06_b` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `mp_parent` varchar(5) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type` varchar(8) NOT NULL DEFAULT 'paysites',
  `identifier` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `page` varchar(25) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT 'girls',
  `hits_raw` mediumint(8) unsigned NOT NULL,
  `hits_unique` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mp_source` (`mp_code`,`date`,`type`,`identifier`,`page`,`service`),
  KEY `date` (`date`),
  KEY `mp_parent` (`mp_parent`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17574 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 17:38:42
