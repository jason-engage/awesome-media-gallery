<?php

abstract class MK_Authorizer
{

	protected static $user;

	public static function authorizeByEmailPass( $email, $password )
	{
		if( empty($email) || empty($password) )
		{
			throw new MK_Exception("Either username and password is blank");
		}
	
		$config = MK_Config::getInstance();

		$password = MK_Utility::getHash( $password );

		$user_module = MK_RecordModuleManager::getFromType('user');
		$search = array(
			array('literal' => "`email` = ".MK_Database::getInstance()->quote($email)." AND ( `password` = ".MK_Database::getInstance()->quote($password)." OR `temporary_password` = ".MK_Database::getInstance()->quote($password)." )"),
			array('field' => 'type', 'value' => MK_RecordUser::TYPE_CORE)
		);

		if( $config->extensions->core->email_verification )
		{
			$search[] = array('field' => 'email_verified', 'value' => '1');	
		}
		
		$results = $user_module->searchRecords( $search );

		if( count($results) === 1 && ( $user = array_pop( $results ) ) )
		{
			self::authorizeById( $user->getId() );
		}
		
		return self::authorize();

	}

	public static function _authorizeByEmailPass( $email, $password )
	{
		if( empty($email) || empty($password) )
		{
			throw new MK_Exception("Either username and password is blank");
		}
	
		$config = MK_Config::getInstance();

		$password = MK_Utility::getHash( $password );

		$user_module = MK_RecordModuleManager::getFromType('user');
		$search = array(
			array('literal' => "`email` = ".MK_Database::getInstance()->quote($email)." AND ( `password` = ".MK_Database::getInstance()->quote($password)." OR `temporary_password` = ".MK_Database::getInstance()->quote($password)." )"),
			array('field' => 'type', 'value' => MK_RecordUser::TYPE_CORE)
		);

		if( $config->extensions->core->email_verification )
		{
			$search[] = array('field' => 'email_verified', 'value' => '1');	
		}
		
		$results = $user_module->searchRecords( $search );

		if( count($results) === 1 && ( $user = array_pop( $results ) ) )
		{
			return $user;
		}
		
		return MK_RecordManager::getNewRecord( $user_module->getId() );

	}

	public static function authorizeByEmail( $email )
	{
		$user_module = MK_RecordModuleManager::getFromType('user');

		$search = array(
			array('field' => 'email', 'value' => $email),
		);

		$search_results = $user_module->searchRecords( $search );
		$user = array_pop( $search_results );

		if( !empty($user) )
		{
			return self::authorizeById( $user->getId() );
		}
		else
		{
			throw new MK_Exception("User with Email $email doesn't exist");
		}
		
	}

	public static function authorizeByWindowsLiveId( $live_id )
	{
		$user_module = MK_RecordModuleManager::getFromType('user');

		$search = array(
			array('field' => 'windows_live_id', 'value' => $live_id),
		);

		$search_results = $user_module->searchRecords( $search );
		!$user = array_pop( $search_results ); 

		if( !empty($user) )
		{
			return self::authorizeById( $user->getId() );
		}
		else
		{
			throw new MK_Exception("User with Windows Live ID $live_id doesn't exist");
		}
		
	}

	public static function authorizeByYahooId( $yahoo_id )
	{
		$user_module = MK_RecordModuleManager::getFromType('user');

		$search = array(
			array('field' => 'yahoo_id', 'value' => $yahoo_id),
		);

		$search_results = $user_module->searchRecords( $search );
		!$user = array_pop( $search_results ); 

		if( !empty($user) )
		{
			return self::authorizeById( $user->getId() );
		}
		else
		{
			throw new MK_Exception("User with Yahoo ID $yahoo_id doesn't exist");
		}
		
	}

	public static function authorizeByFacebookId( $facebook_id )
	{
		$user_module = MK_RecordModuleManager::getFromType('user');

		$search = array(
			array('field' => 'facebook_id', 'value' => $facebook_id),
		);

		$search_results = $user_module->searchRecords( $search );
		!$user = array_pop( $search_results ); 

		if( !empty($user) )
		{
			return self::authorizeById( $user->getId() );
		}
		else
		{
			throw new MK_Exception("User with Facebook ID $facebook_id doesn't exist");
		}
		
	}

	public static function authorizeByWordpressId( $wordpress_id )
	{
		$user_module = MK_RecordModuleManager::getFromType('user');

		$search = array(
			array('field' => 'wordpress_id', 'value' => $wordpress_id),
		);

		$search_results = $user_module->searchRecords( $search );
		!$user = array_pop( $search_results ); 

		if( !empty($user) )
		{
			return self::authorizeById( $user->getId() );
		}
		else
		{
			throw new MK_Exception("User with Wordpress ID $wordpress_id doesn't exist");
		}
		
	}
	
	public static function authorizeByTwitterId( $twitter_id )
	{
		$user_module = MK_RecordModuleManager::getFromType('user');

		$search = array(
			array('field' => 'twitter_id', 'value' => $twitter_id),
		);

		$search_results = $user_module->searchRecords( $search );
		$user = array_pop( $search_results );

		if( !empty($user) )
		{
			return self::authorizeById( $user->getId() );
		}
		else
		{
			throw new MK_Exception("User with Twitter ID $twitter_id, doesn't exist");
		}
		
	}

	public static function authorizeByLinkedInId( $linkedin_id )
	{
		$user_module = MK_RecordModuleManager::getFromType('user');

		$search = array(
			array('field' => 'linkedin_id', 'value' => $linkedin_id),
		);

		$search_results = $user_module->searchRecords( $search );
		$user = array_pop( $search_results );

		if( !empty($user) )
		{
			return self::authorizeById( $user->getId() );
		}
		else
		{
			throw new MK_Exception("User with Google ID $linkedin_id, doesn't exist");
		}
		
	}

	public static function authorizeByGoogleId( $google_id )
	{
		$user_module = MK_RecordModuleManager::getFromType('user');

		$search = array(
			array('field' => 'google_id', 'value' => $google_id),
		);

		$search_results = $user_module->searchRecords( $search );
		$user = array_pop( $search_results );

		if( !empty($user) )
		{
			return self::authorizeById( $user->getId() );
		}
		else
		{
			throw new MK_Exception("User with Google ID $google_id, doesn't exist");
		}
		
	}

	public static function authorizeById( $id )
	{
		$config = MK_Config::getInstance();

		$user_module = MK_RecordModuleManager::getFromType('user');
		
		try
		{
			self::$user = MK_RecordManager::getFromId( $user_module->getId(), $id );
			self::$user
				->setLastip( MK_Utility::getUserIp() )
				->setLastLogin( date('Y-m-d H:i:s') )
				->setTemporaryPassword('')
				->save(false);
		}
		catch(Exception $e){}

		return self::authorize();

	}
	
	public static function authorize()
	{
		
		if( empty(self::$user) )
		{
			$user_module = MK_RecordModuleManager::getFromType('user');
			self::$user = MK_RecordManager::getNewRecord( $user_module->getId() );
		}

		return self::$user;

	}
	
}

?>