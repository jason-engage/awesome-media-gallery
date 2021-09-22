<?php

/**
 * Manages access to modules
 * 
 * @author Matt Lowden
 * @version 1.0
 *
 */

class MK_RecordManager{

	protected static $records = array();

	/**
	 * Finds module based on given slug
	 *
	 * @param integer $slug
	 * @throws MK_ModuleException
	 * @return MK_Module
	 */
	
	public static function getNewRecord($module_id)
	{

		$module = MK_RecordModuleManager::getFromId($module_id);
		
		$class = 'MK_Record'.MK_Utility::stringToReference($module->getType());

		if(class_exists($class) && $class === 'MK_RecordModule')
		{
			return new $class();
		}
		elseif(class_exists($class))
		{
			return new $class($module_id);
		}
		else
		{
			return new MK_Record($module_id);
		}

	}

	public static function getFromId($module_id, $id)
	{
		$config = MK_Config::getInstance();
			
		if( empty(self::$records[$module_id]) )
		{
			self::$records[$module_id] = array();
		}
		elseif( !empty(self::$records[$module_id][$id]) )
		{
			return self::$records[$module_id][$id];
		}

		$module = MK_RecordModuleManager::getFromId($module_id);

		$table = MK_Database::getTableName( $module->getTable() );

		$pre_record = MK_Database::getInstance()->prepare("SELECT * FROM `$table` WHERE `id` = :id LIMIT 1");
		$pre_record->bindValue(':id', $id, PDO::PARAM_INT);
		$pre_record->execute();

		if( $res_record = $pre_record->fetch( PDO::FETCH_ASSOC ) )
		{
			$class = 'MK_Record'.MK_Utility::stringToReference($module->getType());

			if(class_exists($class) && $class === 'MK_RecordModule')
			{
				self::$records[$module->getId()][$id] = new $class($id);
			}
			elseif(class_exists($class))
			{
				self::$records[$module->getId()][$id] = new $class($module->getId(), $id);
			}
			else
			{
				self::$records[$module->getId()][$id] = new MK_Record($module->getId(), $id);
			}
			
			$pre_record->closeCursor();
			return self::$records[$module->getId()][$id];
		}
		else
		{
			throw new MK_ModuleException('Record could not be found using id; '.$id);
		}
		
	}
	
}

?>