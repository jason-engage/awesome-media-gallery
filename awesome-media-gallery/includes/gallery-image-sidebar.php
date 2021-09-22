<?php 
$rec_fav = $author->getTotalReceivedFavourites();
$rec_com = $author->getTotalReceivedComments();
$rec_view = $author->getTotalReceivedViews();

$tot_images = number_format($author->getTotalUserImages(1));
$tot_videos = number_format($author->getTotalUserImages(2));
$tot_audios = number_format($author->getTotalUserImages(3));

$tot_followers = number_format($author->getTotalFollowers());
?>

<aside class="sidebar image-single pure-u-5-24" id="image-sidebar">
	<div class="sidebar-wrapper <?php if ($config->site->style->enable_full_width) { echo 'full-width-on'; } ?>">
		<span class="meta-avatar image loading">
			<a href="<?php echo $author->getUsername(); ?>"><img data-src="library/thumb.php?f=<?php echo ( $author_avatar ? $author_avatar : $config->site->default_avatar ); ?>&amp;m=crop&amp;w=225&amp;h=225" alt="<?php echo $author_name; ?>"></a>
		</span>
		
		<div class="meta-user-data">
	  
	    	<a href="<?php echo $author->getUsername(); ?>"><button class="pure-button pure-button-primary"><span><?php echo $langscape["View Profile"];?></span></button></a>
			<!--<button class="pure-button pure-button-primary pure-button-active check icon" data-style="slide-up"><span>Following</span></button>-->
	    <?php 
	    include 'includes/generate-follow-button.php';
	    
	    if (!empty($following_button)) {
	 	   echo $following_button;
	    } ?>				
			<dl>
				<dt class="username"><?php echo $author_name; ?></dt>
				<dd class="meta-location"><?php echo $author->getMetaValue('region'); ?></dd>
				<dd class="meta-since"><?php echo $langscape["Member since:"];?> <?php  
					
					$member_since = MK_Utility::timeSince( strtotime($author->renderDateRegistered())); 
					$member_since_time = format_time($member_since);
					echo $member_since_time; ?>
				</dd>
			</dl>
		</div>
		
		<div class="meta-user-details">
			
			
			<?php if( $level = $author->getMetaValue('category') ) { ?><span class="category meta-title"><?php echo $langscape["Category"];?><span class="meta"><?php echo $users_types_array_combined[$level]; ?></span></span><?php } ?>
			
			<?php if( $occupation = $author->getMetaValue('occupation') ) { ?><span class="category meta-title"><?php echo $langscape["Occupation"];?><span class="meta"><?php echo $occupation; ?></span></span><?php } ?>
			
			<?php if( $ye = $author->getMetaValue('years_of_experience') ) { ?><span class="category meta-title"><?php echo $langscape["Experience"];?><span class="meta"><?php echo $ye; ?> <?php echo $langscape["years"];?></span></span><?php } ?>
			
			<?php if ($config->site->members->enable_available) { ?>
			<span class="height meta-title"><?php echo $langscape["Freelancing"];?><span class="meta"><?php echo $author->isMetaValue('available_for_freelance') ? 'Yes' : 'No'; ?></span></span>
			<?php }?>
			
		</div>
		
		<div class="meta-user-stats">
			<?php 
			if ( ($config->site->media->enable_images) && ($tot_images>0) ) {
			 ?>
			<span data-image-id="<?php echo $tot_images; ?>" class="pure-u meta-hearts stats">
				<a href="<?php echo getProfileUrl( $author_id, 'images' ); ?>">
					<i class="pictures icon"></i>
					<span class="attribute"><?php echo $langscape["Images"];?></span><span class="text"><?php echo $tot_images; ?></span>
				</a>
			</span>	
			<?php 
			}
			if ( ($config->site->media->enable_videos) && ($tot_videos>0) ) {
			 ?>
			<span data-view-id="<?php echo $tot_videos; ?>" class="pure-u meta-views stats">
				<a href="<?php echo getProfileUrl( $author_id, 'videos' ); ?>">
					<i class="fa fa-youtube-play icon"></i>
					<span class="attribute"><?php echo $langscape["Videos"];?></span><span class="text"><?php echo $tot_videos; ?></span>
				</a>
			</span>
			<?php 
			}
			if ( ($config->site->media->enable_audio) && ($tot_audios>0) ) {
			 ?>
			<span data-view-id="<?php echo $tot_audios; ?>" class="pure-u meta-views stats">
				<a href="<?php echo getProfileUrl( $author_id, 'audios' ); ?>">
					<i class="fa fa-soundcloud icon"></i>
					<span class="attribute"><?php echo $langscape["Audios"];?></span><span class="text"><?php echo $tot_audios; ?></span>
				</a>
			</span>
			<?php 
			}
			if ($tot_followers>0) {
			?>
			<span data-comment-id="<?php echo $tot_followers; ?>" class="pure-u meta-comment stats">
			<i class="users icon"></i>
				<span class="attribute"><?php echo $langscape["Followers"];?></span><span class="text"><?php echo $tot_followers; ?></span>
			</span>	
			<?php 
			}
			if ($rec_fav>0) {
			 ?>
			<span data-image-id="<?php echo $rec_fav; ?>" class="pure-u meta-hearts stats">
			<i class="heart icon"></i>
			<span class="attribute"><?php echo $langscape["Fan Favorites"];?></span><span class="text"><?php echo $rec_fav; ?></span>
			</span>
			<?php 
			}
			if ($rec_view>0) {
			 ?>
			<span data-view-id="<?php echo $rec_view; ?>" class="pure-u meta-views stats">
			<i class="eye icon"></i>
				<span class="attribute"><?php echo $langscape["Fan Views"];?></span><span class="text"><?php echo $rec_view; ?></span>
			</span>
			<?php 
			}
			if (($rec_com>0) && ($config->extensions->gallery->image_comments)) {
			 ?>
			<span data-comment-id="<?php echo $rec_com; ?>" class="pure-u meta-comment stats">
			<i class="comment icon"></i>
			<span class="attribute"><?php echo $langscape["Fan Comments"];?></span><span class="text"><?php echo $rec_com; ?></span>
			</span>	
			<?php 
			}
			 ?>
		</div>
		
	</div>
</aside>
