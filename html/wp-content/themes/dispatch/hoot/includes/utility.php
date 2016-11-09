<?php
/**
 * Additional helper functions that the framework or themes may use.  The functions in this file are functions
 * that don't really have a home within any other parts of the framework.
 *
 * @package hoot
 * @subpackage framework
 * @since hoot 1.0.0
 */

/**
 * Helper function to return the theme option value.
 * If no value has been saved, it returns $default.
 * If no $default provided, it checks the default options array.
 * 
 * @since 1.0.0
 * @access public
 * @deprecated 2.0.0
 * @deprecated Use hoot_get_mod()
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function hoot_get_option( $name, $default = NULL ) {

	_deprecated_function( __FUNCTION__, '2.0.0', 'hoot_get_mod()' );

	return hoot_get_mod( $name, $default );
}

/* Add extra support for post types. */
add_action( 'init', 'hoot_add_post_type_support' );

/**
 * This function is for adding extra support for features not default to the core post types.
 * Excerpts are added to the 'page' post type.  Comments and trackbacks are added for the
 * 'attachment' post type.  Technically, these are already used for attachments in core, but 
 * they're not registered.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_add_post_type_support() {

	/* Add support for excerpts to the 'page' post type. */
	add_post_type_support( 'page', array( 'excerpt' ) );

	/* Add thumbnail support for audio and video attachments. */
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );
}

/* Filters the title for untitled posts. */
add_filter( 'the_title', 'hoot_untitled_post' );

/**
 * The WordPress standards requires that a link be provided to the single post page for untitled 
 * posts.  This is a filter on 'the_title' so that an '(Untitled)' title appears in that scenario, allowing 
 * for the normal method to work.
 *
 * @since 1.0.0
 * @access public
 * @param string $title
 * @return string
 */
function hoot_untitled_post( $title ) {

	if ( empty( $title ) && in_the_loop() && !is_admin() ) { // && !is_singular()

		/* Translators: Used as a placeholder for untitled posts on non-singular views. */
		$title = __( '(Untitled)', 'dispatch' );
	}

	return $title;
}

/**
 * Retrieves the file with the highest priority that exists. The function searches both the stylesheet 
 * and template directories. This function is similar to the locate_template() function in WordPress 
 * but returns the file name with the URI path instead of the directory path.
 *
 * @since 1.0.0
 * @deprecated 2.0.0
 * @deprecated Use hoot_locate_uri()
 * 
 * @access public
 * @link http://core.trac.wordpress.org/ticket/18302
 * @param array $file_names The files to search for.
 * @return string
 */

function hoot_locate_theme_file( $file_names ) {

	_deprecated_function( __FUNCTION__, '2.0.0', 'hoot_locate_uri()' );

	$located = '';

	/* Loops through each of the given file names. */
	foreach ( (array) $file_names as $file ) {

		/* If the file exists in the stylesheet (child theme) directory. */
		if ( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$located = trailingslashit( get_stylesheet_directory_uri() ) . $file;
			break;
		}

		/* If the file exists in the template (parent theme) directory. */
		elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$located = trailingslashit( get_template_directory_uri() ) . $file;
			break;
		}
	}

	return $located;
}

/**
 * Trim a string like PHP trim function
 * It additionally trims the <br> tags and breaking and non breaking spaces as well
 *
 * @since 1.0.0
 * @access public
 * @param string $content
 * @return array
 */
function hoot_trim( $content ) {
	$content = trim( $content, " \t\n\r\0\x0B\xC2\xA0" ); // trim non breaking spaces as well
	$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);
	$content = preg_replace('/(?:<br\s*\/?>\s*)+$/', '', $content);
	$content = trim( $content, " \t\n\r\0\x0B\xC2\xA0" ); // trim non breaking spaces as well
	return $content;
}

/**
 * Insert into associative array at a specific location
 *
 * @since 1.1.1
 * @access public
 * @param array $insert
 * @param array $target
 * @param int|string $location 0 based position, or key in $target
 * @param string $order 'before' or 'after'
 * @return array
 */
function hoot_array_insert( $insert, $target, $location, $order = 'before' ) {

	if ( !is_array( $insert ) || !is_array( $target ) )
		return $target;

	if ( is_int( $location ) ) {

		if ( $order == 'after' )
			$location++;
		$target = array_slice( $target, 0, $location, true ) +
					$insert +
					array_slice( $target, $location, count( $target ) - 1, true );
		return $target;

	} elseif ( is_string( $location ) ) {

		$count = ( $order == 'after' ) ? 1 : 0;
		foreach ( $target as $key => $value ) {
			if ( $key === $location ) {
				$target = array_slice( $target, 0, $count, true ) +
							$insert +
							array_slice( $target, $count, count( $target ) - 1, true );
				return $target;
			}
			$count++;
		}
		// $location not found. So lets just return a simple array merge
		return array_merge( $target, $insert );

	}

	// Just for brevity
	return $target;
}

/**
 * Function for grabbing a WP nav menu theme location name.
 *
 * @since 1.0.0
 * @access public
 * @param string $location
 * @return string
 */
function hoot_get_menu_location_name( $location ) {

	$locations = get_registered_nav_menus();

	return $locations[ $location ];
}

/**
 * Function for grabbing a dynamic sidebar name.
 *
 * @since 1.0.0
 * @access public
 * @param string $sidebar_id
 * @return string
 */
function hoot_get_sidebar_name( $sidebar_id ) {
	global $wp_registered_sidebars;

	if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) )
		return $wp_registered_sidebars[ $sidebar_id ]['name'];
}

/**
 * Helper function for getting the script/style `.min` suffix for minified files.
 *
 * @since 1.0.0
 * @deprecated 2.0.0
 * @deprecated Use hoot_locate_uri()
 * @see hoot_locate_script() hoot_locate_style() hoot_locate_uri()
 * 
 * @access public
 * @return string
 */
function hoot_get_min_suffix() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'hoot_locate_uri()' );
	return '';
}

/**
 * Helper function for getting the minified script uri if available.
 *
 * @since 2.0.0
 * @access public
 * @param string $location
 * @return string
 */
function hoot_locate_script( $location ) {
	$location = preg_replace( array(
		'/\.min\.css$/',
		'/\.css$/',
		), '', $location );
	return hoot_locate_uri( $location, 'js' );
}

/**
 * Helper function for getting the minified style uri if available.
 *
 * @since 2.0.0
 * @access public
 * @param string $location
 * @return string
 */
function hoot_locate_style( $location ) {
	$location = preg_replace( array(
		'/\.min\.js$/',
		'/\.js$/',
		), '', $location );
	return hoot_locate_uri( $location, 'css' );
}

/**
 * Helper function for getting the minified script/style uri if available.
 *
 * @since 2.0.0
 * @access public
 * @param string $location absolute or relative path
 * @param string $type
 * @return string
 */
function hoot_locate_uri( $location, $type ) {

	$location = str_replace( array(
		trailingslashit( THEME_URI ),
		trailingslashit( THEME_DIR ),
		), '', $location );

	$pattern = apply_filters( 'hoot_locate_uri_extension_pattern', array( '/\.' . $type . '$/' ) );
	$location = preg_replace( $pattern, '', $location );

	if ( defined( 'HOOT_DEBUG' ) )
		$loadminified = ( HOOT_DEBUG ) ? false : true;
	else
		$loadminified = hoot_get_mod( 'load_minified', 0 );

	/** Prepare Locations **/

	$locations = array();
	if ( is_child_theme() ) {

		if ( $loadminified )
			$locations['child-premium-min'] = array(
				'path' => trailingslashit( CHILD_THEME_DIR ) . 'premium/' . $location . '.min.' . $type,
				'uri'  => trailingslashit( CHILD_THEME_URI ) . 'premium/' . $location . '.min.' . $type,
				);

		$locations['child-premium'] = array(
			'path' => trailingslashit( CHILD_THEME_DIR ) . 'premium/' . $location . '.' . $type,
			'uri'  => trailingslashit( CHILD_THEME_URI ) . 'premium/' . $location . '.' . $type,
			);

		if ( $loadminified )
			$locations['child-default-min'] = array(
				'path' => trailingslashit( CHILD_THEME_DIR ) . $location . '.min.' . $type,
				'uri'  => trailingslashit( CHILD_THEME_URI ) . $location . '.min.' . $type,
				);

		$locations['child-default'] = array(
			'path' => trailingslashit( CHILD_THEME_DIR ) . $location . '.' . $type,
			'uri'  => trailingslashit( CHILD_THEME_URI ) . $location . '.' . $type,
			);

	}

	if ( $loadminified )
		$locations['premium-min'] = array(
			'path' => trailingslashit( THEME_DIR ) . 'premium/' . $location . '.min.' . $type,
			'uri'  => trailingslashit( THEME_URI ) . 'premium/' . $location . '.min.' . $type,
			);

	$locations['premium'] = array(
		'path' => trailingslashit( THEME_DIR ) . 'premium/' . $location . '.' . $type,
		'uri'  => trailingslashit( THEME_URI ) . 'premium/' . $location . '.' . $type,
		);

	if ( $loadminified )
		$locations['default-min'] = array(
			'path' => trailingslashit( THEME_DIR ) . $location . '.min.' . $type,
			'uri'  => trailingslashit( THEME_URI ) . $location . '.min.' . $type,
			);

	$locations['default'] = array(
		'path' => trailingslashit( THEME_DIR ) . $location . '.' . $type,
		'uri'  => trailingslashit( THEME_URI ) . $location . '.' . $type,
		);

	$locations = apply_filters( 'hoot_locate_uri', $locations, $location, $type );

	/** Locate the file **/

	$located = '';
	foreach ( $locations as $locate ) {
		if ( file_exists( $locate['path'] ) ) {
			$located = $locate['uri'];
			break;
		}
	}

	return $located;

}

/**
 * Function for checking if 404 page is being displayed.
 * This is an extension to the WordPress 'is_404()' conditional tag, as it checks if the main query is
 * altered to display a custom page as 404 page by the Hoot framework.
 *
 * @since 1.0.0
 * @access public
 * @return bool
 */
function hoot_is_404() {
	global $hoot;
	if ( isset( $hoot->is_404 ) && is_bool( $hoot->is_404 ) )
		return $hoot->is_404;
	else
		return is_404();
}

/**
 * Check if this is the premium version of theme.
 * Ideally, HOOT_PREMIUM should be set to (bool)true at the start of '/hoot-theme/hoot-theme.php' file.
 *
 * @since 1.1.0
 * @deprecated 2.0.0
 * @access public
 * @return bool
 */
function hoot_is_premium() {

	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	$return = ( defined( 'HOOT_PREMIUM' ) && HOOT_PREMIUM === true );
	return $return;
}

/**
 * A class of helper functions to cache and build options
 * 
 * @since 1.0.0
 */
class Hoot_Options_Helper {

	/**
	 * Utility functions for processing list count
	 *
	 * @since 2.0.0
	 * @return int
	 */
	static function countval( $number ){

		if ( $number===false)
			return HOOT_ADMIN_LIST_ITEM_COUNT;

		$number = intval( $number );
		if ( empty( $number ) || $number < 0 )
			return 0;

		return $number;
	}

	/**
	 * Get pages array
	 *
	 * @since 1.0.0
	 * @param int $number
	 * @param string $post_type for custom post types
	 * @return array
	 */
	static function get_pages( $number, $post_type = 'page' ){
		$number = ( !$number ) ? -1 : $number;
		$pages = array();
		$the_query = new WP_Query( array( 'post_type' => $post_type, 'posts_per_page' => $number, 'orderby' => 'post_title', 'order' => 'ASC', 'post_status' => 'publish' ) );
		if ( $the_query->have_posts() ) :
			while ( $the_query->have_posts() ) : $the_query->the_post();
				$pages[ get_the_ID() ] = get_the_title();
			endwhile;
			wp_reset_postdata();
		endif;
		return $pages;
	}

	/**
	 * Get posts array
	 *
	 * @since 1.0.0
	 * @param int $number
	 * @return array
	 */
	static function get_posts( $number ){
		$number = ( !$number ) ? '' : $number;
		$posts = array();
		$object = get_posts("numberposts=$number");
		foreach ( $object as $post ) {
			$posts[ $post->ID ] = $post->post_title;
		}
		return $posts;
	}

	/**
	 * Get terms array
	 *
	 * @since 2.0.0
	 * @param int $number
	 * @param string $taxonomy
	 * @return array
	 */
	static function get_terms( $number, $taxonomy = 'category' ){
		$number = ( !$number ) ? '' : $number;
		$terms = array();
		$object = (array) get_terms( $taxonomy, array( 'number' => $number ) );
		foreach ( $object as $term )
			$terms[$term->term_id] = $term->name;
		return $terms;
	}

	/**
	 * Pull all the categories into an array
	 *
	 * @since 1.0.0
	 * @param int $number false for default, empty or -1 for all
	 * @return array
	 */
	static function categories( $number = false ){
		$number = self::countval( $number );

		if ( $number == HOOT_ADMIN_LIST_ITEM_COUNT ) {
			static $options_categories_default = array();
			if ( empty( $options_categories_default ) )
				$options_categories_default = self::get_terms( $number, 'category' );
			return $options_categories_default;
		}

		elseif ( empty( $number ) ) {
			static $options_categories = array();
			if ( empty( $options_categories ) )
				$options_categories = self::get_terms( $number, 'category' );
			return $options_categories;
		}

		else
			return self::get_terms( $number, 'category' );

	}

	/**
	 * Pull all the tags into an array
	 *
	 * @since 1.0.0
	 * @param int $number false for default, empty or -1 for all
	 * @return array
	 */
	static function tags( $number = false ){
		$number = self::countval( $number );

		if ( $number == HOOT_ADMIN_LIST_ITEM_COUNT ) {
			static $options_tags_default = array();
			if ( empty( $options_tags_default ) )
				$options_tags_default = self::get_terms( $number, 'post_tag' );
			return $options_tags_default;
		}

		elseif ( empty( $number ) ) {
			static $options_tags = array();
			if ( empty( $options_tags ) )
				$options_tags = self::get_terms( $number, 'post_tag' );
			return $options_tags;
		}

		else
			return self::get_terms( $number, 'post_tag' );

	}

	/**
	 * Pull all the pages into an array
	 *
	 * @since 1.0.0
	 * @param int $number false for default, empty or -1 for all
	 * @return array
	 */
	static function pages( $number = false ){
		$number = self::countval( $number );

		if ( $number == HOOT_ADMIN_LIST_ITEM_COUNT ) {
			static $options_pages_default = array();
			if ( empty( $options_pages_default ) )
				$options_pages_default = self::get_pages( $number, 'page' );
			return $options_pages_default;
		}

		elseif ( empty( $number ) ) {
			static $options_pages = array();
			if ( empty( $options_pages ) )
				$options_pages = self::get_pages( $number, 'page' );
			return $options_pages;
		}

		else
			return self::get_pages( $number, 'page' );

	}

	/**
	 * Pull all the posts into an array
	 *
	 * @since 1.0.0
	 * @param int $number false for default, empty or -1 for all
	 * @return array
	 */
	static function posts( $number = false ){
		$number = self::countval( $number );

		if ( $number == HOOT_ADMIN_LIST_ITEM_COUNT ) {
			static $options_posts_default = array();
			if ( empty( $options_posts_default ) )
				$options_posts_default = self::get_posts( $number );
			return $options_posts_default;
		}

		elseif ( empty( $number ) ) {
			static $options_posts = array();
			if ( empty( $options_posts ) )
				$options_posts = self::get_posts( $number );
			return $options_posts;
		}

		else
			return self::get_posts( $number );

	}

	/**
	 * Pull all the cpt posts into an array
	 *
	 * @since 1.1.1
	 * @param string $post_type for custom post types
	 * @param int $number Set to -1 for all pages
	 * @param string $append Append a value
	 * @return array
	 */
	static function cpt( $post_type = 'page', $number = false, $append = false ){
		$number = self::countval( $number );

		if ( $number == HOOT_ADMIN_LIST_ITEM_COUNT ) {
			static $cpt_default = array();
			if ( empty( $cpt_default[ $post_type ] ) )
				$cpt_default[ $post_type ] = self::get_pages( $number, $post_type );
			$return = $cpt_default[ $post_type ];
		}

		elseif ( empty( $number ) ) {
			static $cpt = array();
			if ( empty( $cpt[ $post_type ] ) )
				$cpt[ $post_type ] = self::get_pages( $number, $post_type );
			$return = $cpt[ $post_type ];
		}

		else
			$return = self::get_pages( $number, $post_type );

		$return = ( $append ) ? array( $append ) + $return : $return;
		return $return;

	}

	/**
	 * Create default typography options array
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.0
	 * @return array
	 */
	static function typography_defaults() {

		_deprecated_function( __FUNCTION__, '2.0.0', '' );

		$fonts = self::fonts( 'websafe' );
		$font = ( is_array( $fonts ) ) ? current( array_keys( $fonts ) ) : 'Arial, Helvetica, sans-serif';
		return apply_filters( 'hoot_of_default_typography', array(
			'size' => '14px',
			'face' => $font,
			'style' => 'normal',
			'color' => '#333333' ) );
	}

	/**
	 * Create font families array
	 *
	 * @since 1.0.0
	 * @param string $return array to return websafe|google-fonts
	 * @return array
	 */
	static function fonts( $return = 'websafe' ) {

		if ( $return == 'websafe' ) {
			if ( function_exists('hoot_fonts_list') )
				return hoot_fonts_list();
			else
				return array();
		}

		if ( $return == 'google-fonts' || $return == 'google-font' ) {
			if ( function_exists('hoot_googlefonts_list') )
				return hoot_googlefonts_list();
			else
				return apply_filters( 'hoot_google_fonts', array() );
		}

		return array();
	}

	/**
	 * Return icon list array
	 *
	 * @since 1.0.0
	 * @param string $return array to return list|icons|sections
	 * @return array
	 */
	static function icons( $return = 'list' ) {

		if ( !function_exists('hoot_fonticons_list') )
			return array();

		if ( $return == 'sections' || $return == 'section' )
			return hoot_fonticons_list('sections');

		$iconsArray = hoot_fonticons_list('icons');

		if ( $return == 'icons' || $return == 'icon' )
			return $iconsArray;

		if ( $return == 'lists' || $return == 'list' ) {
			$iconsList = array();
			foreach ( $iconsArray as $name => $array ) {
				$iconsList = array_merge( $iconsList, $array );
			}
			return $iconsList;
		}

		return array();
	}

}