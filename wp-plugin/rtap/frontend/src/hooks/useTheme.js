import { useState, useEffect } from 'react';

export function detectTheme() {
  const html  = document.documentElement;
  const body  = document.body;

  // Explicit attribute checks
  if (html.getAttribute('data-theme') === 'dark') return 'dark';
  if (body.getAttribute('data-theme') === 'dark')  return 'dark';
  if (html.getAttribute('data-theme') === 'light') return 'light';
  if (body.getAttribute('data-theme') === 'light') return 'light';

  // Common dark-mode class names
  const darkClasses = ['dark', 'dark-mode', 'theme-dark', 'night-mode', 'dark-theme'];
  for (const cls of darkClasses) {
    if (html.classList.contains(cls) || body.classList.contains(cls)) return 'dark';
  }

  // Detect by computed background brightness of <body>
  try {
    const bg  = window.getComputedStyle(body).backgroundColor;
    const rgb = bg.match(/\d+/g);
    if (rgb && rgb.length >= 3) {
      const brightness = (parseInt(rgb[0]) * 299 + parseInt(rgb[1]) * 587 + parseInt(rgb[2]) * 114) / 1000;
      if (brightness < 100) return 'dark';
    }
  } catch (_) { /* ignore */ }

  // OS-level preference
  if (window.matchMedia?.('(prefers-color-scheme: dark)').matches) return 'dark';

  return 'light';
}

export function useTheme() {
  const [theme, setTheme] = useState(detectTheme);

  useEffect(() => {
    const refresh = () => setTheme(detectTheme());

    const obs = new MutationObserver(refresh);
    obs.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme', 'class', 'style'] });
    obs.observe(document.body,            { attributes: true, attributeFilter: ['data-theme', 'class', 'style'] });

    const mq = window.matchMedia?.('(prefers-color-scheme: dark)');
    mq?.addEventListener('change', refresh);

    return () => { obs.disconnect(); mq?.removeEventListener('change', refresh); };
  }, []);

  return theme;
}
