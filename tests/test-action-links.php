<?php
/**
 * Pro Dev Tools Action Links Tests.
 *
 * @since   1.0.0
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Action Links Tests.
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class PDT_Action_Links_Test extends Pro_Dev_Tools_UnitTestCase {

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
		$this->file       = plugin_dir_path( __DIR__ ) . 'includes/class-action-links.php';
		$this->class      = new PDT_Action_Links( pro_dev_tools() );
		$this->class_name = 'PDT_Action_Links';
		$this->methods    = array(
			'init',
			'get_active_plugins',
			'add_action_links',
		);
		$this->proprties  = array(
			'required_plugins',
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
		$action_links  = ( is_multisite() ) ? 'network_admin_plugin_action_links' : 'plugin_action_links';
		$hooks = array(
			array(
				'hook_name' => $action_links,
				'method'    => 'add_action_links',
				'priority'  => 10,
			),
		);
		foreach ( $hooks as $hook ) {
			$this->assertEquals( $hook['priority'], has_action( $hook['hook_name'], array( $this->class, $hook['method'] ) ), 'init() is not attaching ' . $hook['method'] . '() to ' . $hook['hook_name'] . '!' );
		}
	}

	/**
	 * Test Action Links.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_action_links() {

		// No required plugins set.
		$expected = '<a href="plugins.php?plugin=' . $this->test_plugin . '&required=true">Required</a>';
		$array    = $this->class->add_action_links( array(), $this->test_plugin );
		$this->assertTrue( in_array( $expected, $array ) );

		// With required plugin set.
		$this->set_property( $this->class, 'required_plugins', array( $this->test_plugin ) );
		$expected = '<a href="plugins.php?plugin=' . $this->test_plugin . '&required=false">Not Required</a>';
		$array    = $this->class->add_action_links( array(), $this->test_plugin );
		$this->assertTrue( in_array( $expected, $array ) );
	}
}
