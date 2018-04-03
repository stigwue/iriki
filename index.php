<?php

session_start();

//base configuration
//make a copy of config.default.php, edit and save as config.php
require_once(__DIR__ . '/config.php');

//Cross-origin Resource Sharing (CORS) test
cors_test(IRIKI_CORS_STRICT);

//interprete request from url
$request_details = iriki\engine\route::parseRequest(
	(
		isset($APP['config']['base_url']) ?
		$APP['config']['base_url'] :
		''
	)
);

$model_profile = iriki\engine\route::buildModelProfile($APP, $request_details);

//handle the request: match a route to a model and its action
$status = iriki\engine\route::matchRequestToModel(
	$APP,
	$model_profile,
	$request_details
);

//return status
if (is_null($status))
{
	//null was returned, very odd!
	//we can tell the user that or?
	$message = array(
		'code' => 400,
		'message' => "This is an Iriki application. URL should be in the right format or else, Abuja, we have a problem."
	);
	echo json_encode($message);
}
else
{
	echo json_encode($status);
}

?>
