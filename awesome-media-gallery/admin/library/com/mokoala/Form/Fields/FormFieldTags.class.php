<?php

class MK_Form_Field_Tags extends MK_Form_Field_Text{
	
	public function process()
	{
		parent::process();
		
		$value = $this->getValue();

		$value = MK_Utility::getCleanTags($value);

		parent::setValue($value);
		
		return $this;
	}
	
}

?>