<?php
/**
 * Functions for handling JavaScript in the framework (frontend).  Themes can add support for the 
 * 'hoot-core-scripts' feature to allow the framework to handle loading the stylesheets into 
 * the theme header or footer at an appropriate time.
 *
 * @package hoot
 * @subpackage framework
 * @since hoot 1.0.0
 */

/* Register Core scripts. */
add_action( 'wp_enqueue_scripts', 'hoot_register_scripts', 0 );

/* Load Core scripts. */
add_action( 'wp_enqueue_scripts', 'hoot_enqueue_scripts', 5 );

/**
 * Registers JavaScript files for the framework. This function merely registers scripts with WordPress
 * using the wp_register_script() function. It does not load any script files on the site. If a theme
 * wants to register its own custom scripts, it should do so on the 'wp_enqueue_scripts' hook.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_register_scripts() {

	/* Supported JavaScript. */
	$supports = get_theme_support( 'hoot-core-scripts' );

	/* Register the 'mobile-toggle' script if the current theme supports 'mobile-toggle'. */
	if ( isset( $supports[0] ) && in_array( 'mobile-toggle', $supports[0] ) ) {
		$script_uri = hoot_locate_script( trailingslashit( HOOT_JS ) . 'mobile-toggle' );
		wp_register_script( 'hoot-mobile-toggle', $script_uri, array( 'jquery' ), '20130528', true );
	}

}

/**
 * Tells WordPress to load the scripts needed for the framework using the wp_enqueue_script() function.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_enqueue_scripts() {

	/* Supported JavaScript. */
	$supports = get_theme_support( 'hoot-core-scripts' );

	/* Load the comment reply script on singular posts with open comments if threaded comments are supported. */
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );

	/* Load the 'mobile-toggle' script if the current theme supports 'mobile-toggle'. */
	if ( isset( $supports[0] ) && in_array( 'mobile-toggle', $supports[0] ) )
		wp_enqueue_script( 'hoot-mobile-toggle' );

}