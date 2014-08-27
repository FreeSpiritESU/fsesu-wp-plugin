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
        $this->arguments['supports'] = array( 'title', 'thumbnail', 'revisions' );
        $this->arguments['taxonomies'] = array( 'post_tag' );
        
        /* Modify some of the default taxonomy arguments */
        $this->tax_arguments['rewrite'] = array( 'slug' => "members/merchandise/types", 'with_front' => false );
        
        /**
         *  Create Product Post Type Custom Meta Boxes for Edit Page
         *
         *  Generates the meta boxes for the custom information on the edit pages of the
         *  custom prodcut post type.
         */
        $this->set_fields();
        $this->meta_context = 'side';
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) ); 
        add_action( 'save_post', array( $this, 'save_post_type' ) );
        
        /**
         *  Create Event Post Type Columns
         *
         *  Generates the columns for display on the main summary page of the
         *  custom event post type.
         */
        $this->set_columns();
        add_filter( 'manage_edit-product_columns', array( $this, 'add_columns' ) );
        add_action( 'manage_product_posts_custom_column', array( $this, 'render_columns' ), 2, 1);
        
        /**
         * Call the parent constructor method to finish registering the various
         * elements of the custom post type.
         */
        parent::__construct();
    }
    
    /**
     * Define the custom fields to be used by the Products post type.
     * 
     * Long description.
     * 
     * @since   0.1.0
     * @return  void    Nothing returned, only sets up the $fields array
     */
    private function set_fields()
    {
        /* Define the custom fields needed by the Events custom post type */
        $this->fields = array(
            array(
                array(
                    'label'         => 'Cost',
                    'id'            => 'cost',
                    'type'          => 'number',
                    'description'   => 'The cost of the product',
                    'default'       => 0
                )
            )
        );
    }
    
    /**
     * Define the custom columns to be used by the Event post type.
     * 
     * Long description.
     * 
     * @since   0.1.0
     * @global  object  $fsesu  Instance of the main plugin class. 
     * @return  void    Nothing returned, only sets up the $fields array
     */
    private function set_columns()
    {
        global $fsesu;
        
        $this->columns = array(
            'cb'            => '<input type="checkbox" />',
            'thumbnail'     => '',
            'title'         => __( 'Product', $fsesu->get_domain() ),
            'cost'          => __( 'Cost', $fsesu->get_domain() ),
            'type'          => __( 'Type', $fsesu->get_domain() ),
        );
    }
    
    /**
     * Render the column data for the custom columns.
     * 
     * @since   0.1.0
     * @global  object  $fsesu      Instance of the main plugin class.
     * @global  array   $post       The WordPress post object.
     * @param   string  $column     Column Key Name
     * @return  void
     */
    public function render_columns( $column )
    {
        global $fsesu, $post;
        
        $meta = get_post_meta( $post->ID );
        
        switch( $column ) {
            case 'thumbnail':
                echo the_post_thumbnail( array( 75, 75 ) );
                break;
            case 'cost':
                // Get the cost for the event
                if ( is_numeric( $meta['cost'][0] ) ) {
                    echo '&pound;' . $meta['cost'][0];
                } else {
                    echo '';
                }
                break;
            case 'type':
                // Get the list of categories
                $product_types = get_the_terms( $post->ID, 'programme_type' );
                $product_types_html = array();
                if ( $product_types ) {
                    foreach ( $product_types as $product_type ) {
                        $product_types_html[] = sprintf( '<a href="%s">%s</a>',
                            esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'programme_type' => $product_type->slug ), 'edit.php' ) ),
                            esc_html( sanitize_term_field( 'name', $product_type->name, $product_type->term_id, 'programme_type', 'display' ) ) );
                    }
                    echo join( ', ', $product_types_html );
                } else {
                    _e( 'None', $fsesu->get_domain() );
                }
                break;
        }
    }
}