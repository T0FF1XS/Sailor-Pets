DROP DATABASE IF EXISTS sailor_pets;
CREATE DATABASE sailor_pets;
USE sailor_pets;

CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(30) NOT NULL
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(60) NOT NULL
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    pet_type VARCHAR(30) NOT NULL,
    retail_price DECIMAL(10,2) NOT NULL,
    wholesale_price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    low_stock_level INT NOT NULL DEFAULT 10,
    tag VARCHAR(30),
    image VARCHAR(150),
    is_active TINYINT NOT NULL DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    CHECK (retail_price > 0),
    CHECK (wholesale_price > 0),
    CHECK (wholesale_price <= retail_price),
    CHECK (stock >= 0),
    CHECK (low_stock_level >= 0)
);

CREATE TABLE stock_in (
    stock_in_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    quantity INT NOT NULL,
    date_received DATE NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    CHECK (quantity > 0)
);

CREATE TABLE low_stock_alerts (
    alert_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    message VARCHAR(150) NOT NULL,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE inquiries (
    inquiry_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(100),
    customer_address VARCHAR(150),
    inquiry_type ENUM('Retail','Wholesale') NOT NULL,
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    message VARCHAR(255),
    handled_by INT,
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (handled_by) REFERENCES users(user_id)
);

CREATE TABLE inquiry_items (
    inquiry_item_id INT AUTO_INCREMENT PRIMARY KEY,
    inquiry_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (inquiry_id) REFERENCES inquiries(inquiry_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    CHECK (quantity > 0),
    CHECK (unit_price >= 0)
);

CREATE TABLE sales (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    cashier_id INT NOT NULL,
    customer_name VARCHAR(100) DEFAULT 'Walk-in',
    price_type ENUM('Retail','Wholesale') NOT NULL,
    payment_method ENUM('Cash','GCash') NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cashier_id) REFERENCES users(user_id),
    CHECK (total_amount >= 0)
);

CREATE TABLE sale_items (
    sale_item_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    CHECK (quantity > 0),
    CHECK (unit_price >= 0),
    CHECK (subtotal = quantity * unit_price)
);

CREATE TABLE storage_rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(50) NOT NULL,
    location VARCHAR(100)
);

CREATE TABLE sensor_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    temperature DECIMAL(4,1) NOT NULL,
    humidity DECIMAL(4,1) NOT NULL,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES storage_rooms(room_id),
    CHECK (humidity >= 0 AND humidity <= 100),
    CHECK (temperature >= -40 AND temperature <= 80)
);

DELIMITER //

CREATE TRIGGER deduct_stock_after_sale
AFTER INSERT ON sale_items
FOR EACH ROW
BEGIN
    UPDATE products SET stock = stock - NEW.quantity
    WHERE product_id = NEW.product_id;
END//

CREATE TRIGGER add_stock_after_receive
AFTER INSERT ON stock_in
FOR EACH ROW
BEGIN
    UPDATE products SET stock = stock + NEW.quantity
    WHERE product_id = NEW.product_id;
END//

CREATE TRIGGER check_low_stock
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF NEW.stock <= NEW.low_stock_level AND OLD.stock > NEW.low_stock_level THEN
        INSERT INTO low_stock_alerts (product_id, message)
        VALUES (NEW.product_id, CONCAT(NEW.product_name, ' is low on stock (', NEW.stock, ' left)'));
    END IF;
END//

DELIMITER ;

CREATE VIEW low_stock_products AS
SELECT p.product_id, p.product_name, c.category_name, p.stock, p.low_stock_level
FROM products p
JOIN categories c ON p.category_id = c.category_id
WHERE p.stock <= p.low_stock_level AND p.is_active = 1;

CREATE VIEW daily_sales AS
SELECT DATE(sale_date) AS sale_day, COUNT(*) AS total_sales, SUM(total_amount) AS total_income
FROM sales
GROUP BY DATE(sale_date);

INSERT INTO roles (role_name) VALUES ('Owner'), ('Sales Staff'), ('Inventory Staff'), ('Customer');
