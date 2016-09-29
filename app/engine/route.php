<?php

namespace iriki\engine;

require_once(__DIR__ . '/config.php');

class route extends config
{
    private $_config;
    private $_routes;
    private $_path_parts;
    
    public function loadFromJson($path)
    {
        $_routes = array(
            'path' => $path
        );
        
        $_config = new config($path . 'index.json');
        $route_json = $_config->getJson();
        
        $_routes['default'] = $route_json['iriki']['routes']['default'];
        $_routes['alias'] = $route_json['iriki']['routes']['alias'];
        $_routes['routes'] = array();
        
        /*get route details from json file
        if a route file can't be found, it'll have no actions and default
        properties too won't be defined*/
        foreach ($route_json['iriki']['routes']['routes'] as $valid_route)
        {
            $valid_route_json = (new config($path . $valid_route . '.json'))->getJson();
            $_routes['routes'][$valid_route] = $valid_route_json['iriki']['routes'][$valid_route];
        }
        
        return $_routes;
    }

    public function getRoutes()
    {
        return $this->_routes;
    }
    
    //takes in a request url
    //gets matching route with expected commands
    public function matchRoute($requested_url)
    {
        $url_parts = parse_url($requested_url);
        //into scheme, host and path e.g
        /*
          ["scheme"] => "http"
          ["host"] => "cashcrow.me"
          ["path"] => "/api/user/edit/1"
          ["query"] =>
          ["fragment"] =>
        */
        $path = $url_parts['path'];
        
        //convert /api/... to api/...
        $path_trim = ltrim($path, '/');
        
        //explode path
        $this->_path_parts = explode("/", $path_trim);
        
        $path_count = count($path_parts);
        
        return $url_parts; //$this->_path_parts;
    }
}

?>
