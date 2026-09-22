<?php
namespace App\Controllers;

use App\Models\Order;
use App\Helpers\Auth;
use App\Helpers\Response;

class OrderController
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function myOrders(): void
    {
        Auth::requireLogin();

        $orders = $this->orderModel->getByUserId(Auth::id());

        $data = [
            'pageTitle' => 'My Orders',
            'orders'    => $orders,
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/my_orders.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function track(string $orderNumber): void
    {
        Auth::requireLogin();

        $order = $this->orderModel->getByNumber($orderNumber);
        if (!$order) {
            http_response_code(404);
            require APP_ROOT . '/views/404.php';
            return;
        }

        if ((int)$order['user_id'] !== (int)Auth::id()) {
            http_response_code(403);
            require APP_ROOT . '/views/404.php';
            return;
        }

        $data = [
            'pageTitle' => 'Track Order',
            'order'     => $order,
            'items'     => $this->orderModel->getItems($order['id']),
            'history'   => $this->orderModel->getStatusHistory($order['id']),
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/track_order.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }
}
