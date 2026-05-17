(function () {
  'use strict';

  /* ── Earth Globe with City Lights ─────────────────────────── */
  const canvas = document.getElementById('globeCanvas');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    const S = 900;
    canvas.width = canvas.height = S;
    const cx = S / 2, cy = S / 2, R = S / 2 - 12;
    let rot = 0;

    /* [lat, lon, brightness 0-1] */
    const cities = [
      // North America East
      [40.7, -74, 1.0], [42.4, -71.1, 0.6], [39.9, -75.2, 0.6],
      [38.9, -77, 0.65], [41.9, -87.6, 0.8], [43.7, -79.4, 0.65],
      [45.5, -73.6, 0.55], [25.8, -80.2, 0.6], [29.8, -95.4, 0.65],
      [32.8, -96.8, 0.65], [33.4, -112, 0.6], [29.9, -90.1, 0.5],
      // North America West
      [34, -118.2, 0.9], [37.8, -122.4, 0.75], [47.6, -122.3, 0.6],
      [49.3, -123.1, 0.55],
      // South America
      [-23.5, -46.6, 0.85], [-22.9, -43.2, 0.75], [-34.6, -58.4, 0.65],
      [-33.5, -70.7, 0.6], [-12.1, -77, 0.55],
      // Europe (dense cluster)
      [51.5, -0.1, 1.0], [48.9, 2.3, 0.95], [52.5, 13.4, 0.85],
      [52.4, 4.9, 0.8], [50.9, 4.3, 0.75], [47.4, 8.5, 0.7],
      [48.2, 16.4, 0.7], [50.1, 14.4, 0.65], [52.2, 21, 0.7],
      [55.8, 37.6, 0.85], [59.3, 18, 0.65], [60.2, 24.9, 0.6],
      [55.7, 12.6, 0.65], [40.4, -3.7, 0.75], [41.4, 2.2, 0.7],
      [45.5, 9.2, 0.75], [41.9, 12.5, 0.75], [53.3, -6.3, 0.6],
      [37.9, 23.7, 0.65], [41, 29, 0.8],
      // Middle East
      [25.2, 55.3, 0.7], [24.7, 46.7, 0.65], [25.1, 51.5, 0.6],
      [30.1, 31.2, 0.7], [35.7, 51.4, 0.65], [33.3, 44.4, 0.6],
      // South Asia
      [28.6, 77.2, 0.85], [19.1, 72.9, 0.9], [22.6, 88.4, 0.75],
      [13, 80.3, 0.7], [12.9, 77.6, 0.7], [23.7, 90.4, 0.65],
      [24.9, 67.1, 0.65],
      // East Asia (very dense)
      [39.9, 116.4, 0.95], [31.2, 121.5, 1.0], [23.1, 113.3, 0.9],
      [22.3, 114.2, 0.85], [25.1, 121.6, 0.8], [35.7, 139.7, 1.0],
      [34.7, 135.5, 0.85], [33.6, 130.4, 0.7], [37.6, 126.9, 0.85],
      // Southeast Asia
      [14.1, 100.5, 0.75], [3.1, 101.7, 0.7], [1.3, 103.8, 0.8],
      [10.8, 106.7, 0.7], [21, 105.8, 0.65],
      // Africa
      [6.5, 3.4, 0.6], [-4.3, 15.3, 0.5], [-26.2, 28, 0.65],
      [-33.9, 18.4, 0.6], [5.6, -0.2, 0.5], [9, 38.7, 0.55],
      [-1.3, 36.8, 0.55],
      // Australia
      [-33.9, 151.2, 0.75], [-37.8, 144.9, 0.7], [-27.5, 153, 0.6],
      [-31.9, 115.9, 0.55],
    ];

    function projectPoint(lat, lon) {
      const phi   = lat * Math.PI / 180;
      const theta = lon * Math.PI / 180 + rot;
      const x3d   = Math.cos(phi) * Math.cos(theta);
      const y3d   = -Math.sin(phi);
      const z3d   = Math.cos(phi) * Math.sin(theta);
      return { x: cx + R * x3d, y: cy + R * y3d, z: z3d };
    }

    function drawGlobe() {
      ctx.clearRect(0, 0, S, S);
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

      ctx.save();
      ctx.beginPath();
      ctx.arc(cx, cy, R, 0, Math.PI * 2);
      ctx.clip();

      /* Ocean fill */
      const fill = ctx.createRadialGradient(cx * .75, cy * .65, R * .05, cx, cy, R);
      if (isDark) {
        fill.addColorStop(0,   '#1a3060');
        fill.addColorStop(.4,  '#0a1a48');
        fill.addColorStop(.75, '#060f32');
        fill.addColorStop(1,   '#030820');
      } else {
        fill.addColorStop(0,   '#c8e0f8');
        fill.addColorStop(.4,  '#9ec4f0');
        fill.addColorStop(.75, '#74a8e4');
        fill.addColorStop(1,   '#5090d8');
      }
      ctx.fillStyle = fill;
      ctx.fillRect(0, 0, S, S);

      /* Subtle parallels (dark only) */
      if (isDark) {
        ctx.globalAlpha = 0.06;
        ctx.strokeStyle = '#4080ff';
        ctx.lineWidth = .5;
        for (let lat = -60; lat <= 60; lat += 30) {
          const yL = cy + R * Math.sin(lat * Math.PI / 180);
          const rL = R  * Math.cos(lat * Math.PI / 180);
          if (rL < 2) continue;
          ctx.beginPath();
          ctx.ellipse(cx, yL, rL, rL * .12, 0, 0, Math.PI * 2);
          ctx.stroke();
        }
        ctx.globalAlpha = 1;
      }

      /* City lights / markers */
      cities.forEach(([lat, lon, br]) => {
        const p = projectPoint(lat, lon);
        if (p.z < -0.1) return;
        const vis = Math.max(0, Math.min(1, (p.z + 0.1) / 1.1));

        if (isDark) {
          const r = br * 3.5 + 0.5;
          const glowR = r * 7;
          const glow = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, glowR);
          glow.addColorStop(0,    `rgba(255,230,150,${vis * br * 0.9})`);
          glow.addColorStop(0.25, `rgba(255,180,80,${vis * br * 0.5})`);
          glow.addColorStop(1,    'rgba(255,120,40,0)');
          ctx.fillStyle = glow;
          ctx.beginPath();
          ctx.arc(p.x, p.y, glowR, 0, Math.PI * 2);
          ctx.fill();

          ctx.beginPath();
          ctx.arc(p.x, p.y, r, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(255,245,210,${vis})`;
          ctx.fill();
        } else {
          const r = br * 2.5 + 0.5;
          ctx.beginPath();
          ctx.arc(p.x, p.y, r, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(20,50,120,${vis * br * 0.75})`;
          ctx.fill();
        }
      });

      ctx.restore();

      /* Atmospheric glow */
      const atm = ctx.createRadialGradient(cx, cy, R * .88, cx, cy, R * 1.18);
      if (isDark) {
        atm.addColorStop(0,   'rgba(40,90,255,0)');
        atm.addColorStop(.55, 'rgba(60,120,255,.04)');
        atm.addColorStop(1,   'rgba(80,150,255,.20)');
      } else {
        atm.addColorStop(0,   'rgba(100,160,255,0)');
        atm.addColorStop(.55, 'rgba(120,180,255,.07)');
        atm.addColorStop(1,   'rgba(140,200,255,.28)');
      }
      ctx.beginPath();
      ctx.arc(cx, cy, R * 1.18, 0, Math.PI * 2);
      ctx.fillStyle = atm;
      ctx.fill();

      rot += .0018;
      requestAnimationFrame(drawGlobe);
    }

    drawGlobe();
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
