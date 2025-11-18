/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.22-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: db-employee.lan.vsmedia.net    Database: BILLING
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
-- Table structure for table `AVS_code`
--

DROP TABLE IF EXISTS `AVS_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AVS_code` (
  `code` char(1) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Action_Blocks`
--

DROP TABLE IF EXISTS `Action_Blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Action_Blocks` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_action` (`category`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Active_Member`
--

DROP TABLE IF EXISTS `Active_Member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Active_Member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `status` char(1) DEFAULT 'A',
  `type` varchar(255) DEFAULT 'vip',
  `product_type_id` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `recur_id` int(11) unsigned NOT NULL DEFAULT 0,
  `transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime DEFAULT NULL,
  `date_expired` datetime DEFAULT NULL,
  `date_last_updated` datetime DEFAULT NULL,
  `dm_credits` smallint(6) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1331981 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AdWords`
--

DROP TABLE IF EXISTS `AdWords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AdWords` (
  `mp_code` char(5) NOT NULL,
  `keywords` varchar(75) NOT NULL,
  PRIMARY KEY (`mp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Affiliate_Risk`
--

DROP TABLE IF EXISTS `Affiliate_Risk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Affiliate_Risk` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `block_type` enum('blocked','high_risk') NOT NULL DEFAULT 'blocked',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `mp_code` (`mp_code`),
  KEY `block_type` (`block_type`),
  KEY `status` (`status`),
  KEY `created_admin_id` (`created_admin_id`),
  KEY `updated_admin_id` (`updated_admin_id`),
  KEY `date_updated` (`date_updated`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Analyze_Promo`
--

DROP TABLE IF EXISTS `Analyze_Promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Analyze_Promo` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `date_last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_purchased` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_spent` float(10,2) NOT NULL DEFAULT 0.00,
  `promo_code` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`promo_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Analyze_User`
--

DROP TABLE IF EXISTS `Analyze_User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Analyze_User` (
  `user_id` int(10) unsigned NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `mp_code` varchar(5) DEFAULT NULL,
  `affiliate_id` mediumint(8) unsigned DEFAULT NULL,
  `total_spent` float(10,2) DEFAULT 0.00,
  `orig_timeleft` mediumint(7) DEFAULT 120,
  `timeleft` mediumint(7) DEFAULT NULL,
  `type` varchar(10) DEFAULT 'api',
  `accounts` smallint(4) unsigned DEFAULT NULL,
  `processed` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Analyze_User_Transact`
--

DROP TABLE IF EXISTS `Analyze_User_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Analyze_User_Transact` (
  `user_id` int(10) unsigned NOT NULL,
  `transact_id` int(11) unsigned NOT NULL,
  `ref_transact_id` int(11) unsigned DEFAULT 0,
  `date_transact` datetime DEFAULT NULL,
  `type` varchar(10) NOT NULL,
  `orientation` varchar(5) DEFAULT NULL,
  `amount` float(10,2) DEFAULT 0.00,
  `credits` mediumint(7) DEFAULT 0,
  `cost` float(10,2) DEFAULT 0.00,
  KEY `user_id` (`user_id`),
  KEY `transact_id` (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Argus_Temp`
--

DROP TABLE IF EXISTS `Argus_Temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Argus_Temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `argus_cust_id` int(11) DEFAULT NULL,
  `argus_pmt_id` int(11) DEFAULT NULL,
  `random_inv_id` varchar(255) DEFAULT NULL,
  `bin` varchar(6) DEFAULT NULL,
  `last_digits` varchar(4) DEFAULT NULL,
  `expiry` varchar(6) DEFAULT NULL,
  `rg_hash` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `argus_cust_id` (`argus_cust_id`,`argus_pmt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1883244 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Attribution_Log`
--

DROP TABLE IF EXISTS `Attribution_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Attribution_Log` (
  `event_date` date NOT NULL DEFAULT '0000-00-00',
  `ref_id` varchar(90) NOT NULL DEFAULT '0',
  `step` varchar(50) NOT NULL DEFAULT '',
  `old_mp_code` char(5) NOT NULL DEFAULT '',
  `new_mp_code` char(5) NOT NULL DEFAULT '',
  `reason` varchar(255) NOT NULL DEFAULT '',
  KEY `ref_id` (`ref_id`),
  KEY `event_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Attribution_V2_Log`
--

DROP TABLE IF EXISTS `Attribution_V2_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Attribution_V2_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_date` datetime DEFAULT current_timestamp(),
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `method` varchar(16) NOT NULL DEFAULT '',
  `ref_id` varchar(90) NOT NULL DEFAULT '0',
  `step` varchar(50) NOT NULL DEFAULT '',
  `old_mp_code` char(5) NOT NULL DEFAULT '',
  `new_mp_code` char(5) NOT NULL DEFAULT '',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `bounty_paid` char(1) NOT NULL DEFAULT 'N',
  `bounty_amount` float(6,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `event_date` (`event_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2776806 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Attrition`
--

DROP TABLE IF EXISTS `Attrition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Attrition` (
  `user_id` int(10) unsigned NOT NULL,
  `date_created` date DEFAULT NULL,
  `last_purchase` date DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `attrition_type` char(1) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `report_date` (`report_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Attrition_Spending_Group`
--

DROP TABLE IF EXISTS `Attrition_Spending_Group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Attrition_Spending_Group` (
  `user_id` int(10) unsigned NOT NULL,
  `attrition_type` char(1) DEFAULT NULL,
  `sum(amount)` double(19,2) DEFAULT NULL,
  `transactions` decimal(23,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Auto_Reload`
--

DROP TABLE IF EXISTS `Auto_Reload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Auto_Reload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `reload_level` smallint(2) NOT NULL DEFAULT 0,
  `use_banked_credits` enum('yes','no') NOT NULL DEFAULT 'no',
  `payment_id` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=99766 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Auto_Reload_Log`
--

DROP TABLE IF EXISTS `Auto_Reload_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Auto_Reload_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auto_reload_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `timeleft` mediumint(7) NOT NULL DEFAULT 0,
  `timeleft_banked` mediumint(7) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `price` float(5,2) DEFAULT 0.00,
  `credits` smallint(4) unsigned NOT NULL DEFAULT 0,
  `payment_id` int(11) NOT NULL DEFAULT 0,
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `source` varchar(255) NOT NULL DEFAULT '',
  `type` enum('purchase','transfer') NOT NULL DEFAULT 'purchase',
  `status` enum('pending','complete','failed') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=168710 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BIN`
--

DROP TABLE IF EXISTS `BIN`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `BIN` (
  `BIN` char(9) NOT NULL,
  `card_brand` varchar(24) NOT NULL DEFAULT '',
  `issuing_bank` varchar(80) NOT NULL,
  `type_of_card` varchar(24) NOT NULL,
  `card_category` varchar(24) NOT NULL,
  `card_product` char(10) NOT NULL,
  `country` varchar(48) NOT NULL,
  `code_a2` char(2) NOT NULL,
  `code_a3` char(3) NOT NULL,
  `country_number` smallint(4) unsigned NOT NULL,
  `info` varchar(48) NOT NULL,
  `phone` varchar(26) NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`BIN`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BIN_2023`
--

DROP TABLE IF EXISTS `BIN_2023`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `BIN_2023` (
  `BIN` char(9) NOT NULL,
  `card_brand` varchar(24) NOT NULL DEFAULT '',
  `issuing_bank` varchar(80) NOT NULL,
  `type_of_card` varchar(24) NOT NULL,
  `card_category` varchar(24) NOT NULL,
  `orig_card_category` varchar(24) DEFAULT NULL,
  `country` varchar(48) NOT NULL,
  `code_a2` char(2) NOT NULL,
  `code_a3` char(3) NOT NULL,
  `country_number` smallint(4) unsigned NOT NULL,
  `info` varchar(48) NOT NULL,
  `phone` varchar(26) NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Bank`
--

DROP TABLE IF EXISTS `Bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Bank` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `merchant` varchar(16) NOT NULL DEFAULT 'vsm',
  `status` tinyint(3) unsigned DEFAULT 0,
  `geo` varchar(16) NOT NULL DEFAULT 'us',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Bank_Allocation_Data`
--

DROP TABLE IF EXISTS `Bank_Allocation_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Bank_Allocation_Data` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `bank` varchar(20) NOT NULL DEFAULT '',
  `merchant_bank` varchar(20) NOT NULL DEFAULT '',
  `payment_type` varchar(20) NOT NULL DEFAULT '',
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `count` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`bank`,`merchant_bank`,`payment_type`,`processor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Bank_Allocation_Tally`
--

DROP TABLE IF EXISTS `Bank_Allocation_Tally`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Bank_Allocation_Tally` (
  `bank` varchar(20) NOT NULL DEFAULT '',
  `payment_type` varchar(10) NOT NULL DEFAULT '',
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `bank_transactions` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_transactions` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`bank`,`payment_type`,`processor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Bank_Distribution_Tests`
--

DROP TABLE IF EXISTS `Bank_Distribution_Tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Bank_Distribution_Tests` (
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_user_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `description` varchar(512) NOT NULL DEFAULT '',
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `customer_trans_lookback` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `customer_trans_breakpoint` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `customer_age_breakpoint` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `customer_age_low` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `customer_age_high` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `customer_trans_reference` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `merchant_trans_reference` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `limits` text NOT NULL,
  `distribution` text NOT NULL,
  `stats` text NOT NULL,
  `summary` text NOT NULL,
  `totals` text NOT NULL,
  `charts` text NOT NULL,
  `html` text NOT NULL,
  PRIMARY KEY (`datetime`,`admin_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Bin_Network_Stats`
--

DROP TABLE IF EXISTS `Bin_Network_Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Bin_Network_Stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime DEFAULT NULL,
  `bin` varchar(9) DEFAULT NULL,
  `processor_id` tinyint(3) NOT NULL DEFAULT 0,
  `network` varchar(25) DEFAULT NULL,
  `transact_id` int(11) DEFAULT 0,
  `transact_log_id` int(11) DEFAULT 0,
  `result` enum('success','fail') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38263046 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CVV_code`
--

DROP TABLE IF EXISTS `CVV_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CVV_code` (
  `code` char(1) NOT NULL DEFAULT '0',
  `description` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Card_Blacklist`
--

DROP TABLE IF EXISTS `Card_Blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Card_Blacklist` (
  `bin` char(9) NOT NULL DEFAULT '',
  `last_digits` char(4) NOT NULL DEFAULT '',
  `block_type` enum('add_cc','all') NOT NULL DEFAULT 'all',
  `comments` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`bin`,`last_digits`),
  KEY `status` (`status`),
  KEY `created_admin_id` (`created_admin_id`),
  KEY `updated_admin_id` (`updated_admin_id`),
  KEY `date_updated` (`date_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cascade`
--

DROP TABLE IF EXISTS `Cascade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cascade` (
  `cascade_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `primary_transact_log_id` int(11) unsigned NOT NULL DEFAULT 0,
  `transact_log_id` int(11) unsigned NOT NULL DEFAULT 0,
  `transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cascade_status` enum('success','fail','not_cascadable','not_available') NOT NULL DEFAULT 'success',
  PRIMARY KEY (`cascade_id`),
  KEY `datetime` (`datetime`),
  KEY `primary_transact_log_id` (`primary_transact_log_id`),
  KEY `transact_log_id` (`transact_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11911272 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chargeback_Counts`
--

DROP TABLE IF EXISTS `Chargeback_Counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Chargeback_Counts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant` varchar(16) NOT NULL DEFAULT 'vsm',
  `bank_id` char(10) NOT NULL DEFAULT '',
  `merchant_bank` varchar(100) NOT NULL DEFAULT '',
  `card_type` varchar(10) NOT NULL DEFAULT '',
  `data_key` varchar(50) NOT NULL DEFAULT '',
  `data_value` int(6) unsigned NOT NULL DEFAULT 0,
  `admin_id` int(10) unsigned NOT NULL DEFAULT 0,
  `report_date` date NOT NULL DEFAULT '0000-00-00',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `merchant` (`merchant`),
  KEY `bank_id` (`bank_id`),
  KEY `merchant_bank` (`merchant_bank`),
  KEY `card_type` (`card_type`),
  KEY `admin_id` (`admin_id`),
  KEY `report_date` (`report_date`),
  KEY `created_date` (`created_date`)
) ENGINE=InnoDB AUTO_INCREMENT=185269 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Chat_Special`
--

DROP TABLE IF EXISTS `Chat_Special`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Chat_Special` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `percent_more` enum('10','15','20','25','30') NOT NULL DEFAULT '15',
  `max_txn_per_hour_user` tinyint(3) unsigned NOT NULL DEFAULT 2,
  `max_txn_per_day_user` tinyint(3) unsigned NOT NULL DEFAULT 4,
  `max_txn_per_day_system` smallint(5) unsigned NOT NULL DEFAULT 1000,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=560 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CoinGate_Requests`
--

DROP TABLE IF EXISTS `CoinGate_Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CoinGate_Requests` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `billing_ref_id` varchar(64) NOT NULL DEFAULT '',
  `data` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`,`billing_ref_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Coinbase_Pending`
--

DROP TABLE IF EXISTS `Coinbase_Pending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Coinbase_Pending` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(10) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `json` text NOT NULL,
  `status` enum('pending','processed','cancelled') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collection`
--

DROP TABLE IF EXISTS `Collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collection` (
  `collection_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `original_collection_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `username` varchar(32) DEFAULT NULL,
  `status_id` int(1) NOT NULL DEFAULT 0,
  `locked_user_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `country` varchar(50) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upd_user_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`collection_id`,`original_collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collection_Address`
--

DROP TABLE IF EXISTS `Collection_Address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collection_Address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `original_collection_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `username` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `address` varchar(80) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(32) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `is_default` int(1) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upd_user_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=207990 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collection_Note`
--

DROP TABLE IF EXISTS `Collection_Note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collection_Note` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `original_collection_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upd_user_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  `upd_user_name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB AUTO_INCREMENT=254191 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collection_Payment`
--

DROP TABLE IF EXISTS `Collection_Payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collection_Payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `original_collection_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `amount` float(7,2) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upd_user_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=414 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collection_Status`
--

DROP TABLE IF EXISTS `Collection_Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collection_Status` (
  `status_id` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collection_Transaction`
--

DROP TABLE IF EXISTS `Collection_Transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collection_Transaction` (
  `original_collection_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `chargeback_id` int(11) NOT NULL DEFAULT 0,
  `amount` float(5,2) DEFAULT NULL,
  `credit_type` tinyint(4) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upd_user_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`original_collection_id`,`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collections`
--

DROP TABLE IF EXISTS `Collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT '',
  `user_in_usa` tinyint(1) NOT NULL DEFAULT 0,
  `min_amount` float(8,2) NOT NULL DEFAULT 0.00,
  `sufficient_amount` tinyint(1) NOT NULL DEFAULT 0,
  `potential_fraud` tinyint(1) NOT NULL DEFAULT 0,
  `refunded_amount` float(10,2) NOT NULL DEFAULT 0.00,
  `penalties_amount` float(10,2) NOT NULL DEFAULT 0.00,
  `updated_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collections_Comments`
--

DROP TABLE IF EXISTS `Collections_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collections_Comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` enum('system','user') NOT NULL DEFAULT 'system',
  `comments` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `collection_id` (`collection_id`),
  KEY `type` (`type`),
  KEY `datetime` (`datetime`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1291 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collections_Files`
--

DROP TABLE IF EXISTS `Collections_Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collections_Files` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) unsigned NOT NULL DEFAULT 0,
  `upload_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `file_name` varchar(50) NOT NULL DEFAULT '',
  `file_size` int(10) unsigned NOT NULL DEFAULT 0,
  `file_mime_type` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `collection_id` (`collection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collections_Payments`
--

DROP TABLE IF EXISTS `Collections_Payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Collections_Payments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT '',
  `amount` float(10,2) NOT NULL DEFAULT 0.00,
  `comments` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `collection_id` (`collection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ComboProduct_bk`
--

DROP TABLE IF EXISTS `ComboProduct_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ComboProduct_bk` (
  `master_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Content_Complaints`
--

DROP TABLE IF EXISTS `Content_Complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Content_Complaints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant` varchar(16) NOT NULL DEFAULT 'vsm',
  `type` varchar(40) NOT NULL DEFAULT '',
  `details` mediumtext DEFAULT NULL,
  `urls` mediumtext DEFAULT NULL,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_name` varchar(50) NOT NULL DEFAULT '',
  `complainant_name` varchar(50) NOT NULL DEFAULT '',
  `contact_method` varchar(50) NOT NULL DEFAULT '',
  `contact_info` varchar(50) NOT NULL DEFAULT '',
  `notes` mediumtext DEFAULT NULL,
  `admin_id` smallint(5) unsigned NOT NULL,
  `status` enum('open','mediation','closed') NOT NULL DEFAULT 'open',
  `report_date` date NOT NULL DEFAULT '0000-00-00',
  `updated_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `resolution_status` enum('pending','approved','closed') NOT NULL DEFAULT 'pending',
  `resolution_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `merchant` (`merchant`),
  KEY `model_id` (`model_id`),
  KEY `complainant_name` (`complainant_name`),
  KEY `contact_info` (`contact_info`),
  KEY `report_date` (`report_date`),
  KEY `updated_date` (`updated_date`),
  KEY `resolution_date` (`resolution_date`),
  KEY `model_name` (`model_name`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Country`
--

DROP TABLE IF EXISTS `Country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Country` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL DEFAULT '',
  `eu` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `security_level` enum('normal','risk','blocked') DEFAULT 'normal',
  `ofac` tinyint(1) NOT NULL DEFAULT 0,
  `require_3ds` tinyint(1) NOT NULL DEFAULT 0,
  `tier` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `ofac_date_in` date NOT NULL DEFAULT '0000-00-00',
  `ofac_date_out` date NOT NULL DEFAULT '0000-00-00',
  KEY `code` (`code`),
  KEY `is_eu` (`eu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Country_to_Distribution_Group`
--

DROP TABLE IF EXISTS `Country_to_Distribution_Group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Country_to_Distribution_Group` (
  `country_code` char(2) NOT NULL DEFAULT '',
  `distribution_group_id` tinyint(3) NOT NULL DEFAULT 0,
  KEY `country_code` (`country_code`),
  KEY `distribution_group_id` (`distribution_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Country_to_Payment_Type`
--

DROP TABLE IF EXISTS `Country_to_Payment_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Country_to_Payment_Type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `country_code` char(2) NOT NULL DEFAULT '',
  `payment_type_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `country_to_payment_type` (`country_code`,`payment_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=470 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Credit_History_Rollup`
--

DROP TABLE IF EXISTS `Credit_History_Rollup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Credit_History_Rollup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `start_dt` datetime NOT NULL,
  `end_dt` datetime NOT NULL,
  `balance` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`start_dt`,`end_dt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Credit_Sink`
--

DROP TABLE IF EXISTS `Credit_Sink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Credit_Sink` (
  `sink_id` tinyint(2) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `left_tree` tinyint(2) DEFAULT NULL,
  `right_tree` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`sink_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Credit_Sink_User`
--

DROP TABLE IF EXISTS `Credit_Sink_User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Credit_Sink_User` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `sink_id` tinyint(2) unsigned DEFAULT NULL,
  `credits` mediumint(7) unsigned DEFAULT NULL,
  `count` smallint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=19632917 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cross_Sale_Offers`
--

DROP TABLE IF EXISTS `Cross_Sale_Offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cross_Sale_Offers` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `offer_description` text NOT NULL,
  `success_description` text NOT NULL,
  `email_description` text NOT NULL,
  `precheck` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `precheck` (`precheck`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cross_Sales_Log`
--

DROP TABLE IF EXISTS `Cross_Sales_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cross_Sales_Log` (
  `user_payment_id` int(11) unsigned NOT NULL DEFAULT 0,
  `orig_transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cross_sale_transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `cross_sale_id` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `comment` text NOT NULL,
  `final_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`user_id`,`cross_sale_id`),
  KEY `user_id` (`user_id`),
  KEY `orig_transact_id` (`orig_transact_id`),
  KEY `cross_sale_transact_id` (`cross_sale_transact_id`),
  KEY `final_status` (`final_status`),
  KEY `date_processed` (`date_processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Currency`
--

DROP TABLE IF EXISTS `Currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Currency` (
  `currency` varchar(3) NOT NULL DEFAULT '',
  `symbol` varchar(20) NOT NULL DEFAULT '',
  `symbol_position` enum('before','after') NOT NULL DEFAULT 'before',
  `thousands_separator` varchar(1) NOT NULL DEFAULT '',
  `decimal_point` varchar(1) NOT NULL DEFAULT '',
  `exchange_rate` float(10,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Currency_Rate`
--

DROP TABLE IF EXISTS `Currency_Rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Currency_Rate` (
  `currency` varchar(3) NOT NULL DEFAULT '',
  `exchange_rate` float(10,4) NOT NULL DEFAULT 0.0000,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `currency` (`currency`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Review_Queue`
--

DROP TABLE IF EXISTS `Customer_Review_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Review_Queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime_closed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_type` varchar(100) NOT NULL DEFAULT '',
  `review_body` text NOT NULL,
  `closed_by_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` enum('open','closed','escalated') NOT NULL DEFAULT 'open',
  PRIMARY KEY (`id`),
  KEY `datetime_created` (`datetime_created`),
  KEY `user_id` (`user_id`),
  KEY `review_type` (`review_type`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=236502 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Customer_Review_Queue_Comments`
--

DROP TABLE IF EXISTS `Customer_Review_Queue_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Customer_Review_Queue_Comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `record_id` (`record_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Direct_Chat_Log`
--

DROP TABLE IF EXISTS `Direct_Chat_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Direct_Chat_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `domain` varchar(255) NOT NULL DEFAULT '',
  `ip_address` int(11) NOT NULL DEFAULT 0,
  `url_request` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `unique_reason` text DEFAULT NULL,
  `create_date` date NOT NULL DEFAULT '0000-00-00',
  `create_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48710 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Discrepancy_Tracking`
--

DROP TABLE IF EXISTS `Discrepancy_Tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Discrepancy_Tracking` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `discrepancy` int(11) NOT NULL,
  `calculated_on` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2244890 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Distribution`
--

DROP TABLE IF EXISTS `Distribution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Distribution` (
  `distribution_group_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `merchant` varchar(2) NOT NULL DEFAULT 'VS',
  `user_age` enum('all','under60','over60') NOT NULL DEFAULT 'all',
  `item_type` enum('merchant_bank_target','merchant_bank_limit','merchant_bank_percent_limit','bank_limit','bank_percent_limit','bank_whitelabel_exclusion','promo_target') NOT NULL DEFAULT 'merchant_bank_target',
  `item` varchar(32) NOT NULL DEFAULT '',
  `payment_type` enum('VISA','other') NOT NULL DEFAULT 'VISA',
  `value` varchar(255) NOT NULL DEFAULT '',
  `admin_user_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`distribution_group_id`,`user_age`,`item_type`,`item`,`payment_type`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Distribution_Group`
--

DROP TABLE IF EXISTS `Distribution_Group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Distribution_Group` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL DEFAULT 'gateway',
  `merchant` varchar(16) NOT NULL DEFAULT 'vsm',
  `name` varchar(32) NOT NULL DEFAULT '',
  `currency` varchar(3) NOT NULL DEFAULT '',
  `geo` varchar(16) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  KEY `currency` (`currency`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Distribution_Group_to_Bank`
--

DROP TABLE IF EXISTS `Distribution_Group_to_Bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Distribution_Group_to_Bank` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `distribution_group_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `bank_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(3) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `distribution_group_bank` (`distribution_group_id`,`bank_id`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Distribution_Log`
--

DROP TABLE IF EXISTS `Distribution_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Distribution_Log` (
  `distribution_group_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `user_age` enum('all','under60','over60') NOT NULL DEFAULT 'all',
  `item_type` enum('merchant_bank_target','merchant_bank_limit','merchant_bank_percent_limit','bank_limit','bank_percent_limit','bank_whitelabel_exclusion','promo_target') NOT NULL,
  `date_changed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changed_from` text NOT NULL,
  `changed_to` text NOT NULL,
  `admin_user_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`distribution_group_id`,`item_type`,`date_changed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Distribution_Review`
--

DROP TABLE IF EXISTS `Distribution_Review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Distribution_Review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(15) NOT NULL,
  `date_notified` datetime NOT NULL,
  `message` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36942 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Error_Activity`
--

DROP TABLE IF EXISTS `Error_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Error_Activity` (
  `billing_ref_id` varchar(32) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `code` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `sequence` int(11) unsigned NOT NULL DEFAULT 0,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `details` mediumtext DEFAULT NULL,
  KEY `billing_ref_id` (`billing_ref_id`),
  KEY `datetime` (`datetime`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Error_Codes`
--

DROP TABLE IF EXISTS `Error_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Error_Codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error_code` int(10) DEFAULT NULL,
  `code_description` text DEFAULT NULL,
  `threshold` int(5) DEFAULT NULL,
  `threshold_period` int(5) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `error_title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2026 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Ethoca_Bin_List`
--

DROP TABLE IF EXISTS `Ethoca_Bin_List`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Ethoca_Bin_List` (
  `bin` int(6) NOT NULL DEFAULT 0,
  `bank` varchar(55) NOT NULL DEFAULT '',
  PRIMARY KEY (`bin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Accounts`
--

DROP TABLE IF EXISTS `External_Accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Accounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `processor_id` tinyint(3) unsigned NOT NULL,
  `processor_data` varchar(255) NOT NULL,
  `descriptor` varchar(255) NOT NULL,
  `hashcode` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Configs`
--

DROP TABLE IF EXISTS `External_Configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Configs` (
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `payable_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `invoice_frequency` varchar(40) NOT NULL DEFAULT 'monthly',
  `credit_limit` float(10,2) NOT NULL DEFAULT 0.00,
  `available_credit` float(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Credit_Transfer`
--

DROP TABLE IF EXISTS `External_Credit_Transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Credit_Transfer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `username` varchar(32) NOT NULL,
  `num_credits` int(11) NOT NULL DEFAULT 0,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_invoiceable` tinyint(1) unsigned DEFAULT 1,
  `invoice_id` int(10) NOT NULL DEFAULT 0,
  `external_transact_id` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20122 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Invoice_Items`
--

DROP TABLE IF EXISTS `External_Invoice_Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Invoice_Items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) NOT NULL DEFAULT 0,
  `account_id` int(10) NOT NULL DEFAULT 0,
  `amount` float(10,2) NOT NULL DEFAULT 0.00,
  `payable_rate` float(4,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8472 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Invoices`
--

DROP TABLE IF EXISTS `External_Invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Invoices` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` int(4) unsigned DEFAULT NULL,
  `amount_due` float(10,2) NOT NULL DEFAULT 0.00,
  `date_paid` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3255 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Membership_Sites`
--

DROP TABLE IF EXISTS `External_Membership_Sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Membership_Sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Memberships`
--

DROP TABLE IF EXISTS `External_Memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Memberships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `active_member_id` int(11) unsigned NOT NULL DEFAULT 0,
  `membership_site_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `ext_user_id` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive','cancelled') NOT NULL DEFAULT 'inactive',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `active_member_id` (`active_member_id`),
  KEY `membership_site_id` (`membership_site_id`),
  KEY `username` (`username`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=362 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Payments`
--

DROP TABLE IF EXISTS `External_Payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Payments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `amount` float(10,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(15) NOT NULL DEFAULT '',
  `pay_period_id` int(40) NOT NULL DEFAULT 0,
  `invoice_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=2216 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Payout_Join`
--

DROP TABLE IF EXISTS `External_Payout_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Payout_Join` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `pay_period_id` int(4) unsigned NOT NULL DEFAULT 0,
  `invoice_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`affiliate_id`,`pay_period_id`,`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `External_Transactions`
--

DROP TABLE IF EXISTS `External_Transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `External_Transactions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT 0,
  `user_id` int(10) NOT NULL DEFAULT 0,
  `amount` float(6,2) NOT NULL DEFAULT 0.00,
  `discount` float(6,2) NOT NULL DEFAULT 0.00,
  `invoice_id` int(10) NOT NULL DEFAULT 0,
  `return_id` int(11) unsigned NOT NULL DEFAULT 0,
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `transact_type` enum('purchase','chargeback','refund') NOT NULL DEFAULT 'purchase',
  `transact_date` date NOT NULL DEFAULT '0000-00-00',
  `transact_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `transact_id` (`transact_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=651331 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fame_Type`
--

DROP TABLE IF EXISTS `Fame_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fame_Type` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  `notification_type_id` smallint(5) unsigned DEFAULT NULL,
  `shame_sort` char(4) DEFAULT NULL,
  `date_last_processed` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fan_Club_Memberships`
--

DROP TABLE IF EXISTS `Fan_Club_Memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fan_Club_Memberships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `screen_name` varchar(32) NOT NULL,
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rate_id` int(11) NOT NULL,
  `fanclub_transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `auto_renew` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`),
  KEY `myIndex` (`model_id`),
  KEY `ScreenName` (`screen_name`)
) ENGINE=InnoDB AUTO_INCREMENT=532507 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fan_Club_Transact`
--

DROP TABLE IF EXISTS `Fan_Club_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fan_Club_Transact` (
  `transact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `num_credits` smallint(5) NOT NULL,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` enum('credit','purchase') NOT NULL DEFAULT 'purchase',
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `ref_fanclub_transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` int(11) unsigned NOT NULL DEFAULT 0,
  `fan_ref` varchar(50) NOT NULL DEFAULT '',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `domain` varchar(253) NOT NULL DEFAULT '',
  `geo_country` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`transact_id`),
  KEY `date_transact` (`date_transact`),
  KEY `geo_country` (`geo_country`),
  KEY `sitekey` (`sitekey`,`domain`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=844101 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`10.%.%.%`*/ /*!50003 TRIGGER BILLING.fanclub_transact_insert AFTER INSERT ON BILLING.Fan_Club_Transact
 FOR EACH ROW BEGIN


IF NEW.type = 'purchase' AND NEW.num_credits > 0 THEN 

CALL BILLING.performer_affiliate_handler( NEW.user_id, NEW.model_id, 'fanclub_membership', NEW.transact_id, NEW.num_credits, NEW.date_transact );

END IF;

  
 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Feature_Show_Ticket_Transact`
--

DROP TABLE IF EXISTS `Feature_Show_Ticket_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feature_Show_Ticket_Transact` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `ticket_id` int(11) unsigned NOT NULL DEFAULT 0,
  `purchase_method` enum('billing','credits','goodwill') DEFAULT NULL,
  `type` enum('purchase','credit') NOT NULL DEFAULT 'purchase',
  `transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_credits` mediumint(7) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `domain` varchar(253) NOT NULL DEFAULT '',
  `geo_country` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `purchase_method` (`purchase_method`),
  KEY `type` (`type`),
  KEY `transact_id` (`transact_id`),
  KEY `date_created` (`date_created`),
  KEY `geo_country` (`geo_country`),
  KEY `sitekey` (`sitekey`,`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Feature_Show_Tickets`
--

DROP TABLE IF EXISTS `Feature_Show_Tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feature_Show_Tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `redemption_status` enum('new','redeemed') NOT NULL DEFAULT 'new',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `feature_show_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_redeemed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`),
  KEY `redemption_status` (`redemption_status`),
  KEY `model_id` (`model_id`),
  KEY `feature_show_id` (`feature_show_id`),
  KEY `date_redeemed` (`date_redeemed`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fingerprint_to_User`
--

DROP TABLE IF EXISTS `Fingerprint_to_User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fingerprint_to_User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` varchar(20) DEFAULT NULL,
  `visitor_id` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `login_type` varchar(12) DEFAULT NULL,
  `confidence_score` float(3,2) NOT NULL DEFAULT 0.00,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `visitor_id` (`visitor_id`),
  KEY `user_id` (`user_id`),
  KEY `datetime` (`datetime`),
  KEY `request_id` (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9874157 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fingerprints`
--

DROP TABLE IF EXISTS `Fingerprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fingerprints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` varchar(20) DEFAULT NULL,
  `linked_id` varchar(20) DEFAULT NULL,
  `visitor_id` varchar(20) DEFAULT NULL,
  `visitor_found` tinyint(1) DEFAULT NULL,
  `time` timestamp NULL DEFAULT NULL,
  `incognito` tinyint(1) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `continent` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `latitude` varchar(15) DEFAULT NULL,
  `longitude` varchar(15) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `subdivision` varchar(255) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `browser` varchar(50) DEFAULT NULL,
  `browser_full_version` varchar(50) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `os_version` varchar(20) DEFAULT NULL,
  `device` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `confidence_score` varchar(15) DEFAULT NULL,
  `first_seen_at_global` datetime DEFAULT NULL,
  `first_seen_at_subscription` datetime DEFAULT NULL,
  `last_seen_at_global` datetime DEFAULT NULL,
  `last_seen_at_subscription` datetime DEFAULT NULL,
  `ip_info_v4_address` varchar(15) DEFAULT NULL,
  `ip_info_v4_geolocation_latitude` varchar(20) DEFAULT NULL,
  `ip_info_v4_geolocation_longitude` varchar(20) DEFAULT NULL,
  `ip_info_v4_geolocation_postal_code` varchar(20) DEFAULT NULL,
  `ip_info_v4_geolocation_timezone` varchar(50) DEFAULT NULL,
  `ip_info_v4_geolocation_city` varchar(50) DEFAULT NULL,
  `ip_info_v4_geolocation_country` varchar(50) DEFAULT NULL,
  `ip_info_v4_geolocation_continent` varchar(50) DEFAULT NULL,
  `ip_info_v4_geolocation_subdivision` varchar(255) DEFAULT NULL,
  `ip_info_asn` varchar(15) DEFAULT NULL,
  `ip_info_asn_name` varchar(50) DEFAULT NULL,
  `ip_info_asn_network` varchar(50) DEFAULT NULL,
  `ip_info_data_center_result` varchar(20) DEFAULT NULL,
  `ip_info_data_center_name` varchar(50) DEFAULT NULL,
  `is_bot` tinyint(1) DEFAULT NULL,
  `is_vpn` tinyint(1) DEFAULT NULL,
  `vpn_timezone_mismatch` tinyint(1) DEFAULT NULL,
  `is_public_vpn` tinyint(1) DEFAULT NULL,
  `tampering` tinyint(1) DEFAULT NULL,
  `tampering_anomaly_score` varchar(15) DEFAULT NULL,
  `privacy_settings_result` varchar(20) DEFAULT NULL,
  `is_virtual_machine` tinyint(1) DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `request_id` (`request_id`),
  KEY `idx_visitor_id` (`visitor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21321826 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `First_Credit_Usage_BRAD_2019_Q2`
--

DROP TABLE IF EXISTS `First_Credit_Usage_BRAD_2019_Q2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `First_Credit_Usage_BRAD_2019_Q2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `percentile` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `sequence_no` smallint(5) unsigned NOT NULL DEFAULT 0,
  `minutes_from_addcc` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `usage_type` varchar(20) NOT NULL DEFAULT '',
  `credit_rate` smallint(5) unsigned NOT NULL DEFAULT 0,
  `credits_spent` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sequence` (`user_id`,`sequence_no`),
  KEY `sequence_no` (`sequence_no`),
  KEY `usage_type` (`usage_type`),
  KEY `model_id` (`model_id`),
  KEY `usage_detail` (`usage_type`,`credit_rate`)
) ENGINE=InnoDB AUTO_INCREMENT=1281044 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fleshlight_Transact_Log`
--

DROP TABLE IF EXISTS `Fleshlight_Transact_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fleshlight_Transact_Log` (
  `user_payment_id` int(11) unsigned NOT NULL DEFAULT 0,
  `orig_transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `order_id` int(11) unsigned NOT NULL DEFAULT 0,
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_processed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `num_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `tracking_code` varchar(255) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `final_status` enum('pending','pending_shipping','complete','failed','cancelled') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`user_id`,`orig_transact_id`),
  KEY `order_id` (`order_id`),
  KEY `final_status` (`final_status`),
  KEY `date_processed` (`date_processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Fleshlight_Usage_Log`
--

DROP TABLE IF EXISTS `Fleshlight_Usage_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fleshlight_Usage_Log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `is_eligible` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `comments` text NOT NULL,
  `url_requested` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('pending','failed','success') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=318516 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FlirtSMS_Transact`
--

DROP TABLE IF EXISTS `FlirtSMS_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `FlirtSMS_Transact` (
  `sms_transact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_sms_transact_id` int(11) unsigned DEFAULT NULL,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_credits` smallint(5) unsigned NOT NULL DEFAULT 0,
  `type` enum('purchase','credit') DEFAULT NULL,
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `reason_code` smallint(3) unsigned DEFAULT NULL,
  `reason_text` text DEFAULT NULL,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`sms_transact_id`),
  KEY `user_id` (`user_id`,`date_transact`),
  KEY `model_id` (`model_id`,`date_transact`)
) ENGINE=InnoDB AUTO_INCREMENT=41686 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Flirt_Phone_Transact`
--

DROP TABLE IF EXISTS `Flirt_Phone_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Flirt_Phone_Transact` (
  `phone_transact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_phone_transact_id` int(11) unsigned DEFAULT NULL,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `duration` smallint(5) unsigned NOT NULL DEFAULT 0,
  `credits_per_minute` smallint(5) unsigned NOT NULL DEFAULT 0,
  `num_credits` mediumint(7) unsigned NOT NULL,
  `type` enum('purchase','credit') NOT NULL DEFAULT 'purchase',
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `reason_code` smallint(3) unsigned DEFAULT NULL,
  `reason_text` text DEFAULT NULL,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`phone_transact_id`),
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`),
  KEY `date_transact` (`date_transact`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=133249 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`localhost`*/ /*!50003 TRIGGER BILLING.flirt_phone_transact_insert AFTER INSERT ON BILLING.Flirt_Phone_Transact 
 FOR EACH ROW BEGIN
 
 
	
	IF NEW.type = 'purchase' AND NEW.num_credits > 0 THEN 

		CALL BILLING.performer_affiliate_handler( NEW.user_id, NEW.model_id, 'flirt_phone_call', NEW.phone_transact_id, NEW.num_credits, NEW.date_transact );		

	END IF;
  

 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Gift_Model_Log`
--

DROP TABLE IF EXISTS `Gift_Model_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Gift_Model_Log` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) DEFAULT NULL,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `gift_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `last_gift_transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`model_id`,`user_nickname`),
  KEY `gift_count` (`gift_count`),
  KEY `service` (`service`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Gift_Transact`
--

DROP TABLE IF EXISTS `Gift_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Gift_Transact` (
  `gift_transact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_gift_transact_id` int(11) unsigned DEFAULT NULL,
  `gift_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `sent_by` enum('model','user') NOT NULL DEFAULT 'user',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_nickname` varchar(32) DEFAULT NULL,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `service` enum('girls','guys','trans') NOT NULL DEFAULT 'girls',
  `studio_code` char(12) DEFAULT NULL,
  `num_credits` mediumint(7) NOT NULL DEFAULT 0,
  `type` enum('purchase','credit') NOT NULL DEFAULT 'purchase',
  `gift_wrap` enum('Y','N') NOT NULL DEFAULT 'N',
  `gift_message` varchar(255) DEFAULT NULL,
  `gift_reply` varchar(255) DEFAULT NULL,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `reason_code` smallint(3) unsigned DEFAULT NULL,
  `reason_text` text DEFAULT NULL,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime DEFAULT NULL,
  `date_exported` datetime DEFAULT NULL,
  `date_last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `custom_gift_quantity` smallint(6) NOT NULL DEFAULT 0,
  `is_anonymous` tinyint(4) NOT NULL DEFAULT 0,
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `domain` varchar(253) NOT NULL DEFAULT '',
  `geo_country` varchar(3) NOT NULL DEFAULT '',
  `paypig_wallet_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`gift_transact_id`),
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`),
  KEY `studio_code` (`studio_code`),
  KEY `type` (`type`),
  KEY `date_transact` (`date_transact`),
  KEY `gift_wrap` (`gift_wrap`),
  KEY `service` (`service`),
  KEY `ip_address` (`ip_address`),
  KEY `geo_country` (`geo_country`),
  KEY `sitekey` (`sitekey`,`domain`),
  KEY `model_search` (`gift_id`,`model_id`,`date_transact`),
  KEY `model_sitekey_type_gw` (`model_id`,`sitekey`,`gift_wrap`,`type`),
  KEY `paypig_wallet_id` (`paypig_wallet_id`),
  KEY `user_nickname_userid` (`user_nickname`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=176206309 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`10.%.%.%`*/ /*!50003 TRIGGER `BILLING`.`gift_transact_insert` AFTER INSERT ON `Gift_Transact` FOR EACH ROW
BEGIN
 
IF NEW.ref_gift_transact_id IS NULL THEN
   
   IF NEW.custom_gift_quantity <> 0 THEN
   SET @gift_qty = NEW.custom_gift_quantity;
   ELSE
   SET @gift_qty = 1;
   END IF;
   

IF NEW.custom_gift_quantity <= 7 THEN
UPDATE BILLING.Gifts SET purchase_cnt = purchase_cnt + 1 WHERE id = NEW.gift_id;
ELSE
UPDATE BILLING.Gifts SET purchase_cnt = purchase_cnt + @gift_qty WHERE id = NEW.gift_id;
END IF;
   
   

IF NEW.gift_wrap = 'N' AND new.sent_by = 'user' THEN

INSERT INTO BILLING.Gift_Model_Log SET 
model_id = NEW.model_id, 
studio_code = NEW.studio_code, 
service = NEW.service, 
user_nickname = NEW.user_nickname, 
gift_count = @gift_qty, 
total_credits = NEW.num_credits, 
last_gift_transact_id = NEW.gift_transact_id 
ON DUPLICATE KEY UPDATE 
studio_code = NEW.studio_code, 
gift_count = gift_count + @gift_qty, 
total_credits = total_credits + NEW.num_credits, 
last_gift_transact_id = NEW.gift_transact_id;

END IF;



IF NEW.type = 'purchase' AND NEW.num_credits > 0 THEN 

CALL BILLING.performer_affiliate_handler( NEW.user_id, NEW.model_id, 'paid_gift', NEW.gift_transact_id, NEW.num_credits, NEW.date_transact );

END IF;

  
END IF;
  
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Gifts`
--

DROP TABLE IF EXISTS `Gifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Gifts` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `category` varchar(25) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `image_name` varchar(100) NOT NULL DEFAULT '',
  `credits` smallint(5) unsigned NOT NULL DEFAULT 0,
  `credits_vip` smallint(5) unsigned NOT NULL DEFAULT 0,
  `purchase_cnt` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `is_sticker` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `promo_only` tinyint(4) NOT NULL DEFAULT 0,
  `chat_message` varchar(255) NOT NULL DEFAULT '',
  `make_it_rain` tinyint(4) NOT NULL DEFAULT 0,
  `gift_set_ids` varchar(255) NOT NULL DEFAULT '',
  `service` tinyint(4) NOT NULL DEFAULT 0,
  `custom_quantity_allowed` tinyint(1) NOT NULL DEFAULT 0,
  `vip_only` tinyint(4) NOT NULL DEFAULT 0,
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `purchase_cnt` (`purchase_cnt`),
  KEY `category` (`category`),
  KEY `vip_only` (`vip_only`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=1172 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Group_Chat_Transact`
--

DROP TABLE IF EXISTS `Group_Chat_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Group_Chat_Transact` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_chat_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `customer_type_id` tinyint(3) NOT NULL DEFAULT 6,
  `user_timeleft_started` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `user_timeleft_deducted` mediumint(6) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_chat_id` (`group_chat_id`),
  KEY `model_id` (`model_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=10808184 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hall_of_Fame_Summary`
--

DROP TABLE IF EXISTS `Hall_of_Fame_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hall_of_Fame_Summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fame_type` tinyint(4) unsigned NOT NULL,
  `date` date NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fame_type_date` (`fame_type`,`date`),
  KEY `fame_type` (`fame_type`),
  KEY `date` (`date`),
  KEY `quantity` (`quantity`)
) ENGINE=InnoDB AUTO_INCREMENT=19144 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Hanger_Offers`
--

DROP TABLE IF EXISTS `Hanger_Offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Hanger_Offers` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '',
  `size` varchar(10) NOT NULL DEFAULT '',
  `special` tinyint(1) NOT NULL DEFAULT 0,
  `offer_multiple` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_active` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `date_active` (`date_active`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Holiday_Promo_Labels`
--

DROP TABLE IF EXISTS `Holiday_Promo_Labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Holiday_Promo_Labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `holiday_promo_id` int(11) NOT NULL,
  `label_name` varchar(255) NOT NULL,
  `label_text` text DEFAULT NULL,
  `label_text_guys` text DEFAULT NULL,
  `label_text_girls` text DEFAULT NULL,
  `allow_html` tinyint(4) NOT NULL DEFAULT 1,
  `ordering` int(11) NOT NULL DEFAULT 1000,
  `group_name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `holiday_promo_id` (`holiday_promo_id`,`label_name`),
  KEY `ordering` (`ordering`),
  KEY `group_name` (`group_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1153 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Holiday_Promo_Tag_Values`
--

DROP TABLE IF EXISTS `Holiday_Promo_Tag_Values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Holiday_Promo_Tag_Values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `holiday_promo_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `label_text_girls` text DEFAULT NULL,
  `label_text_guys` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `holiday_promo_id` (`holiday_promo_id`,`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1456 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Holiday_Promo_Tags`
--

DROP TABLE IF EXISTS `Holiday_Promo_Tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Holiday_Promo_Tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_group` varchar(255) NOT NULL,
  `label_name` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `force_caps` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label_name` (`label_name`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Holiday_Promo_Winners`
--

DROP TABLE IF EXISTS `Holiday_Promo_Winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Holiday_Promo_Winners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_promo_id` int(10) unsigned NOT NULL,
  `type` enum('ROYALTY','MOST_CUSTOMERS','MOST_COLLECTED','MODEL_GIFTS') DEFAULT NULL,
  `winner_girls` varchar(64) NOT NULL DEFAULT '',
  `winner_guys` varchar(64) NOT NULL DEFAULT '',
  `prize_amount` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `holiday_promo_id` (`holiday_promo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6268 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Holiday_Promos`
--

DROP TABLE IF EXISTS `Holiday_Promos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Holiday_Promos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `pre_promo_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pre_promo_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pre_promo_banner` int(1) NOT NULL DEFAULT 1,
  `promo_start` datetime NOT NULL,
  `promo_end` datetime NOT NULL,
  `studio_promo_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `make_it_rain_category` varchar(255) NOT NULL DEFAULT '',
  `gift_ids` varchar(255) NOT NULL DEFAULT '',
  `misc_badge_category` varchar(255) NOT NULL DEFAULT '',
  `credits_badge_category` varchar(255) NOT NULL DEFAULT '',
  `gift_count_badge_category` varchar(255) NOT NULL DEFAULT '',
  `contest_ids` varchar(255) NOT NULL DEFAULT '',
  `promo_ids` varchar(255) NOT NULL DEFAULT '',
  `collectible_name` varchar(64) NOT NULL DEFAULT 'Promo Gifts',
  `is_default_test_promo` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `pre_promo_start` (`pre_promo_start`,`pre_promo_end`),
  KEY `promo_start` (`promo_start`,`promo_end`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Ideal_Percent_Log`
--

DROP TABLE IF EXISTS `Ideal_Percent_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Ideal_Percent_Log` (
  `date_changed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` enum('merchant_bank','processor','bank') NOT NULL DEFAULT 'bank',
  `type_id` varchar(20) NOT NULL DEFAULT '',
  `ideal_percent` float(6,5) unsigned NOT NULL DEFAULT 0.00000,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date_changed`,`type`,`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Import_Log`
--

DROP TABLE IF EXISTS `Import_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Import_Log` (
  `data_type` enum('spy','private','activity','activity_copy') NOT NULL DEFAULT 'private',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `server_location` enum('dev','live') NOT NULL DEFAULT 'live'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `In_Show_Credits`
--

DROP TABLE IF EXISTS `In_Show_Credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `In_Show_Credits` (
  `datetime` datetime DEFAULT NULL,
  `id` int(11) unsigned DEFAULT NULL,
  `source` char(1) DEFAULT NULL,
  `amount` float(6,2) DEFAULT NULL,
  `credits` mediumint(6) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `model_id` int(11) unsigned DEFAULT NULL,
  `affiliate_id` mediumint(8) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Internal_Message_Languages`
--

DROP TABLE IF EXISTS `Internal_Message_Languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Internal_Message_Languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int(11) unsigned NOT NULL DEFAULT 0,
  `language` char(3) NOT NULL DEFAULT 'en',
  `message` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `message_id_language` (`message_id`,`language`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Internal_Messages`
--

DROP TABLE IF EXISTS `Internal_Messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Internal_Messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Internal_One_Click_Log`
--

DROP TABLE IF EXISTS `Internal_One_Click_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Internal_One_Click_Log` (
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  `success` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`datetime`,`user_id`),
  KEY `success` (`success`,`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Internal_Response_Codes`
--

DROP TABLE IF EXISTS `Internal_Response_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Internal_Response_Codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `message` varchar(255) DEFAULT '',
  `message_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=844 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MP_Code_Overwrite`
--

DROP TABLE IF EXISTS `MP_Code_Overwrite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MP_Code_Overwrite` (
  `datetime` datetime NOT NULL,
  `transact_id` int(11) NOT NULL,
  `new_mp_code` varchar(5) NOT NULL,
  `orig_mp_code` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Manager_Special`
--

DROP TABLE IF EXISTS `Manager_Special`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Manager_Special` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `daily_running_sales` int(10) unsigned NOT NULL DEFAULT 0,
  `daily_running_free_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `daily_projected_sales` int(10) unsigned NOT NULL DEFAULT 0,
  `daily_projected_free_credits` int(10) unsigned NOT NULL DEFAULT 0,
  `hourly_ratio` decimal(5,2) NOT NULL DEFAULT 0.00,
  `sales_target` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `current_offer` int(2) NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `comments` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `datetime` (`datetime`),
  KEY `current_offer` (`current_offer`)
) ENGINE=InnoDB AUTO_INCREMENT=154219 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MembershipSite`
--

DROP TABLE IF EXISTS `MembershipSite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MembershipSite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(100) NOT NULL DEFAULT '',
  `domain` varchar(100) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Merchant_Allocation_Tally`
--

DROP TABLE IF EXISTS `Merchant_Allocation_Tally`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Merchant_Allocation_Tally` (
  `merchant_bank` varchar(20) NOT NULL DEFAULT '',
  `payment_type` varchar(10) NOT NULL DEFAULT '',
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `bank_transactions` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_transactions` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`merchant_bank`,`payment_type`,`processor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Moderation_Event_Comments`
--

DROP TABLE IF EXISTS `Moderation_Event_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Moderation_Event_Comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `type` enum('system','user') NOT NULL DEFAULT 'system',
  `comments` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `date_created` (`date_created`),
  KEY `type` (`type`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1472385 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Moderation_Event_Images`
--

DROP TABLE IF EXISTS `Moderation_Event_Images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Moderation_Event_Images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(1024) NOT NULL DEFAULT '',
  `image_path` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2417779 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Moderation_Event_Violations`
--

DROP TABLE IF EXISTS `Moderation_Event_Violations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Moderation_Event_Violations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `title` varchar(255) NOT NULL DEFAULT '',
  `violation_type` varchar(255) NOT NULL DEFAULT '',
  `event_id` int(10) unsigned NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2422708 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Moderation_Events`
--

DROP TABLE IF EXISTS `Moderation_Events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Moderation_Events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL,
  `status` enum('resolved','ignored','pending') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `status` (`status`),
  KEY `date_created` (`date_created`),
  KEY `date_updated` (`date_updated`)
) ENGINE=InnoDB AUTO_INCREMENT=636324 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Offers`
--

DROP TABLE IF EXISTS `Offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Offers` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL DEFAULT '',
  `primary_package_id` int(11) unsigned NOT NULL DEFAULT 365,
  `primary_user_limit` tinyint(2) unsigned DEFAULT NULL,
  `fallback_package_id` int(11) unsigned NOT NULL DEFAULT 363,
  `fallback_user_limit` tinyint(2) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `date_range` (`date_start`,`date_end`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `One_Click_Log`
--

DROP TABLE IF EXISTS `One_Click_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `One_Click_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL DEFAULT '',
  `reg_template` varchar(40) NOT NULL DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `mid` varchar(125) NOT NULL DEFAULT '',
  `pi_code` varchar(125) NOT NULL DEFAULT '',
  `tokeninfo` varchar(125) NOT NULL DEFAULT '',
  `enc_subscription_id` text DEFAULT NULL,
  `referring_merchant_id` varchar(255) NOT NULL DEFAULT '',
  `referring_customer_id` varchar(255) NOT NULL DEFAULT '',
  `card_hash` varchar(255) NOT NULL DEFAULT '',
  `octoken` varchar(255) NOT NULL DEFAULT '',
  `cid` varchar(255) NOT NULL DEFAULT '',
  `iv_id` varchar(255) NOT NULL DEFAULT '0',
  `ipsp_cookie` text DEFAULT NULL,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `destination_url` text DEFAULT NULL,
  `origin_url` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `unique_reason` text DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=714112 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `One_Click_Redirect_Log`
--

DROP TABLE IF EXISTS `One_Click_Redirect_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `One_Click_Redirect_Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT 0,
  `experience` varchar(50) NOT NULL DEFAULT '',
  `source` enum('direct','api','cac','vac','cam','scem','lander') NOT NULL DEFAULT 'api',
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `datetime_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=12058575 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Package`
--

DROP TABLE IF EXISTS `Package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `type_id` smallint(3) unsigned DEFAULT NULL,
  `source_site_id` smallint(6) NOT NULL DEFAULT 1,
  `service` varchar(255) NOT NULL,
  `payment_type` varchar(10) NOT NULL,
  `sitekey` varchar(20) NOT NULL,
  `credits_per_dollar` float(5,3) DEFAULT 10.000,
  `is_default` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=368 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Package_Product`
--

DROP TABLE IF EXISTS `Package_Product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Package_Product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) DEFAULT 0,
  `icon_name` varchar(255) DEFAULT NULL,
  `user_limit` smallint(6) DEFAULT 0,
  `user_limit_duration` smallint(6) DEFAULT 0,
  `total_limit` smallint(6) DEFAULT 0,
  `total_limit_duration` smallint(6) DEFAULT 0,
  `vip_offer` tinyint(1) DEFAULT 0,
  `date_start` datetime DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39071 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PaymentAttempt`
--

DROP TABLE IF EXISTS `PaymentAttempt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `PaymentAttempt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` varchar(25) DEFAULT NULL,
  `product_type` varchar(25) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `country` char(2) NOT NULL,
  `mp_code` varchar(5) DEFAULT NULL,
  `source_code` varchar(50) DEFAULT NULL,
  `sitekey` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`),
  KEY `user_id` (`user_id`),
  KEY `country` (`country`)
) ENGINE=InnoDB AUTO_INCREMENT=71559143 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PaymentAttempt_Step`
--

DROP TABLE IF EXISTS `PaymentAttempt_Step`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `PaymentAttempt_Step` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PA_ID` int(11) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `amount` float(5,2) unsigned NOT NULL DEFAULT 0.00,
  `error_code` smallint(4) NOT NULL DEFAULT 0,
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `processor_decline_msg` varchar(255) NOT NULL,
  `time` mediumint(6) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `PaymentAttempt_Step` (`PA_ID`),
  KEY `type_id` (`type_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=477702 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PaymentAttempt_Step_Log`
--

DROP TABLE IF EXISTS `PaymentAttempt_Step_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `PaymentAttempt_Step_Log` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `type_id` int(11) unsigned NOT NULL DEFAULT 0,
  `registered_user` enum('Y','N') NOT NULL DEFAULT 'N',
  `total` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`type_id`,`registered_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PaymentAttempt_Step_Type`
--

DROP TABLE IF EXISTS `PaymentAttempt_Step_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `PaymentAttempt_Step_Type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Icon_to_Type`
--

DROP TABLE IF EXISTS `Payment_Icon_to_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Icon_to_Type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `payment_type` smallint(5) unsigned DEFAULT NULL,
  `payment_icon` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `icon_to_type` (`payment_type`,`payment_icon`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Icons`
--

DROP TABLE IF EXISTS `Payment_Icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Icons` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Payment_Types`
--

DROP TABLE IF EXISTS `Payment_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment_Types` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `lowest_price` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `info_description` varchar(255) NOT NULL DEFAULT '',
  `risky` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `auto_enabled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Peekshows_Argus_Tokens`
--

DROP TABLE IF EXISTS `Peekshows_Argus_Tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Peekshows_Argus_Tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ps_cust_id` int(11) NOT NULL DEFAULT 0,
  `vs_cust_id` int(11) NOT NULL DEFAULT 0,
  `ar_cust_id` int(11) NOT NULL DEFAULT 0,
  `ar_pay_id` int(11) NOT NULL DEFAULT 0,
  `bin` varchar(6) NOT NULL DEFAULT '',
  `last_digits` varchar(4) NOT NULL DEFAULT '',
  `date_expire` varchar(6) NOT NULL DEFAULT '',
  `date_imported` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ps_cust_id` (`ps_cust_id`,`vs_cust_id`,`ar_cust_id`,`ar_pay_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1858064 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Peekshows_Customers`
--

DROP TABLE IF EXISTS `Peekshows_Customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Peekshows_Customers` (
  `username` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(50) DEFAULT NULL,
  `passwordtemp` varchar(50) DEFAULT NULL,
  `passworddate` datetime DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `phone` varchar(32) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `emailnewsletter` enum('Y','N') NOT NULL DEFAULT 'N',
  `signuptime` varchar(50) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ipaddress` varchar(50) DEFAULT NULL,
  `cust_id` int(11) NOT NULL DEFAULT 0,
  `lastlogin` datetime DEFAULT NULL,
  `ip_address` varchar(16) DEFAULT NULL,
  `cardstatus1` char(1) DEFAULT NULL,
  `cardid1` varchar(8) DEFAULT NULL,
  `cardstatus3` char(1) DEFAULT NULL,
  `cardstatus2` char(1) DEFAULT NULL,
  `cardid2` varchar(8) DEFAULT NULL,
  `cardid3` varchar(8) DEFAULT NULL,
  `totalcomps` int(11) DEFAULT NULL,
  `totalbilling` float(10,2) NOT NULL DEFAULT 0.00,
  `first_purchase` date NOT NULL DEFAULT '0000-00-00',
  `country` varchar(64) DEFAULT NULL,
  `country_code` char(2) NOT NULL DEFAULT '',
  `uniqueID` varchar(32) DEFAULT NULL,
  `uniqueID2` varchar(32) DEFAULT NULL,
  `vs_new_user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `vs_username_exists` enum('Y','N','P') NOT NULL DEFAULT 'P',
  `vs_email_exists` enum('Y','N','P') NOT NULL DEFAULT 'P',
  `vs_skip_reason` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`username`),
  KEY `email` (`email`),
  KEY `password` (`password`),
  KEY `lastlogin` (`lastlogin`),
  KEY `uniqueID` (`uniqueID`),
  KEY `vs_new_user_id` (`vs_new_user_id`),
  KEY `vs_username_exists` (`vs_username_exists`),
  KEY `vs_email_exists` (`vs_email_exists`),
  KEY `vs_skip_reason` (`vs_skip_reason`),
  KEY `cust_id` (`cust_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Peekshows_Customers_Payments`
--

DROP TABLE IF EXISTS `Peekshows_Customers_Payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Peekshows_Customers_Payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cust_id` int(11) NOT NULL DEFAULT 0,
  `bin` varchar(6) NOT NULL DEFAULT '0',
  `last_digits` varchar(4) NOT NULL DEFAULT '0',
  `exp_month` varchar(2) NOT NULL DEFAULT '0',
  `exp_year` varchar(4) NOT NULL DEFAULT '0',
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `zip` varchar(64) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_last_purchased` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cust_id` (`cust_id`,`bin`,`last_digits`),
  KEY `cust_id_idx` (`cust_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8384 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Peekshows_Rocketgate_Tokens`
--

DROP TABLE IF EXISTS `Peekshows_Rocketgate_Tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Peekshows_Rocketgate_Tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vs_cust_id` int(11) NOT NULL DEFAULT 0,
  `rg_pay_id` varchar(255) NOT NULL DEFAULT '',
  `bin` varchar(6) NOT NULL DEFAULT '',
  `last_digits` varchar(4) NOT NULL DEFAULT '',
  `card_expire` varchar(4) NOT NULL DEFAULT '',
  `date_imported` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ps_cust_id` (`vs_cust_id`,`rg_pay_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7904 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci PACK_KEYS=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Peekshows_Tokens`
--

DROP TABLE IF EXISTS `Peekshows_Tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Peekshows_Tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `bank` int(8) NOT NULL DEFAULT 0,
  `tokens` int(8) NOT NULL DEFAULT 0,
  `adultclub` int(8) NOT NULL DEFAULT 0,
  `nude1on1` int(8) NOT NULL DEFAULT 0,
  `euroticalive` int(8) NOT NULL DEFAULT 0,
  `militaryguy` int(8) NOT NULL DEFAULT 0,
  `wickedwest` int(8) NOT NULL DEFAULT 0,
  `purepornlive` int(8) NOT NULL DEFAULT 0,
  `pcgirls` int(8) NOT NULL DEFAULT 0,
  `naughtygirlslive` int(8) NOT NULL DEFAULT 0,
  `removed` int(8) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_2` (`username`),
  KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=79729 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Deals_Codes`
--

DROP TABLE IF EXISTS `Performer_Deals_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Deals_Codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL,
  `code` varchar(20) NOT NULL,
  `screen_name` varchar(32) DEFAULT NULL,
  `seconds_value` smallint(5) unsigned NOT NULL,
  `seconds_required` smallint(5) unsigned NOT NULL,
  `uses` smallint(5) unsigned NOT NULL DEFAULT 0,
  `max_uses` smallint(5) unsigned NOT NULL,
  `date_created` date NOT NULL,
  `date_expire` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`),
  KEY `screen_name` (`screen_name`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=487980 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Performer_Deals_Redemption`
--

DROP TABLE IF EXISTS `Performer_Deals_Redemption`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Performer_Deals_Redemption` (
  `deal_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `show_activity_id` int(11) unsigned NOT NULL,
  `redeemed_value` smallint(5) unsigned NOT NULL,
  `date_redeemed` datetime NOT NULL,
  KEY `deal_id` (`deal_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Photos_Transact`
--

DROP TABLE IF EXISTS `Photos_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Photos_Transact` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `num_credits` smallint(5) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `ref_photo_transact_id` int(11) unsigned DEFAULT NULL,
  `type` enum('credit','purchase') DEFAULT 'purchase',
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `date_transact` (`date_transact`),
  KEY `user_id` (`user_id`),
  KEY `photo_id` (`photo_id`),
  KEY `model_id` (`model_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Play_And_Pay_Pending`
--

DROP TABLE IF EXISTS `Play_And_Pay_Pending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Play_And_Pay_Pending` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `activity_type` varchar(20) NOT NULL DEFAULT '',
  `activity_ref_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ip_address` varbinary(16) NOT NULL DEFAULT '',
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT 'flirt4free',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `amount` float(6,2) NOT NULL DEFAULT 0.00,
  `amount_tax` float(6,2) NOT NULL DEFAULT 0.00,
  `num_credits` smallint(5) unsigned NOT NULL DEFAULT 0,
  `credits_per_dollar` float(5,3) DEFAULT 10.000,
  `num_process_attempts` smallint(5) unsigned NOT NULL DEFAULT 0,
  `last_process_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_error_code` char(3) DEFAULT NULL,
  `last_user_payment_id` int(11) unsigned DEFAULT NULL,
  `status` enum('pending','processing','failed') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_type` (`activity_type`),
  KEY `activity_ref_id` (`activity_ref_id`),
  KEY `model_id` (`model_id`),
  KEY `last_process_date` (`last_process_date`),
  KEY `status` (`status`),
  KEY `date_created` (`date_created`),
  KEY `ip_address` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=5772343 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Postback_Activity`
--

DROP TABLE IF EXISTS `Postback_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Postback_Activity` (
  `billing_ref_id` varchar(32) NOT NULL DEFAULT '',
  `processor_id` tinyint(3) NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT '',
  `subtype` varchar(50) NOT NULL DEFAULT '',
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `processor_inv_id` varchar(255) NOT NULL DEFAULT '',
  `result` varchar(32) NOT NULL DEFAULT '',
  `notes` varchar(255) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `billing_ref_id` (`billing_ref_id`),
  KEY `processor_id` (`processor_id`),
  KEY `type` (`type`),
  KEY `subtype` (`subtype`),
  KEY `transact_id` (`transact_id`),
  KEY `processor_inv_id` (`processor_inv_id`),
  KEY `result` (`result`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Postback_Queue`
--

DROP TABLE IF EXISTS `Postback_Queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Postback_Queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `processor_id` tinyint(3) NOT NULL DEFAULT 0,
  `processor_inv_id` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `subtype` varchar(50) NOT NULL DEFAULT '',
  `request` text DEFAULT NULL,
  `server` varchar(50) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `processor_id` (`processor_id`),
  KEY `type` (`type`),
  KEY `subtype` (`subtype`),
  KEY `server` (`server`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=12939462 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Processor`
--

DROP TABLE IF EXISTS `Processor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Processor` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT 0,
  `running` char(1) NOT NULL,
  `priority` tinyint(3) unsigned DEFAULT 0,
  `gateway` tinyint(1) unsigned DEFAULT 0,
  `api_enabled` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `commission_adjustment` float(6,5) NOT NULL DEFAULT 1.00000,
  `ideal_percent` float(6,5) unsigned NOT NULL,
  `add_cc_count` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProcessorTransact`
--

DROP TABLE IF EXISTS `ProcessorTransact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ProcessorTransact` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `processor_id` int(11) NOT NULL DEFAULT 0,
  `processor_inv_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProcessorUsers`
--

DROP TABLE IF EXISTS `ProcessorUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ProcessorUsers` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `processor_id` int(11) NOT NULL DEFAULT 0,
  `processor_user_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Processor_Log`
--

DROP TABLE IF EXISTS `Processor_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Processor_Log` (
  `processor_id` int(3) NOT NULL DEFAULT 0,
  `processor_txn_id` int(11) NOT NULL DEFAULT 0,
  `processor_ref_txn_id` int(11) NOT NULL DEFAULT 0,
  `merchant_account` varchar(32) NOT NULL DEFAULT '',
  `txn_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `type` enum('sale','refund','chargeback','failure') NOT NULL DEFAULT 'sale',
  `amount` float(8,2) NOT NULL DEFAULT 0.00,
  `settled_amount` float(8,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(32) NOT NULL DEFAULT '',
  `settled_currency` varchar(32) NOT NULL DEFAULT '',
  `postback_str` longtext NOT NULL,
  `processed` enum('Y','N') NOT NULL DEFAULT 'N',
  `validated` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`processor_id`,`processor_txn_id`,`processor_ref_txn_id`),
  UNIQUE KEY `processor_pb` (`processor_id`,`postback_str`(500))
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Processor_Payment_Type`
--

DROP TABLE IF EXISTS `Processor_Payment_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Processor_Payment_Type` (
  `processor_id` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `payment_type` varchar(10) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Processor_Product_bk`
--

DROP TABLE IF EXISTS `Processor_Product_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Processor_Product_bk` (
  `product_id` int(11) NOT NULL DEFAULT 0,
  `processor_id` int(11) NOT NULL DEFAULT 1,
  `processor_product_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Processor_Response_Codes`
--

DROP TABLE IF EXISTS `Processor_Response_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Processor_Response_Codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `processor_id` int(11) unsigned NOT NULL DEFAULT 0,
  `code` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `internal_code` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `processor_code` (`processor_id`,`code`),
  KEY `processor_id` (`processor_id`),
  KEY `code` (`code`),
  KEY `internal_code` (`internal_code`)
) ENGINE=InnoDB AUTO_INCREMENT=1336 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Processor_Transactions`
--

DROP TABLE IF EXISTS `Processor_Transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Processor_Transactions` (
  `processor_id` tinyint(3) NOT NULL DEFAULT 0,
  `processor_inv_id` varchar(255) NOT NULL DEFAULT '',
  `processor_member_id` varchar(255) DEFAULT NULL,
  `processor_auth_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `processor_settle_date` datetime DEFAULT NULL,
  `processor_response` varchar(255) NOT NULL DEFAULT '',
  `processor_site_id` varchar(20) NOT NULL DEFAULT '',
  `credit_reason` varchar(20) NOT NULL DEFAULT '',
  `cb_report_date` datetime DEFAULT NULL,
  `cb_dispute_date` datetime DEFAULT NULL,
  `cb_dispute_type` varchar(20) NOT NULL DEFAULT '',
  `cb_reason` varchar(20) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned DEFAULT NULL,
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `transaction_date` datetime DEFAULT NULL,
  `merch_acct_id` varchar(20) NOT NULL DEFAULT '',
  `merch_acct_name` varchar(255) NOT NULL DEFAULT '',
  `merch_acct_number` varchar(20) NOT NULL DEFAULT '',
  `trans_amount` float(8,2) NOT NULL DEFAULT 0.00,
  `order_amount` decimal(5,2) NOT NULL DEFAULT 0.00,
  `currency_requested` varchar(20) NOT NULL DEFAULT '',
  `currency_settled` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '',
  `processing_type` enum('gateway','ipsp') NOT NULL DEFAULT 'gateway',
  `system` enum('billing','xvp') NOT NULL DEFAULT 'billing',
  PRIMARY KEY (`processor_inv_id`,`processor_auth_date`,`type`,`status`),
  KEY `transact_id` (`transact_id`),
  KEY `user_id` (`user_id`),
  KEY `processor_inv_id` (`processor_inv_id`),
  KEY `processor_settle_date_idx` (`processor_settle_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product`
--

DROP TABLE IF EXISTS `Product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `html_description` varchar(255) DEFAULT NULL,
  `category` enum('hot_deal','promo_code','promo_offer','standard') NOT NULL DEFAULT 'standard',
  `price` float(8,2) DEFAULT 0.00,
  `cs_price` float(8,2) DEFAULT 0.00,
  `renew_product_id` int(11) DEFAULT 0,
  `ed_product_id` int(11) DEFAULT 0,
  `fleshlight_product_id` int(11) NOT NULL DEFAULT 0,
  `user_purchase_limit` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `source_id` smallint(4) unsigned NOT NULL DEFAULT 1,
  `credits_per_dollar` float(5,3) DEFAULT 10.000,
  `status` mediumint(9) DEFAULT 1,
  `platform` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `platform` (`platform`)
) ENGINE=InnoDB AUTO_INCREMENT=143332 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProductCodeTranslation_bk`
--

DROP TABLE IF EXISTS `ProductCodeTranslation_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ProductCodeTranslation_bk` (
  `product_type` int(11) DEFAULT NULL,
  `old_prod_code` varchar(50) DEFAULT NULL,
  `new_prod_id` int(11) DEFAULT NULL,
  KEY `old_prod_code` (`old_prod_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProductSite_bk`
--

DROP TABLE IF EXISTS `ProductSite_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ProductSite_bk` (
  `product_id` int(11) NOT NULL DEFAULT 0,
  `site_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProductType_bk`
--

DROP TABLE IF EXISTS `ProductType_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ProductType_bk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProductUnit_bk`
--

DROP TABLE IF EXISTS `ProductUnit_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ProductUnit_bk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product_Code_Translation`
--

DROP TABLE IF EXISTS `Product_Code_Translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Product_Code_Translation` (
  `old_prod_id` varchar(255) DEFAULT NULL,
  `new_prod_id` int(11) DEFAULT NULL,
  KEY `old_prod_id` (`old_prod_id`),
  KEY `new_prod_id` (`new_prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product_Item`
--

DROP TABLE IF EXISTS `Product_Item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Product_Item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `quantity` mediumint(6) NOT NULL DEFAULT 0,
  `quantity_free` mediumint(6) NOT NULL DEFAULT 0,
  `percent_free` float(6,2) NOT NULL DEFAULT 0.00,
  `product_unit_id` tinyint(2) unsigned NOT NULL,
  `product_type_id` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43368 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product_Price`
--

DROP TABLE IF EXISTS `Product_Price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Product_Price` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `currency` varchar(3) NOT NULL DEFAULT '',
  `price` float(8,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `currency` (`currency`)
) ENGINE=InnoDB AUTO_INCREMENT=82160 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product_Type`
--

DROP TABLE IF EXISTS `Product_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Product_Type` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product_Unit`
--

DROP TABLE IF EXISTS `Product_Unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Product_Unit` (
  `id` tinyint(2) unsigned NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Product_bk`
--

DROP TABLE IF EXISTS `Product_bk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Product_bk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  `price` float(5,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit` int(11) NOT NULL DEFAULT 1,
  `product_type` int(11) NOT NULL DEFAULT 1,
  `service` int(11) NOT NULL DEFAULT 1,
  `renews_as` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo`
--

DROP TABLE IF EXISTS `Promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `percent` tinyint(2) DEFAULT 0,
  `status` char(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Code`
--

DROP TABLE IF EXISTS `Promo_Code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Code` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `promo_id` smallint(4) unsigned NOT NULL,
  `promo_code` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `percent_credits` float(4,2) NOT NULL DEFAULT 0.00,
  `percent_discount` float(5,2) NOT NULL DEFAULT 0.00,
  `dollar_discount` float(6,2) NOT NULL DEFAULT 0.00,
  `min_purchase` float(7,2) NOT NULL DEFAULT 0.00,
  `max_purchase` float(7,2) NOT NULL DEFAULT 0.00,
  `vip_only` tinyint(1) NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `whitelabel_domain` varchar(255) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `max_usage` int(6) unsigned NOT NULL DEFAULT 0,
  `max_per_user` int(6) unsigned NOT NULL DEFAULT 0,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `status` char(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `delivery_type` enum('email','site','all') NOT NULL DEFAULT 'site',
  `platform` varchar(20) NOT NULL DEFAULT 'flirt4free',
  PRIMARY KEY (`id`),
  KEY `user_search` (`user_id`,`status`,`delivery_type`)
) ENGINE=InnoDB AUTO_INCREMENT=93976 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Code_User`
--

DROP TABLE IF EXISTS `Promo_Code_User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Code_User` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `promo_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `promo_code_id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `promo_code` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `whitelabel_domain` varchar(255) NOT NULL DEFAULT '',
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `mp_code` varchar(5) NOT NULL DEFAULT '',
  `transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `price` float(7,2) NOT NULL DEFAULT 0.00,
  `cs_price` float(7,2) NOT NULL DEFAULT 0.00,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=136492 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Code_User_Attempts`
--

DROP TABLE IF EXISTS `Promo_Code_User_Attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Code_User_Attempts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_attempt` (`user_id`,`sitekey`)
) ENGINE=InnoDB AUTO_INCREMENT=12818 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Discounts`
--

DROP TABLE IF EXISTS `Promo_Discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Discounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `credits` int(11) unsigned NOT NULL DEFAULT 0,
  `price` float(5,2) unsigned NOT NULL DEFAULT 0.00,
  `discount` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `credits` (`credits`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Filler_Settings`
--

DROP TABLE IF EXISTS `Promo_Filler_Settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Filler_Settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant` varchar(16) NOT NULL DEFAULT 'vsm',
  `geo` varchar(3) NOT NULL DEFAULT 'us',
  `type` varchar(32) NOT NULL DEFAULT 'visa',
  `system_daily_transact` int(11) NOT NULL DEFAULT 0,
  `user_daily_transact` int(11) NOT NULL DEFAULT 0,
  `user_monthly_transact` int(11) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Winners`
--

DROP TABLE IF EXISTS `Promo_Winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Winners` (
  `promo_id` int(11) unsigned NOT NULL,
  `rank` int(11) unsigned NOT NULL,
  `model_id_girls` int(11) unsigned NOT NULL,
  `model_id_guys` int(11) unsigned NOT NULL,
  `credits_girls` int(11) unsigned NOT NULL,
  `credits_guys` int(11) unsigned NOT NULL,
  `confirmed` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`promo_id`,`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promo_Winners_Cash_Bonus`
--

DROP TABLE IF EXISTS `Promo_Winners_Cash_Bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promo_Winners_Cash_Bonus` (
  `promo_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `cash_bonus` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`promo_id`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promos`
--

DROP TABLE IF EXISTS `Promos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `contest_winners` int(11) NOT NULL DEFAULT 0,
  `contest_type` varchar(25) NOT NULL DEFAULT 'standard',
  `total_prize_money` float(8,2) DEFAULT NULL,
  `cash_bonus_1` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `cash_bonus_2` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `folder` varchar(100) NOT NULL,
  `folder2` varchar(100) DEFAULT NULL,
  `folder3` varchar(100) DEFAULT NULL,
  `link_url` text NOT NULL,
  `title_girls` varchar(255) NOT NULL,
  `title_girls2` varchar(255) NOT NULL DEFAULT '',
  `title_girls3` varchar(255) NOT NULL DEFAULT '',
  `title_guys` varchar(255) NOT NULL,
  `title_guys2` varchar(255) NOT NULL DEFAULT '',
  `title_guys3` varchar(255) NOT NULL DEFAULT '',
  `category_girls` int(11) unsigned DEFAULT 0,
  `category_guys` int(11) unsigned DEFAULT 0,
  `broadcasting_desc` text NOT NULL,
  `customer_desc_girls` text NOT NULL,
  `customer_desc_guys` text NOT NULL,
  `multi_service` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `package_name` varchar(255) DEFAULT NULL,
  `default_discount` int(11) unsigned NOT NULL DEFAULT 0,
  `discounts` varchar(255) NOT NULL DEFAULT '',
  `source_site_id` int(11) unsigned DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `perks` text NOT NULL,
  `prize_breakdown` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `start` (`start`),
  KEY `end` (`end`),
  KEY `folder` (`folder`)
) ENGINE=InnoDB AUTO_INCREMENT=4177 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promos_Credit_Tiers_Join`
--

DROP TABLE IF EXISTS `Promos_Credit_Tiers_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promos_Credit_Tiers_Join` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) unsigned NOT NULL DEFAULT 0,
  `credit_tier_id` smallint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `join_key` (`promo_id`,`credit_tier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Promos_Winners`
--

DROP TABLE IF EXISTS `Promos_Winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Promos_Winners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) unsigned NOT NULL,
  `rank` int(11) NOT NULL,
  `service` enum('girls','guys','multi') NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `total_credits` int(11) unsigned DEFAULT 0,
  `total_hours` int(11) unsigned NOT NULL DEFAULT 0,
  `cash_bonus` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `last_updated` datetime DEFAULT current_timestamp(),
  `confirmed` enum('N','Y') NOT NULL DEFAULT 'N',
  `cb_payment_status` enum('pending','paid','non_payee') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `promo_service_rank` (`promo_id`,`service`,`rank`),
  KEY `model_id` (`model_id`),
  KEY `last_updated` (`last_updated`),
  KEY `confirmed` (`confirmed`),
  KEY `cash_bonus` (`cash_bonus`)
) ENGINE=InnoDB AUTO_INCREMENT=19937805 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `RB_Customers`
--

DROP TABLE IF EXISTS `RB_Customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `RB_Customers` (
  `customer_id` int(11) unsigned NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(256) NOT NULL DEFAULT '',
  `lifetime_spend` int(11) unsigned NOT NULL DEFAULT 0,
  `last_transaction_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `signed_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip_address_long` int(11) unsigned NOT NULL DEFAULT 0,
  `paid_credits` mediumint(5) unsigned NOT NULL DEFAULT 0,
  `screen_name` varchar(32) NOT NULL DEFAULT '',
  `cellphone_number` varchar(16) NOT NULL DEFAULT '',
  `vsm_user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `vsm_username_conflict` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `vsm_screen_name_conflict` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `vsm_email_conflict` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `vsm_user_email_conflict` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `vsm_invalid_username` varchar(255) NOT NULL DEFAULT '',
  `vsm_invalid_screen_name` varchar(255) NOT NULL DEFAULT '',
  `vsm_username_notes` varchar(255) NOT NULL DEFAULT '',
  `vsm_screen_name_notes` varchar(255) NOT NULL DEFAULT '',
  `vsm_email_notes` varchar(255) NOT NULL DEFAULT '',
  `vsm_mp_code` varchar(5) NOT NULL DEFAULT 'b7cer',
  `vsm_date_imported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `customer_id` (`customer_id`),
  KEY `vsm_mp_code` (`vsm_mp_code`),
  KEY `vsm_user_id` (`vsm_user_id`),
  KEY `vsm_username_conflict` (`vsm_username_conflict`),
  KEY `vsm_screen_name_conflict` (`vsm_screen_name_conflict`),
  KEY `vsm_email_conflict` (`vsm_email_conflict`),
  KEY `vsm_user_email_conflict` (`vsm_user_email_conflict`),
  KEY `vsm_date_imported` (`vsm_date_imported`),
  KEY `vsm_invalid_username` (`vsm_invalid_username`),
  KEY `vsm_invalid_screen_name` (`vsm_invalid_screen_name`),
  KEY `vsm_username_notes` (`vsm_username_notes`),
  KEY `vsm_screen_name_notes` (`vsm_screen_name_notes`),
  KEY `vsm_email_notes` (`vsm_email_notes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recur`
--

DROP TABLE IF EXISTS `Recur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `prod_id` varchar(6) DEFAULT NULL,
  `user_payment_id` int(11) DEFAULT NULL,
  `orig_transact_id` int(11) DEFAULT NULL,
  `last_transact_id` int(11) DEFAULT NULL,
  `last_processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `last_recur_date` datetime DEFAULT NULL,
  `last_bill_date` datetime DEFAULT NULL,
  `next_bill_date` datetime DEFAULT NULL,
  `source_id` tinyint(3) unsigned DEFAULT 1,
  `fail_count` tinyint(3) unsigned DEFAULT 0,
  `status` char(1) DEFAULT 'A',
  `accepted_amount` float(8,2) DEFAULT NULL,
  `accepted_currency` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `orig_transact_id` (`orig_transact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=412372 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recur_Log`
--

DROP TABLE IF EXISTS `Recur_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recur_Log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `recur_id` int(11) NOT NULL DEFAULT 0,
  `active_member_id` int(11) NOT NULL DEFAULT 0,
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `amount` float(6,2) NOT NULL DEFAULT 0.00,
  `date_transact` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `recur_id` (`recur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16330770 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Refund_Activity`
--

DROP TABLE IF EXISTS `Refund_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Refund_Activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio` char(12) DEFAULT NULL,
  `show_activity_id` int(11) unsigned NOT NULL DEFAULT 0,
  `created_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `amount_to_user` int(11) DEFAULT 0,
  `days_to_user` smallint(5) DEFAULT 0,
  `vod_passes_to_user` tinyint(3) DEFAULT 0,
  `amount_from_model` mediumint(9) DEFAULT 0,
  `reason_code` smallint(3) unsigned NOT NULL DEFAULT 0,
  `reason_text` text NOT NULL,
  `date_exported` date NOT NULL DEFAULT '0000-00-00',
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `review_notes` text NOT NULL,
  `showpass_to_user` smallint(5) DEFAULT 0,
  `rewards_points_to_user` smallint(5) unsigned NOT NULL DEFAULT 0,
  `bonus_from_model` int(10) DEFAULT 0,
  `tickets_to_user` int(11) DEFAULT 0,
  `dm_credits_to_user` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `date_exported` (`date_exported`),
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`),
  KEY `performer_activity_id` (`show_activity_id`),
  KEY `reason_code` (`reason_code`),
  KEY `created_datetime` (`created_datetime`),
  KEY `review_datetime` (`review_datetime`),
  KEY `review_status` (`review_status`)
) ENGINE=InnoDB AUTO_INCREMENT=10266012 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`localhost`*/ /*!50003 TRIGGER `BILLING`.`refund_activity_insert` AFTER INSERT ON `Refund_Activity` FOR EACH ROW
BEGIN 
		
		IF @prevent_refund_activity_trigger IS NULL THEN
		
			SELECT total_spent  
			INTO @o_total_spent 
			FROM ntl_db.optiusers 
			WHERE user_id = NEW.user_id;
			
			IF ( @o_total_spent > 0 ) OR ( @o_total_spent = 0 AND NEW.amount_to_user < 0 ) THEN 
			
				UPDATE ntl_db.optiusers_ex 
				SET total_credits_since_2010 = GREATEST( CAST( total_credits_since_2010 AS SIGNED ) + NEW.amount_to_user, 0 ) 
				WHERE user_id = NEW.user_id;
			
			END IF;
		
		END IF;
		
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Refund_Objections`
--

DROP TABLE IF EXISTS `Refund_Objections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Refund_Objections` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `show_id` int(11) unsigned NOT NULL DEFAULT 0,
  `created_admin_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_comments` text NOT NULL,
  `review_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `review_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_comments` text NOT NULL,
  `review_refund_id` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('pending','closed') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `show_id` (`show_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2605 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Refund_Reasons`
--

DROP TABLE IF EXISTS `Refund_Reasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Refund_Reasons` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL DEFAULT '',
  `is_deductable` tinyint(1) unsigned DEFAULT 0,
  `display` char(1) DEFAULT 'Y',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `rank` smallint(4) NOT NULL DEFAULT 0,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Return`
--

DROP TABLE IF EXISTS `Return`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `return_type` int(11) NOT NULL DEFAULT 0,
  `amount` float(5,2) NOT NULL DEFAULT 0.00,
  `date_in` float(13,3) NOT NULL DEFAULT 0.000,
  `date_out` float(13,3) NOT NULL DEFAULT 0.000,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22002 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ReturnType`
--

DROP TABLE IF EXISTS `ReturnType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ReturnType` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Review_Comments`
--

DROP TABLE IF EXISTS `Review_Comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Review_Comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `review_item_id` int(11) unsigned NOT NULL DEFAULT 0,
  `type` enum('system','user') NOT NULL DEFAULT 'system',
  `comments` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `review_item_id` (`review_item_id`),
  KEY `type` (`type`),
  KEY `datetime` (`datetime`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1543805 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Review_Items`
--

DROP TABLE IF EXISTS `Review_Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Review_Items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_type` varchar(20) NOT NULL DEFAULT '',
  `item_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `status` enum('pending','closed') NOT NULL DEFAULT 'pending',
  `assigned_group` varchar(20) NOT NULL DEFAULT '',
  `assigned_admin_id` int(11) NOT NULL DEFAULT 0,
  `details` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `admin_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `item_type` (`item_type`),
  KEY `item_id` (`item_id`),
  KEY `title` (`title`),
  KEY `status` (`status`),
  KEY `assigned_group` (`assigned_group`),
  KEY `assigned_admin_id` (`assigned_admin_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=471963 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Risk_Answers`
--

DROP TABLE IF EXISTS `Risk_Answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Risk_Answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(10) unsigned NOT NULL DEFAULT 0,
  `question_id` int(10) unsigned NOT NULL DEFAULT 0,
  `answer` enum('yes','no','not_applicable') DEFAULT 'not_applicable',
  `impression` enum('looks_good','looks_bad','not_sure') DEFAULT 'not_sure',
  `note` mediumtext DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `assessment_id` (`assessment_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8734 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Risk_Assessments`
--

DROP TABLE IF EXISTS `Risk_Assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Risk_Assessments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL,
  `impression` enum('looks_good','looks_bad','not_sure') DEFAULT 'not_sure',
  `note` mediumtext DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Risk_Questions`
--

DROP TABLE IF EXISTS `Risk_Questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Risk_Questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL DEFAULT '',
  `subsection` varchar(50) NOT NULL DEFAULT '',
  `question` mediumtext DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT 0,
  `updated_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `section` (`section`),
  KEY `subsection` (`subsection`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Risk_Scores`
--

DROP TABLE IF EXISTS `Risk_Scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Risk_Scores` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `data_type` varchar(20) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `composite_score` enum('3','6','9') NOT NULL DEFAULT '9',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `score_pair` (`data_type`,`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=65629 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Search_Reason`
--

DROP TABLE IF EXISTS `Search_Reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Search_Reason` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Search_Tracking`
--

DROP TABLE IF EXISTS `Search_Tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Search_Tracking` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `search_reason_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `search_params_csv` text NOT NULL,
  `user_id_results_csv` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=4673287 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SegPay_Proportions`
--

DROP TABLE IF EXISTS `SegPay_Proportions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `SegPay_Proportions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant` varchar(16) NOT NULL DEFAULT 'vsm',
  `geo` varchar(16) NOT NULL DEFAULT 'us',
  `target` smallint(3) NOT NULL DEFAULT 0,
  `comment` text DEFAULT NULL,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_effective` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `merchant` (`merchant`),
  KEY `geo` (`geo`),
  KEY `admin_id` (`admin_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Segment`
--

DROP TABLE IF EXISTS `Segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Segment` (
  `id` smallint(4) unsigned NOT NULL DEFAULT 0,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Segment_User`
--

DROP TABLE IF EXISTS `Segment_User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Segment_User` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `segment_id` smallint(4) unsigned DEFAULT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Sexy_Selfie`
--

DROP TABLE IF EXISTS `Sexy_Selfie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Sexy_Selfie` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `broadcasting_desc` text NOT NULL,
  `total_prize_money` float(8,2) DEFAULT NULL,
  `prize_breakdown` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `start` (`start`),
  KEY `end` (`end`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Sexy_Selfie_Winners`
--

DROP TABLE IF EXISTS `Sexy_Selfie_Winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Sexy_Selfie_Winners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) NOT NULL,
  `selfie_date` date NOT NULL,
  `rank` smallint(5) unsigned NOT NULL,
  `service` enum('girls','guys','trans') NOT NULL,
  `model_id` int(11) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contest_rank_service` (`contest_id`,`rank`,`service`),
  KEY `idx_selfie_date` (`selfie_date`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Show_Activity`
--

DROP TABLE IF EXISTS `Show_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_Activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `performer_login_type_id` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `studio_code` char(12) DEFAULT NULL,
  `location_id` mediumint(7) unsigned NOT NULL DEFAULT 0,
  `room_name` varchar(100) NOT NULL DEFAULT '',
  `date_started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_started_confirmed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_ended_confirmed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_ended` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `duration` mediumint(7) unsigned NOT NULL,
  `show_end_type` smallint(5) unsigned NOT NULL DEFAULT 0,
  `show_rate_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `show_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `show_type_join_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `customer_type_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `user_timeleft_started` mediumint(6) unsigned NOT NULL DEFAULT 0,
  `user_timeleft_deducted` mediumint(7) unsigned NOT NULL,
  `date_exported` date NOT NULL DEFAULT '0000-00-00',
  `server_public_name` varchar(255) NOT NULL DEFAULT '',
  `video_codec` tinyint(1) unsigned NOT NULL DEFAULT 3,
  `is_play_and_pay` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `domain` varchar(253) NOT NULL DEFAULT '',
  `geo_country` varchar(3) NOT NULL DEFAULT '',
  `needs_review` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `date_started` (`date_started`),
  KEY `model_id` (`model_id`),
  KEY `user_nickname` (`user_nickname`),
  KEY `date_ended` (`date_ended`),
  KEY `room_name` (`room_name`),
  KEY `user_id` (`user_id`),
  KEY `date_exported` (`date_exported`),
  KEY `show_type_id` (`show_type_id`),
  KEY `video_codec` (`video_codec`),
  KEY `show_end_type` (`show_end_type`),
  KEY `geo_country` (`geo_country`),
  KEY `sitekey` (`sitekey`,`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=73934501 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`10.%.%.%`*/ /*!50003 TRIGGER `BILLING`.`show_activity_insert` AFTER INSERT ON `Show_Activity` FOR EACH ROW
BEGIN


IF NEW.show_type_id = 93 THEN

CALL BILLING.performer_affiliate_handler( NEW.user_id, NEW.model_id, 'paid_show', NEW.id, NEW.user_timeleft_deducted, NEW.date_ended );

END IF;


END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`10.%.%.%`*/ /*!50003 TRIGGER `BILLING`.`show_activity_update` AFTER UPDATE ON `Show_Activity` FOR EACH ROW
BEGIN


IF NEW.date_ended != '0000-00-00 00:00:00' THEN

CALL BILLING.performer_affiliate_handler( NEW.user_id, NEW.model_id, 'paid_show', NEW.id, NEW.user_timeleft_deducted, NEW.date_ended );

END IF;


END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Show_Activity_Ex`
--

DROP TABLE IF EXISTS `Show_Activity_Ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Show_Activity_Ex` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `show_activity_id` int(11) unsigned DEFAULT NULL,
  `user_ip` varbinary(16) NOT NULL DEFAULT '',
  `initial_show_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_users` smallint(5) unsigned NOT NULL DEFAULT 0,
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_ip` (`user_ip`),
  KEY `initial_show_id` (`initial_show_id`),
  KEY `show_activity_id` (`show_activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64307578 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Source_Codes`
--

DROP TABLE IF EXISTS `Source_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Source_Codes` (
  `source_code` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`source_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Source_Site`
--

DROP TABLE IF EXISTS `Source_Site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Source_Site` (
  `id` smallint(4) unsigned NOT NULL,
  `site` varchar(255) DEFAULT NULL,
  UNIQUE KEY `site` (`site`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Spending_Group_Change_Log`
--

DROP TABLE IF EXISTS `Spending_Group_Change_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Spending_Group_Change_Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `old_spending_group` varchar(25) NOT NULL,
  `new_spending_group` varchar(25) NOT NULL,
  `change_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `datetime` (`date`),
  KEY `user_id` (`user_id`),
  KEY `old_spending_group` (`old_spending_group`),
  KEY `new_spending_group` (`new_spending_group`),
  KEY `change_type` (`change_type`)
) ENGINE=InnoDB AUTO_INCREMENT=14042318 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Spending_Group_Daily_Summary`
--

DROP TABLE IF EXISTS `Spending_Group_Daily_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Spending_Group_Daily_Summary` (
  `date` date NOT NULL,
  `spending_group` varchar(25) NOT NULL,
  `num_users` smallint(5) unsigned NOT NULL DEFAULT 0,
  `gross_sales` float(8,2) unsigned NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`date`,`spending_group`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Super_Voyeur_Activity`
--

DROP TABLE IF EXISTS `Super_Voyeur_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Super_Voyeur_Activity` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_nickname` varchar(32) NOT NULL DEFAULT '',
  `num_credits` smallint(5) unsigned NOT NULL DEFAULT 1,
  `date_exported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`model_id`,`date_created`,`user_id`,`date_exported`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Super_Voyeur_Transact`
--

DROP TABLE IF EXISTS `Super_Voyeur_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Super_Voyeur_Transact` (
  `voyeur_transact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_voyeur_transact_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `num_credits` smallint(5) NOT NULL DEFAULT 0,
  `type` enum('purchase','credit') NOT NULL DEFAULT 'purchase',
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `reason_code` smallint(3) unsigned DEFAULT NULL,
  `reason_text` text DEFAULT NULL,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime DEFAULT NULL,
  `date_exported` datetime DEFAULT NULL,
  PRIMARY KEY (`voyeur_transact_id`),
  KEY `date_transact` (`date_transact`),
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`),
  KEY `studio_code` (`studio_code`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Alert_Logs`
--

DROP TABLE IF EXISTS `System_Alert_Logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Alert_Logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `alert_id` int(11) unsigned NOT NULL,
  `admin_id` int(11) unsigned NOT NULL,
  `note` text NOT NULL,
  `status` enum('open','acknowledged','closed') NOT NULL DEFAULT 'open',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `alert_id` (`alert_id`),
  KEY `admin_id` (`admin_id`),
  KEY `status` (`status`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`)
) ENGINE=InnoDB AUTO_INCREMENT=924621 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Alerts`
--

DROP TABLE IF EXISTS `System_Alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Alerts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `metric_id` int(11) unsigned NOT NULL DEFAULT 0,
  `log_id` bigint(20) unsigned DEFAULT 0,
  `identifier` varchar(64) NOT NULL DEFAULT '',
  `alert_level` enum('normal','warning','critical') NOT NULL DEFAULT 'normal',
  `message` text DEFAULT NULL,
  `details` text NOT NULL,
  `snooze_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('open','closed','acknowledged') NOT NULL DEFAULT 'open',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pagerduty_id` varchar(64) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `metric_id` (`metric_id`),
  KEY `identifier` (`identifier`),
  KEY `alert_level` (`alert_level`),
  KEY `status` (`status`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`),
  KEY `log_id` (`log_id`),
  KEY `snooze_end` (`snooze_end`),
  KEY `pagerduty_id` (`pagerduty_id`)
) ENGINE=InnoDB AUTO_INCREMENT=543083 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Components`
--

DROP TABLE IF EXISTS `System_Components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Components` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `system_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(128) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `operation_control` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `operation_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `alert_level` enum('normal','warning','critical') NOT NULL DEFAULT 'normal',
  `last_ping` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `system_id` (`system_id`),
  KEY `status` (`status`),
  KEY `operation_control` (`operation_control`),
  KEY `operation_status` (`operation_status`),
  KEY `alert_level` (`alert_level`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Departments`
--

DROP TABLE IF EXISTS `System_Departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Departments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Log_Data`
--

DROP TABLE IF EXISTS `System_Log_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Log_Data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` bigint(20) unsigned DEFAULT 0,
  `metric_id` int(11) unsigned NOT NULL DEFAULT 0,
  `identifier` varchar(64) NOT NULL DEFAULT '',
  `data_label` varchar(64) NOT NULL,
  `data_value` decimal(20,2) DEFAULT 0.00,
  `data_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `log_id` (`log_id`),
  KEY `metric_id` (`metric_id`),
  KEY `identifier` (`identifier`),
  KEY `data_label` (`data_label`),
  KEY `data_date` (`data_date`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`)
) ENGINE=InnoDB AUTO_INCREMENT=265209119 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Logs`
--

DROP TABLE IF EXISTS `System_Logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `metric_id` int(11) unsigned NOT NULL DEFAULT 0,
  `identifier` varchar(64) NOT NULL DEFAULT '',
  `alert_level` enum('normal','warning','critical') NOT NULL DEFAULT 'normal',
  `message` text DEFAULT NULL,
  `details` text NOT NULL,
  `alert_id` int(11) unsigned NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `metric_id` (`metric_id`),
  KEY `identifier` (`identifier`),
  KEY `alert_level` (`alert_level`),
  KEY `alert_id` (`alert_id`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`)
) ENGINE=InnoDB AUTO_INCREMENT=140543834 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Metrics`
--

DROP TABLE IF EXISTS `System_Metrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Metrics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `component_id` int(11) unsigned NOT NULL DEFAULT 0,
  `title` varchar(128) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `alert_level` enum('normal','warning','critical') NOT NULL DEFAULT 'normal',
  `last_ping` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `component_id` (`component_id`),
  KEY `status` (`status`),
  KEY `alert_level` (`alert_level`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`)
) ENGINE=InnoDB AUTO_INCREMENT=402 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `System_Notifications`
--

DROP TABLE IF EXISTS `System_Notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `System_Notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(11) unsigned NOT NULL DEFAULT 1,
  `notification_group` smallint(5) unsigned NOT NULL DEFAULT 0,
  `api_key` varchar(128) NOT NULL DEFAULT '',
  `service` varchar(128) NOT NULL DEFAULT '',
  `alert_level` enum('warning','critical','test') NOT NULL DEFAULT 'warning',
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `service` (`service`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Systems`
--

DROP TABLE IF EXISTS `Systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Systems` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(11) unsigned NOT NULL DEFAULT 1,
  `title` varchar(128) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `operation_control` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `operation_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `alert_level` enum('normal','warning','critical') NOT NULL DEFAULT 'normal',
  `last_ping` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `operation_control` (`operation_control`),
  KEY `operation_status` (`operation_status`),
  KEY `alert_level` (`alert_level`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Task_Value`
--

DROP TABLE IF EXISTS `Task_Value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Task_Value` (
  `task_id` mediumint(8) unsigned DEFAULT NULL,
  `annual_value` float(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tax_by_Country`
--

DROP TABLE IF EXISTS `Tax_by_Country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tax_by_Country` (
  `country` char(2) NOT NULL,
  `rate` float(4,2) DEFAULT NULL,
  PRIMARY KEY (`country`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tax_by_Country_Schedule`
--

DROP TABLE IF EXISTS `Tax_by_Country_Schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tax_by_Country_Schedule` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `country` char(2) NOT NULL,
  `rate` float(4,2) DEFAULT NULL,
  `date_entered` datetime DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `applied` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `country_datestart` (`country`,`date_start`),
  KEY `date_start` (`date_start`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tech_Notes`
--

DROP TABLE IF EXISTS `Tech_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tech_Notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `note_type` varchar(20) NOT NULL DEFAULT 'tech',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `last_admin_update` (`admin_id`,`date_time`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Test_Accounts`
--

DROP TABLE IF EXISTS `Test_Accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Test_Accounts` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `bank` varchar(32) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_verified` date NOT NULL DEFAULT '0000-00-00',
  `last_verified_status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Test_Email`
--

DROP TABLE IF EXISTS `Test_Email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Test_Email` (
  `test_id` tinyint(2) unsigned DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `test_condition` varchar(3) DEFAULT NULL,
  KEY `test_id` (`test_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Test_Name`
--

DROP TABLE IF EXISTS `Test_Name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Test_Name` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Test_User`
--

DROP TABLE IF EXISTS `Test_User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Test_User` (
  `test_id` tinyint(2) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `test_condition` smallint(5) unsigned DEFAULT 0,
  `datetime` datetime DEFAULT NULL,
  KEY `test_id` (`test_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Top_Spender_Notes`
--

DROP TABLE IF EXISTS `Top_Spender_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Top_Spender_Notes` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `admin_id` (`admin_id`),
  KEY `date_time` (`date_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tracking`
--

DROP TABLE IF EXISTS `Tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tracking` (
  `tracking_category_id` int(11) NOT NULL,
  `pa_id` varchar(255) NOT NULL,
  `time` mediumint(6) unsigned NOT NULL,
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tracking_Category`
--

DROP TABLE IF EXISTS `Tracking_Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Tracking_Category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TransactIDTranslation`
--

DROP TABLE IF EXISTS `TransactIDTranslation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `TransactIDTranslation` (
  `old_transact_id` int(11) DEFAULT NULL,
  `new_transact_id` int(11) DEFAULT NULL,
  KEY `old_transact_id` (`old_transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Transact_Affiliate`
--

DROP TABLE IF EXISTS `Transact_Affiliate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Transact_Affiliate` (
  `transact_id` int(11) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `affiliate_id` mediumint(8) unsigned NOT NULL,
  `return_date` datetime NOT NULL,
  PRIMARY KEY (`transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Transact_Processor`
--

DROP TABLE IF EXISTS `Transact_Processor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Transact_Processor` (
  `transact_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned DEFAULT NULL,
  `processor_id` tinyint(3) NOT NULL DEFAULT 0,
  `processor_inv_id` varchar(255) NOT NULL DEFAULT '',
  `processor_member_id` varchar(255) DEFAULT NULL,
  `processor_txn_date` datetime DEFAULT NULL,
  KEY `transact_id` (`transact_id`),
  KEY `user_id` (`user_id`),
  KEY `processor_inv_id` (`processor_inv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Transaction_Attempts`
--

DROP TABLE IF EXISTS `Transaction_Attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Transaction_Attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type` enum('Recur','Play_And_Pay') DEFAULT NULL,
  `transaction_type_id` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `error_code` char(3) DEFAULT NULL,
  `account_type` varchar(20) DEFAULT NULL,
  `direct_payment_id` int(11) DEFAULT NULL,
  `merchant_bank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_type` (`transaction_type`,`transaction_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=259233 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Transaction_States`
--

DROP TABLE IF EXISTS `Transaction_States`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Transaction_States` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_ref` varchar(255) NOT NULL,
  `processor_id` tinyint(3) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `state` varchar(75) NOT NULL,
  `sitekey` varchar(20) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `ip_address` varbinary(16) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp(),
  `product_id` int(11) unsigned DEFAULT NULL,
  `currency` varchar(25) NOT NULL DEFAULT 'USD',
  `amount` decimal(12,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `fee` decimal(9,8) NOT NULL DEFAULT 0.00000000,
  `total_amount` decimal(12,8) NOT NULL DEFAULT 0.00000000,
  `received_amount` decimal(12,8) NOT NULL DEFAULT 0.00000000,
  `transact_id` int(11) unsigned DEFAULT NULL,
  `payload` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `internal_ref` (`internal_ref`),
  KEY `processor_id` (`processor_id`),
  KEY `user_id` (`user_id`),
  KEY `state` (`state`),
  KEY `sitekey` (`sitekey`),
  KEY `domain` (`domain`),
  KEY `date_created` (`date_created`),
  KEY `date_modified` (`date_modified`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=749275 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Transaction_Stops`
--

DROP TABLE IF EXISTS `Transaction_Stops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Transaction_Stops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `error_code` char(3) NOT NULL DEFAULT '',
  `error_message` varchar(255) NOT NULL DEFAULT '',
  `billing_ref_id` varchar(32) NOT NULL DEFAULT '',
  `cc_num` varchar(4) NOT NULL DEFAULT '',
  `bin` varchar(9) NOT NULL DEFAULT '',
  `user_payment_id` int(11) NOT NULL DEFAULT 0,
  `remote_ip` varbinary(16) NOT NULL DEFAULT '',
  `experience_ref_id` varchar(32) NOT NULL DEFAULT '',
  `experience_flow` varchar(64) NOT NULL DEFAULT '',
  `experience_trigger` varchar(64) NOT NULL DEFAULT '',
  `sitekey` varchar(20) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  `mp_code` varchar(12) NOT NULL DEFAULT '',
  `log_data` mediumtext DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `error_code` (`error_code`),
  KEY `billing_ref_id` (`billing_ref_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=5279712 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Transaction_Tests`
--

DROP TABLE IF EXISTS `Transaction_Tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Transaction_Tests` (
  `test_group` varchar(32) NOT NULL DEFAULT '',
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `transact_status` (`transact_id`,`status`),
  KEY `test_group` (`test_group`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Update_CS_Price`
--

DROP TABLE IF EXISTS `Update_CS_Price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Update_CS_Price` (
  `product_id` int(11) unsigned NOT NULL DEFAULT 0,
  `old_cs_price` float(8,2) NOT NULL DEFAULT 0.00,
  `new_cs_price` float(8,2) NOT NULL DEFAULT 0.00,
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(80) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `zip` varchar(15) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `subpw` varchar(32) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=290162 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User2Consultant_Activity`
--

DROP TABLE IF EXISTS `User2Consultant_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User2Consultant_Activity` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`model_id`),
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User2Model_Activity`
--

DROP TABLE IF EXISTS `User2Model_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User2Model_Activity` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_shows` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_credits` mediumint(8) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`model_id`),
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User2Model_Suggestion_Data`
--

DROP TABLE IF EXISTS `User2Model_Suggestion_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User2Model_Suggestion_Data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `num_points` smallint(6) NOT NULL DEFAULT 0,
  `date_last_updated` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_pair` (`user_id`,`model_id`),
  KEY `date_search` (`date_last_updated`)
) ENGINE=InnoDB AUTO_INCREMENT=562985 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Access_Log`
--

DROP TABLE IF EXISTS `User_Access_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Access_Log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `search_reason_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `section` varchar(255) NOT NULL DEFAULT 'general',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `user_id` (`user_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=12838908 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Achievement2Badges`
--

DROP TABLE IF EXISTS `User_Achievement2Badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Achievement2Badges` (
  `achievement` varchar(25) NOT NULL DEFAULT '',
  `badge` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`achievement`,`badge`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Achievements`
--

DROP TABLE IF EXISTS `User_Achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Achievements` (
  `achievement` varchar(25) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `priority` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `category` varchar(30) NOT NULL DEFAULT '',
  `image` varchar(30) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`achievement`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Achievements_Join`
--

DROP TABLE IF EXISTS `User_Achievements_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Achievements_Join` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `screen_name` varchar(32) NOT NULL DEFAULT '',
  `achievement` varchar(25) NOT NULL DEFAULT '',
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `display_status` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`user_id`,`screen_name`,`achievement`),
  KEY `date_earned` (`date_earned`),
  KEY `display_status` (`display_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Action_Blocks`
--

DROP TABLE IF EXISTS `User_Action_Blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Action_Blocks` (
  `id` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `action_block_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` enum('blocked','pending','inactive') NOT NULL DEFAULT 'blocked',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_block` (`user_id`,`action_block_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Allocation_Data`
--

DROP TABLE IF EXISTS `User_Allocation_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Allocation_Data` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `bank` varchar(20) NOT NULL DEFAULT '',
  `merchant_bank` varchar(20) NOT NULL DEFAULT '',
  `payment_type` varchar(10) NOT NULL DEFAULT '',
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `count` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`date`,`user_id`,`bank`,`merchant_bank`,`payment_type`,`processor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Badges`
--

DROP TABLE IF EXISTS `User_Badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Badges` (
  `badge` varchar(45) NOT NULL DEFAULT '',
  `name` varchar(35) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `priority` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `category` varchar(45) NOT NULL DEFAULT '',
  `image` varchar(45) NOT NULL DEFAULT '',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`badge`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Badges_Join`
--

DROP TABLE IF EXISTS `User_Badges_Join`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Badges_Join` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `screen_name` varchar(32) NOT NULL DEFAULT '',
  `badge` varchar(45) NOT NULL DEFAULT '',
  `date_earned` date NOT NULL DEFAULT '0000-00-00',
  `display_status` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`user_id`,`screen_name`,`badge`),
  KEY `date_earned` (`date_earned`),
  KEY `display_status` (`display_status`),
  KEY `badge` (`badge`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Bank_Allocation_Tally`
--

DROP TABLE IF EXISTS `User_Bank_Allocation_Tally`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Bank_Allocation_Tally` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `bank` varchar(20) NOT NULL DEFAULT '',
  `payment_type` varchar(10) NOT NULL DEFAULT '',
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `user_transactions` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_transactions` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`bank`,`payment_type`,`processor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Credit_Scoring`
--

DROP TABLE IF EXISTS `User_Credit_Scoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Credit_Scoring` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `score` double DEFAULT NULL,
  `model` varchar(128) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=105640 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Documents`
--

DROP TABLE IF EXISTS `User_Documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Documents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `notes` varchar(1024) NOT NULL DEFAULT '',
  `upload_admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `file_name` varchar(50) NOT NULL DEFAULT '',
  `file_size` int(10) unsigned NOT NULL DEFAULT 0,
  `file_mime_type` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2048 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_First_Shows`
--

DROP TABLE IF EXISTS `User_First_Shows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_First_Shows` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `show_activity_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`user_id`),
  KEY `date` (`date`),
  KEY `show_activity_id` (`show_activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_IP_Log`
--

DROP TABLE IF EXISTS `User_IP_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_IP_Log` (
  `user_id` int(10) unsigned NOT NULL,
  `ip_address_long` varbinary(16) NOT NULL DEFAULT '',
  `last_used` datetime DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `region` char(2) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `isp` varchar(50) DEFAULT NULL,
  `visit_count` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`user_id`,`ip_address_long`),
  KEY `ip_address_long` (`ip_address_long`),
  KEY `last_used_idx` (`last_used`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Merchant_Allocation_Tally`
--

DROP TABLE IF EXISTS `User_Merchant_Allocation_Tally`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Merchant_Allocation_Tally` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `merchant_bank` varchar(20) NOT NULL DEFAULT '',
  `payment_type` varchar(10) NOT NULL DEFAULT '',
  `processor_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `user_transactions` smallint(5) unsigned NOT NULL DEFAULT 0,
  `total_transactions` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`merchant_bank`,`payment_type`,`processor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Milestone`
--

DROP TABLE IF EXISTS `User_Milestone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Milestone` (
  `user_id` int(10) unsigned NOT NULL,
  `created` datetime DEFAULT NULL,
  `first_purchase` datetime DEFAULT NULL,
  `first_purchase_amount` float(6,2) DEFAULT NULL,
  `first_purchase_seconds` int(9) unsigned DEFAULT NULL,
  `first_money` datetime DEFAULT NULL,
  `first_money_amount` float(6,2) DEFAULT NULL,
  `first_money_seconds` int(9) unsigned DEFAULT NULL,
  `first_purchase_transact_id` int(11) unsigned DEFAULT NULL,
  `first_money_transact_id` int(11) unsigned DEFAULT NULL,
  `copied_from_user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `first_login` datetime DEFAULT NULL,
  `first_spend` datetime DEFAULT NULL,
  `first_spend_type` varchar(12) DEFAULT '',
  `first_spend_amount` int(10) unsigned DEFAULT 0,
  `first_spend_transact_id` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`user_id`),
  KEY `first_purchase` (`first_purchase`),
  KEY `first_money` (`first_money`),
  KEY `copied_from_user_id` (`copied_from_user_id`),
  KEY `first_spend` (`first_spend`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Mission_Model_Unique`
--

DROP TABLE IF EXISTS `User_Mission_Model_Unique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Mission_Model_Unique` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT 0,
  `model_id` int(10) unsigned DEFAULT 0,
  `base_mission_id` int(10) unsigned DEFAULT 0,
  `date_created` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_mission_model` (`user_id`,`base_mission_id`,`model_id`),
  KEY `datetime` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=23235840 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Missions`
--

DROP TABLE IF EXISTS `User_Missions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Missions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `frequency` varchar(45) NOT NULL DEFAULT '',
  `num_points` int(11) unsigned NOT NULL DEFAULT 0,
  `num_credits` int(11) unsigned NOT NULL DEFAULT 0,
  `badge` varchar(45) NOT NULL DEFAULT '',
  `model_specific` enum('Y','N') NOT NULL DEFAULT 'N',
  `required_mission_id` int(11) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `goal_quantity` int(11) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Missions_Achieved`
--

DROP TABLE IF EXISTS `User_Missions_Achieved`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Missions_Achieved` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mission_id` int(11) unsigned NOT NULL DEFAULT 0,
  `period_earned` date NOT NULL DEFAULT '0000-00-00',
  `date_achieved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` int(11) unsigned NOT NULL DEFAULT 0,
  `num_credits` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`mission_id`,`period_earned`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Missions_Data`
--

DROP TABLE IF EXISTS `User_Missions_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Missions_Data` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `mission_id` int(11) unsigned NOT NULL DEFAULT 0,
  `quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `achieved` enum('Y','N') NOT NULL DEFAULT 'N',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `percentage` float(4,3) unsigned NOT NULL DEFAULT 0.000,
  PRIMARY KEY (`user_id`,`mission_id`),
  KEY `achieved` (`achieved`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Model_Activity`
--

DROP TABLE IF EXISTS `User_Model_Activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Model_Activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(10) unsigned NOT NULL DEFAULT 0,
  `credits_gifts` int(9) unsigned NOT NULL DEFAULT 0,
  `credits_shows` int(9) unsigned NOT NULL DEFAULT 0,
  `count_gifts` smallint(4) unsigned NOT NULL DEFAULT 0,
  `count_shows` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_constraint` (`date_created`,`user_id`,`model_id`),
  KEY `user_id` (`user_id`,`date_created`,`model_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=32471634 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Note_Tags`
--

DROP TABLE IF EXISTS `User_Note_Tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Note_Tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `note_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `note_id` (`note_id`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=6966 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Notes`
--

DROP TABLE IF EXISTS `User_Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `email` varchar(255) DEFAULT '',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comments` text NOT NULL,
  `temp_chargeback_id` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `admin_id` (`admin_id`),
  KEY `email` (`email`),
  KEY `temp_chargeback_id` (`temp_chargeback_id`),
  KEY `datetime` (`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=77267913 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Payment`
--

DROP TABLE IF EXISTS `User_Payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `processor_id` tinyint(3) NOT NULL DEFAULT 0,
  `processor_inv_id` varchar(255) DEFAULT NULL,
  `payment_id` varchar(150) NOT NULL DEFAULT '',
  `affiliate_payment_id` varchar(150) NOT NULL DEFAULT '',
  `pin` varchar(20) DEFAULT NULL,
  `bin` varchar(9) DEFAULT NULL,
  `last_digits` varchar(11) DEFAULT NULL,
  `card_expire` varchar(6) DEFAULT NULL,
  `card_expire_last_used` varchar(6) DEFAULT NULL,
  `payment_type_id` int(1) NOT NULL DEFAULT 1,
  `external_account_id` int(10) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_purchased` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) DEFAULT 1,
  `account_type` varchar(20) NOT NULL,
  `payment_type` varchar(32) NOT NULL DEFAULT '',
  `distribution_group_id` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `card_category` varchar(24) DEFAULT NULL,
  `type_of_card` varchar(24) DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL,
  `card_brand` varchar(24) DEFAULT NULL,
  `issuing_bank` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `last_digits` (`last_digits`),
  KEY `affiliate_payment_id` (`affiliate_payment_id`),
  KEY `payment_id` (`payment_id`),
  KEY `payment_type` (`payment_type`),
  KEY `date_created` (`date_created`),
  KEY `processor_id` (`processor_id`),
  KEY `card_category` (`card_category`),
  KEY `country_code` (`country_code`),
  KEY `card_brand` (`card_brand`),
  KEY `bin` (`bin`),
  KEY `date_last_purchased` (`date_last_purchased`)
) ENGINE=InnoDB AUTO_INCREMENT=12953939 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Payment_Copy`
--

DROP TABLE IF EXISTS `User_Payment_Copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Payment_Copy` (
  `old_user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `new_user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `old_user_payment_id` int(11) unsigned NOT NULL,
  `new_user_payment_id` int(11) unsigned NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `old_user_id` (`old_user_id`),
  KEY `new_user_id` (`new_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Payment_Preferences`
--

DROP TABLE IF EXISTS `User_Payment_Preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Payment_Preferences` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `payment_type` varchar(20) NOT NULL DEFAULT 'default',
  `bin` varchar(9) NOT NULL DEFAULT '',
  `last_digits` varchar(11) NOT NULL DEFAULT '',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `user_id` (`user_id`),
  KEY `payment_type` (`payment_type`),
  KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Payment_to_Payment_Type`
--

DROP TABLE IF EXISTS `User_Payment_to_Payment_Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Payment_to_Payment_Type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `account_type` varchar(255) NOT NULL DEFAULT '',
  `payment_type` varchar(255) NOT NULL DEFAULT '',
  `payment_type_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `icon_override` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_payment_to_type` (`account_type`,`payment_type`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Point_Activities`
--

DROP TABLE IF EXISTS `User_Point_Activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Point_Activities` (
  `activity` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `num_points` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `max_points_daily` mediumint(8) unsigned DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `model_specific` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`activity`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Point_BRAD`
--

DROP TABLE IF EXISTS `User_Point_BRAD`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Point_BRAD` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `lifetime_points` int(11) unsigned NOT NULL DEFAULT 0,
  `recent_points` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Point_Date_Summary`
--

DROP TABLE IF EXISTS `User_Point_Date_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Point_Date_Summary` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `total_points` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=25220962 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Point_Event_Log`
--

DROP TABLE IF EXISTS `User_Point_Event_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Point_Event_Log` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `action` enum('level_up','level_down','status_up') NOT NULL DEFAULT 'level_up',
  `prev_ref_id` smallint(3) unsigned NOT NULL DEFAULT 1,
  `new_ref_id` smallint(3) unsigned NOT NULL DEFAULT 1,
  KEY `user_id` (`user_id`),
  KEY `date_search` (`date`,`action`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Point_Levels`
--

DROP TABLE IF EXISTS `User_Point_Levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Point_Levels` (
  `id` smallint(3) unsigned NOT NULL DEFAULT 1,
  `title` varchar(25) NOT NULL DEFAULT '',
  `min_points` mediumint(8) NOT NULL DEFAULT 0,
  `max_points` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Point_Status`
--

DROP TABLE IF EXISTS `User_Point_Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Point_Status` (
  `id` smallint(2) unsigned NOT NULL DEFAULT 1,
  `title` varchar(25) NOT NULL DEFAULT '',
  `min_points` mediumint(8) NOT NULL DEFAULT 0,
  `max_points` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Point_Summary`
--

DROP TABLE IF EXISTS `User_Point_Summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Point_Summary` (
  `user_id` int(11) unsigned NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  `total_points` int(11) NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Points_Goodwill`
--

DROP TABLE IF EXISTS `User_Points_Goodwill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Points_Goodwill` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `num_points` smallint(5) unsigned NOT NULL DEFAULT 0,
  `activity` varchar(50) NOT NULL DEFAULT '',
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reason` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity` (`activity`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB AUTO_INCREMENT=2261 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Processor`
--

DROP TABLE IF EXISTS `User_Processor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Processor` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `processor_id` tinyint(3) NOT NULL DEFAULT 0,
  `processor_user_id` varchar(255) NOT NULL DEFAULT '',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_last_purchased` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(1) NOT NULL DEFAULT 1,
  `sub_type` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `processor_id` (`processor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8982095 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Referral`
--

DROP TABLE IF EXISTS `User_Referral`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Referral` (
  `ref_screen_name` varchar(32) NOT NULL DEFAULT '',
  `new_username` varchar(32) NOT NULL DEFAULT '',
  `processed` char(1) NOT NULL DEFAULT 'N',
  KEY `new_username` (`new_username`),
  KEY `processed` (`processed`),
  KEY `top_referrals` (`ref_screen_name`,`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Spending`
--

DROP TABLE IF EXISTS `User_Spending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Spending` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `returned` char(1) DEFAULT NULL,
  `amount` float(9,2) DEFAULT NULL,
  `30_day_num` tinyint(3) unsigned DEFAULT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Spending_Velocity`
--

DROP TABLE IF EXISTS `User_Spending_Velocity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Spending_Velocity` (
  `user_id` int(10) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` float(9,2) DEFAULT NULL,
  `velocity` float(6,4) DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Watchlist`
--

DROP TABLE IF EXISTS `User_Watchlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_Watchlist` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `date_expire` date NOT NULL DEFAULT '0000-00-00',
  `admin_id_created` smallint(5) unsigned NOT NULL DEFAULT 0,
  `comments` text DEFAULT NULL,
  `status` enum('high','medium','low') NOT NULL DEFAULT 'high',
  `review_date` date DEFAULT '0000-00-00',
  `review_required` tinyint(1) unsigned DEFAULT 0,
  KEY `user_id` (`user_id`),
  KEY `admin_id_created` (`admin_id_created`),
  KEY `date_expire` (`date_expire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_PPM`
--

DROP TABLE IF EXISTS `VOD_PPM`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_PPM` (
  `recording_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `play_type` enum('primary','secondary') NOT NULL DEFAULT 'primary',
  `num_credits` smallint(5) unsigned NOT NULL DEFAULT 1,
  `date_exported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`recording_id`,`date_created`,`user_id`,`play_type`,`date_exported`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Package_Transact`
--

DROP TABLE IF EXISTS `VOD_Package_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Package_Transact` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vod_package_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `ref_package_transact_id` smallint(5) unsigned DEFAULT NULL,
  `vod_package_price_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) NOT NULL DEFAULT '',
  `paid_credits` smallint(5) NOT NULL DEFAULT 0,
  `type` enum('purchase','credit','bonus') NOT NULL DEFAULT 'purchase',
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `reason_code` smallint(3) unsigned DEFAULT NULL,
  `reason_text` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `vod_package_id` (`vod_package_id`),
  KEY `vod_package_price_id` (`vod_package_price_id`),
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`),
  KEY `date_created` (`date_created`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=2808 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Promo`
--

DROP TABLE IF EXISTS `VOD_Promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Promo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('bonus','discount','bogo') DEFAULT 'bonus',
  `name` varchar(255) DEFAULT '',
  `promo_start` datetime DEFAULT '0000-00-00 00:00:00',
  `promo_end` datetime DEFAULT '0000-00-00 00:00:00',
  `min_price` smallint(5) unsigned DEFAULT 1,
  `max_price` smallint(5) unsigned DEFAULT 10000,
  `bogo_name` varchar(255) DEFAULT '',
  `bogo_expire_days` tinyint(3) unsigned DEFAULT 0,
  `bogo_duration_days` tinyint(3) unsigned DEFAULT 0,
  `bogo_to_award` tinyint(3) unsigned DEFAULT 0,
  `bogo_end` datetime DEFAULT '0000-00-00 00:00:00',
  `bogo_image` varchar(255) DEFAULT '',
  `bonus_reward_multiplier` tinyint(3) unsigned DEFAULT 0,
  `discount_percent` tinyint(3) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `promo_start` (`promo_start`),
  KEY `promo_end` (`promo_end`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Refund_Reasons`
--

DROP TABLE IF EXISTS `VOD_Refund_Reasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Refund_Reasons` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL DEFAULT '',
  `is_deductable` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `display` char(1) NOT NULL DEFAULT 'Y',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `rank` smallint(4) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Rewards_Access`
--

DROP TABLE IF EXISTS `VOD_Rewards_Access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Rewards_Access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `date_redeemed` datetime NOT NULL DEFAULT current_timestamp(),
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`date_end`)
) ENGINE=InnoDB AUTO_INCREMENT=9200 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `VOD_Transact`
--

DROP TABLE IF EXISTS `VOD_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Transact` (
  `vod_transact_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ref_vod_transact_id` int(11) unsigned DEFAULT NULL,
  `recording_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `studio_code` char(12) DEFAULT NULL,
  `paid_tokens` smallint(5) NOT NULL DEFAULT 0,
  `free_tokens` smallint(5) NOT NULL DEFAULT 0,
  `type` enum('purchase','credit','bonus') NOT NULL DEFAULT 'purchase',
  `purchase_type` varchar(20) NOT NULL DEFAULT 'access',
  `product_id` int(11) NOT NULL DEFAULT 0,
  `admin_id` smallint(5) unsigned DEFAULT NULL,
  `reason_code` smallint(3) unsigned DEFAULT NULL,
  `reason_text` text DEFAULT NULL,
  `payable` tinyint(1) NOT NULL DEFAULT 1,
  `date_transact` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created` datetime DEFAULT NULL,
  `date_exported` datetime DEFAULT NULL,
  `sitekey` varchar(16) NOT NULL DEFAULT '',
  `domain` varchar(253) NOT NULL DEFAULT '',
  `geo_country` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`vod_transact_id`),
  KEY `date_transact` (`date_transact`),
  KEY `user_id` (`user_id`),
  KEY `model_id` (`model_id`),
  KEY `studio_code` (`studio_code`),
  KEY `recording_id` (`recording_id`),
  KEY `type` (`type`),
  KEY `geo_country` (`geo_country`),
  KEY `sitekey` (`sitekey`,`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=17396988 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
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
/*!50003 CREATE*/ /*!50017 DEFINER=`triggers`@`10.%.%.%`*/ /*!50003 TRIGGER `BILLING`.`vod_transact_insert` AFTER INSERT ON `VOD_Transact` FOR EACH ROW
BEGIN
   

IF NEW.type = 'purchase' AND NEW.paid_tokens > 0 THEN 

CALL BILLING.performer_affiliate_handler( NEW.user_id, NEW.model_id, 'vod_view', NEW.vod_transact_id, NEW.paid_tokens, NEW.date_transact );

END IF;
  



IF NEW.type = 'purchase' AND NEW.paid_tokens > 0 THEN 

     UPDATE VOD.Recordings 
     SET total_credits = total_credits + NEW.paid_tokens 
     WHERE id = NEW.recording_id;

END IF;


END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `VOD_Transact_Ex`
--

DROP TABLE IF EXISTS `VOD_Transact_Ex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `VOD_Transact_Ex` (
  `vod_transact_id` int(11) unsigned NOT NULL DEFAULT 0,
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `user_platform` varchar(32) NOT NULL DEFAULT '',
  `user_platform_device` varchar(32) NOT NULL DEFAULT '',
  `user_platform_type` varchar(32) NOT NULL DEFAULT '',
  `supports_hls` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_flash` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `supports_websockets` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `referring_url` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`vod_transact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Verifi_Bin_List`
--

DROP TABLE IF EXISTS `Verifi_Bin_List`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Verifi_Bin_List` (
  `bin` int(6) NOT NULL DEFAULT 0,
  `bank` varchar(55) NOT NULL DEFAULT '',
  `fraud_coverage` tinyint(1) NOT NULL DEFAULT 0,
  `nonfraud_coverage` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`bin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Versus_Tip_Transact`
--

DROP TABLE IF EXISTS `Versus_Tip_Transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Versus_Tip_Transact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `versus_id` mediumint(8) unsigned NOT NULL,
  `gift_transact_id` int(10) unsigned NOT NULL,
  `datetime_transact` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `XVC_Credit_Tracking`
--

DROP TABLE IF EXISTS `XVC_Credit_Tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `XVC_Credit_Tracking` (
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `date` varchar(7) NOT NULL DEFAULT '',
  `credit_balance_start` int(11) NOT NULL DEFAULT 0,
  `credits_used` int(11) NOT NULL DEFAULT 0,
  `credit_balance_end` int(11) NOT NULL DEFAULT 0,
  UNIQUE KEY `user_id_by_date` (`user_id`,`date`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Zones`
--

DROP TABLE IF EXISTS `Zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Zones` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `zone_id` smallint(5) unsigned NOT NULL DEFAULT 0,
  `description` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `zone_id` (`zone_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cs_feedback`
--

DROP TABLE IF EXISTS `cs_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cs_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_contacted` datetime NOT NULL DEFAULT current_timestamp(),
  `admin_id` smallint(5) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `contact_channel` varchar(32) NOT NULL,
  `issue_type` varchar(32) NOT NULL,
  `area` varchar(32) NOT NULL,
  `status` enum('Open','Resolved') DEFAULT NULL,
  `date_resolved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cs_upvotes` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `date_contacted` (`date_contacted`),
  KEY `status` (`status`),
  KEY `contact_channel` (`contact_channel`),
  KEY `issue_type` (`issue_type`),
  KEY `area` (`area`),
  KEY `date_resolved` (`date_resolved`)
) ENGINE=InnoDB AUTO_INCREMENT=582 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dhd_list`
--

DROP TABLE IF EXISTS `dhd_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dhd_list` (
  `dhd_inv_id` varchar(255) DEFAULT NULL,
  KEY `dhd_inv_id` (`dhd_inv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dhd_list_internal`
--

DROP TABLE IF EXISTS `dhd_list_internal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dhd_list_internal` (
  `processor_inv_id` varchar(255) NOT NULL DEFAULT '',
  KEY `processor_inv_id` (`processor_inv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iovation_transact`
--

DROP TABLE IF EXISTS `iovation_transact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `iovation_transact` (
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `blackbox_sent` char(1) DEFAULT NULL,
  `account_code` varchar(255) DEFAULT NULL,
  `rule_type` varchar(35) DEFAULT NULL,
  `remote_ip` varchar(20) DEFAULT NULL,
  `result` char(1) DEFAULT NULL,
  `reason` varchar(160) DEFAULT NULL,
  `tracking_number` varchar(40) DEFAULT NULL,
  `device_alias` varchar(40) DEFAULT NULL,
  `device_first_seen` datetime DEFAULT NULL,
  `device_new` char(1) DEFAULT NULL,
  `device_screen` varchar(20) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `device_os` varchar(50) DEFAULT NULL,
  `device_timezone` smallint(4) DEFAULT NULL,
  `device_js_enabled` char(1) DEFAULT NULL,
  `device_flash_enabled` char(1) DEFAULT NULL,
  `device_flash_installed` char(1) DEFAULT NULL,
  `device_flash_version` varchar(20) DEFAULT NULL,
  `device_flash_storage_enabled` char(1) DEFAULT NULL,
  `device_cookie_enabled` char(1) DEFAULT NULL,
  `device_browser_type` varchar(50) DEFAULT NULL,
  `device_browser_version` varchar(20) DEFAULT NULL,
  `device_browser_charset` varchar(50) DEFAULT NULL,
  `device_browser_configured_lang` varchar(50) DEFAULT NULL,
  `device_browser_lang` varchar(50) DEFAULT NULL,
  `real_ip_address` char(20) DEFAULT NULL,
  `rules_matched` smallint(4) unsigned DEFAULT NULL,
  `rules_score` smallint(4) DEFAULT NULL,
  `evidence` text DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `result_override` char(1) DEFAULT NULL,
  `rules_reasons` text DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  KEY `datetime` (`datetime`),
  KEY `account_code` (`account_code`),
  KEY `remote_ip` (`remote_ip`),
  KEY `real_ip_address` (`real_ip_address`),
  KEY `device_alias` (`device_alias`),
  KEY `result_override` (`result_override`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_pay_period`
--

DROP TABLE IF EXISTS `models_pay_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_pay_period` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `sales` decimal(32,4) DEFAULT NULL,
  `rate` decimal(4,3) NOT NULL DEFAULT 0.000
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `models_solo_pay_period_1261`
--

DROP TABLE IF EXISTS `models_solo_pay_period_1261`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_solo_pay_period_1261` (
  `model_id` int(11) unsigned NOT NULL DEFAULT 0,
  `sales` decimal(32,4) DEFAULT NULL,
  `rate` decimal(4,3) NOT NULL DEFAULT 0.000
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mp_code_log`
--

DROP TABLE IF EXISTS `mp_code_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mp_code_log` (
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(10) unsigned DEFAULT NULL,
  `old_mp_code` char(5) DEFAULT NULL,
  `new_mp_code` char(5) DEFAULT NULL,
  KEY `new_mp_code` (`new_mp_code`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pivotal_affiliate_traffic`
--

DROP TABLE IF EXISTS `pivotal_affiliate_traffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pivotal_affiliate_traffic` (
  `affiliate_id` mediumint(8) unsigned DEFAULT NULL,
  `traffic_date` date DEFAULT NULL,
  `unique_hits` int(9) unsigned DEFAULT NULL,
  `user_joins` mediumint(8) unsigned DEFAULT NULL,
  KEY `affiliate_id` (`affiliate_id`,`traffic_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pivotal_affiliates`
--

DROP TABLE IF EXISTS `pivotal_affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pivotal_affiliates` (
  `affiliate_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `house_account` char(1) DEFAULT NULL,
  `affected_users` smallint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`affiliate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rdx_checksum_log`
--

DROP TABLE IF EXISTS `rdx_checksum_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rdx_checksum_log` (
  `TABLE_NAME` varchar(64) NOT NULL DEFAULT '',
  `TABLE_HOST_A` varchar(512) NOT NULL DEFAULT '',
  KEY `TABLE_NAME` (`TABLE_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_country`
--

DROP TABLE IF EXISTS `temp_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `temp_country` (
  `country` varchar(75) DEFAULT NULL
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

-- Dump completed on 2025-10-24 17:49:17
