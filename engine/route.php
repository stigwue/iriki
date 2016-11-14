<?php

namespace iriki\engine;

//require_once(__DIR__ . '/autoload.php');
//require_once(__DIR__ . '/../app/autoload.php');
//require_once(realpath(__DIR__ . '/..') . '/app/autoload.php');

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
    //a match is 3 part process, depends on iriki mode
    //a specific model or alias
    //a specific action for said model
    //a specific set of parameters for said action
    private function matchRoute($url_params)
    {
        $model = null;
        $action = null;

        $model_exists = false;
        $action_exists = false;
        
        $count = count($url_params['parts']);
        
        if ($count != 0)
        {
            //get the model
            $model = $url_params['parts'][0];
            if ($count >= 2) $action = $url_params['parts'][1];

            //note that namespace is important
            $model_instance = new generic_model();

            //test for model existence in app
            $app_namespace = $this->_app['app']['name'];
            $model_full = '\\' . $app_namespace . '\\' . $model;
            $model_exists = class_exists($model_full);

            //$model_exists = class_exists('\iriki\session');

            if (!$model_exists)
            {
                //test for model existence in engine
                $engine_namespace = $this->_app['engine']['name'];
                $model_full = '\\' . $engine_namespace . '\\' . $model;
                $model_exists = class_exists($model_full);
            }

            if ($model_exists)
            {
                class_alias($model_full, 'generic_model');
            }

            if ($model_exists)
            {
                //$action_exists = method_exists($model_instance, $action);

                $action_exists = method_exists($model_full, $action);

                if ($action_exists)
                {
                    $x = new generic_model();
                    var_dump($x);
                    var_dump($model_instance);
                }
                else
                {

                }
            }
            else
            {

            }
        }
        else
        {
            //no model found, default to routes info?
        }

        //$get_params = $_GET;//Self::parseGetParams($url_params['query']);
        //$post_params = $_POST;

        return compact('model', 'action', 'model_exists', 'action_exists');
    }
    
    public function matchRouteUrl($path, $trim_left = '', $post_params = null, $get_params = null, $cookie_params = null)
    {
        $url_parsed = Self::parseUrl($path, $trim_left);

        //check models?

        return $this->matchRoute($url_parsed);
    }
    
    private static function parseUrl($path, $trim_left)
    {
        //try php's parse_url
        $parsed = parse_url($path);

        $to_parse = $parsed['path'];

        isset($parsed['query']) ? $query = $parsed['query'] : $query = null;

        //trim
        $trimmed = $to_parse;
        if (strlen($trim_left) != 0 )
        {
            if (substr($to_parse, 0, strlen($trim_left)) == $trim_left)
            {
                $trimmed = substr($to_parse, strlen($trim_left));
            }
        }
        
        //split path
        $parts = explode("/", $trimmed);
        
        return compact('path', 'trimmed', 'parts', 'query');
    }

    /*private static function parseGetParams($query)
    {
        $get_params = array();
        $key_values = explode("&", $query);
        foreach ($key_values as $key_value)
        {
            $pair = explode('=', $key_value);
            if (count($pair) == 2)
            {
                $get_params[$pair[0]] = $pair[1];
            }
            else
            {
                $get_params[$key_value] = '';
            }
        }
        return $get_params;
    }*/
    
    public function matchModel($model, $action, $get_params, $params)
    {
        
    }
}

?>
