<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

add_filter('post_class', 'ucpm_listing_post_class', 20, 3);

/**
 * Content Wrappers.
 *
 */
add_action('ucpm_before_main_content', 'ucpm_output_content_wrapper', 10);
add_action('ucpm_after_main_content', 'ucpm_output_content_wrapper_end', 10);

/**
 * Footer copyright.
 */
add_action('ucpm_after_main_content', 'ucpm_footer_copyright', 10);
add_action('ucpm_after_listings_loop', 'ucpm_footer_copyright', 10);

/**
 * Add recaptcha to contact form.
 */
function ucpm_add_recaptcha() {
    echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
}
add_action('wp_footer', 'ucpm_add_recaptcha', 10);

/**
 * Sidebar.
 *
 */
add_action('ucpm_sidebar', 'ucpm_get_sidebar', 10);

/**
 * Before listings
 *
 */
add_action('ucpm_archive_page_content', 'ucpm_listing_archive_title', 10);
add_action('ucpm_archive_page_content', 'ucpm_listing_archive_content', 20);

add_action('ucpm_before_listings_loop', 'ucpm_ordering', 10);
add_action('ucpm_before_listings_loop', 'ucpm_view_switcher', 20);

add_action('ucpm_after_listings_loop', 'ucpm_pagination', 10);

/**
 * Listing Loop Items.
 *
 */
add_action('ucpm_before_listings_loop_item_summary', 'ucpm_template_loop_image', 10);
add_action('ucpm_before_listings_loop_item_wrapper', 'ucpm_before_listings_loop_item_wrapper', 10);
add_action('ucpm_after_listings_loop_item_wrapper', 'ucpm_after_listings_loop_item_wrapper', 10);

add_action('ucpm_listings_loop_item', 'ucpm_template_loop_title', 10);
add_action('ucpm_listings_loop_item', 'ucpm_template_loop_description', 20);
add_action('ucpm_listings_loop_item', 'ucpm_template_loop_address', 30);

/**
 * Single Listing
 *
 */
add_action('ucpm_single_listing_gallery', 'ucpm_template_single_gallery', 10);

add_action('ucpm_single_listing_summary', 'ucpm_template_single_title', 10);

add_action('ucpm_single_listing_content', 'ucpm_template_single_tagline', 10);
add_action('ucpm_single_listing_content', 'ucpm_template_single_description', 20);
add_action('ucpm_single_listing_content', 'ucpm_template_single_sale', 30);
add_action('ucpm_single_listing_content', 'ucpm_template_single_lease', 40);
add_action('ucpm_single_listing_content', 'ucpm_template_single_documents', 50);
add_action('ucpm_single_listing_content', 'ucpm_template_single_map', 60);
add_action('ucpm_single_listing_content', 'ucpm_template_single_contact_form', 70);

add_action('ucpm_single_listing_sidebar', 'ucpm_template_single_address', 10);
add_action('ucpm_single_listing_sidebar', 'ucpm_template_single_features', 20);
add_action('ucpm_single_listing_sidebar', 'ucpm_template_single_contacts', 30);
