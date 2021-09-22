<?php

//print '<h3>Contact</h3>';

$settings = array(
	'attributes' => array(
		'class' => 'clear-fix standard'
	)
);

$structure = array(
	'email' => array(
		//'label' => 'Your Email',
		'validation' => array(
			'email' => array(),
			'instance' => array()
		),
    'attributes' => array(
      'placeholder' => ''.$langscape["Your Email Address"].''
		)
	),
	'name' => array(
		//'label' => 'Your Name',
		'validation' => array(
			'instance' => array(),
		),
    'attributes' => array(
      'placeholder' => ''.$langscape["Your Name"].''
		)
	),
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
	$message = "<p><strong>" . $langscape["Name:"] . "</strong> ".$contact_form->getField('name')->getValue()."<br><strong>" . $langscape["Email:"]. "</strong> ".$contact_form->getField('email')->getValue()."<br><br>".nl2br($contact_form->getField('message')->getValue())."</p>";
	$email = new MK_BrandedEmail();
	$email
		->setSubject($langscape["Email Us"])
		->setReplyTo($contact_form->getField('email')->getValue())
		->setMessage($message)
		->send($config->site->email);
?>
	<p class="notice-text"><?php echo $langscape["Thanks for messaging us"];?></p>
<?php
}
else
{
	print '<h3 class="header">' . $langscape["Email Us"] . '</h3>' . $contact_form->render();
}
?>
