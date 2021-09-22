<?php

class MK_Form_Field_File extends MK_Form_Field_Abstract{

	protected $value = array();
	protected $file_remove_link;
	protected $upload_path;

	protected $valid_extensions = array();

	public function __construct($name, $field_data = null)
	{

		parent::__construct( $name, $field_data );

		if(!empty($field_data['upload_path']))
		{
			$this->upload_path = $field_data['upload_path'];
		}
		else
		{
			$config = MK_Config::getInstance();
	
			$this->upload_path = $config->site->upload_path;
		}

		if(!empty($field_data['file_remove_link']))
		{
			$this->file_remove_link = $field_data['file_remove_link'];
		}

		if(!empty($field_data['valid_extensions']))
		{
			$this->valid_extensions = $field_data['valid_extensions'];
		}

	}

	public function setValue($post_data = null)
	{
		if(is_string($post_data))
		{
			$this->value['existing'] = $post_data;
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
			return $this->value['new'];
		}
		elseif( !empty($this->value['existing']) )
		{
			return $this->value['existing'];	
		}
		else
		{
			return null;	
		}
	}
	
	public function getFileRemoveLink()
	{

		if( !empty($this->file_remove_link) )
		{
			return $this->file_remove_link;
		}
		
	}
	
	public function process()
	{
		if( !empty($this->value['name']) )
		{

			// Get file extension
			$extension = explode('.', $this->value['name']);
			$extension = array_pop( $extension );
			$extension = strtolower( $extension );

			// If the file's extension is invalid
			if( !empty($this->valid_extensions) && !in_array($extension, $this->valid_extensions) )
			{
				$this->getValidator()->addError("Sorry, <em>".$extension."</em> files are not valid.");
			}
			// If the file is too big
			elseif( !empty($this->value['error']) && $this->value['error'] === UPLOAD_ERR_INI_SIZE)
			{
				$this->getValidator()->addError("Sorry, the uploaded file exceeds the upload_max_filesize directive.");
			}
			elseif( !empty($this->value['error']) && $this->value['error'] === UPLOAD_ERR_FORM_SIZE)
			{
				$this->getValidator()->addError("Sorry, the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
			}
			elseif( !empty($this->value['error']) && $this->value['error'] === UPLOAD_ERR_PARTIAL || $this->value['error'] === UPLOAD_ERR_EXTENSION )
			{
				$this->getValidator()->addError("Sorry, there was an error uploading this file.");
			}
			elseif( !empty($this->value['error']) && $this->value['error'] === UPLOAD_ERR_NO_TMP_DIR)
			{
				$this->getValidator()->addError("Sorry, missing a temporary folder.");
			}
			elseif( !empty($this->value['error']) && $this->value['error'] === UPLOAD_ERR_CANT_WRITE)
			{
				$this->getValidator()->addError("Sorry, failed to write file to disk.");
			}
			elseif( !empty($this->value['error']) && $this->value['error'] === UPLOAD_ERR_OK )
			{

				if(!empty($this->value['existing']))
				{
					try
					{
						$old_image = new MK_File($this->value['existing']);
						$old_image->delete();
					}
					catch(Exception $e){}
				}

				$path = $this->upload_path;
				$file_parts = explode('.', $this->value['name']);
				$ext = strtolower(array_pop($file_parts));
				$filename = slug(implode('.', $file_parts)).'.'.$ext;
				$counter = 0;
				while(is_file($path.$filename))
				{
					$counter++;
					$filename = slug(implode('.', $file_parts)).'_'.$counter.'.'.$ext;
				}
				move_uploaded_file($this->value['tmp_name'], $path.$filename);
				$this->value['new'] = form_data(trim($path.$filename, './'));
			
			}
			
		}
		elseif( array_key_exists('instance', $this->validation) && !empty($this->value['error']) && $this->value['error'] === UPLOAD_ERR_NO_FILE )
		{
			$this->getValidator()->addError("This field cannot be blank.");
		}
	}

	protected function renderField()
	{
		$html = '';
		if( $this->getLabel() )
		{
			$html.= '<label for="'.$this->getName().'">'.$this->getLabel().'</label>';
		}
		$html .= '<div class="file-details">';
		$html .= '<input type="hidden" name="'.$this->getName().'[existing]" value="'.form_data($this->getValue()).'" />';

		if($this->getValue() && is_string($this->getValue()))
		{
			$html.='<p class="input-static">'.$this->getValue().'</p>';
			if($this->getFileRemoveLink())
			{
				$html.='<a class="mini-button mini-button-remove" href="'.$this->getFileRemoveLink().'">Remove file</a>';
			}
		}

		$html .= '<div class="input"><input'.($this->getAttributes()).' type="file" data-valid-extensions="*.'.implode(';*.', $this->valid_extensions).';" name="'.$this->getName().'" id="'.$this->getName().'" /></div>';

		$html .= '</div>';

		return $html;
	}

}

?>