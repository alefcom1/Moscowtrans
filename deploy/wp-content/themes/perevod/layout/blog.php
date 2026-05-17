<?php 
/*
    Template Name: Blog
*/
?>
<?php get_header(); ?>
     </section>
    <section class="pf-blog">
        <div class="container">           
           
            <h2>Новости</h2>
            <div class="row">
				<!--?php // Display blog posts on any page @ http://m0n.co/l
				$temp = $wp_query; $wp_query= null;
				$wp_query = new WP_Query(); $wp_query->query('showposts=9' . '&paged='.$paged);
				while ($wp_query->have_posts()) : $wp_query->the_post(); ?-->
				<?php	
                         $page = (get_query_var('paged')) ? get_query_var('paged') : 1;       
                                query_posts('cat=10&showposts=12&paged='.$page); // вместо "5" указываем идентификатор вашей рубрики.
while (have_posts()) : the_post();?>

                <div class="col-md-4">
                    <a href="<?php the_permalink(); ?>" class="blog-item">
                        <div class="blog-img"><?php the_post_thumbnail(array(360,250));?></div>
                        <div class="blog-text">
                            <div class="blog-time"><?php echo get_the_date('d.m.Y'); ?></div>
                            <h4><?php the_title(); ?></h4>
                        </div>
                    </a>
                </div>
				
 
        <?php endwhile; ?>
  
 

            </div>
			<?php wp_pagenavi(); ?>

        </div>
    </section> 
 
<?php get_footer(); ?>