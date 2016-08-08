<?php

namespace mongovc;

public class config
{
    private static function load_json_file($file_path)
    {
        try {
            $json = json_decode(file_get_contents($filepath), TRUE);
        }
        catch (Exception $e) {
            $json = json_encode(PrintonObject::getSliderDescription());
        }

        return $json;
    }

    function __construct()
    {
        //load app.json, it should point to other valid models or routes
    }
}
?>
