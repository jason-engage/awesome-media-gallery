<?php

class MK_Form_Field_Link extends MK_Form_Field_Abstract{

	protected $text;

	public function __construct($name, $field_data = null){
		
		parent::__construct($name, $field_data);
		
		if( !empty($field_data['text']) ){
			$this->text = $field_data['text'];
		}
    
    if( !empty($field_data['icon']) ){
			$this->html_icon = $field_data['icon'];
		}

	}
	
	public function getText(){
		return $this->text;
	}
  
  public function getIcon(){
		return $this->html_icon;
	}

	protected function renderField(){
    
    
		$html = '<a'.($this->getAttributes()).'>'. $this->html_icon . $this->getText().'</a>';
		return $html;
	}

}

?>