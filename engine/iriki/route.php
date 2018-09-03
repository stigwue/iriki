<?php

namespace iriki\engine;

/**
* Iriki route, routes to a model's action
*
*/
class route
{
    /**
    * String constant, header parameter holding session token.
    *
    */
    const authorization = 'user_session_token';

    /**
    * String constant, header parameter holding authenticated user.
    *
    */
    const user_authenticate = 'user_authenticate';

    /**
    * String constant, header parameter holding authenticated user groups.
    *
    */
    const user_group_authenticate = 'user_group_authenticate';

    /**
    * String constant, header parameter holding user groups to not authenticate.
    *
    */
    const user_group_authenticate_not = 'user_group_authenticate_not';

    /**
    * Parse the HTTP request details before matching existing models.
    * Note that all HTTP method parameters will be returned for later filtering.
    *
    * @param base_url Optional base url if framework isn't run from server root/home. Default is ''.
    * @return An associative array of request details: url (properties include path, parts, parameters and the query) and parameters ('params' with HTTP method keys and a dictionary of parameters supplied) or an error response.
    * @throw
    */
    public static function parseRequest($base_url = '')
    {
        //get the details of the request
        $request_details = \iriki\engine\url::getRequestDetails(null, $base_url);

        //check the details
        /*
        url [path, parts, parameters, query],
        params: array of [method, data]
        */

        $url_parts = (isset($request_details['url']['parts'])) ? $request_details['url']['parts'] : null;

        $url_parameters = (isset($request_details['url']['parameters'])) ? $request_details['url']['parameters'] : null;

        $params = (isset($request_details['params'])) ? $request_details['params'] : null;

        //check url_parts count
        //typically, Iriki uses the model/action/parameters....
        //format, so count(url_parts) must be at least 1

        $url_parts_count = count($url_parts);

        if ($url_parts_count >= 1)
        {
            //get the model and action
            $result = array(
                'model' => null,
                'action' => null,
                'url_parameters' => $url_parameters,
                'params' => $params
            );

            if ($url_parts_count == 1)
            {
                $result['model'] = $url_parts[$url_parts_count - 1];
                //set the default action?
                //$result['action'] = 'info';
            }
            if ($url_parts_count >= 2)
            {
                $result['model'] = $url_parts[0];
                $result['action'] = $url_parts[1];
            
                if ($url_parts_count > 2)
                {
                    //handle requests such as /model/action/val1
                    //e.g /user/read/1
                    //note that these parameters would aready have been configured as url_parameters
                    if (is_null($url_parameters))
                    {
                        return response::error("URL parameters not defined.");
                    }
                }
            }

            return response::data($result);
        }
        else
        {
            return response::error("This is an Iriki application. URL should be in the right format or else, Abuja, we have a problem.", true);
        }
    }

    /**
    * Given a model action, build a profile that defines availability, filling out aliases.
    * @param app Application configuration already initialised.
    * @param request_details Request details. This is only passed in so determine if an error occurred in the parseRequest stage.
    * @return Status of matched model action. See especially code and message properties.
    * @throw
    */
    public static function buildModelProfile($app = null, $request_details)
    {
        //default model profile to fill
        $model_profile = array(
            //string, model
            'str' => null, 
            //string, full model including namespace
            'str_full' => null, 
            //boolean, model defined in app or engine config
            'defined' => false, 
            //boolean, model class exists in code
            'exists' => false, 
            //array, model description, properties and relationships
            'details' => null,
            //boolean, model defined in app. otherwise engine 
            'app_defined' => false, 
            //string, action
            'action'=> null, 
            //array, default actions
            'default' => null, 
            //boolean, action defined
            'action_defined' => false, 
            //boolean, action is defined in default actions
            'action_default' => false, 
            //boolean, action exists in class
            'action_exists' => false, 
            //array, action description, parameters, exempt, authenticate
            'action_details' => null,

            'code' => response::OK,
            'message' => ''
        );

        //app must be initialised
        if (is_null($app))
        {
            $model_profile['code'] = response::ERROR;
            $model_profile['message'] = "Iriki application not initialised.";

            return $model_profile;
        }
        //request_details must be OK
        else if (isset($request_details['code']) AND ($request_details['code'] != response::OK))
        {
            //some previous error contained in request_details
            $model_profile['code'] = $request_details['code'];
            $model_profile['message'] = $request_details['message'];

            return $model_profile;
        }

        //existing models and routes
        $existing = array(
            'name' => array(
                'engine' => $app['engine'],
                'application' => $app['application']
            ),
            'models' => array(
                'app' => $app['models']['app'],
                'engine' => $app['models']['engine']
            ),
            'routes' => array(
                'app' => $app['routes']['app'],
                'engine' => $app['routes']['engine']
            )
        );

        //models
        $engine_models = $app['models']['engine'];
        $app_models = $app['models']['app'];

        //routes
        $engine_routes = $app['routes']['engine'];
        $app_routes = $app['routes']['app'];

        //check if the supplied model is an alias e.g
        //api/alias/the_alias => api/model/action
        if ($request_details['data']['model'] == 'alias')
        {
            $alias = array(
                'model' => null, 
                'action' => $request_details['data']['action'],
                'details' => null,
                'default' => null
            );
            //where is the alias defined? engine or app?
            //then we can read the model & action it points to
            $alias_is_defined = array(
                'in_app' => isset($existing['routes']['app']['alias'][$alias['action']]),
                'in_engine' => isset($existing['routes']['engine']['alias'][$alias['action']])
            );

            if ($alias_is_defined['in_app'] OR $alias_is_defined['in_engine'])
            {
                if ($alias_is_defined['in_app'])
                {
                    $alias['model'] = $existing['routes']['app']['alias'][$alias['action']]['model'];
                    $alias['action'] = $existing['routes']['app']['alias'][$alias['action']]['action'];

                    $alias['details'] = $existing['models']['app'][$alias['model']];
                    $alias['default'] = $existing['routes']['app']['default'];
                }
                elseif ($alias_is_defined['in_engine'])
                {
                    $alias['model'] = $existing['routes']['engine']['alias'][$alias['action']]['model'];
                    $alias['action'] = $existing['routes']['engine']['alias'][$alias['action']]['action'];

                    $alias['details'] = $existing['models']['engine'][$alias['model']];
                    $alias['default'] = $existing['routes']['engine']['default'];
                }

                $model_profile['str'] = $alias['model'];
                //$model_profile[str_full'] = null;
                $model_profile['defined'] = true; 
                //$model_profile['exists'] = false;
                $model_profile['details'] = $alias['details'];
                $model_profile['app_defined'] = $alias_is_defined['in_app'];
                $model_profile['action'] = $alias['action']; 
                $model_profile['default'] = $alias['default'];

                $model_profile = Self::buildActionProfile($existing, $model_profile);
            }
            else
            {
                //alias not found in app or engine
                $model_profile['code'] = response::ERROR;
                $model_profile['message'] = "Aliased model not found in application or engine.";

                return $model_profile;
            }
        
        }
        else
        {
            //where is the model defined? engine or app?
            $route = array(
                'model' => $request_details['data']['model'], 
                'action' => $request_details['data']['action'],
                'details' => null,
                'default' => null
            );

            $model_is_defined = array(
                'in_app' => isset($existing['models']['app'][$route['model']]),
                'in_engine' => isset($existing['models']['engine'][$route['model']])
            );

            if ($model_is_defined['in_app'] OR $model_is_defined['in_engine'])
            {
                if ($model_is_defined['in_app'])
                {
                    $route['details'] = $existing['models']['app'][$route['model']];
                    $route['default'] = $existing['routes']['app']['default'];
                }
                elseif ($model_is_defined['in_engine'])
                {
                    $route['details'] = $existing['models']['engine'][$route['model']];
                    $route['default'] = $existing['routes']['engine']['default'];
                }

                $model_profile['str'] = $route['model'];
                //$model_profile[str_full'] = null;
                $model_profile['defined'] = true; 
                //$model_profile['exists'] = false;
                $model_profile['details'] = $route['details'];
                $model_profile['app_defined'] = $model_is_defined['in_app'];
                $model_profile['action'] = $route['action']; 
                $model_profile['default'] = $route['default'];

                $model_profile = Self::buildActionProfile($existing, $model_profile);
            }
            else
            {
                //model not found in app or engine

                $model_profile['code'] = response::ERROR;
                $model_profile['message'] = 'Model \'' . $route['model'] . '\' is not defined.';

                return $model_profile;
            }
        }

        return $model_profile;
    }

    /**
    * Given an action, build a profile that defines it.
    * @param existing Application model and routes.
    * @param model_profile Already built model profile to complete.
    * @return Status of matched action.
    * @throw
    */
    public static function buildActionProfile($existing, $model_profile)
    {
        //we can't have a model defined in an app and its action defined in the engine
        //not that it can't work but it is bad practice
        //a user should be aware of the defaults they have set

        //please note that some already filled model_profile properties
        //can be used to infer some of the later conditions

        $action_is_defined = array(
            'in_custom' => false,
            'in_default' => false
        );

        if ($model_profile['app_defined'])
        {
            // we are already looking in the app space
            //default is already preloaded by app defaults
            $action_is_defined = array(
                'in_custom' => isset($existing['routes']['app']['routes'][$model_profile['str']][$model_profile['action']]),
                'in_default' => isset($model_profile['default'][$model_profile['action']])
            );

            $model_profile['str_full'] = '\\' . $existing['name']['application'] . '\\' . $model_profile['str'];
        }
        else
        {
            //engine space
            $action_is_defined = array(
                'in_custom' => isset($existing['routes']['engine']['routes'][$model_profile['str']][$model_profile['action']]),
                'in_default' => isset($model_profile['default'][$model_profile['action']])
            );

            $model_profile['str_full'] = '\\' . $existing['name']['engine'] . '\\' . $model_profile['str'];
        }

        //test for model exists (exists)
        $model_profile['exists'] = class_exists($model_profile['str_full']);

        if ($action_is_defined['in_custom'] OR $action_is_defined['in_default'])
        {
            $model_profile['action_defined'] = true;
            if ($action_is_defined['in_custom'])
            {
                $model_profile['action_default'] = false;
                //$model_profile['action_exists'] = false;

                //app or engine?
                if ($model_profile['app_defined'])
                {
                    $model_profile['action_details'] = $existing['routes']['app']['routes'][$model_profile['str']][$model_profile['action']];
                }
                else
                {
                    $model_profile['action_details'] = $existing['routes']['engine']['routes'][$model_profile['str']][$model_profile['action']];
                }
            }
            else //default
            {
                $model_profile['action_default'] = true;
                //$model_profile['action_exists'] = false;

                //default already set from app or engine
                $model_profile['action_details'] = $model_profile['default'][$model_profile['action']];
            }


            //at this point, the model_profile property not yet set
            //is that of the actual class method
            $model_profile['action_exists'] = method_exists($model_profile['str_full'], $model_profile['action']);
        }
        return $model_profile;
    }

    /**
    * Matches the request to a route, performing a model action.
    *
    * @param app Application configuration already initialised.
    * @param model_profile Matched model profile.
    * @param request_details Details of the HTTP request.
    * @param test_mode Test mode to disable authentication needs. Use very sparingly. Default is false.
    * @param user_session_token For cases where setting headers isn't possible, add user session token here.
    * @return Status of matched model action.
    * @throw
    */
    public static function matchRequestToModel($app = null, $model_status, $request_details, $test_mode = false, $user_session_token = null
    )
    {
        //model_status may contain an error message
        if (isset($model_status['code']) AND ($model_status['code'] != response::OK))
        {
            return response::error($model_status['message']);
        }

        //model class exists
        if (!$model_status['exists'])
        {
            return response::error($model_status['str_full'] . ' does not exist.');
        }
        else
        {
            //fill in action details
            if ($model_status['app_defined'])
            {
                $model_status = model::getActionDetails(
                    $model_status['str'],
                    $model_status,
                    $app['routes']['app']['routes']
                );
            }
            else
            {
                $model_status = model::getActionDetails(
                    $model_status['str'],
                    $model_status,
                    $app['routes']['engine']['routes']
                );
            }

            //action defined? plus exception made for default defined action
            if ($model_status['action_defined'] OR $model_status['action_default'])
            {
                //action exists?
                if (!$model_status['action_exists'])
                {
                    return response::error('Action \'' . $model_status['action'] . '\' of ' . $model_status['str_full'] . ' does not exist.');
                }
                else
                {
                    //parameter check
                    //on fail, describe action
                    $params = $request_details['data']['params'];
                    $url_parameters = $request_details['data']['url_parameters'];

                    $parameter_status = model::doPropertyMatch(
                      $model_status['details'],
                      $params,
                      $url_parameters,
                      $model_status['action_details']
                    );

                    $missing_parameters = count($parameter_status['missing']);

                    //note that extra parameters could be ids signifying belongsto relationships
                    //so we have to remove checking it from route
                    //to the request
                    if ($missing_parameters != 0)
                    {
                        return response::error(response::showMissing($parameter_status['missing'], 'parameter', 'missing or of wrong type'));
                    }
                    else
                    {
                        //check for auth
                        //user_session_token is null or already supplied
                        $valid_user = null;
                        $valid_user_groups = array();
                        $valid_user_groups_not = array();

                        //get headers, please note that this function might not be available
                        //for instance, via the CLI

                        if (function_exists('getallheaders'))
                        {
                            //this might replace already supplied token with the one in header
                            //if one has been supplied, do not use header's
                            $request_headers = getallheaders();
                            
                            if (isset($request_headers[Self::authorization]) AND is_null($user_session_token) == true)
                            {
                                $user_session_token = $request_headers[Self::authorization];
                            }

                            if (isset($request_headers[Self::user_authenticate]))
                            {
                                $valid_user = $request_headers[Self::user_authenticate];
                            }

                            if (isset($request_headers[Self::user_group_authenticate]))
                            {
                                $valid_user_groups = $request_headers[Self::user_group_authenticate];
                            }

                            if (isset($request_headers[Self::user_group_authenticate_not]))
                            {
                                $valid_user_groups_not = $request_headers[Self::user_group_authenticate_not];
                            }
                        }

                        if ($test_mode)
                        {
                            //ignore auth checks
                        }
                        else
                        {
                            /*
                            Authentication logic is two step:
                            1. If a specific authentication is set, see if the necessary variable was provided.
                            2. If the variable is provided, test it.
                            */

                            //This is the first step. The second is done just before the action.

                            //check for user authentication
                            if ($model_status['action_details']['authenticate'] == true)
                            {
                                //authentication required, was token found?
                                if (is_null($user_session_token))
                                {
                                    //no, token wasn't found
                                    return response::error('User session token missing.');
                                }

                                //user auth
                                if ($model_status['action_details']['user_authenticate'] == true)
                                {
                                    if (is_null($valid_user))
                                    {
                                        //no user groups supplied
                                        return response::error('User to authenticate missing.');
                                    }
                                }

                                /*
                                group auth logic:
                                */

                                //group authentication
                                if (count($model_status['action_details']['user_group_authenticate']) != 0)
                                {
                                    if (count($valid_user_groups) == 0)
                                    {
                                        //no user groups supplied
                                        return response::error('User groups to authenticate missing.');
                                    }
                                }
                                //group not authentication
                                if (count($model_status['action_details']['user_group_authenticate_not']) != 0)
                                {
                                    if (count($valid_user_groups_not) == 0)
                                    {
                                        //no user groups supplied
                                        return response::error('User groups to not authenticate missing.');
                                    }
                                }
                            }
                            else
                            {
                                //authentication isn't required, ignore it
                                $user_session_token = null;
                            }
                        }

                        //persistence
                        if (!isset($app['database']['type']))
                        {
                            return response::error('Database type definition missing.');
                        }
                        else
                        {
                            $db_class = new $app['database']['type'];
                            $db_handle = $db_class::doInitialise($app['database']);

                            if (is_null($db_handle))
                            {
                                return response::error('Database type undefined.');
                            }
                            else
                            {
                                $model_instance = new $model_status['str_full']();
                                $action = $model_status['action'];

                                //build request;
                                $request = new request();
                                //test mode
                                $request->setTestMode($test_mode);
                                //session
                                $request->setSession($user_session_token);
                                //db_instance
                                $request->setDBInstance($db_class);
                                //model status
                                $request->setModelStatus($model_status);
                                //parameter_status
                                $request->setParameterStatus($parameter_status);
                                //data
                                $request->setData($params);
                                //meta
                                //parameters explicitly defined?
                                $request->setTag($parameter_status['explicit_def']);

                                //instance action
                                return $model_instance->$action($request);
                            }
                        }
                    }
                }
            }
            else
            {
                return response::error('No valid action specified.');
            }
        }
    }

    /**
    * Combine the two after-parse-request steps: buildModelProfile and matchRequestToModel.
    *
    * @param request_details Details of the HTTP request.
    * @param app Application configuration already initialised.
    * @param test_mode Test mode to disable authentication needs. Use very sparingly. Default is false.
    * @param user_session_token For cases where setting headers isn't possible, add user session token here.
    * @return Status of matched model action.
    * @throw
    */
    public static function simulateRequest($request_details, $app, $test_mode = false, $user_session_token = null)
    {
        $model_profile = Self::buildModelProfile($app, $request_details);

        //handle the request: match a route to a model and its action
        $status = Self::matchRequestToModel(
            $app,
            $model_profile,
            $request_details,
            $test_mode,
            $user_session_token
        );

        return $status;
    }

}

?>
