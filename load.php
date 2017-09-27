<?php
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
?>