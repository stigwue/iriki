<?php

namespace iriki\engine;

class database
{
	protected static $_key_values;
	protected static $_type = 'default';
	protected static $_namespace;
	protected static $_db_class;
	protected static $_db_class_exists;
	protected static $_instance;

	public static function doInitialise($app, $engine = 'iriki', $config_values = null)
	{
        Self::$_key_values = $config_values;


		if (isset(Self::$_key_values['type']))
		{
			Self::$_type = Self::$_key_values['type'];
		}
		else
		{
			//a db wasn't specified. no persistence then?
			Self::$_type = '';
			Self::$_instance = null;
			Self::$_db_class_exists = false;
		}

		//defined in \iriki\engine
        //or \app\database
		Self::$_namespace = '\\' . $engine . '\\engine\\';
		Self::$_db_class = Self::$_namespace . Self::$_type;
		if (class_exists(Self::$_db_class))
		{
			Self::$_instance = new Self::$_db_class();
			Self::$_db_class_exists = true;
		}
		else
		{
			//try application
			Self::$_namespace = '\\' . $app . '\\engine\\';
			Self::$_db_class = Self::$_namespace . Self::$_type;
			if (class_exists(Self::$_db_class))
			{
				Self::$_instance = new Self::$_db_class();
				Self::$_db_class_exists = true;
			}
		}
		return Self::$_db_class_exists;
	}

	/*public static function getInstanceOfType()
	{
		if (Self::$_db_class_exists)
		{
			Self::$_instance = new Self::$_db_class();
			return Self::$_instance;
		}
		else
		{
			return null;
		}
	}*/

	public static function getType()
	{
		return Self::$_type;
	}

	public static function getClass()
	{
		return Self::$_db_class;
	}

	//create
	public static function doCreate($params)
	{
		return null;
	}

	//read/retrieve
	public static function doRead($params)
	{
		return null;
	}

	//update
	public static function doUpdate($params)
	{
		return null;
	}

	//delete
	public static function doDelete($params)
	{
		return null;
	}

	//close?
}

?>