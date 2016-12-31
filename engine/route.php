<?php

namespace iriki;

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

    private function loadFromJsonIndex($config_values, $app = 'iriki')
    {
        $var = '_engine';
        $path = $config_values['engine']['path'];
        if ($app != 'iriki')
        {
            $var = '_app';
            $path = $config_values[$app]['path'];
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
            $path = $config_values[$app]['path'];
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

    //matches route with supplied url
    //a match is 2 part process
    //1. a route/alias is matched to a specific model
    //2. we go into said model to further the match
    public function matchUrl($request_details,
        $models = null,
        $routes = null,
        $database = null
    )
    {
        //models
        $app_models = null;
        $engine_models = null;
        if (!is_null($models))
        {
            $app_models = $models['app'];
            $engine_models = $models['engine'];
        }

        //routes
        $app_routes = null;
        $engine_routes = null;
        if (!is_null($routes))
        {
            $app_routes = $routes['app'];
            $engine_routes = $routes['engine'];
        }

        $model = null;
        $action = null;

        $model_defined = false;
        $model_exists = false;
        $model_is_app_defined = true;
        $action_exists = false;

        $params = (isset($request_details['params'])) ? $request_details['params'] : null;
        $url_parts = (isset($request_details['url']['parts'])) ? $request_details['url']['parts'] : null;

        $count = count($url_parts);

        $status = array(
            "error" => array(
                "code" => 404,
                "message" => "Url could not be matched with a route."
            )
        );

        if ($count >= 1)
        {
            //get the model
            if ($count >= 2)
            {
                $model = $url_parts[$count - 2];
                $action = $url_parts[$count - 1];
            }
            else
            {
                $model = $url_parts[$count - 1];
            }

            //note that namespace is important
            $model_instance = null;

            //test for alias
            if ($model == 'alias')
            {
                //set model and action

                //test for model existence is a configuration search in app then engine
                $model_is_app_defined = isset($routes['app']['alias'][$action]);
                $model_is_engine_defined = isset($routes['engine']['alias'][$action]);

                $define_switch = 'engine';
                if ($model_is_app_defined)
                {
                    $define_switch = 'app';
                }
                $model = $routes[$define_switch]['alias'][$action]['model'];
                $action = $routes[$define_switch]['alias'][$action]['action'];
            }
            else
            {
                $model_defined = (
                    (isset($models['app'][$model]))
                        OR
                    (isset($models['engine'][$model]))
                );

                if (!$model_defined)
                {
                    return response::error('Model is not defined.');
                }

                //test for model existence is a configuration search in app then engine
                $model_is_app_defined = isset($models['app'][$model]);
                //confirm using route
                $route_is_app_defined = isset($routes['app']['routes'][$model]);

                if ($model_is_app_defined != $route_is_app_defined)
                {
                    //something's wrong
                    //model and route definitions are across app/engine boundary
                    //might have to explain further

                    return response::error('Model and route not defined in the same space.');
                }
            }

            //class exist test
            $app_namespace = ($model_is_app_defined ?
                $this->_app['app']['name'] :
                $this->_engine['app']['name']
            );
            $model_full = '\\' . $app_namespace . '\\' . $model;

            $model_exists = class_exists($model_full);
            $action_exists = method_exists($model_full, $action);

            $model_status = array(
                'str' => $model, //model e.g session
                'str_full' => $model_full, //full model string including namespace
                'defined' => $model_defined, //model defined in app or engine config
                'exists' => $model_exists, //model class exists
                'details' => null, //array of description, properties and relationships
                'app_defined' => $model_is_app_defined, //model defined in app or engine?
                'action'=> $action, //action e.g create
                'action_defined' => false, //action defined?
                'action_default' => false, //action is default defined?
                'action_exists' => $action_exists, //action exists in class
                'action_details' => null //array of action description and parameters
            );

            $model_status = model::doMatch($model_status,
                ($model_is_app_defined ? $app_models : $engine_models),
                ($model_is_app_defined ? $app_routes : $engine_routes)
            );

            /*
            perform action based on $model_status
            order of priority is this:
            0. model/action must be configured in config space
            1. model/action must be defined in code space
            */

            //model definition already determined

            //model class does not exist
            if ($model_status['exists'] == false)
            {
                return response::error($model_status['str_full'] . ' does not exist.');
            }
            else
            {
                //action defined?
                if ($model_status['action_defined'])
                {
                    //action exists
                    $status = array();
                    $status['error'] = array(
                        'code' => 404,
                        'message' => $model_status['str_full'] . ' does not exist'
                    );
                }
                else
                {
                    //provide description?
                    if (isset($model_status['details']['description']))
                    {
                        $status = array();
                        $status['info'] = array(
                            'code' => 200,
                            'message' => $model_status['details']['description']
                        );
                    }
                }
            }

            /*if ($model_status['exists'] AND $model_status['action_exists'])
            {
                $model_instance =  new $model_status['str_full']();

                //bring in database, defined in one of these two locations
                //$this->_app['app']['name'] :
                //$this->_engine['app']['name']

                database::doInitialise(
                    $this->_app['app']['name'],
                    $this->_engine['app']['name'],
                    $database
                );

                //instance action
                $status = $model_instance->$action(
                    database::getClass(),
                    array(
                        'data' => $params,
                        'persist' => $model
                    )
                );
            }
            else if (!$model_status['exists'])
            {
                if (isset($model_status['details']['description']))
                {
                    $status = array();
                    $status['info'] = array(
                        'code' => 200,
                        'message' => $model_status['details']['description']
                    );
                }
            }
            else if (!$model_status['action_exists'])
            {
                if (isset($model_status['action_details']['description']))
                {
                    $status = array();
                    $status['info'] = array(
                        'code' => 200,
                        'message' => $model_status['action_details']['description']
                    );
                }
            }*/
        }

        return $status;
    }

    private static function parseUrl($path)
    {
        //try php's parse_url
        $parsed = parse_url($path);

        $to_parse = $parsed['path'];

        $query = '';

        if (isset($parsed['query']))
        {
            $query = $parsed['query'];
        }

        //split path
        $parts = explode("/", $to_parse);
        //clear empties
        $parts = array_filter($parts);
        //reset index
        $model_action = array();

        foreach ($parts as $part)
        {
            $model_action[] = $part;
        }

        $parts = $model_action;


        return compact('path', 'parts', 'query');
    }

    public static function getRequestDetails($uri = null, $method = null, $base_url = '')
    {
        if (is_null($uri))
        {
            $uri = $_SERVER['REQUEST_URI'];
        }

        if ($base_url != '')
        {
            //trim uri by base

        	//optional step
        	//if you are running this framework from
        	//foobar.com/*iriki* then ignore
        	//or else, if running from foobar.com/some/weird/path/*iriki* then
        	//shorten url by /some/weird/path
        	$uri = substr($uri, strlen($base_url));
        }

        if (is_null($method))
        {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $status = array(
            'url' => Self::parseUrl($uri),
            'method' => $method,
            'params' => null
        );

        switch ($method) {
          /*case 'PUT':
            do_something_with_put($request);
            break;*/
          /*case 'HEAD':
            break;
          case 'DELETE':
            break;
          case 'OPTIONS':
            break;*/

            case 'GET':
                $status['method'] = 'GET';
                //$params = $_GET;
                //array_shift($params);
                $status['params'] = (isset($status['url']['query'])) ? Self::parseGetParams($status['url']['query']) : null;
            break;

            case 'POST':
                $status['method'] = 'POST';
                $params = $_POST;
                //array_shift($params);
                $status['params'] = $params;
            break;

            default: //case 'POST':
                //$status['method'] = 'POST';
                $params = $_REQUEST;
                //array_shift($params);
                $status['params'] = $params;
            break;
        }

        return $status;
    }

    private static function parseGetParams($query)
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
    }

}

?>
