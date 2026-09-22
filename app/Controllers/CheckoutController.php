<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Helpers\Sanitizer;
use App\Helpers\Mailer;
use App\Helpers\Database;

class CheckoutController
{
    private Order $orderModel;
    private Product $productModel;
    private User $userModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->userModel = new User();
    }

    public function index(): void
    {
        if (empty(Session::getCart())) {
            Response::redirect(APP_URL . '/shop');
            return;
        }

        $user = null;
        if (Auth::check()) {
            $user = $this->userModel->getById(Auth::id());
        }

        $data = [
            'pageTitle' => 'Checkout',
            'cart'      => Session::getCart(),
            'subtotal'  => Session::getCartTotal(),
            'user'      => $user,
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/checkout.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function place(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Invalid request method');
        }

        $validator = new Validator($_POST);
        $validator->required('customer_name', 'Name')
                  ->required('customer_phone', 'Phone')
                  ->phone('customer_phone', 'Phone')
                  ->required('shipping_address', 'Address')
                  ->required('delivery_area', 'Delivery Area')
                  ->required('payment_method', 'Payment Method');

        if ($validator->fails()) {
            Response::error($validator->firstError());
        }

        $cart = Session::getCart();
        if (empty($cart)) {
            Response::error('Cart is empty');
        }

        $db = Database::getInstance();
        $db->getConnection()->beginTransaction();

        try {
            $customerName  = Sanitizer::clean($_POST['customer_name']);
            $customerPhone = Sanitizer::clean($_POST['customer_phone']);
            $customerEmail = !empty($_POST['customer_email']) ? Sanitizer::clean($_POST['customer_email']) : null;

            $user = null;
            if (Auth::check()) {
                $user = $this->userModel->getById(Auth::id());
            }

            if (!$user) {
                $user = $this->userModel->getByPhone($customerPhone);
            }

            if (!$user) {
                $userId = $this->userModel->createAuto($customerName, $customerPhone, $customerEmail);
                $user = $this->userModel->getById($userId);
            } else {
                $this->userModel->update($user['id'], [
                    'name'  => $customerName,
                    'email' => $customerEmail ?? $user['email'],
                ]);
            }

            Auth::login($user);

            $subtotal = 0;
            $orderItems = [];

            foreach ($cart as $item) {
                $product = $this->productModel->getById($item['product_id']);
                if (!$product || $product['stock_quantity'] < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$item['name']}");
                }

                $unitPrice = $item['discount_price'] ?: $item['price'];
                $subtotal += $unitPrice * $item['quantity'];

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal'   => $unitPrice * $item['quantity'],
                ];

                $this->productModel->update($item['product_id'], [
                    'stock_quantity' => $product['stock_quantity'] - $item['quantity'],
                ]);
            }

            $shippingCost = $_POST['delivery_area'] === 'inside_dhaka' ? SHIPPING_INSIDE_DHAKA : SHIPPING_OUTSIDE_DHAKA;
            $totalAmount = $subtotal + $shippingCost;

            $orderData = [
                'order_number'     => $this->orderModel->generateOrderNumber(),
                'user_id'          => $user['id'],
                'customer_name'    => $customerName,
                'customer_phone'   => $customerPhone,
                'customer_email'   => $customerEmail,
                'shipping_address' => Sanitizer::clean($_POST['shipping_address']),
                'total_amount'     => $totalAmount,
                'shipping_cost'    => $shippingCost,
                'payment_method'   => Sanitizer::clean($_POST['payment_method']),
                'payment_status'   => 'pending',
                'order_status'     => 'pending',
                'created_at'       => date('Y-m-d H:i:s'),
            ];

            $orderId = $this->orderModel->create($orderData);
            $this->orderModel->createItems($orderId, $orderItems);
            $this->orderModel->addStatusHistory($orderId, 'pending', 'Order placed');

            $fullOrder = $this->orderModel->getById($orderId);
            $fullItems = $this->orderModel->getItems($orderId);

            Mailer::orderConfirmation($fullOrder, $fullItems);

            $db->getConnection()->commit();

            Session::setCart([]);

            if ($_POST['payment_method'] === 'sslcommerz') {
                Session::set('pending_sslcommerz_order_id', $orderId);
                Response::json([
                    'success'      => true,
                    'message'      => 'Redirecting to payment...',
                    'order_number' => $orderData['order_number'],
                    'redirect'     => APP_URL . '/payment/initiate',
                ]);
            } else {
                Response::json([
                    'success'      => true,
                    'message'      => 'Order placed successfully!',
                    'order_number' => $orderData['order_number'],
                    'redirect'     => APP_URL . '/order-success?order=' . $orderData['order_number'],
                ]);
            }
        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            Response::error($e->getMessage());
        }
    }

    public function success(): void
    {
        $orderNumber = $_GET['order'] ?? '';
        $order = $this->orderModel->getByNumber($orderNumber);
        if (!$order) {
            http_response_code(404);
            require APP_ROOT . '/views/404.php';
            return;
        }

        $data = [
            'pageTitle' => 'Order Success',
            'order'     => $order,
            'items'     => $this->orderModel->getItems($order['id']),
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/order_success.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function calculateShipping(): void
    {
        $area = $_POST['area'] ?? '';
        $subtotal = (float) ($_POST['subtotal'] ?? 0);

        if ($area === 'inside_dhaka') {
            $shipping = SHIPPING_INSIDE_DHAKA;
        } elseif ($area === 'outside_dhaka') {
            $shipping = SHIPPING_OUTSIDE_DHAKA;
        } else {
            $shipping = 0;
        }

        Response::json([
            'success'      => true,
            'shipping'     => $shipping,
            'subtotal'     => $subtotal,
            'grand_total'  => $subtotal + $shipping,
        ]);
    }
}
