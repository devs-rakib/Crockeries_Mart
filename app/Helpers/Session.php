<?php
namespace App\Helpers;

class Session
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    public static function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function getCart(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public static function setCart(array $cart): void
    {
        $_SESSION['cart'] = $cart;
    }

    public static function getCartCount(): int
    {
        $cart = self::getCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    public static function getCartTotal(): float
    {
        $cart = self::getCart();
        $total = 0;
        foreach ($cart as $item) {
            $price = $item['discount_price'] ?? $item['price'];
            $total += $price * $item['quantity'];
        }
        return $total;
    }

    public static function flashInput(array $data): void
    {
        $_SESSION['_old_input'] = $data;
    }

    public static function getOldInput(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['_old_input'][$key] ?? $default;
    }

    public static function clearOldInput(): void
    {
        unset($_SESSION['_old_input']);
    }
}
