<?php
/**
 * Ремарка — functions.php
 * Loads all theme modules from inc/.
 */

require_once get_template_directory() . '/inc/theme-setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/ajax.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/setup-pages.php';
require_once get_template_directory() . '/inc/subservice-visuals.php';

// setup-subpages.php is 6 MB — load only when explicitly triggered
add_action('init', function () {
    if (!empty($_GET['remarka_setup_subpages']) && current_user_can('manage_options')) {
        require_once get_template_directory() . '/inc/setup-subpages.php';
    }
}, 1);

// SEO descriptions setup — load only when explicitly triggered
add_action('init', function () {
    if (!empty($_GET['remarka_setup_seo']) && current_user_can('manage_options')) {
        require_once get_template_directory() . '/inc/setup-seo-descriptions.php';
    }
}, 1);

/**
 * Helper: get custom field with fallback.
 */
function remarka_meta(string $key, string $default = ''): string {
    global $post;
    $val = get_post_meta($post->ID ?? 0, $key, true);
    return $val !== '' && $val !== false ? (string) $val : $default;
}

/**
 * Helper: output Schema.org JSON-LD for current page.
 * Stored as post meta '_remarka_schema'.
 */
function remarka_schema(): void {
    global $post;
    if (empty($post)) return;
    $schema = get_post_meta($post->ID, '_remarka_schema', true);
    if ($schema) {
        echo '<script type="application/ld+json">' . $schema . '</script>' . "\n";
    }
}

/**
 * Blog section on homepage: latest 3 posts.
 */
function remarka_latest_posts(): WP_Query {
    return new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

/**
 * Yandex Metrika counter — output in <head>.
 */
function remarka_yandex_metrika(): void {
    ?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){
        m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for(var j=0;j<document.scripts.length;j++){if(document.scripts[j].src===r){return;}}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
    })(window,document,'script','https://mc.yandex.ru/metrika/tag.js','ym');
    ym(95836354,'init',{webvisor:true,clickmap:true,referrer:document.referrer,url:location.href,accurateTrackBounce:true,trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/95836354" style="position:absolute;left:-9999px;" alt=""></div></noscript>
<!-- /Yandex.Metrika counter -->
    <?php
}
add_action('wp_head', 'remarka_yandex_metrika', 1);

/**
 * Add Google Fonts to wp_head (faster than wp_enqueue_style for external fonts).
 */
function remarka_google_fonts(): void {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap&subset=cyrillic" rel="stylesheet">' . "\n";
}
add_action('wp_head', 'remarka_google_fonts', 2);

/**
 * Add favicon.
 */
function remarka_favicon(): void {
    $uri = get_template_directory_uri();
    echo '<link rel="icon" type="image/x-icon" href="' . $uri . '/assets/images/favicon.ico">' . "\n";
}
add_action('wp_head', 'remarka_favicon', 3);
