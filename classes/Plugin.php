<?php
/**
 * Main plugin.
 *
 * @package   Locationews
 * @copyright Copyright (c) 2018, Locationews, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Main plugin class.
 *
 * @package Locationews
 * @since   2.0.0
 */
class Locationews_Plugin extends Locationews_AbstractPlugin {

	/**
	 * Locationews pre-configured options
	 *
	 * @since 2.0.0
	 * @var
	 */
	protected $options;

	/**
	 * User specified options
	 *
	 * @since 2.0.0
	 * @var
	 */
	protected $user_options;

	/**
	 * Frontend options
	 *
	 * @since 2.0.0
	 * @var
	 */
	protected $front_options;

	/**
	 * Meta key name
	 *
	 * @since 2.0.0
	 * @var
	 */
	protected $meta_name;

	/**
	 * Load the plugin.
	 *
	 * @since 2.0.0
	 */
	public function ln_load_plugin() {

	}

	/**
	 * Retrieve plugin options
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function ln_get_options() {
		return $this->options;
	}

	/**
	 * Set plugin options
	 *
	 * @since 2.0.0
	 *
	 * @return $this
	 */
	public function ln_set_options() {

		$this->options = get_option( 'locationews_options' );

		if ( empty( $this->options ) && is_multisite() ) {
			$this->options = get_site_option( 'locationews_options' );
		}


		$this->user_options = get_option( 'locationews_user' );

		$front_options = [];

		foreach (
			[
				'defaultCategories',
				'postTypes',
				'location',
				'gApiKey',
				'gLanguage',
				'gRegion',
				'gZoom',
				'gIcon',
				'image',
				'url',
				'themeColor',
				'lan',
			] as $option
		) {
			if ( isset( $this->options[ $option ] ) ) {
				$front_options[ $option ] = $this->options[ $option ];
			}
		}

		foreach (
			[
				'defaultCategories',
				'postTypes',
				'location',
			] as $option
		) {
			if ( isset( $this->user_options[ $option ] ) ) {
				$front_options[ $option ] = $this->user_options[ $option ];
			}
		}

		$this->front_options = $front_options;

		if ( strpos( $this->options['apiUrl'], 'api_dev' ) !== false ) {
			$meta_name = 'locationews_dev';
		} else {
			$meta_name = 'locationews';
		}

		$this->meta_name = $meta_name;

		return $this;
	}

	/**
	 * Retrieve user options
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function ln_get_user_options() {
		return $this->user_options;
	}

	/**
	 * Retrieve front options
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function ln_get_front_options() {
		return $this->front_options;
	}


	/**
	 * Get option value
	 *
	 * @since 2.0.0
	 *
	 * @param string $key
	 * @param string $name
	 *
	 * @return null
	 */
	public function ln_get_option( $key = '', $name = '' ) {

		switch ( $name ) {
			case 'user':
				$option_field = 'user_options';
				break;
			case 'front':
				$option_field = 'front_options';
				break;
			default:
				$option_field = 'options';
		}

		if ( isset( $this->{$option_field}[ $key ] ) ) {
			return $this->{$option_field}[ $key ];
		}

		return null;

	}

	/**
	 * Retrieve meta key name
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function ln_get_meta_name() {
		return $this->meta_name;
	}

	/**
	 * Get post meta
	 *
	 * Get meta data for post
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function ln_get_post_meta( $id = null ) {
		global $post;

		if ( ! empty( $id ) ) {
			$post_meta = get_post_meta( $id, $this->ln_get_meta_name(), true );
		} elseif ( isset( $post->ID ) ) {
			$post_meta = get_post_meta( $post->ID, $this->ln_get_meta_name(), true );
		}

		if ( is_array( $post_meta ) ) {
			if ( ! isset( $post_meta['latlng'] ) ) {
				//$post_meta['latlng'] = '';
			}

			$meta_keys = [ 'id', 'on', 'ads', 'showmore', 'category', 'authors', 'latlng', 'api' ];

			if ( is_array( $post_meta ) ) {
				foreach ( $post_meta as $post_meta_key => $post_meta_value ) {

					if ( ! in_array( $post_meta_key, $meta_keys ) ) {
						unset( $post_meta[ $post_meta_key ] );
					} else {
						$post_meta[ $post_meta_key ] = $this->ln_validate_meta( $post_meta_key, $post_meta_value );
					}

				}
			}
		} else {
			$post_meta = array();
		}

		
		return $post_meta;

	}

	/**
	 * Validate meta value
	 * 
	 * @param  string $key   [description]
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function ln_validate_meta( $key = '', $value = '' ) {

		switch ( $key ) {
			// only numbers
			case 'id':
			case 'on':
			case 'ads':
			case 'showmore':
			case 'category':
			default:
				return trim( preg_replace("/[^0-9]/", "", $value ) );
				break;

			// string
			case 'authors':
			case 'api':
				return trim( filter_var( $value, FILTER_SANITIZE_STRING ) );
				break;

			// coordinates
			case 'latlng':
				if ( preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $value ) ) {
					return $value;
				} else {
					return '';
				}
				break;

			case 'geotags':
				if ( is_array( $value ) ) {
					return $value;
				} else {
					return false;
				}
				break;
		}

	}

	/**
	 * Get Locationews categories
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function ln_get_categories() {

		if ( get_locale() == 'fi' ) {
			$lan = 'fi';
		} else {
			$lan = 'en';
		}

		$transient_field = 'locationews_categories_' . $lan;
		$transient_age   = 604800; // 7 days


		if ( false === ( $categories = get_transient( $transient_field ) ) || empty( $categories ) ) {

			$api_categories = $this->ln_get_api_categories( $lan );
			$api_categories = json_decode( $api_categories, true );

			if ( $api_categories ) {
				foreach ( $api_categories as $category ) {
					$categories[] = [
						'name'  => __( $category['name'], $this->ln_get_slug() ),
						'value' => $category['id'],
					];
				}

				set_transient( $transient_field, $categories, $transient_age );

			} else {

				$categories = [];
				if ( is_array( $this->options['categories'] ) ) {
					foreach ( $this->options['categories'] as $category ) {
						$categories[] = [
							'name'  => __( $category['name'], $this->ln_get_slug() ),
							'value' => $category['id'],
						];
					}
				}
			}

			usort( $categories, function ( $a, $b ) {
				return strcmp( $a['name'], $b['name'] );
			} );

			set_transient( $transient_field, $categories, $transient_age );
		}

		return $categories;
	}

	/**
	 * Get WP categories
	 *
	 * Retrieve registered categories.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function ln_get_wp_categories() {

		$categories[] = [
			'id'          => 'locationews_categories_all',
			'name'        => 'locationews_user[defaultCategories][all]',
			'description' => __( 'All', 'locationews' ),
			'value'       => 'all',
			'value_id'    => 0,
		];

		$wp_categories = get_categories( [
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => '0',
		] );

		foreach ( $wp_categories as $category ) {
			$categories[] = [
				'id'          => 'locationews_categories_' . $category->slug,
				'name'        => 'locationews_user[defaultCategories][' . $category->slug . ']',
				'description' => __( $category->name, 'locationews' ),
				'value'       => $category->slug,
				'value_id'    => $category->term_id,
			];
		}

		return $categories;

	}

	/**
	 * Get WP post types
	 *
	 * Retrieve registered post types.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function ln_get_wp_post_types() {

		$post_types = [];

		$i = 0;

		foreach (
			get_post_types( [
				'public'  => true,
				'show_ui' => true,
			], 'names' ) as $post_type
		) {
			if ( $post_type != 'attachment' ) {
				$post_types[ $i ] = [
					'id'          => 'post_types-' . $post_type,
					'name'        => 'locationews_user[postTypes][' . $post_type . ']',
					'description' => __( $post_type, 'locationews' ),
					'value'       => $post_type,
				];

				$i ++;
			}
		}

		return $post_types;
	}

	/**
	 * Field Select
	 *
	 * Creates select field.
	 *
	 * @since 2.0.0
	 *
	 * @param $args
	 */
	public function ln_field_select( $args ) {

		$defaults = [
			'name'  => 'locationews_user[' . $args['id'] . ']',
			'value' => '',
		];

		$atts = wp_parse_args( $args, $defaults );

		if ( ! empty( $this->user_options[ $atts['id'] ] ) ) {
			$atts['value'] = $this->user_options[ $atts['id'] ];
		}
		?>
		<div class="form-group locationews-form-group">
			<div class="ln-tooltip"
			     title="<?php echo esc_attr( $atts['description'] ); ?>">
				<label for="<?php echo esc_attr( $atts['id'] ); ?>"></label>
				<select id="<?php echo esc_attr( $atts['id'] ); ?>"
				        name="<?php echo esc_attr( $atts['name'] ); ?>"
				        class="form-control">
					<?php foreach ( $atts['fields'] as $key => $field ): ?>
						<option
							value="<?php echo esc_attr( $field['value'] ); ?>"<?php if ( $field['value'] == $atts['value'] ) {
							echo ' selected="selected"';
						} elseif ( empty( $atts['value'] ) && $field['value'] == '1' ) {
							echo ' selected="selected"';
						} ?>><?php echo $field['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php
	}

	/**
	 * Field multicheckbox
	 *
	 * Creates multiple checkbox fields.
	 *
	 * @since 2.0.0
	 *
	 * @param $args
	 */
	public function ln_field_multicheckbox( $args ) {

		$atts = wp_parse_args( $args, [] );

		if ( ! empty( $this->user_options[ $atts['id'] ] ) ) {
			$atts['value'] = $this->user_options[ $atts['id'] ];
		}
		?>
		<div class="form-group locationews-form-group">
			<div class="ln-tooltip"
			     title="<?php echo esc_attr( $atts['description'] ); ?>">
				<?php
				$all = false;
				foreach ( $atts['fields'] as $key => $field ):
					if ( $atts['id'] == 'defaultCategories' ) {
						
						$user_categories = [];
						if ( is_array( $this->user_options['defaultCategories'] ) ) {
							$user_categories = array_keys( $this->user_options['defaultCategories'] );	
						}

						if ( in_array( 'all', $user_categories ) ) {
							$all = true;
						}
						if ( $all === true ) {
							$this->user_options[ $atts['id'] ][ $field['value'] ] = 1;
						}
					}
					?>
					<label for="<?php echo esc_attr( $field['id'] ); ?>">

						<input data-size="small"
						       <?php if ( strlen( $field['description'] ) >= 30 ): ?>
						       data-label-width="180"
						       data-label-text="<?php echo esc_attr( substr( $field['description'], 0, 30 ) ); ?>"
						       <?php elseif ( strlen( $field['description'] ) >= 20 ): ?>
						       data-label-width="150"
						       data-label-text="<?php echo esc_attr( $field['description'] ); ?>"
							   <?php elseif ( strlen( $field['description'] ) >= 15 ): ?>
						       data-label-width="100"
						       data-label-text="<?php echo esc_attr( $field['description'] ); ?>"
						       <?php elseif ( strlen( $field['description'] ) >= 10 ): ?>
						       data-label-width="100"
						       data-label-text="<?php echo esc_attr( $field['description'] ); ?>"
						       <?php else: ?>
						       data-label-text="<?php echo esc_attr( $field['description'] ); ?>"
				               <?php endif; ?>
						       data-on-color="default" data-off-color="default"
						       data-on-text="ON" data-off-text="OFF"
						       aria-role="checkbox"
						       class="locationews locationews-<?php echo $atts['id']; ?>" <?php isset( $this->user_options[ $atts['id'] ][ $field['value'] ] ) ? checked( 1, $this->user_options[ $atts['id'] ][ $field['value'] ], true ) : null ?>
						       id="<?php echo esc_attr( $field['id'] ); ?>"
						       name="<?php echo esc_attr( $field['name'] ); ?>"
						       type="checkbox" value="1"/>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Field Input
	 *
	 * Creates input field.
	 *
	 * @since 2.1.0
	 *
	 * @param $args
	 */
	public function ln_field_input( $args ) {

		$defaults = [
			'name'  => 'locationews_user[' . $args['id'] . ']',
			'value' => '',
			'id' => '',
			'description' => '',
		];

		$atts = wp_parse_args( $args, $defaults );

		if ( ! empty( $this->user_options[ $atts['id'] ] ) ) {
			$atts['value'] = $this->user_options[ $atts['id'] ];
		}
		?>
		<div class="form-group locationews-form-group">
			<div class="ln-tooltip"
			     title="<?php echo esc_attr( $atts['description'] ); ?>">
				<label for="<?php echo esc_attr( $atts['id'] ); ?>">
					<input type="text" id="<?php echo esc_attr( $atts['id'] ); ?>" name="<?php echo esc_attr( $atts['name'] ); ?>" class="form-control" value="<?php echo esc_attr( $atts['value'] ); ?>" placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>" />
				<?php echo esc_attr( $atts['description'] ) ?></label>
			</div>
		</div>
		<?php
	}

	/**
	 * Field textarea
	 *
	 * Creates textarea field.
	 *
	 * @since 2.0.2
	 *
	 * @param $args
	 */
	public function ln_field_textarea( $args ) {

		$defaults = [
			'name'  => '',
			'value' => '',
			'id' => '',
			'description' => '',
		];

		$atts = wp_parse_args( $args, $defaults );

		if ( ! empty( $this->user_options[ $atts['id'] ] ) ) {
			$atts['value'] = $this->user_options[ $atts['id'] ];
		}
		?>
		<div class="form-group locationews-form-group">
			<label for="<?php echo esc_attr( $atts['id'] ); ?>">
				<textarea id="<?php echo esc_attr( $atts['id'] ); ?>" name="<?php echo esc_attr( $atts['name'] ); ?>" class="form-control" rows="5" cols="100"><?php echo esc_attr( $atts['value'] ); ?></textarea>
				<?php echo esc_attr( $atts['description'] ) ?>
			</label>
		
		</div>
		<?php
	}

	/**
	 * Field Google Map
	 *
	 * Creates a Google Map location field.
	 *
	 * @since 2.0.0
	 *
	 * @param $args
	 */
	public function ln_field_google_map( $args ) {

		$defaults = [
			'class'       => 'gllpLatlonPicker',
			'description' => '',
			'name'        => 'locationews_user[' . $args['id'] . ']',
		];

		$atts = wp_parse_args( $args, $defaults );

		if ( ! empty( $this->user_options[ $atts['id'] ] ) ) {
			$atts['value'] = $this->user_options[ $atts['id'] ];
		}
		?>
		<div class="form-group locationews-form-group">
			<div class="ln-tooltip"
			     title="<?php echo esc_attr( $atts['description'] ); ?>">
				<input id="locationews-pac-input" class="controls" type="text"
				       placeholder="<?php _e( 'Search location', 'locationews' ); ?>">
				<div id="locationews-google-map"
				     class="locationews-google-map <?php echo esc_attr( $atts['class'] ); ?>"></div>
				<input type="text" id="locationews-location"
				       name="<?php echo esc_attr( $atts['name'] ); ?>"
				       value="<?php echo esc_attr( $atts['value'] ); ?>"
				       placeholder="<?php echo esc_attr( isset( $atts['placeholders']['gllpLatitudeLongitude'] ) ? $atts['placeholders']['gllpLatitudeLongitude'] : '' ); ?>"
				       class="form-control gllpLatitudeLongitude text widefat"/>
			</div>
		</div>
		<?php
	}

	public function ln_get_widget_sizes() {
		$size = [];

		$i = 50;
		while( $i <= 500 ) {

			$size[] = [
				'id' => $i . ' px',
				'name' => $i . ' px',
				'value' => $i . ' px'
			];

			$i = ( $i + 25 );
		}

		return $size;

	}

	/**
	 * API Call
	 *
	 * Send article data to API.
	 *
	 * @since 2.0.0
	 *
	 * @param $action
	 * @param array $data
	 *
	 * @return array
	 */
	public function ln_api_call( $action, $data = [] ) {
		
		// Test use
		if ( $this->ln_get_option( 'jwt' ) == 'plugintest' ) {
			return [
				'success' => 1,
				'test'    => 1,
				'id'      => rand( 1, 1000 ),
				'action'  => $action,
			];
		}

		$args = [
			'headers'     =>
				[
					'Authorization' => 'Bearer ' . trim( $this->ln_get_option( 'jwt' ) ),
				],
			'method'      => 'POST',
			'timeout'     => '30',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'sslverify'   => $this->ln_get_enviroment() == 'dev' ? false : true,
			'body'        => $data,
		];

		switch ( $action ) {
			case 'add':
				$response = wp_remote_post( $this->ln_get_option( 'apiUrl' ) . '/news', $args );
				break;
			case 'update':
				$response = wp_remote_post( $this->ln_get_option( 'apiUrl' ) . '/news/' . $data['Id'], $args );
				break;
			case 'delete':
				$args['method'] = 'DELETE';
				$response       = wp_remote_request( $this->ln_get_option( 'apiUrl' ) . '/news/' . $data['Id'], $args );
				break;
		}

		if ( ! $response || ! is_array( $response ) ) {
			return [
				'error'  => 1,
				'msg'    => 'locationews response error: no response',
				'action' => $action,
			];
		}

		$data = json_decode( $response['body'], true );

		if ( isset( $data['response']['status'] ) && $data['response']['status'] == 'ERROR' ) {
			return [
				'error'  => 1,
				'msg'    => 'locationews response error: ' . $data['response']['errormessage'],
				'action' => $action,
			];

		} else {

			if ( isset( $data['errors'] ) ) {
				$errorcodes = [];
				foreach ( $data['errors'] as $key => $err ) {
					$errorcodes[] = $err['code'];
				}

				return [
					'error'  => 1,
					'msg'    => urlencode( implode( ',', $errorcodes ) ),
					'action' => $action,
				];

			} else {
				return [
					'success' => '1',
					'msg'     => $data['message'],
					'id'      => isset( $data['id'] ) ? $data['id'] : null,
					'action'  => $action,
				];
			}

		}

	}

	/**
	 * Get categories
	 *
	 * Retrieve categories from API.
	 *
	 * @since 2.0.0
	 *
	 * @param string $lan
	 *
	 * @return string
	 */
	public function ln_get_api_categories( $lan = 'en' ) {

		$response = wp_remote_get( $this->ln_get_option( 'apiUrl' ) . '/categories?lan=' . $lan );

		if ( is_array( $response ) ) {
			return $response['body'];
		}

		return null;
	}

	/**
	 * Get success message
	 *
	 * Return success message
	 *
	 * @since 2.0.0
	 *
	 * @param bool $code
	 *
	 * @return bool|mixed
	 */
	public function ln_get_success_message( $code = false ) {
		$success = [
			1   => __( 'Post successfully added', $this->ln_get_slug() ),
			2   => __( 'Post successfully updated', $this->ln_get_slug() ),
			3   => __( 'Post deleted from Locationews', $this->ln_get_slug() ),
			101 => __( 'Post successfully added. <strong>Note that this is just a testing enviroment. Your article was not really posted to Locationews API.</strong> Register your free account at <a href="https://locationews.com/en/" target="_blank">Locationews.com</a>', $this->ln_get_slug() ),
			102 => __( 'Post successfully updated. <strong>Note that this is just a testing enviroment. Your article was not really posted to Locationews API.</strong> Register your free account at <a href="https://locationews.com/en/" target="_blank">Locationews.com</a>', $this->ln_get_slug() ),
			103 => __( 'Post deleted from Locationews. <strong>Note that this is just a testing enviroment. Your article was not really posted to Locationews API.</strong> Register your free account at <a href="https://locationews.com/en/" target="_blank">Locationews.com</a>', $this->ln_get_slug() ),
		];

		if ( false != $code && isset( $success[ $code ] ) ) {
			return $success[ $code ];
		} else {
			return false;
		}
	}

	/**
	 * Get error message
	 *
	 * Return error message
	 *
	 * @since 2.0.0
	 *
	 * @param bool $code
	 *
	 * @return bool|mixed
	 */
	public function ln_get_error_message( $code = false ) {
		$errors = [
			0  => __( 'General error', $this->ln_get_slug() ),
			1  => __( 'Invalid URL or request method', $this->ln_get_slug() ),
			2  => __( 'No Authorization header', $this->ln_get_slug() ),
			3  => __( 'JWT error', $this->ln_get_slug() ),
			4  => __( 'Missing required parameter', $this->ln_get_slug() ),
			5  => __( 'Forbidden parameter', $this->ln_get_slug() ),
			6  => __( 'Invalid value for parameter', $this->ln_get_slug() ),
			7  => __( 'Insufficient privileges to perform action', $this->ln_get_slug() ),
			8  => __( 'No news id', $this->ln_get_slug() ),
			9  => __( 'No user id', $this->ln_get_slug() ),
			10 => __( 'User not found', $this->ln_get_slug() ),
			11 => __( 'Error uploading image', $this->ln_get_slug() ),
			12 => __( 'Unable to delete file since the file does not exist', $this->ln_get_slug() ),
			13 => __( 'Image file not deleted since it does not belong to this news', $this->ln_get_slug() ),
			14 => __( 'Saving news data failed', $this->ln_get_slug() ),
			15 => __( 'No publication id', $this->ln_get_slug() ),
			16 => __( 'Publication not found', $this->ln_get_slug() ),
			17 => __( 'Error processing image', $this->ln_get_slug() ),
			18 => __( 'Error resizing image', $this->ln_get_slug() ),
			19 => __( 'Error saving resized image', $this->ln_get_slug() ),
			20 => __( 'Error saving image thumbnail', $this->ln_get_slug() ),
			21 => __( 'Error copying image, file not found', $this->ln_get_slug() )
		];

		if ( false != $code && isset( $errors[ $code ] ) ) {
			return $errors[ $code ];
		} else {
			return false;
		}
	}

	/**
	 * Show notice
	 *
	 * Show admin notice
	 *
	 * @since 2.0.0
	 *
	 * @param $message
	 * @param string $type
	 */
	public function ln_show_notice( $message, $type = 'error' ) {
		?>
		<div class="<?php echo esc_attr( $type ); ?> below-h2">
			<p><strong>Locationews</strong></p>
			<?php
			if ( is_array( $message ) ):
				?>
				<ul>
					<?php
					foreach ( $message as $msg ):
						if ( ! is_array( $msg ) ):
							?>
							<li><?php echo $msg; ?>.</li>
						<?php
						endif;
					endforeach;
					?>
				</ul>
			<?php
			else:
				?>
				<p><?php echo $message; ?>.</p>
			<?php
			endif;
			?>
		</div>
		<?php
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
