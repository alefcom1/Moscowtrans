(function () {
  'use strict';

  /* ── Wireframe Globe with continent outlines ──────────────────── */
  const canvas = document.getElementById('globeCanvas');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    const S = 900;
    canvas.width = canvas.height = S;
    const cx = S / 2, cy = S / 2, R = S / 2 - 12;
    let rot = 0;
    let lastTime = 0;

    /* Simplified continent polygons [lon°, lat°] */
    const CONTINENTS = [
      /* North America */
      [[-168,72],[-130,70],[-90,73],[-55,50],[-66,44],[-75,35],[-85,15],
       [-95,20],[-108,22],[-120,32],[-125,38],[-130,55],[-145,60],[-168,72]],
      /* South America */
      [[-80,12],[-50,8],[-35,-5],[-38,-23],[-65,-55],[-75,-42],
       [-70,-20],[-68,-5],[-78,2],[-80,8],[-80,12]],
      /* Europe */
      [[-12,36],[-8,38],[-9,44],[0,43],[8,45],[10,56],[18,58],
       [26,62],[28,70],[18,70],[10,63],[0,50],[-10,44],[-12,36]],
      /* Africa */
      [[-17,15],[-12,5],[0,-2],[10,-2],[20,-5],[35,-3],[40,12],
       [38,20],[35,37],[15,37],[0,34],[-16,20],[-17,15]],
      /* Asia (mainland + India) */
      [[35,70],[60,68],[80,72],[110,73],[135,68],[145,45],[135,33],
       [125,22],[110,18],[105,5],[98,15],[90,25],[80,32],[70,38],
       [55,46],[50,42],[40,38],[35,36],[35,45],[26,65],[35,70]],
      /* Indian subcontinent */
      [[68,22],[80,8],[85,10],[92,22],[80,28],[72,24],[68,22]],
      /* Australia */
      [[114,-22],[122,-14],[130,-12],[140,-14],[148,-20],
       [155,-32],[150,-40],[140,-38],[130,-35],[116,-32],[114,-22]],
      /* Greenland */
      [[-20,82],[-15,80],[-20,76],[-30,72],[-45,68],
       [-58,68],[-60,75],[-50,82],[-30,84],[-20,82]],
    ];

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

      /* ── Continent polygons ── */
      const landFill   = isDark ? 'rgba(45,130,65,.82)'  : 'rgba(80,160,55,.85)';
      const landStroke = isDark ? 'rgba(70,190,90,.35)'  : 'rgba(55,130,35,.40)';

      for (const ring of CONTINENTS) {
        ctx.beginPath();
        let started = false;
        for (const [lo, la] of ring) {
          const phi = la * Math.PI / 180;
          const lam = lo * Math.PI / 180 + rot;
          const z   = Math.cos(phi) * Math.cos(lam);
          if (z < 0) { started = false; continue; }
          const px = cx + R * Math.cos(phi) * Math.sin(lam);
          const py = cy - R * Math.sin(phi);
          if (!started) { ctx.moveTo(px, py); started = true; }
          else ctx.lineTo(px, py);
        }
        ctx.closePath();
        ctx.fillStyle = landFill;
        ctx.fill();
        ctx.strokeStyle = landStroke;
        ctx.lineWidth = 0.7;
        ctx.stroke();
      }

      const lineColor = isDark ? '80,150,255' : '30,80,180';

      /* Parallels (latitude lines) */
      for (let lat = -75; lat <= 75; lat += 15) {
        const yL = cy + R * Math.sin(lat * Math.PI / 180);
        const rL = R  * Math.cos(lat * Math.PI / 180);
        if (rL < 2) continue;
        const bright = lat === 0 ? (isDark ? .38 : .30) : (isDark ? .12 : .09);
        ctx.strokeStyle = `rgba(${lineColor},${bright})`;
        ctx.lineWidth   = lat === 0 ? 0.9 : .5;
        ctx.beginPath();
        ctx.ellipse(cx, yL, rL, rL * .13, 0, 0, Math.PI * 2);
        ctx.stroke();
      }

      /* Meridians (rotating) */
      for (let lon = 0; lon < 180; lon += 15) {
        const phase = (lon * Math.PI / 180 + rot) % (Math.PI * 2);
        const cosP  = Math.cos(phase);
        const base  = isDark ? .05 : .04;
        const range = isDark ? .16 : .12;
        const opacity = base + ((cosP + 1) / 2) * range;
        ctx.strokeStyle = `rgba(${lineColor},${opacity})`;
        ctx.lineWidth   = .55;
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
