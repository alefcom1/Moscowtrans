(function () {
  'use strict';

  /* ── Голосовой ввод: Web Speech API ──────────────────────── */
  const waveformSvg = document.getElementById('waveformSvg');
  const cwMicBtn    = document.getElementById('cwMicBtn');
  const cwVoiceBtn  = document.getElementById('cwVoiceBtn');
  const inputEl     = document.getElementById('cwInput');
  const langFlags   = document.querySelectorAll('.lang-flag');

  let recognition    = null;
  let listening      = false;
  let currentLang    = 'ru-RU';
  let manualStop     = false;
  let hasFinalResult = false;

  const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;

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
      'traduz', 'documento', 'per favore', 'italiano', 'tradurre',
      ' il ', ' la ', ' lo ', ' un ', ' una ', ' mi ', ' si ',
      ' di ', ' per ', ' con ', ' del ', ' ho ', ' non ', ' che '
    ];
    const itScore = itWords.filter(function (w) { return t.includes(w); }).length;

    // Английский
    const enWords = [
      'hello', 'hi ', 'thank', 'please', 'need', 'want', 'translat',
      'document', 'english', 'help', 'quote', 'price',
      ' the ', ' is ', ' are ', ' you ', ' this ', ' that ',
      ' have ', ' can ', ' my ', ' for ', ' with ', ' of ', ' i '
    ];
    const enScore = enWords.filter(function (w) { return t.includes(w); }).length;

    if (itScore === 0 && enScore === 0) return null;
    return itScore > enScore ? 'it-IT' : 'en-US';
  }

  if (SpeechRec) {
    recognition = new SpeechRec();
    recognition.continuous     = false;
    recognition.interimResults = true;

    recognition.onresult = function (e) {
      const results = Array.from(e.results);
      const transcript = results.map(function (r) { return r[0].transcript; }).join('');
      if (inputEl) inputEl.value = transcript;
      hasFinalResult = results.some(function (r) { return r.isFinal; }) && !!transcript.trim();
    };

    recognition.onend = function () {
      setListening(false);
      if (!manualStop && hasFinalResult && inputEl && inputEl.value.trim()) {
        hasFinalResult = false;

        const transcript = inputEl.value.trim();
        const detected   = detectLang(transcript);

        if (detected) {
          const currentGroup  = currentLang.split('-')[0];
          const detectedGroup = detected.split('-')[0];
          if (detectedGroup !== currentGroup) {
            window._chatLangMismatch = detected;
          }
        }

        setTimeout(function () {
          const sendBtn = document.querySelector('.cw-btn-send');
          if (sendBtn && !sendBtn.disabled) sendBtn.click();
        }, 250);
      }
      hasFinalResult = false;
      manualStop     = false;
    };

    recognition.onerror = function (e) {
      if (e.error !== 'no-speech') console.warn('[voice]', e.error);
      hasFinalResult = false;
      manualStop     = false;
      setListening(false);
    };
  }

  /* Выбор языка по флагу */
  langFlags.forEach(function (btn) {
    btn.addEventListener('click', function () {
      langFlags.forEach(function (b) { b.classList.remove('lang-flag--active'); });
      btn.classList.add('lang-flag--active');
      currentLang = btn.dataset.lang;
      if (listening && recognition) {
        manualStop = true;
        recognition.stop();
      }
    });
  });

  function setListening(val) {
    listening = val;
    if (waveformSvg) waveformSvg.classList.toggle('active',  val);
    if (cwMicBtn)    cwMicBtn.classList.toggle('listening',   val);
    if (cwVoiceBtn)  cwVoiceBtn.classList.toggle('listening', val);
  }

  function toggleVoice() {
    if (!recognition) {
      alert('Голосовой ввод не поддерживается в этом браузере. Используйте Chrome или Edge.');
      return;
    }
    if (listening) {
      manualStop = true;
      recognition.stop();
    } else {
      recognition.lang = currentLang;
      manualStop     = false;
      hasFinalResult = false;
      setListening(true);
      try { recognition.start(); }
      catch (err) { console.warn('[voice] start error', err); setListening(false); }
    }
  }

  if (cwMicBtn)   cwMicBtn.addEventListener('click', toggleVoice);
  if (cwVoiceBtn) cwVoiceBtn.addEventListener('click', toggleVoice);

  /* ── Закрытие / открытие чата ────────────────────────────── */
  const closeBtn     = document.querySelector('.cw-close');
  const chatWindow   = document.querySelector('.chat-window');
  const chatFloatBtn = document.getElementById('chatFloatBtn');

  if (closeBtn && chatWindow) {
    closeBtn.addEventListener('click', function () {
      chatWindow.classList.add('chat-minimized');
      if (chatFloatBtn) chatFloatBtn.classList.add('visible');
    });
  }

  if (chatFloatBtn && chatWindow) {
    chatFloatBtn.addEventListener('click', function () {
      chatWindow.classList.remove('chat-minimized');
      chatFloatBtn.classList.remove('visible');
    });
  }

}());
