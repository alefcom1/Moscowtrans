/* ============================================================
   REMARKA — Prototype JS
   Темы, мобильное меню, переключатель языка.
   ============================================================ */

(() => {
  /* ─── Тема: dark / light ─────────────────────────────── */
  const THEME_KEY = 'remarka.theme';
  const root = document.documentElement;

  function getAutoTheme() {
    const hour = new Date().getHours();
    // Светлая 7:00–19:00, тёмная — остальное время
    return (hour >= 7 && hour < 19) ? 'light' : 'dark';
  }

  function applyTheme(theme) {
    root.setAttribute('data-theme', theme);
  }

  function initTheme() {
    const stored = localStorage.getItem(THEME_KEY);
    if (stored === 'light' || stored === 'dark') {
      applyTheme(stored);
      return;
    }
    // Первый визит: системная тема или по времени суток
    const systemDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches;
    applyTheme(systemDark ? 'dark' : getAutoTheme());
  }

  function toggleTheme() {
    const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    applyTheme(next);
    localStorage.setItem(THEME_KEY, next);
  }

  initTheme();
  document.querySelector('.theme-toggle')?.addEventListener('click', toggleTheme);


  /* ─── Header: shadow на скролле ──────────────────────── */
  const header = document.querySelector('.site-header');
  function updateHeader() {
    header?.classList.toggle('is-scrolled', window.scrollY > 8);
  }
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });


  /* ─── Переключатель языка ────────────────────────────── */
  const langSwitcher = document.querySelector('.lang-switcher');
  langSwitcher?.querySelector('.lang-switcher-btn')?.addEventListener('click', (e) => {
    e.stopPropagation();
    langSwitcher.classList.toggle('is-open');
  });
  document.addEventListener('click', () => langSwitcher?.classList.remove('is-open'));


  /* ─── Мобильное меню ─────────────────────────────────── */
  const mobileToggle = document.querySelector('.mobile-toggle');
  const mobileDrawer = document.querySelector('.mobile-drawer');
  mobileToggle?.addEventListener('click', () => {
    mobileDrawer?.classList.toggle('is-open');
    const isOpen = mobileDrawer?.classList.contains('is-open');
    document.body.style.overflow = isOpen ? 'hidden' : '';
  });
})();
