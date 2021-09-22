/*
 * jQuery File Upload Plugin JS Example 8.8.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, regexp: true */
/*global $, window, blueimp */

$(function () {
	'use strict';
	
		
	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url : 'server/php/'
	});

	// Enable iframe cross-domain access via redirect option:
	$('#fileupload').fileupload(
		'option',
		'redirect',
		window.location.href.replace(
			/\/[^\/]*$/,
			'/cors/result.html?%s'));

	$('#fileupload').fileupload('option', {
		autoUpload : true,
		maxFileSize : max_upload_filesize, //This is the max file size for uploads. NOTE: the php.ini value set on your server does not take this in to account.
		acceptFileTypes : /(\.|\/)(gif|jpe?g|png)$/i
	}, {
		add : function (e, data) {},
		progress : function (e, data) {},
		start : function (e) {},
		done : function (e, data) {},
		fail : function (e, data) {},
	}).bind('fileuploadadd', function (e, data) {
		console.log('fileuploadadd');
		$('.scroller').css('background', 'none');
		$('#cancel-btn').css('display', 'inline-block');
		console.log($('#fileupload')[0]);


	}).bind('fileuploadprogress', function (e, data) {}).bind('fileuploadstart', function (e) {}).bind('fileuploadfinished', function (e, data) { //Complete
		//console.log(data);
		$('#delete-btn').css('display', 'inline-block');

	}).bind('fileuploaddone', function (e, data) {

		var getFilesFromResponse = data.getFilesFromResponse;
		var files = getFilesFromResponse(data)
		var error = files[0].error;

		$('#cancel-btn').css('display', 'none');

		if (typeof error === 'undefined') {

			$(".start").css("display", "inline-block");
			var json = JSON.stringify(data.result.files[0]);
			var filename = data.files[0].name.substr(0, data.files[0].name.lastIndexOf('.'));
            
			var replaced = filename.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, ' ');
            
            replaced = toTitleCase(replaced);

            (function () {
	            if (watermark==1 && watermark_path) {
		            //console.log ('Watermark Enabled');
            		addWaterMark(data.result.files[0].name);
            		
            	}
            }());
                       
			$("#uploaded-fields form").append("<input type=\"hidden\" data-id=\"" + data.files[0].size + "\" data-name=\"" + data.files[0].name + "\" name=\"image-file[]\" class=\"" + replaced + "\" value=\"" + data.result.files[0].url + "\" />");

			$("#uploaded-fields form").append("<input type=\"hidden\" data-id=\"" + data.files[0].size + "\" data-name=\"" + data.files[0].name + "\" name=\"image-name[]\" class=\"" + replaced + "\" value=\"" + replaced + "\" />");

		}

	}).bind('fileuploaddestroy', function (e, data) {}).bind('fileuploaddestroyed', function (e, data) {

		var data_id = data.id;
		$('#uploaded-fields form input[data-id=' + data_id + ']').remove();

		if ($('#uploaded-fields form input').length == 0) {
			$('.start').css('display', 'none');
			$('#delete-btn').css('display', 'none');
		}

	});

});

function toTitleCase(str)
{
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

function addWaterMark(filename)
{
	//console.log('Processing Watermark ' + filename);
    $.ajax({
	    url: "server/php/watermark.php",
	    type: 'post',
	    data: {
		    filename: filename
		},
	    success: function(){
	      //console.log('Watermark added');
	    }
	});
}