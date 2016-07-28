<?php

	//this is the API endpoint

	//calls to this will be handled by the router
	$requested = $_SERVER['REQUEST_URI'];

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
