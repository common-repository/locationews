<?php
/**
 * Internationalization provider.
 *
 * @package   Locationews
 * @copyright Copyright (c) 2018, Locationews, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Internationalization provider class.
 *
 * @package Locationews
 * @since   2.0.0
 */
class Locationews_Provider_I18n extends Locationews_AbstractProvider {

	/**
	 * Register hooks
	 *
	 * Loads the text domain during the `plugins_loaded` action.
	 *
	 * @since 2.0.0
	 */
	public function ln_register_hooks() {
		if ( did_action( 'plugins_loaded' ) ) {
			$this->ln_load_textdomain();
		} else {
			add_action( 'plugins_loaded', [ $this, 'ln_load_textdomain' ] );
		}

		//add_action( 'admin_init', [ $this, 'ln_load_textdomain' ] );
	}

	/**
	 * Load textdomain
	 *
	 * Load the text domain to localize the plugin.
	 *
	 * @since 2.0.0
	 */
	public function ln_load_textdomain() {
		$plugin_rel_path = dirname( $this->plugin->ln_get_basename() ) . '/languages';
		load_plugin_textdomain( $this->plugin->ln_get_slug(), false, $plugin_rel_path );
	}
}
