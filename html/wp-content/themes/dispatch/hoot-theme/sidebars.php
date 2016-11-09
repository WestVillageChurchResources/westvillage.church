<?php
/**
 * Register sidebar widget areas for the theme
 * This file is loaded via the 'after_setup_theme' hook at priority '10'
 *
 * Dynamic widget areas (like template areas, footers) are handled by the framework. To override them,
 * remove actions 'hoot_footer_register_sidebars' and 'hoot_widgetized_template_register_sidebars' from
 * 'widgets_init' hook, and add custom sidebars here using 'hoot_register_sidebar'.
 *
 * @package hoot
 * @subpackage dispatch
 * @since dispatch 1.0
 */

/* Register sidebars. */
add_action( 'widgets_init', 'hoot_base_register_sidebars', 5 );

/**
 * Registers sidebars.
 *
 * @since 1.0
 * @access public
 * @return void
 */
function hoot_base_register_sidebars() {

	hoot_register_sidebar(
		array(
			'id'          => 'primary-sidebar',
			'name'        => _x( 'Primary Sidebar', 'sidebar', 'dispatch' ),
			'description' => __( 'The main sidebar throughout the site.', 'dispatch' )
		)
	);

	hoot_register_sidebar(
		array(
			'id'          => 'topbar-left',
			'name'        => _x( 'Topbar Left', 'sidebar', 'dispatch' ),
			'description' => __( 'Leave empty if you dont want to show topbar.', 'dispatch' )
		)
	);

	hoot_register_sidebar(
		array(
			'id'          => 'topbar-right',
			'name'        => _x( 'Topbar Right', 'sidebar', 'dispatch' ),
			'description' => __( 'Leave empty if you dont want to show topbar.', 'dispatch' )
		)
	);

	hoot_register_sidebar(
		array(
			'id'          => 'sub-footer',
			'name'        => _x( 'Sub Footer', 'sidebar', 'dispatch' ),
			'description' => __( 'Leave empty if you dont want to show subfooter.', 'dispatch' )
		)
	);

}