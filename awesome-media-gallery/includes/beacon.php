<?php

// This file initializes Mokoala
require_once '../admin/library/com/mokoala/Mokoala.php';

if ($config->site->enable_tracking == true ) { //Tracking enabled.
    
    $last_run = ( !empty( $config->beacon->lastrun ) ? intval($config->beacon->lastrun) : NULL ); //Get the last runtime.
    $time_now = time();
    
    //Time in seconds between each run.
    $delay    = 604800; //7 Days
    
    if ( $last_run ) { //Beacon has been sent at least once.
    
        if ($time_now - $last_run >= $delay) { //its been more than a day so run our external file
            
            $run = true;
            
        } else { //No need to run.
        
            //Do nothing.
            $run = false;
        
        }
        
    } else { //Beacon never sent. 

        $run = true;

    }

} else { //Tracking disbaled.

    //Do nothing.
    $run = false;

} 

if ( !empty($run) && $run === true ) {

 // Connect to the database
    MK_Database::connect(MK_Database::DBMS_MYSQL, $config->db->host, $config->db->username, $config->db->password, $config->db->name);

    $image_module   = MK_RecordModuleManager::getFromType('image');
    $member_module  = MK_RecordModuleManager::getFromType('user');

    $server_ip      = ( !empty( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : 'Unknown'); //The server IP address.
    $site_url       = $config->site->url; //The site URL from the config file.
    $site_name      = ( !empty( $config->site->name ) ? $config->site->name : NULL ); //Get the last runtime.
    $site_email     = ( !empty( $config->site->email ) ? $config->site->email : NULL ); //The site email address.

    $total_members  = $member_module->getTotalRecords();
    $total_images   = $image_module->getTotalRecords(array(array('field' => 'type_gallery', 'value' => 1)));
    $total_videos   = $image_module->getTotalRecords(array(array('field' => 'type_gallery', 'value' => 2)));

    //API Url
    $url = "http://engagefb.com/api/gallery/";

    //Setup the data array.
    $data = array(
            'api_key'   => "be121740bf988b2225a313fa1f107ca1", //API Key for this application.
            'data'      => array(
                'time_sent'  => date('Y-m-d H:i:s', $time_now), //Time stamp.
                'site_url'   => $site_url, //Site url.
                'site_name'  => $site_name, //Site name.
                'site_email' => $site_email, //Site name.
                'ip_address' => $server_ip, //Server IP address.
                'members'    => $total_members, //Total number of members.
                'videos'     => $total_videos, //Total number of videos.
                'images'     => $total_images //Total number of images.
            )
    );
    
    //Build data string based on array.
    $data_string = http_build_query($data);
	
    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

    //execute post
    $result = curl_exec($ch);
    
    $result = json_decode($result); //Decode the JSON response.
    
    if(!empty ($result) ) { //Response is not NULL
        
        if( $result->status == 'ok' ) {
        
            $success = true;
        
        } else { //Error in the response.
        
            $success = false;

        }

        //close connection
        curl_close($ch);
    
    } else { //No resposne from the server.
    
        $success = false;
    
    }
    
    //Update the time last run to now.
    $config_data_new = array();
    $config_data_new['beacon.lastrun'] = $time_now;
    
    if( is_array( $config_data_new ) ) {
        
        $config = parse_ini_file('../admin/config.ini.php');
        write_ini_file(array_merge_replace($config, $config_data_new), '../admin/config.ini.php');
    
    }

}

//  Send a BEACON image back to the user's browser
  header( 'Content-type: image/gif' );
  # The transparent, beacon image
  echo chr(71).chr(73).chr(70).chr(56).chr(57).chr(97).
      chr(1).chr(0).chr(1).chr(0).chr(128).chr(0).
      chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).
      chr(33).chr(249).chr(4).chr(1).chr(0).chr(0).
      chr(0).chr(0).chr(44).chr(0).chr(0).chr(0).chr(0).
      chr(1).chr(0).chr(1).chr(0).chr(0).chr(2).chr(2).
      chr(68).chr(1).chr(0).chr(59);
?>