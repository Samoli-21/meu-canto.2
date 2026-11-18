<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method:string, regex:string, handler:mixed, middleware:array}> */
    private array $routes = [];
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

    public function delete(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('DELETE', $pattern, $handler, $middleware);
    }

    private function add(string $method, string $pattern, array|callable $handler, array $middleware): void
    {
        $pattern = rtrim($pattern, '/') ?: '/';
        $regex = '#^' . preg_replace('#\{([a-z_]+)\}#i', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[] = compact('method', 'regex', 'handler', 'middleware');
    }

    public function dispatch(Request $request): Response
    {
        // HEAD deve devolver os mesmos cabeçalhos de GET, sem conteúdo.
        // O Apache suprime o corpo da resposta HTTP HEAD.
        $dispatchMethod = $request->method === 'HEAD' ? 'GET' : $request->method;
        $pathMatched = false;
        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $request->path, $m)) {
                continue;
            }
            $pathMatched = true;
            if ($route['method'] !== $dispatchMethod) {
                continue;
            }
            $request->params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            $core = fn (Request $r): Response => $this->invoke($route['handler'], $r);
            return $this->runMiddleware(array_merge($this->globalMiddleware, $route['middleware']), $request, $core);
        }
        return $pathMatched ? $this->error(405, 'Método não permitido') : $this->error(404, 'Página não encontrada');
    }

    private function runMiddleware(array $stack, Request $request, callable $core): Response
    {
        $next = array_reduce(
            array_reverse($stack),
            fn (callable $next, string $class) => fn (Request $r): Response => (new $class())->handle($r, $next),
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
        return Response::html(View::render('errors/error', compact('status', 'message')), $status);
    }
}
