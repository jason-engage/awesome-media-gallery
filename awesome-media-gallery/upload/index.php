<?php
require_once('../_inc.php'); 

include ('../_variables.php'); 

//IF NOT SIGN IN REDIRECT
if ( !$user->isAuthorized() ) {
	header("Location: sign-up.php");
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <base href="_parent">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="robots" content="noindex">
    <title><?php echo implode(' / ', $head_title); ?></title>
    <meta name="viewport" content="width=550, user-scalable=0">   
    <style type="text/css">
	     body {background:transparent;}
	</style> 

    <link rel="stylesheet" type="text/css" href="../css/vendor/socicon.css">
    <link rel="stylesheet" type="text/css" href="../css/vendor/entypo.css">
    <link rel="stylesheet" type="text/css" href="../css/modal.css">

    <?php if ($config->site->style->enable_cdn) { ?>
		<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.7/css/jquery.fileupload-ui.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.7/css/jquery.fileupload-ui-noscript.min.css">
		
    <?php }else { ?>
    
    	<link rel="stylesheet" type="text/css" href="../css/vendor/bootstrap.3.0.min.css">
		<link rel="stylesheet" type="text/css" href="css/jquery.fileupload-ui.css">
		<noscript><link rel="stylesheet" type="text/css" href="css/jquery.fileupload-ui-noscript.css"></noscript>

    <?php } ?>


    <?php if ($config->site->style->enable_cdn) { ?>
    	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <?php }else { ?>
	    <script src="../js/vendor/jquery-1.11.0.min.js"></script>
    <?php } ?>

	<script>
      
      if ( window.self === window.top ) { 
    
        //alert('Im NOT in a frame!');

      } else { //Loaded within an iframe.
      	
      	parent.$('#modal-choose .choose-container').css('height','500px');
        parent.$('#modal-choose .choose-container').css('max-height','500px');
        parent.$('#ChooseFrame').css('height','500px');
        
      }
      
      $(document).ready(function() { //iFrame document is loaded.
      
      	$('body').css('display','block');
       
      
      $(document).bind('dragover', function (e) {
		    var dropZone = $('#dropzone'),
		        timeout = window.dropZoneTimeout;
		    if (!timeout) {
		        dropZone.addClass('in');
		    } else {
		        clearTimeout(timeout);
		    }
		    var found = false,
		      	node = e.target;
		    do {
		        if (node === dropZone[0]) {
		       		found = true;
		       		break;
		       	}
		       	node = node.parentNode;
		    } while (node != null);
		    if (found) {
		        dropZone.addClass('hover');
		    } else {
		        dropZone.removeClass('hover');
		    }
		    window.dropZoneTimeout = setTimeout(function () {
		        window.dropZoneTimeout = null;
		        dropZone.removeClass('in hover');
		    }, 100);
		});
      
      });
      
    </script>
  
  <style>
  
  	html {
	  	overflow:hidden;
  	}
  	
    body {
    
        display: none;
        background-color: transparent;
    
    }
              
    p{
	    margin: 0;
    }
    
    .files .name {
      width: 185px;
    }
    
    
    .progress {
      margin-bottom: 0;
    }
    
        
    .table tbody>tr>td {
      border-top: none;
      vertical-align: middle;
    }
         
	
	.progress-extended{
	  text-align: center;
	  padding-top: 0px;
	}

	
	.start {
	display: none;
	}
   

  </style>
</head>
<body class="modal-body modal-upload">

<div class="container modal-container">
      
    <!-- The file upload form used as target for the file upload widget -->
    <form id="fileupload" action="//server/php/" method="POST" enctype="multipart/form-data" >
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <noscript><input type="hidden" name="redirect" value="<?php echo $config->site->url; ?>"></noscript>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
        
            <div class="col action-buttons">

                <!-- The fileinput-button span is used to style the file input field as button -->
                
                <div class="buttons add-image" id="upload-btn">
                  <span class="btn fileinput-button upload-icon-bg">
                      <i class="glyphicon glyphicon-plus"></i>
                      <input type="file" name="files[]" accept="image/*" multiple>
                  </span>
                  
                </div>
                
                <div class="buttons cancel-image" id="cancel-btn" style="display:none;">
                <button type="reset" class="btn cancel upload-icon-bg">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span></span>
                </button>                
                </div>
                
                <div class="buttons delete-image" id="delete-btn" style="display:none;">
                <button type="button" class="btn delete upload-icon-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span></span>
                </button>
                </div>
                
                <?php if ($config->site->media->enable_watermark) { ?>
                <div class="buttons watermark-image">
                <span class="btn">
                    <span><?php echo $langscape["Watermark Enabled"]; ?></span>
                </span>               
                </div>
                <?php } ?>
                
                 <input type="checkbox" class="toggle" checked="yes">
                <!-- The loading indicator is shown during file processing -->
                <span class="fileupload-loading"></span>
            </div>
            
            
            <!-- The global progress information -->
            <div class="col fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        
        <div class="col" id="dropzone">
          <!-- The table listing the files available for upload/download -->
         
        <div class="scroller">

         
         
        <table role="presentation" class="table table-striped">
          <tbody class="files">
        
          

          </tbody>
        </table>

        
        </div>
        
    </form>
    <br>
    
    <div id="uploaded-fields" style="text-align:center;">
      <form action="../upload-details.php" name="image-files" method="POST" target="_top">
        
        <button type="submit" class="btn-normal btn-primary start">
            <span>Next</span>
        </button>
        
      <form>
    </div>
</div>

<!-- The template to display files available for upload -->

<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            {% if (file.error) { %}
                <div><span class="label label-important">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <p class="size">{%=o.formatFileSize(file.size)%}</p>
            {% if (!o.files.error) { %}
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            {% } %}
        </td>
        <td>
            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn-normal btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn-normal btn-primary cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}

    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
            </p>
            {% if (file.error) { %}
                <div><span class="label label-important">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <button class="btn-normal btn-primary delete" data-type="{%=file.deleteType%}" data-id="{%=file.size%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="glyphicon glyphicon-trash"></i>
            </button>
            <input type="checkbox" name="delete" value="1" class="toggle" checked="yes">
        </td>
    </tr>
{% } %}
</script>
 

<script type="text/javascript">
//SET WATERMARK TRUE/FALSE

var watermark = '<?php echo $config->site->media->enable_watermark; ?>';
var watermark_path = '<?php echo $config->site->media->watermark; ?>';
var max_upload_filesize = <?php echo $config->site->media->max_filesize; ?>;

</script>

<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="js/load-image.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="js/jquery.fileupload-image.js"></script>
<!-- The File Upload validation plugin -->
<script src="js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="js/main.js"></script>

<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
</body> 
</html>