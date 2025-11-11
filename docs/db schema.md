-- ============================================
-- Restaurant Ordering System - Database Schema
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS restaurant_ordering;
USE restaurant_ordering;

-- ============================================
-- Table: users
-- Stores customer, admin, and staff accounts
-- ============================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin', 'staff') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- ============================================
-- Table: categories
-- Stores food categories (e.g., Appetizers, Main Course, Desserts)
-- ============================================
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_display_order (display_order)
);

-- ============================================
-- Table: menu_items
-- Stores all food items available for ordering
-- ============================================
CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    preparation_time INT DEFAULT 15, -- in minutes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_available (is_available)
);

-- ============================================
-- Table: orders
-- Stores customer orders
-- ============================================
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    delivery_address TEXT,
    delivery_phone VARCHAR(20),
    special_instructions TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date),
    INDEX idx_order_number (order_number)
);

-- ============================================
-- Table: order_items
-- Stores individual items in each order
-- ============================================
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_at_order DECIMAL(10, 2) NOT NULL, -- Price at the time of order
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_item (item_id)
);

-- ============================================
-- Table: admin_logs (Optional - for tracking admin actions)
-- ============================================
CREATE TABLE admin_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
);

-- ============================================
-- Insert Sample Data for Testing
-- ============================================

-- Insert Categories
INSERT INTO categories (category_name, description, display_order) VALUES
('Appetizers', 'Start your meal with these delicious starters', 1),
('Main Course', 'Our signature main dishes', 2),
('Sides', 'Perfect accompaniments to your meal', 3),
('Desserts', 'Sweet endings to your meal', 4),
('Beverages', 'Refreshing drinks', 5);

-- Insert Default Admin User
-- Password: admin123 (hashed with PHP password_hash)
-- You'll need to generate this hash using PHP
INSERT INTO users (full_name, email, phone, password_hash, role) VALUES
('Admin User', 'admin@restaurant.com', '0240000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Menu Items (without images for now)
INSERT INTO menu_items (category_id, item_name, description, price, preparation_time) VALUES
-- Appetizers
(1, 'Spring Rolls', 'Crispy vegetable spring rolls served with sweet chili sauce', 12.50, 10),
(1, 'Chicken Wings', 'Spicy buffalo chicken wings with ranch dressing', 18.00, 15),
(1, 'Soup of the Day', 'Chef\'s special soup made fresh daily', 10.00, 5),

-- Main Course
(2, 'Grilled Chicken', 'Marinated grilled chicken breast with herbs', 35.00, 25),
(2, 'Beef Steak', 'Premium beef steak cooked to perfection', 55.00, 30),
(2, 'Fish & Chips', 'Crispy battered fish with french fries', 38.00, 20),
(2, 'Vegetable Pasta', 'Fresh pasta with seasonal vegetables', 28.00, 15),

-- Sides
(3, 'French Fries', 'Crispy golden french fries', 8.00, 10),
(3, 'Coleslaw', 'Fresh cabbage salad', 7.00, 5),
(3, 'Rice', 'Steamed white or jollof rice', 10.00, 15),

-- Desserts
(4, 'Chocolate Cake', 'Rich chocolate cake with ganache', 15.00, 5),
(4, 'Ice Cream', 'Vanilla, chocolate, or strawberry', 12.00, 3),
(4, 'Fruit Salad', 'Fresh seasonal fruits', 10.00, 5),

-- Beverages
(5, 'Soft Drinks', 'Coca-Cola, Sprite, Fanta', 5.00, 2),
(5, 'Fresh Juice', 'Orange, pineapple, or mango', 8.00, 5),
(5, 'Coffee', 'Hot or iced coffee', 7.00, 5);

-- ============================================
-- Useful Queries for Development
-- ============================================

-- Get all menu items with category names
-- SELECT m.*, c.category_name 
-- FROM menu_items m 
-- JOIN categories c ON m.category_id = c.category_id 
-- WHERE m.is_available = TRUE 
-- ORDER BY c.display_order, m.item_name;

-- Get order details with items
-- SELECT o.*, u.full_name, u.email 
-- FROM orders o 
-- JOIN users u ON o.user_id = u.user_id 
-- WHERE o.order_id = ?;

-- Get items in a specific order
-- SELECT oi.*, mi.item_name, mi.image_url 
-- FROM order_items oi 
-- JOIN menu_items mi ON oi.item_id = mi.item_id 
-- WHERE oi.order_id = ?;

-- Get user's order history
-- SELECT * FROM orders 
-- WHERE user_id = ? 
-- ORDER BY order_date DESC;

-- Get today's orders
-- SELECT * FROM orders 
-- WHERE DATE(order_date) = CURDATE() 
-- ORDER BY order_date DESC;