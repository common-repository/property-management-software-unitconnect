<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * The main class
 *
 * @since 1.0.0
 */
class ucpm_Post_Types {

	/**
	 * Main constructor
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct() {
		// Hook into actions & filters
		$this->hooks();
	}

	/**
	 * Hook in to actions & filters
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action('init', array($this, 'register_post_type'));
	}

	/**
	 * Registers and sets up the custom post types
	 *
	 * @since 1.0
	 * @return void
	 */
	public function register_post_type() {

		// get the slug for a single listing
		$listing_slug = ucpm_option('single_url') ? ucpm_option('single_url') : 'listing';

		$listing_labels = apply_filters('ucpm_listing_labels', array(
			'name' => _x('%2$s', 'listing post type name', 'ucpm'),
			'singular_name' => _x('%1$s', 'singular listing post type name', 'ucpm'),
			'add_new' => esc_html__('New %1s', 'ucpm'),
			'add_new_item' => esc_html__('Add New %1$s', 'ucpm'),
			'edit_item' => esc_html__('Edit %1$s', 'ucpm'),
			'new_item' => esc_html__('New %1$s', 'ucpm'),
			'all_items' => esc_html__('%2$s', 'ucpm'),
			'view_item' => esc_html__('View %1$s', 'ucpm'),
			'search_items' => esc_html__('Search %2$s', 'ucpm'),
			'not_found' => esc_html__('No %2$s found', 'ucpm'),
			'not_found_in_trash' => esc_html__('No %2$s found in Trash', 'ucpm'),
			'parent_item_colon' => '',
			'menu_name' => _x('%2$s', 'listing post type menu name', 'ucpm'),
			'filter_items_list' => esc_html__('Filter %2$s list', 'ucpm'),
			'items_list_navigation' => esc_html__('%2$s list navigation', 'ucpm'),
			'items_list' => esc_html__('%2$s list', 'ucpm'),
				));

		foreach ($listing_labels as $key => $value) {
			$listing_labels[$key] = sprintf($value, esc_html__('Property', 'ucpm'), esc_html__('Properties', 'ucpm'));
		}

		if( ucpm_is_theme_compatible() ) {
			$listing_archive = false;
		} else {
			$listing_archive = ( $archive_page = ucpm_option('archives_page') ) && get_post($archive_page) ? get_page_uri($archive_page) : 'listings';
		}

		$listing_args = array(
			'labels' => $listing_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => 'dashicons-admin-multisite',
			'menu_position' => 56,
			'query_var' => true,
			'rewrite' => array('slug' => untrailingslashit($listing_slug), 'with_front' => false, 'feeds' => true),
			'map_meta_cap' => true,
			'has_archive' => $listing_archive,
			'hierarchical' => false,
			'supports' => apply_filters('ucpm_listing_supports', array('title', 'revisions', 'author')),
            'show_in_rest' => true,
		);
		register_post_type('listing', apply_filters('ucpm_listing_post_type_args', $listing_args));

		flush_rewrite_rules(true);

		$inquiry_labels = apply_filters('ucpm_inquiry_labels', array(
			'name' => _x('%2$s', 'inquiry post type name', 'ucpm'),
			'singular_name' => _x('%1$s', 'singular inquiry post type name', 'ucpm'),
			'add_new' => esc_html__('New %1s', 'ucpm'),
			'add_new_item' => esc_html__('Add New %1$s', 'ucpm'),
			'edit_item' => esc_html__('Edit %1$s', 'ucpm'),
			'new_item' => esc_html__('New %1$s', 'ucpm'),
			'all_items' => esc_html__('%2$s', 'ucpm'),
			'view_item' => esc_html__('View %1$s', 'ucpm'),
			'search_items' => esc_html__('Search %2$s', 'ucpm'),
			'not_found' => esc_html__('No %2$s found', 'ucpm'),
			'not_found_in_trash' => esc_html__('No %2$s found in Trash', 'ucpm'),
			'parent_item_colon' => '',
			'menu_name' => _x('%2$s', 'inquiry post type menu name', 'ucpm'),
			'filter_items_list' => esc_html__('Filter %2$s list', 'ucpm'),
			'items_list_navigation' => esc_html__('%2$s list navigation', 'ucpm'),
			'items_list' => esc_html__('%2$s list', 'ucpm'),
				));

		foreach ($inquiry_labels as $key => $value) {
			$inquiry_labels[$key] = sprintf($value, esc_html__('Inquiry', 'ucpm'), esc_html__('Inquiries', 'ucpm'));
		}

		$inquiry_args = array(
			'labels' => $inquiry_labels,
			'public' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'show_in_menu' => 'edit.php?post_type=listing',
			'show_in_admin_bar' => false,
			'menu_icon' => 'dashicons-email',
			'menu_position' => 56,
			'query_var' => true,
			'capability_type' => 'post',
			'capabilities' => array(
				'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
			),
			'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			//'has_archive'			=> '',
			'hierarchical' => false,
			'supports' => apply_filters('ucpm_inquiry_supports', array('title', 'revisions')),
            'show_in_rest' => true,
		);
		register_post_type('listing-inquiry', apply_filters('ucpm_inquiry_post_type_args', $inquiry_args));

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name' => _x('Property Type', 'taxonomy general name', 'ucpm'),
			'singular_name' => _x('Property Type', 'taxonomy singular name', 'ucpm'),
			'search_items' => esc_html__('Search Property Type', 'ucpm'),
			'all_items' => esc_html__('All Types', 'ucpm'),
			'parent_item' => esc_html__('Parent Property Type', 'ucpm'),
			'parent_item_colon' => esc_html__('Parent Property Type:', 'ucpm'),
			'edit_item' => esc_html__('Edit Property Type', 'ucpm'),
			'update_item' => esc_html__('Update Property Type', 'ucpm'),
			'add_new_item' => esc_html__('Add New Property Type', 'ucpm'),
			'new_item_name' => esc_html__('New Property Type', 'ucpm'),
			'menu_name' => esc_html__('Property Type', 'ucpm'),
		);

		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'listing-type'),
			'show_in_quick_edit' => false,
			'meta_box_cb' => false
		);

		register_taxonomy('listing-type', array('listing'), $args);

        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name' => _x('Property Status', 'taxonomy general name', 'ucpm'),
            'singular_name' => _x('Property Status', 'taxonomy singular name', 'ucpm'),
            'search_items' => esc_html__('Search Property Status', 'ucpm'),
            'all_items' => esc_html__('All Types', 'ucpm'),
            'parent_item' => esc_html__('Parent Property Status', 'ucpm'),
            'parent_item_colon' => esc_html__('Parent Property Status:', 'ucpm'),
            'edit_item' => esc_html__('Edit Property Status', 'ucpm'),
            'update_item' => esc_html__('Update Property Status', 'ucpm'),
            'add_new_item' => esc_html__('Add New Property Status', 'ucpm'),
            'new_item_name' => esc_html__('New Property Status', 'ucpm'),
            'menu_name' => esc_html__('Property Status', 'ucpm'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'listing-status'),
            'show_in_quick_edit' => false,
            'meta_box_cb' => false
        );

        register_taxonomy('listing-status', array('listing'), $args);
	}

}

return new ucpm_Post_Types();