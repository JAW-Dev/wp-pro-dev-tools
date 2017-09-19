<?php
/**
 * Template Tags
 *
 * @package    WP_Pro_Dev_Tools
 * @subpackage WP_Pro_Dev_Tools/Includes/Classes/Settings
 * @author     Jason Witt <contact@jawittdesigns.com>
 * @copyright  Copyright (c) 2017, Jason Witt
 * @license    GNU General Public License v2 or later
 * @version    0.0.1
 */

if ( ! function_exists( 'wppdt_create_checkbox' ) ) {
	/**
	 * Create checkbox for settings.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @param array $args {
	 *     The arguments.
	 *
	 *     @type string $option      Name for the field.
	 *     @type string $label       Label for the field.
	 *     @type string $description Description for the field.
	 *     @type string $classes     Custom classes for the table row.
	 *     @type string $plugin_slug The plugin slug.
	 *     @type array  $settings    The plugin settings.
	 * }
	 *
	 * @return void
	 */
	function wppdt_create_checkbox( $args = array() ) {

		// Defaults.
		$defaults = array(
			'setting'     => '',
			'label'       => '',
			'description' => '',
			'classes'     => '',
			'plugin_slug' => \WP_Pro_Dev_Tools\wp_pro_dev_tools()->plugin_slug,
			'options'     => \WP_Pro_Dev_Tools\wp_pro_dev_tools()->get_options,
		);

		// The arguments.
		$args        = wp_parse_args( $args, $defaults );
		$option      = ( isset( $args['setting'] ) ) ? $args['setting'] : '';
		$name        = "{$args['plugin_slug']}[{$args['setting']}]";
		$label       = ( $args['label'] ) ? $args['label'] : '';
		$checked     = ( isset( $args['options'][ $args['setting'] ] ) ) ? $args['options'][ $args['setting'] ] : '';
		$classes     = ( $args['classes'] ) ? ' class=' . $args['classes'] . ' ' : '';
		$description = ( isset( $args['description'] ) ) ? $args['description'] : '';
		?>
		<tr id="wppdt-field__<?php echo esc_attr( $option ); ?>" class="wppdt-field wppdt-field__<?php echo esc_attr( $option ); ?>" >
			<th class='field__heading' scope="row">
				<?php echo esc_html( $label ); ?>
			</th>
			<td class='field__fields'>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo esc_html( $label ); ?></span>
					</legend>
					<label for="<?php echo esc_attr( $name ); ?>">
						<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="false" />
						<input type="checkbox" id="<?php echo esc_attr( $option ); ?>"<?php echo esc_html( $classes ); ?> name="<?php echo esc_attr( $name ); ?>" <?php checked( $checked, 'true' ); ?>  value="true" />
						<span class="field__description"><?php echo esc_html( $description ); ?></span>
					</label>
				</fieldset>
			</td>
		</tr>
		<?php
	}
}

if ( ! function_exists( 'wppdt_create_text_input' ) ) {
	/**
	 * Create checkbox for settings.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @param array $args {
	 *     The arguments.
	 *
	 *     @type string $option      Name for the field.
	 *     @type string $label       Label for the field.
	 *     @type string $description Description for the field.
	 *     @type string $classes     Custom classes for the table row.
	 *     @type string $placeholder Custom placeholder.
	 *     @type string $plugin_slug The plugin slug.
	 *     @type array  $settings    The plugin settings.
	 * }
	 *
	 * @return void
	 */
	function wppdt_create_text_input( $args = array() ) {

		// Defaults.
		$defaults = array(
			'setting'     => '',
			'label'       => '',
			'description' => '',
			'classes'     => '',
			'placeholder' => '',
			'plugin_slug' => \WP_Pro_Dev_Tools\wp_pro_dev_tools()->plugin_slug,
			'options'     => \WP_Pro_Dev_Tools\wp_pro_dev_tools()->get_options,
		);

		// The arguments.
		$args        = wp_parse_args( $args, $defaults );
		$option      = ( isset( $args['setting'] ) ) ? $args['setting'] : '';
		$name        = "{$args['plugin_slug']}[{$args['setting']}]";
		$label       = ( $args['label'] ) ? $args['label'] : '';
		$value       = ( isset( $args['options'][ $args['setting'] ] ) ) ? $args['options'][ $args['setting'] ] : '';
		$classes     = ( $args['classes'] ) ? ' class=' . $args['classes'] . ' ' : '';
		$placeholder = ( $args['placeholder'] ) ? ' placeholder=' . $args['placeholder'] . ' ' : '';
		$description = ( isset( $args['description'] ) ) ? $args['description'] : '';
		?>

		<tr id="wppdt-field__<?php echo esc_attr( $option ); ?>" class="wppdt-field wppdt-field__<?php echo esc_attr( $option ); ?>" >
			<th>
				<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
			</th>
			<td>
				<input type="text" id="<?php echo esc_attr( $option ); ?>"<?php echo esc_html( $classes ) . esc_html( $placeholder ); ?> name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<span class="field__description"><?php echo esc_html( $description ); ?></span>
			</td>
		</tr>
		<?php
	}
}

if ( ! function_exists( 'wppdt_is_allowed_role' ) ) {
	/**
	 * Is Allowed Roles.
	 *
	 * @since  0.0.1
	 * @author Jason Witt
	 *
	 * @return boolean
	 */
	function wppdt_is_allowed_role() {
		$current_user  = wp_get_current_user();
		$options       = ( is_multisite() ) ? get_site_option( 'wp_pro_dev_tools' ) : get_option( 'wp_pro_dev_tools' );
		$allowed_roles = ( isset( $options['allowed_roles'] ) ) ? $options['allowed_roles'] : null;
		if ( is_multisite() && is_network_admin() && is_super_admin() ) {
			return true;
		}
		if ( null !== $allowed_roles ) {
			foreach ( $allowed_roles as $key => $value ) {
				$is_allowed = ( in_array( $key, $current_user->roles, true ) && 'true' === $value ) ? true : false;
				//echo '<pre>'; var_dump( $is_allowed ); echo '</pre>';
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
