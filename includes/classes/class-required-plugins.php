<?php
/**
 * WP Pro Dev Tools Required Plugins.
 *
 * @package    WP_Pro_Dev_Tools
 * @subpackage WP_Pro_Dev_Tools/Includes/Classes
 * @author     Jason Witt <contact@jawittdesigns.com>
 * @copyright  Copyright (c) 2017, Jason Witt
 * @license    GNU General Public License v2 or later
 * @version    0.0.1
 */

namespace WP_Pro_Dev_Tools\Includes\Classes;

use \WP_Pro_Dev_Tools as Root;

if ( ! class_exists( 'Required_Plugins' ) ) {

	/**
	 * WP Pro Dev Tools Required Plugins.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Required_Plugins {

		/**
		 * Is Required.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		private $is_required;

		/**
		 * Plugin.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $plugin;

		/**
		 * Required Plugins.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $required_plugins;

		/**
		 * Settings.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @var array
		 */
		protected $options;

		/**
		 * Constructor.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			if ( $this->can_run() ) {
				$this->init();
			}
		}

		/**
		 * Can Run.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function can_run() {
			global $pagenow;

			// Bail if not in the admin dashboard.
			if ( ! is_admin() ) {
				return;
			}

			// Bail if it not the plugins page.
			if ( 'plugins.php' !== $pagenow ) {
				return;
			}

			return true;
		}

		/**
		 * Initiate.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function init() {

			// Set the properties.
			$this->is_required      = ( isset( $_GET['required'] ) ) ? sanitize_text_field( $_GET['required'] ) : null;
			$this->plugin           = ( isset( $_GET['plugin'] ) ) ? sanitize_text_field( $_GET['plugin'] ) : null;
			$this->required_plugins = Root\wp_pro_dev_tools()->get_required_plugins;
			$this->options          = Root\wp_pro_dev_tools()->get_options;

			// Run the hooks.
			if ( ! wppdt_is_allowed_role() ) {
				add_action( 'pre_current_active_plugins', array( $this, 'remove_plugins' ) );
			} else {
				$this->toggle_required_plugin();
			}
		}

		/**
		 * Remove plugins.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function remove_plugins() {

			global $wp_list_table;

			// Remove the required plugins for the plugin table.
			foreach ( $wp_list_table->items as $key => $value ) {
				if ( in_array( $key, $this->required_plugins, true ) ) {
					unset( $wp_list_table->items[ $key ] );
				}
			}
		}

		/**
		 * Add to Required Plugins.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function toggle_required_plugin() {

			// Bail if $this->is_required or $this->plugin is null.
			if ( is_null( $this->is_required ) || is_null( $this->plugin ) ) {
				return;
			}

			// Set variable as array.
			$required_plugins = array();

			// Toggle required.
			if ( 'true' === $this->is_required ) {

				// Add plugin to required list.
				$required_plugins = $this->add_required( $this->plugin );
			} elseif ( 'false' === $this->is_required ) {

				// Remove plugin from required list.
				$required_plugins = $this->remove_required( $this->plugin );
			}

			$this->update_required_plugins( $required_plugins );

			return $this->redirect_after_update();
		}

		/**
		 * Update Required Plugin.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param array $required_plugins The array of required plugins to update.
		 *
		 * @return void
		 */
		public function update_required_plugins( $required_plugins ) {

			// If $required_plugins is differnt from the option, update the option.
			if ( $this->required_plugins !== $required_plugins ) {

				// Get the active plugins array.
				if ( is_multisite() ) {
					if ( is_network_admin() ) {
						$active_site_plugins = get_site_option( 'active_sitewide_plugins' );
						foreach ( $active_site_plugins as $key => $value ) {
							$active_plugins[] = $key;
						}
					} else {
						$active_plugins = get_blog_option( get_current_blog_id(), 'active_plugins' );
					}
				} else {
					$active_plugins = get_option( 'active_plugins' );
				}

				// Remove plugin from the $required_plugins array if the plugin is not active.
				foreach ( $required_plugins as $required_plugin ) {
					if ( ! in_array( $required_plugin, $active_plugins, true ) ) {
						$key = array_search( $required_plugin, $active_plugins, true );
						unset( $required_plugins[ $key ] );
					}
				}

				// Remove any possible duplicates.
				$required_plugins = array_unique( $required_plugins );
				$options          = $this->options;

				// Add new required plugin to options.
				foreach ( $options as $key => $value ) {
					if ( 'required_plugins' === $key ) {
						$options[ $key ] = $required_plugins;
					}
				}

				// Update the 'required_plugins' option.
				if ( is_multisite() ) {
					if ( is_network_admin() ) {
						update_site_option( 'wp_pro_dev_tools', $required_plugins, true );
					} else {
						update_blog_option( get_current_blog_id(), 'wp_pro_dev_tools', $required_plugins );
					}
				} else {
					update_option( 'wp_pro_dev_tools', $options, true );
				}
			}
		}

		/**
		 * Redirect After Update.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function redirect_after_update() {

			// Get the current url without query arguments.
			$current_url = strtok( ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",'?' );

			// Redirect to plugins.php page after update.
			wp_safe_redirect( $current_url );
			exit;
		}

		/**
		 * Add Required.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param string|array $plugins The plugin(s) to require.
		 *
		 * @return array $required_plugins The modified array of required plugins.
		 */
		public function add_required( $plugins ) {

			$required_plugins = $this->required_plugins;

			// Set as an array if it's not set.
			if ( ! is_array( $required_plugins ) ) {
				$required_plugins = array();
			}

			// Add the plugin(s) to the required plugins array.
			if ( ! is_array( $plugins ) ) {
				if ( ! in_array( $plugins, $required_plugins, true ) ) {
					$required_plugins[] = $plugins;
				}
			} else {
				foreach ( $plugins as $key => $value ) {
					if ( ! in_array( $plugins, $required_plugins, true ) ) {
						$required_plugins[] = $value;
					}
				}
			}

			// Remove any duplicates.
			$required_plugins = array_unique( $required_plugins );

			return $required_plugins;
		}

		/**
		 * Remove Required.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param string|array $plugins The plugin to remove from required list.
		 *
		 * @return array $required_plugins The modified array of required plugins.
		 */
		public function remove_required( $plugins ) {
			$required_plugins = $this->required_plugins;

			// Bail if fails array check.
			if ( ! $required_plugins || ! is_array( $required_plugins ) || empty( $required_plugins ) ) {
				return array();
			}

			// Remove the plugin(s) to the required plugins array.
			if ( ! is_array( $plugins ) ) {
				if ( in_array( $plugins, $required_plugins, true ) ) {
					foreach ( $required_plugins as $key => $value ) {
						if ( $plugins === $value ) {
							unset( $required_plugins[ $key ] );
						}
					}
				}
			} else {
				foreach ( $plugins as $plugin ) {
					if ( in_array( $plugin, $required_plugins, true ) ) {
						foreach ( $required_plugins as $key => $value ) {
							if ( $plugin === $value ) {
								unset( $required_plugins[ $key ] );
							}
						}
					}
				}
			}

			// Reset the array keys.
			$required_plugins = array_values( $required_plugins );
			return $required_plugins;
		}
	}
}
