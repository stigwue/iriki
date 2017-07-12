<?php

session_start();

//set time zone, no where like home
date_default_timezone_set('Africa/Lagos');

//url where this runs from
define('IRIKI_URL', 'eyeti.xyz/iriki');
//obey CORS?
define('IRIKI_STRICT_CORS', false);

//Cross-origin Resource Sharing (CORS) test
$cors_test_passed = cors(IRIKI_STRICT_CORS);

if (!$cors_test_passed) {
	if (IRIKI_STRICT_CORS) {
		$message = array(
			'code' => 400,
			'message' => 'CORS error'
		);
		echo json_encode($message);
		exit();
	}
}

//some random key, the one below won't do
//codeigniter types from http://randomkeygen.com/ should do
define('IRIKI_KEY', 'correct_horse_battery_staple');

//app persistence switch
define('IRIKI_MODE', 'local');

//use iriki to manage sessions?
//should IRIKI_REFRESH be obeyed for re-reads?
define('IRIKI_SESSION', false);

//refresh time in seconds
//time (seconds) until Iriki re-reads config files or session token expires
define('IRIKI_REFRESH', 60 * 7);

//config file for this app
define('IRIKI_CONFIG', 'apps/kronos/app.json');

//engine
require_once('engine/autoload.php');

//this are the components of an iriki app
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

	$APP['expires'] = time(NULL) + IRIKI_REFRESH;
	$_SESSION[IRIKI_KEY] = array(
		'iriki_expires' => $APP['expires'],
		'app' => $APP
	);
}
else
{
	$APP = $_SESSION[IRIKI_KEY]['app'];
}

//load up application's class files
require_once($APP['config']['application']['path'] . 'autoload.php');

//vendors
require_once('vendors/autoload.php');


//interprete request from url
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
	$message = array(
		'code' => 400,
		'message' => 'Iriki MC endpoint. Specify a model.'
	);
	echo json_encode($message);
}
else
{
	echo json_encode($status);
}


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

/**
*  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
*  origin.
*
*  In a production environment, you probably want to be more restrictive, but this gives you
*  the general idea of what is involved.  For the nitty-gritty low-down, read:
*
*  - https://developer.mozilla.org/en/HTTP_access_control
*  - http://www.w3.org/TR/cors/
*
*/
function cors($strict_cors) {
	//test request method
	if ($_SERVER['REQUEST_METHOD'] == 'GET' OR $_SERVER['REQUEST_METHOD'] == 'POST'  ) {

		//test for origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {

			$http_origin = strtolower($_SERVER['HTTP_ORIGIN']);

			if (strContains($http_origin, IRIKI_URL) OR !IRIKI_STRICT_CORS){
				header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
				header('Access-Control-Allow-Credentials: true');
				header('Access-Control-Max-Age: 86400');    // cache for 1 day

				return true;
			}
			else {
				return false;
			}
		}
		else {
			//origin isn't set
			return false;
		}
	}
	// Access-Control headers are received during OPTIONS requests
	else if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		// may also be using PUT, PATCH, HEAD etc
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

		exit(0);

		return true; //will not be hit
	}
	else {

		//fail
		return false;
	}
}

?>
