<?php

//print '<h3>Contact</h3>';

//echo ($user_id);
//exit();

if (($_SERVER['REQUEST_METHOD'] != 'POST') ) {

   try {
  
      if (!empty($user_id) ) {
      
        $user_module = MK_RecordModuleManager::getFromType('user');
        $user_record = MK_RecordManager::getFromId( $user_module->getId(), $user_id );
      
        $user_to_email = $user_record->getMetaValue('email');
      
      } else {
      
        throw new Exception(''.$langscape["User not found"].'.');
      
      }
  
  } catch( Exception $e ) {
    
        header('Location: ' . $config->site->url . 'not-found.php', true, 301);
        exit;
    
    }

} else {
  $user_to_email = NULL;
}

$settings = array(
	'attributes' => array(
		'class' => 'clear-fix standard'
	)
);

$structure = array(
	  
	'message' => array(
		'type' => 'textarea',
		//'label' => 'Your Message',
		'validation' => array(
			'instance' => array(),
		),
    'attributes' => array(
      'placeholder' => ''.$langscape["Your Message"].''
		)
	),
	
  'user_to_email' => array(
    'attributes' => array(
      'type' => 'hidden',
    ),
    'value' => $user_to_email
  ),
  
	'send' => array(
		'type' => 'submit',
		'attributes' => array(
			'value' => ''.$langscape["Send Your Message"].'',
      'class' => 'btn-normal btn-primary'
		)
	)
);

$contact_form = new MK_Form($structure, $settings);	

if($contact_form->isSuccessful())
{
	$message = '<p><strong>'.$langscape["From:"].'</strong> '.$user->getDisplayName().'<br><strong>'.$langscape["From Email:"].'</strong><br><a href="' . $config->site->url . $user->getUsername() . '">'.$langscape["Visit their profile"].'</a><br><br>'.nl2br($contact_form->getField("message")->getValue()).'</p>';
	$email = new MK_BrandedEmail();
	$email
		->setSubject($langscape["Someone has contacted you!"])
		->setReplyTo($contact_form->getField('email')->getValue())
		->setMessage($message)
		->send($contact_form->getField('user_to_email')->getValue());
    ?>
	<p class="notice-text"><?php echo $langscape["Thanks your message has been sent."];?></p>
<?php
}
else
{
  print '<h3 class="header">' . $langscape["Contact User"] . '</h3>';
  print $contact_form->render();
  
  }
?>
