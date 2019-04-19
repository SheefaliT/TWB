<html><head><?php
/** Template Name: WOSHOP
 * The main template file.
 * 
 * check https://codex.wordpress.org/Class_Reference/WP_Query
 * @package WordPress
 * @subpackage Alaska Flag Theme     
 */
?>
<html><head>
<?php get_header();?>
<?php get_header('banner');?>

<div id="content" role="main" class="WOSHOP">
<?php woocommerce_content(); ?>

</div>
    <?php get_footer(); ?>
        </div></html>