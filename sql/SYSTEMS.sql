/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: SYSTEMS
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
-- Table structure for table `Blacklisted_IPs`
--

DROP TABLE IF EXISTS `Blacklisted_IPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blacklisted_IPs` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(64) NOT NULL DEFAULT '',
  `subnet_cidr` tinyint(3) unsigned NOT NULL DEFAULT 32,
  `duration` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `duration_type` enum('minutes','hours','days') NOT NULL DEFAULT 'minutes',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_applied` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_block_by` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_address` (`ip_address`,`subnet_cidr`),
  KEY `status` (`status`),
  KEY `date_expires` (`date_expires`),
  KEY `date_block_by` (`date_block_by`)
) ENGINE=InnoDB AUTO_INCREMENT=33543 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Blacklisted_IPs_Notes`
--

DROP TABLE IF EXISTS `Blacklisted_IPs_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blacklisted_IPs_Notes` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`ip_id`)
) ENGINE=InnoDB AUTO_INCREMENT=122588 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CDNS`
--

DROP TABLE IF EXISTS `CDNS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CDNS` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `short_name` varchar(8) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `deprecated` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `video_domain` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `deprecated` (`deprecated`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CDNS_Platforms`
--

DROP TABLE IF EXISTS `CDNS_Platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CDNS_Platforms` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cdn_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `platform_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `website_domain` varchar(128) NOT NULL DEFAULT '',
  `website_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `video_status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `internal_test` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cdn_platform` (`cdn_id`,`platform_id`),
  KEY `platform` (`platform_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Clusters`
--

DROP TABLE IF EXISTS `Clusters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Clusters` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `php_version` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Computer_Device_MAC`
--

DROP TABLE IF EXISTS `Computer_Device_MAC`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Computer_Device_MAC` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `computer_device_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `mac_address` varchar(24) NOT NULL DEFAULT '',
  `description` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `computer_device_id` (`computer_device_id`),
  KEY `mac_address` (`mac_address`)
) ENGINE=InnoDB AUTO_INCREMENT=1272 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Computer_Devices`
--

DROP TABLE IF EXISTS `Computer_Devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Computer_Devices` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `vlan_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=618 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cron_Jobs`
--

DROP TABLE IF EXISTS `Cron_Jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cron_Jobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `department` varchar(32) DEFAULT '',
  `user` varchar(16) DEFAULT '',
  `notify` varchar(50) NOT NULL DEFAULT 'cron-errors@vsmedia.pagerduty.com',
  `path` varchar(250) NOT NULL DEFAULT '',
  `type` enum('php','shell','bash','cli','perl','python','python3') NOT NULL DEFAULT 'php',
  `arguments` varchar(255) DEFAULT NULL,
  `min` varchar(12) NOT NULL DEFAULT '*',
  `hour` varchar(32) NOT NULL DEFAULT '*',
  `date` varchar(12) NOT NULL DEFAULT '*',
  `month` varchar(12) NOT NULL DEFAULT '*',
  `weekday` varchar(12) NOT NULL DEFAULT '*',
  `run_once` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `last_run` datetime DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime DEFAULT current_timestamp(),
  `notification_type_id` smallint(5) unsigned NOT NULL,
  `email_subject` varchar(255) NOT NULL DEFAULT '',
  `max_runtime` smallint(6) NOT NULL DEFAULT 5,
  `is_external` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `path` (`path`),
  KEY `status` (`status`),
  KEY `last_updated` (`last_updated`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=2208 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cron_Jobs_Change_Log`
--

DROP TABLE IF EXISTS `Cron_Jobs_Change_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cron_Jobs_Change_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cron_id` int(11) unsigned NOT NULL,
  `admin_user_id` smallint(3) unsigned NOT NULL,
  `status` enum('update','delete') NOT NULL DEFAULT 'update',
  `updated` datetime DEFAULT '0000-00-00 00:00:00',
  `changes_json` text NOT NULL,
  `change_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cron_id` (`cron_id`),
  KEY `admin_user_id` (`admin_user_id`),
  KEY `updated` (`updated`)
) ENGINE=InnoDB AUTO_INCREMENT=6846 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cron_Jobs_Hosts`
--

DROP TABLE IF EXISTS `Cron_Jobs_Hosts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cron_Jobs_Hosts` (
  `cron_id` int(11) unsigned NOT NULL DEFAULT 0,
  `host_type` enum('Servers','Clusters') NOT NULL DEFAULT 'Servers',
  `host_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`cron_id`,`host_type`,`host_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cron_Jobs_Logs`
--

DROP TABLE IF EXISTS `Cron_Jobs_Logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cron_Jobs_Logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cron_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `message` text NOT NULL,
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `datetime_start` (`datetime_start`),
  KEY `cron_id_end` (`cron_id`,`datetime_end`),
  KEY `server_cron_id_end` (`server_id`,`cron_id`,`datetime_end`),
  KEY `indx_id_start` (`cron_id`,`datetime_start`),
  KEY `indx_id_time` (`server_id`,`cron_id`,`datetime_start`),
  KEY `indx_date` (`datetime_end`,`datetime_start`)
) ENGINE=InnoDB AUTO_INCREMENT=938939763 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cron_Jobs_Redundancy_Limiter`
--

DROP TABLE IF EXISTS `Cron_Jobs_Redundancy_Limiter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cron_Jobs_Redundancy_Limiter` (
  `cron_id` int(10) unsigned NOT NULL DEFAULT 0,
  `datetime_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compid` (`cron_id`,`datetime_start`),
  KEY `datetime_start` (`datetime_start`)
) ENGINE=InnoDB AUTO_INCREMENT=449736 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Deploy_Log`
--

DROP TABLE IF EXISTS `Deploy_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Deploy_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `file_path_md5` varchar(32) NOT NULL DEFAULT '',
  `file_cksum` varchar(32) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `revision_num` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `action` varchar(20) NOT NULL DEFAULT 'publish',
  `destination` varchar(30) NOT NULL DEFAULT 'staging',
  `comments` text DEFAULT NULL,
  `target_hosts` varchar(32) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `file_path_md5` (`file_path_md5`)
) ENGINE=InnoDB AUTO_INCREMENT=765941 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `IP_Unpack_Test`
--

DROP TABLE IF EXISTS `IP_Unpack_Test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `IP_Unpack_Test` (
  `ip_address` varbinary(39) NOT NULL,
  `expires` float(13,3) NOT NULL DEFAULT 0.000,
  `inserted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Maintenance_Windows`
--

DROP TABLE IF EXISTS `Maintenance_Windows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Maintenance_Windows` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) NOT NULL DEFAULT 0,
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `service` varchar(20) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `active` tinyint(1) DEFAULT 0,
  `message` text NOT NULL,
  `memcache_ttl` int(11) DEFAULT 1200,
  PRIMARY KEY (`id`),
  KEY `status` (`service`,`active`),
  KEY `item` (`service`,`active`,`start_time`,`end_time`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Query_Tool_Categories`
--

DROP TABLE IF EXISTS `Query_Tool_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Query_Tool_Categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `category_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_id_2` (`admin_id`,`category_name`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Query_Tool_Saved_Queries`
--

DROP TABLE IF EXISTS `Query_Tool_Saved_Queries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Query_Tool_Saved_Queries` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `category_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `run_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `query_name` varchar(100) NOT NULL DEFAULT '',
  `query` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`,`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
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
  `num_reads` int(11) unsigned NOT NULL DEFAULT 0,
  `num_writes` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`hostname`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SSL_Cert_Domains`
--

DROP TABLE IF EXISTS `SSL_Cert_Domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SSL_Cert_Domains` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `site_type` enum('internal','external') NOT NULL DEFAULT 'internal',
  `type` enum('root','sub-domain','wildcard') NOT NULL DEFAULT 'root',
  `provider` enum('le','godaddy') NOT NULL DEFAULT 'le',
  `date_renewed` date NOT NULL DEFAULT '2000-01-01',
  `date_expires` date NOT NULL DEFAULT '0000-00-00',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`),
  KEY `provider` (`provider`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Server_Stats`
--

DROP TABLE IF EXISTS `Server_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Server_Stats` (
  `host` varchar(50) NOT NULL DEFAULT '',
  `minute` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mem_total` int(10) NOT NULL DEFAULT 0,
  `mem_free` int(10) NOT NULL DEFAULT 0,
  `mem_used` int(10) NOT NULL DEFAULT 0,
  `apache_processes` int(10) NOT NULL DEFAULT 0,
  `apache_mem_per_proc` int(10) NOT NULL DEFAULT 0,
  `apache_proc_up_time` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`host`,`minute`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Servers`
--

DROP TABLE IF EXISTS `Servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Servers` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cluster_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `host` varchar(64) NOT NULL DEFAULT '',
  `ipv4_public` varchar(15) NOT NULL DEFAULT '',
  `ipv4_private` varchar(15) NOT NULL DEFAULT '',
  `sync_user` varchar(20) NOT NULL DEFAULT 'developers',
  `status` enum('online','offline','maintenance') NOT NULL DEFAULT 'offline',
  PRIMARY KEY (`id`),
  UNIQUE KEY `host` (`host`)
) ENGINE=InnoDB AUTO_INCREMENT=580 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Shell_Exec_Schedule`
--

DROP TABLE IF EXISTS `Shell_Exec_Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Shell_Exec_Schedule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL DEFAULT '',
  `arguments` varchar(255) NOT NULL DEFAULT '',
  `created_datetime` datetime DEFAULT current_timestamp(),
  `executed_datetime` datetime DEFAULT NULL,
  `is_executed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=285 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Snowball_Logs`
--

DROP TABLE IF EXISTS `Snowball_Logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Snowball_Logs` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `server` varchar(255) NOT NULL DEFAULT '',
  `service` varchar(20) NOT NULL DEFAULT '',
  `num_requests` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`server`,`service`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Settings`
--

DROP TABLE IF EXISTS `System_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL DEFAULT '',
  `value` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Text_Filters`
--

DROP TABLE IF EXISTS `Text_Filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Text_Filters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL,
  `search_method` varchar(32) NOT NULL,
  `replace_method` varchar(32) NOT NULL,
  `date_expires` date DEFAULT NULL,
  `is_global` enum('Y','N') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date_expires` (`date_expires`)
) ENGINE=InnoDB AUTO_INCREMENT=8428 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Text_Filters_Log`
--

DROP TABLE IF EXISTS `Text_Filters_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Text_Filters_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned DEFAULT 0,
  `message` text DEFAULT NULL,
  `datetime_changed` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `datetime_changed` (`datetime_changed`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Text_Filters_Models`
--

DROP TABLE IF EXISTS `Text_Filters_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Text_Filters_Models` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL,
  `word` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `model_index` (`model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19338 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Text_Filters_Zones`
--

DROP TABLE IF EXISTS `Text_Filters_Zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Text_Filters_Zones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Text_Filters_Zones_Join`
--

DROP TABLE IF EXISTS `Text_Filters_Zones_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Text_Filters_Zones_Join` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) unsigned DEFAULT NULL,
  `filter_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comp_id` (`zone_id`,`filter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1676 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Uptime_Monitors`
--

DROP TABLE IF EXISTS `Uptime_Monitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Uptime_Monitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `check_dom_class` varchar(255) NOT NULL DEFAULT '',
  `check_dom_id` varchar(255) NOT NULL DEFAULT '',
  `recipients` varchar(255) NOT NULL DEFAULT '',
  `time_interval` int(11) NOT NULL DEFAULT 10,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VLANS`
--

DROP TABLE IF EXISTS `VLANS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VLANS` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `location` enum('HQ','LA3') NOT NULL DEFAULT 'HQ',
  `vlan` smallint(5) unsigned NOT NULL DEFAULT 0,
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location` (`location`,`vlan`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Webserver_Ports`
--

DROP TABLE IF EXISTS `Webserver_Ports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Webserver_Ports` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `port_number` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  UNIQUE KEY `port_number` (`port_number`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Whitelisted_IPs`
--

DROP TABLE IF EXISTS `Whitelisted_IPs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Whitelisted_IPs` (
  `ip_address` varchar(32) NOT NULL DEFAULT '',
  `type` enum('internal','customer','broadcaster') NOT NULL DEFAULT 'internal',
  `type_details` varchar(16) NOT NULL DEFAULT '',
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`ip_address`),
  KEY `type_details` (`type`,`type_details`),
  KEY `status` (`status`)
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

-- Dump completed on 2025-10-25 17:38:27
