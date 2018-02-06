<?php

//url where this runs from
define('IRIKI_CORS_URL', 'iriki.eyeti.xyz');
//obey CORS?
define('IRIKI_CORS_STRICT', false);

//set time zone, no where like home
date_default_timezone_set('Africa/Lagos');

//some random key, the one below won't do
//codeigniter types from http://randomkeygen.com/ should do
define('IRIKI_KEY', 'correct_horse_battery_staple');

//app persistence switch
//TODO: debug mode should be quite... "debuggy"
define('IRIKI_MODE', 'dev');

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
define('IRIKI_CONFIG', 'apps/blank/app.json');

//engine
require_once('engine/autoload.php');

//load up application's class files
require_once($GLOBALS['APP']['config']['application']['path'] . 'autoload.php');

?>