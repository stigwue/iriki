<?php

namespace iriki\engine;

class config
{
    private $_json;
    private $_key_values;

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

    public function doInitialise($json_path)
    {
        $this->_json = Self::load_json_file($json_path);

        $this->_key_values = $this->_json['iriki']['app'];
    }

    public function getStatus()
    {
        //return key-value pairs
        /*
        title: Iriki App
        author: Stephen Igwue
        version: major.minor.build

        url: //iriki/app/
        routes:

        default: actions*/ 

        //unset some private ones

        return $this->_key_values;
    }

    public static function Status($obj_config)
    {
        return $obj_config->getStatus();
    }
}
?>
