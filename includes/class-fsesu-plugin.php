<?php
/**
 * FSESU_Plugin is the core class that instantiates all functionality of the plugin
 *  
 * @package         Wordpress\Plugins\FreeSpiritESU
 * @subpackage      Classes
 * @author          Richard Perry <http://www.perry-online.me.uk/>
 * @copyright       Copyright (c) 2014 FreeSpirit ESU
 * @license         http://www.gnu.org/licenses/gpl-2.0.html
 * @since           0.1.0
 * @version         0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    19 August 2014
 */


class FSESU_Plugin {

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * 
	 */
	protected $categories = array(
		array (
	        'term' => 'News',
	        'args' => 
	            array(
	                'description' => "News about what is happening in our Unit",
	                'slug' => 'news'
	            )
	    ),
	    array (
	        'term' => "What's New",
	        'args' => 
	            array(
	                'description' => "Quick updates about new things on the website, as well as quick notices for the Unit",
	                'slug' => 'whatsnew',
	                'parent' => 'News'
	            )
	    ),
	    array (
	        'term' => 'Camp Diaries',
	        'args' => 
	            array(
	                'description' => "Everytime we participate in a major camp, or jamboree, as a group, we will be keeping everyone informed of how we are getting on through our camp diary. These diaries will be posted here, and pictures will generally be found on our Gallery.",
	                'slug' => 'campdiaries'
	            )
	    )
	);

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.1.0
	 */
	protected function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );
		
		$this->set_features();
		$this->set_categories( $this->categories );
	}
	
	/**
	 * 
	 */
	private function set_features() {
		// Add in custom role definitions
		require_once FSESU_PLUGIN_DIR . 'includes/class-fsesu-roles.php';
		//add_action( 'plugins_loaded', array( 'FSESU_Roles', 'init' ) );
		FSESU_Roles::init();
		
		// Add in custom post type support
		//require_once FSESU_PLUGIN_DIR . 'includes/class-fsesu-custom-post-type.php';
	}
	
	/**
	 * 
	 */
	public function admin_menu() {
		// Add new menu items for News and Camp Diaries
		add_posts_page( 'News Items', 'News Items', 'edit_posts', 'edit.php?category_name=news' );
		add_posts_page( 'Camp Diaries', 'Camp Diaries', 'edit_posts', 'edit.php?category_name=campdiaries' );
		
		// Reorder some of the menu items
		global $submenu;
		$submenu['edit.php'][6] = $submenu['edit.php'][17];
		$submenu['edit.php'][7] = $submenu['edit.php'][18];
		unset( $submenu['edit.php'][17] );
		unset( $submenu['edit.php'][18] );
		ksort( $submenu['edit.php'] );
	}
	
	/**
     * Add the standard categories that will be used by the site.
     * 
     * This function uses the wp_insert_term function to add new categories to 
     * the standard category taxonomy (n.b. it cannot be used to add new terms to 
     * custom taxonomies)
     * 
     * 
     * @param       array   $categories array containing category details.
     * @return      void
     * 
     * @since       3.0.0
     */
    private function set_categories( $categories ) {
        /*
         * Breakdown the categories array into individual category arrays
         * then check there is not already a category by that name and 
         * insert the new category if required
         */
        foreach ( $categories as $category ) {
            if ( !get_cat_ID( $category['term'] ) ) {
            	if ( $category['args']['parent'] ) {
            		$category['args']['parent'] = get_cat_ID( $category['args']['parent'] );
            	}
                wp_insert_term( $category['term'], 'category', $category['args'] ); 
            }
        }
    }

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function init() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    0.1.0
	 */
	public static function activate() {
		
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 */
	public static function deactivate() {
		
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'fsesu', false, FSESU_PLUGIN_DIR . '/languages/' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		//wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		//wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    0.1.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    0.1.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

}