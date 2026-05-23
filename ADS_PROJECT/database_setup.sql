-- SQL script to set up the Stud-Track database
-- Run this in phpMyAdmin or MySQL command line

-- Create database
CREATE DATABASE IF NOT EXISTS stud_track;

-- Use the database
USE stud_track;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Staff', 'Admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create login history table
CREATE TABLE IF NOT EXISTS login_history (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('Student', 'Staff', 'Admin') NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    guardian_name VARCHAR(100) NOT NULL,
    address TEXT,
    grade_level VARCHAR(20) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create sections table
CREATE TABLE IF NOT EXISTS sections (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grade_level VARCHAR(20) NOT NULL,
    section_name VARCHAR(50) NOT NULL,
    adviser_name VARCHAR(100) DEFAULT NULL,
    capacity INT(4) NOT NULL DEFAULT 30,
    room_number VARCHAR(50) DEFAULT NULL,
    status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_section (grade_level, section_name)
);

-- Create section assignment table
CREATE TABLE IF NOT EXISTS section_assignments (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    section_id INT(6) UNSIGNED NOT NULL,
    enrollment_id INT(6) UNSIGNED NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (section_id, enrollment_id)
);

-- Create system settings table for dashboard configuration
CREATE TABLE IF NOT EXISTS system_settings (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    system_name VARCHAR(100) NOT NULL DEFAULT 'Stud-Track',
    school_year VARCHAR(20) NOT NULL DEFAULT '2025-2026',
    default_capacity INT(4) NOT NULL DEFAULT 30,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert initial system settings
INSERT IGNORE INTO system_settings (id, system_name, school_year, default_capacity) VALUES
(1, 'Stud-Track', '2025-2026', 30);

-- Insert sample users (passwords are hashed)
-- Password for all is 'password123'
INSERT IGNORE INTO users (username, email, password, role) VALUES
('Student User', 'student@stud-track.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student'),
('Staff User', 'staff@stud-track.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff'),
('Admin User', 'admin@stud-track.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');