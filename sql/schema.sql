-- Mixlar Marketplace Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS mixlar_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mixlar_marketplace;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    reset_token VARCHAR(255) NULL,
    reset_token_expiry DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plugins table
CREATE TABLE IF NOT EXISTS plugins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category ENUM('core', 'streaming', 'smarthome', 'control', 'creative') NOT NULL,
    tag VARCHAR(100) NOT NULL,
    status ENUM('instruction', 'download', 'installed', 'pending', 'approved', 'rejected') DEFAULT 'pending',
    author VARCHAR(100) NOT NULL,
    author_id INT NULL,
    social_url VARCHAR(500) NULL,
    description TEXT NOT NULL,
    image_color VARCHAR(100) DEFAULT 'from-blue-600 to-indigo-600',
    icon VARCHAR(100) DEFAULT 'fa-puzzle-piece',
    download_url VARCHAR(500) NULL,
    instruction_url VARCHAR(500) NULL,
    version VARCHAR(20) DEFAULT '1.0.0',
    downloads INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_featured (featured),
    INDEX idx_author_id (author_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plugin devices (many-to-many relationship)
CREATE TABLE IF NOT EXISTS plugin_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plugin_id INT NOT NULL,
    device_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (plugin_id) REFERENCES plugins(id) ON DELETE CASCADE,
    INDEX idx_plugin_id (plugin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, role)
VALUES ('admin', 'admin@mixlarlabs.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE username=username;
