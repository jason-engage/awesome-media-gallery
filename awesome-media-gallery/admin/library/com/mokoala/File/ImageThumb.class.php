<?php

class MK_Image_Thumb extends MK_Image{

	protected $resize_methods = array('width', 'height', 'crop', 'crop-top', 'crop-hd', 'contain', 'fill', 'snap');
	protected $thumb = array();

	public function __construct($image_file, $new_width, $new_height, $method = 'width', $destination, $offset_x = 0, $offset_y = 0, $quality = 75, $animate = false, $png_compression = 6){
		
		parent::__construct($image_file);

		$path_parts = array_filter( explode('/', $image_file ) );
		$path_filename = array_pop( $path_parts );

		$this->thumb = array(
			'offset' => array(
				'x' => $offset_x,
				'y' => $offset_y
			),
			'quality' => $quality,
			'method' => $method,
			'destination_path' => !empty($destination) ? $destination : implode( '/',  $path_parts).'/thumbs/',
			'destination_filename' => $new_width.'x'.$new_height.'-'.$offset_y.'x'.$offset_x.'-'.$quality.'-'.$png_compression.'-'.$method.'-'.$path_filename,
            'animate' => $animate,
            'png_compression' => $png_compression,
            
		);

		$this->calculateNewDimensions($new_width, $new_height);


	}
	
	public function create(){

		if( !$this->thumbExists() ){
			switch( $this->getType() ){
				
				case 'jpeg':
					$this->createJPG();
					break;
				case 'png':
					$this->createPNG();
					break;
				case 'gif':
					$this->createGIF();
					break;
				default:
					break;
				
			}
		
		}

	}
	
	protected function calculateNewDimensions($width, $height){
		
		$source_width = $this->getWidth();
		$source_height = $this->getHeight();
		
		switch( $this->getThumbResizeMethod() ){
			
			case 'width';

				if($source_width >= $width){ //if the natural image is larger, do nothing
					//$width = $source_width;
				}else{ //if the natural width is smaller, set the width to the natural width. Prevents stretched images, disabled
					//$width = $source_width;
				}
				$this->setThumbResizeWidth($width);
				$this->setThumbWidth($width);
				$this->setThumbResizeHeight( $this->getAspectHeightFromWidth( $width ) );
				$this->setThumbHeight( $this->getAspectHeightFromWidth( $width ) );
				break;
			case 'height':
				if($source_height >= $height){
				}else{
					$height = $source_height;
				}
				$this->setThumbResizeHeight($height);
				$this->setThumbHeight($height);
				$this->setThumbResizeWidth( $this->getAspectWidthFromHeight( $height ) );
				$this->setThumbWidth( $this->getAspectWidthFromHeight( $height ) );
				break;
			case 'crop':
				$this->setThumbHeight( $height );
				$this->setThumbWidth( $width );

				if( $height >= $width )
				{
					$new_width = $width;
					$new_height = $this->getAspectHeightFromWidth( $width );
					if( $new_height < $height )
					{
						$new_height = $height;
						$new_width = $this->getAspectWidthFromHeight( $height );
					}
				}
				
				if( $width >= $height )
				{
					$new_height = $height;
					$new_width = $this->getAspectWidthFromHeight( $height );

					if( $new_width < $width )
					{
						$new_width = $width;
						$new_height = $this->getAspectHeightFromWidth( $width );
					}
				}

				if( $new_width > $width )
				{
					$_difference = $new_width - $width;
					$_difference = ceil($_difference / 2);

					$this->thumb['offset']['x'] = -$_difference;
				}
				elseif( $new_height > $height )
				{
					$_difference = $new_height - $height;
					$_difference = ceil($_difference / 2);
					
					$this->thumb['offset']['y'] = -$_difference;
				}

				$this->setThumbResizeHeight( $new_height );
				$this->setThumbResizeWidth( $new_width );
				break;
            
            case 'crop-top':
				$this->setThumbHeight( $height );
				$this->setThumbWidth( $width );

				if( $height >= $width )
				{
					$new_width = $width;
					$new_height = $this->getAspectHeightFromWidth( $width );
					if( $new_height < $height )
					{
						$new_height = $height;
						$new_width = $this->getAspectWidthFromHeight( $height );
					}
				}
				
				if( $width >= $height )
				{
					$new_height = $height;
					$new_width = $this->getAspectWidthFromHeight( $height );

					if( $new_width < $width )
					{
						$new_width = $width;
						$new_height = $this->getAspectHeightFromWidth( $width );
					}
				}

				if( $new_width > $width )
				{
					$_difference = $new_width - $width;
					$_difference = ceil($_difference / 2);

					$this->thumb['offset']['x'] = -$_difference;
				}
				elseif( $new_height > $height )
				{
					$_difference = $new_height - $height;
					$_difference = ceil($_difference / 2);
					
					$this->thumb['offset']['y'] = 0;
				}

				$this->setThumbResizeHeight( $new_height );
				$this->setThumbResizeWidth( $new_width );
				break;
				    
            case 'crop-hd':
            
				$this->setThumbHeight( $height );
				$this->setThumbWidth( $width );

				
				if( $width >= $height )
				{
					$new_height = $height;
					$new_width = $this->getAspectWidthFromHeight( $height );

					if( $new_width < $width )
					{
						$new_width = $width;
						$new_height = $this->getAspectHeightFromWidth( $width );
					}
				}

				if( $new_width > $width )
				{
					$_difference = $new_width - $width;
					$_difference = ceil($_difference / 2);

					$this->thumb['offset']['x'] = -$_difference;
				}
				elseif( $new_height > $height )
				{
					$_difference = $new_height - $height;
					$_difference = ceil($_difference / 2);
					
					$this->thumb['offset']['y'] = -45;
                    //$this->thumb['offset']['y'] = -$_difference;
                    
                     //$this->thumb['offset']['y'] = $this->thumb['offset']['y']
				}

				$this->setThumbResizeHeight( $new_height );
				$this->setThumbResizeWidth( $new_width );
				break;

            case 'crop-youtube':
            	
            	//youtube is 4:3 with 90px black on top and bottom
            	
            	
            	
				$this->setThumbHeight( $height );
				$this->setThumbWidth( $width );

				
				if( $width >= $height )
				{
					$new_height = $height;
					$new_width = $this->getAspectWidthFromHeight( $height );
					
					//echo 'height: ' . $new_height;
					//echo 'width: ' . $new_width;
            	
				}

				if( $new_width > $width )
				{
					$_difference = $new_width - $width;
					$_difference = ceil($_difference / 2);

					$this->thumb['offset']['x'] = -$_difference;
					
				}
				elseif( $new_height > $height )
				{
					$_difference = $new_height - $height;
					$_difference = ceil($_difference / 2);
					
					$this->thumb['offset']['y'] = 45;
                    //$this->thumb['offset']['y'] = -$_difference;
                    
                     //$this->thumb['offset']['y'] = $this->thumb['offset']['y']
				}

				$this->setThumbResizeHeight( $new_height );
				$this->setThumbResizeWidth( $new_width );
				break;

	
			case 'fill':
				$new_width = $width;
				$new_height = $this->getAspectHeightFromWidth( $new_width );
				
				if($new_height < $height)
				{
					$new_height = $height;
					$new_width = $this->getAspectWidthFromHeight( $new_height );
				}

				$this->setThumbHeight( $new_height );
				$this->setThumbWidth( $new_width );
				$this->setThumbResizeHeight( $new_height );
				$this->setThumbResizeWidth( $new_width );
				break;
			case 'snap':
				$this->setThumbHeight( $height );
				$this->setThumbWidth( $width );

				$this->setThumbResizeHeight( $this->getHeight() );
				$this->setThumbResizeWidth( $this->getWidth() );
				break;
			case 'contain':
			default:
				if($height >= $width)
				{
					$new_width = $width;
					$new_height = $this->getAspectHeightFromWidth( $width );
					if($new_height > $height)
					{
						$new_height = $height;
						$new_width = $this->getAspectWidthFromHeight( $height );
					}
				}
				elseif($width >= $height)
				{
					$new_height = $height;
					$new_width = $this->getAspectWidthFromHeight( $height );

					if($new_width > $width)
					{
						$new_width = $width;
						$new_height = $this->getAspectHeightFromWidth( $width );
					}
				}

				if($new_width > $this->getWidth() || $new_height > $this->getHeight())
				{
					$new_height = $this->getHeight();
					$new_width = $this->getWidth();
				}

				$this->setThumbHeight( $new_height );
				$this->setThumbWidth( $new_width );
				$this->setThumbResizeHeight( $new_height );
				$this->setThumbResizeWidth( $new_width );
				break;
		}
		
	}
	
	protected function createJPG(){
		$original_image = imagecreatefromjpeg($this->getFile());

		$thumb_image = imagecreatetruecolor($this->getThumbWidth(), $this->getThumbHeight());
		imagecopyresampled( $thumb_image, $original_image, $this->thumb['offset']['x'], $this->thumb['offset']['y'], 0, 0, $this->getThumbResizeWidth(), $this->getThumbResizeHeight(), $this->getWidth(), $this->getHeight() );
		imagejpeg( $thumb_image, $this->getThumbDestination(), $this->getThumbQuality() );
		
		imagedestroy($thumb_image);
		imagedestroy($original_image);
		
	}

	protected function createPNG(){

		$original_image = imagecreatefrompng($this->getFile());
		$thumb_image = imagecreatetruecolor($this->getThumbWidth(), $this->getThumbHeight());
		$transparency = imagecolortransparent($original_image);

		if ($transparency >= 0) {
			$transparency_color = imagecolorsforindex($original_image, $transparency);
			$transparency = imagecolorallocate($thumb_image, $transparency_color['red'], $transparency_color['green'], $transparency_color['blue']);
			imagefill($thumb_image, 0, 0, $transparency);
			imagecolortransparent($thumb_image, $transparency);
		}elseif($this->getType() === 'png') {
			imagealphablending($thumb_image, false);
			$color = imagecolorallocatealpha($thumb_image, 0, 0, 0, 127);
			imagefill($thumb_image, 0, 0, $color);
			imagesavealpha($thumb_image, true);
		}

		imagecopyresampled( $thumb_image, $original_image, $this->thumb['offset']['x'], $this->thumb['offset']['y'], 0, 0, $this->getThumbResizeWidth(), $this->getThumbResizeHeight(), $this->getWidth(), $this->getHeight() );
		imagepng( $thumb_image, $this->getThumbDestination(), $this->getThumbPNGCompression() );

		imagedestroy($thumb_image);
		imagedestroy($original_image);
		
	}

	protected function createGIF(){

        if ( $this->isAnimatedGif() && $this->getAnimate() == 'true' ) {
        
           $gifresize = new gifresizer;	//New Instance Of GIFResizer
           $gifresize->temp_dir = $this->getThumbDestinationPath(); //Used for extracting GIF Animation Frames
           $gifresize->resize($this->getFile(),$this->getThumbDestination(),$this->getThumbResizeWidth(),$this->getThumbResizeHeight()); //Resizing the animation into a new file.
        
            //echo 'Animated GIF!';
            //echo $this->getFile() . '<br>';
            //echo $this->getThumbDestination();
            //die;
        
        } else {
        
            $original_image = imagecreatefromgif($this->getFile());

            $thumb_image = imagecreatetruecolor($this->getThumbWidth(), $this->getThumbHeight());

            $transparency = imagecolortransparent($original_image);

            if($transparency >= 0) {
                $transparency_color    = imagecolorsforindex($original_image, $transparency);
                $transparency    = imagecolorallocate($thumb_image, $transparency_color['red'], $transparency_color['green'], $transparency_color['blue']);
                imagefill($thumb_image, 0, 0, $transparency);
                imagecolortransparent($thumb_image, $transparency);
            }

            imagecopyresampled( $thumb_image, $original_image, $this->thumb['offset']['x'], $this->thumb['offset']['y'], 0, 0, $this->getThumbResizeWidth(), $this->getThumbResizeHeight(), $this->getWidth(), $this->getHeight() );
            imagegif( $thumb_image, $this->getThumbDestination() );

            imagedestroy($thumb_image);
            imagedestroy($original_image);
        
        }
    
		
		
	}

	public function thumbDestinationWritable(){
		return is_writable($this->getThumbDestinationPath());
	}
	
	public function getThumbQuality(){
		return $this->thumb['quality'];
	}

	public function getThumbPNGCompression(){
		return $this->thumb['png_compression'];
	}
	
	public function getThumbWidth(){
		return $this->thumb['width'];
	}

	public function getThumbHeight(){
		return $this->thumb['height'];
	}

	public function setThumbWidth($width){
		return $this->thumb['width'] = $width;
	}

	public function setThumbHeight($height){
		return $this->thumb['height'] = $height;
	}

	public function setThumbResizeWidth($width){
		return $this->thumb['resize_width'] = $width;
	}

	public function setThumbResizeHeight($height){
		return $this->thumb['resize_height'] = $height;
	}

	public function getThumbResizeWidth(){
		return $this->thumb['resize_width'];
	}

	public function getThumbResizeHeight(){
		return $this->thumb['resize_height'];
	}

	public function getThumbResizeMethod(){
		return $this->thumb['method'];
	}
    
    public function getAnimate(){
		return $this->thumb['animate'];
	}
	
	public function getAspectWidthFromHeight($height){
		$aspect = $this->getWidth() / $this->getHeight();
		return ceil($aspect * $height);
	}

	public function getAspectHeightFromWidth($width){
		$aspect = $this->getHeight()/$this->getWidth();
		return ceil($aspect * $width);
	}

	public function thumbExists(){
		return is_file( $this->getThumbDestination() );
	}

	public function getThumbDestination(){
		return $this->thumb['destination_path'].$this->thumb['destination_filename'];
	}
	
	public function getThumbDestinationFilename(){
		return $this->thumb['destination_filename'];
	}
	
	public function getThumbDestinationPath(){
		return $this->thumb['destination_path'];
	}
	
	public function getThumbData(){
		return readfile( $this->getThumbDestination() );
	}
    
    public function isAnimatedGif() { //Returns true if file is an animated gif
    
        $filecontents = file_get_contents($this->getFile());
     
        $str_loc = 0;
        $count = 0;
     
        // There is no point in continuing after we find a 2nd frame
        while ( $count < 2 ) {
        
            $where1 = strpos($filecontents,"\x00\x21\xF9\x04", $str_loc);
            
            if ( $where1 === FALSE ) {
                break;
            }
     
            $str_loc = $where1 + 1;
            $where2  = strpos( $filecontents, "\x00\x2C", $str_loc);
            
            if ($where2 === FALSE) {
                break;
            } else {
            
                if ( $where1 + 8 == $where2) {
                    $count++;
                }
            
                $str_loc = $where2 + 1;
            }
        }
     
        // gif is animated when it has two or more frames
        return ($count >= 2); 
    }
	
}

?>