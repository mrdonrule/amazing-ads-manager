	/*	
	*	Amazing Ads Manager BackEnd JavaScript File
	*	---------------------------------------------------------------------
	* 	@version	0.0.4
	* 	Author: Amazing Themes
	*	Author URI: http://naijadomains.com/amazing-themes/
	*	---------------------------------------------------------------------
	*	This file contatin backedn style sheet for Amazing Ads Manager
	*	---------------------------------------------------------------------
	*/

jQuery.noConflict();
(function ($) {
  aMads = {
	  	  widget:function(){
			  $('.amads_select').live('change',function(){
				if($(this).val()=='s_specific_ad'){
					$(".amads-by-ids").show();
				}
				else { 
					$(".amads-by-ids").hide();
				}
				 
			  });
			 $('.amad_upload').on('click', function(e) {
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
	 jQuery('.amads-img-url').val(attachment.url);
    });
  });

  amads_uploader_frame.open();

  });
		 
	  },
	  Postype:function(){
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
  };
  $(document).ready(function () {
  if(pagenow=="amadsmananger"){
		aMads.Postype();
	}
	if(pagenow=="widgets"){
  		aMads.widget();
	}
  })
})(jQuery);

