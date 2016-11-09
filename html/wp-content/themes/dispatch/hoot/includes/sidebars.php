<?php
/**
 * Helper functions for working with the WordPress sidebar system.  Currently, the framework creates a 
 * simple function for registering HTML5-ready sidebars instead of the default WordPress unordered lists.
 *
 * @package hoot
 * @subpackage framework
 * @since hoot 1.0.0
 */

/**
 * Wrapper function for WordPress' register_sidebar() function.  This function exists so that theme authors 
 * can more quickly register sidebars with an HTML5 structure instead of having to write the same code 
 * over and over.  Theme authors are also expected to pass in the ID, name, and description of the sidebar. 
 * This function can handle the rest at that point.
 *
 * @since 1.0.0
 * @access public
 * @param array   $args
 * @return string  Sidebar ID.
 */
function hoot_register_sidebar( $args ) {

	/* Set up some default sidebar arguments. */
	$defaults = array(
		'id'            => '',
		'name'          => '',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	);

	/* Allow developers to filter the default sidebar arguments. */
	$defaults = apply_filters( 'hoot_sidebar_defaults', $defaults );

	/* Parse the arguments. */
	$args = wp_parse_args( $args, $defaults );

	/* Allow developers to filter the sidebar arguments. */
	$args = apply_filters( 'hoot_sidebar_args', $args );

	/* Remove action. */
	remove_action( 'widgets_init', '__return_false', 95 );

	/* Register the sidebar. */
	return register_sidebar( $args );
}

/* Compatibility for when a theme doesn't register any sidebars. */
add_action( 'widgets_init', '__return_false', 95 );

/**
 * Registers footer widget areas.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_footer_register_sidebars() {

	$columns = hoot_get_footer_columns();

	if( $columns ) :
		$alphas = range('a', 'z');
		for ( $i=0; $i < $columns; $i++ ) :
			if ( isset( $alphas[ $i ] ) ) :
				hoot_register_sidebar(
					array(
						'id'          => 'footer-' . $alphas[ $i ],
						'name'        => sprintf( _x( 'Footer %s', 'sidebar', 'dispatch' ), strtoupper( $alphas[ $i ] ) ),
						'description' => __( 'You can set footer columns in Theme Options page.', 'dispatch' )
					)
				);
			endif;
		endfor;
	endif;

}

/* Hook into action */
add_action( 'widgets_init', 'hoot_footer_register_sidebars' );

/**
 * Get footer column option.
 *
 * @since 2.0.0
 * @access public
 * @return int
 */
function hoot_get_footer_columns() {
	$footers = hoot_get_mod( 'footer' );
	$columns = ( $footers ) ? intval( substr( $footers, 0, 1 ) ) : false;
	$columns = ( is_numeric( $columns ) && 0 < $columns ) ? $columns : false;
	return $columns;
}

/**
 * Get footer column option.
 *
 * @since 1.0.0
 * @access public
 * @deprecated 2.0.0
 * @deprecated Use hoot_get_footer_columns()
 * @return int
 */
function hoot_get_option_footer() {

	_deprecated_function( __FUNCTION__, '2.0.0', 'hoot_get_footer_columns()' );

	return hoot_get_footer_columns();
}

/**
 * Registers widgetized template widget areas.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_widgetized_template_register_sidebars() {

	if ( current_theme_supports( 'hoot-widgetized-template' ) ) :
		$areas = array();

		/* Set up defaults */
		$defaults = apply_filters( 'hoot_widgetized_template_widget_areas', array( 'a', 'b', 'c', 'd', 'e' ) );
		$locations = array(
			__( 'Left', 'dispatch' ),
			__( 'Center Left', 'dispatch' ),
			__( 'Center', 'dispatch' ),
			__( 'Center Right', 'dispatch' ),
			__( 'Right', 'dispatch' ),
		);

		// Get user settings
		$sections = hoot_sortlist( hoot_get_mod( 'widgetized_template_sections' ) );

		foreach ( $defaults as $key ) {
			$id = "area_{$key}";
			if ( empty( $sections[$id]['sortitem_hide'] ) ) {

				$columns = ( isset( $sections[$id]['columns'] ) ) ? $sections[$id]['columns'] : '';
				$count = count( explode( '-', $columns ) ); // empty $columns still returns array of length 1
				$location = '';

				for ( $c = 1; $c <= $count ; $c++ ) {
					switch ( $count ) {
						case 2: $location = ($c == 1) ? $locations[0] : $locations[4];
								break;
						case 3: $location = ($c == 1) ? $locations[0] : (
									($c == 2) ? $locations[2] : $locations[4]
								);
								break;
						case 4: $location = ($c == 1) ? $locations[0] : (
									($c == 2) ? $locations[1] : (
										($c == 3) ? $locations[3] : $locations[4]
									)
								);
					}
					$areas[ $id . '_' . $c ] = sprintf( __('Widgetized Template - Area %s %s', 'dispatch'), strtoupper( $key ), $location );
				}

			}
		}

		foreach ( $areas as $key => $name ) {
			hoot_register_sidebar(
				array(
					'id'          => 'widgetized-template-' . $key,
					'name'        => $name,
					'description' => __( "You can order Widgetized Template areas in Customizer > 'Templates' panel > 'Widgetized Template' section.", 'dispatch' )
				)
			);
		}

	endif;

}

/* Hook into action */
add_action( 'widgets_init', 'hoot_widgetized_template_register_sidebars' );
