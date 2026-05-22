<?php
/**
 * Generic page template — used for all pages without a specific template.
 */
get_header();
?>

<main class="site-main page-content">
  <div class="container">
    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('page-article'); ?>>
        <header class="page-header">
          <h1 class="page-title"><?php the_title(); ?></h1>
        </header>
        <div class="page-body">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
</main>

<?php get_footer();
