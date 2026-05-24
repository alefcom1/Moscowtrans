import { useState, useEffect } from 'react';

function getBrightness(el) {
  try {
    const bg = window.getComputedStyle(el).backgroundColor;
    const rgb = bg.match(/[\d.]+/g);
    if (!rgb || rgb.length < 3) return null;
    const r = parseInt(rgb[0]), g = parseInt(rgb[1]), b = parseInt(rgb[2]);
    const a = rgb[3] !== undefined ? parseFloat(rgb[3]) : 1;
    if (a < 0.05) return null; // transparent — ignore
    return (r * 299 + g * 587 + b * 114) / 1000;
  } catch { return null; }
}

export function detectTheme(rootEl) {
  // 1. Explicit data-theme attribute on html/body
  for (const el of [document.documentElement, document.body]) {
    const v = el.getAttribute('data-theme');
    if (v === 'dark')  return 'dark';
    if (v === 'light') return 'light';
  }

  // 2. Common dark-mode class names
  const darkClasses = ['dark', 'dark-mode', 'theme-dark', 'night-mode'];
  for (const el of [document.documentElement, document.body]) {
    if (darkClasses.some(c => el.classList.contains(c))) return 'dark';
  }

  // 3. Walk up from widget container to find a non-transparent background
  const candidates = [
    rootEl?.parentElement,
    document.body,
    document.documentElement,
  ].filter(Boolean);

  for (const el of candidates) {
    let node = el;
    let depth = 0;
    while (node && node !== document && depth < 8) {
      const b = getBrightness(node);
      if (b !== null) return b < 128 ? 'dark' : 'light';
      node = node.parentElement;
      depth++;
    }
  }

  // 4. OS preference
  if (window.matchMedia?.('(prefers-color-scheme: dark)').matches) return 'dark';

  return 'light';
}

export function useTheme(rootEl) {
  const [theme, setTheme] = useState(() => detectTheme(rootEl));

  useEffect(() => {
    const refresh = () => setTheme(detectTheme(rootEl));

    const obs = new MutationObserver(refresh);
    obs.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme', 'class', 'style'] });
    obs.observe(document.body,            { attributes: true, attributeFilter: ['data-theme', 'class', 'style'] });

    const mq = window.matchMedia?.('(prefers-color-scheme: dark)');
    mq?.addEventListener('change', refresh);

    // Re-check once after styles settle
    const tid = setTimeout(refresh, 200);

    return () => { obs.disconnect(); mq?.removeEventListener('change', refresh); clearTimeout(tid); };
  }, [rootEl]);

  return theme;
}
