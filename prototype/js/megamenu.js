(function () {
  'use strict';

  /* ── Выпадающее меню ─────────────────────────── */
  var navItems = document.querySelectorAll('.nav-item[data-dropdown]');

  navItems.forEach(function (item) {
    var trigger = item.querySelector('.nav-trigger');
    if (!trigger) return;

    trigger.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = item.classList.contains('is-open');
      navItems.forEach(function (i) {
        i.classList.remove('is-open');
        var t = i.querySelector('.nav-trigger');
        if (t) t.setAttribute('aria-expanded', 'false');
      });
      if (!isOpen) {
        item.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
      }
    });
  });

  document.addEventListener('click', function () {
    navItems.forEach(function (i) {
      i.classList.remove('is-open');
      var t = i.querySelector('.nav-trigger');
      if (t) t.setAttribute('aria-expanded', 'false');
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      navItems.forEach(function (i) {
        i.classList.remove('is-open');
        var t = i.querySelector('.nav-trigger');
        if (t) t.setAttribute('aria-expanded', 'false');
      });
      closeStartModal();
    }
  });

  /* ── Модал «Как начать» ─────────────────────── */
  var startModal = document.getElementById('startModal');

  function openStartModal() {
    if (!startModal) return;
    startModal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeStartModal() {
    if (!startModal) return;
    startModal.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('[data-open="start-modal"]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      openStartModal();
    });
  });

  var smClose = document.getElementById('smClose');
  if (smClose) smClose.addEventListener('click', closeStartModal);

  if (startModal) {
    startModal.addEventListener('click', function (e) {
      if (e.target === startModal) closeStartModal();
    });
  }

  /* Опции модала */
  var smOptions = document.querySelectorAll('.sm-option[data-action]');
  smOptions.forEach(function (opt) {
    opt.addEventListener('click', function (e) {
      e.preventDefault();
      var action = opt.dataset.action;
      closeStartModal();
      if (action === 'calc' || action === 'order') {
        var target = document.getElementById('calc-section');
        if (target) {
          setTimeout(function () {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 200);
        }
      } else if (action === 'pricing') {
        var p = document.getElementById('pricing');
        if (p) {
          setTimeout(function () {
            p.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 200);
        } else {
          window.location.href = 'stoimost-perevoda.html';
        }
      }
    });
  });

}());
