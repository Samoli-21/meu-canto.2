<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Controller, Database, Request, Response, Session};

final class AdminController extends Controller
{
    private function guard(): ?Response
    {
        if (!Session::get('user_id')) return $this->redirect('/entrar');
        if (Session::get('user_role') !== 'admin') return $this->view('errors/error', ['status' => 403, 'message' => 'Não tens permissão para aceder à administração.'], 403);
        return null;
    }

    public function dashboard(Request $request): Response
    {
        if ($r = $this->guard()) return $r;
        $stats = [
            'users' => (int) Database::query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'utilizador'")->fetchColumn(),
            'pending' => (int) Database::query("SELECT COUNT(*) FROM profissionais_saude WHERE estado = 'pendente'")->fetchColumn(),
            'approved' => (int) Database::query("SELECT COUNT(*) FROM profissionais_saude WHERE estado = 'aprovado'")->fetchColumn(),
            'contents' => (int) Database::query("SELECT COUNT(*) FROM conteudos_site WHERE estado = 'publicado'")->fetchColumn(),
        ];
        $recent = Database::query('SELECT id, nome, especialidade, estado, submetido_em FROM profissionais_saude ORDER BY submetido_em DESC LIMIT 6')->fetchAll();
        return $this->view('admin/dashboard', ['title' => 'Painel administrativo', 'stats' => $stats, 'recent' => $recent], 200, 'admin');
    }

    public function professionals(Request $request): Response
    {
        if ($r = $this->guard()) return $r;
        $filter = (string) $request->input('estado', 'pendente');
        $allowed = ['pendente', 'aprovado', 'rejeitado', 'suspenso', 'todos'];
        if (!in_array($filter, $allowed, true)) $filter = 'pendente';
        $sql = 'SELECT * FROM profissionais_saude';
        $params = [];
        if ($filter !== 'todos') { $sql .= ' WHERE estado = ?'; $params[] = $filter; }
        $sql .= ' ORDER BY submetido_em DESC';
        $items = Database::query($sql, $params)->fetchAll();
        return $this->view('admin/professionals', ['title' => 'Profissionais de saúde', 'items' => $items, 'filter' => $filter], 200, 'admin');
    }

    public function decideProfessional(Request $request): Response
    {
        if ($r = $this->guard()) return $r;
        $id = (int) ($request->params['id'] ?? 0);
        $decision = (string) $request->input('decisao', '');
        $note = trim((string) $request->input('nota_revisao', ''));
        if (!in_array($decision, ['aprovado', 'rejeitado', 'suspenso'], true) || $id < 1) {
            Session::flash('error', 'A decisão indicada não é válida.');
            return $this->redirect('/admin/profissionais');
        }
        Database::query('UPDATE profissionais_saude SET estado = ?, nota_revisao = ?, revisor_id = ?, revisto_em = NOW() WHERE id = ?',
            [$decision, $note !== '' ? $note : null, (int) Session::get('user_id'), $id]);
        Session::flash('ok', 'O estado do profissional foi actualizado.');
        return $this->redirect('/admin/profissionais?estado=' . rawurlencode($decision));
    }

    public function contents(Request $request): Response
    {
        if ($r = $this->guard()) return $r;
        $items = Database::query('SELECT id, slug, tipo, titulo, estado, actualizado_em FROM conteudos_site ORDER BY actualizado_em DESC')->fetchAll();
        return $this->view('admin/contents', ['title' => 'Conteúdos do site', 'items' => $items], 200, 'admin');
    }

    public function newContent(Request $request): Response
    {
        if ($r = $this->guard()) return $r;
        return $this->view('admin/content-form', ['title' => 'Novo conteúdo', 'item' => null], 200, 'admin');
    }

    public function editContent(Request $request): Response
    {
        if ($r = $this->guard()) return $r;
        $id = (int) ($request->params['id'] ?? 0);
        $item = Database::query('SELECT * FROM conteudos_site WHERE id = ?', [$id])->fetch();
        if (!$item) return $this->view('errors/error', ['status' => 404, 'message' => 'Conteúdo não encontrado.'], 404);
        return $this->view('admin/content-form', ['title' => 'Editar conteúdo', 'item' => $item], 200, 'admin');
    }

    public function saveContent(Request $request): Response
    {
        if ($r = $this->guard()) return $r;
        $id = (int) $request->input('id', 0);
        $title = trim((string) $request->input('titulo', ''));
        $slug = $this->slug((string) $request->input('slug', '') ?: $title);
        $type = (string) $request->input('tipo', 'artigo');
        $summary = trim((string) $request->input('resumo', ''));
        $body = trim((string) $request->input('corpo', ''));
        $image = trim((string) $request->input('imagem_url', ''));
        $status = (string) $request->input('estado', 'rascunho');
        $order = (int) $request->input('ordem', 0);
        if ($title === '' || $slug === '' || $body === '' || !in_array($type, ['artigo', 'recurso', 'pagina', 'destaque'], true) || !in_array($status, ['rascunho', 'publicado', 'arquivado'], true)) {
            Session::flash('error', 'Preenche o título, o conteúdo e confirma as opções do formulário.');
            return $this->redirect($id > 0 ? '/admin/conteudos/' . $id . '/editar' : '/admin/conteudos/novo');
        }
        if ($image !== '' && (!filter_var($image, FILTER_VALIDATE_URL) || strtolower((string) parse_url($image, PHP_URL_SCHEME)) !== 'https')) {
            Session::flash('error', 'A imagem deve usar um endereço HTTPS válido.');
            return $this->redirect($id > 0 ? '/admin/conteudos/' . $id . '/editar' : '/admin/conteudos/novo');
        }
        if ($id > 0) {
            Database::query('UPDATE conteudos_site SET slug=?, tipo=?, titulo=?, resumo=?, corpo=?, imagem_url=?, estado=?, ordem=? WHERE id=?',
                [$slug, $type, $title, $summary ?: null, $body, $image ?: null, $status, $order, $id]);
        } else {
            Database::query('INSERT INTO conteudos_site (slug,tipo,titulo,resumo,corpo,imagem_url,estado,ordem,autor_id) VALUES (?,?,?,?,?,?,?,?,?)',
                [$slug, $type, $title, $summary ?: null, $body, $image ?: null, $status, $order, (int) Session::get('user_id')]);
        }
        Session::flash('ok', 'Conteúdo guardado com sucesso.');
        return $this->redirect('/admin/conteudos');
    }

    public function deleteContent(Request $request): Response
    {
        if ($r = $this->guard()) return $r;
        $id = (int) ($request->params['id'] ?? 0);
        Database::query('DELETE FROM conteudos_site WHERE id = ?', [$id]);
        Session::flash('ok', 'Conteúdo removido.');
        return $this->redirect('/admin/conteudos');
    }

    private function slug(string $value): string
    {
        $value = function_exists('iconv') ? (string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) : $value;
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }
}
