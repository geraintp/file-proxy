<?php
/**
 * Ttd File Proxy - Plugin Class File
 *
 * @return void
 * @author Geraint Palmer 
 */

class TtdFileProxy extends TtdPluginClass
{	
	protected $plugin_domain='TtdFileProxy';
	protected $options_key = 'plugin:ttd:file-proxy';
	protected $options;
	
	protected $rules;

	protected $_options = array(
		'key-length'			=> 7,
		'uninstall'				=> true,
		'url-key'				=> 'file',
	);
	
	function __construct()
	{
		parent::__construct();
		
		// pages where our plugin needs translation
		$local_pages = array('plugins.php');
		
		// init options manager
		$this->options = new GcpOptions($this->options_key, $this->_options);
		
		// load localisation
		if( in_array( $pagenow, $local_pages ) )
			$this->handle_load_domain();
		
		// Add admin menu interface
		if( is_admin() ){
			//include( GCPF_ADMIN.DS."adminController.php" );
			//$adminCrtl = new GcpfAdminController( &$this );
			//add_action('admin_menu', array(&$adminCrtl, 'adminMenus'));
		}
		//add_action('template_redirect', array(&$this,'uri_detect'));
					
		// add activation hooks
		register_activation_hook   ( TTDFP_PLUGIN_FILE , array(&$this, 'activate'  ));
		register_deactivation_hook ( TTDFP_PLUGIN_FILE , array(&$this, 'deactivate'));
		
		// shortcodes
		add_shortcode('file-proxy', array(&$this, 'return_proxy_url'));
		
		// adds proxy rewrite rule & query_var
		add_action('generate_rewrite_rules', array(&$this,'add_rewrite_rules'));
		add_filter('query_vars', array(&$this, 'query_vars'));
		
		// intercepts and acts on query_var file-proxy
		add_action('init', array(&$this,'request_handler'), 999);		
	
		add_action('init', array(&$this,'flush_rules'));
	}
	
	/**
	 * flushes the new rewrite rule
	 *
	 * @return void
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	function flush_rules(){
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	} 
	
	/**
	 * Adds a rewrite rule to wordpress, rewrite logic
	 *
	 * @return void
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	function add_rewrite_rules( $wp_rewrite ) {
		$new_rules = array( $this->get_option('url-key').'/(.+)' => 'index.php?'. $this->get_option('url-key').'=1'.$wp_rewrite->preg_index(1) );
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
		//$this->rules = $wp_rewrite->rules;
	}
	
	/**
	 * Filters query_vars array add required get variable
	 *
	 * @return array
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	function query_vars( $vars )
	{
	    array_push($vars, $this->get_option('url-key'));
	    return $vars;
	}
	
	/**
	 * activate function/hook installs and initialized nessacery components
	 *
	 * @return void
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	public function activate()
	{
		$this->flush_rules();
		
		$this->update_option("version", TTDPF_VERSION );
		if( defined('WP_CONTENT_DIR') ){
			if(!is_dir(WP_CONTENT_DIR.DS.'cache')){
				mkdir(WP_CONTENT_DIR.DS.'cache');
			}
			if(!is_dir(WP_CONTENT_DIR.DS.'cache'.DS. $plugin_domain)){	
				mkdir(WP_CONTENT_DIR.DS.'cache'.DS. $plugin_domain);
			}
			if(!is_dir(WP_CONTENT_DIR.DS.'cache'.DS. $plugin_domain)){
				exit ("cache dir failure");
			}
		}
		else
			mkdir(TTDFP_DIR.DS.'cache');
	}
		
	/**
	 * deactivate function/hook cleans up after the plugin
	 *
	 * @return void
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	public function deactivate()
	{
		if( (boolean)$this->get_option("uninstall") ){
			delete_option($this->options_key);
			
			if(is_dir(WP_CONTENT_DIR.DS.'cache'.DS. $plugin_domain ))
				$this->rmdirr(WP_CONTENT_DIR.DS.'cache'.DS. $plugin_domain );
			if(is_dir(TTDFP_DIR.DS.'cache'))
				$this->rmdirr(TTDFP_DIR.DS.'cache');
		}
	}
	
	/**
	 * Intercepts file request and indexes and authenticates before returning file
	 *
	 * @return void
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	public function request_handler()
	{	
		global $wp_query;
		
		if ( isset( $_GET[ $this->get_option('url-key') ] )) {
			
			if(!is_user_logged_in()){
				auth_redirect();
				exit;
			}
				
			$this->return_file( $_GET[ $this->get_option('url-key') ] );
			exit;
		}
	}
	
	public function return_file($id='')
	{
		global $wpdb;
		
		// define absolute path to image folder
		if ( ! defined( 'WP_CONTENT_DIR' ) )
		      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		
		$upload_folder = WP_CONTENT_DIR.DS.'uploads'.DS ;
		
		$file_data = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}posts WHERE id={$id}" );
		
		$query = "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id={$id} AND meta_key='_wp_attached_file'";
		
		$file_path = $upload_folder . $wpdb->get_var( $query );
				
		if ( file_exists( $file_path ) && is_readable( $file_path ) && is_file( $file_path ) ) {
			header( 'Content-type: '.$file_data->post_mime_type );
			header( "Content-Disposition: attachment; filename=\"" . $file_data->post_name .'.'.substr( $file_data->post_mime_type, -3 )."\"");
		    header( 'Content-length: '. (string)(filesize( $file_path )) );
			$file = @ fopen($file_path, 'rb');
		    if ( $file ) {
	        	fpassthru( $file );
	        	exit;
	      	}
		}else{
			echo "404 - File not found";
		}	
		exit;
	}
	
	
	/**
	 * Intercepts file request and indexes and authenticates before returning file
	 *
	 * @return void
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	public function return_proxy_url($atts, $content = '')
	{	
		extract(shortcode_atts(array(
				'id' => '',
				'alt' => 'Some Really Great File',
			), $atts));
		
		$link =  get_bloginfo('url') .'/index.php?'. $this->options->get_option('url-key') .'='. $id;
		$title = empty($content) ? 'DEFAULT TITLE' : $content ;
		
		if( !is_user_logged_in() )
			$title = $title . " - Login to download this file." . $_SERVER['REQUEST_URI'];
		echo "<a href='{$link}' alt='{$alt}'>{$title}</a>";
	}
}
?>