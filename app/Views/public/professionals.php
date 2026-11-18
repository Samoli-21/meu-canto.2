<section class="professional-hero">
    <div class="container professional-hero-inner">
        <div class="breadcrumbs"><a href="<?= e(url('/')) ?>">Início</a><span>›</span><span>Profissionais</span></div>
        <span class="eyebrow">APOIO COM CREDENCIAIS REVISTAS</span>
        <h1>Encontra um espaço de escuta com <em>confiança.</em></h1>
        <p>Conhece os profissionais aprovados pelo Meu Canto. Cada perfil público passa por análise administrativa antes de aparecer no directório.</p>
        <a class="btn btn-primary" href="#directorio">Ver profissionais <span>↓</span></a>
    </div>
    <div class="professional-hero-orbit professional-orbit-one"></div><div class="professional-hero-orbit professional-orbit-two"></div>
</section>
<section class="section directory-section" id="directorio">
    <div class="container">
        <div class="section-heading"><div class="eyebrow">DIRECTÓRIO</div><h2>Profissionais com estado <em>aprovado.</em></h2><p>Confirma directamente com o profissional a disponibilidade, os valores e as condições de atendimento.</p></div>
        <?php if (!empty($professionals)): ?>
        <div class="professional-grid">
            <?php foreach ($professionals as $person): ?>
            <article class="professional-card">
                <div class="professional-card-top">
                    <?php if (!empty($person['foto_url'])): ?><img src="<?= e($person['foto_url']) ?>" alt="Fotografia de <?= e($person['nome']) ?>" loading="lazy"><?php else: ?><span class="professional-placeholder">♡</span><?php endif; ?>
                    <span class="verified-label">✓ Perfil aprovado</span>
                </div>
                <div class="professional-card-body"><span class="professional-specialty"><?= e($person['especialidade']) ?></span><h3><?= e($person['nome']) ?></h3><p><?= e($person['biografia']) ?></p>
                    <div class="professional-meta"><span>⌖ <?= e(trim(($person['municipio'] ?? '') . ', ' . ($person['provincia'] ?? ''), ', ')) ?></span><span>◷ <?= e(ucfirst($person['modalidade'])) ?></span></div>
                    <?php if (!empty($person['formacao'])): ?><small><?= e($person['formacao']) ?></small><?php endif; ?>
                    <div class="professional-contact"><a href="mailto:<?= e($person['email']) ?>">Enviar email</a><a href="tel:<?= e($person['telefone']) ?>">Contactar</a></div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="directory-empty"><span class="directory-empty-icon">♧</span><h3>Estamos a preparar o directório.</h3><p>Ainda não existem perfis aprovados para apresentação pública. Os profissionais só serão exibidos depois da revisão da equipa Meu Canto.</p></div>
        <?php endif; ?>
    </div>
</section>
<section class="section professional-apply-section" id="candidatura">
    <div class="container professional-apply-grid">
        <div class="professional-apply-copy"><span class="eyebrow">FAZER PARTE DO DIRECTÓRIO</span><h2>És profissional de saúde? <em>Apresenta a tua candidatura.</em></h2><p>Envia os teus dados de contacto, especialidade e número de cédula. A candidatura ficará pendente até à verificação. O envio não garante aprovação nem publicação.</p><div class="apply-steps"><span><b>01</b> Submissão de dados</span><span><b>02</b> Análise das credenciais</span><span><b>03</b> Aprovação e publicação</span></div></div>
        <form class="professional-form" method="post" action="<?= e(url('/profissionais/candidatar')) ?>">
            <?= csrf_field() ?><h3>Formulário de candidatura</h3><p>Os campos com * são obrigatórios.</p>
            <div class="form-grid"><label>Nome completo *<input name="nome" required maxlength="150"></label><label>Email profissional *<input type="email" name="email" required maxlength="190"></label><label>Telefone *<input name="telefone" required maxlength="30"></label><label>N.º de cédula profissional *<input name="cedula_profissional" required maxlength="80"></label><label>Especialidade *<input name="especialidade" required maxlength="140" placeholder="Ex.: Psicologia clínica"></label><label>Formação académica<input name="formacao" maxlength="255"></label><label>Anos de experiência<input type="number" name="experiencia_anos" min="0" max="70"></label><label>Município<input name="municipio" maxlength="100"></label><label>Província<input name="provincia" value="Luanda" maxlength="100"></label><label>Modalidade<select name="modalidade"><option value="presencial">Presencial</option><option value="online">Online</option><option value="ambos">Presencial e online</option></select></label><label class="form-span">Morada do consultório<input name="morada_consultorio" maxlength="255"></label><label class="form-span">Fotografia (URL https opcional)<input type="url" name="foto_url" placeholder="https://..."></label><label class="form-span">Breve biografia e abordagem profissional *<textarea name="biografia" rows="4" required minlength="30" maxlength="2500"></textarea></label></div>
            <label class="consent-check"><input type="checkbox" name="consent" value="1" required> Confirmo que os dados submetidos são verdadeiros e autorizo a sua análise para candidatura ao directório.</label>
            <button class="btn btn-primary" type="submit">Enviar candidatura <span>→</span></button>
            <small>Não envies documentos de identificação ou dados clínicos de pacientes neste formulário.</small>
        </form>
    </div>
</section>
