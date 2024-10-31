<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Enqueues the required admin scripts.
 *
 */
function ucpm_load_admin_scripts( $hook ) {
	
	$css_dir = UCPM_PLUGIN_URL . 'includes/admin/assets/css/';
	$js_dir  = UCPM_PLUGIN_URL . 'includes/admin/assets/js/';

	if ( $hook == 'profile.php' || $hook == 'user-edit.php' || is_ucpm_admin() == true ) {
		wp_enqueue_style( 'ucpm-admin', $css_dir . 'ucpm-admin.css', UCPM_VERSION );
		if( is_rtl() )
			wp_enqueue_style( 'ucpm-rtl-admin', $css_dir . 'ucpm-admin-rtl.css', UCPM_VERSION );
		/*
		 * Google map scripts
		 */
		if( ucpm_map_key() ) {
			$api_url = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places';
			$api_url = $api_url . '&key=' . ucpm_map_key();
			wp_enqueue_script( 'ucpm-google-maps', $api_url, array(), true );
			wp_enqueue_script( 'ucpm-geocomplete', $js_dir . 'jquery.geocomplete.min.js', array(), UCPM_VERSION, true );
			wp_enqueue_script( 'ucpm-geocomplete-map', $js_dir . 'ucpm-admin-geocomplete.js', array(), UCPM_VERSION, true );
		}
	}
	wp_enqueue_script( 'ucpm-admin', $js_dir . 'ucpm-admin.js', array(), UCPM_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'ucpm_load_admin_scripts', 100 );
add_action( 'customize_controls_print_styles', 'ucpm_load_admin_scripts' );