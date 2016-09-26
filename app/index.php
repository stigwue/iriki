<?php

	require_once('engine/config.php');

	//this is the API endpoint

	//read json to get app settings
	try
	{
		$app_json = (new mongovc\engine\config('app.json'))->toObject();
        $app_config = $app_json['mongovc']['app'];
	}
	catch (Exception $e)
	{
        
	}

	//get paths
	global $config;
	$config = array();
	$config['title'] = $app_config['title'];
	$config['author'] = $app_config['author'];
	$config['base_url'] = $app_config['base_url'];

	//engine, already known from require
	$config['engine'] = $app_config['engine'];


	//routes
	require_once('engine/route.php');
    $config['route'] = (new mongovc\engine\route())->loadFromJson($app_config['routes']);

	//models
	require_once('engine/model.php');
    $config['model'] = (new mongovc\engine\model())->loadFromJson($app_config['models'], $config['route']['routes']);

    //load model json files for already defined routes
    //no route, no model
    //$model_files = glob($config['model']['path'] . '*.json');
    /*$config['model']['models'] = array();
    foreach ($config['route']['routes'] as $route_title => $route_actions)
    {
        $model_json = (new mongovc\engine\config($config['model']['path'] . $route_title . '.json'))->toObject();
        $model_config = $model_json['mongovc']['models'][$route_title];
        $config['model']['models'][] = $model_config;
    }*/

	//var_dump($config);
    //print_r($config);

    $url = array();

	//parse the url
	$requested = $_SERVER['REQUEST_URI'];

	//do routing
	//require_once('engine/route.php');
    //$route = new mongovc\engine\route(

	//match models

	// Formatting kungfu here
	// You may want to strip preceding/trailing slashes
	// Remove queries in url, etc

	/*parse_url('http://cashcrow.me/api/user/edit/1')

	array(3) {
	  ["scheme"]=>
	  string(4) "http"
	  ["host"]=>
	  string(11) "cashcrow.me"
	  ["path"]=>
	  string(16) "/api/user/edit/1"
	}*/

	$requested = ltrim($requested, '/');

	$parameters = explode("/", $requested);

	$parameter_count = count($parameters);

	//route it!

	//if test, route to test



?>
