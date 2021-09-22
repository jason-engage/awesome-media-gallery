<?php
//GET BREAD CRUMBS
//$breadcrumb = '<a href="./">Home</a> ';

$breadcrumb = '<a href="' . $config->site->url . '"><i class="icon house"></i></a>';

if ( isset( $gallery_id ) && $gallery_id <> '' ) {
    
    try {
        $current_gallery = MK_RecordManager::getFromId( $gallery_module->getId(), $gallery_id );
        
        if ( $current_gallery == '' ) {
            throw new Exception( "Gallery not found" );
        }
    }
    catch ( Exception $e ) {
        header( 'Location: ' . $config->site->url . 'not-found.php', true, 301 );
        exit;
    }
    
    $gallery_type = $current_gallery->getMetaValue( 'type_gallery' );
    
    if ( getImageTypeName($gallery_type) == 'image' ) {
        $breadcrumb .= '<i class="icon arrow-right-6"></i> <span><a href="media/images">'.$langscape["Images"].'</a></span>';
        
        $url_part = 'media/images';
        
    } elseif (getImageTypeName($gallery_type) == 'video') {
        $breadcrumb .= '<i class="icon arrow-right-6"></i> <span><a href="media/videos">'.$langscape["Videos"].'</a></span>';
        
        $url_part = 'media/videos';
        
    } elseif (getImageTypeName($gallery_type) == 'audio') {
        $breadcrumb .= '<i class="icon arrow-right-6"></i> <span><a href="media/audios">'.$langscape["Audios"].'</a></span>';
        
        $url_part = 'media/videos';
        
    }
    
    $breadcrumb .= ' <i class="icon arrow-right-6"></i> <span><a href="gallery/' . $current_gallery->getId() . '">' . $current_gallery->getName() . '</a></span>';
    
} elseif ( isset( $tag ) && $tag <> '' ) {
    
    
    $breadcrumb .= '<i class="icon arrow-right-6"></i> <span>'.$langscape["Tag"].'</span>';
    $breadcrumb .= '<i class="icon arrow-right-6"></i> <span><a href="tag/' . $tag . '">' . ucfirst( $tag ) . '</a></span>';
    
} elseif ( isset( $gallery_type ) && $gallery_type <> '' ) {
    
    $breadcrumb .= ' <i class="icon arrow-right-6"></i> <span><a href="media/'.$image_type_name.'">'.ucfirst($image_type_name).'</a></span>';
    
    
} elseif ( isset( $search_keywords ) && $search_keywords <> '' ) {
    
    $breadcrumb .= '<i class="icon arrow-right-6"></i> <span>'.$langscape["Search"].'</span>';
    $breadcrumb .= ' <i class="icon arrow-right-6"></i> <span><a href="' . $home_page . '?s=' . $search_keywords . '">' . ucfirst( $search_keywords ) . '</a></span>';
    
} elseif ( isset( $user_id ) && $user_id <> '' ) {
    
    $breadcrumb .= '<i class="icon arrow-right-6"></i> <span><a href="members">'.$langscape["Members"].'</a></span> <i class="icon arrow-right-6"></i> ';
    
    $user_module = MK_RecordModuleManager::getFromType( 'user' );
    
    
    try {
        
        $user_record = MK_RecordManager::getFromId( $user_module->getId(), $user_id );
        if ( $user_record == '' ) {
            throw new Exception( 'User Not Found' );
        }
    }
    catch ( Exception $e ) {
        header( 'Location: ' . $config->site->url . 'not-found.php', true, 301 );
        exit;
    }
    
    $breadcrumb .= '<a href="' . $config->site->url . '' . $user_record->getUsername() . '"><span class="user-name">' . $user_record->getMetaValue( 'display_name' ) . '</span></a> ';
    
    if ( !empty( $section ) ) {
        
        $plural = new Inflect;
        $breadcrumb .= '<i class="icon arrow-right-6"></i> <a href="' . getProfileUrl( $user_id, $section ) . '"><span>' . $langscape[ucfirst( $section )] . '</span></a>';
        
    }
    
    //$breadcrumb .= '<i class="icon arrow-right-6"></i> <a href="' . getProfileUrl( $user_id, $section ) . '"><span>' . ucfirst( $section ) . '</span></a>';
    
    
    
    
} elseif ( isset( $image_id ) && $image_id <> '' ) {
    
    $gallery_id   = $image->objectGallery()->getId();
    $gallery_name = $image->objectGallery()->getName();
    
    $breadcrumb .= '<i class="icon arrow-right-6"></i> <span><a href="' . $home_page . '">'.$langscape["Galleries"].'</a></span>';
    $breadcrumb .= ' <i class="icon arrow-right-6"></i> <span><a href="gallery/' . $gallery_id . '">' . ucfirst( $gallery_name ) . '</a></span>';
    
} elseif ( $this_filename == $members_page ) {
    
    $breadcrumb .= '<i class="icon arrow-right-6"></i> <span class="members"><a href="members">'.$langscape["Members"].'</a></span>';
    
    if ( !empty( $section ) ) {
        
        $plural = new Inflect;
        $breadcrumb .= '<i class="icon arrow-right-6"></i> <a href="members/' . $section . '"><span>' . $plural->pluralize( ucfirst( $section ) ) . '</span></a>';
        
    }
    
} else {

    $breadcrumb .= '<i class="icon arrow-right-6"></i> <a href="' . $config->site->url . '">'.$langscape["All"].'</a>';
    

}

if ( isset( $order_by ) && $order_by <> '' ) {
    
    if ( $gallery_id == '' && !isset( $search_keywords ) ) {
        $breadcrumb .= '<i class="icon arrow-right-6"></i> <span><a href="' . $config->site->url . '">'.$langscape["Galleries"].'</a></span>';
    }
    
    //Not featured
    /*
    if($order_by <> 'featured') {
    $order_by_text = 'Order by ';
    } else {
    $order_by_text = NULL;
    }*/
    $order_by_text = NULL;
    
    if ( !empty($url_part) ) { 
        
        $breadcrumb .= ' <i class="icon arrow-right-6"></i> <span><a href="' . $url_part . '/order-by/' . $order_by . '">' . $order_by_text . ucfirst( $order_by ) . '</a></span>';
    
    } else {
    
         $breadcrumb .= ' <i class="icon arrow-right-6"></i> <span><a href="order-by/' . $order_by . '">' . $order_by_text . ucfirst( $order_by ) . '</a></span>';
    
    }

}
?>