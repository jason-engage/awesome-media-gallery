<?php
//Create/get instances.
$activity_paginator = new MK_Paginator( MK_Request::getParam('page', 1), 15 );

$activity_feed = $profile->getPublicActivity($activity_paginator); 
//$activity_feed = $profile->getPrivateActivity(false, $activity_paginator);

?>

<section class="content activity"><?php
		
    if( !empty($activity_feed) ) { //Activity feed is not empty. ?>
    
        <ul class="cbp_tmtimeline" data-autoload="true"><?php
			
            foreach( $activity_feed as $activity_feed_single ) {
				
				$related_user          = $activity_feed_single->objectRelatedUser(); 
				$activity_time         = MK_Utility::timeSince( strtotime($activity_feed_single->getDateTime()) );
				$activity_text         = $activity_feed_single->getText();
				$activity_type         = $activity_feed_single->getType();
				$member_user_id        = $related_user->getId();
				
				switch ($activity_type) {
				case 'comment':
					$icon = 'chat';
					break;   
				case 'like':
					$icon = 'heart';
					break; 
				case 'follow':
					$icon = 'users';
					break; 
				case 'upload':
					$icon = 'upload-2';
					break;
				default: 
					$icon = 'upload-2';
					break; 
				} ?>

				<li>                   
			        <time class="cbp_tmtime" datetime="<?php echo $activity_time; ?>">
				        <span>
				        	<?php echo format_time($activity_time); ?> <?php echo $langscape["ago"];?>
				        </span>
			        </time>
			        <div class="cbp_tmicon <?php echo $icon; ?> icon"></div> 
			        <div class="cbp_tmlabel">
				        <span>
			        	<?php echo $activity_text; ?>
			        	</span>
			        </div>                   
				</li><?php
			} ?>
        </ul>
            
        <div class="paginator"><?php echo $activity_paginator->render('member.php?section=activity&user='.$profile_id.'&page={page}'); ?></div><?php
    
    } else { ?>
	
        <p class="alert alert-information"><?php echo $profile_name; ?> <?php echo $langscape["doesn't have any recent activity"];?></p><?php
        
    } ?>
    
</section>