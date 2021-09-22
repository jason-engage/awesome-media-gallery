<?php

class MK_RecordImageFavourite extends MK_Record
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
			$image_favourite_module = MK_RecordModuleManager::getFromType('image_favourite');
	
			$total_favourites = $image_favourite_module->getTotalRecords(array(
				array('field' => 'image', 'value' => $this->getImage())
			));
	
			$this->objectImage()->setTotalFavourites($total_favourites)->save();
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