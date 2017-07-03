<?php

namespace iriki;

/**
* Iriki model, capable of self or inhertited actions
*
*/
class model_config extends config
{
    /**
    * Engine's models amd configuration.
    *
    * @var array
    */
    private $_engine = array(
        'config' => null,
        'models' => null
    );

    /**
    * Application's models amd configuration.
    *
    * @var array
    */
    private $_app = array(
        'config' => null,
        'models' => null
    );

    /**
    * Load model details from configuration files, given an array of routes
    *
    *
    * @param array Configuration key value pairs to get path
    * @param array Defined routes
    * @param string Application or engine name
    * @return array Model details
    * @throw
    */
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

        foreach ($routes as $route_title => $route_actions)
        {
            $model_json = (new config($path . 'models/' . $route_title . '.json'))->getJson();
            $store['models'][$route_title] = $model_json[$app]['models'][$route_title];
        }

        return $store['models'];
    }

    /**
    * Initialize an application's (engine's too) models
    *
    *
    * @param array Configuration key value pairs to get path
    * @param array Defined routes
    * @param string Application or engine name
    * @return array Model details
    * @throw
    */
    public function doInitialise($config_values, $routes, $app = 'iriki')
    {
        return $this->loadFromJson($config_values, $routes['routes'], $app);
    }

    /**
    * Get application's stored models
    *
    *
    * @param string Application or engine name
    * @return array Model details
    * @throw
    */
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

    /**
    * Get status, a summary of model details
    *
    *
    * @param array Previous status array to append to
    * @param boolean Encode result as json
    * @return array Status array or json representation
    * @throw
    */
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

}

?>
