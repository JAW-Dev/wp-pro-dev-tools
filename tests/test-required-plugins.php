<?php
/**
 * Pro Dev Tools Required Plugins Tests.
 *
 * @since   1.0.0
 * @package Pro_Dev_Tools
 */

/**
 * Pro Dev Tools Required Plugins Tests.
 *
 * @author Jason Witt
 * @since  1.0.
 */
class PDT_Required_Plugins_Test extends Pro_Dev_Tools_UnitTestCase {

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
		$this->file       = plugin_dir_path( __DIR__ ) . 'includes/class-required-plugins.php';
		$this->class      = new PDT_Required_Plugins( pro_dev_tools() );
		$this->class_name = 'PDT_Required_Plugins';
		$this->methods    = array(
			'toggle_required_plugin',
			'redirect_after_update',
			'add_required',
			'remove_required',
		);
		$this->proprties  = array(
			'is_required',
			'required_plugin',
		);
		set_current_screen( 'plugins' );
		// Create user with developer role.
		$user_id = $this->factory->user->create( array( 'role' => 'developer' ) );
		wp_set_current_user( $user_id );
		$this->go_to( admin_url( 'plugins.php' ) );

		$this->test_plugin = 'pro-dev-tools/pro-dev-tools.php';
	}

	/**
	 * Test Init.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_init() {
		$mock = $this->getMockBuilder( $this->class_name )->setMethods( array(
				'toggle_required_plugin',
			) )->getMock();
		$mock->expects( $this->once() )->method( 'toggle_required_plugin' );
		$mock->init();
	}

	/**
	 * Test Set Properties.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_set_properties() {
		$this->go_to( admin_url( 'plugins.php?plugin=' . $this->test_plugin . '&required=true' ) );
		$plugin      = ( isset( $_GET['plugin'] ) ) ? sanitize_text_field( $_GET['plugin'] ) : null;
		$is_required = ( isset( $_GET['required'] ) ) ? sanitize_text_field( $_GET['required'] ) : null;

		$this->assertEquals( $this->test_plugin, $plugin );
		$this->assertEquals( 'true', $is_required );
		$this->assertNotTrue( get_option( '_pdt_required_plugins' ) );
	}

	/**
	 * Test Add Required.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_add_required() {
		$expected = array( $this->test_plugin );
		$this->assertEquals( $expected, $this->class->add_required( $this->test_plugin ) );
	}

	/**
	 * Test Remove Required.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_remove_required() {
		$expected = array();
		update_option( '_pdt_required_plugins', array( $this->test_plugin ), true );
		$this->assertEquals( $expected, $this->class->remove_required( $this->test_plugin ) );
	}
}
