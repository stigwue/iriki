<?php
	
	//application switch
	//define('IRIKI_APP', 'cashcrow');
	define('IRIKI_APP', 'elims');

	//app switch
	//define('IRIKI_MODE', 'development');
	define('IRIKI_MODE', 'production');


	//engine
	require_once('engine/autoload.php');
	//application
	require_once('app/' . IRIKI_APP . '/autoload.php');
	//vendors
	require_once('vendors/autoload.php');

	//this is the API endpoint

	$app = array(
		'config' => null,

		'routes' => null,

		'models' => null,

		'database' => null
	);

	$status = array();

	$app_config = new iriki\engine\config();
	$app_config->doInitialise('app.json');
		
	$app['config'] = $app_config->getKeyValues();
	$status = $app_config->getStatus();

	$app['database'] = $app['config']['database'][IRIKI_MODE];

	$app_routes = new iriki\engine\route();
	$app['routes']['engine'] = $app_routes->doInitialise($app['config']);
	$app['routes']['app'] = $app_routes->doInitialise($app['config'], IRIKI_APP);
	
	$status = $app_routes->getStatus($status);

	$app_models = new iriki\engine\model();
	$app['models']['engine'] = $app_models->loadModels($app['config'], $app_routes->getRoutes());
	
	$app['models']['app'] = $app_models->loadModels($app['config'], $app_routes->getRoutes(IRIKI_APP), IRIKI_APP);

	$request_details = iriki\engine\route::getRequestDetails();

	//var_dump($request_details);

	//parse the url and match a route to a model and its action
    $status = $app_routes->matchUrl(
    	$request_details,
    	//models
    	array(
    		'engine' => $app['models']['engine'],
    		'app' => $app['models']['app']
		),
		//routes
    	array(
    		'engine' => $app['routes']['engine'],
    		'app' => $app['routes']['app']
		),
		//database
		$app['database']
	);

    echo json_encode($status);

	//if test, route to test

?>
