<?php
/**
 * Single listing address
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/address.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$address = ucpm_meta( 'displayed_address' );
$city = ucpm_meta( 'city' );
$state = ucpm_meta( 'state' );
$zip = ucpm_meta( 'zip' );

if( empty( $address ) )
	return;

?>

<div class="address sidebar-item" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
    <h6><?php esc_html_e('Address:', 'ucpm'); ?></h6>
	<span itemprop="streetAddress"><?php echo esc_html( $address ); ?></span>
	<span itemprop="detailAddress"><?php echo esc_html( $city . ', ' . $state . ' ' . $zip ); ?></span>
</div>
