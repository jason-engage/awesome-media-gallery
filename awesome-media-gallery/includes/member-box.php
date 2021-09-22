<?php	 
	 //set for all member pages
	$email_pub = $member->isMetaValue('email_public');
	$tot_images = number_format($member->getTotalUserImages(1));
	$tot_videos = number_format($member->getTotalUserImages(2));
	$tot_audios = number_format($member->getTotalUserImages(3));

?>
<li class="pure-u-r-1-3 member-box-li">
    <div class="avatar image loading">
        <a href="<?php echo getProfileUrl($member->getId()); ?>" title="<?php echo $member->getDisplayName();  ?>"><img data-src="library/thumb.php?f=<?php echo ($member->getAvatar() ? $member->getAvatar() : $config->site->default_avatar ); ?>&amp;h=250&amp;w=250&amp;m=crop" alt="<?php echo $member->getDisplayName(); ?>" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D"></a> 
    </div><!--/member-float-left-->
      
    <div class="user-meta">
        <div class="members-info">	         
            <div class="members-name"><?php echo $member->getDisplayName(); ?></div>
            <div class="members-region"><?php echo $member->getMetaValue('region'); ?></div>
        </div>	
        <div class="members-button">
            <a href="<?php echo getProfileUrl($member->getId()); ?>">
                <button class="pure-button pure-button-primary">
                    <span><?php echo $langscape["View"];?></span>
                </button>
            </a>
        </div>
        <div class="members-stats">
            <ul>
				<?php 
				if ( ($config->site->media->enable_images) && ($tot_images>0) ) {
				 ?>
                <li title="<?php echo $langscape["Total Images"];?>"><a href="<?php echo getProfileUrl($member->getId()); ?>/images"><i class="pictures icon"></i><?php echo number_format($member->getTotalUserImages(1));?></a></li>
				<?php 
				}
				if ( ($config->site->media->enable_videos) && ($tot_videos>0) ) {
				?>
                <li title="<?php echo $langscape["Total Videos"];?>"><a href="<?php echo getProfileUrl($member->getId()); ?>/videos"><i class="fa fa-youtube-play icon"></i><?php echo number_format($member->getTotalUserImages(2));?></a></li>
				<?php 
				}
				if ( ($config->site->media->enable_audio) && ($tot_audios>0) ) {
				?>
                <li title="<?php echo $langscape["Total Audios"];?>"><a href="<?php echo getProfileUrl($member->getId()); ?>/videos"><i class="fa fa-music icon"></i><?php echo number_format($member->getTotalUserImages(3));?></a></li>
                <?php 
	            } 
	            ?>
                <li title="<?php echo $langscape["Total Followers"];?>"><a href="<?php echo getProfileUrl($member->getId()); ?>/followers"><i class="users icon"></i><?php echo number_format($member->getTotalFollowers()); ?></a></li>

            </ul>
        </div> 
   </div>	    
</li>