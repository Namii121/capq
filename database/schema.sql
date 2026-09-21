-- CarePoint CAPQ - Clean Database Schema
-- Database: carepoint_capq

CREATE DATABASE IF NOT EXISTS `carepoint_capq` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `carepoint_capq`;

-- --------------------------------------------------------
-- Table: users (Staff, Doctor, Admin User Accounts)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('ADMIN', 'DOCTOR', 'RECEPTIONIST', 'KIOSK') DEFAULT 'DOCTOR',
  `department` VARCHAR(100) DEFAULT 'General Medicine',
  `room_number` VARCHAR(10) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: clinic_rooms
-- Stores room metadata, assigned doctor, and active room ticket
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clinic_rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_number` VARCHAR(10) NOT NULL UNIQUE,
  `room_name` VARCHAR(100) NOT NULL,
  `doctor_name` VARCHAR(100) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `current_ticket` VARCHAR(20) DEFAULT NULL,
  `color_theme` VARCHAR(20) DEFAULT 'teal',
  `status` ENUM('AVAILABLE', 'BUSY', 'OFFLINE') DEFAULT 'AVAILABLE',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
  `department` VARCHAR(100) DEFAULT 'General Medicine',
  `room_assigned` VARCHAR(20) DEFAULT NULL,
  `doctor_name` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('WAITING', 'CALLING', 'COMPLETED', 'CANCELLED') DEFAULT 'WAITING',
  `vitals_bp` VARCHAR(20) DEFAULT '120/80',
  `vitals_temp` VARCHAR(10) DEFAULT '36.8',
  `vitals_pulse` VARCHAR(10) DEFAULT '76',
  `vitals_spo2` VARCHAR(10) DEFAULT '99',
  `vitals_weight` VARCHAR(10) DEFAULT '65',
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
-- Seed Default User Accounts
-- --------------------------------------------------------
INSERT INTO `users` (`username`, `password_hash`, `full_name`, `role`, `department`, `room_number`) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeF5a2Ff2A7aXkZl0vJ3K6Xz1Y2Z3W4K.', 'Clinic Operations Admin', 'ADMIN', 'Administration', NULL)
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- --------------------------------------------------------
-- Seed Default Clinic Rooms (Clean State)
-- --------------------------------------------------------
INSERT INTO `clinic_rooms` (`room_number`, `room_name`, `doctor_name`, `department`, `current_ticket`, `color_theme`, `status`) VALUES
('01', 'Triage & Vitals', 'Nurse Station 1', 'Triage', NULL, 'amber', 'AVAILABLE'),
('02', 'Pediatrics Clinic', 'Dr. Patricia Lim, MD', 'Pediatrics', NULL, 'blue', 'AVAILABLE'),
('03', 'General Medicine', 'Dr. Sarah Santos, MD', 'General Medicine', NULL, 'teal', 'AVAILABLE'),
('04', 'Dental & Oral Care', 'Dr. Michael Tan, DMD', 'Dental', NULL, 'purple', 'AVAILABLE'),
('05', 'Pharmacy & Billing', 'Prescription Counter', 'Pharmacy', NULL, 'emerald', 'AVAILABLE')
ON DUPLICATE KEY UPDATE `current_ticket` = NULL, `status` = 'AVAILABLE';
