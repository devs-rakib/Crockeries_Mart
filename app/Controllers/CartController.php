<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Auth;
use App\Helpers\Database;

class CartController
{
    private Product $productModel;
    private Wishlist $wishlistModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->wishlistModel = new Wishlist();
    }

    public function add(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $variantId = (int) ($_POST['variant_id'] ?? 0);

        $product = $this->productModel->getById($productId);
        if (!$product) {
            Response::error('Product not found');
        }

        if ($product['stock_quantity'] < $quantity) {
            Response::error('Insufficient stock');
        }

        $cart = Session::getCart();
        $key = $productId . ($variantId ? "_{$variantId}" : '');

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
            if ($cart[$key]['quantity'] > $product['stock_quantity']) {
                $cart[$key]['quantity'] = $product['stock_quantity'];
            }
        } else {
            $variant = null;
            if ($variantId) {
                $db = Database::getInstance();
                $variant = $db->fetch("SELECT * FROM product_variants WHERE id = ? AND product_id = ?", [$variantId, $productId]);
            }

            $cart[$key] = [
                'product_id'    => $productId,
                'variant_id'    => $variantId,
                'name'          => $product['name'],
                'slug'          => $product['slug'],
                'image'         => $product['main_image'],
                'price'         => $variant ? ($product['price'] + $variant['extra_price']) : $product['price'],
                'discount_price'=> $variant ? ($product['discount_price'] + $variant['extra_price']) : $product['discount_price'],
                'quantity'      => $quantity,
                'stock'         => $product['stock_quantity'],
                'variant_name'  => $variant ? $variant['color_name'] : '',
            ];
        }

        Session::setCart($cart);
        Response::jsonWithCart('Product added to cart');
    }

    public function update(): void
    {
        $key = $_POST['key'] ?? '';
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

        $cart = Session::getCart();
        if (!isset($cart[$key])) {
            Response::error('Item not in cart');
        }

        if ($quantity > $cart[$key]['stock']) {
            $quantity = $cart[$key]['stock'];
        }

        $cart[$key]['quantity'] = $quantity;
        Session::setCart($cart);
        Response::jsonWithCart('Cart updated');
    }

    public function remove(): void
    {
        $key = $_POST['key'] ?? '';
        $cart = Session::getCart();

        if (isset($cart[$key])) {
            unset($cart[$key]);
            Session::setCart($cart);
        }

        Response::jsonWithCart('Item removed from cart');
    }

    public function get(): void
    {
        $cart = Session::getCart();
        $items = [];
        foreach ($cart as $key => $item) {
            $items[] = array_merge($item, ['key' => $key]);
        }

        Response::json([
            'success'     => true,
            'items'       => $items,
            'count'       => Session::getCartCount(),
            'total'       => Session::getCartTotal(),
            'shipping'    => 0,
            'grand_total' => Session::getCartTotal(),
        ]);
    }

    public function toggleWishlist(): void
    {
        if (!Auth::check()) {
            Response::error('Please login first', 401);
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        if (!$productId) {
            Response::error('Invalid product');
        }

        $added = $this->wishlistModel->toggle(Auth::id(), $productId);
        Response::json([
            'success' => true,
            'added'   => $added,
            'message' => $added ? 'Added to wishlist' : 'Removed from wishlist',
            'count'   => $this->wishlistModel->getCount(Auth::id()),
        ]);
    }

    public function wishlistCount(): void
    {
        $count = Auth::check() ? $this->wishlistModel->getCount(Auth::id()) : 0;
        Response::json(['success' => true, 'count' => $count]);
    }

    public function quickBuy(): void
    {
        $this->add();
        Response::json(['success' => true, 'redirect' => APP_URL . '/checkout']);
    }
}
