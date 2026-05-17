<?php
/*
Template Name: Контакты
*/
?>
<?php get_header(); ?>
    </section>

   <section class="pf-inner">
        <div class="container">
			<br>
			<?php the_breadcrumb(); ?>

            <h1><?php the_title(); ?></h1>

				    <?php if (have_posts()) : ?>
					<?php while (have_posts()) : the_post(); ?>
					<?php the_content(); ?>
					<?php endwhile; ?>
					<?php endif; ?>
			        <?php comments_template(); ?>

		</div>
    </section>

<?php get_footer(); ?>