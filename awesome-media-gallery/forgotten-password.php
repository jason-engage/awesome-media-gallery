<?php

require_once('_inc.php');

include ('_variables.php');

ob_start();



$head_title[] = ''.$langscape["Password Recovery"].'';



if($user->isAuthorized()){

	header('Location: '.MK_Utility::serverUrl('/'), true, 302);

	exit;

}



$body_class[] = 'narrow';



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

			'instance' => array(),

		),
    'attributes' => array(
        'placeholder' => ''.$langscape["Email Address"].''
			)

	),

	'submit' => array(

		'type' => 'submit',

		'attributes' => array(

			'value' => ''.$langscape["Recover password"].'',
      'class' => 'btn-normal btn-primary'

		)

	)

);



$form = new MK_Form($structure, $settings);



if($form->isSuccessful())

{

	$user_type = MK_RecordModuleManager::getFromType('user');

	$user_search = array(

		array('field' => 'email', 'value' => $form->getField('email')->getValue()),
		array('field' => 'type', 'value' => MK_RecordUser::TYPE_CORE)

	);

	$user_search = $user_type->searchRecords($user_search);



	if( $reset_user = array_pop($user_search) )

	{

		$new_password = MK_Utility::getRandomPassword(8);

		$reset_user

			->setPassword($new_password)

			->save();

?>

<!--<h3>Password Recovery</h3>-->

<p class="alert alert-success"><?php echo $langscape["Sent email with new password"];?></p>
<p class="alert alert-success"><a href="sign-in.php"><?php echo $langscape["Click here"];?></a> <?php echo $langscape["to access the login screen"];?></p>

<?php

		$message = '<p class="header">Dear '.$reset_user->getDisplayName().',<br /><br />'.$langscape["Your new password is"].': <strong>'.$new_password.'</strong></p>';

		$mailer = new MK_BrandedEmail();

		$mailer

			->setSubject(''.$langscape["Password Recovery"].'')

			->setMessage($message)

			->send($reset_user->getEmail(), $reset_user->getDisplayName());

	}

	else

	{

		$form->getField('email')->getValidator()->addError(''.$langscape["This email does not match our records"].'');

?>

		<!--<h3>Password Recovery</h3>-->

		<p class="header"><?php echo $langscape["Enter your email below to reset your password"];?></p>
    <p class="notice-header"><?php echo $langscape["NOTE: reset social network password will not work"];?></p>
<?php

		print $form->render();

	}

}

else

{

?>

		<!--<h3>Password Recovery</h3>-->

		<p class="header"><?php echo $langscape["Enter your email below to reset your password"];?></p>
    <p class="notice-header"><?php echo $langscape["NOTE: reset social network password will not work"];?></p>
<?php



	print $form->render();

}



$output = ob_get_contents();

ob_end_clean(); ?>



<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
	<base href="<?php echo $config->site->url; ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="robots" content="noindex">
    <title><?php echo implode(' / ', $head_title); ?></title>
    <link href="favicon.png" rel="icon" type="image/x-icon">
    <meta name="viewport" content="width=480, user-scalable=0">    
    <link rel="stylesheet" type="text/css" href="css/vendor/entypo.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/socicon.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">

    <?php if ($config->site->style->enable_cdn) { ?>
    	<script type="application/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    	
    <?php }else { ?>
	    <script type="application/javascript" src="js/vendor/jquery-1.11.0.min.js"></script>
    
    <?php } ?>
    

    <script>

	<?php if ($enable_modals) { ?>
	        
        $(document).ready(function() {
     
 			//check if in iFrame, if so hide title
			if(self!=top) {
				$('.modal-title').hide();				
			}
 			
            var ModalContainer = '.sign-in-container';
            var ModalInnerContainer = '.modal-container';
            
            setNewHeight2(ModalContainer, ModalInnerContainer, '#SignInFrame');
        		
        });

	<?php } ?>
        
    </script>

  </head>
  <body class="modal-body modal-forgot-password">
	<div class="modal-container">
    	<div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
		<?php echo $output;	?>
    </div>
  </body>
</html>