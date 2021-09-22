<?php 
header('Content-type: text/html; charset=utf-8'); 
header("Last-Modified: " . gmdate('D, d M Y H:i:s', time() ) . ' GMT' );

if ( $config->site->style->enable_cached_headers ) {
	$offset = 3600 * 1; //1 Hour
	header("Cache-Control: max-age=3600, public");
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
    <base href="<?php echo $config->site->url; ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=10" />
    <meta charset="UTF-8">
    
    <?php
    //Custom SEO and Social Meta Tags
    include ('includes/social-meta-tags.php'); ?>

    <title><?php echo htmlspecialchars($meta_title); ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>"> <?php 
    
    if( !empty($config->site->google_site_verification) ) { ?>
    <meta name="google-site-verification" content="<?php echo $config->site->google_site_verification; ?>" />
	<?php
	}
	
    if( !empty($config->site->facebook->app_id) && $config->site->facebook->app_id ) { ?> 
    <meta property="fb:app_id" content="<?php echo $config->site->facebook->app_id; ?>"/><?php 
    } ?>
    
    <meta property="og:locale" content="en_US"/>
    <meta property="og:url" content="<?php echo "http://" . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']); ?>"/>
    <meta property="og:site_name" content="<?php echo $meta_sitename_clean; ?>"/>
    <meta property="og:title" content="<?php echo htmlspecialchars($meta_title) ?>">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:image" content="<?php echo $meta_image_src; ?>">
    <?php echo $facebook_meta; ?>
    
    <meta property="twitter:card" content="<?php echo $meta_twitter_card_type; ?>">
    <meta property="twitter:site" content="<?php echo $site_twitter_handle; ?>">
    <meta property="twitter:creator" content="<?php echo $meta_twitter_creator;?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($meta_title); ?>">
    <meta property="twitter:description" content="<?php echo $meta_description; ?>">
    <meta property="twitter:image" content="<?php echo $meta_image_src; ?>">
    <?php echo $twitter_meta;
    
    if ( ($deviceType == 'phone') && (!$config->site->mobile->enable_responsive_phone ) ) {  ?>
        <meta name="viewport" content="width=1040"><?php
    } elseif ( ($deviceType == 'tablet') && (!$config->site->mobile->enable_responsive_tablet ) ) { ?>
        <meta name="viewport" content="width=1100"><?php
    } else { ?>
        <meta name="viewport" content="width=device-width, user-scalable=0"><?php
    } ?>

    <!-- Favicon  -->
    <link href="favicon.png" rel="icon" type="image/x-icon">
   
    <!-- Style Sheets Start Here -->
    
<?php if ($config->site->style->enable_minified) { ?>
    <link rel="stylesheet" type="text/css" href="css/vendor/pure-min-0.2.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/entypo.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/socicon.css">
<?php }else { ?>
    <link rel="stylesheet" type="text/css" href="css/vendor/pure-min-0.2.css">
    <!--ALL.CSS-->
    <link rel="stylesheet" type="text/css" href="css/vendor/jquery.dropdown.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/jquery-editable.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/component.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/activity-feed.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/elastislide.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/nprogress.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/jquery.fancybox.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/socialcount.min.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/fancySelect.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/animate.min.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/green.css">
    <!-- -->
    <link rel="stylesheet" type="text/css" href="css/vendor/entypo.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/socicon.css">
<?php } ?>

	<?php include('includes/header.inc.php'); //Contains important variables ?>
	    
    <link rel="stylesheet" type="text/css" href="css/style.css"><?php 

    if ( ($deviceType == 'phone') && ($config->site->mobile->enable_responsive_phone ) ) { ?>
        <link rel="stylesheet" type="text/css" href="css/phone.css"><?php
    }
    
    if ( ($deviceType == 'tablet') && ($config->site->mobile->enable_responsive_tablet ) ) { ?>
        <link rel="stylesheet" type="text/css" href="css/tablet.css"><?php
    } 
    
    if ( ($enable_modals) ) { ?>
	    <link rel="stylesheet" type="text/css" href="css/modal.css"><?php 
	} ?>

    <!-- Stlye Sheets End Here -->

	
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <!--[if lt IE 9]>
      <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
    <![endif]-->
</head>

<body>
  <!--[if lt IE 10]>
  <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
  <![endif]-->

<?php

if (!empty($config->site->media->comments_type) && ( $config->site->media->comments_type == 'FACEBOOK' ) ) {
?>

<div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo (!empty($config->site->facebook->app_id) ? $config->site->facebook->app_id:''); ?>',
          status     : true,
          xfbml      : true
        });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/all.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>
 
<?php 

	}

?>

<?php
//Get Galleries for menus
 if ( !empty($config->site->media->enable_videos) ) { 
	$gallery_list             = MK_RecordModuleManager::getFromType('image_gallery');
	$search_criteria_video[]  = array('literal' => "(`type_gallery` = 2)");
	$gallery_videos           = $gallery_list->searchRecords($search_criteria_video);
}
 if ( !empty($config->site->media->enable_images) ) { 
	$gallery_list_images      = MK_RecordModuleManager::getFromType('image_gallery');
	$search_criteria_images[] = array('literal' => "(`type_gallery` = 1)");
	$gallery_images           = $gallery_list_images->searchRecords($search_criteria_images);
}

 if ( !empty($config->site->media->enable_audio) ) { 
	$gallery_list_audio      = MK_RecordModuleManager::getFromType('image_gallery');
	$search_criteria_audio[] = array('literal' => "(`type_gallery` = 3)");
	$gallery_audio           = $gallery_list_audio->searchRecords($search_criteria_audio); 
}

?>
<div id="layout">

<?php 
// SHOW SIDEBAR MENU
if ( ($deviceType == 'phone') && $config->site->mobile->enable_responsive_phone ) {

	include ('includes/menu-mobile.php'); 

}	
?>
<div class="header-container <?php if ( $user->isAuthorized() ) echo 'logged-in'; ?>" style="<?php if (!empty($css_bg_image)) {echo 'background-image: url(tpl/uploads/'.$css_bg_image.');'; } ?>">

    <header class="clearfix">

<?php	if ($config->site->header->menu_position == 'TOP') { include ('includes/menu.php'); } ?>
 		
<?php	if ($config->site->header->enable_header) { ?>
        <div class="wrapper">
    
            <div class="pure-g-r header-content">
      
                <div class="pure-u-1-2 logo">
          
                    <div id="logo"><!-- Logo --><?php 
            
                        if( $config->site->logo ) { ?>
                
                        <a href="<?php echo $config->site->url; ?>"><img src="<?php echo $config->site->logo; ?>"  alt="<?php echo $meta_sitename_clean; ?>"></a><?php 
                         
                        } ?>
                        
                    </div>
          
                    <div class="pure-g"> 
                    
                        <div class="tagline">
                            <span id="tagline"><?php echo $config->site->caption; ?></span>	
                        </div>
            
                    </div>
          
                </div>

                <div class="pure-u-1-2 header-sub">
	                
                 	<?php 
                 	
                 	// ADVERTISING
                 	if ( $config->site->ads->enable_header && $disable_responsive ) {

                 		include ('includes/ad-header.php');
				 	
				 	}
				 	
				 	$sign_up_disabled = false;
				 	if ( $user->isAuthorized() || $config->site->members->disable_registration ) {
					 	$sign_up_disabled = true;
					}
                 	?>
				 	<div class="button-container <?php echo ($sign_up_disabled)? 'disabled': ''; ?>">
	                 	<div class="button-flip">
							
					<?php
					//IF SIGNED-IN && UPLOADS ALLOWED
					if ( !$config->site->members->disable_uploads ) {
					?>
							<a href="upload-choose.php" class="en-trigger" data-modal="modal-choose">
								<button class="button-face front">
					 				<?php echo $langscape['Upload Now']; ?>
					 			</button>
					 		</a>
				 	<?php
					}
					?>

					<?
					//IF NOT SIGNED IN && SIGNUPS ALLOWED
					if ( !$sign_up_disabled && !$config->site->wordpress->strict_login) {
					?>
							<a href="sign-up.php" class="en-trigger" data-modal="modal-sign-up">
								<button class="button-face back">
				 					<?php echo $langscape['Sign Up']; ?>
				 				</button>
							</a>
				 	<?php
					} elseif ( !$sign_up_disabled && $config->site->wordpress->strict_login) {
					?>
							<a href="sign-in.php?platform=wordpress">
								<button class="button-face back">
				 					<?php echo $langscape['Sign In']; ?>
				 				</button>
							</a>
					<?php 
					} 
					?>
						</div>
				 	</div>
				 						
                </div><!-- HEADER RIGHT SIDE -->

            </div><!-- HEADER ROW -->
      
        </div><!-- Wrapper Ends -->
<?php 	}	?>

<?php	if ($config->site->header->menu_position == 'BOTTOM') { include ('includes/menu.php'); } ?>

    </header>

</div>

<?php
//Carousel
if ( ($config->site->carousel->type == "OWL") && ($config->site->carousel->layout_style == "HEADER") && (basename($_SERVER['SCRIPT_NAME']) <> $upload_details_page) ) {
?>
<div <?php if (!$config->site->carousel->enable_fullscreen) { echo 'class="wrapper"'; } ?>>
	<?php include ('includes/carousel.php'); ?>
</div>
<?php
} 
?>