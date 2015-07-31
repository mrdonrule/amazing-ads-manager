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
(function() {
  tinymce.PluginManager.add('amads_tc_button', function( editor, url ) {
		
        editor.addButton( 'amads_tc_button', {
			title : 'Amazing Ads Manager',
             icon: 'icon amads-own-icon',
			 onclick: function() {
				 
    editor.windowManager.open( {
		
        title: 'Amazing Ads Manager',
		width  : 320,
        height : 120,
       inline : 1,
        body: [
        {
            type: 'listbox',
			name: 'level', 
            label: 'Select Ads',  
            values: amadsData, 
        }],
		  onsubmit: function( e ) {
            editor.insertContent(e.data.level);
        }
    });
			 }
		})
	})
})();