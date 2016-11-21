<?php

	require_once('engine/autoload.php');
	require_once('app/autoload.php');

	//this is the API endpoint

	$app = array(
		'config' => null,

		//'routes' => null,

		//'models' => null
	);

	$status = array();

	$app_config = new iriki\engine\config();
	$app_config->doInitialise('app.json');
		
	$app['config'] = $app_config->getKeyValues();
	$status = $app_config->getStatus();

	$app_routes = new iriki\engine\route();
	$app_routes->doInitialise($app['config']);
	$app_routes->doInitialise($app['config'], 'cashcrow');
	
	$status = $app_routes->getStatus($status);

	$app_models = new iriki\engine\model();
	$app['engine_models'] = $app_models->loadModels($app['config'], $app_routes->getRoutes());
	
	$app['app_models'] = $app_models->loadModels($app['config'], $app_routes->getRoutes('cashcrow'), 'cashcrow');

	//var_dump($app);


	echo json_encode($status);
	
    //do routing
	//parse the url
	$url_requested = $_SERVER['REQUEST_URI'];
	//$url_parsed = iriki\engine\route::parseUrl($url_requested, '/iriki/api');
	//var_dump($url_parsed);

	//match a route
    $selected_route = $app_routes->matchRouteUrl($url_requested, '/iriki/api/', $app['engine_models'], $app['app_models']);
	
	//match models
	//var_dump($selected_route);

	//var_dump($_REQUEST);

	//route it!

	//if test, route to test


?>
