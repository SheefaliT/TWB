<html><head><?php
/** Template Name: Background Image Style
 * The main template file.
 *
 *
 * @package WordPress
 * @subpackage Alaska Flag Theme
 */
?>
<?php get_header();?>
<?php if ( has_post_thumbnail() ) {$feat_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "full", true );}?>
<?php get_header('banner');?>
<div id="content" role="main" class="backimage">
<div id="main-content" style="background-image: url(<?php echo $feat_image[0]; ?>);min-height:<?php echo (($feat_image[2]/$feat_image[1])*96) ?>vw" >
<?php if (have_posts()) : while (have_posts()) : the_post();?>
<?php the_content(); ?>
<?php endwhile; endif; ?>
<br /></div></div>
<?php get_footer(); ?>
</div></html>