<?php

class TtdFileProxyAdmin
{
	protected $m;
	protected $menu_parent = 'options-general.php';
	protected $setting_identifier = 'ttd_file_proxy';
	
	function __construct( $mainReference )
	{
		$this->m = &$mainReference;
		$this->m->get_option("uninstall");
		
		$this->domain = $this->m->get_domain();
		
		add_action( 'init', array(&$this, 'init') );
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
	
		/* Save settings page meta boxes. */
		//add_action( "ttd_file_proxy_update_settings_page", 'ttd_file_proxy_settings' );
	
		/* Add a new meta box to the post editor. */
		//add_action( 'admin_menu', 'ttd_file_proxy_post_meta_box' );
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
		$ttd_file_proxy->settings_page =  add_submenu_page( $this->menu_parent, __('File Proxy Settings'), __('File Proxy') , '10', $this->setting_identifier, array(&$this, 'render_settings_page') );
		
		/* Register the default theme settings meta boxes. 
		add_action( "load-{$hybrid->settings_page}", 'hybrid_create_settings_meta_boxes' );*/
	
		/* Make sure the settings are saved. 
		add_action( "load-{$hybrid->settings_page}", 'hybrid_load_settings_page' );*/
	
		/* Load the JavaScript and stylehsheets needed for the theme settings. 
		add_action( "load-{$hybrid->settings_page}", 'hybrid_settings_page_enqueue_script' );
		add_action( "load-{$hybrid->settings_page}", 'hybrid_settings_page_enqueue_style' );
		add_action( "admin_head-{$hybrid->settings_page}", 'hybrid_settings_page_load_scripts' );*/
	}

	/**
	 * Displays the plugin settings page and calls do_meta_boxes() to allow additional settings
	 * meta boxes to be added to the page.
	 *
	 * @since 0.5
	 */
	function render_settings_page() { ?>
		<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div> 
		<h2><?php  _e( 'File Proxy Settings', $this->domain ); ?> <small>(<a href="<?php $this->settings_link(); ?>&amp;opt=reset"><?php _e('Reset', $this->domain ); ?></a>)</small></h2>

		<?php if ( 'true' == esc_attr( $_GET['updated'] ) ) echo '<p class="updated fade below-h2" style="padding: 5px 10px;"><strong>' . __( 'Settings saved.', $this->domain ) . '</strong></p>'; ?>

		<div id="poststuff">

			<form method="post" action="<?php $this->settings_link(); ?>">

				<?php wp_nonce_field( "ttd-file-proxy-settings-page" ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div class="metabox-holder">
					<div class="post-box-container column-1 normal"><?php do_meta_boxes( $hybrid->settings_page, 'normal', $theme_data ); ?></div>
					<div class="post-box-container column-2 advanced"><?php do_meta_boxes( $hybrid->settings_page, 'advanced', $theme_data ); ?></div>
					<div class="post-box-container column-3 side"><?php do_meta_boxes( $hybrid->settings_page, 'side', $theme_data ); ?></div>
                    <?php echo $this->m->get_option("cache", 'uho spaggetti ohhs'); ?>
				</div>

				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="<?php _e( 'Update Settings', $domainthis->domain); ?>" />
					<input type="hidden" name="<?php echo "ttd_file_proxy_submit_hidden"; ?>" value="Y" />
				</p><!-- .submit -->

			</form>

		</div><!-- #poststuff -->

	</div><!-- .wrap --> <?php
	}
	
	function settings_header(){
		
	}
}
?>