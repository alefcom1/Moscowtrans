<?php
/**
 * Template Name: Политика конфиденциальности
 */
get_header();
?>

<main class="site-main privacy-page">
  <div class="container">
    <header class="page-header">
      <nav class="cw-breadcrumbs" aria-label="Хлебные крошки">
        <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
        <span class="cw-bc-sep" aria-hidden="true">›</span>
        <span class="cw-bc-current" aria-current="page">Политика конфиденциальности</span>
      </nav>
      <?php while (have_posts()) : the_post(); ?>
        <h1 class="page-title"><?php the_title(); ?></h1>
        <div class="page-body privacy-body">
          <?php the_content(); ?>
        </div>
      <?php endwhile; ?>
    </header>
  </div>
</main>

<?php get_footer();
