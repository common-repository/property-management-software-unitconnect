<?php
/**
 * The Template for displaying the listings archive
 *
 * This template can be overridden by copying it to yourtheme/listings/archive-listing.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'listings' ); 

	/**
	 * @hooked ucpm_output_content_wrapper (outputs opening divs for the content)
	 *
	 */
	do_action( 'ucpm_before_main_content' );

		/**
		 * @hooked ucpm_listing_archive_description (displays any content, including shortcodes, within the main content editor of your chosen listing archive page)
		 *
		 */
		do_action( 'ucpm_archive_page_content' );

		if ( have_posts() ) :
			$default_listing_mode = ucpm_default_display_mode();

		    echo '<div class="ucpm-ordering-wrapper">';
			/**
			 * @hooked ucpm_ordering (the ordering dropdown)
			 *
			 */
			do_action( 'ucpm_before_listings_loop' );
			echo '</div>';
			echo '<div id="ucpm-archive-wrapper">';
			echo '<ul class="ucpm-items '. esc_attr( $default_listing_mode ) .'">';
				while ( have_posts() ) : the_post();

					ucpm_get_part( 'content-listing.php' );

				endwhile;
			echo '</ul>';
				echo '<div class="ucpm-orderby-loader"><img src="'. UCPM_PLUGIN_URL .'assets/images/loading.svg" /></div>';
			echo '</div>';

			/**
			 * @hooked ucpm_pagination (the pagination)
			 *
			 */
			do_action( 'ucpm_after_listings_loop' );

		else : ?>

			<p class="alert ucpm-no-results"><?php esc_html_e( 'Sorry, no properties were found.', 'ucpm' ); ?></p>

		<?php endif;

	/**
	 * @hooked ucpm_output_content_wrapper_end (outputs closing divs for the content)
	 *
	 */
	do_action( 'ucpm_after_main_content' );
	do_action( 'ucpm_sidebar' );
get_footer( 'listings' );