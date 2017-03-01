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

	public static function getType()
	{
		return Self::$_type;
	}

	public static function getClass()
	{
		return Self::$_db_class;
	}

	//create
	public static function doCreate($request_obj)
	{
		$response = array();
		$response['error'] = array(
			'code' => response::ERROR,
			'message' => 'Action not yet configured'
		);

		//do logging?

		return $response;
	}

	//read/retrieve
	public static function doRead($request_obj)
	{
		$response = array();
		$response['error'] = array(
			'code' => response::ERROR,
			'message' => 'Action not yet configured'
		);

		//do logging?

		return $response;
	}

	//update
	public static function doUpdate($request_obj)
	{
		$response = array();
		$response['error'] = array(
			'code' => response::ERROR,
			'message' => 'Action not yet configured'
		);

		//do logging?

		return $response;
	}

	//delete
	public static function doDelete($request_obj)
	{
		$response = array();
		$response['error'] = array(
			'code' => response::ERROR,
			'message' => 'Action not yet configured'
		);

		//do logging?

		return $response;
	}
}

?>
