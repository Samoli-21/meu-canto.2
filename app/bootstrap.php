<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Autoloader PSR-4 (App\ => app/) — funciona sem Composer.
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/Core/helpers.php';

App\Core\Env::load(BASE_PATH . '/.env');
date_default_timezone_set((string) config('app.timezone', 'Africa/Luanda'));
if (function_exists('mb_internal_encoding')) { mb_internal_encoding('UTF-8'); }
