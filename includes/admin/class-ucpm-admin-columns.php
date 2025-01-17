<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ucpm_Admin_Columns' ) ) :

	/**
	 * Admin columns
	 * @version 0.1.0
	 */
	class ucpm_Admin_Columns {

		/**
		 * fields used for the filter dropdowns
		 */
		public $filter_fields = array(
			'status'		=> 'status',
			'listing-type'	=> 'listing-type',
			'purpose'		=> 'purpose',
		);
		
		private $max_price = 0;

		/**
		 * Constructor
		 * @since 0.1.0
		 */
		public function __construct() {
			$this->get_max_price();
			return $this->hooks();
		}
		
		public function get_max_price() {
			$args = array( 
				'post_type'			=> 'listing', 
				'posts_per_page'	=> '1', 
				'post_status'		=> 'publish',
				'meta_key'			=> '_ucpm_listing_asking_price',
				'orderby'			=> 'meta_value_num',
				'order'				=> 'DESC'
			);

			$listings = get_posts( $args );
			if( !empty( $listings ) ) {
				$listing_id = ($listings[0]->ID);
				$this->max_price = $price = get_post_meta( $listing_id, '_ucpm_listing_asking_price', true );
			}
			return false;
		}

		public function hooks() {
			add_filter( 'manage_listing_posts_columns', array( $this, 'listing_columns' ) );
			add_action( 'manage_listing_posts_custom_column', array( $this, 'listing_data' ), 10, 2 );

			// sorting
			add_filter( 'manage_edit-listing_sortable_columns', array( $this, 'table_sorting' ) );
			add_filter( 'request', array( $this, 'listing_orderby_status' ) );
			add_filter( 'request', array( $this, 'listing_orderby_purpose' ) );
			add_filter( 'request', array( $this, 'listing_orderby_price' ) );

			// filtering
			add_action( 'restrict_manage_posts', array( $this, 'table_filtering' ) );
			add_action( 'parse_query', array( $this, 'filter' ) );
		}

		/**
		 * Set columns for listing
		 */
		public function listing_columns( $defaults ) {

			$post_type  = sanitize_text_field( $_GET['post_type'] );

			$columns    = array();
			$taxonomies = array();

			/* Get taxonomies that should appear in the manage posts table. */
			$taxonomies = get_object_taxonomies( sanitize_text_field($post_type), 'objects');
			$taxonomies = wp_filter_object_list( $taxonomies, array( 'show_admin_column' => true ), 'and', 'name');

			/* Allow devs to filter the taxonomy columns. */
			$taxonomies = apply_filters("manage_taxonomies_for_ucpm_{$post_type}_columns", $taxonomies, $post_type);
			$taxonomies = array_filter($taxonomies, 'taxonomy_exists');

			/* Loop through each taxonomy and add it as a column. */
			foreach ( $taxonomies as $taxonomy ) {
				$columns[ 'taxonomy-' . $taxonomy ] = get_taxonomy($taxonomy)->labels->name;
			}
			$date = $defaults['date'];
			unset($defaults['date']);
			unset($defaults['author']);
			unset($defaults['taxonomy-listing-status']);
			unset($defaults['taxonomy-listing-type']);
			$defaults['image']			= '<span class="dashicons dashicons-images-alt2"></span>';
            $defaults['address']		= esc_html__( 'Address', 'ucpm' );
            $defaults['listing-type']	= esc_html__( 'Type', 'ucpm' );
            $defaults['purpose']		= esc_html__( 'Sale/Lease', 'ucpm' );
            $defaults['inquiries']		= esc_html__( 'Inquiries', 'ucpm' );
			$defaults['date']			= $date;
			return $defaults;
		}

		public function listing_data( $column_name, $post_id ) {

			if ( $column_name == 'id' ) {
				echo '<span class="id">#' . esc_html( $post_id ) . '</div>'; 

			}

			if ( $column_name == 'status' ) {
				$status = ucpm_meta( 'status', $post_id );   
				if( ! $status ) {
					echo '—';
				} else {
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=listing&status='.strtolower( $status ) ) ) . '"><span class="btn status ' . esc_attr( strtolower( $status ) ) . '">' . esc_html( $status ) . '</span></a>';
				}
			}

			if ( $column_name == 'listing-type' ) {
				$type = ucpm_meta( 'listing-type', $post_id );
				if( ! $type ) {
					echo '—';
				} else {
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=listing&listing-type='.strtolower( $type ) ) ) . '"><span class="btn type ' . esc_attr( strtolower( $type ) ) . '">' . esc_html( ucfirst( $type ) ) . '</span></a>';
				}
			}

			if ( $column_name == 'purpose' ) {
				$show_sale = ucpm_meta('show_sale') ? 'Yes' : 'No';
				$show_lease = ucpm_meta('show_lease') ? 'Yes' : 'No';
				
				echo $show_sale . ' / ' . $show_lease;
			}

			if ( $column_name == 'price' ) {
				$width = 0;
				$class = '';
				$price = ucpm_meta( 'price', $post_id );
				 if( ! $price ) {
					echo '—';
				} else {
					$status = ucpm_meta( 'status', $post_id );
					if($status)
						$class = strtolower ($status);

					if( $this->max_price > 0 )
						$width = ceil( ( $price / $this->max_price ) * 100 );

					if($width < 50) {
						$class .= ' yellow';
					}
					echo '<span class="price-range '. esc_attr( $class ) .'" style = "width:'. esc_attr( $width ) .'%"></span>';
					echo ucpm_price( $price );
				}
			}

			if ( $column_name == 'address' ) {
				$address = ucpm_meta( 'displayed_address', $post_id );
				if( ! $address ) {
					echo '—';
				} else {
					echo esc_html( $address ); 
				}
			}

			if ( $column_name == 'image' ) {
				$images = ucpm_meta( 'image_gallery', $post_id );
				if( ! $images )
					return;
				$image  = array_keys( $images );
				$img    = wp_get_attachment_image_src( $image[0], 'thumbnail' );
				if ( $img ) :
					echo '<img src="' . esc_url( $img[0] ) . '" width="50" height="50" />';
				endif; 
			}

			if ( $column_name == 'inquiries' ) {
				$inquiries = ucpm_meta( 'inquiries', $post_id );
				$count = ! empty( $inquiries ) ? count( $inquiries ) : 0;
				if( $count > 0 ) {
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=listing-inquiry&listings='.$post_id ) ) . '"><span>' . esc_html( $count ) . '</a></span>';
				} else {
					echo '—';
				}
			}

		}

		/*
		 * Sorting the table
		 */
		function table_sorting( $columns ) {
			$columns['status']			= 'status';
			$columns['listing-type']	= 'listing-type';
			$columns['purpose']			= 'purpose';
			$columns['price']			= 'price';
			return $columns;
		}

		function listing_orderby_status( $vars ) {
			if ( isset( $vars['orderby'] ) && 'status' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
						'meta_key'	=> '_ucpm_listing_status',
						'orderby'	=> 'meta_value'
				) );
			}
			return $vars;
		}

		function listing_orderby_purpose( $vars ) {
			if ( isset( $vars['orderby'] ) && 'purpose' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key'	=> '_ucpm_listing_purpose',
					'orderby'	=> 'meta_value'
				) );
			}
			return $vars;
		}

		function listing_orderby_price( $vars ) {
			if ( isset( $vars['orderby'] ) && 'price' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key'	=> '_ucpm_listing_asking_price',
					'orderby'	=> 'meta_value'
				) );
			}
			return $vars;
		}

		function table_filtering() {
			global $pagenow;
			$type = get_post_type() ? get_post_type() : 'listing';
			if ( isset( $_GET['post_type'] ) ) {
				$type = sanitize_text_field( $_GET['post_type'] );
			}

			//only add filter to post type you want
			if ( 'listing' == $type && is_admin() && $pagenow == 'edit.php' ) {

				$fields = $this->build_fields();
				$fields['purpose'] = array(
					'sale' => esc_html__( 'Sale', 'ucpm' ),
					'lease' => esc_html__( 'Lease', 'ucpm' ),
				);

				if( $fields ) {
					foreach ( $fields as $field => $values ) {
						asort( $values ); // sort our values
						$values = array_unique( $values ); // make them unique
						?>
						<select name='<?php echo esc_attr( $field ); ?>' id='<?php echo esc_attr( $field ); ?>' class='postform'>

							<option value=''><?php printf( __( 'Show all %s', 'ucpm' ), $field ) ?></option>

							<?php foreach ( $values as $val => $text ) { 
									if( $val != '' ) { ?>
										<option value="<?php echo esc_attr( $val ) ?>" <?php isset( $_GET[$field] ) ? selected( sanitize_text_field( $_GET[$field] ), $val ) : ''; ?>><?php echo esc_html( $text ) ?></option>
										<?php
									}

							} ?>

						</select>

						<?php

						reset( $values );
					}

				}

			}

		}

		/**
		 * Build the dropdown field values for the filtering
		 *
		 */
		private function build_fields(){

			$fields = array();
			$output = '';

			// The Query args
			$args = array( 
				'post_type'			=> 'listing', 
				'posts_per_page'	=> '-1', 
				'post_status'		=> 'publish',
			);

			$listings = query_posts( $args );

			// The Loop
			if ( $listings ) {
				$fields = array();

				foreach ( $listings as $listing ) {
					foreach ( $this->filter_fields as $field => $text ) {
						$val = ucpm_meta( $field, $listing->ID );
						if( $val )
							$fields[$text][$val] = $val;
					}
				}
			}

			/* Restore original Post Data */
			wp_reset_query();
			return $fields;

		}

		function filter( $query ){
			global $pagenow;
			$type = get_post_type() ? get_post_type() : 'listing';
			if (isset($_GET['post_type'])) {
					$type = sanitize_text_field($_GET['post_type']);
			}
			
			if ( 'listing' == $type && is_admin() && $pagenow == 'edit.php' ) {
				foreach ( $this->filter_fields as $field => $text ) {
					
					if( isset( $_GET[$text] ) && $_GET[$text] != '' ) {
						if( $text == 'listing-type' ) {
							$query->query_vars[$text] = sanitize_text_field($_GET[$text]);
						} else if( $text == 'purpose' ) {
							$query->query_vars['meta_key'] = '_ucpm_listing_show_' . sanitize_text_field($_GET[$text]);
							$query->query_vars['meta_value'] = 'on';
						} else {
							$query->query_vars['meta_key'] = '_ucpm_listing_' . $field;
							$query->query_vars['meta_value'] = sanitize_text_field($_GET[$text]);
						}
					}
				}
			}
		}

	}

	new ucpm_Admin_Columns;

endif;