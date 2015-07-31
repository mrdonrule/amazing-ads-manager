<?php
/*
Plugin Name: Amazing Ads Manager
Plugin URI: http://naijadomains.com/amazing-themes/plugin/adsManager/
Description: Amazing Ads Manager is easy to use plugin providing a flexible logic of displaying advertisements, Randomly and Customizable  display of advertisements on single post page or category archive page by category (categories) or custom post types. Amazing Ads Mnager includes all Google Adsense Display and Text Unit Sizes.
Version: 0.0.4
Author: Amazing Themes
@Compatibility WP 3.5+
Author URI: http://naijadomains.com/amazing-themes/
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
//Ads sizes array
$adsizes = array(
		#   Google Adsense Display and Text Unit Sizes
			'970x90'  => 'Large Leaderboard (970x90)', 
			'728x90'  => 'Leaderboard (728x90)',
			'468x60'  => 'Banner (468x60)',
			'336x280' => 'Large Rectangle (336x280)',
			'320x100' => 'Large Mobile Banner (320x100)',
			'320x50'  => 'Mobile Banner (320x50)',
			'300x600' => 'Large Skyscraper (300x600)',
			'300x250' => 'Medium Rectangle (300x250)',
			'250x250' => 'Square (250x250)',
			'234x60'  => 'Half Banner (234x60)',
			'200x200' => 'Small Square (200x200)',
			'180x150' => 'Small Rectangle (180x150)',
			'160x600' => 'Wide Skyscraper (160x600)',
			'125x125' => 'Button (125x125)',
			'120x600' => 'Skyscraper (120x600)',
			'120x240' => 'Vertical Banner (120x240)',
		
		#   Google Adsense Link Unit Sizes
			'728x15'  => 'Displays 4 links (728x15)',
			'468x15'  => 'Displays 4 links (468x15)',
			'200x90'  => 'Displays 3 links (200x90)',
			'180x90'  => 'Displays 3 links (180x90)',
			'160x90'  => 'Displays 3 links (160x90)',
			'120x90'  => 'Displays 3 links (120x90)',
		
		);
if(!class_exists('AmazingAds')) {
	class AmazingAds {
		var $adsizesm,$amads_option=array();
		//constants
		const CAPABILITY = 'manage_options';
		const VERSION = '0.0.4';
		const VERSION_FIELD_NAME = 'amAdsmanager_var';
		const post_type ="amAdsMananger";
		//Constructer
		public function __construct() {
			global $adsizes;
			$this->adsizes =$adsizes;
					

			//action hooks
			add_action( 'admin_init', array( &$this,'init_admin') );
			add_action( 'after_setup_theme', array( &$this, 'setup_amAds' ) );
			add_action('widgets_init',array(&$this,'amads_widget'));
			add_action( 'manage_posts_custom_column', array( &$this, 'posts_custom_column' ), 10, 2 );
			add_action( 'save_post', array( &$this,'update_custom_meta_fields' ) );
			add_action( 'add_meta_boxes', array( &$this, 'add_custom_box' ) );			
			add_action('admin_head',array(&$this,'getamadsData'));
			add_action('init',array(&$this,'front_style'));
			add_action( 'admin_menu', array( &$this,'add_amAds_sub_pages' ) );
			//filters
			add_filter( 'manage_amadsmananger_posts_columns', array( &$this, 'add_custom_columns' ) );
			add_filter('post_updated_messages', array( &$this, 'amadsmananger_updated_messages' ) );
			add_filter( "mce_external_plugins",  array( &$this, 'amads_add_tinymce_plugin' ) );
		    add_filter( 'mce_buttons',  array( &$this, 'amads_register_my_tc_button' ) );
			add_filter('the_content', array( &$this, 'amads_a_content' )); 
			add_filter('the_content', array( &$this, 'amads_b_content' )); 


			// shortcodes
			add_shortcode( 'amads',  array( &$this, 'amads_shortcode' ) );
		}
		
		// setup custom post type
		public function setup_amAds(){
			
			//CUSTOM POST TYPES
			$amAd_labels = array(
			  'name' => _x('All Ads', 'post type general name'),
			  'singular_name' => _x('All Ads', 'post type singular name'),
			  'add_new' => _x('Add New Ads', 'amAds'),
			  'add_new_item' => __('Add New Ads'),
			  'edit_item' => __('Edit Ads'),
			  'new_item' => __('New Ads'),
			  'all_items' => __('All Ads'),
			  'view_item' => __('View Ads'),
			  'search_items' => __('Search Ads'),
			  'not_found' =>  __('No Ads found'),
			  'not_found_in_trash' => __('No Tracks found in Trash'),
			  'parent_item_colon' => '',
			  'menu_name' => 'Amazing Ads Manager'

			);
			$amAd_args = array(
			  'labels' => $amAd_labels,
			  'public' => false,
			  'show_ui' => true,
			  'show_in_menu' => true,
			  'has_archive' => true,
			  'hierarchical' => false,
			  'menu_icon' => plugins_url('/assets/images/icon.png', __FILE__ ),
			  'supports' => array('title', 'custom_fields'),
			 'register_meta_box_vo' => array(&$this, 'add_meta_boxes')
			);
			
			register_post_type( self::post_type, $amAd_args );
			

			}
			/*
			*	amadsDisplay
			*	@param int $id,limit string $size,$order
			*	@since 0.0.4
			*/
			public function amadsDisplay($size,$order,$limit){
				
				//set limit to 1 as default
				$limit=(!empty($limit)||!int($limit))? $limit:1;
				// default size
				$size=(!empty($size))? $size:'320x100';
						if($order=="rand"){
							$amads_args = array( 
							  'post_type' => 'amadsmananger', 			
							  'orderby' => 'rand',
							 'meta_key'		=> 'ad_sizes',
							  'showposts'=>$limit,
							  'meta_value'	=> $size,
							  'post_status'=>'publish'
							);
						}
						if($order=="DESC"){
							$amads_args = array( 
							'post_type' => 'amadsmananger', 
							'order' => 'DESC',
							'meta_key'		=> 'ad_sizes',
							'showposts'=>$limit,
							'meta_value'	=> $size,
							'post_status'=>'publish'
							);
						}
						if($order=="ASC"){						
							$amads_args = array( 
							'post_type' => 'amadsmananger', 
							'order' => 'ASC',
							'meta_key'		=> 'ad_sizes',
							'showposts'=>$limit,
							'meta_value'	=> $size,
							'post_status'=>'publish'
							);
						}?>
                        <ul class="list-amads">
                         <?php 	
                            query_posts($amads_args);
                            while (have_posts()) : the_post();
                            		if ( post_custom('ad_type') ) {

                            if( post_custom('ad_type')=="image"){?>
                         		   <li class="amads_<?php echo $size;?>">
                           		 <a href="<?php echo post_custom('amads_link');?>" id="<?php echo the_ID() ;?>" class="amadas-cl" target="_blank" title="<?php echo the_title();?>"/>
                            <img src="<?php echo post_custom('amads_image');?>" alt="<?php echo the_title();?>" />
                            </a></li>
                            <?php	}
                            if( post_custom('ad_type')=="codes"){?>
                            <li class="amads_<?php echo $size;?>" id="<?php echo the_ID();?>">
                            <?php echo post_custom('amads_codes');?>
                            </li>
                            <?php	} 
                            }
                            endwhile;
                            wp_reset_query();
                            ?>                            
                         </ul>	
                <?php
				
			}
			/*
			*	amads_b_content
			*	@param bigtext $amads_data_content
			*	@since 0.0.4
			*/
			public function amads_b_content($amads_data_content){
				//global $post;
				$this->amads_option=get_option(self::post_type);
				if($this->amads_option['amads-bppc-e']==true){
					$newcontent=$this->amadsDisplay($this->amads_option['amads-as'],$this->amads_option['amads-adot'],$this->amads_option['amads-limit']);
					return  $newcontent.$amads_data_content;
				}else {
					return $amads_data_content;
				}
				
			}
			/*
			*	amads_after_content
			*	@param bigtext $amads_data_content
			*	@since 0.0.4
			*/
			public function amads_a_content($amads_data_content){
				//global $post;
				$this->amads_option=get_option(self::post_type);
				if($this->amads_option['amads-appc-e']==true){
					$newcontent=$this->amadsDisplay($this->amads_option['amads-aas'],$this->amads_option['amads-aadot'],$this->amads_option['amads-alimit']);
					return  $amads_data_content.$newcontent;
				}else {
					return $amads_data_content;
				}
				
			}
			/*
			*	settingDefault
			*	@param null
			*	@since 0.0.4
			*/
			public function settingDefault(){
				return $default=array(	'amads-bppc-e' =>'',
										'amads-limit' =>'',
										'amads-adot' => '',
										'amads-as' => '',
										'amads-appc-e' =>'',
										'amads-alimit' =>'',
										'amads-aadot' => '',
										'amads-aas' => ''
									);
			}
			//initialize from style
			public function front_style(){
				if(is_admin()){
				wp_enqueue_style('amads-admin-style', plugins_url('/assets/css/amads.css', __FILE__));
				}
				else {
					wp_enqueue_style('amads-front-style', plugins_url('/assets/css/amads-front.css', __FILE__));			
				}
			}
			
			
			function my_admin_error_notice() {
					$class = "update-nag";
					$message = "This plugin will not work properly with this WordPress version, Update your WordPress to latest version";
						echo"<div class=\"$class\"> <p>$message</p></div>"; 
				}

			// initialize admin 
		public function init_admin() {
				// Localize wp version
				global $typenow, $wp_version;
				if(version_compare($wp_version, '3.5', '<'))
				{
					add_action( 'admin_notices', array(&$this,'my_admin_error_notice')); 
				
				}		
			 	wp_enqueue_script('amads-admin-js', plugins_url('/assets/js/amads.js', __FILE__));
				if(!get_option(self::post_type)){
				add_option(self::post_type,$this->settingDefault());
				}
				if(function_exists( 'wp_enqueue_media' )){
								wp_enqueue_media();
							}else{
								wp_enqueue_style('thickbox');
								wp_enqueue_script('media-upload');
								wp_enqueue_script('thickbox');					
				}
		}
		/*
		*	add Amazing Ads Manager to admin menu
		*	since 0.0.4
		*/
		
		public function add_amAds_sub_pages() {
			$addnew = add_submenu_page( 'edit.php?post_type=amadsmananger',
										__( 'Ads Settings' ),
										__( 'Ads Settings' )
										,self::CAPABILITY,
										'ads-setting',array(&$this, 'amAds_setting')
									);
							}
		public function amAds_setting(){		
			if(isset($_POST['sec_code'])){
				$amads_option =$_POST;
				unset($amads_option['sec_code'],$amads_option['amads_save']);
				update_option(self::post_type,$amads_option);
			}
				$this->amads_option=get_option(self::post_type);
			?>
			<div class='amadsWrapper'>
				<div class="amads-header"><h2>Amazing Ads Manager Settings</h2></div>
                <form name="amads-settings" id="amads-settings" method="post" action="edit.php?post_type=amadsmananger&page=ads-setting">
                <div class="sub-title">Auto Insert Ads before Post/Page Content</div>
                <ul class="input-group">
                <li>
                <label for="amads-bppc-e">Enable</label>
                <input type="checkbox" name="amads-bppc-e" id="amads-bppc-e" 
				<?php 					
						echo ($this->amads_option['amads-bppc-e']==true)? 'checked': ''; 
				?> value="true"/>
                </li>
                 <li>
                <label for="amads-limit">Ads Limit</label>
                <input type="text" name="amads-limit" id="amads-limit" value="<?php echo $this->amads_option['amads-limit'];?>" />
                </li>
                 <li>
                <label for="amads-adot">Ads Display Oder Type</label>
                <select name="amads-adot" id="amads-adot">
                <option value="">Select</option>
                <option value="rand" <?php if($this->amads_option['amads-adot']=="rand"){echo 'selected="selected"';} ?>>Show Ads Randomly</option>
                 <option value="DESC"  <?php if($this->amads_option['amads-adot']=="DESC"){echo 'selected="selected"';} ?>>Show By Latest Ad</option>
                 <option value="ASC"  <?php if($this->amads_option['amads-adot']=="ASC"){echo 'selected="selected"';} ?>>Show By Oldest Ad</option>
               
                </select>
                </li>
                 <li>
                <label for="amads-as">Ads Size</label>
                 <select name="amads-as" id="amads-as">
                 <option value="">Select</option>
                	 <?php foreach($this->adsizes as $key => $val){
                            echo '<option value="'.$key.'"';
                            if($this->amads_option['amads-as']==$key)
                            {
                                echo 'selected';
                            } 
                            echo '>'.$val.'</option>';
                        } 
                        ?>         
                </select>
                </li>
                <div class="sub-title">Auto Insert Ads After Post/Page Content</div>
                <ul class="input-group">
                <li>
                <label for="amads-appc-e">Enable</label>
                <input type="checkbox" name="amads-appc-e" id="amads-appc-e" 
				<?php 					
						echo ($this->amads_option['amads-appc-e']==true)? 'checked': ''; 
				?> value="true"/>
                </li>
                 <li>
                <label for="amads-alimit">Ads Limit</label>
                <input type="text" name="amads-alimit" id="amads-alimit" value="<?php echo $this->amads_option['amads-alimit'];?>" />
                </li>
                 <li>
                <label for="amads-aadot">Ads Display Oder Type</label>
                <select name="amads-aadot" id="amads-aadot">
                <option value="">Select</option>
                <option value="rand" <?php if($this->amads_option['amads-aadot']=="rand"){echo 'selected="selected"';} ?>>Show Ads Randomly</option>
                 <option value="DESC"  <?php if($this->amads_option['amads-aadot']=="DESC"){echo 'selected="selected"';} ?>>Show By Latest Ad</option>
                 <option value="ASC"  <?php if($this->amads_option['amads-aadot']=="ASC"){echo 'selected="selected"';} ?>>Show By Oldest Ad</option>
               
                </select>
                </li>
                 <li>
                <label for="amads-aas">Ads Size</label>
                 <select name="amads-aas" id="amads-aas">
                 <option value="">Select</option>
                	 <?php foreach($this->adsizes as $key => $val){
                            echo '<option value="'.$key.'"';
                            if($this->amads_option['amads-aas']==$key)
                            {
                                echo 'selected';
                            } 
                            echo '>'.$val.'</option>';
                        } 
                        ?>         
                </select>
                </li>
                <li>
                 <label for="amads_save"> </label>
                   <input type="hidden" name="sec_code" id="sec_code" value="amads" />
                 <input type="submit" name="amads_save" id="amads_save" class="button button-primary button-large" value="Save Setting" /><span>
				 <?php if(isset($_POST['amads_save'])){ echo "Saved"; } ?></span></li>
                </ul>
                </form>
			</div>
			
		<?php }
			 //add filter to ensure the text Ad message is displayed when user updates an Ad 

			public function amadsmananger_updated_messages( $messages ) {
			  global $post, $post_ID;
			
			  $messages['amadsmananger'] = array(
				0 => '', // Unused. Messages start at index 1.
				1 => 'Ad updated',
				2 => 'Custom field updated.',
				3 => 'Custom field deleted.',
				4 => 'Ad updated.',
				/* translators: %s: date and time of the revision */
				5 => isset($_GET['revision']) ? sprintf('Ad restored to revision from %s', wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6 => 'Ad published. ' ,
				7 => 'Ad saved.',
				8 => 'Ad submitted. ',
			
				10 => 'Ad draft updated. ',
			  );
			
			  return $messages;
			}
		//create custom column shortcode
		public function add_custom_columns( $defaults ) {
			unset($defaults['date'],$defaults['thumbnail']);			
		    $defaults['amads_shortcode'] = 'Shortcode';
			$defaults['date'] = 'Date';
		    return $defaults;

		}
		//add meta box in the "Add New Ads" page
				public function add_custom_box() {
					global $pagenow,$typenow;				
					add_meta_box('amads-meta-box', 'Ads Manager Option', array( &$this, 'create_meta_box'), 'amadsmananger', 'normal', 'high');
				}
		
		// create Custom meta boxes
		public function create_meta_box(){
			global $post;
			$custom_fields = get_post_custom($post->ID);
				if(!isset($custom_fields['ad_sizes'][0])){
					$custom_fields['ad_sizes'][0]="";
				}
				if(!isset($custom_fields['ad_type'][0])){
					$custom_fields['ad_type'][0]="";
				}
				if(!isset($custom_fields['amads_link'][0])){
					$custom_fields['amads_link'][0]="";
				}
				if(!isset($custom_fields['amads_image'][0])){
					$custom_fields['amads_image'][0]="";
				}
				if(!isset($custom_fields['amads_codes'][0])){
					$custom_fields['amads_codes'][0]="";
				}
			
			?>
            <div class="input-holder">
                <label for="ads-size">Select Ads Size</label>
                    <select name="ad_sizes" id="ads-size">
                        <?php foreach($this->adsizes as $key => $val){
                            echo '<option value="'.$key.'"';
                            if($custom_fields['ad_sizes'][0]==$key)
                            {
                                echo 'selected';
                            } 
                            echo '>'.$val.'</option>';
                        } 
                        ?>                
                    </select>
            </div>
				<div class="input-holder">
                    <label for="ads-type">Select Ad Type </label>
                    <select name="ad_type" id="ads-type">
                    	<option value="">Select Ad Type</option>
                        <option value="image" <?php if($custom_fields["ad_type"][0]=="image"){echo 'selected="selected"';} ?> >Image</option>
                        <option value="codes" <?php if($custom_fields["ad_type"][0]=="codes"){echo 'selected="selected"';} ?>>Codes</option>                        
                    </select>
           		</div>
                <div id="image-ad-type">
	          		 <div class="input-holder">
               		  <label for="link-url">Enter Adlink</label>
                    <input type="text" name="amads_link" id="amads_link" value="<?php echo $custom_fields["amads_link"][0]; ?>" class="widefat" />
                    </div>
                     <div class="input-holder">
                    <label for="image-select">Select Image</label>
                    <input type="hidden" name="amads_image" id="amads_image" value="<?php echo $custom_fields["amads_image"][0]; ?>" class="widefat" />
                    <input class="button button-primary button-large amads-image-btn" id="amads-image-btn" type="button" value="Select Image" />
                            <div class="amads-image-preview" id="amads-image-preview">
                           	 <img id="amads_preveiew" src="<?php echo $custom_fields["amads_image"][0];?>" />
                            </div>    
                  
                                                    
              </div>
             </div>
            <div class="input-holder" id="codes">
              
                <label for="codes-select">Paste your ads code</label>
                <textarea name="amads_codes" class="shortcode-in-list-table wp-ui-text-highlight code" placeholder="paste your Google Ads Code or others"><?php echo $custom_fields["amads_codes"][0]; ?></textarea>
              
             </div>
			<?php
		
		}
		//add associated data to column
		public function posts_custom_column( $column_name, $id ) {

			global $typenow;
		    if ( $typenow=='amadsmananger' ) {
		        echo "<input type='text' onfocus='this.select();' readonly='readonly' 
				class='shortcode-in-list-table wp-ui-text-highlight code' size='50%'
				 value='".get_post_meta( $id, 'amads_shortcode', true )."' />";
		    }

		}
		public function update_custom_meta_fields()	{

			//disable autosave,so custom fields will not be empty
		
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			
		        return $post_id;

				global $post,$typenow;
				
		    if ( $typenow=='amadsmananger' && isset($_POST['ad_type'])) {
				
				switch($_POST['ad_type']){
					case "image":update_post_meta($post->ID, "amads_image", trim($_POST["amads_image"]));
								update_post_meta($post->ID, "amads_link", trim($_POST["amads_link"]));
				break;
					case "codes":update_post_meta($post->ID, "amads_codes", trim($_POST["amads_codes"]));
				break;	
				}
				
			update_post_meta($post->ID, "ad_sizes", trim($_POST["ad_sizes"]));
			update_post_meta($post->ID, "ad_type", trim($_POST["ad_type"]));
			update_post_meta($post->ID, "amads_title", trim($_POST["post_title"]));
			update_post_meta($post->ID, "amads_shortcode", 
							'[amads id="'.$post->ID.'" size="'.trim($_POST["ad_sizes"]).'" title="'.$_POST['post_title'] .'"]');
				}

		}
		// create Shortcode
		public function amads_shortcode( $atts ) {
			global $post;
			extract(shortcode_atts( array(
				'id' => 'no',
				'size' => 'no',
				'title' => 'default'
			), $atts, 'amads' ));
				
				$amads_args = array( 
				  'post_type' => 'amadsmananger', 
				 'p'      => $id,
				 'meta_key'		=> 'ad_sizes',
				 'meta_value'	=> $sizes,
			);
			query_posts($amads_args);
				 while (have_posts()) : the_post();
				if ( post_custom('ad_type') ) {
						$ad_type = post_custom('ad_type');
						if($ad_type=="image"){
							$amads_contnet='<a href="'.post_custom("amads_link").'" data-amads-id='.$id.' class="amadas-cl" target="_blank" title="'.$title.'"/>
                            	<img src="'.post_custom('amads_image').'" alt="'.$title.'" />
                             </a></li>';
					}
						 if($ad_type=="codes"){
							$amads_contnet=post_custom('amads_codes');
						} 
					}
					endwhile;
					wp_reset_query();
					return '<div data-amads-id='.$id.' class="amads_'.$size.' amadas-cl">'.$amads_contnet.'</div>';
				}
			//Shortcode Button on TinyMCE.
			public function amads_add_tinymce_plugin( $plugin_array ) {
				
					$plugin_array['amads_tc_button'] = plugins_url('/assets/js/amads-tinymce.js', __FILE__);
					return $plugin_array;
			}
			public function amads_register_my_tc_button( $buttons ) {
				array_push( $buttons, 'amads_tc_button' ); 
				return $buttons;
			}
			public function getamadsData() {
         		global $wpdb,$post;
        $querystr = "   SELECT $wpdb->posts.* , $wpdb->postmeta.*
                        FROM $wpdb->posts, $wpdb->postmeta
                        WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
                        AND $wpdb->postmeta.meta_key = 'amads_shortcode'
                        AND $wpdb->posts.post_type = 'amadsmananger'
						AND $wpdb->posts.post_status = 'publish'
                        ORDER BY $wpdb->posts.ID DESC
                     ";
			$amadsposts = $wpdb->get_results($querystr);
 				echo '<script type="text/javascript">
			var amadsData=['; 
			if ( $amadsposts ) 
        {
        foreach ( $amadsposts as $amadsData ) : setup_postdata( $amadsData );
       echo "{text :'".get_post_meta($amadsData->ID, 'amads_title', true)."',value:'".get_post_meta($amadsData->ID, 'amads_shortcode', true)."'},";
        endforeach; 
        }
		echo "];
		</script>";
	}
		/*
		*	Amazing Ad Manager Widget
		*	@parm null
		*	@since 0.0.1
		*/
		public function amads_widget(){
			
			$file = dirname(__FILE__) .'/widgets/amads-widget.php';
			
			if (!file_exists($file)) {
			
				continue;
			}

			include_once $file;
			$class=array("amAds_Widgets","amAds_Image_Widgets","amAds_Codes_Widgets");
			foreach($class as $widgetClass):
			if (method_exists($widgetClass, 'register_widget')) {
				$caller = new $widgetClass;
				$caller->register_widget(); 
			}
			else {
				register_widget($widgetClass);
			}
			endforeach;
		}
	}
}
//init Amazing Ads Manager
if(class_exists('AmazingAds')) {
	$vo_amAds = new AmazingAds();
}
?>