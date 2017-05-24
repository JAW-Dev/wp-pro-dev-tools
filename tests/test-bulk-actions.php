<?php
/**
 * Pro Dev Tools Bulk Actions Tests.
 *
 * @since   1.0.0
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Bulk Actions Tests.
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class PDT_Bulk_Actions_Test extends Pro_Dev_Tools_UnitTestCase {

	/**
	 * Test plugin.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var string
	 */
	protected $test_plugin;

	/**
	 * SetUp.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function setUp() {
		$this->file       = plugin_dir_path( __DIR__ ) . 'includes/class-bulk-actions.php';
		$this->class      = new PDT_Bulk_Actions( pro_dev_tools() );
		$this->class_name = 'PDT_Bulk_Actions';
		$this->methods    = array(
			'init',
			'add_bulk_action',
			'bulk_action_handler',
		);
		$this->proprties  = array(
			'required_plugin',
		);

		// Create user with developer role.
		$user_id = $this->factory->user->create( array( 'role' => 'developer' ) );
		wp_set_current_user( $user_id );

		// Set the current screen.
		set_current_screen( 'plugins' );
		$this->go_to( admin_url( 'plugins.php' ) );

		$this->test_plugin = 'pro-dev-tools/pro-dev-tools.php';
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
		$actions = ( is_multisite() ) ? 'bulk_actions-plugins-network' : 'bulk_actions-plugins';
		$handle  = ( is_multisite() ) ? 'handle_bulk_actions-plugins-network' : 'handle_bulk_actions-plugins';
		$hooks = array(
			array(
				'hook_name' => $actions,
				'method'    => 'add_bulk_action',
				'priority'  => 10,
			),
			array(
				'hook_name' => $handle,
				'method'    => 'bulk_action_handler',
				'priority'  => 10,
			),
		);
		foreach ( $hooks as $hook ) {
			$this->assertEquals( $hook['priority'], has_action( $hook['hook_name'], array( $this->class, $hook['method'] ) ), 'init() is not attaching ' . $hook['method'] . '() to ' . $hook['hook_name'] . '!' );
		}
	}

	/**
	 * Test Bulk Action.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_bulk_action() {

		// No required plugins set.
		$expected = 'required';
		$array = $this->class->add_bulk_action( array() );
		$this->assertArrayHasKey( $expected, $array );
		$this->assertEquals( 'Required', $array[ $expected ] );

		// With required plugin set.
		$this->set_property( $this->class, 'required_plugins', array( $this->test_plugin ) );
		$expected = 'not-required';
		$array = $this->class->add_bulk_action( array() );
		$this->assertArrayHasKey( $expected, $array );
		$this->assertEquals( 'Not Required', $array[ $expected ] );
	}
}
