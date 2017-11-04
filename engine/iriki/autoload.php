<?php

//primitives
require_once(__DIR__ . '/config/autoload.php');

//databases
require_once(__DIR__ . '/database/autoload.php');

//log?

//utilities
require_once(__DIR__ . '/utility/autoload.php');


foreach (glob(__DIR__ . "/*.php") as $filepath)
{
    //skip this very file
    if ($filepath == __FILE__) continue;
    require_once($filepath);
}

?>
