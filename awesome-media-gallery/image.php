<?php

require_once '_inc.php';

// We get an instance of the image & gallery module
$image_module              = MK_RecordModuleManager::getFromType('image'); //Image details
$gallery_module            = MK_RecordModuleManager::getFromType('image_gallery'); //Gallery Info
$image_favourite_module    = MK_RecordModuleManager::getFromType('image_favourite'); //Fav info
$image_comment_module      = MK_RecordModuleManager::getFromType('image_comment'); //Comments Info
$image_comment_like_module = MK_RecordModuleManager::getFromType('image_comment_like'); //Comment Likes Info
$field_module              = MK_RecordModuleManager::getFromType('module_field');
$user_follower_module      = MK_RecordModuleManager::getFromType('user_follower'); //Follow

//Variables
include ('_variables.php');

//Image Functions
include ('includes/functions-image.php');

//Breadcrumb
include ('includes/breadcrumbs.php');

// Header
require_once ('header.php');

?>
<div class="main-container">
  <div class="main wrapper clearfix pure-g-r">  

    <!-- Content Section GALLERY Starts Here -->
    <?php include ('includes/gallery-image-single.php'); ?>    
    <!-- Content Section GALLERY Ends Here -->
    
  </div>
</div>

<!-- Footer Starts Here -->
<?php include ('footer.php'); ?>
<!-- Footer Ends Here -->