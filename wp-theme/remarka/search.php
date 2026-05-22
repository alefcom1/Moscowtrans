<?php
/**
 * Search results template.
 */
get_header();
$query_str = get_search_query();
?>

<main class="site-main search-results">
  <div class="container">

    <header class="search-header">
      <h1 class="search-title">
        <?php if ($query_str): ?>
          Результаты поиска: «<?php echo esc_html($query_str); ?>»
        <?php else: ?>
          Поиск
        <?php endif; ?>
      </h1>
      <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="search" name="s" class="search-input" value="<?php echo esc_attr($query_str); ?>" placeholder="Введите запрос...">
        <button type="submit" class="btn btn-primary">Найти</button>
      </form>
    </header>

    <?php if (have_posts()): ?>
      <div class="search-count">
        Найдено результатов: <?php echo $wp_query->found_posts; ?>
      </div>
      <div class="blog-grid">
        <?php while (have_posts()) : the_post(); ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
            <?php if (has_post_thumbnail()): ?>
              <a class="blog-card-thumb" href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('blog-thumb', ['alt' => get_the_title()]); ?>
              </a>
            <?php endif; ?>
            <div class="blog-card-body">
              <h2 class="blog-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h2>
              <div class="blog-card-excerpt"><?php the_excerpt(); ?></div>
              <a class="blog-card-link" href="<?php the_permalink(); ?>">Читать далее →</a>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
      <nav class="pagination" aria-label="Навигация по страницам">
        <?php echo paginate_links(['prev_text' => '← Назад', 'next_text' => 'Вперёд →', 'mid_size' => 2]); ?>
      </nav>
    <?php else: ?>
      <p class="no-posts">По вашему запросу «<?php echo esc_html($query_str); ?>» ничего не найдено.</p>
      <p>Попробуйте воспользоваться нашим <a href="<?php echo esc_url(home_url('/#calc-section')); ?>">калькулятором стоимости</a> или <a href="/kontakty/">свяжитесь с нами</a>.</p>
    <?php endif; ?>

  </div>
</main>

<?php get_footer();
