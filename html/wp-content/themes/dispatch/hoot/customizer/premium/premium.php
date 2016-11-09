<?php
/**
 * Functions and hooks to add premium section to customizer
 *
 * @package hoot
 * @subpackage hoot-customizer
 * @since hoot 2.0.0
 */

/** Determine whether to load premium fly **/
$premium_features_file = trailingslashit( HOOT_THEMEDIR ) . 'admin/premium.php';
$hoot_customizer_load_premiumfly = apply_filters( 'hoot_customizer_load_premiumfly', file_exists( $premium_features_file ) );
if ( !$hoot_customizer_load_premiumfly )
	return;

/**
 * Add Content to Customizer Panel Footer
 *
 * @since 2.0.0
 * @return void
 */
function hoot_customizer_premium() {

	/** Load Premium Features Data **/
	include_once( trailingslashit( HOOT_THEMEDIR ) . 'admin/premium.php' );

	/** Get Premium URL **/
	$hoot_customizer = Hoot_Customizer::get_instance();
	$infobuttons = $hoot_customizer->get_infobuttons();
	$premium_url = 'http://wphoot.com/';
	foreach ( $infobuttons as $key => $button ) {
		if ( isset( $button['type'] ) && $button['type']=='premium' ) {
			$premium_url = esc_url( $button['url'] );
		}
	}
	$demo_url = ( empty( $demo_url ) ) ? $premium_url : $demo_url;

	?>
	<div id="hoot-flypremium" class="hoot-flypanel">
		<div class="hoot-flypanel-header hoot-flypanel-nav">
			<div class="primary-actions">
				<span class="hoot-flypanel-back" tabindex="-1"><span class="screen-reader-text"><?php _e( 'Back', 'dispatch' ) ?></span></span>
				<a class="button button-primary" href="<?php echo $premium_url; ?>" target="_blank"><i class="fa fa-star"></i> <?php _e( 'Buy Now', 'dispatch' ) ?></a>
			</div>
		</div>
		<div id="hoot-flypremium-content" class="hoot-flypanel-content">
			<?php
			if ( !empty( $hoot_options_premium ) && is_array( $hoot_options_premium ) ):
				foreach ( $hoot_options_premium as $key => $feature ) :
					?>
					<div class="section-premium-info">
					<?php if ( !empty( $feature['desc'] ) ) : ?>
						<div class="premium-info">
							<div class="premium-info-text">
								<?php if ( !empty( $feature['name'] ) ) : ?>
									<h4 class="heading"><?php echo $feature['name']; ?></h4>
								<?php endif; ?>
								<?php echo $feature['desc']; ?>
							</div>
							<?php if ( !empty( $feature['img'] ) ) : ?>
								<div class="premium-info-img">
									<img src="<?php echo esc_url( $feature['img'] ); ?>" />
								</div>
							<?php endif; ?>
							<div class="clear"></div>
						</div>
					<?php elseif ( !empty( $feature['name'] ) ) : ?>
						<h4 class="heading"><?php echo $feature['name']; ?></h4>
					<?php endif; ?>
					<?php if ( !empty( $feature['std'] ) ) echo $feature['std']; ?>
					</div>
				<?php
				endforeach;
			endif;
			?>
		</div>
		<div class="hoot-flypanel-footer hoot-flypanel-nav">
			<div class="primary-actions">
				<a class="button button-primary" href="<?php echo $demo_url; ?>" target="_blank"><i class="fa fa-eye"></i> <?php _e( 'Theme Demo', 'dispatch' ) ?></a>
			</div>
		</div>
	</div><!-- .hoot-flypanel -->
	<?php

}
add_action( 'customize_controls_print_footer_scripts', 'hoot_customizer_premium' );

/**
 * Add Content to JS object passed to hoot-customizer-script
 *
 * @since 2.0.0
 * @param array $data
 * @return array
 */
function hoot_customizer_premium_fly_js_object( $data ) {
	$data['premium-section'] =
		'<div id="accordion-panel-premium" class="hoot-premium-section control-section control-panel">' .
			'<h3 id="hoot-premium-section-title" class="accordion-section-title hoot-flypanel-button" data-flypaneltype="premium" tabindex="0">' .
				'<i class="fa fa-star"></i> ' .
				__( 'Premium Options', 'dispatch' ) .
				'<span class="screen-reader-text">' . __( 'Press return or enter to expand', 'dispatch' ) . '</span>' .
			'</h3>' .
		'</div>';
	return $data;
}
add_filter( 'hoot_customizer_control_footer_js_data_object', 'hoot_customizer_premium_fly_js_object' );

/**
 * Enqueue scripts/styles for premium section to customizer screen
 *
 * @since 2.0.0
 * @return void
 */
function hoot_customizer_premium_enqueue_scripts() {
	wp_enqueue_style( 'hoot-customizer-premium', trailingslashit( HOOTCUSTOMIZER_URI ) . "premium/style.css", array(),  HOOT_VERSION );
	wp_enqueue_script( 'hoot-customizer-premium', trailingslashit( HOOTCUSTOMIZER_URI ) . "premium/script.js", array( 'jquery', 'customize-controls' ), HOOT_VERSION, true );
}
add_action( 'customize_controls_enqueue_scripts', 'hoot_customizer_premium_enqueue_scripts' );

/**
 * Add Icon for Premium Panels to Icon Nav
 *
 * @since 2.0.0
 * @param array $panels
 * @return array
 */
function hoot_customizer_premium_panel_icon( $panels ) {
	$panels['premium'] = array(
		'title' => __( 'Premium Options', 'dispatch' ),
		'icon' => 'fa fa-star',
	);
	return $panels;
}
add_filter( 'hoot_customizer_panel_icons', 'hoot_customizer_premium_panel_icon' );