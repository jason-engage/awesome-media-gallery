<?php

class MK_Form_Field_Captcha extends MK_Form_Field_Abstract
{

	public function setValue( $value = null )
	{
		$this->value = $value;
	}

	public function getValue()
	{
		return $this->value;
	}

	protected function renderField()
	{
		$value = $this->getValue();
		$question_first = rand(1, 20);
		$question_second = rand(1, 20);

		$question = $question_first + $question_second;

		if( isset($value['given_answer']) && ( $value['given_answer'] != $value['correct_answer'] ) )
		{
			$this->validator->addError('The answer given was incorrect');
		}

		$html = '';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
		$html .= '<input type="hidden" value="'.$question.'" name="'.$this->getName().'[correct_answer]" />';
		$html .= '<div class="input"><input'.($this->getAttributes()).' class="data input-text" name="'.$this->getName().'[given_answer]" id="'.$this->getName().'" /></div>';
		return $html;
		return $html;
	}

}

?>