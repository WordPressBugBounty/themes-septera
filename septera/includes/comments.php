<?php
/**
 * Comments related functions
 *
 * @package septera
 */

/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own septera_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
if ( ! function_exists( 'septera_comment' ) ) :
function septera_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback'  :
		case 'trackback' :
		?>
			<li class="post pingback">
			<p><?php _e( 'Pingback: ', 'septera' ); ?><?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'septera' ), ' ' ); ?></p>
		<?php
		break;
		case '' :
		default :
		?>
			<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>"<?php cryout_schema_microdata( 'comment' ); ?>>

				<article>
					<footer class="comment-header vcard">

						<div class="comment-author" <?php cryout_schema_microdata( 'comment-author' ); ?>>
							<?php echo get_avatar( $comment, 50, '', '', array( 'extra_attr' => cryout_schema_microdata('image', 0) )  ); ?>
							<?php printf(  '%s ', sprintf( '<span class="author-name fn"' . cryout_schema_microdata( 'author-name', 0) . '>%s</span>', get_comment_author_link() ) ); ?>
						</div> <!-- .comment-author -->

						<div class="comment-meta">
							<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
							<time datetime="<?php comment_time( 'c' );?>" <?php cryout_schema_microdata( 'time' );?>>

								<span class="comment-date">
									<?php /* translators: 1: date, 2: time */
									printf(  '%1$s ' . __( 'at', 'septera' ) . ' %2$s', get_comment_date(),  get_comment_time() ); ?>
								</span>
								<span class="comment-timediff">
									<?php printf( _x( '%1$s ago', '%s = human-readable time difference', 'septera' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
								</span>

							</time>
							</a>
							<?php edit_comment_link( __( '(Edit)', 'septera' ), ' ' ); ?>
						</div><!-- .comment-meta -->

						<div class="reply">
							<?php comment_reply_link( array_merge( $args, array(
									'reply_text' 	=> __( 'Reply', 'septera' ),
									'depth'			=> $depth,
									'max_depth'		=> $args['max_depth'] ) ) );
							?>
						</div><!-- .reply -->

					</footer><!-- .comment-header .vcard -->

					<?php if ( $comment->comment_approved == '0' ) : ?>
						<span class="comment-await"><em><?php _e( 'Your comment is awaiting moderation.', 'septera' ); ?></em></span>
						<br />
					<?php endif; ?>

					<div class="comment-body" <?php cryout_schema_microdata( 'text' ); ?>>
						<?php comment_text(); ?>
					</div>

					<footer>
					</footer>
				</article>
		<?php
		break;
	endswitch;

	// </li><!-- #comment-##  -->  closed by wp_comments_list()
} // septera_comment()
endif;

/** Number of comments on loop post if comments are enabled. */
if ( ! function_exists( 'septera_comments_on' ) ) :
function septera_comments_on() {
	$septera_meta_comment = cryout_get_option( 'septera_meta_comment' );
    // Only show comments if they're open, or are closed but with comments already posted, if the theme's meta comments are enabled and if it's not a single post
    if ( ( comments_open() || get_comments_number() ) && ! post_password_required() && $septera_meta_comment && ! is_single() ) :
			echo '<span class="comments-link">';
			comments_popup_link(
				'<i class="icon-comments icon-metas" title="' . esc_attr__( 'Leave a comment', 'septera' ) . '"></i><strong>0</strong>',
				'<i class="icon-comments icon-metas" title="' .
                    sprintf( _n( 'One Comment', '%1$s Comments', get_comments_number(), 'septera' ), number_format_i18n( get_comments_number() ) ) .
                 '"></i><strong>1</strong>',
				'<i class="icon-comments icon-metas" title="' .
                    sprintf( _n( 'One Comment', '%1$s Comments', get_comments_number(), 'septera' ), number_format_i18n( get_comments_number() ) ) .
                '"></i><strong>%</strong>',
				'',
				''
			);
			echo '</span>';
		endif;
} // septera_comments_on()
endif;

/** Adds microdata tags to comment link */
if ( ! function_exists( 'septera_comments_microdata' ) ) :
function septera_comments_microdata() {

	cryout_schema_microdata('comment-meta', 0); // no echo

} // septera_comments_microdata()
endif;
add_filter( 'comments_popup_link_attributes', 'septera_comments_microdata' );


/* Edit comments form inputs: removed labels and replaced them with placeholders */
function septera_comments_form( $arg ) {
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );

	$arg =  array(

		'author' =>	'<p class="comment-form-author"><label for="author">' . __( 'Name', 'septera' ) .  ( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
					'<input id="author" placeholder="'. esc_attr__( 'Name', 'septera' ) .'*" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
					'" size="30" maxlength="245"' . $aria_req . ' /></p>',

		'email' =>	'<p class="comment-form-email"><label for="email">' . __( 'Email', 'septera' ) . ( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
					'<input id="email" placeholder="'. esc_attr__( 'Email', 'septera' ) . '*" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) .
					'" size="30"  maxlength="100" aria-describedby="email-notes"' . $aria_req . ' /></p>',

		'url' =>	'<p class="comment-form-url"><label for="url">' . __( 'Website', 'septera' ) . '</label>' .
					'<input id="url" placeholder="'. esc_attr__( 'Website', 'septera' ) .'" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) .
					'" size="30"  maxlength="200" /></p>',
		'cookies' => '<p class="comment-form-cookies-consent"><label for="wp-comment-cookies-consent">' .
					  '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" />' .
					   __( 'Save my name, email, and site URL in my browser for next time I post a comment.', 'septera' ) . '</label></p>',

	);

	return $arg;
} // septera_comments_form()

/* Edit comments form textarea: removed label and replaced it with a placeholder */
function septera_comments_form_textarea( $arg ) {
	$arg = '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun', 'septera' ) .
			'</label><textarea placeholder="'. esc_attr_x( 'Comment', 'noun', 'septera' ) .'" id="comment" name="comment" cols="45" rows="8" aria-required="true">' .
			'</textarea></p>';

	return $arg;
} // septera_comments_form_textarea()

/* Hooks are located in cryout_master_hook() in core.php */

/* FIN */
