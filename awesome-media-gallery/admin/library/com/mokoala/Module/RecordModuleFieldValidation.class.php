<?php

class MK_RecordModuleFieldValidation extends MK_Record
{
	
	public function save( $update_meta = true )
	{
		$table = MK_Database::getTableName( 'modules_fields_validation_arguments' );

		$pre_delete = MK_Database::getInstance()->prepare("DELETE FROM `$table` WHERE `validation_id` = :validation_id");
		$pre_delete->bindValue(':validation_id', $this->getId(), PDO::PARAM_INT);
		$pre_delete->execute();
		
		if( is_array($this->getValidationArguments()) )
		{
			$pre_insert = MK_Database::getInstance()->prepare("INSERT INTO `$table` (`validation_id`, `index`, `value`) VALUES (:validation_id, :index, :argument)");
			foreach( $this->getValidationArguments() as $index => $argument )
			{
				$pre_insert->execute(array(
					'validation_id' => $this->getId(),
					'index' => $index,
					'value' => $argument,
				));
				$pre_insert->closeCursor();
			}
		}
		
		return parent::save($update_meta);
	}
	
	public function delete()
	{
		parent::delete();
		
		$table = MK_Database::getTableName( 'modules_fields_validation_arguments' );

		$pre_insert = MK_Database::getInstance()->prepare("DELETE FROM `$table` WHERE `validation_id` = :validation_id");
		$pre_delete->bindValue(':validation_id', $this->getId(), PDO::PARAM_INT);
		$pre_delete->execute();
	}
	
	public function getRule()
	{
		$table = MK_Database::getTableName( 'modules_fields_validation_arguments' );

		$pre_records = MK_Database::getInstance()->prepare("SELECT * FROM `$table` WHERE `validation_id` = :validation_id ORDER BY `index` ASC");
		$pre_records->bindValue(':validation_id', $this->getId(), PDO::PARAM_INT);
		$pre_records->execute();

		$rule = array();
		$arguments = array();
		
		while( $res_records = $pre_records->fetch( PDO::FETCH_ASSOC ) )
		{
			$arguments[$res_records['index']] = $res_records['value'];
		}

		$pre_records->closeCursor();

		$rule = array(
			$this->getName() => $arguments
		);

		return $rule;
		
	}

}

?>