<?php

abstract class MK_Backup
{

	public static function load( $backup_file )
	{
		ini_set('memory_limit', '200M');
		set_time_limit(0);
		$config = MK_Config::getInstance();

		if(!MK_Database::isConnected())
		{
			throw new MK_Exception("Could not find connection to SQL server");
		}

		if( !is_dir('../tpl/uploads/') )
		{
			throw new MK_Exception("The directory '../tpl/uploads/' does not exist.");
		}

		if( !is_dir('resources/restore/') )
		{
			throw new MK_Exception("The directory 'resources/restore/' does not exist.");
		}
		
		$backup_file_parts = explode('.', $backup_file);
		$backup_file_extension = array_pop($backup_file_parts);
		
		if( $backup_file_extension !== 'zip' )
		{
			throw new MK_Exception("Backups must be in .zip format.");
		}
		
		$zip = new MK_ZipArchive();
		if ($zip->open($backup_file, ZIPARCHIVE::CHECKCONS) !== true)
		{
			throw new MK_Exception("Could not open backup file '$backup_file'.");
		}
		
		//Extract files & begin restoration
		$zip->extractTo('resources/restore/');

		if( is_file('resources/restore/database/database.sql') !== true )
		{
			throw new MK_Exception("This backup is either corrupt or invalid.");
		}
		
		$current_uploads = new MK_Directory('../tpl/uploads/');
		$current_uploads->delete(true);
		
		// Restore database
		$get_tables = MK_Database::getInstance()->query("SHOW TABLES");
		$table_list = array();
		while($res_tables = $get_tables->fetch( PDO::FETCH_ASSOC ))
		{
			$table_list[] = array_pop($res_tables);
		}

		// Delete tables
		$delete_tables = MK_Database::getInstance()->query("DROP TABLE `".implode('`, `', $table_list)."`");

		$get_sql = file_get_contents('resources/restore/database/database.sql');
		$get_sql_parts = MK_Utility::SQLSplit($get_sql);
		
		foreach( $get_sql_parts as $get_sql_part )
		{
			$get_sql_part = trim($get_sql_part);
			if(empty($get_sql_part))
			{
				continue;
			}
			
			MK_Database::getInstance()->query($get_sql_part);
		}
		
		// Move files
		if( is_dir('resources/restore/uploads/') )
		{
			$uploads = new MK_Directory('resources/restore/uploads/');
			$uploads->move('../tpl/uploads/');
		}
		else
		{
			$uploads = new MK_Directory('../tpl/uploads/');
		}
		
		// Clean temp restore folders
		$database = new MK_Directory('resources/restore/database/');
		$database->delete(true);

	}
	
}

?>