<?php
$paginator = new MK_Paginator();
$paginator
	->setPerPage(15)
	->setPage( MK_Request::getQuery('page', 1) );

$images = $image_favourite_module->searchRecords(array(
  array('field' => 'user', 'value' => $profile->getId())
), $paginator);

if( !empty($images) ) { ?>
  <ul class="awesome-gallery" data-autoload="true" <?php if ($config->site->grid->type == "MASONRYJS") { echo 'data-masonry="true"'; } ?>><?php if ($config->site->grid->type == "MASONRYJS") { ?><div class="grid-sizer"></div><div class="gutter-sizer"></div><?php } ?><!--<?php
    $counter = 0;
    foreach( $images as $image ) {
      $image = $image->objectImage();
      include 'includes/image-box.php';
    } ?>-->
  </ul>

  <div class="paginator clear-fix">
    <button class="pure-button pure-button-primary load-more ladda-button">
      <span class="ladda-label"><?php 
        echo $paginator->render('member.php?section='.$section.'&amp;user='.$profile_id.'&amp;page={page}'); ?>
      </span>
    </button>
  </div><?php
} else { ?>
  <p class="alert alert-information"><?php print $profile_name; ?> <?php echo $langscape["hasn't favorited any images"];?></p><?php
}	?>