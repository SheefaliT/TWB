<html><head>
<?php get_header();?>
<?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?>
<?php get_header('banner');?>
        
<div id="content" role="main" class="singlepost">
<div id="posts"><br />
<?php if ( have_posts() ) : while (have_posts()) : the_post(); ?>

    <div class="post-style">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                    <?php the_post_thumbnail('thumbnail'); ?>
                    </div> 
                    <?php endif; ?>

	 <div class="post-header"><?php the_title(); ?></div> 
              
        	<div class="post-content">
                <?php the_content(); ?>
                <?php comments_template(); ?>
                </div>
    </div>
<?php endwhile; ?>
    <?php else: ?><div class="post-header">Sorry no Posts</div><?php endif; ?>                  
</div>
   <div id="moreposts">    
	<?php next_posts_link('Older Entries', 0); wp_link_pages(); previous_posts_link('Newer Entries', 0); ?>
   </div></div>

</div>        
    <?php get_footer(); ?>
        </div>
</html>