-- Database: it_inventory_system
CREATE DATABASE IF NOT EXISTS it_inventory_system;
USE it_inventory_system;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    staff_id VARCHAR(50) NOT NULL UNIQUE,
    contact_number VARCHAR(20),
    access_permission ENUM('view', 'add', 'edit', 'admin') DEFAULT 'view',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Logs table
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    action TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Inventory tables
CREATE TABLE computers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100) NOT NULL,
    brand VARCHAR(50),
    serial_number VARCHAR(100),
    model VARCHAR(50),
    processor VARCHAR(50),
    ram VARCHAR(50),
    storage VARCHAR(50),
    ip_address VARCHAR(50),
    mac_address VARCHAR(50),
    location VARCHAR(100),
    existing_user VARCHAR(100),
    status ENUM('Available', 'Assigned', 'Under Repair', 'Decommissioned'),
    purchase_date DATE,
    warranty DATE,
    other_details TEXT
);

CREATE TABLE printers LIKE computers;
CREATE TABLE tablets LIKE computers;
CREATE TABLE phones LIKE computers;
CREATE TABLE servers LIKE computers;
CREATE TABLE network_equipment LIKE computers;
CREATE TABLE accessories LIKE computers;
