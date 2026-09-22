-- Migration: Add transaction_id column to orders table
-- For SSLCommerz payment gateway integration
-- Run this AFTER the main schema.sql

ALTER TABLE `orders`
  ADD COLUMN `transaction_id` VARCHAR(255) DEFAULT NULL
  AFTER `payment_method`;

ALTER TABLE `orders`
  ADD INDEX `idx_orders_transaction_id` (`transaction_id`);
