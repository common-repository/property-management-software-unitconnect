<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * All Stylesheets And Scripts
 * 
 * @return void
 */
function ucpm_enqueue_styles_scripts() {

	$url = UCPM_PLUGIN_URL;
	$ver = UCPM_VERSION;

	$css_dir = 'assets/css/';
	$js_dir = 'assets/js/';

	if ( is_ucpm() ) {


		wp_register_style('ucpm-lightslider', $url . $css_dir . 'lightslider.css', array(), $ver, 'all');
		wp_register_script('ucpm-lightslider', $url . $js_dir . 'lightslider.js', array('jquery'), $ver, true);

		if( is_singular( 'listing' ) ) {
			wp_enqueue_style('ucpm-lightslider');
			wp_enqueue_script('ucpm-lightslider');
		}

		wp_enqueue_script('ucpm', $url . $js_dir . 'ucpm.js', array('jquery'), $ver, true);

		/*
		 * Localize our script
		 */
		$localized_array = array();

		if (is_single_ucpm()) {
			$localized_array = array(
				'map_width' => ucpm_option('map_width'),
				'map_height' => ucpm_option('map_height'),
				'map_zoom' => ucpm_option('map_zoom'),
				'lat' => ucpm_meta('lat', ucpm_get_ID()),
				'lng' => ucpm_meta('lng', ucpm_get_ID()),
				'address' => ucpm_meta('displayed_address', ucpm_get_ID())
			);

			$slider_localize_data = array(
				'gallery_mode' => 'slide',
				'auto_slide' => true,
				'slide_delay' => 5000,
				'slide_duration' => 1500,
				'thumbs_shown' => 6,
				'gallery_prev' => '<i class="prev ucpm-icon-arrow-2"></i>',
				'gallery_next' => '<i class="next ucpm-icon-arrow-2"></i>',
			);
			wp_localize_script('ucpm-lightslider', 'ucpm_slider', apply_filters('ucpm_localized_script', $slider_localize_data));
		}
		$localized_array['ajax_url'] = admin_url('admin-ajax.php');

		wp_localize_script('ucpm', 'ucpm', apply_filters('ucpm_localized_script', $localized_array));
		wp_enqueue_style('ucpm', $url . $css_dir . 'ucpm.css', array(), $ver, 'all');
		if (is_rtl()) {
			wp_enqueue_style('ucpm-rtl', $url . $css_dir . 'ucpm-rtl.css', array(), $ver, 'all');
		}
	}
}

add_action('wp_enqueue_scripts', 'ucpm_enqueue_styles_scripts', 10);