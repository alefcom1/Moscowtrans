<?php
function remarka_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');

    register_nav_menus([
        'primary' => 'Основное меню',
        'mobile'  => 'Мобильное меню',
        'footer'  => 'Меню в футере',
    ]);

    add_image_size('blog-thumb', 800, 450, true);
    add_image_size('team-photo', 200, 200, true);
}
add_action('after_setup_theme', 'remarka_setup');

// Remove emoji scripts (saves requests)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Clean up WP head
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

/**
 * Walker: выводит пункты мобильного меню как плоские <a> без <ul>/<li>,
 * чтобы соответствовать CSS-правилу .mobile-drawer nav a.
 */
class Remarka_Mobile_Nav_Walker extends Walker_Nav_Menu {
    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ): void {
        $item   = $data_object;
        $title  = apply_filters( 'the_title', $item->title, $item->ID );
        $target = $item->target ? ' target="' . esc_attr( $item->target ) . '"' : '';
        $rel    = $item->xfn    ? ' rel="'    . esc_attr( $item->xfn )    . '"' : '';
        $output .= '<a href="' . esc_url( $item->url ) . '"' . $target . $rel . '>'
                 . esc_html( $title )
                 . '</a>';
    }
    public function start_lvl( &$output, $depth = 0, $args = null ): void {}
    public function end_lvl( &$output, $depth = 0, $args = null ): void {}
    public function end_el( &$output, $data_object, $depth = 0, $args = null ): void {}
}

/**
 * Fallback: показывается, пока меню не назначено в «Внешний вид → Меню».
 */
function remarka_mobile_menu_fallback(): void {
    echo '<nav aria-label="Мобильное меню">';
    echo '<a href="/#services">Услуги</a>';
    echo '<a href="/#industries">Отрасли</a>';
    echo '<a href="/yazyki-perevoda/">Языки</a>';
    echo '<a href="/stoimost-perevoda/">Цены</a>';
    echo '<a href="/#about">О нас</a>';
    echo '</nav>';
}

