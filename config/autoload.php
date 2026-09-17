<?php
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\Controllers\\' => APP_ROOT . '/app/Controllers/',
        'App\\Models\\'      => APP_ROOT . '/app/Models/',
        'App\\Helpers\\'     => APP_ROOT . '/app/Helpers/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($class, $prefix, strlen($prefix)) === 0) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return \App\Helpers\Session::getOldInput($key, $default);
    }
}
