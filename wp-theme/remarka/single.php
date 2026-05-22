<?php
/**
 * Single blog post template.
 */
get_header();
?>

<main class="site-main single-post-main">
  <div class="container">
    <?php while (have_posts()) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>

        <header class="single-post-header">
          <nav class="cw-breadcrumbs post-breadcrumbs" aria-label="Хлебные крошки">
            <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
            <span class="cw-bc-sep" aria-hidden="true">›</span>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">Блог</a>
            <span class="cw-bc-sep" aria-hidden="true">›</span>
            <span class="cw-bc-current" aria-current="page"><?php the_title(); ?></span>
          </nav>

          <?php
          $cats = get_the_category();
          if ($cats) {
            echo '<div class="post-cats">';
            foreach ($cats as $cat) {
              echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '" class="post-cat-badge">' . esc_html($cat->name) . '</a>';
            }
            echo '</div>';
          }
          ?>

          <h1 class="single-post-title"><?php the_title(); ?></h1>

          <div class="post-meta">
            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date('j F Y'); ?></time>
            <span class="post-meta-sep">·</span>
            <span><?php echo esc_html(get_the_author()); ?></span>
          </div>

          <?php if (has_post_thumbnail()): ?>
            <div class="single-post-thumb">
              <?php the_post_thumbnail('full', ['alt' => get_the_title()]); ?>
            </div>
          <?php endif; ?>
        </header>

        <div class="single-post-body">
          <?php the_content(); ?>
        </div>

        <footer class="single-post-footer">
          <div class="post-tags">
            <?php the_tags('<span class="post-tags-label">Теги: </span>', ', ', ''); ?>
          </div>
        </footer>

      </article>

      <nav class="post-navigation" aria-label="Навигация по записям">
        <?php
        $prev = get_previous_post();
        $next = get_next_post();
        if ($prev || $next): ?>
          <div class="post-nav-links">
            <?php if ($prev): ?>
              <a class="post-nav-prev" href="<?php echo esc_url(get_permalink($prev)); ?>">
                <span class="nav-arrow">←</span>
                <span class="nav-text">
                  <span class="nav-label">Предыдущая статья</span>
                  <span class="nav-title"><?php echo esc_html(get_the_title($prev)); ?></span>
                </span>
              </a>
            <?php endif; ?>
            <?php if ($next): ?>
              <a class="post-nav-next" href="<?php echo esc_url(get_permalink($next)); ?>">
                <span class="nav-text">
                  <span class="nav-label">Следующая статья</span>
                  <span class="nav-title"><?php echo esc_html(get_the_title($next)); ?></span>
                </span>
                <span class="nav-arrow">→</span>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </nav>

    <?php endwhile; ?>
  </div>
</main>

<?php get_footer();
