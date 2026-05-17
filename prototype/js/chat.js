(function () {
  'use strict';

  const msgsCol  = document.querySelector('.cw-msgs-col');
  const msgsEl   = document.querySelector('.cw-msgs');
  const inputEl  = document.getElementById('cwInput');
  const sendBtn  = document.querySelector('.cw-btn-send');

  if (!msgsEl || !inputEl || !sendBtn) return;

  const history = [];

  /* ── Приветственные сообщения: появляются по одному ── */
  (function showGreetings() {
    const greetings = Array.from(msgsEl.querySelectorAll('.msg-bub[data-greeting]'));
    if (!greetings.length) return;

    /* Скрываем все сразу */
    greetings.forEach(function (el) { el.style.visibility = 'hidden'; el.style.position = 'absolute'; });

    let idx = 0;

    function next() {
      if (idx >= greetings.length) return;
      const el = greetings[idx];
      const charCount = (el.textContent || '').trim().length;
      const typingMs  = Math.max(700, Math.min(charCount * 18, 2200));

      const dot = appendTyping();

      setTimeout(function () {
        dot.remove();
        /* Показываем сообщение */
        el.style.position = '';
        el.style.visibility = '';
        el.removeAttribute('data-greeting');
        el.classList.add('msg-bub--entering');
        scrollBottom();
        idx++;
        if (idx < greetings.length) setTimeout(next, 380);
      }, typingMs);
    }

    /* Первое сообщение — после небольшой паузы */
    setTimeout(next, 500);
  }());

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
  inputEl.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
  });

  /* ── Прикрепление файла ── */
  const attachBtn = document.querySelector('.cw-btn-icon[title="Прикрепить файл"]');
  if (attachBtn) {
    const fileInput = document.createElement('input');
    fileInput.type    = 'file';
    fileInput.accept  = '.pdf,.doc,.docx,.odt,.txt,.rtf,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.webp,.zip,.rar,.7z';
    fileInput.style.cssText = 'position:absolute;width:0;height:0;opacity:0;pointer-events:none';
    document.body.appendChild(fileInput);

    attachBtn.addEventListener('click', function () { fileInput.click(); });

    fileInput.addEventListener('change', function () {
      const file = fileInput.files[0];
      if (!file) return;
      fileInput.value = '';

      const sizeMB = (file.size / 1048576).toFixed(1);
      const isImage = file.type.startsWith('image/');

      if (isImage) {
        const reader = new FileReader();
        reader.onload = function (ev) {
          const div = document.createElement('div');
          div.className = 'msg-bub msg-bub--user';
          div.innerHTML =
            '<img src="' + ev.target.result + '" alt="' + esc(file.name) +
            '" class="msg-img"><span class="msg-ts">' + ts() + '</span>';
          msgsEl.appendChild(div);
          scrollBottom();
          history.push({ role: 'user', content: '[Изображение: ' + file.name + ']' });
          autoReply('[Изображение: ' + file.name + ']');
        };
        reader.readAsDataURL(file);
      } else {
        const ext = file.name.split('.').pop().toUpperCase();
        const div = document.createElement('div');
        div.className = 'msg-bub msg-bub--user';
        div.innerHTML =
          '<div class="msg-file"><div class="msg-file-icon">' + esc(ext) + '</div>' +
          '<div class="msg-file-info"><span class="msg-file-name">' + esc(file.name) +
          '</span><span class="msg-file-size">' + sizeMB + ' МБ</span></div></div>' +
          '<span class="msg-ts">' + ts() + '</span>';
        msgsEl.appendChild(div);
        scrollBottom();
        history.push({ role: 'user', content: '[Файл: ' + file.name + ', ' + sizeMB + ' МБ]' });
        autoReply('[Файл: ' + file.name + ']');
      }
    });
  }

  /* Запрашиваем ответ Ольги на вложение */
  async function autoReply(userText) {
    const dot = appendTyping();
    try {
      const res = await fetch('/api/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messages: history }),
      });
      dot.remove();
      if (!res.ok) throw new Error();
      const data = await res.json();
      appendBub(data.text, 'assistant');
      history.push({ role: 'assistant', content: data.text });
    } catch {
      dot.remove();
      appendBub('Получила ваш файл! Уточните, пожалуйста, с какого языка нужен перевод и для каких целей.', 'assistant');
    }
  }

})();
