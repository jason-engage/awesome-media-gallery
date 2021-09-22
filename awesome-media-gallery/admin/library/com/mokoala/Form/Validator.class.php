<?php
class MK_Validator
{
	
	protected $validation_status = true;
	protected $error_list = array();

	protected static $rules = array(
		'array_length' => array('label' => 'Length (Min, Max)', 'arguments' => 2),
		'length' => array('label' => 'Length (Min, Max)', 'arguments' => 2),
		'length_max' => array('label' => 'Max Length (Max)', 'arguments' => 1),
		'length_min' => array('label' => 'Min Length (Min)', 'arguments' => 1),
		'boolean_true' => array('label' => 'Boolean (Must be true)', 'arguments' => 0),
		'integer' => array('label' => 'Integer', 'arguments' => 0),
		'integer_not_zero' => array('label' => 'Integer (Not zero)', 'arguments' => 0),
		'instance' => array('label' => 'Instance', 'arguments' => 1),
		'unique' => array('label' => 'Unique field value', 'arguments' => 0),
		'unique_current' => array('label' => 'Unique field value (For current module)', 'arguments' => 0),
		'confirm' => array('label' => 'Confirm', 'arguments' => 0),
		'email' => array('label' => 'Email address', 'arguments' => 0),
		'url' => array('label' => 'URL', 'arguments' => 0),
		'file_format' => array('label' => 'File Format', 'arguments' => 0),
		'image_format' => array('label' => 'Image Format', 'arguments' => 0)
	);

	public static function getRules()
	{
		return self::$rules;
	}

	public function addError($message)
	{
		$this->validation_status = false;
		array_push($this->error_list, $message);
	}

	public function getErrors()
	{
		$error_list_return = $this->error_list;
		$this->error_list = array();
		return $error_list_return;
	}
	
	public function getStatus()
	{
		return $this->validation_status;
	}

	public function checkLength($string, $args)
	{
		$string = strip_tags($string);
		list($min_characters, $max_characters) = $args;
		$s_len = strlen($string);

		if($s_len > $min_characters && $max_characters+1 > $s_len)
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("Must be between $min_characters and $max_characters characters");
			}
			return false;
		}
	}

	public function checkArrayLength($options, $args)
	{
		$min_options = array_shift($args);
		$max_options = array_shift($args);
		$custom_notification = array_shift($args);

		$options_len = count($options);

		if($options_len >= $min_options && $max_options >= $options_len)
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError($custom_notification ? $custom_notification : "Please choose between $min_options and $max_options options");
			}
			return false;
		}
	}

	public function checkLengthMax($string, $args)
	{
		$string = strip_tags($string);
		list($max_characters) = $args;
		$s_len = strlen($string);

		if($max_characters >= $s_len)
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("Must be no more than $max_characters characters in length");
			}
			return false;
		}
	}

	public function checkLengthMin($string, $args)
	{
		$string = strip_tags($string);
		list($min_characters) = $args;
		$s_len = strlen($string);
		if($s_len >= $min_characters)
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("Must be at least $min_characters characters in length");
			}
			return false;
		}
	}

	public function checkBooleanTrue($string, $args)
	{
		$outcome = (boolean) $string;
		
		if($outcome === true)
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("The field must be set");
			}
			return false;
		}
	}

	public function checkInstance($string, $args)
	{
		$s_len = strlen($string);
		$prompt = array_pop($args);

		if($s_len > 0)
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError($prompt ? $prompt : "This field cannot be blank");
			}
			return false;
		}
	}

	public function checkInteger($int)
	{
		if(is_numeric($int))
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("This field must be a number");
			}
			return false;
		}
	}

	public function checkIntegerNotZero($int)
	{
		if(is_numeric($int) && $int > 0)
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("This field cannot be blank");
			}
			return false;
		}
	}

	public function checkUnique($string, $args)
	{
		$config = MK_Config::getInstance();
		
		$module = array_pop($args);
		$field = array_pop($args);
		$record = array_pop($args);
		
		if ($field->getName() == "username") {
			$t_string = iconv('UTF-8', 'ASCII//TRANSLIT', utf8_encode($string));
			$reserved = array("?","video","audio","image","videos","audios","images","gallery","media","medias","order-by","sort-by","members","member","search","blog","privacy","privacy-policy","terms","page","post","about","contact"," ");	
			$t_string = str_replace($reserved, "", $t_string);

			if ($t_string == ''){
				$this->addError("Please use another name");
				return false;
			}
		}
			
						
		$search_criteria = array(
			array('field' => $field->getName(), 'value' => $string)
		);
		
		if($record)
		{
			$search_criteria[] = array('literal' => "`id` <> ".MK_Database::getInstance()->quote($record->getId()));
		}
		
		$records = $module->searchRecords($search_criteria);
		
		if(count($records) === 0)
		{
			return true;
		}
		else
		{
			$this->addError("This ".$field->getLabel()." is already in use");
			return false;
		}
	}

	// Change _post reference
	public function checkUniqueCurrent($string, $args)
	{
		$config = MK_Config::getInstance();

		$module = array_pop($args);
		$field = array_pop($args);
		$record = array_pop($args);

		$search_criteria = array(
			array('field' => $field->getName(), 'value' => $string),
			array('field' => 'module', 'value' => MK_Request::getPost('module')),
			array('literal' => "`id` <> ".MK_Database::getInstance()->quote($record->getId())."")
		);
		
		$records = $module->searchRecords($search_criteria);

		if(count($records) === 0)
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("This value is already in use");
			}
			return false;
		}
	}

	public function checkUrl($string)
	{
		if(empty($string) || preg_match("#^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?#i", $string))
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("Must be a valid URL");
			}
			return false;
		}
	}

	public function checkEmail($string)
	{
		if(empty($string) || preg_match('/^(?!(?>\x22?(?>\x22\x40|\x5C?[\x00-\x7F])\x22?){255,})(?!(?>\x22?\x5C?[\x00-\x7F]\x22?){65,}@)(?>[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+|(?>\x22(?>[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|\x5C[\x00-\x7F])*\x22))(?>\.(?>[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+|(?>\x22(?>[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|\x5C[\x00-\x7F])*\x22)))*@(?>(?>(?!.*[^.]{64,})(?>(?>xn--)?[a-z0-9]+(?>-[a-z0-9]+)*\.){0,126}(?>xn--)?[a-z0-9]+(?>-[a-z0-9]+)*)|(?:\[(?>(?>IPv6:(?>(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){7})|(?>(?!(?:.*[a-f0-9][:\]]){8,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?::(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,6})?)))|(?>(?>IPv6:(?>(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){5}:)|(?>(?!(?:.*[a-f0-9]:){6,})(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4})?::(?>[a-f0-9]{1,4}(?>:[a-f0-9]{1,4}){0,4}:)?)))?(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(?>\.(?>25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}))\]))$/isD', $string))
		{
			return true;
		}
		else
		{
			if( isset($this) )
			{
				$this->addError("Must be a valid email");
			}
			return false;
		}
	}

	public function checkConfirm($string, $args)
	{
		list($string_new_confirm, $string_confirm) = $args;
		if($string_new_confirm != $string_confirm)
		{
			if( isset($this) )
			{
				$this->addError("Fields do not match");
			}
			return false;
		}
		else
		{
			return true;
		}
	}

}
?>