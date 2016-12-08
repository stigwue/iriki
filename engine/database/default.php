<?php

namespace iriki\engine;

class database
{
	protected static $_key_values;
	protected static $_type;
	protected static $_namespace;
	protected static $_db_class;
	protected static $_db_class_exists;
	protected static $_instance;

	public static function getOfType($app, $engine = 'iriki', $config_values = null)
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

	public static function getInstanceOfType()
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
	}

	public function getInstance()
	{
		return null;
	}

	//create
	public function doCreate($instance, $params)
	{
		return null;
	}

	//read/retrieve
	public function doRead($instance, $params)
	{
		return null;
	}

	//update
	public function doUpdate($instance, $params)
	{
		return null;
	}

	//delete
	public function doDelete($instance, $params)
	{
		return null;
	}

	//close?
}

?>