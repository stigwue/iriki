<?php
	
	//application switch
	//define('IRIKI_APP', 'cashcrow');
	define('IRIKI_APP', 'elims');

	//app switch
	define('IRIKI_MODE', 'test');


	require_once('engine/autoload.php');
	require_once('app/' . IRIKI_APP . '/autoload.php');

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
	$app_routes->doInitialise($app['config'], IRIKI_APP);
	
	$status = $app_routes->getStatus($status);

	$app_models = new iriki\engine\model();
	$app['engine_models'] = $app_models->loadModels($app['config'], $app_routes->getRoutes());
	
	$app['app_models'] = $app_models->loadModels($app['config'], $app_routes->getRoutes(IRIKI_APP), IRIKI_APP);

	//do routing
	$url_requested = $_SERVER['REQUEST_URI'];
	$params = $_REQUEST;

	//parse the url and match a route to a model and its action
    $selected_route = $app_routes->matchUrl($url_requested, '/iriki/api/', $app['engine_models'], $app['app_models'], $params);

    //var_dump($app['app_models']);

    echo json_encode($selected_route);

	//if test, route to test


?>
