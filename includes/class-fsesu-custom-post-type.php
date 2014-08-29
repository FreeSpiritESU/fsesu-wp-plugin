<?php
/**
 * This file contains the an abstract class that contains the functionality to 
 * create custom post types
 * 
 * Custom Post Type is an abstract base class that needs to be extended to create
 * new custom post types. The class contains all the necessary properties and
 * methods to define new custom post types, with custom taxonomies, custom fields,
 * meta boxes and columns. It is up to each child class to define how these are
 * all put together.
 *  
 * @package         Wordpress\Plugins\FreeSpiritESU
 * @subpackage      Classes
 * @author          Richard Perry <http://www.perry-online.me.uk/>
 * @copyright       Copyright (c) 2014 FreeSpirit ESU
 * @license         http://www.gnu.org/licenses/gpl-2.0.html
 * @since           0.1.0
 * @version         0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    29 August 2014
 */

namespace FSESU;

/**
 * Custom Post Type abstract class for defining new Custom Post Types.
 * 
 * This abstract class defines the main properties and functions required by the
 * child classes to register custom post types that include custom taxonomies,
 * custom fields, meta boxes and custom columns. The default values are also 
 * defined within this class.
 * 
 * @since   0.1.0
 */
abstract class Custom_Post_Type 
{
    /**
     * Instance of this class and subclasses.
     *
     * @since   0.1.0
     * @var     object
     */
    private static $instance = array();
    
    /**
     * The post type designation.
     * 
     * @since 	0.1.0
     * @var		string
     */
    protected $post_type = '';
    
    /**
     * The post type plural designation.
     * 
     * @since 	0.1.0
     * @var		string
     */
    protected $post_type_plural = '';
    
    /**
     * Defines the array for the post type arguments.
     * 
     * @since 	0.1.0
     * @var		array
     */
    protected $arguments = array();
    
    /**
     * Defines the array for the post type labels.
     * 
     * @since 	0.1.0
     * @var		array
     */
    protected $labels = array();
    
    /**
     * Custom taxonomy designation.
     * 
     * @since 	0.1.0
     * @var		array
     */
    protected $taxonomy;
    
    /**
     * Defines the array for the taxonomy arguments.
     * 
     * @since 	0.1.0
     * @var		array
     */
    protected $tax_arguments = array();
    
    /**
     * Defines the array for the taxonomy labels.
     * 
     * @since 	0.1.0
     * @var		array
     */
    protected $tax_labels = array();
    
    /**
     * Defines the array for any custom fields required.
     * 
     * @since   0.1.0
     * @var     array
     */
    protected $fields = array();
    
    /**
     * Defines the context for any custom meta box.
     * 
     * @since   0.1.0
     * @var     string
     */
    protected $meta_context = 'normal';
    
    /**
     * Defines the priority for any custom meta box.
     * 
     * @since   0.1.0
     * @var     string
     */
    protected $meta_priority = 'high';
    
    /**
     * Define the columns to display on the admin list page. 
     * 
     * @since   0.1.0
     * @var     array
     */
    protected $columns = array();
    
    /**
     * The construct method which run on instantiation.
     * 
     * This method runs once the class has been initialised. It needs to be called
     * by all child classes as there are a number of action hooks included here
     * that all custom post types need to register.
     * 
     * @since   0.1.0
     * @return  void
     */
    protected function __construct()
    {
        global $fsesu;
        
        /* Add action to register the post type, if the post type does not already exist */
        if( ! post_type_exists( $this->post_type ) ) {
            add_action( 'init', array( $this, 'register_post_type' ) );
        }
        
        /* If a custom taxonomy has been defined, register it */
        if( $this->taxonomy ) {
            add_action( 'init', array( $this, 'register_taxonomy' ), 0 );
        }
        
        $fsesu->add_admin_style( 'meta-box', FSESU_URI . 'assets/css/meta-box.css' );
    }
    
    /**
     * Set the defaults for the main arguments.
     * 
     * @since 	0.1.0
     * @return	void
     */
    protected function set_defaults()
    {
        global $fsesu;
        
        $name = ucwords( str_replace( '_', ' ', $this->post_type ) );
        $plural = $this->post_type_plural;
        $domain = $fsesu->get_domain();
        
        /* Set up the defaults for the label elements */
        $this->labels = array(
            'name' 				    => _x( $plural, $plural . 'General Name', $domain ),
            'singular_name' 		=> _x( $name, $plural . 'Singular Name', $domain ),
            'menu_name'             => _x( $plural, $plural . 'Menu Name', $domain ),
            'add_new' 			    => _x( 'Add New', $this->post_type, $domain ),
            'add_new_item' 		    => __( 'Add New ' . $name, $domain ),
            'edit_item' 			=> __( 'Edit ' . $name, $domain ),
            'new_item' 			    => __( 'New ' . $name, $domain ),
            'view_item' 			=> __( 'View ' . $name, $domain ),
            'search_items' 		    => __( 'Search ' . $plural, $domain ),
            'not_found' 			=> __( 'No ' . $plural . ' found' , $domain ),
            'not_found_in_trash'	=> __( 'No ' . $plural . ' found in Trash' , $domain )
        );
       
        /* Set up the default post type arguments */
        $this->arguments = array(
            'labels' 				=> $this->labels,
            'description'			=> '',
            'public' 				=> true,
            'exclude_from_search'   => false,
            'publicly_queryable' 	=> true,
            'show_ui' 			    => true,
            'show_in_nav_menus'	    => true,
            'show_in_menu'		    => true,
            'show_in_admin_bar'	    => true,
            'menu_icon' 			=> '',
            'capability_type' 	    => 'post',
            'hierarchical' 		    => false,
            'supports'			    => array( 'title', 'editor', 'author', 'comments', 'revisions' ),
            'register_meta_box_cb'  => '',
            'has_archive'			=> true,
            'rewrite' 			    => array( 'slug' => $name, 'with_front' => false ),
            'can_export' 			=> true
        );
        
        /* Check if a custom taxonomy has been defined */
        if ( $this->taxonomy ) {
            
            /* Set up the default labels for a custom taxonomy */
            $this->tax_labels = array(
                'name'              => _x( $name . ' Types', 'taxonomy general name' ),
                'singular_name'     => _x( $name . ' Type', 'taxonomy singular name' ),
                'search_items'      => __( 'Search ' . $name . ' Types' ),
                'all_items'         => __( 'All ' . $name . ' Types' ),
                'edit_item'         => __( 'Edit ' . $name . ' Type' ), 
                'update_item'       => __( 'Update ' . $name . ' Type' ),
                'add_new_item'      => __( 'Add New ' . $name . ' Type' ),
                'new_item_name'     => __( 'New ' . $name . ' Type' ),
                'menu_name'         => __(  $name . ' Types' ),
            );
            
            $this->tax_arguments = array(
                'labels'            => $this->tax_labels,
                'public'            => true,
                'show_ui'           => true,
                'meta_box_cb'       => null,
                'show_admin_column' => false,
                'hierarchical'      => false,
                'rewrite'           => array( 'slug' => $this->taxonomy, 'with_front' => false, 'hierarchical' => true )
            );
        }
    }
    
    /**
     * Method to register the post type.
     * 
     * @since 	0.1.0
     * @return	void
     */
    public function register_post_type() {
        /* Register the post type */
        register_post_type( $this->post_type, $this->arguments );
    }
    
    /**
     * Method to register a custom taxonomy for the post type.
     * 
     * @since 	0.1.0
     * @return	void
     */
    public function register_taxonomy() {
        /* Register the new taxonomy */
        register_taxonomy( $this->taxonomy, $this->post_type, $this->tax_arguments );
    }
    
    /**
     * This method defines
     *
     * @since   0.1.0
     * @global  object  $fsesu      Instance of the main plugin class.
     * @param   string  $post_type  The post type of the calling post.
     * @return  void    
     */
    public function add_meta_box( $post_type )
    {
        global $fsesu;
        
        add_meta_box( 
            $this->post_type . '_meta', 
            __( ucwords( $this->post_type ) . ' Details', $fsesu->get_domain() ), 
            array( $this, 'render_meta_box' ),
            $this->post_type, 
            $this->meta_context, 
            $this->meta_priority 
        );
    }
    
    /**
     * A method to render the custom fields within their own custom meta box.
     *
     * @since   0.1.0
     * @param   array   $post   The WordPress post object.
     * @return  void    
     */
    public function render_meta_box( $post )
    {
        /* Add an nonce field so we can check for it later */
        wp_nonce_field( $this->post_type . '_meta', $this->post_type . '_meta_nonce' );
        
        $meta = get_post_meta( $post->ID );
        
        echo '<div class="' . $this->post_type . '_custom_meta_box">';
        foreach( $this->fields as $group ) {
            
            echo '<div class="custom_meta_box_group">';
            foreach( $group as $field ) {
                extract ( $field );
                
                $default = isset( $default ) ? $default : '';
                $value = isset( $meta[$id][0] ) ? $meta[$id][0] : $default;
                
                echo '<span class="' . $this->post_type . '_meta_' . $id . ' custom_meta_data">';
                echo '<label for="' . $id . '">' . $label . '</label>';
                
                switch ( $type ) {
                    case 'datetime':
                    case 'date':
                        $value = date( 'd M Y', $value );
                        echo '<input type="' . $type . '" id="' . $id . '" name="' . $id . '" value="' . $value . '">';
                        break;
                    case 'textarea':
                        echo '<textarea id="' . $id . '" name="' . $id . '">' . $value . '</textarea>';
                        break;
                    default:
                        $value = ( $value ) ? ' value="' . $value . '"' : '';
                        echo '<input type="' . $type . '" id="' . $id . '" name="' . $id . '"' . $value . '>';
                }
                
                echo '</span>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
    
    /**
     * This method saves the custom fields to the database.
     *
     * @since   0.1.0
     * @param   int     $post_id    The id of the post that is being saved.
     */
    public function save_post_type( $post_id )
    {
        
        /* Check if the nonce is set. */
        if ( ! isset( $_POST[$this->post_type . '_meta_nonce'] ) ) {
            return;
        }
    
        /* Verify that the nonce is valid. */
        if ( ! wp_verify_nonce( $_POST[$this->post_type . '_meta_nonce'], $this->post_type . '_meta' ) ) {
            return;
        }
    
        /** If this is an autosave, our form has not been submitted, so we don't want to do anything. */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
    
        /* Check the user's permissions. */
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        foreach ( $this->fields as $group ) {
            foreach( $group as $field ) {
                extract ( $field );
                
                if ( ! isset( $_POST[ $id ] ) ) return $post_id;
                
                switch ( $type ) {
                    case 'date':
                        update_post_meta( $post_id, $id, strtotime( $_POST[$id] ) );
                        break;
                    case 'text':
                        update_post_meta( $post_id, $id, sanitize_text_field( $_POST[ $id ] ) );
                        break;
                    default:
                        update_post_meta( $post_id, $id, $_POST[ $id ] );
                }
            }
        }
    }
    
    /**
     * Define the columns to be displayed on the All Items page.
     * 
     * @since   0.1.0
     * @param   array   $columns    The default columns displayed by WP_List_Table.
     * @return  array   $columns    The columns to the displayed for this post type.
     */
    public function add_columns( $columns )
    {
        $columns = $this->columns;
        
        return $columns;
    }
    
    
    
    
    
    /**
     * Return an instance of this class.
     *
     * @since    0.1.0
     * @return   object    A single instance of this class.
     */
    public static function init()
    {
        $c = get_called_class();
        if ( !isset( self::$instance[$c] ) ) {
            self::$instance[$c] = new $c();
            self::$instance[$c]->init();
        }

        return self::$instance[$c];
    }
}