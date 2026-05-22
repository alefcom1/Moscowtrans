<?php
function remarka_enqueue_assets() {
    $ver = '1.0.0';
    $uri = get_template_directory_uri();

    // ── CSS ──────────────────────────────────────────────────────────────
    wp_enqueue_style('remarka-tokens',      $uri . '/assets/css/tokens.css',      [], $ver);
    wp_enqueue_style('remarka-styles',      $uri . '/assets/css/styles.css',      ['remarka-tokens'], $ver);
    wp_enqueue_style('remarka-megamenu',    $uri . '/assets/css/megamenu.css',    ['remarka-styles'], $ver);
    wp_enqueue_style('remarka-calc-widget', $uri . '/assets/css/calc-widget.css', ['remarka-styles'], $ver);

    // ── External SDK (calc widget dependencies) — load in <head> ─────────
    wp_enqueue_script('emailjs',   'https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js', [], null, false);
    wp_enqueue_script('tesseract', 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js', [], null, false);
    wp_enqueue_script('mammoth',   'https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js', [], null, false);
    wp_enqueue_script('franc',     'https://cdn.jsdelivr.net/npm/franc-min@6.2.0/index.min.js',         [], null, false);

    // ── Theme JS ──────────────────────────────────────────────────────────
    wp_enqueue_script('remarka-megamenu',          $uri . '/assets/js/megamenu.js',          [],                      $ver, true);
    wp_enqueue_script('remarka-main',              $uri . '/assets/js/main.js',              ['remarka-megamenu'],    $ver, true);
    wp_enqueue_script('remarka-hero',              $uri . '/assets/js/hero.js',              ['remarka-main'],        $ver, true);
    wp_enqueue_script('remarka-chat',              $uri . '/assets/js/chat.js',              ['remarka-main'],        $ver, true);
    wp_enqueue_script('remarka-animations',        $uri . '/assets/js/animations.js',        ['remarka-main'],        $ver, true);
    wp_enqueue_script('remarka-calc-widget',       $uri . '/assets/js/calc-widget.js',       ['emailjs', 'tesseract', 'mammoth', 'franc'], $ver, true);
    wp_enqueue_script('remarka-calc-hero-content', $uri . '/assets/js/calc-hero-content.js', ['remarka-calc-widget'], $ver, true);

    // Pass WP AJAX URL + nonce to calc-widget.js
    wp_localize_script('remarka-calc-widget', 'remarka_ajax', [
        'url'    => admin_url('admin-ajax.php'),
        'nonce'  => wp_create_nonce('remarka_upload_nonce'),
        'action' => 'remarka_upload',
    ]);

    // Logo theme-switch script (inline, after DOM ready)
    $logo_script = '
    (function() {
        function updateLogos() {
            var isDark = document.documentElement.getAttribute("data-theme") === "dark";
            var src = isDark ? "' . $uri . '/assets/images/logo-dark.png" : "' . $uri . '/assets/images/logo-light.png";
            document.querySelectorAll("#logo-img, #footer-logo").forEach(function(img) { img.src = src; });
        }
        updateLogos();
        new MutationObserver(updateLogos).observe(document.documentElement, { attributes: true, attributeFilter: ["data-theme"] });
    })();';
    wp_add_inline_script('remarka-main', $logo_script);
}
add_action('wp_enqueue_scripts', 'remarka_enqueue_assets');
