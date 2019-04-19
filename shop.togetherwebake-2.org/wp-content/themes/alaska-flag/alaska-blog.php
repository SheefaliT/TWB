<html><head><?php
/** Template Name: Category = Retail
 * The main template file.
 * cat = 28 = retail locations
 *
 * @package WordPress
 * @subpackage Alaska Flag Theme
 */
?>
<?php get_header();?>
<?php get_header('banner');?>
<div id="content" role="main" class="catretail">
<div id="posts"><?php query_posts('cat=28&showposts=10'); ?>
<?php if ( have_posts() ) : while(have_posts()) : the_post(); ?>
    <div class="post-style">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                    <?php the_post_thumbnail('thumbnail'); ?>
                    </div> 
                    <?php endif; ?>

                <div class="post-content"><?php the_excerpt(); ?></div>
    </div>

<?php endwhile; endif; ?>
<br /></div></div>

<?php get_footer(); ?>
</div></html>