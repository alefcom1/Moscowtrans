<?php
/**
 * Register Custom Post Types and Taxonomies.
 *
 * remarka_sub_service — sub-level service pages
 * (e.g. /yuridicheskiy-perevod/dogovor-perevod/)
 */

function remarka_register_post_types() {
    register_post_type('remarka_sub_service', [
        'labels' => [
            'name'               => 'Подстраницы услуг',
            'singular_name'      => 'Подстраница услуги',
            'add_new'            => 'Добавить',
            'add_new_item'       => 'Добавить подстраницу',
            'edit_item'          => 'Редактировать',
            'new_item'           => 'Новая подстраница',
            'view_item'          => 'Смотреть',
            'search_items'       => 'Поиск',
            'not_found'          => 'Не найдено',
            'not_found_in_trash' => 'Корзина пуста',
        ],
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_rest'        => true,
        'query_var'           => true,
        'rewrite'             => ['slug' => '', 'with_front' => false],
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-translation',
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'],
    ]);

    // Taxonomy: service_category (ties sub-pages to parent services)
    register_taxonomy('service_category', 'remarka_sub_service', [
        'labels' => [
            'name'          => 'Категории услуг',
            'singular_name' => 'Категория услуги',
            'add_new_item'  => 'Добавить категорию',
            'edit_item'     => 'Редактировать категорию',
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'service-category'],
    ]);
}
add_action('init', 'remarka_register_post_types');

// Flush rewrite rules on theme activation
function remarka_flush_rewrite_rules() {
    remarka_register_post_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'remarka_flush_rewrite_rules');
