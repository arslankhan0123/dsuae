-- =====================================================================
-- Migration: 21 Nov 2026
-- Run once in phpMyAdmin on the live database (Import tab, or paste in SQL tab).
-- Safe to run more than once: nothing is dropped, and every statement
-- skips itself when the table / column already exists.
-- Needs MariaDB (the live server and Laragon both use MariaDB).
-- =====================================================================

-- 1) Items: "Style" column used by the item add / edit form
ALTER TABLE `db_items`
  ADD COLUMN IF NOT EXISTS `style_id` INT(11) NULL DEFAULT NULL;

-- 2) Branch settings: "Last Name (Arabic)" - 2nd line of the Arabic company name on PDFs
ALTER TABLE `db_store`
  ADD COLUMN IF NOT EXISTS `store_last_name_arabic` VARCHAR(255) NULL DEFAULT NULL;

-- 3) Opening Cash module (Accounts > Opening Cash)
CREATE TABLE IF NOT EXISTS `db_opening_cash` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `store_id` INT(11) NOT NULL,
  `opening_date` DATE NOT NULL,
  `opening_time` VARCHAR(50) NULL DEFAULT NULL,
  `cash_type` VARCHAR(50) NOT NULL DEFAULT 'Opening Cash',
  `amount` DOUBLE(20,2) NOT NULL DEFAULT 0,
  `created_by` VARCHAR(50) NULL DEFAULT NULL,
  `created_date` DATE NULL DEFAULT NULL,
  `created_time` VARCHAR(50) NULL DEFAULT NULL,
  `updated_by` VARCHAR(50) NULL DEFAULT NULL,
  `updated_date` DATE NULL DEFAULT NULL,
  `updated_time` VARCHAR(50) NULL DEFAULT NULL,
  `system_ip` VARCHAR(100) NULL DEFAULT NULL,
  `status` INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `store_date` (`store_id`, `opening_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 4) Day Book: bank note / coin count, one row per note or coin per branch per date
CREATE TABLE IF NOT EXISTS `bank_note_coin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `store_id` INT(11) NOT NULL,
  `entry_date` DATE NOT NULL,
  `currency` DECIMAL(10,2) NOT NULL,
  `type` VARCHAR(10) NOT NULL,
  `qty` INT(11) NOT NULL DEFAULT 0,
  `amount` DOUBLE(20,2) NOT NULL DEFAULT 0,
  `created_by` VARCHAR(50) NULL DEFAULT NULL,
  `created_date` DATE NULL DEFAULT NULL,
  `created_time` VARCHAR(50) NULL DEFAULT NULL,
  `updated_by` VARCHAR(50) NULL DEFAULT NULL,
  `updated_date` DATE NULL DEFAULT NULL,
  `updated_time` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_date` (`store_id`, `entry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
