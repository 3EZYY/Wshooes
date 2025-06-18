-- SQL script untuk membuat tabel-tabel yang dibutuhkan admin dashboard
-- Jalankan script ini jika tabel belum ada

-- Tabel untuk settings sistem
CREATE TABLE IF NOT EXISTS `settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL UNIQUE,
    `setting_value` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk coupons
CREATE TABLE IF NOT EXISTS `coupons` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(50) NOT NULL UNIQUE,
    `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
    `discount_value` decimal(10,2) NOT NULL,
    `min_amount` decimal(10,2) DEFAULT 0,
    `max_uses` int(11) DEFAULT 0,
    `used_count` int(11) DEFAULT 0,
    `status` enum('active','inactive') DEFAULT 'active',
    `expires_at` datetime NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk reviews (jika belum ada)
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `rating` int(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `comment` text,
    `status` enum('pending','approved','rejected') DEFAULT 'pending',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `product_id` (`product_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk categories (jika belum ada)
CREATE TABLE IF NOT EXISTS `categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text,
    `image` varchar(255) NULL,
    `status` enum('active','inactive') DEFAULT 'active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default categories jika belum ada
INSERT IGNORE INTO `categories` (`name`, `description`) VALUES
('Sneakers', 'Casual and sport sneakers'),
('Formal Shoes', 'Business and formal footwear'),
('Sandals', 'Casual sandals and flip-flops'),
('Boots', 'Boots for various occasions'),
('Athletic', 'Sports and running shoes'),
('Casual', 'Everyday casual shoes');

-- Insert default settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Wshooes'),
('site_description', 'Premium Footwear E-commerce'),
('contact_email', 'admin@wshooes.com'),
('contact_phone', '+62 123 456 7890'),
('currency', 'IDR'),
('tax_rate', '10'),
('shipping_fee', '15000'),
('free_shipping_threshold', '500000'),
('low_stock_threshold', '10'),
('maintenance_mode', '0'),
('registration_enabled', '1'),
('email_notifications', '1');

-- Sample coupons
INSERT IGNORE INTO `coupons` (`code`, `discount_type`, `discount_value`, `min_amount`, `max_uses`, `expires_at`) VALUES
('WELCOME10', 'percentage', 10.00, 100000, 100, '2025-12-31 23:59:59'),
('SAVE50K', 'fixed', 50000.00, 500000, 50, '2025-06-30 23:59:59'),
('NEWUSER', 'percentage', 15.00, 200000, 0, '2025-12-31 23:59:59');

-- Sample reviews (jika tabel products dan users sudah ada)
INSERT IGNORE INTO `reviews` (`user_id`, `product_id`, `rating`, `comment`, `status`) 
SELECT 
    u.id as user_id,
    p.id as product_id,
    5 as rating,
    'Great product! Highly recommended.' as comment,
    'approved' as status
FROM users u, products p 
WHERE u.role = 'customer' AND p.status = 'active'
LIMIT 5;

-- Menambahkan kolom category_id ke tabel products jika belum ada
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `category_id` int(11) NULL,
ADD KEY IF NOT EXISTS `category_id` (`category_id`);

-- Update products dengan random category_id jika belum ada
UPDATE `products` 
SET `category_id` = (SELECT `id` FROM `categories` ORDER BY RAND() LIMIT 1) 
WHERE `category_id` IS NULL OR `category_id` = 0;

-- Menambahkan foreign key constraint
-- ALTER TABLE `products` 
-- ADD CONSTRAINT `fk_products_category` 
-- FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

-- Tabel untuk order_items (jika belum ada)
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    `total` decimal(10,2) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `order_id` (`order_id`),
    KEY `product_id` (`product_id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample order_items jika belum ada
INSERT IGNORE INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`, `total`)
SELECT 
    o.id as order_id,
    p.id as product_id,
    1 as quantity,
    p.price,
    p.price as total
FROM orders o
CROSS JOIN products p
WHERE NOT EXISTS (SELECT 1 FROM order_items oi WHERE oi.order_id = o.id)
AND RAND() < 0.3
LIMIT 20;

-- Menambahkan kolom main_image ke products jika belum ada
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `main_image` varchar(255) NULL;

-- Update beberapa products dengan sample image
UPDATE `products` 
SET `main_image` = CONCAT('sample_shoe_', MOD(id, 5) + 1, '.jpg')
WHERE `main_image` IS NULL
LIMIT 10;
