<?php

namespace iriki\engine;

require_once(__DIR__ . '/config.php');

class route extends config
{
    private $_config;
    private $_route; //['model', 'action', 'parameters']
    private $_routes;
    private $_path_parts;
    
    public function loadFromJson($path)
    {
        $this->_routes = array(
            'path' => $path
        );
        
        $this->_config = new config($path . 'index.json');
        $route_json = $this->_config->getJson();
        
        $this->_routes['default'] = $route_json['iriki']['routes']['default'];
        $this->_routes['alias'] = $route_json['iriki']['routes']['alias'];
        $this->_routes['routes'] = array();
        
        /*get route details from json file
        if a route file can't be found, it'll have no actions and default
        properties too won't be defined*/
        foreach ($route_json['iriki']['routes']['routes'] as $valid_route)
        {
            $valid_route_json = (new config($path . $valid_route . '.json'))->getJson();
            $this->_routes['routes'][$valid_route] = $valid_route_json['iriki']['routes'][$valid_route];
        }
        
        return $this->_routes;
    }

    public function doInitialise($config_values)
    {
        $this->_route = $this->loadFromJson($config_values['routes']);

        return $this->_routes;
    }

    public function getRoutes()
    {
        return $this->_routes;
    }

    public function getStatus()
    {
        $status = "Routes: ";
        foreach ($this->_routes['routes'] as $model => $actions)
        {
            $status .= $model . ', ';
        }
        
        $status = substr($status, 0, -strlen(', '));
        //Default: actions";

        return $status;
    }
    
    //takes in a request url
    //returns matching route
    //a match is 3 part process
    //a specific model or alias
    //a specific action for said model
    //a specific set of parameters for said action
    private function matchRoute($url_params)
    {
        $model = null;
        $action = null;
        $params = null;
        
        $count = count($url_params['parts']);
        
        //routes already in $_routes;
        if ($count != 0)
        {
            $model = $url_params['parts'][0];
            if ($count >= 2) $action = $url_params['parts'][1];
        }
        else
        {
            
        }
        return compact('model', 'action', 'params');
    }
    
    public function matchRouteUrl($path, $trim_left)
    {
        $url_parsed = Self::parseUrl($path, $trim_left);
        var_dump($url_parsed);
        return $this->matchRoute($url_parsed);
    }
    
    private static function parseUrl($path, $trim_left)
    {
        //trim
        $trimmed = ltrim($path, $trim_left);
        
        //split path
        $parts = explode("/", $trimmed);
        
        $count = count($parts);
        
        return compact('path', 'trimmed', 'parts', 'count');
    }
    
    public function matchModel()
    {
        
    }
}

?>
