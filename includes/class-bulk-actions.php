<?php
/**
 * Pro Dev Tools Bulk Actions.
 *
 * @author Jason Witt
 * @since  1.0.0
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Bulk Actions.
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class PDT_Bulk_Actions {

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
	 */
	public function init() {

		// Set the properties.
		$this->required_plugins = ( pro_dev_tools()->get_required_plugins ) ? pro_dev_tools()->get_required_plugins : array();

		// Run the hooks.
		if ( is_allowed_role() ) {
			add_filter( is_multisite() && is_network_admin() ? 'bulk_actions-plugins-network' : 'bulk_actions-plugins', array( $this, 'add_bulk_action' ) );
			add_filter( is_multisite() && is_network_admin() ? 'handle_bulk_actions-plugins-network' : 'handle_bulk_actions-plugins', array( $this, 'bulk_action_handler' ), 10, 3 );
		}
	}

	/**
	 * Bulk Action.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $actions An array of bulk actions to list in the dropdown.
	 *
	 * @return array $actions An array of bulk actions to list in the dropdown.
	 */
	public function add_bulk_action( $actions ) {

		$actions['required'] = apply_filters( 'pro_dev_tools_bulk_action_required_text', __( 'Required', 'pro-dev-tools' ) );

		if ( count( (array) $this->required_plugins ) > 0 && is_array( $this->required_plugins ) ) {
			$actions['not-required'] = apply_filters( 'pro_dev_tools_bulk_action_not_required_text', __( 'Not Required', 'pro-dev-tools' ) );
		}

		return $actions;
	}

	/**
	 * Bulk Action Handler.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
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

			// Action before bulk add required plugins.
			do_action( 'pro_dev_tools_before_bulk_add_required_plugins' );

			// Add plugins to required list.
			$required_plugins = pro_dev_tools()->required_plugins->add_required( $items );

			// Action after bulk add required plugins.
			do_action( 'pro_dev_tools_before_bulk_add_required_plugins' );
		}

		// Remove plugin from required list.
		if ( 'not-required' === $doaction ) {

			// Action before bulk remove required plugins.
			do_action( 'pro_dev_tools_before_bulk_remove_required_plugins' );

			// Remove plugins for required list.
			$required_plugins = pro_dev_tools()->required_plugins->remove_required( $items );

			// Action after bulk remove required plugins.
			do_action( 'pro_dev_tools_after_bulk_remove_required_plugins' );
		}

		if ( ! empty( $required_plugins ) ) {

			// Action before update bulk required plugins.
			do_action( 'pro_dev_tools_before_update_bulk_required_plugins' );

			// Update the required plugins option.
			pro_dev_tools()->required_plugins->update_required_plugins( $required_plugins );

			// Action after update bulk required plugins.
			do_action( 'pro_dev_tools_after_update_bulk_required_plugins' );
		}

		// Action before bulk redirect.
		do_action( 'pro_dev_tools_before_bulk_redirect' );
		return $redirect_url;
	}
}
