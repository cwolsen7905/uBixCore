/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: MAILINGS
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
-- Table structure for table `Affiliate_Subaccounts_Alerts`
--

DROP TABLE IF EXISTS `Affiliate_Subaccounts_Alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Subaccounts_Alerts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_subaccounts` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `alert_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `affiliate_numsubs_date` (`affiliate_id`,`num_subaccounts`,`alert_date`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Automated_Emails`
--

DROP TABLE IF EXISTS `Automated_Emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Automated_Emails` (
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `source_site_id` smallint(4) unsigned NOT NULL DEFAULT 1,
  `type` enum('triggered','engagement') NOT NULL DEFAULT 'triggered',
  `mailing_type_id` smallint(3) unsigned NOT NULL DEFAULT 100,
  `script_path` varchar(255) NOT NULL DEFAULT '',
  `template_path` varchar(255) NOT NULL DEFAULT '',
  `default_subject` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `split_test` varchar(255) NOT NULL DEFAULT '',
  `service` enum('girls','guys','girls_guys','trans') NOT NULL DEFAULT 'girls_guys',
  PRIMARY KEY (`identifier`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Automated_Engagement_Log`
--

DROP TABLE IF EXISTS `Automated_Engagement_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Automated_Engagement_Log` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`user_id`,`identifier`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Automated_Engagement_Schedule`
--

DROP TABLE IF EXISTS `Automated_Engagement_Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Automated_Engagement_Schedule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `spending_group` varchar(25) NOT NULL DEFAULT '',
  `source_site_id` smallint(4) unsigned NOT NULL DEFAULT 1,
  `day_number` smallint(3) unsigned NOT NULL DEFAULT 0,
  `hour` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `spending_group` (`spending_group`,`source_site_id`,`day_number`,`hour`)
) ENGINE=InnoDB AUTO_INCREMENT=5678 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Automated_Outgoing`
--

DROP TABLE IF EXISTS `Automated_Outgoing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Automated_Outgoing` (
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `spending_group` varchar(25) NOT NULL DEFAULT '',
  `day_number` smallint(3) unsigned NOT NULL DEFAULT 0,
  `hour` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL DEFAULT '',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `username` varchar(32) NOT NULL DEFAULT '',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `mailing_type_id` smallint(3) unsigned NOT NULL DEFAULT 100,
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `server` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`identifier`,`user_id`,`date`),
  KEY `server` (`server`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Automated_Stats`
--

DROP TABLE IF EXISTS `Automated_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Automated_Stats` (
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `spending_group` varchar(25) NOT NULL DEFAULT 'ALL_GROUPS',
  `day_number` smallint(3) unsigned NOT NULL DEFAULT 0,
  `hour` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `num_recipients` int(11) unsigned NOT NULL DEFAULT 0,
  `num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  `num_bounces` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`identifier`,`date`,`spending_group`,`day_number`,`hour`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Banned`
--

DROP TABLE IF EXISTS `Banned`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Banned` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2013442 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Batch_Type`
--

DROP TABLE IF EXISTS `Batch_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Batch_Type` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Billing_Receipt_Log`
--

DROP TABLE IF EXISTS `Billing_Receipt_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Billing_Receipt_Log` (
  `address` varchar(255) NOT NULL DEFAULT '',
  `receipt_type` varchar(50) NOT NULL DEFAULT '',
  `num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`address`,`receipt_type`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Billing_Receipt_Stats`
--

DROP TABLE IF EXISTS `Billing_Receipt_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Billing_Receipt_Stats` (
  `receipt_type` varchar(50) NOT NULL DEFAULT '',
  `num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  `num_recipients` int(11) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`receipt_type`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Blocked_Email_Blast_Models`
--

DROP TABLE IF EXISTS `Blocked_Email_Blast_Models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Blocked_Email_Blast_Models` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_blocked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1572 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Broadcasting_Followup_Stats`
--

DROP TABLE IF EXISTS `Broadcasting_Followup_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Broadcasting_Followup_Stats` (
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `num_recipients` int(11) unsigned NOT NULL DEFAULT 0,
  `num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`identifier`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CamReg_API_Stats`
--

DROP TABLE IF EXISTS `CamReg_API_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CamReg_API_Stats` (
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `num_recipients` int(11) unsigned NOT NULL DEFAULT 0,
  `num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`identifier`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Journey_Events`
--

DROP TABLE IF EXISTS `Customer_Journey_Events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Journey_Events` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Journey_Events_To_Templates`
--

DROP TABLE IF EXISTS `Customer_Journey_Events_To_Templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Journey_Events_To_Templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` smallint(5) DEFAULT NULL,
  `template_id` smallint(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_template` (`event_id`,`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Journey_Templates`
--

DROP TABLE IF EXISTS `Customer_Journey_Templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Journey_Templates` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `body_html` mediumtext DEFAULT NULL,
  `body_plain` text DEFAULT NULL,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `subject` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Journey_User_List`
--

DROP TABLE IF EXISTS `Customer_Journey_User_List`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Journey_User_List` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL DEFAULT '',
  `event_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `template_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime_added` datetime DEFAULT NULL,
  `datetime_sent` datetime DEFAULT NULL,
  `subject` varchar(255) DEFAULT '',
  `user_is_unsubscribed` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_event` (`user_id`,`event_id`),
  KEY `event_id` (`event_id`),
  KEY `datetime_sent` (`datetime_sent`)
) ENGINE=InnoDB AUTO_INCREMENT=10055 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Notifications`
--

DROP TABLE IF EXISTS `Customer_Notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Notifications` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT 'online',
  `method` varchar(20) NOT NULL DEFAULT 'email',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `date_last_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_sent` mediumint(6) NOT NULL DEFAULT 0,
  `num_opens` mediumint(6) NOT NULL DEFAULT 0,
  `num_clicks` mediumint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`model_id`,`type`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Dynamic_List`
--

DROP TABLE IF EXISTS `Dynamic_List`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Dynamic_List` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `site_type` enum('adult','psychic') NOT NULL DEFAULT 'adult',
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `user_status` enum('pending','active') NOT NULL DEFAULT 'active',
  `spending_group` varchar(25) NOT NULL DEFAULT 'all',
  `is_customer` enum('Y','N','ALL') NOT NULL DEFAULT 'ALL',
  `vip` enum('Y','N','ALL') NOT NULL DEFAULT 'ALL',
  `total_spent` float(8,2) NOT NULL DEFAULT 0.00,
  `total_spent_flag` enum('greater_than','less_than','equal_to') NOT NULL DEFAULT 'greater_than',
  `days_last_purchase` smallint(3) NOT NULL DEFAULT 0,
  `days_last_flag` enum('greater_than','less_than','equal_to') NOT NULL DEFAULT 'greater_than',
  `days_created` smallint(3) unsigned NOT NULL DEFAULT 0,
  `days_created_flag` enum('greater_than','less_than','equal_to') NOT NULL DEFAULT 'greater_than',
  `list_sql` text DEFAULT NULL,
  `sitekey` text NOT NULL,
  `wl_domain` varchar(255) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_size` int(11) NOT NULL DEFAULT 0,
  `estimated_size` int(10) unsigned NOT NULL DEFAULT 0,
  `total_size` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=10859 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Mailing_Overview_Stats`
--

DROP TABLE IF EXISTS `Mailing_Overview_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Mailing_Overview_Stats` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `data_type` varchar(25) NOT NULL DEFAULT '',
  `value` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`data_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Mailing_Types`
--

DROP TABLE IF EXISTS `Model_Mailing_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Mailing_Types` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Model_Mailing_Unsubscribe`
--

DROP TABLE IF EXISTS `Model_Mailing_Unsubscribe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Model_Mailing_Unsubscribe` (
  `type_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `unsub_method` varchar(32) NOT NULL DEFAULT 'email_settings',
  PRIMARY KEY (`type_id`,`model_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Outgoing`
--

DROP TABLE IF EXISTS `Outgoing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Outgoing` (
  `schedule_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `address` varchar(255) NOT NULL DEFAULT '',
  `server` varchar(50) NOT NULL DEFAULT '',
  `priority` tinyint(1) NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '0000',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `domain` varchar(255) NOT NULL DEFAULT 'flirt4free.com',
  PRIMARY KEY (`schedule_id`,`address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Outgoing_Billing_Receipts`
--

DROP TABLE IF EXISTS `Outgoing_Billing_Receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Outgoing_Billing_Receipts` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `address` varchar(255) NOT NULL DEFAULT '',
  `server` varchar(50) NOT NULL DEFAULT '',
  `ref_transact_id` int(10) unsigned NOT NULL DEFAULT 0,
  `receipt_type` varchar(50) NOT NULL DEFAULT '',
  `language` varchar(2) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`user_id`,`address`,`ref_transact_id`),
  KEY `server` (`server`),
  KEY `receipt_type` (`receipt_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Outgoing_Emails`
--

DROP TABLE IF EXISTS `Outgoing_Emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Outgoing_Emails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `to_address` varchar(255) NOT NULL DEFAULT '',
  `to_name` varchar(255) DEFAULT '',
  `from_address` varchar(255) NOT NULL DEFAULT 'newsletters@vs.com',
  `from_name` varchar(255) DEFAULT NULL,
  `bcc` varchar(255) DEFAULT '',
  `reply_to` varchar(255) NOT NULL DEFAULT 'newsletters@vs.com',
  `return_path` varchar(255) DEFAULT 'newsletters@vs.com',
  `list_unsubscribe` text DEFAULT NULL,
  `custom_headers` varchar(255) NOT NULL DEFAULT '',
  `additional_headers` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body_html` longtext DEFAULT NULL,
  `body_plain` text DEFAULT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT 0,
  `server` varchar(50) NOT NULL DEFAULT '',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `queued_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dont_send_after` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `server_dsa` (`server`,`dont_send_after`,`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=661720104 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Outgoing_Forum_Notifications`
--

DROP TABLE IF EXISTS `Outgoing_Forum_Notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Outgoing_Forum_Notifications` (
  `forum_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `thread_id` int(11) unsigned NOT NULL DEFAULT 0,
  `post_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_type` enum('customer','model','studio_user') NOT NULL DEFAULT 'customer',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`forum_id`,`thread_id`,`post_id`,`user_type`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Outgoing_Notifications`
--

DROP TABLE IF EXISTS `Outgoing_Notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Outgoing_Notifications` (
  `model_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `address` varchar(255) NOT NULL DEFAULT '',
  `alert_type` varchar(20) NOT NULL DEFAULT '',
  `profile_type` varchar(20) NOT NULL DEFAULT '',
  `server` varchar(50) NOT NULL DEFAULT '',
  `priority` tinyint(1) NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '0000',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `domain` varchar(255) NOT NULL DEFAULT 'flirt4free.com',
  PRIMARY KEY (`model_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Overview_Stats`
--

DROP TABLE IF EXISTS `Overview_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Overview_Stats` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `data_type` varchar(25) NOT NULL DEFAULT '',
  `identifier` varchar(25) NOT NULL DEFAULT '',
  `sub_identifier` varchar(25) NOT NULL DEFAULT '',
  `num_emails` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`data_type`,`identifier`,`sub_identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Parked_Emails`
--

DROP TABLE IF EXISTS `Parked_Emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Parked_Emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `publisher` varchar(100) NOT NULL DEFAULT '',
  `identifier` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `service` enum('girls','guys') NOT NULL DEFAULT 'girls',
  `source_site_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `bounty_paid` char(1) NOT NULL DEFAULT 'N',
  `opt_in_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `opt_in_ip` varchar(20) NOT NULL DEFAULT '',
  `double_opt_in_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `double_opt_in_ip` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(10) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `publisher` (`publisher`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=31260 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Mailers_Log`
--

DROP TABLE IF EXISTS `Promo_Mailers_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Mailers_Log` (
  `type_id` smallint(3) unsigned NOT NULL DEFAULT 1001,
  `address` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `promo_mailer_id` (`type_id`,`address`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=2692757 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prospect_Followup_Log`
--

DROP TABLE IF EXISTS `Prospect_Followup_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Prospect_Followup_Log` (
  `address` varchar(255) NOT NULL DEFAULT '',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`address`,`date_sent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recurring`
--

DROP TABLE IF EXISTS `Recurring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recurring` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `day_of_week` varchar(14) NOT NULL DEFAULT '0',
  `hour_of_day` tinyint(2) NOT NULL DEFAULT 0,
  `list_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `msg_body_html` text DEFAULT NULL,
  `msg_body_plain` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Returned_Emails`
--

DROP TABLE IF EXISTS `Returned_Emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Returned_Emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `return_type` enum('bounce','complaint','unsubscribe') NOT NULL DEFAULT 'bounce',
  `email` varchar(255) NOT NULL DEFAULT '',
  `email_type` varchar(125) NOT NULL DEFAULT '',
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `server` varchar(128) NOT NULL DEFAULT '',
  `site_type` enum('','F4F','WL') NOT NULL DEFAULT '',
  `spending_group` varchar(25) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `date_bounceback` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`),
  KEY `tracking_id` (`tracking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49526603 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Returned_Emails_Incomplete`
--

DROP TABLE IF EXISTS `Returned_Emails_Incomplete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Returned_Emails_Incomplete` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `return_type` enum('bounce','complaint','unsubscribe') NOT NULL DEFAULT 'bounce',
  `email` varchar(255) NOT NULL DEFAULT '',
  `email_type` varchar(125) NOT NULL DEFAULT '',
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `server` varchar(128) NOT NULL DEFAULT '',
  `site_type` enum('','F4F','WL') NOT NULL DEFAULT '',
  `spending_group` varchar(25) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `date_bounceback` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`),
  KEY `tracking_id` (`tracking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Returnpath_Status`
--

DROP TABLE IF EXISTS `Returnpath_Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Returnpath_Status` (
  `report_date` date NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `provider` varchar(64) NOT NULL,
  `volume` int(10) unsigned NOT NULL,
  `complaints` int(10) unsigned NOT NULL,
  `state` varchar(15) NOT NULL,
  PRIMARY KEY (`report_date`,`ip_address`,`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Bounced_Log`
--

DROP TABLE IF EXISTS `SMTP_Bounced_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Bounced_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `num_bounced` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_esp_smtp_server_id_date` (`esp`,`smtp_server_id`,`date`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `date` (`date`),
  KEY `esp` (`esp`)
) ENGINE=InnoDB AUTO_INCREMENT=23548326 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Codes`
--

DROP TABLE IF EXISTS `SMTP_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Codes` (
  `code` smallint(3) unsigned NOT NULL,
  `ra_user_score` char(1) NOT NULL,
  `Unsubscribe` char(1) NOT NULL DEFAULT 'N',
  `User_List` varchar(20) NOT NULL DEFAULT 'active',
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Daily_Summary`
--

DROP TABLE IF EXISTS `SMTP_Daily_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Daily_Summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `spending_group` varchar(25) NOT NULL,
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_sent` int(11) unsigned NOT NULL DEFAULT 0,
  `num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  `num_bounced` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_date_identifier_spending_group_esp_smtp_type_smtp_server_id` (`date`,`identifier`,`spending_group`,`esp`,`smtp_type`,`smtp_server_id`),
  KEY `date` (`date`),
  KEY `date_identifier` (`date`,`identifier`),
  KEY `date_spending_group` (`date`,`spending_group`),
  KEY `date_esp` (`date`,`esp`),
  KEY `date_smtp_type` (`date`,`smtp_type`),
  KEY `date_smtp_server_id` (`date`,`smtp_server_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1076243287 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Daily_Summary_By_Spending_Group`
--

DROP TABLE IF EXISTS `SMTP_Daily_Summary_By_Spending_Group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Daily_Summary_By_Spending_Group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `spending_group` varchar(25) NOT NULL,
  `num_sent` int(11) unsigned NOT NULL DEFAULT 0,
  `num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_date_spending_group` (`date`,`spending_group`),
  KEY `date` (`date`),
  KEY `spending_group` (`spending_group`)
) ENGINE=InnoDB AUTO_INCREMENT=56532 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Email_Identifiers`
--

DROP TABLE IF EXISTS `SMTP_Email_Identifiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Email_Identifiers` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `risk_level` enum('low','medium','high') NOT NULL DEFAULT 'low',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Monthly_Detail`
--

DROP TABLE IF EXISTS `SMTP_Monthly_Detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Monthly_Detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `spending_group` varchar(25) NOT NULL,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_active_users` int(11) unsigned NOT NULL DEFAULT 0,
  `num_unsubscribed` int(11) unsigned NOT NULL DEFAULT 0,
  `num_users_emailed` int(11) unsigned NOT NULL DEFAULT 0,
  `total_num_sent` int(11) unsigned NOT NULL DEFAULT 0,
  `total_num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `total_num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  `total_num_bounced` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_monthly_summary` (`date_sent`,`identifier`,`subject`,`spending_group`,`smtp_type`,`smtp_server_id`),
  KEY `date_sent_identifier` (`date_sent`,`identifier`),
  KEY `date_sent_spending_group` (`date_sent`,`spending_group`),
  KEY `date_sent_smtp_type` (`date_sent`,`smtp_type`),
  KEY `date_sent_smtp_server_id` (`date_sent`,`smtp_server_id`),
  KEY `date_sent_subject` (`date_sent`,`subject`)
) ENGINE=InnoDB AUTO_INCREMENT=70715044 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Monthly_Summary`
--

DROP TABLE IF EXISTS `SMTP_Monthly_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Monthly_Summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `month_start` varchar(10) NOT NULL DEFAULT '0000-00-01',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `spending_group` varchar(25) NOT NULL,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_active_users` int(11) unsigned NOT NULL DEFAULT 0,
  `num_unsubscribed` int(11) unsigned NOT NULL DEFAULT 0,
  `num_users_emailed` int(11) unsigned NOT NULL DEFAULT 0,
  `total_num_sent` int(11) unsigned NOT NULL DEFAULT 0,
  `total_num_opens` int(11) unsigned NOT NULL DEFAULT 0,
  `total_num_clicks` int(11) unsigned NOT NULL DEFAULT 0,
  `total_num_bounced` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_month_start_spending_group` (`month_start`,`identifier`,`spending_group`,`smtp_type`,`smtp_server_id`),
  KEY `month_start_identifier` (`month_start`,`identifier`),
  KEY `month_start_spending_group` (`month_start`,`spending_group`),
  KEY `month_start_smtp_type` (`month_start`,`smtp_type`),
  KEY `month_start_smtp_server_id` (`month_start`,`smtp_server_id`)
) ENGINE=InnoDB AUTO_INCREMENT=532366977 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log`
--

DROP TABLE IF EXISTS `SMTP_Send_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=714119675 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2019_08`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2019_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2019_08` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2019_09`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2019_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2019_09` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2019_10`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2019_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2019_10` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2019_11`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2019_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2019_11` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2019_12`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2019_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2019_12` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_01`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_01` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_02`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_02` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_03`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_03` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_04`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_04` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_05`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_05` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_06`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_06` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_07`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_07` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_08`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_08` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_09`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_09` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_10`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_10` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_11`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_11` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2020_12`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2020_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2020_12` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_01`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_01` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_02`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_02` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_03`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_03` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_04`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_04` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_05`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_05` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_06`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_06` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_07`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_07` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_08`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_08` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_09`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_09` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_10`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_10` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_11`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_11` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2021_12`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2021_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2021_12` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_01`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_01` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_02`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_02` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_03`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_03` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_04`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_04` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_05`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_05` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_06`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_06` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_07`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_07` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_08`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_08` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_09`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_09` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_10`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_10` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_11`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_11` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2022_12`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2022_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2022_12` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_01`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_01` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_02`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_02` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_03`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_03` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_04`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_04` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_05`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_05` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_06`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_06` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_07`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_07` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_08`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_08` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_09`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_09` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_10`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_10` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_11`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_11` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2023_12`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2023_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2023_12` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_01`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_01` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_02`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_02` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_03`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_03` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_04`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_04` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_05`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_05` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_06`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_06` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_07`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_07` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_08`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_08` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_09`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_09` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_10`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_10` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_11`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_11` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2024_12`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2024_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2024_12` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_01`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_01` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_02`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_02` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_03`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_03` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_04`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_04` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_05`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_05` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_06`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_06` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_07`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_07` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_08`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_08` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_09`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_09` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_10`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_10` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Send_Log_2025_11`
--

DROP TABLE IF EXISTS `SMTP_Send_Log_2025_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Send_Log_2025_11` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  `esp` varchar(100) NOT NULL DEFAULT '',
  `smtp_server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `smtp_type` varchar(32) NOT NULL DEFAULT 'Internal',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `spending_group` varchar(25) NOT NULL,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime_opened` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_opened` date NOT NULL DEFAULT '0000-00-00',
  `date_clicked` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_sent` (`date_sent`),
  KEY `date_opened` (`date_opened`),
  KEY `date_clicked` (`date_clicked`),
  KEY `user_id` (`user_id`),
  KEY `mp_code` (`mp_code`),
  KEY `tracking_id` (`tracking_id`),
  KEY `spending_group` (`spending_group`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `esp_smtp_server` (`esp`,`smtp_server_id`),
  KEY `smtp_type_smtp_server` (`smtp_type`,`smtp_server_id`),
  KEY `esp_smtp_server_smtp_type` (`esp`,`smtp_server_id`,`smtp_type`),
  KEY `type_id` (`type_id`),
  KEY `identifier` (`identifier`),
  KEY `subject` (`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Server_Control`
--

DROP TABLE IF EXISTS `SMTP_Server_Control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Server_Control` (
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`server_id`,`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Server_Excluded_Domain`
--

DROP TABLE IF EXISTS `SMTP_Server_Excluded_Domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Server_Excluded_Domain` (
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `domain` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`server_id`,`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Server_Metrics`
--

DROP TABLE IF EXISTS `SMTP_Server_Metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Server_Metrics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `minute` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `queue_size` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_point` (`minute`,`server_id`),
  KEY `date` (`date`),
  KEY `server_id` (`server_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36121958 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Server_Risk_Level`
--

DROP TABLE IF EXISTS `SMTP_Server_Risk_Level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Server_Risk_Level` (
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `risk_level` enum('low','medium','high') NOT NULL DEFAULT 'low',
  PRIMARY KEY (`server_id`,`risk_level`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Server_Scores`
--

DROP TABLE IF EXISTS `SMTP_Server_Scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Server_Scores` (
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `score_type` varchar(40) NOT NULL DEFAULT '',
  `score_value` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`server_id`,`date`,`score_type`),
  KEY `score_search` (`date`,`score_type`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Servers`
--

DROP TABLE IF EXISTS `SMTP_Servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Servers` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` varchar(128) NOT NULL DEFAULT '',
  `ip_address` varchar(16) NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `returnpath_state_microsoft` varchar(12) NOT NULL DEFAULT '',
  `returnpath_state_yahoo` varchar(12) NOT NULL DEFAULT '',
  `returnpath_state_global` varchar(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Tracking_Temp`
--

DROP TABLE IF EXISTS `SMTP_Tracking_Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Tracking_Temp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tracking_id` varchar(32) NOT NULL DEFAULT '',
  `type` enum('open','click') DEFAULT 'open',
  `datetime_logged` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `email` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(128) NOT NULL DEFAULT '',
  `datetime_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_tracking_id_type` (`tracking_id`,`type`),
  KEY `tracking_id` (`tracking_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=27070170 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SMTP_Types`
--

DROP TABLE IF EXISTS `SMTP_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SMTP_Types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL DEFAULT '',
  `risk_level` enum('low','medium','high') NOT NULL DEFAULT 'low',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Schedule`
--

DROP TABLE IF EXISTS `Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Schedule` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `list_id_exclude` varchar(255) DEFAULT '',
  `type_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `inc_seed_list` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) NOT NULL DEFAULT 0,
  `recurring_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_finish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `reply_to` varchar(255) NOT NULL DEFAULT '',
  `msg_body_html` mediumtext NOT NULL,
  `msg_body_plain` text NOT NULL,
  `priority` tinyint(1) NOT NULL DEFAULT 0,
  `num_recipients` int(11) unsigned NOT NULL DEFAULT 0,
  `num_opens` int(11) NOT NULL DEFAULT 0,
  `num_clicks` int(11) NOT NULL DEFAULT 0,
  `date_last_open` date NOT NULL DEFAULT '0000-00-00',
  `date_last_click` date NOT NULL DEFAULT '0000-00-00',
  `status` enum('draft','pending','rejected','ready','queued','sending','sent','paused') DEFAULT NULL,
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_comments` text NOT NULL,
  `additional_list_ids` varchar(255) NOT NULL DEFAULT '',
  `template` varchar(100) NOT NULL DEFAULT '',
  `service` varchar(255) NOT NULL DEFAULT '',
  `sitekey` varchar(255) NOT NULL DEFAULT '',
  `test_type` enum('none','subject','body') DEFAULT 'none',
  `test_subject` varchar(255) DEFAULT '',
  `test_body` mediumtext DEFAULT NULL,
  `excluded_domains` varchar(255) DEFAULT NULL,
  `send_limit_name` varchar(255) DEFAULT 'None',
  `send_limit_count` int(11) DEFAULT 0,
  `seedlist_sender_domain` varchar(64) NOT NULL DEFAULT '',
  `local_time` tinyint(4) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mp_code` varchar(5) DEFAULT NULL,
  `promo_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_id` (`recurring_id`)
) ENGINE=InnoDB AUTO_INCREMENT=152513 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Schedule_Lists`
--

DROP TABLE IF EXISTS `Schedule_Lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Schedule_Lists` (
  `schedule_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `dynamic_list_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`schedule_id`,`dynamic_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Stats`
--

DROP TABLE IF EXISTS `Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Stats` (
  `schedule_id` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `num_opens` int(11) NOT NULL DEFAULT 0,
  `num_clicks` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`schedule_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Templates`
--

DROP TABLE IF EXISTS `Templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Templates` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `department` enum('affiliate','broadcasting','customer') NOT NULL DEFAULT 'customer',
  `purpose` varchar(32) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department` (`department`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tracking`
--

DROP TABLE IF EXISTS `Tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tracking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(12) NOT NULL DEFAULT '',
  `type_id` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `spending_group` varchar(25) NOT NULL DEFAULT '',
  `site_type` enum('','F4F','WL') NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `opened` tinyint(4) NOT NULL DEFAULT 0,
  `opened_first` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `opened_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clicked` tinyint(4) NOT NULL DEFAULT 0,
  `clicked_first` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clicked_last` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `bounced` enum('Y','N') NOT NULL DEFAULT 'N',
  `complained` enum('Y','N') NOT NULL DEFAULT 'N',
  `unsubscribed` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Triggered_Engagement_Log`
--

DROP TABLE IF EXISTS `Triggered_Engagement_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Triggered_Engagement_Log` (
  `address` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `subject` text NOT NULL,
  `num_clicks` smallint(4) unsigned NOT NULL DEFAULT 0,
  `num_opens` smallint(4) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`address`,`identifier`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Triggered_Engagement_Stats`
--

DROP TABLE IF EXISTS `Triggered_Engagement_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Triggered_Engagement_Stats` (
  `identifier` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `num_clicks` smallint(4) unsigned NOT NULL DEFAULT 0,
  `num_opens` smallint(4) unsigned NOT NULL DEFAULT 0,
  `num_recipients` smallint(4) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`identifier`,`subject`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Types`
--

DROP TABLE IF EXISTS `Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Types` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `user_can_edit` enum('Y','N') NOT NULL DEFAULT 'N',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `source_site_id` smallint(4) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `source_site_id` (`source_site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Unsubscribe`
--

DROP TABLE IF EXISTS `Unsubscribe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Unsubscribe` (
  `type_id` smallint(3) unsigned NOT NULL DEFAULT 1001,
  `address` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `unsub_method` varchar(32) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`address`,`type_id`),
  KEY `unsub_method` (`unsub_method`),
  KEY `address` (`address`),
  KEY `datetime` (`datetime`),
  KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Unsubscribe_Settings`
--

DROP TABLE IF EXISTS `Unsubscribe_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Unsubscribe_Settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` smallint(3) unsigned NOT NULL DEFAULT 1001,
  `address` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `unsub_method` varchar(32) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  UNIQUE KEY `address_by_platform` (`address`,`type_id`,`platform`),
  KEY `unsub_method` (`unsub_method`),
  KEY `address` (`address`),
  KEY `datetime` (`datetime`),
  KEY `identifier` (`identifier`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=12339038 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Unsubscribe_Temp`
--

DROP TABLE IF EXISTS `Unsubscribe_Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Unsubscribe_Temp` (
  `type_id` smallint(3) unsigned NOT NULL DEFAULT 1001,
  `address` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`address`,`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Unsubscribe_Temp2`
--

DROP TABLE IF EXISTS `Unsubscribe_Temp2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Unsubscribe_Temp2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(4) NOT NULL DEFAULT 0,
  `address` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`,`address`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=879165 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Bounce_Notification`
--

DROP TABLE IF EXISTS `User_Bounce_Notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Bounce_Notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `email` varchar(64) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `banner` tinyint(1) NOT NULL DEFAULT 0,
  `follow_up` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_email` (`user_id`,`email`),
  KEY `follow_up` (`follow_up`)
) ENGINE=InnoDB AUTO_INCREMENT=894996 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_List`
--

DROP TABLE IF EXISTS `User_List`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_List` (
  `address` varchar(255) NOT NULL DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '0000',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `source_site_id` smallint(4) unsigned NOT NULL,
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `domain` varchar(255) NOT NULL DEFAULT 'flirt4free.com',
  `confirm_ip` varbinary(16) NOT NULL,
  `service_girls` enum('Y','N') DEFAULT 'Y',
  `service_guys` enum('Y','N') DEFAULT 'N',
  `service_trans` enum('Y','N') DEFAULT 'N',
  `date_subscribe` date NOT NULL DEFAULT '0000-00-00',
  `date_confirm` date NOT NULL DEFAULT '0000-00-00',
  `date_last_email` date NOT NULL DEFAULT '0000-00-00',
  `date_last_open` date NOT NULL DEFAULT '0000-00-00',
  `num_emails` mediumint(6) NOT NULL DEFAULT 0,
  `num_opens` mediumint(6) NOT NULL DEFAULT 0,
  `num_clicks` mediumint(6) NOT NULL DEFAULT 0,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
  `girls_percent` float(4,3) NOT NULL,
  `guys_percent` float(4,3) NOT NULL,
  `trans_percent` float(4,3) NOT NULL,
  `date_last_click` date NOT NULL DEFAULT '0000-00-00',
  `address_domain` varchar(64) DEFAULT NULL,
  KEY `status` (`status`),
  KEY `user_id` (`user_id`),
  KEY `address` (`address`),
  KEY `source_site_id` (`source_site_id`),
  KEY `address_prefix` (`address`(1)),
  KEY `date_subscribe` (`date_subscribe`),
  KEY `domain_lookup` (`status`,`domain`),
  KEY `address_domain` (`address_domain`)
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`localhost`*/ /*!50003 TRIGGER MAILINGS.date_last_clicked_update BEFORE UPDATE ON MAILINGS.User_List

FOR EACH ROW

    BEGIN
        
        IF NEW.num_clicks <> OLD.num_clicks AND NEW.num_clicks > 0 THEN
        
            SET NEW.date_last_click = NOW(); 
            
        END IF;
        
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `User_List_By_Spending_Group_Temp`
--

DROP TABLE IF EXISTS `User_List_By_Spending_Group_Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_List_By_Spending_Group_Temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `spending_group` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `spending_group` (`spending_group`)
) ENGINE=InnoDB AUTO_INCREMENT=221561 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-25 17:37:24
