<?php
// Parse config
$config_ini = parse_ini_file(dirname(__FILE__).'/../../../config.ini.php');

foreach( $config_ini as $config_key => $config_value )
{
	if( is_string($config_value) )
	{
		$config_ini[$config_key] = MK_Utility::unescapeText( $config_value );
	}
}

// set timezone
$config_ini['site.timezone'] = !empty($config_ini['site.timezone']) ? $config_ini['site.timezone'] : 'Europe/London' ;
date_default_timezone_set($config_ini['site.timezone']);

// Set error reporting
$error_levels = array(
	0 => 0,
	1 => E_ERROR | E_WARNING | E_PARSE,
	2 => E_ALL
);
$error_level = $config_ini['site.error_reporting'];
$error_level = $error_levels[$error_level];
error_reporting( $error_level );

// Current URI
$current_page = parse_url($_SERVER['REQUEST_URI']);
$current_page = (!empty($current_page['path']) ? $current_page['path']: '').(!empty($current_page['query']) ? '?'.$current_page['query'] : '').(!empty($current_page['fragment']) ? '#'.$current_page['fragment'] : '');


/*
$actual_link = parse_url($_SERVER['REQUEST_URI']);
var_dump($actual_link);
//$actual_link = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME);
$actual_link = rtrim( str_replace('admin', '', $actual_link), '\\/' ).'/';
echo $actual_link;
echo '<br>';

$cookie_base_href_path = !empty($config_ini['site.url']) ? $config_ini['site.url'] : ;
*/


// Base URI
$base_href_path = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
$cookie_base_href_path = rtrim( str_replace('admin', '', $base_href_path), '\\/' ).'/';

//TEST
$cookie_base_href_path = '/';
//die;


$current_page_name = str_replace_first($base_href_path, '', $current_page);

$base_href_protocol = ( array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http' ).'://';
if( array_key_exists('HTTP_HOST', $_SERVER) && !empty($_SERVER['HTTP_HOST']) )
{
	$base_href_host = $_SERVER['HTTP_HOST'];
}
elseif( array_key_exists('SERVER_NAME', $_SERVER) && !empty($_SERVER['SERVER_NAME']) )
{
	$base_href_host = $_SERVER['SERVER_NAME'].( $_SERVER['SERVER_PORT'] !== 80 ? ':'.$_SERVER['SERVER_PORT'] : '' );
}
$base_href = rtrim( $base_href_protocol.$base_href_host.$base_href_path, '\\/' ).'/';

// $_POST, $_FILES, $_GET
$params = $_GET;
$post = array_merge_replace($_POST, $_FILES);

$path = array();

if( !empty( $params['module_path'] ) )
{
	$path = array_filter( explode( '/', $params['module_path'] ) );
	unset( $params['module_path'] );
}

MK_Request::init( $params, $post );

// Define Cookie & Session
$cookie_path = '/';
MK_Session::start( 'mk', $cookie_base_href_path, ( $_SERVER['SERVER_NAME'] === 'localhost' ? false : $base_href_host ) );
MK_Cookie::start(  $cookie_base_href_path, ( $_SERVER['SERVER_NAME'] === 'localhost' ? false : $base_href_host ) );

// Get session
$session = MK_Session::getInstance();

/*
	Referring URL
*/
$tidy_referer = '';
if( !empty($_SERVER['HTTP_REFERER']) )
{
	$tidy_referer = parse_url($_SERVER['HTTP_REFERER']);
	$tidy_referer = (!empty($tidy_referer['path']) ? $tidy_referer['path'] : '').(!empty($tidy_referer['query']) ? '?'.$tidy_referer['query'] : '').(!empty($tidy_referer['fragment']) ? '#'.$tidy_referer['fragment'] : '');
}
		
if(!empty($_SERVER['REQUEST_URI']))
{
	$tidy_current = parse_url($_SERVER['REQUEST_URI']);
	$tidy_current = (!empty($tidy_referer['path']) ? $tidy_referer['path'] : '').(!empty($tidy_current['query']) ? '?'.$tidy_current['query'] : '').(!empty($tidy_current['fragment']) ? '#'.$tidy_current['fragment'] : '');
}

// If no refer has been set
if( empty($session->referer) )
{
	// Has the user came from another site
	if(!empty($tidy_referer) && strpos($_SERVER['HTTP_REFERER'], $base_href_host) === false)
	{
		$http_referer = $tidy_referer;
	}
	// If not then set their session to the actual referer
	else
	{
		$http_referer = $tidy_current;
	}
}
elseif( $tidy_referer !== $tidy_current )
{
	$http_referer = $tidy_referer;
}
else
{
	$http_referer = $session->referer;
}

$session->referer = $http_referer;

// Custom module settings
foreach($config_ini as $custom_key => $custom_value)
{
	$config_key_sections = explode('.', $custom_key);
	if(count($config_key_sections) === 3 && $config_key_sections[0] === 'extensions')
	{
		$config_data['extensions'][$config_key_sections[1]][$config_key_sections[2]] = $custom_value;
	}
}

// Template / Theme
list($template, $template_theme) = explode('/', $config_ini['site.template']);

/* DATABASE SETTINGS */
$config_data['db']['prefix'] 				= (string) !empty($config_ini['db.prefix']) ? $config_ini['db.prefix'] : null;
$config_data['db']['host'] 					= (string) !empty($config_ini['db.host']) ? $config_ini['db.host'] : null;
$config_data['db']['name'] 					= (string) !empty($config_ini['db.name']) ? $config_ini['db.name'] : null;
$config_data['db']['username'] 				= (string) !empty($config_ini['db.username']) ? $config_ini['db.username'] : null;
$config_data['db']['password'] 				= (string) !empty($config_ini['db.password']) ? $config_ini['db.password'] : null;
$config_data['db']['charset'] 				= (string) 'utf8';
$config_data['db']['components'] 			= !empty($config_ini['db.components']) ? $config_ini['db.components'] : array();

/* PHP INI SETTINGS */
$config_data['site']['settings']['post_max_size'] = MK_Utility::getBytes( ini_get('post_max_size') );
$config_data['site']['settings']['upload_max_filesize'] = MK_Utility::getBytes( ini_get('upload_max_filesize') );
$config_data['site']['settings']['memory_limit'] = MK_Utility::getBytes( ini_get('memory_limit') );

/* API KEYS */
$config_data['site']['bitly']['login_id'] 	= (string) !empty($config_ini['site.bitly.login_id']) ? $config_ini['site.bitly.login_id'] : null;
$config_data['site']['bitly']['app_key']	= (string) !empty($config_ini['site.bitly.app_key']) ? $config_ini['site.bitly.app_key'] : null;
$config_data['site']['bitly']['enabled']	= (boolean) !empty($config_ini['site.bitly.enabled']) ? $config_ini['site.bitly.enabled'] : null;

$config_data['site']['soundcloud']['app_id'] 	= (string) !empty($config_ini['site.soundcloud.app_id']) ? $config_ini['site.soundcloud.app_id'] : null;
$config_data['site']['soundcloud']['app_secret']	= (string) !empty($config_ini['site.soundcloud.app_secret']) ? $config_ini['site.soundcloud.app_secret'] : null;
$config_data['site']['soundcloud']['enabled']	= (boolean) !empty($config_ini['site.soundcloud.enabled']) ? $config_ini['site.soundcloud.enabled'] : null;

$config_data['site']['facebook']['app_id'] 	= (string) !empty($config_ini['site.facebook.app_id']) ? $config_ini['site.facebook.app_id'] : null;
$config_data['site']['facebook']['app_secret']	= (string) !empty($config_ini['site.facebook.app_secret']) ? $config_ini['site.facebook.app_secret'] : null;
$config_data['site']['facebook']['login']	= (boolean) !empty($config_ini['site.facebook.login']) ? $config_ini['site.facebook.login'] : null;
$config_data['site']['facebook']['access_token']	= (boolean) !empty($config_ini['site.facebook.access_token']) ? $config_ini['site.facebook.access_token'] : null;
$config_data['site']['facebook']['page_id']	= (boolean) !empty($config_ini['site.facebook.page_id']) ? $config_ini['site.facebook.page_id'] : null;

$config_data['site']['twitter']['app_key'] 	= (string) !empty($config_ini['site.twitter.app_key']) ? $config_ini['site.twitter.app_key'] : null;
$config_data['site']['twitter']['app_secret']	= (string) !empty($config_ini['site.twitter.app_secret']) ? $config_ini['site.twitter.app_secret'] : null;
$config_data['site']['twitter']['login']	= (boolean) !empty($config_ini['site.twitter.login']) ? $config_ini['site.twitter.login'] : null;

$config_data['site']['linkedin']['client_id'] 	= (string) !empty($config_ini['site.linkedin.client_id']) ? $config_ini['site.linkedin.client_id'] : null;
$config_data['site']['linkedin']['client_secret']	= (string) !empty($config_ini['site.linkedin.client_secret']) ? $config_ini['site.linkedin.client_secret'] : null;
$config_data['site']['linkedin']['login']	= (boolean) !empty($config_ini['site.linkedin.login']) ? $config_ini['site.linkedin.login'] : null;

$config_data['site']['yahoo']['client_id'] 	= (string) !empty($config_ini['site.yahoo.client_id']) ? $config_ini['site.yahoo.client_id'] : null;
$config_data['site']['yahoo']['client_secret']	= (string) !empty($config_ini['site.yahoo.client_secret']) ? $config_ini['site.yahoo.client_secret'] : null;
$config_data['site']['yahoo']['login']	= (boolean) !empty($config_ini['site.yahoo.login']) ? $config_ini['site.yahoo.login'] : null;

$config_data['site']['windowslive']['client_id'] 	= (string) !empty($config_ini['site.windowslive.client_id']) ? $config_ini['site.windowslive.client_id'] : null;
$config_data['site']['windowslive']['client_secret']= (string) !empty($config_ini['site.windowslive.client_secret']) ? $config_ini['site.windowslive.client_secret'] : null;
$config_data['site']['windowslive']['login']		= (boolean) !empty($config_ini['site.windowslive.login']) ? $config_ini['site.windowslive.login'] : null;

$config_data['site']['google']['client_id'] 	= (string) !empty($config_ini['site.google.client_id']) ? $config_ini['site.google.client_id'] : null;
$config_data['site']['google']['client_secret']= (string) !empty($config_ini['site.google.client_secret']) ? $config_ini['site.google.client_secret'] : null;
$config_data['site']['google']['login']		= (boolean) !empty($config_ini['site.google.login']) ? $config_ini['site.google.login'] : null;
$config_data['site']['google']['api_key'] 	= (string) !empty($config_ini['site.google.api_key']) ? $config_ini['site.google.api_key'] : null;


$config_data['site']['wordpress']['client_id'] 	= (string) !empty($config_ini['site.wordpress.client_id']) ? $config_ini['site.wordpress.client_id'] : null;
$config_data['site']['wordpress']['client_secret']= (string) !empty($config_ini['site.wordpress.client_secret']) ? $config_ini['site.wordpress.client_secret'] : null;
$config_data['site']['wordpress']['login']		= (boolean) !empty($config_ini['site.wordpress.login']) ? $config_ini['site.wordpress.login'] : null;

/* OTHER SETTINGS */
$config_data['site']['hash'] 				= !empty($config_ini['site.hash']) ? $config_ini['site.hash'] : 'sha1';
$config_data['site']['installed'] 			= (boolean) !empty($config_ini['site.installed']) ? $config_ini['site.installed'] : null;
$config_data['site']['path'] 				= (string) realpath(dirname(__FILE__).'/../../../..');
$config_data['site']['base'] 				= (string) str_replace('admin', '', $base_href_path);
$config_data['site']['page'] 				= (string) $current_page;
$config_data['site']['page_name'] 			= (string) $current_page_name;
$config_data['site']['base_href'] 			= (string) $base_href;
$config_data['site']['upload_path']			= (string) !empty($config_ini['site.upload_path']) ? $config_ini['site.upload_path'] : null;

$config_data['admin']['path'] 				= (string) realpath(dirname(__FILE__).'/../../..');

$config_data['site']['valid_file_extensions'] 	= !empty($config_ini['site.valid_file_extensions']) ? explode(',', $config_ini['site.valid_file_extensions']) : array();
$config_data['site']['referer'] 			= (string) $session->referer;
$config_data['site']['charset'] 			= (string) 'utf-8';


/* SITE OPTIONS */

$config_data['site']['name'] 				= (string) !empty($config_ini['site.name']) ? $config_ini['site.name'] : null;

$config_data['site']['title'] 				= (string) !empty($config_ini['site.title']) ? $config_ini['site.title'] : null;
$config_data['site']['desc'] 				= (string) !empty($config_ini['site.desc']) ? $config_ini['site.desc'] : null;

$config_data['site']['caption'] 			= (string) !empty($config_ini['site.caption']) ? $config_ini['site.caption'] : null;

$config_data['site']['google_site_verification'] 	= (string) !empty($config_ini['site.google_site_verification']) ? $config_ini['site.google_site_verification'] : null;

$config_data['site']['logo'] 				= (string) !empty($config_ini['site.logo']) ? $config_ini['site.logo'] : null;
$config_data['site']['logo_sticky'] 		= (string) !empty($config_ini['site.logo_sticky']) ? $config_ini['site.logo_sticky'] : null;
$config_data['site']['logo_modal'] 			= (string) !empty($config_ini['site.logo_modal']) ? $config_ini['site.logo_modal'] : null;

$config_data['site']['email'] 				= (string) !empty($config_ini['site.email']) ? $config_ini['site.email'] : null;

$config_data['site']['url'] 				= (string) !empty($config_ini['site.url']) ? $config_ini['site.url'] : null;
$config_data['site']['timezone'] 			= (string) $config_ini['site.timezone'];

$config_data['site']['user_timeout'] 		= (integer) !empty($config_ini['user.timeout']) ? $config_ini['user.timeout'] : null;

$config_data['site']['date_format'] 		= (string) !empty($config_ini['site.date_format']) ? $config_ini['site.date_format'] : null;
$config_data['site']['time_format'] 		= (string) !empty($config_ini['site.time_format']) ? $config_ini['site.time_format'] : null;
$config_data['site']['datetime_format'] 	= (string) $config_data['site']['date_format'].' '.$config_data['site']['time_format'];

$config_data['site']['enable_tracking'] 	= (boolean) !empty($config_ini['site.enable_tracking']) ? $config_ini['site.enable_tracking'] : false;

//$config_data['site']['path'] 	= (string) !empty($config_ini['site.path']) ? $config_ini['site.path'] : false;
//$config_data['site']['log_actions'] 		= (boolean) !empty($config_ini['site.log_actions']) ? $config_ini['site.log_actions'] : false;

/* HEADER OPTIONS */
$config_data['site']['header']['enable_page_loader'] 		= (boolean) !empty($config_ini['site.header.enable_page_loader']) ? $config_ini['site.header.enable_page_loader'] : false;
$config_data['site']['header']['enable_header'] 		= (boolean) !empty($config_ini['site.header.enable_header']) ? $config_ini['site.header.enable_header'] : false;
$config_data['site']['header']['enable_bg_image'] 		= (boolean) !empty($config_ini['site.header.enable_bg_image']) ? $config_ini['site.header.enable_bg_image'] : false;
$config_data['site']['header']['bg_image'] 		= (integer) !empty($config_ini['site.header.bg_image']) ? $config_ini['site.header.bg_image'] : false;
$config_data['site']['header']['height'] 		= (integer) !empty($config_ini['site.header.height']) ? $config_ini['site.header.height'] : false;
$config_data['site']['header']['menu_position'] 		= (boolean) !empty($config_ini['site.header.menu_position']) ? $config_ini['site.header.menu_position'] : false;
$config_data['site']['style']['emphasize_upload'] = (boolean) !empty($config_ini['site.style.emphasize_upload']) ? $config_ini['site.style.emphasize_upload'] : null;

/*$config_data['site']['header']['enable_fb_like_button'] 		= (boolean) !empty($config_ini['site.header.enable_fb_like_button']) ? $config_ini['site.header.enable_fb_like_button'] : false;

$config_data['site']['header']['combine_sign_in_up'] 		= (boolean) !empty($config_ini['site.header.combine_sign_in_up']) ? $config_ini['site.header.combine_sign_in_up'] : false;
*/

/* MEMBER PAGE OPTIONS */
$config_data['site']['default_avatar'] 		= (string) !empty($config_ini['site.default_avatar']) ? $config_ini['site.default_avatar'] : null;

$config_data['site']['members']['enable_cover_photo'] 	= (string) !empty($config_ini['site.members.enable_cover_photo']) ? $config_ini['site.members.enable_cover_photo'] : false;

$config_data['site']['members']['default_cover_photo'] 	= (string) !empty($config_ini['site.members.default_cover_photo']) ? $config_ini['site.members.default_cover_photo'] : false;

$config_data['site']['members']['enable_gender'] 	= (boolean) !empty($config_ini['site.members.enable_gender']) ? $config_ini['site.members.enable_gender'] : false;
$config_data['site']['members']['enable_video'] 	= (boolean) !empty($config_ini['site.members.enable_video']) ? $config_ini['site.members.enable_video'] : false;
$config_data['site']['members']['enable_skills'] 	= (boolean) !empty($config_ini['site.members.enable_skills']) ? $config_ini['site.members.enable_skills'] : false;
$config_data['site']['members']['enable_occupation'] 	= (boolean) !empty($config_ini['site.members.enable_occupation']) ? $config_ini['site.members.enable_occupation'] : false;
$config_data['site']['members']['enable_category'] 	= (boolean) !empty($config_ini['site.members.enable_category']) ? $config_ini['site.members.enable_category'] : false;
$config_data['site']['members']['enable_interests'] 	= (boolean) !empty($config_ini['site.members.enable_interests']) ? $config_ini['site.members.enable_interests'] : false;
$config_data['site']['members']['enable_dob'] 	= (boolean) !empty($config_ini['site.members.enable_dob']) ? $config_ini['site.members.enable_dob'] : false;
$config_data['site']['members']['enable_software'] 	= (boolean) !empty($config_ini['site.members.enable_software']) ? $config_ini['site.members.enable_software'] : false;
$config_data['site']['members']['enable_contact_form'] 	= (boolean) !empty($config_ini['site.members.enable_contact_form']) ? $config_ini['site.members.enable_contact_form'] : false;
$config_data['site']['members']['enable_available'] 	= (boolean) !empty($config_ini['site.members.enable_available']) ? $config_ini['site.members.enable_available'] : false;
$config_data['site']['members']['enable_resume'] 	= (boolean) !empty($config_ini['site.members.enable_resume']) ? $config_ini['site.members.enable_resume'] : false;
$config_data['site']['members']['enable_public_emails'] 	= (boolean) !empty($config_ini['site.members.enable_public_emails']) ? $config_ini['site.members.enable_public_emails'] : false;
$config_data['site']['members']['enable_stats'] 	= (boolean) !empty($config_ini['site.members.enable_stats']) ? $config_ini['site.members.enable_stats'] : false;
$config_data['site']['members']['enable_email_registration'] 	= (boolean) !empty($config_ini['site.members.enable_email_registration']) ? $config_ini['site.members.enable_email_registration'] : false;
$config_data['site']['members']['enable_signup_notice'] 	= (boolean) !empty($config_ini['site.members.enable_signup_notice']) ? $config_ini['site.members.enable_signup_notice'] : false;

$config_data['site']['members']['enable_approval'] 	= (boolean) !empty($config_ini['site.members.enable_approval']) ? $config_ini['site.members.enable_approval'] : false;
$config_data['site']['members']['disable_registration'] 	= (boolean) !empty($config_ini['site.members.disable_registration']) ? $config_ini['site.members.disable_registration'] : false;
$config_data['site']['members']['disable_uploads'] 	= (boolean) !empty($config_ini['site.members.disable_uploads']) ? $config_ini['site.members.disable_uploads'] : false;


//$config_data['site']['members']['enable_invitations'] 	= (boolean) !empty($config_ini['site.members.enable_invitations']) ? $config_ini['site.members.enable_invitations'] : false;
//$config_data['site']['members']['enable_verification'] 	= (boolean) !empty($config_ini['site.members.enable_verification']) ? $config_ini['site.members.enable_verification'] : false;
//$config_data['site']['members']['maximum_uploads'] 	= (integer) !empty($config_ini['site.members.maximum_uploads']) ? $config_ini['site.members.maximum_uploads'] : false;
//$config_data['site']['members']['activity_time']	= (integer) !empty($config_ini['site.members.activity_time']) ? $config_ini['site.members.activity_time'] : null;
//$config_data['site']['members']['enable_bg_image']	= (boolean) !empty($config_ini['site.members.enable_bg_image']) ? $config_ini['site.members.enable_bg_image'] : null;


/* STYLESHEET OPTIONS */
$config_data['site']['dev_mode'] 			= (boolean) !empty($config_ini['site.dev_mode']) ? $config_ini['site.dev_mode'] : false;


/* SOCIAL OPTIONS */
$config_data['site']['social']['image_square'] = (string) !empty($config_ini['site.social.image_square']) ? $config_ini['site.social.image_square'] : null;
$config_data['site']['social']['image_wide'] 	= (string) !empty($config_ini['site.social.image_wide']) ? $config_ini['site.social.image_wide'] : null;
$config_data['site']['social']['twitter'] 		= (string) !empty($config_ini['site.social.twitter']) ? $config_ini['site.social.twitter'] : null;
$config_data['site']['social']['enable_post_to_fb'] 		= (string) !empty($config_ini['site.social.enable_post_to_fb']) ? $config_ini['site.social.enable_post_to_fb'] : null;
$config_data['site']['social']['fb_post_type'] 		= (string) !empty($config_ini['site.social.fb_post_type']) ? $config_ini['site.social.fb_post_type'] : null;


/* FOOTER */
$config_data['site']['footer']['enable_footer'] 		= (boolean) !empty($config_ini['site.footer.enable_footer']) ? $config_ini['site.footer.enable_footer'] : false;

$config_data['site']['footer']['twitter'] 		= (string) !empty($config_ini['site.footer.twitter']) ? $config_ini['site.footer.twitter'] : null;
$config_data['site']['footer']['facebook'] 	= (string) !empty($config_ini['site.footer.facebook']) ? $config_ini['site.footer.facebook'] : null;
$config_data['site']['footer']['youtube'] 	= (string) !empty($config_ini['site.footer.youtube']) ? $config_ini['site.footer.youtube'] : null;
$config_data['site']['footer']['instagram'] 	= (string) !empty($config_ini['site.footer.instagram']) ? $config_ini['site.footer.instagram'] : null;
$config_data['site']['footer']['pinterest'] 	= (string) !empty($config_ini['site.footer.pinterest']) ? $config_ini['site.footer.pinterest'] : null;
$config_data['site']['footer']['vimeo'] 	= (string) !empty($config_ini['site.footer.vimeo']) ? $config_ini['site.footer.vimeo'] : null;
$config_data['site']['footer']['google_plus'] 	= (string) !empty($config_ini['site.footer.google_plus']) ? $config_ini['site.footer.google_plus'] : null;
$config_data['site']['footer']['flickr'] 	= (string) !empty($config_ini['site.footer.flickr']) ? $config_ini['site.footer.flickr'] : null;
$config_data['site']['footer']['blog'] 	= (string) !empty($config_ini['site.footer.blog']) ? $config_ini['site.footer.blog'] : null;

/*
$config_data['site']['footer']['height'] 		= (string) !empty($config_ini['site.footer.height']) ? $config_ini['site.footer.height'] : null;
*/

/*THEME OPTIONS*/

$config_data['site']['style']['enable_forced_login'] 	= (boolean) !empty($config_ini['site.style.enable_forced_login']) ? $config_ini['site.style.enable_forced_login'] : null;
$config_data['site']['style']['enable_full_width'] 	= (boolean) !empty($config_ini['site.style.enable_full_width']) ? $config_ini['site.style.enable_full_width'] : null;

$config_data['site']['media']['max_filesize'] 	= (boolean) !empty($config_ini['site.media.max_filesize']) ? $config_ini['site.media.max_filesize'] : '1572864';

$config_data['site']['members']['sort_by'] 	= (boolean) !empty($config_ini['site.members.sort_by']) ? $config_ini['site.members.sort_by'] : null;
$config_data['site']['media']['enable_approval'] 	= (boolean) !empty($config_ini['site.media.enable_approval']) ? $config_ini['site.media.enable_approval'] : null;
$config_data['site']['media']['enable_unapproved_login'] 	= (boolean) !empty($config_ini['site.media.enable_unapproved_login']) ? $config_ini['site.media.enable_unapproved_login'] : null;
$config_data['site']['media']['enable_images'] 	= (boolean) !empty($config_ini['site.media.enable_images']) ? $config_ini['site.media.enable_images'] : null;
$config_data['site']['media']['enable_videos'] 	= (boolean) !empty($config_ini['site.media.enable_videos']) ? $config_ini['site.media.enable_videos'] : null;
$config_data['site']['media']['enable_audio'] 	= (boolean) !empty($config_ini['site.media.enable_audio']) ? $config_ini['site.media.enable_audio'] : null;
$config_data['site']['style']['enable_cookies_notification'] 			= (string) !empty($config_ini['site.style.enable_cookies_notification']) ? $config_ini['site.style.enable_cookies_notification'] : null;
$config_data['site']['style']['modal_effect'] 	= (string) !empty($config_ini['site.style.modal_effect']) ? $config_ini['site.style.modal_effect'] : null;
$config_data['site']['style']['loading'] 	= (integer) !empty($config_ini['site.style.loading']) ? $config_ini['site.style.loading'] : 1;
$config_data['site']['style']['icon_like'] 	= (string) !empty($config_ini['site.style.icon_like']) ? $config_ini['site.style.icon_like'] : "heart";
$config_data['site']['header']['enable_search'] 		= (boolean) !empty($config_ini['site.header.enable_search']) ? $config_ini['site.header.enable_search'] : false;

/*
//$config_data['site']['style']['primary_color'] 		= (string) !empty($config_ini['site.style.primary_color']) ? $config_ini['site.style.primary_color'] : null;
//$config_data['site']['style']['secondary_color'] 		= (string) !empty($config_ini['site.style.secondary_color']) ? $config_ini['site.style.secondary_color'] : null;
//$config_data['site']['style']['stroke_color'] 		= (string) !empty($config_ini['site.style.stroke_color']) ? $config_ini['site.style.stroke_color'] : null;

//$config_data['site']['style']['box_radius'] 		= (integer) !empty($config_ini['site.style.box_radius']) ? $config_ini['site.style.box_radius'] : null;
//$config_data['site']['style']['box_shadow'] 		= (integer) !empty($config_ini['site.style.box_shadow']) ? $config_ini['site.style.box_shadow'] : null;


//$config_data['site']['style']['button_radius'] 		= (integer) !empty($config_ini['site.style.button_radius']) ? $config_ini['site.style.button_radius'] : null;
//$config_data['site']['style']['button_shadow'] 		= (integer) !empty($config_ini['site.style.button_shadow']) ? $config_ini['site.style.button_shadow'] : null;

$config_data['site']['style']['enable_google_font'] = (boolean) !empty($config_ini['site.style.enable_google_font']) ? $config_ini['site.style.enable_google_font'] : null;
$config_data['site']['style']['google_font'] 		= (string) !empty($config_ini['site.style.google_font']) ? $config_ini['site.style.google_font'] : null;
$config_data['site']['style']['enable_captchas'] 	= (boolean) !empty($config_ini['site.style.enable_captchas']) ? $config_ini['site.style.enable_captchas'] : null;
$config_data['site']['style']['enable_bg_image'] 	= (boolean) !empty($config_ini['site.style.enable_bg_image']) ? $config_ini['site.style.enable_bg_image'] : null;
*/

/* DEFAULT VALUES */

$config_data['site']['values']['width_single_image'] 	= (string) !empty($config_ini['site.values.width_single_image']) ? $config_ini['site.values.width_single_image'] : '993';
$config_data['site']['values']['height_single_image'] 	= (string) !empty($config_ini['site.values.height_single_image']) ? $config_ini['site.values.height_single_image'] : '2000';

$config_data['site']['values']['width_carousel_image'] 	= (string) !empty($config_ini['site.values.width_carousel_image']) ? $config_ini['site.values.width_carousel_image'] : '100';
$config_data['site']['values']['height_carousel_image'] 	= (string) !empty($config_ini['site.values.height_carousel_image']) ? $config_ini['site.values.height_carousel_image'] : '100';
$config_data['site']['values']['width_comments_avatar_image'] 	= (string) !empty($config_ini['site.values.width_comments_avatar_image']) ? $config_ini['site.values.width_comments_avatar_image'] : '100';
$config_data['site']['values']['height_comments_avatar_image'] 	= (string) !empty($config_ini['site.values.height_comments_avatar_image']) ? $config_ini['site.values.height_comments_avatar_image'] : '100';

$config_data['site']['values']['width_member_banner'] 	= (string) !empty($config_ini['site.values.width_member_banner']) ? $config_ini['site.values.width_member_banner'] : '779';
$config_data['site']['values']['height_member_banner'] 	= (string) !empty($config_ini['site.values.height_member_banner']) ? $config_ini['site.values.height_member_banner'] : '180';

$config_data['site']['values']['width_image_box'] 	= (string) !empty($config_ini['site.values.width_image_box']) ? $config_ini['site.values.width_image_box'] : '450';
$config_data['site']['values']['height_image_box'] 	= (string) !empty($config_ini['site.values.height_image_box']) ? $config_ini['site.values.height_image_box'] : '300';
$config_data['site']['values']['width_main_carousel_image'] 	= (string) !empty($config_ini['site.values.width_main_carousel_image']) ? $config_ini['site.values.width_main_carousel_image'] : '200';
$config_data['site']['values']['height_main_carousel_image'] 	= (string) !empty($config_ini['site.values.height_main_carousel_image']) ? $config_ini['site.values.height_main_carousel_image'] : '200';

/* PERFORMANCE */
$config_data['site']['style']['enable_cdn'] 	 = (boolean) !empty($config_ini['site.style.enable_cdn']) ? $config_ini['site.style.enable_cdn'] : null;
$config_data['site']['style']['enable_minified'] = (boolean) !empty($config_ini['site.style.enable_minified']) ? $config_ini['site.style.enable_minified'] : null;
$config_data['site']['media']['jpg_quality'] = (integer) (!empty($config_ini['site.media.jpg_quality']) && ($config_ini['site.media.jpg_quality'] > 0) && ($config_ini['site.media.jpg_quality'] <= 100) ) ? $config_ini['site.media.jpg_quality'] : 75;
$config_data['site']['media']['jpg_quality_single'] = (integer) (!empty($config_ini['site.media.jpg_quality_single']) && ($config_ini['site.media.jpg_quality_single'] > 0) && ($config_ini['site.media.jpg_quality_single'] <= 100) ) ? $config_ini['site.media.jpg_quality_single'] : 75;
$config_data['site']['media']['png_compression'] = (integer) ( !empty($config_ini['site.media.png_compression']) && ($config_ini['site.media.png_compression'] >= 1) && ($config_ini['site.media.png_compression'] <= 7) ) ? $config_ini['site.media.png_compression'] : 6;
$config_data['site']['style']['enable_cached_headers'] 	 = (boolean) !empty($config_ini['site.style.enable_cached_headers']) ? $config_ini['site.style.enable_cached_headers'] : null;
$config_data['site']['values']['site_width_calc'] 	= (string) !empty($config_ini['site.values.site_width_calc']) ? $config_ini['site.values.site_width_calc'] : '1500';
$config_data['site']['error_reporting'] 	= (string) !empty($config_ini['site.error_reporting']) ? $config_ini['site.error_reporting'] : '1500';

/* LANGUAGES */
$config_data['site']['languages']['language'] 			= (string) !empty($config_ini['site.languages.language']) ? $config_ini['site.languages.language'] : null;
//$config_data['site']['style']['enable_languages_menu'] 			= (boolean) !empty($config_ini['site.style.enable_languages_menu']) ? $config_ini['site.style.enable_languages_menu'] : null;

/* ANALYTICS */
$config_data['site']['analytics'] 			= (string) !empty($config_ini['site.analytics']) ? $config_ini['site.analytics'] : null;

/* MEDIA PAGE */
$config_data['site']['media']['enable_exif'] 	= (boolean) !empty($config_ini['site.media.enable_exif']) ? $config_ini['site.media.enable_exif'] : null;
$config_data['site']['media']['enable_stretched_image'] 	= (boolean) !empty($config_ini['site.media.enable_stretched_image']) ? $config_ini['site.media.enable_stretched_image'] : null;
$config_data['site']['media']['enable_source'] 	= (boolean) !empty($config_ini['site.media.enable_source']) ? $config_ini['site.media.enable_source'] : null;
$config_data['site']['media']['enable_view_original'] 	= (boolean) !empty($config_ini['site.media.enable_view_original']) ? $config_ini['site.media.enable_view_original'] : null;

$config_data['site']['media']['audio_player'] 	= (boolean) !empty($config_ini['site.media.audio_player']) ? $config_ini['site.media.audio_player'] : null;

$config_data['site']['media']['layout_style'] 	= (boolean) !empty($config_ini['site.media.layout_style']) ? $config_ini['site.media.layout_style'] : null;
$config_data['site']['media']['comments_type'] 	= (string) !empty($config_ini['site.media.comments_type']) ? $config_ini['site.media.comments_type'] : false;
$config_data['site']['enable_guest_comments'] 	= (boolean) !empty($config_ini['site.enable_guest_comments']) ? $config_ini['site.enable_guest_comments'] : false;
$config_data['site']['enable_guest_likes'] 	= (boolean) !empty($config_ini['site.enable_guest_likes']) ? $config_ini['site.enable_guest_likes'] : false;
$config_data['site']['enable_reporting'] 	= (boolean) !empty($config_ini['site.enable_reporting']) ? $config_ini['site.enable_reporting'] : false;
$config_data['site']['media']['enable_autoplay'] 	= (boolean) !empty($config_ini['site.media.enable_autoplay']) ? $config_ini['site.media.enable_autoplay'] : false;

//$config_data['site']['media']['video_player'] 	= (boolean) !empty($config_ini['site.media.video_player']) ? $config_ini['site.media.video_player'] : null;
//$config_data['site']['media']['enable_page_lightbox'] 	= (boolean) !empty($config_ini['site.media.enable_page_lightbox']) ? $config_ini['site.media.enable_page_lightbox'] : null;

/* WATERMARK */
$config_data['site']['media']['enable_watermark'] 	= (boolean) !empty($config_ini['site.media.enable_watermark']) ? $config_ini['site.media.enable_watermark'] : null;
$config_data['site']['media']['watermark'] 	= (string) !empty($config_ini['site.media.watermark']) ? $config_ini['site.media.watermark'] : null;
$config_data['site']['media']['watermark_scale'] 	= (string) !empty($config_ini['site.media.watermark_scale']) ? $config_ini['site.media.watermark_scale'] : null;
$config_data['site']['media']['watermark_position'] 	= (string) !empty($config_ini['site.media.watermark_position']) ? $config_ini['site.media.watermark_position'] : null;

/* WORDPRESS */
$config_data['site']['wordpress']['enable_post_to_wp'] 	= (boolean) !empty($config_ini['site.wordpress.enable_post_to_wp']) ? $config_ini['site.wordpress.enable_post_to_wp'] : false;
$config_data['site']['wordpress']['force_login'] 	= (boolean) !empty($config_ini['site.wordpress.force_login']) ? $config_ini['site.wordpress.force_login'] : false;
$config_data['site']['wordpress']['strict_login'] 	= (boolean) !empty($config_ini['site.wordpress.strict_login']) ? $config_ini['site.wordpress.strict_login'] : false;
$config_data['site']['wordpress']['site_url'] 		= (string) !empty($config_ini['site.wordpress.site_url']) ? $config_ini['site.wordpress.site_url'] : false;
$config_data['site']['wordpress']['admin_username'] 	= (string) !empty($config_ini['site.wordpress.admin_username']) ? $config_ini['site.wordpress.admin_username'] : false;
$config_data['site']['wordpress']['admin_password'] 	= (string) !empty($config_ini['site.wordpress.admin_password']) ? $config_ini['site.wordpress.admin_password'] : false;
$config_data['site']['wordpress']['admin_id'] 	= (integer) isset($config_ini['site.wordpress.admin_id']) ? $config_ini['site.wordpress.admin_id'] : '0';
$config_data['site']['wordpress']['taxonomy_tags'] 	= (string) !empty($config_ini['site.wordpress.taxonomy_tags']) ? $config_ini['site.wordpress.taxonomy_tags'] : 'post_tag';
$config_data['site']['wordpress']['taxonomy_categories'] 	= (string) !empty($config_ini['site.wordpress.taxonomy_categories']) ? $config_ini['site.wordpress.taxonomy_categories'] : 'category';

		
/*MEDIA GRID*/
$config_data['site']['grid']['thumbnail_style'] 	= (string) !empty($config_ini['site.grid.thumbnail_style']) ? $config_ini['site.grid.thumbnail_style'] : null;

$config_data['site']['grid']['column_count'] 	= (integer) !empty($config_ini['site.grid.column_count']) ? $config_ini['site.grid.column_count'] : null;

$config_data['site']['grid']['margin'] 	= (float) !empty($config_ini['site.grid.margin']) ? $config_ini['site.grid.margin'] : null;

$config_data['site']['grid']['type'] 	= (string) !empty($config_ini['site.grid.type']) ? $config_ini['site.grid.type'] : null;
$config_data['site']['grid']['enable_full_width'] 	= (integer) !empty($config_ini['site.grid.enable_full_width']) ? $config_ini['site.grid.enable_full_width'] : null;

$config_data['site']['grid']['items_per_page'] 	= (integer) !empty($config_ini['site.grid.items_per_page']) ? $config_ini['site.grid.items_per_page'] : null;
$config_data['site']['grid']['pagination_type'] 	= (integer) !empty($config_ini['site.grid.pagination_type']) ? $config_ini['site.grid.pagination_type'] : null;
$config_data['site']['grid']['enable_caption'] 	= (boolean) !empty($config_ini['site.grid.enable_caption']) ? $config_ini['site.grid.enable_caption'] : null;
$config_data['site']['grid']['enable_stats'] 	= (boolean) !empty($config_ini['site.grid.enable_stats']) ? $config_ini['site.grid.enable_stats'] : null;
$config_data['site']['grid']['hover_style'] 	= (string) !empty($config_ini['site.grid.hover_style']) ? $config_ini['site.grid.hover_style'] : null;
$config_data['site']['grid']['hover_enable_icon'] 	= (boolean) !empty($config_ini['site.grid.hover_enable_icon']) ? $config_ini['site.grid.hover_enable_icon'] : null;

/*
$config_data['site']['grid']['hover_bgcolor'] 	= (string) !empty($config_ini['site.grid.hover_bgcolor']) ? $config_ini['site.grid.hover_bgcolor'] : null;
$config_data['site']['grid']['boximage_height'] 	= (integer) !empty($config_ini['site.grid.boximage_height']) ? $config_ini['site.grid.boximage_height'] : null;
$config_data['site']['grid']['crop_type'] 	= (integer) !empty($config_ini['site.grid.crop_type']) ? $config_ini['ssite.grid.crop_type'] : null;
*/

/* ADVERTISING */
$config_data['site']['ads']['header_468x60'] 		= (string) !empty($config_ini['site.ads.header_468x60']) ? $config_ini['site.ads.header_468x60'] : null;

$config_data['site']['ads']['top_728x90'] 		= (string) !empty($config_ini['site.ads.top_728x90']) ? $config_ini['site.ads.top_728x90'] : null;
$config_data['site']['ads']['top_242x90'] 		= (string) !empty($config_ini['site.ads.top_242x90']) ? $config_ini['site.ads.top_242x90'] : null;
$config_data['site']['ads']['top_970x90'] 		= (string) !empty($config_ini['site.ads.top_970x90']) ? $config_ini['site.ads.top_970x90'] : null;
$config_data['site']['ads']['top_980x120'] 		= (string) !empty($config_ini['site.ads.top_980x120']) ? $config_ini['site.ads.top_980x120'] : null;

$config_data['site']['ads']['sidebar_160x600'] 	= (string) !empty($config_ini['site.ads.sidebar_160x600']) ? $config_ini['site.ads.sidebar_160x600'] : null;
$config_data['site']['ads']['sidebar_300x250'] 	= (string) !empty($config_ini['site.ads.sidebar_300x250']) ? $config_ini['site.ads.sidebar_300x250'] : null;

$config_data['site']['ads']['enable_header'] 	= (boolean) !empty($config_ini['site.ads.enable_header']) ? $config_ini['site.ads.enable_header'] : null;

$config_data['site']['ads']['enable_home_top'] 	= (boolean) !empty($config_ini['site.ads.enable_home_top']) ? $config_ini['site.ads.enable_home_top'] : null;

$config_data['site']['ads']['enable_media_top'] 	= (boolean) !empty($config_ini['site.ads.enable_media_top']) ? $config_ini['site.ads.enable_media_top'] : null;

$config_data['site']['ads']['enable_member_top'] 	= (boolean) !empty($config_ini['site.ads.enable_member_top']) ? $config_ini['site.ads.enable_member_top'] : null;
$config_data['site']['ads']['enable_member_sidebar'] 	= (boolean) !empty($config_ini['site.ads.enable_member_sidebar']) ? $config_ini['site.ads.enable_member_sidebar'] : null;

$config_data['site']['ads']['enable_members_top'] 	= (boolean) !empty($config_ini['site.ads.enable_members_top']) ? $config_ini['site.ads.enable_members_top'] : null;

$config_data['site']['ads']['enable_blog_top'] 	= (boolean) !empty($config_ini['site.ads.enable_blog_top']) ? $config_ini['site.ads.enable_blog_top'] : null;
$config_data['site']['ads']['enable_other_top'] 	= (boolean) !empty($config_ini['site.ads.enable_other_top']) ? $config_ini['site.ads.enable_other_top'] : null;

/*
$config_data['site']['ads']['enable_media_sidebar'] 	= (boolean) !empty($config_ini['site.ads.enable_media_sidebar']) ? $config_ini['site.ads.enable_media_sidebar'] : null;
$config_data['site']['ads']['enable_home_sidebar'] 	= (boolean) !empty($config_ini['site.ads.enable_home_sidebar']) ? $config_ini['site.ads.enable_home_sidebar'] : null;
$config_data['site']['ads']['enable_members_sidebar'] 	= (boolean) !empty($config_ini['site.ads.enable_members_sidebar']) ? $config_ini['site.ads.enable_members_sidebar'] : null;
$config_data['site']['ads']['enable_blog_sidebar'] 	= (boolean) !empty($config_ini['site.ads.enable_blog_sidebar']) ? $config_ini['site.ads.enable_blog_sidebar'] : null;
$config_data['site']['ads']['enable_other_sidebar'] 	= (boolean) !empty($config_ini['site.ads.enable_other_sidebar']) ? $config_ini['site.ads.enable_other_sidebar'] : null;
*/


/* MOBILE RESPONSIVE */
$config_data['site']['mobile']['enable_responsive_tablet'] 	= (boolean) !empty($config_ini['site.mobile.enable_responsive_tablet']) ? $config_ini['site.mobile.enable_responsive_tablet'] : null;
$config_data['site']['mobile']['enable_responsive_phone'] 	= (boolean) !empty($config_ini['site.mobile.enable_responsive_phone']) ? $config_ini['site.mobile.enable_responsive_phone'] : null;
$config_data['site']['mobile']['disable_modals'] 	= (boolean) !empty($config_ini['site.mobile.disable_modals']) ? $config_ini['site.mobile.disable_modals'] : null;
$config_data['site']['mobile']['items_per_page'] 	= (boolean) !empty($config_ini['site.mobile.items_per_page']) ? $config_ini['site.mobile.items_per_page'] : null;
//$config_data['site']['mobile']['enable_upload'] 	= (boolean) !empty($config_ini['site.mobile.enable_upload']) ? $config_ini['site.mobile.enable_upload'] : null;


/* SLIDER */
$config_data['site']['slider']['layout_style'] 	= (boolean) !empty($config_ini['site.slider.layout_style']) ? $config_ini['site.slider.layout_style'] : null;
$config_data['site']['slider']['type'] 	= (boolean) !empty($config_ini['site.slider.type']) ? $config_ini['site.slider.type'] : null;
$config_data['site']['slider']['media_type'] 	= (string) !empty($config_ini['site.slider.media_type']) ? $config_ini['site.slider.media_type'] : null;
$config_data['site']['slider']['media_source'] 	= (boolean) !empty($config_ini['site.slider.media_source']) ? $config_ini['site.slider.media_source'] : null;
$config_data['site']['slider']['effect_owl'] 	= (boolean) !empty($config_ini['site.slider.effect_owl']) ? $config_ini['site.slider.effect_owl'] : null;
$config_data['site']['slider']['count'] 	= (integer) !empty($config_ini['site.slider.count']) ? $config_ini['site.slider.count'] : 5;

$config_data['site']['slider']['height'] 	= (integer) !empty($config_ini['site.slider.height']) ? $config_ini['site.slider.height'] : null;
$config_data['site']['slider']['width'] 	= (integer) !empty($config_ini['site.slider.width']) ? $config_ini['site.slider.width'] : null;
$config_data['site']['slider']['enable_autoplay'] 	= (string) !empty($config_ini['site.slider.enable_autoplay']) ? $config_ini['site.slider.enable_autoplay'] : false;
$config_data['site']['slider']['enable_navigation'] 	= (string) !empty($config_ini['site.slider.enable_navigation']) ? $config_ini['site.slider.enable_navigation'] : false;
$config_data['site']['slider']['enable_video_play'] 	= (string) !empty($config_ini['site.slider.enable_video_play']) ? $config_ini['site.slider.enable_video_play'] : false;
$config_data['site']['slider']['enable_dots'] 	= (string) !empty($config_ini['site.slider.enable_dots']) ? $config_ini['site.slider.enable_dots'] : false;

/*
$config_data['site']['slider']['enable_fullscreen'] 	= (boolean) !empty($config_ini['site.slider.enable_fullscreen']) ? $config_ini['site.slider.enable_fullscreen'] : null;
$config_data['site']['slider']['enable_home'] 	= (boolean) !empty($config_ini['site.slider.enable_home']) ? $config_ini['site.slider.enable_home'] : null;
$config_data['site']['slider']['enable_media'] 	= (boolean) !empty($config_ini['site.slider.enable_media']) ? $config_ini['site.slider.enable_media'] : null;
$config_data['site']['slider']['enable_members'] 	= (boolean) !empty($config_ini['site.slider.enable_members']) ? $config_ini['site.slider.enable_members'] : null;
$config_data['site']['slider']['enable_member'] 	= (boolean) !empty($config_ini['site.slider.enable_member']) ? $config_ini['site.slider.enable_member'] : null;
$config_data['site']['slider']['enable_other'] 	= (boolean) !empty($config_ini['site.slider.enable_other']) ? $config_ini['site.slider.enable_other'] : null;
//$config_data['site']['slider']['effect_ultimate'] 	= (boolean) !empty($config_ini['site.slider.effect_ultimate']) ? $config_ini['site.slider.effect_ultimate'] : null;
//$config_data['site']['slider']['theme_ultimate'] 	= (boolean) !empty($config_ini['site.slider.theme_ultimate']) ? $config_ini['site.slider.theme_ultimate'] : null;
*/

/* CAROUSEL */
$config_data['site']['carousel']['layout_style'] 	= (string) !empty($config_ini['site.carousel.layout_style']) ? $config_ini['site.carousel.layout_style'] : null;

$config_data['site']['carousel']['enable_fullscreen'] 	= (boolean) !empty($config_ini['site.carousel.enable_fullscreen']) ? $config_ini['site.carousel.enable_fullscreen'] : null;

$config_data['site']['carousel']['type'] 	= (string) !empty($config_ini['site.carousel.type']) ? $config_ini['site.carousel.type'] : null;

$config_data['site']['carousel']['media_source'] 	= (string) !empty($config_ini['site.carousel.media_source']) ? $config_ini['site.carousel.media_source'] : null;

$config_data['site']['carousel']['media_type'] 	= (string) !empty($config_ini['site.carousel.media_type']) ? $config_ini['site.carousel.media_type'] : null;

$config_data['site']['carousel']['column_count'] 	= (integer) !empty($config_ini['site.carousel.column_count']) ? $config_ini['site.carousel.column_count'] : null;

$config_data['site']['carousel']['count'] 	= (integer) !empty($config_ini['site.carousel.count']) ? $config_ini['site.carousel.count'] : 12;

$config_data['site']['carousel']['margin'] 	= (float) !empty($config_ini['site.carousel.margin']) ? $config_ini['site.carousel.margin'] : null;

$config_data['site']['carousel']['thumbnail_style'] 	= (string) !empty($config_ini['site.carousel.thumbnail_style']) ? $config_ini['site.carousel.thumbnail_style'] : null;

$config_data['site']['carousel']['enable_autoplay'] 	= (string) !empty($config_ini['site.carousel.enable_autoplay']) ? $config_ini['site.carousel.enable_autoplay'] : false;

$config_data['site']['carousel']['enable_navigation'] 	= (string) !empty($config_ini['site.carousel.enable_navigation']) ? $config_ini['site.carousel.enable_navigation'] : false;

$config_data['site']['carousel']['enable_video_play'] 	= (string) !empty($config_ini['site.carousel.enable_video_play']) ? $config_ini['site.carousel.enable_video_play'] : false;

$config_data['site']['carousel']['enable_dots'] 	= (string) !empty($config_ini['site.carousel.enable_dots']) ? $config_ini['site.carousel.enable_dots'] : false;


//$config_data['site']['carousel']['enable_home'] 	= (boolean) !empty($config_ini['site.carousel.enable_home']) ? $config_ini['site.carousel.enable_home'] : null;

//$config_data['site']['carousel']['enable_media'] 	= (boolean) !empty($config_ini['site.carousel.enable_media']) ? $config_ini['site.carousel.enable_media'] : null;

//$config_data['site']['carousel']['enable_members'] 	= (boolean) !empty($config_ini['site.carousel.enable_members']) ? $config_ini['site.carousel.enable_members'] : null;

//$config_data['site']['carousel']['enable_member'] 	= (boolean) !empty($config_ini['site.carousel.enable_member']) ? $config_ini['site.carousel.enable_member'] : null;

//$config_data['site']['carousel']['enable_other'] 	= (boolean) !empty($config_ini['site.carousel.enable_other']) ? $config_ini['site.carousel.enable_other'] : null;




/* EMAIL TEMPLATES */


$config_data['site']['email_template'] 		= (string) !empty($config_ini['site.email_template']) ? $config_ini['site.email_template'] : null;

$config_data['site']['emails']['ssl_enabled']	= (string) !empty($config_ini['site.emails.ssl_enabled']) ? $config_ini['site.emails.ssl_enabled'] : null;
$config_data['site']['emails']['ssl_server']	= (string) !empty($config_ini['site.emails.ssl_server']) ? $config_ini['site.emails.ssl_server'] : null;
$config_data['site']['emails']['ssl_username']	= (string) !empty($config_ini['site.emails.ssl_username']) ? $config_ini['site.emails.ssl_username'] : null;
$config_data['site']['emails']['ssl_password']	= (string) !empty($config_ini['site.emails.ssl_password']) ? $config_ini['site.emails.ssl_password'] : null;

$config_data['site']['emails']['registration_subject']	= (string) !empty($config_ini['site.emails.registration_subject']) ? $config_ini['site.emails.registration_subject'] : null;

$config_data['site']['emails']['registration_text']	= (string) !empty($config_ini['site.emails.registration_text']) ? $config_ini['site.emails.registration_text'] : null;

$config_data['site']['emails']['registration_approval_notice']	= (string) !empty($config_ini['site.emails.registration_approval_notice']) ? $config_ini['site.emails.registration_approval_notice'] : null;

$config_data['site']['emails']['registration_subject_admin']	= (string) !empty($config_ini['site.emails.registration_subject_admin']) ? $config_ini['site.emails.registration_subject_admin'] : null;

$config_data['site']['emails']['registration_text_admin']	= (string) !empty($config_ini['site.emails.registration_text_admin']) ? $config_ini['site.emails.registration_text_admin'] : null;

$config_data['site']['emails']['registration_approval_notice_admin']	= (string) !empty($config_ini['site.emails.registration_approval_notice_admin']) ? $config_ini['site.emails.registration_approval_notice_admin'] : null;

$config_data['site']['emails']['approved_subject']	= (string) !empty($config_ini['site.emails.approved_subject']) ? $config_ini['site.emails.approved_subject'] : null;

$config_data['site']['emails']['approved_text']	= (string) !empty($config_ini['site.emails.approved_text']) ? $config_ini['site.emails.approved_text'] : null;

/*
$config_data['site']['email']['enable_admin_action_like']	= (boolean) !empty($config_ini['site.email.enable_admin_action_like']) ? $config_ini['site.email.enable_admin_action_like'] : null;
$config_data['site']['email']['enable_admin_action_comment']	= (boolean) !empty($config_ini['site.email.enable_admin_action_comment']) ? $config_ini['site.email.enable_admin_action_comment'] : null;
$config_data['site']['email']['enable_admin_action_download']	= (boolean) !empty($config_ini['site.email.enable_admin_action_download']) ? $config_ini['site.email.enable_admin_action_download'] : null;
$config_data['site']['email']['enable_admin_action_follow']	= (boolean) !empty($config_ini['site.email.enable_admin_action_follow']) ? $config_ini['site.email.enable_admin_action_follow'] : null;
$config_data['site']['email']['enable_admin_action_upload']	= (boolean) !empty($config_ini['site.email.enable_admin_action_upload']) ? $config_ini['site.email.enable_admin_action_upload'] : null;
$config_data['site']['email']['enable_admin_action_signup']	= (boolean) !empty($config_ini['site.email.enable_admin_action_signup']) ? $config_ini['site.email.enable_admin_action_signup'] : null;
$config_data['site']['email']['enable_admin_action_invitation']	= (boolean) !empty($config_ini['site.email.enable_admin_action_invitation']) ? $config_ini['site.email.enable_admin_action_invitation'] : null;
$config_data['site']['email']['verification_subject']	= (string) !empty($config_ini['site.email.verification_subject']) ? $config_ini['site.email.verification_subject'] : null;
$config_data['site']['email']['verification_text']	= (string) !empty($config_ini['site.email.verification_text']) ? $config_ini['site.email.verification_text'] : null;
$config_data['site']['email']['verification_complete_subject']	= (string) !empty($config_ini['site.email.verification_complete_subject']) ? $config_ini['site.email.verification_complete_subject'] : null;
$config_data['site']['email']['verification_complete_text']	= (string) !empty($config_ini['site.email.verification_complete_text']) ? $config_ini['site.email.verification_complete_text'] : null;
$config_data['site']['email']['email_activation_subject']	= (string) !empty($config_ini['site.email.email_activation_subject']) ? $config_ini['site.email.email_activation_subject'] : null;
$config_data['site']['email']['activation_text']	= (string) !empty($config_ini['site.email.activation_text']) ? $config_ini['site.email.activation_text'] : null;
$config_data['site']['email']['activation_complete_subject']	= (string) !empty($config_ini['site.email.activation_complete_subject']) ? $config_ini['site.email.activation_complete_subject'] : null;
$config_data['site']['email']['activation_complete_text']	= (string) !empty($config_ini['site.email.activation_complete_text']) ? $config_ini['site.email.activation_complete_text'] : null;
$config_data['site']['email']['invitation_subject']	= (string) !empty($config_ini['site.email.invitation_subject']) ? $config_ini['site.email.invitation_subject'] : null;
$config_data['site']['email']['invitation_text']	= (string) !empty($config_ini['site.email.invitation_text']) ? $config_ini['site.email.invitation_text'] : null;
$config_data['site']['email']['invitation_complete_subject']	= (string) !empty($config_ini['site.email.invitation_complete_subject']) ? $config_ini['site.email.invitation_complete_subject'] : null;
$config_data['site']['email']['invitation_complete_text']	= (string) !empty($config_ini['site.email.invitation_complete_text']) ? $config_ini['site.email.invitation_complete_text'] : null;
$config_data['site']['email']['enable_new_comment_emails']	= (boolean) !empty($config_ini['site.email.enable_new_comment_emails']) ? $config_ini['site.email.enable_new_comment_emails'] : null;
$config_data['site']['email']['new_comment_subject']	= (string) !empty($config_ini['site.email.new_comment_subject']) ? $config_ini['site.email.new_comment_subject'] : null;
$config_data['site']['email']['new_comment_text']	= (string) !empty($config_ini['site.email.new_comment_text']) ? $config_ini['site.email.new_comment_text'] : null;
$config_data['site']['email']['enable_new_like_emails']	= (boolean) !empty($config_ini['site.email.enable_new_like_emails']) ? $config_ini['site.email.enable_new_like_emails'] : null;
$config_data['site']['email']['new_like_subject']	= (string) !empty($config_ini['site.email.new_like_subject']) ? $config_ini['site.email.new_like_subject'] : null;
$config_data['site']['email']['new_like_text']	= (string) !empty($config_ini['site.email.new_like_text']) ? $config_ini['site.email.new_like_text'] : null;
$config_data['site']['email']['enable_new_follower_emails']	= (boolean) !empty($config_ini['site.email.enable_new_follower_emails']) ? $config_ini['site.email.enable_new_follower_emails'] : null;
$config_data['site']['email']['new_follower_subject']	= (string) !empty($config_ini['site.email.new_follower_subject']) ? $config_ini['site.email.new_follower_subject'] : null;
$config_data['site']['email']['new_follower_text']	= (string) !empty($config_ini['site.email.new_follower_text']) ? $config_ini['site.email.new_follower_text'] : null;
$config_data['site']['email']['enable_image_reported_emails']	= (boolean) !empty($config_ini['site.email.enable_image_reported_emails']) ? $config_ini['site.email.enable_image_reported_emails'] : null;
$config_data['site']['email']['new_media_subject']	= (string) !empty($config_ini['site.email.new_media_subject']) ? $config_ini['site.email.new_media_subject'] : null;
$config_data['site']['email']['new_media_text']	= (string) !empty($config_ini['site.email.new_media_text']) ? $config_ini['site.email.new_media_text'] : null;
$config_data['site']['email']['enable_new_media_emails']	= (boolean) !empty($config_ini['site.email.enable_new_media_emails']) ? $config_ini['site.email.enable_new_media_emails'] : null;
$config_data['site']['email']['new_media_subject']	= (string) !empty($config_ini['site.email.new_media_subject']) ? $config_ini['site.email.new_media_subject'] : null;
$config_data['site']['email']['new_media_text']	= (string) !empty($config_ini['site.email.new_media_text']) ? $config_ini['site.email.new_media_text'] : null;
$config_data['site']['email']['enable_activity_emails']	= (boolean) !empty($config_ini['site.email.enable_activity_emails']) ? $config_ini['site.email.enable_activity_emails'] : null;
$config_data['site']['email']['activity_subject']	= (string) !empty($config_ini['site.email.activity_subject']) ? $config_ini['site.email.activity_subject'] : null;
$config_data['site']['email']['activity_text']	= (string) !empty($config_ini['site.email.activity_text']) ? $config_ini['site.email.activity_text'] : null;
*/




$config_data['beacon']['lastrun'] 		= (integer) !empty($config_ini['beacon.lastrun']) ? $config_ini['beacon.lastrun'] : null;

$config_data['server']['local'] 			= (string) $_SERVER['SERVER_NAME'] === 'localhost' ? true : false;
$config_data['server']['name'] 				= (string) $base_href_host;
$config_data['server']['time'] 				= (integer) time();

$config_data['server']['execution_start'] 	= (float) !empty($start) ? $start : 0;

$config_data['template'] 					= (string) $template;
$config_data['template_theme'] 				= (string) $template_theme;
$config_data['template_theme_directory']	= (string) 'application/views/'.$template_theme.'/';

$config_data['core']['name'] 				= (string) 'Mokoala';
$config_data['core']['version'] 			= (string) '3.5.0';
$config_data['core']['mode'] 				= (string) !empty($config_ini['site.mode']) ? $config_ini['site.mode'] : MK_Core::MODE_PRODUCT;

$config_data['core']['clean_uris'] 			= (boolean) false;
$config_data['core']['url'] 				= (string) 'http://mokoala.com/';

$config_data['instance']['name'] 			= (string) 'Mokoala CMS';
$config_data['instance']['version'] 		= (string) '1.0';
$config_data['instance']['url'] 			= (string) 'http://mokoala.com/';

// If the PayPal API details are defined then instantiate the PayPal API class 
if( function_exists('curl_init') && !empty($config_data['extensions']['payments_paypal']['api_signature']) && !empty($config_data['extensions']['payments_paypal']['api_password']) && !empty($config_data['extensions']['payments_paypal']['api_username']) && !empty($config_data['extensions']['payments_paypal']['currency']) )
{
	$config_data['paypal'] = new MK_PayPal($config_data['extensions']['payments_paypal']['api_username'], $config_data['extensions']['payments_paypal']['api_password'], $config_data['extensions']['payments_paypal']['api_signature'], true);
}

// If the Facebook API & Secret are defined then load the Facebook API class
if( function_exists('curl_init') && function_exists('json_encode') && !empty($config_data['site']['facebook']['app_secret']) && !empty($config_data['site']['facebook']['app_id']) )
{
	$facebook = new Facebook(array(
		'appId' => $config_data['site']['facebook']['app_id'],
		'secret' => $config_data['site']['facebook']['app_secret'],
		'cookie' => false
	));
	$config_data['facebook'] = $facebook;
}
else
{
	$config_data['site']['facebook']['login'] = (boolean) false;
	$config_data['facebook'] = null;
}

// If the Windows Live API details are defined then instantiate the Windows Live API class 
if( $config_data['site']['windowslive']['login'] && $config_data['site']['windowslive']['client_id'] && $config_data['site']['windowslive']['client_secret'] )
{
	$config_data['windowslive'] = new oauth_client_class();

	$config_data['windowslive']->server = 'Microsoft';
	$config_data['windowslive']->redirect_uri = $config_data['site']['url'] . 'sign-in.php?platform=windowslive';

	$config_data['windowslive']->client_id = $config_data['site']['windowslive']['client_id'];
	$config_data['windowslive']->client_secret = $config_data['site']['windowslive']['client_secret'];
	$config_data['windowslive']->scope = 'wl.basic wl.emails';

	$config_data['windowslive']->Initialize();
}
else
{
	$config_data['site']['windowslive']['login'] = (boolean) false;
	$config_data['windowslive'] = null;
}

// If the Twitter Key & Secret are defined then load the Twitter API class
if( !empty($config_data['site']['twitter']['app_secret']) && !empty($config_data['site']['twitter']['app_key']) )
{
	if(empty($session->twitter_access_token) && ( $oauth_verifier = MK_Request::getQuery('oauth_verifier') ) && !empty($session->twitter_oauth_token) && !empty($session->twitter_oauth_token_secret))
	{
		$twitter = new TwitterOAuth($config_data['site']['twitter']['app_key'], $config_data['site']['twitter']['app_secret'], $session->twitter_oauth_token, $session->twitter_oauth_token_secret);
		unset($session->twitter_oauth_token, $session->twitter_oauth_token_secret);
		
		$twitter_access_token = $twitter->getAccessToken($oauth_verifier);
		$session->twitter_access_token = true;
		$twitter = new TwitterOAuth($config_data['site']['twitter']['app_key'], $config_data['site']['twitter']['app_secret'], $twitter_access_token['oauth_token'], $twitter_access_token['oauth_token_secret']);
	}
	else
	{
		$twitter = new TwitterOAuth($config_data['site']['twitter']['app_key'], $config_data['site']['twitter']['app_secret']);
	}
	$config_data['twitter'] = $twitter;
}
else
{
	$config_data['site']['twitter']['login'] = (boolean) false;
	$config_data['twitter'] = null;
}

// If the Google Key & Secret are defined then load the Google API class
if( !empty($config_data['site']['google']['client_id']) && !empty($config_data['site']['google']['client_secret']) )
{
	$config_data['google'] = new oauth_client_class();

	$config_data['google']->server = 'Google';
	$config_data['google']->redirect_uri = $config_data['site']['url'] . 'sign-in.php?platform=google';

	$config_data['google']->client_id = $config_data['site']['google']['client_id'];
	$config_data['google']->client_secret = $config_data['site']['google']['client_secret'];
	$config_data['google']->scope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';

	$config_data['google']->Initialize();
}
else
{
	$config_data['site']['google']['login'] = (boolean) false;
	$config_data['google'] = null;
}

// If the Yahoo Key & Secret are defined then load the Yahoo API class
if( !empty($config_data['site']['yahoo']['client_id']) && !empty($config_data['site']['yahoo']['client_secret']) )
{
	$config_data['yahoo'] = new oauth_client_class();

	$config_data['yahoo']->server = 'Yahoo';
	$config_data['yahoo']->redirect_uri = $config_data['site']['url'] . 'sign-in.php?platform=yahoo';
	$config_data['yahoo']->client_id = $config_data['site']['yahoo']['client_id'];
	$config_data['yahoo']->client_secret = $config_data['site']['yahoo']['client_secret'];

	$config_data['yahoo']->Initialize();
}
else
{
	$config_data['site']['yahoo']['login'] = (boolean) false;
	$config_data['yahoo'] = null;
}

// If the Wordpress Key & Secret are defined then load the Yahoo API class
if( !empty($config_data['site']['wordpress']['client_id']) && !empty($config_data['site']['wordpress']['client_secret']) )
{
	$config_data['wordpress'] = new oauth_client_class();

	$config_data['wordpress']->server = 'Wordpress';
	//$config_data['wordpress']->redirect_uri = $config_data['site']['url'] . 'sign-in.php';
	$config_data['wordpress']->client_id = $config_data['site']['wordpress']['client_id'];
	$config_data['wordpress']->client_secret = $config_data['site']['wordpress']['client_secret'];
	//$config_data['wordpress']->scope = 'basic';
	//$config_data['wordpress']->state = 'platform=wordpress';
	$config_data['wordpress']->Initialize();
}
else
{
	$config_data['site']['wordpress']['login'] = (boolean) false;
	$config_data['wordpress'] = null;
}

// If the LinkedIn Key & Secret are defined then load the LinkedIn API class
if( !empty($config_data['site']['linkedin']['client_id']) && !empty($config_data['site']['linkedin']['client_secret']) )
{
	$config_data['linkedin'] = new oauth_client_class();

	$config_data['linkedin']->server = 'LinkedIn';
	$config_data['linkedin']->redirect_uri = $config_data['site']['url'] . 'sign-in.php?platform=linkedin';
	$config_data['linkedin']->client_id = $config_data['site']['linkedin']['client_id'];
	$config_data['linkedin']->client_secret = $config_data['site']['linkedin']['client_secret'];
	$config_data['linkedin']->scope = 'r_basicprofile r_emailaddress r_fullprofile';

	$config_data['linkedin']->Initialize();
}
else
{
	$config_data['site']['linkedin']['login'] = (boolean) false;
	$config_data['linkedin'] = null;
}

MK_Config::loadConfig($config_data);
$config = MK_Config::getInstance();


?>