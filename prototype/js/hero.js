(function () {
  'use strict';

  /* ── Голосовой ввод: Web Speech API ──────────────────────── */
  const waveformSvg = document.getElementById('waveformSvg');
  const cwMicBtn    = document.getElementById('cwMicBtn');
  const cwVoiceBtn  = document.getElementById('cwVoiceBtn');
  const inputEl     = document.getElementById('cwInput');

  let recognition = null;
  let listening    = false;

  const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;

  if (SpeechRec) {
    recognition = new SpeechRec();
    recognition.lang            = 'ru-RU';
    recognition.continuous      = false;
    recognition.interimResults  = true;

    recognition.onresult = function (e) {
      const transcript = Array.from(e.results)
        .map(function (r) { return r[0].transcript; })
        .join('');
      if (inputEl) inputEl.value = transcript;
    };

    recognition.onend = function () {
      setListening(false);
    };

    recognition.onerror = function (e) {
      console.warn('[voice]', e.error);
      setListening(false);
    };
  }

  function setListening(val) {
    listening = val;
    if (waveformSvg) waveformSvg.classList.toggle('active',    val);
    if (cwMicBtn)    cwMicBtn.classList.toggle('listening',     val);
    if (cwVoiceBtn)  cwVoiceBtn.classList.toggle('listening',   val);
  }

  function toggleVoice() {
    if (!recognition) {
      alert('Голосовой ввод не поддерживается в этом браузере. Используйте Chrome или Edge.');
      return;
    }
    if (listening) {
      recognition.stop();
    } else {
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
