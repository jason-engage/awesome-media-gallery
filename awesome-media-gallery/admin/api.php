<?php
$start = microtime(true);

require_once 'library/com/mokoala/Mokoala.php';

$session = MK_Session::getInstance();
$cookie = MK_Cookie::getInstance();
$config = MK_Config::getInstance();

if( $cookie->login && empty($session->login) )
{
	$session->login = $cookie->login;
}

MK_Database::connect(MK_Database::DBMS_MYSQL, $config->db->host, $config->db->username, $config->db->password, $config->db->name);

// Get current user
if( !empty($session->login) )
{
    MK_Authorizer::authorizeById( $session->login );
}

$user = MK_Authorizer::authorize();

header('Content-Type: text/html; charset=utf-8');

$api_server = new MK_API_REST_Server();

if( $parameters = MK_Request::getPost() )
{
	$method = 'post';
}
else
{
	$method = 'get';
	$parameters = MK_Request::getQuery();
}

//var_dump($method);
//var_dump($parameters);
//var_dump($user);

$output = $api_server->processRequest($method, $parameters, $user);


print json_encode( $output );

MK_Database::disconnect();

?>