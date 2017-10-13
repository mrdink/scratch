<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Scratch
 */

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function scratch_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}

add_action( 'wp_head', 'scratch_pingback_header' );

/**
 * Show kitchen sink by default
 *
 * @param $args
 *
 * @return mixed
 */
function scratch_unhide_kitchensink( $args ) {
	$args['wordpress_adv_hidden'] = false;

	return $args;
}

add_filter( 'tiny_mce_before_init', 'scratch_unhide_kitchensink' );

/**
 * Renames sticky class.
 *
 * @param $classes
 *
 * @return array
 */
function scratch_change_sticky_class( $classes ) {
	if ( in_array( 'sticky', $classes, true ) ) {
		$classes   = array_diff( $classes, array( 'sticky' ) );
		$classes[] = 'sticky-post';
	}

	return $classes;
}

add_filter( 'post_class', 'scratch_change_sticky_class' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function scratch_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Add class for boxed or fluid for page layouts.
	if ( get_theme_mod( 'page_layout' ) === 'fluid-layout' ) {
		$classes[] = 'fluid-layout';
	} else {
		$classes[] = 'boxed-layout';
	}

	return $classes;
}

add_filter( 'body_class', 'scratch_body_classes' );
