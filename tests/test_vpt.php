<?php

/**
 * Tests to test that that testing framework is testing tests. Meta, 
 *
 * @package wordpress-plugins-tests
 */
class WP_Test_Vpt extends WP_UnitTestCase 
{
	/* A reference to the plugin */
 	private $vpt;
 	private $current_user;

	/* Set and initiate the plugins here */
 	function setUp() 
 	{
		parent::setUp();
 		$this->vpt = new VirtualPagesTemplates;
 		$this->current_user = get_current_user_id();

 		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
 		
 	}
 
 	function tearDown() 
 	{
 		parent::tearDown();
 	}

	/**
	 * If these tests are being run on Travis CI, verify that the version of
	 * WordPress installed is the version that we requested.
	 *
	 * @requires PHP 5.3
	 */
	function test_wp_version() 
	{

		if ( !getenv( 'TRAVIS' ) )
			$this->markTestSkipped( 'Test skipped since Travis CI was not detected.' );

		$requested_version = getenv( 'WP_VERSION' ) . '-src';

		// The "master" version requires special handling.
		if ( $requested_version == 'master-src' ) 
		{
			$file = file_get_contents( 'https://raw.github.com/tierra/wordpress/master/src/wp-includes/version.php' );
			preg_match( '#\$wp_version = \'([^\']+)\';#', $file, $matches );
			$requested_version = $matches[1];
		}

		$this->assertEquals( get_bloginfo( 'version' ), $requested_version );

	}

	/**
	 * Ensure that the plugin has been installed and activated.
	 */
	function test_plugin_activated() 
	{
		$this->assertTrue( is_plugin_active( VPT_PLUGIN_TO_TEST ) );
	}

	/**
 	* Verifies that the plugin isn't null and was properly retrieved.
 	 */
 	function test_plugin_init() 
 	{
 		$this->assertFalse( null == $this->vpt );
 	}
	
 	// The plugin core functionality tests 
 	// Add tests here for the plugins core functionalities

 	/**
 	 * Test if the menu was added
 	 */
 	function test_plugin_menu_added()
 	{
 		$this->set_admin_user();

 		$this->vpt->display_menu();

 		$this->assertNotNull($this->vpt->menu_slug);
 		$this->assertEquals(VPT_PLUGIN_FOLDER . '/form.php', str_replace('admin_page_', '', $this->vpt->menu_slug));

 		$this->set_default_user();
 	}

 	/**
 	 * test if JS was loaded
 	 */
 	function test_plugin_js_loaded(){
 		//list javascript used by plugin
 		$plugin_js = array('vpt-scripts');
 
 		//load the js
 		$this->vpt->admin_includes();

 		//Assert
 		foreach($plugin_js as $js){
 			if(wp_script_is($js, 'enqueued')===FALSE){
 				$this->fail();
 			}
 		}
 	}

 	/**
 	 * sets the current user as the admin / temporarily overrides the current user
 	 */
 	private function set_admin_user()
 	{
 		wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
 	}

 	/**
 	 * sets the current user back to default
 	 */
 	private function set_default_user()
 	{
 		wp_set_current_user( $this->current_user );
 	}

}