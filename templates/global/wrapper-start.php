<?php
/**
 * Content wrappers
 *
 * This template can be overridden by copying it to yourtheme/listings/global/wrapper-start.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ucpm_option( 'opening_html' ) ) {

	echo wp_kses_post( ucpm_option( 'opening_html' ) );

} else {

	switch( ucpm_get_theme() ) {
		case 'genesis' :
			echo '<div id="primary"><div id="content" role="main" class="ucpm-content">';
		break;
		case 'divi' :
			echo '<div id="main-content"><div class="container ucpm-content"><div id="content-area" class="clearfix"><div id="left-area">';
		break;
		case 'twentyeleven' :
			echo '<div id="primary"><div id="content" role="main" class="twentyeleven ucpm-content">';
		break;
		case 'twentytwelve' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve ucpm-content">';
		break;
		case 'twentythirteen' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen ucpm-content">';
		break;
		case 'twentyfourteen' :
			echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen ucpm-content"><div class="tfwc">';
		break;
		case 'twentyfifteen' :
			echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main ucpm-content t15wc">';
		break;
		case 'twentysixteen' :
			echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main ucpm-content" role="main">';
		break;
		case 'twentyseventeen' :
			echo '<div class="wrap"><div id="primary" class="content-area twentyseventeen"><main id="main" class="site-main ucpm-content"role="main">';
		break;
		default :
			echo apply_filters( 'ucpm_wrapper_start', '<div id="container"><div id="ucpm-content" class="ucpm-content" role="main">' );
		break;
	}

}