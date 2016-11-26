<?php

	//define('APP', 'cashcrow');
define('APP', 'myboard');

	require_once('engine/autoload.php');
	require_once('app/' . APP . '/autoload.php');

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
	$app_routes->doInitialise($app['config'], APP);
	
	$status = $app_routes->getStatus($status);

	$app_models = new iriki\engine\model();
	$app['engine_models'] = $app_models->loadModels($app['config'], $app_routes->getRoutes());
	
	$app['app_models'] = $app_models->loadModels($app['config'], $app_routes->getRoutes(APP), APP);

	//do routing
	$url_requested = $_SERVER['REQUEST_URI'];

	//parse the url and match a route to a model and its action
    $selected_route = $app_routes->matchRouteUrl($url_requested, '/iriki/api/', $app['engine_models'], $app['app_models'], $_REQUEST);

    //var_dump($app['app_models']);

    echo json_encode($selected_route);

	//var_dump($_REQUEST);

	//if test, route to test


?>
