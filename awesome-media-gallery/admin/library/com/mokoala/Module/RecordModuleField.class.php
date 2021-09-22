<?php

class MK_RecordModuleField extends MK_Record{
	
	public function getValidation(MK_Record &$record){

		$validation_module = MK_RecordModuleManager::getFromType('module_field_validation');
		$rules = array();

		$search = array(
			array(
				'field' => 'field_id',
				'value' => $this->getId()
			)
		);

		$validations = $validation_module->searchRecords( $search );

		foreach($validations as $validation)
		{
			$rule = $validation->getRule();

			$module = MK_RecordModuleManager::getFromId($this->getModule());

			foreach($rule as $name => $args)
			{
				if($name === 'unique' || $name === 'unique_current')
				{
					$args = array($record, $this, $module);
				}
				$rules[$name] = $args;
			}
		}

		return $rules;
		
	}
	
	protected function fieldAttributes($type){
		$modules = MK_RecordModuleManager::getFromType('module');
		$module_types = $modules->getRecords();
		$type_list = array();
		foreach($module_types as $module_type){
			$type_list[] = $module_type->getType();
		}

		if(in_array($type, array('order_by_direction'))):
			return "VARCHAR(4) NOT NULL DEFAULT ''";
		elseif(in_array($type, array('rich_text_large', 'rich_text_small', 'textarea_large', 'textarea_small', 'file_image_multiple_clone', 'file_image_multiple'))):
			return "TEXT NOT NULL DEFAULT ''";
		elseif(in_array($type, array('datetime_now'))):
			return "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
		elseif(in_array($type, array('datetime', 'datetime_static'))):
			return "TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'";
		elseif(in_array($type, array('date'))):
			return "DATE NOT NULL DEFAULT '0000-00-00'";
		elseif(in_array($type, array('id'))):
			return "INT(16) NOT NULL AUTO_INCREMENT PRIMARY KEY";
		elseif(in_array($type, array('checkbox', 'yes_no', 'no_yes'))):
			return "TINYINT(1) NOT NULL DEFAULT 0";
		elseif(in_array($type, array('order_status', 'integer', 'file_size')) || in_array($type, $type_list)):
			return "BIGINT (32) UNSIGNED NOT NULL DEFAULT 0";
		elseif(in_array($type, array('currency')) || in_array($type, $type_list)):
			return "FLOAT UNSIGNED NOT NULL DEFAULT '0'";
		elseif(in_array($type, array('module_field_current')) || in_array($type, $type_list)):
			return "INT(32) NOT NULL DEFAULT 0";
		else:
			return "VARCHAR(255) NOT NULL DEFAULT ''";
		endif;
	}
	
	public function save( $update_meta = true )
	{
		$config = MK_Config::getInstance();

		$module = $this->objectModule();

		if( !empty( $this->meta['id'] ) )
		{
			if( $this->getType() != 'id' )
			{
				$old_data = $this->build( $this->getId() );
				MK_Database::getInstance()->query("ALTER TABLE `".MK_Database::getTableName( $module->getTable() )."` CHANGE `".$old_data['name']."` `".$this->getName()."` ".$this->fieldAttributes($this->getType()));
			}
		}
		else
		{
			MK_Database::getInstance()->query("ALTER TABLE `".MK_Database::getTableName( $module->getTable() )."` ADD `".$this->getName()."` ".$this->fieldAttributes( $this->getType() ));
		}
	
		return parent::save( $update_meta );
	}
	
	public function delete(){
		
		$config = MK_Config::getInstance();
		
		$module = MK_RecordModuleManager::getFromId( $this->getModule() );

		MK_Database::getInstance()->query("ALTER TABLE `".MK_Database::getTableName( $module->getTable() )."` DROP COLUMN `".$this->getName()."`");

		parent::delete();
		
	}

}

?>