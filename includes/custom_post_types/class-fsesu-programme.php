<?php
/**
 * This class defines the Events Post Type (events are grouped together into the
 * programme for FreeSpirit ESU)
 *  
 * @package         Wordpress\Plugins\FreeSpiritESU
 * @subpackage      Classes
 * @author          Richard Perry <http://www.perry-online.me.uk/>
 * @copyright       Copyright (c) 2014 FreeSpirit ESU
 * @license         http://www.gnu.org/licenses/gpl-2.0.html
 * @since           0.1.0
 * @version         0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    21 August 2014
 */

namespace FSESU;

class Programme extends Custom_Post_Type {
    
    protected function __construct() {
        
        global $fsesu;
        
        $this->post_type = 'event';
        $this->post_type_plural = 'Events';
        
        $this->set_defaults();
        
        /* Modify some of the default label elements */
        $this->labels['menu_name'] = _x( 'Programme', 'Programme Menu Name', $fsesu->get_domain() );
        $this->labels['search_items'] = __( 'Search Programme', $fsesu->get_domain() );
        
        /* Redefine the labels argument since we made some changes */
        $this->arguments['labels'] = $this->labels;
        
        /* Modify some of the default post type arguments */
        $this->arguments['menu_icon'] = 'dashicons-calendar';
        $this->arguments['rewrite'] = array( 'slug' => "unitinfo/programme", 'with_front' => false );
        //$this->arguments['taxonomies'] = array( '', '' );
        
        parent::__construct();
    }
}