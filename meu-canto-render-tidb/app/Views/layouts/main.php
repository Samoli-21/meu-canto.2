<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F8F5F0">
    
    <title><?= e(($title ?? 'Meu Canto') . ' · Meu Canto') ?></title>
    <meta name="description" content="Um espaço de informação, bem-estar e orientação em saúde emocional, pensado para a realidade angolana.">
    
    <!-- Favicon e Folhas de Estilo -->
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('images/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/refinamentos.css')) ?>">
    
    <!-- Scripts principais carregados de forma diferida (defer) -->
    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
</head>
<?php $routeKey = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/'); $routeKey = preg_replace('/[^a-z0-9-]+/i', '-', $routeKey) ?: 'inicio'; ?>
<body class="mc-page mc-page-<?= e($routeKey) ?>" data-page-transition>
    <!-- Link de acessibilidade para saltar diretamente para o conteúdo principal -->
    <a class="skip-link" href="#conteudo">Saltar para o conteúdo</a>

    <!-- Barra de topo com mensagem comunitária e localização -->

    <!-- Cabeçalho Principal do Site -->
    <header class="site-header">
        <div class="container header-inner">
            <!-- Logótipo e Marca -->
            <a class="brand" href="<?= e(url('/')) ?>" aria-label="Meu Canto — página inicial">
                <img src="<?= e(asset('images/brand-mark.svg')) ?>" width="42" height="42" alt="">
                <span>meu<span class="brand-dot">.</span>canto</span>
            </a>

            <!-- Botão de Menu para Dispositivos Móveis (Hamburger) -->
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation" data-menu-toggle>
                <span class="menu-toggle-lines" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="sr-only">Abrir menu</span>
            </button>

            <!-- Navegação Principal -->
            <nav class="main-navigation" id="main-navigation" aria-label="Navegação principal" data-navigation>
                <a href="<?= e(url('/o-que-sinto')) ?>">O que sinto</a>
                <a href="<?= e(url('/recursos')) ?>">Recursos</a>
                <a href="<?= e(url('/escola-e-trabalho')) ?>">Escola e trabalho</a>
                <a href="<?= e(url('/profissionais')) ?>">Profissionais</a>
                <a href="<?= e(url('/sobre')) ?>">Sobre</a>
                <a href="<?= e(url('/canto')) ?>">Conversar com a Canto</a>
            </nav>

            <!-- Ações do Cabeçalho (Pesquisa e Botão de Emergência/Ajuda) -->
            <div class="header-actions">
                <details class="search-popover">
                    <summary aria-label="Abrir pesquisa" title="Pesquisar">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="10.8" cy="10.8" r="6.8"></circle>
                            <path d="m16 16 5 5"></path>
                        </svg>
                    </summary>
                    <form action="<?= e(url('/pesquisa')) ?>" method="get" role="search" class="search-popover-form">
                        <label for="site-search">O que procuras?</label>
                        <div class="search-input-row">
                            <input id="site-search" type="search" name="q" placeholder="Ex.: ansiedade, descanso…" required>
                            <button type="submit" aria-label="Pesquisar">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="10.8" cy="10.8" r="6.8"></circle>
                                    <path d="m16 16 5 5"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </details>
                
                <?php if (\App\Core\Session::get('user_id')): ?>
                    <a class="btn btn-account-small" href="<?= e(url(\App\Core\Session::get('user_role') === 'admin' ? '/admin' : '/conta')) ?>">A minha área</a>
                <?php else: ?>
                    <a class="btn btn-account-small" href="<?= e(url('/entrar')) ?>">Entrar</a>
                <?php endif; ?>
                <a class="btn btn-sos btn-sos-small" href="<?= e(url('/sos')) ?>">
                    <i class="sos-pulse"></i> 
                    <span class="btn-label-long">Procurar ajuda</span>
                    <span class="btn-label-short">Ajuda</span>
                </a>
            </div>
        </div>
    </header>

    <div class="page-transition-curtain" aria-hidden="true"></div>

    <!-- Mensagens Flash / Alertas Dinâmicos (Sessão) -->
    <?php if ($error = flash('error')): ?><div class="container"><div class="alert alert-error" role="alert"><?= e($error) ?></div></div><?php endif; ?>
    <?php if ($msg = flash('ok')): ?>
        <div class="container">
            <div class="alert alert-ok" role="status"><?= e($msg) ?></div>
        </div>
    <?php endif; ?>

    <!-- Conteúdo Principal da Página -->
    <main id="conteudo" class="site-main"><?= $content ?></main>

    <!-- Rodapé do Site -->
    <footer class="site-footer">
        <div class="container footer-main">
            <!-- Coluna da Marca e Descrição -->
            <div class="footer-brand-column">
                <a class="brand footer-brand" href="<?= e(url('/')) ?>">
                    <img src="<?= e(asset('images/brand-mark.svg')) ?>" width="42" height="42" alt="">
                    <span>meu<span class="brand-dot">.</span>canto</span>
                </a>
                <p>Um espaço para compreender emoções, encontrar informação e descobrir caminhos de apoio — com respeito pelo teu ritmo e pelo contexto de Angola.</p>
                <div class="social-row" aria-label="Redes sociais">
                    <span class="social-icon" title="Instagram">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.7" r=".8" class="icon-fill"></circle></svg>
                    </span>
                    <span class="social-icon" title="Facebook">
                        <svg viewBox="0 0 24 24"><path d="M14 21v-8h3l.5-3H14V8.1c0-.9.3-1.6 1.7-1.6h1.9V3.8c-.9-.1-1.8-.2-2.7-.2-2.7 0-4.5 1.6-4.5 4.6V10H7.5v3h2.9v8"></path></svg>
                    </span>
                    <span class="social-icon" title="YouTube">
                        <svg viewBox="0 0 24 24"><rect x="2.5" y="5" width="19" height="14" rx="4"></rect><path d="m10 9 5 3-5 3z" class="icon-fill"></path></svg>
                    </span>
                    <span class="social-icon" title="LinkedIn">
                        <svg viewBox="0 0 24 24"><path d="M5 9v10M5 5.5v.1M10 19v-6c0-2 1.2-3.3 3-3.3s3 1.3 3 3.3v6M10 10v9"></path></svg>
                    </span>
                    <span class="social-note">Canais oficiais em preparação</span>
                </div>
            </div>

            <!-- Grupo de Links: Explorar -->
            <div class="footer-links-group">
                <h2>Explorar</h2>
                <a href="<?= e(url('/o-que-sinto')) ?>">O que sinto</a>
                <a href="<?= e(url('/recursos')) ?>">Biblioteca de recursos</a>
                <a href="<?= e(url('/escola-e-trabalho')) ?>">Escola e trabalho</a>
                <a href="<?= e(url('/profissionais')) ?>">Encontrar apoio</a>
            </div>

            <!-- Grupo de Links: O Projeto -->
            <div class="footer-links-group">
                <h2>O projecto</h2>
                <a href="<?= e(url('/sobre')) ?>">Sobre o Meu Canto</a>
                <a href="<?= e(url('/privacidade')) ?>">Privacidade e dados</a>
                <a href="<?= e(url('/acessibilidade')) ?>">Acessibilidade</a>
                <a href="<?= e(url('/estado')) ?>">Estado técnico</a>
            </div>

            <!-- Cartão de Nota / Apoio Emocional no Rodapé -->
            <div class="footer-note-card">
                <span class="footer-note-icon">♥</span>
                <h2>Não tens de resolver tudo hoje.</h2>
                <p>Informação pode ser um primeiro passo. Pedir apoio também.</p>
                <a href="<?= e(url('/profissionais')) ?>" class="text-link">Conhecer opções de apoio <span>→</span></a>
            </div>
        </div>

        <!-- Informações de Copyright e Localização -->
        <div class="container footer-bottom">
            <span>© <?= date('Y') ?> Meu Canto. Conteúdo informativo.</span>
            <span>Desenvolvido para a realidade angolana · <strong>Africa/Luanda</strong></span>
        </div>

        <!-- Disclaimer / Aviso Legal -->
        <div class="container footer-disclaimer">
            O Meu Canto é uma plataforma informativa. Não realiza diagnósticos, não prescreve tratamentos e não substitui a avaliação de profissionais de saúde. Os contactos de emergência só serão publicados depois de confirmação por fontes oficiais.
        </div>
    </footer>
    <div class="page-loading-indicator" aria-hidden="true"></div>
</body>
</html>