/*
 * READ ONLY IF POSSIBLE - RE-DECLARE AND MODIFY IN CUSTOM.JS
 *
 * Awesome Image Galler: A social image/video gallery
 *
 * Inspired by Dribbble and 500px
 *
 * Copyright 2013 ENGAGE Inc. (http://en.gg/)
 *
 * Envato / CodeCanyon License Applies
 *
 * This file is still a work in progress. It will be cleaned and updated in the near future. Dec. 2013. Version 0.9
 *
 */
var uploadStarted = false;

var deviceAgent = navigator.userAgent.toLowerCase();

document.head || (document.head = document.getElementsByTagName('head')[0]);

/* GENERAL FUNCTIONS */

function changeFavicon(src) {
 var link = document.createElement('link'),
     oldLink = document.getElementById('dynamic-favicon');
 link.id = 'dynamic-favicon';
 link.rel = 'shortcut icon';
 link.href = src;
 if (oldLink) {
  document.head.removeChild(oldLink);
 }
 document.head.appendChild(link);
}

//PROFANITY FILTER
function censorString(str) {  
	var replacements = new Array();
	// create your array of bad words       
	var badwords = [
	'fuk',
	'fag',
	'tit',
	'cum',
	'jiz',
	'vag',
	'cunt',
	'kunt',
	'fuck',
	'piss',
	'twat',
	'slut',
	'dick',
	'jizz',
	'shit',
	'shlt',
	'sh1t',
	'cock',
	'c0ck',
	'gook',
	'g00k',
	'bitch',
	'whore',
	'wh0re',
	'wh0r3',
	'spick',
	'nigger',
	'n1gger',
	'n1gg3r'];
	
	var rg, BAD;
	for (var i=0; i<badwords.length; i++) {
        BAD = badwords[i];
        rg = new RegExp(BAD,"ig");
        str = str.replace(rg,fillChars('*',BAD.length));
  }
  return str;
}

function fillChars(chr,cnt) {
  var s = '*';
  for (var i=0; i<cnt; i++) { s += chr; }
  return s;
}

function str_ireplace (search, replace, subject) {
  var i, k = '';
  var searchl = 0;
  var reg;

  var escapeRegex = function (s) {
    return s.replace(/([\\\^\$*+\[\]?{}.=!:(|)])/g, '\\$1');
  };

  search += '';
  searchl = search.length;
  if (Object.prototype.toString.call(replace) !== '[object Array]') {
    replace = [replace];
    if (Object.prototype.toString.call(search) === '[object Array]') {
      // If search is an array and replace is a string,
      // then this replacement string is used for every value of search
      while (searchl > replace.length) {
        replace[replace.length] = replace[0];
      }
    }
  }

  if (Object.prototype.toString.call(search) !== '[object Array]') {
    search = [search];
  }
  while (search.length > replace.length) {
    // If replace has fewer values than search,
    // then an empty string is used for the rest of replacement values
    replace[replace.length] = '';
  }

  if (Object.prototype.toString.call(subject) === '[object Array]') {
    // If subject is an array, then the search and replace is performed
    // with every entry of subject , and the return value is an array as well.
    for (k in subject) {
      if (subject.hasOwnProperty(k)) {
        subject[k] = str_ireplace(search, replace, subject[k]);
      }
    }
    return subject;
  }

  searchl = search.length;
  for (i = 0; i < searchl; i++) {
    reg = new RegExp(escapeRegex(search[i]), 'gi');
    subject = subject.replace(reg, replace[i]);
  }

  return subject;
}

function ReplaceNumberWithCommas(yourNumber) {
    //Seperates the components of the number
    if (yourNumber > 0) {
	    var components = yourNumber.toString().split(".");
	    //Comma-fies the first part
	    components [0] = components [0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	    //Combines the two sections
	    return components.join(".");
	} else {
		return yourNumber;
	}
}  

function removeChars(string_name) {
	outString = string_name.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
	return outString;
}

/* USED TO CUSTOMIZE THE FACEBOOK POPUP WINDOW */
function fbShare(url, winWidth, winHeight) {
	var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
	window.open('http://www.facebook.com/sharer.php?s=100&p[url]=' + url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
}

//This can be used to turn logging on and off easily
var logger = function()
{
    var oldConsoleLog = null;
    var pub = {};

    pub.enableLogger =  function enableLogger() 
                        {
                            if(oldConsoleLog == null)
                                return;

                            window['console']['log'] = oldConsoleLog;
                        };

    pub.disableLogger = function disableLogger()
                        {
                            oldConsoleLog = console.log;
                            window['console']['log'] = function() {};
                        };

    return pub;
}();

var mokoala = {
	Utility : {
		numberFormat : function (integer) {
			integer += '';
			x = integer.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		},
		nl2br : function (str, is_xhtml) {
			var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
			return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
		}
	},
	API : {
		api_base : 'api.php',
		api_auth : 'signed_sha256',
	
		load : function (parameters, callback) {
			var parameters = parameters || {};
			var callback = callback || function () {};
	
			var timestamp = Math.round(new Date().getTime() / 1000);
			var url = this.api_base;
	
			var signature = timestamp.toString();
			var signature = CryptoJS.SHA256(signature);
			var signature = signature.toString();
	
			parameters.timestamp = timestamp;
			parameters.signature = signature;
	
			if (url_parameters = this.buildQuery(parameters)) {
				url += '?' + url_parameters;
			}
	
			this.request(url, callback);
	
			return this;
		},
	
		buildQuery : function (formdata, numeric_prefix, arg_separator) {
			var value,
			key,
			tmp = [],
			that = this;
	
			var _http_build_query_helper = function (key, val, arg_separator) {
				var k,
				tmp = [];
				if (val === true) {
					val = "1";
				} else if (val === false) {
					val = "0";
				}
	
				if (val != null) {
					if (typeof(val) === "object") {
						for (k in val) {
							if (val[k] != null) {
								tmp.push(_http_build_query_helper(key + "[" + k + "]", val[k], arg_separator));
							}
						}
	
						return tmp.join(arg_separator);
					} else if (typeof(val) !== "function") {
						return encodeURIComponent(key) + "=" + encodeURIComponent(val);
					} else {
						throw new Error('There was an error processing your object.');
					}
				} else {
					return '';
				}
			};
	
			if (!arg_separator) {
				arg_separator = "&";
			}
	
			for (key in formdata) {
				value = formdata[key];
				if (numeric_prefix && !isNaN(key)) {
					key = String(numeric_prefix) + key;
				}
	
				var query = _http_build_query_helper(key, value, arg_separator);
	
				if (query != '') {
					tmp.push(query);
				}
			}
	
			return tmp.join(arg_separator);
		},
	
		request : function (url, callback, post_data) {
	
			var XMLHttpFactories = [
				function () {
					return new XMLHttpRequest()
				},
				function () {
					return new ActiveXObject("Msxml2.XMLHTTP")
				},
				function () {
					return new ActiveXObject("Msxml3.XMLHTTP")
				},
				function () {
					return new ActiveXObject("Microsoft.XMLHTTP")
				}
			];
	
			var req = false;
			for (var i = 0; i < XMLHttpFactories.length; i++) {
				try {
					req = XMLHttpFactories[i]();
				} catch (e) {
					continue;
				}
				break;
			}
	
			if (!req)
				return;
			NProgress.start();
			var method = post_data ? "POST" : "GET";
			req.open(method, url, true);
			NProgress.inc();
			if (post_data) {
				req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			}
	
			req.onreadystatechange = function () {
				if (req.readyState != 4)
					return;
				if (req.status != 200 && req.status != 304) {
					return;
				}
				//console.log ('responsetext' + req.responseText);
				var data = JSON.parse(req.responseText);
				callback(data);
				NProgress.done();
			}
	
			if (req.readyState == 4) {
	
				return;
			}
	
			req.send(post_data);
		}
	
	}
}; /* finish MOKOALA */

$(document).ready(function() {

	logger.enableLogger(); //logger.disableLogger();
	$(".main-container").fitVids();
	$('.gallery-nav .not-visible').removeClass('not-visible');
	$('.media-select').fancySelect();
	$('.js-gallery-all').removeClass('hidden');
    
    //Tweak to Reload Choose Frame
    $('a[data-modal="modal-choose"]').click(function() {
		uploadStarted = true;
    });
    
	/*{{{ Scroll to top */
	(function () {
		var settings = {
			text : 'To Top',
			min : 200,
			inDelay : 600,
			outDelay : 400,
			containerID : 'toTop',
			containerHoverID : 'toTopHover',
			scrollSpeed : 400,
			easingType : 'linear'
		};
		var containerIDhash = '#' + settings.containerID;
		var containerHoverIDHash = '#' + settings.containerHoverID;

		$('body').append('<a href="#" id="' + settings.containerID + '" onclick="return false;"><i class="arrow-up-4 icon to-top-arrow"></i></a>');
		$(containerIDhash).hide().click(function () {
			$('html, body').animate({
				scrollTop : 0
			}, settings.scrollSpeed, settings.easingType);
			$('#' + settings.containerHoverID, this).stop().animate({
				'opacity' : 0
			}, settings.inDelay, settings.easingType);
			return false;
		})
		.prepend('<span id="' + settings.containerHoverID + '"></span>')
		.hover(function () {
			$(containerHoverIDHash, this).stop().animate({
                    'opacity' : .75
                }, 600, 'linear');
            }, function () {
                $(containerHoverIDHash, this).stop().animate({
                    'opacity' : 0
                }, 700, 'linear');
            });

		$(window).scroll(function () {
			var sd = $(window).scrollTop();
			if (typeof document.body.style.maxHeight === "undefined") {
				$(containerIDhash).css({
					'position' : 'absolute',
					'top' : $(window).scrollTop() + $(window).height() - 50
				});
			}
			if (sd > settings.min) {
				$(containerIDhash).fadeIn(settings.inDelay);
			} else {
				$(containerIDhash).fadeOut(settings.outDelay);
			}
		});

	})();
	/*}}}*/
    
    /* SINGLE MEDIA PAGE TAGS MANAGER */

    $('input[name=hidden-tags]').on('change', function() {
	    var this_field  = $(this);
	    var input_field = $('.tm-input');
	    
		var data_id     = input_field.attr('data-id');
		var field_name  = input_field.attr('data-field-name');
		var module_name = input_field.attr('data-module-name');

		var d           = new $.Deferred;
		var params      = {};
		var value       = $(this).val();
		console.log(value);
		
		//validate entries
		value           = value.replace(/[`~!@#$%^&*()_|+=?;:'".<>\{\}\[\]\\\/]/gi, '');
		value           = value.replace("http://", "");
		value           = value.replace("https://", "");
		value           = value.replace("www.", "");
		console.log(value);
		
		params[field_name] = value;
		
		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax

			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above moule that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				
				d.resolve();

			});

			return d.promise();
		} //End Ajax
	    
	});

	//single media page download button
	$('#download-image').click(function(e){
		e.preventDefault();
        downloadFile('image-download.php?a=' + $(this).attr('data-img'));  
    })	
	
	function downloadFile(url)
    {
        var iframe;
        iframe = document.getElementById("download-container");
        if (iframe === null)
        {
            iframe = document.createElement('iframe');  
            iframe.id = "download-container";
            iframe.style.visibility = 'hidden';
            document.body.appendChild(iframe);
        }
        iframe.src = url;   
    }
	
	    
    $('#js-clear-titles').on('click', function () {
    
        $( ".js-upload-form input[name*='title']" ).val( "" );
    
    });
    
    $('#js-tags-button').on('click', function () {
    
        add_tag = $('.js-upload-all-tags').val().replace (/,/g, ""); //Get the value from the input box.
            
        $(".js-upload-form input.tm-input").each(function() { //Loop though each of the tag fields
            
                add_tag = add_tag.replace(/[`~!@#$%^&*()_|+=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(add_tag);
	            var ev = jQuery.Event("keydown");
				ev.which = 13; // # Some key code value
				$(this).focus().trigger(ev);

        });
        
        $('.js-upload-all-tags').val('').focus(); //Focus back to the main tag field.
        
    });     
      
    $('.js-upload-all-tags').on('keydown',function( e ){
    
        if(/(188|13)/.test(e.which)) { //true, comma or enter
            
            add_tag = $('.js-upload-all-tags').val().replace (/,/g, ""); //Get the value from the input box.
            //console.log (add_tag);

            $(".js-upload-form input.tm-input").each(function() { //Loop though each of the tag fields
                
                add_tag = add_tag.replace(/[`~!@#$%^&*()_|+=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(add_tag);
	            var ev = jQuery.Event("keydown");
				ev.which = 13; // # Some key code value
				$(this).focus().trigger(ev);

            });
            

            //var ev = jQuery.Event('keydown', {keyCode: 144});
            //$(".js-upload-form input.tm-input").trigger(ev);
            
            $('.js-upload-all-tags').val('').focus(); //Focus back to the main tag field.
            
        } else { //false
            
        }
    });
    
    
    $('#js-upload-desc-button').on('click', function () {
    
        var desc_text = $( "#js-upload-desc-text" ).val();
        
        $( ".js-upload-form textarea[name*='description']" ).val( desc_text );
    
    });

    $('#js-upload-desc-text').on('keydown',function( e ) {
        if(/(13)/.test(e.which)) { //true, comma or enter
    
	        var desc_text = $( "#js-upload-desc-text" ).val();
	        
	        $( ".js-upload-form textarea[name*='description']" ).val( desc_text );
		}
    });

	//Change the values based on main gallery drop down.
	$(".js-gallery-all").on('change', function () {
		$(".form-field-select .input-select").val($(this).val());
	}); 
    
    //Remove the element on the upload form.
	$(".remove-image").on('click', function (e) {
		e.preventDefault();
		//if (window.confirm("Are you sure?")) {
			$(this).closest("li").fadeOut(1000, function () {
				$(this).closest("li").remove();
				var n = $(".awesome-gallery li").length;

				if (n == 0) { //Nothing left.
					$("#upload-button").click();
					$('.category-wrapper').remove();
					$('.field-submit').remove();
					$('.field-cancel').remove();
				}
			});
		//}
	});

	//CHANGE THE CROP OF THE IMAGE ON THE UPLOAD FORM
	$('.crop-top').on('ifToggled', function (e) {
		
		button = $(this);
		//console.log(button[0].checked);
		
		
		if ( button[0].checked == true ) {
			
			button.closest("li").find("img").attr( "src", button.closest("li").find(".crop-top-url").val() );
			console.log ("A" + button.closest("li").find(".crop-top-url").val());

		} else {
			
			button.closest("li").find("img").attr( "src", button.closest("li").find(".crop-url").val() );
			console.log ("B" + button.closest("li").find(".crop-url").val());
		
		}
	
	});


	$('.user-profile-nav .pure-button').on('click', function () {

		//unselect all buttons
		$('.user-profile-nav .pure-button').removeClass("pure-button-active");

		//select specific button
		if ($(this).hasClass('pure-button-active')) {
			$(this).removeClass("pure-button-active");
		} else {
			$(this).addClass("pure-button-active");
		}

	});

	//LINKIFY SCRIPT
	(function ($) {

		var url1 = /(^|&lt;|\s)(www\..+?\..+?)(\s|&gt;|$)/g,
		url2 = /(^|&lt;|\s)(((https?|ftp):\/\/|mailto:).+?)(\s|&gt;|$)/g,

		linkifyThis = function () {
			var childNodes = this.childNodes,
			i = childNodes.length;
			while (i--) {
				var n = childNodes[i];
				if (n.nodeType == 3) {
					var html = $.trim(n.nodeValue);
					if (html) {
						html = html.replace(/&/g, '&amp;')
							.replace(/</g, '&lt;')
							.replace(/>/g, '&gt;')
							.replace(url1, '$1<a href="http://$2">$2</a>$3')
							.replace(url2, '$1<a href="$2">$2</a>$5');
						$(n).after(html).remove();
					}
				} else if (n.nodeType == 1 && !/^(a|button|textarea)$/i.test(n.tagName)) {
					linkifyThis.call(n);
				}
			}
		};

		$.fn.linkify = function () {
			return this.each(linkifyThis);
		};

	})(jQuery);

	jQuery('div.meta-about').linkify();
	jQuery('span.meta-skills').linkify();
	jQuery('span.meta-software').linkify();
	jQuery('.cbp_tmtimeline p').linkify();

	//DISABLE LINKS WHEN EDITTING
	$("a.edit").bind("click", function (event) {
		event.preventDefault();
		return false;
	});

	$(".edit-gallery a").bind("click", function (event) {
		event.preventDefault();
		return false;
	});    

	$('.edit-link').editable(function (value, settings) {

		var this_field  = $(this);
		var data_id     = $(this).attr('data-id');
		var field_name  = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var field_url   = $(this).attr('data-field-url');
		var d           = new $.Deferred;
		var params      = {};
		var regex       = new RegExp('"', 'g');
		value           = value.replace(regex, "'");
		value           = value.replace("http://", "");
		value           = value.replace("https://", "");
		value           = value.replace("www.", "");

		//remove blanks from code
		value = value.replace(/ /g,'');

		
		params[$(this).attr('data-field-name')] = value;
		
		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax

			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above moule that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				d.resolve();

				if (field_name == 'demo_reel_url') {
					//console.log('DEMO REEL VALUE:' + data.body[field_name]);

					if (data.body[field_name] != '') {

						$("a.demo-reel-link").addClass('js-show');

						$("a.demo-reel-link").attr("href", data.body[field_name]);

					} else {

						$("a.demo-reel-link").removeClass('js-show');
					}

				}
				
				if (data.body[field_name] == undefined || data.body[field_name] == '') {
					data.body[field_name] = txt_placeholder_arr[field_name];
					this_field.attr('data-field-url', '');
				} else {
					this_field.attr('data-field-url', data.body[field_name]);
				}

				this_field.text(data.body[field_name]); //Update the field with the new value.
				link_flag = 0;

				if (field_name == 'display_name') { //Update the username in the navigation bar.
					$("#user-name").text(data.body[field_name]);
				}

			});

			return d.promise();
		} //End Ajax

	}, {
		type : 'text',
		style : 'display: inline;',
		placeholder : 'Saving...',
		onblur : 'submit',
		data : function (value, settings) {
			return $(this).attr('data-field-url');
		}
	});

	$('.edit-text').editable(function (value, settings) {

		var this_field = $(this);
		var data_id = $(this).attr('data-id');
		var field_name = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var field_text = $(this).attr('data-field-text');
		var d = new $.Deferred;
		
		
		//DONT ALLOW ANY SPECIAL CHARACTERS
		if ((field_name == "title") || (field_name == "display_name") || (field_name == "username")) {
			if (value == '') {
				value = field_text;
			}else {
				value = removeChars(value);
			}
		}
		
		//Quick Overflow Fix / Should also update MAXLENGTH on INPUT / JEDITABLE
		if ((field_name == "username") || (field_name == "region")) {
			if (value == '') {
				value = field_text;
			} else {
				value = value.substring(0,22);
			}
		}
		
		//USERNAME REMOVE WHITESPACE - JUST MAKE SURE
		if ((field_name == "username")) {
			if (value == '') {
				value = field_text;
			} else {
				value = value.replace(/\s+/g, ' ');
			}
		}
		
		//CENSOR TEXT
		value = censorString(value);

		var params = {};
		value = value.replace('"', "'");
		value = value.replace("http://", "");
		value = value.replace("https://", "");

		//params['user'] = data_id;
		params[$(this).attr('data-field-name')] = value;

		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax

			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above moule that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				if (data.body[field_name] == '') {
					data.body[field_name] = txt_placeholder_arr[field_name];
					this_field.attr('data-field-text', '');
				} else {
					this_field.attr('data-field-text', data.body[field_name]);
				}
				d.resolve();
				if (data.body[field_name] == '') {
					data.body[field_name] = txt_placeholder_arr[field_name];
				}
				this_field.text(data.body[field_name]); //Update the field with the new value.

				if (field_name == 'display_name') { //Update the username in the navigation bar.
					$("#user-name").text(data.body[field_name]);
				}

			});
			return d.promise();
		} //End Ajax

	}, {
		type : 'text',
		style : 'display: inline;',
		placeholder : 'Saving...',
		onblur : 'submit',
		data : function (value, settings) {
			return $(this).attr('data-field-text');
		}
	});

	$('.edit-textarea').editable(function (value, settings) {

		var this_field  = $(this);
		var data_id     = $(this).attr('data-id');
		var field_name  = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var field_text  = $(this).attr('data-field-text');
		var d           = new $.Deferred;
		var regex       = new RegExp('"', 'g');
		value           = value.replace(regex, "'");
        
		//FIX FOR HOSTGATOR SERVERS
		value           = value.replace("http://", "{{link}}");
		value           = value.replace("https://", "{{link}}");
		
		value           = censorString(value); //CENSOR TEXT

		var params      = {};

		params[$(this).attr('data-field-name')] = value;

		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax
			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above module that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				if (data.body[field_name] == '') {
					data.body[field_name] = txt_placeholder_arr[field_name];
					this_field.attr('data-field-text', '');
				} else {
					this_field.attr('data-field-text', data.body[field_name]);
				}
				d.resolve();
				if (data.body[field_name] == '') {
					data.body[field_name] = txt_placeholder_arr[field_name];
				}
				this_field.text(data.body[field_name]); //Update the field with the new value.

			});
			return d.promise();
		} //End Ajax

	}, {
		type : 'textarea',
		submit : 'Save',
		cancel : 'Cancel',
		style : '',
		placeholder : 'Saving...',
		onblur : 'ignore',
		inputclass : 'textarea-about',
		rows : 12,
		width : 400,
		data : function (value, settings) {
			return $(this).attr('data-field-text');
		}
	});

	$('.edit-gender').editable(function (value, settings) {

		var this_field  = $(this);
		var data_id     = $(this).attr('data-id');
		var field_name  = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var d           = new $.Deferred;
		var params      = {};
        
		params[$(this).attr('data-field-name')] = value;
		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax
			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above moule that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				d.resolve();
				if (data.body[field_name] == '') {
					data.body[field_name] = txt_placeholder_arr[field_name];
				}
				this_field.text(data.body[field_name]); //Update the field with the new value.
			});
			return d.promise();
		} //End Ajax

	}, {
		data : " {'Male':'Male','Female':'Female','Not Specified':'Not Specified'}",
		type : 'select',
		onblur : 'submit',
		style : 'display: inline;',
		placeholder : 'Saving...'
	});

	$('.edit-gallery').editable(function (value, settings) {
		console.log('click');
	
		var this_field  = $(this);
		var data_id     = $(this).attr('data-id');
		var field_name  = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var d           = new $.Deferred;
		var params      = {};

		params[$(this).attr('data-field-name')] = value;

		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax
			mokoala.API.load(
			{
				module : module_name,
				id : data_id, // Id of the record in the above module that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				d.resolve();
				this_field.text(data.body.gallery.name); //Update the field with the new value.
			}
			);
			return d.promise();
		} //End Ajax

	}, {

		data : galleries_data,
		type : 'select',
		onblur : 'submit',
		style : 'display: inline;',
		placeholder : 'Saving...'
	});

	$('.edit-category').editable(function (value, settings) {

		var this_field  = $(this);
		var data_id     = $(this).attr('data-id');
		var field_name  = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var d           = new $.Deferred;
		var params      = {};

		params[$(this).attr('data-field-name')] = value;

		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax
			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above moule that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				d.resolve();

				this_field.text(data.body.category.title); //Update the field with the new value.
				
			});
			return d.promise();
		} //End Ajax

	}, {

		data : users_types_data,
		type : 'select',
		onblur : 'submit',
		style : 'display: inline;',
		placeholder : 'Saving...'

	});

	$('.edit-yesno').editable(function (value, settings) {

		var this_field  = $(this);
		var data_id     = $(this).attr('data-id');
		var field_name  = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var d           = new $.Deferred;

		var params = {};
		//params['user'] = data_id;
		params[$(this).attr('data-field-name')] = value;

		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax
			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above moule that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				d.resolve();
				$yn = (data.body[field_name] == 1) ? 'Yes' : 'No';
				this_field.text($yn); //Update the field with the new value.
			});
			return d.promise();
		} //End Ajax


	}, {
		data : " {'1':'Yes','0':'No'}",
		type : 'select',
		onblur : 'submit',
		style : 'display: inline;',
		placeholder : 'Saving...'
	});

	$('.edit-yesno-freelance').editable(function (value, settings) {

		var this_field  = $(this);
		var data_id     = $(this).attr('data-id');
		var field_name  = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var d           = new $.Deferred;
		var params      = {};
		//params['user'] = data_id;
		params[$(this).attr('data-field-name')] = value;

		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax
			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above moule that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				d.resolve();
				$yn = (data.body[field_name] == 1) ? 'Yes' : 'No';
				this_field.text($yn); //Update the field with the new value.
			});
			return d.promise();
		} //End Ajax


	}, {
		data : " {'1':'Yes','0':'No'}",
		type : 'select',
		onblur : 'submit',
		style : 'display: inline;',
		placeholder : 'Saving...'
	});

	$('.edit-date').editable(function (value, settings) {

		var this_field  = $(this);
		var data_id     = $(this).attr('data-id');
		var field_name  = $(this).attr('data-field-name');
		var module_name = $(this).attr('data-module-name');
		var d           = new $.Deferred;
		var params      = {};
		//params['user'] = data_id;
		params[$(this).attr('data-field-name')] = value;

		if (value === 'abc') {
			return d.reject('error message'); //returning error via deferred object
		} else { //Run the Ajax
			mokoala.API.load({
				module : module_name,
				id : data_id, // Id of the record in the above moule that you want to edit
				fields : params,
				action : 'update'
			},
				function (data) { //Success!!!!
				this_field.addClass('animated flash');
				d.resolve();
				this_field.text(data.body[field_name]); //Update the field with the new value.
				// $("#user-name").text(data.body.display_name); //Update the username in the navigation bar.
			});
			return d.promise();
		} //End Ajax

	}, {

		format : 'yyyy-mm-dd',
		viewformat : 'dd/mm/yyyy',
		datepicker : {
			weekStart : 1
		},
		placeholder : 'Saving...'

	});

	$('#dob').editable({
		format : 'yyyy-mm-dd',
		viewformat : 'dd/mm/yyyy',
		datepicker : {
			weekStart : 1
		}
	});

	$('.edit-date2').editable('/post-years', {
		format : 'YYYY-MM-DD',
		viewformat : 'DD.MM.YYYY',
		template : 'D / MMMM / YYYY',
		type : 'select',
		submit : 'Ok',
		cancel : 'Cancel',
		style : 'display: inline;',
		placeholder : 'Add Date Of Birth'
	});
    
    
//} IS TOUCH DEVICE

	//SHOW CARROSEL
	$('.related-images img').css("display", "inline");

	//Launch File Upload on click of avatar!
	$('.profile-avatar-wrap .drag-hover').click(function (e) {
		//console.log('Avatar Image Clicked!!');
		$('#avatar-img').click();
	});

	//Launch File Upload on click of cover photo!
	$('.profile-photo-wrap .alert').click(function (e) {
		//console.log('Cover Photo Image Clicked!!');
		$('#photo-img').click();
	});

	//Launch File Upload on click of cover photo!
	$('#js-my-photo').click(function (e) {
		//console.log('Cover Photo Image Clicked!!');
		$('#photo-img').click();
	});
	
	$(".textarea-about").focus(function () { //Listen for focus!
		//console.log('Focusing in a textarea!');
		var str = $(this).html();
		var regex = /<br\s*[\/]?>/gi;
		$(this).html(str.replace(regex, "\n"));
	});



	//Avatar Upload
		
	$('#fileupload-avatar').fileupload({
	    acceptFileTypes : '/(\.|\/)(gif|jpe?g|png)$/i',
	    dataType : 'json',
	    done : function (e, data) {
	        var error = data.result.files[0].error;
	        
	        if (typeof error === 'undefined') { //Only proceed if no error.
	            
	            data.formData = $('#fileupload-avatar').serializeArray();
	            $.each(data.result.files, function (index, file) {
	                main.Avatar.sendAvatar(data);
	            });
	        }
	
	    }
	
	});
	
	$('#fileupload-photo').fileupload({
	    acceptFileTypes : '/(\.|\/)(gif|jpe?g|png)$/i',
	    dataType : 'json',
	    done : function (e, data) {
	        var error = data.result.files[0].error;
	        
	        if (typeof error === 'undefined') { //Only proceed if no error.
	            
	            data.formData = $('#fileupload-photo').serializeArray();
	            $.each(data.result.files, function (index, file) {
	                main1.Cover.sendCover(data);
	            });
				
	        }
	
	    }
	    
	
	});
	
	var main1 = {
	    Cover : {
	        sendCover : function (data) {
	            var file_name = 'tpl/uploads/' + data.files[0].name;
	            var data_id = data.formData[0].value;
	
	            var params = {};
	            params['cover_photo'] = file_name;
	
	            mokoala.API.load({
	                request : [
	                    'cover_photo'
	                ],
	                module : 'user',
	                id : data_id, //Id of the record in the above moule that you want to edit
	                fields : params,
	                action : 'update'
	            },
	                function (data) { //Success!!!!
	                	location.reload();
	                //var mini_avatar = 'library/thumb.php?f='+file_name+'&amp;h=24&amp;w=24&amp;m=crop';
	                //$(".mini-avatar img").attr("src", mini_avatar);
	            });
	        }
	    }
	};
	
	var main = {
	    Avatar : {
	        sendAvatar : function (data) {
	            var file_name = 'tpl/uploads/' + data.files[0].name;
	            var data_id = data.formData[0].value;
				
	            var params = {};
	            params['avatar'] = file_name;
	
	            mokoala.API.load({
	                request : [
	                    'avatar'
	                ],
	                module : 'user',
	                id : data_id, //Id of the record in the above moule that you want to edit
	                fields : params,
	                action : 'update'
	            },
	                function (data) { //Success!!!!
	                	location.reload();
	                //var mini_avatar = 'library/thumb.php?f='+file_name+'&amp;h=24&amp;w=24&amp;m=crop';
	                //$(".mini-avatar img").attr("src", mini_avatar);
	            });
	        }
	    }
	};
});
$(document).bind('ready', function () {	
	//COMMENTS
	$('div.reply form').bind('submit', function (e) {

		var form = $(this);
		var comment = form.find('[name$=_comment]').val();
		var user = form.find('[name$=_user]').val();
		var image = form.find('[name$=_image]').val();
		var reply_to = form.find('[name$=_reply_to]').val();

		//CENSOR COMMENT
		comment = censorString(comment);

		//FIX LINKS FOR HOSTGATOR
		comment= comment.replace("http://", "{{link}}");
		comment= comment.replace("https://", "{{link}}");

		if (comment=='') {
			return false;
		}
		
		form
		.fadeTo(500, .5)
		.find('[name$=_post-reply]')
		.attr('disabled', 'disabled');

		mokoala.API.load({
			module : 'image_comment',
			fields : {
				comment : comment,
				user : ( user ? user : 0 ),
				image : image,
				reply_to : reply_to
			},
			action : 'add'
		},
			function (data) {
			var comment_object = data.body;
			var container = form.closest('li');

			var container_content = '<li class="comment-new" id="comment-' + comment_object.id + '">';
			container_content += '<div class="cbp_tmicon">';
			container_content += '<img src="library/thumb.php?f=' + (comment_object.user.avatar ? comment_object.user.avatar : 'tpl/uploads/default-avatar.png') + '&amp;m=crop&amp;w=100&amp;h=100">';
			container_content += '</div>';
			container_content += '<div class="reply">';
			container_content += '<div class="cbp_tmlabel">';
			container_content += '<span class="user">';
			if( user )
			{
			container_content += '<a class="username-wrap" href="members.php?user=' + comment_object.user.id + '">' + comment_object.user.display_name + '</a>';
			}
			else
			{
				container_content += 'Guest';
			}
			container_content += '<time class="cbp_tmtime" datetime=""><span>Just now</span></time>';
			container_content += '<span rel="image-comment add-like" data-image-comment-likes-total="0" data-image-comment-id="' + comment_object.image.id + '" data-user-id="' + comment_object.user.id + '" class="pure-u meta-hearts stat"></span>';
			
			if( user )
			{
			container_content += '<span data-comment-id="' + comment_object.id + '" rel="comment delete-comment" class="delete-comment new"><span>Delete</span></span>';
			}
			
			container_content += '</span>';
			container_content += '<p>' + mokoala.Utility.nl2br(comment_object.comment) + '</p>';
			container_content += '</div>';
			container_content += '</div>';
			container_content += '</li>';

			container.html('');
			container.after(container_content);
			li_tag = $("#comment-" + comment_object.id).parents('li.has-replies');
			li_tag.addClass("on");
		});

		return false;
	});

	$('form.comment').bind('submit', function (e) {

		var form = $(this);
		var comment = form.find('[name=comment]').val();
		var user = form.find('[name=user]').val();
		var image = form.find('[name=image]').val();
		var container = form.closest('li');
		
		//CENSOR COMMENT
		comment = censorString(comment);
		
		//FIX LINKS FOR HOSTGATOR
		comment= comment.replace("http://", "{{link}}");
		comment= comment.replace("https://", "{{link}}");
		
		if (comment=='') {
			return false;
		}
		
		form
		.fadeTo(500, .5)
		.find('[name=post-comment]')
		.attr('disabled', 'disabled');

		mokoala.API.load({
			module : 'image_comment',
			fields : {
				comment : comment,
				user : ( user ? user : 0 ),
				image : image
			},
			action : 'add'
		},
			function (data) {
			var comment_object = data.body;
			var container_content = '<span class="user">';

			container_content += '<a class="class="username-wrap" href="members.php?user=' + comment_object.user.id + '">' + comment_object.user.display_name + '</a>';
			container_content += '<time class="cbp_tmtime" datetime=""><span>Just now</span></time>';
			container_content += '<span rel="image-comment add-like" data-image-comment-likes-total="0" data-image-comment-id="' + comment_object.image.id + '" data-user-id="' + comment_object.user.id + '" class="pure-u meta-hearts stat"></span>';

			if( user )
			{
			container_content += '<span data-comment-id="' + comment_object.id + '" rel="comment delete-comment" class="delete-comment new"><span>Delete</span></span>';
			}
		
			container_content += '</span>';
			container_content += '<p>' + mokoala.Utility.nl2br(comment_object.comment) + '</p>';

			form.after(container_content);
			form.remove();

			if ($('ul.cbp_tmtimeline').length) {
				//console.log('ul.cbp_tmtimeline has a length!');
				//$('ul.cbp_tmtimeline').append(container_content);
				//container.html('');

				//container.after(container_content);
				//container.before('After!!!');
			} else {
				//$('#comments').next().remove();
				//var comments_list = $('<ul class="cbp_tmtimeline"></ul>');
				//comments_list.append(container_content);
				//$('#comments').after(comments_list);
			}
		});
		//console.log('2');
		return false;
	});

	$('div.form-field-textmultiple').each(function () {
		var object = $(this);
		var link = $('<a href="">Add Another URL</a>');
		var paragraph = $('<p></p>').append(link);

		link.bind('click', function () {
			var new_input = object.find('input').first().clone().val('');
			paragraph.before(new_input);
			return false;
		});

		object.append(paragraph);
	});

	$('ul[data-autoload="true"]').each(function () {
		var list = $(this);
		var paginator = $('.paginator');
		var paginator_next = paginator.find('[rel~="paginator"][rel~="next"]');

		if (paginator.length && paginator_next.length) {
			paginator_next = paginator_next.find('a').attr('href');
			paginator.remove();

			var load_more = $('<button data-source="' + paginator_next + '" class="pure-button pure-button-primary load-more ladda-button" data-style="slide-up"><span class="ladda-label">Load More</span></button>');

			$(document).on('click', '.load-more', function () {
				if (!load_more.is('.loading')) {
					$(this)
					.addClass('loading')
					.find('span').text('Loading...');
					
					if (typeof(waypoint) === 'object') {
						waypoint.disable();
					}
					
					paginator_next = $(this).attr('data-source');

					$.ajax({
						url : paginator_next,
						success : function (html) {
							var $html = $(html);
							var paginator_next = $html.find('.paginator li[rel~="paginator"][rel~="next"] a').attr('href');

							load_more
							.removeClass('loading')
							.find('span').text('Load More');
							
							if (paginator_next) {
								load_more.attr('data-source', paginator_next);
							} else {
								load_more.remove();
							}
							
							if (list.data('masonry') == true) {
							
								if (html.length > 0) {							
						            var el = jQuery(html);
						            el = el.find('li.box');
						            el.find('.grid-sizer').remove();
									el.find('.gutter-sizer').remove();
						            jQuery(".awesome-gallery").append(el);
						            msnry.appended( el );
						            //console.log(el);
						            
						            $('.image.loading img').each(loadImageSmall);
						            imagesLoaded( '.awesome-gallery', function() {
										msnry.layout();
									});
						        }								

							} else {
							
								var images_html = $html.find('ul[data-autoload="true"]').html();
								var pagetype = $html.find('ul[data-autoload="true"]').data('page-type');
								
								if (images_html && (pagetype != "members") ) {
					
									list.html(list.html().replace('<!---->',''));
									list.find('li').last().after(images_html);
									$('.image.loading img').each(loadImageSmall);
					
								} else if (images_html && (pagetype == "members")) {
					
									list.append(images_html);
									$('.image.loading img').each(loadImageSmall);									
								}
									
							}
							
														
							if (typeof(waypoint) === 'object') {
								if (typeof(imagesLoaded) === 'function') {
									imagesLoaded( '.awesome-gallery', function() {
										waypoint.enable();
										Waypoint.refreshAll();
										//console.log("offset Refresh");
									});
									
								} else {
									waypoint.enable();
									Waypoint.refreshAll();
									//console.log("offset Refresh");
								}
							}
						},
						dataType : 'html'
					});
				}

				return false;
			});

			list.after(load_more);
		} else {
			paginator.remove();
		}
	});

	$('.button[data-hover-text], .button-mini[data-hover-text]')
	.on('mouseenter', function () {
		var hover_class = $(this).attr('data-hover-class');
		var hover_text = $(this).attr('data-hover-text');

		if (hover_text.length > 0) {
			$(this)
			.find('button span').text(hover_text)
			.addClass(hover_class);
		}
	})
	.on('mouseleave', function () {
		var hover_class = $(this).attr('data-hover-class');
		var hover_text = $(this).attr('data-text');

		$(this)
		.find('button span').text(hover_text)
		.removeClass(hover_class);
	})
	.each(function () {
		$(this)
		.attr('data-text', $(this).text())
		.width($(this).width());
	});

	$('.image.loading img').each(loadImageSmall);

	$('.image-large img').each(function () {
		var image = $(this);
		var container = image.closest('.image-large');
		var max_width = parseInt($(this).attr('data-max-width'));

		$(this)
		.bind('load', function () {
			var width = this.width > max_width ? max_width : this.width;
			var height = (width / this.width) * this.height;

			container.animate({
				//width: width,
				//height: height
			},
				500,
				function () {
				image
				//.height(height)
				//.width(width)
				.fadeIn(200, function () {
					container.removeClass('loading');
					image.removeClass('loading');
				});
			});

		})
		.attr('src', $(this).attr('data-src'));
	});

	/* Report Image */
	$(document).on("click", '[rel~="image"][rel~="report-image"]', function (e) {

		var image_id = $(this).attr('data-image-id');

		e.preventDefault();

		var box = confirm("Are you sure?");
		if (box == true) {

			//Run the AJAX here....
			mokoala.API.load({
				module : 'image',
				id : image_id,
				request : [
					'id'
				],
				action : 'report-image'
			},
				function (data) {
				//console.log('Callback!');
				$('a.report').text('Image Reported');
				$('a.report').addClass('pure-button-disabled');
				$('a.report').attr('rel', '');
			});

		}

		return false;
	});

    /******************** Delete User ********************/
	$(document).on("click", '[rel~="delete-profile"]', function (e) {

		e.preventDefault();
		var box = confirm("Are you sure?\n\nTHIS WILL DELETE ALL IMAGES, VIDEOS, COMMENTS AND ASSOCIATED DATA.");
		if (box == true) { //Ok
			document.location.href = $(this).attr('href');
		}
		return false;
	});
    
	/******************** Delete Image ********************/
	$(document).on("click", '[rel~="image"][rel~="delete-image"]', function (e) {

		e.preventDefault();
		var box = confirm("Are you sure?");
		if (box == true) { //Ok
			document.location.href = $(this).attr('href');
		}
		return false;
	});

	/******************** Delete Comment ********************/
	$(document).on("click", '[rel~="comment"][rel~="delete-comment"]', function (e) {

		var box = confirm("Are you sure you want to delete this comment?");
		if (box == true) {

			var button = $(this);
			var comment_id = button.attr('data-comment-id');

			mokoala.API.load({
				module : 'image_comment',
				id : comment_id,
				request : [
					'id'
				],
				action : 'delete'
			},
				function (data) {
				//console.log('Comment deleted!');
				var li = button.closest('li');
				li.remove();

			});

			return false;

		} 

	});

	//REMOVE FAVOURITE AJAX
	$(document).on("click", '[rel~="image"][rel~="remove-favourite"]', function (e) { //Updated to use correct params for .on
		var button = $(this);
		var image_favourite_id = button.attr('data-image-favourite-id');
		var image_favourites_total = parseInt(button.attr('data-image-favourites-total'));

		mokoala.API.load({
			module : 'image_favourite',
			id : image_favourite_id,
			request : [
				'id'
			],
			action : 'delete'
		},
			function (data) {
			image_favourites_total = image_favourites_total - 1;
			button
			.attr('data-image-favourites-total', image_favourites_total)
			.attr('rel', 'image add-favourite');
			button.find('.text').text(image_favourites_total);

			button.removeClass('favourite');

			button.removeClass('pulse animated').addClass('pulse animated');
		});

		return false;
	});

	//ADD FAVOURITE AJAX
	$(document).on("click", '[rel~="image"][rel~="add-favourite"]', function (e) { //Updated to use correct params for .on

		var button                 = $(this);
		var image_id               = button.attr('data-image-id');
		var user_id                = button.attr('data-user-id');
		var image_favourites_total = parseInt(button.attr('data-image-favourites-total'));

		mokoala.API.load({
			module : 'image_favourite',
			request : [
				'id'
			],
			fields : {
				user : user_id,
				image : image_id
			},
			action : 'add'
		},
			function (data) {
			image_favourites_total = image_favourites_total + 1;
			button
			.attr('rel', 'image remove-favourite')
			.attr('data-image-favourites-total', image_favourites_total)
			.attr('data-image-favourite-id', data.body.id);

			button.find('.text').text(image_favourites_total);
			button.addClass('favourite');
			button.removeClass('pulse animated').addClass('pulse animated');

		});
		return false;
	});

	//REMOVE COMMENT LIKE AJAX
	$(document).on("click", '[rel~="image-comment"][rel~="remove-like"]', function (e) { //Updated to use correct params for .on

		e.preventDefault();

		var span = $(this);
		var image_comment_like_id = span.attr('data-image-comment-like-id');
		var image_comment_likes_total = parseInt(span.attr('data-image-comment-likes-total'));

		mokoala.API.load({
			module : 'image_comment_like',
			request : [
				'id'
			],
			id : image_comment_like_id,
			action : 'delete'
		},
			function (data) {
			image_comment_likes_total = image_comment_likes_total - 1;
			span
			.removeClass('remove-like')
			.attr('data-image-comment-likes-total', image_comment_likes_total)
			.attr('rel', 'image-comment add-like');

			span.removeClass('pulse animated').addClass('pulse animated');
			span.find('.text').text(image_comment_likes_total);
			span.find('.number').text(mokoala.Utility.numberFormat(image_comment_likes_total));
		});

		return false;
	});

	//ADD COMMENT LIKE AJAX

	$(document).on("click", '[rel~="image-comment"][rel~="add-like"]', function (e) { //Updated to use correct params for .on

		e.preventDefault();

		var span = $(this);
		var image_comment_id = span.attr('data-image-comment-id');
		var user_id = span.attr('data-user-id');
		var image_comment_likes_total = parseInt(span.attr('data-image-comment-likes-total'));
		var image_id = span.attr('data-image-id');

		mokoala.API.load({
			module : 'image_comment_like',
			request : [
				'id'
			],
			fields : {
				user : user_id,
				comment : image_comment_id,
				imageid : image_id
			},
			action : 'add'
		},
			function (data) {
			image_comment_likes_total = image_comment_likes_total + 1;
			span
			//.removeClass('button-green')
			.addClass('remove-like')
			.attr('rel', 'image-comment remove-like')
			.attr('data-image-comment-likes-total', image_comment_likes_total)
			.attr('data-image-comment-like-id', data.body.id);
			span.removeClass('pulse animated').addClass('pulse animated');
			span.find('.text').text(image_comment_likes_total);
			span.find('.number').text(mokoala.Utility.numberFormat(image_comment_likes_total));
		});
		return false;
	});

	//APPROVE USER AJAX
	$(document).on("click", '[rel~="user"][rel~="approve"]', function (e) {
		e.preventDefault();
		
		var button = $(this);
		var profile_id = button.data('id');

		mokoala.API.load({
			module : 'user',
			id : profile_id,
			fields : {
				approved : true
			},
			action : 'update'
		},
			function (data) {
			button
			.hide();
		});
		return false;
	});

	//APPROVE IMAGE AJAX
	$(document).on("click", '[rel~="image"][rel~="approve"]', function (e) {
		e.preventDefault();
		
		var button = $(this);
		var image_id = button.data('id');

		mokoala.API.load({
			module : 'image',
			id : image_id,
			fields : {
				approved : true
			},
			action : 'update'
		},
			function (data) {
			button.closest('li').hide();
		});
		return false;
	});
	
	//FOLLOW USER AJAX
	$(document).on("click", '[rel~="user"][rel~="follow"]', function (e) {
		
		var button = $(this);
		var follower_id = button.attr('data-follower-id');
		var following_id = button.attr('data-following-id');

		mokoala.API.load({
			module : 'user_follower',
			fields : {
				following : following_id,
				follower : follower_id
			},
			action : 'add'
		},
			function (data) {
			button
			.attr('data-follower-object-id', data.body.id)
			.attr('data-hover-class', '')
			.attr('data-hover-text', 'Unfollow')
			.attr('data-text', 'Following')
			.attr('rel', 'user unfollow');

			button.find('span').addClass('following').text('Following');
		});
		return false;
	});

	//UNFOLLOW USER AJAX
	$(document).on("click", '[rel~="user"][rel~="unfollow"]', function (e) {
		
		var button = $(this);
		var follower_id = button.attr('data-follower-object-id');

		mokoala.API.load({
			module : 'user_follower',
			id : follower_id,
			action : 'delete'
		},
			function (data) {
			button
			.attr('data-hover-class', '')
			.attr('data-hover-text', '')
			.attr('data-text', 'Follow')
			.css('width', 'auto')
			.attr('rel', 'user follow');

			button.find('span').text('Follow');
		});
		return false;
	});

	//EXPAND COMMENTS CSS
	$('[rel~="comments"][rel~="expand"]').bind('click', function () {
		$(this).closest('ul').removeClass('replies-hidden');
		return false;
	});

	//ADJUST REPLY BOX CSS
	$('[rel~="comment"][rel~="reply"]').each(function () {
		var textarea = $(this).find('textarea');

		textarea
		.bind('focus', function () {
			$(this).closest('form').addClass('focus');
		})
		.bind('blur', function () {
			if ($(this).val().length == 0) {
				$(this).closest('form').removeClass('focus');
			}
		});

		if (textarea.val().length > 0) {
			textarea.trigger('focus');
		}
	});

	function loadImageSmall() {
		var container = $(this).closest('.image');

		$(this).bind('load', function () {

			$(this).css('display', 'block');

			container.removeClass('loading');

		}).attr('src', $(this).attr('data-src'));
		
	}
	

	$('#fileupload').fileupload({
    dropZone: $('#dropzone')
    });

});