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
        
        return $json;
    }

    function __construct($json_path = '')
    {
        if (strlen($json_path) != 0)
        {
             $this->_json = Self::load_json_file($json_path);
        }
    }
    
    public function doLoadJson($path)
    {
        $this->_json = Self::load_json_file($path);
    }

    public function getJson()
    {
        return $this->_json;
    }
}
?>
