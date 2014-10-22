<?php
/*
Plugin Name: صلوات شمار
Plugin URI : http://wp-master.ir
Author: wp-master.ir
Author URI: http://wp-master.ir
Version: 0.1
url:http://wp-master.ir
*/

define(__SC__ , 'salavat_counter');
/*
* load plugin language
*/
add_action( 'plugins_loaded', 'salavac_counter_widget_lang');
function salavac_counter_widget_lang()
{
  load_plugin_textdomain( __SC__, false, dirname( plugin_basename( __FILE__ ) ).DIRECTORY_SEPARATOR);
}

/*
* widget class
*/
class widget_salavat_shomar extends WP_Widget
{
  function widget_salavat_shomar()
  {
    $widget_ops = array('classname' => 'widget_salavat_shomar', 'description' => __('salavat counter' , __SC__) );
    $this->WP_Widget('widget_salavat_shomar', __('salavat counter' , __SC__), $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => __('salavat counter' , __SC__)) );
    $title = $instance['title'];
	?>
	<p>
		<?php _e('title' , __SC__); ?> ::: <input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo $title; ?>">
	</p>
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
	$title = empty($instance['title']) ? __('salavat counter' , __SC__) : apply_filters('widget_title', $instance['title']);
	echo $before_widget;
	echo $before_title . $title . $after_title;

  $counter = get_option( __SC__.'_counter', 0 );
  if(is_rtl()) { $class = 'rtl'; } else{ $class = 'ltr'; }
	?>
  <div class="salavat-counter <?php echo $class; ?>">
    <p><span class="awaiting-mod"><span class="pending-count sc-padding-count"> <?php echo $counter; ?></span></span><?php _e('salavat was saied till now!' , __SC__); ?></p>
    <p>
      <img width="200" src="<?php echo plugin_dir_url(__FILE__).'salavat.gif'; ?>">
    </p>
    <p>
      <button class="sc-say-salavat"> <img src="<?php echo plugin_dir_url(__FILE__); ?>/ajax-loader.gif" style="display:none;"> <?php _e('say 1 salavat' , __SC__); ?> <img src="<?php echo plugin_dir_url(__FILE__); ?>/ajax-loader.gif" style="display:none;"></button>
    </p>

  </div>
	<?php

	echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("widget_salavat_shomar");') );


/*
* widget css
*/
add_action( 'wp_print_styles', 'salavat_counter_css', 1);
function salavat_counter_css()
{
  wp_enqueue_style( 'salavat_counter_css_', plugin_dir_url(__FILE__ ).'style.css', array(), '0.1', 'all' );
  wp_enqueue_script( 'salavat_counter_js_',  plugin_dir_url(__FILE__ ).'script.js', array('jquery'), '0.1', false );
}



/*
* Ajax callback
*/

add_action( 'wp_ajax_nopriv_say_salavat', 'salavat_counter_ajax');
add_action( 'wp_ajax_say_salavat' , 'salavat_counter_ajax');
function salavat_counter_ajax()
{
  $counter = get_option(__SC__.'_counter' , 0 );
  $counter +=1;
  update_option( __SC__.'_counter' , $counter );
  echo json_encode(array('msg' => 'OK' , 'ok' => 'OK' , 'counter' =>$counter));
  die();
}


/*
add absolute admin js path
*/
add_action('wp_head' , 'salavat_counter_head' , 1);
function salavat_counter_head()
{
  ?>
  <script type="text/javascript"> var sc_ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>
  <?php
}
