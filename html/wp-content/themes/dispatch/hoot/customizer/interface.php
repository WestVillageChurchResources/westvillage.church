<?php
/**
 * Builds out customizer options
 *
 * @package hoot
 * @subpackage hoot-customizer
 * @since hoot 2.0.0
 */

/**
 * Configure and add panels, sections, settings/controls for the theme customizer
 *
 * @since 2.0.0
 * @param object $wp_customize The global customizer object.
 * @return void
 */
function hoot_customizer_register( $wp_customize ) {

	$hoot_customizer = Hoot_Customizer::get_instance();
	$options = $hoot_customizer->get_options();
	if ( empty( $options ) ) {
		return;
	}

	/** Add the panels **/
	if ( !empty( $options['panels'] ) && is_array( $options['panels'] ) ) {
		hoot_customizer_add_panels( $options['panels'], $wp_customize );
	}

	/** Add the sections **/
	if ( !empty( $options['sections'] ) && is_array( $options['sections'] ) ) {
		hoot_customizer_add_sections( $options['sections'], $wp_customize );
	}

	/** Exit if no settings to add **/
	if ( empty( $options['settings'] ) || !is_array( $options['settings'] ) )
		return;

	/** Objects added.. Use this hook instead of 'customize_register' hook to remove or modify any Customizer object, and to access the Customizer Manager. For adding, continue using 'customize_register' **/
	do_action( 'hoot_customize_registered', $wp_customize, $hoot_customizer );

	// Sets the priority for each control added
	$loop = 0;

	/** Loop through each of the settings **/
	foreach ( $options['settings'] as $id => $setting ) :
		if ( isset( $setting['type'] ) ) :

			/** Prepare Setting **/

			// Apply a default sanitization if one isn't set and
			// set blank active_callback if one isn't set
			$setting = wp_parse_args( $setting, array(
				'label'             => '',
				'sanitize_callback' => hoot_customizer_get_sanitization( $setting['type'], $setting, $id ),
				'active_callback'   => '',
			) );

			// Set Priority (increment priority by 10 to allow child themes to insert controls in between)
			if ( ! isset( $setting['priority'] ) || ! is_numeric( $setting['priority'] ) ) {
				$loop += 10;
				$setting['priority'] = $loop;
			}
			if ( defined( 'HOOT_DEBUG' ) && true === HOOT_DEBUG )
				hoot_debug_info( "[{$setting['priority']}] {$id}\n" );

			// Set and prepare description
			if ( empty( $setting['description'] ) ) {
				$setting['description'] = '';
			} elseif ( is_array( $setting['description'] ) && !empty( $setting['description']['text'] ) ) {

				$description_icon = 'fa fa-question-circle hoot-tooltipicon-general';
				$popup_class = 'hoot-tooltip-general';

				if ( isset( $setting['description']['type'] ) ) {
					switch( $setting['description']['type'] ) {
						case 'info': case 'blue':
							$description_icon = 'fa fa-exclamation-circle hoot-tooltipicon-blue';
							$popup_class = 'hoot-tooltip-blue';
							break;
						case 'warning': case 'error': case 'important': case 'red':
							$description_icon = 'fa fa-exclamation-triangle hoot-tooltipicon-red';
							$popup_class = 'hoot-tooltip-red';
							break;
						case 'tip': case 'optional': case 'yellow':
							$description_icon = 'fa fa-lightbulb-o hoot-tooltipicon-yellow'; // fa-plus-square
							$popup_class = 'hoot-tooltip-yellow';
							break;
						case 'success': case 'ok': case 'green':
							$description_icon = 'fa fa-check-circle hoot-tooltipicon-green';
							$popup_class = 'hoot-tooltip-green';
							break;
					}
				}

				$setting['description'] = '<i class="' . $description_icon . '"></i><span class="hoot-tooltip-text ' . $popup_class . '">' . $setting['description']['text'] . '</span>';
			} else {
				$setting['description'] = '<i class="fa fa-question-circle hoot-tooltipicon-general"></i><span class="hoot-tooltip-text hoot-tooltip-general">' . $setting['description'] . '</span>';
			}

			/** Add the setting **/

			hoot_customizer_add_setting( $wp_customize, $id, $setting );

			/** Adds control **/

			switch ( $setting['type'] ) :

				/* input Text */
				case 'text':
				case 'url':
				case 'select':
				case 'radio':
				case 'checkbox':
				case 'range':
				case 'dropdown-pages':
					$wp_customize->add_control( $id, $setting );
					break;

				/* Textarea */
				// @todo remove backward compatibility in future
				case 'textarea':
					// Custom control required before WordPress 4.0
					if ( version_compare( $GLOBALS['wp_version'], '3.9.2', '<=' ) ) :
						$wp_customize->add_control(
							new Hoot_Customize_Textarea_Control( $wp_customize, $id, $setting )
						);
					else :
						$wp_customize->add_control( $id, $setting );
					endif;
					break;

				/* Color Picker */
				case 'color':
					$wp_customize->add_control(
						new WP_Customize_Color_Control( $wp_customize, $id, $setting )
					);
					break;

				/* Image Upload */
				case 'image':
					$wp_customize->add_control(
						new WP_Customize_Image_Control( $wp_customize, $id, array(
							'label'             => $setting['label'],
							'section'           => $setting['section'],
							'sanitize_callback' => $setting['sanitize_callback'],
							'priority'          => $setting['priority'],
							'active_callback'   => $setting['active_callback'],
							'description'       => $setting['description']
						) )
					);
					break;

				/* File Upload */
				case 'upload':
					$wp_customize->add_control(
						new WP_Customize_Upload_Control( $wp_customize, $id, array(
							'label'             => $setting['label'],
							'section'           => $setting['section'],
							'sanitize_callback' => $setting['sanitize_callback'],
							'priority'          => $setting['priority'],
							'active_callback'   => $setting['active_callback'],
							'description'       => $setting['description']
						) )
					);
					break;

				/* Allow custom controls to hook into interface */
				default:
					do_action( 'hoot_customizer_control_interface', $wp_customize, $id, $setting );

			endswitch;

		endif;
	endforeach;
}

add_action( 'customize_register', 'hoot_customizer_register', 99 );

/**
 * Add the customizer panels
 * 
 * @since 2.0.0
 * @param array $panels
 * @return void
 */
function hoot_customizer_add_panels( $panels, $wp_customize ) {

	$loop = 0;

	foreach ( $panels as $id => $panel ) {
		if ( ! isset( $panel['description'] ) ) {
			$panel['description'] = FALSE;
		}
		if ( ! isset( $panel['priority'] ) || ! is_numeric( $panel['priority'] ) ) {
			$loop += 10;
			$panel['priority'] = $loop;
		}
		if ( defined( 'HOOT_DEBUG' ) && true === HOOT_DEBUG )
			hoot_debug_info( "Panel [{$panel['priority']}] {$id}\n" );
		$wp_customize->add_panel( $id, $panel );
	}

}

/**
 * Add the customizer sections
 *
 * @since 2.0.0
 * @param array $sections
 * @return void
 */
function hoot_customizer_add_sections( $sections, $wp_customize ) {

	$loop = 160;

	foreach ( $sections as $id => $section ) {
		if ( ! isset( $section['description'] ) ) {
			$section['description'] = FALSE;
		}
		if ( ! isset( $section['priority'] ) || ! is_numeric( $section['priority'] ) ) {
			$loop += 10;
			$section['priority'] = $loop;
		}
		if ( defined( 'HOOT_DEBUG' ) && true === HOOT_DEBUG )
			hoot_debug_info( "Section [{$section['priority']}] {$id}\n" );
		$wp_customize->add_section( $id, $section );
	}

}

/**
 * Add the setting and proper sanitization
 *
 * @since 2.0.0
 * @param string $id
 * @param array $setting
 * @return void
 */
function hoot_customizer_add_setting( $wp_customize, $id, $setting ) {

	$setting_default = array(
		'default'              => NULL,
		'option_type'          => 'theme_mod',
		'capability'           => 'edit_theme_options',
		'theme_supports'       => NULL,
		'transport'            => NULL,
		'sanitize_callback'    => 'wp_kses_post',
		'sanitize_js_callback' => NULL
	);

	// Setting defaults
	$add_setting = array_merge( $setting_default, $setting );

	// Arguments for $wp_customize->add_setting
	$wp_customize->add_setting( $id, array(
			'default'              => $add_setting['default'],
			'type'                 => $add_setting['option_type'],
			'capability'           => $add_setting['capability'],
			'theme_supports'       => $add_setting['theme_supports'],
			'transport'            => $add_setting['transport'],
			'sanitize_callback'    => $add_setting['sanitize_callback'],
			'sanitize_js_callback' => $add_setting['sanitize_js_callback']
		)
	);

}

/**
 * Enqueue scripts to customizer screen
 *
 * @since 2.0.0
 * @return void
 */
function hoot_customizer_enqueue_scripts() {

	// Enqueue Styles
	wp_enqueue_style( 'hoot-font-awesome' );
	wp_enqueue_style( 'hoot-customizer-styles', trailingslashit( HOOTCUSTOMIZER_URI ) . 'assets/style.css', array(),  HOOT_VERSION );

	// Enqueue Scripts
	wp_enqueue_script( 'hoot-customizer-script', trailingslashit( HOOTCUSTOMIZER_URI ) . 'assets/script.js', array( 'jquery', 'wp-color-picker', 'customize-controls' ), HOOT_VERSION, true );

	// Localize Script
	$data = apply_filters( 'hoot_customizer_control_footer_js_data_object', array() );
	if ( is_array( $data ) && !empty( $data ) )
		wp_localize_script( 'hoot-customizer-script', '_hoot_customizer_data', $data );

}
// Load scripts at priority 11 so that Hoot Customizer Custom Controls have loaded their scripts
add_action( 'customize_controls_enqueue_scripts', 'hoot_customizer_enqueue_scripts', 11 );

/**
 * Add Content to Customizer Panel Footer
 *
 * @since 2.0.0
 * @return void
 */
function hoot_customizer_footer_content() {

	$hoot_customizer = Hoot_Customizer::get_instance();
	$panels = $hoot_customizer->get_options('panels');
	$infobuttons = $hoot_customizer->get_infobuttons();
	?>
	<div class="hoot-prevent-fouc">

		<div id="hoot-inav">
			<ul id="hoot-inav-icons">
				<?php
				$panels = apply_filters( 'hoot_customizer_panel_icons', $panels );
				foreach ( $panels as $id => $panel ) {
					if ( !empty( $id ) ) {
						?>
						<li id="hoot-inav-<?php echo $id ?>" class="hoot-inav-icon" data-panel="accordion-panel-<?php echo $id ?>">
							<i class="<?php if ( !empty( $panel['icon'] ) ) echo $panel['icon']; else echo 'fa fa-cog'; ?>"></i>
							<span><?php echo esc_html( $panel['title'] ) ?></span>
						</li>
						<?php
					}
				}
				?>
			</ul>
		</div>

		<a href="#" class="stretch-sidebar button-secondary" title="Stretch Sidebar">
			<span class="dashicons dashicons-image-flip-horizontal"></span>
			<span class="stretch-sidebar-label"><?php _e( 'Stretch', 'dispatch' ) ?></span>
		</a>

		<?php if ( !empty( $infobuttons ) ) : ?>
			<div id="hoot-info-buttons"><div class="hoot-info-buttons">
				<div class="hoot-info-block hoot-logo-button">
					<a href="<?php echo esc_url( THEME_AUTHOR_URI ); ?>" target="_blank"><img src="<?php echo trailingslashit( HOOT_IMAGES ) . 'logo.png'; ?>" target="_blank"></a>
				</div>
				<?php foreach ( $infobuttons as $key => $button ) { ?>
					<?php if ( isset( $button['type'] ) && $button['type']=='premium' ) : ?>
						<div class="hoot-info-block hoot-premium-button">
							<a href="<?php echo esc_url( $button['url'] ) ?>" target="_blank"><i class="<?php echo esc_attr( $button['icon'] ) ?>"></i><span class="hoot-info-button-label"><?php echo strip_tags( $button['text'], '<i>' ) ?></span></a>
						</div>
					<?php else: ?>
						<div class="hoot-info-block hoot-info-button">
							<a href="<?php echo esc_url( $button['url'] ) ?>" target="_blank"><i class="<?php echo esc_attr( $button['icon'] ) ?>"></i><span class="hoot-info-button-label hoot-tooltip-text"><?php echo strip_tags( $button['text'], '<i>' ) ?></span></a>
						</div>
					<?php endif; ?>
				<?php } ?>
			</div></div>
		<?php endif; ?>

	</div>
	<?php

}
add_action( 'customize_controls_print_footer_scripts', 'hoot_customizer_footer_content' );

/**
 * Add Content to JS object passed to hoot-customizer-script
 *
 * @since 2.0.0
 * @return void
 */
function hoot_customizer_control_js_data_object( $data ) {

	$hoot_customizer = Hoot_Customizer::get_instance();
	$panels = $hoot_customizer->get_options('panels');
	$panels = apply_filters( 'hoot_customizer_panel_icons', $panels );
	$panelicons = array();

	if ( !empty( $panels ) && is_array( $panels ) ) {
		foreach ( $panels as $id => $panel ) {
			if ( !isset( $id ) )
				continue;
			if ( isset( $panel['icon'] ) )
				$panelicons[ $id ] = $panel['icon'];
			else
				$panelicons[ $id ] = 'fa fa-cog';
		}
	}

	$data['panelicons'] = $panelicons;

	global $wp_version;
	$data['bcomp'] = ( version_compare( $wp_version, '4.3', '>=' ) ) ? 'no' : 'yes'; // Compare to 4.3 and not 4.3.0
	$data['scomp'] = ( version_compare( $wp_version, '4.3', '==' ) ) ? 'yes' : 'no'; // Compare to 4.3 and not 4.3.0

	return $data;
}
add_filter( 'hoot_customizer_control_footer_js_data_object', 'hoot_customizer_control_js_data_object' );