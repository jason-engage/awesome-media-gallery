<!-- Profile Starts Here -->
<div class="user-profile-meta">
    <!-- User Meta Starts --> 

    <div class="pure-g-r">
      
        <div class="pure-u-5-8 about left-side"><?php
        
            $about = str_replace('"',"'",$profile->getAbout());
         
            if( $about <> '' || $admin_mode == 1) {
            
                if (str_replace('"','',$about) == '') { 
                    $about_text = $txt_placeholder_arr['about'];
                } else {
                    $about_text=$about;
                } ?>

                <div class="about-me block">
                
                    <span class="heading"><?php echo $langscape["About Me"];?></span>
                    <div class="meta-about <?php echo $edit_class; ?> <?php echo $edit_type_textarea; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="about" data-field-text="<?php echo $about; ?>"><?php echo text2urls(str_replace('{{link}}', 'http://',$about_text)); ?></div>
        
                </div><?php
            
                $profile_flag = 1; 
                
            }
         
            $demo_reel_url = $profile->getMetaValue('demo_reel_url');
    
            if( ( $demo_reel_url <> '' || $admin_mode == 1 )  && ($config->site->members->enable_video)) {
               
                if ($demo_reel_url == '') { 
                    $demo_reel_url_text = $txt_placeholder_arr['demo_reel_url'];
                } else {
                    $demo_reel_url_text = $demo_reel_url;
                } ?>
                
	        <div class="demo-url block">
		        
	          <span class="heading"><?php echo $langscape["My Personal Video"];?></span>
	          
	          <?php if ($admin_mode==1) { ?>
	          <span class="meta-demo-reel <?php echo $edit_class; ?> <?php echo $edit_type_link; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="demo_reel_url" data-field-url="<?php echo $demo_reel_url; ?>"><?php echo str_replace("http://","",$demo_reel_url_text); ?></span>
	          <?php } ?>
	        
	        <?php
		    $demo_reel_url = $profile->getMetaValue('demo_reel_url');
		    
		    if( $demo_reel_url <> '') {
		      	$js_show = 'js-show';
		      	}else{
		      	$js_show = '';
		      	}
		       
		      $res = parse_url($demo_reel_url);
		      
		      if (($res['host'] = "www.youtube.com") || ($res['host'] = "youtube.com") || ($res['host'] = "vimeo.com") || ($res['host'] = "www.vimeo.com")) {
		        $media_class = "fancybox-media";
		        $target = "";
		      } else {
		         $media_class = "";
		         $target = "_blank";
		      } ?>
		      
		      <a href="http://<?php echo str_replace("http://","",$demo_reel_url); ?>" class="demo-reel-link <?php echo $media_class . ' ' . $js_show; ?>" target="<?php echo $target; ?>"><button class="pure-button pure-button-primary video-reel"><?php echo $langscape["Play My Video"];?></button></a>
		      
	      	</div>
 
	         <?php 
			$profile_flag = 1;
			} ?>
		
			<?php
				
				$resume_url = $profile->getMetaValue('resume_url');
	
		  	if( ($config->site->members->enable_resume) && ($resume_url <> '' || $admin_mode ==1) )
			{ 
				if ($resume_url == '') { $resume_url_text=$txt_placeholder_arr['resume_url'];} else {$resume_url_text=''.$langscape["My Resume"].'';}
			
			?>
			<div class="resume block">
				<span class="heading"><?php echo $langscape["Resume URL"];?></span>
				<span class="ellipsis">
					<i class="folder icon"></i>
					<a target="_blank" href="http://<?php echo str_replace("http://","",$resume_url); ?>" class="<?php echo $edit_class; ?> <?php echo $edit_type_link; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="resume_url" data-field-url="<?php echo $resume_url; ?>"><?php echo $resume_url_text; ?></a>
				</span>
			</div>
			<?php
				$profile_flag = 1;
			}
			?>
			
			<?php
	          
            if( $interests = $profile->getMetaValue('interests') ) {
				$interests = explode(',', $interests);
			}
			
            if ( ( $interests || $admin_mode == 1 ) && ($config->site->members->enable_skills) ) { ?>
          
            <!-- TAGS -->
            <div class="interests block">
                <span class="heading"><?php echo $langscape["Interests"];?></span>
                
                <?php
	                
				if ($admin_mode == 1) {		
            	?>
				<script type="text/javascript">
				    
				    var tags = <?php echo json_encode($interests); ?>;
				    
				    jQuery(document).ready( function() { 
				    	
			            jQuery(".tm-input").tagsManager({
				            hiddenTagListName: 'hidden-tags',
			                tagsContainer: '.interests',
			                prefilled: tags
			            });
				    
				    });
				   
				</script>
				
				<input placeholder="<?php echo $langscape['Interests']; ?>" data-value="" class="tm-input pure-u-1 <?php echo $edit_class; ?> <?php echo $edit_type_tags; ?>" type="text" name="tags" id="tags" value="" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="interests">
				<input name="hidden-tags" type="hidden" value="">
				
                <?php
				
				} elseif ( $interests ) {
                    foreach( $interests as $interest ) {
                        
                        $tag = trim( $interest );
                        echo '<a href="./tag/' . urlencode( $interest ) . '"><button class="tm-tag pure-button">' . $interest .'</button></a>';
                   
                    } 
				} ?>
    
          	</div>
		  	
            <?php
            }
			?>
			
		     <?php
		     
	         $skills = str_replace( '"', "'", $profile->getSkills() );
	         
	         if( ($skills <> '' || $admin_mode ==1) && ($config->site->members->enable_skills)) {
		         
	     	    if ($skills == '') { $skills_text=$txt_placeholder_arr['skills'];}else {$skills_text=$skills;}
	          ?>        
	        <div class="skills block">
		        
	          <span class="heading"><?php echo $langscape["Skills"];?></span>
	          <span class="meta-skills <?php echo $edit_class; ?> <?php echo $edit_type_textarea; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="skills" data-field-text="<?php echo $skills; ?>"><?php echo text2urls(str_replace('{{link}}', 'http://',$skills_text)); ?></span>
	          
	        </div>
	        <?php 
			$profile_flag = 1;
			} ?>
  
	     <?php
         $software = $profile->getSoftware();

	         if( ($software <> '' || $admin_mode ==1)  && ($config->site->members->enable_software) ) {
	       	  
	       	  	if ($software == '') { 
	       	  	
	       	  	$software_text=$txt_placeholder_arr['software'];
	       	  	
	       	  	} else {
	       	  	
	       	  	$software_text=$software;
	       	  	
	       	  	}
         
         ?> 
			<div class="software block">
			 
			  <span class="heading"><?php echo $langscape["Software"];?></span>
			  <span class="meta-software <?php echo $edit_class; ?> <?php echo $edit_type_textarea; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="software" data-field-text="<?php echo $software; ?>"><?php echo text2urls(str_replace('{{link}}', 'http://',$software_text)); ?></span>
			
			</div>
			<?php
				$profile_flag = 1;
			} 
				
			$category = $profile->getMetaValue('category');
			
			if( ($category > 0 || $admin_mode ==1)  && ($config->site->members->enable_category) ) {
			  	
				if ($category == 0) { 
				    $category = $txt_placeholder_arr['category'];
				} else { 
				    $category = $users_types_array_combined[$category];
				}
			  ?>
			
			<div class="category block">
				
			  <span class="heading"><?php echo $langscape["Category"];?></span>
			  <span class="meta-category <?php echo $edit_class; ?> <?php echo $edit_type_category; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="category"><?php echo $category; ?></span>
			  
			</div>
			
			<?php
				$profile_flag = 1;
			}
			?>
			
			 <?php
			 $gender = $profile->getMetaValue('gender');
			 if( ($gender <> '' || $admin_mode ==1) && ($config->site->members->enable_gender)) {
				 if ($gender == '') { $gender=$txt_placeholder_arr['gender'];}
			  ?>
			
			<div class="gender block">
				
			  <span class="heading"><?php echo $langscape["Gender"];?></span>
			  <span id="meta-gender" class="meta-gender <?php echo $edit_class; ?> <?php echo $edit_type_gender; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="gender"><?php echo $gender; ?></span>
			  
			</div>
			
			<?php
			$profile_flag = 1;
			}
			?>	
		
			<?php
			if( $profile_flag == 0) {
			?> 
			<div class="profile_empty">
				<span class="heading"><?php echo $profile_name; ?> <?php echo $langscape["has not setup his profile yet."];?></span>
			</div>
			<?php 
			} ?>

		</div>
			
		<div class="pure-u-3-8 right-side">
			  
			<?php
			$occupation = str_replace('"',"'",$profile->getOccupation());
			$years_of_experience = $profile->getMetaValue('years_of_experience');
			
			if( ($occupation <> '' ||  $admin_mode ==1) && $config->site->members->enable_occupation ) {
				if ($occupation == '') { $occupation_text=$txt_placeholder_arr['occupation'];
					
				} else {
					$occupation_text=$occupation;
				}
				
				if ($years_of_experience == '') { 
					$years_of_experience=$txt_placeholder_arr['years_of_experience'];
				} else {
					$years_of_experience=$years_of_experience;
				}
			?>
			
			<div class="occupation block">
				
				<span class="heading"><?php echo $langscape["Occupation"];?><br>
			  <?php 
				  if (isset($years_of_experience) || $admin_mode ==1) { 
				  
			  ?>
					<span class="edit-experience"><?php echo $langscape["Experience:"];?>&nbsp;
					<span class="meta-experience <?php echo $edit_class; ?> <?php echo $edit_type_text; ?>" data-id="<?php echo $profile_id; ?>" data-field-name="years_of_experience" data-field-text="<?php echo $years_of_experience; ?>" data-module-name="user" style="display:inline;"><?php echo $years_of_experience; ?></span> <?php echo $langscape["years"];?></span>
				  <?php 
				  } ?>
				</span>
			  
				<i class="star icon"></i>
				<span class="meta-occupation <?php echo $edit_class; ?> <?php echo $edit_type_text; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="occupation" data-field-text="<?php echo $occupation; ?>"><?php echo text2urls($occupation_text); ?></span>   
			         
			</div>
			
			<?php
			}
			
			$availability = $profile->getMetaValue('available_for_freelance');
			
			//Available for freelance. 
			if ( $config->site->members->enable_available ) { 
			?>
			<div class="availability block">
				<span class="heading"><?php echo $langscape["Availability"];?></span>
				
		    	<i class="icon <?php echo $profile->isMetaValue('available_for_freelance') ? 'checkmark' : 'cross'; ?>"></i>
		    	<span class="<?php echo $edit_class; ?> <?php echo $edit_type_yesno_freelance; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="available_for_freelance"><?php echo $availability ? $langscape["Yes"] : $langscape["No"]; ?></span>
			    	
			</div>
			<?php 
			} ?>
			
			<?php 
			$email_pub = $profile->isMetaValue('email_public');
			
			if ( (($email_pub ==1 && $profile_email <> '')  || $admin_mode == 1) && $config->site->members->enable_public_emails) {
			?>
			<div class="email block">
				<span class="heading"><?php echo $langscape["Email"];?></span>
				<span>
					<i class="mail icon"></i>
					<a target="_blank" href="mailto:<?php echo $profile_email; ?>"><?php echo $profile_email; ?></a>
				</span>
			</div>
			<?php
			}
			?>
			
			<?php	       
			if ( ($admin_mode == 1) && $config->site->members->enable_public_emails ) {
			
			?>
			<div class="pub_email block">
				<span class="heading"><?php echo $langscape["Publicize your email?"];?></span>
				
				<i class="<?php echo ($email_pub) ? 'checkmark' : 'warning'; ?> icon"></i>
				<span class="meta-email-public <?php echo $edit_class; ?> <?php echo $edit_type_yesno; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="email_public"><?php echo ($email_pub)?''.$langscape["Yes"].'':''.$langscape["No"].''; ?></span>
			</div>
			<?php	       
			} 
			
			$facebook_url = $profile->getMetaValue('facebook_url');		
			$twitter_url = $profile->getMetaValue('twitter_url');
			$google_url = $profile->getMetaValue('google_url');
			$linkedin_url = $profile->getMetaValue('linkedin_url');
			$kickstarter_url = $profile->getMetaValue('kickstarter_url');
			
			if (($facebook_url<>'') || ($twitter_url<>'') || ($google_url<>'') || ($linkedin_url<>'') || ($kickstarter_url<>'') || ($admin_mode == 1)) {
			?>
			<div class="meta-social block">
				<span class="heading"><?php echo $langscape["Social Links"];?></span>
				
					<?php          
					    if( $facebook_url <> ''  || $admin_mode == 1)
						{
							if ($facebook_url == '') { $facebook_url_text=$txt_placeholder_arr['facebook_url'];}else{$facebook_url_text=''.$langscape["My Facebook page"].'';}
					?>
					
				<span class="meta-soc meta-facebook">
					<i class="facebook icon"></i>
					<a href="http://<?php echo str_replace("http://","",$facebook_url); ?>" target="_blank" class="<?php echo $edit_class; ?> <?php echo $edit_type_link; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="facebook_url" data-field-url="<?php echo $facebook_url; ?>"><?php echo $facebook_url_text; ?></a>
				</span>
				<?php
					}
					
					if( $twitter_url <> ''  || $admin_mode == 1)
					{
						if ($twitter_url == '') { $twitter_url_text=$txt_placeholder_arr['twitter_url'];}else{$twitter_url_text=''.$langscape["My Twitter page"].'';}
				?>
				<span class="meta-soc meta-twitter">
					<i class="twitter icon"></i>
					<a href="http://<?php echo str_replace("http://","",$twitter_url); ?>" target="_blank" class="<?php echo $edit_class; ?> <?php echo $edit_type_link; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="twitter_url" data-field-url="<?php echo $twitter_url; ?>"><?php echo $twitter_url_text; ?></a>
				</span>
				<?php
					}
					
					if( $google_url <> ''  || $admin_mode == 1)
					{
						if ($google_url == '') { $google_url_text=$txt_placeholder_arr['google_url'];}else{$google_url_text=''.$langscape["My Google+ page"].'';}
				?>
				<span class="meta-soc meta-google">
					<i class="googleplus icon"></i>
					<a href="http://<?php echo str_replace("http://","",$google_url); ?>" target="_blank" class="<?php echo $edit_class; ?> <?php echo $edit_type_link; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="google_url" data-field-url="<?php echo $google_url; ?>"><?php echo $google_url_text; ?></a>
				</span>
				
				<?php
					}
					
					if( $linkedin_url <> ''  || $admin_mode == 1)
					{
						if ($linkedin_url == '') { $linkedin_url_text=$txt_placeholder_arr['linkedin_url'];}else{$linkedin_url_text=''.$langscape["My LinkedIn page"].'';}
				?>
			    <span class="meta-soc meta-linkedin">
				    <i class="linkedin icon"></i>
				    <a href="http://<?php echo str_replace("http://","",$linkedin_url); ?>" target="_blank" class="<?php echo $edit_class; ?> <?php echo $edit_type_link; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="linkedin_url" data-field-url="<?php echo $linkedin_url; ?>"><?php echo $linkedin_url_text; ?></a>
				</span>
				
				<?php
					}
					
					if( $kickstarter_url <> ''  || $admin_mode == 1)
					{
						if ($kickstarter_url == '') { $kickstarter_url_text=$txt_placeholder_arr['kickstarter_url'];}else{$kickstarter_url_text=''.$langscape["My Kickstarter page"].'';}
				?>
			    <span class="meta-soc meta-kickstarter">
			    	<i class="rocket icon"></i>
			    	<a href="http://<?php echo str_replace("http://","",$kickstarter_url); ?>" target="_blank" class="<?php echo $edit_class; ?> <?php echo $edit_type_link; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="kickstarter_url"  data-field-url="<?php echo $kickstarter_url; ?>"><?php echo $kickstarter_url_text; ?></a>
			    </span>
				
				<?php
					} ?>
			</div>	
			<?php	
			}
			?>
		
		<?php		
		/* NOT ACTIVE AT THE MOMENT */
		$other_urls = explode(',', $user->getOtherUrls());
		$txt = array_pop($other_urls);
		if( $txt <> '' ) {
		?>
		    <span class="heading other_urls"><?php echo $langscape["Other Urls"];?></span>
		<?php
			foreach( $other_urls as $other_url )
			{
		?>
			<span class="other-url"><a target="_blank" href="<?php echo $other_url; ?>"><?php echo $other_url; ?></a></span>
		<?php
			}
		
		}
		
			if ( $config->site->members->enable_stats ) { ?>
			
			<div class="meta-stats block">
				
				<span class="heading"><?php echo $langscape["Stats"];?></span>
				
				<?php if ($config->site->media->enable_images) { ?>       
				<span class="pure-u meta-views stat">
					<i class="pictures icon"></i>
					<span class="attribute"><?php echo $langscape["Total Images"];?></span>
					<span class="text"><?php echo number_format($profile->getTotalUserImages(1)); ?></span>
				</span>
				<?php } ?>
				
				<?php if ($config->site->media->enable_videos) { ?>
				<span class="pure-u meta-views stat">
					<i class="fa fa-youtube-play icon"></i>
					<span class="attribute"><?php echo $langscape["Total Videos"];?></span>
					<span class="text"><?php echo number_format($profile->getTotalUserImages(2)); ?></span>
				</span>
				<?php } ?>
				
				<?php if ($config->site->media->enable_audio) { ?>
				<span class="pure-u meta-views stat">
					<i class="fa fa-music icon"></i>
					<span class="attribute"><?php echo $langscape["Total Audios"];?></span>
					<span class="text"><?php echo number_format($profile->getTotalUserImages(3)); ?></span>
				</span>
				<?php } ?>
				         
				<span class="pure-u meta-views stat">
				    <i class="users icon"></i>
				    <span class="attribute"><?php echo $langscape["Followers"];?></span>
				    <span class="text"><?php echo number_format($profile->getTotalFollowers()); ?></span>
				  </span>
				
				<span class="pure-u meta-views stat">
				    <i class="users icon"></i>
				    <span class="attribute"><?php echo $langscape["Following"];?></span>
				    <span class="text"><?php echo number_format($profile->getTotalFollowing()); ?></span>
				  </span>
				
				<span data-image-id="" data-likes="<?php echo number_format($profile->getTotalFavourites()); ?>" class="pure-u meta-hearts stat">
		            <i class="heart icon"></i>
		            <span class="attribute"><?php echo $langscape["Favorites"];?></span>
		            <span class="text"><?php echo number_format($profile->getTotalFavourites()); ?></span>
				</span>
	        
				<span data-image-id="" data-likes="<?php echo $profile->getTotalReceivedFavourites(); ?>" class="pure-u meta-hearts stat">
					<i class="heart icon"></i>
					<span class="attribute"><?php echo $langscape["Fan Favorites"];?></span>
					<span class="text"><?php echo $profile->getTotalReceivedFavourites(); ?></span>
				</span>	
				
				<span class="pure-u meta-views stat">
					<i class="eye icon"></i>
					<span class="attribute"><?php echo $langscape["Fan Views"];?></span>
					<span class="text"><?php echo number_format($profile->getTotalReceivedViews()); ?></span>
				</span>
				
				<?php if ($config->extensions->gallery->image_comments) { ?>
				<span data-comment-id="<?php echo $profile->getTotalReceivedComments(); ?>" class="pure-u meta-comment stat">
				  <i class="comment icon"></i>
				  <span class="attribute"><?php echo $langscape["Fan Comments"];?></span>
				  <span class="text"><?php echo $profile->getTotalReceivedComments(); ?></span>
				</span>					
				<?php } ?>
	        
	        </div>
		
			<?php 
			} //enable stats ?>

      	</div> <!-- RIGHT SIDE END -->

	</div>  <!-- User meta Ends -->

  </div> <!-- user-profile-meta -->
  
  <!--Profile Ends Here -->	