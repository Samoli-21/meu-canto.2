<?php
declare(strict_types=1);

return [
    'host' => (string) env('DB_HOST', '127.0.0.1'),
    'port' => (int) env('DB_PORT', 3306),
    'name' => (string) env('DB_NAME', 'meu_canto'),
    'user' => (string) env('DB_USER', 'root'),
    'pass' => (string) env('DB_PASS', ''),
    'charset' => 'utf8mb4',
    'ssl' => filter_var(env('DB_SSL', 'false'), FILTER_VALIDATE_BOOLEAN),
    'ssl_ca' => (string) env('DB_SSL_CA', '/etc/ssl/certs/ca-certificates.crt'),
];
