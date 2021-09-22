<?php

require_once '_inc.php';

// Create instances of the modules we need.
$image_module             = MK_RecordModuleManager::getFromType('image');
$user_module              = MK_RecordModuleManager::getFromType('user');
$user_notification_module = MK_RecordModuleManager::getFromType('user_notification');
$user_follower_module     = MK_RecordModuleManager::getFromType('user_follower');
$image_favourite_module   = MK_RecordModuleManager::getFromType('image_favourite');
$image_comment_module     = MK_RecordModuleManager::getFromType('image_comment');

//Variables include
include('_variables.php');

/* Start Username slug */
if(empty($user_id) || ($user_id == '')) {
    
    //Let's remove HTTP:// OR HTTPS://
    $t_url = str_replace( "http://", "", str_replace("https://","",$config->site->url) );
    
    $url   = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; //Double quotes are ok in this instance.
    
    $parts = explode($t_url, $url);
    $parts = explode('/', $parts[1]);
    
    $strUser = $parts[0]; //Assign the user as the first array value, regardless.
    
    if ( count( $parts ) > 1 ) { //Looks like we have more data to assign.
        
        $section = htmlspecialchars(MK_Utility::sanitize($parts[1], true, true )); //Assign the secon array value to the section variable.
     
    }
    
    try {
        
        $user_record = $user_module->searchRecords( array(
            array( 'field' => 'username', 'value' => $strUser )
        )) ;

        if ( !empty( $user_record ) ) {
            
            $user_id = $user_record[0]->getId();
            
        } else {
        
            throw new Exception( 'User Not Found' );
        
        }
      
    } catch( Exception $e ) { //Failed to find a user. Send to 404 page.

        header( 'Location: ' . $config->site->url . 'not-found.php', true, 301 );
        exit;
        
    }

}
/* End Username slug */

//Breadcrumb include
include ('includes/breadcrumbs.php');

//Code for deleting users.
if( $action == 'delete-profile' && $user_record->canDelete( $user ) ) { //Check the action and the permission.
    
    $action_log_module = MK_RecordModuleManager::getFromType('action_log');
    $new_logged_action = MK_RecordManager::getNewRecord($action_log_module->getId());
    
    //Add the action to the admin log.
    $new_logged_action
    ->setUser( $user->getId() )
    ->setAction($user_record->getDisplayName() . ' was deleted.')
    ->save();

    $user_record->delete();
    header('Location: '.MK_Utility::serverUrl('index.php'), true, 302);

}

//Header include
require_once 'header.php'; ?>

<div class="main-container">
  <div class="main wrapper clearfix pure-g-r">  

    <!-- Content Section MEMBER Starts Here -->
    <?php include ('includes/member.php'); ?>    
    <!-- Content Section MEMBER Ends Here -->
    
  </div>
</div>

<?php 
//Footer include
include ('footer.php'); ?>