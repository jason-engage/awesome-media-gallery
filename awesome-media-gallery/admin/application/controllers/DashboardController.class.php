<?php

require_once 'DefaultController.class.php';

class MK_DashboardController extends MK_DefaultController
{

	public function _init()
	{
		parent::_init();
		$this->getView()->getHead()->prependTitle( 'Dashboard' );
	}
	
	public function sectionActionLog()
	{
		if( MK_Request::getParam('method') == 'delete' )
		{
			$action_log_module = MK_RecordModuleManager::getFromType('action_log');
			$action_log_module->flush();
		}
		$this->getView()->redirect(array('controller' => 'dashboard', 'section' => 'index'));
	}
	
	public function sectionInstalledComponents()
	{
		$config = MK_Config::getInstance();

		$components = MK_ComponentManager::getComponents( MK_ComponentManager::TYPE_OPTIONAL );
		$installed_components = $config->db->components;

		$form_structure = array(
			'components' => array(
				'label' => 'Components',
				'type' => 'checkbox-multiple',
				'options' => $components
			),
			'submit' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => 'Install Selected Modules'
				)
			)
		);

		$form_settings = array(
			'attributes' => array(
				'class' => 'standard clear-fix large'
			)
		);
		
		$form = new MK_Form($form_structure, $form_settings);

		$this->getView()->form = $form->render();

		if( $form->isSuccessful() )
		{
			$this->getView()->setDisplayPath('dashboard/installed-components-processed');		

			$fields = $form->getFields();
			
			$selected_components = $form->getField('components')->getValue();
			
			$messages = array();
			
			try
			{
				foreach( $selected_components as $selected_component )
				{
					$details = MK_ComponentManager::installComponent( $selected_component );
					$messages[] = new MK_Message('success', "The <strong>".$details['name']."</strong> component was installed successfully.");
				}
				$messages[] = new MK_Message('success', "The selected components were installed successfully. <a href=\"".$this->getView()->uri()."\">Return to the component list</a>.");
			}
			catch(Exception $e)
			{
				$messages[] = new MK_Message('success', "The following error occured: ".$e->getMessage());
			}
			
			$this->getView()->messages = $messages;
		}
	}

	public function sectionIndex()
	{
		
		$config = MK_Config::getInstance();

		$message_list = array();

		// Check: If running in demo mode
		if( $config->core->mode == MK_Core::MODE_DEMO )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "<strong>".$config->instance->name."</strong> is currently running in demonstration mode. Editing and deleting records is disabled. You can create records, however."
			);
		}
		
		// Check: json_encode function
		if( !function_exists('json_encode') )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "Cannot find the function 'json_encode' - This is used for some extended JavaScript functionality."
			);
		}
		
		// Check: cURL installed
		if( !function_exists('curl_init') )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "cURL library not installed - This is used for downloading resources located remotely."
			);
		}
		
		// Check: ZipArchive class
		if( !class_exists('ZipArchive') )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "Cannot find the 'ZipArchive' library - This is used for performing a backup."
			);
		}
		
		// Check: Can use ini_set
		if( !ini_set('memory_limit', '20M') )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "Cannot use 'ini_set'."
			);
		}
		else
		{
			ini_restore('memory_limit');
		}
		
		// Check: Uploads directory is writable
		if( !is_writable('../'.$config->site->upload_path) )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "Uploads folder is not writable '../".$config->site->upload_path."' Please chmod to 0777."
			);
		}
		
		// Check: Thumbs directory is writable
		if( !is_writable('../tpl/img/thumbs') )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "Thumbs folder is not writable '../tpl/img/thumbs/' Please chmod to 0777."
			);
		}
		
		// Check: Backups directory is writable
		if( !is_writable('resources/backups') )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "Backups folder is not writable 'resources/backups/' Please chmod to 0777."
			);
		}
		
		// Check: Restore directory is writable
		if( !is_writable('resources/restore') )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "Restore folder is not writable 'resources/restore/' Please chmod to 0777."
			);
		}
		
		// Check: Can use set_time_limit
		if( !set_time_limit(0) )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "Cannot use 'set_time_limit'."
			);
		}
		else
		{
			ini_restore('time_limit');
		}
		
		// Check: Get recent log entries
		$action_log_paginator = new MK_Paginator();
		$action_log_paginator
			->setPage( MK_Request::getParam('page', 1) )
			->setPerPage(20);

		$action_log_module = MK_RecordModuleManager::getFromType('action_log');
		$action_log_records = $action_log_module->getRecords($action_log_paginator);
		
		$paging_url = $this->getView()->uri( array('page' => '{page}') , false );
		$this->getView()->action_log_paginator = $action_log_paginator->render($paging_url);
		
		$action_log = array();
		
		foreach( $action_log_records as $action_log_record )
		{
			$user_link = $this->getView()->uri( array('controller' => 'users', 'section' => 'index', 'method' => 'edit', 'id' => $action_log_record->getUser()) );
			$action_log[] = new MK_Message('information', $action_log_record->getAction().($action_log_record->getUser() ? '<small> - '.$action_log_record->renderDateTime().'</small>' : ''));
		}
		
		$this->getView()->action_log = $action_log;

		// Check: Backup no more than 30 days ago
		$backup_type = MK_RecordModuleManager::getFromType('backup');
		$backup_search = array(
			array('literal' => "`date_time` > DATE_SUB(NOW(), INTERVAL 30 DAY)")
		);

		$backup_records = $backup_type->searchRecords($backup_search);
		if( count($backup_records) == 0 )
		{
			$message_list[] = array(
				'type' => 'warning',
				'message' => "You have not <a href=\"".$this->getView()->uri(array('controller' => 'dashboard', 'section' => 'backup'))."\">backed up</a> within the last 30 days."
			);
		}
		
		if( count($message_list) === 0 )
		{
			$message_list[] = array(
				'type' => 'information',
				'message' => "There are currently no system notifications."
			);
		}
		
		$this->getView()->message_list = $message_list;
		
	}

	public function sectionEmailUsers()
	{
		$config = MK_Config::getInstance();

		$this->getView()->getHead()->prependTitle( 'Email Users' );
		
		if( $user_group = MK_Request::getQuery('group') )
		{
			
		}
		else
		{
			$user_module = MK_RecordModuleManager::getFromType('user');
			$user_group_module = MK_RecordModuleManager::getFromType('user_group');
			
			$_user_groups = $user_group_module->getRecords();
			$user_groups = array();
			
			foreach($_user_groups as $key => $group)
			{
				$total_users = (integer) $user_module->countRecords(array(
					array('field' => 'group', 'value' => $group->getId())
				));

				if( $total_users > 0 )
				{
					$group->setTotalUsers($total_users);
					$user_groups[$key] = $group;
				}
			}
			
			$this->getView()->groups = $user_groups;
		}

	}

	public function sectionFileManager()
	{
		$config = MK_Config::getInstance();

		$this->getView()->getHead()->prependTitle( 'File Manager' );

		$messages = array();
		$files = array();

		$files_to_delete = array();

		if( $get_file = MK_Request::getQuery('file-select') )
		{
			$files_to_delete[] = $get_file;
		}
		elseif( $post_files = MK_Request::getPost('file-select') )
		{
			$files_to_delete = $post_files;
		}

		if( count($files_to_delete) > 0 )
		{
			foreach($files_to_delete as $file)
			{
				$file = '../'.$config->site->upload_path.$file;
				$current_file = new MK_File($file);
				$current_file->delete();
			}
			$messages[] = new MK_Message('success', "The selected files were successfully deleted. <a href=\"".$this->getView()->uri()."\">Return to file list</a>.");
		}
		else
		{
		
			if( !is_readable('../'.$config->site->upload_path) )
			{
				$messages[] = new MK_Message('error', "The folder '../".$config->site->upload_path."' cannot be read. Please chmod this folder to 0777.");
			}
			else
			{
				$paginator = new MK_Paginator();
				$paginator
					->setPage( MK_Request::getParam('page', 1) )
					->setPerPage( 10 );
	
				$file_list = scandir('../'.$config->site->upload_path);
				
				foreach($file_list as $file)
				{
					if( $file === 'index.php' )
					{
						continue;
					}
					$file = '../'.$config->site->upload_path.$file;
					if($file != '.' && $file != '..' && !is_dir($file))
					{
						$files[] = new MK_File($file);
					}
				}
	
				$paginator->setTotalRecords( count($files) );
				$files = array_splice($files, $paginator->getRecordStart(), $paginator->getPerPage());
				$this->getView()->paginator = $paginator->render( $this->getView()->uri(array('page' => '{page}')) );
			}
	
			if( count($files) === 0 )
			{
				$messages[] = new MK_Message('information', "There are no files to display.");
			}
	
			$this->getView()->files = $files;
		}

		$this->getView()->messages = $messages;
	}

	public function sectionBackup(){

		$config = MK_Config::getInstance();

		$this->getView()->getHead()->prependTitle( 'Backup' );

		$form_settings = array(
			'attributes' => array(
				'class' => 'standard clear-fix large'
			)
		);

		$form_structure = array(
			'backup_confirm' => array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Start backup?',
				'validation' => array(
					'boolean_true' => array()
				)
			),
			'backup_submit' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => 'Confirm'
				)
			)
		);

		$form = new MK_Form($form_structure, $form_settings);
		
		$this->getView()->form = $form->render();

		if($form->isSuccessful())
		{
			$messages = array();
			try
			{
				if(!class_exists('ZipArchive'))
				{
					throw new MK_Exception("Cannot find the 'ZipArchive' library - This is used for performing a backup.");
				}
				$this->getView()->setDisplayPath('dashboard/backup-processed');		
				ini_set('memory_limit', '200M');
				set_time_limit(0);
				$config = MK_Config::getInstance();
		
				// Create archive
				$zip = new MK_ZipArchive();
				$timestamp = time();
				$file_name = 'resources/backups/backup-'.$timestamp.'.zip';
				if ($zip->open($file_name, ZIPARCHIVE::CREATE) !== true)
				{
					throw new MK_Exception("Could not create backup file '$file_name'. Please ensure this directory is writable.");
				}
				// Backup files
				$message_list = array();
				
				$iterator  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("../tpl/uploads/"));
		
				foreach($iterator as $key=>$value)
				{
					$filename = basename($key);
					$zip->addFile($key, 'uploads/'.$filename);// or $messages[] = new MK_Message('warning', "Cannot add file '$key' to archive - It may be corrupt or too large.");
				}
				
				// Backup Database
				$database_backup = 'resources/backups/database-'.$timestamp.'.sql';
				$mysql_dump = new MK_MySQLDump($config->db->name, $database_backup, false, false);
				$mysql_dump->doDump();
				$zip->addFile($database_backup, 'database/database.sql') or $messages[] = new MK_Message('warning', "Cannot add file 'database.sql' to archive - It may be corrupt or too large.");
		
				// Create backup record
				$backup_type = MK_RecordModuleManager::getFromType('backup');
				$new_backup = MK_RecordManager::getNewRecord( $backup_type->getId() );
				$new_backup
					->setFile('admin/'.$file_name)
					->save();
				$this->getView()->backup = $new_backup;
		
				$messages[] = new MK_Message('success', 'Your backup has been successfully created. You can restore backups using the <a href="'.$this->getView()->uri( array('controller' => 'backups') ).'">backups module</a>.');
				
				//FOR TESTING 
				/*
				for( $i = 0; $i < $zip->numFiles; $i++ ){ 
				    $stat = $zip->statIndex( $i ); 
				    print_r( basename( $stat['name'] ) . PHP_EOL ); 
				} 
				die;
				*/
				
				// Close archive
				$zip->close();
				chmod($file_name, 0755);
				unlink($database_backup);
			}
			catch(Exception $e)
			{
				$messages[] = new MK_Message('error', $e->getMessage());
			}

			$this->getView()->messages = $messages;
		}
		
	}

	public function sectionSettings(){
		$config = MK_Config::getInstance();

		$this->getView()->getHead()->prependTitle( 'Settings' );

		$form_settings = array(
			'attributes' => array(
				'class' => 'standard clear-fix large'
			)
		);

		$selected_fieldset = MK_Request::getParam('form', 'amg-site-settings');
		$this->getView()->selected_fieldset = $selected_fieldset;

		$form_structure = array();

		/* Website */
		if( $config->site->mode != MK_Core::MODE_DEMO )
		{
			$form_structure['site_mode'] = array(
				'type' => 'select',
				'options' => array(
					MK_Core::MODE_PRODUCT => 'Restricted',
					MK_Core::MODE_FULL => 'Full Access'
				),
				'label' => 'CMS Mode',
				'tooltip' => '<strong>Restricted</strong> - Includes all of features needed to use the gallery.<br /><strong>Full Access</strong> - Opens additional features for coders.',
				'fieldset' => 'AMG Site Settings',
				'value' => $config->core->mode
			);
		}

		if( $this->getView()->selected_fieldset == 'stylesheets-and-colors' )
		{
		
			$file_colors_less = file_get_contents(dirname(__FILE__).'/../../../css/colors.less', false);
			$file_style_less = file_get_contents(dirname(__FILE__).'/../../../css/style.less', false);
			$file_modal_less = file_get_contents(dirname(__FILE__).'/../../../css/modal.less', false);
			$file_tablet_less = file_get_contents(dirname(__FILE__).'/../../../css/tablet.less', false);
			$file_phone_less = file_get_contents(dirname(__FILE__).'/../../../css/phone.less', false);

		}

        $form_structure['site_url'] = array(
			'type' => 'text',
			'label' => 'Site URL',
			'tooltip' => 'Your site URL<br>php variable: $config->site->url',
			'fieldset' => 'AMG Site Settings',
			'validation' => array(
				'url' => array(),
				'instance' => array()
			),
			'value' => $config->site->url
		);
        
		$form_structure['site_email'] = array(
			'type' => 'text',
			'label' => 'Site Email Address',
			'tooltip' => 'Admin email address.<br>php variable: $config->site->email',
			'fieldset' => 'AMG Site Settings',
			'validation' => array(
				'email' => array(),
				'instance' => array()
			),
			'value' => $config->site->email
		);

		$form_structure['site_timezone'] = array(
			'type' => 'select',
			'options' => MK_Utility::getTimezoneList(),
			'label' => 'Site Timezone',
			'fieldset' => 'AMG Site Settings',
			'validation' => array(
				'instance' => array()
			),
			'value' => $config->site->timezone
		);

		$form_structure['site_name'] = array(
			'type' => 'text',
			'label' => 'Site Name',
			'tooltip' => 'The name of your site<br>php variable: $config->site->name',
			'fieldset' => 'AMG Site Settings',
			'validation' => array(
				'instance' => array()
			),
			'value' => $config->site->name
		);

        $form_structure['site_caption'] = array(
			'type' => 'text',
			'label' => 'Site Caption/Tagline',
			'tooltip' => 'Short phrase for your site<br>php variable: $config->site->caption',
			'fieldset' => 'AMG Site Settings',
			'validation' => array(
				'instance' => array()
			),
			'value' => $config->site->caption
		);

        $form_structure['site_title'] = array(
			'type' => 'text',
			'label' => 'Meta Title',
			'tooltip' => 'Appended meta title.<br>php variable: $config->site->title',
			'fieldset' => 'AMG Site Settings',
			'validation' => array(
				'instance' => array()
			),
			'value' => $config->site->title
		);

         $form_structure['site_desc'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Meta Description',
			'tooltip' => 'Meta description for your site.<br>php variable: $config->site->desc',
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->desc
		);
      
        $form_structure['site_logo'] = array(
			'type' => 'file_image',
			'label' => 'Logo',
            'tooltip' => 'max-width: 440px max-height: 200px; / (Make it 2x for retina).<br>php variable: $config->site->logo',
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->logo
		);
        
        $form_structure['site_logo_sticky'] = array(
			'type' => 'file_image',
			'label' => 'Sticky Menu Logo',
            'tooltip' => 'Max height is 24px / (make it 48px high for retina).<br>php variable: $config->site->logo_sticky',
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->logo_sticky
		);

        $form_structure['site_logo_modal'] = array(
			'type' => 'file_image',
			'label' => 'Modal Top Logo',
            'tooltip' => 'Max-width: 380px and Max-height: 36px (2x for retina)<br>php variable: $config->site->logo_modal',
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->logo_modal
		);
		        
        $form_structure['site_google_site_verification'] = array(
			'type' => 'text',
			'label' => 'Google Webmaster',
			'tooltip' => 'Verification Code for Google Webmaster Tools.<br>php variable: $config->site->google_site_verification',
			'fieldset' => 'AMG Site Settings',
			'value' => !empty($config->site->google_site_verification) ? $config->site->google_site_verification : ''
		);
	
		
        $form_structure['site_enable_tracking'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Engage Tracking',
			'tooltip' => 'Help improve this product by sharing some of your public information with Engage.',
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->enable_tracking
		);
		        
		$form_structure['user_timeout'] = array(
			'type' => 'text',
			'label' => 'Member login time',
			'validation' => array(
				'instance' => array(),
				'integer' => array()
			),
			'tooltip' => 'This field defines how long members will be signed in for (in seconds). After this period, a member will need to login again.<br>Default: 31556926 (= 1 year)',
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->user_timeout
		);
		
		$form_structure['site_date_format'] = array(
			'type' => 'text',
			'label' => 'Date format',
			'tooltip' => ' Default: Y-m-d',
			'validation' => array(
				'instance' => array()
			),
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->date_format
		);
		$form_structure['site_time_format'] = array(
			'type' => 'text',
			'label' => 'Time format',
			'tooltip' => ' Default: H:i:s',

			'validation' => array(
				'instance' => array()
			),
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->time_format
		);
		$form_structure['site_template'] = array(
			'type' => 'select',
			'options' => array(),
			'label' => 'Admin Area Template',
			'fieldset' => 'AMG Site Settings',
			'value' => $config->template.'/'.$config->template_theme
		);

		/*
		$form_structure['site_path'] = array(
			'type' => 'text',
			'label' => 'Site Server Path',
			'fieldset' => 'AMG Site Settings',
			'validation' => array(
				'instance' => array()
			),
			'value' => !empty($config->site->path) ? $config->site->path : ''
		);
		*/		
		/*		
		$form_structure['site_log_actions'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Log Actions',
			'tooltip' => 'Log user actions such as deleting, editing and creating records.',
			'fieldset' => 'AMG Site Settings',
			'value' => $config->site->log_actions
		);
        */

		/*
		$form_structure['site_valid_file_extensions'] = array(
			'type' => 'textarea',
			'label' => 'File extensions',
			'tooltip' => 'The following file types can be uploaded to the site. Separate with a comma \',\'.',
			'fieldset' => 'AMG Site Settings',
			'validation' => array(
				'instance' => array()
			),
			'value' => implode(', ', (array) $config->site->valid_file_extensions)
		);
		*/


        /* THEME OPTIONS */
	
        $form_structure['site_style_enable_full_width'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Full Page Width',
			'tooltip' => 'Display the site with full width (95% edge to edge). Goto media grid settings for another option.<br>php variable: $config->site->style->enable_full_width',
			'fieldset' => 'Theme Options',
			'value' => $config->site->style->enable_full_width
		);

        $form_structure['site_style_enable_forced_login'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Force Login Page',
			'tooltip' => 'Display the sign-up page for all unauthorized users?<br>php variable: $config->site->style->enable_forced_login',
			'fieldset' => 'Theme Options',
			'value' => $config->site->style->enable_forced_login
		);
		
        $form_structure['site_media_enable_images'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Images',
			'tooltip' => 'Allow images to be displayed on your site?<br>php variable: $config->site->media->enable_images',
			'fieldset' => 'Theme Options',
			'value' => $config->site->media->enable_images
		);		

        $form_structure['site_media_enable_videos'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Videos',
			'tooltip' => 'Allow videos to be displayed on your site?<br>php variable: $config->site->media->enable_videos',
			'fieldset' => 'Theme Options',
			'value' => $config->site->media->enable_videos
		);		
		
        $form_structure['site_media_enable_audio'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Audio',
			'tooltip' => 'Allow audio to be displayed on your site?<br>php variable: $config->site->media->enable_audio',
			'fieldset' => 'Theme Options',
			'value' => $config->site->media->enable_audio
		);		

        $form_structure['site_style_modal_effect'] = array(
			'type' => 'select',
			'options' => array(
				1 => 'Fade In',
				2 => 'Right Slide',
				3 => 'Bottom Slide',
				4 => 'Newspaper',
				5 => 'Fall',
				6 => 'Side Fall',
				7 => 'Slide and Stick Top',
				8 => '3D Flip Horizontal',
				9 => '3D Flip Vertical',
				10 => '3D Sign',
				11 => 'Super Scaled',
				12 => 'Just Me',
				13 => '3D Slit',
				14 => '3D Rotate Bottom',
				15 => '3D Rotate Left',
				16 => 'Blur',
				17 => 'Slide Bottom Perspective',
				18 => 'Slide Right Perspective',
				19 => 'Slip In Top Perspective'
			),
			'label' => 'Choose Modal Effect',
			'tooltip' => 'Appears underneath images<br>php variable: $config->site->style->modal_effect',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->style->modal_effect) ? $config->site->style->modal_effect : 1
		);

		$form_structure['site_style_loading'] = array(
			'type' => 'select',
			'options' => array(
				1 => 'Style 1',
				2 => 'Style 2',
				3 => 'Style 3',
				4 => 'Style 4',
				5 => 'Style 5',
				6 => 'Style 6'
			),
			'label' => 'Choose Loading Image',
			'tooltip' => 'An animated gif that appears before an image loads.<br>Generate your own here: <a href="http://www.chimply.com/Generator" target="_blank">http://www.chimply.com/Generator</a><br>php variable: $config->site->style->loading',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->style->loading) ? $config->site->style->loading : 1
		);

        $form_structure['site_style_icon_like'] = array(
			'type' => 'select',
			'options' => array(
				'heart' => 'Heart',
				'star' => 'Star',
				'thumbs-up' => 'Thumbs Up'
			),
			'label' => 'Choose Like Icon',
			'tooltip' => 'Appears underneath images<br>php variable: $config->site->style->icon_like',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->style->icon_like) ? $config->site->style->icon_like : 'heart'
		);
				
		$form_structure['site_media_enable_approval'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Media Approval',
			'tooltip' => 'Images must be approved by admin before being shown on site.<br>php variable: $config->site->media->enable_approval',
			'fieldset' => 'Theme Options',
			'value' => $config->site->media->enable_approval
		);				

        $form_structure['site_header_enable_search'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Search',
			'tooltip' => 'Display search area?<br>php variable: $config->site->header->enable_search',
			'fieldset' => 'Theme Options',
			'value' => $config->site->header->enable_search
		);
		
		$form_structure['site_members_sort_by'] = array(
			'type' => 'select',
			'options' => array(
				'ALPHA' => 'Alphabetically',
				'COUNT' => 'Media Count'
			),
			'label' => 'Members Sort By',
			'tooltip' => 'How should members be ranked on the members page?.<br>php variable: $config->site->media->enable_approval',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->members->sort_by) ? $config->site->members->sort_by : 'COUNT'
		);		

		$form_structure['site_style_enable_cookies_notification'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Cookies Notification',
			'tooltip' => 'Display cookie popup in the footer? Useful for EU sites.<br>php variable: $config->site->style->enable_cookies_notification',
			'fieldset' => 'Theme Options',
			'value' => $config->site->style->enable_cookies_notification
		);
		
        $form_structure['site_media_max_filesize'] = array(
			'type' => 'text',
            'label' => 'Uploads Max File Size',
			'tooltip' => 'Any user-uploaded image filesize in bytes. Default: 1572864 (1.5 MB)<br>php variable: $config->site->media->max_filesize',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->media->max_filesize) ? $config->site->media->max_filesize : 1572864
		);
		
	    /*    
        $form_structure['site_style_primary_color'] = array(
			'type' => 'text',
			'label' => 'Header Color',
			'tooltip' => 'Color code: #XXXXXX or rgb(x,x,x). Will be used at other places as well.',
			'fieldset' => 'Theme Options',
			'validation' => array(
			),
			'value' => !empty($config->site->style->primary_color) ? $config->site->style->primary_color : ''
		);
        $form_structure['site_style_secondary_color'] = array(
			'type' => 'text',
			'label' => 'Menu Text Color (Must be dark). Will be used at other places as well.',
			'tooltip' => 'Color code: #XXXXXX or rgb(x,x,x)',
			'fieldset' => 'Theme Options',
			'validation' => array(
			),
			'value' => !empty($config->site->style->secondary_color) ? $config->site->style->secondary_color : ''
		);
        $form_structure['site_style_stroke_color'] = array(
			'type' => 'text',
			'label' => 'Stroke Color (Must be dark)',
			'tooltip' => 'Color code: #XXXXXX or rgb(x,x,x) - Default Medium Grey',
			'fieldset' => 'Theme Options',
			'validation' => array(
			),
			'value' => !empty($config->site->style->stroke_color) ? $config->site->style->stroke_color : ''
		);

        $form_structure['site_style_box_radius'] = array(
			'type' => 'text',
			'label' => 'Menu Text Color (Must be dark)',
			'tooltip' => 'Ex. 3px -> Rounds the edges of many boxes.',
			'fieldset' => 'Theme Options',
			'validation' => array(
			),
			'value' => !empty($config->site->style->box_radius) ? $config->site->style->box_radius : ''
		);
		
        $form_structure['site_style_box_shadow'] = array(
			'type' => 'text',
			'label' => 'Menu Text Color (Must be dark)',
			'tooltip' => 'Ex. 2px -> Adds a box shadow to some boxes.',
			'fieldset' => 'Theme Options',
			'validation' => array(
			),
			'value' => !empty($config->site->style->box_shadow) ? $config->site->style->box_shadow : ''
		);

        $form_structure['site_style_button_radius'] = array(
			'type' => 'text',
			'label' => 'Menu Text Color (Must be dark)',
			'tooltip' => 'Ex. 2px -> Adds a box shadow to some boxes.',
			'fieldset' => 'Theme Options',
			'validation' => array(
			),
			'value' => !empty($config->site->style->button_radius) ? $config->site->style->button_radius : ''
		);

        $form_structure['site_style_button_shadow'] = array(
			'type' => 'text',
			'label' => 'Menu Text Color (Must be dark)',
			'tooltip' => 'Ex. 2px -> Adds a box shadow to some boxes.',
			'fieldset' => 'Theme Options',
			'validation' => array(
			),
			'value' => !empty($config->site->style->button_shadow) ? $config->site->style->button_shadow : ''
		);

        $form_structure['site_style_enable_bg_image'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Background Image',
			'tooltip' => 'Display a random image that has been selected as a background.',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->style->enable_bg_image) ? $config->site->style->enable_bg_image : '0'
		);

        $form_structure['site_style_enable_google_fonts'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Google Fonts',
			'tooltip' => 'Use a google font as your main site font.',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->style->enable_google_fonts) ? $config->site->style->enable_google_fonts : ''
		);

        $form_structure['site_style_google_font'] = array(
			'type' => 'select',
			'options' => array(),
			'label' => 'Select a Google Font',
			'tooltip' => 'Choose google font for main text of site.',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->style->google_font) ? $config->site->style->google_font : ''
		);

        $form_structure['site_style_enable_captchas'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Captchas',
			'tooltip' => 'All forms will have captcha fields.',
			'fieldset' => 'Theme Options',
			'value' => !empty($config->site->style->enable_captchas) ? $config->site->style->enable_captchas : '0'
		);
		*/
		

		/* DEFAULT VALUES */
        $form_structure['site_values_width_single_image'] = array(
			'type' => 'text',
            'label' => 'Image Max Width PX<br>for Single Media Page',
			'tooltip' => 'Enter 0 for default. Lower value may produce lower quality images, with smaller size.<br>php variable: $config->site->values->width_single_image',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->width_single_image
		);		

        $form_structure['site_values_height_single_image'] = array(
			'type' => 'text',
            'label' => 'Image Max Height PX<br>for Single Media Page',
			'tooltip' => 'Enter 0 for default. Lower value may produce lower quality images, with smaller size.<br>php variable: $config->site->values->height_single_image',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->height_single_image
		);	
		
        $form_structure['site_values_width_carousel_image'] = array(
			'type' => 'text',
            'label' => 'Carousel Image Width PX<br>for Single Media Page',
			'tooltip' => 'Enter 0 for default<br>php variable: $config->site->values->width_carousel_image',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->width_carousel_image
		);	

        $form_structure['site_values_height_carousel_image'] = array(
			'type' => 'text',
            'label' => 'Carousel Image Height PX<br>for Single Media Page',
			'tooltip' => 'Enter 0 for default<br>php variable: $config->site->values->height_carousel_image',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->height_carousel_image
		);	
		
        $form_structure['site_values_width_comments_avatar_image'] = array(
			'type' => 'text',
            'label' => 'Avatar Image Width PX<br>for Comments',
			'tooltip' => 'Enter 0 for default<br>php variable: $config->site->values->width_comments_avatar_image',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->width_comments_avatar_image
		);	

        $form_structure['site_values_height_comments_avatar_image'] = array(
			'type' => 'text',
            'label' => 'Avatar Image Height PX<br>for Comments',
			'tooltip' => 'Enter 0 for default<br>php variable: $config->site->values->height_comments_avatar_image',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->height_comments_avatar_image
		);			

        $form_structure['site_values_width_member_banner'] = array(
			'type' => 'text',
            'label' => 'Banner Image Width PX<br>for Member Profile',
			'tooltip' => 'Enter 0 for default<br>php variable: $config->site->media->width_member_banner',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->width_member_banner
		);		

        $form_structure['site_values_height_member_banner'] = array(
			'type' => 'text',
            'label' => 'Banner Image Width PX<br>for Member Profile',
			'tooltip' => 'Enter 0 for default<br>php variable: $config->site->values->height_member_banner',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->height_member_banner
		);		

        $form_structure['site_values_width_image_box'] = array(
			'type' => 'text',
            'label' => 'Image Box Width PX<br>for Media Grids',
			'tooltip' => 'Enter 0 for default. This value determines "Custom" thumbnail box dimensions.<br>php variable: $config->site->values->width_image_box',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->width_image_box
		);	

        $form_structure['site_values_height_image_box'] = array(
			'type' => 'text',
            'label' => 'Image Box Height PX<br>for Media Grids',
			'tooltip' => 'Enter 0 for default. This value determines "Custom" thumbnail box dimensions<br>php variable: $config->site->values->height_image_box',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->height_image_box
		);	
		
        $form_structure['site_values_width_main_carousel_image'] = array(
			'type' => 'text',
            'label' => 'Carousel Image Width PX<br>for Main Carousel',
			'tooltip' => 'Enter 0 for default<br>php variable: $config->site->values->width_main_carousel_image',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->width_main_carousel_image
		);	

        $form_structure['site_values_height_main_carousel_image'] = array(
			'type' => 'text',
            'label' => 'Carousel Image Height PX<br>for Main Carousel',
			'tooltip' => 'Enter 0 for default<br>php variable: $config->site->values->height_main_carousel_image',
			'fieldset' => 'Default Values',
			'value' => $config->site->values->height_main_carousel_image
		);			
				
		
		/* LESS CSS FILES */
        $form_structure['site_dev_mode'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Development Mode',
			'tooltip' => 'The .less files will no longer be recompiled and the site will run with the latest .css files.<br>This may speed up the site.',
			'fieldset' => 'Stylesheets and Colors',
			'value' => $config->site->dev_mode
		);

        $form_structure['site_colors_less'] = array(
			'type' => 'textarea',
			'label' => 'colors.less',
			'fieldset' => 'Stylesheets and Colors',
			'value' => !empty($file_colors_less) ? $file_colors_less : ''
		);
		
        $form_structure['site_style_less'] = array(
			'type' => 'textarea',
			'label' => 'style.less',
			'fieldset' => 'Stylesheets and Colors',
			'value' => !empty($file_style_less) ? $file_style_less : ''
		);

        $form_structure['site_modal_less'] = array(
			'type' => 'textarea',
			'label' => 'modal.less',
			'fieldset' => 'Stylesheets and Colors',
			'value' => !empty($file_modal_less) ? $file_modal_less : ''
		);

        $form_structure['site_tablet_less'] = array(
			'type' => 'textarea',
			'label' => 'tablet.less',
			'fieldset' => 'Stylesheets and Colors',
			'value' => !empty($file_tablet_less) ? $file_tablet_less : ''
		);

        $form_structure['site_phone_less'] = array(
			'type' => 'textarea',
			'label' => 'phone.less',
			'fieldset' => 'Stylesheets and Colors',
			'value' => !empty($file_phone_less) ? $file_phone_less : ''
		);

		/* PERFORMANCE */
		
		$form_structure['site_style_enable_cdn'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Fetch JS from CDN',
			'tooltip' => 'Javascript files will be fetched from CDN sources, instead of locally. May improve site loading.<br>php variable: $config->site->style->enable_cdn',
			'fieldset' => 'Performance',
			'value' => $config->site->style->enable_cdn
		);	

		$form_structure['site_style_enable_minified'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Use Single CSS/JS',
			'tooltip' => 'This feature forces all.min.css and all.min.js to improve loading speed. It ignores CDN setting.<br>php variable: $config->site->style->enable_minified',
			'fieldset' => 'Performance',
			'value' => $config->site->style->enable_minified
		);	

		$form_structure['site_style_enable_cached_headers'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Use Cached Headers',
			'tooltip' => 'This may improve client performance, but may prevent loading of recent changes and updates.<br>You can modify the amount of time cached in header.php<br>php variable: $config->site->style->enable_cached_headers',
			'fieldset' => 'Performance',
			'value' => $config->site->style->enable_cached_headers
		);
		
        $form_structure['site_media_jpg_quality'] = array(
			'type' => 'integer',
			'label' => 'JPG Quality of Grid Images',
			'tooltip' => 'Values of 1 (lowest) to 100 (highest) are allowed. Default is 75.<br>php variable: $config->site->media->jpg_quality',
			'fieldset' => 'Performance',
			'value' => !empty($config->site->media->jpg_quality) ? $config->site->media->jpg_quality : 75
		);		

        $form_structure['site_media_jpg_quality_single'] = array(
			'type' => 'integer',
			'label' => 'JPG Quality of Single Image',
			'tooltip' => 'Values of 1 (lowest) to 100 (highest) are allowed. Default is 75.<br>php variable: $config->site->media->jpg_quality',
			'fieldset' => 'Performance',
			'value' => !empty($config->site->media->jpg_quality_single) ? $config->site->media->jpg_quality_single : 75
		);	
		
        $form_structure['site_media_png_compression'] = array(
			'type' => 'integer',
			'label' => 'PNG Compression',
			'tooltip' => 'Values of 1 (least compression) to 7 (more compression) are allowed. Default is 6.<br>php variable: $config->site->media->png_compression',
			'fieldset' => 'Performance',
			'value' => isset($config->site->media->png_compression) ? $config->site->media->png_compression : 6
		);		

		$form_structure['site_values_site_width_calc'] = array(
			'type' => 'text',
            'label' => 'Site Width PX<br>for Calculations',
			'tooltip' => 'Default is 1500. Lower value may produce lower quality thumbnails, with smaller size (faster download).<br>If you want the grid images to look great on very large screens (ie. retina), use a higher number.<br>php variable: $config->site->values->site_width_calc',
			'fieldset' => 'Performance',
			'value' => $config->site->values->site_width_calc
		);			

		$form_structure['site_error_reporting'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes, except notices',
				2 => 'Yes, all errors'
			),
			'label' => 'Enable PHP Error Reporting',
			'tooltip' => 'You should probably leave this on to know when something goes wrong,<br>but feel free to turn it off for security reasons.<br>php variable: $config->site->error_reporting',
			'fieldset' => 'Performance',
			'value' => $config->site->error_reporting
		);
	
		/* LANGUAGE FILES */

		 $langfiles = array();
		 foreach( glob("../lang/*.php",1) as $filename ) {
			 $filename_arr = explode('/', $filename);
			 $filename = array_pop($filename_arr);
			 $filename_arr = explode('.', $filename);
			 $langname = array_shift($filename_arr);
		     $langfiles[$filename] = ucfirst($langname);
		 }
		
        $form_structure['site_languages_language'] = array(
			'type' => 'select',
			'options' => $langfiles,
			'label' => 'Choose Default Language',
			'tooltip' => 'Select the language for the front end. You may add and edit language files in /lang/.',
			'fieldset' => 'Languages',
			'value' => !empty($config->site->languages->language) ? $config->site->languages->language : 'english.php'
		);
		
		/*
        $form_structure['site_languages_file'] = array(
			'type' => 'textarea',
			'label' => 'Selected Language File',
			'tooltip' => 'Edit the language file and click save. Be very careful! If you break it, you might have to start over.',
			'fieldset' => 'Languages',
			'value' => !empty($file_current_language) ? $file_current_language : ''
		);

        $form_structure['site_style_enable_languages_menu'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Show Languages Menu',
			'tooltip' => 'A languages menu will appear in the header<br>php variable: $config->site->style->enable_languages_menu',
			'fieldset' => 'Languages',
			'value' => !empty($config->site->style->enable_languages_menu) ? $config->site->style->enable_languages_menu : '0'
		);
		*/
		
		/* MOBILE RESPONSIVE */
		
        $form_structure['site_mobile_enable_responsive_tablet'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Responsive<br>for Tablets',
			'tooltip' => 'Responsive mode disables pinch and zoom functionality across the site. Modify responsive css in Tablet.less<br>php variable: $config->site->mobile->enable_responsive_tablet',
			'fieldset' => 'Mobile Responsive',
			'value' => $config->site->mobile->enable_responsive_tablet
		);

        $form_structure['site_mobile_enable_responsive_phone'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Responsive<br>for Phones',
			'tooltip' => 'Responsive mode disables pinch and zoom functionality across the site. Modify responsive css in Phone.less<br>php variable: $config->site->mobile->enable_responsive_phone',
			'fieldset' => 'Mobile Responsive',
			'value' => $config->site->mobile->enable_responsive_phone
		);
				
        $form_structure['site_mobile_disable_modals'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Disable Modals<br>for Tablets',
			'tooltip' => 'Replace pop-up modals with static pages on tablets.<br>php variable: $config->site->mobile->disable_modals',
			'fieldset' => 'Mobile Responsive',
			'value' => $config->site->mobile->disable_modals
		);

        $form_structure['site_mobile_items_per_page'] = array(
			'type' => 'select',
			'options' => array(
				4 => '4',
				6 => '6',
				8 => '8',
				10 => '10',
				14 => '14',
				20 => '20'
			),
			'label' => 'Item Count Per Page<br>for Phones',
			'tooltip' => '# of media items to display in grids. Only applies to phones. Improves Loading Time. <br>php variable: $config->site->mobile->items_per_page',
			'fieldset' => 'Mobile Responsive',
			'value' => !empty($config->site->mobile->items_per_page) ? $config->site->mobile->items_per_page : '6'
		);
        
		/* HEADER */

        $form_structure['site_header_enable_page_loader'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Loader',
			'tooltip' => 'Display the progress bar on every page load. Always used for Fancybox.<br>php variable: $config->site->header->enable_page_loader',
			'fieldset' => 'Header',
			'value' => $config->site->header->enable_page_loader
		);

        $form_structure['site_header_enable_header'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Header',
			'tooltip' => 'If this is set to no, only the menu will show<br>php variable: $config->site->header->enable_header',
			'fieldset' => 'Header',
			'value' => $config->site->header->enable_header
		);		

        $form_structure['site_header_enable_bg_image'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Background Image',
			'tooltip' => 'Show a custom image in header banner?<br>php variable: $config->site->header->enable_bg_image',
			'fieldset' => 'Header',
			'value' => $config->site->header->enable_bg_image
		);		

        $form_structure['site_header_bg_image'] = array(
			'type' => 'file_image',
            'label' => 'Header Background Image',
			'tooltip' => 'Upload a background image for the header.<br>php variable: $config->site->header->bg_image',
			'fieldset' => 'Header',
			'value' => !empty($config->site->header->bg_image) ? $config->site->header->bg_image : 'tpl/uploads/default-banner.png'
		);


        $form_structure['site_header_height'] = array(
			'type' => 'text',
            'label' => 'Header Height in px',
			'tooltip' => 'Adjust for banner images. Enter 0 for none (auto height).<br>php variable: $config->site->header->height',
			'fieldset' => 'Header',
			'value' => $config->site->header->height
		);

        $form_structure['site_header_menu_position'] = array(
			'type' => 'select',
			'options' => array(
				'TOP' => 'Top',
				'BOTTOM' => 'Bottom'
			),
			'label' => 'Menu Bar Position',
			'tooltip' => 'Display the menu above of below the header?<br>php variable: $config->site->header->menu_position',
			'fieldset' => 'Header',
			'value' => !empty($config->site->header->menu_position) ? $config->site->header->menu_position : 'TOP'
		);	

        $form_structure['site_style_emphasize_upload'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Emphasize Upload Button',
			'tooltip' => 'A button will appear underneath the upload and sign-up links.<br>php variable: $config->site->style->emphasize_upload',
			'fieldset' => 'Header',
			'value' => $config->site->style->emphasize_upload
		);
						
		/*
        $form_structure['site_header_enable_fb_like_button'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable FB Like Btn',
			'tooltip' => 'Display a facebook like button in the header.',
			'fieldset' => 'Header',
			'value' => !empty($config->site->header->enable_fb_like_button) ? $config->site->header->enable_fb_like_button: ''
		);
		*/
		
		/*
        $form_structure['site_header_combine_sign_in_up'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Merge Sign-up and Sign-in',
			'tooltip' => 'Combine Sign-up and Sign-in into one button to save space',
			'fieldset' => 'Header',
			'value' => !empty($config->site->header->combine_sign_in_up) ? $config->site->header->combine_sign_in_up: ''
		);*/



		/* FOOTER */
		
        $form_structure['site_footer_enable_footer'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Footer',
			'tooltip' => 'Display the footer area<br>php variable: $config->site->footer_enable_footer',
			'fieldset' => 'Footer',
			'value' => $config->site->footer->enable_footer
		);

		/*
        $form_structure['site_footer_height'] = array(
			'type' => 'text',
			'label' => 'Footer Height',
			'tooltip' => 'Height of footer in pixels',
			'fieldset' => 'Footer',
			'validation' => array(
				'instance' => array()
			),
			'value' => $config->site->footer->height
		);
		*/

        $form_structure['site_footer_twitter'] = array(
			'type' => 'text',
			'label' => 'Twitter Page',
			'tooltip' => 'ex. http://twitter.com/yourname<br>php variable: $config->site->footer->twitter',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->twitter) ? $config->site->footer->twitter : ''
		);


        $form_structure['site_footer_facebook'] = array(
			'type' => 'text',
			'label' => 'Facebook Page',
			'tooltip' => 'ex. http://facebook.com/yourpage<br>php variable: $config->site->footer->facebook',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->facebook) ? $config->site->footer->facebook : ''
		);
		
		$form_structure['site_footer_instagram'] = array(
			'type' => 'text',
			'label' => 'Instagram Profile Page',
			'tooltip' => 'ex. http://instagram.com/yourpage<br>php variable: $config->site->footer->instagram',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->instagram) ? $config->site->footer->instagram : ''
		);
		$form_structure['site_footer_google_plus'] = array(
			'type' => 'text',
			'label' => 'Google+ Profile Page',
			'tooltip' => 'ex. http://plus.google.com/yourpage<br>php variable: $config->site->footer->google_plus',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->google_plus) ? $config->site->footer->google_plus : ''
		);

		$form_structure['site_footer_flickr'] = array(
			'type' => 'text',
			'label' => 'Flickr Profile Page',
			'tooltip' => 'ex. http://flickr.com/yourpage<br>php variable: $config->site->footer->flickr',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->flickr) ? $config->site->footer->flickr : ''
		);	
					
		$form_structure['site_footer_pinterest'] = array(
			'type' => 'text',
			'label' => 'Pinterest Profile Page',
			'tooltip' => 'ex. http://pinterest.com/yourpage<br>php variable: $config->site->footer->pinterest',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->pinterest) ? $config->site->footer->pinterest : ''
		);
		
		$form_structure['site_footer_youtube'] = array(
			'type' => 'text',
			'label' => 'Youtube Profile Page',
			'tooltip' => 'ex. http://youtube.com/yourpage<br>php variable: $config->site->footer->youtube',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->youtube) ? $config->site->footer->youtube : ''
		);	

		$form_structure['site_footer_vimeo'] = array(
			'type' => 'text',
			'label' => 'Vimeo Profile Page',
			'tooltip' => 'ex. http://vimeo.com/yourpage<br>php variable: $config->site->footer->vimeo',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->vimeo) ? $config->site->footer->vimeo : ''
		);	
		$form_structure['site_footer_blog'] = array(
			'type' => 'text',
			'label' => 'Wordpress Site',
			'tooltip' => 'ex. http://yourblog.com<br>php variable: $config->site->footer->blog',
			'fieldset' => 'Footer',
			'validation' => array(
				'url' => array()
			),
			'value' => !empty($config->site->footer->blog) ? $config->site->footer->blog : ''
		);					



		/* MEDIA GRID */

        $form_structure['site_grid_type'] = array(
			'type' => 'select',
			'options' => array(
				'DEFAULT' => 'Proportional',
				'MASONRYJS' => 'Masonry - JS'
			),
			'label' => 'Grid Style',
			'tooltip' => 'Type of grid layout on home page<br>php variable: $config->site->grid->type',
			'fieldset' => 'Media Grid',
			'value' => !empty($config->site->grid->type) ? $config->site->grid->type : 'DEFAULT'
		);

        $form_structure['site_grid_enable_full_width'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Full Page Width',
			'tooltip' => 'Display the grid with full width (95% edge to edge).<br>php variable: $config->site->grid->enable_full_width',
			'fieldset' => 'Media Grid',
			'value' => $config->site->grid->enable_full_width
		);

		
        $form_structure['site_grid_items_per_page'] = array(
			'type' => 'select',
			'options' => array(
				12 => '12',
				15 => '15',
				16 => '16',
				20 => '20',
				24 => '24',
				25 => '25',
				36 => '36',
				48 => '48',
				60 => '60',
				72 => '72'
			),
			'label' => 'Item Count Per Page',
			'tooltip' => '# of media items to display on the home page<br>php variable: $config->site->grid->items_per_page',
			'fieldset' => 'Media Grid',
			'value' => !empty($config->site->grid->items_per_page) ? $config->site->grid->items_per_page : '12'
		);


        $form_structure['site_grid_thumbnail_style'] = array(
			'type' => 'select',
			'options' => array(
				'WIDE' => '16:9',
				'STANDARD' => '4:3',
				'SQUARE' => 'Square',
				'CUSTOM' => 'Custom'
			),
			'label' => 'Thumbnail Style',
			'tooltip' => 'Choose a dimension for the thumbnails - edit custom dimensions in "Default Values".<br>php variable: $config->site->grid->thumbnail_style',
			'fieldset' => 'Media Grid',
			'value' => !empty($config->site->grid->thumbnail_style) ? $config->site->grid->thumbnail_style : 'STANDARD'
		);

        $form_structure['site_grid_column_count'] = array(
			'type' => 'select',
			'options' => array(
				2 => '2',
				3 => '3',
				4 => '4',
				5 => '5',
				6 => '6',
				7 => '7',
				8 => '8'
			),
			'label' => 'Number of Columns',
			'tooltip' => 'How many columns do you want? Mobile devices are unaffected if responsive mode is enabled.<br>php variable: $config->site->grid->column_count',
			'fieldset' => 'Media Grid',
			'value' => !empty($config->site->grid->column_count) ? $config->site->grid->column_count : 4
		);

        $form_structure['site_grid_margin'] = array(
			'type' => 'text',
			'label' => 'Margin in %',
			'tooltip' => 'Amount of % space between the thumbnails. Suggested between: 0 and 5. A decimal value is allowed - eg. 2.5<br>php variable: $config->site->grid->margin',
			'fieldset' => 'Media Grid',
			'value' => $config->site->grid->margin
		);

        $form_structure['site_grid_pagination_type'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'Load More Button',
				1 => 'Fixed Page Numbers',
				2 => 'Infinite Scroll',
			),
			'label' => 'Pagination Style',
			'tooltip' => 'Type of pagination on home page.<br>php variable: $config->site->grid->pagination_type',
			'fieldset' => 'Media Grid',
			'value' => $config->site->grid->pagination_type
			);
		
        $form_structure['site_grid_enable_caption'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Show Caption',
			'tooltip' => 'Display the caption area under images?<br>php variable: $config->site->grid->enable_caption',
			'fieldset' => 'Media Grid',
			'value' => $config->site->grid->enable_caption
		);

        $form_structure['site_grid_enable_stats'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Stats Area',
			'tooltip' => 'Display the stats bar under images><br>php variable: $config->site->grid->enable_stats',
			'fieldset' => 'Media Grid',
			'value' => $config->site->grid->enable_stats
		);

        $form_structure['site_grid_hover_style'] = array(
			'type' => 'select',
			'options' => array(
				'no-effect' => 'None',
				'blur' => 'Blur',
				'sepia' => 'Sepia',
				'invert' => 'Invert',
				'grayscale' => 'Grayscale',
			),
			'label' => 'Choose a Hover Effect',
			'tooltip' => 'Applies filter effect - adjust values in custom-color.less<br>php variable: $config->site->grid->hover_style',
			'fieldset' => 'Media Grid',
			'value' => !empty($config->site->grid->hover_style) ? $config->site->grid->hover_style : 'blur'
		);

        $form_structure['site_grid_hover_enable_icon'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes',
			),
			'label' => 'Show Hover Icon',
			'tooltip' => 'Display icon on image box hover?<br>php variable: $config->site->grid->hover_enable_icon ',
			'fieldset' => 'Media Grid',
			'value' => $config->site->grid->hover_enable_icon
		);

		/*
		$form_structure['site_grid_boximage_height'] = array(
			'type' => 'text',
			'label' => 'Image Height',
			'tooltip' => 'Height in pixels. 0/Blank = maximum',
			'fieldset' => 'Media Grid',
			'validation' => array(
				'integer' => array(),
			),
			'value' => $config->site->grid->boximage_height
		);

        $form_structure['site_grid_crop_type'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'Top',
				1 => 'Center',
				2 => 'Max Width',
			),
			'label' => 'Crop Style',
			'tooltip' => 'Type of image crop on home page.',
			'fieldset' => 'Media Grid',
			'value' => $config->site->grid->crop_type
		);

        $form_structure['site_grid_lazyload'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'None',
				1 => 'Only on Mobile',
				2 => 'All Devices',
			),
			'label' => 'Lazyload Style',
			'tooltip' => 'Type of lazyload for gallery.',
			'fieldset' => 'Media Grid',
			'value' => $config->site->grid->lazyload
		);

		$form_structure['site_grid_hover_bgcolor'] = array(
			'type' => 'text',
			'label' => 'Hover BG Color',
			'tooltip' => 'Must be Hex - #A1A1A1',
			'fieldset' => 'Media Grid',
			'value' => !empty($config->site->grid->hover_bgcolor) ? $config->site->grid->hover_bgcolor : '#ffffff' 
		);	
		*/

		/* MEDIA SLIDER */

        $form_structure['site_slider_type'] = array(
			'type' => 'select',
			'options' => array(
				'NONE' => 'None',
				'OWL' => 'Owl (Simple)'
			),
			'label' => 'Slider Type',
			'tooltip' => 'Select a type of slider. Adjust the various options in footer.php!<br>php variable: $config->site->slider->type',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->type) ? $config->site->slider->type : 'OWL'
		);

        $form_structure['site_slider_media_type'] = array(
			'type' => 'select',
			'options' => array(
				'IMAGE' => 'Images Only',
				'VIDEO' => 'Video Only',
				'AUDIO' => 'Audio Only',
				'MIX' => 'Mixed',
			),
			'label' => 'Media Type',
			'tooltip' => 'Select what type of media to display<br>php variable: $config->site->slider->media_type',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->media_type) ? $config->site->slider->media_type : 'MIX'
		);

				
        $form_structure['site_slider_media_source'] = array(
			'type' => 'select',
			'options' => array(
				'SLIDER' => 'Slider Images',
				'FEATURED' => 'Featured Images',
			),
			'label' => 'Media Source',
			'tooltip' => 'Where will the media be selected from?<br>php variable: $config->site->slider->media_source',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->media_source) ? $config->site->slider->media_source : 'SLIDER'
		);
						
        $form_structure['site_slider_layout_style'] = array(
			'type' => 'select',
			'options' => array(
				'GALLERY' => 'Above Gallery'
			),
			'label' => 'Layout Style',
			'tooltip' => 'Select the position for the slider.<br>php variable: $config->site->slider->layout_style',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->layout_style) ? $config->site->slider->layout_style : 'GALLERY'
		);

        $form_structure['site_slider_count'] = array(
			'type' => 'text',
			'label' => 'Max Number of Slides',
			'tooltip' => 'How many slides will you allow?<br>php variable: $config->site->slider->count',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->count) ? $config->site->slider->count : 5
		);
		
		$form_structure['site_slider_width'] = array(
			'type' => 'text',
			'label' => 'Slider Width',
			'tooltip' => 'Width in px;<br>php variable: $config->site->slider->width',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->width) ? $config->site->slider->width : '650'
		);	
			
		$form_structure['site_slider_height'] = array(
			'type' => 'text',
			'label' => 'Slider Height',
			'tooltip' => 'Height in px;<br>php variable: $config->site->slider->height',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->height) ? $config->site->slider->height : '250'
		);	

        $form_structure['site_slider_effect_owl'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'None',
				
				'bounceZZ' => 'Bounce',
				'bounceZZDown' => 'Bounce Down',
				'bounceZZLeft' => 'Bounce Left',
				'bounceZZRight' => 'Bounce Right',
				'bounceZZUp' => 'Bounce Up',

				'fadeZZ' => 'Fade',
				'fadeZZDown' => 'Fade Down',
				'fadeZZLeft' => 'Fade Left',
				'fadeZZRight' => 'Fade Right',
				'fadeZZUp' => 'Fade Up',

				'rotateZZ' => 'Rotate',
				'rotateZZDown' => 'Rotate Down',
				'rotateZZLeft' => 'Rotate Left',
				'rotateZZRight' => 'Rotate Right',
				'rotateZZUp' => 'Rotate Up',

				'zoomZZ' => 'Zoom',
				'zoomZZDown' => 'Zoom Down',
				'zoomZZLeft' => 'Zoom Left',
				'zoomZZRight' => 'Zoom Right',
				'zoomZZUp' => 'Zoom Up',

				'flipZZX' => 'Flip X',
				'flipZZY' => 'Flip Y',

				'lightSpeedZZ' => 'LightSpeed',
				'rollZZ' => 'Roll'

			),
			'label' => 'Owl Slider Effect',
			'tooltip' => 'Select an effect style for Owl Slider.<br>php variable: $config->site->slider->effect_owl',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->effect_owl) ? $config->site->slider->effect_owl : 'fadeZZ'
		);

        $form_structure['site_slider_enable_autoplay'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			),
			'label' => 'Enable Slider Autoplay',
			'tooltip' => 'Autoplay delay time can be adjusted in the footer.<br>php variable: $config->site->slider->enable_autoplay',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->enable_autoplay) ? $config->site->slider->enable_autoplay : 'false'
		);

        $form_structure['site_slider_enable_navigation'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			),
			'label' => 'Enable Controls',
			'tooltip' => 'Show prev/next navigation controls?<br>php variable: $config->site->slider->enable_navigation',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->enable_navigation) ? $config->site->slider->enable_navigation : 'false'
		);

        $form_structure['site_slider_enable_dots'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			),
			'label' => 'Enable Dots',
			'tooltip' => 'Show dot navigation controls?<br>php variable: $config->site->slider->enable_dots',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->enable_dots) ? $config->site->slider->enable_dots : 'false'
		);


        $form_structure['site_slider_enable_video_play'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			),
			'label' => 'Enable Play Button',
			'tooltip' => 'Allow playing of video in slider?<br>php variable: $config->site->slider->enable_video_play',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->enable_video_play) ? $config->site->slider->enable_video_play : 'false'
		);


		/*
        $form_structure['site_slider_enable_home'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Home Page',
			'tooltip' => 'Shows on Home Page - ignored if layout style is Header',
			'fieldset' => 'Media Slider',
			'value' => $config->site->slider->enable_home
		);		
		
        $form_structure['site_slider_enable_media'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Media Page',
			'tooltip' => 'Shows on Media Page - ignored if layout style is Header',
			'fieldset' => 'Media Slider',
			'value' => $config->site->slider->enable_media
		);
		
        $form_structure['site_slider_enable_members'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Members Page',
			'tooltip' => 'Shows on Members Page - ignored if layout style is Header',
			'fieldset' => 'Media Slider',
			'value' => $config->site->slider->enable_members
		);
		
        $form_structure['site_slider_enable_member'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Member Page',
			'tooltip' => 'Shows on Member Page - ignored if layout style is Header',
			'fieldset' => 'Media Slider',
			'value' => $config->site->slider->enable_member
		);		
		
        $form_structure['site_slider_enable_other_pages'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Other Pages',
			'tooltip' => 'Shows on all other pages - ignored if layout style is Header',
			'fieldset' => 'Media Slider',
			'value' => $config->site->slider->enable_other_pages
		);
		
        $form_structure['site_slider_enable_fullscreen'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Force Full Screen',
			'tooltip' => 'Will force width to go edge-to-edge',
			'fieldset' => 'Media Slider',
			'value' => $config->site->slider->enable_fullscreen
		);

	
		$form_structure['site_slider_theme_ultimate'] = array(
			'type' => 'select',
			'options' => array(
				'theme1' => 'Theme 1',
				'theme2' => 'Theme 2',
				'theme3' => 'Theme 3',
				'theme4' => 'Theme 4'
			),
			'label' => 'Ultimate Slider Theme',
			'tooltip' => 'Select a general style/theme',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->theme_ultimate) ? $config->site->slider->theme_ultimate : 'theme4'
		);
		
        $form_structure['site_slider_effect_ultimate'] = array(
			'type' => 'select',
			'options' => array(
				'fade' => 'fade',
				'slide' => 'slide',
				'show' => 'show',
				'fromTop' => 'fromTop',
				'fromBottom' => 'fromBottom',
				'fromRight' => 'fromRight',
				'fromLeft' => 'fromLeft',
				'fromTopRight' => 'fromTopRight',
				'fromBottomRight' => 'fromBottomRight',
				'fromTopLeft' => 'fromTopLeft',
				'fromBottomLeft' => 'fromBottomLeft',
				'openBookY' => 'openBookY',
				'openBookX' => 'openBookX',
				'zoomIn' => 'zoomIn',
				'zoomOut' => 'zoomOut',
				'boxes' => 'boxes',
				'boxes-openBookY' => 'boxes-openBookY',
				'boxes-openBookX' => 'boxes-openBookX',
				'boxes-zoomIn' => 'boxes-zoomIn',
				'boxes-zoomOut' => 'boxes-zoomOut',
				'boxesOrder' => 'boxesOrder',
				'boxesOrder-openBookY' => 'boxesOrder-openBookY',
				'boxesOrder-openBookX' => 'boxesOrder-openBookX',
				'boxesOrder-zoomIn' => 'boxesOrder-zoomIn',
				'boxesOrder-zoomOut' => 'boxesOrder-zoomOut',
				'horizontalStripes' => 'horizontalStripes',
				'verticalStripes' => 'verticalStripes',
				'boxesDiagonal' => 'boxesDiagonal'
			),
			'label' => 'Ultimate Effect',
			'tooltip' => 'Select an effect style for Ultimate Smart Slider.<br>php variable: $config->site->slider->effect_ultimate',
			'fieldset' => 'Media Slider',
			'value' => !empty($config->site->slider->effect_ultimate) ? $config->site->slider->effect_ultimate : 'fade'
		);
		
		*/


		/* MEDIA CAROUSEL */

        $form_structure['site_carousel_type'] = array(
			'type' => 'select',
			'options' => array(
				'NONE' => 'None',
				'OWL' => 'Owl'
			),
			'label' => 'Carousel Type',
			'tooltip' => 'Select a type of carousel. Adjust the various options in footer.php!',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->type) ? $config->site->carousel->type : 'OWL' 
		);
				
        $form_structure['site_carousel_layout_style'] = array(
			'type' => 'select',
			'options' => array(
								
				'HEADER' => 'Below Header',
				'FOOTER' => 'Above Footer'
			),
			'label' => 'Layout Style',
			'tooltip' => 'Select the position for the slider.<br>php variable: $config->site->carousel->layout_style',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->layout_style) ? $config->site->carousel->layout_style : 'HEADER' 
		);

        $form_structure['site_carousel_enable_fullscreen'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Force Full Screen',
			'tooltip' => 'Will force width to go edge-to-edge',
			'fieldset' => 'Media Carousel',
			'value' => $config->site->carousel->enable_fullscreen
		);
				
        $form_structure['site_carousel_media_source'] = array(
			'type' => 'select',
			'options' => array(
				'CAROUSEL' => 'Carousel Images',
				'FEATURED' => 'Featured Images',
			),
			'label' => 'Media Source',
			'tooltip' => 'Where will the media be selected from?<br>php variable: $config->site->carousel->media_source',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->media_source) ? $config->site->carousel->media_source : 'FEATURED'
		);

        $form_structure['site_carousel_media_type'] = array(
			'type' => 'select',
			'options' => array(
				'IMAGE' => 'Images Only',
				'VIDEO' => 'Video Only',
				'AUDIO' => 'Audio Only',
				'MIX' => 'Mixed Media',
				'MEMBERS' => 'Members'
			),
			'label' => 'Media Type',
			'tooltip' => 'What type of media should be displayed?<br>php variable: $config->site->carousel->media_type',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->media_type) ? $config->site->carousel->media_type : 'MIX'
		);

        $form_structure['site_carousel_column_count'] = array(
			'type' => 'select',
			'options' => array(
				5 => '5',
				6 => '6',
				7 => '7',
				8 => '8',
				9 => '9',
				10 => '10',
				11 => '11'
			),
			'label' => 'Number of Columns',
			'tooltip' => 'How many columns do you want? Mobile and small devices are unaffected if responsive mode is enabled.<br>php variable: $config->site->carousel->column_count',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->column_count) ? $config->site->carousel->column_count : 6
		);

        $form_structure['site_carousel_count'] = array(
			'type' => 'text',
			'label' => 'Max Number of Images',
			'tooltip' => 'How many images will you allow? Phones are set to load half this value.<br>php variable: $config->site->carousel->count',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->count) ? $config->site->carousel->count : 12
		);

        $form_structure['site_carousel_margin'] = array(
			'type' => 'text',
			'label' => 'Margin in %',
			'tooltip' => 'Amount of % space between the thumbnails. Suggested between: 0 and 20. A decimal value is allowed - eg. 9.5<br>php variable: $config->site->carousel->margin',
			'fieldset' => 'Media Carousel',
			'value' => $config->site->carousel->margin
		);

        $form_structure['site_carousel_thumbnail_style'] = array(
			'type' => 'select',
			'options' => array(
				'WIDE' => '16:9',
				'STANDARD' => '4:3',
				'SQUARE' => 'Square',
				'CUSTOM' => 'Custom'
			),
			'label' => 'Thumbnail Style',
			'tooltip' => 'Choose a dimension for the thumbnails; edit custom dimensions in "Default Values".<br>php variable: $config->site->carousel->thumbnail_style',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->thumbnail_style) ? $config->site->carousel->thumbnail_style : 'STANDARD'
		);

        $form_structure['site_carousel_enable_autoplay'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			),
			'label' => 'Enable Carousel Autoplay',
			'tooltip' => 'Autoplay delay time can be adjusted in the footer.<br>php variable: $config->site->carousel->enable_autoplay',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->enable_autoplay) ? $config->site->carousel->enable_autoplay : 'false'
		);

        $form_structure['site_carousel_enable_navigation'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			),
			'label' => 'Enable Controls',
			'tooltip' => 'Show prev/next navigation controls?',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->enable_navigation) ? $config->site->carousel->enable_navigation : 'false'
		);

        $form_structure['site_carousel_enable_dots'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			),
			'label' => 'Enable Dots',
			'tooltip' => 'Show dot navigation controls?',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->enable_dots) ? $config->site->carousel->enable_dots : 'false'
		);


        $form_structure['site_carousel_enable_video_play'] = array(
			'type' => 'select',
			'options' => array(
				'false' => 'No',
				'true' => 'Yes'
			),
			'label' => 'Enable Play Button',
			'tooltip' => 'Allow playing of video in carousel?',
			'fieldset' => 'Media Carousel',
			'value' => !empty($config->site->carousel->enable_video_play) ? $config->site->carousel->enable_video_play : 'false'
		);


		/*
        $form_structure['site_carousel_enable_home'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Home Page',
			'tooltip' => 'Shows on Home Page - ignored if layout style is Header',
			'fieldset' => 'Media Carousel',
			'value' => $config->site->carousel->enable_home
		);
		
        $form_structure['site_carousel_enable_media'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Media Page',
			'tooltip' => 'Shows on Media Page - ignored if layout style is Header',
			'fieldset' => 'Media Carousel',
			'value' => $config->site->carousel->enable_media
		);
		
        $form_structure['site_carousel_enable_members'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Members Page',
			'tooltip' => 'Shows on Members Page - ignored if layout style is Header',
			'fieldset' => 'Media Carousel',
			'value' => $config->site->carousel->enable_members
		);

        $form_structure['site_carousel_enable_member'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Member Page',
			'tooltip' => 'Shows on Member Page - ignored if layout style is Header',
			'fieldset' => 'Media Carousel',
			'value' => $config->site->carousel->enable_member
		);		

        $form_structure['site_carousel_enable_other_pages'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable on Other Pages',
			'tooltip' => 'Shows on all other pages - ignored if layout style is Header',
			'fieldset' => 'Media Carousel',
			'value' => $config->site->carousel->enable_other_pages
		);
		*/


        /* MEDIA SINGLE PAGE */

        $form_structure['site_media_layout_style'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'Image first'
			),
            'label' => 'Choose Layout Style',
			'tooltip' => 'Does the image or the title go on top?<br>php variable: $config->site->media->layout_style',
			'fieldset' => 'Media Page',
			'value' => $config->site->media->layout_style
		);

		$form_structure['site_media_enable_stretched_image'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Stretched Image',
			'tooltip' => 'Should images be stretched to fit?<br>Disable if you intend to upload lower resolution images and want to avoid blurry jpgs.<br>php variable: $config->site->media->enable_stretched_image',
			'fieldset' => 'Media Page',
			'value' => $config->site->media->enable_stretched_image
		);
				        
        $form_structure['site_media_comments_type'] = array(
			'type' => 'select',
			'options' => array(
				'DEFAULT' => 'AMG Default',
				'FACEBOOK' => 'Facebook Comments',
				'DISABLED' => 'Disabled'				
			),
			'label' => 'Enable Comments',
			'tooltip' => 'Display comment area?<br>php variable: $config->site->media->comments_type',
			'fieldset' => 'Media Page',
			'value' => !empty($config->site->media->comments_type) ? $config->site->media->comments_type : 'DEFAULT'
		);	
		
		$form_structure['site_enable_guest_comments'] = array(
			'label' => 'Can Guests Comment?',
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'tooltip' => 'Are non-registered users able to post comments? May produce spam!<br>php variable: $config->site->enable_guest_comments',
			'fieldset' => 'Media Page',
			'value' => $config->site->enable_guest_comments
		);

		$form_structure['site_enable_guest_likes'] = array(
			'label' => 'Can Guests Like Images & Comments?',
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'tooltip' => 'Are non-registered users able to like images and comments?<br>php variable: $config->site->enable_guest_likes',
			'fieldset' => 'Media Page',
			'value' => $config->site->enable_guest_likes
		);

        $form_structure['site_media_audio_player'] = array(
			'type' => 'select',
			'options' => array(
				'soundcloud' => 'Soundcloud Embed'
			),
			'label' => 'Audio Player Style',
			'tooltip' => 'Choose a type of audio player<br>php variable: $config->site->media->audio_player',
			'fieldset' => 'Media Page',
			'value' => isset($config->site->media->audio_player) ? $config->site->media->audio_player : 'soundcloud'
		);		


        $form_structure['site_media_enable_autoplay'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Media Autoplay',
			'tooltip' => 'Autoplay settings do not effect Vines.<br>php variable: $config->site->media->enable_autoplay',
			'fieldset' => 'Media Page',
			'value' => $config->site->media->enable_autoplay
		);

        $form_structure['site_media_enable_exif'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Exif Data',
			'tooltip' => 'Display exif data below single images if exists?<br>php variable: $config->site->media->enable_exif',
			'fieldset' => 'Media Page',
			'value' => $config->site->media->enable_exif
		);

        $form_structure['site_media_enable_source'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Credit Link',
			'tooltip' => 'Allow users to add a source/credit link?<br>php variable: $config->site->media->enable_source',
			'fieldset' => 'Media Page',
			'value' => $config->site->media->enable_source
		);

        $form_structure['site_media_enable_view_original'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable View Original',
			'tooltip' => 'Show a button that links directly to source media file?<br>php variable: $config->site->media->enable_view_original',
			'fieldset' => 'Media Page',
			'value' => $config->site->media->enable_view_original
		);	

        $form_structure['site_enable_reporting'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Image Reporting',
			'tooltip' => 'Show button to let users report spammy uploads to admins?<br>php variable: $config->site->enable_reporting',
			'fieldset' => 'Media Page',
			'value' => $config->site->enable_reporting
		);

			
	        
		/*
        $form_structure['site_media_video_player'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'Default',
				1 => 'JVPlayer'
			),
            'label' => 'Video Player',
			'tooltip' => 'Use a custom video player.',
			'fieldset' => 'Media Page',
			'value' => $config->site->media->video_player
		);

        $form_structure['site_media_enable_page_lightbox'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
            'label' => 'Show in Lightbox',
			'tooltip' => 'Do you want to show the media page in a pop-up lightbox instead of a single page?',
			'fieldset' => 'Media Page',
			'value' => $config->site->media->enable_page_lightbox
		);
		*/
		

		/* MEMBER PROFILES */

        $form_structure['site_members_enable_email_registration'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Email Registration',
			'tooltip' => 'Can new members register with email? If not, they must use a social network.<br>php variable: $config->site->members->enable_email_registration',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_email_registration
		);

        $form_structure['site_members_enable_signup_notice'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Show Sign Up Notice?',
			'tooltip' => 'Notice is displayed above sign-up fields. You can modify the text in the language file.<br>php variable: $config->site->members->enable_signup_notice',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_signup_notice
		);
		
        $form_structure['site_members_enable_approval'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Member Approval',
			'tooltip' => 'A member cannot upload or comment until their account is approved by an admin.<br>php variable: $config->site->members->enable_approval',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_approval
		);

        $form_structure['site_members_enable_unapproved_login'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Unapproved Logins',
			'tooltip' => 'Can a member login without being approved? Only works if member approval is enabled.<br>php variable: $config->site->members->enable_unapproved_login',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_unapproved_login
		);
		
        $form_structure['site_members_disable_registration'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Disable New Member Registration',
			'tooltip' => 'The sign-up button will be removed and no new members can register.<br>php variable: $config->site->members->disable_registration',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->disable_registration
		);

        $form_structure['site_members_disable_uploads'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Disable Media Uploads',
			'tooltip' => 'The upload button will be removed and all members can no longer upload.<br>php variable: $config->site->members->disable_uploads',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->disable_uploads
		);

        $form_structure['site_members_enable_contact_form'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Contacting',
			'tooltip' => 'Allow users to contact each other?<br>php variable: $config->site->members->enable_contact_form',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_contact_form
		);
		
        $form_structure['site_default_avatar'] = array(
			'type' => 'file_image',
            'label' => 'Default Avatar',
			'tooltip' => 'Square - min 225 x 225 / x2 for retina.<br>php variable: $config->site->default_avatar',
			'fieldset' => 'Member Page',
			'value' => !empty($config->site->default_avatar) ? $config->site->default_avatar : 'tpl/uploads/default-avatar.png'
		);


        $form_structure['site_members_enable_cover_photo'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
            'label' => 'Enable Cover Photo',
			'tooltip' => 'Allow members to add custom profile banners?<br>php variable: $config->site->members->enable_cover_photo',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_cover_photo
		);

        $form_structure['site_members_default_cover_photo'] = array(
			'type' => 'file_image',
            'label' => 'Default Cover Photo',
			'tooltip' => 'Rectangle - min 960 x 223 / x1.5 for retina.<br>php variable: $config->site->members->default_cover_photo',
			'fieldset' => 'Member Page',
			'value' => !empty($config->site->members->default_cover_photo) ? $config->site->members->default_cover_photo : 'tpl/uploads/default-cover-photo.png'
		);
		
        $form_structure['site_members_enable_video'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Video Field',
			'tooltip' => 'Allow users to display a personal video?<br>php variable: $config->site->members->enable_video',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_video
		);

		
        $form_structure['site_members_enable_gender'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Gender Field',
			'tooltip' => 'Allow users to set a gender?<br>php variable: $config->site->members->enable_gender',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_gender
		);

        $form_structure['site_members_enable_occupation'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Occupation Field',
			'tooltip' => 'Allow users to set an occupation?<br>php variable: $config->site->members->enable_occupation',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_occupation
		);

        $form_structure['site_members_enable_category'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Category Field',
			'tooltip' => 'Allow users to set a category?<br>php variable: $config->site->members->enable_category',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_category
		);

        $form_structure['site_members_enable_interests'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Interests Field',
			'tooltip' => 'Allow users to add interest tags?<br>php variable: $config->site->members->enable_interests',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_interests
		);

        $form_structure['site_members_enable_skills'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Skills Field',
			'tooltip' => 'Allow users to add skills to their profile?<br>php variable: $config->site->members->enable_skills',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_skills
		);
				
        $form_structure['site_members_enable_dob'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Date of Birth Field',
			'tooltip' => 'Allow users to set a date of birth?<br>php variable: $config->site->members->enable_dob',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_dob
		);

        $form_structure['site_members_enable_software'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Software Field',
			'tooltip' => 'Allow users to set a favorite software field?<br>php variable: $config->site->members->enable_software',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_software
		);

        $form_structure['site_members_enable_available'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Contact Availability',
			'tooltip' => 'Allow users to set contact availability?<br>php variable: $config->site->members->enable_available',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_available
		);

        $form_structure['site_members_enable_resume'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Portfolio Url',
			'tooltip' => 'Allow users to provide a resume/portfolio url?<br>php variable: $config->site->members->enable_resume',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_resume
		);

        $form_structure['site_members_enable_public_emails'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Public Emails',
			'tooltip' => 'Allow users to display their email addresses?<br>php variable: $config->site->members->enable_public_emails',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_public_emails
		);

        $form_structure['site_members_enable_stats'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Statistics',
			'tooltip' => 'Allow users to display their statistics?<br>php variable: $config->site->members->enable_stats',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_stats
		);
		

		/*
        $form_structure['site_members_enable_invitations'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Invitations',
			'tooltip' => 'Allow users to invite other users.',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_invitations
		);

        $form_structure['site_members_enable_verification'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Member Verification',
			'tooltip' => 'Members cannot edit their profile until their email address is verified.',
			'fieldset' => 'Member Page',
			'value' => $config->site->members->enable_verification
		);

        $form_structure['site_members_maximum_uploads'] = array(
			'type' => 'text',
			'label' => 'Maximum Member Uploads',
			'tooltip' => 'Each member is limited to this maximum number of items. 0=unlimited.',
			'fieldset' => 'Member Page',
			'value' => isset($config->site->members->maximum_uploads) ? $config->site->members->maximum_uploads : '0'
		);
		*/
		
		/*
        $form_structure['site_members_activity_time'] = array(
			'type' => 'text',
			'label' => 'Days Between Activity Emails',
			'tooltip' => 'An activity email will be sent to users that have not signed in for X days, and every X days after that.',
			'fieldset' => 'Member Page',
			'value' => !empty($config->site->members->activity_time) ? $config->site->members->activity_time : ''
		);

        $form_structure['site_members_enable_bg_image'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Most Liked Image',
				1 => 'Most Viewed Image',
				1 => 'A Random Image'
			),
			'label' => 'Enable Background Img',
			'tooltip' => 'A custom background Image must be some minimum size to qualify, depending on device, otherwise none will be shown.',
			'fieldset' => 'Member Page',
			'value' => !empty($config->site->members->enable_bg_image) ? $config->site->members->enable_bg_image : ''
		);
		*/

		/* WATERMARK */

		$form_structure['site_media_enable_watermark'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Watermark',
			'tooltip' => 'Add a watermark to all uploaded images. Caution: all new images saved onto server will have watermarks.<br>php variable: $config->site->media->enable_watermark',
			'fieldset' => 'Watermark',
			'value' => $config->site->media->enable_watermark
		);	
				
        $form_structure['site_media_watermark'] = array(
			'type' => 'file_image',
            'label' => 'Watermark Image',
			'tooltip' => 'Upload a watermark image.<br>php variable: $config->site->media->watermark',
			'fieldset' => 'Watermark',
			'value' => !empty($config->site->media->watermark) ? $config->site->media->watermark : 'default.png'
		);

        $form_structure['site_media_watermark_scale'] = array(
			'type' => 'text',
            'label' => 'Watermark % scaled size',
			'tooltip' => '100 stretches the watermark edge to edge, width-wise. Recommended 20-30<br>php variable: $config->site->media->watermark_scale',
			'fieldset' => 'Watermark',
			'value' => !empty($config->site->media->watermark_scale) ? $config->site->media->watermark_scale : 30
		);

        $form_structure['site_media_watermark_position'] = array(
			'type' => 'select',
			'options' => array(
				'center' => 'Center',
				'top-right' => 'Top Right',
				'top-left' => 'Top Left',
				'bottom-right' => 'Bottom Right',
				'bottom-left' => 'Bottom Left',
				'top' => 'Top',
				'bottom' => 'Bottom',
				'left' => 'Left',
				'right' => 'Right'
				
			),            
			'label' => 'Watermark Position',
			'tooltip' => 'Default: Center<br>php variable: $config->site->media->watermark_position',
			'fieldset' => 'Watermark',
			'value' => !empty($config->site->media->watermark_position) ? $config->site->media->watermark_position : 'center'
		);

	
		/* WORDPRESS AUTO POST */

        $form_structure['site_wordpress_site_url'] = array(
			'type' => 'text',
			'label' => 'WP Site Url',
			'tooltip' => 'Site url for wordpress site. ie. http://www.yoursite.com<br>php variable: $config->site->wordpress->site_url',
			'fieldset' => 'Wordpress',
			'validation' => array(
				'url' => array()
			),
			'value' => isset($config->site->wordpress->site_url) ? $config->site->wordpress->site_url : ''
		);

        $form_structure['site_wordpress_force_login'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'WP Forced Login',
			'tooltip' => 'Users will be automatically logged in with their WP credentials.<br> If they are not logged into your wp site, they will be redirected<br>and forced to login before being able to access AMG.<br>php variable: $config->site->wordpress->force_login',
			'fieldset' => 'Wordpress',
			'value' => $config->site->wordpress->force_login
		);

        $form_structure['site_wordpress_strict_login'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'WP Strict Login',
			'tooltip' => 'Any visitor can view AMG, but the only login option will be via a Wordpress site.<br> The sign-up and sign-in modals are fully disabled.<br>php variable: $config->site->wordpress->strict_login',
			'fieldset' => 'Wordpress',
			'value' => $config->site->wordpress->strict_login
		);
								
        $form_structure['site_wordpress_enable_post_to_wp'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable WP Autopost',
			'tooltip' => 'AMG will automatically combine and create single multi-media posts to a Wordpress site.<br>Useful for easily building blog content. See AMG documentation for more info.<br>php variable: $config->site->wordpress->enable_post_to_wp',
			'fieldset' => 'Wordpress',
			'value' => $config->site->wordpress->enable_post_to_wp
		);

        $form_structure['site_wordpress_admin_username'] = array(
			'type' => 'text',
			'label' => 'WP Admin Username',
			'tooltip' => 'Main /WP-Admin login username. Used for auto-posting.<br>php variable: $config->site->wordpress->admin_username',
			'fieldset' => 'Wordpress',
			'value' => !empty($config->site->wordpress->admin_username) ? $config->site->wordpress->admin_username : ''
		);

        $form_structure['site_wordpress_admin_password'] = array(
			'type' => 'text',
			'label' => 'WP Admin Password',
			'tooltip' => 'Main /WP-Admin login password. Used for auto-posting.<br>php variable: $config->site->wordpress->admin_password',
			'fieldset' => 'Wordpress',
			'value' => isset($config->site->wordpress->admin_password) ? $config->site->wordpress->admin_password : ''
		);

        $form_structure['site_wordpress_admin_id'] = array(
			'type' => 'integer',
			'label' => 'WP Author ID',
			'tooltip' => 'Used whenever a member does not have a specific WP User Id set.<br>Setting to 0 disables WP auto-posts for these members.<br>php variable: $config->site->wordpress->admin_id',
			'fieldset' => 'Wordpress',
			'value' => isset($config->site->wordpress->admin_id) ? $config->site->wordpress->admin_id : 0
		);		

        $form_structure['site_wordpress_taxonomy_categories'] = array(
			'type' => 'text',
			'label' => 'WP Taxonomy for Categories',
			'tooltip' => 'Change this if you have renamed the default category taxonomy slug.<br>php variable: $config->site->wordpress->taxonomy_categories',
			'fieldset' => 'Wordpress',
			'value' => isset($config->site->wordpress->taxonomy_categories) ? $config->site->wordpress->taxonomy_categories : 'category'
		);

        $form_structure['site_wordpress_taxonomy_tags'] = array(
			'type' => 'text',
			'label' => 'WP Taxonomy for Tags',
			'tooltip' => 'Change this if you have renamed the default tags taxonomy slug.<br>php variable: $config->site->wordpress->taxonomy_tags',
			'fieldset' => 'Wordpress',
			'value' => isset($config->site->wordpress->taxonomy_tags) ? $config->site->wordpress->taxonomy_tags : 'post_tag'
		);		
		
		
        /* SOCIAL */
        
        $form_structure['site_social_image_square'] = array(
			'type' => 'file_image',
			'label' => 'Square Banner For Social Sharing',
            'tooltip' => 'Recommended min. 250px x 250px.<br>php variable: $config->site->social->image_square',
			'fieldset' => 'Social Sharing',
			'value' => isset($config->site->social->image_square) ? $config->site->social->image_square : ''
		);
        $form_structure['site_social_image_wide'] = array(
			'type' => 'file_image',
			'label' => 'Wide Banner For Social Sharing',
            'tooltip' => 'Recommended min. 650px x 338px.<br>php variable: $config->site->social->image_wide',
			'fieldset' => 'Social Sharing',
			'value' => isset($config->site->social->image_wide) ? $config->site->social->image_wide : ''
		);
        $form_structure['site_social_twitter'] = array(
			'type' => 'text',
			'label' => 'Twitter Handle',
			'tooltip' => 'ex. @username<br>php variable: $config->site->social->twitter',
			'fieldset' => 'Social Sharing',
			'value' => isset($config->site->social->twitter) ? $config->site->social->twitter : ''
		);

        $form_structure['site_social_enable_post_to_fb'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Facebook Autopost',
			'tooltip' => 'AMG will automatically post media to a Facebook Fan Page. Adjust in "API Keys".',
			'fieldset' => 'Social Sharing',
			'value' => $config->site->social->enable_post_to_fb
		);

        $form_structure['site_social_fb_post_type'] = array(
			'type' => 'select',
			'options' => array(
				'UPLOAD' => 'Upload',
				'LINK' => 'Link'
			),
			'label' => 'Facebook Post Type',
			'tooltip' => 'Uploads will be added to your page album, while links produce blog-post style links.',
			'fieldset' => 'Social Sharing',
			'value' => !empty($config->site->social->fb_post_type) ? $config->site->social->fb_post_type : 'UPLOAD'
		);

        /* ADVERTISING */

        $form_structure['site_ads_header_468x60'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Header: Right Side<br>HTML Code',
			'tooltip' => 'Medium leaderboard - 468 x 60<br>php variable: $config->site->ads->header_468x60',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->header_468x60) ? $config->site->ads->header_468x60 : ''
		);

        $form_structure['site_ads_top_728x90'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Top: Above Fold Left Side<br>HTML Code',
			'tooltip' => 'Large leaderboard - 728 x 90<br>php variable: $config->site->ads->top_728x90',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->top_728x90) ? $config->site->ads->top_728x90 : ''
		);
        
        $form_structure['site_ads_top_242x90'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Top: Above Fold Right Side<br>HTML Code',
			'tooltip' => 'Small leaderboard - 242 x 90<br>php variable: $config->site->ads->top_242x90',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->top_242x90) ? $config->site->ads->top_242x90 : ''
		);

        $form_structure['site_ads_top_970x90'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Top: Above Fold Full Size<br>HTML Code',
			'tooltip' => 'Full size leaderboard - 970 x 90<br>php variable: $config->site->ads->top_970x90',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->top_970x90) ? $config->site->ads->top_970x90 : ''
		);

        $form_structure['site_ads_top_980x120'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Top: Above Fold Full Size<br>HTML Code',
			'tooltip' => 'Full size leaderboard - 980 x 120<br>php variable: $config->site->ads->top_980x120',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->top_980x120) ? $config->site->ads->top_980x120 : ''
		);
		
        $form_structure['site_ads_sidebar_160x600'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Sidebar: Medium Screens<br>HTML Code',
			'tooltip' => 'Skyscraper - 160 x 600<br>php variable: $config->site->ads->sidebar_160x600',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->sidebar_160x600) ? $config->site->ads->sidebar_160x600 : ''
		);

        $form_structure['site_ads_sidebar_300x250'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Sidebar: Large Screens<br>HTML Code',
			'tooltip' => 'Rectangle - 300 x 250<br>php variable: $config->site->ads->sidebar_300x250',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->sidebar_300x250) ? $config->site->ads->sidebar_300x250 : ''
		);

        $form_structure['site_ads_enable_header'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Header Ad',
			'tooltip' => 'Display horizontal ad in the header?',
			'fieldset' => 'Advertising',
			'value' => $config->site->ads->enable_header
		);	
		
        $form_structure['site_ads_enable_home_top'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Home Page Top Ad',
			'tooltip' => 'Display horizontal ads above the fold?',
			'fieldset' => 'Advertising',
			'value' => $config->site->ads->enable_home_top
		);	
				
        $form_structure['site_ads_enable_media_top'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Single Media Page Top Ad',
			'tooltip' => 'Display horizontal ads above the fold?',
			'fieldset' => 'Advertising',
			'value' => $config->site->ads->enable_media_top
		);	

        $form_structure['site_ads_enable_member_top'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Member Page Top Ad',
			'tooltip' => 'Display horizontal ads above the fold?',
			'fieldset' => 'Advertising',
			'value' => $config->site->ads->enable_member_top
		);	
		
        $form_structure['site_ads_enable_member_sidebar'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Member Page Sidebar Ad',
			'tooltip' => 'Display vertical ads on the right side?',
			'fieldset' => 'Advertising',
			'value' => $config->site->ads->enable_member_sidebar
		);	

        $form_structure['site_ads_enable_members_top'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Members Page Top Ad',
			'tooltip' => 'Display horizontal ads above the fold?',
			'fieldset' => 'Advertising',
			'value' => $config->site->ads->enable_members_top
		);	
		/*
        $form_structure['site_ads_enable_other_top'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Other Pages Top Ad',
			'tooltip' => 'Displays horizontal ads above the fold.',
			'fieldset' => 'Advertising',
			'value' => $config->site->ads->enable_other_top
		);	
		*/
		/*
        $form_structure['site_ads_enable_home_sidebar'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Home Page<br>Sidebar Ad Area',
			'tooltip' => 'Displays vertical ads on the right side.',
			'fieldset' => 'Advertising',
			'value' => !empty($config->site->ads->enable_home_sidebar) ? $config->site->ads->enable_home_sidebar : '0'
		);	

		$form_structure['site_ads_enable_media_sidebar'] = array(
			'type' => 'select',
			'options' => array(
				0 => 'No',
				1 => 'Yes'
			),
			'label' => 'Enable Single Media Page Sidebar Ad',
			'tooltip' => 'Display vertical ads on the right side?',
			'fieldset' => 'Advertising',
			'value' => $config->site->ads->enable_media_sidebar
		);	
		
        $form_structure['site_ads_enable_members_sidebar'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Members Page<br>Sidebar Ad Area',
			'tooltip' => 'Displays vertical ads on the right side.',
			'fieldset' => 'Advertising',
			'value' => !empty($config->site->ads->enable_members_sidebar) ? $config->site->ads->enable_members_sidebar : '0'
		);
		

        $form_structure['site_ads_enable_blog_top'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Blog Page<br>Top Ad Area',
			'tooltip' => 'Displays horizontal ads above the fold.',
			'fieldset' => 'Advertising',
			'value' => !empty($config->site->ads->enable_blog_top) ? $config->site->ads->enable_blog_top : '0'
		);	

        $form_structure['site_ads_enable_blog_sidebar'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Blog Page<br>Sidebar Ad Area',
			'tooltip' => 'Displays vertical ads on the right side.',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->enable_blog_sidebar) ? $config->site->ads->enable_blog_sidebar : '0'
		);	

         $form_structure['site_ads_enable_other_sidebar'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Other Pages<br>Sidebar Ad Area',
			'tooltip' => 'Displays vertical ads on the right side.',
			'fieldset' => 'Advertising',
			'value' => isset($config->site->ads->enable_other_sidebar) ? $config->site->ads->enable_other_sidebar : '0'
		);
		*/




        /* EMAILS */

        $form_structure['site_emails_ssl_enabled'] = array(
			'type' => 'select',
			'options' => array(
				'no' => 'No',
				'ssl' => 'SSL',
				'tls' => 'TLS',
			),            
			'label' => 'Enable Secure Email',
			'tooltip' => 'Do you want email sent by SSL or TLS?<br>php variable: $config->site->emails->ssl_enable',
			'fieldset' => 'Emails',
			'value' => !empty($config->site->emails->ssl_enabled) ? $config->site->emails->ssl_enabled : 'NO'
		);

        $form_structure['site_emails_ssl_server'] = array(
			'type' => 'text',
			'label' => 'Secure Email Server',
			'fieldset' => 'Emails',
			'tooltip' => 'Example: mail.yourserver.com<br>php variable: $config->site->emails->ssl_server',
			'value' => isset($config->site->emails->ssl_server) ? $config->site->emails->ssl_server : ''
		);
		  
        $form_structure['site_emails_ssl_username'] = array(
			'type' => 'text',
			'label' => 'Secure Email Username',
			'fieldset' => 'Emails',
			'value' => isset($config->site->emails->ssl_username) ? $config->site->emails->ssl_username : ''
		);

        $form_structure['site_emails_ssl_password'] = array(
			'type' => 'text',
			'label' => 'Secure Email Password',
			'fieldset' => 'Emails',
			'value' => isset($config->site->emails->ssl_password) ? $config->site->emails->ssl_password : ''
		);
		
		$form_structure['site_email_template'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-large'
			),
			'label' => 'Email Template',
			'tooltip' => 'All emails sent from the site will use this template.<br>Use {email_content} to specify where you want the body text to appear.<br> You can also use {site_name} for your site name.',
			'fieldset' => 'Emails',
			'value' => $config->site->email_template
		);

        $form_structure['site_emails_registration_subject'] = array(
			'type' => 'text',
			'label' => 'Welcome User<br>Subject Text',
			'tooltip' => 'Subject line of welcome email. You can use {site_name} as a variable for your site name.',
			'fieldset' => 'Emails',
			'validation' => array(
				'instance' => array()
			),
			'value' => isset($config->site->emails->registration_subject) ? $config->site->emails->registration_subject : ''
		);
	
		$form_structure['site_emails_registration_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Welcome User<br>Body Text',
			'tooltip' => 'Main content of welcome email. You can use {user_display_name} and {site_domain} as variables.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->emails->registration_text) ? $config->site->emails->registration_text : ''
		);

		$form_structure['site_emails_registration_approval_notice'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Welcome User<br>Approval Notice',
			'tooltip' => 'If member approval is enabled. This note will be added to the email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->emails->registration_approval_notice) ? $config->site->emails->registration_approval_notice : ''
		);

        $form_structure['site_emails_registration_subject_admin'] = array(
			'type' => 'text',
			'label' => 'New Sign Up Admin<br>Subject Text',
			'tooltip' => 'Subject line of admin new sign-up notice..',
			'fieldset' => 'Emails',
			'validation' => array(
				'instance' => array()
			),
			'value' => isset($config->site->emails->registration_subject_admin) ? $config->site->emails->registration_subject_admin : ''
		);
	
		$form_structure['site_emails_registration_text_admin'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'New Sign Up Admin<br>Body Text',
			'tooltip' => 'Main content of admin new sign-up email.<br>You can use {user_profile_link} and {user_display_name} as variables.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->emails->registration_text_admin) ? $config->site->emails->registration_text_admin : ''
		);

		$form_structure['site_emails_registration_approval_notice_admin'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'New Sign Up Admin<br>Approval Notice',
			'tooltip' => 'If member approval is enabled, this note will be added to the sign-up email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->emails->registration_approval_notice_admin) ? $config->site->emails->registration_approval_notice_admin : ''
		);

        $form_structure['site_emails_approved_subject'] = array(
			'type' => 'text',
			'label' => 'Approved User<br>Subject Text',
			'tooltip' => 'Subject line of approved email.<br>You can use {site_name} and {user_display_name} as variables.',
			'fieldset' => 'Emails',
			'validation' => array(
				'instance' => array()
			),
			'value' => isset($config->site->emails->approved_subject) ? $config->site->emails->approved_subject : ''
		);
	
		$form_structure['site_emails_approved_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-medium'
			),
			'label' => 'Approved User<br>Body Text',
			'tooltip' => 'Main content of approved email.<br>You can use {user_profile_link}, {user_display_name}, {site_domain}, and {site_name} as variables.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->emails->approved_text) ? $config->site->emails->approved_text : ''
		);

		/*

        $form_structure['email_enable_admin_action_download'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Download Email<br>(Admin Only)',
			'tooltip' => 'Admin will receive an email for every download action.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_admin_action_download) ? $config->site->email->enable_admin_action_download : ''
		);

        $form_structure['email_enable_admin_action_like'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Like Email<br>(Admin Only)',
			'tooltip' => 'Admin will receive an email for every like action.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_admin_action_like) ? $config->site->email->enable_admin_action_like : ''
		);

        $form_structure['email_enable_admin_action_follow'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Follow Email<br>(Admin Only)',
			'tooltip' => 'Admin will receive an email for every follow action.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_admin_action_follow) ? $config->site->email->enable_admin_action_follow : ''
		);

        $form_structure['email_enable_admin_action_upload'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Upload Email<br>(Admin Only)',
			'tooltip' => 'Admin will receive an email for every upload action.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_admin_action_upload) ? $config->site->email->enable_admin_action_upload : ''
		);

        $form_structure['email_enable_admin_action_comment'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Comment Email<br>(Admin Only)',
			'tooltip' => 'Admin will receive an email for every comment action.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_admin_action_comment) ? $config->site->email->enable_admin_action_comment : ''
		);

        $form_structure['email_enable_admin_action_signup'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Signup Email<br>(Admin Only)',
			'tooltip' => 'Admin will receive an email for every signup action.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_admin_action_signup) ? $config->site->email->enable_admin_action_signup : ''
		);

        $form_structure['email_enable_admin_action_invitation'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Invitation Email<br>(Admin Only)',
			'tooltip' => 'Admin will receive an email for every invitation action.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_admin_action_invitation) ? $config->site->email->enable_admin_action_invitation : ''
		);
		
        $form_structure['email_verification_subject'] = array(
			'type' => 'text',
			'label' => 'Email Verification<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'validation' => array(
				'instance' => array()
			),
			'value' => isset($config->site->email->verification_subject) ? $config->site->email->verification_subject : ''
		);

		$form_structure['email_verification_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Email Verification<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->verification_text) ? $config->site->email->verification_text : ''
		);

		$form_structure['email_text_verification'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Email Text (Verification)',
			'tooltip' => 'This message will be sent to all new users that need to verify their email address after signing up.',
			'fieldset' => 'Email',
			'value' => $config->site->email_text_verification
		);

        $form_structure['email_verification_complete_subject'] = array(
			'type' => 'text',
			'label' => 'Verification Complete<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'validation' => array(
				'instance' => array()
			),
			'value' => isset($config->site->email->verification_complete_subject) ? $config->site->email->verification_complete_subject : ''
		);

		$form_structure['email_verification_complete_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Verification Complete<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->verification_complete_text) ? $config->site->email->verification_complete_text : ''
		);

        $form_structure['email_activation_subject'] = array(
			'type' => 'text',
			'label' => 'Account Pre-Activation<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'validation' => array(
				'instance' => array()
			),
			'value' => isset($config->site->email->activation_subject) ? $config->site->email->activation_subject : ''
		);

		$form_structure['email_activation_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Account Pre-Activation<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->activation_text) ? $config->site->email->activation_text : ''
		);

        $form_structure['email_activation_complete_subject'] = array(
			'type' => 'text',
			'label' => 'Activation Complete<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'validation' => array(
				'instance' => array()
			),
			'value' => isset($config->site->email->activation_complete_subject) ? $config->site->email->activation_complete_subject : ''
		);

		$form_structure['email_activation_complete_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Activation Complete<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->activation_complete_text) ? $config->site->email->activation_complete_text : ''
		);

        $form_structure['email_invitation_subject'] = array(
			'type' => 'text',
			'label' => 'Member Invitation<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->invitation_subject) ? $config->site->email->invitation_subject : ''
		);

		$form_structure['email_invitation_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Member Invitation<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->invitation_text) ? $config->site->email->invitation_text : ''
		);
			
        $form_structure['email_invitation_complete_subject'] = array(
			'type' => 'text',
			'label' => 'Welcome Email subject line (Sign Up)',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->invitation_complete_subject) ? $config->site->email->invitation_complete_subject : ''
		);

		$form_structure['email_invitation_complete_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Welcome Email Text (Sign Up)',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->invitation_complete_text) ? $config->site->email->invitation_complete_text : ''
		);
		
        $form_structure['email_enable_new_comment_emails'] = array(
			'type' => 'checkbox',
			'label' => 'Enable New Comment<br>Emails',
			'tooltip' => 'Sent to members receiving the new comment.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_new_comment_emails) ? $config->site->email->enable_new_comment_emails : ''
		);

        $form_structure['email_new_comment_subject'] = array(
			'type' => 'text',
			'label' => 'New Comment<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->new_comment_subject) ? $config->site->email->new_comment_subject : ''
		);

		$form_structure['email_new_comment_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'New Comment<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->new_comment_text) ? $config->site->email->new_comment_text : ''
		);

        $form_structure['email_enable_new_like_emails'] = array(
			'type' => 'checkbox',
			'label' => 'Enable New Like<br>Emails',
			'tooltip' => 'Sent to members receiving the new like. Max limit 1 per day.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_new_like_emails) ? $config->site->email->enable_new_like_emails : ''
		);

        $form_structure['email_new_like_subject'] = array(
			'type' => 'text',
			'label' => 'New Like<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->new_like_subject) ? $config->site->email->new_like_subject : ''
		);

		$form_structure['email_new_like_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'New Like<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->new_like_text) ? $config->site->email->new_like_text : ''
		);

        $form_structure['email_enable_new_follower_emails'] = array(
			'type' => 'checkbox',
			'label' => 'Enable New Follower<br>Emails',
			'tooltip' => 'Sent to members receiving the new follower.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_new_follower_emails) ? $config->site->email->enable_new_follower_emails : ''
		);

        $form_structure['email_new_follower_subject'] = array(
			'type' => 'text',
			'label' => 'New Follower<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->new_follower_subject) ? $config->site->email->new_follower_subject : ''
		);

		$form_structure['email_new_follower_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'New Follower<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->new_follower_text) ? $config->site->email->new_follower_text : ''
		);
		
        $form_structure['email_enable_image_reported_emails'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Media Flagged<br>Emails',
			'tooltip' => 'Sent to the 3 people -> Admin & Member that Flagged & Media Uploader.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_image_reported_emails) ? $config->site->email->enable_image_reported_emails : ''
		);

        $form_structure['email_image_reported_subject'] = array(
			'type' => 'text',
			'label' => 'Media Flagged<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->image_reported_subject) ? $config->site->email->image_reported_subject : ''
		);

		$form_structure['email_image_reported_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Media Flagged<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->image_reported_text) ? $config->site->email->image_reported_text : ''
		);
	
        $form_structure['email_enable_new_media_emails'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Media Flagged<br>Emails',
			'tooltip' => 'Sent to follower when following uploads new media. Max limit 1 per day.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_new_media_emails) ? $config->site->email->enable_new_media_emails : ''
		);

        $form_structure['email_new_media_subject'] = array(
			'type' => 'text',
			'label' => 'New Media Uploaded<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->new_media_subject) ? $config->site->email->new_media_subject : ''
		);

		$form_structure['email_new_media_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'New Media Uploaded<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->new_media_text) ? $config->site->email->new_media_text : ''
		);

        $form_structure['email_enable_activity_emails'] = array(
			'type' => 'checkbox',
			'label' => 'Enable Activity Feed<br>Emails',
			'tooltip' => 'Sent to members that have not signed in recently. More settings available in member page.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->enable_activity_emails) ? $config->site->email->enable_activity_emails : ''

		);

        $form_structure['email_activity_subject'] = array(
			'type' => 'text',
			'label' => 'Activity Feed<br>Subject Text',
			'tooltip' => 'Subject line of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->activity_subject) ? $config->site->email->activity_subject : ''
		);

		$form_structure['email_activity_text'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Activity Feed<br>Body Text',
			'tooltip' => 'Main content of email.',
			'fieldset' => 'Emails',
			'value' => isset($config->site->email->activity_text) ? $config->site->email->activity_text : ''
		);


		*/
 
         /* ANALYTICS */
        $form_structure['site_analytics'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'class' => 'input-textarea-small'
			),
			'label' => 'Analytics Code',
			'tooltip' => 'Google Analytics (Stats Tracking) Code',
			'fieldset' => 'Analytics',
			'value' => !empty($config->site->analytics) ? $config->site->analytics : ''
		);

		/* API KEYS */

		if($config->core->mode != MK_Core::MODE_DEMO)
		{

			// Bitly
			$form_structure['site_bitly_login_id'] = array(
				'type' => 'text',
				'label' => 'Bitly Login ID',
				'fieldset' => 'API Keys',
				'value' => $config->site->bitly->login_id
			);
	
			$form_structure['site_bitly_app_key'] = array(
				'type' => 'text',
				'label' => 'Bitly App Key',
				'fieldset' => 'API Keys',
				'value' => $config->site->bitly->app_key
			);
			
			$form_structure['site_bitly_enabled'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Enable Bit.ly',
				'tooltip' => 'Short links for social sharing? https://bitly.com/a/your_api_key',
				'fieldset' => 'API Keys',
				'value' => $config->site->bitly->enabled
			);

			// Soundcloud	
			$form_structure['site_soundcloud_app_id'] = array(
				'type' => 'text',
				'label' => 'Soundcloud App ID',
				'fieldset' => 'API Keys',
				'value' => $config->site->soundcloud->app_id
			);
			
			$form_structure['site_soundcloud_app_secret'] = array(
				'type' => 'text',
				'label' => 'Soundcloud Secret Key',
				'fieldset' => 'API Keys',
				'value' => $config->site->soundcloud->app_secret
			);
			
			$form_structure['site_soundcloud_enabled'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Enable Soundcloud',
				'tooltip' => 'Enable Soundcloud uploads? https://developers.soundcloud.com/',
				'fieldset' => 'API Keys',
				'value' => $config->site->soundcloud->enabled
			);

			// Facebook API
			$form_structure['site_facebook_app_id'] = array(
				'type' => 'text',
				'label' => 'Facebook App ID',
				'tooltip' => 'Needed for social login',
				'fieldset' => 'API Keys',
				'value' => $config->site->facebook->app_id
			);
	
			$form_structure['site_facebook_app_secret'] = array(
				'type' => 'text',
				'label' => 'Facebook App Secret',
				'tooltip' => 'Needed for social login',
				'fieldset' => 'API Keys',
				'value' => $config->site->facebook->app_secret
			);
			
			$form_structure['site_facebook_login'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Facebook Login',
				'tooltip' => 'Can users login to the site using their Facebook account?',
				'fieldset' => 'API Keys',
				'value' => $config->site->facebook->login
			);
			
			$form_structure['site_facebook_access_token'] = array(
				'type' => 'text',
				'label' => 'Facebook Access Token',
				'tooltip' => 'Needed to auto-post to your FB fan page.<br>Save the ID and Secret keys first!<br>Step 1) <a href="' . $config->site->url . 'library/fbAccessToken.php" target="_blank">Click here to generate a token</a>.<br>Step 2) Refresh this page',
				'fieldset' => 'API Keys',
				'value' => $config->site->facebook->access_token
			);

			$form_structure['site_facebook_page_id'] = array(
				'type' => 'text',
				'label' => 'Facebook Page ID',
				'tooltip' => 'Needed to auto-post to your FB fan page.<br>Step 3) <a href="https://graph.facebook.com/me/accounts?access_token=' . $config->site->facebook->access_token . '" target="_blank">Find your page ID here</a>  and paste above.<br>Step 4) Also copy & replace the ACCESS TOKEN above<br>with the one provided for that specific ID.<br>You must perform step 1 before step 2 will work!',
				'fieldset' => 'API Keys',
				'value' => $config->site->facebook->page_id
			);
						
			// Twitter API
			/*
            $form_structure['site_twitter_app_key'] = array(
				'type' => 'text',
				'label' => 'Twitter Consumer Key',
				'fieldset' => 'API Keys',
				'value' => $config->site->twitter->app_key
			);
	
			$form_structure['site_twitter_app_secret'] = array(
				'type' => 'text',
				'label' => 'Twitter Consumer Secret',
				'fieldset' => 'API Keys',
				'value' => $config->site->twitter->app_secret
			);
			
			$form_structure['site_twitter_login'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Twitter Login',
				'tooltip' => 'Can users login to the site using their Twitter account?',
				'fieldset' => 'API Keys',
				'value' => $config->site->twitter->login
			);*/

			// LinkedIn API
			$form_structure['site_linkedin_client_id'] = array(
				'type' => 'text',
				'label' => 'LinkedIn API Key',
				'fieldset' => 'API Keys',
				'value' => $config->site->linkedin->client_id
			);
	
			$form_structure['site_linkedin_client_secret'] = array(
				'type' => 'text',
				'label' => 'LinkedIn Secret Key',
				'fieldset' => 'API Keys',
				'value' => $config->site->linkedin->client_secret
			);
			
			$form_structure['site_linkedin_login'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'LinkedIn Login',
				'tooltip' => 'Can users login to the site using their LinkedIn account?',
				'fieldset' => 'API Keys',
				'value' => $config->site->linkedin->login
			);

			// Yahoo API
			$form_structure['site_yahoo_client_id'] = array(
				'type' => 'text',
				'label' => 'Yahoo Consumer Key',
				'fieldset' => 'API Keys',
				'value' => $config->site->yahoo->client_id
			);
	
			$form_structure['site_yahoo_client_secret'] = array(
				'type' => 'text',
				'label' => 'Yahoo Consumer Secret',
				'fieldset' => 'API Keys',
				'value' => $config->site->yahoo->client_secret
			);
			
			$form_structure['site_yahoo_login'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Yahoo Login',
				'tooltip' => 'Can users login to the site using their Yahoo account?',
				'fieldset' => 'API Keys',
				'value' => $config->site->yahoo->login
			);
	
			// Windows Live API
			$form_structure['site_windowslive_client_id'] = array(
				'type' => 'text',
				'label' => 'Windows Live Client ID',
				'fieldset' => 'API Keys',
				'value' => $config->site->windowslive->client_id
			);
	
			$form_structure['site_windowslive_client_secret'] = array(
				'type' => 'text',
				'label' => 'Windows Live Client Secret',
				'fieldset' => 'API Keys',
				'value' => $config->site->windowslive->client_secret
			);
			
			$form_structure['site_windowslive_login'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Windows Live Login',
				'tooltip' => 'Can users login using their Windows / Xbox Live account?',
				'fieldset' => 'API Keys',
				'value' => $config->site->windowslive->login
			);
	
			// Google API
			$form_structure['site_google_client_id'] = array(
				'type' => 'text',
				'label' => 'Google Client ID',
				'fieldset' => 'API Keys',
				'value' => $config->site->google->client_id
			);
	
			$form_structure['site_google_client_secret'] = array(
				'type' => 'text',
				'label' => 'Google Client Secret',
				'fieldset' => 'API Keys',
				'value' => $config->site->google->client_secret
			);
			
			$form_structure['site_google_login'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Google Login',
				'tooltip' => 'Can users login to the site using their Google account?',
				'fieldset' => 'API Keys',
				'value' => $config->site->google->login
			);

			$form_structure['site_google_api_key'] = array(
				'type' => 'text',
				'label' => 'Google Api Key for Youtube',
				'fieldset' => 'API Keys',
				'tooltip' => 'Setup Here: https://console.developers.google.com',
				'value' => $config->site->google->api_key
			);			

			// Wordpress API
			$form_structure['site_wordpress_client_id'] = array(
				'type' => 'text',
				'label' => 'Wordpress Client ID',
				'fieldset' => 'API Keys',
				'value' => $config->site->wordpress->client_id
			);
	
			$form_structure['site_wordpress_client_secret'] = array(
				'type' => 'text',
				'label' => 'Wordpress Client Secret',
				'fieldset' => 'API Keys',
				'value' => $config->site->wordpress->client_secret
			);
			
			$form_structure['site_wordpress_login'] = array(
				'type' => 'select',
				'options' => array(
					0 => 'No',
					1 => 'Yes'
				),
				'label' => 'Wordpress Login',
				'tooltip' => 'Can users login to the site using their Wordpress account?',
				'fieldset' => 'API Keys',
				'value' => $config->site->wordpress->login
			);

		}


 

		// Pull in custom component settings
		foreach($config->db->components as $component)
		{
			$module_settings = 'resources/components/'.$component.'/settings.php';
			if( is_file($module_settings) )
			{
				require_once $module_settings;
			}
		}
	
	
		
		/* DATABASE */
		if( $config->core->mode === MK_Core::MODE_FULL )
		{
			$form_structure['db_host'] = array(
				'type' => 'text',
				'label' => 'Host',
				'fieldset' => 'Database',
				'value' => $config->db->host
			);
			$form_structure['db_username'] = array(
				'type' => 'text',
				'label' => 'Username',
				'fieldset' => 'Database',
				'value' => $config->db->username
			);
			$form_structure['db_password'] = array(
				'type' => 'text',
				'label' => 'Password',
				'fieldset' => 'Database',
				'value' => $config->db->password,
				'attributes' => array(
					'type' => 'password'
				)
			);
			$form_structure['db_name'] = array(
				'type' => 'text',
				'label' => 'Database name',
				'fieldset' => 'Database',
				'value' => $config->db->name
			);
		}


		/* SAVE BUTTON */
		$form_structure['search_submit'] = array(
			'type' => 'submit',
			'attributes' => array(
				'value' => 'Save Changes'
			)
		);

		$handle = scandir('application/views');

		$components_core = array();
		$components_optional = array();
		foreach($handle as $template_folder)
		{
			if($template_folder!='.' && $template_folder!='..' && is_dir('application/views/'.$template_folder))
			{
				$template_details = simplexml_load_file('application/views/'.$template_folder.'/details.xml');
				$template_name = (string) $template_details->name;
				$form_structure['site_template']['options'][$template_name] = array();
				
				$theme_handle = scandir('application/views/'.$template_folder.'/themes');
				foreach( $theme_handle as $theme_folder )
				{
					if($theme_folder!='.' && $theme_folder!='..' && is_dir('application/views/'.$template_folder.'/themes/'.$theme_folder))
					{
						$theme_details = simplexml_load_file('application/views/'.$template_folder.'/themes/'.$theme_folder.'/details.xml');
						$form_structure['site_template']['options'][$template_name][$template_folder.'/'.$theme_folder] = (string) $theme_details->name.' - By '.(string) $theme_details->author;
					}
				}

			}
		}

		// Extract fieldsets for navigation tabs
		$fieldsets = array();
		foreach( $form_structure as $form_element_key => $form_element )
		{
			if( !empty($form_element['fieldset']) )
			{
				$fieldsets[MK_Utility::getSlug($form_element['fieldset'])] = $form_element['fieldset'];

				if( MK_Utility::getSlug($form_element['fieldset']) != $selected_fieldset )
				{
					unset($form_structure[$form_element_key]);
				}
			}
		}
		
		$this->getView()->fieldsets = $fieldsets;

		$form = new MK_Form($form_structure, $form_settings);

		if( $form->isSuccessful() )
		{
			$message = array();
			$config_data = array();
			
			$fields = $form->getFields();
			
			if($config->core->mode === MK_Core::MODE_FULL && $selected_fieldset == 'stylesheets-and-colors' )
			{
			
				$config_data['site.dev_mode'] = $form->getField('site_dev_mode')->getValue();

				if( $site_style_less = $form->getField('site_style_less')->getValue() )
				{
					file_put_contents(dirname(__FILE__).'/../../../css/style.less', $site_style_less, false);
				}

				if( $site_colors_less = $form->getField('site_colors_less')->getValue() )
				{
					file_put_contents(dirname(__FILE__).'/../../../css/colors.less', $site_colors_less, false);
				}

				if( $site_modal_less = $form->getField('site_modal_less')->getValue() )
				{
					file_put_contents(dirname(__FILE__).'/../../../css/modal.less', $site_modal_less, false);
				}

				if( $site_tablet_less = $form->getField('site_tablet_less')->getValue() )
				{
					file_put_contents(dirname(__FILE__).'/../../../css/tablet.less', $site_tablet_less, false);
				}
				
				if( $site_phone_less = $form->getField('site_phone_less')->getValue() )
				{
					file_put_contents(dirname(__FILE__).'/../../../css/phone.less', $site_phone_less, false);
				}
								
			}


			if( $selected_fieldset == 'languages' )
			{
				$config_data['site.languages.language'] = $form->getField('site_languages_language')->getValue();
				//$config_data['site.style.enable_languages_menu'] = $form->getField('site_style_enable_languages_menu')->getValue();

			}
						
			if($config->core->mode === MK_Core::MODE_FULL && $selected_fieldset == 'database' )
			{
				$config_data['db.host'] = $form->getField('db_host')->getValue();
				$config_data['db.name'] = $form->getField('db_name')->getValue();
				$config_data['db.username'] = $form->getField('db_username')->getValue();
				$config_data['db.password'] = $form->getField('db_password')->getValue();
			}

			if($config->core->mode != MK_Core::MODE_DEMO && $selected_fieldset == 'api-keys' )
			{
				$config_data['site.bitly.login_id'] = $form->getField('site_bitly_login_id')->getValue();
				$config_data['site.bitly.app_key'] = $form->getField('site_bitly_app_key')->getValue();
				$config_data['site.bitly.enabled'] = $form->getField('site_bitly_enabled')->getValue();
				$config_data['site.soundcloud.app_id'] = $form->getField('site_soundcloud_app_id')->getValue();
				$config_data['site.soundcloud.app_secret'] = $form->getField('site_soundcloud_app_secret')->getValue();
				$config_data['site.soundcloud.enabled'] = $form->getField('site_soundcloud_enabled')->getValue();
				$config_data['site.facebook.app_id'] = $form->getField('site_facebook_app_id')->getValue();
				$config_data['site.facebook.app_secret'] = $form->getField('site_facebook_app_secret')->getValue();
				$config_data['site.facebook.login'] = $form->getField('site_facebook_login')->getValue(); 
				
				$config_data['site.facebook.access_token'] = $form->getField('site_facebook_access_token')->getValue();    
				$config_data['site.facebook.page_id'] = $form->getField('site_facebook_page_id')->getValue();    
				
				$config_data['site.yahoo.client_id'] = $form->getField('site_yahoo_client_id')->getValue();
				$config_data['site.yahoo.client_secret'] = $form->getField('site_yahoo_client_secret')->getValue();
				$config_data['site.yahoo.login'] = $form->getField('site_yahoo_login')->getValue();
				$config_data['site.windowslive.client_id'] = $form->getField('site_windowslive_client_id')->getValue();
				$config_data['site.windowslive.client_secret'] = $form->getField('site_windowslive_client_secret')->getValue();
				$config_data['site.windowslive.login'] = $form->getField('site_windowslive_login')->getValue();
				$config_data['site.google.client_id'] = $form->getField('site_google_client_id')->getValue();
				$config_data['site.google.client_secret'] = $form->getField('site_google_client_secret')->getValue();
				$config_data['site.google.login'] = $form->getField('site_google_login')->getValue();
				$config_data['site.google.api_key'] = $form->getField('site_google_api_key')->getValue();
				$config_data['site.linkedin.client_id'] = $form->getField('site_linkedin_client_id')->getValue();
				$config_data['site.linkedin.client_secret'] = $form->getField('site_linkedin_client_secret')->getValue();
				$config_data['site.linkedin.login'] = $form->getField('site_linkedin_login')->getValue();
				$config_data['site.wordpress.client_id'] = $form->getField('site_wordpress_client_id')->getValue();
				$config_data['site.wordpress.client_secret'] = $form->getField('site_wordpress_client_secret')->getValue();
				$config_data['site.wordpress.login'] = $form->getField('site_wordpress_login')->getValue();

				/*$config_data['site.twitter.app_key'] = $form->getField('site_twitter_app_key')->getValue();
				$config_data['site.twitter.app_secret'] = $form->getField('site_twitter_app_secret')->getValue();
				$config_data['site.twitter.login'] = $form->getField('site_twitter_login')->getValue();
				*/
			}
            
            if( $selected_fieldset == 'analytics' )
			{
            
                $config_data['site.analytics'] = $form->getField('site_analytics')->getValue();

            }

            if( $selected_fieldset == 'media-page' )
			{

                $config_data['site.media.comments_type'] = $form->getField('site_media_comments_type')->getValue();
                $config_data['site.media.enable_stretched_image'] = $form->getField('site_media_enable_stretched_image')->getValue();
                $config_data['site.enable_guest_comments'] = $form->getField('site_enable_guest_comments')->getValue();
                $config_data['site.enable_guest_likes'] = $form->getField('site_enable_guest_likes')->getValue();
                $config_data['site.enable_reporting'] = $form->getField('site_enable_reporting')->getValue();
                $config_data['site.media.enable_autoplay'] = $form->getField('site_media_enable_autoplay')->getValue();
                $config_data['site.media.enable_exif'] = $form->getField('site_media_enable_exif')->getValue();
                $config_data['site.media.enable_source'] = $form->getField('site_media_enable_source')->getValue();
                $config_data['site.media.enable_view_original'] = $form->getField('site_media_enable_view_original')->getValue();
                $config_data['site.media.audio_player'] = $form->getField('site_media_audio_player')->getValue();
                $config_data['site.media.layout_style'] = $form->getField('site_media_layout_style')->getValue();
                /*$config_data['site.media.video_player'] = $form->getField('site_media_video_player')->getValue();*/
                /*$config_data['site.media.enable_page_lightbox'] = $form->getField('site_media_enable_page_lightbox')->getValue();*/

            }
            
            if( $selected_fieldset == 'wordpress' )
			{
                $config_data['site.wordpress.enable_post_to_wp'] = $form->getField('site_wordpress_enable_post_to_wp')->getValue();
                $config_data['site.wordpress.force_login'] = $form->getField('site_wordpress_force_login')->getValue();
                $config_data['site.wordpress.strict_login'] = $form->getField('site_wordpress_strict_login')->getValue();
				$config_data['site.wordpress.site_url'] = $form->getField('site_wordpress_site_url')->getValue();
				$config_data['site.wordpress.admin_username'] = $form->getField('site_wordpress_admin_username')->getValue();
				$config_data['site.wordpress.admin_password'] = $form->getField('site_wordpress_admin_password')->getValue();
				$config_data['site.wordpress.admin_id'] = $form->getField('site_wordpress_admin_id')->getValue();
				$config_data['site.wordpress.taxonomy_tags'] = $form->getField('site_wordpress_taxonomy_tags')->getValue();
				$config_data['site.wordpress.taxonomy_categories'] = $form->getField('site_wordpress_taxonomy_categories')->getValue();

			}
			
             if( $selected_fieldset == 'advertising' )
			{

                $config_data['site.ads.header_468x60'] = $form->getField('site_ads_header_468x60')->getValue();
                $config_data['site.ads.top_728x90'] = $form->getField('site_ads_top_728x90')->getValue();
                $config_data['site.ads.top_242x90'] = $form->getField('site_ads_top_242x90')->getValue();               
                $config_data['site.ads.top_970x90'] = $form->getField('site_ads_top_970x90')->getValue();
                $config_data['site.ads.top_980x120'] = $form->getField('site_ads_top_980x120')->getValue();
                $config_data['site.ads.sidebar_160x600'] = $form->getField('site_ads_sidebar_160x600')->getValue();
                $config_data['site.ads.sidebar_300x250'] = $form->getField('site_ads_sidebar_300x250')->getValue();          
                $config_data['site.ads.enable_header'] = $form->getField('site_ads_enable_header')->getValue();
                $config_data['site.ads.enable_home_top'] = $form->getField('site_ads_enable_home_top')->getValue();                
                $config_data['site.ads.enable_member_top'] = $form->getField('site_ads_enable_member_top')->getValue();                
                $config_data['site.ads.enable_member_sidebar'] = $form->getField('site_ads_enable_member_sidebar')->getValue();
                $config_data['site.ads.enable_members_top'] = $form->getField('site_ads_enable_members_top')->getValue();                
                $config_data['site.ads.enable_media_top'] = $form->getField('site_ads_enable_media_top')->getValue();                
                //$config_data['site.ads.enable_other_top'] = $form->getField('site_ads_enable_other_top')->getValue();                
                //$config_data['site.ads.enable_media_sidebar'] = $form->getField('site_ads_enable_media_sidebar')->getValue();
                //$config_data['site.ads.enable_home_sidebar'] = $form->getField('site_ads_enable_home_sidebar')->getValue();
                //$config_data['site.ads.enable_members_sidebar'] = $form->getField('site_ads_enable_members_sidebar')->getValue();
                //$config_data['site.ads.enable_blog_sidebar'] = $form->getField('site_ads_enable_blog_sidebar')->getValue();
                //$config_data['site.ads.enable_other_sidebar'] = $form->getField('site_ads_enable_other_sidebar')->getValue();   
                //$config_data['site.ads.enable_blog_top'] = $form->getField('site_ads_enable_blog_top')->getValue();                
                
            }
            
            if( $selected_fieldset == 'emails' )
			{
				$config_data['site.emails.ssl_enabled'] = $form->getField('site_emails_ssl_enabled')->getValue();
				$config_data['site.emails.ssl_server'] = $form->getField('site_emails_ssl_server')->getValue();
				$config_data['site.emails.ssl_username'] = $form->getField('site_emails_ssl_username')->getValue();
				$config_data['site.emails.ssl_password'] = $form->getField('site_emails_ssl_password')->getValue();

				$config_data['site.email_template'] = $form->getField('site_email_template')->getValue();
				$config_data['site.emails.registration_subject'] = $form->getField('site_emails_registration_subject')->getValue();
				$config_data['site.emails.registration_text'] = $form->getField('site_emails_registration_text')->getValue();
				$config_data['site.emails.registration_approval_notice'] = $form->getField('site_emails_registration_approval_notice')->getValue();
				$config_data['site.emails.registration_subject_admin'] = $form->getField('site_emails_registration_subject_admin')->getValue();
				$config_data['site.emails.registration_text_admin'] = $form->getField('site_emails_registration_text_admin')->getValue();
				$config_data['site.emails.registration_approval_notice_admin'] = $form->getField('site_emails_registration_approval_notice_admin')->getValue();
				$config_data['site.emails.approved_subject'] = $form->getField('site_emails_approved_subject')->getValue();
				$config_data['site.emails.approved_text'] = $form->getField('site_emails_approved_text')->getValue();
				/*
				$config_data['site.email.enable_admin_action_like'] = $form->getField('enable_admin_action_like')->getValue();
				$config_data['site.email.enable_admin_action_comment'] = $form->getField('enable_admin_action_comment')->getValue();
				$config_data['site.email.enable_admin_action_upload'] = $form->getField('enable_admin_action_upload')->getValue();
				$config_data['site.email.enable_admin_action_follow'] = $form->getField('enable_admin_action_follow')->getValue();
				$config_data['site.email.enable_admin_action_signup'] = $form->getField('enable_admin_action_signup')->getValue();
				$config_data['site.email.enable_admin_action_download'] = $form->getField('enable_admin_action_download')->getValue();
				$config_data['site.email.enable_admin_action_invitation'] = $form->getField('enable_admin_action_invitation')->getValue();
				$config_data['site.email.verification_subject'] = $form->getField('email_verification_subject')->getValue();
				$config_data['site.email.verification_text'] = $form->getField('email_verification_text')->getValue();
				//$config_data['site.email_text_verification'] = $form->getField('email_text_verfification')->getValue();
				$config_data['site.email.verification_complete_subject'] = $form->getField('email_verification_complete_subject')->getValue();
				$config_data['site.email.verification_complete_text'] = $form->getField('email_verification_complete_text')->getValue();
				$config_data['site.email.activation_subject'] = $form->getField('email_activation_subject')->getValue();
				$config_data['site.email.activation_text'] = $form->getField('email_activation_text')->getValue();
				$config_data['site.email.activation_complete_subject'] = $form->getField('email_activation_complete_subject')->getValue();
				$config_data['site.email.activation_complete_text'] = $form->getField('email_activation_complete_text')->getValue();
				$config_data['site.email.invitation_subject'] = $form->getField('email_invitation_subject')->getValue();
				$config_data['site.email.invitation_text'] = $form->getField('email_invitation_text')->getValue();
				$config_data['site.email.invitation_complete_subject'] = $form->getField('email_invitation_complete_subject')->getValue();
				$config_data['site.email.invitation_complete_text'] = $form->getField('email_invitation_complete_text')->getValue();
				
				//FROM ANYBODY
				$config_data['site.email.enable_new_comment_emails'] = $form->getField('email_enable_new_comment_emails')->getValue();
				$config_data['site.email.new_comment_subject'] = $form->getField('email_new_comment_subject')->getValue();
				$config_data['site.email.new_comment_text'] = $form->getField('email_new_comment_text')->getValue();

				$config_data['site.email.enable_new_like_emails'] = $form->getField('email_enable_new_like_emails')->getValue();
				$config_data['site.email.new_like_subject'] = $form->getField('email_new_like_subject')->getValue();
				$config_data['site.email.new_like_text'] = $form->getField('email_new_like_text')->getValue();

				$config_data['site.email.enable_new_follower_emails'] = $form->getField('enable_new_follower_emails')->getValue();
				$config_data['site.email.new_follower_subject'] = $form->getField('email_new_follower_subject')->getValue();
				$config_data['site.email.new_follower_text'] = $form->getField('email_new_follower_text')->getValue();

				//FROM MEMBERS YOU ARE FOLLOWING
				$config_data['site.email.enable_new_media_emails'] = $form->getField('email_enable_new_media_emails')->getValue();
				$config_data['site.email.new_media_subject'] = $form->getField('email_new_media_subject')->getValue();
				$config_data['site.email.new_media_text'] = $form->getField('email_new_media_text')->getValue();

				$config_data['site.email.enable_activity_emails'] = $form->getField('email_enable_activity_emails')->getValue();
				$config_data['site.email.activity_subject'] = $form->getField('email_activity_subject')->getValue();
				$config_data['site.email.activity_text'] = $form->getField('email_activity_text')->getValue();
				*/
				
			}

            if( $selected_fieldset == 'social-sharing' )
			{

            
                $config_data['site.social.image_wide'] = $form->getField('site_social_image_wide')->getValue();
                $config_data['site.social.image_square'] = $form->getField('site_social_image_square')->getValue();
                $config_data['site.social.twitter'] = $form->getField('site_social_twitter')->getValue();
                $config_data['site.social.enable_post_to_fb'] = $form->getField('site_social_enable_post_to_fb')->getValue();
                $config_data['site.social.fb_post_type'] = $form->getField('site_social_fb_post_type')->getValue();
                
            }

            if( $selected_fieldset == 'footer' )
			{
                $config_data['site.footer.enable_footer'] = $form->getField('site_footer_enable_footer')->getValue();
                $config_data['site.footer.twitter'] = $form->getField('site_footer_twitter')->getValue();
                $config_data['site.footer.facebook'] = $form->getField('site_footer_facebook')->getValue();
                $config_data['site.footer.instagram'] = $form->getField('site_footer_instagram')->getValue();
                $config_data['site.footer.flickr'] = $form->getField('site_footer_flickr')->getValue();
                $config_data['site.footer.google_plus'] = $form->getField('site_footer_google_plus')->getValue();
                $config_data['site.footer.youtube'] = $form->getField('site_footer_youtube')->getValue();
                $config_data['site.footer.vimeo'] = $form->getField('site_footer_vimeo')->getValue();
                $config_data['site.footer.blog'] = $form->getField('site_footer_blog')->getValue();
                $config_data['site.footer.pinterest'] = $form->getField('site_footer_pinterest')->getValue();
                //$config_data['site.footer.height'] = $form->getField('site_footer_height')->getValue();			
			}
			
			
            if( $selected_fieldset == 'header' )
			{

                $config_data['site.header.enable_page_loader'] = $form->getField('site_header_enable_page_loader')->getValue();
                $config_data['site.header.enable_header'] = $form->getField('site_header_enable_header')->getValue();
                $config_data['site.header.enable_bg_image'] = $form->getField('site_header_enable_bg_image')->getValue();
                $config_data['site.header.bg_image'] = $form->getField('site_header_bg_image')->getValue();
                $config_data['site.header.height'] = $form->getField('site_header_height')->getValue();
                $config_data['site.header.menu_position'] = $form->getField('site_header_menu_position')->getValue();    
                $config_data['site.style.emphasize_upload'] = $form->getField('site_style_emphasize_upload')->getValue();
                //$config_data['site.header.enable_fb_like_button'] = $form->getField('site_header_enable_fb_like_button')->getValue();
                //$config_data['site.header.combine_sign_in_up'] = $form->getField('site_header_combine_sign_in_up')->getValue();

            }


            if( $selected_fieldset == 'member-page' )
			{
                $config_data['site.default_avatar'] = $form->getField('site_default_avatar')->getValue();
                $config_data['site.members.enable_cover_photo'] = $form->getField('site_members_enable_cover_photo')->getValue();
                $config_data['site.members.default_cover_photo'] = $form->getField('site_members_default_cover_photo')->getValue();
                $config_data['site.members.enable_gender'] = $form->getField('site_members_enable_gender')->getValue();
                $config_data['site.members.enable_video'] = $form->getField('site_members_enable_video')->getValue();
                $config_data['site.members.enable_skills'] = $form->getField('site_members_enable_skills')->getValue();
                $config_data['site.members.enable_occupation'] = $form->getField('site_members_enable_occupation')->getValue();
                $config_data['site.members.enable_category'] = $form->getField('site_members_enable_category')->getValue();
                $config_data['site.members.enable_interests'] = $form->getField('site_members_enable_interests')->getValue();
                $config_data['site.members.enable_dob'] = $form->getField('site_members_enable_dob')->getValue();
                $config_data['site.members.enable_software'] = $form->getField('site_members_enable_software')->getValue();
                $config_data['site.members.enable_contact_form'] = $form->getField('site_members_enable_contact_form')->getValue();
                $config_data['site.members.enable_available'] = $form->getField('site_members_enable_available')->getValue();
                $config_data['site.members.enable_resume'] = $form->getField('site_members_enable_resume')->getValue();
                $config_data['site.members.enable_public_emails'] = $form->getField('site_members_enable_public_emails')->getValue();
                $config_data['site.members.enable_stats'] = $form->getField('site_members_enable_stats')->getValue();
                $config_data['site.members.enable_email_registration'] = $form->getField('site_members_enable_email_registration')->getValue();
                $config_data['site.members.enable_signup_notice'] = $form->getField('site_members_enable_signup_notice')->getValue();                
                $config_data['site.members.enable_approval'] = $form->getField('site_members_enable_approval')->getValue();
                $config_data['site.members.enable_unapproved_login'] = $form->getField('site_members_enable_unapproved_login')->getValue();
                $config_data['site.members.disable_registration'] = $form->getField('site_members_disable_registration')->getValue();
                $config_data['site.members.disable_uploads'] = $form->getField('site_members_disable_uploads')->getValue();

				//$config_data['site.members.activity_time'] = $form->getField('site_members_activity_time')->getValue();
                //$config_data['site.members.maximum_uploads'] = $form->getField('site_members_maximum_uploads')->getValue();
                //$config_data['site.members.enable_invitations'] = $form->getField('site_members_enable_invitations')->getValue();    
                //$config_data['site.members.enable_verification'] = $form->getField('site_members_enable_verification')->getValue();
         		//$config_data['site.members.enable_bg_image'] = $form->getField('site_members_enable_bg_image')->getValue();          		
            }

            if( $selected_fieldset == 'media-grid' )
			{

                $config_data['site.grid.thumbnail_style'] = $form->getField('site_grid_thumbnail_style')->getValue();       
                $config_data['site.grid.column_count'] = $form->getField('site_grid_column_count')->getValue();       
                $config_data['site.grid.margin'] = $form->getField('site_grid_margin')->getValue();       			
                $config_data['site.grid.type'] = $form->getField('site_grid_type')->getValue();
                $config_data['site.grid.enable_full_width'] = $form->getField('site_grid_enable_full_width')->getValue();
                $config_data['site.grid.items_per_page'] = $form->getField('site_grid_items_per_page')->getValue();
                $config_data['site.grid.pagination_type'] = $form->getField('site_grid_pagination_type')->getValue();
                $config_data['site.grid.enable_caption'] = $form->getField('site_grid_enable_caption')->getValue();           
                $config_data['site.grid.enable_stats'] = $form->getField('site_grid_enable_stats')->getValue();           
                $config_data['site.grid.hover_style'] = $form->getField('site_grid_hover_style')->getValue();                        
                $config_data['site.grid.hover_enable_icon'] = $form->getField('site_grid_hover_enable_icon')->getValue();                        
                //$config_data['site.grid.boximage_height'] = $form->getField('site_grid_boximage_height')->getValue();
                //$config_data['site.grid.crop_type'] = $form->getField('site_grid_crop_type')->getValue();           
                //$config_data['site.grid.lazyload'] = $form->getField('site_grid_lazyload')->getValue();           
                //$config_data['site.grid.hover_bgcolor'] = $form->getField('site_grid_hover_bgcolor')->getValue();                        
                                
            }

            if( $selected_fieldset == 'mobile-responsive' )
			{

                $config_data['site.mobile.enable_responsive_phone'] = $form->getField('site_mobile_enable_responsive_phone')->getValue();
                $config_data['site.mobile.enable_responsive_tablet'] = $form->getField('site_mobile_enable_responsive_tablet')->getValue();
                $config_data['site.mobile.disable_modals'] = $form->getField('site_mobile_disable_modals')->getValue();
                $config_data['site.mobile.items_per_page'] = $form->getField('site_mobile_items_per_page')->getValue();
  
            }

            if( $selected_fieldset == 'media-slider' )
			{


                $config_data['site.slider.layout_style'] = $form->getField('site_slider_layout_style')->getValue(); //Header Menu On Top, Header Menu On Bottom, Below Top Ad, Above Top Ad
                $config_data['site.slider.type'] = $form->getField('site_slider_type')->getValue();
                $config_data['site.slider.media_type'] = $form->getField('site_slider_media_type')->getValue();
                $config_data['site.slider.media_source'] = $form->getField('site_slider_media_source')->getValue();
                $config_data['site.slider.effect_owl'] = $form->getField('site_slider_effect_owl')->getValue();
                $config_data['site.slider.count'] = $form->getField('site_slider_count')->getValue();
                $config_data['site.slider.height'] = $form->getField('site_slider_height')->getValue();
                $config_data['site.slider.width'] = $form->getField('site_slider_width')->getValue();
                $config_data['site.slider.enable_autoplay'] = $form->getField('site_slider_enable_autoplay')->getValue();
                $config_data['site.slider.enable_navigation'] = $form->getField('site_slider_enable_navigation')->getValue();
                $config_data['site.slider.enable_video_play'] = $form->getField('site_slider_enable_video_play')->getValue();
                $config_data['site.slider.enable_dots'] = $form->getField('site_slider_enable_dots')->getValue();

                //$config_data['site.slider.enable_home'] = $form->getField('site_slider_enable_home')->getValue();
                //$config_data['site.slider.enable_media'] = $form->getField('site_slider_enable_media')->getValue();
                //$config_data['site.slider.enable_members'] = $form->getField('site_slider_enable_members')->getValue();
                //$config_data['site.slider.enable_member'] = $form->getField('site_slider_enable_member')->getValue();
                //$config_data['site.slider.enable_other'] = $form->getField('site_slider_enable_home')->getValue();
                //$config_data['site.slider.effect_ultimate'] = $form->getField('site_slider_effect_ultimate')->getValue();
                //$config_data['site.slider.theme_ultimate'] = $form->getField('site_slider_theme_ultimate')->getValue();
                
            }

            if( $selected_fieldset == 'media-carousel' )
			{

                $config_data['site.carousel.type'] = $form->getField('site_carousel_type')->getValue();
                $config_data['site.carousel.layout_style'] = $form->getField('site_carousel_layout_style')->getValue(); //Above Header, Above Ads, Below Ads, Above Footer
                $config_data['site.carousel.enable_fullscreen'] = $form->getField('site_carousel_enable_fullscreen')->getValue(); //Force Full Screen
                $config_data['site.carousel.media_source'] = $form->getField('site_carousel_media_source')->getValue();
                $config_data['site.carousel.media_type'] = $form->getField('site_carousel_media_type')->getValue();
                $config_data['site.carousel.column_count'] = $form->getField('site_carousel_column_count')->getValue(); 
                $config_data['site.carousel.count'] = $form->getField('site_carousel_count')->getValue();
                $config_data['site.carousel.margin'] = $form->getField('site_carousel_margin')->getValue();       			
                $config_data['site.carousel.thumbnail_style'] = $form->getField('site_carousel_thumbnail_style')->getValue();       
                $config_data['site.carousel.enable_autoplay'] = $form->getField('site_carousel_enable_autoplay')->getValue();
                $config_data['site.carousel.enable_navigation'] = $form->getField('site_carousel_enable_navigation')->getValue();
                $config_data['site.carousel.enable_video_play'] = $form->getField('site_carousel_enable_video_play')->getValue();
                $config_data['site.carousel.enable_dots'] = $form->getField('site_carousel_enable_dots')->getValue();

				/*
                $config_data['site.carousel.enable_home'] = $form->getField('site_carousel_enable_home')->getValue();
                $config_data['site.carousel.enable_media'] = $form->getField('site_carousel_enable_media')->getValue();
                $config_data['site.carousel.enable_members'] = $form->getField('site_carousel_enable_members')->getValue();
                $config_data['site.carousel.enable_member'] = $form->getField('site_carousel_enable_member')->getValue();
                $config_data['site.carousel.enable_blog'] = $form->getField('site_carousel_enable_home')->getValue();
				*/   
                
            }

            if( $selected_fieldset == 'theme-options' )
			{

                $config_data['site.style.enable_full_width'] = $form->getField('site_style_enable_full_width')->getValue();
                $config_data['site.style.enable_forced_login'] = $form->getField('site_style_enable_forced_login')->getValue();
                $config_data['site.style.modal_effect'] = $form->getField('site_style_modal_effect')->getValue(); //heart or star
                $config_data['site.style.loading'] = $form->getField('site_style_loading')->getValue(); //heart or star
                $config_data['site.style.icon_like'] = $form->getField('site_style_icon_like')->getValue(); //heart or star
				$config_data['site.members.sort_by'] = $form->getField('site_members_sort_by')->getValue();
				$config_data['site.media.enable_approval'] = $form->getField('site_media_enable_approval')->getValue();
                $config_data['site.media.enable_images'] = $form->getField('site_media_enable_images')->getValue();
                $config_data['site.media.enable_videos'] = $form->getField('site_media_enable_videos')->getValue();
                $config_data['site.media.enable_audio'] = $form->getField('site_media_enable_audio')->getValue();  
                $config_data['site.style.enable_cookies_notification'] = $form->getField('site_style_enable_cookies_notification')->getValue();                  
                $config_data['site.media.max_filesize'] = $form->getField('site_media_max_filesize')->getValue();
                $config_data['site.header.enable_search'] = $form->getField('site_header_enable_search')->getValue();
                
				//$config_data['site.style.primary_color'] = $form->getField('site_style_primary_color')->getValue();
                //$config_data['site.style.secondary_color'] = $form->getField('site_style_secondary_color')->getValue();
                //$config_data['site.style.stroke_color'] = $form->getField('site_style_stroke_color')->getValue(); //Default Medium Grey
                //$config_data['site.style.box_radius'] = $form->getField('site_style_box_radius')->getValue();
                //$config_data['site.style.box_shadow'] = $form->getField('site_style_box_shadow')->getValue();
                //$config_data['site.style.button_radius'] = $form->getField('site_style_button_radius')->getValue();
                //$config_data['site.style.button_shadow'] = $form->getField('site_style_button_shadow')->getValue();
                //$config_data['site.style.enable_bg_image'] = $form->getField('site_style_enable_bg_image')->getValue();                $config_data['site.style.enable_google_fonts'] = $form->getField('site_style_enable_google_fonts')->getValue();
                //$config_data['site.style.google_font'] = $form->getField('site_style_google_font')->getValue();
                //$config_data['site.style.enable_captchas'] = $form->getField('site_style_enable_captchas')->getValue();
    
            }

			if( $selected_fieldset == 'watermark' )
			{

                $config_data['site.media.enable_watermark'] = $form->getField('site_media_enable_watermark')->getValue();
                $config_data['site.media.watermark'] = $form->getField('site_media_watermark')->getValue();
                $config_data['site.media.watermark_scale'] = $form->getField('site_media_watermark_scale')->getValue();
                $config_data['site.media.watermark_position'] = $form->getField('site_media_watermark_position')->getValue();
			
			}

            if( $selected_fieldset == 'performance' )
			{

                $config_data['site.style.enable_cdn'] = $form->getField('site_style_enable_cdn')->getValue();  
                $config_data['site.style.enable_minified'] = $form->getField('site_style_enable_minified')->getValue();  
                $config_data['site.style.enable_cached_headers'] = $form->getField('site_style_enable_cached_headers')->getValue();  
                $config_data['site.media.jpg_quality'] = $form->getField('site_media_jpg_quality')->getValue();  
                $config_data['site.media.jpg_quality_single'] = $form->getField('site_media_jpg_quality_single')->getValue();  
                $config_data['site.media.png_compression'] = $form->getField('site_media_png_compression')->getValue();  
				$config_data['site.values.site_width_calc'] = $form->getField('site_values_site_width_calc')->getValue();
				$config_data['site.error_reporting'] = $form->getField('site_error_reporting')->getValue();
			
			}
           
            if( $selected_fieldset == 'default-values' )
			{
        		$config_data['site.values.width_single_image'] = $form->getField('site_values_width_single_image')->getValue();
				$config_data['site.values.height_single_image'] = $form->getField('site_values_height_single_image')->getValue();
				$config_data['site.values.width_carousel_image'] = $form->getField('site_values_width_carousel_image')->getValue();
				$config_data['site.values.height_carousel_image'] = $form->getField('site_values_height_carousel_image')->getValue();
				$config_data['site.values.width_comments_avatar_image'] = $form->getField('site_values_width_comments_avatar_image')->getValue();
				$config_data['site.values.height_comments_avatar_image'] = $form->getField('site_values_height_comments_avatar_image')->getValue();
				$config_data['site.values.width_member_banner'] = $form->getField('site_values_width_member_banner')->getValue();
				$config_data['site.values.height_member_banner'] = $form->getField('site_values_height_member_banner')->getValue();
				$config_data['site.values.width_image_box'] = $form->getField('site_values_width_image_box')->getValue();
				$config_data['site.values.height_image_box'] = $form->getField('site_values_height_image_box')->getValue();
				$config_data['site.values.width_main_carousel_image'] = $form->getField('site_values_width_main_carousel_image')->getValue();
				$config_data['site.values.height_main_carousel_image'] = $form->getField('site_values_height_main_carousel_image')->getValue();
			}

            if( $selected_fieldset == 'members-page' )
			{
                $config_data['site.members.sort_style'] = $form->getField('site_members_sort_grid')->getValue();
                $config_data['site.members.box_style'] = $form->getField('site_members_box_style')->getValue();

			}
			            
			if( $selected_fieldset == 'amg-site-settings' )
			{
				
                $config_data['site.mode'] = $form->getField('site_mode')->getValue();
				$config_data['site.name'] = $form->getField('site_name')->getValue();
                $config_data['site.caption'] = $form->getField('site_caption')->getValue();
				$config_data['site.title'] = $form->getField('site_title')->getValue();
                $config_data['site.desc'] = $form->getField('site_desc')->getValue();
                $config_data['site.enable_tracking'] = $form->getField('site_enable_tracking')->getValue();
				$config_data['user.timeout'] = $form->getField('user_timeout')->getValue();
				$config_data['site.email'] = $form->getField('site_email')->getValue();
				$config_data['site.timezone'] = $form->getField('site_timezone')->getValue();
				$config_data['site.url'] = $form->getField('site_url')->getValue();
				$config_data['site.logo'] = $form->getField('site_logo')->getValue();
                $config_data['site.logo_sticky'] = $form->getField('site_logo_sticky')->getValue();
                $config_data['site.logo_modal'] = $form->getField('site_logo_modal')->getValue();
				$config_data['site.date_format'] = $form->getField('site_date_format')->getValue();
				$config_data['site.time_format'] = $form->getField('site_time_format')->getValue();
				$config_data['site.template'] = $form->getField('site_template')->getValue();				
                $config_data['site.google_site_verification'] = $form->getField('site_google_site_verification')->getValue();
				/*
				$valid_file_extensions = explode(',', $form->getField('site_valid_file_extensions')->getValue());
				$valid_file_extensions = array_map('trim', $valid_file_extensions);
				$valid_file_extensions = array_filter($valid_file_extensions);
				$valid_file_extensions = array_unique($valid_file_extensions);
				$config_data['site.valid_file_extensions'] = implode(',', $valid_file_extensions);
				*/
                //$config_data['site.path'] = $form->getField('site_path')->getValue();
				//$config_data['site.log_actions'] = $form->getField('site_log_actions')->getValue();

			}

			foreach($fields as $field_key => $field_value)
			{
				$field_value_elements = explode('-', $field_key);
				$config_area = array_shift($field_value_elements);
				$config_section = array_shift($field_value_elements);
				$config_node = array_shift($field_value_elements);

				if($config_area === 'extensions')
				{
					$config_data["extensions.".$config_section.".".$config_node] = $field_value->getValue();
				}
			}

			if($config->core->mode === MK_Core::MODE_DEMO)
			{
				$messages[] = new MK_Message('warning', 'Settings cannot be updated as <strong>'.$config->instance->name.'</strong> is running in demonstration mode.');
			}
			else
			{
				$messages[] = new MK_Message('success', 'Your settings have been updated. <a href="'.$this->getView()->uri( array('controller' => 'dashboard', 'section' => 'settings', 'form' => $selected_fieldset) ).'">Make more changes</a> or <a href="'.$this->getView()->uri( array('controller' => 'dashboard', 'section' => 'index') ).'">return to the dashboard</a>.');
				MK_Utility::writeConfig($config_data);
			}

			$this->getView()->messages = $messages;
			$this->getView()->setDisplayPath('dashboard/settings-processed');		
		}
		else
		{
			$this->getView()->form = $form->render();
		}
		
	}

}

?>