<?php
declare(strict_types=1);

use App\Core\{Config, Csrf, Env, Session};

function env(string $key, mixed $default = null): mixed
{
    return Env::get($key, $default);
}

function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/** Escapa texto para HTML (prevenção de XSS). Usar em TODA a saída dinâmica. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return ($GLOBALS['mc_base_path'] ?? '') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function csrf_field(): string
{
    return Csrf::field();
}

function flash(string $key, mixed $default = null): mixed
{
    return Session::pullFlash($key, $default);
}
