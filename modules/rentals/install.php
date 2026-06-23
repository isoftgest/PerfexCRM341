<?php

defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();
$CI->load->dbforge();

function rentals_create_table($table, $sql)
{
    $CI = &get_instance();
    if (!$CI->db->table_exists(db_prefix() . $table)) {
        $CI->db->query($sql);
    }
}

$charset = $CI->db->char_set ?: 'utf8';
$collation = $CI->db->dbcollat ?: 'utf8_general_ci';
$engine = 'ENGINE=InnoDB DEFAULT CHARSET=' . $charset . ' COLLATE=' . $collation;

rentals_create_table('rentals_license', "CREATE TABLE `" . db_prefix() . "rentals_license` (`id` INT NOT NULL AUTO_INCREMENT,`license_key` VARCHAR(191) NOT NULL,`installation_uuid` VARCHAR(191) NOT NULL,`domain` VARCHAR(191) NULL,`server_fingerprint` VARCHAR(255) NOT NULL,`license_status` VARCHAR(50) NOT NULL DEFAULT 'inactive',`license_payload` LONGTEXT NULL,`license_signature` LONGTEXT NULL,`last_check` DATETIME NULL,`activated_at` DATETIME NULL,`expires_at` DATETIME NULL,`created_at` DATETIME NULL,`updated_at` DATETIME NULL,PRIMARY KEY (`id`), KEY `license_status` (`license_status`)) $engine;");
rentals_create_table('rentals_license_logs', "CREATE TABLE `" . db_prefix() . "rentals_license_logs` (`id` INT NOT NULL AUTO_INCREMENT,`event` VARCHAR(100) NOT NULL,`license_key` VARCHAR(191) NULL,`message` TEXT NULL,`ip_address` VARCHAR(100) NULL,`created_at` DATETIME NULL,`created_by` INT NULL,PRIMARY KEY (`id`), KEY `event` (`event`)) $engine;");
rentals_create_table('rental_properties', "CREATE TABLE `" . db_prefix() . "rental_properties` (`id` INT NOT NULL AUTO_INCREMENT,`name` VARCHAR(191) NOT NULL,`reference` VARCHAR(100) NULL,`address` TEXT NULL,`city` VARCHAR(100) NULL,`province` VARCHAR(100) NULL,`postal_code` VARCHAR(20) NULL,`country` VARCHAR(100) NULL,`owner_name` VARCHAR(191) NULL,`owner_phone` VARCHAR(100) NULL,`owner_email` VARCHAR(191) NULL,`notes` TEXT NULL,`active` TINYINT(1) DEFAULT 1,`created_at` DATETIME NULL,`updated_at` DATETIME NULL,`created_by` INT NULL,`updated_by` INT NULL,PRIMARY KEY (`id`), KEY `active` (`active`)) $engine;");
rentals_create_table('rental_units', "CREATE TABLE `" . db_prefix() . "rental_units` (`id` INT NOT NULL AUTO_INCREMENT,`property_id` INT NOT NULL,`type` VARCHAR(30) NOT NULL,`name` VARCHAR(191) NOT NULL,`reference` VARCHAR(100) NULL,`description` TEXT NULL,`default_price` DECIMAL(15,2) DEFAULT 0.00,`active` TINYINT(1) DEFAULT 1,`created_at` DATETIME NULL,`updated_at` DATETIME NULL,`created_by` INT NULL,`updated_by` INT NULL,PRIMARY KEY (`id`), KEY `property_id` (`property_id`), KEY `type` (`type`)) $engine;");
rentals_create_table('rentals', "CREATE TABLE `" . db_prefix() . "rentals` (`id` INT NOT NULL AUTO_INCREMENT,`contract_reference` VARCHAR(100) NULL,`clientid` INT NOT NULL,`property_id` INT NOT NULL,`unit_id` INT NOT NULL,`start_date` DATE NOT NULL,`end_date` DATE NULL,`monthly_price` DECIMAL(15,2) NOT NULL,`status` VARCHAR(30) NOT NULL DEFAULT 'active',`notes` TEXT NULL,`created_at` DATETIME NULL,`updated_at` DATETIME NULL,`created_by` INT NULL,`updated_by` INT NULL,PRIMARY KEY (`id`), KEY `clientid` (`clientid`), KEY `unit_dates` (`unit_id`,`start_date`,`end_date`), KEY `status` (`status`)) $engine;");
rentals_create_table('rental_price_history', "CREATE TABLE `" . db_prefix() . "rental_price_history` (`id` INT NOT NULL AUTO_INCREMENT,`rental_id` INT NOT NULL,`old_price` DECIMAL(15,2) NULL,`new_price` DECIMAL(15,2) NOT NULL,`change_date` DATE NOT NULL,`reason` TEXT NULL,`created_at` DATETIME NULL,`created_by` INT NULL,PRIMARY KEY (`id`), KEY `rental_id` (`rental_id`)) $engine;");
rentals_create_table('rental_payments', "CREATE TABLE `" . db_prefix() . "rental_payments` (`id` INT NOT NULL AUTO_INCREMENT,`rental_id` INT NOT NULL,`clientid` INT NOT NULL,`payment_month` VARCHAR(7) NOT NULL,`due_date` DATE NOT NULL,`amount` DECIMAL(15,2) NOT NULL,`amount_paid` DECIMAL(15,2) DEFAULT 0.00,`status` VARCHAR(30) NOT NULL DEFAULT 'pending',`payment_date` DATE NULL,`payment_method` VARCHAR(100) NULL,`reference` VARCHAR(191) NULL,`notes` TEXT NULL,`created_at` DATETIME NULL,`updated_at` DATETIME NULL,`created_by` INT NULL,`updated_by` INT NULL,PRIMARY KEY (`id`), UNIQUE KEY `rental_month` (`rental_id`,`payment_month`), KEY `status` (`status`)) $engine;");
rentals_create_table('rental_deposits', "CREATE TABLE `" . db_prefix() . "rental_deposits` (`id` INT NOT NULL AUTO_INCREMENT,`rental_id` INT NOT NULL,`clientid` INT NOT NULL,`amount` DECIMAL(15,2) NOT NULL,`deposit_date` DATE NOT NULL,`status` VARCHAR(30) NOT NULL DEFAULT 'held',`returned_amount` DECIMAL(15,2) DEFAULT 0.00,`returned_date` DATE NULL,`notes` TEXT NULL,`created_at` DATETIME NULL,`updated_at` DATETIME NULL,`created_by` INT NULL,`updated_by` INT NULL,PRIMARY KEY (`id`), KEY `rental_id` (`rental_id`), KEY `status` (`status`)) $engine;");
rentals_create_table('rental_expenses', "CREATE TABLE `" . db_prefix() . "rental_expenses` (`id` INT NOT NULL AUTO_INCREMENT,`property_id` INT NOT NULL,`expense_date` DATE NOT NULL,`concept` VARCHAR(191) NOT NULL,`amount` DECIMAL(15,2) NOT NULL,`supplier` VARCHAR(191) NULL,`reference` VARCHAR(191) NULL,`notes` TEXT NULL,`created_at` DATETIME NULL,`updated_at` DATETIME NULL,`created_by` INT NULL,`updated_by` INT NULL,PRIMARY KEY (`id`), KEY `property_id` (`property_id`), KEY `expense_date` (`expense_date`)) $engine;");

if (function_exists('add_option')) {
    add_option('rentals_license_endpoint', 'https://licencias.tudominio.com/api/validate');
    add_option('rentals_default_due_day', '1');
}
