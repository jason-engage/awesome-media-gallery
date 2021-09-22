<?php

require_once 'FormFieldOptionsMultiple.class.php';

class MK_Form_Field_Radio extends MK_Form_Field_Options_Multiple_Abstract{

	public function setValue($value = null)
	{
		$this->value = $value;
		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	protected function renderField(){

		$html = '';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
		$html .= '<div class="form-field-group">';

		foreach($this->options as $value => $option){

			if( !is_array( $option ) ){
				$option = array(
					'title' => $option
				);
			}

			if( $value == $this->value || !empty( $option['disabled'] ) ){
				$option['checked'] = 'checked';
				unset($option['disabled']);
			}
			
			$option['id'] = $this->getName().'_'.slug($option['title']);
			$option['name'] = $this->getName();
			$option['type'] = 'radio';
			$option['value'] = $value;
			
			$html.='<div class="group-option"><input'.$this->getAttributesFromArray( $option ).'><label for="'.$option['id'].'">'.$option['title'].'</label></div>';

		}

		$html .= '</div>';

		return $html;
	}

}

?>