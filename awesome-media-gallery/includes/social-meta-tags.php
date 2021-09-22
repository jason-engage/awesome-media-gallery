<?php
/* get the short url */
//$short_url = get_bitly_short_url('http://davidwalsh.name/','davidwalshblog','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

/* get the long url from the short one */
//$long_url = get_bitly_long_url($short_url,'davidwalshblog','xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');


$social_banner_wide_url   = $config->site->social->image_wide;
$social_banner_square_url = $config->site->social->image_square;

$site_twitter_handle = ( ( !empty($config->site->social->twitter) ) ? $config->site->social->twitter : '' );

$meta_sitename_clean    = strip_tags( $config->site->name ); //implode(' / ', $head_title)
$meta_title_clean       = strip_tags( $config->site->title );
$meta_description_clean = strip_tags( $config->site->desc );

$txt_meta_member_default = "A talented member of " . $meta_sitename_clean;

switch ( $this_filename ) {
    case $home_page:
        $meta_title             = ucfirst( $order_by ) . ' ' . ( ( ( isset( $gallery_id ) ) && $gallery_id <> '' ) ? $current_gallery->getName() . ' - ' . $meta_title_clean : $meta_title_clean );
        $meta_description       = $meta_description_clean;
        $meta_description_full  = $meta_description_clean;
        $meta_twitter_creator = ( ( !empty($config->site->social->twitter) ) ? $config->site->social->twitter : '' ); //variables.php

        
        $meta_twitter_card_type = "gallery";
        $meta_image_src         = $config->site->url . $social_banner_wide_url; //variables.php
        $twitter_meta           = '';
        $cnt                    = 0;
        
        //GET 4 IMAGES FOR TWITTER GALLERY TYPE
        $paginator = new MK_Paginator();
        $paginator->setPerPage( 4 )->setPage( MK_Request::getQuery( 'page', 1 ) );
        
        $image_type = $gallery_module->getTypeGallery();
        
        $search_options = buildSearchOptions( $gallery_id, $gallery_module->getId(), $tag, $order_by, $gallery_type, $search_keywords );
        
        $twitter_images = $image_module->searchRecords( $search_options[ 'search_criteria' ], $paginator, $search_options[ 'options' ] );
        
        foreach ( $twitter_images as $image ) {
            
            $twitter_meta .= '<meta name="twitter:image' . $cnt . '" content="' . $config->site->url . ( ( $image_type == 1 ) ? rtrim( $config->site->url, "/" ) : '' ) . $image->getImage() . '">';
            $cnt++;
        }
        
        $facebook_meta = '<meta property="og:type" content="website"/>';
        
        break;
    
    case $image_page:
        $content = $image->getDescription();
        $content = truncate( $content, 160 );
        $content = str_replace( array(
             '\r\n',
            '\r',
            '\n',
            '"' 
        ), '', $content );
        
        $meta_title            = trim( $image->getTitle() ) . ' - ' . $meta_title_clean;
        $meta_description      = $content;
        $meta_description_full = $image->getDescription();
        $meta_twitter_creator  = ( convertTwitter( $author->getTwitterUrl() ) <> '' ) ? convertTwitter( $author->getTwitterUrl() ) : $config->site->social->twitter;
        $image_type            = $image->getTypeGallery();
        
        if ( $image_type == 2 ) { //video
            
            if ( isYouTube( $image->getVideoUrl() ) ) {
                $embed_converted = convertYouTubeUrl( $image->getVideoUrl(), 'v');
            } elseif ( isVimeo( $image->getVideoUrl() ) ) {
                $embed_converted = convertVimeoUrl( $image->getVideoUrl() );
            } else {
                $embed_converted = $image->getVideoUrl();
            }
            
            $meta_twitter_card_type = "player";
            $meta_image_src         = $config->site->url . $image->getImage(); //16:9 IMAGE
            $twitter_meta           = '
<meta property="twitter:player" content="' . $embed_converted . '">
<meta property="twitter:player:width" content="435">
<meta property="twitter:player:height" content="251">';
            //$facebook_meta='';          = '<meta property="og:type" content="video"/><meta property="og:video:url" content="' .$embed_converted . '?autoplay=1">';            
            $facebook_meta          = '<meta property="og:type" content="article"/>';

        } elseif ( $image_type == 1 ) { //image
            
            $meta_twitter_card_type = "photo";
            $meta_image_src         = $config->site->url . $image->getImage(); //16:9 IMAGE
            $twitter_meta           = '';
            $facebook_meta          = '<meta property="og:type" content="article"/>';
            
        } elseif ( $image_type == 3 ) { //audio
            
            $embed_converted = $image->getAudioUrl();
            
            $meta_twitter_card_type = "player";
            $meta_image_src         = $config->site->url . $image->getImage(); //16:9 IMAGE
            $twitter_meta           = '
<meta property="twitter:player" content="' . $embed_converted . '">
<meta property="twitter:player:width" content="435">
<meta property="twitter:player:height" content="251">';
            //$facebook_meta          = '<meta property="og:type" content="video"/><meta property="og:video" content="' .$embed_converted . '">';
            $facebook_meta          = '<meta property="og:type" content="article"/>';

        }
        
        break;
    
    case $member_page:
        
        $member_profile      = $user_record; //Already generated in BREADCRUMBS
        $meta_member_default = $txt_meta_member_default;
        $content             = ( ( $member_profile->getAbout() <> '' ) ? $member_profile->getAbout() : $meta_member_default );
        $content             = truncate( $content, 160 );
        
        $meta_title            = ucfirst( $member_profile->getDisplayName() ) . "'s " . ucfirst( $section ) . ' ' . $langscape["Profile"] . ' - ' . $meta_title_clean;
        $meta_description      = $content;
        $meta_description_full = ( ( $member_profile->getAbout() <> '' ) ? $member_profile->getAbout() : $meta_member_default );
        
        $twitter_meta = '';
        
        if ( ( $section == 'images' ) || ( $section == 'videos' ) || ( $section == 'audios' ) ) {
            
            $meta_twitter_card_type = "gallery";
            $cnt                    = 0;
            
            if ( $section == 'images' ) {
                $type = 1;
            } elseif ( $section == 'videos' ) {
                $type = 2;
            } elseif ( $section == 'audios' ) {
                $type = 3;
            }
            
            
            $images = getItems( 4, $type, $member_profile->getId() );
            
            if ( $images ) {
                
                foreach ( $images as $image ) {
                    
                    $twitter_meta .= '<meta name="twitter:image' . $cnt . '" content="' . $config->site->url . $image->getImage() . '">';
                    
                    $cnt++;
                }
                
            }
            
        } else {
            
            //DISPLAY SUMMARY TYPE CARD TYPE
            $twitter_meta = '';
            
            $meta_twitter_card_type = "summary";
            
        }
        
        $meta_image_src = $config->site->url . ( $member_profile->getAvatar() ? $member_profile->getAvatar() : $config->site->default_avatar ); //Must be SQUARE
        
        $meta_twitter_creator = ( convertTwitter( $member_profile->getTwitterUrl() ) <> '' ) ? convertTwitter( $member_profile->getTwitterUrl() ) : $config->site->social->twitter;
        $facebook_meta        = '
<meta property="og:type" content="profile"/>
		    ';
        
        
        
        break;
    
    
    case $members_page:
        
        $meta_title             = $langscape["List of Members"] . ' - ' . $meta_title_clean;
        $meta_description       = $meta_description_clean;
        $meta_description_full  = $meta_description_clean;
        $meta_twitter_card_type = "summary_large_image";
        $meta_image_src         = $config->site->url . $social_banner_wide_url;
        $meta_twitter_creator = ( ( !empty($config->site->social->twitter) ) ? $config->site->social->twitter : '' );
        $facebook_meta          = '<meta property="og:type" content="blog"/>';
        $twitter_meta           = '';
        $cnt                    = 0;
        $last_members           = getLastMembers( 4 ); //Request array of the last 4 members.
        
        if ( $last_members ) {
            
            foreach ( $last_members as $member ) {
                
                $twitter_meta .= '<meta name="twitter:image' . $cnt . '" content="' . $config->site->url . 'library/thumb.php?f=' . ( $member->getAvatar() ? $member->getAvatar() : $config->site->default_avatar ) . '&amp;h=250&w=250&amp;m=crop">';
                
                $cnt++;
            }
            
        }
        
        break;
    
    
    default:
        $meta_title             = $meta_title_clean;
        $meta_description       = $meta_description_clean;
        $meta_description_full  = $meta_description_clean;
        $meta_twitter_card_type = "summary_large_image";
        $meta_image_src         = $config->site->url . $social_banner_wide_url;
        $meta_twitter_creator = ( ( !empty($config->site->social->twitter) ) ? $config->site->social->twitter : '' );
        $facebook_meta          = '<meta property="og:type" content="blog"/>';
        $twitter_meta           = '';
        break;
        
} ?>