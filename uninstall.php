<?php
/**
 * Uninstall ucpm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

// Load ucpm file.
include_once( 'ucpm.php' );

$remove = ucpm_option( 'delete_data' );
/**
 * ucpm_Uninstall Class
 *
 * This class removes post data, options data and user roles and capabilities
 *
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'ucpm_Uninstall' ) ) :

	class ucpm_Uninstall {

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheating huh?', 'ucpm' ), '1.0.0' );
		}

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheating huh?', 'ucpm' ), '1.0.0' );
		}

		/**
		 * The Constructor.
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			$this->remove_post_types();
			$this->remove_pages();
			$this->delete_options();
			$this->delete_capabilities();
		}

		/**
		 * Removes Posts data.
		 *
		 * @since  1.0.0
		 */
		public function remove_post_types() {
			global $wpdb;
			$taxonomies = array( 'listing-type' );

			$ucpm_post_types = array( 'listing', 'listing-inquiry' );
			foreach ( $ucpm_post_types as $post_type ) {
				$taxonomies = array_merge( $taxonomies, get_object_taxonomies( $post_type ) );
				$ucpm_posts = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
				if ( $ucpm_posts ) {
					foreach ( $ucpm_posts as $ucpm_post ) {
						$this->remove_post_attachment( $ucpm_post );
						wp_delete_post( $ucpm_post, true);
					}
				}
			}

			/** Delete All the Terms & Taxonomies */
			foreach ( array_unique( array_filter( $taxonomies ) ) as $taxonomy ) {

				$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );

				// Delete Terms
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
						$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
					}
				}

				// Delete Taxonomies
				$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
			}

		}
		
		/**
		 * Removes Plugin Specific pages.
		 *
		 * @since  1.0.0
		 */
		public function remove_pages() {
			
			$listings_page = ucpm_option( 'archives_page' );
			$pages = array( $listings_page );
			foreach ( $pages as $page ) {
				wp_delete_post( $page, true);
			}

		}

		/**
		* Removes Post Attachments.
		*
		* @param  int	$post_id
		*
		*/
		public function remove_post_attachment($post_id) {

			$images = ucpm_meta( 'image_gallery', $post_id );
			if( !empty($images) ) {
				foreach( $images as $attachment_id => $image ) {
					$this->remove_attachment( $attachment_id, $post_id );
				}
			}

		}

		/**
		 * Function to check if attachment is used anywhere else before deleting it.
		 *
		 * @since  1.0.0
		 */
		public function remove_attachment( $attachment_id, $post_id = '' ) {

			// First we'll check if it is used as a thumbnail by another post
			if ( empty ( get_posts( array( 'post_type' => 'any', 'post_status' => 'any', 'fields' => 'ids', 'no_found_rows' => true, 'posts_per_page' => -1, 'meta_key' => '_thumbnail_id', 'meta_value' => $attachment_id, 'post__not_in' => array( $post_id ) ) ) ) ) {

				// Now we have to check if it's used somewhere in content.
				$attachment_urls = array( wp_get_attachment_url( $attachment_id ) );
				foreach ( get_intermediate_image_sizes() as $size ) {
					$intermediate = image_get_intermediate_size( $attachment_id, $size );
					if ( $intermediate ) {
						$attachment_urls[] = $intermediate['url'];
					}
				}

				// Now we can search for these URLs in content
				$used = array();
				foreach ( $attachment_urls as $attachment_url ) {
					$used = array_merge( $used, get_posts( array( 'post_type' => 'any', 'post_status' => 'any', 'fields' => 'ids', 'no_found_rows' => true, 'posts_per_page' => -1, 's' => $attachment_url, 'post__not_in' => array( $post_id ) ) ) );
				}
				if ( empty( $used ) ) {
					// The image is not used anywhere in the content
					// So finally we can delete it
					wp_delete_attachment( $attachment_id, true );
				}
			}
		}

		/**
		 * Function to get attachment id from attachment url.
		 *
		 * @since  1.0.0
		 */
		public function get_attachment_id_from_url( $attachment_url ) {
			global $wpdb;
			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $attachment_url ));
			return $attachment[0]; 
		}

		/**
		 * Function to delete plugin options.
		 *
		 * @since  1.0.0
		 */
		public function delete_options() {

			delete_option( 'ucpm_options' );
			delete_option( 'UCPM_VERSION' );
			delete_option( 'UCPM_VERSION_upgraded_from' );
			delete_option( 'ucpm_import_progress' );

		}

		/**
		 * Function to delete capabilities and role.
		 *
		 * @since  1.0.0
		 */
		public function delete_capabilities() {
			/** Delete Capabilities */
			$roles = new ucpm_Roles;
			$roles->remove_caps();
		}

	}

endif;

if( $remove == 'yes' ) {
	return new ucpm_Uninstall();
}