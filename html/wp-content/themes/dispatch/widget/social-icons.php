<?php
// Return if no icons to show
if ( empty( $icons ) || !is_array( $icons ) )
	return;
?>

<div class="social-icons-widget <?php echo 'social-icons-' . esc_attr( $size ); ?>"><?php
	foreach( $icons as $key => $icon ) :
		if ( !empty( $icon['url'] ) && !empty( $icon['icon'] ) ) :

			$icon_class = sanitize_html_class( $icon['icon'] ) . '-block';

			if ( $icon['icon'] == 'fa-envelope' ) {
				$url = str_replace( array( 'http://', 'https://'), '', esc_url( $icon['url'] ) );
				$url = 'mailto:' . $url;
				$context = 'email';

			} else {
				$url = esc_url( $icon['url'] );
				$context = 'link';
			}

			?><a href="<?php echo $url; ?>" <?php hoot_attr( 'social-icons-icon', $context, $icon_class ); ?>>
				<i class="fa <?php echo sanitize_html_class( $icon['icon'] ); ?>"></i>
			</a><?php

		endif;
	endforeach; ?>
</div>