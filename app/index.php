<?php

	require_once('engine/config.php');
	require_once('engine/route.php');
	require_once('engine/model.php');

	//this is the API endpoint

	$app = array(
		'config' => null,
		'routes' => null,
		'models' => null
	);

	$app_config = new iriki\engine\config();
	$app_config->doInitialise('app.json');
		
	$app['config'] = $app_config->getKeyValues();

	$app_routes = new iriki\engine\route();
	$app['routes'] = $app_routes->doInitialise($app['config']);

	$app_models = new iriki\engine\model();
	$app['models'] = $app_models->loadModels($app['config'], $app['routes']);

	echo $app_config->getStatus();
	echo $app_routes->getStatus();

	//var_dump($app);

	/*
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

	*/

	 



?>
