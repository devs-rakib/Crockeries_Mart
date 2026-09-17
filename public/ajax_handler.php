<?php
session_start();

define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/config/config.php';
require APP_ROOT . '/config/autoload.php';
require APP_ROOT . '/config/database.php';

use App\Helpers\Session;
use App\Helpers\Response;

Session::init();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add_to_cart':
        require APP_ROOT . '/app/Controllers/CartController.php';
        $cart = new App\Controllers\CartController();
        $cart->add();
        break;

    case 'update_cart':
        require APP_ROOT . '/app/Controllers/CartController.php';
        $cart = new App\Controllers\CartController();
        $cart->update();
        break;

    case 'remove_from_cart':
        require APP_ROOT . '/app/Controllers/CartController.php';
        $cart = new App\Controllers\CartController();
        $cart->remove();
        break;

    case 'get_cart':
        require APP_ROOT . '/app/Controllers/CartController.php';
        $cart = new App\Controllers\CartController();
        $cart->get();
        break;

    case 'live_search':
        require APP_ROOT . '/app/Controllers/ProductController.php';
        $product = new App\Controllers\ProductController();
        $product->search();
        break;

    case 'toggle_wishlist':
        require APP_ROOT . '/app/Controllers/CartController.php';
        $cart = new App\Controllers\CartController();
        $cart->toggleWishlist();
        break;

    case 'get_wishlist_count':
        require APP_ROOT . '/app/Controllers/CartController.php';
        $cart = new App\Controllers\CartController();
        $cart->wishlistCount();
        break;

    case 'shop_filter':
        require APP_ROOT . '/app/Controllers/ProductController.php';
        $product = new App\Controllers\ProductController();
        $product->filter();
        break;

    default:
        Response::json(['error' => 'Invalid action'], 400);
        break;
}
