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

    public function getStatus($status = null, $json = false)
    {
        //engine's routes
        if (is_null($status))
        {
            $status = array('data' => array());
        }

        $status['data']['engine'] = array();
        $status['data']['engine']['name'] = $this->_engine['app']['name'];
        $status['data']['engine']['path'] = $this->_engine['app']['path'];

        
        foreach ($this->_engine['routes']['routes'] as $model => $actions)
        {
            $status['data']['engine']['routes'][] = $model;
        }
        
        //app's routes
        $status['data']['application'] = array();

        $status['data']['application']['name'] = $this->_app['app']['name'];
        $status['data']['application']['path'] = $this->_app['app']['path'];
        
        foreach ($this->_app['routes']['routes'] as $model => $actions)
        {
            $status['data']['application']['routes'][] = $model;
        }

        if ($json)
        {
            return json_encode($status);
        }
        else
        {
            return $status;
        }
    }
    
    //takes in a request url
    //returns matching route
    //a match is 3 part process, depends on iriki mode
    //a specific model or alias
    //a specific action for said model
    //a specific set of parameters for said action
    private function matchRoute($url_params, $engine_models, $app_models, $params)
    {
        $model = null;
        $action = null;

        $model_exists = false;
        $model_is_app_defined = true;
        $action_exists = false;
        
        $count = count($url_params['parts']);

        $status = array();
        
        if ($count != 0)
        {
            //get the model
            $model = $url_params['parts'][0];
            if ($count >= 2) $action = $url_params['parts'][1];

            //note that namespace is important
            $model_instance = null;

            //test for model existence in app
            $app_namespace = $this->_app['app']['name'];
            $model_full = '\\' . $app_namespace . '\\' . $model;
            $model_exists = class_exists($model_full);

            if (!$model_exists)
            {
                //test for model existence in engine
                $engine_namespace = $this->_engine['app']['name'];
                $model_full = '\\' . $engine_namespace . '\\' . $model;
                $model_exists = class_exists($model_full);

                $model_is_app_defined = false;
            }

            if ($model_exists)
            {
                //class_alias($model_full, 'generic_model');
                $model_instance = new $model_full();
            }

            if ($model_exists)
            {
                $model_instance =  new $model_full();


                $action_exists = method_exists($model_full, $action);

                if ($action == 'info') $action_exists = false;

                if ($action_exists)
                {
                    $status = $model_instance->$action($params);
                }
                else
                {
                    //no action specified, display the possible actions, using info
                    $action = 'info';
                    if ($model_is_app_defined)
                    {
                        //find model among the app models
                        $status = $model_instance->$action($model, $action, $app_models);
                    }
                    else
                    {
                        $status = $model_instance->$action($model, $action, $engine_models);
                    }
                }
            }
            else
            {
                $status['error'] = array(
                    'code' => 404,
                    'message' => 'Model not found'
                );
            }
        }
        else
        {
            //no model found, show possible routes?
            $status['error'] = array(
                'code' => 404,
                'message' => 'Url could not be parsed'
            );
        }

        //$get_params = $_GET;//Self::parseGetParams($url_params['query']);
        //$post_params = $_POST;

        return $status; //compact('model', 'action', 'model_exists', 'action_exists');
    }
    
    public function matchRouteUrl($path, $trim_left = '', $engine_models = null, $app_models = null, $params = null)
    {
        $url_parsed = Self::parseUrl($path, $trim_left);

        //check models?

        return $this->matchRoute($url_parsed, $engine_models, $app_models, $params);
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
