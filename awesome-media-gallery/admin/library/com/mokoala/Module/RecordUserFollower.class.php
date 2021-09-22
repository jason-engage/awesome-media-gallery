<?php

class MK_RecordUserFollower extends MK_Record
{
	
	public function canEdit( MK_RecordUser $user )
	{
		if( $user->isAuthorized() && $user->getId() == $this->getFollower() )
		{
			return true;
		}
		
		return parent::canEdit( $user );
	}

}

?>