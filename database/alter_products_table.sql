-- Add brand, sizes, and colors columns to products table
ALTER TABLE `products`
ADD COLUMN `brand` VARCHAR(100) DEFAULT NULL AFTER `category_id`,
ADD COLUMN `sizes` TEXT DEFAULT NULL AFTER `brand`,
ADD COLUMN `colors` TEXT DEFAULT NULL AFTER `sizes`;