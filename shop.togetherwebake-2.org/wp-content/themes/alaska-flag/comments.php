	<div id="comments">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php ( 'This post is password protected. Enter Password.'); ?></p>
	</div>
	<?php return; endif; ?>

	<?php if ( have_comments() ) : ?>
                <ul class='commentlist'>
			<?php wp_list_comments(array('avatar_size'=>80)); ?>
		</ul>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<div id="moreposts">
			<div class="nav-previous"><?php previous_comments_link ( '&larr; Older Comments' ); ?></div>
			<div class="nav-next"><?php next_comments_link ( 'Newer Comments &rarr;' ); ?></div>
		</div>
		<?php endif; ?>

	<?php elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="nocomments"><?php ( 'Comments are closed.' ); ?></p>
	<?php endif; ?>

<?php $comments_args = array(
        // change the title of send button 
        'title_reply'=>'Comment on this Post:',
        // remove "Text or HTML to be displayed after the set of comment fields"
        'comment_notes_after' => 'Comment Field is a required field.',
        'comment_notes_before' => 'Required Field: * ',);

comment_form($comments_args); ?>
	<?php if(get_option('comment_moderation') == '1'){ ?>
		<p>Comments are held for moderation, do not resubmit.</p>
	<?php } ?>
</div><!-- #comments -->
