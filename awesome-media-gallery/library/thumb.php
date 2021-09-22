<?php

require_once '../admin/library/com/mokoala/File/File.class.php';
require_once '../admin/library/com/mokoala/File/Image.class.php';
require_once '../admin/library/com/mokoala/File/ImageThumb.class.php';
require_once '../admin/library/com/mokoala/File/GifResizer.class.php';

$method = !empty($_GET['m']) ? $_GET['m'] : null;
$h = !empty($_GET['h']) ? $_GET['h'] : null;
$w = !empty($_GET['w']) ? $_GET['w'] : null;
$x = !empty($_GET['x']) ? $_GET['x'] : 0;
$y = !empty($_GET['y']) ? $_GET['y'] : 0;
$q = !empty($_GET['q']) ? $_GET['q'] : 100;
$c = !empty($_GET['c']) ? $_GET['c'] : 6;
$file = '../'.( !empty($_GET['f']) ? $_GET['f'] : null );
$animate = !empty($_GET['a']) ? $_GET['a'] : null;
$quality = $q;
$png_compression = $c;

$image = new MK_Image_Thumb($file, $w, $h, $method, '../tpl/img/thumbs/', $x, $y, $quality, $animate, $png_compression);

if(!$image->fileExists()){
	
	print 'Source file does not exist';

}elseif(!$image->fileReadable()){
	
	print 'Source file not readable';

}elseif(!$image->thumbDestinationWritable()){

	print 'Thumbnail directory not writable';

}else{
	
	$image->create();
	header('Content-Type: '.$image->getMime());
	header('Content-Disposition: inline; filename="'.$image->getThumbDestinationFilename().'"');
	print $image->getThumbData();

}

?>