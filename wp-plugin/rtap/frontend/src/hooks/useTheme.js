import { useState, useEffect } from 'react';

export function useTheme() {
  const getTheme = () => {
    const el = document.documentElement;
    if (el.getAttribute('data-theme') === 'dark') return 'dark';
    if (document.body.getAttribute('data-theme') === 'dark') return 'dark';
    if (el.classList.contains('dark') || document.body.classList.contains('dark')) return 'dark';
    return 'light';
  };

  const [theme, setTheme] = useState(getTheme);

  useEffect(() => {
    const obs = new MutationObserver(() => setTheme(getTheme()));
    obs.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme', 'class'] });
    obs.observe(document.body,            { attributes: true, attributeFilter: ['data-theme', 'class'] });
    return () => obs.disconnect();
  }, []);

  return theme;
}
