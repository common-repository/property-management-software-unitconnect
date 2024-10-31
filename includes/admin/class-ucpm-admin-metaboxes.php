<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('ucpm_Admin_Metaboxes')) :

	/**
	 * CMB2 Theme Options
	 * @version 0.1.0
	 */
	class ucpm_Admin_Metaboxes {

		/**
		 * Constructor
		 * @since 0.1.0
		 */
		public function __construct() {
			add_action('cmb2_admin_init', array($this, 'register_metaboxes'));
			add_filter('cmb2-taxonomy_meta_boxes', array($this, 'ucpm_property_type_metaboxes'));
		}

		/**
		 * Add the options metabox to the array of metaboxes
		 * @since  0.1.0
		 */
		public function register_metaboxes() {

			/**
			 * Load the metaboxes for property post type
			 */
			$property_metaboxes = new ucpm_Metaboxes();
			$property_metaboxes->get_instance();
		}

		/**
		 * Define the metabox and field configurations.
		 *
		 * @param  array $meta_boxes
		 * @return array
		 */
		function ucpm_property_type_metaboxes(array $meta_boxes) {

			// Start with an underscore to hide fields from custom fields list
			$prefix = '_ucpm_';

			/**
			 * Sample metabox to demonstrate each field type included
			 */
			$meta_boxes['marker_metabox'] = array(
				'id' => 'marker_image_metabox',
				'title' => esc_html__('Marker Image', 'ucpm'),
				'object_types' => array('property-type'), // Taxonomy
				'context' => 'normal',
				'priority' => 'high',
				'show_names' => true, // Show field names on the left
				// 'cmb_styles' => false, // false to disable the CMB stylesheet
				'fields' => array(
					array(
						'name' => esc_html__('Marker Image', 'ucpm'),
						'desc' => esc_html__('Upload an image or enter a URL.', 'ucpm'),
						'id' => $prefix . 'marker_image',
						'type' => 'file',
						'text' => array(
							'add_upload_files_text' => esc_html__('Add Image', 'ucpm'),
						),
					),
				),
			);

			return $meta_boxes;
		}

	}

	new ucpm_Admin_Metaboxes();

endif;