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

    public function getJson()
    {
        return $this->_json;
    }

    public function getKeyValues()
    {
        //return key-value pairs
        return $this->_key_values;
    }

    public function doInitialise($json_path, $app = 'iriki')
    {
        $this->_json = Self::load_json_file($json_path);

        $this->_key_values = $this->_json['iriki']['app'];
    }

    public function getStatus()
    {
        //unset some private ones

        $status = "Title: " . $this->_key_values['title'] . "
Author: " . $this->_key_values['author'] . "
Version: " . $this->_key_values['version']['major'] . '.' . $this->_key_values['version']['minor'] . '.' . $this->_key_values['version']['build'] . "
Base URL: " . $this->_key_values['base_url'] . "
";
/*"Engine: " . $this->_key_values['engine']['name'] . "
Application: " . $this->_key_values['application']['name'] ."
";*/

        return $status;
    }
}
?>
