<?php
/*
$form_structure['extensions-core-email_verification'] = array(
	'label' => 'Email Verification',
	'type' => 'select',
	'options' => array(
		0 => 'No',
		1 => 'Yes'
	),
	'tooltip' => 'Do users need to confirm their email address before they can user their account?',
	'fieldset' => 'User Settings',
	'value' => !empty($config->extensions->core->email_verification) ? $config->extensions->core->email_verification : ''
);*/

$form_structure['extensions-core-email_admin_signup'] = array(
	'label' => 'Email Admin on New Registration',
	'type' => 'select',
	'options' => array(
		0 => 'No',
		1 => 'Yes'
	),
	'tooltip' => 'Should the admin be emailed when a new user registers?',
	'fieldset' => 'Other Admin Settings',
	'value' => !empty($config->extensions->core->email_admin_signup) ? $config->extensions->core->email_admin_signup : ''
);

$form_structure['extensions-core-logout_url'] = array(
	'label' => 'Post-logout URL',
	'tooltip' => 'This is the URL that users will be redirected to after they have logged out.<br>If you leave it blank they will return to the previous page.',
	'fieldset' => 'Other Admin Settings',
	'prefix' => '<p class="input-static">'.$config->site->url.'</p>',
	'value' => !empty($config->extensions->core->logout_url) ? $config->extensions->core->logout_url : ''
);

$form_structure['extensions-core-login_url'] = array(
	'label' => 'Post-login URL',
	'tooltip' => 'This is the URL that users will be redirected to after they have logged in.<br>If you leave it blank they will return to the previous page.',
	'fieldset' => 'Other Admin Settings',
	'prefix' => '<p class="input-static">'.$config->site->url.'</p>',
	'value' => !empty($config->extensions->core->login_url) ? $config->extensions->core->login_url : ''
);

$form_structure['extensions-core-register_url'] = array(
	'label' => 'Post-registration URL',
	'tooltip' => 'This is the URL that users will be redirected to after they have registered.<br>If you leave it blank they will be a shown a registration confirmation message.',
	'fieldset' => 'Other Admin Settings',
	'prefix' => '<p class="input-static">'.$config->site->url.'</p>',
	'value' => !empty($config->extensions->core->register_url) ? $config->extensions->core->register_url : ''
);
?>