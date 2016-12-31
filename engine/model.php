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
            $path = $config_values[$app]['path'];
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
    public static function doMatch($model_status, $models = null, $routes = null /*,engine_route_index, app_route_index for alias and defaults*/)
    {
        //see $model_status structure in in route->matchUrl

        $model = (isset($model_status['str']) ? $model_status['str'] : null);

        //find model details
        foreach ($models as $_model => $_action)
        {
            if ($_model == $model_status['str'])
            {
                //we are in the model
                $action = $model_status['action'];

                $model_status['details'] = array(
                    'description' => $_action['description'],
                    'properties' => $_action['properties'],
                    'relationships' => $_action['relationships']
                );

                //loop for route actions
                foreach ($routes['routes'] as $_route => $_route_action)
                {
                    if ($_route == $model_status['str'])
                    {
                        if (isset($_route_action[$model_status['action']]))
                        {
                            $model_status['action_details'] = array(
                                'description' => (isset($_route_action[$model_status['action']]['description']) ? $_route_action[$model_status['action']]['description'] : ''),
                                'parameters' => $_route_action[$model_status['action']]['parameters']
                            );

                            $model_status['action_defined'] = true;

                            break;
                        }
                        else
                        {
                            //default to description of model action if defined in default
                            $model_status['action_defined'] = false;

                            //check default for action
                            $model_status['action_default'] = isset($routes['default'][$model_status['action']]);

                            //default to description of model since action does not exist

                            $model_status['action_details'] = array(
                                'description' => $_action['description']
                            );

                            break;
                        }
                    }
                }

                //route default actions
            }
        }

        //action exists
        //action does not exist, is it defined in

        return $model_status;
    }

    public function getStatus($status = null, $json = false)
    {
        //show model routes and pull model info definitions
        if (is_null($status))
        {
            $status = array('data' => array());
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
    public function create($db_type, $params_persist = null)
    {
        $instance = new $db_type();
        $instance::initInstance();

        //do validation of params (count check and isset?)
        //if mode is strict and this check fails, do not call create

        if (!is_null($params_persist))
        {
            return $instance::doCreate($params_persist);
        }

    }

    public function read($db_type, $params_persist = null)
    {
        $instance = new $db_type();
        $instance::initInstance();

        if (!is_null($params_persist))
        {
            return $instance::doRead($params_persist);
        }

    }

    public function update($db_type, $params_persist = null)
    {
        $instance = new $db_type();
        $instance::initInstance();

        if (!is_null($params_persist))
        {
            return $instance::doUpdate($params_persist);
        }
        else
        {

        }

    }

    public function delete($db_type, $params_persist = null)
    {
        $instance = new $db_type();
        $instance::initInstance();


        if (!is_null($params_persist))
        {
            return $instance::doDelete($params_persist);
        }

    }
}

?>
