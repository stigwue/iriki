<?php
//setup app components
global $APP;

$APP = array(
	//string, the engine, which is also an iriki application
	'engine' => null,
	//string, your application
	'application' => null,
	//array, a persistence structure
	'database' => null,
	'constants' => null,
	'session' => null,


	//array, the routes
	'routes' => null,
	//array, the models the routes point to
	'models' => null,


	//array, the configuration used for this instance
	'config' => null,

	//app expiry stamp so it auto updates
	//if changes are made to config files, set this to zero
	//or old ones will be used until expiry
	'expires' => (IRIKI_SESSION ?
	(isset($_SESSION[IRIKI_KEY]['iriki_expires']) ? $_SESSION[IRIKI_KEY]['iriki_expires'] : 0)
	:
	0
	)
);

$status = array();

if ($APP['expires'] == 0 OR $APP['expires'] <= time(NULL))
{
	//initialise app config values
	$app_config = new iriki\config(IRIKI_CONFIG);
	$APP['config'] = $app_config->getKeyValues();

	//load up configurations
	$APP['engine'] = $APP['config']['engine']['name'];
	$APP['application'] = $APP['config']['application']['name'];
	$APP['database'] = $APP['config']['database'][IRIKI_MODE];
	$APP['constants'] = $APP['config']['constants'];
	//$status = $app_config->getStatus();


	$app_routes = new iriki\route_config();
	//load up routes
	$APP['routes']['engine'] = $app_routes->doInitialise($APP['config'], $APP['engine']);
	$APP['routes']['app'] = $app_routes->doInitialise($APP['config'], $APP['application']);
	//$status = $app_routes->getStatus($status);


	$app_models = new iriki\model_config();
	//load up models
	$APP['models']['engine'] = $app_models->doInitialise($APP['config'], $app_routes->getRoutes());
	$APP['models']['app'] = $app_models->doInitialise($APP['config'], $app_routes->getRoutes($APP['application']), $APP['application']);
	//$status = $app_models->getStatus($status);

	$APP['expires'] = time(NULL) + IRIKI_SESSION_REFRESH;
	$_SESSION[IRIKI_KEY] = array(
		'iriki_expires' => $APP['expires'],
		'app' => $APP
	);
}
else
{
	$APP = $_SESSION[IRIKI_KEY]['app'];
}

//CORS tests

//utility function, as named
function strContains($haystack, $needle) {
	if (strpos($haystack, $needle) !== FALSE)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//http://stackoverflow.com/a/9866124/3323338
function cors_test($strict_cors) {
	//test for origin
	$request_origin = (isset($_SERVER['HTTP_ORIGIN'])) ? strtolower($_SERVER['HTTP_ORIGIN']) : $_SERVER['HTTP_HOST'];

	if ($strict_cors)
	{
		//we are enforcing strict cors
		//we tell asks what requests we allow and what origin we allow
		//we allow requests from only IRIKI_CORS_URL

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			// may also be using PUT, PATCH, HEAD etc
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

			if (strContains($request_origin, IRIKI_CORS_URL))
			{
				//valid origin
				header("Access-Control-Allow-Origin: {$request_origin}");
				header('Access-Control-Allow-Credentials: true');
				header('Access-Control-Max-Age: 86400');    // cache for 1 day

				return;
			}

			exit(0);

			return;
		}
		//test request method
		//TODO: add other requests apart from GET and POST
		else if ($_SERVER['REQUEST_METHOD'] == 'GET' OR $_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if (strContains($request_origin, IRIKI_CORS_URL))
			{
				//valid origin
				header("Access-Control-Allow-Origin: {$request_origin}");
				header('Access-Control-Allow-Credentials: true');
				header('Access-Control-Max-Age: 86400');    // cache for 1 day

				return;
			}
			else
			{
				//invalid origin
				echo json_encode([
					'code' => 400,
					'message' => 'CORS origin not allowed.'
				]);
				
				exit(0);

				return;
			}
		}
		else
		{
			//other request types
			echo json_encode([
				'code' => 400,
				'message' => 'CORS request not handled.'
			]);
			
			exit(0);

			return;
		}
	}
	else
	{
		//no strict cors enforcement
		//we allow all requests
		//we allow requests from * (all)

		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			// may also be using PUT, PATCH, HEAD etc
			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
			
			//valid origin
			header("Access-Control-Allow-Origin: *");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day

			exit(0);

			return;
		}
		else if ($_SERVER['REQUEST_METHOD'] == 'GET' OR $_SERVER['REQUEST_METHOD'] == 'POST')
		{
			//valid origin
			header("Access-Control-Allow-Origin: *");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day

			return;
		}
	}
}
?>