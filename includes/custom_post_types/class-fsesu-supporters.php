<?php
/**
 * This class defines the Products Post Type
 *  
 * @package         Wordpress\Plugins\FreeSpiritESU
 * @subpackage      Classes
 * @author          Richard Perry <http://www.perry-online.me.uk/>
 * @copyright       Copyright (c) 2014 FreeSpirit ESU
 * @license         http://www.gnu.org/licenses/gpl-2.0.html
 * @since           0.1.0
 * @version         0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    22 August 2014
 */

namespace FSESU;

class Supporters extends Custom_Post_Type {
    
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