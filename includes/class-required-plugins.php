<?php
/**
 * Pro Dev Tools Required Plugins.
 *
 * @author Jason Witt
 * @since  1.0.0
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Required Plugins.
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class PDT_Required_Plugins {

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
		$this->required_plugins = pro_dev_tools()->get_required_plugins;

		// Run the hooks.
		if ( ! is_allowed_role() ) {
			add_action( 'pre_current_active_plugins', array( $this, 'remove_plugins' ) );
		}

		// Toggle the required plugins.
		if ( is_allowed_role() ) {
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

		// Action before Toggle Required Plugins.
		do_action( 'pro_dev_tools_before_toggle_required_plugins' );

		// Set variable as array.
		$required_plugins = array();

		// Toggle required.
		if ( 'true' === $this->is_required ) {

			// Action before Add Required Plugins.
			do_action( 'pro_dev_tools_before_add_required_plugins' );

			// Add plugin to required list.
			$required_plugins = $this->add_required( $this->plugin );

			// Action after Add Required Plugins.
			do_action( 'pro_dev_tools_after_add_required_plugins' );
		} elseif ( 'false' === $this->is_required ) {

			// Action before Remove Required Plugins.
			do_action( 'pro_dev_tools_before_remove_required_plugins' );

			// Remove plugin from required list.
			$required_plugins = $this->remove_required( $this->plugin );

			// Action after Remove Required Plugins.
			do_action( 'pro_dev_tools_after_remove_required_plugins' );
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

		// If $required_plugins is not empty or is differnt from the option, update the option.
		if ( ! empty( $required_plugins ) || $this->required_plugins !== $required_plugins ) {

			// Action before Update Required Plugins.
			do_action( 'pro_dev_tools_before_update_required_plugins' );

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

			// Update the '_pdt_required_plugins' option.
			if ( is_multisite() ) {
				if ( is_network_admin() ) {
					update_site_option( '_pdt_required_plugins', $required_plugins, true );
				} else {
					update_blog_option( get_current_blog_id(), '_pdt_required_plugins', $required_plugins, true );
				}
			} else {
				update_option( '_pdt_required_plugins', $required_plugins, true );
			}

			// Action after Toggle Required Plugins.
			do_action( 'pro_dev_tools_after_toggle_required_plugins' );
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
	 * @param string|array $plugins          The plugin to remove from required list.
	 *
	 * @return array       $required_plugins The modified array of required plugins.
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
