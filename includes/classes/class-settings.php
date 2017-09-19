<?php
/**
 * Settings
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

if ( ! class_exists( 'Settings' ) ) {

	/**
	 * Settings
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 */
	class Settings {

		/**
		 * Plugin Slug.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @var string
		 */
		protected $plugin_slug;

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
		 * Active Tab.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @var string
		 */
		protected $active_tab;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function __construct() {
			// Only run if on the admin dashboard.
			if ( is_admin() ) {
				$this->init();
			}

			// Set the properties.
			$this->plugin_slug = Root\wp_pro_dev_tools()->plugin_slug;
			$this->options     = Root\wp_pro_dev_tools()->get_options;
			$this->active_tab  = ( isset( $_GET['tab'] ) ) ? sanitize_text_field( $_GET['tab'] ) : '';

			// Get the field views.
			$this->fields = ( is_array( $this->include_fields() ) ) ? $this->include_fields() : array();
		}

		/**
		 * Init
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function init() {
			add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', array( $this, 'settings_page' ) );
			add_action( 'init', array( $this, 'save' ) );
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'admin_notice' ) );
			}
		}

		/**
		 * Settings Page.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function settings_page() {
			$page = ( is_multisite() ) ? 'settings.php' : 'options-general.php';
			add_submenu_page(
				$page,
				__( 'Pro Dev Tools', 'wp-pro-dev-tools' ),
				__( 'Pro Dev Tools', 'wp-pro-dev-tools' ),
				'manage_options',
				$this->plugin_slug,
				array( $this, 'render_settings_page' )
			);
		}

		/**
		 * Render Settings Page.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function render_settings_page() {
			$network_url = network_admin_url( 'edit.php?action  =' ) . $this->plugin_slug;
			$admin_url   = admin_url( 'options-general.php?page =' ) . $this->plugin_slug;
			$action      = ( is_multisite() ) ? $network_url : $admin_url;
			$enable      = $this->options['enable_pro_dev_tools'];
			$is_enabled  = ( isset( $enable ) ) ? $enable : 'false';
			?>
			<div class="wrap">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<?php if ( 'true' === $is_enabled ) : ?>
					<h2 class="nav-tab-wrapper">
						<?php
						echo wp_kses_post(
							$this->tabs( array(
								'general'       => __( 'General', 'wp-pro-dev-tools' ),
								'environment'   => __( 'Environment', 'wp-pro-dev-tools' ),
								'roles'         => __( 'Roles', 'wp-pro-dev-tools' ),
							) )
						);
						?>
					</h2>
				<?php endif; ?>
				<?php settings_errors(); ?>
				<form action="<?php echo esc_attr( $action ); ?>" method="post">
					<input type="hidden" name="action" value="update_<?php echo esc_attr( $this->plugin_slug ); ?>" />
					<?php wp_nonce_field( $this->plugin_slug . '_nonce', $this->plugin_slug . '_nonce' ); ?>
					<table class="form-table">
						<tbody>
							<?php
							if ( ! empty( $this->fields ) ) {
								switch ( $this->active_tab ) {
									case 'environment':
										include $this->fields['environment'];
										break;
									case 'roles':
										include $this->fields['roles'];
										break;
									case 'general':
									default:
										include $this->fields['general'];
										break;
								}
							}
							?>
							<tr><td><?php submit_button(); ?></td></tr>
						</tbody>
					</table>
				</form>
			</div>
			<?php
		}

		/**
		 * Tabs.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param array $args The tabs arguments.
		 *
		 * @return void
		 */
		public function tabs( $args = array() ) {
			foreach ( $args as $slug => $title ) :
				?>
				<a href="?page=<?php echo esc_html( $this->plugin_slug ); ?>&tab=<?php echo esc_attr( $slug ); ?>" class="nav-tab<?php echo esc_attr( $this->get_active_tab( $slug ) ); ?>"><?php echo esc_html( $title ); ?></a>
				<?php
			endforeach;
		}

		/**
		 * Admin notice.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function admin_notice() {
			if ( isset( $_GET['saved'] ) ) {
				?>
				<div class="notice notice-success is-dismissible">
					<p><strong>Settings saved.</strong></p>
				</div>
				<?php
			}
		}

		/**
		 * Save.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		public function save() {

			// Only run if form is saved.
			if ( ! isset( $_POST['submit'] ) ) {
				return;
			}

			// The form nonce.
			$nonce   = $this->plugin_slug . '_nonce';
			$options = $this->options;
			if ( isset( $_POST[ $this->plugin_slug ] ) ) {
				$post = $_POST[ $this->plugin_slug ];
			} else {
				return;
			}

			// Bail if nonce is not verified.
			if ( ! isset( $_POST[ $nonce ] ) || ! wp_verify_nonce( $_POST[ $nonce ], $nonce ) ) {
				return;
			}

			// // The post data.
			$settings = ( isset( $post ) || ! empty( $post ) ) ? $this->sanitize( $post ) : array();
			$settings = array_merge( $options, $post );

			if ( isset( $settings ) ) {
				if ( is_multisite() ) {

					// Update site options.
					update_site_option( $this->plugin_slug, $settings );
				} else {

					// Update options.
					update_option( $this->plugin_slug, $settings );
				}
			}

			// Redirect after save.
			$this->redirect_after_save();
		}

		/**
		 * Include Field Views.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return array $fields An array of field views.
		 */
		private function include_fields() {
			$fields = array();
			$dir    = trailingslashit( dirname( __FILE__ ) ) . 'settings';
			$files  = scandir( trailingslashit( dirname( __FILE__ ) ) . 'settings' );
			foreach ( $files as $file ) {
				if ( ('.' === $file ) || ( '..' === $file ) ) {
					continue;
				}
				$file      = trailingslashit( $dir ) . $file;
				$header    = get_file_data( $file, array( 'Load' => 'Load' ) );
				$extension = substr( $file, strrpos( $file, '.' ) + 1 );

				if ( 'true' === $header['Load'] && 'php' === $extension ) {
					$name            = str_replace( '.php', '', basename( $file ) );
					$fields[ $name ] = $file;
				}
			}

			return $fields;
		}

		/**
		 * Get Active Tab.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param string $slug The tabd slug.
		 *
		 * @return string $class The class to add to the active tab.
		 */
		private function get_active_tab( $slug ) {
			$class = ( $slug === $this->active_tab ) ? ' nav-tab-active' : '';
			return $class;
		}

		/**
		 * Sanitize.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @param array $input The data to input.
		 *
		 * @return array $output The array of sanitized data.
		 */
		private function sanitize( $input ) {

			$output = array();

			// Loop though the post data.
			foreach ( $input as $key => $value ) {

				if ( is_array( $value ) ) {

					// Loop though data if multidimensional array.
					$this->sanitize( $value );
				}

				// Strip out any HTML or JS tags or slashes.
				$output[ $key ] = ( ! is_array( $value ) ) ? sanitize_text_field( $value ) : $value;
			}
			// Filter.
			return apply_filters( "{$this->plugin_slug}_settings_sanitized_data", $output, $input );
		}

		/**
		 * Redirect After Save.
		 *
		 * @author Jason Witt
		 * @since  0.0.1
		 *
		 * @return void
		 */
		private function redirect_after_save() {

			// Get the current url.
			$current_url = strtok( ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?' );

			// Redirect after save.
			wp_safe_redirect( $current_url . "?page={$this->plugin_slug}&saved=true" );
			exit;
		}
	}
}
