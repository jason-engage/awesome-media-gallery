<?php  

#################################################################################
# Watermark Image
#################################################################################

require_once($_SERVER['DOCUMENT_ROOT'] . '/_inc.php'); 

include ($_SERVER['DOCUMENT_ROOT'] . '/_variables.php');


$domain = $_SERVER['DOCUMENT_ROOT'];

// Temporary Images folder, must end with slash.
$images_folder = '/tpl/uploads/';
//$images_folder = '/upload/server/php/files/';
//$images_folder = '/';


// Save watermarked images to this folder, must end with slash.
$destination_folder = '/tpl/uploads/';
//$destination_folder = '/';

// Path to the watermark image (apply this image as waretmark)
//$watermark_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $config->site->upload_path . $config->site->media->watermark;
$watermark_path = $domain . '/' . $config->site->media->watermark;

$source_file = $_REQUEST['filename']; //CHANGE TO $_POST
//$source_file = $_GET['img_file']; //CHANGE TO $_POST

$p = $config->site->media->watermark_position;  //CHANGE TO $_POST
$s = $config->site->media->watermark_scale;  //CHANGE TO $config


$filename = $domain. $images_folder. $source_file;


// Watermark all the "jpg" files from images folder
// and save watermarked images into destination folder
if (file_exists($filename)) {

	// Load functions for image watermarking
	include("watermark.class.php");
	
	// Image path
	
	// Where to save watermarked image
	$imgdestpath = $domain . $destination_folder . basename($filename);

	// Watermark image
	$img = new Zubrag_watermark($filename, $s, $p); // filename, size %, position
	$img->ApplyWatermark($watermark_path);
	$img->SaveAsFile($imgdestpath);
	$img->Free();

}

?><!--<img src="<?php echo $images_folder . basename($filename); ?>" />-->