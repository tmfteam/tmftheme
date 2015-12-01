<?php
/**
 * WordPress.com-specific functions and definitions.
 *
 * This file is centrally included from `wp-content/mu-plugins/wpcom-theme-compat.php`.
 *
 * @package TMF
 * @since TMF 1.0
 */

/**
 * Adds support for wp.com-specific theme functions.
 *
 * @global array $themecolors
 */
function tmf_wpcom_setup() {
	global $themecolors;

	// Set theme colors for third party services.
	if ( ! isset( $themecolors ) ) {
		$themecolors = array(
			'bg'     => '#888',
			'border' => '#ccc',
			'text'   => '#767676',
			'link'   => '#00f',
			'url'    => '',
		);
	}
}
add_action( 'after_setup_theme', 'tmf_wpcom_setup' );
