<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class ucpm_Maps_Shortcodes {

	public function __construct() {

		add_filter('wp', array($this, 'has_shortcode'));
		add_shortcode('ucpm_map', array($this, 'ucpm_map'));
		add_action( 'wp_enqueue_scripts', array($this, 'ucpm_enqueue_map_scripts') );
	}
	
	public function ucpm_enqueue_map_scripts() {
		/*
		 * Google map scripts
		 */
		$key = ucpm_map_key();
		$api_url = ucpm_google_maps_url();
		if (!empty($key)) {
			
			$url = UCPM_PLUGIN_URL;
			$ver = UCPM_VERSION;

			$css_dir = 'assets/css/';
			$js_dir = 'assets/js/';
			
			wp_enqueue_script('ucpm-google-maps', $api_url);
			wp_enqueue_script('ucpm-geocomplete', $url . 'includes/admin/assets/js/jquery.geocomplete.min.js', array(), $ver, true);

			wp_enqueue_script('ucpm-gm-markers-js', $url . $js_dir . 'ucpm-gm-markers.js', array(), $ver, true);
			wp_enqueue_style('ucpm-gm-markers', $url . $css_dir . 'ucpm-google-map.css', array(), $ver, 'all');
		}
	}

	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
		global $post;
		if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'ucpm_map')) {
			add_filter('is_ucpm', array($this, 'is_ucpm'));
		}
	}

	/**
	 * Add this as a properties_wp page
	 *
	 * @param bool $return
	 * @return bool
	 */
	public function is_ucpm($return) {
		return true;
	}

	/**
	 * The shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public function ucpm_map($atts) {
		$meta_query = array();
		$tax_query = array();
		$atts = shortcode_atts(array(
			'number' => -1,
			'include' => '', // comma separated
			'exclude' => '', // comma separated
			'height' => '400',
			'type' => '', // Custom types
			'purpose' => '', // Lease/Sell
			'status' => '', // Custom statuses
			'relation' => 'AND', // relation between the type, purpose and status
			// JS specific otpions
			'fit' => 'true', // true/false fit to bounds
			'zoom' => '14', // int only applicable if fit is set to false
			'center' => '35.652832, 139.839478', // lat/lng only applicable if fit is set to false
			'search' => 'true', // true/false show search box
			'search_zoom' => '12' // int only applicable if search is set to true
				), $atts);

		$key = ucpm_map_key();
		if (!$key)
			return false;

		$properties_data = array();
		$atts['center'] = array_map('trim', explode(',', $atts['center']));
		$properties_data['map_settings'] = array(
			'fit' => $atts['fit'],
			'zoom' => $atts['zoom'],
			'center' => $atts['center'],
			'search' => $atts['search'],
			'search_zoom' => $atts['search_zoom'],
		);

		// start the query
		$query_args = array(
			'post_type' => 'listing',
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page' => $atts['number'],
		);

		// include only these properties
		if (!empty($atts['include'])) {
			$query_args['post__in'] = array_map('trim', explode(',', $atts['include']));
		}
		// exclude these properties
		if (!empty($atts['exclude'])) {
			$query_args['post__not_in'] = array_map('trim', explode(',', $atts['exclude']));
		}

		// do our meta queries
		if (!empty($atts['type']) || !empty($atts['purpose']) || !empty($atts['status'])) {

			$tax_query = $this->type_query($atts, $tax_query);
            $tax_query = $this->status_query($atts, $tax_query);
            $meta_query = $this->purpose_query($atts, $meta_query);

			if ($meta_query > 1) {
				$meta_query['relation'] = $atts['relation'];
			}

			$query_args['meta_query'] = $meta_query;
			if($tax_query > 1) {
				$query_args['tax_query'] = $tax_query;
			}
		}
		$properties = new WP_Query(apply_filters('ucpm_maps_query', $query_args, $atts));

		if ($properties->have_posts()) :

			while ($properties->have_posts()) : $properties->the_post();

				$property_id = get_the_ID();
				$lat = ucpm_meta('lat');
				$lng = ucpm_meta('lng');
				$property_types = wp_get_post_terms($property_id, 'listing-type', array('fields' => 'ids'));
				$marker_image = '';
				if (!empty($property_types)) {
					$marker_image = get_term_meta($property_types[0], '_ucpm_marker_image', true);
				}

				if ($lat && $lng) {
					$content = wp_trim_words(esc_html(ucpm_meta('content')), 20, '...');
					$content = preg_replace("/[^ \w]+/", "", $content);
					$properties_data['properties'][] = apply_filters('ucpm_maps_property_data', array(
						'title' => get_the_title(),
						'permalink' => get_the_permalink(),
						'lat' => $lat,
						'lng' => $lng,
						'price' => ucpm_price(ucpm_meta('price')),
						'content' => $content,
						'thumbnail' => ucpm_get_first_image(),
						'icon' => $marker_image
					));
				}

			endwhile;

			
			$map = $this->output_the_map($atts, $properties_data);
		else :
			$map = '<p>'._e( 'Sorry, no properties were found.', 'ucpm' ).'</p>';
		endif;
		wp_reset_postdata();

		return $map;
	}

	/**
	 * Display a property map.
	 *
	 * @param array $atts
	 * @return string
	 */
	public function output_the_map($atts, $properties_data) {
		$output = '';
		ob_start();
		?>
		<div class="ucpm-map-wrapper">
		<?php if ($atts['search'] == 'true') { ?>
				<div class="search-panel form-group">
					<input class="form-control search-input" id="ucpm-map-address" type="text" value="" placeholder="<?php esc_html_e('City, Street, Landmark...', 'properties-wp-maps'); ?>" />
					<input class="form-control button btn"  id="ucpm-map-submit" type="submit" value="" />
				</div>
		<?php } ?>

			<ul class="map-controls list-unstyled">
				<li><a href="#" class="control zoom-in" id="ucpm-zoom-in">&#x254B;</a></li>
				<li><a href="#" class="control zoom-out" id="ucpm-zoom-out">&#9472;</a></li>
				<li><a href="#" class="control map-type" id="ucpm-map-type">
						&#x26F6;
						<ul class="list-unstyled">
							<li id="ucpm-map-type-roadmap" class="map-type"><?php esc_html_e('Roadmap', 'ucpm'); ?></li>
							<li id="ucpm-map-type-satellite" class="map-type"><?php esc_html_e('Satellite', 'ucpm'); ?></li>
							<li id="ucpm-map-type-hybrid" class="map-type"><?php esc_html_e('Hybrid', 'ucpm'); ?></li>
							<li id="ucpm-map-type-terrain" class="map-type"><?php esc_html_e('Terrain', 'ucpm'); ?></li>
						</ul>
					</a></li>
				<li><a href="#" id="ucpm-current-location" class="control"><?php esc_html_e('My Location', 'ucpm'); ?></a></li>
			</ul>

			<div id="ucpm-advanced-map" class="ucpm-google-map" data-properties-data='<?php echo json_encode($properties_data, true); ?>' style="height: <?php echo (int) $atts['height']; ?>px">
				<div class="ucpm-loader-container">
					<div class="svg-loader"></div>
				</div>
			</div>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('ucpm_maps_output_map', $output);
	}

	/**
	 * Add to the meta query
	 *
	 */
	public function type_query($atts, $tax_query) {
		// show only a certain type(s)
		if (!empty($atts['type'])) {
			$type_array = array(
				'taxonomy'	=> 'property-type',
				'field'		=> 'term_id',
				'terms'		=> $atts['type']
			);

			array_push($tax_query, $type_array);
		}
		return $tax_query;
	}

	/**
	 * Add to the meta query
	 *
	 */
	public function status_query($atts, $tax_query) {
		// show only a certain status(s)
        if (!empty($atts['status'])) {
            $status_array = array(
                'taxonomy'	=> 'property-status',
                'field'		=> 'term_id',
                'terms'		=> $atts['status']
            );

            array_push($tax_query, $status_array);
        }
        return $tax_query;
	}

	/**
	 * Add to the meta query
	 *
	 */
	public function purpose_query($atts, $meta_query) {
		// show only a certain purpose(s)
		if (!empty($atts['purpose'])) {
            if ( strtolower($atts['purpose']) === 'lease' ) {
                $purpose_array[] = array(
                    array(
                        'key' => '_ucpm_listing_show_lease',
                        'value' => 'on',
                        'compare' => 'LIKE'
                    )
                );
            } elseif ( strtolower($atts['purpose']) === 'sell' ) {
                $purpose_array[] = array(
                    array(
                        'key' => '_ucpm_listing_show_sale',
                        'value' => 'on',
                        'compare' => 'LIKE'
                    )
                );
            }

			array_push($meta_query, $purpose_array);
		}
		return $meta_query;
	}

}

return new ucpm_Maps_Shortcodes();