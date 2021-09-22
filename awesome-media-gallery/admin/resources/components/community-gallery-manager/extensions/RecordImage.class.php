<?php

class MK_RecordImage extends MK_Record
{
	public function canEdit( MK_RecordUser $user )
	{
		if( $user->isAuthorized() && $this->getUser() == $user->getId() )
		{
			return true;
		}
		
		return parent::canEdit( $user );
	}
	
	public function getTotalComments()
	{
		$image_comment_module = MK_RecordModuleManager::getFromType('image_comment');
		$total = $image_comment_module->getTotalRecords(array(
			array('field' => 'image', 'value' => $this->getId())
		));
	
		return $total;
	}
	
	public function getTotalFavourites()
	{
		$image_favourite_module = MK_RecordModuleManager::getFromType('image_favourite');
		$total = $image_favourite_module->getTotalRecords(array(
			array('field' => 'image', 'value' => $this->getId())
		));
	
		return $total;
	}
	
	public function save( $update_meta = true )
	{
		if( $this->getId() )
		{
			$old_data = $this->build( $this->getId() );
			
			$featured = (boolean) $old_data['featured'];
			
			if( !$featured && $this->isFeatured() )
			{
				$this->setFeaturedDate(date('Y-m-d H:i:s'));
			}
		}

		return parent::save( $update_meta );
		
	}

}

?>