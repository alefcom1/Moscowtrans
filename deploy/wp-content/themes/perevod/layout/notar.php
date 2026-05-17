<?php
/*
Template Name: Notar
*/
?>
<?php get_header(); ?>
    <div id="area" class="layout--2">
        <div class="container">
            <div id="content">
                <?php the_breadcrumb(); ?>
                <div class="grid">
                    <div class="row">
                        <div class="column column-12">
                            <div class="u-mb1">
                                <h1><?php the_title(); ?></h1>
                                <?php if (have_posts()) : ?>
								<?php while (have_posts()) : the_post(); ?>
								<?php the_content(); ?>
								<?php endwhile; ?>
								<?php endif; ?>   
								<div align="center"><?php echo do_shortcode('[contact-form-7 id="887" title="Стоимость перевода"]'); ?></div>
								<h2></h2><?php comments_template(); ?>
							</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sidebar" class="default">
                <div id="nav_menu-3" class="widget-odd widget-last widget-first widget-1 widget widget_nav_menu">
                    <div class="widget__inner">
                        <h2></h2>
                        <div class="menu-information-container">
							<?php wp_nav_menu( array(
								'theme_location'  => '',
								'menu'            => 'Notar',
								'container'       => '',
								'container_class' => '',
								'container_id'    => 'menu-information',
								'menu_class'      => 'menu',
								'menu_id'         => '',
								'echo'            => true,
								'fallback_cb'     => 'wp_page_menu',
								'link_before'     => '',
								'link_after'      => '',
								'depth'           => 0,
								'walker'          => '',
							) ); ?>
                        </div>
                    </div>
                </div>
                <div class="test-widget">
                    <h4>Наш рейтинг</h4>
                    <iframe src="https://yandex.ru/sprav/widget/rating-badge/51867347382?type=rating" width="150" height="50" frameborder="0"></iframe>
                </div>
                
            </div>
        </div>
    </div>
<?php get_footer(); ?>