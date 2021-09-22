<?php

class MK_RecordImageComment extends MK_Record
{
	public function canEdit( MK_RecordUser $user )
	{
		if( $user->isAuthorized() && $this->getUser() == $user->getId() )
		{
			return true;
		}
		
		return parent::canEdit( $user );
	}

	public function recalculate()
	{
		if( $this->getImage() )
		{
			$image_comment_module = MK_RecordModuleManager::getFromType('image_comment');
	
			$total_comments = $image_comment_module->getTotalRecords(array(
				array('field' => 'image', 'value' => $this->getImage())
			));
	
			$this->objectImage()->setTotalComments($total_comments)->save();
		}
	}
	
	public function delete()
	{
		parent::delete();
		$this->recalculate();
	}
	
	public function save( $update_meta = true )
	{
		parent::save( $update_meta );
		$this->recalculate();
	}
}

?>