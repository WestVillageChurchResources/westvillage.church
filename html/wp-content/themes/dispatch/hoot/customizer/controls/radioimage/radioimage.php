<?php
/**
 * Customize for radioimage, extend the WP customizer
 *
 * @package hoot
 * @subpackage hoot-customizer
 * @since hoot 2.0.0
 */

/**
 * Radioimage Control Class extends the WP customizer
 *
 * @since 2.0.0
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
class Hoot_Customize_Radioimage_Control extends WP_Customize_Control {

	/**
	 * @since 2.0.0
	 * @access public
	 * @var string
	 */
	public $type = 'radioimage';

	/**
	 * Enqueue scripts/styles for the radioimage.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'hoot-customizer-controls-radioimage', trailingslashit( HOOTCUSTOMIZER_URI ) . "controls/radioimage/script.js", array( 'jquery', 'customize-controls' ), HOOT_VERSION, true );
		wp_enqueue_style( 'hoot-customizer-controls-radioimage', trailingslashit( HOOTCUSTOMIZER_URI ) . "controls/radioimage/style.css", array(),  HOOT_VERSION );
	}

	/**
	 * Render the control's content.
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function render_content() {

		switch ( $this->type ) {

			case 'radioimage' :
				if ( empty( $this->choices ) )
					return;

				$name = '_customize-radio-' . $this->id;

				if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;

				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description ; ?></span>
				<?php endif;

				foreach ( $this->choices as $value => $image ) :
					$checked = checked( $this->value(), $value, false );
					?>
					<label class="hoot-customize-radioimage<?php if ($checked) echo ' radiocheck' ?>">
						<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); echo $checked; ?> />
						<img src="<?php echo esc_url( $image ); ?>" />
					</label>
					<?php
				endforeach;

				break;

		}

	}

}
endif;

/**
 * Hook into control display interface
 *
 * @since 2.0.0
 * @param object $wp_customize
 * @param string $id
 * @param array $setting
 * @return void
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
function hoot_customizer_radioimage_control_interface ( $wp_customize, $id, $setting ) {
	if ( isset( $setting['type'] ) ) :
		if ( $setting['type'] == 'radioimage' ) {
			$wp_customize->add_control(
				new Hoot_Customize_Radioimage_Control( $wp_customize, $id, $setting )
			);
		}
	endif;
}
add_action( 'hoot_customizer_control_interface', 'hoot_customizer_radioimage_control_interface', 10, 3 );
endif;

/**
 * Add sanitization function
 *
 * @since 2.0.0
 * @param string $name
 * @param string $type
 * @param array $setting
 * @return string
 */
function hoot_customizer_radioimage_sanitization_function( $name, $type, $setting ) {
	if ( $type == 'radioimage' )
		$name = 'hoot_customizer_sanitize_choices';
	return $name;
}
add_filter( 'hoot_customizer_sanitization_function', 'hoot_customizer_radioimage_sanitization_function', 5, 3 );