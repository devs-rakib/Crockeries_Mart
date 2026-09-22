-- ============================================================
-- CrockeriesMart E-Commerce Platform - MySQL 8.0 Schema
-- Engine: InnoDB | Charset: utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS `crockeriesmart`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `crockeriesmart`;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. users
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)    NOT NULL,
  `email`      VARCHAR(150)    DEFAULT NULL,
  `phone`      VARCHAR(20)     NOT NULL,
  `password`   VARCHAR(255)    NOT NULL,
  `role`       ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  `status`     TINYINT         NOT NULL DEFAULT 1,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_phone` (`phone`),
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. categories (self-referencing parent_id)
-- ============================================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id`   INT UNSIGNED DEFAULT NULL,
  `name`        VARCHAR(150) NOT NULL,
  `slug`        VARCHAR(150) NOT NULL,
  `icon_class`  VARCHAR(100) DEFAULT NULL,
  `image`       VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT      NOT NULL DEFAULT 0,
  `status`      TINYINT      NOT NULL DEFAULT 1,
  `position`    INT          NOT NULL DEFAULT 0,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categories_slug` (`slug`),
  INDEX `idx_categories_parent` (`parent_id`),
  INDEX `idx_categories_featured` (`is_featured`),
  INDEX `idx_categories_status` (`status`),
  INDEX `idx_categories_position` (`position`),
  CONSTRAINT `fk_categories_parent`
    FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. brands
-- ============================================================
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`   VARCHAR(150) NOT NULL,
  `slug`   VARCHAR(150) NOT NULL,
  `logo`   VARCHAR(255) DEFAULT NULL,
  `status` TINYINT      NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_brands_slug` (`slug`),
  INDEX `idx_brands_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. products
-- ============================================================
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`       INT UNSIGNED    NOT NULL,
  `brand_id`          INT UNSIGNED    DEFAULT NULL,
  `name`              VARCHAR(255)    NOT NULL,
  `slug`              VARCHAR(255)    NOT NULL,
  `sku`               VARCHAR(100)    NOT NULL,
  `short_description` TEXT            DEFAULT NULL,
  `long_description`  LONGTEXT        DEFAULT NULL,
  `main_image`        VARCHAR(255)    DEFAULT NULL,
  `price`             DECIMAL(10,2)   NOT NULL,
  `discount_price`    DECIMAL(10,2)   DEFAULT NULL,
  `stock_quantity`    INT             NOT NULL DEFAULT 0,
  `is_featured`       TINYINT         NOT NULL DEFAULT 0,
  `is_offer`          TINYINT         NOT NULL DEFAULT 0,
  `views_count`       INT             NOT NULL DEFAULT 0,
  `status`            TINYINT         NOT NULL DEFAULT 1,
  `created_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_products_slug` (`slug`),
  UNIQUE KEY `uk_products_sku` (`sku`),
  INDEX `idx_products_category` (`category_id`),
  INDEX `idx_products_brand` (`brand_id`),
  INDEX `idx_products_featured` (`is_featured`),
  INDEX `idx_products_offer` (`is_offer`),
  INDEX `idx_products_status` (`status`),
  INDEX `idx_products_created` (`created_at`),
  INDEX `idx_products_price` (`price`),
  INDEX `idx_filter` (`category_id`, `status`, `price`),
  CONSTRAINT `fk_products_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_products_brand`
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. product_images
-- ============================================================
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255)    NOT NULL,
  `sort_order` INT             NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_product_images_product` (`product_id`),
  CONSTRAINT `fk_product_images_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. product_variants
-- ============================================================
DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `color_name` VARCHAR(50)     NOT NULL,
  `color_code` VARCHAR(7)      NOT NULL,
  `extra_price` DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  INDEX `idx_product_variants_product` (`product_id`),
  CONSTRAINT `fk_product_variants_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. orders
-- ============================================================
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number`    VARCHAR(20)     NOT NULL,
  `user_id`         BIGINT UNSIGNED DEFAULT NULL,
  `customer_name`   VARCHAR(150)    NOT NULL,
  `customer_phone`  VARCHAR(20)     NOT NULL,
  `customer_email`  VARCHAR(150)    DEFAULT NULL,
  `shipping_address` TEXT           NOT NULL,
  `total_amount`    DECIMAL(10,2)   NOT NULL,
  `shipping_cost`   DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `payment_method`  VARCHAR(50)     NOT NULL,
  `payment_status`  ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_id`  VARCHAR(100)    DEFAULT NULL,
  `order_status`    ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_orders_number` (`order_number`),
  UNIQUE KEY `uk_orders_transaction` (`transaction_id`),
  INDEX `idx_orders_user` (`user_id`),
  INDEX `idx_orders_customer_phone` (`customer_phone`),
  INDEX `idx_orders_status` (`order_status`),
  INDEX `idx_orders_payment_status` (`payment_status`),
  INDEX `idx_orders_created` (`created_at`),
  CONSTRAINT `fk_orders_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. order_items
-- ============================================================
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`   BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity`   INT             NOT NULL,
  `unit_price` DECIMAL(10,2)   NOT NULL,
  `subtotal`   DECIMAL(10,2)   NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_order_items_order` (`order_id`),
  INDEX `idx_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. order_status_history
-- ============================================================
DROP TABLE IF EXISTS `order_status_history`;
CREATE TABLE `order_status_history` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`   BIGINT UNSIGNED NOT NULL,
  `status`     VARCHAR(50)     NOT NULL,
  `note`       TEXT            DEFAULT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_osh_order` (`order_id`),
  CONSTRAINT `fk_osh_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. wishlists
-- ============================================================
DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE `wishlists` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wishlists_user_product` (`user_id`, `product_id`),
  INDEX `idx_wishlists_product` (`product_id`),
  CONSTRAINT `fk_wishlists_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wishlists_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. reviews
-- ============================================================
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `rating`     TINYINT         NOT NULL,
  `comment`    TEXT            DEFAULT NULL,
  `status`     TINYINT         NOT NULL DEFAULT 1,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_reviews_product` (`product_id`),
  INDEX `idx_reviews_user` (`user_id`),
  CONSTRAINT `fk_reviews_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. banners_sliders
-- ============================================================
DROP TABLE IF EXISTS `banners_sliders`;
CREATE TABLE `banners_sliders` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(255) NOT NULL,
  `image`       VARCHAR(255) NOT NULL,
  `link`        VARCHAR(500) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `button_text` VARCHAR(100) DEFAULT 'Shop Now',
  `type`        ENUM('hero_slider','middle_banner','sidebar_banner') NOT NULL,
  `position`    INT          NOT NULL DEFAULT 0,
  `status`      TINYINT      NOT NULL DEFAULT 1,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_banners_type` (`type`),
  INDEX `idx_banners_status` (`status`),
  INDEX `idx_banners_position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. coupons
-- ============================================================
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`          VARCHAR(50)  NOT NULL,
  `discount_type` ENUM('percentage','fixed') NOT NULL,
  `discount_value` DECIMAL(10,2) NOT NULL,
  `min_order`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_uses`      INT          NOT NULL DEFAULT 0,
  `used_count`    INT          NOT NULL DEFAULT 0,
  `expires_at`    DATE         NOT NULL,
  `status`        TINYINT      NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_coupons_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. newsletter_subscribers
-- ============================================================
DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(150) NOT NULL,
  `status`     TINYINT      NOT NULL DEFAULT 1,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_newsletter_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. site_settings
-- ============================================================
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `setting_key`   VARCHAR(100)  NOT NULL,
  `setting_value` TEXT          DEFAULT NULL,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. contacts
-- ============================================================
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)  NOT NULL,
  `email`      VARCHAR(150)  NOT NULL,
  `phone`      VARCHAR(20)   DEFAULT NULL,
  `subject`    VARCHAR(255)  NOT NULL,
  `message`    TEXT          NOT NULL,
  `status`     TINYINT       NOT NULL DEFAULT 0,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_contacts_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. otp_tokens
-- ============================================================
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

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA
-- ============================================================

-- ------------------------------------------------------------
-- Admin user  (password: admin123 — bcrypt hash)
-- Generated via: SELECT PASSWORD('admin123') equivalent
-- $2y$10$  hash for "admin123"
-- ------------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `status`)
VALUES
('Admin', 'admin@crockeriesmart.com', '+8801712345678',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin', 1);

-- Sample customer
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `status`)
VALUES
('Rahman Ahmed', 'rahman@example.com', '+8801812345679',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'customer', 1);

-- ------------------------------------------------------------
-- Categories (8-10)
-- ------------------------------------------------------------
INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `icon_class`, `image`, `is_featured`, `status`, `position`)
VALUES
(1,  NULL, 'Dinner Sets',        'dinner-sets',        'bi bi-egg-fried',          NULL, 1, 1, 1),
(2,  NULL, 'Tea & Coffee Sets',  'tea-coffee-sets',    'bi bi-cup-hot',            NULL, 1, 1, 2),
(3,  NULL, 'Bowls & Plates',     'bowls-plates',       'bi bi-record-circle',      NULL, 1, 1, 3),
(4,  NULL, 'Bakeware',           'bakeware',           'bi bi-cake',               NULL, 1, 1, 4),
(5,  NULL, 'Serveware',          'serveware',          'bi bi-hand-index-thumb',   NULL, 1, 1, 5),
(6,  NULL, 'Storage & Jars',     'storage-jars',       'bi bi-box-seam',           NULL, 0, 1, 6),
(7,  NULL, 'Glassware',          'glassware',          'bi bi-cup-straw',          NULL, 1, 1, 7),
(8,  NULL, 'Kitchen Accessories', 'kitchen-accessories','bi bi-gear-wide-connected',NULL, 0, 1, 8),
(9,  1,    'Bone China Sets',    'bone-china-sets',    NULL,                       NULL, 0, 1, 1),
(10, 1,    'Melamine Sets',      'melamine-sets',      NULL,                       NULL, 0, 1, 2);

-- ------------------------------------------------------------
-- Brands (5)
-- ------------------------------------------------------------
INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `status`)
VALUES
(1, 'Prestige',        'prestige',         'images/brands/prestige.svg',         1),
  (2, 'Miyako',          'miyako',           'images/brands/miyako.svg',           1),
  (3, 'Walton',          'walton',           'images/brands/walton.svg',           1),
  (4, 'Sonali',          'sonali',           'images/brands/sonali.svg',           1),
  (5, 'Fine Ceramics',   'fine-ceramics',    'images/brands/fine-ceramics.svg',    1);

-- ------------------------------------------------------------
-- Products (20) — prices in BDT
-- ------------------------------------------------------------
INSERT INTO `products`
(`id`,`category_id`,`brand_id`,`name`,`slug`,`sku`,`short_description`,`long_description`,`main_image`,`price`,`discount_price`,`stock_quantity`,`is_featured`,`is_offer`,`views_count`,`status`)
VALUES
( 1,  1,  1, 'Royal Bone China Dinner Set 28pc',          'royal-bone-china-dinner-set-28pc',          'DC-001', 'Premium bone china dinner set with gold trim, service for 6.',                   'Elegant 28-piece bone china dinner set featuring a delicate gold rim pattern. Perfect for special occasions. Includes dinner plates, side plates, soup bowls, and serving bowls. Dishwasher safe.',                                    'images/products/royal-bone-china-28pc.jpg',          8500.00,  7499.00,  25, 1, 0,  342, 1),
( 2,  1,  5, 'Classic Melamine Dinner Set 30pc',          'classic-melamine-dinner-set-30pc',          'DC-002', 'Durable melamine dinner set, ideal for daily family use.',                       'Break-resistant melamine dinner set in elegant floral design. BPA free and food safe. Complete set for 6 persons. Microwave safe for reheating.',                                                                          'images/products/melamine-dinner-30pc.jpg',          4200.00,  3800.00,  50, 1, 1,  528, 1),
( 3,  2,  2, 'Miyako Porcelain Tea Set 15pc',             'miyako-porcelain-tea-set-15pc',             'TC-001', 'Beautiful porcelain tea set with teapot, cups, and saucers.',                     'Exquisite 15-piece porcelain tea set featuring hand-painted floral motifs. Includes teapot, 6 cups, 6 saucers, sugar bowl, and creamer.',                                                                                  'images/products/miyako-tea-set-15pc.jpg',            3200.00,  NULL,      40, 1, 0,  215, 1),
( 4,  2,  4, 'Sonali Stainless Steel Kettle 2L',          'sonali-stainless-steel-kettle-2l',          'TC-002', 'Double-wall insulated kettle keeps tea hot for hours.',                           'Premium 304 stainless steel kettle with double-wall vacuum insulation. 2-litre capacity. Cool-touch handle. Suitable for all stovetops.',                                                                                  'images/products/sonali-kettle-2l.jpg',              1800.00,  1599.00,  60, 0, 1,  183, 1),
( 5,  3,  5, 'Fine Ceramic Bowl Set 6pc',                 'fine-ceramic-bowl-set-6pc',                 'BP-001', 'Handcrafted ceramic bowls, perfect for soups and cereals.',                      'Set of 6 handmade ceramic bowls in assorted earth tones. Each bowl holds 500ml. Microwave and dishwasher safe.',                                                                                                    'images/products/ceramic-bowls-6pc.jpg',             1600.00,  NULL,      80, 0, 0,  109, 1),
( 6,  3,  1, 'Prestige Dinner Plates 6pc',                'prestige-dinner-plates-6pc',                'BP-002', 'Sleek white porcelain plates with subtle rim design.',                            'Set of 6 white porcelain dinner plates (27cm). Chip-resistant glaze. Stackable design for easy storage.',                                                                                                               'images/products/prestige-plates-6pc.jpg',           2100.00,  1899.00,  45, 1, 1,  267, 1),
( 7,  4,  3, 'Walton Non-Stick Baking Tray 3pc',          'walton-non-stick-baking-tray-3pc',          'BK-001', 'Heavy-duty non-stick baking trays, oven safe up to 230°C.',                      'Professional-grade carbon steel baking tray set with 3-layer non-stick coating. Includes 3 sizes: 35cm, 30cm, and 25cm. PFOA free.',                                                                                     'images/products/walton-baking-tray-3pc.jpg',        1900.00,  NULL,      35, 0, 0,  88,  1),
( 8,  4,  2, 'Miyako Silicone Mould Set 4pc',             'miyako-silicone-mould-set-4pc',             'BK-002', 'Food-grade silicone moulds for cakes, muffins, and bread.',                       'Set of 4 silicone moulds: loaf pan, round cake pan, muffin tray (6-cup), and square pan. Temperature range -40°C to 230°C. Non-stick and flexible.',                                                                     'images/products/miyako-silicone-moulds.jpg',        1200.00,  999.00,   70, 0, 1,  156, 1),
( 9,  5,  1, 'Prestige Serving Platter Oval',             'prestige-serving-platter-oval',             'SW-001', 'Elegant oval platter for roasts and appetizers.',                                'Large oval serving platter (38cm x 26cm) in high-gloss white porcelain. Raised edge for easy carrying.',                                                                                                                'images/products/prestige-platter-oval.jpg',         2400.00,  NULL,      30, 1, 0,  195, 1),
(10,  5,  4, 'Sonali Stainless Steel Curry Server',       'sonali-stainless-steel-curry-server',       'SW-002', 'Insulated curry server to keep food warm at the table.',                         'Double-wall insulated stainless steel curry server (1.5L). Twist-lock lid for spill-free transport. Mirror-polished finish.',                                                                                           'images/products/sonali-curry-server.jpg',           2200.00,  1999.00,  40, 0, 1,  142, 1),
(11,  6,  5, 'Fine Ceramic Cookie Jar 1.5L',              'fine-ceramic-cookie-jar-15l',               'SJ-001', 'Airtight ceramic jar for cookies, tea, or sugar storage.',                       'Handcrafted ceramic storage jar with silicone-seal lid. 1.5-litre capacity. Rustic farmhouse finish.',                                                                                                                   'images/products/ceramic-cookie-jar.jpg',           1100.00,  NULL,      55, 0, 0,  77,  1),
(12,  6,  3, 'Walton Airtight Container Set 4pc',         'walton-airtight-container-set-4pc',         'SJ-002', 'BPA-free plastic containers with snap-lock lids.',                               'Set of 4 graduated airtight containers (500ml, 1L, 1.5L, 2L). Transparent body for easy content identification. Stackable design.',                                                                                    'images/products/walton-containers-4pc.jpg',          900.00,  799.00,   90, 0, 1,  120, 1),
(13,  7,  2, 'Miyako Crystal Wine Glass Set 6pc',         'miyako-crystal-wine-glass-set-6pc',         'GW-001', 'Lead-free crystal wine glasses, elegant stemware.',                              'Set of 6 lead-free crystal red wine glasses (650ml). Classic balloon bowl design for enhanced aeration. Dishwasher safe.',                                                                                              'images/products/miyako-wine-glasses-6pc.jpg',       2800.00,  2499.00,  30, 1, 1,  198, 1),
(14,  7,  1, 'Prestige Highball Glass Set 8pc',           'prestige-highball-glass-set-8pc',           'GW-002', 'Durable soda-lime glass tumblers for everyday use.',                             'Set of 8 highball glasses (350ml). Thick base for stability. Suitable for water, juice, and cocktails.',                                                                                                               'images/products/prestige-highball-8pc.jpg',         1400.00,  NULL,      65, 0, 0,  96,  1),
(15,  8,  3, 'Walton Mortar & Pestle Marble 6"',          'walton-mortar-pestle-marble-6',             'KA-001', 'Solid marble mortar and pestle for grinding spices.',                             'Hand-carved solid marble mortar and pestle. 6-inch diameter. Non-porous surface prevents flavour absorption. Weight: 2.5kg.',                                                                                           'images/products/walton-mortar-pestle.jpg',           1500.00,  1299.00,  45, 0, 1,  134, 1),
(16,  8,  4, 'Sonali Wooden Chopping Board Large',        'sonali-wooden-chopping-board-large',        'KA-002', 'Natural acacia wood chopping board, 45cm x 30cm.',                               'Solid acacia wood chopping board with juice groove. Naturally antimicrobial. Oil-finished for longevity. Dimensions: 45cm x 30cm x 2cm.',                                                                                'images/products/sonali-chopping-board.jpg',         1300.00,  NULL,      50, 0, 0,  111, 1),
(17,  1,  5, 'Fine Ceramics Stoneware Set 16pc',          'fine-ceramics-stoneware-set-16pc',          'DC-003', 'Rustic stoneware set, service for 4.',                                            'Artisan stoneware 16-piece set in speckled glaze. Includes dinner plates, side plates, bowls, and mugs. Each piece is uniquely crafted.',                                                                                'images/products/stoneware-set-16pc.jpg',            5500.00,  4999.00,  20, 1, 0,  275, 1),
(18,  3,  2, 'Miyako Dessert Plates 8pc',                'miyako-dessert-plates-8pc',                 'BP-003', 'Colourful dessert plates with scalloped edges.',                                 'Set of 8 dessert/appetizer plates (20cm) in pastel colours. Scalloped rim. Perfect for high tea and dessert service.',                                                                                                  'images/products/miyako-dessert-plates-8pc.jpg',     1800.00,  NULL,      55, 0, 0,  144, 1),
(19,  5,  1, 'Prestige Gravy Boat',                       'prestige-gravy-boat',                       'SW-003', 'Porcelain gravy boat with underplate, 700ml capacity.',                          'Classic porcelain gravy boat with matching underplate. 700ml capacity. Easy-pour spout and comfortable handle.',                                                                                                       'images/products/prestige-gravy-boat.jpg',           1600.00,  1399.00,  35, 0, 1,  89,  1),
(20,  7,  5, 'Fine Ceramics Beer Mug Set 4pc',            'fine-ceramics-beer-mug-set-4pc',            'GW-003', 'Heavy stoneware beer mugs, 500ml each.',                                          'Set of 4 stoneware beer mugs with handles. 500ml capacity each. Glazed interior, matte exterior. Dishwasher safe.',                                                                                                    'images/products/stoneware-beer-mugs-4pc.jpg',       2000.00,  NULL,      40, 0, 0,  67,  1);

-- ------------------------------------------------------------
-- Product images (extra gallery images)
-- ------------------------------------------------------------
INSERT INTO `product_images` (`product_id`, `image_path`, `sort_order`)
VALUES
( 1, 'images/products/royal-bone-china-28pc-2.jpg', 1),
( 1, 'images/products/royal-bone-china-28pc-3.jpg', 2),
( 2, 'images/products/melamine-dinner-30pc-2.jpg',  1),
( 6, 'images/products/prestige-plates-6pc-2.jpg',   1),
(13, 'images/products/miyako-wine-glasses-6pc-2.jpg',1),
(17, 'images/products/stoneware-set-16pc-2.jpg',    1);

-- ------------------------------------------------------------
-- Product variants (colours)
-- ------------------------------------------------------------
INSERT INTO `product_variants` (`product_id`, `color_name`, `color_code`, `extra_price`)
VALUES
( 2, 'White',       '#FFFFFF',   0.00),
( 2, 'Cream',       '#FFFDD0',   0.00),
( 2, 'Blue floral', '#4A90D9', 100.00),
( 5, 'Terracotta',  '#C84B31',   0.00),
( 5, 'Forest Green','#2D6A4F',   0.00),
( 6, 'White',       '#FFFFFF',   0.00),
( 6, 'Ivory',       '#FFFFF0',  50.00),
(17, 'Speckled Grey','#B0B0B0',  0.00),
(17, 'Speckled Blue','#7BA7BC', 200.00);

-- ------------------------------------------------------------
-- Banners — Hero Slider (3)
-- ------------------------------------------------------------
INSERT INTO `banners_sliders` (`title`, `image`, `link`, `type`, `position`, `status`)
VALUES
('Summer Sale — Up to 30% Off Dinner Sets',     'images/banners/hero-1.jpg', '/offers/dinner-sets',     'hero_slider', 1, 1),
('New Arrivals — Fine Ceramics Collection',     'images/banners/hero-2.jpg', '/categories/fine-ceramics','hero_slider', 2, 1),
('Free Shipping on Orders Above ৳3000',        'images/banners/hero-3.jpg', '/shipping-policy',        'hero_slider', 3, 1);

-- Middle Banners (2)
INSERT INTO `banners_sliders` (`title`, `image`, `link`, `type`, `position`, `status`)
VALUES
('Premium Tea & Coffee Sets',                    'images/banners/mid-1.jpg', '/categories/tea-coffee-sets','middle_banner', 1, 1),
('Kitchen Accessories — Must Haves',             'images/banners/mid-2.jpg', '/categories/kitchen-accessories','middle_banner', 2, 1);

-- ------------------------------------------------------------
-- Coupons (2)
-- ------------------------------------------------------------
INSERT INTO `coupons` (`code`, `discount_type`, `discount_value`, `min_order`, `max_uses`, `used_count`, `expires_at`, `status`)
VALUES
('WELCOME10',  'percentage', 10.00,  1000.00, 500,  12, '2026-12-31', 1),
('FLAT500',    'fixed',      500.00, 3000.00, 200,  45, '2026-09-30', 1);

-- ------------------------------------------------------------
-- Site Settings
-- ------------------------------------------------------------
INSERT INTO `site_settings` (`setting_key`, `setting_value`)
VALUES
('site_name',        'CrockeriesMart'),
('site_tagline',     'Your One-Stop Crockery Shop'),
('site_email',       'info@crockeriesmart.com'),
('site_phone',       '+8801712345678'),
('site_phone_alt',   '+8801812345679'),
('site_address',     '42 Ring Road, Banani, Dhaka 1213, Bangladesh'),
('site_facebook',    'https://facebook.com/crockeriesmart'),
('site_instagram',   'https://instagram.com/crockeriesmart'),
('site_youtube',     'https://youtube.com/@crockeriesmart'),
('currency',         'BDT'),
('currency_symbol',  '৳'),
('shipping_fee',     '100.00'),
('free_shipping_min','3000.00'),
('tax_rate',         '0.00'),
('meta_title',       'CrockeriesMart — Premium Crockery & Kitchenware Online Bangladesh'),
('meta_description',  'Shop premium dinner sets, tea sets, glassware, and kitchen accessories at CrockeriesMart. Fast delivery across Bangladesh.'),
('logo',             'images/logo.png'),
('favicon',          'images/favicon.ico');

-- ------------------------------------------------------------
-- Sample order for demonstration
-- ------------------------------------------------------------
INSERT INTO `orders` (`order_number`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `shipping_address`, `total_amount`, `shipping_cost`, `payment_method`, `payment_status`, `order_status`)
VALUES
('CM-2026-00001', 2, 'Rahman Ahmed', '+8801812345679', 'rahman@example.com', 'House 12, Road 5, Gulshan-2, Dhaka 1212', 8099.00, 100.00, 'cod', 'paid', 'processing');

INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`)
VALUES
(1, 1,  1, 7499.00, 7499.00),
(1, 15, 1, 1299.00, 1299.00);

INSERT INTO `order_status_history` (`order_id`, `status`, `note`)
VALUES
(1, 'pending',    'Order placed by customer.'),
(1, 'processing', 'Payment confirmed. Preparing for shipment.');

-- ------------------------------------------------------------
-- Sample wishlist items
-- ------------------------------------------------------------
INSERT INTO `wishlists` (`user_id`, `product_id`)
VALUES
(2, 3),
(2, 13),
(2, 17);

-- ------------------------------------------------------------
-- Sample reviews
-- ------------------------------------------------------------
INSERT INTO `reviews` (`user_id`, `product_id`, `rating`, `comment`, `status`)
VALUES
(2, 1, 5, 'Absolutely beautiful dinner set! The gold trim is elegant and the quality is outstanding.',              1),
(2, 15,4, 'Very sturdy and well-made. Weight is perfect for grinding spices. Slightly pricey but worth it.',        1);

-- ------------------------------------------------------------
-- Sample newsletter subscriber
-- ------------------------------------------------------------
INSERT INTO `newsletter_subscribers` (`email`, `status`)
VALUES
('rahman@example.com', 1),
('visitor@example.com', 1);

-- ============================================================
-- END OF SCHEMA
-- ============================================================
