       
        <div id="main">
            <div id="ttd-nav" class="hide-if-no-js">
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
                            <div class="controls on_off">
                            <input id="ttd_show_options" name="ttd_show_options" class="checkbox ttd-input" type="checkbox" value="true" <?php if(get_option('ttd_show_options') == 'true' ) { echo 'checked=""'; } ?>/>
                            <br/>
                            </div>
                            <div class="explain">
                                Disable the ability to show your ttd Options. You can view the themes option by URL e.g. http://yoursite.com/?options=ttd
                            </div>
                            <div class="clear"></div>
                          </div>
                       
                       </div>
                       <div class="section section-checkbox">
                        <h3 class="heading">Theme Version Checker</h3>
                       <div class="option">
                            <div class="controls on_off danger">
                            <input id="ttd_theme_version_checker" name="ttd_theme_version_checker" class="checkbox ttd-input" type="checkbox" value="true" <?php if(get_option('ttd_theme_version_checker') == 'true' ) { echo 'checked=""'; } ?>/>
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
                            <input id="ttd_buy_themes" name="ttd_buy_themes" class="checkbox ttd-input" type="checkbox" value="true" <?php if(get_option('ttd_buy_themes') == 'true' ) { echo 'checked=""'; } ?>/>
                            <br/>
                            </div>
                            <div class="explain">
                                This disables the "Buy Themes" tab. This page lists latest availbe themes from the ttdThemes.com website.
                            </div>
                            <div class="clear"></div>
                         </div>
                        </div>
                        <div class="section section-checkbox">
                        <h3 class="heading">Framework Core update (BETA)</h3>
                       <div class="option">
                            <div class="controls">
                            <input id="ttd_framework_update" name="ttd_framework_update" class="checkbox ttd-input" type="checkbox" value="true" <?php if(get_option('ttd_framework_update') == 'true' ) { echo 'checked=""'; } ?>/>
                            <br/>
                            </div>
                            <div class="explain">
                                <strong>BETA:</strong> This option will active the ttdFramework core update. Please use with caution. If you get an error when going to the Framework Update page please ensure you have added <code>require_once ($functions_path . 'admin-framework-update.php');</code> to your theme's <code>functions.php</code> file.
                            </div>
                            <div class="clear"></div>
                            </div>
                        </div>
                        </div>
                
                 <div id="importoptions" class="group" style="display: block;">
                    <h2>Import Options</h2>
                    <div class="section">
                        <h3 class="heading">Import options from another ttdThemes instance.</h3>
                        <div class="option">
                            <div class="controls">
                            <textarea rows="8" cols="" id="ttd_import_options" name="ttd_import_options" class="ttd-input"></textarea>
                            <br/>
                            </div>
                            <div class="explain">
                                You can transfer options from another ttdThemes (same theme) to this one by copying the export code and adding it here. Works best if it's imported from identical themes.
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
                            $query = "SELECT * FROM $wpdb->options WHERE option_name LIKE 'ttd_%' AND
                                        option_name != 'ttd_options' AND
                                        option_name != 'ttd_template' AND
                                        option_name != 'ttd_custom_template' AND
                                        option_name != 'ttd_settings_encode' AND
                                        option_name != 'ttd_export_options' AND
                                        option_name != 'ttd_import_options' AND
                                        option_name != 'ttd_framework_version' AND
                                        option_name != 'ttd_manual' AND
                                        option_name != 'ttd_shortname'";
                           
                            $results = $wpdb->get_results($query);
                           
                            foreach ($results as $result){
                           
                                    $output[$result->option_name] = $result->option_value;
                           
                            }
                            $output = serialize($output);
                            ?>
                            <textarea rows="8" cols="" class="ttd-input"><?php echo base64_encode($output); ?></textarea>
                            <br/>
                            </div>
                            <div class="explain">
                                You can transfer options from another ttdThemes (same theme) to this one by copying the export code and adding it here. Works best if it's imported from identical themes.
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
        <form action="<?php echo wp_specialchars( $_SERVER['REQUEST_URI'] ) ?>" method="post" style="display:inline" id="ttdform-reset">
            <span class="submit-footer-reset">

            <input name="reset" type="submit" value="Reset Options" class="button submit-button reset-button" onclick="return confirm('Click OK to reset. Any settings will be lost!');" />
            <input type="hidden" name="ttd_save" value="reset" />
            </span>
        </form>
        */ ?>
       
        </div>
        <?php  // echo $update_message; ?>   
        <?php  //wp_nonce_field('reset_options'); echo "\n"; // Legacy ?>
