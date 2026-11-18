<?php
declare(strict_types=1);

namespace App\Core;

final class Response
{
    public function __construct(
        private string $body = '',
        private int $status = 200,
        private array $headers = []
    ) {}

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function json(array $data, int $status = 200): self
    {
        return new self(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $status,
            ['Content-Type' => 'application/json; charset=UTF-8']);
    }

    public static function redirect(string $path, int $status = 303): self
    {
        return new self('', $status, ['Location' => url($path)]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach (self::securityHeaders() as $name => $value) {
            header("$name: $value");
        }
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') {
            echo $this->body;
        }
    }

    private static function securityHeaders(): array
    {
        return [
            'X-Content-Type-Options'  => 'nosniff',
            'X-Frame-Options'         => 'DENY',
            'Referrer-Policy'         => 'strict-origin-when-cross-origin',
            'Permissions-Policy'      => 'camera=(), microphone=(), geolocation=()',
            'Content-Security-Policy' => "default-src 'self'; img-src 'self' data: https:; script-src 'self'; "
                . "style-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'",
        ];
    }
}
