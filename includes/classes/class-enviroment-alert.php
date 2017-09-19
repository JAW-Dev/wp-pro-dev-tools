<?php
/**
 * WP Pro Dev Tools Enviroment Alert.
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

if ( ! class_exists( 'Enviroment_Alert' ) ) {

	/**
	 * WP Pro Dev Tools Enviroment Alert.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Enviroment_Alert {

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
			if ( ! wppdt_is_allowed_role() ) {
				return;
			}

			// Set the properties.
			$this->options = Root\wp_pro_dev_tools()->get_options;

			// Bail is noticed aren't enabled.
			if ( ! isset( $this->options['enable-enviroment-notices'] ) || ! $this->options['enable-enviroment-notices'] ) {
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
			$parse_url   = wp_parse_url( $current_url );
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
			$type    = '';

			// Bail early if $domain is not set.
			if ( ! $domain ) {
				return;
			}
			switch ( $domain ) {
				case $this->options['local-domain']:
					$type = 'development';
					break;
				case $this->options['staging-domain']:
					$type = 'staging';
					break;
				case $this->options['production-domain']:
					$type = 'production';
					break;
			}

			if ( $type ) {
				return $type;
			}
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
			$dev_domains    = $this->options['local-domain'];
			$current_domain = $this-> get_current_domain_type();

			if ( $current_domain ) {
				foreach ( $dev_domains as $dev_domain ) {

					if ( $current_domain === $dev_domain ) {
						return true;
					}
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
				$div_style = apply_filters( 'wp_pro_dev_tools_environment_alert_frontend_wrapper', 'style="background: #BDE5F8; padding: 1px 12px; box-shadow: 0 1px 1px 0 rgba( 0,0,0,.3 );"' );
				$p_style   = apply_filters( 'wp_pro_dev_tools_environment_alert_frontend_ptag', 'style="padding: 6px; font-size: 13px; margin: 0; text-align: center; color: #333;"' );
			}

			$domain = $this->get_current_domain_type();

			// Translators: The environment.
			$message = apply_filters( 'wp_pro_dev_tools_environment_alert_message', wp_sprintf( __( 'You are currently working on the <strong>%1$s</strong> environment.', 'wds_wpmu' ), ucwords( $domain ) ), ucwords( $domain ) );

			if ( null !== $domain ) {

				echo '<div class="notice notice-info" ' . wp_kses_post( $div_style ) . '>';
				echo '<p ' . wp_kses_post( $p_style ) . '>' . wp_kses_post( $message ) . '</p>';
				echo '</div>';
			}
		}
	}
}
