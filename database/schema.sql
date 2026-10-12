-- Computer Laboratory Management System - Database Schema
-- MySQL 8+ & SQLite compatible

CREATE DATABASE IF NOT EXISTS `lab-management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lab-management`;

-- Roles
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Users
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL,
    `avatar` VARCHAR(255),
    `department_segment` VARCHAR(100) NULL,
    `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Students
CREATE TABLE `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_number` VARCHAR(50) NOT NULL UNIQUE,
    `full_name` VARCHAR(255) NOT NULL,
    `course` VARCHAR(100),
    `year_level` VARCHAR(20),
    `section` VARCHAR(50),
    `email` VARCHAR(255),
    `password` VARCHAR(255),
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `or_number` VARCHAR(100) NULL,
    `semester` VARCHAR(50) DEFAULT 'First Semester',
    `or_date` DATE NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_student_number` (`student_number`),
    INDEX `idx_course` (`course`),
    INDEX `idx_year_level` (`year_level`),
    INDEX `idx_or_number` (`or_number`)
) ENGINE=InnoDB;

-- Laboratories
CREATE TABLE `laboratories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `room_number` VARCHAR(50) NOT NULL,
    `building` VARCHAR(255),
    `capacity` INT DEFAULT 0,
    `description` TEXT,
    `qr_code` VARCHAR(255),
    `status` ENUM('active', 'archived') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_room_number` (`room_number`)
) ENGINE=InnoDB;

-- Equipment Categories
CREATE TABLE `equipment_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Equipment
CREATE TABLE `equipment` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `asset_tag` VARCHAR(100) NOT NULL UNIQUE,
    `property_code` VARCHAR(100) NULL,
    `name` VARCHAR(255) NOT NULL,
    `category_id` INT NOT NULL,
    `classification` VARCHAR(100) DEFAULT 'ICT Equipment',
    `brand` VARCHAR(255),
    `model` VARCHAR(255),
    `serial_number` VARCHAR(255),
    `laboratory_id` INT NOT NULL,
    `acquisition_date` DATE,
    `purchase_cost` DECIMAL(12, 2) DEFAULT 0,
    `status` ENUM('active', 'missing', 'defective', 'under_repair', 'borrowed', 'retired') DEFAULT 'active',
    `service_status` VARCHAR(50) DEFAULT 'operational',
    `notes` TEXT,
    `qr_code` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `equipment_categories`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`laboratory_id`) REFERENCES `laboratories`(`id`) ON DELETE CASCADE,
    INDEX `idx_asset_tag` (`asset_tag`),
    INDEX `idx_property_code` (`property_code`),
    INDEX `idx_equipment_status` (`status`),
    INDEX `idx_serial_number` (`serial_number`)
) ENGINE=InnoDB;

-- Attendance
CREATE TABLE `attendance` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `laboratory_id` INT NOT NULL,
    `attendance_date` DATE NOT NULL,
    `time_in` TIME NOT NULL,
    `time_out` TIME NULL,
    `ip_address` VARCHAR(45),
    `browser` TEXT,
    `session_id` VARCHAR(255),
    `status` ENUM('present', 'late', 'absent') DEFAULT 'present',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`laboratory_id`) REFERENCES `laboratories`(`id`) ON DELETE CASCADE,
    INDEX `idx_attendance_date` (`attendance_date`),
    INDEX `idx_student_lab_date` (`student_id`, `laboratory_id`, `attendance_date`)
) ENGINE=InnoDB;

-- Audits
CREATE TABLE `audits` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `laboratory_id` INT NOT NULL,
    `conducted_by` INT NOT NULL,
    `audit_date` DATE NOT NULL,
    `status` ENUM('in_progress', 'completed', 'cancelled') DEFAULT 'in_progress',
    `completed_at` TIMESTAMP NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`laboratory_id`) REFERENCES `laboratories`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`conducted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_audit_date` (`audit_date`),
    INDEX `idx_audit_status` (`status`)
) ENGINE=InnoDB;

-- Audit Items
CREATE TABLE `audit_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `audit_id` INT NOT NULL,
    `equipment_id` INT NOT NULL,
    `status` ENUM('present', 'missing', 'defective') DEFAULT 'missing',
    `scanned_at` TIMESTAMP NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`audit_id`) REFERENCES `audits`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`) ON DELETE CASCADE,
    INDEX `idx_audit_item_status` (`status`),
    UNIQUE KEY `unique_audit_equipment` (`audit_id`, `equipment_id`)
) ENGINE=InnoDB;

-- Investigations / Search Tracking
CREATE TABLE `investigations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `equipment_id` INT NOT NULL,
    `audit_id` INT,
    `initiated_by` INT NOT NULL,
    `status` ENUM('open', 'under_review', 'resolved', 'closed') DEFAULT 'open',
    `findings` TEXT,
    `recommendations` TEXT,
    `resolved_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`audit_id`) REFERENCES `audits`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`initiated_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_investigation_status` (`status`)
) ENGINE=InnoDB;

-- Borrowings
CREATE TABLE `borrowings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `borrower_id` INT NOT NULL,
    `equipment_id` INT NOT NULL,
    `date_borrowed` DATE NOT NULL,
    `expected_return` DATE NOT NULL,
    `actual_return` DATE,
    `status` ENUM('pending', 'approved', 'borrowed', 'returned', 'rejected', 'overdue') DEFAULT 'pending',
    `approved_by` INT,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`borrower_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_borrowing_status` (`status`),
    INDEX `idx_borrowing_date` (`date_borrowed`)
) ENGINE=InnoDB;

-- Activity Logs
CREATE TABLE `activity_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_activity_user` (`user_id`),
    INDEX `idx_activity_action` (`action`),
    INDEX `idx_activity_created` (`created_at`)
) ENGINE=InnoDB;

-- Settings
CREATE TABLE `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Equipment Services (Maintenance & Repair Records)
CREATE TABLE `equipment_services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `equipment_id` INT NOT NULL,
    `service_type` VARCHAR(50) NOT NULL,
    `service_date` DATE NOT NULL,
    `technician_name` VARCHAR(255),
    `company_or_dept` VARCHAR(255),
    `cost` DECIMAL(10, 2) DEFAULT 0,
    `status` VARCHAR(50) DEFAULT 'completed',
    `notes` TEXT,
    `next_service_date` DATE NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`) ON DELETE CASCADE,
    INDEX `idx_equipment_service` (`equipment_id`),
    INDEX `idx_service_date` (`service_date`)
) ENGINE=InnoDB;

-- Equipment Dispatches
CREATE TABLE `equipment_dispatches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `equipment_id` INT NOT NULL,
    `from_lab_id` INT,
    `to_lab_id` INT,
    `dispatched_to_user_id` INT,
    `segment` VARCHAR(100),
    `dispatch_date` DATE NOT NULL,
    `return_due_date` DATE,
    `actual_return_date` DATE,
    `purpose` TEXT,
    `status` VARCHAR(30) DEFAULT 'dispatched',
    `notes` TEXT,
    `dispatched_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Segment Requests (Long NR identifier)
CREATE TABLE `segment_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_number` VARCHAR(100) NOT NULL UNIQUE,
    `requester_name` VARCHAR(255) NOT NULL,
    `requester_email` VARCHAR(255) NOT NULL,
    `laboratory_id` INT NOT NULL,
    `segment_name` VARCHAR(100) NOT NULL,
    `purpose` TEXT NOT NULL,
    `request_date` DATE NOT NULL,
    `start_time` VARCHAR(20),
    `end_time` VARCHAR(20),
    `expected_users` INT DEFAULT 1,
    `equipment_needed` TEXT,
    `status` VARCHAR(30) DEFAULT 'pending',
    `admin_notes` TEXT,
    `reviewed_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`laboratory_id`) REFERENCES `laboratories`(`id`) ON DELETE CASCADE,
    INDEX `idx_seg_req_number` (`request_number`)
) ENGINE=InnoDB;

-- Account Requests (Capability for account requests)
CREATE TABLE `account_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_number` VARCHAR(100) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `requested_role_id` INT DEFAULT 4,
    `department_segment` VARCHAR(100),
    `id_number` VARCHAR(100),
    `reason` TEXT,
    `status` VARCHAR(30) DEFAULT 'pending',
    `reviewed_by` INT,
    `admin_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_acc_req_number` (`request_number`)
) ENGINE=InnoDB;

-- Insert default roles
INSERT INTO `roles` (`name`, `description`) VALUES
('Super Administrator', 'Full system access'),
('Laboratory Administrator', 'Manage laboratories and equipment'),
('Laboratory Custodian', 'Perform audits and manage inventory'),
('Faculty', 'Manage laboratory sessions, requests, attendance, and equipment'),
('Student', 'Scan QR codes for attendance'),
('Human Resources (HR)', 'Check new equipment, asset registers, property tags, and personnel assignments');

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('duplicate_scan_minutes', '30'),
('app_name', 'Computer Laboratory Management System'),
('session_lifetime', '120'),
('qr_size', '300'),
('current_semester', 'First Semester'),
('academic_year', '2026-2027'),
('offline_mode', 'true'),
('time_in_shuffle_mode', 'auto');

-- Insert default equipment categories
INSERT INTO `equipment_categories` (`name`) VALUES
('System Unit'), ('Monitor'), ('Keyboard'), ('Mouse'), ('RAM'), ('GPU'),
('Power Supply'), ('Motherboard'), ('SSD'), ('HDD'), ('Network Device'),
('Printer'), ('Projector'), ('Laboratory Apparatus'), ('Others');

-- Create default users (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `department_segment`, `status`) VALUES
('System Administrator', 'admin@labmanagement.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Main Admin Office', 'active'),
('Laboratory Administrator', 'labadmin@labmanagement.com', '$2y$10$8KzQMG.CcVJ3S80U41LdYOJ7PEax4BfHk6CqDGTOBvd6aQ6wY6KMe', 2, 'Computer Labs Department', 'active'),
('Laboratory Custodian', 'custodian@labmanagement.com', '$2y$10$8KzQMG.CcVJ3S80U41LdYOJ7PEax4BfHk6CqDGTOBvd6aQ6wY6KMe', 3, 'Property & Custodial Office', 'active'),
('Faculty Member', 'faculty@labmanagement.com', '$2y$10$8KzQMG.CcVJ3S80U41LdYOJ7PEax4BfHk6CqDGTOBvd6aQ6wY6KMe', 4, 'College of Computer Studies', 'active'),
('HR Asset Specialist', 'hr@labmanagement.com', '$2y$10$8KzQMG.CcVJ3S80U41LdYOJ7PEax4BfHk6CqDGTOBvd6aQ6wY6KMe', 6, 'Human Resources Department', 'active');
