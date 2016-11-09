<?php
/**
 * If viewing a multi post page 
 */
if ( !is_front_page() && !is_singular() && !hoot_is_404() ) :

	// Let child themes add content if they want to
	hoot_display_loop_title_content( 'pre' );
	?>

	<div id="loop-meta">
		<div class="grid">

			<div <?php hoot_attr( 'loop-meta', '', 'grid-span-12' ); ?>>

				<h1 <?php hoot_attr( 'loop-title' ); ?>><?php hoot_loop_title(); // Displays title for archive type (multi post) pages. ?></h1>

				<?php if ( $desc = hoot_get_loop_description() ) : ?>
					<div <?php hoot_attr( 'loop-description' ); ?>>
						<?php echo $desc; // Displays description for archive type (multi post) pages. ?>
					</div><!-- .loop-description -->
				<?php endif; // End paged check. ?>

			</div><!-- .loop-meta -->

		</div>
	</div>

	<?php
	// Let child themes add content if they want to
	hoot_display_loop_title_content( 'post' );

/**
 * If viewing a single post/page, and the page is not set as frontpage
 */
elseif ( !is_front_page() && is_singular() && !hoot_is_404() ) :

	if ( have_posts() ) :

		// Begins the loop through found posts, and load the post data.
		while ( have_posts() ) : the_post();

			$display_title =              apply_filters( 'loop_meta_display_title',             '' );
			$pre_title_content =          apply_filters( 'loop_meta_pre_title_content',         '' );
			$pre_title_content_stretch =  apply_filters( 'loop_meta_pre_title_content_stretch', '' );
			$pre_title_content_post =     apply_filters( 'loop_meta_pre_title_content_post',    '' );

			hoot_display_loop_title_content( 'pre', array(
				$pre_title_content, $pre_title_content_stretch, $pre_title_content_post
				) );

			if ( $display_title !== 'hide' ) :
			?>

				<div id="loop-meta">
					<div class="grid">

						<div <?php hoot_attr( 'loop-meta', '', 'grid-span-12' ); ?>>
							<div class="entry-header">

								<?php
								global $post;
								$pretitle = ( !isset( $post->post_parent ) || empty( $post->post_parent ) ) ? '' : get_the_title( $post->post_parent ) . ' &raquo; ';
								?>
								<h1 <?php hoot_attr( 'loop-title' ); ?>><?php the_title( $pretitle ); ?></h1>

								<?php $hide_meta_info = apply_filters( 'hoot_hide_meta_info', false, 'top' ); ?>
								<?php if ( !$hide_meta_info && 'top' == hoot_get_mod( 'post_meta_location' ) && !is_attachment() ) : ?>
									<div <?php hoot_attr( 'loop-description' ); ?>>
										<?php
										if ( is_page() )
											hoot_meta_info_blocks( hoot_get_mod('page_meta'), 'loop-meta' );
										else
											hoot_meta_info_blocks( hoot_get_mod('post_meta'), 'loop-meta' );
										?>
									</div><!-- .loop-description -->
								<?php endif; ?>

							</div><!-- .entry-header -->
						</div><!-- .loop-meta -->

					</div>
				</div>

			<?php
			endif;

			hoot_display_loop_title_content( 'post', array(
				$pre_title_content, $pre_title_content_stretch, $pre_title_content_post
				) );

		endwhile;
		rewind_posts();

	endif;

endif;