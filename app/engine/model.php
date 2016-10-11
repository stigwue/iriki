<?php

namespace iriki\engine;

require_once(__DIR__ . '/config.php');

class model extends config
{
    private $_models;
    
    public function loadFromJson($model_path, $routes)
    {
        $this->_models = array(
            'path' => $model_path
        );
        
        $this->_models['models'] = array();
        
        foreach ($routes as $route_title => $route_actions)
        {
            $model_json = (new config($model_path . $route_title . '.json'))->getJson();
            $this->_models['models'][$route_title] = $model_json['iriki']['models'][$route_title];
        }
        
        return $this->_models;
    }

    //essentially its doInitialise
    public function loadModels($app_values, $routes)
    {
        $model_path = $app_values['models'];

        return $this->loadFromJson($model_path, $routes['routes']);
    }

    public function getModels()
    {
        return $this->_models;
    }

    public function getStatus()
    {
        //show model routes and pull model info definitions
        $status = "";

        return $status;
    }
}

?>
