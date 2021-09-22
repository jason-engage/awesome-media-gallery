<?php

class MK_RSS{

	public function __construct( $module )
	{
		$module = MK_RecordModuleManager::getFromType($module);
	}
	
}

?>