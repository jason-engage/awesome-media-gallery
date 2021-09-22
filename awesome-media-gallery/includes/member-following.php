<!-- Followers grid-cs-cs Starts Here --><?php
$paginator = new MK_Paginator();
$paginator
    ->setPerPage(15)
    ->setPage( MK_Request::getQuery('page', 1) );

$users = $profile->getFollowing($paginator);

if( !empty ($users ) ) { ?>

    <ul data-autoload="true" class="members"><?php

        foreach( $users as $member ) {
            
            include ('includes/member-box.php');

        } ?>
        
    </ul>
        
    <div class="paginator clear-fix">
        <button class="pure-button pure-button-primary load-more ladda-button">
            <span class="ladda-label"><?php echo $paginator->render('member.php?section='.$section.'&amp;user='.$profile_id.'&amp;page={page}'); ?></span>
        </button>
    </div><?php
    
} else { ?>

    <p class="alert alert-information"><?php echo $profile_name; ?> <?php echo $langscape["isn't following anyone!"];?></p><?php

} ?>