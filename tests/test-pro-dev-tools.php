<?php
/**
 * Base Plugin File Test
 *
 * Test the base plugin files
 *
 * @package    Pro_Dev_Tool
 * @subpackage Pro_Dev_Tool/Tests
 * @author     Jason Witt <contact@jawittdesigns.com>
 * @copyright  Copyright (c) 2017, Jason Witt
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

/**
 * Base Plugin File Test
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class Pro_Dev_Tools_Test extends Pro_Dev_Tools_UnitTestCase {

	/**
	 * SetUp.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function setUp() {
		$this->file       = plugin_dir_path( __DIR__ ) . 'pro-dev-tools.php';
		$this->class_name = 'Pro_Dev_Tools';
		$this->methods    = array(
			'plugin_classes',
			'_activate',
			'_deactivate',
			'init',
		);
		$this->proprties  = array(
			'url',
			'path',
			'basename',
		);
	}

	/**
	 * Test that our main helper function is an instance of our class.
	 *
	 * @since  1.0.0
	 */
	function test_get_instance() {
		$this->assertInstanceOf( $this->class_name, pro_dev_tools() );
	}
}
