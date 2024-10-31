<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ucpm_Shortcodes {

	public function __construct() {
		add_filter( 'wp', array( $this, 'has_shortcode' ) );
		add_shortcode( 'ucpm_property', array( $this, 'property' ) );
		add_shortcode( 'ucpm_properties', array( $this, 'properties' ) );
	}

	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
			global $post;
			if ( is_a( $post, 'WP_Post' ) &&
				( has_shortcode( $post->post_content, 'ucpm_property') ||
				has_shortcode( $post->post_content, 'ucpm_properties') ||
				has_shortcode( $post->post_content, 'ucpm_search' ) ||
				has_shortcode( $post->post_content, 'ucpm_contact_form' ) )
			)
			{
				add_filter( 'is_ucpm', array( $this, 'return_true' ) );
			}

			if ( is_a( $post, 'WP_Post' ) && 
				has_shortcode( $post->post_content, 'ucpm_property') )
			{
				add_filter( 'is_single_ucpm', array( $this, 'return_true' ) );
			}
	}

	/**
	 * Add this as a ucpm page
	 *
	 * @param bool $return
	 * @return bool
	 */
	public function return_true( $return ) {
		return true;
	}

	/**
	 * Loop over found listings.
	 * @param  array $query_args
	 * @param  array $atts
	 * @param  string $loop_name
	 * @return string
	 */
	private static function listing_loop( $query_args, $atts, $loop_name ) {

		$listings = new WP_Query( apply_filters( 'ucpm_shortcode_query', $query_args, $atts, $loop_name ) );

		ob_start();

			if ( $listings->have_posts() ) { ?>

				<?php do_action( "ucpm_shortcode_before_{$loop_name}_loop" ); ?>

					<ul class="ucpm-items grid-view">

						<?php while ( $listings->have_posts() ) : $listings->the_post(); ?>

								<?php ucpm_get_part( 'content-listing.php' ); ?>

						<?php endwhile; // end of the loop. ?>

					</ul>

				<?php

				do_action( "ucpm_shortcode_after_{$loop_name}_loop" );

			} else {
				do_action( "ucpm_shortcode_{$loop_name}_loop_no_results" );
			}

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * List multiple listings shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function properties( $atts ) {
		$atts = shortcode_atts( array(
			'orderby'	=> 'date',
			'order'		=> 'asc',
			'number'	=> '10',
			'ids'		=> '',
			'compact'	=> '',
			'purpose'	=> '',
		), $atts );

		$query_args = array(
			'post_type'				=> 'listing',
			'post_status'			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby'				=> $atts['orderby'],
			'order'					=> $atts['order'],
			'posts_per_page'		=> $atts['number'],
		);

		if ( isset( $atts['purpose'] ) ) {
		    if ( strtolower($atts['purpose']) === 'lease' ) {
                $query_args['meta_query'] = array(
                    array(
                        'key' => '_ucpm_listing_show_lease',
                        'value' => 'on',
                        'compare' => 'LIKE'
                    )
                );
            } elseif ( strtolower($atts['purpose']) === 'sell' ) {
                $query_args['meta_query'] = array(
                    array(
                        'key' => '_ucpm_listing_show_sale',
                        'value' => 'on',
                        'compare' => 'LIKE'
                    )
                );
            }
        }

		if ($atts['orderby'] == 'price') {
			$query_args['meta_key'] = '_ucpm_listing_asking_price';
			$query_args['orderby'] = 'meta_value_num';
		}

		if ( ! empty( $atts['ids'] ) ) {
			$query_args['post__in'] = array_map( 'trim', explode( ',', $atts['ids'] ) );
		}

		// if we are in compact mode
		if ( ! empty( $atts['compact'] ) && $atts['compact'] == 'true' ) {
			remove_action( 'ucpm_before_listings_loop_item_wrapper', 'ucpm_before_listings_loop_item_wrapper', 10 );
			remove_action( 'ucpm_after_listings_loop_item_wrapper', 'ucpm_after_listings_loop_item_wrapper', 10 );
			
			remove_action( 'ucpm_listings_loop_item', 'ucpm_template_loop_at_a_glance', 40 );
			remove_action( 'ucpm_listings_loop_item', 'ucpm_template_loop_description', 50 );
			add_filter( 'post_class', array( __CLASS__, 'listings_compact_mode' ), 20, 3 );
		}

		return self::listing_loop( $query_args, $atts, 'listings' );
	}

	/**
	 * Add the compact class to the listings
	 */
	public static function listings_compact_mode( $classes, $class = '', $post_id = '' ) {
		$classes[] = 'compact';
		return $classes;
	}

	/**
	 * Display a single listing.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function property( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		$args = array(
			'post_type'			=> 'listing',
			'posts_per_page'	=> 1,
			'no_found_rows'		=> 1,
			'post_status'		=> 'publish',
		);

		if ( isset( $atts['id'] ) ) {
			$args['p'] = $atts['id'];
		}

		ob_start();

			$listings = new WP_Query( apply_filters( 'ucpm_shortcode_query', $args, $atts ) );

			if ( $listings->have_posts() ) : ?>

				<div id="listing-<?php the_ID(); ?>" class="ucpm-single">

					<?php while ( $listings->have_posts() ) : $listings->the_post(); ?>

						<?php ucpm_get_part( 'content-single-listing.php' ); ?>

					<?php endwhile; // end of the loop. ?>

				</div>

			<?php endif;

			wp_reset_postdata();

		return ob_get_clean();
	}

}

return new ucpm_Shortcodes();