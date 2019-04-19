<html><head>
<?php get_header();?>
<?php get_header('banner');?>

<div id="content" role="main" class="index">
<div id="posts">
<?php if ( have_posts() ) : while (have_posts()) : the_post(); ?>
    <div class="post-style">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                    <?php the_post_thumbnail('thumbnail'); ?>
                    </div> 
                    <?php endif; ?>

	 <div class="post-header">
	 <a href="<?php the_permalink(); ?>" title="the_title_attribute()"><?php the_title(); ?></a>
         </div>
                <div class="post-content">
                        <?php the_content('Continue Reading'); ?>
                            <?php if ( comments_open() ) :
                             comments_popup_link( 'Make or Read Comments', 'Make or Read Comments', 'Make or Read Comments', 'comments-link', 'Comments Closed');
                             endif; ?>
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
        </div></html>