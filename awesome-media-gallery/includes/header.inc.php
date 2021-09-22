<?php

	//SET TO STATIC
	$tablet_res_column_count = 3;

	// NEEDED FOR IMAGE-BOX.PHP
    $css_box_column_class = "c" . $config->site->grid->column_count;  	
	if ( ($deviceType == 'tablet') && ($config->site->mobile->enable_responsive_tablet) ) {
		$css_box_column_class = "c" . $tablet_res_column_count;
	}
    
    //WILL RE-COMPILE IF DEV MODE
    if ($config->site->dev_mode == 1) { //Less development mode. If turn on less will be recomplied on the fly.
        //autoCompileLess('css/style.less', 'css/style.css');


    	//SETTING LESS VARIABLES

    	//LOADING BOX HEIGHT
		$css_image_height = $hib . 'px'; 
    	
    	//COLUMN WIDTH AND MARGINS
    	//get margin
    	if (!$config->site->grid->margin) {
	    	$css_margin = 0;
    	} else {
	    	$css_margin = floatval($config->site->grid->margin);
    	}
    	
    	$css_box_width = floor_dec( round( ((100/$config->site->grid->column_count) - $css_margin), 3), 2, '.');
    	$css_box_margin = floor_dec( round( (100 - $css_box_width * $config->site->grid->column_count)/($config->site->grid->column_count - 1), 2 ), 1,'.'); //Round down a decimal
		
		
		//COMPILING FOR RESPONSIVE TABLETS
    	$css_box_width_tablet = floor_dec( round( ((100/$tablet_res_column_count) - $css_margin), 3), 2, '.');
    	$css_box_margin_tablet = floor_dec( round( (100 - $css_box_width_tablet * $tablet_res_column_count)/($tablet_res_column_count - 1), 2 ), 1,'.'); //Round down a decimal
    	    	
		
    	//WRAPPER WIDTH
    	if ($config->site->style->enable_full_width) { $css_wrapper_width = '95%'; } else { $css_wrapper_width = '980px'; }
    	if ($config->site->grid->enable_full_width) { $css_grid_wrapper_width = '95%'; } else { $css_grid_wrapper_width = '980px'; }

    	
    	if ( ($config->site->header->enable_bg_image) && ($config->site->header->bg_image) ) {
	    	$css_bg_ext = get_extension($config->site->header->bg_image);
			$css_bg_image = basename($config->site->header->bg_image);
    	} else {
	    	$css_bg_ext = '-';
	    	$css_bg_image = '-';
    	}
 
     	
    	//MENU BAR POSITION
    	if ( $disable_responsive && ($config->site->header->menu_position == 'TOP') ) {
	    	$css_menu_position = "0px";
	    	$css_menu_height = "45px";
	    	$css_menu_position_type = "absolute";
    	} elseif ($config->site->header->menu_position == 'TOP') {
	    	$css_menu_position = "0px";
	    	$css_menu_height = "45px";
	    	$css_menu_position_type = "fixed";
	    } else {
		    $css_menu_height = "0";
		    $css_menu_position_type = "static";
		    $css_menu_position = "0";
		}
   	
    	//HEADER HEIGHT - To Align Logo Button and Banner
    	if ($config->site->header->height) {
	    	
	    	$css_header_height = $config->site->header->height . 'px';
	    	
	    	//CENTER THE LOGO - TWEAK THE NUMBERS TO ADJUST FOR YOUR LOGO
	    	$css_header_center = ( ($config->site->header->height-140)/2 );
	    	$css_header_center += ($config->site->header->menu_position == 'TOP') ? 15:0; 	
	    	$css_header_center = $css_header_center . 'px';
	    	$css_menu_height = "0";
	    	
    	} else {
	    	$css_header_height = 'auto';
	    	$css_header_center = 'auto';	    	
    	}
		
		/*
    	if ($config->site->header->height) {
	    	$css_menu_position = $config->site->header->height . 'px';
    	} else {
	    	$css_menu_position = 'auto';
    	}
		*/
		
      	//CAROUSEL MARGINS
    	//get margin
    	if (!$config->site->carousel->margin) {
	    	$css_carousel_margin = 0;
    	} else {
	    	$css_carousel_margin = floatval($config->site->carousel->margin);
    	}  	
		
    	$less = new lessc;
		$less->setFormatter("compressed");
	    $less->setVariables( array(
	    	
	    	"loading-thumbnail-height" => $css_image_height,
	    	"box-width" => $css_box_width . '%',
	    	"box-margin" => $css_box_margin . '%',
	    	"header-img" => $css_bg_image,
	    	"header-img-ext" => $css_bg_ext,
	    	"header-height" => $css_header_height,
	    	"header-menu-height" => $css_menu_height,
	    	"header-menu-position" => $css_menu_position,
	    	"header-menu-position-type" => $css_menu_position_type,
	    	"header-center-adjust" => $css_header_center,
	    	"wrapper-width" => $css_wrapper_width,
	    	"grid-wrapper-width" => $css_grid_wrapper_width,
	    	"carousel-margin" => $css_carousel_margin . '%',
	    	"unfeature-text" => $langscape['un-feature'],
	    	"unslider-text" => $langscape['un-slider'],
	    	"uncarousel-text" => $langscape['un-carousel'],
	    	"slider-height" => $hsi . 'px',
	    	"slider-width" => $wsi . 'px',
	    	"loading-gif" => 'loading'.$config->site->style->loading
	    	
	    ) );
	    		
	    file_put_contents('css/style.css', $less->compileFile('css/style.less'));

	    //autoCompileLess('css/style.less','css/style.css', $less_variables);
		//trouble autoCompiling because of dynamic variables. Future fix
		
		//COMPILE TABLE VERSION
		if ( ($deviceType == 'tablet') && ($config->site->mobile->enable_responsive_tablet) ) {

    		$less_t = new lessc;
			$less_t->setFormatter("compressed");
		    $less_t->setVariables( array(		    	
		    	"box-width" => $css_box_width_tablet . '%',
		    	"box-margin" => $css_box_margin_tablet . '%'
		    ));
    		
	    	file_put_contents('css/tablet.css', $less_t->compileFile('css/tablet.less'));

			//autoCompileLess('css/tablet.less','css/tablet.css', $less_variables);
			
		}
		
		//COMPILE PHONE VERSION
		if ( ($deviceType == 'phone') && ($config->site->mobile->enable_responsive_phone) ) {
    		
    		$less_p = new lessc;
    		$less_p->setFormatter("compressed");

	    	file_put_contents('css/phone.css', $less_p->compileFile('css/phone.less'));
	    	
	    	//autoCompileLess('css/phone.less','css/phone.css');

		}
		//COMPILE MODALS FILE
		if ( ($enable_modals) ) {
    		$less_m = new lessc;
    		$less_m->setFormatter("compressed");
	    	file_put_contents('css/modal.css', $less_m->compileFile('css/modal.less'));
			
			//autoCompileLess('css/modal.less','css/modal.css');
		}
				
    } ?>