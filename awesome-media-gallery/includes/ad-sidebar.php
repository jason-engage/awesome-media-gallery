<aside class="sidebar pure-u-1-4 ad-bar-side"><?php
	
    if( !empty($config->site->ads->sidebar_160x600) ) { ?>
        <div class="side-ad ad-space-160-600"><?php 
            echo $config->site->ads->sidebar_160x600; ?>
        </div><?php
    }
    
    if( !empty($config->site->ads->sidebar_300x250) ) { ?>
        <div class="side-ad ad-space-300-250"><?php 
            echo $config->site->ads->sidebar_300x250; ?>
        </div><?php
    } ?>
    
</aside>