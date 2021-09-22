	<div class="ad-bar-top" id="ad_holder"><?php
	    
	    if ( !empty($config->site->ads->top_728x90) ) { ?>
	        <div class="ad-space-728-90">
	            <?php echo $config->site->ads->top_728x90; ?>
	        </div><?php
	    }
	    
	    if ( !empty($config->site->ads->top_242x90) ) { ?>
	        <div class="ad-space-242-90">
	            <?php echo $config->site->ads->top_242x90; ?>
	        </div><?php
	    }

	    if ( !empty($config->site->ads->top_970x90) ) { ?>
	        <div class="ad-space-970-90">
	            <?php echo $config->site->ads->top_970x90; ?>
	        </div><?php
	    } 
	    
	    if ( !empty($config->site->ads->top_980x120) ) { ?>
	        <div class="ad-space-980-120">
	            <?php echo $config->site->ads->top_980x120; ?>
	        </div><?php
	    }?>
	    	    
	</div>
