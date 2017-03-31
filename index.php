<?php

//Cross-origin Resource Sharing
cors();

session_start();

//set time zone, no where like home
date_default_timezone_set('Africa/Lagos');

//some random key, the one bellow won't do
define('IRIKI_KEY', 'correct_horse_battery_staple');

//app persistence switch
define('IRIKI_MODE', 'local');

//use iriki to manage sessions?
define('IRIKI_SESSION', true);
//refresh time in seconds
define('IRIKI_REFRESH', 60 * 7);

define('IRIKI_CONFIG', 'app/app.json');

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
	//$status = $app_config->getStatus();


	$app_routes = new iriki\route();
	//load up routes
	$APP['routes']['engine'] = $app_routes->doInitialise($APP['config'], $APP['engine']);
	$APP['routes']['app'] = $app_routes->doInitialise($APP['config'], $APP['application']);
	//$status = $app_routes->getStatus($status);


	$app_models = new iriki\model();
	//load up models
	$APP['models']['engine'] = $app_models->doInitialise($APP['config'], $app_routes->getRoutes());
	$APP['models']['app'] = $app_models->doInitialise($APP['config'], $app_routes->getRoutes($APP['application']), $APP['application']);
	//$status = $app_models->getStatus($status);

	$APP['expires'] = time(NULL) + IRIKI_REFRESH;
	//$_SESSION['iriki_expires'] = $APP['expires'];
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
	echo json_encode(\iriki\response::error('.....', true));
}
else
{
	echo json_encode($status);
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
function cors() {
	// Allow from any origin
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
		// you want to allow, and if so:
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}

	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		// may also be using PUT, PATCH, HEAD etc
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

		exit(0);
	}
}

?>
