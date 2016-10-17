<?php

	require_once('engine/config.php');
	require_once('engine/route.php');
	require_once('engine/model.php');

	//this is the API endpoint

	$app = array(
		'config' => null,

		//'routes' => null,

		//'models' => null
	);

	$app_config = new iriki\engine\config();
	$app_config->doInitialise('app.json');
		
	$app['config'] = $app_config->getKeyValues();
	echo $app_config->getStatus();


	$app_routes = new iriki\engine\route();
	$app_routes->doInitialise($app['config']);
	$app_routes->doInitialise($app['config'], 'cashcrow');
	
	echo $app_routes->getStatus();

	$app_models = new iriki\engine\model();
	$app['iriki_models'] = $app_models->loadModels($app['config'], $app_routes->getRoutes());
	
	//var_dump($app['iriki_models']);
	
	$app['app_models'] = $app_models->loadModels($app['config'], $app_routes->getRoutes('cashcrow'), 'cashcrow');


	
    //do routing
	//parse the url
	$url_requested = $_SERVER['REQUEST_URI'];
	//$url_parsed = iriki\engine\route::parseUrl($url_requested, '/iriki/api');
	//var_dump($url_parsed);

	//match a route
    $selected_route = $app_routes->matchRouteUrl($url_requested, '/iriki/');
	
	//match models
	var_dump($selected_route);

	//route it!

	//if test, route to test


?>
