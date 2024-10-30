<?php
/**
 * Locationews Options provider.
 *
 * @package   Locationews
 * @copyright Copyright (c) 2018, Locationews, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Options provider class.
 *
 * @package Locationews
 * @since   2.0.0
 */
class Locationews_Provider_Options extends Locationews_AbstractProvider {

	/**
	 * Register hooks
	 *
	 * Loads the text domain during the `plugins_loaded` action.
	 *
	 * @since 2.0.0
	 */
	public function ln_register_hooks() {
		add_action( 'admin_menu', [ $this, 'ln_add_plugin_page' ] );
		add_action( 'admin_init', [ $this, 'ln_settings_page_init' ] );
		add_action( 'admin_init', [ $this, 'ln_json_settings_page_init' ] );	
		add_filter( 'pre_update_option_locationews_json_settings', [ $this, 'ln_import_json_settings' ], 10, 2 );
		add_action( 'add_option_locationews_json_settings', function( $old_value, $value ) {
     		delete_option('locationews_json_settings');
		}, 10, 2);

	}

	/**
	 * Add plugin page
	 *
	 * Add settings page for plugin.
	 *
	 * @since 2.0.0
	 */
	public function ln_add_plugin_page() {
		add_menu_page(
			'Locationews',
			'Locationews',
			'manage_options',
			'locationews-settings',
			[ $this, 'ln_create_admin_page' ],
			'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAAYCAMAAAArvOYAAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACGVBMVEUAAADrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGSHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGiHrGSHrGSDrGB/rGiHrGB/rHyPtMTLuPzzrGiHrGSDvRkT3opn81c/95uLrGiHrGB/wVFL82dP////////82NPwVFLrGB/rGCDtNjX6zsjt8PDx8fH7zsjtNjXrFx70dnD8+PaKioylpKX39/f09PT09PX/////+/jzdXDrGiHrHiH3pZz8//94eHmZmZn39/iHh4lvb3Fzc3Ts7Oz3pZzrICL4q6H8//95eHqTkpTq6utiYWOTk5VYV1jQ0NH+///4q6HrGR71i4X8/fyCgoNPTk91dXdgYGK4ubpqaWuHiIrMzc33jYfrGB/vS0f85uHh4+TLy8zOz9DU1dbs7O3Y2drT1dbp08/wS0jrGiHrHSL0hX3+9vP/9/T1hn7rHSLrGSDsIyb0fXb82dP+9/T//Pn0fXbsIybrGSDrGSDrGyDuOjjyZV70dnHrGyDrGSDrGiHrGCDrFx7rFh7rGiGalQZVAAAAPXRSTlMAAAo5ZHEFQJ/f9vkMeegGfvZO7hC2ROt0+oiE/2f2MOAHmS/WTuABStwATOMBZfMIk/4m03f6LMoSewQexmgNFwAAAAFiS0dEAIgFHUgAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAAHdElNRQfhAwYHDTd7W4pMAAAA40lEQVQY023OPSvFARTH8e/3uP7ykFIWbDeLiaKsdpm9FkIYvBezshsV5Wa4i3gBBqU8lOFn+N/rKs5wOn0659cRENUkISA47lDyFcRpXW2ll7xFZ3VtuHOXvOqc66Or27w476YqoPp5nU45qfoByZRWOuWE6secqlZc3PGnnrrvuXDJ3RH5dp5OOfNL2pz/5RJgu0226/7gwzE9yqOy7MlQ9vJAharTpmmapqmDKiK4omcA+0k/FKRfdQgcVvUDYwDPC3W1daz3AQogvSqqegGQtm+YGwLQASCWaWEgUGYw/ZVvt+hf4QWLPjYAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTctMDMtMDZUMDc6MTM6NTUtMDU6MDBD8t61AAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE3LTAzLTA2VDA3OjEzOjU1LTA1OjAwMq9mCQAAAABJRU5ErkJggg==',
			76
		);

		add_submenu_page( 
		 	'locationews-settings', 
		 	__('Edit Settings', $this->plugin->ln_get_slug() ), 
		 	__('Edit Settings', $this->plugin->ln_get_slug() ),
		    'manage_options', 
		    'locationews-settings',
		    [ $this, 'ln_create_admin_page' ]
		);

		add_submenu_page( 
		 	'locationews-settings', 
		 	__('Import Settings', $this->plugin->ln_get_slug() ),
		 	__('Import Settings', $this->plugin->ln_get_slug() ),
		    'manage_options', 
		    'locationews-json-settings',
		    [ $this, 'ln_create_admin_page_2' ]
		);
	}

	/**
	 * Create admin page
	 *
	 * Create settings page for plugin.
	 *
	 * @since 2.0.0
	 */
	public function ln_create_admin_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="locationews-wp-plugin">
			<div class="wrap">
				<div id="ln-block">
					<div id="ln-title">
						<h1>Locationews</h1>
						<p>
							<?php _e( 'Locationews plugin publish your news to Locationews service. With these settings, you can specify the basic functions on the map selector which appear in the article edit view.', $this->plugin->ln_get_slug() ); ?>
						</p>
						<?php if ( $this->plugin->ln_get_option( 'jwt' ) == 'plugintest' ): ?>
							<p>
								<?php _e( 'Register your free account at <a href="https://locationews.com/en/" target="_blank">Locationews.com</a> and start publishing.', $this->plugin->ln_get_slug() ); ?>
							</p>
						<?php endif; ?>
					</div>
					<div id="ln-logo">
						<a href="https://www.locationews.com/en/"
						   target="_blank"
						   title="Locationews.com">
							<img
								src="<?php echo $this->plugin->ln_get_url(); ?>assets/img/icon.png"
								alt="Locationews"
								id="locationewslogo" class="pull-x-right"/>
						</a>
					</div>
				</div>
			</div>
			<div class="wrap">
				<?php
				settings_errors();
				?>
				<form method="post" action="options.php">
					<?php
					settings_fields( 'locationews_user' );
					do_settings_sections( 'locationews' );
					?>
					<input type="submit" id="locationews-save-btn"
					       class="locationews btn btn-danger"
					       value="<?php _e( 'Save Settings', $this->plugin->ln_get_slug() ); ?>">
				</form>
			</div>
		</div>
		<?php

	}

	/**
	 * Create admin page for JSON settings
	 *
	 * Create settings page for plugin.
	 *
	 * @since 2.0.2
	 */
	public function ln_create_admin_page_2() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="locationews-wp-plugin">
			<div class="wrap">
				<div id="ln-block">
					<div id="ln-title">
						<h1>Locationews</h1>
						<p>
							<?php _e( 'Download plugin settings from <a href="https://locationews.com/en/" target="_blank">Locationews.com</a> and copy contents of settings.json to textarea.', $this->plugin->ln_get_slug() ); ?>
						</p>
					</div>
					<div id="ln-logo">
						<a href="https://www.locationews.com/en/"
						   target="_blank"
						   title="Locationews.com">
							<img
								src="<?php echo $this->plugin->ln_get_url(); ?>assets/img/icon.png"
								alt="Locationews"
								id="locationewslogo" class="pull-x-right"/>
						</a>
					</div>
				</div>
			</div>
			<div class="wrap">
				<?php
				settings_errors();
				?>
				<form method="post" action="options.php">
					<?php
					settings_fields( 'locationews_json' );
					do_settings_sections( 'locationews_json' );
					?>
					<input type="submit" id="locationews-save-btn"
					       class="locationews btn btn-danger"
					       value="<?php _e( 'Import Settings', $this->plugin->ln_get_slug() ); ?>">
				</form>
			</div>
		</div>
		<?php

	}

	/**
	 * Settings page init
	 *
	 * Initialize plugin settings.
	 *
	 * @since 2.0.0
	 */
	public function ln_settings_page_init() {

		register_setting(
			'locationews_user',
			'locationews_user',
			[ $this, 'ln_settings_sanitize' ]
		);

		add_settings_section(
			'locationews-fields',
			__( 'Settings', $this->plugin->ln_get_slug() ),
			[ $this, 'ln_settings_section_info' ],
			'locationews'
		);

		/*
		add_settings_field(
			'locationewsCategory',
			__( 'Default Category', $this->plugin->ln_get_slug() ),
			[ $this->plugin, 'ln_field_select' ],
			'locationews',
			'locationews-fields',
			[
				'description' => __( 'Set the default category for Locationews articles. This function does not affect on the WordPress categories.', $this->plugin->ln_get_slug() ),
				'id'          => 'locationewsCategory',
				'value'       => '',
				'fields'      => $this->plugin->ln_get_categories(),
			]
		);
		*/
		
		add_settings_field(
			'defaultCategories',
			__( 'Categories', $this->plugin->ln_get_slug() ),
			[ $this->plugin, 'ln_field_multicheckbox' ],
			'locationews',
			'locationews-fields',
			[
				'description' => __( 'Select WordPress Categories whose news you want to post to Locationews.', $this->plugin->ln_get_slug() ),
				'id'          => 'defaultCategories',
				'value'       => 'all',
				'fields'      => $this->plugin->ln_get_wp_categories(),
			]
		);

		add_settings_field(
			'postTypes',
			__( 'Post types', $this->plugin->ln_get_slug() ),
			[ $this->plugin, 'ln_field_multicheckbox' ],
			'locationews',
			'locationews-fields',
			[
				'description' => __( 'Choose which post types you want to allow use Locationews. The default option is normal post type.', $this->plugin->ln_get_slug() ),
				'id'          => 'postTypes',
				'value'       => 'post',
				'fields'      => $this->plugin->ln_get_wp_post_types(),
			]
		);

		add_settings_field(
			'location',
			__( 'Default location', $this->plugin->ln_get_slug() ),
			[ $this->plugin, 'ln_field_google_map' ],
			'locationews',
			'locationews-fields',
			[
				'description' => __( "Select the default location (the default option is your publication's address, here you can choose another location).", $this->plugin->ln_get_slug() ),
				'id'          => 'location',
				'value'       => $this->plugin->ln_get_option( 'location', 'user' ),
			]
		);

	}

	/**
	 * JSON settings page init
	 *
	 * Initialize plugin settings for JSON import.
	 *
	 * @since 2.0.2
	 */
	public function ln_json_settings_page_init() {

		register_setting('locationews_json', 'locationews_json_settings', [ $this, 'ln_json_settings' ] );
		
		add_settings_section(
			'locationews-json',
			__( 'Import Settings', $this->plugin->ln_get_slug() ),
			[ $this, 'ln_json_settings_section_info' ],
			'locationews_json'
		);

		add_settings_field(
			'locationews_json_settings',
			__( 'Contents of settings.json', $this->plugin->ln_get_slug() ), 
			[ $this->plugin, 'ln_field_textarea' ],
			'locationews_json',
			'locationews-json',
			[
				'description' => __('Copy and paste contents of settings.json here.' , $this->plugin->ln_get_slug() ),
				'id'		  => 'locationews_json_settings',
				'name'		  => 'locationews_json_settings',
				'value' 	  => ''
			]
		);

	}

	/**
	 * Settings sanitize
	 *
	 * Sanitize plugin settings.
	 *
	 * @since 2.0.0
	 *
	 * @param $input
	 *
	 * @return array
	 */
	public function ln_settings_sanitize( $input ) {

		$sanitary_values = [];
		if ( isset( $input['locationewsCategory'] ) ) {
			$sanitary_values['locationewsCategory'] = $input['locationewsCategory'];
		}

		if ( isset( $input['defaultCategories'] ) ) {
			$sanitary_values['defaultCategories'] = $input['defaultCategories'];
		}

		if ( isset( $input['postTypes'] ) ) {
			$sanitary_values['postTypes'] = $input['postTypes'];
		}

		if ( isset( $input['location'] ) ) {
			$sanitary_values['location'] = sanitize_text_field( $input['location'] );
		}

		return $sanitary_values;
	}

	/**
	 * Settings section info
	 *
	 * Dummy callback function.
	 *
	 * @since 2.0.0
	 */
	public function ln_settings_section_info() {
	}

	/**
	 * JSON settings section info
	 *
	 * Dummy callback function.
	 *
	 * @since 2.0.2
	 */
	public function ln_json_settings_section_info() {
	}

	/**
	 * Validate JSON if you wish
	 * 
	 * @since 2.0.2
	 */
	public function ln_json_settings( $input ) {
		return $input;
	}

	/**
	 * Import JSON settings
	 *
	 * Import publication settings.
	 *
	 * @since 2.0.2
	 */
	public function ln_import_json_settings( $new_value, $old_value ) {

		if ( trim( $new_value ) == '' ) {
			wp_die( __( "Settings can't be empty", 'locationews' ), 'Plugin dependency check', array('back_link' => true ) );
		}

		$settings = json_decode( $new_value, true );

		if ( is_array( $settings ) ) {

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
				}
			}

			update_option( 'locationews_options', $settings );

			$user_options = array(
                'defaultCategories'    => $settings['defaultCategories'],
                'postTypes'            => $settings['postTypes'],
                'location'             => $settings['location']
            );
            if ( isset( $settings['switch'] ) ) {
                $user_options['switch'] = $settings['switch'];
            }
			update_option('locationews_user',  $user_options );


		} else {
			wp_die( __( "Invalid JSON format.", 'locationews' ), 'Plugin dependency check', array('back_link' => true ) );
		}

		return '';
	}

}
