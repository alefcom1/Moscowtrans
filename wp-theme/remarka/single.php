<?php
/**
 * Single blog post template.
 */
get_header();
?>

<main class="site-main">
  <div class="container">
    <div class="blog-layout blog-layout--single">

      <div class="blog-main">
        <?php while (have_posts()) : the_post(); ?>

          <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>

            <header class="single-article-header">

              <nav class="cw-breadcrumbs" aria-label="Хлебные крошки">
                <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
                <span class="cw-bc-sep" aria-hidden="true">›</span>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">Блог</a>
                <?php
                $cats = get_the_category();
                if ($cats):
                  echo '<span class="cw-bc-sep" aria-hidden="true">›</span>';
                  echo '<a href="' . esc_url(get_category_link($cats[0]->term_id)) . '">' . esc_html($cats[0]->name) . '</a>';
                endif;
                ?>
                <span class="cw-bc-sep" aria-hidden="true">›</span>
                <span class="cw-bc-current" aria-current="page"><?php the_title(); ?></span>
              </nav>

              <?php if ($cats): ?>
                <div class="post-cats">
                  <?php foreach ($cats as $cat): ?>
                    <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="blog-cat-badge"><?php echo esc_html($cat->name); ?></a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <h1 class="single-article-title"><?php the_title(); ?></h1>

              <div class="single-article-meta">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date('j F Y'); ?></time>
                <span class="post-meta-sep">·</span>
                <span><?php echo esc_html(get_the_author()); ?></span>
              </div>

              <?php if (has_post_thumbnail()): ?>
                <div class="single-article-thumb">
                  <?php the_post_thumbnail('full', ['alt' => get_the_title()]); ?>
                </div>
              <?php endif; ?>

            </header>

            <div class="single-article-body prose">
              <?php the_content(); ?>
            </div>

            <footer class="single-article-footer">
              <?php the_tags('<div class="post-tags"><span class="post-tags-label">Теги: </span>', ', ', '</div>'); ?>
            </footer>

          </article>

          <nav class="post-nav" aria-label="Навигация по записям">
            <?php
            $prev = get_previous_post();
            $next = get_next_post();
            if ($prev || $next): ?>
              <div class="post-nav-links">
                <?php if ($prev): ?>
                  <a class="post-nav-item post-nav-prev" href="<?php echo esc_url(get_permalink($prev)); ?>">
                    <span class="pni-arrow">←</span>
                    <span class="pni-text">
                      <span class="pni-label">Предыдущая</span>
                      <span class="pni-title"><?php echo esc_html(get_the_title($prev)); ?></span>
                    </span>
                  </a>
                <?php endif; ?>
                <?php if ($next): ?>
                  <a class="post-nav-item post-nav-next" href="<?php echo esc_url(get_permalink($next)); ?>">
                    <span class="pni-text">
                      <span class="pni-label">Следующая</span>
                      <span class="pni-title"><?php echo esc_html(get_the_title($next)); ?></span>
                    </span>
                    <span class="pni-arrow">→</span>
                  </a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </nav>

        <?php endwhile; ?>
      </div><!-- /blog-main -->

      <?php get_template_part('template-parts/blog-sidebar'); ?>

    </div><!-- /blog-layout -->
  </div>
</main>

<?php get_footer();
