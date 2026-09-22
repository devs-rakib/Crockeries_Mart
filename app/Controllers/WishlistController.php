<?php
namespace App\Controllers;

use App\Models\Wishlist;
use App\Helpers\Auth;

class WishlistController
{
    private Wishlist $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new Wishlist();
    }

    public function index(): void
    {
        Auth::requireLogin();

        $items = $this->wishlistModel->getItems(Auth::id());

        $data = [
            'pageTitle' => 'My Wishlist',
            'items'     => $items,
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/wishlist.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }
}
