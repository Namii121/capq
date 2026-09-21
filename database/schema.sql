-- CarePoint CAPQ - Solo Clinic Queue & Patient Portal Schema
-- Database: carepoint_capq

CREATE DATABASE IF NOT EXISTS `carepoint_capq` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `carepoint_capq`;

-- --------------------------------------------------------
-- Table: users (Single Doctor = Admin Account)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('ADMIN', 'DOCTOR') DEFAULT 'ADMIN',
  `department` VARCHAR(100) DEFAULT 'General Consultation',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: queue_tickets
-- Primary Queue & Patient Medical Record Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `queue_tickets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ticket_number` VARCHAR(20) NOT NULL UNIQUE,
  `patient_id` VARCHAR(30) NOT NULL,
  `patient_name` VARCHAR(150) NOT NULL,
  `age` INT DEFAULT 35,
  `gender` VARCHAR(20) DEFAULT 'Female',
  `phone` VARCHAR(30) DEFAULT NULL,
  `type` ENUM('Appointment', 'Walk-in') DEFAULT 'Walk-in',
  `time_slot` VARCHAR(30) DEFAULT NULL,
  `department` VARCHAR(100) DEFAULT 'General Consultation',
  `room_assigned` VARCHAR(20) DEFAULT 'Consultation Room',
  `doctor_name` VARCHAR(100) DEFAULT 'Dr. Sarah Santos, MD',
  `status` ENUM('WAITING', 'PREPARING', 'CALLING', 'COMPLETED', 'CANCELLED') DEFAULT 'WAITING',
  `complaint` TEXT DEFAULT NULL,
  `diagnosis` TEXT DEFAULT NULL,
  `prescription` TEXT DEFAULT NULL,
  `is_priority` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `called_at` DATETIME DEFAULT NULL,
  `completed_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: system_logs
-- Audit log for system events & calls
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `event_type` VARCHAR(50) NOT NULL,
  `ticket_number` VARCHAR(20) DEFAULT NULL,
  `details` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed Default Solo Doctor & Admin Account
-- --------------------------------------------------------
INSERT INTO `users` (`username`, `password_hash`, `full_name`, `role`, `department`) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeF5a2Ff2A7aXkZl0vJ3K6Xz1Y2Z3W4K.', 'Dr. Sarah Santos, MD', 'ADMIN', 'General Consultation'),
('dr_santos', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeF5a2Ff2A7aXkZl0vJ3K6Xz1Y2Z3W4K.', 'Dr. Sarah Santos, MD', 'DOCTOR', 'General Consultation')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);
