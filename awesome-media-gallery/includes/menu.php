        <div id="user-menu"> <!-- Navigation Starts Here -->
    
            <div class="user-menu-bg"></div>
            
            <div class="wrapper">
      
                <div class="nav pure-g-r">
    
                    <div class="pure-u-3-8">
      
                        <div class="logo-sticky"> <!-- Sticky Logo -->
                            
                            <a href="<?php echo $config->site->url; ?>"><img src="<?php echo $config->site->logo_sticky; ?>"  alt="<?php echo $meta_sitename_clean; ?>"></a>
                        
                        </div><?php 
        
                        if (isset($breadcrumb) && $breadcrumb <> '') { //Display Breadcrumb variable set from breadcrumbs.php ?>
          
                        <div class="breadcrumb"><?php 
            
                            echo $breadcrumb; ?>
                        
                        </div><?php
                        
                        } ?>
      
                    </div> <!-- BREADCRUMB/LOGO AREA -->
					
					<?php 
						 	if ( $config->site->style->emphasize_upload ) {
		                    
		                    	$uclass = "upload-button";
		                    
		                    } else {
			                    
			                    $uclass = "";
		                    
		                    }
		            ?>
					<div class="nav-main pure-u-5-8">
						<?php 
	                   	if( !$user->isAuthorized() ) { //Only Display the upload button for logged in users. DH ENGAGE 29/08. 
	               			if ( ($config->site->wordpress->strict_login) && ( !$config->site->members->disable_registration ) ) {
		               		?>
			   			<a class="menu-item <?php echo $uclass; ?>" href="sign-in.php?platform=wordpress"><span><i class="user-add icon"></i><?php echo $langscape["Sign Up"];?></span></a><?php
	               			} elseif ( !$config->site->members->disable_registration ) {
               				?>
               		
			   			<a class="menu-item <?php echo $uclass; ?>" href="sign-up.php"><span class="en-trigger" data-modal="modal-sign-up"><i class="user-add icon"></i><?php echo $langscape["Sign Up"];?></span></a><?php
	                    	}
                    	
	                    } else {
                                              
	                      
	                   		if ( $config->site->members->enable_approval )  {
		                   		if ( $user->isAuthorized() && $user->getMetaValue('approved') && (!$config->site->members->disable_uploads) ) 			{ 
		                   		?>
	                        	<a href="upload-choose.php" id="upload-button" class="menu-item en-trigger <?php echo $uclass; ?>" data-modal="modal-choose"><i class="upload-2 icon"></i><?php echo $langscape["Upload"];?></a><?php
	                        
								}
							} elseif ( !$config->site->members->disable_uploads ) {
								 ?>
	                        	<a href="upload-choose.php" id="upload-button" class="menu-item en-trigger <?php echo $uclass; ?>" data-modal="modal-choose"><i class="upload-2 icon"></i><?php echo $langscape["Upload"];?></a><?php
							}
						}	 ?>
						
                    	<span id="members-button" class="menu-item"><i class="users icon"></i><a href="members.php"><?php echo $langscape["Members"];?></a></span><?php 

							if ( !empty($config->site->media->enable_audio) && !empty($config->site->soundcloud->app_id) && ($config->site->soundcloud->enabled) && ($gallery_audio != NULL) ) { ?> 
      
                         <!-- Audio Menu Starts -->
                        <span class="nav-dropdown menu-item" data-dropdown="#audio-drop" data-vertical-offset="10" data-horizontal-offset="2">
                            <i class="fa fa-music icon"></i>
                            <a href="#"><?php echo $langscape["Audios"];?></a>
                            <i class="arrow-down-6 icon"></i>
                        </span>
                        
                        <div id="audio-drop" class="dropdown dropdown-tip dropdown-anchor-right dropdown-relative">
                            <ul class="dropdown-menu"><?php

                                foreach($gallery_audio as $gallery) {
                    
                                    $total_records = $image_module->getTotalRecords(array(
                                        array('field' => 'gallery', 'value' => $gallery->getId())
                                    )); ?>
                                
                                    <li>
                                        <a href="gallery/audio/<?php print urlencode($gallery->getName()); ?>"><div class="badge"><?php echo $total_records; ?></div><span><?php echo $gallery->getName(); ?></span></a>
                                    </li><?php
                                } ?>
                            
                            </ul>
        
                        </div>
                        <!-- Audio Menu Ends --><?php
                    
						}

      
                    	if ( !empty($config->site->media->enable_videos) && $gallery_videos != NULL ) { ?>
      
                         <!-- Videos Menu Starts -->
                        <span class="nav-dropdown menu-item" data-dropdown="#video-drop" data-vertical-offset="10" data-horizontal-offset="2">
                            <i class="fa fa-youtube-play icon"></i>
                            <a href="#"><?php echo $langscape["Videos"];?></a>
                            <i class="arrow-down-6 icon"></i>
                        </span>
                        
                        <div id="video-drop" class="dropdown dropdown-tip dropdown-anchor-right dropdown-relative">
                            <ul class="dropdown-menu"><?php

                                foreach($gallery_videos as $gallery) {
                    
                                    $total_records = $image_module->getTotalRecords(array(
                                        array('field' => 'gallery', 'value' => $gallery->getId())
                                    )); ?>
                                
                                    <li>
                                        <a href="gallery/video/<?php echo urlencode($gallery->getName()); ?>"><div class="badge"><?php echo $total_records; ?></div><span><?php echo $gallery->getName(); ?></span></a>
                                    </li><?php
                                } ?>
                            
                            </ul>
        
                        </div>
                        <!-- Video Menu Ends --><?php
                    
						}
      
                    	if ( !empty($config->site->media->enable_images) && $gallery_images != NULL ) { ?>
       
                         <!-- Photos Menu Starts -->
                        <span class="nav-dropdown menu-item" data-dropdown="#gallery-drop" data-vertical-offset="10" data-horizontal-offset="2">
                            <i class="pictures icon"></i>
                            <a href="#"><?php echo $langscape["Images"];?></a>
                            <i class="arrow-down-6 icon"></i>
                        </span>
                        
                        <div id="gallery-drop" class="dropdown dropdown-tip dropdown-anchor-right dropdown-relative">
                            
                            <ul class="dropdown-menu"><?php
            
                                //$gallery_list_images      = MK_RecordModuleManager::getFromType('image_gallery');
                                //$search_criteria_images[] = array('literal' => "(`type_gallery` = 1)");
                                //$gallery_images           = $gallery_list_images->searchRecords($search_criteria_images);
            
                                foreach($gallery_images as $gallery) {
                                    
                                    $total_records = $image_module->getTotalRecords(array(
                                        array('field' => 'gallery', 'value' => $gallery->getId())
                                    )); ?>
                                    
                                    <li>
                                        <a href="gallery/image/<?php print urlencode($gallery->getName()); ?>"><div class="badge"><?php echo $total_records; ?></div><span><?php echo $gallery->getName(); ?></span></a>
                                    </li><?php
                                } ?>
                                
                            </ul>
                        
                        </div>
                        <!-- Photos Menu Ends --><?php
                    
	                    }
	                    
	                    //SHOW USER DROP DOWN
	                    if( $user->isAuthorized() ) {
          
                        $user_notification_module = MK_RecordModuleManager::getFromType('user_notification');
      
                        $activity_feed_unread = $user_notification_module->getTotalRecords(array(
                            array('field' => 'user', 'value' => $user->getId()),
                            array('field' => 'public', 'value' => 0),
                            array('field' => 'unread', 'value' => 1)
                        )); ?>        
                        
                        <!-- My Account Menu Starts -->
                        <span class="nav-dropdown menu-item" data-dropdown="#myaccount-drop" data-vertical-offset="10" data-horizontal-offset="8">
                        
                            <span id="mini-avatar">
                                <img src="library/thumb.php?f=<?php echo ($user->getAvatar() ? $user->getAvatar() : $config->site->default_avatar ); ?>&amp;h=24&amp;w=24&amp;m=crop" alt="">
                            </span>
                
                            <span id="user-name"><?php echo $user->getDisplayName(); ?></span>
                            <i class="arrow-down-6 icon"></i>
                        </span>
          
                        <div id="myaccount-drop" class="dropdown dropdown-tip dropdown-anchor-right dropdown-relative">
           
                            <ul class="dropdown-menu">
                                
                                <li><a href="<?php echo getProfileUrl($user->getId()); ?>"><i class="user icon"></i><span><?php echo $langscape["My Profile"];?></span></a></li>
              
                                <li><a href="<?php echo getProfileUrl($user->getId()); ?>/activity"><i class="clock icon"></i><span><?php echo $langscape["Activity Feed"];?></span></a></li>
								<?php if ( !empty($config->site->media->enable_images) ) { ?>
                                <li><a href="<?php echo getProfileUrl($user->getId()); ?>/images"> <i class="pictures icon"></i><span><?php echo $langscape["Images"];?></span></a></li>
								<?php } ?>
								<?php if ( !empty($config->site->media->enable_videos) ) { ?>
                                <li><a href="<?php echo getProfileUrl($user->getId()); ?>/videos"><i class="fa fa-youtube-play icon"></i><span><?php echo $langscape["Videos"];?></span></a></li>
								<?php } ?>
								<?php if ( !empty($config->site->media->enable_audio) ) { ?>
                                <li><a href="<?php echo getProfileUrl($user->getId()); ?>/audios"><i class="fa fa-music icon"></i><span><?php echo $langscape["Audios"];?></span></a></li>
								<?php } ?>
                                <li><a href="<?php echo getProfileUrl($user->getId()); ?>/likes"><i class="heart icon"></i><span><?php echo $langscape["Favorites"];?></span></a></li>
              
                                <li><a href="<?php echo getProfileUrl($user->getId()); ?>/followers"><i class="flow-tree icon"></i><span><?php echo $langscape["Followers"];?></span></a></li>
              
                                <li><a href="<?php echo getProfileUrl($user->getId()); ?>/following"><i class="flow-branch icon"></i><span><?php echo $langscape["Following"];?></span></a></li>
              
                                <li class="dropdown-divider"></li><?php 
              
                                if (empty($_SESSION["OAUTH_ACCESS_TOKEN"])) { //User is not logged in using a social network, show the change password link. ?>
              
                                    <li><a href="#" class="en-trigger" data-modal="modal-change-password"><i class="user-add icon"></i><span><?php echo $langscape["Change Password"];?></span></a></li><?php
                                
                                } ?>
              
                                <li><a href="sign-out.php"><i class="logout icon"></i><span><?php echo $langscape["Sign Out"];?></span></a></li>
            
                            </ul>
                        
                        </div>
                        <!-- My Account Menu Ends --><?php
            
                        } elseif ( !$config->site->wordpress->strict_login ) { // User is not logged in ?>
                        <a class="menu-item" href="sign-in.php"><span class="en-trigger" data-modal="modal-sign-in"><i class="key icon"></i><?php echo $langscape["Sign In"];?></span></a><?php 
                        } elseif ( $config->site->wordpress->strict_login ) { 
                         ?>
                        <a class="menu-item" href="sign-in.php?platform=wordpress"><span><i class="key icon"></i><?php echo $langscape["Sign In"];?></span></a><?php 
						} ?>
					</div><!-- End Two Thirds -->
          
				</div><!-- End Row -->
			
            </div><!-- Wrapper Ends -->

        </div><!-- End Navigation -->