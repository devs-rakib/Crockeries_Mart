<?php
$dotenv = \Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->safeLoad();

$onVercel = getenv('VERCEL') !== false && getenv('VERCEL') !== '';

// On Vercel the filesystem is read-only except /tmp
$tmpBase = $onVercel ? (sys_get_temp_dir() . '/crockeriesmart') : null;

// ── App URL ──
function app_request_base_url(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host === '') {
        return '';
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($serverPort = $_SERVER['SERVER_PORT'] ?? null) == 443)
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    return ($https ? 'https://' : 'http://') . $host;
}

$appUrl = getenv('APP_URL') ?: ($_ENV['APP_URL'] ?? '');
if ($appUrl === '' && $host = ($_SERVER['HTTP_HOST'] ?? '')) {
    $appUrl = app_request_base_url();
}
if ($appUrl === '') {
    $appUrl = 'http://localhost/CrokersesMart/public';
}
$appUrl = rtrim($appUrl, '/');

define('APP_NAME', 'CrokersesMart');
define('APP_URL', $appUrl);
define('ADMIN_URL', APP_URL . '/admin/');
define('UPLOAD_PATH', $tmpBase ? $tmpBase . '/uploads' : APP_ROOT . '/public/uploads');
define('CACHE_PATH', $tmpBase ? $tmpBase . '/cache' : APP_ROOT . '/storage/cache');
define('CACHE_TTL', 3600);
define('ON_VERCEL', $onVercel);

// ── Database ──
define('DB_HOST', getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost'));
define('DB_NAME', getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'crockeriesmart'));
define('DB_USER', getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root'));
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? ''));
define('DB_CHARSET', getenv('DB_CHARSET') ?: ($_ENV['DB_CHARSET'] ?? 'utf8mb4'));

// ── SMTP ──
define('SMTP_HOST', getenv('SMTP_HOST') ?: ($_ENV['SMTP_HOST'] ?? 'smtp.gmail.com'));
define('SMTP_PORT', (int) (getenv('SMTP_PORT') ?: ($_ENV['SMTP_PORT'] ?? 587)));
define('SMTP_USER', getenv('SMTP_USER') ?: ($_ENV['SMTP_USER'] ?? ''));
define('SMTP_PASS', getenv('SMTP_PASS') !== false ? getenv('SMTP_PASS') : ($_ENV['SMTP_PASS'] ?? ''));
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: ($_ENV['SMTP_ENCRYPTION'] ?? 'tls'));
define('SMTP_FROM', getenv('SMTP_FROM') ?: ($_ENV['SMTP_FROM'] ?? 'noreply@crockeriesmart.com'));
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: ($_ENV['SMTP_FROM_NAME'] ?? 'CrokersesMart'));

define('PASSWORD_RESET_EXPIRY_MINUTES', 30);

// ── SSLCommerz ──
define('SSLCOMMERZ_STORE_ID', getenv('SSLCOMMERZ_STORE_ID') ?: ($_ENV['SSLCOMMERZ_STORE_ID'] ?? ''));
define('SSLCOMMERZ_STORE_PASS', getenv('SSLCOMMERZ_STORE_PASS') !== false ? getenv('SSLCOMMERZ_STORE_PASS') : ($_ENV['SSLCOMMERZ_STORE_PASS'] ?? ''));
define('SSLCOMMERZ_SANDBOX', ((getenv('SSLCOMMERZ_SANDBOX') ?: ($_ENV['SSLCOMMERZ_SANDBOX'] ?? 'true')) === 'true'));

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
