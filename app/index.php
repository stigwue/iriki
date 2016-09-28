<?php

	require_once('engine/config.php');

	//this is the API endpoint

	//read json to get app settings
	try
	{
		$obj_config = new iriki\engine\config('app.json');
		$app_json = $obj_config->toObject();
        $app_config = $app_json['iriki']['app'];
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
	$obj_route = new iriki\engine\route();
    $config['route'] = $obj_route->loadFromJson($app_config['routes']);
    //print_r($config['route']['routes']);

	/*$config['route']['path'] = $app_config['routes'];
    $config['route']['routes'] = [
        'user' => ['auth' => ['auth'], 'signup' => []],
        'session' => ['validate'=>['id'], 'create'=>[], 'read'=>['id']],
        'merchant' => []
    ];*/

	//models
	require_once('engine/model.php');
	$obj_model = new iriki\engine\model();
    $config['model'] = $obj_model->loadFromJson($app_config['models'], $config['route']['routes']);
    print_r($config['model']['models']);
	
	//var_dump($config);

    $url = array();

	//parse the url
	$requested = $_SERVER['REQUEST_URI'];

	//do routing
	//require_once('engine/route.php');
    //$route = new iriki\engine\route(

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
