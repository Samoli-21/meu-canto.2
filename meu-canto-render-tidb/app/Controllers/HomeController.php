<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Controller, Database, Request, Response, Session};

final class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('public/home', ['title' => 'Início']);
    }

    /** Página técnica de verificação — só disponível com APP_DEBUG=true. */
    public function status(Request $request): Response
    {
        if (!config('app.debug')) {
            return $this->view('errors/error', ['status' => 404, 'message' => 'Página não encontrada'], 404);
        }
        $checks = [];
        $checks['PHP ' . PHP_VERSION] = version_compare(PHP_VERSION, '8.2.0', '>=');
        foreach (['pdo_mysql', 'mbstring', 'fileinfo', 'openssl'] as $ext) {
            $checks["Extensão {$ext}"] = extension_loaded($ext);
        }
        $tables = 0;
        try {
            $tables = (int) Database::query(
                'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ?', [config('database.name')]
            )->fetchColumn();
            $checks['Ligação à base de dados'] = true;
            $checks["Entidades principais criadas ({$tables})"] = $tables >= 3;
        } catch (\Throwable $e) {
            $checks['Ligação à base de dados'] = false;
        }
        $priv = BASE_PATH . '/' . trim((string) config('app.private_storage'), '/') . '/documents';
        $checks['Pasta privada de documentos gravável'] = is_dir($priv) && is_writable($priv);
        $checks['Sessão activa'] = session_status() === PHP_SESSION_ACTIVE;

        return $this->view('public/status', ['title' => 'Estado do sistema', 'checks' => $checks]);
    }

    public function csrfTest(Request $request): Response
    {
        Session::flash('ok', 'Pedido aceite: o token CSRF é válido.');
        return $this->redirect('/estado');
    }
}
