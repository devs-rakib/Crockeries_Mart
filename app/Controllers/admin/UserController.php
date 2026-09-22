<?php
namespace App\Controllers\admin;

use App\Models\User;
use App\Models\Order;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\CSRF;

class UserController
{
    private User $userModel;
    private Order $orderModel;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->userModel = new User();
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        try {
            $page = (int) ($_GET['page'] ?? 1);
            $search = Sanitizer::clean($_GET['search'] ?? '');

            $result = $this->userModel->getAllAdmin($page, 20, $search);

            $data = [
                'pageTitle' => 'Users',
                'users' => $result['users'],
                'total' => $result['total'],
                'page' => $result['page'],
                'totalPages' => $result['total_pages'],
                'search' => $search,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/users/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("User list error: " . $e->getMessage());
            Session::flash('error', 'Failed to load users');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function view(int $id): void
    {
        try {
            $user = $this->userModel->getById($id);
            if (!$user) {
                Session::flash('error', 'User not found');
                Response::redirect(APP_URL . '/admin/users');
                return;
            }

            $result = $this->orderModel->getAllAdmin(1, 100, '', '');
            $userOrders = array_filter($result['orders'], function ($order) use ($id) {
                return $order['user_id'] == $id;
            });

            $data = [
                'pageTitle' => 'User: ' . $user['name'],
                'user' => $user,
                'orders' => array_values($userOrders),
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/users/view.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("User view error: " . $e->getMessage());
            Session::flash('error', 'Failed to load user details');
            Response::redirect(APP_URL . '/admin/users');
        }
    }

    public function toggleStatus(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $user = $this->userModel->getById($id);
            if (!$user) {
                Session::flash('error', 'User not found');
                Response::redirect(APP_URL . '/admin/users');
                return;
            }

            if ($user['role'] === 'admin') {
                Session::flash('error', 'Cannot change admin status');
                Response::redirect(APP_URL . '/admin/users');
                return;
            }

            $this->userModel->toggleStatus($id);
            $newStatus = $user['status'] == 1 ? 'inactive' : 'active';
            Session::flash('success', "User status changed to {$newStatus}");
        } catch (\Throwable $e) {
            error_log("User toggle error: " . $e->getMessage());
            Session::flash('error', 'Failed to update user status');
        }

        Response::redirect(APP_URL . '/admin/users');
    }

    public function destroy(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $user = $this->userModel->getById($id);
            if (!$user) {
                Session::flash('error', 'User not found');
                Response::redirect(APP_URL . '/admin/users');
                return;
            }

            if ($user['role'] === 'admin') {
                Session::flash('error', 'Cannot delete admin user');
                Response::redirect(APP_URL . '/admin/users');
                return;
            }

            $result = $this->userModel->delete($id);

            if ($result) {
                Session::flash('success', 'User deleted successfully');
            } else {
                Session::flash('error', 'Failed to delete user');
            }
        } catch (\Throwable $e) {
            error_log("User delete error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while deleting user');
        }

        Response::redirect(APP_URL . '/admin/users');
    }
}