<?php
/**
 * Autoloader
 *
 * @package    WP_Pro_Dev_Tools
 * @subpackage WP_Pro_Dev_Tools/Includes
 * @author     Jason Witt <{{plugin-author-email}}>
 * @copyright  Copyright (c) 2017, Jason Witt
 * @license    GNU General Public License v2 or later
 * @version    0.0.1
 */

namespace WP_Pro_Dev_Tools\Includes;

/**
 * Autoloader
 *
 * @param string $class Name of the class being requested.
 *
 * @since 0.0.1
 *
 * @return void
 */
function _autoload_classes( $class ) {

	// Get the class name.
	$class = explode( '\\', $class );

	// Full path to the classes directory.
	$path  = trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'classes' );

	// Constructed file with full path to autoload.
	$file  = $path . 'class-' . strtolower( str_replace( '_', '-', end( $class ) ) ) . '.php';

	// Add classes to be excluded from autoloading. example: array( $path . 'class-name.php' );.
	$excludes  = array();

	// Require file if the file exists and is not in the excludes list.
	if ( file_exists( $file ) && ! in_array( $file, $excludes, true ) ) {

		require_once( $file );
	}
}
spl_autoload_register( __NAMESPACE__ . '\\_autoload_classes' );
