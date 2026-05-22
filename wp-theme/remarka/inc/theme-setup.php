<?php
function remarka_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');

    register_nav_menus([
        'primary' => 'Основное меню',
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
