<?php

class MK_Form_Field_Checkbox extends MK_Form_Field_Abstract{
	
	public function setValue( $value = null )
	{
		$this->value = (boolean) $value;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	protected function renderField()
	{
		$this->attributes['type'] = 'checkbox';

		if($this->getValue())
		{
			$this->attributes['checked'] = 'checked';
		}

		$html = '';
		
		$html .= '<input'.($this->getAttributes()).' class="data input-checkbox" name="'.$this->getName().'" id="'.$this->getName().'" />';
        
        if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
        
		return $html;
	}

}

?>