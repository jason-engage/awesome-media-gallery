<?php
require_once '_inc.php';

include ('_variables.php');

$session = MK_Session::getInstance();
$cookie = MK_Cookie::getInstance();
unset($session->login, $cookie->login);

if( !$redirect = $config->extensions->core->logout_url )
{
	$redirect = $logical_redirect;
}

$redirect = $home_page;

header('Location: '.($redirect), true, 302);
exit;
?>