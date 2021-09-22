<?php

require_once('../_inc.php'); 

include ('../_variables.php');

$app_id = $config->site->facebook->app_id;
$app_secret = $config->site->facebook->app_secret;
$app_scope = "manage_pages,publish_actions,offline_access";
$post_login_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if (isset($_REQUEST['code'])) {
	$code = $_REQUEST['code'];
}

//Obtain the access_token with publish_stream permission 
if(empty($code)){ 
	$dialog_url= "http://www.facebook.com/dialog/oauth?"
	. "client_id=" .  $app_id 
	. "&redirect_uri=" . urlencode( $post_login_url)
	.  "&scope=" . $app_scope;
	
	echo("<script>top.location.href='" . $dialog_url . "'</script>");

} else {


	$token_url="https://graph.facebook.com/oauth/access_token?"
	. "client_id=" . $app_id 
	. "&redirect_uri=". urlencode($post_login_url)
	. "&client_secret=" . $app_secret
	. "&code=" . $code;
	$response = file_get_contents($token_url);
	$params = null;
	parse_str($response, $params);
	$access_token = $params['access_token'];

	if($access_token) {
	
	
		$token_url="https://graph.facebook.com/oauth/access_token?"
		. "client_id=" . $app_id 
		. "&redirect_uri=". urlencode($post_login_url)
		. "&client_secret=" . $app_secret
		.'&grant_type=fb_exchange_token'
		. "&fb_exchange_token=" . $access_token;
		$response = file_get_contents($token_url);
		$access_token = $params['access_token'];
		//echo 'new access token: '.$access_token;
		
		//SAVE TOKEN IN CONFIG FILE
		$config_data_new = array();
		$config_data_new['site.facebook.access_token'] = $access_token;
		
		if( is_array( $config_data_new ) ) {
		
			$config = parse_ini_file('../admin/config.ini.php');
			write_ini_file(array_merge_replace($config, $config_data_new), '../admin/config.ini.php');
			echo 'Successfully saved access token: '.$access_token . '<br><br> You may close this window';		
		}
	}
}