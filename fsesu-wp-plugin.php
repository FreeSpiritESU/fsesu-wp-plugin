<?php
/* 
 * Plugin Name:       FreeSpirit ESU Plugin
 * Description:       Custom Plugin designed for use by FreeSpirit ESU only
 * Version:           0.1.0
 * Author:            Richard Perry
 * Author URI:        http://richard.perry-online.me.uk/
 * License:           GPL2+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       fsesu
 * GitHub Theme URI:  richardp2/fsesu-theme
 * GitHub Branch:     develop
 * 
 * 
 *    Copyright 2014  FreeSpirit ESU  (email : richard@freespiritesu.org.uk)
 * 
 *    This program is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License, version 2, as 
 *    published by the Free Software Foundation.
 * 
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *    along with this program; if not, write to the Free Software
 *    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 * @package         Wordpress\Plugins\FreeSpiritESU
 * @subpackage      Classes
 * @copyright       Copyright (c) 2014 FreeSpirit ESU
 * @since           0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    29 August 2014
 */

namespace FSESU;

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Plugin {

    /**
     * Instance of this class.
     *
     * @since   0.1.0
     * @access  protected
     * @var     object
     */
    protected static $instance = null;
    
    /**
     * Text domain of the plugin
     * 
     * @since   0.1.0
     * @access  public
     * @var     string
     */
    protected $domain;
    
    /**
     * Short description. 
     * 
     * @since x.x.x
     * @access (private, protected, or public)
     * @var type $var Description.
     */
    protected $styles = array();
    
    /**
     * Short description. 
     * 
     * @since x.x.x
     * @access (private, protected, or public)
     * @var type $var Description.
     */
    protected $scripts = array();
    
    /**
     * Short description. 
     * 
     * @since x.x.x
     * @access (private, protected, or public)
     * @var type $var Description.
     */
    protected $admin_styles = array();
    
    /**
     * Short description. 
     * 
     * @since x.x.x
     * @access (private, protected, or public)
     * @var type $var Description.
     */
    protected $admin_scripts = array();
    
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
        
        /* Set the constants needed by the plugin. */
        add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );
        
        /* Internationalize the text strings used. */
        add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );
        
        /* Load the functions files. */
        add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );
        
        /* Initialise required features. */
        add_action( 'plugins_loaded', array( $this, 'features' ), 4 );
        
        /* Load the admin files. */
        add_action( 'plugins_loaded', array( $this, 'admin' ), 5 );
        
        /* Enqueue scripts and styles. */
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ), 99 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        
        /* Register activation hook. */
        register_activation_hook( __FILE__, array( $this, 'activation' ) );
        
        /* Register deactivation hook */
        register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
        
        $this->domain = 'fsesu';
        
        $this->set_categories( $this->categories );
    }
	
    /**
     * Defines constants used by the plugin.
     *
     * @since  0.1.0
     * @access public
     * @return void
     */
    public function constants() {

        /* Set constant path to the plugin directory. */
        define( 'FSESU_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

        /* Set the constant path to the plugin directory URI. */
        define( 'FSESU_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
        
        /* Set the constant path to the includes directory. */
	    define( 'FSESU_INC', FSESU_DIR . trailingslashit( 'includes' ) );
	    
	    /* Set the constant path to the custom post type directory. */
	    define( 'FSESU_CPT', FSESU_INC . trailingslashit( 'custom_post_types' ) );
	    
        /* Set the constant path to the admin directory. */
	    define( 'FSESU_ADM', FSESU_DIR . trailingslashit( 'admin' ) );
    }

    /**
     * Loads the initial files needed by the plugin.
     *
     * @since  0.1.0
     * @access public
     * @return void
     */
    public function includes() {
        
        /*require_once( FSESU_DIR . 'inc/post-types.php' );
        require_once( FSESU_DIR . 'inc/taxonomies.php' );
        require_once( FSESU_DIR . 'inc/class-fsesu-and-bells.php' );
        require_once( FSESU_DIR . 'inc/class-fsesu-and-tabs.php' );
        require_once( FSESU_DIR . 'inc/class-fsesu-and-toggles.php' );
        require_once( FSESU_DIR . 'inc/class-fsesu-and-accordions.php' );
        require_once( FSESU_DIR . 'inc/functions.php' ); */
        require_once( FSESU_INC . 'class-fsesu-roles.php' );
        require_once( FSESU_INC . 'class-fsesu-custom-post-type.php' );
        require_once( FSESU_CPT . 'class-fsesu-programme.php' );
        require_once( FSESU_CPT . 'class-fsesu-products.php' );
        require_once( FSESU_CPT . 'class-fsesu-supporters.php' );
        require_once( FSESU_CPT . 'class-fsesu-downloads.php' );
    }
    
    /**
     * Initialises the various features to be used by the plugin
     *
     * @since  0.1.0
     * @access public
     * @return void
     */
    public function features() {
        /* Add in custom role definitions */
        Roles::init();
        
        /* Add the Programme/Event Custom Post Type */
        Programme::init();
        Products::init();
        Supporters::init();
        Downloads::init();
    }
    
    /**
     * Loads admin files.
     *
     * @since  0.1.0
     * @access public
     * @return void
     */
    public function admin() {
        
        if ( is_admin() ) {
            //require_once( FSESU_ADM . 'class-fsesu-admin.php'    );
        
            /* Update the admin menu order and add in additional items */
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_filter( 'custom_menu_order', array( $this, 'admin_menu_order' ) ); 
            add_filter( 'menu_order', array( $this, 'admin_menu_order' ) );
        }
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
     * 
     */
    public function admin_menu_order( $menu_ord ) {
        if (!$menu_ord) return true;
         
        return array(
            'index.php', // Dashboard
            'separator1', // First separator
            'edit.php?post_type=page', // Pages
            'edit.php', // Posts
            'edit.php?post_type=event', // Programme
            'edit.php?post_type=product', // Products
            'edit.php?post_type=supporter', // Unit Supporters
            'upload.php', // Media
            'edit.php?post_type=file', // Downloads
            'users.php', // Users
            'link-manager.php', // Links
            'edit-comments.php', // Comments
            'separator2', // Second separator
            'themes.php', // Appearance
            'plugins.php', // Plugins
            'tools.php', // Tools
            'options-general.php', // Settings
            'separator-last', // Last separator
        );
    }
    
    /**
     * 
     */
    public function get_domain() {
        return $this->domain;
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
     * @since    0.1.0
     * @access   public
     * @return   object    A single instance of this class.
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
        flush_rewrite_rules();
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
    public function i18n() {
        load_plugin_textdomain( 'fsesu', false, FSESU_DIR . '/languages/' );
    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    0.1.0
     */
    public function enqueue_styles()
    {
        foreach( $this->styles as $style ) {
            wp_enqueue_style( 'fsesu-' . $style['slug'], $style['location'], $style['dependencies'], $style['version'] );
        }
    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    0.1.0
     */
    public function enqueue_admin_styles()
    {
        foreach( $this->admin_styles as $style ) {
            wp_enqueue_style( 'fsesu-' . $style['slug'], $style['location'], $style['dependencies'], $style['version'] );
        }
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts()
    {
        foreach( $this->scripts as $script ) {
            wp_enqueue_script( 'fsesu-' . $script['slug'], $script['location'], $script['dependencies'], $script['version'] );
        }
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    0.1.0
     */
    public function enqueue_admin_scripts()
    {
        foreach( $this->admin_scripts as $script ) {
            wp_enqueue_script( 'fsesu-' . $script['slug'], $script['location'], $script['dependencies'], $script['version'] );
        }
    }
    
    /**
	 * Registers the filters with WordPress and the respective objects and
	 * their methods.
	 *
	 * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
	 * @param  object    $component   The object that contains the method to be called when the hook is fired.
	 * @param  string    $callback    The function that resides on the specified component.
	 */
    public function add_style( $slug, $location, $dependencies = array(), $version = null )
    {
        $this->styles[] = array(
            'slug'          => $slug,
            'location'      => $location,
            'dependencies'  => $dependencies,
            'version'       => $version
        );
    }
    
    /**
	 * Registers the filters with WordPress and the respective objects and
	 * their methods.
	 *
	 * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
	 * @param  object    $component   The object that contains the method to be called when the hook is fired.
	 * @param  string    $callback    The function that resides on the specified component.
	 */
    public function add_admin_style( $slug, $location, $dependencies = array(), $version = null )
    {
        $this->admin_styles[] = array(
            'slug'          => $slug,
            'location'      => $location,
            'dependencies'  => $dependencies,
            'version'       => $version
        );
    }
    
    /**
	 * Registers the filters with WordPress and the respective objects and
	 * their methods.
	 *
	 * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
	 * @param  object    $component   The object that contains the method to be called when the hook is fired.
	 * @param  string    $callback    The function that resides on the specified component.
	 */
    public function add_script( $slug, $location, $dependencies = array(), $version = null )
    {
        $this->scripts[] = array(
            'slug'          => $slug,
            'location'      => $location,
            'dependencies'  => $dependencies,
            'version'       => $version
        );
    }
    
    /**
	 * Registers the filters with WordPress and the respective objects and
	 * their methods.
	 *
	 * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
	 * @param  object    $component   The object that contains the method to be called when the hook is fired.
	 * @param  string    $callback    The function that resides on the specified component.
	 */
    public function add_admin_script( $slug, $location, $dependencies = array(), $version = null )
    {
        $this->admin_scripts[] = array(
            'slug'          => $slug,
            'location'      => $location,
            'dependencies'  => $dependencies,
            'version'       => $version
        );
    }

}

$fsesu = Plugin::init();