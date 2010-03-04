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
		'key-length'	=> 7,
		'uninstall'		=> true,
		'url-key'		=> 'file',
		'cache'			=> 'disabled',
		'permalinks'	=> 'disabled',
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
		$this->admin();

		//add_action('template_redirect', array(&$this,'uri_detect'));
					
		// add activation hooks
		register_activation_hook   ( TTDFP_PLUGIN_FILE , array(&$this, 'activate'  ));
		register_deactivation_hook ( TTDFP_PLUGIN_FILE , array(&$this, 'deactivate'));
		register_uninstall_hook	   ( TTDFP_PLUGIN_FILE , array(&$this, 'uninstall'));
		
		// shortcodes
		add_shortcode('file-proxy', array(&$this, 'return_proxy_link'));
		add_shortcode('file-proxy-url', array(&$this, 'return_proxy_url'));
		
		// adds proxy rewrite rule & query_var
		add_action('generate_rewrite_rules', array(&$this,'add_rewrite_rules'));
		add_filter('query_vars', array(&$this, 'query_vars'));
		
		// intercepts and acts on query_var file-proxy
		add_action('init', array(&$this,'request_handler'), 999);
		//add_action('init', array(&$this,'flush_rules'));
	}
	
	function admin(){
		if( is_admin() ){
			require_once( TTDFP_ADMIN.DS.'admin.php' );
			$ttd_file_proxy_admin = new TtdFileProxyAdmin( &$this );
		}
	}
	
	/**
	 * exposes the text options key constant
	 *
	 * @return String
	 * @author Geraint Palmer
	 * @since 0.5
	 **/
	function get_options_key(){
		return $this->options_key;
	}
	
	
	/**
	 * exposes the text domain contant
	 *
	 * @return String
	 * @author Geraint Palmer
	 * @since 0.5
	 **/
	function get_domain(){
		return $this->domain;
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
		$this->install();		
	}
	
	public function install(){
		switch ( $this->get_option("version", "0") )
		{
			case '0.1':
			case '0.2':
			case '0.3':
			case '0.4':
				// Clears options for previous version
				delete_option( $this->options_key );
				break;
			default:
				break;
		}
		if( $this->get_option("cache") != "disabled" )
			$this->build_cache_dir();
			
		$this->update_option("version", TTDFP_VERSION );
	}
	
	public function build_cache_dir()
	{
		if( defined('WP_CONTENT_DIR') ){
			if(!is_dir( WP_CONTENT_DIR.DS.'cache' ) && is_writable( WP_CONTENT_DIR )){
				mkdir( WP_CONTENT_DIR.DS.'cache' );
			}
			if(!is_dir( WP_CONTENT_DIR.DS.'cache'.DS. $this->plugin_domain ) && is_writable( WP_CONTENT_DIR.DS.'cache' )){	
				mkdir( WP_CONTENT_DIR.DS.'cache'.DS. $this->plugin_domain );
			}
			if(!is_dir( WP_CONTENT_DIR.DS.'cache'.DS. $this->plugin_domain )){
				$this->update_option('cache', 'disabled');
			}
		}
		else if(!is_dir( TTDFP_DIR.DS.'cache') && is_writable( TTDFP_DIR ))
		{
			mkdir( TTDFP_DIR.DS.'cache' );	
			if(is_dir( TTDFP_DIR.DS.'cache') && is_writable( TTDFP_DIR )){
				$this->update_option('cache', 'off');
			}
		}else{
			$this->update_option('cache', 'disabled');
		}
	}
	

	
	public function uninstall(){
		if( (boolean)$this->get_option("uninstall") ){
			delete_option($this->options_key);
			
			if( is_dir( WP_CONTENT_DIR.DS.'cache'.DS. $this->plugin_domain ) && is_writable( WP_CONTENT_DIR.DS.'cache'.DS. $this->plugin_domain ) )
				$this->rmdirr(WP_CONTENT_DIR.DS.'cache'.DS. $this->plugin_domain );
			if( is_dir( TTDFP_DIR.DS.'cache' ) && is_writable( TTDFP_DIR.DS. $this->plugin_domain ) )
				$this->rmdirr( TTDFP_DIR.DS.'cache' );
		}	
	}
	
	/**
	 * deactivate function/hook cleans up after the plugin
	 *
	 * @return void
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	public function deactivate()
	{ }
	
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
		
		$id = $_GET[ $this->get_option('url-key') ];
		
		if ( isset( $id )) {
			
			// Sanatize url var.
			$id = intval( $id );
			if( $id <= 0 ){ return; }
			
			if(!is_user_logged_in()){
				auth_redirect();
				exit;
			}
				
			$this->return_file( $id );
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
		
		$file_data     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}posts WHERE id=%d", $id ));
		$file_location = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id=%d AND meta_key='_wp_attached_file'", $id ));
		
		$file_path = $upload_folder . $file_location;
		
		$file_name = explode( DS , $file_location );
		$file_name = $file_name[( count($file_name)-1 )];
				
		if ( file_exists( $file_path ) && is_readable( $file_path ) && is_file( $file_path ) ) {
			header( 'Content-type: '.$file_data->post_mime_type );
			header( "HTTP/1.0 200 OK" );
			header( "Content-Disposition: attachment; filename=\"" . $file_name ."\"");
		    header( 'Content-length: '. (string)(filesize( $file_path )) );
			$file = @ fopen($file_path, 'rb');
		    if ( $file ) {
	        	fpassthru( $file );
	        	exit;
	      	}
		}else{
			return;
		}	
		return;
	}
	
	
	/**
	 * Intercepts file request and indexes and authenticates before returning file
	 *
	 * @return void
	 * @author Geraint Palmer
	 * @since 0.1
	 **/
	public function return_proxy_link($atts, $content = '')
	{	
		global $wpdb;
		
		extract(shortcode_atts(array(
				'id' => '0',
				'alt' => '',
				'type' => 'link',
			), $atts));
			
		$id = intval($id);
		$file_name = $wpdb->get_var( $wpdb->prepare( "SELECT guid FROM {$wpdb->prefix}posts WHERE id=%d", $id ));
		$file_name = explode( DS , $file_name );
		$file_name = $file_name[( count($file_name)-1 )];
	
		
		$title = empty($content) ? $file_name : $content ;
		$link = $this->generate_url($id);
		
		//if( !is_user_logged_in() )
		//	$title = $title . " - Login to download this file.";
		switch( $type )
		{	
			case 'url':
				echo $link;
				break;
				
			case 'link':
			default:
				echo "<a href='{$link}' alt='{$alt}'>{$title}</a>";
				break;
		}
	}
	
	public function return_proxy_url($atts, $content = '')
	{	
		global $wpdb;
		$id = intval($content);
		
		$link = $this->generate_url($id);
		echo $link;
	}
		
	
	/**
	 * Constructs the correct Download URI
	 *
	 * @return String
	 * @author Geraint Palmer
	 * @since 0.5
	 **/
	public function generate_url($id)
	{
		global $wp_rewrite;
		
		$link =  get_bloginfo('url') .'/index.php?'. $this->options->get_option('url-key') .'='. $id;
		
		if ( $this->get_option('permalinks') == 'on' ) 
		{	
			if( $wp_rewrite->using_permalinks() )
				$link =  get_bloginfo('url') .'/'. $this->get_option('url-key') .'/'. $id ."/";
			else if( $wp_rewite->using_index_permalinks() )
				$link =  get_bloginfo('url') .'/index.php/'. $this->get_option('url-key') .'/'. $id ."/";
		}
		return $link;
	}
}
?>