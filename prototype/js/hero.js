(function () {
  'use strict';

  /* ── Wireframe Globe (low CPU) ───────────────────────────────── */
  const canvas = document.getElementById('globeCanvas');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    const S = 900;
    canvas.width = canvas.height = S;
    const cx = S / 2, cy = S / 2, R = S / 2 - 12;
    let rot = 0;
    let lastTime = 0;

    function drawGlobe(timestamp) {
      /* Cap at ~30 fps to reduce CPU load */
      if (timestamp - lastTime < 34) { requestAnimationFrame(drawGlobe); return; }
      lastTime = timestamp;

      ctx.clearRect(0, 0, S, S);
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

      /* ── Clip to sphere ── */
      ctx.save();
      ctx.beginPath();
      ctx.arc(cx, cy, R, 0, Math.PI * 2);
      ctx.clip();

      /* Ocean fill */
      const fill = ctx.createRadialGradient(cx * .75, cy * .65, R * .05, cx, cy, R);
      if (isDark) {
        fill.addColorStop(0,   '#1c3468');
        fill.addColorStop(.4,  '#0c1c50');
        fill.addColorStop(.75, '#07113a');
        fill.addColorStop(1,   '#040c22');
      } else {
        fill.addColorStop(0,   '#c8e0f8');
        fill.addColorStop(.4,  '#9ec4f0');
        fill.addColorStop(.75, '#74a8e4');
        fill.addColorStop(1,   '#5090d8');
      }
      ctx.fillStyle = fill;
      ctx.fillRect(0, 0, S, S);

      const lineColor = isDark ? '80,150,255' : '30,80,180';

      /* Parallels (latitude lines) */
      for (let lat = -75; lat <= 75; lat += 15) {
        const yL = cy + R * Math.sin(lat * Math.PI / 180);
        const rL = R  * Math.cos(lat * Math.PI / 180);
        if (rL < 2) continue;
        const bright = lat === 0 ? (isDark ? .45 : .35) : (isDark ? .18 : .14);
        ctx.strokeStyle = `rgba(${lineColor},${bright})`;
        ctx.lineWidth   = lat === 0 ? 1.0 : .65;
        ctx.beginPath();
        ctx.ellipse(cx, yL, rL, rL * .13, 0, 0, Math.PI * 2);
        ctx.stroke();
      }

      /* Meridians (rotating) */
      for (let lon = 0; lon < 180; lon += 15) {
        const phase = (lon * Math.PI / 180 + rot) % (Math.PI * 2);
        const cosP  = Math.cos(phase);
        const base  = isDark ? .07 : .06;
        const range = isDark ? .28 : .22;
        const opacity = base + ((cosP + 1) / 2) * range;
        ctx.strokeStyle = `rgba(${lineColor},${opacity})`;
        ctx.lineWidth   = .7;
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(phase);
        ctx.beginPath();
        ctx.ellipse(0, 0, R * .13, R, 0, 0, Math.PI * 2);
        ctx.stroke();
        ctx.restore();
      }

      ctx.restore(); /* end clip */

      /* Atmospheric glow */
      const a0 = isDark ? 'rgba(40,90,255,0)'   : 'rgba(100,160,255,0)';
      const a1 = isDark ? 'rgba(60,120,255,.05)' : 'rgba(120,180,255,.07)';
      const a2 = isDark ? 'rgba(80,150,255,.22)' : 'rgba(140,200,255,.30)';
      const atm = ctx.createRadialGradient(cx, cy, R * .88, cx, cy, R * 1.18);
      atm.addColorStop(0, a0); atm.addColorStop(.5, a1); atm.addColorStop(1, a2);
      ctx.beginPath();
      ctx.arc(cx, cy, R * 1.18, 0, Math.PI * 2);
      ctx.fillStyle = atm;
      ctx.fill();

      rot += .0018;
      requestAnimationFrame(drawGlobe);
    }

    requestAnimationFrame(drawGlobe);
  }

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
