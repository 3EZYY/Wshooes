-- Add remember_token and token_expires columns to users table
ALTER TABLE users
ADD COLUMN remember_token VARCHAR(100) DEFAULT NULL,
ADD COLUMN token_expires DATETIME DEFAULT NULL;

-- Add reset token columns to users table
ALTER TABLE users
ADD COLUMN reset_token VARCHAR(100) DEFAULT NULL,
ADD COLUMN token_expires DATETIME DEFAULT NULL;