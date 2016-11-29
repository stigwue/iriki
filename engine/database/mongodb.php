<?php

namespace iriki\engine\database;

require_once(__DIR__ . '/default.php');

class mongodb extends database
{
	public static function getInstance()
	{
		$instance = new \MongoClient();
        return $instance;
	}

    public static function doConnect(&$instance, $params)
    {
    	if (isset($params['db']))
    	{
    		$db_name = $params['db'];
			
			return $instance->$db_name;
		}
		else
		{
			return null; //or some test/random db
		}
	}
}

?>