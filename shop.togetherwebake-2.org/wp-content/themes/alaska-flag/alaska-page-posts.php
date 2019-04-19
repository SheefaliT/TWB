<html><head><?php
/** Template Name: Page with Post
 * The main template file.
 *
 *
 * @package WordPress
 * @subpackage Alaska Flag Theme
 */
?>
<?php get_header();?>
<?php get_header('banner');?>
<div id="content" role="main" class="pagewpost">
<?php get_sidebar(); ?>
<div id="main-content">
<?php while ( have_posts() ) :  the_post();
   the_content();
endwhile; ?>

<div id="post-withcontent">
    <?php 
    $recentBlog = new WP_Query();
    $recentBlog->query('showposts=1');
    while($recentBlog->have_posts('1')) : $recentBlog->the_post();
    ?>
    <div id="posts" class="post-style"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="post-thumbnail">
                    <?php the_post_thumbnail('thumbnail'); ?>
                    </div></a>
                    <?php endif; ?>
    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><div class="post-header"><?php the_title(); ?></div></a>
    <div class="post-content"><?php the_content_feed('Read More&hellip;', false, '', 35); ?>Read More&hellip;</div>
    </a><?php endwhile; ?>
    </div>
</div><br />
</div>
<?php get_footer(); ?>
</div></html>