<?php
	session_start();

	//set time zone, no where like home
	date_default_timezone_set('Africa/Lagos');

	//app persistence switch
	define('IRIKI_MODE', 'local');
	//define('IRIKI_MODE', 'test');
	//define('IRIKI_MODE', 'development');
	//define('IRIKI_MODE', 'production');

	//use iriki to manage sessions?
	define('IRIKI_SESSION', false);
	//refresh time in seconds
	define('IRIKI_REFRESH', 120);

	//engine
	require_once('engine/autoload.php');

	//this are the components of an iriki app
	global $APP;
	
	$APP = array(
		//string, the engine, which is also an iriki application
		'engine' => NULL,
		//string, your application
		'application' => NULL,
		//array, a persistence structure
		'database' => null,


		//array, the routes
		'routes' => null,
		//array, the models the routes point to
		'models' => null,


		//array, the config files parsed on initialisation
		'config' => null,

		//app expiry stamp so it auto updates
		//if changes are made to config files, set this to zero
		//or old ones will be used until expiry
		'expires' => (IRIKI_SESSION ?
			(isset($_SESSION['iriki_expires']) ? $_SESSION['iriki_expires'] : 0)
			:
			0
		)
	);


	$status = array();
	$app_config = new iriki\config();
	$app_routes = new iriki\route();
	$app_models = new iriki\model();

	if ($APP['expires'] == 0 OR $APP['expires'] <= time(NULL))
	{
		//initialise app config values
		$app_config->doInitialise('apps/emis/app.json');
		$APP['config'] = $app_config->getKeyValues();

		//load up configurations
		$APP['engine'] = $APP['config']['engine']['name'];
		$APP['application'] = $APP['config']['application']['name'];
		$APP['database'] = $APP['config']['database'][IRIKI_MODE];
		//$status = $app_config->getStatus();

		//load up routes
		$APP['routes']['engine'] = $app_routes->doInitialise($APP['config'], $APP['engine']);
		$APP['routes']['app'] = $app_routes->doInitialise($APP['config'], $APP['application']);
		//$status = $app_routes->getStatus($status);

		//load up models
		$APP['models']['engine'] = $app_models->doInitialise($APP['config'], $app_routes->getRoutes());
		$APP['models']['app'] = $app_models->doInitialise($APP['config'], $app_routes->getRoutes($APP['application']), $APP['application']);
		//$status = $app_models->getStatus($status);

		$APP['expires'] = time(NULL) + IRIKI_REFRESH;
		$_SESSION['iriki_expires'] = $APP['expires'];
		$_SESSION['iriki_app'] = $APP;
	}
	else
	{
		$app = $_SESSION['iriki_app'];
	}

	//load up application's class files
	require_once($APP['config']['application']['path'] . 'autoload.php');

	//vendors
	require_once('vendors/autoload.php');

	//iriki\stat_request::moveHead(time(NULL));


	//interprete request
	$request_details = iriki\route::getRequestDetails(null, null, $APP['config']['base_url']);

	//handle the request: match a route to a model and its action
	$status = iriki\route::matchUrl(
    	$request_details,
    	//app
    	$APP
	);


	//return status
	if (is_null($status))
  {
  	echo json_encode($app_config->getStatus());
  }
  else
	{
		echo json_encode($status);
	}

?>
