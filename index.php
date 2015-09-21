<?php
/*
Plugin Name: salavat-counter
Plugin URI : http://wp-master.ir
Author: wp-master.ir
Author URI: http://wp-master.ir
Description: salavat-counter
Version: 0.8.5
Text Domain: salavatcounter
*/

/*
* No script kiddies please!
*/
defined('ABSPATH') or die("اللهم صل علی محمد و آل محمد و عجل فرجهم");

/*
* Defines
*/
define('_salavatcounter_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('_salavatcounter_PATH', plugin_dir_url(__FILE__));
define('__SC__', 'salavat_counter');


/*
* load plugin language
*/
add_action('plugins_loaded', '_salavatcounter_lang');
function _salavatcounter_lang()
{
    load_plugin_textdomain('salavatcounter', false, dirname(plugin_basename(__FILE__)) . DIRECTORY_SEPARATOR);
}

__('salavat-counter', 'salavatcounter');
__('salavat-counter', 'salavatcounter');
__('blue', 'salavatcounter');
__('green', 'salavatcounter');
__('lajevardi', 'salavatcounter');
__('orange', 'salavatcounter');
__('phosphor', 'salavatcounter');
__('red', 'salavatcounter');
__('yellow', 'salavatcounter');


/**
 * On plugin Activation
 */
register_activation_hook(__FILE__, 'salavatcounter_activate');
function salavatcounter_activate()
{
    global $wpdb;
    //create tables
    $salavatcounter = $wpdb->prefix . 'salavatcounter';
    $sql[] = "CREATE TABLE IF NOT EXISTS `$salavatcounter` (
	      `id` 						bigint(20) 										NOT NULL AUTO_INCREMENT,
	      `title` 					varchar(200) 									NOT NULL,
	      `niyat` 					varchar(200) 									NOT NULL,
	      `salavat`					bigint(11) 										NULL,
	      `view_method`				ENUM( 'widget' , 'corner' ) DEFAULT 'corner' 	NULL,
	      `view_style`				varchar(200) DEFAULT 'green'					NULL,
	      `position`				varchar(200) 									NULL,
	      `image_url`				varchar(200)		 							NULL,
	      `image_show`				ENUM( '0' , '1' ) DEFAULT '1' 					NOT NULL,
	      `status`					ENUM( '0' , '1' ) DEFAULT '1' 					NOT NULL,
	      `show_corner`				ENUM( '0' , '1' ) DEFAULT '0' 					NOT NULL,
	      `rounded`					ENUM( '0' , '1' ) DEFAULT '0' 					NOT NULL,
	      PRIMARY KEY (`id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    foreach ($sql as $s) {
        dbDelta($s);
        $wpdb->query($s);
    }
}


/**
 * On plugin deactivation
 */
function salavatcounter_deactivate()
{

    // deactivation code here...
}

register_deactivation_hook(__FILE__, 'salavatcounter_deactivate');


/**
 * Load Plugin Style And script that are need in front area
 */
function salavatcounter_user_styles_scripts($hook)
{
    if (!is_admin()) {
        wp_register_script('salavatcounter' . 'user_JS', _salavatcounter_PATH . 'js/script.js', array('jquery'), 1.0, false);
        wp_register_style('salavatcounter' . 'user_CSS', _salavatcounter_PATH . 'css/style.css', array(), 1.0, 'all');
        /**
         * Localizes Scripts
         */
        $translation_array = array(
            'text' => __('Localizing was successful', 'salavatcounter'),
            'ajaxurl' => admin_url('admin-ajax.php')
        );
        wp_localize_script('salavatcounter' . 'user_JS', 'salavatcounter', $translation_array);
        wp_enqueue_script('salavatcounter' . 'user_JS');
        wp_enqueue_style('salavatcounter' . 'user_CSS');


    } elseif ($hook == 'settings_page_salavatcounter-options') {
        wp_enqueue_media();
        wp_register_script('salavatcounter' . 'admin_JS', _salavatcounter_PATH . 'js/admin.js', array('jquery'), 1.0, false);
        wp_register_style('salavatcounter' . 'admin_CSS', _salavatcounter_PATH . 'css/admin.css', array(), 1.0, 'all');
        wp_enqueue_script('salavatcounter' . 'admin_JS');
        wp_enqueue_style('salavatcounter' . 'admin_CSS');
    }

}

add_action('wp_enqueue_scripts', 'salavatcounter_user_styles_scripts');
add_action('admin_enqueue_scripts', 'salavatcounter_user_styles_scripts');


/**
 * plugin shortcode
 */
function salavatcounter_shortcode()
{
    $html = '<div id="salavat-counter">';
    $html .= 'salavatcounter shortcode contents here';
    $html .= '</div> <!-- end of #salavat-counter --> ';
    return $html;
}

add_shortcode('salavatcounter_shortcode', 'salavatcounter_shortcode');


/**
 * Admin panel menu
 */
require_once 'wp-table-class.php';
require_once 'wp-table-options.php';

/**
 * Ajax request handling
 */
add_action('wp_ajax_salavatcounter', 'salavatcounter_callback');
add_action('wp_ajax_nopriv_salavatcounter', 'salavatcounter_callback');
function salavatcounter_callback()
{
	header('Content-Type: application/javascript');
	$callback = $_GET['callback'];
    $ok = true;
    $counter = false;
    $msg = '';

    if ($ok && !isset($_GET['id']) && $_GET['type'] == 'corner') {
        $ok = false;
        // $msg = __('id parametr does not specefied' , 'salavatcounter');
        $msg = __('Err:id', 'salavatcounter');
    }

    if ($ok && trim($_GET['id']) == '') {
        //try to work as old version
        $counter = get_option(__SC__ . '_counter', 0);
        $counter += 1;
        update_option(__SC__ . '_counter', $counter);
        echo $callback.'('.json_encode(array('msg' => 'OK', 'ok' => 'OK', 'counter' => $counter)).')';
        die();
    }
    global $wpdb;
    $id = $_GET['id'];
    $salavatcounter = $wpdb->prefix . 'salavatcounter';
    $status = $wpdb->get_col($wpdb->prepare("select `status` from $salavatcounter where id=%d", $id));
    $status = $status[0];
    if ($ok && $status != 1) {
        $ok = false;
        // $msg = __('salavat is not enable' , 'salavatcounter');
        $msg = __('Err:dsbl', 'salavatcounter');
    }

    //set salavat
    if ($ok) {
        if ($wpdb->query($wpdb->prepare("update $salavatcounter set salavat=salavat+1 where id=%d", $id))) {
            $counter = $wpdb->get_col($wpdb->prepare("select salavat from $salavatcounter where id=%d", $id));
			$counter = $counter[0];
            $msg = __('counted', 'salavatcounter');
            $ok = 'OK';
        } else {
            $msg = __('Err:u', 'salavatcounter');
        }
    }
	echo $callback.'('.json_encode(array('msg' => $msg, 'ok' => $ok, 'counter' => $counter)).')';
    die();
}


add_action('wp_ajax_salavatcounter_admin', 'salavatcounter_admin_callback');
add_action('wp_ajax_nopriv_salavatcounter_admin', 'salavatcounter_admin_callback');
function salavatcounter_admin_callback()
{
    $ok = true;
    $msg = '';
    if ($_POST['type'] != 'corner') {
        $ok = false;
        // $msg = __('Only corner mode supported' , 'salavatcounter');
        $msg = 'Err:c';
    }

    if ($ok && !isset($_POST['id'])) {
        $ok = false;
        // $msg = __('id parametr does not specefied' , 'salavatcounter');
        $msg = 'Err:id';
    }


    //set salavat
    if ($ok) {
        global $wpdb;
        $salavatcounter = $wpdb->prefix . 'salavatcounter';
        $id = $_POST['id'];
        if ($wpdb->query($wpdb->prepare("update $salavatcounter set salavat=salavat+1 where id=%d", $id))) {
            $msg = $wpdb->get_col($wpdb->prepare("select salavat from $salavatcounter where id=%d", $id));
            $msg = $msg[0];
        } else {
            $msg = 'Err:u';
        }
    }
    echo json_encode(array('msg' => $msg));
    die();
}

add_action('wp_ajax_salavatcounter_admin_on_corner', 'salavatcounter_admin_on_corner_callback');
add_action('wp_ajax_nopriv_salavatcounter_admin_on_corner', 'salavatcounter_admin_on_corner_callback');
function salavatcounter_admin_on_corner_callback()
{
    $ok = true;
    $msg = '';

    if ($ok && !isset($_POST['id'])) {
        $ok = false;
        // $msg = __('id parametr does not specefied' , 'salavatcounter');
        $msg = 'Err:id';
    }

    if ($ok && !isset($_POST['chkd'])) {
        $ok = false;
        // $msg = __('chkd parametr does not specefied' , 'salavatcounter');
        $msg = 'Err:chkd';
    }

    //set salavat
    if ($ok) {
        global $wpdb;
        $salavatcounter = $wpdb->prefix . 'salavatcounter';
        $id = $_POST['id'];
        $chkd = ($_POST['chkd'] == '' || $_POST['chkd'] == 0) ? 0 : 1;
        if ($wpdb->query($wpdb->prepare("update $salavatcounter set show_corner='%d' where id=%d", $chkd, $id))) {
            $msg = __('done', 'salavatcounter');
        } else {
            $msg = 'Err:u';
        }
    }
    echo json_encode(array('msg' => $msg));
    die();
}


/**
 * grab and generate html with script
 */
add_action('init', 'salavatcounter_js_grab' , 0);
function salavatcounter_js_grab()
{
    if (isset($_GET['salavat-counter-js']) && trim($_GET['salavat-counter-js']) != '') {
        $salavat_id = $_GET['salavat-counter-js'];
        global $wpdb;
        $salavatcounter = $wpdb->prefix . 'salavatcounter';
        $salavat = $wpdb->get_row($wpdb->prepare("select * from $salavatcounter where id =%d", $salavat_id));
        if (!$salavat) return;
        $view_style = ($salavat->view_style == '' ? 'green' : $salavat->view_style);
        $class = 'salavatcounter-corner';
        $class .= ' ' . $view_style;
        $class .= ' ' . $salavat->position;
        $class .= ' ' . 'salavatcounter-' . $salavat->view_method . '-' . $salavat->position;
        $class .= ' ' . $salavat->view_method;
        $class = trim($class);

        /**
         * get styles
         */
        $style = '';
        $style_file = _salavatcounter_DIR . 'css' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . $view_style . '.css';
        $style_file_all = _salavatcounter_DIR . 'css' . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . 'all.css';
        if (!file_exists($style_file))
            return;
        if (file_exists($style_file_all))
            $style .= file_get_contents($style_file_all);

        $style .= file_get_contents($style_file);
        $style = str_replace(array('%URL%', "\""), array(_salavatcounter_PATH . 'css/styles/' . $view_style, '\''), $style);
        header('Content-Type: application/javascript');
        header("cache-control: must-revalidate");
        $offset = 60 * 60;
        $expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header($expire);
        $html = "<style rel='stylesheet' media='screen'>
					" . $style . "
				</style>
				<img class='salavat-ajax-loader salavat-counter-ajax-loader' src='" . _salavatcounter_PATH . "css/img/ajax-loader.gif'>
				<div class='" . $class . "'>
					<span class='salavatacounter-count'>" . $salavat->salavat . "</span>
					<span class='salavatcounter-add-salavat' data-id='" . $salavat->id . "'></span>
				</div>";
        $html = salavat_css_compress($html);
        ?>
        window.onload=function(){
        if(typeof jQuery == 'undefined'){
        //Load jQuery library using plain JavaScript
        var newscript = document.createElement('script');
        newscript.type = 'text/javascript';
        newscript.async = true;
        newscript.src = "<?php bloginfo('siteurl'); ?>/wp-includes/js/jquery/jquery.js";
        (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(newscript);
        }
        jQuery(document).ready(function($){
        $('body').append("<?php echo $html; ?>");
        /*
        add ajax
        */
        $('.salavatcounter-add-salavat').on('click' , function(){
        var this_counter_placeholder = $(this).siblings('span').eq(0);
        this_counter_placeholder.html('<img class="salavat-counter-ajax-loader" src="<?php echo _salavatcounter_PATH . 'css/img/ajax-loader.gif' ?>">');
        $.ajax({
        url: "<?php echo admin_url('admin-ajax.php'); ?>",
        type: 'GET',
		crossDomain: true,
        dataType: 'jsonp',
        data: {action: 'salavatcounter' , type:'corner' , id:$(this).data('id')},
        })
        .done(function(data) {
        this_counter_placeholder.html(data.counter);
        console.log("success");
        })
        .fail(function() {
        console.log("error");
        })
        .always(function() {
        console.log("complete");
        });

        });
        });

        }
        <?php
        die();
    }
}

/**
 * fpr cpmprtess js and css
 */
function salavat_css_compress($txt)
{
    /* remove comments */
    $txt = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $txt);
    /* remove tabs, spaces, newlines, etc. */
    $txt = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $txt);
    return $txt;
}


/**
 * add "show_corner=1" items to site
 */
add_action("wp_head", 'salavatcounter_footer');
function salavatcounter_footer()
{
    global $wpdb;
    $salavatcounter = $wpdb->prefix . 'salavatcounter';
    $result = $wpdb->get_results("select * from $salavatcounter where view_method='corner' and show_corner='1' and `status` ='1'");
    $site_url = get_bloginfo('siteurl');
    $_script = "<script type='text/javascript' src='$site_url?salavat-counter-js=%ID%&mode=%MODE%'></script>";
    foreach ($result as $res) {
        echo str_replace(array('%ID%', '%MODE%'), array($res->id, $res->view_method), $_script);
    }
}


/**
 * salavat shortcod
 */
add_shortcode('salavat_counter', 'salavat_counter_shortcode');
function salavat_counter_shortcode($atts)
{
    $id = false;
    if (isset($atts['id'])) {
        $id = $atts['id'];
    }
    salavat_counter_make_form(true, true, $id);
}


/**
 * make salavat counter form
 */

function salavat_counter_make_form($echo = true, $shortcode = false, $id = false)
{
    global $wpdb;
    $salavatcounter = $wpdb->prefix . 'salavatcounter';
    $txt_domain = 'salavatcounter';
    $html = '';
    $img = plugin_dir_url(__FILE__) . '/css/img/salavat.gif';
    $img_show = true;
    $class = array();
    if (!$id) {
        $counter = get_option(__SC__ . '_counter', 0);
        $counter_for = get_option(__SC__ . '_counter_for', 0);
		$class[] = 'old-ver';
    } else {
        $row = $wpdb->get_row($wpdb->prepare("select * from $salavatcounter where id=%d", $id));
        $counter = $row->salavat;
        $counter_for = $row->niyat;
        if ($row->image_url != '')
            $img = $row->image_url;
        $img_show = $row->image_show;
        $class[] = $row->view_style;
        $class[] = 'salavat-'.$row->view_method;
        $class[] = ($row->rounded == 1)?'salavat-rounded':'';
    }
    if (is_rtl()) {
        $class[] = 'rtl';
    } else {
        $class[] = 'ltr';
    }
    if ($shortcode) {
        $class[] = 'shortcode';
    }

    $html .= '
  <noscript> <div style="display:none;" </noscript>
  <div class="salavat-counter ' . implode(' ',$class) . '">';
    if ($counter_for != '') {
        $html .= '<p class="salavat-for"><span class="salavat-for-title">' . __('for', $txt_domain) . ': </span><br/><span class="salavat-for-text">' . $counter_for . '</span></p>';
    }

    $html .= '
    <p><span class="awaiting-mod"><span class="pending-count sc-padding-count"> ' . $counter . '</span></span><span class="sc-till-now">' . __('salavat was saied till now!', $txt_domain) . '</span></p>
    ';
    if ($img_show) {
        $html .= '    <p>
      <img class="salavat_gif" width="200" src="' . $img . '">
    </p>';
    }
    $html .= '
    <p>
      <button class="sc-say-salavat" data-id="' . $id . '"><img class="ajax_loader_gif" src="' . plugin_dir_url(__FILE__) . '/css/img/ajax-loader.gif" style="display:none;">&nbsp;&nbsp;&nbsp;' . __('say 1 salavat', $txt_domain) . '&nbsp;&nbsp;&nbsp;<img class="ajax_loader_gif" src="' . plugin_dir_url(__FILE__) . '/css/img/ajax-loader.gif" style="display:none;"></button>
    </p>

  </div>';
    $html .= '<noscript> </div><p>' . __('for working salavat counter javascript is needed.', $txt_domain) . '</p> </noscript>';
    if ($echo) echo $html; else  return $html;
}



/*
* widget class
*/
class widget_salavat_shomar extends WP_Widget
{
  function widget_salavat_shomar()
  {
    $widget_ops = array('classname' => 'widget_salavat_shomar', 'description' => __('salavat counter' , 'salavatcounter') );
    $this->WP_Widget('widget_salavat_shomar', __('salavat counter' , 'salavatcounter'), $widget_ops);
  }
 
  function form($instance)
  {
  	global $wpdb;
  	$salavatcounter = $wpdb->prefix . 'salavatcounter';
    $instance = wp_parse_args( (array) $instance, array( 'title' => __('salavat counter' , 'salavatcounter') ,'salavat_id' => '' ) );
    $title = $instance['title'];
    $salavat_id = $instance['salavat_id'];
	?>
	<p>
		<?php _e('title' , 'salavatcounter'); ?> ::: <input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo $title; ?>">
	</p>
	<p>
		<?php _e('salavat counter' , 'salavatcounter'); ?> :::
		<select name="<?php echo $this->get_field_name('salavat_id'); ?>" id="<?php echo $this->get_field_id('salavat_id'); ?>">
			<?php
			$result = $wpdb->get_results("select * from $salavatcounter where `view_method`='widget' and `status`='1'");
			foreach ($result as $res) {
				echo '<option '.selected( $salavat_id, $res->id, false ).' value="'.$res->id.'">'.$res->title.'</option>';
			}
			?>
		</select>
	</p>
	<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['salavat_id'] = $new_instance['salavat_id'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
	extract($args, EXTR_SKIP);
	$title = empty($instance['title']) ? __('salavat counter' , 'salavatcounter') : apply_filters('widget_title', $instance['title']);
	$salavat_id = empty($instance['salavat_id']) ? '' : apply_filters('widget_salavat_id', $instance['salavat_id']);
	echo $before_widget;
	echo $before_title . $title . $after_title;
  salavat_counter_make_form($echo = true, $shortcode = false, $salavat_id );
  echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("widget_salavat_shomar");') );