<?php
namespace App\Helpers;

class Sanitizer
{
    public static function clean(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function cleanArray(array $input): array
    {
        return array_map(function ($value) {
            return is_string($value) ? self::clean($value) : $value;
        }, $input);
    }

    public static function slug(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }

    public static function phone(string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    public static function price(float $price): string
    {
        return number_format($price, 2, '.', ',');
    }

    public static function banglaPrice(float $price): string
    {
        return '৳' . number_format($price, 0, '.', ',');
    }

    public static function image(string $path): string
    {
        if (empty($path) || !file_exists(UPLOAD_PATH . '/' . $path)) {
            return APP_URL . '/assets/images/placeholder.svg';
        }
        return APP_URL . '/uploads/' . $path;
    }

    public static function truncate(string $text, int $length = 100): string
    {
        if (strlen($text) <= $length) return $text;
        return substr($text, 0, $length) . '...';
    }

    public static function timeAgo(string $datetime): string
    {
        $now = time();
        $time = strtotime($datetime);
        $diff = $now - $time;

        if ($diff < 60) return 'এইমাত্র';
        if ($diff < 3600) return floor($diff / 60) . ' মিনিট আগে';
        if ($diff < 86400) return floor($diff / 3600) . ' ঘণ্টা আগে';
        if ($diff < 2592000) return floor($diff / 86400) . ' দিন আগে';
        return date('d M Y', $time);
    }
}
