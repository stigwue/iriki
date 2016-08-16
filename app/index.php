<?php

	namespace mongovc;
	use mongovc;

	require_once('engine/app.php');

	//this is the API endpoint

	//read json to get app settings

	//read all routes from /routes

		//$files = glob("/path/to/directory/*.txt");

	//read all models from /models

	//parse the url
	$requested = $_SERVER['REQUEST_URI'];

	//do routing

	//match models

	// Formatting kungfu here
	// You may want to strip preceding/trailing slashes
	// Remove queries in url, etc

	/*parse_url('http://cashcrow.me/api/user/edit/1')

	array(3) {
	  ["scheme"]=>
	  string(4) "http"
	  ["host"]=>
	  string(11) "cashcrow.me"
	  ["path"]=>
	  string(16) "/api/user/edit/1"
	}*/

	$requested = trim($requested, '/');

	$parameters = explode("/", $requested);

	$parameter_count = count($parameters);

	//route it!

	//if test, route to test



?>
