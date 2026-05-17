(function () {
  'use strict';

  const msgsCol  = document.querySelector('.cw-msgs-col');
  const msgsEl   = document.querySelector('.cw-msgs');
  const inputEl  = document.getElementById('cwInput');
  const sendBtn  = document.querySelector('.cw-btn-send');

  if (!msgsEl || !inputEl || !sendBtn) return;

  const history = [];

  /* ── Helpers ── */
  function ts() {
    return new Date().toLocaleTimeString('ru', { hour: '2-digit', minute: '2-digit' });
  }
  function esc(s) {
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/\n/g, '<br>');
  }

  function appendBub(text, role) {
    const div = document.createElement('div');
    div.className = 'msg-bub' + (role === 'user' ? ' msg-bub--user' : '');
    div.innerHTML = `<p>${esc(text)}</p><span class="msg-ts">${ts()}</span>`;
    msgsEl.appendChild(div);
    scrollBottom();
    return div;
  }

  function appendTyping() {
    const div = document.createElement('div');
    div.className = 'typing-dots';
    div.innerHTML = '<span></span><span></span><span></span>';
    msgsEl.appendChild(div);
    scrollBottom();
    return div;
  }

  function scrollBottom() {
    if (msgsCol) msgsCol.scrollTop = msgsCol.scrollHeight;
  }

  /* ── Send ── */
  async function send() {
    const text = inputEl.value.trim();
    if (!text) return;

    inputEl.value = '';
    inputEl.disabled = true;
    sendBtn.disabled = true;

    appendBub(text, 'user');
    history.push({ role: 'user', content: text });

    const dot = appendTyping();

    try {
      const res = await fetch('/api/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messages: history }),
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      dot.remove();
      appendBub(data.text, 'assistant');
      history.push({ role: 'assistant', content: data.text });
    } catch {
      dot.remove();
      appendBub('Сервер недоступен. Запустите: python3 prototype/server.py', 'assistant');
    } finally {
      inputEl.disabled = false;
      sendBtn.disabled = false;
      inputEl.focus();
    }
  }

  sendBtn.addEventListener('click', send);
  inputEl.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
  });

})();
