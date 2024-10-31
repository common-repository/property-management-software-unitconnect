<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

function ucpm_inquiry_meta( $meta, $post_id = 0 ) {
	if( ! $post_id )
		$post_id = get_the_ID();

	$meta_key = '_ucpm_inquiry_' . $meta;
	$data = get_post_meta( $post_id, $meta_key, true );
	return $data;
}