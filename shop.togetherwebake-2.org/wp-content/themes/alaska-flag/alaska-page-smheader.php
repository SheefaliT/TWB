<html><head><?php
/** Template Name: Small Header Page Style
 * The main template file.
 *
 *
 * @package WordPress
 * @subpackage Alaska Flag Theme
 */
?>
<?php get_header();?>
<?php if ( has_post_thumbnail() ) {$feat_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ), "full" );}?>
<?php get_header('smbanner');?>
<div id="content" role="main" class="smheader">
<div id="main-content"><br />
<?php if (have_posts()) : while (have_posts()) : the_post();?>
<?php the_content(); ?>
<?php endwhile; endif; ?>
<br /></div></div>
<?php get_footer(); ?>
</div></html>