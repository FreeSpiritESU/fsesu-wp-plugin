<?php
/**
 * This class defines the File Post Type
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

class Downloads extends Custom_Post_Type {
    
    protected function __construct() {
        
        global $fsesu;
        
        $this->post_type = 'file';
        $this->post_type_plural = 'Files';
        $this->taxonomy = 'download_cetegory';
        
        $this->set_defaults();
        
        /* Modify some of the default label elements */
        $this->labels['menu_name'] = _x( 'Downloads', 'Downloads Menu Name', $fsesu->get_domain() );
        
        /* Redefine the labels argument since we made some changes */
        $this->arguments['labels'] = $this->labels;
        
        /* Modify some of the default post type arguments */
        $this->arguments['menu_icon'] = 'dashicons-download';
        $this->arguments['rewrite'] = array( 'slug' => "downloads", 'with_front' => false );
        $this->arguments['taxonomies'] = array( 'post_tag' );
        
        /* Modify the default taxonomy labels */
        $this->tax_labels = array(
            'name'              => _x( 'File Categories', 'taxonomy general name' ),
            'singular_name'     => _x( 'File Category', 'taxonomy singular name' ),
            'search_items'      => __( 'Search File Categories' ),
            'all_items'         => __( 'All File Categories' ),
            'edit_item'         => __( 'Edit File Category' ), 
            'update_item'       => __( 'Update File Category' ),
            'add_new_item'      => __( 'Add New File Category' ),
            'new_item_name'     => __( 'New File Category' ),
            'menu_name'         => __( 'File Categories' ),
        );
        
        /* Redefine the texonomy labels argument since we made some changes */
        $this->tax_arguments['labels'] = $this->tax_labels;
        
        /* Modify some of the default taxonomy arguments */
        $this->tax_arguments['rewrite'] = array( 'slug' => "downloads/category", 'with_front' => false );
        
        parent::__construct();
    }
}