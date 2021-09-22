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
    <?php }else { ?>
	    <script type="application/javascript" src="js/vendor/jquery-1.11.0.min.js"></script>
    <?php } ?>
    <script type="application/javascript" src="js/vendor/modernizr.custom.min.js"></script>
    <script type="application/javascript" src="js/modal.js"></script>
    <style>

    html {
		overflow:hidden;
	}
    </style>
    <script>

        var ModalContainer = '.choose-container';
        var ModalInnerContainer = '.modal-container';
        var ModalFrame = '#ChooseFrame';

        window.onload = function() {

	      	parent.$('#modal-choose .choose-container').css('height','300px');
	        parent.$('#modal-choose .choose-container').css('max-height','300px');
	        parent.$('#ChooseFrame').css('height','280px');

        }


        $(function () {
            'use strict';

            //var ModalContainer = '#modal-choose .choose-container';
            //var ModalInnerContainer = 'body';

            //parent.$("#modal-choose .choose-container").css('height', '400px');

            var isValid = false;
            var i = $('form .video-fields input[name="video_url[]"]').size() + 1; //Set field count.

            $("#submit-videos").click(function( event ) { //Form submitted

                var x = 0;

                var count = $('form .video-fields').find('input[name="video_url[]"]').length;

                var $url_inputs = $('form .video-fields');

                $url_inputs.each(function() {

                    var curr_id    = (i-1);
                    var video_url  = $(this).find('input[name="video_url[]"]').val(); //Assign input url as variable.
                    var youtubeUrl = video_url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
                    var vimeoUrl   = video_url.match(/(?:vimeo(?:pro)?.com)\/(?:[^\d]+)?(\d+)(?:.*)/);
                    var vineUrl    = video_url.match(/^(http|https):\/\/?vine\.co\/v\/?\b.*$/);

                    $(this).prop('disabled', true);

                    if (youtubeUrl) { //Youtube Video

                        var video_id = youtubeUrl[1];

                        var request = $.ajax({
                            url: "https://www.googleapis.com/youtube/v3/videos?key=<?php echo $config->site->google->api_key; ?>&part=snippet&id=" + video_id,
                            dataType: 'json',
                            success: function(data) {

                                x++;
                                isValid == true;

                                var video_title = data.items[0].snippet.title; //Video Title
                                var video_desc  = data.items[0].snippet.description; //Video Description.
                                var video_thumb = data.items[0].snippet.thumbnails.standard?(data.items[0].snippet.thumbnails.standard.url):(data.items[0].snippet.thumbnails.high.url); //SD screen. 480x640

                                //console.log (video_thumb);
                                //GET THE MAXRESDEFAULT VERSION INSTEAD
                                ///var y = video_thumb;
                                ///y = y.substring(y.lastIndexOf("/")+1);
                                ///video_thumb = video_thumb.replace(y, 'maxresdefault.jpg');
                                //console.log (video_thumb);
                                //console.log (video_url + '-------');

                                $url_inputs.find('#video_url_' + x).val(video_url);
                                $url_inputs.find('#title_' + x).val(video_title);
                                $url_inputs.find('#image_url_' + x).val(video_thumb);
                                $url_inputs.find('#description_' + x).val(video_desc);

                            },
                            error: function(jqXHR, status, errorThrown){   //the status returned will be "timeout"

                                //console.log('Youtube ajax request failed.');
                                //console.log(errorThrown);
                                isValid == false;
                                //$(this).prop('disabled', false);
                            },
                            timeout: 10000, //10 second timeout
                            complete: function(jqXHR, status) {
                                //Call function to check if all ajax finished.
                                submitCallback(count, x);
                            }
                        });

                    } else if (vimeoUrl) { //Vimeo Video

                        var video_id = vimeoUrl[1];

                        var request = $.ajax({
                            url: 'http://www.vimeo.com/api/v2/video/' + video_id + '.json?callback=?',
                            dataType: 'json',
                            success: function(data) {

                                x++;
                                isValid == true;

                                var video_title = data[0].title; //Video Title
                                var video_desc  = data[0].description; //Video Description.
                                var video_thumb = data[0].thumbnail_large; //High Quality screen.

                                $url_inputs.find('#video_url_' + x).val('http://vimeo.com/' + video_id);
                                $url_inputs.find('#title_' + x).val(video_title);
                                $url_inputs.find('#image_url_' + x).val(video_thumb);
                                $url_inputs.find('#description_' + x).val(br2nl(video_desc));

                            },
                            error: function(jqXHR, status, errorThrown){   //the status returned will be "timeout"

                                if(jqXHR.status==404) {
                                    alert(thrownError);
                                }


                            },
                            timeout: 10000, //10 second timeout
                            complete: function(jqXHR, status) {
                                //Call function to check if all ajax finished.
                                submitCallback(count, x);
                            }
                        });

                    } else if (vineUrl) { //Vimeo Video

                        var video_id = vineUrl[0].split("/v/");

                        var request = $.ajax({
                            url: 'includes/vine-getInfo.php?video_id=' + video_id[1],
                            dataType: 'json',
                            success: function(data) {

                                x++;
                                isValid == true;

                                var video_title = data['title']; //Video Title
                                var video_desc  = data['description']; //Video Description.
                                var video_thumb = data['thumbnail_large']; //High Quality screen.
                                    //console.log('X is: ' + x);
                                $url_inputs.find('#video_url_' + x).val(video_url);
                                $url_inputs.find('#title_' + x).val(video_title);
                                $url_inputs.find('#image_url_' + x).val(video_thumb);
                                $url_inputs.find('#description_' + x).val(video_desc);

                            },
                            error: function(jqXHR, status, errorThrown){   //the status returned will be "timeout"

                                if(jqXHR.status==404) {
                                    alert(thrownError);
                                }

                                console.log('Vine ajax request failed.');
                                console.log(errorThrown);
                                isValid == false;

                            },
                            timeout: 10000, //10 second timeout
                            complete: function(jqXHR, status) {
                                //Call function to check if all ajax finished.
                                submitCallback(count, x);
                            }

                        });

                    } else { //Url is not Vimeo or Youtube.

                        console.log('URL: ' + video_url + ' is not valid.');
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

            url_inputs.on('keyup', 'input[name="video_url[]"]', function(ev){

                if(validVimeoYoutube($(this).val())) {

                    $('#submit-videos').show();

                    //Run Height Setting Function
                    setNewHeight(ModalContainer, ModalInnerContainer, ModalFrame, 560);

                } else {

                    $('#submit-videos').hide();

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

                var add_fields = '<div id="vid_' + i + '" class="video-fields">';
                add_fields += '<input type="text" id="video_url_' + i + '" size="20" name="video_url[]" placeholder="Vimeo / YouTube / Vine URL" class="data input-text"></input><a href="#" id="remove" title="<?php echo $langscape["Delete video link"];?>"><i class="video-modal glyphicon glyphicon-trash"></i></a>';
                add_fields += '<input type="hidden" name="title[]" id="title_' + i + '" placeholder="<?php echo $langscape["Title"];?>">';
                add_fields += '<input type="hidden" name="image_url[]" id="image_url_' + i + '" placeholder="<?php echo $langscape["Image URL"];?>">';
                add_fields += '<input type="hidden" name="description[]" id="description_' + i + '" placeholder="<?php echo $langscape["Image Description"];?>">';
                add_fields += '</div>';

                $(add_fields).insertAfter("div #vid_" + (i-1));

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
  <body class="modal-body modal-video">
    <div class="modal-container">

	    <div class="video-icons">
	        <i class="socicon socicon-vimeo"></i>

	        <i class="socicon socicon-youtube"></i>

	        <i class="socicon socicon-vine"></i>
	    </div>

		<form action="upload-details.php" id="video-form" name="image-files" method="post" target="_top" class="clear-fix standard standard-left">

			<fieldset>

				<div class="clear-fix form-field field-video form-field-text">

					<div class="input">

	                    <div id="vid_1" class="video-fields">

	                        <input type="text" name="video_url[]" id="video_url_1" placeholder="Vimeo / YouTube / Vine URL" class="data input-text" value="">
	                        <input type="hidden" name="title[]" id="title_1" placeholder="<?php echo $langscape["Title"];?>">
	                        <input type="hidden" name="image_url[]" id="image_url_1" placeholder="<?php echo $langscape["Image URL"];?>">
	                        <input type="hidden" name="description[]" id="description_1" placeholder="<?php echo $langscape["Image Description"];?>">
	                        <input type="hidden" name="type" value="video">

	                    </div>

					</div>
				</div>

	            <a href="#" id="add" title="<?php echo $langscape["Add another video link"];?>" class="upload-icon-bg">
		            <span class="btn-normal"><i class="glyphicon glyphicon-plus"></i></span>
		        </a>

				<div class="clear-fix form-field field-login form-field-submit">
					<input type="button" value="<?php echo $langscape["Proceed"];?>" id="submit-videos" style="display:none;" class="btn-normal btn-primary">
				</div>

			</fieldset>
		</form>

    </div>
  </body>
</html>
