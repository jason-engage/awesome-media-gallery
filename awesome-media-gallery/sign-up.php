<?php

require_once('_inc.php');

include ('_variables.php');

header('Content-type: text/html; charset=utf-8');

ob_start();

$head_title[]     = $langscape["Sign Up"];

$user_module      = MK_RecordModuleManager::getFromType('user');
$user_meta_module = MK_RecordModuleManager::getFromType('user_meta');
$field_module     = MK_RecordModuleManager::getFromType('module_field');

$social = false;

$output = '';

// If the user is already logged in then return them to the homepage
if($user->isAuthorized() && !$user->isApproved() && !$config->site->members->enable_unapproved_login)
{
  
	echo '<link rel="stylesheet" type="text/css" href="css/modal.css"><body class="modal-body modal-sign-up"><span class="signed-in-text">' . $langscape["Your account is awaiting approval. We will contact you soon."] . '</span></body>';
exit;

} elseif ($user->isAuthorized()) {

	echo '<link rel="stylesheet" type="text/css" href="css/modal.css"><body class="modal-body modal-sign-up"><span class="signed-in-text">' . $langscape["You are logged in."] . '</span></body>';
exit;

}

// User is logging in with their site account
if( MK_Request::getQuery('platform') === 'core' )
{
	unset( $session->registration_details );
}

if( $verification_code = MK_Request::getQuery('verification_code') )
{
	$get_user_from_code = $user_meta_module->searchRecords(array(
		array('field' => 'key', 'value' => 'verification_code'),
		array('field' => 'value', 'value' => $verification_code),
	));

	if( $user_from_code = array_pop($get_user_from_code) )
	{
		$user = $user_from_code->objectUser();
		$user
			->setVerificationCode('')
			->isEmailVerified(true)
			->save();
?>
        <div class="notice-header success-header"><?php echo $langscape["Awesome, You're Verified!"];?></div>
        <p class="alert alert-success"><?php echo $langscape["We're sending you back to the home page."];?></p>
        <script>
         // setTimeout("top.location.href = 'index.php';",1500);
        </script>
<?php
		$session->login = $user->getId();
		$cookie->set('login', $user->getId(), $config->site->user_timeout);
        exit();
	}
	else
	{
?>
        <h3><?php echo $langscape["Invalid Verification Code"];?></h3>
        <p class="alert alert-error"><?php echo $langscape["This verification code is invalid."];?></p>
<?php
	}

}
else
{

	$criteria = array(
		array('field' => 'module', 'value' => $user_module->getId()),
		array('field' => 'name', 'value' => 'email')
	);
	
	$user_email_field = $field_module->searchRecords($criteria);
	$user_email_field = array_pop( $user_email_field );
    
    $criteria_display_name = array(
		array('field' => 'module', 'value' => $user_module->getId()),
		array('field' => 'name', 'value' => 'username')
	);
	
	$display_name_field = $field_module->searchRecords($criteria_display_name);
	$display_name_field = array_pop( $display_name_field );
	
	
	$settings_login = array(
		'attributes' => array(
			'class' => 'clear-fix standard standard-right social'
		)
	);
	
	$structure_login = array();
  
	if($config->site->facebook->login)
	{
		$structure_login['facebook'] = array(
			'fieldset' => 'Social-SignUp',
			'type' => 'link',
			'text' => '',
			'icon' => '<span class="socicon socicon-facebook"></span>',
			'attributes' => array(
				'href' => 'sign-in.php?platform=facebook',
                'class' => 'btn-social facebook-btn',
                'target' => "_parent"
            )
		);	
	}

  
	if($config->site->twitter->login)
	{
		$structure_login['twitter'] = array(
			'fieldset' => 'Social-SignUp',
			'type' => 'link',
			'text' => '',
			'icon' => '<span class="socicon socicon-twitter"></span>',
			'attributes' => array(
				'href' => 'sign-in.php?platform=twitter',
                'class' => 'btn-social twitter-btn',
                'target' => "_parent"
                )
            );	
	}

	if($config->site->linkedin->login)
	{
		$structure_login['linkedin'] = array(
			'fieldset' => 'Social-SignUp',
			'type' => 'link',
			'text' => '',
			'icon' => '<span class="socicon socicon-linkedin"></span>',
			'attributes' => array(
				'href' => 'sign-in.php?platform=linkedin',
                'class' => 'btn-social linkedin-btn',
                'target' => "_parent"
			)
		);	
	}

	if($config->site->windowslive->login)
	{
		$structure_login['windowslive'] = array(
			'fieldset' => 'Social-SignUp',
			'type' => 'link',
			'text' => '',
			'icon' => '<span class="socicon socicon-windows"></span>',
			'attributes' => array(
				'href' => 'sign-in.php?platform=windowslive',
				'class'=>'btn-social  windowslive-btn',
                'target' => "_parent"
			)
		);	
	}

	if($config->site->yahoo->login)
	{
		$structure_login['yahoo'] = array(
			'fieldset' => 'Social-SignUp',
			'type' => 'link',
			'text' => '',
			'icon' => '<span class="socicon socicon-yahoo"></span>',
			'attributes' => array(
				'href' => 'sign-in.php?platform=yahoo',
				'class'=>'btn-social yahoo-btn',
                'target' => "_parent"
			)
		);
	}

	if($config->site->google->login)
	{
		$structure_login['google'] = array(
			'fieldset' => 'Social-SignUp',
			'type' => 'link',
			'text' => '',
			'icon' => '<span class="socicon socicon-google"></span>',
			'attributes' => array(
				'href' => 'sign-in.php?platform=google',
                'class'=>'btn-social google-btn',
                'target' => "_parent"
			)
		);	
	}
	
	if($config->site->wordpress->login)
	{
		$structure_login['wordpress'] = array(
			'fieldset' => 'Social-SignUp',
			'type' => 'link',
			'text' => '',
			'icon' => '<span class="socicon socicon-wordpress"></span>',
			'attributes' => array(
				'href' => 'sign-in.php?platform=wordpress',
                'class' => 'btn-social wordpress-btn',
                'target' => "_parent"
			)
		);	
	}
	
	$settings = array(
		'attributes' => array(
			'class' => 'clear-fix standard standard-'.( count($structure_login) > 0 ? 'left' : 'full' )
		)
	);

	if ($config->site->members->enable_email_registration) {
		    
		$structure = array(
	    
	        'display_name' => array(
				//'label' => 'Display name',
				'fieldset' => 'Sign Up',
				'validation' => array(
					'instance' => array(),
	                'unique' => array(null, $display_name_field, $user_module)
				),
	            'attributes' => array(
	                'placeholder' => ''.$langscape["Display Name"].''
				),
	            'suffix' => '<div id="username-text" style="display:none;"></div>'
			),
	    
	        'email' => array(
				//'label' => 'Email',
				'fieldset' => 'Sign Up',
				'validation' => array(
					'email' => array(),
					'instance' => array(),
					'unique' => array(null, $user_email_field, $user_module)
				),
	            'attributes' => array(
	                'placeholder' => ''.$langscape["Email Address"].''
				)
			),
	
			'password' => array(
				//'label' => 'Password',
				'fieldset' => 'Sign Up',
				'type' => 'password',
				'validation' => array(
					'instance' => array(),
				),
	            'attributes' => array(
	                'placeholder' => ''.$langscape["Password"].''
				),
			),
	        'terms' => array(
	            'type' => 'checkbox',
				'label' => ''.$langscape["I agree to the"].' <a class="terms" href="terms.php">'.$langscape["Terms and Conditions"].'</a>',
				'fieldset' => 'Sign Up',
	            'attributes' => array(
					'value' => '1'
				),
				'validation' => array(
					'instance' => array(),
				),
			),
	        
			'register' => array(
				'type' => 'submit',
				'fieldset' => 'Sign Up',
				'attributes' => array(
					'value' => ''.$langscape["Sign Up"].'',
	                'class' => 'btn-normal btn-primary'
				)
			),
	        'signin' => array(
	            'type' => 'link',
	            'fieldset' => 'Other',
	            'text' => ''.$langscape["Already Registered?"].'',
	            'attributes' => array(
	                 'href' => 'sign-in.php',
	                'class' => 'sign-in-instead' 
	            ) 
	        ) 
		);

	$form = new MK_Form($structure, $settings);
	
	} // If email disabled

	
	if( (!empty($form)) && $form->isSuccessful() )
	{
        $username = str_replace(' ', '', $form->getField('display_name')->getValue());
        $username = iconv('UTF-8', 'ASCII//TRANSLIT', utf8_encode($username));
        $username = str_replace('?', '', $username);
        $username = strtolower($username);
    
		$new_user = MK_RecordManager::getNewRecord($user_module->getId());
		
		$new_user
			->setEmail($form->getField('email')->getValue())
			->setPassword($form->getField('password')->getValue())
			->setDisplayName($form->getField('display_name')->getValue())
            ->setCategory(1)
            ->setUsername($username)
			->save();
?>
           
<?php
		if( $config->extensions->core->email_verification )
		{
?>
			<link rel="stylesheet" type="text/css" href="css/modal.css"><body class="modal-body modal-sign-up"><div class="modal-container"><p class="alert alert-success"><?php echo $langscape["You've been emailed a link to verify your email"];?></p>
			<div class="notice-spam"><?php echo $langscape["Spam Notice"];?></div><div class="notice-spam-text"><?php echo $langscape["Check your spam folder"];?></div></div></body>
<?php
		}
		else
		{
			
			$session->login = $new_user->getId();
			$cookie->set('login', $new_user->getId(), $config->site->user_timeout);
?>

			<link rel="stylesheet" type="text/css" href="css/modal.css"><body class="modal-body modal-sign-up"><div class="modal-container"><p class="alert alert-success"><?php echo $langscape["You are signed in. Please wait..."];?></p>
			<div class="notice-spam"><?php echo $langscape["Spam Notice"];?></div><div class="notice-spam-text"><?php echo $langscape["Check your spam folder"];?></div></div></body>
			
      <script>
       setTimeout("top.location.href = 'index.php';",0);
      </script>
<?php
		}
	
		if( $redirect = $config->extensions->core->register_url )
		{
			header('Location: '.MK_Utility::serverUrl($redirect), true, 302);
		}
	}
	else
	{

	if( $config->site->members->enable_signup_notice ) {
        $output = '<div class="notice-header notice-login">'.$langscape["Important"].'</div><div class="notice-text">'.$langscape["Please respect other people"].'</div>';
	}
	
    if(count($structure_login) > 0)
		{
			$social=true;
            echo '<div class="notice-header social-login">'.$langscape["Use a social network"].'</div>';
			$login_form = new MK_Form($structure_login, $settings_login);
			print $login_form->render();
		}
		
		if ( !empty($form) ) {
			
			if ($social) {
				echo '<div class="notice-header email-login">'.$langscape["Or use email"].'</div>';
			} else {
				echo '<div class="notice-header email-login">'.$langscape["Use email login"].'</div>';
			}
			print $form->render();
		}
	}
}

$output .= ob_get_contents();
ob_end_clean();

?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
	<base href="<?php echo $config->site->url; ?>" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="robots" content="noindex">
    <title><?php echo implode(' / ', $head_title); ?></title>
    
    <?php if ($deviceType == 'tablet') { ?>
    <meta name="viewport" content="width=600, user-scalable=0"> 
    <?php } else { ?>
    <meta name="viewport" content="width=460, user-scalable=0">     
    <?php } ?>

    <link href="favicon.png" rel="icon" type="image/x-icon">
	<style type="text/css">
		 body {background:transparent;}
	</style>
    <link rel="stylesheet" type="text/css" href="css/vendor/entypo.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/socicon.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/green.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    
    <?php if ($config->site->style->enable_cdn) { ?>
    	<script type="application/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    	<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.1/icheck.min.js"></script>

    <?php }else { ?>
	    <script type="application/javascript" src="js/vendor/jquery-1.11.0.min.js"></script>
	    <script type="application/javascript" src="js/vendor/jquery.icheck.min.js"></script>
    <?php } ?>

    <script src="js/modal.js"></script>
   	
    <script type="text/javascript">
            
    $(document).ready(function() { //iFrame document is loaded.
       
<?php 
	if ($enable_modals) {	?>

		//check if in iFrame, if so hide title
		if(self!=top) {
			$('.modal-title').hide();				
		}
       
        var ModalContainer = '.sign-up-container';
        var ModalInnerContainer = '.modal-container';
        
        setNewHeight2(ModalContainer, ModalInnerContainer, '#SignUpFrame');
       
        $('a.terms').bind('click',function(e) {
            e.preventDefault();
            parent.$('span[data-modal="modal-terms"]', window.parent.document).trigger( "click" );  
        });
		
        $('a.sign-in-instead').bind('click',function(e) {
            //e.preventDefault(); 
            //parent.$('span[data-modal="modal-sign-in"]', window.parent.document).trigger("click");  
        });           
 
       
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });
      
        var display_name    = $('#display_name');
        var password_new_1   = $('#password_new_1'); 
        var password_new_2 = $('#password_new_2');
        
        //var pad = <?php echo (($social)?'40':'41'); ?> /*fix for bluriness */
       
        
        password_new_1.on('keyup', function() {
        
            if ((this.value != '' || this.value != this.defaultValue) && (password_new_2.css('display') != 'inline-block')) {
                password_new_2.css('display', 'inline-block');
				setNewHeight2(ModalContainer, ModalInnerContainer, '#SignUpFrame');
            } 
        });
        
               
       //Check if an error message is above the password field. if true, show confirm password field.
       if ($("body").find('.error').length) {
            password_new_2.css('display', 'inline-block');
			setNewHeight2(ModalContainer, ModalInnerContainer, '#SignUpFrame');
       }

        
<?php 
	} else {
 ?>
 	    $('body').contents().find('a.terms').bind('click',function(e) {
           // e.preventDefault();
        });

       $('body').contents().find('input').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });

        var display_name    = $('#display_name');
        var password_new_1   = $('#password_new_1'); 
        var password_new_2 = $('#password_new_2');
        
        password_new_1.on('keyup', function() {
        
            if ((this.value != '' || this.value != this.defaultValue) && (password_new_2.css('display') != 'inline-block')) {            
                password_new_2.css('display', 'inline-block');
            }
        });
       
       //Check if an error message is above the password field. if true, show confirm password field.
       if ($("body").find('.error').length) {
            password_new_2.css('display', 'inline-block');
       }        

<?php } ?>

    });
    
    </script>
  </head>
  <body class="modal-body modal-sign-up">	
	<div class="modal-container">
    	<div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
    	<?php echo $output; ?>
	</div>
  </body>
</html>