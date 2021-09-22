<?php

require_once 'FormFieldFile.class.php';

class MK_Form_Field_File_Image extends MK_Form_Field_File
{

	protected $valid_extensions = array(
		'jpg', 'jpeg', 'jpe', 'jif', 'jfif', 'jfi', // JPEG
		'gif', // GIF
		'png' // PNG
	);

	protected function renderField()
	{

		$html = '';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
		$html .= '<div class="file-details">';

		if($this->getValue() && is_string($this->getValue()))
		{
			if($this->getFileRemoveLink())
			{
				$html.='<a class="mini-button mini-button-remove" href="'.$this->getFileRemoveLink().'">Remove file</a>';
			}
		}

		if($this->getValue() && is_string($this->getValue()))
		{
			$html.='<img class="preview-image" src="library/thumb.php?f='.form_data($this->getValue()).'&w=200&h=200&m=contain" />';
		}

		$html .= '<input type="hidden" class="data" data-value="'.MK_Utility::escapeText($this->getValue()).'" name="'.$this->getName().'[existing]" value="'.MK_Utility::escapeText($this->getValue()).'" />';
	
		$html.= '<div class="input">';
		$html.= '<input'.( $this->getAttributes()).' type="file" data-valid-extensions="*.'.implode(';*.', $this->valid_extensions ).';" name="'.$this->getName().'" id="'.$this->getName().'" />';
		$html.= '</div>';
		$html .= '</div>';

		return $html;

	}

}

?>