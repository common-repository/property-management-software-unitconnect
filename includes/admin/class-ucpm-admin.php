<?php
/**
 * ucpm Admin
 *
 * @class    ucpm_Admin
 * @author   ucpm
 * @category Admin
 * @package  ucpm/Admin
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * ucpm_Admin class.
 */
class ucpm_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('init', array($this, 'includes'));
		add_filter('admin_body_class', array($this, 'admin_body_class'));
		add_action('after_wp_tiny_mce', array($this, 'ucpm_tinymce_extra_vars'));
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {

		// option pages
		include_once( 'class-ucpm-admin-options.php' );

		// metaboxes
		include_once( 'class-ucpm-admin-metaboxes.php' );
		include_once( 'metaboxes/class-ucpm-metaboxes.php' );
		include_once( 'metaboxes/functions.php' );

		include_once( 'class-ucpm-admin-enqueues.php' );
		include_once( 'class-ucpm-admin-menu.php' );
		include_once( 'class-ucpm-admin-columns.php' );
		include_once( 'class-ucpm-admin-inquiry-columns.php' );

		//Abort early if the user will never see TinyMCE
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
			return;

		//Add a callback to regiser our tinymce plugin   
		add_filter("mce_external_plugins", array($this, 'ucpm_register_tinymce_plugin'));

		// Add a callback to add our button to the TinyMCE toolbar
		add_filter('mce_buttons', array($this, 'ucpm_add_tinymce_dropdown'));
	}

	/**
	 * Adds one or more classes to the body tag in the dashboard.
	 *
	 * @param  String $classes Current body classes.
	 * @return String          Altered body classes.
	 */
	public function admin_body_class($classes) {

		if (is_ucpm_admin()) {
			return "$classes ucpm";
		}
	}

	//This callback registers our plug-in
	public function ucpm_register_tinymce_plugin($plugin_array) {
		$js_dir = UCPM_PLUGIN_URL . 'includes/admin/assets/js/';
		$plugin_array['ucpm_shortcodes_dropdown'] = $js_dir . 'ucpm-shortcodes.js';
		return $plugin_array;
	}

	//This callback adds our button to the toolbar
	public function ucpm_add_tinymce_dropdown($buttons) {
		$buttons[] = "ucpm_shortcodes_dropdown";
		return $buttons;
	}

	public function ucpm_tinymce_extra_vars() {
		$listing_meta_fields_values = array();
		$args = array(
			'posts_per_page' => -1,
			'offset' => 0,
			'post_type' => 'listing',
			'post_status' => 'publish',
		);
		$listings = get_posts($args);

		if (!empty($listings)) {
			foreach ($listings as $listing) {
				$listing_meta_fields_values[] = array('text' => $listing->post_title, 'value' => $listing->ID);
			}
		}

		$listing_meta_fields = array(
			'id' => array(
				'type' => 'listbox',
				'label' => esc_html__('Listing', 'ucpm'),
				'value' => '',
				'tooltip' => esc_html__('The ID of the listing you want to display.', 'ucpm'),
				'values' => $listing_meta_fields_values,
			)
		);

		$search_fields = array(
			'placeholder' => array(
				'type' => 'textbox',
				'label' => esc_html__('Placeholder', 'ucpm'),
				'value' => esc_html__('Address, Suburb, Region, Zip or Landmark', 'ucpm'),
				'tooltip' => esc_html__('Text to display as the placeholder text in the text input', 'ucpm')
			),
			'submit_btn' => array(
				'type' => 'textbox',
				'label' => esc_html__('Submit Button', 'ucpm'),
				'value' => esc_html__('Search', 'ucpm'),
				'tooltip' => esc_html__('Text to display on the search button', 'ucpm')
			),
			'exclude' => array(
				'type' => 'textbox',
				'label' => esc_html__('Exclude (Comma separated list of fields)', 'ucpm'),
				'value' => 'type',
				'tooltip' => esc_html__('Comma separated list of fields that you don\'t want to include on the search box.', 'ucpm')
			),
			'show_map' => array(
				'type' => 'listbox',
				'label' => esc_html__('Show Map', 'ucpm'),
				'values' => [
					array('text' => esc_html__('Yes', 'ucpm'), 'value' => 'yes'),
					array('text' => esc_html__('No', 'ucpm'), 'value' => 'no')
				],
				'value' => 'no',
			),
		);

		$nearby_listings_fields = array(
			'distance' => array(
				'type' => 'listbox',
				'label' => esc_html__('Distance', 'ucpm'),
				'values' => [
					array('text' => esc_html__('Miles', 'ucpm'), 'value' => 'miles'),
					array('text' => esc_html__('Kilometers', 'ucpm'), 'value' => 'kilometers')
				],
				'value' => 'miles',
				'tooltip' => esc_html__('Choose miles or kilometers for the radius.', 'ucpm')
			),
			'radius' => array(
				'type' => 'textbox',
				'label' => esc_html__('Radius', 'ucpm'),
				'value' => 50,
				'tooltip' => esc_html__('Show listings that are within this distance (mi or km as selected above).', 'ucpm')
			),
			'view' => array(
				'type' => 'listbox',
				'label' => esc_html__('List View', 'ucpm'),
				'values' => [
					array('text' => esc_html__('List View', 'ucpm'), 'value' => 'list-view'),
					array('text' => esc_html__('Grid View', 'ucpm'), 'value' => 'grid-view')
				],
				'value' => 'list-view',
			),
			'columns' => array(
				'type' => 'listbox',
				'label' => esc_html__('Number of Columns', 'ucpm'),
				'values' => [
					array('text' => esc_html__('2 columns', 'ucpm'), 'value' => '2'),
					array('text' => esc_html__('3 columns', 'ucpm'), 'value' => '3'),
					array('text' => esc_html__('4 columns', 'ucpm'), 'value' => '4')
				],
				'value' => '2',
				'tooltip' => esc_html__('The number of columns to display, when viewing listings in grid mode.', 'ucpm'),
			),
			'compact' => array(
				'type' => 'listbox',
				'label' => esc_html__('Compact', 'ucpm'),
				'values' => [
					array('text' => esc_html__('True', 'ucpm'), 'value' => 'true'),
					array('text' => esc_html__('False', 'ucpm'), 'value' => 'false')
				],
				'value' => 'true',
			),
			'number' => array(
				'type' => 'textbox',
				'label' => esc_html__('Number of listings to show', 'ucpm'),
				'value' => 10,
				'tooltip' => esc_html__('Number of listings to show', 'ucpm')
			)
		);

		$listings_fields = array(
			'orderby' => array(
				'type' => 'listbox',
				'label' => esc_html__('OrderBy', 'ucpm'),
				'values' => [
					array('text' => esc_html__('Date', 'ucpm'), 'value' => 'date'),
					array('text' => esc_html__('Title', 'ucpm'), 'value' => 'title'),
					array('text' => esc_html__('Price', 'ucpm'), 'value' => 'price'),
				],
				'value' => 'date'
			),
			'order' => array(
				'type' => 'listbox',
				'label' => esc_html__('Order', 'ucpm'),
				'values' => [
					array('text' => esc_html__('Asc', 'ucpm'), 'value' => 'asc'),
					array('text' => esc_html__('Desc', 'ucpm'), 'value' => 'desc')
				],
				'value' => 'asc'
			),
			'number' => array(
				'type' => 'textbox',
				'label' => esc_html__('Number of listings to show', 'ucpm'),
				'value' => 10,
				'tooltip' => esc_html__('Number of listings to show', 'ucpm')
			),
			'ids' => array(
				'type' => 'textbox',
				'label' => esc_html__('Listing Ids (only show these listings)', 'ucpm'),
				'value' => '',
				'tooltip' => esc_html__('Comma seperated ids of Listings to display on front-end', 'ucpm')
			),
			'compact' => array(
				'type' => 'listbox',
				'label' => esc_html__('Compact', 'ucpm'),
				'values' => [
					array('text' => esc_html__('True', 'ucpm'), 'value' => 'true'),
					array('text' => esc_html__('False', 'ucpm'), 'value' => 'false')
				],
				'value' => 'true',
			)
		);

		$status_data[] = array('text' => esc_html__('Select Status', 'ucpm'), 'value' => '');
		$listing_statuses = ucpm_option('listing_status');
		if (!empty($listing_statuses)) {
			foreach ($listing_statuses as $listing_status) {
				$status_slug = strtolower(str_replace(' ', '-', $listing_status));
				$status_data[] = array('text' => $listing_status, 'value' => $status_slug);
			}
		}
		$listing_types = get_terms('listing-type', array('fields' => 'id=>name'));
		$types_data[] = array('text' => esc_html__('Select Type', 'ucpm'), 'value' => '');
		if (!empty($listing_types)) {
			foreach ($listing_types as $key => $listing_type) {
				$types_data[] = array('text' => $listing_type, 'value' => $key);
			}
		}
		$map_fields = array(
			'number' => array(
				'type' => 'textbox',
				'label' => esc_html__('Number of listings to show', 'ucpm'),
				'value' => 100,
				'tooltip' => esc_html__('Number of listings to show', 'ucpm')
			),
			'include' => array(
				'type' => 'textbox',
				'label' => esc_html__('Include Ids (only show these listings)', 'ucpm'),
				'value' => '',
				'tooltip' => esc_html__('Comma seperated ids of Listings to display', 'ucpm')
			),
			'exclude' => array(
				'type' => 'textbox',
				'label' => esc_html__('Exclude Ids (exclude these listings)', 'ucpm'),
				'value' => '',
				'tooltip' => esc_html__('Comma seperated ids of Listings to exclude', 'ucpm')
			),
			'purpose' => array(
				'type' => 'listbox',
				'label' => esc_html__('Listings Purpose', 'ucpm'),
				'values' => [
					array('text' => esc_html__('Both', 'ucpm'), 'value' => ''),
					array('text' => esc_html__('Lease', 'ucpm'), 'value' => 'lease'),
					array('text' => esc_html__('Sell', 'ucpm'), 'value' => 'sell')
				],
				'value' => ''
			),
			'status' => array(
				'type' => 'listbox',
				'label' => esc_html__('Listings Status', 'ucpm'),
				'values' => $status_data,
				'value' => ''
			),
			'type' => array(
				'type' => 'listbox',
				'label' => esc_html__('Listings Type', 'ucpm'),
				'values' => $types_data,
				'value' => ''
			),
			'relation' => array(
				'type' => 'listbox',
				'label' => esc_html__('Relation', 'ucpm'),
				'values' => [
					array('text' => esc_html__('AND', 'ucpm'), 'value' => 'and'),
					array('text' => esc_html__('OR', 'ucpm'), 'value' => 'or')
				],
				'value' => 'and',
				'tooltip' => esc_html__('This is the relationship between purpose, and status.', 'ucpm')
			),

			'divider' => array(
				'type' => 'container',
				'label' => '',
				'value' => esc_html__('Map Settings', 'ucpm')
			),
			
			'height ' => array(
				'type' => 'textbox',
				'label' => esc_html__('Height (Height of the map in pixels.)', 'ucpm'),
				'value' => 400,
				'tooltip' => esc_html__('Height of the map in pixels.', 'ucpm')
			),
			
			'fit' => array(
				'type' => 'listbox',
				'label' => esc_html__('Fit', 'ucpm'),
				'values' => [
					array('text' => esc_html__('TRUE', 'ucpm'), 'value' => 'true'),
					array('text' => esc_html__('FALSE', 'ucpm'), 'value' => 'false')
				],
				'value' => 'true',
				'tooltip' => esc_html__(' This will automatically adjust center and zoom so that all listings fit within the map viewport.', 'ucpm')
			),
			'zoom' => array(
				'type' => 'textbox',
				'label' => esc_html__('Zoom (A number between 1-20)', 'ucpm'),
				'value' => 13,
				'tooltip' => esc_html__('A number between 1-20. Only works if fit is set to false.', 'ucpm')
			),
			'center ' => array(
				'type' => 'textbox',
				'label' => esc_html__('Center (Latitude and longitude of the center of the map)', 'ucpm'),
				'value' => '35.652832, 139.839478',
				'tooltip' => esc_html__('Comma seperated lat and lng. Only works if fit is set to false', 'ucpm')
			),
			'search' => array(
				'type' => 'listbox',
				'label' => esc_html__('Search', 'ucpm'),
				'values' => [
					array('text' => esc_html__('TRUE', 'ucpm'), 'value' => 'true'),
					array('text' => esc_html__('FALSE', 'ucpm'), 'value' => 'false')
				],
				'value' => 'true',
				'tooltip' => esc_html__(' Show the search box or not on the map.', 'ucpm')
			),
			'search_zoom ' => array(
				'type' => 'textbox',
				'label' => esc_html__('Search Zoom (A number between 1-20)', 'ucpm'),
				'value' => 13,
				'tooltip' => esc_html__('A number between 1-20. Only works if search is set to false.', 'ucpm')
			),
		);
		?>
		<script type="text/javascript">
			var ucpm_tinyMCE_object = <?php
		echo json_encode(
				array(
					'listing_fields' => $listing_meta_fields,
					'search_fields' => $search_fields,
					'nearby_listing_fields' => $nearby_listings_fields,
					'listings_fields' => $listings_fields,
					'ucpm_map_fields' => $map_fields
				)
		);
		?>
		</script><?php
	}

}

return new ucpm_Admin();
