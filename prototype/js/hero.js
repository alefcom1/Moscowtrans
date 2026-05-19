(function () {
  'use strict';

  /* ── Elements ── */
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
  let continuousMode = false;
  let ttsEnabled     = true;

  const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;

  /* ══ TTS ═══════════════════════════════════════════════════════ */
  let ttsVoices = [];

  if (window.speechSynthesis) {
    const loadVoices = function () { ttsVoices = speechSynthesis.getVoices(); };
    loadVoices();
    speechSynthesis.addEventListener('voiceschanged', loadVoices);
  }

  function getBestVoice(lang) {
    if (!ttsVoices.length) return null;
    const code = lang.toLowerCase();
    return ttsVoices.find(function (v) {
      return v.lang.toLowerCase() === code && /female|woman/i.test(v.name);
    }) || ttsVoices.find(function (v) {
      return v.lang.toLowerCase().startsWith(code.split('-')[0]);
    }) || null;
  }

  function speak(text, lang) {
    if (!ttsEnabled || !window.speechSynthesis) return;
    speechSynthesis.cancel();
    const utt   = new SpeechSynthesisUtterance(text);
    utt.lang    = lang || currentLang;
    utt.rate    = 1.0;
    utt.pitch   = 1.1;
    const voice = getBestVoice(utt.lang);
    if (voice) utt.voice = voice;
    speechSynthesis.speak(utt);
  }

  window.speakOlga = speak; // вызывается из chat.js

  /* ══ AudioContext waveform ═════════════════════════════════════ */
  let audioCtx    = null;
  let analyser    = null;
  let animFrame   = null;
  let mediaStream = null;

  const PATH1_REST = 'M0,60 C18,35 36,85 54,60 C72,35 90,15 108,60 C126,105 144,25 162,60 C180,95 200,40 220,60';
  const PATH2_REST = 'M0,60 C20,45 40,75 60,60 C80,45 100,28 120,60 C140,92 160,32 180,60 C200,88 210,48 220,60';

  function resetWaveformPaths() {
    const wp1 = waveformSvg && waveformSvg.querySelector('.wp1');
    const wp2 = waveformSvg && waveformSvg.querySelector('.wp2');
    if (wp1) wp1.setAttribute('d', PATH1_REST);
    if (wp2) wp2.setAttribute('d', PATH2_REST);
  }

  function drawWaveform() {
    if (!analyser || !waveformSvg) return;
    const bufLen  = analyser.frequencyBinCount;
    const data    = new Uint8Array(bufLen);
    const wp1     = waveformSvg.querySelector('.wp1');
    const wp2     = waveformSvg.querySelector('.wp2');
    const W = 220, MID = 60, SAMPLES = 64;
    const idxStep = bufLen / SAMPLES;

    function frame() {
      animFrame = requestAnimationFrame(frame);
      analyser.getByteTimeDomainData(data);

      let d1 = '', d2 = '';
      for (let i = 0; i < SAMPLES; i++) {
        const v  = (data[Math.floor(i * idxStep)] / 128.0) - 1.0; // -1..+1
        const x  = ((i / (SAMPLES - 1)) * W).toFixed(1);
        const y1 = (MID + v * 44).toFixed(1);
        const y2 = (MID - v * 28).toFixed(1);
        d1 += (i === 0 ? 'M' + x + ',' + y1 : ' L' + x + ',' + y1);
        d2 += (i === 0 ? 'M' + x + ',' + y2 : ' L' + x + ',' + y2);
      }

      if (wp1) wp1.setAttribute('d', d1);
      if (wp2) wp2.setAttribute('d', d2);
    }

    frame();
  }

  async function startAudioViz() {
    if (!waveformSvg || !navigator.mediaDevices) return;
    try {
      mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
      audioCtx    = new (window.AudioContext || window.webkitAudioContext)();
      const src   = audioCtx.createMediaStreamSource(mediaStream);
      analyser    = audioCtx.createAnalyser();
      analyser.fftSize               = 512;
      analyser.smoothingTimeConstant = 0.75;
      src.connect(analyser);
      drawWaveform();
    } catch (e) {
      console.warn('[waveform] getUserMedia:', e.message);
      // Нет доступа к микрофону — работает CSS-анимация через .active
    }
  }

  function stopAudioViz() {
    if (animFrame)    { cancelAnimationFrame(animFrame); animFrame = null; }
    if (analyser)     { analyser = null; }
    if (audioCtx)     { audioCtx.close().catch(function () {}); audioCtx = null; }
    if (mediaStream)  { mediaStream.getTracks().forEach(function (t) { t.stop(); }); mediaStream = null; }
    resetWaveformPaths();
  }

  /* ══ Определение языка ═════════════════════════════════════════ */
  function detectLang(text) {
    if (!text || text.trim().length < 3) return null;
    const t = ' ' + text.toLowerCase() + ' ';
    const cyrillic = (t.match(/[а-яё]/g) || []).length;
    const total    = t.replace(/\s/g, '').length;
    if (total > 0 && cyrillic / total > 0.3) return 'ru-RU';
    const itW = ['ciao','salve','buongiorno','grazie','voglio','bisogno','traduz',
                 'parlo','parla','parlate','italiano',' il ',' la ',' un ',' di ',' per ',' ho '];
    const enW = ['hello',' hi ','please','need','want','translat','english',
                 ' the ',' is ',' are ',' you ',' have ',' can ',' my ',' for '];
    const it  = itW.filter(function (w) { return t.includes(w); }).length;
    const en  = enW.filter(function (w) { return t.includes(w); }).length;
    if (!it && !en) return null;
    return it >= en ? 'it-IT' : 'en-US';
  }

  /* ══ Recognition ═══════════════════════════════════════════════ */
  if (SpeechRec) {
    recognition = new SpeechRec();
    recognition.interimResults  = true;
    recognition.maxAlternatives = 1;

    recognition.onresult = function (e) {
      var interim = '', finalText = '';
      for (var i = e.resultIndex; i < e.results.length; i++) {
        var t = e.results[i][0].transcript;
        if (e.results[i].isFinal) finalText += t;
        else interim += t;
      }
      if (inputEl) inputEl.value = finalText || interim;

      if (finalText.trim()) {
        hasFinalResult = true;
        if (continuousMode) {
          var detected = detectLang(finalText);
          if (detected && detected.split('-')[0] !== currentLang.split('-')[0]) {
            window._chatLangMismatch = detected;
          }
          setTimeout(function () {
            var btn = document.querySelector('.cw-btn-send');
            if (btn && !btn.disabled && inputEl && inputEl.value.trim()) btn.click();
          }, 200);
        }
      }
    };

    recognition.onend = function () {
      if (continuousMode && !manualStop) {
        try { recognition.start(); return; } catch (e) { /* fall through */ }
      }
      setListening(false);
      stopAudioViz();
      if (!manualStop && hasFinalResult && inputEl && inputEl.value.trim()) {
        hasFinalResult = false;
        var detected = detectLang(inputEl.value);
        if (detected && detected.split('-')[0] !== currentLang.split('-')[0]) {
          window._chatLangMismatch = detected;
        }
        setTimeout(function () {
          var btn = document.querySelector('.cw-btn-send');
          if (btn && !btn.disabled) btn.click();
        }, 250);
      }
      hasFinalResult = false;
      manualStop     = false;
    };

    recognition.onerror = function (e) {
      if (continuousMode && !manualStop && e.error === 'no-speech') {
        try { recognition.start(); return; } catch (err) { /* ignore */ }
      }
      if (e.error !== 'no-speech') console.warn('[voice]', e.error);
      hasFinalResult = false;
      manualStop     = false;
      if (!continuousMode) { setListening(false); stopAudioViz(); }
    };
  }

  /* ══ Listening helpers ════════════════════════════════════════ */
  function setListening(val) {
    listening = val;
    if (waveformSvg) waveformSvg.classList.toggle('active', val);
    if (cwMicBtn)    cwMicBtn.classList.toggle('listening', val);
    if (cwVoiceBtn)  cwVoiceBtn.classList.toggle('listening', val);
  }

  function startListening() {
    if (!recognition) {
      alert('Голосовой ввод не поддерживается в этом браузере. Используйте Chrome или Edge.');
      return;
    }
    if (window.speechSynthesis) speechSynthesis.cancel();
    recognition.lang       = currentLang;
    recognition.continuous = continuousMode;
    manualStop     = false;
    hasFinalResult = false;
    setListening(true);
    startAudioViz();
    try { recognition.start(); }
    catch (err) { console.warn('[voice] start:', err); setListening(false); stopAudioViz(); }
  }

  function stopListening() {
    manualStop = true;
    setListening(false);
    stopAudioViz();
    if (recognition) recognition.stop();
  }

  function toggleVoice() {
    if (listening) stopListening();
    else startListening();
  }

  if (cwMicBtn)   cwMicBtn.addEventListener('click', toggleVoice);
  if (cwVoiceBtn) cwVoiceBtn.addEventListener('click', toggleVoice);

  /* ══ Lang flags ═══════════════════════════════════════════════ */
  langFlags.forEach(function (btn) {
    btn.addEventListener('click', function () {
      langFlags.forEach(function (b) { b.classList.remove('lang-flag--active'); });
      btn.classList.add('lang-flag--active');
      currentLang = btn.dataset.lang;
      if (listening && recognition) { manualStop = true; recognition.stop(); }
    });
  });

  /* ══ Кнопки управления голосом ════════════════════════════════ */
  function buildVoiceControls() {
    var panel = cwMicBtn && cwMicBtn.closest('.cw-agent-panel');
    if (!panel) return;

    var wrap = document.createElement('div');
    wrap.className = 'voice-controls';
    wrap.innerHTML =
      '<button class="voice-ctrl-btn" id="btnContinuous" title="Микрофон не выключается между фразами">' +
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>' +
        'Непрерывный' +
      '</button>' +
      '<button class="voice-ctrl-btn active" id="btnTTS" title="Ольга отвечает голосом">' +
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>' +
        'Озвучка' +
      '</button>';

    var tooltip = panel.querySelector('.mic-tooltip');
    if (tooltip) tooltip.insertAdjacentElement('afterend', wrap);
    else panel.appendChild(wrap);

    var btnC = document.getElementById('btnContinuous');
    if (btnC) {
      btnC.addEventListener('click', function () {
        continuousMode = !continuousMode;
        btnC.classList.toggle('active', continuousMode);
        if (cwMicBtn) cwMicBtn.classList.toggle('continuous', continuousMode);
        if (!continuousMode && listening) stopListening();
      });
    }

    var btnT = document.getElementById('btnTTS');
    if (btnT) {
      btnT.addEventListener('click', function () {
        ttsEnabled = !ttsEnabled;
        btnT.classList.toggle('active', ttsEnabled);
        if (!ttsEnabled && window.speechSynthesis) speechSynthesis.cancel();
      });
    }
  }

  buildVoiceControls();

  /* ══ Закрытие / открытие чата ════════════════════════════════ */
  var closeBtn     = document.querySelector('.cw-close');
  var chatWindow   = document.querySelector('.chat-window');
  var chatFloatBtn = document.getElementById('chatFloatBtn');

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
