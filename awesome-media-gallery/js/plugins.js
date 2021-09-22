$(document).ready(function() {

	var deviceAgent = navigator.userAgent.toLowerCase();
	
	var isTouchDevice = Modernizr.touch || 
	(deviceAgent.match(/(iphone|ipod|ipad)/) ||
	deviceAgent.match(/(android)/)  || 
	deviceAgent.match(/(iemobile)/) || 
	deviceAgent.match(/iphone/i) || 
	deviceAgent.match(/ipad/i) || 
	deviceAgent.match(/ipod/i) || 
	deviceAgent.match(/blackberry/i) || 
	deviceAgent.match(/bada/i));


	if (!isTouchDevice) {
	       
		if ($('.sidebar.image-single').length > 0) {
			
			$('.sidebar.image-single').sticky( {
			  topSpacing: 20, 
			  wrapperClassName:'sticky-sidebar',
			  bottomSpacing: 300,
			  getWidthFrom: 'sidebar-wrapper'
			});
			
		}
	
		if ($('.fancybox-media').length > 0) {
			
			$('.fancybox-media').fancybox({
				openEffect  : 'fade',
				closeEffect : 'fade',
				tpl : {
					closeBtn : '<i class="close-button-fancybox"></i>',
					prev:'<div class="fancybox-big-arrow"><div class="icon arrow-left arrow-left-7"></div></div>',
					next:'<div class="fancybox-big-arrow"><div class="icon arrow-right untitled-2"></div></div>'	
				},
				helpers : {
					media : {}
					
				},
			    afterShow: function() { 
				    var alt = $(this.element).attr('alt');
				    
			        $('<i class="open-button-fancybox fa fa-external-link icon"></i>').appendTo(this.inner).on('click', function() {
			            window.location.href = alt;
			        });
			    }
			    
			});
			
		}
		
		if ($('#fancybox-view-button').length > 0) {
			
			$('#fancybox-view-button').click(function(e) {
				e.preventDefault();
				$('.fancybox-media:eq(1)').click();
			});
			
		}
	
	} //IS NOT TOUCH DEVICE

});

