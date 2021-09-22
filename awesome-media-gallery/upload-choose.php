<?php

require_once ('_inc.php');
include ('_variables.php'); //Variables 

//IF NOT SIGN IN REDIRECT
if ( !$user->isAuthorized() || ($config->site->members->disable_uploads && !$user->objectGroup()->isAdmin() ) ) {
	header("Location: sign-up.php");
}

?>

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
    	<meta name="viewport" content="width=630, user-scalable=0"> 
    <?php } else { ?>
    	<meta name="viewport" content="width=530, user-scalable=0">     
    <?php } ?>
  
    <link rel="stylesheet" type="text/css" href="css/vendor/entypo.css">

    <?php if ($config->site->style->enable_cdn) { ?>
	    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
    <?php }else { ?>
	    <link rel="stylesheet" type="text/css" href="css/vendor/font-awesome.css">
    <?php } ?>

    <link rel="stylesheet" type="text/css" href="css/modal.css">

    <?php if ($config->site->style->enable_cdn) { ?>
    	<script type="application/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <?php }else { ?>
	    <script type="application/javascript" src="js/vendor/jquery-1.11.0.min.js"></script>
    <?php } ?>
        
    <script type="application/javascript" src="js/modal.js"></script>

    <script>
        
        $(document).ready(function() {
    
    	<?php if ($enable_modals) {	?>
  
            var ModalContainer = '.choose-container';
            var ModalInnerContainer = '.modal-container';
            
            setNewHeight2(ModalContainer, ModalInnerContainer, '#ChooseFrame');
        		
		<?php } ?>
		
        });
        
    </script>
  </head>
  <body class="modal-body modal-choose">
  
  	<div class="modal-container">
		
	    <div class="dotted choose">
	    
		    <!--<span class="choose-text notice-text">Choose:</span> -->
		   
			<?php
				//check for member approval
				if ($config->site->members->enable_approval && !$user->getMetaValue('approved')) {
				
					echo '<br><p>' . $langscape["Your account has not yet been approved"] . '</p>';
					
				} else {

					if ( !empty($config->site->media->enable_images) ) { ?> 
		    <div class="upload-images">
	            <a href="upload/">
	                <span><?php echo $langscape["Images"];?></span>
	                <i class="pictures icon"></i>
	            </a>
	        </div>
			<?php 	}
			
					if ( !empty($config->site->media->enable_videos) ) { ?> 
	
		    <div class="upload-videos">
	            <a href="upload-video.php">
	                <span><?php echo $langscape["Videos"];?></span>
	                <i class="fa fa-youtube-play icon"></i>
	            </a>
	        </div>
	
			<?php }
			
				  if ( !empty($config->site->media->enable_audio) && !empty($config->site->soundcloud->app_id) && ($config->site->soundcloud->enabled) ) { ?> 
	
		    <div class="upload-audio">
	            <a href="upload-audio.php">
	                <span><?php echo $langscape["Sounds"]; ?></span>
	                <i class="fa fa-music icon"></i>
	            </a>
	        </div>
	        
			<?php }
				
			} //If approved ?>
	        
	    </div>
  
  	</div>
  </body>
</html>