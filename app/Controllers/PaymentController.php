<?php
namespace App\Controllers;

use App\Models\Order;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\SSLCommerz;

class PaymentController
{
    private Order $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function initiate(): void
    {
        $orderId = Session::get('pending_sslcommerz_order_id');
        if (!$orderId) {
            Response::redirect(APP_URL . '/checkout');
            return;
        }

        $order = $this->orderModel->getById($orderId);
        if (!$order) {
            Session::remove('pending_sslcommerz_order_id');
            Response::redirect(APP_URL . '/checkout');
            return;
        }

        try {
            $sslcommerz = new SSLCommerz();

            $transactionId = $order['order_number'] . '-' . time();
            $this->orderModel->updateTransactionId($orderId, $transactionId);

            $sessionData = [
                'total_amount'  => (float) $order['total_amount'],
                'tran_id'       => $transactionId,
                'success_url'   => APP_URL . '/payment/success',
                'fail_url'      => APP_URL . '/payment/fail',
                'cancel_url'    => APP_URL . '/payment/cancel',
                'ipn_url'       => APP_URL . '/payment/ipn',
                'cus_name'      => $order['customer_name'],
                'cus_email'     => $order['customer_email'] ?: '',
                'cus_phone'     => $order['customer_phone'],
                'cus_add1'      => $order['shipping_address'],
                'cus_city'      => '',
                'cus_state'     => '',
                'cus_postcode'  => '',
                'cus_country'   => 'Bangladesh',
                'ship_name'     => $order['customer_name'],
                'ship_add1'     => $order['shipping_address'],
                'ship_city'     => '',
                'ship_state'    => '',
                'ship_postcode' => '',
                'ship_country'  => 'Bangladesh',
                'product_name'  => 'Order #' . $order['order_number'],
                'product_category' => 'E-commerce',
                'product_profile'  => 'general',
                'value_a'       => $order['order_number'],
            ];

            $result = $sslcommerz->createSession($sessionData);

            Session::set('sslcommerz_trx_id', $transactionId);

            $redirectUrl = $result['GatewayPageURL'] ?? $result['redirect_url'] ?? '';
            if (empty($redirectUrl)) {
                throw new \Exception('No redirect URL received from SSLCommerz');
            }

            Response::redirect($redirectUrl);
        } catch (\Exception $e) {
            Session::remove('pending_sslcommerz_order_id');
            $this->orderModel->updatePaymentStatus($orderId, 'failed');
            $this->orderModel->addStatusHistory($orderId, 'pending', 'Payment initiation failed: ' . $e->getMessage());
            Response::redirect(APP_URL . '/payment/fail?order=' . urlencode($order['order_number']));
        }
    }

    public function success(): void
    {
        $valId   = $_POST['val_id'] ?? $_GET['val_id'] ?? '';
        $tranId  = $_POST['tran_id'] ?? $_GET['tran_id'] ?? '';
        $amount  = $_POST['amount'] ?? $_GET['amount'] ?? '';
        $status  = $_POST['status'] ?? $_GET['status'] ?? '';

        if (empty($tranId)) {
            Response::redirect(APP_URL . '/payment/fail');
            return;
        }

        $order = $this->orderModel->getByTransactionId($tranId);
        if (!$order) {
            Response::redirect(APP_URL . '/payment/fail');
            return;
        }

        if ($order['payment_status'] === 'paid') {
            Session::remove('pending_sslcommerz_order_id');
            Session::remove('sslcommerz_trx_id');
            Session::setCart([]);
            Response::redirect(APP_URL . '/order-success?order=' . $order['order_number']);
            return;
        }

        try {
            $sslcommerz = new SSLCommerz();

            $isValid = $sslcommerz->validate([
                'val_id' => $valId,
            ]);

            if ($isValid && $status === 'VALID') {
                $this->orderModel->updatePaymentStatus($order['id'], 'paid');
                $this->orderModel->addStatusHistory($order['id'], 'processing', 'Payment confirmed via SSLCommerz');

                Session::remove('pending_sslcommerz_order_id');
                Session::remove('sslcommerz_trx_id');
                Session::setCart([]);

                Response::redirect(APP_URL . '/order-success?order=' . $order['order_number']);
            } else {
                $this->orderModel->updatePaymentStatus($order['id'], 'failed');
                $this->orderModel->addStatusHistory($order['id'], 'pending', 'Payment validation failed');

                Session::remove('pending_sslcommerz_order_id');
                Response::redirect(APP_URL . '/payment/fail?order=' . urlencode($order['order_number']));
            }
        } catch (\Exception $e) {
            $this->orderModel->updatePaymentStatus($order['id'], 'failed');
            $this->orderModel->addStatusHistory($order['id'], 'pending', 'Payment validation error: ' . $e->getMessage());

            Session::remove('pending_sslcommerz_order_id');
            Response::redirect(APP_URL . '/payment/fail?order=' . urlencode($order['order_number']));
        }
    }

    public function fail(): void
    {
        $orderNumber = $_GET['order'] ?? '';

        $orderId = Session::get('pending_sslcommerz_order_id');
        if ($orderId) {
            $order = $this->orderModel->getById($orderId);
            if ($order) {
                $this->orderModel->updatePaymentStatus($orderId, 'failed');
                $this->orderModel->addStatusHistory($orderId, 'pending', 'Payment failed via SSLCommerz gateway');
                $orderNumber = $orderNumber ?: $order['order_number'];
            }
            Session::remove('pending_sslcommerz_order_id');
        }

        $data = [
            'pageTitle'   => 'Payment Failed',
            'orderNumber' => $orderNumber,
            'reason'      => 'The payment could not be processed. Please try again.',
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/payment_fail.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function cancel(): void
    {
        $orderNumber = '';

        $orderId = Session::get('pending_sslcommerz_order_id');
        if ($orderId) {
            $order = $this->orderModel->getById($orderId);
            if ($order) {
                $this->orderModel->updatePaymentStatus($orderId, 'failed');
                $this->orderModel->addStatusHistory($orderId, 'pending', 'Payment cancelled by customer');
                $orderNumber = $order['order_number'];
            }
            Session::remove('pending_sslcommerz_order_id');
        }

        $data = [
            'pageTitle'   => 'Payment Cancelled',
            'orderNumber' => $orderNumber,
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/payment_cancel.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function ipn(): void
    {
        $tranId = $_POST['tran_id'] ?? '';
        $status = $_POST['status'] ?? '';
        $valId  = $_POST['val_id'] ?? '';

        if (empty($tranId) || empty($valId)) {
            echo 'ok';
            return;
        }

        $order = $this->orderModel->getByTransactionId($tranId);
        if (!$order) {
            echo 'ok';
            return;
        }

        if ($order['payment_status'] === 'paid') {
            echo 'ok';
            return;
        }

        if ($status === 'VALID') {
            try {
                $sslcommerz = new SSLCommerz();
                $isValid = $sslcommerz->validate($_POST);

                if ($isValid) {
                    $this->orderModel->updatePaymentStatus($order['id'], 'paid');
                    $this->orderModel->addStatusHistory($order['id'], 'processing', 'Payment confirmed via SSLCommerz IPN');
                } else {
                    error_log("SSLCommerz IPN validation failed for tran_id: {$tranId}");
                }
            } catch (\Exception $e) {
                error_log("SSLCommerz IPN validation error: " . $e->getMessage());
            }
        }

        echo 'ok';
    }
}
