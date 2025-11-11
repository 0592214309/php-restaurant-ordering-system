-- ============================================
-- Beginner-Friendly Restaurant Ordering System
-- Database: restaurant
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS restaurant;
USE restaurant;

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
-- Stores customer orders
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
-- That's it! You can import this file in phpMyAdmin.
-- It will create the database, tables, and some sample data for you to test.
