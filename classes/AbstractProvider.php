<?php
/**
 * Base hook provider.
 *
 * @package   Locationews
 * @copyright Copyright (c) 2018, Locationews, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Base hook provider class.
 *
 * @package Locationews
 * @since   2.0.+
 */
abstract class Locationews_AbstractProvider {

	/**
	 * Plugin instance.
	 *
	 * @since 2.0.0
	 * @var Locationews_Plugin
	 */
	protected $plugin;

	/**
	 * Set a reference to the main plugin instance.
	 *
	 * @since 2.0.0
	 *
	 * @param Locationews_Plugin $plugin Main plugin instance.
	 */
	public function ln_set_plugin( $plugin ) {
		$this->plugin = $plugin;

		return $this;
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.0.0
	 */
	abstract public function ln_register_hooks();

}
