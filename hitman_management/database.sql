-- ================================================================
-- Hitman Management System - Database
-- Import this file into phpMyAdmin once before using the app.
-- ================================================================

CREATE DATABASE IF NOT EXISTS hitman_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hitman_db;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS handlers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    target_name VARCHAR(200) NOT NULL,
    location VARCHAR(100) NOT NULL,
    bounty INT NOT NULL DEFAULT 0,
    fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    handler_id INT NULL,
    FOREIGN KEY (handler_id) REFERENCES handlers(id) ON DELETE SET NULL
) ENGINE=InnoDB;
