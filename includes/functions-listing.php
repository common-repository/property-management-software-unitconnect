<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Type of listings to display (buy or rent).
 */
function ucpm_display() {
	$purpose = ucpm_option( 'display_purpose' );
	$default = ucpm_option( 'display_default' );

	$return = 'Sell'; // set default
	if( $purpose == 'both' ) {
		$return = $default ? $default : 'Sell';
	}
	if( $purpose == 'lease' ) {
		$return = 'Lease';
	}
	if( isset( $_GET['purpose'] ) && ! empty( $_GET['purpose'] ) ) {
		$return = sanitize_text_field( $_GET['purpose'] ) == 'buy' ? 'Sell' : 'Lease';
	}
	return apply_filters( 'ucpm_default_display', $return );
}

/**
 * Post classes for listings.
 */
 if( !function_exists( 'ucpm_listing_post_class' ) ) {

	function ucpm_listing_post_class( $classes, $class = '', $post_id = '' ) {

		if ( ! $post_id || 'listing' !== get_post_type( $post_id ) ) {
			return $classes;
		}

		$listing = get_post( $post_id );

		if ( $listing ) {

			$classes[] = 'listing';
			$classes[] = 'listing-' . $listing->ID;

			if ( ucpm_meta( 'type' ) ) {
				$classes[] = strtolower( ucpm_meta( 'type' ) );
			}

			if ( ucpm_meta( 'status' ) ) {
				$classes[] = strtolower( ucpm_meta( 'status' ) );
			}

			if ( ucpm_meta( 'purpose' ) ) {
				$classes[] = strtolower( ucpm_meta( 'purpose' ) );
			}

			$images = ucpm_meta( 'image_gallery' );
			if ( $images ) {
				foreach ( $images as $key => $url ) {
					if( ! empty( $url ) ) {
						$classes[] = strtolower( 'has-thumbnail' );
						break;
					}
				}
			}

		}

		if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
			unset( $classes[ $key ] );
		}

		return $classes;
	}
}
/*
 * Show Archive Page title within page content area
 */
if( !function_exists( 'ucpm_force_page_title' ) ) {
	function ucpm_force_page_title() {
		$force = ucpm_option( 'archives_page_title' ) ? ucpm_option( 'archives_page_title' ) : 'no';
		return $force;
	}
}
/*
 * Map height
 */
function ucpm_map_height() {
	$height = ucpm_option( 'map_height' ) ? ucpm_option( 'map_height' ) : '200';
	return apply_filters( 'ucpm_map_height', $height );
}

/*
 * Are we hiding an item
 */
function ucpm_hide_item( $item ) {
	$hide = ucpm_meta( 'hide' );
	if( ! $hide ) {
		return false;
	}
	return in_array( $item, $hide );
}

/*
 * Output the chosen tick
 */
function ucpm_tick() {
	return '<i class="ucpm-icon-tick-7"></i>';
}

/*
 * Get the URL of the first image of a listing
 */
function ucpm_get_first_image( $post_id = 0 ) {

	if( ! $post_id )
		$post_id = get_the_ID();

	$gallery = ucpm_meta( 'image_gallery', $post_id );

	if( empty( $gallery ) ) {
		$sml 	= apply_filters( 'ucpm_default_no_image', UCPM_PLUGIN_URL . 'assets/images/no-image.jpg' );
		$alt 	= '';
	} else {
		$id 	= key( $gallery );
		$sml 	= wp_get_attachment_image_url( $id, 'ucpm-sml' );
		$alt 	= get_post_meta( $id, '_wp_attachment_image_alt', true );
	}

	return array(
		'alt' => $alt,
		'sml' => $sml,
	);
}

/*
 * Get the listing status
 */
 if( !function_exists( 'ucpm_get_status' ) ) {

	function ucpm_get_status() {

		$listing_status     = ucpm_meta( 'status' );
		$option_statuses    = ucpm_option( 'listing_status' );

		if( ! $listing_status )
			return;

		$status = null;
		foreach ($option_statuses as $option_status) {
			$status_slug = strtolower( str_replace( ' ', '-', $option_status) );
			if( $listing_status == $status_slug ) {
				$status = isset( $option_status ) ? $option_status : null;
				if( $status ) {


					$status_bg_color = '';
					$status_text_color = '';
					$status_icon_class = '';

					$bg_color = $text_color = $icon = null;

					if($status_bg_color)
						$bg_color = $status_bg_color;

					if($status_text_color)
						$text_color = $status_text_color;

					if($status_icon_class)
						$icon = $status_icon_class;
				}
			}
		}

		if( ! $status ) {
			$status 	= $listing_status;
			$bg_color 	= '#fff';
			$text_color = '#444';
			$icon 		= '';
		}

		return array(
			'status'		=> $status,
			'bg_color'		=> $bg_color,
			'text_color'	=> $text_color,
			'icon'			=> $icon,
		);
	}
}
/**
 * Do we include the decimals
 * @since  1.0.0
 * @return string
 */
function ucpm_include_decimals() {
	return 'no';
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
 if( !function_exists( 'ucpm_format_price_format' ) ) {

		function ucpm_format_price_format() {
			$currency_pos = 'left';
			$format = '%1$s%2$s';

			return apply_filters( 'ucpm_format_price_format', $format, $currency_pos );
		}

	}

/**
 * Return the currency_symbol for prices.
 * @since  1.0.0
 * @return string
 */
function ucpm_currency_symbol() {
	return '$';
}

/**
 * Return the thousand separator for prices.
 * @since  1.0.0
 * @return string
 */
function ucpm_thousand_separator() {
	return ',';
}

/**
 * Return the decimal separator for prices.
 * @since  1.0.0
 * @return string
 */
function ucpm_decimal_separator() {
	return '.';
}

/**
 * Return the number of decimals after the decimal point.
 * @since  1.0.0
 * @return int
 */
function ucpm_decimals() {
	return 2;
}

/**
 * Trim trailing zeros off prices.
 *
 * @param mixed $price
 * @return string
 */
function ucpm_trim_zeros( $price ) {
	return preg_replace( '/' . preg_quote( ucpm_decimal_separator(), '/' ) . '0++$/', '', $price );
}

/**
 * Format the price with a currency symbol.
 *
 * @param float $price
 * @param array $args (default: array())
 * @return string
 */
function ucpm_format_price( $price, $args = array() ) {
	extract( apply_filters( 'ucpm_format_price_args', wp_parse_args( $args, array(
		'currency_symbol'		=> ucpm_currency_symbol(),
		'decimal_separator'		=> ucpm_decimal_separator(),
		'thousand_separator'	=> ucpm_thousand_separator(),
		'decimals'				=> ucpm_decimals(),
		'price_format'			=> ucpm_format_price_format(),
		'include_decimals'		=> ucpm_include_decimals()
	) ) ) );

	$return = null;
	if( $price != 0 ) {
		$negative	= $price < 0;
		$price		= apply_filters( 'ucpm_raw_price', floatval( $negative ? $price * -1 : $price ) );
		$price		= apply_filters( 'ucpm_formatted_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

		if ( $include_decimals == 'no' ) {
			$price = ucpm_trim_zeros( $price );
		}

		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<span class="currency-symbol">' . esc_html( $currency_symbol ) . '</span>', $price );
		$return = '<span class="price-amount">' . $formatted_price . '</span>';
	}

	return apply_filters( 'ucpm_format_price', $return, $price, $args );
}

/**
 * Format the price with a currency symbol.
 *
 * @param float $price
 * @param array $args (default: array())
 * @return string
 */
function ucpm_raw_price( $price ) {
	return strip_tags( ucpm_format_price( $price ) );
}

/*
 * Outputs the price HTML
 */
function ucpm_price( $price ) {
	$suffix = ucpm_meta( 'price_suffix' );
	return ucpm_format_price( $price ) . ' ' . $suffix;
}

add_filter( 'cmb2_override__ucpm_listing_agent_meta_save', 'ucpm_cpt_author_meta_save_override', 0, 4 );
/**
 * Override CPT author meta save in order to store as post author
 */
function ucpm_cpt_author_meta_save_override( $override, $data_args, $args, $field ) {
	// Checks to avoid infinite loops
	// @link	https://codex.wordpress.org/Function_Reference/wp_update_post#Caution_-_Infinite_loop
	if ( ! wp_is_post_revision( $data_args['id'] ) ) {
		// Remove filter to avoid loop
		remove_filter( 'cmb2_override__ucpm_listing_agent_meta_save', 'ucpm_cpt_author_meta_save_override', 0 );
		// Update post author
		// Will return non-null value to short-circuit normal meta save

		$prev_listing_author = get_post_meta( $data_args['id'], '_ucpm_listing_agent', true );
		$prev_author_listings = get_user_meta( $prev_listing_author, '_ucpm_listing_ids', true);
		if( !empty( $prev_author_listings ) && in_array( $data_args['id'], $prev_author_listings ) ) {
			if(($key = array_search($data_args['id'], $prev_author_listings)) !== false) {
				unset($prev_author_listings[$key]);
			}
			if( empty($prev_author_listings) )
				$prev_author_listings = '';

			update_user_meta( $prev_listing_author, '_ucpm_listing_ids', $prev_author_listings );
		}
		$override = wp_update_post( array(
			'ID'			=> $data_args['id'],
			'post_author'	=> $data_args['value'],
		));

		$current_author_listings = get_user_meta( $data_args['value'], '_ucpm_listing_ids', true);
		if( ! is_array( $current_author_listings ) )
			$current_author_listings = array();

		array_push($current_author_listings, $data_args['id']);
		update_user_meta( $data_args['value'], '_ucpm_listing_ids', $current_author_listings );
		// Add filter back
		add_filter( 'cmb2_override__ucpm_listing_agent_meta_save', 'ucpm_cpt_author_meta_save_override', 0 );
	}
	return $override;
}

function ucpm_default_display_mode() {
	return $default_listing_mode = ucpm_option( 'ucpm_default_display_mode' ) ? ucpm_option( 'ucpm_default_display_mode' ) : 'grid-view';
}

function ucpm_default_posts_number() {
	return $default_property_number = ucpm_option( 'archive_property_number' ) ? ucpm_option( 'archive_property_number' ) : 10;
}

function ucpm_default_grid_columns() {
	return $default_columns = ucpm_option( 'ucpm_grid_columns' ) ? ucpm_option( 'ucpm_grid_columns' ) : 3;
}
