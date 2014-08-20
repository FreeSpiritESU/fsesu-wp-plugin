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
 * @lastmodified    20 August 2014
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
		// Add new menu items for News and Camp Diaries
		add_users_page( 'Leaders', 'Leaders', 'list_users', 'users.php?role=leader' );
		add_users_page( 'Explorers', 'Explorers', 'list_users', 'users.php?role=explorer' );
		add_users_page( 'Parents', 'Parents', 'list_users', 'users.php?role=parents' );
		add_users_page( 'Past Members', 'Past Members', 'list_users', 'users.php?role=ex-member' );
		
		// Reorder some of the menu items
		global $submenu;
		$submenu['users.php'][6] = $submenu['users.php'][16];
		$submenu['users.php'][7] = $submenu['users.php'][17];
		$submenu['users.php'][8] = $submenu['users.php'][18];
		$submenu['users.php'][9] = $submenu['users.php'][19];
		$submenu['users.php'][20] = $submenu['users.php'][15];
		$submenu['users.php'][15] = $submenu['users.php'][10];
		unset( $submenu['users.php'][10] );
		unset( $submenu['users.php'][16] );
		unset( $submenu['users.php'][17] );
		unset( $submenu['users.php'][18] );
		unset( $submenu['users.php'][19] );
		ksort( $submenu['users.php'] );
	}
    
    /**
     * 
     */
    private function define_roles() {
        // Define arrays of additional capabilities for the roles to be added
        $leader = array(
                'activate_plugins',
                'create_users',
                'edit_files',
                'edit_theme_options',
                'edit_themes',
                'edit_users',
                'export',
                'import',
                'install_plugins',
                'list_users',
                'promote_users',
                'update_core',
                'update_plugins',
                'update_themes'
            );
        
        // Define the new roles
        $this->add_role( 'leader', 'Leader', 'editor' );
        $this->add_role( 'explorer', 'Explorer', 'contributor' );
        $this->add_role( 'parent', 'Parent', 'subscriber' );
        $this->add_role( 'ex-member', 'Past Member', 'subscriber' );
       
        // Link the additional capabilties to the new roles
        $this->add_capabilities( 'leader', $leader );
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