(function () {
  'use strict';

  /* ── Voice toggle ──────────────────────────────────────────── */
  const waveformSvg = document.getElementById('waveformSvg');
  const cwMicBtn    = document.getElementById('cwMicBtn');
  const cwVoiceBtn  = document.getElementById('cwVoiceBtn');
  let listening = false;

  function toggleVoice() {
    listening = !listening;
    if (waveformSvg) waveformSvg.classList.toggle('active', listening);
    if (cwMicBtn)    cwMicBtn.classList.toggle('listening', listening);
  }
  if (cwMicBtn)   cwMicBtn.addEventListener('click', toggleVoice);
  if (cwVoiceBtn) cwVoiceBtn.addEventListener('click', toggleVoice);

  /* ── Chat minimize → floating button ──────────────────────── */
  const closeBtn     = document.querySelector('.cw-close');
  const chatWindow   = document.querySelector('.chat-window');
  const chatFloatBtn = document.getElementById('chatFloatBtn');

  if (closeBtn && chatWindow) {
    closeBtn.addEventListener('click', () => {
      chatWindow.classList.add('chat-minimized');
      if (chatFloatBtn) chatFloatBtn.classList.add('visible');
    });
  }

  if (chatFloatBtn && chatWindow) {
    chatFloatBtn.addEventListener('click', () => {
      chatWindow.classList.remove('chat-minimized');
      chatFloatBtn.classList.remove('visible');
    });
  }

})();
