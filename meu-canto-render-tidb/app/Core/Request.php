<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    public readonly string $method;
    public readonly string $path;
    public readonly string $basePath;
    public readonly array $query;
    public readonly array $body;
    public readonly array $files;
    public array $params = [];

    public function __construct()
    {
        $this->query = $_GET;
        $this->body  = $_POST;
        $this->files = $_FILES;

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        // Override por formulário (PUT/PATCH/DELETE) — só aceite em POST.
        if ($method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper((string) $_POST['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                $method = $override;
            }
        }
        $this->method = $method;

        $uri = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $parentDir = rtrim(str_replace('\\', '/', dirname($scriptDir)), '/');

        // Funciona em /meu-canto/public, /meu-canto (via .htaccess) e em VirtualHost (raiz).
        if ($scriptDir !== '' && str_starts_with($uri, $scriptDir)) {
            $this->basePath = $scriptDir;
        } elseif ($parentDir !== '' && str_starts_with($uri, $parentDir)) {
            $this->basePath = $parentDir;
        } else {
            $this->basePath = '';
        }
        $path = substr($uri, strlen($this->basePath));
        $this->path = '/' . trim($path, '/');
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
