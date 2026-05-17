(function () {
  'use strict';

  /* ── Глобус ──────────────────────────────────────────────── */
  const canvas = document.getElementById('globeCanvas');
  if (canvas) {
    const ctx  = canvas.getContext('2d');
    const S    = 700;
    canvas.width = canvas.height = S;
    const cx = S / 2, cy = S / 2, R = S / 2 - 8;
    let   rot = 0;

    function drawGlobe() {
      ctx.clearRect(0, 0, S, S);

      /* Сфера — заливка */
      ctx.save();
      ctx.beginPath();
      ctx.arc(cx, cy, R, 0, Math.PI * 2);
      ctx.clip();

      const fill = ctx.createRadialGradient(cx * .72, cy * .65, R * .05, cx, cy, R);
      fill.addColorStop(0,   '#1e3c8a');
      fill.addColorStop(.35, '#0d1f60');
      fill.addColorStop(.75, '#07103a');
      fill.addColorStop(1,   '#040a1e');
      ctx.fillStyle = fill;
      ctx.fillRect(0, 0, S, S);

      /* Параллели */
      ctx.lineWidth = .75;
      for (let lat = -75; lat <= 75; lat += 15) {
        const yL = cy + R * Math.sin(lat * Math.PI / 180);
        const rL = R  * Math.cos(lat * Math.PI / 180);
        if (rL < 2) continue;
        const bright = lat === 0 ? .4 : .18;
        ctx.strokeStyle = `rgba(80,150,255,${bright})`;
        ctx.beginPath();
        ctx.ellipse(cx, yL, rL, rL * .13, 0, 0, Math.PI * 2);
        ctx.stroke();
      }

      /* Меридианы (анимированные) */
      for (let lon = 0; lon < 180; lon += 15) {
        const phase   = (lon * Math.PI / 180 + rot) % (Math.PI * 2);
        const cosP    = Math.cos(phase);
        const opacity = .07 + ((cosP + 1) / 2) * .25;
        ctx.strokeStyle = `rgba(80,150,255,${opacity})`;
        ctx.lineWidth = .75;
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(phase);
        ctx.beginPath();
        ctx.ellipse(0, 0, R * .13, R, 0, 0, Math.PI * 2);
        ctx.stroke();
        ctx.restore();
      }

      ctx.restore(); /* конец clip */

      /* Атмосферное свечение */
      const atm = ctx.createRadialGradient(cx, cy, R * .82, cx, cy, R * 1.22);
      atm.addColorStop(0,   'rgba(40,100,255,0)');
      atm.addColorStop(.55, 'rgba(60,120,255,.05)');
      atm.addColorStop(1,   'rgba(80,150,255,.18)');
      ctx.beginPath();
      ctx.arc(cx, cy, R * 1.22, 0, Math.PI * 2);
      ctx.fillStyle = atm;
      ctx.fill();

      /* Горизонтальное свечение снизу */
      const hor = ctx.createLinearGradient(0, cy + R * .4, 0, cy + R * 1.1);
      hor.addColorStop(0,   'rgba(60,120,255,.12)');
      hor.addColorStop(.5,  'rgba(80,150,255,.22)');
      hor.addColorStop(1,   'rgba(40,100,255,0)');
      ctx.fillStyle = hor;
      ctx.fillRect(cx - R * 1.2, cy + R * .4, R * 2.4, R * .7);

      rot += .0025;
      requestAnimationFrame(drawGlobe);
    }

    drawGlobe();
  }

  /* ── Голосовой ввод: waveform + mic ──────────────────────── */
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

  /* ── Закрытие окна чата ────────────────────────────────────── */
  const closeBtn   = document.querySelector('.cw-close');
  const chatWindow = document.querySelector('.chat-window');
  if (closeBtn && chatWindow) {
    closeBtn.addEventListener('click', () => {
      chatWindow.style.opacity    = '0';
      chatWindow.style.transform  = 'scale(.97)';
      chatWindow.style.transition = 'all .3s ease';
      setTimeout(() => { chatWindow.style.display = 'none'; }, 300);
    });
  }

})();
