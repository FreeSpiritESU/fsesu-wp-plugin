<?php
/**
 * Custom Post Type is an abstract base class that needs to be extended to create
 * new custom post types
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

abstract class Custom_Post_Type {
    
    /**
     * Instance of this class and subclasses.
     *
     * @since   0.1.0
     * @access  private
     * @var     object
     */
    private static $instance = array();
    
    /**
     * The post type designation
     * 
     * @since 	0.1.0
     * @access	protected
     * @var		string
     */
    protected $post_type = '';
    
    /**
     * The post type plural designation
     * 
     * @since 	0.1.0
     * @access	protected
     * @var		string
     */
    protected $post_type_plural = '';
    
    /**
     * Defines the array for the post type arguments
     * 
     * @since 	0.1.0
     * @access	protected
     * @var		array
     */
    protected $arguments = array();
    
    /**
     * Defines the array for the post type labels
     * 
     * @since 	0.1.0
     * @access	protected
     * @var		array
     */
    protected $labels = array();
    
    /**
     * Custom taxonomy designation
     * 
     * @since 	0.1.0
     * @access	protected
     * @var		array
     */
    protected $taxonomy;
    
    /**
     * Defines the array for the taxonomy arguments
     * 
     * @since 	0.1.0
     * @access	protected
     * @var		array
     */
    protected $tax_arguments = array();
    
    /**
     * Defines the array for the taxonomy labels
     * 
     * @since 	0.1.0
     * @access	protected
     * @var		array
     */
    protected $tax_labels = array();

    protected function __construct() {
        /* Add action to register the post type, if the post type does not already exist */
        if( ! post_type_exists( $this->post_type ) ) {
            add_action( 'init', array( $this, 'register_post_type' ) );
        }
        
        /* If a custom taxonomy has been defined, register it */
        if( $this->taxonomy ) {
            add_action( 'init', array( $this, 'register_taxonomy' ), 0 );
        }
    }
    
    /**
     * Set the defaults for the main arguments
     * 
     * @since 	0.1.0
     * @access	protected
     * @return	void
     */
    protected function set_defaults() {
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
     * Method to register the post type 
     * 
     * @since 	0.1.0
     * @access 	public
     * @return	void
     */
    public function register_post_type() {
        /* Register the post type */
        register_post_type( $this->post_type, $this->arguments );
    }
    
    /**
     * Method to register a custom taxonomy for the post type 
     * 
     * @since 	0.1.0
     * @access 	public
     * @return	void
     */
    public function register_taxonomy() {
        /* Register the post type */
        register_taxonomy( $this->taxonomy, $this->post_type, $this->tax_arguments );
    }
    
    /**
     * Return an instance of this class.
     *
     * @since    0.1.0
     * @access   public
     * @return   object    A single instance of this class.
     */
    public static function init() {
        $c = get_called_class();
        if ( !isset( self::$instance[$c] ) ) {
            self::$instance[$c] = new $c();
            self::$instance[$c]->init();
        }

        return self::$instance[$c];
    }
}

/*class Custom_Post_Type {
    public $post_type_name;
    public $post_type_args;
    public $post_type_labels;
    
    /* Class constructor 
    public function __construct( $name, $args = array(), $labels = array() )
    {
        // Set some important variables
        $this->post_type_name		= strtolower( str_replace( ' ', '_', $name ) );
        $this->post_type_args 		= $args;
        $this->post_type_labels 	= $labels;
        
        // Add action to register the post type, if the post type does not already exist
        if( ! post_type_exists( $this->post_type_name ) )
        {
            add_action( 'init', array( &$this, 'register_post_type' ) );
        }
        
        // Listen for the save post hook
        $this->save();
    }
    
    
    
    /* Method to attach the taxonomy to the post type 
    public function add_taxonomy( $name, $args = array(), $labels = array() )
    {
        if( ! empty( $name ) )
        {
            // We need to know the post type name, so the new taxonomy can be attached to it.
            $post_type_name = $this->post_type_name;

            // Taxonomy properties
            $taxonomy_name		= strtolower( str_replace( ' ', '_', $name ) );
            $taxonomy_labels	= $labels;
            $taxonomy_args		= $args;

            if( ! taxonomy_exists( $taxonomy_name ) )
            {
                /* Create taxonomy and attach it to the object type (post type) 
            }
            else
            {
                /* The taxonomy already exists. We are going to attach the existing taxonomy to the object type (post type) 
            }
            
            //Capitilize the words and make it plural
            $name 		= ucwords( str_replace( '_', ' ', $name ) );
            $plural 	= $name . 's';
            
            // Default labels, overwrite them with the given labels.
            $labels = array_merge(
            
                // Default
                array(
                    'name' 					=> _x( $plural, 'taxonomy general name' ),
                    'singular_name' 		=> _x( $name, 'taxonomy singular name' ),
                    'search_items' 			=> __( 'Search ' . $plural ),
                    'all_items' 			=> __( 'All ' . $plural ),
                    'parent_item' 			=> __( 'Parent ' . $name ),
                    'parent_item_colon' 	=> __( 'Parent ' . $name . ':' ),
                    'edit_item' 			=> __( 'Edit ' . $name ),
                    'update_item' 			=> __( 'Update ' . $name ),
                    'add_new_item' 			=> __( 'Add New ' . $name ),
                    'new_item_name' 		=> __( 'New ' . $name . ' Name' ),
                    'menu_name' 			=> __( $name ),
                ),
        
                // Given labels
                $taxonomy_labels
        
            );
        
            // Default arguments, overwritten with the given arguments
            $args = array_merge(
        
                // Default
                array(
                    'label'					=> $plural,
                    'labels'				=> $labels,
                    'public' 				=> true,
                    'show_ui' 				=> true,
                    'show_in_nav_menus' 	=> true,
                    '_builtin' 				=> false,
                ),
        
                // Given
                $taxonomy_args
        
            );
            
            // Add the taxonomy to the post type
            add_action( 'init',
                function() use( $taxonomy_name, $post_type_name, $args )
                {
                    register_taxonomy( $taxonomy_name, $post_type_name, $args );
                }
            );
            
            add_action( 'init',
                function() use( $taxonomy_name, $post_type_name )
                {
                    register_taxonomy_for_object_type( $taxonomy_name, $post_type_name );
                }
            );
        }
    }
    
    /* Attaches meta boxes to the post type 
        public function add_meta_box( $title, $fields = array(), $context = 'normal', $priority = 'default' )
    {
        if( ! empty( $title ) )
        {
            // We need to know the Post Type name again
            $post_type_name = $this->post_type_name;

            // Meta variables
            $box_id 		= strtolower( str_replace( ' ', '_', $title ) );
            $box_title		= ucwords( str_replace( '_', ' ', $title ) );
            $box_context	= $context;
            $box_priority	= $priority;
            
            // Make the fields global
            global $custom_fields;
            $custom_fields[$title] = $fields;
            
            add_action( 'admin_init',
                function() use( $box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields )
                {
                    add_meta_box(
                        $box_id,
                        $box_title,
                        function( $post, $data )
                        {
                            global $post;
                            
                            // Nonce field for some validation
                            wp_nonce_field( plugin_basename( __FILE__ ), 'custom_post_type' );
                            
                            // Get all inputs from $data
                            $custom_fields = $data['args'][0];
                            
                            // Get the saved values
                            $meta = get_post_custom( $post->ID );
                            
                            // Check the array and loop through it
                            if( ! empty( $custom_fields ) )
                            {
                                /* Loop through $custom_fields 
                                foreach( $custom_fields as $label => $type )
                                {
                                    $field_id_name 	= strtolower( str_replace( ' ', '_', $data['id'] ) ) . '_' . strtolower( str_replace( ' ', '_', $label ) );
                                    
                                    echo '<label for="' . $field_id_name . '">' . $label . '</label><input type="text" name="custom_meta[' . $field_id_name . ']" id="' . $field_id_name . '" value="' . $meta[$field_id_name][0] . '" />';
                                }
                            }
                        
                        },
                        $post_type_name,
                        $box_context,
                        $box_priority,
                        array( $fields )
                    );
                }
            );
        }
        
    }
    
    /* Listens for when the post type being saved 
    public function save()
    {
        // Need the post type name again
        $post_type_name = $this->post_type_name;
    
        add_action( 'save_post',
            function() use( $post_type_name )
            {
                // Deny the WordPress autosave function
                if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

                if ( ! wp_verify_nonce( $_POST['custom_post_type'], plugin_basename(__FILE__) ) ) return;
            
                global $post;
                
                if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $post_type_name )
                {
                    global $custom_fields;
                    
                    // Loop through each meta box
                    foreach( $custom_fields as $title => $fields )
                    {
                        // Loop through all fields
                        foreach( $fields as $label => $type )
                        {
                            $field_id_name 	= strtolower( str_replace( ' ', '_', $title ) ) . '_' . strtolower( str_replace( ' ', '_', $label ) );
                            
                            update_post_meta( $post->ID, $field_id_name, $_POST['custom_meta'][$field_id_name] );
                        }
                    
                    }
                }
            }
        );
    }
    
    public static function beautify( $string )
    {
        return ucwords( str_replace( '_', ' ', $string ) );
    }
    
    public static function uglify( $string )
    {
        return strtolower( str_replace( ' ', '_', $string ) );
    }
    
    public static function pluralize( $string )
    {
        $last = $string[strlen( $string ) - 1];
        
        if( $last == 'y' )
        {
            $cut = substr( $string, 0, -1 );
            //convert y to ies
            $plural = $cut . 'ies';
        }
        else
        {
            // just attach an s
            $plural = $string . 's';
        }
        
        return $plural;
    }

}





    $book = new Custom_Post_Type( 'Book' );
    $book->add_taxonomy( 'category' );
    $book->add_taxonomy( 'author' );
    
    $book->add_meta_box( 
        'Book Info', 
        array(
            'Year' => 'text',
            'Genre' => 'text'
        )
    );
    
    $book->add_meta_box( 
        'Author Info', 
        array(
            'Name' => 'text',
            'Nationality' => 'text',
            'Birthday' => 'text'
        )
    ); */