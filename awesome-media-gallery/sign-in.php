<?php

require_once( '_inc.php' );

include( '_variables.php' );

header( 'Content-type: text/html; charset=utf-8' );

ob_start();

$head_title[] = $langscape["Sign In"];

$social = false;

$callback_url = MK_Utility::serverUrl( 'index.php' );

// If the user is already logged in then return them to the homepage
if($user->isAuthorized() && !$user->isApproved() && !$config->site->members->enable_unapproved_login)
{
  
	echo '<link rel="stylesheet" type="text/css" href="css/modal.css"><body class="modal-body modal-sign-up"><span class="signed-in-text">' . $langscape["Your account is awaiting approval. We will contact you soon."] . '</span></body>';
exit;

} elseif ($user->isAuthorized()) {

	echo '<link rel="stylesheet" type="text/css" href="css/modal.css"><body class="modal-body modal-sign-up"><span class="signed-in-text">' . $langscape["You are logged in."] . '</span></body>';
exit;

}

if ( $config->site->yahoo->login && $platform === 'yahoo' ) { // If the user clicked the 'sign in with Yahoo' link
    
} elseif ( $config->site->windowslive->login && $platform === 'windowslive' ) { // If the user clicked the 'sign in with Windows Live' link
    
} elseif ( $config->site->facebook->login && $platform === 'facebook' ) { // If the user clicked the 'sign in with Facebook' link
    
    $config          = MK_Config::getInstance();
    $facebook_return = MK_Utility::serverUrl( '/' );
    //$facebook_return = $callback_url;
    $facebook_url    = $config->facebook->getLoginUrl( array(
         'redirect_uri' => $facebook_return,
        'scope' => 'email,user_photos' 
    ) );
    //echo $facebook_url;
    header( 'Location: ' . $facebook_url, true, 302 );
    exit;
    
} elseif ( $config->site->twitter->login && $platform === 'twitter' && empty( $session->registration_details ) ) { // If the user clicked the 'sign in with Twitter' link
    
    $config = MK_Config::getInstance();
    
    $callback_url = MK_Utility::serverUrl( 'sign-in.php?platform=twitter' );
    
    $twitter_request_token = $config->twitter->getRequestToken( $callback_url );
    
    $session->twitter_oauth_token        = $twitter_request_token['oauth_token'];
    $session->twitter_oauth_token_secret = $twitter_request_token['oauth_token_secret'];
    
    $twitter_url = $config->twitter->getAuthorizeURL( $session->twitter_oauth_token );
    
    header( 'Location: ' . $twitter_url, true, 302 );
    exit;
    
} elseif ( $config->site->google->login && $platform === 'google' ) { // If the user clicked the 'sign in with Google' link
    

} elseif ( $config->site->wordpress->login && $platform === 'wordpress' ) { // If the user clicked the 'sign in with Google' link
    
    
} elseif ( $platform === 'core' ) { // User is logging in with their site account
    unset( $session->registration_details );
}

$user_module  = MK_RecordModuleManager::getFromType( 'user' );
$field_module = MK_RecordModuleManager::getFromType( 'module_field' );
$criteria     = array(
     array(
         'field' => 'module',
        'value' => $user_module->getId() 
    ),
    array(
         'field' => 'name',
        'value' => 'email' 
    ) 
);

$user_email_field = $field_module->searchRecords( $criteria );
$user_email_field = array_pop( $user_email_field );



if ( !empty( $session->registration_details ) ) {
    $body_class[] = 'narrow';
    
    $user_details = unserialize( $session->registration_details );
    $settings     = array(
         'attributes' => array(
             'class' => 'clear-fix standard standard-narrow',
            'action' => 'sign-in.php' 
        ) 
    );
    
    if ( !empty( $user_details['twitter_id'] ) ) { //Returned from twitter.
        
?>
		<h3><?php echo $langscape["Twitter Sign In"];?></h3>
<?php
    }
?>
	<p><?php echo $langscape["Enter your email address to complete sign-in"];?></p>
<?php
    $structure = array(
         'email' => array(
             'label' => 'Email',
            'validation' => array(
                 'email' => array(),
                'instance' => array(),
                'unique' => array(
                     null,
                    $user_email_field,
                    $user_module 
                ) 
            ) 
        ) 
    );
    
    $complete_field = array(
         'type' => 'submit',
        'attributes' => array(
             'value' => ''.$langscape["Complete Sign-In"].'' 
        ) 
    );
    
    if ( !empty( $user_details['twitter_id'] ) ) {
        $structure['twitter'] = $complete_field;
    }
    
    $structure['cancel'] = array(
         'type' => 'submit',
        'attributes' => array(
             'value' => ''.$langscape["Cancel Sign-In"].'',
            'class' => 'button-red' 
        ) 
    );
    
    $form = new MK_Form( $structure, $settings );
    
    if ( $form->isSubmitted() && $form->getField( 'cancel' )->getValue() ) {
        header( 'Location: ' . MK_Utility::serverUrl( 'sign-in.php?platform=core' ), true, 302 );
        exit;
    }
    
    if ( $form->isSuccessful() ) {
        $user_details['email']         = $form->getField( 'email' )->getValue();
        $session->registration_details = serialize( $user_details );
        
        header( 'Location: ' . MK_Utility::serverUrl( 'index.php' ), true, 302 );
        
        exit;
    } else {
        print $form->render();
    }
} else {
    $settings_login = array(
         'attributes' => array(
             'class' => 'clear-fix standard standard-right social' 
        ) 
    );
    
    $structure_login = array();
    
    
    if ( $config->site->facebook->login ) {
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
    
    
    if ( $config->site->twitter->login ) {
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
    
    if ( $config->site->linkedin->login ) {
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
    
    if ( $config->site->windowslive->login ) {
        $structure_login['windowslive'] = array(
            'fieldset' => 'Social-SignUp',
            'type' => 'link',
            'text' => '',
            'icon' => '<span class="socicon socicon-windows"></span>',
            'attributes' => array(
                 'href' => 'sign-in.php?platform=windowslive',
                'class' => 'btn-social  windowslive-btn',
                'target' => "_parent" 
            ) 
        );
    }
    
    if ( $config->site->yahoo->login ) {
        $structure_login['yahoo'] = array(
            'fieldset' => 'Social-SignUp',
            'type' => 'link',
            'text' => '',
            'icon' => '<span class="socicon socicon-yahoo"></span>',
            'attributes' => array(
                 'href' => 'sign-in.php?platform=yahoo',
                'class' => 'btn-social yahoo-btn',
                'target' => "_parent" 
            ) 
        );
    }
    
    if ( $config->site->google->login ) {
        $structure_login['google'] = array(
            'fieldset' => 'Social-SignUp',
            'type' => 'link',
            'text' => '',
            'icon' => '<span class="socicon socicon-google"></span>',
            'attributes' => array(
                 'href' => 'sign-in.php?platform=google',
                'class' => 'btn-social google-btn',
                'target' => "_parent" 
            ) 
        );
    }

    if ( $config->site->wordpress->login ) {
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
             'class' => 'clear-fix standard standard-' . ( count( $structure_login ) > 0 ? 'left' : 'full' ) 
        ) 
    );
    
    
    if ($config->site->members->enable_email_registration) {
	   
	    $structure = array(
	         'email' => array(
	            //'label' => 'Email',
	             'fieldset' => 'Sign In',
	            'validation' => array(
	                 'email' => array(),
	                'instance' => array ()
	            ),
	            'attributes' => array(
	                 'placeholder' => ''.$langscape["Email Address"].'' 
	            ) 
	        ),
	        'password' => array(
	            //'label' => 'Password',
	             'fieldset' => 'Sign In',
	            'validation' => array(
	                 'instance' => array() 
	            ),
	            'attributes' => array(
	                 'type' => 'password',
	                'placeholder' => ''.$langscape["Password"].'' 
	            ) 
	        ),
	        'login' => array(
	             'type' => 'submit',
	            'fieldset' => 'Sign In',
	            'attributes' => array(
	                 'value' => 'Sign In',
	                'class' => 'btn-normal btn-primary' 
	            ) 
	        ),
	        'forgotten-password' => array(
	             'type' => 'link',
	            'fieldset' => 'Other',
	            'text' => ''.$langscape["Forgotten Password?"].'',
	            'attributes' => array(
	                 'href' => 'forgotten-password.php',
	                'class' => 'forgot-pass' 
	            ) 
	        ),
	        'signup' => array(
	            'type' => 'link',
	            'fieldset' => 'Other',
	            'text' => ''.$langscape["Sign up for an account"].'',
	            'attributes' => array(
	                 'href' => 'sign-up.php',
	                'class' => 'sign-up-instead' 
	            ) 
	        ) 
	    );  

	$form = new MK_Form( $structure, $settings );

    } // if email registration disabled
    
		    
    if (  (!empty($form)) && $form->isSuccessful() ) {
        $user = MK_Authorizer::authorizeByEmailPass( $form->getField( 'email' )->getValue(), $form->getField( 'password' )->getValue() );
        
        if ( $user->isAuthorized() ) {
            $session->login = $user->getId();
            $cookie->set( 'login', $user->getId(), $config->site->user_timeout );
            
            if ( !$redirect = $config->extensions->core->login_url ) {
                $redirect = $logical_redirect;
            }
            
		    echo '<link rel="stylesheet" type="text/css" href="css/modal.css"><body class="modal-body modal-sign-in"><span class="signed-in-text">'.$langscape["You are signed in. Please wait..."].'</span></body>';

            echo '<script> setTimeout("top.location.href = \'index.php\';",0); </script>';
        
            exit;
        } else {
            $form->getField( 'email' )->getValidator()->addError( ''.$langscape["Username and password does not match"].'' );
        }
    }
    
    
    if ( count( $structure_login ) > 0 ) {
	    $social=true;
        echo '<div class="notice-header social-login">'.$langscape["Use a social network"].'</div>';
        $login_form = new MK_Form( $structure_login, $settings_login );
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
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    
    <?php if ($config->site->style->enable_cdn) { ?>
    	<script type="application/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <?php }else { ?>
	    <script type="application/javascript" src="js/vendor/jquery-1.11.0.min.js"></script>
    <?php } ?>
      
    <script src="js/modal.js"></script>
    
    <script>
        
        $(document).ready(function() {
    
    	<?php if ($enable_modals) {	?>
 
 			//check if in iFrame, if so hide title
			if(self!=top) {
				$('.modal-title').hide();				
			}
 			
            var ModalContainer = '.sign-in-container';
            var ModalInnerContainer = '.modal-container';
            
            setNewHeight2(ModalContainer, ModalInnerContainer, '#SignInFrame');
        
            $('.sign-up-instead').bind('click',function(e) {
                e.preventDefault(); 
                parent.$("span[data-modal='modal-sign-up']", window.parent.document).click();  
            });       
		
		<?php } ?>
		
        });
        
    </script>
  </head>
  <body class="modal-body modal-sign-in">
    <div class="modal-container">
    <div class="modal-title"><img src="<?php echo $config->site->logo_modal; ?>" alt="<?php echo $config->site->name; ?>"></div>
	<?php echo $output; ?>
    </div>
  </body>
</html>