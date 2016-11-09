<?php
// Get Left Content
$topbar_left = is_active_sidebar( 'topbar-left' );
$topbar_right = is_active_sidebar( 'topbar-right' );

// Get Right Content
// $topbar_right = '';
// $topbar_icons = hoot_get_mod( 'topbar_icons' );
// if ( is_array( $topbar_icons ) ) {
// 	foreach ( $topbar_icons as $profile ) {
// 		if ( !empty( $profile['icon'] ) && !empty( $profile['url'] ) ) {
// 			$topbar_right .= '<a class="social-icons-icon ' . $profile['icon'] . '-block" href="' . esc_url( $profile['url'] ) . '"><i class="fa ' . $profile['icon'] . '"></i></a>';
// 		}
// 	}
// }

// Display Topbar
if ( !empty( $topbar_left ) || !empty( $topbar_right ) ) :

	?>
	<div id="topbar" class="grid-stretch">
		<div class="grid">
			<div class="grid-span-12">

				<div class="table">
					<?php if ( $topbar_left ): ?>
						<div id="topbar-left" class="table-cell-mid">
							<?php dynamic_sidebar( 'topbar-left' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $topbar_right ): ?>
						<div id="topbar-right" class="table-cell-mid">
							<div id="topbar-right-inner">
								<?php dynamic_sidebar( 'topbar-right' ); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>
	<?php

endif;

// Template modification Hook
do_action( 'hoot_template_after_topbar', $topbar_left, $topbar_right );