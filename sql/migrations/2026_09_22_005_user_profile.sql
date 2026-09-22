-- Add profile fields to users table
ALTER TABLE `users`
  ADD COLUMN `address`  VARCHAR(255) DEFAULT NULL AFTER `phone`,
  ADD COLUMN `city`     VARCHAR(100) DEFAULT NULL AFTER `address`,
  ADD COLUMN `avatar`   VARCHAR(255) DEFAULT NULL AFTER `city`;
