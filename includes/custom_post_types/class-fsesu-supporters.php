<?php
/**
 * This class defines the Supporters Post Type
 * 
 * People who provide support to the Unit need to be recognised and as such, it
 * was decided that there should be a section listing supporters on the website.
 * To make life easier, rather than adding Unit Supporters in as a type of user,
 * we decided to make a custom post type to represent them.
 *  
 * @package         Wordpress\Plugins\FreeSpiritESU
 * @subpackage      Classes
 * @author          Richard Perry <http://www.perry-online.me.uk/>
 * @copyright       Copyright (c) 2014 FreeSpirit ESU
 * @license         http://www.gnu.org/licenses/gpl-2.0.html
 * @since           0.1.0
 * @version         0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    27 August 2014
 */

namespace FSESU;

/**
 * Custom post type class for adding in Unit Supporters.
 * 
 * This class extends the abstract class, Custom_Post_Type, and defines the various
 * elements that are specific to the Supporters Custom Post Type.
 * 
 * @since   0.1.0
 * 
 * @see     Custom_Post_Type
 */ 
class Supporters extends Custom_Post_Type {
    
    /**
     * Class constructor method.
     * 
     * The class constructor method is fired when the class is instantiated (or
     * cosntructed ;-)). As this class extends the Abstract Class Custom_Post_Type
     * it defines the specific post type, sets the defaults and adds in specific
     * elements unique to the Supporters post type.
     * 
     * @since   0.1.0
     * @global  object  $fsesu  Instance of the main plugin class.
     * @return  void.
     */
    protected function __construct() {
        
        global $fsesu;
        
        $this->post_type = 'supporter';
        $this->post_type_plural = 'Unit Supporters';
        
        $this->set_defaults();
        
        /* Modify some of the default post type arguments */
        $this->arguments['menu_icon'] = 'dashicons-awards';
        $this->arguments['rewrite'] = array( 'slug' => "unitinfo/supporters", 'with_front' => false );
        $this->arguments['has_archive'] = false;
        
        parent::__construct();
    }
}