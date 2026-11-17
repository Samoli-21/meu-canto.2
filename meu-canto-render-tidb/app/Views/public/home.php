<!-- =====================================================================
     SECÇÃO HERO (BANNER PRINCIPAL)
     Apresentação da plataforma, mensagem de acolhimento e botões de ação
     ===================================================================== -->
<section class="hero-section">
    <div class="container hero-grid">
        <!-- Coluna da esquerda: Textos e CTAs (Call to Action) -->
        <div class="hero-copy">
            <div class="eyebrow">
                <i class="eyebrow-line"></i> UM ESPAÇO DE CUIDADO E INFORMAÇÃO
            </div>
            <h1>Há espaço para <em>aquilo que sentes.</em></h1>
            <p class="hero-lead">
                Nem sempre é fácil encontrar as palavras certas. Aqui podes compreender melhor as tuas emoções, descobrir recursos úteis e conhecer caminhos de apoio — ao teu ritmo.
            </p>
            <div class="hero-actions">
                <a class="btn btn-primary btn-large" href="<?= e(url('/o-que-sinto')) ?>">
                    Começar pelo que sinto <span>→</span>
                </a>
                <a class="btn btn-quiet btn-large" href="<?= e(url('/sobre')) ?>">
                    Conhecer o Meu Canto
                </a>
            </div>
            <div class="hero-footnote">
                <span class="tiny-heart">♥</span> Sem julgamentos. Sem pressa. Um passo de cada vez.
            </div>
        </div>

        <!-- Coluna da direita: Ilustração principal e cartões flutuantes -->
        <div class="hero-art-wrap">
            <div class="hero-art-blob blob-one"></div>
            <div class="hero-art-blob blob-two"></div>
            <img class="hero-art" src="<?= e(asset('images/Home.jpeg')) ?>" alt="Ilustração de pessoas num espaço acolhedor, rodeadas por plantas e formas suaves.">
            
 
            <div class="art-caption">UM LUGAR PARA RESPIRAR</div>
        </div>
    </div>
</section>

<!-- =====================================================================
     FAIXA DE CONFIANÇA (PILARES DO PROJETO)
     ===================================================================== -->
<section class="trust-strip">
    <div class="container trust-grid">
        <div class="trust-item">
            <span class="trust-icon">✳</span>
            <span>
                <strong>Informação clara</strong>
                <small>Conteúdos para compreender melhor</small>
            </span>
        </div>
        <div class="trust-item">
            <span class="trust-icon">♡</span>
            <span>
                <strong>Respeito pelo teu ritmo</strong>
                <small>Cada pessoa tem o seu caminho</small>
            </span>
        </div>
        <div class="trust-item">
            <span class="trust-icon">☼</span>
            <span>
                <strong>Contexto angolano</strong>
                <small>Escola, trabalho, família e comunidade</small>
            </span>
        </div>
    </div>
</section>

<!-- =====================================================================
     SECÇÃO DE EMOÇÕES / TEMAS
     Grelha dinâmica gerada a partir do array $topics
     ===================================================================== -->
<section class="section section-feelings">
    <div class="container">
        <div class="section-heading section-heading-split">
            <div>
                <div class="eyebrow">COMEÇA POR AQUI</div>
                <h2>O que se passa <em>contigo?</em></h2>
                <p>Às vezes, escolher uma palavra já ajuda a organizar o que vai cá dentro. Explora um tema que se aproxima do que estás a viver.</p>
            </div>
            <a class="text-link" href="<?= e(url('/o-que-sinto')) ?>">
                Ver todos os temas <span>→</span>
            </a>
        </div>

        <div class="feeling-grid">
            <?php
            // Define a ordem fixa de exibição dos cartões
            $order = ['ansiedade', 'tristeza', 'stress', 'solidao', 'cansaco', 'mudanca'];
            
            // Metadados visuais para cada tema: [subtítulo/tag, ícone/símbolo, classe de cor]
            $meta = [
                'ansiedade' => ['Mente inquieta', '〰', 'feeling-lilac'],
                'tristeza'  => ['Dias mais pesados', '☁', 'feeling-blue'],
                'stress'    => ['Muita coisa ao mesmo tempo', '⌁', 'feeling-peach'],
                'solidao'   => ['Vontade de ligação', '♡', 'feeling-green'],
                'cansaco'   => ['Pouca energia', '◒', 'feeling-yellow'],
                'mudanca'   => ['Um novo capítulo', '✳', 'feeling-pink'],
            ];

            // Itera sobre os temas ordenados para renderizar cada cartão
            foreach ($order as $slug):
                $topic = $topics[$slug];
                [$tag, $symbol, $colour] = $meta[$slug];
            ?>
                <a class="feeling-card <?= e($colour) ?>" href="<?= e(url('/tema/' . $slug)) ?>">
                    <span class="feeling-symbol"><?= e($symbol) ?></span>
                    <span class="feeling-card-body">
                        <span class="feeling-tag"><?= e($tag) ?></span>
                        <strong><?= e($topic['title']) ?></strong>
                        <small><?= e($topic['summary']) ?></small>
                        <span class="card-arrow">↗</span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>

        <p class="gentle-note">
            <span>✦</span> Não precisas de encontrar a palavra perfeita. Explora apenas o que fizer sentido para ti.
        </p>
    </div>
</section>

<!-- =====================================================================
     SECÇÃO DE ÁREAS DO DIA A DIA
     Ligações para contextos específicos (Bem-estar, Escola, Trabalho, Comunidade)
     ===================================================================== -->
<section class="section section-areas">
    <div class="container">
        <div class="section-heading centered-heading">
            <div class="eyebrow">A VIDA ACONTECE EM MUITOS LUGARES</div>
            <h2>Também cuidamos do <em>dia a dia.</em></h2>
            <p>O bem-estar não vive separado das nossas relações, responsabilidades e experiências.</p>
        </div>

        <div class="areas-grid">
            <!-- Cartão: Bem-estar -->
            <a class="area-card" href="<?= e(url('/recursos')) ?>">
                <span class="area-art area-wellbeing"><span>☼</span></span>
                <div>
                    <span class="mini-label">BEM-ESTAR</span>
                    <h3>Cuidar de mim</h3>
                    <p>Autocuidado, descanso, confiança e relações saudáveis.</p>
                    <span class="text-link">Explorar <b>→</b></span>
                </div>
            </a>

            <!-- Cartão: Escola -->
            <a class="area-card" href="<?= e(url('/escola-e-trabalho')) ?>">
                <span class="area-art area-school"><span>✎</span></span>
                <div>
                    <span class="mini-label">ESCOLA</span>
                    <h3>Aprender com equilíbrio</h3>
                    <p>Pressão dos exames, amizades, bullying e mudanças.</p>
                    <span class="text-link">Explorar <b>→</b></span>
                </div>
            </a>

            <!-- Cartão: Trabalho -->
            <a class="area-card" href="<?= e(url('/escola-e-trabalho')) ?>">
                <span class="area-art area-work"><span>⌂</span></span>
                <div>
                    <span class="mini-label">TRABALHO</span>
                    <h3>Encontrar o meu ritmo</h3>
                    <p>Stress, procura de emprego, ambiente e propósito.</p>
                    <span class="text-link">Explorar <b>→</b></span>
                </div>
            </a>

            <!-- Cartão: Relações e Comunidade -->
            <a class="area-card" href="<?= e(url('/sobre')) ?>">
                <span class="area-art area-community"><span>♡</span></span>
                <div>
                    <span class="mini-label">RELAÇÕES E COMUNIDADE</span>
                    <h3>Não estamos isolados</h3>
                    <p>Família, amizades, cuidado e apoio mútuo.</p>
                    <span class="text-link">Explorar <b>→</b></span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- =====================================================================
     SECÇÃO DE RECURSOS EM DESTAQUE
     Grelha dinâmica gerada a partir do array $resources
     ===================================================================== -->
<section class="section section-resources">
    <div class="container">
        <div class="section-heading section-heading-split">
            <div>
                <div class="eyebrow">IDEIAS QUE PODES LEVAR CONTIGO</div>
                <h2>Recursos para diferentes <em>momentos.</em></h2>
                <p>Informação em formatos variados, para leres, ouvires ou experimentares quando te for conveniente.</p>
            </div>
            <a class="text-link" href="<?= e(url('/recursos')) ?>">
                Ver biblioteca completa <span>→</span>
            </a>
        </div>

        <div class="resource-grid">
            <?php foreach ($resources as $r): ?>
                <article class="resource-card">
                    <!-- Cabeçalho visual do recurso com cor e ícone dinâmicos -->
                    <div class="resource-visual <?= e($r['tone']) ?>">
                        <span class="resource-illustration-icon">
                            <?php // Mapeia o nome do ícone para um símbolo Unicode (fallback: '✦') ?>
                            <?= ['book' => '▤', 'leaf' => '✳', 'play' => '▷', 'check' => '✓'][$r['icon']] ?? '✦' ?>
                        </span>
                        <span class="resource-format"><?= e($r['format']) ?></span>
                    </div>
                    
                    <!-- Conteúdo textual do recurso -->
                    <div class="resource-copy">
                        <div class="resource-meta">
                            <span><?= e($r['type']) ?></span>
                            <span><?= e($r['duration']) ?></span>
                        </div>
                        <h3><?= e($r['title']) ?></h3>
                        <p><?= e($r['summary']) ?></p>
                        <a href="<?= e(url('/recursos')) ?>" class="text-link">
                            Explorar recurso <span>→</span>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($featuredContents)): ?>
<section class="section section-editorial" aria-labelledby="editorial-title">
    <div class="container">
        <div class="section-heading section-heading-split"><div><div class="eyebrow">CONTEÚDOS REVISTOS</div><h2>Ideias e informação para <em>levar contigo.</em></h2><p>Materiais publicados pela equipa do Meu Canto.</p></div><a class="text-link" href="<?= e(url('/recursos')) ?>">Explorar recursos <span>→</span></a></div>
        <div class="editorial-grid">
            <?php foreach ($featuredContents as $content): ?>
            <a class="editorial-card" href="<?= e(url('/informacao/' . $content['slug'])) ?>">
                <?php if (!empty($content['imagem_url'])): ?><img src="<?= e($content['imagem_url']) ?>" alt="" loading="lazy"><?php else: ?><span class="editorial-card-art">♡</span><?php endif; ?>
                <div class="editorial-card-copy"><span><?= e(ucfirst($content['tipo'])) ?></span><h3><?= e($content['titulo']) ?></h3><p><?= e($content['resumo']) ?></p><strong>Continuar a ler →</strong></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =====================================================================
     SECÇÃO DA ASSISTENTE DIGITAL ("CANTO")
     Pré-visualização visual (mockup) da futura funcionalidade de chat
     ===================================================================== -->
<section class="assistant-section">
    <div class="container assistant-panel">
        <!-- Texto explicativo -->
        <div class="assistant-copy">
            <div class="eyebrow eyebrow-light">
                <i class="eyebrow-line"></i> NÃO SABES POR ONDE COMEÇAR?
            </div>
            <h2>Podemos começar por uma <em>conversa.</em></h2>
            <p>A assistente do Meu Canto foi pensada para te orientar até à informação mais útil. Podes começar por dizer o que procuras — sem precisares de explicar tudo.</p>
            <a href="<?= e(url('/canto')) ?>" class="btn btn-light btn-large">
                Conversar com a Canto <span>→</span>
            </a>
            <span class="assistant-footnote">A conversa é opcional. Não substitui acompanhamento clínico nem serviços de urgência.</span>
        </div>

        <!-- Janela simulada do chat -->
        <div class="assistant-preview">
            <div class="assistant-window-top">
                <span class="assistant-avatar">
                    <img src="<?= e(asset('images/assistant-mark.svg')) ?>" alt="">
                </span>
                <span>
                    <strong>Olá, sou a Canto.</strong>
                    <small>Assistente de exploração do Meu Canto</small>
                </span>
                <i class="online-indicator"></i>
            </div>

            <div class="assistant-chat-area">
                <div class="chat-date">UM PRIMEIRO PASSO</div>
                <div class="chat-bubble">
                    Olá! Às vezes, começar é a parte mais difícil. O que gostavas de compreender melhor hoje?
                </div>
                <!-- Sugestões rápidas clicáveis -->
                <div class="chat-suggestions">
                    <a href="<?= e(url('/tema/ansiedade')) ?>">Quero perceber a ansiedade</a>
                    <a href="<?= e(url('/tema/stress')) ?>">Tenho sentido muita pressão</a>
                    <a href="<?= e(url('/recursos')) ?>">Procuro um exercício simples</a>
                </div>
            </div>

            <div class="assistant-input-mock">
                <span>Escolhe um tema para começar…</span>
                <b>↑</b>
            </div>
            <div class="assistant-privacy">
                ⌑ A conversa real será opcional e terá aviso de privacidade.
            </div>
        </div>
    </div>
</section>

<!-- =====================================================================
     SECÇÃO DE PEDIDO DE APOIO E SOS
     Diretório de profissionais e linha de ajuda urgente
     ===================================================================== -->
<section class="section section-support">
    <div class="container support-grid">
        <!-- Elemento gráfico decorativo -->
        <div class="support-art">
            <div class="support-circle">♡</div>
            <i class="support-sun"></i>
            <i class="support-leaf leaf-a"></i>
            <i class="support-leaf leaf-b"></i>
            <p>Há várias formas<br>de pedir apoio.</p>
        </div>

        <!-- Informação e botões de encaminhamento -->
        <div class="support-copy">
            <div class="eyebrow">NÃO PRECISAS DE FAZER TUDO SOZINHO</div>
            <h2>Às vezes, o próximo passo é <em>pedir apoio.</em></h2>
            <p>Conversar com alguém de confiança pode ser um começo. Quando precisares de acompanhamento especializado, procura um profissional qualificado e confirma sempre as suas credenciais.</p>
            <div class="support-actions">
                <a class="btn btn-primary" href="<?= e(url('/profissionais')) ?>">
                    Conhecer o directório <span>→</span>
                </a>
                <a class="btn btn-outline" href="<?= e(url('/sos')) ?>">
                    Preciso de ajuda agora
                </a>
            </div>
            <small>O directório estará disponível quando a validação de profissionais estiver concluída.</small>
        </div>
    </div>
</section>

<!-- =====================================================================
     SECÇÃO SOBRE A LINGUAGEM E REVISÃO EDITORIAL
     ===================================================================== -->
<section class="language-section">
    <div class="container language-inner">
        <div>
            <span class="language-kicker">LINGUAGEM CLARA</span>
            <h2>Uma mensagem de cuidado deve ser <em>fácil de entender.</em></h2>
            <p>Escrevemos em português simples e próximo, sem termos técnicos desnecessários. Os conteúdos serão revistos por profissionais de saúde mental.</p>
        </div>

        <!-- Etiquetas de compromisso editorial -->
        <div class="language-pills">
            <span>Português claro</span>
            <span>Sem jargão</span>
            <span>Sem julgamento</span>
            <span>Revisão profissional <small>em preparação</small></span>
        </div>
    </div>
</section>

<!-- =====================================================================
     FAIXA FINAL (CALL TO ACTION DE ENCERRAMENTO)
     ===================================================================== -->
<section class="newsletter-band">
    <div class="container newsletter-inner">
        <div>
            <span class="eyebrow">UM LEMBRETE PARA CUIDAR DE TI</span>
            <h2>Vai ao teu ritmo. <em>Estamos a construir este espaço.</em></h2>
            <p>Novos artigos, exercícios e materiais serão publicados depois de revisão editorial.</p>
        </div>
        <a href="<?= e(url('/recursos')) ?>" class="btn btn-primary">
            Ver recursos disponíveis <span>→</span>
        </a>
    </div>
</section>