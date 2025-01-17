<?php
/**
 * Single listing description
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/description.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$description = ucpm_meta( 'content' );
if( empty( $description ) )
	return;
?>
<div class="description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>