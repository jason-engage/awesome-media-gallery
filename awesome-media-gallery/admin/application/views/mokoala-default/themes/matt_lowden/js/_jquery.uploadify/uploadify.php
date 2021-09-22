<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
require_once dirname(__FILE__).'/../../../../../../../library/com/mokoala/Utility/Utility.class.php';

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$folder_offset = dirname(__FILE__) . '/../../../../../../../../';
	$targetPath = $folder_offset . $_REQUEST['folder'] . '/';
	$targetPath = str_replace('//','/',$targetPath);

	$target_file = $targetPath . $_FILES['Filedata']['name'];

	$file_parts = pathinfo($target_file);
	
	$target_file = $file_parts['dirname'].'/'.MK_Utility::getSlug( $file_parts['filename'] ).'.'.$file_parts['extension'];
		
	$counter = 0;
	
	while( is_file($target_file) )
	{
		$counter++;
		$target_file = $file_parts['dirname'].'/'.MK_Utility::getSlug( $file_parts['filename'] ).'-'.$counter.'.'.$file_parts['extension'];
		$target_file = str_replace('//', '/', $target_file);
	}

	move_uploaded_file($tempFile, $target_file);

	$local_file = str_replace($folder_offset, '', $target_file);
	$local_file = str_replace('//', '/', $local_file);
	print $local_file;
}
?>