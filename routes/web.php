<?php
declare(strict_types=1);

use App\Controllers\{AdminController, AuthController, ChatController, HomeController, PublicController};
use App\Core\{Request, Response};
/** @var App\Core\Router $router */

// Endpoint simples para health check do Render (não depende do TiDB).
$router->get('/healthz', static fn (Request $request): Response => Response::html('ok', 200));

// Páginas públicas
$router->get('/', [PublicController::class, 'home']);
$router->get('/o-que-sinto', [PublicController::class, 'feelings']);
$router->get('/tema/{slug}', [PublicController::class, 'topic']);
$router->get('/recursos', [PublicController::class, 'resources']);
$router->get('/escola-e-trabalho', [PublicController::class, 'schoolWork']);
$router->get('/profissionais', [PublicController::class, 'professionals']);
$router->post('/profissionais/candidatar', [PublicController::class, 'submitProfessional']);
$router->get('/informacao/{slug}', [PublicController::class, 'content']);
$router->get('/sos', [PublicController::class, 'sos']);
$router->get('/sobre', [PublicController::class, 'about']);
$router->get('/privacidade', [PublicController::class, 'privacy']);
$router->get('/acessibilidade', [PublicController::class, 'accessibility']);
$router->get('/pesquisa', [PublicController::class, 'search']);

// Conta, autenticação e assistente Canto
$router->get('/entrar', [AuthController::class, 'loginPage']);
$router->post('/entrar', [AuthController::class, 'login']);
$router->get('/registar', [AuthController::class, 'registerPage']);
$router->post('/registar', [AuthController::class, 'register']);
$router->post('/sair', [AuthController::class, 'logout']);
$router->get('/conta', [AuthController::class, 'account']);
$router->get('/canto', [ChatController::class, 'page']);
$router->post('/api/canto', [ChatController::class, 'reply']);

// Administração
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/profissionais', [AdminController::class, 'professionals']);
$router->post('/admin/profissionais/{id}/decisao', [AdminController::class, 'decideProfessional']);
$router->get('/admin/conteudos', [AdminController::class, 'contents']);
$router->get('/admin/conteudos/novo', [AdminController::class, 'newContent']);
$router->get('/admin/conteudos/{id}/editar', [AdminController::class, 'editContent']);
$router->post('/admin/conteudos/guardar', [AdminController::class, 'saveContent']);
$router->post('/admin/conteudos/{id}/apagar', [AdminController::class, 'deleteContent']);

// Diagnóstico técnico, só em modo debug
$router->get('/estado', [HomeController::class, 'status']);
$router->post('/estado/csrf', [HomeController::class, 'csrfTest']);
