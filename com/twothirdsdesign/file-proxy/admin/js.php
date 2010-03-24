<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function(){
    
	jQuery('.on_off :checkbox').iphoneStyle();
    var flip = 0;
    
    jQuery('#expand_options').click(function(){
        
		jQuery('#ttd_file_proxy_container #ttd-nav').toggle();
		
		if(flip == 0){
            flip = 1;
           	jQuery('#ttd_file_proxy_container #content').width(755);
            jQuery('#ttd_file_proxy_container .group').add('#ttd_file_proxy_container .group h2').show();

            jQuery(this).text('[-]');
        } else {
            flip = 0;
            jQuery('#ttd_file_proxy_container #content').width(595);
            jQuery('#ttd_file_proxy_container .group').add('#ttd_file_proxy_container .group h2').hide();
            
			jQuery('#ttd_file_proxy_container .group:first').show();
            jQuery('#ttd_file_proxy_container #ttd-nav li').removeClass('current');
            jQuery('#ttd_file_proxy_container #ttd-nav li:first').addClass('current');
            
            jQuery(this).text('[+]');
        }
    });
	
	// converts to tab view. i.e. if js enabled
	jQuery('#ttd_file_proxy_container #content').width(595);
	jQuery('#ttd_file_proxy_container .group').add('#ttd_file_proxy_container .group h2').hide();
    
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
        
     if('<?php echo ( isset( $_REQUEST['ttd_file_proxy_submit_hidden'] ) && $_REQUEST['ttd_file_proxy_submit_hidden'] == 'reset' ) ? 'false' : 'true'; ?>' == 'true'){
         
         var reset_popup = jQuery('#ttd-popup-reset');
         reset_popup.fadeIn();
         window.setTimeout(function(){
                reset_popup.fadeOut();                  
             }, 2000); 
     }
            
    //Update Message popup
    jQuery.fn.center = function () {
        this.animate({"top":( jQuery(window).height() - this.height() - 200 ) / 2+jQuery(window).scrollTop() + "px"},100);
        this.css("left", 250 );
        return this;
    }

    // Center Messages
    jQuery('#ttd-popup-save').center();
    jQuery('#ttd-popup-fail').center();
	jQuery('#ttd-popup-reset').center();
	
    jQuery(window).scroll(function() { 
    
        jQuery('#ttd-popup-save').center();
        jQuery('#ttd-popup-fail').center();
		jQuery('#ttd-popup-reset').center();
    
    });
    
	// ajax submit
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
    
        var data = {
			action: 'ttd_file_proxy',
			data: serializedReturn,
			<?php if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'ttd_file_proxy'){ ?>
            type: 'settings',
            <?php } ?>
		};
		
        jQuery.post(ajax_url, data, function(response) {
            var success = jQuery('#ttd-popup-save');
			var fail = jQuery('#ttd-popup-fail');
            var loading = jQuery('.ajax-loading-img');
            loading.fadeOut();
  			if( response == 1 ){
            	success.fadeIn();
	            window.setTimeout(function(){
	               success.fadeOut();                    
            	}, 2000);
			}else{
				fail.fadeIn();
	            window.setTimeout(function(){
	               fail.fadeOut();                    
            	}, 2000);
			}
			//alert(response);
        });
        
        return false;   
    });
});
//]]>	
</script>