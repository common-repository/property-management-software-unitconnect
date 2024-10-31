<?php
/**
 * loop address
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/address.php.
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
<div class="address" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
    <span itemprop="streetAddress"><?php echo esc_html( $address ); ?></span>
    <span itemprop="detailAddress"><?php echo esc_html( $city . ', ' . $state . ' ' . $zip ); ?></span>
</div>
