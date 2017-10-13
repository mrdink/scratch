<?php
/**
 * Scratch Theme Customizer
 *
 * @package Scratch
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function scratch_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname', array(
				'selector'        => '.site-title a',
				'render_callback' => 'scratch_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription', array(
				'selector'        => '.site-description',
				'render_callback' => 'scratch_customize_partial_blogdescription',
			)
		);
		$wp_customize->add_setting(
			'content_background_color', array(
				'default'           => '#ffffff',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize, 'content_background_color', array(
					'label'        => 'Content Background Color',
					'section'    => 'colors',
					'settings'   => 'content_background_color',
				)
			)
		);
		$wp_customize->add_section(
			'theme_options', array(
				'title'    => __( 'Theme Options', 'scratch' ),
				'priority' => 130, // Before Additional CSS.
			)
		);
		$wp_customize->add_setting(
			'page_layout', array(
				'default'           => 'boxed-layout',
				'sanitize_callback' => 'scratch_sanitize_page_layout',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'page_layout', array(
				'label'       => __( 'Page Layout', 'scratch' ),
				'section'     => 'theme_options',
				'type'        => 'radio',
				'choices'     => array(
					'boxed-layout' => __( 'Boxed', 'scratch' ),
					'fluid-layout' => __( 'Fluid', 'scratch' ),
				),
			)
		);
	}
}

add_action( 'customize_register', 'scratch_customize_register' );

/**
 * Sanitize the page layout options.
 *
 * @param string $input Page layout.
 *
 * @return string
 */
function scratch_sanitize_page_layout( $input ) {
	$valid = array(
		'boxed-layout' => __( 'Boxed', 'scratch' ),
		'fluid-layout' => __( 'Fluid', 'scratch' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	}

	return '';
}

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function scratch_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function scratch_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Output background color styles
 */
function scratch_customizer_css() {
	$content_background_color = get_theme_mod( 'content_background_color' );
	// If no custom options for site background color are set, let's bail.
	if ( '#ffffff' === $content_background_color ) {
		return;
	}

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style id="scratch-customizer-styles" type="text/css">
		.site {
			background-color: <?php echo esc_attr( $content_background_color ); ?>;
		}
	</style>
	<?php
}

add_action( 'wp_head', 'scratch_customizer_css' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function scratch_customize_preview_js() {
	wp_enqueue_script( 'scratch-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}

add_action( 'customize_preview_init', 'scratch_customize_preview_js' );
