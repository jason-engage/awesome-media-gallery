<?php

//Force Wordpress platform is strict login enabled
if ( ($config->site->wordpress->strict_login) && (!empty($platform)) && ($platform != 'wordpress') ) {
	$platform = 'wordpress';
}

//WINDOWS LIVE LOGIN Check if user is logging in with Windows Live
if( $config->site->windowslive->login && $platform == 'windowslive' )
{
	if( $success = $config->windowslive->Process() )
	{
		if( strlen($config->windowslive->authorization_error) )
		{
			header('Location:'.MK_Utility::serverUrl('/'));
			exit;
		}
		elseif(strlen($config->windowslive->access_token))
		{
			$success = $config->windowslive->CallAPI(
				'https://apis.live.net/v5.0/me',
				'GET',
				array(),
				array(
					'FailOnAccessError' => true
				),
				$_user_details
			);
		}

		if( $success = $config->windowslive->Finalize($success) )
		{
			$email = !empty($_user_details->emails->preferred) ? $_user_details->emails->preferred : $_user_details->emails->personal;
			$user_details = array(
				'windows_live_id' => $_user_details->id,
				'email' => $email,
				'display_name' => $_user_details->name,
                'user_name' => generateUsername($_user_details->name)
			);

			try
			{
				MK_Authorizer::authorizeByWindowsLiveId( $user_details['windows_live_id'] );
				$user = MK_Authorizer::authorize();
			}
			// If user hasn't logged in with this Yahoo account
			catch( Exception $e )
			{
				// Check if this user is already registered with their Yahoo email address 
				try
				{
					MK_Authorizer::authorizeByEmail( $user_details['email'] );
					$user = MK_Authorizer::authorize();
					
					if( !$user->getDisplayName() )
					{
						$user->setDisplayName( $user_details['display_name'] );
					}

					$user
						->setWindowsLiveId( $user_details['windows_live_id'] )
						->save();
				}
				catch( Exception $e )
				{
					$user = MK_RecordManager::getNewRecord($user_module->getId());

					$user
						->setEmail( $user_details['email'] )
						->setDisplayName( $user_details['display_name'] )
                        ->setUsername( $user_details['user_name'] )
						->setType( MK_RecordUser::TYPE_WINDOWS_LIVE )
						->setWindowsLiveId( $user_details['windows_live_id'] )
                        ->setCategory( 1 )
						->save();
				}
			}

			$cookie->set('login', $user->getId(), $config->site->user_timeout);
			$session->login = $user->getId();
		}
	}

	header('Location:'.MK_Utility::serverUrl('/'));
	exit;
}



// LINKED-IN LOGIN Check if user is logging in with LinkedIn
if( $config->site->linkedin->login && $platform == 'linkedin' )
{
	if( $success = $config->linkedin->Process() )
	{
		if( strlen($config->linkedin->authorization_error) )
		{
			header('Location:'.MK_Utility::serverUrl('/'));
			exit;
		}
		elseif(strlen($config->linkedin->access_token))
		{
			$success = $config->linkedin->CallAPI(
				'http://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,picture-urls::(original))',
				'GET',
				array(
					'format' => 'json'
				),
				array(
					'FailOnAccessError' => true
				),
				$_user_details
			);
		}

		if( $success = $config->linkedin->Finalize($success) )
		{
			$first_name = !empty($_user_details->firstName) ? $_user_details->firstName : '';
			$last_name = !empty($_user_details->lastName) ? $_user_details->lastName : '';
			$name = trim($first_name.' '.$last_name);
			
			$user_details = array(
				'linkedin_id' => $_user_details->id,
				'email' => $_user_details->emailAddress,
				'display_name' => $name,
                'user_name' => generateUsername($name),
                'picture' => $_user_details->pictureUrls->values[0],
			);
            
            //var_dump($_user_details->pictureUrls->values[0]);
			//die;
            
			if( !empty($user_details['linkedin_id']) && !empty($user_details['email']) && !empty($user_details['display_name']) )
			{
				try
				{
					MK_Authorizer::authorizeByLinkedinId( $user_details['linkedin_id'] );
					$user = MK_Authorizer::authorize();
				}
				// If user hasn't logged in with this LinkedIn account
				catch( Exception $e )
				{
					// Check if this user is already registered with their LinkedIn email address 
					try
					{
						MK_Authorizer::authorizeByEmail( $user_details['email'] );
						$user = MK_Authorizer::authorize();
	
						if( !$user->getDisplayName() )
						{
							$user->setDisplayName( $user_details['display_name'] );
						}
	
						$user
							->setLinkedinId( $user_details['linkedin_id'] )
							->save();
					}
					catch( Exception $e )
					{
                    
                        $user_details['picture'] = MK_FileManager::uploadFileFromUrl( $user_details['picture'], $config->site->upload_path );
                    
						$user = MK_RecordManager::getNewRecord($user_module->getId());
	 
						$user
							->setEmail( $user_details['email'] )
							->setDisplayName( $user_details['display_name'] )
                            ->setUsername( $user_details['user_name'] )
                            ->setAvatar( $user_details['picture'] )
							->setType( MK_RecordUser::TYPE_LINKEDIN )
							->setLinkedinId( $user_details['linkedin_id'] )
                            ->setCategory( 1 )
							->save();
					}
				}
	
				$cookie->set('login', $user->getId(), $config->site->user_timeout);
				$session->login = $user->getId();
			}
		}
	}
	header('Location:'.MK_Utility::serverUrl('/'));
	exit;
}

// YAHOO LOGIN
elseif( $config->site->yahoo->login && $platform == 'yahoo')

{

	if( $success = $config->yahoo->Process() )
	{
		if( strlen($config->yahoo->authorization_error) )
		{
			header('Location:'.MK_Utility::serverUrl('/'));
			exit;
		}
		elseif(strlen($config->yahoo->access_token))
		{
			$success = $config->yahoo->CallAPI(
				'http://query.yahooapis.com/v1/yql', 
				'GET',
				array(
					'q'=>'select * from social.profile where guid=me',
					'format'=>'json'
				),
				array( 'FailOnAccessError' => true ),
				$_user_details
			);
            
		}

		if( $success = $config->yahoo->Finalize($success) )
		{
			$_user_details = $_user_details->query->results->profile;
            
            //if(is_array($_user_details->emails)) { //More than one email address found for Yahoo acount.
            
            if(is_array($_user_details->emails)) {
            
                $yahoo_email = $_user_details->emails[0]->handle; //Assign the first one in the array.
 
            
            } else { //Single email found in yahoo account.
            
            
               $yahoo_email = $_user_details->emails->handle; //Straight up assign it.

            
            }
            
			$user_details = array(
				'yahoo_id' => $_user_details->guid,
				'display_name' => $_user_details->nickname,
                'email' => $yahoo_email,
                'user_name' => generateUsername($_user_details->nickname),
                'picture' => $_user_details->image->imageUrl,
			);

			try
			{
				MK_Authorizer::authorizeByYahooId( $user_details['yahoo_id'] );
				$user = MK_Authorizer::authorize();

				$cookie->set('login', $user->getId(), $config->site->user_timeout);
				$session->login = $user->getId();
			}
			// If user hasn't logged in with this Yahoo account
			catch( Exception $e )
			{
            
            $user_details['picture'] = MK_FileManager::uploadFileFromUrl( $user_details['picture'], $config->site->upload_path );
                
            $user = MK_RecordManager::getNewRecord($user_module->getId());

            $user
                ->setEmail( $user_details['email'] )
                ->setDisplayName( $user_details['display_name'] )
                ->setUsername( $user_details['user_name'] )
                ->setAvatar( $user_details['picture'] )
                ->setType( MK_RecordUser::TYPE_YAHOO )
                ->setYahooId( $user_details['yahoo_id'] )
                ->setCategory( 1 )
                ->save();
            
            
            
				/*$session->registration_details = serialize(
					array(
						'display_name' => $user_details['display_name'],
						'yahoo_id' => $user_details['yahoo_id'],
						'email' => $user_details['email'],
					)
				);*/
			}
		}
	}
    
    $cookie->set('login', $user->getId(), $config->site->user_timeout);
	$session->login = $user->getId();

	//header('Location: '.$login_redirect, true, 302);
    header('Location:'.MK_Utility::serverUrl('/'));
	exit;

	//header('Location:'.MK_Utility::serverUrl('/sign-in.php?platform=yahoo'), true, 302);
	//exit;
}

// FACEBOOK LOGIN - Check if user is logging in with Facebook
elseif( $config->site->facebook->login && ( $facebook_id = $facebook->getUser() ) && MK_Request::getParam('state') && MK_Request::getParam('code'))
{
	$access_token = $facebook->getAccessToken();
	$user_details = $facebook->api('/me');

	// Check if users has already logged in with this facebook account
	try
	{
		MK_Authorizer::authorizeByFacebookId( $user_details['id'] );
		$user = MK_Authorizer::authorize();
	}
	// If user hasn't logged in with this Facebook account
	catch( Exception $e )
	{
		// Check if this user is already registered with their Facebook email address 
		try
		{
			MK_Authorizer::authorizeByEmail( $user_details['email'] );
			$user = MK_Authorizer::authorize();
			
			if( !$user->getDisplayName() )
			{
				$user->setDisplayName( $user_details['display_name'] );
                $user->setUsername( generateUsername($user_details['display_name']) );
			}
			
			if( !$user->getAvatar() )
			{
				$user_details['picture'] = MK_FileManager::uploadFileFromUrl( 'http://graph.facebook.com/'.$user_details['id'].'/picture?type=large', $config->site->upload_path );
				$user->setAvatar( $user_details['picture'] );
			}

			$user
				->setFacebookId( $user_details['id'] )
				->save();
		}
		catch( Exception $e )
		{
			$user_module = MK_RecordModuleManager::getFromType('user');
			$user = MK_RecordManager::getNewRecord($user_module->getId());
			
      //var_dump($user_details);
      //die();
      
			$user_details['picture'] = MK_FileManager::uploadFileFromUrl( 'http://graph.facebook.com/'.$user_details['id'].'/picture?type=large', $config->site->upload_path );
	
			$user
				->setEmail( $user_details['email'] )
				->setDisplayName( $user_details['name'] )
                ->setUsername( generateUsername($user_details['name']) )
				->setAvatar( $user_details['picture'] )
				->setType( MK_RecordUser::TYPE_FACEBOOK )
				->setFacebookId( $user_details['id'] )
                ->setCategory( 1 )
				->save();
		}
	}

	$cookie->set('login', $user->getId(), $config->site->user_timeout);
	$session->login = $user->getId();

	//header('Location: '.$login_redirect, true, 302);
    header('Location:'.MK_Utility::serverUrl('/'));
	exit;
}

// Check if user logged in with Twitter 
elseif( $config->site->twitter->login && $session->twitter_access_token )
{
	$user_details = $config->twitter->get('account/verify_credentials');

    //var_dump($user_details);
    //die;
    
	unset($session->twitter_access_token);
	
	try
	{
		MK_Authorizer::authorizeByTwitterId( $user_details->id );
		$user = MK_Authorizer::authorize();

		$cookie->set('login', $user->getId(), $config->site->user_timeout);
		$session->login = $user->getId();
        
        //echo 'Try to authorize using Twitter';
       // die;
	}
	catch( Exception $e )
	{
		$session->registration_details = serialize(
			array(
				'picture' => str_replace('_normal', '', $user_details->profile_image_url),
				'display_name' => $user_details->name,
				'twitter_id' => $user_details->id,
				'email' => '',
                'username' => generateUsername ( $user_details->name ),
			)
		);
        
       // echo 'Serialize registration details. WITHOUT email address.<br><br>';
        //die;
       // var_dump($session->registration_details);
	}

    //echo '<br><br>Redirect to sign-in with Twitter Platform we need the EMAIL ADDRESS.<br><br>';
    //die;
    
	header('Location: '.MK_Utility::serverUrl('sign-in.php?platform=twitter'), true, 302);
}

// GOOGLE PLUS LOGIN Check if user logged in with Google 
elseif( $config->site->google->login && $platform == 'google' )
{
	if( $success = $config->google->Process() )
	{
		if( strlen($config->google->authorization_error) )
		{
			header('Location:'.MK_Utility::serverUrl('/'));
			exit;
		}
		elseif(strlen($config->google->access_token))
		{
			$success = $config->google->CallAPI(
				'https://www.googleapis.com/oauth2/v1/userinfo',
				'GET',
				array(),
				array( 'FailOnAccessError' => true ),
				$_user_details
			);
            
            
		}

		if( ($success = $config->google->Finalize($success)) && !empty($_user_details) )
		{
        
			$user_details = array(
				'google_id' => $_user_details->id,
				'email' => $_user_details->email,
				'display_name' => $_user_details->name,
                'picture' => (isset($_user_details->picture)) ? $_user_details->picture : '',
                'user_name' => generateUsername($_user_details->name),
			);

			try
			{
				MK_Authorizer::authorizeByGoogleId( $user_details['google_id'] );
				$user = MK_Authorizer::authorize();
			}
			// If user hasn't logged in with this Google account
			catch( Exception $e )
			{
				// Check if this user is already registered with their Google email address 
				try
				{
					MK_Authorizer::authorizeByEmail( $user_details['email'] );
					$user = MK_Authorizer::authorize();
					
					if( !$user->getDisplayName() )
					{
						$user->setDisplayName( $user_details['display_name'] );
                        $user->setUsername( $user_details['user_name'] );
					}
                    
                    if( !$user->getAvatar() )
                    {
                        $user_details['picture'] = MK_FileManager::uploadFileFromUrl( $user_details['picture'], $config->site->upload_path );
                        $user->setAvatar( $user_details['picture'] );
                    }

					$user
						->setGoogleId( $user_details['google_id'] )
						->save();
				}
				catch( Exception $e )
				{
                    $user_details['picture'] = MK_FileManager::uploadFileFromUrl( $user_details['picture'], $config->site->upload_path );
                
					$user = MK_RecordManager::getNewRecord($user_module->getId());

					$user
						->setEmail( $user_details['email'] )
						->setDisplayName( $user_details['display_name'] )
                        ->setUsername( $user_details['user_name'] )
                        ->setAvatar( $user_details['picture'] )
						->setType( MK_RecordUser::TYPE_GOOGLE )
						->setGoogleId( $user_details['google_id'] )
                        ->setCategory( 1 )
						->save();
				}
			}

			$cookie->set('login', $user->getId(), $config->site->user_timeout);
			$session->login = $user->getId();
		
			header('Location: '.MK_Utility::serverUrl('/'), true, 302);
			exit;
		}
		else
		{
			header('Location:'.MK_Utility::serverUrl('/'));
			exit;
		}
	}
	else
	{
		header('Location:'.MK_Utility::serverUrl('/'));
		exit;
	}
}

// Wordpress LOGIN - Check if user is logging in with Wordpress
elseif( $config->site->wordpress->login && $platform == 'wordpress' )
{
	//echo 'step 1';
	if( $success = $config->wordpress->Process() )
	{
		if( strlen($config->wordpress->authorization_error) )
		{
				echo $config->wordpress->authorization_error;

			header('Location:'.MK_Utility::serverUrl('/'));
			exit;
		}
		elseif(strlen($config->wordpress->access_token))
		{
			//echo rtrim($config->site->wordpress->site_url, "/") . '/oauth/me?access_token=' . $config->wordpress->access_token;
			$success = $config->wordpress->CallAPI(
				rtrim($config->site->wordpress->site_url, "/") . '/oauth/me',
				'POST',
				array(
					'format'=>'json'
				),
				array(
					'FailOnAccessError' => true
				),
				$_user_details
			);
		}

		if( $success = $config->wordpress->Finalize($success) )
		{		
			$user_details = array(
				'wordpress_id' => $_user_details->ID,
				'email' => $_user_details->user_email,
				'display_name' => $_user_details->display_name,
                'user_name' => generateUsername($_user_details->display_name),
			);
                        
			if( !empty($user_details['wordpress_id']) && !empty($user_details['email']) && !empty($user_details['display_name']) )
			{
				try
				{
					MK_Authorizer::authorizeByWordpressId( $user_details['wordpress_id'] );
					$user = MK_Authorizer::authorize();
				}
				// If user hasn't logged in with this Wordpress account
				catch( Exception $e )
				{
					// Check if this user is already registered with their Wordpress email address 
					try
					{
						MK_Authorizer::authorizeByEmail( $user_details['email'] );
						$user = MK_Authorizer::authorize();
	
						if( !$user->getDisplayName() )
						{
							$user->setDisplayName( $user_details['display_name'] );
						}
	
						$user
							->setWordpressId( $user_details['wordpress_id'] )
							->save();
					}
					catch( Exception $e )
					{
                    	
                    	$grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $user_details['email'] ) ) ) . "?d=" . urlencode( $config->site->url . $config->site->default_avatar ) . "&s=" . "300";

                        $user_details['picture'] = MK_FileManager::uploadFileFromUrl( $grav_url, $config->site->upload_path );
                    
						$user = MK_RecordManager::getNewRecord($user_module->getId());
	 
						$user
							->setEmail( $user_details['email'] )
							->setDisplayName( $user_details['display_name'] )
                            ->setUsername( $user_details['user_name'] )
                            ->setAvatar( $user_details['picture'] )
							->setType( MK_RecordUser::TYPE_WORDPRESS )
							->setWordpressId( $user_details['wordpress_id'] )
							->setWpAuthorId( $user_details['wordpress_id'] )
                            ->setCategory( 1 )
							->save();
					}
				}
	
				$cookie->set('login', $user->getId(), $config->site->user_timeout);
				$session->login = $user->getId();
			}
		}
	}
	header('Location:'.MK_Utility::serverUrl('/'));
	exit;
}


// OATH LOGIN Check if user completed login with oAuth 
elseif( ($config->site->twitter->login || $config->site->yahoo->login) && !empty($session->registration_details) )
{
	$user_details = unserialize( $session->registration_details );
	if( !empty($user_details['email']) )
	{
	
		$user_module = MK_RecordModuleManager::getFromType('user');
		$user = MK_RecordManager::getNewRecord($user_module->getId());
	
		$user
			->setEmail( $user_details['email'] )
            ->setCategory( 1 )
			->setDisplayName( $user_details['display_name'] );
			

		if( !empty($user_details['picture']) )
		{
			$user_details['picture'] = MK_FileManager::uploadFileFromUrl( $user_details['picture'], $config->site->upload_path );
			$user->setAvatar( $user_details['picture'] );
		}
		
		if( !empty($user_details['twitter_id']) )
		{
			$user
				->setType( MK_RecordUser::TYPE_TWITTER )
				->setTwitterId( $user_details['twitter_id'] );
		}
		elseif( !empty($user_details['yahoo_id']) )
		{
			$user
				->setType( MK_RecordUser::TYPE_YAHOO )
				->setYahooId( $user_details['yahoo_id'] );
		}

		$user->save();

		$cookie->set('login', $user->getId(), $config->site->user_timeout);
		$session->login = $user->getId();
		
		unset($session->registration_details);

		header('Location: '.$login_redirect, true, 302);
		exit;
	}
}
// Check if user hasn't finished logging in with oAuth
elseif( !empty($session->registration_details) && strpos($config->site->page_name, 'sign-in') === false )
{
	echo $langscape["Did not finish sign-in process"];
    die;
    
    header('Location: '.MK_Utility::serverUrl('sign-in.php'), true, 302);
	exit;
}

// If user session has expired but cookie is still active
if( $cookie->login && empty($session->login) )
{
	$session->login = $cookie->login;
}

// Get current user
if( !empty($session->login) )
{
    MK_Authorizer::authorizeById( $session->login );
}

$user = MK_Authorizer::authorize();

?>
