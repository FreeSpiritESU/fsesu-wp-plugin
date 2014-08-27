<?php
/**
 * This file contains the class that defines the Products Post Type
 * 
 * The Unit produces a certain amount of merchandise for it's members. The Products
 * Custom Post Type was created to separate the products out from the standard
 * posts post type, and simplify the creation of new products/removal of old ones.
 * It was decided that a page would be more difficult to maintain so a new Post
 * Type would be easier.
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
 * Custom Post Type class for handling files.
 * 
 * This class extends the abstract class, Custom_Post_Type, and defines the various
 * elements that are specific to the Products Custom Post Type.
 * 
 * @since   0.1.0
 * 
 * @see     Custom_Post_Type
 */
class Products extends Custom_Post_Type
{
    
    /**
     * Class constructor method.
     * 
     * The class constructor method is fired when the class is instantiated (or
     * cosntructed ;-)). As this class extends the Abstract Class Custom_Post_Type
     * it defines the specific post type, sets the defaults and adds in specific
     * elements unique to the Products post type.
     * 
     * @since   0.1.0
     * @global  object  $fsesu  Instance of the main plugin class.
     * @return  void.
     */
    protected function __construct()
    { 
        global $fsesu;
        
        $this->post_type = 'product';
        $this->post_type_plural = 'Products';
        $this->taxonomy = 'product_type';
        
        $this->set_defaults();
        
        /* Modify some of the default post type arguments */
        $this->arguments['menu_icon'] = 'dashicons-products';
        $this->arguments['rewrite'] = array( 'slug' => "members/merchandise", 'with_front' => false );
        $this->arguments['taxonomies'] = array( 'post_tag' );
        
        /* Modify some of the default taxonomy arguments */
        $this->tax_arguments['rewrite'] = array( 'slug' => "members/merchandise/types", 'with_front' => false );
        
        parent::__construct();
    }
}