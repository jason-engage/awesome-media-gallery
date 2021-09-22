<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
require_once dirname(__FILE__).'/../../../../../../../library/com/mokoala/Utility/Utility.class.php';

$target_folder = 'tpl/uploads'; // Relative to the root

//$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) /*&& $_POST['token'] == $verifyToken*/)
{
	$error = $_FILES['Filedata']['error'];

	if( $error === UPLOAD_ERR_OK )
	{
		$temp_file = $_FILES['Filedata']['tmp_name'];
		$folder_offset = dirname(__FILE__) . '/../../../../../../../../';
		$target_path = $folder_offset . $target_folder;
		$target_file = rtrim($target_path,'/') . '/' . $_FILES['Filedata']['name'];
	
		$file_parts = pathinfo($target_file);
		
		$target_file = $file_parts['dirname'].'/'.MK_Utility::getSlug( $file_parts['filename'] ).'.'.$file_parts['extension'];
			
		$counter = 0;
		
		while( is_file($target_file) )
		{
			$counter++;
			$target_file = $file_parts['dirname'].'/'.MK_Utility::getSlug( $file_parts['filename'] ).'-'.$counter.'.'.$file_parts['extension'];
			$target_file = str_replace('//', '/', $target_file);
		}

		move_uploaded_file($temp_file, $target_file);

		$local_file = str_replace($folder_offset, '', $target_file);
		$local_file = str_replace('//', '/', $local_file);
		print $local_file;
	}
	else
	{
		header('HTTP/1.1 403');
		if( $error === UPLOAD_ERR_INI_SIZE)
		{
			print "Sorry, the uploaded file exceeds the upload_max_filesize directive.";
		}
		elseif( $error === UPLOAD_ERR_FORM_SIZE)
		{
			print "Sorry, the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
		}
		elseif( $error === UPLOAD_ERR_PARTIAL || $error === UPLOAD_ERR_EXTENSION )
		{
			print "Sorry, there was an error uploading this file.";
		}
		elseif( $error === UPLOAD_ERR_NO_TMP_DIR)
		{
			print "Sorry, missing a temporary folder.";
		}
		elseif( $error === UPLOAD_ERR_CANT_WRITE)
		{
			print "Sorry, failed to write file to disk.";
		}
	}
}
?>