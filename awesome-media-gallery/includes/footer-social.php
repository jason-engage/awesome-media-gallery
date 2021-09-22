		    <div class="wrapper about-social">
			   	<ul>
	<?php 
	            if (!empty($config->site->footer->facebook)) { ?>
	                <li><a href="<?php echo $config->site->footer->facebook; ?>" target="_self" title="facebook page"><i class="fa fa-facebook icon"></i></a></li><?php
	            }
	            
	            if (!empty($config->site->footer->twitter)) { ?>
	                <li><a href="<?php echo convertTwitterUsernameUrl($config->site->footer->twitter); ?>" target="_self" title="twitter page"><i class="fa fa-twitter icon"></i></a></li><?php
	            }
	            
	            if (!empty($config->site->footer->pinterest)) { ?>
	                <li><a href="<?php echo $config->site->footer->pinterest; ?>" target="_self" title="pinterest page"><i class="fa fa-pinterest icon"></i></a></li><?php
	            }
	
	            if (!empty($config->site->footer->instagram)) { ?>
	                <li><a href="<?php echo $config->site->footer->instagram; ?>" target="_self" title="instagram page"><i class="fa fa-instagram icon"></i></a></li><?php
	            }
	
	            if (!empty($config->site->footer->google_plus)) { ?>
	                <li><a href="<?php echo $config->site->footer->google_plus; ?>" target="_self" title="google+ page"><i class="fa fa-google-plus icon"></i></a></li><?php
	            }
	
	            if (!empty($config->site->footer->flickr)) { ?>
	                <li><a href="<?php echo $config->site->footer->flickr; ?>" target="_self" title="flickr page"><i class="fa fa-flickr icon"></i></a></li><?php
	            }
	
	            if (!empty($config->site->footer->youtube)) { ?>
	                <li><a href="<?php echo $config->site->footer->youtube; ?>" target="_self" title="youtube page"><i class="fa fa-youtube icon"></i></a></li><?php
	            }
	
	            if (!empty($config->site->footer->vimeo)) { ?>
	                <li><a href="<?php echo $config->site->footer->vimeo; ?>" target="_self" title="vimeo page"><i class="fa fa-vimeo-square icon"></i></a></li><?php
	            }
	            
	            if (!empty($config->site->footer->blog)) { ?>
	                <li><a href="<?php echo $config->site->footer->blog; ?>" target="_self" title="wordpress page"><i class="fa fa-wordpress icon"></i></a></li><?php
	            }
				?>
			   	</ul>
		    </div>