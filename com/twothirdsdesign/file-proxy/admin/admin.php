<?php
require_once( TTDFP_CORE.DS.'ttd_plugin_admin_class.php' );
/**
 * Ttd File Proxy - Admin Settings Plugin Class File
 *
 * @return void
 * @author Geraint Palmer 
 */
class TtdFileProxyAdmin extends TtdPluginAdminClass
{
	protected $m;
	protected $menu_parent 			= 'options-general.php';
	protected $setting_identifier 	= 'ttd_file_proxy';
	protected $msg;
	
	function __construct( $main_ref )
	{
		$this->m = &$main_ref;
		$this->m->get_option("uninstall");
		
		$this->domain = $this->m->get_domain();
		
		add_action( 'init', array(&$this, 'init') );
		
		add_action('wp_ajax_ttd_file_proxy_action', array(&$this, 'admin_ajax_commit') );	
	}
	
	/**
	 * Initializes the theme administration functions. Makes sure we have a theme settings
	 * page and a meta box on the edit post/page screen.
	 *
	 * @since 0.5
	 */
	function init(){
	
		/* Initialize the theme settings page. */
		add_action( 'admin_menu', array(&$this, 'settings_page_init' ) );
		
		/* Adds file proxy button to the upload manager */
		add_filter( 'attachment_fields_to_edit', array(&$this, 'upload_form_filter'), 999, 2 );
	}
	
	/**
	 * add file proxy button to the media upload manager.
	 *
	 * @since 0.5
	 */
	function upload_form_filter( $form_fields, $post ){
		$link = "[ttd-fp-url]{$post->ID}[/ttd-fp-url]";
		$form_fields['url']['html'] = $form_fields['url']['html'] . "<button type='button' class='button urlfileproxy' title='" . esc_attr($link) . "'>" . __( 'File Proxy', $this->domain ) . "</button>";
		return $form_fields;
	}
	
	
	/**
	 * Generate admin url for plugin settings.
	 *
	 * @since 0.5
	 */
	function get_settings_link(){
		return admin_url() . $this->menu_parent .'?page='. $this->setting_identifier ;
	}
	
	/**
	 * echos admin url for plugin settings.
	 *
	 * @since 0.5
	 */
	function settings_link(){
		echo $this->get_settings_link();
	}

	/**
	 * Initializes all the plugin settings page functions. This function is used to create the plugin 
	 * settings page, then use that as a launchpad for specific actions that need to be tied to the
	 * settings page.
	 *
	 * @since 0.5
	 */
	function settings_page_init() {
	
		/* Create the theme settings page. */
		$this->settings_page =  add_submenu_page( $this->menu_parent, __('File Proxy Settings' , $this->domain ), __('File Proxy', $this->domain ) , '10', $this->setting_identifier, array(&$this, 'render_settings_page') );
		
	
		/* Make sure the settings are saved. */
		add_action( "load-{$this->settings_page}", array(&$this, 'load_settings_page') );
	
		/* Load the JavaScript and stylehsheets needed for the theme settings.*/ 
		add_action( "load-{$this->settings_page}", array(&$this, 'enqueue_script') );
		add_action( "load-{$this->settings_page}", array(&$this, 'enqueue_style') );
		add_action( "admin_head-{$this->settings_page}", array(&$this,'execute_scripts') );
	}
	
		
	/**
	 * Injects required css for admin settings page
	 *
	 * @since 0.5
	 */
	function enqueue_style() {
		wp_enqueue_style( 'iphone-switch' , TTDFP_URL .'assets/css/iphone-switch.css' , false, $this->m->get_option("version"), 'screen' );
		wp_enqueue_style( 'aaia-admin-style' , TTDFP_URL .'assets/css/admin-style.css' , false, $this->m->get_option("version"), 'screen' );
	}
	
	
	/**
	 * Injects required js for admin settings page
	 *
	 * @since 0.5
	 */
	function enqueue_script() {
		$src = TTDFP_URL .'assets/js/iphone-style-checkboxes.js';
		wp_enqueue_script("iphone-style-checkboxes" , $src, "jquery", $this->m->get_option("version"), false );
	}
	
	
	/**
	 * Injects required css for admin settings page
	 *
	 * @since 0.5
	 */
	function execute_scripts() { 
		include 'js.php';
	}
	
	
	/**
	 * Process the admin setting form and save any changes
	 *
	 * @since 0.5
	 */
	function load_settings_page(){
		global $user_level;

		if($user_level > 9){
			if( "Y" == esc_attr( $_POST['ttd_file_proxy_submit_hidden'] )){
					//echo "<pre>"; print_r( $_POST ); echo "</pre>";
					
					if( $this->m->get_option( "permalinks" != "disabled" ) )
						$this->m->update_option( "permalinks", isset( $_POST[ 'permalinks' ] ) ? 'on' : 'off' );
	
					if( $this->m->get_option( "cache" != "disabled" ) )
						$this->m->update_option( "cache", isset( $_POST[ 'cache' ] ) ? 'on' : 'off' );
					
					$this->m->update_option( "uninstall", isset( $_POST[ 'uninstall' ] ) ? true : false );
					$this->m->update_option( "url-key", sanitize_title_with_dashes( strval( $_POST['url-key']) ) );
					
					$this->msg = "saved";
			}
			else if( $_POST['ttd_file_proxy_submit_hidden'] == "reset" ){
				$this->reset_options();
			}
		}
	}
	
	
	/**
	 * needs documenting
	 *
	 * @since 0.6
	 */
	function reset_options(){
		delete_option( $this->m->get_options_key() );
		$this->m->flush_options();
		$this->m->update_option("version", TTDPF_VERSION);
		$this->m->update_option("default-login-url", get_option('siteurl') . '/wp-login.php' );
		$this->m->update_option("login-url", get_option('siteurl') . '/wp-login.php' );	
		wp_redirect( $this->get_settings_link() );
	}

	
	
	/**
	 * Displays the plugin settings page and calls do_meta_boxes() to allow additional settings
	 * meta boxes to be added to the page.
	 *
	 * @since 0.6
	 */
	function render_settings_page() 
	{	
		global $wp_rewrite;
		
		$url = $this->m->generate_url( 0 );
		$url = explode( $this->m->get_option('url-key'), $url);
		
		$cache_desc = (string)$this->m->get_option("cache") == "disabled" ? __( 'Error: Caching Disabled, can not write to file system.', $this->domain ) :
																			__('This setting is not yet used.', $this->domain);															
		
		$url_key_desc = sprintf( __("Change the url your file are referenced through, i.e. %surl-key%s", $this->domain ), $url[0], $url[1] );
		$login_url = __("The url guest visitors should be redirected to.", $this->domain );
		$redirect_target = __("Where a user should been sent after logging in.", $this->domain );
		
		$panels[] = array( 'name'    => 'generaloptions', 
						   'title'   => __('General Options', $this->domain),
						   'options' => array( array( 'name'  => 'url-key', 'title' => __('URL Key', $this->domain), 'type'  => 'textfield', 'description' => $url_key_desc ),
											   array( 'name'  => 'login-url', 'title' => __('Login Redirect URL', $this->domain), 'type'  => 'textfield', 'description' => $login_url ),
											   array( 'name'  => 'redirect-target', 'title' => __('Redirect Target', $this->domain), 'type'  => 'select', 'description' => $redirect_target, 'options' => array( 'file', 'page' ) ),
											   array( 'name'  => 'cache', 'title' => __('Cache', $this->domain), 'type'  => 'checkbox', 'description' => $cache_desc ),
											 )
						  );
			
		$perma_desc = $wp_rewrite->using_permalinks() ? '<span id="change-permalinks"><a href="options-permalink.php" class="button" target="_blank">'. __('Change Permalinks') .'</a></span>' :
														 __('Uses permalink urls.', $this->domain );
		
		if((string)$this->m->get_option("permalinks") == "disabled" )
			$perma_desc = __('This setting is not yet used.', $this->domain);
				
		$panels[] = array( 'name'    => 'permalinkoptions', 
						   'title'   => __('Permalink Options', $this->domain),
						   'options' => array( array( 'name'  => 'permalinks', 'title' => __('Permalinks', $this->domain), 'type'  => 'checkbox', 'description' => $perma_desc )
											 )
						  );
		
		$uninstall_desc =  __("This setting should be \"<strong><em>OFF</em></strong>\" unless you want to permenantly delete this plugin.", $this->domain );
		$uninstall_desc .= (boolean)$this->m->get_option("uninstall") ? "<br/>". __( "All information and settings stored by this plugin will be deleted <strong>when the delete button on the plugin page is select.</strong>", $this->domain ) : '';
		
		$panels[] = array( 'name'    => 'advancedoptions', 
						   'title'   => __('Advanced Options', $this->domain),
						   'options' => array( array( 'name'  => 'uninstall', 'title' => __('Uninstall', $this->domain), 'type'  => 'checkbox', 'description' => $uninstall_desc, "class" => "danger" )
											 )
						  );
		
		
		$this->render_page( $panels );
		
		/*?>
		<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div> 
		<h2><?php  _e( 'File Proxy Settings', $this->domain ); ?> <small>(<a href="<?php $this->settings_link(); ?>&amp;opt=reset"><?php _e('Reset', $this->domain ); ?></a>)</small></h2>

		<?php if ( isset($this->msg)  ) echo '<p class="updated fade below-h2" style="padding: 5px 10px;"><strong>' . __( 'Settings saved.', $this->domain ) . '</strong></p>'; ?>

		<div id="poststuff">

			<form method="post" action="<?php $this->settings_link(); ?>">

				<?php wp_nonce_field( "ttd-file-proxy-settings-page" ); ?>
				<?php //wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php //wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div class="metabox-holder">
					<div class="post-box-container column-1 normal"><?php do_meta_boxes( $this->settings_page, 'normal', NULL ); ?></div>
					<div class="post-box-container column-2 advanced"><?php do_meta_boxes( $this->settings_page, 'advanced', NULL ); ?></div>
					<div class="post-box-container column-3 side"><?php do_meta_boxes( $this->settings_page, 'side', NULL ); ?></div>
				</div>

				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="<?php _e( 'Update Settings', $this->domain); ?>" />
					<input type="hidden" name="<?php echo "ttd_file_proxy_submit_hidden"; ?>" value="Y" />
				</p><!-- .submit -->

			</form>

		</div><!-- #poststuff -->

	</div><!-- .wrap --> <?php */
	}



	function admin_ajax_commit() {
		global $wpdb; // this is how you get access to the database
		$themename = get_option('template') . "_";
		//Uploads
		if(isset($_POST['type'])){
			if($_POST['type'] == 'upload'){

				$clickedID = $_POST['data']; // Acts as the name
				$filename = $_FILES[$clickedID];
				$override['test_form'] = false;
				$override['action'] = 'wp_handle_upload';    
				$uploaded_file = wp_handle_upload($filename,$override);

						$upload_tracking[] = $clickedID;
						update_option( $clickedID , $uploaded_file['url'] );
						//update_option( $themename . $clickedID , $uploaded_file['url'] );
				 if(!empty($uploaded_file['error'])) {echo 'Upload Error: ' . $uploaded_file['error']; }	
				 else { echo $uploaded_file['url']; } // Is the Response
			}


			elseif($_POST['type'] == 'image_reset'){

					$id = $_POST['data']; // Acts as the name
					global $wpdb;
					$query = "DELETE FROM $wpdb->options WHERE option_name LIKE '$id'";
					$wpdb->query($query);
					//die;

			}
			elseif($_POST['type'] == 'framework'){

				$data = $_POST['data'];
				parse_str($data,$output);

				foreach($output as $id => $value){

					if($id == 'woo_import_options'){

						//Decode and over write options.
						$new_import = base64_decode($value);
						$new_import = unserialize($new_import);
						print_r($new_import);

						if(!empty($new_import)) {
							foreach($new_import as $id2 => $value2){

								if(is_serialized($value2)) {

									update_option($id2,unserialize($value2));

								} else {

									update_option($id2,$value2);

								}
							}
						}
					}

					// Woo Show Option Save
					if(!isset($output['woo_show_options'])){ 
						update_option('woo_show_options','false'); 
					}
					elseif ( $id == 'woo_show_options' AND $value == 'true') { update_option($id,'true'); }

					// Woo Theme Version Checker Save
					if(!isset($output['woo_theme_version_checker'])){ 
						update_option('woo_theme_version_checker','false'); 
					}
					elseif ( $id == 'woo_theme_version_checker' AND $value == 'true') { update_option($id,'true'); }


					// Woo Core update Save
					if(!isset($output['woo_framework_update'])){ 
						update_option('woo_framework_update','false'); 
					}
					elseif ( $id == 'woo_framework_update' AND $value == 'true') { update_option($id,'true'); }

					// Woo Buy Themes Save
					if(!isset($output['woo_buy_themes'])){ 
						update_option('woo_buy_themes','false'); 
					}
					elseif ( $id == 'woo_buy_themes' AND $value == 'true') { update_option($id,'true'); }


				}

			}
		}

		else {
			$data = $_POST['data'];
			parse_str($data,$output);

			print_r($output);

			$options =  get_option('woo_template');

			foreach($options as $option_array){


					if(isset($option_array['id'])) { // Headings...


						$id = $option_array['id'];
						$old_value = get_option($id);
						$new_value = '';

						if(isset($output[$id])){
							$new_value = $output[$option_array['id']];
						}
						$type = $option_array['type'];


						if ( is_array($type)){
									foreach($type as $array){
										if($array['type'] == 'text'){
											$id = $array['id'];
											$new_value = $output[$id];
											update_option( $id, stripslashes($new_value));
										}
									}                 
						}
						elseif($new_value == '' && $type == 'checkbox'){ // Checkbox Save

							update_option($id,'false');
							//update_option($themename . $id,'false');


						}
						elseif ($new_value == 'true' && $type == 'checkbox'){ // Checkbox Save

							update_option($id,'true');
							//update_option($themename . $id,'true');

						}
						elseif($type == 'multicheck'){ // Multi Check Save

							$options = $option_array['options'];

							foreach ($options as $options_id => $options_value){

								$multicheck_id = $id . "_" . $options_id;

								if(!isset($output[$multicheck_id])){
								  update_option($multicheck_id,'false');
								  //update_option($themename . $multicheck_id,'false');    
								}
								else{
								   update_option($multicheck_id,'true'); 
								   //update_option($themename . $multicheck_id,'true'); 
								}

							}

						} 

						elseif($type == 'typography'){

							$typography_array = array();	

							/* Size */
							$typography_array['size'] = $output[$option_array['id'] . '_size'];

							/* Face  */
							$typography_array['face'] = stripslashes($output[$option_array['id'] . '_face']);

							/* Style  */
							$typography_array['style'] = $output[$option_array['id'] . '_style'];

							/* Color  */
							$typography_array['color'] = $output[$option_array['id'] . '_color'];

							update_option($id,$typography_array);


						}
						elseif($type == 'border'){

							$border_array = array();	

							/* Width */
							$border_array['width'] = $output[$option_array['id'] . '_width'];

							/* Style  */
							$border_array['style'] = $output[$option_array['id'] . '_style'];

							/* Color  */
							$border_array['color'] = $output[$option_array['id'] . '_color'];

							update_option($id,$border_array);


						}
						elseif($type != 'upload_min'){

							update_option($id,stripslashes($new_value));
						}
					}

			}
		}


		/* Create, Encrypt and Update the Saved Settings */
		global $wpdb;

		$woo_options = array();

		$query = "SELECT * FROM $wpdb->options WHERE option_name LIKE 'woo_%' AND
					option_name != 'woo_options' AND
					option_name != 'woo_template' AND
					option_name != 'woo_custom_template' AND
					option_name != 'woo_settings_encode' AND
					option_name != 'woo_export_options' AND
					option_name != 'woo_import_options' AND
					option_name != 'woo_framework_version' AND
					option_name != 'woo_manual' AND 
					option_name != 'woo_shortname'";

		$results = $wpdb->get_results($query);

		$output = "<ul>";

		foreach ($results as $result){
				$name = $result->option_name;
				$value = $result->option_value;

				if(is_serialized($value)) {

					$value = unserialize($value);
					$woo_array_option = $value;
					$temp_options = '';
					foreach($value as $v){
						if(isset($v))
							$temp_options .= $v . ',';

					}	
					$value = $temp_options;
					$woo_array[$name] = $woo_array_option;
				} else {
					$woo_array[$name] = $value;
				}

				$output .= '<li><strong>' . $name . '</strong> - ' . $value . '</li>';
		}
		$output .= "</ul>";
		$output = base64_encode($output);

		update_option('woo_options',$woo_array);
		update_option('woo_settings_encode',$output);



	  die();

	}	

}
?>