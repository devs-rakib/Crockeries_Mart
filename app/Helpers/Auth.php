<?php
namespace App\Helpers;

class Auth
{
    public static function login(array $user): void
    {
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);
        Session::set('logged_in', true);
        Session::regenerate();
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::get('logged_in', false) === true;
    }

    public static function admin(): bool
    {
        return self::check() && Session::get('user_role') === 'admin';
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function name(): string
    {
        return Session::get('user_name', 'Guest');
    }

    public static function email(): ?string
    {
        return Session::get('user_email');
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Please login first');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::admin()) {
            Session::flash('error', 'Access denied');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
