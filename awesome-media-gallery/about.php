<?php
require_once '_inc.php';

//Variables
include ('_variables.php'); ?>
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
    
	<link rel="stylesheet" type="text/css" href="css/modal.css">

  </head>
  <body class="modal-body modal-about">
	  <div class="modal-container">
	  	<?php include ('includes/about.php'); ?>  
	  </div>  
  </body>
</html>