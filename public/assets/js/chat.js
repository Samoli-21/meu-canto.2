(() => {
  const form = document.getElementById('canto-form');
  const input = document.getElementById('canto-input');
  const messages = document.getElementById('canto-messages');
  const status = document.getElementById('canto-status');
  if (!form || !input || !messages) return;

  const conversation = [];
  const scrollToBottom = () => { messages.scrollTop = messages.scrollHeight; };
  const addMessage = (text, who) => {
    const row = document.createElement('div');
    row.className = `canto-message-row ${who === 'user' ? 'canto-message-user' : 'canto-message-assistant'}`;
    if (who !== 'user') {
      const avatar = document.createElement('span');
      avatar.className = 'canto-message-avatar';
      const image = document.createElement('img');
      image.src = form.dataset.avatar || '/assets/images/assistant-mark.svg';
      image.alt = '';
      avatar.appendChild(image);
      row.appendChild(avatar);
    }
    const bubble = document.createElement('div');
    bubble.className = 'canto-message';
    bubble.textContent = text;
    row.appendChild(bubble);
    messages.appendChild(row);
    scrollToBottom();
    return row;
  };

  const sendMessage = async (message) => {
    const clean = message.trim();
    if (!clean) return;
    addMessage(clean, 'user');
    input.value = '';
    input.style.height = 'auto';
    input.disabled = true;
    const submit = form.querySelector('button[type="submit"]');
    submit.disabled = true;
    status.textContent = 'A Canto está a preparar uma resposta…';

    const loading = addMessage('Estou a ler o que partilhaste…', 'assistant');
    loading.classList.add('canto-message-loading');
    const data = new FormData(form);
    data.set('message', clean);
    data.set('history', JSON.stringify(conversation.slice(-12)));

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: data,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      });
      const result = await response.json();
      loading.remove();
      if (!response.ok) throw new Error(result.error || 'Não foi possível enviar a mensagem.');
      const reply = result.reply || 'Não consegui preparar uma resposta agora.';
      addMessage(reply, 'assistant');
      conversation.push({ role: 'user', content: clean }, { role: 'assistant', content: reply });
      if (conversation.length > 16) conversation.splice(0, conversation.length - 16);
      status.textContent = result.mode === 'guided'
        ? 'Modo de orientação básica · IA ainda não configurada'
        : 'Podes continuar ao teu ritmo.';
    } catch (error) {
      loading.remove();
      addMessage(error.message || 'Não foi possível contactar a Canto. Tenta novamente.', 'assistant');
      status.textContent = 'Ocorreu um problema de ligação.';
    } finally {
      input.disabled = false;
      submit.disabled = false;
      input.focus();
      scrollToBottom();
    }
  };

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    sendMessage(input.value);
  });
  input.addEventListener('input', () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 120)}px`;
  });
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      form.requestSubmit();
    }
  });
  document.querySelectorAll('[data-prompt]').forEach((button) => {
    button.addEventListener('click', () => sendMessage(button.dataset.prompt || ''));
  });
})();
