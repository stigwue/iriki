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
    private $_key_values = null;

    /**
    * Load the contents of a supplied json file
    *
    *
    * @param string JSON file path
    * @returns string File contents or null if file not found.
    * @throw
    */
    public static function load_json_file($json_path)
    {
        if (file_exists($json_path))
        {
            $file_contents = file_get_contents($json_path);
            return $file_contents;
        }
        else
        {
            return null;
        }
    }

    /**
    * Parse supplied json string
    *
    *
    * @param string JSON string
    * @returns array Array of JSON or null if parse fails.
    * @throw
    */
    public static function parse_json_string($json_string)
    {
        if ($json_string == '' || is_null($json_string))
        {
            return null;
        }
        else
        {
            return json_decode($json_string, TRUE);
        }
    }

    /**
    * Constructor, takes file path and sets contents.
    *
    *
    * @param string JSON file path
    * @throw
    */
    function __construct($json_path = '')
    {
        if (strlen($json_path) != 0)
        {
            $this->_json_string = Self::load_json_file($json_path);

            $this->_json = Self::parse_json_string($this->_json_string);
        }
    }

    /**
    * Get json array
    *
    *
    * @returns array Array of JSON or null if parse fails.
    * @throw
    */
    public function getJson()
    {
        return $this->_json;
    }

    /**
    * Get iriki app json array of configuration
    *
    *
    * @returns array Array of JSON or null if parse fails.
    * @throw
    */
    public function getKeyValues()
    {
        //return key-value pairs
        if (isset($this->_json['iriki']['app'])) 
        {
            $this->_key_values = $this->_json['iriki']['app'];
        }
        else
        {
            $this->_key_values = null;
        }
        return $this->_key_values;
    }

    /**
    * Get status, a summary of config details
    *
    *
    * @param array Previous status array to append to
    * @param boolean Encode result as json
    * @returns array Status array or json representation
    * @throw
    */
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
