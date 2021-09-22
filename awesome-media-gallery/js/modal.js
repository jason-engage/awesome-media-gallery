/*  Check if integer is Odd */

$(document).ready(function() {
    
    //Lazyload the iframes
    var chooseiframe = $('#ChooseFrame');
    
    $('a[data-modal="modal-choose"]').click(function() {
        chooseiframe.attr('src', function() {
            return $(this).data('src');
        });
    });
});

    
/* Function will set the height of an outter parent element based on the height an inner frame element */
/* USED ON UPLOAD MODALS ONLY */
function setNewHeight(strOutterElement, strInnerElement, strFrameElement, intMaxHeight) {
	
    intMaxHeight = (typeof intMaxHeight === "undefined") ? "defaultValue" : intMaxHeight; //Optional parameter

    var i_Height =  $(strInnerElement).outerHeight(); //Get the height of Inner-Body
    
    var o_height = parseInt(parent.$(strOutterElement).css("height")); //Get the height from the outside container.
    
    /* Account for Modal Fuzzy Behavior */
    if(isOdd(i_Height) == true) {
        i_Height = (i_Height + 1);
    }
    
    
    if(i_Height > intMaxHeight) { //REACHED LIMIT
        parent.$(strOutterElement).css('height', intMaxHeight+'px');
        parent.$(strOutterElement).css('max-height', intMaxHeight+'px');
        parent.$(strFrameElement).css('height', (intMaxHeight-20)+'px');
        $('html').css('overflow','visible');
    
    } else { //Grow Box
       parent.$(strOutterElement).css('height', i_Height+'px'); 
       parent.$(strOutterElement).css('max-height', i_Height+'px');
        parent.$(strFrameElement).css('height', (i_Height+10)+'px');
        $('html').css('overflow','hidden');
    }

}

/* USED FOR SIGN IN and SIGN UP MODAL ONLY */
function setNewHeight2(strOutterElement, strInnerElement, strFrameElement) {

    var i_Height =  parseInt($(strInnerElement).outerHeight()) + 10; //Get the height of Inner-Body
    
    /* Account for Modal Fuzzy Behavior */
    if(isOdd(i_Height) == true) {
        i_Height = (i_Height + 1);
    }
    
	parent.$(strOutterElement).css('height', i_Height+'px'); 
	parent.$(strOutterElement).css('max-height', i_Height+'px');
    parent.$(strFrameElement).css('height', (i_Height)+'px');

}



/* Check if the url is a valid Youtube/Vimeo link via RegEx. */
function validSoundcloud(url) { 

    var soundcloud = url.match(/https?:\/\/(m\.)?(soundcloud.com|snd.sc)\/(.*)$/);
    
    if(soundcloud) { //If either of the RegEx's return true, we have a valid url.

        return true;
        
    } else {
    
        return false;
        
    }

}


/* Check if the url is a valid Youtube/Vimeo link via RegEx. */
function validVimeoYoutube(url) { 

    var youtube = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
    //var vimeo   = url.match(/^https?:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/);
    var vimeo   = url.match(/(?:vimeo(?:pro)?.com)\/(?:[^\d]+)?(\d+)(?:.*)/);
    var vine   = url.match(/^https?:\/\/?vine\.co\/v\/?\b.*$/);
    
    if(youtube || vimeo || vine) { //If either of the RegEx's return true, we have a valid url.

        return true;
        
    } else {
    
        return false;
        
    }

}

/* Replace BR with line breaks */
function br2nl(str) {

    return str.replace(/<br\s*\/?>/mg,"");

}

function isOdd(num) {
    return (num % 2) == 1;    
}

function isEven(num) { //USE WHEN SOCIAL ENABLED
    return (num % 2) == 0
}