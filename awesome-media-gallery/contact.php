<?php
require_once '_inc.php';

//Variables
include ('_variables.php'); ?>
<?php header('Content-type: text/html; charset=utf-8');?>
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
    <meta name="viewport" content="width=560, user-scalable=0"> 
    <?php } else { ?>
    <meta name="viewport" content="width=460, user-scalable=0">     
    <?php } ?> 
    <link href="favicon.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/vendor/entypo.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/socicon.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">
    <?php if ($config->site->style->enable_cdn) { ?>
    	<script type="application/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <?php } else { ?>
	    <script type="application/javascript" src="js/vendor/jquery-1.11.0.min.js"></script>
    <?php } ?>

    <?php if ($enable_modals) {	?>
    <script type="application/javascript" src="js/modal.js"></script>    
    <script>
        
        $(document).ready(function() {

			var ModalContainer = '.contact-container';
			var ModalInnerContainer = '.modal-container';
			
			setNewHeight2(ModalContainer, ModalInnerContainer, '#ContactFrame');
    		
        });
        
    </script>
	<?php } ?>

  </head>
  <body class="modal-body  modal-contact">
	  <div class="modal-container">
	  	<?php include ('includes/contact.php'); ?>
	  </div>  
  </body>
</html>