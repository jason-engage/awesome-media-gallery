<?php


function getVineVideoFromUrl($url) {
    
    $result = array();
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    
    //Video MP4 Url
    preg_match('/twitter:player:stream.*content="(.*)"/', $res, $output);
    $result['video_mp4'] = $output[1];
    
    //Video Description
    preg_match('/twitter:description.*content="(.*)"/', $res, $output);
    $result['description'] = $output[1];
    
    //Video Title
    preg_match('/twitter:title.*content="(.*)"/', $res, $output);
    $result['title'] = $output[1];
    
    //Video Thumbnail Url
    preg_match('/twitter:image.*content="(.*)"/', $res, $output);
    $result['thumbnail_large'] = $output[1];
    
    return $result;
    
}

if(isset($_GET["video_id"]) && !empty($_GET["video_id"])) {

    $vineUrl = 'https://vine.co/v/' . $_GET["video_id"];

    echo json_encode(getVineVideoFromUrl($vineUrl));

} ?>
