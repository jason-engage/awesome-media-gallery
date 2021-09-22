<?php

require_once 'FormField.class.php';

class MK_Form_Field_Password extends MK_Form_Field_Abstract{

	public function setValue($post_value = null){
		if(is_array($post_value)){

			if( empty($post_value['new_1']) ){
				$this->value = $post_value['existing'];
			}elseif($post_value['new_1'] === $post_value['new_2']){
				$this->value = $post_value['new_1'];
			}else{
				$this->getValidator()->addError('Both fields must match');
			}
			
		}else{
			$this->value = $post_value;
		}

	}

	public function render(){
		return $this->renderField();
	}

	protected function renderField(){

		$this->attributes['type'] = 'password';

		$html = '<div class="'.$this->getClasses().'">';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
    $html .= $this->getErrors();
		$html .= '<div class="input"><input'.($this->getAttributes()).' class="data input-text" name="'.$this->getName().'[new_1]" id="'.$this->getName().'_new_1" /></div>';
		$html .= '</div>';
		$html .= '<div class="'.$this->getClasses().'">';

		if( $this->getLabel() )
		{
			$html.= '<label class="input-low" for="'.$this->getName().'">Confirm '.$this->getLabel().'</label>';
		}
    
    $this->attributes['placeholder'] = 'Confirm Password';
    
		$html .= '<div class="input"><input'.($this->getAttributes()).' class="data input-text" name="'.$this->getName().'[new_2]" id="'.$this->getName().'_new_2" /></div>';
		$html .= '<input type="hidden" value="'.$this->getValue().'" name="'.$this->getName().'[existing]" />';
		$html .= $this->getTooltip();
		
		$html .= '</div>';
		return $html;
	}

}

?>