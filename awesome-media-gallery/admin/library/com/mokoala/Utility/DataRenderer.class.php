<?php

class MK_DataRenderer
{

	public static function getRealValue($value, MK_Record $field)
	{
		return self::render($value, $field);	
	}

	public static function render($value, MK_Record $field)
	{
		$config = MK_Config::getInstance();
		try{
			
			$type = $field->getType();
			
			if( in_array($type, array('datetime', 'datetime_now', 'datetime_static')) )
			{
				if(empty($value) || $value === '0000-00-00 00:00:00')
				{
					if($field->getName() === 'last_login')
					{
						return 'Never logged in';
					}
					else
					{
						return 'None defined';
					}
				}
				else
				{
					$datetime = strtotime($value);
					$previous_day_datetime = strtotime('-1 day', $datetime);
					return date( $config->site->datetime_format, $datetime);
				}
			}
			elseif( in_array($type, array('currency')) )
			{
				$parsed_value = explode('.', $value);
				$parsed_value_main = !empty($parsed_value[0]) ? $parsed_value[0] : 0;
				$parsed_value_cent = !empty($parsed_value[1]) ? substr($parsed_value[1], 0, 2) : 0;

				return '£'.$parsed_value_main.'.'.str_pad($parsed_value_cent, 2, 0, STR_PAD_LEFT);
			}
			elseif( in_array($type, array('order_status')) )
			{
				$states = array(
					0 => 'Allocating Stock',
					1 => 'Dispatched',
					2 => 'Completed'
				);

				return !empty($states[$value]) ? $states[$value] : '';
			}
			elseif( in_array($type, array('date')) )
			{
				if(empty($value) || $value === '0000-00-00')
				{
					return 'None defined';
				}
				else
				{
					$datetime = strtotime($value);
					return date( $config->site->date_format, $datetime);
				}
			}
			elseif( $type === 'file_image' || $type === 'file_image_multiple' || $type === 'file_image_multiple_clone' )
			{
				if( empty($value) )
				{
					return 'None defined';
				}
				else
				{
					$value = explode(',', $value);
					$value = array_pop($value);
					$value_ext = explode('.', $value);
					$value_ext = array_pop( $value_ext );
					return '<img class="image" src="'.( $value_ext == 'gif' ? '../'.$value : 'library/thumb.php?f='.$value.'&w=120&h=60&m=contain' ).'" />';
				}
			}
			elseif( $type === 'file_size' )
			{
				return MK_Utility::formatBytes($value);
			}
			elseif( $type === 'user_type' )
			{
				$type_readable = ucwords( str_replace('_', ' ', $value) );
				$type_description = ( $value == MK_RecordUser::TYPE_CORE ? 'This user registered an account' : 'This user logged in with their '.$type_readable.' account' );
				return '<span title="'.$type_description.'" class="user-type user-type-'.$value.'">'.ucwords(str_replace('_', ' ', $value)).'</span>';
			}
			elseif( $type === 'yes_no' || $type === 'no_yes' )
			{
				return ($value ? '<span class="yes">Yes</span>' : '<span class="no">No</span>');
			}
			else
			{

				$field_module = MK_RecordModuleManager::getFromType('module_field');
				$module = MK_RecordModuleManager::getFromType($field->getType());
				$slug_field = MK_RecordManager::getFromId($field_module->getId(), $module->getFieldTitle());

				if($value == 0){
					return 'None defined';
				}

				$record = MK_RecordManager::getFromId($module->getId(), $value);
				if($field->getType() === 'module_field')
				{
					return $record->getLabel().' ('.$record->getName().')';
				}
				else
				{
					return $record->getMetaValue($slug_field->getName());
				}
			}
			
		}catch(Exception $e){
			
			return $value;
			
		}
		
	}

}

?>