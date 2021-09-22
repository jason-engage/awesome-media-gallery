<?php
$config = MK_Config::getInstance();

// Get Module Types
$module_type = MK_RecordModuleManager::getFromType('module');
$module_field_type = MK_RecordModuleManager::getFromType('module_field');
$module_field_validation_type = MK_RecordModuleManager::getFromType('module_field_validation');

// Users - Types
$user_type_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$user_type_module
	->setName('Categories')
	->setTable('users_types')
	->setSlug('types')
	->setParentModule( 0 )
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('ASC')
	->setManagementWidth('15%')
	->setType('user_type')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

	
$user_type_module_title = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_type_module_title
	->setOrder(2)
	->setModule( $user_type_module->getId() )
	->setName('title')
	->setLabel('Title')
	->setType('text')
	->setEditable(1)
	->setDisplayWidth('70%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


// Users - Groups
$user_group_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$user_group_module
	->setName('Groups')
	->setTable('users_groups')
	->setSlug('groups')
	->setParentModule( 0 )
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('ASC')
	->setManagementWidth('15%')
	->setType('user_group')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

	
$user_group_module_name = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_group_module_name
	->setOrder(2)
	->setModule( $user_group_module->getId() )
	->setName('name')
	->setLabel('Name')
	->setType('text')
	->setEditable(1)
	->setDisplayWidth('70%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_group_module_name_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$user_group_module_name_validation
	->setName('instance')
	->setFieldId($user_group_module_name->getId())
	->save();

$user_group_module_admin = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_group_module_admin
	->setOrder(3)
	->setModule( $user_group_module->getId() )
	->setName('admin')
	->setLabel('Users are Admins?')
	->setType('yes_no')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_group_module_default_value = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_group_module_default_value
	->setOrder(4)
	->setModule( $user_group_module->getId() )
	->setName('default_value')
	->setLabel('Default Group')
	->setType('yes_no')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_group_module_access_level = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_group_module_access_level
	->setOrder(5)
	->setModule( $user_group_module->getId() )
	->setName('access_level')
	->setLabel('Access Level')
	->setType('integer')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

// Users
$user_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$user_module
	->setName('Users')
	->setTable('users')
	->setSlug('users')
	->setParentModule(0)
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('DESC')
	->setManagementWidth('20%')
	->setType('user')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$user_module_type = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_type
	->setOrder(0)
	->setModule( $user_module->getId() )
	->setName('type')
	->setLabel('Type')
	->setType('user_type')
	->setEditable(0)
	->setDisplayWidth('2%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();
	
$user_module_email = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_email
	->setOrder(2)
	->setModule( $user_module->getId() )
	->setName('email')
	->setLabel('Email')
	->setType('text')
	->setEditable(1)
	->setDisplayWidth('30%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$user_module_email_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$user_module_email_validation
	->setName('email')
	->setFieldId($user_module_email->getId())
	->save();

$user_module_email_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$user_module_email_validation
	->setName('unique')
	->setFieldId($user_module_email->getId())
	->save();

$user_module_email_public = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_email_public
	->setOrder(3)
	->setModule( $user_module->getId() )
	->setName('email_public')
	->setLabel('Email Public')
	->setType('no_yes')
	->setEditable(0)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
	
	
$user_module_email_verified = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_email_verified
	->setOrder(43)
	->setModule( $user_module->getId() )
	->setName('email_verified')
	->setLabel('Email Verified')
	->setType('yes_no')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Only works if email verification module is activated')
	->setFieldset('')
	->setSpecificSearch(false)
    ->setDefaultValue(0)
	->save();

	
$user_module_approved = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_approved
	->setOrder(5)
	->setModule( $user_module->getId() )
	->setName('approved')
	->setLabel('Approved')
	->setType('yes_no')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Only works if member approval is enabled')
	->setFieldset('')
	->setSpecificSearch(0)
    ->setDefaultValue(0)
	->save();

$user_module_display_name = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_display_name
	->setOrder(6)
	->setModule( $user_module->getId() )
	->setName('display_name')
	->setLabel('Display Name')
	->setType('text')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$user_module_display_name_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$user_module_display_name_validation
	->setName('instance')
	->setFieldId($user_module_display_name->getId())
	->save();
	
$user_module_username = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_username
	->setOrder(7)
	->setModule( $user_module->getId() )
	->setName('username')
	->setLabel('Username')
	->setType('')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();	
	
$user_module_password = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_password
	->setOrder(8)
	->setModule( $user_module->getId() )
	->setName('password')
	->setLabel('Password')
	->setType('password')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


$user_module_temporary_password = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_temporary_password
	->setOrder(9)
	->setModule( $user_module->getId() )
	->setName('temporary_password')
	->setLabel('Temporary Password')
	->setType('hidden')
	->setEditable(0)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_group = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_group
	->setOrder(10)
	->setModule( $user_module->getId() )
	->setName('group')
	->setLabel('User Group')
	->setType('user_group')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_category = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_category
	->setOrder(11)
	->setModule( $user_module->getId() )
	->setName('category')
	->setLabel('Category')
	->setType('user_type')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


$user_module_date_registered = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_date_registered
	->setOrder(12)
	->setModule( $user_module->getId() )
	->setName('date_registered')
	->setLabel('Date Registered')
	->setType('datetime_static')
	->setEditable(1)
	->setDisplayWidth('25%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

	
$user_module_last_login = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_last_login
	->setOrder(13)
	->setModule( $user_module->getId() )
	->setName('last_login')
	->setLabel('Last Login')
	->setType('datetime_static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_lastip = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_lastip
	->setOrder(14)
	->setModule( $user_module->getId() )
	->setName('lastip')
	->setLabel('Last IP Used')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_about = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_about
	->setOrder(15)
	->setModule( $user_module->getId() )
	->setName('about')
	->setLabel('About')
	->setType('textarea_small')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_gender = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_gender
	->setOrder(16)
	->setModule( $user_module->getId() )
	->setName('gender')
	->setLabel('Gender')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_date_of_birth = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_date_of_birth
	->setOrder(17)
	->setModule( $user_module->getId() )
	->setName('date_of_birth')
	->setLabel('Date of Birth')
	->setType('date')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


$user_module_avatar = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_avatar
	->setOrder(18)
	->setModule( $user_module->getId() )
	->setName('avatar')
	->setLabel('Profile Image')
	->setType('file_image')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_cover_photo = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_cover_photo
	->setOrder(19)
	->setModule( $user_module->getId() )
	->setName('cover_photo')
	->setLabel('Cover Photo')
	->setType('file_image')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_media_count = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_media_count
	->setOrder(20)
	->setModule( $user_module->getId() )
	->setName('media_count')
	->setLabel('Media Count')
	->setType('integer')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_wp_author_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_wp_author_id
	->setOrder(21)
	->setModule( $user_module->getId() )
	->setName('wp_author_id')
	->setLabel('WP Author Id')
	->setType('integer')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('ID Number of Wordpress User - See your WP Admin')
	->setFieldset('')
	->setSpecificSearch(0)
    ->setDefaultValue(0)
	->save();

$user_module_website = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_website
	->setOrder(22)
	->setModule( $user_module->getId() )
	->setName('website')
	->setLabel('Website URL')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

/*
$user_module_website_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$user_module_website_validation
	->setName('url')
	->setFieldId($user_module_website->getId())
	->save();
*/

$user_module_facebook_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_facebook_url
	->setOrder(23)
	->setModule( $user_module->getId() )
	->setName('facebook_url')
	->setLabel('Facebook URL')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

/*
$user_module_facebook_url_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$user_module_facebook_url_validation
	->setName('url')
	->setFieldId($user_module_facebook_url->getId())
	->save();
*/

$user_module_twitter_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_twitter_url
	->setOrder(24)
	->setModule( $user_module->getId() )
	->setName('twitter_url')
	->setLabel('Twitter URL')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

/*
$user_module_twitter_url_validation = MK_RecordManager::getNewRecord( $module_field_validation_type->getId() );
$user_module_twitter_url_validation
	->setName('url')
	->setFieldId($user_module_twitter_url->getId())
	->save();
*/

$user_module_kickstarter_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_kickstarter_url
	->setOrder(25)
	->setModule( $user_module->getId() )
	->setName('kickstarter_url')
	->setLabel('Kickstarter Url')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_demo_reel_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_demo_reel_url
	->setOrder(26)
	->setModule( $user_module->getId() )
	->setName('demo_reel_url')
	->setLabel('Demo Reel Url')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Setup for a link to a personal video')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_linkedin_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_linkedin_url
	->setOrder(27)
	->setModule( $user_module->getId() )
	->setName('linkedin_url')
	->setLabel('Linkedin Url')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_google_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_google_url
	->setOrder(28)
	->setModule( $user_module->getId() )
	->setName('google_url')
	->setLabel('Google Url')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_resume_url = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_resume_url
	->setOrder(29)
	->setModule( $user_module->getId() )
	->setName('resume_url')
	->setLabel('Resume Url')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('Link to resume/portfolio')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_other_urls = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_other_urls
	->setOrder(30)
	->setModule( $user_module->getId() )
	->setName('other_urls')
	->setLabel('Other Urls')
	->setType('textarea_small')
	->setEditable(0)
	->setDisplayWidth('')
	->setTooltip('Not setup / Ignore')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
	
$user_module_occupation = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_occupation
	->setOrder(31)
	->setModule( $user_module->getId() )
	->setName('occupation')
	->setLabel('Occupation')
	->setType('text')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_interests = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_interests
	->setOrder(32)
	->setModule( $user_module->getId() )
	->setName('interests')
	->setLabel('interests')
	->setType('text')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
	
$user_module_skills = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_skills
	->setOrder(33)
	->setModule( $user_module->getId() )
	->setName('skills')
	->setLabel('Skills')
	->setType('textarea_small')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_software = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_software
	->setOrder(34)
	->setModule( $user_module->getId() )
	->setName('software')
	->setLabel('Software')
	->setType('textarea_small')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_module_years_of_experience = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_years_of_experience
	->setOrder(35)
	->setModule( $user_module->getId() )
	->setName('years_of_experience')
	->setLabel('Years of Experience')
	->setType('integer')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
    ->setDefaultValue(0)
	->save();

$user_module_facebook_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_facebook_id
	->setOrder(36)
	->setModule( $user_module->getId() )
	->setName('facebook_id')
	->setLabel('Facebook ID')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('If the user has linked their account with Facebook or uses Facebook to login, this is their Facebook account ID.')
	->setFieldset('social')
	->setSpecificSearch(0)
	->save();

$user_module_twitter_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_twitter_id
	->setOrder(37)
	->setModule( $user_module->getId() )
	->setName('twitter_id')
	->setLabel('Twitter ID')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('If the user has linked their account with Twitter or uses Twitter to login, this is their Twitter account ID.')
	->setFieldset('social')
	->setSpecificSearch(0)
	->save();

$user_module_linkedin_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_linkedin_id
	->setOrder(38)
	->setModule( $user_module->getId() )
	->setName('linkedin_id')
	->setLabel('LinkedIn ID')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('If the user has linked their account with LinkedIn or uses LinkedIn to login, this is their LinkedIn account ID.')
	->setFieldset('social')
	->setSpecificSearch(0)
	->save();

$user_module_yahoo_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_yahoo_id
	->setOrder(39)
	->setModule( $user_module->getId() )
	->setName('yahoo_id')
	->setLabel('Yahoo ID')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('If the user has linked their account with Yahoo or uses Yahoo to login, this is their Yahoo account ID.')
	->setFieldset('social')
	->setSpecificSearch(0)
	->save();

$user_module_windowslive_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_windowslive_id
	->setOrder(40)
	->setModule( $user_module->getId() )
	->setName('windows_live_id')
	->setLabel('Windows Live ID')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('If the user has linked their account with Windows Live or uses Windows Live to login, this is their Windows Live account ID.')
	->setFieldset('social')
	->setSpecificSearch(0)
	->save();

$user_module_google_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_google_id
	->setOrder(41)
	->setModule( $user_module->getId() )
	->setName('google_id')
	->setLabel('Google ID')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('If the user has linked their account with Google or uses Google to login, this is their Google account ID.')
	->setFieldset('social')
	->setSpecificSearch(0)
	->save();

$user_module_wordpress_id = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_module_wordpress_id
	->setOrder(42)
	->setModule( $user_module->getId() )
	->setName('wordpress_id')
	->setLabel('Wordpress ID')
	->setType('static')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('If the user has linked their account with Wordpress or uses Wordpress to login, this is their WP account ID.')
	->setFieldset('social')
	->setSpecificSearch(0)
	->save();



// Users - Followers
$user_followers_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$user_followers_module
	->setName('Followers')
	->setTable('users_followers')
	->setSlug('followers')
	->setParentModule( $user_module->getId() )
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('DESC')
	->setManagementWidth('20%')
	->setType('user_follower')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$user_followers_module_follower = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_followers_module_follower
	->setOrder(2)
	->setModule( $user_followers_module->getId() )
	->setName('follower')
	->setLabel('Follower')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('30%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$user_followers_module_following = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_followers_module_following
	->setOrder(4)
	->setModule( $user_followers_module->getId() )
	->setName('following')
	->setLabel('Following')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('30%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$user_followers_module_date_time = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_followers_module_date_time
	->setOrder(6)
	->setModule( $user_followers_module->getId() )
	->setName('date_time')
	->setLabel('Date & Time')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


// Users - Friendships
$user_friendships_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$user_friendships_module
	->setName('Friendships')
	->setTable('users_friendships')
	->setSlug('friendships')
	->setParentModule( $user_module->getId() )
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('DESC')
	->setManagementWidth('20%')
	->setType('user_friendship')
	->setHidden(1)
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$user_friendships_module_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_friendships_module_user
	->setOrder(2)
	->setModule( $user_friendships_module->getId() )
	->setName('user')
	->setLabel('User')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('30%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_friendships_module_friend = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_friendships_module_friend
	->setOrder(2)
	->setModule( $user_friendships_module->getId() )
	->setName('friend')
	->setLabel('Friend')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('30%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_friendships_module_accepted = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_friendships_module_accepted
	->setOrder(3)
	->setModule( $user_friendships_module->getId() )
	->setName('accepted')
	->setLabel('Accepted')
	->setType('no_yes')
	->setEditable(1)
	->setDisplayWidth('15%')
	->setTooltip('Has the friendship request been accepted?')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_friendships_module_date_time = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_friendships_module_date_time
	->setOrder(6)
	->setModule( $user_friendships_module->getId() )
	->setName('date_time')
	->setLabel('Date & Time')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


// Users - Meta
$user_meta_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$user_meta_module
	->setName('Meta')
	->setTable('users_meta')
	->setSlug('meta')
	->setParentModule( $user_module->getId() )
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('DESC')
	->setManagementWidth('20%')
	->setType('user_meta')
	->setHidden(1)
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$user_meta_module_key = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_meta_module_key
	->setOrder(2)
	->setModule( $user_meta_module->getId() )
	->setName('key')
	->setLabel('Key')
	->setType('text')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_meta_module_value = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_meta_module_value
	->setOrder(3)
	->setModule( $user_meta_module->getId() )
	->setName('value')
	->setLabel('Value')
	->setType('textarea_small')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_meta_module_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_meta_module_user
	->setOrder(4)
	->setModule( $user_meta_module->getId() )
	->setName('user')
	->setLabel('User')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('30%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

// Notifications
$user_notification_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$user_notification_module
	->setName('Notifications')
	->setTable('users_notifications')
	->setSlug('notifications')
	->setParentModule( $user_module->getId() )
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('DESC')
	->setManagementWidth('20%')
	->setType('user_notification')
	->setHidden(0)
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$user_notification_module_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_notification_module_user
	->setOrder(2)
	->setModule( $user_notification_module->getId() )
	->setName('user')
	->setLabel('User')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$user_notification_module_related_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_notification_module_related_user
	->setOrder(4)
	->setModule( $user_notification_module->getId() )
	->setName('related_user')
	->setLabel('Related User')
	->setType('user')
	->setEditable(1)
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_notification_module_public = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_notification_module_public
	->setOrder(6)
	->setModule( $user_notification_module->getId() )
	->setName('public')
	->setLabel('Public')
	->setType('yes_no')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_notification_module_unread = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_notification_module_unread
	->setOrder(8)
	->setModule( $user_notification_module->getId() )
	->setName('unread')
	->setLabel('Unread')
	->setType('no_yes')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_notification_module_date_time = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_notification_module_date_time
	->setOrder(10)
	->setModule( $user_notification_module->getId() )
	->setName('date_time')
	->setLabel('Date & Time')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('40%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$user_notification_module_text = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$user_notification_module_text
	->setOrder(12)
	->setModule( $user_notification_module->getId() )
	->setName('text')
	->setLabel('Text')
	->setType('textarea_small')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

// Backups
$backup_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$backup_module
	->setName('Backups')
	->setTable('backups')
	->setSlug('backups')
	->setParentModule(0)
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('DESC')
	->setManagementWidth('20%')
	->setType('backup')
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$backup_module_date_time = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$backup_module_date_time
	->setOrder(2)
	->setModule( $backup_module->getId() )
	->setName('date_time')
	->setLabel('Date & Time')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('40%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

$backup_module_date_time = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$backup_module_date_time
	->setOrder(3)
	->setModule( $backup_module->getId() )
	->setName('file')
	->setLabel('File')
	->setType('file')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();

// Action Log
$action_log_module = MK_RecordManager::getNewRecord( $module_type->getId() );
$action_log_module
	->setName('Action Log')
	->setTable('action_log')
	->setSlug('action-log')
	->setParentModule(0)
	->setFieldId(0)
	->setFieldTitle(0)
	->setFieldParent(0)
	->setFieldOrderBy(0)
	->setOrderByDirection('DESC')
	->setManagementWidth('20%')
	->setType('action_log')
	->setHidden(1)
	->setLocked(0)
	->setLockRecords(0)
	->setCoreModule(0)
	->save();

$action_log_module_user = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$action_log_module_user
	->setOrder(2)
	->setModule( $action_log_module->getId() )
	->setName('user')
	->setLabel('User')
	->setType('user')
	->setEditable(1)
	->setDisplayWidth('20%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(1)
	->save();

$action_log_module_action = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$action_log_module_action
	->setOrder(3)
	->setModule( $action_log_module->getId() )
	->setName('action')
	->setLabel('Action')
	->setType('textarea_small')
	->setEditable(1)
	->setDisplayWidth('')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();
	
$action_log_module_date_time = MK_RecordManager::getNewRecord( $module_field_type->getId() );
$action_log_module_date_time
	->setOrder(4)
	->setModule( $action_log_module->getId() )
	->setName('date_time')
	->setLabel('Date & Time')
	->setType('datetime_now')
	->setEditable(1)
	->setDisplayWidth('40%')
	->setTooltip('')
	->setFieldset('')
	->setSpecificSearch(0)
	->save();


// Update based on inserts
$action_log_module
	->setFieldTitle( $action_log_module_date_time->getId() )
	->setFieldOrderBy( $action_log_module_date_time->getId() )
	->save();

$user_group_module
	->setFieldTitle( $user_group_module_name->getId() )
	->setFieldOrderBy( $user_group_module_name->getId() )
	->setParentModule( $user_module->getId() )
	->save();

$user_meta_module
	->setFieldTitle( $user_meta_module_key->getId() )
	->setFieldOrderBy( $user_meta_module_key->getId() )
	->save();

$user_friendships_module
	->setFieldTitle( $user_friendships_module_user->getId() )
	->setFieldOrderBy( $user_friendships_module_user->getId() )
	->save();

$user_followers_module
	->setFieldTitle( $user_followers_module_date_time->getId() )
	->setFieldOrderBy( $user_followers_module_date_time->getId() )
	->save();

$user_type_module
	->setFieldTitle( $user_type_module_title->getId() )
	->setParentModule( $user_module->getId() )
	->setFieldOrderBy( $user_type_module_title->getId() )
	->save();

$user_module
	->setFieldTitle( $user_module_display_name->getId() )
	->setFieldOrderBy( $user_module_date_registered->getId() )
	->save();

$user_notification_module
	->setFieldTitle( $user_notification_module_text->getId() )
	->setFieldOrderBy( $user_notification_module_date_time->getId() )
	->save();

$backup_module
	->setFieldTitle( $backup_module_date_time->getId() )
	->setFieldOrderBy( $backup_module_date_time->getId() )
	->save();

// Insert new records
$new_user_group = MK_RecordManager::getNewRecord( $user_group_module->getId() );
$new_user_group
	->setName('Administrators')
	->setAdmin(1)
	->setDefaultValue(0)
	->setAccessLevel(3)
	->save();

$new_user_group = MK_RecordManager::getNewRecord( $user_group_module->getId() );
$new_user_group
	->setName('Members')
	->setAdmin(0)
	->setDefaultValue(1)
	->setAccessLevel(1)
	->save();

$_new_user_type = MK_RecordManager::getNewRecord($user_type_module->getId());
$_new_user_type
	->setTitle('General')
	->save();    
      
?>