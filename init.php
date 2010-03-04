<?php
	/*
	Plugin Name: File Proxy	
	Version: 0.5
	Description: File Proxy is a simple WordPress plug that lest you protect / restrict access to a specific embedded file.  It lets you embed files from the upload directory into a post or page using a short code that restricts access to registered users.  guest users who click on the link are prompted to login before returning the file.<code>[file-proxy id='attachment_id']link text[/file-proxy]</code>.
	Author: Geraint Palmer
	Author URI: http://www.twothirdsdesign.co.uk/
	Plugin URI: http://www.twothirdsdesign.co.uk/wordpress/plugin/2010/02/file-proxy/
	*/
	
	/* Version check */
	global $wp_version;
	
	$exit_msg= __('ttd-file-proxy requires WordPress 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>');
	if ( version_compare( $wp_version , "2.8" , "<" ) ) exit ($exit_msg);

	// Plugin Variables
	define( 'TTDPF_VERSION', "0.5" );
	
	// Define URL
	define( 'TTDFP_URL', trailingslashit( WP_PLUGIN_URL ) . basename( dirname(__FILE__) ) .'/' );   
	
	// Define constant paths (PHP files)
	define( 'TTDFP_DIR', dirname( __FILE__)  );
	define( 'TTDFP_PLUGIN_FILE', __FILE__    );
	
	// Another plugin / ttd plugin may have defined this!!
	!defined('DS') ? define( 'DS', DIRECTORY_SEPARATOR ) : NULL;
	
	if ( DS != DIRECTORY_SEPARATOR ) {
		exit('Constant conflict: the constant DS is defined and not equal to the DIRECTORY_SEPARATOR');
	}
	
	define( 'TTDFP_LIB'   	, TTDFP_DIR.DS.'com'.DS.'twothirdsdesign' );
	define( 'TTDFP_CORE'   	, TTDFP_LIB.DS.'core' );
	define( 'TTDFP_ADMIN'   , TTDFP_LIB.DS.'file-proxy'.DS.'admin' );
	define( 'TTDFP_LANG'    , TTDFP_DIR.DS.'lang'  );
	
	define( 'TTDFP_INCLUDES' , TTDFP_DIR.DS.'includes' );

	
	if ( class_exists('TtdFileProxy') )
	{
		deactivate_plugins( TTDFP_PLUGIN_FILE );
    	exit('This plugin can not be installed as there is a class name conflict.');
	} 
	else 	
	{
		if( !class_exists('TtdPluginClass') )
			require_once( TTDFP_CORE.DS.'ttd_plugin_class.php' );
		if( !class_exists('GcpOptions') )
			require_once( TTDFP_CORE.DS.'gcp_options.php' );
		
		require_once( TTDFP_LIB.DS.'file-proxy'.DS.'ttd_file_proxy.php' );
		
		// Create Plugin Instance
		$ttd_file_proxy = new TtdFileProxy();
	}	
?>