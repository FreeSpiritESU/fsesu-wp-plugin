<?php
/* SVN FILE: $Id$ */
/**
 *  events.php Event Custom Post Type Setup File
 *  
 *  This file is part of the FreeSpirit ESU Wordpress Theme functions. It is used to setup a 
 *  custom post type for events to be used to generate and display the programme. Custom  
 *  taxonomies are used for the event location and price, and custom meta boxes are used for
 *  the start and end date of the event. All this information is then used by the events 
 *  theming files to produce the programme pages, PDF's and ics outputs.
 *  
 *  PHP Version 5
 *  
 *  @package        FreeSpiritESU
 *  @subpackage     Functions
 *  @subpackage     CustomPostTypes
 *  @copright       FreeSpirit ESU <http://www.freespiritesu.org.uk/> 2011 
 *  @author         Richard Perry <http: //www.perry-online.me.uk/>
 *  @since          Release 0.1.0
 *  @version        $Rev$
 * 	@modifiedby    	$LastChangedBy$
 * 	@lastmodified  	$Date$
 *
 *  @todo           ToDo List
 *                  - Complete the contextual help for the events post type
 *                  - Add the ICS output functionality
 *                  - Add the PDF output functionality
 */

/**
 *  Create Event Post Type
 *
 *  Creates a new custom post type with the name Events
 */

add_action( 'init', 'create_event_postype' );

function create_event_postype() {
   $labels = array(
      'name' => _x('Events', 'Events'),
      'singular_name' => _x('Event', 'Event'),
      'add_new' => _x('Add New', 'events'),
      'add_new_item' => __('Add New Event'),
      'edit_item' => __('Edit Event'),
      'new_item' => __('New Event'),
      'view_item' => __('View Event'),
      'search_items' => __('Search Events'),
      'not_found' =>  __('No events found'),
      'not_found_in_trash' => __('No events found in Trash'),
      'parent_item_colon' => '',
   );

   $args = array(
      'label' => __('Programme'),
      'labels' => $labels,
      'public' => true,
      'can_export' => true,
      'show_ui' => true,
      'capability_type' => 'post',
      'menu_icon' => 'dashicons-calendar',
      'hierarchical' => false,
      'rewrite' => array( 'slug' => "unitinfo/programme" ),
      'supports'=> array('title', 'thumbnail', 'excerpt', 'editor') ,
      'show_in_nav_menus' => true,
      'taxonomies' => array( 'fs_eventcategory', 'post_tag')
   );

   register_post_type( 'fs_events', $args);
}









/**
 *  Create Event Post Type Custom Taxonomies
 *
 *  Creates new taxonomies to be used with the custom event post type
 */

add_action( 'init', 'create_eventcategory_taxonomy', 0 );

function create_eventcategory_taxonomy() {

   $cat_labels = array(
      'name' => _x( 'Event Categories', 'taxonomy general name' ),
      'singular_name' => _x( 'Event Category', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Event Categories' ),
      'popular_items' => __( 'Popular Event Categories' ),
      'all_items' => __( 'All Event Categories' ),
      'parent_item' => null,
      'parent_item_colon' => null,
      'edit_item' => __( 'Edit Event Category' ),
      'update_item' => __( 'Update Event Category' ),
      'add_new_item' => __( 'Add New Event Category' ),
      'new_item_name' => __( 'New Event Category Name' ),
      'separate_items_with_commas' => __( 'Separate event categories with commas' ),
      'add_or_remove_items' => __( 'Add or remove event categories' ),
      'choose_from_most_used' => __( 'Choose from the most used event categories' ),
   );

   register_taxonomy('fs_eventcategory','fs_events', array(
      'label' => __('Event Category'),
      'labels' => $cat_labels,
      'hierarchical' => true,
      'show_ui' => true,
      'query_var' => true,
      'rewrite' => array( 'slug' => 'event-category' ),
   ));
}









/**
 *  Create Event Post Type Columns
 *
 *  Generates the columns for display on the main summary page of the
 *  custom event post type
 */

add_filter ("manage_edit-fs_events_columns", "fs_events_edit_columns");
add_action ("manage_posts_custom_column", "fs_events_custom_columns", 2, 1);

function fs_events_edit_columns($columns) {

   $columns = array(
      "cb" => "<input type=\"checkbox\" />",
      "fs_col_ev_date" => "Date",
      "title" => "Event",
      "fs_col_ev_thumb" => "Thumbnail",
      "fs_col_ev_loc" => "Location",
      "fs_col_ev_price" => "Price",
      "fs_col_ev_desc" => "Description",
      "fs_col_ev_cat" => "Category",
   );

   return $columns;

}

function fs_events_custom_columns($column) {

   global $post;
   $custom = get_post_custom();
   switch ($column) {
      case "fs_col_ev_date":
         // Get the dates and time data
         $start = $custom["fs_events_startdate"][0];
         $end = $custom["fs_events_enddate"][0];

         // Format the dates
         $startdate = date("j M y", $start);
         $enddate = date("j M y", $end);

         // Format the times
         $time_format = get_option('time_format');
         $starttime = date($time_format, $start);
         $endtime = date($time_format, $end);
         if ($startdate == $enddate) {
            echo $startdate . '<br />' . $starttime . ' - ' . $endtime;
         } else {
            echo $startdate . ' ' . $starttime . ' - <br />' . $enddate . ' ' . $endtime;
         }
         break;
      case "fs_col_ev_thumb":
         // Get the post thumbnail
         $post_image_id = get_post_thumbnail_id(get_the_ID());
         if ($post_image_id) {
            $thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
            if ($thumbnail) (string)$thumbnail = $thumbnail[0];
            echo '<img src="';
            echo bloginfo('template_url');
            echo '/timthumb/timthumb.php?src=';
            echo $thumbnail;
            echo '&h=60&w=60&zc=1" alt="" />';
         }
         break;
      case "fs_col_ev_loc":
         // Get the location
         echo $custom["fs_events_loc"][0];
         break;
      case "fs_col_ev_price":
         // Get the cost for the event
         echo $custom["fs_events_price"][0];
         break;
      case "fs_col_ev_desc";
         the_excerpt();
         break;
      case "fs_col_ev_cat":
         // Get the list of categories
         $eventcats = get_the_terms($post->ID, "fs_eventcategory");
         $eventcats_html = array();
         if ($eventcats) {
            foreach ($eventcats as $eventcat)
            array_push($eventcats_html, $eventcat->name);
            echo implode($eventcats_html, ", ");
         } else {
            _e('None', 'themeforce');;
         }
         break;
   }
}









/**
 *  Create Event Post Type Custom Meta Boxes for Edit Page
 *
 *  Generates the meta boxes for the custom information on the edit pages of the
 *  custom event post type
 */

add_action( 'admin_init', 'fs_events_create' );

function fs_events_create() {
   add_meta_box('fs_events_meta', 'Events', 'fs_events_meta', 'fs_events', 'side', 'high');
}

function fs_events_meta () {

   // Get the data from the database

   global $post;
   $custom     = get_post_custom($post->ID);
   $meta_sd    = $custom["fs_events_startdate"][0];
   $meta_ed    = $custom["fs_events_enddate"][0];
   $meta_st    = $meta_sd;
   $meta_et    = $meta_ed;
   $meta_loc   = $custom["fs_events_loc"][0];
   $meta_price = str_replace("&pound;", "�", $custom["fs_events_price"][0]);
   $meta_link  = $custom["fs_events_link"][0];

   // Get the default time and date formats

   $date_format = get_option('date_format'); // Not required in this code
   $time_format = get_option('time_format');

   // Populate the fields if there is not data already, 00:00 for time

   if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;}

   // Convert the dates to more human readable formats

   $clean_sd = date("D, j M Y", $meta_sd);
   $clean_ed = date("D, j M Y", $meta_ed);
   $clean_st = date($time_format, $meta_st);
   $clean_et = date($time_format, $meta_et);

   // Add a hidden fields used purely for security purposes

   echo '<input type="hidden" name="fs-events-nonce" id="fs-events-nonce" value="' .
   wp_create_nonce( 'fs-events-nonce' ) . '" />';

   
   // Generate the output

   ?>
   <div class="fs-meta">
      <ul>
         <li><label>Start Date</label><input name="fs_events_startdate" id="fs_events_startdate" value="<?php echo $clean_sd; ?>" /></li>
         <li><label>Start Time</label><input name="fs_events_starttime" value="<?php echo $clean_st; ?>" /><em></em></li>
         <li><label>End Date</label><input name="fs_events_enddate" id='fs_events_enddate' value="<?php echo $clean_ed; ?>" /></li>
         <li><label>End Time</label><input name="fs_events_endtime" value="<?php echo $clean_et; ?>" /><em></em></li>
      </ul>
      <ul>
         <li><label>Location</label><input name="fs_events_loc" class="fslocation" value="<?php echo $meta_loc; ?>" /></li>
         <li><label>Price</label><input name="fs_events_price" class="fsprice" value="<?php echo $meta_price; ?>" /></li>
         <li><label>Link</label><input name="fs_events_link" class="fslink" value="<?php echo $meta_link; ?>" /></li>
      </ul>
   </div>
   <?php
}









/**
 *  Save data
 *
 *  Save all the custom meta data input by the user when entering a new
 *  event in the event custom post type.
 */

add_action ('save_post', 'save_fs_events');

function save_fs_events(){

    global $post;

    // Use the nonce generated by the form to verify the data

    if ( !wp_verify_nonce( $_POST['fs-events-nonce'], 'fs-events-nonce' )) {
        return $post->ID;
    }

    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;

    
    // Convert dates into Unix format and update

    if(!isset($_POST["fs_events_startdate"])):
        return $post;
        endif;
        $updatestartd = strtotime ( $_POST["fs_events_startdate"] . $_POST["fs_events_starttime"] );
        update_post_meta($post->ID, "fs_events_startdate", $updatestartd );

    if(!isset($_POST["fs_events_enddate"])):
        return $post;
        endif;
        $updateendd = strtotime ( $_POST["fs_events_enddate"] . $_POST["fs_events_endtime"]);
        update_post_meta($post->ID, "fs_events_enddate", $updateendd );

    
    // Update the other custom meta data fields

    if(!isset($_POST["fs_events_loc"])):
        return $post;
        endif;
        $location = $_POST["fs_events_loc"];
        update_post_meta($post->ID, "fs_events_loc", $location );

    if(!isset($_POST["fs_events_price"])):
        return $post;
        endif;
        $price = $_POST["fs_events_price"];
        update_post_meta($post->ID, "fs_events_price", $price );

    if(!isset($_POST["fs_events_link"])):
        return $post;
        endif;
        $link = str_replace("�", "&pound;", $_POST["fs_events_link"]);
        update_post_meta($post->ID, "fs_events_link", $link );

}









/**
 *  Amend the default messages
 *
 *  Change the default updates messages so that when an event is added/updated
 *  'events' is used instead of 'posts' for the event custom post type.
 */

add_filter('post_updated_messages', 'events_updated_messages');

function events_updated_messages( $messages ) {

  global $post, $post_ID;

  $messages['fs_events'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Event updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Event updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Event published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Event saved.'),
    8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'j M Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}









/**
 *  JS Datepicker UI
 *
 *  Deregister the default WP jQuery UI file and then register a 
 *  customised jQuery file to suit our needs
 */

add_action( 'admin_print_styles-post.php', 'events_styles', 1000 );
add_action( 'admin_print_styles-post-new.php', 'events_styles', 1000 );

add_action( 'admin_print_scripts-post.php', 'events_scripts', 1000 );
add_action( 'admin_print_scripts-post-new.php', 'events_scripts', 1000 );

function events_styles() {
    global $post_type;
    if( 'fs_events' != $post_type )
        return;
    wp_enqueue_style('ui-datepicker', get_stylesheet_directory_uri() . '/css/jquery-ui-1.8.9.custom.css');
}

function events_scripts() {
    global $post_type;
    if( 'fs_events' != $post_type )
    return;
    wp_enqueue_script('jquery-ui', get_stylesheet_directory_uri() . '/js/jquery-ui-1.8.9.custom.min.js', array('jquery'));
    wp_enqueue_script('ui-datepicker', get_stylesheet_directory_uri() . '/js/jquery.ui.datepicker.min.js');
    wp_enqueue_script('custom_script', get_stylesheet_directory_uri() . '/js/pubforce-admin.js', array('jquery'));
}









/**
 *  Display contextual help for the Events Custom Post Type
 *
 *  Generate and display customised help for the Events Custom Post
 *  Type to help the end user to better understand the new Post Type
 *  & make full use of it
 */

add_action( 'contextual_help', 'fs_events_add_help_text', 10, 3 );

function fs_events_add_help_text($contextual_help, $screen_id, $screen) { 
  //$contextual_help .= var_dump($screen); // use this to help determine $screen->id
  if ('book' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Things to remember when adding or editing an Event:') . '</p>' .
      '<ul>' .
      '<li>' . __('Specify the correct genre such as Mystery, or Historic.') . '</li>' .
      '<li>' . __('Specify the correct writer of the book.  Remember that the Author module refers to you, the author of this book review.') . '</li>' .
      '</ul>' .
      '<p>' . __('If you want to schedule the book review to be published in the future:') . '</p>' .
      '<ul>' .
      '<li>' . __('Under the Publish module, click on the Edit link next to Publish.') . '</li>' .
      '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more information:') . '</strong></p>' .
      '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>') . '</p>' .
      '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>' ;
  } elseif ( 'edit-book' == $screen->id ) {
    $contextual_help = 
      '<p>' . __('This is the help screen displaying the table of books blah blah blah.') . '</p>' ;
  }
  return $contextual_help;
}