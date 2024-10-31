<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ucpm_Search {
	
	/**
	 * Get things going
	 *
	 */
	public function __construct() {
		add_shortcode( 'ucpm_search', array( $this, 'search_form' ) );

		if( !ucpm_is_theme_compatible() )
			add_filter( 'query_vars', array( $this, 'register_query_vars' ) );

		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 999 );
		
		add_action('wp_ajax_ucpm_orderby_value', array( $this, 'ucpm_orderby_value' ));
		add_action('wp_ajax_nopriv_ucpm_orderby_value', array( $this, 'ucpm_orderby_value' ));
	}
	
	private $ids = '';

	public function ucpm_orderby_value() {
		$search_data = str_replace('?', '', sanitize_text_field( $_POST['search_data'] ));
		parse_str($search_data, $form_data);
		$paged = $form_data['paged'];
		$posts_per_page = ucpm_default_posts_number();
		$query_args = array(
			'post_type' => 'listing',
			'post_status' => 'publish',
			'posts_per_page' => $posts_per_page,
			'paged' => $paged,
		);

        $meta_query = array();

		if ( isset( $form_data['city'] ) ) {
		    $meta_query[] = array(
                'key' => '_ucpm_listing_city',
                'value' => sanitize_text_field( $form_data['city'] ),
                'compare' => 'LIKE'
            );
        }

		if ( ! empty( $form_data['s'] ) ) {
		    $query_args['s'] = $form_data['s'];
        }

		if ( isset( $_GET['purpose'] ) ) {
            $purpose_query[] = array(
                'key' => '_ucpm_listing_purpose',
                'value' => sanitize_text_field( $_GET['purpose'] ),
                'compare' => 'LIKE'
            );
        }

        $type = isset( $form_data['type'] ) ? $form_data['type'] : '';
		$location = isset( $form_data['location'] ) ? $form_data['location'] : '';

		$type_query[] = $this->type_meta_query( $type );
		$keyword_query[] = $this->keyword_query('', $location);
		
		$order_by = sanitize_text_field( $_POST['order_by'] );
		$orderby_value = explode('-', $order_by);
		$orderby = $orderby_value[0];
		$order = !empty($orderby_value[1]) ? $orderby_value[1] : $order;
		$ordering = $this->get_ordering_args($orderby, $order);
		if (isset($ordering['meta_key'])) {
			$query_args['meta_key'] = $ordering['meta_key'];
		}
		$query_args['orderby'] = $ordering['orderby'];
		$query_args['order'] = $ordering['order'];

		// this should be always set
		$query_1 = $purpose_query;

		// within radius AND purpose AND type
		$query_2 = array_merge($keyword_query);

		// if no keyword
		if ( $location == '' ) {
			$query_1['relation'] = 'AND';
			$meta_query[] = $query_1;
		}

		// if keyword
		if ($location != '') {
			$query_2['relation'] = 'OR';
			$meta_query[] = $query_1;
			$meta_query[] = $query_2;
			$meta_query['relation'] = 'AND';
		}
		if (isset($_GET['type']) && !empty($_GET['type'])) {
			$query_args['tax_query'] = $type_query;
		}

		$query_args['meta_query'] = $meta_query;
		
		$archive_listings = new WP_Query($query_args);
		ob_start();
		while ($archive_listings->have_posts()) : $archive_listings->the_post();
			ucpm_get_part('content-listing.php');
		endwhile;
		echo $data = ob_get_clean();
		exit;
	}
	/**
	 * Register custom query vars, based on our registered fields
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 */
	public function register_query_vars( $vars ) {
		foreach ( $this->register_meta_fields() as $key => $field ) {
			$vars[] = strtolower( $key );
		}
		$vars[] = 'purpose'; // always add the purpose (buy/sell)
		return $vars;
	} 

	/**
	 * Register our search fields
	 *
	 */
	public function register_meta_fields() {
		$fields = array(
			'type'	=> array( // the key is the main ID
				'name'		=> 'type',
				'meta_key'	=> 'type',
				'format'	=> 'select', 
				'class'		=> '',
			),
		);
		return apply_filters( 'ucpm_search_fields', $fields );
	}


	/**
	 * Build the form
	 *
	 */
	public function search_form( $atts ) {

		$atts = shortcode_atts( array(
			'placeholder'	=> esc_html__( 'Search by Text', 'ucpm' ),
			'submit_btn'	=> esc_html__( 'Search', 'ucpm' ),
			'show_map'		=> '',
			'exclude'		=> array(),
		), $atts );

		$fields	= $this->build_fields( $atts );
		$purpose = ucpm_option( 'display_purpose' );
		$listing_ids = '';
		ob_start();

		if( $atts['show_map'] == 'yes' ) {
			if( is_array( $this->ids ) && ! empty( $this->ids ) ) {
				$listing_ids = implode(',', $this->ids);
			}
			echo do_shortcode( "[ucpm_map include = $listing_ids]" );
		}

		?>

		<form 
			id="ucpm-search-form" 
			class="ucpm-search-form fields-<?php echo esc_attr( absint( $fields['count'] ) ) ?> display-<?php echo esc_attr( $purpose ) ?>" autocomplete="off"
			action="<?php echo esc_url( get_permalink( ucpm_option( 'archives_page' ) ) ); ?>" 
			method="GET" 
			role="search">

			<div class="form-group">

				<?php
				// include the Buy/Rent dropdown if we want to display both
				// or a hidden field with the purpose if only displaying one
                if ( ucpm_is_theme_compatible() ) {
                    $text_val = isset( $_GET['location'] ) ? sanitize_text_field( $_GET['location'] ) : '';
                } else {
                    $text_val = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
                }

				if( $purpose == 'both' ){
					$both	= isset( $_GET['purpose'] ) && $_GET['purpose'] == 'both' ? ' selected="selected"' : '';
					$lease	= isset( $_GET['purpose'] ) && $_GET['purpose'] == 'lease' ? ' selected="selected"' : '';
					$sell	= isset( $_GET['purpose'] ) && $_GET['purpose'] == 'sell' ? ' selected="selected"' : '';
				?>

					<div class="purpose-wrap">
						<div class="ucpm-select-wrap">
							<select class="form-control purpose" name="purpose">
								<option value="both" <?php echo esc_attr( $both ) ?>><?php esc_html_e( 'Both', 'ucpm' ) ?></option>
								<option value="sell" <?php echo esc_attr( $sell ) ?>><?php esc_html_e( 'Sell', 'ucpm' ) ?></option>
								<option value="lease" <?php echo esc_attr( $lease ); ?>><?php esc_html_e( 'Lease', 'ucpm' ) ?></option>
							</select>
						</div>
					</div>

				<?php } else { ?>

						<input type="hidden" name="purpose" value="<?php echo esc_attr( $purpose ) ?>"/>

				<?php } ?>
				<div class="search-text-wrap">
					<input class="form-control search-input" type="text" name="<?php echo ucpm_is_theme_compatible() ? 'location' : 's'; ?>" placeholder="<?php echo esc_attr( $atts['placeholder'] ) ?>" value="<?php echo esc_attr( $text_val ); ?>" />
				</div>
				
				<input class="button btn btn-default search-button" type="submit" value="<?php echo esc_attr( $atts['submit_btn'] ) ?>" />

			</div>

			<?php
			// add our registered and built fields
			echo $fields['output'];
			?>

		</form>

		<?php

		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters( 'ucpm_search_form_output', $output, $atts );

	}

	public function select_field( $field, $array ) {

		// get the label (or the formatted field key if no label)
		$fields	= $this->register_meta_fields();
		$label	= isset( $fields[$field]['label'] ) && ! empty( $fields[$field]['label'] ) ? $fields[$field]['label'] : ucwords( str_replace( '_', ' ', $field));
		$class	= isset( $fields[$field]['class'] ) && ! empty( $fields[$field]['class'] ) ? ' ' . $fields[$field]['class'] : '';
		$name	= strtolower( $field );

		$output = '';
		ob_start();

		?>
			<div class="<?php echo esc_attr( $class ) ?>">
				<div class="ucpm-select-wrap">

					<select class="form-control <?php echo esc_attr( $name ) ?>" name="<?php echo esc_attr( $name ) ?>">

						<option value=""><?php echo esc_html( $label ) ?></option>

						<?php if( ! empty( $array ) ) {

								foreach ( $array as $val => $text ) {

									$selected = isset( $_GET[$field] ) && $_GET[$field] == $val ? ' selected="selected"' : '';

									if( ! empty( $val ) ) { ?>
										<option value="<?php echo esc_attr( $val ); ?>" <?php echo esc_html( $selected ); ?> ><?php echo esc_html( ucwords( $text ) ) ?></option>
									<?php } 
								}
						}
						?>

					</select>

				</div>
			</div>

		<?php

		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters( 'ucpm_search_form_fields_output', $output );

	}

	/**
	 * Build the fields for output
	 *
	 */
	private function build_fields( $atts ){

		$registered_fields	= $this->register_meta_fields();
		$exclude			= isset( $atts['exclude'] ) && ! empty( $atts['exclude'] ) ? $atts['exclude'] : null;
		$exclude_fields		= ! empty( $exclude ) ? array_map('trim', explode( ',', $exclude ) ) : array();
		$output = '';

		// The Query
		$args = array(
			'post_type'			=> 'listing', 
			'posts_per_page'	=> '-1',
			'post_status'		=> 'publish',
		);

		// modify the query if we only want rent or only want sell
        if ( isset( $_GET['purpose'] ) && $_GET['purpose'] !== 'both' ) {
            $args['meta_key']		= '_ucpm_listing_purpose';
            $args['meta_value']		= sanitize_text_field( $_GET['purpose'] );
            $args['meta_compare']	= 'LIKE';
        }

		$listings = query_posts( apply_filters( 'ucpm_search_populate_dropdown_args', $args ) );

		// The Loop
		$fields = array();
		if ( $listings ) {

			foreach ( $listings as $listing ) {

				foreach ( $registered_fields as $key => $field ) {
					
					if( in_array( $key, $exclude_fields ) )
						continue;

					$val = ucpm_meta( $field['meta_key'], $listing->ID );

					if( $val && $val !='' ) {
						$fields[$key][$val] = ucwords( str_replace( '-', ' ', $val ) );
					}

				}

			}
		}

		/* Restore original Post Data */
		wp_reset_query();

		$field_count = count( $fields );

		// loop through our registered fields
		foreach ( $fields as $field => $values ) {
			asort( $values ); // sort our values
			$values = array_unique( $values ); // make them unique

			switch ( $registered_fields[$field]['format'] ) {
				case 'select':
					$output .= $this->select_field( $field, $values ); // add the select field to the output
				break;

				default:
					# code...
				break;
			}

			reset( $values );

		}

		return array( 
			'output'	=> $output,
			'count'		=> $field_count
		);	

	}

	/**
	 * Build a custom query based on several conditions
	 * The pre_get_posts action gives developers access to the $query object by reference
	 * any changes you make to $query are made directly to the original object - no return value is requested
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts
	 *
	 */
	public function pre_get_posts( $q ) {
		
		// check if the user is requesting an admin page 
		// or current query is not the main query
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}

		if ( ! is_post_type_archive( 'listing' ) ) {
			return;
		}

		$meta_query = array();
		$tax_query = array();
		// start our separate queries
		// they are all merged together later

        if ( isset( $_GET['purpose'] ) ) {
            $purpose_query[] = array(
                'key'		=> '_ucpm_listing_purpose',
                'value'		=> sanitize_text_field( $_GET['purpose'] ),
                'compare'	=> 'LIKE',
            );
        }

        $meta_query[] = array(
			'key'		=> '_ucpm_listing_display_property',
			'value'		=> 'on',
			'compare'	=> 'LIKE',
		);

		$type_query[]		= $this->type_meta_query();
		$keyword_query[]	= $this->keyword_query( $q );
		

		// this should be always set
		$query_1 = $purpose_query;

		// within radius AND purpose AND Bedrooms AND price AND type
		$query_2 = array_merge( $keyword_query );
			
		// if no keyword
		if ( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
			$query_1['relation'] = 'AND';
			$meta_query[] = $query_1;
		}

		// if keyword
		if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
			$query_2['relation'] = 'OR';
			$meta_query[] = $query_1;
			$meta_query[] = $query_2;
			$meta_query['relation'] = 'AND';
		}
		if ( isset( $_GET['type'] ) && ! empty( $_GET['type'] ) ) {
			$q->set( 'tax_query', $type_query );
		}
		
		$q->set( 'meta_query', $meta_query );
		$this->post_listings($q->query_vars);

	}
	
	public function post_listings($args) {
		$args['fields'] = 'ids';
		$this->ids = get_posts($args);
	}

	// function to geocode address, it will return false if unable to geocode address
	private function geocode( $address ) {

		// url encode the address
		$address = urlencode( esc_html( $address ) );
		 
		// google map geocode api url
		$url = ucpm_google_geocode_maps_url( $address );
		
		$arrContextOptions = array(
			"ssl"	=> array(
				"verify_peer"		=> false,
				"verify_peer_name"	=> false,
			),
		); 

		// get the json response
		$resp_json = file_get_contents( $url, false, stream_context_create($arrContextOptions) );
		 
		// decode the json
		$resp = json_decode( $resp_json, true );

		//pp( $resp );

		// response status will be 'OK', if able to geocode given address 
		if( $resp['status'] == 'OK' ){

			// get the lat and lng
			$lat = $resp['results'][0]['geometry']['location']['lat'];
			$lng = $resp['results'][0]['geometry']['location']['lng'];

			// verify if data is complete
			if( $lat && $lng ){

				return array(
					'lat'	=> $lat,
					'lng'	=> $lng,
				);

			} else {
				return false;
			}

		} else {
			return false;
		}

	}


	/**
	 * Searches through our custom fields using a keyword match
	 * @return array
	 */
	protected function keyword_query( $q, $location = '' ) {
		if ( (isset( $_GET['s'] ) && ! empty( $_GET['s'] )) || (isset( $_GET['location'] ) && ! empty( $_GET['location'] )) || $location == '' ) {

			$custom_fields = apply_filters( 'ucpm_keyword_search_fields', array(
				// put all the meta fields you want to search for here
				'_ucpm_listing_city',
				'_ucpm_listing_zip',
				'_ucpm_listing_state',
				'_ucpm_listing_county',
				'_ucpm_listing_route',
				'_ucpm_listing_displayed_address',
			) );
			if( isset( $_GET['s'] ) ) {
			$searchterm = sanitize_text_field( $_GET['s'] );
			// we have to remove the "s" parameter from the query, because it will prevent the posts from being found
			$q->query_vars['s'] = '';
			} else {
				if( $location == '' && isset( $_GET['location'] ) ) {
					$location = sanitize_text_field( $_GET['location'] );
			}
				$searchterm = $location;
			}

			if ( $searchterm != '' ) {

				$meta_query = array('relation' => 'OR');
				foreach($custom_fields as $cf) {
					array_push( $meta_query, array(
						'key'		=> $cf,
						'value'		=> $searchterm,
						'compare'	=> 'LIKE'
					));
				}
				return $meta_query;
			};
		}
		return array();
	}

	/**
	 * Return a meta query for filtering by type.
	 * @return array
	 */
	protected function type_meta_query( $type = '' ) {
		
		if( $type == '' ) {
		if ( isset( $_GET['type'] ) && ! empty( $_GET['type'] ) ) {
				$type = sanitize_text_field( $_GET['type'] );
			}
		}
		
		if ( $type != '' ) {
			return array(
				'taxonomy'	=> 'listing-type',
				'field'		=> 'slug',
				'terms'		=> sanitize_text_field( $_GET['type'] ),
			);
		}
		return array();
	}

	/**
	 * Returns an array of arguments for ordering listings based on the selected values.
	 *
	 * @access public
	 * @return array
	 */
	protected function get_ordering_args($orderby = '', $order = '') {

		// Get ordering from query string unless defined
		if (!$orderby) {
			$orderby_value = isset($_GET['ucpm-orderby']) ? sanitize_text_field( $_GET['ucpm-orderby'] ) : 'date';

			// Get order + orderby args from string
			$orderby_value = explode('-', $orderby_value);
			$orderby = $orderby_value[0];
			$order = !empty($orderby_value[1]) ? $orderby_value[1] : $order;
		}

		$orderby = strtolower($orderby);
		$order = strtoupper($order);
		$args = array();

		// default - menu_order
		$args['orderby'] = 'date ID';
		$args['order'] = $order == 'OLD' ? 'ASC' : 'DESC';
		$args['meta_key'] = '';

		switch ($orderby) {

			case 'date' :
				$args['orderby'] = 'date ID';
				$args['order'] = $order == 'OLD' ? 'ASC' : 'DESC';
				break;
			case 'price' :
				$args['orderby'] = "meta_value_num ID";
				$args['order'] = $order == 'HIGH' ? 'DESC' : 'ASC';
				$args['meta_key'] = '_ucpm_listing_asking_price';
				break;
		}

		return apply_filters('ucpm_get_ordering_args', $args);
	}

}

return new ucpm_Search();