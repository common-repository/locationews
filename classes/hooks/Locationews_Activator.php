<?php
/**
 * Fired during plugin activation
 *
 * @link       http://www.locationews.com
 * @since      1.0.0
 */
class Locationews_Activator {

	/**
	 * Activate plugin
	 *
	 * @param $network_wide
	 */
	public static function activate( $network_wide ) {

		global $wpdb;

		// We need to have Curl library
		if ( ! function_exists('curl_init') ) {
			wp_die( __( "cURL library is required. Can't use the plugin without it.", 'locationews' ), 'Plugin dependency check', array('back_link' => true ) );
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			wp_die( __('Locationews 2 requires PHP 5.6 or higher.', 'locationews'), 'Plugin dependency check', array('back_link' => true ) );
		}

		// Do we have valid settings
		$settings = self::get_config();
		
		if ( empty($settings)) {
			wp_die( __( "Can't read required settings", 'locationews' ), 'Plugin dependency check', array( 'back_link' => true ) );
		}

		// Should be associative array
		if ( is_array( $settings ) ) {

			// See if being activated on the entire network or one blog
			if ( is_multisite() && $network_wide ) {

				// Is multisite
				// Get current blog id so we can switch back to it later
				$current_blog = $wpdb->blogid;

				// For storing the list of activated blogs
				$activated = array();

				// Get all blogs in the network and activate plugin on each one
				$blog_ids = $wpdb->get_col( "SELECT `blog_id` FROM `$wpdb->blogs`" );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );

					// Activate blog
					self::activate_site( $settings );

					// Store blog id
					$activated[] = $blog_id;
				}

				// Switch back to the current blog
				switch_to_blog( $current_blog );

				// Store newwork wide options for later use
				update_site_option('locationews_options', $settings );

				// Store the array for a later function
				update_site_option('locationews_activated', $activated );

			} else {
				// Single install
				self::activate_site( $settings );
			}

			// Remove config settings
			self::remove_config();

		} else {
			// Can't read settings
			wp_die( __( "Can't read required settings", 'locationews' ), 'Plugin dependency check', array( 'back_link' => true ) );
		}

	}

	/**
	 * Check activated multisites
	 *
	 * @return bool
	 */
	public function check_activated_multisite() {
		global $wpdb;

		// Get this so we can switch back to it later
		$current_blog = $wpdb->blogid;

		// If the option does not exist, plugin was not set to be network active
		if ( false === get_site_option('locationews_activated') ) {
			return false;
		}

		// Get network wide options
		$settings = get_site_option('locationews_options');

		// Now compare the stored value to the current value
		$activated = get_site_option('locationews_activated'); // An array of blogs with the plugin activated


		$blog_ids = $wpdb->get_col("SELECT `blog_id` FROM `$wpdb->blogs`");
		foreach ( $blog_ids as $blog_id ) {

			// Plugin is not activated on that blog
			if ( ! is_array( $activated ) || ! in_array( $blog_id, $activated ) ) {
				switch_to_blog( $blog_id );

				// Activate blog
				self::activate_site( $settings );

				// Store blog id
				$activated[] = $blog_id;
			}
		}

		// Switch back to the current blog
		switch_to_blog( $current_blog );

		// Save the list for later runs
		update_site_option('locationews_activated', $activated );
	}

	/**
	 * Add plugin options
	 *
	 * @param $settings
	 */
	public static function activate_site( $settings ) {

		// Insert config settings to database
		if ( update_option('locationews_options', $settings ) ) {

			// Save user editable settings to different option meta
            $user_options = array(
                'defaultCategories'    => $settings['defaultCategories'],
                'postTypes'            => $settings['postTypes'],
                'location'             => $settings['location']
            );
            if ( isset( $settings['switch'] ) ) {
                $user_options['switch'] = $settings['switch'];
            }
			update_option('locationews_user',  $user_options );
        }
	}

	/**
	 * Get plugin config settings
	 *
	 * @return array|mixed|object
	 */
	public static function get_config() {

		// Do we have a valid settings file
		if ( file_exists( LOCATIONEWS_BASE_DIR . 'settings.json') || file_exists( LOCATIONEWS_BASE_URL . 'settings.json') ) {
			
			//  Read config settings
			$settings_json = file_get_contents( LOCATIONEWS_BASE_DIR . 'settings.json');
			$settings = json_decode( $settings_json, true );

			if ( ! is_array( $settings ) || empty( $settings ) ) {
				$settings_json = file_get_contents( LOCATIONEWS_BASE_URL . 'settings.json');
				$settings = json_decode( $settings_json, true );
			}

			// If the data is a valid array
			if ( is_array( $settings ) ) {
				// Return settings merged with default settings
				return self::merge_defaults( $settings );
			} else {
				// Can't read config settings
				wp_die( __( 'Required settings are invalid', 'locationews' ) . ' (' . LOCATIONEWS_BASE_URL . 'settings.json).', 'Plugin dependency check', array('back_link' => true ) );
			}

		} elseif ( false != get_option('locationews_options') ) {
			// So we don't have a valid settings file.
			// But it seems that we had a previous install of the plugin and have stored the settings before
			// and the plugin has not been uninstalled.
			$settings = get_option('locationews_options');

			// Return old settings
			return $settings;

		} elseif ( false != get_option('_locationews_trash') ) {
			// So we don't have a valid settings file.
			// But it seems that we had a previous install of the plugin and have stored the settings before
			$settings = get_option('_locationews_trash');

			// Delete old data
			delete_option('_locationews_trash');

			// Return old settings
			return $settings;

		}
		
		return self::merge_defaults();
	}

	/**
	 * Add default settings and merge with publication settings
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public static function merge_defaults( $settings = array() ) {

		// We may add some default settings also
		if ( file_exists( LOCATIONEWS_BASE_DIR . 'defaults.json' ) || file_exists( LOCATIONEWS_BASE_URL . 'defaults.json' ) ) {

			$defaults_json = file_get_contents( LOCATIONEWS_BASE_DIR . 'defaults.json');
			$defaults = json_decode( $defaults_json, true );

			if ( ! is_array( $defaults ) || empty( $defaults ) ) {
				$defaults_json = file_get_contents( LOCATIONEWS_BASE_URL . 'defaults.json');
				$defaults = json_decode( $defaults_json, true );
			}

			// If we have default settings, let's merge them with config settings
			if ( is_array( $defaults ) && ! empty( $defaults ) ) {
				$settings = array_merge( $defaults, $settings );
			} else {
				$settings = array_merge( 
					array(
						'defaultCategories' => array('all' => 1),
						'postTypes'         => array('post' => 1),
						'location'          => '60.192059,24.945831'
					),
					$settings
				);
			}
		}
		return $settings;
	}

	/**
	 * Try to remove settings.json
	 */
	public static function remove_config() {
		// If settings file exists
		if ( file_exists( LOCATIONEWS_BASE_DIR . 'settings.json')  && is_file( LOCATIONEWS_BASE_DIR . 'settings.json') ) {
			// Try to remove the file or reset the file
			if ( is_writable( LOCATIONEWS_BASE_DIR . 'settings.json') ) {
			    @unlink( LOCATIONEWS_BASE_DIR . 'settings.json' );
			} else {
				@file_put_contents( LOCATIONEWS_BASE_DIR . 'settings.json', '');
			}
		} elseif ( file_exists( LOCATIONEWS_BASE_URL . 'settings.json')  && is_file( LOCATIONEWS_BASE_URL . 'settings.json') ) {
			// Try to remove the file or reset the file
			if ( is_writable( LOCATIONEWS_BASE_URL . 'settings.json') ) {
			    @unlink( LOCATIONEWS_BASE_URL . 'settings.json' );
			} else {
				@file_put_contents( LOCATIONEWS_BASE_URL . 'settings.json', '');
			}
		}
	}
}
