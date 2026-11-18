<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\{Logger, Request, Response, Router, Session, View};
use App\Middleware\CsrfMiddleware;

$debug = (bool) config('app.debug');
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

Session::start();
$request = new Request();
$GLOBALS['mc_base_path'] = $request->basePath;

try {
    $router = new Router();
    $router->middleware(CsrfMiddleware::class);
    require BASE_PATH . '/routes/web.php';
    $response = $router->dispatch($request);
} catch (Throwable $e) {
    Logger::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
    $response = Response::html(View::render('errors/error', [
        'status'  => 500,
        'message' => $debug ? $e->getMessage() : 'Ocorreu um erro inesperado. Tenta novamente dentro de instantes.',
    ]), 500);
}
$response->send();
