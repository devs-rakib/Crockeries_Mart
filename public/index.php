<?php
session_start();

define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/config/config.php';
require APP_ROOT . '/config/autoload.php';

use App\Helpers\Session;
use App\Helpers\Cache;

Session::init();
Cache::init();

$url = trim($_GET['url'] ?? '', '/');
$url = rtrim($url, '/');
$url = $url === '' ? 'home' : $url;
$urlParts = explode('/', $url);

/* ── Route Map ── */
$routeMap = [
    'login'              => ['controller' => 'Auth', 'action' => 'login'],
    'register'           => ['controller' => 'Auth', 'action' => 'register'],
    'logout'             => ['controller' => 'Auth', 'action' => 'logout'],
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
    'my-orders'          => ['controller' => 'Order', 'action' => 'myOrders'],
    'track-order'        => ['controller' => 'Order', 'action' => 'track'],
    'offer'              => ['controller' => 'Product', 'action' => 'shop'],
    'category'           => ['controller' => 'Product', 'action' => 'shop'],
];

if ($urlParts[0] === 'admin') {
    require APP_ROOT . '/app/Helpers/Auth.php';
    \App\Helpers\Auth::requireAdmin();

    $adminSegment = $urlParts[1] ?? '';

    $adminRouteMap = [
        'dashboard'   => 'Dashboard',
        'products'    => 'Product',
        'product'     => 'Product',
        'categories'  => 'Category',
        'category'    => 'Category',
        'brands'      => 'Brand',
        'brand'       => 'Brand',
        'banners'     => 'Banner',
        'banner'      => 'Banner',
        'orders'      => 'Order',
        'order'       => 'Order',
        'users'       => 'User',
        'user'        => 'User',
        'contacts'    => 'Contact',
        'contact'     => 'Contact',
        'settings'    => 'Settings',
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
