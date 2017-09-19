<?php
/**
 * General Settings
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
?>
<tr>
	<th>
		<h2><?php echo esc_html( __( 'General Settings', 'wp-pro-dev-tools' ) ); ?></h2>
	</th>
</tr>
<?php
// Enable WP Pro Dev Tools field.
echo wp_kses_post( wppdt_create_checkbox( array(
	'setting'     => 'enable_pro_dev_tools',
	'label'       => __( 'Enable Pro Dev Tools', 'wp-pro-dev-tools' ),
	'description' => __( 'Globaly enable the Pro Dev Tools', 'wp-pro-dev-tools' ),
) ) );
