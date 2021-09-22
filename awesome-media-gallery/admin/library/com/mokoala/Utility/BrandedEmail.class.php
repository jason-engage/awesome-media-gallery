<?php

class MK_BrandedEmail extends MK_Email
{
	
	public function __construct()
	{
		$config = MK_Config::getInstance();
		$this->sender_email = $config->site->email;
		$this->replyto = $config->site->email;
		$this->sender_name = $config->site->name;
	}

	public function getSubject()
	{
		$config = MK_Config::getInstance();
		return '['.$config->site->name.'] '.$this->subject;
	}
	
	public function getMessage()
	{
		$config = MK_Config::getInstance();
		$email_template = $config->site->email_template;
		$template_theme_directory = MK_Utility::serverUrl( 'admin/'.$config->template_theme_directory );

		$message = $this->message;
		
		$message = str_replace('{email_content}', $message, $email_template);

		$message = str_replace('{core_name}', $config->core->name, $message);
		$message = str_replace('{core_url}', $config->core->url, $message);
		
		$message = str_replace('{site_name}', $config->site->name, $message);
		$message = str_replace('{site_template}', $template_theme_directory, $message);

		return $message;
	}

}

?>