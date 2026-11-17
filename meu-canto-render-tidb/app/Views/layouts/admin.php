<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f5ef">
    <title><?= e(($title ?? 'Administração') . ' · Meu Canto') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('images/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/refinamentos.css')) ?>">
    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
</head>
<body class="mc-admin-page" data-page-transition>
<div class="page-transition-curtain" aria-hidden="true"></div>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="<?= e(url('/admin')) ?>"><img src="<?= e(asset('images/brand-mark.svg')) ?>" alt=""><span>meu<span>.</span>canto<small>ADMINISTRAÇÃO</small></span></a>
        <div class="admin-user-chip"><span class="admin-user-avatar">MC</span><span><strong><?= e(\App\Core\Session::get('user_name', 'Administrador')) ?></strong><small>Administrador</small></span></div>
        <nav class="admin-navigation" aria-label="Administração">
            <a href="<?= e(url('/admin')) ?>" class="<?= (($_SERVER['REQUEST_URI'] ?? '') === '/admin') ? 'is-current' : '' ?>">⌂ <span>Visão geral</span></a>
            <a href="<?= e(url('/admin/profissionais')) ?>">♧ <span>Profissionais</span></a>
            <a href="<?= e(url('/admin/conteudos')) ?>">▤ <span>Conteúdos do site</span></a>
        </nav>
        <div class="admin-sidebar-bottom"><a href="<?= e(url('/')) ?>">↗ Ver site público</a><form method="post" action="<?= e(url('/sair')) ?>"><?= csrf_field() ?><button type="submit">Sair da administração <span>→</span></button></form></div>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar"><div><span class="eyebrow">MEU CANTO · ESPAÇO DE GESTÃO</span><strong><?= e($title ?? 'Administração') ?></strong></div><div class="admin-topbar-actions"><a href="<?= e(url('/')) ?>" class="admin-view-site">Abrir site ↗</a><form method="post" action="<?= e(url('/sair')) ?>"><?= csrf_field() ?><button class="admin-topbar-logout" type="submit">Terminar sessão</button></form></div></header>
        <?php if ($error = flash('error')): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
        <?php if ($msg = flash('ok')): ?><div class="alert alert-ok" role="status"><?= e($msg) ?></div><?php endif; ?>
        <div class="admin-content"><?= $content ?></div>
    </main>
</div>
</body>
</html>
