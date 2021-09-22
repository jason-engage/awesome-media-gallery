<?php

class MK_RecordModule extends MK_Record{
	
	protected $records = array();
	protected $fields = array();
	protected $deleted = false;
	protected $parent_module = false;
	
	public function __construct( $id = null )
	{
		
		if( !empty($id) )
		{
			$this
				->setId($id)
				->buildFieldList();
		}
		else
		{
			$this->meta = $this->module()->getFieldList();
		}
	
	}

	public static function newRecord()
	{		
		$class = 'MK_Record'.MK_Utility::stringToReference( $this->getType() );

		if( class_exists($class) && $class === 'MK_RecordModule' )
		{
			return new $class();
		}
		elseif( class_exists($class) )
		{
			return new $class( $this->getId() );
		}
		else
		{
			return new MK_Record( $this->getId() );
		}

	}

	public static function getRecord( $id )
	{
		$config = MK_Config::getInstance();
			
		if( !empty(self::$records[$id]) )
		{
			return self::$records[$id];
		}

		$table = MK_Database::getTableName( $this->getTable() );

		$pre_record = MK_Database::getInstance()->prepare("SELECT * FROM `$table` WHERE `id` = :id LIMIT 1");
		$pre_record->bindValue(':id', $id, PDO::PARAM_INT);
		$pre_record->execute();

		if( $res_record = $pre_record->fetch( PDO::FETCH_ASSOC ) )
		{
			$class = 'MK_Record'.MK_Utility::stringToReference($this->getType());

			if( class_exists($class) && $class === 'MK_RecordModule' )
			{
				self::$records[$id] = new $class($id);
			}
			elseif( class_exists($class) )
			{
				self::$records[$id] = new $class($this->getId(), $id);
			}
			else
			{
				self::$records[$id] = new MK_Record($this->getId(), $id);
			}
			
			$pre_record->closeCursor();
			return self::$records[$id];
		}
		else
		{
			throw new MK_ModuleException('Record could not be found within the module '.$this->getId().' using id; '.$id);
		}
		
	}

	protected function buildFieldList()
	{
		$config = MK_Config::getInstance();

		$modules_table = MK_Database::getTableName( 'modules' );

		$pre_modules = MK_Database::getInstance()->prepare("SELECT * FROM `$modules_table` WHERE `id` = :id LIMIT 1");
		$pre_modules->bindValue(':id', $this->getId(), PDO::PARAM_INT);
		$pre_modules->execute();

		$this->meta = $pre_modules->fetch( PDO::FETCH_ASSOC );

		$pre_modules->closeCursor();

		$actual_field_module = 'fields';

		if($this->getSlug() === $actual_field_module)
		{
			$field_module = $this;
		}
		else
		{
			$field_module = MK_RecordModuleManager::getFromSlug($actual_field_module);
		}

		$modules_fields_table = MK_Database::getTableName( 'modules_fields' );

		$pre_fields = MK_Database::getInstance()->prepare("SELECT * FROM `$modules_fields_table` WHERE `module` = :module ORDER BY `order` ASC");
		$pre_fields->bindValue(':module', $this->getId(), PDO::PARAM_INT);
		$pre_fields->execute();

		while($res_fields = $pre_fields->fetch( PDO::FETCH_ASSOC ))
		{
			$this->fields[$res_fields['name']] = $res_fields['id'];
		}

		$pre_fields->closeCursor();
		return $this;
	}
	
	public function _getRecords(&$records, MK_Paginator &$paginator = null, $options = array(), $parent = 0, $level = 0)
	{
		
		$sql_where = array();
		$sql_order_by = array();
		
		$parameters = array();

		$config = MK_Config::getInstance();
		$field_module = MK_RecordModuleManager::getFromType('module_field');

		if( $this->getFieldParent() )
		{
			$field = MK_RecordManager::getFromId($field_module->getId(), $this->getFieldParent());
			$sql_where[] = "`".$field->getName()."` = :".$field->getName();
			$parameters[$field->getName()] = $parent;
		}

		if( !empty($options['order_by']) )
		{
			if( $options['order_by']['direction'] == 'rand' )
			{
				$sql_order_by[] = " RAND() ";
			}
			else
			{
				$sql_order_by[] = "`".$options['order_by']['field']."` ".$options['order_by']['direction']."";
			}
		}

		$table = MK_Database::getTableName( $this->getTable() );

		$pre_records = MK_Database::getInstance()->prepare("SELECT * FROM `".$table."`".( count($sql_where) > 0 ? " WHERE ".implode(" AND ", $sql_where) : '').(count($sql_order_by) > 0 ? " ORDER BY ".implode(", ", $sql_order_by) : '').( !$this->getFieldParent() && $paginator ? " LIMIT ".$paginator->getRecordStart().", ".$paginator->getPerPage() : "" ));
		$pre_records->execute($parameters);

		if( $pre_records->rowCount() > 0 )
		{
			while($res_records = $pre_records->fetch( PDO::FETCH_ASSOC ))
			{
				if( $paginator && $paginator->getTotalRecords() == count($records) )
				{
					break;
				}

				$new_field = MK_RecordManager::getFromId($this->getId(), $res_records['id']);
				$new_field->setNestedLevel($level);
				$records[] = $new_field;
				if($this->getFieldParent())
				{
					$this->_getRecords($records, $paginator, $options, $new_field->getId(), $level + 1);
				}

			}

		}

		$pre_records->closeCursor();
	}

	public function getRecords(MK_Paginator &$paginator = null, $options = array())
	{
		$config = MK_Config::getInstance();

		$records = array();
	
		if( $this->deleted === true )
		{
			return $records;
		}
		
		$field_module = MK_RecordModuleManager::getFromType('module_field');
		
		if( !empty($options['order_by']) )
		{
			if( $options['order_by'] == 'rand' )
			{
				$order_by_field = 'rand';
				$order_by_direction = 'rand';
			}
			else
			{
				$field = MK_RecordManager::getFromId($field_module->getId(), $options['order_by']['field']);
				$order_by_field = $field->getName();
				$order_by_direction = $options['order_by']['direction'];
			}
		}
		elseif( $this->getFieldOrderBy() && $this->getOrderByDirection() )
		{
			$field = MK_RecordManager::getFromId($field_module->getId(), $this->getFieldOrderBy());
			$order_by_field = $field->getName();
			$order_by_direction = $this->getOrderByDirection();
		}

		if( isset($order_by_field, $order_by_direction) )
		{
			$options = array(
				'order_by' => array(
					'field' => $order_by_field,
					'direction' => $order_by_direction
				)
			);
		}
		
		if( $paginator )
		{
			$table = MK_Database::getTableName( $this->getTable() );
	
			$pre_records = MK_Database::getInstance()->prepare("SELECT COUNT(`id`) as 'total' FROM `".$table."`");
			$pre_records->bindValue(':module', $this->getId(), PDO::PARAM_INT);
			$pre_records->execute();

			$res_records = $pre_records->fetch( PDO::FETCH_ASSOC );
			$paginator->setTotalRecords($res_records['total']);

			$pre_records->closeCursor();
		}

		$this->_getRecords($records, $paginator, $options);

		if($this->getFieldParent() && $paginator)
		{
			$records = array_slice( $records, $paginator->getRecordStart(), $paginator->getPerPage() );
		}

		return $records;
		
	}

	public function searchRecords($search_criteria, $paginator = null, $options = array()){
		
		$sql_parts = array();
		$search_results = array();
		$parameters = array();

		$field_module = MK_RecordModuleManager::getFromType('module_field');

		// Search criteria
		foreach($search_criteria as $criteria)
		{
			if( empty($criteria['literal']) )
			{
				$field = $criteria['field'];
				$parameters[$field] = $criteria['value'];
				if( !empty( $criteria['wildcard'] ) && $criteria['wildcard'] === true)
				{
					$sql_parts[] = "`$field` LIKE :$field";
				}
				else
				{
					$sql_parts[] = "`$field` = :$field";
				}
			}
			else
			{
				$sql_parts[] = $criteria['literal'];
			}

		}
		
		// Ordering
		if( !empty($options['order_by']) && !empty($options['order_by']['direction']) && !empty($options['order_by']['field']) )
		{
			if( $options['order_by'] == 'rand' )
			{
				$order_by_field = 'rand';
				$order_by_direction = 'rand';
			}
			else
			{
				$field = MK_RecordManager::getFromId($field_module->getId(), $options['order_by']['field']);
				$order_by_field = $field->getName();
				$order_by_direction = $options['order_by']['direction'];
			}
		}
		elseif( $this->getFieldOrderBy() && $this->getOrderByDirection() )
		{
			$field = MK_RecordManager::getFromId($field_module->getId(), $this->getFieldOrderBy());
			$order_by_field = $field->getName();
			$order_by_direction = $this->getOrderByDirection();
		}

		if( isset($order_by_field, $order_by_direction) ){
			$options = array(
				'order_by' => array(
					'field' => $order_by_field,
					'direction' => $order_by_direction
				)
			);
		}

		$table = MK_Database::getTableName( $this->getTable() );

		$pre_records = MK_Database::getInstance()->prepare("SELECT * FROM `$table`".($sql_parts ? " WHERE ".implode(' AND ', $sql_parts) : '').( !empty($options['order_by']) ? $options['order_by']['direction'] == 'rand' ? ' ORDER BY RAND() ' : ' ORDER BY `'.($options['order_by']['field'].'` '.$options['order_by']['direction']) : "" ).( $paginator ? " LIMIT ".$paginator->getRecordStart().", ".$paginator->getPerPage() : ""));

		$pre_records->execute($parameters);

		if($paginator)
		{
			$pre_records_total = MK_Database::getInstance()->prepare("SELECT COUNT(*) AS `total` FROM `$table`".($sql_parts ? " WHERE ".implode(' AND ', $sql_parts) : '' ));
			$pre_records_total->execute($parameters);

			$res_total_records = $pre_records_total->fetch( PDO::FETCH_ASSOC );

			$paginator->setTotalRecords($res_total_records['total']);

			$pre_records_total->closeCursor();
		}

		while( $res_records = $pre_records->fetch( PDO::FETCH_ASSOC ) )
		{
			$search_results[] = MK_RecordManager::getFromId($this->getId(), $res_records['id']);
		}

		$pre_records->closeCursor();

		return $search_results;
		
	}

	public function getTotalRecords($search_criteria = null)
	{
		return $this->countRecords($search_criteria);
	}

	public function countRecords($search_criteria = null)
	{
		$sql_parts = array();
		$parameters = array();

		$field_module = MK_RecordModuleManager::getFromType('module_field');

		// Search criteria
		if( !empty($search_criteria) )
		{
			foreach($search_criteria as $criteria)
			{
				if( empty($criteria['literal']) )
				{
					$field = $criteria['field'];
					$value = $criteria['value'];
					
					$parameters[$field] = $value;
					
					if( !empty( $criteria['wildcard'] ) && $criteria['wildcard'] === true)
					{
						$sql_parts[] = "`$field` LIKE :$field";
					}
					else
					{
						$sql_parts[] = "`$field` = :$field";
					}
				}
				else
				{
					$sql_parts[] = $criteria['literal'];
				}
	
			}
		}

		$table = MK_Database::getTableName( $this->getTable() );

		$pre_records = MK_Database::getInstance()->prepare("SELECT COUNT(*) AS `total` FROM `$table`".($sql_parts ? " WHERE ".implode(' AND ', $sql_parts) : '' ));
		$pre_records->execute( $parameters );

		$res_records = $pre_records->fetch( PDO::FETCH_ASSOC );

		$pre_records->closeCursor();

		return $res_records['total'];
		
	}
	
	public function getDataGridFields()
	{
		return $this->getFields(true);
	}

	public function getFields($display_grid_only = false){
		
		$return_fields = array();
		$field_module = MK_RecordModuleManager::getFromSlug('fields');

		foreach($this->fields as $field_key => $field){
			$field = MK_RecordManager::getFromId($field_module->getId(), $field);

			if($display_grid_only === true){
				if($field->getDisplayWidth()){
					$return_fields[] = $field;
				}
			}else{
				$return_fields[] = $field;
			}

		}
		
		return $return_fields;
		
	}

	public function getField($field_name){
		
		if( !empty($this->fields[$field_name]) ){
			
			return $this->fields[$field_name];
			
		}
		
	}

	public function save( $update_meta = true )
	{
		$config = MK_Config::getInstance();

		if( !empty( $this->meta['id'] ) )
		{
			$old_data = $this->build( $this->getId() );
			MK_Database::getInstance()->query("ALTER TABLE `".MK_Database::getTableName( $old_data['table'] )."` RENAME `".MK_Database::getTableName( $this->getTable() )."`");
			return parent::save( $update_meta );
		}
		else
		{
			MK_Database::getInstance()->query("CREATE TABLE `".MK_Database::getTableName( $this->getTable() )."` (id INT(16) NOT NULL AUTO_INCREMENT PRIMARY KEY) CHARSET=utf8 COLLATE=utf8_unicode_ci");
			parent::save();

			MK_Database::getInstance()->query("INSERT INTO `".MK_Database::getTableName('modules_fields')."` (`order`, `module`, `name`, `label`, `type`, `editable`, `display_width`) VALUES ('1', '".$this->getId()."', 'id', 'ID', 'id', '0', '')");

			$this->setFieldId( MK_Database::getInstance()->lastInsertId() );

			return parent::save( $update_meta );
		}
		
	}
	
	public function module()
	{
		$module = MK_RecordModuleManager::getFromType('module');
		return $module;
	}
	
	public function getFieldList()
	{
		$fields = $this->fields;

		if(count($fields) === 1)
		{
			$this->buildFieldList();
			$fields = $this->fields;
		}

		return array_fill_keys( array_keys( $fields ), '' );
	}
	
	public function flush()
	{
		MK_Database::getInstance()->query("DELETE FROM `".MK_Database::getTableName( $this->getTable() )."`");
	}

	public function delete()
	{

		$records = $this->getRecords();

		foreach($records as $record)
		{
			$record->delete();
		}

		parent::delete();

		$this->deleted = true;
		MK_Database::getInstance()->query("DROP TABLE `".MK_Database::getTableName( $this->getTable() )."`");
	}
	
}

?>