<?php
/**
 * Single listing tagline
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/tagline.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$lat = ucpm_meta( 'lat' );
$lng = ucpm_meta( 'lng' );

if( empty( $lat ) && empty( $lng ) )
	return;
?>

<h2 class="widget-title"><span><?php esc_html_e( 'Map', 'ucpm' ); ?></span></h2>
<div id="ucpm-map" class="ucpm-map" width="500" height="<?php echo esc_attr( ucpm_map_height() ); ?>" style="height:<?php echo esc_attr( ucpm_map_height() ); ?>px;"></div>