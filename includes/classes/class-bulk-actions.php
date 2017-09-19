<?php
/**
 * Pro Dev Tools Bulk Actions.
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

if ( ! class_exists( 'Bulk_Actions' ) ) {

	/**
	 * WP Pro Dev Tools Bulk Actions.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 */
	class Bulk_Actions {

		/**
		 * Required Plugins.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @var array
		 */
		protected $required_plugins;

		/**
		 * Constructor.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 */
		public function __construct() {

			// Set the properties.
			$this->required_plugins = Root\wp_pro_dev_tools()->get_required_plugins;

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
		 */
		public function init() {

			// Run the hooks.
			if ( wppdt_is_allowed_role() ) {
				add_filter( is_multisite() && is_network_admin() ? 'bulk_actions-plugins-network' : 'bulk_actions-plugins', array( $this, 'add_bulk_action' ) );
				add_filter( is_multisite() && is_network_admin() ? 'handle_bulk_actions-plugins-network' : 'handle_bulk_actions-plugins', array( $this, 'bulk_action_handler' ), 10, 3 );
			}
		}

		/**
		 * Bulk Action.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param array $actions An array of bulk actions to list in the dropdown.
		 *
		 * @return array $actions An array of bulk actions to list in the dropdown.
		 */
		public function add_bulk_action( $actions ) {

			$actions['required'] = apply_filters( 'wp_pro_dev_tools_bulk_action_required_text', __( 'Required', 'pro-dev-tools' ) );

			if ( count( (array) $this->required_plugins ) > 0 && is_array( $this->required_plugins ) ) {
				$actions['not-required'] = apply_filters( 'wp_pro_dev_tools_bulk_action_not_required_text', __( 'Not Required', 'pro-dev-tools' ) );
			}

			return $actions;
		}

		/**
		 * Bulk Action Handler.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param string $redirect_url The URL ro redirect to.
		 * @param string $doaction     The action being taken.
		 * @param string $items        The items to take the action on.
		 *
		 * @return string $redirect_url The URL ro redirect to.
		 */
		public function bulk_action_handler( $redirect_url, $doaction, $items ) {
			$required_plugins = array();

			// Get the current url without query arguments.
			$redirect_url = strtok( ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?' );

			// Add plugin to required list.
			if ( 'required' === $doaction ) {

				// Add plugins to required list.
				$required_plugins = Root\wp_pro_dev_tools()->required_plugins->add_required( $items );
			}

			// Remove plugin from required list.
			if ( 'not-required' === $doaction ) {

				// Remove plugins for required list.
				$required_plugins = Root\wp_pro_dev_tools()->required_plugins->remove_required( $items );
			}

			// Update the required plugins option.
			Root\wp_pro_dev_tools()->required_plugins->update_required_plugins( $required_plugins );

			return $redirect_url;
		}
	}
}
