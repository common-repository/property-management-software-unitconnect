<?php
if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly.

/**
 * Get the id af any item (used only to localize address for shortcodes)
 */
function ucpm_get_ID() {
	$post_id = null;

	if (!$post_id)
		$post_id = ucpm_shortcode_att('id', 'ucpm_property');

	if (!$post_id)
		$post_id = get_the_ID();

	return $post_id;
}

/**
 * Get the meta af any item
 */
function ucpm_meta($meta, $post_id = 0) {
	if (!$post_id)
		$post_id = get_the_ID();

	$meta_key = '_ucpm_listing_' . $meta;
	if( $meta == 'content' ) {
		$meta_key = $meta;
	}
	
	if ($meta == 'type' || $meta == 'listing-type') {
		$data = wp_get_post_terms($post_id, 'listing-type', array("fields" => "slugs"));
		if (!empty($data))
			$data = $data[0];
	} elseif ($meta == 'status' || $meta == 'listing-status') {
		$data = wp_get_post_terms($post_id, 'listing-status', array("fields" => "slugs"));
		if (!empty($data))
			$data = $data[0];
	} else {
		$data = get_post_meta($post_id, $meta_key, true);
	}

	return $data;
}

/**
 * Get any option
 */
function ucpm_option($option) {
	$options = get_option('ucpm_options');
	$return = isset($options[$option]) ? $options[$option] : false;
	return $return;
}

/**
 * Return an attribute value from any shortcode
 */
function ucpm_shortcode_att($attribute, $shortcode) {

	global $post;

	if (!$attribute && !$shortcode)
		return;

	if (has_shortcode($post->post_content, $shortcode)) {
		$pattern = get_shortcode_regex();
		if (preg_match_all('/' . $pattern . '/s', $post->post_content, $matches) && array_key_exists(2, $matches) && in_array($shortcode, $matches[2])) {
			$key = array_search($shortcode, $matches[2], true);

			if ($matches[3][$key]) {
				$att = str_replace($attribute . '="', "", trim($matches[3][$key]));
				$att = str_replace('"', '', $att);

				if (isset($att)) {
					return $att;
				}
			}
		}
	}
}

/**
 * is_ucpm_admin - Returns true if on a listings page in the admin
 */
function is_ucpm_admin() {
	$post_type = get_post_type();
	$screen = get_current_screen();
	$return = false;

	if (in_array($post_type, array('listing', 'listing-inquiry'))) {
		$return = true;
	}

	if (in_array($screen->id, array('listing', 'edit-listing', 'listing-inquiry', 'edit-listing-inquiry', 'listing_page_ucpm_options'))) {
		$return = true;
	}

	return apply_filters('is_ucpm_admin', $return);
}

/**
 * is_ucpm - Returns true if a page uses ucpm templates
 */
function is_ucpm() {
	$result = apply_filters('is_ucpm', ( is_ucpm_archive() || is_single_ucpm() || is_ucpm_search() || is_ucpm_active_widget() ) ? true : false );

	return $result;
}

/**
 * is_ucpm_archive - Returns true when viewing the listing type archive.
 */
if (!function_exists('is_ucpm_archive')) {

	function is_ucpm_archive() {
		return ( is_post_type_archive('listing') );
	}

}

/**
 * is_lisitng - Returns true when viewing a single listing.
 */
if (!function_exists('is_single_ucpm')) {

	function is_single_ucpm() {
		$result = false;
		if (is_singular('listing')) {
			$result = true;
		}

		return apply_filters('is_single_ucpm', $result);
	}

}
/**
 * is_lisitng - Returns true when viewing listings search results page
 */
if (!function_exists('is_ucpm_search')) {

	function is_ucpm_search() {
		if (!is_search())
			return false;
		$current_page = sanitize_post($GLOBALS['wp_the_query']->get_queried_object());
		if ($current_page)
			return $current_page->name == 'listing';

		return false;
	}

}

/**
 * is_ucpm_active_widget - Returns true when viewing ucpm widget.
 */
if (!function_exists('is_ucpm_active_widget')) {

	function is_ucpm_active_widget() {
		if ( is_active_widget('', '', 'ucpm-recent-listings') || is_active_widget('', '', 'ucpm-search-listings') || is_active_widget('', '', 'ucpm-nearby-listings')) {
			return true;
		}
		return false;
	}

}

add_action('init', 'ucpm_add_new_image_sizes', 11);

function ucpm_add_new_image_sizes() {
	add_theme_support('post-thumbnails');
	add_image_size('ucpm-lge', 800, 600, array('center', 'center')); //main
	add_image_size('ucpm-sml', 400, 300, array('center', 'center')); //thumb
}

/*
 * Run date formatting through here
 */

function ucpm_format_date($date) {
	$timestamp = strtotime($date);
	$date = date_i18n(get_option('date_format'), $timestamp, false);
	return apply_filters('ucpm_format_date', $date, $timestamp);
}

function ucpm_map_key() {
	return $key = ucpm_option('maps_api_key') ? ucpm_option('maps_api_key') : false;
}

/*
 * Build Google maps URL
 */

function ucpm_google_maps_url() {
	$api_url = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places';
	$key = ucpm_map_key();
	if ($key) {
		$api_url = $api_url . '&key=' . $key;
	}
	return $api_url;
}

/*
 * Build Google maps Geocode URL
 */

function ucpm_google_geocode_maps_url($address) {
	$api_url = "https://maps.google.com/maps/api/geocode/json?address={$address}";
	$key = ucpm_map_key();
	$country = ucpm_search_country();
	if (!empty($key)) {
		$api_url = $api_url . '&key=' . $key;
	}

	if (!empty($country)) {
		$api_url = $api_url . '&components=country:' . $country;
	}

	return apply_filters('ucpm_google_geocode_maps_url', $api_url);
}

/*
 * Get search country
 */

function ucpm_search_country() {
	$country = ucpm_option('search_country') ? ucpm_option('search_country') : '';
	return apply_filters('ucpm_search_country', $country);
}

/*
 * Get distance measurement
 */

function ucpm_distance_measurement() {
	$measurement = ucpm_option('distance_measurement') ? ucpm_option('distance_measurement') : 'kilometers';
	return apply_filters('ucpm_distance_measurement', $measurement);
}

/*
 * Get search radius
 */

function ucpm_search_radius() {
	$search_radius = ucpm_option('search_radius') ? ucpm_option('search_radius') : 20;
	return apply_filters('ucpm_search_radius', $search_radius);
}

/*
 * Validate Select value
 */

function ucpm_sanitize_select($input, $setting) {

	//input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
	$input = sanitize_key($input);

	//get the list of possible select options 
	$choices = $setting->manager->get_control($setting->id)->choices;

	//return input if valid or return default option
	return ( array_key_exists($input, $choices) ? $input : $setting->default );
}

if (!function_exists('ucpm_is_theme_compatible')) {

	function ucpm_is_theme_compatible() {
		$ucpm_theme_compatible = ucpm_option('ucpm_theme_compatibility') ? ucpm_option('ucpm_theme_compatibility') : 'enable';

		if ($ucpm_theme_compatible == 'disable')
			return false;

		return true;
	}

}

function ucpm_theme_activation($oldname, $oldtheme=false) {
	$ucpm_options = get_option('ucpm_options');
	$theme_compatible = $ucpm_options['ucpm_theme_compatibility'];
	$theme_compatible = apply_filters('ucpm_theme_compatibility', $theme_compatible);
	if( $theme_compatible ) {
		$theme_compatible = 'enable';
	} else {
		$theme_compatible = 'disable';
	}
	$ucpm_options['ucpm_theme_compatibility'] = $theme_compatible;
	update_option( 'ucpm_options', $ucpm_options );
}
add_action("after_switch_theme", "ucpm_theme_activation", 10 , 2);


add_filter('the_content', 'ucpm_overwrite_content');

if (!function_exists('ucpm_overwrite_content')) {

	function ucpm_overwrite_content($content) {

		if ( ucpm_is_theme_compatible() && is_singular('listing') ) {

			ob_start();
			/**
			 * @hooked ucpm_output_content_wrapper (outputs opening divs for the content)
			 */
			do_action('ucpm_before_main_content');

				ucpm_get_part('content-single-listing.php');

			/*
			 * @hooked ucpm_output_content_wrapper_end (outputs closing divs for the content)
			 */
			do_action('ucpm_after_main_content');

			$content = ob_get_clean();
		}

		return $content;
	}

}

if (!function_exists('ucpm_get_contextual_query')) {

	function ucpm_get_contextual_query() {
		
		global $wp_query, $post;
		static $contextual_query;
		if (!is_archive()) {

			$archive_listings_page = ucpm_option('archives_page');

			if (is_page($archive_listings_page) || has_shortcode( $post->post_content, 'ucpm_archive_listings')) {
				$contextual_query = new ucpm_Archive_Listings();
				$contextual_query = $contextual_query->build_query();
			}
			return $contextual_query;
		}

		return $wp_query;
	}

}

/*
 * Set the path to be used in the theme folder.
 * Templates in this folder will override the plugins frontend templates.
 */

function ucpm_template_path() {
	return apply_filters('ucpm_template_path', 'ucpm/');
}

function ucpm_get_part($part, $id = null) {

	if ($part) {

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
				array(
					trailingslashit(ucpm_template_path()) . $part,
					$part,
				)
		);

		// Get template from plugin directory
		if (!$template) {

			$check_dirs = apply_filters('ucpm_template_directory', array(
				UCPM_PLUGIN_DIR . 'templates/',
			));
			foreach ($check_dirs as $dir) {
				if (file_exists(trailingslashit($dir) . $part)) {
					$template = $dir . $part;
				}
			}
		}

		include( $template );
	}
}

/* Display a notice*/

add_action('admin_notices', 'wp_real_estate_admin_notice');

function wp_real_estate_admin_notice() {
    global $current_user ;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */

    /* Other notice appears right after activating */
		/* And it gets hidden after showing 3 times */
		if ( ! get_user_meta($user_id, 'wp_real_estate_ignore_notice_2') && get_option('wp_real_estate_ignore_notice_views', 0) < 3 && get_option( 'wp_real_estate_activated', 0 ) ) {
			$views = get_option('wp_real_estate_notice_views', 0);
			update_option( 'wp_real_estate_notice_views', ($views + 1) );
			echo '<div class="updated notice-info ucpm-notice" id="wprealestate-notice2" style="position:relative;">';
			echo '<p>';
			esc_html_e('Thank you for trying Property Management Software by UnitConnect. We hope you will like it.', 'ucpm');
			echo '</p>';
			echo '<a class="notice-dismiss mts-realestate-notice-dismiss" data-ignore="1" href="#"></a>';
			echo "</div>";
		}
}

add_action('wp_ajax_mts_dismiss_realestate_notice', function(){
  global $current_user;
  $user_id = $current_user->ID;
  /* If user clicks to ignore the notice, add that to their user meta */
  if ( isset($_POST['dismiss']) ) {

    if ( '0' == $_POST['dismiss'] ) {
      add_user_meta($user_id, 'wp_real_estate_ignore_notice', 'true', true);
    } elseif ( '1' == $_POST['dismiss'] ) {
      add_user_meta($user_id, 'wp_real_estate_ignore_notice_2', 'true', true);
    }
  }
});

function ucpm_check_image_file( $file ) {
	$check = false;
	$filetype = wp_check_filetype( $file );
	$valid_exts = array( 'jpg', 'jpeg', 'gif', 'png' );
	if ( in_array( strtolower( $filetype['ext'] ), $valid_exts ) ) {
		$check = true;
	}

	return $check;
}

function ucpm_download_image_file( $file, $post_id = '' ) {
	// Need to require these files
	if (!function_exists('media_handle_upload')) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
	}

	if (!empty($file) && ucpm_check_image_file( $file ) ) {
		// Download file to temp location
		$tmp = download_url($file);

		// Set variables for storage, fix file filename for query strings.
		preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $file, $matches);
		$file_array['name'] = basename($matches[0]);
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink
		if (is_wp_error($tmp)) {
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
			return false;
		}
		$desc = $file_array['name'];
		$id = media_handle_sideload($file_array, $post_id, $desc);
		// If error storing permanently, unlink
		if (is_wp_error($id)) {
			@unlink($file_array['tmp_name']);
			return false;
		}

		return $id;
	}
}

function ucpm_get_inquiries_by_email($email) {
	return get_posts(
			array (
				'posts_per_page' => -1,
				'post_type' => 'listing-inquiry',
				'meta_key' => '_ucpm_inquiry_email',
				'meta_value' => sanitize_email( $email ),
				'fields' => 'ids'
			)
		);
}