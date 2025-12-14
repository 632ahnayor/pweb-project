-- MangroveTour Database Schema
-- Created for Mangrove Wonorejo Ecotourism Management System

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS mangrove_wonorejo;
USE mangrove_wonorejo;

-- Table: User (Admin & Operator accounts)
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('Admin', 'Operator') NOT NULL DEFAULT 'Operator',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Pengunjung (Visitor)
CREATE TABLE IF NOT EXISTS `pengunjung` (
  `id_pengunjung` INT PRIMARY KEY AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `username` VARCHAR(50) UNIQUE,
  `password` VARCHAR(255),
  `is_active` TINYINT(1) DEFAULT 1,
  `terakhir_login` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Tiket (Tickets)
CREATE TABLE IF NOT EXISTS `tiket` (
  `id_tiket` INT PRIMARY KEY AUTO_INCREMENT,
  `id_pengunjung` INT NOT NULL,
  `tanggal_berkunjung` DATE NOT NULL,
  `status` ENUM('Active', 'Used', 'Expired') NOT NULL DEFAULT 'Active',
  `harga` DECIMAL(10, 2) NOT NULL DEFAULT 50000.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_pengunjung`) REFERENCES `pengunjung`(`id_pengunjung`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Review
CREATE TABLE IF NOT EXISTS `review` (
  `id_review` INT PRIMARY KEY AUTO_INCREMENT,
  `id_pengunjung` INT NOT NULL,
  `rating` INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `komentar` TEXT NOT NULL,
  `tanggal` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_pengunjung`) REFERENCES `pengunjung`(`id_pengunjung`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Transactions (Midtrans Payment Integration)
CREATE TABLE IF NOT EXISTS `transactions` (
  `id_transaction` INT PRIMARY KEY AUTO_INCREMENT,
  `id_tiket` INT,
  `order_id` VARCHAR(100) UNIQUE NOT NULL,
  `gross_amount` INT NOT NULL,
  `payment_type` VARCHAR(50),
  `transaction_status` VARCHAR(50) NOT NULL,
  `transaction_time` DATETIME,
  `settlement_time` DATETIME,
  `fraud_status` VARCHAR(50),
  `response_code` VARCHAR(10),
  `status_message` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_tiket`) REFERENCES `tiket`(`id_tiket`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for better query performance
CREATE INDEX idx_pengunjung_email ON pengunjung(email);
CREATE INDEX idx_pengunjung_username ON pengunjung(username);
CREATE INDEX idx_pengunjung_active ON pengunjung(is_active);
CREATE INDEX idx_tiket_pengunjung ON tiket(id_pengunjung);
CREATE INDEX idx_tiket_status ON tiket(status);
CREATE INDEX idx_tiket_tanggal ON tiket(tanggal_berkunjung);
CREATE INDEX idx_review_pengunjung ON review(id_pengunjung);
CREATE INDEX idx_review_rating ON review(rating);
CREATE INDEX idx_user_username ON user(username);
CREATE INDEX idx_transaction_order_id ON transactions(order_id);
CREATE INDEX idx_transaction_status ON transactions(transaction_status);
CREATE INDEX idx_transaction_created ON transactions(created_at);

-- Insert sample Admin user (password: 'admin123')
-- Hash generated with: password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10])
INSERT IGNORE INTO `user` (`username`, `password`, `role`) VALUES 
('admin', '$2y$10$WXbgZWo0Qjw3wJLwV8LDWOvZZCh/xL5uJqUc5YgNYwWwJqZnC8sBu', 'Admin');

-- Insert sample Operator user (password: 'operator123')
-- Hash generated with: password_hash('operator123', PASSWORD_BCRYPT, ['cost' => 10])
INSERT IGNORE INTO `user` (`username`, `password`, `role`) VALUES 
('operator', '$2y$10$rXZfNTgZjgJV3mC9xK5OZelRhvZ7wW8aJ2L4uM9bN7oV5pQ2rS6Vu', 'Operator');
