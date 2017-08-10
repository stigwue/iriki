<?php

namespace iriki;

/**
* Iriki route, routes to a model's action
*
*/
class route
{
    /**
    * String constant, parameter holding session token
    *
    * @var constant
    */
    const authorization = 'user_session_token';

    /**
    * Matches the requested url to a route, performing a model action.
    *
    *
    * @param array HTTP request details
    * @param array Application configuration already initialised
    * @return array Status of matched model action
    * @throw
    */
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

        $url_params = (isset($request_details['url']['params'])) ? $request_details['url']['params'] : null;

        $count = count($url_parts);

        if ($count >= 1)
        {
            //get the model
            if ($count == 1)
            {
                $model = $url_parts[$count - 1];
            }
            if ($count >= 2)
            {
                $model = $url_parts[$count - 2];
                $action = $url_parts[$count - 1];
            
                if ($count > 2)
                {
                    //handle requests such as /model/action/val1
                    //e.g /user/read/1
                    //note that these parameters would aready have been configured in url_params
                    if (is_null($url_params))
                    {
                        return response::error("URL parameters not defined.");
                    }
                    /*else
                    {
                        $url_params_count = count($url_params);

                    }*/
                }
            }

            //note that namespace is important
            $model_instance = null;

            //test for alias
            //TODO: use a model alias?
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
                    //TODO: third party iriki apps?
                    //distributed somehow?
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
                'action_details' => null //array, action description, parameters, exempt, authenticate
            );

            $model_status = model::doMatch($model_status,
                ($model_is_app_defined ? $app_models : $engine_models),
                ($model_is_app_defined ? $app_routes : $engine_routes)
            );

            //var_dump($request_details, $model_status['action_details']);

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
                        //parameter check
                        //on fail, describe action
                        $parameter_status = model::doPropertyMatch(
                          $model_status['details'],
                          $params,
                          $model_status['action_details']
                        );

                        $missing_parameters = count($parameter_status['missing']);

                        //note that extra parameters could be ids signifying belongsto relationships
                        //so we have to remove checking it from route
                        //to the request
                        if ($missing_parameters == 0)
                        {
                            //check for auth

                            //user_session_token is left as null if action needs not be authenticated
                            //else it is set
                            $user_session_token = null;

                            //get headers
                            $request_headers = getallheaders();

                            if (isset($request_headers[Self::authorization]))
                            {
                                $user_session_token = $request_headers[Self::authorization];
                            }


                            //check for authentication
                            if ($model_status['action_details']['authenticate'] == 'true')
                            {
                                //authentication required, was token found?
                                if (is_null($user_session_token))
                                {
                                    //no, token wasn't found
                                    return response::error('User session token missing.');
                                }
                            }
                            else
                            {
                                //authentication isn't required, ignore it
                                $user_session_token = null;
                            }

                            //persistence
                            //defined in one of two locations
                            engine\database::doInitialise(
                                $app['application'],
                                $app['engine'],
                                $app['database']
                            );

                            $model_instance = new $model_status['str_full']();

                            //build request;
                            $request = request::initialize(
                              engine\database::getClass(), //db_type
                              $model_status,
                              $parameter_status,
                              $params, //data
                              $user_session_token //session
                            );

                            //instance action
                            return $model_instance->$action($request);
                        }
                        else
                        {
                            return response::error(response::showMissing($parameter_status['missing'], 'parameter', 'missing or of wrong type'));
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

    /**
    * Parses the requested url to pull out models, action and queries
    *
    *
    * @param array Request url/path
    * @return array Path, parts of the url and the query
    * @throw
    */
    private static function parseUrl($path)
    {
        //try php's parse_url
        $parsed = parse_url($path);

        $to_parse = $parsed['path'];
        
        //path should not start with /
        if ($to_parse[0] == '/') $to_parse = substr($to_parse, 1);
        
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
        $params = array();

        $count = 0;
        foreach ($parts as $part)
        {
            $model_action[] = $part;
            $count += 1;

            if ($count > 2) $params[] = $part;
        }

        return compact('path', 'parts', 'params', 'query');
    }

    /**
    * Gets the HTTP request details: methods, parameters and so forth
    *
    *
    * @param string URI supplied or deduced
    * @param string HTTP request method supplied or deduced
    * @param string Optional base url if framework isn't run from server root/home?
    * @return array Request details for use
    * @throw
    */
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

        //parameters
        switch ($method) {
          /*case 'PUT':
            do_something_with_put($request);
            break;
          case 'DELETE':
            break;*/

            case 'GET':
                $status['method'] = 'GET';
                $status['params'] = (isset($status['url']['query'])) ? Self::parseGetParams($status['url']['query']) : null;
            break;

            case 'POST':
                $status['method'] = 'POST';
                $params = $_POST;
                $status['params'] = $params;
            break;

            default: 
                //$status['method'] = 'POST';
                $params = $_REQUEST;
                //array_shift($params);
                $status['params'] = $params;
            break;
        }

        return $status;
    }

    /**
    * Read GET parameters from query part of the url
    *
    *
    * @param string Query section of url
    * @return array Key-value query pairs
    * @throw
    */
    private static function parseGetParams($query)
    {
        $get_params = array();
        if (strlen($query) != 0) 
        {
            $key_values = explode("&", $query);
            foreach ($key_values as $key_value)
            {
                $pair = explode('=', $key_value);
                if (count($pair) == 2)
                {
                    //property=value
                    $get_params[$pair[0]] = $pair[1];
                }
                else
                {
                    //property=value=corrupted
                    $get_params[$key_value] = '';
                }
            }
        }
        return $get_params;
    }

}

?>
