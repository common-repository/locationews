<?php
/**
 * Fired during plugin deactivation
 *
 * @link       http://www.locationews.com
 * @since      1.0.0
 */
class Locationews_Deactivator {
	/**
	 * Deactivate actions
	 */
	public static function deactivate( $network_wide = false ) {
		global $wpdb;

		// See if being activated on the entire network or one blog
		if ( is_multisite() && $network_wide ) {

			// Get current blog id so we can switch back to it later
			$current_blog = $wpdb->blogid;

			// Get all blogs in the network and activate plugin on each one
			$blog_ids = $wpdb->get_col("SELECT `blog_id` FROM `$wpdb->blogs`");
			foreach ( $blog_ids as $blog_id ) {

				switch_to_blog( $blog_id );
				// Deactivate blog
				self::deactivate_site();
			}

			// Switch back to the current blog
			switch_to_blog( $current_blog );

			// Delete network wide options
			delete_site_option('locationews_activated');

		} else {
			// Single site deactivate
			self::deactivate_site();
		}

	}

	public static function deactivate_site() {
		update_option('_locationews_trash', get_option('locationews_options') );
		delete_option('locationews_options');
		update_option('_locationews_user', get_option('locationews_user') );
		delete_option('locationews_user');
	}
}
