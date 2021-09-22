<a href="#menu" id="menuLink" class="menu-link">
    <!-- Hamburger icon -->
    <span></span>
</a>

<div id="menu">
    <div class="pure-menu pure-menu-open">

        <ul class="pure-menu-list">
            
	<?php 
   	if( !$user->isAuthorized() && !$config->site->wordpress->strict_login ) { //Only Display the upload button for logged in users. DH ENGAGE 29/08. 
			if ( !$config->site->members->disable_registration ) {
			?>
	
			<li class="pure-menu-item"><a href="sign-up.php" class="pure-menu-link"><i class="user-add icon"></i><?php echo $langscape["Sign Up"];?></a></li><?php
			}
	
    } elseif ( !$user->isAuthorized() && $config->site->wordpress->strict_login ) { ?>

			<li class="pure-menu-item"><a href="sign-in.php?platform=wordpress" class="pure-menu-link"><i class="user-add icon"></i><?php echo $langscape["Sign Up"];?></a></li><?php

    
    } else {
    
        if (!empty($config->site->style->emphasize_upload) && ( $config->site->style->emphasize_upload ) ) {
        
        	$uclass = "upload-button";
        
        } else {
            $uclass = "";
        }
   
      
   		if ( $config->site->members->enable_approval )  {
       		if ( $user->isAuthorized() && $user->getMetaValue('approved') && (!$config->site->members->disable_uploads) ) 			{ 
       		?>
        	<li class="pure-menu-item"><a href="upload-choose.php" id="upload-button" class="pure-menu-link <?php echo $uclass; ?>"><i class="upload-2 icon"></i><?php echo $langscape["Upload"];?></a></li><?php
        
			}
		} elseif ( !$config->site->members->disable_uploads ) {
			 ?>
        	<li class="pure-menu-item"><a href="upload-choose.php" id="upload-button" class="pure-menu-link <?php echo $uclass; ?>"><i class="upload-2 icon"></i><?php echo $langscape["Upload"];?></a></li><?php
		}
	}	 ?>
<?php 
	if( $user->isAuthorized() ) { ?>
	
            <li class="pure-menu-item"><a href="<?php echo getProfileUrl($user->getId()); ?>" class="pure-menu-link"><i class="user icon"></i><span><?php echo $langscape["My Profile"];?></span></a></li>

            <li class="pure-menu-item"><a href="<?php echo getProfileUrl($user->getId()); ?>/activity" class="pure-menu-link"><i class="clock icon"></i><span><?php echo $langscape["My Activity"];?></span></a></li>
			<?php if ( !empty($config->site->media->enable_images) ) { ?>
            <li class="pure-menu-item"><a href="<?php echo getProfileUrl($user->getId()); ?>/images" class="pure-menu-link"> <i class="pictures icon"></i><span><?php echo $langscape["My Images"];?></span></a></li>
			<?php } ?>
			<?php if ( !empty($config->site->media->enable_videos ) ) { ?>
            <li class="pure-menu-item"><a href="<?php echo getProfileUrl($user->getId()); ?>/videos" class="pure-menu-link"><i class="fa fa-youtube-play icon"></i><span><?php echo $langscape["My Videos"];?></span></a></li>
			<?php } ?>
			<?php if ( !empty($config->site->media->enable_audio ) ) { ?>
            <li class="pure-menu-item"><a href="<?php echo getProfileUrl($user->getId()); ?>/audios" class="pure-menu-link"><i class="fa fa-music icon"></i><span><?php echo $langscape["My Audios"];?></span></a></li>
			<?php } ?>
            <li class="pure-menu-item"><a href="<?php echo getProfileUrl($user->getId()); ?>/likes" class="pure-menu-link"><i class="heart icon"></i><span><?php echo $langscape["My Favorites"];?></span></a></li>

            <li class="pure-menu-item"><a href="<?php echo getProfileUrl($user->getId()); ?>/followers" class="pure-menu-link"><i class="flow-tree icon"></i><span><?php echo $langscape["My Followers"];?></span></a></li>

            <li class="pure-menu-item"><a href="<?php echo getProfileUrl($user->getId()); ?>/following" class="pure-menu-link"><i class="flow-branch icon"></i><span><?php echo $langscape["My Following"];?></span></a></li>

            <li class="dropdown-divider"></li><?php 

            if (empty($_SESSION["OAUTH_ACCESS_TOKEN"])) { //User is not logged in using a social network, show the change password link. ?>

            <li class="pure-menu-item"><a href="change-password.php" class="pure-menu-link"><i class="user-add icon"></i><span><?php echo $langscape["Change Password"];?></span></a></li><?php
            
            } ?>

            <li><a href="sign-out.php" class="pure-menu-link"><i class="logout icon"></i><span><?php echo $langscape["Sign Out"];?></span></a></li>
            
			<?php
            } elseif ( !$config->site->wordpress->strict_login ) { // User is not logged in ?>
            <li class="pure-menu-item"><a href="sign-in.php" class="pure-menu-link"><i class="key icon"></i><?php echo $langscape["Sign In"];?></a><?php 
             } elseif ( $config->site->wordpress->strict_login ) {
             ?>
            <li class="pure-menu-item"><a href="sign-in.php?platform=wordpress" class="pure-menu-link"><i class="key icon"></i><?php echo $langscape["Sign In"];?></a><?php 
	         }
	         ?>
            <li class="pure-menu-item menu-item-divided">
            	<a href="members.php" class="pure-menu-link">
	            	<i class="users icon"></i><?php echo $langscape["Members"];?>
	            </a>
	        </li>

            <li class="pure-menu-item">
            	<a href="about.php" class="pure-menu-link">
	            	<i class="question icon"></i><?php echo $langscape["About Us"];?>
	            </a>
	        </li>
	                    
            <li class="pure-menu-item">
            	<a href="contact.php" class="pure-menu-link">
	            	<i class="mail icon"></i><?php echo $langscape["Email Us"];?>
	            </a>
	        </li>
	        
            <li class="pure-menu-item">
            	<a href="terms.php" class="pure-menu-link">
	            	<i class="newspaper icon"></i><?php echo $langscape["Terms"];?>
	            </a>
	        </li>

            <li class="pure-menu-item">
            	<a href="privacy.php" class="pure-menu-link">
	            	<i class="cone icon"></i><?php echo $langscape["Privacy"];?>
	            </a>
	        </li>

			<?php if ($config->site->media->enable_images ) { ?>
             <!-- Images Menu Starts -->
            <li class="pure-menu-item menu-item-divided">
                <a href="gallery/images" class="pure-menu-link"><i class="pictures icon"></i><?php echo $langscape["Images"];?></a>
            </li>

		<?php
        foreach($gallery_images as $gallery) {
            
            $total_records = $image_module->getTotalRecords(array(
                array('field' => 'gallery', 'value' => $gallery->getId())
            )); ?>
            
            <li class="pure-menu-item">
                <a href="gallery/image/<?php print urlencode($gallery->getName()); ?>" class="pure-menu-link"><div class="badge"><?php echo $total_records; ?></div><span><?php echo $gallery->getName(); ?></span></a>
            </li><?php
        } ?>
			<?php } ?>

			<?php if ($config->site->media->enable_videos ) { ?>
             <!-- Videos Menu Starts -->
            <li class="pure-menu-item menu-item-divided">
                <a href="media/videos" class="pure-menu-link"><i class="fa fa-youtube-play icon"></i><?php echo $langscape["Videos"];?></a>
            </li>

		<?php
        foreach($gallery_videos as $gallery) {

            $total_records = $image_module->getTotalRecords(array(
                array('field' => 'gallery', 'value' => $gallery->getId())
            )); ?>
        
            <li class="pure-menu-item">
                <a href="gallery/video/<?php echo urlencode($gallery->getName()); ?>" class="pure-menu-link"><div class="badge"><?php echo $total_records; ?></div><span><?php echo $gallery->getName(); ?></span></a>
            </li><?php
        } ?>
			<?php } ?>

			<?php if ( !empty($config->site->media->enable_audio) && !empty($config->site->soundcloud->app_id) && ($config->site->soundcloud->enabled) && ($gallery_audio != NULL) ) { ?> 
             <!-- Audio Menu Starts -->
            <li class="pure-menu-item menu-item-divided">
                <a href="media/audios" class="pure-menu-link"><i class="fa fa-music icon"></i><?php echo $langscape["Audios"];?></a>
	        </li>
		
		<?php
        foreach($gallery_audio as $gallery) {

            $total_records = $image_module->getTotalRecords(array(
                array('field' => 'gallery', 'value' => $gallery->getId())
            )); ?>
        
            <li class="pure-menu-item">
                <a href="gallery/audio/<?php print urlencode($gallery->getName()); ?>" class="pure-menu-link"><div class="badge"><?php echo $total_records; ?></div><span><?php echo $gallery->getName(); ?></span></a>
            </li><?php
        } ?>
			<?php } ?>
<?php
		if ($config->site->header->enable_search) { //ENABLE SEARCH IN ADMIN ?>

        <li class="pure-menu-item menu-item-divided">
            <a href="#" class="pure-menu-link"><i class="search icon"></i><?php echo $langscape["Search"];?></a>
        </li>

        <li class="pure-menu-item">    
            <div class="search-box"><!-- Search Box --> 
                <form name="form" autocomplete="on" enctype="multipart/form-data" method="get" action="index.php" class="pure-form">

                    <input placeholder="<?php echo $langscape["Search for Media"];?>" type="text" class="data input-text pure-input" name="s" id="s" value="">
                    
                </form>
            </div>
        </li><?php 
         
		} ?>
					
        <li class="pure-menu-item menu-item-divided">
            <a href="#" class="pure-menu-link"><i class="chat icon"></i><?php echo $langscape["Social Links"];?></a>
        </li>
	<?php 
        if (!empty($config->site->footer->facebook)) { ?>
            <li class="pure-menu-item"><a href="<?php echo $config->site->footer->facebook; ?>" target="_self"><i class="fa fa-facebook icon" title="facebook page"></i>Facebook</a></li><?php
        }
        
        if (!empty($config->site->footer->twitter)) { ?>
            <li class="pure-menu-item"><a href="<?php echo convertTwitterUsernameUrl($config->site->footer->twitter); ?>" target="_self"><i class="fa fa-twitter icon" title="twitter page"></i>Twitter</a></li><?php
        }
        
        if (!empty($config->site->footer->pinterest)) { ?>
            <li class="pure-menu-item"><a href="<?php echo $config->site->footer->pinterest; ?>" target="_self"><i class="fa fa-pinterest icon" title="pinterest page"></i>Pinterest</a></li><?php
        }

        if (!empty($config->site->footer->instagram)) { ?>
            <li class="pure-menu-item"><a href="<?php echo $config->site->footer->instagram; ?>" target="_self"><i class="fa fa-instagram icon" title="instagram page"></i>Instagram</a></li><?php
        }

        if (!empty($config->site->footer->google_plus)) { ?>
            <li class="pure-menu-item"><a href="<?php echo $config->site->footer->google_plus; ?>" target="_self"><i class="fa fa-google-plus icon" title="google-plus page"></i>Google+</a></li><?php
        }

        if (!empty($config->site->footer->flickr)) { ?>
            <li class="pure-menu-item"><a href="<?php echo $config->site->footer->flickr; ?>" target="_self"><i class="fa fa-flickr icon" title="flickr page"></i>Flickr</a></li><?php
        }

        if (!empty($config->site->footer->youtube)) { ?>
            <li class="pure-menu-item"><a href="<?php echo $config->site->footer->youtube; ?>" target="_self"><i class="fa fa-youtube icon" title="youtube page"></i>Youtube</a></li><?php
        }

        if (!empty($config->site->footer->vimeo)) { ?>
            <li class="pure-menu-item"><a href="<?php echo $config->site->footer->vimeo; ?>" target="_self"><i class="fa fa-vimeo-square icon" title="vimeo page"></i>Vimeo</a></li><?php
        }
		?>

        </ul>
    </div>
</div>