<?php

require_once dirname(__FILE__).'/../../library/com/mokoala/Core/Controller.class.php';

class MK_DefaultController extends MK_Controller{


	protected function _init()
	{
		parent::_init();
		$this->_initNavigation();

		$config = MK_Config::getInstance();
		$this->getView()->getHead()->setBase( $config->site->base_href );
		$this->getView()->getHead()->prependTitle( $config->instance->name );

		$components = MK_ComponentManager::getComponents();
		foreach($components as $component)
		{
			if( !empty($component['assets']) )
			{
				foreach( $component['assets'] as $asset )
				{
					if( $asset['type'] == 'css' )
					{
						$this->getView()->getHead()->setLink(array(
							'type' => 'text/css',
							'media' => 'screen',
							'rel' => 'stylesheet',
							'href' => $component['folder'].$asset['src'],
						));
					}
				}
			}
		}
	}

	protected function _initNavigation()
	{

		$config = MK_Config::getInstance();
		
		$html = '';
		if( MK_Database::isConnected() && $config->site->installed )
		{
			$module_module = MK_RecordModuleManager::getFromType('module');

			$search_options = array(
				array('field' => 'parent_module', 'value' => 0),
				array('field' => 'hidden', 'value' => 0)
			);

			$modules = $module_module->searchRecords($search_options);

			foreach($modules as &$module)
			{
				$search_options = array(
					array('field' => 'parent_module', 'value' => $module->getId()),
					array('field' => 'hidden', 'value' => 0)
				);
				$module->setSubModules( $module_module->searchRecords($search_options) );
			}

			$this->getView()->modules = $modules;
		}
	
	}

}

?>