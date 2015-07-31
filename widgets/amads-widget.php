<?php
/**
*----------------------------------------------------
	Amazing Ads Manager Widget, Load Custom Post Ads
*----------------------------------------------------
**/
class amAds_Widgets extends WP_Widget
{
	
    function amAds_Widgets() {		
		global $adsizes;
		$this->adsizes =$adsizes;
		 $widget_ops = array('description' => __('Load ads added in Amazing Ads Manager custom page', 'vo') );
        parent::WP_Widget(false, $name = 'Amazing Ad Manager', $widget_ops);	
		
    }
	
	public function widget($args, $instance)
	{
	

	  global $post;
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		
		?>

		<?php echo $before_widget; 
		
		if($instance['amads_option']=="s_ad_rand"){
			$amads_args = array( 
			  'post_type' => 'amadsmananger', 			
			  'orderby' => 'rand',
			 'meta_key'		=> 'ad_sizes',
			  'showposts'=>$instance['amads_limit'],
			  'meta_value'	=> $instance['amads_sizes'],
			  'post_status'=>'publish'
			);
		}
		if($instance['amads_option']=="s_lates_ad"){
			$amads_args = array( 
			  'post_type' => 'amadsmananger', 
			  'showposts' => $instance['amads_limit'],
			 
			  'order' => 'DESC',
			'meta_key'		=> 'ad_sizes',
			  'meta_value'	=> $instance['amads_sizes'],
			   'post_status'=>'publish'
			);
		}
		if($instance['amads_option']=="s_oldest_ad"){
			
			$amads_args = array( 
			  'post_type' => 'amadsmananger', 
			  'showposts' => $instance['amads_limit'],
			  'order' => 'ASC',
			 'meta_key'		=> 'ad_sizes',
			  'meta_value'	=> $instance['amads_sizes'],
			   'post_status'=>'publish'
			);
		}
		if($instance['amads_option']=="s_specific_ad"){
			$amads_post=explode(',',$instance['ads_ids']);
			
			$amads_args = array( 
			  'post_type' => 'amadsmananger', 
			  'showposts' => $instance['amads_limit'],
			 'post__in'      => $amads_post,
			 'meta_key'		=> 'ad_sizes',
			 'meta_value'	=> $instance['amads_sizes'],
			'post_status'=>'publish'
			);
		}
		

?>
		
			<?php if ($title): ?>
				<?php echo $before_title . $title . $after_title; ?>
			<?php endif; 
		
			?>
			
			<div class="amads-widget">
				<ul class="list-amads">
                <?php 	
				query_posts($amads_args);
				 while (have_posts()) : the_post();
				if ( post_custom('ad_type') ) {
						$ad_type = post_custom('ad_type');
						if($ad_type=="image"){?>
							<li class="amads_<?php echo $instance['amads_sizes'];?>">
							<a href="<?php echo post_custom('amads_link');?>" id="<?php echo the_ID() ;?>" class="amadas-cl" target="_blank" title="<?php echo the_title();?>"/>
                            	<img src="<?php echo post_custom('amads_image');?>" alt="<?php echo the_title();?>" />
                             </a></li>
					<?php	}
						 if($ad_type=="codes"){?>
							<li class="amads_<?php echo $instance['amads_sizes'];?>" id="<?php echo the_ID();?>" class="amadas-cl" >
                            <?php echo post_custom('amads_codes');?>
							</li>
					<?php	} 
					}
					endwhile;
					wp_reset_query();
				?>
                
                		
              
				
                </ul>	
			
			</div>
		
		<?php  echo $after_widget; ?>
		
		<?php
	}
	
	public function update($new, $old)
	{
		foreach ($new as $key => $val) {
			$new[$key] = $val;
		}
		
		return $new;
	}
	
	public function form($instance)
	{
		$defaults = array('title' => '', 'amads_option' => '','shortcode'=>'','amads_sizes'=>'','amads_limit'=>'','ads_ids'=>'');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
		//load all ads from custom post type
	?>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title: (Optional)', 'vo'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	
	<p>
    <label for="<?php echo esc_attr($this->get_field_id('amads_sizes')); ?>"><?php _e('Ads Sizes:', 'vo'); ?></label>
               	<select name="<?php echo esc_attr($this->get_field_name('amads_sizes')); ?>" class="widefat" id="<?php echo esc_attr($this->get_field_id('amads_sizes')); ?>">
                <option value="">Select</option>
                <?php foreach($this->adsizes as $key => $val){
                            echo '<option value="'.$key.'"';
                            if($amads_sizes==$key)
                            {
                                echo 'selected';
                            } 
                            echo '>'.$val.'</option>';
                        } 
                        ?>     
                
                </select>
                </p>
                <p>
               		<label for="<?php echo esc_attr($this->get_field_id('amads_limit')); ?>"><?php _e('Number of Ads to show:', 'vo'); ?></label>
					<input class="widefat adl"  id="<?php echo esc_attr($this->get_field_id('amads_limit')); ?>" name="<?php 
					echo esc_attr($this->get_field_name('amads_limit')); ?>" type="text" value="<?php echo esc_attr($amads_limit); ?>" />
                </p>
               
		<label for="<?php echo esc_attr($this->get_field_id('amads_option')); ?>"><?php _e('Ads Options:', 'vo'); ?></label>
               	<select name="<?php echo esc_attr($this->get_field_name('amads_option')); ?>" class="widefat amads_select" id="<?php echo esc_attr($this->get_field_id('amads_option')); ?>">                
                <option value="">Select</option>
                <option value="s_ad_rand" <?php if($amads_option=="s_ad_rand"){echo 'selected="selected"';} ?>>Show Ads Randomly</option>
                 <option value="s_lates_ad"  <?php if($amads_option=="s_lates_ad"){echo 'selected="selected"';} ?>>Show By Latest Ad</option>
                 <option value="s_oldest_ad"  <?php if($amads_option=="s_oldest_ad"){echo 'selected="selected"';} ?>>Show By Oldest Ad</option>
                <option value="s_specific_ad"  <?php if($amads_option=="s_specific_ad"){echo 'selected="selected"';} ?>>Show Ad by IDs</option>
                
                </select>
               
               <p class="amads-by-ids">
		<label for="<?php echo esc_attr($this->get_field_id('ads_ids')); ?>"><?php _e('Enter Ids (Seperate with comma (,)):', 'vo'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('ads_ids')); ?>" name="<?php 
					echo esc_attr($this->get_field_name('ads_ids')); ?>" type="text" value="<?php echo esc_attr($ads_ids); ?>" />
	</p>
	<?php
	}
}/**
*----------------------------------------------
	Amazing Ads Widget, Custom Image Ad Place
*----------------------------------------------
**/
class amAds_Image_Widgets extends WP_Widget
{
	
    function amAds_Image_Widgets() {		
		global $adsizes;
		$this->adsizes =$adsizes;
		 $widget_ops = array('description' => __('Single Image Ad', 'vo') );
        parent::WP_Widget(false, $name = 'Amazing Ad Manager:Image', $widget_ops);	
		
    }
	
	public function widget($args, $instance)
	{
	  global $post;
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		
		?>

		<?php echo $before_widget; ?>
		
			<?php if ($title): ?>
				<?php echo $before_title . $title . $after_title; ?>
			<?php endif; 
		
			?>
			
			<div class="amads-widget">
				<ul class="list-amads">
    
							<li class="amads_<?php echo $instance['amads_sizes'];?>">
							<a href="<?php echo $instance['amads_link'];?>" target="_blank" title=" Ads <?php echo $instance['amads_sizes'];?>"/>
                            	<img src="<?php echo $instance['amadimgurl'];?>" alt=" Ads <?php echo $instance['amads_sizes'];?>" />
                             </a></li>
                </ul>	
			
			</div>
		
		<?php  echo $after_widget; ?>
		
		<?php
	}
	
	public function update($new, $old)
	{
		foreach ($new as $key => $val) {
			$new[$key] = $val;
		}
		
		return $new;
	}
	
	public function form($instance)
	{
		$defaults = array('title' => '','amads_sizes'=>'','amadimgurl'=>'','amads_link'=>'');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
		
	?>
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title: (Optional)', 'vo'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
    <p>
		<label for="<?php echo esc_attr($this->get_field_id('ad_size')); ?>"><?php _e('Ad Size:', 'vo'); ?></label>
        	<select name="<?php 
			echo esc_attr($this->get_field_name('ad_size')); ?>" id="<?php echo esc_attr($this->get_field_id('ad_size')); ?>" class="ad-size widefat">
            <option <?php if($amads_size=="300x600"){ echo 'selected'; };?> value="300x600">Large Skyscraper (300x600)</option>
            <option <?php if($amads_size=="300x250"){ echo 'selected'; };?> value="300x250">Medium Rectangle (300x250)</option>
            <option <?php if($amads_size=="250x250"){ echo 'selected'; };?> value="250x250">Square (250x250)</option>
            <option <?php if($amads_size=="200x200"){ echo 'selected'; };?> value="200x200">Small Square (200x200)</option>
            <option <?php if($amads_size=="180x150"){ echo 'selected'; };?> value="180x150">Small Rectangle (180x150)</option>
            <option <?php if($amads_size=="160x600"){ echo 'selected'; };?> value="160x600">Wide Skyscraper (160x600)</option>
            <option <?php if($amads_size=="125x125"){ echo 'selected'; };?> value="125x125">Button (125x125)</option>
           
            </select>
        </p>
        <p style="text-align:center;">
            <div class="button button-primary amad_upload" id="amad_upload">Select Image</div>
		</p>
        <input type="text" name="<?php 
			echo esc_attr($this->get_field_name('amadimgurl')); ?>" placeholder="Image Url" id="<?php echo esc_attr($this->get_field_id('amadimgurl')); ?>" class="amads-img-url widefat" value="<?php echo esc_attr($amadimgurl); ?>"/>
	</p>
    <p>
		<label for="<?php echo esc_attr($this->get_field_id('amads_link')); ?>"><?php _e('Link:', 'vo'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('amads_link')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('amads_link')); ?>" type="text" value="<?php echo esc_attr($amads_link); ?>" />
	</p>
    
	<?php
	}
}
/**
*----------------------------------------------
	Amazing Ads Widget, Custom Codes Ad Place
*----------------------------------------------
**/
class amAds_Codes_Widgets extends WP_Widget
{
	
    function amAds_Codes_Widgets() {		
		global $adsizes;
		$this->adsizes =$adsizes;
		 $widget_ops = array('description' => __('Single Custom Ad Code', 'vo') );
        parent::WP_Widget(false, $name = 'Amazing Ad Manager:Code', $widget_ops);	
		
    }
	
	public function widget($args, $instance)
	{
	  global $post;
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		
		?>

		<?php echo $before_widget; ?>
		
			<?php if ($title): ?>
				<?php echo $before_title . $title . $after_title; ?>
			<?php endif; 
		
			?>
			
			<div class="amads-widget">
				<ul class="list-amads">
    
							<li class="ads_codes">
								<?php echo $instance['ads_codes'];?>
                            </li>
                </ul>	
			
			</div>
		
		<?php  echo $after_widget; ?>
		
		<?php
	}
	
	public function update($new, $old)
	{
		foreach ($new as $key => $val) {
			$new[$key] = $val;
		}
		
		return $new;
	}
	
	public function form($instance)
	{
		$defaults = array('title' => '','ads_codes'=>'');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
		
	?>
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title: (Optional)', 'vo'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
    <p>
		<label for="<?php echo esc_attr($this->get_field_id('ads_codes')); ?>"><?php _e('Ads Code:', 'vo'); ?></label>
        </p>
        <p>
        	<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('ads_codes')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('ads_codes')); ?>"  rows="8"  placeholder="Paste your ad code here"><?php echo $ads_codes; ?></textarea>
        </p>
       
	<?php
	}
}


?>

