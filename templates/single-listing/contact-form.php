<?php
/**
 * Single listing contact-form
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/contact-form.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<h2 class="widget-title"><span><?php esc_html_e( 'Contact', 'ucpm' ); ?></span></h2>
<div class="ucpm-contact-form" id="ucpm-contact">
	<div class="message-wrapper"></div>
	<?php echo do_shortcode( '[ucpm_contact_form]' ); ?>
</div>