<?php
/**
 * FSESU_Roles defines the custom role definitions to be used on the FreeSpirit
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
 * @lastmodified    19 August 2014
 */
 
class FSESU_Roles {
    
    /**
     * Instance of this class.
     *
     * @since    0.1.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin
     *
     * @since     0.1.0
     */
    protected function __construct() {
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
        $this->define_roles();
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
	public function admin_menu() {
	    
	}
    
    /**
     * 
     */
    private function define_roles() {
        // Define arrays of additional capabilities for the roles to be added
        $assistant_leader = array(
                'create_users',
                'edit_users',
                'list_users',
                'promote_users',
            );
        $leader = array_merge( $assistant_leader, array( 
                    'activate_plugins',
                    'edit_files',
                    'edit_theme_options',
                    'edit_themes',
                    'export',
                    'import',
                    'install_plugins',
                    'update_core',
                    'update_plugins',
                    'update_themes'
                )
            );
        
        // Define the new roles
        $this->add_role( 'leader', 'Unit Leader', 'editor' );
        $this->add_role( 'assistant-leader', 'Assistant Unit Leader', 'editor' );
        $this->add_role( 'committee', 'Unit Committee Member', 'author' );
        $this->add_role( 'explorer', 'Explorer', 'contributor' );
        $this->add_role( 'parent', 'Parent', 'subscriber' );
        $this->add_role( 'ex-member', 'Past Member', 'subscriber' );
        
        // Link the additional capabilties to the new roles
        $this->add_capabilities( 'leader', $leader );
        $this->add_capabilities( 'assistant-leader', $assistant_leader );
    }
    
    /**
     * Add a new role with the capabilities based on a default role
     */
    private function add_role( $role, $display_name, $template ) {
        if ( ! get_role( $role ) ) {
            $capabilities = get_role( $template )->capabilities;
            
            add_role( $role, $display_name, $capabilities );
        }
    }
    
    /**
     * Add new capabilities to a role
     */
    private function add_capabilities( $role, $capabilities ) {
        if ( get_role( $role ) ) {
            $role = get_role( $role );
            
            if ( ! is_array( $capabilities ) ) {
                $role->add_cap( $capabilities );
            } else {
                foreach ( $capabilities as $capability ) {
                    if ( ! $role->has_cap( $capability ) ) {
                        $role->add_cap( $capability );
                    }
                }
            }
        }
    }
    
}