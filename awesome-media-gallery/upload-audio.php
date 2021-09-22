<?php

require_once('_inc.php');
include ('_variables.php'); //Variables 

//IF NOT SIGN IN REDIRECT
if ( !$user->isAuthorized() ) {
	header("Location: sign-up.php");
}

?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <base href="<?php echo $config->site->url; ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="robots" content="noindex">
    <title><?php echo implode(' / ', $head_title); ?></title>
    <meta name="viewport" content="width=550, user-scalable=0">  
    
    <style type="text/css">
	     html {overflow:hidden;}
	     body {background:transparent;}
	</style>
	  
    <link rel="stylesheet" type="text/css" href="css/vendor/socicon.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/entypo.css">
    <link rel="stylesheet" type="text/css" href="css/modal.css">

    <?php if ($config->site->style->enable_cdn) { ?>
    	<link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <?php }else { ?>
    	<link rel="stylesheet" type="text/css" href="css/vendor/bootstrap.3.0.min.css">
    <?php } ?>

    <?php if ($config->site->style->enable_cdn) { ?>
    	<script type="application/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script type="application/javascript" src="http://connect.soundcloud.com/sdk.js" async></script>
    <?php }else { ?>
	    <script type="application/javascript" src="js/vendor/jquery-1.11.0.min.js"></script>
	    <script type="application/javascript" src="js/vendor/soundcloud-sdk.js" async></script>
    <?php } ?>
 
    <script type="application/javascript" src="js/modal.js"></script>
    
    <script>
    	uploadStarted = true;
        var ModalContainer = '.choose-container';
        var ModalInnerContainer = '.modal-container';
        var ModalFrame = '#ChooseFrame';

        window.onload = function() {
                
      	parent.$('#modal-choose .choose-container').css('height','300px');
        parent.$('#modal-choose .choose-container').css('max-height','300px');
        parent.$('#ChooseFrame').css('height','300px');

        }

      
        $(function () {
            'use strict';
            
            //var ModalContainer = '#modal-choose .choose-container';
            //var ModalInnerContainer = 'body';
            
            //parent.$("#modal-choose .choose-container").css('height', '400px');
            
            var isValid = false;
            var i = $('form .video-fields input[name="audio_url[]"]').size() + 1; //Set field count.

            $("#submit-audios").click(function( event ) { //Form submitted

                var x = 0;
            
                var count = $('form .video-fields').find('input[name="audio_url[]"]').length;
            
                var $url_inputs = $('form .video-fields');
            
                $url_inputs.each(function() {
                
                    var curr_id    = (i-1);
                    var audio_url  = $(this).find('input[name="audio_url[]"]').val(); //Assign input url as variable.
                   
                    var soundcloudUrl    = audio_url.match(/https?:\/\/(m\.)?(soundcloud.com|snd.sc)\/(.*)$/);
                    
                    $(this).prop('disabled', true);
                    
                    if (soundcloudUrl) { // Souncloud


						SC.initialize({
						  client_id: '<?php echo $config->site->soundcloud->app_id; ?>'
						});
						
						// permalink to a track
						var track_url = audio_url;
						
						SC.get('/resolve', { url: track_url }, function(track, error) {
						  		
						  		if (error) { 
							  		console.log('Error: ' + error.message);
							  		x++;
							  	} else {
								  	//console.log('Validated');
								  	//console.log('1:' + track.permalink_url);
								  	//console.log('2:' + track.title);
								  	//console.log('3:' + track.artwork_url.replace('large.jpg','t500x500.jpg'));
								  	//console.log('4:' + track.description);
								  	//console.log('5:' + track.id);
						  			x++;
							  		isValid == true;
							  		
							  		$url_inputs.find('#audio_url_' + x).val(track.permalink_url);
	                                $url_inputs.find('#title_' + x).val(track.title + ' - ' + track.user.username);
									
	                                $url_inputs.find('#soundcloud_id_' + x).val(track.id);
	                                
	                                if (track.artwork_url) {
	                       	    	    $url_inputs.find('#image_url_' + x).val(track.artwork_url.replace('large.jpg','t500x500.jpg'));
	                                } else if (track.user.avatar_url) {
	                        	        $url_inputs.find('#image_url_' + x).val(track.user.avatar_url);		                                
	                                } else  {
	                        	        $url_inputs.find('#image_url_' + x).val('<?php echo $config->site->url . $config->site->upload_path; ?>default-soundcloud.png');		                                
	                                }
	                                
	                                if (track.description) {
	                                	$url_inputs.find('#description_' + x).val(br2nl(track.description));
	                                } else if (track.user.username) {
		                            	$url_inputs.find('#description_' + x).val(track.user.username);
	                                } else {
		                            	$url_inputs.find('#description_' + x).val('');
	                                }
	                                submitCallback(count, x);
								}
						});
	
                        
                    } else { //Url is not Soundcloud.
                    
                        console.log('URL: ' + audio_url + ' is not valid.');
                        isValid == false;
                    }
                 
                });
                
                event.preventDefault();
                
            });
            
            
            $(document).keypress(function(e) { //Pressed enter, do nothing.
                if(e.which == 13) {
                    e.preventDefault();
                }
            });
            
            function submitCallback(count, x) {
                if (count == x) { //Loop has finished, time to submit for real..
                    $('form').hide();
                    $('form').submit();
                } else { 
                    //Not finished yet.
                }
            }
            
            var url_inputs = $('form');
            
            url_inputs.on('keyup', 'input[name="audio_url[]"]', function(ev){

	    
                if(validSoundcloud($(this).val())) {
                    
                    $('#submit-audios').show();
                    
                    //Run Height Setting Function
                    setNewHeight(ModalContainer, ModalInnerContainer, ModalFrame, 560);
                    
                } else {
                    
                    $('#submit-audios').hide();
                    
                    //Run Height Setting Function
                    //setNewHeight(ModalContainer, ModalInnerContainer, ModalFrame, 560);
                    
                }
            
            });
            
            
            
            
            $("form").submit(function()
                {
                 //alert('Form is submitting');
                 //return true;
                });
            
                
            $('#add').click(function() { //On click add new input field.

                var add_fields = '<div id="aud_' + i + '" class="video-fields">';
                add_fields += '<input type="text" id="audio_url_' + i + '" size="20" name="audio_url[]" placeholder="<?php echo $langscape["Audio URL"];?>" class="data input-text"></input><a href="#" id="remove" title="<?php echo $langscape["Delete audio link"];?>"><i class="video-modal glyphicon glyphicon-trash"></i></a>';
                add_fields += '<input type="hidden" name="title[]" id="title_' + i + '" placeholder="">';

                add_fields += '<input type="hidden" name="soundcloud_id[]" id="soundcloud_id_' + i + '" placeholder="">';

                add_fields += '<input type="hidden" name="image_url[]" id="image_url_' + i + '" placeholder="">';
                add_fields += '<input type="hidden" name="description[]" id="description_' + i + '" placeholder="">';
                add_fields += '</div>';
                
                $(add_fields).insertAfter("div #aud_" + (i-1));
                
                //Run Height Setting Function
                setNewHeight(ModalContainer, ModalInnerContainer, ModalFrame, 560);
                
                i++;
                
                return false;
            
            });
            
            
            $('body').on('click', '#remove', function() { //On click remove input field.
                
                if( i > 2 ) {
                
                    $(this).closest('div').remove();
                    
                    //Run Height Setting Function
                    setNewHeight(ModalContainer, ModalInnerContainer, ModalFrame, 560);
                    
                    i--;
                    
                }
                    
                return false;
            
            });
            
            

        });
    </script>
    
  </head>
  <body class="modal-body modal-audio">
    <div class="modal-container">
	    
	    <div class="audio-icons">
	        <i class="socicon socicon-soundcloud"></i>
	    </div>  
	
		<form action="upload-details.php" id="audio-form" name="image-files" method="post" target="_top" class="clear-fix standard standard-left">
		
			<fieldset>
			
				<div class="clear-fix form-field field-video form-field-text">
				
					<div class="input">
	                
	                    <div id="aud_1" class="video-fields">
				
	                        <input type="text" name="audio_url[]" id="audio_url_1" placeholder="<?php echo $langscape["Audio URL"];?>" class="data input-text" value="">
	                        <input type="hidden" name="title[]" id="title_1" placeholder="">
	                        <input type="hidden" name="image_url[]" id="image_url_1" placeholder="">
	                        <input type="hidden" name="soundcloud_id[]" id="soundcloud_id_1" placeholder="">
	                        <input type="hidden" name="description[]" id="description_1" placeholder="">
	                        <input type="hidden" name="type" value="audio">
				
	                    </div>
	                    
					</div>
				</div>
				
	            <a href="#" id="add" title="<?php echo $langscape["Add another audio link"];?>" class="upload-icon-bg">
		            <span class="btn-normal"><i class="glyphicon glyphicon-plus"></i></span>
		        </a>
					    
				<div class="clear-fix form-field field-login form-field-submit">
					<input type="button" value="<?php echo $langscape["Proceed"];?>" id="submit-audios" style="display:none;" class="btn-normal btn-primary">
				</div>
		
			</fieldset>
		</form>
    </div>
  </body>
</html>
