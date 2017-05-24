<?php
/**
 * Pro Dev Tools Add Roles Tests.
 *
 * @since   1.0.0
 * @package Pro_Dev_Tools
 */
class PDT_Add_Roles_Test extends Pro_Dev_Tools_UnitTestCase {

	/**
	 * SetUp.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function setUp() {
		$this->file       = plugin_dir_path( __DIR__ ) . 'includes/class-add-roles.php';
		$this->class      = new PDT_Add_Roles();
		$this->class_name = 'PDT_Add_Roles';
		$this->methods    = array(
			'init',
			'get_role_capabilities',
			'add_roles',
		);
		$properties       = array(
			'wp_roles',
		);
	}

	/**
	 * Roles exist.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_add_roles() {

		global $wp_roles;

		$current_roles = (array) $wp_roles->roles;
		$roles         = array(
			'developer',
			'core-administrator',
		);

		foreach ( $roles as $role ) {
			$this->assertTrue( array_key_exists( $role, $current_roles ), 'The role "' . $role . '" doesn\'t exist!' );
		}
	}

	/**
	 * Test get_role_capabilities.
	 *
	 * @author Jason Witt
	 * @since  0.0.1
	 *
	 * @return void
	 */
	public function test_get_role_capabilities() {
		$developer  = $this->class->get_role_capabilities( 'developer' );
		$core_admin = $this->class->get_role_capabilities( 'core-administrator' );
		$result = array(
			'switch_themes'          => true,
			'edit_themes'            => true,
			'activate_plugins'       => true,
			'edit_plugins'           => true,
			'edit_users'             => true,
			'edit_files'             => true,
			'manage_options'         => true,
			'moderate_comments'      => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'upload_files'           => true,
			'import'                 => true,
			'unfiltered_html'        => true,
			'edit_posts'             => true,
			'edit_others_posts'      => true,
			'edit_published_posts'   => true,
			'publish_posts'          => true,
			'edit_pages'             => true,
			'read'                   => true,
			'level_10'               => true,
			'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'edit_others_pages'      => true,
			'edit_published_pages'   => true,
			'publish_pages'          => true,
			'delete_pages'           => true,
			'delete_others_pages'    => true,
			'delete_published_pages' => true,
			'delete_posts'           => true,
			'delete_others_posts'    => true,
			'delete_published_posts' => true,
			'delete_private_posts'   => true,
			'edit_private_posts'     => true,
			'read_private_posts'     => true,
			'delete_private_pages'   => true,
			'edit_private_pages'     => true,
			'read_private_pages'     => true,
			'delete_users'           => true,
			'create_users'           => true,
			'unfiltered_upload'      => true,
			'edit_dashboard'         => true,
			'update_plugins'         => true,
			'delete_plugins'         => true,
			'install_plugins'        => true,
			'update_themes'          => true,
			'install_themes'         => true,
			'update_core'            => true,
			'list_users'             => true,
			'remove_users'           => true,
			'promote_users'          => true,
			'edit_theme_options'     => true,
			'delete_themes'          => true,
			'export'                 => true,
		);
		$this->assertEquals( $result, $developer );
		$this->assertEquals( $result, $core_admin );
	}
}
