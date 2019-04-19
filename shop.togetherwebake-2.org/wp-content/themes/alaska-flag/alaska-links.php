<html><head><?php
/** Template Name: Category = Links
 * The main template file.
 * cat = 6 = links
 *
 * @package WordPress
 * @subpackage Alaska Flag Theme
 */
?>
<?php get_header();?>
<?php get_header('banner');?>
<div id="content" role="main" class="cat_links">
<div id="posts">
<?php query_posts('cat=6&showposts=20'); ?>
<?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
  <div class="post-style">
   <?php $link_post = get_post_meta($post->ID, 'link_post_url', false);?>
    <a class="page-link" href="<?php echo $link_post[0] ?>" target="_blank" >
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                    <?php the_post_thumbnail('thumbnail'); ?>
                    </div> 
                    <?php endif; ?>
                <div class="post-content"><?php the_title(); ?><?php the_content(); ?></div>
  </a></div>

<?php endwhile; ?>
<?php endif; ?>
</div>
</div></div>
<?php get_footer(); ?>
</div></html>