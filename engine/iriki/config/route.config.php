<?php

namespace iriki;

/**
* Iriki route configuration. Routes to a model's action.
*
*/
class route_config extends config
{
    /**
    * Engine's routes.
    *
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
    * @param config_values Configuration key value pairs.
    * @param app Application name.
    * @return Route details such as default, alias, list and an empty routes array.
    * @throw
    */
    public function loadFromJsonIndex($config_values, $app = 'iriki')
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

    /**
    * Load route details from configuration files, given an array of routes.
    *
    *
    * @param config_values Configuration key value pairs to get path.
    * @param routes Defined routes.
    * @param app Application or engine name.
    * @return Route details after empty routes array is filled
    * @throw
    */
    public function loadFromJson($config_values, $routes, $app = 'iriki')
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
            $valid_route_json = (new config($path . 'routes/' . $valid_route . '.json'))->getJson();
            $store['routes']['routes'][$valid_route] = $valid_route_json[$app]['routes'][$valid_route];
        }

        return $store['routes'];
    }

    /**
    * Initialize an application's (engine's too) routes
    *
    *
    * @param array Application configuration key value pairs
    * @param string Application or engine name
    * @returns array Route details
    * @throw
    */
    public function doInitialise($config_values, $app = 'iriki')
    {
        $routes = $this->loadFromJsonIndex($config_values, $app);

        return $this->loadFromJson($config_values, $routes, $app);
    }

    /**
    * Get application's stored routes
    *
    *
    * @param string Application or engine name
    * @returns array Route details
    * @throw
    */
    public function getRoutes($app = 'iriki')
    {
        $store = &Self::$_engine;
        if ($app != 'iriki')
        {
            $store = &Self::$_app;
        }

        return $store['routes'];
    }

    /**
    * Get status, a summary of route details
    *
    *
    * @param array Previous status array to append to
    * @param boolean Encode result as json
    * @returns array Status array or json representation
    * @throw
    */
    public function getStatus($status = null, $json = false)
    {
        //engine's routes
        if (is_null($status))
        {
            $status = array('data' => array());
        }

        $status['data']['engine'] = array();
        $status['data']['engine']['name'] = Self::$_engine['app']['name'];
        $status['data']['engine']['path'] = Self::$_engine['app']['path'];

        $status['data']['engine']['routes'] = array();
        foreach (Self::$_engine['routes']['routes'] as $model => $actions)
        {
            $status['data']['engine']['routes'][] = $model;
        }

        //app's routes
        $status['data']['application'] = array();

        $status['data']['application']['name'] = Self::$_app['app']['name'];
        $status['data']['application']['path'] = Self::$_app['app']['path'];

        $status['data']['application']['routes'] = array();
        foreach (Self::$_app['routes']['routes'] as $model => $actions)
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

}

?>
