<div class="user-profile pure-g-r">

<?php if ($config->site->members->enable_cover_photo) { ?>
    <div class="pure-u-1 meta-cover-photo profile-photo-wrap loading"><?php
    
        if ( $admin_mode == 1 ) { ?>
            
            <div class="hover-wrapper">
                <div class="drag-hover">
	                <div class="alert"><?php echo $langscape['Click to upload a banner']; //. ' (' .$wcp.'x'.$hcp.')'; ?></div>
            	</div><?php 
        
        } ?>
	        <img src="library/thumb.php?f=<?php echo ( $profile->getCoverPhoto() ? $profile->getCoverPhoto() : $config->site->members->default_cover_photo ); ?>&amp;h=<?php echo $hcp; ?>&amp;w=<?php echo $wcp; ?>&amp;m=crop"  src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" alt="<?php echo $profile->getDisplayName() . '\'s ' . $langscape["Cover Photo"]; ?>" <?php if( $admin_mode == 1 ) echo ' id="js-my-photo"';?>><?php 
		
		
        if( $admin_mode == 1 ) { ?>
    
            </div>
    
            <form id="fileupload-photo" action="upload/server/php/" method="POST" enctype="multipart/form-data">
                <input type="file" id="photo-img" name="files[]" accept="image/*">
                <input type="hidden" name="id" value="<?php echo $profile_id; ?>">
            </form><?php 
        
        } ?>
    </div>
<?php } ?>

	<div class="meta-avatar profile-avatar-wrap loading"><?php 
    
        if ( $admin_mode == 1 ) { ?>
            
            <div class="hover-wrapper">
                <div class="drag-hover">
	                <div class="alert"><?php echo $langscape['Click to add image']?></div>
            	</div><?php 
        
        } ?>
	
        <img src="library/thumb.php?f=<?php echo ( $profile->getAvatar() ? $profile->getAvatar() : $config->site->default_avatar ); ?>&amp;h=250&amp;w=250&amp;m=crop"  src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" alt="<?php echo $profile->getDisplayName(); ?>" <?php if( $admin_mode == 1 ) echo ' id="js-my-avatar"';?>><?php 
  
        if( $admin_mode == 1 ) { ?>
    
            </div>
    
            <form id="fileupload-avatar" action="upload/server/php/" method="POST" enctype="multipart/form-data">
                <input type="file" id="avatar-img" name="files[]" accept="image/*">
                <input type="hidden" name="id" value="<?php echo $profile_id; ?>">
            </form><?php 
        
        } ?>
    
	</div>

	<div class="meta-data">
  
	    <div class="account-meta">
        
			<div class="username-holder">
				
                <span id="profile-username" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="display_name" data-type="text" class="meta-name <?php echo $edit_class; ?> <?php echo $edit_type_text; ?>" data-field-text="<?php echo $profile_name; ?>"><?php echo $profile_name; ?></span>
				
                <?php echo ( $user->isAuthorized() && $profile->isFollowing( $user ) ? ' <span class="follows-you"><small>'.$langscape["Follows you"].'</small></span>' : '' ); ?>
			
				<span class="last-seen">
	                <i class="icon calendar"></i>
	                <?php echo $langscape["Member since:"];?>  <?php 
					$member_since = MK_Utility::timeSince( strtotime($profile->renderDateRegistered()));
					$member_since_time = format_time($member_since);
					echo $member_since_time; ?>
	            </span>
				
				<span class="last-seen">
	                <i class="icon clock"></i>
	                <?php echo $langscape["Last seen:"];?> <?php
	                $last_seen = MK_Utility::timeSince( strtotime($profile->renderLastLogin()) );
					$last_seen_time = format_time($last_seen);
	                
	                if ( $profile->getMetaValue('last_login') == 0 ) {
	                    echo ''.$langscape["Never"].'';
	                } else {
	                    echo ucfirst($last_seen_time) . ' '.$langscape["ago"].'';
	                } ?>
	            </span>
            			
            </div>
			
			<div class="region-holder">
				
				<?php
                //Username section.
				$username = $profile->getMetaValue('username'); //Assign username variable from the profile meta.
                
				if( $username <> '' || $admin_mode == 1 ) { //Username is not blank or admin mode
					
                    if ($username == '') { //Username is blank, show placeholder. 
                        
                        $username_text = $txt_placeholder_arr['username'];
                        
                    } else { //Show username.
                    
                        $username_text = $username;
                    
                    } ?>
                
                    <span class="pure-u-1-3 meta-web">
                        <i class="user icon"></i>
                        <span style="float:none;" class=" <?php if ( $user->objectGroup()->isAdmin() && $user->isAuthorized() ) { echo $edit_class . ' ' . $edit_type_text;} ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="username" data-field-text="<?php echo $username; ?>"><?php echo $username_text; ?></span>
                    </span><?php
                    
				}
                
                //Region Section
				$region = $profile->getMetaValue('region'); //Assign region variable from the profile meta.
                
				if( $region <> '' || $admin_mode == 1  ) { 
					
                    if ($region == '') { 
                        
                        $region_text = $txt_placeholder_arr['region'];
                    
                    } else { 
                        
                        $region_text = $region;
                        
                    } ?>
				
                    <span class="pure-u-1-3 meta-web">
                        <i class="earth icon"></i>
                        <span style="float:none;" class=" <?php echo $edit_class; ?> <?php echo $edit_type_text; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="region" data-field-text="<?php echo str_replace('"',"'",$region); ?>"><?php echo $region_text; ?></span>
                    </span><?php
                    
				}
                
                //Website section
				$website = $profile->getMetaValue('website'); //Assign website variable from the profile meta.
				
                if( $website <> '' || $admin_mode ==1 ) {
                
                    if ( $website == '' ) {
                        
                        $website_text = $txt_placeholder_arr['website'];
                    
                    } else {
                        
                        $website_text=$website;
                        
                    } ?>
				
                    <span class="pure-u-1-3 meta-web">
                        <i class="network icon"></i>
                        <a target="_blank" href="http://<?php echo str_replace("http://","",$website); ?>" class="<?php echo $edit_class; ?> <?php echo $edit_type_link; ?>" data-module-name="user" data-id="<?php echo $profile_id; ?>" data-field-name="website" data-field-url="<?php echo $website; ?>"><?php echo str_replace("http://","",$website_text); ?></a>
                    </span><?php
				
                } ?>
			</div>
            
	    </div>
	     
		<div class="user-actions"><?php

            //Start follow button generation.
            if( $user->isAuthorized() && $profile->getId() != $user->getId() ) { //Logged in and not own profile.
            
                if( $user->isFollowing( $profile ) ) { //Logged in user is following profile user.
					
                    $follow_instance = $user_follower_module->searchRecords(array(
                        array('field' => 'follower', 'value' => $user->getId()),
						array('field' => 'following', 'value' => $profile->getId())
					));
						
                    $follow_instance = array_pop($follow_instance);
			
                    //Make the button.
                    $following_button = '<a data-follower-object-id="'.$follow_instance->getId().'" data-follower-id="'.$user->getId().'" data-following-id="'.$profile_id .'" data-hover-text="'.$langscape["Unfollow"].'" class="button" data-hover-class="button" href="#" rel="user unfollow"><button class="pure-button pure-button-primary pure-button-active"><span>'.$langscape["Following"].'</span></button></a>';
                    
				} else {
			
					$following_button = '<a href="#" class="button" data-hover-text="" data-hover-class="button" data-follower-id="'.$user->getId().'" data-following-id="'.$profile_id .'" rel="user follow"><button class="pure-button pure-button-primary follow-button"><span>'.$langscape["Follow"].'</span></button></a>';
					
                }  
					
            } elseif ( $profile->getId() != $user->getId() ) {
					
                $following_button = '<a href="sign-up.php" class="button en-trigger" data-modal="modal-sign-up"><button class="pure-button pure-button-primary follow-button"><span>'.$langscape["Follow"].'</span></button></a>';
			
            } else {
                
                $following_button = NULL;
			
            }
			
			echo $following_button;
            //End follow button.
            
            //Contact button.
            $email_pub = $profile->isMetaValue('email_public');
            //Freelance being used to allow contact
            $allow_contact = $profile->isMetaValue('available_for_freelance');
            if (($user->isAuthorized() && $allow_contact == 1)  && ($config->site->members->enable_contact_form)) { //User is logged in. ?>
			
                <button class="pure-button pure-button-primary contact-button en-trigger" data-modal="modal-contact-user"><span><?php echo $langscape["Contact"]; ?></span></button><?php 
            
            }
            
            //Delete user button
            if ( $user->objectGroup()->isAdmin() && $user->getId() != $profile->getId() )  { //Logged in as admin and not own profile. ?>
			
                <a rel="user delete-profile" href="<?php echo $this_filename . '?user=' . $profile_id; ?>&amp;action=delete-profile"><button class="pure-button pure-button-primary delete-button"><span><?php echo $langscape["Delete"]; ?></span></button></a><?php 
            
            } 
            
            if ( $config->site->members->enable_approval ) {
            
            	if ( ( $user->objectGroup()->isAdmin() && $user->getId() != $profile->getId() ) && !$profile->isApproved() )  { //Logged in as admin and not own profile. Approve user button ?>
			
                <a rel="user approve" href="#" data-id="<?php echo $profile_id; ?>"><button class="pure-button pure-button-primary approve-button"><span><?php echo $langscape["Approve"]; ?></span></button></a><?php 

				} elseif ( !$profile->isApproved() ) { ?>
				
				<span class="approval"><?php echo $langscape["Awaiting Approval"]; ?></span>
				<?php	
				}
            }
            
			?>
			
		</div>
        		
	</div>
    
</div>
