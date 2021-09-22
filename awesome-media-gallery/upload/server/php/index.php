<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
require('../../../_inc.php');
 
//error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');

/*
$options = array(
  'param_name' => 'files',
  'upload_dir' => '../../../tpl/uploads/',
  'upload_url' => 'http://'.$_SERVER['SERVER_NAME'].'/tpl/uploads/',
);*/

$options = array(
  'param_name' => 'files',
  'upload_dir' => '../../../' . $config->site->upload_path,
  'upload_url' => $config->site->url . $config->site->upload_path,
);

$upload_handler = new UploadHandler($options);
