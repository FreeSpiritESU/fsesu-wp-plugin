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
 * @lastmodified    26 August 2014
 */

namespace FSESU;

class Programme extends Custom_Post_Type {
    
    protected function __construct() {
        
        global $fsesu;
        
        $this->post_type = 'event';
        $this->post_type_plural = 'Events';
        $this->taxonomy = 'programme_type';
        
        $this->set_defaults();
        
        /* Modify some of the default label elements */
        $this->labels['menu_name'] = _x( 'Programme', 'Programme Menu Name', $fsesu->get_domain() );
        $this->labels['search_items'] = __( 'Search Programme', $fsesu->get_domain() );
        
        /* Redefine the labels argument since we made some changes */
        $this->arguments['labels'] = $this->labels;
        
        /* Modify some of the default post type arguments */
        $this->arguments['menu_icon'] = 'dashicons-calendar';
        $this->arguments['rewrite'] = array( 'slug' => "unitinfo/programme", 'with_front' => false );
        $this->arguments['taxonomies'] = array( 'post_tag' );
        
        /* Modify some of the default taxonomy arguments */
        $this->tax_arguments['rewrite'] = array( 'slug' => "unitinfo/programme/category", 'with_front' => false );
        $this->tax_arguments['hierarchical'] = true;
        
        /* Define the custom fields needed by the Events custom post type */
        $this->fields = array(
            array(
                'label'         => 'Start Date',
                'id'            => 'start_date',
                'type'          => 'date',
                'description'   => 'The date the event starts on',
                'default'       => date( 'Y-m-d', strtotime( 'Monday' ) )
            ),
            array(
                'label'         => 'Start Time',
                'id'            => 'start_time',
                'type'          => 'time',
                'description'   => 'The time the event starts',
                'default'       => '19:00'
            ),
            array(
                'label'         => 'End Date',
                'id'            => 'end_date',
                'type'          => 'date',
                'description'   => 'The date the event ends on',
                'default'       => date( 'Y-m-d', strtotime( 'Monday' ) )
            ),
            array(
                'label'         => 'End Time',
                'id'            => 'end_time',
                'type'          => 'time',
                'description'   => 'The time the event ends',
                'default'       => '21:00'
            ),
            array(
                'label'         => 'Location',
                'id'            => 'location',
                'type'          => 'text',
                'description'   => 'Where the event takes place',
                'default'       => 'The Hut'
            ),
            array(
                'label'         => 'Cost',
                'id'            => 'cost',
                'type'          => 'number',
                'description'   => 'The cost of the event (if any)',
                'default'       => 0
            ),
            array(
                'label'         => 'Link',
                'id'            => 'link',
                'type'          => 'url',
                'description'   => 'Link to the event details',
                'default'       => ''
            )
        );
        
        /* Generate any custom meta boxes */
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		
		/* Check and save the post data */
        add_action( 'save_post', array( $this, 'save_post_type' ) );
        
        parent::__construct();
    }
}