<?php
/**
 * Pro Dev Tools Enviroment Alert.
 *
 * @author Jason Witt
 * @since  1.0.0
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Enviroment Alert.
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class PDT_Enviroment_Alert {

	/**
	 * Constructor.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init();
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
		static $executed = false;

		// Bail early if already executed.
		if ( $executed ) {
			return;
		}

		// Bail if not allowed role.
		if ( ! is_allowed_role() ) {
			return;
		}

		// Set the properties.
		$this->settings = pro_dev_tools()->get_settings;

		// Bail is noticed aren't enabled.
		if ( ! isset( $this->settings['enable-enviroment-notices'] ) || ! $this->settings['enable-enviroment-notices'] ) {
			return;
		}

		// Run the hooks.
		add_action( 'admin_notices', array( $this, 'display_notice' ) );
		add_action( 'wp_head', array( $this, 'display_notice' ) );

		$executed = true;
	}

	/**
	 * Get Current Domain
	 *
	 * @since  0.0.1
	 * @author Jason Witt
	 *
	 * @return string $domain The current Domain.
	 */
	public function get_current_domain() {

		$current_url = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$parse_url   = parse_url( $current_url );
		$host        = $parse_url['host'];
		$host_names  = explode( '.', $host );
		$domain      = $host_names[ count( $host_names ) - 2 ] . '.' . $host_names[ count( $host_names ) - 1 ];

		return $domain;
	}

	/**
	 * Get current domain.
	 *
	 * @since  0.0.1
	 * @author Jason Witt
	 *
	 * @return mixed The current domain type.
	 */
	public function get_current_domain_type() {

		$domain  = $this->get_current_domain();

		// Bail early if $domain is not set.
		if ( ! $domain ) {
			return;
		}
		switch ( $domain ) {
			case $this->settings['local-domain'] :
				return 'development';
				break;
			case $this->settings['staging-domain'] :
				return 'staging';
				break;
			case $this->settings['production-domain'] :
				return 'production';
				break;
		}
		return;
	}

	/**
	 * Is Development
	 *
	 * @since  0.0.1
	 * @author Jason Witt
	 *
	 * @return boolean
	 */
	public function is_development() {
		$dev_domains    = $this->settings['local-domain'];
		$current_domain = $this-> get_current_domain_type();
		foreach ( $dev_domains as $dev_domain ) {

			if ( $current_domain === $dev_domain ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Display notice.
	 *
	 * @since  0.0.1
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function display_notice() {

		$div_style = '';
		$p_style   = '';

		if ( ! is_admin() ) {
			$div_style = apply_filters( 'pro-dev-tools-environment-alert-frontend-wrapper', 'style="background: #BDE5F8; padding: 1px 12px; box-shadow: 0 1px 1px 0 rgba( 0,0,0,.3 );"' );
			if ( is_admin_bar_showing() ) {
				$div_style = apply_filters( 'pro-dev-tools-environment-alert-frontend-wrapper', 'style="background: #BDE5F8; padding: 1px 12px; box-shadow: 0 1px 1px 0 rgba( 0,0,0,.3 );"' );
			}
			$p_style = apply_filters( 'pro-dev-tools-environment-alert-frontend-ptag', 'style="padding: 6px; font-size: 13px; margin: 0; text-align: center; color: #333;"' );
		}

		$domain = $this->get_current_domain_type();

		// Translators: The environment.
		$message = apply_filters( 'pro-dev-tools-environment-alert-message', wp_sprintf( __( 'You are currently working on the <strong>%1$s</strong> environment.', 'wds_wpmu' ), ucwords( $domain ) ), ucwords( $domain ) );

		if ( null !== $domain ) {

			echo '<div class="notice notice-info" ' . wp_kses_post( $div_style ) . '>';
			echo '<p ' . wp_kses_post( $p_style ) . '>' . wp_kses_post( $message ) . '</p>';
			echo '</div>';
		}
	}
}
