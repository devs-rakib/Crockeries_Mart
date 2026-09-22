<?php
namespace App\Controllers\admin;

use App\Models\Order;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\Mailer;
use App\Helpers\CSRF;

class OrderController
{
    private Order $orderModel;

    public function __construct()
    {
        Auth::requirePermission('manage_orders');
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        try {
            $page = (int) ($_GET['page'] ?? 1);
            $status = Sanitizer::clean($_GET['status'] ?? '');
            $search = Sanitizer::clean($_GET['search'] ?? '');

            $result = $this->orderModel->getAllAdmin($page, 20, $status, $search);

            $data = [
                'pageTitle' => 'Orders',
                'orders' => $result['orders'],
                'total' => $result['total'],
                'page' => $result['page'],
                'totalPages' => $result['total_pages'],
                'status' => $status,
                'search' => $search,
                'statusCounts' => $this->orderModel->getOrderStatusCounts(),
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/orders/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Order list error: " . $e->getMessage());
            Session::flash('error', 'Failed to load orders');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function view(int $id): void
    {
        try {
            $order = $this->orderModel->getById($id);
            if (!$order) {
                Session::flash('error', 'Order not found');
                Response::redirect(APP_URL . '/admin/orders');
                return;
            }

            $items = $this->orderModel->getItems($id);
            $history = $this->orderModel->getStatusHistory($id);

            $data = [
                'pageTitle' => 'Order #' . $order['order_number'],
                'order' => $order,
                'items' => $items,
                'history' => $history,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/orders/view.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Order view error: " . $e->getMessage());
            Session::flash('error', 'Failed to load order details');
            Response::redirect(APP_URL . '/admin/orders');
        }
    }

    public function updateStatus(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $order = $this->orderModel->getById($id);
            if (!$order) {
                Session::flash('error', 'Order not found');
                Response::redirect(APP_URL . '/admin/orders');
                return;
            }

            $orderStatus = Sanitizer::clean($_POST['status'] ?? '');
            $paymentStatus = Sanitizer::clean($_POST['payment_status'] ?? '');
            $note = Sanitizer::clean($_POST['note'] ?? '');

            $validOrderStatuses = array_keys(Order::STATUSES);
            if (!in_array($orderStatus, $validOrderStatuses)) {
                Session::flash('error', 'Invalid order status');
                Response::redirect(APP_URL . "/admin/orders/{$id}");
                return;
            }

            $validPaymentStatuses = Order::PAYMENT_STATUSES;
            if (!in_array($paymentStatus, $validPaymentStatuses)) {
                Session::flash('error', 'Invalid payment status');
                Response::redirect(APP_URL . "/admin/orders/{$id}");
                return;
            }

            $statusChanged = false;
            if ($order['order_status'] !== $orderStatus) {
                $this->orderModel->updateStatus($id, $orderStatus);
                $statusChanged = true;
            }

            if ($order['payment_status'] !== $paymentStatus) {
                $this->orderModel->updatePaymentStatus($id, $paymentStatus);
                $statusChanged = true;
            }

            if ($statusChanged) {
                $historyNote = $note;
                if (empty($historyNote)) {
                    $historyNote = "Status updated from " . ucfirst($order['order_status']) . " to " . ucfirst($orderStatus);
                }
                $this->orderModel->addStatusHistory($id, $orderStatus, $historyNote);

                try {
                    Mailer::orderStatusUpdate($order, $orderStatus);
                } catch (\Throwable $e) {
                    error_log("Email send failed: " . $e->getMessage());
                }

                Session::flash('success', 'Order status updated successfully');
            } else {
                Session::flash('info', 'No changes were made');
            }
        } catch (\Throwable $e) {
            error_log("Order update error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while updating order');
        }

        Response::redirect(APP_URL . "/admin/orders/{$id}");
    }

    public function addNote(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $order = $this->orderModel->getById($id);
            if (!$order) {
                Session::flash('error', 'Order not found');
                Response::redirect(APP_URL . '/admin/orders');
                return;
            }

            $note = Sanitizer::clean($_POST['note'] ?? '');
            if (empty($note)) {
                Session::flash('error', 'Note cannot be empty');
                Response::redirect(APP_URL . "/admin/orders/{$id}");
                return;
            }

            $this->orderModel->addStatusHistory($id, $order['order_status'], $note);
            Session::flash('success', 'Note added successfully');
        } catch (\Throwable $e) {
            error_log("Order note error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while adding note');
        }

        Response::redirect(APP_URL . "/admin/orders/{$id}");
    }

    public function updatePayment(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $order = $this->orderModel->getById($id);
            if (!$order) {
                Session::flash('error', 'Order not found');
                Response::redirect(APP_URL . '/admin/orders');
                return;
            }

            $paymentStatus = Sanitizer::clean($_POST['payment_status'] ?? '');
            $paymentMethod = Sanitizer::clean($_POST['payment_method'] ?? '');

            $validPaymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
            if (!in_array($paymentStatus, $validPaymentStatuses)) {
                Session::flash('error', 'Invalid payment status');
                Response::redirect(APP_URL . "/admin/orders/{$id}");
                return;
            }

            $this->orderModel->updatePaymentStatus($id, $paymentStatus);
            if ($paymentMethod !== '') {
                $this->orderModel->update($id, ['payment_method' => $paymentMethod]);
            }

            $this->orderModel->addStatusHistory($id, $order['order_status'], "Payment status updated to {$paymentStatus}");
            Session::flash('success', 'Payment status updated successfully');
        } catch (\Throwable $e) {
            error_log("Order payment update error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while updating payment');
        }

        Response::redirect(APP_URL . "/admin/orders/{$id}");
    }

    public function destroy(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $order = $this->orderModel->getById($id);
            if (!$order) {
                Session::flash('error', 'Order not found');
                Response::redirect(APP_URL . '/admin/orders');
                return;
            }

            $result = $this->orderModel->delete($id);

            if ($result) {
                Session::flash('success', 'Order deleted successfully');
            } else {
                Session::flash('error', 'Failed to delete order');
            }
        } catch (\Throwable $e) {
            error_log("Order delete error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while deleting order');
        }

        Response::redirect(APP_URL . '/admin/orders');
    }
}