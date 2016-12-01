<?php

namespace iriki\engine\database;

class database
{
	private static $_key_values;

	public static function doInitialise($config_values = null)
	{
        Self::$_key_values = $config_values;
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