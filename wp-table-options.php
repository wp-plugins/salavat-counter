<?php

/**
 * Master theme class
 * 
 * @package Bolts
 * @since 1.0
 */
class salavatcounter_Options {
	
	private $sections;
	private $checkboxes;
	private $settings;
	
	/**
	 * Construct
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
	
	}
	
	/**
	 * Add options page
	 *
	 * @since 1.0
	 */
	public function add_pages() {
		
		$admin_page = add_options_page( __( 'salavat-counter' , 'salavatcounter' ), __( 'salavat-counter' , 'salavatcounter' ), 'manage_options', 'salavatcounter-options', array( &$this, 'display_page' ) );
		
		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'styles' ) );
		
	}
	
	
	/**
	 * Display options page
	 *
	 * @since 1.0
	 */
	public function display_page() {
		global $wpdb;
		$salavatcounter =$wpdb->prefix.'salavatcounter';
		
		echo '<div class="wrap">';
		echo '<h1>'.__('Salavat Counters' , 'salavatcounter').' <a class="page-title-action" href="options-general.php?page='.$_GET['page'].'&addnew=true">'.__('New Salavat Counter' , 'salavatcounter').'</a></h1>';
		if(isset($_GET['addnew'])){
			/**
			 * grab new item data
			 */
			if($_POST){
				if(trim($_POST['salavat_title']) != ''){
					extract($_POST);
					$insert = $wpdb->insert(
							$salavatcounter,
							array(
								'title'			=>	$salavat_title,
								'niyat'			=>	$salavat_niyat,
								'salavat'		=>	'0',
								'view_method'	=>	$salavat_view_method,
								'view_style'	=>	$salavat_view_style,
								'position'		=>	$salavat_position,
								'image_url'		=>	$image_url,
								'image_show'	=>	$image_show,
								'status'		=>	$salavat_status,
								'rounded'		=>	$rounded,
								),
							array(
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								)
						);
					if($insert){
						echo '<div class="updated">'.__('Inserted' , 'salavatcounter').'</div>';
					}else{
						echo '<div class="fail error">'.__('Problem , try again' , 'salavatcounter').'</div>';
					}
				}
			}
			?>
			<form action="" method="post" id="new-salavat-submit-form">
				<table class="form-table">
					<tbody>
						
						<tr>
						<th scope="row">
						<label for="salavat_status"><?php _e('status' , 'salavatcounter') ?></label>
						</th>
						<td>
						<select name="salavat_status" id="salavat_status">
							<option value="1"><?php _e('Enable' , 'salavatcounter'); ?></option>
							<option value="0"><?php _e('Disable' , 'salavatcounter'); ?></option>
						</select>
						</td>
						</tr>

						<tr>
						<th scope="row">
						<label for="salavat_title"><?php _e('title' , 'salavatcounter') ?></label>
						</th>
						<td>
						<input type="text" name="salavat_title" id="salavat_title" value="">
						</td>
						</tr>

						<tr>
						<th scope="row">
						<label for="salavat_niyat"><?php _e('niyat' , 'salavatcounter') ?></label>
						</th>
						<td>
						<input type="text" name="salavat_niyat" id="salavat_niyat" value="">
						</td>
						</tr>

						<tr>
						<th scope="row">
						<label for="salavat_view_method"><?php _e('view method' , 'salavatcounter') ?></label>
						</th>
						<td>
						<select name="salavat_view_method" id="salavat_view_method">
							<option value="widget"><?php _e('Widget' , 'salavatcounter'); ?></option>
							<option value="corner"><?php _e('Corner' , 'salavatcounter'); ?></option>
						</select>

						<select class="salavatcounter-hide-me" name="salavat_position" id="salavat_position">
							<option value="top-right"><?php _e('top-right' , 'salavatcounter'); ?></option>
							<option value="top-left"><?php _e('top-left' , 'salavatcounter'); ?></option>
							<option value="bottom-right"><?php _e('bottom-right' , 'salavatcounter'); ?></option>
							<option value="bottom-left"><?php _e('bottom-left' , 'salavatcounter'); ?></option>
						</select>
						
						<br>
						<span class="salavatcounter-hide-me"><strong><?php _e('Enable image' , 'salavatcounter'); ?></strong><input type="checkbox" name="image_show" id="image_show" value="1" checked="checked"><strong><?php _e('rounded?' , 'salavatcounter'); ?></strong><input type="checkbox" name="rounded" id="rounded" value="1"></span>
						<span class="salavatcounter-hide-me"><strong><?php _e('image(Leave empty for default image)' , 'salavatcounter'); ?></strong><input type="text" name="image_url" id="image_url" value=""> <button class="button-secondary" id="salavatcounter-load-wp-uploader"><?php _e('Select' , 'salavatcounter'); ?></button> </span>

						</td>
						</tr>

						<tr>
						<th scope="row">
						<label for="salavat_view_style"><?php _e('view style' , 'salavatcounter') ?></label>
						</th>
						<td>
						<select name="salavat_view_style" id="salavat_view_style">
							<?php
							$styles = scandir(_salavatcounter_DIR.'css'.DIRECTORY_SEPARATOR.'styles');
							foreach ($styles as $style) {
								if(strpos($style , '.css') === false) continue;
								$style = str_replace('.css' , '' , $style);

								if($style  == 'all') continue;
								echo '<option value="'.$style.'">'.__($style , 'salavatcounter').'</option>';
							}

							?>
						</select>

						</td>
						</tr>


					</tbody>
				</table>
				<input type="submit" value="<?php _e('Add' , 'salavatcounter'); ?>" id="new-salavat-submit" name="new-salavat-submit" class="button-primary">
			</form>
			<?php
		}


			/**
			 * update
			 */
			if(isset($_GET['action']) && $_GET['action'] == 'edit' && trim($_GET['salavat']) !=''){
				$id = $_GET['salavat'];
				/**
				 * update submitted form
				 */
				if(isset($_POST['update-salavat-submit'])){
					extract($_POST);
					$update = $wpdb->update(
						$salavatcounter,
						array(
							'title'			=>	$salavat_title,
							'niyat'			=>	$salavat_niyat,
							'view_method'	=>	$salavat_view_method,
							'view_style'	=>	$salavat_view_style,
							'position'		=>	$salavat_position,
							'image_url'		=>	$image_url,
							'image_show'	=>	$image_show,
							'status'		=>	$salavat_status,
							'rounded'		=>	$rounded,
								),
							array('id' => $id),
							array(
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								),
								array('%d')
						);
	
					if($update){
						echo '<div class="updated">'.__('Updated' , 'salavatcounter').'</div>';
					}else{
						echo '<div class="fail error">'.__('Problem , try again' , 'salavatcounter').'</div>';
					}
				}
				/*************************************************************/
				$row = $wpdb->get_row($wpdb->prepare("select * from $salavatcounter where id=%d" , $id));
				if($row){
					?>
					<form action="" method="post" id="new-salavat-submit-form">
						<table class="form-table">
							<tbody>
								
								<tr>
								<th scope="row">
								<label for="salavat_status"><?php _e('status' , 'salavatcounter') ?></label>
								</th>
								<td>
								<select name="salavat_status" id="salavat_status">
									<option <?php selected( $row->status, '1', true ); ?> value="1"><?php _e('Enable' , 'salavatcounter'); ?></option>
									<option <?php selected( $row->status, '0', true ); ?> value="0"><?php _e('Disable' , 'salavatcounter'); ?></option>
								</select>
								</td>
								</tr>

								<tr>
								<th scope="row">
								<label for="salavat_title"><?php _e('title' , 'salavatcounter') ?></label>
								</th>
								<td>
								<input type="text" name="salavat_title" id="salavat_title" value="<?php echo $row->title; ?>">
								</td>
								</tr>

								<tr>
								<th scope="row">
								<label for="salavat_niyat"><?php _e('niyat' , 'salavatcounter') ?></label>
								</th>
								<td>
								<input type="text" name="salavat_niyat" id="salavat_niyat" value="<?php echo $row->niyat; ?>">
								</td>
								</tr>

								<tr>
								<th scope="row">
								<label for="salavat_view_method"><?php _e('view method' , 'salavatcounter') ?></label>
								</th>
								<td>
								<select name="salavat_view_method" id="salavat_view_method">
									<option <?php selected( $row->view_method, 'widget', true ); ?> value="widget"><?php _e('Widget' , 'salavatcounter'); ?></option>
									<option <?php selected( $row->view_method, 'corner', true ); ?> value="corner"><?php _e('Corner' , 'salavatcounter'); ?></option>
								</select>

								<select class="salavatcounter-hide-me" name="salavat_position" id="salavat_position">
									<option <?php selected( $row->position, 'top-right', true ); ?> value="top-right"><?php _e('top-right' , 'salavatcounter'); ?></option>
									<option <?php selected( $row->position, 'top-left', true ); ?> value="top-left"><?php _e('top-left' , 'salavatcounter'); ?></option>
									<option <?php selected( $row->position, 'top-center', true ); ?> value="top-center"><?php _e('top-center' , 'salavatcounter'); ?></option>
									<option <?php selected( $row->position, 'bottom-right', true ); ?> value="bottom-right"><?php _e('bottom-right' , 'salavatcounter'); ?></option>
									<option <?php selected( $row->position, 'bottom-left', true ); ?> value="bottom-left"><?php _e('bottom-left' , 'salavatcounter'); ?></option>
									<option <?php selected( $row->position, 'bottom-center', true ); ?> value="bottom-center"><?php _e('bottom-center' , 'salavatcounter'); ?></option>
								</select>
								
								<br>
								<span class="salavatcounter-hide-me"><strong><?php _e('Enable image' , 'salavatcounter'); ?></strong><input type="checkbox" name="image_show" id="image_show" value="1" <?php checked($row->image_show , 1 , true); ?>><strong><?php _e('rounded' , 'salavatcounter'); ?></strong><input type="checkbox" name="rounded" id="rounded" value="1" <?php checked($row->rounded , 1 , true); ?>></span>
								<span class="salavatcounter-hide-me"><strong><?php _e('image(Leave empty for default image)' , 'salavatcounter'); ?></strong><input type="text" name="image_url" id="image_url" value="<?php echo $row->image_url; ?>"> <button class="button-secondary" id="salavatcounter-load-wp-uploader"><?php _e('Select' , 'salavatcounter'); ?></button> </span>

								</td>
								</tr>

								<tr>
								<th scope="row">
								<label for="salavat_view_style"><?php _e('view style' , 'salavatcounter') ?></label>
								</th>
								<td>
								<select name="salavat_view_style" id="salavat_view_style">
									<?php
									$styles = scandir(_salavatcounter_DIR.'css'.DIRECTORY_SEPARATOR.'styles');
									foreach ($styles as $style) {
										if(strpos($style , '.css') === false) continue;
										$style = str_replace('.css' , '' , $style);

										if($style  == 'all') continue;
										echo '<option '.selected( $row->view_style, $style, false ).' value="'.$style.'">'.__($style , 'salavatcounter').'</option>';
									}

									?>
								</select>

								</td>
								</tr>


							</tbody>
						</table>
						<input type="submit" value="<?php _e('Update' , 'salavatcounter'); ?>" id="update-salavat-submit" name="update-salavat-submit" class="button-primary">
					</form>

					<?php
				}
			}
		/**
		 * delete
		 */
		if(isset($_GET['action']) && $_GET['action'] == 'delete' && trim($_GET['salavat']) !=''){
				$id = $_GET['salavat'];
				$delete = $wpdb->query($wpdb->prepare("delete from $salavatcounter where id=%d" , $id));
				if($delete){
					echo '<div class="updated">'.__('Deleted' , 'salavatcounter').'</div>';
				}else{
					echo '<div class="fail error">'.__('Problem , try again' , 'salavatcounter').'</div>';
				}
		}
		/**
		 * active
		 */
		if(isset($_GET['action']) && $_GET['action'] == 'active' && trim($_GET['salavat']) !=''){
				$id = $_GET['salavat'];
				$delete = $wpdb->update(
						$salavatcounter,
						array('status' 	=>	1),
						array('id' 		=>	$id),
						array('%s'),
						array('%d')
					);
				if($delete){
					echo '<div class="updated">'.__('Activated' , 'salavatcounter').'</div>';
				}else{
					echo '<div class="fail error">'.__('Problem , try again' , 'salavatcounter').'</div>';
				}
		}

		/**
		 * deactive
		 */
		if(isset($_GET['action']) && $_GET['action'] == 'deactive' && trim($_GET['salavat']) !=''){
				$id = $_GET['salavat'];
				$delete = $wpdb->update(
						$salavatcounter,
						array('status' 	=>	0),
						array('id' 		=>	$id),
						array('%s'),
						array('%d')
					);
				if($delete){
					echo '<div class="updated">'.__('Deactivated' , 'salavatcounter').'</div>';
				}else{
					echo '<div class="fail error">'.__('Problem , try again' , 'salavatcounter').'</div>';
				}
		}
		$list = new salavatcounter_lists();
		$list->prepare_items();
		echo $list->display();

		echo '</div>';
		
		
	}
	
	
	/**
	* jQuery Tabs
	*
	* @since 1.0
	*/
	public function scripts() {
		
		wp_print_scripts( 'jquery-ui-tabs' );
		
	}
	
	/**
	* Styling for the theme options page
	*
	* @since 1.0
	*/
	public function styles() {
		

		
	}
	
	
}

$theme_options = new salavatcounter_Options();
