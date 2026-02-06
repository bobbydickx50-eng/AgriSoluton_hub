-- AgriSolutions Hub Database Structure
-- Version: 1.0
-- Created: 2024-03-15
-- Author: David Bablo Mushii

-- Create database
CREATE DATABASE IF NOT EXISTS agrisolutions_db;
USE agrisolutions_db;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    country VARCHAR(50) DEFAULT 'Tanzania',
    password VARCHAR(255) NOT NULL,
    user_type ENUM('farmer', 'business', 'student', 'expert', 'admin') DEFAULT 'farmer',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    profile_image VARCHAR(255),
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Market prices table
CREATE TABLE market_prices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    commodity VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    market VARCHAR(100),
    unit VARCHAR(20) DEFAULT 'TZS/kg',
    change_percent DECIMAL(5,2),
    trend ENUM('up', 'down', 'stable') DEFAULT 'stable',
    category ENUM('grains', 'cash', 'vegetables', 'fruits', 'other') DEFAULT 'other',
    source VARCHAR(100),
    verified BOOLEAN DEFAULT FALSE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_commodity (commodity),
    INDEX idx_category (category),
    INDEX idx_updated (updated_at)
);

-- Weather data table
CREATE TABLE weather_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    temperature_high INT,
    temperature_low INT,
    condition VARCHAR(50),
    humidity INT,
    precipitation_chance INT,
    wind_speed INT,
    wind_direction VARCHAR(10),
    advisory TEXT,
    source VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_location (location),
    INDEX idx_date (date),
    UNIQUE KEY unique_location_date (location, date)
);

-- Resources table
CREATE TABLE resources (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    content LONGTEXT,
    category ENUM('crops', 'techniques', 'tools', 'soil', 'weather', 'business', 'research') NOT NULL,
    subcategory VARCHAR(100),
    author VARCHAR(100),
    author_type ENUM('expert', 'farmer', 'institution', 'other') DEFAULT 'expert',
    file_url VARCHAR(500),
    image_url VARCHAR(500),
    views INT DEFAULT 0,
    downloads INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    status ENUM('published', 'draft', 'archived') DEFAULT 'published',
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_featured (featured),
    INDEX idx_status (status),
    FULLTEXT idx_search (title, description, content)
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    billing_address TEXT,
    city VARCHAR(50),
    region VARCHAR(50),
    country VARCHAR(50) DEFAULT 'Tanzania',
    postal_code VARCHAR(20),
    total_amount DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    shipping_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    final_amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('mpesa', 'bank', 'cod', 'card') DEFAULT 'mpesa',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    tracking_number VARCHAR(100),
    estimated_delivery DATE,
    delivered_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_user_id (user_id),
    INDEX idx_status (order_status),
    INDEX idx_created (created_at)
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_type VARCHAR(100) NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id)
);

-- Contact messages table
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    category ENUM('general', 'technical', 'market', 'export', 'order', 'other') DEFAULT 'general',
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    assigned_to INT,
    reply_text TEXT,
    replied_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_created (created_at)
);

-- Global opportunities table
CREATE TABLE global_opportunities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    country VARCHAR(100),
    region VARCHAR(100),
    product_types TEXT,
    requirements TEXT,
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    deadline DATE,
    status ENUM('active', 'expired', 'closed') DEFAULT 'active',
    views INT DEFAULT 0,
    inquiries INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_country (country)
);

-- News and updates table
CREATE TABLE news_updates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    category ENUM('market', 'weather', 'technology', 'policy', 'event', 'research') DEFAULT 'market',
    author VARCHAR(100),
    image_url VARCHAR(500),
    views INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    publish_date DATE,
    status ENUM('published', 'draft', 'archived') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_featured (featured),
    INDEX idx_status (status)
);

-- Activity logs table
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
);

-- Newsletter subscriptions table
CREATE TABLE newsletter_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    category ENUM('market', 'weather', 'resources', 'opportunities', 'all') DEFAULT 'all',
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME,
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- Insert sample data

-- Sample users (password is hashed 'password123')
INSERT INTO users (name, email, phone, country, password, user_type) VALUES
('David Bablo Mushii', 'david@agrisolutionshub.com', '+255748550225', 'Tanzania', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('John Farmer', 'john@example.com', '+255712345678', 'Tanzania', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer'),
('Sarah AgriBusiness', 'sarah@agribusiness.co.tz', '+255754321098', 'Tanzania', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business'),
('Dr. James Expert', 'james@agriculture.edu', '+255788765432', 'Kenya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'expert');

-- Sample market prices
INSERT INTO market_prices (commodity, price, market, unit, change_percent, trend, category) VALUES
('Maize', 1250.00, 'Arusha', 'TZS/kg', 2.5, 'up', 'grains'),
('Rice', 3200.00, 'Mbeya', 'TZS/kg', -1.2, 'down', 'grains'),
('Beans', 2800.00, 'Morogoro', 'TZS/kg', 3.7, 'up', 'grains'),
('Coffee', 8500.00, 'Kilimanjaro', 'TZS/kg', 5.1, 'up', 'cash'),
('Cashew Nuts', 6200.00, 'Mtwara', 'TZS/kg', 1.8, 'up', 'cash'),
('Tea', 3800.00, 'Njombe', 'TZS/kg', -0.5, 'down', 'cash'),
('Tomatoes', 1800.00, 'Dar es Salaam', 'TZS/kg', 4.2, 'up', 'vegetables'),
('Onions', 2100.00, 'Dodoma', 'TZS/kg', 2.1, 'up', 'vegetables');

-- Sample weather data
INSERT INTO weather_data (location, date, temperature_high, temperature_low, condition, humidity, precipitation_chance, wind_speed) VALUES
('Morogoro', CURDATE(), 28, 19, 'Partly Cloudy', 65, 10, 12),
('Morogoro', DATE_ADD(CURDATE(), INTERVAL 1 DAY), 29, 20, 'Sunny Intervals', 60, 5, 10),
('Morogoro', DATE_ADD(CURDATE(), INTERVAL 2 DAY), 26, 18, 'Light Rain', 75, 40, 8),
('Morogoro', DATE_ADD(CURDATE(), INTERVAL 3 DAY), 27, 19, 'Cloudy', 70, 20, 9),
('Morogoro', DATE_ADD(CURDATE(), INTERVAL 4 DAY), 30, 21, 'Sunny', 55, 0, 15);

-- Sample resources
INSERT INTO resources (title, description, category, subcategory, author, content, featured) VALUES
('Maize Cultivation Guide', 'Complete guide to maize farming in Tanzania', 'crops', 'grains', 'SUA Agriculture Department', 'This comprehensive guide covers all aspects of maize cultivation...', TRUE),
('Drip Irrigation Systems', 'Modern irrigation techniques for water conservation', 'techniques', 'irrigation', 'Irrigation Expert', 'Learn how to install and maintain efficient drip irrigation systems...', TRUE),
('Organic Pest Control', 'Natural methods for pest management', 'techniques', 'pest_control', 'Organic Farming Association', 'Chemical-free approaches to controlling agricultural pests...', FALSE),
('Soil Management Basics', 'Understanding and improving soil health', 'soil', 'soil_health', 'Soil Science Expert', 'Essential knowledge for maintaining productive soil...', TRUE);

-- Sample global opportunities
INSERT INTO global_opportunities (title, description, country, product_types, requirements, status) VALUES
('EU Organic Coffee Import', 'European company seeking organic coffee from Tanzania', 'Germany', 'Coffee, Organic Products', 'Organic certification, EU quality standards', 'active'),
('Cashew Nuts Export to China', 'Chinese distributor looking for high-quality cashew nuts', 'China', 'Cashew Nuts, Processed Nuts', 'Quality certification, Proper packaging', 'active'),
('Spices Export Opportunity', 'US retailer seeking African spices', 'USA', 'Spices, Herbs', 'FDA approval, Fair trade certification', 'active');

-- Sample news updates
INSERT INTO news_updates (title, content, category, author, publish_date, featured) VALUES
('New Maize Hybrid Varieties Released', 'Tanzania Agricultural Research Institute has released new drought-resistant maize varieties...', 'research', 'TARI Researcher', CURDATE(), TRUE),
('Export Opportunities to European Union', 'New trade agreements create export opportunities for Tanzanian agricultural products...', 'market', 'Trade Expert', DATE_SUB(CURDATE(), INTERVAL 2 DAY), TRUE),
('Weather Advisory for Farmers', 'Expected rainfall patterns for the coming planting season...', 'weather', 'TMA Meteorologist', DATE_SUB(CURDATE(), INTERVAL 5 DAY), FALSE);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_market_prices_commodity ON market_prices(commodity);
CREATE INDEX idx_weather_data_location_date ON weather_data(location, date);
CREATE INDEX idx_resources_category_status ON resources(category, status);
CREATE INDEX idx_orders_user_status ON orders(user_id, order_status);
CREATE INDEX idx_contact_messages_status_created ON contact_messages(status, created_at);

-- Create views for common queries

-- View for active market prices
CREATE VIEW active_market_prices AS
SELECT * FROM market_prices 
WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY updated_at DESC;

-- View for latest resources
CREATE VIEW latest_resources AS
SELECT * FROM resources 
WHERE status = 'published' 
ORDER BY created_at DESC 
LIMIT 10;

-- View for user orders summary
CREATE VIEW user_orders_summary AS
SELECT 
    u.id as user_id,
    u.name,
    u.email,
    COUNT(o.id) as total_orders,
    SUM(o.final_amount) as total_spent,
    MAX(o.created_at) as last_order_date
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
GROUP BY u.id, u.name, u.email;

-- Create stored procedures

-- Procedure to update market price
DELIMITER //
CREATE PROCEDURE UpdateMarketPrice(
    IN p_commodity VARCHAR(100),
    IN p_price DECIMAL(10,2),
    IN p_market VARCHAR(100),
    IN p_unit VARCHAR(20)
)
BEGIN
    DECLARE old_price DECIMAL(10,2);
    DECLARE change_pct DECIMAL(5,2);
    DECLARE trend_val ENUM('up', 'down', 'stable');
    
    -- Get old price
    SELECT price INTO old_price 
    FROM market_prices 
    WHERE commodity = p_commodity 
    AND market = p_market 
    ORDER BY updated_at DESC 
    LIMIT 1;
    
    -- Calculate change percentage
    IF old_price IS NOT NULL THEN
        SET change_pct = ((p_price - old_price) / old_price) * 100;
        
        -- Determine trend
        IF change_pct > 1 THEN
            SET trend_val = 'up';
        ELSEIF change_pct < -1 THEN
            SET trend_val = 'down';
        ELSE
            SET trend_val = 'stable';
        END IF;
    ELSE
        SET change_pct = 0;
        SET trend_val = 'stable';
    END IF;
    
    -- Insert new price
    INSERT INTO market_prices (commodity, price, market, unit, change_percent, trend)
    VALUES (p_commodity, p_price, p_market, p_unit, change_pct, trend_val);
    
    SELECT 'Price updated successfully' as message;
END//
DELIMITER ;

-- Procedure to get weather forecast
DELIMITER //
CREATE PROCEDURE GetWeatherForecast(
    IN p_location VARCHAR(100),
    IN p_days INT
)
BEGIN
    SELECT * FROM weather_data 
    WHERE location = p_location 
    AND date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL p_days DAY)
    ORDER BY date ASC;
END//
DELIMITER ;

-- Create triggers

-- Trigger to update order number
DELIMITER //
CREATE TRIGGER before_order_insert
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_number IS NULL THEN
        SET NEW.order_number = CONCAT('AG-', YEAR(CURDATE()), '-', 
            LPAD((SELECT COUNT(*) FROM orders WHERE YEAR(created_at) = YEAR(CURDATE())) + 1, 4, '0'));
    END IF;
END//
DELIMITER ;

-- Trigger to log user activity
DELIMITER //
CREATE TRIGGER after_user_login
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.last_login != OLD.last_login THEN
        INSERT INTO activity_logs (user_id, action, details)
        VALUES (NEW.id, 'login', CONCAT('User logged in from IP: ', @user_ip));
    END IF;
END//
DELIMITER ;

-- Create events for automated tasks

-- Event to archive old orders (run daily)
DELIMITER //
CREATE EVENT archive_old_orders
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE orders 
    SET order_status = 'archived' 
    WHERE order_status = 'delivered' 
    AND delivered_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
END//
DELIMITER ;

-- Event to update expired opportunities (run daily)
DELIMITER //
CREATE EVENT update_expired_opportunities
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE global_opportunities 
    SET status = 'expired' 
    WHERE status = 'active' 
    AND deadline < CURDATE();
END//
DELIMITER ;

-- Set up database user (run this separately with root privileges)
-- CREATE USER 'agrisolutions_user'@'localhost' IDENTIFIED BY 'secure_password_123';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON agrisolutions_db.* TO 'agrisolutions_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Notes for deployment:
-- 1. Change database credentials in config.php
-- 2. Run this SQL file to set up the database
-- 3. Update the database user credentials as needed
-- 4. Test the database connection
-- 5. Enable events: SET GLOBAL event_scheduler = ON;