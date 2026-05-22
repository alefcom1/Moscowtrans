  <script src="/js/megamenu.js"></script>
  <script src="/js/main.js"></script>
  <script src="/js/hero.js"></script>
  <script src="/js/chat.js"></script>
  <script src="/js/animations.js"></script>
  <script src="/js/calc-widget.js"></script>
  <script src="/js/calc-hero-content.js"></script>
<?php if (!empty($extraScripts)) echo $extraScripts; ?>
  <script>
    (function() {
      function updateLogos() {
        var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        var src = isDark ? '/assets/logo-dark.png' : '/assets/logo-light.png';
        document.querySelectorAll('#logo-img, #footer-logo').forEach(function(img) { img.src = src; });
      }
      updateLogos();
      new MutationObserver(updateLogos).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    })();
  </script>
