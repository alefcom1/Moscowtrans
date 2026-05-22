<?php
/**
 * Template Name: Кейсы
 */
get_header();
?>

  <!-- Hero -->
  <section class="page-hero-simple">
    <div class="container">
      <nav class="cw-breadcrumbs" aria-label="Хлебные крошки">
        <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
        <span class="cw-bc-sep" aria-hidden="true">›</span>
        <span class="cw-bc-current" aria-current="page">Кейсы</span>
      </nav>
      <h1 class="page-hero-title">Кейсы бюро переводов «Ремарка»</h1>
      <p class="page-hero-sub">Реальные проекты, решённые задачи, конкретные результаты</p>
    </div>
  </section>

  <!-- Cases content -->
  <main class="site-main cases-page">
    <div class="container">
      <?php while (have_posts()) : the_post(); ?>
        <div class="page-body">
          <?php the_content(); ?>
        </div>
      <?php endwhile; ?>
    </div>
  </main>

<?php
get_template_part('template-parts/section-calc');
get_footer();
