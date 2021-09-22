<?php
$num_items = !empty($config->site->grid->items_per_page) ? $config->site->grid->items_per_page : 12;
$num_items = ($deviceType == 'phone' && $config->site->mobile->enable_responsive_phone) ? $config->site->mobile->items_per_page : $num_items;


$paginator = new MK_Paginator();
	$paginator
		->setPerPage($num_items)
		->setPage( MK_Request::getQuery('page', 1) );

$paginator_options = array(
			'paging_range' => 4,
			'next_previous_link' => true,
			'prev_character' => '&lsaquo; ' . $langscape["prev"],
			'next_character' => $langscape["next"] . ' &rsaquo;',
			'first_last_link' => true,
			'first_character' => '&laquo;',
			'last_character' => '&raquo;',
		);
		
/****************************** START SORT BY ******************************/

//echo 'ORDER BY IS:' . $order_by . '<br>';


		$options_t = array('field' => 'type_gallery', 'value' => 1);


$search_options = buildSearchOptions($gallery_id, $gallery_module->getId(), $tag, $order_by, $gallery_type, $search_keywords);

//ACTIVE BUTTONS
if( $order_by == 'latest' ) {$a1 = ' pure-button-active'; }else{$a1 = '';} 
if( $order_by == 'popular'){$a2 = ' pure-button-active'; }else{$a2 = '';} 
if( $order_by == 'comments' ){$a3 = ' pure-button-active'; }else{$a3 = '';}   
if( $order_by == 'favorites' ){$a4 = ' pure-button-active'; }else{$a4 = '';}   
if( $order_by == 'featured' ){$a5 = ' pure-button-active'; }else{$a5 = '';} 
if( $order_by == 'queue' ){$a6 = ' pure-button-active'; }else{$a6 = '';} 

//if( $order_by == 'some_member_cat' ){$a6 = ' pure-button-active'; }else{$a6 = '';} 

if( $order_by == '' ){
  $a1 = ' pure-button-active'; 
} ?>

<?php

	if ( ( ($deviceType == 'phone') && (!$config->site->mobile->enable_responsive_phone) && ($config->site->ads->enable_home_top) ) || ($config->site->ads->enable_home_top) && ($deviceType <> 'phone') ) { ?>

	<!-- Top Ad Banner -->
	<?php include ('includes/ad-top.php'); ?> 

<?php 
	} ?>


<!-- Content Section GALLERY Starts Here -->

<!-- SLIDER -->
<?php

if ( ($config->site->slider->type <> 'NONE') && $disable_responsive ) { ?>
<div <?php if (!$config->site->slider->enable_fullscreen) { echo 'class="wrapper"'; } ?>>
	<?php include ('includes/slider.php'); ?>
</div>
<?php
}
?>   
                   
<section class="content gallery pure-u-1"> 
	
    <div class="gallery-nav">
	    
	    <div>
	    	
			<select class="media-select selectize" id="media-select" onchange="window.location.replace(this.options[this.selectedIndex].value);" style="display:none;">
		        <?php 
		        
		        	$current_url  = $_SERVER['REQUEST_URI'];
		        	$select_media 	= array();
		        	$select_media[] = array(
		        			'type'  => 0,
		        			'text'  => ''.$langscape["All"].'',
		        			'query' => 'index.php'
		        		);


                    if ( !empty($config->site->media->enable_images) ) {
	                    $select_media[] = array(
		        			'type'  => 1,
		        			'text'  => ''.$langscape["Images"].'',
		        			'query' => 'media/images/'
		        		);
		        	}
		        	
                    if ( !empty($config->site->media->enable_videos) ) {
	                    $select_media[] = array(
		        			'type'  => 2,
		        			'text'  => ''.$langscape["Videos"].'',
		        			'query' => 'media/videos/'
		        		);
					}
					
                    if ( !empty($config->site->media->enable_audio) ) {
	                    $select_media[] = array(
		        			'type'  => 3,
		        			'text'  => ''.$langscape["Audios"].'',
		        			'query' => 'media/audios/'
		        		);
					}
		        	
		        	/*
		        	$select_media = array(
		        		array(
		        			'type'  => 0,
		        			'text'  => ''.$langscape["All"].'',
		        			'query' => 'index.php'
		        		),
		        		array(
		        			'type'  => 1,
		        			'text'  => ''.$langscape["Images"].'',
		        			'query' => 'media/images/'
		        		),
		        		array(
		        			'type'  => 2,
		        			'text'  => ''.$langscape["Videos"].'',
		        			'query' => 'media/videos/'
		        		)
		        	);
		        	*/
		        	
		        	foreach ($select_media as $selected){
			        	if ( $selected['type'] == $gallery_type ){
				        	echo '<option value="' . $selected['query'] . '" selected>'. $selected['text'] .'</option>';
			        	} else{
				        	echo '<option value="' . $selected['query'] . '">'. $selected['text'] .'</option>';
			        	}
		        	}
		        ?>
			</select><?php 
	    	
	    	
	    	$image_type_url = ''; //( $image_type_name ) ? 'media/' . $image_type_name . '/' : ''; 
            
            if ( !empty( $current_gallery ) ) {
	        
		        $gallery_url = 'gallery/' . $gallery_type_name . '/' . urlencode($gallery_name) . '/'; 
	            
            } elseif ( !empty( $gallery_type ) ) {
	            	            
	            $gallery_url = 'media/' . getImageTypeNamePlural($gallery_type) . '/';
					            
            } else {
	        
		        $gallery_url = '';    
            }
            
            ?>
        

	        <a href="<?php echo $gallery_url . $image_type_url; ?>order-by/latest<?php echo (!empty($search_keywords) ? '&amp;s='.$search_keywords : '')?><?php echo (!empty($tag) ? '&amp;tag='.$tag : '')?>" title="<?php echo $langscape["Newest Images"];?>"><div class="pure-button pure-button-primary<?php echo $a1; ?>"><span><?php echo $langscape["Latest"];?></span></div></a>
	          
	        <a href="<?php echo $gallery_url . $image_type_url; ?>order-by/popular<?php echo (!empty($search_keywords) ? '&amp;s='.$search_keywords : '')?><?php echo (!empty($tag) ? '&amp;tag='.$tag : '')?>" title="<?php echo $langscape["Most Views"];?>"><div class="pure-button pure-button-primary<?php echo $a2; ?>"><span><?php echo $langscape["Popular"];?></span></div></a>
	        
			<?php 
				if( !empty($config->site->enable_comments) && $config->site->enable_comments && ($deviceType <> 'phone') ) {
			?> 
	        <a href="<?php echo $gallery_url . $image_type_url; ?>order-by/comments<?php echo (!empty($search_keywords) ? '&amp;s='.$search_keywords : '')?><?php echo (!empty($tag) ? '&amp;tag='.$tag : '')?>" title="<?php echo $langscape["Most Comments"];?>"><div class="pure-button pure-button-primary<?php echo $a3; ?>"><span><?php echo $langscape["Comments"];?></span></div></a>	
	        <?php } ?>
	         
	        <a href="<?php echo $gallery_url . $image_type_url; ?>order-by/favorites<?php echo (!empty($search_keywords) ? '&amp;s='.$search_keywords : '')?><?php echo (!empty($tag) ? '&amp;tag='.$tag : '')?>" title="<?php echo $langscape["Most Favorites"];?>"><div class="pure-button pure-button-primary<?php echo $a4; ?>"><span><?php echo $langscape["Favorites"] ;?></span></div></a>
	          
	        <a href="<?php echo $gallery_url . $image_type_url; ?>order-by/featured" title="<?php echo $langscape["Featured"];?>"><div class="pure-button pure-button-primary<?php echo $a5; ?>"><span><?php echo $langscape["Featured"];?></span></div></a>

			<?php 
				$tot_unapproved = getTotalUnapprovedCount();
				if ( $user->isAuthorized() && $user->objectGroup()->isAdmin() && $config->site->media->enable_approval && ($tot_unapproved > 0) ) {
			?> 
	        <a href="<?php echo $gallery_url . $image_type_url; ?>order-by/queue<?php echo (!empty($search_keywords) ? '&amp;s='.$search_keywords : '')?><?php echo (!empty($tag) ? '&amp;tag='.$tag : '')?>" title="<?php echo $langscape["Queue"];?>"><div class="pure-button pure-button-primary<?php echo $a6; ?>"><span><?php echo $langscape["Queue"];?></span></div></a>	
	        <?php } ?>
	        
	        	        
	        <?php /*
	        <a href="<?php echo $gallery_url . $image_type_url; ?>order-by/some_member_cat" title="Some Member Cat"><div class="pure-button pure-button-primary<?php echo $a6; ?>"><span>Some Member Cat</span></div></a>
	        */ ?>

			<?php
					if ($config->site->header->enable_search) { //ENABLE SEARCH IN ADMIN ?>
				 	 
                        <div class="search-box"><!-- Search Box --> 
       
                            <form name="form" autocomplete="on" enctype="multipart/form-data" method="get" action="index.php" class="pure-form">
            
                                <input placeholder="<?php echo $langscape["Search for Media"];?>" type="text" class="data input-text pure-input" name="s" id="s" value="">
                                
                                <button class="search-button">
                                    <i class="search icon "></i> 	    
                                </button>
            
                            </form> 
            
                        </div><?php 
         
					} ?>
						        
	    </div>
    </div>  

    <!-- Image grid-cs-cs Starts Here --><?php

    if( !empty($search_options['search_criteria']) ) { //Using Search Criteria
   
      	//echo 'Using Search Criteria';
        
        //var_dump($search_options);
        //die;
   
        $images = $image_module->searchRecords($search_options['search_criteria'], $paginator, $search_options['options']);

    } else {  //No Search Criteria
    
        $images = $image_module->getRecords($paginator, $search_options['options']);
    
    }

    if ( !empty( $images ) ) { ?>
    
        <ul class="awesome-gallery index" data-autoload="true" <?php if ($config->site->grid->type == "MASONRYJS") { echo 'data-masonry="true"'; } ?>><?php if ($config->site->grid->type == "MASONRYJS") { ?><div class="grid-sizer"></div><div class="gutter-sizer"></div><?php } ?><!--
	        <?php
    
        $counter = 0;
    
        foreach($images as $image)  {

            include 'includes/image-box.php';

        } //End Image Loop ?>-->
  
        </ul><?php
    
    } else { ?>
    
        <p class="alert alert-information"><?php echo $langscape["There are no items in this gallery."];?></p><?php
    
    } ?>
    
    <!-- Image grid-cs-cs Ends Here -->	            
	<?php 
		if ($config->site->grid->pagination_type == 0 || $config->site->grid->pagination_type == 2) { //LOAD MORE BUTTON & INFINITE SCROLL
	?>
    <div class="paginator"><?php echo $paginator->render($home_page.'?page={page}'.(!empty($current_gallery) ? '&amp;gallery='.$current_gallery->getId() : '').(!empty($search_keywords) ? '&amp;s='.$search_keywords : '').(!empty($order_by) ? '&amp;order-by='.$order_by : '')); ?></div>
	<?php 
		} elseif ($config->site->grid->pagination_type == 1) { //PAGE NUMBERS
	?>
	
    <div class="paginator2 clear-fix"><?php echo $paginator->render($home_page.'?page={page}'.(!empty($current_gallery) ? '&amp;gallery='.$current_gallery->getId() : '').(!empty($search_keywords) ? '&amp;s='.$search_keywords : '').(!empty($order_by) ? '&amp;order-by='.$order_by : ''), $paginator_options); ?></div>

	<?php 
		}
	?>
	


</section>
