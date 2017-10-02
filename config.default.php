<?php

//url where this runs from
define('IRIKI_CORS_URL', 'eyeti.xyz');
//obey CORS?
define('IRIKI_CORS_STRICT', false);

//Cross-origin Resource Sharing (CORS) test
cors_test(IRIKI_CORS_STRICT);

//set time zone, no where like home
date_default_timezone_set('Africa/Lagos');

//some random key, the one below won't do
//codeigniter types from http://randomkeygen.com/ should do
define('IRIKI_KEY', 'correct_horse_battery_staple');

//app persistence switch
//TODO: debug mode should be quite... "debuggy"
define('IRIKI_MODE', 'local');

//use iriki to manage sessions?
//should IRIKI_SESSION_REFRESH be obeyed for re-reads?
define('IRIKI_SESSION', false);

//time in seconds
//time until Iriki re-reads config files
define('IRIKI_SESSION_REFRESH', 30 * 24 * 60 * 60);
//times until a session token expires:
//no 'remember me', last 1 week
define('IRIKI_SESSION_SHORT', 7 * 24 * 60 * 60);
//'remember me', last 3 months
define('IRIKI_SESSION_LONG', 3 * 30 * 24 * 60 * 60);

//config file for this app
define('IRIKI_CONFIG', 'apps/kronos/app.json');

//engine
require_once('engine/autoload.php');

//vendor via composer
require_once('vendor/autoload.php');

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