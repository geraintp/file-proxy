<?php
/**
 * TtdPluginClass
 *
 * @author		Geraint Palmer
 * @version 	1.0.1 	   
 */
	class TtdPluginAdminClass
	{
		const VERSION = '1.0.0';
		
		function __construct(){}
	
		/**
		 * Render the admin settings page content header
		 *
		 * @since 0.6
		 */
		function admin_header(){ ?>
	        <div class="wrap" id="ttd_file_proxy_container">
	        <div id="ttd-popup-save" class="ttd-save-popup"><div class="ttd-save-save">Options Updated</div></div>
			<div id="ttd-popup-fail" class="ttd-save-popup"><div class="ttd-save-fail">Update Failed</div></div>
	        <div id="ttd-popup-reset" class="ttd-save-popup"><div class="ttd-save-reset">Options Reset</div></div>
	        <?php // <form method="post"  enctype="multipart/form-data"> ?>
	        <form method="post" action="" enctype="multipart/form-data" id="ttdform">
				<?php wp_nonce_field('ttd-file-proxy'); ?>
	            <div id="header">
	                <div class="logo"><img alt="ttdThemes" src="<?php echo TTDFP_URL ?>assets/img/plugin-logo.png"/></div>
	                <div class="theme-info">
	                    <span class="theme"><?php _e('File-Proxy', $this->domain) ?></span>
	                    <span class="framework"><?php echo __('version', $this->domain) ." ". $this->m->get_option("version", 0 ); ?></span>
	                </div>
	                <div class="clear"></div>
	            </div>
	            <div id="support-links">

	                <ul>
	                    <li class="changelog"><a title="<?php _e('Changelog', $this->domain) ?>" href="<?php echo $manualurl; ?>#Changelog"><?php _e('View Changelog', $this->domain) ?></a></li>
	                    <li class="docs"><a title="<?php _e('Documentation', $this->domain) ?>" href="<?php echo $manualurl; ?>"><?php _e('View Plugin docs', $this->domain) ?></a></li>
	                    <li class="forum"><a href="http://wordpress.org/tags/file-proxy/" target="blank"><?php _e('Visit Forum', $this->domain) ?></a></li>
	                    <li class="right"><img style="display:none" src="<?php echo TTDFP_URL ?>assets/img/loading-top.gif" class="ajax-loading-img ajax-loading-img-top" alt="Working..." /><a href="#" id="expand_options" class='hide-if-no-js'>[+]</a> <input type="submit" value="<?php _e('Save All Changes', $this->domain) ?>" class="button submit-button" /></li>
	                </ul>

	            </div><?php 
		}


		/**
		 * Render the admin settings page content footer
		 *
		 * @since 0.6
		 */
		function admin_footer(){ ?>
				<div class="save_bar_top">
					<img style="display:none" src="<?php echo bloginfo('template_url'); ?>/functions/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
					<input type="submit" value="Save All Changes" class="button submit-button" />
					<input type="hidden" name="ttd_file_proxy_submit_hidden" value="Y" />
					<input type="hidden" name="ttd_file_proxy_submit_nonce" value="<?php echo wp_create_nonce('ttd-file-proxy'); ?>" />       
				</form>
						<form action="<?php echo wp_specialchars( $_SERVER['REQUEST_URI'] ) ?>" method="post" style="display:inline" id="ttdform-reset">
							<span class="submit-footer-reset">
								<?php wp_nonce_field('ttd-file-proxy-reset'); ?>
								<input name="reset" type="submit" value="Reset Options" class="button submit-button reset-button" onclick="return confirm('Click OK to reset. Any settings will be lost!');" />
								<input type="hidden" name="ttd_file_proxy_submit_hidden" value="reset" />
							</span>
						</form>
					</div>
				</div>

				<div style="clear:both;"></div>   
			</div><!--wrap--><?php
		}

		/**
		 * Render the admin settings page content
		 *
		 * @since 0.6
		 */
		function render_page( $panels )
		{
			$this->admin_header();
			$first = true; ?>
			<div id="main">
	            <div id="ttd-nav" class="hide-if-no-js">
	                <ul>
					<?php foreach ( $panels as $panel ): ?>
						<li <?php echo $first ? 'class="current"': ''; ?> ><a href="#<?php echo $panel['name'] ?>"><?php echo $panel['title']; $first = false; ?></a></li>
					<?php endforeach ?>
					</ul>
				</div>
				<div id="content" style="width: 755px;"><?php 
					foreach ($panels as $panel) {
						$this->render_panel($panel);
					} ?>
				</div>
				<div class="clear"></div>
			</div>
			<?php
			$this->admin_footer();
		}

		/**
		 * @since 0.6
		 */
		function render_panel( $panel )
		{ ?>
			<div id="<?php echo $panel['name'] ?>" class="group" style="display: block;">
	            <h2 style="display: block;"><?php echo $panel['title'] ?></h2>
	            <!-- option -->
				<?php foreach ( $panel['options'] as $option ) {
					if ( method_exists($this, "{$option['type']}" ) )
						call_user_func( array( $this, "{$option['type']}" ), $option, $this->m->get_option($option['name']) );
				} ?>

			</div><?php
		}


		function pre_field( $title, $type )
		{ ?>
			<div class="section section-<?php echo $type ?>">
	                <h3 class="heading"><?php echo $title ?></h3>
	                <div class="option"><?php	
		}
		

		function post_field( $desc )
		{	?>
	                <div class="explain">
	                    <?php echo $desc ?> 
	                </div>
	                <div class="clear"></div>
	            </div>
	        </div><?php 
		}

		/**
		 *
		 * @since 0.6
		 */	
		function checkbox( $args = array(), $value = false )
		{ 
			$this->pre_field( $args['title'], 'checkbox' );

			if( (string)$value != "disabled" ): ?>
	                    <div class="controls on_off <?php echo $args['class'] ?>">
	                        <input id="<?php echo $args['name'] ?>" name="<?php echo $args['name'] ?>" class="checkbox ttd-input" type="checkbox" value="true" <?php echo ( $value == "on" || $value == 1 ) ? 'checked="checked"' : ''; ?>/>
	                    	<br/>
	                    </div>
	        <?php endif;

			$this->post_field( $args['description'] );
		}


		/**
		 *
		 * @since 0.6
		 */	
		function textfield ( $args = array(), $value = '' )
		{ 
			$this->pre_field( $args['title'], 'text' ); ?>
					<div class="controls"> 
						<input class="ttd-input" name="<?php echo $args['name'] ?>" id="<?php echo $args['name'] ?>" type="text" value="<?php echo $value ?>" /><br/>
	                </div> <?php
			$this->post_field( $args['description'] );
		} 

		/**
		 *
		 *
		 * @since 0.6
		 */	
		function select ( $args = array(), $value = '' )
		{
			$this->pre_field( $args['title'], 'select' ); ?>  
					<div class="controls"> 
						<select class="ttd-input" name="<?php echo $args['name'] ?>" id="<?php echo $args['name'] ?>">
	                    	<?php foreach( $args['options'] as $option ): ?>
	                        <option <?php echo $option == $value ? 'selected="selected"' : '' ; ?>><?php echo $option ?></option>
	                        <?php endforeach; ?>
	                     </select><br/>
	                </div> <?php
			 $this->post_field( $args['description'] );
		}
	}
?>