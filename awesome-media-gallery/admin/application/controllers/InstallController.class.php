<?php

require_once 'DefaultController.class.php';

class MK_InstallController extends MK_DefaultController
{
	
	public function _init()
	{
		parent::_init();
		$this->getView()->setTemplatePath('small');
	
		$this->getView()->getHead()->prependTitle( 'Install' );

		$config = MK_Config::getInstance();
		$session = MK_Session::getInstance();

		$step_1_complete = ( !empty( $session->install['db.host'] ) && !empty( $session->install['db.username'] ) && !empty( $session->install['db.name'] ) && !empty( $session->install['db.components'] ) );
		$step_2_complete = ( !empty( $session->install['site.name'] ) && !empty( $session->install['site.email'] ) && !empty( $session->install['site.url'] ) );
		$step_3_complete = ( !empty( $session->user['display_name'] ) && !empty( $session->user['email'] ) && !empty( $session->user['password'] ) );

		if( $config->site->installed )
		{
			$this->getView()->redirect(array('controller' => 'dashboard', 'section' => 'index'));
		}
		elseif( MK_Request::getParam('section') === 'step-2' && (!$step_1_complete) )
		{
			$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-1'));
		}
		elseif( MK_Request::getParam('section') === 'step-3' && (!$step_2_complete || !$step_1_complete) )
		{
			$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-2'));
		}
		elseif( ( MK_Request::getParam('section') === 'step-4' || MK_Request::getParam('section') === 'finished' ) && (!$step_3_complete || !$step_2_complete || !$step_1_complete) )
		{
			$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-3'));
		}

	}

	public function sectionIndex(){
		
		$session = MK_Session::getInstance();
		unset($session->install, $session->user);
		$session->install = array();

		$this->getView()->getHead()->prependTitle( 'Requirements' );
		
		$messages = array();
		
		$messages[] = new MK_Message( (phpversion() >= 5.2 ? 'success' : 'warning'), '<strong>PHP 5.2</strong> or above' );
		$messages[] = new MK_Message( (is_writable('../css/style.less') ? 'success' : 'warning'), 'The permissions of the file "<strong>../css/style.less</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('../css/colors.less') ? 'success' : 'warning'), 'The permissions of the file "<strong>../css/colors.less</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('../css/modal.less') ? 'success' : 'warning'), 'The permissions of the file "<strong>../css/modal.less</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('../css/phone.less') ? 'success' : 'warning'), 'The permissions of the file "<strong>../css/phone.less</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('../css/tablet.less') ? 'success' : 'warning'), 'The permissions of the file "<strong>../css/tablet.less</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('../tpl/uploads/') ? 'success' : 'warning'), 'The permissions of folder "<strong>../tpl/uploads/</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('../tpl/uploads/thumbnail/') ? 'success' : 'warning'), 'The permissions of folder "<strong>../tpl/uploads/thumbnail/</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('../tpl/img/thumbs/') ? 'success' : 'warning'), 'The permissions of folder "<strong>../tpl/img/thumbs/</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('resources/backups/') ? 'success' : 'warning'), 'The permissions of folder "<strong>resources/backups/</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('resources/restore/') ? 'success' : 'warning'), 'The permissions of folder "<strong>resources/restore/</strong>" set to <strong>0777</strong>' );
		$messages[] = new MK_Message( (is_writable('config.ini.php') ? 'success' : 'warning'), 'The permissions of file "<strong>config.ini.php</strong>" set to <strong>0777</strong>' );
        $messages[] = new MK_Message( (is_writable('../css/') ? 'success' : 'warning'), 'The permissions of folder "<strong>../css/</strong>" set to <strong>0777</strong>' );
        $messages[] = new MK_Message( (is_writable('../css/style.css') ? 'success' : 'warning'), 'The permissions of file "<strong>../css/style.css</strong>" set to <strong>0777</strong>' );
		
		$this->getView()->messages = $messages;
	}
	
	public function sectionStep1(){

		$session = MK_Session::getInstance();
		$config = MK_Config::getInstance();

		$this->getView()->getHead()->prependTitle( 'Step 1' );

		$components = MK_ComponentManager::getComponents( MK_ComponentManager::TYPE_OPTIONAL );

		$components_core = MK_ComponentManager::getComponents( MK_ComponentManager::TYPE_CORE );
		$components_optional = MK_ComponentManager::getComponents( MK_ComponentManager::TYPE_OPTIONAL );
		
		$_components_all = array();
		$components_all = array_merge($components_core, $components_optional);
		
		foreach( $components_all as $_component_key => $_component )
		{
			unset($_component['assets']);
			$_components_all[$_component_key] = $_component;
		}

        $host     = !empty( $config->db->host ) ? $config->db->host : 'localhost';
        $name     = !empty( $config->db->name ) ? $config->db->name : '';
        $username = !empty( $config->db->username ) ? $config->db->username : '';
        $password = !empty( $config->db->password ) ? $config->db->password : '';

		$form_structure = array(
			'db_host' => array(
				'label' => 'Host',
				'value' => !empty( $session->install['db.host'] ) ? $session->install['db.host'] : $host,
				'validation' => array(
					'instance' => array()
				),
				'attributes' => array(
					'autofocus' => 'autofocus'
				)
			),
			'db_username' => array(
				'label' => 'Username',
				'value' => !empty( $session->install['db.username'] ) ? $session->install['db.username'] : $username,
				'validation' => array(
					'instance' => array()
				)
			),
			'db_password' => array(
				'label' => 'Password',
				'value' => !empty( $session->install['db.password'] ) ? $session->install['db.password'] : $password,
				'attributes' => array(
					'type' => 'password'
				)
			),
			'db_prefix' => array(
				'label' => 'Table Prefix',
				'value' => !empty( $session->install['db.prefix'] ) ? $session->install['db.prefix'] : 'mk_',
				'tooltip' => 'If Mokoala is already installed on this database then change this.',
				'validation' => array(
					'instance' => array()
				)
			),
			'db_name' => array(
				'label' => 'Database Name',
				'value' => !empty( $session->install['db.name'] ) ? $session->install['db.name'] : $name,
				'validation' => array(
					'instance' => array()
				)
			),
			'db_components' => array(
				'label' => 'Components',
				'type' => 'checkbox-multiple',
				'value' => !empty( $session->install['db.components'] ) ? $session->install['db.components'] : '',
				'options' => $_components_all
			),
			'next_2' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => 'Step 2 &raquo;'
				)
			)
		);

		$form_settings = array(
			'attributes' => array(
				'class' => 'standard clear-fix small '.$config->core->mode
			)
		);
		
		$form = new MK_Form($form_structure, $form_settings);
		
		if($form->isSuccessful())
		{
			try
			{
				MK_Database::connect(MK_Database::DBMS_MYSQL, $form->getField('db_host')->getValue(), $form->getField('db_username')->getValue(), $form->getField('db_password')->getValue(), $form->getField('db_name')->getValue());

				try
				{
					$database_prefix = MK_Database::getInstance()->quote( $form->getField('db_prefix')->getValue().'%' );
					$database_records_tables = MK_Database::getInstance()->prepare("SHOW TABLES LIKE $database_prefix");
					$database_records_tables->execute();
	
					$res_total_records = $database_records_tables->fetchAll( PDO::FETCH_ASSOC );

					if( count($res_total_records) > 0 )
					{
						$form->getField('db_name')->getValidator()->addError("There are already tables present in this database, with the prefix \"<strong>".$form->getField('db_prefix')->getValue()."</strong>\", which could cause conflicts.");
					}
					else
					{
						$session->install = array_merge_replace(
							(array) $session->install,
							array(
								'db.host' => $form->getField('db_host')->getValue(),
								'db.username' => $form->getField('db_username')->getValue(),
								'db.password' => $form->getField('db_password')->getValue(),
								'db.name' => $form->getField('db_name')->getValue(),
								'db.prefix' => $form->getField('db_prefix')->getValue(),
								'db.components' => $form->getField('db_components')->getValue()
							), 1
						);
						$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-2'));
					}
				}
				catch( Exception $e )
				{
					$form->getField('db_name')->getValidator()->addError("There are already tables present on this database, which could cause conflicts.");
				}

			}
			catch( Exception $e )
			{
				$form->getField('db_host')->getValidator()->addError("Could not connect to host/database using details provided.");
			}
		}

		$this->getView()->install_form = $form->render();

	}
	
	public function sectionStep2(){
		$session = MK_Session::getInstance();
		$config = MK_Config::getInstance();
		
		$this->getView()->getHead()->prependTitle( 'Step 2' );

        
        if ( !empty ( $config->site->url ) ) {
        
            $site_url = $config->site->url;
        
        } else {
        
		$site_url = explode( '/', $config->site->base_href );
		array_pop($site_url); array_pop($site_url);
		$site_url = implode('/', $site_url).'/';
        
        }
		

		$form_structure = array(
			'site_name' => array(
				'label' => 'Site Name',
				'value' => !empty( $session->install['site.name'] ) ? $session->install['site.name'] : '',
				'validation' => array(
					'length' => array(2, 64)
				),
				'attributes' => array(
					'autofocus' => 'autofocus'
				)
			),
			'site_url' => array(
				'label' => 'Site URL',
				'value' => !empty( $session->install['site.url'] ) ? $session->install['site.url'] : $site_url,
				'validation' => array(
					'instance' => array(),
					'url' => array()
				)
			),
			'site_email' => array(
				'label' => 'Admin Email',
				'value' => !empty( $session->install['site.email'] ) ? $session->install['site.email'] : '',
				'validation' => array(
					'instance' => array(),
					'email' => array()
				)
			),
			'next_3' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => 'Step 3 &raquo;'
				)
			),
			'prev_1' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => '&laquo; Step 1'
				)
			)
		);

		$form_settings = array(
			'attributes' => array(
				'class' => 'standard clear-fix small'
			)
		);
		
		$form = new MK_Form($form_structure, $form_settings);
		
		if($form->getField('prev_1')->isSubmitted()){
			$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-1'));
		}elseif($form->isSuccessful()){
			$session->install = array_merge_replace(
				(array) $session->install,
				array(
					'site.name' => $form->getField('site_name')->getValue(),
					'site.email' => $form->getField('site_email')->getValue(),
					'site.url' => $form->getField('site_url')->getValue()
				)
			);

			$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-3'));
		}

		$this->getView()->install_form = $form->render();

	}

	public function sectionStep3(){
		$session = MK_Session::getInstance();
		$config = MK_Config::getInstance();

		$this->getView()->getHead()->prependTitle( 'Step 3' );

		$form_structure = array(
			'user_display_name' => array(
				'label' => 'Display Name',
				'value' => !empty( $session->user['display_name'] ) ? $session->user['display_name'] : '',
				'validation' => array(
					'length' => array(2, 64)
				),
				'attributes' => array(
					'autofocus' => 'autofocus'
				)
			),
			'user_email' => array(
				'label' => 'Email Address',
				'value' => !empty( $session->user['email'] ) ? $session->user['email'] : $session->install['site.email'],
				'validation' => array(
					'email' => array()
				)
			),
			'user_password' => array(
				'label' => 'Password',
				'value' => !empty( $session->user['password'] ) ? $session->user['password'] : '',
				'attributes' => array(
					'type' => 'password'
				),
				'validation' => array(
					'length' => array(4, 16)
				)
			),
			'next_4' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => 'Step 4 &raquo;'
				)
			),
			'prev_2' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => '&laquo; Step 2'
				)
			)
		);

		$form_settings = array(
			'attributes' => array(
				'class' => 'standard clear-fix small'
			)
		);
		
		$form = new MK_Form($form_structure, $form_settings);
		
		if($form->getField('prev_2')->isSubmitted()){
			$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-2'));
		}elseif($form->isSuccessful()){
			$session->user = array(
				'display_name' => $form->getField('user_display_name')->getValue(),
				'email' => $form->getField('user_email')->getValue(),
				'password' => $form->getField('user_password')->getValue()
			);

			MK_Utility::writeConfig($session->install);
			$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-4'));
		}
		
		$this->getView()->install_form = $form->render();

	}

	public function sectionStep4(){
		$session = MK_Session::getInstance();

		$this->getView()->getHead()->prependTitle( 'Step 4' );

		$form_structure = array(
			'next_finish' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => 'Finish &raquo;'
				)
			),
			'prev_3' => array(
				'type' => 'submit',
				'attributes' => array(
					'value' => '&laquo; Step 3'
				)
			)
			
		);

		$form_settings = array(
			'attributes' => array(
				'class' => 'standard clear-fix small'
			)
		);
		
		$form = new MK_Form($form_structure, $form_settings);
		
		if($form->getField('prev_3')->isSubmitted())
		{
			$this->getView()->redirect(array('controller' => 'install', 'section' => 'step-3'));
		}
		elseif($form->isSuccessful())
		{

			$config = MK_Config::getInstance();

			// Install Components
			foreach($session->install['db.components'] as $component)
			{
				$details = MK_ComponentManager::installComponent( $component, true );
			}

			$this->getView()->redirect(array('controller' => 'install', 'section' => 'finished'));

		}

		$this->getView()->install_form = $form->render();

	}

	public function sectionFinished(){
		$session = MK_Session::getInstance();

		$group_module = MK_RecordModuleManager::getFromType('user_group');
		$search_group = array(
			array('field' => 'admin', 'value' => '1')
		);
		
		$admin_group = $group_module->searchRecords($search_group);
		$admin_group = array_pop( $admin_group );

		$user_module = MK_RecordModuleManager::getFromType('user');
		$new_user = MK_RecordManager::getNewRecord( $user_module->getId() );
		$new_user
			->setDisplayName( $session->user['display_name'] )
			->setEmail( $session->user['email'] )
			->setGroup( $admin_group->getId() )
			->setPassword( $session->user['password'] )
			->isEmailVerified(1)
			->save();
		
		$session->login = $new_user->getId();

		$session->install = array(
			'site.installed' => '1'
		);

		MK_Utility::writeConfig($session->install);

		$this->getView()->getHead()->prependTitle( 'Finished' );

		unset($session->user, $session->install);
	}

}

?>