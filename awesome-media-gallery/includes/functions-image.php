<?php
$notification = '';

// If an image ID is defined & exists then get an instance of that image

if( $image_id <> '' || $slug <> '' ) {//Query String Exists
    
        try {
            
            if(!empty($image_id)) {
            
                $image = MK_RecordManager::getFromId( $image_module->getId(), $image_id );
            
            } elseif(!empty($slug)) {
                
                $image = $image_module->searchRecords(array(
                            array('literal' => ' image_slug = "' . $slug . '" ')
                    ));
                    
                $image = array_pop($image);
                
               if(!empty($image)) {
                    
                    $image_id = $image->getId();
                
                } else {
                
                    throw new Exception('Image could not be loaded');
                
                }
                
            }
      
            if ($image=='') {
                throw new Exception('Image could not be loaded"]');
		  	}
    
    } catch( Exception $e ) {
    
        header('Location: ' . $config->site->url . 'not-found.php', true, 301);
        exit;
    
    }
	
  if ($image) { //ID for image found
  
    $url_part = getImageTypeName( $image->getMetaValue('type_gallery') );
  
	if( empty($session->images_viewed) ) { //Create Image View Array
		$session->images_viewed = array();
	}
	$images_viewed = $session->images_viewed;
	
	if( !array_key_exists($image->getId(), $images_viewed) ) { //INCREMENT IMAGE VIEWS
		$image
			->setViews( $image->getViews() + 1 )
			->save();
		$images_viewed[$image->getId()] = true;
		$session->images_viewed = $images_viewed;
		//$notification .= '<span>Views updated</span>';

	}
	
	if( is_file($image->getImage()) && ($image_details = getimagesize($image->getImage())) )  { //Check if image exists
		$image_details = array( //Create image details array
			'date_added' => date($config->site->date_format, strtotime($image->getDateAdded())),
			'size' => filesize($image->getImage()),
			'width' => $image_details[0],
			'height' => $image_details[1]
		);
	} else {
	
		//header('Location: '.MK_Utility::serverUrl('/'), true, 302);
		//$notification .= '<span>Unable to find image or get file information</span>';
		
	}
	

	
    //Set the Title of the html page
    $head_title[] = $image->getTitle();
    
    /******************** Record has been selected for deletion and the user has permission *********************/
   
   
    if( $action == 'delete-image' && $image->canDelete( $user ) )
    {

      $notification_text = '<a href="' . $user->getUsername() . '">'.$user->getDisplayName().'</a> '.$langscape["deleted image"].' '.$image->getTitle();

      $user->addNotification($notification_text);
      
      $image->delete();
      header('Location: '.MK_Utility::serverUrl('index.php'), true, 302);
    
    }

    /**************************************** User wants to report record ****************************************/
    
//THIS IS NOW VIA AJAX!
    
    if( $action == 'report-image' && $user->isAuthorized() ) {
    
      $report_image_settings = array(
        'attributes' => array(
          'class' => 'clear-fix standard'
        )
      );
    
      $report_image_structure = array(
        'yes' => array(
          'type' => 'submit',
          'attributes' => array(
            'value' => ''.$langscape["Yes"].'',
            'class' => 'button-red'
          )
        ),
        'no' => array(
          'type' => 'submit',
          'attributes' => array(
            'value' => ''.$langscape["No"].'',
            'class' => 'button-green'
          )
        ),
      );

      $report_image_form = new MK_Form($report_image_structure, $report_image_settings);
      
      //Yes/No Delete Popup.
      if( $report_image_form->isSubmitted() && $report_image_form->getField('no')->getValue() ) {
      
        header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
        //$notification .= '<span>User not reported</span>';			
        exit;
        
      }	elseif( $report_image_form->isSuccessful() ) {
      
        $emailResponse = emailReportedImage();
        //$notification .= '<span class="success">This image has been reported</span>';
        
      } else {
      
        $notification = '<span>'.$langscape["Are you sure you want to report this image?"].'</span>';
        echo $report_image_form->render();
        
      }
    }
    
    /************** LIKE has been selected for deletion and the user has permission ******************/
    
    
//This is now via AJAX!
    if( $action == 'delete-comment-like' && $comment <> '')
    {
      try
      {
        $comment = MK_RecordManager::getFromId( $image_comment_module->getId(), $comment );

        $image_comment_likes = $image_comment_like_module->searchRecords(array(
          array('field' => 'comment', 'value' => $comment->getId()),
          array('field' => 'user', 'value' => $user->getId()),
        ));

        if( count($image_comment_likes) == 0 ) //CREATE A RECORD TO STORE LIKES ??
        {
          $like = MK_RecordManager::getNewRecord( $image_comment_like_module->getId() );
          $like
            ->setUser( $user->getId() )
            ->setComment( $comment->getId() )
            ->save();
        }
        else   //DELETE LIKE RECORD
        {
          $image_comment_likes = array_pop($image_comment_likes);
          $image_comment_likes->delete();
        }
        
        //$notification .= '<span class="success">Like record deleted</span>';
      }
      catch( Exception $e ){}
          //$notification .= '<span>Problem deleting like record</span>';
          header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
      exit;
    }
    
    
    
  // Comment has been selected for deletion and the user has permission
    if( $action == 'delete-comment' && ( $comment_id <> '' ) ) {
    
      $comment = MK_RecordManager::getFromId( $image_comment_module->getId(), $comment_id );
    
      if( $comment->canDelete($user) ) { //user can delete

        //$notification .= '<span>Deleting comment</span>"';
        
        $delete_comment_settings = array(
            'attributes' => array(
              'class' => 'clear-fix standard'
            ));
      
        $delete_comment_structure = array(
            'yes' => array(
              'type' => 'submit',
              'attributes' => array(
                'value' => ''.$langscape["Yes"].'',
                'class' => 'button-red'
              )
            ),
            'no' => array(
              'type' => 'submit',
              'attributes' => array(
                'value' => ''.$langscape["No"].'',
                'class' => 'button-green'
              )
            ),
        );

        $delete_comment_form = new MK_Form($delete_comment_structure, $delete_comment_settings);
      
        if( $delete_comment_form->isSubmitted() && $delete_comment_form->getField('no')->getValue() ) { //Submit and NO
        
          header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
          exit;
        
        } elseif( $delete_comment_form->isSuccessful() ) 	{ //Successful = YES
        
          $comment->delete();
          //$notification .= '<span class="success">This comment has been deleted</span>';
          //header('Location: '.MK_Utility::serverUrl($this_filename.'?image='.$image->getId()), true, 302);
          
        } else { //Question
        
          //$notification .= '<span>Are you sure you want to delete this comment?</span>';
          print $delete_comment_form->render();
          
        }

      } else { //user can NOT delete

        //$notification .= '<span>User does not have sufficient privileges to delete this comment</span>';
        //header('Location: '.MK_Utility::serverUrl($this_filename.'?image='.$image->getId()), true, 302);
        //exit();
      }

    }


  //Record has been selected for editing and the user has permission
    
    if( $section == 'edit' && $image->canEdit( $user ) )
    {
      $edit_image_settings = array(
        'attributes' => array(
          'class' => 'clear-fix standard'
          ));
    
      $edit_image_structure = array(
        'title' => array(
          'label' => ''.$langscape["Title"].'',
          'validation' => array(
            'instance' => array()
          ),
          'value' => $image->getTitle()
        ),
        'tags' => array(
          'label' => ''.$langscape["Tags"].'',
          'type' => 'tags',
          'tooltip' => ''.$langscape["Separate with a comma"].' ','.',
          'value' => $image->getTags()
        ),
        'description' => array(
          'label' => ''.$langscape["Description"].'',
          'type' => 'textarea',
          'value' => $image->getDescription()
        ),
        'submit' => array(
          'type' => 'submit',
          'attributes' => array(
            'value' => ''.$langscape["Save Changes"].''
          )
        ),
        'cancel' => array(
          'type' => 'submit',
          'attributes' => array(
            'value' => ''.$langscape["Cancel"].'',
            'class' => 'button-red'
          )
        )
      );

      $edit_image_form = new MK_Form($edit_image_structure, $edit_image_settings);

      if( $edit_image_form->isSubmitted() && $edit_image_form->getField('cancel')->getValue() ) { //Submitted & No
      
        header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
        exit;
        
      } elseif( $edit_image_form->isSuccessful() ) { //Submitted & YES
        
        $image
          ->setTitle( $edit_image_form->getField('title')->getValue() )
          ->setTags( $edit_image_form->getField('tags')->getValue() )
          ->setDescription( $edit_image_form->getField('description')->getValue() )
          ->save();

          //$notification .= '<span class="success">Your changes have been saved.</span>';
        
      } else {
      
        //$notification .= '<span>Editing</span>';
        print $edit_image_form->render();
        
      }

    }

    

    
    if( $user->isAuthorized() ) { //If Logged In	// DO ACTIONS

		//USED FOR FAVORITE HEARTS FUNCTION
        $favourite = $image_favourite_module->searchRecords(array(
          array('field' => 'image', 'value' => $image->getId()),
          array('field' => 'user', 'value' => $user->getId())
        ));   
        //var_dump($favourite);
        $favourite = array_pop($favourite); 
   
   
   		//IMAGE ACTIONS
        if( $action == 'remove-featured' ) { //REMOVE FEATURED
          $image
            ->isFeatured(false)
            ->save();
          
          //$notification .= '<span class="success">Featured image removed</span>';
          header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
          exit;
        }
        
        
        if( $action == 'add-featured' ) { // ADD FEATURED
          $image
            ->isFeatured(true)
            ->save();
            
            //$notification .= '<span class="success">Featured image added</span>';
            header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
            exit;
        }

        if( $action == 'remove-slider' ) { //REMOVE FEATURED
          $image
            ->isSlider(false)
            ->save();
          
          //$notification .= '<span class="success">Slider image removed</span>';
          header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
          exit;
        }
        
        
        if( $action == 'add-slider' ) { // ADD FEATURED
          $image
            ->isSlider(true)
            ->save();
            
            //$notification .= '<span class="success">Slider image added</span>';
            header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
            exit;
        }

        if( $action == 'remove-carousel' ) { //REMOVE FEATURED
          $image
            ->isCarousel(false)
            ->save();
          
          //$notification .= '<span class="success">Carousel image removed</span>';
          header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
          exit;
        }
        
        
        if( $action == 'add-carousel' ) { // ADD FEATURED
          $image
            ->isCarousel(true)
            ->save();
            
            //$notification .= '<span class="success">Carousel image added</span>';
            header('Location: '.MK_Utility::serverUrl($url_part . '/'.$image->getImageSlug()), true, 302);
            exit;
        }
        
    }


  ///////// IF NO section is set or if the record has just been successfully edited then display the image details

    //if( $section == '' || ( $section == 'edit' && !empty($edit_image_form) && $edit_image_form->isSuccessful() ) ) 

    if( $section == '' ) {
    
      $gallery_id = $image->getGallery();
    
      $parent_galleries = array();

      //$parent_galleries = makeBreadCrumbs();

          
      if( $author = $image->getUser() ) //IF USER SPECIFIC
      {
        $author = MK_RecordManager::getFromId($user_module->getId(), $author);
        $search_criteria = array(
          array('field' => 'id', 'value' => $author->getId())
        );
  
		//GET ALL IMAGES FOR VIEW NEXT PREV BUTTONS
        $search_criteria = array(
            array('literal' => ' `gallery` IN (' . $gallery_id . ') ')
		);

    /* //CODE TO CREATE SUB GALLERIES - DISABLED
        $images_in_this_gallery = $image_module->searchRecords($search_criteria);
      }
      
      elseif( $gallery = MK_Request::getQuery('gallery') ) //IF GALLERY SPECIFIC
      {
        $gallery = MK_RecordManager::getFromId($gallery_module->getId(), $gallery);
        
        
        $sub_records = $gallery->getSubRecords();
      
        $sub_galleries = array();
        $immediate_galleries = array();
        foreach($sub_records as $sub_gallery)
        {
          if( $gallery->getId() === $sub_gallery->getParentGallery() )
          {
            $immediate_galleries[] = $sub_gallery;
          }
          $sub_galleries[] = $sub_gallery->getId();
        }
        $sub_galleries[] = $gallery->getId();
        
        
      
        $search_criteria = array(
          array('literal' => ' `gallery` IN ('.implode(', ', $sub_galleries).') ')
        );
    */
        $images_in_this_gallery = $image_module->searchRecords($search_criteria);
        
      } else { //What is this for? DH. ENGAGE 27/08/13
      
        $images_in_this_gallery = $image_module->getRecords();
      }

      $_previous_image = null;
      $_is_next_image = false;
      $previous_image = null;
      $next_image = null;
    
      foreach($images_in_this_gallery as $gallery_image)
      {
        if($gallery_image->getId() === $image->getId())
        {
          if(!empty($_previous_image))
          {
            $previous_image = $_previous_image;
          }
          $_is_next_image = true;
        }
        elseif($_is_next_image === true)
        {
          $next_image = $gallery_image;
          break;
        }			
        $_previous_image = $gallery_image;
      }

      if( strpos($config->site->referer, $this_filename) !== false && strpos($config->site->referer, 'image/') === false ) { //Back to images button
        $back_to_images = '<a href="' . $config->site->referer . '">'.$langscape["Back to images"].'</a>';
      }
    
      if( !empty($parent_galleries) ) { //What is this for? DH. ENGAGE 27/08/13
        $breadcrumb_text = !empty($parent_galleries) ? implode(' / ', $parent_galleries) : '';
      }
      
    }
  
  }  else { //No ID for image found
		//header('Location: '.MK_Utility::serverUrl('/'.$this_filename), true, 302);
		//$notification .= '<span>No image with that ID</span>';
	}
  
} else { //No Query String Exists
  //$notification .= '<span>'.$langscape["Image could not be loaded"].'</span>';
  header('Location: http://' . $config->site->referer . '/');
}
?>
