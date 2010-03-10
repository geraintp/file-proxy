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
//AJAX Upload
?>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/functions/js/ajaxupload.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
    
    var flip = 0;
	jQuery('#ttd_file_proxy_container #content').width(595);
	jQuery('#ttd_file_proxy_container .group').add('#ttd_file_proxy_container .group h2').hide();
        
    jQuery('#expand_options').click(function(){
        if(flip == 0){
            flip = 1;
            jQuery('#ttd_file_proxy_container #ttd-nav').hide();
            jQuery('#ttd_file_proxy_container #content').width(755);
            jQuery('#ttd_file_proxy_container .group').add('#ttd_file_proxy_container .group h2').show();

            jQuery(this).text('[-]');
            
        } else {
            flip = 0;
            jQuery('#ttd_file_proxy_container #ttd-nav').show();
            jQuery('#ttd_file_proxy_container #content').width(595);
            jQuery('#ttd_file_proxy_container .group').add('#ttd_file_proxy_container .group h2').hide();
            jQuery('#ttd_file_proxy_container .group:first').show();
            jQuery('#ttd_file_proxy_container #ttd-nav li').removeClass('current');
            jQuery('#ttd_file_proxy_container #ttd-nav li:first').addClass('current');
            
            jQuery(this).text('[+]');
        
        }
    
    });
    
        jQuery('.group').hide();
        jQuery('.group:first').fadeIn();
        jQuery('.ttd-radio-img-img').click(function(){
            jQuery(this).parent().parent().find('.ttd-radio-img-img').removeClass('ttd-radio-img-selected');
            jQuery(this).addClass('ttd-radio-img-selected');
            
        });
        jQuery('.ttd-radio-img-label').hide();
        jQuery('.ttd-radio-img-img').show();
        jQuery('.ttd-radio-img-radio').hide();
        jQuery('#ttd-nav li:first').addClass('current');
        jQuery('#ttd-nav li a').click(function(evt){
        
                jQuery('#ttd-nav li').removeClass('current');
                jQuery(this).parent().addClass('current');
                
                var clicked_group = jQuery(this).attr('href');
 
                jQuery('.group').hide();
                
                    jQuery(clicked_group).fadeIn();

                evt.preventDefault();
                
            });
        
        if('<?php if(isset($_REQUEST['reset'])) { echo $_REQUEST['reset'];} else { echo 'false';} ?>' == 'true'){
            
            var reset_popup = jQuery('#ttd-popup-reset');
            reset_popup.fadeIn();
            window.setTimeout(function(){
                   reset_popup.fadeOut();                        
                }, 2000);
                //alert(response);
            
        }
            
    //Update Message popup
    jQuery.fn.center = function () {
        this.animate({"top":( jQuery(window).height() - this.height() - 200 ) / 2+jQuery(window).scrollTop() + "px"},100);
        this.css("left", 250 );
        return this;
    }

    
    jQuery('#ttd-popup-save').center();
    jQuery('#ttd-popup-reset').center();
    jQuery(window).scroll(function() { 
    
        jQuery('#ttd-popup-save').center();
        jQuery('#ttd-popup-reset').center();
    
    });
    
    

    //AJAX Upload
    jQuery('.image_upload_button').each(function(){
    
    var clickedObject = jQuery(this);
    var clickedID = jQuery(this).attr('id');	
    new AjaxUpload(clickedID, {
          action: '<?php echo admin_url("admin-ajax.php"); ?>',
          name: clickedID, // File upload name
          data: { // Additional data to send
                action: 'ttd_ajax_post_action',
                type: 'upload',
                data: clickedID },
          autoSubmit: true, // Submit file after selection
          responseType: false,
          onChange: function(file, extension){},
          onSubmit: function(file, extension){
                clickedObject.text('Uploading'); // change button text, when user selects file	
                this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
                interval = window.setInterval(function(){
                    var text = clickedObject.text();
                    if (text.length < 13){	clickedObject.text(text + '.'); }
                    else { clickedObject.text('Uploading'); } 
                }, 200);
          },
          onComplete: function(file, response) {
           
            window.clearInterval(interval);
            clickedObject.text('Upload Image');	
            this.enable(); // enable upload button
            
            // If there was an error
            if(response.search('Upload Error') > -1){
                var buildReturn = '<span class="upload-error">' + response + '</span>';
                jQuery(".upload-error").remove();
                clickedObject.parent().after(buildReturn);
            
            }
            else{
                var buildReturn = '<img class="hide ttd-option-image" id="image_'+clickedID+'" src="'+response+'" width="300" alt="" />';
//					var buildReturn = '<img class="hide" id="image_'+clickedID+'" src="<?php bloginfo('template_url') ?>/thumb.php?src='+response+'&w=345" alt="" />';
                jQuery(".upload-error").remove();
                jQuery("#image_" + clickedID).remove();	
                clickedObject.parent().after(buildReturn);
                jQuery('img#image_'+clickedID).fadeIn();
                clickedObject.next('span').fadeIn();
                clickedObject.parent().prev('input').val(response);
            }
          }
        });
    
    });
    
    //AJAX Remove (clear option value)
    jQuery('.image_reset_button').click(function(){
    
            var clickedObject = jQuery(this);
            var clickedID = jQuery(this).attr('id');
            var theID = jQuery(this).attr('title');	

            var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
        
            var data = {
                action: 'ttd_ajax_post_action',
                type: 'image_reset',
                data: theID
            };
            
            jQuery.post(ajax_url, data, function(response) {
                var image_to_remove = jQuery('#image_' + theID);
                var button_to_hide = jQuery('#reset_' + theID);
                image_to_remove.fadeOut(500,function(){ jQuery(this).remove(); });
                button_to_hide.fadeOut();
                clickedObject.parent().prev('input').val('');
                
                
                
            });
            
            return false; 
            
        });   	 	



    //Save everything else
    jQuery('#ttdform').submit(function(){
        
            function newValues() {
              var serializedValues = jQuery("#ttdform").serialize();
              return serializedValues;
            }
            jQuery(":checkbox, :radio").click(newValues);
            jQuery("select").change(newValues);
            jQuery('.ajax-loading-img').fadeIn();
            var serializedReturn = newValues();
             
            var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
        
             //var data = {data : serializedReturn};
            var data = {
                <?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'ttdthemes_framework_settings'){ ?>
                type: 'framework',
                <?php } ?>
                action: 'ttd_ajax_post_action',
                data: serializedReturn
            };
            
            jQuery.post(ajax_url, data, function(response) {
                var success = jQuery('#ttd-popup-save');
                var loading = jQuery('.ajax-loading-img');
                loading.fadeOut();  
                success.fadeIn();
                window.setTimeout(function(){
                   success.fadeOut(); 
                   
                                        
                }, 2000);
            });
            
            return false; 
            
        });   	 	
        
    });
</script>