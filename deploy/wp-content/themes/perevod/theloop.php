<?php if (have_posts()) : ?>

<div class="news-list in">
<?php /* Включаем сам LOOP */
while (have_posts()) : the_post();
?>

<?php /* сам пост, включает постоянную ссылку, метаданные, счетчик комментариев и текст */ ?>

	<div class="news-item">
                    <h4><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h4>
                    
                    <div class="news-text">
                        		<?php if ( (is_archive()) or (is_search()) ) { ?>
		<?php the_excerpt(); ?>
		<?php } else { ?>
		<?php the_content("Читать дальше..."); ?>
		<?php } ?>
		<?php link_pages('<p><strong>Страницы:</strong> ', '', 'number'); ?>
                    </div>
    </div>


<?php endwhile; ?>

<?php /* Подключение навигации */
if ( (is_archive()) or (is_search()) or (is_paged()) or (is_category()) ) {
/* Подключаем файл */
include (TEMPLATEPATH . '/navigation.php'); }
?>
</div>


<?php /* в случае ошибки 404 */
else :
?>
<div class="news-list news-content">
		<h1>Error</h1>
		<p>Произошла ошибка - проверьте, пожалуйста, правильность запроса.</p>
</div>
<p align="center"><?php include (TEMPLATEPATH . "/searchform.php"); ?></p>
<?php endif; ?>