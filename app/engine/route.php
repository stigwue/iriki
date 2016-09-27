<?php

namespace mongovc\engine;

require_once(__DIR__ . '/config.php');

class route extends config
{
    //private $_json;
    private $_routes;
    
    public function loadFromJson($path)
    {
        $route_struct = array(
            'path' => $path
        );
        
        $route_json = (new config($path . 'index.json'))->toObject();
        $route_config = $route_json['mongovc']['routes'];
        
        $route_struct['default'] = $route_config['default'];
        $route_struct['alias'] = $route_config['alias'];
        $route_struct['routes'] = array();
        /*get route details from json file
        if a route file can't be found, it'll have no actions but default
        properties too won't be defined*/
        foreach ($route_config['routes'] as $valid_route)
        {
            $valid_route_json = (new config($path . $valid_route . '.json'))->toObject();
            $route_struct['routes'][$valid_route] = $valid_route_json['mongovc']['routes'][$valid_route];
        }
        
        return $route_struct;
    }
    
    //returns route title
    public static function matchRoute($requested_url, $routes)
    {
        $url_parts = parse_url($requested_url);
        //into scheme, host and path e.g
        /*
          ["scheme"]=> "http"
          ["host"]=> "cashcrow.me"
          ["path"]=> "/api/user/edit/1"
        */
        $path = $url_parts['path'];
        
        //convert /api/... to api/...
        $path_trim = ltrim($path, '/');
    }
}

?>
