<?php
/**
 * Blog archive / category / tag template.
 */
get_header();

$archive_title = get_the_archive_title();
$archive_desc  = get_the_archive_description();
?>

<main class="site-main blog-archive">
  <div class="container">

    <header class="archive-header">
      <nav class="cw-breadcrumbs" aria-label="Хлебные крошки">
        <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
        <span class="cw-bc-sep" aria-hidden="true">›</span>
        <span class="cw-bc-current" aria-current="page">Блог</span>
      </nav>
      <h1 class="archive-title"><?php echo wp_kses_post($archive_title); ?></h1>
      <?php if ($archive_desc): ?>
        <div class="archive-desc"><?php echo wp_kses_post($archive_desc); ?></div>
      <?php endif; ?>
    </header>

    <?php if (have_posts()): ?>
      <div class="blog-grid">
        <?php while (have_posts()) : the_post(); ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
            <?php if (has_post_thumbnail()): ?>
              <a class="blog-card-thumb" href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('blog-thumb', ['alt' => get_the_title()]); ?>
              </a>
            <?php endif; ?>
            <div class="blog-card-body">
              <?php
              $cats = get_the_category();
              if ($cats) {
                echo '<a href="' . esc_url(get_category_link($cats[0]->term_id)) . '" class="blog-card-cat">' . esc_html($cats[0]->name) . '</a>';
              }
              ?>
              <h2 class="blog-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h2>
              <div class="blog-card-excerpt"><?php the_excerpt(); ?></div>
              <div class="blog-card-meta">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date('j F Y'); ?></time>
              </div>
              <a class="blog-card-link" href="<?php the_permalink(); ?>">Читать далее →</a>
            </div>
          </article>
        <?php endwhile; ?>
      </div>

      <nav class="pagination" aria-label="Навигация по страницам">
        <?php
        echo paginate_links([
          'prev_text' => '← Назад',
          'next_text' => 'Вперёд →',
          'mid_size'  => 2,
        ]);
        ?>
      </nav>

    <?php else: ?>
      <p class="no-posts">Записи не найдены.</p>
    <?php endif; ?>

  </div>
</main>

<?php get_footer();
