<?php

$profile_flag = 0;

if( $id = $user_id ) {

	$id = explode( '-' , $id );
	$id = array_shift( $id );

	$profile = $user_record; //Already generated in BREADCRUMB

	$profile_name   = $profile->getDisplayName();
	$profile_image  = ( $profile->getAvatar() ? $profile->getAvatar() : $config->site->default_avatar );
	$profile_email  = $profile->getEmail();
	$profile_id     = $profile->getId();
}

/*EDITING*/
$admin_mode   = 0;

if( ($user->isAuthorized() && ($user->getId() == $profile->getId())) || ($user->objectGroup()->isAdmin()) ) { //User is logged in, viewing own profile or is an admin.

    $edit_class                = "edit";
    $edit_type_text            = "edit-text";
    $edit_type_link            = "edit-link";
    $edit_type_textarea        = "edit-textarea";
    $edit_type_category        = "edit-category";
    $edit_type_gender          = "edit-gender";
    $edit_type_date            = "edit-date";
    $edit_type_yesno           = "edit-yesno";
    $edit_type_yesno_freelance = "edit-yesno-freelance";
    $edit_type_tags            = "edit-tags";
    $admin_mode                = 1;

} else {

    $edit_class = "";
    $edit_type_text = "";
    $edit_type_link = "";
    $edit_type_textarea = "";
    $edit_type_category = "";
    $edit_type_gender = "";
    $edit_type_date = "";
    $edit_type_yesno = "";
    $edit_type_yesno_freelance = "";
    $edit_type_tags = "";
    $admin_mode = 0;

}


//GET COUNTS
$tot_images = number_format($profile->getTotalUserImages(1));
$tot_videos = number_format($profile->getTotalUserImages(2));
$tot_audios = number_format($profile->getTotalUserImages(3));
$tot_followers = number_format($profile->getTotalFollowers());
$tot_following = number_format($profile->getTotalFollowing());
$tot_likes = number_format($profile->getTotalFavourites());
?>

<?php

	if ( ( ($deviceType <> 'phone') || !$config->site->mobile->enable_responsive_phone) && ($config->site->ads->enable_member_top) ) { ?>

	<!-- 728x90 Ad Banner -->
	<?php include ('includes/ad-top.php'); ?>

<?php
	}
?>

<!-- Content Section PROFILE Starts Here -->
<section class="content profile pure-u-3-4">

    <!-- User  Profile Starts Here --><?php
        include ("member-profile.php"); ?>
    <!-- User Profile Ends Here -->

    <div class="user-profile-nav">

        <a href="<?php echo getProfileUrl( $profile_id ); ?>"><button class="pure-button pure-button-primary <?php if ( $section == 'about' || empty( $section ) ) echo 'pure-button-active'; ?>"><span><?php echo $langscape["Profile"];?></span></button></a>

		<?php if ($config->site->media->enable_images && ($tot_images > 0) ) { ?>
        <a href="<?php echo getProfileUrl( $profile_id, 'images' ); ?>"><button class="pure-button pure-button-primary <?php if ( $section == 'images' ) echo 'pure-button-active'; ?>"><span><?php echo $langscape["Images"];?></span></button></a>
		<?php } ?>

		<?php if ($config->site->media->enable_videos && ($tot_videos > 0) ) { ?>
        <a href="<?php echo getProfileUrl( $profile_id, 'videos' ); ?>"><button class="pure-button pure-button-primary <?php if ( $section == 'videos' ) echo 'pure-button-active'; ?>"><span><?php echo $langscape["Videos"];?></span></button></a>
		<?php } ?>

		<?php if ($config->site->media->enable_audio && ($tot_audios > 0) ) { ?>
        <a href="<?php echo getProfileUrl( $profile_id, 'audios' ); ?>"><button class="pure-button pure-button-primary <?php if ( $section == 'audios' ) echo 'pure-button-active'; ?>"><span><?php echo $langscape["Audios"];?></span></button></a>
		<?php } ?>

		<?php if ( $tot_followers > 0 ) { ?>
        <a href="<?php echo getProfileUrl( $profile_id, 'followers' ); ?>"><button class="pure-button pure-button-primary <?php if ( $section == 'followers' ) echo 'pure-button-active'; ?>"><span><?php echo $langscape["Followers"];?></span></button></a>
		<?php } ?>

		<?php if ( $tot_following > 0 ) { ?>
        <a href="<?php echo getProfileUrl( $profile_id, 'following' ); ?>"><button class="pure-button pure-button-primary <?php if ( $section == 'following' ) echo 'pure-button-active'; ?>"><span><?php echo $langscape["Following"];?></span></button></a>
		<?php } ?>

		<?php if ( $tot_likes > 0 ) { ?>
        <a href="<?php echo getProfileUrl( $profile_id, 'likes' ); ?>"><button class="pure-button pure-button-primary <?php if ( $section == 'likes' ) echo 'pure-button-active'; ?>"><span><?php echo $langscape["Favorites"];?></span></button></a>
		<?php } ?>

        <a href="<?php echo getProfileUrl( $profile_id, 'activity' ); ?>"><button class="pure-button pure-button-primary <?php if ( $section == 'activity' ) echo 'pure-button-active'; ?>"><span><?php echo $langscape["Activity"];?></span></button></a>

    </div><?php

    switch ($section) {
        case 'about':
            include('includes/member-about.php');
            break;
        case 'following':
            include('includes/member-following.php');
            break;
        case 'followers':
            include('includes/member-followers.php');
            break;
        case 'activity':
            include('includes/member-activity.php');
            break;
        case 'images':
            include('includes/member-images.php');
            break;
        case 'videos':
            include('includes/member-videos.php');
            break;
        case 'audio':
            include('includes/member-audio.php');
            break;
        case 'likes':
            include('includes/member-likes.php');
            break;
        default:
            include('includes/member-about.php');
    } ?>

</section>

<!-- Sidebar Starts Here --><?php

if (  $disable_responsive ) {

    include ('includes/ad-sidebar.php');

} ?>

<!-- Sidebar Ends Here -->
