<?php
/**
 * Enqueue scripts and styles for the theme.
 * This file is loaded via the 'after_setup_theme' hook at priority '10'
 *
 * @package hoot
 * @subpackage dispatch
 * @since dispatch 1.0
 */

/* Add custom scripts. Set priority to 11 so that the theme's main js is loaded after default scripts which are loaded at priority 10 */
add_action( 'wp_enqueue_scripts', 'hoot_base_enqueue_scripts', 11 );

/* Add custom styles. Set priority to default 10 so that theme's main style is loaded after these styles (at priority 11), and can thus easily override any style without over-qualification.  */
add_action( 'wp_enqueue_scripts', 'hoot_base_enqueue_styles', 10 );

/* Dequeue font awesome */
add_action( 'wp_enqueue_scripts', 'hoot_base_dequeue_fontawesome', 99 );

/**
 * Load scripts for the front end.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function hoot_base_enqueue_scripts() {

	/* Load jquery */
	wp_enqueue_script( 'jquery' );

	/* Load modernizr */
	$script_uri = hoot_locate_script( 'js/modernizr.custom' );
	wp_enqueue_script( 'modernizr', $script_uri, array(), '2.8.3' );

	/* Load Superfish and WP's hoverIntent */
	// WordPress prior to v3.6 uses an older version of HoverIntent which doesn't support event delegation :( 
	wp_enqueue_script( 'hoverIntent' );
	$script_uri = hoot_locate_script( 'js/jquery.superfish' );
	wp_enqueue_script( 'superfish', $script_uri, array( 'jquery', 'hoverIntent'), '1.7.5', true );

	/* Load lightSlider if 'light-slider' is active. */
	if ( current_theme_supports( 'light-slider' ) ) {
		$script_uri = hoot_locate_script( 'js/jquery.lightSlider' );
		wp_enqueue_script( 'lightSlider', $script_uri, array( 'jquery' ), '1.1.1', true );
	}

	/* Load fitvids */
	$script_uri = hoot_locate_script( 'js/jquery.fitvids' );
	wp_enqueue_script( 'fitvids', $script_uri, array(), '1.1', true );

	/* Load Theme Javascript */
	$script_uri = hoot_locate_script( 'js/hoot.theme' );
	wp_enqueue_script( 'hoot-theme', $script_uri, array(), THEME_VERSION, true );

	/* Localize Theme Javascript - Must be called after the script has been registered. */
	hoot_localize_theme_script();

}

/**
 * Pass data to Theme Javascript
 *
 * @since 1.0
 * @access public
 * @return void
 */
function hoot_localize_theme_script() {
	$hootdata = array();
	$hootdata = apply_filters( 'hoot_localize_theme_script', $hootdata );
	if ( !empty( $hootdata ) )
		wp_localize_script( 'hoot-theme', 'hootData', $hootdata );
}

/**
 * Load stylesheets for the front end.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function hoot_base_enqueue_styles() {

	/* Load Google Fonts if 'google-fonts' is active. */
	if ( current_theme_supports( 'google-fonts' ) ) {
		wp_enqueue_style( 'hoot-google-fonts', hoot_google_fonts_enqueue_url(), array(), null );
	}

	/* Load lightSlider style if 'light-slider' is active. */
	if ( current_theme_supports( 'light-slider' ) ) {
		$style_uri = hoot_locate_style( 'css/lightSlider' );
		wp_enqueue_style( 'lightSlider', $style_uri, false, '1.1.0' );
	}

	/* Load gallery style if 'cleaner-gallery' is active. */
	if ( current_theme_supports( 'cleaner-gallery' ) ) {
		$style_uri = hoot_locate_style( trailingslashit( HOOT_CSS ) . 'gallery' );
		wp_enqueue_style( 'gallery', $style_uri );
	}

	/* Load gallery styles if Jetpack 'tiled-gallery' module is active */
	if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'tiled-gallery' ) ) {
		$style_uri = hoot_locate_style( trailingslashit( HOOT_CSS ) . 'gallery' );
		wp_enqueue_style( 'gallery', $style_uri );
		$style_uri = hoot_locate_style( 'css/jetpack' );
		wp_enqueue_style( 'hoot-jetpack', $style_uri );
	}

	/* Load font awesome if 'font-awesome' is active. */
	if ( current_theme_supports( 'font-awesome' ) ) {
		$style_uri = hoot_locate_style( trailingslashit( HOOT_CSS ) . 'font-awesome' );
		wp_enqueue_style( 'font-awesome', $style_uri, false, '4.5.0' );
	}

}

/**
 * Dequeue font awesome from frontend if a similar handle exists (registered by another plugin)
 * but it is already enqueued using the theme
 *
 * @since 1.0
 * @access public
 * @return void
 */
function hoot_base_dequeue_fontawesome() {
	if ( current_theme_supports( 'font-awesome' ) && wp_style_is( 'fontawesome' ) )
		wp_dequeue_style( 'fontawesome' );
}