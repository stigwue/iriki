<?php

namespace iriki;

class route extends config
{
    /**
    * Engine's routes.
    *
    * @var array
    */
    private static $_engine = array(
        //app details
        'app' => null,
        //config object
        'config' => null,
        //routes: default, alias, list and routes
        'routes' => null
    );

    /**
    * Application's routes.
    *
    * @var array
    */
    private static $_app = array(
        'app' => null,
        'config' => null,
        'routes' => null
    );

    /**
    * Load route index details from supplied app configuration.
    *
    *
    * @param array Configuration key value pairs
    * @param string Application name
    * @return
    * @throw
    */
    private function loadFromJsonIndex($config_values, $app = 'iriki')
    {
        $store = &Self::$_engine;
        $path = $config_values['engine']['path'];
        if ($app != 'iriki')
        {
            $store = &Self::$_app;
            $path = $config_values['application']['path'];
        }

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
        $store = &Self::$_engine;
        $path = $config_values['engine']['path'];
        if ($app != 'iriki')
        {
            $store = &Self::$_app;
            $path = $config_values['application']['path'];
        }


        /*get route details from json file
        if a route file can't be found, it'll have no actions and default
        properties too won't be defined*/
        foreach ($routes['list'] as $valid_route)
        {
            //var_dump($path . 'routes/' . $valid_route . '.json');
            $valid_route_json = (new config($path . 'routes/' . $valid_route . '.json'))->getJson();
            //var_dump($valid_route_json[$app]['routes']);
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
        $store = &Self::$_engine;
        if ($app != 'iriki')
        {
            $store = &Self::$_app;
        }

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

        $status['data']['engine']['routes'] = array();
        foreach ($this->_engine['routes']['routes'] as $model => $actions)
        {
            $status['data']['engine']['routes'][] = $model;
        }

        //app's routes
        $status['data']['application'] = array();

        $status['data']['application']['name'] = $this->_app['app']['name'];
        $status['data']['application']['path'] = $this->_app['app']['path'];

        $status['data']['application']['routes'] = array();
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
    public static function matchUrl($request_details,
        $app = null
    )
    {
        //app must be initialised
        if (is_null($app)) return null;

        //models
        $engine_models = $app['models']['engine'];
        $app_models = $app['models']['app'];

        //routes
        $engine_routes = $app['routes']['engine'];
        $app_routes = $app['routes']['app'];

        $model = null;
        $action = null;
        $defaults = null;

        $model_defined = false;
        $model_exists = false;
        $model_is_app_defined = true;
        $action_exists = false;

        $params = (isset($request_details['params'])) ? $request_details['params'] : null;
        $url_parts = (isset($request_details['url']['parts'])) ? $request_details['url']['parts'] : null;

        $count = count($url_parts);

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
                $model_is_engine_defined = isset($engine_routes['alias'][$action]);
                $model_is_app_defined = isset($app_routes['alias'][$action]);

                if ($model_is_engine_defined)
                {
                    $model = $engine_routes['alias'][$action]['model'];
                    $action = $engine_routes['alias'][$action]['action'];
                }
                else if ($model_is_app_defined)
                {
                    $model = $app_routes['alias'][$action]['model'];
                    $action = $app_routes['alias'][$action]['action'];
                }
                else
                {
                    //?
                }
            }
            else
            {
                $model_defined = (
                    (isset($app_models[$model]))
                        OR
                    (isset($engine_models[$model]))
                );

                if (!$model_defined)
                {
                    return response::error('Model \'' . $model . '\' is not defined.');
                }

                //test for model existence is a configuration search in app then engine
                $model_is_app_defined = isset($app_models[$model]);
                //confirm using route
                $route_is_app_defined = isset($app_routes['routes'][$model]);

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
                $app['application'] :
                $app['engine']
            );
            $model_full = '\\' . $app_namespace . '\\' . $model;

            $model_exists = class_exists($model_full);
            $action_exists = method_exists($model_full, $action);

            $defaults = $engine_routes['default'];
            if ($model_is_app_defined)
            {
                $defaults = $app_routes['default'];
            }

            $model_status = array(
                'str' => $model, //string, model
                'str_full' => $model_full, //string, full model including namespace
                'defined' => $model_defined, //boolean, model defined in app or engine config
                'exists' => $model_exists, //boolean, model class exists
                'details' => null, //array, model description, properties and relationships
                'app_defined' => $model_is_app_defined, //boolean, model defined in app. otherwise engine
                'action'=> $action, //string, action
                'default' => $defaults, //array, default actions
                'action_defined' => false, //boolean, action defined
                'action_default' => false, //boolean, action is default defined
                'action_exists' => $action_exists, //boolean, action exists in class
                'action_details' => null //array, action description, parameters, exempt
            );

            $model_status = model::doMatch($model_status,
                ($model_is_app_defined ? $app_models : $engine_models),
                ($model_is_app_defined ? $app_routes : $engine_routes)
            );

            /*
            perform action based on $model_status
            order of priority is this:
            0. model/action must be in config space (defined)
            1. model/action must be in code space (exists)
            2. action can be default or custom
            */

            //model class does not exist
            if ($model_status['exists'] == false)
            {
                return response::error($model_status['str_full'] . ' does not exist.');
            }
            else
            {
                //action defined? plus exception made for default defined action
                if ($model_status['action_defined'] OR $model_status['action_default'])
                {
                    //action exists?
                    if ($model_status['action_exists'])
                    {
                        //paramter check
                        //on fail, describe action

                        $parameter_status = model::doPropertyMatch($model_status['details'], $params, $model_status['action_details']);

                        $missing_parameters = count($parameter_status['missing']);
                        //$extra_parameters = count($parameter_status['extra']);

                        //note that extra parameters could be ids signifying belongsto relationships
                        //so we have to leave that check until later
                        if ($missing_parameters == 0) // AND $extra_parameters == 0)
                        {
                            //persistence
                            //defined in one of two locations
                            engine\database::doInitialise(
                                $app['application'],
                                $app['engine'],
                                $app['database']
                            );

                            $model_instance = new $model_status['str_full']();

                            //build request
                            $request = request::initialize(
                              engine\database::getClass(), //db_type
                              $model_status,
                              $parameter_status,
                              $params //data
                              //session
                            );

                            //instance action
                            return $model_instance->$action($request);
                        }
                        else
                        {
                            if ($missing_parameters != 0)
                            {
                                return response::error(response::showMissing($parameter_status['missing'], 'parameter', 'missing'));
                            }
                            
                            //authorisation or other error
                            return response::error('Authorisation missing.');
                        }
                    }
                    else
                    {
                        return response::error('Action \'' . $model_status['action'] . '\' of ' . $model_status['str_full'] . ' does not exist.');
                    }
                }
                else
                {
                    //provide model description and possible actions
                    if (isset($model_status['details']['description']))
                    {
                        return response::information(
                            $model_status['details']['description']
                        );
                    }
                }
            }
        }

        //all other parsing failed
        return null; //response::error('Please specify a route.');
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
