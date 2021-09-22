<?php

if (empty($field_module)) {
	$field_module = MK_RecordModuleManager::getFromType('module_field');
}

$paginator_slider = new MK_Paginator();

//SET TO 6 SLIDES
$paginator_slider
  ->setPerPage($config->site->slider->count)
  ->setPage( MK_Request::getQuery('page_s', 1) );

switch ($config->site->slider->media_source) {
	case "FEATURED":
		$field_1 = array('field' => 'featured', 'value' => 1);
		break;
	case "SLIDER":
		$field_1 = array('field' => 'slider', 'value' => 1);
		break;
}

switch ($config->site->slider->media_type) {
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
    array('field' => 'name', 'value' => 'slider_date')
));

$order_by_field = array_pop( $order_by_field );

$options                 = array(
                            'order_by' => array(
                                'field' => $order_by_field->getId(),
                                'direction' => 'DESC'
                                )
                            );

//PERFORM SEARCH
$images_sli = $image_module->searchRecords(
	$search_array, 
	$paginator_slider,
	$options
	);


if( !empty($images_sli) && (count($images_sli) > 1) ) {
	
	//OWL SLIDER
	if ($config->site->slider->type == "OWL") {

	//OPTIONS Here: http://owlgraphic.com/owlcarousel/index.html
	
		echo '<div id="slider" class="owl-carousel owl-theme">';
	    $counter = 0;
	    foreach( $images_sli as $image_sli ) {
			//SHOW THE RESULTS
		
			$extension        = explode('.', $image_sli->getImage()); //File extension check.
			$extension        = array_pop($extension); //Pop extension.
			$popup_url		  = '';
			$icon 			  = '';
			$custom_link	  = $image_sli->getMetaValue('link_url');
			$href			  = getImageTypeName($image_sli->getMetaValue('type_gallery')) . '/' . $image_sli->getImageSlug();
			$attr		  	  = 'href="' . ((!empty($custom_link))?$custom_link:$href) . '"';
			$extra_class 	  = '';
			
			$src_string = 'library/thumb.php?f='.$image_sli->getImage().'&amp;m=crop&amp;w=' . $wsi . '&amp;h=' . $hsi;
		
			//Build links based on item type.
			switch ( getImageTypeName($image_sli->getMetaValue('type_gallery')) ) { 
						
				case 'video':
					
					$icon =  '<i class="fa fa-youtube-play icon rollover-icon"></i>';
					
					if ( !isVine( $image_sli->getVideoUrl() ) && $config->site->slider->enable_video_play == 'true' ) {
						$attr = 'class="owl-video" onclick="event.preventDefault();" href="' . $image_sli->getVideoUrl() . '"';
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
			//if (!$config->site->grid->hover_enable_icon || ( ($deviceType == 'phone') || ($deviceType == 'tablet') ) ) {
				$icon="";
			//}
		    ?>
	     
	       <a <?php echo $attr; ?> title="<?php echo $image_sli->getTitle(); ?>"><div class="item"><?php echo $icon; ?><img class="<?php //echo ( isset($extra_class) ? ' ' . $extra_class : '' ); echo $class_effect; ?>" src="<?php echo $config->site->url . $src_string; ?>" alt="<?php echo $image_sli->getTitle(); ?>"></div></a>
	       
	<?php } // FOR EACH
		
	echo '</div>';




//ULTIMATE SMART SLIDER - NOT SETUP IGNORE
} elseif ($config->site->slider->type == "CUSTOM") {

//OPTIONS HERE: http://www.davidbo.dreamhosters.com/plugins/slider/documentation/index.html
?>

	<div id="slider" class="as_slider">
	 
	  <img src="assets/fullimage1.jpg" data-effect="<?php echo $config->site->slider->effect_ultimate; ?>" data-captioneffect="slide" data-caption="The Last of us">
	  <img src="assets/fullimage2.jpg" data-effect="<?php echo $config->site->slider->effect_ultimate; ?>" data-captioneffect="fade" data-caption="GTA V">
	  <img src="assets/fullimage3.jpg" data-effect="<?php echo $config->site->slider->effect_ultimate; ?>" data-captioneffect="fromLeft" data-caption="Mirror Edge">
	 
	</div>

<?php }

}
?>