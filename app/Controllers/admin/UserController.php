<?php
namespace App\Controllers\admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\CSRF;
use App\Helpers\Validator;
use App\Helpers\ActivityLog;

class UserController
{
    private User $userModel;
    private Role $roleModel;
    private Order $orderModel;

    public function __construct()
    {
        Auth::requirePermission('manage_users');
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->orderModel = new Order();
    }

    public function index(): void
    {
        try {
            $page = (int) ($_GET['page'] ?? 1);
            $search = Sanitizer::clean($_GET['search'] ?? '');
            $roleFilter = (int) ($_GET['role'] ?? 0);

            $result = $this->userModel->getAllAdmin($page, 20, $search, $roleFilter);
            $roles = $this->roleModel->getAll();

            $data = [
                'pageTitle'   => 'Users',
                'users'       => $result['users'],
                'total'       => $result['total'],
                'page'        => $result['page'],
                'totalPages'  => $result['total_pages'],
                'search'      => $search,
                'roles'       => $roles,
                'roleFilter'  => $roleFilter,
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

    public function create(): void
    {
        $roles = $this->roleModel->getAll();

        $data = [
            'pageTitle' => 'Create User',
            'roles'     => $roles,
        ];

        require APP_ROOT . '/views/admin/admin_header.php';
        require APP_ROOT . '/views/admin/users/create.php';
        require APP_ROOT . '/views/admin/admin_footer.php';
    }

    public function store(): void
    {
        if (!CSRF::verify()) {
            Session::flash('error', 'Invalid request');
            Response::redirect(APP_URL . '/admin/users');
            return;
        }

        try {
            $validator = new Validator($_POST);
            $validator->required('name', 'Name')
                      ->required('email', 'Email')
                      ->email('email', 'Email')
                      ->unique('email', 'users', 'email', 0, 'Email')
                      ->required('phone', 'Phone')
                      ->phone('phone', 'Phone')
                      ->unique('phone', 'users', 'phone', 0, 'Phone')
                      ->required('password', 'Password')
                      ->minLength('password', 6, 'Password')
                      ->required('role_id', 'Role');

            if ($validator->fails()) {
                Session::flash('error', $validator->firstError());
                Response::redirect(APP_URL . '/admin/users/create');
                return;
            }

            $userId = $this->userModel->create([
                'name'       => Sanitizer::clean($_POST['name']),
                'email'      => Sanitizer::clean($_POST['email']),
                'phone'      => Sanitizer::clean($_POST['phone']),
                'password'   => Auth::hashPassword($_POST['password']),
                'role_id'    => (int) $_POST['role_id'],
                'status'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            ActivityLog::log('user_created', 'user', Sanitizer::clean($_POST['email']), Auth::id());

            Session::flash('success', 'User created successfully');
            Response::redirect(APP_URL . '/admin/users');
        } catch (\Throwable $e) {
            error_log("User create error: " . $e->getMessage());
            Session::flash('error', 'Failed to create user');
            Response::redirect(APP_URL . '/admin/users');
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

            $roles = $this->roleModel->getAll();
            $userPermissions = $this->roleModel->getPermissionNames($user['role_id']);

            $data = [
                'pageTitle'       => 'User: ' . $user['name'],
                'user'            => $user,
                'orders'          => array_values($userOrders),
                'roles'           => $roles,
                'userPermissions' => $userPermissions,
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

    public function updateRole(int $id): void
    {
        if (!CSRF::verify()) {
            Session::flash('error', 'Invalid request');
            Response::redirect(APP_URL . '/admin/users');
            return;
        }

        try {
            $user = $this->userModel->getById($id);
            if (!$user) {
                Session::flash('error', 'User not found');
                Response::redirect(APP_URL . '/admin/users');
                return;
            }

            if ($user['role_id'] == Auth::ROLE_ADMIN && Auth::id() !== $id) {
                Session::flash('error', 'Cannot change the role of another admin');
                Response::redirect(APP_URL . "/admin/users/{$id}");
                return;
            }

            if ($user['role_id'] == Auth::ROLE_ADMIN && (int) $_POST['role_id'] !== Auth::ROLE_ADMIN) {
                $adminCount = $this->userModel->countByRole(Auth::ROLE_ADMIN);
                if ($adminCount <= 1) {
                    Session::flash('error', 'Cannot remove the last admin from the Admin role');
                    Response::redirect(APP_URL . "/admin/users/{$id}");
                    return;
                }
            }

            $newRoleId = (int) $_POST['role_id'];
            $this->userModel->updateRole($id, $newRoleId);

            $roleName = $this->roleModel->getRoleName($newRoleId);
            ActivityLog::log('role_changed', 'user', "Changed {$user['name']} to {$roleName}", Auth::id());

            Session::flash('success', "User role updated to {$roleName}");
            Response::redirect(APP_URL . "/admin/users/{$id}");
        } catch (\Throwable $e) {
            error_log("User update role error: " . $e->getMessage());
            Session::flash('error', 'Failed to update user role');
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

            if ($user['role_id'] == Auth::ROLE_ADMIN) {
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

            if ($user['role_id'] == Auth::ROLE_ADMIN) {
                Session::flash('error', 'Cannot delete admin user');
                Response::redirect(APP_URL . '/admin/users');
                return;
            }

            if ($user['role_id'] != Auth::ROLE_USER) {
                Session::flash('error', 'Cannot delete staff or manager users');
                Response::redirect(APP_URL . '/admin/users');
                return;
            }

            $result = $this->userModel->delete($id);

            if ($result) {
                ActivityLog::log('user_deleted', 'user', $user['email'], Auth::id());
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
