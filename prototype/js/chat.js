(function () {
  'use strict';

  const EJS_PUBLIC_KEY  = 'qIHC--GaJ6MMVCOg5';
  const EJS_SERVICE_ID  = 'service_htuz6bm';
  const EJS_TEMPLATE_ID = 'template_zl1knyb';
  let _ejsReady = false;
  function ensureEjs() {
    if (typeof emailjs === 'undefined') return false;
    if (!_ejsReady) { emailjs.init({ publicKey: EJS_PUBLIC_KEY }); _ejsReady = true; }
    return true;
  }

  const msgsCol  = document.querySelector('.cw-msgs-col');
  const msgsEl   = document.querySelector('.cw-msgs');
  const inputEl  = document.getElementById('cwInput');
  const sendBtn  = document.querySelector('.cw-btn-send');

  if (!msgsEl || !inputEl || !sendBtn) return;

  const history = [];

  /* ── Определение языка по тексту ─────────────────────────── */
  function detectLang(text) {
    if (!text || text.trim().length < 3) return null;
    const t = ' ' + text.toLowerCase() + ' ';

    // Кириллица → русский
    const cyrillic = (t.match(/[а-яё]/g) || []).length;
    const total    = t.replace(/\s/g, '').length;
    if (total > 0 && cyrillic / total > 0.3) return 'ru-RU';

    // Итальянский
    const itWords = [
      'ciao', 'salve', 'buongiorno', 'grazie', 'prego', 'voglio', 'bisogno',
      'traduz', 'documento', 'per favore', 'italiano', 'tradurre', 'parla',
      'parlo', 'parlate', 'vorrei', 'capisce', ' il ', ' la ', ' lo ',
      ' un ', ' una ', ' mi ', ' si ', ' di ', ' per ', ' con ', ' ho ', ' che '
    ];
    const itScore = itWords.filter(function (w) { return t.includes(w); }).length;

    // Английский
    const enWords = [
      'hello', ' hi ', 'thank', 'please', 'need', 'want', 'translat',
      'document', 'english', 'help', 'quote', 'price', 'speak',
      ' the ', ' is ', ' are ', ' you ', ' this ', ' that ',
      ' have ', ' can ', ' my ', ' for ', ' with ', ' of ', ' i ', ' do '
    ];
    const enScore = enWords.filter(function (w) { return t.includes(w); }).length;

    if (itScore === 0 && enScore === 0) return null;
    return itScore >= enScore ? 'it-IT' : 'en-US';
  }

  /* Читаем текущий язык из активного флага */
  function getCurrentLang() {
    const active = document.querySelector('.lang-flag--active');
    return active ? active.dataset.lang : 'ru-RU';
  }

  /* Сообщения Ольги при обнаружении языка */
  const LANG_PROMPTS = {
    'en-US': "I can see you're writing in English 🇬🇧 To chat with me in English, please tap the 🇬🇧 flag in my panel.",
    'it-IT': "Vedo che scrivi in italiano 🇮🇹 Per chattare con me in italiano, clicca sulla bandiera 🇮🇹 nel mio pannello."
  };

  /* ── Приветственные сообщения ── */
  (function showGreetings() {
    const greetings = Array.from(msgsEl.querySelectorAll('.msg-bub[data-greeting]'));
    if (!greetings.length) return;

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
        el.style.position   = '';
        el.style.visibility = '';
        el.removeAttribute('data-greeting');
        el.classList.add('msg-bub--entering');
        scrollBottom();
        idx++;
        if (idx < greetings.length) setTimeout(next, 380);
      }, typingMs);
    }

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
    div.innerHTML = '<p>' + esc(text) + '</p><span class="msg-ts">' + ts() + '</span>';
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

  function getActiveLang() {
    var active = document.querySelector('.lang-flag--active');
    return active ? active.dataset.lang : 'ru-RU';
  }

  function sayOlga(text) {
    if (typeof window.speakOlga === 'function') window.speakOlga(text, getActiveLang());
  }

  function pulseLangFlags() {
    const flagsEl = document.querySelector('.agent-langs');
    if (!flagsEl) return;
    flagsEl.classList.add('lang-flags--pulse');
    setTimeout(function () { flagsEl.classList.remove('lang-flags--pulse'); }, 2400);
  }

  /* ── Send ── */
  async function send() {
    const text = inputEl.value.trim();
    if (!text) return;

    inputEl.value    = '';
    inputEl.disabled = true;
    sendBtn.disabled = true;

    appendBub(text, 'user');
    history.push({ role: 'user', content: text });

    /* Определяем язык сообщения (работает и для печатного и для голосового) */
    const detected    = detectLang(text);
    const currentLang = getCurrentLang();
    const langMismatch =
      detected &&
      detected.split('-')[0] !== currentLang.split('-')[0]
        ? detected
        : (window._chatLangMismatch || null);
    window._chatLangMismatch = null;

    if (langMismatch) {
      const reply = LANG_PROMPTS[langMismatch] || LANG_PROMPTS['en-US'];
      const dot   = appendTyping();
      setTimeout(function () {
        dot.remove();
        appendBub(reply, 'assistant');
        history.push({ role: 'assistant', content: reply });
        if (typeof window.speakOlga === 'function') window.speakOlga(reply, langMismatch);
        pulseLangFlags();
        inputEl.disabled = false;
        sendBtn.disabled = false;
        inputEl.focus();
      }, 900);
      return;
    }

    const dot = appendTyping();

    try {
      const res = await fetch('/api/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messages: history }),
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      dot.remove();
      appendBub(data.text, 'assistant');
      history.push({ role: 'assistant', content: data.text });
      sayOlga(data.text);
    } catch {
      dot.remove();
      showOfflineFallback(text);
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
      var fallback = 'Получила ваш файл! Уточните, пожалуйста, с какого языка нужен перевод и для каких целей.';
      appendBub(fallback, 'assistant');
      sayOlga(fallback);
    }
  }

  /* ── Офлайн-фолбэк: собираем контакт и шлём через EmailJS ── */
  function showOfflineFallback(userMessage) {
    const intro = 'Обрабатываю ваш запрос — дайте мне секунду. Чтобы я могла написать вам напрямую, оставьте e-mail или телефон:';
    appendBub(intro, 'assistant');
    sayOlga(intro);

    const wrap = document.createElement('div');
    wrap.className = 'chat-lead-form';
    wrap.innerHTML =
      '<input type="text" class="clf-input" placeholder="E-mail или телефон" />' +
      '<button class="clf-btn">Отправить</button>' +
      '<a href="https://wa.me/79859704413" class="clf-wa" target="_blank" rel="noopener">или написать в WhatsApp</a>';
    msgsEl.appendChild(wrap);
    scrollBottom();

    const inp = wrap.querySelector('.clf-input');
    const btn = wrap.querySelector('.clf-btn');

    async function submitLead() {
      const contact = inp.value.trim();
      if (!contact) { inp.focus(); return; }
      btn.disabled = true;
      btn.textContent = '…';

      const msgHistory = history
        .filter(function(m){ return m.role === 'user'; })
        .map(function(m){ return m.content; })
        .join('\n');

      if (ensureEjs()) {
        try {
          await emailjs.send(EJS_SERVICE_ID, EJS_TEMPLATE_ID, {
            to_email:  'alefcom1@gmail.com',
            from_name: contact,
            from_email: contact.includes('@') ? contact : 'chat@moscowtrans.ru',
            reply_to:  contact.includes('@') ? contact : 'chat@moscowtrans.ru',
            phone:     contact.includes('@') ? '—' : contact,
            company:   '—',
            calc_type: 'Чат-сообщение с сайта',
            price_est: '—',
            details:   msgHistory || userMessage,
            comment:   'Контакт для связи: ' + contact
          });
        } catch(e) { console.warn('Chat EmailJS error:', e); }
      }

      wrap.innerHTML = '';
      const thanks = 'Спасибо! Свяжусь с вами в течение 30 минут в рабочее время.';
      appendBub(thanks, 'assistant');
      sayOlga(thanks);
      history.push({ role: 'assistant', content: thanks });
    }

    btn.addEventListener('click', submitLead);
    inp.addEventListener('keydown', function(e){
      if (e.key === 'Enter') { e.preventDefault(); submitLead(); }
    });
  }

}());
