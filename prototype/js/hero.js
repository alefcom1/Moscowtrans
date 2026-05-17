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
  let manualStop     = false;  // true когда пользователь сам нажал стоп
  let hasFinalResult = false;  // есть ли финальный результат для авто-отправки

  const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;

  if (SpeechRec) {
    recognition = new SpeechRec();
    recognition.continuous     = false; // остановится сам после паузы говорящего
    recognition.interimResults = true;

    recognition.onresult = function (e) {
      const results = Array.from(e.results);
      const transcript = results.map(function (r) { return r[0].transcript; }).join('');
      if (inputEl) inputEl.value = transcript;
      // Фиксируем финальный результат (пауза распознана)
      hasFinalResult = results.some(function (r) { return r.isFinal; }) && !!transcript.trim();
    };

    recognition.onend = function () {
      setListening(false);
      // Авто-отправка после паузы, если не было ручной остановки
      if (!manualStop && hasFinalResult && inputEl && inputEl.value.trim()) {
        hasFinalResult = false;
        setTimeout(function () {
          const sendBtn = document.querySelector('.cw-btn-send');
          if (sendBtn && !sendBtn.disabled) sendBtn.click();
        }, 250);
      }
      hasFinalResult = false;
      manualStop = false;
    };

    recognition.onerror = function (e) {
      if (e.error !== 'no-speech') console.warn('[voice]', e.error);
      hasFinalResult = false;
      manualStop = false;
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
    if (waveformSvg) waveformSvg.classList.toggle('active',   val);
    if (cwMicBtn)    cwMicBtn.classList.toggle('listening',    val);
    if (cwVoiceBtn)  cwVoiceBtn.classList.toggle('listening',  val);
  }

  function toggleVoice() {
    if (!recognition) {
      alert('Голосовой ввод не поддерживается в этом браузере. Используйте Chrome или Edge.');
      return;
    }
    if (listening) {
      manualStop = true; // не авто-отправлять при ручной остановке
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

})();
