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

    public function info($model, $action = null, $params = null, $json = false)
    {
        //params would contain
        //var_dump($params);



        $status = array();

        foreach ($params as $_model => $_action)
        {
            //var_dump($action);
            if ($_model == $model)
            {
                //var_dump($_action);
                if ((is_null($action) OR $action == 'info') AND isset($_action['info']))
                {
                    $status['data'] = array(
                        'model' => $_model,
                        'info' => $_action['info']
                    );
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
