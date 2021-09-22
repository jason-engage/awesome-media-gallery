/*
	Default loader
*/

$(document).ready(function(){

	if( jQuery().tinymce )
	{
		$('.form-field-richtext textarea').tinymce({
			// Location of TinyMCE script
			script_url : mokoala.templateFolder('js/jquery.tinymce/tiny_mce.js'),

			// General options
			theme : "advanced",
			plugins : "autolink,lists,style,advhr,advimage,advlink,inlinepopups,media,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist,filemanager",

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect",
			theme_advanced_buttons2 : "cut,copy,pastetext,|,bullist,numlist,|,outdent,indent,blockquote,link,unlink,image,removeformat,media,|,sub,sup,|,code",
			theme_advanced_statusbar_location : "none",
			theme_advanced_resizing : true,
			theme_advanced_resizing_min_width : 685,
			theme_advanced_resizing_max_width : 685,
			theme_advanced_resizing_min_height : 300,
			theme_advanced_resizing_max_height : 1000,

			mode : "exact",
			elements : 'abshosturls',
			relative_urls : false,
			remove_script_host : false
		});
	}

	if( swfobject.hasFlashPlayerVersion('9.0.0') && jQuery().uploadify )
	{
		$('div.form-field-fileimagemultiple input[type="file"], div.form-field-fileimagemultipleclone input[type="file"]').each(function(){
			var element_id = $(this).attr('id');

			$(this).uploadify({
				'swf' : mokoala.templateFolder('js/jquery.uploadify/uploadify.swf'),
				'uploader' : mokoala.templateFolder('js/jquery.uploadify/uploadify.php'),
				'buttonText' : 'Select Files',
				'auto'      : true,
				'multi'		: true,
				'removeCompleted' : true,
				'width'		: 85,
				'height'	: 15,
				'fileSizeLimit' : ( mokoala.settings_upload_max_filesize / 1024 )+'KB',
				'fileTypeExts'   : '*.jpg;*.jpeg;*.gif;*.png',
				'fileTypeDesc'  : 'Files',
				onSelectError: function(file, error_code, error_message)
				{
					var container = $(this.movieElement).closest('.form-field');
					container.find('p.error').remove();
					container.append('<p class="error">'+error_message+'</p>');
					
					setTimeout(function(){
						container.find('p.error').remove();
					}, 4000);
				},
				onUploadSuccess: function(file, data, response)
				{
					var event_target = $(this.movieElement);
	
					var container = event_target.closest('.form-field');

					var file_path = data;
					var file_path_parts = file_path.split('/');
					var file_path_basename = file_path_parts.pop();

					var file_object = event_target.closest('.input');
					var existing_file_instances = file_object.find('.input-single');
					
					if( existing_file_instances.length == 0 )
					{
						file_object.find('input[type="hidden"]').remove();
					}
					
					var file_instance = $('<div class="input-single"><img class="preview-image" src=""><button type="button" class="close"></button><input type="hidden" name="'+element_id+'[existing][]" value=""></div>');
	
					file_instance.find('input').val(file_path);
					file_instance.find('img').attr('src', '../'+file_path+'');
	
					file_object.append(file_instance);
				},
				onSWFReady	: function()
				{
					var container = $(this.movieElement).closest('.form-field');
					container.find('.file-details').addClass('file-details-rich');
				}
		
			});
		});

		$('div.form-field-fileimage input[type="file"], div.form-field-file input[type="file"]').each(function(){
			var is_image = $(this).closest('.form-field').is('.form-field-fileimage');

			$(this).uploadify({
				'swf' : mokoala.templateFolder('js/jquery.uploadify/uploadify.swf'),
				'uploader' : mokoala.templateFolder('js/jquery.uploadify/uploadify.php'),
				'buttonText' : 'Select File',
				'auto'      : true,
				'multi'		: false,
				'removeCompleted' : true,
				'width'		: 79,
				'height'	: 15,
				'fileSizeLimit': ( mokoala.settings_upload_max_filesize / 1024 )+'KB',
				'fileTypeExts': is_image ? '*.jpg;*.jpeg;*.gif;*.png' : $(this).attr('data-valid-extensions'),
				'fileTypeDesc': 'Files',
				onSelectError: function(file, error_code, error_message)
				{
					var container = $(this.movieElement).closest('.form-field');
					container.find('p.error').remove();
					container.append('<p class="error">'+error_message+'</p>');
				},
				onUploadSuccess: function(file, data, response)
				{
					var event_target = $(this.movieElement);
	
					var container = event_target.closest('.form-field');
					var is_image = container.is('.form-field-fileimage');
					
					var hidden_input = container.find('input[type=hidden]');
						hidden_input.find('.input-static').remove();
	
					var file_path = data;
					var file_path_parts = file_path.split('/');
					var file_path_basename = file_path_parts.pop();
					
					if( is_image )
					{
						var file_object = event_target.closest('.file-details');
		
						if( file_object.find('img.preview-image').length > 0 )
						{
							file_object.find('img.preview-image').attr('src', '../'+file_path);
						}
						else
						{
							file_object.prepend('<img class="preview-image" src="../'+file_path+'" />');
						}
					}
					else
					{
						hidden_input.after('<p class="input-static">'+response+'</p>');	
					}
	
					hidden_input.val(file_path);
				},
				onSWFReady	: function()
				{
					var container = $(this.movieElement).closest('.form-field');
					var hidden_input = container.find('input[type=hidden]');
					
					var uploader_object = $(this.movieElement);
	
					uploader_object.closest('.file-details').addClass('file-details-rich');
	
					var html_manual = '<div class="input-text-manual">';
						html_manual+= '<p class="float-text">/tpl/uploads/</p>';
						html_manual+= '<input type="text" class="input-text" name="'+this.id+'_manual" value="" />';
						html_manual+= '</div>';
			
						html_manual+= '<a rel="record file-manual" class="mini-button" href="">Select File From Server</a>';
	
					uploader_object.closest('.input').prepend(html_manual);
	
					container.find('.input-text-manual input').bind('change', function(){
						var container = $(this).closest('.form-field');
						var object = container.find('input.data');
				
						if( $(this).val().length == 0 )
						{
							if( file_location_name = object.attr('data-value') )
							{
								var file_location = file_location_name;
							}
							else
							{
								var file_location = '';
							}
						}
						else
						{
							var file_location = 'tpl/uploads/'+$(this).val();
						}
				
						object.val( file_location );
					});
				}
		
			});
		});
	}
	
	$('.file-details-rich button.close').live('click', function(){
		$(this).closest('.input-single').remove();
		return false;
	});

	$('.input-single button').bind('live', function(){
		$(this).closest('div.input-single').remove();		
		return false;
	});

	$('div.field-tags input.data')
		.live('change', function(){
			var values = $(this).val().split(',');
			var new_values = [];
			
			for( key in values )
			{
				var _value = values[key];
					_value = _value.trim();

				if( _value.length > 0 )
				{
					new_values.push(_value);
				}
			}
			
			$(this).val( new_values.join(', ') );
		});

	$('form.email-users').each(function(){
		
		var o = $(this);
		
		$('ul.email-users-groups a', o).bind('click', function(){
			var checkbox = $('input', this);
			if( checkbox.is(':checked') )
			{
				checkbox.removeAttr('checked');
				$(this).closest('li').removeClass('selected');
			}
			else
			{
				checkbox.attr('checked', 'checkbox');
				$(this).closest('li').addClass('selected');
			}
			return false;
		});
		
	});

	$('div.form-field-integer')
		.each(function(){
			var o = $(this);
			var input = o.find('div.input');
			var input_element = $('input', input);
			input
				.before('<div class="form-field-submit integer-minus"><div class="input"><button type="button">-</button></div></div>')
				.after('<div class="form-field-submit integer-plus"><div class="input"><button type="button">+</button></div></div>');
			
			input_element.bind('change', function(){
				var value = parseInt(input_element.val());
				if( !value )
				{
					value = 0;
					input_element.val(value);
				}
				return false;
			});
			
			$('div.integer-minus', o).bind('click', function(){
				var value = parseInt(input_element.val());
				if( !value )
				{
					value = 0;
				}
				else
				{
					value = value - 1;
				}
				input_element.val(value);
				return false;
			});
			
			$('div.integer-plus', o).bind('click', function(){
				var value = parseInt(input_element.val());
				if( !value )
				{
					value = 1;
				}
				else
				{
					value = value + 1;
				}
				input_element.val(value);
				return false;
			});
			
		});

	$('#navigation-main li')
		.bind('mouseenter', function(){
			$(this).addClass('hover');
		})
		.bind('mouseleave', function(){
			$(this).removeClass('hover');
		});

	if( !$('html').is('.mobile') )
	{
		/*$('#navigation-main li ul').each(function(){
			var ul = $(this);
			var li = ul.parent();
			var width_difference = Math.ceil( ( li.innerWidth() - ul.width() ) / 2 );
			ul.css('left', width_difference+'px');
		});*/
	}

	$('a[rel*=core][rel*=navigation-toggle]').live('click', function(){
		$(this).toggleClass('navigation-toggle-close');
		var toggle = $('#navigation-wrapper').toggle();
		var toggle_background = $('#navigation-wrapper-background').toggle();
		return false;
	});

	$('a[rel*=record][rel*=delete]').live('click', function(){
		return confirm($(this).attr('title'));
	});
	
	$('a[rel*=record][rel*=file-manual]').live('click', function(){
		var object = $(this).closest('.form-field').find('div.input-text-manual').toggle();

		if( object.is(':visible') )
		{
			$(this).addClass('hover');
		}
		else
		{
			$(this).removeClass('hover');
		}
		return false;
	});
	
	$('input[rel*=record][rel*=delete]').live('click', function(){
		var checked = $('table.table-data input[type=checkbox]:checked');
		if(checked.length == 0)
		{
			return false;
		}
		else
		{
			return confirm($(this).attr('title'));
		}
	});

	$('div.field-validationarguments').each(function(){
		var form = $(this).closest('form');
		var rule_name_container = $('div.field-name', form);
		var rule_arguments_field = $('input', rule_arguments_container).first().clone().val('');
		var rule_arguments_container = $(this);
		var loaded = false;
		
		$('select', rule_name_container).bind('change', function()
		{
			var option = $('option[value='+$(this).val()+']', rule_name_container).html();
			var argument_number = option.split('-');
				argument_number = parseInt( $.trim(argument_number.pop()) );
			var argument_inputs = $('input', rule_arguments_container);
			
			if( argument_inputs.length != argument_number )
			{
				$('input, p', rule_arguments_container).remove();
	
				for( var i = 0; i < argument_number; i++)
				{
					rule_arguments_container.append( rule_arguments_field.clone() );
				}
			}
			
			if( argument_number == 0 && $('p', rule_arguments_container).length === 0 )
			{
				rule_arguments_container.append('<p class="input-static">No arguments required</p>');
			}
			
			loaded = true;
			
		});
		
		$('select', rule_name_container).trigger('change');

	});

	$('select[rel*=toggle]').MK_Input_YesNo();

	$('input[data-module-id]').MK_Input_ModuleSelect();

	$('table.table-data:not(table.table-supplement)')
		.MK_Table_Data()
		.MK_Table_Paginator();
	
	$('form#module-search').MK_Module_Search();
	
	$('div.module-export-container').MK_Module_Export();

});

$.fn.MK_Input_YesNo = function()
{
	return $(this).each(function(){
		var o = $(this);
		
		var toggle_element = $('<div class="toggle-container"><span class="toggle-yes">Yes</span><span class="toggle-no">No</span></div>');
		
		toggle_element
			.bind('click', function(){
				var value = parseInt(o.val());
				if( value )
				{
					o.val(0);
				}
				else
				{
					o.val(1);
				}
				
				o.trigger('change');
			});
		
		o
			.bind('change', function(){
				var value = parseInt($(this).val());
				toggle_element.find('.selected').removeClass('selected');
				if( value )
				{
					toggle_element.find('.toggle-yes').addClass('selected');
				}
				else
				{
					toggle_element.find('.toggle-no').addClass('selected');
				}
			})
			.trigger('change')
			.css('display', 'none')
			.before(toggle_element);
	})
};

$.fn.MK_Module_Export = function()
{
	return $(this).each(function(){
		var o = $(this);
		
		var base_href = $('p.module-export a').attr('href');
		var export_button = $('a.input-submit', o);

		$('p.module-export a').bind('click', function(){
			o.toggleClass('module-export-container-expanded');
			return false;
		});

		$('form input', o).bind('change', function(){
			o.update_fields();
		});

		o.update_fields = function()
		{
			var fields = Array();
			$('form input:checked', o).each(function(index, field){
				fields.push( $(field).val() );
			});
			export_button.attr('href', base_href+'/fields/'+fields.join(','));
		};
		
		o.update_fields();
		
	});
	
}

$.fn.MK_Module_Search = function()
{
	return $(this).each(function(){
		var o = $(this);

		o.v = {
			form_elements: $(':input:not([type=submit])', o),
			switch_search: $('p.module-search-expand a', o.parent()),
			clear: $('div.form-field-link a', o)
		};
		
		o.v.clear.bind('click', function()
		{
			o.find(':input:not([type=submit])').val('').trigger('change');
			return false;
		});
		
		o.v.switch_search.bind('click', function()
		{
			var target = $(this).parent();
			target.toggleClass('module-search-contract');
			if(target.is('.module-search-contract'))
			{
				$(this).text('Fewer options')
					.prev().html('&ndash;');
				o.removeClass('search-mini').addClass('search-full');
			}
			else
			{
				$(this).text('More options')
					.prev().html('+');
				o.removeClass('search-full').addClass('search-mini');
			}
			return false;
		});

	});
};

$.fn.MK_Input_ModuleSelect = function() {

	return $(this).each(function() {
		var main_input = $(this);
		var total_records = parseInt( main_input.attr('data-module-total-records') );
		var module_type = main_input.attr('data-module-type');
		var select_type = main_input.attr('data-select-type');
		var slug_name = main_input.attr('data-module-slug-name');
		var field = main_input.closest('.form-field');
		var prompt_container = field.find('.input-prompt');
		var prompt_input = prompt_container.find('.input-text');
		var options = $('<div data-page="1" class="options"></div>');
		var prompt_handler = $('<div class="handler"></div>');

		var prompt_placeholder = total_records ? prompt_input.attr('data-placeholder') : 'None Available';

		main_input.removeVal = function( value ){
			var values = $(this).val().split(',');

			if( values.indexOf( value.toString() ) > -1 )
			{
				delete( values[values.indexOf(value.toString())] );
				values = values.filter(Number);
				$(this).val( values.join(',') );
			}

			main_input.createTextValue();
		};

		main_input.containsVal = function( value ){
			return $(this).val().split(',').indexOf( value.toString() ) > -1;
		};

		main_input.addVal = function( value ){

			if( select_type == 'single' )
			{
				$(this).val( value );
			}
			else
			{
				var values = $(this).val().split(',');
				if( !values.indexOf( value.toString() ) > -1 )
				{
					values.push(value);
					values = values.filter(Number)
					$(this).val( values.join(',') );
				}
			}
			
			main_input.createTextValue();
		};

		main_input.createTextValue = function(){
			var values = $(this).val().split(',').filter(Number);
			values = values.filter(Number);
			
			var type = main_input.attr('data-module-type');
			var slug_name = main_input.attr('data-module-slug-name');

			if( values.length > 0 )
			{
				mokoala.api(
					type,
					{
						id: values,
						expand: 0
					},
					function( data ){
						var title = [];
						for( data_key in data.body )
						{
							title.push(data.body[data_key][slug_name]);
						}

						prompt_input.removeClass('input-text-empty');

						if( title.length > 1 )
						{
							var last_item = title.pop();
							prompt_input.text( title.join(', ')+' & '+last_item )
						}
						else
						{
							prompt_input.text( title.join(', ') )
						}
					}
				);
			}
			else
			{
				prompt_input
					.addClass('input-text-empty')
					.text( prompt_placeholder );
			}
		};
		
		main_input.createTextValue();

		prompt_handler.css({
			height: prompt_input.innerHeight(),
			width: prompt_input.innerWidth()
		});

		var position = main_input.parent().parent().offset();

		if( ( position.top + 300 ) > $(window).height() )
		{
			prompt_container.addClass( 'options-bottom' );
		}
		else
		{
			prompt_container.addClass( 'options-top' );
		}

		options.css('width', prompt_input.innerWidth(true));
		
		if( prompt_container.is('.options-top') )
		{
			options.css('top', prompt_input.outerHeight(true));
		}
		else
		{
			options.css('bottom', prompt_input.outerHeight(true));
		}

		prompt_container
			.append(prompt_handler)
			.append(options);
		
		options.close = function(){
			options.html('');
			prompt_container.removeClass('focus loaded');
		};

		options.request = null;
		options.loadItems = function(){
			if( !prompt_container.is('.focus') )
			{
				$('.input-prompt.focus .handler').trigger('click');
			}
			prompt_container.removeClass('loaded');

			var parameters = {
				page: options.attr('data-page')
			};
			
			if( keywords = options.attr('data-keywords') )
			{
				parameters.keywords = keywords;
			}

			parameters.expand = 0;

			options.request = mokoala.api( module_type, parameters, function(data){
				var search = options.find('input.input-text'); 
				var list   = options.find('ul.items');

				if( search.length == 0 )
				{
					search = $('<input placeholder="Search records" type="text" class="input-text" />');
					options.append(search);

					search
						.bind( 'keydown', function(event){
							var key_code = event.which || event.keyCode;
							if( key_code == 13 )
							{
								event.preventDefault();
							}
						})
						.bind( ($.browser.opera ? 'keypress' : 'keyup'), function(event){
							var key_code = event.which || event.keyCode;
			
							switch(key_code)
							{
								// Up arrow
								case 38:
									event.preventDefault();
									if(list.length > 0)
									{
										var result_selected = list.find('.hover');
										if( result_selected.length == 0 )
										{
											list.find('li:last-child').addClass('hover');
										}
										else if( result_selected.prev().is('li') )
										{
											result_selected.removeClass('hover').prev().addClass('hover');
										}
										else
										{
											result_selected.removeClass('hover');
										}
									}
									break;
								// Down arrow
								case 40:
									event.preventDefault();
									if(list.length > 0)
									{
										var result_selected = list.find('.hover');
										if( result_selected.length == 0 )
										{
											list.find('li:first-child').addClass('hover');
										}
										else if( result_selected.next().is('li') )
										{
											result_selected.removeClass('hover').next().addClass('hover');
										}
										else
										{
											result_selected.removeClass('hover');
										}
									}
									break;
								case 13:
									event.preventDefault();
									if(list.length > 0)
									{
										var result_selected = list.find('.hover');
										result_selected.trigger('click');
									}
									break;
								default:
									if( options.request )
									{
										options.request.abort();
									}
			
									options
										.attr('data-keywords', $(this).val())
										.attr('data-page', 1)
										.loadItems();
									break;
							}
						})
						.trigger('focus');

				}

				if( list.length == 0 )
				{
					list = $('<ul class="items"></ul>');
					options.append(list);
				}
				else
				{
					list.html('');
				}

				$.each(data.body.records, function( key, record ){
					var item = $('<li data-record-id="'+record['id']+'">'+record['text_indent']+record[slug_name]+'</li>');
					if( main_input.containsVal( parseInt(record['id']) ) )
					{
						item.addClass('selected');
					}

					item.bind('click', function(){
						var id = $(this).attr('data-record-id');
						var title = $(this).text();

						if( $(this).is('.selected') )
						{
							$(this).removeClass('selected');
							main_input.removeVal(id);
						}
						else
						{
							$(this).addClass('selected');
							main_input.addVal(id);
						}
						
						if( select_type == 'single' )
						{
							options.close();
						}
					});

					list.append(item);
				});
				
				var _page = parseInt(data.body.page);
				var _total_pages = parseInt(data.body.total_pages);
				
				var paginator = options.find('div.paginator');
				if( paginator.length == 0 )
				{
					paginator = $('<div class="paginator clear-fix">');
					options.append(paginator);
				}

				paginator.html('<p>Page '+mokoala.utility.numberFormat(_page)+' of '+mokoala.utility.numberFormat(_total_pages)+'</p>'
					+'<ul class="list">'
					+( _page > 1 ? '<li class="prev"><a href="">&lsaquo; Prev</a></li>' : '')
					+( _page < _total_pages ? '<li class="next"><a href="">Next &rsaquo;</a></li>' : '')
					+'</ul>'
					+'</div>');
				
				prompt_container.addClass('loaded');

				paginator.find('a').bind('click', function(){
					var parent = $(this).parent();
					var current_page = parseInt(options.attr('data-page')) || 1;
					
					if( parent.is('.next') )
					{
						current_page++;
					}
					else
					{
						current_page--;
					}
					
					options
						.attr('data-page', current_page)
						.loadItems();
					
					return false;
				});

			});
		};

		main_input.bind('change', function(){
			main_input.createTextValue();
		});
		
		if( total_records > 0 )
		{
			prompt_handler
				.bind('click', function(){
					options.html('');
					if( prompt_container.is('.focus') )
					{
						options.close();
					}
					else
					{
						options.loadItems();
						prompt_container.addClass('focus');
					}
				});
		}
		else
		{
			prompt_container.addClass('input-no-records');
		}
	});
};

$.fn.MK_Table_Paginator = function(){

	return $(this);
	return $(this).each(function(){
		
		var form = $(this).parent();
		var table = $(this);
		var paginator = table.parent().find('.paginator');

		paginator.find('a').bind('click', function(){
			$.get($(this).attr('href'), {}, function(data){
				table.remove();
				paginator.remove();
				var data = $(data);
				var table_data = data.find('table.table-data');
				var paginator_data = data.find('div.paginator');

				form.prepend(paginator_data).prepend(table_data);
				
				table_data
					.MK_Table_Data()
					.MK_Table_Paginator();

			}, 'html');

			return false;
		});
		
	});

};

$.fn.MK_Table_Data = function() {

	return $(this).each(function() {
		var table = $(this);

		$('tbody tr td:not(.first, .last)', table)
			.live('click', function(){
				var tr = $(this).closest('tr');
				if(tr.is('.highlight')){
					$('thead input[type=checkbox]:not([disabled="disabled"])', table).removeAttr('checked');
					$('input[type=checkbox]:not([disabled="disabled"])', tr).removeAttr('checked').trigger('change');
				}else{
					$('input[type=checkbox]:not([disabled="disabled"])', tr).attr('checked', 'checked').trigger('change');
				}
			});

		$('tbody tr', table)
			.live('mouseenter', function(){
				$(this).addClass('hover');
			})
			.live('mouseleave', function(){
				$(this).removeClass('hover');
			});

		var body_checkboxes = $('tbody input[type=checkbox]:not([disabled="disabled"])', table);
		var head_checkboxes = $('thead input[type=checkbox]', table);
		
		head_checkboxes
			.live('change', function(){
				var checkboxes = $('tbody input[type=checkbox]', table);
				if($(this).is(':checked')){
					checkboxes.attr('checked', 'checked')
						.closest('tr').addClass('highlight');
				}else{
					checkboxes.removeAttr('checked')
						.closest('tr').removeClass('highlight');
				}
				table.check_selected();
			});
		
		if( body_checkboxes.length > 0 )
		{
			body_checkboxes
				.live('change', function(){
					var tr = $(this).closest('tr');
					if($(this).is(':checked')){
						tr.addClass('highlight');
						table.check_selected();
						if($('tbody input[type=checkbox]', table).length === $('tbody input[type=checkbox]:checked', table).length){
							$('thead input[type=checkbox]', table).attr('checked', 'checked');
						}
					}else{
						tr.removeClass('highlight');
						$('thead input[type=checkbox]', table).removeAttr('checked');
						table.check_selected();
					}
				})
				.each(function(){
					var tr = $(this).closest('tr');
					if($(this).is(':checked')){
						tr.addClass('highlight');
					}
				});
		}
		else
		{
			head_checkboxes.attr('disabled', 'disabled');
		}
		
		table.check_selected = function()
		{
			if( table.find('tbody input[type=checkbox]:checked').length === 0 )
			{
				table.parent().find('div.field-delete input').attr('disabled', 'disabled');
			}
			else
			{
				table.parent().find('div.field-delete input').removeAttr('disabled');
			}
		}

		table.check_selected();
   });

};

var mokoala = {
	template_folder : '',
	base_href : '',
	site_href : '',

	templateFolder: function( local_dir )
	{
		return mokoala.template_folder+local_dir;
	},
	
	utility: {
		randomString: function( length )
		{
			var text = "";
			var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		
			for( var i=0; i < length; i++ )
			{
				text += possible.charAt(Math.floor(Math.random() * possible.length));
			}
		
			return text;
		},
		bytesToSize: function(bytes)
		{
			var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
			if (bytes == 0) return 'n/a';
			var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
			return Math.round(bytes / Math.pow(1024, i), 2) + '' + sizes[i];
		},
		trimContent: function(string, max_length, hellip)
		{
			var hellip = hellip ? hellip : true;
			
			if( string.length > max_length )
			{
				return string.substr(0, max_length)+'&hellip;';
			}
			
			return string;
		},
		numberFormat: function( number )
		{
			number += '';
			x = number.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1))
			{
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}
	},

	api: function( module_type, params, callback )
	{
		var params   = params || {};
		var callback = callback || function(){};
		params.module = module_type;
		
		var url = mokoala.base_href+'api.php';

		return $.ajax({
			url:      url,
			dataType: 'json',
			data:     params,
			success:  function(data){
				callback(data);
			}
		});
	},
	
	modal: {
		current: null,

		open: function(){
			
		},
		
		confirm: function(){
			
		},
		
		close: function(){
			
		}
	}
	
}

String.prototype.trim = function(){
	return this.replace(/^\s+|\s+$/g, '');
};

String.prototype.ltrim = function(){
	return this.replace(/^\s+/, '');
};

String.prototype.rtrim = function(){
	return this.replace(/\s+$/, '');
};
