<?php

class MK_RecordUser extends MK_Record
{
	const TYPE_CORE = 'core';
	const TYPE_FACEBOOK = 'facebook';
	const TYPE_TWITTER = 'twitter';
	const TYPE_YAHOO = 'yahoo';
	const TYPE_WINDOWS_LIVE = 'windows_live';
	const TYPE_GOOGLE = 'google';
	const TYPE_LINKEDIN = 'linkedin';
	const TYPE_WORDPRESS = 'wordpress';

	const FRIENDSHIP_NONE = 'none';
	const FRIENDSHIP_PENDING = 'pending';
	const FRIENDSHIP_PENDING_YOU = 'pending_you';
	const FRIENDSHIP_ACCEPTED = 'accepted';
	const FRIENDSHIP_INVALID = 'invalid';

	public function __construct( $module_id, $record_id = null ){
		
		parent::__construct( $module_id, $record_id );
		
		if(empty($record_id))
		{
			$group_type = MK_RecordModuleManager::getFromType('user_group');
			$search = array(
				array('field' => 'default_value', 'value' => '1')
			);
			$group = $group_type->searchRecords($search);
			$group = array_pop( $group );
			$this
				->setGroup($group->getId())
				->setDateRegistered(date('Y-m-d H:i:s'))
				->setType( self::TYPE_CORE );
		}
		else
		{
			$user_meta_type = MK_RecordModuleManager::getFromType('user_meta');
			$search_criteria = array(
				array('field' => 'user', 'value' => $record_id)
			);
			$user_meta = $user_meta_type->searchRecords($search_criteria);

			foreach($user_meta as $meta)
			{
				$this->setMetaValue($meta->getKey(), $meta->getValue());
			}			
		}
		
	}
    
    public function getTotalUserImages($type_gallery = NULL)
	{
		$image_table = MK_Database::getTableName( 'images' );

        if(!empty($type_gallery)) {
            $pre_record = MK_Database::getInstance()->prepare("SELECT COUNT(*) AS total_images FROM `$image_table` WHERE `user` = :id AND `type_gallery` = $type_gallery");
        } else {
            $pre_record = MK_Database::getInstance()->prepare("SELECT COUNT(*) AS total_images FROM `$image_table` WHERE `user` = :id ");
        }

		$pre_record->bindValue(':id', $this->getId(), PDO::PARAM_INT);
		$pre_record->execute();

		$res_record = $pre_record->fetch( PDO::FETCH_ASSOC );
		
		return $res_record['total_images'];
	}



	public function getTotalReceivedViews()
	{
		$image_table = MK_Database::getTableName( 'images' );

		$pre_record = MK_Database::getInstance()->prepare("SELECT SUM(`views`) AS total_views FROM `$image_table` WHERE `user` = :id");

		$pre_record->bindValue(':id', $this->getId(), PDO::PARAM_INT);
		$pre_record->execute();

		$res_record = $pre_record->fetch( PDO::FETCH_ASSOC );
		
		return $res_record['total_views'];
	}

	public function getTotalReceivedFavourites()
	{
		$image_table = MK_Database::getTableName( 'images' );
		$image_comment_table = MK_Database::getTableName( 'images_favourites' );

		$pre_record = MK_Database::getInstance()->prepare("SELECT COUNT(*) AS total_comments FROM `$image_table` AS image, `$image_comment_table` AS image_comment WHERE image.user = :id AND image_comment.image = image.id");

		$pre_record->bindValue(':id', $this->getId(), PDO::PARAM_INT);
		$pre_record->execute();

		$res_record = $pre_record->fetch( PDO::FETCH_ASSOC );
		
		return $res_record['total_comments'];
	}

	public function getTotalReceivedComments()
	{
		$image_table = MK_Database::getTableName( 'images' );
		$image_comment_table = MK_Database::getTableName( 'images_comments' );

		$pre_record = MK_Database::getInstance()->prepare("SELECT COUNT(*) AS total_comments FROM `$image_table` AS image, `$image_comment_table` AS image_comment WHERE image.user = :id AND image_comment.image = image.id");

		$pre_record->bindValue(':id', $this->getId(), PDO::PARAM_INT);
		$pre_record->execute();

		$res_record = $pre_record->fetch( PDO::FETCH_ASSOC );
		
		return $res_record['total_comments'];
	}

	public function setPassword($value)
	{
		$current_value = $this->getPassword();
		if( ($current_value !== $value) || (empty($current_value)) )
		{
			$value = MK_Utility::getHash($value);
			$this->setMetaValue('password', $value);
		}
		return $this;
	}

	public function setTemporaryPassword($value)
	{
		$current_value = $this->getPassword();
		if( $current_value !== $value)
		{
			$value = MK_Utility::getHash($value);
			$this->setMetaValue('temporary_password', $value);
		}
		return $this;
	}

	public function canEdit( MK_RecordUser $user )
	{
		
		if( $this->getId() == $user->getId() )
		{
			return true;
		}
		else
		{
			return parent::canEdit( $user );
		}
	}
	public function canDelete( MK_RecordUser $user )
	{
		if( $this->getId() == $user->getId() )
		{
			return false;
		}
		else
		{
			return parent::canDelete( $user );
		}
	}
	
	public function isAuthorized()
	{
		if( $this->getId() )
		{
			return true;
		}
		else
		{
			return false;	
		}
	}

	public function isFollowing( MK_RecordUser $following )
	{
		$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');
		$_following = $user_follower_module->searchRecords(array(
			array( 'field' => 'follower', 'value' => $this->getId() ),
			array( 'field' => 'following', 'value' => $following->getId() )
		));
		
		if( count($_following) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function isFollower( MK_RecordUser $follower )
	{
		$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');
		$_follower = $user_follower_module->searchRecords(array(
			array( 'field' => 'follower', 'value' => $follower->getId() ),
			array( 'field' => 'following', 'value' => $this->getId() )
		));
		
		if( count($_follower) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function follow( MK_RecordUser $user )
	{
		if( $user->getId() != $this->getId() && !$this->isFollowing( $user ) )
		{
			$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');
			$user_following = MK_RecordManager::getNewRecord( $user_follower_module->getId() );
			$user_following
				->setFollower( $this->getId() )
				->setFollowing(  $user->getId() )
				->save();
		}
		
		return $this;
	}

	public function unfollow( MK_RecordUser $user )
	{
		$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');
		$_following = $user_follower_module->searchRecords(array(
			array( 'field' => 'follower', 'value' => $this->getId() ),
			array( 'field' => 'following', 'value' => $user->getId() )
		));
		
		if( $_following = array_pop($_following) )
		{
			$_following->delete();
		}

		return $this;
	}

	public function getTotalFollowers( )
	{
		$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');

		$total_followers = $user_follower_module->getTotalRecords(array(
			array('field' => 'following', 'value' => $this->getId())
		));

		return $total_followers;
	}

	public function getFollowers( MK_Paginator &$paginator = null )
	{
		$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');

		$followers = array();
		$_followers = $user_follower_module->searchRecords(array(
			array('field' => 'following', 'value' => $this->getId())
		), !empty($paginator) ? $paginator : null);
		
		foreach( $_followers as $_follower )
		{
			$followers[] = $_follower->objectFollower();
		}
		
		return $followers;
	}

	public function getTotalFavourites( )
	{
	
		$user_fav = MK_RecordModuleManager::getFromType('image_favourite');

		$total_fav = $user_fav->getTotalRecords(array(
			array('field' => 'user', 'value' => $this->getId())
		));

		return $total_fav;
	}
	
	public function getTotalFollowing( )
	{
		$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');

		$total_following = $user_follower_module->getTotalRecords(array(
			array('field' => 'follower', 'value' => $this->getId())
		));

		return $total_following;
	}

	public function getFollowing( MK_Paginator &$paginator = null )
	{
		$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');

		$following = array();
		$_following = $user_follower_module->searchRecords(array(
			array('field' => 'follower', 'value' => $this->getId())
		), !empty($paginator) ? $paginator : null);
		
		foreach( $_following as $_follower )
		{
			$following[] = $_follower->objectFollowing();
		}
		
		return $following;
		
	}

	public function removeFriend( MK_RecordUser $friend )
	{
		$status = $this->getFriendshipStatus( $friend );
		
		$user_friendships_module = MK_RecordModuleManager::getFromType('user_friendship');
		if( $status == self::FRIENDSHIP_PENDING_YOU || $status == self::FRIENDSHIP_ACCEPTED )
		{
			$friendship = $user_friendships_module->searchRecords(array(
				array('literal' => " (`user` = '".$this->getId()."' AND `friend` = '".$friend->getId()."') OR (`friend` = '".$this->getId()."' AND `user` = '".$friend->getId()."') "),
			));
			$friendship = array_pop($friendship);
			$friendship->delete();
		}

		return $this;
	}

	public function addFriend( MK_RecordUser $friend )
	{
		$status = $this->getFriendshipStatus( $friend );
		
		$user_friendships_module = MK_RecordModuleManager::getFromType('user_friendship');
		if( $status == self::FRIENDSHIP_PENDING_YOU )
		{
			// Ensure that users aren't both following one another and friends
			$friend->unfollow( $this );
			$this->unfollow( $friend );

			$friendship = $user_friendships_module->searchRecords(array(
				array('literal' => " (`user` = '".$this->getId()."' AND `friend` = '".$friend->getId()."') OR (`friend` = '".$this->getId()."' AND `user` = '".$friend->getId()."') "),
			));

			$friendship = array_pop($friendship);
			$friendship
				->isAccepted( true )
				->save();
		}
		elseif( $status == self::FRIENDSHIP_NONE )
		{
			$friendship = MK_RecordManager::getNewRecord( $user_friendships_module->getId() );
			$friendship
				->setUser( $this->getId() )
				->setFriend( $friend->getId() )
				->save();
		}
		
		return $this;
	}

	public function getFriendshipStatus( MK_RecordUser $friend )
	{
		$user_friendships_module = MK_RecordModuleManager::getFromType('user_friendship');
		$friends = $user_friendships_module->searchRecords(array(
			array('literal' => " (`user` = '".$this->getId()."' AND `friend` = '".$friend->getId()."') OR (`friend` = '".$this->getId()."' AND `user` = '".$friend->getId()."') "),
		));
		$friends = array_pop($friends);
		
		if( $friend->getId() == $this->getId() )
		{
			return self::FRIENDSHIP_INVALID;
		}
		elseif( !empty($friends) )
		{
			if( $friends->isAccepted() )
			{
				return self::FRIENDSHIP_ACCEPTED;
			}
			elseif( $friends->getFriend() == $this->getId() )
			{
				return self::FRIENDSHIP_PENDING_YOU;
			}
			else
			{
				return self::FRIENDSHIP_PENDING;
			}
		}
		else
		{
			return self::FRIENDSHIP_NONE;	
		}
	}

	public function getTotalFriends()
	{
		$user_friendships_module = MK_RecordModuleManager::getFromType('user_friendship');

		$total_friends = $user_friendships_module->getTotalRecords(array(
			array('literal' => " (`user` = '".$this->getId()."' OR `friend` = '".$this->getId()."') "),
			array('field' => 'accepted', 'value' => 1)
		));
		
		return $total_friends;
	}

	public function getFriends( MK_Paginator &$paginator = null )
	{
		$user_friendships_module = MK_RecordModuleManager::getFromType('user_friendship');

		$friends = $user_friendships_module->searchRecords(array(
			array('literal' => " (`user` = '".$this->getId()."' OR `friend` = '".$this->getId()."') "),
			array('field' => 'accepted', 'value' => 1)
		), !empty($paginator) ? $paginator : null);
		
		$friend_list = array();
		foreach( $friends as $friendship )
		{
			if( $friendship->getUser() == $this->getId() )
			{
				$friend_list[] = $friendship->objectFriend();
			}
			else
			{
				$friend_list[] = $friendship->objectUser();
			}
		}
		
		return $friend_list;
		
	}
	
	public function getFriendsRequests( MK_Paginator &$paginator = null )
	{
		$user_friendships_module = MK_RecordModuleManager::getFromType('user_friendship');

		$friends = $user_friendships_module->searchRecords(array(
			array('literal' => " `friend` = '".$this->getId()."' "),
			array('field' => 'accepted', 'value' => 0)
		), !empty($paginator) ? $paginator : null );
		
		$friend_list = array();
		foreach( $friends as $friendship )
		{
			$friend_list[] = $friendship->objectUser();
		}
		
		return $friend_list;
		
	}

	public function getTotalFriendsRequests( )
	{
		$paginator = new MK_Paginator(1, 1);
		$user_friendships_module = MK_RecordModuleManager::getFromType('user_friendship');

		$friends = $user_friendships_module->searchRecords(array(
			array('literal' => " `friend` = '".$this->getId()."' "),
			array('field' => 'accepted', 'value' => 0)
		), $paginator );
		
		return $paginator->getTotalRecords();
	}
	
	public function clearUnreadActivity()
	{
		$user_notification_module = MK_RecordModuleManager::getFromType('user_notification');
		$user_notification_table = MK_Database::getTableName( $user_notification_module->getTable() );

		$pre_fields = MK_Database::getInstance()->prepare("UPDATE `$user_notification_table` SET `unread` = 0 WHERE `user` = :user");
		$pre_fields->bindValue(':user', $this->getId(), PDO::PARAM_INT);
		$pre_fields->execute();

		return $this;
	}
	
	public function getPublicActivity( MK_Paginator &$paginator = null )
	{
		$user_notification_module = MK_RecordModuleManager::getFromType('user_notification');

		$activity_feed = $user_notification_module->searchRecords(array(
			array('field' => 'user', 'value' => $this->getId()),
			array('field' => 'public', 'value' => 1)
		), $paginator ? $paginator : null);
		
		return $activity_feed;
	}
	
	public function getPrivateActivity( $unread_only = false, MK_Paginator &$paginator = null )
	{
		$following = $this->getFollowing();
		$friends = $this->getFriends();
		
		$following_full = array();
		
		foreach( $following as $following_single )
		{
			$following_full[$following_single->getId()] = $following_single->getId();
		}
		
		foreach( $friends as $friends_single )
		{
			$following_full[$friends_single->getId()] = $friends_single->getId();
		}

        $following_full[$this->getId()] = $this->getId(); //Add own user to array to merge activity streams.
        
		$user_notification_module = MK_RecordModuleManager::getFromType('user_notification');

		if( $unread_only )
		{
			$user_notification_criteria = array(
				array( 'field' => 'user', 'value' => $this->getId() ),
				array( 'field' => 'public', 'value' => 0 ),
				array( 'field' => 'unread', 'value' => 1 )
			);
		}
		else
		{
			$user_notification_criteria = array(
				array(
					'literal' => "( (`public` = 0 AND `user` = ".$this->getId().")".( !empty($following_full) ? " OR ( `public` = 1 AND `user` IN (".implode(',', $following_full).")  )" : "")." )"
				)
			);
		}

		$activity_feed = $user_notification_module->searchRecords($user_notification_criteria, $paginator ? $paginator : null);
		
		return $activity_feed;
	}
	
	public function sendAdminEmail()
	{
		$config = MK_Config::getInstance();
		$subject = $config->site->emails->registration_subject_admin;
		$message = $config->site->emails->registration_text_admin;

		$message = str_replace(
			array( '{user_profile_link}', '{user_display_name}' ),
			array( MK_Utility::serverUrl($this->getUsername()), $this->getDisplayName() ),
			$message
		);

		if ($config->site->members->enable_approval) {
			$message .= $config->site->emails->registration_approval_notice_admin;
		}
		
		$email = new MK_BrandedEmail();
		$email
			->setSubject($subject)
			->setMessage($message)
			->send( $config->site->email );
	}
	
	public function sendSignUpEmail()
	{
		$config = MK_Config::getInstance();
		
		$subject = $config->site->emails->registration_subject;
		$subject = str_replace('{site_name}', $config->site->name, $subject);
		
		$message = $config->site->emails->registration_text;
		$message = str_replace(
			array( '{user_display_name}', '{site_domain}' ),
			array( $this->getDisplayName(), $config->site->url ),
			$message
		);
		
		if ($config->site->members->enable_approval) {
			$message .= $config->site->emails->registration_approval_notice;
		}
		
		$email = new MK_BrandedEmail();
		$email
			->setSubject($subject)
			->setMessage($message)
			->send( $this->getEmail() );
	}

	public function sendUploadEmailAdmin()
	{
		
		$config = MK_Config::getInstance();
		
		if ($config->site->media->enable_approval) {
			
			$subject = 'There are new uploads needing approval';
			$message = 'Goto <a href="' . $config->site->url . 'order-by/queue">' . $config->site->url . 'queue</a> to approve the new content.';
		
		} else {
		
			$subject = 'There are new uploads';
			$message = 'Your site has new uploads! <a href="' . $config->site->url . '">Check em out</a>';
		
		}
		
		//$subject = $config->site->emails->upload_subject_admin;
		//$subject = str_replace('{site_name}', $config->site->name, $subject);
		
		//$message = $config->site->emails->upload_text_admin;
		//$message = str_replace(
		//	array( '{user_display_name}', '{site_domain}' ),
		//	array( $this->getDisplayName(), $config->site->url ),
		//	$message
		//);
		
		//if ($config->site->members->enable_approval) {
		//	$message .= $config->site->emails->registration_approval_notice;
		//}
		
		$email = new MK_BrandedEmail();
		$email
			->setSubject($subject)
			->setMessage($message)
			->send( $config->site->email );
	}

	public function sendApprovedEmail($uid)
	{
		
		$config = MK_Config::getInstance();
		$user_module = MK_RecordModuleManager::getFromType('user');
		$approved_user = MK_RecordManager::getFromId( $user_module->getId(), $uid );
		
		$subject = $config->site->emails->approved_subject;
		$subject = str_replace(
			array( '{user_display_name}', '{site_domain}' ),
			array( $approved_user->getDisplayName(), $config->site->name ),
			$subject
		);
	
		$message = $config->site->emails->approved_text;
		$message = str_replace(
			array( '{user_display_name}', '{site_domain}', '{user_profile_link}', '{site_name}' ),
			array( $approved_user->getDisplayName(), $config->site->url, MK_Utility::serverUrl($approved_user->getUsername()) , $config->site->name),
			$message
		);
				
		$email = new MK_BrandedEmail();
		$email
			->setSubject($subject)
			->setMessage($message)
			->send( $approved_user->getEmail() );
	}

	public function sendVerificationEmail()
	{
		$config = MK_Config::getInstance();
		
		if(!$verification_code = $this->getVerificationCode())
		{
			$verification_code = MK_Utility::getRandomString(20);
			$this->setVerificationCode($verification_code);
		}

		$verification_link = MK_Utility::serverUrl( "sign-up.php?verification_code=".urlencode($verification_code) );
		
		$message = $config->site->email_text_verification;
		$message = str_replace(
			array('{user_display_name}', '{verification_link}'),
			array($this->getDisplayName(), $verification_link),
			$message
		);

		$email = new MK_BrandedEmail();
		$email
			->setSubject('Verify Email Address')
			->setMessage('<p>Dear '.$this->getDisplayName().',<br /><br />Thank you for registering. Click the link below (or copy it into your browser) to verify your email address:<br /><a href="'.$verification_link.'">'.$verification_link.'</a>.</p>')
			->send($this->getEmail());
	}
	
	public function addNotification( $text, $public = true, MK_RecordUser $related_user = null, $type = null )
	{
		$user_notification_module = MK_RecordModuleManager::getFromType('user_notification');
		$new_notification = MK_RecordManager::getNewRecord( $user_notification_module->getId() );
		$new_notification
			->isPublic( $public )
			->isUnread( !$public )
			->setType( $type )
			->setUser( $this->getId() )
			->setRelatedUser( $related_user ? $related_user->getId() : $this->getId() )
			->setText( $text )
			->save();

		return $this;
	}
	
	public function save( $update_meta = true )
	{
		$config = MK_Config::getInstance();
		$new_user = !$this->getId();

		if( !$this->getUsername() )
		{
			$user_name = str_replace(" ", "", $this->getDisplayName() );
			$user_name = strtolower( $user_name );
			$this->setUsername($user_name);
		}
				
		if( !$this->getId() && !$config->extensions->core->email_verification )
		{
			$this
				->isEmailVerified(1)
				->sendSignUpEmail();
		}

		if( !$this->getId() && !$this->isEmailVerified() && $config->extensions->core->email_verification && $this->getType() == self::TYPE_CORE )
		{
			$this->sendVerificationEmail();
		}
		elseif( $this->getType() != self::TYPE_CORE )
		{
			$this->isEmailVerified(1);
		}

		parent::save( $update_meta );

		if( $new_user  && !empty($config->extensions->core->email_admin_signup) && $config->extensions->core->email_admin_signup )
		{
			$this->sendAdminEmail();

			$action_log_module = MK_RecordModuleManager::getFromType('action_log');
			$new_logged_action = MK_RecordManager::getNewRecord($action_log_module->getId());
			
			$new_logged_action
                ->setUser( $this->getId() )
				->setAction('<a href="?module_path=users/index/method/edit/id/' . $this->getId() . '">' . $this->getDisplayName() . '</a> signed up for an account.')
				->save();
		}

		if( $update_meta === true )
		{
			$user_meta_type = MK_RecordModuleManager::getFromType('user_meta');
	
			$search_criteria = array(
				array('field' => 'user', 'value' => $this->getId())
			);

			$user_meta = $user_meta_type->searchRecords($search_criteria);
			
			foreach($user_meta as $meta)
			{
				$meta->delete();
			}
	
			foreach($this->meta_extra as $key => $value)
			{
				if(!empty($value))
				{
					$new_meta = MK_RecordManager::getNewRecord($user_meta_type->getId());
					$new_meta
						->setKey($key)
						->setValue($value)
						->setUser($this->getId())
						->save( false );
				}
			}
		}

		return $this;	
	}

}

?>