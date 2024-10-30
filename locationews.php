<?php
/**
 * Locationews - Where matters
 *
 * @link              https://www.locationews.com
 * @since             1.0.0
 * @package           Locationews
 *
 * @wordpress-plugin
 * Plugin Name:       Locationews
 * Description:       Publish location based articles in Locationews.
 * Version:           2.0.6
 * Author:            Locationews
 * Author URI:        https://www.locationews.com
 * Requires:          PHP >= 5.6, Wordpress >= 4.9
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       locationews
 * Domain Path:       /languages/
 */

// Build on top of https://github.com/cedaro/structure.

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 */
$locationews_version = '2.0.6';

/**
 * Plugin enviroment.
 */
$locationews_enviroment = 'production';

define( 'LOCATIONEWS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

if ( ! defined('LOCATIONEWS_BASE_DIR') ) {
	define('LOCATIONEWS_BASE_DIR', dirname( __FILE__ ) . '/' );
}
if ( ! defined('LOCATIONEWS_BASE_URL') ) {
	define('LOCATIONEWS_BASE_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Autoloader callback.
 *
 * Converts a class name to a file path and requires it if it exists.
 *
 * @since 2.0.0
 *
 * @param string $class Class name.
 */
function locationews_autoloader( $class ) {
	if ( 0 !== strpos( $class, 'Locationews_' ) ) {
		return;
	}

	$file = dirname( __FILE__ ) . '/classes/';
	$file .= str_replace( [ 'Locationews_', '_' ], [ '', '/' ], $class );
	$file .= '.php';

	if ( file_exists( $file ) ) {
		require_once( $file );
	}

	/*
	if ( file_exists( dirname( __FILE__ ) . '/classes/Publication_Widget.php' ) ) {
		require_once( dirname( __FILE__ ) . '/classes/Publication_Widget.php' );
	}
	*/

}

spl_autoload_register( 'locationews_autoloader' );

/**
 * Locationews
 *
 * Retrieve the main plugin instance.
 *
 * @since 2.0.0
 *
 * @return Locationews_Plugin
 */
function locationews() {
	static $instance;

	if ( NULL === $instance ) {
		$instance = new Locationews_Plugin();
	}

	return $instance;
}

// set up the main plugin instance
locationews()->ln_set_basename( plugin_basename( __FILE__ ) )
             ->ln_set_directory( plugin_dir_path( __FILE__ ) )
             ->ln_set_file( __FILE__ )
             ->ln_set_slug( 'locationews' )
             ->ln_set_url( plugin_dir_url( __FILE__ ) )
             ->ln_set_version( $locationews_version )
             ->ln_set_enviroment( $locationews_enviroment )
             ->ln_set_options();


// register hook providers
if ( is_admin() ) {
	locationews()
		->ln_register_hooks( new Locationews_Provider_I18n() )
		->ln_register_hooks( new Locationews_Provider_Admin() )
		->ln_register_hooks( new Locationews_Provider_Options() )
		->ln_register_hooks( new Locationews_Provider_Metabox() );

} else {
	// No need for front end scripts
	//locationews()->ln_register_hooks( new Locationews_Provider_Public() );
}

// load the plugin
add_action( 'plugins_loaded', [ locationews(), 'ln_load_plugin' ] );

/**
 * The code that runs during plugin activation.
 */
function activate_locationews( $network_wide ) {
	require_once locationews()->ln_get_directory() . 'classes/hooks/Locationews_Activator.php';
	Locationews_Activator::activate( $network_wide );
}
register_activation_hook( __FILE__, 'activate_locationews');

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_locationews( $network_wide ) {
	require_once locationews()->ln_get_directory() . 'classes/hooks/Locationews_Deactivator.php';
	Locationews_Deactivator::deactivate( $network_wide );
}
register_deactivation_hook( __FILE__, 'deactivate_locationews');
