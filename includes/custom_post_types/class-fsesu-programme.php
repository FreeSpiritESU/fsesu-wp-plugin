<?php
/**
 * This file contains the class that defines the Events Post Type 
 * 
 * The Unit meets weekly for various activities at various locations at various
 * times. Each meeting is an event that is collated together to produce a 
 * programme for each term. The class for the event post type is therefore called
 * Programme as each event is part of the programme.
 *  
 * @package         Wordpress\Plugins\FreeSpiritESU
 * @subpackage      Classes
 * @author          Richard Perry <http://www.perry-online.me.uk/>
 * @copyright       Copyright (c) 2014 FreeSpirit ESU
 * @license         http://www.gnu.org/licenses/gpl-2.0.html
 * @since           0.1.0
 * @version         0.1.0
 * @modifiedby      Richard Perry <richard@freespiritesu.org.uk>
 * @lastmodified    03 September 2014
 */

namespace FSESU;

/**
 * Custom Post Type class for managing the Unit Programme.
 * 
 * This class extends the abstract class, Custom_Post_Type, and defines the various
 * elements that are specific to the Events Custom Post Type.
 * 
 * @since   0.1.0
 * 
 * @see     Custom_Post_Type
 */
class Programme extends Custom_Post_Type
{
    /**
     * Contains the information regarding the terms
     * 
     * @since   0.1.0
     * @var     array
     */
    protected $terms = array();
    
    /**
     * The WordPress date format. 
     * 
     * @since   0.1.0
     * @var     string
     */
    protected $dateformat;
    
    /**
     * Class constructor method.
     * 
     * The class constructor method is fired when the class is instantiated (or
     * cosntructed ;-)). As this class extends the Abstract Class Custom_Post_Type
     * it defines the specific post type, sets the defaults and adds in specific
     * elements unique to the Events post type.
     * 
     * @since   0.1.0
     * @global  object  $fsesu  Instance of the main plugin class.
     * @return  void.
     */
    protected function __construct()
    { 
        global $fsesu;
        
        /**
         * Set the default variables relating to this post format.
         */
        $this->post_type = 'event';
        $this->post_type_plural = 'Events';
        $this->taxonomy = 'programme_type';
        $this->dateformat = get_option('date_format');
        
        /**
         * Run the set_defaults function to define the array variables.
         */
        $this->set_defaults();
        
        /**
         * Modify some of the default label elements.
         */
        $this->labels['menu_name'] = _x( 'Programme', 'Programme Menu Name', $fsesu->get_domain() );
        $this->labels['search_items'] = __( 'Search Programme', $fsesu->get_domain() );
        
        /**
         * Redefine the labels argument since we made some changes. 
         */
        $this->arguments['labels'] = $this->labels;
        
        /**
         * Modify some of the default post type arguments. 
         */
        $this->arguments['menu_icon'] = 'dashicons-calendar';
        $this->arguments['rewrite'] = array( 'slug' => "unitinfo/programme", 'with_front' => false );
        
        /**
         * Modify some of the default taxonomy arguments. 
         */
        $this->tax_arguments['rewrite'] = array( 'slug' => "unitinfo/programme/category", 'with_front' => false );
        $this->tax_arguments['hierarchical'] = true;
        
        /**
         *  Create Event Post Type Custom Meta Boxes for Edit Page.
         *
         *  Generates the meta boxes for the custom information on the edit pages of the
         *  custom event post type.
         */
        $this->set_fields();
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) ); 
        add_action( 'save_post', array( $this, 'save_post_type' ) );
        
        /**
         *  Create Event Post Type Columns.
         *
         *  Generates the columns for display on the main summary page of the
         *  custom event post type.
         */
        $this->set_columns();
        add_filter( 'manage_edit-event_columns', array( $this, 'add_columns' ) );
        add_action( 'manage_event_posts_custom_column', array( $this, 'render_columns' ), 2, 1);
        add_filter( 'manage_edit-event_sortable_columns', array( $this, 'define_sortable_columns') );
        add_filter( 'pre_get_posts', array( $this, 'orderby_sortable_columns' ), 1 );
        
        /**
         * Set the default term variables.
         * 
         * This sets up what the current term is today, what the last term was and
         * what the next term will be.
         */
        $this->get_terms( strtotime( date( $this->dateformat ) ) );
        
        /**
         * Add a shortcode for displaying various types of programme lists.
         */
        add_shortcode( 'programme', array( $this, 'render_programme' ) );
        
        /**
         * Add the ability to include shortcodes in text widgets.
         * 
         * This filter has been included to allow the use of the 'programme'
         * shortcode to generate a list of upcoming events in the sidebar.
         */
        add_filter('widget_text', 'do_shortcode');
        
        /**
         * Define the style and scripts needed by the Event CPT
         */
        if ( ! is_admin() ) {
            $fsesu->add_style( 'programme', FSESU_URI . 'assets/css/programme.css' );
            $fsesu->add_script( 'programme', FSESU_URI . 'assets/js/programme.js' );
        } else {
            $fsesu->add_admin_script( 'datepicker', FSESU_URI . 'assets/js/datepicker.js', array( 'jquery-ui-datepicker', 'jquery' ), null, true );
            $fsesu->add_admin_style( 'jquery-ui-style', FSESU_URI . 'assets/css/jquery-ui.css' );
        }
        
        
        /**
         * Call the parent constructor method to finish registering the various
         * elements of the custom post type.
         */
        parent::__construct();
        
        /**
         * Register another custom taxonomy.
         */
        add_action( 'init', array( $this, 'register_location' ) );
    }
    
    /**
     * Register location as a taxonomy for the event post type.
     * 
     * @since   0.1.0
     * @return  void
     */
    public function register_location()
    {
        $taxonomy = 'location';
        
        /* Set up the default labels for a custom taxonomy */
        $location_labels = array(
            'name'              => _x( 'Locations', 'taxonomy general name' ),
            'singular_name'     => _x( 'Location', 'taxonomy singular name' ),
            'search_items'      => __( 'Search Locations' ),
            'all_items'         => __( 'All Locations' ),
            'edit_item'         => __( 'Edit Location' ), 
            'update_item'       => __( 'Update Location' ),
            'add_new_item'      => __( 'Add New Location' ),
            'new_item_name'     => __( 'New Location' ),
            'menu_name'         => __( 'Locations' ),
        );
        
        $location_arguments = array(
            'labels'            => $location_labels,
            'public'            => true,
            'show_ui'           => true,
            'meta_box_cb'       => null,
            'show_admin_column' => false,
            'hierarchical'      => false,
            'rewrite'           => array( 'slug' => 'location', 'with_front' => false, 'hierarchical' => true )
        );
        
        register_taxonomy( $taxonomy, $this->post_type, $location_arguments );
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
            case 'event_date':
                // Get the dates and time data
                $date_format = get_option('date_format');
                $start = date( $date_format, $meta['start_date'][0] );
                $end = date( $date_format, $meta['end_date'][0] );
                
                // Format the times
                $time_format = get_option('time_format');
                $starttime = date( $time_format, strtotime( $meta['start_time'][0] ) );
                $endtime = date( $time_format, strtotime( $meta['end_time'][0] ) );
                
                // Output the full date details
                if ( $start == $end ) {
                    echo $start . '<br />' . $starttime . ' - ' . $endtime;
                } else {
                    echo $start . ' ' . $starttime . ' - <br />' . $end . ' ' . $endtime;
                }
                break;
            case 'location':
                // Get the location
                $locations = get_the_terms( $post->ID, 'location' );
                $locations_html = array();
                if ( ! empty( $locations ) ) {
                    foreach ( $locations as $location ) {
                        $locations_html[] = sprintf( '<a href="%s">%s</a>',
                            esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'location' => $location->slug ), 'edit.php' ) ),
                            esc_html( sanitize_term_field( 'name', $location->name, $location->term_id, 'location', 'display' ) ) );
                    }
                    echo join( ', ', $locations_html );
                } else {
                    echo '';
                }
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
                $event_types = get_the_terms( $post->ID, 'programme_type' );
                $event_types_html = array();
                if ( $event_types ) {
                    foreach ( $event_types as $event_type ) {
                        $event_types_html[] = sprintf( '<a href="%s">%s</a>',
                            esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'programme_type' => $event_type->slug ), 'edit.php' ) ),
                            esc_html( sanitize_term_field( 'name', $event_type->name, $event_type->term_id, 'programme_type', 'display' ) ) );
                    }
                    echo join( ', ', $event_types_html );
                } else {
                    _e( 'None', $fsesu->get_domain() );
                }
                break;
        }
    }
    
    /**
     * Defines which Contact columns are sortable.
     *
     * @param   array   $columns    Existing sortable columns
     * @return  array               New sortable columns
     */
    public function define_sortable_columns( $columns )
    {
        $columns['event_date'] = 'event_date';
        $columns['location'] = 'location';
         
        return $columns;
    }
    
    /**
     * Review the query and adjust accordingly
     *
     * @param   array   $query  WordPress query details
     * @return  void
     */
    public function orderby_sortable_columns( $query )
    {
        /**
         * Only review the query if on an admin page, the post type is set and it
         * is set to the event post type.
         */
        if ( is_admin() ) {
            if ( isset( $query->query_vars['post_type'])) {
                if ( $query->query_vars['post_type'] == 'event') {
                    
                    /**
                     * If the orderby variable has not been set, set the default
                     * sort order to the start date in descending numerical order,
                     * but if the orderby variable has been set to event_date or 
                     * location, set the query to the correct column without
                     * specifying asc or desc.
                     */
                    if( ! isset( $query->query_vars['orderby'] ) ) {
                        $query->set('meta_key', 'start_date');
                        $query->set('orderby', 'meta_value_num');
                        $query->set('order', 'DESC');
                    } elseif ( $query->query_vars['orderby'] == 'event_date' ) {
                        $query->set('meta_key', 'start_date');
                        $query->set('orderby', 'meta_value_num');
                    } elseif ( $query->query_vars['orderby'] == 'location' ) {
                        $query->set('meta_key', 'location');
                        $query->set('orderby', 'meta_value');
                    }
                }
            }
        }
    }
    
    
    
    
    
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    public function render_programme( $atts )
    {
        extract( shortcode_atts(
            array(
                'number'    => -1,
                'type'      => 'table'
            ), $atts )
        );
        
        $programme = '<div class="programme">';
        
        $args = array( 
            'posts_per_page'    => $number,
            'post_type'         => 'event',
            'meta_key'          => 'start_date',
            'orderby'           => 'meta_value_num',
            'order'             => 'ASC'
        );
        
        switch ( $type ) {
            case 'planner':
                $programme .= $this->render_programme_planner( $args );
                break;
            case 'upcoming':
                $programme .= $this->render_programme_upcoming( $args );
                break;
            case 'table':
            default:
                $programme .= $this->render_programme_links();
                $programme .= $this->render_programme_table( $args );
                break;
        }
        
        $programme .= '</div>';
        
        return $programme;
    }
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    private function render_programme_table( $args )
    {
        if ( isset( $_GET['fsterm'] ) ) {
            $this->get_terms( $_GET['fsterm'] );
        }
        
        $start = $this->terms['current']['start'];
        $end = $this->terms['current']['end'];
    
        if ( ! isset( $_GET['all'] ) ) {
            $args['meta_query'] =array(
                array(
                    'key'       => 'start_date',
                    'value'     => array( $start, $end ),
                    'type'      => 'numeric',
                    'compare'   => 'BETWEEN'
                ),
            );
        }
        
        $events = $this->get_programme( $args );
        $data_url = FSESU_URI . 'includes/details.php';
        
        $output = <<<EOT
    
            <table class='programme-table'>
                <thead>
                    <th class='date'>Date</th>
                    <th class='activity'>Activity</th>
                </thead>
                <tbody>
            
EOT;
        if ( $events ) {
            foreach ( $events as $event ) {
                $output .= <<<EOD
                
                    <tr>
                        <th>{$event['date']}</th>
                        <td><a href='{$event['permalink']}' data-url='{$data_url}' data-id='{$event['id']}' class='event' onclick='return false;'>{$event['title']}</a></td>
                    </tr>
                    
EOD;
            }
        } else {
            $output .= <<<EOD

                    <tr>
                        <td colspan='2'>No events to display</td>
                    </tr>
EOD;
        }
        
        $output .= <<<EOT
    
                </tbody>
            </table>
EOT;
        
        return $output;
    }
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    private function render_programme_upcoming( $args )
    {
        $args['meta_query'] =array(
            array(
                'key'       => 'end_date',
                'value'     => strtotime( 'today' ),
                'type'      => 'numeric',
                'compare'   => '>=',
            ),
        );
        
        $events = $this->get_programme( $args );
        $data_url = FSESU_URI . 'includes/details.php';
        
        $output = '<dl class="programme-upcoming"><dt></dt><dd></dd>';
        
        if ( $events ) {
            foreach( $events as $event ) {
                $output .= <<<EOD
            
                    <dt>{$event['date']}</dt>
                    <dd>
                        Event: <a href='{$event['permalink']}' data-url='{$data_url}' data-id='{$event['id']}' class='event' onclick='return false;'>{$event['title']}</a><br>
                        Where: {$event['location']}<br>
                        Price: {$event['cost']}
                    </dd>
                
EOD;
            }
        } else {
            $output .= <<<EOD

                    No upcoming events
EOD;
        }
        
        $output .= '</dl>';
        
        return $output;
    }
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    private function render_programme_planner( $args )
    {
        $year = ( isset( $_GET['y'] ) ) ? $_GET['y'] : date( 'Y' );

        $month['1']     = 'Jan';
        $month['2']     = 'Feb';
        $month['3']     = 'Mar';
        $month['4']     = 'Apr';
        $month['5']     = 'May';
        $month['6']     = 'Jun';
        $month['7']     = 'Jul';
        $month['8']     = 'Aug';
        $month['9']     = 'Sep';
        $month['10']    = 'Oct';
        $month['11']    = 'Nov';
        $month['12']    = 'Dec';
        
        $next = $year + 1;
        $prev = $year - 1;
        $url = get_page_link();
        
        $calendar = <<<EOT
        
                <table class='planner'>
                    <caption>$year</caption>
                    <thead>
                        <th>&nbsp;</th>
                        <th>m</th><th>t</th><th>w</th><th>t</th><th>f</th><th>s</th><th>s</th>
                        <th>m</th><th>t</th><th>w</th><th>t</th><th>f</th><th>s</th><th>s</th>
                        <th>m</th><th>t</th><th>w</th><th>t</th><th>f</th><th>s</th><th>s</th>
                        <th>m</th><th>t</th><th>w</th><th>t</th><th>f</th><th>s</th><th>s</th>
                        <th>m</th><th>t</th><th>w</th><th>t</th><th>f</th><th>s</th><th>s</th>
                        <th>m</th><th>t</th>
                    </thead>
                
EOT;
        
        foreach ( $month as $nr => $name ) {
            if ( $nr &1 ) {
                $calendar .= "
                    <tr>";
            } else {
                $calendar .= "
                    <tr class='even'>";
            } 
            $calendar .= "<th class='month'>$name</th>";
        
            $days_in_month = cal_days_in_month( CAL_GREGORIAN, $nr, $year );
            $first_day = date('w', mktime( 0, 0, 0, $nr, 1, $year )); 
            $first_day = ( $first_day == 0 ) ? 7 : $first_day; 
            
            $days = $first_day + $days_in_month;
            $counter = 1;
            
            for ( $i = 1; $i < 38; $i++ ) {
                $counter++;
                $date[$i] = $counter;
                $date[$i] -= $first_day;
                $class = '';
                $classes = array();
                if ( $date[$i] < 1 || $date[$i] > $days_in_month ) {
                    $calendar .= '<td>&nbsp;</td>';
                } else { 
                    $calendar .= $this->get_event( $args, $year, $nr, $date[$i] );
                }
            } 
            
            $calendar .= "
                    </tr>";
        } 
        
        $calendar .=  "</table>";
    
        if ($year != '2008') {
            $calendar .=  <<<EOT
            <div class='alignleft'>
                <a href='$url?y=$prev' title='Previous Year'>
                    <i class='fa fa-chevron-left'></i> Previous Year
                </a>
            </div>
            <div class='alignright textright'>
                <a href='$url?y=$next' title='Next Year'>
                    Next Year <i class="fa fa-chevron-right"></i>
                </a>
            </div>
            <div class='aligncenter textcenter'>
                <a href='$url' title='This Year'>
                    This Year
                </a>
            </div>

EOT;
        } else {
            $calendar .= <<<EOT
            <div class='alignright textright'>
                <a href='$url?y=$next' title='Next Year'>
                    Next Year <i class="fa fa-chevron-right"></i>
                </a>
            </div>
            <div class='aligncenter textcenter'>
                <a href='$url' title='This Year'>
                    This Year
                </a>
            </div>

EOT;
        }
        
        $calendar .= "<ul class='event_types'>";
    
        $types = get_terms( 'programme_type' );
        if ( !empty( $types ) && !is_wp_error( $types ) ) {
            foreach ( $types as $type ) {
                $calendar .=  "<li class='{$type->slug}'>{$type->name}</li>";
            }
        }
    
        $calendar .= "</ul>";
        
        return $calendar;
    }
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    private function render_event_date( $startdate, $enddate )
    {
        if ( date( $this->dateformat, $startdate ) == date( $this->dateformat, $enddate ) ) {
            $date = date( 'd F', $startdate );
        } elseif ( date( 'm', $startdate ) == date( 'm', $enddate ) ) {
            $date = date( 'd', $startdate ) . ' - ' . date( 'd F', $enddate );
        } elseif ( date( 'y', $startdate ) == date( 'y', $enddate ) ) {
            $date = date( 'd M', $startdate ) . ' - ' . date( 'd M', $enddate );
        } else {
            $date = date( 'd M Y', $startdate ) . ' - ' . date( 'd M Y', $enddate );
        }
        
        return $date;
    }
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    private function render_programme_links( )
    {
        $url = get_page_link();
        
        $links = <<<EOT
        
            <div class='alignleft'>
                <a href='$url?fsterm={$this->terms['previous']['start']}'>
                    <i class='fa fa-chevron-left'></i> Previous Term
                </a>
            </div>
            <div class='alignright textright'>
                <a href='$url?fsterm={$this->terms['next']['start']}'>
                    Next Term <i class="fa fa-chevron-right"></i>
                </a>
            </div>
            <div class='aligncenter textcenter'>
                <a href='$url?all'>
                    Whole Programme
                </a><br>
                <a href='$url'>
                    Current Programme
                </a>
            </div>
            
EOT;

        return $links;
    }
    
    
    
    
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    private function get_programme( $args )
    {
        $query = new \WP_Query( $args );
        
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                
                $id             = get_the_ID();
                $title          = get_the_title();
                $startdate      = get_post_meta( $id, 'start_date', true );
                $enddate        = get_post_meta( $id, 'end_date', true );
                $date           = $this->render_event_date( $startdate, $enddate );
                $description    = get_the_content();
                $location       = '';
                $cost           = get_post_meta( $id, 'cost', true );
                $link           = get_post_meta( $id, 'link', true );
                $permalink      = get_permalink();
                $category       = '';
                
                $locations = get_the_terms( $id, 'location' );
                
                if ( $locations && ! is_wp_error( $locations ) ) { 
                	$location_list = array();
                	foreach ( $locations as $location ) {
                		$location_list[] = $location->name;
                	}
                	$location = join( ', ', $location_list );
                }
                
                $categories = get_the_terms( $id, 'programme_type' );
                
                if ( $categories && ! is_wp_error( $categories ) ) { 
                	$category_list = array();
                	foreach ( $categories as $category ) {
                		$category_list[] = $category->name;
                	}
                	$category = join( ', ', $category_list );
                }
                
                $programme[] = array( 
                    'id'            => $id,
                    'title'         => $title,
                    'startdate'     => $startdate,
                    'enddate'       => $enddate,
                    'date'          => $date,
                    'description'   => $description,
                    'location'      => $location,
                    'cost'          => $cost,
                    'link'          => $link,
                    'permalink'     => $permalink,
                    'category'      => $category
                );
                
            }
            
            wp_reset_postdata();
            
            return $programme;
            
        } else {
            return;
        }
        
    }
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    private function get_event( $args, $y, $m, $d )
    {
        $args['meta_query'] =array(
            array(
                'key'       => 'start_date',
                'value'     => mktime( 0, 0, 0, $m, $d + 1, $y ),
                'type'      => 'numeric',
                'compare'   => '<',
            ),
            array(
                'key'       => 'end_date',
                'value'     => mktime( 0, 0, 0, $m, $d, $y ),
                'type'      => 'numeric',
                'compare'   => '>=',
            )
        );
        
        $events = $this->get_programme( $args );
        $data_url = FSESU_URI . 'includes/details.php';
        
        $day = mktime( 0, 0, 0, $m, $d, $y );
        if ( date( 'N', $day ) > 5 ) {
            $classes[] = 'weekend';
        }
        if ( $events[0] ) {
            $classes[] = 'activity';
            $cell = "<a href='{$events[0]['permalink']}' data-start='{$events[0]['startdate']}' data-url='$data_url' data-id='{$events[0]['id']}' class='event' onclick='return false;'>$d</a>";
        } else {
            $cell = $d;
        }
        if ( $events[0]['category'] ) {
            $classes[] = strtolower( str_replace( ',_', ' ', str_replace( ' ', '_', trim( $events[0]['category'] ) ) ) );
        }
        if ( ! empty( $classes ) ) {
            $class = ' class="' . join( ' ', $classes ) . '"';
        }
        
        $event = "<td$class>$cell</td>";
        
        return $event;
    }
    
    /**
     * Short description.
     * 
     * Long description.
     * 
     * @since x.x.x
     * @access (for functions: only use if private)
     * 
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Short description.
     * 
     * @param  type $var Description.
     * @param  type $var Optional. Description.
     * @return type Description.
     */
    private function get_terms( $date )
    {
        /**
         * Define the years based on the $date parameter
         */
        $year = date( 'Y', $date );
        $next_year = date( 'Y', $date ) + 1;
        $last_year = date( 'Y', $date ) - 1;
        
        /**
         * Define the easter dates based on the $date parameter
         */
        $easter = easter_date( $year );
        $next_easter = easter_date( $next_year );
        $last_easter = easter_date( $last_year );
        
        /**
         * Define the term details based on the $date parameter
         */
        $spring = array( 
            'name'      => 'Spring ' . $year,
            'start'     => strtotime( '01 January ' . $year ),
            'end'       => $easter
        );
        $next_spring = array( 
            'name'      => 'Spring ' . $next_year,
            'start'     => strtotime( '01 January ' . $next_year ),
            'end'       => $next_easter
        );
        $summer = array( 
            'name'      => 'Summer ' . $year,
            'start'     => $easter + 86400,
            'end'       => strtotime( '15 August ' . $year )
        );
        $last_autumn = array( 
            'name'      => 'Autumn ' . $last_year,
            'start'     => strtotime( '16 August ' . $last_year ),
            'end'       => strtotime( '31 December ' . $last_year )
        );
        $autumn = array( 
            'name'      => 'Autumn ' . $year,
            'start'     => strtotime( '16 August ' . $year ),
            'end'       => strtotime( '31 December ' . $year )
        );
        
        /**
         * Define the term dates based on the $date parameter
         */
        if ( $date < $easter ) {
            $this->terms['current'] = $spring;
            $this->terms['next'] = $summer;
            $this->terms['previous'] = $last_autumn;
        } elseif ( $date > $easter
        && $date < strtotime( '15 August ' . $year ) ) {
            $this->terms['current'] = $summer;
            $this->terms['next'] = $autumn;
            $this->terms['previous'] = $spring;
        } elseif ( $date > strtotime( '15 August ' . $year ) ) {
            $this->terms['current'] = $autumn;
            $this->terms['next'] = $next_spring;
            $this->terms['previous'] = $summer;
        }
    }
    
    
    
    
    
    /**
     * Define the custom fields to be used by the Event post type.
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
                    'label'         => 'Start Date',
                    'id'            => 'start_date',
                    'type'          => 'datetime',
                    'description'   => 'The date the event starts on',
                    'default'       => strtotime( 'Monday' )
                ),
                array(
                    'label'         => 'Start Time',
                    'id'            => 'start_time',
                    'type'          => 'time',
                    'description'   => 'The time the event starts',
                    'default'       => '19:00'
                )
            ),
            array( 
                array(
                    'label'         => 'End Date',
                    'id'            => 'end_date',
                    'type'          => 'datetime',
                    'description'   => 'The date the event ends on',
                    'default'       => strtotime( 'Monday' )
                ),
                array(
                    'label'         => 'End Time',
                    'id'            => 'end_time',
                    'type'          => 'time',
                    'description'   => 'The time the event ends',
                    'default'       => '21:00'
                )
            ),
            array(
                array(
                    'label'         => 'Cost',
                    'id'            => 'cost',
                    'type'          => 'text',
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
            'event_date'    => __( 'Date', $fsesu->get_domain() ),
            'title'         => __( 'Event', $fsesu->get_domain() ),
            'location'      => __( 'Location', $fsesu->get_domain() ),
            'cost'          => __( 'Cost', $fsesu->get_domain() ),
            'type'          => __( 'Type', $fsesu->get_domain() ),
        );
    }
}