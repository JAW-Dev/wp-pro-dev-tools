<?php
/**
 * WP Pro Dev Tools Environment Fields.
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

$notices = ( isset( $this->options['enable-enviroment-notices'] ) ) ? $this->options['enable-enviroment-notices'] : '';
?>
<tr>
	<th colspan="2">
		<h3><?php echo esc_html( __( 'Environment Domains', 'pro-dev-tools' ) ); ?></h3>
		<p><?php echo esc_html( __( 'Set the domain for your development environments.', 'pre-dev-tools' ) ); ?></p>
	</th>
</tr>
<?php
// Enable Enviroment Notices field.
echo wp_kses_post( wppdt_create_checkbox( array(
	'setting'     => 'enable-enviroment-notices',
	'label'       => __( 'Enable Enviroment Notices', 'wp-pro-dev-tools' ),
	'description' => __( 'Globaly enable environment notices', 'wp-pro-dev-tools' ),
) ) );
// Local enviroment field.
echo wp_kses_post( wppdt_create_text_input( array(
	'setting'     => 'local-domain',
	'label'       => 'Local',
	'classes'     => 'local-domain',
	'placeholder' => 'example.dev',
) ) );
// Staging enviroment field.
echo wp_kses_post( wppdt_create_text_input( array(
	'setting'     => 'staging-domain',
	'label'       => 'Staging',
	'classes'     => 'staging-domain',
	'placeholder' => 'staging.example.com',
) ) );
// Staging enviroment field.
echo wp_kses_post( wppdt_create_text_input( array(
	'setting'     => 'production-domain',
	'label'       => 'Production',
	'classes'     => 'production-domain',
	'placeholder' => 'example.com',
) ) );
?>
