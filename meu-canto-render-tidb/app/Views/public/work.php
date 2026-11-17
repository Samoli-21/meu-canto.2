<section class="page-hero page-hero-work">
    <div class="container page-hero-inner page-hero-split">
        <!-- Coluna da esquerda: Texto -->
        <div>
            <div class="breadcrumbs">
                <a href="<?= e(url('/')) ?>">Início</a>
                <span>›</span>
                <span>Escola e trabalho</span>
            </div>
            <div class="eyebrow">A VIDA NÃO PÁRA À PORTA DA ESCOLA OU DO TRABALHO</div>
            <h1>Bem-estar também é ter espaço para <em>aprender e trabalhar.</em></h1>
            <p>Pressões, relações, expectativas e mudanças fazem parte destas experiências. Informação e apoio podem ajudar a encontrar um caminho mais equilibrado.</p>
        </div>

        <!-- Coluna da direita: Ilustração principal -->
        <div class="hero-art-wrap">
            <div class="hero-art-blob blob-one"></div>
            <div class="hero-art-blob blob-two"></div>
            <img class="hero-art" src="<?= e(asset('images/Trabalho-escola.jpeg')) ?>" alt="Ilustração de pessoas num espaço acolhedor, rodeadas por plantas e formas suaves.">
        </div>
    </div>
</section>

<section class="section section-page-content">
    <div class="container">
        <div class="work-columns">
            <article class="work-feature work-school">
                <div class="work-illustration">
                    <span class="work-illustration-icon">✎</span>
                    <span class="work-illustration-orbit">✳</span>
                </div>
                <div class="eyebrow">NA ESCOLA E NA UNIVERSIDADE</div>
                <h2>Aprender sem esquecer de <em>cuidar de mim.</em></h2>
                <p>A escola e a universidade podem trazer novas oportunidades, mas também desafios emocionais e sociais. Não tens de lidar com tudo em silêncio.</p>
                <ul class="topic-link-list">
                    <li><a href="<?= e(url('/tema/stress')) ?>">Pressão dos testes e exames <span>→</span></a></li>
                    <li><a href="<?= e(url('/tema/solidao')) ?>">Amizades, pertença e isolamento <span>→</span></a></li>
                    <li><a href="<?= e(url('/tema/mudanca')) ?>">Transições e escolhas de futuro <span>→</span></a></li>
                    <li><a href="<?= e(url('/recursos')) ?>">Organização e hábitos de estudo <span>→</span></a></li>
                </ul>
            </article>

            <article class="work-feature work-career">
                <div class="work-illustration">
                    <span class="work-illustration-icon">⌂</span>
                    <span class="work-illustration-orbit">♡</span>
                </div>
                <div class="eyebrow">NO TRABALHO E NA PROCURA DE EMPREGO</div>
                <h2>O trabalho faz parte da vida, <em>não é a vida toda.</em></h2>
                <p>Responsabilidades, desemprego, ambiente profissional e incerteza podem afectar o bem-estar. Falar sobre essas experiências é legítimo.</p>
                <ul class="topic-link-list">
                    <li><a href="<?= e(url('/tema/stress')) ?>">Stress e sobrecarga profissional <span>→</span></a></li>
                    <li><a href="<?= e(url('/tema/ansiedade')) ?>">Insegurança e preocupação com o futuro <span>→</span></a></li>
                    <li><a href="<?= e(url('/tema/cansaco')) ?>">Cansaço e limites saudáveis <span>→</span></a></li>
                    <li><a href="<?= e(url('/tema/mudanca')) ?>">Mudança de carreira e desemprego <span>→</span></a></li>
                </ul>
            </article>
        </div>

        <div class="wide-quote">
            <span>“</span>
            <p>Um ambiente mais saudável começa quando as pessoas podem falar, ser ouvidas e encontrar apoio sem medo de julgamento.</p>
            <small>MEU CANTO · ESCOLA, TRABALHO E COMUNIDADE</small>
        </div>

        <div class="section-heading section-heading-split">
            <div>
                <h2>Recursos para o teu <em>contexto.</em></h2>
                <p>Explora materiais que ajudam a pensar em rotinas, relações e bem-estar.</p>
            </div>
            <a href="<?= e(url('/recursos')) ?>" class="text-link">Ver biblioteca <span>→</span></a>
        </div>
    </div>
</section>