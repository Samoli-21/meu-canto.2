<?php
return [
    'name'     => env('APP_NAME', 'Meu Canto'),
    'env'      => env('APP_ENV', 'production'),
    'debug'    => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
    'timezone' => env('APP_TIMEZONE', 'Africa/Luanda'),
    'locale'   => 'pt',   // Apenas português
    'private_storage' => env('PRIVATE_STORAGE', 'storage/private'),
];
