<?php
/**
 * Pro Dev Tools Settings.
 *
 * @since   1.0.0
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Settings.
 *
 * @since 1.0.0
 */
class PDT_Settings {

	/**
	 * Settings Name.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var type var description
	 */
	protected $settings_name = 'pro_dev_tools_settings';

	/**
	 * Settings.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Active Tab.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var string
	 */
	protected $active_tab;

	/**
	 * Fields.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var object
	 */
	protected $fields;

	/**
	 * Constructor.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function __construct() {

		// Only run if on the admin dashboard.
		if ( is_admin() ) {
			$this->init();
		}
	}

	/**
	 * Initiate.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	public function init() {

		// Bail if not allowed role.
		if ( ! is_allowed_role() ) {
			return;
		}

		// Set the properties.
		$this->settings   = pro_dev_tools()->get_settings;
		$this->active_tab = ( isset( $_GET['tab'] ) ) ? sanitize_text_field( $_GET['tab'] ) : 'allowed-roles';

		// Get the field views.
		$this->fields = $this->include_fields();

		// Run the hooks.
		add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu',  array( $this, 'settings_page' ) );
		add_action( 'init', array( $this, 'save' ) );
		add_action( is_multisite() ? 'network_admin_notices' : 'admin_notices',  array( $this, 'admin_notice' ) );
	}

	/**
	 * Settings Page.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function settings_page() {
		$page = ( is_multisite() ) ? 'settings.php' : 'options-general.php';
		add_submenu_page(
			$page,
			'Pro Dev Tools',
			'Pro Dev Tools',
			'manage_options',
			'pro_dev_tools',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Include Field Views.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return array $fields An array of field views.
	 */
	public function include_fields() {
		$fields = array();
		foreach ( glob( trailingslashit( dirname( __FILE__ ) ) . 'settings/*.php' ) as $file ) {
			$name = str_replace( '.php', '', basename( $file ) );
			$fields[ $name ] = $file;
		}

		return $fields;
	}

	/**
	 * Render Settings Page.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function render_settings_page() {
		$action = ( is_multisite() ) ? 'edit.php?action=' . $this->settings_name : 'options.php';
		?>
		<div class="wrap">
			<!-- <form action="<?php echo esc_attr( $action ); ?>" method="post" /> -->
			<?php settings_errors(); ?>
			<form method="post" />
				<input type="hidden" name="action" value="update_<?php echo esc_attr( $this->settings_name ); ?>" />
				<?php wp_nonce_field( $this->settings_name . '_nonce', $this->settings_name . '_nonce' ); ?>
				<h1><?php echo esc_html( __( 'Pro Dev Tools Settings', 'pro-dev-tools' ) ); ?></h1>
				<h2 class="nav-tab-wrapper">
					<a href="?page=pro_dev_tools&tab=allowed-roles" class="nav-tab<?php echo esc_attr( $this->get_active_tab( 'allowed-roles' ) ); ?>">Allowed Roles</a>
					<a href="?page=pro_dev_tools&tab=environment" class="nav-tab<?php echo esc_attr( $this->get_active_tab( 'environment' ) ); ?>">Environment</a>
				</h2>
				<table class="form-table">
					<tbody>
						<?php
						switch ( $this->active_tab ) {
							case 'environment':
								include $this->fields['environment'];
								break;
							case 'allowed-roles':
							default:
								include $this->fields['allowed-roles'];
								break;
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
	 * Get Active Tab.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $slug The tabd slug.
	 *
	 * @return string $class The class to add to the active tab.
	 */
	public function get_active_tab( $slug ) {
		$class = ( $slug === $this->active_tab ) ? ' nav-tab-active' : '';

		return $class;
	}

	/**
	 * Save.
	 *
	 * @author Jason Witt
	 * @since  1.0.1
	 *
	 * @return void
	 */
	public function save() {

		// Only run if form is saved.
		if ( ! isset( $_POST['submit'] ) ) {
			return;
		}

		// The form nonce.
		$nonce   = $this->settings_name . '_nonce';
		$options = pro_dev_tools()->get_settings;

		// Bail if nonce is not verified.
		if ( ! isset( $_POST[ $nonce ] ) || ! wp_verify_nonce( $_POST[ $nonce ], $nonce ) ) {
			return;
		}

		// The post data.
		$settings = array_merge( $options, $this->sanitize( $_POST[ $this->settings_name ] ) );

		if ( isset( $settings ) ) {
			if ( is_multisite() ) {

				// Update site options.
				update_site_option( $this->settings_name, $settings );
			} else {

				// Update options.
				update_option( $this->settings_name, $settings );
			}
		}

		// Redirect after save.
		$this->redirect_after_save();
	}

	/**
	 * Admin notice.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
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
	 * Redirect After Save.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function redirect_after_save() {

		// Get the current url.
		$current_url = strtok( ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?' );

		// Redirect after save.
		wp_safe_redirect( $current_url . '?page=pro_dev_tools&saved=true' );
		exit;
	}

	/**
	 * Sanitize.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $input The data to input.
	 *
	 * @return array $output The array of sanitized data.
	 */
	public function sanitize( $input ) {

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
		return apply_filters( 'pro_dev_tools_settings_sanitized_data', $output, $input );
	}
}
