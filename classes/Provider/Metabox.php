<?php
/**
 * Locationews Metabox provider.
 *
 * @package   Locationews
 * @copyright Copyright (c) 2018, Locationews, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Metabox provider class.
 *
 * @package Locationews
 * @since   2.0.0
 */
class Locationews_Provider_Metabox extends Locationews_AbstractProvider {

	/**
	 * Meta data
	 *
	 * @var
	 */
	private $locationews_meta;

	private $show_metabox;

	private $show_metabox_always;

	private $active_categories;

	/**
	 * Register hooks
	 *
	 * Loads the text domain during the `plugins_loaded` action.
	 *
	 * @since 2.0.0
	 */
	public function ln_register_hooks() {
		add_action( 'add_meta_boxes', [ $this, 'ln_add_meta_box' ], 999 );
		add_action( 'save_post', [ $this, 'ln_save_post' ] );
		add_action( 'publish_future_post', [ $this, 'ln_save_post' ] );
		add_action( 'transition_post_status', [
			$this,
			'ln_unpublished',
		], 10, 3 );
		add_action( 'before_delete_post', [ $this, 'ln_delete_post' ] );
		add_action( 'edit_form_top', [ $this, 'ln_admin_notices' ] );
	}

	/**
	 * Add meta box
	 *
	 * Creates a meta box.
	 *
	 * @since 2.0.0
	 */
	public function ln_add_meta_box() {
		add_meta_box(
			'locationews',
			__( 'Locationews', $this->plugin->ln_get_slug() ),
			[ $this, 'ln_metabox' ],
			array_keys( $this->plugin->ln_get_option( 'postTypes', 'user' ) ),
			'normal',
			'default'
		);
	}

	/**
	 * Metabox
	 *
	 * Format meta box fields and values
	 *
	 * @since 2.0.0
	 *
	 * @param $post
	 */
	public function ln_metabox( $post ) {

		if ( ! is_admin() || empty( $this->plugin->ln_get_option( 'postTypes', 'user' ) ) || ! in_array( $post->post_type, array_keys( $this->plugin->ln_get_option( 'postTypes', 'user' ) ) ) || empty( $this->plugin->ln_get_option( 'jwt' ) ) ) {
			return;
		}

		$this->show_metabox        = false;
		$this->show_metabox_always = false;
		$this->active_categories   = [];

		if ( $post->ID ) {
			$this->locationews_meta = $this->plugin->ln_get_post_meta( $post->ID );
		} else {
			$this->locationews_meta = [];
		}

		if ( ! isset( $this->locationews_meta['id'] ) ) {
			$this->locationews_meta['id'] = '';
		}

		if ( get_current_screen()->action == 'add' ) {
			$this->locationews_meta['on'] = '1';
		}

		if ( ! isset( $this->locationews_meta['on'] ) ) {
			$this->locationews_meta['on'] = '0';
		} elseif ( $this->locationews_meta['on'] == '1' ) {
			$this->show_metabox = true;
		}

		if ( ! isset( $this->locationews_meta['ads'] ) ) {
			$this->locationews_meta['ads'] = '1';
		}

		if ( ! isset( $this->locationews_meta['showmore'] ) ) {
			$this->locationews_meta['showmore'] = '';
		}

		if ( ! isset( $this->locationews_meta['category'] ) ) {
			$this->locationews_meta['category'] = ! empty( $this->plugin->ln_get_option( 'locationewsCategory', 'user' ) ) ? $this->plugin->ln_get_option( 'locationewsCategory', 'user' ) : $this->plugin->ln_get_option( 'locationewsCategory' );
		}

		if ( ! isset( $this->locationews_meta['authors'] ) ) {
			$author_id    = get_post_field( 'post_author', $post->ID );
			$display_name = get_the_author_meta( 'display_name', $author_id );
			if ( false === strpos( $display_name, '@' ) ) {
				$this->locationews_meta['authors'] = $display_name;
			} else {
				$current_user = wp_get_current_user();
				$display_name = $current_user->display_name;
				if ( false !== strpos( $display_name, '@' ) ) {
					$this->locationews_meta['authors'] = '';
				} else {
					$this->locationews_meta['authors'] = $current_user->display_name;
				}
			}
		}

		if ( ! isset( $this->locationews_meta['latlng'] ) ) {
			$this->locationews_meta['latlng'] = '';
		} elseif ( $this->locationews_meta['on'] == '0' && $this->locationews_meta['latlng'] == $this->plugin->ln_get_option('latlng', 'user') ) {
			$this->locationews_meta['latlng'] = '';
		}

		foreach ( $this->plugin->ln_get_option( 'defaultCategories', 'user' ) as $catname => $val ) {
			
			$cat = get_category_by_slug( $catname );
			if ( $cat ) {
				$this->active_categories[] = $cat->term_id;

				if ( in_array( $cat->term_id, wp_get_post_categories( $post->ID ) ) ) {
					$this->show_metabox = true;
				}
			}

			if ( $catname == 'all' ) {
				$this->show_metabox = true;
			}
		}

		if ( ! isset( $locationews_meta['api'] ) ) {
			$this->locationews_meta['api'] = $this->plugin->ln_get_option( 'apiUrl' );
		}

		if ( ! empty( $this->plugin->ln_get_option( 'defaultCategories', 'user' ) ) ) {
			if ( in_array( 'all', array_keys( $this->plugin->ln_get_option( 'defaultCategories', 'user' ) ) ) ) {
				$this->show_metabox_always = true;
			}
		}

		if ( isset( get_current_screen()->post_type ) && in_array( get_current_screen()->post_type, array_keys( $this->plugin->ln_get_option( 'postTypes', 'user' ) ) ) ) {
			wp_localize_script(
				'locationews-metabox-init-js',
				'locationews_metabox_init',
				[
					'action'				 => get_current_screen()->action,
					'post_type'              => $post->post_type,
					'display_metabox'        => $this->show_metabox,
					'display_metabox_always' => $this->show_metabox_always,
					'catids'                 => $this->active_categories,
					'locationewson'			 => $this->locationews_meta['on'],
				]
			);
		}

		$this->ln_generate_fields( $post );

	}


	/**
	 * Generate fields
	 *
	 * Generate meta box contents
	 *
	 * @since 2.0.0
	 *
	 * @param $post
	 */
	public function ln_generate_fields( $post ) {
		?>
		<div class="locationews-wp-plugin">
			<?php wp_nonce_field( 'save_locationews_meta', 'locationews-meta-box-nonce' ); ?>
			<input type="hidden" name="locationews_Id"
				   value="<?php echo esc_attr( $this->locationews_meta['id'] ); ?>"/>
			<input type="hidden" name="locationews_hidden" id="locationews_hidden" value="<?php echo $this->locationews_meta['on']; ?>" />
			<div class="row">
				<div class="col-md-4">
					<div class="form-group locationews-form-group">
		                <span class="ln-tooltip"
		                      title="<?php _e( 'When Locationews is enabled, the article will be automatically added to Locationews when you press the Publish. If you would like to take published article out of Locationews, change to Off and update the article.', $this->plugin->ln_get_slug() ); ?>">
		                    <label
			                    for="locationewson"><?php _e( 'Locationews enabled', $this->plugin->ln_get_slug() ); ?>
			                    <br>
		                        <input data-size="small" data-on-color="default"
		                               data-off-color="default"
		                               data-on-text="ON" data-off-text="OFF"
		                               aria-role="checkbox"
		                               class="locationews" <?php checked( 1, $this->locationews_meta['on'], true ); ?>
		                               id="locationewson" name="locationews"
		                               type="checkbox" value="1"/>
		                    </label>
		                </span>
					</div>
					<?php if ( ! empty( $this->plugin->ln_get_option( 'ads' ) ) ): ?>
						<div class="form-group locationews-form-group">
			                <span class="ln-tooltip"
			                      title="<?php _e( 'Enable Ads', $this->plugin->ln_get_slug() ); ?>">
			                    <label
				                    for="locationews_ads"><?php _e( 'Ads', $this->plugin->ln_get_slug() ); ?>
				                    <br>
			                        <input data-size="small"
			                               data-on-color="default"
			                               data-off-color="default"
			                               data-on-text="ON" data-off-text="OFF"
			                               aria-role="checkbox"
			                               class="locationews" <?php checked( 1, $this->locationews_meta['ads'], true ); ?>
			                               id="locationews_ads"
			                               name="locationews_ads"
			                               type="checkbox"
			                               value="1"/>
			                    </label>
			                </span>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $this->plugin->ln_get_option( 'showMore' ) ) ): ?>
						<div class="form-group locationews-form-group">
			                <span class="ln-tooltip"
			                      title="<?php _e( 'Enable show more', $this->plugin->ln_get_slug() ); ?>">
			                    <label
				                    for="locationews_showmore"><?php _e( 'Show more', $this->plugin->ln_get_slug() ); ?>
				                    <br>
			                        <input data-size="small"
			                               data-on-color="default"
			                               data-off-color="default"
			                               data-on-text="ON" data-off-text="OFF"
			                               aria-role="checkbox"
			                               class="locationews" <?php checked( 1, $this->locationews_meta['showmore'], true ); ?>
			                               id="locationews_showmore"
			                               name="locationews_showmore"
			                               type="checkbox" value="1"/>
			                    </label>
			                </span>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $this->plugin->ln_get_option( 'category' ) ) ): ?>
					<div class="form-group locationews-form-group clear">
		                <span class="ln-tooltip"
		                      title="<?php _e( 'Select a Locationews category for the news. This function does not affect to the WordPress categories.', $this->plugin->ln_get_slug() ); ?>">
		                <label
			                for="locationews_category"><?php _e( 'Category', $this->plugin->ln_get_slug() ); ?></label>
		                    <select id="locationews_category"
		                            name="locationews_category"
		                            class="form-control">
		                        <?php foreach ( $this->plugin->ln_get_categories() as $key => $cat ) {
			                        echo '<option value="' . $cat['value'] . '"';
			                        if ( $this->locationews_meta['category'] == $cat['value'] ) {
				                        echo ' selected="selected"';
			                        }
			                        echo '> ' . $cat['name'] . '</option>';
		                        }
		                        ?>
		                    </select>
		                </span>
					</div>
					<?php endif; ?>
					<div class="form-group locationews-form-group">
						<span class="ln-tooltip"
						      title="<?php _e( 'Article\'s authors', $this->plugin->ln_get_slug() ); ?>">
							<label
								for="locationews-authors"><?php _e( 'Authors', $this->plugin->ln_get_slug() ); ?></label>
							<input type="text" id="locationews-authors"
							       name="locationews_authors"
							       value="<?php echo $this->locationews_meta['authors']; ?>"
							       class="form-control text widefat"
							       maxlength="255"/>
						</span>
					</div>
					<div class="form-group locationews-form-group">
		                <span class="ln-tooltip"
		                      title="<?php _e( 'Coordinates', $this->plugin->ln_get_slug() ); ?>">
		                    <label
			                    for="locationews-location"><?php _e( 'Coordinates', $this->plugin->ln_get_slug() ); ?></label>
		                    <input type="text" id="locationews-location"
		                           name="locationews_coordinates"
		                           value="<?php echo $this->locationews_meta['latlng']; ?>"
		                           class="form-control gllpLatitudeLongitude text widefat"/>
		                </span>
					</div>
					<?php if ( ! empty( $this->plugin->ln_get_option( 'controlPanelUrl' ) ) ): ?>
						<div
							class="form-group locationews-form-group text-center">
							<a class="ln-tooltip"
							   href="<?php echo $this->plugin->ln_get_option( 'controlPanelUrl' ); ?>"
							   target="blank"
							   title="<?php _e( 'Show control panel', $this->plugin->ln_get_slug() ); ?>">
								<button type="button"
								        class="btn btn-danger locationews"><?php _e( 'Show control panel', $this->plugin->ln_get_slug() ); ?></button>
							</a>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-md-8 ln-tooltip"
				     title="<?php _e( 'Add your news location on the map by dragging the marker or double clicking on the map. You can also search location by address.', $this->plugin->ln_get_slug() ); ?>">
					<input id="locationews-pac-input" class="controls"
					       type="text"
					       placeholder="<?php _e( 'Search location', $this->plugin->ln_get_slug() ); ?>">
					<div id="locationews-google-map"
					     class="locationews-google-map"></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save post
	 *
	 * Save post metas & send to Locationews API
	 *
	 * @param $post_id
	 * @param string $post
	 * @param string $update
	 *
	 * @return mixed
	 */
	public function ln_save_post( $post_id ) {
		
		$response = false;

		// if this is scheduled future post, do not check these cases
		if ( current_filter() != 'publish_future_post' ) {
			if ( ! isset( $_POST['locationews-meta-box-nonce'] ) || ! wp_verify_nonce( $_POST['locationews-meta-box-nonce'], 'save_locationews_meta' ) || ! current_user_can( 'edit_post', $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				return $post_id;
			}
		}

		$post = get_post( $post_id );

		if ( isset( $_POST['locationews-meta-box-nonce'] ) ) {
			$post_meta = [
				'on'       => isset( $_POST['locationews'] ) ? 1 : 0,
				'ads'      => isset( $_POST['locationews_ads'] ) ? 1 : 0,
				'showmore' => isset( $_POST['locationews_showmore'] ) ? 1 : 0,
				'id'       => isset( $_POST['locationews_Id'] ) ? sanitize_text_field( $_POST['locationews_Id'] ) : null,
				'latlng'   => isset( $_POST['locationews_coordinates'] ) ? sanitize_text_field( $_POST['locationews_coordinates'] ) : null,
				'category' => isset( $_POST['locationews_category'] ) ? sanitize_text_field( $_POST['locationews_category'] ) : 1,
				'authors'  => isset( $_POST['locationews_authors'] ) ? sanitize_text_field( $_POST['locationews_authors'] ) : null,
				'api'      => $this->plugin->ln_get_option( 'apiUrl' ),
			];
		}

		$image_to_api = null;
		$image_url    = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		$url          = parse_url( $image_url );
		if ( is_array( $url ) ) {
			if ( isset( $url['scheme'] ) && isset( $url['host'] ) && isset( $url['path'] ) ) {
				$image_path   = explode( '/', $url['path'] );
				$image        = array_pop( $image_path );
				$image_to_api = $url['scheme'] . '://' . $url['host'] . implode( '/', $image_path ) . '/' . urlencode( $image );
			}
		}
		$caption = get_the_post_thumbnail_caption( $post_id );

		$data = [
			'Id'            => $this->plugin->ln_validate_meta( 'id', $post_meta['id'] ),
			'title'         => apply_filters( 'the_title', get_post_field( 'post_title', $post_id ) ),
			'text'          => apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ),
			'shortText'     => get_post_field( 'post_excerpt', $post_id ),
			'url'           => get_permalink( $post_id ),
			'image'         => $image_to_api,
			'caption'       => $caption,
			'authors'       => $this->plugin->ln_validate_meta( 'authors', $post_meta['authors'] ),
			'showMore'      => $this->plugin->ln_validate_meta( 'showmore', $post_meta['showmore'] ),
			'ads'           => $this->plugin->ln_validate_meta( 'ads', $post_meta['ads'] ),
			'publicationId' => $this->plugin->ln_get_option( 'id' ),
			'categoryId'    => $this->plugin->ln_validate_meta( 'category', $post_meta['category'] ),
		];

		if ( strpos( $post_meta['latlng'], ',' ) !== false && $this->plugin->ln_validate_meta( 'latlng', $post_meta['latlng'] ) ) {
			list( $data['latitude'], $data['longitude'] ) = explode( ',', $post_meta['latlng'] );
		} else {
			$data['latitude']  = '';
			$data['longitude'] = '';
		}
		if ( empty( $data['latitude'] ) || empty( $data['longitude'] ) ) {
			// Get possible GEOTAGGED data
			$geotagged_coordinates = $this->has_geotags( $post_id );
			if ( is_array( $geotagged_coordinates ) && isset( $geotagged_coordinates[0] ) ) {
				if ( $this->plugin->ln_validate_meta('latlng', $geotagged_coordinates[0] ) != '' ) {
					list( $data['latitude'], $data['longitude'] ) = explode( ',', $geotagged_coordinates[0] );
					$post_meta['latlng'] = $geotagged_coordinates[0];
					$post_meta['geotags'] = $geotagged_coordinates;
				}
			} 
		}
		
		foreach ( $post_meta as $post_meta_key => $post_meta_value ) {

			$post_meta[ $post_meta_key ] = $this->plugin->ln_validate_meta( $post_meta_key, $post_meta_value );
		}

		// is post published
		if ( 'publish' == $post->post_status ) {

			// Locationews is enabled
			if ( $post_meta['on'] == 1 ) {

				if ( empty( $post_meta['id'] ) ) {
					// add to Locationews
					if ( isset( $data['Id'] ) ) {
						unset( $data['Id'] );
					}
					$response = $this->plugin->ln_api_call( 'add', $data );

					// get Locationews Id
					if ( isset( $response['id'] ) ) {
						$post_meta['id'] = $response['id'];
					}

				} elseif ( $post_meta['id'] ) {
					// update to Locationews
					$response = $this->plugin->ln_api_call( 'update', $data );
				}

			} elseif ( $post_meta['id'] ) {
				// has Locationews Id but Locationews disabled, delete from Locationews
				$response = $this->plugin->ln_api_call( 'delete', $data );

				// set Locationews Id to false
				$post_meta['id'] = false;
			}
		}

		// if post is not published, set Locationews Id to false
		if ( 'publish' != $post->post_status ) {
			$post_meta['on'] = false;
			$post_meta['id'] = false;
		}

		// save Locationews post meta data
		update_post_meta( $post_id, $this->plugin->ln_get_meta_name(), $post_meta );

		// if response
		if ( $response ) {

			if ( isset( $response['success'] ) ) {

				switch ( $response['action'] ) {
					case 'add':
						$successcode = 1;
						break;
					case 'update':
						$successcode = 2;
						break;
					case 'delete':
						$successcode = 3;
						break;
				}

				if ( isset( $response['test'] ) ) {
					$successcode = $successcode + 100;
				}

				// no errors, show admin notice
				add_filter( 'redirect_post_location', function ( $loc ) use ( $successcode ) {
					remove_query_arg( 'locationews-err' );

					return add_query_arg( 'locationews-msg', $successcode, $loc );
				} );

			} elseif ( isset( $response['error'] ) ) {
				// show errors
				add_filter( 'redirect_post_location', function ( $loc ) use ( $response ) {
					return add_query_arg( 'locationews-err', $response['msg'], $loc );
				} );
			}
		}

		return $post_id;
	}

	/**
	 * Unpublished
	 *
	 * If post status changes from publish to something else.
	 *
	 * @since 2.0.0
	 *
	 * @param $new_status
	 * @param $old_status
	 * @param $post
	 */
	public function ln_unpublished( $new_status, $old_status, $post ) {
		// delete post from Locationews API if status is not publish
		if ( ! in_array( $new_status, [ 'publish', 'auto-draft' ] ) ) {
			$this->ln_delete_post( $post->ID );
		}
	}

	/**
	 * Delete post
	 *
	 * Delete post from Locationews API.
	 *
	 * @since 2.0.0
	 *
	 * @param $postID
	 */
	public function ln_delete_post( $postID ) {
		if ( $postID ) {
			$post_meta = $this->plugin->ln_get_post_meta( $postID );

			if ( isset( $post_meta['id'] ) && isset( $post_meta['on'] ) && $post_meta['on'] == 1 ) {

				$response = $this->plugin->ln_api_call( 'delete', [ 'Id' => $post_meta['id'] ] );

				// set Locationews Id to false
				$post_meta['id'] = false;

				$successcode = 3;

				// if response
				if ( $response ) {

					if ( isset( $response['success'] ) ) {
						// no errors, show admin notice
						add_filter( 'redirect_post_location', function ( $loc ) use ( $successcode ) {
							remove_query_arg( 'locationews-err' );

							return add_query_arg( 'locationews-msg', $successcode, $loc );
						} );
					} else {
						// show errors
						add_filter( 'redirect_post_location', function ( $loc ) use ( $response ) {
							return add_query_arg( 'locationews-err', $response['msg'], $loc );
						} );
					}
				}
			}
		}
	}

	/**
	 * Has Geotags
	 *
	 * Find GEOTAGS from post tags
	 *
	 * @since 2.0.3
	 *
	 * @param $post_id
	 * 
	 * @return mixed
	 */
	public function has_geotags( $post_id ) {
		$geo = array();
		$coordinates = array();
		$lat = '';
		$lon = '';
		$tags = wp_get_post_tags( $post_id );

		if ( is_array( $tags ) ) {
			foreach ( $tags as $tag ) {
				if ( false !== strpos( $tag->slug, 'geolat' ) ) {
					list( $tmp, $location ) = explode('geolat', $tag->slug );
					if ( $location ) {
						$location = preg_replace("/[^0-9.,-]/","", $location );
						$lat = floatval( trim( str_replace('-', '.', $location ) ) );
						$geo['lat'][] = $lat;
					}
				}
				if ( false !== strpos( $tag->slug, 'geolon' ) ) {
					list( $tmp, $location ) = explode('geolon', $tag->slug );
					if ( $location ) {
						$location = preg_replace("/[^0-9.,-]/","", $location );
						$lon = floatval( trim( str_replace('-', '.', $location ) ) );
						$geo['lon'][] = $lon;
					}
				}
			}
		}

		if ( isset( $geo['lat'] ) && is_array( $geo['lat'] ) && isset( $geo['lon'] ) && is_array( $geo['lon'] ) ) {
			$i = 0;
			foreach ( $geo['lat'] as $latitude ) {
				if ( isset( $geo['lon'][ $i ] ) ) {
					if ( preg_match('/^[-]?((([0-8]?[0-9])(\.(\d+))?)|(90(\.0+)?)),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))(\.(\d+))?)|180(\.0+)?)$/', $geo['lat'][ $i ] . ',' . $geo['lon'][ $i ] ) ) {
						$coordinates[] = ( $geo['lat'][ $i ] . ',' . $geo['lon'][ $i ] );
					}
				}
				$i++;
			}
		}
		
		if ( ! empty( $coordinates ) ) {
			return $coordinates;
		} else {
			return false;
		}
	}

	/**
	 * Admin notices
	 *
	 * Show admin notices when saving post
	 *
	 * @since 2.0.0
	 */
	public function ln_admin_notices() {
		// do we have an error?
		if ( isset( $_GET['locationews-err'] ) ) {
			// make sure we are in the proper post type
			if ( in_array( get_current_screen()->post_type, array_keys( $this->plugin->ln_get_option( 'postTypes' ) ) ) ) {

				// get error codes from current url
				$errorcodes = explode( ',', $_GET['locationews-err'] );
				$msg        = [];

				// do we have an error array?
				if ( is_array( $errorcodes ) ) {
					// loop through error codes
					foreach ( $errorcodes as $errorcode ) {
						$msg[] = $this->plugin->ln_get_error_message( $errorcode );
					}
				}
				$msg = array_filter( $msg );
				$msg = array_unique( $msg );

				if ( ! empty( $msg ) ) {
					$this->plugin->ln_show_notice( $msg, 'error is-dismissible' );
				}
			}
		} elseif ( isset( $_GET['locationews-msg'] ) ) {
			// set correct message based on code
			$msg = $this->plugin->ln_get_success_message( $_GET['locationews-msg'] );

			if ( ! empty( $msg ) && ! is_array( $msg ) ) {
				$this->plugin->ln_show_notice( $msg, 'updated notice notice-success is-dismissible' );
			}
		}
	}

}
