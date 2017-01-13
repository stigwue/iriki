<?php
	session_start();
	//var_dump($_SESSION);

	//app persistence switch
	define('IRIKI_MODE', 'local');
	//define('IRIKI_MODE', 'test');
	//define('IRIKI_MODE', 'development');
	//define('IRIKI_MODE', 'production');

	//engine
	require_once('engine/autoload.php');

	//this are the components of an iriki app
	$app = array(
		//the iriki engine, which is also an iriki app ala mysql
		'engine' => NULL,
		//your application
		'application' => NULL,
		//a persistence structure
		'database' => null,


		//the routes
		'routes' => null,
		//the models the routes point to
		'models' => null,
		

		//the config files parsed on initialisation
		'config' => null,

		//app initialised status? set to false to re-initialise
		'initialised' => isset($_SESSION['iriki_session'])
	);


	$status = array();
	$app_config = new iriki\config();
	$app_routes = new iriki\route();
	$app_models = new iriki\model();

	//if ($app['initialised'] == false)
	{
		//initialise app config values
		$app_config->doInitialise('app.json');
		$app['config'] = $app_config->getKeyValues();

		//load up configurations
		$app['engine'] = $app['config']['engine']['name'];
		$app['application'] = $app['config']['application']['name'];
		$app['database'] = $app['config']['database'][IRIKI_MODE];
		//$status = $app_config->getStatus();

		//load up routes
		$app['routes']['engine'] = $app_routes->doInitialise($app['config'], $app['engine']);
		$app['routes']['app'] = $app_routes->doInitialise($app['config'], $app['application']);
		//$status = $app_routes->getStatus($status);

		//load up models
		$app['models']['engine'] = $app_models->loadModels($app['config'], $app_routes->getRoutes());
		$app['models']['app'] = $app_models->loadModels($app['config'], $app_routes->getRoutes($app['application']), $app['application']);
		//$status = $app_models->getStatus($status);

		$app['initialised'] = true;
		$_SESSION['iriki_session'] = true;
		$_SESSION['iriki_app'] = $app;
	}
	//else
	{
		//$app = $_SESSION['iriki_app'];
	}


	//load up application's class files
	require_once($app['config']['application']['path'] . 'autoload.php');

	//vendors
	require_once('vendors/autoload.php');


	//interprete request
	$request_details = iriki\route::getRequestDetails(null, null, $app['config']['base_url']);

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

	
	//return status
	if (is_null($status))
    {
    	echo json_encode($app_config->getStatus());
    }
    else echo json_encode($status);

?>
