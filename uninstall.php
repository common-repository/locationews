<?php
/**
 * Fired when the plugin is uninstalled.
 * @link       http://www.locationews.com
 * @since      1.0.0
 *
 * @package    Locationews
 */

// If uninstall not called from WordPress, then exit.
if (! defined('ABSPATH') || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function locationews_delete_plugin_options() {

    $options = get_option('_locationews_trash');
	if ( strpos( $options['apiUrl'], 'api_dev' ) !== false ) {
        delete_post_meta_by_key('locationews_dev');
    } else {
        // Delete all post meta
        delete_post_meta_by_key('locationews');
    }


	// Delete plugin options
	delete_option('locationews_options');
	delete_option('locationews_user');
	delete_option('_locationews_trash');
	delete_option('_locationews_user');
}

if ( ! is_multisite() ) {
	locationews_delete_plugin_options();
} else {
	global $wpdb;

	delete_site_option('locationews_options');
	delete_site_option('locationews_activated');

	$old_blog = $wpdb->blogid;

	// Get all blog ids
	$blog_ids = $wpdb->get_col("SELECT `blog_id` FROM `$wpdb->blogs`");
	foreach ( $blog_ids as $blog_id ) {

		switch_to_blog( $blog_id );

		locationews_delete_plugin_options();
	}
	switch_to_blog( $old_blog );
}



