(function () {
  'use strict';

  /* ── Включаем анимации только при наличии JS ── */
  document.body.classList.add('js-loaded');

  var STAGGER = 75; // мс между карточками в сетке

  /* ── Настройки Observer ── */
  var observerOpts = {
    threshold: 0.10,
    rootMargin: '0px 0px -32px 0px'
  };

  /* ── 1. Анимация появления блоков ── */

  /* Элементы, анимируемые по одному (fade-up) */
  var SINGLE_SELECTORS = [
    '.sec-head',
    '.langs-stat',
    '.langs-cloud',
    '.intro-grid',
    '.vol-table-wrap',
    '.cmp-table-wrap',
    '.cta-title',
    '.cta-sub',
    '.cta-btns'
  ];

  /* Дочерние элементы, анимируемые внутри сетки (stagger) */
  var GRID_SELECTORS = [
    '.topics-grid .topic-card',
    '.team-grid .team-card',
    '.pricing-row .price-card',
    '.reviews-grid .review-card',
    '.blog-grid .blog-card',
    '.steps-row .step-item',
    '.translators-grid .translator-card',
    '.guarantees-grid .guarantee-card',
    '.docs-accordion .doc-item',
    '.faq-list .faq-item',
    '.intro-features .intro-feat'
  ];

  /* Статистика — scale + stagger */
  var SCALE_SELECTORS = ['.stats-row .stat-item'];

  /* Языковые пилюли — stagger, но быстро */
  var PILL_STAGGER = 30;

  function addAnimClass(el, cls, delay) {
    el.classList.add(cls);
    if (delay) el.style.transitionDelay = delay + 'ms';
  }

  /* Применяем классы анимации */
  SINGLE_SELECTORS.forEach(function (sel) {
    document.querySelectorAll(sel).forEach(function (el) {
      addAnimClass(el, 'anim-fade-up');
    });
  });

  GRID_SELECTORS.forEach(function (sel) {
    /* Группируем по родителю, чтобы stagger был внутри каждой сетки */
    var parentMap = new Map();
    document.querySelectorAll(sel).forEach(function (el) {
      var p = el.parentElement;
      if (!parentMap.has(p)) parentMap.set(p, []);
      parentMap.get(p).push(el);
    });
    parentMap.forEach(function (children) {
      children.forEach(function (el, i) {
        addAnimClass(el, 'anim-fade-up', Math.min(i, 5) * STAGGER);
      });
    });
  });

  SCALE_SELECTORS.forEach(function (sel) {
    document.querySelectorAll(sel).forEach(function (el, i) {
      addAnimClass(el, 'anim-scale', i * STAGGER);
    });
  });

  /* Языковые пилюли — fade только */
  document.querySelectorAll('.langs-cloud .lang-pill').forEach(function (el, i) {
    addAnimClass(el, 'anim-fade', Math.min(i, 10) * PILL_STAGGER);
  });

  /* ── 2. IntersectionObserver — добавляет is-visible ── */
  var allAnimated = document.querySelectorAll(
    '.anim-fade-up, .anim-scale, .anim-fade'
  );

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, observerOpts);

    allAnimated.forEach(function (el) { io.observe(el); });
  } else {
    /* Fallback для старых браузеров — показываем сразу */
    allAnimated.forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* ── 3. Счётчики цифр ── */
  function parseStatNum(el) {
    /* Структура: <span class="stat-num">2 400<span class="stat-suffix">+</span></span> */
    var textNode = null;
    for (var i = 0; i < el.childNodes.length; i++) {
      if (el.childNodes[i].nodeType === 3 && el.childNodes[i].textContent.trim()) {
        textNode = el.childNodes[i];
        break;
      }
    }
    if (!textNode) return null;
    var raw = textNode.textContent.replace(/[\s ]/g, '');
    var num = parseFloat(raw);
    if (isNaN(num)) return null;
    return { node: textNode, target: num, isFloat: raw.includes('.'), original: textNode.textContent };
  }

  function formatNum(n, isFloat, original) {
    if (isFloat) return n.toFixed(2);
    /* Сохраняем формат оригинала (пробел как разделитель тысяч) */
    var s = Math.round(n).toString();
    if (s.length > 3) s = s.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return s;
  }

  function runCounter(info) {
    var duration = 1600;
    var start = null;

    function tick(ts) {
      if (!start) start = ts;
      var elapsed = ts - start;
      var p = Math.min(elapsed / duration, 1);
      /* ease-out cubic */
      var eased = 1 - Math.pow(1 - p, 3);
      info.node.textContent = formatNum(info.target * eased, info.isFloat, info.original);
      if (p < 1) {
        requestAnimationFrame(tick);
      } else {
        info.node.textContent = info.original;
      }
    }
    requestAnimationFrame(tick);
  }

  if ('IntersectionObserver' in window) {
    var statsSection = document.querySelector('.sec-stats');
    if (statsSection) {
      var counterDone = false;
      var counterIO = new IntersectionObserver(function (entries) {
        if (counterDone) return;
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            counterDone = true;
            counterIO.disconnect();
            statsSection.querySelectorAll('.stat-num').forEach(function (el) {
              var info = parseStatNum(el);
              if (info) runCounter(info);
            });
          }
        });
      }, { threshold: 0.5 });
      counterIO.observe(statsSection);
    }
  }

})();
