<?php

abstract class MK_Request
{
	protected static $params = array();
	protected static $codes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '(Unused)',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
	);

	public static function init($query = array(), $post = array())
	{
		self::$params = array(
			'param' => array(),
			'query' => $query,
			'post' => $post
		);
	}

	public static function getParam( $key, $default_value = null )
	{
		$value = $default_value;
		$value = self::_getParam( $key, $value );
		$value = self::getQuery( $key, $value );
		$value = self::getPost( $key, $value );
		return $value;
	}

	protected static function _getParam( $key = null, $default_value = null )
	{
		if(empty($key))
		{
			return self::$params['param'];
		}
		elseif( array_key_exists( $key, self::$params['param'] ) )
		{
			return self::$params['param'][$key];
		}
		else
		{
			return $default_value;
		}
	}

	public static function getPost( $key = null, $default_value = null )
	{
		if(empty($key))
		{
			return self::$params['post'];
		}
		elseif( array_key_exists( $key, self::$params['post'] ) )
		{
			return self::$params['post'][$key];
		}
		else
		{
			return $default_value;
		}
	}

	public static function getQuery( $key = null, $default_value = null )
	{
		if(empty($key))
		{
			return self::$params['query'];
		}
		elseif( array_key_exists( $key, self::$params['query'] ) )
		{
			return self::$params['query'][$key];
		}
		else
		{
			return $default_value;
		}
	}
	
	public static function setParam( $key, $value )
	{
		self::$params['param'][$key] = $value;
	}
	
	public static function setPost( $key, $value )
	{
		self::$params['post'][$key] = $value;
	}
	
	public static function setQuery( $key, $value )
	{
		self::$params['query'][$key] = $value;
	}
	
	public static function getStatusCode( $status = null )
	{
		if( array_key_exists($status, self::$codes) )
		{
			return self::$codes[$status];
		}
		else
		{
			return self::$codes;
		}
	}
	
}

?>