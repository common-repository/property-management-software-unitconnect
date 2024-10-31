<?php
/**
 * Plugin Name: UnitConnect Property Management
* Description: UnitConnect Property Management plugin for WordPress. Create a smart Property Management Company Website with your inventory quickly and easily.
 * Version: 1.0.0
 * Text Domain: ucpm
 * Domain Path: /languages
 *
 * @since     1.0.0
 * @copyright Copyright (c) 2021
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! class_exists( 'UCPM' ) ) :

	/*
	 * Helper function for quick debugging
	 */
	if (!function_exists('pp')) {
		function pp( $array ) {
			echo '<pre style="white-space:pre-wrap;">';
			print_r( $array );
			echo '</pre>';
		}
	}

	/**
	 * Main ucpm Class.
	 *
	 * @since 1.0.0
	 */
	final class UCPM {

		/**
		 * Plugin Version
		 * @var string
		 */
		private $version = '1.1.8';

		/**
		 * @var ucpm The one true ucpm
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Query instance.
		 * @since 1.0.0
		 */
		public $query = null;

		/**
		 * Main ucpm Instance.
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			self::$_instance->define_constants();
			self::$_instance->includes();
			self::$_instance->init_hooks();

			do_action( 'ucpm_loaded' );
			return self::$_instance;
		}

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheating huh?', 'ucpm' ), $this->version );
		}

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheating huh?', 'ucpm' ), $this->version );
		}

		/**
		 * Constructor. Intentionally left empty and public.
		 *
		 * @see instance()
		 * @since  1.0.0
		 */
		public function __construct() {}

		/**
		 * Hook into actions and filters.
		 * @since  1.0.0
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'init' ), 0 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Define Constants.
		 * @since  1.0.0
		 */
		private function define_constants() {
			$upload_dir = wp_upload_dir();
			$this->define( 'UCPM_PLUGIN_FILE', __FILE__ );
			$this->define( 'UCPM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'UCPM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'UCPM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'UCPM_VERSION', $this->version );
		}

		/**
		 * Define constant if not already set.
		 * @since  1.0.0
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 * @since  1.0.0
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 * @since  1.0.0
		 */
		public function includes() {

			include_once( 'includes/libraries/cmb2/init.php' );
			include_once( 'includes/libraries/cmb2-grid/Cmb2GridPlugin.php' );
			include_once( 'includes/libraries/cmb2-metatabs/cmb2_metatabs_options.php' );
			include_once( 'includes/libraries/cmb2-taxonomy-master/init.php' );
			include_once( 'includes/libraries/cmb2-switch-button.php' );

			include_once( 'includes/class-ucpm-install.php' );
			include_once( 'includes/functions-general.php' );
			include_once( 'includes/class-ucpm-roles.php' );
			include_once( 'includes/class-ucpm-post-types.php' );
			include_once( 'includes/class-ucpm-post-status.php' );
			include_once( 'includes/class-ucpm-shortcodes.php' );
			include_once( 'includes/class-ucpm-query.php' );
			include_once( 'includes/class-ucpm-search.php' );
			include_once( 'includes/class-ucpm-map.php' );
			include_once( 'includes/class-ucpm-contact-form.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'includes/admin/class-ucpm-admin.php' );
				include_once( 'includes/admin/export.php' );
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'includes/frontend/class-ucpm-frontend.php' );
			}

			include_once( 'includes/functions-listing.php' );
			include_once( 'includes/functions-inquiry.php' );
			include_once( 'includes/ucpm-widgets.php' );

			if( ucpm_is_theme_compatible() && $this->is_request( 'frontend' ) ) {
				include_once( 'includes/class-ucpm-archive-listings.php' );
			}
		}

		/**
		 * Init ucpm when WordPress Initialises.
		 * @since 1.0.0
		 */
		public function init() {
			// Before init action.
			do_action( 'before_ucpm_init' );
			// Set up localisation.
			$this->load_plugin_textdomain();

			// Load class instances.
			$this->query = new UCPM_Query();

			// Init action.
			do_action( 'ucpm_init' );
		}

		/**
		 * Load Localisation files.
		 * @since 1.0.0
		 */
		public function load_plugin_textdomain() {

			$locale = apply_filters( 'plugin_locale', get_locale(), 'ucpm' );

			load_textdomain( 'ucpm', WP_LANG_DIR . '/ucpm-' . $locale . '.mo' );
			load_plugin_textdomain( 'ucpm', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Show row meta on the plugin screen.
		 * @since 1.0.0
		 */
		public function plugin_row_meta( $links, $file ) {

			if ( $file == UCPM_PLUGIN_BASENAME ) {

				$row_meta = array(
					'docs' => '<a href="#" title="' . esc_attr__( 'View Documentation', 'ucpm' ) . '">' . esc_html__( 'Help', 'ucpm' ) . '</a>',
					);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

	}

endif;

/**
 * Main instance of ucpm.
 *
 * @since  1.0.0
 * @return ucpm
 */
function ucpm() {
	return UCPM::instance();
}

ucpm();
