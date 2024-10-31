<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ucpm_Admin_Inquiry_Columns' ) ) :

	/**
	 * Admin columns
	 * @version 0.1.0
	 */
	class ucpm_Admin_Inquiry_Columns {

		/**
		 * fields used for the filter dropdowns
		 */
		public $filter_fields = array(
			'listing_id'	=> 'listings',
			'name'			=> 'names',
			'email'			=> 'emails',
		);

		/**
		 * Constructor
		 * @since 0.1.0
		 */
		public function __construct() {
			return $this->hooks();
		}

		public function hooks() {
			add_filter( 'manage_listing-inquiry_posts_columns', array( $this, 'inquiry_columns' ) );
			add_action( 'manage_listing-inquiry_posts_custom_column', array( $this, 'inquiry_data' ), 10, 2 );

			// sorting
			add_filter( 'manage_edit-listing-inquiry_sortable_columns', array( $this, 'table_sorting' ) );
			add_filter( 'request', array( $this, 'inquiry_orderby_listing' ) );
			add_filter( 'request', array( $this, 'inquiry_orderby_name' ) );
			add_filter( 'request', array( $this, 'inquiry_orderby_email' ) );

			// filtering
			add_action( 'restrict_manage_posts', array( $this, 'table_filtering' ) );
			add_action( 'parse_query', array( $this, 'filter' ) );
		}

		/**
		 * Set columns for listing
		 */
		public function inquiry_columns( $defaults ) {

			$post_type  = sanitize_text_field( $_GET['post_type'] );

			$columns    = array();
			$taxonomies = array();
			$date = $defaults['date'];
			unset($defaults['date']);
			/* Get taxonomies that should appear in the manage posts table. */
			$taxonomies = get_object_taxonomies( $post_type, 'objects');
			$taxonomies = wp_filter_object_list( $taxonomies, array( 'show_admin_column' => true ), 'and', 'name');

			/* Allow devs to filter the taxonomy columns. */
			$taxonomies = apply_filters("manage_taxonomies_for_ucpm_{$post_type}_columns", $taxonomies, $post_type);
			$taxonomies = array_filter($taxonomies, 'taxonomy_exists');

			/* Loop through each taxonomy and add it as a column. */
			foreach ( $taxonomies as $taxonomy ) {
				$columns[ 'taxonomy-' . $taxonomy ] = get_taxonomy($taxonomy)->labels->name;
			}

			$defaults['listing']	= esc_html__( 'Listing', 'ucpm' );
			$defaults['name']		= esc_html__( 'From Name', 'ucpm' );
			$defaults['email']		= esc_html__( 'From Email', 'ucpm' );
			$defaults['date']		= $date;
			return $defaults;
		}

		public function inquiry_data( $column_name, $post_id ) {

			$listing_id = ucpm_inquiry_meta( 'listing_id', $post_id );

			if ( $column_name == 'listing' ) {

				if( ! $listing_id )
					return;

				echo '<a title="' . esc_html__( 'Edit Listing', 'ucpm' ) . '" target="_blank" href="' . esc_url( get_edit_post_link( $listing_id ) ) . '">' . esc_html( get_the_title( $listing_id ) ) . ' <span class="dashicons dashicons-external"></span></a><br>';
				echo esc_html( ucpm_meta( 'displayed_address', $listing_id ) ); 

			}

			if ( $column_name == 'name' ) {
				$name = ucpm_inquiry_meta( 'name', $post_id );
				if( ! $name ) {
					echo '—';
				} else {
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=listing-inquiry&names='.$name ) ) . '">' . esc_html( $name ) . '</a>';
				}
			}

			if ( $column_name == 'email' ) {
				$email = ucpm_inquiry_meta( 'email', $post_id );
				if( ! $email ) {
					echo '—';
				} else {
					echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=listing-inquiry&emails='.$email ) ) . '">' . esc_html( $email ) . '</a>';
				}
			}

		}

		/*
		 * Sorting the table
		 */
		function table_sorting( $columns ) {
			$columns['listing']		= 'listing_title';
			$columns['name']		= 'name';
			$columns['email']		= 'email';
			return $columns;
		}


		function inquiry_orderby_listing( $vars ) {
			if ( isset( $vars['orderby'] ) && 'listing' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_ucpm_inquiry_listing_title',
					'orderby' => 'meta_value'
				) );
			}
			return $vars;
		}

		function inquiry_orderby_name( $vars ) {
			if ( isset( $vars['orderby'] ) && 'name' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_ucpm_inquiry_first_name',
					'orderby' => 'meta_value'
				) );
			}
			return $vars;
		}
		function inquiry_orderby_email( $vars ) {
			if ( isset( $vars['orderby'] ) && 'email' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					'meta_key' => '_ucpm_inquiry_email',
					'orderby' => 'meta_value'
				) );
			}
			return $vars;
		}

		function table_filtering() {
			global $pagenow;
			$type = get_post_type() ? get_post_type() : 'listing-inquiry';
			if ( isset( $_GET['post_type'] ) ) {
				$type = sanitize_text_field( $_GET['post_type'] );
			}

			//only add filter to post type you want
			if ( 'listing-inquiry' == $type && is_admin() && $pagenow == 'edit.php' ) {

				$fields = $this->build_fields();
				if( $fields ) {

					foreach ( $fields as $field => $values ) {
						asort( $values ); // sort our values
						$values = array_unique( $values ); // make them unique

						?>
						<select name='<?php echo esc_attr( $field ); ?>' id='<?php echo esc_attr( $field ); ?>' class='postform'>

							<option value=''><?php printf( __( 'Show all %s', 'ucpm' ), $field ) ?></option>

							<?php foreach ( $values as $val => $text ) {
									if( $field == 'listings' ) :
										$text = get_the_title( $text );
									else :
										$text = $text;
									endif;
									if( empty( $val ) ) 
										continue;
							?>
									<option value="<?php echo esc_attr( $val ) ?>" <?php isset( $_GET[$field] ) ? selected( sanitize_text_field( $_GET[$field] ), $val ) : ''; ?>><?php echo esc_html( $text ) ?></option>

							<?php } ?>

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

			$fields = '';
			$output = '';

			// The Query args
			$args = array( 
				'post_type'         => 'listing-inquiry',
				'posts_per_page'    => '-1', 
				'post_status'       => 'publish',
			);

			$listings = query_posts( $args );

			// The Loop
			if ( $listings ) {

				$fields = array();

				foreach ( $listings as $listing ) {
					foreach ( $this->filter_fields as $field => $text ) {

						$val = ucpm_inquiry_meta( $field, $listing->ID );
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
			$type = get_post_type() ? get_post_type() : 'listing-inquiry';
			if (isset($_GET['post_type'])) {
				$type = sanitize_text_field( $_GET['post_type'] );
			}
			if ( 'listing-inquiry' == $type && is_admin() && $pagenow == 'edit.php' ) {

				foreach ( $this->filter_fields as $field => $text ) {
					if( isset( $_GET[$text] ) && $_GET[$text] != '' ) {
						$query->query_vars['meta_key']      = '_ucpm_inquiry_' . $field;
						$query->query_vars['meta_value']    = sanitize_text_field($_GET[$text]);
					}
				}

			}

		}

	}

	new ucpm_Admin_Inquiry_Columns;

endif;