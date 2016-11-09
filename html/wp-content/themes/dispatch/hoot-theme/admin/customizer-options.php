<?php
/**
 * Defines customizer options
 *
 * This file is loaded at 'after_setup_theme' hook with 10 priority.
 *
 * @package hoot
 * @subpackage dispatch
 * @since dispatch 2.0
 */

/**
 * Build the Customizer options (panels, sections, settings)
 *
 * @since 2.0
 * @access public
 * @return array
 */
if ( !function_exists( 'hoot_theme_customizer_options' ) ) :
function hoot_theme_customizer_options() {

	// Stores all the settings to be added
	$settings = array();

	// Stores all the sections to be added
	$sections = array();

	// Stores all the panels to be added
	$panels = array();

	// Theme defaults
	extract( apply_filters( 'hoot_theme_options_defaults', array(
		'accent_color'             => '#48ab79',
		'accent_font'              => '#ffffff',
		'box_background'           => '#ffffff',
		'site_background'          => '#ffffff',
		'wt_html_slide_background' => '#ffffff',
		// 'font_size'                => '14px',
		// 'font_face'                => '"Open Sans", sans-serif',
		// 'font_style'               => 'none',
		// 'font_color'               => '#444444',
	) ) );

	// Directory path for radioimage buttons
	$imagepath =  trailingslashit( HOOT_THEMEURI ) . 'admin/images/';

	// Logo Sizes (different range than standard typography range)
	// $logosizes = array();
	// $logosizerange = range( 14, 110 );
	// foreach ( $logosizerange as $isr )
	// 	$logosizes[ $isr . 'px' ] = $isr . 'px';
	// $logosizes = apply_filters( 'hoot_theme_options_logosizes', $logosizes);

	// Logo Font Options for Lite version
	// $logofont = apply_filters( 'hoot_theme_options_logofont', array(
	// 				'standard' => __('Standard Body Font (Open Sans)', 'dispatch'),
	// 				'heading' => __('Heading Font', 'dispatch'),
	// 				) );

	/*** Add Options (Panels, Sections, Settings) ***/

	/** Panel Genral **/

	$panel = 'general';

	$panels[ $panel ] = array(
		'title'    => __( 'General', 'dispatch' ),
		'icon'     => 'dashicons dashicons-admin-settings', // fa fa-sliders
	);

	$section = 'logo';

	$sections[ $section ] = array(
		'title'       => __( 'Logo Settings', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['logo'] = array(
		'label'       => __( 'Site Logo', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radio',
		// 'priority'    => '10',
		'choices'     => array(
			'text'        => __('Default Text (Site Title)', 'dispatch'),
			'image'       => __('Image Logo', 'dispatch'),
		),
		'default'     => 'text',
		'description' => array(
			'type' => 'blue',
			'text' => sprintf( __('Use %sSite Title%s as default text logo', 'dispatch'), '<a href="' . admin_url('/') . 'options-general.php" target="_blank">', '</a>' ),
		),
	);

	$settings['site_title_icon'] = array(
		'label'           => __( 'Site Title Icon (Optional)', 'dispatch' ),
		'section'         => $section,
		'type'            => 'icon',
		// 'priority'     => '10',
		// 'default'         => 'fa-anchor',
		'description'     => __( 'Leave empty to hide icon.', 'dispatch' ),
		'active_callback' => 'hoot_callback_site_title_icon',
	);

	$settings['logo_image'] = array(
		'label'           => __( 'Upload Logo', 'dispatch' ),
		'section'         => $section,
		'type'            => 'image',
		// 'priority'     => '10',
		'active_callback' => 'hoot_callback_logo_image',
	);

	$settings['show_tagline'] = array(
		'label'           => __( 'Show Tagline', 'dispatch' ),
		'sublabel'        => __( 'Display site description as tagline below logo.', 'dispatch' ),
		'section'         => $section,
		'type'            => 'checkbox',
		'default'         => 1,
		'active_callback' => 'hoot_callback_show_tagline',
	);

	$section = 'setup';

	$sections[ $section ] = array(
		'title'       => __( 'Site Setup', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['site_layout'] = array(
		'label'       => __( 'Site Layout - Boxed vs Stretched', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'boxed'   => __('Boxed layout', 'dispatch'),
			'stretch' => __('Stretched layout (full width)', 'dispatch'),
		),
		'default'     => 'stretch',
		'description' => array(
			'type' => 'blue',
			'text' => __("For boxed layouts, both backgrounds (site and content box) can be set in the 'Styling > Backgrounds' panel.<br /><br />For Stretched Layout, only site background is available.", 'dispatch'),
		),
	);

	$settings['site_width'] = array(
		'label'       => __( 'Max. Site Width (pixels)', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'1260' => __('1260px (wide)', 'dispatch'),
			'1080' => __('1080px (standard)', 'dispatch'),
		),
		'default'     => '1260',
	);

	$settings['load_minified'] = array(
		'label'       => __( 'Load Minified Styles and Scripts (when available)', 'dispatch' ),
		'sublabel'    => __( 'Checking this option reduces data size, hence increasing page load speed.', 'dispatch' ),
		'section'     => $section,
		'type'        => 'checkbox',
	);

	$section = 'layout';

	$sections[ $section ] = array(
		'title'       => __( 'Site Layout', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['sidebar'] = array(
		'label'       => __( 'Sidebar Layout (Site-wide)', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radioimage',
		// 'priority'    => '10',
		'choices'     => array(
			'wide-right'   => $imagepath . 'sidebar-wide-right.png',
			'narrow-right' => $imagepath . 'sidebar-narrow-right.png',
			'wide-left'    => $imagepath . 'sidebar-wide-left.png',
			'narrow-left'  => $imagepath . 'sidebar-narrow-left.png',
			'none'         => $imagepath . 'sidebar-none.png',
		),
		'default'     => 'wide-right',
		'description' => __("Set the default sidebar width and position for your entire site.<hr>* Wide Right Sidebar<br />* Narrow Right Sidebar<br />* Wide Left Sidebar<br />* Narrow Left Sidebar<br />* No Sidebar", 'dispatch'),
	);

	$settings['sidebar_pages'] = array(
		'label'       => __( 'Sidebar Layout (for Pages)', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radioimage',
		// 'priority'    => '10',
		'choices'     => array(
			'wide-right'   => $imagepath . 'sidebar-wide-right.png',
			'narrow-right' => $imagepath . 'sidebar-narrow-right.png',
			'wide-left'    => $imagepath . 'sidebar-wide-left.png',
			'narrow-left'  => $imagepath . 'sidebar-narrow-left.png',
			'none'         => $imagepath . 'sidebar-none.png',
		),
		'default'     => 'wide-right',
		'description' => __("Set the default sidebar width and position for pages.<hr>* Wide Right Sidebar<br />* Narrow Right Sidebar<br />* Wide Left Sidebar<br />* Narrow Left Sidebar<br />* No Sidebar", 'dispatch'),
	);

	$settings['sidebar_posts'] = array(
		'label'       => __( 'Sidebar Layout (for single Posts)', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radioimage',
		// 'priority'    => '10',
		'choices'     => array(
			'wide-right'   => $imagepath . 'sidebar-wide-right.png',
			'narrow-right' => $imagepath . 'sidebar-narrow-right.png',
			'wide-left'    => $imagepath . 'sidebar-wide-left.png',
			'narrow-left'  => $imagepath . 'sidebar-narrow-left.png',
			'none'         => $imagepath . 'sidebar-none.png',
		),
		'default'     => 'wide-right',
		'description' => __("Set the default sidebar width and position for single posts.<hr>* Wide Right Sidebar<br />* Narrow Right Sidebar<br />* Wide Left Sidebar<br />* Narrow Left Sidebar<br />* No Sidebar", 'dispatch'),
	);

	if ( current_theme_supports( 'woocommerce' ) ) :

		$settings['sidebar_wooshop'] = array(
			'label'       => __( 'Sidebar Layout (Woocommerce Shop/Archives)', 'dispatch' ),
			'section'     => $section,
			'type'        => 'radioimage',
			// 'priority'    => '10',
			'choices'     => array(
				'wide-right'   => $imagepath . 'sidebar-wide-right.png',
				'narrow-right' => $imagepath . 'sidebar-narrow-right.png',
				'wide-left'    => $imagepath . 'sidebar-wide-left.png',
				'narrow-left'  => $imagepath . 'sidebar-narrow-left.png',
				'none'         => $imagepath . 'sidebar-none.png',
			),
			'default'     => 'wide-right',
			'description' => __("Set the default sidebar width and position for WooCommerce shop and archives pages like product categories etc.<hr>* Wide Right Sidebar<br />* Narrow Right Sidebar<br />* Wide Left Sidebar<br />* Narrow Left Sidebar<br />* No Sidebar", 'dispatch'),
		);

		$settings['sidebar_wooproduct'] = array(
			'label'       => __( 'Sidebar Layout (Woocommerce Single Product Page)', 'dispatch' ),
			'section'     => $section,
			'type'        => 'radioimage',
			// 'priority'    => '10',
			'choices'     => array(
				'wide-right'   => $imagepath . 'sidebar-wide-right.png',
				'narrow-right' => $imagepath . 'sidebar-narrow-right.png',
				'wide-left'    => $imagepath . 'sidebar-wide-left.png',
				'narrow-left'  => $imagepath . 'sidebar-narrow-left.png',
				'none'         => $imagepath . 'sidebar-none.png',
			),
			'default'     => 'wide-right',
			'description' => __("Set the default sidebar width and position for WooCommerce product pages<hr>* Wide Right Sidebar<br />* Narrow Right Sidebar<br />* Wide Left Sidebar<br />* Narrow Left Sidebar<br />* No Sidebar", 'dispatch'),
		);

	endif;

	$settings['footer'] = array(
		'label'       => __( 'Footer Layout', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radioimage',
		// 'priority'    => '10',
		'choices'     => array(
			'1-1' => $imagepath . '1-1.png',
			'2-1' => $imagepath . '2-1.png',
			'2-2' => $imagepath . '2-2.png',
			'2-3' => $imagepath . '2-3.png',
			'3-1' => $imagepath . '3-1.png',
			'3-2' => $imagepath . '3-2.png',
			'3-3' => $imagepath . '3-3.png',
			'3-4' => $imagepath . '3-4.png',
			'4-1' => $imagepath . '4-1.png',
		),
		'default'     => '3-1',
		'description' => array(
			'type' => 'red',
			'text' => sprintf( __('You must first save the changes you make here and refresh this screen for footer columns to appear in the Widgets panel (in customizer).<hr> Once you save the settings here, you can add content to footer columns using the %sWidgets Management screen%s.', 'dispatch'), '<a href="' . admin_url('/') . 'widgets.php" target="_blank">', '</a>' ),
		),
	);

	/** Panel Styling **/

	$panel = 'styling';

	$panels[ $panel ] = array(
		'title'    => __( 'Styling', 'dispatch' ),
		'icon'     => 'dashicons dashicons-art', // fa fa-align-center
	);

	$section = 'colors';

	// Redundant as 'colors' section is added by WP. But we still add it for brevity
	$sections[ $section ] = array(
		'title'       => __( 'Colors', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['accent_color'] = array(
		'label'       => __( 'Accent Color', 'dispatch' ),
		'section'     => $section,
		'type'        => 'color',
		'default'     => $accent_color,
	);

	$settings['accent_font'] = array(
		'label'       => __( 'Font Color on Accent Color', 'dispatch' ),
		'section'     => $section,
		'type'        => 'color',
		'default'     => $accent_font,
	);

	if ( current_theme_supports( 'woocommerce' ) ) :
		$settings['woocommerce-colors-plugin'] = array(
			'label'       => __( 'Woocommerce Styling', 'dispatch' ),
			'section'     => $section,
			'type'        => 'content',
			'content'     => sprintf( __('Looks like you are using Woocommerce. Install %sthis plugin%s to change colors and styles for WooCommerce elements like buttons etc.', 'dispatch'), '<a href="https://wordpress.org/plugins/woocommerce-colors/" target="_blank">', '</a>' ),
		);
	endif;

	$section = 'backgrounds';

	$sections[ $section ] = array(
		'title'       => __( 'Backgrounds', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['background'] = array(
		'label'       => __( 'Site Background', 'dispatch' ),
		'section'     => $section,
		'type'        => 'betterbackground',
		'default'     => array(
			'color'      => $site_background,
			// 'pattern'    => 'hoot/images/patterns/7.png',
		),
	);

	$settings['box_background'] = array(
		'label'       => __( 'Content Box Background', 'dispatch' ),
		'section'     => $section,
		'type'        => 'betterbackground',
		'default'     => array(
			'color'      => $box_background,
			// 'pattern'    => 'hoot/images/patterns/7.png',
		),
		'description' => array(
			'type' => 'blue',
			'text' => __("This background is available only when <strong>'Boxed'</strong> option is selected in the <strong>'General > Site Setup'</strong> panel.", 'dispatch'),
		),
		// 'active_callback' => 'hoot_callback_box_background_color',
	);

	/** Panel Content **/

	$panel = 'content';

	$panels[ $panel ] = array(
		'title'    => __( 'Content', 'dispatch' ),
		'icon'     => 'dashicons dashicons-editor-alignleft', // fa fa-align-center
	);

	$section = 'topbar';

	$sections[ $section ] = array(
		'title'       => __( 'Topbar', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['topbar-widget-areas'] = array(
		'label'       => __( 'Left/Right Topbar', 'dispatch' ),
		'section'     => $section,
		'type'        => 'content',
		'content'     => sprintf( __('You can add content to Left/Right Topbar using Text Widget in the %sWidget Management Screen%s', 'dispatch'), '<a href="' . admin_url('/') . 'widgets.php" target="_blank">', '</a>' ),
	);

	$section = 'archives';

	$sections[ $section ] = array(
		'title'       => __( 'Archives (Blog, Cats, Tags)', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['archive_post_content'] = array(
		'label'       => __( 'Post Items Content', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radio',
		// 'priority'    => '10',
		'choices'     => array(
			'excerpt' => __('Post Excerpt', 'dispatch'),
			'full-content' => __('Full Post Content', 'dispatch'),
		),
		'default'     => 'excerpt',
		'description' => __( 'Content to display for each post in the list', 'dispatch' ),
	);

	$settings['archive_post_meta'] = array(
		'label'       => __( 'Meta Information for Post List Items', 'dispatch' ),
		'sublabel'    => __( 'Check which meta information to display for each post item in the archive list.', 'dispatch' ),
		'section'     => $section,
		'type'        => 'checkbox',
		// 'priority'    => '10',
		'choices'     => array(
			'author'   => __('Author', 'dispatch'),
			'date'     => __('Date', 'dispatch'),
			'cats'     => __('Categories', 'dispatch'),
			'tags'     => __('Tags', 'dispatch'),
			'comments' => __('No. of comments', 'dispatch')
		),
		'default'     => 'author, date, cats, comments',
	);

	$settings['excerpt_length'] = array(
		'label'       => __( 'Excerpt Length', 'dispatch' ),
		'section'     => $section,
		'type'        => 'text',
		// 'priority'    => '10',
		'description' => __( 'Number of words in excerpt. Default is 105. Leave empty if you dont want to change it.', 'dispatch' ),
		'input_attrs' => array(
			'min' => 0,
			'max' => 3,
			'placeholder' => __( 'default: 105', 'dispatch' ),
		),
	);

	$settings['read_more'] = array(
		'label'       => __( "'Read More' Text", 'dispatch' ),
		'section'     => $section,
		'type'        => 'text',
		// 'priority'    => '10',
		'description' => __( "Replace the default 'Read More' text. Leave empty if you dont want to change it.", 'dispatch' ),
		'input_attrs' => array(
			'placeholder' => __( 'default: READ MORE &rarr;', 'dispatch' ),
		),
	);

	$section = 'singular';

	$sections[ $section ] = array(
		'title'       => __( 'Single (Posts, Pages)', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['post_featured_image'] = array(
		'label'       => __( 'Display Featured Image', 'dispatch' ),
		'sublabel'    => __( 'Display featured image at the beginning of post/page content.', 'dispatch' ),
		'section'     => $section,
		'type'        => 'checkbox',
		// 'priority'    => '10',
		'default'     => 1,
	);

	$settings['post_meta'] = array(
		'label'       => __( 'Meta Information on Posts', 'dispatch' ),
		'sublabel'    => __( "Check which meta information to display on an individual 'Post' page", 'dispatch' ),
		'section'     => $section,
		'type'        => 'checkbox',
		// 'priority'    => '10',
		'choices'     => array(
			'author'   => __('Author', 'dispatch'),
			'date'     => __('Date', 'dispatch'),
			'cats'     => __('Categories', 'dispatch'),
			'tags'     => __('Tags', 'dispatch'),
			'comments' => __('No. of comments', 'dispatch')
		),
		'default'     => 'author, date, cats, tags, comments',
	);

	$settings['page_meta'] = array(
		'label'       => __( 'Meta Information on Page', 'dispatch' ),
		'sublabel'    => __( "Check which meta information to display on an individual 'Page' page", 'dispatch' ),
		'section'     => $section,
		'type'        => 'checkbox',
		// 'priority'    => '10',
		'choices'     => array(
			'author'   => __('Author', 'dispatch'),
			'date'     => __('Date', 'dispatch'),
			'comments' => __('No. of comments', 'dispatch')
		),
		'default'     => 'author, date, comments',
	);

	$settings['post_meta_location'] = array(
		'label'       => __( 'Meta Information location', 'dispatch' ),
		'section'     => $section,
		'type'        => 'radio',
		// 'priority'    => '10',
		'choices'     => array(
			'top'    => __('Top (below title)', 'dispatch'),
			'bottom' => __('Bottom (after content)', 'dispatch'),
		),
		'default'     => 'top',
	);

	$settings['post_prev_next_links'] = array(
		'label'       => __( 'Previous/Next Posts', 'dispatch' ),
		'sublabel'    => __( 'Display links to Prev/Next Post links at the end of post content.', 'dispatch' ),
		'section'     => $section,
		'type'        => 'checkbox',
		// 'priority'    => '10',
		'default'     => 1,
	);

	$settings['contact-form'] = array(
		'label'       => __( 'Contact Form', 'dispatch' ),
		'section'     => $section,
		'type'        => 'content',
		'content'     => sprintf( __('This theme comes bundled with CSS required to style %sContact-Form-7%s forms. Simply install and activate the plugin to add Contact Forms to your pages.', 'dispatch'), '<a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">', '</a>'),
	);

	if ( current_theme_supports( 'woocommerce' ) ) :

		$section = 'woocommerce';

		$sections[ $section ] = array(
			'title'       => __( 'WooCommerce', 'dispatch' ),
			'panel'       => $panel,
			// 'description' => __( '', 'dispatch' ),
		);

		$wooproducts = range( 0, 99 );
		for ( $wpr=0; $wpr < 4; $wpr++ )
			unset( $wooproducts[$wpr] );
		$settings['wooshop_products'] = array(
			'label'       => __( 'Total Products per page', 'dispatch' ),
			'section'     => $section,
			'type'        => 'select',
			// 'priority'    => '10',
			'choices'     => $wooproducts,
			'default'     => '12',
			'description' => __( 'Total number of products to show on the Shop page', 'dispatch' ),
		);

		$settings['wooshop_product_columns'] = array(
			'label'       => __( 'Product Columns', 'dispatch' ),
			'section'     => $section,
			'type'        => 'select',
			// 'priority'    => '10',
			'choices'     => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			),
			'default'     => '3',
			'description' => __( 'Number of products to show in 1 row on the Shop page', 'dispatch' ),
		);

	endif;

	$section = 'footer';

	$sections[ $section ] = array(
		'title'       => __( 'Footer', 'dispatch' ),
		'panel'       => $panel,
		// 'description' => __( '', 'dispatch' ),
	);

	$settings['site_info'] = array(
		'label'       => __( 'Site Info Text (footer)', 'dispatch' ),
		'section'     => $section,
		'type'        => 'textarea',
		// 'priority'    => '10',
		'default'     => __( '<!--default-->', 'dispatch'),
		'description' => array(
			'type' => 'yellow',
			'text' => sprintf( __('Text shown in footer. Useful for showing copyright info etc.<hr>Use the <code>&lt;!--default--&gt;</code> tag to show the default Info Text.<hr>Use the <code>&lt;!--year--&gt;</code> tag to insert the current year.<hr>Always use %sHTML codes%s for symbols. For example, the HTML for &copy; is <code>&amp;copy;</code>', 'dispatch'), '<a href="http://ascii.cl/htmlcodes.htm" target="_blank">', '</a>' ),
		),
	);

	/** Panel Templates **/

	if ( current_theme_supports( 'hoot-widgetized-template' ) ) :

		$panel = 'templates';

		global $wp_version;
		$panels[ $panel ] = array(
			'title'    => __( 'Templates', 'dispatch' ),
			'icon'     => ( ( version_compare( $wp_version, '4.3', '>=' ) ) ? 'dashicons dashicons-editor-table' : 'dashicons dashicons-grid-view' ),
		);

		$section = 'widgetized-template';

		$sections[ $section ] = array(
			'title'       => __( 'Widgetized Template', 'dispatch' ),
			'panel'       => $panel,
			'description' => sprintf( __( "<strong>How to use this template</strong><p>'Widgetized Template' is a special Page Template which is often used as a quick way to create a Front Page.</p><ol><li>Create a %sNew Page%s. In the <strong>'Page Attributes'</strong> option box select the <strong>'Widgetized Template'</strong> in the <strong>'Template'</strong> dropdown.</li><li>Goto %sSetting > Reading%s menu. In the <strong>'Front page displays'</strong> option, select <strong>'A static page'</strong> and select the page you created in previous step.</li></ol>", 'dispatch'),'<a href="' . admin_url('/') . 'post-new.php?post_type=page" target="_blank">', '</a>','<a href="' . admin_url('/') . 'options-reading.php" target="_blank">', '</a>'),
		);

		$widget_area_options = array(
			'highlight' => array(
				'label'   => __( 'Highlight Background', 'dispatch' ),
				'type'    => 'checkbox',
			),
			'columns' => array(
				'label'   => __( 'Columns', 'dispatch' ),
				'type'    => 'select',
				'choices' => array(
					'100'         => __('One Column [100]', 'dispatch'),
					'50-50'       => __('Two Columns [50 50]', 'dispatch'),
					'33-66'       => __('Two Columns [33 66]', 'dispatch'),
					'66-33'       => __('Two Columns [66 33]', 'dispatch'),
					'25-75'       => __('Two Columns [25 75]', 'dispatch'),
					'75-25'       => __('Two Columns [75 25]', 'dispatch'),
					'33-33-33'    => __('Three Columns [33 33 33]', 'dispatch'),
					'25-25-25-25' => __('Four Columns [25 25 25 25]', 'dispatch'),
				),
			),
		);

		$settings['widgetized_template_sections'] = array(
			'label'       => __( 'Widget Areas Order', 'dispatch' ),
			'sublabel'    => sprintf( __("<p>Sort different sections of the 'Widgetized Template' in the order you want them to appear.</p><ul><li>You can add content to widget areas from the %sWidgets Management screen%s.</li><li>You can disable areas by clicking the 'eye' icon. (This will hide them on the Widgets screen as well)</li><li>'Page Content' is the actual content of the page on which this template is applied. This can be used in special situations for creating extra customizable content.</li></ul>", 'dispatch'), '<a href="' . admin_url('/') . 'widgets.php" target="_blank">', '</a>' ),
			'section'     => $section,
			'type'        => 'sortlist',
			// 'priority'    => '10',
			'choices'     => array(
				'slider_html' => __('HTML Slider', 'dispatch'),
				'slider_img'  => __('Image Slider', 'dispatch'),
				'area_a'      => __('Widget Area A', 'dispatch'),
				'area_b'      => __('Widget Area B', 'dispatch'),
				'area_c'      => __('Widget Area C', 'dispatch'),
				'area_d'      => __('Widget Area D', 'dispatch'),
				'area_e'      => __('Widget Area E', 'dispatch'),
				'content'     => __('Page Content Area', 'dispatch'),
			),
			'default'     => array(
				'content' => array( 'sortitem_hide' => 1, ),
				'area_d'  => array( 'columns' => '50-50' ),
			),
			'options'     => array(
				// 'slider_html' => $widget_area_options,
				// 'slider_img'  => $widget_area_options,
				'area_a'      => $widget_area_options,
				'area_b'      => $widget_area_options,
				'area_c'      => $widget_area_options,
				'area_d'      => $widget_area_options,
				'area_e'      => $widget_area_options,
				'content'     => array( 'highlight' => array(
									'label'   => __( 'Highlighted Background', 'dispatch' ),
									'type'    => 'checkbox',
								), ),
			),
			'attributes'  => array(
				'hideable'      => true,
				'sortable'      => true,
			),
			'description' => array(
				'type' => 'red',
				'text' => sprintf( __('You must first save the changes you make here and refresh this screen for widget areas to appear in the Widgets panel (in customizer).<hr> Once you save the settings here, you can add content to these widget areas using the %sWidgets Management screen%s.', 'dispatch'), '<a href="' . admin_url('/') . 'widgets.php" target="_blank">', '</a>' ),
			),
		);

		$section = 'slider_html';

		$sections[ $section ] = array(
			'title'       => __( 'HTML Slider', 'dispatch' ),
			'panel'       => $panel,
		);

		$settings['wt_html_slider_width'] = array(
			'label'       => __( 'Slider Width (in Stretched Site Layout)', 'dispatch' ),
			'section'     => $section,
			'type'        => 'radioimage',
			'choices'     => array(
				'boxed'   => $imagepath . 'slider-width-boxed.png',
				'stretch' => $imagepath . 'slider-width-stretch.png',
			),
			'default'     => 'boxed',
			'description' => array(
				'type' => 'blue',
				'text' => __("Enable this to stretch the slider to Full Screen Width.<hr>Note: This option is useful only if the <strong>'Site Layout'</strong> is set to <strong>'Stretched (full width)'</strong> in the 'General > Site Setup' panel.", 'dispatch'),
			),
		);

		$settings['wt_html_slider'] = array(
			'label'       => __( 'Slides', 'dispatch' ),
			'section'     => $section,
			'type'        => 'content',
			'content'     => __( 'Premium version of this theme comes with capability to create <strong>Unlimited slides</strong>.', 'dispatch' ),
		);

		for ( $slide = 1; $slide <= 4; $slide++ ) {

			$settings["wt_html_slide_{$slide}"] = array(
				'label'       => sprintf( __( 'Slide %s Content', 'dispatch' ), $slide),
				'section'     => $section,
				'type'        => 'group',
				// 'priority'    => '10',
				'button'      => sprintf( __( 'Edit Slide %s', 'dispatch' ), $slide),
				'options'     => array(
					'description' => array(
						'label'       => '',
						'type'        => 'content',
						'content'     => __( '<strong>To hide this slide</strong>, simply leave the slide fields empty.', 'dispatch' ),
					),
					'image' => array(
						'label'       => __( 'Slide Image', 'dispatch' ),
						'type'        => 'image',
					),
					'title' => array(
						'label'       => __( 'Title', 'dispatch' ),
						'type'        => 'text',
					),
					'content' => array(
						'label'       => __( 'Content', 'dispatch' ),
						'type'        => 'textarea',
					),
					'button' => array(
						'label'       => __( 'Link Text', 'dispatch' ),
						'type'        => 'text',
					),
					'url' => array(
						'label'       => __( 'Link URL', 'dispatch' ),
						'type'        => 'url',
						'description' => __( 'Leave empty if you do not want to show the link.', 'dispatch' ),
						'input_attrs' => array(
							'placeholder' => 'http://',
						),
					),
					'background' => array(
						'label'       => __( 'Slide Background', 'dispatch' ),
						'type'        => 'color',
						'description' => array(
							'type' => 'yellow',
							'text' => __("This can be useful if you are using transparent images", 'dispatch'),
						),
						'default'     => $wt_html_slide_background,
					),
				),
			);

		} // end for

		$section = 'slider_img';

		$sections[ $section ] = array(
			'title'       => __( 'Image Slider', 'dispatch' ),
			'panel'       => $panel,
		);

		$settings['wt_img_slider_width'] = array(
			'label'       => __( 'Slider Width (in Stretched Site Layout)', 'dispatch' ),
			'section'     => $section,
			'type'        => 'radioimage',
			'choices'     => array(
				'boxed'   => $imagepath . 'slider-width-boxed.png',
				'stretch' => $imagepath . 'slider-width-stretch.png',
			),
			'default'     => 'boxed',
			'description' => array(
				'type' => 'blue',
				'text' => __("Enable this to stretch the slider to Full Screen Width.<hr>Note: This option is useful only if the <strong>'Site Layout'</strong> is set to <strong>'Stretched (full width)'</strong> in the 'General > Site Setup' panel.", 'dispatch'),
			),
		);

		$settings['wt_img_slider'] = array(
			'label'       => __( 'Slides', 'dispatch' ),
			'section'     => $section,
			'type'        => 'content',
			'content'     => __( 'Premium version of this theme comes with capability to create <strong>Unlimited slides</strong>.', 'dispatch' ),
		);

		for ( $slide = 1; $slide <= 4; $slide++ ) { 

			$settings["wt_img_slide_{$slide}"] = array(
				'label'       => '',//sprintf( __( 'Slide %s Content', 'dispatch' ), $slide),
				'section'     => $section,
				'type'        => 'group',
				// 'priority'    => '10',
				'button'      => sprintf( __( 'Edit Slide %s', 'dispatch' ), $slide),
				'options'     => array(
					'description' => array(
						'label'       => '',
						'type'        => 'content',
						'content'     => __( '<strong>To hide this slide</strong>, simply leave the Image empty.', 'dispatch' ),
					),
					'image' => array(
						'label'       => __( 'Slide Image', 'dispatch' ),
						'type'        => 'image',
						'description' => __( 'The main showcase image.', 'dispatch' ),
					),
					'caption' => array(
						'label'       => __( 'Slide Caption (optional)', 'dispatch' ),
						'type'        => 'text',
					),
					'url' => array(
						'label'       => __( 'Slide Link', 'dispatch' ),
						'type'        => 'url',
						'description' => __( 'Leave empty if you do not want to link the slide.', 'dispatch' ),
						'input_attrs' => array(
							'placeholder' => 'http://',
						),
					),
				),
			);

		} // end for

	endif;


	/*** Return Options Array ***/
	return apply_filters( 'hoot_theme_customizer_options', array(
		'settings' => $settings,
		'sections' => $sections,
		'panels'   => $panels,
	) );

}
endif;

/**
 * Add Options (settings, sections and panels) to Hoot_Customizer class options object
 *
 * @since 2.0
 * @access public
 * @return void
 */
if ( !function_exists( 'hoot_theme_add_customizer_options' ) ) :
function hoot_theme_add_customizer_options() {

	$hoot_customizer = Hoot_Customizer::get_instance();

	// Add Options
	$options = hoot_theme_customizer_options();
	$hoot_customizer->add_options( array(
		'settings' => $options['settings'],
		'sections' => $options['sections'],
		'panels' => $options['panels'],
		) );

	// Add Inforbuttons
	$hoot_customizer->add_infobuttons( array(
		'demo'    => array( 'text'   => __( 'Demo', 'dispatch'),
							'url'    => 'http://demo.wphoot.com/dispatch/',
							'icon'   => 'fa fa-eye' ),
		'docs'    => array( 'text'   => __( 'Documentation &amp; Support', 'dispatch'),
							'url'    => 'http://help.wphoot.com/support/solutions',
							'icon'   => 'fa fa-support' ),
		'rate'    => array( 'text'   => __( 'If you like this theme, support our work by giving it a 5<i class="fa fa-star"></i> rating on wordpress.org :)', 'dispatch'),
							'url'    => 'https://wordpress.org/support/view/theme-reviews/dispatch#postform',
							'icon'   => 'fa fa-star' ),
		) );

	// Add Premium Infobutton
	$hoot_customizer->add_infobuttons( array(
		'premium' => array( 'text'   => __( 'Premium', 'dispatch'),
							'type'   => 'premium',
							'url'    => 'http://wphoot.com/themes/dispatch/',
							'icon'   => 'fa fa-rocket' ),
		) );

}
endif;
add_action( 'init', 'hoot_theme_add_customizer_options', 0 ); // cannot hook into 'after_setup_theme' as this hook is already being executed (this file is loaded at after_setup_theme @priority 10) (hooking into same hook from within while hook is being executed leads to undesirable effects as $GLOBALS[$wp_filter]['after_setup_theme'] has already been ksorted)
// Hence, we hook into 'init' @priority 0, so that settings array gets populated before 'widgets_init' action ( which itself is hooked to 'init' at prioirty 1 ) for creating widget areas ( settings array is needed for creating defaults when user value has not been stored )
 
/**
 * Add Icons for WordPress Default Panels to Icon Nav
 *
 * @since 2.0
 * @param array $panels
 * @return array
 */
function hoot_customizer_panel_icons( $panels ) {

	$panels['widgets'] = array(
		'title' => __( 'Widgets &amp; Sidebars', 'dispatch' ),
		'icon' => 'dashicons dashicons-admin-plugins', // fa fa-puzzle-piece
	);

	global $wp_version;
	if ( version_compare( $wp_version, '4.3', '>=' ) ) {
		$panels['nav_menus'] = array(
			'title' => __( 'Menus', 'dispatch' ),
			'icon' => 'dashicons dashicons-menu', // fa fa-bars
		);
	}

	return $panels;
}
add_filter( 'hoot_customizer_panel_icons', 'hoot_customizer_panel_icons' );

/**
 * Modify default WordPress Settings Sections and Panels
 *
 * @since 2.0
 * @param object $wp_customize
 * @return void
 */
function hoot_customizer_modify_default_options( $wp_customize ) {

	$wp_customize->get_section( 'title_tagline' )->panel = 'general';
	$wp_customize->get_section( 'title_tagline' )->priority = 1;

	$wp_customize->get_section( 'static_front_page' )->panel = 'content';
	$wp_customize->get_section( 'static_front_page' )->priority = 1;

	$wp_customize->get_section( 'colors' )->panel = 'styling';

	// global $wp_version;
	// if ( version_compare( $wp_version, '4.3', '>=' ) ) // 'Creating Default Object from Empty Value' error before 4.3 since 'nav_menus' panel did not exist ( we did have 'nav' section till 4.1.9 i.e. before 4.2 )
	// 	$wp_customize->get_panel( 'nav_menus' )->priority = 999;
	// This will set the priority, however will give a 'Creating Default Object from Empty Value' error first.
	// $wp_customize->get_panel( 'widgets' )->priority = 999;

}
add_action( 'customize_register', 'hoot_customizer_modify_default_options', 100 );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since 2.0
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function hoot_customizer_customize_register( $wp_customize ) {
	// $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
}
add_action( 'customize_register', 'hoot_customizer_customize_register' );

/**
 * Callback Functions for customizer settings
 */

function hoot_callback_site_title_icon( $control ) {
	$selector = $control->manager->get_setting('logo')->value();
	return ( $selector == 'text' ) ? true : false;
}
function hoot_callback_logo_image( $control ) {
	$selector = $control->manager->get_setting('logo')->value();
	return ( $selector == 'image' ) ? true : false;
}
function hoot_callback_show_tagline( $control ) {
	$selector = $control->manager->get_setting('logo')->value();
	return ( $selector == 'text' ) ? true : false;
}

function hoot_callback_box_background_color( $control ) {
	$selector = $control->manager->get_setting('site_layout')->value();
	return ( $selector == 'boxed' ) ? true : false;
}