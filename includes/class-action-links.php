<?php
/**
 * Pro Dev Tools Action Links.
 *
 * @author Jason Witt
 * @since  1.0.0
 *
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Action Links.
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class PDT_Action_Links {

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
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function init() {

		// Set the properties.
		$this->required_plugins = pro_dev_tools()->get_required_plugins;

		// Run the hooks.
		if ( is_allowed_role() ) {
			add_filter( is_multisite() && is_network_admin() ? 'network_admin_plugin_action_links' : 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
		}
	}

	/**
	 * Get Active Plugins.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return array The array of active plugins.
	 */
	public function get_active_plugins() {
		$plugins = array();
		if ( is_multisite() ) {
			$plugins = get_blog_option( get_current_blog_id(), 'active_plugins' );
			if ( is_network_admin() ) {
				$network_plugins = get_site_option( 'active_sitewide_plugins' );
				foreach ( $network_plugins as $key => $value ) {
					$plugins[] = $key;
				}
			}
		} else {
			$plugins = get_option( 'active_plugins' );
		}
		return $plugins;
	}

	/**
	 * Add Action Links.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array  $actions     Array of action names to anchor tags.
	 * @param string $plugin_file Plugin file name.
	 *
	 * @return array $action New array of action names to anchor tags.
	 */
	public function add_action_links( $actions, $plugin_file ) {

		$active_plugins    = $this->get_active_plugins();
		$not_required_text = apply_filters( 'pro_dev_tools_action_link_not_required_text',__( 'Not Required', 'pro-dev-tools' ) );
		$required_text     = apply_filters( 'pro_dev_tools_action_link_required_text', __( 'Required', 'pro-dev-tools' ) );

		// Bail and return actions if $active_plugins is not set or is empty.
		if ( ! $active_plugins || ! is_array( $active_plugins ) || empty( $active_plugins ) ) {
			return $actions;
		}

		// If plugin is not in the active_plugins option.
		if ( ! in_array( $plugin_file, $active_plugins ) ) {
			return $actions;
		}

		// If the required plugin option is set.
		if ( $this->required_plugins && ( is_array( $this->required_plugins ) && ! empty( $this->required_plugins ) ) ) {

			// If plugin is already in the required plugin list.
			if ( in_array( $plugin_file, $this->required_plugins ) ) {
				$actions['required'] = $this->render_action_link( $plugin_file, 'false', $not_required_text );

			// If plugin is not in the required plugin list.
			} else {
				$actions['required'] = $this->render_action_link( $plugin_file, 'true', $required_text );
			}
		} else {
			$actions['required'] = $this->render_action_link( $plugin_file, 'true', $required_text );
		}

		return $actions;
	}

	/**
	 * Render Action Link.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $plugin_file Plugin file name.
	 * @param string $required    Set to 'true' if required, else set to 'false'.
	 * @param string $title       The title of the link.
	 *
	 * @return string
	 */
	private function render_action_link( $plugin_file, $required, $title ) {
		return wp_sprintf( '<a href="%1$s?plugin=%2$s&required=%3$s">%4$s</a>', esc_url( 'plugins.php' ), $plugin_file, $required, $title );
	}
}
