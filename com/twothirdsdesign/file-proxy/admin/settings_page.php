<div class="wrap" id="woo_container">
<div id="woo-popup-save" class="woo-save-popup"><div class="woo-save-save">Options Updated</div></div>
<div id="woo-popup-reset" class="woo-save-popup"><div class="woo-save-reset">Options Reset</div></div>
    <?php // <form method="post"  enctype="multipart/form-data"> ?>
    <form action="" enctype="multipart/form-data" id="wooform">
        <div id="header">
            <div class="logo"><img alt="WooThemes" src="<?php echo bloginfo('template_url'); ?>/functions/images/logo.png"/></div>
            <div class="theme-info">
                <span class="theme"><?php echo $themename; ?> <?php echo $local_version; ?></span>
                <span class="framework">Framework <?php echo get_option('woo_framework_version'); ?></span>
            </div>
            <div class="clear"></div>
        </div>
        <div id="support-links">
   
            <ul>
                <li class="changelog"><a title="Theme Changelog" href="<?php echo $manualurl; ?>#Changelog">View Changelog</a></li>
                <li class="docs"><a title="Theme Documentation" href="<?php echo $manualurl; ?>">View Themedocs</a></li>
                <li class="forum"><a href="http://forum.woothemes.com" target="blank">Visit Forum</a></li>
                <li class="right"><img style="display:none" src="<?php echo bloginfo('template_url'); ?>/functions/images/loading-top.gif" class="ajax-loading-img ajax-loading-img-top" alt="Working..." /><a href="#" id="expand_options">[+]</a> <input type="submit" value="Save All Changes" class="button submit-button" /></li>
            </ul>
   
        </div>
       
        <div id="main">
            <div id="woo-nav">
                <ul>
                    <li class="current"><a href="#generaloptions">General Options</a></li>
                    <li class=""><a href="#importoptions">Import Options</a></li>
                    <li class=""><a href="#exportoptions">Export Options</a></li>
                </ul>       
            </div>
            <div id="content">
                <div id="generaloptions" class="group" style="display: block;">
                    <h2>Framework Options</h2>
                    <div class="section section-checkbox">
                        <h3 class="heading">Disable options panel ouput option</h3>
                        <div class="option">
                            <div class="controls">
                            <input id="woo_show_options" name="woo_show_options" class="checkbox woo-input" type="checkbox" value="true" <?php if(get_option('woo_show_options') == 'true' ) { echo 'checked=""'; } ?>/>
                            <br/>
                            </div>
                            <div class="explain">
                                Disable the ability to show your Woo Options. You can view the themes option by URL e.g. http://yoursite.com/?options=woo
                            </div>
                            <div class="clear"></div>
                          </div>
                       
                       </div>
                       <div class="section section-checkbox">
                        <h3 class="heading">Theme Version Checker</h3>
                       <div class="option">
                            <div class="controls">
                            <input id="woo_theme_version_checker" name="woo_theme_version_checker" class="checkbox woo-input" type="checkbox" value="true" <?php if(get_option('woo_theme_version_checker') == 'true' ) { echo 'checked=""'; } ?>/>
                            <br/>
                            </div>
                            <div class="explain">
                                This will enable notices on your theme options page that there is an update available for your theme.
                            </div>
                            <div class="clear"></div>
                         </div>
                        </div>
                       <div class="section section-checkbox">
                        <h3 class="heading">Disable Buy Themes Tab</h3>
                       <div class="option">
                            <div class="controls">
                            <input id="woo_buy_themes" name="woo_buy_themes" class="checkbox woo-input" type="checkbox" value="true" <?php if(get_option('woo_buy_themes') == 'true' ) { echo 'checked=""'; } ?>/>
                            <br/>
                            </div>
                            <div class="explain">
                                This disables the "Buy Themes" tab. This page lists latest availbe themes from the WooThemes.com website.
                            </div>
                            <div class="clear"></div>
                         </div>
                        </div>
                        <div class="section section-checkbox">
                        <h3 class="heading">Framework Core update (BETA)</h3>
                       <div class="option">
                            <div class="controls">
                            <input id="woo_framework_update" name="woo_framework_update" class="checkbox woo-input" type="checkbox" value="true" <?php if(get_option('woo_framework_update') == 'true' ) { echo 'checked=""'; } ?>/>
                            <br/>
                            </div>
                            <div class="explain">
                                <strong>BETA:</strong> This option will active the WooFramework core update. Please use with caution. If you get an error when going to the Framework Update page please ensure you have added <code>require_once ($functions_path . 'admin-framework-update.php');</code> to your theme's <code>functions.php</code> file.
                            </div>
                            <div class="clear"></div>
                            </div>
                        </div>
                        </div>
                
                 <div id="importoptions" class="group" style="display: block;">
                    <h2>Import Options</h2>
                    <div class="section">
                        <h3 class="heading">Import options from another WooThemes instance.</h3>
                        <div class="option">
                            <div class="controls">
                            <textarea rows="8" cols="" id="woo_import_options" name="woo_import_options" class="woo-input"></textarea>
                            <br/>
                            </div>
                            <div class="explain">
                                You can transfer options from another WooThemes (same theme) to this one by copying the export code and adding it here. Works best if it's imported from identical themes.
                            </div>
                            <div class="clear"></div>
                            </div>
                        </div>
                  </div>
                  <div id="exportoptions" class="group" style="display: block;">
                     <h2>Export Options</h2>
                     <div class="section">
                        <h3 class="heading">Use the code below to export this themes settings to another theme.</h3>
                        <div class="option">
                            <div class="controls">
                            <?php
                            //Create, Encrypt and Update the Saved Settings
                            global $wpdb;
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
                           
                            foreach ($results as $result){
                           
                                    $output[$result->option_name] = $result->option_value;
                           
                            }
                            $output = serialize($output);
                            ?>
                            <textarea rows="8" cols="" class="woo-input"><?php echo base64_encode($output); ?></textarea>
                            <br/>
                            </div>
                            <div class="explain">
                                You can transfer options from another WooThemes (same theme) to this one by copying the export code and adding it here. Works best if it's imported from identical themes.
                            </div>
                            <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                    </div>
                <div class="clear"></div>
         
           
        </div>
        <div class="save_bar_top">
        <img style="display:none" src="<?php echo bloginfo('template_url'); ?>/functions/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
        <input type="submit" value="Save All Changes" class="button submit-button" />       
        </form>
         <?php /*
        <form action="<?php echo wp_specialchars( $_SERVER['REQUEST_URI'] ) ?>" method="post" style="display:inline" id="wooform-reset">
            <span class="submit-footer-reset">

            <input name="reset" type="submit" value="Reset Options" class="button submit-button reset-button" onclick="return confirm('Click OK to reset. Any settings will be lost!');" />
            <input type="hidden" name="woo_save" value="reset" />
            </span>
        </form>
        */ ?>
       
        </div>
        <?php  // echo $update_message; ?>   
        <?php  //wp_nonce_field('reset_options'); echo "\n"; // Legacy ?>


<div style="clear:both;"></div>   
</div><!--wrap-->