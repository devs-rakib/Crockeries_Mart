<?php
namespace App\Controllers;

use App\Models\User;
use App\Helpers\Auth;
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
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                Response::error('Email and password are required');
            }

            $user = $this->userModel->getByEmail($email);
            if (!$user || !Auth::verifyPassword($password, $user['password'])) {
                Response::error('Invalid email or password');
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
