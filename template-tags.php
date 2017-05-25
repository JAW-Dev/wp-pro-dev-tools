<?php
/**
 * Pro Dev Tools Action Links.
 *
 * @author Jason Witt
 * @since  0.0.1
 *
 * @package Pro_Dev_Tools
 */

if ( ! function_exists( 'is_allowed_role' ) ) {
	/**
	 * Is Developer.
	 *
	 * @since 1.0.1
	 * @author Jason Witt
	 *
	 * @return boolean
	 */
	function is_allowed_role() {
		$current_user  = wp_get_current_user();
		$settings      = pro_dev_tools()->get_settings;
		$allowed_roles = ( isset( $settings['allowed_roles'] ) ) ? $settings['allowed_roles'] : null;
		if ( is_multisite() && is_network_admin() && is_super_admin() ) {
			return true;
		}
		if ( null !== $allowed_roles ) {
			foreach ( $allowed_roles as $role ) {
				$is_allowed = ( in_array( $role, $current_user->roles, true ) ) ? true : false;
				if ( $is_allowed || current_user_can( 'developer' ) ) {
					return true;
				}
			}
		}
		if ( current_user_can( 'developer' ) ) {
			return true;
		}
		return false;
	}
}
