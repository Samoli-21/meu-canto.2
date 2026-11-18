<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Controller, Database, Request, Response, Session};
use PDOException;

final class AuthController extends Controller
{
    public function loginPage(Request $request): Response
    {
        if (Session::get('user_id')) return $this->redirect(Session::get('user_role') === 'admin' ? '/admin' : '/conta');
        return $this->view('auth/login', ['title' => 'Entrar na tua conta']);
    }

    public function login(Request $request): Response
    {
        $email = strtolower(trim((string) $request->input('email', '')));
        $password = (string) $request->input('password', '');
        $attempts = (int) Session::get('login_attempts', 0);
        if ($attempts >= 8) {
            Session::flash('error', 'Foram feitas demasiadas tentativas. Aguarda alguns minutos e tenta novamente.');
            return $this->redirect('/entrar');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            Session::flash('error', 'Preenche o email e a palavra-passe.');
            return $this->redirect('/entrar');
        }

        $user = Database::query('SELECT id, nome, email, password_hash, tipo, estado FROM usuarios WHERE email = ? LIMIT 1', [$email])->fetch();
        if (!$user || $user['estado'] !== 'ativo' || !password_verify($password, $user['password_hash'])) {
            Session::set('login_attempts', $attempts + 1);
            Session::flash('error', 'Não foi possível iniciar sessão. Verifica os teus dados.');
            return $this->redirect('/entrar');
        }

        Session::set('login_attempts', 0);
        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        Session::set('user_name', $user['nome']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['tipo']);
        Database::query('UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?', [(int) $user['id']]);
        return $this->redirect($user['tipo'] === 'admin' ? '/admin' : '/conta');
    }

    public function registerPage(Request $request): Response
    {
        if (Session::get('user_id')) return $this->redirect('/conta');
        return $this->view('auth/register', ['title' => 'Criar conta']);
    }

    public function register(Request $request): Response
    {
        $name = trim((string) $request->input('nome', ''));
        $email = strtolower(trim((string) $request->input('email', '')));
        $phone = trim((string) $request->input('telefone', ''));
        $password = (string) $request->input('password', '');
        $confirm = (string) $request->input('password_confirmation', '');
        $length = function_exists('mb_strlen') ? mb_strlen($name) : strlen($name);

        if ($length < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 10 || $password !== $confirm) {
            Session::flash('error', 'Confirma o nome, o email e uma palavra-passe com pelo menos 10 caracteres.');
            return $this->redirect('/registar');
        }

        try {
            Database::query(
                "INSERT INTO usuarios (nome, email, telefone, password_hash, tipo, estado) VALUES (?, ?, ?, ?, 'utilizador', 'ativo')",
                [$name, $email, $phone !== '' ? $phone : null, password_hash($password, PASSWORD_DEFAULT)]
            );
        } catch (PDOException $e) {
            if ((string) $e->getCode() === '23000') {
                Session::flash('error', 'Já existe uma conta com este email.');
                return $this->redirect('/registar');
            }
            throw $e;
        }

        $id = (int) Database::pdo()->lastInsertId();
        Session::regenerate();
        Session::set('user_id', $id);
        Session::set('user_name', $name);
        Session::set('user_email', $email);
        Session::set('user_role', 'utilizador');
        Session::flash('ok', 'A tua conta foi criada. Bem-vindo ao Meu Canto.');
        return $this->redirect('/conta');
    }

    public function logout(Request $request): Response
    {
        Session::destroy();
        return $this->redirect('/');
    }

    public function account(Request $request): Response
    {
        $userId = (int) Session::get('user_id', 0);
        if ($userId < 1) return $this->redirect('/entrar');
        $user = Database::query('SELECT id, nome, email, telefone, tipo, criado_em FROM usuarios WHERE id = ? LIMIT 1', [$userId])->fetch();
        if (!$user) { Session::destroy(); return $this->redirect('/entrar'); }
        $professional = Database::query('SELECT id, estado, especialidade, nota_revisao FROM profissionais_saude WHERE usuario_id = ? ORDER BY id DESC LIMIT 1', [$userId])->fetch();
        return $this->view('user/account', ['title' => 'A tua área', 'user' => $user, 'professional' => $professional ?: null]);
    }
}
