<?php
namespace App\Helpers;

class Response
{
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success(mixed $data = null, string $message = 'Success'): void
    {
        self::json(['success' => true, 'message' => $message, 'data' => $data]);
    }

    public static function error(string $message = 'Error', int $statusCode = 400): void
    {
        self::json(['success' => false, 'message' => $message], $statusCode);
    }

    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    public static function jsonWithCart(string $message = 'Success'): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'cart_count' => Session::getCartCount(),
            'cart_total' => Session::getCartTotal(),
            'cart' => Session::getCart()
        ]);
    }
}
