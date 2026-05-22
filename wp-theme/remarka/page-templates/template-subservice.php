<?php
/**
 * Template Name: Подстраница услуги
 *
 * Used for all subservice pages (yuridicheskiy-perevod/dogovory/, etc.).
 * Content is stored in post_content as raw HTML sections from the prototype.
 */

$post_id  = get_the_ID();
$parent   = get_post($post->post_parent ?? 0);
$bc_label = get_post_meta($post_id, '_hero_breadcrumb', true) ?: get_the_title();
$g1       = get_post_meta($post_id, '_hero_greeting_1', true) ?: 'Здравствуйте! 👋 Чем могу помочь?';
$g2       = get_post_meta($post_id, '_hero_greeting_2', true) ?: 'Опишите задачу или загрузите документ — рассчитаем стоимость и срок.';
$g3       = get_post_meta($post_id, '_hero_greeting_3', true) ?: 'Конфиденциальность гарантирована. NDA подписывается до передачи файлов.';

$breadcrumb_parent = null;
if ($parent && $parent->ID) {
    $breadcrumb_parent = [
        'url'   => get_permalink($parent->ID),
        'label' => get_the_title($parent->ID),
    ];
}

// Disable wpautop so raw HTML sections render correctly
remove_filter('the_content', 'wpautop');

get_header();

get_template_part('template-parts/hero-chat-window', null, [
    'greeting_1'       => $g1,
    'greeting_2'       => $g2,
    'greeting_3'       => $g3,
    'breadcrumb'       => $bc_label,
    'breadcrumb_parent'=> $breadcrumb_parent,
    'home_dots'        => false,
]);
?>
  </div><!-- /hero-bg-block -->

  <?php while (have_posts()) : the_post(); ?>
    <div class="subservice-content">
      <?php the_content(); ?>
    </div>
  <?php endwhile; ?>

<?php
get_template_part('template-parts/section-calc');

get_footer();
