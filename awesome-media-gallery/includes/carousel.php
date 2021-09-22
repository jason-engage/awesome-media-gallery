<?php

//FIND SELECTED IMAGES OR FEATURED IMAGES
//$config->site->carousel->media_source

//Include Videos?
//$config->site->carousel->media_type

if (empty($field_module)) {
	$field_module              = MK_RecordModuleManager::getFromType('module_field');
}

if (empty($user_module)) {
	$user_module = MK_RecordModuleManager::getFromType('user'); //To Return images by user category
}

$num_items_car = ($deviceType == 'phone')?intval($config->site->carousel->count/2):intval($config->site->carousel->count);

$paginator_carousel = new MK_Paginator();

$paginator_carousel
  ->setPerPage($num_items_car)
  ->setPage( MK_Request::getQuery('page_c', 1) );

if ($config->site->carousel->media_type <> "MEMBERS") {
	switch ($config->site->carousel->media_source) {
		case "FEATURED":
			$field_1 = array('field' => 'featured', 'value' => 1);
			break;
		case "CAROUSEL":
			$field_1 = array('field' => 'carousel', 'value' => 1);
			break;
	}
	
	switch ($config->site->carousel->media_type) {
		case "IMAGE":
			$field_2 = array('field' => 'type_gallery', 'value' => 1);
			break;
		case "VIDEO":
			$field_2 = array('field' => 'type_gallery', 'value' => 2);
			break;
		case "AUDIO":
			$field_2 = array('field' => 'type_gallery', 'value' => 3);
			break;
		case "MIX":
			$field_2 = array();
			break;
	}

	$search_array = ($field_2)? array($field_1,$field_2):array($field_1);

	//GET FIELD ID TO ORDER BY
    $order_by_field = $field_module->searchRecords(array(
        array('field' => 'module', 'value' => $image_module->getId()),
        array('field' => 'name', 'value' => 'carousel_date')
    ));
    
    $order_by_field = array_pop( $order_by_field );
    
    $options                 = array(
                                'order_by' => array(
                                    'field' => $order_by_field->getId(),
                                    'direction' => 'DESC'
                                    )
                                );

	$images_car = $image_module->searchRecords(
		$search_array, 
		$paginator_carousel,
		$options
		);
} else { 

	$members = $user_module->searchRecords(array(), $paginator_carousel, NULL);
	$images_car = array();
	
	if( !empty($members) ) {
		 foreach( $members as $member ) {
		 	if ($member->getAvatar()<>'') {
			 	$images_car[] = $member;
		 	}
		 }
	}

}


if( !empty($images_car) && (count($images_car) > 1) ) { 
	
	echo '<div id="owl" class="owl-carousel owl-theme">';
    $counter = 0;
    
	if ($config->site->carousel->media_type <> "MEMBERS") {
	    foreach( $images_car as $image_car ) {
			//SHOW THE RESULTS
		
			$extension        = explode('.', $image_car->getImage()); //File extension check.
			$extension        = array_pop($extension); //Pop extension.
			$popup_url		  = '';
			$icon 			  = '';
			$attr		  	  = 'href="' . getImageTypeName($image_car->getMetaValue('type_gallery')) . '/' . $image_car->getImageSlug() . '"';
			$extra_class 	  = '';
			
			$src_string = 'library/thumb.php?f='.$image_car->getImage().'&amp;m=crop&amp;w=' . $wci . '&amp;h=' . $hci;
		
			//Build links based on item type.
			switch ( getImageTypeName($image_car->getMetaValue('type_gallery')) ) { 
						
				case 'video':
					
					$icon =  '<i class="fa fa-youtube-play icon rollover-icon"></i>';
					
					if ( !isVine( $image_car->getVideoUrl() ) && $config->site->carousel->enable_video_play == 'true' ) {
						$attr = 'class="owl-video" onclick="event.preventDefault();" href="' . $image_car->getVideoUrl() . '"';
						$extra_class = '-video';
						$icon = '';
					}
				    break;
			    
				case 'image':
					
					if ($extension == 'gif') {
						$icon = '<i class="bolt icon rollover-icon"></i>';
					} else {
						$icon = '<i class="camera icon rollover-icon"></i>';
					}
		
					break;
					
				case 'audio':
				
					$icon = '<i class="fa fa-soundcloud icon rollover-icon"></i>';
					break;
					
				default:
					
			}
		
			//HIDE ICON IF DISABLED
			if (!$config->site->grid->hover_enable_icon || ( ($deviceType == 'phone') || ($deviceType == 'tablet') ) ) {
				$icon="";
			}
		    ?>
	     
	       <a <?php echo $attr; ?> title="<?php echo $image_car->getTitle(); ?>"><div class="item"><?php echo $icon; ?><img src="<?php echo $config->site->url . $src_string; ?>" alt="<?php echo $image_car->getTitle(); ?>"></div></a>
	       
	<?php $counter++;
		} // FOR EACH
	} else { //LOOP MEMBERS
	    foreach( $images_car as $image_car ) {
			$src_string = 'library/thumb.php?f='.$image_car->getAvatar().'&amp;m=crop&amp;w=' . $wci . '&amp;h=' . $hci;
			$attr		= 'href="' . $config->site->url . $image_car->getUsername() . '"';
			$icon = '<i class="fa fa-user icon rollover-icon"></i>'; 
			
			//HIDE ICON IF DISABLED
			if (!$config->site->grid->hover_enable_icon || ( ($deviceType == 'phone') || ($deviceType == 'tablet') ) ) {
				$icon="";
			}			
			?>
			
				       <a <?php echo $attr; ?> title="<?php echo $image_car->getDisplayName(); ?>"><div class="item"><?php echo $icon; ?><img src="<?php echo $config->site->url . $src_string; ?>" alt="<?php echo $image_car->getDisplayName(); ?>"></div></a>

		<?php
		}	
	}

	
echo '</div>';

}

?>