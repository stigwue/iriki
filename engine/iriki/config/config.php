<?php

namespace iriki\engine;

/**
* Iriki config. Base class for handling a configuration file
* and its JSON parsing
*
*/
class config
{
    /**
    * String contents of a JSON file.
    *
    */
    private $_json_string = '';

    /**
    * An array bearing parsed contents of a JSON file.
    *
    */
    private $_json = null;

    /**
    * A subset of the JSON array of the configuration file.
    * This is identified by an Iriki app name.
    *
    */
    private $_key_values = null;

    /**
    * Load the contents of a supplied json file.
    *
    *
    * @param json_path JSON file path
    * @return File contents or null if file not found.
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
    * Parse a supplied json string.
    *
    *
    * @param json_string JSON string body
    * @return An array rray of JSON or null if parse fails.
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
    * Constructor. Takes a file path, sets and parses its contents.
    *
    *
    * @param json_path JSON file path
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
    * Get the json array.
    *
    *
    * @return Array of JSON or null if parse fails.
    * @throw
    */
    public function getJson()
    {
        return $this->_json;
    }

    /**
    * Get iriki app json array of configuration.
    *
    *
    * @return An array of JSON or null if parse fails.
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
    * Get status, a summary of config details.
    *
    *
    * @param status Previous status array to append to.
    * @param json Boolean, encode result as json?
    * @return array Status array or json representation
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
