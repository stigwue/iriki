<?php

session_start();

//base configuration
require_once(__DIR__ . '/config.php');

//load other configuration
require_once(__DIR__ . '/load.php');

//load up application's class files
require_once($APP['config']['application']['path'] . 'autoload.php');

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
	//null was returned, very odd!
	//we can tell the user that or?
	$message = array(
		'code' => 400,
		'message' => "This might be an Iriki application's base url or else, Abuja, we have a problem."
	);
	echo json_encode($message);
}
else
{
	echo json_encode($status);
}

?>
