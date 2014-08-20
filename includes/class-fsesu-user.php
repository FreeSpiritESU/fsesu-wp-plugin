<?php
/**
 * FSESU_User defines the custom user functionality to be used on the FreeSpirit
 * website
 *  
 * @package         Wordpress\Plugins\FreeSpiritESU
 * @subpackage      Classes
 * @author          Richard Perry <http://www.perry-online.me.uk/>
 * @copyright       Copyright (c) 2014 FreeSpirit ESU
 * @license         http://www.gnu.org/licenses/gpl-3.0.html
 * @since           0.1.0
 * @version         0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    20 August 2014
 */
 
class FSESU_User {
    
    /**
     * Instance of this class.
     *
     * @since    0.1.0
     *
     * @var      object
     */
    protected static $instance = null;
    
    /**
     * The additional fields required.
     *
     * @since    0.1.0
     *
     * @var      array
     */
    protected $fields;

    /**
     * Initialize the plugin
     *
     * @since     0.1.0
     */
    protected function __construct() {
        // Add the additional user fields to the profile
        add_action( 'show_user_profile', array( $this, 'show_user_fields' ) );
        add_action( 'edit_user_profile', array( $this, 'show_user_fields' ) );
        
        // Save the additional user fields on update
        add_action( 'personal_options_update', array( $this, 'user_profile_update' ) );
        add_action( 'edit_user_profile_update', array( $this, 'user_profile_update' ) );
        
        // Setup custom action for display on the site
        add_action( 'fsesu_user_profile', array( $this, 'show_user_profile' ), 10, 1 );
		
    }

    /**
     * Return an instance of this class.
     *
     * @since     0.1.0
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
     * 
     */
    public function user_profile_update( $user_id ) {
        
    }
    
    /**
     * 
     */
    public function show_user_fields( $user ) {
        
    }
    
    /**
     * 
     */
    public function show_user_profile( $user ) {
        
    }
    
    /**
     * 
     */
    protected function add_field( $name, $display, $type, $options ) {
        
    }
    
    /**
     * 
     */
    protected function set_fields() {
        
    }
    
    /**
     * 
     */
    protected function get_fields() {
        
    }
    
}