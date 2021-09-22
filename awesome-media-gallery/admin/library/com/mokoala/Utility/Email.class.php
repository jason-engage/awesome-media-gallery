<?php
class MK_Email{
	
	protected $sender_name;
	protected $sender_email;
	protected $replyto;
	protected $subject;
	protected $message;
	
	public function setSender($email, $name = null)
	{
		$this->sender_email = $email;
		$this->sender_name = $name;
		return $this;
	}
	
	public function setReplyTo($email)
	{
		$this->replyto = $email;
		return $this;
	}
	
	public function setSubject($subject)
	{
		$this->subject = $subject;
		return $this;
	}
	
	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}
	
	public function getSubject()
	{
		return $this->subject;
	}
	
	public function getMessage()
	{
		return $this->message;
	}
	
	public function send($email, $name = null)
	{
		
		$config = MK_Config::getInstance();

		$mail = new PHPMailer(true);
		
		try {
			$mail->From = $this->sender_email;
			$mail->FromName = $this->sender_name;
			$mail->AddAddress($email, $name);
			$mail->AddReplyTo($this->sender_email, $this->sender_name);
			
			$mail->WordWrap = 50;
			$mail->IsHTML(true);
			
			$mail->Subject = $this->getSubject();
			$mail->Body    = $this->getMessage();
			$mail->AltBody = $this->message;
	
			// SEND VIA SSL IF ENABLED
			if ( isset($config->site->emails->ssl_enable) && ($config->site->emails->ssl_enable <> 'no') && !empty($config->site->emails->ssl_server) && !empty($config->site->emails->ssl_username) && !empty($config->site->emails->ssl_password) ) {
				
				$mail->IsSMTP();
	            $mail->SMTPAuth = true;
	            $mail->Port = 465;
	            $mail->SMTPSecure = $config->site->emails->ssl_enable;
	            $mail->Host = $config->site->emails->ssl_server;
	            $mail->Username = $config->site->emails->ssl_username;
	            $mail->Password = $config->site->emails->ssl_password;	
			
			}
			
			$mail->Send();

		} catch (phpmailerException $e) {
		  echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  echo $e->getMessage(); //Boring error messages from anything else!
		}

		return $this;
	}

}

?>