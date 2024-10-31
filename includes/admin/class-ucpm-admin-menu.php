<?php
/**
 * Setup menus in WP admin.
 *
 * @author   ucpm
 * @category Admin
 * @package  ucpm/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'ucpm_Admin_Menu' ) ) :

	/**
	 * ucpm_Admin_Menus Class.
	 */
	class ucpm_Admin_Menu {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'listings_menu' ), 9 );
			add_action( 'admin_head', array( $this, 'menu_highlight' ) );
		}

		/**
		 * Add menu item.
		 */
		public function listings_menu() {}

		/**
		 * Keep menu open.
		 *
		 * Highlights the wanted admin (sub-) menu items for the CPT.
		 */
		function menu_highlight() {
			global $parent_file, $submenu_file;
		}

	}

endif;

return new ucpm_Admin_Menu();