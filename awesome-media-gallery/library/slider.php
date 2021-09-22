<?php 
	
	$imgTypes 		= array('jpeg', 'jpg', 'png', 'gif'); //The extensions of the images that you want the plugin to read
	$sliderEffects 	= array('boxes-zoomOut', 'boxesDiagonal-zoomOut', 'verticalStripes-openBookY', 'horizontalStripes-openBookX'); //The effects in the order you wish them to appear
	$captionEffects = array('openBookY', 'fromLeft', 'openBookX', 'zoomOut'); //The effects in the order you wish them to appear for the captions
	$randomOrder 	= false; // If is set to true the order of the images will be random each time it load


	function make_slider($id, $folder, $sliderEffects = false, $captionEffects = false){

		if( $sliderEffects == false ){
			global $sliderEffects;
		}
		if( $captionEffects == false ){
			global $captionEffects;
		}

		$list = getDirectoryList($folder);

		if(sizeof($list) == 0){
			echo "<div style='width:100%;text-align:center;color:red;'>";
				echo "<h4>No images were found in this folder: $folder</h4>";
			echo "</div>";
			return;
		}

		$currentEffect = 0;
		$currentEffect2 = 0;

		echo "<div class='as_slider' id='$id'>";

			foreach ($list as $key => $value) {
				$extension = preg_split('/\.(?=[^.]*$)/', $value);
    			$title = $extension[0];
    			$caption = "data-caption='$title'";

    			if( substr($title, 1, 1) == "-" ){
    				$title = substr($title, 2);
    				$caption = "data-caption='$title'";
    			}

    			//If the image name ends with a '--' then do NOT put a caption
    			if( substr($title, -2) == '--' ){
    				$caption = "";
    			}

    			$fx 		= $sliderEffects[$currentEffect];
    			$fxCaption 	= $captionEffects[$currentEffect2];

    			$caption = str_replace("<:", "</", $caption);

				echo "<img src='$folder/$value' data-effect='$fx' data-captioneffect='$fxCaption' $caption />";

				$currentEffect++;
				if( $caption != "" ){
					$currentEffect2++;
				}
				if($currentEffect >= count($sliderEffects)){
					$currentEffect = 0;
				}
				if($currentEffect2 >= count($captionEffects)){
					$currentEffect2 = 0;
				}
			}

		echo "</div>";
	}

	function getDirectoryList ($directory) {
	    global $imgTypes;
	    global $randomOrder;

	    if( !is_dir($directory)){
	      return array();
	    }

	    $results = array();

	    $handler = opendir($directory);

	    while ($file = readdir($handler)) {
	      if ($file != "." && $file != ".." && $file != ".DS_Store") {
	         $extension = preg_split('/\./',$file);
	         $extension = strtolower($extension[count($extension)-1]);
	         
	         if(array_search($extension,$imgTypes) !== FALSE){
	            $results[] = $file;
	         }   
	            
	      }
	    }
	    if( $randomOrder ){
	    	shuffle($results);
	    }else{
	    	sort($results);
	    }

	    closedir($handler);
	    return $results;
	  }

?>