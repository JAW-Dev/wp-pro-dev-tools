<?php
/**
 * Pro Dev Tools Fields.
 *
 * @author Jason Witt
 * @since  0.0.1
 * @package Pro_Dev_Tools
 */

$notices = ( isset( $this->settings['enable-enviroment-notices'] ) ) ? $this->settings['enable-enviroment-notices'] : '';
?>
<tr>
	<th style="padding: 0 10px 0">
		<h3><?php echo esc_html( __( 'Environment Domains', 'pro-dev-tools' ) ); ?></h3>
	</th>
</tr>
<tr>
	<th colspan="2" style="padding: 0 10px 0">
		<p><?php echo esc_html( __( 'Set the domain for your development environments.', 'pre-dev-tools' ) ); ?></p>
	</th>
</tr>
<tr>
	<th><label for="<?php echo esc_attr( $this->settings_name ); ?>[enable-enviroment-notices]"><?php echo esc_html( __( 'Enable the enviroment notices.', 'pre-dev-tools' ) ); ?></label></th>
	<td><input type="checkbox" name="<?php echo esc_attr( $this->settings_name ); ?>[enable-enviroment-notices]" <?php checked( $notices, 1, true ); ?> value='1' /></td>
</tr>
<tr>
	<th><label for="<?php echo esc_attr( $this->settings_name ); ?>[local-domain]"><?php esc_html_e( 'Local', 'pro-dev-tools' ); ?></label></th>
	<td>
		<input type='text' class="all-options" placeholder="example.dev" name="<?php echo esc_attr( $this->settings_name ); ?>[local-domain]" value='<?php echo esc_html( $this->settings['local-domain'] ); ?>'>
	</td>
</tr>
<tr>
	<th><label for="<?php echo esc_attr( $this->settings_name ); ?>[staging-domain]"><?php esc_html_e( 'Staging', 'pro-dev-tools' ); ?></label></th>
	<td>
		<input type='text' class="all-options" placeholder="staging.example.com" name="<?php echo esc_attr( $this->settings_name ); ?>[staging-domain]" value='<?php echo esc_html( $this->settings['staging-domain'] ); ?>'>
	</td>
</tr>
<tr>
	<th><label for="<?php echo esc_attr( $this->settings_name ); ?>[production-domain]"><?php esc_html_e( 'Production', 'pro-dev-tools' ); ?></label></th>
	<td>
		<input type='text' class="all-options" placeholder="example.com" name="<?php echo esc_attr( $this->settings_name ); ?>[production-domain]" value='<?php echo esc_html( $this->settings['production-domain'] ); ?>'>
	</td>
</tr>
