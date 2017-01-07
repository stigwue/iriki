<?php

namespace iriki;

require_once(__DIR__ . '/config.php');

class model extends config
{
    //engine models
    private $_engine = array(
        'config' => null,
        'models' => null
    );

    //app models;
    private $_app = array(
        'config' => null,
        'models' => null
    );

    //persistence
    private $_db = array();

    public function loadFromJson($config_values, $routes, $app = 'iriki')
    {
        $var = '_engine';
        $path = $config_values['engine']['path'];
        if ($app != 'iriki')
        {
            $var = '_app';
            $path = $config_values['application']['path'];
        }
        $store = &$this->$var;

        foreach ($routes as $route_title => $route_actions)
        {
            $model_json = (new config($path . 'models/' . $route_title . '.json'))->getJson();
            $store['models'][$route_title] = $model_json[$app]['models'][$route_title];
        }

        return $store['models'];
    }

    //essentially its doInitialise
    public function loadModels($config_values, $routes, $app = 'iriki')
    {
        return $this->loadFromJson($config_values, $routes['routes'], $app);
    }

    public function getModels($app = 'iriki')
    {
        $var = '_engine';
        if ($app != 'iriki')
        {
            $var = '_app';
        }
        $store = &$this->$var;

        return $store['models'];
    }


    //returns a 3 level match
    //a specific model or alias
    //a specific action for said model
    //a specific set of parameters for said action
    public static function doMatch($model_status, $models = null, $routes = null)
    {
        //see $model_status structure in route->matchUrl

        $model = (isset($model_status['str']) ? $model_status['str'] : null);

        //find model details
        foreach ($models as $_model => $_action)
        {
            if ($_model == $model_status['str'])
            {
                //we have found the model
                $action = $model_status['action'];

                $model_status['details'] = array(
                    'description' => $_action['description'],
                    'properties' => $_action['properties'],
                    'relationships' => $_action['relationships']
                );

                //now, to find the model's route
                foreach ($routes['routes'] as $_route => $_route_action)
                {
                    if ($_route == $model_status['str'])
                    {
                        //model's route found, look up action
                        if (isset($_route_action[$model_status['action']]))
                        {
                            $model_status['action_details'] = array(
                                'description' => (isset($_route_action[$model_status['action']]['description']) ? $_route_action[$model_status['action']]['description'] : ''),
                                'parameters' => $_route_action[$model_status['action']]['parameters'],
                                'exempt' => (isset($_route_action[$model_status['action']]['exempt']) ? $_route_action[$model_status['action']]['exempt'] : array())
                            );

                            $model_status['action_defined'] = true;
                            $model_status['action_default'] = false;

                            break;
                        }
                        //test for action in default
                        else if (isset($model_status['default'][$model_status['action']]))
                        {
                            $model_status['action_details'] = array(
                                'description' => (isset($model_status['default'][$model_status['action']]['description']) ? $model_status['default'][$model_status['action']]['description'] : ''),
                                'parameters' => $model_status['default'][$model_status['action']]['parameters'],
                                'exempt' => (isset($model_status['default'][$model_status['action']]['exempt']) ? $model_status['default'][$model_status['action']]['exempt'] : array())
                            );

                            $model_status['action_defined'] = true;
                            $model_status['action_default'] = true;

                            break;
                        }
                        else
                        {
                            //action not found
                            $model_status['action_defined'] = false;
                            $model_status['action_default'] = false;

                            //default to description of model since action does not exist

                            $model_status['action_details'] = array(
                                'description' => $_action['description']
                            );

                            break;
                        }
                    }
                }
            }
        }

        return $model_status;
    }


    //parameter property match: exist and type?
    public static function doPropertyMatch($details, $sent, $filter)
    {
        //parameters work thus:
        //empty valid => all paramters valid except 'exempt'
        //non-empty valid => listed parameters except 'exempt'

        $all_properties = $details['properties'];

        $valid_properties = $filter['parameters'];
        $exempt_properties = (isset($filter['exempt']) ? $filter['exempt'] : null);


        //build valid properties
        if (count($valid_properties) == 0)
        {
            //all properties are valid
            foreach ($all_properties as $property => $property_details) $valid_properties[] = $property;
        }

        //check exempt properties
        if (count($exempt_properties) == 0)
        {
            //there's no except list, carry on
        }
        else
        {
            for ($i = count($valid_properties) - 1; $i >= 0; $i--)
            {
                if (in_array($valid_properties[$i], $exempt_properties))
                {
                    unset($valid_properties[$i]);
                }
            }
        }

        //check for sent properties
        $properties_missing = array();
        $final_properties = array();
        foreach ($valid_properties as $property)
        {
            if (isset($sent[$property]))
            {
                //property was sent
                //check type?
                $final_properties[] = $property;
            }
            else
            {
                $properties_missing[] = $property;
            }
        }

        return array(
            //properties supplied
            'final' => $final_properties,
            //missing properties that should have been supplied
            'missing' => $properties_missing,
            'extra' => count($sent) - count($final_properties)
        );
    }


    //relationship?
    public static function doRelationMatch($details, $params)
    {

    }

    public function getStatus($status = null, $json = false)
    {
        if (is_null($status))
        {
            $status = array('data' => array());
        }

        //engine's models
        $status['data']['engine']['models'] = array();
        foreach ($this->_engine['models'] as $model => $details)
        {
            $status['data']['engine']['models'][] = $model;
        }

        //app's models
        $status['data']['application']['models'] = array();
        foreach ($this->_app['models'] as $model => $details)
        {
            $status['data']['application']['models'][] = $model;
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

    //persistence

    //note that all (most?) actions have a single parameter $params_persist containing
    //db_type -> string full namespace of db class
    //persist -> string table/collection to persist in
    //data -> data to persist
    //session ->

    public function create($params_persist = null)
    {
        $instance = new $params_persist['db_type']();
        $instance::initInstance();

        //do validation of params (count check and isset?)
        //if mode is strict and this check fails, do not call create

        //add created and modified timestamps?

        if (!is_null($params_persist))
        {
            return $instance::doCreate($params_persist);
        }
    }

    public function read($params_persist = null)
    {
        $instance = new $params_persist['db_type']();
        $instance::initInstance();

        if (!is_null($params_persist))
        {
            return $instance::doRead($params_persist);
        }

    }

    public function update($params_persist = null)
    {
        $instance = new $params_persist['db_type']();
        $instance::initInstance();

        if (!is_null($params_persist))
        {
            return $instance::doUpdate($params_persist);
        }
        else
        {

        }

    }

    public function delete($params_persist = null)
    {
        $instance = new $params_persist['db_type']();
        $instance::initInstance();


        if (!is_null($params_persist))
        {
            return $instance::doDelete($params_persist);
        }

    }
}

?>
