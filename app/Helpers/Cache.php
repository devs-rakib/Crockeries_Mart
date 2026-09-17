<?php
namespace App\Helpers;

class Cache
{
    private static string $cachePath;
    private static int $defaultTTL = 3600;

    public static function init(): void
    {
        self::$cachePath = CACHE_PATH;
        if (!is_dir(self::$cachePath)) {
            mkdir(self::$cachePath, 0755, true);
        }
    }

    public static function get(string $key): mixed
    {
        $file = self::$cachePath . '/' . md5($key) . '.cache';
        if (!file_exists($file)) return null;

        $data = file_get_contents($file);
        $cached = unserialize($data);

        if (time() > $cached['expires']) {
            self::delete($key);
            return null;
        }
        return $cached['data'];
    }

    public static function set(string $key, mixed $data, int $ttl = 0): bool
    {
        $ttl = $ttl > 0 ? $ttl : self::$defaultTTL;
        $cached = [
            'data'    => $data,
            'expires' => time() + $ttl
        ];
        $file = self::$cachePath . '/' . md5($key) . '.cache';
        return file_put_contents($file, serialize($cached)) !== false;
    }

    public static function delete(string $key): bool
    {
        $file = self::$cachePath . '/' . md5($key) . '.cache';
        if (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }

    public static function flush(): void
    {
        $files = glob(self::$cachePath . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $data = self::get($key);
        if ($data !== null) return $data;

        $data = $callback();
        self::set($key, $data, $ttl);
        return $data;
    }
}
