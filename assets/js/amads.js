	/*	
	*	Amazing Ads Manager BackEnd JavaScript File
	*	---------------------------------------------------------------------
	* 	@version	0.0.2
	* 	Author: Amazing Themes
	*	Author URI: http://naijadomains.com/amazing-themes/
	*	---------------------------------------------------------------------
	*	This file contatin backedn style sheet for Amazing Ads Manager
	*	---------------------------------------------------------------------
	*/

jQuery.noConflict();
jQuery(document).ready(function ($) {
	
			if($("#ads-type").val().split('#')[0] == 'image'){
			$('#codes').hide();
			$('#image-ad-type').show();	
			
		}
		if($("#ads-type").val().split('#')[0] == 'codes'){
			$('#image-ad-type').hide();	
			$('#codes').show();	
		}
		$("#ads-type").change(function() {
		if($(this).val()=='image'){
			$('#codes').hide();
			$('#image-ad-type').show();	
			
		}
		if($(this).val()=='codes'){
			$('#image-ad-type').hide();	
			$('#codes').show();	
		}
	});
	if(amAds.wp_version=="wp_ver3_5_low"){
	
	jQuery('#amads-image-btn').click(function() {
    formfield = jQuery('#amads_image').attr('name');
    tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	 tbframe_interval = setInterval(function() { jQuery('#TB_iframeContent').contents().find('.savesend .button').val('Use as Ad'); }, 2000);
    return false;
});

window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#amads_image').val(imgurl);
 tb_remove();

 jQuery('#amads-image-preview').html("<img src='"+imgurl+"'/>");
}
	}
if(amAds.wp_version=="wp_ver3_5_up"){
$('#amads-image-btn').on('click', function(e) {
  var amads_uploader_frame;
    if ( amads_uploader_frame ) {
    amads_uploader_frame.open();
    return;
  }
  amads_uploader_frame = wp.media.frames.amads_uploader_frame = wp.media({
	    title: 'Select Images For Amazing Ads Manager',
        button: {
            text: 'Use Image as Ad'
        },
    multiple: false,
    library: {
      type: 'image'
    },
  });
   
  amads_uploader_frame.on('select', function(){
    var selection = amads_uploader_frame.state().get('selection');
    selection.map( function( attachment ) {
      attachment = attachment.toJSON();
	 jQuery('#amads_image').val(attachment.url);
     jQuery('#amads-image-preview').html("<img src='"+attachment.url+"'/>");
    });
  });

  amads_uploader_frame.open();

  });
}
    });
	
  