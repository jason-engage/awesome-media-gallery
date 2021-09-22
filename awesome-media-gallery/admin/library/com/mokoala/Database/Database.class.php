<?php

abstract class MK_Database
{
	protected static $connection = null;
	
	const DBMS_MYSQL = 'mysql';

	public static function disconnect()
	{
		self::$connection = null;
	}

	public static function getTableName($table_name)
	{
		$config = MK_Config::getInstance();
		return $config->db->prefix.$table_name;
	}
	
	public static function isConnected()
	{
		if( self::getInstance() )
		{
			$get_test = self::getInstance()->query("SELECT 1 AS test");
			$connection = (boolean) $get_test->rowCount();
		}
		else
		{
			$connection = false;
		}
		return $connection;
	}
	
	public static function connect( $dbms, $host, $username, $password, $database, $port = null )
	{
		$config = MK_Config::getInstance();

		if( !extension_loaded('PDO') || !extension_loaded('pdo_mysql') )
		{
			throw new MK_SQLException("PHP PDO extension needs to be loaded.");
		}

		try
		{
			$pdo = new PDO(
				$dbms.':host='.$host.';'.( $port ? $port.';' : '' ).' dbname='.$database.';',
				$username,
				$password
			);
		
			// Specify character set
			$pdo->query("SET NAMES '".$config->db->charset."'");
	
			// Specify timezone
			$now = new DateTime();  
			$mins = $now->getOffset() / 60;  
			
			$sgn = ($mins < 0 ? -1 : 1);  
			$mins = abs($mins);  
			$hrs = floor($mins / 60);  
			$mins -= $hrs * 60; 
			
			$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);  
	
			$pdo->query("SET time_zone = '$offset'");
	
			// Specify PDO attributes
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 
			$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
	
			self::$connection = $pdo;
		}
		catch( Exception $e )
		{
			throw new MK_SQLException($e->getMessage());
		}
	}
	
	public static function getInstance()
	{
		return self::$connection;
	}
	
}

?>