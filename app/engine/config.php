<?php

namespace iriki\engine;

class config
{
    private $_json;

    private static function load_json_file($json_path)
    {
        try {
            $json = json_decode(file_get_contents($json_path), TRUE);
        }
        catch (Exception $e) {
            //load default json
            $json = '{}';
        }
        
        //$json = json_decode(file_get_contents($json_path), TRUE);

        return $json;
    }

    function __construct($json_path = '')
    {
        if (strlen($json_path) != 0)
        {
            //load app.json, it should point to other valid models or routes
            $this->_json = Self::load_json_file($json_path);
        }
    }
    
    public function doLoadJson($path)
    {
        $this->_json = Self::load_json_file($path);
    }

    public function toObject()
    {
        return $this->_json;
    }
}
?>
