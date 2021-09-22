<?php
if( $this->export === 'csv' )
{
	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=export-".date('Y-m-d').".csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	$data_array = array();
	$data_output = array();
	$counter = 0;
	foreach($this->records as $record)
	{
		$counter++;
		$data_array[$counter] = array();
		foreach($this->fields as $field)
		{
			if( !empty($this->selected_fields) && !in_array($field->getName(), $this->selected_fields) )
			{
				continue;
			}
			if( in_array($field->getType(), array('password', 'file', 'hidden', 'file_image')) )
			{
				continue;
			}
			$value = MK_Utility::removeHTML( $record->renderMetaValue($field->getName()) );
			$value = addslashes( $value );
			$data_array[$counter][$field->getName()] = $value;
		}
		$data_output[] = '"'.implode('","',$data_array[$counter]).'"';
	}
	print implode("\n", $data_output);
	exit;
}
?>