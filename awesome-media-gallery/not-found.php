<?php

require_once '_inc.php';


// We get an instance of the image & gallery module
$image_module = MK_RecordModuleManager::getFromType('image');
$user_module = MK_RecordModuleManager::getFromType('user');
$user_notification_module = MK_RecordModuleManager::getFromType('user_notification');
$user_follower_module = MK_RecordModuleManager::getFromType('user_follower');
$image_favourite_module = MK_RecordModuleManager::getFromType('image_favourite');
$image_comment_module = MK_RecordModuleManager::getFromType('image_comment');

//Variables
include('_variables.php');

//CREATE BREADCRUMP
include ('includes/breadcrumbs.php');


?>
<?php
// Header Starts Here //
require_once 'header.php';
// Header Ends Here //
?>
<div class="main-container">
  <div class="main wrapper clearfix pure-g-r">  

    <!--- Content Section Not Found Starts Here -->
    <div class="not-found">
	    <i class="icon warning"></i>
	    <p class="alert not-found-text"><?php echo $langscape["Sorry, page not found"];?></p>
	    <p class="alert not-found-text"><?php echo $langscape["It may have been moved or deleted"];?></p>
    </div>    
    <!-- Content Section Not Found Ends Here -->
    
  </div>
</div>

<!-- Footer Starts Here -->
<?php include ('footer.php'); ?>
<!-- Footer Ends Here -->