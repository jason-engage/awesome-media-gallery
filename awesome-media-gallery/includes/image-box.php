<?php
//List Item Single Image Box
//This is an include file.
//image-box.php

$total_favourites = $image->getTotalFavourites(); //Total favs for this item.
$total_comments   = $image->getTotalComments(); //Total comment for this item.
$total_views      = $image->getViews(); //Total views for this item.
$gallery_name     = $image->getGallery() ? $image->objectGallery()->getName() : ''; //Gallery name the item belongs to.
$gallery_id       = $image->getGallery() ? $image->objectGallery()->getId() : ''; //Id of the gallery the item belongs to.
$image_type		  = $image->getMetaValue('type_gallery'); //Type of gallery the item belongs to.
$title            = MK_Utility::escapeText($image->getTitle()); //Title of the item.
$extension        = explode('.', $image->getImage()); //File extension check.
$extension        = array_pop($extension); //Pop extension.
$author           = $image->objectUser(); //Author of the item.
$extra_class      = ''; //Set extra class to empty for starters.
$popup_url		  = '';
$fancy_class 	  = '';

if ($config->site->grid->type == "MASONRYJS") {
	
	$src_string = 'library/thumb.php?f='.$image->getImage().'&amp;m=width&amp;w=' . $wim . '&amp;q=' . $config->site->media->jpg_quality . '&amp;c=' . $config->site->media->png_compression;

} else {

	if ( !$image->getMetaValue('crop_top') ) {
	    $src_string = 'library/thumb.php?f='.$image->getImage().'&amp;m=crop&amp;w=' . $wim . '&amp;h=' . $him . '&amp;q=' . $config->site->media->jpg_quality . '&amp;c=' . $config->site->media->png_compression;
	}else {
	    $src_string = 'library/thumb.php?f='.$image->getImage().'&amp;m=crop-top&amp;w=' . $wim . '&amp;h=' . $him . '&amp;q=' . $config->site->media->jpg_quality . '&amp;c=' . $config->site->media->png_compression;
	} 

} 

//Build links based on item type.
if ( $image_type == 2 ) { //VIDEO
	
	$popup_url	= $image->getMetaValue('video_url');
	
	if ( isVine ( $popup_url ) ) { //Its Vine

    	$popup_url = convertVine($image->getMetaValue('video_url'));
    }
	
	$icon_rollover_class = 'fa fa-youtube icon';
	
    if ( isYouTube( $popup_url ) ) {
        $extra_class = 'youtube';
		$icon_rollover_class = 'fa fa-youtube icon';
		$icon_class = 'fa fa-youtube icon';
    }
    
    if ( isVimeo( $popup_url ) ) {
        $extra_class = 'vimeo';
        $icon_rollover_class = 'fa fa-vimeo-square icon';
        $icon_class = 'fa fa-youtube icon';
    }
    
	if ( isVine( $popup_url ) ) {
        $extra_class = 'vine';
		$icon_rollover_class = 'fa fa-vine icon';
		$icon_class = 'fa fa-youtube icon';
		$fancy_class = "fancybox.iframe";
    }
    
} elseif  ($image_type == 1 ) { //IMAGE

	$icon_class = 'pictures icon';
    $popup_url  = $image->getImage();

} elseif  ($image_type == 3 ) {

    $icon_class = 'fa fa-soundcloud icon';
    //$popup_url = "/soundcloud/" . $image->getSoundcloudId();
    $popup_url  = 'https://w.soundcloud.com/player/?url=https://api.soundcloud.com/tracks/' . $image->getSoundcloudId() . '&show_artwork=true&auto_play=true';
	$fancy_class = "fancybox.iframe";

}

 ?>		

<?php
	$icon = "";
	$class_effect = "";
	//SHOW ICON IF ENABLED
	if (isset($config->site->grid->hover_enable_icon)) {
		
		switch ($image_type) {
			case 1:
				if ($extension == 'gif') {
					$icon = '<i class="bolt icon rollover-icon"></i>';
				} else {
					$icon = '<i class="camera icon rollover-icon"></i>';
				}
				break;
			case 2:
				$icon =  '<i class="' . $icon_rollover_class . ' icon rollover-icon"></i>';
				break;
			case 3:
				$icon = '<i class="fa fa-soundcloud icon rollover-icon"></i>';
				break;
		}		
	
	}
	//SHOW FILTER EFFECT IF ENABLED
	if (!empty($config->site->grid->hover_style)) {
			$class_effect = ' ' . $config->site->grid->hover_style;		
	}
	
?>--><li class="pure-u-1-4 box <?php echo $css_box_column_class; ?>">
  
    <figure>
      
        <a href="<?php echo getImageTypeName($image_type) . '/' .$image->getImageSlug(); ?>" title="<?php echo $title; ?>">
        
            <span class="image loading <?php echo ( isset($extra_class) ? $extra_class : '' ); ?>"><?php 
             echo $icon;
                 
                ?>
              
                <img class="meta-image<?php echo ( isset($extra_class) ? ' ' . $extra_class : '' ); echo $class_effect; ?>" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" data-src="<?php echo $src_string; ?>" alt="<?php echo $title; ?>">
             
            </span>
            
        </a>


        <?php if ($config->site->grid->enable_caption) { ?>
        <figcaption>
        
            <div class="pure-g">
                
                <!-- Author Avatar -->
                <div class="pure-u-1-12">
                
                    <span class="meta-avatar"><a href="<?php echo getProfileUrl( $author->getId() ); ?>"><img src="library/thumb.php?f=<?php echo ($author->getAvatar() ? $author->getAvatar() : $config->site->default_avatar ); ?>&amp;h=24&amp;w=24&amp;m=crop" alt="<?php echo $author->getDisplayName(); ?>"></a></span>
                
                </div>
                
                <!-- Author -->
                <div class="pure-u-1-2 name">
                    
                    <span class="meta-name"><a href="<?php echo getProfileUrl( $author->getId() ); ?>"><?php echo $author->getDisplayName(); ?></a></span>
                
                </div>

                <!-- Date Time Uploaded -->
                <div class="pure-u-1-12">
                    <span class="meta-date"><?php echo time_since(time() - strtotime($image->getDateAdded())); ?></span>
                </div>
          
            </div>
          
            <!-- Title -->
            <span class="meta-title"><a href="<?php echo getImageTypeName($image_type) . '/' .$image->getImageSlug(); ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a></span>

            <!-- Gallery Name -->
            <span class="meta-title meta-gallery"><i class="<?php echo $icon_class; ?>"></i><a href="gallery/<?php echo getImageTypeName($image_type) . '/' . urlencode($gallery_name); ?>" title=" <?php echo $gallery_name; ?>"><?php echo $gallery_name; ?></a></span><?php
          
            if( $tags = $image->getTags() ) {

                $tags = explode(',', $tags); ?>
            
                <!-- Tags -->
                <span class="meta-tag">
                    <i class="tag icon"></i><?php
                    
                    foreach( $tags as $tag ) {
                        $tag = trim($tag); ?>
                        <a href="tag/<?php echo urlencode($tag); ?>"><?php echo $tag; ?></a><?php
                    } ?>
                
                </span><?php
              } ?>
          
        </figcaption>
        <?php } ?>
        
        <?php if ($config->site->grid->enable_stats) { ?>
        <div class="meta-icons">
          <div class="icon-wrapper">
            <!-- Favorite Heart -->
            <?php echo returnFavouriteHeart(); ?>
		
		<?php 
			if( !empty($config->site->media->comments_type) && ( $config->site->media->comments_type <> 'DISABLED') )  {
		?> 
            <!-- Comments -->
            <span data-comment-id="1" class="meta-comment">
                <a href="<?php echo getImageTypeName($image_type) . '/' . $image->getImageSlug(); ?>#comment-anchor" title="<?php echo $title; ?>"><i class="comment icon"></i></a>
                
                <span class="text"><?php
                
                if ( $config->site->media->comments_type == 'DEFAULT' ) { 
                	echo ( $total_comments > 999 ? '999+' : $total_comments );
                	
                } else if ( $config->site->media->comments_type == 'FACEBOOK' ) {
	            ?>
	            	<fb:comments-count href=<?php echo $config->site->url; echo getImageTypeName($image_type) . '/' . $image->getImageSlug(); ?>></fb:comments-count>

                <?php
                }
                ?></span>

            </span>
		<?php 
			}
		?>
          
            <!-- Views -->
            <span data-view-id="1" class="meta-views">
                <a href="<?php echo $popup_url; ?>" rel="group" class="fancybox-media <?php echo $fancy_class; ?>" title="<?php echo $title; ?>" alt="<?php echo $config->site->url; echo getImageTypeName($image_type) . '/' . $image->getImageSlug(); ?>"><i class="eye icon"></i></a>
                <span class="text"><?php echo ( $total_views > 999 ? '999+' : $total_views ); ?></span>
            </span>
            
            <?php
				if ( $user->isAuthorized() && $image->canDelete( $user ) && $config->site->media->enable_approval && (!$image->getApproved()) && !empty( $order_by ) && $order_by == 'queue'  ) { ?>
            <!-- Approve / Delete Image -->
            <span class="meta-approve-delete">
            	<button class="approve-button" rel="image approve" data-id="<?php echo $image->getId(); ?>" title="<?php echo $langscape["Approve Media"]; ?>"><i class="fa fa-check-square icon"></i></button>
            	<a rel="image delete-image" href="<?php echo 'image.php?image=' . $image->getId(); ?>&amp;action=delete-image" title="<?php echo $langscape["Delete Media"]; ?>"><button class="delete-button"><i class="fa fa-minus-square icon"></i></button></a>
            </span>
			<?php } ?>
			
          </div>
        </div>
     <?php } //Enable Stats ?>
      
    </figure>

</li><!--