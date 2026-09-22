<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Auth;
use App\Helpers\OTP;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\Sanitizer;
use App\Helpers\Response;

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(): void
    {
        if (Auth::check()) {
            Response::redirect(APP_URL);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($identifier)) {
                Response::error('Email/Phone is required');
            }

            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                // Email login — password required
                if (empty($password)) {
                    Response::error('Password is required');
                }

                $user = $this->userModel->getByEmail($identifier);
                if (!$user || !Auth::verifyPassword($password, $user['password'])) {
                    Response::error('Invalid credentials');
                }
            } else {
                // Phone login — password bypass
                $phone = Sanitizer::clean($identifier);
                $user = $this->userModel->getByPhone($phone);

                if (!$user) {
                    $userId = $this->userModel->createAuto('User ' . $phone, $phone, null);
                    $user = $this->userModel->getById($userId);
                }
            }

            if ($user['status'] != 1) {
                Response::error('Your account has been deactivated');
            }

            Auth::login($user);
            Response::json(['success' => true, 'message' => 'Login successful', 'redirect' => APP_URL]);
            return;
        }

        $data = ['pageTitle' => 'Login'];
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/auth/login.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function sendOtp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Invalid request method');
        }

        $phone = trim($_POST['phone'] ?? '');
        if (empty($phone)) {
            Response::error('Phone number is required');
        }

        $phone = Sanitizer::clean($phone);
        $validator = new Validator(['phone' => $phone]);
        $validator->phone('phone', 'Phone');
        if ($validator->fails()) {
            Response::error($validator->firstError());
        }

        $user = $this->userModel->getByPhone($phone);
        if (!$user) {
            Response::error('No account found with this phone number');
        }

        if ($user['status'] != 1) {
            Response::error('Your account has been deactivated');
        }

        $otp = OTP::generate($phone);

        Response::json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp'     => $otp,
        ]);
    }

    public function verifyOtp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::error('Invalid request method');
        }

        $phone = trim($_POST['phone'] ?? '');
        $otp   = trim($_POST['otp'] ?? '');

        if (empty($phone) || empty($otp)) {
            Response::error('Phone and OTP are required');
        }

        $phone = Sanitizer::clean($phone);

        if (!OTP::verify($phone, $otp)) {
            Response::error('Invalid or expired OTP');
        }

        $user = $this->userModel->getByPhone($phone);
        if (!$user) {
            Response::error('Account not found');
        }

        if ($user['status'] != 1) {
            Response::error('Your account has been deactivated');
        }

        Auth::login($user);
        Response::json([
            'success'  => true,
            'message'  => 'Login successful',
            'redirect' => APP_URL,
        ]);
    }

    public function me(): void
    {
        if (!Auth::check()) {
            Response::json(['success' => false, 'message' => 'Not authenticated'], 401);
            return;
        }

        $user = $this->userModel->getById(Auth::id());
        if (!$user) {
            Response::json(['success' => false, 'message' => 'User not found'], 404);
            return;
        }

        Response::json([
            'success' => true,
            'user'    => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'phone' => $user['phone'],
                'email' => $user['email'],
            ],
        ]);
    }

    public function register(): void
    {
        if (Auth::check()) {
            Response::redirect(APP_URL);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                      ->required('password_confirmation', 'Password Confirmation');

            if ($validator->fails()) {
                Response::error($validator->firstError());
            }

            if ($_POST['password'] !== $_POST['password_confirmation']) {
                Response::error('Passwords do not match');
            }

            $userId = $this->userModel->create([
                'name'       => Sanitizer::clean($_POST['name']),
                'email'      => Sanitizer::clean($_POST['email']),
                'phone'      => Sanitizer::clean($_POST['phone']),
                'password'   => Auth::hashPassword($_POST['password']),
                'role'       => 'customer',
                'status'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $user = $this->userModel->getById($userId);
            Auth::login($user);
            Response::json(['success' => true, 'message' => 'Registration successful', 'redirect' => APP_URL]);
            return;
        }

        $data = ['pageTitle' => 'Register'];
        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/auth/register.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function logout(): void
    {
        Auth::logout();
        Response::redirect(APP_URL);
    }
}
