<?php

class MK_Record extends MK_MetaFactoryExtra
{
	
	protected $module_id;
	
	public function __construct( $module_id, $record_id = null )
	{
		$this->module_id = $module_id;
		$this->meta = $this->module()->getFieldList();

		if($record_id)
		{
			$data = $this->build($record_id);
			$this->populate($data);
		}
		
	}

	public function module()
	{
		return MK_RecordModuleManager::getFromId($this->module_id);
	}

	public function getMetaValue( $key, MK_Record $field = null ){

		$value = parent::getMetaValue($key);

		if( !empty($field) ){

			return MK_DataRenderer::getRealValue($value, $field);
			
		}else{
			return $value;	
		}

	}

	public function canEdit( MK_RecordUser $user )
	{
		if( !$user->isAuthorized() )
		{
			return false;
		}

		$config = MK_Config::getInstance();

		if( $config->core->mode == MK_Core::MODE_DEMO )
		{
			if($this->module()->getType() === 'module')
			{
				return !(boolean) $this->module()->isLocked();
			}
			else
			{
				return !(boolean) $this->module()->isLockRecords();
			}
		}
		
		if( $user->objectGroup()->isAdmin() )
		{
			return true;
		}
		
		return false;
	}
	
	public function canDelete( MK_RecordUser $user )
	{
		return $this->canEdit( $user );
	}
	
	public function getSubRecords( $levels = 0, &$records = array() )
	{
		$record_module = $this->module();

		if($field_parent = $record_module->getFieldParent())
		{
			$field_module = MK_RecordModuleManager::getFromType('module_field');
			$field_parent = MK_RecordManager::getFromId($field_module->getId(), $field_parent);

			$search_criteria = array(
				array('value' => $this->getId(), 'field' => $field_parent->getName())
			);

			$search_records = $record_module->searchRecords($search_criteria);

			foreach( $search_records as $search_record )
			{
				$records[] = $search_record;
				$search_record->getSubRecords( 0, $records);
			}
		}
		return $records;

	}

	public function getParentRecords()
	{
		$record_module = $this->module();
		$records = array();

		if($field_parent = $record_module->getFieldParent())
		{
			$field_module = MK_RecordModuleManager::getFromType('module_field');
			$field_parent = MK_RecordManager::getFromId($field_module->getId(), $field_parent);

			$current_record = $this;

			while( $parent_id = $current_record->getMetaValue($field_parent->getName()) )
			{
				$current_record = MK_RecordManager::getFromId($record_module->getId(), $parent_id);
				$records[] = $current_record;
			}
			
			unset($current_record);

		}

		return $records;

	}

	public function save( $update_meta = true )
	{
		$table = MK_Database::getTableName( $this->module()->getTable() );

		if( !empty( $this->meta['id'] ) )
		{
			$old_data = $this->build( $this->getId() );
			
			$sql_parts = array();
			$parameters = array();

			foreach($this->meta as $value_key => $value_data)
			{
				if($old_data[$value_key] != $value_data)
				{
					$sql_parts[] = "`$value_key` = :$value_key";
					$parameters[$value_key] = $value_data;
				}
			}

			if(count($parameters) > 0)
			{
				$pre_record = MK_Database::getInstance()->prepare("UPDATE `$table` SET ".implode(', ', $sql_parts)." WHERE `id` = :id LIMIT 1");
				$parameters['id'] = $this->getId();
				$pre_record->execute($parameters);
			}
			
			$record_id = $this->meta['id'];
		}
		else
		{
			$parameters = array_filter($this->meta);
			$fields = array_keys($parameters);

			$pre_record = MK_Database::getInstance()->prepare("INSERT INTO `$table` (".( !empty($fields) ? "`".implode('`, `', $fields)."`" : "" ).") VALUES (".( !empty($fields) ? ":".implode(', :', $fields) : "" ).")");
			$pre_record->execute($parameters);

			$record_id = MK_Database::getInstance()->lastInsertId();
			$data = $this->build($record_id);
			$this->populate($data);
		}
		
		return $this;
		
	}

	public function __process( $type, $call, $arguments )
	{
		if( $type === 'render' )
		{
			return $this->renderMetaValue( $call, reset($arguments) ? array_shift($arguments) : null );
		}
		elseif( $type === 'object' )
		{
			return $this->objectMetaValue( $call, reset($arguments) ? array_shift($arguments) : null );
		}
		else
		{
			return parent::__process( $type, $call, $arguments );
		}
	}
	
	public function renderMetaValue( $key )
	{
		if( array_key_exists( $key, $this->meta ) )
		{
			$field_module = MK_RecordModuleManager::getFromType('module_field');
			$field = MK_RecordManager::getFromId( $field_module->getId(), $this->module()->getField($key) );

			return MK_DataRenderer::render( $this->meta[$key], $field );
		}
		else
		{
			throw new MK_ModuleException('Field \''.$key.'\' does not exist');
		}
		
	}

	public function objectMetaValue( $key )
	{
		if( array_key_exists( $key, $this->meta ) )
		{
			$field_module = MK_RecordModuleManager::getFromType('module_field');
			$field = MK_RecordManager::getFromId( $field_module->getId(), $this->module()->getField($key) );

			try
			{
				$field_type = str_replace('_current', '', $field->getType());
				$module = MK_RecordModuleManager::getFromType( $field_type );
				$record = MK_RecordManager::getFromId( $module->getId(), $this->meta[$key] );
				return $record;
			}
			catch( Exception $e )
			{
				throw new MK_ModuleException("Field '".$field->getType()."' cannot be represented as an object");
			}

			return MK_DataRenderer::render( $this->meta[$key], $field );
		}
		else
		{
			throw new MK_ModuleException('Field \''.$key.'\' does not exist');
		}
		
	}

	protected function build( $id )
	{
		$table = MK_Database::getTableName($this->module()->getTable());

		$pre_record = MK_Database::getInstance()->prepare("SELECT * FROM `$table` WHERE `id` = :id LIMIT 1");
		$pre_record->bindValue(':id', $id, PDO::PARAM_INT);
		$pre_record->execute();
		
		if( $pre_record->rowCount() > 0 )
		{
			return $pre_record->fetch( PDO::FETCH_ASSOC );
		}
		else
		{
			throw new MK_ModuleRecordException('The Record Id \''.$id.'\' given does not exist');	
		}

		$pre_record->closeCursor();
	}

	public function populate( $data )
	{
		foreach( $data as $field => $value )
		{
			if( array_key_exists( $field, $this->meta) )
			{
				$this->meta[$field] = $value;
			}
			else
			{
				$this->meta_extra[$field] = $value;
			}
		}
	}
	
	public function delete()
	{

		$config = MK_Config::getInstance();

		$dependents = $this->getDependents();

		foreach($dependents['records'] as $record)
		{
			$record->delete();
		}

		$path_base = dirname(__FILE__).'/../../../../..';
		$thumbs_path = $path_base.'/tpl/img/thumbs';
		foreach($dependents['files'] as $file)
		{
			$path_base_file = $path_base.'/'.$file;

			
			if( is_file($path_base_file) )
			{
				$file_name = explode('/', $file);
				$file_name = array_pop($file_name);

				$file_name_parts = explode('.', $file_name);
				$file_name_parts = array_shift($file_name_parts);
				$thumb_list = glob($thumbs_path.'/*'.$file_name_parts.'.*');

				foreach( $thumb_list as $thumb )
				{
					unlink($thumb);
				}


				$admin_fix = explode("/", $_SERVER['SCRIPT_NAME']);

				if ($admin_fix[count($admin_fix)-2] == "admin") {					
					unlink( str_replace("tpl/","../tpl/", str_replace("admin/","", $file) ) );
				} else {
					unlink( $file );
				}
				
			}
		}

		$table = MK_Database::getTableName( $this->module()->getTable() );

		$pre_record = MK_Database::getInstance()->prepare("DELETE FROM `$table` WHERE `id` = :id LIMIT 1");
		$pre_record->bindValue(':id', $this->getId(), PDO::PARAM_INT);
		$pre_record->execute();
	}
	
	public function toArray( $expand_object = true, $response = null )
	{
		$fields = array();
		$record_array = array();

		foreach( $this->module()->getFields() as $module_field )
		{
			if( in_array( $module_field->getType(), array('password', 'hidden')) )
			{
				continue;
			}
			
			if( !empty($response) && !in_array( $module_field->getName(), $response ) )
			{
				continue;
			}
			
			$fields[$module_field->getName()] = '';
		}
		
		if( $this->module()->getType() == 'user' )
		{
			$table = MK_Database::getTableName('users_meta');

			$pre_meta = MK_Database::getInstance()->prepare("SELECT `key` FROM `".$table."` GROUP BY `key`");
			$pre_meta->execute();
			
			while( $record = $pre_meta->fetch( PDO::FETCH_ASSOC ) )
			{
				$fields[ $record['key'] ] = '';
			}
		}

		foreach( $fields as $field_name => $field_data )
		{
			try
			{
				if( $this instanceof MK_RecordModuleField || $this instanceof MK_RecordModule )
				{
					throw new Exception("Field and Module Modules should not be recursively explored.");
				}

				if( $expand_object )
				{
					$field_object = $this->objectMetaValue($field_name);
					$record_array[$field_name] = $field_object->toArray();
				}
				else
				{
					$record_array[$field_name] = $this->renderMetaValue($field_name);
				}
			}
			catch( Exception $e )
			{
				$record_array[$field_name] = $this->getMetaValue($field_name);
			}
		}
		
		return $record_array;
	}
	
	public function getDependents()
	{

		$dependents = array(
			'files' => array(),
			'records' => array()
		);

		$config = MK_Config::getInstance();
		$fields = $this->module()->getFields();
		$files = array();

		foreach($fields as $name => $attributes)
		{
			if( substr( $attributes->getType(), 0, 4) === 'file' && !empty($this->meta[$attributes->getName()]) ){
				$dependents['files'][] = $this->meta[$attributes->getName()];
			}
		}

		$field_module = MK_RecordModuleManager::getFromType('module_field');

		$search_criteria = array(
			array('value' => $this->module()->getType(), 'field' => 'type')
		);
		
		$records_fields = $field_module->searchRecords($search_criteria);
		
		foreach($records_fields as $record_field){
			
			$record_module = $record_field->objectModule();

			$search_criteria = array(
				array('value' => $this->getId(), 'field' => $record_field->getName())
			);
			
			$new_dependents = $record_module->searchRecords($search_criteria);
			
			foreach($new_dependents as $dependent){
				$dependents['records'][] = $dependent;
			}

		}
		
		if( $this->module()->getFieldParent() )
		{

			$parent_field = MK_RecordManager::getFromId($field_module->getId(), $this->module()->getFieldParent());

			$search_criteria = array(
				array('value' => $this->getId(), 'field' => $parent_field->getName())
			);
			
			$sub_records = $this->module()->searchRecords($search_criteria);
			
			foreach($sub_records as $sub_record)
			{
				$dependents['records'][] = $sub_record;
			}
			
		}
		
		return $dependents;

	}

}

?>