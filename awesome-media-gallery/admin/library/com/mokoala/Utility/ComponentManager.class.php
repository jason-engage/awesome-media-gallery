<?php

class MK_ComponentManager
{

	const TYPE_CORE = 'core';
	const TYPE_OPTIONAL = 'optional';

	public static function installComponent( $component, $force = false )
	{
		$config = MK_Config::getInstance();

		$installed_components = (array) $config->db->components;

		$base_directory = dirname(__FILE__).'/../../../../resources/components';
		
		if( $force == false && in_array($component, $installed_components) )
		{
			throw new Exception("The component '$component' is already installed.");
		}
		
		$component_directory = $base_directory.'/'.$component;

		if( !is_dir($component_directory) )
		{
			throw new Exception("The component '$component' doesn't exist.");
		}

		$component_details = file_get_contents($component_directory.'/details.json');
		$component_details = json_decode($component_details, true);

		// Run SQL queries
		$sql_queries = array();
		$sql_file = $component_directory.'/mysql.sql';
		if(is_file($sql_file))
		{
			$sql_file = file_get_contents($sql_file);
			$sql_file = str_replace('{db.prefix}', $config->db->prefix, $sql_file);
			$sql_queries = array_merge($sql_queries, MK_Utility::SQLSplit($sql_file));

			foreach($sql_queries as $query)
			{
				if( empty($query) )
				{
					continue;
				}

				MK_Database::getInstance()->query($query);
			}
		}

		// Run install script
		$install_file = $component_directory.'/install.php';
		if(is_file($install_file))
		{
			require_once $install_file;
		}

		$installed_components[] = $component;
		$installed_components = array_filter($installed_components);
		sort($installed_components);

		$config_update = array(
			'db.components' => $installed_components
		);

		MK_Config::loadConfig(array(
			'db' => array(
				'components' => $installed_components
			)
		));

		MK_Utility::writeConfig($config_update);

		return $component_details;
	}

	public static function getComponents( $type = null )
	{
		$config = MK_Config::getInstance();
		$installed_components = (array) $config->db->components;

		$base_directory = dirname(__FILE__).'/../../../../resources/components';

		$handle = scandir($base_directory);

		$components_core = array();
		$components_optional = array();

		foreach($handle as $filefolder)
		{
			if( $filefolder != '.' && $filefolder != '..' && is_dir($base_directory.'/'.$filefolder) )
			{
				$component_details_json = file_get_contents($base_directory.'/'.$filefolder.'/details.json');

				$component_details = json_decode($component_details_json, true);

				$data = array(
					'assets' => !empty($component_details['assets']) ? $component_details['assets'] : array(),
					'id' => (integer) !empty($component_details['id']) ? $component_details['id'] : 0,
					'version' => (float) $component_details['version'],
					'title' => (string) $component_details['name'],
					'folder' => 'resources/components/'.$filefolder.'/'
				);

				$checked = (boolean) $component_details['core'];
				
				if( in_array( $filefolder, $installed_components ) )
				{
					$data['disabled'] = 'disabled';
					$data['checked'] = 'checked';
				}

				if($checked || $config->core->mode !== MK_Core::MODE_FULL)
				{
					$data['checked'] = 'checked';
					$data['forced'] = 'forced';
				}

				if($checked)
				{
					$components_core[$filefolder] = $data;
				}
				else
				{
					$components_optional[$filefolder] = $data;
				}
				
			}
			
		}
		
		if( $type == self::TYPE_CORE )
		{
			return $components_core;
		}
		elseif( $type == self::TYPE_OPTIONAL )
		{
			return $components_optional;
		}
		else
		{
			return array_merge($components_core, $components_optional);
		}
	}
	
}

?>