<?php
/**
 * Pro Dev Tools Settings Tests.
 *
 * @since   1.0.0
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Settings Tests.
 *
 * @author Jason Witt
 * @since  0.0.1
 */
class PDT_Settings_Test extends Pro_Dev_Tools_UnitTestCase {

	/**
	 * SetUp.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function setUp() {
		$this->file       = plugin_dir_path( __DIR__ ) . 'includes/class-settings.php';
		$this->class      = new PDT_Settings( pro_dev_tools() );
		$this->class_name = 'PDT_Settings';
		$this->methods    = array(
			'init',
			'settings_page',
			'render_settings_page',
			'save',
			'admin_notice',
			'redirect_after_save',
			'sanitize',
		);
		$this->proprties  = array(
			'settings_name',
			'settings',
		);

		// Create user with developer role.
		$user_id = $this->factory->user->create( array( 'role' => 'developer' ) );
		wp_set_current_user( $user_id );

		// Set the current screen.
		$this->go_to( admin_url() );
	}

	/**
	 * Test Hooks.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_hooks() {
		$this->class->init();
		$menu    = ( is_multisite() ) ? 'network_admin_menu' : 'admin_menu';
		$notices = ( is_multisite() ) ? 'network_admin_notices' : 'admin_notices';
		$hooks = array(
			array(
				'hook_name' => $menu,
				'method'    => 'settings_page',
				'priority'  => 10,
			),
			array(
				'hook_name' => 'init',
				'method'    => 'save',
				'priority'  => 10,
			),
			array(
				'hook_name' => $notices,
				'method'    => 'admin_notice',
				'priority'  => 10,
			),
		);
		foreach ( $hooks as $hook ) {
			$this->assertEquals( $hook['priority'], has_action( $hook['hook_name'], array( $this->class, $hook['method'] ) ), 'init() is not attaching ' . $hook['method'] . '() to ' . $hook['hook_name'] . '!' );
		}
	}
}
