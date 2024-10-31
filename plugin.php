<?php
/*
Plugin Name: Related Links Blender
Plugin URI: http://wordpress.org/extend/plugins/related-links-blender/
Description: This plugin adds some author chosen replated links to page or post content
Author: Blender 
Version: 0.81
Author URI: http://blender.ca/wordpress-plugin-related-links/


License:

  Copyright 2013 TODO (email@domain.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/


/*
helpful code credits:
https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate


*/


/**
 * Define some useful constants
 **/
define('RelatedLinksBlender_VERSION', '0.6');
define('RelatedLinksBlender_DIR', plugin_dir_path(__FILE__));
define('RelatedLinksBlender_URL', plugin_dir_url(__FILE__));




class RelatedLinksBlender {
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
		
		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );
	
		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		//register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );
		
	    /*
	     * TODO:
	     * Define the custom functionality for your plugin. The first parameter of the
	     * add_action/add_filter calls are the hooks into which your code should fire.
	     *
	     * The second parameter is the function name located within this class. See the stubs
	     * later in the file.
	     *
	     * For more information: 
	     * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
	     */
	    add_action( 'TODO', array( $this, 'action_method_name' ) );
	    add_filter( 'TODO', array( $this, 'filter_method_name' ) );




	} // end constructor
	
	/**
	 * Fired when the plugin is activated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function activate( $network_wide ) {

		$this->rlb_plugin_options_activation_initialize();

	} // end activate
	
	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function deactivate( $network_wide ) {
		// TODO:	Define deactivation functionality here		
	} // end deactivate
	
	/**
	 * Fired when the plugin is uninstalled.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function uninstall( $network_wide ) {
		// TODO:	Define uninstall functionality here		
	} // end uninstall

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {
	
		// TODO: replace "plugin-name-locale" with a unique value for your plugin
		$domain = 'plugin-name-locale';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	} // end plugin_textdomain


	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */	
	public function register_admin_scripts() {
	
		// TODO:	Change 'plugin-name' to the name of your plugin
		//wp_enqueue_script( 'plugin-name-admin-script', plugins_url( 'related-links-blender-2/assets/js/admin.js' ) );
	
	} // end register_admin_scripts
	
	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
	
		// TODO:	Change 'plugin-name' to the name of your plugin
		//wp_enqueue_style( 'plugin-name-plugin-styles', plugins_url( 'related-links-blender-2/assets/css/display.css' ) );
	
	} // end register_plugin_styles
	
	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
	
		// TODO:	Change 'plugin-name' to the name of your plugin
		//wp_enqueue_script( 'plugin-name-plugin-script', plugins_url( 'related-links-blender-2/assets/js/display.js' ) );
	
	} // end register_plugin_scripts
	
	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/
	
	/**
 	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *		  WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *		  Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 */
	function action_method_name() {
    	// TODO:	Define your action method here
	} // end action_method_name
	
	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *		  WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *		  Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 */
	function filter_method_name() {
	    // TODO:	Define your filter method here
	} // end filter_method_name





	// validate our options
	protected function rlb_plugin_options_activation_initialize() {
	
		//these defaults are NEVER changed, for copying only
		$default_options = array(
		'rlb_a_test_option'=>'I am just a test field with no function',
		
		'rlb_controls_page'=>false,
		'rlb_controls_post'=>false,
	
		'rlb_wrapper_prefix'=>"<div class=\"rlb_related_links\"><h2>Related Posts:</h2>",
		'rlb_wrapper_suffix'=>'</div>',
	
		'rlb_thumb_width'=>(int)100,
		'rlb_thumb_height'=>(int)100,
	
		'rlb_link_wrapper_prefix'=>"<p class=\"rlb_related_link\">",
		'rlb_link_wrapper_suffix'=>'</p>',
	
		'rlb_styles'=>"
			.rlb_related_links  { margin: 0 0 25px 15px;	}
			.rlb_related_links  h2 { margin-left:0px;	}
			.rlb_related_links p {
					height:52px; 
					margin:10px 0 0 0;
					overflow:hidden;
			}
			.rlb_related_link img {
					height:50px; width:50px;
					float:left;
					margin-right:5px;
					border:1px solid #555;
			}"
		);
	
		//get any existing options
		$options = get_option('rlb_plugin_options');
		
		
		if (!$options) { //options will be false on a fresh install
			$options= $default_options;
			foreach ($options as $key => $value) { //step through the fresh options to sanitize
					if (is_string($options[$key]))  $options[$key]= htmlentities(trim($options[$key]));
			}
		} else { //add any missing options
			foreach ($default_options as $key => $value) {
				if (!array_key_exists($key,$options)) { //in defaults, not in existing so add sanitized
					if (is_string($default_options[$key]))  $options[$key]= htmlentities(trim($default_options[$key]));
					elseif (is_int($default_options[$key]))  $options[$key]=$default_options[$key];
					elseif (is_bool($default_options[$key]))  $options[$key]=$default_options[$key];
					else $options[$key]="init error";
					
				} 
			}
	
		}
		$options['rlb_a_test_option']='another test';	
		update_option( "rlb_plugin_options", $options );
	}







  
} // end class

// TODO:	Update the instantiation call of your plugin to the name given at the class definition


require_once(RelatedLinksBlender_DIR.'includes/core.php');

if(is_admin()) { //load admin files only in admin
	require_once(RelatedLinksBlender_DIR.'includes/admin.php');
	$relatedlinksblender = new RelatedLinksBlender_admin();
} else {
	$relatedlinksblender = new RelatedLinksBlender_core();
}


