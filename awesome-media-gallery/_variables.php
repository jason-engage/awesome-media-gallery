<?php

//IMPORTANT QUERY PARAMETERS

//Gets
$section       = (MK_Request::getQuery('section')<>'')    ? htmlspecialchars(MK_Utility::sanitize(MK_Request::getQuery('section'), true, true )) : '';
$order_by      = (MK_Request::getQuery('order-by')<>'')   ? MK_Utility::sanitize(MK_Request::getQuery('order-by'), true, true ) : '';
$gallery_type  = (MK_Request::getQuery('media')<>'')      ? MK_Utility::sanitize(MK_Request::getQuery('media'), true, true ) : '';
$image_id      = (MK_Request::getQuery('image')<>'')      ? MK_Utility::sanitize(MK_Request::getQuery('image'), true, true ) : '';
$gallery_id    = (MK_Request::getQuery('gallery')<>'')    ? MK_Utility::sanitize(MK_Request::getQuery('gallery'), true, true ) : '';
$gallery_name    = (MK_Request::getQuery('gallery-name')<>'')    ? MK_Utility::sanitize(MK_Request::getQuery('gallery-name'), true, false ) : '';
$action        = (MK_Request::getQuery('action')<>'')     ? MK_Request::getQuery('action') : '';
$comment_id    = (MK_Request::getQuery('comment')<>'')    ? MK_Utility::sanitize(MK_Request::getQuery('comment'), true, true ) : '';
$user_id       = (MK_Request::getQuery('user')<>'')       ? MK_Utility::sanitize(MK_Request::getQuery('user'), true, true ) : '';
$user_name     = (MK_Request::getQuery('username')<>'')   ? MK_Utility::sanitize(MK_Request::getQuery('username'), true, true ) : '';
$platform      = (MK_Request::getParam('platform')<>'')   ? MK_Utility::sanitize(MK_Request::getParam('platform'), true, true ) : ''; 
$tag           = (MK_Request::getQuery('tag')<>'')        ? MK_Utility::sanitize(MK_Request::getQuery('tag'), true, false ) : '';
$slug          = (MK_Request::getQuery('slug')<>'')       ? MK_Utility::sanitize(MK_Request::getQuery('slug'), true, false ) : '';

//Set current page
$this_filename = explode('/', $_SERVER['SCRIPT_NAME']);
$this_filename = array_pop($this_filename);

//Set Page Names
$home_page    = 'index.php'; 
$image_page   = 'image.php';
$member_page  = 'member.php';
$members_page = 'members.php';
$upload_details_page = 'upload-details.php';
$signin_page = 'sign-in.php';
$signup_page = 'sign-up.php';
$forgotten_password_page = 'forgotten-password.php';
$rss_page = 'rss.php';

//ENGAGE FUNCTIONS. DH 21/08
include ('includes/functions.php');

//SOCIAL LOGIN FUNCTIONS
include ('includes/functions-social-login.php');

// If user session has expired but cookie is still active
if( $cookie->login && empty($session->login) )
{
	$session->login = $cookie->login;
}

//GET USER INSTANCE
$user_module = MK_RecordModuleManager::getFromType('user');

// Get current user
if( !empty($session->login) )
{
    MK_Authorizer::authorizeById( $session->login );
}

$user = MK_Authorizer::authorize();


//WP FORCE LOGIN
if ((empty($_SESSION["OAUTH_ACCESS_TOKEN"])) && $config->site->wordpress->force_login && ($this_filename <> $rss_page)) { //User is not logged in using a social network
	header("Location: ".MK_Utility::serverUrl("sign-in.php?platform=wordpress"));
}

//CHECK FOR WP LOGIN COOKIE, IF LOGGED IN, REDIRECT AND AUTO SIGN-IN
if ( ($config->site->wordpress->strict_login) && empty($_SESSION["OAUTH_ACCESS_TOKEN"]) ) {
	foreach($_COOKIE as $key=>$value){
		if("wordpress_logged_in_" == substr($key,0,20)){
			header("Location: ".MK_Utility::serverUrl("sign-in.php?platform=wordpress"));
		}
	}
}

//Check if Unapproved logins are allowed, if forced login is enabled, and redirect if needed
if ( ( (!$user->isAuthorized() && $config->site->members->enable_approval && !$config->site->members->enable_unapproval_login) || ($user->isAuthorized() && !$user->isApproved() && $config->site->members->enable_approval && !$config->site->members->enable_unapproval_login ) || ($config->site->style->enable_forced_login && !$user->isAuthorized() ) ) && ( ($this_filename <> $signin_page) && ($this_filename <> $signup_page) && ($this_filename <> $forgotten_password_page) && ($this_filename <> $rss_page) ) )  {
	header("Location: ".MK_Utility::serverUrl($signup_page));
}

//Set META TITLE
$head_title = array($config->site->name);

//MOBILE DETECT
require('library/Mobile_Detect.php');
global $deviceType;
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$scriptVersion = $detect->getScriptVersion();

//HELPER CLASSES
//Pluralize
include ('library/pluralize.php');

//Less Complier
include ('library/lessc.inc.php');

//Languages
if ( isset($config->site->languages->language) ) {
	require("lang/" . $config->site->languages->language);
} else {
	require("lang/english.php");	
}


//IF GALLERY NAME AND TYPE EXIST FIND GALLERY_ID
if ( !empty($gallery_name) && !empty($gallery_type) ) {
	
	$gallery_type_name = $gallery_type;
	$gallery_type = getImageTypeId($gallery_type);
	
	$gallery_search = $gallery_module->searchRecords(array(
            array('field' => 'name', 'value' => $gallery_name),
            array('field' => 'type_gallery', 'value' => $gallery_type)
        ));
    
    foreach( $gallery_search as $gallery ) {
    
        $gallery_id = $gallery->getId();
    
    }	
}

$gallery_name = urldecode($gallery_name);


//Posts
$search_keywords = (MK_Request::getQuery('s')<>'')      ? MK_Utility::sanitize(MK_Request::getQuery('s'), true, false ) : '';

//SET GALLERY TYPE NAME

$image_type_name = getImageTypeNamePlural($gallery_type);


//MODALS
if ( ( $deviceType == 'computer' ) ||  ( ( $deviceType == 'tablet' ) && !( $config->site->mobile->disable_modals ) ) ) { 

	$enable_modals = 1;

} else {

	$enable_modals = 0;

}

if ( ( ($deviceType == 'phone') && (!$config->site->mobile->enable_responsive_phone) ) || ( ($deviceType == 'tablet') && !$config->site->mobile->enable_responsive_tablet ) || ($deviceType == 'computer') ) {
	$disable_responsive = 1;
} else {
	$disable_responsive = 0;
}

if ( ( ($deviceType == 'phone') && ($config->site->mobile->enable_responsive_phone) ) || ( ($deviceType == 'tablet') && $config->site->mobile->enable_responsive_tablet ) ) {
	$enable_responsive = 1;
} else {
	$enable_responsive = 0;
}


//HEIGHTS AND WIDTHS
$mwsi = intval($config->site->values->width_single_image); // max_width_single_image;
$mhsi = intval($config->site->values->height_single_image); // max_height_single_image;

$wcsi = intval($config->site->values->width_carousel_image); // width_carrousel_image_single_image_page;
$hcsi = intval($config->site->values->height_carousel_image); // height_carrousel_image_single_image_page;

$wca = intval($config->site->values->width_comments_avatar_image); // width_comment_avatar;
$hca = intval($config->site->values->height_comments_avatar_image); // height_comment_avatar;

$wcp = intval($config->site->values->width_member_banner); // width member cover photo
$hcp = intval($config->site->values->height_member_banner); // height member cover photo

$wib = intval($config->site->values->width_image_box); // width of box - be careful;
$hib = intval($config->site->values->height_image_box); // height of box - be careful;

$wsi = intval($config->site->slider->width); // width slider image
$hsi = intval($config->site->slider->height); // height slider image

$wci = intval($config->site->values->width_main_carousel_image); // width_carrousel_image;
$hci = intval($config->site->values->height_main_carousel_image); // height_carrousel_image;

$site_width_for_calc = intval($config->site->values->site_width_calc);

$him_offset = 0;

// GRID IMAGES
if ($config->site->grid->thumbnail_style == 'CUSTOM')  { //CUSTOM
	
	// THE IMAGE IS BIGGER THAN THE BOX for RETINA & RESPONSIVE
	$wim = $wib * 1.5; // width_image in box;
	$him = $hib * 1.5; // height_image in box;
	
	
} else { //CALCULATE
	
	
	switch ($config->site->grid->thumbnail_style) {
		case 'WIDE':
			$div = 16/9;
			break;
		case 'STANDARD':
			$div = 4/3;
			break;
		case 'SQUARE':
			$div = 1;
			break;			
	}
	
	$wim = $wib * 1.5; // width_image in box;
	$him = round($wim/$div);
	
	$wib = ($site_width_for_calc/$config->site->grid->column_count); // default width_image_box;
	$hib = round($wib/$div);
	
	
}

//GRID IMAGES FOR RESPONSIVE TABLET - SET TO 3 COLUMNS

if ( ($deviceType == 'tablet') && ($config->site->mobile->enable_responsive_tablet) ) {

}


//CAROUSEL IMAGES
if ($config->site->carousel->thumbnail_style == 'CUSTOM')  { //CUSTOM

	// THE IMAGE IS BIGGER THAN THE BOX for RETINA & RESPONSIVE
	$wci = round($wci * 1.5); // width_image in carousel;
	$hci = round($hci * 1.5); // height_image in carousel;
	
	
} else { //CALCULATE
	
	switch ($config->site->carousel->thumbnail_style) {
		case 'WIDE':
			$div = 16/9;
			break;
		case 'STANDARD':
			$div = 4/3;
			break;
		case 'SQUARE':
			$div = 1;
			break;			
	}
	
	$wci = round($site_width_for_calc/$config->site->carousel->column_count); // default width_image_box;
	$hci = round($wci/$div);
	
}


//WP AUTOPOST VARIABLES
if ( $user->isAuthorized() ) {
	$wp_author_id = $user->getWpAuthorId();
	
	//check if user has set a wp_admin_id variable, otherwise set it to the global one
	if (empty($wp_author_id)) {
		$wp_author_id = $config->site->wordpress->admin_id;
	}
	
	$wp_taxonomy_categories = $config->site->wordpress->taxonomy_categories;
	$wp_taxonomy_tags = $config->site->wordpress->taxonomy_tags;

}


//GET USER TYPES FOR LOOKUP ( USEFUL TO GET NAME FROM ID )
$num = 0;
$users_types_list = MK_RecordModuleManager::getFromType('user_type'); //User Category

foreach($users_types_list->getRecords() as $users_types) {
	$users_types_id_array[$num] = $users_types->getId();
	$users_types_name_array[$num] = $users_types->getTitle();;
	$num++;
}

$users_types_id_array_encoded = json_encode($users_types_id_array);
$users_types_name_array_encoded = json_encode($users_types_name_array);
$users_types_array_combined = array_combine($users_types_id_array,$users_types_name_array);

$num = 0;
$users_types_data = "{ ";

$total = sizeof($users_types_id_array);

foreach($users_types_id_array as $value) {
	$users_types_data .= "'" . $value . "' : '" . $users_types_name_array[$num] . "'";
	if ($total<>$num) { $users_types_data .= ",";}
	$num++;	
}

$users_types_data .= " }";


//PLACEHOLDERS FOR EDITABLE FIELDS
$txt_placeholder_arr                    = array();
$txt_placeholder_arr['display_name']    = '('.$langscape["Choose a name"].')';
$txt_placeholder_arr['username']        = '('.$langscape["Choose a username"].')';
$txt_placeholder_arr['region']          = '('.$langscape["Enter your location"].')';
$txt_placeholder_arr['website']         = '('.$langscape["Enter your website"].')';
$txt_placeholder_arr['about']           = '('.$langscape["Add a description about you"].')';
$txt_placeholder_arr['demo_reel_url']   = '('.$langscape["Add a link to your video"].')';
$txt_placeholder_arr['skills']          = '('.$langscape["Add a summary of your skills"].')';
$txt_placeholder_arr['software']        = '('.$langscape["Add a description about your tools"].')';
$txt_placeholder_arr['category']        = '('.$langscape["Choose a category"].')';
$txt_placeholder_arr['gender']          = '('.$langscape["Add a gender"].')';
$txt_placeholder_arr['occupation']      = '('.$langscape["Add an occupation"].')';
$txt_placeholder_arr['resume_url']      = '('.$langscape["Add a link to your resume"].')';
$txt_placeholder_arr['facebook_url']    = '('.$langscape["Add link to Facebook profile"].')';
$txt_placeholder_arr['twitter_url']     = '('.$langscape["Add link to Twitter profile"].')';
$txt_placeholder_arr['google_url']      = '('.$langscape["Add link to Google profile"].')';
$txt_placeholder_arr['linkedin_url']    = '('.$langscape["Add link to LinkedIn profile"].')';
$txt_placeholder_arr['kickstarter_url'] = '('.$langscape["Add link to Kickstarter profile"].')';
$txt_placeholder_arr['years_of_experience'] = '0';
$txt_placeholder_arr['description']     = '('.$langscape["Add a description"].')';
$txt_placeholder_arr['source']     = '('.$langscape["Add link to a source"].')';

?>