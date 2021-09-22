<?php

require_once '_inc.php';


// We get an instance of the image & gallery module
$image_module = MK_RecordModuleManager::getFromType('image');
$user_module = MK_RecordModuleManager::getFromType('user');
$user_notification_module = MK_RecordModuleManager::getFromType('user_notification');
$image_favourite_module = MK_RecordModuleManager::getFromType('image_favourite');
$image_comment_module = MK_RecordModuleManager::getFromType('image_comment');
$field_module = MK_RecordModuleManager::getFromType('module_field');

//Variables
require_once '_variables.php';

//CREATE BREADCRUMB
include ('includes/breadcrumbs.php');


// Header Starts Here
require_once 'header.php';

?>
<div class="main-container">
  <div class="main wrapper clearfix pure-g-r">  

    <!--- Content Section GALLERY Starts Here -->
    <?php include ('includes/members.php'); ?>    
    <!-- Content Section GALLERY Ends Here -->
    
  </div>
</div>

<!-- Footer Starts Here -->
<?php include ('footer.php'); ?>
<!-- Footer Ends Here -->