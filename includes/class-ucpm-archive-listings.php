<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class ucpm_Archive_Listings extends ucpm_Search {
    private $atts;

	public function __construct() {
		add_filter('wp', array($this, 'has_shortcode'));
		add_shortcode('ucpm_archive_listings', array($this, 'ucpm_archive_listings'));
	}

	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
		global $post;
		if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'ucpm_archive_listings')) {
			add_filter('is_ucpm', array($this, 'is_ucpm'));
		}
	}

	/**
	 * Add this as a listings_wp page
	 *
	 * @param bool $return
	 * @return bool
	 */
	public function is_ucpm($return) {
		return true;
	}

	public function ucpm_archive_listings($atts) {
        $this->atts = shortcode_atts( array(
            'purpose' => '',
        ), $atts );
		
		if( !  ucpm_is_theme_compatible() ) return;
		
		ob_start();
		$archive_listings = $this->build_query();

		/**
		 * @hooked ucpm_output_content_wrapper (outputs opening divs for the content)
		 *
		 */
		do_action('ucpm_before_main_content');

		/**
		 * @hooked ucpm_listing_archive_description (displays any content, including shortcodes, within the main content editor of your chosen listing archive page)
		 *
		 */
		do_action('ucpm_archive_page_content');
		if ($archive_listings->have_posts()) :

            echo '<div class="ucpm-ordering-wrapper">';
			/**
			 * @hooked ucpm_ordering (the ordering dropdown)
			 * @hooked ucpm_pagination (the pagination)
			 *
			 */
			do_action('ucpm_before_listings_loop');
			echo '</div>';
			$default_listing_mode = ucpm_default_display_mode();
			echo '<div id="ucpm-archive-wrapper"><ul class="ucpm-items ' . esc_attr( $default_listing_mode ) . '">';
				while ($archive_listings->have_posts()) : $archive_listings->the_post();

					ucpm_get_part('content-listing.php');

				endwhile;

			echo '</ul>';
			echo '<div class="ucpm-orderby-loader"><img src="'. UCPM_PLUGIN_URL .'assets/images/loading.svg" /></div>';
			echo '</div>';

			/**
			 * @hooked ucpm_pagination (the pagination)
			 *
			 */
			do_action('ucpm_after_listings_loop');

		else :
			?>

			<p class="alert ucpm-no-results"><?php esc_html_e('Sorry, no properties were found.', 'ucpm'); ?></p>

		<?php
		endif;

        /**
         * @hooked ucpm_output_content_wrapper (outputs opening divs for the content)
         *
         */
        do_action('ucpm_after_main_content');

		return ob_get_clean();
	}

	/**
	 * The shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public function build_query() {
		$paged = ( get_query_var('paged') );
		if ( ! $paged && isset( $_GET['paged'] ) && $_GET['paged'] != '' ) {
			$paged = sanitize_text_field( $_GET['paged'] );
		} else if( $paged == 0 ) {
			$paged = 1;
		}
		$posts_per_page = ucpm_default_posts_number();
		$query_args = array(
			'post_type' => 'listing',
			'posts_per_page' => $posts_per_page,
			'post_status' => 'publish',
			'paged' => $paged,
		);

        $purpose_query = array();

		if ( isset( $this->atts['purpose'] ) ) {
            if ( strtolower($this->atts['purpose']) === 'lease' ) {
                $purpose_query[] = array(
                    array(
                        'key' => '_ucpm_listing_show_lease',
                        'value' => 'on',
                        'compare' => 'LIKE'
                    )
                );
            } elseif ( strtolower($this->atts['purpose']) === 'sale' ) {
                $purpose_query[] = array(
                    array(
                        'key' => '_ucpm_listing_show_sale',
                        'value' => 'on',
                        'compare' => 'LIKE'
                    )
                );
            }
        }

		if ( isset( $_GET['purpose'] ) && $_GET['purpose'] !== 'both' ) {
            if ( strtolower($_GET['purpose']) === 'lease' ) {
                $purpose_query[] = array(
                    array(
                        'key' => '_ucpm_listing_show_lease',
                        'value' => 'on',
                        'compare' => 'LIKE'
                    )
                );
            } elseif ( strtolower($_GET['purpose']) === 'sell' ) {
                $purpose_query[] = array(
                    array(
                        'key' => '_ucpm_listing_show_sale',
                        'value' => 'on',
                        'compare' => 'LIKE'
                    )
                );
            }
        }

        $purpose_query[] = array(
            'key'		=> '_ucpm_listing_display_property',
            'value'		=> 'on',
            'compare'	=> 'LIKE',
        );

		$meta_query = array();
		$type_query[] = self::type_meta_query();
		$keyword_query[] = self::keyword_query('');
		$ordering = self::get_ordering_args();
		$query_args['orderby'] = $ordering['orderby'];
		$query_args['order'] = $ordering['order'];
		if (isset($ordering['meta_key']) && $ordering['meta_key'] !== '') {
			$query_args['meta_key'] = $ordering['meta_key'];
		}

		$meta_query = $purpose_query;

		// this should be always set
		$query_1 = $purpose_query;

		// within radius AND purpose AND type
		$query_2 = array_merge($keyword_query);

		// if no keyword
		if (isset($_GET['location']) && empty($_GET['location'])) {
			$query_1['relation'] = 'AND';
			$meta_query[] = $query_1;
		}

		// if keyword
		if (isset($_GET['location']) && !empty($_GET['location'])) {
			$query_2['relation'] = 'OR';
			$meta_query[] = $query_1;
			$meta_query[] = $query_2;
			$meta_query['relation'] = 'AND';
		}
		if (isset($_GET['type']) && !empty($_GET['type'])) {
			$query_args['tax_query'] = $type_query;
		}

		$query_args['meta_query'] = $meta_query;

		return $archive_listings = new WP_Query($query_args);
	}

		}

return new ucpm_Archive_Listings();