<?php

namespace iriki;

/**
* Iriki config, base class for handling a configuration file
* and its JSON parsing
*
*/

class config
{
    /**
    * String contents of a JSON file
    *
    * @var string
    */
    private $_json_string = '';

    /**
    * Array, parsed contents of a JSON file
    *
    * @var array
    */
    private $_json = null;

    /**
    * Configuration file contents. Tied to Iriki app name.
    *
    * @var array
    */
    private $_key_values;

    /**
    * Load the contents of a supplied json file .
    *
    *
    * @param string JSON file path
    * @return string File contents or an empty string if file not found.
    * @throw
    */
    private static function load_json_file($json_path)
    {
        if (file_exists($json_path))
        {
            $file_contents = file_get_contents($json_path);
            return $file_contents;
        }
        else
        {
            return '';
        }
    }

    private static function parse_json_string($json_string)
    {
        if ($json_string == '')
        {
            return null;
        }
        else
        {
            return json_decode($json_string, TRUE);
        }
    }

    function __construct($json_path = '')
    {
        if (strlen($json_path) != 0)
        {
            $this->_json_string = Self::load_json_file($json_path);

            $this->_json = Self::parse_json_string($this->_json_string);
        }
    }

    public function getJson()
    {
        return $this->_json;
    }

    public function getKeyValues()
    {
        //return key-value pairs
        $this->_key_values = $this->_json['iriki']['app'];
        return $this->_key_values;
    }

    public function getStatus($status = null, $json = false)
    {
        //unset some private ones?
        if (is_null($status))
        {
            $status = array('data' => array());
        }

        $status['data']['base_url'] = $this->_key_values['base_url'];

        //application
        $context = "application";
        $status['data']['description'] = $this->_key_values[$context]['description'];
        $status['data']['author'] = $this->_key_values[$context]['author'];
        $status['data']['version'] = array(
            'major' => $this->_key_values[$context]['version']['major'],
            'minor' => $this->_key_values[$context]['version']['minor'],
            'build' => $this->_key_values[$context]['version']['build']
        );
        //engine

        if ($json)
        {
            return json_encode($status);
        }
        else
        {
            return $status;
        }
    }
}
?>
