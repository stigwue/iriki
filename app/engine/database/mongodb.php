<?php

namespace iriki\engine\database;

require_once(__DIR__ . '/default.php');

class mongodb extends database
{
    private static $database = null;

    public static function doconnect($db_name, $reconnect = false)
    {
		if ($reconnect)
		{
	        $connection = new MongoClient();
	        //Self::database = $connection->$$db_name; //$conn->myboard;
	        return $database;
		}
    }
}

?>