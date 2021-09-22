var ModalEffects = (function() {
    function init() {
        var overlay = document.querySelector('.en-overlay');
        
        if (overlay != null) {
	        [].slice.call(document.querySelectorAll('.en-trigger')).forEach(function(el, i) {
	            var modal = document.querySelector('#' + el.getAttribute('data-modal')),
	                close = modal.querySelector('.en-close');
	
	            function removeModal(hasPerspective) {
	                classie.remove(modal, 'en-show');
	                if (hasPerspective) {
	                    classie.remove(document.documentElement, 'en-perspective');
	                }
	                $("body").unbind("mousewheel");
	              	
	              	if ( (uploadStarted!=undefined) && (uploadStarted == true) ) {
				        $('#ChooseFrame').attr('src', function() {
				            return $(this).data('src');
				        });
				        uploadStarted = false;
			        }
	            }
	
	            function removeModalHandler() {
	                removeModal(classie.has(el, 'en-setperspective'));
	            }
	            el.addEventListener('click', function(ev) {
	                ev.preventDefault();
	                classie.add(modal, 'en-show');
	                $("body").bind("mousewheel", function() {
	                    return false;
	                });
	                overlay.removeEventListener('click', removeModalHandler);
	                overlay.addEventListener('click', removeModalHandler);
	                if (classie.has(el, 'en-setperspective')) {
	                    setTimeout(function() {
	                        classie.add(document.documentElement, 'en-perspective');
	                    }, 25);
	                }
	            });
	            close.addEventListener('click', function(ev) {
	                ev.stopPropagation();
	                removeModalHandler();
	            });
	        });
		}
    }
    init();
})();