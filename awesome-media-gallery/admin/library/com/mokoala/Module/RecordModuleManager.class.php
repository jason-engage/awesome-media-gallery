<?php

/**
 * Manages access to modules
 * 
 * @author Matt Lowden
 * @version 1.0
 *
 */

class MK_RecordModuleManager{

	protected static $modules = array();
	protected static $module_slugs = array();
	protected static $module_types = array();

	/**
	 * Finds module based on given slug
	 *
	 * @param string $slug
	 * @throws MK_ModuleException
	 * @return MK_Module
	 */
	public static function getFromSlug($slug)
	{
		if( !empty( self::$module_slugs[$slug] ) )
		{
			return self::$modules[ self::$module_slugs[$slug] ];
		}
		else
		{
			$table = MK_Database::getTableName( 'modules' );
			$pre_module = MK_Database::getInstance()->prepare("SELECT `id`, `slug` FROM `$table` WHERE `slug` = :slug");
			$pre_module->bindValue(':slug', $slug, PDO::PARAM_STR);
			$pre_module->execute();

			if( $res_module = $pre_module->fetch( PDO::FETCH_ASSOC ) )
			{
				$pre_module->closeCursor();
				self::$module_slugs[ $res_module['slug'] ] = $res_module['id'];
				self::$modules[ $res_module['id'] ] = new MK_RecordModule( $res_module['id'] );
				return self::$modules[ $res_module['id'] ];
			}
			else
			{
				throw new MK_ModuleException('Module could not be found using slug; '.$slug);
			}
			
		}
		
	}
	
	/**
	 * Finds module based on given type
	 *
	 * @param string $type
	 * @throws MK_ModuleException
	 * @return MK_Module
	 */
	public static function getFromType( $type )
	{
		if( !empty(self::$module_types[ $type ]) )
		{
			return self::$modules[ self::$module_types[$type] ];
		}

		$table = MK_Database::getTableName( 'modules' );

		$pre_module = MK_Database::getInstance()->prepare("SELECT `id`, `type` FROM `$table`");
		$pre_module->execute();

		$res_modules = $pre_module->fetchAll( PDO::FETCH_ASSOC );

		foreach( $res_modules as $res_module )
		{
			self::$module_types[ $res_module['type'] ] = $res_module['id'];
			self::$modules[ $res_module['id'] ] = new MK_RecordModule( $res_module['id'] );
		}

		$pre_module->closeCursor();

		if( empty(self::$module_types[$type]) || empty(self::$modules[ self::$module_types[$type] ]) )
		{
			throw new MK_ModuleException('Module could not be found using type; '.$type);
		}
		else
		{
			return self::$modules[ self::$module_types[$type] ];
		}
		
	}
	
	/**
	 * Returns true of false depending on whether module exists based on the type supplied
	 *
	 * @param string $type
	 * @return boolean
	 */
	public static function existsFromType( $type )
	{
		try
		{
			self::getFromType( $type );
			return true;
		}
		catch( Exception $e )
		{
			return false;
		}
	}
	
	/**
	 * Finds module based on given id
	 *
	 * @param integer $slug
	 * @throws MK_ModuleException
	 * @return MK_Module
	 */
	public static function getFromId($id)
	{
		if(!empty(self::$modules[$id]))
		{
			return self::$modules[$id];
		}
		else
		{
			$table = MK_Database::getTableName( 'modules' );

			$pre_module = MK_Database::getInstance()->prepare("SELECT `slug`, `id` FROM `$table` WHERE `id` = :id");
			$pre_module->bindValue(':id', $id, PDO::PARAM_INT);
			$pre_module->execute();

			if( $res_module = $pre_module->fetch( PDO::FETCH_ASSOC ) )
			{
				$pre_module->closeCursor();
				self::$module_slugs[ $res_module['slug'] ] = $res_module['id'];
				self::$modules[ $res_module['id'] ] = new MK_RecordModule( $res_module['id'] );
				return self::$modules[ $res_module['id'] ];
			}
			else
			{
				throw new MK_ModuleException('Module could not be found using id; '.$id);
			}
		}
		
	}
	
}

?>