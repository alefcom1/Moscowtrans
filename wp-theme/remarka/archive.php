<?php
/**
 * Blog archive / category / tag template.
 */
get_header();

$archive_title = get_the_archive_title();
$archive_desc  = get_the_archive_description();
?>

<main class="site-main">
  <div class="container">

    <header class="blog-archive-header">
      <nav class="cw-breadcrumbs" aria-label="Хлебные крошки">
        <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
        <span class="cw-bc-sep" aria-hidden="true">›</span>
        <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">Блог</a>
        <span class="cw-bc-sep" aria-hidden="true">›</span>
        <span class="cw-bc-current" aria-current="page"><?php echo wp_kses_post($archive_title); ?></span>
      </nav>
      <h1 class="blog-archive-title"><?php echo wp_kses_post($archive_title); ?></h1>
      <?php if ($archive_desc): ?>
        <p class="blog-archive-sub"><?php echo wp_kses_post($archive_desc); ?></p>
      <?php endif; ?>
    </header>

    <div class="blog-layout">

      <div class="blog-main">
        <?php if (have_posts()): ?>
          <div class="blog-post-list">
            <?php while (have_posts()) : the_post(); ?>
              <article id="post-<?php the_ID(); ?>" <?php post_class('blog-row'); ?>>
                <?php if (has_post_thumbnail()): ?>
                  <a class="blog-row-thumb" href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('medium_large', ['alt' => get_the_title()]); ?>
                  </a>
                <?php else: ?>
                  <a class="blog-row-thumb blog-row-thumb--placeholder" href="<?php the_permalink(); ?>">
                    <?php
                    $cats = get_the_category();
                    echo '<span class="brt-icon">';
                    echo $cats ? esc_html(mb_strtoupper(mb_substr($cats[0]->name, 0, 1))) : 'Б';
                    echo '</span>';
                    ?>
                  </a>
                <?php endif; ?>
                <div class="blog-row-body">
                  <?php
                  $cats = get_the_category();
                  if ($cats) {
                    echo '<a href="' . esc_url(get_category_link($cats[0]->term_id)) . '" class="blog-cat-badge">' . esc_html($cats[0]->name) . '</a>';
                  }
                  ?>
                  <h2 class="blog-row-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h2>
                  <div class="blog-row-excerpt"><?php the_excerpt(); ?></div>
                  <div class="blog-row-meta">
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date('j F Y'); ?></time>
                    <a class="blog-row-more" href="<?php the_permalink(); ?>">Читать →</a>
                  </div>
                </div>
              </article>
            <?php endwhile; ?>
          </div>

          <nav class="blog-pagination" aria-label="Навигация по страницам">
            <?php echo paginate_links(['prev_text' => '← Назад', 'next_text' => 'Вперёд →', 'mid_size' => 2]); ?>
          </nav>

        <?php else: ?>
          <p class="no-posts">Записи не найдены.</p>
        <?php endif; ?>
      </div><!-- /blog-main -->

      <?php get_template_part('template-parts/blog-sidebar'); ?>

    </div><!-- /blog-layout -->

  </div>
</main>

<?php get_footer();
