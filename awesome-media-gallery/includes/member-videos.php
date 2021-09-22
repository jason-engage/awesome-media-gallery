<?php
$num_items = !empty($config->site->grid->items_per_page) ? $config->site->grid->items_per_page : 12;
$num_items = ($deviceType == 'phone' && $config->site->mobile->enable_responsive_phone) ? $config->site->mobile->items_per_page : $num_items;

$paginator = new MK_Paginator();
$paginator
  ->setPerPage($num_items)
  ->setPage( MK_Request::getQuery('page', 1) );


$field_1 = array('field' => 'user', 'value' => $profile->getId());
$field_2 = array('field' => 'type_gallery', 'value' => 2);


if ($config->site->media->enable_approval) {

	$field_3 = array('field' => 'approved', 'value' => 1);

} else {
	
	$field_3 = array();

}

$search_array = ($field_3)? array($field_1,$field_2,$field_3):array($field_1,$field_2);

$images = $image_module->searchRecords(
   	$search_array, 
    $paginator);  


	
if( !empty($images) ) { ?>
	
  <ul class="awesome-gallery" data-autoload="true" <?php if ($config->site->grid->type == "MASONRYJS") { echo 'data-masonry="true"'; } ?>><?php if ($config->site->grid->type == "MASONRYJS") { ?><div class="grid-sizer"></div><div class="gutter-sizer"></div><?php } ?><!--<?php
    $counter = 0;
    foreach( $images as $image ) {
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
  <p class="alert alert-information"><?php echo $profile_name; ?> <?php echo $langscape["hasn't shared any videos"];?></p><?php
} ?>
