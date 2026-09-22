<?php
session_start();

define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';
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

    case 'submit_review':
        if (!isset($_SESSION['user_id'])) {
            Response::json(['success' => false, 'message' => 'Please login first'], 401);
        }
        require APP_ROOT . '/app/Models/Review.php';
        $reviewModel = new App\Models\Review();
        $productId = (int) ($_POST['product_id'] ?? 0);
        $rating = max(1, min(5, (int) ($_POST['rating'] ?? 0)));
        $title = trim($_POST['title'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        if (!$productId || $rating < 1 || empty($title)) {
            Response::json(['success' => false, 'message' => 'Please fill all required fields']);
        }
        $reviewModel->create([
            'user_id'    => $_SESSION['user_id'],
            'product_id' => $productId,
            'rating'     => $rating,
            'title'      => $title,
            'comment'    => $comment,
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        Response::json(['success' => true, 'message' => 'Review submitted successfully!']);
        break;

    default:
        Response::json(['error' => 'Invalid action'], 400);
        break;
}
