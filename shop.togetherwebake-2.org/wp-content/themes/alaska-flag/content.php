<?php
/**
 * The default template for displaying content
 *
 * @package WordPress
 * @subpackage Alaska Flag
 */
?>

	<article id="post"><div id="posts">
		<div class="post-header">
			<?php if ( is_sticky() ) : ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>
                        <?php else : ?>
                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>
                           <?php endif; ?>
                        <br /><span><?php the_date(); ?></span><br />
                </div>
            	<div class="post-content">
                        <?php the_content('Read more...'); ?>
                        <?php if ( comments_open() ) :
                        comments_popup_link( 'Make a Comment', '1 comment', '% comments', 'comments-link', 'Comments are off');
                        endif;?>
                </div>
            </div></article>
