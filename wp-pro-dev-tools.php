<?php
/**
 * Plugin Name: WP Pro Dev Tools
 * Plugin URI:  https://github.com/jawittdesigns/wp-pro-dev-tools
 * Description: A WordPress plugin that has tools and functionality that will assist you in WordPress development.
 * Version:     0.0.1
 * Author:      Jason Witt
 * Author URI:  https://github.com/jawittdesigns/
 * License:     GPLv2
 * Text Domain: wp-pro-dev-tools
 * Domain Path: /languages
 *
 * @package   WP_Pro_Dev_Tools
 * @author    Jason Witt <contact@jawittdesigns.com>
 * @copyright Copyright (c) 2017, Jason Witt
 * @license   GNU General Public License v2 or later
 * @version   0.0.1
 */

namespace WP_Pro_Dev_Tools;

use WP_Pro_Dev_Tools\Includes\Classes as Classes;

// ==============================================
// Autoloader
// ==============================================
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'includes' ) . 'autoload.php';

if ( ! class_exists( 'WP_Pro_Dev_Tools' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 */
	class WP_Pro_Dev_Tools {

		/**
		 * Plugin Slug.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @var sting
		 */
		public $plugin_slug = 'wp_pro_dev_tools';

		/**
		 * Get the plugin settings options.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @var array
		 */
		public $get_options;

		/**
		 * Required Plugins List.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		public $get_required_plugins;

		/**
		 * Singleton instance of plugin.
		 *
		 * @var   WP_Pro_Dev_Tools
		 * @since 0.0.1
		 */
		protected static $single_instance = null;

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return A single instance of this class.
		 */
		public static function get_instance() {
			if ( null === self::$single_instance ) {
				self::$single_instance = new self();
			}

			return self::$single_instance;
		}

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function __construct() {
			// Get the settings option.
			$options                    = ( is_multisite() ) ? get_site_option( $this->plugin_slug ) : get_option( $this->plugin_slug );
			$this->get_options          = ( $options ) ? $options : array();
			$this->get_required_plugins = $this->get_required_plugins();
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
			$options = $this->get_options;

			if ( is_multisite() ) {
				if ( is_network_admin() ) {
					$plugins = $options['required_plugins'];
				} else {
					$plugins = array_merge( get_blog_option( get_current_blog_id(), $options['required_plugins'] ), $options['required_plugins'] );
				}
			} else {
				$plugins = $options['required_plugins'];
			}

			// Is Not Array sanity check.
			if ( ! $plugins || ! is_array( $plugins ) || empty( $plugins ) ) {
				return array();
			}

			return $plugins;
		}

		/**
		 * Init
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function init() {

			// Load translated strings for plugin.
			load_plugin_textdomain( 'wp-pro-dev-tools', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Load Template Tags.
			include trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/template-tags.php';

			// Instantiate Classes.
			$this->classes();
		}

		/**
		 * Instantiate Classes.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function classes() {
			// Load the settings page.
			$settings = new Classes\Settings();

			// Bail if enable_cms_settings not set.
			$option   = ( isset( $this->get_options['enable_pro_dev_tools'] ) ) ? $this->get_options['enable_pro_dev_tools'] : 'false';
			if ( ! isset( $option ) || 'true' !== $option ) {
				return;
			}

			// Load if the classes.
			$this->add_roles        = new Classes\Add_Roles();
			$this->required_plugins = new Classes\Required_Plugins();
			$this->action_links     = new Classes\Action_Links();
			$this->bulk_actions     = new Classes\Bulk_Actions();
			$this->enviroment_alert = new Classes\Enviroment_Alert();
		}

		/**
		 * Activate the plugin.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function _activate() {

			// Deafult Settings.
			$settings = array(
				'enable_pro_dev_tools' => 'true',
				'required_plugins'     => array(),
			);

			// If is multisite.
			if ( is_multisite() ) {

				// Update site options.
				update_site_option( $this->plugin_slug, $settings );
			} else {

				// Update options.
				update_option( $this->plugin_slug, $settings );
			}

			// Add new roles.
			new Classes\Add_Roles();

			// Flush Rewrite Rules.
			flush_rewrite_rules();
		}

		/**
		 * Deactivate the plugin.
		 * Uninstall routines should be in uninstall.php.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function _deactivate() {

		}
	}
}

/**
 * Return an instance of the plugin class.
 *
 * @author Jason Witt
 * @since  0.0.1
 *
 * @return Singleton instance of plugin class.
 */
function wp_pro_dev_tools() {
	return WP_Pro_Dev_Tools::get_instance();
}
add_action( 'plugins_loaded', array( wp_pro_dev_tools(), 'init' ) );

/**
 * Activation
 *
 * @author Jason Witt
 * @since  0.0.1
 */
register_activation_hook( __FILE__, array( wp_pro_dev_tools(), '_activate' ) );
