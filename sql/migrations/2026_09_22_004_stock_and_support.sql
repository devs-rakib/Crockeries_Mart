-- Stock History Table
CREATE TABLE IF NOT EXISTS `stock_history` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `type`       ENUM('in','out') NOT NULL,
    `quantity`   INT NOT NULL,
    `note`       VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_sh_product` (`product_id`),
    INDEX `idx_sh_type` (`type`),
    INDEX `idx_sh_date` (`created_at`),
    CONSTRAINT `fk_sh_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Support Tickets Table
CREATE TABLE IF NOT EXISTS `support_tickets` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED DEFAULT NULL,
    `name`        VARCHAR(100) NOT NULL,
    `email`       VARCHAR(150) NOT NULL,
    `phone`       VARCHAR(20) DEFAULT NULL,
    `subject`     VARCHAR(255) NOT NULL,
    `category`    VARCHAR(50) DEFAULT 'general',
    `message`     TEXT NOT NULL,
    `status`      ENUM('open','replied','closed') NOT NULL DEFAULT 'open',
    `priority`    ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    `order_number` VARCHAR(20) DEFAULT NULL,
    `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_st_user` (`user_id`),
    INDEX `idx_st_status` (`status`),
    INDEX `idx_st_email` (`email`),
    CONSTRAINT `fk_st_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Support Replies Table
CREATE TABLE IF NOT EXISTS `support_replies` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ticket_id` INT UNSIGNED NOT NULL,
    `sender`    ENUM('customer','admin') NOT NULL,
    `sender_name` VARCHAR(100) DEFAULT NULL,
    `message`   TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_sr_ticket` (`ticket_id`),
    CONSTRAINT `fk_sr_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
