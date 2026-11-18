<section class="page-hero page-hero-resources">
    <div class="container page-hero-inner page-hero-split">
        <div>
            <div class="breadcrumbs"><a href="<?= e(url('/')) ?>">Início</a><span>›</span><span>Biblioteca</span></div>
            <div class="eyebrow">PEQUENOS RECURSOS, AO TEU RITMO</div>
            <h1>Ideias que podem <em>acompanhar-te.</em></h1>
            <p>Leituras, exercícios e materiais para explorar o bem-estar com calma. Escolhe o formato que faz sentido para o teu momento.</p>
            <a class="btn btn-primary" href="#biblioteca">Explorar recursos <span>↓</span></a>
        </div>
        <div class="hero-art-wrap">
            <div class="hero-art-blob blob-one"></div><div class="hero-art-blob blob-two"></div>
            <img class="hero-art" src="<?= e(asset('images/recursos.png')) ?>" alt="Ilustração tranquila sobre aprendizagem e autocuidado.">
        </div>
    </div>
</section>

<section class="section section-page-content" id="biblioteca">
    <div class="container">
        <div class="section-heading section-heading-left">
            <div class="eyebrow">BIBLIOTECA DO MEU CANTO</div>
            <h2>Encontra um ponto de <em>partida.</em></h2>
            <p>Os conteúdos publicados pela equipa aparecem aqui. Os materiais editoriais iniciais ajudam a explorar a biblioteca enquanto novos recursos são preparados.</p>
        </div>
        <nav class="resource-filter-tabs" aria-label="Filtrar recursos">
            <a class="<?= $selectedType === '' ? 'is-active' : '' ?>" href="<?= e(url('/recursos')) ?>">Todos</a>
            <?php foreach (['Leitura','Prática','Ver','Descarregar','Ouvir'] as $filter): ?>
                <a class="<?= $selectedType === $filter ? 'is-active' : '' ?>" href="<?= e(url('/recursos?tipo=' . rawurlencode($filter))) ?>"><?= e($filter) ?></a>
            <?php endforeach; ?>
        </nav>
        <?php if (!empty($resources)): ?>
            <div class="resource-library-grid">
                <?php foreach ($resources as $item): ?>
                    <article class="resource-library-card <?= e($item['tone'] ?? 'resource-mint') ?>">
                        <div class="resource-library-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?= e($item['image_url']) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <span aria-hidden="true"><?= e($item['icon'] ?? '✦') ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="resource-library-body">
                            <span class="resource-kind-label"><?= e($item['type'] ?? 'Recurso') ?></span>
                            <h3><?= e($item['title']) ?></h3>
                            <p><?= e($item['summary']) ?></p>
                            <div class="resource-library-footer">
                                <small><?= e($item['duration'] ?? ($item['format'] ?? 'Explorar')) ?></small>
                                <?php if (!empty($item['slug'])): ?>
                                    <a href="<?= e(url('/informacao/' . rawurlencode($item['slug']))) ?>">Ler conteúdo <span>↗</span></a>
                                <?php else: ?>
                                    <a href="<?= e(url('/profissionais')) ?>">Explorar <span>↗</span></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><span>✧</span><h2>Ainda não há recursos neste formato.</h2><p>Experimenta outro filtro ou volta a ver todos os materiais.</p><a class="btn btn-primary" href="<?= e(url('/recursos')) ?>">Ver todos os recursos</a></div>
        <?php endif; ?>
        <div class="resource-editorial-note"><span>✦</span><p><strong>Um lembrete:</strong> os recursos são materiais informativos e não substituem uma avaliação ou acompanhamento por um profissional de saúde.</p></div>
    </div>
</section>
