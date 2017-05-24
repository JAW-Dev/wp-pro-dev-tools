<?php
/**
 * Pro_Dev_Tools.
 *
 * @since   1.0.0
 * @package Pro_Dev_Tools
 */
abstract class Pro_Dev_Tools_UnitTestCase extends WP_UnitTestCase {

	/**
	 * File.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var strinf
	 */
	protected $file = '';

	/**
	 * Class Object.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var obnject
	 */
	protected $class = '';

	/**
	 * Class name.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var string
	 */
	protected $class_name = '';

	/**
	 * Methods.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected $methods = array();

	/**
	 * Properties.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected $properties = array();

	/**
	 * Test if file file exists.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_file_exists() {

		// Is String sanity check.
		if ( $this->file && is_string( $this->file ) || $this->file && ! empty( $this->file ) ) {
			$this->assertFileExists( $this->file );
		}
	}

	/**
	 * Test if class exists.
	 *
	 * @since  1.0.0
	 */
	public function test_class_exists() {

		// Is String sanity check.
		if ( $this->class_name && is_string( $this->class_name ) || $this->class_name && ! empty( $this->class_name ) ) {
			$this->assertTrue( class_exists( $this->class_name ), 'The class "' . $this->class_name . '()" doesn\'t exist!' );
		}
	}

	/**
	 * Test if methods exist.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_methods_exist() {

		// Is Array sanity check.
		if ( $this->methods && ( is_array( $this->methods ) && ! empty( $this->methods ) ) ) {
			foreach ( $this->methods as $method ) {
				$this->assertTrue( method_exists( $this->class_name, $method ), 'The method "' . $method . '()" doesn\'t exist!' );
			}
		}
	}

	/**
	 * Test if wp_roles property exists.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_property_exists() {

		// Is Array sanity check.
		if ( $this->properties && ( is_array( $this->properties ) && ! empty( $this->properties ) ) ) {
			foreach ( $this->properties as $property ) {
				$this->assertTrue( property_exists( $this->class_name, $property ), 'The property "$' . $property . '" doesn\'t exist!' );
			}
		}
	}

	/**
	 * Set Property.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @param object $object   The class object.
	 * @param string $property The name of the property to set.
	 * @param mixed  $value    The value to set the property to.
	 *
	 * @return void
	 */
	public function set_property( $object, $property, $value ) {
		$reflection = new \ReflectionClass( $object );
		$reflection_property = $reflection->getProperty( $property );
		$reflection_property->setAccessible( true );
		$reflection_property->setValue( $object, $value );
	}

	/**
	 * Invoke Private Method.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param object $object      The class object.
	 * @param string $method_name The name of the method to invoke.
	 * @param mixed  $parameters  The parameters of the method.
	 *
	 * @return mixed Method return.
	 */
	public function invoke_private_method( &$object, $method_name, $parameters = array() ) {
		$reflection = new \ReflectionClass( get_class( $object ) );
	    $reflection_method = $reflection->getMethod( $method_name );
	    $reflection_method->setAccessible( true );

	    return $reflection_method->invokeArgs( $object, $parameters );
	}
}
