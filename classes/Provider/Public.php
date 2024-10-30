<?php
/**
 * Locationews Public provider.
 *
 * @package   Locationews
 * @copyright Copyright (c) 2018, Locationews, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Public provider class.
 *
 * @package Locationews
 * @since   2.0.0
 */
class Locationews_Provider_Public extends Locationews_AbstractProvider {

	/**
	 * Register hooks
	 *
	 * Loads the text domain during the `plugins_loaded` action.
	 *
	 * @since 2.0.0
	 */
	public function ln_register_hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'ln_load_scripts' ] );
	}

	/**
	 * Load scripts
	 *
	 * Load public styles and scripts.
	 *
	 * @since 2.0.0
	 */
	public function ln_load_scripts() {
		wp_enqueue_script(
			'locationews',
			$this->plugin->ln_get_url() . 'assets/js/min.js',
			[ 'jquery' ],
			$this->plugin->ln_get_version(),
			true
		);

		if ( false === $this->plugin->ln_check_registered_library( 'maps.googleapis' ) ) {
			wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $this->plugin->ln_get_option('gApiKey', 'front') . '&language=' . $this->plugin->ln_get_option('gLanguage', 'front') . '&region=' . $this->plugin->ln_get_option('gRegion', 'front') . '&libraries=places', [ 'jquery' ], $this->plugin->ln_get_version(), true );
		}

	}

}
