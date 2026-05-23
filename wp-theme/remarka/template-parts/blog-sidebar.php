<?php
/**
 * Blog sidebar — categories + CTA.
 * Used in home.php, archive.php, single.php.
 */
$categories = get_categories(['hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC']);
?>
<aside class="blog-sidebar">

  <!-- Categories widget -->
  <div class="sidebar-widget">
    <h3 class="sidebar-widget-title">Рубрики</h3>
    <ul class="sidebar-cats">
      <li class="sidebar-cat-item <?php echo (!is_category() && !is_single()) ? 'is-active' : ''; ?>">
        <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">
          Все статьи
          <span class="scat-count"><?php echo (int) wp_count_posts()->publish; ?></span>
        </a>
      </li>
      <?php foreach ($categories as $cat): ?>
        <li class="sidebar-cat-item <?php echo is_category($cat->term_id) ? 'is-active' : ''; ?>">
          <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
            <?php echo esc_html($cat->name); ?>
            <span class="scat-count"><?php echo (int) $cat->count; ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- CTA widget -->
  <div class="sidebar-widget sidebar-cta">
    <div class="scta-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </div>
    <h4 class="scta-title">Нужен перевод?</h4>
    <p class="scta-text">Опишите задачу — рассчитаем стоимость и срок за 30 минут.</p>
    <a href="<?php echo esc_url(home_url('/kontakty/')); ?>" class="btn btn-primary">Написать менеджеру</a>
  </div>

</aside>
