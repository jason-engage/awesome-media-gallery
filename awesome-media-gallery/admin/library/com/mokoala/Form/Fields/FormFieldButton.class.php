<?php

class MK_Form_Field_Button extends MK_Form_Field_Abstract{

	protected function renderField(){
		if(empty($this->attributes['type'])){
			$this->attributes['type'] = 'submit';
		}
		$this->attributes['id'] = $this->getName();
		$this->attributes['name'] = $this->getName();
		$html = '<button'.$this->getAttributes().'>'.$this->attributes['value'].'</button>';
		return $html;
	}

}

?>