-- ============================================
-- Restaurant Ordering System - Phase 2
-- Database: restaurant
-- Updated with user authentication
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS restaurant;
USE restaurant;

-- ============================================
-- Table: users
-- Stores customer, admin, and staff accounts
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin', 'staff') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- ============================================
-- Table: categories
-- Stores food categories (e.g., Appetizers, Main Course)
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
);

-- ============================================
-- Table: menu_items
-- Stores all food items available for ordering
-- ============================================
CREATE TABLE IF NOT EXISTS menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- ============================================
-- Table: orders
-- Stores customer orders (updated for Phase 2)
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- ============================================
-- Table: order_items
-- Stores individual items in each order
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_at_order DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- ============================================
-- Insert Sample Categories
-- ============================================
INSERT INTO categories (category_name, description, display_order) VALUES
('Appetizers', 'Start your meal with these delicious starters', 1),
('Main Course', 'Our signature main dishes', 2),
('Desserts', 'Sweet endings to your meal', 3),
('Beverages', 'Refreshing drinks', 4);

-- ============================================
-- Insert Sample Menu Items
-- ============================================
INSERT INTO menu_items (category_id, item_name, description, price, image_url) VALUES
(1, 'Spring Rolls', 'Crispy vegetable spring rolls', 12.50, NULL),
(1, 'Chicken Wings', 'Spicy buffalo chicken wings', 18.00, NULL),
(2, 'Grilled Chicken', 'Marinated grilled chicken breast', 35.00, NULL),
(2, 'Beef Steak', 'Premium beef steak', 55.00, NULL),
(3, 'Chocolate Cake', 'Rich chocolate cake', 15.00, NULL),
(4, 'Soft Drink', 'Coca-Cola, Sprite, Fanta', 5.00, NULL);

-- ============================================
-- INSERT SAMPLE USER (Optional - for testing)
-- You can remove this and use the registration page instead
-- ============================================
-- Password: demo123 (hashed with PHP password_hash)
-- INSERT INTO users (full_name, email, phone, password_hash, role) VALUES
-- ('Demo Customer', 'demo@example.com', '0240000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- ============================================
-- That's it! The database is ready for Phase 2.
-- You can now use the registration page to create users.
