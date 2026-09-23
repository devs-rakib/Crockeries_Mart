<?php
// Vercel rewrites every request here. Dispatch AJAX (.php) requests separately;
// everything else boots the front controller.
$__uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
if (preg_match('#(^|/)(public/)?ajax_handler\.php$#', $__uri)) {
    require_once __DIR__ . '/../public/ajax_handler.php';
    exit;
}

require_once __DIR__ . '/../public/index.php';