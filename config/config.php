<?php
$dotenv = \Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->safeLoad();

define('APP_NAME', 'CrokersesMart');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost/CrokersesMart/public');
define('ADMIN_URL', APP_URL . '/admin/');
define('UPLOAD_PATH', APP_ROOT . '/public/uploads');
define('CACHE_PATH', APP_ROOT . '/storage/cache');
define('CACHE_TTL', 3600);

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'crockeriesmart');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com');
define('SMTP_PORT', (int) ($_ENV['SMTP_PORT'] ?? 587));
define('SMTP_USER', $_ENV['SMTP_USER'] ?? '');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? '');
define('SMTP_ENCRYPTION', $_ENV['SMTP_ENCRYPTION'] ?? 'tls');
define('SMTP_FROM', $_ENV['SMTP_FROM'] ?? 'noreply@crockeriesmart.com');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'CrokersesMart');

define('PASSWORD_RESET_EXPIRY_MINUTES', 30);

define('SSLCOMMERZ_STORE_ID', $_ENV['SSLCOMMERZ_STORE_ID'] ?? '');
define('SSLCOMMERZ_STORE_PASS', $_ENV['SSLCOMMERZ_STORE_PASS'] ?? '');
define('SSLCOMMERZ_SANDBOX', ($_ENV['SSLCOMMERZ_SANDBOX'] ?? 'true') === 'true');

define('ITEMS_PER_PAGE', 12);
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('SHIPPING_INSIDE_DHAKA', 60);
define('SHIPPING_OUTSIDE_DHAKA', 120);

define('OTP_EXPIRY_MINUTES', 5);
define('OTP_LENGTH', 6);

define('SITE_NAME', 'CrokersesMart');
define('SITE_PHONE', '+880 1XXX-XXXXXX');
define('SITE_EMAIL', 'info@crockeriesmart.com');
define('SITE_ADDRESS', 'Dhaka, Bangladesh');
define('SITE_FACEBOOK', '#');
define('SITE_YOUTUBE', '#');
define('SITE_INSTAGRAM', '#');
