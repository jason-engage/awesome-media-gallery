<?php

class MK_Form_Field_Date extends MK_Form_Field_Abstract{
	
	public function setValue($post_value = null){

		if( is_array($post_value) && ( !empty($post_value['m']) && !empty($post_value['d']) && !empty($post_value['y'] )) ){
			$this->value = mktime(12, 12, 00, $post_value['m'], $post_value['d'], $post_value['y']);
		}elseif(is_string($post_value) && !empty($post_value)){
			$this->value = strtotime( $post_value );
		}elseif(!is_array($post_value)){
			$this->value = $post_value;
		}

	}

	public function getValue(){
		return !empty($this->value) ? date('Y-m-d', $this->value) : null;
	}

	protected function renderField()
	{
		$value = (integer) $this->value;
		
		if( $value < 0 )
		{
			$value = 0;
		}

		$name = $this->getName();
		$months = MK_Utility::getMonthList();

		$html = '';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}

		$html.='<select name="'.$this->getName().'[m]" id="'.$this->getName().'_m" class="select-small">';

			$html.='<option selected="selected" value=""></option>';

		foreach($months as $m => $month)
		{
			$html.='<option'.( $value && $m == date('n', $value) ? ' selected="selected"' : null ).' value="'.$m.'">'.$month.'</option>';
		}

		$html.='</select>';

		$html.='<select name="'.$name.'[d]" id="'.$name.'_d" class="select-xxsmall">';

			$html.='<option selected="selected" value=""></option>';

		for( $d=1; $d<=31; $d++ )
		{
			$html.='<option'.( $value && $d == date('j', $value) ? ' selected="selected"' : null ).' value="'.$d.'">'.str_pad($d, 2, "0", STR_PAD_LEFT).'</option>';
		}

		$html.='</select>';

		$html.='<select name="'.$name.'[y]" id="'.$name.'_y" class="select-xsmall">';


			$html.='<option selected="selected" value=""></option>';

		for( $y = date('Y') - 100; $y <= date('Y') + 1; $y++ )
		{
			$html.='<option'.( $value && $y == date('Y', $value) ? ' selected="selected"' : null ).' value="'.$y.'">'.$y.'</option>';
		}

		$html.='</select>';
		return $html;
	}
	
}

?>