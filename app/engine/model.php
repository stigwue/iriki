<?php

namespace mongovc\engine;

require_once(__DIR__ . '/config.php');

class model extends config
{
	private static $db_connection;

	/*function __contruct()
	{
		//read db details

		//initialise connection
	}*/
    
    public function loadFromJson($path, $routes)
    {
        $model_struct = array(
            'path' => $path
        );
        
        $model_struct['models'] = array();
        
        foreach ($routes as $route_title => $route_actions)
        {
            $model_json = (new config($path . $route_title . '.json'))->toObject();
            $model_config = $model_json['mongovc']['models'][$route_title];
            $model_struct['models'][] = $model_config;
            
            var_dump($path . $route_title . '.json');
        }
        
        return $model_struct;
    }

	public static function saveNew($model)
	{

	}
}

class mongodb
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
