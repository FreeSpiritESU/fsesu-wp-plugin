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
 * @lastmodified    21 August 2014
 */

namespace FSESU;

class Products extends Custom_Post_Type {
    
    protected function __construct() {
        
        global $fsesu;
        
        $this->post_type = 'product';
        $this->post_type_plural = 'Products';
        
        $this->set_defaults();
        
        /* Modify some of the default post type arguments */
        $this->arguments['menu_icon'] = 'dashicons-products';
        $this->arguments['rewrite'] = array( 'slug' => "members/merchandise", 'with_front' => false );
        //$this->arguments['taxonomies'] = array( '', '' );
        
        parent::__construct();
    }
}