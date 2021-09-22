<?php

function clean_name($tmp) {
	$tmp = str_replace(" & ","_",$tmp);
	$tmp = str_replace("&","_",$tmp);
	$tmp = str_replace("+","_",$tmp);
	$tmp = str_replace(".","",$tmp);
	$tmp = str_replace("\'s","s",$tmp);
	$tmp = str_replace("\'","",$tmp);
	$tmp = str_replace("/","",$tmp);	
	$tmp = str_replace("'","",$tmp);
	$tmp = str_replace(")","",$tmp);
	$tmp = str_replace("(","",$tmp);
	$tmp = str_replace(" - ","-",$tmp);						   
	$tmp = str_replace("  ","",$tmp);	
	$tmp = str_replace(" ","",$tmp);
	$tmp = trim($tmp);
	return $tmp;
}

/****************************************/
function getImageTypeId($id = NULL) {
	if ($id=='image') {
		$n = 1;
	} elseif ($id=='video') {
		$n = 2;
	} elseif ($id=='audio') {
		$n = 3;
	} else {
		$n = false;
	}
	return $n;
}

/****************************************/
function getImageTypeName($id = NULL) {
	if ($id==1){
		$n = 'image';
	} elseif ($id==2) {
		$n = 'video';
	} elseif ($id==3) {
		$n = 'audio';
	} else{
		$n = '';
	}
	return $n;
}

/****************************************/
function getImageTypeNamePlural($id = NULL) {
	if ($id==1){
		$n = 'images';
	} elseif ($id==2) {
		$n = 'videos';
	} elseif ($id==3) {
		$n = 'audios';
	} else{
		$n = '';
	}
	return $n;
}

/****************************************/
function time_since($since) { //Should be passed in seconds
  $chunks = array(
      array(60 * 60 * 24 * 365 , 'yr'),
      array(60 * 60 * 24 * 30 , 'mth'),
      array(60 * 60 * 24 * 7, 'wk'),
      array(60 * 60 * 24 , 'day'),
      array(60 * 60 , 'hr'),
      array(60 , 'min'),
      array(1 , 'sec')
  );

  for ($i = 0, $j = count($chunks); $i < $j; $i++) {
      $seconds = $chunks[$i][0];
      $name = $chunks[$i][1];
      if (($count = floor($since / $seconds)) != 0) {
          break;
      }
  }

  $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
  return $print;
}

/****************************************/
function format_time($time_str) {
	global $langscape;
	
	$search_time  = array('moments', 'minute', 'minutes', 'hour', 'hours', 'day', 'days', 'week', 'weeks', 'month', 'months', 'year', 'years');
	$replace_time = array(''.$langscape["moments"].'', ''.$langscape["minute"].'', ''.$langscape["minutes"].'', ''.$langscape["hour"].'', ''.$langscape["hours"].'', ''.$langscape["day"].'', ''.$langscape["days"].'', ''.$langscape["week"].'', ''.$langscape["weeks"].'', ''.$langscape["month"].'', ''.$langscape["months"].'', ''.$langscape["year"].'', ''.$langscape["years"].'');
	$time = str_replace($search_time, $replace_time, $time_str);

	return $time;
}

/****************************************/
function text2urls($Text) {
	if (preg_match("/http/", $Text) OR preg_match("/www/", $Text))
	{
	$Text = str_replace(" www" , " http://www" , $Text);
	$Explode = explode(" ", $Text);
	foreach($Explode as $Check) {if (preg_match("/http/", "$Check") OR preg_match("/www/", "$Check")) {$Text = str_replace($Check , "$Check" , $Text);}}
	}
	return $Text;
}

/****************************************/
function makeClickableLinks($s) {
  return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
}

/****************************************/
function truncate($string, $chars) {
	$string = preg_replace( "/\r|\n/", " / ", $string );
	
	if (strlen($string) > $chars) {
		$string = substr($string,0,$chars) . '...';
	} else {
		$string = $string;
	}
	return $string;
}

//STATIC VERSION - WE USE AJAX INSTEAD - SEE Server.class.php
/****************************************/
function emailReportedImage() {
  global $image;
  global $config;
  global $image_page;
  global $langscape;
  
  $email = new MK_BrandedEmail();
  $email
  ->setSubject($langscape["Media Item Reported"])
  ->setMessage('<p>' . $langscape["The media item"] . ' <a href="'.MK_Utility::serverUrl( $image_page.'?image='.$image->getId() ).'">'.$image->getTitle().'</a> ' . $langscape["has been reported"] . '.</p>')
  ->send( $config->site->email );
  return 'Success';
}

/****************************************/
function returnFavouriteHeart() {

  global $user;
  global $image;
  global $image_favourite_module;
  global $total_favourites;
  global $image_favourites;
  global $favourite_id;
  global $favourite;
  
  $config = MK_Config::getInstance();  
  $enable_guest_likes = $config->site->enable_guest_likes;
  $icon_style = $config->site->style->icon_like;
  
  if( $user->isAuthorized() || $enable_guest_likes ) {

	if( $user->isAuthorized() )
	{
    //GET WHETHER THIS IS A FAVORITE IMAGE OR NOT
      $favourite = $image_favourite_module->searchRecords(array(
				array('field' => 'image', 'value' => $image->getId()),
				array('field' => 'user', 'value' => $user->getId())
			));
			$favourite = array_pop($favourite);
	}
      

    if (!empty($favourite)) { // USER HAS RIGHT TO KNOW IF IMAGE HAS BEEN LIKED BY HIM OR NOT

      $favourite_id = $favourite->getId(); 
      $fav_class    = 'remove-favourite';
      $has_fav      = 'favourite';
      $title_text   = 'Remove Favorite';
      
    } else {

        $favourite_id = ""; //Has not been favorited
        $fav_class  = 'add-favourite';
        $has_fav = '';
         $title_text   = 'Add Favorite';
    }


    /****************** LOGGED IN AND THIS IS NOT YOUR IMAGE PAGE *******************/
    if( ( $user->getId() != $image->getUser() ) || ( $enable_guest_likes ) ) { //OK TO FAVOURITE

      //$favourite = array_pop($favourite);

      $image_favourites = '<span title="'. $title_text .'" rel="image '. $fav_class .'" data-image-favourites-total="' . $total_favourites . '" class="pure-u meta-hearts stats red '.  $has_fav .' ' . $icon_style . '" data-user-id="' .( $user->isAuthorized() ? $user->getId() : 0 ) . '" data-image-id="' .$image->getId() . '" data-image-favourite-id="' . $favourite_id . '"><i class="'. $icon_style . ' icon"></i><span class="text">' .number_format($total_favourites) . '</span></span>';

      
    } else { //CANNOT FAV IMAGE
      
      $image_favourites = '<span data-image-favourites-total="' . $total_favourites . '" class="pure-u meta-hearts stats ' . $icon_style . '" data-user-id="' .( $user->isAuthorized() ? $user->getId() : 0 ) . '" data-image-id="' .$image->getId() . '"><i class="'. $icon_style . ' icon"></i><span class="text">' .number_format($total_favourites) . '</span></span>';
      
    } //END NOT YOUR PAGE 
      
  } else { //NOT LOGGED IN - CANNOT FAV IMAGE
	  
	  global $deviceType;
	  $modalClass = '';
	  if (!($deviceType=='phone') && !($deviceType=='tablet') && !$config->site->wordpress->strict_login){
		  $modalClass='en-trigger';
	  }
	
    $image_favourites = '<span data-image-favourites-total="' . $total_favourites . '" class="pure-u meta-hearts stats ' . $modalClass . ' ' . $icon_style . '" data-modal="modal-sign-in" data-user-id="' .( $user->isAuthorized() ? $user->getId() : 0 ) . '" data-image-id="' .$image->getId() . '"><i class="'. $icon_style . ' icon"></i><span class="text">' .number_format($total_favourites) . '</span></span>';
    
  } 

  return $image_favourites;

}

/****************************************/
function getUsernameReservedWords() {
	
	$reserved = array("?","video","audio","image","videos","audios","images","gallery","media","medias","order-by","sort-by","members","member","search","blog","privacy","privacy-policy","terms","page","post","about","contact"," ");	
	return $reserved;

}


/****************************************/
function generateUsername( $displayName ) {

	//Check to make sure username is not equal to a protected site slug
	$t_string = iconv('UTF-8', 'ASCII//TRANSLIT', utf8_encode($displayName));
	$t_string = str_replace("?","",$t_string);
	$t_string = str_replace(getUsernameReservedWords(), "", $t_string);

	if ($t_string == ''){
		//This line exists to protect the URL slugs. Probably only an issue if someone is gaming the site
		$displayName = $displayName . 'dude';
	
	}
	
	$userName = iconv('UTF-8', 'ASCII//TRANSLIT', utf8_encode($displayName));
	$userName = str_replace("?","",$userName);	
	$userName = str_replace(" ", "", $userName);
    $userName = strtolower( $userName ); 
    return $userName;

}

/****************************************/
/** 
  * Return embded url version of YouTube url.
  * @param string $url 
  * @return string 
  * Author: Daniel Hewes - ENGAGE INC
  */
function convertYouTubeUrl( $url, $div = NULL ) {

    $search       = '/youtube\.com\/watch\?v=([a-zA-Z0-9s_s-]+)/smi';
    $search_short = '/youtu\.be\/([a-zA-Z0-9s_s-]+)/';
    
    $match = preg_match( $search, $url, $matches );
    
    $div = (!empty($div))?$div:'embed';
    
    if ( $match ) {
    
        return 'https://youtube.com/'.$div.'/' . $matches[1];
        
    } else {
    
        $match = preg_match( $search_short, $url, $matches );
        
        if ( $match ) {
        
            return 'https://youtube.com/'.$div.'/' . $matches[1];
        
        }
       
    }
    
}


/****************************************/
/** 
  * Return embded url version of Vimeo url.
  * @param string $url 
  * @return string 
  * Author: Daniel Hewes - ENGAGE INC
  */
function convertVimeoUrl( $url ) { //Function to convert the vimeo watch URL to embed version
    

    $search     = '/vimeo\.com\/([0-9]{1,10})/';
    $replace    = "player.vimeo.com/video/$1";    
    $url        = preg_replace( $search, $replace, $url );
    
    $url = str_replace('http:', 'https:', $url);
    return $url;
}

/****************************************/
/** 
  * Return embded url version of vine url.
  * @param string $url 
  * @return string 
  * Author: Daniel Hewes - ENGAGE INC
  */
function convertVine($url ) {
      
    //$url = $url . '/card?audio=1';
    $url = $url . '/embed/simple?audio=1';
    
    return $url;
    
}

/****************************************/
/** 
  * Return True/False boolen. Check if url is YouTube.
  * @param string $url 
  * @return boolen
  * Author: Daniel Hewes - ENGAGE INC
  */
function isYouTube( $url ) {
     
    $rx = '#^http(?:s?)://(?:www\.)?youtu(?:be\.com/watch\?(?:.*?&(?:amp;)?)?v=|\.be/)([\w??\-]+)(?:&(?:amp;)?[\w\?=]*)?#i';

    $match = preg_match( $rx, $url, $matches );
    
    if ( $match == 1 ) {
    
        return true;
        
    } else {
    
        return false;
    
    }

}

/****************************************/
/** 
  * Return True/False boolen. Check if url is Vimeo.
  * @param string $url 
  * @return boolen
  * Author: Daniel Hewes - ENGAGE INC
  */
function isVimeo( $url ) {

    $rx = '/^http:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/';
    
    $match = preg_match ($rx, $url, $matches);
    
    if ( $match == 1 ) {
    
        return true;
        
    } else {
    
        return false;
    
    }
    
}


/****************************************/
/** 
  * Return True/False boolen. Check if url is Vine.
  * @param string $url 
  * @return boolen
  * Author: Daniel Hewes - ENGAGE INC
  */
function isVine( $url ) {

    $rx = '/^https:\/\/?vine\.co\/v\/?\b.*$/';
    
    $match = preg_match ($rx, $url, $matches );
    
    if ( $match == 1 ) {
    
        return true;
        
    } else {
    
        return false;
    
    }
    
}

/****************************************/
/** 
  * Return Twitter username string
  * @param string $string 
  * @return string 
  * Author: Daniel Hewes - ENGAGE INC
  */
function convertTwitter( $url = NULL ) {

    $url = explode( "twitter.com/", $url );
    
    if( !empty( $url[1] ) ) {
    
        return '@' . $url[1];
    
    } else {
    
        return '';
    
    }
    
}

/****************************************/
/** 
  * Return Twitter url string
  * @param string $string 
  * @return string 
  * Author: Daniel Hewes - ENGAGE INC
  */
function convertTwitterUsernameUrl( $username = NULL ) {

    $username = explode( "@", $username );
    
    if( !empty( $username[1] ) ) {
    
        return 'https://twitter.com/' . $username[1];
    
    } else {
    
        return '';
    
    }
    
}

/****************************************/
/** 
  * Return URL-Friendly string slug
  * @param string $string 
  * @return string 
  * Author: Daniel Hewes - ENGAGE INC
  */
function seoUrl( $string ) {
    
    $string = strtolower($string); //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
   
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string); //Strip any unwanted characters
    
    $string = preg_replace("/[\s-]+/", " ", $string); //Clean multiple dashes or whitespaces
    
    $string = preg_replace("/[\s_]/", "-", $string); //Convert whitespaces and underscore to dash
    
    return $string;
    
}

/****************************************/
/** 
  * Return user profile url string
  * @param integer $userId 
  * @return string 
  * Author: Daniel Hewes - ENGAGE INC
  */
function getProfileUrl( $userId, $section = NULL ) {

    $user_module = MK_RecordModuleManager::getFromType( 'user' );
    $user        = MK_RecordManager::getFromId( $user_module->getId(), $userId );
    
    if ( $user->getUsername() ) {
    
        if ( !empty( $section ) ) { 
        
            $section = '/' . $section;
        
        }
    
       return $user->getUsername() . $section;
    
    } else {
        
        if ( !empty( $section ) ) {
        
            $section = '&amp;section=' . $section;
        
        }
        
        return 'member.php?user=' . $user->getId() . $section;
    
    }

}

/****************************************/


function getGalleryName($gallery_id) {
    $gallery_module       = MK_RecordModuleManager::getFromType('image_gallery'); //Gallery Info
    
    $gallery = $image_module->searchRecords(array(  
                                            array('field' => 'id', 'value' => $gallery_id))
                                            , NULL);
    return $gallery->getName();                                        
}


/****************************************/
	
function getTotalUnapprovedCount($type_gallery = NULL) {
  
  		$image_table = MK_Database::getTableName( 'images' );

        if(!empty($type_gallery)) {
            $pre_record = MK_Database::getInstance()->prepare("SELECT COUNT(*) AS total_images FROM `$image_table` WHERE `approved` = 0 AND `type_gallery` = $type_gallery");
        } else {
            $pre_record = MK_Database::getInstance()->prepare("SELECT COUNT(*) AS total_images FROM `$image_table` WHERE `approved` = 0 ");
        }

		$pre_record->execute();

		$res_record = $pre_record->fetch( PDO::FETCH_ASSOC );
		
		return $res_record['total_images'];

}

/****************************************/
function buildSearchOptions($gallery_id = NULL, $image_module_id, $tag = NULL, $order_by = NULL, $image_type = NULL, $search_keywords = NULL) {
    
    $image_module         = MK_RecordModuleManager::getFromType('image'); //Image details
    $gallery_module       = MK_RecordModuleManager::getFromType('image_gallery'); //Gallery Info
    $image_comment_module = MK_RecordModuleManager::getFromType('image_comment'); //Comments Info
    $field_module         = MK_RecordModuleManager::getFromType('module_field');
    $user_module          = MK_RecordModuleManager::getFromType('user');

	$config = MK_Config::getInstance();

    
    $options         = array();
    $search_criteria = array();
    
    //Setup the order by options.
    if ( !empty( $order_by ) ) {
    
        switch ( $order_by ) {
        
            case 'favorites' :
            
                $order_by_field = $field_module->searchRecords(array(
                    array('field' => 'module', 'value' => $image_module->getId()),
                    array('field' => 'name', 'value' => 'total_favourites')
                ));
                
                $order_by_field = array_pop( $order_by_field );

                $options = array(
                    'order_by' => array(
                        'field' => $order_by_field->getId(),
                        'direction' => 'DESC'
                    )
                );
            
            break;
            
            case 'comments' :
                
                $order_by_field = $field_module->searchRecords(array(
                    array('field' => 'module', 'value' => $image_module->getId()),
                    array('field' => 'name', 'value' => 'total_comments')
                ));
                
                $order_by_field = array_pop( $order_by_field );
                
                $options = array(
                    'order_by' => array(
                        'field' => $order_by_field->getId(),
                        'direction' => 'DESC'
                    )
                );
                
                break;
                
            case 'popular' :
            
                $order_by_field = $field_module->searchRecords(array(
                    array('field' => 'module', 'value' => $image_module->getId()),
                    array('field' => 'name', 'value' => 'views')
                ));
                
                $order_by_field = array_pop( $order_by_field );

                $options = array(
                    'order_by' => array(
                        'field' => $order_by_field->getId(),
                        'direction' => 'DESC'
                    )
                );
                
                break;
                
            case 'featured' : 
                
                $search_criteria[] = array(
                    'field' => 'featured', 'value' => 1
                );
      
                $order_by_field = $field_module->searchRecords(array(
                    array('field' => 'module', 'value' => $image_module->getId()),
                    array('field' => 'name', 'value' => 'featured_date')
                ));
                
                $order_by_field = array_pop( $order_by_field );
      
                $options = array(
                    'order_by' => array(
                        'field' => $order_by_field->getId(),
                        'direction' => 'DESC'
                    )
                );
                
                break;

            case 'queue' : 
                
                $user = MK_Authorizer::authorize();
                //Only show to ADMIN
                if ( $user->isAuthorized() && $user->objectGroup()->isAdmin() ) {
	                
	                $search_criteria[] = array(
	                    'field' => 'approved', 'value' => 0
	                );
	      
	                $order_by_field = $field_module->searchRecords(array(
	                    array('field' => 'module', 'value' => $image_module->getId()),
						array('field' => 'name', 'value' => 'date_added')
	                ));
	                
	                $order_by_field = array_pop( $order_by_field );
	      
	                $options = array(
	                    'order_by' => array(
	                        'field' => $order_by_field->getId(),
	                        'direction' => 'DESC'
	                    )
	                );
                }
                
                break;

           
           //MODIFY IF YOU WANT TO ADD A MEMBER CATEGORY SEARCH     
           case 'some_member_cat' : 
                
                	global $users_types_array_combined;
                	global $order_by;
                	
                	$user_category_list  = $users_types_array_combined;

				    $key = array_search(ucfirst($order_by), $user_category_list); //Convert section to category id.
				    
				    $user_search_criteria[] = array(
				        'literal' => "(`category` = " . $key . ")"
				    );
				    
				    $active = '';
				    
				    $member_type = ucfirst($order_by);
				    
			        //User Search
			        $user_search = $user_module->searchRecords(array(
			            array('field' => 'category', 'value' => $key)
			        ));
			    
			        foreach( $user_search as $user_search_single ) {
			        	
			            $matching_member_types[] = $user_search_single->getId();
			        
			        }
                
                break;
                
        }
    
    }

    //Add HIDE Search Criteria
    $search_criteria[] = array(
        'field' => 'hide', 'value' => 0
    );
    
     //Create search criteria on gallery id
    if ( !empty( $image_type ) ) {
    	
        $search_criteria[] =
            array('literal' => ' `type_gallery` IN (' . $image_type . ') ');

	}
    
    //Create search criteria on gallery id
    if ( !empty( $gallery_id ) ) {
    
        $search_criteria[] = 
            array('literal' => ' `gallery` IN (' . $gallery_id . ') ');
    
    }
    
    //Create search criteria on user type
    if ( !empty( $member_type ) ) {
    
        $search_criteria[] =
            array('literal' => ' `user` IN(' .implode(',', $matching_member_types). ')' );
    
    }    
 
    //Create search criteria on tag
    if( !empty( $tag ) ) {
        
        $tag_wildcard = '%,'.$tag.',%';
        
        $search_criteria[] = array('literal' => "CONCAT(',', `tags`, ',') LIKE ".MK_Database::getInstance()->quote($tag_wildcard));
    
    }

   
    //Search Criteria for Approved Field
    if ( ( $config->site->media->enable_approval ) && ( ( !empty( $order_by ) && ($order_by <> 'queue' ) ) || empty( $order_by ) ) ) {    	
	    $search_criteria[] = array(
	    	'field' => 'approved', 'value' => '1'
	    );
	}

    
    if( !empty( $search_keywords ) ) {

        $keywords_wildcard = '%' . $search_keywords . '%';
        $keywords_wildcard = MK_Database::getInstance()->quote( $keywords_wildcard );
    
        $matching_galleries = array();
        $matching_users     = array();

        //Gallery Search
        $gallery_search = $gallery_module->searchRecords(array(
            array('literal' => "(`name` LIKE " . $keywords_wildcard . ")")
        ));
    
        foreach( $gallery_search as $gallery_search_single ) {
      
            $matching_galleries[] = $gallery_search_single->getId();
        
        }

        //User Search
        $user_search = $user_module->searchRecords(array(
            array('literal' => "(`display_name` LIKE " . $keywords_wildcard . ")")
        ));
    
        foreach( $user_search as $user_search_single ) {
        
            $matching_users[] = $user_search_single->getId();
        
        }

        //Keywords
        $search_criteria[] = array(
            'literal' => "(`title` LIKE " . $keywords_wildcard . " OR `description` LIKE " . $keywords_wildcard . " OR `tags` LIKE ".$keywords_wildcard ." ".( !empty($matching_galleries) ? " OR `gallery` IN(".implode(',', $matching_galleries).") " : "" ) . " " . ( !empty($matching_users) ? " OR `user` IN(".implode(',', $matching_users).") " : "" ) ." )"
        );
        
    }
    
    return array(
        'options'         => $options,
        'search_criteria' => $search_criteria
    );

}


/****************************************/
function autoCompileLess($inputFile, $outputFile, $vars=NULL) {
  // load the cache
  $cacheFile = $inputFile.".cache";

  if (file_exists($cacheFile)) {
    $cache = unserialize(file_get_contents($cacheFile));
  } else {
    $cache = $inputFile;
  }

  $less_cache = new lessc;
  $less_cache->setFormatter("compressed");
  
  if (!empty($vars)){
	  $less_cache->setVariables($vars);
  }
  
  try {
    $newCache = $less_cache->cachedCompile($cache);
	} catch (Exception $ex) {
	    echo "lessphp fatal error: ".$ex->getMessage();
	}
  
  if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
    file_put_contents($cacheFile, serialize($newCache));
    file_put_contents($outputFile, $newCache['compiled']);
  }
}


/****************************************/
function getShortUrl($url,$login,$appkey,$enabled) {
	if ($enabled==1) {
		$url = get_bitly_short_url($url,$login,$appkey);
		return $url;
	} else {
		return $url;
	}
	
}

/****************************************/
//BITLY SHORT URL FUNCTIONS

/* returns the shortened url */
function get_bitly_short_url($url,$login,$appkey,$format='txt') {
	$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
	return curl_get_result($connectURL);
}

/* returns expanded url */
function get_bitly_long_url($url,$login,$appkey,$format='txt') {
	$connectURL = 'http://api.bit.ly/v3/expand?login='.$login.'&apiKey='.$appkey.'&shortUrl='.urlencode($url).'&format='.$format;

	return curl_get_result($connectURL);
}

/* returns a result form url */
function curl_get_result($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}


/****************************************/
/** 
  * Returns an array of the last x members information
  * @param integer $count
  * @return array
  * Author: Daniel Hewes - ENGAGE INC
  */
function getLastMembers($count = NULL) {

    $members_module = MK_RecordModuleManager::getFromType('user');
	$field_module   = MK_RecordModuleManager::getFromType('module_field');
	
	//GET FIELD ID TO ORDER BY
    $order_by_field = $field_module->searchRecords(array(
        array('field' => 'module', 'value' => $members_module->getId()),
        array('field' => 'name', 'value' => 'date_registered')
    ));
    
    $order_by_field = array_pop( $order_by_field );
            
    //BUILD OPTIONS   
    $members_search_criteria = array(); //Setup the search criteria array
    $options                 = array(
                                'order_by' => array(
                                    'field' => $order_by_field->getId(),
                                    'direction' => 'DESC'
                                    )
                                );
	//FIND LAST X MEMBERS							
    $members = $members_module->searchRecords($members_search_criteria, NULL, $options); 

    $members = array_slice($members, 0, $count); //Trim down the array to the desired length.
    
    return $members;

}

/****************************************/
/** 
  * Returns an array of the last x members images/videos
  * @param integer $count
  * @return array
  * Author: Daniel Hewes - ENGAGE INC
  */
function getItems($count = NULL, $type, $userId) {

    $image_module  = MK_RecordModuleManager::getFromType('image'); //Image details
    
    $images = $image_module->searchRecords(array(  
                                            array('field' => 'user', 'value' => $userId),
                                            array('field' => 'type_gallery', 'value' => $type))
                                            , NULL);

    $images = array_slice($images, 0, $count); //Trim down the array to the desired length.
    
    return $images;

}

/****************************************/
/** 
  * Returns true is the file is an animated gif
  * @param string $file
  * @return boolen (only if true)
  * Author: Daniel Hewes - ENGAGE INC
  */
function isAnimatedGif($file) { //Returns true if file is an animated gif
    
    $filecontents = file_get_contents($file);
 
    $str_loc = 0;
    $count = 0;
 
    // There is no point in continuing after we find a 2nd frame
    while ( $count < 2 ) {
    
        $where1 = strpos($filecontents,"\x00\x21\xF9\x04", $str_loc);
        
        if ( $where1 === FALSE ) {
            break;
        }
 
        $str_loc = $where1 + 1;
        $where2  = strpos( $filecontents, "\x00\x2C", $str_loc);
        
        if ($where2 === FALSE) {
            break;
        } else {
        
            if ( $where1 + 8 == $where2) {
                $count++;
            }
        
            $str_loc = $where2 + 1;
        }
    }
 
    // gif is animated when it has two or more frames
    return ($count >= 2); 
}


function postToFacebook ($image_id) {
	//http://www.sanwebe.com/2012/02/post-to-facebook-page-wall-using-php-graph
	//http://www.pontikis.net/blog/auto_post_on_facebook_with_php
}


/*************************/

function floor_dec($number,$precision,$separator)
{
    $numberpart=explode($separator,$number);
    
    if (count($numberpart)>1) {
	
	    $numberpart[1]=substr_replace($numberpart[1],$separator,$precision,0);
	
	    if($numberpart[0]>=0)
	    {
		    $numberpart[1]=floor($numberpart[1]);
		}
	    else
	    {
		    $numberpart[1]=ceil($numberpart[1]);
		}
	
	    $ceil_number= array($numberpart[0],$numberpart[1]);
	    
	    return implode($separator,$ceil_number);
    
    } else {
	    return $number;
    }
}

/***********************/

function get_extension($file) {
	$arr = explode(".", $file);
	$extension = end($arr);
	return $extension ? $extension : false;
}

/***********************/

function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
?>
