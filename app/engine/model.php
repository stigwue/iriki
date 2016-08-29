<?php

namespace mongovc\engine;

class mongodb
{
    private static $database = null;

    public static function doconnect($db_name, $reconnect = false)
    {
		if ($reconnect)
		{
	        $connection = new MongoClient();
	        Self::database = $connection->$$db_name; //$conn->myboard;
	        return $database;
		}
    }
}


class model extends config
{
	private static $db_connection;

	function __contruct()
	{
		//read db details

		//initialise connection
	}

	public static function saveNew($model)
	{

	}
}

?>
