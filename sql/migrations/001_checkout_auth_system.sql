-- ============================================================
-- Migration: Checkout Auth System
-- Run this on existing crockeriesmart database
-- ============================================================

USE `crockeriesmart`;

-- 1. Make email nullable, make phone NOT NULL + UNIQUE
ALTER TABLE `users`
  MODIFY COLUMN `email` VARCHAR(150) DEFAULT NULL,
  MODIFY COLUMN `phone` VARCHAR(20) NOT NULL,
  DROP INDEX `idx_users_phone`,
  ADD UNIQUE KEY `uk_users_phone` (`phone`);

-- 2. Add transaction_id to orders
ALTER TABLE `orders`
  ADD COLUMN `transaction_id` VARCHAR(100) DEFAULT NULL AFTER `payment_status`,
  ADD UNIQUE KEY `uk_orders_transaction` (`transaction_id`);

-- 3. Create otp_tokens table
DROP TABLE IF EXISTS `otp_tokens`;
CREATE TABLE `otp_tokens` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone`      VARCHAR(20)     NOT NULL,
  `otp_code`   VARCHAR(6)      NOT NULL,
  `expires_at` DATETIME        NOT NULL,
  `used`       TINYINT         NOT NULL DEFAULT 0,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_otp_phone` (`phone`),
  INDEX `idx_otp_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
