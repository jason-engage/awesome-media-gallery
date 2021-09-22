<?php

if (isset($_GET['lang'])) {
 $lang = $_GET["lang"];
 setcookie( 'GoLanguage', $lang, time() + 60*60*24*30 );
 header( "Location: /index.php" );
}

	
require_once '_inc.php';

// We get an instance of the image & gallery module
$image_module              = MK_RecordModuleManager::getFromType('image'); //Image details
$gallery_module            = MK_RecordModuleManager::getFromType('image_gallery'); //Gallery Info
$image_favourite_module    = MK_RecordModuleManager::getFromType('image_favourite'); //Fav info
$image_comment_module      = MK_RecordModuleManager::getFromType('image_comment'); //Comments Info
$image_comment_like_module = MK_RecordModuleManager::getFromType('image_comment_like'); //Comment Likes Info
$field_module              = MK_RecordModuleManager::getFromType('module_field');
$user_follower_module      = MK_RecordModuleManager::getFromType('user_follower'); //Follow
$user_module = MK_RecordModuleManager::getFromType('user'); //To Return images by user category

//Variables
include ('_variables.php');	

//Breadcrumb
include ('includes/breadcrumbs.php');

// Header
require_once ('header.php'); ?>


<div class="main-container">
  <div class="main grid-wrapper clearfix pure-g-r">  

    <!-- Content Section GALLERY Starts Here -->
    <?php include ('includes/gallery-home.php'); ?>    
    <!-- Content Section GALLERY Ends Here -->
    
  </div>
</div>

<!-- Footer Starts Here -->
<?php include ('footer.php'); ?>
<!-- Footer Ends Here -->
