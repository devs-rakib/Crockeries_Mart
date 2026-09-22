-- ============================================================
-- RBAC System Migration
-- Adds roles, permissions, role_permissions, activity_logs
-- Converts users.role ENUM to users.role_id INT
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. roles table
-- ============================================================
CREATE TABLE IF NOT EXISTS `roles` (
  `id`          INT UNSIGNED NOT NULL,
  `name`        VARCHAR(50)  NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. permissions table
-- ============================================================
CREATE TABLE IF NOT EXISTS `permissions` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_permissions_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. role_permissions table
-- ============================================================
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`       INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_permission` (`role_id`, `permission_id`),
  INDEX `idx_rp_permission` (`permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. activity_logs table
-- ============================================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED DEFAULT NULL,
  `action`     VARCHAR(100)    NOT NULL,
  `target`     VARCHAR(100)    DEFAULT NULL,
  `details`    TEXT            DEFAULT NULL,
  `ip_address` VARCHAR(45)     DEFAULT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_al_user` (`user_id`),
  INDEX `idx_al_created` (`created_at`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. Seed roles
-- ============================================================
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Admin',    'Full system access'),
(2, 'Manager',  'Management-level access'),
(3, 'Staff',    'Operational access'),
(4, 'User',     'Customer access');

-- ============================================================
-- 6. Seed permissions
-- ============================================================
INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1,  'view_dashboard',       'View dashboard'),
(2,  'manage_users',         'Manage user accounts'),
(3,  'create_user',          'Create new users'),
(4,  'edit_user',            'Edit user details'),
(5,  'delete_user',          'Delete user accounts'),
(6,  'change_user_role',     'Change user roles'),
(7,  'manage_products',      'Manage products (full)'),
(8,  'create_product',       'Create new products'),
(9,  'edit_product',         'Edit existing products'),
(10, 'delete_product',       'Delete products'),
(11, 'manage_categories',    'Manage categories'),
(12, 'manage_orders',        'Manage orders'),
(13, 'view_orders',          'View orders'),
(14, 'update_order_status',  'Update order status'),
(15, 'manage_customers',     'Manage customers'),
(16, 'manage_inventory',     'Manage inventory'),
(17, 'manage_coupons',       'Manage coupons and discounts'),
(18, 'view_reports',         'View sales reports'),
(19, 'view_analytics',       'View analytics'),
(20, 'manage_settings',      'Manage website settings'),
(21, 'view_activity_logs',   'View activity logs'),
(22, 'manage_banners',       'Manage banners and sliders'),
(23, 'manage_brands',        'Manage brands'),
(24, 'manage_contacts',      'Manage contact messages'),
(25, 'manage_support',       'Manage support tickets');

-- ============================================================
-- 7. Admin permissions (all)
-- ============================================================
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions`;

-- ============================================================
-- 8. Manager permissions
-- ============================================================
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, `id` FROM `permissions` WHERE `name` IN (
  'view_dashboard',
  'manage_products', 'create_product', 'edit_product',
  'manage_categories',
  'manage_orders', 'view_orders', 'update_order_status',
  'manage_customers',
  'manage_inventory',
  'manage_coupons',
  'view_reports', 'view_analytics',
  'manage_banners', 'manage_brands',
  'manage_contacts', 'manage_support'
);

-- ============================================================
-- 9. Staff permissions
-- ============================================================
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 3, `id` FROM `permissions` WHERE `name` IN (
  'view_dashboard',
  'view_orders', 'update_order_status',
  'manage_inventory',
  'manage_contacts', 'manage_support'
);

-- ============================================================
-- 10. User (customer) permissions
-- ============================================================
-- Users have no admin permissions (they access the frontend only)

-- ============================================================
-- 11. Convert users.role ENUM to users.role_id INT
-- ============================================================

-- First, add role_id column with default 4 (User)
ALTER TABLE `users` ADD COLUMN `role_id` INT UNSIGNED NOT NULL DEFAULT 4 AFTER `password`;

-- Migrate existing data
UPDATE `users` SET `role_id` = 1 WHERE `role` = 'admin';
UPDATE `users` SET `role_id` = 4 WHERE `role` = 'customer';

-- Drop the old ENUM column
ALTER TABLE `users` DROP COLUMN `role`;

-- Add index for role_id
ALTER TABLE `users` ADD INDEX `idx_users_role` (`role_id`);

SET FOREIGN_KEY_CHECKS = 1;
