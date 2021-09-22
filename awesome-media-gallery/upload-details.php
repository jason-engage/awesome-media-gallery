<?php

require_once '_inc.php';

// Used for Wordpress Transfers
include 'library/IXR_Library.php';	

// We get an instance of the image & gallery module
$image_module   = MK_RecordModuleManager::getFromType('image'); //Image details
$gallery_module = MK_RecordModuleManager::getFromType('image_gallery'); //Gallery Info
$field_module   = MK_RecordModuleManager::getFromType('module_field');

include ('_variables.php'); //Variables

if( !$user->isAuthorized() || empty($_POST) ) { //User is not logged in or no content posted, get them out of here. DH ENGAGE 29/08

  header('Location: '.MK_Utility::serverUrl('/'), true, 302);
  exit;
  
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {  //Check to ensure the user came from the uploads modal.

        if (empty($_POST["gallery_type"])) {
            $type_gallery      = 1;
            $type_gallery_name = $langscape["Image"];
        } else {
            $type_gallery = $_POST["gallery_type"];
            
            switch ($type_gallery) {
	            case 1:
	                $type_gallery_name = $langscape["Image"];
					break;
				case 2:
	                $type_gallery_name = $langscape["Video"];
					break;
				case 3:
	                $type_gallery_name = $langscape["Audio"];
	                break;
	            default:
	            	$type_gallery_name = '';
					break;
        	}
        }

  
    if (empty($_POST['type'])) { //Type is empty. Assume user is uploading images.
          

        if( !empty($_POST['image0']) ) { //FROM UPLOADS PAGE

            $counter    = 0;
            $image_file = array();
          
            while( !empty( $_POST['image' . $counter]) ) {
              
                $image_file[] = $config->site->url.$_POST['image' . $counter];
                $title[] = $_POST['title' . $counter];
                $counter++;
          
            }
          
        } else { //FROM MODAL

            $image_file = !empty($_POST['image-file']) ? $_POST['image-file'] : $_GET['image-file'];
            $title = $_POST['image-name'];

        }
  
        
        foreach($image_file as $key=>$value) { //Loop through the image array     
        
            $url_split_array = explode($config->site->url, $value); //Split the URL on the .com
            
            $cropped_url[]   = $config->site->url."library/thumb.php?f=".$url_split_array[1]."&amp;m=crop&amp;w=" . $wim . "&amp;h=" . $him; //Create the cropped version

            $cropped_top_url[]   = $config->site->url."library/thumb.php?f=".$url_split_array[1]."&amp;m=crop-top&amp;w=" . $wim . "&amp;h=" . $him; //Create the cropped version

            $real_url[]      = $url_split_array[1]; //Keep the real URL also.
            //$title[]         = '';
            
            
            
            $description[]   = '';
            $video_url[]     = '';
            $audio_url[]     = '';
            $soundcloud_id[]     = '';
            
        }
  
    } elseif ( ($_POST['type'] == 'video') || ($_POST['type'] == 'audio') ) { //Post Type is video.
        
        $isVideo      = true;
        $type_gallery = ($_POST['type'] == 'video') ? 2 : 3 ;

        $title        = array_reverse($_POST['title']);
        
        $soundcloud_id = ($_POST['type'] == 'audio') ? array_reverse($_POST['soundcloud_id']) : '';
				
        $video_url = ($_POST['type'] == 'video') ? array_reverse($_POST['video_url']) : '';
        $audio_url = ($_POST['type'] == 'audio') ? array_reverse($_POST['audio_url']) : '';
                
        $image_file   = array_reverse($_POST['image_url']);
        $description  = array_reverse($_POST['description']);
        
        foreach($image_file as $key=>$value) { //Loop through the video array
            
            $uploaded_video_image = MK_FileManager::uploadFileFromUrl( $value, $config->site->upload_path ); //Upload the video image url to server.
        
            $cropped_url[]   = $config->site->url."library/thumb.php?f=".$uploaded_video_image."&amp;m=crop&amp;w=" . $wim . "&amp;h=" . $him; //Create the cropped version

            $cropped_top_url[]   = $config->site->url."library/thumb.php?f=".$uploaded_video_image."&amp;m=crop-top&amp;w=" . $wim . "&amp;h=" . $him; //Create the cropped version

            
            $real_url[]     = $uploaded_video_image;
        }
        
    }

	/* Wordpress - Might be useful to reset values if something goes wrong  
    $wp_post = !empty($_POST['wp_post']) ? $_POST['wp_post'] : '1';
    $amg_post = !empty($_POST['amg_post']) ? $_POST['amg_post'] : '0';
    */
    
    
    /* Setup the galleries */
    
        $gallery_options = array();

        $gallery_module = MK_RecordModuleManager::getFromType('image_gallery');

        $search_criteria[] = array('literal' => "(`type_gallery` = " . $type_gallery . ")");

        foreach($gallery_module->searchRecords($search_criteria) as $gallery) {
        
            $gallery_options[$gallery->getId()] = $gallery->getName();
            
        }
    
    /* End galleries setup */
    
    /* Setup the form */
    $criteria = array(
		array('field' => 'module', 'value' => $image_module->getId()),
		array('field' => 'name', 'value' => 'title')
	);
	
	$title_field = $field_module->searchRecords($criteria);
	$title_field = array_pop( $title_field );
    
    $image_file_query = array(
        'image-file' => $image_file
    );
   
    $add_image_settings = array(
        'attributes' => array(
            'class' => 'clear-fix standard pure-g-r pure-form js-upload-form',
            //'action' => 'upload-details.php?'.http_build_query($image_file_query)
            'action' => 'upload-details.php'
        ),
        'html_start' => '<li class="pure-u-1-4 box-upload" id="image-x"><figure>',
        'html_start2' => '<figcaption>',
        'image_urls' => $cropped_url,
        'html_end' => '</figcaption></figure></li>'
    );

    /******************** Build the image fields ********************/

    $add_image_structure = array();

    for ( $i = 0; $i < count($cropped_url); ++$i ) { //Loop for creating all of the fields.
    	
    	 $add_image_structure['gallery'.$i] = array(
            'label' => ''.$langscape["Choose a gallery:"].'', //$type_gallery_name . ' gallery',
            'fieldset' => 'fieldset-'.$i,
            'type' => 'select',
            'options' => $gallery_options,
            'validation' => array(
                'instance' => array()
            )
        );

        $add_image_structure['title'.$i] = array(
            //'label' => 'Title',
            'fieldset' => 'fieldset-'.$i,
            'validation' => array(
                'instance' => array(),
                'unique' => array(null, $title_field, $image_module)
            ),
            'attributes' => array(
                'placeholder' => ''.$langscape["Title"].''
            ),
            'value' => strip_tags(stripslashes($title[$i]))
        );
      
        $add_image_structure['image'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden'
            ),
            'value' => $real_url[$i]
        );

        $add_image_structure['crop_url'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden',
                'class' => 'crop-url'
            ),
            'value' => $cropped_url[$i]
        );

        $add_image_structure['crop_top_url'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden',
                'class' => 'crop-top-url'
            ),
            'value' => $cropped_top_url[$i]
        );

        $add_image_structure['approved'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden'
            ),
            'value' => ($user->objectGroup()->isAdmin()) ? 1 : 0
        );        
              		
        $add_image_structure['video_url'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden',
            ),
            'value' => ($type_gallery == 2) ? $video_url[$i] : ''
        );
		
        $add_image_structure['audio_url'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden',
            ),
            'value' => ($type_gallery == 3) ? $audio_url[$i] : '' 
        );	   

        $add_image_structure['soundcloud_id'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden',
            ),
            'value' => ($type_gallery == 3) ? $soundcloud_id[$i] : '' 
        );	
                
        $add_image_structure['type_gallery'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden',
            ),
            'value' => $type_gallery
        );
    
        $add_image_structure['description'.$i] = array(
            //'label' => 'Description',
            'fieldset' => 'fieldset-'.$i,
            
            'type' => 'textarea',
            'attributes' => array(
                'placeholder' => ''.$langscape["Description"].''
            ),
            'value' => strip_tags(stripslashes($description[$i]))
        );

        $add_image_structure['tags'.$i] = array(
            //'label' => 'Tags',
            'fieldset' => 'fieldset-'.$i,
            //'tooltip' => "Separate with a comma ','.",
            'attributes' => array(
                'placeholder' => ''.$langscape["Tags"].'',
                'data-value' => !empty($_POST['hidden-tags'.$i]) ? $_POST['hidden-tags'.$i] : '',
                'class' => 'tm-input'
            )
        );

        $add_image_structure['crop_top'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'checkbox',
            'label' => $langscape['Crop from top'],
            'attributes' => array(
	            'class' => 'crop-top',
            ),
            'value' => 0
        );
        
        if ( (!empty($wp_author_id))  && ($config->site->wordpress->enable_post_to_wp) ) {

	        $add_image_structure['amg_post'.$i] = array(
	            'fieldset' => 'fieldset-'.$i,
	            'attributes' => array(
                	'type' => 'hidden',
	            ),
	            'value' => 1
	        );
	
	        $add_image_structure['wp_post'.$i] = array(
	            'fieldset' => 'fieldset-'.$i,
	            'type' => 'checkbox',
	            'label' => $langscape['Post to WP'],
	            'value' => 1
	        );
	        
	                
        } else {
        
	        $add_image_structure['amg_post'.$i] = array(
	            'fieldset' => 'fieldset-'.$i,
	            'attributes' => array(
                	'type' => 'hidden',
	            ),
	            'value' => 1
	        );
	
	        $add_image_structure['wp_post'.$i] = array(
	            'fieldset' => 'fieldset-'.$i,
	            'attributes' => array(
               		'type' => 'hidden',
	            ),
	            'value' => 0
	        );	        
        }
		
		if ($config->site->social->enable_post_to_fb && !empty($config->site->facebook->page_id)) {
	        $add_image_structure['fb_post'.$i] = array(
	            'fieldset' => 'fieldset-'.$i,
	            'type' => 'checkbox',
	            'label' => $langscape['Post to FB'],
	            'value' => 1
	        );
	    } else {
	        $add_image_structure['fb_post'.$i] = array(
	            'fieldset' => 'fieldset-'.$i,
	            'attributes' => array(
	            	'type' => 'hidden',
	            ),
	            'value' => 0
	        );		    
	    }
    
        $add_image_structure['hidden-tags'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'text',
            'attributes' => array(
                'type' => 'hidden',
            ),
            'value' => !empty($_POST['hidden-tags'.$i]) ? $_POST['hidden-tags'.$i] : ''
        );
        
        /*      
        $add_image_structure['remove-btn-'.$i] = array(
            'fieldset' => 'fieldset-'.$i,
            'type' => 'link',
            'text' => ''.$langscape["Remove"].'',
            'icon' => '<i class="check icon"></i>',
            'attributes' => array(
                'href' => '#',
                'class' => 'pure-button pure-button-primary remove-image'
            )
        );
        */
        
    } //End for each loop
	
    $add_image_structure['gallery_type'] = array(
        'fieldset' => 'fieldset-bottom',
        'attributes' => array(
            'type' => 'hidden',
        ),
        'value' => $type_gallery
    );
    
    $add_image_structure['image_count'] = array(
        'fieldset' => 'fieldset-bottom',
        'attributes' => array(
            'type' => 'hidden',
        ),
        'value' => count($cropped_url)
    );
    
    /*
    $add_image_structure['image_count'] = array(
        'fieldset' => 'fieldset-bottom',
        'attributes' => array(
            'type' => 'hidden',
        ),
        'value' => count($cropped_url)
    );*/
    
    $add_image_structure['submit'] = array(
        'type' => 'submit',
        'fieldset' => 'fieldset-bottom',
        'icon' => '<i class="checkmark icon"></i>',
        'attributes' => array(
            'value' => ''.$langscape["Save"].'',
            'class' => 'pure-button pure-button-primary submit'
        )
    );
    
    $add_image_structure['cancel'] = array(
        'type' => 'submit',
        'icon' => '<i class="cross icon"></i>',
        'fieldset' => 'fieldset-bottom',
        'attributes' => array(
            'value' => ''.$langscape["Cancel"].'',
            'class' => 'button-red pure-button pure-button-primary cancel'
        )
    );

    $add_image_form = new MK_Form($add_image_structure, $add_image_settings);
	
	// If the user clicks 'Cancel' then send them to the main gallery page
	if( $add_image_form->isSubmitted() && $add_image_form->getField('cancel')->getValue() ) { //User clicked cancel.

        header('Location: '.MK_Utility::serverUrl($this_filename), true, 302);
        exit();
    
	} elseif( $add_image_form->isSuccessful() ) { // Form is successfull then create the record and direct them to the home page
    
		$image_count = (integer) $add_image_form->getField('image_count')->getValue();
		
		$current_image = 0;
		//$current_image = $image_count-1;
		
		//ORDER FIX FOR VIDEOS VS IMAGES - DIRTY SHOULD NOT BE WORKING LIKE THIS
		//if ($type_gallery == 1) {
		//	$current_image = $current_image;
		//}
		
        //CONFIG WP AUTOPOST
        $wp_site = $config->site->wordpress->site_url;
        $wp_enabled = $config->site->wordpress->enable_post_to_wp;
		$usr = $config->site->wordpress->admin_username;
		$pwd = $config->site->wordpress->admin_password;
		$xmlrpc = $config->site->wordpress->site_url . '/xmlrpc.php';
		$content_html = '';
		$content_title = '';
		$content_tags = '';
		$content_thumb = '';
		$content_wp = false;
		$tags_arr = array();
		$categories_arr = array();
		
        while( $image_count > $current_image ) {
        
			if( $add_image_form->isField('gallery'.$current_image) ) {
            	
            	
            	//CHECK IF IMAGE TO BE POSTED
            	$amg_post = $add_image_form->getField('amg_post'.$current_image)->getValue();
            	
            	
            	if ($amg_post) {
            
					$new_image = MK_RecordManager::getNewRecord( $image_module->getId() );
	                
					$new_image
						->setGallery( $add_image_form->getField('gallery'.$current_image)->getValue() )
						->setImage( $add_image_form->getField('image'.$current_image)->getValue() )
	                    ->setVideoUrl( $add_image_form->getField('video_url'.$current_image)->getValue() )	                    
	                    ->setAudioUrl( $add_image_form->getField('audio_url'.$current_image)->getValue() )
	                    ->setSoundcloudId( $add_image_form->getField('soundcloud_id'.$current_image)->getValue() )
						->setDescription( $add_image_form->getField('description'.$current_image)->getValue() )
						->setTitle( $add_image_form->getField('title'.$current_image)->getValue() )
						->setTags( $add_image_form->getField('hidden-tags'.$current_image)->getValue() )
	                    ->setTypeGallery( $add_image_form->getField('type_gallery'.$current_image)->getValue() ) 
	                    ->setCropTop( $add_image_form->getField('crop_top'.$current_image)->getValue() )
	                    ->setApproved( $add_image_form->getField('approved'.$current_image)->getValue() )
	                    ->setImageSlug( seoUrl($add_image_form->getField('title'.$current_image)->getValue()) )
						->setUser( $user->getId() )
						->save(); 
			  
					//UPDATE MEDIA COUNT FOR USER
					$user
						->setMediaCount( $user->getMediaCount() + 1 )
						->save();
	          
	                $action_log_module = MK_RecordModuleManager::getFromType('action_log');
	                $new_logged_action = MK_RecordManager::getNewRecord($action_log_module->getId());
	                
                }
				
				// USED FOR AMG AND FB
				$gallery_id = $add_image_form->getField('type_gallery'.$current_image)->getValue();
	            $gallery_type_name = getImageTypeName($gallery_id);
	            $gallery_link = $gallery_name . '/' . $gallery_type_name . '/' . $new_image->getImageSlug();
	            
                // ADD ACTIVITY / ACTION TO AMG
                if ($amg_post) {
	                
	                	
	               
	                    $user->addNotification('<a href="' . $user->getUsername() . '">'.$user->getDisplayName().'</a> ' . $langscape['uploaded the ' . $gallery_type_name] . ' <a href="' . $gallery_link .'">'.$new_image->getTitle().'</a>', true, null, 'upload');
	       
	                    $new_logged_action
	                    ->setUser( $user->getId() )
	                    ->setAction('<a href="?module_path=users/index/method/edit/id/'.$user->getId().'">'.$user->getDisplayName().'</a> ' . $langscape['uploaded the ' . $gallery_type_name] . ' <a href="?module_path=images/index/method/edit/id/'.$new_image->getId().'">'.$new_image->getTitle().'</a>')
	                    ->save();
	                    
                } // ADD ACTION

               
              
               	//CHECK TO SEE IF IMAGE TO BE POSTED TO WP & BUILD SINGLE POST
			   	$wp_post = $add_image_form->getField('wp_post'.$current_image)->getValue();                 
        	
            	if ( ($wp_post) && ($wp_enabled) && ($wp_author_id>0) && ($usr) && ($pwd) )  {

	                $content_wp = true;
					
					//Open the Connection
                	$client = new IXR_Client($xmlrpc);
                	
	      	        //UPLOAD IMAGE TO WP SITE
	      	        $myFile = urldecode($add_image_form->getField('image'.$current_image)->getValue());
	      	        $ext = pathinfo($myFile , PATHINFO_EXTENSION);
					$fh = fopen($myFile, 'r');
					$fs = filesize($myFile);
					$theData = fread($fh, $fs);
					fclose($fh);
							 	 
					$client->debug = false;
					$params = array('name' => $add_image_form->getField('title'.$current_image)->getValue() . ' ' . $current_image . '.' . $ext, 'type' => 'image/'. $ext, 'bits' => new IXR_Base64($theData), 'overwrite' => false);
					$res = $client->query('wp.uploadFile',1, $usr, $pwd, $params);
					$image_data = $client->getResponse();

					if($res == true){
					    
					    //echo $image_data['id']; //UPLOADED IMAGE ID
					    //echo $image_data['url'];
					    //echo $add_image_form->getField('hidden-tags'.$current_image)->getValue();
					   
					}
					
					//SET THUMB
					if ($content_thumb == '') {
					   $content_thumb = $image_data['id'];
					}
							 
					// SET TITLE
					if ($content_title == '') {
					   $content_title = $add_image_form->getField('title'.$current_image)->getValue();
						//$content_title = array('post_title'=>$add_image_form->getField('title'.$current_image)->getValue());
					} else {
						$content_html .= "<h3>" . $add_image_form->getField('title'.$current_image)->getValue() . "</h3>";
					}
					 				 
					// SET CATEGORY
				 
	               //GET GALLERY NAME FROM ID - MOVE TO FUNCTIONS             
				   $search_criteria = array(
				          array('field' => 'id', 'value' => $add_image_form->getField('gallery'.$current_image)->getValue())
				        );                
					$gallery_query = $gallery_module->searchRecords($search_criteria);
					
			        foreach( $gallery_query as $gallery ) {
			        
			            $gallery_name = $gallery->getName();
			        
			        }
			        
				 	$categories_arr[] = $gallery_name; 
				 	$categories_arr = array_unique($categories_arr);

					//SET TAGS
					$tags = explode(',' , ucwords(strtolower($add_image_form->getField('hidden-tags'.$current_image)->getValue())));
					$tags_arr[] = $gallery_name;
					$tags_arr = array_merge($tags_arr,$tags);
					$tags_arr = array_filter(array_unique($tags_arr));
					

					//SET CONTENT - ADD IMAGE TAG + ADD POST CONTENT
					if ($type_gallery == 1) { //Image
						$img_url = '<img src="' . $image_data['url'] . '" />';
					} elseif ($type_gallery == 2) { //video
						$img_url = "\n" . $add_image_form->getField('video_url'.$current_image)->getValue() ."\n";
					} elseif ($type_gallery == 3) { //audio
						$img_url = "\n" . $add_image_form->getField('audio_url'.$current_image)->getValue() ."\n";
					}
					
					$content_html .= $img_url . $add_image_form->getField('description'.$current_image)->getValue();			
		
					
				} //IF WP_POST
						
				
            	//CHECK IF IMAGE TO BE POSTED TO FACEBOOK
            	$fb_post = $add_image_form->getField('fb_post'.$current_image)->getValue();

				// SEND POST TO FACEBOOK
		        if ($fb_post && !empty($config->site->facebook->page_id) && !empty($config->site->facebook->access_token) && !empty($config->site->facebook->app_id) && !empty($config->site->facebook->app_secret) ) {
		
			    	$fb_config = array();
					$fb_config['appId'] = $config->site->facebook->app_id;
					$fb_config['secret'] = $config->site->facebook->app_secret;
					$fb_config['fileUpload'] = true;
					
					$fb = new Facebook($fb_config);
					
					$acc_token=$config->site->facebook->access_token;
					$img_path = $_SERVER["DOCUMENT_ROOT"] . '/' . $add_image_form->getField('image'.$current_image)->getValue();
					$img_url = $config->site->url . $add_image_form->getField('image'.$current_image)->getValue();
					$link_url = $config->site->url . $gallery_link;
					$link_title = $add_image_form->getField('title'.$current_image)->getValue();
					$link_video = $add_image_form->getField('video_url'.$current_image)->getValue();
					$link_audio = $add_image_form->getField('audio_url'.$current_image)->getValue();
					$tags = $add_image_form->getField('hidden-tags'.$current_image)->getValue();
					$tags = str_replace(' ','',$tags);
					$tags = str_replace(',',' #',$tags);
					$tags = ( !empty($tags) )? "#" . $tags : '';
					$message = $user->getDisplayName() . " " . $langscape['uploaded the ' . $gallery_type_name] . " " . $link_title . ' ' . $tags;
					$message_with_link_description = $message . "\n" . $link_url . "\n" . "\n" . $add_image_form->getField('description'.$current_image)->getValue();

					
					//IF UPLOAD
					if ( ($config->site->social->fb_post_type == "UPLOAD") && ($type_gallery_name == "Image") ) {
						//echo $img_path;
						
						$params = array(
						  // this is the access token for Fan Page
						  "access_token" => $acc_token,
						  "message" => $message_with_link_description,
						  "source" => "@" . $img_path, // ATTENTION give the PATH not URL
						);
						 
						try {
						  $ret = $fb->api('/' . $config->site->facebook->page_id . '/photos', 'POST', $params);
						  //echo 'Photo successfully uploaded to Facebook Album';
						} catch(Exception $e) {
						  //echo 'Facebook Error: ' . $e->getMessage();
						  //die;
						}
						
					
					//IF LINK			    
					} elseif ( ($config->site->social->fb_post_type == "LINK") && ($type_gallery_name == "Image") ) {
						$params = array(
						  // this is the access token for Fan Page
						  "access_token" => $acc_token,
						  "message" => $message,
						  "link" => $link_url,
						  "picture" => $img_url,
						  "name" => $link_title,
						  "caption" => $config->site->name,
						  "description" => $add_image_form->getField('description'.$current_image)->getValue()
						);
						 
						try {
						  $ret = $fb->api('/' . $config->site->facebook->page_id . '/feed', 'POST', $params);
						  //echo 'Successfully posted to Facebook Fan Page';
						} catch(Exception $e) {
						  //echo 'Facebook Error: ' . $e->getMessage();
						  //die;
						}
						
					} elseif ($type_gallery_name == "Video") {
						//Post link to Video Instead
						$params = array(
						  // this is the access token for Fan Page
						  "access_token" => $acc_token,
						  "message" => $message,
						  "link" => $link_video,
						  "name" => $link_title,
						  "caption" => $config->site->name,
						  "description" => $add_image_form->getField('description'.$current_image)->getValue()
						);
						 
						try {
						  $ret = $fb->api('/' . $config->site->facebook->page_id . '/feed', 'POST', $params);
						  //echo 'Successfully posted to Facebook Fan Page';
						} catch(Exception $e) {
						  //echo 'Facebook Error: ' . $e->getMessage();
						  //die;
						}
					} elseif ($type_gallery_name == "Audio") {
						//Post link to Video Instead
						$params = array(
						  // this is the access token for Fan Page
						  "access_token" => $acc_token,
						  "message" => $message,
						  "link" => $link_audio,
						  "name" => $link_title,
						  "caption" => $config->site->name,
						  "description" => $add_image_form->getField('description'.$current_image)->getValue()
						);
						 
						try {
						  $ret = $fb->api('/' . $config->site->facebook->page_id . '/feed', 'POST', $params);
						  //echo 'Successfully posted to Facebook Fan Page';
						} catch(Exception $e) {
						  //echo 'Facebook Error: ' . $e->getMessage();
						  //die;
						}
					}		  
			    }
			
            }
		
			$current_image++;
			
			//WP - DIRTY SORTING FIX TO CREATE WP MULTIPOST
			//if ($type_gallery == 1) {
			//	$current_image++;
			//} else {	
			//	$current_image--;
			//}
			
			
        } //End while loop.

		// SEND SINGLE POST TO WP
        if ($content_wp  && $wp_enabled && ($wp_author_id>0) && $usr && $pwd ) {
       				
			$post_content = array('post_type'=>'post','post_status'=>'publish','post_title'=>$content_title,'terms_names'=> array( $wp_taxonomy_tags => $tags_arr, $wp_taxonomy_categories => $categories_arr ), 'post_author'=>$wp_author_id, 'post_content'=>(''.$content_html.''), 'post_thumbnail'=>$content_thumb);
			//var_dump($post_content);
			$client = new IXR_Client($xmlrpc);
			$res = $client->query('wp.newPost',1, $usr, $pwd, $post_content);
			$post_data = $client->getResponse();
			//var_dump($post_data);
			
	    }
	    
	    //Send Email Notification to Admin
	    $user->sendUploadEmailAdmin();

	    //die;
		header('Location: '.MK_Utility::serverUrl('index.php'), true, 302);
		exit;
    
	}

    //Include header.
    require_once 'header.php'; ?>   

    <script>
        jQuery(document).ready( function() { <?php  
        
            for ( $i = 0; $i < count($cropped_url); ++$i ) { //Start PHP loop for each images tags.?> 
            
                jQuery(".tm-input:eq(<?php echo $i; ?>)").tagsManager({
                    tagsContainer: '.field-tags<?php echo $i; ?>',
                    prefilled: jQuery(".tm-input:eq(<?php echo $i; ?>)").attr('data-value').split(',')
                });<?php
                
            } ?>
			
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green'
			});	

        });
       
       

    </script>

    <div class="main-container">
    
        <div class="main wrapper clearfix pure-g-r">  
  
            <!-- Content Section Starts Here -->
            <?php include ('includes/upload-details.php') ?>
  
        </div>
  
    </div><?php 

    //Include Footer.
    include ('footer.php'); } 
	
else { //Not via POST method. Bounce outta here...

    header('Location: '.MK_Utility::serverUrl('index.php'), true, 302);
    exit;

} ?>
