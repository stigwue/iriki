<?php

foreach (glob(__DIR__ . "/*.php") as $filepath)
{
    //skip this very file
    if ($filepath == __FILE__) continue;
    require_once($filepath);
}

//database
require_once(__DIR__ . '/database/autoload.php');

//engine
require_once(__DIR__ . '/iriki/autoload.php');

//app autoloaded from caller
?>
