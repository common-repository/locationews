<?php
/**
 * Common plugin functionality.
 *
 * @package   Locationews
 * @copyright Copyright (c) 2018 Locationews, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Abstract plugin class.
 *
 * @package Locationews
 * @since   2.0.0
 */
abstract class Locationews_AbstractPlugin {

	/**
	 * Plugin basename.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	protected $basename;

	/**
	 * Absolute path to the main plugin directory.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	protected $directory;

	/**
	 * Absolute path to the main plugin file.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	protected $file;

	/**
	 * Plugin identifier.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	protected $slug;

	/**
	 * URL to the main plugin directory.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	protected $url;

	/**
	 * Plugin version
	 *
	 * @since 2.0.0
	 * @var
	 */
	protected $version;

	/**
	 * Plugin enviroment
	 *
	 * @since 2.0.0
	 * @var
	 */
	protected $enviroment;

	/**
	 * Retrieve the relative path from the main plugin directory.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function ln_get_basename() {
		return $this->basename;
	}

	/**
	 * Set the plugin basename.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $basename Relative path from the main plugin directory.
	 *
	 * @return string
	 */
	public function ln_set_basename( $basename ) {
		$this->basename = $basename;

		return $this;
	}

	/**
	 * Retrieve the plugin directory.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function ln_get_directory() {
		return $this->directory;
	}

	/**
	 * Set the plugin's directory.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $directory Absolute path to the main plugin directory.
	 *
	 * @return $this
	 */
	public function ln_set_directory( $directory ) {
		$this->directory = rtrim( $directory, '/' ) . '/';

		return $this;
	}

	/**
	 * Retrieve the path to a file in the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $path Optional. Path relative to the plugin root.
	 *
	 * @return string
	 */
	public function ln_get_path( $path = '' ) {
		return $this->directory . ltrim( $path, '/' );
	}

	/**
	 * Retrieve the absolute path for the main plugin file.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function ln_get_file() {
		return $this->file;
	}

	/**
	 * Set the path to the main plugin file.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $file Absolute path to the main plugin file.
	 *
	 * @return $this
	 */
	public function ln_set_file( $file ) {
		$this->file = $file;

		return $this;
	}

	/**
	 * Retrieve the plugin indentifier.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function ln_get_slug() {
		return $this->slug;
	}

	/**
	 * Set the plugin identifier.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $slug Plugin identifier.
	 *
	 * @return $this
	 */
	public function ln_set_slug( $slug ) {
		$this->slug = $slug;

		return $this;
	}

	/**
	 * Retrieve the URL for a file in the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $path Optional. Path relative to the plugin root.
	 *
	 * @return string
	 */
	public function ln_get_url( $path = '' ) {
		return $this->url . ltrim( $path, '/' );
	}

	/**
	 * Set the URL for plugin directory root.
	 *
	 * @since 2.0.0
	 *
	 * @param  string $url URL to the root of the plugin directory.
	 *
	 * @return $this
	 */
	public function ln_set_url( $url ) {
		$this->url = rtrim( $url, '/' ) . '/';

		return $this;
	}

	/**
	 * Retrieve plugin version
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function ln_get_version() {
		return $this->version;
	}

	/**
	 * Set plugin version
	 *
	 * @since 2.0.0
	 *
	 * @param $version
	 *
	 * @return $this
	 */
	public function ln_set_version( $version ) {
		$this->version = $version;

		return $this;
	}

	/**
	 * Retrieve plugin enviroment
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function ln_get_enviroment() {
		return $this->enviroment;
	}

	/**
	 * Set plugin enviroment
	 *
	 * @since 2.0.0
	 *
	 * @param string $enviroment
	 *
	 * @return $this
	 */
	public function ln_set_enviroment( $enviroment = 'production' ) {
		$this->enviroment = $enviroment;

		return $this;
	}

	/**
	 * Register a hook provider.
	 *
	 * @since 2.0.0
	 *
	 * @param  object $provider Hook provider.
	 *
	 * @return $this
	 */
	public function ln_register_hooks( $provider ) {
		if ( method_exists( $provider, 'ln_set_plugin' ) ) {
			$provider->ln_set_plugin( $this );
		}

		$provider->ln_register_hooks();

		return $this;
	}

	abstract public function ln_set_options();

	abstract public function ln_get_options();

	abstract public function ln_get_user_options();

	abstract public function ln_get_front_options();

	abstract public function ln_get_meta_name();
}
