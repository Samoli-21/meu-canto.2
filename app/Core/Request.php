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

        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        if ($method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper((string) $_POST['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                $method = $override;
            }
        }
        $this->method = $method;

        $rawPath = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
        $uriPath = rawurldecode($rawPath);
        $uriPath = preg_replace('#/+#', '/', $uriPath) ?: '/';
        if ($uriPath[0] !== '/') {
            $uriPath = '/' . $uriPath;
        }

        // Detecta instalações em subdirectório sem remover por engano prefixos parciais.
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        $scriptDir = rtrim($scriptDir, '/.');
        if ($scriptDir === '') {
            $scriptDir = '';
        }

        $parentDir = $scriptDir !== '' ? rtrim(str_replace('\\', '/', dirname($scriptDir)), '/.') : '';
        $basePath = '';
        if ($scriptDir !== '' && ($uriPath === $scriptDir || str_starts_with($uriPath, $scriptDir . '/'))) {
            $basePath = $scriptDir;
        } elseif ($parentDir !== '' && ($uriPath === $parentDir || str_starts_with($uriPath, $parentDir . '/'))) {
            // Instalação XAMPP em /meu-canto com rewrite interno para /public/index.php.
            $basePath = $parentDir;
        }
        $this->basePath = $basePath;

        $path = substr($uriPath, strlen($basePath));
        $path = '/' . trim($path, '/');
        $this->path = $path === '//' ? '/' : $path;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function ip(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }
}
