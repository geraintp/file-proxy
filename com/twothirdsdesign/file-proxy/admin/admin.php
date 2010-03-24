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
		
		add_action('wp_ajax_ttd_file_proxy', array(&$this, 'admin_ajax_commit') );	
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
					
					// check for CSRF
					check_admin_referer('ttd-file-proxy');
					
					//echo "<pre>"; print_r( $_POST ); echo "</pre>";
					
					if( $this->m->get_option( "permalinks" != "disabled" ) )
						$this->m->update_option( "permalinks", isset( $_POST[ 'permalinks' ] ) ? 'on' : 'off' );
	
					if( $this->m->get_option( "cache" != "disabled" ) )
						$this->m->update_option( "cache", isset( $_POST[ 'cache' ] ) ? 'on' : 'off' );
					
					$this->m->update_option( "uninstall", isset( $_POST[ 'uninstall' ] ) ? true : false );
					$this->m->update_option( "url-key", sanitize_title_with_dashes( strval( $_POST['url-key']) ) );
					$this->m->update_option( "login-url", strval( $_POST['login-url'] ) );
					$this->m->update_option( "redirect-target", esc_attr( $_POST['redirect-target'] ) );
					
					$this->msg = "saved";
			}
			else if( $_POST['ttd_file_proxy_submit_hidden'] == "reset" ){
				// check for CFX
				check_admin_referer('ttd-file-proxy-reset');
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
	function render_settings_page(){	
		
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
	}


	/**
	 * ajax saves settings panel options
	 *
	 * @since 0.6
	 */
	function admin_ajax_commit() {
		
		if (!current_user_can( 'manage_options' )) die('0');
		
		if( isset( $_POST['type'] ) ){

			switch( esc_attr( $_POST['type'] ) ){
				case 'settings':
					
					$data = $_POST['data'];
					parse_str($data,$output);
					
					if( $this->m->get_option( "permalinks" != "disabled" ) )
						$this->m->update_option( "permalinks", isset( $output[ 'permalinks' ] ) ? 'on' : 'off' );
	
					if( $this->m->get_option( "cache" != "disabled" ) )
						$this->m->update_option( "cache", isset( $output[ 'cache' ] ) ? 'on' : 'off' );
					
					$this->m->update_option( "uninstall", isset( $output[ 'uninstall' ] ) ? true : false );
					$this->m->update_option( "url-key", strval( $output['url-key'] ) );
					$this->m->update_option( "login-url", strval( $output['login-url'] ) );
					$this->m->update_option("redirect-target", esc_attr( $output['redirect-target'] ) );
					
					
					die('1');
					break;
				default:
					die('0');
					break;
			}
		}
	}
}
?>