<?php

	require_once('engine/config.php');

	//this is the API endpoint

	//read json to get app settings
	try
	{
		$obj_config = new iriki\engine\config('app.json');
		$app_json = $obj_config->getJson();
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

	//models
	require_once('engine/model.php');
	$obj_model = new iriki\engine\model();
    $config['model'] = $obj_model->loadFromJson($app_config['models'], $config['route']['routes']);
    //print_r($config['model']['models']);
    
    //do routing
	//parse the url
	$url_requested = $_SERVER['REQUEST_URI'];
	//$url_parsed = iriki\engine\route::parseUrl($url_requested, '/iriki/api');
	//var_dump($url_parsed);

	//match a route
    $selected_route = $obj_route->matchRouteUrl($url_requested, '/iriki/api');
	
	//match models
	var_dump($selected_route);

	//route it!

	//if test, route to test



?>
