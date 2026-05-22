<?php
/**
 * 404 Not Found template.
 */
get_header();
?>

<main class="site-main error-404">
  <div class="container">
    <div class="error-404-inner">
      <div class="error-404-code">404</div>
      <h1 class="error-404-title">Страница не найдена</h1>
      <p class="error-404-text">К сожалению, страница, которую вы ищете, не существует или была перемещена. Попробуйте воспользоваться меню или вернитесь на главную страницу.</p>
      <div class="error-404-actions">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">На главную</a>
        <a href="#calc-section" class="btn btn-secondary" onclick="location.href='<?php echo esc_url(home_url('/')); ?>#calc-section'">Рассчитать стоимость</a>
      </div>
      <div class="error-404-links">
        <p>Популярные страницы:</p>
        <ul>
          <li><a href="/yuridicheskiy-perevod/">Юридический перевод</a></li>
          <li><a href="/tekhnicheskiy-perevod/">Технический перевод</a></li>
          <li><a href="/meditsinskiy-perevod/">Медицинский перевод</a></li>
          <li><a href="/stoimost-perevoda/">Стоимость перевода</a></li>
          <li><a href="/yazyki-perevoda/">Языки перевода</a></li>
          <li><a href="/kontakty/">Контакты</a></li>
        </ul>
      </div>
    </div>
  </div>
</main>

<?php get_footer();
