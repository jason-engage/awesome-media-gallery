<?php

	if( $user->isAuthorized() && $author->getId() != $user->getId() )
	{
		// Friendship
		$friendship_status = $user->getFriendshipStatus($author);

		// Following
		if( $friendship_status != MK_RecordUser::FRIENDSHIP_ACCEPTED )
		{
			if( $user->isFollowing( $author ) )
			{
				$follow_instance = $user_follower_module->searchRecords(array(
					array('field' => 'follower', 'value' => $user->getId()),
					array('field' => 'following', 'value' => $author->getId())
				));
				$follow_instance = array_pop($follow_instance);
				$following_button = '<a data-follower-object-id="'.$follow_instance->getId().'" data-follower-id="'.$user->getId().'" data-following-id="'.$author->getId().'" class="button" data-hover-text="'.$langscape["Unfollow"].'" data-hover-class="button" href="#" rel="user unfollow"><button class="pure-button pure-button-primary follow-button"><span class="following">'.$langscape["Following"].'</span></button></a>'; 
			} else {
				$following_button = '<a href="#" class="button" data-hover-text="" data-hover-class="button" data-follower-id="'.$user->getId().'" data-following-id="'.$author->getId().'" rel="user follow">
        <button class="pure-button pure-button-primary follow-button"><span>'.$langscape["Follow"].'</span></button></a>';
			}
		}
	} else {
    $following_button = NULL;
  } 

?>