// Required for drag and drop file access

// IIFE to prevent globals
(function() {

  var s;
  var Cover = {

    settings: {
      bod: $(".profile-photo-wrap"),
      img: $("#js-my-photo"),
      fileInput: $("#photo-img")
    },

    init: function() {
      s = Cover.settings;
      Cover.bindUIActions();
    },

    bindUIActions: function() {

      var timer;

      s.bod.on("dragover", function(event) {
        clearTimeout(timer);
        if (event.currentTarget == s.bod[0]) {
          Cover.showDroppableArea();
        }

        // Required for drop to work
        return false;
      });

      s.bod.on('dragleave', function(event) {
        if (event.currentTarget == s.bod[0]) {
          // Flicker protection
          timer = setTimeout(function() {
            Cover.hideDroppableArea();
          }, 200);
        }
      });

      s.bod.on('drop', function(event) {
        // Or else the browser will open the file
        event.preventDefault();

        Cover.handleDrop(event.dataTransfer.files);
      });

      s.fileInput.on('change', function(event) {
        Cover.handleDrop(event.target.files);
      });
    },

    showDroppableArea: function() {
      s.bod.addClass("droppable");
    },

    hideDroppableArea: function() {
      s.bod.removeClass("droppable");
    },

    handleDrop: function(files) {

      Cover.hideDroppableArea();

      // Multiple files can be dropped. Lets only deal with the "first" one.
      var file = files[0];

      if (typeof file !== 'undefined' && file.type.match('image.*')) {

        Cover.resizeImage(file, 779, 180, function(data) {
	        
          Cover.placeImage(data);
        });

      } else {

        alert("That file wasn't an image.");

      }

    },

    resizeImage: function(file, sizew, sizeh, callback) {
      var fileTracker = new FileReader;
      fileTracker.onload = function() {
        Resample(
         this.result,
         sizew,
         sizeh,
         callback
       );
      }
      fileTracker.readAsDataURL(file);

      fileTracker.onabort = function() {
        alert("The upload was aborted.");
      }
      fileTracker.onerror = function() {
        alert("An error occured while reading the file.");
      }

    },

    placeImage: function(data) {
      s.img.css("opacity", 0);
      //s.img.attr("src", data);
      //main.Cover.sendCover(data);
    }

  }
  Cover.init();

})();