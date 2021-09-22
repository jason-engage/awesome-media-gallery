<?php
/////// MOKOALA ///////////
// This file initializes Mokoala
require_once 'admin/library/com/mokoala/Mokoala.php';

// The next logical redirect page
$logical_redirect = (!empty($config->site->referer) && $config->site->referer === $config->site->page ? '/' : $config->site->referer);

// Get an instance of the session object
$session = MK_Session::getInstance();

// Get an instance of the config object
$config = MK_Config::getInstance();

// Get an instance of the cookie object
$cookie = MK_Cookie::getInstance();

// Is Mokoala installed?
if( !$config->site->installed )
{
	header("Location: ".MK_Utility::serverUrl("admin/"), true, 302);
	exit;
}

// Connect to the database
MK_Database::connect(MK_Database::DBMS_MYSQL, $config->db->host, $config->db->username, $config->db->password, $config->db->name);

// If user is logging in decide which page they are redirected to next
if( !$config->extensions->core->login_url )
{
	$login_redirect = ltrim($logical_redirect, '/');
}
else
{
	$login_redirect = !empty($config->extensions->core->login_url) ? $config->extensions->core->login_url : '/';	
}

//GET USER INSTANCE
$user_module = MK_RecordModuleManager::getFromType('user');




?>