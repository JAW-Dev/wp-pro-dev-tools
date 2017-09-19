<?php
/**
 * WP Pro Dev Tools Add Roles.
 *
 * Add custom roles.
 * Developer: For special functionality only accessible to developers.
 * Core Administrator: A custom Administrator for access to this plugin
 * on the plugin.php admin page. It's recomended to add least one
 * non-developer administrator this role.
 *
 * @package    WP_Pro_Dev_Tools
 * @subpackage WP_Pro_Dev_Tools/Includes/Classes
 * @author     Jason Witt <contact@jawittdesigns.com>
 * @copyright  Copyright (c) 2017, Jason Witt
 * @license    GNU General Public License v2 or later
 * @version    0.0.1
 */

namespace WP_Pro_Dev_Tools\Includes\Classes;

if ( ! class_exists( '\\WP_Pro_Dev_Tools\\Includes\\Classes\\Add_Roles' ) ) {

	/**
	 * Add Roles
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 */
	class Add_Roles {

		/**
		 * Roles.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var object
		 */
		protected $wp_roles;

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
		 * @since  0.0.1
		 */
		public function init() {
			add_action( 'init', array( $this, 'add_roles' ) );
		}

		/**
		 * Get the administrator capabilities.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param string $role The role capabilities to get.
		 *
		 * @return array $capabilities The administrator capabilities.
		 */
		public function get_role_capabilities( $role ) {

			global $wp_roles;

			// Bail if $wp_roles global is not set.
			if ( ! $wp_roles || ( ! $wp_roles instanceof WP_Roles ) ) {
				return;
			}

			// Get the role. Convert object to array.
			$the_role = (array) $wp_roles->get_role( $role );

			// Bail if the role is not set.
			if ( ! $the_role || ! is_array( $the_role ) || empty( $the_role ) ) {
				return;
			}

			/**
			 * Clone Capabilities
			 *
			 * @author Jason Witt
			  * @since  0.0.1
			  *
			 * @param array The cpabilities of the role.
			 */
			$capabilities = apply_filters( 'wp_pro_dev_tools_clone_capabilities', $the_role['capabilities'] );

			// Return empty if the role capabilities are not set.
			if ( ! $capabilities || ! is_array( $capabilities ) || empty( $capabilities ) ) {
				return array();
			}
			return $capabilities;
		}

		/**
		 * Add Developer Role.
		 *
		 * Add a custom role cloned from the administrator role.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function add_roles() {

			$get_role = apply_filters( 'wp_pro_dev_tools_get_role', 'administrator' );

			$capabilities = $this->get_role_capabilities( 'administrator' );

			// The custom roles to create.
			$pdt_roles = array(
				array(
					'role' => 'developer',
					'name' => __( 'Developer', 'wp-pro-dev-tools' ),
					'caps' => $capabilities,
				),
			);

			$roles = apply_filters( 'wp_pro_dev_tools_roles', $pdt_roles );

			// Bail if $capabilities is not set.
			if ( ! $capabilities || ! is_array( $capabilities ) || empty( $capabilities ) ) {
				return;
			}

			// Add the roles.
			foreach ( $roles as $role ) {
				add_role( $role['role'], $role['name'], $role['caps'] );
			}
		}
	}
}
