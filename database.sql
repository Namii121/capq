-- ============================================================================
-- CarePoint Clinic Appointment & Patient Queue System (CAPQ)
-- Database Schema & Initial Seed Data
-- Compatible with MySQL 5.7+ / MySQL 8.0+ / MariaDB (XAMPP Default)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `capq_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `capq_db`;

-- Drop existing tables in reverse dependency order
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `consultations`;
DROP TABLE IF EXISTS `queues`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `patients`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. Table: departments
-- ----------------------------------------------------------------------------
CREATE TABLE `departments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL,
  `room_default` VARCHAR(50) NOT NULL,
  `prefix` VARCHAR(5) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Table: doctors
-- ----------------------------------------------------------------------------
CREATE TABLE `doctors` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `department_id` INT UNSIGNED NOT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `title` VARCHAR(50) NOT NULL DEFAULT 'MD',
  `specialty` VARCHAR(100) NOT NULL,
  `room_number` VARCHAR(50) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_doctors_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Table: patients
-- ----------------------------------------------------------------------------
CREATE TABLE `patients` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `patient_code` VARCHAR(30) NOT NULL UNIQUE,
  `full_name` VARCHAR(120) NOT NULL,
  `phone_number` VARCHAR(30) NOT NULL,
  `email` VARCHAR(120) NULL,
  `birth_date` DATE NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `patient_type` VARCHAR(50) DEFAULT 'Returning',
  `is_priority` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=Senior/PWD/Pregnant',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Table: appointments
-- ----------------------------------------------------------------------------
CREATE TABLE `appointments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reference_code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'e.g. CP-8921',
  `patient_id` INT UNSIGNED NOT NULL,
  `doctor_id` INT UNSIGNED NOT NULL,
  `department_id` INT UNSIGNED NOT NULL,
  `appointment_date` DATE NOT NULL,
  `slot_time` VARCHAR(20) NOT NULL,
  `chief_complaint` TEXT NULL,
  `is_priority` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('SCHEDULED', 'CHECKED_IN', 'IN_CONSULTATION', 'COMPLETED', 'CANCELLED', 'NO_SHOW') NOT NULL DEFAULT 'SCHEDULED',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Table: queues
-- ----------------------------------------------------------------------------
CREATE TABLE `queues` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ticket_number` VARCHAR(20) NOT NULL COMMENT 'e.g. A-102, P-012',
  `appointment_id` INT UNSIGNED NULL,
  `patient_id` INT UNSIGNED NOT NULL,
  `doctor_id` INT UNSIGNED NULL,
  `department_id` INT UNSIGNED NOT NULL,
  `room_number` VARCHAR(50) NOT NULL,
  `queue_type` ENUM('Appointment', 'Walk-in') NOT NULL DEFAULT 'Walk-in',
  `status` ENUM('WAITING', 'CALLED', 'IN_CONSULTATION', 'COMPLETED', 'SKIPPED') NOT NULL DEFAULT 'WAITING',
  `is_priority` TINYINT(1) NOT NULL DEFAULT 0,
  `check_in_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `called_time` TIMESTAMP NULL,
  `completed_time` TIMESTAMP NULL,
  CONSTRAINT `fk_queue_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_queue_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_queue_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_queue_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Table: consultations (Clinical Records & EHR Notes)
-- ----------------------------------------------------------------------------
CREATE TABLE `consultations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `queue_id` INT UNSIGNED NOT NULL,
  `patient_id` INT UNSIGNED NOT NULL,
  `doctor_id` INT UNSIGNED NOT NULL,
  `bp` VARCHAR(20) NULL DEFAULT '120/80',
  `temp` VARCHAR(10) NULL DEFAULT '36.8',
  `pulse` VARCHAR(10) NULL DEFAULT '76',
  `spo2` VARCHAR(10) NULL DEFAULT '99',
  `weight` VARCHAR(10) NULL DEFAULT '65',
  `diagnosis` VARCHAR(255) NULL,
  `prescription` TEXT NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_consult_queue` FOREIGN KEY (`queue_id`) REFERENCES `queues` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_consult_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_consult_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INITIAL SEED DATA
-- ============================================================================

-- Insert Departments
INSERT INTO `departments` (`id`, `code`, `name`, `description`, `room_default`, `prefix`) VALUES
(1, 'GEN_MED', 'General Medicine', 'Adult & Family Outpatient Consultation', 'Room 03', 'A'),
(2, 'PEDIA', 'Pediatrics', 'Infant, Child & Adolescent Healthcare', 'Room 02', 'P'),
(3, 'DENTAL', 'Dental Clinic', 'Comprehensive Oral & Dental Health', 'Room 04', 'D'),
(4, 'TRIAGE', 'Triage & Vital Signs', 'Patient Intake, Vitals & Screening', 'Room 01', 'T'),
(5, 'PHARMACY', 'Pharmacy & Billing', 'Prescription Dispensing & Cashier', 'Room 05', 'B');

-- Insert Doctors
INSERT INTO `doctors` (`id`, `department_id`, `full_name`, `title`, `specialty`, `room_number`, `is_active`) VALUES
(1, 1, 'Dr. Sarah Santos', 'MD', 'Internal & General Medicine', 'Room 03', 1),
(2, 1, 'Dr. Raymond Cruz', 'MD', 'Family Medicine', 'Room 01', 1),
(3, 2, 'Dr. Patricia Lim', 'MD', 'Pediatrics & Neonatology', 'Room 02', 1),
(4, 3, 'Dr. Michael Tan', 'DMD', 'Dental Specialist', 'Room 04', 1);

-- Insert Sample Patients
INSERT INTO `patients` (`id`, `patient_code`, `full_name`, `phone_number`, `email`, `birth_date`, `gender`, `patient_type`, `is_priority`) VALUES
(1, 'PT-10492', 'Juan Carlos Dela Cruz', '0917-555-0192', 'juan.carlos@example.com', '1988-04-12', 'Male', 'Returning', 0),
(2, 'PT-10503', 'Beatriz Mendoza', '0922-334-1188', 'b.mendoza@example.com', '1997-08-25', 'Female', 'Returning', 0),
(3, 'PT-09821', 'Rosa Flores', '0918-771-2299', 'rosa.flores@example.com', '1958-11-03', 'Female', 'Returning', 1),
(4, 'PT-10512', 'Eduardo Rivera', '0919-445-8821', 'eduardo.r@example.com', '1981-01-19', 'Male', 'Returning', 0),
(5, 'PT-10525', 'Gabriel Ramos', '0920-551-7733', 'gabriel.r@example.com', '1992-06-14', 'Male', 'New Patient', 0),
(6, 'PT-10531', 'Cristina Ocampo', '0917-112-9900', 'cristina.o@example.com', '2004-09-02', 'Female', 'New Patient', 0);

-- Insert Sample Appointments
INSERT INTO `appointments` (`id`, `reference_code`, `patient_id`, `doctor_id`, `department_id`, `appointment_date`, `slot_time`, `chief_complaint`, `is_priority`, `status`) VALUES
(1, 'CP-8921', 1, 1, 1, CURRENT_DATE(), '10:15 AM', 'Persistent dry cough for 4 days, mild fatigue', 0, 'IN_CONSULTATION'),
(2, 'CP-9014', 2, 1, 1, CURRENT_DATE(), '10:30 AM', 'Follow-up for complete blood count results', 0, 'CHECKED_IN'),
(3, 'CP-9022', 5, 1, 1, CURRENT_DATE(), '11:00 AM', 'Executive check-up lab review', 0, 'SCHEDULED'),
(4, 'CP-9035', 6, 1, 1, CURRENT_DATE(), '11:15 AM', 'Allergy evaluation on forearms', 0, 'SCHEDULED');

-- Insert Sample Queue Entries
INSERT INTO `queues` (`id`, `ticket_number`, `appointment_id`, `patient_id`, `doctor_id`, `department_id`, `room_number`, `queue_type`, `status`, `is_priority`, `check_in_time`, `called_time`) VALUES
(1, 'A-102', 1, 1, 1, 1, 'Room 03', 'Appointment', 'IN_CONSULTATION', 0, NOW() - INTERVAL 25 MINUTE, NOW() - INTERVAL 12 MINUTE),
(2, 'A-103', 2, 2, 1, 1, 'Room 03', 'Appointment', 'WAITING', 0, NOW() - INTERVAL 15 MINUTE, NULL),
(3, 'P-012', NULL, 3, 1, 1, 'Room 03', 'Walk-in', 'WAITING', 1, NOW() - INTERVAL 12 MINUTE, NULL),
(4, 'A-104', NULL, 4, 1, 1, 'Room 03', 'Walk-in', 'WAITING', 0, NOW() - INTERVAL 8 MINUTE, NULL),
(5, 'A-105', 3, 5, 1, 1, 'Room 03', 'Appointment', 'WAITING', 0, NOW() - INTERVAL 5 MINUTE, NULL);

-- Insert Sample Consultation
INSERT INTO `consultations` (`id`, `queue_id`, `patient_id`, `doctor_id`, `bp`, `temp`, `pulse`, `spo2`, `weight`, `diagnosis`, `prescription`, `notes`) VALUES
(1, 1, 1, 1, '120/80', '36.8', '76', '99', '68', 'J06.9 - Acute Upper Respiratory Infection (Mild)', 'Cetirizine 10mg once daily for 5 days. Paracetamol 500mg PRN for sore throat.', 'Patient instructed to increase fluid intake and rest.');
