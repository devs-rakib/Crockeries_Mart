<?php
// Vercel filesystem is read-only except /tmp — sessions must go there
if (getenv('VERCEL') !== false && getenv('VERCEL') !== '') {
    ini_set('session.save_path', sys_get_temp_dir());
}
session_start();

// Absolute path loader to prevent working directory issues on Vercel
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/config/config.php';
require APP_ROOT . '/config/autoload.php';

use App\Helpers\Session;
use App\Helpers\Cache;

Session::init();
Cache::init();

if (!isset($_GET['url'])) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $uriPath = preg_replace('#^/?(public/index\.php|index\.php)?#', '', $uriPath);
    $_GET['url'] = ltrim($uriPath, '/');
}
$url = trim($_GET['url'] ?? '', '/');
$url = rtrim($url, '/');
$url = $url === '' ? 'home' : $url;
$urlParts = explode('/', $url);

/* ── Route Map ── */
$routeMap = [
    'login'              => ['controller' => 'Auth', 'action' => 'login'],
    'register'           => ['controller' => 'Auth', 'action' => 'register'],
    'logout'             => ['controller' => 'Auth', 'action' => 'logout'],
    'forgot-password'    => ['controller' => 'ForgotPassword', 'action' => 'showForm'],
    'reset-password'     => ['controller' => 'ForgotPassword', 'action' => 'showResetForm'],
    'api/auth/send-otp'  => ['controller' => 'Auth', 'action' => 'sendOtp'],
    'api/auth/verify-otp'=> ['controller' => 'Auth', 'action' => 'verifyOtp'],
    'api/user/profile'   => ['controller' => 'Auth', 'action' => 'me'],
    'about'              => ['controller' => 'Pages', 'action' => 'about'],
    'pages/return-policy'=> ['controller' => 'Pages', 'action' => 'returnPolicy'],
    'pages/shipping-info'=> ['controller' => 'Pages', 'action' => 'shippingInfo'],
    'pages/faq'          => ['controller' => 'Pages', 'action' => 'faq'],
    'pages/terms'        => ['controller' => 'Pages', 'action' => 'terms'],
    'account/profile'          => ['controller' => 'Pages', 'action' => 'myAccount'],
    'account/update-profile'   => ['controller' => 'Pages', 'action' => 'updateProfile'],
    'account/change-password'  => ['controller' => 'Pages', 'action' => 'changePassword'],
    'account/upload-avatar'    => ['controller' => 'Pages', 'action' => 'uploadAvatar'],
    'order/track'        => ['controller' => 'Pages', 'action' => 'orderTracking'],
    'shop'               => ['controller' => 'Product', 'action' => 'shop'],
    'product'            => ['controller' => 'Product', 'action' => 'detail'],
    'search'             => ['controller' => 'Product', 'action' => 'search'],
    'filter'             => ['controller' => 'Product', 'action' => 'filter'],
    'cart/add'           => ['controller' => 'Cart', 'action' => 'add'],
    'cart/update'        => ['controller' => 'Cart', 'action' => 'update'],
    'cart/remove'        => ['controller' => 'Cart', 'action' => 'remove'],
    'cart/get'           => ['controller' => 'Cart', 'action' => 'get'],
    'checkout'           => ['controller' => 'Checkout', 'action' => 'index'],
    'checkout/place'     => ['controller' => 'Checkout', 'action' => 'place'],
    'order-success'      => ['controller' => 'Checkout', 'action' => 'success'],
    'payment/initiate'   => ['controller' => 'Payment', 'action' => 'initiate'],
    'payment/success'    => ['controller' => 'Payment', 'action' => 'success'],
    'payment/fail'       => ['controller' => 'Payment', 'action' => 'fail'],
    'payment/cancel'     => ['controller' => 'Payment', 'action' => 'cancel'],
    'payment/ipn'        => ['controller' => 'Payment', 'action' => 'ipn'],
    'my-orders'          => ['controller' => 'Order', 'action' => 'myOrders'],
    'track-order'        => ['controller' => 'Order', 'action' => 'track'],
    'wishlist'           => ['controller' => 'Wishlist', 'action' => 'index'],
    'support'            => ['controller' => 'Support', 'action' => 'index'],
    'support/submit'     => ['controller' => 'Support', 'action' => 'submit'],
    'support/ticket'     => ['controller' => 'Support', 'action' => 'view'],
    'support/ticket/reply' => ['controller' => 'Support', 'action' => 'reply'],
    'offer'              => ['controller' => 'Product', 'action' => 'shop'],
    'category'           => ['controller' => 'Product', 'action' => 'shop'],
];

if ($urlParts[0] === 'admin') {
    require APP_ROOT . '/app/Helpers/Auth.php';
    \App\Helpers\Auth::requireRole(\App\Helpers\Auth::ROLE_ADMIN, \App\Helpers\Auth::ROLE_MANAGER, \App\Helpers\Auth::ROLE_STAFF);

    $adminSegment = $urlParts[1] ?? '';

    $adminRouteMap = [
        'dashboard'     => 'Dashboard',
        'products'      => 'Product',
        'product'       => 'Product',
        'categories'    => 'Category',
        'category'      => 'Category',
        'brands'        => 'Brand',
        'brand'         => 'Brand',
        'banners'       => 'Banner',
        'banner'        => 'Banner',
        'orders'        => 'Order',
        'order'         => 'Order',
        'users'         => 'User',
        'user'          => 'User',
        'contacts'      => 'Contact',
        'contact'       => 'Contact',
        'support'       => 'Support',
        'settings'      => 'Settings',
        'activity-logs' => 'ActivityLog',
    ];

    if ($adminSegment === '' || $adminSegment === 'logout') {
        if ($adminSegment === 'logout') {
            \App\Helpers\Auth::logout();
            \App\Helpers\Response::redirect(APP_URL . '/login');
            return;
        }
        $controllerName = 'DashboardController';
        $action = 'index';
    } elseif (isset($adminRouteMap[$adminSegment])) {
        $controllerName = $adminRouteMap[$adminSegment] . 'Controller';
        $action = $urlParts[2] ?? 'index';
    } else {
        $controllerName = ucfirst($adminSegment) . 'Controller';
        $action = $urlParts[2] ?? 'index';
    }

    $params = array_slice($urlParts, 3);

    $controllerFile = APP_ROOT . '/app/Controllers/admin/' . $controllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $fqcn = 'App\\Controllers\\admin\\' . $controllerName;
        $controller = new $fqcn();
        if (method_exists($controller, $action)) {
            call_user_func_array([$controller, $action], $params);
        } else {
            http_response_code(404);
            require APP_ROOT . '/views/404.php';
        }
    } else {
        http_response_code(404);
        require APP_ROOT . '/views/404.php';
    }
} else {
    $routeKey = $url;
    if (isset($routeMap[$routeKey])) {
        $controllerName = $routeMap[$routeKey]['controller'] . 'Controller';
        $action = $routeMap[$routeKey]['action'];
        $params = array_slice($urlParts, 1);
    } elseif (isset($urlParts[1]) && isset($routeMap[$urlParts[0]])) {
        $controllerName = $routeMap[$urlParts[0]]['controller'] . 'Controller';
        $action = $routeMap[$urlParts[0]]['action'];
        $params = array_slice($urlParts, 1);
    } else {
        $controllerName = ucfirst($urlParts[0]) . 'Controller';
        $action = $urlParts[1] ?? 'index';
        $params = array_slice($urlParts, 2);
    }

    $controllerFile = APP_ROOT . '/app/Controllers/' . $controllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $fqcn = 'App\\Controllers\\' . $controllerName;
        $controller = new $fqcn();
        if (method_exists($controller, $action)) {
            call_user_func_array([$controller, $action], $params);
        } else {
            http_response_code(404);
            require APP_ROOT . '/views/404.php';
        }
    } else {
        http_response_code(404);
        require APP_ROOT . '/views/404.php';
    }
}
