<?php
namespace mongovc\engine;

class route extends config
{
    private $_routes;

    public function __contruct($json_path = '')
    {
        $json = $this->load_json_file($json_path);
    }
    
    public function loadFromJson($path)
    {
        $route_struct = array(
            'path' => $path
        );
        
        $route_json = (new mongovc\engine\config($path . 'index.json'))->toObject();
        $route_config = $route_json['mongovc']['routes'];
        $route_struct['default'] = $route_config['default'];
        $route_struct['alias'] = $route_config['alias'];
        $route_struct['routes'] = array();
        //get route details from json file
        //if a route file can't be found, it'll have no actions but default
        //properties too won't be defined
        foreach ($route_config['routes'] as $valid_route)
        {
            $valid_route_json = (new mongovc\engine\config($path . $valid_route . '.json'))->toObject();
            $route_struct['routes'][$valid_route] = $valid_route_json['mongovc']['routes'][$valid_route];
        }
    }
    
    //returns route title
    public static function matchRoute($requested_url, routes)
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
