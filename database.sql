-- Create database
CREATE DATABASE IF NOT EXISTS anefarm;
USE anefarm;

-- Owners table (for owners)
CREATE TABLE IF NOT EXISTS owners (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    role_en VARCHAR(200),
    role_om VARCHAR(200),
    role_am VARCHAR(200),
    bio_en TEXT,
    bio_om TEXT,
    bio_am TEXT,
    image VARCHAR(255),
    facebook VARCHAR(255),
    telegram VARCHAR(255),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name_en VARCHAR(100) NOT NULL,
    name_om VARCHAR(100),
    name_am VARCHAR(100),
    description_en TEXT,
    description_om TEXT,
    description_am TEXT,
    price DECIMAL(10,2),
    unit VARCHAR(50),
    image VARCHAR(255),
    in_stock BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0
);

-- Gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title_en VARCHAR(255),
    title_om VARCHAR(255),
    title_am VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    category VARCHAR(50),
    featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE,
    value_en TEXT,
    value_om TEXT,
    value_am TEXT
);
-- Messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Insert default settings
INSERT INTO settings (setting_key, value_en, value_om, value_am) VALUES
('farm_name', 'Anefarm', 'Anefarm', 'አኔፋርም'),
('founder', 'Anwar', 'Anwar', 'አንዋር'),
('address', 'Asebe teferi, Ethiopia', 'Asebe teferi, Itoophiyaa', 'አሰበ ተፈሪ፣ ኢትዮጵያ'),
('phone', '+251 98 978 4673', '+251 98 978 4673', '+251 98 978 4673'),
('email', 'info@anefarm.com', 'info@anefarm.com', 'info@anefarm.com'),
('facebook', 'https://facebook.com/anefarm', 'https://facebook.com/anefarm', 'https://facebook.com/anefarm'),
('telegram', 'https://t.me/anefarm', 'https://t.me/anefarm', 'https://t.me/anefarm'),
('tiktok', 'https://tiktok.com/@anefarm', 'https://tiktok.com/@anefarm', 'https://tiktok.com/@anefarm');
-- Insert sample owners (5 owners)
INSERT INTO owners (name, role_en, role_om, role_am, sort_order) VALUES
('Anwar', 'Founder & Head Farmer', 'Abbaa Dhaabbataa fi Qonnaan Bulaa', 'መስራች እና ዋና ገበሬ', 1);


ALTER TABLE gallery ADD COLUMN description_en TEXT NULL AFTER title_am;
ALTER TABLE gallery ADD COLUMN description_om TEXT NULL AFTER description_en;
ALTER TABLE gallery ADD COLUMN description_am TEXT NULL AFTER description_om;

-- Create database
CREATE DATABASE IF NOT EXISTS anefarm;
USE anefarm;

-- Owners table (for owners)
CREATE TABLE IF NOT EXISTS owners (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    role_en VARCHAR(200),
    role_om VARCHAR(200),
    role_am VARCHAR(200),
    bio_en TEXT,
    bio_om TEXT,
    bio_am TEXT,
    image VARCHAR(255),
    facebook VARCHAR(255),
    telegram VARCHAR(255),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name_en VARCHAR(100) NOT NULL,
    name_om VARCHAR(100),
    name_am VARCHAR(100),
    description_en TEXT,
    description_om TEXT,
    description_am TEXT,
    price DECIMAL(10,2),
    unit VARCHAR(50),
    image VARCHAR(255),
    in_stock BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0
);

-- Gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title_en VARCHAR(255),
    title_om VARCHAR(255),
    title_am VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    category VARCHAR(50),
    featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE,
    value_en TEXT,
    value_om TEXT,
    value_am TEXT
);
-- Messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Insert default settings
INSERT INTO settings (setting_key, value_en, value_om, value_am) VALUES
('farm_name', 'Anefarm', 'Anefarm', 'አኔፋርም'),
('founder', 'Anwar', 'Anwar', 'አንዋር'),
('address', 'Asebe teferi, Ethiopia', 'Asebe teferi, Itoophiyaa', 'አሰበ ተፈሪ፣ ኢትዮጵያ'),
('phone', '+251 98 978 4673', '+251 98 978 4673', '+251 98 978 4673'),
('email', 'anem5005@gmail.com', 'anem5005@gmail.com', 'anem5005@gmail.com'),
('facebook', 'https://facebook.com/anefarm', 'https://facebook.com/anefarm', 'https://facebook.com/anefarm'),
('telegram', 'https://t.me/anefarm', 'https://t.me/anefarm', 'https://t.me/anefarm'),
('tiktok', 'https://tiktok.com/@anefarm', 'https://tiktok.com/@anefarm', 'https://tiktok.com/@anefarm');
-- Insert sample owners (5 owners)
INSERT INTO owners (name, role_en, role_om, role_am, sort_order) VALUES
('Anwar', 'Founder & Head Farmer', 'Abbaa Dhaabbataa fi Qonnaan Bulaa', 'መስራች እና ዋና ገበሬ', 1);


UPDATE settings 
SET value_en = 'anem5005@gmail.com', 
    value_om = 'anem5005@gmail.com', 
    value_am = 'anem5005@gmail.com' 
WHERE setting_key = 'email';

-- 1. Update the Farm Name (EN, OM, AM)
UPDATE settings 
SET value_en = 'Ane Dairy Farm', 
    value_om = 'Eyru Aananii Ane', 
    value_am = 'አኔ የወተት እርሻ' 
WHERE setting_key = 'farm_name';

-- 2. Update the Email address
UPDATE settings 
SET value_en = 'anem5005@gmail.com', 
    value_om = 'anem5005@gmail.com', 
    value_am = 'anem5005@gmail.com' 
WHERE setting_key = 'email';

-- 3. Update Social Media Links to match the new brand
UPDATE settings SET 
    value_en = 'https://facebook.com/anedairyfarm',
    value_om = 'https://facebook.com/anedairyfarm',
    value_am = 'https://facebook.com/anedairyfarm'
WHERE setting_key = 'facebook';

UPDATE settings SET 
    value_en = 'https://t.me/anedairyfarm',
    value_om = 'https://t.me/anedairyfarm',
    value_am = 'https://t.me/anedairyfarm'
WHERE setting_key = 'telegram';

UPDATE settings SET 
    value_en = 'https://tiktok.com/@anedairyfarm',
    value_om = 'https://tiktok.com/@anedairyfarm',
    value_am = 'https://tiktok.com/@anedairyfarm'
WHERE setting_key = 'tiktok';



-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
    last_login DATETIME NULL,
    last_login_ip VARCHAR(45) NULL,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME NULL,
    password_reset_token VARCHAR(100) NULL,
    password_reset_expires DATETIME NULL,
    two_factor_secret VARCHAR(255) NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_reset_token (password_reset_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login history table
CREATE TABLE IF NOT EXISTS admin_login_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    login_time DATETIME NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    status ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_user_login (user_id, login_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset requests table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(100) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Remember me tokens table
CREATE TABLE IF NOT EXISTS admin_remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: Anefarm@2024$Secure)
-- You'll need to run this after creating the Auth class to generate proper hash
INSERT INTO admin_users (username, email, password_hash, full_name, role) 
VALUES (
    'anediaryfarm_admin', 
    'admin@anediaryfarm.com', 
    '$2y$10$ClhXGxuAXP1MtD0aGvJCwukeMFl9PUYY1tUS99t3ZOpwOXFj6IgKO',
    'Anediaryfarm Administrator', 
    'super_admin'
);