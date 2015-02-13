<?php
/*
Plugin Name: Salavat counter
Plugin URI : http://wp-master.ir
Description: Salavat counter in widget and shortcode mode
Author: wp-master.ir
Author URI: http://wp-master.ir
Version: 0.4
url:http://wp-master.ir
Text Domain: salavat_counter
*/

define('__SC__' , 'salavat_counter');
/*
* load plugin language
*/
add_action( 'plugins_loaded', 'salavac_counter_widget_lang');
function salavac_counter_widget_lang()
{
  load_plugin_textdomain( __SC__, false, dirname( plugin_basename( __FILE__ ) ));
}
__('Salavat counter in widget and shortcode mode' , __SC__);

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
  salavat_counter_make_form();
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
  if(get_option( __SC__.'_salavat_display_image', 'true' ) != 'true'){ echo '<style type="text/css" media="all"> .salavat-counter img.salavat_gif{display:none !important;} </style>'; }
}



/*
salavat shortcod
*/
add_shortcode('salavat_counter' , 'salavat_counter_shortcode');
function salavat_counter_shortcode(){
    salavat_counter_make_form(true,true);
}


/*
make salavat counter form
*/
function salavat_counter_make_form($echo=true , $shortcode=false){
  $html = '';
  $counter = get_option( __SC__.'_counter', 0 );
  $counter_for = get_option( __SC__.'_counter_for', 0 );
  if(is_rtl()) { $class = 'rtl'; } else{ $class = 'ltr'; }
  if($shortcode) { $class .= ' shortcode'; }
  $html .='
  <noscript> <div style="display:none;" </noscript>
  <div class="salavat-counter '.$class.'">';
    if($counter_for != ''){
      $html .= '<p class="salavat-for"><span class="salavat-for-title">'.__('for' , __SC__).': </span><br/><span class="salavat-for-text">'.$counter_for.'</span></p>';
    }

   $html .='
    <p><span class="awaiting-mod"><span class="pending-count sc-padding-count"> '.$counter.'</span></span><span class="sc-till-now">'.__('salavat was saied till now!' , __SC__).'</span></p>
    <p>
      <img class="salavat_gif" width="200" src="'.plugin_dir_url(__FILE__).'salavat.gif">
    </p>
    <p>
      <button class="sc-say-salavat"><img class="ajax_loader_gif" src="'.plugin_dir_url(__FILE__).'/ajax-loader.gif" style="display:none;">&nbsp;&nbsp;&nbsp;'.__('say 1 salavat' , __SC__).'&nbsp;&nbsp;&nbsp;<img class="ajax_loader_gif" src="'.plugin_dir_url(__FILE__).'/ajax-loader.gif" style="display:none;"></button>
    </p>

  </div>';
  $html .= '<noscript> </div><p>'.__('for working salavat counter javascript is needed.' , __SC__).'</p> </noscript>';
  if($echo) echo $html; else  return $html;
}


/*
admin page
*/
add_action('admin_menu', '_salavat_counter_admin_fn');
function _salavat_counter_admin_fn(){
  add_options_page( __('Salavat counter' , __SC__), __('Salavat counter' , __SC__), 'manage_options' , __SC__ , 'salavat_counter_admin_fn');
}

function salavat_counter_admin_fn(){
  ?>
  <style type="text/css">

  </style>
  <?php
  $class = 'ltr';
  if(is_rtl()) { $class = 'rtl'; } else{ $class = 'ltr'; }
  ?>
  <div class="salavat_counter_admin <?php echo $class; ?>">
    <h2><?php _e('salavat counter' , __SC__); ?></h2>
    <div class="content">
      <?php
      if(isset($_POST['salavat_for'])){
          update_option( __SC__.'_counter_for' , esc_sql($_POST['salavat_for']));
          update_option( __SC__.'_salavat_display_image' , esc_sql($_POST['salavat_display_image']));
        }
        $counter_for = get_option( __SC__.'_counter_for', '' );
        $salavat_display_image = get_option( __SC__.'_salavat_display_image', '' );

      ?>
      <form method="post">
      <table class="form-table">
        <tbody>
          <tr>
          <th scope="row">
          <label for="salavat_for"><?php _e('say salavat for:' , __SC__); ?></label>
          </th>
          <th scope="row">
          <label for="salavat_for"><?php _e('Display image?' , __SC__); ?></label>
          </th>
		  </tr>
		  <tr>
          <td>
          <input id="salavat_for" class="regular-text" type="text" value="<?php echo $counter_for; ?>" name="salavat_for">
          <p class="description"> <?php _e('if you want to use any subject for salavats fill it , otherwise leave it empty' , __SC__); ?> </p>
          </td>
		  <td>
          <input id="salavat_display_image" class="regular-text" type="checkbox" value="true" <?php if($salavat_display_image=='true'){ echo 'checked'; } ?> name="salavat_display_image">
          <p class="description"> <?php _e('show image or not' , __SC__); ?> </p>
          </td>
          </tr>
        </tbody>
      </table>
      <p class="submit"><input type="submit" value="<?php _e('submit' , __SC__); ?>" class="button button-primary" id="submit" name="submit"></p>
      </form>
    </div>
    <hr/>
    <h2><?php _e('Usage:' , __SC__); ?></h2>
    <div class="content">
      <b><?php _e('widget',__SC__); ?> : </b><?php _e('you can use salavat counter widget ' , __SC__); ?> <br/>
      <b><?php _e('shortcode',__SC__); ?> : </b><?php _e('you can use [salavat_counter] shortcode in anywhere you want,posts,pages,... ' , __SC__); ?> <br/>
     <b><?php _e('theme',__SC__); ?> : </b> <?php _e('use this code anywhere in your theme codes:' , __SC__); ?> <br/><textarea style="width:400px; height:40px; direction:ltr;background:#222;color:#fff;"><?php echo '<?php do_shortcode(\'[salavat_counter]\'); ?>'; ?></textarea>

    </div>
  </div>
  <?php
}