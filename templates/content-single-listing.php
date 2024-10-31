<?php
/**
 * The Template for displaying listing content in the single-listing.php template
 *
 * This template can be overridden by copying it to yourtheme/listings/content-single-listing.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'ucpm_before_single_listing' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}

	$hide_sidebar = ucpm_option('ucpm_hide_in_content_sidebar');
	$main_class = '';
	if($hide_sidebar == 'yes') {
		$main_class = 'full-width';
	}
?>

	<div id="listing-<?php the_ID(); ?>" class="ucpm-single listing">

		<div class="main-wrap <?php echo esc_attr( $main_class ); ?>" itemscope itemtype="http://schema.org/House">

			<?php
			$images = ucpm_meta( 'image_gallery' );
			if($images) :
			?>
				<div class="image-gallery">
					<?php
					/**
					 * @hooked ucpm_template_single_gallery
					 */
					do_action( 'ucpm_single_listing_gallery' );
					?>
				</div>
			<?php endif; ?>
			<div class="summary">
				<?php
				/**
				 * @hooked ucpm_template_single_title
				 */
				do_action( 'ucpm_single_listing_summary' );
				?>
			</div>

			<div class="ucpm-content">
				<?php
				/**
				 * @hooked ucpm_template_single_tagline
				 * @hooked ucpm_template_single_description
				 */
				do_action( 'ucpm_single_listing_content' );
				?>
			</div>

		</div>
		<?php if( $hide_sidebar != 'yes' ) { ?>
			<div class="ucpm-sidebar">
				<?php
				/**
				 * @hooked ucpm_template_single_map
				 * @hooked ucpm_template_single_contact_form
				 */
				do_action( 'ucpm_single_listing_sidebar' );
				?>
			</div>
		<?php } ?>

		<?php do_action( 'ucpm_single_listing_bottom' ); ?>
	</div>

<?php do_action( 'ucpm_after_single_listing' );