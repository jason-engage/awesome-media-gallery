<?php

class MK_Form_Field_Integer extends MK_Form_Field_Abstract
{

	protected $text;

	public function getValue()
	{
		return is_numeric($this->value) ? $this->value : 0;
	}

}

?>