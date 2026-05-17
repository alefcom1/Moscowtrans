<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found" style="background-color: #fff; padding: 30px 15px; text-align: center;">
				<div class="page-header">
					<div class="img404">
						<img src="/wp-content/uploads/2020/02/404.png">
					</div>
					<div class="text404">
						<h1 class="page-title">К сожалению, страница не найдена.</h1>
					</div>
				</div><!-- .page-header -->

				<div class="page-content link404">
					<p>Поробуйте начать с <a href="/">главной страницы</a>.</p>
				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
