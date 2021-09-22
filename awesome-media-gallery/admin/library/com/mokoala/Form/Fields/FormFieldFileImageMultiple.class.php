<?php

require_once 'FormFieldFileImage.class.php';

class MK_Form_Field_File_Image_Multiple extends MK_Form_Field_File_Image
{

	protected $value = array();

	public function setValue($post_data = null)
	{
		if(is_string($post_data))
		{
			$this->value['existing'] = explode(',', $post_data);
		}
		elseif(is_array($post_data))
		{
			$this->value = array_merge_replace( $this->value, $post_data );
		}

	}

	public function getValue()
	{
		if( !empty($this->value['new']) )
		{
			$return = $this->value['new'];
		}
		elseif( !empty($this->value['existing']) )
		{
			$return = $this->value['existing'];	
		}
		else
		{
			$return = array();	
		}

		$return = is_string($return) ? explode(',', $return) : $return;
		$return = array_filter($return);

		return $return;
	}

	protected function renderField()
	{

		$html = '';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
		$html .= '<div class="file-details">';

		$html .= '<div class="input">';
		$html .= '<input'.($this->getAttributes()).' type="file" name="'.$this->getName().'" id="'.$this->getName().'" />';

		if( count($this->getValue()) > 0 )
		{
			foreach( $this->getValue() as $file_name )
			{
				$html .= '<div class="input-single">';
				$html .= '<img class="preview-image" src="library/thumb.php?f='.form_data($file_name).'w=200&h=200&m=contain" /><button type="button" class="close"></button>';
				$html .= '<input type="hidden" name="'.$this->getName().'[existing][]" value="'.form_data($file_name).'" />';
				$html .= '</div>';
			}
		}
		else
		{
			$html .= '<input type="hidden" name="'.$this->getName().'[existing][]" value="" />';
		}

		$html .= '</div>';
		$html .= '</div>';

		return $html;

	}

}

?>