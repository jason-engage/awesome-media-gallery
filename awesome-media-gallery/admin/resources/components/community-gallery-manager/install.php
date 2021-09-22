<?php

$config = MK_Config::getInstance();

// Get Module Types
$module_type = MK_RecordModuleManager::getFromType('module');
$module_field_type = MK_RecordModuleManager::getFromType('module_field');
$module_field_validation_type = MK_RecordModuleManager::getFromType('module_field_validation');
//$page_module = MK_RecordModuleManager::getFromType('page');

// Images
$image_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$image_module
	->setName('Media')
	->setTable('images')
	->setSlug('images')
	->setParentModule(0)
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('DESC')
	->setManagementWidth('20%')
	->setType('image')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();


$image_module_image_slug = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_image_slug
	->setOrder(2)
	->setModule( $image_module->getId() )
	->setName('image_slug')
	->setLabel('Image Slug')
	->setType('')
	->setEditable(1)
	->setDisplayWidth()
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_title = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_title
	->setOrder(3)
	->setModule( $image_module->getId() )
	->setName('title')
	->setLabel('Title')
	->setType('')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_title_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$image_module_title_validation
	->setName('instance')
	->setFieldId($image_module_title->getId())
	->save();

$image_module_date_added = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_date_added
	->setOrder(4)
	->setModule( $image_module->getId() )
	->setName('date_added')
	->setLabel('Date added')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_user
	->setOrder(5)
	->setModule( $image_module->getId() )
	->setName('user')
	->setLabel('Author')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$image_module_gallery = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_gallery
	->setOrder(6)
	->setModule( $image_module->getId() )
	->setName('gallery')
	->setLabel('Gallery')
	->setType('image_gallery')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();


$image_module_gallery_type = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_gallery_type
	->setOrder(7)
	->setModule( $image_module->getId() )
	->setName('type_gallery')
	->setLabel('Image Type')
	->setType('image_gallery_type')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
	
	
$image_module_tags = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_tags
	->setOrder(8)
	->setModule( $image_module->getId() )
	->setName('tags')
	->setLabel('Tags')
	->setType('tags')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Separate tags with a comma \',\'')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


$image_module_image = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_image
	->setOrder(9)
	->setModule( $image_module->getId() )
	->setName('image')
	->setLabel('Image')
	->setType('file_image_multiple_clone')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_video_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_video_url
	->setOrder(10)
	->setModule( $image_module->getId() )
	->setName('video_url')
	->setLabel('Video URL')
	->setType('')
	->setEditable(1)
	->setDisplayWidth()
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_audio_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_audio_url
	->setOrder(11)
	->setModule( $image_module->getId() )
	->setName('audio_url')
	->setLabel('Audio URL')
	->setType('')
	->setEditable(1)
	->setDisplayWidth()
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_link_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_link_url
	->setOrder(12)
	->setModule( $image_module->getId() )
	->setName('link_url')
	->setLabel('Link URL')
	->setType('')
	->setEditable(1)
	->setDisplayWidth()
	->setTooltip('Currently used for SLIDER images only. Clicking it will redirect to this link.')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_soundcloud_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_soundcloud_id
	->setOrder(13)
	->setModule( $image_module->getId() )
	->setName('soundcloud_id')
	->setLabel('Soundcloud ID')
	->setType('')
	->setEditable(1)
	->setDisplayWidth()
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
		
$image_module_description = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_description
	->setOrder(14)
	->setModule( $image_module->getId() )
	->setName('description')
	->setLabel('Description')
	->setType('textarea_large')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


$image_module_views = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_views
	->setOrder(15)
	->setModule( $image_module->getId() )
	->setName('views')
	->setLabel('Views')
	->setType('integer')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();
	

$image_module_total_favourites = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_total_favourites
	->setOrder(16)
	->setModule( $image_module->getId() )
	->setName('total_favourites')
	->setLabel('Total Favourites')
	->setType('integer')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$image_module_total_comments = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_total_comments
	->setOrder(17)
	->setModule( $image_module->getId() )
	->setName('total_comments')
	->setLabel('Total Comments')
	->setType('integer')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$image_module_crop_top = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_crop_top
	->setOrder(18)
	->setModule( $image_module->getId() )
	->setName('crop_top')
	->setLabel('Crop From Top')
	->setType('no_yes')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('If selected, cropped image thumbnails will not be centered')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
	
$image_module_approved = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_approved
	->setOrder(19)
	->setModule( $image_module->getId() )
	->setName('approved')
	->setLabel('Approved')
	->setType('no_yes')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Works only if enable approval is turned on')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
	
$image_module_featured = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_featured
	->setOrder(20)
	->setModule( $image_module->getId() )
	->setName('featured')
	->setLabel('Featured')
	->setType('no_yes')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_slider = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_slider
	->setOrder(21)
	->setModule( $image_module->getId() )
	->setName('slider')
	->setLabel('Slider')
	->setType('no_yes')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_carousel = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_carousel
	->setOrder(22)
	->setModule( $image_module->getId() )
	->setName('carousel')
	->setLabel('Carousel')
	->setType('no_yes')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_hide = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_hide
	->setOrder(23)
	->setModule( $image_module->getId() )
	->setName('hide')
	->setLabel('Hide')
	->setType('no_yes')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Still accessible but hidden from main gallery pages and search')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


$image_module_featured_date = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_featured_date
	->setOrder(24)
	->setModule( $image_module->getId() )
	->setName('featured_date')
	->setLabel('Featured Date')
	->setType('datetime')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Used to modify order of featured images')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_slider_date = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_slider_date
	->setOrder(25)
	->setModule( $image_module->getId() )
	->setName('slider_date')
	->setLabel('Slider Date')
	->setType('datetime')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Used to modify order of slider images')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_module_carousel_date = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_module_carousel_date
	->setOrder(26)
	->setModule( $image_module->getId() )
	->setName('carousel_date')
	->setLabel('Carousel Date')
	->setType('datetime')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Used to modify order of carousel images')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


	
// Images - Comments
$image_comment_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$image_comment_module
	->setName('Comments')
	->setTable('images_comments')
	->setSlug('comments')
	->setParentModule($image_module->getId())
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('ASC')
	->setManagementWidth('20%')
	->setType('image_comment')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$image_comment_module_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_comment_module_user
	->setOrder(2)
	->setModule( $image_comment_module->getId() )
	->setName('user')
	->setLabel('User')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('25%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_comment_module_reply_to = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_comment_module_reply_to
	->setOrder(3)
	->setModule( $image_comment_module->getId() )
	->setName('reply_to')
	->setLabel('In Reply To')
	->setType('image_comment')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_comment_module_date_added = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_comment_module_date_added
	->setOrder(4)
	->setModule( $image_comment_module->getId() )
	->setName('date_added')
	->setLabel('Date Added')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_comment_module_image = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_comment_module_image
	->setOrder(5)
	->setModule( $image_comment_module->getId() )
	->setName('image')
	->setLabel('Image')
	->setType('image')
	->setEditable(1)
	->setDisplayWidth('25%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


$image_comment_module_comment = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_comment_module_comment
	->setOrder(6)
	->setModule( $image_comment_module->getId() )
	->setName('comment')
	->setLabel('Comment')
	->setType('textarea_large')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


// Images - Comments - Likes
$image_comment_like_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$image_comment_like_module
	->setName('Likes')
	->setTable('images_comments_likes')
	->setSlug('likes')
	->setParentModule($image_comment_module->getId())
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('ASC')
	->setManagementWidth('20%')
	->setType('image_comment_like')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$image_comment_like_module_comment = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_comment_like_module_comment
	->setOrder(2)
	->setModule( $image_comment_like_module->getId() )
	->setName('comment')
	->setLabel('Comment')
	->setType('image_comment')
	->setEditable(1)
	->setDisplayWidth('25%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
	
$image_comment_like_module_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_comment_like_module_user
	->setOrder(3)
	->setModule( $image_comment_like_module->getId() )
	->setName('user')
	->setLabel('User')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('25%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_comment_like_module_date_added = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_comment_like_module_date_added
	->setOrder(4)
	->setModule( $image_comment_like_module->getId() )
	->setName('date_time')
	->setLabel('Date Added')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

// Images - Favourites
$image_favourite_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$image_favourite_module
	->setName('Favourites')
	->setTable('images_favourites')
	->setSlug('favourites')
	->setParentModule($image_module->getId())
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('ASC')
	->setManagementWidth('20%')
	->setType('image_favourite')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$image_favourite_module_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_favourite_module_user
	->setOrder(2)
	->setModule( $image_favourite_module->getId() )
	->setName('user')
	->setLabel('User')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('25%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_favourite_module_image = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_favourite_module_image
	->setOrder(3)
	->setModule( $image_favourite_module->getId() )
	->setName('image')
	->setLabel('Image')
	->setType('image')
	->setEditable(1)
	->setDisplayWidth('25%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_favourite_module_date_added = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_favourite_module_date_added
	->setOrder(4)
	->setModule( $image_favourite_module->getId() )
	->setName('date_added')
	->setLabel('Date Added')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


// Gallery - Types
$image_gallery_type_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$image_gallery_type_module
	->setName('Gallery Types')
	->setTable('images_galleries_types')
	->setSlug('gallerytypes')
	->setParentModule($image_module->getId())
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('ASC')
	->setManagementWidth('15%')
	->setType('image_gallery_type')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();


$image_gallery_type_module_title = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_gallery_type_module_title
	->setOrder(2)
	->setModule( $image_gallery_type_module->getId() )
	->setName('name')
	->setLabel('Name')
	->setType('text')
	->setEditable(1)
	->setDisplayWidth('70%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


// Images - Galleries
$image_gallery_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$image_gallery_module
	->setName('Galleries')
	->setTable('images_galleries')
	->setSlug('galleries')
	->setParentModule($image_module->getId())
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('ASC')
	->setManagementWidth('20%')
	->setType('image_gallery')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$image_gallery_module_name = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_gallery_module_name
	->setOrder(2)
	->setModule( $image_gallery_module->getId() )
	->setName('name')
	->setLabel('Name')
	->setType('')
	->setEditable(1)
	->setDisplayWidth('65%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_gallery_module_name_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$image_gallery_module_name_validation
	->setName('instance')
	->setFieldId($image_gallery_module_name->getId())
	->save();

$image_gallery_module_description = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_gallery_module_description
	->setOrder(3)
	->setModule( $image_gallery_module->getId() )
	->setName('description')
	->setLabel('Description')
	->setType('textarea_small')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_gallery_module_parent_gallery = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_gallery_module_parent_gallery
	->setOrder(4)
	->setModule( $image_gallery_module->getId() )
	->setName('parent_gallery')
	->setLabel('Parent Gallery')
	->setType('image_gallery')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$image_gallery_module_gallery_type = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$image_gallery_module_gallery_type
	->setOrder(5)
	->setModule( $image_gallery_module->getId() )
	->setName('type_gallery')
	->setLabel('Gallery Type')
	->setType('image_gallery_type')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


// Update based on inserts
$image_gallery_module
	->setFieldTitle($image_gallery_module_name->getId())
	->setFieldOrderBy($image_gallery_module_name->getId())
	->setFieldParent($image_gallery_module_parent_gallery->getId())
	->save();

$image_favourite_module
	->setFieldTitle($image_favourite_module_date_added->getId())
	->setFieldOrderBy($image_favourite_module_date_added->getId())
	->setFieldOrderByDirection('DESC')
	->save();

$image_comment_module
	->setFieldTitle($image_comment_module_comment->getId())
	->setFieldOrderBy($image_comment_module_date_added->getId())
	->setFieldOrderByDirection('DESC')
	->setFieldParent($image_comment_module_reply_to->getId())
	->save();

$image_module
	->setFieldTitle($image_module_title->getId())
	->setFieldOrderBy($image_module_date_added->getId())
	->setFieldOrderByDirection('DESC')
	->save();

$image_comment_like_module
	->setFieldTitle($image_comment_like_module_date_added->getId())
	->setFieldOrderBy($image_comment_like_module_date_added->getId())
	->setFieldOrderByDirection('DESC')
	->save();

$image_gallery_type_module
	->setFieldTitle($image_gallery_type_module_title->getId())
	->setFieldOrderBy($image_gallery_type_module_title->getId())
	->setFieldOrderByDirection('DESC')
	->save();

$image_gallery_type = MK_RecordManager::getNewRecord($image_gallery_type_module->getId());
$image_gallery_type
	->setName('Image')
	->save();

$video_gallery_type = MK_RecordManager::getNewRecord($image_gallery_type_module->getId());
$video_gallery_type
	->setName('Videos')
	->save();

$audio_gallery_type = MK_RecordManager::getNewRecord($image_gallery_type_module->getId());
$audio_gallery_type
	->setName('Audio')
	->save();
	
$_new_gallery = MK_RecordManager::getNewRecord($image_gallery_module->getId());
$_new_gallery
	->setName('Food')
	->setTypeGallery($image_gallery_type->getId())
	->save();

$_new_gallery = MK_RecordManager::getNewRecord($image_gallery_module->getId());
$_new_gallery
	->setName('Photography')
	->setTypeGallery($image_gallery_type->getId())
	->save();

$_new_gallery = MK_RecordManager::getNewRecord($image_gallery_module->getId());
$_new_gallery
	->setName('Sports')
	->setTypeGallery($video_gallery_type->getId())
	->save();

$_new_gallery = MK_RecordManager::getNewRecord($image_gallery_module->getId());
$_new_gallery
	->setName('Funny')
	->setTypeGallery($video_gallery_type->getId())
	->save();

$_new_gallery = MK_RecordManager::getNewRecord($image_gallery_module->getId());
$_new_gallery
	->setName('Music')
	->setTypeGallery($audio_gallery_type->getId())
	->save();

$_new_gallery = MK_RecordManager::getNewRecord($image_gallery_module->getId());
$_new_gallery
	->setName('Podcasts')
	->setTypeGallery($audio_gallery_type->getId())
	->save();
?>