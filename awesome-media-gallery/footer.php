<?php
//Carousel
if ( ($config->site->carousel->type == "OWL") && ($config->site->carousel->layout_style == "FOOTER") ) {
?>
<div <?php if (!$config->site->carousel->enable_fullscreen) { echo 'class="wrapper"'; } ?>>
	<?php include ('includes/carousel.php'); ?>
</div>
<?php
} 

if( $config->site->footer->enable_footer) { ?>

<div class="footer-container">
    
    <footer>
   
        <div class="top wrapper">
            
            <div class="pure-g-r">
        
                <div class="pure-u-1-2 float-left"> 
                    
                    <div id="footer-logo"><!-- Logo --> <?php 
                
                    if( $config->site->logo ) { ?>
                
                        <a href="index.php" title="<?php echo $config->site->name; ?> logo"><img src="<?php echo $config->site->logo; ?>"  alt="<?php echo $config->site->name; ?>"></a><?php 
                    } ?>
             
                    </div>
                
                    <div class="about-caption">
                        <?php echo $config->site->caption; ?>  
                    </div>
				    <?php
					    
				if ( ( $deviceType == 'computer' ) || ( ( $deviceType == 'phone' ) && !$config->site->mobile->enable_responsive_phone ) || ( ( $deviceType == 'tablet' ) && !$config->site->mobile->enable_responsive_tablet ) ) {
					
					include('includes/footer-social.php');
 
				} ?>    
                
                </div>
                
                <div class="pure-u-1-2 nav float-right"> 
                
                    <div class="pure-g-r">
                        
                        <div class="pure-u-1-3 link-col">
                        
                                <div class="title">&nbsp;</div>
                            
                                <ul class="links"><!--EMPTY-->
                                </ul>                    
                        </div>
                    
                        <div class="pure-u-1-3 link-col"> 
                            
                            <div class="title"><?php echo $langscape["Contact"];?></div>
                        
                            <ul class="links">
                                <li><a class="menu-item en-trigger" data-modal="modal-about" href="about.php" title="<?php echo $langscape["About Us"];?>"><?php echo $langscape["About Us"];?></a></li>
                                <li><a class="menu-item en-trigger" data-modal="modal-contact" href="contact.php" title="<?php echo $langscape["Email Us"];?>"><?php echo $langscape["Email Us"];?></a></li>
                                <li><a class="menu-item" href="rss.php" target="_blank" title="<?php echo $langscape["Rss Feed"];?>"><?php echo $langscape["Rss Feed"];?></a></li>

                            </ul>					
                    
                        </div>
                    
                        <div class="pure-u-1-3 link-col"> 
                            
                            <div class="title"><?php echo $langscape["Policies"];?></div>
                        
                            <ul class="links">
                                <li><a href="privacy-policy.php" target="_self" title="<?php echo $langscape["Privacy Policy"];?>" class="menu-item en-trigger" data-modal="modal-privacy"><?php echo $langscape["Privacy Policy"];?></a></li>
                                <li><a href="terms.php"><span class="menu-item en-trigger" data-modal="modal-terms" title="<?php echo $langscape["Terms and Conditions"];?>"><?php echo $langscape["Terms and Conditions"];?></span></a></li>
                            </ul>
                        
                        </div>
                
                    </div>
            
                </div>	
        
            </div>
	
        </div>
            
	    <div class="bottom">
		    <?php
			if ( ( ( $deviceType == 'phone' ) && $config->site->mobile->enable_responsive_phone ) || ( ( $deviceType == 'tablet' ) && $config->site->mobile->enable_responsive_tablet ) ) {
				
				include('includes/footer-social.php');
				
			 } ?>    
	        <div class="wrapper copyright"><?php echo $langscape["Copyright"] . ' &copy; ' . (date("Y") - 1) . '-' . date("Y") . ' ' . $config->site->name; ?>
	        	<span class="engage-mark"><?php echo $langscape["Developed by"];?>&nbsp;<a href="http://en.gg" title="Engage Web Development">ENGAGE</a></span>
	        </div>
	        
	    </div>
	    
	    <img src="trans.gif" height="1" width="1" border="0" class="trans">

    </footer>	

</div><?php
        
} //ENABLE FOOTER


if ($enable_modals) {
	
    include ('includes/modals.php'); 
	?>
	<!-- Overlay element for modals -->
	<div class="en-overlay"></div><?php
	
} else { ?>

<script type="application/javascript">
	
	$(document).ready(function() {
		$(".en-trigger").removeClass("en-trigger");
		$("[data-modal]").removeAttr("data-modal");
	});
</script>

<?php } ?>

<!-- Scripts Start Here -->
<script type="application/javascript" src="js/main.js"></script>

<script type="application/javascript"><?php

//loading data for editable fields

if(!empty($galleries_data)) {
	//echo "var gallery_id_array = ". $gallery_id_array_encoded . ";\n";
    //echo "var gallery_name_array = ". $gallery_name_array_encoded . ";\n";
    echo "var galleries_data = ". $galleries_data . ";\n";
} else {
    echo "var galleries_data = '';\n";
}

if(!empty($users_types_data)) {
	//echo "var users_types_id_array = ". $users_types_id_array_encoded . ";\n";
	//echo "var users_types_name_array = ". $users_types_name_array_encoded . ";\n"; 
	echo "var users_types_data = ". $users_types_data . ";\n";
}

echo "txt_placeholder_arr = ". json_encode($txt_placeholder_arr) . ";\n";
?>
</script>


<?php if ( (!$config->site->style->enable_minified) ) { ?>

<!-- Modernizer -->
<script type="application/javascript" src="js/vendor/modernizr.custom.min.js"></script>

<!-- Drag Avatar & Resample-->
<script type="application/javascript" src="js/vendor/avatar.min.js"></script>
<script type="application/javascript" src="js/vendor/cover-photo.min.js"></script>
<script type="application/javascript" src="js/vendor/resample.js"></script>

<!-- Dropdowns for the menu -->
<script type="application/javascript" src="js/vendor/jquery.dropdown.min.js"></script>

<!-- Fancy Select -->
<script type="application/javascript" src="js/vendor/fancySelect.min.js"></script>

<!-- Nice tags -->
<script type="application/javascript" src="js/vendor/bootstrap-tagmanager.min.js"></script>	

<?php } ?>   

<?php 
	if ( ($config->site->style->enable_cdn) && (!$config->site->style->enable_minified) ) { ?>

<!-- Inline editing script -->
<script type="application/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jeditable.js/1.7.3/jeditable.min.js"></script>

<!-- NProgress AJAX Bar Used for Fancybox Too-->
<script type="application/javascript" src="//cdnjs.cloudflare.com/ajax/libs/nprogress/0.1.2/nprogress.min.js"></script>

<!-- Sticky JS -->
<script type="application/javascript" src="//cdn.jsdelivr.net/jquery.sticky/1.0.0/jquery.sticky.min.js"></script>

<?php 
	} elseif ( (!$config->site->style->enable_cdn) && (!$config->site->style->enable_minified) ) { ?>

<script type="application/javascript" src="js/vendor/jeditable.min.js"></script>
<script type="application/javascript" src="js/vendor/nprogress.min.js"></script>
<script type="application/javascript" src="js/vendor/jquery.sticky.min.js"></script>
<?php 
	}
	
if ( ( ($deviceType == 'phone') && (!$config->site->mobile->enable_responsive_phone) ) || ($deviceType == 'tablet') || ($deviceType == 'computer') ) { ?>

<script type="application/javascript">
	$(document).on('ready', function () {
	 	if ( $('#user-menu').length > 0 ) {
			$("#user-menu").sticky( {
			    topSpacing:0
			} );
		}
	});
</script>

<?php 
}

	//PAGE LOADER
	if (isset($config->site->enable_page_loader) && ($config->site->enable_page_loader==true)) {
?>
		<script type="application/javascript">
		//LOAD PROGRESS BAR
        
        var Loading = {}
        Loading.timer = setInterval(function() { 
                NProgress.inc()
            } , 1000); 
            
        Loading.clear = function ClearMyInt(interval) {
            clearInterval(interval);
            interval = 0;
        }
        
        $(document).ready(function() { // executes when HTML-Document is loaded and DOM is ready
            NProgress.start();
            Loading.timer;
        });
        
        $(window).load(function() {
            NProgress.done();
            Loading.clear(Loading.timer);
        });
        
		</script>
<?php
	}

//DISABLE FANCYBOX ON MOBILE
if ( $disable_responsive || ($deviceType == 'computer') ) { 
	
	if (!$config->site->style->enable_minified)  { 
?>
    <!-- FancyBox & Media Helper -->
    <script type="application/javascript" src="js/vendor/jquery.fancybox.mod.min.js"></script>
    <!--<script src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-media.js"></script>-->
    <script type="application/javascript" src="js/vendor/jquery.fancybox-media.min.js"></script>  
<?php
	}
}

if ($enable_modals) { ?>

	<?php  if ( (!$config->site->style->enable_minified) ) { ?>
    <!-- classie.js by @desandro: https://github.com/desandro/classie -->
    <script src="js/vendor/classie.min.js"></script>

    <!-- modalEffects.js by @codedrops: http://tympanus.net/codrops/2013/06/25/nifty-modal-window-effects/ -->
    <script src="js/vendor/modalEffects.min.js"></script>
	<?php }
}

if ( ( ($deviceType == 'phone') && (!$config->site->mobile->enable_responsive_phone) ) || ($deviceType <> 'phone') )  { ?>
	
	<?php  if ( (!$config->site->style->enable_minified) ) { ?>
    <!-- Carousel JS For Single image page -->
    <script src="js/vendor/jquerypp.custom.min.js"></script>
    <script src="js/vendor/jquery.elastislide.min.js"></script>
	<?php }
} ?>

<script type="application/javascript">
	$(document).on('ready', function () {			
		if ($('#carousel').length > 0) {
			$( '#carousel' ).elastislide({
				speed : 500,
				easing : 'ease-in-out'
			});	 
		}
	});
</script>

<?php if ( ($config->site->style->enable_cdn) && (!$config->site->style->enable_minified) ) { ?>

<!-- iCheck -->
<script type="application/javascript" src="//cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.1/icheck.min.js"></script>
<!-- fitVids -->
<script type="application/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fitvids/1.0.1/jquery.fitvids.min.js"></script>

<?php } elseif ( (!$config->site->style->enable_cdn) && (!$config->site->style->enable_minified) ) { ?>

<script type="application/javascript" src="js/vendor/jquery.icheck.min.js"></script>
<script type="application/javascript" src="js/vendor/jquery.fitvids.min.js"></script>

<?php } ?>

<?php 

if (basename($_SERVER['SCRIPT_NAME']) == $image_page) { ?>

<?php  if ( (!$config->site->style->enable_minified) ) { ?>
<!-- SocialCount -->
<script type="application/javascript" src="js/vendor/socialcount.min.js"></script>
<?php } ?>

<script type="application/javascript">
	$(document).bind('ready', function () {

 	    if ( $('.socialcount').length > 0 ) {
			    
		    //SOCIAL COUNT
			$URL = window.location.href.toString();
		   	        
		    // Facebook Shares Count
		    $.getJSON( 'https://graph.facebook.com/?id=' + $URL, function( fbdata ) {
		    	if (fbdata.shares) {
		        	$('.facebook-count').text( ReplaceNumberWithCommas(fbdata.shares))
		        }
		    });
		    
		    // Twitter Shares Count
		    $.getJSON( 'https://cdn.api.twitter.com/1/urls/count.json?url=' + $URL + '&callback=?', function( twitdata ) {
			    if (twitdata.count > 0) {
		        	$('.twitter-count').text( ReplaceNumberWithCommas(twitdata.count))
		        }
		    });
		
		    // Pinterest Shares Count
		    $.getJSON( 'https://api.pinterest.com/v1/urls/count.json?url=' + image_url + '&callback=?', function( pindata ) {
			    if (pindata.count > 0) {
		        	$('.pin-count').text( ReplaceNumberWithCommas(pindata.count))
		        }
		    });

		    /*
		    // GOOGLE PLUS
		    $.postJSON( 'https://clients6.google.com/rpc',
		    
			[{
			    "method":"pos.plusones.get",
			    "id":"p",
			    "params":{
			        "nolog":true,
			        "id":'http://google.com',
			        "source":"widget",
			        "userId":"@viewer",
			        "groupId":"@self"
			        },
			    "jsonrpc":"2.0",
			    "key":"p",
			    "apiVersion":"v1"
			}], function( googledata ) {
		        $('.google-count').text( ReplaceNumberWithCommas(googledata.count))
		    });
		    console.log ('google-shares: ' + googledata);
		    */
		
		}    
	});
	
</script>
	    
<?php
	}   
?>

<?php
	// OWL CAROUSEL / SLIDER
if ( ($config->site->carousel->type == "OWL") || ($config->site->slider->type == "OWL") ) {

 	if ( (!$config->site->style->enable_minified) ) {
 		echo '<script src="js/vendor/owl.carousel.custom.min.js"></script>';
 	}
} 
		
		if ($config->site->slider->type == "OWL") { // OWL SLIDER
		
	 	?>
	 	 	<script type="application/javascript">
	 	 	$(document).ready(function(){
	 	 		if ( $('#slider.owl-carousel').length > 0 ) {
						$("#slider.owl-carousel").owlCarousel({
								
							loop:true,
							center: false,
							mouseDrag: true,
							touchDrag: true,
							pullDrag: false,
							freeDrag: false,
							stagePadding: 0,
							mergeFit: true,
							autoWidth: false,
							autoHeight: false,
							startPosition: 0,
							navRewind: true,
							navText: ["<i class='fa fa-chevron-circle-left icon'></i>","<i class='fa fa-chevron-circle-right icon'></i>"],
							slideBy: 1,
							dotsEach: false,
							dotData: false,
							autoplayHoverPause: true,						
							autoplayTimeout: 7000,
							smartSpeed: 750,
							autoplaySpeed: false,
							responsiveClass: false,
							videoHeight: false,
							videoWidth: false,
							fallbackEasing: 'swing',
							lazyLoad: true,
							margin: 0,
							items: 1,
							animateOut: '<?php echo str_replace('ZZ', 'Out', $config->site->slider->effect_owl); ?>',
							animateIn: '<?php echo str_replace('ZZ', 'In', $config->site->slider->effect_owl); ?>',
							autoplay: <?php echo $config->site->slider->enable_autoplay;?>,
							nav: <?php echo $config->site->slider->enable_navigation;?>,
							dots: <?php echo $config->site->slider->enable_dots;?>,						
							video: <?php echo $config->site->slider->enable_video_play;?>
						});
					}
				});
			</script>
	 	<?php
	 	} 
	
	if ($config->site->carousel->type == "OWL") {
		
		?>
		
	 	<script type="application/javascript">
	 	 	$(document).ready(function(){

	 	 		if ( $('#owl.owl-carousel').length > 0 ) {

				  	$("#owl.owl-carousel").owlCarousel({
				  			  		
						loop:true,
						center: false,
						mouseDrag: true,
						touchDrag: true,
						pullDrag: false,
						freeDrag: false,
						stagePadding: 0,
						mergeFit: true,
						autoWidth: false,
						autoHeight: false,
						startPosition: 0,
						navRewind: true,
						navText: ["prev","next"],
						slideBy: 1,
						dotsEach: false,
						dotData: false,
						autoplayHoverPause: true,						
						responsiveClass: false,
						videoHeight: false,
						videoWidth: false,
						fallbackEasing: 'swing',
						smartSpeed: 0,
						autoplayTimeout: 0,
						autoplaySpeed: 3000,
						slideTransition: 'linear',

						autoplay: <?php echo $config->site->carousel->enable_autoplay;?>,
						nav: <?php echo $config->site->carousel->enable_navigation;?>,
						dots: <?php echo $config->site->carousel->enable_dots;?>,						
						video: <?php echo $config->site->carousel->enable_video_play;?>,
						lazyLoad: true,
						margin: <?php echo $css_carousel_margin; ?>,
						responsive:{
						    0:{
						        items: 2
						    },
						    479:{
						        items:<?php echo ($config->site->carousel->column_count-3); ?>
						    },
						    768:{
						        items:<?php echo ($config->site->carousel->column_count-2); ?>
						    },
						    980:{
						        items:<?php echo ($config->site->carousel->column_count-1); ?>
						    },
						    1199:{
						        items:<?php echo ($config->site->carousel->column_count); ?>
						    }
						}
						
				  	});
				}  					  	
			});
		</script>
	 	<?php
	}

	//LOAD MASONRY ON HOME AND MEMBER PAGE ONLY
	if ( ($config->site->grid->type == "MASONRYJS") && 
	( (basename($_SERVER['SCRIPT_NAME']) == $home_page) || (basename($_SERVER['SCRIPT_NAME']) == $member_page) ) ) { ?>
	
	<!-- MASONRY GRID -->
	<?php if ( ($config->site->style->enable_cdn) && (!$config->site->style->enable_minified) ) { ?>
	<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/masonry/3.1.5/masonry.pkgd.min.js"></script>
	<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/3.1.8/imagesloaded.min.js"></script>
	<?php } elseif ( (!$config->site->style->enable_cdn) && (!$config->site->style->enable_minified) ) { ?>
	<script type="application/javascript" src="js/vendor/masonry.pkgd.min.js"></script>
	<script type="application/javascript" src="js/vendor/imagesloaded.min.js"></script>	
	<?php } ?>

	<script type="application/javascript">
	var msnry;
	
	$(document).ready(function(){
		
		if ( $('.awesome-gallery').length > 0 ) {
			
			msnry = new Masonry( '.awesome-gallery', {itemSelector:'.box',columnWidth: '.grid-sizer', gutter: '.gutter-sizer', hiddenStyle: { opacity: 0 } });
			// layout Masonry again after all images have loaded
			imagesLoaded( '.awesome-gallery', function() {
				msnry.layout();
			});
		}
		
  	});
  	
  	</script>

<?php }
	
	// PAGINATION - INFINITE SCROLL ON HOME, MEMBERS, and MEMBER PAGES
	if ( ($config->site->grid->pagination_type == 2) && 
	((basename($_SERVER['SCRIPT_NAME']) == $home_page) || (basename($_SERVER['SCRIPT_NAME']) == $members_page) || (basename($_SERVER['SCRIPT_NAME']) == $member_page) ) ) {  
		
 		if ( (!$config->site->style->enable_minified) ) { ?>
		<script src="js/vendor/jquery.waypoints.min.js"></script>
		<?php } ?>		
		
		<script type="application/javascript">
		var waypoint;
		
		$(window).load(function() {
		    $loadmore = $('.paginator');
		
			if (document.getElementsByClassName('load-more').length>0) {
				waypoint = new Waypoint({
				  element: document.getElementsByClassName('load-more'),
				  handler: function() {
				    //console.log('fired');
				    $('.load-more').trigger('click');
				  },
				  enabled: true,
				  offset: '130%'
				});	
			}	
		});
		</script> 
		
<?php } ?>

<!-- COOOKIES NOTIFICATION -->
<?php if ($config->site->style->enable_cookies_notification) {
	
		if ( (!$config->site->style->enable_minified) ) { ?>
	<script type="application/javascript" src="js/vendor/jquery.cookiebar.min.js"></script>
	<?php } ?>

<script type="application/javascript">	
	$(document).ready(function(){
		$.cookieBar({
			message: '<?php echo $langscape["We use cookies"]; ?>',
			acceptButton: true,
			acceptText: '<?php echo $langscape["Accept Cookies"]; ?>',
			declineButton: false,
			declineText: '<?php echo $langscape["Refuse Cookies"]; ?>',
			policyButton: true,
			policyText: '<?php echo $langscape["Privacy Policy"]; ?>',
			policyURL: 'privacy-policy.php',
			autoEnable: true,
			acceptOnContinue: false,
			expireDays: 365,
			forceShow: false,
			effect: 'slide',
			element: 'body',
			append: false,
			fixed: true,
			bottom: true,
			zindex: 100,
			redirect: '/',
			domain: '<?php echo str_replace("http://","",$config->site->url); ?>',
			referrer: '<?php echo $_SERVER['SCRIPT_NAME']; ?>'
		});
	});
</script>

<?php } ?>

<?php if ( ($config->site->style->enable_cdn) && (!$config->site->style->enable_minified) ) { ?>
<script type="application/javascript" src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha256.js"></script>
<?php } elseif ( (!$config->site->style->enable_cdn) && (!$config->site->style->enable_minified) ) { ?>
<script type="application/javascript" src="js/vendor/sha256.min.js"></script>
<?php } ?>

<?php if ( ($deviceType == 'phone') && ($config->site->mobile->enable_responsive_phone) ) { ?>
	<script src="js/vendor/ui.min.js"></script>
<?php } ?>

<!-- jQuery Upload Scripts -->
<script type="application/javascript" src="upload/js/vendor/jquery.ui.widget.js"></script>
<script type="application/javascript" src="upload/js/jquery.fileupload.js"></script>

<script type="application/javascript" src="js/plugins.js"></script>

<!-- AMG Custom Files -->
<script src="js/custom.js"></script>

<?php if ($config->site->style->enable_minified) { ?>
<script type="application/javascript" src="js/vendor/all.min.js"></script>
<?php } ?>

<script type="application/javascript">
    mokoala.base_href = '<?php print $config->site->base_href; ?>';
    mokoala.site_href = '<?php print $config->site->url; ?>';
    mokoala.settings_upload_max_filesize = '<?php print $config->site->settings->upload_max_filesize; ?>';
</script> 


<!-- Google Analytics Tracking Code -->
<?php print $config->site->analytics; ?>

<!-- Scripts End Here -->
</div> <!--LAYOUT-->
</body>
</html>
