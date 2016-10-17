<?php

namespace iriki\engine;

require_once(__DIR__ . '/config.php');

class route extends config
{
    //engine routes
    private $_engine = array(
        'app' => null,
        'config' => null,
        'routes' => null //default, alias, list and routes
    );

    //app routes
    private $_app  = array(
        'app' => null,
        'config' => null,
        'routes' => null
    );

    //url matching
    private $_path_parts;
    
    private function loadFromJsonIndex($config_values, $app = 'iriki')
    {
        $var = '_engine';
        $path = $config_values['engine']['path'];
        if ($app != 'iriki')
        {
            $var = '_app';
            $path = $config_values['application']['path'];
        }
        $store = &$this->$var;

        $store['app'] = array(
            'name' => $app,
            'path' => $path
        );

        $store['config'] = new config($path . 'routes/index.json');
        $route_json = $store['config']->getJson();
        
        $store['routes']['default'] = (isset($route_json[$app]['routes']['default']) ? $route_json[$app]['routes']['default'] : array());

        $store['routes']['alias'] = (isset($route_json[$app]['routes']['alias']) ? $route_json[$app]['routes']['alias'] : array());

        $store['routes']['list'] = (isset($route_json[$app]['routes']['routes']) ? $route_json[$app]['routes']['routes'] : array());

        $store['routes']['routes'] = array();

        return $store['routes'];
    }
    
    private function loadFromJson($config_values, $routes, $app = 'iriki')
    {
        $var = '_engine';
        $path = $config_values['engine']['path'];
        if ($app != 'iriki')
        {
            $var = '_app';
            $path = $config_values['application']['path'];
        }
        $store = &$this->$var;
        
        /*get route details from json file
        if a route file can't be found, it'll have no actions and default
        properties too won't be defined*/
        foreach ($routes['list'] as $valid_route)
        {
            $valid_route_json = (new config($path . 'routes/' . $valid_route . '.json'))->getJson();
            $store['routes']['routes'][$valid_route] = $valid_route_json[$app]['routes'][$valid_route];
        }
        
        return $store['routes'];
    }

    public function doInitialise($config_values, $app = 'iriki')
    {
        $routes = $this->loadFromJsonIndex($config_values, $app);
        
        return $this->loadFromJson($config_values, $routes, $app);
    }

    public function getRoutes($app = 'iriki')
    {
        $var = '_engine';
        if ($app != 'iriki')
        {
            $var = '_app';
        }
        $store = &$this->$var;

        return $store['routes'];
    }

    public function getStatus()
    {
        //engine's routes
        $status = "Engine: " . $this->_engine['app']['name'];
        $status .= " (";
        foreach ($this->_engine['routes']['routes'] as $model => $actions)
        {
            $status .= $model . ', ';
        }
        
        if (substr($status, -strlen(', ')) == ', ')
        {
            $status = substr($status, 0, -strlen(', '));
        }
        $status .= ")
";
        
        //app's routes
        $status .= "Application: " . $this->_app['app']['name'];
        $status .= " (";
        foreach ($this->_app['routes']['routes'] as $model => $actions)
        {
            $status .= $model . ', ';
        }

        if (substr($status, -strlen(', ')) == ', ')
        {
            $status = substr($status, 0, -strlen(', '));
        }
        $status .= ")
";
        

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
        //try php's parse_url
        $parsed = parse_url($path);

        $to_parse = $parsed['path'];

        //trim
        //$trimmed = ltrim($path, $trim_left);
        $trimmed = $path;
        if (substr($path, 0, strlen($trim_left)) == $trim_left)
        {
            $trimmed = substr($path, strlen($trim_left));
        }
        
        //split path
        $parts = explode("/", $trimmed);
        
        $count = count($parts);

        var_dump($parsed);
        
        return compact('path', 'trimmed', 'parts', 'count');
    }
    
    public function matchModel()
    {
        
    }
}

?>
