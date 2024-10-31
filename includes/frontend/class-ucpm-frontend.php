<?php
/**
 * ucpm Frontend
 *
 * @version  1.0.0
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

/**
 * ucpm_Frontend class.
 */
class ucpm_Frontend {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('init', array($this, 'includes'));
		add_action('body_class', array($this, 'body_class'));
	}

	/**
	 * Include any files we need within frontend.
	 */
	public function includes() {
		if( ! ucpm_is_theme_compatible() )
		include_once( 'class-ucpm-template-loader.php' );

		include_once( 'class-ucpm-enqueues.php' );
		include_once( 'template-hooks.php' );
		include_once( 'template-tags.php' );
	}

	/**
	 * Add body classes for our pages.
	 *
	 * @param  array $classes
	 * @return array
	 */
	public function body_class($classes) {
		$classes = (array) $classes;

		if (is_ucpm()) {
			$classes[] = 'ucpm';
		}

		return array_unique($classes);
	}

}

return new ucpm_Frontend();