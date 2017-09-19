<?php
/**
 * Pro Dev Tools Fields.
 *
 * Load: true
 *
 * @package    WP_Pro_Dev_Tools
 * @subpackage WP_Pro_Dev_Tools/Includes/Classes/Settings
 * @author     Jason Witt <contact@jawittdesigns.com>
 * @copyright  Copyright (c) 2017, Jason Witt
 * @license    GNU General Public License v2 or later
 * @version    0.0.1
 */

global $wp_roles;

$roles = $wp_roles->role_names;

// Bail if array is not set.
if ( ! $roles || ! is_array( $roles ) || empty( $roles ) ) {
	return;
}

foreach ( $roles as $key => $value ) {
	if ( 'developer' === $key ) {
		unset( $roles[ $key ] );
	}
}

?>
<tr>
	<th style="padding: 0 10px 0">
		<h2><?php echo esc_html( __( 'Allowed Roles', 'pro-dev-tools' ) ); ?></h2>
		<p><?php echo esc_html( __( 'Select the administrator roles allowed to access the Pro Dev Tools features.', 'pre-dev-tools' ) ); ?></p>
	</th>
</tr>
<tr>
	<th><?php echo esc_html( __( 'Developer', 'pro-dev-tools' ) ); ?></th>
	<td><input type="checkbox" checked="checked" disabled="disabled" /></td>
</tr>
<?php
foreach ( $roles as $key => $value ) {
	$caps = get_role( $key );
	if ( array_key_exists( 'manage_options', $caps->capabilities ) ) {
		$setting = 'allowed_roles';
		$name    = "{$this->plugin_slug}[{$setting}][{$key}]";
		$checked = ( isset( $this->options['allowed_roles'][ $key ] ) ) ? $this->options['allowed_roles'][ $key ] : '';
		?>
		<tr>
			<th><label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $value ); ?></label></th>
			<td>
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="false" />
				<input type="checkbox" id="<?php echo esc_attr( $setting . '-' . $value ); ?>"name="<?php echo esc_attr( $name ); ?>" <?php checked( $checked, 'true' ); ?>  value="true" />
			</td>
		</tr>
		<?php
	}
}
