<?php

namespace iriki\engine;

class database
{
	private static $_key_values;
	private static $_type;

	public static function doInitialise($config_values = null)
	{
        Self::$_key_values = $config_values;
        
	}

	private static function setType($app, $engine = 'iriki')
	{
		if (isset(Self::$_key_values['type']))
		{
			Self::$_type = Self::$_key_values['type'];
		}
		else
		{
			//a db wasn't specified. no persistence then?
			Self::$_type = '';
		}

		//class_exists($model_full)
	}

	public static function getInstance()
	{
		//parse key values and return an instance
        return null;
	}

	//create
	public static function doCreate($instance, $params)
	{
		return null;
	}

	//read/retrieve
	public static function doRead($instance, $params)
	{
		return null;
	}

	//update
	public static function doUpdate($instance, $params)
	{
		return null;
	}

	//delete
	public static function doDelete($instance, $params)
	{
		return null;
	}

	//close?
}

?>