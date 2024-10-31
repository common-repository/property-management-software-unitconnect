<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Work out which theme the user has active
 */
function ucpm_get_theme() {

	if (function_exists('et_divi_fonts_url')) {
		$theme = 'divi';
	} else if (function_exists('genesis_constants')) {
		$theme = 'genesis';
	} else {
		$theme = get_option('template');
	}
	return $theme;
}

/* =================================== Global =================================== */

/**
 * Output the start of the page wrapper.
 */
if (!function_exists('ucpm_output_content_wrapper')) {

	function ucpm_output_content_wrapper() {
		ucpm_get_part('global/wrapper-start.php');
	}

}

/**
 * Output the end of the page wrapper.
 */
if (!function_exists('ucpm_output_content_wrapper_end')) {

	function ucpm_output_content_wrapper_end() {
		ucpm_get_part('global/wrapper-end.php');
	}

}

/**
 * Output the end of the page wrapper.
 */
if (!function_exists('ucpm_get_sidebar')) {

	function ucpm_get_sidebar() {
		ucpm_get_part('global/sidebar.php');
	}

}

/**
 * Output the footer copyright.
 */
if (!function_exists('ucpm_footer_copyright')) {

	function ucpm_footer_copyright() {
		$show_copy = ucpm_option( 'ucpm_default_show_copy' );
		if ( $show_copy == 'yes' ) {
			ucpm_get_part('global/footer-copyright.php');
		}
	}

}

/* =================================== Single Listing =================================== */

/**
 * Output the title.
 */
if (!function_exists('ucpm_template_single_title')) {

	function ucpm_template_single_title() {
		if( ucpm_is_theme_compatible() ) return;
		ucpm_get_part('single-listing/title.php');
	}

}

/**
 * Output the address.
 */
if (!function_exists('ucpm_template_single_address')) {

	function ucpm_template_single_address() {
		if (ucpm_hide_item('address'))
			return;
		ucpm_get_part('single-listing/address.php');
	}

}

/**
 * Output the sale.
 */
if (!function_exists('ucpm_template_single_sale')) {

    function ucpm_template_single_sale() {
        if (ucpm_hide_item('sale'))
            return;
        ucpm_get_part('single-listing/sale.php');
    }

}

/**
 * Output the lease.
 */
if (!function_exists('ucpm_template_single_lease')) {

    function ucpm_template_single_lease() {
        if (ucpm_hide_item('lease'))
            return;
        ucpm_get_part('single-listing/lease.php');
    }

}

/**
 * Output the documents.
 */
if (!function_exists('ucpm_template_single_documents')) {

    function ucpm_template_single_documents() {
        if (ucpm_hide_item('documents'))
            return;
        ucpm_get_part('single-listing/documents.php');
    }

}

/**
 * Output the features.
 */
if (!function_exists('ucpm_template_single_features')) {

    function ucpm_template_single_features() {
        if (ucpm_hide_item('features'))
            return;
        ucpm_get_part('single-listing/features.php');
    }

}

/**
 * Output the contacts.
 */
if (!function_exists('ucpm_template_single_contacts')) {

    function ucpm_template_single_contacts() {
        if (ucpm_hide_item('contacts'))
            return;
        ucpm_get_part('single-listing/contacts.php');
    }

}

/**
 * Output the gallery.
 */
if (!function_exists('ucpm_template_single_gallery')) {

	function ucpm_template_single_gallery() {
		$images = ucpm_meta('image_gallery');
		if (!$images)
			return;
		ucpm_get_part('single-listing/gallery.php');
	}

}
/**
 * Output the map.
 */
if (!function_exists('ucpm_template_single_map')) {

	function ucpm_template_single_map() {
		$key = ucpm_map_key();

		if (ucpm_hide_item('map') || !$key)
			return;

		ucpm_get_part('single-listing/map.php');
	}

}

/**
 * Output the tagline.
 */
if (!function_exists('ucpm_template_single_tagline')) {

	function ucpm_template_single_tagline() {
		ucpm_get_part('single-listing/tagline.php');
	}

}

/**
 * Output the description.
 */
if (!function_exists('ucpm_template_single_description')) {

	function ucpm_template_single_description() {
		ucpm_get_part('single-listing/description.php');
	}

}

/**
 * Output the contact form.
 */
if (!function_exists('ucpm_template_single_contact_form')) {

	function ucpm_template_single_contact_form() {
		if (ucpm_hide_item('contact_form'))
			return;
		ucpm_get_part('single-listing/contact-form.php');
	}

}

/* =================================== Archive page =================================== */

add_filter('get_the_archive_title', 'ucpm_listing_display_theme_title');

function ucpm_listing_display_theme_title($title) {
	if (is_ucpm_archive()) {
		$title = ucpm_listing_archive_get_title();
	}
	return $title;
}

if (!function_exists('ucpm_listing_archive_title')) {

	function ucpm_listing_archive_title() {

		$force = ucpm_force_page_title();

		if ($force != 'yes')
			return;
		?>

		<h1 class="page-title"><?php esc_html_e(ucpm_listing_archive_get_title()); ?></h1>

		<?php
	}

}

function ucpm_listing_archive_get_title() {

	// get the title we need (search page or not)
	if (is_search()) {

		$query = isset($_GET['s']) && !empty($_GET['s']) ? ' - ' . sanitize_text_field( $_GET['s'] ) : '';
		$page_title = sprintf(__('Search Results %s', 'ucpm'), esc_html($query));

		if (get_query_var('paged'))
			$page_title .= sprintf(__('&nbsp;&ndash; Page %s', 'ucpm'), get_query_var('paged'));
	} elseif (is_ucpm_archive()) {

		$page_id = ucpm_option('archives_page');
		$page_title = get_the_title($page_id);
	} else {
		$page_title = get_the_title();
	}

	$page_title = apply_filters('ucpm_archive_page_title', $page_title);

	return $page_title;
}

/**
 * Archive page title
 *
 */
if (!function_exists('ucpm_page_title')) {

	function ucpm_page_title() {
		$page_title = ucpm_listing_archive_get_title();
		return $page_title;
	}

}

/**
 * Show the description on listings archive page
 */
if (!function_exists('ucpm_listing_archive_content')) {

	function ucpm_listing_archive_content() {
		if (is_post_type_archive('listing')) {
			$archive_page = get_post(ucpm_option('archives_page'));
			if ($archive_page) {
				$description = apply_filters('ucpm_format_archive_content', do_shortcode(shortcode_unautop(wpautop($archive_page->post_content))), $archive_page->post_content);
				if ($description) {
					echo '<div class="page-description">' . wp_kses_post( $description ) . '</div>';
				}
			}
		}
	}

}

/* =================================== Loop =================================== */

/**
 * Output listings to compare.
 */
if (!function_exists('ucpm_before_listings_loop_item_wrapper')) {

	function ucpm_before_listings_loop_item_wrapper() {
		echo '<div class="inner-container">';
	}

}

/**
 * Output listings to compare.
 */
if (!function_exists('ucpm_after_listings_loop_item_wrapper')) {

	function ucpm_after_listings_loop_item_wrapper() {
		echo '</div>';
	}

}

/**
 * Output sorting options.
 */
if (!function_exists('ucpm_ordering')) {

	function ucpm_ordering() {
		ucpm_get_part('loop/orderby.php');
	}

}

/**
 * View switcher.
 */
if (!function_exists('ucpm_view_switcher')) {

	function ucpm_view_switcher() {
		ucpm_get_part('loop/view-switcher.php');
	}

}

/**
 * Output pagination.
 */
if (!function_exists('ucpm_pagination')) {

	function ucpm_pagination() {
		ucpm_get_part('loop/pagination.php');
	}

}

/**
 * Output the title.
 */
if (!function_exists('ucpm_template_loop_title')) {

	function ucpm_template_loop_title() {
		ucpm_get_part('loop/title.php');
	}

}

/**
 * Output the address.
 */
if (!function_exists('ucpm_template_loop_address')) {

	function ucpm_template_loop_address() {
		if (ucpm_hide_item('address'))
			return;
		ucpm_get_part('loop/address.php');
	}

}

/**
 * Output the price.
 */
if (!function_exists('ucpm_template_loop_price')) {

	function ucpm_template_loop_price() {
		if (ucpm_hide_item('price'))
			return;
		ucpm_get_part('loop/price.php');
	}

}

/**
 * Output the sizes.
 */
if (!function_exists('ucpm_template_loop_sizes')) {

	function ucpm_template_loop_sizes() {
		ucpm_get_part('loop/sizes.php');
	}

}

/**
 * Output the tagline.
 */
if (!function_exists('ucpm_template_loop_tagline')) {

	function ucpm_template_loop_tagline() {
		ucpm_get_part('loop/tagline.php');
	}

}

/**
 * Output the description.
 */
if (!function_exists('ucpm_template_loop_description')) {

	function ucpm_template_loop_description() {
		ucpm_get_part('loop/description.php');
	}

}

/**
 * Output the image.
 */
if (!function_exists('ucpm_template_loop_image')) {

	function ucpm_template_loop_image() {
		ucpm_get_part('loop/image.php');
	}

}
