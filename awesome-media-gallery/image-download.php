<?php
	if (isset($_GET['a'])) {
		$filename = 'tpl/uploads/'.str_replace('../','',str_replace('./','',urldecode($_GET['a']) ) );
		
	    header("Pragma: public"); // required
	    header("Expires: 0");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, private");
	    header("Content-Description: File Transfer");
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="' . str_replace('../','',str_replace('./','',urldecode($_GET['a']) ) ).'"');
	    header("Content-Transfer-Encoding: binary");
	    header("Content-Length: ".filesize($filename));
	    readfile($filename);
	    exit;
	} else {
		header('location: ' . 'index.php');
	}
?>