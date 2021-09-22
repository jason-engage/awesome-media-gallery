<?php

require_once('_inc.php');

include ('_variables.php');

ob_start();

$head_title[] = $langscape["Change Password"];

if(!$user->isAuthorized()){

	//header('Location: '.MK_Utility::serverUrl('/'), true, 302);

	exit;

}

// Change Password

	$change_password_settings = array(

		'attributes' => array(

			'class' => 'clear-fix standard'

		)
	);

    $change_password_structure = array(
        'old_password' => array(
            //'label' => 'Current Password',
            'attributes' => array(
                'type' => 'password',
                'placeholder' => ''.$langscape["Current Password"].''
            ),
            'validation' => array(
                'instance' => array()
            )
        ),
    
        'new_password' => array(
            //'label' => 'New Password',
            'type' => 'password',
            'attributes' => array(
                'placeholder' => ''.$langscape["New Password"].''
            ),
            'validation' => array(
                'instance' => array()
            )
        ),
        'submit' => array(
            'type' => 'submit',
            'attributes' => array(
                'value' => ''.$langscape["Save Changes"].'',
                'class' => 'btn-normal btn-primary'
            )
        )
    );

	$change_password_form = new MK_Form($change_password_structure, $change_password_settings);
	
	//print '<h3>Change Password</h3>';

	if( $change_password_form->isSubmitted() )
	{
        //echo 'Form submitted!';
        //exit();
    	try
		{
			$_user = MK_Authorizer::_authorizeByEmailPass(
				$user->getEmail(),
				$change_password_form->getField('old_password')->getValue()
			);
		}
		catch( Exception $e )
		{
			$change_password_form->getField('old_password')->getValidator()->addError($langscape["Incorrect password"]);
		}
	}


	if( $change_password_form->isSuccessful() )

	{
    
?>

		<p class="error message"><?php echo $langscape["Your changes have been saved."];?></p>

<?php

		$user

			->setPassword( $change_password_form->getField('new_password')->getValue() )

			->save();

	}

print $change_password_form->render();
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
    <link rel="stylesheet" type="text/css" href="css/modal.css">

	<?php if ($enable_modals) { ?>
	
    <script>
      
        window.onload = function() {              
                
            var $ = jQuery = window.parent.$;
            
            $(document).ready(function() {

			         function isOdd(num) { 
			            return (num % 2) == 1
			        }

			        var o_Height = parseInt(parent.$(".password-container").css("height"), 10);
			        var i_Height = $("#ChangePasswordFrame").contents().find("body").outerHeight();
			        
			        
				    pad = (o_Height - i_Height);
				    
				    if (pad < 20 ) {
					    pad = pad + 20;
				    } else {
					    pad =  40;
				    }
			        new_height = i_Height;
			        
			        //console.log('new height:' + new_height);
			        /*ADJUST FOR BLURRY MODALS*/
			        if (isOdd(new_height)) {
			           
			            new_height = (new_height + 1);
			        
			        } 			        
			        new_height = new_height + pad;
			        
           	        parent.$('#ChangePasswordFrame').css('height', (new_height)+'px');

			        parent.$(".password-container").css("max-height", (new_height) + 'px');
			        
			        parent.$(".password-container").css("height", (new_height) + 'px');

           });
              
        }

    </script>
    
	<?php } ?>

  </head>

  <body class="modal-body modal-change-password">
  	<div class="modal-container">  	
  		<?php echo $output;?>
  	</div>
  </body>

</html>