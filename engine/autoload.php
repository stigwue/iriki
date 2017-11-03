<?php

//iriki internal classes
require_once(__DIR__ . '/iriki/autoload.php');

//vendor classes via composer
require_once(__DIR__ . '/vendor/autoload.php');

//iriki classes
foreach (glob(__DIR__ . "/*.php") as $filepath)
{
    //skip this very file
    if ($filepath == __FILE__) continue;
    require_once($filepath);
}

?>
