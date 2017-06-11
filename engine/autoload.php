<?php

//engine
require_once(__DIR__ . '/iriki/autoload.php');

//app autoloaded from caller

foreach (glob(__DIR__ . "/*.php") as $filepath)
{
    //skip this very file
    if ($filepath == __FILE__) continue;
    require_once($filepath);
}

?>
