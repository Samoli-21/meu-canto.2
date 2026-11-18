<section class="page-hero page-hero-soft">
    <div class="container page-hero-inner page-hero-split">
        <div>
            <div class="breadcrumbs">
                <a href="<?= e(url('/')) ?>">Início</a>
                <span>›</span>
                <span>O que sinto</span>
            </div>
            <div class="eyebrow">UM PONTO DE PARTIDA</div>
            <h1>O que estás a <em>sentir?</em></h1>
            <p>Não precisas de saber exactamente o que se passa. Escolhe o tema que mais se aproxima da tua experiência e começa por explorar informação simples.</p>
            <div class="page-hero-stamp">♡ <span>Sem julgamentos</span></div>
        </div>

        <!-- Coluna da direita: Ilustração principal -->
        <div class="hero-art-wrap">
            <div class="hero-art-blob blob-one"></div>
            <div class="hero-art-blob blob-two"></div>
            <img class="hero-art" src="<?= e(asset('images/Mulher-com-coração.jpeg')) ?>" alt="Ilustração de pessoas num espaço acolhedor, rodeadas por plantas e formas suaves.">
        </div>
    </div>
</section>

<section class="section section-page-content">
    <div class="container">
        <div class="section-heading section-heading-split">
            <div>
                <h2>Talvez encontres aqui uma <em>palavra.</em></h2>
                <p>As emoções não são caixas fechadas. Podes sentir várias coisas ao mesmo tempo — e isso também merece espaço.</p>
            </div>
            <div class="content-note">
                Se nenhum tema corresponder ao que sentes, podes explorar a biblioteca ou procurar apoio.
            </div>
        </div>

        <div class="feeling-grid feeling-grid-page">
            <?php
            $symbols = [
                'ansiedade' => '〰',
                'tristeza'  => '☁',
                'stress'    => '⌁',
                'solidao'   => '♡',
                'cansaco'   => '◒',
                'mudanca'   => '✳'
            ];
            $colours = [
                'ansiedade' => 'feeling-lilac',
                'tristeza'  => 'feeling-blue',
                'stress'    => 'feeling-peach',
                'solidao'   => 'feeling-green',
                'cansaco'   => 'feeling-yellow',
                'mudanca'   => 'feeling-pink'
            ];
            foreach ($topics as $slug => $t):
            ?>
                <a class="feeling-card <?= e($colours[$slug]) ?>" href="<?= e(url('/tema/' . $slug)) ?>">
                    <span class="feeling-symbol"><?= e($symbols[$slug]) ?></span>
                    <span class="feeling-card-body">
                        <span class="feeling-tag">EXPLORAR TEMA</span>
                        <strong><?= e($t['title']) ?></strong>
                        <small><?= e($t['summary']) ?></small>
                        <span class="card-arrow">↗</span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="callout callout-neutral">
            <span class="callout-icon">✦</span>
            <div>
                <strong>Uma nota importante</strong>
                <p>Estes conteúdos ajudam a informar e a reflectir. Não servem para identificar doenças nem para fazer diagnósticos.</p>
            </div>
        </div>
    </div>
</section>