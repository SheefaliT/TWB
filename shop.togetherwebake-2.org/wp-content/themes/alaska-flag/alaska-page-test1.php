<html><head><?php
/** Template Name: For Bakers
 * The main template file.
 *
 *
 * @package WordPress
 * @subpackage Alaska Flag Theme
 */
?>
	

<html><head>

<?php get_header();?>
<?php get_header('custom');?>
<?php get_header('banner');?>
<div id="content" role="main" class="test">
<div id="main-content">
<?php if (have_posts()) : while (have_posts()) : the_post();?>

<?php the_content(); ?>
<?php endwhile; endif; ?>
<br /></div>
</div>
<?php get_footer(); ?>
        </div></html>