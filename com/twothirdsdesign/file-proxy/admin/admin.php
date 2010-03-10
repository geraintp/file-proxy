<?php
/**
 * Ttd File Proxy - Admin Settings Plugin Class File
 *
 * @return void
 * @author Geraint Palmer 
 */
class TtdFileProxyAdmin
{
	protected $m;
	protected $menu_parent 			= 'options-general.php';
	protected $setting_identifier 	= 'ttd_file_proxy';
	protected $msg;
	
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
		
		/* Adds file proxy button to the upload manager */
		add_filter( 'attachment_fields_to_edit', array(&$this, 'upload_form_filter'), 999, 2 );
	}
	
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
		
		/* Register the default theme settings meta boxes. */
		add_action( "load-{$this->settings_page}", array(&$this, 'create_settings_meta_boxes') );
	
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
	?>
<script type="text/javascript"> 
//<![CDATA[
jQuery(document).ready( function() {
	jQuery('.on_off :checkbox').iphoneStyle();
	jQuery('#url-key-feild').hide();
});

function editUrlKey(){
	jQuery('#url-key-feild').toggle();
	jQuery('#editable-post-name').toggle();
	var text = jQuery('#url-key-feild').val();
	jQuery('#editable-post-name').text(text);
}
//]]>
</script> 
	<?php
	}
   
	/**
	 * Creates the default meta boxes for the theme settings page. Child theme and plugin developers
	 * should use add_meta_box() to create additional meta boxes.
	 *
	 * @since 0.7
	 * @global string $hybrid The global theme object.
	 */
	function create_settings_meta_boxes() {
		global $hybrid;
	
		/* Get theme information. */
		$theme_data = get_theme_data( TEMPLATEPATH . '/style.css' );
	
		/* Adds the About box for the parent theme. */
		add_meta_box( "file-proxy-about-meta-box", __( 'File Proxy', $this->domain ), array(&$this, 'about_meta_box'), $this->settings_page, 'normal', 'high' );
		/* Creates a meta box for the general theme settings. */
		add_meta_box( "file-proxy-general-meta-box", __( 'General Settings', $this->domain ), array(&$this, 'general_settings_meta_box'), $this->settings_page, 'normal', 'high' );
		add_meta_box( "file-proxy-advanced-meta-box", __( 'Advanced Settings', $this->domain ), array(&$this, 'advanced_settings_meta_box'), $this->settings_page, 'advanced', 'high' );
	
		/* Creates a meta box for the footer settings. */
		//add_meta_box( "{$prefix}-footer-settings-meta-box", __( 'Footer settings', $domain ), 'hybrid_footer_settings_meta_box', $hybrid->settings_page, 'normal', 'high' );
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
			else if( $_GET['opt'] == "reset" ){
				delete_option( $this->m->get_options_key() );
				$this->m->flush_options();
				$this->m->update_option("version", TTDPF_VERSION);
				wp_redirect( $this->get_settings_link() );
			}
		}
	}

	/**
	 * Displays the plugin settings page and calls do_meta_boxes() to allow additional settings
	 * meta boxes to be added to the page.
	 *
	 * @since 0.5
	 */
	function render_settings_page() {
		
		require_once 'settings_page.php';
		if(false) {?>
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

	</div><!-- .wrap --> <?php
		}
	}
	
	
	/**
	 * Displays the plugin settings page and calls do_meta_boxes() to allow additional settings
	 * meta boxes to be added to the page.
	 *
	 * @since 0.5
	 */
	
	function about_meta_box() {  ?>
	
		<table class="form-table">
			<tr>
				<th><?php _e( 'Author:', $this->domain ); ?></th>
				<td><a href="http://wordpress.org/extend/plugins/file-proxy/" title="Geraint Palmer">Geraint Palmer</a></td>
			</tr>
			<tr>
				<th><?php _e( 'Description:', $this->domain ); ?></th>
				<td>File Proxy is a simple WordPress plug that lest you protect / restrict access to a specific embedded file.  It lets you embed files from the upload directory into a post or page using a short code that restricts access to registered users.  guest users who click on the link are prompted to login before returning the file.<code>[file-proxy id='attachment_id']link text[/file-proxy]</code>.</td>
			</tr>
            <tr>
				<th><?php _e( 'Version:', $this->domain ); ?></th>
				<td><?php echo $this->m->get_option("version", 0 );?></td>
			</tr>
			<tr>
				<th><?php _e( 'Support:', $this->domain ); ?></th>
				<td><a href="http://wordpress.org/tags/file-proxy/" title="Support Forum">Support Forum</a></td>
			</tr>
		</table><!-- .form-table --><?php
	}

	
	
	/**
	 * Displays the plugin settings page and calls do_meta_boxes() to allow additional settings
	 * meta boxes to be added to the page.
	 *
	 * @since 0.5
	 */
	
	function general_settings_meta_box() { 
		global $wp_rewrite;
		
		$url = $this->m->generate_url( 0 );
		$url = explode( $this->m->get_option('url-key'), $url);
	?>

	<table class="form-table">
		<?php if($this->m->get_option('permalinks') != "disabled"): ?>
		<tr>
			<th><label for="permalinks"><?php _e( 'Use Permalinks:', $this->domain ); ?></label></th>
			<td>
            	<?php if($wp_rewrite->using_permalinks()): ?>
            	<div class="on_off">
					<input id="permalinks" name="permalinks" type="checkbox" <?php if($this->m->get_option("permalinks") == 'on') echo "checked=checked" ?> value="true" />				
				</div>
                <div class="helptext">
                    <label for="permalinks"><?php _e( "Uses permalink urls", $this->domain ); ?></label> 
                </div>
                <?php else: ?>
                	<span id="change-permalinks"><a href="options-permalink.php" class="button" target="_blank">Change Permalinks</a></span>
				<?php endif; ?>
			</td>
		</tr>
       	<?php endif; ?>
		<tr>
			<th><label for="url-key"><?php _e( 'Url Key:', $this->domain ); ?></label></th>
			<td>
				<span id="sample-url-key"><?php echo $url[0] ?><input id="url-key-feild" name="url-key" value="<?php echo $this->m->get_option('url-key') ?>" type="text"><span id="editable-post-name" class="hide-if-no-js" title="Click to edit this part of the permalink"><?php echo $this->m->get_option('url-key') ?></span><?php echo $url[1]; ?></span> 
				
				<span id="edit-slug-buttons"><a href="#post_name" class="edit-slug button hide-if-no-js" onclick="editUrlKey(); return false;">edit</a></span>
				<br/>
				<label for="url-key"><?php printf( __("Change the url your file are referenced through, ie %surl-key%s", $this->domain ), $url[0], $url[1] ); ?></label>
			</td>
		</tr>
	</table><!-- .form-table --><?php
	}
	
	
	/**
	 * Displays the plugin settings page and calls do_meta_boxes() to allow additional settings
	 * meta boxes to be added to the page.
	 *
	 * @since 0.6
	 */
	
	function advanced_settings_meta_box() {  ?>
		<table class="form-table">
	        <?php if($this->m->get_option('cache') != "disabled"): ?>
			<tr>
				<th><label for="cache"><?php _e( 'Caching:', $this->domain ); ?></label></th>
				<td>
		            <?php if($this->m->get_option('cache') != "disabled"): ?>
					<div class="on_off">
						<input id="cache" name="cache" type="checkbox" <?php if ( $this->m->get_option('cache') == "on" ) echo 'checked="checked"'; ?> value="true" /> 
					</div>            
					<label for="cache"><?php _e( 'This setting is not yet used.', $this->domain ); ?></label>
	                <?php else : ?>
					<label for="cache"><?php _e( 'Error: Caching Disabled, can not write to file system.', $this->domain ); ?></label>
	         		<?php endif; ?>
				</td>
			</tr>
	        <?php endif; ?>
			<tr>
				<th><label for="uninstall"><?php _e( 'Uninstall:', $this->domain ); ?></label></th>
				<td>
	            	<div class="on_off danger">
						<input id="uninstall" name="uninstall" type="checkbox" <?php if((boolean)$this->m->get_option("uninstall")) echo "checked=checked" ?> value="true" />				
					</div>
	                <div class="helptext">
	                    <label for="uninstall"><?php _e( "This should be \"<strong><em>OFF</em></strong>\" unless you want to permenantly delete this plugin.", $this->domain); ?><br/> 
						<?php if((boolean)$this->m->get_option("uninstall")) _e( "All information and settings stored by this plugin will be deleted <strong>when the delete button on the plugin page is select.</strong>", $this->domain ); ?></label> 
	                </div>
				</td>
			</tr>
		</table><!-- .form-table --><?php 
	}
}
?>