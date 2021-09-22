<?php

class MK_Form_Field_Currency extends MK_Form_Field_Text{
	
	public function __construct($name, $field_data = null){
		
		parent::__construct($name, $field_data);

		if( isset($field_data['value']) )
		{
			if( is_array($field_data['value']) )
			{
				$value = (float) implode('.', $field_data['value']);
			}
			else
			{
				$value = (float) $field_data['value'];
			}
			
			$parsed_value = explode('.', $value);
			$parsed_value_main = !empty($parsed_value[0]) ? $parsed_value[0] : 0;
			$parsed_value_cent = !empty($parsed_value[1]) ? substr($parsed_value[1], 0, 2) : 0;
			
			$value = (float) $parsed_value_main.'.'.str_pad($parsed_value_cent, 2, 0, STR_PAD_LEFT);
		}
		else
		{
			$value = (float) 0;
		}

		$this->setValue($value);
	}

	protected function renderField()
	{
		$parsed_value = explode('.', $this->getValue());
		$parsed_value_main = !empty($parsed_value[0]) ? $parsed_value[0] : 0;
		$parsed_value_cent = !empty($parsed_value[1]) ? substr($parsed_value[1], 0, 2) : 0;
		
		$html = '';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
		$html.= $this->getHtmlPrefix();
		$html.= '<div class="input">';
		$html.= '<p class="float-text">£</p>';
		$html.= '<input'.($this->getAttributes()).' class="data input-text" name="'.$this->getName().'[]" id="'.$this->getName().'_1" value="'.$parsed_value_main.'" />';
		$html.= '<p class="float-text">:</p>';
		$html.= '<input'.($this->getAttributes()).' class="data input-text" name="'.$this->getName().'[]" id="'.$this->getName().'_2" value="'.str_pad($parsed_value_cent, 2, 0, STR_PAD_LEFT).'" />';
		$html.= '</div>';
		$html.= $this->getHtmlSuffix();
		return $html;
	}

}

?>