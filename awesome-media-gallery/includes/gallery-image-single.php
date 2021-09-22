<?php 

/*NEEDS PHP 5.3 TO WORK
include_once('../library/socialworth.php'); 
	
$networks = array('facebook', 'googleplus', 'pinterest', 'twitter');
$page_url      = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$social_count = new SocialWorth($networks);
$worth  = $social_count->value($page_url);
*/
$worth['facebook']= 0;
$worth['googleplus'] = 0;
$worth['pinterest'] = 0;
$worth['twitter'] = 0;

?>
<?php 

//Set Page Variables
$enable_guest_comments = $config->site->enable_guest_comments;
$enable_guest_likes = $config->site->enable_guest_likes;

$author           = $image->objectUser();
$author_id        = $author->getId();
$author_name      = $author->getDisplayName();
$author_avatar    = $author->getAvatar();
$gallery_id       = $gallery_id; //Retrieved in BreadCrumbs
$gallery_name     = $image->objectGallery()->getName();
$image_title      = $image->getTitle();
$image_type		  = $image->getMetaValue('type_gallery');
$video_url		  = $image->getMetaValue('video_url');
$audio_url		  = $image->getMetaValue('audio_url');
$image_type_name  = getImageTypeName($image_type);
$total_favourites = $image->getTotalFavourites(); //Get total favorties count
$build_href_prev  = ''; //Set prev button
$build_href_next  = ''; //Set next button

if ( !empty( $previous_image ) ) {
  $build_href_prev = $config->site->url . getImageTypeName($previous_image->getTypeGallery()) . '/' . $previous_image->getImageSlug();
  //$build_href_prev .= ( !empty( $author ) ? '&amp;user='.$author->getId() : '' );
}

if ( !empty( $next_image ) ) {
  $build_href_next = $config->site->url . getImageTypeName($next_image->getTypeGallery()) . '/' . $next_image->getImageSlug();
  //$build_href_next .= ( !empty( $author ) ? '&amp;user=' . $author->getId() : '' );
}

/***** Setup TAGS for Single Image *****/
if( $tags = $image->getTags() )	{
	$tags = explode(',', $tags);
}


//BUILD LINK BASED ON VIDEO VS. IMAGE VS. AUDIO
if ( getImageTypeName($image_type) == 'video' ) { //VIDEO 2

    $img_string = $image->getImage() . '&amp;q=' . $config->site->media->jpg_quality_single . '&amp;c=' . $config->site->media->png_compression;
    $popup_string = $video_url;

} elseif ( getImageTypeName($image_type) == 'image' ){ //IMAGE 1

	if ($config->site->media->enable_stretched_image) {
			
	    $img_string = 'library/thumb.php?f='.$image->getImage().'&amp;m=width&amp;a=true&amp;w=' . $mwsi . '&amp;h=' . $mhsi . '&amp;q=' . $config->site->media->jpg_quality_single . '&amp;c=' . $config->site->media->png_compression;
	    
    } else {
		
		//check image size - if less than max single image width, supply original width
		$image_size = getimagesize($image->getImage());
		if ($image_size[0] < $config->site->values->width_single_image) { 
			
	    	$img_string = 'library/thumb.php?f='.$image->getImage().'&amp;m=width&amp;a=true&amp;w=' . $image_size[0] . '&amp;q=' . $config->site->media->jpg_quality_single . '&amp;c=' . $config->site->media->png_compression;
	    	
		} else {
			
			//make sure any huge images are shrunk
	    	$img_string = 'library/thumb.php?f='.$image->getImage().'&amp;m=contain&amp;a=true&amp;w=' . $mwsi . '&amp;h=' . $mhsi . '&amp;q=' . $config->site->media->jpg_quality_single . '&amp;c=' . $config->site->media->png_compression;
		
		}
	    
    }
    
    $popup_string = $config->site->url . $image->getImage();
    
    
} elseif ( getImageTypeName($image_type) == 'audio' ) { //AUDIO 3

    $img_string = $image->getImage();
    $popup_string = $audio_url;
}

?>
<script type="text/javascript">
var image_url = '<?php echo $popup_string; ?>';
</script>
<?php

$exifdata = '';
//SETUP EXIF DATA
if (($config->site->media->enable_exif) && ($image_type == 1)) {
	if  (exif_imagetype($popup_string) == 2) {
		$exifdata = @exif_read_data($popup_string); 
	}
	
}


/********************* IMAGE COMMENTING *********************/

//If commenting is turned on, setup the arroy.
if( !empty($config->site->media->comments_type) && ( $config->site->media->comments_type == 'DEFAULT') ) {
  
  $page = MK_Request::getQuery('comments-page', 1);
  
  $paginator = new MK_Paginator($page, 20);

  // Comment form
  $settings = array(
    'attributes' => array(
      'class' => 'clear-fix standard comment',
      'action' => $this_filename.'?image='.$image->getId().'#leave-comment'
    )
  );

  $fields = array(
    'comment' => array(
      'label' => 'Comment'.( !$user->isAuthorized() ? ' as Guest' : '' ),
      'type' => 'textarea',
      'validation' => array(
        'instance' => array(),
        'length_max' => array(1000)
      ),
      'attributes' => array(
        'placeholder' => ''.$langscape["Write a comment..."].''
      )
    ),
    'user' => array(
      'attributes' => array(
        'type' => 'hidden',
      ),
      'value' => $user->getId()
    ),
    'image' => array(
      'attributes' => array(
        'type' => 'hidden',
      ),
      'value' => $image->getId()
    ),
    'post-comment' => array(
      'type' => 'submit',
      'attributes' => array(
        'value' => ''.$langscape["Post Comment"].'',
        'class' => 'pure-button'
      )
    )
  );

  $form = new MK_Form($fields, $settings);
}

//Setup the editing classes
if( ($user->isAuthorized() && ($user->getId() == $author->getId())) || ($user->objectGroup()->isAdmin()) ) {
		
    $edit_class          ="edit";
    $edit_type_text      ="edit-text";
    $edit_type_link      ="edit-link";
    $edit_type_textarea  ="edit-textarea";
    $edit_type_date      ="edit-date";
    $edit_type_yesno     ="edit-yesno";	
    $edit_type_tags      ="edit-tags";
    $edit_type_galleries ="edit-gallery";	
    $admin_mode          = 1;
    
} else {

    $edit_class          = "";
    $edit_type_text      = "";
    $edit_type_link      = "";
    $edit_type_textarea  = "";
    $edit_type_date      = "";
    $edit_type_yesno     = "";
    $edit_type_tags      = "";
    $edit_type_galleries = "";
    $admin_mode          = 0;
    
}

//GET GALLERIES FOR EDITABLE
$num = 0;
$gallery_list = MK_RecordModuleManager::getFromType('image_gallery'); //Gallery Info

$gallery_array = $gallery_list->searchRecords(array(array('field' => 'type_gallery', 'value' => $image_type)));

if($gallery_array != NULL) {

    foreach($gallery_array as $gallery) {
 
        $gallery_id_array[$num] = $gallery->getId();
        $gallery_name_array[$num] = $gallery->getName();
        $num++;
    }

    $gallery_id_array_encoded = json_encode($gallery_id_array);
    $gallery_name_array_encoded = json_encode($gallery_name_array);
    $galleries_array_combined = array_combine($gallery_id_array,$gallery_name_array);

    $num = 0;
    $galleries_data = "{ ";

    $total = sizeof($gallery_id_array);

    foreach($gallery_id_array as $value) {
        $galleries_data .= "'" . $value . "' : '" . $gallery_name_array[$num] . "'";
        if ($total<>$num) { $galleries_data .= ",";}
        $num++;	
    }
    $galleries_data .= " }"; 
}
?>
<?php

	if ( ( ($deviceType <> 'phone') || !$config->site->mobile->enable_responsive_phone) && ($config->site->ads->enable_media_top) ) { ?>

	<!-- 728x90 Ad Banner -->
	<?php include ('includes/ad-top.php'); ?> 

<?php 
	} ?>

<!-- Content Section GALLERY Starts Here -->
<section class="content gallery-single comments pure-u-19-24">                 

	<!-- Single Image Starts -->
    <div class="single-image">
    
        <?php 
        
        if ( $image_type == 1 ) { //Type is image. ?>
		<!-- Container Starts -->
        <div class="image-container image-large loading" role="main-gallery">
                
                <?php 
        
                //Extension check.
                $extension = explode('.', $image->getImage());
                $extension = array_pop($extension); ?>
                
                <a href="<?php echo $popup_string; ?>" class="fancybox-media" rel="group" title="<?php echo $image_title; ?>" alt="<?php echo $image_type_name . '/' .$image->getImageSlug(); ?>"><img data-src="<?php echo $img_string; ?>" alt="<?php echo $image->getTitle(); ?>" src=""></a><?php 
        
        } elseif ( $image_type == 2 ) { //Type is video ?>
        	<!-- Container Starts -->
            <div class="image-container image-large">
            
                <?php
                
                if (!empty($config->site->media->enable_autoplay)) {
	                $ap=$config->site->media->enable_autoplay;
                }else{
	                $ap=0;
                }
                
                if ( isYouTube ( $popup_string ) ) { //Its YouTube
                
                    $embed_string = convertYouTubeUrl( $popup_string ); //Convert url in to embed version ?>
            
                    <iframe id="ytplayer" type="text/html" src="<?php echo $embed_string . '?autoplay=' . $ap; ?>&amp;origin=<?php echo $config->site->url; ?>&amp;wmode=transparent&amp;theme=light" frameborder="0" /></iframe><?php
                
                } elseif ( isVimeo ( $popup_string ) ) { //Its Vimeo

                    $embed_string = convertVimeoUrl($popup_string); //Convert url in to embed version ?>

                    <iframe src="<?php echo $embed_string . '?autoplay=' . $ap; ?>" width="755" height="425" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><?php

                } elseif ( isVine ( $popup_string ) ) { //Its Vine

                    $embed_string = convertVine($popup_string); //Convert url in to embed version ?>

                    <iframe src="<?php echo $embed_string; ?>" width="756" height="756" frameborder="0" scrolling="no" seamless="seamless" webkitallowfullscreen="webkitAllowFullScreen" mozallowfullscreen="mozallowfullscreen" allowfullscreen="allowfullscreen"></iframe><?php

                }
                
        } elseif ( ( $image_type = 3 ) && $config->site->soundcloud->enabled ) {  //type is audio. 
        
        	if ($config->site->media->audio_player == "javascript") {
        ?>
        
        		<?php echo $audio_url; ?>
        	
		<?php 
			
			} elseif ($config->site->media->audio_player == "soundcloud") { 
			
				require_once 'library/Soundcloud.php';
				
				if (!empty($config->site->media->enable_autoplay)) {
	                $ap='true';
                }else{
	                $ap='false';
                }
                
				// create a client object with your app credentials
				$client = new Services_Soundcloud($config->site->soundcloud->app_id, $config->site->soundcloud->secret);
				$client->setCurlOptions(array(CURLOPT_FOLLOWLOCATION => 1));
				
				// get a tracks oembed data
				$track_url = $audio_url;
				$embed_info = json_decode($client->get('oembed', array('url' => $track_url, 'artwork_url' => $track_url, 'auto_play' => $ap)));
				
				// render the html for the player widget
				echo '<div class="image-container image-large">' . $embed_info->html;
 
			} ?>
		
		<?php 
		} ?>
		
        <div class="meta-holder">
        
            <div class="meta-data holder">
                <!-- Meta Data --><?php 
                
                echo returnFavouriteHeart();
                
                if ( !empty($config->site->media->comments_type) && ( $config->site->media->comments_type <> 'DISABLED') )  { //If Image comments are turned on. $config->extensions->gallery->image_comments ?>
                
                    <span class="pure-u meta-comment stats">
                        <i class="comment icon"></i>
                        <span class="text">
                        <?php
                
	                if ( $config->site->media->comments_type == 'DEFAULT' ) { 
	                	echo ( $image->getTotalComments() > 999 ? '999+' : $image->getTotalComments() );
	                	
	                } else if ( $config->site->media->comments_type == 'FACEBOOK' ) {
		            ?>
		            	<fb:comments-count href=<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>></fb:comments-count>
	
	                <?php
	                }
	                ?>
                        </span>
	                </span>
                <?php
                } // IMAGE COMMENTS
                ?>
                
                <!-- View Count -->
                <span class="pure-u meta-views stats">
                    <i class="eye icon"></i>
                    <span class="text"><?php echo number_format($image->getViews()); ?></span>
                </span>
                              
                <!-- Posted X Ago -->
                <span class="meta-date pure-u">
                    <i class="clock icon"></i><?php echo time_since(time() - strtotime($image->getDateAdded())); ?> <?php echo $langscape["ago"];?>
                </span>
            
            </div>
        
        </div>

    </div><!-- Container Ends --><?php 
    
	//Check for more images to be used in the carousel and button.
	$more_images_paginator = new MK_Paginator(1, 30);
	$more_images_options = NULL;

	$options_s = array('field' => 'gallery', 'value' => $image->getGallery());
	
	if ($config->site->media->enable_approval) {
	
		$options_t = array('field' => 'approved', 'value' => 1);
	
	} else {
		
		$options_t = array();
	
	}
	
	$search_array = ($options_t)? array($options_s,$options_t):array($options_s);

	$more_images = $image_module->searchRecords(
       	$search_array, 
        $more_images_paginator, 
        $more_images_options); ?>   
    
    <!-- Buttons Start -->
    <div class="meta-data buttons"><?php
	    	
        //View all button
        if( !empty( $more_images ) && ( ( count( $more_images ) ) > 1 ) ) { //There are more images! ?>
		
            <a href="#" class="prev-next"><button id="fancybox-view-button" class="pure-button pure-button-primary"><?php echo $langscape["View All"];?></button></a><?php
        
        }


        if ( $build_href_prev <> '' ) { 
            
            echo '<a href="' . $build_href_prev . '" class="prev-next"><button class="pure-button pure-button-primary">' . $langscape["Prev"] . '</button></a>';
            
        }
        
        if( $build_href_next <> '' ) {

            echo '<a href="' . $build_href_next . '" class="prev-next"><button class="pure-button pure-button-primary">' . $langscape["Next"] . '</button></a>';
            
        } 
           	
	    	
	    //Delete Image Button
	    if( $image->canDelete( $user ) ) { //User can edit so show them a button. ?> 
        
            <a rel="image delete-image" href="<?php echo $this_filename . '?image=' . $image->getId(); ?>&amp;action=delete-image"><button class="pure-button pure-button-primary delete-button"><?php echo $langscape["Delete"];?></button></a><?php
            
		} 
      
        //Full Image Button
        if($user->isAuthorized() && $config->site->media->enable_view_original ) { //If logged in then show download full image. 
      
            if ( $image_type == 1 ) { //Image. ?>
                <a id="download-image" title="<?php echo $langscape["View original source file"];?>" target="_blank" href="<?php print $image->getImage(); ?>" data-img="<?php print basename($image->getImage()); ?>"><button class="pure-button pure-button-primary viewfull-button"><?php echo $langscape["View Full Size"];?></button></a><?php
        
            } elseif ( $image_type == 2 ) { //Video. ?>
  
                <a title="<?php echo $langscape["View original source file"];?>" target="_blank" href="<?php print $popup_string; ?>"><button class="pure-button pure-button-primary viewfull-button"><?php echo $langscape["View Original"];?></button></a><?php
            
            } elseif ( $image_type == 3 ) { //Audio. ?>
  
                <a title="<?php echo $langscape["View original source file"];?>" target="_blank" href="<?php print $popup_string; ?>"><button class="pure-button pure-button-primary viewfull-button"><?php echo $langscape["Visit Soundcloud"];?></button></a><?php
            
            }
      
        } 

        //Source Link Button
        $media_source = $image->getSource();
        
        if ( !empty($media_source) && $config->site->media->enable_source ) {
        ?>
			<!-- Goto Source Page -->	  
	        <a title="View original source file" target="_blank" href="http://<?php echo $media_source; ?>"><button class="pure-button pure-button-primary viewfull-button"><?php echo $langscape["Goto Source"];?></button></a>
	        <?php

	    }


	    //Report Image Button
	    if( $user->isAuthorized() && $user->getId() != $image->getUser() && $config->site->enable_reporting ) { ?>

            <a rel="image report-image" title="<?php echo $langscape["Report this image as inappropriate"];?>" data-image-id="<?php echo $image->getId(); ?>"><button class="pure-button pure-button-primary report-button"><?php echo $langscape["Report"];?></button></a><?php
		
        }
	    
	    //Featured Image Button + Logged in as Admin
        if( $user->objectGroup()->isAdmin() && $user->isAuthorized() ) {
	    
            if( $image->isFeatured() ) { 
	        
                $f_Query = 'remove-featured';
                $f_Class = 'pure-button-active';
                $f_Text = ''.$langscape["Featured"].'';
                $f_id = 'featured_button';
	        
            } else {  
	        
                $f_Query = 'add-featured';
                $f_Class = '';
                $f_Text = ''.$langscape["Feature"].'';
                $f_id = '';
            
            } ?>

            <a data-image-id="<?php echo $image->getId(); ?>" href="<?php echo $this_filename . '?image=' . $image->getId() ?>&amp;action=<?php echo $f_Query; ?>"><button class="pure-button <?php echo $f_Class; ?> pure-button-primary action-button" id="<?php echo $f_id; ?>"><span><?php echo $f_Text; ?></span></button></a><?php
        
	    }


	    //Carousel Image Button + Logged in as Admin
        if( $user->objectGroup()->isAdmin() && $user->isAuthorized() ) {
	    
            if( $image->isCarousel() ) { 
	        
                $f_Query = 'remove-carousel';
                $f_Class = 'pure-button-active';
                $f_Text = ''.$langscape["Added to Carousel"].'';
                $f_id = 'carousel_button';
	        
            } else {  
	        
                $f_Query = 'add-carousel';
                $f_Class = '';
                $f_Text = ''.$langscape["Add to Carousel"].'';
                $f_id = '';
            
            } ?>

            <a data-image-id="<?php echo $image->getId(); ?>" href="<?php echo $this_filename . '?image=' . $image->getId() ?>&amp;action=<?php echo $f_Query; ?>"><button class="pure-button <?php echo $f_Class; ?> pure-button-primary action-button" id="<?php echo $f_id; ?>"><span><?php echo $f_Text; ?></span></button></a><?php

		
		}
		
	    //Slider Image Button + Logged in as Admin
        if( $user->objectGroup()->isAdmin() && $user->isAuthorized() ) {
	    
            if( $image->isSlider() ) { 
	        
                $f_Query = 'remove-slider';
                $f_Class = 'pure-button-active';
                $f_Text = ''.$langscape["Added to Slider"].'';
                $f_id = 'slider_button';
	        
            } else {  
	        
                $f_Query = 'add-slider';
                $f_Class = '';
                $f_Text = ''.$langscape["Add to Slider"].'';
                $f_id = '';
            
            } ?>

            <a data-image-id="<?php echo $image->getId(); ?>" href="<?php echo $this_filename . '?image=' . $image->getId() ?>&amp;action=<?php echo $f_Query; ?>"><button class="pure-button <?php echo $f_Class; ?> pure-button-primary action-button" id="<?php echo $f_id; ?>"><span><?php echo $f_Text; ?></span></button></a><?php
        
                
	    }
	            
        ?> 

    </div><!-- Meta Data Ends -->
    
  </div><!-- Single Image Ends -->    <?php 

    if ( ($deviceType <> 'phone') || ( ($deviceType == 'phone') && (!$config->site->mobile->enable_responsive_phone) ) ) {

        if( !empty($more_images) && ((count($more_images)) > 1) ) { //There are more images! ?>
        
            <!-- Carousel Starts -->
            <div class="related-images">
            
                <ul id="carousel" class="elastislide-list"><?php
              
                $counter = 0;
                $image_list = '';
                $double_flag = false;
                
                foreach( $more_images as $more_images_single ) {

                    $more_image_type = $more_images_single->getMetaValue('type_gallery');
                    $video_url       = $more_images_single->getMetaValue('video_url');
                    $audio_url       = $more_images_single->getMetaValue('audio_url');
                    $extra_class     = '';
                    $youtube_class   = '';
                    $image_type_name = getImageTypeName($more_image_type);
					
					if ( $more_images_single->getMetaValue('crop_top') ) {
                    	$tn_src = ( 'library/thumb.php?f='.$more_images_single->getImage().'&amp;m=crop-top&amp;w=' . ($wcsi*1.5) . '&amp;h=' . ($hcsi*1.5) . '&amp;q=' . $config->site->media->jpg_quality . '&amp;c=' . $config->site->media->png_compression );		
                    } else {
						$tn_src = ( 'library/thumb.php?f='.$more_images_single->getImage().'&amp;m=crop&amp;w=' . ($wcsi*1.5) . '&amp;h=' . ($hcsi*1.5) . '&amp;q=' . $config->site->media->jpg_quality . '&amp;c=' . $config->site->media->png_compression );
                    }
   
					$counter++;
                    $title     = MK_Utility::escapeText($more_images_single->getTitle());		
                    $extension = explode('.', $more_images_single->getImage());
                    $extension = array_pop($extension); 
                    
                    
                    if ($image_type_name=='video') {
                        
                        $popup_str = $video_url;
                        $icon_class = "fa fa-youtube-play";
                                                
                    } elseif ( $image_type_name == 'image' ) {
                      
                        $popup_str = $more_images_single->getImage();
                        $icon_class = ($extension == 'gif') ? 'bolt' : 'camera';
                        
                    } elseif ( $image_type_name == 'audio' ) {

                        $popup_str = $audio_url;
                        $icon_class = "fa fa-soundcloud";
                    }
                
					//Destroy icon if disabled
					if (!$config->site->grid->hover_enable_icon) {
						$icon_class = "";
					}
                    
					//Check for current image
                    if( $more_images_single->getId() == $image_id ) {
                        $extra_class = "current-image";
                    } else { //No match.
                        $extra_class = "";
                    }
                  
                    //Build image list for fancybox ALL button - will be hidden
                    if ( $more_images_single->getId() <> $image_id ) {
                        $image_list .= '<a href="' . $popup_str . '" rel="group" class="fancybox-media" style="display:none;visibility:hidden;" title="' . $title . '" alt="' . $image_type_name . '/' .$more_images_single->getImageSlug() . '"></a>';
                    }
                  
                     ?>
                  
                    <li class="">
                        <a title="<?php echo $title; ?>" href="<?php echo $image_type_name . '/' .$more_images_single->getImageSlug(); ?>"><i class="<?php echo $icon_class; ?> icon rollover-icon"></i><img src="<?php echo $tn_src; ?>" class="<?php echo $extra_class; ?>" alt="<?php echo $title; ?>"></a>
                    </li><?php 
                  
                } //End for each ?>
                
                </ul>
            
                <!-- Image list for fancybox --> <?php         
                
                echo $image_list; //echo all images for fancybox ?>
            
            </div>
            <!-- Carousel Ends --><?php 
        } //if empty 

    } //End device check. ?>
        
    <!-- Meta Data Starts -->
    <div class="image-meta">
        
        <div class="pure-u-1">
        
            <div class="pure-g-r">
        
                <!-- IMAGE TITLE -->
                <div class="pure-u-1-2 float-left">
        	
                    <div class="content-wrapper">
                
                        <h2 class="meta-name heading pure-u <?php echo $edit_class; ?> <?php echo $edit_type_text; ?>" data-module-name="image" data-id="<?php echo $image_id; ?>" data-field-name="title" data-field-text="<?php echo str_replace('"',"'",$image_title); ?>"><?php echo $image_title; ?></h2>
                    
                        <!-- Image Gallery Link -->
                        <span class="meta-category pure-u-1">
                            
                            <span><i class="<?php if ($image_type==1) { echo 'pictures'; } elseif ($image_type==2) { echo 'fa fa-youtube-play'; } elseif ($image_type==3) { echo 'fa fa-soundcloud'; } ?> icon"></i><a href="gallery/<?php echo getImageTypeName($image_type) . '/' . urlencode($gallery_name); ?>" title=" <?php echo $gallery_name; ?>" class="<?php echo $edit_class; ?> <?php echo $edit_type_galleries; ?>" data-module-name="image" data-id="<?php echo $image_id; ?>" data-field-name="gallery"><?php echo $gallery_name; ?></a></span>
                            
                            <span><i class="user icon"></i><a href="<?php echo getProfileUrl($author_id); ?>"><?php echo $author_name; ?></a></span>
                        
                        </span>


                        <!-- Exif Data -->
						<?php
						
						if ($config->site->media->enable_exif && ($image_type==1) ) {
							//NEED TO FORMAT SHUTTERSPEED VALUE
							
							if (isset($exifdata[ 'ShutterSpeedValue' ])) {
							
								$results = sscanf($exifdata[ 'ShutterSpeedValue' ], '%d/%d');
								if (count($results) == 2 && $results[0] > 0) {
									if ($results[1] == 0) {
										$shutterspeed = sprintf('%d', $results[0]);
									}
									else
									if ($results[1] % $results[0] == 0) {
										$shutterspeed = sprintf('1/%d', $results[1] / $results[0]);
									}
									else
									if ($results[0] / $results[1] > 0) {
										$shutterspeed = sprintf('%d', round($results[0] / $results[1]));
									} 
								}
							}
												
						?>
						
                        <div class="exif-data">
    	
					    	<ul>   
								<?php if (isset($exifdata[ 'Model' ])) { ?><li class="pure-g"><div class="title pure-u-1-2"><?php echo$langscape["Camera"]; ?></div><div class="data pure-u-1-2"><?php echo $exifdata[ 'Model' ]; ?></div></li><?php } ?>
								
								<?php if (isset($exifdata[ 'FocalLength' ])) { ?><li class="pure-g"><div class="title pure-u-1-2"><?php echo $langscape["Focal Length"]; ?></div><div class="data pure-u-1-2"><?php echo $exifdata[ 'FocalLength' ]; ?></div></li><?php } ?>
								
								<?php if (isset($exifdata[ 'ShutterSpeedValue' ])) { ?><li class="pure-g"><div class="title pure-u-1-2"><?php echo $langscape["Shutterspeed"]; ?></div><div class="data pure-u-1-2"><?php echo $shutterspeed; ?> <?php echo $langscape["sec"]; ?></div></li><?php } ?>
								
								<?php if (isset($exifdata[ 'ISOSpeedRatings' ])) { ?><li class="pure-g"><div class="title pure-u-1-2"><?php echo $langscape["ISO Speed"]; ?></div><div class="data pure-u-1-2"><?php echo $exifdata[ 'ISOSpeedRatings' ]; ?></div></li><?php  } ?>
								
								<?php  if (isset($exifdata[ 'COMPUTED'][ 'ApertureFNumber' ])) { ?><li class="pure-g"><div class="title pure-u-1-2"><?php echo $langscape["Aperture"]; ?></div><div class="data pure-u-1-2"><?php echo $exifdata[ 'COMPUTED'][ 'ApertureFNumber' ]; ?></div></li><?php } ?>
								
								<?php if (isset($exifdata[ 'ExposureTime' ])) { ?><li class="pure-g"><div class="title pure-u-1-2"><?php echo $langscape["Exposure Time"]; ?></div><div class="data pure-u-1-2"><?php echo $exifdata[ 'ExposureTime' ]; ?></div></li><?php  } ?>
								
								<?php if (isset($exifdata[ 'Flash' ])) { ?><li class="pure-g"><div class="title pure-u-1-2"><?php echo $langscape["Flash"]; ?></div><div class="data pure-u-1-2"><?php echo ($exifdata[ 'Flash' ])?$langscape["Yes"]:$langscape["No"]; ?></div></li><?php  } ?>

					    	</ul>
					    
					    </div><!-- Exif Data Ends -->

					    <?php 
					    
					    }  // IF EXIF ENABLED
					    
					    ?>

		
                        <!-- DESCRIPTION --><?php
                        $description = str_replace('"',"'",$image->getDescription());
                        
                        if ( ($description == '') && ($admin_mode == 1) ) { 
                            $description_text = $txt_placeholder_arr['description'];
                        }
                        else {
                            $description_text = $description;
                        } ?>
		          
                        <p class="meta-content <?php echo $edit_class; ?> <?php echo $edit_type_textarea; ?>" data-module-name="image" data-id="<?php echo $image_id; ?>" data-field-name="description" data-field-text="<?php echo $description; ?>"><?php echo makeClickableLinks(str_replace('{{link}}', 'http://',stripslashes($description_text))); ?></p>
        
                    </div>
                    
                </div>
        
                <div class="pure-u-1-2 meta-description tag social float-right">
	    
                    <div class="meta-description">
                        
                        <!-- Social Share -->
                        
                            <div class="social-share">
                
                                <h4 class="heading"><i class="share icon"></i><?php echo $langscape["Share"]; ?></h4>
					
                                <div class="social-buttons">

                                    <!-- Social Buttons Go Here -->
                                    <?php 
                                    $social_url_encoded = urlencode($config->site->url . getImageTypeName($image_type) . '/' . $image->getImageSlug());
                                    $social_url = $config->site->url . getImageTypeName($image_type) . '/' . $image->getImageSlug();
                                    
                                    //for pinterest
                                    $social_image_url_encoded = urlencode( $config->site->url . $image->getImage() );

                                    ?>
                                    <ul class="socialcount socialcount-large" data-url="<?php echo $social_url; ?>" data-share-text="<?php echo getShortUrl($social_url, $config->site->bitly->login_id, $config->site->bitly->app_key, $config->site->bitly->enabled) . ' - ' . $image_title . ' - ' . truncate($description_text, 100); ?>" data-media="<?php echo $social_url_encoded; ?>" data-mediapin="<?php echo $social_image_url_encoded; ?>" data-description="<?php echo truncate($description,100);?>">
							            
                                        <li class="facebook"><a href="javascript:fbShare('<?php echo $social_url_encoded; ?>', 520, 450)" title="<?php echo $langscape["Share on"];?> Facebook" target="_blank"><span class="socicon socicon-facebook"></span><span class="count facebook-count"><?php echo $langscape["Like"]; ?></span></a></li>
                                        <li class="twitter"><a href="https://twitter.com/intent/tweet?text=<?php echo $social_url_encoded; ?>" title="<?php echo $langscape["Share on"];?> Twitter" target="_blank"><span class="socicon socicon-twitter"></span><span class="count twitter-count"><?php echo $langscape["Tweet"]; ?></span></a></li>
                                        <li class="googleplus"><a href="https://plus.google.com/share?url=<?php echo $social_url_encoded; ?>" title="<?php echo $langscape["Share on"];?> Google Plus" target="_blank"><span class="socicon socicon-google"></span><span class="count google-count"><?php echo '+1'; ?></span></a></li>
                                        <?php if ($deviceType=="computer") { ?>
                                        <li class="pinterest"><a title="Pin It"><span class="socicon socicon-pinterest"></span><span class="count pin-count"><?php echo $langscape["Pin It"]; ?></span></a></li><?php } ?>

                                    </ul>
					
                                </div>
				
                            </div>
                                                        				
	                    <?php
	            
                        if ( $tags  || $admin_mode == 1 ) { ?>
                      
                        <!-- TAGS -->
                        <div class="meta-tag">
                            <h4 class="heading"><i class="tag icon"></i><?php echo $langscape["Tags"];?></h4><?php
							
							if ($admin_mode == 1) {		
                        	?>
							<script type="text/javascript">
							    
							    var tags = <?php echo json_encode($tags); ?>;
							    
							    $(document).ready( function() { 
							    	
						            $(".tm-input").tagsManager({
							            hiddenTagListName: 'hidden-tags',
						                tagsContainer: '.meta-tag',
						                prefilled: tags
						            });
							    
							    });
							   
							</script>
							
							<input placeholder="<?php echo $langscape['Tags']; ?>" data-value="" class="tm-input pure-u-1 <?php echo $edit_class; ?> <?php echo $edit_type_tags; ?>" type="text" name="tags" id="tags" value="" data-module-name="image" data-id="<?php echo $image_id; ?>" data-field-name="tags">
							<input name="hidden-tags" type="hidden" value="">
							
                            <?php
							
							} elseif ( $tags ) {
	                            foreach( $tags as $tag ) {
	                                
	                                $tag = trim( $tag );
	                                echo '<a href="./tag/' . urlencode( $tag ) . '"><button class="tm-tag pure-button">' . $tag .'</button></a>';
	                           
	                            } 
							} ?>
                
                      	</div>
					  	
                        <?php
                        }
                 
    				    if( ($media_source <> ''  || $admin_mode == 1) && $config->site->media->enable_source )
						{
							if ($media_source == '') { 
			                    $media_source_text = $txt_placeholder_arr['source'];
			                } else {
			                    $media_source_text = $media_source;
			                }
						?>
                        <div class="meta-source">
    
                            <!-- CREDITS / SOURCE -->
                            <h4 class="heading"><i class="thumbs-up icon"></i><?php echo $langscape["Credits"];?></h4>
                             <div class="pure-u-1 "><button class="tm-tag pure-button"><a href="http://<?php echo $media_source; ?>" class="<?php echo $edit_class . " " . $edit_type_link; ?>" data-module-name="image" data-id="<?php echo $image_id; ?>" data-field-name="source" data-field-text="<?php echo $media_source; ?>"><?php echo $media_source_text; ?></a></button></div>
                   
                        </div>
                       <?php
                       	}
                       ?>
                    </div>
	    
                </div>
	    
                <div class="clearfix"></div>
      
            </div>
    
        </div>
      
    </div>
    <!-- Meta Data Ends -->
  

  <!-- COMMENT HOLDER -->
  <div id ="comment-anchor" class="comment-holder"><?php


	 if (!empty($config->site->media->comments_type) && ( $config->site->media->comments_type == 'FACEBOOK' ) ) { //IF FACEBOOK COMMENTS
	
	?>
	
	<fb:comments href="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" numposts="5" colorscheme="light"></fb:comments>

	<?php
	 	
 	} else if( !empty($config->site->media->comments_type) && ( $config->site->media->comments_type == 'DEFAULT') ) {  //IF AMG DEFAULT COMMENTS
	
		if( $form->isSuccessful() ) { //IF A NEW COMMENT HAS BEEN SUBMITTED
			
      $new_comment = MK_RecordManager::getNewRecord($image_comment_module->getId());
			$new_comment
				->setUser($user->getId())
				->setImage($image->getId())
				->setComment($form->getField('comment')->getValue())
				->save();
			
      //ADD ACTIVITY NOTE FOR COMMENTING USER
      $user->addNotification( '<a href="member.php?user='.$user->getId().'">'.$user->getDisplayName().'</a> '.$langscape["commented on the image"].' <a href="'. getImageTypeName($image_type) . '/' . $image->getImageSlug() . '#comment-'.$new_comment->getId().'">'.$image->getTitle().'</a>' );

      //ADD ACTIVITY NOTE FOR AUTHOR OF IMAGE
      $image->objectUser()->addNotification('<a href="members.php?user='.$user->getId().'">'.$user->getDisplayName().'</a> '.$langscape["commented on your image"].' <a href="'. getImageTypeName($image_type) . '/' . $image->getImageSlug() . '#comment-'.$new_comment->getId().'">'.$image->getTitle().'</a>', false, $user);
			
		} //END FORM SUBMITTED

		$comments_paginator = new MK_Paginator(1, 20);
	
		$image_comments = $image_comment_module->searchRecords(array(
			array('field' => 'image', 'value' => $image_id),
			array('field' => 'reply_to', 'value' => 0)
		), $comments_paginator);
?>

    <div class="comment-feed">
		<div class="comment-head">
			<h4 class="heading"><i class="chat icon"></i><?php echo $langscape["Comments"];?></h4>


			<div class="comment-count">
				<span class="total-comments"><?php echo ( $image->getTotalComments() > 999 ? '999+' : $image->getTotalComments() ); ?> <?php echo $langscape["Comments"];?></span>
				
				<?php

				if (!$user->isAuthorized()) {
				?>
				<span class="spacing">|</span>
				<span class="en-trigger" data-modal="modal-sign-in"><?php echo $langscape["Leave a Comment"];?></span>
				<?php } ?>
			
			</div>

		</div>

    </div>
    
    <div class="comment-box"><!-- EMPTY? --></div><?php
		
    if(count($image_comments) > 0) { ?>
    
      <ul class="cbp_tmtimeline" data-autoload="true"><?php
    
        foreach($image_comments as $image_comment) { 
	        
	        $in_replies=0;
				
          /********** SEARCH FOR REPLIES **********/
					$image_comment_replies = $image_comment_module->searchRecords(array(
            array(
              'field' => 'reply_to', 
              'value' =>$image_comment->getId()
              )
            )
          );

          /********** HAS REPLIES **********/
			if( !empty($image_comment_replies)) { $has_replies=true; } else { $has_replies=false; }
				$author_comment = null;
			try
			{
				$author_comment = $image_comment->objectUser();
			}
			catch( Exception $e )
			{
				$author_comment = null;
			}
					
					$image_comment_likes_count = $image_comment_like_module->getTotalRecords(array(
						array(
				              'field' => 'comment', 
							  'value' => $image_comment->getId()
							  )
							  )); ?>

            <li <?php echo ($has_replies)?'class="has-replies"':'class="has-replies"'; ?> id="comment-<?php echo $image_comment->getId(); ?>">
            
            <div class="cbp_tmicon">
		<?php
			if( $author_comment )
			{
		?>
		       <a href="member.php?user=<?php echo $author_comment->getId(); ?>"><img src="library/thumb.php?f=<?php echo ( $author_comment->getAvatar() ? $author_comment->getAvatar() : $config->site->default_avatar ); ?>&amp;m=crop&amp;<?php echo 'w=' . $wca . '&amp;h=' . $hca; ?>" alt="<?php echo $author_comment->getTitle(); ?>"></a>
		<?php
			}
			else
			{
		?>
		       <a><img src="library/thumb.php?f=<?php echo $config->site->default_avatar; ?>&amp;m=crop&amp;<?php echo 'w=' . $wca . '&amp;h=' . $hca; ?>"></a>
		<?php
			}
		?>
            </div> <!-- cbp_tmicon -->

            <!-- DISPLAY A COMMENT  -->
            <div class="cbp_tmlabel">
<?php
	if( $author_comment )
	{
?>			   <a name="comment-<?php echo $image_comment->getId(); ?>"></a>
              <span class="user">
              	<a class="username-wrap" href="member.php?user=<?php echo $author_comment->getId(); ?>"><?php echo $author_comment->getDisplayName(); ?></a>
              	<time class="cbp_tmtime" datetime="<?php echo $image_comment->getDateAdded(); ?>">
              	<span><?php echo time_since(time() - strtotime($image_comment->getDateAdded())); ?> <?php echo $langscape["ago"];?></span>
              	</time>
<?php
	}
	else
	{
?>			  <a name="comment-<?php echo $image_comment->getId(); ?>"></a>
              <span class="user">
              	<a class="username-wrap"><?php echo $langscape["Guest"];?></a>
              	<time class="cbp_tmtime" datetime="<?php echo $image_comment->getDateAdded(); ?>">
              	<span><?php echo time_since(time() - strtotime($image_comment->getDateAdded())); ?> <?php echo $langscape["ago"];?></span>
              	</time>
<?php
	}
?>
   
              
              
                          
  					<?php  //DELETE + LIKE BUTTONS
					
					//SHOW DELETE BUTTON
					if( $image_comment->canDelete($user) ) { ?>
            
	            <span rel="comment delete-comment" data-comment-id="<?php echo $image_comment->getId(); ?>" class="delete-comment">
	            	<span><?php echo $langscape["Delete"];?></span>
	            </span>
            
            <?php
					}
					
					if( $user->isAuthorized() ) {
					
						$logged_in = 1;
						$extra_class = '';
						$extra_attr = '';
					}
					else {
					
						$logged_in = 0;
						$extra_class = 'en-trigger';
						$extra_attr = 'data-modal="modal-sign-in"';
					
					}
					
					$image_comment_likes = $image_comment_like_module->searchRecords(array(
						array('field' => 'comment', 'value' => $image_comment->getId()),
						array('field' => 'user', 'value' => $user->getId()),
					));
					
					$icon_style = $config->site->style->icon_like;

              		if( $image_comment_likes = array_pop($image_comment_likes) ) { //User has already liked ?>
							<span rel="image-comment <?php echo ($logged_in)? 'remove-like':''; ?>" data-image-comment-like-id="<?php echo $image_comment_likes->getId(); ?>" data-user-id="<?php echo $user->getId(); ?>" data-image-id="<?php echo $image->getId(); ?>" data-image-comment-likes-total="<?php echo $image_comment_likes_count; ?>" class="pure-u meta-hearts stats remove-like">
								<i class="<?php echo $icon_style; ?> icon"></i>
								<span class="text"><?php echo number_format($image_comment_likes_count); ?></span>
							</span>
					
					<?php 
							} else { ?>
					
							<span rel="image-comment <?php echo ($logged_in)? 'add-like':''; ?>" data-image-comment-id="<?php echo $image_comment->getId(); ?>" data-image-comment-like-id="" data-user-id="<?php echo $user->getId(); ?>" data-image-id="<?php echo $image->getId(); ?>" data-image-comment-likes-total="<?php echo $image_comment_likes_count; ?>" class="pure-u meta-hearts stats <?php echo $extra_class; ?>" <?php echo $extra_attr; ?>>
								<i class="<?php echo $icon_style; ?> icon"></i>
								<span class="text"><?php echo number_format($image_comment_likes_count); ?></span>
							</span>
					
					<?php
							}
					?>
              </span>
              <span><?php echo makeClickableLinks(str_replace('{{link}}', 'http://',nl2br(MK_Utility::escapeText($image_comment->getComment())))); ?></span>
              
            </div> <!-- cbp_tmlabel -->

            <!-- CREATE COMMENT FORM AREA --><?php

            if( $user->isAuthorized() || $enable_guest_comments ) {
            
              $form_prefix = 'reply_'.$image_comment->getId();
          
              $comment_settings = array(
                'attributes' => array(
                  'class' => 'clear-fix standard',
                  'id' => $form_prefix,
                  'rel' => 'comment reply',
                  'action' => $this_filename.'?image='.$image->getId().'#reply_'.$image_comment->getId()
                )
              );
        
              $comment_fields = array(
                $form_prefix.'_comment' => array(
                  'label' => 'Comment'.( !$user->isAuthorized() ? ' as Guest' : '' ),
                  'type' => 'textarea',
                  'validation' => array(
                    'instance' => array(),
                    'length_max' => array(500)
                  ),
                  'attributes' => array(
                    'placeholder' => ''.$langscape["Reply to this comment..."].''
                  )
                ),
                $form_prefix.'_user' => array(
                  'attributes' => array(
                    'type' => 'hidden',
                  ),
                  'value' => $user->getId()
                ),
                $form_prefix.'_image' => array(
                  'attributes' => array(
                    'type' => 'hidden',
                  ),
                  'value' => $image->getId()
                ),
                $form_prefix.'_reply_to' => array(
                  'attributes' => array(
                    'type' => 'hidden',
                  ),
                  'value' => $image_comment->getId()
                ),
                $form_prefix.'_post-reply' => array(
                  'type' => 'submit',
                  'attributes' => array(
                    'value' => ''.$langscape["Post Reply"].'',
                    'class' => 'pure-button'
                  )
                )
              );
        
              $comment_form = new MK_Form($comment_fields, $comment_settings);

              if( $comment_form->isSuccessful() ) { //Comment was valid...
                $comment_reply = MK_RecordManager::getNewRecord( $image_comment_module->getId() );
                $comment_reply
                  ->setUser( $user->getId() )
                  ->setImage( $image->getId() )
                  ->setComment( $comment_form->getField($form_prefix.'_comment')->getValue() )
                  ->setReplyTo( $image_comment->getId() )
                  ->save();
                
                // ADD ACTIVITY FEED FOR USER COMMENTING
                $user->addNotification( '<a href="member.php?user='.$user->getId().'">'.$user->getDisplayName().'</a> '.$langscape["replied to a"].' <a href="'. getImageTypeName($image_type) . '/' . $image->getImageSlug() . '#comment-'.$image_comment->getId().'">'.$langscape["comment"].'</a> '.$langscape["on the image"].' <a href="'.$this_filename.'?image='.$image->getId().'#comment-'.$comment_reply->getId().'">'.$image->getTitle().'</a>' );

                // ADD ACTIVITY FEED FOR IMAGE AUTHOR
                $author->addNotification( '<a href="member.php?user='.$user->getId().'">'.$user->getDisplayName().'</a> '.$langscape["replied to a"].' <a href="'. getImageTypeName($image_type) . '/' . $image->getImageSlug() . '#comment-'.$image_comment->getId().'">'.$langscape["comment"].'</a> '.$langscape["on the image"].' <a href="'.$this_filename.'?image='.$image->getId().'#comment-'.$comment_reply->getId().'">'.$image->getTitle().'</a>' );
                
                // ADD ACTIVITY FEED FOR COMMENT AUTHOR
                $author_comment->addNotification('<a href="member.php?user='.$user->getId().'">'.$user->getDisplayName().'</a> '.$langscape["replied to your"].' <a href="'. getImageTypeName($image_type) . '/' . $image->getImageSlug() . '#comment-'.$image_comment->getId().'">'.$langscape["comment"].'</a> '.$langscape["on the image"].' <a href="'.$this_filename.'?image='.$image->getId().'#comment-'.$comment_reply->getId().'">'.$image->getTitle().'</a>', false, $user);
              }
            } //END IF AUTHORISED USER AND COMMENT SUBMITTED
        
            //SEARCH FOR ANY REPLIES TO COMMENTS
            $image_comment_replies = $image_comment_module->searchRecords(array(
              array('field' => 'reply_to', 'value' => $image_comment->getId())
            ));
        
        // HAS REPLIES 
        if( !empty($image_comment_replies) || ( $user->isAuthorized() || $enable_guest_comments ) ) {?>

          <ul data-autoload="true" class="replies<?php echo ( count($image_comment_replies) > 3 ? ' replies-hidden' : '' ); ?>"><?php
        
            //LOOP THRU REPLIES
            foreach($image_comment_replies as $image_comment_reply) {	$in_replies=1;
              $image_comment_reply_likes_count = $image_comment_like_module->getTotalRecords(array(
                array('field' => 'comment', 'value' => $image_comment_reply->getId()),
              ));

	$author_reply = null;
	try
	{
        $author_reply = $image_comment_reply->objectUser();
	}
	catch( Exception $e )
	{
		$author_reply = null;
	}
		  
			  ?>
          
              <li id="comment-<?php echo $image_comment_reply->getId(); ?>">
            
                <div class="cbp_tmicon">
<?php
	if( $author_reply )
	{
?>
                  <a href="member.php?user=<?php echo $author_reply->getId(); ?>"><img src="library/thumb.php?f=<?php echo ( $author_reply->getAvatar() ? $author_reply->getAvatar() : $config->site->default_avatar ); ?>&amp;m=crop&amp;<?php echo 'w=' . $wca . '&amp;h=' . $hca; ?>" alt="<?php echo $author_reply->getTitle(); ?>"></a>
<?php
	}
	else
	{
?>
              <a><img src="library/thumb.php?f=<?php echo $config->site->default_avatar; ?>&amp;m=crop&amp;<?php echo 'w=' . $wca . '&amp;h=' . $hca; ?>"></a>
<?php
	}
?>
                </div><!-- cbp_tmicon -->
                                
              <div class="reply">
              
              
                <!-- HAS REPLY COMMENT AREA -->
                <div class="cbp_tmlabel">
<?php
	if( $author_reply )
	{
?>				<a name="comment-<?php echo $image_comment_reply->getId(); ?>"></a>
                <span class="user">
                	<a class="username-wrap" href="member.php?user=<?php echo $author_reply->getId(); ?>"><?php echo $author_reply->getDisplayName(); ?></a>
	                <time class="cbp_tmtime" datetime="<?php echo $image_comment_reply->getDateAdded(); ?>">
	                	<span><?php echo time_since(time() - strtotime($image_comment_reply->getDateAdded())); ?> <?php echo $langscape["ago"];?></span>
					</time>
<?php
	}
	else
	{
?>				<a name="comment-<?php echo $image_comment_reply->getId(); ?>"></a>
				<span class="user">
					<a class="username-wrap"><?php echo $langscape["Guest"];?></a>
					<time class="cbp_tmtime" datetime="<?php echo $image_comment_reply->getDateAdded(); ?>">
						<span><?php echo time_since(time() - strtotime($image_comment_reply->getDateAdded())); ?> <?php echo $langscape["ago"];?></span>
					</time>
<?php
	}
?>
                  
                  
					<?php  //DELETE + LIKE BUTTONS
					
					//SHOW DELETE BUTTON
					if( $image_comment_reply->canDelete($user) ) { ?>
            
	            <span rel="comment delete-comment" data-comment-id="<?php echo $image_comment_reply->getId(); ?>" class="delete-comment">
	            	<span><?php echo $langscape["Delete"];?></span>
	            </span>
            <?php
					}
					
					if( $user->isAuthorized() ) {
					
						$logged_in = 1;
						$extra_class = '';
						$extra_attr = '';
					}
					else {
					
						$logged_in = 0;
						$extra_class = 'en-trigger';
						$extra_attr = 'data-modal="modal-sign-in"';
					
					}
					
						$image_comment_reply_likes = $image_comment_like_module->searchRecords(array(
						array('field' => 'comment', 'value' => $image_comment_reply->getId()),
						array('field' => 'user', 'value' => $user->getId()),
						));
						
						$icon_style = $config->site->style->icon_like;
					
						if( $image_comment_reply_likes = array_pop($image_comment_reply_likes) ) { //User Likes Comment ?>
							 <span rel="image-comment <?php echo ($logged_in)? 'remove-like':''; ?>" data-image-comment-like-id="<?php echo $image_comment_reply_likes->getId(); ?>" data-user-id="<?php echo $user->getId(); ?>" data-image-id="<?php echo $image->getId(); ?>" data-image-comment-likes-total="<?php echo $image_comment_reply_likes_count; ?>" class="pure-u meta-hearts stats remove-like">
							 	<i class="<?php echo $icon_style; ?> icon"></i>
							 	<span class="text"><?php echo number_format($image_comment_reply_likes_count); ?></span>
							 </span>
					
					<?php 
							} else { ?>
					
							<span rel="image-comment <?php echo ($logged_in)? 'add-like':''; ?>" data-image-comment-id="<?php echo $image_comment_reply->getId(); ?>" data-user-id="<?php echo $user->getId(); ?>" data-image-id="<?php echo $image->getId(); ?>" data-image-comment-likes-total="<?php echo $image_comment_reply_likes_count; ?>" class="pure-u meta-hearts stats <?php echo $extra_class; ?>" <?php echo $extra_attr; ?>>
								<i class="<?php echo $icon_style; ?> icon"></i>
								<span class="text"><?php echo number_format($image_comment_reply_likes_count); ?></span>
							</span>
					
					<?php
							}
					?>
                  
                  </span>

                  <span><?php echo makeClickableLinks(str_replace('{{link}}', 'http://',nl2br(MK_Utility::escapeText($image_comment_reply->getComment())))); ?></span>
                </div><!-- cbp_label -->
                
               </div> <!-- reply -->
                
              </li><?php
            } //FINISH REPLIES LOOP ?>

            <!-- VIEW ALL JAVASCRIPT BUTTON GOES HERE IF NEEDED UNHIDE EXTRA REPLIES --><?php
            //DISPLAY COMMENT FORM UNDER REPLIES IF NECESSARY
        
            if( $user->isAuthorized() || $enable_guest_comments ) { 
	            
	           // if ($in_replies==0) { echo '<ul class="replies">';}
            ?>
        
        
              <li class="clear-fix form"><?php
                
                if( $comment_form->isSuccessful() ) {	?>  
                  <p id="<?php echo $form_prefix; ?>" class="alert alert-success"><?php echo $langscape["Thank you for submitting your reply!"];?></p><?php
                } else { //echo $in_replies;?>
                  
                  
                	<div class="cbp_tmicon">
						<a href="member.php?user=<?php echo $user->getId(); ?>"><img src="library/thumb.php?f=<?php echo ( $user->getAvatar() ? $user->getAvatar() : $config->site->default_avatar ); ?>&amp;m=crop&amp;<?php echo 'w=' . $wca . '&amp;h=' . $hca; ?>"></a>
                  </div>
                  
                  <div class="reply">
				  		<div class="cbp_tmlabel"><?php echo $comment_form->render(); ?></div>
                  </div>
                
                  <?php
                } //END IF COMMENT FORM SUBMITTED ?>
            
              </li><?php
              
	        //if ($in_replies==0) { echo '</ul>';}

            } //END IF USER IS AUTHORIZED ?>
          </ul><?php
    } //END IF HAS REPLIES AND AUTHORIZED ?>
    </li>
    
    <!-- ADD REPLIES BOX AT THE END -->
    
    <?php
 	} //END COMMENTS 
 	?>
	</ul>
	
	<div class="clear-fix paginator"><?php echo $paginator->render($this_filename."?comments-page={page}&image=" . $image_id . "#comments"); ?></div><?php
  
} else { ?>
	<!--<p class="alert alert-information">There are no comments, why not be the first to write one?</p>--><?php
} ?>	
	
<!--<h4 id="leave-comment">Leave a Comment</h4>--><?php
if( $user->isAuthorized() || $enable_guest_comments )
{

  if($form->isSuccessful()) { ?>
    <p class="alert alert-success"><?php echo $langscape["Thank you for submitting your comment!"];?></p><?php
	} else { ?>
    <ul class="cbp_tmtimeline" data-autoload="true">
      <li id="main-reply-box">
        <div class="cbp_tmicon">
<?php
if( $user->isAuthorized() ) {
?>
          <a href="member.php?user=<?php echo $user->getId(); ?>"><img src="library/thumb.php?f=<?php echo ( $user->getAvatar() ? $user->getAvatar() : $config->site->default_avatar ); ?>&amp;m=crop&amp;<?php echo 'w=' . $wca . '&amp;h=' . $hca; ?>"></a>
<?php
}else{
?>
          <a><img src="library/thumb.php?f=<?php echo $config->site->default_avatar; ?>&amp;m=crop&amp;<?php echo 'w=' . $wca . '&amp;h=' . $hca; ?>"></a>
<?php
}
?>
        </div>
        <div class="cbp_tmlabel"><?php echo $form->render(); ?></div>              
      </li>
    </ul><?php
	}
} else { ?>
	<p class="alert alert-information"><a href="sign-in.php" class="en-trigger pure-button" data-modal="modal-sign-in"><i class="chat icon"></i><span><?php echo $langscape["Sign in to comment!"];?></span></a></p><?php
}

?>

<?php } ?>

</div> <!-- COMMENT HOLDER -->

</section>

<?php if ( $disable_responsive || ($deviceType == 'computer') ) { ?>

<!-- Sidebar Starts Here -->
<?php include ('gallery-image-sidebar.php'); ?>
<!-- Sidebar Starts Here -->

<?php } ?>
