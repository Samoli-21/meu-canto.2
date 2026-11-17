<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\{Csrf, Request, Response, View};

/** Rejeita pedidos POST/PUT/PATCH/DELETE sem token CSRF válido. */
final class CsrfMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        if (in_array($request->method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if (!Csrf::verify($request->body[Csrf::FIELD] ?? null)) {
                return Response::html(View::render('errors/error', [
                    'status'  => 419,
                    'message' => 'A sessão expirou ou o pedido não é válido. Volta atrás, recarrega a página e tenta novamente.',
                ]), 419);
            }
        }
        return $next($request);
    }
}
