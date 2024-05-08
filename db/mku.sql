-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.1.41 - Source distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for default_db
CREATE DATABASE IF NOT EXISTS `default_db` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `default_db`;


-- Dumping structure for table default_db.access_type
CREATE TABLE IF NOT EXISTS `access_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.access_type: 3 rows
/*!40000 ALTER TABLE `access_type` DISABLE KEYS */;
INSERT INTO `access_type` (`id`, `name`, `description`) VALUES
	(1, 'Root', NULL),
	(2, 'Administrator', 'Manage user accounts and group privileges.'),
	(44, 'Admin', 'Type 2 Admin, limited');
/*!40000 ALTER TABLE `access_type` ENABLE KEYS */;


-- Dumping structure for table default_db.account
CREATE TABLE IF NOT EXISTS `account` (
  `account_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `account_code` varchar(20) NOT NULL,
  `account` varchar(50) NOT NULL,
  `account_type_id` bigint(6) NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.account: 1 rows
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` (`account_id`, `account_code`, `account`, `account_type_id`) VALUES
	(1, '0001', 'Cash', 1);
/*!40000 ALTER TABLE `account` ENABLE KEYS */;


-- Dumping structure for table default_db.accountables
CREATE TABLE IF NOT EXISTS `accountables` (
  `accountable_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `rr_detail_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `account_id` bigint(12) NOT NULL,
  `received` char(1) NOT NULL DEFAULT '1',
  `systemtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `accountables_void` char(1) NOT NULL DEFAULT '0',
  `qty` decimal(12,2) NOT NULL,
  `item_status` varchar(100) NOT NULL,
  `remarks` text,
  PRIMARY KEY (`accountable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.accountables: ~0 rows (approximately)
/*!40000 ALTER TABLE `accountables` DISABLE KEYS */;
/*!40000 ALTER TABLE `accountables` ENABLE KEYS */;


-- Dumping structure for table default_db.accounts_payable
CREATE TABLE IF NOT EXISTS `accounts_payable` (
  `ap_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `header` varchar(100) NOT NULL,
  `header_id` bigint(12) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `due_date` date NOT NULL,
  `supplier_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `reference` varchar(20) DEFAULT NULL,
  `tbl` varchar(50) DEFAULT 'rr_header',
  `type` char(1) NOT NULL,
  PRIMARY KEY (`ap_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.accounts_payable: 0 rows
/*!40000 ALTER TABLE `accounts_payable` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts_payable` ENABLE KEYS */;


-- Dumping structure for table default_db.accounts_receivable
CREATE TABLE IF NOT EXISTS `accounts_receivable` (
  `ar_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `header` varchar(100) NOT NULL,
  `header_id` bigint(12) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `account` varchar(50) NOT NULL,
  `account_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `date` date NOT NULL,
  PRIMARY KEY (`ar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.accounts_receivable: 0 rows
/*!40000 ALTER TABLE `accounts_receivable` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts_receivable` ENABLE KEYS */;


-- Dumping structure for table default_db.account_type
CREATE TABLE IF NOT EXISTS `account_type` (
  `account_type_id` bigint(6) NOT NULL AUTO_INCREMENT,
  `account_type` varchar(30) NOT NULL,
  PRIMARY KEY (`account_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.account_type: 2 rows
/*!40000 ALTER TABLE `account_type` DISABLE KEYS */;
INSERT INTO `account_type` (`account_type_id`, `account_type`) VALUES
	(2, 'employee'),
	(3, 'subcon');
/*!40000 ALTER TABLE `account_type` ENABLE KEYS */;


-- Dumping structure for table default_db.admin_access
CREATE TABLE IF NOT EXISTS `admin_access` (
  `userID` varchar(200) NOT NULL,
  `user_lname` varchar(200) NOT NULL,
  `user_fname` varchar(200) NOT NULL,
  `user_mname` varchar(200) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '0cc175b9c0f1b6a831c399e269772661',
  `access` int(10) NOT NULL,
  `active` char(1) DEFAULT '1',
  `membered_since` datetime NOT NULL,
  `companyID` int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.admin_access: 3 rows
/*!40000 ALTER TABLE `admin_access` DISABLE KEYS */;
INSERT INTO `admin_access` (`userID`, `user_lname`, `user_fname`, `user_mname`, `email`, `username`, `password`, `access`, `active`, `membered_since`, `companyID`) VALUES
	('20080228-111008', 'Catague', 'Michael Francis', 'Chin', NULL, 'root', '942452d3206faf769b654cf2236d15f7', 1, '1', '0000-00-00 00:00:00', 1),
	('20160719-110150', 'Sobrino', 'Martin Louie', 'Mandid', NULL, 'mls', '0cc175b9c0f1b6a831c399e269772661', 1, '1', '2016-07-19 11:01:50', 0),
	('20170813-105326', 'Dela Cruz', 'Juan', 'P.', NULL, 'juan', '0cc175b9c0f1b6a831c399e269772661', 44, '1', '2017-08-13 10:53:26', 1);
/*!40000 ALTER TABLE `admin_access` ENABLE KEYS */;


-- Dumping structure for table default_db.application
CREATE TABLE IF NOT EXISTS `application` (
  `application_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(12) NOT NULL,
  `reservation_no` varchar(50) NOT NULL,
  `reservation_amount` decimal(12,2) NOT NULL,
  `subd_id` bigint(12) NOT NULL,
  `model_id` bigint(12) NOT NULL,
  `phase` varchar(50) NOT NULL,
  `block` varchar(50) NOT NULL,
  `lot` varchar(20) NOT NULL,
  `lot_area` decimal(12,4) NOT NULL,
  `floor_area` decimal(12,4) NOT NULL,
  `payment_code` int(10) NOT NULL,
  `dp_code` varchar(50) NOT NULL,
  `loan_value` decimal(12,2) NOT NULL,
  `dp_percent` double(15,15) NOT NULL,
  `with_disc` varchar(10) NOT NULL,
  `loan_term` int(4) NOT NULL,
  `dp_disc_fixed` varchar(50) NOT NULL,
  `disc_rate` double(15,15) NOT NULL,
  `interest_rate` decimal(12,2) NOT NULL,
  `dp_amount` double(12,2) NOT NULL,
  `dp_period` varchar(10) NOT NULL,
  `outstanding_balance` decimal(12,2) NOT NULL,
  `dp_balance` decimal(12,2) NOT NULL,
  `application_date` date NOT NULL,
  `net_loan` decimal(12,2) NOT NULL,
  `amortization` decimal(12,2) NOT NULL,
  `date_due` int(5) NOT NULL,
  `penalized` tinyint(1) NOT NULL,
  `penalty_per_day` double(15,15) NOT NULL,
  `grace_period` int(4) NOT NULL,
  `date_approved` date NOT NULL,
  `date_cancelled` date NOT NULL,
  `application_void` char(1) NOT NULL DEFAULT '0',
  `package_type_id` int(10) NOT NULL,
  `datebeg` date NOT NULL,
  `datecut` date NOT NULL,
  PRIMARY KEY (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.application: ~0 rows (approximately)
/*!40000 ALTER TABLE `application` DISABLE KEYS */;
/*!40000 ALTER TABLE `application` ENABLE KEYS */;


-- Dumping structure for table default_db.apv_detail
CREATE TABLE IF NOT EXISTS `apv_detail` (
  `apv_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `apv_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `rr_id` bigint(12) DEFAULT NULL,
  PRIMARY KEY (`apv_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.apv_detail: 0 rows
/*!40000 ALTER TABLE `apv_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `apv_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.apv_header
CREATE TABLE IF NOT EXISTS `apv_header` (
  `apv_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `po_header_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `po_date` date NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `supplier_id` bigint(12) NOT NULL,
  `terms` int(4) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(30) NOT NULL,
  `tax_gchart_id` bigint(12) DEFAULT NULL,
  `vat_gchart_id` bigint(12) DEFAULT NULL,
  `vatable` char(1) NOT NULL DEFAULT '0',
  `w_tax` int(2) NOT NULL DEFAULT '0',
  `discount_amount` decimal(12,2) DEFAULT NULL,
  `remarks` text,
  PRIMARY KEY (`apv_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.apv_header: 0 rows
/*!40000 ALTER TABLE `apv_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `apv_header` ENABLE KEYS */;


-- Dumping structure for table default_db.apv_mrr_detail
CREATE TABLE IF NOT EXISTS `apv_mrr_detail` (
  `apv_mrr_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `apv_detail_id` bigint(12) NOT NULL,
  `rr_header_id` bigint(12) NOT NULL,
  PRIMARY KEY (`apv_mrr_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.apv_mrr_detail: ~0 rows (approximately)
/*!40000 ALTER TABLE `apv_mrr_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `apv_mrr_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.ap_detail
CREATE TABLE IF NOT EXISTS `ap_detail` (
  `ap_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `ap_header_id` bigint(12) NOT NULL,
  `ap_id` bigint(12) NOT NULL,
  PRIMARY KEY (`ap_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ap_detail: 0 rows
/*!40000 ALTER TABLE `ap_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `ap_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.ap_header
CREATE TABLE IF NOT EXISTS `ap_header` (
  `ap_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `status` char(1) NOT NULL DEFAULT 'S',
  `supplier_id` bigint(12) NOT NULL,
  PRIMARY KEY (`ap_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ap_header: 0 rows
/*!40000 ALTER TABLE `ap_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `ap_header` ENABLE KEYS */;


-- Dumping structure for table default_db.ap_payment
CREATE TABLE IF NOT EXISTS `ap_payment` (
  `ap_payment_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `bank` varchar(100) NOT NULL,
  `checkno` varchar(50) NOT NULL,
  `checkdate` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` char(2) NOT NULL,
  `date` date NOT NULL,
  `supplier_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `apv_header_id` bigint(12) NOT NULL,
  PRIMARY KEY (`ap_payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ap_payment: 0 rows
/*!40000 ALTER TABLE `ap_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ap_payment` ENABLE KEYS */;


-- Dumping structure for table default_db.ap_payment_detail
CREATE TABLE IF NOT EXISTS `ap_payment_detail` (
  `ap_payment_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `ap_payment_header_id` bigint(12) NOT NULL,
  `ap_id` bigint(12) NOT NULL,
  PRIMARY KEY (`ap_payment_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ap_payment_detail: 0 rows
/*!40000 ALTER TABLE `ap_payment_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `ap_payment_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.ap_payment_header
CREATE TABLE IF NOT EXISTS `ap_payment_header` (
  `ap_payment_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `bank` varchar(100) NOT NULL,
  `checkno` varchar(50) NOT NULL,
  `checkdate` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` char(2) NOT NULL,
  `date` date NOT NULL,
  `supplier_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  PRIMARY KEY (`ap_payment_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ap_payment_header: 0 rows
/*!40000 ALTER TABLE `ap_payment_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `ap_payment_header` ENABLE KEYS */;


-- Dumping structure for table default_db.aradjust_details
CREATE TABLE IF NOT EXISTS `aradjust_details` (
  `aradjust_detail_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `aradjust_header_id` bigint(12) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  PRIMARY KEY (`aradjust_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.aradjust_details: 0 rows
/*!40000 ALTER TABLE `aradjust_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `aradjust_details` ENABLE KEYS */;


-- Dumping structure for table default_db.aradjust_header
CREATE TABLE IF NOT EXISTS `aradjust_header` (
  `aradjust_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `remarks` blob NOT NULL,
  `audit` blob NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`aradjust_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.aradjust_header: 0 rows
/*!40000 ALTER TABLE `aradjust_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `aradjust_header` ENABLE KEYS */;


-- Dumping structure for table default_db.ar_detail
CREATE TABLE IF NOT EXISTS `ar_detail` (
  `ar_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `ar_header_id` bigint(12) NOT NULL,
  `ar_id` bigint(12) NOT NULL,
  PRIMARY KEY (`ar_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ar_detail: 0 rows
/*!40000 ALTER TABLE `ar_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `ar_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.ar_header
CREATE TABLE IF NOT EXISTS `ar_header` (
  `ar_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `status` char(1) NOT NULL DEFAULT 'S',
  `account` varchar(30) NOT NULL,
  `account_id` bigint(12) NOT NULL,
  PRIMARY KEY (`ar_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ar_header: 0 rows
/*!40000 ALTER TABLE `ar_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `ar_header` ENABLE KEYS */;


-- Dumping structure for table default_db.ar_payment
CREATE TABLE IF NOT EXISTS `ar_payment` (
  `ar_payment_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `bank` varchar(100) NOT NULL,
  `checkno` varchar(50) NOT NULL,
  `checkdate` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` char(2) NOT NULL,
  `date` date NOT NULL,
  `account` varchar(30) NOT NULL,
  `account_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `ar_header_id` bigint(12) NOT NULL,
  PRIMARY KEY (`ar_payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ar_payment: 0 rows
/*!40000 ALTER TABLE `ar_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ar_payment` ENABLE KEYS */;


-- Dumping structure for table default_db.asset_circulation_detail
CREATE TABLE IF NOT EXISTS `asset_circulation_detail` (
  `acd_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `ach_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `status` varchar(1) NOT NULL COMMENT 'I=IN; O=OUT',
  `date_received` varchar(50) NOT NULL,
  `date_returned` varchar(50) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` int(10) NOT NULL,
  PRIMARY KEY (`acd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.asset_circulation_detail: 0 rows
/*!40000 ALTER TABLE `asset_circulation_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_circulation_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.asset_circulation_header
CREATE TABLE IF NOT EXISTS `asset_circulation_header` (
  `ach_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `from_project_id` bigint(12) NOT NULL,
  `to_project_id` bigint(12) NOT NULL,
  `employeeID` bigint(12) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` int(10) NOT NULL,
  PRIMARY KEY (`ach_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.asset_circulation_header: 0 rows
/*!40000 ALTER TABLE `asset_circulation_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_circulation_header` ENABLE KEYS */;


-- Dumping structure for table default_db.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `attachmentID` varchar(200) NOT NULL,
  `Afilename` varchar(200) NOT NULL,
  `pmid` varchar(200) NOT NULL,
  PRIMARY KEY (`attachmentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.attachments: 0 rows
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;


-- Dumping structure for table default_db.audit
CREATE TABLE IF NOT EXISTS `audit` (
  `audit_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(30) NOT NULL,
  `header_id` bigint(12) NOT NULL,
  `header` varchar(30) NOT NULL,
  `transaction` char(1) NOT NULL,
  PRIMARY KEY (`audit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.audit: 13 rows
/*!40000 ALTER TABLE `audit` DISABLE KEYS */;
INSERT INTO `audit` (`audit_id`, `user_id`, `header_id`, `header`, `transaction`) VALUES
	(1, '20160719-110150', 1, 'issuance_header_id', 'I'),
	(2, '20170813-105326', 1, 'gltran_header_id', 'I'),
	(3, '20170813-105326', 1, 'pr_header_id', 'I'),
	(4, '20170813-105326', 1, 'pr_header_id', 'U'),
	(5, '20170813-105326', 1, 'pr_header_id', 'C'),
	(6, '20170813-105326', 2, 'pr_header_id', 'I'),
	(7, '20170813-105326', 2, 'pr_header_id', 'U'),
	(8, '20170813-105326', 2, 'pr_header_id', 'F'),
	(9, '20170813-105326', 3, 'pr_header_id', 'I'),
	(10, '20170813-105326', 3, 'pr_header_id', 'F'),
	(11, '20170813-105326', 1, 'po_header_id', 'I'),
	(12, '20170813-105326', 1, 'po_header_id', 'C'),
	(13, '20170813-105326', 2, 'po_header_id', 'I');
/*!40000 ALTER TABLE `audit` ENABLE KEYS */;


-- Dumping structure for table default_db.audit_trail
CREATE TABLE IF NOT EXISTS `audit_trail` (
  `audit_trail_id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `user_id` varchar(30) NOT NULL,
  `header_id` bigint(12) NOT NULL,
  `trans` varchar(20) NOT NULL,
  `time_entry` datetime NOT NULL,
  PRIMARY KEY (`audit_trail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.audit_trail: ~0 rows (approximately)
/*!40000 ALTER TABLE `audit_trail` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_trail` ENABLE KEYS */;


-- Dumping structure for table default_db.batching_plant_categ
CREATE TABLE IF NOT EXISTS `batching_plant_categ` (
  `batching_plant_categ_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `batching_plant_categ` varchar(100) NOT NULL,
  `batching_plant_categ_void` char(1) DEFAULT '0',
  PRIMARY KEY (`batching_plant_categ_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.batching_plant_categ: 0 rows
/*!40000 ALTER TABLE `batching_plant_categ` DISABLE KEYS */;
/*!40000 ALTER TABLE `batching_plant_categ` ENABLE KEYS */;


-- Dumping structure for table default_db.batch_prod
CREATE TABLE IF NOT EXISTS `batch_prod` (
  `batch_prod_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `operator_id` bigint(12) NOT NULL,
  `eq_operator_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `weather` varchar(100) NOT NULL,
  `cement` decimal(12,2) NOT NULL,
  `wsand` decimal(12,2) NOT NULL,
  `agg_g1` decimal(12,2) NOT NULL,
  `agg_34` decimal(12,2) NOT NULL,
  `agg_38` decimal(12,2) NOT NULL,
  `admix` decimal(12,2) NOT NULL,
  `water` decimal(12,2) NOT NULL,
  `electricity` decimal(12,2) NOT NULL,
  `tm_rental` decimal(12,2) NOT NULL,
  `pl_rental` decimal(12,2) NOT NULL,
  `tmd_rental` decimal(12,2) NOT NULL,
  `manpower` decimal(12,2) NOT NULL,
  `fuel` decimal(12,2) NOT NULL,
  `incentives` decimal(12,2) NOT NULL,
  `depre` decimal(12,2) NOT NULL,
  `cement_price` decimal(12,2) NOT NULL,
  `wsand_price` decimal(12,2) NOT NULL,
  `agg_g1_price` decimal(12,2) NOT NULL,
  `agg_34_price` decimal(12,2) NOT NULL,
  `agg_38_price` decimal(12,2) NOT NULL,
  `admix_price` decimal(12,2) NOT NULL,
  `water_price` decimal(12,2) NOT NULL,
  `electricity_price` decimal(12,2) NOT NULL,
  `tm_rental_price` decimal(12,2) NOT NULL,
  `pl_rental_price` decimal(12,2) NOT NULL,
  `tmd_rental_price` decimal(12,2) NOT NULL,
  `manpower_price` decimal(12,2) NOT NULL,
  `fuel_price` decimal(12,2) NOT NULL,
  `incentives_price` decimal(12,2) NOT NULL,
  `depre_cost` decimal(12,2) NOT NULL,
  `total_vol` decimal(12,2) NOT NULL,
  `price_unit` decimal(12,2) NOT NULL,
  `user_id` varchar(40) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `stock_id` bigint(12) DEFAULT NULL,
  `project_id` bigint(12) DEFAULT NULL,
  `from_project_id` bigint(12) NOT NULL DEFAULT '10',
  `remarks` text,
  `billed` char(1) DEFAULT '0',
  PRIMARY KEY (`batch_prod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.batch_prod: ~0 rows (approximately)
/*!40000 ALTER TABLE `batch_prod` DISABLE KEYS */;
/*!40000 ALTER TABLE `batch_prod` ENABLE KEYS */;


-- Dumping structure for table default_db.biometric_entries
CREATE TABLE IF NOT EXISTS `biometric_entries` (
  `biometric_ID` bigint(50) unsigned NOT NULL AUTO_INCREMENT,
  `empID` bigint(20) DEFAULT NULL,
  `No` bigint(50) DEFAULT NULL,
  `b_date` date DEFAULT NULL,
  `b_time` time NOT NULL,
  `brand` char(1) NOT NULL,
  `processed` char(1) NOT NULL DEFAULT '0',
  `old` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`biometric_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.biometric_entries: 0 rows
/*!40000 ALTER TABLE `biometric_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `biometric_entries` ENABLE KEYS */;


-- Dumping structure for table default_db.bir_contrib
CREATE TABLE IF NOT EXISTS `bir_contrib` (
  `bir_contribID` int(10) NOT NULL AUTO_INCREMENT,
  `empStatID` int(3) NOT NULL,
  `bir_table_type` char(1) NOT NULL,
  `no_of_dependents` int(1) NOT NULL,
  `salary_cutoff` decimal(10,2) NOT NULL,
  `tax_value` decimal(10,2) NOT NULL,
  `over` float(10,2) NOT NULL,
  PRIMARY KEY (`bir_contribID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.bir_contrib: 0 rows
/*!40000 ALTER TABLE `bir_contrib` DISABLE KEYS */;
/*!40000 ALTER TABLE `bir_contrib` ENABLE KEYS */;


-- Dumping structure for table default_db.brand
CREATE TABLE IF NOT EXISTS `brand` (
  `brand_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `brandcode` varchar(50) NOT NULL,
  `brandname` varchar(50) NOT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.brand: 0 rows
/*!40000 ALTER TABLE `brand` DISABLE KEYS */;
/*!40000 ALTER TABLE `brand` ENABLE KEYS */;


-- Dumping structure for table default_db.budget_deduction
CREATE TABLE IF NOT EXISTS `budget_deduction` (
  `budget_deduction_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `rr_detail_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  PRIMARY KEY (`budget_deduction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.budget_deduction: ~0 rows (approximately)
/*!40000 ALTER TABLE `budget_deduction` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget_deduction` ENABLE KEYS */;


-- Dumping structure for table default_db.budget_detail
CREATE TABLE IF NOT EXISTS `budget_detail` (
  `budget_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `budget_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`budget_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.budget_detail: 0 rows
/*!40000 ALTER TABLE `budget_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.budget_equipment_detail
CREATE TABLE IF NOT EXISTS `budget_equipment_detail` (
  `budget_equipment_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `budget_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `rate_per_day` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`budget_equipment_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.budget_equipment_detail: 0 rows
/*!40000 ALTER TABLE `budget_equipment_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget_equipment_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.budget_fuel_detail
CREATE TABLE IF NOT EXISTS `budget_fuel_detail` (
  `budget_fuel_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `budget_header_id` bigint(12) NOT NULL,
  `fuel_id` bigint(12) NOT NULL,
  `equipment_id` bigint(12) NOT NULL,
  `consumption_per_day` decimal(12,2) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `cost_per_litter` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`budget_fuel_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.budget_fuel_detail: 0 rows
/*!40000 ALTER TABLE `budget_fuel_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget_fuel_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.budget_header
CREATE TABLE IF NOT EXISTS `budget_header` (
  `budget_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(12) NOT NULL,
  `description` varchar(50) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `date` date NOT NULL,
  `scope_of_work` varchar(100) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `remarks` text,
  PRIMARY KEY (`budget_header_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.budget_header: 1 rows
/*!40000 ALTER TABLE `budget_header` DISABLE KEYS */;
INSERT INTO `budget_header` (`budget_header_id`, `project_id`, `description`, `status`, `date`, `scope_of_work`, `work_category_id`, `sub_work_category_id`, `remarks`) VALUES
	(1, 1, '', 'F', '2016-07-20', '', 1, 2, 'test');
/*!40000 ALTER TABLE `budget_header` ENABLE KEYS */;


-- Dumping structure for table default_db.budget_section_detail
CREATE TABLE IF NOT EXISTS `budget_section_detail` (
  `budget_section_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `budget_detail_id` bigint(16) NOT NULL,
  `budget_header_id` bigint(16) NOT NULL,
  `section_id` bigint(16) NOT NULL,
  `stock_id` bigint(16) NOT NULL,
  `qty_used` decimal(12,2) NOT NULL,
  `description` text NOT NULL,
  `date_added` datetime NOT NULL,
  `is_deleted` int(10) unsigned NOT NULL,
  PRIMARY KEY (`budget_section_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.budget_section_detail: 0 rows
/*!40000 ALTER TABLE `budget_section_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget_section_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.budget_service_detail
CREATE TABLE IF NOT EXISTS `budget_service_detail` (
  `budget_service_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `budget_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `rate_per_day` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`budget_service_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.budget_service_detail: 0 rows
/*!40000 ALTER TABLE `budget_service_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget_service_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.cash_advance
CREATE TABLE IF NOT EXISTS `cash_advance` (
  `cash_advance_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `account_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(30) NOT NULL,
  `scope_of_work` varchar(100) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `project_id` bigint(12) NOT NULL,
  PRIMARY KEY (`cash_advance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.cash_advance: ~0 rows (approximately)
/*!40000 ALTER TABLE `cash_advance` DISABLE KEYS */;
/*!40000 ALTER TABLE `cash_advance` ENABLE KEYS */;


-- Dumping structure for table default_db.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `categ_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `level` int(1) NOT NULL,
  `subcateg_id` bigint(8) NOT NULL,
  `category` varchar(40) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `expense_id` int(11) DEFAULT NULL,
  `income_id` int(11) DEFAULT NULL,
  `category_code` varchar(30) NOT NULL,
  `category_type` char(1) NOT NULL,
  PRIMARY KEY (`categ_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.categories: 1 rows
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`categ_id`, `level`, `subcateg_id`, `category`, `remark`, `expense_id`, `income_id`, `category_code`, `category_type`) VALUES
	(1, 1, 0, 'HAULING EQUIPMENT', 'test, used for heavy equipments', 0, 0, 'HE01', 'E');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;


-- Dumping structure for table default_db.companies
CREATE TABLE IF NOT EXISTS `companies` (
  `companyID` int(5) NOT NULL AUTO_INCREMENT,
  `company_name` text NOT NULL,
  `company_abbrevation` varchar(10) NOT NULL,
  `company_logo` varchar(50) NOT NULL,
  `company_void` char(1) DEFAULT '0',
  PRIMARY KEY (`companyID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.companies: 1 rows
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` (`companyID`, `company_name`, `company_abbrevation`, `company_logo`, `company_void`) VALUES
	(1, 'MKU Construction', 'MKU', 'logo_main', '0');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;


-- Dumping structure for table default_db.contractor
CREATE TABLE IF NOT EXISTS `contractor` (
  `contractor_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `contractor` varchar(50) NOT NULL,
  `contractor_code` varchar(50) DEFAULT NULL,
  `address` varchar(60) NOT NULL,
  `contactno` varchar(20) NOT NULL,
  `contactperson` varchar(50) NOT NULL,
  PRIMARY KEY (`contractor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.contractor: 0 rows
/*!40000 ALTER TABLE `contractor` DISABLE KEYS */;
/*!40000 ALTER TABLE `contractor` ENABLE KEYS */;


-- Dumping structure for table default_db.contracts_alp
CREATE TABLE IF NOT EXISTS `contracts_alp` (
  `alp_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employeeID` int(11) NOT NULL,
  `con_date` date NOT NULL,
  `start_date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `project_id` int(11) NOT NULL,
  `salary` decimal(12,2) NOT NULL,
  `position` varchar(100) NOT NULL,
  PRIMARY KEY (`alp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.contracts_alp: 0 rows
/*!40000 ALTER TABLE `contracts_alp` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts_alp` ENABLE KEYS */;


-- Dumping structure for table default_db.contracts_oncall
CREATE TABLE IF NOT EXISTS `contracts_oncall` (
  `oncall_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employeeID` int(11) NOT NULL,
  `con_date` date NOT NULL,
  `no_of_days` varchar(200) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `rom_no` varchar(50) NOT NULL,
  `project_id` int(11) NOT NULL,
  `position` varchar(100) NOT NULL,
  `salary` decimal(12,2) NOT NULL,
  PRIMARY KEY (`oncall_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.contracts_oncall: 0 rows
/*!40000 ALTER TABLE `contracts_oncall` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts_oncall` ENABLE KEYS */;


-- Dumping structure for table default_db.contracts_raf
CREATE TABLE IF NOT EXISTS `contracts_raf` (
  `raf_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employeeID` int(11) NOT NULL,
  `con_date` date NOT NULL,
  `effectivity_date` date NOT NULL,
  `project_id` int(11) NOT NULL,
  `base_rate` decimal(12,2) NOT NULL,
  `allowance` decimal(12,2) NOT NULL,
  `others` decimal(12,2) NOT NULL,
  `position` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `separation` int(1) NOT NULL,
  PRIMARY KEY (`raf_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.contracts_raf: 0 rows
/*!40000 ALTER TABLE `contracts_raf` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts_raf` ENABLE KEYS */;


-- Dumping structure for table default_db.contracts_tsp
CREATE TABLE IF NOT EXISTS `contracts_tsp` (
  `tsp_id` int(11) NOT NULL AUTO_INCREMENT,
  `employeeID` int(11) NOT NULL,
  `con_date` date NOT NULL,
  `effectivity_date` date NOT NULL,
  `project_id` int(10) NOT NULL,
  `base_rate` decimal(12,2) NOT NULL,
  `allowance` decimal(12,2) NOT NULL,
  `others` decimal(12,2) NOT NULL,
  `position` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`tsp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.contracts_tsp: 0 rows
/*!40000 ALTER TABLE `contracts_tsp` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts_tsp` ENABLE KEYS */;


-- Dumping structure for table default_db.cr_detail
CREATE TABLE IF NOT EXISTS `cr_detail` (
  `cr_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `cr_header_id` bigint(12) NOT NULL,
  `gchart_id` bigint(12) NOT NULL,
  `_amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`cr_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.cr_detail: ~0 rows (approximately)
/*!40000 ALTER TABLE `cr_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.cr_header
CREATE TABLE IF NOT EXISTS `cr_header` (
  `cr_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `invoice_no` varchar(30) NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `user_id` varchar(40) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `cash_gchart_id` bigint(12) NOT NULL,
  `ar_gchart_id` bigint(12) NOT NULL,
  `or_no` varchar(30) NOT NULL,
  `bank` varchar(50) NOT NULL,
  `check_date` date NOT NULL,
  `check_no` varchar(50) NOT NULL,
  `or_type` varchar(20) NOT NULL,
  `particulars` text,
  `received_from` text,
  PRIMARY KEY (`cr_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.cr_header: ~0 rows (approximately)
/*!40000 ALTER TABLE `cr_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_header` ENABLE KEYS */;


-- Dumping structure for table default_db.cr_header_id
CREATE TABLE IF NOT EXISTS `cr_header_id` (
  `cr_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `invoice_no` varchar(30) NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `amount` decimal(12,0) NOT NULL,
  `user_id` varchar(40) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `cash_gchart_id` bigint(12) NOT NULL,
  `ar_gchart_id` bigint(12) NOT NULL,
  `or_no` varchar(30) NOT NULL,
  PRIMARY KEY (`cr_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.cr_header_id: ~0 rows (approximately)
/*!40000 ALTER TABLE `cr_header_id` DISABLE KEYS */;
/*!40000 ALTER TABLE `cr_header_id` ENABLE KEYS */;


-- Dumping structure for table default_db.customer
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `customer_last_name` varchar(100) NOT NULL,
  `customer_first_name` varchar(100) NOT NULL,
  `customer_middle_name` varchar(100) NOT NULL,
  `customer_appel` varchar(10) NOT NULL,
  `customer_gender` varchar(50) NOT NULL,
  `customer_civil_status` varchar(50) NOT NULL,
  `customer_tel` varchar(50) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_address1` text NOT NULL,
  `customer_address2` text NOT NULL,
  `remarks` text NOT NULL,
  `customer_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.customer: ~0 rows (approximately)
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;


-- Dumping structure for table default_db.cv_detail
CREATE TABLE IF NOT EXISTS `cv_detail` (
  `cv_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `cv_header_id` bigint(12) NOT NULL,
  `apv_header_id` bigint(12) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `gchart_id` bigint(12) NOT NULL,
  `project_id` bigint(12) DEFAULT NULL,
  `account_id` bigint(12) DEFAULT NULL,
  PRIMARY KEY (`cv_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.cv_detail: ~0 rows (approximately)
/*!40000 ALTER TABLE `cv_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `cv_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.cv_header
CREATE TABLE IF NOT EXISTS `cv_header` (
  `cv_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `percent` decimal(12,2) NOT NULL,
  `cv_date` date NOT NULL,
  `check_date` date NOT NULL,
  `check_no` varchar(50) NOT NULL,
  `supplier_id` bigint(12) NOT NULL,
  `cash_gchart_id` bigint(12) NOT NULL,
  `ap_gchart_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(20) NOT NULL,
  `type` char(1) NOT NULL DEFAULT 'M' COMMENT 'M-aterials ; E-xpense',
  `vat` decimal(12,2) DEFAULT NULL,
  `vat_gchart_id` bigint(12) DEFAULT NULL,
  `wtax` decimal(12,2) DEFAULT NULL,
  `wtax_gchart_id` bigint(12) DEFAULT NULL,
  `particulars` text,
  `cv_no` varchar(20) DEFAULT NULL,
  `cleared` char(1) DEFAULT '0',
  `released` int(10) NOT NULL,
  `date_cleared` date DEFAULT NULL,
  `date_released` date DEFAULT NULL,
  `retention_gchart_id` bigint(12) DEFAULT NULL,
  `chargable_gchart_id` bigint(12) DEFAULT NULL,
  `retention_amount` decimal(12,2) DEFAULT NULL,
  `chargable_amount` decimal(12,2) DEFAULT NULL,
  `sub_apv_header_id` bigint(12) DEFAULT NULL,
  `rmy_gchart_id` bigint(12) DEFAULT NULL,
  `rmy_amount` decimal(12,2) DEFAULT NULL,
  `cash_amount` decimal(12,2) DEFAULT NULL,
  `retention_project_id` bigint(12) NOT NULL,
  `printed` char(1) DEFAULT '0',
  `first_pdc_date` date DEFAULT NULL,
  `no_of_payments` int(11) DEFAULT NULL,
  `printing_type` int(11) NOT NULL,
  PRIMARY KEY (`cv_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.cv_header: ~0 rows (approximately)
/*!40000 ALTER TABLE `cv_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `cv_header` ENABLE KEYS */;


-- Dumping structure for table default_db.day_status
CREATE TABLE IF NOT EXISTS `day_status` (
  `dayStatID` int(3) NOT NULL AUTO_INCREMENT,
  `dayStat` varchar(50) DEFAULT NULL,
  `dayStatValue` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`dayStatID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.day_status: 0 rows
/*!40000 ALTER TABLE `day_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `day_status` ENABLE KEYS */;


-- Dumping structure for table default_db.delivery
CREATE TABLE IF NOT EXISTS `delivery` (
  `delivery_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `package_id` bigint(8) NOT NULL,
  `customername` bigint(8) NOT NULL,
  `finishedproduct` bigint(12) NOT NULL,
  `qty` double(12,3) NOT NULL,
  `deliverydate` date NOT NULL,
  PRIMARY KEY (`delivery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.delivery: 0 rows
/*!40000 ALTER TABLE `delivery` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery` ENABLE KEYS */;


-- Dumping structure for table default_db.departments
CREATE TABLE IF NOT EXISTS `departments` (
  `Did` int(10) NOT NULL AUTO_INCREMENT,
  `Dname` varchar(200) NOT NULL,
  PRIMARY KEY (`Did`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.departments: 0 rows
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;


-- Dumping structure for table default_db.dependents
CREATE TABLE IF NOT EXISTS `dependents` (
  `dependentsID` bigint(50) NOT NULL AUTO_INCREMENT,
  `employeeID` bigint(50) NOT NULL,
  `dep_lname` varchar(50) NOT NULL,
  `dep_fname` varchar(50) NOT NULL,
  `dep_mname` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `dependents_void` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dependentsID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dependents: 0 rows
/*!40000 ALTER TABLE `dependents` DISABLE KEYS */;
/*!40000 ALTER TABLE `dependents` ENABLE KEYS */;


-- Dumping structure for table default_db.disc_action
CREATE TABLE IF NOT EXISTS `disc_action` (
  `actionID` int(2) NOT NULL AUTO_INCREMENT,
  `ac_desc` varchar(20) NOT NULL,
  `ac_code` varchar(5) NOT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.disc_action: 0 rows
/*!40000 ALTER TABLE `disc_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `disc_action` ENABLE KEYS */;


-- Dumping structure for table default_db.division
CREATE TABLE IF NOT EXISTS `division` (
  `divisionID` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `companyID` bigint(12) NOT NULL,
  `division_name` text NOT NULL,
  `division_abbrevation` varchar(10) NOT NULL,
  `division_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`divisionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.division: 0 rows
/*!40000 ALTER TABLE `division` DISABLE KEYS */;
/*!40000 ALTER TABLE `division` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_ap
CREATE TABLE IF NOT EXISTS `dprc_ap` (
  `dprc_ap_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `issuance_detail_id` bigint(16) NOT NULL,
  `issuance_header_id` bigint(16) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(100) NOT NULL,
  `account_id` bigint(12) NOT NULL,
  `equipment_id` bigint(12) NOT NULL,
  `joborder_header_id` bigint(12) DEFAULT NULL,
  `quantity_cum` decimal(12,4) DEFAULT NULL,
  `driverID` int(20) DEFAULT NULL,
  `_reference` varchar(30) DEFAULT NULL,
  `_unit` varchar(40) NOT NULL,
  PRIMARY KEY (`dprc_ap_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_ap: 0 rows
/*!40000 ALTER TABLE `dprc_ap` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_ap` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_dp
CREATE TABLE IF NOT EXISTS `dprc_dp` (
  `dprc_dp_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `application_id` bigint(12) NOT NULL,
  `dprc_payment_id` bigint(12) NOT NULL,
  `dp_principal` double(10,2) NOT NULL,
  `dp_days` int(10) NOT NULL,
  `dp_penalty` decimal(10,2) NOT NULL,
  `dp_outbal` double(10,2) NOT NULL,
  `remarks` text NOT NULL,
  `discount` tinyint(1) NOT NULL,
  PRIMARY KEY (`dprc_dp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_dp: 0 rows
/*!40000 ALTER TABLE `dprc_dp` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_dp` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_dp_code
CREATE TABLE IF NOT EXISTS `dprc_dp_code` (
  `dp_code` int(10) NOT NULL AUTO_INCREMENT,
  `dp_type` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL,
  `term` int(2) NOT NULL,
  PRIMARY KEY (`dp_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_dp_code: 0 rows
/*!40000 ALTER TABLE `dprc_dp_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_dp_code` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_inventory
CREATE TABLE IF NOT EXISTS `dprc_inventory` (
  `inv_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subd_id` bigint(12) NOT NULL,
  `model_id` bigint(12) NOT NULL,
  `inv_phase` varchar(50) NOT NULL,
  `inv_block` varchar(50) NOT NULL,
  `inv_lot` varchar(20) NOT NULL,
  `inv_lot_area` decimal(12,4) NOT NULL,
  `inv_floor_area` decimal(12,4) NOT NULL,
  `application_id` bigint(12) NOT NULL,
  `inv_void` char(1) DEFAULT '0',
  PRIMARY KEY (`inv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_inventory: 0 rows
/*!40000 ALTER TABLE `dprc_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_inventory` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_ledger
CREATE TABLE IF NOT EXISTS `dprc_ledger` (
  `dprc_ledger_id` bigint(50) NOT NULL AUTO_INCREMENT,
  `dprc_payment_id` bigint(50) NOT NULL,
  `period` varchar(10) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `principal` decimal(12,2) NOT NULL,
  `interest` decimal(12,2) NOT NULL,
  `due_date` date NOT NULL,
  `penalty` decimal(12,2) NOT NULL,
  `late_days` decimal(12,2) NOT NULL,
  `outbal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`dprc_ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_ledger: ~0 rows (approximately)
/*!40000 ALTER TABLE `dprc_ledger` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_ledger` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_package_types
CREATE TABLE IF NOT EXISTS `dprc_package_types` (
  `package_type_id` int(10) NOT NULL AUTO_INCREMENT,
  `package_type` varchar(50) NOT NULL,
  PRIMARY KEY (`package_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_package_types: 0 rows
/*!40000 ALTER TABLE `dprc_package_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_package_types` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_payment
CREATE TABLE IF NOT EXISTS `dprc_payment` (
  `dprc_payment_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `application_id` bigint(12) NOT NULL,
  `or_date` date NOT NULL,
  `postcode` char(1) NOT NULL,
  `pay_mode` varchar(100) NOT NULL,
  `date_encoded` date NOT NULL,
  `payment_amount` decimal(12,2) NOT NULL,
  `or_no` varchar(100) NOT NULL,
  `penalize` tinyint(1) NOT NULL,
  `check_no` varchar(100) NOT NULL,
  `user_id` varchar(40) NOT NULL,
  `remarks` text NOT NULL,
  PRIMARY KEY (`dprc_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_payment: ~0 rows (approximately)
/*!40000 ALTER TABLE `dprc_payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_payment` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_payment_codes
CREATE TABLE IF NOT EXISTS `dprc_payment_codes` (
  `payment_code` int(10) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(50) NOT NULL,
  `term` int(2) NOT NULL,
  `type` varchar(5) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`payment_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_payment_codes: 0 rows
/*!40000 ALTER TABLE `dprc_payment_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_payment_codes` ENABLE KEYS */;


-- Dumping structure for table default_db.dprc_post_codes
CREATE TABLE IF NOT EXISTS `dprc_post_codes` (
  `postcode_id` int(10) NOT NULL AUTO_INCREMENT,
  `postcode_desc` varchar(30) NOT NULL,
  `postcode` char(1) NOT NULL,
  PRIMARY KEY (`postcode_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dprc_post_codes: 0 rows
/*!40000 ALTER TABLE `dprc_post_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `dprc_post_codes` ENABLE KEYS */;


-- Dumping structure for table default_db.drivers
CREATE TABLE IF NOT EXISTS `drivers` (
  `driverID` int(20) NOT NULL AUTO_INCREMENT,
  `driver_name` varchar(100) NOT NULL,
  `driver_void` char(1) DEFAULT '0',
  PRIMARY KEY (`driverID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.drivers: 0 rows
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;


-- Dumping structure for table default_db.dr_detail
CREATE TABLE IF NOT EXISTS `dr_detail` (
  `dr_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `dr_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `package_id` bigint(8) NOT NULL,
  `srp` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` char(1) NOT NULL,
  PRIMARY KEY (`dr_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dr_detail: 0 rows
/*!40000 ALTER TABLE `dr_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `dr_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.dr_header
CREATE TABLE IF NOT EXISTS `dr_header` (
  `dr_header_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `drnum` varchar(15) DEFAULT NULL,
  `date` date NOT NULL,
  `account_id` bigint(8) NOT NULL,
  `locale_id` int(4) NOT NULL,
  `grossamount` decimal(12,2) DEFAULT NULL,
  `netamount` decimal(12,2) DEFAULT NULL,
  `discounttotal` decimal(10,2) DEFAULT NULL,
  `tax` decimal(12,2) DEFAULT NULL,
  `paytype` char(1) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  `order_header_id` bigint(8) NOT NULL,
  `freight` decimal(12,2) DEFAULT NULL,
  `time_entered` datetime DEFAULT NULL,
  PRIMARY KEY (`dr_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dr_header: 0 rows
/*!40000 ALTER TABLE `dr_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `dr_header` ENABLE KEYS */;


-- Dumping structure for table default_db.dtr
CREATE TABLE IF NOT EXISTS `dtr` (
  `dtrID` bigint(50) unsigned NOT NULL AUTO_INCREMENT,
  `employeeID` bigint(20) NOT NULL,
  `dtr_date` date NOT NULL,
  `fieldID` int(5) DEFAULT NULL,
  `dayStatID` int(3) NOT NULL,
  `employee_statusID` int(3) NOT NULL,
  `workID` bigint(10) NOT NULL,
  `saved_rate` double(10,2) DEFAULT NULL,
  `work_value` decimal(10,6) NOT NULL,
  `in1` time NOT NULL,
  `out1` time NOT NULL,
  `in2` time NOT NULL,
  `out2` time NOT NULL,
  `hrs_required` decimal(10,2) NOT NULL,
  `break` decimal(10,2) NOT NULL,
  `hrs_ot` decimal(10,2) NOT NULL,
  `hrs_ut` decimal(10,2) NOT NULL,
  `unitID` int(3) NOT NULL,
  `incentives` decimal(10,2) DEFAULT NULL,
  `userID` varchar(50) NOT NULL,
  `closed` char(1) DEFAULT '0',
  `dtr_void` char(1) DEFAULT '0',
  `saved_allowance_rate` decimal(12,2) NOT NULL,
  `actual_rate` decimal(12,2) DEFAULT NULL,
  `time_in` time NOT NULL,
  PRIMARY KEY (`dtrID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.dtr: 0 rows
/*!40000 ALTER TABLE `dtr` DISABLE KEYS */;
/*!40000 ALTER TABLE `dtr` ENABLE KEYS */;


-- Dumping structure for table default_db.employee
CREATE TABLE IF NOT EXISTS `employee` (
  `employeeID` bigint(12) NOT NULL AUTO_INCREMENT,
  `employeeNUM` varchar(20) NOT NULL,
  `employee_lname` varchar(50) NOT NULL,
  `employee_fname` varchar(50) NOT NULL,
  `employee_mname` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `spouse_maiden` varchar(50) NOT NULL,
  `spouse_fname` varchar(50) NOT NULL,
  `spouse_mname` varchar(50) NOT NULL,
  `sex` char(1) NOT NULL,
  `dateofbirth` date NOT NULL,
  `datehired` date NOT NULL,
  `date_enrolled` date NOT NULL,
  `tin` varchar(15) NOT NULL,
  `sss` varchar(15) NOT NULL,
  `philhealth` varchar(15) NOT NULL,
  `hdmf` varchar(15) NOT NULL,
  `dependents` int(1) NOT NULL,
  `base_rate` double(255,2) NOT NULL,
  `employee_statusID` int(3) NOT NULL,
  `projectsID` int(3) NOT NULL,
  `employee_void` int(1) NOT NULL DEFAULT '0',
  `companyID` bigint(12) DEFAULT NULL,
  `divisionID` bigint(12) DEFAULT NULL,
  `employee_type_id` bigint(12) DEFAULT NULL,
  `allowance` decimal(12,4) DEFAULT NULL,
  `apply_tax` char(1) DEFAULT '1',
  `fixed_ot` decimal(12,2) DEFAULT '0.00',
  `inactive` char(1) DEFAULT '0',
  `apply_sss` char(1) DEFAULT '1',
  `apply_philhealth` char(1) DEFAULT '1',
  `apply_hdmf` char(1) DEFAULT '1',
  `position` text,
  `separation_date` date DEFAULT NULL,
  `rmy_employee_type_id` bigint(12) DEFAULT NULL,
  `employee_remarks` text,
  `contact_no` varchar(100) DEFAULT NULL,
  `emp_bank` varchar(100) DEFAULT NULL,
  `emp_account_no` varchar(100) DEFAULT NULL,
  `emp_time_in` time DEFAULT '07:30:00',
  `work_category_id` bigint(12) unsigned DEFAULT NULL,
  `release_type_id` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`employeeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.employee: 0 rows
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;


-- Dumping structure for table default_db.employee_contracts
CREATE TABLE IF NOT EXISTS `employee_contracts` (
  `contract_id` int(10) NOT NULL AUTO_INCREMENT,
  `contract_num` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL,
  `employeeID` int(10) NOT NULL,
  `projectsID` int(10) NOT NULL,
  `companyID` int(10) NOT NULL,
  `date_hired` date NOT NULL,
  `separation_date` date NOT NULL,
  `position` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `effectivity_date` date NOT NULL,
  `end_of_contract` date NOT NULL,
  PRIMARY KEY (`contract_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.employee_contracts: 0 rows
/*!40000 ALTER TABLE `employee_contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_contracts` ENABLE KEYS */;


-- Dumping structure for table default_db.employee_status
CREATE TABLE IF NOT EXISTS `employee_status` (
  `employee_statusID` int(3) NOT NULL AUTO_INCREMENT,
  `employee_status` varchar(10) NOT NULL,
  `employee_status_void` int(1) NOT NULL DEFAULT '0',
  `no_of_days` int(3) NOT NULL,
  PRIMARY KEY (`employee_statusID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.employee_status: 0 rows
/*!40000 ALTER TABLE `employee_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_status` ENABLE KEYS */;


-- Dumping structure for table default_db.employee_type
CREATE TABLE IF NOT EXISTS `employee_type` (
  `employee_type_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `employee_type` varchar(50) NOT NULL,
  `employee_type_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`employee_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.employee_type: ~0 rows (approximately)
/*!40000 ALTER TABLE `employee_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_type` ENABLE KEYS */;


-- Dumping structure for table default_db.emp_violations
CREATE TABLE IF NOT EXISTS `emp_violations` (
  `violation_id` int(12) NOT NULL AUTO_INCREMENT,
  `memo_num` varchar(120) NOT NULL,
  `employeeID` int(12) NOT NULL,
  `actionID` int(12) NOT NULL,
  `projectsID` int(12) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_awol` date NOT NULL,
  `violation1` varchar(120) NOT NULL,
  `date_termination` date NOT NULL,
  `date_resigned` date NOT NULL,
  `suspension_from` date NOT NULL,
  `suspension_to` date NOT NULL,
  `violation2` varchar(120) NOT NULL,
  PRIMARY KEY (`violation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.emp_violations: 0 rows
/*!40000 ALTER TABLE `emp_violations` DISABLE KEYS */;
/*!40000 ALTER TABLE `emp_violations` ENABLE KEYS */;


-- Dumping structure for table default_db.equipment
CREATE TABLE IF NOT EXISTS `equipment` (
  `eqID` int(10) NOT NULL AUTO_INCREMENT,
  `eq_name` varchar(100) NOT NULL,
  `date_of_purchase` date DEFAULT NULL,
  `rateperhour` double(10,2) NOT NULL,
  `minimum_time` float(10,2) NOT NULL,
  `eq_notes` text NOT NULL,
  `plateNumber` varchar(18) DEFAULT NULL,
  `eq_catID` int(2) NOT NULL,
  `eqModel` varchar(50) NOT NULL,
  `change_oil_start` date NOT NULL DEFAULT '0000-00-00',
  `hours_limit` int(3) DEFAULT '650',
  `stock_id` bigint(16) NOT NULL,
  PRIMARY KEY (`eqID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.equipment: 0 rows
/*!40000 ALTER TABLE `equipment` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipment` ENABLE KEYS */;


-- Dumping structure for table default_db.equipment_categories
CREATE TABLE IF NOT EXISTS `equipment_categories` (
  `eq_catID` int(2) NOT NULL AUTO_INCREMENT,
  `eq_cat_name` varchar(100) NOT NULL,
  `eq_cat_notes` text,
  PRIMARY KEY (`eq_catID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.equipment_categories: 0 rows
/*!40000 ALTER TABLE `equipment_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipment_categories` ENABLE KEYS */;


-- Dumping structure for table default_db.eur_branding
CREATE TABLE IF NOT EXISTS `eur_branding` (
  `eur_branding_id` int(11) NOT NULL AUTO_INCREMENT,
  `eur_header_id` int(11) NOT NULL,
  `branding_num` varchar(100) NOT NULL,
  PRIMARY KEY (`eur_branding_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.eur_branding: 0 rows
/*!40000 ALTER TABLE `eur_branding` DISABLE KEYS */;
/*!40000 ALTER TABLE `eur_branding` ENABLE KEYS */;


-- Dumping structure for table default_db.eur_charge_type
CREATE TABLE IF NOT EXISTS `eur_charge_type` (
  `eur_charge_type_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `eur_charge_type` varchar(100) NOT NULL,
  PRIMARY KEY (`eur_charge_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.eur_charge_type: ~0 rows (approximately)
/*!40000 ALTER TABLE `eur_charge_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `eur_charge_type` ENABLE KEYS */;


-- Dumping structure for table default_db.eur_detail
CREATE TABLE IF NOT EXISTS `eur_detail` (
  `eur_detail_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `eur_header_id` bigint(12) NOT NULL,
  `po_header_id` bigint(12) NOT NULL,
  `driver_id` bigint(12) NOT NULL,
  `released_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `computed_time` decimal(12,2) NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `remarks` text NOT NULL,
  `eur_void` char(1) NOT NULL DEFAULT '0',
  `eur_ref_id` bigint(12) NOT NULL,
  `eur_charge_type_id` bigint(12) NOT NULL,
  `unit_rate` decimal(12,2) NOT NULL,
  `km` decimal(12,4) DEFAULT NULL,
  `sqm` decimal(12,4) DEFAULT NULL,
  `cum` decimal(12,4) DEFAULT NULL,
  `po_detail_id` bigint(12) DEFAULT NULL,
  `from_ref` decimal(12,2) DEFAULT NULL,
  `to_ref` decimal(12,2) DEFAULT NULL,
  `eur_position` varchar(50) DEFAULT NULL,
  `project_id` bigint(12) DEFAULT NULL,
  `no_of_trips` decimal(12,2) NOT NULL,
  PRIMARY KEY (`eur_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.eur_detail: ~0 rows (approximately)
/*!40000 ALTER TABLE `eur_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `eur_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.eur_header
CREATE TABLE IF NOT EXISTS `eur_header` (
  `eur_header_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `eur_no` varchar(50) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `rate_per_hour` decimal(12,2) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `before_filling` decimal(12,2) NOT NULL,
  `after_filling` decimal(12,2) NOT NULL,
  `fvs_no` bigint(12) NOT NULL,
  `place_of_origin` varchar(100) NOT NULL,
  `fuel_station` varchar(100) NOT NULL,
  `no_of_liters` decimal(12,2) NOT NULL,
  `price_per_liter` decimal(12,2) NOT NULL,
  `fv_remarks` text,
  `encoded_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`eur_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.eur_header: ~0 rows (approximately)
/*!40000 ALTER TABLE `eur_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `eur_header` ENABLE KEYS */;


-- Dumping structure for table default_db.eur_income
CREATE TABLE IF NOT EXISTS `eur_income` (
  `eur_income_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `eur_income` varchar(100) NOT NULL,
  PRIMARY KEY (`eur_income_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.eur_income: ~0 rows (approximately)
/*!40000 ALTER TABLE `eur_income` DISABLE KEYS */;
/*!40000 ALTER TABLE `eur_income` ENABLE KEYS */;


-- Dumping structure for table default_db.eur_position
CREATE TABLE IF NOT EXISTS `eur_position` (
  `eur_position` varchar(50) NOT NULL,
  PRIMARY KEY (`eur_position`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.eur_position: 0 rows
/*!40000 ALTER TABLE `eur_position` DISABLE KEYS */;
/*!40000 ALTER TABLE `eur_position` ENABLE KEYS */;


-- Dumping structure for table default_db.eur_ref
CREATE TABLE IF NOT EXISTS `eur_ref` (
  `eur_ref_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `eur_unit_id` bigint(12) DEFAULT NULL,
  `eur_ref` text NOT NULL,
  `eur_ref_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eur_ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.eur_ref: ~0 rows (approximately)
/*!40000 ALTER TABLE `eur_ref` DISABLE KEYS */;
/*!40000 ALTER TABLE `eur_ref` ENABLE KEYS */;


-- Dumping structure for table default_db.eur_unit
CREATE TABLE IF NOT EXISTS `eur_unit` (
  `eur_unit_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `eur_unit` varchar(100) NOT NULL,
  `eur_unit_rate` decimal(12,2) NOT NULL,
  `eur_unit_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eur_unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.eur_unit: ~0 rows (approximately)
/*!40000 ALTER TABLE `eur_unit` DISABLE KEYS */;
/*!40000 ALTER TABLE `eur_unit` ENABLE KEYS */;


-- Dumping structure for table default_db.ev_detail
CREATE TABLE IF NOT EXISTS `ev_detail` (
  `ev_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `ev_header_id` bigint(12) NOT NULL,
  `gchart_id` bigint(12) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `project_id` bigint(12) DEFAULT NULL,
  PRIMARY KEY (`ev_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ev_detail: 0 rows
/*!40000 ALTER TABLE `ev_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `ev_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.ev_header
CREATE TABLE IF NOT EXISTS `ev_header` (
  `ev_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(50) NOT NULL,
  `cash_gchart_id` bigint(12) NOT NULL,
  `vat` decimal(12,2) DEFAULT NULL,
  `vat_gchart_id` bigint(12) DEFAULT NULL,
  `wtax` decimal(12,2) DEFAULT NULL,
  `wtax_gchart_id` bigint(12) DEFAULT NULL,
  `po_header_id` bigint(12) DEFAULT NULL,
  `labor_mat_po` bigint(12) DEFAULT NULL,
  `cv_header_id` bigint(12) DEFAULT NULL,
  PRIMARY KEY (`ev_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.ev_header: 0 rows
/*!40000 ALTER TABLE `ev_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `ev_header` ENABLE KEYS */;


-- Dumping structure for table default_db.fabrication
CREATE TABLE IF NOT EXISTS `fabrication` (
  `fabrication_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `from_project_id` bigint(12) NOT NULL,
  `to_project_id` bigint(12) NOT NULL,
  `remarks` text NOT NULL,
  `excess_stock_id` bigint(20) unsigned NOT NULL,
  `excess_quantity` decimal(12,4) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `prepared_by` varchar(100) NOT NULL,
  `prepared_time` datetime NOT NULL,
  `edited_by` varchar(100) DEFAULT NULL,
  `last_edit_time` datetime DEFAULT NULL,
  `excess_weight_per_unit` decimal(12,4) NOT NULL,
  `excess_total_weight` decimal(12,4) NOT NULL,
  `excess_length` decimal(12,4) NOT NULL,
  PRIMARY KEY (`fabrication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.fabrication: 0 rows
/*!40000 ALTER TABLE `fabrication` DISABLE KEYS */;
/*!40000 ALTER TABLE `fabrication` ENABLE KEYS */;


-- Dumping structure for table default_db.fabrication_product
CREATE TABLE IF NOT EXISTS `fabrication_product` (
  `fabrication_product_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fabrication_id` bigint(20) unsigned NOT NULL,
  `product_stock_id` bigint(20) NOT NULL,
  `product_quantity` decimal(12,4) NOT NULL,
  `product_void` char(1) NOT NULL DEFAULT '0',
  `product_weight_per_unit` decimal(12,4) NOT NULL,
  `product_total_weight` decimal(12,4) NOT NULL,
  `clr_no` varchar(100) NOT NULL,
  PRIMARY KEY (`fabrication_product_id`),
  KEY `fabrication_id` (`fabrication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.fabrication_product: 0 rows
/*!40000 ALTER TABLE `fabrication_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `fabrication_product` ENABLE KEYS */;


-- Dumping structure for table default_db.fabrication_raw_mat
CREATE TABLE IF NOT EXISTS `fabrication_raw_mat` (
  `fabrication_raw_mat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `raw_mat_stock_id` bigint(20) unsigned NOT NULL,
  `raw_mat_quantity` decimal(12,4) NOT NULL,
  `raw_mat_void` char(1) NOT NULL DEFAULT '0',
  `fabrication_id` bigint(20) unsigned NOT NULL,
  `raw_mat_weight_per_unit` decimal(12,4) NOT NULL,
  `raw_mat_total_weight` decimal(12,4) NOT NULL,
  PRIMARY KEY (`fabrication_raw_mat_id`),
  KEY `raw_mat_stock_id` (`raw_mat_stock_id`),
  KEY `fabrication_id` (`fabrication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.fabrication_raw_mat: ~0 rows (approximately)
/*!40000 ALTER TABLE `fabrication_raw_mat` DISABLE KEYS */;
/*!40000 ALTER TABLE `fabrication_raw_mat` ENABLE KEYS */;


-- Dumping structure for table default_db.financial_budget_detail
CREATE TABLE IF NOT EXISTS `financial_budget_detail` (
  `financial_budget_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `financial_budget_header_id` bigint(12) NOT NULL,
  `gchart_id` bigint(12) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`financial_budget_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.financial_budget_detail: 0 rows
/*!40000 ALTER TABLE `financial_budget_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `financial_budget_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.financial_budget_header
CREATE TABLE IF NOT EXISTS `financial_budget_header` (
  `financial_budget_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(50) NOT NULL,
  PRIMARY KEY (`financial_budget_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.financial_budget_header: 0 rows
/*!40000 ALTER TABLE `financial_budget_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `financial_budget_header` ENABLE KEYS */;


-- Dumping structure for table default_db.folders
CREATE TABLE IF NOT EXISTS `folders` (
  `folderID` int(10) NOT NULL AUTO_INCREMENT,
  `folderName` varchar(200) NOT NULL,
  `folderdescription` text,
  PRIMARY KEY (`folderID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.folders: 0 rows
/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
/*!40000 ALTER TABLE `folders` ENABLE KEYS */;


-- Dumping structure for table default_db.folder_contents
CREATE TABLE IF NOT EXISTS `folder_contents` (
  `policyID` varchar(200) NOT NULL,
  `policy_filename` varchar(200) NOT NULL,
  `policy_description` text,
  `folderID` int(10) NOT NULL,
  `uploaded_when` datetime DEFAULT NULL,
  PRIMARY KEY (`policyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.folder_contents: 0 rows
/*!40000 ALTER TABLE `folder_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `folder_contents` ENABLE KEYS */;


-- Dumping structure for table default_db.formulation_details
CREATE TABLE IF NOT EXISTS `formulation_details` (
  `formulation_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `formulation_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  PRIMARY KEY (`formulation_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.formulation_details: 0 rows
/*!40000 ALTER TABLE `formulation_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `formulation_details` ENABLE KEYS */;


-- Dumping structure for table default_db.formulation_header
CREATE TABLE IF NOT EXISTS `formulation_header` (
  `formulation_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `formulation_code` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `date_created` date NOT NULL,
  `date_updated` date NOT NULL,
  `main_id` bigint(12) NOT NULL,
  `kilosperbag` decimal(12,3) NOT NULL,
  `output` decimal(12,3) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `product_id` bigint(12) NOT NULL,
  PRIMARY KEY (`formulation_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.formulation_header: 0 rows
/*!40000 ALTER TABLE `formulation_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `formulation_header` ENABLE KEYS */;


-- Dumping structure for table default_db.gatepass
CREATE TABLE IF NOT EXISTS `gatepass` (
  `gatepass_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `employee_id` bigint(12) unsigned NOT NULL,
  `remarks` text,
  `status` char(1) NOT NULL DEFAULT 'S',
  `prepared_by` varchar(100) NOT NULL,
  `prepared_time` datetime NOT NULL,
  `edited_by` varchar(100) DEFAULT NULL,
  `last_edit_time` datetime DEFAULT NULL,
  `project_id` bigint(12) unsigned NOT NULL,
  `check_borrowed_items` char(1) DEFAULT '0',
  `check_for_return` char(1) DEFAULT '0',
  `check_for_project_use` char(1) DEFAULT '0',
  `check_personal_items` char(1) DEFAULT '0',
  `check_for_repair` char(1) DEFAULT '0',
  `check_chargeable_items` char(1) DEFAULT '0',
  `check_for_official_use` char(1) NOT NULL,
  `check_for_hauling` char(1) NOT NULL,
  `check_for_rescue` char(1) NOT NULL,
  `check_purchase` char(1) NOT NULL,
  `items_check` char(1) NOT NULL,
  `vehicle_check` char(1) NOT NULL,
  `eur_reference` varchar(50) NOT NULL,
  `check_for_pouring` char(1) NOT NULL,
  `stock_id` int(10) NOT NULL,
  `supplier_id` bigint(12) unsigned NOT NULL,
  `visitor` varchar(100) NOT NULL,
  `reference` varchar(100) NOT NULL,
  PRIMARY KEY (`gatepass_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.gatepass: 0 rows
/*!40000 ALTER TABLE `gatepass` DISABLE KEYS */;
/*!40000 ALTER TABLE `gatepass` ENABLE KEYS */;


-- Dumping structure for table default_db.gatepass_detail
CREATE TABLE IF NOT EXISTS `gatepass_detail` (
  `gatepass_detail_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `gatepass_id` bigint(12) unsigned NOT NULL,
  `stock_id` bigint(12) unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `gatepass_void` char(1) NOT NULL DEFAULT '0',
  `header_id` bigint(12) unsigned NOT NULL,
  `is_returned` char(1) DEFAULT '0',
  PRIMARY KEY (`gatepass_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.gatepass_detail: 0 rows
/*!40000 ALTER TABLE `gatepass_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `gatepass_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.gchart
CREATE TABLE IF NOT EXISTS `gchart` (
  `gchart_id` int(11) NOT NULL AUTO_INCREMENT,
  `acode` varchar(10) NOT NULL,
  `scode` varchar(10) NOT NULL,
  `gchart` varchar(100) NOT NULL,
  `sub_mclass` varchar(10) DEFAULT NULL,
  `enable` char(1) NOT NULL DEFAULT 'Y',
  `gchart_void` char(1) NOT NULL DEFAULT '0',
  `mclass` varchar(10) DEFAULT NULL,
  `parent_gchart_id` bigint(12) NOT NULL DEFAULT '0',
  `beg_debit` decimal(12,2) NOT NULL,
  `beg_credit` decimal(12,2) NOT NULL,
  PRIMARY KEY (`gchart_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.gchart: 5 rows
/*!40000 ALTER TABLE `gchart` DISABLE KEYS */;
INSERT INTO `gchart` (`gchart_id`, `acode`, `scode`, `gchart`, `sub_mclass`, `enable`, `gchart_void`, `mclass`, `parent_gchart_id`, `beg_debit`, `beg_credit`) VALUES
	(1, '00001', '', 'Cash on Hand', '1', 'Y', '0', 'A', 0, 10000000.00, 0.00),
	(2, '00002', '', 'Accounts Payables', '2', 'Y', '0', 'L', 0, 5000000.00, 0.00),
	(3, '00003', '', 'Materials Inventory', '3', 'Y', '0', 'A', 0, 0.00, 0.00),
	(4, '00004', '', 'Direct Materials', '8', 'Y', '0', 'E', 0, 0.00, 0.00),
	(5, '00005', '', 'Cash in Bank', '3', 'Y', '0', 'A', 0, 0.00, 0.00);
/*!40000 ALTER TABLE `gchart` ENABLE KEYS */;


-- Dumping structure for table default_db.gchart_beginning
CREATE TABLE IF NOT EXISTS `gchart_beginning` (
  `gchart_bb_id` int(11) NOT NULL AUTO_INCREMENT,
  `gchart_id` varchar(10) NOT NULL,
  `year_bal` varchar(4) NOT NULL,
  `date` datetime NOT NULL,
  `beg_debit` decimal(12,2) NOT NULL,
  `beg_credit` decimal(12,2) NOT NULL,
  PRIMARY KEY (`gchart_bb_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.gchart_beginning: 2 rows
/*!40000 ALTER TABLE `gchart_beginning` DISABLE KEYS */;
INSERT INTO `gchart_beginning` (`gchart_bb_id`, `gchart_id`, `year_bal`, `date`, `beg_debit`, `beg_credit`) VALUES
	(1, '1', '2016', '2016-07-20 12:07:33', 10000000.00, 0.00),
	(2, '2', '2016', '2016-07-20 12:13:28', 5000000.00, 0.00);
/*!40000 ALTER TABLE `gchart_beginning` ENABLE KEYS */;


-- Dumping structure for table default_db.gltran_detail
CREATE TABLE IF NOT EXISTS `gltran_detail` (
  `gltran_detail_id` bigint(100) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  `debit` decimal(14,2) NOT NULL,
  `credit` decimal(14,2) NOT NULL,
  `enable` char(1) NOT NULL,
  `gltran_header_id` bigint(50) unsigned NOT NULL,
  `gchart_id` int(11) NOT NULL,
  `rr_header_id` bigint(20) NOT NULL,
  `project_id` bigint(12) DEFAULT NULL,
  `supplier_id` bigint(12) DEFAULT NULL,
  `h` varchar(100) DEFAULT NULL,
  `h_id` bigint(12) DEFAULT NULL,
  PRIMARY KEY (`gltran_detail_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.gltran_detail: 2 rows
/*!40000 ALTER TABLE `gltran_detail` DISABLE KEYS */;
INSERT INTO `gltran_detail` (`gltran_detail_id`, `description`, `debit`, `credit`, `enable`, `gltran_header_id`, `gchart_id`, `rr_header_id`, `project_id`, `supplier_id`, `h`, `h_id`) VALUES
	(1, 'test', 1000.00, 0.00, 'Y', 1, 3, 0, 1, NULL, NULL, NULL),
	(2, 'test', 0.00, 1000.00, 'Y', 1, 2, 0, 1, NULL, NULL, NULL);
/*!40000 ALTER TABLE `gltran_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.gltran_header
CREATE TABLE IF NOT EXISTS `gltran_header` (
  `gltran_header_id` bigint(50) unsigned NOT NULL AUTO_INCREMENT,
  `xrefer` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `particulars` varchar(150) NOT NULL,
  `journal_id` int(8) NOT NULL,
  `details` blob NOT NULL,
  `account` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `account_id` varchar(20) NOT NULL,
  `checkdate` date NOT NULL,
  `mcheck` varchar(20) NOT NULL,
  `audit` blob NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'S',
  `user_id` varchar(20) NOT NULL,
  `generalreference` varchar(20) NOT NULL,
  `header` varchar(30) NOT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `header_id` bigint(20) unsigned DEFAULT NULL,
  `po_header_id` bigint(12) DEFAULT NULL,
  `trans` varchar(50) DEFAULT NULL,
  `trans_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`gltran_header_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.gltran_header: 1 rows
/*!40000 ALTER TABLE `gltran_header` DISABLE KEYS */;
INSERT INTO `gltran_header` (`gltran_header_id`, `xrefer`, `date`, `particulars`, `journal_id`, `details`, `account`, `address`, `account_id`, `checkdate`, `mcheck`, `audit`, `status`, `user_id`, `generalreference`, `header`, `bank`, `header_id`, `po_header_id`, `trans`, `trans_no`) VALUES
	(1, 'JV-123', '2017-08-06', 'Test', 1, _binary '', '', '', 'p-1', '0000-00-00', '', _binary 0x41646465642062793A2044656C61204372757A2C204A75616E6F6E20323031372D30382D31332031313A32333A30372C20, 'S', '20170813-105326', 'JV-2017-0001', '', '', NULL, 0, '', '');
/*!40000 ALTER TABLE `gltran_header` ENABLE KEYS */;


-- Dumping structure for table default_db.groups
CREATE TABLE IF NOT EXISTS `groups` (
  `groupID` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text,
  PRIMARY KEY (`groupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.groups: 0 rows
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;


-- Dumping structure for table default_db.group_members
CREATE TABLE IF NOT EXISTS `group_members` (
  `gm_id` varchar(200) NOT NULL,
  `groupID` int(10) NOT NULL,
  `userID` varchar(200) NOT NULL,
  PRIMARY KEY (`gm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.group_members: 0 rows
/*!40000 ALTER TABLE `group_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_members` ENABLE KEYS */;


-- Dumping structure for table default_db.heavy_equipment_categories
CREATE TABLE IF NOT EXISTS `heavy_equipment_categories` (
  `he_categ_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `stock_id` bigint(10) NOT NULL,
  `he_type_id` bigint(10) NOT NULL,
  `remarks` varchar(100) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`he_categ_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.heavy_equipment_categories: 0 rows
/*!40000 ALTER TABLE `heavy_equipment_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `heavy_equipment_categories` ENABLE KEYS */;


-- Dumping structure for table default_db.holiday
CREATE TABLE IF NOT EXISTS `holiday` (
  `holiday_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `description` text NOT NULL,
  `rate` decimal(12,4) NOT NULL,
  `holiday_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`holiday_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.holiday: ~0 rows (approximately)
/*!40000 ALTER TABLE `holiday` DISABLE KEYS */;
/*!40000 ALTER TABLE `holiday` ENABLE KEYS */;


-- Dumping structure for table default_db.invadjust_detail
CREATE TABLE IF NOT EXISTS `invadjust_detail` (
  `invadjust_detail_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `invadjust_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`invadjust_detail_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.invadjust_detail: 0 rows
/*!40000 ALTER TABLE `invadjust_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `invadjust_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.invadjust_header
CREATE TABLE IF NOT EXISTS `invadjust_header` (
  `invadjust_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `remarks` blob NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `project_id` bigint(12) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `scope_of_work` varchar(50) NOT NULL,
  PRIMARY KEY (`invadjust_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.invadjust_header: 0 rows
/*!40000 ALTER TABLE `invadjust_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `invadjust_header` ENABLE KEYS */;


-- Dumping structure for table default_db.issuance
CREATE TABLE IF NOT EXISTS `issuance` (
  `issuance_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `description` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(30) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `scope_of_work` varchar(100) NOT NULL,
  PRIMARY KEY (`issuance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.issuance: 0 rows
/*!40000 ALTER TABLE `issuance` DISABLE KEYS */;
/*!40000 ALTER TABLE `issuance` ENABLE KEYS */;


-- Dumping structure for table default_db.issuance_detail
CREATE TABLE IF NOT EXISTS `issuance_detail` (
  `issuance_detail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issuance_header_id` bigint(16) unsigned NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(100) NOT NULL,
  `account_id` bigint(12) NOT NULL,
  `equipment_id` bigint(12) NOT NULL,
  `joborder_header_id` bigint(12) DEFAULT NULL,
  `quantity_cum` decimal(12,4) DEFAULT NULL,
  `driverID` int(20) DEFAULT NULL,
  `_reference` varchar(100) DEFAULT NULL,
  `_unit` varchar(40) NOT NULL,
  `dprc_exported` char(1) DEFAULT '0',
  `posted` char(1) DEFAULT '0',
  `posted_to` bigint(12) DEFAULT NULL,
  PRIMARY KEY (`issuance_detail_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.issuance_detail: 0 rows
/*!40000 ALTER TABLE `issuance_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `issuance_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.issuance_header
CREATE TABLE IF NOT EXISTS `issuance_header` (
  `issuance_header_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(30) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `scope_of_work` varchar(100) NOT NULL,
  `issued_to` bigint(12) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `remarks` text,
  `encoded_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`issuance_header_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.issuance_header: 1 rows
/*!40000 ALTER TABLE `issuance_header` DISABLE KEYS */;
INSERT INTO `issuance_header` (`issuance_header_id`, `date`, `project_id`, `status`, `user_id`, `work_category_id`, `sub_work_category_id`, `scope_of_work`, `issued_to`, `reference`, `remarks`, `encoded_datetime`) VALUES
	(1, '2016-07-20', 1, 'S', '20160719-110150', 1, 0, '', 0, '131212', 'hjjhjhjh', '2016-07-20 16:19:29');
/*!40000 ALTER TABLE `issuance_header` ENABLE KEYS */;


-- Dumping structure for table default_db.joborder_detail
CREATE TABLE IF NOT EXISTS `joborder_detail` (
  `joborder_detail_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `joborder_header_id` bigint(12) unsigned NOT NULL,
  `stock_id` bigint(12) unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `ref_no` varchar(100) NOT NULL,
  `joborder_detail_void` char(1) NOT NULL DEFAULT '0',
  `issuance_detail_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`joborder_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.joborder_detail: ~0 rows (approximately)
/*!40000 ALTER TABLE `joborder_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `joborder_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.joborder_details
CREATE TABLE IF NOT EXISTS `joborder_details` (
  `joborderdetail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `joborder_id` varchar(20) NOT NULL,
  `formulation_id` varchar(40) NOT NULL,
  `type` varchar(50) NOT NULL,
  `material` bigint(12) NOT NULL,
  `quantity` double(10,3) NOT NULL,
  `cost` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`joborderdetail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.joborder_details: 0 rows
/*!40000 ALTER TABLE `joborder_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `joborder_details` ENABLE KEYS */;


-- Dumping structure for table default_db.joborder_header
CREATE TABLE IF NOT EXISTS `joborder_header` (
  `joborder_header_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `equipment_id` bigint(12) NOT NULL,
  `driver_id` bigint(12) NOT NULL,
  `job_id` bigint(12) NOT NULL,
  `inspected_by` bigint(12) NOT NULL,
  `estimated_hours` decimal(12,2) NOT NULL,
  `details` text NOT NULL,
  `conducted_by` bigint(12) NOT NULL,
  `date_started` date NOT NULL,
  `time_started` time NOT NULL,
  `date_completed` date NOT NULL,
  `time_completed` time NOT NULL,
  `trial_conducted_by` bigint(12) NOT NULL,
  `trial_date` date NOT NULL,
  `results` text NOT NULL,
  `accepted_by` bigint(12) NOT NULL,
  `accepted_date` date NOT NULL,
  `encoded_datetime` datetime NOT NULL,
  `encoded_by` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `jo_option` text,
  `reference` varchar(50) NOT NULL,
  `type` char(2) NOT NULL,
  `from_project_id` int(50) NOT NULL,
  `from_eqID` int(50) NOT NULL,
  `from_position` int(50) NOT NULL,
  `branding_num` varchar(100) NOT NULL,
  `to_project_id` int(50) NOT NULL,
  `to_eqID` int(50) NOT NULL,
  `to_position` int(50) NOT NULL,
  PRIMARY KEY (`joborder_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.joborder_header: ~0 rows (approximately)
/*!40000 ALTER TABLE `joborder_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `joborder_header` ENABLE KEYS */;


-- Dumping structure for table default_db.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `job_id` int(4) NOT NULL AUTO_INCREMENT,
  `job` varchar(100) NOT NULL,
  `job_typeID` int(4) NOT NULL,
  `s_time` float(10,2) NOT NULL,
  `is_alert` int(10) NOT NULL,
  `km_run` decimal(9,2) NOT NULL,
  `heq_d` decimal(9,2) NOT NULL,
  `truck_d` decimal(9,2) NOT NULL,
  `c_time` int(10) NOT NULL,
  PRIMARY KEY (`job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.jobs: 0 rows
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;


-- Dumping structure for table default_db.job_types
CREATE TABLE IF NOT EXISTS `job_types` (
  `job_typeID` int(4) NOT NULL AUTO_INCREMENT,
  `job_type` varchar(100) NOT NULL,
  PRIMARY KEY (`job_typeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.job_types: 0 rows
/*!40000 ALTER TABLE `job_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_types` ENABLE KEYS */;


-- Dumping structure for table default_db.journal
CREATE TABLE IF NOT EXISTS `journal` (
  `journal_id` int(8) NOT NULL AUTO_INCREMENT,
  `journal` varchar(40) NOT NULL,
  `journal_code` varchar(5) NOT NULL,
  `enable` char(1) NOT NULL,
  PRIMARY KEY (`journal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.journal: 5 rows
/*!40000 ALTER TABLE `journal` DISABLE KEYS */;
INSERT INTO `journal` (`journal_id`, `journal`, `journal_code`, `enable`) VALUES
	(1, 'General Journal', 'JV', 'Y'),
	(2, 'Purchase Journal', 'AP', 'Y'),
	(3, 'Sales Journal', 'SJ', 'Y'),
	(4, 'Disbursement Journal', 'DV', 'Y'),
	(5, 'Cash Receipts Journal', 'CR', 'Y');
/*!40000 ALTER TABLE `journal` ENABLE KEYS */;


-- Dumping structure for table default_db.jo_details
CREATE TABLE IF NOT EXISTS `jo_details` (
  `jo_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `jo_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `formulation_header_id` bigint(12) NOT NULL,
  `actualoutput` decimal(12,2) NOT NULL,
  PRIMARY KEY (`jo_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.jo_details: 0 rows
/*!40000 ALTER TABLE `jo_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `jo_details` ENABLE KEYS */;


-- Dumping structure for table default_db.jo_header
CREATE TABLE IF NOT EXISTS `jo_header` (
  `jo_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `order_header_id` bigint(12) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  PRIMARY KEY (`jo_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.jo_header: 0 rows
/*!40000 ALTER TABLE `jo_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `jo_header` ENABLE KEYS */;


-- Dumping structure for table default_db.junk_tires
CREATE TABLE IF NOT EXISTS `junk_tires` (
  `junk_tire_id` int(11) NOT NULL AUTO_INCREMENT,
  `branding_num` varchar(100) NOT NULL,
  `date_junked` date NOT NULL,
  PRIMARY KEY (`junk_tire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.junk_tires: ~0 rows (approximately)
/*!40000 ALTER TABLE `junk_tires` DISABLE KEYS */;
/*!40000 ALTER TABLE `junk_tires` ENABLE KEYS */;


-- Dumping structure for table default_db.labor_budget
CREATE TABLE IF NOT EXISTS `labor_budget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `work_category_id` int(11) NOT NULL,
  `sub_work_category_id` int(11) NOT NULL,
  `remarks` text NOT NULL,
  `date` date NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'S',
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.labor_budget: ~0 rows (approximately)
/*!40000 ALTER TABLE `labor_budget` DISABLE KEYS */;
/*!40000 ALTER TABLE `labor_budget` ENABLE KEYS */;


-- Dumping structure for table default_db.labor_budget_details
CREATE TABLE IF NOT EXISTS `labor_budget_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_code_id` int(11) NOT NULL,
  `labor_budget_id` int(11) NOT NULL,
  `is_deleted` int(10) unsigned NOT NULL,
  `qty` int(10) unsigned NOT NULL,
  `price_per_unit` decimal(12,2) NOT NULL,
  `no_per` int(10) NOT NULL,
  `total_qty` int(10) NOT NULL,
  `tag` int(10) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.labor_budget_details: ~0 rows (approximately)
/*!40000 ALTER TABLE `labor_budget_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `labor_budget_details` ENABLE KEYS */;


-- Dumping structure for table default_db.labor_budget_pr
CREATE TABLE IF NOT EXISTS `labor_budget_pr` (
  `pr_lb_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `labor_budget_details_id` bigint(12) NOT NULL,
  `pr_header_id` bigint(12) NOT NULL,
  `requested_qty` int(10) unsigned NOT NULL,
  `requested_no_per` int(10) NOT NULL,
  `total_req_qty` int(10) NOT NULL,
  `allowed` char(1) NOT NULL,
  `date_requested` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pr_lb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.labor_budget_pr: 0 rows
/*!40000 ALTER TABLE `labor_budget_pr` DISABLE KEYS */;
/*!40000 ALTER TABLE `labor_budget_pr` ENABLE KEYS */;


-- Dumping structure for table default_db.leave_info
CREATE TABLE IF NOT EXISTS `leave_info` (
  `leave_id` int(11) NOT NULL AUTO_INCREMENT,
  `lf_num` varchar(50) NOT NULL,
  `date_requested` date NOT NULL,
  `inclusive_date` date NOT NULL,
  `inclusive_date_to` date NOT NULL,
  `particular` tinytext NOT NULL,
  `employee_id` int(11) NOT NULL,
  `employee_type` char(10) NOT NULL,
  `prepared_by` varchar(100) NOT NULL,
  `prepared_time` datetime NOT NULL,
  `edited_by` varchar(100) NOT NULL,
  `last_edit_time` datetime NOT NULL,
  `status` char(2) NOT NULL DEFAULT 'S',
  PRIMARY KEY (`leave_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.leave_info: 0 rows
/*!40000 ALTER TABLE `leave_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_info` ENABLE KEYS */;


-- Dumping structure for table default_db.loan_ref
CREATE TABLE IF NOT EXISTS `loan_ref` (
  `loan_ref_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `loan_payment_id` bigint(12) NOT NULL,
  `paID` bigint(12) NOT NULL,
  PRIMARY KEY (`loan_ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.loan_ref: ~0 rows (approximately)
/*!40000 ALTER TABLE `loan_ref` DISABLE KEYS */;
/*!40000 ALTER TABLE `loan_ref` ENABLE KEYS */;


-- Dumping structure for table default_db.location
CREATE TABLE IF NOT EXISTS `location` (
  `locale_id` int(4) NOT NULL AUTO_INCREMENT,
  `location` varchar(50) NOT NULL,
  PRIMARY KEY (`locale_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.location: 0 rows
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
/*!40000 ALTER TABLE `location` ENABLE KEYS */;


-- Dumping structure for table default_db.maintenance
CREATE TABLE IF NOT EXISTS `maintenance` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `stock_id` int(10) NOT NULL,
  `job_id` int(11) NOT NULL,
  `date_fix` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.maintenance: 0 rows
/*!40000 ALTER TABLE `maintenance` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance` ENABLE KEYS */;


-- Dumping structure for table default_db.menu
CREATE TABLE IF NOT EXISTS `menu` (
  `M_id` int(10) NOT NULL AUTO_INCREMENT,
  `Mname` varchar(100) NOT NULL,
  `level` char(1) NOT NULL,
  `icon_filename` varchar(255) DEFAULT NULL,
  `parent` int(10) DEFAULT '0',
  `PCode` int(10) DEFAULT NULL,
  `enable` char(1) DEFAULT '1',
  `placement` int(1) DEFAULT NULL,
  PRIMARY KEY (`M_id`)
) ENGINE=MyISAM AUTO_INCREMENT=400 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.menu: 259 rows
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` (`M_id`, `Mname`, `level`, `icon_filename`, `parent`, `PCode`, `enable`, `placement`) VALUES
	(1, 'Messages', '1', 'email.png', 0, 0, '0', NULL),
	(3, 'Options', '1', 'asterisk_orange.png', 0, 0, '1', 6),
	(4, 'Private Messages', '2', '', 1, 20, '1', NULL),
	(5, 'Sent Messages', '2', '', 1, 21, '1', NULL),
	(8, 'List Unread', '3', '', 6, 7, '1', NULL),
	(9, 'Search Read', '3', '', 6, 5, '1', NULL),
	(13, 'Search Read SMS', '3', '', 10, 4, '1', NULL),
	(17, 'Change Password', '2', '', 3, 1, '1', NULL),
	(18, 'Manage Accounts', '2', '', 3, 0, '1', NULL),
	(19, 'User Access', '3', '', 18, 9, '1', NULL),
	(20, 'Messaging Groups', '3', '', 18, 22, '0', NULL),
	(21, 'Engineers', '3', '', 18, 10, '1', NULL),
	(34, 'Important Posts', '2', '', 1, 27, '1', NULL),
	(23, 'Manage Areas', '2', '', 3, 0, '1', NULL),
	(26, 'Feeders / Sensors Locations', '3', '', 23, 11, '1', NULL),
	(28, 'Keywords', '3', '', 27, 19, '1', NULL),
	(30, 'System Configurations', '2', '', 3, 0, '1', NULL),
	(31, 'Manage System Files', '3', '', 30, 26, '1', NULL),
	(32, 'Manage System Menu', '3', '', 30, 24, '1', NULL),
	(33, 'Access Groups and Privileges', '3', '', 18, 25, '1', 0),
	(35, 'New Message', '2', '', 1, 61, '1', NULL),
	(38, 'Manage Folders and Contents', '3', '', 36, 62, '1', NULL),
	(39, 'Master Files', '1', 'application_cascade.png', 0, 0, '1', 0),
	(40, 'Product Master', '2', '', 39, 66, '1', 1),
	(41, 'Categories', '2', '', 39, 67, '1', 2),
	(43, 'Supplier', '2', '', 39, 75, '1', 6),
	(166, 'Seach Stocks Receiving', '3', '', 164, 258, '1', 0),
	(167, 'Search Budget', '3', '', 154, 250, '1', 0),
	(163, 'Search Purchase Order', '3', '', 162, 167, '1', 0),
	(183, 'New Purchase Order', '3', '', 162, 165, '1', 0),
	(62, 'Inventory Report', '2', '', 61, 0, '1', 0),
	(52, 'Accounts Receivables', '2', 'money.png', 156, 0, '1', 11),
	(53, 'New Formulation', '3', '', 56, 79, '1', 2),
	(193, 'Accounts Payable', '2', '', 156, 0, '1', 11),
	(55, 'Search Formulation', '3', '', 56, 198, '1', 1),
	(56, 'Formulation', '2', '', 39, 0, '1', 7),
	(201, 'Account Type', '2', '', 39, 315, '1', 0),
	(61, 'Reports', '1', 'report.png', 0, 0, '1', 5),
	(175, 'Search Stock Returns', '3', '', 339, 265, '1', 2),
	(174, 'New Stock Returns', '3', '', 339, 232, '1', 1),
	(173, 'Stocks Return', '2', '', 156, 0, '0', 7),
	(70, 'Inventory Balance Report', '3', '', 62, 102, '0', 3),
	(186, 'New Service Receiving', '3', '', 185, 276, '1', 0),
	(169, 'Stocks Transfer', '2', '', 156, 0, '1', 5),
	(170, 'New Stocks Transfer', '3', '', 169, 263, '1', 0),
	(171, 'Issuance', '2', '', 156, 0, '1', 6),
	(172, 'New Issuance', '3', '', 171, 264, '1', 0),
	(82, 'New Customer Payment', '3', '', 81, 118, '1', 2),
	(80, 'Stock Card Report Project', '3', '', 62, 117, '1', 4),
	(83, 'Search Customer Payments', '3', '', 81, 121, '1', 1),
	(84, 'Accounts Receivable Reports', '2', '', 61, 0, '1', 3),
	(86, 'Account Ledger', '3', '', 84, 320, '1', 2),
	(165, 'New Stocks Receiving', '3', '', 164, 184, '1', 0),
	(88, 'Aging of Accounts', '3', '', 84, 323, '1', 4),
	(89, 'Due Checks', '3', '', 84, 325, '1', 5),
	(164, 'Stocks Receiving', '2', '', 156, 0, '1', 3),
	(91, 'Accounting', '1', 'book_open.png', 0, 0, '1', 4),
	(92, 'Chart of Accounts', '2', '', 91, 134, '1', 1),
	(93, 'Journal', '2', '', 91, 135, '1', 2),
	(94, 'GL Transactions', '2', '', 91, 0, '1', 3),
	(95, 'New GL Transaction', '3', '', 94, 136, '1', 2),
	(96, 'Search General Ledger', '3', '', 94, 137, '1', 1),
	(97, 'Sales Reports', '2', '', 61, 0, '0', 3),
	(98, 'Sales by Customer', '3', '', 97, 307, '1', 1),
	(99, 'Periodic Sales Report', '3', '', 97, 306, '1', 2),
	(100, 'Monthly Sales Report', '3', '', 97, 303, '1', 3),
	(101, 'Periodic Sales by Product', '3', '', 97, 305, '1', 4),
	(102, 'Monthly Sales Report By Product', '3', '', 97, 304, '0', 5),
	(103, 'Accounting Reports', '2', '', 61, 0, '1', 4),
	(104, 'Journal Listing', '3', '', 103, 293, '1', 1),
	(105, 'General Ledger Listing', '3', '', 103, 291, '1', 2),
	(107, 'Income Statement', '3', '', 103, 292, '1', 4),
	(382, 'Witholding Tax Report - MRR', '2', '', 61, 743, '1', 26),
	(112, 'Browse undelivered Items', '3', '', 110, 170, '0', 3),
	(113, 'Summary of Accounts Payable', '3', '', 205, 327, '1', 6),
	(114, 'Accounts Payable Ledger', '3', '', 205, 328, '1', 2),
	(143, 'Inventory Adjustment', '2', '', 156, 0, '1', 9),
	(144, 'New Inventory Adjustment', '3', '', 143, 230, '1', 2),
	(203, 'Sales Invoice', '3', '', 52, 316, '1', 1),
	(119, 'New AR Adjustment', '3', '', 118, 179, '1', 2),
	(120, 'Search AR Adjustment', '3', '', 118, 181, '1', 1),
	(200, 'Purchase Order Approval', '3', '', 162, 313, '1', 0),
	(207, 'Search AP Voucher', '3', '', 193, 344, '1', 3),
	(206, 'Aging of Account Payables', '3', '', 205, 331, '1', 3),
	(204, 'Budget Report', '2', '', 61, 318, '1', 4),
	(205, 'Accounts Payable Reports', '2', '', 61, 0, '1', 3),
	(176, 'Search Stocks Transfer', '3', '', 169, 266, '1', 0),
	(177, 'Search Issuance', '3', '', 171, 267, '1', 0),
	(178, 'Production', '2', '', 156, 0, '1', 4),
	(180, 'New Production', '3', '', 178, 268, '1', 0),
	(182, 'Search Production', '3', '', 178, 270, '1', 0),
	(184, 'Work Category', '2', '', 39, 273, '1', 4),
	(191, 'Purchase Request | Not Budgeted', '3', '', 157, 282, '1', 4),
	(190, 'Search Service Payments', '3', '', 188, 281, '1', 0),
	(181, 'Search Inventory Adjustment', '3', '', 143, 231, '1', 0),
	(187, 'Search Service Receiving', '3', '', 185, 277, '1', 0),
	(188, 'Service Payments', '2', '', 156, 0, '0', 8),
	(189, 'New Service Payments', '3', '', 188, 278, '1', 0),
	(153, 'Projects', '2', '', 39, 251, '1', 3),
	(154, 'Budget', '2', '', 39, 0, '1', 5),
	(155, 'New Budget', '3', '', 154, 249, '1', 0),
	(156, 'Transactions', '1', 'newspaper.png', 0, 0, '1', 0),
	(157, 'Purchase Request', '2', '', 156, 0, '1', 1),
	(158, 'New Purchase Request', '3', '', 157, 253, '1', 0),
	(159, 'Search Purchase Request', '3', '', 157, 254, '1', 1),
	(160, 'Purchase Request Approval', '3', '', 157, 255, '1', 0),
	(162, 'Purchase Order', '2', '', 156, 0, '1', 2),
	(202, 'Accounts', '2', '', 39, 68, '1', 0),
	(216, 'Search PO for APV', '3', '', 193, 345, '0', 1),
	(209, 'Financial Budget', '2', '', 39, 0, '1', 7),
	(210, 'New Financial Budget', '3', '', 209, 334, '1', 0),
	(211, 'Search Financial Budget', '3', '', 209, 335, '1', 0),
	(212, 'Warehouse Inventory Balance Report', '3', '', 62, 338, '1', 0),
	(213, 'Project Inventory Balance Report', '3', '', 62, 340, '1', 2),
	(214, 'Cash Advance', '2', '', 156, 341, '1', 12),
	(217, 'Product Master Limited', '2', '', 39, 346, '1', 0),
	(218, 'Product Issuance Statistics Report', '2', '', 61, 348, '1', 7),
	(219, 'Search Check Voucher', '3', '', 193, 350, '1', 3),
	(220, 'Disbursement Voucher', '2', '', 156, 351, '1', 0),
	(221, 'MRR History Report', '3', '', 316, 354, '1', 8),
	(222, 'Outstanding PR Report', '2', '', 61, 357, '1', 9),
	(223, 'Outstanding PO Report', '2', '', 61, 359, '1', 10),
	(224, 'Stock Transfer History Report', '3', '', 316, 463, '1', 11),
	(225, 'Issuance History Report', '3', '', 316, 363, '1', 12),
	(226, 'Aggregates Issuance History', '3', '', 316, 365, '1', 13),
	(227, 'SubCon/Special PO', '3', '', 162, 370, '1', 4),
	(228, 'PO History Report', '3', '', 316, 372, '1', 14),
	(229, 'Labor Materials PO', '3', '', 162, 375, '0', 5),
	(230, 'Stock Card Report MCD Warehouse', '3', '', 62, 384, '1', 5),
	(231, 'Cash Receipts', '3', '', 52, 385, '1', 0),
	(232, 'Equipment', '2', '', 39, 387, '1', 0),
	(233, 'Bank Reconcilation Report', '2', '', 61, 388, '1', 15),
	(235, 'Witholding Tax Report', '2', '', 61, 395, '1', 17),
	(236, 'Aggregates Income Statement', '3', '', 103, 422, '1', 18),
	(237, 'AP Subcontractor Balance Report', '2', '', 61, 427, '1', 19),
	(238, 'Purchase Request History', '3', '', 316, 434, '1', 20),
	(239, 'Supplier Ledger', '3', '', 103, 437, '1', 0),
	(240, 'Subcontractor APV', '3', '', 193, 439, '1', 0),
	(241, 'PHP Info', '2', '', 3, 440, '1', 4),
	(242, 'Stocks Receiving Unlock Module', '3', '', 164, 447, '1', 0),
	(243, 'DPRC', '2', 'book.png', 39, 0, '0', 100),
	(244, 'DPRC Customers', '3', '', 243, 449, '0', 2),
	(245, 'DPRC Subdivisions', '3', '', 243, 451, '0', 4),
	(246, 'DPRC Models', '3', '', 243, 450, '0', 3),
	(247, 'DPRC Applications', '3', '', 243, 452, '0', 1),
	(248, 'Budget Monitoring Report', '2', '', 61, 455, '1', 21),
	(249, 'Accountability Receipt', '2', '', 156, 457, '1', 0),
	(250, 'MRR ASSET BEG. BAL.', '3', '', 164, 464, '1', 0),
	(254, 'Employees', '3', '', 261, 470, '1', 0),
	(255, 'Employee Records', '3', '', 258, 471, '1', 0),
	(256, 'DPRC Inventory', '3', '', 243, 473, '0', 0),
	(257, 'DPRC A/P', '3', '', 243, 475, '0', 0),
	(258, 'Payroll Report', '2', '', 61, 0, '1', 0),
	(259, 'Payroll Transactions', '2', '', 156, 0, '1', 0),
	(260, 'DTR', '3', '', 259, 476, '1', 0),
	(261, 'Employees Masterfiles', '2', '', 39, 0, '1', 0),
	(263, 'Cash Receipts History Report', '3', '', 316, 480, '1', 0),
	(264, 'Generate Semi Monthly Payroll', '3', '', 259, 485, '1', 0),
	(265, 'Payroll Holidays', '3', '', 259, 484, '1', 0),
	(266, 'EUR', '3', '', 304, 486, '1', 0),
	(267, 'EUR Unit', '3', '', 304, 489, '1', 0),
	(268, 'EUR Reference', '3', '', 304, 488, '1', 0),
	(270, 'EUR Incentives', '3', '', 269, 490, '1', 0),
	(271, 'APV History Report', '3', '', 316, 497, '1', 0),
	(273, 'EUR Summary', '3', '', 269, 499, '1', 0),
	(275, 'Post RIS to GL', '2', '', 156, 501, '1', 0),
	(276, 'Biometrics DTR', '3', '', 259, 502, '1', 4),
	(277, 'RTP Monitoring', '3', '', 157, 503, '1', 0),
	(279, 'Advances Report', '3', '', 103, 510, '1', 0),
	(282, 'Schedule of Project Allowances', '3', '', 258, 515, '1', 0),
	(283, 'HE Income Statement', '3', '', 269, 518, '1', 0),
	(284, 'DTR Entries', '3', '', 258, 520, '1', 3),
	(285, 'Generate Payslip', '3', '', 258, 522, '1', 0),
	(286, 'Payroll Summary Report', '3', '', 258, 523, '1', 0),
	(287, 'RTP Monitoring History', '3', '', 316, 525, '1', 0),
	(288, 'Total Expeses Report', '2', '', 61, 527, '1', 0),
	(289, 'Transaction History', '3', '', 316, 529, '1', 0),
	(290, 'HE PO EUR Balance', '3', '', 269, 533, '1', 0),
	(292, 'Subclassification', '2', '', 91, 534, '1', 0),
	(293, 'Official Business Log book', '3', '', 259, 535, '1', 0),
	(294, 'Official Business Report', '3', '', 258, 536, '1', 0),
	(295, 'CV Printing Administration', '2', '', 156, 542, '1', 0),
	(296, 'Audit of Accountables', '2', '', 61, 543, '1', 0),
	(297, 'Audit Report', '2', '', 61, 545, '1', 0),
	(300, 'Job Order', '3', '', 304, 560, '1', 0),
	(301, 'Equipment History Report', '3', '', 269, 558, '1', 0),
	(302, 'Budget Details', '3', '', 154, 555, '1', 3),
	(303, 'Contributions Report', '3', '', 258, 562, '1', 0),
	(305, 'Vehicle Pass', '3', '', 304, 563, '1', 0),
	(306, 'Vehicle Pass Report', '3', '', 269, 565, '1', 0),
	(307, 'Petty Cash', '2', '', 156, 567, '1', 23),
	(308, 'Petty Cash Approval', '2', '', 156, 568, '1', 24),
	(309, 'Tardiness Summary', '3', '', 258, 570, '1', 0),
	(310, 'Premix Delivery', '3', '', 342, 574, '1', 0),
	(311, 'Premix Delivery Statement of Account', '3', '', 280, 575, '1', 0),
	(312, 'Accountability Receipt History', '3', '', 316, 576, '1', 0),
	(313, 'Work Type', '2', '', 39, 588, '1', 16),
	(314, 'Labor Budget', '3', '', 154, 591, '1', 4),
	(315, 'Purchase Request | Labor', '3', '', 157, 593, '1', 5),
	(316, 'History', '2', '', 61, 0, '1', 0),
	(317, 'Job Order History per Eqpt. Category', '3', '', 269, 600, '1', 0),
	(318, 'Mechanic Accomplishment Detail', '3', '', 269, 607, '1', 0),
	(319, 'Change Oil History', '3', '', 269, 603, '1', 0),
	(320, 'Retained Units Report', '3', '', 269, 605, '1', 0),
	(321, 'Rando Change Oil History', '3', '', 269, 599, '1', 0),
	(322, 'Admin Labor PO', '3', '', 162, 608, '1', 5),
	(323, 'Generate Weekly Payroll', '3', '', 259, 609, '1', 0),
	(324, 'Premix Quotation', '3', '', 342, 610, '1', 0),
	(326, 'KM Report', '3', '', 269, 613, '1', 12),
	(328, 'Maintenance Alert', '3', '', 304, 615, '1', 6),
	(329, 'Jobs', '2', '', 39, 618, '0', 17),
	(330, 'Job Order History Filter per Job', '3', '', 269, 622, '1', 0),
	(331, 'Part File List', '3', '', 269, 624, '1', 14),
	(337, 'Sales Returns', '3', '', 339, 635, '1', 0),
	(338, 'Purchase Returns', '3', '', 339, 637, '1', 0),
	(339, 'Returns', '2', '', 156, 0, '1', 0),
	(340, 'Gatepass', '2', '', 156, 639, '1', 0),
	(343, 'Admin Payroll Summary', '2', '', 61, 642, '1', 24),
	(344, 'Waste Cut Inventory Balance', '3', '', 333, 645, '1', 0),
	(345, 'Raw Mats Usage', '3', '', 333, 646, '1', 0),
	(346, 'SubCon PO Summary', '2', '', 61, 648, '1', 25),
	(348, 'Encoding History Report', '3', '', 316, 651, '1', 0),
	(365, 'Trial Balance Report', '3', '', 103, 736, '1', 9),
	(351, 'Transmittal Log History Report', '3', '', 316, 664, '1', 4),
	(353, 'Stocks Return History Report', '3', '', 316, 667, '1', 6),
	(354, 'Tire Branding History', '3', '', 269, 675, '0', 14),
	(355, 'Tire Branding History As Of Date', '3', '', 269, 672, '0', 16),
	(356, 'Tire Search', '3', '', 304, 671, '0', 6),
	(358, 'Tire Transfer', '3', '', 304, 677, '0', 7),
	(359, 'Tire List Per Equipment', '3', '', 269, 688, '0', 16),
	(360, 'Tire List Report', '3', '', 269, 689, '0', 17),
	(361, 'Junk Tire List Report', '3', '', 269, 692, '0', 18),
	(362, 'Tire List Per Equipment Installed', '3', '', 269, 694, '0', 19),
	(363, 'Project Type', '2', '', 39, 696, '1', 11),
	(364, 'Transmittal Log', '3', '', 169, 663, '1', 3),
	(366, 'Parent Account Breakdown', '3', '', 103, 701, '1', 10),
	(367, 'Premix Deliverry History Report', '3', '', 280, 703, '1', 3),
	(368, 'New Balance Sheet', '3', '', 103, 705, '1', 11),
	(369, 'New Income Statement', '3', '', 103, 714, '1', 12),
	(370, 'Contracts', '2', '', 39, 0, '1', 7),
	(381, 'Official Leave Request', '3', '', 259, 741, '1', 6),
	(374, 'Rank and File', '3', '', 370, 716, '1', 1),
	(375, 'Technical Staff - Project', '3', '', 370, 715, '1', 2),
	(376, 'On-Call', '3', '', 370, 717, '1', 3),
	(378, 'Admin-Laborer', '3', '', 370, 719, '1', 4),
	(380, 'Search SubCon PO for APV', '3', '', 162, 738, '1', 0),
	(383, 'General Ledger Listing - Limited', '3', '', 103, 745, '1', 14),
	(384, 'CV History', '3', '', 316, 747, '1', 15),
	(386, 'CA Adjustments', '3', '', 316, 756, '1', 16),
	(387, 'APV Report', '3', '', 103, 759, '1', 15),
	(388, 'Subcon APV Report', '3', '', 103, 761, '1', 16),
	(389, 'Income Statement - Project', '3', '', 103, 763, '1', 15),
	(391, 'New Cancellation Order', '3', '', 390, 766, '1', 1),
	(392, 'Search Cancellation Order', '3', '', 390, 765, '1', 2),
	(393, 'Accounts Payable - Transactions', '3', '', 205, 767, '1', 4),
	(394, 'Subcontractor Retention Report', '2', '', 61, 770, '1', 27),
	(395, 'Subcon PO Closed Out', '3', '', 162, 773, '1', 0),
	(397, 'Supplier Evaluation', '2', '', 61, 777, '1', 28),
	(398, 'PO Search Limited', '3', '', 162, 780, '1', 0);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;


-- Dumping structure for table default_db.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.messages: 0 rows
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;


-- Dumping structure for table default_db.model
CREATE TABLE IF NOT EXISTS `model` (
  `model_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `subd_id` bigint(12) NOT NULL,
  `model` varchar(100) NOT NULL,
  `package_type` int(10) NOT NULL,
  `lot_price` decimal(12,2) NOT NULL,
  `floor_price` decimal(12,2) NOT NULL,
  `lot` varchar(20) NOT NULL,
  `block` varchar(20) NOT NULL,
  `phase` varchar(29) NOT NULL,
  `remarks` text,
  `model_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.model: ~0 rows (approximately)
/*!40000 ALTER TABLE `model` DISABLE KEYS */;
/*!40000 ALTER TABLE `model` ENABLE KEYS */;


-- Dumping structure for table default_db.my_functions
CREATE TABLE IF NOT EXISTS `my_functions` (
  `FID` varchar(200) NOT NULL,
  `Fname` varchar(200) NOT NULL,
  `PCode` int(10) NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`FID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.my_functions: 481 rows
/*!40000 ALTER TABLE `my_functions` DISABLE KEYS */;
INSERT INTO `my_functions` (`FID`, `Fname`, `PCode`, `date_modified`) VALUES
	('20100720-094909', 'edit_functions', 26, '2010-07-20 09:49:09'),
	('20100720-100708', 'save_functions', 26, '2010-07-20 10:07:08'),
	('20100720-100714', 'show_functions', 26, '2010-07-20 10:07:14'),
	('20100720-100721', 'deleteF', 26, '2010-07-20 10:07:21'),
	('20100720-102803', 'new_folderform', 62, '2010-07-20 10:28:03'),
	('20100720-102808', 'new_folder', 62, '2010-07-20 10:28:08'),
	('20100720-102815', 'edit_folderform', 62, '2010-07-20 10:28:15'),
	('20100720-102822', 'edit_folder', 62, '2010-07-20 10:28:22'),
	('20101004-100607', 'new_userform', 9, '2010-10-04 22:06:07'),
	('20101004-100616', 'new_user', 9, '2010-10-04 22:06:16'),
	('20101004-100627', 'edit_userform', 9, '2010-10-04 22:06:27'),
	('20101004-100633', 'edit_user', 9, '2010-10-04 22:06:33'),
	('20100720-104751', 'update_wall', 2, '2010-07-20 10:47:51'),
	('20100720-104758', 'postWall', 2, '2010-07-20 10:47:58'),
	('20100720-104803', 'update_impt', 2, '2010-07-20 10:48:03'),
	('20100720-104832', 'address_book', 61, '2010-07-20 10:48:32'),
	('20100720-104840', 'show_users', 61, '2010-07-20 10:48:40'),
	('20100720-104852', 'show_groups', 61, '2010-07-20 10:48:52'),
	('20100720-105356', 'new_groupform', 22, '2010-07-20 10:53:56'),
	('20100720-105409', 'new_group', 22, '2010-07-20 10:54:09'),
	('20100720-105418', 'edit_groupform', 22, '2010-07-20 10:54:18'),
	('20100720-105428', 'edit_group', 22, '2010-07-20 10:54:28'),
	('20100720-105504', 'addUsersToGroup', 22, '2010-07-20 10:55:04'),
	('20100720-105638', 'showUsersGroup', 22, '2010-07-20 10:56:38'),
	('20100720-105647', 'saveSelectedUsers', 22, '2010-07-20 10:56:47'),
	('20100720-105656', 'showSelectedUsers', 22, '2010-07-20 10:56:56'),
	('20100720-105704', 'deleteGM', 22, '2010-07-20 10:57:04'),
	('20100720-110129', 'putGM', 61, '2010-07-20 11:01:29'),
	('20100720-110420', 'new_menuform', 24, '2010-07-20 11:04:20'),
	('20100720-110434', 'new_menu', 24, '2010-07-20 11:04:34'),
	('20100720-110440', 'edit_menuform', 24, '2010-07-20 11:04:40'),
	('20100720-110452', 'edit_menu', 24, '2010-07-20 11:04:52'),
	('20100720-110626', 'new_privilegesform', 25, '2010-07-20 11:06:26'),
	('20100720-110640', 'new_privileges', 25, '2010-07-20 11:06:40'),
	('20100720-110647', 'edit_privilegesform', 25, '2010-07-20 11:06:47'),
	('20100720-110653', 'edit_privileges', 25, '2010-07-20 11:06:53'),
	('20100720-110757', 'addPToGroup', 25, '2010-07-20 11:07:57'),
	('20100720-110811', 'showFiles', 25, '2010-07-20 11:08:11'),
	('20100720-110822', 'saveSelectedFiles', 25, '2010-07-20 11:08:22'),
	('20100720-110834', 'showSelectedFiles', 25, '2010-07-20 11:08:34'),
	('20100720-110851', 'deleteFile', 25, '2010-07-20 11:08:51'),
	('20100720-111201', 'view_recipients', 21, '2010-07-20 11:12:01'),
	('20100720-111508', 'read_pm', 20, '2010-07-20 11:15:08'),
	('20100720-111524', 'show_attachments', 20, '2010-07-20 11:15:24'),
	('20100720-111630', 'show_attachments', 21, '2010-07-20 11:16:30'),
	('20110216-091416', 'getCategory2Options', 66, '2011-02-16 09:14:16'),
	('20110215-114249', 'new_location', 70, '2011-02-15 23:42:49'),
	('20110215-114047', 'new_supplier', 75, '2011-02-15 23:40:47'),
	('20110215-114041', 'new_supplierform', 75, '2011-02-15 23:40:41'),
	('20110215-113242', 'new_account', 68, '2011-02-15 23:32:42'),
	('20110215-113238', 'new_accountform', 68, '2011-02-15 23:32:38'),
	('20110215-114247', 'new_locationform', 70, '2011-02-15 23:42:47'),
	('20110328-120053', 'display_joborderdetail', 83, '2011-03-28 12:00:53'),
	('20110215-090701', 'showMyDiv', 66, '2011-02-15 21:07:01'),
	('20110215-082234', 'new_productmasterform', 66, '2011-02-15 20:22:34'),
	('20100803-114824', 'show_clients', 73, '2010-08-03 23:48:24'),
	('20100804-110057', 'preview_score', 73, '2010-08-04 23:00:57'),
	('20100804-110219', 'update_scorecard', 73, '2010-08-04 23:02:19'),
	('20100810-094214', 'select_to_manage', 9, '2010-08-10 21:42:14'),
	('20101004-100430', 'save_newPass', 9, '2010-10-04 22:04:30'),
	('20110215-110508', 'new_categoriesform', 67, '2011-02-15 23:05:08'),
	('20110216-092143', 'getCategory3Options', 66, '2011-02-16 09:21:43'),
	('20110216-092148', 'getCategory4Options', 66, '2011-02-16 09:21:48'),
	('20110216-104140', 'new_categories', 67, '2011-02-16 10:41:40'),
	('20110216-104540', 'new_productmaster', 66, '2011-02-16 10:45:40'),
	('20110216-121711', 'new_purchaseheaderform', 74, '2011-02-16 12:17:11'),
	('20110216-121715', 'new_purchaseheader', 74, '2011-02-16 12:17:15'),
	('20110216-122356', 'edit_productmaster', 66, '2011-02-16 12:23:56'),
	('20110216-122400', 'edit_productmasterform', 66, '2011-02-16 12:24:00'),
	('20110216-122425', 'edit_categoriesform', 67, '2011-02-16 12:24:25'),
	('20110216-122428', 'edit_categories', 67, '2011-02-16 12:24:28'),
	('20110216-122447', 'edit_account', 68, '2011-02-16 12:24:47'),
	('20110216-122450', 'edit_accountform', 68, '2011-02-16 12:24:50'),
	('20110216-122510', 'edit_supplier', 75, '2011-02-16 12:25:10'),
	('20110216-122514', 'edit_supplierform', 75, '2011-02-16 12:25:14'),
	('20110216-122550', 'edit_location', 70, '2011-02-16 12:25:50'),
	('20110216-122553', 'edit_locationform', 70, '2011-02-16 12:25:53'),
	('20110307-065747', 'new_brandform', 80, '2011-03-07 18:57:47'),
	('20110307-065751', 'new_brand', 80, '2011-03-07 18:57:51'),
	('20110307-065757', 'edit_brandform', 80, '2011-03-07 18:57:57'),
	('20110307-065802', 'edit_brand', 80, '2011-03-07 18:58:02'),
	('20110307-102306', 'show_formulation', 81, '2011-03-07 22:23:06'),
	('20110308-122951', 'print_formulation', 81, '2011-03-08 00:29:51'),
	('20110310-010331', 'edit_formulation', 81, '2011-03-10 13:03:31'),
	('20110310-110834', 'delete_formulationdetail', 81, '2011-03-10 23:08:34'),
	('20110310-112644', 'display_formulationdetail', 81, '2011-03-10 23:26:44'),
	('20110310-114843', 'add_formulationdetail', 81, '2011-03-10 23:48:43'),
	('20110317-032938', 'changekeywordfield', 81, '2011-03-17 15:29:38'),
	('20110317-085011', 'duplicateformulation', 81, '2011-03-17 20:50:11'),
	('20110317-090901', 'display_formulationdetailtable', 81, '2011-03-17 21:09:01'),
	('20110317-092606', 'factorquantity', 81, '2011-03-17 21:26:06'),
	('20110317-094951', 'duplicateformulationform', 81, '2011-03-17 21:49:51'),
	('20110327-115702', 'getCategory2Options', 78, '2011-03-27 23:57:02'),
	('20110327-115711', 'getCategory3Options', 78, '2011-03-27 23:57:11'),
	('20110327-115713', 'getCategory4Options', 78, '2011-03-27 23:57:13'),
	('20110328-113721', 'show_joborder', 83, '2011-03-28 11:37:21'),
	('20110328-120824', 'edit_joborder', 83, '2011-03-28 12:08:24'),
	('20110328-120839', 'add_joborderdetail', 83, '2011-03-28 12:08:39'),
	('20110328-120848', 'delete_joborderdetail', 83, '2011-03-28 12:08:48'),
	('20110401-102018', 'new_package', 85, '2011-04-01 22:20:18'),
	('20110401-102020', 'new_packageform', 85, '2011-04-01 22:20:20'),
	('20110401-102024', 'edit_packageform', 85, '2011-04-01 22:20:24'),
	('20110401-102026', 'edit_package', 85, '2011-04-01 22:20:26'),
	('20110401-115240', 'changedetaildiv', 69, '2011-04-01 23:52:40'),
	('20110402-011405', 'changedetaildiv', 83, '2011-04-02 01:14:05'),
	('20110402-011357', 'updateheader', 83, '2011-04-02 01:13:57'),
	('20110408-090112', 'addformulationdetail', 69, '2011-04-08 21:01:12'),
	('20110408-093340', 'displayformulationonchange', 69, '2011-04-08 21:33:40'),
	('20110408-105245', 'display_joborderformulation', 83, '2011-04-08 22:52:45'),
	('20110408-114217', 'print_joborder', 83, '2011-04-08 23:42:17'),
	('20110414-020558', 'print_formulation', 86, '2011-04-14 14:05:58'),
	('20110418-040405', 'displayformulationdetails', 69, '2011-04-18 16:04:05'),
	('20110418-051947', 'displayformulationdetails', 88, '2011-04-18 17:19:47'),
	('20110418-070927', 'updateheader', 88, '2011-04-18 19:09:27'),
	('20110419-060627', 'jofinishedproductonchange', 69, '2011-04-19 18:06:27'),
	('20110419-060632', 'jofinishedproductonchange', 88, '2011-04-19 18:06:32'),
	('20110419-062203', 'displayformulationonchange', 88, '2011-04-19 18:22:03'),
	('20110426-100941', 'new_deliveryform', 89, '2011-04-26 22:09:41'),
	('20110426-100946', 'new_delivery', 89, '2011-04-26 22:09:46'),
	('20110426-100951', 'edit_delivery', 89, '2011-04-26 22:09:51'),
	('20110426-100955', 'edit_deliveryform', 89, '2011-04-26 22:09:55'),
	('20110506-072742', 'print_delivery', 89, '2011-05-06 19:27:42'),
	('20111105-113445', 'getSRP', 98, '2011-11-05 23:34:45'),
	('20110519-111317', 'print_delivery', 97, '2011-05-19 23:13:17'),
	('20110520-124535', 'updateStatus', 97, '2011-05-20 00:45:35'),
	('20110530-124228', 'print_rr', 101, '2011-05-30 00:42:28'),
	('20110530-125744', 'updateRRStatus', 101, '2011-05-30 00:57:44'),
	('20110530-010533', 'updateJOStatus', 83, '2011-05-30 01:05:33'),
	('20110604-060030', 'updateStocksTransferStatus', 107, '2011-06-04 18:00:30'),
	('20110604-060046', 'print_stockstransfer', 107, '2011-06-04 18:00:46'),
	('20110605-034403', 'print_productconversion', 111, '2011-06-05 15:44:03'),
	('20110605-035002', 'updateProductConversionStatus', 111, '2011-06-05 15:50:02'),
	('20110615-035636', 'print_stockcard', 115, '2011-06-15 15:56:36'),
	('20110719-082243', 'new_chartofaccounts', 134, '2011-07-19 20:22:43'),
	('20110719-082245', 'new_chartofaccountsform', 134, '2011-07-19 20:22:45'),
	('20110719-082249', 'edit_chartofaccountsform', 134, '2011-07-19 20:22:49'),
	('20110719-082252', 'edit_chartofaccounts', 134, '2011-07-19 20:22:52'),
	('20110719-090922', 'new_journalform', 135, '2011-07-19 21:09:22'),
	('20110719-090927', 'new_journal', 135, '2011-07-19 21:09:27'),
	('20110719-090932', 'edit_journal', 135, '2011-07-19 21:09:32'),
	('20110719-090935', 'edit_journalform', 135, '2011-07-19 21:09:35'),
	('20110720-075217', 'printGLTransac', 137, '2011-07-20 19:52:17'),
	('20110720-075228', 'updateGLTransacStatus', 137, '2011-07-20 19:52:28'),
	('20110901-092731', 'print_journal', 135, '2011-09-01 09:27:31'),
	('20110907-082801', 'importTransactions', 136, '2011-09-07 20:28:01'),
	('20110907-095117', 'refreshGLTable', 136, '2011-09-07 21:51:17'),
	('20110907-101944', 'addTransaction', 136, '2011-09-07 22:19:44'),
	('20110907-103130', 'removeParent', 136, '2011-09-07 22:31:30'),
	('20110911-092651', 'refreshGLTable', 138, '2011-09-11 09:26:51'),
	('20110911-092231', 'addTransaction', 138, '2011-09-11 09:22:31'),
	('20110911-092235', 'removeParent', 138, '2011-09-11 09:22:35'),
	('20110911-092246', 'importTransactions', 138, '2011-09-11 09:22:46'),
	('20111103-094217', 'addPODetails', 165, '2011-11-03 21:42:17'),
	('20111103-094224', 'removePODetails', 165, '2011-11-03 21:42:24'),
	('20111103-094231', 'refreshPODetails', 165, '2011-11-03 21:42:31'),
	('20111103-100929', 'addPODetails', 166, '2011-11-03 22:09:29'),
	('20111103-100935', 'removePODetails', 166, '2011-11-03 22:09:35'),
	('20111103-100942', 'refreshPODetails', 166, '2011-11-03 22:09:42'),
	('20111103-102029', 'print_po', 167, '2011-11-03 22:20:29'),
	('20111103-102034', 'updatePOStatus', 167, '2011-11-03 22:20:34'),
	('20111105-085648', 'displayCurrentBalance', 89, '2011-11-05 20:56:48'),
	('20111105-090948', 'getSRP', 89, '2011-11-05 21:09:48'),
	('20111105-094312', 'addDRDetails', 89, '2011-11-05 21:43:12'),
	('20111105-111117', 'refreshDR', 89, '2011-11-05 23:11:17'),
	('20111105-111123', 'removeDRDetail', 89, '2011-11-05 23:11:23'),
	('20111105-113450', 'addDRDetails', 98, '2011-11-05 23:34:50'),
	('20111105-113454', 'refreshDR', 98, '2011-11-05 23:34:54'),
	('20111105-113459', 'removeDRDetail', 98, '2011-11-05 23:34:59'),
	('20111105-113520', 'addDRReturns', 98, '2011-11-05 23:35:20'),
	('20111105-113530', 'addDRAdjustments', 98, '2011-11-05 23:35:30'),
	('20111105-114240', 'displayCurrentBalance', 98, '2011-11-05 23:42:40'),
	('20111106-085116', 'po_getCostOfStock', 165, '2011-11-06 20:51:16'),
	('20111106-085121', 'po_getCostOfStock', 166, '2011-11-06 20:51:21'),
	('20111106-102558', 'getPODetails', 99, '2011-11-06 22:25:58'),
	('20111106-112412', 'getRRTable', 99, '2011-11-06 23:24:12'),
	('20111107-093844', 'getPODetails', 100, '2011-11-07 09:38:44'),
	('20111107-093851', 'getRRTable', 100, '2011-11-07 09:38:51'),
	('20111126-093723', 'displayPackageField', 165, '2011-11-26 21:37:23'),
	('20111126-093734', 'displayPackageField', 166, '2011-11-26 21:37:34'),
	('20111126-094742', 'solvePODetails', 165, '2011-11-26 21:47:42'),
	('20111126-094748', 'solvePODetails', 166, '2011-11-26 21:47:48'),
	('20111130-031646', 'displayProductConvertQty', 109, '2011-11-30 15:16:46'),
	('20111130-031652', 'displayProductConvertQty', 110, '2011-11-30 15:16:52'),
	('20111130-032701', 'computeConvertQuantity', 109, '2011-11-30 15:27:01'),
	('20111130-032705', 'computeConvertQuantity', 110, '2011-11-30 15:27:05'),
	('20120106-053319', 'po_getCostOfStock', 184, '2012-01-06 17:33:19'),
	('20120106-053329', 'solvePODetails', 184, '2012-01-06 17:33:29'),
	('20120106-054340', 'displayPackageField', 184, '2012-01-06 17:43:40'),
	('20120106-054646', 'getRRTable', 184, '2012-01-06 17:46:46'),
	('20120106-055159', 'addRRDetails', 184, '2012-01-06 17:51:59'),
	('20120106-061548', 'removeRRDetails', 184, '2012-01-06 18:15:48'),
	('20120110-053511', 'print_order', 186, '2012-01-10 17:35:11'),
	('20120110-053519', 'removeOrderDetails', 186, '2012-01-10 17:35:19'),
	('20120110-053526', 'getUpdatedOrderTable', 186, '2012-01-10 17:35:26'),
	('20120110-053532', 'addOrderDetails', 186, '2012-01-10 17:35:32'),
	('20120110-053538', 'getSRPOfStock', 186, '2012-01-10 17:35:38'),
	('20120111-084756', 'getSRP', 188, '2012-01-11 08:47:56'),
	('20120111-084802', 'addDRDetails', 188, '2012-01-11 08:48:02'),
	('20120111-084809', 'refreshDR', 188, '2012-01-11 08:48:09'),
	('20120111-084814', 'removeDRDetail', 188, '2012-01-11 08:48:14'),
	('20120111-011512', 'getUpdatedOrderTable', 188, '2012-01-11 13:15:12'),
	('20120120-112034', 'removeFormulationDetail', 79, '2012-01-20 23:20:34'),
	('20120120-112041', 'getFormulationTable', 79, '2012-01-20 23:20:41'),
	('20120120-112053', 'addFormulationDetail', 79, '2012-01-20 23:20:53'),
	('20120121-120610', 'addFormulationForm', 78, '2012-01-21 12:06:10'),
	('20120121-031000', 'addFormulationHeaderPM', 78, '2012-01-21 15:10:00'),
	('20120121-031008', 'addFormulationDetailPM', 78, '2012-01-21 15:10:08'),
	('20120121-034508', 'getDialogFormulationTable', 78, '2012-01-21 15:45:08'),
	('20120121-034528', 'updateFormulationHeaderPM', 78, '2012-01-21 15:45:28'),
	('20120121-104643', 'removeFormulationDetailPM', 78, '2012-01-21 22:46:43'),
	('20120123-012145', 'editFormulationForm', 78, '2012-01-23 13:21:45'),
	('20120123-013346', 'updateFormulation', 78, '2012-01-23 13:33:46'),
	('20120123-022143', 'computeOrderAmount', 186, '2012-01-23 14:21:43'),
	('20120127-052802', 'getDeliveryDetailsForStockReturns', 201, '2012-01-27 17:28:02'),
	('20120128-065750', 'getOrderDetailsForJO', 203, '2012-01-28 18:57:50'),
	('20120131-102741', 'table_orderForDR', 188, '2012-01-31 22:27:41'),
	('20120202-101048', 'deliveryStatus', 188, '2012-02-02 22:10:48'),
	('20120203-052553', 'getTotalOrders', 210, '2012-02-03 17:25:53'),
	('20120205-084507', 'getBeginningBalance', 210, '2012-02-05 20:45:07'),
	('20120205-085516', 'solveRequired', 210, '2012-02-05 20:55:16'),
	('20120206-041353', 'getPriceListOfStock', 186, '2012-02-06 16:13:53'),
	('20120212-094220', 'addPReturnDetails', 228, '2012-02-12 09:42:20'),
	('20120212-094226', 'removePReturnDetails', 228, '2012-02-12 09:42:26'),
	('20120212-094232', 'refreshPReturnDetails', 228, '2012-02-12 09:42:32'),
	('20120212-072528', 'removeInventoryAdjustmentDetail', 230, '2012-02-12 19:25:28'),
	('20120212-072531', 'getUpdatedInventoryAdjustmentTable', 230, '2012-02-12 19:25:31'),
	('20120212-072537', 'addOrderDetails', 230, '2012-02-12 19:25:37'),
	('20120212-073058', 'addInventoryAdjustmentDetail', 230, '2012-02-12 19:30:58'),
	('20120329-064314', 'productionFormulationDetails', 268, '2012-03-29 18:43:14'),
	('20120325-051533', 'returns_stock_id', 232, '2012-03-25 17:15:33'),
	('20120325-051532', 'returns_stock_id_form', 232, '2012-03-25 17:15:32'),
	('20120301-104623', 'new_projectform', 251, '2012-03-01 22:46:23'),
	('20120301-110305', 'edit_projectform', 251, '2012-03-01 23:03:05'),
	('20120301-110310', 'edit_project', 251, '2012-03-01 23:03:10'),
	('20120301-111022', 'new_project', 251, '2012-03-01 23:10:22'),
	('20120319-084412', 'receive_stock_id_form', 184, '2012-03-19 20:44:12'),
	('20120319-090123', 'receive_stock_id', 184, '2012-03-19 21:01:23'),
	('20120325-015503', 'transfer_stock_id_form', 263, '2012-03-25 13:55:03'),
	('20120325-015504', 'transfer_stock_id', 263, '2012-03-25 13:55:04'),
	('20120325-030652', 'issuance_stock_id_form', 264, '2012-03-25 15:06:52'),
	('20120325-030654', 'issuance_stock_id', 264, '2012-03-25 15:06:54'),
	('20120331-095114', 'po_stock_id', 165, '2012-03-31 21:51:14'),
	('20120331-094627', 'getProjectFromPR', 165, '2012-03-31 21:46:27'),
	('20120331-095121', 'po_stock_id_form', 165, '2012-03-31 21:51:21'),
	('20120402-014259', 'getProjectFromPO', 184, '2012-04-02 13:42:59'),
	('20120403-115934', 'show_purchase_request', 165, '2012-04-03 11:59:34'),
	('20120403-123948', 'show_purchase_request_details', 165, '2012-04-03 12:39:48'),
	('20120403-011854', 'purchase_request_place_details', 165, '2012-04-03 13:18:54'),
	('20120403-053607', 'pr_stock_id_form', 253, '2012-04-03 17:36:07'),
	('20120410-083330', 'new_work_categoryform', 273, '2012-04-10 08:33:30'),
	('20120403-053610', 'pr_stock_id', 253, '2012-04-03 17:36:10'),
	('20120410-083331', 'new_work_category', 273, '2012-04-10 08:33:31'),
	('20120410-083335', 'edit_work_category', 273, '2012-04-10 08:33:35'),
	('20120410-083336', 'edit_work_categoryform', 273, '2012-04-10 08:33:36'),
	('20120410-102712', 'display_subworkcategory', 249, '2012-04-10 10:27:12'),
	('20120412-041236', 'display_subworkcategory', 253, '2012-04-12 16:12:36'),
	('20120415-060413', 'search_purchase_request', 165, '2012-04-15 18:04:13'),
	('20120415-102208', 'display_subworkcategory', 264, '2012-04-15 22:22:08'),
	('20120416-122006', 'display_subworkcategory', 256, '2012-04-16 00:20:06'),
	('20120422-083600', 'update_scope_of_work', 253, '2012-04-22 20:36:00'),
	('20120422-083701', 'update_scope_of_work', 165, '2012-04-22 20:37:01'),
	('20120422-112550', 'search_po', 184, '2012-04-22 23:25:50'),
	('20120422-112556', 'show_po', 184, '2012-04-22 23:25:56'),
	('20120422-112604', 'show_po_details', 184, '2012-04-22 23:26:04'),
	('20120422-112615', 'po_place_details', 184, '2012-04-22 23:26:15'),
	('20120423-120950', 'update_scope_of_work', 264, '2012-04-23 00:09:50'),
	('20120423-103225', 'search_po', 276, '2012-04-23 10:32:25'),
	('20120423-103231', 'show_po', 276, '2012-04-23 10:32:31'),
	('20120423-103239', 'show_po_details', 276, '2012-04-23 10:32:39'),
	('20120423-103245', 'po_place_details', 276, '2012-04-23 10:32:45'),
	('20120423-112154', 'service_receive_stock_id_form', 276, '2012-04-23 11:21:54'),
	('20120423-112155', 'service_receive_stock_id', 276, '2012-04-23 11:21:55'),
	('20120423-023117', 'pr_service_stock_id_form', 253, '2012-04-23 14:31:17'),
	('20120423-023119', 'pr_service_stock_id', 253, '2012-04-23 14:31:19'),
	('20120423-034346', 'display_subworkcategory', 276, '2012-04-23 15:43:46'),
	('20120423-034351', 'update_scope_of_work', 276, '2012-04-23 15:43:51'),
	('20120423-072711', 'service_po_stock_id_form', 165, '2012-04-23 19:27:11'),
	('20120423-072527', 'service_po_stock_id', 165, '2012-04-23 19:25:27'),
	('20120503-080712', 'update_scope_of_work', 282, '2012-05-03 08:07:12'),
	('20120503-080826', 'display_subworkcategory', 282, '2012-05-03 08:08:26'),
	('20120503-091607', 'display_warehouse_qty', 282, '2012-05-03 09:16:07'),
	('20120503-110355', 'display_warehouse_qty', 253, '2012-05-03 11:03:55'),
	('20120503-040427', 'get_supplier_term', 165, '2012-05-03 16:04:27'),
	('20120504-121323', 'new_contractorform', 283, '2012-05-04 00:13:23'),
	('20120504-121324', 'new_contractor', 283, '2012-05-04 00:13:24'),
	('20120504-121327', 'edit_contractor', 283, '2012-05-04 00:13:27'),
	('20120504-121328', 'edit_contractorform', 283, '2012-05-04 00:13:28'),
	('20120504-074103', 'issue_to_form', 264, '2012-05-04 07:41:03'),
	('20120504-074105', 'issue_to', 264, '2012-05-04 07:41:05'),
	('20120504-103736', 'display_subworkcategory', 263, '2012-05-04 10:37:36'),
	('20120504-103742', 'update_scope_of_work', 263, '2012-05-04 10:37:42'),
	('20120504-104240', 'display_subworkcategory', 232, '2012-05-04 10:42:40'),
	('20120504-104243', 'update_scope_of_work', 232, '2012-05-04 10:42:43'),
	('20120505-113957', 'ap_form', 285, '2012-05-05 11:39:57'),
	('20120505-114001', 'ap_pay', 285, '2012-05-05 11:40:01'),
	('20120505-111803', 'ap_form_add', 284, '2012-05-05 23:18:03'),
	('20120505-113147', 'ap_pay_add', 284, '2012-05-05 23:31:47'),
	('20120506-070835', 'new_employeeform', 289, '2012-05-06 19:08:35'),
	('20120506-070836', 'new_employee', 289, '2012-05-06 19:08:36'),
	('20120506-070839', 'edit_employee', 289, '2012-05-06 19:08:39'),
	('20120506-070842', 'edit_employeeform', 289, '2012-05-06 19:08:42'),
	('20120507-104901', 'ar_form', 300, '2012-05-07 10:49:01'),
	('20120507-104908', 'ar_pay', 300, '2012-05-07 10:49:08'),
	('20120507-104915', 'ar_form_add', 300, '2012-05-07 10:49:15'),
	('20120507-104923', 'ar_pay_add', 300, '2012-05-07 10:49:23'),
	('20120507-020823', 'ar_pay_add', 301, '2012-05-07 14:08:23'),
	('20120507-020834', 'ar_form_add', 301, '2012-05-07 14:08:34'),
	('20120509-052105', 'new_account_typeform', 315, '2012-05-09 17:21:05'),
	('20120509-052106', 'new_account_type', 315, '2012-05-09 17:21:06'),
	('20120509-052111', 'edit_account_type', 315, '2012-05-09 17:21:11'),
	('20120509-052113', 'edit_account_typeform', 315, '2012-05-09 17:21:13'),
	('20120509-081037', 'new_sales_invoiceform', 316, '2012-05-09 20:10:37'),
	('20120509-081039', 'new_sales_invoice', 316, '2012-05-09 20:10:39'),
	('20120509-081041', 'edit_sales_invoice', 316, '2012-05-09 20:10:41'),
	('20120509-081042', 'edit_sales_invoiceform', 316, '2012-05-09 20:10:42'),
	('20120509-101918', 'sales_invoice_finish', 316, '2012-05-09 22:19:18'),
	('20120510-112802', 'new_sales_invoice_finish', 316, '2012-05-10 11:28:02'),
	('20120510-112813', 'edit_sales_invoice_finish', 316, '2012-05-10 11:28:13'),
	('20120510-112826', 'sales_invoice_payment_finish', 316, '2012-05-10 11:28:26'),
	('20120510-112838', 'sales_invoice_paymentform', 316, '2012-05-10 11:28:38'),
	('20120512-021453', 'pr_equipment_stock_id_form', 253, '2012-05-12 14:14:53'),
	('20120512-021455', 'pr_equipment_stock_id', 253, '2012-05-12 14:14:55'),
	('20120514-084117', 'equipment_po_stock_id_form', 165, '2012-05-14 08:41:17'),
	('20120514-084119', 'equipment_po_stock_id', 165, '2012-05-14 08:41:19'),
	('20120515-112255', 'show_po', 285, '2012-05-15 23:22:55'),
	('20120516-075430', 'apv_form', 332, '2012-05-16 07:54:30'),
	('20120516-081039', 'ap_pay', 332, '2012-05-16 08:10:39'),
	('20120517-044707', 'pr_fuel_stock_id_form', 253, '2012-05-17 16:47:07'),
	('20120517-044710', 'pr_fuel_stock_id', 253, '2012-05-17 16:47:10'),
	('20120529-033919', 'fuel_po_stock_id_form', 165, '2012-05-29 15:39:19'),
	('20120529-033922', 'fuel_po_stock_id', 165, '2012-05-29 15:39:22'),
	('20120611-111741', 'getCategory3Options', 66, '2012-06-11 23:17:41'),
	('20120611-111746', 'getCategory4Options', 78, '2012-06-11 23:17:46'),
	('20120612-084111', 'po_warehouse_stock_id_form', 165, '2012-06-12 20:41:11'),
	('20120612-084113', 'po_warehouse_stock_id', 165, '2012-06-12 20:41:13'),
	('20120612-094056', 'fuel_po_warehouse_stock_id', 165, '2012-06-12 21:40:56'),
	('20120612-094059', 'fuel_po_warehouse_stock_id_form', 165, '2012-06-12 21:40:59'),
	('20120619-075704', 'add_deduction_form', 316, '2012-06-19 07:57:04'),
	('20120705-102458', 'display_subworkcategory', 230, '2012-07-05 22:24:58'),
	('20120923-082754', 'display_subworkcategory', 370, '2012-09-23 20:27:54'),
	('20120916-053826', 'get_supplier_term', 370, '2012-09-16 17:38:26'),
	('20120916-061315', 'spo_form', 370, '2012-09-16 18:13:15'),
	('20120916-061317', 'spo', 370, '2012-09-16 18:13:17'),
	('20120923-082757', 'display_subworkcategory', 375, '2012-09-23 20:27:57'),
	('20120923-082804', 'get_supplier_term', 375, '2012-09-23 20:28:04'),
	('20120924-105558', 'display_subworkcategory', 258, '2012-09-24 22:55:58'),
	('20120929-100406', 'getDataFromInvoiceNo', 385, '2012-09-29 22:04:06'),
	('20121005-103117', 'displayTotalCheckAmount', 350, '2012-10-05 22:31:17'),
	('20121005-111626', 'new_equipmentform', 387, '2012-10-05 23:16:26'),
	('20121005-111627', 'new_equipment', 387, '2012-10-05 23:16:27'),
	('20121005-111632', 'edit_equipment', 387, '2012-10-05 23:16:32'),
	('20121005-111633', 'edit_equipmentform', 387, '2012-10-05 23:16:33'),
	('20121022-112252', 'checkPOAccount', 136, '2012-10-22 23:22:52'),
	('20121117-100055', 'display_subworkcategory', 184, '2012-11-17 22:00:55'),
	('20121117-101657', 'getFromBudget', 184, '2012-11-17 22:16:57'),
	('20121117-111516', 'displayBudgetDeductionForm', 184, '2012-11-17 23:15:16'),
	('20121118-021557', 'deductToBudget', 184, '2012-11-18 14:15:57'),
	('20121118-021600', 'updateDeductions', 184, '2012-11-18 14:16:00'),
	('20121118-115255', 'display_subworkcategory', 267, '2012-11-18 23:52:55'),
	('20121214-111324', 'computeAmortization', 452, '2012-12-14 11:13:24'),
	('20121220-073408', 'getCategory2Options', 340, '2012-12-20 19:34:08'),
	('20121220-073411', 'getCategory3Options', 340, '2012-12-20 19:34:11'),
	('20121220-073415', 'getCategory4Options', 340, '2012-12-20 19:34:15'),
	('20121228-100410', 'asset_details_form', 184, '2012-12-28 22:04:10'),
	('20121228-100416', 'update_asset_details', 184, '2012-12-28 22:04:16'),
	('20130106-014508', 'edit_employeesform', 470, '2013-01-06 13:45:08'),
	('20130106-014502', 'edit_employees', 470, '2013-01-06 13:45:02'),
	('20130106-014459', 'new_employees', 470, '2013-01-06 13:44:59'),
	('20130106-014451', 'new_employeesform', 470, '2013-01-06 13:44:51'),
	('20130110-071512', 'getDateForLoanLedger', 452, '2013-01-10 19:15:12'),
	('20130110-071516', 'getDateForStatementOfAccount', 452, '2013-01-10 19:15:16'),
	('20130110-084831', 'show_employees', 476, '2013-01-10 20:48:31'),
	('20130110-090625', 'show_employees', 477, '2013-01-10 21:06:25'),
	('20130110-095203', 'edit_dependentsform', 477, '2013-01-10 21:52:03'),
	('20130110-095209', 'edit_dependents', 477, '2013-01-10 21:52:09'),
	('20130121-094227', 'getLatestORSeries', 385, '2013-01-21 09:42:27'),
	('20130208-112250', 'display_subworkcategory', 434, '2013-02-08 23:22:50'),
	('20130208-112302', 'display_subworkcategory', 372, '2013-02-08 23:23:02'),
	('20130208-112314', 'display_subworkcategory', 354, '2013-02-08 23:23:14'),
	('20130208-112323', 'display_subworkcategory', 363, '2013-02-08 23:23:23'),
	('20130225-034000', 'display_subworkcategory', 455, '2013-02-25 15:40:00'),
	('20130227-113045', 'getUnitRate', 486, '2013-02-27 23:30:45'),
	('20130301-115227', 'display_subworkcategory', 497, '2013-03-01 23:52:27'),
	('20130310-060854', 'displayPOItem', 486, '2013-03-10 18:08:54'),
	('20130310-063623', 'getCategory2Options', 102, '2013-03-10 18:36:23'),
	('20130310-063626', 'getCategory4Options', 102, '2013-03-10 18:36:26'),
	('20130310-063627', 'getCategory3Options', 102, '2013-03-10 18:36:27'),
	('20130310-063638', 'getCategory2Options', 338, '2013-03-10 18:36:38'),
	('20130310-063641', 'getCategory3Options', 338, '2013-03-10 18:36:41'),
	('20130310-063642', 'getCategory4Options', 338, '2013-03-10 18:36:42'),
	('20130415-103958', 'display_subworkcategory', 503, '2013-04-15 22:39:58'),
	('20130424-102852', 'display_subworkcategory', 463, '2013-04-24 22:28:52'),
	('20130609-113402', 'display_subworkcategory', 525, '2013-06-09 23:34:02'),
	('20131008-080912', 'show_employees', 520, '2013-10-08 08:09:12'),
	('20131209-060639', 'new_sectionform', 556, '2013-12-09 18:06:39'),
	('20131209-060646', 'new_section', 556, '2013-12-09 18:06:46'),
	('20131209-060651', 'edit_sectionform', 556, '2013-12-09 18:06:51'),
	('20131209-060701', 'edit_section', 556, '2013-12-09 18:07:01'),
	('20131219-032704', 'add_details', 555, '2013-12-19 15:27:04'),
	('20131219-032723', 'save_details', 555, '2013-12-19 15:27:23'),
	('20131219-032732', 'show_details', 555, '2013-12-19 15:27:32'),
	('20131219-032738', 'deleteD', 555, '2013-12-19 15:27:38'),
	('20140113-091233', 'new_vehiclepassform', 563, '2014-01-13 21:12:33'),
	('20140113-091246', 'new_vehiclepass', 563, '2014-01-13 21:12:46'),
	('20140113-091312', 'edit_vehiclepass', 563, '2014-01-13 21:13:12'),
	('20140113-091315', 'edit_vehiclepassform', 563, '2014-01-13 21:13:15'),
	('20140122-034228', 'new_pcform', 567, '2014-01-22 15:42:28'),
	('20140122-034233', 'new_pc', 567, '2014-01-22 15:42:33'),
	('20140122-034239', 'edit_pcform', 567, '2014-01-22 15:42:39'),
	('20140122-034248', 'edit_pc', 567, '2014-01-22 15:42:48'),
	('20140122-034308', 'new_pcamtform', 568, '2014-01-22 15:43:08'),
	('20140122-034314', 'new_pcamt', 568, '2014-01-22 15:43:14'),
	('20140127-052940', 'liquidate_pcform', 568, '2014-01-27 17:29:40'),
	('20140127-052948', 'liquidate_pc', 568, '2014-01-27 17:29:48'),
	('20140209-023830', 'new_pcform_rjr', 579, '2014-02-09 14:38:30'),
	('20140209-023844', 'new_pc_rjr', 579, '2014-02-09 14:38:44'),
	('20140209-023900', 'edit_pcform_rjr', 579, '2014-02-09 14:39:00'),
	('20140209-023914', 'edit_pc_rjr', 579, '2014-02-09 14:39:14'),
	('20140209-023927', 'new_pcamtform_rjr', 579, '2014-02-09 14:39:27'),
	('20140209-023940', 'new_pcamt_rjr', 579, '2014-02-09 14:39:40'),
	('20140213-121411', 'new_worksection', 588, '2014-02-13 00:14:11'),
	('20140213-121421', 'new_workform', 588, '2014-02-13 00:14:21'),
	('20140213-121430', 'edit_workform', 588, '2014-02-13 00:14:30'),
	('20140213-121437', 'edit_work', 588, '2014-02-13 00:14:37'),
	('20140213-122846', 'display_subworkcategory', 590, '2014-02-13 00:28:46'),
	('20140213-122907', 'display_subworkcategory', 589, '2014-02-13 00:29:07'),
	('20140218-100745', 'display_subworkcategory', 592, '2014-02-18 10:07:45'),
	('20140218-100757', 'pr_labor_form', 592, '2014-02-18 10:07:57'),
	('20140218-100805', 'pr_labor', 592, '2014-02-18 10:08:05'),
	('20140218-100814', 'delete_labor_pr', 592, '2014-02-18 10:08:14'),
	('20140304-014241', 'display_subworkcategory', 597, '2014-03-04 13:42:41'),
	('20140509-074410', 'new_jobform', 618, '2014-05-09 19:44:10'),
	('20140509-074420', 'new_job', 618, '2014-05-09 19:44:20'),
	('20140509-074426', 'edit_jobform', 618, '2014-05-09 19:44:26'),
	('20140509-074433', 'edit_job', 618, '2014-05-09 19:44:33'),
	('20140509-074440', 'job_search', 618, '2014-05-09 19:44:40'),
	('20140509-074452', 'select_job', 618, '2014-05-09 19:44:52'),
	('20140625-115134', 'add_payroll', 608, '2014-06-25 23:51:34'),
	('20140625-115136', 'add_payrollform', 608, '2014-06-25 23:51:36'),
	('20140625-115149', 'display_subworkcategory', 608, '2014-06-25 23:51:49'),
	('20140723-031321', 'get_supplier_term', 608, '2014-07-23 03:13:21'),
	('20150119-100253', 'update', 249, '2015-01-19 22:02:53'),
	('20150119-021431', 'update_budget', 249, '2015-01-19 14:14:31'),
	('20150227-111430', 'new_contract', 470, '2015-02-27 11:14:30'),
	('20150227-111434', 'save_contract', 470, '2015-02-27 11:14:34'),
	('20150227-111437', 'deleteContract', 470, '2015-02-27 11:14:37'),
	('20150227-111440', 'show_contracts', 470, '2015-02-27 11:14:40'),
	('20150227-111444', 'EditContract', 470, '2015-02-27 11:14:44'),
	('20150227-111448', 'edit_contract', 470, '2015-02-27 11:14:48'),
	('20150307-014510', 'display_subworkcategory', 663, '2015-03-07 13:45:10'),
	('20150307-014524', 'update_scope_of_work', 663, '2015-03-07 13:45:24'),
	('20150307-014545', 'display_subworkcategory', 664, '2015-03-07 13:45:45'),
	('20150313-014752', 'new_tire_form', 666, '2015-03-13 13:47:52'),
	('20150313-014757', 'new_tire', 666, '2015-03-13 13:47:57'),
	('20150313-014804', 'edit_tire_form', 666, '2015-03-13 13:48:04'),
	('20150313-014808', 'edit_tire', 666, '2015-03-13 13:48:08'),
	('20150324-012330', 'display_subworkcategory', 667, '2015-03-24 13:23:30'),
	('20150422-023030', 'display_subworkcategory', 427, '2015-04-22 14:30:30'),
	('20150622-032530', 'edit_pc', 568, '2015-06-22 15:25:30'),
	('20150622-032533', 'edit_pcform', 568, '2015-06-22 15:25:33'),
	('20150914-114136', 'new_project_typeform', 696, '2015-09-14 11:41:36'),
	('20150914-114138', 'new_project_type', 696, '2015-09-14 11:41:38'),
	('20150914-114144', 'edit_project_typeform', 696, '2015-09-14 11:41:44'),
	('20150914-114147', 'edit_project_type', 696, '2015-09-14 11:41:47'),
	('20151022-041425', 'getBranding', 486, '2015-10-22 16:14:25'),
	('20160216-105159', 'new_acform', 749, '2016-02-16 10:51:59'),
	('20160216-105213', 'new_ac', 749, '2016-02-16 10:52:13'),
	('20160216-105221', 'edit_acform', 749, '2016-02-16 10:52:21'),
	('20160216-105228', 'edit_ac', 749, '2016-02-16 10:52:28'),
	('20160216-105234', 'display_subworkcategory', 749, '2016-02-16 10:52:34'),
	('20160216-105241', 'rec_itemform', 749, '2016-02-16 10:52:41'),
	('20160216-105249', 'rec_item', 749, '2016-02-16 10:52:49'),
	('20160216-105256', 'ret_itemform', 749, '2016-02-16 10:52:56'),
	('20160216-105301', 'ret_item', 749, '2016-02-16 10:53:01'),
	('20160222-024024', 'violations', 470, '2016-02-22 14:40:24'),
	('20160222-024038', 'save_violations', 470, '2016-02-22 14:40:38'),
	('20160222-024046', 'deleteViolations', 470, '2016-02-22 14:40:46'),
	('20160222-024058', 'show_violations', 470, '2016-02-22 14:40:58'),
	('20160222-024117', 'EditViolations', 470, '2016-02-22 14:41:17'),
	('20160222-024126', 'edit_violations', 470, '2016-02-22 14:41:26'),
	('20160627-024512', 'print_po', 780, '2016-06-27 14:45:12');
/*!40000 ALTER TABLE `my_functions` ENABLE KEYS */;


-- Dumping structure for table default_db.my_privileges
CREATE TABLE IF NOT EXISTS `my_privileges` (
  `id` varchar(200) NOT NULL,
  `PCode` int(20) NOT NULL,
  `access_type_ID` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.my_privileges: 1,480 rows
/*!40000 ALTER TABLE `my_privileges` DISABLE KEYS */;
INSERT INTO `my_privileges` (`id`, `PCode`, `access_type_ID`) VALUES
	('20100711-065146', 9, 2),
	('20100711-065144', 25, 2),
	('20100718-095250', 1, 2),
	('20110216-112539', 66, 2),
	('20110216-112551', 67, 2),
	('20110216-112604', 68, 2),
	('20110216-112614', 75, 2),
	('20110216-112625', 70, 2),
	('20110308-011155', 81, 2),
	('20110302-105622', 78, 2),
	('20120211-092737', 218, 2),
	('20110308-011151', 79, 2),
	('20110328-122756', 69, 2),
	('20110328-122802', 83, 2),
	('20110408-031932', 85, 2),
	('20110506-102348', 89, 2),
	('20120212-100917', 229, 2),
	('20110516-102424', 91, 2),
	('20110516-102427', 92, 2),
	('20110516-102430', 97, 2),
	('20110516-102432', 98, 2),
	('20110516-102447', 99, 2),
	('20110516-102449', 100, 2),
	('20110516-102452', 101, 2),
	('20110516-102512', 94, 2),
	('20120212-100817', 228, 2),
	('20110516-102544', 102, 2),
	('20110516-102548', 103, 2),
	('20110605-042443', 105, 2),
	('20110605-042449', 106, 2),
	('20110605-042453', 107, 2),
	('20110605-042508', 109, 2),
	('20110605-042511', 110, 2),
	('20110605-042514', 111, 2),
	('20110615-055230', 113, 2),
	('20110615-055231', 114, 2),
	('20110615-055249', 115, 2),
	('20110618-115238', 117, 2),
	('20110626-110404', 118, 2),
	('20110626-110405', 119, 2),
	('20110626-110406', 121, 2),
	('20110627-044830', 124, 2),
	('20110627-044835', 122, 2),
	('20110627-044836', 123, 2),
	('20110627-044841', 125, 2),
	('20110627-044849', 129, 2),
	('20110704-094915', 133, 2),
	('20110720-084421', 134, 2),
	('20110720-084428', 135, 2),
	('20110720-084434', 136, 2),
	('20110720-084435', 137, 2),
	('20110720-084436', 138, 2),
	('20110725-125757', 140, 2),
	('20110725-125758', 141, 2),
	('20110725-125759', 142, 2),
	('20110725-125800', 143, 2),
	('20110725-112250', 148, 2),
	('20111003-113116', 150, 2),
	('20111003-113127', 154, 2),
	('20111005-095124', 160, 2),
	('20111005-095129', 162, 2),
	('20111005-095133', 159, 2),
	('20111103-102724', 165, 2),
	('20111103-102727', 166, 2),
	('20111103-102728', 167, 2),
	('20111107-113752', 171, 2),
	('20111107-113809', 170, 2),
	('20111118-093415', 173, 2),
	('20111126-111738', 179, 2),
	('20111126-111739', 180, 2),
	('20111126-111740', 181, 2),
	('20120212-073459', 231, 2),
	('20120212-101108', 178, 2),
	('20120212-101106', 176, 2),
	('20120112-082353', 184, 2),
	('20120112-082413', 188, 2),
	('20120123-112342', 186, 2),
	('20120123-112423', 198, 2),
	('20120123-112439', 187, 2),
	('20120325-090534', 265, 2),
	('20120130-080826', 202, 2),
	('20120211-092744', 222, 2),
	('20120211-092741', 220, 2),
	('20120202-111237', 210, 2),
	('20120202-111238', 211, 2),
	('20120211-092747', 227, 2),
	('20120211-092754', 224, 2),
	('20120211-092820', 225, 2),
	('20120212-073500', 230, 2),
	('20120218-110628', 232, 2),
	('20120218-110633', 234, 2),
	('20120319-115902', 253, 2),
	('20120319-115903', 254, 2),
	('20120319-115904', 255, 2),
	('20120319-115921', 258, 2),
	('20120320-120001', 251, 2),
	('20120320-120005', 250, 2),
	('20120320-120007', 249, 2),
	('20120325-090501', 263, 2),
	('20120325-090502', 266, 2),
	('20120325-090550', 264, 2),
	('20120325-090551', 267, 2),
	('20120329-102640', 268, 2),
	('20120329-102641', 270, 2),
	('20120426-014511', 276, 2),
	('20120426-014514', 277, 2),
	('20120426-014515', 278, 2),
	('20120426-015513', 281, 2),
	('20120503-114707', 282, 2),
	('20120504-122221', 283, 2),
	('20120506-072058', 289, 2),
	('20120506-072155', 284, 2),
	('20120506-072156', 285, 2),
	('20120506-092221', 293, 2),
	('20120506-092232', 291, 2),
	('20120506-092236', 298, 2),
	('20120506-092240', 292, 2),
	('20120506-092244', 290, 2),
	('20120507-094509', 273, 2),
	('20120507-023428', 300, 2),
	('20120507-023429', 301, 2),
	('20120508-120616', 303, 2),
	('20120508-120619', 304, 2),
	('20120508-120621', 305, 2),
	('20120508-120622', 306, 2),
	('20120508-120624', 307, 2),
	('20120508-033058', 165, 7),
	('20120508-033059', 167, 7),
	('20120508-033108', 253, 7),
	('20120508-033110', 254, 7),
	('20120508-033114', 282, 7),
	('20160308-024130', 117, 8),
	('20120508-033156', 258, 8),
	('20120508-034458', 264, 8),
	('20120508-034500', 267, 8),
	('20121011-082146', 255, 13),
	('20120508-034513', 250, 9),
	('20120508-034528', 135, 9),
	('20120508-034531', 134, 9),
	('20120508-034553', 136, 9),
	('20120508-034554', 137, 9),
	('20160312-104923', 184, 42),
	('20130628-081715', 167, 12),
	('20120508-034910', 67, 10),
	('20120508-034911', 273, 10),
	('20130528-090912', 351, 9),
	('20160312-104941', 354, 42),
	('20160312-104926', 258, 42),
	('20120508-035132', 293, 11),
	('20130310-114632', 499, 2),
	('20120508-035135', 291, 11),
	('20130301-115312', 497, 2),
	('20120508-035139', 298, 11),
	('20120508-040902', 263, 8),
	('20120508-040904', 266, 8),
	('20120508-040911', 232, 8),
	('20120508-040912', 265, 8),
	('20120508-043912', 249, 12),
	('20120508-043913', 250, 12),
	('20120508-044319', 273, 12),
	('20130119-094915', 349, 12),
	('20120508-044417', 1, 7),
	('20120508-044423', 1, 8),
	('20120508-044427', 1, 9),
	('20120508-044438', 1, 10),
	('20120508-044448', 1, 11),
	('20120509-042506', 313, 2),
	('20120509-062727', 315, 2),
	('20120509-104040', 316, 2),
	('20120510-030158', 318, 2),
	('20120514-011524', 320, 2),
	('20120514-011528', 323, 2),
	('20120514-011533', 325, 2),
	('20120514-015021', 327, 2),
	('20120514-015022', 328, 2),
	('20120514-020639', 331, 2),
	('20120515-102013', 253, 12),
	('20120515-102014', 254, 12),
	('20120515-102017', 282, 12),
	('20120516-125030', 332, 2),
	('20160312-104931', 266, 42),
	('20120516-110401', 250, 10),
	('20141027-100203', 136, 28),
	('20120516-055156', 334, 2),
	('20120516-055158', 335, 2),
	('20120710-024041', 66, 12),
	('20130125-044046', 291, 15),
	('20130627-085803', 529, 2),
	('20140827-101343', 648, 2),
	('20140822-101104', 1, 22),
	('20120615-084937', 338, 2),
	('20120615-084951', 338, 10),
	('20120615-092715', 340, 10),
	('20120615-092724', 340, 2),
	('20120617-102606', 341, 2),
	('20120620-110505', 184, 10),
	('20120620-110451', 258, 10),
	('20120620-110518', 165, 10),
	('20120620-110524', 167, 10),
	('20160317-081318', 295, 40),
	('20160317-081220', 719, 40),
	('20120702-123630', 254, 8),
	('20120702-063920', 264, 10),
	('20120702-063921', 267, 10),
	('20120702-063939', 253, 10),
	('20120702-063941', 254, 10),
	('20120703-020901', 253, 13),
	('20121011-082016', 282, 13),
	('20121011-082040', 256, 13),
	('20120705-101717', 282, 8),
	('20120710-024103', 285, 12),
	('20120710-024104', 328, 12),
	('20120802-112430', 345, 2),
	('20120802-112508', 344, 2),
	('20120808-042134', 345, 12),
	('20120808-042145', 332, 12),
	('20120809-083321', 344, 12),
	('20130221-095730', 167, 8),
	('20160317-081228', 470, 40),
	('20120813-113853', 350, 2),
	('20120813-113900', 348, 2),
	('20120828-095008', 332, 15),
	('20120828-095116', 350, 15),
	('20120828-095704', 344, 15),
	('20120829-093734', 351, 2),
	('20120905-105959', 354, 2),
	('20120905-110005', 357, 2),
	('20120905-110007', 359, 2),
	('20120905-110015', 354, 7),
	('20120905-110017', 359, 7),
	('20120905-110025', 357, 7),
	('20120906-120008', 361, 2),
	('20120908-093116', 263, 10),
	('20120908-093119', 266, 10),
	('20120908-093126', 361, 10),
	('20120910-080300', 363, 2),
	('20120910-080319', 348, 10),
	('20120910-080321', 363, 10),
	('20120910-080343', 354, 10),
	('20120910-080352', 357, 10),
	('20120910-080354', 359, 10),
	('20120912-070835', 365, 2),
	('20120912-070840', 365, 10),
	('20120913-104025', 318, 12),
	('20120914-105513', 351, 15),
	('20120914-112834', 372, 2),
	('20120916-071555', 370, 2),
	('20151208-043214', 184, 26),
	('20141027-111542', 522, 28),
	('20141027-110513', 439, 28),
	('20120925-120537', 354, 12),
	('20120925-120541', 359, 12),
	('20120925-120554', 184, 12),
	('20120925-120556', 258, 12),
	('20120925-111917', 117, 12),
	('20120925-111920', 384, 12),
	('20120925-111928', 384, 2),
	('20160317-081229', 471, 40),
	('20160312-105633', 167, 42),
	('20120926-091638', 75, 15),
	('20120929-104133', 385, 2),
	('20120930-075130', 373, 2),
	('20121001-083932', 346, 13),
	('20121002-084747', 362, 2),
	('20121002-084754', 362, 10),
	('20121004-040956', 354, 8),
	('20121005-112146', 387, 2),
	('20130423-095303', 508, 2),
	('20121008-084740', 388, 2),
	('20121008-084750', 393, 2),
	('20121008-084804', 395, 2),
	('20121008-085005', 363, 8),
	('20121008-085011', 365, 8),
	('20121008-085023', 372, 8),
	('20121008-085049', 362, 8),
	('20121010-094949', 357, 8),
	('20160317-081620', 639, 40),
	('20121011-081349', 253, 9),
	('20141027-110335', 313, 28),
	('20121011-081631', 282, 9),
	('20121011-083903', 340, 13),
	('20121011-084155', 359, 8),
	('20141027-111513', 476, 28),
	('20121011-024405', 167, 13),
	('20121015-105508', 422, 2),
	('20121015-110832', 393, 15),
	('20150305-010208', 471, 16),
	('20121015-023448', 1, 16),
	('20121023-121350', 427, 2),
	('20121023-121355', 375, 2),
	('20151208-043150', 264, 35),
	('20160315-020119', 75, 22),
	('20150603-051243', 666, 28),
	('20121106-093351', 117, 10),
	('20121106-093354', 384, 10),
	('20121109-100403', 293, 9),
	('20121109-100413', 291, 9),
	('20121109-100420', 298, 9),
	('20121109-100440', 292, 9),
	('20121109-100450', 290, 9),
	('20121115-101539', 434, 9),
	('20121115-101548', 434, 2),
	('20121115-101656', 434, 12),
	('20121119-104113', 346, 2),
	('20121119-104416', 1, 13),
	('20121119-110137', 254, 13),
	('20121122-113330', 328, 15),
	('20121122-113352', 331, 15),
	('20121122-113404', 327, 15),
	('20121124-020919', 437, 2),
	('20121127-111752', 439, 2),
	('20121129-085945', 165, 9),
	('20121129-090045', 167, 9),
	('20141027-110445', 318, 28),
	('20121129-092446', 439, 9),
	('20141027-110453', 455, 28),
	('20130216-115141', 68, 19),
	('20121203-092404', 295, 9),
	('20121203-092406', 416, 9),
	('20160312-105026', 357, 42),
	('20121206-075830', 447, 2),
	('20121212-103922', 360, 7),
	('20121212-115614', 395, 15),
	('20121217-112816', 346, 17),
	('20121217-112822', 1, 17),
	('20130119-094907', 350, 12),
	('20121219-072023', 449, 18),
	('20121219-072024', 450, 18),
	('20121219-072026', 451, 18),
	('20121219-072027', 452, 18),
	('20121219-072231', 1, 18),
	('20121219-072424', 25, 18),
	('20121219-072426', 9, 18),
	('20121220-123211', 455, 2),
	('20121220-123218', 457, 2),
	('20121220-072051', 134, 15),
	('20121222-050446', 455, 12),
	('20121222-050546', 455, 9),
	('20141027-111517', 471, 28),
	('20130419-040150', 255, 12),
	('20121226-023618', 350, 9),
	('20121227-112850', 399, 9),
	('20141027-111550', 609, 28),
	('20121227-112911', 393, 9),
	('20121228-012947', 466, 2),
	('20121228-013103', 464, 2),
	('20121228-085846', 457, 10),
	('20121228-082834', 463, 10),
	('20121228-082850', 463, 2),
	('20121229-092409', 463, 8),
	('20130103-023236', 372, 7),
	('20130106-101722', 470, 19),
	('20130108-083653', 1, 19),
	('20130110-084141', 470, 2),
	('20130110-084144', 471, 2),
	('20130110-032921', 423, 9),
	('20130110-032944', 327, 9),
	('20130110-034043', 328, 9),
	('20130110-095435', 477, 19),
	('20130112-114611', 354, 9),
	('20160312-104922', 107, 42),
	('20141211-114713', 363, 27),
	('20130116-093309', 385, 15),
	('20130116-093357', 135, 15),
	('20130116-093410', 136, 15),
	('20130117-081247', 480, 2),
	('20130119-082928', 327, 12),
	('20130122-115127', 363, 9),
	('20130122-115153', 331, 9),
	('20130126-020322', 332, 9),
	('20130216-082547', 102, 9),
	('20130216-084027', 117, 9),
	('20130216-083842', 340, 9),
	('20130216-084124', 338, 9),
	('20130216-084226', 384, 9),
	('20150305-044833', 609, 31),
	('20130223-023453', 357, 17),
	('20130223-024042', 354, 17),
	('20130225-094538', 385, 9),
	('20141027-111611', 292, 28),
	('20130225-094600', 316, 9),
	('20130227-113208', 486, 20),
	('20130227-113210', 488, 20),
	('20130227-113212', 489, 20),
	('20130227-113215', 490, 20),
	('20130228-094918', 490, 2),
	('20130228-094921', 486, 2),
	('20130228-094922', 488, 2),
	('20130228-094923', 489, 2),
	('20130301-081450', 363, 11),
	('20130301-081452', 354, 11),
	('20130301-081454', 365, 11),
	('20130301-081500', 372, 11),
	('20130301-081508', 481, 11),
	('20130301-081517', 463, 11),
	('20130301-081529', 264, 11),
	('20130301-081541', 434, 11),
	('20130311-121710', 476, 2),
	('20130311-121713', 477, 2),
	('20130311-121714', 484, 2),
	('20130311-121715', 485, 2),
	('20130311-090158', 500, 2),
	('20130313-103724', 501, 2),
	('20130316-021936', 357, 12),
	('20130316-032715', 471, 19),
	('20150305-010204', 470, 16),
	('20130316-033054', 476, 16),
	('20130316-040429', 372, 12),
	('20130319-084117', 344, 9),
	('20130319-084124', 184, 9),
	('20130319-084135', 258, 9),
	('20130319-090801', 502, 2),
	('20130319-090842', 502, 19),
	('20130326-082322', 464, 9),
	('20130327-083544', 466, 9),
	('20141027-111533', 515, 28),
	('20130327-100606', 67, 9),
	('20160315-111913', 714, 9),
	('20140908-041902', 107, 26),
	('20130404-084339', 476, 19),
	('20130404-084340', 485, 19),
	('20130404-084342', 484, 19),
	('20130408-035215', 395, 19),
	('20130416-045315', 503, 2),
	('20130930-085637', 536, 2),
	('20130419-085800', 167, 20),
	('20130419-095235', 499, 20),
	('20130419-112630', 66, 20),
	('20130419-120302', 282, 10),
	('20130424-114306', 510, 2),
	('20130425-034314', 508, 20),
	('20130425-034523', 503, 8),
	('20130504-020953', 513, 2),
	('20130508-121156', 510, 19),
	('20130513-080628', 515, 2),
	('20130513-080635', 515, 19),
	('20130515-112731', 510, 9),
	('20130528-051905', 429, 9),
	('20130528-052511', 501, 9),
	('20130529-083813', 513, 21),
	('20130529-083812', 500, 21),
	('20130529-084744', 1, 21),
	('20130529-044629', 457, 9),
	('20130529-075407', 518, 2),
	('20130531-110940', 503, 12),
	('20130603-113207', 102, 12),
	('20130603-113234', 340, 12),
	('20130605-125146', 522, 2),
	('20130605-125152', 522, 19),
	('20130606-120555', 520, 2),
	('20130606-012425', 523, 2),
	('20130606-012433', 523, 19),
	('20130607-055333', 520, 19),
	('20130609-113617', 525, 2),
	('20130609-113755', 525, 8),
	('20130609-113807', 525, 12),
	('20160312-104929', 263, 42),
	('20130622-081207', 527, 2),
	('20130624-020441', 359, 9),
	('20130628-081718', 231, 12),
	('20141211-114703', 354, 27),
	('20130705-120750', 2, 2),
	('20130705-120759', 533, 2),
	('20130705-121629', 534, 2),
	('20130705-121642', 534, 9),
	('20130705-035647', 1, 12),
	('20130711-090146', 535, 2),
	('20130716-024010', 291, 19),
	('20130724-034601', 363, 12),
	('20130726-083806', 513, 10),
	('20130726-083845', 499, 10),
	('20130730-093751', 513, 9),
	('20130730-093752', 513, 9),
	('20130730-093755', 500, 9),
	('20130730-093756', 500, 9),
	('20130730-051029', 535, 19),
	('20130803-120936', 502, 16),
	('20130803-120941', 520, 16),
	('20130805-093854', 535, 16),
	('20130813-105412', 533, 20),
	('20130819-115930', 501, 19),
	('20130819-120033', 137, 19),
	('20130819-120034', 136, 19),
	('20140908-041838', 272, 26),
	('20130823-045755', 497, 22),
	('20130823-045758', 345, 22),
	('20130823-045802', 439, 22),
	('20130823-045804', 332, 22),
	('20130823-045823', 354, 22),
	('20130823-045826', 359, 22),
	('20140908-041836', 363, 26),
	('20130823-045856', 350, 22),
	('20140908-041832', 267, 26),
	('20130823-045920', 167, 22),
	('20140908-041829', 264, 26),
	('20140829-014017', 363, 23),
	('20130824-111157', 258, 22),
	('20130824-111328', 370, 22),
	('20130824-115122', 285, 22),
	('20130824-115128', 344, 22),
	('20130824-115310', 328, 22),
	('20130824-115414', 327, 22),
	('20130824-115504', 331, 22),
	('20140829-014029', 267, 23),
	('20130830-081015', 301, 15),
	('20130830-081259', 316, 15),
	('20130904-105916', 232, 10),
	('20130904-110219', 265, 10),
	('20130904-110249', 404, 10),
	('20130905-044219', 102, 7),
	('20130905-044225', 338, 7),
	('20130905-044229', 340, 7),
	('20130907-094556', 388, 9),
	('20130907-094856', 165, 23),
	('20130907-094913', 370, 23),
	('20130907-094929', 341, 23),
	('20130907-094931', 510, 23),
	('20130907-094942', 501, 23),
	('20130907-095002', 427, 23),
	('20130907-095004', 439, 23),
	('20130907-095027', 349, 23),
	('20130907-095030', 350, 23),
	('20130910-041357', 136, 23),
	('20130910-041403', 291, 23),
	('20130912-120518', 427, 22),
	('20130920-022140', 499, 9),
	('20130920-022143', 489, 9),
	('20130920-022150', 533, 9),
	('20130930-085649', 536, 19),
	('20131008-090245', 395, 9),
	('20131011-091808', 542, 2),
	('20131019-115018', 543, 2),
	('20131107-043725', 499, 21),
	('20131119-051236', 434, 10),
	('20131113-090531', 267, 9),
	('20131126-091728', 167, 23),
	('20131204-104446', 545, 2),
	('20131206-054606', 249, 11),
	('20131206-054615', 455, 11),
	('20131210-035150', 554, 2),
	('20131210-035158', 73, 2),
	('20131210-035205', 556, 2),
	('20131215-055451', 560, 2),
	('20131215-060800', 558, 2),
	('20131216-055728', 560, 20),
	('20131216-055743', 558, 20),
	('20140106-055004', 388, 15),
	('20140112-113330', 562, 2),
	('20140112-113347', 562, 19),
	('20140113-022300', 562, 9),
	('20140113-022304', 562, 9),
	('20140113-022328', 523, 9),
	('20140113-022349', 515, 9),
	('20140115-085523', 563, 2),
	('20140115-085527', 565, 2),
	('20140115-085541', 563, 20),
	('20140115-085543', 565, 20),
	('20140122-034500', 567, 9),
	('20140122-034525', 568, 2),
	('20141122-084518', 592, 22),
	('20140122-104856', 570, 2),
	('20140123-101759', 567, 2),
	('20140123-021929', 350, 19),
	('20140123-022002', 363, 19),
	('20140123-022147', 267, 19),
	('20140127-092502', 556, 12),
	('20140127-093533', 570, 16),
	('20140127-094026', 570, 19),
	('20141122-084517', 591, 22),
	('20140129-120036', 574, 2),
	('20140129-120037', 575, 2),
	('20140129-031825', 576, 2),
	('20140203-030508', 363, 20),
	('20140207-030143', 499, 24),
	('20160312-104902', 264, 42),
	('20140207-030219', 354, 24),
	('20160312-023641', 688, 24),
	('20160312-022914', 622, 24),
	('20140211-033905', 204, 24),
	('20140211-033908', 214, 24),
	('20140211-033913', 558, 24),
	('20140211-033915', 560, 24),
	('20140211-033917', 216, 24),
	('20140211-033920', 215, 24),
	('20140211-033921', 213, 24),
	('20140211-042252', 555, 2),
	('20140213-040211', 555, 12),
	('20140215-014600', 251, 15),
	('20140220-094022', 588, 2),
	('20140220-094431', 589, 2),
	('20140225-083124', 588, 23),
	('20140225-031013', 204, 9),
	('20140225-031015', 213, 9),
	('20140225-031019', 558, 9),
	('20140225-031021', 560, 9),
	('20140225-031023', 215, 9),
	('20140225-031024', 216, 9),
	('20140225-031026', 214, 9),
	('20140225-032519', 204, 10),
	('20140301-041432', 568, 15),
	('20140301-041430', 579, 15),
	('20140225-032853', 560, 10),
	('20140904-120225', 434, 8),
	('20140827-101441', 648, 12),
	('20140908-041854', 105, 26),
	('20140309-084413', 600, 2),
	('20140309-084418', 607, 2),
	('20140309-084425', 599, 2),
	('20140309-084430', 603, 2),
	('20140309-084436', 605, 2),
	('20140310-015730', 576, 10),
	('20140311-113422', 590, 2),
	('20140311-113423', 592, 2),
	('20140311-113424', 591, 2),
	('20140311-113425', 593, 2),
	('20140311-113427', 608, 2),
	('20140312-111139', 609, 2),
	('20140312-111207', 609, 19),
	('20140315-045007', 574, 21),
	('20140315-045017', 575, 21),
	('20160312-105022', 499, 42),
	('20160312-105010', 434, 42),
	('20160312-105003', 282, 42),
	('20140323-094239', 610, 2),
	('20140401-085614', 610, 21),
	('20140403-021128', 66, 10),
	('20140404-100230', 204, 8),
	('20140404-100232', 216, 8),
	('20140404-104829', 213, 8),
	('20140404-105145', 560, 8),
	('20140410-101738', 575, 9),
	('20140410-101741', 610, 9),
	('20140410-052519', 613, 20),
	('20140412-095719', 503, 10),
	('20140412-095723', 525, 10),
	('20140422-113524', 613, 13),
	('20140422-113525', 615, 13),
	('20140422-114215', 560, 13),
	('20140422-114214', 558, 13),
	('20140422-114141', 600, 13),
	('20140422-114144', 603, 13),
	('20140422-114217', 599, 13),
	('20140422-114220', 605, 13),
	('20140422-114228', 607, 13),
	('20140425-102055', 589, 12),
	('20140425-102059', 590, 12),
	('20140425-102101', 591, 12),
	('20140425-102102', 592, 12),
	('20140425-102103', 593, 12),
	('20140425-102105', 608, 12),
	('20140425-102627', 588, 12),
	('20140425-113450', 213, 13),
	('20140425-113845', 216, 13),
	('20140425-113847', 214, 13),
	('20140425-113849', 215, 13),
	('20140425-113851', 204, 13),
	('20140426-022052', 363, 24),
	('20140429-024156', 481, 9),
	('20140429-024206', 463, 9),
	('20140505-111403', 613, 2),
	('20140509-075016', 618, 13),
	('20140509-075028', 618, 20),
	('20140512-042352', 622, 2),
	('20140515-034327', 624, 13),
	('20140515-034334', 624, 20),
	('20140515-110251', 615, 2),
	('20140515-111810', 624, 2),
	('20140520-101818', 626, 2),
	('20140522-112555', 628, 2),
	('20140522-112559', 630, 2),
	('20140522-112603', 632, 2),
	('20140523-094313', 628, 10),
	('20140523-094318', 630, 10),
	('20140523-094330', 632, 10),
	('20160317-081605', 535, 40),
	('20160317-081217', 716, 40),
	('20160317-081216', 715, 40),
	('20140616-044003', 66, 25),
	('20160312-105114', 372, 42),
	('20140616-044013', 78, 25),
	('20140616-044024', 264, 25),
	('20140616-044026', 267, 25),
	('20140616-044030', 363, 25),
	('20140616-044041', 354, 25),
	('20140616-044046', 359, 25),
	('20140616-044056', 253, 25),
	('20140616-044058', 254, 25),
	('20140616-044736', 626, 25),
	('20140616-044739', 628, 25),
	('20140616-044742', 630, 25),
	('20140616-044743', 632, 25),
	('20140616-044745', 627, 25),
	('20140616-044809', 102, 25),
	('20140616-044812', 231, 25),
	('20140616-052709', 372, 25),
	('20141211-114516', 464, 27),
	('20140616-044828', 340, 25),
	('20140616-044829', 338, 25),
	('20140616-044840', 232, 25),
	('20140616-044843', 236, 25),
	('20140616-044845', 265, 25),
	('20140616-045014', 463, 25),
	('20140616-045113', 105, 25),
	('20140616-045116', 107, 25),
	('20140616-045120', 115, 25),
	('20140616-045122', 117, 25),
	('20140616-045137', 263, 25),
	('20140616-045150', 384, 25),
	('20140616-052730', 525, 25),
	('20140616-052940', 434, 25),
	('20140616-053855', 357, 25),
	('20140617-012900', 266, 25),
	('20140617-012913', 1, 25),
	('20140617-013437', 613, 24),
	('20140617-013455', 624, 24),
	('20140617-013532', 599, 24),
	('20140617-013556', 600, 24),
	('20140617-044306', 370, 10),
	('20140618-040300', 518, 9),
	('20140628-084437', 124, 9),
	('20140629-101642', 637, 2),
	('20140629-101650', 635, 2),
	('20140701-084711', 228, 10),
	('20140701-084713', 229, 10),
	('20140701-084715', 236, 10),
	('20140701-084719', 403, 10),
	('20140701-084721', 635, 10),
	('20140701-084722', 637, 10),
	('20160317-081218', 717, 40),
	('20140701-084752', 229, 8),
	('20160314-104048', 664, 15),
	('20140701-084800', 403, 8),
	('20140701-084802', 404, 8),
	('20140701-084803', 635, 8),
	('20140701-084804', 637, 8),
	('20140701-102309', 639, 2),
	('20150422-102618', 664, 28),
	('20150422-102617', 663, 28),
	('20160312-104934', 463, 42),
	('20140710-120157', 618, 2),
	('20140710-115941', 216, 2),
	('20141027-110344', 167, 28),
	('20140711-083652', 107, 9),
	('20140711-084344', 266, 9),
	('20141027-110431', 254, 28),
	('20140711-025641', 630, 9),
	('20140711-025647', 632, 9),
	('20140711-025654', 628, 9),
	('20140715-045536', 576, 9),
	('20140718-114131', 639, 9),
	('20140724-044124', 375, 9),
	('20140724-044227', 608, 9),
	('20140725-055908', 642, 2),
	('20140725-113729', 592, 23),
	('20140725-113742', 593, 23),
	('20140725-113817', 375, 23),
	('20140730-112119', 645, 2),
	('20140731-084142', 646, 2),
	('20140801-084044', 642, 12),
	('20140801-094359', 645, 10),
	('20140801-094425', 646, 10),
	('20140801-094636', 645, 25),
	('20140801-094646', 646, 25),
	('20140801-094759', 96, 25),
	('20140802-111604', 480, 15),
	('20140811-054134', 563, 19),
	('20140811-054135', 565, 19),
	('20140811-054601', 639, 19),
	('20140908-042458', 1, 26),
	('20140908-042510', 354, 26),
	('20141003-094444', 463, 12),
	('20160312-020949', 1, 24),
	('20160312-105721', 254, 42),
	('20141027-094206', 167, 27),
	('20141027-094245', 457, 27),
	('20141027-094249', 458, 27),
	('20141027-094303', 576, 27),
	('20141027-095117', 102, 13),
	('20141027-095501', 338, 13),
	('20141027-111443', 523, 28),
	('20141027-100205', 137, 28),
	('20141027-100207', 138, 28),
	('20141027-100209', 291, 28),
	('20141027-100212', 295, 28),
	('20141027-100216', 416, 28),
	('20141027-100223', 429, 28),
	('20141027-100227', 501, 28),
	('20141027-100339', 567, 28),
	('20141027-100342', 568, 28),
	('20141027-100347', 578, 28),
	('20141027-100407', 579, 28),
	('20141027-100410', 581, 28),
	('20141027-100413', 583, 28),
	('20141027-100459', 586, 28),
	('20141027-100501', 594, 28),
	('20141027-100536', 327, 28),
	('20141027-100543', 134, 28),
	('20141027-100551', 328, 28),
	('20141027-100556', 285, 28),
	('20141027-100606', 323, 28),
	('20141027-100609', 333, 28),
	('20141027-100618', 344, 28),
	('20141027-100646', 560, 28),
	('20141027-100653', 600, 28),
	('20141027-100657', 599, 28),
	('20141027-100702', 605, 28),
	('20141027-100706', 622, 28),
	('20141027-100730', 576, 28),
	('20141027-100735', 457, 28),
	('20141027-100739', 543, 28),
	('20141027-100742', 458, 28),
	('20141027-100811', 363, 28),
	('20141027-100816', 365, 28),
	('20141027-100823', 348, 28),
	('20141027-100830', 267, 28),
	('20141027-100838', 354, 28),
	('20141027-100842', 359, 28),
	('20141027-100845', 464, 28),
	('20141027-100906', 486, 28),
	('20141027-100912', 489, 28),
	('20141027-100917', 488, 28),
	('20141027-100920', 518, 28),
	('20141027-100924', 499, 28),
	('20141027-100927', 490, 28),
	('20141027-100930', 533, 28),
	('20141027-110537', 427, 28),
	('20141027-110541', 648, 28),
	('20141027-111620', 422, 28),
	('20141027-111622', 406, 28),
	('20141027-111626', 296, 28),
	('20141027-111750', 290, 28),
	('20141027-111754', 298, 28),
	('20141027-111804', 338, 28),
	('20141027-111808', 102, 28),
	('20141027-111814', 340, 28),
	('20141027-111820', 408, 28),
	('20141027-111824', 294, 28),
	('20141027-111829', 645, 28),
	('20141027-111832', 425, 28),
	('20141027-111835', 299, 28),
	('20141027-014157', 1, 28),
	('20141027-014211', 500, 28),
	('20141027-014215', 513, 28),
	('20141027-014218', 574, 28),
	('20141027-014222', 575, 28),
	('20141027-014227', 610, 28),
	('20141029-053037', 350, 28),
	('20141029-053140', 384, 28),
	('20141029-053301', 117, 28),
	('20141104-043902', 503, 27),
	('20141104-043903', 525, 27),
	('20141107-015637', 480, 9),
	('20141108-112953', 351, 28),
	('20141108-042004', 499, 12),
	('20141108-042231', 427, 12),
	('20141111-012848', 466, 28),
	('20141114-025539', 503, 29),
	('20141114-025541', 525, 29),
	('20141114-032434', 1, 29),
	('20141117-110839', 167, 29),
	('20141122-084044', 567, 12),
	('20141122-084053', 594, 12),
	('20141122-084056', 586, 12),
	('20141122-084516', 590, 22),
	('20141122-084515', 589, 22),
	('20141122-084101', 579, 12),
	('20141122-084102', 578, 12),
	('20141122-084512', 375, 22),
	('20141122-084520', 593, 22),
	('20141122-084525', 608, 22),
	('20141122-084526', 640, 22),
	('20141122-084643', 480, 22),
	('20141122-084645', 510, 22),
	('20141122-084717', 648, 22),
	('20141122-103325', 581, 12),
	('20141122-103326', 568, 12),
	('20141122-103327', 583, 12),
	('20141122-013457', 291, 22),
	('20141122-013502', 137, 22),
	('20141122-013522', 501, 22),
	('20141122-041145', 258, 28),
	('20141124-083533', 476, 30),
	('20141124-083752', 520, 30),
	('20141124-083949', 502, 30),
	('20141124-050117', 165, 22),
	('20141124-050131', 254, 22),
	('20141127-114608', 395, 28),
	('20141203-103638', 651, 2),
	('20141205-095748', 375, 12),
	('20141205-100516', 640, 12),
	('20141209-094920', 370, 28),
	('20141209-095740', 357, 26),
	('20150815-081737', 560, 27),
	('20141211-030905', 466, 27),
	('20141212-084554', 184, 27),
	('20141212-084606', 258, 27),
	('20141212-033102', 372, 26),
	('20141212-033200', 463, 26),
	('20141220-085939', 230, 28),
	('20141220-085940', 231, 28),
	('20150109-035822', 499, 11),
	('20150129-041539', 282, 26),
	('20150129-041553', 253, 26),
	('20150209-052908', 319, 31),
	('20150209-052912', 387, 31),
	('20150210-021923', 455, 26),
	('20160317-081611', 741, 40),
	('20150212-120013', 661, 24),
	('20160312-104942', 359, 42),
	('20150212-120257', 661, 7),
	('20150212-120456', 661, 2),
	('20150217-113224', 466, 7),
	('20160314-104044', 663, 15),
	('20150223-042147', 213, 31),
	('20150223-042149', 204, 31),
	('20150223-042150', 215, 31),
	('20150223-042153', 216, 31),
	('20150223-042154', 214, 31),
	('20150223-042405', 560, 31),
	('20150224-094934', 1, 30),
	('20150227-010834', 485, 31),
	('20150227-010843', 522, 31),
	('20150227-023453', 510, 28),
	('20150303-084618', 253, 19),
	('20150303-084621', 254, 19),
	('20150303-084700', 282, 19),
	('20150304-084202', 320, 32),
	('20150304-084214', 125, 32),
	('20150304-084333', 292, 32),
	('20150304-084340', 290, 32),
	('20150304-084341', 298, 32),
	('20150304-084351', 102, 32),
	('20150304-084528', 1, 32),
	('20150304-085342', 291, 32),
	('20150304-085433', 510, 32),
	('20150304-092155', 470, 28),
	('20150304-092356', 477, 28),
	('20150304-102906', 293, 32),
	('20150304-102915', 399, 32),
	('20150304-102934', 135, 32),
	('20150304-102950', 480, 32),
	('20150304-103007', 388, 32),
	('20150304-103214', 466, 32),
	('20150304-112310', 327, 32),
	('20150304-112318', 328, 32),
	('20150304-112332', 331, 32),
	('20150307-020107', 663, 2),
	('20150307-020108', 664, 2),
	('20150307-020117', 663, 8),
	('20150307-020119', 664, 8),
	('20150307-020136', 663, 10),
	('20150307-020138', 664, 10),
	('20150309-083039', 618, 24),
	('20150309-083056', 618, 24),
	('20150312-112123', 253, 24),
	('20150312-112309', 282, 24),
	('20160312-104908', 365, 42),
	('20150313-015104', 666, 2),
	('20160317-081643', 663, 40),
	('20150314-090201', 615, 24),
	('20150314-010646', 520, 31),
	('20150314-010801', 476, 31),
	('20150314-023942', 254, 24),
	('20150318-025735', 666, 24),
	('20150320-085139', 523, 23),
	('20160312-104921', 105, 42),
	('20150321-083558', 523, 31),
	('20150324-012723', 667, 2),
	('20150324-012746', 667, 10),
	('20150325-085520', 535, 33),
	('20150325-085632', 470, 33),
	('20150325-085638', 1, 33),
	('20150407-094316', 372, 10),
	('20150413-040003', 1, 20),
	('20150414-120543', 313, 9),
	('20150416-101124', 605, 24),
	('20150418-043801', 667, 8),
	('20150418-050629', 639, 16),
	('20150418-050653', 639, 33),
	('20150422-102641', 463, 28),
	('20150422-104602', 663, 31),
	('20150422-104603', 664, 31),
	('20150422-052615', 666, 9),
	('20150423-103257', 639, 30),
	('20150423-110412', 370, 12),
	('20150423-114446', 663, 27),
	('20150423-114447', 664, 27),
	('20150424-111742', 165, 12),
	('20150425-103012', 439, 12),
	('20150427-111815', 535, 30),
	('20150428-051702', 254, 26),
	('20150429-085457', 533, 12),
	('20150502-085003', 102, 34),
	('20150502-085033', 363, 34),
	('20150502-085041', 354, 34),
	('20150504-082649', 340, 34),
	('20150504-082700', 338, 34),
	('20160507-104732', 533, 37),
	('20150504-083052', 576, 34),
	('20150504-083100', 1, 34),
	('20150504-084528', 664, 29),
	('20150504-084529', 663, 29),
	('20150504-035009', 664, 7),
	('20150512-040207', 470, 9),
	('20150505-021204', 291, 12),
	('20150511-105014', 502, 35),
	('20150525-032746', 667, 26),
	('20150608-045336', 354, 29),
	('20150528-082455', 558, 28),
	('20150609-091214', 520, 35),
	('20150609-091226', 476, 35),
	('20150609-100128', 503, 25),
	('20150609-100134', 184, 25),
	('20150609-100136', 258, 25),
	('20150609-101142', 167, 25),
	('20150703-013212', 664, 9),
	('20150716-112858', 357, 24),
	('20150716-112902', 359, 24),
	('20150720-010620', 66, 28),
	('20150724-093847', 672, 2),
	('20150724-093850', 671, 2),
	('20150724-093851', 675, 2),
	('20150728-094118', 254, 36),
	('20150728-094051', 637, 36),
	('20150728-094040', 265, 36),
	('20150728-094043', 403, 36),
	('20150728-094032', 236, 36),
	('20150728-094114', 167, 36),
	('20160308-045505', 455, 10),
	('20150728-093949', 66, 36),
	('20150728-093959', 666, 36),
	('20150728-094122', 282, 36),
	('20150728-094227', 105, 36),
	('20150728-094210', 258, 36),
	('20150728-094158', 184, 36),
	('20150728-094053', 667, 36),
	('20150728-094044', 404, 36),
	('20150728-094049', 635, 36),
	('20150728-094024', 229, 36),
	('20150728-094020', 228, 36),
	('20150728-094028', 232, 36),
	('20150728-094233', 106, 36),
	('20150728-094238', 107, 36),
	('20150728-094247', 263, 36),
	('20150728-094248', 266, 36),
	('20150728-094303', 503, 36),
	('20150728-094317', 264, 36),
	('20150728-094319', 267, 36),
	('20150728-094329', 525, 36),
	('20150728-094343', 663, 36),
	('20150728-094344', 664, 36),
	('20150728-094409', 354, 36),
	('20150728-094413', 359, 36),
	('20150728-094426', 463, 36),
	('20150728-094632', 363, 36),
	('20150728-094648', 434, 36),
	('20150728-094657', 372, 36),
	('20150728-094707', 357, 36),
	('20150728-094713', 1, 36),
	('20150728-094916', 671, 36),
	('20150728-094917', 672, 36),
	('20150728-094919', 675, 36),
	('20150728-094930', 213, 36),
	('20150728-095022', 214, 36),
	('20150728-095023', 216, 36),
	('20150728-095031', 560, 36),
	('20150728-095046', 622, 36),
	('20150728-023558', 340, 36),
	('20150729-085643', 466, 10),
	('20150731-111322', 486, 9),
	('20150805-015335', 214, 2),
	('20150805-015339', 676, 2),
	('20160312-105517', 1, 42),
	('20150810-101813', 676, 24),
	('20150810-101818', 677, 24),
	('20150810-102049', 676, 36),
	('20150810-102050', 677, 36),
	('20150810-103045', 677, 2),
	('20150814-100745', 282, 27),
	('20150814-032435', 672, 10),
	('20150814-032438', 675, 10),
	('20160312-104904', 267, 42),
	('20160312-104244', 66, 24),
	('20150901-021327', 263, 27),
	('20160315-111614', 705, 9),
	('20150819-032417', 522, 29),
	('20150822-103049', 672, 24),
	('20150822-103051', 671, 24),
	('20150822-103052', 675, 24),
	('20150824-084204', 689, 2),
	('20150824-084205', 688, 2),
	('20150824-084214', 680, 2),
	('20150826-114816', 680, 36),
	('20150826-114817', 688, 36),
	('20150826-114819', 689, 36),
	('20160312-104906', 363, 42),
	('20150901-103047', 254, 27),
	('20150829-072107', 692, 2),
	('20150901-014254', 264, 27),
	('20150901-014259', 267, 27),
	('20150901-020929', 105, 27),
	('20150901-021122', 106, 27),
	('20150901-021330', 185, 27),
	('20150902-085855', 671, 28),
	('20150902-085900', 688, 28),
	('20150902-085903', 692, 28),
	('20150902-085907', 680, 28),
	('20150908-084814', 214, 27),
	('20150908-084816', 216, 27),
	('20150908-084817', 215, 27),
	('20150908-084823', 204, 27),
	('20150908-085010', 213, 27),
	('20150908-085011', 212, 27),
	('20150908-013338', 574, 9),
	('20150908-032324', 677, 28),
	('20150908-032333', 672, 28),
	('20150908-032334', 675, 28),
	('20150908-032338', 689, 28),
	('20150909-041957', 694, 2),
	('20150910-111627', 676, 28),
	('20150910-021551', 694, 28),
	('20150910-021614', 694, 36),
	('20150910-021616', 694, 36),
	('20150910-021618', 694, 36),
	('20150914-100309', 1, 15),
	('20150914-032402', 384, 8),
	('20150914-044948', 671, 10),
	('20150914-044952', 677, 10),
	('20150914-044954', 680, 10),
	('20150914-044955', 688, 10),
	('20150914-045000', 689, 10),
	('20150914-045001', 692, 10),
	('20150914-045002', 694, 10),
	('20150914-045026', 692, 36),
	('20150915-115458', 75, 9),
	('20150918-090815', 464, 37),
	('20150918-090820', 486, 37),
	('20150918-090833', 363, 37),
	('20150918-090849', 384, 37),
	('20150918-090856', 463, 37),
	('20150918-091023', 1, 37),
	('20150918-091149', 117, 37),
	('20150918-091307', 481, 37),
	('20150921-115323', 694, 24),
	('20150923-104227', 338, 20),
	('20150923-104231', 340, 20),
	('20150928-045729', 698, 2),
	('20150929-114507', 384, 20),
	('20150930-093805', 672, 20),
	('20150930-093806', 675, 20),
	('20150930-093810', 677, 20),
	('20150930-093813', 688, 20),
	('20150930-093815', 689, 20),
	('20150930-093817', 692, 20),
	('20150930-093821', 694, 20),
	('20151002-092739', 698, 9),
	('20151002-092839', 698, 28),
	('20151006-011500', 702, 2),
	('20151006-041151', 434, 7),
	('20151007-090657', 702, 28),
	('20151007-090816', 702, 9),
	('20151007-093652', 701, 2),
	('20151009-093129', 574, 23),
	('20151009-093216', 500, 23),
	('20151009-093218', 513, 23),
	('20151009-093257', 610, 23),
	('20151015-093434', 663, 25),
	('20151015-093436', 664, 25),
	('20151015-093448', 282, 25),
	('20151015-104540', 703, 2),
	('20151016-081241', 567, 15),
	('20151016-081247', 578, 15),
	('20151016-081252', 581, 15),
	('20151017-082525', 703, 23),
	('20151017-101746', 575, 23),
	('20151017-101759', 703, 9),
	('20151023-094005', 705, 2),
	('20151023-104913', 714, 2),
	('20151024-103230', 513, 20),
	('20151026-094632', 267, 24),
	('20151102-092529', 568, 9),
	('20151102-050259', 216, 25),
	('20151102-050444', 213, 25),
	('20151102-051537', 214, 25),
	('20151102-051543', 560, 25),
	('20151103-092641', 340, 24),
	('20151103-092755', 117, 24),
	('20151103-095632', 715, 2),
	('20151103-095633', 716, 2),
	('20151103-095634', 717, 2),
	('20151103-095657', 715, 16),
	('20151103-095659', 716, 16),
	('20151103-095700', 717, 16),
	('20151104-104727', 730, 9),
	('20151104-110233', 730, 2),
	('20151106-084008', 384, 24),
	('20151106-101458', 301, 38),
	('20151106-101638', 500, 38),
	('20151106-101641', 574, 38),
	('20151106-101646', 513, 38),
	('20151106-101649', 610, 38),
	('20151106-101653', 703, 38),
	('20151106-101700', 575, 38),
	('20151106-102255', 316, 38),
	('20151106-042309', 354, 20),
	('20151107-112453', 499, 37),
	('20151117-035238', 719, 16),
	('20151118-104000', 351, 12),
	('20151123-113454', 736, 2),
	('20151126-090129', 292, 12),
	('20151202-091641', 107, 27),
	('20151202-091833', 266, 27),
	('20151203-034937', 387, 37),
	('20151209-082229', 363, 35),
	('20151209-082247', 463, 35),
	('20151210-015337', 370, 7),
	('20151210-020604', 387, 17),
	('20151210-020645', 359, 17),
	('20151210-020714', 434, 17),
	('20151210-020801', 338, 17),
	('20151210-020805', 340, 17),
	('20151210-020819', 363, 17),
	('20151210-020847', 372, 17),
	('20151210-020854', 463, 17),
	('20151210-021131', 558, 17),
	('20151210-021141', 499, 17),
	('20151210-021225', 249, 17),
	('20151210-021232', 318, 17),
	('20151210-021238', 455, 17),
	('20151210-021257', 427, 17),
	('20151211-114400', 663, 26),
	('20151211-114401', 664, 26),
	('20160303-024249', 761, 2),
	('20160311-070741', 66, 27),
	('20151222-115157', 463, 34),
	('20151222-115231', 694, 34),
	('20151222-115233', 688, 34),
	('20151228-082912', 560, 37),
	('20160104-021104', 264, 19),
	('20160104-022017', 1, 39),
	('20160104-022021', 264, 39),
	('20160104-022024', 267, 39),
	('20160104-022027', 363, 39),
	('20160105-102426', 723, 2),
	('20160106-100310', 701, 9),
	('20160107-095047', 68, 25),
	('20160116-042709', 741, 2),
	('20160113-084912', 1, 27),
	('20160116-042723', 741, 33),
	('20160116-042732', 741, 16),
	('20160120-020604', 705, 28),
	('20160125-032941', 715, 33),
	('20160125-032943', 716, 33),
	('20160125-032946', 717, 33),
	('20160125-032947', 719, 33),
	('20160201-044321', 354, 37),
	('20160511-090920', 523, 12),
	('20160210-090448', 745, 38),
	('20160210-092210', 253, 33),
	('20160212-103324', 689, 34),
	('20160212-103328', 692, 34),
	('20160216-021411', 749, 2),
	('20160216-021415', 751, 2),
	('20160216-021418', 752, 2),
	('20160217-083557', 675, 34),
	('20160218-035402', 466, 34),
	('20160502-112926', 600, 43),
	('20160502-112933', 1, 43),
	('20160220-113857', 749, 10),
	('20160220-113858', 751, 10),
	('20160220-113900', 752, 10),
	('20160220-113901', 753, 10),
	('20160220-113903', 754, 10),
	('20160220-113929', 749, 27),
	('20160220-113930', 751, 27),
	('20160220-113932', 752, 27),
	('20160220-113933', 753, 27),
	('20160220-113934', 754, 27),
	('20160223-082615', 756, 2),
	('20160226-101429', 510, 40),
	('20160226-101445', 384, 40),
	('20160226-101447', 117, 40),
	('20160226-101502', 102, 40),
	('20160226-101503', 338, 40),
	('20160226-101505', 340, 40),
	('20160226-101524', 1, 40),
	('20160226-101534', 671, 40),
	('20160226-101536', 672, 40),
	('20160226-101538', 675, 40),
	('20160226-101545', 688, 40),
	('20160226-101546', 689, 40),
	('20160226-101548', 692, 40),
	('20160226-101550', 694, 40),
	('20160226-101619', 427, 40),
	('20160226-101652', 346, 40),
	('20160226-101732', 536, 40),
	('20160226-101944', 291, 40),
	('20160226-102119', 466, 40),
	('20160226-102132', 327, 40),
	('20160226-102137', 125, 40),
	('20160226-102143', 328, 40),
	('20160226-102211', 576, 40),
	('20160226-102306', 363, 40),
	('20160226-102321', 354, 40),
	('20160226-102324', 372, 40),
	('20160226-102328', 434, 40),
	('20160226-102330', 480, 40),
	('20160226-102333', 463, 40),
	('20160226-102335', 525, 40),
	('20160226-102339', 529, 40),
	('20160226-104025', 357, 40),
	('20160226-104026', 359, 40),
	('20160226-104052', 513, 40),
	('20160226-104102', 703, 40),
	('20160226-104305', 293, 40),
	('20160226-104915', 568, 40),
	('20160226-104916', 578, 40),
	('20160226-104917', 579, 40),
	('20160226-104920', 581, 40),
	('20160226-104922', 586, 40),
	('20160226-104924', 594, 40),
	('20160226-104925', 678, 40),
	('20160226-105152', 543, 40),
	('20160226-105516', 497, 40),
	('20160226-105623', 318, 40),
	('20160226-105628', 455, 40),
	('20160226-105721', 320, 40),
	('20160226-105834', 322, 40),
	('20160226-105837', 323, 40),
	('20160226-105849', 499, 40),
	('20160226-105853', 523, 40),
	('20160226-105900', 648, 40),
	('20160226-110719', 437, 40),
	('20160226-112819', 747, 40),
	('20160226-112838', 714, 40),
	('20160226-112847', 698, 40),
	('20160226-112849', 702, 40),
	('20160226-112852', 705, 40),
	('20160226-112906', 734, 40),
	('20160226-112907', 735, 40),
	('20160226-113823', 388, 40),
	('20160226-113832', 395, 40),
	('20160226-113905', 558, 40),
	('20160226-113911', 600, 40),
	('20160226-113913', 605, 40),
	('20160226-113919', 622, 40),
	('20160226-034652', 117, 34),
	('20160226-034658', 384, 34),
	('20160229-012000', 756, 9),
	('20160301-022241', 267, 40),
	('20160301-022248', 365, 40),
	('20160301-022257', 107, 40),
	('20160301-023213', 167, 40),
	('20160301-022311', 258, 40),
	('20160301-022316', 265, 40),
	('20160301-022318', 266, 40),
	('20160301-022438', 78, 40),
	('20160301-022633', 730, 40),
	('20160301-022645', 137, 40),
	('20160301-022718', 229, 40),
	('20160301-022721', 236, 40),
	('20160301-022756', 677, 40),
	('20160301-022828', 387, 40),
	('20160301-022840', 350, 40),
	('20160301-022904', 664, 40),
	('20160301-022906', 667, 40),
	('20160301-022929', 486, 40),
	('20160301-023218', 254, 40),
	('20160301-023241', 344, 40),
	('20160301-023412', 701, 40),
	('20160301-023456', 332, 40),
	('20160302-023029', 759, 2),
	('20160302-060523', 427, 9),
	('20160308-102423', 291, 41),
	('20160308-102435', 701, 41),
	('20160308-102738', 736, 41),
	('20160308-102803', 705, 41),
	('20160308-102833', 714, 41),
	('20160308-103119', 293, 41),
	('20160317-081653', 520, 40),
	('20160317-081937', 264, 40),
	('20160317-081942', 105, 40),
	('20160317-081948', 184, 40),
	('20160317-081952', 232, 40),
	('20160317-081953', 263, 40),
	('20160317-082010', 66, 40),
	('20160317-082013', 67, 40),
	('20160317-082026', 500, 40),
	('20160317-082029', 630, 40),
	('20160317-082057', 134, 40),
	('20160317-082134', 333, 40),
	('20160317-082137', 342, 40),
	('20160317-082141', 457, 40),
	('20160317-082145', 575, 40),
	('20160317-082204', 68, 40),
	('20160317-082213', 273, 40),
	('20160317-082221', 75, 40),
	('20160317-082234', 249, 40),
	('20160317-082236', 250, 40),
	('20160317-082240', 334, 40),
	('20160317-082242', 335, 40),
	('20160317-082258', 351, 40),
	('20160317-082311', 228, 40),
	('20160317-082317', 637, 40),
	('20160317-082341', 231, 40),
	('20160317-082342', 230, 40),
	('20160317-082355', 439, 40),
	('20160317-082542', 635, 40),
	('20160317-084319', 483, 40),
	('20160317-082612', 204, 40),
	('20160317-082623', 216, 40),
	('20160317-082641', 574, 40),
	('20160317-082642', 610, 40),
	('20160317-082729', 490, 40),
	('20160317-082732', 533, 40),
	('20160317-082909', 560, 40),
	('20160317-082915', 607, 40),
	('20160317-083210', 251, 40),
	('20160317-083231', 618, 40),
	('20160317-083256', 501, 40),
	('20160317-083311', 476, 40),
	('20160317-083317', 458, 40),
	('20160317-083332', 503, 40),
	('20160317-083340', 464, 40),
	('20160319-044255', 763, 2),
	('20160317-083638', 370, 40),
	('20160317-084320', 749, 40),
	('20160317-084321', 751, 40),
	('20160317-084323', 752, 40),
	('20160317-084324', 753, 40),
	('20160317-084326', 754, 40),
	('20160317-084658', 345, 40),
	('20160404-020116', 766, 7),
	('20160404-020122', 765, 7),
	('20160406-101806', 608, 7),
	('20160406-110833', 767, 2),
	('20160413-102847', 770, 2),
	('20160419-020258', 773, 2),
	('20160523-045518', 500, 37),
	('20160523-051014', 513, 37),
	('20160601-030617', 770, 22),
	('20160610-042122', 471, 9),
	('20160622-023012', 184, 7),
	('20160622-023016', 777, 7),
	('20160627-024622', 780, 37),
	('20160628-114332', 780, 26),
	('20170813-105401', 66, 44),
	('20170813-105409', 273, 44),
	('20170813-105541', 588, 44),
	('20170813-105549', 75, 44),
	('20170813-110220', 251, 44),
	('20170813-110336', 470, 44),
	('20170813-110414', 696, 44),
	('20170813-111555', 135, 44),
	('20170813-111622', 134, 44),
	('20170813-111709', 534, 44),
	('20170813-111750', 136, 44),
	('20170813-111752', 137, 44),
	('20170813-125301', 264, 44),
	('20170813-125309', 267, 44),
	('20170813-012802', 165, 44),
	('20170813-012819', 254, 44),
	('20170813-012834', 167, 44),
	('20170813-013716', 184, 44),
	('20170813-013717', 258, 44),
	('20170813-014350', 1, 44),
	('20170813-014444', 349, 44),
	('20170813-014456', 350, 44),
	('20170813-014546', 385, 44),
	('20170813-014556', 316, 44),
	('20170813-022127', 567, 44),
	('20170813-022128', 568, 44),
	('20170813-022406', 351, 44),
	('20170813-055445', 293, 44),
	('20170813-055122', 736, 44),
	('20170813-055458', 291, 44),
	('20170813-075212', 357, 44),
	('20170813-075213', 359, 44),
	('20170813-075258', 434, 44),
	('20170813-081418', 282, 44);
/*!40000 ALTER TABLE `my_privileges` ENABLE KEYS */;


-- Dumping structure for table default_db.official_logbook
CREATE TABLE IF NOT EXISTS `official_logbook` (
  `official_logbook_id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(12) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `from_project_id` bigint(12) DEFAULT NULL,
  `to_project_id` bigint(12) DEFAULT NULL,
  `notes` text,
  `user_id` varchar(100) DEFAULT NULL,
  `status` char(1) DEFAULT 'S',
  PRIMARY KEY (`official_logbook_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.official_logbook: 0 rows
/*!40000 ALTER TABLE `official_logbook` DISABLE KEYS */;
/*!40000 ALTER TABLE `official_logbook` ENABLE KEYS */;


-- Dumping structure for table default_db.order_details
CREATE TABLE IF NOT EXISTS `order_details` (
  `order_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `order_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `formulation_header_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`order_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.order_details: 0 rows
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;


-- Dumping structure for table default_db.order_header
CREATE TABLE IF NOT EXISTS `order_header` (
  `order_header_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(8) NOT NULL,
  `time` varchar(30) DEFAULT NULL,
  `date` date NOT NULL,
  `netamount` decimal(12,2) NOT NULL,
  `user_id` varchar(30) NOT NULL,
  `status` char(1) NOT NULL,
  `remarks` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`order_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.order_header: 0 rows
/*!40000 ALTER TABLE `order_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_header` ENABLE KEYS */;


-- Dumping structure for table default_db.package
CREATE TABLE IF NOT EXISTS `package` (
  `package_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `packagecode` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `qty` decimal(12,3) NOT NULL,
  `unit` varchar(15) NOT NULL,
  PRIMARY KEY (`package_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.package: 0 rows
/*!40000 ALTER TABLE `package` DISABLE KEYS */;
/*!40000 ALTER TABLE `package` ENABLE KEYS */;


-- Dumping structure for table default_db.packaging
CREATE TABLE IF NOT EXISTS `packaging` (
  `packaging_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `piece_stock_id` bigint(12) NOT NULL,
  `piece_quantity` decimal(12,2) NOT NULL,
  `pack_stock_id` bigint(12) NOT NULL,
  `pack_quantity` decimal(12,2) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `date` date NOT NULL,
  PRIMARY KEY (`packaging_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.packaging: 0 rows
/*!40000 ALTER TABLE `packaging` DISABLE KEYS */;
/*!40000 ALTER TABLE `packaging` ENABLE KEYS */;


-- Dumping structure for table default_db.pack_detail
CREATE TABLE IF NOT EXISTS `pack_detail` (
  `pack_detail_id` bigint(20) NOT NULL,
  `pack_header_id` bigint(16) NOT NULL,
  `joborder_id` bigint(16) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `qty` decimal(12,3) NOT NULL,
  PRIMARY KEY (`pack_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pack_detail: 0 rows
/*!40000 ALTER TABLE `pack_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `pack_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.pack_header
CREATE TABLE IF NOT EXISTS `pack_header` (
  `pack_header_id` bigint(16) NOT NULL,
  `date` date NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `qty` decimal(12,3) NOT NULL,
  `remarks` varchar(100) NOT NULL,
  `user_id` bigint(8) NOT NULL,
  `audit` blob NOT NULL,
  PRIMARY KEY (`pack_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pack_header: 0 rows
/*!40000 ALTER TABLE `pack_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `pack_header` ENABLE KEYS */;


-- Dumping structure for table default_db.part_file_list
CREATE TABLE IF NOT EXISTS `part_file_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `stock_id` int(10) NOT NULL,
  `kms_run` varchar(100) NOT NULL,
  `dys_run` varchar(100) NOT NULL,
  `is_deleted` int(10) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.part_file_list: 0 rows
/*!40000 ALTER TABLE `part_file_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `part_file_list` ENABLE KEYS */;


-- Dumping structure for table default_db.payment_detail
CREATE TABLE IF NOT EXISTS `payment_detail` (
  `payment_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `payment_header_id` bigint(12) NOT NULL,
  `dr_header_id` bigint(16) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  PRIMARY KEY (`payment_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.payment_detail: 0 rows
/*!40000 ALTER TABLE `payment_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.payroll_accumulator
CREATE TABLE IF NOT EXISTS `payroll_accumulator` (
  `paID` bigint(20) NOT NULL AUTO_INCREMENT,
  `basic_salary` decimal(10,3) NOT NULL,
  `sss` decimal(10,3) NOT NULL,
  `philhealth` decimal(10,3) DEFAULT NULL,
  `hdmf` decimal(10,3) DEFAULT NULL,
  `taxes` decimal(10,3) DEFAULT NULL,
  `ca_deduction` decimal(10,3) DEFAULT NULL,
  `pa_from` date NOT NULL,
  `pa_to` date NOT NULL,
  `empID` bigint(20) NOT NULL,
  `payroll_sequence` int(2) NOT NULL,
  `allowance` decimal(12,2) NOT NULL,
  `rmy_lending` decimal(12,2) NOT NULL,
  `canteen` decimal(12,2) NOT NULL,
  `house_rental` decimal(12,2) NOT NULL,
  `personal_chargables` decimal(12,2) NOT NULL,
  `regular_hrs_ot` decimal(12,2) NOT NULL,
  `regular_ot_amount` decimal(12,2) NOT NULL,
  `special_hrs_ot` decimal(12,2) NOT NULL,
  `special_ot_amount` decimal(12,2) NOT NULL,
  `legal_hrs_ot` decimal(12,2) NOT NULL,
  `legal_ot_amount` decimal(12,2) NOT NULL,
  `no_of_days` decimal(12,2) NOT NULL,
  `pagibig_loan` decimal(12,2) NOT NULL,
  `sss_loan` decimal(12,2) NOT NULL,
  `no_of_absences` decimal(12,2) NOT NULL,
  `canteen_ded_header_id` bigint(12) NOT NULL,
  `total_deductions` decimal(12,2) NOT NULL,
  `net_amount` decimal(12,2) NOT NULL,
  `gross` decimal(12,2) NOT NULL,
  `out_bal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`paID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.payroll_accumulator: 0 rows
/*!40000 ALTER TABLE `payroll_accumulator` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_accumulator` ENABLE KEYS */;


-- Dumping structure for table default_db.payroll_sequence
CREATE TABLE IF NOT EXISTS `payroll_sequence` (
  `payroll_sequence_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`payroll_sequence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.payroll_sequence: ~0 rows (approximately)
/*!40000 ALTER TABLE `payroll_sequence` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_sequence` ENABLE KEYS */;


-- Dumping structure for table default_db.pay_checks
CREATE TABLE IF NOT EXISTS `pay_checks` (
  `pay_check_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `pay_header_id` bigint(12) NOT NULL,
  `bank` varchar(25) NOT NULL,
  `checkno` varchar(20) NOT NULL,
  `datecheck` date NOT NULL,
  `checkamount` decimal(14,2) NOT NULL,
  `checkstatus` varchar(12) NOT NULL,
  PRIMARY KEY (`pay_check_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pay_checks: 0 rows
/*!40000 ALTER TABLE `pay_checks` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_checks` ENABLE KEYS */;


-- Dumping structure for table default_db.pay_header
CREATE TABLE IF NOT EXISTS `pay_header` (
  `pay_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `reference` varchar(255) NOT NULL,
  `account_id` int(8) NOT NULL,
  `total_amount` decimal(14,2) NOT NULL,
  `remarks` blob NOT NULL,
  `status` char(1) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `audit` blob NOT NULL,
  `locale_id` int(4) NOT NULL,
  PRIMARY KEY (`pay_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pay_header: 0 rows
/*!40000 ALTER TABLE `pay_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `pay_header` ENABLE KEYS */;


-- Dumping structure for table default_db.petty_cash
CREATE TABLE IF NOT EXISTS `petty_cash` (
  `petty_cash_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `department_id` bigint(12) NOT NULL,
  `employeeID` bigint(12) NOT NULL,
  `approve_by` bigint(12) NOT NULL,
  `amount` decimal(9,2) NOT NULL,
  `returned_amount` decimal(9,2) NOT NULL,
  `liquidated_amount` decimal(9,2) NOT NULL,
  `difference` decimal(9,2) NOT NULL,
  `purpose` text NOT NULL,
  `remarks` text NOT NULL,
  `date_requested` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_approved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_target_liquidation` datetime NOT NULL,
  `date_liquidated` datetime NOT NULL,
  `is_approve` int(10) NOT NULL,
  `is_deleted` int(10) NOT NULL,
  `is_liquidated` int(10) NOT NULL,
  `is_replenish` int(10) NOT NULL,
  `date_replenish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`petty_cash_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.petty_cash: 0 rows
/*!40000 ALTER TABLE `petty_cash` DISABLE KEYS */;
/*!40000 ALTER TABLE `petty_cash` ENABLE KEYS */;


-- Dumping structure for table default_db.petty_cash_budget
CREATE TABLE IF NOT EXISTS `petty_cash_budget` (
  `pc_budget_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `amount` decimal(9,2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` int(10) NOT NULL,
  `is_liquidation` int(10) NOT NULL,
  `petty_cash_id` int(11) NOT NULL,
  PRIMARY KEY (`pc_budget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.petty_cash_budget: 0 rows
/*!40000 ALTER TABLE `petty_cash_budget` DISABLE KEYS */;
/*!40000 ALTER TABLE `petty_cash_budget` ENABLE KEYS */;


-- Dumping structure for table default_db.petty_cash_budget_rjr
CREATE TABLE IF NOT EXISTS `petty_cash_budget_rjr` (
  `pc_budget_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `amount` decimal(9,2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_deleted` int(10) NOT NULL,
  PRIMARY KEY (`pc_budget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.petty_cash_budget_rjr: 0 rows
/*!40000 ALTER TABLE `petty_cash_budget_rjr` DISABLE KEYS */;
/*!40000 ALTER TABLE `petty_cash_budget_rjr` ENABLE KEYS */;


-- Dumping structure for table default_db.petty_cash_rjr
CREATE TABLE IF NOT EXISTS `petty_cash_rjr` (
  `petty_cash_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `amount` decimal(9,2) NOT NULL,
  `returned_amount` decimal(9,2) NOT NULL,
  `difference` decimal(9,2) NOT NULL,
  `purpose` text NOT NULL,
  `remarks` text NOT NULL,
  `date_requested` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_target_liquidation` datetime NOT NULL,
  `date_liquidated` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `is_deleted` int(10) NOT NULL,
  `is_liquidated` int(10) NOT NULL,
  PRIMARY KEY (`petty_cash_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.petty_cash_rjr: 0 rows
/*!40000 ALTER TABLE `petty_cash_rjr` DISABLE KEYS */;
/*!40000 ALTER TABLE `petty_cash_rjr` ENABLE KEYS */;


-- Dumping structure for table default_db.philhealth_contrib
CREATE TABLE IF NOT EXISTS `philhealth_contrib` (
  `ph_contribID` int(10) NOT NULL AUTO_INCREMENT,
  `ph_range_min` decimal(10,2) NOT NULL,
  `ph_range_max` decimal(10,2) NOT NULL,
  `base_salary` decimal(10,2) NOT NULL,
  `total_monthly_premium` decimal(10,2) NOT NULL,
  `employee_share` decimal(10,2) NOT NULL,
  `employer_share` decimal(10,2) NOT NULL,
  `phvoid` char(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ph_contribID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.philhealth_contrib: 0 rows
/*!40000 ALTER TABLE `philhealth_contrib` DISABLE KEYS */;
/*!40000 ALTER TABLE `philhealth_contrib` ENABLE KEYS */;


-- Dumping structure for table default_db.pmrecipients
CREATE TABLE IF NOT EXISTS `pmrecipients` (
  `id` varchar(200) NOT NULL,
  `userID` varchar(200) NOT NULL,
  `read_` varchar(6) NOT NULL DEFAULT 'Unread',
  `pmID` varchar(200) NOT NULL,
  `read_when` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pmrecipients: 0 rows
/*!40000 ALTER TABLE `pmrecipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `pmrecipients` ENABLE KEYS */;


-- Dumping structure for table default_db.posted_headers
CREATE TABLE IF NOT EXISTS `posted_headers` (
  `posted_header_id` bigint(50) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` bigint(20) NOT NULL,
  `journal_code` varchar(20) NOT NULL,
  `gltran_header_id` bigint(20) NOT NULL,
  `header` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`posted_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.posted_headers: 0 rows
/*!40000 ALTER TABLE `posted_headers` DISABLE KEYS */;
/*!40000 ALTER TABLE `posted_headers` ENABLE KEYS */;


-- Dumping structure for table default_db.po_cancellation
CREATE TABLE IF NOT EXISTS `po_cancellation` (
  `cancellation_id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `po_header_id` bigint(16) NOT NULL,
  `date` date NOT NULL,
  `supplier_id` bigint(8) NOT NULL,
  `project_id` bigint(8) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `justification` text,
  `userID` varchar(50) DEFAULT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`cancellation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.po_cancellation: 0 rows
/*!40000 ALTER TABLE `po_cancellation` DISABLE KEYS */;
/*!40000 ALTER TABLE `po_cancellation` ENABLE KEYS */;


-- Dumping structure for table default_db.po_detail
CREATE TABLE IF NOT EXISTS `po_detail` (
  `po_detail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `po_header_id` bigint(16) unsigned NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(10,4) NOT NULL,
  `cost` decimal(12,4) NOT NULL,
  `discount` decimal(12,4) NOT NULL,
  `amount` decimal(14,4) NOT NULL,
  `details` varchar(100) DEFAULT NULL,
  `_unit` varchar(50) DEFAULT NULL,
  `_type` char(1) DEFAULT 'M',
  `chargables` text,
  `person` text,
  `factor` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`po_detail_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.po_detail: 1 rows
/*!40000 ALTER TABLE `po_detail` DISABLE KEYS */;
INSERT INTO `po_detail` (`po_detail_id`, `po_header_id`, `stock_id`, `quantity`, `cost`, `discount`, `amount`, `details`, `_unit`, `_type`, `chargables`, `person`, `factor`) VALUES
	(1, 2, 1, 2.0000, 20.0000, 0.0000, 40.0000, '', NULL, 'M', '', '', NULL);
/*!40000 ALTER TABLE `po_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.po_equipment_detail
CREATE TABLE IF NOT EXISTS `po_equipment_detail` (
  `po_equipment_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `po_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `rate_per_day` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`po_equipment_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.po_equipment_detail: 0 rows
/*!40000 ALTER TABLE `po_equipment_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `po_equipment_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.po_fuel_detail
CREATE TABLE IF NOT EXISTS `po_fuel_detail` (
  `po_fuel_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `po_header_id` bigint(12) NOT NULL,
  `fuel_id` bigint(12) NOT NULL,
  `equipment_id` bigint(12) NOT NULL,
  `consumption_per_day` decimal(12,2) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `cost_per_litter` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `request_quantity` decimal(12,2) NOT NULL,
  `warehouse_quantity` decimal(12,2) NOT NULL,
  PRIMARY KEY (`po_fuel_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.po_fuel_detail: 0 rows
/*!40000 ALTER TABLE `po_fuel_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `po_fuel_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.po_header
CREATE TABLE IF NOT EXISTS `po_header` (
  `po_header_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `status` char(1) NOT NULL,
  `pr_header_id` bigint(16) NOT NULL,
  `payroll_header_id` int(10) NOT NULL,
  `supplier_id` bigint(8) NOT NULL,
  `terms` varchar(50) NOT NULL,
  `scope_of_work` varchar(100) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `approval_status` char(1) DEFAULT 'P',
  `approved_by` varchar(30) DEFAULT NULL,
  `remarks` text,
  `po_type` char(1) NOT NULL DEFAULT 'M',
  `ap_gchart_id` bigint(12) DEFAULT NULL,
  `expense_gchart_id` bigint(12) DEFAULT NULL,
  `wtax` decimal(12,2) DEFAULT NULL,
  `vat` decimal(12,2) DEFAULT NULL,
  `discount_amount` decimal(12,2) DEFAULT NULL,
  `budget_header_id` int(10) NOT NULL,
  `note` text,
  `closed` char(1) DEFAULT '0',
  `date_closed` date NOT NULL,
  `no_of_days_delivery` decimal(12,2) NOT NULL,
  `datetime_encoded` datetime DEFAULT NULL,
  PRIMARY KEY (`po_header_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.po_header: 2 rows
/*!40000 ALTER TABLE `po_header` DISABLE KEYS */;
INSERT INTO `po_header` (`po_header_id`, `project_id`, `date`, `user_id`, `status`, `pr_header_id`, `payroll_header_id`, `supplier_id`, `terms`, `scope_of_work`, `work_category_id`, `sub_work_category_id`, `approval_status`, `approved_by`, `remarks`, `po_type`, `ap_gchart_id`, `expense_gchart_id`, `wtax`, `vat`, `discount_amount`, `budget_header_id`, `note`, `closed`, `date_closed`, `no_of_days_delivery`, `datetime_encoded`) VALUES
	(1, 1, '2017-08-13', '20170813-105326', 'C', 2, 0, 1, '', '', 4, 0, 'P', NULL, 'test', 'M', NULL, NULL, 1.00, 0.00, 0.00, 0, '', '0', '0000-00-00', 20.00, '2017-08-13 21:08:12'),
	(2, 1, '2017-08-13', '20170813-105326', 'S', 2, 0, 1, '30', '', 4, 0, 'A', NULL, 'test', 'M', NULL, NULL, 1.00, 0.00, 0.00, 0, '', '0', '0000-00-00', 30.00, '2017-08-13 21:11:04');
/*!40000 ALTER TABLE `po_header` ENABLE KEYS */;


-- Dumping structure for table default_db.po_header_payroll
CREATE TABLE IF NOT EXISTS `po_header_payroll` (
  `payroll_header_details` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_header_id` int(11) NOT NULL,
  `overtime` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  PRIMARY KEY (`payroll_header_details`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.po_header_payroll: 0 rows
/*!40000 ALTER TABLE `po_header_payroll` DISABLE KEYS */;
/*!40000 ALTER TABLE `po_header_payroll` ENABLE KEYS */;


-- Dumping structure for table default_db.po_header_payroll_det
CREATE TABLE IF NOT EXISTS `po_header_payroll_det` (
  `payroll_header_id` int(10) NOT NULL AUTO_INCREMENT,
  `po_header_id` int(11) NOT NULL,
  PRIMARY KEY (`payroll_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.po_header_payroll_det: 0 rows
/*!40000 ALTER TABLE `po_header_payroll_det` DISABLE KEYS */;
/*!40000 ALTER TABLE `po_header_payroll_det` ENABLE KEYS */;


-- Dumping structure for table default_db.po_service_detail
CREATE TABLE IF NOT EXISTS `po_service_detail` (
  `po_service_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `po_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `rate_per_day` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`po_service_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.po_service_detail: 0 rows
/*!40000 ALTER TABLE `po_service_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `po_service_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.premix_delivery
CREATE TABLE IF NOT EXISTS `premix_delivery` (
  `premix_delivery_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `batch_no` bigint(10) DEFAULT NULL,
  `project_id` bigint(12) NOT NULL,
  `premix_id` bigint(12) unsigned DEFAULT NULL,
  `volume` decimal(12,4) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `equipment_id` bigint(12) unsigned NOT NULL,
  `remarks` text,
  `reference` varchar(20) NOT NULL,
  `driver_id` bigint(12) NOT NULL,
  `pl_operator` bigint(12) NOT NULL,
  `bp_operator` bigint(12) NOT NULL,
  `encoded_by_id` varchar(100) DEFAULT NULL,
  `encoded_datetime` datetime DEFAULT NULL,
  `updated_by_id` varchar(100) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `checked_by_id` varchar(100) DEFAULT NULL,
  `status` char(1) DEFAULT 'S',
  `pumpcrete_cost` decimal(12,2) NOT NULL,
  PRIMARY KEY (`premix_delivery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.premix_delivery: 0 rows
/*!40000 ALTER TABLE `premix_delivery` DISABLE KEYS */;
/*!40000 ALTER TABLE `premix_delivery` ENABLE KEYS */;


-- Dumping structure for table default_db.premix_quotation_detail
CREATE TABLE IF NOT EXISTS `premix_quotation_detail` (
  `premix_quotation_detail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `premix_quotation_header_id` bigint(20) unsigned NOT NULL,
  `premix_desc` varchar(100) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `premix_cost` decimal(12,2) NOT NULL,
  `pumpcrete_cost` decimal(12,2) NOT NULL,
  `premix_amount` decimal(12,2) NOT NULL,
  `pumpcrete_amount` decimal(12,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `premix_quotation_void` char(1) NOT NULL DEFAULT '0',
  `stock_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`premix_quotation_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.premix_quotation_detail: 0 rows
/*!40000 ALTER TABLE `premix_quotation_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `premix_quotation_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.premix_quotation_header
CREATE TABLE IF NOT EXISTS `premix_quotation_header` (
  `premix_quotation_header_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `client_info` text NOT NULL,
  `client_address` text NOT NULL,
  `remarks` text NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `prepared_by` varchar(100) NOT NULL,
  `noted_by` bigint(12) NOT NULL,
  `encoded_datetime` datetime NOT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`premix_quotation_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.premix_quotation_header: 0 rows
/*!40000 ALTER TABLE `premix_quotation_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `premix_quotation_header` ENABLE KEYS */;


-- Dumping structure for table default_db.preturn_detail
CREATE TABLE IF NOT EXISTS `preturn_detail` (
  `preturn_detail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `preturn_header_id` bigint(12) unsigned NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `preturn_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`preturn_detail_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.preturn_detail: 0 rows
/*!40000 ALTER TABLE `preturn_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `preturn_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.preturn_details
CREATE TABLE IF NOT EXISTS `preturn_details` (
  `preturn_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `preturn_header_id` bigint(12) NOT NULL,
  `stock_id` int(8) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  PRIMARY KEY (`preturn_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.preturn_details: 0 rows
/*!40000 ALTER TABLE `preturn_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `preturn_details` ENABLE KEYS */;


-- Dumping structure for table default_db.preturn_header
CREATE TABLE IF NOT EXISTS `preturn_header` (
  `preturn_header_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `supplier_id` bigint(12) unsigned NOT NULL,
  `reference` varchar(100) NOT NULL,
  `remarks` text,
  `status` char(1) NOT NULL DEFAULT 'S',
  `prepared_by` varchar(100) NOT NULL,
  `prepared_time` datetime NOT NULL,
  `edited_by` varchar(100) DEFAULT NULL,
  `last_edit_time` datetime DEFAULT NULL,
  PRIMARY KEY (`preturn_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.preturn_header: 0 rows
/*!40000 ALTER TABLE `preturn_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `preturn_header` ENABLE KEYS */;


-- Dumping structure for table default_db.private_messages
CREATE TABLE IF NOT EXISTS `private_messages` (
  `id` varchar(200) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `privatemsg` text NOT NULL,
  `date_sent` datetime DEFAULT NULL,
  `sent_by` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.private_messages: 0 rows
/*!40000 ALTER TABLE `private_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `private_messages` ENABLE KEYS */;


-- Dumping structure for table default_db.production
CREATE TABLE IF NOT EXISTS `production` (
  `production_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `stock_id` bigint(12) NOT NULL,
  `required` decimal(12,2) NOT NULL,
  `actual` decimal(12,2) DEFAULT NULL,
  `formulation_header_id` bigint(12) DEFAULT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `date` date NOT NULL,
  `buffer` decimal(12,2) DEFAULT NULL,
  `orders` decimal(12,2) DEFAULT NULL,
  `beginning_balance` decimal(12,2) NOT NULL,
  PRIMARY KEY (`production_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.production: 0 rows
/*!40000 ALTER TABLE `production` DISABLE KEYS */;
/*!40000 ALTER TABLE `production` ENABLE KEYS */;


-- Dumping structure for table default_db.production_detail
CREATE TABLE IF NOT EXISTS `production_detail` (
  `production_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `production_header_id` bigint(16) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`production_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.production_detail: 0 rows
/*!40000 ALTER TABLE `production_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `production_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.production_formulations
CREATE TABLE IF NOT EXISTS `production_formulations` (
  `production_formulation_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `production_id` bigint(16) NOT NULL,
  `formulation_header_id` bigint(12) NOT NULL,
  PRIMARY KEY (`production_formulation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.production_formulations: 0 rows
/*!40000 ALTER TABLE `production_formulations` DISABLE KEYS */;
/*!40000 ALTER TABLE `production_formulations` ENABLE KEYS */;


-- Dumping structure for table default_db.production_header
CREATE TABLE IF NOT EXISTS `production_header` (
  `production_header_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `formulation_header_id` bigint(12) NOT NULL,
  `actualoutput` decimal(12,2) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(30) NOT NULL,
  PRIMARY KEY (`production_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.production_header: 0 rows
/*!40000 ALTER TABLE `production_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `production_header` ENABLE KEYS */;


-- Dumping structure for table default_db.productmaster
CREATE TABLE IF NOT EXISTS `productmaster` (
  `stock_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `stockcode` varchar(100) DEFAULT NULL,
  `barcode` varchar(20) DEFAULT NULL,
  `stock` text NOT NULL,
  `description` text NOT NULL,
  `type` varchar(2) NOT NULL,
  `categ_id1` int(2) NOT NULL,
  `categ_id2` int(2) NOT NULL,
  `categ_id3` int(2) NOT NULL,
  `categ_id4` int(2) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `price1` decimal(12,2) NOT NULL,
  `price2` decimal(12,2) NOT NULL,
  `price3` decimal(12,2) NOT NULL,
  `price4` decimal(12,2) NOT NULL,
  `price5` decimal(12,2) NOT NULL,
  `price6` decimal(12,2) NOT NULL,
  `price7` decimal(12,2) NOT NULL,
  `price8` decimal(12,2) NOT NULL,
  `price9` decimal(12,2) NOT NULL,
  `price10` decimal(12,2) NOT NULL,
  `reorderlevel` decimal(8,0) NOT NULL,
  `reorderqty` decimal(12,0) NOT NULL,
  `supplier_id` bigint(8) DEFAULT NULL,
  `manysuppliers` char(1) NOT NULL,
  `picname` varchar(20) NOT NULL,
  `piclocate` varchar(20) NOT NULL,
  `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` char(1) NOT NULL DEFAULT 'S',
  `audit` blob NOT NULL,
  `buffer` decimal(12,2) DEFAULT NULL,
  `eq_catID` bigint(12) DEFAULT NULL,
  `parent_stock_id` bigint(12) DEFAULT NULL,
  `rate_per_hour` decimal(12,2) DEFAULT NULL,
  `min_time` decimal(12,2) DEFAULT NULL,
  `plate_num` varchar(50) DEFAULT NULL,
  `eq_model` varchar(50) DEFAULT NULL,
  `batching_plant_categ_id` bigint(12) NOT NULL,
  `kg` decimal(12,4) DEFAULT '0.0000',
  `budget_category` char(1) DEFAULT 'M',
  `standard_comparison` double(10,2) NOT NULL,
  `e_status` int(10) NOT NULL DEFAULT '1',
  `stock_length` decimal(12,2) NOT NULL,
  `fabrication_raw_mat_parent_id` bigint(12) unsigned NOT NULL,
  `branding_number` varchar(100) NOT NULL,
  `size` varchar(100) NOT NULL,
  `tire_type` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `manufacturer` varchar(100) NOT NULL,
  PRIMARY KEY (`stock_id`),
  KEY `unit` (`unit`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.productmaster: 1 rows
/*!40000 ALTER TABLE `productmaster` DISABLE KEYS */;
INSERT INTO `productmaster` (`stock_id`, `stockcode`, `barcode`, `stock`, `description`, `type`, `categ_id1`, `categ_id2`, `categ_id3`, `categ_id4`, `unit`, `cost`, `price1`, `price2`, `price3`, `price4`, `price5`, `price6`, `price7`, `price8`, `price9`, `price10`, `reorderlevel`, `reorderqty`, `supplier_id`, `manysuppliers`, `picname`, `piclocate`, `dateadded`, `status`, `audit`, `buffer`, `eq_catID`, `parent_stock_id`, `rate_per_hour`, `min_time`, `plate_num`, `eq_model`, `batching_plant_categ_id`, `kg`, `budget_category`, `standard_comparison`, `e_status`, `stock_length`, `fabrication_raw_mat_parent_id`, `branding_number`, `size`, `tire_type`, `brand`, `manufacturer`) VALUES
	(1, '1212', '', 'sample', '', '', 0, 0, 0, 0, 'KG', 20.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', '', '', '2016-07-20 16:21:19', 'S', _binary '', 0.00, 0, 0, 0.00, 0.00, '', '', 0, 0.0000, '', 0.00, 0, 0.00, 0, '', '', 0, '', '');
/*!40000 ALTER TABLE `productmaster` ENABLE KEYS */;


-- Dumping structure for table default_db.product_convert
CREATE TABLE IF NOT EXISTS `product_convert` (
  `product_convert_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `locale_id` int(4) NOT NULL,
  `finishedproduct_id` bigint(12) NOT NULL,
  `packagetype` varchar(40) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `packqty` int(6) NOT NULL,
  `qty` int(8) NOT NULL,
  `audit` blob NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  `quantity` decimal(12,3) DEFAULT NULL,
  PRIMARY KEY (`product_convert_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.product_convert: 0 rows
/*!40000 ALTER TABLE `product_convert` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_convert` ENABLE KEYS */;


-- Dumping structure for table default_db.programs
CREATE TABLE IF NOT EXISTS `programs` (
  `PCode` int(10) NOT NULL AUTO_INCREMENT,
  `Pfilename` varchar(255) NOT NULL,
  `view_keyword` varchar(20) DEFAULT NULL,
  `enabled` char(1) DEFAULT '1',
  `protect` char(1) DEFAULT '0',
  `Fdescription` text,
  PRIMARY KEY (`PCode`)
) ENGINE=MyISAM AUTO_INCREMENT=784 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.programs: 693 rows
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
INSERT INTO `programs` (`PCode`, `Pfilename`, `view_keyword`, `enabled`, `protect`, `Fdescription`) VALUES
	(40, 'index.php', '3bf18a4a53ab968d3438', '0', '0', ''),
	(38, 'generate_pdf.php', '2d6c64f9b97a9a9310ec', '0', '0', ''),
	(37, 'fpdf.php', '732042d56053d1e63272', '0', '0', ''),
	(34, 'file_down.php', '95cddc7c94ee4be670ca', '0', '0', 'Message if file is down'),
	(30, 'admin.php', 'bc11b559d30a8499c797', '0', '0', ''),
	(27, 'important.php', '91aff6deb69c796f1934', '0', '0', 'Important Messages'),
	(26, 'manage_files.php', '0730804cd38ebe0b62bb', '0', '0', ''),
	(25, 'access_types.php', 'd97ab011cd26e4fcc38a', '1', '0', 'Manage System Groups and Grant Privileges'),
	(24, 'manage_menu.php', '0a3d73475d0899721df4', '0', '0', ''),
	(22, 'groups.php', 'a62f3e276bc74405e619', '0', '0', 'Manage Messaging Groups'),
	(21, 'sent_items.php', 'ba17eb72c48c0193518d', '0', '0', 'Sent Items'),
	(20, 'inbox.php', '323055358bd2330971e9', '0', '0', 'Private Messages'),
	(9, 'users.php', 'a398f30eb08f68546584', '1', '0', 'Manage User Access'),
	(6, 'maps.php', 'e1709958103ca470c3d8', '0', '0', 'Map Monitor'),
	(3, 'messages.php', 'a2619104b323b015768b', '0', '0', 'Standard SMS Reply'),
	(2, 'home.php', 'home', '1', '0', ''),
	(1, 'change_password.php', 'a3908b2e18ada04c40ac', '1', '0', 'Change Password'),
	(43, 'login.php', 'de6026eb7c263c58c9fd', '0', '0', ''),
	(44, 'logout.php', '9e02ee148c26fffb7dd8', '0', '0', ''),
	(46, 'menu.php', 'a3cddbb668421ba32299', '0', '0', ''),
	(49, 'process.php', 'c2a9c20b0d306bb4aa6d', '0', '0', ''),
	(60, 'folder_contents.php', 'a95dd23f711dfe1e32f8', '0', '0', 'View Folder File Contents with Upload File'),
	(61, 'new_message.php', 'bbbe0d02a34903137cf2', '0', '0', 'Create Private Message'),
	(62, 'policies.php', '942e8c57b49626abf641', '0', '0', 'List File Folders with Add and Delete Folder'),
	(63, 'folder_contents_limitted.php', '2e9299ffacb4ce0739b2', '0', '0', 'View Folder File Contents Only'),
	(64, 'policies_limitted.php', 'e2bd1a164cae1e8426aa', '0', '0', 'List File Folders Only'),
	(66, 'productmaster.php', '8c497b0fc2c68c99a85b', '1', '0', 'Product Master'),
	(67, 'categories.php', 'd0f76de734f6b18fd6a3', '1', '0', 'Product Categories'),
	(68, 'account.php', 'd7b4997013393b5f706d', '1', '0', ''),
	(214, 'joborder_edit.php', '976cc244f4f5d6f88f9e', '1', '0', NULL),
	(70, 'location.php', '4ce31071976217d80e88', '1', '0', ''),
	(71, 'packdetail.php', '3f243c0767a992985f05', '1', '0', 'Package Details'),
	(72, 'packheader.php', 'a08d72922d2b15ae58e0', '1', '0', 'Package Header'),
	(73, 'purchasedetail.php', '6da40bf4b8fc1d0e2462', '1', '0', ''),
	(74, 'purchaseheader.php', '3ffb7cb53bd8197c214d', '1', '0', ''),
	(75, 'supplier.php', '2ac6015931630e2af7b2', '1', '0', ''),
	(76, 'withdrawdet.php', '73575c723a9be8799cd2', '1', '0', ''),
	(77, 'withdrawhdr.php', 'e0501658e0cc070d5347', '1', '0', ''),
	(78, 'new_productmaster.php', 'a09dc2e7caa66cf3d3ec', '1', '0', 'New Product Master '),
	(79, 'formulation.php', '10d746d1b509cf50e86d', '1', '0', 'formulation'),
	(80, 'brand.php', '03d44694aca19be5ed12', '1', '0', 'brand'),
	(81, 'formulation_seach.php', '7fc22aed40086746a977', '1', '0', 'formulation search'),
	(82, 'formulationreport.php', '50da62acc9b6a2af1378', '1', '0', ''),
	(216, 'joborder_search.php', '9228975eac90947e136f', '1', '0', ''),
	(205, 'dd_suppliers.php', '503d616aa609dffee49a', '1', '0', NULL),
	(84, 'printFormulation.php', 'fa912a68e0b9fb352ca4', '1', '1', ''),
	(85, 'package.php', '02cb85ce8363508a147d', '1', '0', ''),
	(86, 'formulation_edit.php', 'a2bcefb19927cacb4c3a', '1', '0', 'formulation edit'),
	(87, 'printJO.php', '4321e7edfb6db99a5391', '1', '1', ''),
	(215, 'joborder_edit.php.bak', '5dd66458d31d538d0aaa', '1', '0', NULL),
	(89, 'delivery.php', '9285a14dca0b4d10ff48', '1', '0', 'Delivery'),
	(90, 'ajax.php', '5c5818c4d60858042e15', '1', '0', ''),
	(91, 'deliveryreceipt.php', '80c508228f4f7d0b25d1', '1', '0', 'Delivery Receipt'),
	(92, 'printDeliveryReceipt.php', '5949dd6e1e7f21ac6973', '1', '1', 'Delivery Print'),
	(93, 'projected.php', '803ff49fd68cb49b4756', '1', '0', NULL),
	(94, 'printProjected.php', 'd5b20de7475e81e6b9b2', '1', '1', NULL),
	(95, 'printRawMaterialsUsed.php', '73ce49c46e8d1df709cd', '1', '1', 'Print Sumarry of RM'),
	(96, 'rawMaterialsUsed.php', '41c736cdb5e9e9b10f41', '1', '0', 'Summary of RM'),
	(97, 'deliveries_search.php', '37f189dc4662b32e51f4', '1', '0', 'Delivery Search'),
	(98, 'delivery_edit.php', '1914dff92d633d57f7e0', '1', '0', 'Delivery Edit'),
	(260, 'print_report_po.php', '9ec3b0725b7c1d2f0587', '1', '1', NULL),
	(100, 'receivingReport_edit.php', '13ae3b767fbe219f1d44', '1', '0', 'Receiving Report Edit'),
	(259, 'print_report_budget.php', '3caaa4465efa0b5f5b11', '1', '1', ''),
	(102, 'inventoryBalanceReport.php', 'c37463f25ef5820304b5', '1', '0', 'Inventory Balance Report'),
	(103, 'printInventoryBalanceReport.php', '6f62f87f59bb79d63272', '1', '1', 'Print Inventory Balance Report'),
	(104, 'printRR.php', '6527b3d7c5d2ce4fd75d', '1', '1', ''),
	(105, 'stockstransfer.php', '4c913d2375ce883c1177', '1', '0', 'Stocks Transfer New'),
	(106, 'stockstransfer_edit.php', '8c36d639dabf0cdce31d', '1', '0', 'Stocks Transfer Edit'),
	(107, 'stockstransfer_search.php', '72865d2a5f85a2ac0a9e', '1', '0', 'Stocks Transfer Search'),
	(108, 'printStocksTransfer.php', '8bb1599cf5fb7dad6bd4', '1', '1', ''),
	(109, 'productconversion.php', 'f0e346015b7d471106ae', '1', '0', 'New Product Conversion'),
	(110, 'productconversion_edit.php', '5cd05c20ce481cd914cc', '1', '0', 'Edit Product Conversion'),
	(111, 'productconversion_search.php', '91eea13613f3807b63f8', '1', '0', 'Search Product Conversion'),
	(112, 'printProductConversion.php', '3addafed8df0c9852f49', '1', '1', NULL),
	(113, 'stockcard.php', 'c3a9a8a69db5fbe4b735', '1', '0', 'New Stock Card'),
	(114, 'stockcard_edit.php', '983c1563664e322d94de', '1', '0', 'Edit Stock Card'),
	(115, 'stockcard_search.php', '83950def8e6bc7319464', '1', '0', 'Search Stock Card'),
	(116, 'printStockCard.php', '6854ac7cbdbd15b22ced', '1', '1', ''),
	(117, 'stockCardReport.php', 'a54037b17331f0186558', '1', '0', 'Stock Card Report Project'),
	(118, 'customerpayments.php', '63fbc1306ca1973d599d', '1', '0', 'New Customer Payments'),
	(119, 'customerpayments_edit.php', '520c72159bd689361b66', '1', '0', 'Edit Customer Payments'),
	(320, 'ar_report_account_ledger.php', '9ded9da7e2df7e4ccd1e', '1', '0', 'Account Ledger'),
	(121, 'customerpayments_search.php', '25bfa3542f6c256493eb', '1', '0', 'Search Customer Payments'),
	(327, 'ap_summary_of_ap.php', '2f631491a4db180cd4ea', '1', '0', 'Summary of Accounts Payable'),
	(124, 'statementofaccount.php', '5592d062f5323fa2f2fe', '1', '0', 'Statement of Account'),
	(125, 'summaryofaccountsreceivables.php', '7e2b36ceeecf2ef6eb01', '1', '0', 'Summary of Accounts Receivables'),
	(126, 'printStatementOfAccount.php', 'd84cccf4dbc1da94a6d1', '1', '1', ''),
	(127, 'printSummaryOfAR.php', 'e7279a03a47a933fdfb5', '1', '1', ''),
	(325, 'ar_report_due_checks.php', 'e16a3fb9732e1e20f58e', '1', '0', 'Summary od Due Checks'),
	(324, 'ar_print_report_due_checks.php', '5906228afe39a82ff45e', '1', '0', ''),
	(130, 'printAccountLedger.php', '35be01e479d5668c004d', '1', '1', ''),
	(326, 'ap_print_summary_of_ap.php', '2f43e1c98fc28aa1f9bb', '1', '0', NULL),
	(132, 'printSalesSummaryForSubsidiary.php', '94c3464f30a7a9b28f3d', '1', '1', ''),
	(133, 'salesSummaryForSubsidiary.php', 'db067258b7f2c67dc771', '1', '0', 'Sales Summary For Subsidiary'),
	(134, 'chartofaccounts.php', '851ce08260d66572102e', '1', '0', 'Chart of Accounts'),
	(135, 'journal.php', 'c325a0ff7e9dece0f55d', '1', '0', 'Journal'),
	(136, 'gltransac.php', '1da21dd42f2e46c2d13e', '1', '0', 'General Ledger Transaction'),
	(137, 'gltransac_search.php', 'bdcd8afe0e18607c61c2', '1', '0', 'General Ledger Search'),
	(138, 'gltransac_edit.php', '0506df0122ebfae05b56', '1', '0', 'General Ledger Edit'),
	(139, 'printGeneralLedger.php', 'a4ea6922446ff715360c', '1', '1', NULL),
	(314, 'po_approval.php', '6463c8fe450f52cf5906', '1', '0', NULL),
	(313, 'po_search_approval.php', 'e877041505bf2b26be3b', '1', '0', 'Purchase Order Approval'),
	(309, 'ar_print_monthly_sales_report_by_product.php', 'a1dfd2704b9881720a06', '1', '0', NULL),
	(310, 'ar_print_periodic_sales_report.php', 'a81b271f4876f2e3f1b5', '1', '0', NULL),
	(308, 'ar_print_monthly_sales_report.php', '8b42f094f19aaaa48083', '1', '0', NULL),
	(147, 'printSalesByCustomer.php', 'ed540ef55ecf4c312e42', '1', '1', NULL),
	(312, 'ar_print_sales_by_customer.php', 'd323cc8db4a359207115', '1', '0', NULL),
	(311, 'ar_print_periodic_sales_report_by_product.php', '8d150c36a1a1a61159e3', '1', '0', NULL),
	(293, 'acctg_journal_listings.php', 'd116c0d5c90584908317', '1', '0', 'Journal Listings'),
	(151, 'printAL.php', '2c7c9c268b2bdb0d6a24', '1', '1', NULL),
	(152, 'printJournal.php', '6a56b96a21a70eb6e755', '1', '1', ''),
	(153, 'printSOA.php', '953a6969cf3df1c75232', '1', '1', NULL),
	(294, 'acctg_print_balance_sheet.php', 'a9f3f651fd4ce2eb01c4', '1', '0', ''),
	(155, 'printGLListing.php', '362207217bed02f95a80', '1', '1', 'Print General Ledger Listing'),
	(156, 'printJournalListing.php', '3777dd7e5ea9cb2e0c75', '1', '1', ''),
	(300, 'ar_search.php', '38e5c693853aa3fe1fa0', '1', '0', 'Search Accounts Receivables'),
	(292, 'acctg_income_statement.php', 'cf71784a34bffbf699f8', '1', '0', 'Income Statement'),
	(291, 'acctg_gl_listings.php', '3dec70e8392031284092', '1', '0', 'General Ledger Listings'),
	(161, 'printIncomeStatement.php', 'b70a0a77a687691ee8a1', '1', '1', 'Print Income Statement'),
	(290, 'acctg_balance_sheet.php', '313b6dc868d4dd0de186', '1', '0', 'Balance Sheet'),
	(163, 'printBalanceSheet.php', '14cbb0f96819a53545e9', '1', '1', 'Print Balance Sheet'),
	(165, 'po.php', '7903cdd0494e804dde22', '1', '0', 'Purchase Order'),
	(166, 'po_edit.php', '063c6c270ef999f3952e', '1', '0', 'Purchase Order Edit'),
	(167, 'po_search.php', 'df852830f43bfe6f1500', '1', '0', 'Purchase Order Search'),
	(168, 'printPO.php', '5b606804fbc56506710e', '1', '1', NULL),
	(169, 'receivingReport_edit.php.bak', '648027521b2bfb0ad50c', '1', '0', ''),
	(170, 'undeliveredItems.php', '6ecc46794d98933ea48b', '1', '0', 'Undelivered Items'),
	(330, 'ap_print_report_aging_of_ap.php', 'e0f7204c3c50b0383d4e', '1', '0', ''),
	(329, 'ap_print_accounts_payable_ledger.php', '959e6fa25fdfac449db8', '1', '0', ''),
	(174, 'company_heading.php', '4afdda82012595cd3da0', '1', '0', NULL),
	(328, 'ap_accounts_payable_ledger.php', '41509680b2b483a92158', '1', '0', 'Accounts Payable Ledger'),
	(231, 'invadjust_search.php', 'f91eae7d45f45fbcc4dc', '1', '0', 'Search Inventory Adjustments'),
	(230, 'invadjust.php', 'bf064376f984e4104e1a', '1', '0', 'Inventory Adjustments'),
	(179, 'aradjust.php', 'a6b7bd63963b643a9ae6', '1', '0', 'AR Adjust'),
	(180, 'aradjust_edit.php', 'f7e91dce2805caa9727f', '1', '0', 'AR Adjust Edit'),
	(181, 'aradjust_search.php', '7cee060d76e6959c5c73', '1', '0', 'AR Adjust Search'),
	(182, 'printBalanceSheet.php.bak', 'fba0979e16b8a8a8541d', '1', '1', NULL),
	(183, 'receivingReport.php.bak', '8624705201dd4ec2c20a', '1', '0', ''),
	(184, 'rr.php', 'b0208e11d5f33ac78fc5', '1', '0', 'Stocks Receiving'),
	(185, 'stockstransfer_edit.php.bak', '375daf80b0b69aa28391', '1', '0', ''),
	(186, 'order.php', '80a667cfc2e56e49eb5f', '1', '0', 'Order Sheet'),
	(187, 'order_search.php', 'ea48bced27d95dc9ad6c', '1', '0', 'Search Order Sheet'),
	(188, 'dr.php', 'e47a963919d3f2310d14', '1', '0', 'Delivery Receipt'),
	(189, 'printOrder.php', '3f990c688bede71fc632', '1', '1', NULL),
	(190, 'customers.php', '131e4bf1afeeaf60ed59', '1', '0', NULL),
	(191, 'orders_dd.php', 'ddd759a31cbff7ee2e8a', '1', '0', NULL),
	(192, 'po.php.bak', '11fecf3f94180ee0ae73', '1', '0', NULL),
	(193, 'printDailyOutletsOrderSheet.php', '697b850208f92b50e576', '1', '1', ''),
	(194, 'stocks.php', 'b377c8c8934bcf741d4a', '1', '0', ''),
	(195, 'dailyOutletsOrderSheet.php', 'a839882ac9f3704bcc53', '1', '0', 'Daily Outlets Order Sheet'),
	(196, 'dailyOrderSheetReconcilation.php', '75a252e3207c79a873ba', '1', '0', 'Order Sheet Reconcilation'),
	(197, 'printDailyOrderSheetReconcilation.php', 'de33b8c1dac5817ff2e4', '1', '1', ''),
	(198, 'formulation_search.php', '95a1aadc171949280567', '1', '0', 'Formulation Search'),
	(199, 'dd_deliveries.php', '76a39a00cdcfa4748629', '1', '0', NULL),
	(200, 'dd_formulations.php', '4f6eb130a3ea53bf0529', '1', '0', NULL),
	(275, 'sample.php', '2f601b69b76d6d91ad32', '1', '0', NULL),
	(274, 'dd_service.php', '1335e3bb537ad37352a9', '1', '0', NULL),
	(204, 'jo_search.php', 'a099a28c96ef26ce8d24', '1', '0', 'Search Job Order'),
	(213, 'joborder.php', '7850122481f1ebd80796', '1', '0', ''),
	(212, 'jo.php', '9788f520b84f1ca2da24', '1', '0', NULL),
	(210, 'production_piece.php', '68bb88848b5e45accb06', '1', '0', 'Production by Piece'),
	(211, 'production_pack.php', 'b28e2bf669cfd225bb74', '1', '0', 'Production by Pack'),
	(217, 'print_report_orderSummary.php', '626d1a8c55db46ad18d0', '1', '1', ''),
	(218, 'report_orderSummary.php', 'f287826990d1920dad11', '1', '0', 'Order Summary'),
	(219, 'print_report_deliverySummary.php', '6c0a85b60c87daf96c98', '1', '1', ''),
	(220, 'report_deliverySummary.php', '948fdfca805bfb9c63b2', '1', '0', 'Delivery Summary By Item'),
	(221, 'print_report_orderDeliverySummary.php', 'ea50016b83c6d99eab3b', '1', '1', ''),
	(222, 'report_orderDeliverySummary.php', '61c1be727dcc4bddc076', '1', '0', 'Order Delivery Summary'),
	(223, 'print_report_productionSchedule.php', 'b4ba564fc657f32e375b', '1', '1', ''),
	(224, 'report_productionSchedule.php', 'bdbd677ddec9dc1c4822', '1', '0', 'Production Schedule'),
	(225, 'production_piece_search.php', 'a438260aec0d36c8c907', '1', '0', ''),
	(226, 'print_report_productionSummary.php', 'da0004d2e2f3225b90b4', '1', '1', ''),
	(227, 'report_productionSummary.php', '0393abd64834378d735c', '1', '0', 'Production Summary'),
	(228, 'purchase_returns.php', '4778be5a4f421c9b5b9d', '1', '0', 'Purchase Returns'),
	(229, 'purchase_returns_search.php', 'a516b52871a6ee0b2aed', '1', '0', 'Search Purchase Returns'),
	(232, 'stock_returns.php', 'd6d21a3f95099992ff29', '1', '0', 'Stock Returns'),
	(233, 'print_report_ordersOverall.php', 'c336717c177488043f60', '1', '1', ''),
	(234, 'report_ordersOverall.php', '4522322be02927a4f62f', '1', '0', 'Overall Orders'),
	(235, 'print_report_summaryOfReturns.php', 'c65b1b16989e190603e9', '1', '1', ''),
	(236, 'report_summaryOfReturns.php', 'a57e451cf58c373169e9', '1', '0', 'Summary of Returns'),
	(237, 'print_report_productionByPiece.php', 'ef7e0ef214d48f010547', '1', '1', ''),
	(238, 'print_report_productionByPack.php', '6c301780357137dad84f', '1', '1', ''),
	(239, 'production_pack_search.php', '1fd51d23ed1b7615432b', '1', '0', 'Search Production by Pack '),
	(240, 'print_report_orderSummaryByID.php', '759e29454c081d4d5aaf', '1', '1', ''),
	(241, 'report_orderSummaryID.php', '690689f8abcd8f82b1ae', '1', '0', 'Order Summary By ID'),
	(242, 'print_report_deliverySummaryID.php', '9c061898ceee5dead6a4', '1', '1', ''),
	(243, 'report_deliverySummaryByID.php', '9f4eef5230d008df242f', '1', '0', 'Delivery Summary By ID'),
	(244, 'print_report_deliverySummaryByID.php', '0518d18c517c74ed0715', '1', '1', ''),
	(245, 'print_report_deliverySummaryTotal.php', '1e0b8ddf1c38169bf672', '1', '1', ''),
	(246, 'print_report_orderSummaryTotal.php', 'd4793ab4e571bb045df1', '1', '1', ''),
	(247, 'report_deliverySummaryTotal.php', '238778fb72b9e9b2f334', '1', '0', 'Total Delivery Summary'),
	(248, 'report_orderSummaryTotal.php', '6ddb31b5628b19f6ceeb', '1', '0', 'Total Order Summary'),
	(249, 'budget.php', '29a6d2e5c71d0ae94395', '1', '0', 'Budget'),
	(250, 'budget_search.php', '3844b094b87a427a0dac', '1', '0', 'Search Budget'),
	(251, 'projects.php', '8f272763df94997ffd08', '1', '0', 'Projects'),
	(252, 'dd_projects.php', '89156883a34dc572abf9', '1', '0', NULL),
	(253, 'purchase_request.php', '92f219b2924a29646e32', '1', '0', 'Purchase Request'),
	(254, 'purchase_request_search.php', '5a49ca80b827b3be2b92', '1', '0', 'Search Purchase Request'),
	(255, 'purchase_request_approval.php', 'c4f6f92bbbb72914dcda', '1', '0', 'Purchase Request Approval'),
	(256, 'purchase_request_approval_details.php', '5f32fa918fa844c4fbe6', '1', '0', 'Purchase Request Approval Details'),
	(257, 'dd_po.php', '65892e1dfaef0692c39f', '1', '0', NULL),
	(258, 'rr_search.php', '13fc647856ccdd5b4259', '1', '0', 'Search Stocks Receiving'),
	(261, 'print_report_purchase_request.php', '0fe90fb98ac44e03f732', '1', '1', ''),
	(262, 'print_report_rr.php', '92dc3a7b42323c20cbfa', '1', '1', NULL),
	(263, 'stocks_transfer.php', '9584cf38e569679c0b52', '1', '0', 'Stocks Transfer'),
	(264, 'issuance.php', '02bb738f4e1ab460dd47', '1', '0', 'Issuance'),
	(265, 'stock_returns_search.php', '900c62f3e9e8eacacf59', '1', '0', 'Search Stock Returns'),
	(266, 'stocks_transfer_search.php', 'f8f765ba7acc1f3db02b', '1', '0', 'Search Stocks Transfer'),
	(267, 'issuance_search.php', '0454aa8bc66b8ae7c49a', '1', '0', 'Search Issuance'),
	(268, 'production.php', 'c46704ff5879e490212a', '1', '0', 'Production'),
	(269, 'invadjust.php.bak', '961d7f2618887ea92966', '1', '0', NULL),
	(270, 'production_search.php', '8b9df9ed471f085e2447', '1', '0', 'Search Production'),
	(271, 'dd_purchase_request.php', '3fecd9409be800f4f28f', '1', '0', ''),
	(272, 'issuance.php.bak', 'b7b1dbfe87ea2feaf5f8', '1', '0', NULL),
	(273, 'work_category.php', 'ada83ffe9a07eeca6798', '1', '0', 'Work Category'),
	(276, 'service_rr.php', 'c92a6780863280cf9030', '1', '0', 'Service Receiving'),
	(277, 'service_rr_search.php', '5b7b95bb21875f0a0ab0', '1', '0', 'Search Service Receiving'),
	(278, 'service_payments.php', '92d742306163e5ac449a', '1', '0', 'Service Payments'),
	(279, 'form_heading.php', '02e408ebaf01409df5c8', '1', '0', NULL),
	(280, 'print_report_issuance.php', 'a609bee433b0be7ffec6', '1', '1', NULL),
	(281, 'service_payments_search.php', 'c5f43f53f1d13c2b98f6', '1', '0', 'Search Service Payments'),
	(282, 'purchase_request_no_budget.php', '5d8be66488f215b6ffb3', '1', '0', 'Purchase Request | No Budget'),
	(283, 'contractor.php', '120edae024034d58d790', '1', '0', 'Contractor'),
	(284, 'ap_payments.php', 'ff3f2e5cb2616e932fff', '1', '0', 'AP Payments'),
	(285, 'ap_search.php', '12cca22c1ca3ba9df1a8', '1', '0', 'Search Accounts Payable'),
	(286, 'print_report_service_rr.php', '5d68906f67a79118dd09', '1', '1', NULL),
	(287, 'print_report_stocks_return.php', 'cecea9b8eaad76dda30f', '1', '1', NULL),
	(288, 'print_report_stocks_transfer.php', '6b0ebc0d2403657b54ec', '1', '1', ''),
	(289, 'employee.php', '972b8db3b29792e779ba', '0', '1', 'Employees'),
	(295, 'acctg_print_gl_listings.php', 'da7b878f1ed0c2313d6c', '1', '0', ''),
	(296, 'acctg_print_income_statement.php', 'b362cd32bbfd28f584c7', '1', '0', ''),
	(297, 'acctg_print_journal_listings.php', 'ca04dd0c2e38bda35a36', '1', '0', ''),
	(298, 'acctg_trial_balance.php', 'e43a3a3e01ac88329388', '0', '0', 'Trial Balance'),
	(299, 'acctg_print_trial_balance.php', '73f80b2aaf5171081bc0', '1', '0', NULL),
	(301, 'ar_payments.php', 'a2d1019fd707b36cae84', '1', '0', 'Accounts Receivable Payments'),
	(302, 'dd_contractors.php', '744b62ee6962fdd1700c', '1', '0', NULL),
	(303, 'ar_monthly_sales_report.php', '0562ff74e875ba637cf4', '1', '0', 'Monthly Sales Report'),
	(304, 'ar_monthly_sales_report_by_product.php', 'dd034ee9ae78bd7f4bd0', '1', '0', 'Monthly Sales Report by Product'),
	(305, 'ar_periodic_sales_by_product.php', '2974a8f3d4d51356bcbd', '1', '0', 'Periodict Sales by Product'),
	(306, 'ar_periodic_sales_report.php', '31ad87ca6b017263afc7', '1', '0', 'Periodic Sales Report'),
	(307, 'ar_sales_by_customer.php', '40e19989e48977336623', '1', '0', 'Sales by Customer'),
	(315, 'account_type.php', 'ff16338073ae5ce9c621', '1', '0', ''),
	(316, 'sales_invoice.php', '3fec528a5e8b65d353a7', '1', '0', 'Sales Invoice'),
	(317, 'budget_print_project.php', 'fc22f95c0b8283181176', '1', '0', NULL),
	(318, 'budget_report_project.php', '8b780cf2b806f8383bd5', '1', '0', 'Budget Report'),
	(319, 'dd_equipment.php', '0bfa560298b569985c02', '1', '0', ''),
	(321, 'ar_print_report_account_ledger.php', '5257e76e2884b769bbbb', '1', '0', ''),
	(322, 'ar_print_report_aging_of_accounts.php', 'e53ff68fb336af6cc4b5', '1', '0', ''),
	(323, 'ar_report_aging_of_accounts.php', '1d2c29459bb609696962', '1', '0', 'Aging of Accounts'),
	(331, 'ap_report_aging_of_ap.php', 'b6f1990ba5520c5e17c5', '1', '0', 'Aging of Account Payables'),
	(332, 'apv_search.php', '75f0bbd7d97be62db4c9', '1', '0', 'Search Accounts Payable Voucher'),
	(333, 'apv.php', '12a83d5f7c94f78124e5', '1', '0', 'Accounts Payable Voucher'),
	(334, 'financial_budget.php', '2e6ffd8f1c122408e585', '1', '0', 'Financial Budget'),
	(335, 'financial_budget_search.php', '40d26df3e4344804033e', '1', '0', 'Search Financial Budget'),
	(336, 'print_report_apv.php', 'dc1a103356f7be51775e', '1', '1', NULL),
	(337, 'print_warehouse_inventory_balance_report.php', '0e60247909fe63a8b944', '1', '1', ''),
	(338, 'warehouse_inventory_balance_report.php', '67c0736d7bd902a75488', '1', '0', 'Warehouse Inventory Balance Report'),
	(339, 'print_project_inventory_balance_report.php', '569c4ff8ceb1255d2c2c', '1', '1', ''),
	(340, 'project_inventory_balance_report.php', '6fee7a0a5db1c30257d6', '1', '0', 'Project Inventory Balance Report'),
	(341, 'cash_advance.php', '0d9b6b2347d1405fc2b0', '1', '0', 'Cash Advance'),
	(342, 'accounts_payable_voucher.php', '687b880d1beb02fa41b1', '1', '0', 'Accounts Payable Voucher'),
	(343, 'dd_accounts.php', '0ae338e0536309c1c51f', '1', '0', ''),
	(344, 'accounts_payable_voucher_search.php', 'dd8d3aeab7942e3578cc', '1', '0', 'Accounts Payable Voucher Search'),
	(345, 'po_search_apv.php', 'aec9455d619626a32461', '1', '0', 'Search PO for APV'),
	(346, 'productmaster_limited.php', '1e092e188db28364570f', '1', '0', 'Product Master Limited'),
	(347, 'print_issuance_statistics.php', '4552ac5680e3b41bc683', '1', '1', ''),
	(348, 'report_issuance_statistics.php', 'b84976bafa0114fb2e89', '1', '0', 'Product Issuance Statistics Report'),
	(349, 'check_voucher.php', '9d825239df14c9830e3b', '1', '0', 'Check Voucher'),
	(350, 'check_voucher_search.php', '31ba01f35eff48089bf2', '1', '0', 'Check Voucher Search'),
	(351, 'ap_expenses.php', '4b3bbde6503058c9d51b', '1', '0', 'Disbursement Voucher'),
	(352, 'dd_equipments_he.php', 'c7e40de98e2849a1abab', '1', '0', ''),
	(353, 'print_cv.php', '1983f3ea7e6061dae431', '1', '1', NULL),
	(354, 'mrr_history.php', '1106e0217d6ffc0f8b83', '1', '0', 'MRR History Report'),
	(355, 'print_budget_balance.php', '3f4be5827c899ea65d2c', '1', '1', NULL),
	(356, 'print_mrr_history.php', '85e72de8d6cb5469e7c2', '1', '1', ''),
	(357, 'outstanding_pr.php', 'c954311321c731a987c3', '1', '0', 'Outstanding PR Report'),
	(358, 'print_outstanding_pr.php', 'eb5752b56404286b8e2a', '1', '1', ''),
	(359, 'outstanding_mrr.php', '9bca93886e02a8e1fdac', '1', '0', 'Outstanding MRR Report'),
	(360, 'print_outstanding_mrr.php', '1c07007f794e3b03d0bd', '1', '1', ''),
	(361, 'print_stockstransfer_history.php', 'eccc241776ef57785e06', '1', '1', ''),
	(363, 'issuance_history.php', '7f31450032f7e8abdc77', '1', '0', 'Issuance History Report'),
	(364, 'print_issuance_history.php', '651c659fc0d96ec95671', '1', '1', ''),
	(365, 'aggregates_issuance_history.php', '504d28b8d515f30dfbaf', '1', '0', 'Aggregates Issuance History'),
	(366, 'print_aggregates_issuance_history.php', '15d79f7cf3b58df3e39d', '1', '1', ''),
	(367, 'print_aggregates_issuance_history_all_projects.php', '8d8e5eb7ee86bed8f562', '1', '1', ''),
	(368, 'print_issuance_history_all_projects.php', 'dc03cbd32de97781abaa', '1', '1', ''),
	(369, 'print_mrr_history_all_projects.php', '68f76dd681fbdf6228da', '1', '1', ''),
	(370, 'special_po.php', '62e77ed88c61ca0618ee', '1', '0', 'Special PO'),
	(371, 'dd_equipment_he.php', 'd84a3b3a0183166ba14d', '1', '0', ''),
	(372, 'po_history.php', 'c3fac7d6e3aed34be196', '1', '0', 'PO History Report'),
	(373, 'print_po_history.php', 'e88c21bbb5deab9a30e8', '1', '1', ''),
	(374, 'print_po_history_all_projects.php', '4865ccc5eb8d4afc7e8e', '1', '1', ''),
	(375, 'labormat_po.php', '73c53445ae61667436ef', '1', '0', 'Labor Materials PO'),
	(376, 'print_report_rr_aggr.php', '1f52605876310d054353', '1', '1', NULL),
	(377, 'print_report_spo.php', '68e0b924db18b5001ce1', '1', '1', NULL),
	(378, 'print_report_transmittal.php', '30d404a8190b490c22e2', '1', '1', NULL),
	(379, 'print_report_invadjust.php', '7913b8f7375a1e8fe97b', '1', '1', NULL),
	(380, 'print_report_issuance_aggr.php', 'a7a49e61f0a01c45b59f', '1', '1', NULL),
	(381, 'print_report_labormat.php', '2e92e746d57e69a75bcd', '1', '1', NULL),
	(382, 'print_report_stock_card_all.php', 'db694ffed5797ee5eb49', '1', '1', NULL),
	(383, 'print_report_stock_card_warehouse.php', 'fc1b3529f602c3520f3c', '1', '1', NULL),
	(384, 'stock_card_report_warehouse.php', 'baf583357f9da67b407f', '1', '0', 'Stock Card Report MCD Warehouse'),
	(385, 'cash_receipts.php', '303ad28410df5c37b305', '1', '0', 'Cash Receipts'),
	(386, 'print_project_inventory_balance_report_all.php', '5c48a177b2310cbfd0a2', '1', '1', NULL),
	(387, 'equipment.php', 'a52d8730da88def56a80', '1', '0', 'Equipments'),
	(388, 'bank_reconcilation_report.php', '40415f7d0ae925a030cd', '1', '0', 'Bank Reconcilation Report'),
	(389, 'print_cleared_checks.php', '9c6e47cb7b79665451ea', '1', '1', NULL),
	(394, 'print_witholding_tax.php', '3b74e8f78efccb021d7a', '1', '1', ''),
	(391, 'print_batch_cv.php', '657f7c853559e3af5101', '1', '1', ''),
	(392, 'print_uncleared_checks.php', '3057fabe2f5e0aa40ff8', '1', '1', NULL),
	(393, 'batch_check_voucher.php', '6ef5b6dcf86c7a48f53f', '1', '0', 'Batch Check Voucher Printing'),
	(395, 'witholding_tax_report.php', '54fb3904fc2ac3ecf681', '1', '0', 'Witholding Tax Report'),
	(396, 'printAccountsPayableLedger.php', '834ea61763e70692f7ac', '1', '1', ''),
	(397, 'printMonthlySalesReportByProduct.php', '9d318ab3e04cad6c3536', '1', '1', NULL),
	(398, 'printTrialBalance.php', 'f664e51f8509d0af8566', '1', '1', NULL),
	(399, 'journalListing.php', '60e5cf468aae8283a79e', '1', '0', NULL),
	(400, 'monthlySalesReport.php', '5834b53b4f034671b8b5', '1', '0', NULL),
	(401, 'printPeriodicSalesReport.php', '75cf6cf6dda04949bd84', '1', '1', NULL),
	(402, 'summaryofduechecks.php', '8aae04d1b3114fae6b79', '1', '0', NULL),
	(403, 'stockreturns.php', '0360fd64e3e29aaf87d1', '1', '0', NULL),
	(404, 'stockreturns_search.php', '3f6dcb8a4ebc03e55fe9', '1', '0', NULL),
	(405, 'periodicSalesReport.php', '428337d2b6f2edbd5226', '1', '0', NULL),
	(406, 'incomeStatement.php', '747d2cb8661397e13be0', '1', '0', NULL),
	(407, 'printMonthlySalesReport.php', 'd9e4cc095478656b6bcf', '1', '1', NULL),
	(408, 'balanceSheet.php', '576d1e418dc5fe67e6d3', '1', '0', NULL),
	(409, 'periodicSalesByProduct.php', '7a100e3f86cd707dad77', '1', '0', NULL),
	(410, 'monthlySalesReportByProduct.php', '3411c46b0fbfd4ad40ae', '1', '0', NULL),
	(411, 'salesByCustomer.php', '1afd99a372344f693fa1', '1', '0', NULL),
	(412, 'printAgingOfAccounts.php', 'ef28eb2c8f4ffb9d1310', '1', '1', ''),
	(413, 'agingofaccounts.php', 'ea9efe8d6a2066dcdd93', '1', '0', ''),
	(414, 'accountledger.php', 'dd3ed4aa71f7b18936ee', '1', '0', ''),
	(415, 'print_issuance_history_summary.php', 'fcbaa950310d24d19f2e', '1', '1', NULL),
	(416, 'GLListing.php', '02314ddef5deef5f0c56', '1', '0', NULL),
	(417, 'print_mrr_history_summary.php', '0d673c856641bd466035', '1', '1', NULL),
	(418, 'print_aggregates_income_statement.php', '69ded374c1dd31fcfbcf', '1', '1', ''),
	(419, 'printPeriodicSalesByProduct.php', '09bfecc6ce01cd2a1bfd', '1', '1', NULL),
	(420, 'print_aggregates_issuance_history_summary.php', '1dd81ec813b9a31f2b13', '1', '1', NULL),
	(421, 'printAPReport.php', 'd5eae465855cd19b18ba', '1', '1', NULL),
	(422, 'aggregates_income_statement.php', '3b01f330d66eaf74d5ab', '1', '0', 'Aggregates Income Statement'),
	(423, 'accountsPayableLedger.php', '7c4883c38e56efc8504c', '1', '0', ''),
	(424, 'printSummaryOfDueChecks.php', '6003178664117a445e7f', '1', '1', NULL),
	(425, 'trialBalance.php', '63861d849a33023817a2', '1', '0', NULL),
	(426, 'apReport.php', 'd62265a529c853044d34', '1', '0', NULL),
	(427, 'ap_subcon_balance_report.php', '0dc088821398772a00cf', '1', '0', 'AP Subcontractor Balance Report'),
	(428, 'print_ap_subcon_balance_report.php', '4329bdca908b7911e5d1', '1', '1', ''),
	(429, 'post_all_gl.php', 'abf853bdb6e871c5cd8b', '1', '0', NULL),
	(430, 'printIncomeStatement2.php', 'c01be460c416280ace99', '1', '1', NULL),
	(431, 'print_pr_history.php', 'f4bce6289aaee08e713d', '1', '1', ''),
	(432, 'print_pr_history_all_projects.php', 'c785fd0d5c84506c55c1', '1', '1', ''),
	(433, 'print_pr_history_summary.php', 'ac7d442e69cb2b5396ca', '1', '1', ''),
	(434, 'pr_history.php', '2ca716690f173b5456bc', '1', '0', 'Purchase Request History'),
	(435, 'printBalanceSheet2.php', '06abe71e6e36fc8d0b4c', '1', '1', NULL),
	(436, 'print_supplier_ledger.php', '94381376dc8575af7307', '1', '1', ''),
	(437, 'supplier_ledger.php', 'fb7a2a88cd30e3306f0a', '1', '0', 'Supplier Ledger'),
	(438, 'sample_budget.php', '7272e96ae3159820dd57', '1', '0', NULL),
	(439, 'subcontractor_apv.php', '906dc84edef3edc8b174', '1', '0', 'Subcontractor APV'),
	(440, 'info_dir/info.php', 'ed4f38008a4d1152a076', '0', '1', ''),
	(441, 'admin.php.bak', 'd00c001297ef755bf66d', '1', '0', NULL),
	(442, 'BIR.php', '66723444ee72f743ef45', '1', '0', NULL),
	(443, 'login.php.bak', 'c8ccc3b2c3ff9f95680c', '1', '0', NULL),
	(444, 'print_report_subcontractor_apv.php', 'd0b572b0663a59e45ad2', '1', '1', NULL),
	(445, 'print_report_subcontractor_ledger.php', 'b778fa19c5843b9ddb1b', '1', '1', NULL),
	(446, 'print_witholding_tax_summary.php', 'f3ffcf0ec961a927f132', '1', '1', NULL),
	(447, 'rr_unlock.php', '122fb44f23f569cb507a', '1', '0', 'Stock Receiving Unlock Module'),
	(448, 'users.php.bak', '9dcb78a510c8f712e58e', '1', '0', NULL),
	(483, 'list_assets.php', '7cd6af25c25f561938f2', '1', '0', NULL),
	(480, 'cash_receipts_history.php', 'd741d3316d75e93af342', '1', '0', 'CASH RECEIPTS HISTORY REPORT'),
	(481, 'stockstransfer_history.php', '0924384f81a4d760138c', '1', '0', NULL),
	(453, 'print_all_checks.php', '1138a3876185868e41c4', '1', '1', NULL),
	(454, 'print_budget_card.php', 'f6f7ecaf2ffecdcdd2f0', '1', '1', NULL),
	(455, 'budget_monitoring_report.php', '8057e0342e894200f0ec', '1', '0', 'BUDGET MONITORING REPORT'),
	(456, 'print_budget_monitoring_report.php', 'efeff86c8a81bdee17b8', '1', '1', ''),
	(457, 'accountability.php', '9353463cd38dc994f32e', '1', '0', 'ACCOUNTABILITY RECEIPT'),
	(458, 'list_accountability.php', 'fca99b44fa8b805a4c8c', '1', '0', ''),
	(459, 'rr.php.bak', '8d03a0039add5deccee8', '1', '0', NULL),
	(460, 'print_accountability.php', '19b6616204c99996fc36', '1', '1', NULL),
	(461, 'print_stocks_transfer_history_all_projects.php', 'df4d193514912107b541', '1', '1', ''),
	(462, 'print_stocks_transfer_history_summary.php', '6bf6026f30422bcf33d2', '1', '1', ''),
	(463, 'stocks_transfer_history.php', '63d354db7012bd4b95c4', '1', '0', 'STOCKS TRANSFER HISTORY REPORT'),
	(464, 'mrr_beginning_balance.php', '4f76d8d68da56860d6ad', '1', '0', 'MRR ASSET BEGINNING BALANCE'),
	(465, 'print_stocks_transfer_history.php', 'd7a10a3e94399d6d6b4b', '1', '1', NULL),
	(466, 'ppe_report_summary_of_ppe.php', '88e401acd194f6755ecc', '1', '0', 'SUMMARY OF PPE'),
	(467, 'print_ppe_report_summary_of_ppe.php', '91bfe5337ed844ff4446', '1', '1', ''),
	(468, 'Excel/reader.php', '8fba9661cb1997a8c44c', '0', '1', ''),
	(469, 'Excel/load_excel.php', '184d4299b5fdb81c87ea', '1', '1', ''),
	(470, 'payroll/employees.php', '7f3a3da99545a69ed775', '1', '0', 'PAYROLL EMPLOYEES'),
	(471, 'payroll/employees_report.php', 'bcaf805e6250fe48f144', '1', '0', 'PAYROLL EMPLOYEES REPORT'),
	(472, 'payroll/print_employee_record.php', 'a0b3dd3ad9f59c2c1c17', '1', '1', ''),
	(482, 'print_ppe_report_lapsing_schedule.php', 'c5a4a02d33058077185a', '1', '1', NULL),
	(478, 'list_drivers.php', '25fe57efa3f39d83e676', '1', '0', NULL),
	(479, 'print_cash_receipts_history.php', 'f4546c041fbfd50e8aa1', '1', '1', ''),
	(476, 'payroll/dtr.php', '7ee93a1c882d63ba6794', '1', '0', 'PAYROLL DTR'),
	(477, 'payroll/dependents.php', '324131b70a497d2aaeff', '1', '0', 'PAYROLL DEPENDENTS'),
	(484, 'payroll/payroll_holidays.php', '4ee83bfc9cfe6241e5df', '1', '0', 'PAYROLL HOLIDAYS'),
	(485, 'payroll/payroll_generate_payroll.php', '29b32928af31cf22c897', '1', '0', 'GENERATE PAYROLL'),
	(486, 'eur/eur.php', 'ba022abaa4e3cdcba58e', '1', '0', 'HE EUR'),
	(487, 'eur/eq_type.php', 'ee9108b2070641c37e09', '1', '0', ''),
	(488, 'eur/eur_ref.php', 'fd35f3482e3dde34aac1', '1', '0', 'HE EUR REF'),
	(489, 'eur/eur_unit.php', 'f964f73fb883b562b778', '1', '0', 'HE EUR UNIT'),
	(490, 'eur/eur_incentives.php', '748cf268093f8e09af8c', '1', '0', 'HE EUR INCENTIVES'),
	(491, 'eur/print_eur_incentives.php', '5e2fec33732cd6969cba', '1', '1', ''),
	(492, 'print_scope_of_work_summary.php', 'd1115c073ab8de26f326', '1', '1', NULL),
	(493, 'print_issuance_history_summary_per_equip_summary.php', '3cf370e684c6b075f15c', '1', '1', NULL),
	(494, 'print_apv_history.php', '2fbc2d6d6ec5ba966a4c', '1', '1', ''),
	(495, 'print_issuance_history_summary_per_equip.php', 'c296e89a9b280e2b8b8e', '1', '1', NULL),
	(496, 'print_budget_summary.php', '5c4835e686465adcafbb', '1', '1', NULL),
	(497, 'apv_history.php', 'e94ae273e513432b70bb', '1', '0', 'APV HISTORY'),
	(498, 'eur/print_eur_summary.php', 'e9efa4139fd1fa0f1dd8', '1', '1', ''),
	(499, 'eur/eur_summary.php', '4b83e8ff011932f5d384', '1', '0', 'HE EUR SUMMARY'),
	(500, 'batching_plant/batching_production.php', '097c4bcfb50a0c22ed1c', '1', '0', 'BATCHING PLANT PRODUCTION'),
	(501, 'transactions/ris_gl.php', '63dd46c7d7a451291bdb', '1', '0', 'TRANSACTIONS RIS GL'),
	(502, 'payroll/load_dtr.php', 'd194258483908f4621e9', '1', '0', ''),
	(503, 'transactions/rtp.php', 'c792614284da8a36c665', '1', '0', 'TRANSACTIONS RTP MONITORING'),
	(504, 'transactions/print_rtp.php', '9db9c6553cd01156177d', '1', '1', ''),
	(505, 'transactions/form_heading.php', 'c71d8dd183ad5ef70eb7', '1', '0', ''),
	(506, 'masterfiles/companies.php', '829caeb1f61a067301f4', '1', '0', ''),
	(507, 'masterfiles/division.php', 'be5de773c77ffc0d736c', '1', '0', ''),
	(508, 'masterfiles/drivers.php', 'a23c9d7bd39c8c570ee4', '1', '0', 'MASTER FILE DRIVERS'),
	(509, 'acctg/print_ca_details.php', '5bc49927eb06cbf6b7dc', '1', '1', NULL),
	(510, 'acctg/ca.php', 'c9cba80c18ca140b02f2', '1', '0', 'CASH ADVANCE REPORT'),
	(511, 'acctg/print_ca_summary.php', '200e709742ea9160b81f', '1', '1', NULL),
	(512, 'batching_plant/print_production_report.php', '46ccbc6834d51bce793b', '1', '1', ''),
	(513, 'batching_plant/production_report.php', '2ae895b324c32d085600', '1', '0', 'BATCHING PLANT PRODUCTION REPORT'),
	(514, 'payroll/print_schedule_of_project_allowances.php', '5a984ed627876f287ca4', '1', '1', ''),
	(515, 'payroll/schedule_of_project_allowances.php', 'fea96d84ad02c54bab19', '1', '0', 'PAYROLL SCHEDULE OF PROJECT ALLOWANCES'),
	(516, 'payroll/print_tmp_payroll.php', '7b7355706abb7bae415b', '1', '1', ''),
	(517, 'eur/print_he_income_statement.php', 'c78fa8abba0d8fd418ae', '1', '1', ''),
	(518, 'eur/he_income_statement.php', '844fc54f7aef41ce35e9', '1', '0', 'HE INCOME STATEMENT'),
	(519, 'payroll/print_dtr_entries.php', 'f9b50116603ed1c01573', '1', '1', ''),
	(520, 'payroll/dtr_entries.php', '74bce85291570e6fbb2c', '1', '0', ''),
	(521, 'payroll/print_payslip.php', '939d79600b4d4d25d4d5', '1', '1', ''),
	(522, 'payroll/generate_payslip.php', 'c05f92ef69a660bf2419', '1', '0', 'PAYROLL GENERATE PAYSLIP'),
	(523, 'payroll/payroll_summary.php', '876c4926612ee13caa8c', '1', '0', 'PAYROLL SUMMARY'),
	(524, 'payroll/print_payroll_summary.php', '99ac6a4ab311ba1f9f77', '1', '1', ''),
	(525, 'transactions/rtp_history.php', '5fe6549a7803175625ec', '1', '0', 'TRANSACTIONS RTP MONITORING HISTORY'),
	(526, 'transactions/print_rtp_history.php', '8c43a15c2ab99f064ad2', '1', '1', ''),
	(527, 'transactions/total_expenses_summary.php', 'b36b50899c3e04b12207', '1', '0', 'TRANSACTIONS TOTAL EXPENSES REPORT'),
	(528, 'transactions/print_total_expenses_summary.php', '8ee95639bc7805988eb3', '1', '1', ''),
	(529, 'transactions/transaction_history.php', '956319744d3c571ba5f0', '1', '0', 'TRANSACTIONS TRANSACTION HISTORY'),
	(530, 'transactions/print_transaction_history.php', 'ad8fb1d909df23ecf6a4', '1', '1', ''),
	(531, 'eur/print_he_income_statement_detail.php', 'be627813beb4d5861039', '1', '1', ''),
	(532, 'eur/print_po_eur_balance.php', '79c88c55bec83a4d69b9', '1', '1', ''),
	(533, 'eur/po_eur_balance.php', 'd655203f84fd8b19d209', '1', '0', 'HE PO EUR BALANCE'),
	(534, 'masterfiles/sub_gchart.php', '1cd5d417296d6c8279e0', '1', '0', 'MASTER FILE SUB GCHART'),
	(535, 'payroll/official_business_logbook.php', '8f488e5693e708ad0e2a', '1', '0', 'PAYROLL OFFICIAL BUSINESS LOG BOOK'),
	(536, 'payroll/ob_report.php', 'd3001dbed626e0f6e5c9', '1', '0', 'REPORT OFFICIAL BUSINESS REPORT'),
	(537, 'payroll/payroll_generate_payroll.php.bak', '6e82c4a5968fc2e6d467', '1', '0', NULL),
	(538, 'payroll/print_ob_report.php', 'b71a17088b630c3e7583', '1', '1', ''),
	(539, 'payroll/print_official_business_logbook.php', '03c0ce1a6b87382fdba8', '1', '1', NULL),
	(540, 'payroll/print_outstanding_collections.php', '3d9a1562926dba0281d8', '1', '1', NULL),
	(541, 'payroll/print_tmp_payroll.php.bak', '2d90cdd1b3f7b7830d42', '1', '1', NULL),
	(542, 'admin/admin_check_voucher.php', '28c0e96f87d5c2cf525c', '1', '0', 'ADMIN CHECK VOUCHER PRINTING'),
	(543, 'reports/audit_of_accountability.php', '29033656652590f6f270', '1', '0', 'REPORT AUDIT OF ACCOUNTABLES'),
	(544, 'reports/print_audit_of_accountability.php', 'bb33ed669732c8cfc714', '1', '1', ''),
	(545, 'reports/report_audit.php', '33be43175f60fbb691b3', '1', '0', 'REPORT AUDIT'),
	(546, 'reports/print_report_audit.php', 'baa40b8669aadb02001b', '1', '1', ''),
	(547, 'form_heading_ieee.php', '5276bdd1bb30af9b4d1f', '1', '0', NULL),
	(548, 'print_budget_summary_per_category.php', '2cb505bac85131376dd2', '1', '1', NULL),
	(549, 'print_mrr_history_summary_per_item.php', 'd5176b3ae94826bb55fe', '1', '1', NULL),
	(550, 'print_issuance_history_summary_per_item.php', 'fbc5da26394d8c820c2c', '1', '1', NULL),
	(551, 'list_po.php', 'bba640514a255e70acc9', '1', '0', NULL),
	(552, 'print_report_subcontractor_payments_ledger.php', '313b6652353f4d5ec4ce', '1', '1', NULL),
	(553, 'print_report_sales_invoice.php', '7141d19e6d8e35b0bbfb', '1', '1', NULL),
	(554, 'backup.php', 'd616890ff3816658643c', '1', '0', NULL),
	(555, 'budget_details/budget_details.php', '68c0162fc46726fb7ce1', '1', '0', NULL),
	(556, 'budget_details/sections.php', '7754303a9a48fd6ce3b7', '1', '0', NULL),
	(557, 'joborder/print_joborder.php', '80c4c8a0b23549587823', '1', '1', ''),
	(558, 'joborder/equipment_history.php', 'ed21c9369cbe26f01855', '1', '0', 'REPORT EQUIPMENT HISTORY'),
	(559, 'joborder/print_equipment_history.php', 'c7373c367646fbeb1413', '1', '1', ''),
	(560, 'joborder/joborder.php', '7b39524250cb38f48e8e', '1', '0', 'TRANSACTION JOB ORDER'),
	(561, 'payroll/print_report_contributions.php', 'ab3cf32c01071b118261', '1', '1', NULL),
	(562, 'payroll/report_contributions.php', '7176aee9752bdbdbf435', '1', '0', 'REPORT CONTRIBUTIONS'),
	(563, 'vehicle_pass/vehicle_pass.php', 'ef62db570dc05e907ef1', '1', '0', NULL),
	(564, 'vehicle_pass/print_vehicle_pass.php', 'f3cd5e5ab28dd2a03d56', '1', '1', NULL),
	(565, 'vehicle_pass/vehicle_pass_report.php', 'b1322ba6bec008c34f0a', '1', '0', NULL),
	(566, 'vehicle_pass/print_vehicle_pass_report.php', '0784007a85a8a51c83f1', '1', '1', NULL),
	(567, 'petty_cash/petty_cash.php', 'f8eb4a03067ff43d8259', '1', '0', NULL),
	(568, 'petty_cash/petty_cash_approve.php', 'a0193b35abf01c20cbc6', '1', '0', NULL),
	(569, 'payroll/print_tardiness_summary_report.php', '256a90123e1623892924', '1', '1', ''),
	(570, 'payroll/tardiness_summary_report.php', '0a327358db0e9f151897', '1', '0', 'TARDINESS SUMMARY REPORT'),
	(571, 'batching_plant/print_cost_report.php', '12efefd37f62e4014de4', '1', '1', NULL),
	(572, 'batching_plant/print_premix_delivery.php', 'a7efc35944a08e4f1283', '1', '1', ''),
	(573, 'batching_plant/print_premix_soa.php', 'b078746e9d8559db679d', '1', '1', ''),
	(574, 'batching_plant/premix_delivery.php', '2bb742220ba8674b95e7', '1', '0', 'TRANSACTION PREMIX DELIVERY'),
	(575, 'batching_plant/premix_soa.php', 'd16b90046dce37b0d1a6', '1', '0', 'REPORT PREMIX STATEMENT OF ACCOUNT'),
	(576, 'reports/accountability_history.php', 'c41677c15a6c1ae17691', '1', '0', 'REPORT ACCOUNTABILITY HISTORY'),
	(577, 'reports/print_accountability_history.php', '4917c080ccd3b2ac712f', '1', '1', NULL),
	(578, 'petty_cash/petty_cash_report.php', '3d1c12c00e1e72099154', '1', '0', NULL),
	(579, 'petty_cash/petty_cash_rjr.php', 'def054fc4974ad3ce9ab', '1', '0', NULL),
	(580, 'petty_cash/print_petty_cash_report_rjr.php', '87622c114032aaac6982', '1', '1', NULL),
	(581, 'petty_cash/petty_cash_approve_report.php', 'bd22ccd54e6a29bf10b9', '1', '0', NULL),
	(582, 'petty_cash/print_petty_cash_rjr.php', 'f69709ff64b77588edda', '1', '1', NULL),
	(583, 'petty_cash/info.php', '52035b8ddb1ece4044e7', '1', '0', NULL),
	(584, 'petty_cash/print_petty_cash_approve_report.php', 'cee49020e1c69a32f87b', '1', '1', NULL),
	(585, 'petty_cash/print_petty_cash_report.php', 'cf5d43236576e4016c4e', '1', '1', NULL),
	(586, 'petty_cash/petty_cash_report_rjr.php', 'acd66d15373263b8fb31', '1', '0', NULL),
	(587, 'petty_cash/print_petty_cash.php', 'a9703550d8b0c0fb7bb5', '1', '1', NULL),
	(588, 'work_type/work_type.php', '1b14e9982995fddc62f5', '1', '0', NULL),
	(589, 'labor_budget/budget.php', 'bc0d9279e99bdefbd0a6', '1', '0', NULL),
	(590, 'labor_budget/add_labor_budget.php', 'b31a95d16cff7cbb1fe3', '1', '0', NULL),
	(591, 'labor_budget/list_labor_budget.php', '4c90bb3103bb91798ebf', '1', '0', NULL),
	(592, 'labor_budget/purchase_request.php', 'f4e9cf5b43526307214f', '1', '0', NULL),
	(593, 'labor_budget/list_purchase_request.php', '15b6169dd50d4adea72a', '1', '0', NULL),
	(594, 'petty_cash/petty_cash_liquidation_report.php', '228c3c288fbc28236646', '1', '0', NULL),
	(595, 'petty_cash/print_petty_cash_liquidation_report.php', '7049d15bc757237943f9', '1', '1', NULL),
	(596, 'budget_details/print_report.php', '0aa77014686b799f2fb9', '1', '1', NULL),
	(597, 'budget_details/report.php', '1ee1b9cd9d1bd63f04db', '1', '0', NULL),
	(598, 'joborder/print_incomplete_job_history.php', '30dda0b1fcb9ed345dbc', '1', '1', ''),
	(599, 'joborder/rando_change_oil_history.php', 'e3c632a5be16e8d70cf6', '1', '0', 'RAND CHANGE OIL HISTORY'),
	(600, 'joborder/joborder_history_per_equipment_category.php', '32bbb307cec315d6f2bd', '1', '0', 'JOB ORDER HISTORY PER EQUIPMENT CATEGORY'),
	(601, 'joborder/print_mechanic_accomplishment_detail.php', 'b3ae309e71437cb9d8a7', '1', '1', ''),
	(602, 'joborder/print_rando_change_oil_history.php', '48f17db2f36014eeeff9', '1', '1', ''),
	(603, 'joborder/change_oil_history.php', 'd50ae56617d065b930fe', '1', '0', 'CHANGE OIL HISTORY'),
	(604, 'joborder/print_change_oil_history.php', '30867946ca7688f72d17', '1', '1', ''),
	(605, 'joborder/incomplete_job_history.php', '3ea0a9939113a1f3004c', '1', '0', 'INCOMPLETE JOB HISTORY'),
	(606, 'joborder/print_joborder_history_per_equipment_category.php', 'ff5c6f65db4aaae184f0', '1', '1', ''),
	(607, 'joborder/mechanic_accomplishment_detail.php', '99af65a26f5b09d4d0dc', '1', '0', 'MECHANIC ACCOMPLISHMENT DETAIL'),
	(608, 'po_labor/po_labor.php', 'f374ef0a421b1630d56e', '1', '0', NULL),
	(609, 'payroll/generate_weekly_payroll.php', '41aadd8c5e5e25b9e7ee', '1', '0', 'GENERATE WEEKLY PAYROLL'),
	(610, 'batching_plant/premix_quotation.php', '9c4c7860aec6c6f8acb3', '1', '0', 'Premix Quotation'),
	(611, 'batching_plant/print_premix_quotation.php', '263aef22d8067dc41ca5', '1', '1', ''),
	(612, 'items/items.php', '98b1ba711c97b55b6151', '0', '0', ''),
	(613, 'kmrun/kmreport.php', 'a2f677d043dc09bee747', '1', '0', NULL),
	(614, 'kmrun/print_kmrun_report.php', '2da1b7ffe634fbecd467', '1', '1', NULL),
	(615, 'kmrun/kmrun.php', '4d0bfc8544271cb45a09', '1', '0', NULL),
	(616, 'print_labor_budget_reports.php', 'fb81680d79d2d76eee22', '1', '1', NULL),
	(617, 'dd_stock.php', '378fa23b2d2913bf9e4a', '1', '0', NULL),
	(618, 'jobs.php', '81ab4fbcabed2e8dda68', '1', '0', NULL),
	(619, 'my_Classes/Formulation.php', 'e1c08cd7ce968cfd5653', '1', '0', NULL),
	(620, 'my_Classes/ps_pagination.php', '3bc46c8cf33f35a37ad9', '1', '0', NULL),
	(621, 'joborder/print_joborder_history.php', 'f06f211544b218755f7a', '1', '1', ''),
	(622, 'joborder/joborder_history.php', 'eb993738187cb89166fb', '1', '0', 'JOB ORDER HISTORY'),
	(623, 'partsfile/print_part_report.php', '616c82c4b2e61bf683ce', '1', '1', NULL),
	(624, 'partsfile/partfile.php', '27165883e68b7d75e4d1', '1', '0', NULL),
	(625, 'raw_mat_fabrication/print_fabrication.php', 'ca93149456ddda787625', '1', '1', ''),
	(626, 'raw_mat_fabrication/fabrication.php', '5960fec83e1586e1dd27', '1', '0', 'FABRICATION'),
	(627, 'raw_mat_fabrication/transaction-status.php', '523b7b95480e21042364', '1', '0', ''),
	(628, 'raw_mat_fabrication/raw_mat_fabrication_report.php', 'a6330038715d503b9cba', '1', '0', 'REPORT RAW MATERIALS FABRICATION SUMMARY'),
	(629, 'raw_mat_fabrication/print_raw_mat_fabrication_report.php', '31d24ecc3966f2e91ffb', '1', '1', ''),
	(630, 'raw_mat_fabrication/product_fabrication_report.php', '19938cfacbb59cb748d9', '1', '0', 'REPORT PRODUCT FABRICATION'),
	(631, 'raw_mat_fabrication/print_waste_cut_fabrication_report.php', 'c68944bac1c798912575', '1', '1', ''),
	(632, 'raw_mat_fabrication/waste_cut_fabrication_report.php', '99f2fa88f122ee74bb34', '1', '0', 'REPORT WASTE CUT FABRICATION'),
	(633, 'raw_mat_fabrication/print_product_fabrication_report.php', 'a6fb1129211343c38b88', '1', '1', ''),
	(634, 'print_labor_budget_reports_per.php', '7db65e32665af1bced7e', '1', '1', NULL),
	(635, 'additional_transac/sales_return.php', '51f9b5eae75ac6a943ed', '1', '0', 'Sales Return'),
	(636, 'additional_transac/transaction-status.php', 'f8735c988d3cac73866d', '1', '0', ''),
	(637, 'additional_transac/purchase_return.php', 'b877a33ba3985c55a5f6', '1', '0', 'Purchase Returns (New)'),
	(638, 'additional_transac/func_gatepass.php', '18213141cb9c1462365f', '1', '0', ''),
	(639, 'additional_transac/gatepass.php', '594312c34a4dd5d03978', '1', '0', 'Gatepass'),
	(640, 'po_labor/add_payroll.php', 'e57f21aa474aa28b4753', '1', '0', NULL),
	(641, 'print_admin_payroll_summary.php', 'b8cb232a4e713241389a', '1', '1', NULL),
	(642, 'admin_payroll.php', 'e786639fc539fabeee5d', '1', '0', NULL),
	(643, 'print_labor_budget_balance.php', '51348c2d31075fb14969', '1', '1', NULL),
	(644, 'raw_mat_fabrication/print_waste_cut_inventory_balance_report.php', 'ae7de259b6ad9065aefb', '1', '1', ''),
	(645, 'raw_mat_fabrication/waste_cut_inventory_balance_report.php', '59b7c47b8cf365ee708f', '1', '0', 'REPORT WASTE CUT INVENTORY BALANCE'),
	(646, 'raw_mat_fabrication/raw_mat_usage_report.php', 'fc8546adff624823a125', '1', '0', 'REPORT RAW MATERIALS USAGE'),
	(647, 'raw_mat_fabrication/print_raw_mat_usage_report.php', '03decd9e37261cb08762', '1', '1', ''),
	(648, 'subcon_po_summary.php', '4e2b47cc6a217882db19', '1', '0', NULL),
	(649, 'print_labor_budget_mat_balance.php', '3d9f3577c3334cd41917', '1', '1', NULL),
	(650, 'print_subcon_po_summary.php', '5e341a1eaa0b43344e47', '1', '1', NULL),
	(651, 'transactions/encoding_history_report.php', '064e063e8f54e4bbee06', '1', '0', 'ENCODING HISTORY REPORT'),
	(652, 'transactions/form_heading.php.bak', '987cbe57b5f29003b737', '1', '0', NULL),
	(653, 'transactions/print_encoding_history_report.php', 'b25e60345ea1fa5e1d4d', '1', '1', ''),
	(654, 'transactions/print_check_commercial.php', 'f4df5694d7a76850b33a', '1', '1', NULL),
	(655, 'transactions/print_apv_report.php', 'bbaa76c8676e3a12cf22', '1', '1', NULL),
	(656, 'print_report_po.php.bak', '8af3f858d7b65bec4a20', '1', '1', NULL),
	(657, 'ap_expenses.php.bak', '6e348289b4b16bac0c2d', '1', '0', NULL),
	(658, 'print_released_checks.php', '3f2ca228829780d3fcc9', '1', '1', NULL),
	(659, 'print_disbursement_voucher.php', '12fa2e09d51621404149', '1', '1', NULL),
	(660, 'print_unreleased_checks.php', 'fe3f040e82d6f6955b20', '1', '1', NULL),
	(661, 'catalogs.php', 'a2baeba22b174bdb591d', '1', '0', NULL),
	(662, 'transactions/print_ts_log_history.php', '41eff5440c1a417f8630', '1', '1', NULL),
	(663, 'transactions/ts_log.php', 'bbea569b7e785008fc55', '1', '0', NULL),
	(664, 'transactions/ts_log_history.php', '6c3e1e3f7bb30954583e', '1', '0', NULL),
	(665, 'transactions/print_tslog.php', 'd4c02ca7f6a431c28380', '1', '1', NULL),
	(666, 'joborder/tires.php', '57d53a40fdc0d84c3b0e', '1', '0', NULL),
	(667, 'stocks_return_history.php', '1f14e3990193c7e36395', '1', '0', NULL),
	(668, 'print_stocks_return_history_all_projects.php', 'f1bdfeb209d8ca57e61f', '1', '1', NULL),
	(669, 'print_stocks_return_summary.php', 'bf4aaf9606d1e9cb5319', '1', '1', NULL),
	(670, 'print_stocks_return_history.php', '13d8a0270402181793f3', '1', '1', NULL),
	(671, 'tire/tires_search.php', '58b3c1fec2d7c4509e3d', '1', '0', NULL),
	(672, 'tire/branding_history_asof.php', '3ce9160e6682d66a9662', '1', '0', NULL),
	(673, 'tire/print_branding_history.php', 'd491a45cbe4a62813d23', '1', '1', NULL),
	(674, 'tire/print_branding_history_asof.php', 'e4f678b0e38a5694cc0d', '1', '1', NULL),
	(675, 'tire/branding_history.php', 'a97103dde92090c04de4', '1', '0', NULL),
	(676, 'masterfiles/tire_position.php', '171ee44ccd4821386a6e', '1', '0', NULL),
	(677, 'joborder/tiretransfer.php', '448139f0399477376b02', '1', '0', NULL),
	(678, 'petty_cash/petty_cash_unliquidated_report.php', '745b8f1125e585c85167', '1', '0', NULL),
	(679, 'petty_cash/print_petty_cash_unliquidated_report.php', '867bdbb11080199200be', '1', '1', NULL),
	(680, 'tires_search.php', 'deb363a7456859ce1277', '1', '0', NULL),
	(681, 'print_cv2.php', 'c0e656f071a424dfc62e', '1', '1', NULL),
	(682, 'print_issuance_history_summary_for_tire.php', '8d58e7297be5202da60b', '1', '1', NULL),
	(683, 'encoding_history_report.php', '4c2eee8eb6a2220cc613', '1', '0', NULL),
	(684, 'branding.php', '13841f4cea109650c219', '1', '0', NULL),
	(685, 'print_check_commercial3.php', '067f98e4b5d229a0bbf5', '1', '1', NULL),
	(686, 'print_cv3.php', '35e486a2204f49f5bda9', '1', '1', NULL),
	(687, 'print_apv_report.php', 'b8bd64d3bf3ec728a8f9', '1', '1', NULL),
	(688, 'tire/eq_tires.php', '629d544dba8f7ebf1672', '1', '0', NULL),
	(689, 'tire/tirelist.php', '6bc84235f84752713d19', '1', '0', NULL),
	(690, 'tire/print_eq_tires.php', '775d4c1abf82727f66b8', '1', '1', NULL),
	(691, 'tire/print_tire_list.php', '0bd35b24cf62b4bfe4f0', '1', '1', NULL),
	(692, 'tire/junktireslist.php', '8d8bd2cefa708cab8f94', '1', '0', NULL),
	(693, 'tire/print_tire_list_junk.php', '3d5531b1343c752896d2', '1', '1', NULL),
	(694, 'tire/eq_tires_2.php', 'a5b395a328665a97a46f', '1', '0', NULL),
	(695, 'tire/print_eq_tires_2.php', 'd1fc4344f269f11104bb', '1', '1', NULL),
	(696, 'project_type.php', 'acba5bbccbdcf93cf99d', '1', '0', NULL),
	(697, 'new_print_trial_balance.php', '91450c4db8f22be53365', '1', '0', NULL),
	(698, 'new_trial_balance.php', 'f2e78ab1f7a4d1dd7903', '1', '0', NULL),
	(699, 'new_print_trial_balance_v2.php', '2579eb671e9317622b90', '1', '0', NULL),
	(700, 'print_parent_account_report.php', '3475851cd33b5d16500b', '1', '1', NULL),
	(701, 'parent_account_report.php', 'ecd7f840da75828b8205', '1', '0', NULL),
	(702, 'new_trial_balance_v2.php', '2ba22b2782d35d41e684', '1', '0', NULL),
	(703, 'batching_plant/premix_delivery_report.php', 'a28ed1499cc99665ac09', '1', '0', NULL),
	(704, 'batching_plant/print_premix_delivery_report.php', '743a227b604055594b28', '1', '1', NULL),
	(705, 'balance_sheet.php', 'f29e65ed85f93bacfe23', '1', '0', NULL),
	(706, 'incomeStatementBackUp.php', '2063e5a00a7e58bd975a', '1', '0', NULL),
	(707, 'print_issuance_history_summary_for_fuel.php', '0ec231c243a44f5b32e3', '1', '1', NULL),
	(708, 'printBalanceSheet3.php', '454cec526eac65b4459e', '1', '1', NULL),
	(709, 'acctg_print_trial_balance_backup.php', '5d0b464436d313d6d6f4', '1', '0', NULL),
	(710, 'print_balance_sheet.php', 'd2078a97fafb4988e135', '1', '1', NULL),
	(711, 'printIncomeStatement_backup.php', '0e0d3bf2a527fffc5363', '1', '1', NULL),
	(712, 'print_sales_invoice_backup.php', '66ee3d9f60991af8f1aa', '1', '1', NULL),
	(713, 'print_income_statement.php', '36b3a3a5b4ece9e62d18', '1', '1', NULL),
	(714, 'income_statement.php', 'd31cbd4efcdb2adf7e4a', '1', '0', NULL),
	(715, 'contracts/contract_tsp.php', '589a8cb8ae084118f6f1', '1', '0', NULL),
	(716, 'contracts/contract_raf.php', '551aac24d3a57e63016a', '1', '0', NULL),
	(717, 'contracts/contract_oncall.php', 'b0720c313561b29e7f77', '1', '0', NULL),
	(718, 'contracts/print_contract_alp.php', 'b5231dae377a9b4bbe2d', '1', '1', NULL),
	(719, 'contracts/contract_alp.php', '31d0252ff9e2d774014f', '1', '0', NULL),
	(720, 'contracts/print_contract_oc.php', 'ef218bb6b6de9b23a62e', '1', '1', NULL),
	(721, 'contracts/print_contract_raf.php', 'd4d2f8c4baaeb19afd83', '1', '1', NULL),
	(722, 'contracts/print_contract_tsp.php', 'f1ff6a0e0849127065e5', '1', '1', NULL),
	(723, 'beginning_balance_breakdown.php', 'ac4cbf8c285f7c13ac97', '1', '0', NULL),
	(724, 'print_beginning_balance_report.php', '1f95debe987ec1a16011', '1', '1', NULL),
	(725, 'beginning_balance.php', '9e1c78827c6fd7eec273', '1', '0', NULL),
	(726, 'beginning_balance_history.php', '07b8e5e0db254ecb2ab7', '1', '0', NULL),
	(727, 'print_beg_bal_report.php', '0435b35ab56d2748bf2a', '1', '1', NULL),
	(728, 'print_beginning_balance_breakdown.php', 'e36212710df3dbde1c96', '1', '1', NULL),
	(729, 'print_error_checker.php', '819f1f398bc8c0692e23', '1', '1', NULL),
	(730, 'error_checker.php', 'c2b6a60c9c32715b7cc9', '1', '0', NULL),
	(731, 'eur/print_eur.php', 'c4a6b883e3bacc07a5f8', '1', '1', NULL),
	(732, 'eur/print_iso_eur.php', 'b67d39d174bd1e79e9b9', '1', '1', NULL),
	(733, 'eur/print_eur_summary2.php', '0b99da53496733c6ec3c', '1', '1', NULL),
	(734, 'new_print_trial_balance_v3.php', '28a1c251574173684ca1', '1', '0', NULL),
	(735, 'new_trial_balance_v3.php', 'f0482cf5c1128903b0cc', '1', '0', NULL),
	(736, 'new_trial_balance_v4.php', 'ee32c62993f65ade8268', '1', '0', NULL),
	(737, 'new_print_trial_balance_v4.php', '83fdebe59d596df1f0ef', '1', '0', NULL),
	(738, 'spo_search_for_apv.php', '1be1ae541fedfde18ef9', '1', '0', NULL),
	(739, 'additional_transac/print_gatepass.php', '6473ab7c1c5001420b90', '1', '1', NULL),
	(740, 'additional_transac/print_leave_form.php', '867235600225c924bf19', '1', '1', NULL),
	(741, 'additional_transac/leave.php', 'b06db1ac4fbadfc63ff4', '1', '0', NULL),
	(742, 'print_wtax_mrr_report.php', '9241ea82ac5a4a63b5b9', '1', '1', NULL),
	(743, 'wtax_mrr_report.php', '319cccdea991e75dbeef', '1', '0', NULL),
	(744, 'print_wtax_mrr_summary.php', 'b9b9a87bf33c6a4ed88a', '1', '1', NULL),
	(745, 'acctg_gl_listings_limited.php', 'f331b6e92d961b494836', '1', '0', NULL),
	(746, 'acctg_print_gl_listings_limited.php', '1bee8ca74ae8eb61174a', '1', '0', NULL),
	(747, 'cv_history.php', 'bd60bdfaafcb98014b36', '1', '0', NULL),
	(748, 'print_cv_history.php', 'fa410f44e0c82accd420', '1', '1', NULL),
	(749, 'asset_circulation/asset_circulation.php', '6607e797fc74266fa964', '1', '0', NULL),
	(750, 'asset_circulation/print_item_report.php', 'be1808ed57dda715307f', '1', '1', NULL),
	(751, 'asset_circulation/asset_circulation_report.php', '8de0514b2e475aec428a', '1', '0', NULL),
	(752, 'asset_circulation/item_report.php', 'a55cc1d4ead7b4522562', '1', '0', NULL),
	(753, 'asset_circulation/item_details.php', '942d7963f16566b7a4a0', '1', '0', NULL),
	(754, 'asset_circulation/item.php', 'cd79fabd1bdc6787c5e0', '1', '0', NULL),
	(755, 'asset_circulation/print_asset_circulation_report.php', 'c7e3ffc1a2584d66c1d2', '1', '1', NULL),
	(756, 'supplier_adjustments.php', '6863579762cbdd384202', '1', '0', NULL),
	(757, 'print_supplier_adjustments.php', '2ea66cf1b2197ec1b453', '1', '1', NULL),
	(758, 'print_mrr_history_all_suppliers.php', 'e9a556f79c781b7d650b', '1', '1', NULL),
	(759, 'apv_report.php', '9a328b24953c0be95344', '1', '0', NULL),
	(760, 'print_sales_return.php', '376a5c9376f4fc20020c', '1', '1', NULL),
	(761, 'sub_apv_report.php', 'b7b9ea14436d5f68ba27', '1', '0', NULL),
	(762, 'acctg_print_gl_listings_modified.php', 'ee7e0ef4c4cece4b9009', '1', '0', NULL),
	(763, 'income_statement_project.php', '76402b6677f29c9602ae', '1', '0', NULL),
	(764, 'print_po_cancellation.php', '542ba066219306aee475', '1', '1', NULL),
	(765, 'cancellation_search.php', '0968d837e5e7d474e0ae', '1', '0', NULL),
	(766, 'po_cancellation.php', '5b94ad18bbc6c24c4123', '1', '0', NULL),
	(767, 'ap_summary_of_ap_tr.php', 'b38a1001c1981a8656c8', '1', '0', NULL),
	(768, 'ap_print_summary_of_ap_tr.php', '0d2d940a6c0e3ebc90a1', '1', '0', NULL),
	(769, 'subcon_print_retention_report.php', 'b8ab6b061488816ee118', '1', '0', NULL),
	(770, 'subcon_retention_report.php', '9ddd6fb856b04407756f', '1', '0', NULL),
	(771, 'acctg_print_gl_listings3.php', '2a01322bb2e336c18ed5', '1', '0', NULL),
	(772, 'batching_plant/print_sales_report.php', '42ba968db6466464ee42', '1', '1', NULL),
	(773, 'spo_search.php', 'fe041365bb6037d10a73', '1', '0', NULL),
	(774, 'detect_transactions_print.php', 'e2df0cccb246a5b3c403', '1', '0', NULL),
	(775, 'detect_transactions.php', '5cb6c2e1a3869beaea6c', '1', '0', NULL),
	(776, 'BIR2.php', 'c18a40b9f8f0adb48a51', '1', '0', NULL),
	(777, 'rr_evaluation.php', '219b466d0f92bcdb86bc', '1', '0', NULL),
	(778, 'print_rr_no_evaluation.php', 'b5034558f36ce53fbb51', '1', '1', NULL),
	(779, 'print_rr_evaluation.php', 'e8d8b777168c2457f2e3', '1', '1', NULL),
	(780, 'po_search_limited.php', '34cff2860b1630610981', '1', '0', NULL),
	(781, 'print_rr_all_evaluation.php', 'a768b277265cdda2f4da', '1', '1', NULL),
	(782, 'print_tax_checker.php', '233d7111410f46d92c50', '1', '1', NULL),
	(783, 'tax_checker.php', '1d33079a76f26d1460bc', '1', '0', NULL);
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;


-- Dumping structure for table default_db.projects
CREATE TABLE IF NOT EXISTS `projects` (
  `project_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `project_name` text NOT NULL,
  `project_code` varchar(20) NOT NULL,
  `location` text NOT NULL,
  `owner` varchar(100) NOT NULL,
  `contract_amount` decimal(12,2) DEFAULT NULL,
  `client_project` char(1) DEFAULT '0',
  `project_type_id` int(11) NOT NULL,
  `pstatus` char(2) NOT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.projects: 1 rows
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` (`project_id`, `project_name`, `project_code`, `location`, `owner`, `contract_amount`, `client_project`, `project_type_id`, `pstatus`) VALUES
	(1, 'Sample Project', '123', 'Bacolod City', 'ABC ', 2000000.00, '1', 1, 'O');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;


-- Dumping structure for table default_db.project_type
CREATE TABLE IF NOT EXISTS `project_type` (
  `project_type_id` bigint(6) NOT NULL AUTO_INCREMENT,
  `project_type` varchar(30) NOT NULL,
  PRIMARY KEY (`project_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.project_type: 2 rows
/*!40000 ALTER TABLE `project_type` DISABLE KEYS */;
INSERT INTO `project_type` (`project_type_id`, `project_type`) VALUES
	(1, 'Horizontal Project'),
	(3, 'Vertical Project');
/*!40000 ALTER TABLE `project_type` ENABLE KEYS */;


-- Dumping structure for table default_db.pr_detail
CREATE TABLE IF NOT EXISTS `pr_detail` (
  `pr_detail_id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `pr_header_id` bigint(12) unsigned NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `in_budget` char(1) NOT NULL,
  `allowed` char(1) NOT NULL,
  `request_quantity` decimal(12,4) NOT NULL,
  `warehouse_quantity` decimal(12,4) NOT NULL,
  `unit2` varchar(30) DEFAULT NULL,
  `quantity2` decimal(12,4) DEFAULT NULL,
  `details` varchar(160) DEFAULT NULL,
  PRIMARY KEY (`pr_detail_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pr_detail: 3 rows
/*!40000 ALTER TABLE `pr_detail` DISABLE KEYS */;
INSERT INTO `pr_detail` (`pr_detail_id`, `pr_header_id`, `stock_id`, `quantity`, `in_budget`, `allowed`, `request_quantity`, `warehouse_quantity`, `unit2`, `quantity2`, `details`) VALUES
	(1, 2, 1, 12.0000, '', '1', 12.0000, 0.0000, '', 0.0000, 'test'),
	(2, 2, 0, 0.0000, '', '0', 0.0000, 0.0000, '', 0.0000, ''),
	(3, 3, 1, 20.0000, '', '1', 20.0000, 0.0000, '', 0.0000, '');
/*!40000 ALTER TABLE `pr_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.pr_equipment_detail
CREATE TABLE IF NOT EXISTS `pr_equipment_detail` (
  `pr_equipment_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `pr_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `rate_per_day` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `allowed` char(1) NOT NULL,
  PRIMARY KEY (`pr_equipment_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pr_equipment_detail: 0 rows
/*!40000 ALTER TABLE `pr_equipment_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `pr_equipment_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.pr_fuel_detail
CREATE TABLE IF NOT EXISTS `pr_fuel_detail` (
  `pr_fuel_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `pr_header_id` bigint(12) NOT NULL,
  `fuel_id` bigint(12) NOT NULL,
  `equipment_id` bigint(12) NOT NULL,
  `consumption_per_day` decimal(12,2) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `cost_per_litter` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `allowed` char(1) NOT NULL,
  `warehouse_quantity` decimal(12,2) NOT NULL,
  `request_quantity` decimal(12,2) NOT NULL,
  PRIMARY KEY (`pr_fuel_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pr_fuel_detail: 0 rows
/*!40000 ALTER TABLE `pr_fuel_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `pr_fuel_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.pr_header
CREATE TABLE IF NOT EXISTS `pr_header` (
  `pr_header_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `description` text NOT NULL,
  `approval_status` char(1) NOT NULL DEFAULT 'P',
  `scope_of_work` varchar(100) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(40) NOT NULL,
  `approved_by` varchar(40) NOT NULL,
  `date_needed` date NOT NULL,
  `no_budget` char(1) DEFAULT '0',
  `type` varchar(40) NOT NULL,
  `is_used` int(10) NOT NULL,
  `datetime_encoded` datetime DEFAULT NULL,
  `approval_date` datetime NOT NULL,
  PRIMARY KEY (`pr_header_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pr_header: 3 rows
/*!40000 ALTER TABLE `pr_header` DISABLE KEYS */;
INSERT INTO `pr_header` (`pr_header_id`, `date`, `project_id`, `description`, `approval_status`, `scope_of_work`, `work_category_id`, `sub_work_category_id`, `status`, `user_id`, `approved_by`, `date_needed`, `no_budget`, `type`, `is_used`, `datetime_encoded`, `approval_date`) VALUES
	(1, '2017-08-13', 1, 'test', 'P', '', 4, 0, 'C', '20170813-105326', '', '2017-08-14', '0', '', 0, '2017-08-13 20:05:11', '0000-00-00 00:00:00'),
	(2, '2017-08-13', 1, 'test', 'A', '', 4, 0, 'F', '20170813-105326', '', '2017-08-13', '1', '', 0, NULL, '0000-00-00 00:00:00'),
	(3, '2017-08-13', 1, 'test 2', 'A', '', 4, 0, 'F', '20170813-105326', '', '2017-08-13', '1', '', 0, NULL, '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `pr_header` ENABLE KEYS */;


-- Dumping structure for table default_db.pr_service_detail
CREATE TABLE IF NOT EXISTS `pr_service_detail` (
  `pr_service_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `pr_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `rate_per_day` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `allowed` char(1) NOT NULL,
  PRIMARY KEY (`pr_service_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pr_service_detail: 0 rows
/*!40000 ALTER TABLE `pr_service_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `pr_service_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.pr_warehouse
CREATE TABLE IF NOT EXISTS `pr_warehouse` (
  `pr_warehouse_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `pr_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `fuel_id` bigint(12) NOT NULL,
  `equipment_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `type` char(1) NOT NULL,
  PRIMARY KEY (`pr_warehouse_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.pr_warehouse: 0 rows
/*!40000 ALTER TABLE `pr_warehouse` DISABLE KEYS */;
/*!40000 ALTER TABLE `pr_warehouse` ENABLE KEYS */;


-- Dumping structure for table default_db.purchase_detail
CREATE TABLE IF NOT EXISTS `purchase_detail` (
  `pdetail_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pheader_id` bigint(16) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `poqty` decimal(10,3) NOT NULL,
  `pocost` decimal(14,6) NOT NULL,
  `inqty` decimal(10,3) NOT NULL,
  `incost` decimal(14,6) NOT NULL,
  `discrate` varchar(20) NOT NULL,
  `discamt` decimal(12,2) NOT NULL,
  `netamount` decimal(14,2) NOT NULL,
  PRIMARY KEY (`pdetail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.purchase_detail: 0 rows
/*!40000 ALTER TABLE `purchase_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.purchase_header
CREATE TABLE IF NOT EXISTS `purchase_header` (
  `pheader_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `ponum` varchar(15) NOT NULL,
  `invnum` varchar(15) NOT NULL,
  `rrnum` varchar(15) NOT NULL,
  `podate` date NOT NULL,
  `invdate` date NOT NULL,
  `rrdate` date NOT NULL,
  `payment_type` char(1) NOT NULL,
  `gross_account` decimal(14,2) NOT NULL,
  `total_discount` decimal(12,2) NOT NULL,
  `total_charges` decimal(14,2) NOT NULL,
  `terms` varchar(10) NOT NULL,
  `remarks` varchar(100) NOT NULL,
  `status` char(1) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `audit` blob NOT NULL,
  `user_id` bigint(8) NOT NULL,
  PRIMARY KEY (`pheader_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.purchase_header: 0 rows
/*!40000 ALTER TABLE `purchase_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_header` ENABLE KEYS */;


-- Dumping structure for table default_db.release_type
CREATE TABLE IF NOT EXISTS `release_type` (
  `release_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `release_type` varchar(100) NOT NULL,
  PRIMARY KEY (`release_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.release_type: 0 rows
/*!40000 ALTER TABLE `release_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `release_type` ENABLE KEYS */;


-- Dumping structure for table default_db.return_detail
CREATE TABLE IF NOT EXISTS `return_detail` (
  `return_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `return_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  PRIMARY KEY (`return_detail_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.return_detail: 0 rows
/*!40000 ALTER TABLE `return_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.return_header
CREATE TABLE IF NOT EXISTS `return_header` (
  `return_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `remarks` blob,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(100) NOT NULL,
  `scope_of_work` varchar(100) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  PRIMARY KEY (`return_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.return_header: 0 rows
/*!40000 ALTER TABLE `return_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `return_header` ENABLE KEYS */;


-- Dumping structure for table default_db.rmy_employee_type
CREATE TABLE IF NOT EXISTS `rmy_employee_type` (
  `rmy_employee_type_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `rmy_employee_type` varchar(100) NOT NULL,
  `rmy_employee_type_void` char(1) DEFAULT '0',
  PRIMARY KEY (`rmy_employee_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.rmy_employee_type: 0 rows
/*!40000 ALTER TABLE `rmy_employee_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `rmy_employee_type` ENABLE KEYS */;


-- Dumping structure for table default_db.rr_detail
CREATE TABLE IF NOT EXISTS `rr_detail` (
  `rr_detail_id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `rr_header_id` bigint(13) unsigned NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `discount` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `invoice` varchar(30) NOT NULL,
  `quantity_cum` decimal(12,4) DEFAULT NULL,
  `driverID` int(20) DEFAULT NULL,
  `equipment_id` bigint(12) DEFAULT NULL,
  `_unit` varchar(40) NOT NULL,
  `account_id` bigint(12) DEFAULT NULL,
  `details` text,
  `asset_code` varchar(100) NOT NULL,
  `date_acquired` date NOT NULL,
  `estimated_life` int(10) NOT NULL,
  `serial_no` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  PRIMARY KEY (`rr_detail_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.rr_detail: 0 rows
/*!40000 ALTER TABLE `rr_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `rr_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.rr_evaluation
CREATE TABLE IF NOT EXISTS `rr_evaluation` (
  `rr_evaluation_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `rr_header_id` bigint(16) NOT NULL,
  `eva_ps` decimal(4,2) NOT NULL,
  `eva_d` decimal(4,2) NOT NULL,
  `eva_cr` decimal(4,2) NOT NULL,
  `eva_sf` decimal(4,2) NOT NULL,
  `eva_p` decimal(4,2) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`rr_evaluation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.rr_evaluation: 0 rows
/*!40000 ALTER TABLE `rr_evaluation` DISABLE KEYS */;
/*!40000 ALTER TABLE `rr_evaluation` ENABLE KEYS */;


-- Dumping structure for table default_db.rr_header
CREATE TABLE IF NOT EXISTS `rr_header` (
  `rr_header_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `account_id` bigint(8) NOT NULL,
  `grossamount` decimal(12,2) NOT NULL,
  `netamount` decimal(12,2) NOT NULL,
  `discounttotal` decimal(10,2) NOT NULL,
  `tax` decimal(12,2) NOT NULL,
  `paytype` char(1) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  `po_header_id` bigint(16) NOT NULL,
  `rr_in` char(1) NOT NULL DEFAULT 'W',
  `project_id` bigint(12) NOT NULL,
  `supplier_id` bigint(8) NOT NULL,
  `discount_amount` decimal(12,2) DEFAULT NULL,
  `advance_payment_amount` decimal(12,4) DEFAULT NULL,
  `rr_type` char(1) DEFAULT 'M',
  `ppe_gchart_id` bigint(12) DEFAULT NULL,
  `encoded_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`rr_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.rr_header: 0 rows
/*!40000 ALTER TABLE `rr_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `rr_header` ENABLE KEYS */;


-- Dumping structure for table default_db.rtp_detail
CREATE TABLE IF NOT EXISTS `rtp_detail` (
  `rtp_detail_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `rtp_header_id` bigint(12) unsigned DEFAULT NULL,
  `description` text,
  `quantity` decimal(12,4) DEFAULT '0.0000',
  `rtp_void` char(1) DEFAULT '0',
  `unit` varchar(100) DEFAULT NULL,
  `mcd_qty` decimal(12,4) DEFAULT NULL,
  `budget_qty` decimal(12,4) DEFAULT NULL,
  `actual_qty` decimal(12,4) DEFAULT NULL,
  `balance_qty` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`rtp_detail_id`),
  KEY `rtp_header_id` (`rtp_header_id`),
  CONSTRAINT `fk_rtp_header_id` FOREIGN KEY (`rtp_header_id`) REFERENCES `rtp_header` (`rtp_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.rtp_detail: ~0 rows (approximately)
/*!40000 ALTER TABLE `rtp_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `rtp_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.rtp_header
CREATE TABLE IF NOT EXISTS `rtp_header` (
  `rtp_header_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(12) DEFAULT NULL,
  `work_category_id` bigint(12) DEFAULT NULL,
  `sub_work_category_id` bigint(12) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` char(1) DEFAULT 'S',
  `user_id` varchar(50) DEFAULT NULL,
  `remarks` text,
  `date_needed` date DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `type` varchar(100) DEFAULT 'RTP',
  `datetime_encoded` datetime DEFAULT NULL,
  PRIMARY KEY (`rtp_header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.rtp_header: ~0 rows (approximately)
/*!40000 ALTER TABLE `rtp_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `rtp_header` ENABLE KEYS */;


-- Dumping structure for table default_db.sales_invoice
CREATE TABLE IF NOT EXISTS `sales_invoice` (
  `sales_invoice_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `date_received` date NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `user_id` varchar(30) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `invoice_no` varchar(40) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `sales_gchart_id` bigint(12) NOT NULL,
  `ar_gchart_id` bigint(12) NOT NULL,
  PRIMARY KEY (`sales_invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sales_invoice: 0 rows
/*!40000 ALTER TABLE `sales_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_invoice` ENABLE KEYS */;


-- Dumping structure for table default_db.sales_invoice_detail
CREATE TABLE IF NOT EXISTS `sales_invoice_detail` (
  `sales_invoice_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_invoice_id` int(11) NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`sales_invoice_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sales_invoice_detail: 0 rows
/*!40000 ALTER TABLE `sales_invoice_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_invoice_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.sales_return_detail
CREATE TABLE IF NOT EXISTS `sales_return_detail` (
  `sales_return_detail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_return_header_id` bigint(12) unsigned NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `sales_return_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sales_return_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sales_return_detail: 0 rows
/*!40000 ALTER TABLE `sales_return_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_return_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.sales_return_header
CREATE TABLE IF NOT EXISTS `sales_return_header` (
  `sales_return_header_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `project_id` bigint(12) unsigned NOT NULL,
  `reference` varchar(100) NOT NULL,
  `remarks` text,
  `status` char(1) NOT NULL DEFAULT 'S',
  `prepared_by` varchar(100) NOT NULL,
  `prepared_time` datetime NOT NULL,
  `edited_by` varchar(100) DEFAULT NULL,
  `last_edit_time` datetime DEFAULT NULL,
  PRIMARY KEY (`sales_return_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sales_return_header: 0 rows
/*!40000 ALTER TABLE `sales_return_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_return_header` ENABLE KEYS */;


-- Dumping structure for table default_db.sections
CREATE TABLE IF NOT EXISTS `sections` (
  `section_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(12) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `section_name` text NOT NULL,
  `section_code` varchar(100) NOT NULL,
  `section_description` text NOT NULL,
  `is_deleted` int(10) NOT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sections: 0 rows
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;


-- Dumping structure for table default_db.service_payment_detail
CREATE TABLE IF NOT EXISTS `service_payment_detail` (
  `service_payment_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `service_pay_header_id` bigint(12) NOT NULL,
  `po_header_id` bigint(16) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  PRIMARY KEY (`service_payment_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.service_payment_detail: 0 rows
/*!40000 ALTER TABLE `service_payment_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_payment_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.service_pay_cash
CREATE TABLE IF NOT EXISTS `service_pay_cash` (
  `service_pay_cash_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `service_pay_header_id` bigint(12) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`service_pay_cash_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.service_pay_cash: 0 rows
/*!40000 ALTER TABLE `service_pay_cash` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_pay_cash` ENABLE KEYS */;


-- Dumping structure for table default_db.service_pay_check
CREATE TABLE IF NOT EXISTS `service_pay_check` (
  `service_pay_check_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `service_pay_header_id` bigint(12) NOT NULL,
  `bank` varchar(25) NOT NULL,
  `checkno` varchar(20) NOT NULL,
  `datecheck` date NOT NULL,
  `checkamount` decimal(14,2) NOT NULL,
  `checkstatus` varchar(12) NOT NULL,
  PRIMARY KEY (`service_pay_check_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.service_pay_check: 0 rows
/*!40000 ALTER TABLE `service_pay_check` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_pay_check` ENABLE KEYS */;


-- Dumping structure for table default_db.service_pay_header
CREATE TABLE IF NOT EXISTS `service_pay_header` (
  `service_pay_header_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `supplier_id` bigint(8) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`service_pay_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.service_pay_header: 0 rows
/*!40000 ALTER TABLE `service_pay_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_pay_header` ENABLE KEYS */;


-- Dumping structure for table default_db.service_rr_detail
CREATE TABLE IF NOT EXISTS `service_rr_detail` (
  `service_rr_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `service_rr_header_id` bigint(12) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `days` decimal(12,2) NOT NULL,
  `rate_per_day` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`service_rr_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.service_rr_detail: 0 rows
/*!40000 ALTER TABLE `service_rr_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_rr_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.service_rr_header
CREATE TABLE IF NOT EXISTS `service_rr_header` (
  `service_rr_header_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `paytype` char(1) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `status` char(1) NOT NULL,
  `po_header_id` bigint(16) NOT NULL,
  `rr_in` char(1) NOT NULL DEFAULT 'W',
  `project_id` bigint(12) NOT NULL,
  `supplier_id` bigint(8) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `scope_of_work` varchar(100) NOT NULL,
  PRIMARY KEY (`service_rr_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.service_rr_header: 0 rows
/*!40000 ALTER TABLE `service_rr_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_rr_header` ENABLE KEYS */;


-- Dumping structure for table default_db.soa_history
CREATE TABLE IF NOT EXISTS `soa_history` (
  `soa_id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  PRIMARY KEY (`soa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.soa_history: 0 rows
/*!40000 ALTER TABLE `soa_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `soa_history` ENABLE KEYS */;


-- Dumping structure for table default_db.spo_detail
CREATE TABLE IF NOT EXISTS `spo_detail` (
  `spo_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `po_header_id` bigint(12) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`spo_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.spo_detail: ~0 rows (approximately)
/*!40000 ALTER TABLE `spo_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `spo_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.sss_contrib
CREATE TABLE IF NOT EXISTS `sss_contrib` (
  `sss_contribID` int(10) NOT NULL AUTO_INCREMENT,
  `range_value_min` decimal(10,2) NOT NULL,
  `range_value_max` decimal(10,2) NOT NULL,
  `er` decimal(10,2) NOT NULL,
  `ee` decimal(10,2) NOT NULL,
  `ec` decimal(10,2) NOT NULL,
  PRIMARY KEY (`sss_contribID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sss_contrib: 0 rows
/*!40000 ALTER TABLE `sss_contrib` DISABLE KEYS */;
/*!40000 ALTER TABLE `sss_contrib` ENABLE KEYS */;


-- Dumping structure for table default_db.stockcard
CREATE TABLE IF NOT EXISTS `stockcard` (
  `stockcard_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `typeoftransaction` char(2) NOT NULL,
  `qtyin` double(12,6) NOT NULL,
  `qtyout` double(12,6) NOT NULL,
  `balance` double(12,6) NOT NULL,
  PRIMARY KEY (`stockcard_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.stockcard: 0 rows
/*!40000 ALTER TABLE `stockcard` DISABLE KEYS */;
/*!40000 ALTER TABLE `stockcard` ENABLE KEYS */;


-- Dumping structure for table default_db.subd
CREATE TABLE IF NOT EXISTS `subd` (
  `subd_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `subd` varchar(255) NOT NULL,
  `subd_address1` text,
  `subd_address2` text,
  `subd_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`subd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.subd: ~0 rows (approximately)
/*!40000 ALTER TABLE `subd` DISABLE KEYS */;
/*!40000 ALTER TABLE `subd` ENABLE KEYS */;


-- Dumping structure for table default_db.sub_apv_detail
CREATE TABLE IF NOT EXISTS `sub_apv_detail` (
  `sub_apv_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `sub_apv_header_id` bigint(12) NOT NULL,
  `description` text NOT NULL,
  `sub_description` text NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`sub_apv_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sub_apv_detail: 0 rows
/*!40000 ALTER TABLE `sub_apv_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_apv_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.sub_apv_header
CREATE TABLE IF NOT EXISTS `sub_apv_header` (
  `sub_apv_header_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `po_header_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `po_date` date NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `supplier_id` bigint(12) NOT NULL,
  `terms` int(4) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(30) NOT NULL,
  `wtax_gchart_id` bigint(12) DEFAULT NULL,
  `vat_gchart_id` bigint(12) DEFAULT NULL,
  `vat` int(2) NOT NULL DEFAULT '0',
  `wtax` int(2) NOT NULL DEFAULT '0',
  `discount_amount` decimal(12,2) DEFAULT NULL,
  `remarks` text,
  `retention_rate` decimal(12,2) DEFAULT NULL,
  `chargable_amount` decimal(12,2) DEFAULT NULL,
  `other_chargable_amount` decimal(12,2) NOT NULL,
  `budget_source` varchar(25) NOT NULL,
  `budget_code` varchar(25) NOT NULL,
  PRIMARY KEY (`sub_apv_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sub_apv_header: 0 rows
/*!40000 ALTER TABLE `sub_apv_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_apv_header` ENABLE KEYS */;


-- Dumping structure for table default_db.sub_gchart
CREATE TABLE IF NOT EXISTS `sub_gchart` (
  `sub_gchart_id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `sub_gchart` varchar(100) NOT NULL,
  `sub_gchart_void` char(1) DEFAULT '0',
  PRIMARY KEY (`sub_gchart_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sub_gchart: 13 rows
/*!40000 ALTER TABLE `sub_gchart` DISABLE KEYS */;
INSERT INTO `sub_gchart` (`sub_gchart_id`, `sub_gchart`, `sub_gchart_void`) VALUES
	(1, 'Asset', '0'),
	(2, 'Current Liabilities', '0'),
	(3, 'Current Asset', '0'),
	(4, 'Expense', '0'),
	(5, 'Other Asset', '0'),
	(6, 'Fixed Asset', '0'),
	(7, 'Sales', '0'),
	(8, 'Cost of Sales', '0'),
	(9, 'Other Income', '0'),
	(10, 'Other Liabilities', '0'),
	(11, 'Long Term Asset', '0'),
	(12, 'Long Term Liabilities', '0'),
	(13, 'Taxation', '0');
/*!40000 ALTER TABLE `sub_gchart` ENABLE KEYS */;


-- Dumping structure for table default_db.sub_spo_detail
CREATE TABLE IF NOT EXISTS `sub_spo_detail` (
  `sub_spo_detail_id` bigint(12) NOT NULL AUTO_INCREMENT,
  `pr_lb_id` int(11) NOT NULL,
  `spo_detail_id` bigint(12) NOT NULL,
  `sub_description` text NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `person` text,
  `chargables` text,
  PRIMARY KEY (`sub_spo_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.sub_spo_detail: ~0 rows (approximately)
/*!40000 ALTER TABLE `sub_spo_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `sub_spo_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `account_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `account` varchar(255) NOT NULL,
  `account_code` varchar(50) DEFAULT NULL,
  `address` text NOT NULL,
  `contactno` varchar(20) NOT NULL,
  `contactperson` varchar(50) NOT NULL,
  `term` varchar(50) NOT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `vat_type` varchar(11) NOT NULL,
  `subcon` char(1) DEFAULT '0',
  `advances_gchart_id` int(10) NOT NULL,
  `payable_gchart_id` int(11) NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.supplier: 1 rows
/*!40000 ALTER TABLE `supplier` DISABLE KEYS */;
INSERT INTO `supplier` (`account_id`, `account`, `account_code`, `address`, `contactno`, `contactperson`, `term`, `tin`, `vat_type`, `subcon`, `advances_gchart_id`, `payable_gchart_id`) VALUES
	(1, 'ABC Company', '1234', '', '', '', '', '', '', '0', 0, 0);
/*!40000 ALTER TABLE `supplier` ENABLE KEYS */;


-- Dumping structure for table default_db.tires
CREATE TABLE IF NOT EXISTS `tires` (
  `tire_id` int(10) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) NOT NULL,
  `purchased_date` date DEFAULT NULL,
  `installed_date` date DEFAULT NULL,
  `remarks` text NOT NULL,
  `branding_no` varchar(20) DEFAULT NULL,
  `type_id` int(10) NOT NULL,
  `size_id` bigint(10) NOT NULL,
  `eqID` int(10) NOT NULL,
  `size` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`tire_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.tires: 0 rows
/*!40000 ALTER TABLE `tires` DISABLE KEYS */;
/*!40000 ALTER TABLE `tires` ENABLE KEYS */;


-- Dumping structure for table default_db.tiretransfer
CREATE TABLE IF NOT EXISTS `tiretransfer` (
  `tiretransfer_header_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `project_id` bigint(12) NOT NULL,
  `equipment_id` bigint(12) NOT NULL,
  `driver_id` bigint(12) NOT NULL,
  `job_id` bigint(12) NOT NULL,
  `from_project_id` int(50) NOT NULL,
  `from_eqID` int(50) NOT NULL,
  `from_position` int(50) NOT NULL,
  `branding_num` varchar(100) NOT NULL,
  `to_project_id` int(50) NOT NULL,
  `to_eqID` int(50) NOT NULL,
  `to_position` int(50) NOT NULL,
  `inspected_by` bigint(12) NOT NULL,
  `estimated_hours` decimal(12,2) NOT NULL,
  `details` text NOT NULL,
  `conducted_by` bigint(12) NOT NULL,
  `date_started` date NOT NULL,
  `time_started` time NOT NULL,
  `date_completed` date NOT NULL,
  `time_completed` time NOT NULL,
  `trial_conducted_by` bigint(12) NOT NULL,
  `trial_date` date NOT NULL,
  `results` text NOT NULL,
  `accepted_by` bigint(12) NOT NULL,
  `accepted_date` date NOT NULL,
  `encoded_datetime` datetime NOT NULL,
  `encoded_by` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `jo_option` text,
  `reference` varchar(50) NOT NULL,
  `type` char(2) NOT NULL,
  PRIMARY KEY (`tiretransfer_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.tiretransfer: 0 rows
/*!40000 ALTER TABLE `tiretransfer` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiretransfer` ENABLE KEYS */;


-- Dumping structure for table default_db.tire_position
CREATE TABLE IF NOT EXISTS `tire_position` (
  `tire_pos_id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(100) NOT NULL,
  PRIMARY KEY (`tire_pos_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.tire_position: 0 rows
/*!40000 ALTER TABLE `tire_position` DISABLE KEYS */;
/*!40000 ALTER TABLE `tire_position` ENABLE KEYS */;


-- Dumping structure for table default_db.tire_size
CREATE TABLE IF NOT EXISTS `tire_size` (
  `size_id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sizes` varchar(15) NOT NULL,
  PRIMARY KEY (`size_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.tire_size: 0 rows
/*!40000 ALTER TABLE `tire_size` DISABLE KEYS */;
/*!40000 ALTER TABLE `tire_size` ENABLE KEYS */;


-- Dumping structure for table default_db.tire_type
CREATE TABLE IF NOT EXISTS `tire_type` (
  `type_id` int(10) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.tire_type: 0 rows
/*!40000 ALTER TABLE `tire_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `tire_type` ENABLE KEYS */;


-- Dumping structure for table default_db.transaction_status
CREATE TABLE IF NOT EXISTS `transaction_status` (
  `status_id` varchar(4) NOT NULL,
  `status` varchar(100) NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.transaction_status: 0 rows
/*!40000 ALTER TABLE `transaction_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction_status` ENABLE KEYS */;


-- Dumping structure for table default_db.transfer_detail
CREATE TABLE IF NOT EXISTS `transfer_detail` (
  `transfer_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `transfer_header_id` varchar(13) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`transfer_detail_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.transfer_detail: 0 rows
/*!40000 ALTER TABLE `transfer_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `transfer_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.transfer_header
CREATE TABLE IF NOT EXISTS `transfer_header` (
  `transfer_header_id` bigint(50) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `remarks` blob NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(20) NOT NULL,
  `scope_of_work` varchar(100) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `auto_issue` char(1) DEFAULT '0',
  `reference` varchar(30) DEFAULT NULL,
  `from_project_id` bigint(20) unsigned NOT NULL DEFAULT '9',
  `encoded_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`transfer_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.transfer_header: 0 rows
/*!40000 ALTER TABLE `transfer_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `transfer_header` ENABLE KEYS */;


-- Dumping structure for table default_db.transfer_log_detail
CREATE TABLE IF NOT EXISTS `transfer_log_detail` (
  `translog_detail_id` bigint(16) NOT NULL AUTO_INCREMENT,
  `translog_header_id` varchar(13) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `tslog_void` int(1) NOT NULL,
  PRIMARY KEY (`translog_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.transfer_log_detail: 0 rows
/*!40000 ALTER TABLE `transfer_log_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `transfer_log_detail` ENABLE KEYS */;


-- Dumping structure for table default_db.transfer_log_header
CREATE TABLE IF NOT EXISTS `transfer_log_header` (
  `translog_header_id` bigint(50) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(12) NOT NULL,
  `date` date NOT NULL,
  `remarks` varchar(200) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'S',
  `user_id` varchar(20) NOT NULL,
  `work_category_id` bigint(12) NOT NULL,
  `sub_work_category_id` bigint(12) NOT NULL,
  `reference` varchar(150) DEFAULT NULL,
  `to_project_id` bigint(20) unsigned NOT NULL,
  `datetime_encoded` datetime DEFAULT NULL,
  PRIMARY KEY (`translog_header_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.transfer_log_header: 0 rows
/*!40000 ALTER TABLE `transfer_log_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `transfer_log_header` ENABLE KEYS */;


-- Dumping structure for table default_db.vehicle_pass
CREATE TABLE IF NOT EXISTS `vehicle_pass` (
  `vh_number` bigint(20) NOT NULL AUTO_INCREMENT,
  `vh_date` date NOT NULL,
  `vh_time_out` time NOT NULL,
  `driverID` int(20) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `vh_purpose_id` int(10) NOT NULL,
  `vh_void` char(1) NOT NULL DEFAULT '0',
  `po_header_id` bigint(16) NOT NULL,
  `userID` varchar(200) NOT NULL,
  `vh_remarks` text NOT NULL,
  PRIMARY KEY (`vh_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.vehicle_pass: 0 rows
/*!40000 ALTER TABLE `vehicle_pass` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_pass` ENABLE KEYS */;


-- Dumping structure for table default_db.vehicle_pass_purpose
CREATE TABLE IF NOT EXISTS `vehicle_pass_purpose` (
  `vh_purpose_id` int(10) NOT NULL AUTO_INCREMENT,
  `vh_purpose_description` text NOT NULL,
  `vh_purpose_void` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vh_purpose_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.vehicle_pass_purpose: 0 rows
/*!40000 ALTER TABLE `vehicle_pass_purpose` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_pass_purpose` ENABLE KEYS */;


-- Dumping structure for table default_db.wall
CREATE TABLE IF NOT EXISTS `wall` (
  `id` varchar(200) NOT NULL,
  `wallmsg` text NOT NULL,
  `date_posted` datetime DEFAULT NULL,
  `posted_by` varchar(200) NOT NULL,
  `posted_to` varchar(200) NOT NULL,
  `important` char(1) DEFAULT '0',
  `read_marked` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.wall: 2 rows
/*!40000 ALTER TABLE `wall` DISABLE KEYS */;
INSERT INTO `wall` (`id`, `wallmsg`, `date_posted`, `posted_by`, `posted_to`, `important`, `read_marked`) VALUES
	('20170808-125418', 'Test ', '2017-08-08 12:53:54', '20080228-111008', '', '0', 0),
	('20170808-125436', 'Test 2', '2017-08-08 12:54:12', '20160719-110150', '', '0', 0);
/*!40000 ALTER TABLE `wall` ENABLE KEYS */;


-- Dumping structure for table default_db.withdraw_det
CREATE TABLE IF NOT EXISTS `withdraw_det` (
  `withdraw_det_id` bigint(20) NOT NULL,
  `stock_id` bigint(12) NOT NULL,
  `outqty` decimal(10,3) NOT NULL,
  `cost` decimal(14,6) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  PRIMARY KEY (`withdraw_det_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.withdraw_det: 0 rows
/*!40000 ALTER TABLE `withdraw_det` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdraw_det` ENABLE KEYS */;


-- Dumping structure for table default_db.withdraw_hdr
CREATE TABLE IF NOT EXISTS `withdraw_hdr` (
  `withdraw_hdr_id` bigint(16) NOT NULL,
  `withdrawno` varchar(15) NOT NULL,
  `jobnum` varchar(15) NOT NULL,
  `date` date NOT NULL,
  `totalamount` decimal(14,2) NOT NULL,
  `fromloc` int(4) NOT NULL,
  `user_id` bigint(8) NOT NULL,
  `audit` blob NOT NULL,
  PRIMARY KEY (`withdraw_hdr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.withdraw_hdr: 0 rows
/*!40000 ALTER TABLE `withdraw_hdr` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdraw_hdr` ENABLE KEYS */;


-- Dumping structure for table default_db.work_category
CREATE TABLE IF NOT EXISTS `work_category` (
  `work_category_id` bigint(8) NOT NULL AUTO_INCREMENT,
  `level` int(1) NOT NULL,
  `work` varchar(50) NOT NULL,
  `work_subcategory_id` bigint(8) NOT NULL,
  PRIMARY KEY (`work_category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.work_category: 4 rows
/*!40000 ALTER TABLE `work_category` DISABLE KEYS */;
INSERT INTO `work_category` (`work_category_id`, `level`, `work`, `work_subcategory_id`) VALUES
	(1, 1, 'Excavation', 0),
	(2, 2, 'Construction Excavation', 1),
	(3, 1, 'Electrical', 0),
	(4, 1, 'Carpentry', 0);
/*!40000 ALTER TABLE `work_category` ENABLE KEYS */;


-- Dumping structure for table default_db.work_type
CREATE TABLE IF NOT EXISTS `work_type` (
  `work_code_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_code` varchar(100) NOT NULL DEFAULT '0',
  `description` varchar(100) NOT NULL,
  `work_cat_id` int(11) NOT NULL,
  `sub_work_id` int(11) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `wt_price_per_unit` decimal(12,2) NOT NULL,
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`work_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table default_db.work_type: ~0 rows (approximately)
/*!40000 ALTER TABLE `work_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_type` ENABLE KEYS */;


-- Dumping structure for trigger default_db.issuance_to_jo_ad
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `issuance_to_jo_ad` AFTER DELETE ON `issuance_detail` FOR EACH ROW update joborder_detail set joborder_detail_void = '1' where issuance_detail_id = old.issuance_detail_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


-- Dumping structure for trigger default_db.issuance_to_jo_ai
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `issuance_to_jo_ai` AFTER INSERT ON `issuance_detail` FOR EACH ROW insert into joborder_detail (joborder_header_id, stock_id, quantity, cost, amount, ref_no, issuance_detail_id)
		values
		(new.joborder_header_id,new.stock_id, new.quantity, new.price, new.amount, concat('RIS#:',new.issuance_header_id), new.issuance_detail_id )//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
