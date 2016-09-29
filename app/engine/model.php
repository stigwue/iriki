<?php

namespace iriki\engine;

require_once(__DIR__ . '/config.php');

class model extends config
{
    private $_models;
    
    public function loadFromJson($model_path, $routes)
    {
        $_models = array(
            'path' => $model_path
        );
        
        $_models['models'] = array();
        
        foreach ($routes as $route_title => $route_actions)
        {
            $model_json = (new config($model_path . $route_title . '.json'))->getJson();
            $_models['models'][$route_title] = $model_json['iriki']['models'][$route_title];
        }
        
        return $_models;
    }

    public function getModels()
    {
        return $this->_models;
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
