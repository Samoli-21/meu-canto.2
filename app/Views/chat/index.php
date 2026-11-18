<section class="canto-page"><div class="container canto-shell">
    <div class="canto-intro"><a class="canto-back" href="<?= e(url('/')) ?>">← Voltar ao Meu Canto</a><span class="eyebrow">UM ESPAÇO DE ESCUTA INICIAL</span><h1>Podemos começar por uma <em>conversa.</em></h1><p>A Canto ajuda-te a explorar o que sentes e a encontrar informação. Partilha apenas aquilo com que te sentes confortável.</p><div class="canto-trust-list"><span>♡ Sem julgamentos</span><span>⌁ Ao teu ritmo</span><span>✳ Informação e orientação</span></div><div class="canto-note"><strong>Uma nota importante</strong><p>A Canto não faz diagnósticos nem substitui um profissional. Se estiveres em perigo imediato, procura ajuda presencial num serviço de urgência e aproxima-te de alguém de confiança.</p></div></div>
    <div class="canto-chat-card" data-chat-card>
        <header class="canto-chat-header"><span class="canto-avatar"><img src="<?= e(asset('images/assistant-mark.svg')) ?>" alt=""></span><span><strong>Olá, sou a Canto.</strong><small>Assistente de exploração do Meu Canto</small></span><i class="canto-online-dot" aria-label="Disponível"></i></header>
        <div class="canto-chat-content" id="canto-messages" aria-live="polite" aria-label="Conversa com a Canto">
            <div class="canto-day-divider"><span>UM PRIMEIRO PASSO</span></div>
            <div class="canto-message-row canto-message-assistant"><span class="canto-message-avatar"><img src="<?= e(asset('images/assistant-mark.svg')) ?>" alt=""></span><div class="canto-message">Olá! Às vezes, começar é a parte mais difícil. O que gostavas de compreender melhor hoje?</div></div>
            <div class="canto-quick-prompts"><button type="button" data-prompt="Quero perceber melhor a ansiedade.">Quero perceber a ansiedade</button><button type="button" data-prompt="Tenho sentido muita pressão ultimamente.">Tenho sentido muita pressão</button><button type="button" data-prompt="Procuro um exercício simples para fazer agora.">Procuro um exercício simples</button></div>
        </div>
        <form class="canto-composer" id="canto-form" method="post" action="<?= e(url('/api/canto')) ?>" data-avatar="<?= e(asset('images/assistant-mark.svg')) ?>">
            <?= csrf_field() ?><label class="sr-only" for="canto-input">Escreve a tua mensagem</label><textarea id="canto-input" name="message" rows="1" maxlength="2000" placeholder="Escolhe um tema para começar..." required></textarea><button type="submit" aria-label="Enviar mensagem"><span>↑</span></button>
        </form>
        <div class="canto-chat-footer"><span>⌑ Não guardamos o histórico no Meu Canto.</span><span id="canto-status">Com IA ligada, a mensagem segue para o fornecedor configurado.</span></div>
    </div>
</div></section>
<script src="<?= e(asset('js/chat.js')) ?>" defer></script>
