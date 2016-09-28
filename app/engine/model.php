<?php

namespace iriki\engine;

require_once(__DIR__ . '/config.php');

class model extends config
{
    private $_models;
    
    public function loadFromJson($model_path, $routes)
    {
        $model_struct = array(
            'path' => $model_path
        );
        
        $model_struct['models'] = array();
        
        foreach ($routes as $route_title => $route_actions)
        {
            $obj_model = new config($model_path . $route_title . '.json');
            $model_json = $obj_model->toObject();
            $model_struct['models'][$route_title] = $model_json['iriki']['models'][$route_title];
        
            //var_dump($route_actions);
        }
        
        return $model_struct;
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
