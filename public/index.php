<?php
declare(strict_types=1);

/*
 * Front controller do Meu Canto.
 * O health check é intencionalmente independente de sessão, rotas e TiDB,
 * para que o Render consiga verificar o processo HTTP mesmo durante uma
 * indisponibilidade temporária da base de dados.
 */
$requestedPath = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
$requestMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($requestedPath === '/healthz' && in_array($requestMethod, ['GET', 'HEAD'], true)) {
    http_response_code(200);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store, max-age=0');
    header('X-Content-Type-Options: nosniff');
    if ($requestMethod !== 'HEAD') {
        echo "ok\n";
    }
    exit;
}

$debug = filter_var((string) (getenv('APP_DEBUG') ?: 'false'), FILTER_VALIDATE_BOOLEAN);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

try {
    require dirname(__DIR__) . '/app/bootstrap.php';

    // Mantém APP_DEBUG sincronizado com a configuração já carregada.
    $debug = (bool) config('app.debug', false);
    ini_set('display_errors', $debug ? '1' : '0');

    \App\Core\Session::start();
    $request = new \App\Core\Request();
    $GLOBALS['mc_base_path'] = $request->basePath;

    $router = new \App\Core\Router();
    $router->middleware(\App\Middleware\CsrfMiddleware::class);
    require BASE_PATH . '/routes/web.php';

    $response = $router->dispatch($request);
    $response->send();
} catch (Throwable $exception) {
    $context = [
        'type' => get_class($exception),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'path' => $requestedPath,
        'method' => $requestMethod,
    ];
    $logMessage = '[Meu Canto] HTTP request failed: ' . $exception->getMessage()
        . ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    error_log($logMessage);

    if (class_exists(\App\Core\Logger::class)) {
        try {
            \App\Core\Logger::error($exception->getMessage(), $context + ['trace' => $exception->getTraceAsString()]);
        } catch (Throwable $loggingFailure) {
            error_log('[Meu Canto] Logger failure: ' . $loggingFailure->getMessage());
        }
    }

    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    header('Cache-Control: no-store');

    if ($debug) {
        echo '<!doctype html><html lang="pt-AO"><meta charset="utf-8"><title>Erro interno</title>';
        echo '<body style="font:16px/1.6 system-ui,sans-serif;padding:2rem">';
        echo '<h1>Erro interno do Meu Canto</h1><p>'
            . htmlspecialchars($exception->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '</p><p><code>'
            . htmlspecialchars($exception->getFile() . ':' . $exception->getLine(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '</code></p></body></html>';
    } else {
        echo '<!doctype html><html lang="pt-AO"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>Temporariamente indisponível · Meu Canto</title>';
        echo '<body style="font:16px/1.6 system-ui,sans-serif;background:#f8f5f0;color:#34463d;padding:2rem">';
        echo '<main style="max-width:42rem;margin:10vh auto;background:#fffefa;padding:2rem;border-radius:1.25rem">';
        echo '<h1>Não foi possível abrir esta página.</h1><p>O Meu Canto encontrou um problema temporário. Tenta novamente dentro de instantes.</p>';
        echo '<p><a href="/" style="color:#2f5d50">Voltar ao início</a></p></main></body></html>';
    }
}
