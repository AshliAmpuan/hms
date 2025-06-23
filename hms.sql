-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping database structure for hms_db
CREATE DATABASE IF NOT EXISTS `hms_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `hms_db`;

-- Dumping structure for table hms_db.cashier
CREATE TABLE IF NOT EXISTS `cashier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinic_id` int DEFAULT NULL,
  `fullname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `contact_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clinic` (`id`, `clinic_name`) VALUES
  (1, 'Shepherd Animal Clinic Paco Manila');

-- Dumping structure for table hms_db.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clinic_id` int NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.clinic
CREATE TABLE IF NOT EXISTS `clinic` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `clinic_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for clinic
INSERT INTO `clinic` (`id`, `clinic_name`) VALUES
  (1, 'Shepherd Animal Clinic Paco Manila');

-- Dumping structure for table hms_db.doctor
CREATE TABLE IF NOT EXISTS `doctor` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `clinic_id` int DEFAULT NULL,
  `fullname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.doctor_fee
CREATE TABLE IF NOT EXISTS `doctor_fee` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` int NOT NULL DEFAULT '0',
  `reservation_id` int NOT NULL DEFAULT '0',
  `amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `tdate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.doctor_laboratory
CREATE TABLE IF NOT EXISTS `doctor_laboratory` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` int NOT NULL DEFAULT '0',
  `laboratory_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.doctor_leave
CREATE TABLE IF NOT EXISTS `doctor_leave` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` int unsigned NOT NULL DEFAULT '0',
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tdate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.laboratory
CREATE TABLE IF NOT EXISTS `laboratory` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `clinic_id` int DEFAULT NULL,
  `category_id` int NOT NULL,
  `laboratory_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `capacity_per_day` int DEFAULT NULL,
  `active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.patient
CREATE TABLE IF NOT EXISTS `patient` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.reservation
CREATE TABLE IF NOT EXISTS `reservation` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `clinic_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `laboratory_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `tdate` date NOT NULL,
  `approve_by` int DEFAULT NULL,
  `time` time DEFAULT NULL,
  `results` text COLLATE utf8mb4_unicode_ci,
  `status` int NOT NULL DEFAULT '0',
  `add_to_checkout` int NOT NULL DEFAULT '0',
  `mop` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `reservation` ADD COLUMN `pet_id` int unsigned DEFAULT NULL AFTER `patient_id`;


-- Dumping structure for table hms_db.reservation_results
CREATE TABLE IF NOT EXISTS `reservation_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reservation_id` int NOT NULL DEFAULT '0',
  `file` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tdate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.transaction
CREATE TABLE IF NOT EXISTS `transaction` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `tdate` date NOT NULL,
  `cashier_id` int NOT NULL,
  `mop` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `status` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table hms_db.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` int NOT NULL DEFAULT '0',
  `active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hms_db.users: ~4 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password`, `role`, `active`) VALUES
  (1, 'admin', '5607ccf4b59cebf46482e1dc2194d96b', 1, 1);


/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

-- Create item_category table
CREATE TABLE IF NOT EXISTS item_category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL
);


-- Create inventory table
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    details TEXT,
    price DECIMAL(10, 2) NOT NULL,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES item_category(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Insert sample categories into item_category
INSERT INTO item_category (category_name) VALUES 
('Electronics'),
('Furniture'),
('Clothing'),
('Food'),
('Toys');

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_items` int(11) NOT NULL DEFAULT 0,
  `order_status` enum('pending','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','partial','refunded') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_order_status` (`order_status`),
  KEY `idx_order_date` (`order_date`),
  KEY `idx_order_number` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Order Items table - stores individual items in each order
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `item_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_inventory_id` (`inventory_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX idx_orders_patient_date ON orders(patient_id, order_date);
CREATE INDEX idx_order_items_order_inventory ON order_items(order_id, inventory_id);
CREATE INDEX idx_cart_patient_date ON shopping_cart(patient_id, added_date);

-- Add some sample data (optional)
-- INSERT INTO orders (patient_id, order_number, total_amount, total_items, order_status) 
-- VALUES (1, 'ORD-2025-001', 45.99, 3, 'completed');

-- Useful Views for reporting
CREATE VIEW v_order_summary AS
SELECT 
    o.id,
    o.order_number,
    o.patient_id,
    o.total_amount,
    o.total_items,
    o.order_status,
    o.payment_status,
    o.order_date,
    COUNT(oi.id) as item_count,
    SUM(oi.quantity) as total_quantity
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

CREATE VIEW v_cart_details AS
SELECT 
    sc.id as cart_id,
    sc.patient_id,
    sc.quantity as cart_quantity,
    sc.added_date,
    i.id as inventory_id,
    i.item_name,
    i.price,
    i.quantity as available_stock,
    ic.category_name,
    i.details,
    (sc.quantity * i.price) as line_total
FROM shopping_cart sc
JOIN inventory i ON sc.inventory_id = i.id
JOIN item_category ic ON i.category_id = ic.id
WHERE i.active = 1;

CREATE TABLE saved_carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    cart_data JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better query performance
    INDEX idx_patient_id (patient_id),
    INDEX idx_created_at (created_at),
    
    -- Ensure one saved cart per patient (supports your ON DUPLICATE KEY UPDATE logic)
    UNIQUE KEY unique_patient_cart (patient_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `orders` CHANGE `payment_method` `mop` VARCHAR(50) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `pet` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `patient_id` int unsigned NOT NULL,
    `pet_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `age` int DEFAULT NULL,
    `species` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `breed` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `weight` decimal(5,2) DEFAULT NULL,
    `sex` enum('M','F','U') COLLATE utf8mb4_unicode_ci DEFAULT 'U',
    `birth_date` date DEFAULT NULL,
    `active` int NOT NULL DEFAULT '1',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `patient_id` (`patient_id`),
    CONSTRAINT `pet_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 
    p.pet_name,
    p.species,
    p.breed,
    p.age,
    p.weight,
    p.sex,
    CONCAT(pt.firstname, ' ', pt.lastname) as owner_name,
    pt.email,
    pt.contact_number
FROM pet p
JOIN patient pt ON p.patient_id = pt.id
WHERE p.active = 1 AND pt.active = 1;

-- Get pets by owner
SELECT * FROM pet WHERE patient_id = 1;

-- Count pets by species
SELECT species, COUNT(*) as pet_count FROM pet GROUP BY species;

 CREATE TABLE IF NOT EXISTS `messages` (
       `id` int unsigned NOT NULL AUTO_INCREMENT,
       `sender_id` int NOT NULL,
       `doctor_id` int NOT NULL,
       `subject` varchar(255) NOT NULL,
       `message` text NOT NULL,
       `priority` enum('normal','high','urgent') DEFAULT 'normal',
       `is_read` tinyint(1) DEFAULT 0,
       `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
       PRIMARY KEY (`id`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
CREATE TABLE `vaccination_record` (
  `id` int(10) UNSIGNED NOT NULL,
  `pet_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL,
  `reservation_date` date DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `vaccination_notes` text DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `temperature_celsius` decimal(4,1) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vaccination_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(20) NOT NULL,
    pet_id VARCHAR(20) NOT NULL,
    doctor_id VARCHAR(20) NOT NULL,
    vaccination_date DATE NOT NULL,
    vaccine_type VARCHAR(100) NOT NULL,
    weight_lbs DECIMAL(5,2),
    temperature_c DECIMAL(4,1),
    medical_history_notes TEXT,
    doctor_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better query performance
    INDEX idx_patient_id (patient_id),
    INDEX idx_pet_id (pet_id),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_vaccination_date (vaccination_date)
);