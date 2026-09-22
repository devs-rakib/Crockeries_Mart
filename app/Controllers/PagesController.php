<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Helpers\Sanitizer;
use App\Models\Order;
use App\Models\User;

class PagesController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function about(): void
    {
        $data = ['pageTitle' => 'About Us'];
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/pages/about.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function returnPolicy(): void
    {
        $data = ['pageTitle' => 'Return Policy'];
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/pages/return_policy.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function shippingInfo(): void
    {
        $data = ['pageTitle' => 'Shipping Info'];
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/pages/shipping_info.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function faq(): void
    {
        $data = ['pageTitle' => 'FAQ'];
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/pages/faq.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function terms(): void
    {
        $data = ['pageTitle' => 'Terms & Conditions'];
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/pages/terms.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function myAccount(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please login first');
            Response::redirect(APP_URL . '/login');
            return;
        }

        $user = $this->userModel->getById(Auth::id());
        $isIncomplete = $this->userModel->isProfileIncomplete($user);

        $data = [
            'pageTitle'    => 'My Account',
            'user'         => $user,
            'isIncomplete' => $isIncomplete,
            'success'      => Session::get('profile_success'),
            'error'        => Session::get('profile_error'),
        ];
        Session::remove('profile_success');
        Session::remove('profile_error');

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/pages/my_account.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function updateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Invalid request');
        }

        if (!Auth::check()) {
            Response::error('Please login first');
        }

        $userId = Auth::id();
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city    = trim($_POST['city'] ?? '');

        if (empty($name)) {
            Response::error('Name is required');
        }
        if (empty($phone)) {
            Response::error('Phone is required');
        }

        $validator = new Validator(['phone' => $phone]);
        $validator->phone('phone', 'Phone');
        if ($validator->fails()) {
            Response::error($validator->firstError());
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email address');
        }

        $existingPhone = $this->userModel->getByPhone($phone);
        if ($existingPhone && $existingPhone['id'] != $userId) {
            Response::error('Phone number already in use');
        }

        if (!empty($email)) {
            $existingEmail = $this->userModel->getByEmail($email);
            if ($existingEmail && $existingEmail['id'] != $userId) {
                Response::error('Email already in use');
            }
        }

        $this->userModel->updateProfile($userId, [
            'name'    => Sanitizer::clean($name),
            'email'   => $email ? Sanitizer::clean($email) : null,
            'phone'   => Sanitizer::clean($phone),
            'address' => $address ? Sanitizer::clean($address) : null,
            'city'    => $city ? Sanitizer::clean($city) : null,
        ]);

        Session::set('user_name', Sanitizer::clean($name));
        Session::set('user_email', $email ?: null);

        Response::json(['success' => true, 'message' => 'Profile updated successfully']);
    }

    public function changePassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Invalid request');
        }

        if (!Auth::check()) {
            Response::error('Please login first');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            Response::error('All password fields are required');
        }

        if ($newPassword !== $confirmPassword) {
            Response::error('New passwords do not match');
        }

        if (strlen($newPassword) < 6) {
            Response::error('Password must be at least 6 characters');
        }

        $user = $this->userModel->getById(Auth::id());
        if (!Auth::verifyPassword($currentPassword, $user['password'])) {
            Response::error('Current password is incorrect');
        }

        $this->userModel->updatePassword(Auth::id(), $newPassword);

        Response::json(['success' => true, 'message' => 'Password changed successfully']);
    }

    public function uploadAvatar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Invalid request');
        }

        if (!Auth::check()) {
            Response::error('Please login first');
        }

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            Response::error('Please select an image');
        }

        $file = $_FILES['avatar'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            Response::error('Only JPG, PNG, GIF, WEBP images are allowed');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            Response::error('Image must be under 2MB');
        }

        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $filename = 'avatar_' . Auth::id() . '_' . time() . '.' . $ext;
        $uploadDir = APP_ROOT . '/uploads/avatars';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            Response::error('Failed to upload image');
        }

        $oldUser = $this->userModel->getById(Auth::id());
        if (!empty($oldUser['avatar'])) {
            $oldPath = APP_ROOT . '/uploads/' . $oldUser['avatar'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $this->userModel->updateProfile(Auth::id(), [
            'avatar' => 'avatars/' . $filename,
        ]);

        Response::json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'avatar'  => APP_URL . '/uploads/avatars/' . $filename,
        ]);
    }

    public function orderTracking(): void
    {
        $order = null;
        $orderItems = [];
        $orderNumber = $_GET['order'] ?? $_POST['order_number'] ?? '';

        if (!empty($orderNumber)) {
            $orderModel = new Order();
            $order = $orderModel->getByNumber($orderNumber);
            if ($order) {
                $orderItems = $orderModel->getItems($order['id']);
            }
        }

        $data = [
            'pageTitle'    => 'Order Tracking',
            'order'        => $order,
            'orderItems'   => $orderItems,
            'orderNumber'  => $orderNumber,
        ];
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/pages/order_tracking.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }
}
