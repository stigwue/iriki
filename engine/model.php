<?php

namespace iriki\engine;

require_once(__DIR__ . '/config.php');

class generic_model
{}

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
    public static function doMatch($model_status, $models = null)
    {
        $model = (isset($model_status['str']) ? $model_status['str'] : null);

        if (is_null($model))
        {
            
        }
        else
        {
            foreach ($models as $_model => $_action)
            {
                //var_dump($action);
                if ($_model == $model)
                {
                    if ((is_null($action) OR $action == 'description') AND isset($_action['description']))
                    {
                        $status['data'] = array(
                            'model' => $_model,
                            'description' => $_action['description']
                        );

                        break;
                    }
                }
            }
        }

        /*if ($model_exists)
        {
            $model_instance =  new $model_full();


            $action_exists = method_exists($model_full, $action);

            if ($action == 'description') $action_exists = false;

            if ($action_exists)
            {
                $status = $model_instance->$action($params);
            }
            else
            {
                //no action specified, display the possible actions, using info
                $action = 'description';
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
            //first, is it found in config?

            $status['error'] = array(
                'code' => 404,
                'message' => 'Model not found'
            );
        }*/
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

    public function description($model, $action = null, $params = null, $json = false)
    {
        //params would contain
        //var_dump($params);

        $status = array();

        foreach ($params as $_model => $_action)
        {
            //var_dump($action);
            if ($_model == $model)
            {
                if ((is_null($action) OR $action == 'description') AND isset($_action['description']))
                {
                    $status['data'] = array(
                        'model' => $_model,
                        'description' => $_action['description']
                    );

                    break;
                }
            }
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
}

?>
