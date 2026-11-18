<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var list<array{method:string, regex:string, handler:callable|array, middleware:list<string>}> */
    private array $routes = [];
    /** @var list<string> */
    private array $globalMiddleware = [];

    public function middleware(string ...$classes): void
    {
        $this->globalMiddleware = array_merge($this->globalMiddleware, $classes);
    }

    public function get(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    public function put(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('PUT', $pattern, $handler, $middleware);
    }

    public function patch(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('PATCH', $pattern, $handler, $middleware);
    }

    public function delete(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('DELETE', $pattern, $handler, $middleware);
    }

    private function add(string $method, string $pattern, array|callable $handler, array $middleware): void
    {
        $pattern = '/' . trim($pattern, '/');
        if ($pattern === '//') {
            $pattern = '/';
        }

        $segments = $pattern === '/' ? [] : explode('/', trim($pattern, '/'));
        $compiled = [];
        foreach ($segments as $segment) {
            if (preg_match('/^\{([A-Za-z][A-Za-z0-9_]*)\}$/', $segment, $matches)) {
                $compiled[] = '(?P<' . $matches[1] . '>[^/]+)';
            } else {
                $compiled[] = preg_quote($segment, '#');
            }
        }

        $regex = $pattern === '/' ? '#^/$#D' : '#^/' . implode('/', $compiled) . '/?$#D';
        $this->routes[] = [
            'method' => strtoupper($method),
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => array_values($middleware),
        ];
    }

    public function dispatch(Request $request): Response
    {
        // HEAD reutiliza a rota GET; Response::send() omite o corpo da resposta.
        $method = $request->method === 'HEAD' ? 'GET' : $request->method;
        $pathMatched = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $request->path, $matches)) {
                continue;
            }

            $pathMatched = true;
            if ($route['method'] !== $method) {
                continue;
            }

            $request->params = array_filter(
                $matches,
                static fn (mixed $key): bool => is_string($key),
                ARRAY_FILTER_USE_KEY
            );

            $core = fn (Request $r): Response => $this->invoke($route['handler'], $r);
            return $this->runMiddleware(
                array_merge($this->globalMiddleware, $route['middleware']),
                $request,
                $core
            );
        }

        return $pathMatched
            ? $this->error(405, 'Método não permitido')
            : $this->error(404, 'Página não encontrada');
    }

    private function runMiddleware(array $stack, Request $request, callable $core): Response
    {
        $next = array_reduce(
            array_reverse($stack),
            static fn (callable $next, string $class): callable =>
                static fn (Request $r): Response => (new $class())->handle($r, $next),
            $core
        );

        return $next($request);
    }

    private function invoke(array|callable $handler, Request $request): Response
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            return (new $class())->$method($request);
        }

        return $handler($request);
    }

    public function error(int $status, string $message): Response
    {
        return Response::html(
            View::render('errors/error', compact('status', 'message')),
            $status
        );
    }
}
