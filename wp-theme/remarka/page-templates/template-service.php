<?php
/**
 * Template Name: Страница услуги
 *
 * Used for all translation service pages (yuridicheskiy-perevod, tekhnicheskiy-perevod, etc.).
 * Custom fields (set via ACF or custom meta boxes):
 *   _hero_greeting_1   — Olga's first chat message
 *   _hero_greeting_2   — Olga's second chat message
 *   _hero_greeting_3   — Olga's third chat message
 *   _hero_breadcrumb   — breadcrumb label (defaults to page title)
 *   _calc_heading      — optional calculator section heading override
 *   _calc_sub          — optional calculator section sub text override
 */

$post_id   = get_the_ID();
$bc_label  = get_post_meta($post_id, '_hero_breadcrumb', true) ?: get_the_title();
$g1        = get_post_meta($post_id, '_hero_greeting_1',  true) ?: 'Здравствуйте! 👋 Чем могу помочь?';
$g2        = get_post_meta($post_id, '_hero_greeting_2',  true) ?: 'Опишите задачу или загрузите документ — рассчитаем стоимость и срок.';
$g3        = get_post_meta($post_id, '_hero_greeting_3',  true) ?: 'Конфиденциальность гарантирована. NDA подписывается до передачи файлов.';
$calc_h    = get_post_meta($post_id, '_calc_heading',     true) ?: '';
$calc_sub  = get_post_meta($post_id, '_calc_sub',         true) ?: '';

get_header();

get_template_part('template-parts/hero-chat-window', null, [
    'greeting_1' => $g1,
    'greeting_2' => $g2,
    'greeting_3' => $g3,
    'breadcrumb' => $bc_label,
    'home_dots'  => false,
]);
?>
  </div><!-- /hero-bg-block -->

  <?php while (have_posts()) : the_post(); ?>
    <div class="service-page-content">
      <?php the_content(); ?>
    </div>
  <?php endwhile; ?>

<?php
get_template_part('template-parts/section-calc', null, array_filter([
    'heading' => $calc_h,
    'sub'     => $calc_sub,
]));

get_footer();
