<?php
/**
 * Plugin Name: Pro Dev Tools
 * Plugin URI:  https://github.com/jawittdesigns/pro-dev-tools
 * Description: A WordPress plugin to assist in Theme and Plugin development.
 * Version:     1.0.0
 * Author:      Jason Witt
 * Author URI:  https://jawittdesigns.com
 * Donate link: https://github.com/jawittdesigns/pro-dev-tools
 * License:     GPLv2
 * Text Domain: pro-dev-tools
 * Domain Path: /languages
 *
 * @link    https://github.com/jawittdesigns/pro-dev-tools
 *
 * @package Pro_Dev_Tools
 * @version 1.0.2
 */

/**
 * Copyright (c) 2017 Jason Witt (email : contact@jawittdesigns.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Autoloads files with classes when needed.
 *
 * @since  1.0.0
 * @param  string $class_name Name of the class being requested.
 */
function pro_dev_tools_autoload_classes( $class_name ) {

	// If our class doesn't have our prefix, don't load it.
	if ( 0 !== strpos( $class_name, 'PDT_' ) ) {
		return;
	}

	// Set up our filename.
	$filename = strtolower( str_replace( '_', '-', substr( $class_name, strlen( 'PDT_' ) ) ) );

	// Include our file.
	Pro_Dev_Tools::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'pro_dev_tools_autoload_classes' );

/**
 * Main initiation class.
 *
 * @since  1.0.0
 */
final class Pro_Dev_Tools {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	const VERSION = '1.0.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Pro_Dev_Tools
	 * @since  1.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Required Plugins List.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected $get_required_plugins;

	/**
	 * Get Settings.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected $get_settings;

	/**
	 * Instance of PDT_Add_Roles
	 *
	 * @since1.0.0
	 * @var PDT_Add_Roles
	 */
	protected $add_roles;

	/**
	 * Instance of PDT_Required_Plugins
	 *
	 * @since1.0.0
	 * @var PDT_Required_Plugins
	 */
	protected $required_plugins;

	/**
	 * Instance of PDT_Action_Links
	 *
	 * @since1.0.0
	 * @var PDT_Action_Links
	 */
	protected $action_links;

	/**
	 * Instance of PDT_Bulk_Actions
	 *
	 * @since1.0.0
	 * @var PDT_Bulk_Actions
	 */
	protected $bulk_actions;

	/**
	 * Instance of PDT_Settings
	 *
	 * @since1.0.0
	 * @var PDT_Settings
	 */
	protected $settings;

	/**
	 * Instance of PDT_Enviroment_Alert
	 *
	 * @since1.0.0
	 * @var PDT_Enviroment_Alert
	 */
	protected $enviroment_alert;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   1.0.0
	 * @return  Pro_Dev_Tools A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  1.0.0
	 */
	protected function __construct() {
		$this->basename             = plugin_basename( __FILE__ );
		$this->url                  = plugin_dir_url( __FILE__ );
		$this->path                 = plugin_dir_path( __FILE__ );
		$this->get_required_plugins = $this->get_required_plugins();
		$this->get_settings         = ( is_multisite() ) ? get_site_option( 'pro_dev_tools_settings' ) : get_option( 'pro_dev_tools_settings' );
	}

	/**
	 * Get Required Plugins.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return array
	 */
	public function get_required_plugins() {
		if ( is_multisite() ) {
			if ( is_network_admin() ) {
				$plugins = get_site_option( '_pdt_required_plugins' );
			} else {
				$plugins = array_merge( get_blog_option( get_current_blog_id(), '_pdt_required_plugins' ), get_site_option( '_pdt_required_plugins' ) );
			}
		} else {
			$plugins = get_option( '_pdt_required_plugins' );
		}

		// Is Not Array sanity check.
		if ( ! $plugins || ! is_array( $plugins ) || empty( $plugins ) ) {
			return array();
		}

		return $plugins;
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since 1.0.2
	 */
	public function plugin_classes() {

		$this->settings         = new PDT_Settings;
		$this->add_roles        = new PDT_Add_Roles;
		$this->required_plugins = new PDT_Required_Plugins;
		$this->action_links     = new PDT_Action_Links;
		$this->bulk_actions     = new PDT_Bulk_Actions;
		$this->enviroment_alert = new PDT_Enviroment_Alert;
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.2
	 */
	public function _activate() {

		// Add new roles.
		new PDT_Add_Roles;

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  1.0.0
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0.0
	 */
	public function init() {

		// Load translated strings for plugin.
		load_plugin_textdomain( 'pro-dev-tools', false, dirname( $this->basename ) . '/languages/' );

		// Include template tags file.
		$this->include_file( 'template-tags' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'add_roles':
			case 'required_plugins':
			case 'action_links':
			case 'get_required_plugins':
			case 'get_settings':
			case 'bulk_actions':
			case 'settings':
			case 'settings':
			case 'enviroment_alert':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $filename Name of the file to be included.
	 * @return boolean          Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path.
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the Pro_Dev_Tools object and return it.
 * Wrapper for Pro_Dev_Tools::get_instance().
 *
 * @since  1.0.0
 * @return Pro_Dev_Tools  Singleton instance of plugin class.
 */
function pro_dev_tools() {
	return Pro_Dev_Tools::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( pro_dev_tools(), 'init' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( pro_dev_tools(), '_activate' ) );
register_deactivation_hook( __FILE__, array( pro_dev_tools(), '_deactivate' ) );
