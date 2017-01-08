<?php

	//app persistence switch
	define('IRIKI_MODE', 'local');
	//define('IRIKI_MODE', 'test');
	//define('IRIKI_MODE', 'development');
	//define('IRIKI_MODE', 'production');

	$app = array(
		'config' => null,

		'engine' => NULL,

		'application' => NULL,

		'database' => null,

		'routes' => null,

		'models' => null
	);


	//engine
	require_once('engine/autoload.php');


	//$status = array();

	$app_config = new iriki\config();
	$app_config->doInitialise('app.json');

	//load up configurations
	$app['config'] = $app_config->getKeyValues();
	$app['engine'] = $app['config']['engine']['name'];
	$app['application'] = $app['config']['application']['name'];
	$app['database'] = $app['config']['database'][IRIKI_MODE];
	//$status = $app_config->getStatus();

	//load up routes
	$app_routes = new iriki\route();
	$app['routes']['engine'] = $app_routes->doInitialise($app['config'], $app['engine']);
	$app['routes']['app'] = $app_routes->doInitialise($app['config'], $app['application']);
	//$status = $app_routes->getStatus($status);

	//load up models
	$app_models = new iriki\model();
	$app['models']['engine'] = $app_models->loadModels($app['config'], $app_routes->getRoutes());
	$app['models']['app'] = $app_models->loadModels($app['config'], $app_routes->getRoutes($app['application']), $app['application']);
	//$status = $app_models->getStatus($status);

	//interprete request
	$request_details = iriki\route::getRequestDetails(null, null, $app['config']['base_url']);

	//load up application's class files
	require_once($app['config']['application']['path'] . 'autoload.php');

	//vendors
	require_once('vendors/autoload.php');

	//handle the request: match a route to a model and its action
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

    if (is_null($status))
    {
    	echo json_encode($app_config->getStatus());
    }
    else echo json_encode($status);

	//if test, route to test

?>
