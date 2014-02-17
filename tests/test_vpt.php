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
 	private $keyword_tag = '%vpt-keyword%';

 	private $test_vpt_keyword = 'this is a test keyword';

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
 	 * form validation
 	 */
 	function test_form_submission()
 	{	
 		// will use current permalink struct but no template used
 		$post = array('vpt_hidden' => 'Y');
 		$this->assertFalse($this->check_posts($post));
 		// use_custom_permalink_structure url s true but no custom url assigned
 		$post = array('vpt_hidden' => 'Y', 'use_custom_permalink_structure' => 1);
 		$this->assertFalse($this->check_posts($post));
 		// use_custom_permalink_structure url s true but no template assigned
 		$post = array('vpt_hidden' => 'Y', 'use_custom_permalink_structure' => 1, 'virtualpageurl' => '123');
 		$this->assertFalse($this->check_posts($post));
 		// use_custom_permalink_structure url is true has custom url and has template
 		$post = array('vpt_hidden' => 'Y', 'use_custom_permalink_structure' => 1, 'virtualpageurl' => '123', 'page_template' => 1);
 		$this->assertTRUE($this->check_posts($post));	
 		// used a template and will use current permalink struct
 		$post = array('vpt_hidden' => 'Y', 'page_template' => 1);
 		$this->assertTRUE($this->check_posts($post), 'xxxx');
 	}

 	/**
 	 * mimic form validation process
 	 */
 	private function check_posts($post)
 	{
 		if(isset($post['vpt_hidden']) && $post['vpt_hidden'] == 'Y') 
 		{  
			if (isset($post['use_custom_permalink_structure']) && empty($post['virtualpageurl'])){
				return FALSE;
			}
			elseif (!isset($post['page_template'])){
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
 	}

 	/**
 	 * check if a keyword can be generated
 	 */
 	function test_init_keyword()
 	{
 		$this->assertTrue(true);
 		$this->vpt->keyword = NULL;
 		// a virtual page
 		
 		// a regular post / page
 		$virtualpageurl = '/shop/%postname%';
 		$current_url = '/'. $this->test_vpt_keyword;
 		$this->vpt->init_keyword($current_url, $virtualpageurl);
 		$this->assertNull($this->vpt->keyword);

 		$virtualpageurl = '/shop/%postname%';
 		$current_url = '/shop/'. $this->test_vpt_keyword;;
 		$this->vpt->init_keyword($current_url, $virtualpageurl);
 		$this->assertEquals($this->test_vpt_keyword, $this->vpt->keyword);
 	}

 	/**
 	 * test content replacement method if actually replacing the content
 	 */
 	function test_get_template_content()
 	{
 		$this->vpt->keyword = $this->test_vpt_keyword;
 		
 		$test_content = 'a test content with keyword - `'. $this->keyword_tag .'`';
 		$expected_output = str_replace($this->keyword_tag, $this->vpt->keyword, $test_content);
 		
 		// create test post
 		$id = $this->factory->post->create(array('post_title' => 'a test title', 'post_content' => $test_content));
 		$this->vpt->options = array('page_template' => $id);
 		
 		$this->vpt->keyword = $this->test_vpt_keyword;
 		$output = $this->vpt->get_template_content();

 		$this->assertEquals($expected_output, $output);
 	}

 	/**
 	 * test the actual virtual content creation
 	 */
 	function test_create_virtual()
 	{
 		$kw_url = $this->test_vpt_keyword;
 		// test urls
 		$test_wp_urls = array('/'.$kw_url, '/shop/'.$kw_url, '/keyword/'.$kw_url, '/keyword/'.$kw_url.'/testing');
 		$test_virtual_urls = array('/%postname%', '/shop/%postname%', '/keyword/%postname%/testing');
 		$test_wp_permalinks = array('/%postname%', '/abc/%postname%');

 		// init
 		$this->vpt->keyword = $kw_url;
 		$test_content = 'a test content with keyword - `'. $this->keyword_tag .'`';
		$id = $this->factory->post->create(array('post_title' => 'a test title', 'post_content' => ''));

 		$this->update_vpt_option(TRUE, '/shop/%postname%/', $id, 'post');

 		
 		// use custom permalink
 		$this->start_asserting($test_virtual_urls, $test_wp_urls, $id );
 		// do not use custom permalink
 		$this->start_asserting($test_wp_permalinks, $test_wp_urls, $id );
 	}

 	private function start_asserting($permalinks, $urls, $post_id)
 	{
 		// do not use custom permalink
 		foreach ($permalinks as $permalink)
 		{
 			foreach ($urls as $url)
 			{
 				$_SERVER['REQUEST_URI'] = $url;

 				$permalink_converted = str_replace('%postname%', $this->test_vpt_keyword, $permalink);

 				$this->update_vpt_option(TRUE, $permalink, $post_id, 'post');
 				if ($permalink_converted == $url)
 				{
 					// redirect to a virtual post / page
 					$this->assertNotEmpty( $this->vpt->create_virtual(array()), $permalink_converted . ' ' . $url );	
 				}
 				else
 				{
 					// redirect to normal post / page
 					$this->assertEmpty( $this->vpt->create_virtual(array()), $permalink_converted . ' ' . $url );	
 				}
 			}
 		} 
 	}

 	/**
 	 * update vpt options
 	 */
 	private function update_vpt_option($use_custom_permalink_structure = 0, $virtualpageurl = NULL, $page_template = NULL, $post_type = 'post')
 	{
 		$post = array('use_custom_permalink_structure' => $use_custom_permalink_structure, 
 			'virtualpageurl' => $virtualpageurl, 
 			'page_template' => $page_template, 
 			'post_type' => $post_type);
 		update_option('vpt_options', $post);
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