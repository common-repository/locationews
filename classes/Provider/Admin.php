<?php
/**
 * Locationews Admin provider.
 *
 * @package   Locationews
 * @copyright Copyright (c) 2018, Locationews, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Admin provider class.
 *
 * @package Locationews
 * @since   2.0.0
 */
class Locationews_Provider_Admin extends Locationews_AbstractProvider {

	/**
	 * Register hooks
	 *
	 * Loads the text domain during the `plugins_loaded` action.
	 *
	 * @since 2.0.0
	 */
	public function ln_register_hooks() {
		add_action( 'admin_init', [ $this, 'ln_init' ] );
	}

	/**
	 * Initialize
	 *
	 * @since 2.0.0
	 */
	public function ln_init() {
		add_action( 'admin_enqueue_scripts', [
			$this,
			'ln_load_scripts',
		], 998 );
	}

	/**
	 * Load scripts
	 *
	 * Load plugin styles and scripts.
	 *
	 * @since 2.0.0
	 */
	public function ln_load_scripts($hook) {

		$post_types = [];
		$user_post_types = $this->plugin->ln_get_option( 'postTypes', 'user' );
		
		if ( is_array( $user_post_types ) ) {
			$post_types = array_keys( $user_post_types );
		}

		if ( 
			in_array( get_current_screen()->id, [ 'toplevel_page_locationews-settings', 'locationews_page_locationews-json-settings' ] ) 
			|| ( in_array( $hook, array('post-new.php', 'post.php') ) ) /*
			|| ( isset( get_current_screen()->post_type ) 
				&& in_array( get_current_screen()->post_type, $post_types ) ) */) {

			wp_enqueue_style(
				'locationews',
				$this->plugin->ln_get_url() . 'assets/css/locationews-wp-plugin-admin.' . ( $this->plugin->ln_get_enviroment() == 'dev' ? 'css' : 'min.css' ),
				[],
				$this->plugin->ln_get_version(),
				'all'
			);

			if ( ! $this->ln_check_registered_library( 'bootstrap.min.js' ) && ! $this->ln_check_registered_library( 'bootstrap.js' ) ) {

				wp_enqueue_script(
					'bootstrap-js',
					$this->plugin->ln_get_url() . 'assets/js/bootstrap.min.js',
					[ 'jquery' ],
					$this->plugin->ln_get_version(),
					false
				);

			}

			if ( ! $this->ln_check_registered_library( 'bootstrap-switch.min.js' ) && ! $this->ln_check_registered_library( 'bootstrap-switch.js' ) ) {

				wp_enqueue_script(
					'bootstrap-switch-js',
					$this->plugin->ln_get_url() . 'assets/js/bootstrap-switch.min.js',
					[ 'jquery' ],
					$this->plugin->ln_get_version(),
					false
				);

			}

			if ( get_current_screen()->id == 'toplevel_page_locationews-settings' ) {

				wp_enqueue_script(
					'locationews-settings-init-js',
					$this->plugin->ln_get_url() . 'assets/js/locationews-settings.' . ( $this->plugin->ln_get_enviroment() == 'dev' ? 'js' : 'min.js' ),
					[],
					$this->plugin->ln_get_version(),
					false
				);

				wp_localize_script(
					'locationews-settings-init-js',
					'locationews_settings_init',
					[
						'options' => $this->plugin->ln_get_user_options(),
					]
				);
			}

			if ( isset( get_current_screen()->post_type ) && in_array( get_current_screen()->post_type, $post_types ) ) {

				wp_enqueue_script(
					'locationews-metabox-init-js',
					$this->plugin->ln_get_url() . 'assets/js/locationews-metabox.' . ( $this->plugin->ln_get_enviroment() == 'dev' ? 'js' : 'min.js' ),
					[ 'jquery' ],
					$this->plugin->ln_get_version(),
					true
				);
			}

			wp_enqueue_script(
				'locationews-google-map-init-js',
				$this->plugin->ln_get_url() . 'assets/js/locationews-google-map-init.' . ( $this->plugin->ln_get_enviroment() == 'dev' ? 'js' : 'min.js' ),
				[ 'jquery' ],
				$this->plugin->ln_get_version(),
				true
			);

			wp_localize_script(
				'locationews-google-map-init-js',
				'locationews_map_init',
				[
					'plugin_url'             => plugin_dir_url( __FILE__ ),
					'locationews_meta'       => $this->plugin->ln_get_post_meta(),
					'locationews_options'    => $this->plugin->ln_get_options(),
					'locationews_user'       => $this->plugin->ln_get_user_options(),
					'zoom'                   => $this->plugin->ln_get_option( 'gZoom', 'front' ),
					'icon'                   => $this->plugin->ln_get_url() . 'assets/img/' . $this->plugin->ln_get_option( 'gIcon', 'front' ),
					'map_search_placeholder' => __( 'Search location', $this->plugin->ln_get_slug() ),
				]
			);

			if ( false === $this->ln_check_registered_library( 'maps.googleapis' ) ) {
				wp_enqueue_script(
					'google-maps',
					'https://maps.googleapis.com/maps/api/js?key=' . $this->plugin->ln_get_option( 'gApiKey', 'front' ) . '&language=' . $this->plugin->ln_get_option( 'gLanguage', 'front' ) . '&region=' . $this->plugin->ln_get_option( 'gRegion', 'front' ) . '&libraries=places',
					[ 'jquery' ],
					$this->plugin->ln_get_version(),
					true
				);
			}

		}

	}

	/**
	 * Check registered library
	 *
	 * Check if script library is already registered.
	 *
	 * @since 2.0.0
	 *
	 * @param string $handle
	 *
	 * @return bool
	 */
	public function ln_check_registered_library( $handle = '' ) {

		global $wp_scripts;

		$library_already_registered = false;

		$registered = $wp_scripts->registered;

		foreach ( $registered as $script ) {

			// For each script, verify if src contains handle
			if ( strpos( $script->src, $handle ) !== false ) {

				$library_already_registered = true;

			}
		}

		return $library_already_registered;

	}
	
}
