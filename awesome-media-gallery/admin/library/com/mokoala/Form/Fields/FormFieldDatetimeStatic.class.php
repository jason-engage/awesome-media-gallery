<?php

require_once 'FormFieldDatetime.class.php';

class MK_Form_Field_Datetime_Static extends MK_Form_Field_Datetime{
	
	protected function renderField(){
		$config = MK_Config::getInstance();
		$value = (integer) $this->value;

		$html = '';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
		$html .= '<p class="input-static">'.( !empty($value) && $value > 0 ? date($config->site->datetime_format, $value) : 'None defined' ).'</p>';
		$html .= '<input type="hidden" name="'.$this->getName().'" value="'.$this->getValue().'" />';
		return $html;
	}

}

?>