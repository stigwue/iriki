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
        
        //load model json files for already defined routes
        //no route, no model
        
        foreach ($routes as $route_title => $route_actions)
        {
            $obj_config = new config($path . $route_title . '.json');
            $model_json = $obj_config->toObject();
            $model_config = $model_json['mongovc']['models'][$route_title];
            $model_struct['models'][] = $model_config;
        }
            
        //var_dump($path . $route_title . '.json');
        //var_dump($model_struct);
        //var_dump($routes);
        var_dump($obj_config);
        
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
