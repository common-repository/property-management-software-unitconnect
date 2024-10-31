<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class ucpm_Template_Loader {

	/**
	 * Get things going
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter('template_include', array($this, 'template_loader'));
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder.
	 * ucpm looks for theme overrides in /theme/listings/ by default.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader($template) {

		$file = '';

		if (is_single() && get_post_type() == 'listing') {
			$file = 'single-listing.php';
		}

		if (( is_archive() && get_post_type() == 'listing' ) || is_ucpm_search()) {
			$file = 'archive-listing.php';
		}

		$file = apply_filters('ucpm_template_file', $file);
		if (!$file) {
			return $template;
		}

		$template = ucpm_get_part($file);

		return $template;
	}

}

new ucpm_Template_Loader();