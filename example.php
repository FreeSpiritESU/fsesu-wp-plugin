<?php
/* 
Plugin Name: Product Database
Description: Versatile product database
Version: 1.0.0
Author: James Bruce
Author URI: http://make-money-blogging-ideas.com/
 */
 
if (!class_exists("Products")) {
    class Products {
    
        function Products(){
            add_action('init', array(&$this,'products_init'));
            register_activation_hook(__FILE__, 'my_rewrite_flush');
        }
    
        // declare the new post type 'sites'
        function products_init() {
            $args = array(
                'labels' => array(
                    'name' => __('Products'),
                    'singular_name' => __('Product'),
                ),
                'public' => true,
                'rewrite' => array("slug" => "products"), // permalink structure
                'supports' => array('thumbnail','custom-fields','title','editor','comments'),
                'has_archive' => true
            );

            register_post_type( 'products' , $args );

        }
        
        function my_rewrite_flush() {
          products_init();
          flush_rewrite_rules();
        }
        
                
        
    }
    
    class LatestProductsWidget extends WP_Widget
    {
      function LatestProductsWidget()
      {
        $widget_ops = array('classname' => 'LatestProductsWidget', 'description' => 'Displays the latest product additions with thumbnail' );
        $this->WP_Widget('LatestProductsWidget', 'Latest Products and Thumbnail', $widget_ops);
      }
     
      function form($instance)
      {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
        $title = $instance['title'];
    ?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
    <?php
      }
     
      function update($new_instance, $old_instance)
      {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        return $instance;
      }
     
      function widget($args, $instance)
      {
        extract($args, EXTR_SKIP);
     
        echo $before_widget;
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
     
        if (!empty($title))
          echo $before_title . $title . $after_title;;
     
        // WIDGET CODE GOES HERE
        query_posts('post_type=products&posts_per_page=2&order=DESC&meta_key=Level&orderby=meta_value');
        if (have_posts()) : 
            while (have_posts()) : the_post(); 
                echo "<a href='".get_permalink()."'>";
                the_title('<h2>','</h2>');
                the_post_thumbnail('thumbnail');
                the_meta(); 
                echo "</a>";
            endwhile;
        endif; 
        wp_reset_query();
     
        echo $after_widget;
      }
 
    }
    add_action( 'widgets_init', create_function('', 'return register_widget("LatestProductsWidget");') );
        
}


if (!isset($pd_plugin_instance)) $pd_plugin_instance = new Products();




?>