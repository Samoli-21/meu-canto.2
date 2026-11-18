<article class="content-article-page">
    <header class="content-article-hero">
        <div class="container"><div class="breadcrumbs"><a href="<?= e(url('/')) ?>">Início</a><span>›</span><a href="<?= e(url('/recursos')) ?>">Explorar</a><span>›</span><span><?= e($item['tipo']) ?></span></div><span class="eyebrow"><?= e(strtoupper($item['tipo'])) ?> · MEU CANTO</span><h1><?= e($item['titulo']) ?></h1><?php if (!empty($item['resumo'])): ?><p><?= e($item['resumo']) ?></p><?php endif; ?></div>
    </header>
    <div class="container content-article-layout">
        <div class="content-article-body">
            <?php if (!empty($item['imagem_url'])): ?><img class="content-cover" src="<?= e($item['imagem_url']) ?>" alt="" loading="lazy"><?php endif; ?>
            <div class="content-rich-text"><?= nl2br(e($item['corpo'])) ?></div>
            <p class="article-disclaimer">Este material é informativo e não substitui uma avaliação individual por um profissional qualificado.</p>
            <a class="btn btn-outline" href="<?= e(url('/recursos')) ?>">Voltar aos recursos</a>
        </div>
        <aside class="content-article-aside"><span>♡</span><h2>Vai ao teu ritmo.</h2><p>Escolhe apenas a informação que te é útil neste momento.</p><a href="<?= e(url('/canto')) ?>" class="text-link">Conversar com a Canto →</a></aside>
    </div>
</article>
